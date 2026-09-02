<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception;

/**
 * Thrown when catalog price rule constraints are violated
 */
class CatalogPriceRuleConstraintException extends CatalogPriceRuleException
{
    /**
     * When catalog price rule id is not valid
     */
    public const INVALID_ID = 10;

    /**
     * When date-time format is invalid
     */
    public const INVALID_DATETIME = 20;

    /**
     * When date range is not valid
     */
    public const INVALID_DATE_RANGE = 30;
}
