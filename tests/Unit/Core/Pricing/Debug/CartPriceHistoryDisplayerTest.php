<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Pricing\Debug;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Pricing\Cart\CartPrice;
use PrestaShop\PrestaShop\Core\Pricing\Cart\TrackedCartPrice;
use PrestaShop\PrestaShop\Core\Pricing\Debug\CartPriceHistoryDisplayer;
use PrestaShop\PrestaShop\Core\Pricing\Debug\ProductPriceHistoryDisplayer;
use PrestaShop\PrestaShop\Core\Pricing\Product\TrackedProductPrice;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxablePrice;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxRate;

class CartPriceHistoryDisplayerTest extends TestCase
{
    protected CartPriceHistoryDisplayer $displayer;

    protected function setUp(): void
    {
        $this->displayer = new CartPriceHistoryDisplayer(new ProductPriceHistoryDisplayer());
    }

    public function testFormatNonTrackedCartReturnsNoData(): void
    {
        $cartPrice = CartPrice::create(1);

        $this->assertSame('No tracking data available', $this->displayer->formatAsString($cartPrice));
        $this->assertSame(['cart' => [], 'products' => []], $this->displayer->formatAsArray($cartPrice));
    }

    public function testFormatTrackedCartWithNoModifications(): void
    {
        $cartPrice = TrackedCartPrice::create(1);

        $output = $this->displayer->formatAsString($cartPrice);

        $this->assertStringContainsString('Cart #1:', $output);
        $this->assertStringContainsString('No modifications recorded', $output);
    }

    public function testFormatTrackedCartAsString(): void
    {
        $cartPrice = TrackedCartPrice::create(42);
        $cartPrice->setProductTotal(TaxablePrice::fromTaxExcluded(new DecimalNumber('100'), TaxRate::zero()));
        $cartPrice->setCartTotal(TaxablePrice::fromTaxExcluded(new DecimalNumber('100'), TaxRate::zero()));

        $output = $this->displayer->formatAsString($cartPrice);

        $this->assertStringContainsString('Cart #42:', $output);
        $this->assertStringContainsString('productTotal', $output);
        $this->assertStringContainsString('cartTotal', $output);
        $this->assertStringContainsString('100', $output);
    }

    public function testFormatTrackedCartWithProductPrices(): void
    {
        $cartPrice = TrackedCartPrice::create(1);
        $cartPrice->setProductTotal(TaxablePrice::fromTaxExcluded(new DecimalNumber('50'), TaxRate::zero()));

        $productPrice = TrackedProductPrice::create(10, 5, 2);
        $productPrice->setUnitPrice(TaxablePrice::fromTaxExcluded(new DecimalNumber('25'), TaxRate::zero()));
        $cartPrice->setProductPrices([$productPrice]);

        $output = $this->displayer->formatAsString($cartPrice);

        $this->assertStringContainsString('Cart #1:', $output);
        $this->assertStringContainsString('Product #10 (combination: 5, qty: 2):', $output);
        $this->assertStringContainsString('unitPrice', $output);
        $this->assertStringContainsString('25', $output);
    }

    public function testFormatTrackedCartAsArray(): void
    {
        $cartPrice = TrackedCartPrice::create(1);
        $cartPrice->setProductTotal(TaxablePrice::fromTaxExcluded(new DecimalNumber('50'), TaxRate::zero()));

        $result = $this->displayer->formatAsArray($cartPrice);

        $this->assertArrayHasKey('cart', $result);
        $this->assertArrayHasKey('products', $result);
        $this->assertCount(1, $result['cart']);
        $this->assertSame('productTotal', $result['cart'][0]['property']);
        $this->assertSame([], $result['products']);
    }

    public function testFormatTrackedCartAsArrayWithProducts(): void
    {
        $cartPrice = TrackedCartPrice::create(1);
        $cartPrice->setProductTotal(TaxablePrice::fromTaxExcluded(new DecimalNumber('50'), TaxRate::zero()));

        $productPrice = TrackedProductPrice::create(10, 5, 2);
        $productPrice->setUnitPrice(TaxablePrice::fromTaxExcluded(new DecimalNumber('25'), TaxRate::zero()));
        $cartPrice->setProductPrices([$productPrice]);

        $result = $this->displayer->formatAsArray($cartPrice);

        $this->assertCount(1, $result['products']);
        $this->assertSame(10, $result['products'][0]['product_id']);
        $this->assertSame(5, $result['products'][0]['combination_id']);
        $this->assertSame(2, $result['products'][0]['quantity']);
        $this->assertCount(1, $result['products'][0]['history']);
        $this->assertSame('unitPrice', $result['products'][0]['history'][0]['property']);
    }
}
