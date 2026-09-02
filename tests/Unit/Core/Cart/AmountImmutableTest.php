<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Cart\AmountImmutable;
use Tests\Resources\TestCase\ExtendedTestCaseMethodsTrait;

class AmountImmutableTest extends TestCase
{
    use ExtendedTestCaseMethodsTrait;

    public function testGet(): void
    {
        $amount = new AmountImmutable(2.3, 3.5);

        $this->assertEquals(2.3, $amount->getTaxIncluded());
        $this->assertEquals(3.5, $amount->getTaxExcluded());
    }

    public function testAdd(): void
    {
        $amount = new AmountImmutable(2.3, 3.5);
        $amount1 = new AmountImmutable(4.6, 7.2);
        $amount2 = $amount->add($amount1);

        $this->assertEquals(2.3, $amount->getTaxIncluded());
        $this->assertEquals(3.5, $amount->getTaxExcluded());

        $this->assertEquals(4.6, $amount1->getTaxIncluded());
        $this->assertEquals(7.2, $amount1->getTaxExcluded());

        $this->assertEqualsWithEpsilon(6.9, $amount2->getTaxIncluded());
        $this->assertEquals(10.7, $amount2->getTaxExcluded());
    }

    public function testSub(): void
    {
        $amount = new AmountImmutable(2.3, 3.5);
        $amount1 = new AmountImmutable(4.8, 7.2);
        $amount2 = $amount1->sub($amount);

        $this->assertEquals(2.3, $amount->getTaxIncluded());
        $this->assertEquals(3.5, $amount->getTaxExcluded());

        $this->assertEquals(4.8, $amount1->getTaxIncluded());
        $this->assertEquals(7.2, $amount1->getTaxExcluded());

        $this->assertEquals(2.5, $amount2->getTaxIncluded());
        $this->assertEquals(3.7, $amount2->getTaxExcluded());
    }
}
