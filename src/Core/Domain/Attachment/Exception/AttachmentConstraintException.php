<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\Exception;

/**
 * Exception is thrown when Attachment constraint is violated
 */
class AttachmentConstraintException extends AttachmentException
{
    public const INVALID_ID = 1;

    public const INVALID_FILE_SIZE = 2;

    public const EMPTY_NAME = 3;

    public const EMPTY_DESCRIPTION = 4;

    public const INVALID_FIELDS = 5;

    public const INVALID_DESCRIPTION = 6;

    public const MISSING_NAME_IN_DEFAULT_LANGUAGE = 7;
}
