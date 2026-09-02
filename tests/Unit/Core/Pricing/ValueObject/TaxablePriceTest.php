<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Pricing\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxablePrice;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxRate;

class TaxablePriceTest extends TestCase
{
    public function testConstructionAutoSyncs(): void
    {
        $price = TaxablePrice::fromTaxExcluded(new DecimalNumber('100'), new TaxRate(new DecimalNumber('20')));

        $this->assertTrue($price->getTaxExcluded()->equals(new DecimalNumber('100')));
        $this->assertTrue($price->getTaxIncluded()->equals(new DecimalNumber('120')));
        $this->assertTrue($price->getTaxAmount()->equals(new DecimalNumber('20')));
    }

    public function testFromTaxIncluded(): void
    {
        $price = TaxablePrice::fromTaxIncluded(new DecimalNumber('120'), new TaxRate(new DecimalNumber('20')));

        $this->assertTrue($price->getTaxExcluded()->equals(new DecimalNumber('100')));
        $this->assertTrue($price->getTaxIncluded()->equals(new DecimalNumber('120')));
        $this->assertTrue($price->getTaxAmount()->equals(new DecimalNumber('20')));
    }

    public function testZero(): void
    {
        $price = TaxablePrice::zero();

        $this->assertTrue($price->getTaxExcluded()->equalsZero());
        $this->assertTrue($price->getTaxIncluded()->equalsZero());
        $this->assertTrue($price->getTaxAmount()->equalsZero());
        $this->assertTrue($price->getTaxRate()->getRate()->equalsZero());
    }

    public function testSetTaxExcludedRecomputes(): void
    {
        $price = TaxablePrice::fromTaxExcluded(new DecimalNumber('100'), new TaxRate(new DecimalNumber('20')));
        $price->setTaxExcluded(new DecimalNumber('200'));

        $this->assertTrue($price->getTaxExcluded()->equals(new DecimalNumber('200')));
        $this->assertTrue($price->getTaxIncluded()->equals(new DecimalNumber('240')));
        $this->assertTrue($price->getTaxAmount()->equals(new DecimalNumber('40')));
    }

    public function testSetTaxIncludedRecomputes(): void
    {
        $price = TaxablePrice::fromTaxExcluded(new DecimalNumber('100'), new TaxRate(new DecimalNumber('20')));
        $price->setTaxIncluded(new DecimalNumber('240'));

        $this->assertTrue($price->getTaxExcluded()->equals(new DecimalNumber('200')));
        $this->assertTrue($price->getTaxIncluded()->equals(new DecimalNumber('240')));
        $this->assertTrue($price->getTaxAmount()->equals(new DecimalNumber('40')));
    }

    public function testSetTaxRateRecomputesFromTaxExcluded(): void
    {
        $price = TaxablePrice::fromTaxExcluded(new DecimalNumber('100'), new TaxRate(new DecimalNumber('20')));
        $price->setTaxRate(new TaxRate(new DecimalNumber('10')));

        $this->assertTrue($price->getTaxExcluded()->equals(new DecimalNumber('100')));
        $this->assertTrue($price->getTaxIncluded()->equals(new DecimalNumber('110')));
        $this->assertTrue($price->getTaxAmount()->equals(new DecimalNumber('10')));
    }

    public function testWithZeroTaxRate(): void
    {
        $price = TaxablePrice::fromTaxExcluded(new DecimalNumber('50'), TaxRate::zero());

        $this->assertTrue($price->getTaxExcluded()->equals(new DecimalNumber('50')));
        $this->assertTrue($price->getTaxIncluded()->equals(new DecimalNumber('50')));
        $this->assertTrue($price->getTaxAmount()->equalsZero());
    }
}
