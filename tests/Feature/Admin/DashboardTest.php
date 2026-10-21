<?php

use App\Models\User;

it('redirects guests to sign in', function () {
    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));
});

it('renders the admin dashboard with the registered user total', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertViewIs('pages.admin.dashboard.index')
        ->assertViewHas('categoryCount', 8)
        ->assertViewHas('userCount', 128)
        ->assertSee('Selamat datang kembali')
        ->assertSee('Kategori')
        ->assertSee('Produk')
        ->assertSee('Transaksi')
        ->assertSee('User');
});
