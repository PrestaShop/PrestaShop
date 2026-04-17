<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Product\Search;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Product\Search\Filter;

class FilterTest extends TestCase
{
    /**
     * @var Filter|null
     */
    private $filter;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->filter = new Filter();
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown(): void
    {
        $this->filter = null;
    }

    public function testCreateANewFilter()
    {
        $this->assertInstanceOf(Filter::class, $this->filter);

        // Filter public integrity of data types.
        $this->assertIsString($this->filter->getLabel());
        $this->assertIsString($this->filter->getType());
        $this->assertIsBool($this->filter->isDisplayed());
        $this->assertIsBool($this->filter->isActive());
        $this->assertIsArray($this->filter->getNextEncodedFacets());
        $this->assertIsArray($this->filter->toArray());
        $this->assertIsInt($this->filter->getMagnitude());

        // Facet public integrity of default Facet data
        $this->assertEmpty($this->filter->getLabel());
        $this->assertEmpty($this->filter->getType());
        $this->assertEmpty($this->filter->getNextEncodedFacets());
        $this->assertSame(0, $this->filter->getMagnitude());
        $this->assertTrue($this->filter->isDisplayed());
        $this->assertFalse($this->filter->isActive());
        $this->assertSame([
            'label' => '',
            'type' => '',
            'active' => false,
            'displayed' => true,
            'properties' => [],
            'magnitude' => 0,
            'value' => null,
            'nextEncodedFacets' => [],
        ],
            $this->filter->toArray()
        );
    }

    public function testGetterAndSetterForLabel()
    {
        $this->assertSame('', $this->filter->getLabel());

        $this->assertInstanceOf(Filter::class, $this->filter->setLabel('Weight'));
        $this->assertSame('Weight', $this->filter->getLabel());
    }

    public function testGetterAndSetterForMagnitude()
    {
        $this->assertSame(0, $this->filter->getMagnitude());

        $this->assertInstanceOf(Filter::class, $this->filter->setMagnitude(10));
        $this->assertSame(10, $this->filter->getMagnitude());
    }

    public function testGetterAndSetterForType()
    {
        $this->assertSame('', $this->filter->getType());

        $this->assertInstanceOf(Filter::class, $this->filter->setType('weight'));
        $this->assertSame('weight', $this->filter->getType());
    }

    public function testGetterAndIsserForDisplayed()
    {
        $this->assertTrue($this->filter->isDisplayed());

        $this->assertInstanceOf(Filter::class, $this->filter->setDisplayed(false));
        $this->assertFalse($this->filter->isDisplayed());
    }

    public function testGetterAndIsserForActive()
    {
        $this->assertFalse($this->filter->isActive());

        $this->assertInstanceOf(Filter::class, $this->filter->setActive(false));
        $this->assertFalse($this->filter->isActive());
    }

    public function testGetterAndIsserForValue()
    {
        $this->assertNull($this->filter->getValue());

        $this->assertInstanceOf(Filter::class, $this->filter->setValue('blue'));
        $this->assertSame('blue', $this->filter->getValue());
    }

    public function testGetterAndSetterForProperties()
    {
        $this->assertNull($this->filter->getProperty('product_name'));
        $this->assertInstanceOf(Filter::class, $this->filter->setProperty('product_name', 'Nice cupcake'));
        $this->assertSame('Nice cupcake', $this->filter->getProperty('product_name'));
    }
}
