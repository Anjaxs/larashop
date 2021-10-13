<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Services\Order\Cart\AddCart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request)
    {
        app(AddCart::class)->execute($request->all());

        return [];
    }
}
