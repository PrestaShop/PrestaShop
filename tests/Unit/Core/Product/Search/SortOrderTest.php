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

namespace Tests\Unit\Core\Product\Search;

use Exception;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

/**
 * @doc ./vendor/bin/phpunit -c tests/phpunit.xml --filter="SortOrderTest"
 */
class SortOrderTest extends TestCase
{
    public function testCreateANewSortOrder()
    {
        $sortOrder = new SortOrder('foo', 'bar');
        $this->assertInstanceOf(SortOrder::class, $sortOrder);

        // SortOrder public integrity of data types.
        $this->assertInternalType('string', $sortOrder->getLabel());
        $this->assertInternalType('string', $sortOrder->getEntity());
        $this->assertInternalType('string', $sortOrder->getField());
        $this->assertInternalType('string', $sortOrder->getDirection());
        $this->assertInternalType('string', $sortOrder->toLegacyOrderWay());
        $this->assertInternalType('string', $sortOrder->toLegacyOrderBy());
        $this->assertInternalType('string', $sortOrder->toString());
        $this->assertInternalType('bool', $sortOrder->isRandom());
        $this->assertInternalType('array', $sortOrder->toArray());

        // SortOrder public integrity of default SortOrder data
        $this->assertSame('', $sortOrder->getLabel());
        $this->assertSame('foo', $sortOrder->getEntity());
        $this->assertSame('bar', $sortOrder->getField());
        $this->assertSame('asc', $sortOrder->getDirection());
        $this->assertFalse($sortOrder->isRandom());
        $this->assertSame('foo.bar.asc', $sortOrder->toString());
        $this->assertSame(
            [
                'entity' => 'foo',
                'field' => 'bar',
                'direction' => 'asc',
                'label' => '',
                'urlParameter' => 'foo.bar.asc',
            ],
            $sortOrder->toArray()
        );
        $this->assertSame('asc', $sortOrder->toLegacyOrderWay());
    }

    public function testCreateANewSortOrderWithInvalidDirection()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid SortOrder direction `baz`. Expecting one of: `ASC`, `DESC`, or `RANDOM`.');
        $this->assertInstanceOf(SortOrder::class, new SortOrder('foo', 'bar', 'baz'));
    }

    public function testCreateNewRandomSortOrder()
    {
        $sortOrder = SortOrder::random();
        $this->assertInstanceOf(SortOrder::class, $sortOrder);
        $this->assertSame('random', $sortOrder->getDirection());
        $this->assertTrue($sortOrder->isRandom());
    }

    public function testCreateNewSortOrderFromString()
    {
        $sortOrder = SortOrder::newFromString('foo.bar.desc');
        $this->assertInstanceOf(SortOrder::class, $sortOrder);
        $this->assertSame('desc', $sortOrder->getDirection());
        $this->assertSame('foo', $sortOrder->getEntity());
        $this->assertSame('bar', $sortOrder->getField());
        $this->assertFalse($sortOrder->isRandom());
    }

    public function testCreateNewSortOrderFromInvalidString()
    {
        $this->expectException(Exception::class);
        $sortOrder = SortOrder::newFromString('invalid.string');
    }

    public function testGetterAndSetterLabel()
    {
        $sortOrder = new SortOrder('product', 'name');
        $this->assertInstanceOf(SortOrder::class, $sortOrder->setLabel('Product name'));
        $this->assertSame('Product name', $sortOrder->getLabel());
    }

    public function testGetterAndSetterEntity()
    {
        $sortOrder = new SortOrder('product', 'name');
        $this->assertInstanceOf(SortOrder::class, $sortOrder->setEntity('category'));
        $this->assertSame('category', $sortOrder->getEntity());
    }

    public function testGetterAndSetterField()
    {
        $sortOrder = new SortOrder('product', 'name');
        $this->assertInstanceOf(SortOrder::class, $sortOrder->setField('description'));
        $this->assertSame('description', $sortOrder->getField());
    }

    public function testGetterAndSetterDirection()
    {
        $sortOrder = new SortOrder('product', 'name');
        $this->assertSame('desc', $sortOrder->setDirection('desc'));
        $this->assertSame('desc', $sortOrder->getDirection());
    }

    public function testToLegacyOrderByProductNameWorksAsExpected()
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

    public function testToLegacyOrderByProductPriceWorksAsExpected()
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

    public function testToLegacyOrderByProductPositionWorksAsExpected()
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

    public function testToLegacyOrderByManufacturerNameWorksAsExpected()
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

    public function testToLegacyOrderWayAscWorksAsExpected()
    {
        $this->assertEquals(
            'asc',
            (new SortOrder('product', 'name', 'asc'))->toLegacyOrderWay()
        );
    }

    public function testToLegacyOrderWayDescWorksAsExpected()
    {
        $this->assertEquals(
            'desc',
            (new SortOrder('product', 'name', 'desc'))->toLegacyOrderWay()
        );
    }

    /**
     * dataProvider for test_serialization
     */
    public function getSerializationExamples()
    {
        return [
            [['entity'    => 'product',
            'field'      => 'name',
            'direction'  => 'asc']]
        ];
    }

    /**
     * @dataProvider getSerializationExamples
     */
    public function testSerializationWorksAsExpected($data)
    {
        $sortOrder = new SortOrder($data['entity'], $data['field'], $data['direction']);

        $serializedSortOrder = $sortOrder->toString();
        $this->assertInternalType('string', $serializedSortOrder);

        $unserializedSortOrder = SortOrder::newFromString($serializedSortOrder);

        $sortOrderArray = $unserializedSortOrder->toArray();
        $this->assertEquals($data['entity'], $sortOrderArray['entity']);
        $this->assertEquals($data['field'],  $sortOrderArray['field']);
        $this->assertEquals($data['direction'], $sortOrderArray['direction']);
    }
}
