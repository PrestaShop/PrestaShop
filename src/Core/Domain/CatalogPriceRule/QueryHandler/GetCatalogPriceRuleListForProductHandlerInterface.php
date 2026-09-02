<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Query\GetCatalogPriceRuleListForProduct;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryResult\CatalogPriceRuleList;

/**
 * Defines contract for GetCatalogPriceRuleForEditingHandler
 */
interface GetCatalogPriceRuleListForProductHandlerInterface
{
    /**
     * @param GetCatalogPriceRuleListForProduct $query
     *
     * @return CatalogPriceRuleList
     */
    public function handle(GetCatalogPriceRuleListForProduct $query): CatalogPriceRuleList;
}
