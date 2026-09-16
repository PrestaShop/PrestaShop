<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Import\Engine;

use LogicException;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportJobContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportJobOptions;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\PhaseBatchResult;

/**
 * Progress bookkeeping of the job context: what a batch adds, what entering a
 * phase rewinds, and what resuming a persisted job puts back.
 *
 * The resume path is the reason this test exists. A job lives across HTTP
 * requests, so everything the next request knows about the previous one comes
 * from the database through restoreProgress(); anything that method fails to
 * replay is silently lost, and the skipped rows are the half where the loss is
 * invisible until rows validation rejected get imported anyway.
 */
class ImportJobContextTest extends TestCase
{
    private const RECORD_COUNT = 10;

    public function testANewContextStartsWithNoPhaseAndNoProgress(): void
    {
        $context = $this->buildContext();

        $this->assertNull($context->getCurrentPhaseId());
        $this->assertSame(0, $context->getCurrentPhaseTotalUnits());
        $this->assertSame(0, $context->getCurrentOffset());
        $this->assertNull($context->getResumeCursor());
        $this->assertSame([], $context->getSkippedRows());
    }

    public function testABatchResultAdvancesTheOffsetAndAccumulatesSkippedRows(): void
    {
        $context = $this->buildContext();
        $context->enterPhase(ImportPhaseDefinition::PHASE_VALIDATION, self::RECORD_COUNT);

        $context->applyBatchResult(new PhaseBatchResult(2, [], [1], 'cursor-2'));
        $context->applyBatchResult(new PhaseBatchResult(2, [], [3], 'cursor-4'));

        $this->assertSame(4, $context->getCurrentOffset(), 'Offsets accumulate; the result carries a count, not a position');
        $this->assertSame('cursor-4', $context->getResumeCursor(), 'The latest cursor wins');
        $this->assertSame([1, 3], $context->getSkippedRows());
    }

    public function testABatchResultCannotBeAppliedBeforeEnteringAPhase(): void
    {
        $this->expectException(LogicException::class);

        $this->buildContext()->applyBatchResult(new PhaseBatchResult(1, [], [], null));
    }

    /**
     * Skipped rows are deliberately NOT namespaced by phase: the phases after
     * validation ask whether a row is dead, never which phase killed it (the
     * phase is recorded on the error message that row produced).
     */
    public function testSkippedRowsSurviveAPhaseChangeWhileTheReaderPositionRewinds(): void
    {
        $context = $this->buildContext();
        $context->enterPhase(ImportPhaseDefinition::PHASE_VALIDATION, self::RECORD_COUNT);
        $context->applyBatchResult(new PhaseBatchResult(4, [], [1, 2], 'cursor-4'));

        $context->enterPhase(ImportPhaseDefinition::PHASE_DATABASE, self::RECORD_COUNT);

        $this->assertSame(0, $context->getCurrentOffset(), 'A phase starts at the beginning of the file');
        $this->assertNull($context->getResumeCursor());
        $this->assertSame([1, 2], $context->getSkippedRows(), 'The database phase must still know which rows validation rejected');
        $this->assertTrue($context->isRowSkipped(1));

        $context->applyBatchResult(new PhaseBatchResult(4, [], [5], 'cursor-4-again'));

        $this->assertSame([1, 2, 5], $context->getSkippedRows(), 'Later phases add to the set instead of replacing it');
    }

    /**
     * The defect the flattening fixed: a job rebuilt from its database row
     * could replay the offset and the cursor but not the rows validation had
     * rejected, so a job resuming into the database phase re-imported rows it
     * had already refused.
     */
    public function testRestoringAPersistedJobReplaysEveryPieceOfProgress(): void
    {
        $context = $this->buildContext();

        $context->restoreProgress(ImportPhaseDefinition::PHASE_DATABASE, self::RECORD_COUNT, 6, 'byte-offset-420', [2, 4]);

        $this->assertSame(ImportPhaseDefinition::PHASE_DATABASE, $context->getCurrentPhaseId());
        $this->assertSame(self::RECORD_COUNT, $context->getCurrentPhaseTotalUnits());
        $this->assertSame(6, $context->getCurrentOffset(), 'A resumed phase must NOT rewind, unlike enterPhase()');
        $this->assertSame('byte-offset-420', $context->getResumeCursor());

        $this->assertSame([2, 4], $context->getSkippedRows());
        $this->assertTrue($context->isRowSkipped(2));
        $this->assertTrue($context->isRowSkipped(4));
        $this->assertFalse($context->isRowSkipped(3));

        // and the restored job keeps accumulating from there
        $context->applyBatchResult(new PhaseBatchResult(2, [], [7], 'byte-offset-900'));

        $this->assertSame(8, $context->getCurrentOffset());
        $this->assertSame([2, 4, 7], $context->getSkippedRows());
    }

    public function testRestoringReplacesTheSkippedRowsInsteadOfMergingThem(): void
    {
        $context = $this->buildContext();
        $context->enterPhase(ImportPhaseDefinition::PHASE_VALIDATION, self::RECORD_COUNT);
        $context->applyBatchResult(new PhaseBatchResult(1, [], [9], 'stale'));

        // the stored set is the whole truth: a context is normally fresh here
        // (one per request), and merging would let a discarded slice's rows
        // survive a job that was reloaded precisely to forget them
        $context->restoreProgress(ImportPhaseDefinition::PHASE_DATABASE, self::RECORD_COUNT, 3, null, [0]);

        $this->assertSame([0], $context->getSkippedRows());
        $this->assertFalse($context->isRowSkipped(9));
    }

    public function testRestoredSkippedRowsAreReturnedSortedAndDeduplicated(): void
    {
        $context = $this->buildContext();

        $context->restoreProgress(ImportPhaseDefinition::PHASE_ASSOCIATION, self::RECORD_COUNT, 0, null, [7, 1, 7, 3]);

        $this->assertSame([1, 3, 7], $context->getSkippedRows(), 'The set is keyed by row index, so duplicates collapse and order is restored');
    }

    private function buildContext(): ImportJobContext
    {
        return new ImportJobContext(
            'product',
            '/tmp/does-not-need-to-exist.csv',
            self::RECORD_COUNT,
            'en',
            ',',
            ['name', 'reference'],
            ImportJobOptions::fromArray([]),
            ShopConstraint::shop(1)
        );
    }
}
