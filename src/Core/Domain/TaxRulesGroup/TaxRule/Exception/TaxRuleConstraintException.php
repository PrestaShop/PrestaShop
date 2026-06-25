<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Exception;

/**
 * Thrown when tax rule constraint is violated
 */
class TaxRuleConstraintException extends TaxRuleException
{
    /**
     * Thrown when provided tax rule id value is not valid
     */
    public const INVALID_ID = 1;

    /**
     * Thrown when provided country id value is not valid
     */
    public const INVALID_COUNTRY_ID = 2;

    /**
     * Thrown when provided tax id value is not valid
     */
    public const INVALID_TAX_ID = 3;

    /**
     * Thrown when provided behavior value is not valid
     */
    public const INVALID_BEHAVIOR = 4;

    /**
     * Thrown when provided zipcode value is not valid
     */
    public const INVALID_ZIPCODE = 5;
}
