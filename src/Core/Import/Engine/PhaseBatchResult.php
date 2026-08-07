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
     * @param list<int> $newlySkippedRows 0-based physical row indexes newly marked as skipped
     * @param string|null $resumeCursor opaque reader cursor to resume the next batch from
     */
    public function __construct(
        public readonly int $processedUnitCount,
        public readonly array $messages = [],
        public readonly array $newlySkippedRows = [],
        public readonly ?string $resumeCursor = null,
    ) {
    }
}
