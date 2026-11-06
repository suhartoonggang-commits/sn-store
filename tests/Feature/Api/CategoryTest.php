<?php

use App\Models\Category;

it('returns active categories publicly with the requested fields and timestamps', function () {
    $category = Category::factory()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => 'Headphone dan speaker.',
        'is_active' => true,
    ]);

    Category::factory()->create([
        'name' => 'Kamera',
        'slug' => 'kamera',
        'description' => 'Kamera digital.',
        'is_active' => false,
    ]);

    $this->getJson(route('api.categories.index'))
        ->assertOk()
        ->assertExactJson([
            'data' => [[
                'id' => $category->id,
                'nama' => 'Audio',
                'slug' => 'audio',
                'deskripsi' => 'Headphone dan speaker.',
                'created_at' => $category->created_at->toISOString(),
                'updated_at' => $category->updated_at->toISOString(),
            ]],
        ]);
});
