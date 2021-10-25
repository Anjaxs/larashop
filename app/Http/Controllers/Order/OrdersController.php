<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order\Order;

class OrdersController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();
        $data['user']  = $request->user();

        $order = app(\App\Services\Order\CreateOrder::class)->execute($data);

        return $order;
    }

    public function index(Request $request)
    {
        $orders = Order::query()
            // 使用 with 方法预加载，避免N + 1问题
            ->with(['items.product', 'items.productSku'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate();

        return view('orders.index', ['orders' => $orders]);
    }
}
