<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
