<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Import\Job;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Import\Job\ImportJobMessageLedger;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;

class ImportJobMessageLedgerTest extends TestCase
{
    public function testMessagesEqualOnEverythingButTheirRowsBecomeOne(): void
    {
        $ledger = new ImportJobMessageLedger();

        $ledger->addAll([$this->error('Invalid price', [1, 2], 'price')]);
        $ledger->addAll([$this->error('Invalid price', [7], 'price')]);

        $messages = $ledger->getMessages();

        $this->assertCount(1, $messages);
        $this->assertSame([1, 2, 7], $messages[0]->getRows());
        $this->assertSame(3, $messages[0]->getRowCount());
    }

    public function testMessagesDifferingOnAnyFieldStayApart(): void
    {
        $ledger = new ImportJobMessageLedger();

        $ledger->addAll([
            $this->error('Invalid price', [1], 'price'),
            $this->error('Invalid price', [1], 'wholesale_price'),
            $this->error('Invalid reference', [1], 'price'),
            new ImportMessage(ImportMessage::SEVERITY_WARNING, ImportPhaseDefinition::PHASE_VALIDATION, 'Invalid price', [1], 'price'),
            new ImportMessage(ImportMessage::SEVERITY_ERROR, ImportPhaseDefinition::PHASE_DATABASE, 'Invalid price', [1], 'price'),
        ]);

        $this->assertCount(5, $ledger->getMessages(), 'Severity, phase, field and text all belong to the identity');
    }

    /**
     * The row list is a sample; the count is not. A warning matching every row of a large
     * catalogue is legitimate and must not be reported as a hundred.
     */
    public function testRowsAreSampledWhileTheCountKeepsCounting(): void
    {
        $ledger = new ImportJobMessageLedger();

        for ($batch = 0; $batch < 30; ++$batch) {
            $rows = range($batch * 10, $batch * 10 + 9);
            $ledger->addAll([new ImportMessage(
                ImportMessage::SEVERITY_WARNING,
                ImportPhaseDefinition::PHASE_VALIDATION,
                'Price was rounded',
                $rows,
                'price'
            )]);
        }

        $message = $ledger->getMessages()[0];

        $this->assertCount(ImportJobMessageLedger::MAX_ROWS_PER_MESSAGE, $message->getRows());
        $this->assertSame(range(0, ImportJobMessageLedger::MAX_ROWS_PER_MESSAGE - 1), $message->getRows(), 'The sample is the first rows seen');
        $this->assertSame(300, $message->getRowCount());
        $this->assertTrue($message->hasTruncatedRows());
    }

    public function testDistinctMessagesAreCappedPerSeverityAndTheRestAreCounted(): void
    {
        $ledger = new ImportJobMessageLedger();

        for ($i = 0; $i < ImportJobMessageLedger::MAX_DISTINCT_MESSAGES_PER_SEVERITY + 12; ++$i) {
            $ledger->addAll([$this->error(sprintf('Reference "REF-%d" matches two products', $i), [$i])]);
        }
        // a different severity has its own budget
        $ledger->addAll([new ImportMessage(ImportMessage::SEVERITY_NOTICE, ImportPhaseDefinition::PHASE_VALIDATION, 'Category created', [1])]);

        $this->assertSame(
            ImportJobMessageLedger::MAX_DISTINCT_MESSAGES_PER_SEVERITY,
            $ledger->countBySeverity(ImportMessage::SEVERITY_ERROR)
        );
        $this->assertSame(['error' => 12], $ledger->getDroppedMessageCounts());
        $this->assertSame(1, $ledger->countBySeverity(ImportMessage::SEVERITY_NOTICE));
    }

    /**
     * A message already stored still accumulates once its severity is full: the cap refuses new
     * report lines, not new occurrences of a line that is already there.
     */
    public function testACappedSeverityStillAccumulatesRowsOfMessagesItAlreadyHolds(): void
    {
        $ledger = new ImportJobMessageLedger();
        for ($i = 0; $i < ImportJobMessageLedger::MAX_DISTINCT_MESSAGES_PER_SEVERITY; ++$i) {
            $ledger->addAll([$this->error(sprintf('Error %d', $i), [$i])]);
        }

        $ledger->addAll([$this->error('Error 0', [9999])]);

        $first = $ledger->getMessages()[0];
        $this->assertSame([0, 9999], $first->getRows());
        $this->assertSame([], $ledger->getDroppedMessageCounts());
    }

    public function testCountingIsRestrictedToOnePhaseForThePausePredicate(): void
    {
        $ledger = new ImportJobMessageLedger();
        $ledger->addAll([
            $this->error('Bad row', [1]),
            new ImportMessage(ImportMessage::SEVERITY_WARNING, ImportPhaseDefinition::PHASE_VALIDATION, 'Odd row', [2]),
            new ImportMessage(ImportMessage::SEVERITY_ERROR, ImportPhaseDefinition::PHASE_DATABASE, 'Write failed', [3]),
        ]);

        $validation = $ledger->countBySeverity(ImportMessage::SEVERITY_ERROR, ImportPhaseDefinition::PHASE_VALIDATION)
            + $ledger->countBySeverity(ImportMessage::SEVERITY_WARNING, ImportPhaseDefinition::PHASE_VALIDATION);

        $this->assertSame(2, $validation, 'The validation phase pauses on its own messages, not on later ones');
        $this->assertSame(1, $ledger->countBySeverity(ImportMessage::SEVERITY_ERROR, ImportPhaseDefinition::PHASE_DATABASE));
        $this->assertSame(2, $ledger->countBySeverity(ImportMessage::SEVERITY_ERROR), 'Unscoped counting spans every phase');
    }

    public function testTheWholeLedgerSurvivesBeingPersistedAndRebuilt(): void
    {
        $ledger = new ImportJobMessageLedger();
        $ledger->addAll([
            $this->error('Invalid price', [1, 2], 'price'),
            new ImportMessage(ImportMessage::SEVERITY_NOTICE, ImportPhaseDefinition::PHASE_DATABASE, 'File-level note'),
        ]);
        for ($i = 0; $i < ImportJobMessageLedger::MAX_DISTINCT_MESSAGES_PER_SEVERITY + 3; ++$i) {
            $ledger->addAll([new ImportMessage(ImportMessage::SEVERITY_WARNING, ImportPhaseDefinition::PHASE_VALIDATION, sprintf('Warning %d', $i), [$i])]);
        }

        $rebuilt = ImportJobMessageLedger::fromArray($ledger->toArray());

        $this->assertEquals($ledger->getMessages(), $rebuilt->getMessages());
        $this->assertSame($ledger->getDroppedMessageCounts(), $rebuilt->getDroppedMessageCounts());
        $this->assertSame($ledger->toArray(), $rebuilt->toArray());
    }

    public function testARebuiltLedgerKeepsAccumulatingOnTheSameKeys(): void
    {
        $stored = ImportJobMessageLedger::fromArray([
            'items' => [[
                'severity' => 'error',
                'phase' => 'validation',
                'message' => 'Invalid price',
                'field' => 'price',
                'rows' => [1],
                'rowCount' => 1,
            ]],
            'droppedMessages' => [],
        ]);

        $stored->addAll([$this->error('Invalid price', [4], 'price')]);

        $messages = $stored->getMessages();
        $this->assertCount(1, $messages, 'The key must survive the round trip or the next batch duplicates every line');
        $this->assertSame([1, 4], $messages[0]->getRows());
        $this->assertSame(2, $messages[0]->getRowCount());
    }

    public function testAnEmptyColumnRebuildsAnEmptyLedger(): void
    {
        $ledger = ImportJobMessageLedger::fromArray([]);

        $this->assertSame([], $ledger->getMessages());
        $this->assertSame(['items' => [], 'droppedMessages' => []], $ledger->toArray());
    }

    /**
     * @param list<int> $rows
     */
    private function error(string $text, array $rows, ?string $field = null): ImportMessage
    {
        return new ImportMessage(ImportMessage::SEVERITY_ERROR, ImportPhaseDefinition::PHASE_VALIDATION, $text, $rows, $field);
    }
}
