<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Query;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\ValueObject\TaxRuleId;

/**
 * Query to get tax rule data for editing
 */
class GetTaxRuleForEditing
{
    /**
     * @var TaxRuleId
     */
    private TaxRuleId $taxRuleId;

    /**
     * @param int $taxRuleId
     */
    public function __construct(int $taxRuleId)
    {
        $this->taxRuleId = new TaxRuleId($taxRuleId);
    }

    /**
     * @return TaxRuleId
     */
    public function getTaxRuleId(): TaxRuleId
    {
        return $this->taxRuleId;
    }
}
