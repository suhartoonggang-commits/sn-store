<?php

namespace App\Contracts;

use App\Models\Order;

interface PaymentGateway
{
    /**
     * @return array{token: string}
     */
    public function createSnapPayment(Order $order): array;

    /**
     * @param  array<string, mixed>  $notification
     */
    public function verifyNotification(array $notification): bool;

    public function transactionStatus(string $orderNumber): object;
}
