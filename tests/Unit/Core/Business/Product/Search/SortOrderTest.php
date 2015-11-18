<?php

namespace PrestaShop\PrestaShop\tests\Unit\Core\Business\Product\Search;

use PHPUnit_Framework_TestCase;
use PrestaShop\PrestaShop\Core\Business\Product\Search\SortOrder;

class SortOrderTest extends PHPUnit_Framework_TestCase
{
    public function test_toLegacyOrderBy_product_name()
    {
        $this->assertEquals(
            'name',
            (new SortOrder('product', 'name'))->toLegacyOrderBy(false)
        );

        $this->assertEquals(
            'pl.name',
            (new SortOrder('product', 'name'))->toLegacyOrderBy(true)
        );
    }

    public function test_toLegacyOrderBy_product_price()
    {
        $this->assertEquals(
            'price',
            (new SortOrder('product', 'price'))->toLegacyOrderBy(false)
        );

        $this->assertEquals(
            'p.price',
            (new SortOrder('product', 'price'))->toLegacyOrderBy(true)
        );
    }

    public function test_toLegacyOrderBy_product_position()
    {
        $this->assertEquals(
            'position',
            (new SortOrder('product', 'position'))->toLegacyOrderBy(false)
        );

        $this->assertEquals(
            'cp.position',
            (new SortOrder('product', 'position'))->toLegacyOrderBy(true)
        );
    }

    public function test_toLegacyOrderBy_manufacturer_name()
    {
        $this->assertEquals(
            'manufacturer_name',
            (new SortOrder('manufacturer', 'name'))->toLegacyOrderBy(false)
        );

        $this->assertEquals(
            'm.name',
            (new SortOrder('manufacturer', 'name'))->toLegacyOrderBy(true)
        );
    }

    public function test_toLegacyOrderWay_asc()
    {
        $this->assertEquals(
            'asc',
            (new SortOrder('product', 'name', 'asc'))->toLegacyOrderWay()
        );
    }

    public function test_toLegacyOrderWay_desc()
    {
        $this->assertEquals(
            'desc',
            (new SortOrder('product', 'name', 'desc'))->toLegacyOrderWay()
        );
    }
}
