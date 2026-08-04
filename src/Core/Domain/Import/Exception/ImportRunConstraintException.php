<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\Exception;

/**
 * Thrown when an import run value does not pass domain constraints.
 */
final class ImportRunConstraintException extends ImportException
{
    public const INVALID_ID = 1;
    public const INVALID_ENTITY_TYPE = 2;
    public const INVALID_FILENAME = 3;
    public const INVALID_LANG_ISO = 4;
    public const INVALID_COLUMN_MAPPING = 5;
    public const INVALID_OPTIONS = 6;
    public const INVALID_STATUS = 7;
    public const INVALID_LIMIT = 8;
}
