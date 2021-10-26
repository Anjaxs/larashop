<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\Product\ProductSku;
use App\Services\Order\Cart\AddCart;
use App\Services\Order\Cart\RemoveCart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request)
    {
        app(AddCart::class)->execute($request->all() + [
            'user' => $request->user(),
        ]);

        return [];
    }

    public function index(Request $request)
    {
        $cartItems = $request->user()->cartItems()->with(['productSku.product'])->get();
        $addresses = $request->user()->addresses()->orderBy('last_used_at', 'desc')->get();

        return view('cart.index', ['cartItems' => $cartItems, 'addresses' => $addresses]);
    }

    public function remove(ProductSku $sku, Request $request)
    {
        app(RemoveCart::class)->execute([
            'skuIds' => $sku->id,
            'user' => $request->user(),
        ]);

        return [];
    }
}
