<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopGroupRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\Filter\DoctrineFilterApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\ProductQueryBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductFilters;

final class ProductQueryBuilderTest extends TestCase
{
    public function testProductTypeFilterRestrictsProductsToTheSelectedType(): void
    {
        $filters = new ProductFilters(
            ShopConstraint::allShops(),
            ['filters' => ['product_type' => ProductType::TYPE_PACK]] + ProductFilters::getDefaults()
        );

        foreach ([
            $this->getQueryBuilder()->getSearchQueryBuilder($filters),
            $this->getQueryBuilder()->getCountQueryBuilder($filters),
        ] as $qb) {
            $this->assertStringContainsString('p.`product_type` = :productType', $qb->getSQL());
            $this->assertSame(ProductType::TYPE_PACK, $qb->getParameter('productType'));
        }
    }

    public function testNoProductTypeConditionWithoutFilter(): void
    {
        $qb = $this->getQueryBuilder()->getSearchQueryBuilder(
            new ProductFilters(ShopConstraint::allShops(), ProductFilters::getDefaults())
        );

        $this->assertStringNotContainsString('product_type` =', $qb->getSQL());
        $this->assertArrayNotHasKey('productType', $qb->getParameters());
    }

    private function getQueryBuilder(): ProductQueryBuilder
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('getDatabasePlatform')->willReturn(new MySQLPlatform());
        $connection->method('createQueryBuilder')->willReturnCallback(
            fn () => new QueryBuilder($connection)
        );

        return new ProductQueryBuilder(
            $connection,
            'ps_',
            $this->createMock(DoctrineSearchCriteriaApplicatorInterface::class),
            1,
            $this->createMock(DoctrineFilterApplicatorInterface::class),
            $this->createMock(Configuration::class),
            $this->createMock(ShopGroupRepository::class)
        );
    }
}
