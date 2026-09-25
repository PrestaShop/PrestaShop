<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\View;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\View\DynamicDateRangeComputer;
use PrestaShop\PrestaShop\Core\Grid\View\DynamicDateRule;

class DynamicDateRangeComputerTest extends TestCase
{
    private DynamicDateRangeComputer $computer;

    protected function setUp(): void
    {
        $this->computer = new DynamicDateRangeComputer();
    }

    /**
     * @dataProvider getRuleExpectations
     */
    public function testItComputesRanges(DynamicDateRule $rule, ?int $lastDaysCount, ?array $expectedRange): void
    {
        $now = new DateTimeImmutable('2026-08-05 15:30:00');

        $this->assertSame($expectedRange, $this->computer->compute($rule, $lastDaysCount, $now));
    }

    public static function getRuleExpectations(): iterable
    {
        yield 'keep as is computes nothing' => [DynamicDateRule::KEEP_AS_IS, null, null];
        yield 'today' => [DynamicDateRule::TODAY, null, ['from' => '2026-08-05', 'to' => '2026-08-05']];
        yield 'yesterday' => [DynamicDateRule::YESTERDAY, null, ['from' => '2026-08-04', 'to' => '2026-08-04']];
        yield 'current week starts on monday' => [DynamicDateRule::CURRENT_WEEK, null, ['from' => '2026-08-03', 'to' => '2026-08-05']];
        yield 'current month' => [DynamicDateRule::CURRENT_MONTH, null, ['from' => '2026-08-01', 'to' => '2026-08-05']];
        yield 'current quarter' => [DynamicDateRule::CURRENT_QUARTER, null, ['from' => '2026-07-01', 'to' => '2026-08-05']];
        yield 'current semester' => [DynamicDateRule::CURRENT_SEMESTER, null, ['from' => '2026-07-01', 'to' => '2026-08-05']];
        yield 'current year' => [DynamicDateRule::CURRENT_YEAR, null, ['from' => '2026-01-01', 'to' => '2026-08-05']];
        yield 'last days' => [DynamicDateRule::LAST_DAYS, 10, ['from' => '2026-07-26', 'to' => '2026-08-05']];
        yield 'last days without count computes nothing' => [DynamicDateRule::LAST_DAYS, null, null];
        yield 'last days with invalid count computes nothing' => [DynamicDateRule::LAST_DAYS, 0, null];
    }

    public function testItComputesFirstSemesterAndQuarter(): void
    {
        $now = new DateTimeImmutable('2026-02-15 08:00:00');

        $this->assertSame(
            ['from' => '2026-01-01', 'to' => '2026-02-15'],
            $this->computer->compute(DynamicDateRule::CURRENT_QUARTER, null, $now)
        );
        $this->assertSame(
            ['from' => '2026-01-01', 'to' => '2026-02-15'],
            $this->computer->compute(DynamicDateRule::CURRENT_SEMESTER, null, $now)
        );
    }
}
