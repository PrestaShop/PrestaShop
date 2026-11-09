<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Security\Exception;

use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Thrown when a zip archive is not extracted, the code gives the reason.
 *
 * The message never holds the sandbox path: a filesystem error is kept as the previous exception.
 */
class ZipExtractionException extends CoreException
{
    /**
     * The archive is missing or is not a zip archive
     */
    public const CANNOT_OPEN_ARCHIVE = 1;

    /**
     * An entry points outside the destination or is a symbolic link
     */
    public const UNSAFE_ENTRY = 2;

    /**
     * The folder to copy is not a single folder name
     */
    public const INVALID_FOLDER = 3;

    /**
     * The folder to copy is not in the archive
     */
    public const MISSING_FOLDER = 4;

    /**
     * The extraction folder cannot be created in the sandbox
     */
    public const SANDBOX_UNAVAILABLE = 5;

    /**
     * The archive cannot be extracted or copied into the destination
     */
    public const EXTRACTION_FAILED = 6;

    /**
     * Tells whether the archive itself is refused, rather than the filesystem failing.
     */
    public function isInvalidArchive(): bool
    {
        return in_array($this->getCode(), [self::CANNOT_OPEN_ARCHIVE, self::UNSAFE_ENTRY, self::MISSING_FOLDER], true);
    }
}
