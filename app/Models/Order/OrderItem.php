<?php

namespace App\Models\Order;

use App\Models\Product\Product;
use App\Models\Product\ProductSku;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return app(OrderItemFactory::class);
    }

    protected $fillable = ['amount', 'price', 'rating', 'review', 'reviewed_at'];

    protected $dates = ['reviewed_at'];

    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productSku()
    {
        return $this->belongsTo(ProductSku::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
