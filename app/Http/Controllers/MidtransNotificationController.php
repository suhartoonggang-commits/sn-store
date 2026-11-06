<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentGateway;
use App\Models\Order;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class MidtransNotificationController extends Controller
{
    public function __invoke(
        Request $request,
        PaymentGateway $paymentGateway,
        CheckoutService $checkoutService
    ): Response {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'string', 'max:255'],
            'status_code' => ['required', 'string', 'max:10'],
            'gross_amount' => ['required', 'numeric'],
            'signature_key' => ['required', 'string', 'max:512'],
            'transaction_id' => ['required', 'string', 'max:255'],
            'transaction_status' => ['required', 'string', 'max:30'],
            'fraud_status' => ['nullable', 'string', 'max:30'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid notification payload.'], 422);
        }

        $notification = $validator->validated();

        if (! $paymentGateway->verifyNotification($notification)) {
            abort(403);
        }

        $order = Order::query()
            ->where('order_number', $notification['order_id'])
            ->firstOrFail();

        if (! $this->amountMatches($notification['gross_amount'], $order->total)) {
            abort(403);
        }

        $transaction = $paymentGateway->transactionStatus($order->order_number);

        if (
            ($transaction->order_id ?? null) !== $order->order_number
            || ($transaction->status_code ?? null) !== '200'
            || ! $this->amountMatches((string) ($transaction->gross_amount ?? ''), $order->total)
        ) {
            abort(403);
        }

        $transactionStatus = $transaction->transaction_status ?? null;
        $fraudStatus = $transaction->fraud_status ?? null;
        $paymentReference = (string) ($transaction->transaction_id ?? $notification['transaction_id']);

        if ($transactionStatus === 'settlement' || ($transactionStatus === 'capture' && $fraudStatus === 'accept')) {
            $checkoutService->markPaid($order, 'midtrans', $paymentReference);
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'], true)) {
            $checkoutService->failAndRestore($order, $paymentReference);
        }

        return response()->noContent();
    }

    private function amountMatches(string $receivedAmount, string|int|float $orderTotal): bool
    {
        return number_format((float) $receivedAmount, 2, '.', '')
            === number_format((float) $orderTotal, 2, '.', '');
    }
}
