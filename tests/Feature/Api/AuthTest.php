<?php

use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Hash;

it('registers a customer without exposing their password', function () {
    $this->seed(RolePermissionSeeder::class);

    $this->postJson(route('api.register'), [
        'name' => 'Nadia Putri',
        'email' => 'nadia@example.test',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])
        ->assertCreated()
        ->assertJsonPath('code', 201)
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('message', 'Registrasi berhasil.')
        ->assertJsonPath('data.name', 'Nadia Putri')
        ->assertJsonPath('data.email', 'nadia@example.test')
        ->assertJsonMissingPath('data.password')
        ->assertJsonMissingPath('data.access_token');

    $user = User::query()->where('email', 'nadia@example.test')->firstOrFail();

    $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Nadia Putri']);
    expect(Hash::check('password', $user->password))->toBeTrue();
    expect($user->hasRole('customer'))->toBeTrue();
});

it('returns a bearer token on login and accepts it for protected cart requests', function () {
    $user = User::factory()->create([
        'email' => 'nadia@example.test',
        'password' => 'password',
    ]);
    $product = Product::factory()->create(['stock' => 3]);
    $user->cartItems()->create(['product_id' => $product->id, 'quantity' => 2]);

    $response = $this->postJson(route('api.login'), [
        'email' => 'NADIA@example.test',
        'password' => 'password',
        'device_name' => 'android-app',
    ])
        ->assertOk()
        ->assertJsonPath('code', 200)
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('message', 'Login berhasil.')
        ->assertJsonPath('data.token_type', 'Bearer')
        ->assertJsonPath('data.user.email', 'nadia@example.test')
        ->assertJsonMissingPath('data.user.password');

    $plainTextToken = $response->json('data.access_token');

    expect($plainTextToken)->toBeString()->not->toBeEmpty();

    $this->assertDatabaseHas('personal_access_tokens', [
        'tokenable_id' => $user->id,
        'name' => 'android-app',
        'token' => hash('sha256', explode('|', $plainTextToken, 2)[1]),
    ]);

    $this->withToken($plainTextToken)
        ->getJson(route('api.cart.show'))
        ->assertOk()
        ->assertJsonPath('data.items.0.product.id', $product->id)
        ->assertJsonPath('data.items.0.quantity', 2);
});

it('rejects invalid login credentials without creating a token', function () {
    $user = User::factory()->create([
        'email' => 'nadia@example.test',
        'password' => 'password',
    ]);

    $this->postJson(route('api.login'), [
        'email' => 'nadia@example.test',
        'password' => 'incorrect-password',
    ])
        ->assertUnauthorized()
        ->assertJsonPath('code', 401)
        ->assertJsonPath('status', 'error')
        ->assertJsonPath('message', 'Email atau password salah.');

    $this->assertDatabaseCount('personal_access_tokens', 0);
    $this->assertModelExists($user);
});

it('rejects API registration with an existing email or mismatched password confirmation', function () {
    User::factory()->create(['email' => 'nadia@example.test']);

    $this->postJson(route('api.register'), [
        'name' => 'Nadia Putri',
        'email' => 'nadia@example.test',
        'password' => 'password',
        'password_confirmation' => 'different-password',
    ])
        ->assertUnprocessable()
        ->assertJsonPath('code', 422)
        ->assertJsonPath('status', 'error')
        ->assertJsonPath('message', 'Data registrasi tidak valid.')
        ->assertJsonStructure(['data' => ['email', 'password']]);

    $this->assertDatabaseCount('users', 1);
});
