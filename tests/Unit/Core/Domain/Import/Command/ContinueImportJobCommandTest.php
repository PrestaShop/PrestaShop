<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Import\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Import\Command\CancelImportJobCommand;
use PrestaShop\PrestaShop\Core\Domain\Import\Command\ContinueImportJobCommand;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportJobConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\BatchLimit;

class ContinueImportJobCommandTest extends TestCase
{
    private const UUID = '0198f1a4-0b3c-7c21-9a4e-1f2b3c4d5e6f';

    public function testTheBudgetIsOptionalAndFallsBackToTheFrozenBatchLimit(): void
    {
        $command = new ContinueImportJobCommand(self::UUID);

        $this->assertSame(self::UUID, $command->getImportJobUuid()->getValue());
        $this->assertNull($command->getBatchLimit(), 'Null is what tells the handler to use the job option');
    }

    public function testACallerCanSizeItsOwnBatch(): void
    {
        $this->assertSame(25, (new ContinueImportJobCommand(self::UUID, 25))->getBatchLimit());
    }

    public function testItRefusesABudgetThatCannotAdvanceTheJob(): void
    {
        $this->expectException(ImportJobConstraintException::class);
        $this->expectExceptionCode(ImportJobConstraintException::INVALID_BATCH_LIMIT);

        new ContinueImportJobCommand(self::UUID, 0);
    }

    public function testItRefusesABudgetAboveTheCap(): void
    {
        $this->assertSame(BatchLimit::MAX_VALUE, (new ContinueImportJobCommand(self::UUID, BatchLimit::MAX_VALUE))->getBatchLimit());

        $this->expectException(ImportJobConstraintException::class);
        $this->expectExceptionCode(ImportJobConstraintException::INVALID_BATCH_LIMIT);

        new ContinueImportJobCommand(self::UUID, BatchLimit::MAX_VALUE + 1);
    }

    public function testItRefusesAMalformedJobIdentifier(): void
    {
        $this->expectException(ImportJobConstraintException::class);
        $this->expectExceptionCode(ImportJobConstraintException::INVALID_UUID);

        new ContinueImportJobCommand('7');
    }

    public function testCancellingTakesTheSameIdentifier(): void
    {
        $this->assertSame(self::UUID, (new CancelImportJobCommand(self::UUID))->getImportJobUuid()->getValue());
    }

    public function testCancellingRefusesAMalformedIdentifier(): void
    {
        $this->expectException(ImportJobConstraintException::class);
        $this->expectExceptionCode(ImportJobConstraintException::INVALID_UUID);

        new CancelImportJobCommand('');
    }
}
