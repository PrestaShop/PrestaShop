<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\CommandResult;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\ValueObject\TaxRuleId;
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
     * @var TaxRuleId[]
     */
    private array $taxRuleIds;

    /**
     * @param TaxRulesGroupId $taxRulesGroupId the (potentially new) group id after historization
     * @param TaxRuleId[] $taxRuleIds the ids of the tax rule(s) actually created (can be fewer than requested
     *                                when a country/state pair already had a unique rule)
     */
    public function __construct(TaxRulesGroupId $taxRulesGroupId, array $taxRuleIds = [])
    {
        $this->taxRulesGroupId = $taxRulesGroupId;
        $this->taxRuleIds = $taxRuleIds;
    }

    /**
     * @return TaxRulesGroupId
     */
    public function getTaxRulesGroupId(): TaxRulesGroupId
    {
        return $this->taxRulesGroupId;
    }

    /**
     * @return TaxRuleId[]
     */
    public function getTaxRuleIds(): array
    {
        return $this->taxRuleIds;
    }
}
