<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Query;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;

/**
 * Gets tax rules group for editing in Back Office
 */
class GetTaxRulesGroupForEditing
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
     * @return TaxRulesGroupId $taxRulesGroupId
     */
    public function getTaxRulesGroupId(): TaxRulesGroupId
    {
        return $this->taxRulesGroupId;
    }
}
