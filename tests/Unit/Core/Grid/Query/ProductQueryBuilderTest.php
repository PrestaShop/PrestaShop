<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopGroupRepository;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\Filter\DoctrineFilterApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\ProductQueryBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductFilters;
use ShopGroup;

class ProductQueryBuilderTest extends TestCase
{
    private const DB_PREFIX = 'ps_';

    /**
     * @see https://github.com/PrestaShop/PrestaShop/issues/36539
     *
     * The product grid must read the tax rules group from product_shop (ps), which is
     * shop-specific, and not from product (p), which is shop-agnostic. Selecting the
     * product-level column overrides the shop-specific one in the result set, so the
     * listing displays the wrong tax/price when shops use different tax rules.
     */
    public function testSearchQuerySelectsShopSpecificTaxRulesGroup(): void
    {
        $builder = new ProductQueryBuilder(
            $this->getMockConnection(),
            self::DB_PREFIX,
            $this->getMockSearchCriteriaApplicator(),
            1,
            $this->createMock(DoctrineFilterApplicatorInterface::class),
            $this->getMockConfiguration(),
            $this->getMockShopGroupRepository()
        );

        $filters = new ProductFilters(ShopConstraint::shop(2), ProductFilters::getDefaults());
        $select = $builder->getSearchQueryBuilder($filters)->getQueryParts()['select'];

        $this->assertContains(
            'ps.`price` AS `price_tax_excluded`, ps.`ecotax` AS `ecotax_tax_excluded`, ps.`id_tax_rules_group`, ps.`active`',
            $select,
            'The shop-specific tax rules group (product_shop) must be selected.'
        );
        $this->assertNotContains(
            'p.`id_tax_rules_group`',
            $select,
            'The shop-agnostic product-level tax rules group must not be selected, it overrides the shop value.'
        );
    }

    private function getMockConnection(): Connection
    {
        $mock = $this->createMock(Connection::class);
        $mock->method('createQueryBuilder')->willReturn($this->createPartialMock(QueryBuilder::class, []));

        return $mock;
    }

    private function getMockSearchCriteriaApplicator(): DoctrineSearchCriteriaApplicatorInterface
    {
        $mock = $this->createMock(DoctrineSearchCriteriaApplicatorInterface::class);
        $mock->method('applyPagination')->willReturnSelf();
        $mock->method('applySorting')->willReturnSelf();

        return $mock;
    }

    private function getMockConfiguration(): Configuration
    {
        $mock = $this->createMock(Configuration::class);
        $mock->method('getBoolean')->willReturn(false);

        return $mock;
    }

    private function getMockShopGroupRepository(): ShopGroupRepository
    {
        $mock = $this->createMock(ShopGroupRepository::class);
        $mock->method('getByShop')->willReturn($this->createMock(ShopGroup::class));

        return $mock;
    }
}
