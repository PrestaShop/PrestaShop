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

namespace Tests\Unit\Core\Search\Filters;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductCombinationFilters;

class ProductCombinationFiltersTest extends TestCase
{
    public function testConstruct(): void
    {
        $shopConstraint = ShopConstraint::shop(2);
        $filters = [
            'filters' => [
                'product_id' => 11,
            ],
            'limit' => 10,
            'offset' => null,
            'orderBy' => 'name',
            'sortOrder' => 'asc',
        ];

        $productCombinationFilters = new ProductCombinationFilters(
            $shopConstraint,
            $filters
        );

        $this->assertSame($shopConstraint, $productCombinationFilters->getShopConstraint());
        $this->assertSame(11, $productCombinationFilters->getProductId());
        $this->assertSame($filters['filters'], $productCombinationFilters->getFilters());
        $this->assertSame($filters['limit'], $productCombinationFilters->getLimit());
        $this->assertSame($filters['offset'], $productCombinationFilters->getOffset());
        $this->assertSame($filters['orderBy'], $productCombinationFilters->getOrderBy());
        $this->assertSame($filters['sortOrder'], $productCombinationFilters->getOrderWay());
        $this->assertSame('product_combinations_11', $productCombinationFilters->getFilterId());
    }

    public function testGenerateFilterId(): void
    {
        $this->assertSame(
            'product_combinations_77',
            ProductCombinationFilters::generateFilterId(77)
        );
    }
}
