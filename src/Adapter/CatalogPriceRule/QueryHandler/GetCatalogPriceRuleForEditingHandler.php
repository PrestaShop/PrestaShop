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

use DateTime;
use PrestaShop\PrestaShop\Adapter\CatalogPriceRule\AbstractCatalogPriceRuleHandler;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Query\GetCatalogPriceRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryHandler\GetCatalogPriceRuleForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryResult\EditableCatalogPriceRule;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\ValueObject\CatalogPriceRuleId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;

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
    public function handle(GetCatalogPriceRuleForEditing $query): EditableCatalogPriceRule
    {
        $catalogPriceRuleId = $query->getCatalogPriceRuleId();
        $specificPriceRule = $this->getSpecificPriceRule($catalogPriceRuleId);

        $from = $specificPriceRule->from;
        $to = $specificPriceRule->to;

        return new EditableCatalogPriceRule(
            new CatalogPriceRuleId((int) $specificPriceRule->id),
            $specificPriceRule->name,
            (int) $specificPriceRule->id_shop,
            (int) $specificPriceRule->id_currency,
            (int) $specificPriceRule->id_country,
            (int) $specificPriceRule->id_group,
            (int) $specificPriceRule->from_quantity,
            (float) $specificPriceRule->price,
            new Reduction($specificPriceRule->reduction_type, (float) $specificPriceRule->reduction),
            (bool) $specificPriceRule->reduction_tax,
            //@todo: Use Utils/DateTime from PR #13584
            $from !== '0000-00-00 00:00:00' ? new DateTime($from) : null,
            $to !== '0000-00-00 00:00:00' ? new DateTime($to) : null
        );
    }
}
