<?php

namespace App\Services\Order\Cart;

use App\Models\Order\CartItem;
use App\Models\Product\ProductSku;
use App\Models\User\User;
use App\Rules\LoginUser;
use App\Services\BaseService;

class AddCart extends BaseService
{
    public function rules()
    {
        return [
            'sku_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!$sku = ProductSku::find($value)) {
                        return $fail('该商品不存在');
                    }
                    if (!$sku->product->on_sale) {
                        return $fail('该商品未上架');
                    }
                    if ($sku->stock === 0) {
                        return $fail('该商品已售完');
                    }
                    if ($this->input['amount'] > 0 && $sku->stock < $this->input['amount']) {
                        return $fail('该商品库存不足');
                    }
                },
            ],
            'amount' => ['required', 'integer', 'min:1'],
            'user' => ['required', new LoginUser],
        ];
    }

    public function attributes()
    {
        return [
            'amount' => '商品数量'
        ];
    }

    public function messages()
    {
        return [
            'sku_id.required' => '请选择商品'
        ];
    }

    /**
     * 给用户添加商品到购物车记录
     */
    public function execute(array $data)
    {
        $this->validate($data);

        $user = request()->user();
        // 从数据库中查询该商品是否已经在购物车中
        if ($cart = $user->cartItems()->where('product_sku_id', $data['sku_id'])->first()) {
            // 如果存在则直接叠加商品数量
            $cart->update([
                'amount' => $cart->amount + $data['amount'],
            ]);
        } else {
            // 否则创建一个新的购物车记录
            $cart = new CartItem(['amount' => $data['amount']]);
            $cart->user()->associate($user);
            $cart->productSku()->associate($data['sku_id']);
            $cart->save();
        }

        return true;
    }
}
