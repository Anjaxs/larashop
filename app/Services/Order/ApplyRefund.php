<?php

namespace App\Services\Order;

use App\Exceptions\InvalidRequestException;
use App\Models\Order\Order;
use App\Services\BaseService;

class ApplyRefund extends BaseService
{
    public function rules()
    {
        return [
            'order_id' => 'required',
            'reason' => 'required|string',  // 退款原因
        ];
    }

    /**
     * 申请退款
     */
    public function execute(array $data)
    {
        $this->validate($data);

        $order = Order::find($data['order_id']);
        // 判断订单是否已付款
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可退款');
        }
        // 判断订单退款状态是否正确
        if ($order->refund_status !== Order::REFUND_STATUS_PENDING) {
            throw new InvalidRequestException('该订单已经申请过退款，请勿重复申请');
        }
        // 将用户输入的退款理由放到订单的 extra 字段中
        $extra = $order->extra ?: [];
        $extra['refund_reason'] = $data['reason'];
        // 将订单退款状态改为已申请退款
        $order->update([
            'refund_status' => Order::REFUND_STATUS_APPLIED,
            'extra'         => $extra,
        ]);

        return $order;
    }
}
