<?php

use App\Models\User;

it('shows a dashboard link to authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('href="'.route('dashboard').'"', false)
        ->assertSee('Dashboard')
        ->assertDontSee('Akun saya');
});

it('shows a login link to guests', function () {
    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('href="'.route('login').'"', false)
        ->assertSee('Login')
        ->assertDontSee('Dashboard admin');
});
