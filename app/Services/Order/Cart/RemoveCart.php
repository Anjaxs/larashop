<?php

namespace App\Services\Order\Cart;

use App\Rules\LoginUser;
use App\Services\BaseService;

class RemoveCart extends BaseService
{
    public function rules()
    {
        return [
            'skuIds' => ['required'],
            'user' => ['required', new LoginUser],
        ];
    }

    /**
     * 给用户移除购物车记录
     */
    public function execute(array $data)
    {
        $this->validate($data);

        // 提取 $data 的变量
        extract($data);

        // 可以传单个 ID，也可以传 ID 数组
        if (!is_array($skuIds)) {
            $skuIds = [$skuIds];
        }
        $user->cartItems()->whereIn('product_sku_id', $skuIds)->delete();

        return true;
    }
}
