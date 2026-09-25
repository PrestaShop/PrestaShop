<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Context;
use Db;
use Product;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Listing pages prefetch every product's "new" flag in one query instead of running
 * Product::isNewStatic() (a COUNT query) once per product during presentation. This guards that the
 * prefetched flag is identical to the per-product flag for every product.
 */
class ProductIsNewPrefetchTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->resetIsNewCache();
    }

    protected function tearDown(): void
    {
        $this->resetIsNewCache();
        parent::tearDown();
    }

    public function testPrefetchedIsNewFlagIsIdenticalToPerProductFlag(): void
    {
        self::bootKernel();
        Context::getContext()->container = self::getContainer();

        $productIds = array_map('intval', array_column(
            Db::getInstance()->executeS('SELECT id_product FROM ' . _DB_PREFIX_ . 'product ORDER BY id_product') ?: [],
            'id_product'
        ));
        self::assertNotEmpty($productIds, 'the test database must contain products');

        // Per-product result first, with an empty cache (the source of truth).
        $this->resetIsNewCache();
        $perProduct = [];
        foreach ($productIds as $idProduct) {
            $perProduct[$idProduct] = Product::isNewStatic($idProduct);
        }

        // Same products, now served from the batch prefetch.
        $this->resetIsNewCache();
        Product::prefetchIsNew($productIds);
        foreach ($productIds as $idProduct) {
            self::assertSame(
                $perProduct[$idProduct],
                Product::isNewStatic($idProduct),
                sprintf('prefetched new flag differs from the per-product flag for product %d', $idProduct)
            );
        }
    }

    private function resetIsNewCache(): void
    {
        $property = (new ReflectionClass(Product::class))->getProperty('cacheIsNew');
        $property->setAccessible(true);
        $property->setValue(null, []);
    }
}
