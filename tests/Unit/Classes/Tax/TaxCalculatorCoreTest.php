<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Classes\Tax;

use PHPUnit\Framework\TestCase;
use Tax;
use TaxCalculator;
use Tests\Resources\TestCase\ExtendedTestCaseMethodsTrait;

class TaxCalculatorCoreTest extends TestCase
{
    use ExtendedTestCaseMethodsTrait;

    public function testGetTotalRateOK()
    {
        $tax = new Tax();
        $tax->rate = 20.6;
        $tax2 = new Tax();
        $tax2->rate = 5.5;

        $tax_calculator = new TaxCalculator([
            $tax, $tax2,
        ], TaxCalculator::COMBINE_METHOD);

        $totalRate = $tax_calculator->getTotalRate();

        $this->assertEquals(26.1, $totalRate);
    }

    public function testGetTotalRateBug()
    {
        $tax = new Tax();
        $tax->rate = 20.6;
        $tax2 = new Tax();
        $tax2->rate = 5.5;

        $tax_calculator = new TaxCalculator([
            $tax, $tax2,
        ], TaxCalculator::ONE_AFTER_ANOTHER_METHOD);

        $totalRate = $tax_calculator->getTotalRate();

        $this->assertEqualsWithEpsilon(27.233, $totalRate);
    }
}
