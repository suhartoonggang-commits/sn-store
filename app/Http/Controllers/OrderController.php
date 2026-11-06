<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function show(Request $request, Order $order): View
    {
        $order = $request->user()
            ->orders()
            ->with('items')
            ->whereKey($order->id)
            ->firstOrFail();

        return view('pages.public.order', [
            'order' => $order,
        ]);
    }
}
