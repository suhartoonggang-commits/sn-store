<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    private const PERMISSIONS = [
        'dashboard.view',
        'categories.view',
        'products.view',
        'transactions.view',
        'users.view',
        'roles.view',
        'roles.create',
        'roles.update',
        'roles.delete',
        'permissions.view',
        'permissions.create',
        'permissions.update',
        'permissions.delete',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::query()
            ->where('guard_name', 'web')
            ->whereNotIn('name', ['admin', 'customer'])
            ->delete();

        $permissions = collect(self::PERMISSIONS)
            ->map(fn (string $name): Permission => Permission::findOrCreate($name, 'web'));

        Role::findOrCreate('admin', 'web')->syncPermissions($permissions);
        Role::findOrCreate('customer', 'web')->syncPermissions([]);
    }
}
