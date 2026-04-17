<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\TaxRulesGroup\QueryHandler;

use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\AbstractTaxRulesGroupHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Query\GetTaxRulesGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\QueryHandler\GetTaxRulesGroupForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\QueryResult\EditableTaxRulesGroup;

/**
 * Handles query which gets tax rules group for editing
 */
#[AsQueryHandler]
final class GetTaxRulesGroupForEditingHandler extends AbstractTaxRulesGroupHandler implements GetTaxRulesGroupForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws TaxRulesGroupNotFoundException
     */
    public function handle(GetTaxRulesGroupForEditing $query): EditableTaxRulesGroup
    {
        $taxRulesGroupId = $query->getTaxRulesGroupId();
        $taxRulesGroup = $this->getTaxRulesGroup($taxRulesGroupId);

        return new EditableTaxRulesGroup(
            $taxRulesGroupId,
            $taxRulesGroup->name,
            (bool) $taxRulesGroup->active,
            $taxRulesGroup->getAssociatedShops()
        );
    }
}
