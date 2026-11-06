<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(CategoryService $categoryService): JsonResponse
    {
        return response()->json([
            'data' => $categoryService->getPublicCategories(),
        ]);
    }
}
