<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Language\Exception;

/**
 * Is thrown when error occurs while copying "No picture" for language
 */
class CopyingNoPictureException extends LanguageException
{
    /**
     * @var int Code is used when error occurs while copying "No picture" image to products directory
     */
    public const PRODUCT_IMAGE_COPY_ERROR = 1;

    /**
     * @var int Code is used when error occurs while copying "No picture" image to categories directory
     */
    public const CATEGORY_IMAGE_COPY_ERROR = 2;

    /**
     * @var int Code is used when error occurs while copying "No picture" image to brands (manufacturers) directory
     */
    public const BRAND_IMAGE_COPY_ERROR = 3;
}
