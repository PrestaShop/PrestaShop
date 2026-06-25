<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Command;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\ValueObject\TaxRuleId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;

/**
 * Command responsible for bulk deletion of tax rules within a group
 */
class BulkDeleteTaxRuleCommand
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
     * @param int $taxRulesGroupId
     * @param int[] $taxRuleIds
     */
    public function __construct(int $taxRulesGroupId, array $taxRuleIds)
    {
        $this->taxRulesGroupId = new TaxRulesGroupId($taxRulesGroupId);
        $this->setTaxRuleIds($taxRuleIds);
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

    /**
     * @param int[] $taxRuleIds
     */
    private function setTaxRuleIds(array $taxRuleIds): void
    {
        foreach ($taxRuleIds as $taxRuleId) {
            $this->taxRuleIds[] = new TaxRuleId($taxRuleId);
        }
    }
}
