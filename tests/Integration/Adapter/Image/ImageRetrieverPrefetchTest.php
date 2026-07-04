<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Image;

use Configuration;
use Context;
use Db;
use Language;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Listing pages prefetch every product's images in one query per table instead of running
 * getImages()/getCombinationImages() once per product. This guards that the prefetched result of
 * getAllProductImages() is identical to the per-product result for every product (all images,
 * combination variants and order preserved).
 */
class ImageRetrieverPrefetchTest extends KernelTestCase
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

    public function testPrefetchedImagesAreIdenticalToPerProductImages(): void
    {
        self::bootKernel();
        Context::getContext()->container = self::getContainer();
        $language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $retriever = new ImageRetriever(Context::getContext()->link);

        $productIds = array_map(
            static fn (array $row): int => (int) $row['id_product'],
            Db::getInstance()->executeS('SELECT id_product FROM ' . _DB_PREFIX_ . 'product ORDER BY id_product')
        );
        self::assertNotEmpty($productIds, 'the test database must contain products with images');

        // Per-product result first, with an empty prefetch cache.
        $perProduct = [];
        foreach ($productIds as $idProduct) {
            $perProduct[$idProduct] = $retriever->getAllProductImages(
                ['id_product' => $idProduct, 'id_product_attribute' => 0],
                $language
            );
        }

        // Same products, now served from the batch prefetch.
        $retriever->prefetchImagesForProducts($productIds, (int) $language->id);

        foreach ($productIds as $idProduct) {
            $prefetched = $retriever->getAllProductImages(
                ['id_product' => $idProduct, 'id_product_attribute' => 0],
                $language
            );
            self::assertSame(
                $perProduct[$idProduct],
                $prefetched,
                sprintf('prefetched images differ from per-product images for product %d', $idProduct)
            );
        }
    }

    private function resetPrefetchCache(): void
    {
        $reflection = new ReflectionClass(ImageRetriever::class);
        foreach (['prefetchedProductImages', 'prefetchedCombinationImages'] as $property) {
            $prop = $reflection->getProperty($property);
            $prop->setAccessible(true);
            $prop->setValue(null, []);
        }
    }
}
