<?php

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\CheckoutService;

it('redirects guests to login before they can use the cart', function () {
    $product = Product::factory()->create(['stock' => 3]);

    $this->get(route('cart.index'))->assertRedirect(route('login'));
    $this->post(route('cart.items.store', $product))->assertRedirect(route('login'));

    $this->assertDatabaseCount('cart_items', 0);
});

it('rejects adding products without stock and quantities above available stock', function () {
    $user = User::factory()->create();
    $emptyProduct = Product::factory()->create(['stock' => 0]);
    $limitedProduct = Product::factory()->create(['stock' => 2]);

    $this->actingAs($user)
        ->from(route('products.index'))
        ->post(route('cart.items.store', $emptyProduct))
        ->assertSessionHasErrors('product');

    $this->actingAs($user)
        ->from(route('products.index'))
        ->post(route('cart.items.store', $limitedProduct), ['quantity' => 3])
        ->assertSessionHasErrors('quantity');

    $this->assertDatabaseCount('cart_items', 0);
});

it('adds items and prevents cart quantities from exceeding stock', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 3]);

    $this->actingAs($user)
        ->post(route('cart.items.store', $product), ['quantity' => 2])
        ->assertRedirect(route('cart.index'));

    $cartItem = CartItem::query()->firstOrFail();
    expect($cartItem->quantity)->toBe(2);

    $this->actingAs($user)
        ->from(route('cart.index'))
        ->patch(route('cart.items.update', $cartItem), ['quantity' => 4])
        ->assertSessionHasErrors('quantity');

    expect($cartItem->refresh()->quantity)->toBe(2);

    $this->actingAs($user)
        ->patch(route('cart.items.update', $cartItem), ['quantity' => 3])
        ->assertRedirect(route('cart.index'));

    expect($cartItem->refresh()->quantity)->toBe(3);
});

it('does not allow users to change another users cart item', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $product = Product::factory()->create(['stock' => 3]);
    $cartItem = $owner->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->actingAs($otherUser)
        ->patch(route('cart.items.update', $cartItem), ['quantity' => 2])
        ->assertNotFound();

    expect($cartItem->refresh()->quantity)->toBe(1);
});

it('prefills checkout fields from the authenticated account', function () {
    $user = User::factory()->create([
        'name' => 'Nadia Putri',
        'email' => 'nadia@example.test',
        'phone' => '08123456789',
        'address' => 'Jalan Merdeka Nomor 10',
    ]);
    $product = Product::factory()->create(['stock' => 2]);
    $user->cartItems()->create(['product_id' => $product->id, 'quantity' => 1]);

    $this->actingAs($user)
        ->get(route('checkout.create'))
        ->assertOk()
        ->assertViewIs('pages.public.checkout')
        ->assertSee('value="Nadia Putri"', false)
        ->assertSee('value="nadia@example.test"', false)
        ->assertSee('value="08123456789"', false)
        ->assertSee('Jalan Merdeka Nomor 10');
});

it('creates a pending order and deducts stock when checkout succeeds', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $product = Product::factory()->for($category)->create([
        'name' => 'Headphone Wireless',
        'price' => 850000,
        'stock' => 5,
    ]);
    $user->cartItems()->create(['product_id' => $product->id, 'quantity' => 2]);

    $response = $this->actingAs($user)->post(route('checkout.store'), [
        'name' => 'Nadia Putri',
        'phone' => '08123456789',
        'email' => 'nadia@example.test',
        'address' => 'Jalan Merdeka Nomor 10, Jakarta',
    ]);

    $order = Order::query()->firstOrFail();
    $response->assertRedirect(route('orders.show', $order));

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'user_id' => $user->id,
        'customer_name' => 'Nadia Putri',
        'customer_phone' => '08123456789',
        'customer_email' => 'nadia@example.test',
        'payment_status' => Order::PAYMENT_PENDING,
        'total' => '1700000.00',
    ]);
    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => 'Headphone Wireless',
        'quantity' => 2,
        'line_total' => '1700000.00',
    ]);
    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 3]);
    $this->assertDatabaseCount('cart_items', 0);
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'phone' => '08123456789',
        'address' => 'Jalan Merdeka Nomor 10, Jakarta',
    ]);

    $this->actingAs($user)
        ->get(route('orders.show', $order))
        ->assertOk()
        ->assertViewIs('pages.public.order')
        ->assertSeeText($order->order_number)
        ->assertSeeText('Menunggu pembayaran')
        ->assertSeeText('Headphone Wireless');
});

it('keeps the cart and stock when checkout finds insufficient stock', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 1]);
    $user->cartItems()->create(['product_id' => $product->id, 'quantity' => 2]);

    $this->actingAs($user)
        ->from(route('checkout.create'))
        ->post(route('checkout.store'), [
            'name' => $user->name,
            'phone' => '08123456789',
            'email' => $user->email,
            'address' => 'Alamat lengkap nomor 10',
        ])
        ->assertSessionHasErrors('cart');

    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 1]);
    $this->assertDatabaseHas('cart_items', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
});

it('does not allow users to view another users order', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $order = Order::query()->create([
        'user_id' => $owner->id,
        'order_number' => 'SN-20261106-ABC12345',
        'customer_name' => $owner->name,
        'customer_phone' => '08123456789',
        'customer_email' => $owner->email,
        'customer_address' => 'Alamat lengkap nomor 10',
        'subtotal' => 10000,
        'total' => 10000,
    ]);

    $this->actingAs($otherUser)
        ->get(route('orders.show', $order))
        ->assertNotFound();
});

it('marks pending orders as paid idempotently for a future gateway callback', function () {
    $user = User::factory()->create();
    $order = Order::query()->create([
        'user_id' => $user->id,
        'order_number' => 'SN-20261106-PAY12345',
        'customer_name' => $user->name,
        'customer_phone' => '08123456789',
        'customer_email' => $user->email,
        'customer_address' => 'Alamat lengkap nomor 10',
        'subtotal' => 10000,
        'total' => 10000,
    ]);

    $checkoutService = app(CheckoutService::class);
    $checkoutService->markPaid($order, 'gateway-test', 'PAYMENT-123');
    $checkoutService->markPaid($order->refresh(), 'gateway-test', 'PAYMENT-456');

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'payment_status' => Order::PAYMENT_PAID,
        'payment_gateway' => 'gateway-test',
        'payment_reference' => 'PAYMENT-123',
    ]);
});
