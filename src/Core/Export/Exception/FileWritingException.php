<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Export\Exception;

/**
 * Is thrown when cannot export due to lacking write permissions
 */
class FileWritingException extends ExportException
{
    /**
     * When file cannot be opened for writing
     */
    public const CANNOT_OPEN_FILE_FOR_WRITING = 10;
}
