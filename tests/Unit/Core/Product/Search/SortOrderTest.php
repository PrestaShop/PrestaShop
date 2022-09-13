<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Product\Search;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class SortOrderTest extends TestCase
{
    public function testToLegacyOrderByProductName()
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

    public function testToLegacyOrderByProductPrice()
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

    public function testToLegacyOrderByProductPosition()
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

    public function testToLegacyOrderByManufacturerName()
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

    public function testToLegacyOrderWayAsc()
    {
        $this->assertEquals(
            'asc',
            (new SortOrder('product', 'name', 'asc'))->toLegacyOrderWay()
        );
    }

    public function testToLegacyOrderWayDesc()
    {
        $this->assertEquals(
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
            [['entity' => 'product',
                'field' => 'name',
                'direction' => 'asc', ]],
        ];
    }

    /**
     * @dataProvider serialization_examples
     */
    public function testSerialization($data)
    {
        $opt = new SortOrder($data['entity'], $data['field'], $data['direction']);

        $encoded = $opt->toString();
        $this->assertIsString($encoded);

        $unserialized = SortOrder::newFromString($encoded);

        $arr = $unserialized->toArray();
        $this->assertEquals($data['entity'], $arr['entity']);
        $this->assertEquals($data['field'], $arr['field']);
        $this->assertEquals($data['direction'], $arr['direction']);
    }
}
