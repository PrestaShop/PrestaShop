<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;

/**
 * Command responsible for single tax rules group deletion
 */
class DeleteTaxRulesGroupCommand
{
    /**
     * @var TaxRulesGroupId
     */
    private $taxRulesGroupId;

    /**
     * @param int $taxRulesGroupId
     *
     * @throws TaxRulesGroupConstraintException
     */
    public function __construct(int $taxRulesGroupId)
    {
        $this->taxRulesGroupId = new TaxRulesGroupId($taxRulesGroupId);
    }

    /**
     * @return TaxRulesGroupId
     */
    public function getTaxRulesGroupId(): TaxRulesGroupId
    {
        return $this->taxRulesGroupId;
    }
}
