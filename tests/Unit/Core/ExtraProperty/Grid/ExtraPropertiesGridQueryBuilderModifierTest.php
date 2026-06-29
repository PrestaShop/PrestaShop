<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Grid;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionCollection;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Grid\ExtraPropertiesGridQueryBuilderModifier;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Covers the JOIN cardinality invariant of ExtraPropertiesGridQueryBuilderModifier:
 * every extra join must cover the FULL primary key of its extra table (1:1 enrichment,
 * never row multiplication — no GROUP BY allowed), and joins/parameters must be built
 * per builder since search and count query shapes differ.
 */
class ExtraPropertiesGridQueryBuilderModifierTest extends TestCase
{
    private const CONTEXT_LANG_ID = 2;
    private const CONTEXT_SHOP_ID = 3;

    public function testCommonScopeJoinsOnPrimaryKeyOnly(): void
    {
        $modifier = $this->buildModifier($this->definition('note', ExtraPropertyScope::COMMON));
        [$searchQb, $countQb] = $this->buildGridBuilders();

        $modifier->apply($searchQb, $countQb, $this->criteria(), 'product');

        foreach ([$searchQb, $countQb] as $qb) {
            $this->assertStringContainsString(
                'LEFT JOIN ps_product_extra extra_entity ON extra_entity.`id_product` = p.`id_product`',
                $qb->getSQL()
            );
        }
        $this->assertSame([], $searchQb->getParameters());
        $this->assertSame([], $countQb->getParameters());
    }

    public function testLangJoinReusesBaseLangJoinAndPinsShopFromIt(): void
    {
        $modifier = $this->buildModifier($this->definition('promo', ExtraPropertyScope::LANG));
        [$searchQb, $countQb] = $this->buildGridBuilders();
        foreach ([$searchQb, $countQb] as $qb) {
            $qb->leftJoin('p', 'ps_product_lang', 'pl', 'pl.`id_product` = p.`id_product` AND pl.`id_lang` = :langId AND pl.`id_shop` = :shopId');
        }

        $modifier->apply($searchQb, $countQb, $this->criteria(), 'product');

        foreach ([$searchQb, $countQb] as $qb) {
            $sql = $qb->getSQL();
            $this->assertStringContainsString('extra_lang.`id_product` = pl.`id_product`', $sql);
            $this->assertStringContainsString('extra_lang.`id_lang` = pl.`id_lang`', $sql);
            $this->assertStringContainsString('extra_lang.`id_shop` = pl.`id_shop`', $sql);
        }
    }

    public function testLangJoinPinsShopFromParamWhenBaseLangJoinHasNoShop(): void
    {
        $modifier = $this->buildModifier($this->definition('promo', ExtraPropertyScope::LANG));
        [$searchQb, $countQb] = $this->buildGridBuilders();
        foreach ([$searchQb, $countQb] as $qb) {
            $qb->leftJoin('p', 'ps_product_lang', 'pl', 'pl.`id_product` = p.`id_product` AND pl.`id_lang` = :langId');
        }

        $modifier->apply($searchQb, $countQb, $this->criteria(), 'product');

        foreach ([$searchQb, $countQb] as $qb) {
            $sql = $qb->getSQL();
            $this->assertStringContainsString('extra_lang.`id_lang` = pl.`id_lang`', $sql);
            // The PK of {e}_extra_lang includes id_shop: it must be pinned even when the
            // base lang join carries no shop clause, or multistore data duplicates rows.
            $this->assertStringContainsString('extra_lang.`id_shop` = :extraLangShopId', $sql);
            $this->assertSame(self::CONTEXT_SHOP_ID, $qb->getParameters()['extraLangShopId']);
        }
    }

    public function testLangFallbackPinsLangAndShopOnBothBuilders(): void
    {
        $modifier = $this->buildModifier($this->definition('promo', ExtraPropertyScope::LANG));
        [$searchQb, $countQb] = $this->buildGridBuilders();

        $modifier->apply($searchQb, $countQb, $this->criteria(), 'product');

        foreach ([$searchQb, $countQb] as $qb) {
            $sql = $qb->getSQL();
            $this->assertStringContainsString('extra_lang.`id_product` = p.`id_product`', $sql);
            $this->assertStringContainsString('extra_lang.`id_lang` = :extraLangId', $sql);
            $this->assertStringContainsString('extra_lang.`id_shop` = :extraLangShopId', $sql);
            $this->assertSame(self::CONTEXT_LANG_ID, $qb->getParameters()['extraLangId']);
            $this->assertSame(self::CONTEXT_SHOP_ID, $qb->getParameters()['extraLangShopId']);
        }
    }

    public function testCountBuilderWithoutBaseLangJoinGetsItsOwnFallbackJoin(): void
    {
        $modifier = $this->buildModifier($this->definition('promo', ExtraPropertyScope::LANG));
        [$searchQb, $countQb] = $this->buildGridBuilders();
        // Only the search builder carries the base lang join — the typical grid shape.
        $searchQb->leftJoin('p', 'ps_product_lang', 'pl', 'pl.`id_product` = p.`id_product` AND pl.`id_lang` = :langId AND pl.`id_shop` = :shopId');

        $modifier->apply($searchQb, $countQb, $this->criteria(['extra_mymodule_promo' => 'sale']), 'product');

        // Search reuses its base lang join aliases.
        $this->assertStringContainsString('extra_lang.`id_lang` = pl.`id_lang`', $searchQb->getSQL());
        // Count has no `pl` alias: it must get a param-based join from its own main alias —
        // getSQL() also proves every referenced alias exists in the count query.
        $countSql = $countQb->getSQL();
        $this->assertStringContainsString('LEFT JOIN ps_product_extra_lang extra_lang ON extra_lang.`id_product` = p.`id_product`', $countSql);
        $this->assertStringContainsString('extra_lang.`id_lang` = :extraLangId', $countSql);
        // The filter applies to both builders so the count matches the page.
        $this->assertStringContainsString('extra_lang.`mymodule_promo` LIKE', $searchQb->getSQL());
        $this->assertStringContainsString('extra_lang.`mymodule_promo` LIKE', $countSql);
    }

    public function testShopJoinReusesBaseShopJoin(): void
    {
        $modifier = $this->buildModifier($this->definition('flag', ExtraPropertyScope::SHOP));
        [$searchQb, $countQb] = $this->buildGridBuilders();
        foreach ([$searchQb, $countQb] as $qb) {
            $qb->leftJoin('p', 'ps_product_shop', 'psh', 'psh.`id_product` = p.`id_product` AND psh.`id_shop` = :shopId');
        }

        $modifier->apply($searchQb, $countQb, $this->criteria(), 'product');

        foreach ([$searchQb, $countQb] as $qb) {
            $sql = $qb->getSQL();
            $this->assertStringContainsString('extra_shop.`id_product` = psh.`id_product`', $sql);
            $this->assertStringContainsString('extra_shop.`id_shop` = psh.`id_shop`', $sql);
        }
    }

    public function testShopFallbackPrefersBuilderShopIdParam(): void
    {
        $modifier = $this->buildModifier($this->definition('flag', ExtraPropertyScope::SHOP));
        [$searchQb, $countQb] = $this->buildGridBuilders();
        $searchQb->setParameter('shopId', 7);

        $modifier->apply($searchQb, $countQb, $this->criteria(), 'product');

        $this->assertStringContainsString('extra_shop.`id_shop` = :extraShopId', $searchQb->getSQL());
        // Search builder pins on its own :shopId value; the count builder has no such
        // param and falls back to the context shop — each builder is self-contained.
        $this->assertSame(7, $searchQb->getParameters()['extraShopId']);
        $this->assertSame(self::CONTEXT_SHOP_ID, $countQb->getParameters()['extraShopId']);
    }

    public function testBooleanFilterUsesEqualityOnBothBuilders(): void
    {
        $definition = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'flag',
            scope: ExtraPropertyScope::COMMON,
            moduleName: 'mymodule',
            associatedGrids: ['product'],
            formFieldType: \Symfony\Component\Form\Extension\Core\Type\CheckboxType::class,
            labelWording: 'Flag',
        );
        $modifier = $this->buildModifier($definition);
        [$searchQb, $countQb] = $this->buildGridBuilders();

        $modifier->apply($searchQb, $countQb, $this->criteria(['extra_mymodule_flag' => '1']), 'product');

        foreach ([$searchQb, $countQb] as $qb) {
            $this->assertStringContainsString('extra_entity.`mymodule_flag` = :extra_filter_', $qb->getSQL());
        }
    }

    public function testNothingHappensWhenCountBuilderHasNoMainTable(): void
    {
        $modifier = $this->buildModifier($this->definition('note', ExtraPropertyScope::COMMON));
        [$searchQb] = $this->buildGridBuilders();
        $countQb = $this->createQueryBuilder();
        $countQb->select('COUNT(*)')->from('ps_other_table', 'o');

        $modifier->apply($searchQb, $countQb, $this->criteria(), 'product');

        // The scope is skipped for BOTH builders: joining/filtering only the search
        // builder would desynchronize the count from the page.
        $this->assertStringNotContainsString('extra_entity', $searchQb->getSQL());
        $this->assertStringNotContainsString('extra_entity', $countQb->getSQL());
    }

    // -------------------------------------------------------------------------
    // Fixtures
    // -------------------------------------------------------------------------

    private function buildModifier(ExtraPropertyDefinition ...$definitions): ExtraPropertiesGridQueryBuilderModifier
    {
        $repository = $this->createMock(ExtraPropertyDefinitionRepositoryInterface::class);
        $repository->method('getAllDefinitions')->willReturn(new ExtraPropertyDefinitionCollection($definitions));

        $languageContext = $this->createMock(LanguageContext::class);
        $languageContext->method('getId')->willReturn(self::CONTEXT_LANG_ID);

        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('getShopConstraint')->willReturn(ShopConstraint::shop(self::CONTEXT_SHOP_ID));

        return new ExtraPropertiesGridQueryBuilderModifier($repository, 'ps_', $languageContext, $shopContext);
    }

    /**
     * @return array{0: QueryBuilder, 1: QueryBuilder} [searchQb, countQb] over ps_product
     */
    private function buildGridBuilders(): array
    {
        $searchQb = $this->createQueryBuilder();
        $searchQb->select('p.id_product')->from('ps_product', 'p');

        $countQb = $this->createQueryBuilder();
        $countQb->select('COUNT(*)')->from('ps_product', 'p');

        return [$searchQb, $countQb];
    }

    private function createQueryBuilder(): QueryBuilder
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('getDatabasePlatform')->willReturn(new MySQLPlatform());

        return new QueryBuilder($connection);
    }

    /**
     * @param array<string, mixed> $filters
     */
    private function criteria(array $filters = []): SearchCriteriaInterface
    {
        $criteria = $this->createMock(SearchCriteriaInterface::class);
        $criteria->method('getFilters')->willReturn($filters);

        return $criteria;
    }

    private function definition(string $propertyName, ExtraPropertyScope $scope): ExtraPropertyDefinition
    {
        return new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: $propertyName,
            scope: $scope,
            moduleName: 'mymodule',
            associatedGrids: ['product'],
            labelWording: 'Label',
        );
    }
}
