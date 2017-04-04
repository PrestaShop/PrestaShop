<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace PrestaShop\PrestaShop\tests\Unit\Core\Product\Search;

use PHPUnit_Framework_TestCase;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

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

    /**
     * dataProvider for test_serialization
     */
    public function serialization_examples()
    {
        return [
            [['entity'    => 'product',
            'field'      => 'name',
            'direction'  => 'asc']]
        ];
    }

    /**
     * @dataProvider serialization_examples
     */
    public function test_serialization($data)
    {
        $opt = new SortOrder($data['entity'], $data['field'], $data['direction']);

        $encoded = $opt->toString();
        $this->assertInternalType('string', $encoded);

        $unserialized = SortOrder::newFromString($encoded);

        $arr = $unserialized->toArray();
        $this->assertEquals($data['entity'],    $arr['entity']);
        $this->assertEquals($data['field'],     $arr['field']);
        $this->assertEquals($data['direction'], $arr['direction']);
    }
}
