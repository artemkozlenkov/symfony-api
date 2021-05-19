<?php

namespace Webapp\Tests\Data;

interface DataProviderInterface
{
    /**
     * Method return the jsonapi format array.
     * @param string $id
     * @return array
     */
    public static function getData(string $id): array;

    /**
     * Method return the appropriate base data array by argument.
     * @param string $id
     * @return array
     */
    public static function prepareData(string $id): array;
}
