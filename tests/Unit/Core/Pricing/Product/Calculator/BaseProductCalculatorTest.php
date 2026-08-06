<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Pricing\Product\Calculator;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Pricing\Exception\ProductPriceNotFoundException;
use PrestaShop\PrestaShop\Core\Pricing\Product\Calculator\BaseProductCalculator;
use PrestaShop\PrestaShop\Core\Pricing\Product\ProductPrice;
use PrestaShop\PrestaShop\Core\Pricing\Product\Provider\MockProductProvider;
use PrestaShop\PrestaShop\Core\Pricing\Product\Provider\ProductPriceData;

class BaseProductCalculatorTest extends TestCase
{
    public function testSetsBasePricesFromProvider(): void
    {
        $provider = new MockProductProvider([
            '1' => new ProductPriceData(
                new DecimalNumber('29.99'),
                new DecimalNumber('5.00'),
                new DecimalNumber('0'),
                new DecimalNumber('0'),
            ),
        ]);
        $calculator = new BaseProductCalculator($provider);
        $productPrice = ProductPrice::create(1, 0);

        $calculator->compute($productPrice);

        $this->assertTrue(
            $productPrice->getOriginalPrice()->getTaxExcluded()->equals(new DecimalNumber('29.99'))
        );
        $this->assertTrue(
            $productPrice->getUnitPrice()->getTaxExcluded()->equals(new DecimalNumber('5.00'))
        );
        // finalPrice initialized to same value as originalPrice
        $this->assertTrue(
            $productPrice->getFinalPrice()->getTaxExcluded()->equals(new DecimalNumber('29.99'))
        );
    }

    public function testComputesCombinationImpacts(): void
    {
        $provider = new MockProductProvider([
            '1-5' => new ProductPriceData(
                new DecimalNumber('100'),
                new DecimalNumber('5.00'),
                new DecimalNumber('15.50'),
                new DecimalNumber('2.50'),
            ),
        ]);
        $calculator = new BaseProductCalculator($provider);
        $productPrice = ProductPrice::create(1, 5);

        $calculator->compute($productPrice);

        // originalPrice = 100 + 15.50 = 115.50
        $this->assertTrue(
            $productPrice->getOriginalPrice()->getTaxExcluded()->equals(new DecimalNumber('115.50'))
        );
        // unitPrice = 5.00 + 2.50 = 7.50
        $this->assertTrue(
            $productPrice->getUnitPrice()->getTaxExcluded()->equals(new DecimalNumber('7.50'))
        );
    }

    public function testUnknownProductThrowsException(): void
    {
        $provider = new MockProductProvider();
        $calculator = new BaseProductCalculator($provider);
        $productPrice = ProductPrice::create(999, 0);

        $this->expectException(ProductPriceNotFoundException::class);
        $calculator->compute($productPrice);
    }

    public function testNoCombinationMeansZeroImpacts(): void
    {
        $provider = new MockProductProvider([
            '1' => new ProductPriceData(
                new DecimalNumber('50'),
                new DecimalNumber('10'),
                new DecimalNumber('0'),
                new DecimalNumber('0'),
            ),
        ]);
        $calculator = new BaseProductCalculator($provider);
        $productPrice = ProductPrice::create(1, 0);

        $calculator->compute($productPrice);

        $this->assertTrue(
            $productPrice->getOriginalPrice()->getTaxExcluded()->equals(new DecimalNumber('50'))
        );
        $this->assertTrue(
            $productPrice->getUnitPrice()->getTaxExcluded()->equals(new DecimalNumber('10'))
        );
    }
}
