<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception;

/**
 * Thrown on failure to update tax rules group
 */
class CannotUpdateTaxRulesGroupException extends TaxRulesGroupException
{
    /**
     * Thrown when status toggling fails
     */
    public const FAILED_TOGGLE_STATUS = 1;

    /**
     * When generic product update fails
     */
    public const FAILED_UPDATE_TAX_RULES_GROUP = 10;
}
