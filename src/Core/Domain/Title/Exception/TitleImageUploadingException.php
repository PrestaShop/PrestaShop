<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Title\Exception;

/**
 * Is thrown when error occurs when uploading title image
 */
class TitleImageUploadingException extends TitleException
{
    /**
     * @var int Code is used when there are less memory than needed to upload image
     */
    public const MEMORY_LIMIT_RESTRICTION = 1;

    /**
     * @var int Code is used when unexpected error occurs while uploading image
     */
    public const UNEXPECTED_ERROR = 2;
}
