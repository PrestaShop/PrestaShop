<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Classes\Tax;

use AverageTaxOfProductsTaxCalculator;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Foundation\Database\DatabaseInterface;

class AverageTaxOfProductsTaxCalculatorTest extends TestCase
{
    public function testTaxIsSplitAccordingToShareOfEachTaxRate(): void
    {
        $db = $this->createMock(DatabaseInterface::class);
        $db->method('select')->withAnyParameters()->willReturn([
            ['id_tax' => 1, 'rate' => 10, 'total_price_tax_excl' => 20],
            ['id_tax' => 2, 'rate' => 20, 'total_price_tax_excl' => 10],
        ]);
        $configuration = $this->createMock(ConfigurationInterface::class);

        $taxCalculator = new AverageTaxOfProductsTaxCalculator($db, $configuration);

        $amounts = $taxCalculator->getTaxesAmount(7, null, 2, PS_ROUND_HALF_UP);

        $expected = [
            1 => round(7 * 20 / (20 + 10) * 0.1, 2),
            2 => round(7 * 10 / (20 + 10) * 0.2, 2),
        ];

        $this->assertEquals($expected, $amounts);
    }
}
