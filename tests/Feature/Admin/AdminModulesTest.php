<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

it('redirects guests from each admin module', function (string $routeName) {
    $this->get(route($routeName))->assertRedirect(route('login'));
})->with([
    'products' => 'admin.products.index',
    'transactions' => 'admin.transactions.index',
    'users' => 'admin.users.index',
]);

it('shows catalog products with their categories prices and stock', function () {
    $this->seed(RolePermissionSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');

    $category = Category::factory()->create(['name' => 'Audio', 'slug' => 'audio']);
    Product::factory()->for($category)->create([
        'name' => 'Headphone Wireless Pro',
        'price' => 850000,
        'stock' => 12,
    ]);

    $response = $this->actingAs($user)->get(route('admin.products.index'));

    $response
        ->assertOk()
        ->assertViewIs('pages.admin.products.index')
        ->assertViewHas('products', fn ($products) => $products->total() === 1)
        ->assertSee('Headphone Wireless Pro')
        ->assertSee('Audio')
        ->assertSee('Rp850.000')
        ->assertSee('12')
        ->assertSee('Daftar produk');
});

it('shows payment details and transaction statuses without a gateway connection', function () {
    $this->seed(RolePermissionSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');

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
    $this->seed(RolePermissionSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');

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
