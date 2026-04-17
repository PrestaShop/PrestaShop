<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Pricing\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\PriceBreakdown;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\PriceModification;

class PriceBreakdownTest extends TestCase
{
    public function testEmptyBreakdown(): void
    {
        $breakdown = new PriceBreakdown();

        $this->assertSame(0, $breakdown->count());
        $this->assertSame([], $breakdown->getSteps());
    }

    public function testAddStep(): void
    {
        $breakdown = new PriceBreakdown();
        $step = new PriceModification('TestCalculator', 42, 'unitPrice', '0', '29.99');

        $breakdown->addStep($step);

        $this->assertSame(1, $breakdown->count());
        $this->assertSame([$step], $breakdown->getSteps());
    }

    public function testMultipleSteps(): void
    {
        $breakdown = new PriceBreakdown();
        $step1 = new PriceModification('BaseCalculator', 10, 'unitPrice', '0', '29.99');
        $step2 = new PriceModification('RoundingCalculator', 20, 'unitPrice', '29.99', '30');

        $breakdown->addStep($step1);
        $breakdown->addStep($step2);

        $this->assertSame(2, $breakdown->count());
        $steps = $breakdown->getSteps();
        $this->assertSame($step1, $steps[0]);
        $this->assertSame($step2, $steps[1]);
    }
}
