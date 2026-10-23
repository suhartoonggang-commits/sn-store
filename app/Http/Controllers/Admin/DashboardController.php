<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdminDemoData;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.dashboard.index', [
            'categoryCount' => AdminDemoData::categories()->count(),
            'productCount' => AdminDemoData::products()->count(),
            'transactionCount' => AdminDemoData::transactions()->count(),
            'userCount' => AdminDemoData::users()->count(),
        ]);
    }
}
