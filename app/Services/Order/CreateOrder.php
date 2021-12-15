<?php

namespace App\Services\Order;

use App\Exceptions\CouponCodeUnavailableException;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;
use App\Models\Order\Order;
use App\Models\Product\ProductSku;
use App\Models\Promotion\CouponCode;
use App\Services\BaseService;
use App\Models\User\Address;
use App\Rules\LoginUser;
use App\Rules\ValidCouponCode;
use App\Services\Order\Cart\RemoveCart;
use App\Services\User\Address\CreateAddress;
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
            'remark' => ['nullable', 'string', 'max:200'],
            'user' => ['required', new LoginUser],
            'coupon_code' => ['nullable', new ValidCouponCode]
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

            $orderAddress = app(CreateAddress::class)->execute(array_merge($address->toArray(), ['user_id' => 0]));

            // 创建一个订单
            $order = new Order([
                'address_id' => $orderAddress->id,
                'remark' => $this->dataGet($data, 'remark'),
                'total_amount' => 0,
                'user_id' => $user->id,
            ]);
            // 写入数据库
            $order->save();

            $totalAmount = 0;
            // 遍历用户提交的 SKU
            foreach ($data['items'] as $item) {
                $sku  = ProductSku::find($item['sku_id']);
                if ($sku->decreaseStock($item['amount']) <= 0) {
                    throw new InvalidRequestException('该商品库存不足');
                }

                // 创建一个 OrderItem 并直接与当前订单关联
                $orderItem = $order->items()->make([
                    'amount' => $item['amount'],
                    'price' => $sku->price,
                    'product_id' => $sku->product_id,
                    'product_sku_id' => $sku->id,
                ]);
                $orderItem->save();

                $totalAmount += $sku->price * $item['amount'];
            }

            $couponCodeId = 0;
            if ($couponCode = $this->dataGet($data, 'coupon_code')) {
                $coupon = CouponCode::where('code', $couponCode)->first();
                // 总金额已经计算出来了，检查是否符合优惠券规则
                $coupon->checkAvailable($user, $totalAmount);
                // 增加优惠券的用量，需判断返回值
                if ($coupon->changeUsed() <= 0) {
                    throw new CouponCodeUnavailableException('该优惠券已被兑完');
                }
                // 把订单金额修改为优惠后的金额
                $totalAmount = $coupon->getAdjustedPrice($totalAmount);
                $couponCodeId = $coupon->id;
            }

            // 更新订单总金额
            $order->update([
                'total_amount' => $totalAmount,
                'coupon_code_id' => $couponCodeId
            ]);

            // 将下单的商品从购物车中移除
            $skuIds = collect($data['items'])->pluck('sku_id')->all();
            app(RemoveCart::class)->execute(compact('skuIds', 'user'));

            return $order;
        });
        dispatch(new CloseOrder($order, config('app.order_ttl')));

        return $order;
    }
}
