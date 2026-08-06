<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Query\GetTaxRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\QueryResult\EditableTaxRule;

/**
 * Defines contract for getting tax rule data for editing
 */
interface GetTaxRuleForEditingHandlerInterface
{
    /**
     * @param GetTaxRuleForEditing $query
     *
     * @return EditableTaxRule
     */
    public function handle(GetTaxRuleForEditing $query): EditableTaxRule;
}
