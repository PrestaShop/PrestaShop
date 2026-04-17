<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Pricing\Cart;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Pricing\Cart\TrackedCartPrice;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxablePrice;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxRate;

class TrackedCartPriceTest extends TestCase
{
    public function testCreateReturnsInstanceWithCartId(): void
    {
        $cartPrice = TrackedCartPrice::create(42);

        $this->assertSame(42, $cartPrice->getCartId());
    }

    public function testBreakdownIsEmptyInitially(): void
    {
        $cartPrice = TrackedCartPrice::create(1);

        $this->assertSame(0, $cartPrice->getBreakdown()->count());
    }

    public function testSetProductTotalRecordsModification(): void
    {
        $cartPrice = TrackedCartPrice::create(1);
        $total = TaxablePrice::fromTaxExcluded(new DecimalNumber('100'), TaxRate::zero());

        $cartPrice->setProductTotal($total);

        $this->assertSame(1, $cartPrice->getBreakdown()->count());
        $step = $cartPrice->getBreakdown()->getSteps()[0];
        $this->assertSame('productTotal', $step->getProperty());
        $this->assertSame('0', $step->getPreviousValue());
        $this->assertSame('100', $step->getNewValue());
    }

    public function testSetShippingTotalRecordsModification(): void
    {
        $cartPrice = TrackedCartPrice::create(1);
        $total = TaxablePrice::fromTaxExcluded(new DecimalNumber('5.99'), TaxRate::zero());

        $cartPrice->setShippingTotal($total);

        $this->assertSame(1, $cartPrice->getBreakdown()->count());
        $step = $cartPrice->getBreakdown()->getSteps()[0];
        $this->assertSame('shippingTotal', $step->getProperty());
    }

    public function testSetWrappingTotalRecordsModification(): void
    {
        $cartPrice = TrackedCartPrice::create(1);
        $total = TaxablePrice::fromTaxExcluded(new DecimalNumber('2.50'), TaxRate::zero());

        $cartPrice->setWrappingTotal($total);

        $this->assertSame(1, $cartPrice->getBreakdown()->count());
        $step = $cartPrice->getBreakdown()->getSteps()[0];
        $this->assertSame('wrappingTotal', $step->getProperty());
    }

    public function testSetDiscountTotalRecordsModification(): void
    {
        $cartPrice = TrackedCartPrice::create(1);
        $total = TaxablePrice::fromTaxExcluded(new DecimalNumber('10'), TaxRate::zero());

        $cartPrice->setDiscountTotal($total);

        $this->assertSame(1, $cartPrice->getBreakdown()->count());
        $step = $cartPrice->getBreakdown()->getSteps()[0];
        $this->assertSame('discountTotal', $step->getProperty());
    }

    public function testSetCartTotalRecordsModification(): void
    {
        $cartPrice = TrackedCartPrice::create(1);
        $total = TaxablePrice::fromTaxExcluded(new DecimalNumber('95.99'), TaxRate::zero());

        $cartPrice->setCartTotal($total);

        $this->assertSame(1, $cartPrice->getBreakdown()->count());
        $step = $cartPrice->getBreakdown()->getSteps()[0];
        $this->assertSame('cartTotal', $step->getProperty());
    }

    public function testMultipleSettersAccumulateModifications(): void
    {
        $cartPrice = TrackedCartPrice::create(1);

        $cartPrice->setProductTotal(TaxablePrice::fromTaxExcluded(new DecimalNumber('100'), TaxRate::zero()));
        $cartPrice->setShippingTotal(TaxablePrice::fromTaxExcluded(new DecimalNumber('5'), TaxRate::zero()));
        $cartPrice->setCartTotal(TaxablePrice::fromTaxExcluded(new DecimalNumber('105'), TaxRate::zero()));

        $this->assertSame(3, $cartPrice->getBreakdown()->count());
    }

    public function testSetProductPricesDoesNotRecordModification(): void
    {
        $cartPrice = TrackedCartPrice::create(1);

        $cartPrice->setProductPrices([]);

        $this->assertSame(0, $cartPrice->getBreakdown()->count());
    }

    public function testRecordsCallerClass(): void
    {
        $cartPrice = TrackedCartPrice::create(1);

        $cartPrice->setProductTotal(TaxablePrice::fromTaxExcluded(new DecimalNumber('50'), TaxRate::zero()));

        $step = $cartPrice->getBreakdown()->getSteps()[0];
        $this->assertSame(self::class, $step->getCallerClass());
    }
}
