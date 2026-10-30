<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Services\ProductService;
use App\Support\AdminDemoData;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(CategoryService $categoryService, ProductService $productService): View
    {
        return view('pages.admin.dashboard.index', [
            'categoryCount' => $categoryService->count(),
            'productCount' => $productService->count(),
            'transactionCount' => AdminDemoData::transactions()->count(),
            'userCount' => AdminDemoData::users()->count(),
        ]);
    }
}
