<?php

use App\Models\Category;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

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
            'code' => 200,
            'status' => 'success',
            'message' => 'Data kategori berhasil diambil.',
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

it('formats error responses with the matching HTTP status and response envelope', function () {
    $response = ApiResponse::error('Input tidak valid.', 422, [
        'name' => ['Nama wajib diisi.'],
    ]);

    expect($response)->toBeInstanceOf(JsonResponse::class);
    expect($response->getStatusCode())->toBe(422);
    expect($response->getData(true))->toBe([
        'code' => 422,
        'status' => 'error',
        'message' => 'Input tidak valid.',
        'data' => [
            'name' => ['Nama wajib diisi.'],
        ],
    ]);
});
