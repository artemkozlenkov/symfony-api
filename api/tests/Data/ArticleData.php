<?php

namespace Webapp\Tests\Data;

class ArticleData
{
    public static function getData(string $id): array
    {
        $raw = self::prepareData($id);

        return ['json' => $raw];
    }

    private static function prepareData(string $id): array
    {
        switch ($id) {
            case 'article_post':
                return [
                    'data' => [
                        'type' => 'Article',
                        'attributes' => [
                            'name' => 'test name',
                            'category' => 'test category',
                            'content' => 'test content',
                        ],
                    ],
                ];
            case 'article_patch':
                return [
                ];
        }
    }
}
