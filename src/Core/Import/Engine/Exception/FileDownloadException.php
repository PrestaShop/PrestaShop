<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\Exception;

/**
 * Thrown when a remote or local file referenced by the import data cannot be
 * fetched into a local temporary file.
 */
class FileDownloadException extends ImportEngineException
{
}
