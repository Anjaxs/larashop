<?php

namespace App\Services\Order;

use App\Exceptions\InvalidRequestException;
use App\Models\Order\Order;
use App\Services\BaseService;
use Carbon\Carbon;

class HandleRefund extends BaseService
{
    public function rules()
    {
        return [
            'order_id' => ['required'],
            'agree'  => ['required', 'boolean'],
            'reason' => ['required_if:agree,false'],  // 拒绝退款时需要输入拒绝理由
        ];
    }

    /**
     * 支付回调
     */
    public function execute(array $data)
    {
        $this->validate($data);

        $order = Order::find($data['order_id']);
        // 判断订单状态是否正确
        if ($order->refund_status !== Order::REFUND_STATUS_APPLIED) {
            throw new InvalidRequestException('订单状态不正确');
        }
        // 是否同意退款
        if ($data['agree']) {
            // 同意退款的逻辑这里先留空
            // todo
        } else {
            // 将拒绝退款理由放到订单的 extra 字段中
            $extra = $order->extra ?: [];
            $extra['refund_disagree_reason'] = $data['reason'];
            // 将订单的退款状态改为未退款
            $order->update([
                'refund_status' => Order::REFUND_STATUS_PENDING,
                'extra'         => $extra,
            ]);
        }

        return $order;
    }
}
