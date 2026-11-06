<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    /**
     * @param  array{name: string, phone: string, email: string, address: string}  $customer
     */
    public function placeOrder(User $user, array $customer): Order
    {
        return DB::transaction(function () use ($customer, $user): Order {
            $cartItems = $user->cartItems()
                ->orderBy('product_id')
                ->lockForUpdate()
                ->get();

            if ($cartItems->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Keranjang masih kosong.',
                ]);
            }

            $products = Product::query()
                ->whereKey($cartItems->pluck('product_id'))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $subtotal = 0;

            foreach ($cartItems as $cartItem) {
                $product = $products->get($cartItem->product_id);

                if (! $product || ! $product->is_active || $product->stock < $cartItem->quantity) {
                    throw ValidationException::withMessages([
                        'cart' => 'Stok salah satu produk tidak lagi mencukupi. Perbarui keranjang Anda.',
                    ]);
                }

                $subtotal += round((float) $product->price * $cartItem->quantity, 2);
            }

            $user->update([
                'phone' => $customer['phone'],
                'address' => $customer['address'],
            ]);

            $order = Order::query()->create([
                'user_id' => $user->id,
                'order_number' => 'SN-'.now()->format('Ymd').'-'.Str::upper(Str::random(8)),
                'customer_name' => $customer['name'],
                'customer_phone' => $customer['phone'],
                'customer_email' => $customer['email'],
                'customer_address' => $customer['address'],
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'payment_status' => Order::PAYMENT_PENDING,
            ]);

            foreach ($cartItems as $cartItem) {
                $product = $products->get($cartItem->product_id);
                $lineTotal = round((float) $product->price * $cartItem->quantity, 2);

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $product->price,
                    'quantity' => $cartItem->quantity,
                    'line_total' => $lineTotal,
                ]);

                $product->decrement('stock', $cartItem->quantity);
            }

            $user->cartItems()->delete();

            return $order;
        });
    }

    public function markPaid(Order $order, string $gateway, string $paymentReference): Order
    {
        return DB::transaction(function () use ($gateway, $order, $paymentReference): Order {
            $lockedOrder = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            if ($lockedOrder->payment_status === Order::PAYMENT_PAID) {
                return $lockedOrder;
            }

            if ($lockedOrder->payment_status !== Order::PAYMENT_PENDING) {
                throw ValidationException::withMessages([
                    'payment' => 'Pesanan ini tidak dapat ditandai sebagai lunas.',
                ]);
            }

            $lockedOrder->update([
                'payment_status' => Order::PAYMENT_PAID,
                'payment_gateway' => $gateway,
                'payment_reference' => $paymentReference,
                'paid_at' => now(),
            ]);

            return $lockedOrder;
        });
    }
}
