<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception;

/**
 * When attribute group Id contains invalid values
 */
class AttributeGroupConstraintException extends AttributeGroupException
{
    /**
     * Used when attribute group id is invalid
     */
    public const INVALID_ID = 10;

    /**
     * Code is used when attribute group does not have name.
     */
    public const EMPTY_NAME = 20;

    /**
     * Code is used when attribute group does not have public name.
     */
    public const EMPTY_PUBLIC_NAME = 30;

    /**
     * Used when attribute group name is invalid.
     */
    public const INVALID_NAME = 40;

    /**
     * Used when attribute group public name is invalid.
     */
    public const INVALID_PUBLIC_NAME = 50;

    /**
     * Used when attribute group type is invalid.
     */
    public const INVALID_TYPE = 60;
}
