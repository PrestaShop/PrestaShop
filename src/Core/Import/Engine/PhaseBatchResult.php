<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine;

/**
 * Outcome of one processPhaseBatch() call. Importers return it, the caller
 * (batch sequencer) applies it to the run context — importers never mutate
 * the context themselves.
 */
class PhaseBatchResult
{
    /**
     * @param int $processedUnitCount units consumed by this batch (skipped rows still consume one unit)
     * @param list<ImportMessage> $messages
     * @param list<int> $newlySkippedRows 0-based data-record indexes newly marked as skipped
     * @param string|null $resumeCursor opaque reader cursor to resume the next batch from.
     *                                  DELIBERATELY NOT OPTIONAL: applyBatchResult() stores it
     *                                  verbatim, so a batch that consumed nothing must hand back
     *                                  the cursor it was given (ImportRunContext::getResumeCursor()).
     *                                  Defaulting it to null would let a plausible
     *                                  `new PhaseBatchResult(0)` rewind the reader to the start of
     *                                  the file while the phase offset stays put — the rows after
     *                                  it would then be imported twice, under the wrong indexes.
     */
    public function __construct(
        public readonly int $processedUnitCount,
        public readonly array $messages,
        public readonly array $newlySkippedRows,
        public readonly ?string $resumeCursor,
    ) {
    }
}
