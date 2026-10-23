<?php

use App\Models\User;

it('redirects guests from each admin module', function (string $routeName) {
    $this->get(route($routeName))->assertRedirect(route('login'));
})->with([
    'products' => 'admin.products.index',
    'transactions' => 'admin.transactions.index',
    'users' => 'admin.users.index',
]);

it('shows sold products with their categories prices quantities and buyers', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.products.index'));

    $response
        ->assertOk()
        ->assertViewIs('pages.admin.products.index')
        ->assertViewHas('categories', fn ($categories) => $categories->count() === 8)
        ->assertViewHas('products', fn ($products) => $products->count() === 2)
        ->assertSee('Headphone Wireless Pro')
        ->assertSee('Smartphone Nova X')
        ->assertSee('Audio')
        ->assertSee('Rp850.000')
        ->assertSee('3')
        ->assertSee('Andi Pratama')
        ->assertSee('belum tersimpan di database');
});

it('shows payment details and transaction statuses without a gateway connection', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.transactions.index'));

    $response
        ->assertOk()
        ->assertViewIs('pages.admin.transactions.index')
        ->assertViewHas('paymentCount', 5)
        ->assertViewHas('paidCount', 3)
        ->assertViewHas('pendingCount', 1)
        ->assertSee('Pembayaran & transaksi')
        ->assertSee('QRIS')
        ->assertSee('MID-DEMO-84021')
        ->assertSee('Menunggu pembayaran')
        ->assertSee('Belum tersambung ke payment gateway');
});

it('shows transacting users and their transaction history', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.users.index'));

    $response
        ->assertOk()
        ->assertViewIs('pages.admin.users.index')
        ->assertViewHas('users', fn ($users) => $users->count() === 3)
        ->assertViewHas('transactions', fn ($transactions) => $transactions->count() === 5)
        ->assertSee('User yang bertransaksi')
        ->assertSee('History transaksi user')
        ->assertSee('Andi Pratama')
        ->assertSee('SN-2026-1023-004')
        ->assertSee('Bunga Lestari');
});
