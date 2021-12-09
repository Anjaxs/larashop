<?php

namespace App\Services\Order;

use App\Events\OrderPaid;
use App\Models\Order\Order;
use App\Services\BaseService;
use Carbon\Carbon;

class OrderPaySuccess extends BaseService
{
    public function rules()
    {
        return [
            'payment_way' => 'required|string|in: wechat_pay,alipay',  // 支付方式
            'order_no'    => 'required|string',    // 订单编号
            'payment_no'  => 'required|string',    // 支付交易号
        ];
    }

    /**
     * 支付回调
     */
    public function execute(array $data)
    {
        $this->validate($data);

        // 找到对应的订单
        $order = Order::where('no', $data['order_no'])->first();
        // 订单不存在
        if (!$order) {
            return 'fail';
        }
        // 订单已支付
        if ($order->paid_at != config('app.null_time')) {
            return app($data['payment_way'])->success();
        }
        // 将订单标记为已支付
        $order->update([
            'paid_at'        => Carbon::now(),
            'payment_method' => $data['payment_way'],
            'payment_no'     => $data['payment_no'],
        ]);

        event(new OrderPaid($order));

        return app($data['payment_way'])->success();
    }
}
