<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\Exception;

/**
 * Thrown when an import run's persisted state cannot be updated (e.g. advancing the offset or
 * stashing the shared data after a batch).
 */
final class CannotUpdateImportRunException extends ImportException
{
}
