<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\Exception;

/**
 * Class CategoryConstraintException.
 */
class CategoryConstraintException extends CategoryException
{
    /**
     * Code is used when category does not have name.
     */
    public const EMPTY_NAME = 1;

    /**
     * Code is used when category does not have link rewrite.
     */
    public const EMPTY_LINK_REWRITE = 2;

    /**
     * Code is used when invalid status is set to category.
     */
    public const INVALID_STATUS = 4;

    /**
     * Code is used when invalid delete mode is used to delete a category.
     */
    public const INVALID_DELETE_MODE = 5;

    /**
     * Code is used when invalid parent id is supplied.
     */
    public const INVALID_PARENT_ID = 6;

    /**
     * Code is used when invalid id is supplied.
     */
    public const INVALID_ID = 10;

    /**
     * Code is used when performing bulk delete of categories with empty data.
     */
    public const EMPTY_BULK_DELETE_DATA = 12;

    /**
     * When category redirect type is invalid
     */
    public const INVALID_REDIRECT_TYPE = 14;

    /**
     * When category redirect target is invalid
     */
    public const INVALID_REDIRECT_TARGET = 15;
}
