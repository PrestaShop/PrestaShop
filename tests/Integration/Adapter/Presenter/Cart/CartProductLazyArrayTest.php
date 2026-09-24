<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Adapter\Presenter\Cart;

use Context;
use Currency;
use Language;
use Link;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\HookManager;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartProductLazyArray;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\OutOfStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\DeliveryTimeNoteType;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Covers #41278: discount-display fields must be per cart line, not per combination.
 */
class CartProductLazyArrayTest extends TestCase
{
    /** @var Configuration|MockObject */
    private $mockConfiguration;
    /** @var HookManager|MockObject */
    private $mockHookManager;
    /** @var ImageRetriever|MockObject */
    private $mockImageRetriever;
    /** @var Language|MockObject */
    private $mockLanguage;
    /** @var Link|MockObject */
    private $mockLink;
    /** @var PriceFormatter|MockObject */
    private $mockPriceFormatter;
    /** @var ProductColorsRetriever|MockObject */
    private $mockProductColorsRetriever;
    /** @var ProductPresentationSettings|MockObject */
    private $mockSettings;
    /** @var TranslatorInterface|MockObject */
    private $mockTranslator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockConfiguration = $this->getMockBuilder(Configuration::class)->disableOriginalConstructor()->getMock();
        $this->mockConfiguration->method('get')->willReturn(true);

        $this->mockImageRetriever = $this->getMockBuilder(ImageRetriever::class)->disableOriginalConstructor()->getMock();
        $this->mockImageRetriever->method('getAllProductImages')->willReturn([]);

        $this->mockHookManager = $this->getMockBuilder(HookManager::class)->disableOriginalConstructor()->getMock();

        $this->mockLanguage = $this->getMockBuilder(Language::class)->disableOriginalConstructor()->getMock();
        $this->mockLanguage->id = 1;
        $this->mockLanguage->method('getId')->willReturn(1);

        $this->mockLink = $this->getMockBuilder(Link::class)->disableOriginalConstructor()->getMock();
        $this->mockPriceFormatter = $this->getMockBuilder(PriceFormatter::class)->disableOriginalConstructor()->getMock();
        $this->mockPriceFormatter->method('format')->willReturnCallback(fn ($price) => number_format((float) $price, 2));
        $this->mockProductColorsRetriever = $this->getMockBuilder(ProductColorsRetriever::class)->disableOriginalConstructor()->getMock();

        $this->mockSettings = $this->getMockBuilder(ProductPresentationSettings::class)->disableOriginalConstructor()->getMock();
        $this->mockSettings->include_taxes = false;
        $this->mockSettings->stock_management_enabled = false; // short-circuits addQuantityInformation

        $this->mockTranslator = $this->getMockBuilder(TranslatorInterface::class)->disableOriginalConstructor()->getMock();
        $this->mockTranslator->method('trans')->willReturnCallback(fn ($id) => $id);

        // The discount block formats the reduction percentage through the context locale.
        $locale = $this->getMockBuilder(\PrestaShop\PrestaShop\Core\Localization\Locale::class)
            ->disableOriginalConstructor()->getMock();
        $locale->method('formatNumber')->willReturnCallback(fn ($n) => (string) $n);
        Context::getContext()->currentLocale = $locale;
        // The per-line discount check rounds to the currency precision.
        Context::getContext()->currency = new Currency();
        Context::getContext()->currency->precision = 2;
    }

    /**
     * @dataProvider providerDiscountCases
     */
    public function testHasDiscountIsEvaluatedPerCartLine(bool $includeTaxes, float $linePrice, float $basePrice, bool $expectedHasDiscount): void
    {
        $this->mockSettings->include_taxes = $includeTaxes;

        // A catalog price rule exists for the combination (per-combination specific_prices + reduction),
        // exactly as Cart::getProducts fills them. The line's actual paid price decides the real discount.
        // Both tax bases are populated so the active include_taxes branch is exercised.
        $product = array_merge($this->baseProduct(), [
            'price' => $linePrice,
            'price_tax_exc' => $linePrice,
            'price_without_reduction' => $basePrice,
            'price_without_reduction_without_tax' => $basePrice,
            'specific_prices' => ['reduction_type' => 'percentage', 'reduction' => '0.2'],
            'reduction' => 2.0, // per-combination rule reduction (non-zero) — would falsely flag every line
        ]);

        $cartProduct = new CartProductLazyArray(
            $this->mockSettings,
            $product,
            $this->mockLanguage,
            $this->mockImageRetriever,
            $this->mockLink,
            $this->mockPriceFormatter,
            $this->mockProductColorsRetriever,
            $this->mockTranslator,
            $this->mockHookManager,
            $this->mockConfiguration
        );

        $this->assertSame($expectedHasDiscount, $cartProduct['has_discount']);
        if (!$expectedHasDiscount) {
            $this->assertNull($cartProduct['discount_percentage_absolute']);
            $this->assertNull($cartProduct['discount_amount_to_display']);
        }
    }

    public static function providerDiscountCases(): array
    {
        return [
            // [include_taxes, line paid price, non-reduced base, expected has_discount]
            // Line below the rule threshold: paid price == non-reduced base → NO discount on this line.
            'tax-excluded, line received no reduction' => [false, 10.0, 10.0, false],
            'tax-excluded, line received the reduction' => [false, 8.0, 10.0, true],
            // Tax-included is the B2C default and takes a different branch in the parent.
            'tax-included, line received no reduction' => [true, 12.0, 12.0, false],
            'tax-included, line received the reduction' => [true, 9.6, 12.0, true],
            // Float noise between the two price paths must not fabricate a discount
            // (line fractionally below base by sub-cent noise → still no discount).
            'negligible float noise is not a discount' => [false, 9.999999999, 10.0, false],
        ];
    }

    private function baseProduct(): array
    {
        return [
            'id_product' => 1,
            'id_product_attribute' => 1,
            'price_without_reduction' => 10.0,
            'price_without_reduction_without_tax' => 10.0,
            'new' => 0,
            'pack' => 0,
            'out_of_stock' => OutOfStockType::OUT_OF_STOCK_DEFAULT,
            'customizable' => 0,
            'active' => 1,
            'minimal_quantity' => 1,
            'quantity' => 1,
            'stock_quantity' => 10,
            'is_virtual' => 0,
            'additional_delivery_times' => DeliveryTimeNoteType::TYPE_DEFAULT,
        ];
    }
}
