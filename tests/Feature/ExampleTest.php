<?php

use App\Models\Category;
use App\Models\Product;

it('renders active categories and products from the database', function () {
    $category = Category::factory()->create([
        'name' => 'Kamera Digital',
        'slug' => 'kamera-digital',
    ]);
    $inactiveCategory = Category::factory()->create([
        'name' => 'Kategori Nonaktif',
        'is_active' => false,
    ]);
    Product::factory()->for($category)->create([
        'name' => 'Kamera Mirrorless',
        'price' => 12499000,
        'stock' => 4,
    ]);
    Product::factory()->for($inactiveCategory)->create([
        'name' => 'Produk Nonaktif',
        'is_active' => false,
    ]);

    $response = $this->get('/');

    $response->assertOk()
        ->assertViewIs('pages.public.home')
        ->assertSeeText('Produk terbaru')
        ->assertSeeText('Kamera Digital')
        ->assertSeeText('Kamera Mirrorless')
        ->assertSeeText('Rp12.499.000')
        ->assertSeeText('Stok: 4')
        ->assertDontSeeText('Kategori Nonaktif')
        ->assertDontSeeText('Produk Nonaktif')
        ->assertDontSeeText('Sony Alpha A6400')
        ->assertViewHas('navigationCategories', fn ($categories) => $categories->count() === 1)
        ->assertViewHas('products', fn ($products) => $products->count() === 1);

    expect(substr_count($response->getContent(), 'data-product-card'))->toBe(1);
});

it('does not show sample products when the database is empty', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertSeeText('Belum ada produk yang tersedia.')
        ->assertDontSeeText('Sony Alpha A6400');

    expect(substr_count($response->getContent(), 'data-product-card'))->toBe(0);
});

it('filters catalog products by category and search query', function () {
    $audioCategory = Category::factory()->create([
        'name' => 'Audio',
        'slug' => 'audio',
    ]);
    $cameraCategory = Category::factory()->create([
        'name' => 'Kamera',
        'slug' => 'kamera',
    ]);
    Product::factory()->for($audioCategory)->create([
        'name' => 'Headphone Wireless',
        'description' => 'Headphone untuk perjalanan.',
    ]);
    Product::factory()->for($audioCategory)->create([
        'name' => 'Speaker Bluetooth',
        'description' => 'Speaker portabel.',
    ]);
    Product::factory()->for($cameraCategory)->create([
        'name' => 'Kamera Wireless',
        'description' => 'Kamera digital.',
    ]);

    $response = $this->get(route('products.index', [
        'category' => 'audio',
        'q' => 'wireless',
    ]));

    $response->assertOk()
        ->assertViewIs('pages.public.products')
        ->assertSeeText('Headphone Wireless')
        ->assertDontSeeText('Speaker Bluetooth')
        ->assertDontSeeText('Kamera Wireless')
        ->assertSee('Login untuk membeli')
        ->assertSee('value="wireless"', false)
        ->assertViewHas('products', fn ($products) => $products->total() === 1);

    expect($response->getContent())->not->toContain('@click="cartCount++"');
});
