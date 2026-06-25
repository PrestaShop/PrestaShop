<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\CommandResult;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;

/**
 * Result of adding a tax rule.
 * Contains the tax rules group id which may have changed due to historization.
 */
class AddTaxRuleResult
{
    /**
     * @var TaxRulesGroupId
     */
    private TaxRulesGroupId $taxRulesGroupId;

    /**
     * @param TaxRulesGroupId $taxRulesGroupId the (potentially new) group id after historization
     */
    public function __construct(TaxRulesGroupId $taxRulesGroupId)
    {
        $this->taxRulesGroupId = $taxRulesGroupId;
    }

    /**
     * @return TaxRulesGroupId
     */
    public function getTaxRulesGroupId(): TaxRulesGroupId
    {
        return $this->taxRulesGroupId;
    }
}
