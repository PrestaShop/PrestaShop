<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Pricing\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\ImmutableTaxablePrice;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxRate;

class ImmutableTaxablePriceTest extends TestCase
{
    public function testStoresValuesAsProvided(): void
    {
        $price = new ImmutableTaxablePrice(
            new DecimalNumber('30'),
            new DecimalNumber('36'),
            new DecimalNumber('6'),
            new TaxRate(new DecimalNumber('20')),
        );

        $this->assertTrue($price->getTaxExcluded()->equals(new DecimalNumber('30')));
        $this->assertTrue($price->getTaxIncluded()->equals(new DecimalNumber('36')));
        $this->assertTrue($price->getTaxAmount()->equals(new DecimalNumber('6')));
        $this->assertTrue($price->getTaxRate()->equals(new TaxRate(new DecimalNumber('20'))));
    }

    public function testValuesAreNotRecomputed(): void
    {
        // taxExcl * 1.2 = 36, but we intentionally store 35 as taxIncl
        // ImmutableTaxablePrice should NOT auto-sync
        $price = new ImmutableTaxablePrice(
            new DecimalNumber('30'),
            new DecimalNumber('35'),
            new DecimalNumber('5'),
            new TaxRate(new DecimalNumber('20')),
        );

        $this->assertTrue($price->getTaxIncluded()->equals(new DecimalNumber('35')));
        $this->assertTrue($price->getTaxAmount()->equals(new DecimalNumber('5')));
    }
}
