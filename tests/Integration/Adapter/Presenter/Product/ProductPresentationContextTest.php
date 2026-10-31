<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Presenter\Product;

use Configuration;
use Db;
use Language;
use Link;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductPresentationContext;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;
use Product;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * The presentation context lets the listing presenter resolve per-product lookups (new flag,
 * colored variants, images) for a whole set with a single batched query each. These tests prove
 * that the batched resolution returns exactly what the per-product resolution returned, and that
 * the lazy array actually routes through the batch when a context is provided.
 */
class ProductPresentationContextTest extends KernelTestCase
{
    private ProductPresentationSettings $settings;

    protected function setUp(): void
    {
        parent::setUp();

        $this->settings = new ProductPresentationSettings();
        $this->settings->catalog_mode = false;
        $this->settings->restricted_country_mode = false;
        $this->settings->showPrices = true;
    }

    public function testRememberInvokesLoaderOnceWithAllUniqueProductIds(): void
    {
        $context = new ProductPresentationContext([3, 1, 2, 2]);

        $calls = 0;
        $seenIds = null;
        $loader = function (array $ids) use (&$calls, &$seenIds) {
            ++$calls;
            $seenIds = $ids;

            return array_flip($ids);
        };

        $first = $context->remember('concern', $loader);
        $second = $context->remember('concern', $loader);

        $this->assertSame(1, $calls, 'The batch loader must run only once for the whole set.');
        $this->assertSame($first, $second, 'The memoized result must be reused.');
        $this->assertSame([3, 1, 2], $seenIds, 'The loader must receive every unique product id, de-duplicated.');
        $this->assertSame([3, 1, 2], $context->getProductIds());
    }

    public function testListingContextRoutesImagesThroughTheBatchedLoader(): void
    {
        $product = $this->productData(1);

        $batchedImages = [['id_image' => 111, 'cover' => 1, 'associatedVariants' => []]];
        $perProductImages = [['id_image' => 999, 'cover' => 1, 'associatedVariants' => []]];

        $imageRetriever = $this->createMock(ImageRetriever::class);
        $imageRetriever->method('getProductsImages')->willReturn([1 => $batchedImages]);
        $imageRetriever->method('getAllProductImages')->willReturn($perProductImages);

        $withContext = $this->presentListing($product, $imageRetriever, new ProductPresentationContext([1]));
        $this->assertSame(111, $withContext['cover']['id_image'], 'With a context, images must come from the batched loader.');

        $withoutContext = $this->presentListing($product, $imageRetriever, null);
        $this->assertSame(999, $withoutContext['cover']['id_image'], 'Without a context, images must fall back to the per-product loader.');
    }

    public function testBatchedImagesMatchPerProductResolution(): void
    {
        $ids = $this->productIdsWithImages();
        $this->assertNotEmpty($ids, 'The test catalog must contain products with images.');

        $language = $this->defaultLanguage();
        $imageRetriever = new ImageRetriever(new Link());

        $batched = $imageRetriever->getProductsImages($ids, $language);

        foreach ($ids as $id) {
            $this->assertEquals(
                $this->normalizeImages($imageRetriever->getAllProductImages(['id_product' => $id], $language)),
                $this->normalizeImages($batched[$id] ?? []),
                sprintf('Batched images differ from per-product resolution for product %d.', $id)
            );
        }
    }

    /**
     * The list of variant ids associated with an image is a set: several combination rows share a
     * single image (hence an identical i.position), and getCombinationImages() orders only by
     * position, so the order among them is engine-defined - already non-deterministic in the
     * original per-product code. Compare it as a set.
     *
     * @param array<int, array> $images
     *
     * @return array<int, array>
     */
    private function normalizeImages(array $images): array
    {
        foreach ($images as &$image) {
            if (isset($image['associatedVariants']) && is_array($image['associatedVariants'])) {
                sort($image['associatedVariants']);
            }
        }

        return $images;
    }

    public function testBatchedNewFlagsMatchPerProductResolution(): void
    {
        $ids = $this->productIdsWithImages();
        $this->assertNotEmpty($ids);

        $flags = Product::getNewProductsFlags($ids);

        foreach ($ids as $id) {
            $this->assertSame(
                (bool) Product::isNewStatic($id),
                $flags[$id] ?? null,
                sprintf('Batched "new" flag differs from isNewStatic() for product %d.', $id)
            );
        }
    }

    public function testBatchedColoredVariantsMatchPerProductResolution(): void
    {
        $ids = $this->productIdsWithImages();
        $this->assertNotEmpty($ids);

        $colorsMap = Product::getAttributesColorList($ids);
        $colorsRetriever = new ProductColorsRetriever();

        foreach ($ids as $id) {
            // getColoredVariants() returns false when a product has no colored variants;
            // the batched map simply omits the key. Both mean "no variants" downstream.
            $expected = $colorsRetriever->getColoredVariants($id);
            if (!is_array($expected)) {
                $expected = null;
            }

            $this->assertEquals(
                $expected,
                $colorsMap[$id] ?? null,
                sprintf('Batched colored variants differ from getColoredVariants() for product %d.', $id)
            );
        }
    }

    /**
     * @return int[]
     */
    private function productIdsWithImages(): array
    {
        $rows = Db::getInstance()->executeS(
            'SELECT DISTINCT id_product FROM `' . _DB_PREFIX_ . 'image` ORDER BY id_product LIMIT 12'
        ) ?: [];

        return array_map('intval', array_column($rows, 'id_product'));
    }

    private function defaultLanguage(): Language
    {
        return new Language((int) Configuration::get('PS_LANG_DEFAULT'));
    }

    /**
     * @return array<string, mixed>
     */
    private function productData(int $idProduct): array
    {
        return [
            'available_for_order' => true,
            'id_product' => $idProduct,
            'id_product_attribute' => 0,
            'link_rewrite' => 'product',
            'reference' => 'ref',
            'price' => null,
            'price_without_reduction' => null,
            'price_tax_exc' => null,
            'specific_prices' => null,
            'customizable' => false,
            'quantity' => 1,
            'allow_oosp' => false,
            'online_only' => false,
            'reduction' => false,
            'on_sale' => false,
            'pack' => false,
            'show_price' => true,
            'active' => true,
        ];
    }

    /**
     * @param array<string, mixed> $product
     */
    private function presentListing(array $product, ImageRetriever $imageRetriever, ?ProductPresentationContext $context)
    {
        $link = $this->createMock(Link::class);
        $link->method('getAddToCartURL')->withAnyParameters()->willReturn('http://add-to-cart.url');

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->withAnyParameters()->willReturn('some label');

        $priceFormatter = $this->createMock(PriceFormatter::class);
        $priceFormatter->method('convertAmount')->withAnyParameters()->willReturnArgument(0);
        $priceFormatter->method('format')->withAnyParameters()->willReturnCallback(function (?float $price) {
            return '#' . $price;
        });

        $presenter = new ProductListingPresenter(
            $imageRetriever,
            $link,
            $priceFormatter,
            $this->createMock(ProductColorsRetriever::class),
            $translator
        );
        $presenter->setPresentationContext($context);

        return $presenter->present($this->settings, $product, $this->createMock(Language::class));
    }
}
