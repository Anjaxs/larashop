<?php

namespace App\Services\Product;

use App\Services\BaseService;
use App\SearchBuilders\ProductSearchBuilder;

class GetSimilarProductIds extends BaseService
{
    public function rules()
    {
        return [
            'product' => ['required', new \App\Rules\IsTypeOf('\App\Models\User\User')],
            'amount'  => ['required', 'integer'],
        ];
    }

    /**
     * 添加用户收货地址
     */
    public function execute(array $data)
    {
        $this->validate($data);
        $product = $data['product'];
        $amount = $data['amount'];

        // 如果商品没有商品属性，则直接返回空
        if (count($product->properties) === 0) {
            return [];
        }
        $builder = (new ProductSearchBuilder())->onSale()->paginate($amount, 1);
        foreach ($product->properties as $property) {
            $builder->propertyFilter($property->name, $property->value, 'should');
        }
        $builder->minShouldMatch(ceil(count($product->properties) / 2));
        $params = $builder->getParams();
        $params['body']['query']['bool']['must_not'] = [['term' => ['_id' => $product->id]]];
        $result = app('es')->search($params);

        return collect($result['hits']['hits'])->pluck('_id')->all();
    }
}
