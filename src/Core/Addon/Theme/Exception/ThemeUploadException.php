<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Addon\Theme\Exception;

use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Class ThemeUploadException
 */
class ThemeUploadException extends CoreException
{
    public const FILE_SIZE_EXCEEDED_ERROR = 1;
    public const UNKNOWN_ERROR = 2;
    public const INVALID_MIME_TYPE = 3;
    public const FAILED_TO_MOVE_FILE = 4;
}
