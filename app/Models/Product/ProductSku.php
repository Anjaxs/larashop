<?php

namespace App\Models\Product;

use App\Exceptions\InternalException;
use Database\Factories\ProductSkuFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSku extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'price', 'stock', 'product_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected static function newFactory()
    {
        return app(ProductSkuFactory::class);
    }

    /**
     * @return int 返回影响条数
     */
    public function decreaseStock($amount)
    {
        if ($amount < 0) {
            throw new InternalException('减库存不可小于0');
        }

        return $this->where('id', $this->id)->where('stock', '>=', $amount)->decrement('stock', $amount);
    }

    public function addStock($amount)
    {
        if ($amount < 0) {
            throw new InternalException('加库存不可小于0');
        }
        $this->increment('stock', $amount);
    }
}
