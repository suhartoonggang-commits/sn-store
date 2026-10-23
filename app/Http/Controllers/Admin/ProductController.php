<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdminDemoData;
use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.products.index', [
            'categories' => AdminDemoData::categories(),
            'products' => AdminDemoData::products(),
        ]);
    }
}
