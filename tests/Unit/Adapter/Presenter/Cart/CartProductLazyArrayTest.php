<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Presenter\Cart;

use Language;
use Link;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\HookManager;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartProductLazyArray;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;
use Symfony\Contracts\Translation\TranslatorInterface;

class CartProductLazyArrayTest extends TestCase
{
    private const DEFAULT_IN_STOCK = 'In stock - default';
    private const DEFAULT_BACKORDER = 'Backorder - default';
    private const DEFAULT_NOT_AVAILABLE = 'Not available - default';

    private const PRODUCT_IN_STOCK = 'Ready today';
    private const PRODUCT_BACKORDER = 'Backorder';
    private const COMBINATION_IN_STOCK = 'Blue ready';
    private const COMBINATION_BACKORDER = 'Blue backorder';

    private const BASE_PRODUCT = [
        'id_product' => 0,
        'id_product_attribute' => 0,
        'price' => 0,
        'price_tax_exc' => 0,
        'specific_prices' => 0,
        'reduction' => 0,
        'price_without_reduction' => 0,
        'price_without_reduction_without_tax' => 0,
        'new' => 0,
        'pack' => 0,
        'customizable' => 0,
        'active' => 1,
        'minimal_quantity' => 1,
        'is_virtual' => 0,
        'show_price' => 1,
        'available_now' => '',
        'available_later' => '',
        'allow_oosp' => 0,
    ];

    /**
     * @dataProvider provideAvailabilityMessages
     */
    public function testAvailabilityMessage(array $product, ?string $expectedMessage): void
    {
        $this->assertSame($expectedMessage, $this->createLazyArray($product)->availability_message);
    }

    public static function provideAvailabilityMessages(): iterable
    {
        yield 'in stock, only the product in-stock label is set' => [
            ['stock_quantity' => 5, 'quantity' => 1, 'available_now' => self::PRODUCT_IN_STOCK],
            self::PRODUCT_IN_STOCK,
        ];

        yield 'in stock, combination and product in-stock labels are set' => [
            [
                'stock_quantity' => 5,
                'quantity' => 1,
                'available_now' => self::PRODUCT_IN_STOCK,
                'attributes' => ['Color' => 'Blue'],
                'combination_available_now' => self::COMBINATION_IN_STOCK,
                'combination_available_later' => '',
            ],
            self::COMBINATION_IN_STOCK,
        ];

        yield 'in stock, combination without own label falls back to the product in-stock label' => [
            [
                'stock_quantity' => 5,
                'quantity' => 1,
                'available_now' => self::PRODUCT_IN_STOCK,
                'attributes' => ['Color' => 'Blue'],
                'combination_available_now' => null,
                'combination_available_later' => null,
            ],
            self::PRODUCT_IN_STOCK,
        ];

        yield 'in stock, only the product out-of-stock label is set' => [
            ['stock_quantity' => 5, 'quantity' => 1, 'available_later' => self::PRODUCT_BACKORDER],
            self::DEFAULT_IN_STOCK,
        ];

        yield 'in stock, both product labels are set' => [
            ['stock_quantity' => 5, 'quantity' => 1, 'available_now' => self::PRODUCT_IN_STOCK, 'available_later' => self::PRODUCT_BACKORDER],
            self::PRODUCT_IN_STOCK,
        ];

        yield 'partially in stock, only the product in-stock label is set' => [
            ['stock_quantity' => 2, 'quantity' => 3, 'allow_oosp' => 1, 'available_now' => self::PRODUCT_IN_STOCK],
            '2 - ' . self::PRODUCT_IN_STOCK . ', the rest - ' . self::DEFAULT_BACKORDER,
        ];

        yield 'partially in stock, combination and product in-stock labels are set' => [
            [
                'stock_quantity' => 2,
                'quantity' => 3,
                'allow_oosp' => 1,
                'available_now' => self::PRODUCT_IN_STOCK,
                'attributes' => ['Color' => 'Blue'],
                'combination_available_now' => self::COMBINATION_IN_STOCK,
                'combination_available_later' => '',
            ],
            '2 - ' . self::COMBINATION_IN_STOCK . ', the rest - ' . self::DEFAULT_BACKORDER,
        ];

        yield 'out of stock with backorders, both product labels are set' => [
            ['stock_quantity' => 0, 'quantity' => 1, 'allow_oosp' => 1, 'available_now' => self::PRODUCT_IN_STOCK, 'available_later' => self::PRODUCT_BACKORDER],
            self::PRODUCT_BACKORDER,
        ];

        yield 'out of stock with backorders, combination and product out-of-stock labels are set' => [
            [
                'stock_quantity' => 0,
                'quantity' => 1,
                'allow_oosp' => 1,
                'available_later' => self::PRODUCT_BACKORDER,
                'attributes' => ['Color' => 'Blue'],
                'combination_available_now' => '',
                'combination_available_later' => self::COMBINATION_BACKORDER,
            ],
            self::COMBINATION_BACKORDER,
        ];
    }

    private function createLazyArray(array $product): CartProductLazyArray
    {
        $language = $this->createMock(Language::class);
        $language->id = 1;
        $language->method('getId')->willReturn(1);

        $settings = $this->createMock(ProductPresentationSettings::class);
        $settings->method('shouldShowPrice')->willReturn(true);
        $settings->stock_management_enabled = true;

        $configuration = $this->createMock(Configuration::class);
        $configuration->method('get')->willReturnCallback(static function (string $key) {
            return match ($key) {
                'PS_LABEL_IN_STOCK_PRODUCTS' => [1 => self::DEFAULT_IN_STOCK],
                'PS_LABEL_OOS_PRODUCTS_BOA' => [1 => self::DEFAULT_BACKORDER],
                'PS_LABEL_OOS_PRODUCTS_BOD' => [1 => self::DEFAULT_NOT_AVAILABLE],
                default => null,
            };
        });

        $imageRetriever = $this->createMock(ImageRetriever::class);
        $imageRetriever->method('getAllProductImages')->willReturn([]);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnCallback(static function (string $id, array $parameters = []): string {
            return strtr($id, $parameters);
        });

        return new CartProductLazyArray(
            $settings,
            array_merge(self::BASE_PRODUCT, $product),
            $language,
            $imageRetriever,
            $this->createMock(Link::class),
            $this->createMock(PriceFormatter::class),
            $this->createMock(ProductColorsRetriever::class),
            $translator,
            $this->createMock(HookManager::class),
            $configuration
        );
    }
}
