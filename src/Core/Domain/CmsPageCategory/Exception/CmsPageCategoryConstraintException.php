<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception;

/**
 * Is thrown when cms page category constraints are violated
 */
class CmsPageCategoryConstraintException extends CmsPageCategoryException
{
    /**
     * @var int is used when incorrect values supplied for bulk cms categories operations
     */
    public const INVALID_BULK_DATA = 1;

    /**
     * @var int is used when cms page category is moved to the same category as it is
     *          or if it is moved to its child category
     */
    public const CANNOT_MOVE_CATEGORY_TO_PARENT = 2;

    /**
     * @var int is used to raise an error when default language is missing for the field
     */
    public const MISSING_DEFAULT_LANGUAGE_FOR_NAME = 3;

    /**
     * @var int is used to raise an error when friendly url is missing for the field
     */
    public const MISSING_DEFAULT_LANGUAGE_FOR_FRIENDLY_URL = 4;

    /**
     * @var int is used to validate category name to match the specific pattern
     */
    public const INVALID_CATEGORY_NAME = 5;

    /**
     * @var int is used to validate link rewrite that matches specific regex pattern
     */
    public const INVALID_LINK_REWRITE = 6;

    /**
     * @var int is used to validate meta title for specific regex pattern
     */
    public const INVALID_META_TITLE = 7;

    /**
     * @var int Is used to validate meta description for specific regex pattern
     */
    public const INVALID_META_DESCRIPTION = 8;

    /**
     * @var int Is used to validate description according to clean html standard/
     */
    public const INVALID_DESCRIPTION = 10;
}
