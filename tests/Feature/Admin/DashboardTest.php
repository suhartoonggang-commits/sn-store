<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

it('redirects guests to sign in', function () {
    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));
});

it('renders the admin dashboard with database product and category totals', function () {
    $this->seed(RolePermissionSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');

    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'is_active' => true,
    ]);
    Product::factory()->for($category)->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertViewIs('pages.admin.dashboard.index')
        ->assertViewHas('categoryCount', 1)
        ->assertViewHas('productCount', 1)
        ->assertViewHas('transactionCount', 5)
        ->assertViewHas('userCount', 3)
        ->assertSee('Selamat datang kembali')
        ->assertSee('Kategori')
        ->assertSee('Produk')
        ->assertSee('Transaksi')
        ->assertSee('User')
        ->assertSee('action="'.route('logout').'"', false)
        ->assertSee('Logout')
        ->assertSee('href="'.route('admin.products.index').'"', false)
        ->assertSee('href="'.route('admin.transactions.index').'"', false)
        ->assertSee('href="'.route('admin.users.index').'"', false);
});
