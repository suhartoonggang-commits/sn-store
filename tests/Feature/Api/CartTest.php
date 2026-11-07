<?php

use App\Models\Product;
use App\Models\User;

it('returns 401 for cart requests when the user is not logged in', function () {
    $product = Product::factory()->create(['stock' => 5]);

    $this->getJson(route('api.cart.show'))
        ->assertUnauthorized();

    $this->postJson(route('api.cart.items.store', $product), ['quantity' => 1])
        ->assertUnauthorized();

    $this->assertDatabaseCount('cart_items', 0);
});

it('adds products to the logged-in user cart and displays its subtotal', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'name' => 'Headphone Wireless',
        'price' => 12500,
        'stock' => 5,
    ]);

    $firstResponse = $this->actingAs($user)
        ->postJson(route('api.cart.items.store', $product), ['quantity' => 2])
        ->assertCreated()
        ->assertJsonPath('code', 201)
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('data.items.0.product.name', 'Headphone Wireless')
        ->assertJsonPath('data.items.0.quantity', 2)
        ->assertJsonPath('data.items.0.line_total', 25000)
        ->assertJsonPath('data.subtotal', 25000);

    $firstCartItemId = $firstResponse->json('data.items.0.id');

    $this->postJson(route('api.cart.items.store', $product), ['quantity' => 1])
        ->assertOk()
        ->assertJsonPath('data.items.0.id', $firstCartItemId)
        ->assertJsonPath('data.items.0.quantity', 3)
        ->assertJsonPath('data.items.0.line_total', 37500)
        ->assertJsonPath('data.subtotal', 37500);

    $this->getJson(route('api.cart.show'))
        ->assertOk()
        ->assertJsonPath('code', 200)
        ->assertJsonPath('message', 'Data cart berhasil diambil.')
        ->assertJsonPath('data.subtotal', 37500);

    $this->assertDatabaseHas('cart_items', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 3,
    ]);
});

it('does not expose another user cart through the API', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $product = Product::factory()->create();
    $owner->cartItems()->create(['product_id' => $product->id, 'quantity' => 2]);

    $this->actingAs($otherUser)
        ->getJson(route('api.cart.show'))
        ->assertOk()
        ->assertJsonPath('data.items', [])
        ->assertJsonPath('data.subtotal', 0);
});

it('rejects invalid quantities without adding an item to cart', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 3]);

    $this->actingAs($user)
        ->postJson(route('api.cart.items.store', $product), ['quantity' => 0])
        ->assertUnprocessable()
        ->assertJsonPath('code', 422)
        ->assertJsonPath('status', 'error')
        ->assertJsonPath('message', 'Jumlah produk tidak valid.')
        ->assertJsonPath('data.quantity.0', 'The quantity field must be at least 1.');

    $this->assertDatabaseCount('cart_items', 0);
});

it('rejects quantities exceeding product stock', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 2]);

    $this->actingAs($user)
        ->postJson(route('api.cart.items.store', $product), ['quantity' => 3])
        ->assertUnprocessable()
        ->assertJsonPath('code', 422)
        ->assertJsonPath('message', 'Jumlah melebihi stok yang tersedia.')
        ->assertJsonPath('data.quantity.0', 'Jumlah melebihi stok yang tersedia.');

    $this->assertDatabaseCount('cart_items', 0);
});

it('rejects products that are inactive or out of stock', function () {
    $user = User::factory()->create();
    $inactiveProduct = Product::factory()->create(['is_active' => false, 'stock' => 3]);
    $outOfStockProduct = Product::factory()->create(['stock' => 0]);

    $this->actingAs($user)
        ->postJson(route('api.cart.items.store', $inactiveProduct), ['quantity' => 1])
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Produk ini sedang tidak tersedia.');

    $this->postJson(route('api.cart.items.store', $outOfStockProduct), ['quantity' => 1])
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Produk ini sedang tidak tersedia.');

    $this->assertDatabaseCount('cart_items', 0);
});
