<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Import\QueryResult;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Import\QueryResult\ImportJobMessage;
use PrestaShop\PrestaShop\Core\Domain\Import\QueryResult\ImportJobPhaseState;
use PrestaShop\PrestaShop\Core\Domain\Import\QueryResult\ImportJobState;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportJobStatus;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * The only logic in the read shape is how progress is derived.
 */
class ImportJobStateTest extends TestCase
{
    public function testAJobThatHasNotStartedReportsNoProgress(): void
    {
        $state = $this->buildState(null, [
            new ImportJobPhaseState('validation', 'Validation', true, 0, 0),
            new ImportJobPhaseState('database', 'Import', false, 0, 0),
        ]);

        $this->assertSame(0, $state->getProgressPercent());
    }

    public function testEachPhaseWeighsTheSameAndTheCurrentOneContributesItsFraction(): void
    {
        $state = $this->buildState('database', [
            new ImportJobPhaseState('validation', 'Validation', true, 200, 200),
            new ImportJobPhaseState('database', 'Import', false, 200, 100),
            new ImportJobPhaseState('association', 'Associations', false, 0, 0),
        ]);

        // one phase done, the second half way: (1 + 0.5) / 3
        $this->assertSame(50, $state->getProgressPercent());
        $this->assertSame(50, $state->getPhases()[1]->getProgressPercent());
    }

    public function testTheFirstPhaseAloneDoesNotInflateProgress(): void
    {
        $state = $this->buildState('validation', [
            new ImportJobPhaseState('validation', 'Validation', true, 200, 100),
            new ImportJobPhaseState('database', 'Import', false, 0, 0),
        ]);

        $this->assertSame(25, $state->getProgressPercent());
    }

    public function testAFinishedJobIsCompleteWhateverTheOffsetsSay(): void
    {
        $state = $this->buildState('database', [
            new ImportJobPhaseState('validation', 'Validation', true, 200, 200),
            // a zero-unit phase is skipped, so its offset never moves
            new ImportJobPhaseState('database', 'Import', false, 0, 0),
        ], ImportJobStatus::FINISHED);

        $this->assertSame(100, $state->getProgressPercent());
    }

    public function testAJobWithNoPhaseAtAllDoesNotDivideByZero(): void
    {
        $this->assertSame(0, $this->buildState(null, [])->getProgressPercent());
    }

    public function testAMessageKnowsWhenItsRowListIsOnlyASample(): void
    {
        $sampled = new ImportJobMessage('warning', 'validation', 'Price was rounded', 'price', [1, 2, 3], 23402);
        $whole = new ImportJobMessage('error', 'database', 'Product could not be created', null, [4], 1);

        $this->assertTrue($sampled->hasTruncatedRows());
        $this->assertSame(23402, $sampled->getRowCount());
        $this->assertFalse($whole->hasTruncatedRows());
    }

    /**
     * @param list<ImportJobPhaseState> $phases
     */
    private function buildState(
        ?string $currentPhaseId,
        array $phases,
        ImportJobStatus $status = ImportJobStatus::RUNNING
    ): ImportJobState {
        return new ImportJobState(
            '0198f1a4-0b3c-7c21-9a4e-1f2b3c4d5e6f',
            'product',
            $status,
            $currentPhaseId,
            $phases,
            200,
            0,
            [],
            [],
            ['langIso' => 'en'],
            ['truncate' => false],
            ShopConstraint::shop(1),
            new DateTimeImmutable('2026-09-17 10:00:00'),
            new DateTimeImmutable('2026-09-17 10:05:00')
        );
    }
}
