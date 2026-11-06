<?php

namespace App\Http\Controllers;

use App\Services\CheckoutService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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

    public function store(Request $request, CheckoutService $checkoutService): RedirectResponse
    {
        $customer = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:5000'],
        ]);

        $order = $checkoutService->placeOrder($request->user(), $customer);

        return redirect()
            ->route('orders.show', $order)
            ->with('status', 'Pesanan berhasil dibuat. Pembayaran menunggu konfigurasi payment gateway.');
    }
}
