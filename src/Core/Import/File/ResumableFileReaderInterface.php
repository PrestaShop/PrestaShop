<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\File;

use Generator;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\InvalidResumeCursorException;
use PrestaShop\PrestaShop\Core\Import\Exception\UnreadableFileException;
use SplFileInfo;

/**
 * A file reader able to resume reading from an opaque cursor.
 *
 * The cursor is reader-specific and must never be interpreted by callers:
 * the CSV reader uses a byte offset (O(1) fseek resume), a future JSON
 * reader may use a split-file/item index. The caller persists the last
 * consumed cursor and hands it back to resume the next batch.
 */
interface ResumableFileReaderInterface extends FileReaderInterface
{
    /**
     * Reads rows starting at the given cursor (or from the beginning when null).
     *
     * The generator yields the cursor resuming AFTER the row as key, and the
     * row as value: the caller's batch cursor is simply the key of the last
     * row it consumed.
     *
     * @return Generator<string, DataRow\DataRowInterface>
     *
     * @throws UnreadableFileException
     * @throws InvalidResumeCursorException
     */
    public function readFrom(SplFileInfo $file, ?string $cursor = null): Generator;
}
