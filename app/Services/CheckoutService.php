<?php

namespace App\Services;

use App\Models\CartItem;
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

                $subtotal += (int) round((float) $product->price) * $cartItem->quantity;
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
                $unitPrice = (int) round((float) $product->price);
                $lineTotal = $unitPrice * $cartItem->quantity;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $unitPrice,
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

    public function failAndRestore(Order $order, ?string $paymentReference = null): void
    {
        DB::transaction(function () use ($order, $paymentReference): void {
            $lockedOrder = Order::query()
                ->with('items')
                ->lockForUpdate()
                ->findOrFail($order->id);

            if ($lockedOrder->payment_status !== Order::PAYMENT_PENDING) {
                return;
            }

            foreach ($lockedOrder->items as $item) {
                if ($item->product_id === null) {
                    continue;
                }

                $product = Product::query()
                    ->lockForUpdate()
                    ->find($item->product_id);

                if (! $product) {
                    continue;
                }

                $product->increment('stock', $item->quantity);

                if ($lockedOrder->user_id === null || ! $product->is_active) {
                    continue;
                }

                $cartItem = CartItem::query()
                    ->where('user_id', $lockedOrder->user_id)
                    ->where('product_id', $product->id)
                    ->lockForUpdate()
                    ->first();
                $availableStock = $product->stock;
                $restoredQuantity = min($availableStock, ($cartItem?->quantity ?? 0) + $item->quantity);

                if ($cartItem) {
                    $cartItem->update(['quantity' => $restoredQuantity]);

                    continue;
                }

                CartItem::query()->create([
                    'user_id' => $lockedOrder->user_id,
                    'product_id' => $product->id,
                    'quantity' => $restoredQuantity,
                ]);
            }

            $lockedOrder->update([
                'payment_status' => Order::PAYMENT_FAILED,
                'payment_gateway' => 'midtrans',
                'payment_reference' => $paymentReference,
            ]);
        });
    }
}
