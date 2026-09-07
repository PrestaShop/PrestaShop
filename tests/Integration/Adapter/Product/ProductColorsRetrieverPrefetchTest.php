<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Product;

use Context;
use Db;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Listing pages prefetch every product's colored variants in one query instead of running
 * Product::getAttributesColorList() once per product. This guards that the prefetched result of
 * getColoredVariants() is identical to the per-product result for every product.
 */
class ProductColorsRetrieverPrefetchTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->resetPrefetchCache();
    }

    protected function tearDown(): void
    {
        $this->resetPrefetchCache();
        parent::tearDown();
    }

    public function testPrefetchedColoredVariantsAreIdenticalToPerProduct(): void
    {
        self::bootKernel();
        Context::getContext()->container = self::getContainer();

        $productIds = array_map('intval', array_column(
            Db::getInstance()->executeS('SELECT id_product FROM ' . _DB_PREFIX_ . 'product ORDER BY id_product') ?: [],
            'id_product'
        ));
        self::assertNotEmpty($productIds, 'the test database must contain products');

        $retriever = new ProductColorsRetriever();

        // Per-product result first, with an empty cache (the source of truth).
        $this->resetPrefetchCache();
        $perProduct = [];
        foreach ($productIds as $idProduct) {
            $perProduct[$idProduct] = $retriever->getColoredVariants($idProduct);
        }

        // Same products, now served from the batch prefetch.
        $this->resetPrefetchCache();
        $retriever->prefetchColoredVariants($productIds);
        foreach ($productIds as $idProduct) {
            self::assertEquals(
                $perProduct[$idProduct],
                $retriever->getColoredVariants($idProduct),
                sprintf('prefetched colored variants differ from the per-product result for product %d', $idProduct)
            );
        }
    }

    private function resetPrefetchCache(): void
    {
        $property = (new ReflectionClass(ProductColorsRetriever::class))->getProperty('prefetchedColoredVariants');
        $property->setAccessible(true);
        $property->setValue(null, []);
    }
}
