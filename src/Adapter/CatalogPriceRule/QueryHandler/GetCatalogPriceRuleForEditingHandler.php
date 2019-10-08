<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\CatalogPriceRule\QueryHandler;

use PrestaShop\PrestaShop\Adapter\CatalogPriceRule\AbstractCatalogPriceRuleHandler;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Query\GetCatalogPriceRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryHandler\GetCatalogPriceRuleForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryResult\EditableCatalogPriceRule;

/**
 * Handles command which gets catalog price rule for editing using legacy object model
 */
final class GetCatalogPriceRuleForEditingHandler extends AbstractCatalogPriceRuleHandler implements GetCatalogPriceRuleForEditingHandlerInterface
{
    /**
     * @param GetCatalogPriceRuleForEditing $query
     *
     * @return EditableCatalogPriceRule
     */
    public function handle(GetCatalogPriceRuleForEditing $query)
    {
        $catalogPriceRuleId = $query->getCatalogPriceRuleId();
        $specificPriceRule = $this->getSpecificPriceRule($catalogPriceRuleId);

        return new EditableCatalogPriceRule(
            (int) $specificPriceRule->id,
            $specificPriceRule->name,
            (int) $specificPriceRule->id_shop,
            (int) $specificPriceRule->id_currency,
            (int) $specificPriceRule->id_country,
            (int) $specificPriceRule->id_group,
            (int) $specificPriceRule->from_quantity,
            (float) $specificPriceRule->price,
            $specificPriceRule->from,
            $specificPriceRule->to,
            $specificPriceRule->reduction_type,
            (bool) $specificPriceRule->reduction_tax,
            (float) $specificPriceRule->reduction
        );
    }
}
