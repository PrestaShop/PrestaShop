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

namespace Tests\Unit\Core\Product\Search;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Product\Search\Facet;
use PrestaShop\PrestaShop\Core\Product\Search\Filter;

class FacetTest extends TestCase
{
    /**
     * @var Facet|null
     */
    private $facet;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->facet = new Facet();
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown(): void
    {
        $this->facet = null;
    }

    public function testCreateANewFacet()
    {
        $this->assertInstanceOf(Facet::class, $this->facet);

        // Facet public integrity of data types.
        $this->assertIsString($this->facet->getLabel());
        $this->assertIsString($this->facet->getWidgetType());
        $this->assertIsString($this->facet->getType());
        $this->assertIsBool($this->facet->isDisplayed());
        $this->assertIsBool($this->facet->isMultipleSelectionAllowed());
        $this->assertIsArray($this->facet->getFilters());
        $this->assertIsArray($this->facet->toArray());

        // Facet public integrity of default Facet data
        $this->assertEmpty($this->facet->getLabel());
        $this->assertEmpty($this->facet->getType());
        $this->assertEmpty($this->facet->getFilters());
        $this->assertSame('radio', $this->facet->getWidgetType());
        $this->assertTrue($this->facet->isDisplayed());
        $this->assertTrue($this->facet->isMultipleSelectionAllowed());
        $this->assertSame([
            'label' => '',
            'displayed' => true,
            'type' => '',
            'properties' => [],
            'filters' => [],
            'multipleSelectionAllowed' => true,
            'widgetType' => 'radio',
        ],
            $this->facet->toArray()
        );
    }

    public function testGetterAndSetterForLabel()
    {
        $this->assertSame('', $this->facet->getLabel());

        $this->assertInstanceOf(Facet::class, $this->facet->setLabel('Weight'));
        $this->assertSame('Weight', $this->facet->getLabel());
    }

    public function testGetterAndSetterForType()
    {
        $this->assertSame('', $this->facet->getType());

        $this->assertInstanceOf(Facet::class, $this->facet->setType('weight'));
        $this->assertSame('weight', $this->facet->getType());
    }

    public function testGetterAndSetterForWidgetType()
    {
        $this->assertSame('radio', $this->facet->getWidgetType());

        $this->assertInstanceOf(Facet::class, $this->facet->setWidgetType('dropdown'));
        $this->assertSame('dropdown', $this->facet->getWidgetType());
    }

    public function testGetterAndIsserForDisplayed()
    {
        $this->assertTrue($this->facet->isDisplayed());

        $this->assertInstanceOf(Facet::class, $this->facet->setDisplayed(false));
        $this->assertFalse($this->facet->isDisplayed());
    }

    public function testGetterAndIsserForMultipleSelectionAllowed()
    {
        $this->assertTrue($this->facet->isMultipleSelectionAllowed());
        $this->assertInstanceOf(Facet::class, $this->facet->setMultipleSelectionAllowed());
        $this->assertTrue($this->facet->isMultipleSelectionAllowed());

        $this->facet->setMultipleSelectionAllowed(false);
        $this->assertFalse($this->facet->isMultipleSelectionAllowed());
    }

    public function testGetterAndAdderForFilters()
    {
        $this->assertSame([], $this->facet->getFilters());
        $filterMock = $this->createMock(Filter::class);
        $this->assertInstanceOf(Facet::class, $this->facet->addFilter($filterMock));
        $this->assertSame([$filterMock], $this->facet->getFilters());
    }

    public function testGetterAndSetterForProperties()
    {
        $this->assertNull($this->facet->getProperty('product_name'));
        $this->assertInstanceOf(Facet::class, $this->facet->setProperty('product_name', 'Nice cupcake'));
        $this->assertSame('Nice cupcake', $this->facet->getProperty('product_name'));
    }
}
