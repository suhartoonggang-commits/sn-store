<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CartService
{
    /**
     * @return Collection<int, CartItem>
     */
    public function getCartItems(User $user): Collection
    {
        return $user->cartItems()
            ->with('product')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();
    }

    /**
     * @return array{items: array<int, array{id: int, product: array{id: int, name: string, price: int, stock: int, image_url: ?string}, quantity: int, line_total: int}>, subtotal: int}
     */
    public function getCartData(User $user): array
    {
        $items = $this->getCartItems($user)
            ->map(fn (CartItem $cartItem): array => [
                'id' => $cartItem->id,
                'product' => [
                    'id' => $cartItem->product->id,
                    'name' => $cartItem->product->name,
                    'price' => (int) round((float) $cartItem->product->price),
                    'stock' => $cartItem->product->stock,
                    'image_url' => $cartItem->product->image_path
                        ? Storage::disk('public')->url($cartItem->product->image_path)
                        : null,
                ],
                'quantity' => $cartItem->quantity,
                'line_total' => (int) round((float) $cartItem->product->price) * $cartItem->quantity,
            ])
            ->all();

        return [
            'items' => $items,
            'subtotal' => array_sum(array_column($items, 'line_total')),
        ];
    }

    public function addProduct(User $user, Product $product, int $quantity): CartItem
    {
        return DB::transaction(function () use ($product, $quantity, $user): CartItem {
            $lockedProduct = Product::query()
                ->lockForUpdate()
                ->findOrFail($product->id);

            if (! $lockedProduct->is_active || $lockedProduct->stock < 1) {
                throw ValidationException::withMessages([
                    'product' => 'Produk ini sedang tidak tersedia.',
                ]);
            }

            $cartItem = $user->cartItems()
                ->where('product_id', $lockedProduct->id)
                ->lockForUpdate()
                ->first();
            $newQuantity = ($cartItem?->quantity ?? 0) + $quantity;

            if ($newQuantity > $lockedProduct->stock) {
                throw ValidationException::withMessages([
                    'quantity' => 'Jumlah melebihi stok yang tersedia.',
                ]);
            }

            if ($cartItem) {
                $cartItem->update(['quantity' => $newQuantity]);

                return $cartItem;
            }

            return $user->cartItems()->create([
                'product_id' => $lockedProduct->id,
                'quantity' => $quantity,
            ]);
        });
    }
}
