<?php

namespace Database\Factories;

use App\Models\Order\OrderItem;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // 从数据库随机取一条商品
        $product = Product::query()->where('on_sale', true)->inRandomOrder()->first();
        // 从该商品的 SKU 中随机取一条
        $sku = $product->skus()->inRandomOrder()->first();

        return [
            'amount'         => random_int(1, 5), // 购买数量随机 1 - 5 份
            'price'          => $sku->price,
            'rating'         => 0,
            'review'         => '',
            'reviewed_at'    => config('app.null_time'),
            'product_id'     => $product->id,
            'product_sku_id' => $sku->id,
        ];
    }
}
