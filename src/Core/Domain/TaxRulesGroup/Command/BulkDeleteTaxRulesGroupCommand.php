<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;

/**
 * Command responsible for multiple tax rules groups deletion
 */
class BulkDeleteTaxRulesGroupCommand
{
    /**
     * @var TaxRulesGroupId[]
     */
    private $taxRulesGroupIds;

    /**
     * @param int[] $taxRulesGroupIds
     *
     * @throws TaxRulesGroupConstraintException
     */
    public function __construct(array $taxRulesGroupIds)
    {
        $this->setTaxRulesGroupIds($taxRulesGroupIds);
    }

    /**
     * @return TaxRulesGroupId[]
     */
    public function getTaxRulesGroupIds(): array
    {
        return $this->taxRulesGroupIds;
    }

    /**
     * @param int[] $taxRulesGroupIds
     *
     * @throws TaxRulesGroupConstraintException
     */
    private function setTaxRulesGroupIds(array $taxRulesGroupIds)
    {
        foreach ($taxRulesGroupIds as $taxRulesGroupId) {
            $this->taxRulesGroupIds[] = new TaxRulesGroupId($taxRulesGroupId);
        }
    }
}
