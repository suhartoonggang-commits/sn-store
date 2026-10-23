<?php

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

it('redirects guests to sign in', function () {
    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));
});

it('renders the admin dashboard with the available demo totals', function () {
    $this->seed(RolePermissionSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertViewIs('pages.admin.dashboard.index')
        ->assertViewHas('categoryCount', 8)
        ->assertViewHas('productCount', 2)
        ->assertViewHas('transactionCount', 5)
        ->assertViewHas('userCount', 3)
        ->assertSee('Selamat datang kembali')
        ->assertSee('Kategori')
        ->assertSee('Produk')
        ->assertSee('Transaksi')
        ->assertSee('User')
        ->assertSee('href="'.route('admin.products.index').'"', false)
        ->assertSee('href="'.route('admin.transactions.index').'"', false)
        ->assertSee('href="'.route('admin.users.index').'"', false);
});
