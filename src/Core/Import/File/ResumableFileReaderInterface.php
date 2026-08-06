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
 * The cursor is a string because it is persisted verbatim (the future
 * ImportRun entity stores it in a resume_cursor varchar column) and handed
 * back untouched: the engine never interprets it. Each reader encodes
 * whatever state it needs INTO the string — the CSV reader uses a byte
 * offset (O(1) fseek resume), a future reader may encode richer state
 * (e.g. JSON with a split-file index and an item offset).
 *
 * Standalone interface: it deliberately does not extend the deprecated
 * FileReaderInterface, so the whole legacy DataRow-based reading layer can
 * be removed in the next major.
 */
interface ResumableFileReaderInterface
{
    /**
     * Reads records starting at the given cursor (or from the beginning when null).
     *
     * The generator yields the cursor resuming AFTER the record as key, and
     * the record as a plain list of string cell values: the caller's batch
     * cursor is simply the key of the last record it consumed.
     *
     * @return Generator<string, array<int, string>>
     *
     * @throws UnreadableFileException
     * @throws InvalidResumeCursorException
     */
    public function readFrom(SplFileInfo $file, ?string $cursor = null): Generator;
}
