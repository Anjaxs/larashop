<?php

namespace App\Console\Commands\Elasticsearch;

use App\Models\Product\Product;
use Illuminate\Console\Command;

class SyncProducts extends Command
{
    protected $signature = 'es:sync-products';

    protected $description = '将商品数据同步到 Elasticsearch';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        /** @var \Elasticsearch\Client 对象*/
        $es = app('es');

        $this->createProductsIndexIfNotExist($es);

        Product::query()
            // 预加载 SKU 和 商品属性数据，避免 N + 1 问题
            ->with(['skus', 'properties'])
            // 使用 chunkById 避免一次性加载过多数据
            ->chunkById(100, function ($products) use ($es) {
                $this->info(sprintf('正在同步 ID 范围为 %s 至 %s 的商品', $products->first()->id, $products->last()->id));

                // 初始化请求体
                $req = ['body' => []];
                // 遍历商品
                foreach ($products as $product) {
                    // 将商品模型转为 Elasticsearch 所用的数组
                    $data = $product->toESArray();

                    $req['body'][] = [
                        'index' => [
                            '_index' => 'products',
                            '_id'    => $data['id'],
                        ],
                    ];
                    $req['body'][] = $data;
                }
                try {
                    // 使用 bulk 方法批量创建
                    $es->bulk($req);
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
            });
        $this->info('同步完成');
    }

    /**
     * 如果不存在 products index 则创建
     * @param \Elasticsearch\Client $es
     */
    protected function createProductsIndexIfNotExist($es)
    {
        try {
            $es->indices()->getMapping(['index' => 'products']);
        } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $e) {
            $es->indices()->create([
                'index' => 'products',
                'body' => [
                    'mappings' => [
                        'properties'  => [
                            "type" => [
                                "type" => "keyword"
                            ],
                            "title" => [
                                "type" => "text",
                                "analyzer" => "ik_smart"
                            ],
                            "long_title" => [
                                "type" => "text",
                                "analyzer" => "ik_smart"
                            ],
                            "category_id" => [
                                "type" => "integer"
                            ],
                            "category" => [
                                "type" => "keyword"
                            ],
                            "category_path" => [
                                "type" => "keyword"
                            ],
                            "description" => [
                                "type" => "text",
                                "analyzer" => "ik_smart"
                            ],
                            "price" => [
                                "type" => "scaled_float",
                                "scaling_factor" => 100
                            ],
                            "on_sale" => [
                                "type" => "boolean"
                            ],
                            "rating" => [
                                "type" => "float"
                            ],
                            "sold_count" => [
                                "type" => "integer"
                            ],
                            "review_count" => [
                                "type" => "integer"
                            ],
                            "skus" => [
                                "type" => "nested",
                                "properties" => [
                                    "title" => [
                                        "type" => "text",
                                        "analyzer" => "ik_smart",
                                        "copy_to" => "skus_title"
                                    ],
                                    "description" => [
                                        "type" => "text",
                                        "analyzer" => "ik_smart",
                                        "copy_to" => "skus_description"
                                    ],
                                    "price" => [
                                        "type" => "scaled_float",
                                        "scaling_factor" => 100
                                    ]
                                ]
                            ],
                            "properties" => [
                                "type" => "nested",
                                "properties" => [
                                    "name" => [
                                        "type" => "keyword"
                                    ],
                                    "value" => [
                                        "type" => "keyword",
                                        "copy_to" => "properties_value"
                                    ],
                                    "search_value" => [
                                        "type" => "keyword",
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
        }
    }
}
