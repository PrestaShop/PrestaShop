<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Import\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportJobConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\BatchLimit;

class BatchLimitTest extends TestCase
{
    public function testItAcceptsTheWholeRangeIncludingBothBounds(): void
    {
        $this->assertSame(BatchLimit::MIN_VALUE, (new BatchLimit(BatchLimit::MIN_VALUE))->getValue());
        $this->assertSame(250, (new BatchLimit(250))->getValue());
        $this->assertSame(BatchLimit::MAX_VALUE, (new BatchLimit(BatchLimit::MAX_VALUE))->getValue());
    }

    public function testABudgetThatCannotAdvanceTheJobIsRefused(): void
    {
        $this->expectException(ImportJobConstraintException::class);
        $this->expectExceptionCode(ImportJobConstraintException::INVALID_BATCH_LIMIT);

        new BatchLimit(0);
    }

    /**
     * A caller asking for the whole file in one call only trades the bound for a request timeout.
     */
    public function testABudgetAboveTheCapIsRefused(): void
    {
        $this->expectException(ImportJobConstraintException::class);
        $this->expectExceptionCode(ImportJobConstraintException::INVALID_BATCH_LIMIT);

        new BatchLimit(BatchLimit::MAX_VALUE + 1);
    }
}
