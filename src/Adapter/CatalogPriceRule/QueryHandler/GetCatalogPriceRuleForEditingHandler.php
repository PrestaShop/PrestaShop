<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\CatalogPriceRule\QueryHandler;

use DateTime;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\CatalogPriceRule\AbstractCatalogPriceRuleHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Query\GetCatalogPriceRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryHandler\GetCatalogPriceRuleForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryResult\EditableCatalogPriceRule;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\ValueObject\CatalogPriceRuleId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtils;

/**
 * Handles command which gets catalog price rule for editing using legacy object model
 */
#[AsQueryHandler]
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
            new DecimalNumber($specificPriceRule->price),
            new Reduction($specificPriceRule->reduction_type, (string) $specificPriceRule->reduction),
            (bool) $specificPriceRule->reduction_tax,
            !DateTimeUtils::isNull($from) ? new DateTime($from) : null,
            !DateTimeUtils::isNull($to) ? new DateTime($to) : null
        );
    }
}
