<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\Exception;

/**
 * Thrown when a well-formed StartImportJobCommand cannot be honoured by this shop.
 *
 * All environment facts the command cannot know. Failing once at start beats one error per row for
 * a job that was never viable.
 */
final class CannotStartImportJobException extends ImportException
{
    public const UNKNOWN_ENTITY_TYPE = 1;
    public const EMPTY_SHOP_SCOPE = 2;
    public const UNSUPPORTED_SHOP_SCOPE = 3;
    public const UNSUPPORTED_TRUNCATE = 4;
    public const UNSUPPORTED_DRY_RUN = 5;
    public const UNKNOWN_LANGUAGE = 6;
    public const SOURCE_FILE_NOT_FOUND = 7;
    public const SOURCE_FILE_OUT_OF_BOUNDS = 8;
    public const EMPTY_SOURCE_FILE = 9;
}
