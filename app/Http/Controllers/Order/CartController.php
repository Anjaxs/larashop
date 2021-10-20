<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\Product\ProductSku;
use App\Services\Order\Cart\AddCart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $data = $request->all();
        $data['user'] = $request->user();
        app(AddCart::class)->execute($data);

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
        $request->user()->cartItems()->where('product_sku_id', $sku->id)->delete();

        return [];
    }
}
