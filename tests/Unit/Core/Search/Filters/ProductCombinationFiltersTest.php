<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
