<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\BestSales;

use Configuration;
use Context;
use Currency;
use Customer;
use Db;
use PrestaShop\PrestaShop\Adapter\BestSales\BestSalesProductSearchProvider;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use ProductSale;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * The provider used to replace a random sort order with "sales, highest to lowest" and answer that
 * instead, so a caller asking for a random selection of best sellers silently got the same list every
 * time. CategoryProductSearchProvider has always honoured a random order.
 */
class BestSalesRandomSortTest extends KernelTestCase
{
    /** @var int[] */
    private array $seeded = [];

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $context = Context::getContext();
        $context->container = self::getContainer();
        // ProductSearchContext reads currency and customer off the context; the CLI one has neither.
        $context->currency = $context->currency ?? new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        $context->customer = $context->customer ?? new Customer();

        // Enough sellers that a random order is very unlikely to match the sales order by chance.
        $products = Db::getInstance()->executeS(
            'SELECT p.id_product FROM ' . _DB_PREFIX_ . 'product p
             INNER JOIN ' . _DB_PREFIX_ . 'product_shop ps ON ps.id_product = p.id_product AND ps.id_shop = 1
             WHERE ps.active = 1 AND ps.visibility != "none" LIMIT 12'
        ) ?: [];

        if (count($products) < 8) {
            $this->markTestSkipped('Not enough active products to tell two orderings apart.');
        }

        $quantity = 100;
        foreach ($products as $row) {
            $id = (int) $row['id_product'];
            if (ProductSale::getNbrSales($id) > 0) {
                continue;
            }
            Db::getInstance()->insert('product_sale', [
                'id_product' => $id,
                'quantity' => $quantity--,
                'sale_nbr' => 1,
                'date_upd' => date('Y-m-d H:i:s'),
            ]);
            $this->seeded[] = $id;
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->seeded as $id) {
            Db::getInstance()->delete('product_sale', 'id_product = ' . $id);
        }
        $this->seeded = [];

        parent::tearDown();
    }

    public function testARandomSortOrderIsNotReplacedBySalesOrder(): void
    {
        $salesOrder = $this->idsFor(new SortOrder('product', 'sales', 'desc'));
        $this->assertGreaterThanOrEqual(8, count($salesOrder), 'the fixture must return enough products');

        // A random order that never differs from the sales order over this many draws is not random.
        $sawADifferentOrder = false;
        for ($draw = 0; $draw < 12 && !$sawADifferentOrder; ++$draw) {
            $sawADifferentOrder = $this->idsFor(SortOrder::random()) !== $salesOrder;
        }

        $this->assertTrue($sawADifferentOrder, 'a random sort order produced the sales order every time');
    }

    /**
     * The non-random path is untouched: a field whose direction is not forced still reverses.
     */
    public function testAnExplicitSortOrderIsStillHonoured(): void
    {
        $ascending = $this->idsFor(new SortOrder('product', 'reference', 'asc'));
        $descending = $this->idsFor(new SortOrder('product', 'reference', 'desc'));

        $this->assertNotSame([], $ascending);
        // The two directions pick different ends of the catalogue, so the pages differ.
        $this->assertNotSame(
            $ascending,
            $descending,
            'ascending and descending must not return the same page'
        );
    }

    /**
     * getBestSales() forces DESC when ordering by sales, random or not - pinned so the random branch
     * cannot be blamed for it later.
     */
    public function testOrderingBySalesIsAlwaysDescending(): void
    {
        $this->assertSame(
            $this->idsFor(new SortOrder('product', 'sales', 'desc')),
            $this->idsFor(new SortOrder('product', 'sales', 'asc'))
        );
    }

    /**
     * @return int[]
     */
    private function idsFor(SortOrder $sortOrder): array
    {
        $provider = new BestSalesProductSearchProvider(self::getContainer()->get(TranslatorInterface::class));

        $query = new ProductSearchQuery();
        $query->setResultsPerPage(10)->setPage(1)->setSortOrder($sortOrder);

        $result = $provider->runQuery(new ProductSearchContext(Context::getContext()), $query);

        return array_map(
            static fn (array $product): int => (int) $product['id_product'],
            $result->getProducts()
        );
    }
}
