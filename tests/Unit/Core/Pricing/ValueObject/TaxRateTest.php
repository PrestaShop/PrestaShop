<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Pricing\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Pricing\Exception\InvalidTaxRateException;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxRate;

class TaxRateTest extends TestCase
{
    public function testConstructionWithValidRate(): void
    {
        $rate = new TaxRate(new DecimalNumber('20'));
        $this->assertTrue($rate->getRate()->equals(new DecimalNumber('20')));
    }

    public function testConstructionRejectsNegativeRate(): void
    {
        $this->expectException(InvalidTaxRateException::class);
        new TaxRate(new DecimalNumber('-1'));
    }

    public function testZero(): void
    {
        $rate = TaxRate::zero();
        $this->assertTrue($rate->getRate()->equalsZero());
    }

    public function testGetMultiplier(): void
    {
        $rate = new TaxRate(new DecimalNumber('20'));
        $multiplier = $rate->getMultiplier();
        // 1 + 20/100 = 1.2
        $this->assertTrue($multiplier->equals(new DecimalNumber('1.2')));
    }

    public function testGetMultiplierWithZeroRate(): void
    {
        $rate = TaxRate::zero();
        $multiplier = $rate->getMultiplier();
        $this->assertTrue($multiplier->equals(new DecimalNumber('1')));
    }

    public function testEqualsWithSameRate(): void
    {
        $a = new TaxRate(new DecimalNumber('20'));
        $b = new TaxRate(new DecimalNumber('20'));
        $this->assertTrue($a->equals($b));
    }

    public function testEqualsWithDifferentRates(): void
    {
        $a = new TaxRate(new DecimalNumber('20'));
        $b = new TaxRate(new DecimalNumber('10'));
        $this->assertFalse($a->equals($b));
    }
}
