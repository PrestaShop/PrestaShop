<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\File\Exception;

/**
 * Thrown when file is invalid
 */
class InvalidFileException extends FileException
{
    /**
     * When file size is too big
     */
    public const INVALID_SIZE = 10;
}
