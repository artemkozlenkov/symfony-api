<?php

namespace Webapp\Tests\Data;

class ProductData implements DataProviderInterface
{
    /**
     * Method return the jsonapi format array.
     */
    public static function getData(string $id): array
    {
        return ['json' => self::prepareData($id)];
    }

    /**
     * Method return the appropriate base data array by argument.
     */
    public static function prepareData(string $id): array
    {
        switch ($id) {
            case 'product_post':
                return [
                    'data' => [
                        'type' => 'Product',
                        'attributes' => [
                            'name' => 'test product name',
                            'quantity' => 4,
                        ],
                        'relationships' => [
                            'category' => [
                                'data' => [
                                    'type' => 'Category',
                                    'id' => '/categories/1',
                                ],
                            ],
                        ],
                    ],
                ];
            case 'article_patch':
                return [
                ];
        }
    }
}
