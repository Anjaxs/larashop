<?php

namespace Database\Factories;

use App\Models\Order\Order;
use App\Models\Promotion\CouponCode;
use App\Models\User\User;
use App\Services\User\Address\CreateAddress;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // 随机取一个用户
        $user = User::query()->inRandomOrder()->first();
        // 随机取一个该用户的地址
        $address = $user->addresses()->inRandomOrder()->first();
        // 10% 的概率把订单标记为退款
        $refund = random_int(0, 10) < 1;
        // 随机生成发货状态
        $ship = $this->faker->randomElement(array_keys(Order::$shipStatusMap));
        // 优惠券
        $coupon = null;
        // 30% 概率该订单使用了优惠券
        if (random_int(0, 10) < 3) {
            // 为了避免出现逻辑错误，我们只选择没有最低金额限制的优惠券
            $coupon = CouponCode::query()->where('min_amount', 0)->inRandomOrder()->first();
            // 增加优惠券的使用量
            $coupon->changeUsed();
        }

        // 更新此地址的最后使用时间
        $address->update(['last_used_at' => Carbon::now()]);

        $orderAddress = app(CreateAddress::class)->execute(array_merge($address->toArray(), ['user_id' => 0]));

        return [
            'address_id'     => $orderAddress->id,
            'total_amount'   => 0,
            'remark'         => $this->faker->sentence,
            'paid_at'        => $this->faker->dateTimeBetween('-30 days'), // 30天前到现在任意时间点
            'payment_method' => $this->faker->randomElement(['wechat_pay', 'alipay']),
            'payment_no'     => $this->faker->uuid,
            'refund_status'  => $refund ? Order::REFUND_STATUS_SUCCESS : Order::REFUND_STATUS_PENDING,
            'refund_no'      => $refund ? Order::getAvailableRefundNo() : '',
            'closed'         => false,
            'reviewed'       => random_int(0, 10) > 2,
            'ship_status'    => $ship,
            'ship_data'      => $ship === Order::SHIP_STATUS_PENDING ? null : [
                'express_company' => $this->faker->company,
                'express_no'      => $this->faker->uuid,
            ],
            'extra'          => $refund ? ['refund_reason' => $this->faker->sentence] : [],
            'user_id'        => $user->id,
            'coupon_code_id' => $coupon ? $coupon->id : 0,
        ];
    }
}
