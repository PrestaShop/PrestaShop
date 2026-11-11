<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Db;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Search\Command\SearchIndexationCommand;
use PrestaShop\PrestaShop\Core\Domain\Search\Query\GetIndexedProductsCount;
use PrestaShop\PrestaShop\Core\Domain\Search\QueryResult\IndexedProductsCount;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Tests\Resources\DatabaseDump;

class SearchIndexationFeatureContext extends AbstractDomainFeatureContext
{
    private const NON_EXISTENT_SHOP_ID = 999;

    /**
     * @BeforeFeature @restore-search-index-before-feature
     *
     * @AfterFeature @restore-search-index-after-feature
     */
    public static function restoreSearchIndexTables(): void
    {
        DatabaseDump::restoreTables(['product', 'product_shop', 'search_index', 'search_word']);
    }

    /**
     * @When I mark all products as not indexed for search
     */
    public function markAllProductsAsNotIndexed(): void
    {
        Db::getInstance()->update('product', ['indexed' => 0]);
        Db::getInstance()->update('product_shop', ['indexed' => 0]);
    }

    /**
     * @When I add the missing products to the search index
     */
    public function addMissingProductsToIndex(): void
    {
        $this->getCommandBus()->handle(new SearchIndexationCommand(false, ShopConstraint::shop($this->getDefaultShopId())));
    }

    /**
     * @When I rebuild the search index
     */
    public function rebuildIndex(): void
    {
        $this->getCommandBus()->handle(new SearchIndexationCommand(true, ShopConstraint::shop($this->getDefaultShopId())));
    }

    /**
     * @Then no searchable product should be indexed in the default shop
     */
    public function assertNoProductIndexed(): void
    {
        $count = $this->getIndexedProductsCount($this->getDefaultShopId());

        Assert::assertGreaterThan(0, $count->getTotal(), 'Fixtures should contain searchable products');
        Assert::assertSame(0, $count->getIndexed());
    }

    /**
     * @Then all searchable products should be indexed in the default shop
     */
    public function assertAllProductsIndexed(): void
    {
        $count = $this->getIndexedProductsCount($this->getDefaultShopId());

        Assert::assertGreaterThan(0, $count->getTotal(), 'Fixtures should contain searchable products');
        Assert::assertSame($count->getTotal(), $count->getIndexed());
    }

    /**
     * @Then all searchable products should be indexed in all shops context
     */
    public function assertAllProductsIndexedInAllShopsContext(): void
    {
        $count = $this->getQueryBus()->handle(new GetIndexedProductsCount(ShopConstraint::allShops()));

        Assert::assertGreaterThan(0, $count->getTotal(), 'Fixtures should contain searchable products');
        Assert::assertSame($count->getTotal(), $count->getIndexed());
    }

    /**
     * @Then no searchable product should be counted for a shop that does not exist
     */
    public function assertNothingCountedForUnknownShop(): void
    {
        $count = $this->getIndexedProductsCount(self::NON_EXISTENT_SHOP_ID);

        Assert::assertSame(0, $count->getTotal());
        Assert::assertSame(0, $count->getIndexed());
    }

    private function getIndexedProductsCount(int $shopId): IndexedProductsCount
    {
        return $this->getQueryBus()->handle(new GetIndexedProductsCount(ShopConstraint::shop($shopId)));
    }
}
