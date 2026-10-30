<?php

use App\Models\Category;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Spatie\Permission\Models\Role;

it('redirects guests to sign in', function () {
    $response = $this->get(route('admin.categories.index'));

    $response->assertRedirect(route('login'));
});

it('redirects guests away from category forms', function () {
    $this->get(route('admin.categories.create'))
        ->assertRedirect(route('login'));
});

it('shows categories saved in the database', function () {
    $this->seed(RolePermissionSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');

    Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => 'Headphone dan speaker.',
        'is_active' => true,
    ]);

    Category::query()->create([
        'name' => 'Kamera',
        'slug' => 'kamera',
        'description' => 'Kamera digital.',
        'is_active' => false,
    ]);

    $response = $this->actingAs($user)->get(route('admin.categories.index'));

    $response
        ->assertOk()
        ->assertViewIs('pages.admin.categories.index')
        ->assertViewHas('categories', fn ($categories) => $categories->count() === 2)
        ->assertSee('Audio')
        ->assertSee('audio')
        ->assertSee('Headphone dan speaker.')
        ->assertSee('Kamera')
        ->assertSee('kamera')
        ->assertSee('Kamera digital.')
        ->assertSee('Nonaktif')
        ->assertSee('2 ditampilkan')
        ->assertDontSee('Gaming')
        ->assertSee('Tambah kategori')
        ->assertSee('@submit.prevent="confirmDelete($event)"', false)
        ->assertSee('Ubah')
        ->assertSee('Hapus')
        ->assertDontSee('onsubmit=');
});

it('shows an empty state when no categories are saved', function () {
    $this->seed(RolePermissionSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get(route('admin.categories.index'));

    $response
        ->assertOk()
        ->assertViewHas('categories', fn ($categories) => $categories->isEmpty())
        ->assertSee('Belum ada kategori')
        ->assertSee('0 ditampilkan');
});

it('links to categories from the admin sidebar', function () {
    $this->seed(RolePermissionSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertSee('href="'.route('admin.categories.index').'"', false);
});

it('creates categories with a unique slug generated from the name', function () {
    $this->seed(RolePermissionSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');

    $this->actingAs($user)
        ->get(route('admin.categories.create'))
        ->assertOk()
        ->assertSee('Slug dibuat otomatis')
        ->assertDontSee('name="slug"', false);

    $response = $this->post(route('admin.categories.store'), [
        'name' => 'Audio Pro',
        'slug' => 'slug-manual-yang-diabaikan',
        'description' => 'Headphone dan speaker.',
        'is_active' => '1',
    ]);

    $response
        ->assertRedirect(route('admin.categories.index'))
        ->assertSessionHas('status', 'Kategori berhasil ditambahkan.');

    $this->assertDatabaseHas('categories', [
        'name' => 'Audio Pro',
        'slug' => 'audio-pro',
        'description' => 'Headphone dan speaker.',
        'is_active' => true,
    ]);
});

it('adds a suffix when a generated category slug is already in use', function () {
    $this->seed(RolePermissionSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');

    Category::query()->create([
        'name' => 'Audio Pro',
        'slug' => 'audio-pro',
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->post(route('admin.categories.store'), [
        'name' => 'Audio-Pro',
        'is_active' => '1',
    ]);

    $response->assertRedirect(route('admin.categories.index'));

    $this->assertDatabaseHas('categories', [
        'name' => 'Audio-Pro',
        'slug' => 'audio-pro-2',
    ]);
});

it('returns validation errors when required category fields are missing', function () {
    $this->seed(RolePermissionSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->post(route('admin.categories.store'), []);

    $response
        ->assertSessionHasErrors(['name', 'is_active'])
        ->assertSessionHasErrors([
            'name' => 'Nama kategori wajib diisi.',
            'is_active' => 'Status kategori wajib dipilih.',
        ]);

    $this->assertDatabaseCount('categories', 0);
});

it('rejects duplicate category names', function () {
    $this->seed(RolePermissionSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');
    Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->post(route('admin.categories.store'), [
        'name' => 'Audio',
        'is_active' => '1',
    ]);

    $response->assertSessionHasErrors([
        'name' => 'Nama kategori sudah digunakan.',
    ]);

    $this->assertDatabaseCount('categories', 1);
});

it('updates categories while allowing their current name', function () {
    $this->seed(RolePermissionSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => 'Deskripsi lama.',
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->get(route('admin.categories.edit', $category))
        ->assertOk()
        ->assertSee('Deskripsi lama.');

    $response = $this->put(route('admin.categories.update', $category), [
        'name' => 'Audio',
        'description' => 'Deskripsi baru.',
        'is_active' => '0',
    ]);

    $response
        ->assertRedirect(route('admin.categories.index'))
        ->assertSessionHas('status', 'Kategori berhasil diperbarui.');

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => 'Deskripsi baru.',
        'is_active' => false,
    ]);
});

it('rejects updating a category to a name used by another category', function () {
    $this->seed(RolePermissionSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'is_active' => true,
    ]);
    Category::query()->create([
        'name' => 'Kamera',
        'slug' => 'kamera',
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->put(route('admin.categories.update', $category), [
        'name' => 'Kamera',
        'is_active' => '1',
    ]);

    $response->assertSessionHasErrors([
        'name' => 'Nama kategori sudah digunakan.',
    ]);

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Audio',
        'slug' => 'audio',
    ]);
});

it('deletes categories', function () {
    $this->seed(RolePermissionSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->delete(route('admin.categories.destroy', $category));

    $response
        ->assertRedirect(route('admin.categories.index'))
        ->assertSessionHas('status', 'Kategori berhasil dihapus.');

    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

it('escapes category content in the admin table', function () {
    $this->seed(RolePermissionSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');
    Category::query()->create([
        'name' => '<script>alert(1)</script>',
        'slug' => 'script-alert-1-script',
        'description' => '<img src=x onerror=alert(1)>',
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->get(route('admin.categories.index'));

    $response
        ->assertOk()
        ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
        ->assertSee('&lt;img src=x onerror=alert(1)&gt;', false)
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertDontSee('<img src=x onerror=alert(1)>', false);
});

it('hides category mutation controls and forbids writes without the matching permission', function () {
    $this->seed(RolePermissionSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');
    Role::findByName('admin')->syncPermissions(['categories.view']);
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->get(route('admin.categories.index'))
        ->assertOk()
        ->assertDontSee('Tambah kategori')
        ->assertDontSee('Ubah')
        ->assertDontSee('action="'.route('admin.categories.destroy', $category).'"', false);

    $this->post(route('admin.categories.store'), [
        'name' => 'Kamera',
        'is_active' => '1',
    ])->assertForbidden();

    $this->put(route('admin.categories.update', $category), [
        'name' => 'Audio baru',
        'is_active' => '1',
    ])->assertForbidden();

    $this->delete(route('admin.categories.destroy', $category))->assertForbidden();

    $this->assertDatabaseCount('categories', 1);
    $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Audio']);
});
