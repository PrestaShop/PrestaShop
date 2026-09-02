<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception;

/**
 * Thrown when tax rules group constraint is violated
 */
class TaxRulesGroupConstraintException extends TaxRulesGroupException
{
    /**
     * Thrown when provided tax rules group id value is not valid
     */
    public const INVALID_ID = 1;

    /**
     * @var int - error is raised when a value in array is not integer type
     */
    public const INVALID_SHOP_ASSOCIATION = 2;

    public const INVALID_NAME = 3;
    public const INVALID_ACTIVE = 4;
    public const INVALID_DELETED = 5;
    public const INVALID_CREATION_DATE = 6;
    public const INVALID_UPDATE_DATE = 7;
}
