<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace LegacyTests\Unit\Core\Product\Search;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class SortOrderTest extends TestCase
{
    public function testToLegacyOrderByProductName()
    {
        static::assertEquals(
            'name',
            (new SortOrder('product', 'name'))->toLegacyOrderBy(false)
        );

        static::assertEquals(
            'pl.name',
            (new SortOrder('product', 'name'))->toLegacyOrderBy(true)
        );
    }

    public function testToLegacyOrderByProductPrice()
    {
        static::assertEquals(
            'price',
            (new SortOrder('product', 'price'))->toLegacyOrderBy(false)
        );

        static::assertEquals(
            'p.price',
            (new SortOrder('product', 'price'))->toLegacyOrderBy(true)
        );
    }

    public function testToLegacyOrderByProductPosition()
    {
        static::assertEquals(
            'position',
            (new SortOrder('product', 'position'))->toLegacyOrderBy(false)
        );

        static::assertEquals(
            'cp.position',
            (new SortOrder('product', 'position'))->toLegacyOrderBy(true)
        );
    }

    public function testToLegacyOrderByManufacturerName()
    {
        static::assertEquals(
            'manufacturer_name',
            (new SortOrder('manufacturer', 'name'))->toLegacyOrderBy(false)
        );

        static::assertEquals(
            'm.name',
            (new SortOrder('manufacturer', 'name'))->toLegacyOrderBy(true)
        );
    }

    public function testToLegacyOrderWayAsc()
    {
        static::assertEquals(
            'asc',
            (new SortOrder('product', 'name', 'asc'))->toLegacyOrderWay()
        );
    }

    public function testToLegacyOrderWayDesc()
    {
        static::assertEquals(
            'desc',
            (new SortOrder('product', 'name', 'desc'))->toLegacyOrderWay()
        );
    }

    /**
     * dataProvider for test_serialization
     */
    public function serialization_examples()
    {
        return [
            [['entity'    => 'product',
                'field'      => 'name',
                'direction'  => 'asc', ]],
        ];
    }

    /**
     * @dataProvider serialization_examples
     */
    public function testSerialization($data)
    {
        $opt = new SortOrder($data['entity'], $data['field'], $data['direction']);

        $encoded = $opt->toString();
        static::assertInternalType('string', $encoded);

        $unserialized = SortOrder::newFromString($encoded);

        $arr = $unserialized->toArray();
        static::assertEquals($data['entity'],    $arr['entity']);
        static::assertEquals($data['field'],     $arr['field']);
        static::assertEquals($data['direction'], $arr['direction']);
    }
}
