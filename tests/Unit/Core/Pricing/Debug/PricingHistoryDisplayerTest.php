<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Pricing\Debug;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Pricing\Debug\ProductPriceHistoryDisplayer;
use PrestaShop\PrestaShop\Core\Pricing\Product\ProductPrice;
use PrestaShop\PrestaShop\Core\Pricing\Product\TrackedProductPrice;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxablePrice;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxRate;

class PricingHistoryDisplayerTest extends TestCase
{
    public function testFormatNonTrackedDtoReturnsNoData(): void
    {
        $displayer = new ProductPriceHistoryDisplayer();
        $productPrice = ProductPrice::create(1, 0);

        $this->assertSame('No tracking data available', $displayer->formatAsString($productPrice));
        $this->assertSame([], $displayer->formatAsArray($productPrice));
    }

    public function testFormatTrackedDtoWithNoModifications(): void
    {
        $displayer = new ProductPriceHistoryDisplayer();
        $productPrice = TrackedProductPrice::create(1, 0);

        $this->assertSame('No modifications recorded', $displayer->formatAsString($productPrice));
    }

    public function testFormatTrackedDtoAsString(): void
    {
        $displayer = new ProductPriceHistoryDisplayer();
        $productPrice = TrackedProductPrice::create(1, 0);
        $productPrice->setUnitPrice(TaxablePrice::fromTaxExcluded(new DecimalNumber('29.99'), TaxRate::zero()));

        $output = $displayer->formatAsString($productPrice);

        $this->assertStringContainsString('unitPrice', $output);
        $this->assertStringContainsString('0', $output);
        $this->assertStringContainsString('29.99', $output);
    }

    public function testFormatTrackedDtoAsArray(): void
    {
        $displayer = new ProductPriceHistoryDisplayer();
        $productPrice = TrackedProductPrice::create(1, 0);
        $productPrice->setUnitPrice(TaxablePrice::fromTaxExcluded(new DecimalNumber('29.99'), TaxRate::zero()));

        $result = $displayer->formatAsArray($productPrice);

        $this->assertCount(1, $result);
        $this->assertSame('unitPrice', $result[0]['property']);
        $this->assertSame('0', $result[0]['previous']);
        $this->assertSame('29.99', $result[0]['new']);
        $this->assertIsString($result[0]['caller']);
        $this->assertIsInt($result[0]['line']);
    }
}
