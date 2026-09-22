<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\Exception;

/**
 * Thrown when an import job value fails a shape constraint, without touching the environment.
 *
 * Whether the shape describes something that exists — a registered importer, a readable file, a
 * usable shop — is the Start handler's business ({@see CannotStartImportJobException}).
 */
final class ImportJobConstraintException extends ImportException
{
    public const INVALID_ID = 1;
    public const INVALID_ENTITY_TYPE = 2;
    public const INVALID_SOURCE_PATH = 3;
    public const INVALID_LANG_ISO = 4;
    public const INVALID_COLUMN_MAPPING = 5;
    public const INVALID_CSV_SEPARATOR = 6;
    public const INVALID_MULTIPLE_VALUE_SEPARATOR = 7;
    public const INVALID_SKIP_ROWS = 8;
    public const INVALID_BATCH_LIMIT = 9;
    public const INVALID_EXPIRATION_DATE = 10;
    public const INVALID_FILE_NAME = 11;
}
