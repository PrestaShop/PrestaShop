<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\DataProvider;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractAssociatedProductQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\AttachmentProductQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\CategoryProductQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicator;
use PrestaShop\PrestaShop\Core\Search\Filters;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AssociatedProductQueryBuilderTest extends KernelTestCase
{
    private const LANG_ID = 1;
    private const SHOP_ID = 1;
    // Far above the IDs of the test data, so the rows inserted here are the only ones of this entity
    private const ENTITY_ID = 987654;
    private const PRODUCT_IDS = [1, 2, 3];

    private Connection $connection;
    private string $dbPrefix;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->connection = self::getContainer()->get('doctrine.dbal.default_connection');
        $this->dbPrefix = self::getContainer()->getParameter('database_prefix');

        foreach (self::PRODUCT_IDS as $productId) {
            $this->connection->insert($this->dbPrefix . 'product_attachment', [
                'id_product' => $productId,
                'id_attachment' => self::ENTITY_ID,
            ]);
            $this->connection->insert($this->dbPrefix . 'category_product', [
                'id_product' => $productId,
                'id_category' => self::ENTITY_ID,
                'position' => $productId,
            ]);
        }
    }

    protected function tearDown(): void
    {
        $this->connection->delete($this->dbPrefix . 'product_attachment', ['id_attachment' => self::ENTITY_ID]);
        $this->connection->delete($this->dbPrefix . 'category_product', ['id_category' => self::ENTITY_ID]);

        parent::tearDown();
    }

    public static function provideQueryBuilderClasses(): iterable
    {
        yield 'products of a file' => [AttachmentProductQueryBuilder::class];
        yield 'products of a category' => [CategoryProductQueryBuilder::class];
    }

    /**
     * @param class-string<AbstractAssociatedProductQueryBuilder> $queryBuilderClass
     */
    #[DataProvider('provideQueryBuilderClasses')]
    public function testListsOnlyTheProductsOfTheEntity(string $queryBuilderClass): void
    {
        $queryBuilder = $this->createQueryBuilder($queryBuilderClass, ShopConstraint::shop(self::SHOP_ID));

        $this->assertSame(self::PRODUCT_IDS, $this->fetchProductIds($queryBuilder, []));
        $this->assertSame(3, $this->countProducts($queryBuilder, []));

        $otherEntity = $this->createQueryBuilder($queryBuilderClass, ShopConstraint::shop(self::SHOP_ID), self::ENTITY_ID + 1);
        $this->assertSame([], $this->fetchProductIds($otherEntity, []));
    }

    /**
     * @param class-string<AbstractAssociatedProductQueryBuilder> $queryBuilderClass
     */
    #[DataProvider('provideQueryBuilderClasses')]
    public function testFilters(string $queryBuilderClass): void
    {
        $queryBuilder = $this->createQueryBuilder($queryBuilderClass, ShopConstraint::shop(self::SHOP_ID));
        $product = $this->connection->fetchAssociative(
            'SELECT p.reference, pl.name FROM ' . $this->dbPrefix . 'product p
             INNER JOIN ' . $this->dbPrefix . 'product_lang pl ON pl.id_product = p.id_product AND pl.id_lang = :lang AND pl.id_shop = :shop
             WHERE p.id_product = 2',
            ['lang' => self::LANG_ID, 'shop' => self::SHOP_ID]
        );

        // The ID matches exactly, not a part of it
        $this->assertSame([2], $this->fetchProductIds($queryBuilder, ['id_product' => '2']));
        $this->assertContains(2, $this->fetchProductIds($queryBuilder, ['name' => $product['name']]));
        $this->assertContains(2, $this->fetchProductIds($queryBuilder, ['reference' => $product['reference']]));
        $this->assertSame(1, $this->countProducts($queryBuilder, ['id_product' => '2']));

        // LIKE wildcards typed in a filter are searched literally
        $this->assertSame([], $this->fetchProductIds($queryBuilder, ['name' => '%']));

        $enabled = $this->fetchProductIds($queryBuilder, ['active' => '1']);
        $disabled = $this->fetchProductIds($queryBuilder, ['active' => '0']);
        $this->assertEqualsCanonicalizing(self::PRODUCT_IDS, array_merge($enabled, $disabled));
    }

    /**
     * @param class-string<AbstractAssociatedProductQueryBuilder> $queryBuilderClass
     */
    #[DataProvider('provideQueryBuilderClasses')]
    public function testAllShopsContextUsesTheDefaultShopOfEachProduct(string $queryBuilderClass): void
    {
        $singleShop = $this->createQueryBuilder($queryBuilderClass, ShopConstraint::shop(self::SHOP_ID));
        $allShops = $this->createQueryBuilder($queryBuilderClass, ShopConstraint::allShops());

        $this->assertSame($this->fetchRows($singleShop, []), $this->fetchRows($allShops, []));
    }

    /**
     * @param class-string<AbstractAssociatedProductQueryBuilder> $queryBuilderClass
     */
    private function createQueryBuilder(
        string $queryBuilderClass,
        ShopConstraint $shopConstraint,
        int $entityId = self::ENTITY_ID
    ): AbstractAssociatedProductQueryBuilder {
        $languageContext = $this->createMock(LanguageContext::class);
        $languageContext->method('getId')->willReturn(self::LANG_ID);
        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('getShopConstraint')->willReturn($shopConstraint);

        return new $queryBuilderClass(
            $this->connection,
            $this->dbPrefix,
            new DoctrineSearchCriteriaApplicator(),
            $languageContext,
            $shopContext,
            $entityId
        );
    }

    /**
     * @param array<string, string> $filters
     *
     * @return array<int, array<string, mixed>>
     */
    private function fetchRows(AbstractAssociatedProductQueryBuilder $queryBuilder, array $filters): array
    {
        return $queryBuilder
            ->getSearchQueryBuilder($this->createSearchCriteria($filters))
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /**
     * @param array<string, string> $filters
     *
     * @return int[]
     */
    private function fetchProductIds(AbstractAssociatedProductQueryBuilder $queryBuilder, array $filters): array
    {
        return array_map('intval', array_column($this->fetchRows($queryBuilder, $filters), 'id_product'));
    }

    /**
     * @param array<string, string> $filters
     */
    private function countProducts(AbstractAssociatedProductQueryBuilder $queryBuilder, array $filters): int
    {
        return (int) $queryBuilder
            ->getCountQueryBuilder($this->createSearchCriteria($filters))
            ->executeQuery()
            ->fetchOne();
    }

    /**
     * @param array<string, string> $filters
     */
    private function createSearchCriteria(array $filters): Filters
    {
        return new Filters([
            'limit' => 50,
            'offset' => 0,
            'orderBy' => 'id_product',
            'sortOrder' => 'asc',
            'filters' => $filters,
        ]);
    }
}
