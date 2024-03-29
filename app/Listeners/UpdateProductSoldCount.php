<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Models\Order\Order;
use App\Models\Order\OrderItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

//  implements ShouldQueue 代表此监听器是异步执行的
class UpdateProductSoldCount implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Laravel 会默认执行监听器的 handle 方法，触发的事件会作为 handle 方法的参数
     *
     * @param  OrderPaid  $event
     * @return void
     */
    public function handle(OrderPaid $event)
    {
        // 从事件对象中取出对应的订单
        $order = $event->getOrder();
        // 预加载商品数据
        $order->load('items.product');
        // 循环遍历订单的商品
        foreach ($order->items as $item) {
            $product = $item->product;
            // 计算对应商品的销量
            $soldCount = OrderItem::query()
                ->where('product_id', $product->id)
                ->whereIn('order_id', Order::where('paid_at', '!=', config('app.null_time'))->select('id'))
                ->sum('amount');
            // 更新商品销量
            $product->update([
                'sold_count' => $soldCount,
            ]);
        }
    }
}
