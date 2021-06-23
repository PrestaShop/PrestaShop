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

namespace Tests\Unit\Core\Search\Builder\TypedBuilder;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Search\Builder\TypedBuilder\ProductCombinationFiltersBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductCombinationFilters;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class ProductCombinationBuilderTest extends TestCase
{
    /**
     * @dataProvider getSupportsValues
     *
     * @param string $filtersClass
     * @param bool $expectedSupport
     */
    public function testSupports(string $filtersClass, bool $expectedSupport)
    {
        $builder = new ProductCombinationFiltersBuilder();
        $isSupported = $builder->supports($filtersClass);
        $this->assertEquals($expectedSupport, $isSupported);
    }

    public function testBuildFilters()
    {
        $productId = 42;
        $builder = new ProductCombinationFiltersBuilder();
        $builder->setConfig(['request' => $this->buildRequestMock($productId)]);

        $builtFilters = $builder->buildFilters();
        $this->assertEquals(ProductCombinationFilters::generateFilterId($productId), $builtFilters->getFilterId());
        $filters = $builtFilters->getFilters();
        $this->assertEquals($productId, $filters['product_id']);
    }

    public function testBuildFiltersWithInitialValues()
    {
        $productId = 42;
        $builder = new ProductCombinationFiltersBuilder();
        $builder->setConfig(['request' => $this->buildRequestMock($productId)]);

        $initialFilters = new Filters([
            'filters' => [
                'product_id' => 51,
                'category_id' => 45,
            ],
        ], 'product_id');

        $builtFilters = $builder->buildFilters($initialFilters);
        $this->assertEquals(ProductCombinationFilters::generateFilterId($productId), $builtFilters->getFilterId());
        $filters = $builtFilters->getFilters();
        $this->assertEquals($productId, $filters['product_id']);
        $this->assertEquals(45, $filters['category_id']);
    }

    public function getSupportsValues(): Generator
    {
        yield [
            ProductCombinationFilters::class,
            true,
        ];

        yield [
            '',
            false,
        ];

        yield [
            Filters::class,
            false,
        ];
    }

    /**
     * @param int $productId
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|Request
     */
    private function buildRequestMock(int $productId)
    {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $parameterBagMock = $this->getMockBuilder(ParameterBag::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $parameterBagMock
            ->expects($this->once())
            ->method('get')
            ->willReturn($productId)
        ;

        $requestMock->attributes = $parameterBagMock;

        return $requestMock;
    }
}
