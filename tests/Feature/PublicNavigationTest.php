<?php

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

it('shows a dashboard link to users with dashboard permission', function () {
    $this->seed(RolePermissionSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('href="'.route('dashboard').'"', false)
        ->assertSee('Dashboard')
        ->assertSee('action="'.route('logout').'"', false)
        ->assertSee('Logout')
        ->assertDontSee('href="'.route('register').'"', false)
        ->assertDontSee('href="'.route('login').'"', false)
        ->assertDontSee('Akun saya');
});

it('shows an account link instead of admin navigation to users without dashboard permission', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('href="'.route('profile.edit').'"', false)
        ->assertSee('Akun saya')
        ->assertSee('action="'.route('logout').'"', false)
        ->assertSee('Logout')
        ->assertDontSee('href="'.route('register').'"', false)
        ->assertDontSee('href="'.route('login').'"', false)
        ->assertDontSee('Dashboard admin');
});

it('shows a login link to guests', function () {
    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('href="'.route('login').'"', false)
        ->assertSee('href="'.route('register').'"', false)
        ->assertDontSee('action="'.route('logout').'"', false)
        ->assertDontSee('Logout')
        ->assertSee('Login')
        ->assertDontSee('Dashboard admin');
});
