<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Pack;

use Pack;
use Product;
use StockAvailable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * A pack that decrements the products it contains can only be assembled as many times as its contents
 * allow, which is not what its own stock row says. Anything checking whether a pack can be ordered has to
 * ask Pack::getQuantity(), not the row. See #24531.
 */
class PackAvailableQuantityTest extends KernelTestCase
{
    /** @var int[] */
    private array $createdProductIds = [];

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;
    }

    protected function tearDown(): void
    {
        foreach ($this->createdProductIds as $id) {
            (new Product($id))->delete();
        }
        $this->createdProductIds = [];
        parent::tearDown();
    }

    private function makeProduct(string $name, int $quantity): int
    {
        $product = new Product();
        $product->name = ['1' => $name];
        $product->link_rewrite = ['1' => strtolower(str_replace(' ', '-', $name))];
        $product->price = 10.0;
        $product->add();
        $this->createdProductIds[] = (int) $product->id;
        StockAvailable::setQuantity((int) $product->id, 0, $quantity);

        return (int) $product->id;
    }

    public function testAPackDecrementingItsContentsIsLimitedByThemNotByItsOwnStockRow(): void
    {
        $itemA = $this->makeProduct('Pack item A ' . uniqid(), 10);
        $itemB = $this->makeProduct('Pack item B ' . uniqid(), 10);

        $pack = new Product();
        $pack->name = ['1' => 'Pack of two ' . uniqid()];
        $pack->link_rewrite = ['1' => 'pack-of-two-' . uniqid()];
        $pack->price = 30.0;
        $pack->cache_is_pack = true;
        $pack->pack_stock_type = Pack::STOCK_TYPE_PRODUCTS_ONLY;
        $pack->add();
        $packId = (int) $pack->id;
        $this->createdProductIds[] = $packId;

        Pack::addItem($packId, $itemA, 2);
        Pack::addItem($packId, $itemB, 2);
        // The pack's own row claims plenty, which is exactly the number the order checks used to read.
        StockAvailable::setQuantity($packId, 0, 10);

        // isPack() memoises per request and the product was created before it had any contents.
        Pack::resetStaticCache();

        self::assertSame(
            10,
            (int) StockAvailable::getQuantityAvailableByProduct($packId, 0),
            'the pack stock row is unaware of what the pack contains'
        );

        // Two of each item, ten of each in stock, so the pack can be assembled five times.
        self::assertSame(
            5,
            (int) Pack::getQuantity($packId),
            'Pack::getQuantity() is limited by the contents'
        );
    }
}
