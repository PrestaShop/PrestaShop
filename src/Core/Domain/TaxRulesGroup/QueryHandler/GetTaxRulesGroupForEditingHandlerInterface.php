<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Query\GetTaxRulesGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\QueryResult\EditableTaxRulesGroup;

/**
 * Defines contract for handler providing tax rules group for editing
 */
interface GetTaxRulesGroupForEditingHandlerInterface
{
    /**
     * @param GetTaxRulesGroupForEditing $query
     *
     * @return EditableTaxRulesGroup
     */
    public function handle(GetTaxRulesGroupForEditing $query): EditableTaxRulesGroup;
}
