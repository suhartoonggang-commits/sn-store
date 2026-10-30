<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

it('lists products and their categories from the database', function () {
    $this->seed(RolePermissionSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $category = Category::factory()->create(['name' => 'Audio', 'slug' => 'audio']);
    Product::factory()->for($category)->create([
        'name' => 'Headphone Wireless Pro',
        'price' => 850000,
        'stock' => 12,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.products.index'));

    $response
        ->assertOk()
        ->assertViewIs('pages.admin.products.index')
        ->assertSee('Headphone Wireless Pro')
        ->assertSee('Audio')
        ->assertSee('Rp850.000')
        ->assertSee('12')
        ->assertDontSee('belum tersimpan di database');
});

it('renders create and edit forms with the required product fields', function () {
    $this->seed(RolePermissionSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $category = Category::factory()->create(['name' => 'Audio']);
    $product = Product::factory()->for($category)->create([
        'name' => 'Headphone Wireless Pro',
        'image_path' => 'products/headphone.jpg',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.products.create'))
        ->assertOk()
        ->assertSee('multipart/form-data', false)
        ->assertSee('name="image"', false)
        ->assertSee('Pilih kategori');

    $this->get(route('admin.products.edit', $product))
        ->assertOk()
        ->assertSee('Headphone Wireless Pro')
        ->assertSee('Gambar saat ini');
});

it('creates products and stores their images on the public local disk', function () {
    Storage::fake('public');
    $this->seed(RolePermissionSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $category = Category::factory()->create();

    $response = $this->actingAs($admin)->post(route('admin.products.store'), [
        'category_id' => $category->id,
        'name' => 'Headphone Wireless Pro',
        'description' => 'Headphone dengan peredam bising.',
        'price' => '850000.00',
        'stock' => 12,
        'image' => UploadedFile::fake()->image('headphone.jpg'),
        'is_active' => true,
    ]);

    $response->assertRedirect(route('admin.products.index'));
    $product = Product::query()->firstOrFail();

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'category_id' => $category->id,
        'name' => 'Headphone Wireless Pro',
        'price' => '850000.00',
        'stock' => 12,
        'is_active' => true,
    ]);
    expect($product->image_path)->toStartWith('products/');
    Storage::disk('public')->assertExists($product->image_path);
});

it('rejects invalid product categories and non-image uploads', function () {
    Storage::fake('public');
    $this->seed(RolePermissionSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->post(route('admin.products.store'), [
        'category_id' => 999,
        'name' => 'Invalid product',
        'description' => null,
        'price' => 5000,
        'stock' => 1,
        'image' => UploadedFile::fake()->create('manual.pdf', 100, 'application/pdf'),
        'is_active' => true,
    ]);

    $response
        ->assertSessionHasErrors(['category_id', 'image'])
        ->assertSessionHasErrors(['image' => 'Gambar harus berupa file gambar.']);
    $this->assertDatabaseMissing('products', ['name' => 'Invalid product']);
    Storage::disk('public')->assertDirectoryEmpty('products');
});

it('updates product details and replaces the previous image', function () {
    Storage::fake('public');
    $this->seed(RolePermissionSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $category = Category::factory()->create();
    Storage::disk('public')->put('products/old-image.jpg', 'old image');
    $product = Product::factory()->for($category)->create([
        'name' => 'Headphone lama',
        'image_path' => 'products/old-image.jpg',
    ]);

    $response = $this->actingAs($admin)->put(route('admin.products.update', $product), [
        'category_id' => $category->id,
        'name' => 'Headphone baru',
        'description' => 'Deskripsi diperbarui.',
        'price' => '900000.00',
        'stock' => 8,
        'image' => UploadedFile::fake()->image('new-headphone.webp'),
        'is_active' => false,
    ]);

    $response->assertRedirect(route('admin.products.index'));
    $product->refresh();

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Headphone baru',
        'price' => '900000.00',
        'stock' => 8,
        'is_active' => false,
    ]);
    expect($product->image_path)->not->toBe('products/old-image.jpg');
    Storage::disk('public')->assertMissing('products/old-image.jpg');
    Storage::disk('public')->assertExists($product->image_path);
});

it('keeps the existing image when a product is updated without a new upload', function () {
    Storage::fake('public');
    $this->seed(RolePermissionSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $category = Category::factory()->create();
    Storage::disk('public')->put('products/current-image.jpg', 'current image');
    $product = Product::factory()->for($category)->create([
        'image_path' => 'products/current-image.jpg',
    ]);

    $response = $this->actingAs($admin)->put(route('admin.products.update', $product), [
        'category_id' => $category->id,
        'name' => 'Nama diperbarui',
        'description' => null,
        'price' => '12000.00',
        'stock' => 4,
        'is_active' => true,
    ]);

    $response->assertRedirect(route('admin.products.index'));
    expect($product->refresh()->image_path)->toBe('products/current-image.jpg');
    Storage::disk('public')->assertExists('products/current-image.jpg');
});

it('deletes a product and its image', function () {
    Storage::fake('public');
    $this->seed(RolePermissionSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $category = Category::factory()->create();
    Storage::disk('public')->put('products/to-delete.jpg', 'product image');
    $product = Product::factory()->for($category)->create([
        'image_path' => 'products/to-delete.jpg',
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.products.destroy', $product));

    $response->assertRedirect(route('admin.products.index'));
    $this->assertDatabaseMissing('products', ['id' => $product->id]);
    Storage::disk('public')->assertMissing('products/to-delete.jpg');
});

it('forbids an admin without product creation permission', function () {
    $this->seed(RolePermissionSeeder::class);
    $adminRole = Role::findByName('admin');
    $adminRole->revokePermissionTo('products.create');
    $admin = User::factory()->create();
    $admin->assignRole($adminRole);

    $this->actingAs($admin)
        ->get(route('admin.products.create'))
        ->assertForbidden();
});

it('escapes product names and descriptions in the catalog', function () {
    $this->seed(RolePermissionSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $category = Category::factory()->create();
    Product::factory()->for($category)->create([
        'name' => '<script>alert("product")</script>',
        'description' => '<img src=x onerror=alert(1)>',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.products.index'))
        ->assertOk()
        ->assertSee('<script>alert("product")</script>')
        ->assertSee('<img src=x onerror=alert(1)>')
        ->assertDontSee('<script>alert("product")</script>', false)
        ->assertDontSee('<img src=x onerror=alert(1)>', false);
});
