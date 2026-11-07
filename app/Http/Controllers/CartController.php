<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function index(Request $request, CartService $cartService): View
    {
        $cartItems = $cartService->getCartItems($request->user());

        return view('pages.public.cart', [
            'cartItems' => $cartItems,
            'subtotal' => $cartItems->sum(fn (CartItem $item): float => (float) $item->product->price * $item->quantity),
        ]);
    }

    public function store(Request $request, Product $product, CartService $cartService): RedirectResponse
    {
        $attributes = $request->validate([
            'quantity' => ['sometimes', 'integer', 'min:1'],
        ]);
        $quantity = (int) ($attributes['quantity'] ?? 1);
        $cartService->addProduct($request->user(), $product, $quantity);

        return redirect()->route('cart.index')->with('status', 'Produk ditambahkan ke keranjang.');
    }

    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        $attributes = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);
        $cartItem = $request->user()
            ->cartItems()
            ->whereKey($cartItem->id)
            ->firstOrFail();

        DB::transaction(function () use ($attributes, $cartItem): void {
            $product = Product::query()
                ->lockForUpdate()
                ->find($cartItem->product_id);

            if (! $product || ! $product->is_active || $product->stock < $attributes['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => 'Jumlah melebihi stok yang tersedia.',
                ]);
            }

            $cartItem->update(['quantity' => $attributes['quantity']]);
        });

        return redirect()->route('cart.index')->with('status', 'Jumlah produk diperbarui.');
    }

    public function destroy(Request $request, CartItem $cartItem): RedirectResponse
    {
        $request->user()
            ->cartItems()
            ->whereKey($cartItem->id)
            ->firstOrFail()
            ->delete();

        return redirect()->route('cart.index')->with('status', 'Produk dihapus dari keranjang.');
    }
}
