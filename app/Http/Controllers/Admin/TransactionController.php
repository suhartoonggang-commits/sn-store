<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdminDemoData;
use Illuminate\Contracts\View\View;

class TransactionController extends Controller
{
    public function index(): View
    {
        $transactions = AdminDemoData::transactions();

        return view('pages.admin.transactions.index', [
            'transactions' => $transactions,
            'paymentCount' => $transactions->count(),
            'paidCount' => $transactions->where('payment_status', 'Lunas')->count(),
            'pendingCount' => $transactions->where('payment_status', 'Menunggu pembayaran')->count(),
        ]);
    }
}
