<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Product\Search;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Product\Search\Filter;

/**
 * @doc ./vendor/bin/phpunit -c tests/phpunit.xml --filter="FilterTest"
 */
class FilterTest extends TestCase
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->filter = new Filter();
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->filter = null;
    }

    public function testCreateANewFilter()
    {
        $this->assertInstanceOf(Filter::class, $this->filter);

        // Filter public integrity of data types.
        $this->assertInternalType('string', $this->filter->getLabel());
        $this->assertInternalType('string', $this->filter->getType());
        $this->assertInternalType('bool', $this->filter->isDisplayed());
        $this->assertInternalType('bool', $this->filter->isActive());
        $this->assertInternalType('array', $this->filter->getNextEncodedFacets());
        $this->assertInternalType('array', $this->filter->toArray());
        $this->assertInternalType('integer', $this->filter->getMagnitude());

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
