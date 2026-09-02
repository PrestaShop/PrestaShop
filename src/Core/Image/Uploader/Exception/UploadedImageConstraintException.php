<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Image\Uploader\Exception;

use PrestaShop\PrestaShop\Core\Image\Exception\ImageException;

class UploadedImageConstraintException extends ImageException
{
    public const EXCEEDED_SIZE = 1;
    public const UNRECOGNIZED_FORMAT = 2;
    public const UNKNOWN_ERROR = 4;
}
