<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrdersController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();
        $data['user']  = $request->user();

        $order = app(\App\Services\Order\CreateOrder::class)->execute($data);

        return $order;
    }
}
