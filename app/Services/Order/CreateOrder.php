<?php

namespace App\Services\Order;

use App\Exceptions\InvalidRequestException;
use App\Models\Order\Order;
use App\Models\Product\ProductSku;
use App\Services\BaseService;
use App\Models\User\Address;
use App\Rules\LoginUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CreateOrder extends BaseService
{
    public function rules()
    {
        return [
            // 判断用户提交的地址 ID 是否存在于数据库并且属于当前用户
            // 后面这个条件非常重要，否则恶意用户可以用不同的地址 ID 不断提交订单来遍历出平台所有用户的收货地址
            'address_id' => [
                'required',
                Rule::exists('addresses', 'id')->where('user_id', $this->input['user']->id),
            ],
            'items' => ['required', 'array'],
            'items.*.sku_id' => [ // 检查 items 数组下每一个子数组的 sku_id 参数
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
                    // 获取当前索引
                    preg_match('/items\.(\d+)\.sku_id/', $attribute, $m);
                    $index = $m[1];
                    // 根据索引找到用户所提交的购买数量
                    $amount = $this->input['items'][$index]['amount'];
                    if ($amount > 0 && $amount > $sku->stock) {
                        return $fail('该商品库存不足');
                    }
                },
            ],
            'items.*.amount' => ['required', 'integer', 'min:1'],
            'remark' => ['required', 'string', 'nullable', 'max:200'],
            'user' => ['required', new LoginUser],
        ];
    }


    /**
     * 添加订单
     */
    public function execute(array $data)
    {
        $this->validate($data);

        // 开启一个数据库事务
        $order = DB::transaction(function () use ($data) {
            $user = $data['user'];
            $address = Address::find($data['address_id']);
            // 更新此地址的最后使用时间
            $address->update(['last_used_at' => Carbon::now()]);

            $orderAddress = $address->replicate()->fill(['user_id' => 0]);
            $orderAddress->save();

            // 创建一个订单
            $order = new Order([
                'address_id' => $orderAddress->id,
                'remark' => $this->dataGet($data, 'remark'),
                'total_amount' => 0,
            ]);
            // 订单关联到当前用户
            $order->user()->associate($user);
            // 写入数据库
            $order->save();

            $totalAmount = 0;
            $items = $data['items'];
            // 遍历用户提交的 SKU
            foreach ($items as $data) {
                $sku  = ProductSku::find($data['sku_id']);
                // 创建一个 OrderItem 并直接与当前订单关联
                $item = $order->items()->make([
                    'amount' => $data['amount'],
                    'price' => $sku->price,
                ]);
                $item->product()->associate($sku->product_id);
                $item->productSku()->associate($sku);
                $item->save();
                $totalAmount += $sku->price * $data['amount'];
                if ($sku->decreaseStock($data['amount']) <= 0) {
                    throw new InvalidRequestException('该商品库存不足');
                }
            }

            // 更新订单总金额
            $order->update(['total_amount' => $totalAmount]);

            // 将下单的商品从购物车中移除
            $skuIds = collect($items)->pluck('sku_id');
            $user->cartItems()->whereIn('product_sku_id', $skuIds)->delete();

            return $order;
        });

        return $order;
    }
}
