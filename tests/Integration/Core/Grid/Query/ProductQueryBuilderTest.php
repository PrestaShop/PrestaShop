<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Grid\Query;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Grid\Query\ProductQueryBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductFilters;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The products grid resolves its page of ids before joining the display tables, so that the database
 * can walk the product primary key and stop at the page size instead of joining every matching product
 * through seven tables and sorting the result.
 *
 * These assert the shape rather than a duration: a timing test would be flaky, and reverting the change
 * leaves a behaviour test green by design, since the rows returned are the same either way. What breaks
 * if the optimisation is undone is that the paginating query stops being narrow.
 */
class ProductQueryBuilderTest extends KernelTestCase
{
    private const DISPLAY_ONLY_TABLES = ['image_shop', 'image_lang', 'shop'];

    private ProductQueryBuilder $queryBuilder;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->queryBuilder = self::getContainer()->get('prestashop.core.grid.query_builder.product');
    }

    public function testThePaginatingQueryDoesNotJoinTheDisplayOnlyTables(): void
    {
        $sql = $this->searchSql([]);

        $paginating = $this->paginatingSubQueryOf($sql);
        foreach (self::DISPLAY_ONLY_TABLES as $table) {
            $this->assertStringNotContainsString(
                'JOIN ' . _DB_PREFIX_ . $table . ' ',
                $paginating,
                sprintf('%s carries no filter and no sort, so joining it before the LIMIT is pure cost', $table)
            );
        }

        // the control: the display query still fetches those columns, they are only fetched later
        $this->assertStringContainsString(_DB_PREFIX_ . 'image_shop', $sql);
        $this->assertStringContainsString(_DB_PREFIX_ . 'image_lang', $sql);
    }

    public function testThePaginatingQueryIsLimitedAndTheDisplayQueryIsNot(): void
    {
        $sql = $this->searchSql([]);

        $this->assertStringContainsString('LIMIT', $this->paginatingSubQueryOf($sql));
        $this->assertSame(
            1,
            substr_count($sql, 'LIMIT'),
            'the outer query hydrates exactly the page the sub-query selected, so it must not limit again'
        );
    }

    /**
     * A column can only be filtered or sorted if its table is part of the paginating query, or the page
     * would be selected from the wrong set.
     *
     * @dataProvider provideColumnsThatNeedTheirTable
     */
    public function testAFilteredOrSortedColumnBringsItsTableIntoThePaginatingQuery(array $overrides, string $table): void
    {
        $paginating = $this->paginatingSubQueryOf($this->searchSql($overrides));

        $this->assertStringContainsString(_DB_PREFIX_ . $table, $paginating);
    }

    public function provideColumnsThatNeedTheirTable(): iterable
    {
        yield 'sorted by name' => [['orderBy' => 'name', 'sortOrder' => 'asc'], 'product_lang'];
        yield 'sorted by category' => [['orderBy' => 'category', 'sortOrder' => 'asc'], 'category_lang'];
        yield 'filtered by name' => [['filters' => ['name' => 'mug']], 'product_lang'];
        yield 'filtered by category' => [['filters' => ['category' => 'home']], 'category_lang'];
        yield 'filtered by category id' => [['filters' => ['id_category' => 2]], 'category_product'];
    }

    public function testTheCountDoesNotJoinTablesThatCannotChangeIt(): void
    {
        $filters = new ProductFilters(ShopConstraint::shop(1), ProductFilters::getDefaults());
        $sql = $this->queryBuilder->getCountQueryBuilder($filters)->getSQL();

        foreach (self::DISPLAY_ONLY_TABLES as $table) {
            $this->assertStringNotContainsString('JOIN ' . _DB_PREFIX_ . $table . ' ', $sql);
        }
        // every one of those is a single-row lookup on a key, so the count is unchanged without them
        $this->assertStringContainsString('COUNT(', $sql);
    }

    /**
     * Ordering cannot change how many rows match, so the sorted column's table has no business in the
     * count query.
     */
    public function testSortingDoesNotAddATableToTheCount(): void
    {
        $filters = new ProductFilters(ShopConstraint::shop(1), array_merge(
            ProductFilters::getDefaults(),
            ['orderBy' => 'name', 'sortOrder' => 'asc']
        ));

        $this->assertStringNotContainsString(
            _DB_PREFIX_ . 'product_lang',
            $this->queryBuilder->getCountQueryBuilder($filters)->getSQL()
        );
    }

    private function searchSql(array $overrides): string
    {
        $filters = new ProductFilters(
            ShopConstraint::shop(1),
            array_merge(ProductFilters::getDefaults(), $overrides)
        );

        return $this->queryBuilder->getSearchQueryBuilder($filters)->getSQL();
    }

    private function paginatingSubQueryOf(string $sql): string
    {
        $start = strpos($sql, 'INNER JOIN (SELECT');
        $this->assertNotFalse($start, 'the search query no longer resolves its page of ids first');
        $end = strpos($sql, ') paginated', $start);
        $this->assertNotFalse($end);

        return substr($sql, $start, $end - $start);
    }
}
