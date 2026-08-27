<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Import\Engine;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\PhaseBatchResult;

class ImportMessageTest extends TestCase
{
    public function testMessagesEqualOnEveryFieldExceptRowsAreMergedIntoOne(): void
    {
        $coalesced = ImportMessage::coalesce([
            $this->warning('Invalid date.', [3], 'available_date'),
            $this->warning('Invalid date.', [7], 'available_date'),
            $this->warning('Invalid date.', [5], 'available_date'),
        ]);

        $this->assertCount(1, $coalesced);
        $this->assertSame([3, 5, 7], $coalesced[0]->rows, 'Merged rows must be sorted ascending');
        $this->assertSame('Invalid date.', $coalesced[0]->message);
        $this->assertSame('available_date', $coalesced[0]->field);
        $this->assertSame(ImportMessage::SEVERITY_WARNING, $coalesced[0]->severity);
    }

    public function testAnyDifferingFieldPreventsTheMerge(): void
    {
        $reference = $this->warning('Invalid date.', [1], 'available_date');
        $differing = [
            new ImportMessage(ImportMessage::SEVERITY_ERROR, ImportPhaseDefinition::PHASE_VALIDATION, 'Invalid date.', [2], 'available_date'),
            new ImportMessage(ImportMessage::SEVERITY_WARNING, ImportPhaseDefinition::PHASE_DATABASE, 'Invalid date.', [2], 'available_date'),
            $this->warning('Invalid price.', [2], 'available_date'),
            $this->warning('Invalid date.', [2], 'date_add'),
            $this->warning('Invalid date.', [2], null),
        ];

        foreach ($differing as $index => $message) {
            $this->assertCount(2, ImportMessage::coalesce([$reference, $message]), sprintf('Message #%d must not merge with the reference', $index));
        }
    }

    /**
     * The coalesce key concatenates free-form strings, so its glue must not be
     * a character that can occur inside them: with a printable glue like "-",
     * these two DIFFERENT messages would share the key "…-a-b-c" and wrongly
     * merge into one report line.
     */
    public function testKeyGlueCannotCollideWithFreeFormText(): void
    {
        $coalesced = ImportMessage::coalesce([
            $this->warning('c', [1], 'a-b'),
            $this->warning('b-c', [2], 'a'),
        ]);

        $this->assertCount(2, $coalesced);
    }

    public function testFirstOccurrenceOrderIsPreservedAndRowsAreDeduplicated(): void
    {
        $coalesced = ImportMessage::coalesce([
            $this->warning('First.', [4]),
            $this->warning('Second.', [2]),
            $this->warning('First.', [4, 0]),
        ]);

        $this->assertSame(['First.', 'Second.'], array_map(static fn (ImportMessage $message): string => $message->message, $coalesced));
        $this->assertSame([0, 4], $coalesced[0]->rows);
        $this->assertSame([2], $coalesced[1]->rows);
    }

    public function testFileLevelMessagesMergeIntoOneFileLevelMessage(): void
    {
        $coalesced = ImportMessage::coalesce([
            $this->warning('The accessories will be dropped.', []),
            $this->warning('The accessories will be dropped.', []),
        ]);

        $this->assertCount(1, $coalesced);
        $this->assertSame([], $coalesced[0]->rows, 'Merging file-level messages must keep them file-level');
    }

    public function testPhaseBatchResultCoalescesItsMessages(): void
    {
        $result = new PhaseBatchResult(
            2,
            [
                $this->warning('Invalid date.', [3], 'available_date'),
                $this->warning('Invalid date.', [4], 'available_date'),
            ],
            [],
            'cursor'
        );

        $this->assertCount(1, $result->messages);
        $this->assertSame([3, 4], $result->messages[0]->rows);
    }

    /**
     * @param list<int> $rows
     */
    private function warning(string $message, array $rows, ?string $field = null): ImportMessage
    {
        return new ImportMessage(ImportMessage::SEVERITY_WARNING, ImportPhaseDefinition::PHASE_VALIDATION, $message, $rows, $field);
    }
}
