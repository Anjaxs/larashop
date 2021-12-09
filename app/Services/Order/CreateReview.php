<?php

namespace App\Services\Order;

use App\Exceptions\InvalidRequestException;
use App\Models\Order\Order;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CreateReview extends BaseService
{
    public function rules()
    {
        return [
            'order_id' => 'required|numeric|min:1',  // 订单id
            'reviews' => ['required', 'array'],
            'reviews.*.id' => [
                'required',
                Rule::exists('order_items', 'id')->where('order_id', $this->input['order_id'])
            ],
            'reviews.*.rating' => ['required', 'integer', 'between:1,5'],
            'reviews.*.review' => ['required'],
        ];
    }

    public function attributes()
    {
        return [
            'reviews.*.rating' => '评分',
            'reviews.*.review' => '评价',
        ];
    }

    /**
     * 支付回调
     */
    public function execute(array $data)
    {
        $this->validate($data);

        $order = Order::with('items')->find($data['order_id']);
        if ($order->paid_at == config('app.null_time')) {
            throw new InvalidRequestException('该订单未支付，不可评价');
        }
        // 判断是否已经评价
        if ($order->reviewed) {
            throw new InvalidRequestException('该订单已评价，不可重复提交');
        }
        $reviews = $data['reviews'];
        // 开启事务
        DB::transaction(function () use ($reviews, $order) {
            // 遍历用户提交的数据
            foreach ($reviews as $review) {
                $orderItem = $order->items->where('id', $review['id'])->first();
                // 保存评分和评价
                $orderItem->update([
                    'rating'      => $review['rating'],
                    'review'      => $review['review'],
                    'reviewed_at' => Carbon::now(),
                ]);
            }
            // 将订单标记为已评价
            $order->update(['reviewed' => true]);
        });
    }
}
