<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('no')->unique();
            $table->unsignedInteger('user_id')->comment('用户id');
            $table->unsignedInteger('address_id')->comment('地址id');
            $table->decimal('total_amount', 10, 2)->comment('总金额');
            $table->string('remark')->default('')->comment('订单备注');
            $table->timestamp('paid_at')->default(config('app.null_time'))->comment('支付时间');
            $table->string('payment_method')->default('')->comment('支付方式');
            $table->string('payment_no')->default('')->comment('支付平台订单号');
            $table->string('refund_status')->default(\App\Models\Order\Order::REFUND_STATUS_PENDING)->comment('退款状态');
            $table->string('refund_no')->default('')->comment('退款单号');
            $table->boolean('closed')->default(false)->comment('是否已关闭');
            $table->boolean('reviewed')->default(false)->comment('是否已评价');
            $table->string('ship_status')->default(\App\Models\Order\Order::SHIP_STATUS_PENDING)->comment('物流状态');
            $table->text('ship_data')->nullable()->comment('物流数据');
            $table->text('extra')->nullable()->comment('额外数据');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
