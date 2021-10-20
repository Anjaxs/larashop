<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('order_id')->comment('订单id orders');
            // $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->unsignedInteger('product_id')->comment('商品id products');
            // $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unsignedInteger('product_sku_id')->comment('sku id product_skus');
            // $table->foreign('product_sku_id')->references('id')->on('product_skus')->onDelete('cascade');
            $table->unsignedInteger('amount')->comment('购买数量');
            $table->decimal('price', 10, 2)->comment('购买单价');
            $table->unsignedTinyInteger('rating')->default(0)->comment('评分');
            $table->string('review', 500)->default(0)->comment('评价');
            $table->timestamp('reviewed_at')->default(config('app.null_time'))->comment('评价时间');
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
        Schema::dropIfExists('order_items');
    }
}
