<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\CatalogPriceRule\QueryHandler;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\CatalogPriceRule\Repository\CatalogPriceRuleRepository;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Query\GetCatalogPriceRuleListForProduct;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryHandler\GetCatalogPriceRuleListForProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryResult\CatalogPriceRuleForListing;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryResult\CatalogPriceRuleList;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;

/**
 * Handles @see GetCatalogPriceRuleListForProduct
 */
class GetCatalogPriceRuleListForProductHandler implements GetCatalogPriceRuleListForProductHandlerInterface
{
    /**
     * @var CatalogPriceRuleRepository
     */
    private $catalogPriceRuleRepository;

    /**
     * @param CatalogPriceRuleRepository $catalogPriceRuleRepository
     */
    public function __construct(
        CatalogPriceRuleRepository $catalogPriceRuleRepository
    ) {
        $this->catalogPriceRuleRepository = $catalogPriceRuleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCatalogPriceRuleListForProduct $query): CatalogPriceRuleList
    {
        $catalogPriceRules = $this->catalogPriceRuleRepository->getByProductId(
            $query->getProductId(),
            $query->getLangId(),
            $query->getLimit(),
            $query->getOffset()
        );

        return new CatalogPriceRuleList(
            $this->formatCatalogPriceRuleList($catalogPriceRules),
            $this->catalogPriceRuleRepository->countByProductId(
                $query->getProductId(),
                $query->getLangId()
            )
        );
    }

    /**
     * @param array<int, array<string, string|null>> $catalogPriceRules
     *
     * @return CatalogPriceRuleForListing[]
     */
    private function formatCatalogPriceRuleList(array $catalogPriceRules): array
    {
        $return = [];
        foreach ($catalogPriceRules as $catalogPriceRule) {
            $return[] = new CatalogPriceRuleForListing(
                (int) $catalogPriceRule['id_specific_price_rule'],
                $catalogPriceRule['specific_price_rule_name'],
                (int) $catalogPriceRule['from_quantity'],
                $catalogPriceRule['reduction_type'],
                new DecimalNumber($catalogPriceRule['reduction']),
                (bool) $catalogPriceRule['reduction_tax'],
                DateTimeUtil::buildNullableDateTime($catalogPriceRule['from']),
                DateTimeUtil::buildNullableDateTime($catalogPriceRule['to']),
                $catalogPriceRule['shop_name'],
                $catalogPriceRule['currency_name'],
                $catalogPriceRule['lang_name'],
                $catalogPriceRule['group_name'],
                $catalogPriceRule['currency_iso']
            );
        }

        return $return;
    }
}
