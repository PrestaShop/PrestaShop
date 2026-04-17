<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception;

/**
 * Thrown when specific price constraints are violated
 */
class SpecificPriceConstraintException extends SpecificPriceException
{
    /**
     * When catalog price rule id is not valid
     */
    public const INVALID_ID = 1;

    /**
     * When date-time format is invalid
     */
    public const INVALID_DATETIME = 2;

    /**
     * When date range is not valid
     */
    public const INVALID_DATE_RANGE = 3;

    /**
     * When specific price priority value is not valid
     */
    public const INVALID_PRIORITY = 4;

    /**
     * When there is duplicated priorities in specific price priority list
     */
    public const DUPLICATE_PRIORITY = 5;

    /**
     * When specific price from quantity value is not valid
     */
    public const INVALID_FROM_QUANTITY = 6;

    /**
     * When specific price tax included value is not valid
     */
    public const INVALID_TAX_INCLUDED = 7;

    /**
     * When specific price reduction amount value is not valid
     */
    public const INVALID_REDUCTION_AMOUNT = 8;

    /**
     * When specific price fixed price value is not valid
     */
    public const INVALID_FIXED_PRICE = 9;

    /**
     * When specific price lower validity limit is not valid
     */
    public const INVALID_FROM_DATETIME = 10;

    /**
     * When specific price upper validity limit is not valid
     */
    public const INVALID_TO_DATETIME = 11;

    /**
     * When specific price reduction type value is not valid
     */
    public const INVALID_REDUCTION_TYPE = 12;

    /**
     * When specific price relation ID is not valid
     */
    public const INVALID_RELATION_ID = 13;

    /**
     * When exactly same specific price already exists for product
     */
    public const NOT_UNIQUE_PER_PRODUCT = 14;

    /**
     * When neither price nor reduction value is set
     */
    public const REDUCTION_OR_PRICE_MUST_BE_SET = 15;
}
