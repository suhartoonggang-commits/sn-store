<?php

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

it('redirects customers from admin pages to the public home page', function (string $routeName) {
    $this->seed(RolePermissionSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('customer');

    $this->actingAs($user)
        ->get(route($routeName))
        ->assertRedirect(route('home'));
})->with([
    'dashboard' => 'dashboard',
    'categories' => 'admin.categories.index',
    'products' => 'admin.products.index',
    'transactions' => 'admin.transactions.index',
    'users' => 'admin.users.index',
]);

it('applies least-privilege permissions to the seeded roles', function () {
    Role::findOrCreate('super-admin', 'web');
    Role::findOrCreate('staff', 'web');

    $this->seed(RolePermissionSeeder::class);

    $admin = Role::findByName('admin');
    $customer = Role::findByName('customer');

    expect($admin->hasPermissionTo('permissions.delete'))->toBeTrue()
        ->and($admin->hasPermissionTo('users.view'))->toBeTrue()
        ->and($customer->permissions)->toBeEmpty();

    $this->seed(RolePermissionSeeder::class);

    $this->assertDatabaseCount('roles', 2);
    $this->assertDatabaseCount('permissions', 19);
    $this->assertDatabaseMissing('roles', ['name' => 'super-admin']);
    $this->assertDatabaseMissing('roles', ['name' => 'staff']);
});

it('allows admins to access all admin modules and hides admin links from customers', function () {
    $this->seed(RolePermissionSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('href="'.route('admin.products.index').'"', false)
        ->assertSee('href="'.route('admin.users.index').'"', false);

    $this->get(route('admin.products.index'))->assertOk();

    $customer = User::factory()->create();
    $customer->assignRole('customer');

    $this->actingAs($customer)
        ->get(route('admin.users.index'))
        ->assertRedirect(route('home'));
});

it('seeds the configured admin account with the admin role', function () {
    config(['app.admin_seed_password' => 'admin123']);
    $this->seed(RolePermissionSeeder::class);

    $this->seed(AdminUserSeeder::class);
    $this->seed(AdminUserSeeder::class);

    $admin = User::where('email', 'admin@gmail.com')->firstOrFail();

    expect($admin->hasRole('admin'))->toBeTrue()
        ->and($admin->hasVerifiedEmail())->toBeTrue()
        ->and(Hash::check(config('app.admin_seed_password'), $admin->password))->toBeTrue();

    $this->assertDatabaseCount('users', 1);
});
