<?php

use App\Models\User;

it('redirects guests to sign in', function () {
    $response = $this->get(route('admin.categories.index'));

    $response->assertRedirect(route('login'));
});

it('shows dummy product categories without a categories table', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.categories.index'));

    $response
        ->assertOk()
        ->assertViewIs('pages.admin.categories.index')
        ->assertViewHas('categories', fn ($categories) => $categories->count() === 8)
        ->assertSee('Audio')
        ->assertSee('audio')
        ->assertSee('Contoh kategori produk')
        ->assertSee('belum tersimpan di database')
        ->assertSee('Kamera')
        ->assertSee('Gaming')
        ->assertSee('Tambah data')
        ->assertSee('Tambah kategori')
        ->assertSee('Simpan (demo)')
        ->assertSee('Edit kategori')
        ->assertSee('Hapus kategori');
});

it('links to categories from the admin sidebar', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertSee('href="'.route('admin.categories.index').'"', false);
});
