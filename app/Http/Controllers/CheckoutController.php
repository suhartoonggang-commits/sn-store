<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentGateway;
use App\Services\CheckoutService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class CheckoutController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->cartItems()->doesntExist()) {
            return redirect()->route('cart.index')->with('status', 'Tambahkan produk sebelum checkout.');
        }

        return view('pages.public.checkout', [
            'user' => $user,
            'cartItems' => $user->cartItems()->with('product')->get(),
        ]);
    }

    public function store(
        Request $request,
        CheckoutService $checkoutService,
        PaymentGateway $paymentGateway
    ): RedirectResponse {
        $customer = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:5000'],
        ]);

        $order = $checkoutService->placeOrder($request->user(), $customer);

        try {
            $payment = $paymentGateway->createSnapPayment($order);
        } catch (Throwable $exception) {
            report($exception);
            $checkoutService->failAndRestore($order);

            return redirect()
                ->route('cart.index')
                ->withErrors(['payment' => 'Pembayaran tidak dapat disiapkan. Silakan coba kembali.']);
        }

        $order->update([
            'payment_gateway' => 'midtrans',
            'snap_token' => $payment['token'],
        ]);

        return redirect()
            ->route('orders.show', $order)
            ->with('status', 'Pesanan berhasil dibuat. Lanjutkan pembayaran melalui Midtrans.');
    }
}
