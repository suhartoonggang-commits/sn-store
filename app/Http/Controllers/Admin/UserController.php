<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdminDemoData;
use Illuminate\Contracts\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.users.index', [
            'users' => AdminDemoData::users(),
            'transactions' => AdminDemoData::transactions(),
        ]);
    }
}
