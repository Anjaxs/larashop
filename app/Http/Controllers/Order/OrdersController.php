<?php

namespace App\Http\Controllers\Order;

use App\Exceptions\InvalidRequestException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order\Order;
use App\Services\Order\ApplyRefund;
use App\Services\Order\CreateReview;

class OrdersController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();
        $data['user']  = $request->user();

        $order = app(\App\Services\Order\CreateOrder::class)->execute($data);

        return $order;
    }

    public function index(Request $request)
    {
        $orders = Order::query()
            // 使用 with 方法预加载，避免N + 1问题
            ->with(['items.product', 'items.productSku'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate();

        return view('orders.index', ['orders' => $orders]);
    }

    public function show(Order $order, Request $request)
    {
        $this->authorize('own', $order);
        return view('orders.show', ['order' => $order->load(['items.productSku', 'items.product', 'address'])]);
    }

    public function received(Order $order, Request $request)
    {
        // 校验权限
        $this->authorize('own', $order);

        // 判断订单的发货状态是否为已发货
        if ($order->ship_status !== Order::SHIP_STATUS_DELIVERED) {
            throw new InvalidRequestException('发货状态不正确');
        }

        // 更新发货状态为已收到
        $order->update(['ship_status' => Order::SHIP_STATUS_RECEIVED]);

        return $order;
    }

    public function review(Order $order)
    {
        // 校验权限
        $this->authorize('own', $order);
        // 判断是否已经支付
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可评价');
        }
        // 使用 load 方法加载关联数据，避免 N + 1 性能问题
        return view('orders.review', ['order' => $order->load(['items.productSku', 'items.product'])]);
    }

    public function sendReview(Order $order, Request $request)
    {
        // 校验权限
        $this->authorize('own', $order);

        app(CreateReview::class)->execute([
            'order_id' => $order->id,
            'reviews' => $request->input('reviews')
        ]);

        return redirect()->back();
    }

    public function applyRefund(Order $order, Request $request)
    {
        // 校验订单是否属于当前用户
        $this->authorize('own', $order);

        return app(ApplyRefund::class)->execute([
            'order_id' => $order->id,
            'reason' => $request->input('reason'),
        ]);
    }
}
