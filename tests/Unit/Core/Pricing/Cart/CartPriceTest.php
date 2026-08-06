<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Pricing\Cart;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Pricing\Cart\CartPrice;
use PrestaShop\PrestaShop\Core\Pricing\Product\ProductPrice;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxablePrice;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxRate;

class CartPriceTest extends TestCase
{
    public function testCreateReturnsInstanceWithCartId(): void
    {
        $cartPrice = CartPrice::create(42);

        $this->assertSame(42, $cartPrice->getCartId());
    }

    public function testAllTotalsInitializedToZero(): void
    {
        $cartPrice = CartPrice::create(1);

        $this->assertTrue($cartPrice->getProductTotal()->getTaxExcluded()->equalsZero());
        $this->assertTrue($cartPrice->getShippingTotal()->getTaxExcluded()->equalsZero());
        $this->assertTrue($cartPrice->getWrappingTotal()->getTaxExcluded()->equalsZero());
        $this->assertTrue($cartPrice->getDiscountTotal()->getTaxExcluded()->equalsZero());
        $this->assertTrue($cartPrice->getCartTotal()->getTaxExcluded()->equalsZero());
    }

    public function testProductPricesInitializedToEmptyArray(): void
    {
        $cartPrice = CartPrice::create(1);

        $this->assertSame([], $cartPrice->getProductPrices());
    }

    public function testSetProductTotal(): void
    {
        $cartPrice = CartPrice::create(1);
        $total = TaxablePrice::fromTaxExcluded(new DecimalNumber('100'), TaxRate::zero());

        $cartPrice->setProductTotal($total);

        $this->assertTrue($cartPrice->getProductTotal()->getTaxExcluded()->equals(new DecimalNumber('100')));
    }

    public function testSetShippingTotal(): void
    {
        $cartPrice = CartPrice::create(1);
        $total = TaxablePrice::fromTaxExcluded(new DecimalNumber('5.99'), TaxRate::zero());

        $cartPrice->setShippingTotal($total);

        $this->assertTrue($cartPrice->getShippingTotal()->getTaxExcluded()->equals(new DecimalNumber('5.99')));
    }

    public function testSetWrappingTotal(): void
    {
        $cartPrice = CartPrice::create(1);
        $total = TaxablePrice::fromTaxExcluded(new DecimalNumber('2.50'), TaxRate::zero());

        $cartPrice->setWrappingTotal($total);

        $this->assertTrue($cartPrice->getWrappingTotal()->getTaxExcluded()->equals(new DecimalNumber('2.50')));
    }

    public function testSetDiscountTotal(): void
    {
        $cartPrice = CartPrice::create(1);
        $total = TaxablePrice::fromTaxExcluded(new DecimalNumber('10'), TaxRate::zero());

        $cartPrice->setDiscountTotal($total);

        $this->assertTrue($cartPrice->getDiscountTotal()->getTaxExcluded()->equals(new DecimalNumber('10')));
    }

    public function testSetCartTotal(): void
    {
        $cartPrice = CartPrice::create(1);
        $total = TaxablePrice::fromTaxExcluded(new DecimalNumber('95.99'), TaxRate::zero());

        $cartPrice->setCartTotal($total);

        $this->assertTrue($cartPrice->getCartTotal()->getTaxExcluded()->equals(new DecimalNumber('95.99')));
    }

    public function testSetProductPrices(): void
    {
        $cartPrice = CartPrice::create(1);
        $productPrice1 = ProductPrice::create(1, 0, 2);
        $productPrice2 = ProductPrice::create(2, 5, 1);

        $cartPrice->setProductPrices([$productPrice1, $productPrice2]);

        $this->assertCount(2, $cartPrice->getProductPrices());
        $this->assertSame(1, $cartPrice->getProductPrices()[0]->getProductId());
        $this->assertSame(2, $cartPrice->getProductPrices()[1]->getProductId());
    }
}
