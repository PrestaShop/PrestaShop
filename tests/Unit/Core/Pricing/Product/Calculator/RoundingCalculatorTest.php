<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Pricing\Product\Calculator;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Pricing\Product\Calculator\RoundingCalculator;
use PrestaShop\PrestaShop\Core\Pricing\Product\ProductPrice;
use PrestaShop\PrestaShop\Core\Pricing\Rounding\RoundingService;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\ImmutableTaxablePrice;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxablePrice;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxRate;

class RoundingCalculatorTest extends TestCase
{
    public function testRoundsFinalPriceToInteger(): void
    {
        $roundingService = new RoundingService(0);
        $calculator = new RoundingCalculator($roundingService);

        $productPrice = ProductPrice::create(1, 0);
        $productPrice->setOriginalPrice(TaxablePrice::fromTaxExcluded(new DecimalNumber('29.99'), TaxRate::zero()));
        $productPrice->setFinalPrice(new ImmutableTaxablePrice(
            new DecimalNumber('29.99'),
            new DecimalNumber('29.99'),
            new DecimalNumber('0'),
            TaxRate::zero(),
        ));

        $calculator->compute($productPrice);

        // finalPrice is rounded to 30
        $this->assertTrue(
            $productPrice->getFinalPrice()->getTaxExcluded()->equals(new DecimalNumber('30'))
        );
        // originalPrice keeps full precision
        $this->assertTrue(
            $productPrice->getOriginalPrice()->getTaxExcluded()->equals(new DecimalNumber('29.99'))
        );
    }

    public function testRoundsFinalPriceDown(): void
    {
        $roundingService = new RoundingService(0);
        $calculator = new RoundingCalculator($roundingService);

        $productPrice = ProductPrice::create(1, 0);
        $productPrice->setFinalPrice(new ImmutableTaxablePrice(
            new DecimalNumber('29.49'),
            new DecimalNumber('29.49'),
            new DecimalNumber('0'),
            TaxRate::zero(),
        ));

        $calculator->compute($productPrice);

        $this->assertTrue(
            $productPrice->getFinalPrice()->getTaxExcluded()->equals(new DecimalNumber('29'))
        );
    }

    public function testDoesNotModifyOtherPriceFields(): void
    {
        $roundingService = new RoundingService(0);
        $calculator = new RoundingCalculator($roundingService);

        $productPrice = ProductPrice::create(1, 0);
        $productPrice->setUnitPrice(TaxablePrice::fromTaxExcluded(new DecimalNumber('5.75'), TaxRate::zero()));
        $productPrice->setOriginalPrice(TaxablePrice::fromTaxExcluded(new DecimalNumber('29.99'), TaxRate::zero()));
        $productPrice->setFinalPrice(new ImmutableTaxablePrice(
            new DecimalNumber('29.99'),
            new DecimalNumber('29.99'),
            new DecimalNumber('0'),
            TaxRate::zero(),
        ));

        $calculator->compute($productPrice);

        // unitPrice and originalPrice are untouched
        $this->assertTrue($productPrice->getUnitPrice()->getTaxExcluded()->equals(new DecimalNumber('5.75')));
        $this->assertTrue($productPrice->getOriginalPrice()->getTaxExcluded()->equals(new DecimalNumber('29.99')));
    }
}
