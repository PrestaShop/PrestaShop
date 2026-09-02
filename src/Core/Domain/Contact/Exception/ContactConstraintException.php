<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Contact\Exception;

/**
 * holds all validation constraints that are used together with contact entity.
 */
class ContactConstraintException extends ContactException
{
    /**
     * @var int - error is raised when preg match fails to validate according to regex /^[^<>{}]*$/u
     */
    public const INVALID_TITLE = 1;

    /**
     * @var int - error is raised when a value in array is not integer type
     */
    public const INVALID_SHOP_ASSOCIATION = 2;

    /**
     * @var int - error is raised when CleanHtml constraint validation fails
     */
    public const INVALID_DESCRIPTION = 3;

    /**
     * @var int - error is raised when an array does not have the default language value. It might not exist or is empty.
     *          DefaultLanguage constraint is used here.
     */
    public const MISSING_TITLE_FOR_DEFAULT_LANGUAGE = 4;
}
