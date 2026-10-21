<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = collect([
            [
                'name' => 'Audio',
                'slug' => 'audio',
                'description' => 'Headphone, earbud, dan speaker untuk setiap momen.',
                'product_count' => 24,
                'is_active' => true,
                'created_at' => '14 Okt 2026',
            ],
            [
                'name' => 'Smartphone',
                'slug' => 'smartphone',
                'description' => 'Ponsel pintar untuk komunikasi dan produktivitas.',
                'product_count' => 18,
                'is_active' => true,
                'created_at' => '12 Okt 2026',
            ],
            [
                'name' => 'Laptop',
                'slug' => 'laptop',
                'description' => 'Laptop untuk bekerja, belajar, dan berkarya.',
                'product_count' => 12,
                'is_active' => true,
                'created_at' => '10 Okt 2026',
            ],
            [
                'name' => 'Wearable',
                'slug' => 'wearable',
                'description' => 'Perangkat wearable untuk aktivitas sehari-hari.',
                'product_count' => 9,
                'is_active' => true,
                'created_at' => '8 Okt 2026',
            ],
            [
                'name' => 'Kamera',
                'slug' => 'kamera',
                'description' => 'Kamera dan perlengkapan untuk mengabadikan momen.',
                'product_count' => 7,
                'is_active' => true,
                'created_at' => '6 Okt 2026',
            ],
            [
                'name' => 'Aksesori',
                'slug' => 'aksesori',
                'description' => 'Aksesori pelengkap perangkat elektronik.',
                'product_count' => 31,
                'is_active' => true,
                'created_at' => '4 Okt 2026',
            ],
            [
                'name' => 'Tablet',
                'slug' => 'tablet',
                'description' => 'Tablet untuk hiburan dan produktivitas.',
                'product_count' => 8,
                'is_active' => true,
                'created_at' => '2 Okt 2026',
            ],
            [
                'name' => 'Gaming',
                'slug' => 'gaming',
                'description' => 'Konsol dan perlengkapan gaming.',
                'product_count' => 11,
                'is_active' => false,
                'created_at' => '1 Okt 2026',
            ],
        ]);

        return view('pages.admin.categories.index', [
            'categories' => $categories,
        ]);
    }
}
