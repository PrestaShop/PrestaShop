<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Search;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Search\Pagination;

class PaginationTest extends TestCase
{
    /**
     * @dataProvider provideOffsets
     */
    public function testItRecognisesAnOffsetPastTheLastRecord(int $recordsTotal, int $offset, bool $expected): void
    {
        self::assertSame($expected, Pagination::isOffsetOutOfRange($recordsTotal, $offset));
    }

    public function provideOffsets(): iterable
    {
        yield 'first page always in range' => [0, 0, false];
        yield 'first page of a full grid' => [11, 0, false];
        yield 'second page while it holds records' => [11, 10, false];
        yield 'second page once the 11th record is gone' => [10, 10, true];
        yield 'far past the end' => [3, 50, true];
        yield 'empty grid, first page' => [0, 0, false];
    }

    /**
     * @dataProvider provideValidOffsets
     */
    public function testItFallsBackToTheLastPageHoldingRecords(int $recordsTotal, int $limit, int $expected): void
    {
        self::assertSame($expected, Pagination::computeValidOffset($recordsTotal, $limit));
    }

    public function provideValidOffsets(): iterable
    {
        yield '11 records, 10 per page -> second page' => [11, 10, 10];
        yield '10 records, 10 per page -> first page' => [10, 10, 0];
        yield '21 records, 10 per page -> third page' => [21, 10, 20];
        yield 'exactly one page' => [3, 10, 0];
        yield 'no records left' => [0, 10, 0];
        yield 'a limit of zero cannot paginate' => [10, 0, 0];
    }

    /**
     * The recursion in DoctrineGridDataFactory relies on this: the offset it falls back to must never be
     * out of range itself, or the grid would keep asking for another one.
     *
     * @dataProvider provideTotalsAndLimits
     */
    public function testTheFallbackOffsetIsNeverOutOfRangeItself(int $recordsTotal, int $limit): void
    {
        $offset = Pagination::computeValidOffset($recordsTotal, $limit);

        self::assertFalse(
            Pagination::isOffsetOutOfRange($recordsTotal, $offset),
            sprintf('offset %d is out of range for %d records', $offset, $recordsTotal)
        );
    }

    public function provideTotalsAndLimits(): iterable
    {
        foreach ([0, 1, 3, 10, 11, 21, 99, 100] as $total) {
            foreach ([1, 10, 20, 50, 300] as $limit) {
                yield sprintf('%d records, %d per page', $total, $limit) => [$total, $limit];
            }
        }
    }
}
