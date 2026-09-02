<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\Exception;

/**
 * Thrown when category image was attempted to delete but failed.
 */
class CannotDeleteImageException extends CategoryException
{
    /**
     * Error codes to specify which type of image were not deleted.
     */
    public const COVER_IMAGE = 1;
    public const THUMBNAIL_IMAGE = 2;
}
