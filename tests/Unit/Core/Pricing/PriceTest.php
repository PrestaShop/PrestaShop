<?php

namespace Tests\Unit\Core\Pricing;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Pricing\Price;
use PrestaShop\PrestaShop\Core\Pricing\TaxAmount;
use PrestaShop\PrestaShop\Core\Pricing\TaxRate;

class PriceTest extends TestCase
{
    public function testTwoObjectsAreEquals(): void
    {
        $price = new Price(
            new DecimalNumber('12.0')
        );

        $this->assertTrue($price->getTotal()->equals(new DecimalNumber('12.0')));
        $this->assertFalse($price->getTotal()->equals(new DecimalNumber('13.0')));
    }

    public function testHasTax(): void
    {
        $price = new Price(
            new DecimalNumber('12.0')
        );

        self::assertFalse($price->hasTax());
    }

    public function testApplyTax(): void
    {
        $price = new Price(
            new DecimalNumber('10.0')
        );

        self::assertNull($price->getTaxAmount());
        self::assertFalse($price->hasTax());
        $price->applyTaxRate(new TaxRate(new DecimalNumber('0.2')));
        self::assertTrue($price->hasTax());

        $this->assertTrue($price->getTotal()->equals(new DecimalNumber('12.0')));
    }

    public function testPriceTotal(): void
    {
        $price = new Price(
            new DecimalNumber('10.0'),
            new TaxAmount(
                new DecimalNumber('2.0'),
                new TaxRate(new DecimalNumber('0.2'))
            )
        );

        $this->assertEquals(new DecimalNumber('12.0'), $price->getTotal());
    }
}
