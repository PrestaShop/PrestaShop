<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Order;

use Context;
use Db;
use Image;
use Order;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Exposes the protected image helpers so the test can compare batched resolution to per-line resolution.
 */
class TestableImageOrder extends Order
{
    /**
     * @param array<string, mixed> $product
     * @param array<int, int>|null $combinationImageIds
     * @param array<int, int>|null $coverImageIds
     *
     * @return array<string, mixed>
     */
    public function resolveImage(array $product, ?array $combinationImageIds = null, ?array $coverImageIds = null): array
    {
        $this->setProductImageInformations($product, $combinationImageIds, $coverImageIds);

        return $product;
    }

    /** @param array<int, array<string, mixed>> $products @return array<int, int> */
    public function combinationImageIds(array $products): array
    {
        return $this->getProductsCombinationImageIds($products);
    }

    /** @param array<int, array<string, mixed>> $products @return array<int, int> */
    public function coverImageIds(array $products): array
    {
        return $this->getProductsCoverImageIds($products);
    }
}

/**
 * Order::getProducts() resolves every line's image id once per line. This guards that resolving the
 * whole order in two batched queries yields the same image for every line.
 */
class OrderProductImagePrefetchTest extends KernelTestCase
{
    public function testBatchedImageIdsMatchPerLineResolution(): void
    {
        self::bootKernel();
        Context::getContext()->container = self::getContainer();

        $rows = [];
        // Lines that resolve through the combination-image path.
        foreach (Db::getInstance()->executeS('
            SELECT pa.id_product, pa.id_product_attribute
            FROM ' . _DB_PREFIX_ . 'product_attribute pa
            JOIN ' . _DB_PREFIX_ . 'product_attribute_image pai ON pai.id_product_attribute = pa.id_product_attribute
            GROUP BY pa.id_product_attribute LIMIT 15') ?: [] as $row) {
            $rows[] = ['product_id' => (int) $row['id_product'], 'product_attribute_id' => (int) $row['id_product_attribute']];
        }
        // Lines that fall back to the product cover.
        foreach (Db::getInstance()->executeS('SELECT id_product FROM ' . _DB_PREFIX_ . 'image WHERE cover = 1 LIMIT 15') ?: [] as $row) {
            $rows[] = ['product_id' => (int) $row['id_product'], 'product_attribute_id' => 0];
        }
        self::assertNotEmpty($rows, 'the test database must contain products with images');

        $order = new TestableImageOrder();
        $combinationImageIds = $order->combinationImageIds($rows);
        $coverImageIds = $order->coverImageIds($rows);

        foreach ($rows as $row) {
            $perLine = $order->resolveImage($row);
            $batched = $order->resolveImage($row, $combinationImageIds, $coverImageIds);

            self::assertSame(
                $perLine['image'] instanceof Image ? (int) $perLine['image']->id : null,
                $batched['image'] instanceof Image ? (int) $batched['image']->id : null,
                sprintf('batched image differs from per-line for product %d / combination %d', $row['product_id'], $row['product_attribute_id'])
            );
        }
    }
}
