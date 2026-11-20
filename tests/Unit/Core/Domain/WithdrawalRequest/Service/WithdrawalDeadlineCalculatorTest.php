<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\WithdrawalRequest\Service;

use DateTimeImmutable;
use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Exception\WithdrawalRequestConstraintException;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Service\WithdrawalDeadlineCalculator;

class WithdrawalDeadlineCalculatorTest extends TestCase
{
    private WithdrawalDeadlineCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new WithdrawalDeadlineCalculator();
    }

    /**
     * @dataProvider getDeadlineProvider
     */
    public function testGetDeadlineWithDeliveryDateReturnsEndOfLastDay(string $deliveredAt, int $periodDays, string $expectedDeadline): void
    {
        $deadline = $this->calculator->getDeadline(new DateTimeImmutable($deliveredAt), $periodDays);

        $this->assertNotNull($deadline);
        $this->assertSame($expectedDeadline, $deadline->format('Y-m-d H:i:s'));
    }

    public static function getDeadlineProvider(): Generator
    {
        // Delivered on Monday 2026-11-02: the 14 days run from Tuesday 3rd to Monday 16th included
        yield 'delivered on a monday morning' => ['2026-11-02 08:15:00', 14, '2026-11-16 23:59:59'];
        yield 'delivery time is not relevant' => ['2026-11-02 23:59:00', 14, '2026-11-16 23:59:59'];
        // Delivered on Saturday 2026-11-07: the 14th day is Saturday 21st, extended to Monday 23rd
        yield 'last day is a saturday' => ['2026-11-07 10:00:00', 14, '2026-11-23 23:59:59'];
        // Delivered on Sunday 2026-11-08: the 14th day is Sunday 22nd, extended to Monday 23rd
        yield 'last day is a sunday' => ['2026-11-08 10:00:00', 14, '2026-11-23 23:59:59'];
        yield 'longer commercial period' => ['2026-11-02 10:00:00', 30, '2026-12-02 23:59:59'];
        yield 'period across the end of the year' => ['2026-12-24 10:00:00', 14, '2027-01-07 23:59:59'];
    }

    public function testGetDeadlineWithoutDeliveryDateReturnsNull(): void
    {
        $this->assertNull($this->calculator->getDeadline(null, 14));
    }

    /**
     * @dataProvider invalidPeriodProvider
     */
    public function testGetDeadlineWithInvalidPeriodThrowsException(int $periodDays): void
    {
        $this->expectException(WithdrawalRequestConstraintException::class);
        $this->expectExceptionCode(WithdrawalRequestConstraintException::INVALID_PERIOD);

        $this->calculator->getDeadline(new DateTimeImmutable('2026-11-02'), $periodDays);
    }

    public static function invalidPeriodProvider(): Generator
    {
        yield 'zero' => [0];
        yield 'negative' => [-14];
    }

    /**
     * @dataProvider isWithinPeriodProvider
     */
    public function testIsWithinPeriodReturnsExpectedResult(?string $deliveredAt, string $now, bool $expected): void
    {
        $this->assertSame(
            $expected,
            $this->calculator->isWithinPeriod(
                null === $deliveredAt ? null : new DateTimeImmutable($deliveredAt),
                14,
                new DateTimeImmutable($now)
            )
        );
    }

    public static function isWithinPeriodProvider(): Generator
    {
        yield 'not delivered yet' => [null, '2030-01-01 00:00:00', true];
        yield 'on delivery day' => ['2026-11-02 08:00:00', '2026-11-02 18:00:00', true];
        yield 'last second of the last day' => ['2026-11-02 08:00:00', '2026-11-16 23:59:59', true];
        yield 'day after the last day' => ['2026-11-02 08:00:00', '2026-11-17 00:00:00', false];
        yield 'weekend extension' => ['2026-11-07 08:00:00', '2026-11-23 12:00:00', true];
    }
}
