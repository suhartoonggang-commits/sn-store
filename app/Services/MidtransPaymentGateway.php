<?php

namespace App\Services;

use App\Contracts\PaymentGateway;
use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use RuntimeException;

class MidtransPaymentGateway implements PaymentGateway
{
    public function createSnapPayment(Order $order): array
    {
        $this->configure();
        $order->loadMissing('items');

        $itemDetails = $order->items->map(fn ($item): array => [
            'id' => (string) ($item->product_id ?? $item->id),
            'price' => (int) round((float) $item->unit_price),
            'quantity' => $item->quantity,
            'name' => mb_substr($item->product_name, 0, 50),
        ])->all();

        $nameParts = preg_split('/\s+/', trim($order->customer_name)) ?: [];
        $firstName = array_shift($nameParts) ?: 'Customer';

        $response = Snap::createTransaction([
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) round((float) $order->total),
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => $firstName,
                'last_name' => implode(' ', $nameParts),
                'email' => $order->customer_email,
                'phone' => $order->customer_phone,
                'shipping_address' => [
                    'first_name' => $firstName,
                    'last_name' => implode(' ', $nameParts),
                    'phone' => $order->customer_phone,
                    'address' => $order->customer_address,
                ],
            ],
        ]);

        return ['token' => $response->token];
    }

    public function verifyNotification(array $notification): bool
    {
        $serverKey = config('services.midtrans.server_key');

        if (! is_string($serverKey) || $serverKey === '') {
            return false;
        }

        foreach (['order_id', 'status_code', 'gross_amount', 'signature_key'] as $field) {
            if (! isset($notification[$field]) || ! is_string($notification[$field])) {
                return false;
            }
        }

        $signature = hash('sha512', $notification['order_id'].$notification['status_code'].$notification['gross_amount'].$serverKey);

        return hash_equals($signature, strtolower($notification['signature_key']));
    }

    public function transactionStatus(string $orderNumber): object
    {
        $this->configure();

        return Transaction::status($orderNumber);
    }

    private function configure(): void
    {
        $serverKey = config('services.midtrans.server_key');

        if (! is_string($serverKey) || $serverKey === '') {
            throw new RuntimeException('Midtrans server key belum dikonfigurasi.');
        }

        Config::$serverKey = $serverKey;
        Config::$clientKey = (string) config('services.midtrans.client_key', '');
        Config::$isProduction = (bool) config('services.midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        Config::$overrideNotifUrl = config('services.midtrans.notification_url')
            ?: route('midtrans.notifications');
    }
}
