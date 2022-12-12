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

use PrestaShop\PrestaShop\Adapter\CatalogPriceRule\Repository\CatalogPriceRuleRepository;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Query\GetCatalogPriceRuleList;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryHandler\GetCatalogPriceRuleListHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryResult\CatalogPriceRuleForListing;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryResult\CatalogPriceRuleList;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Query\GetSpecificPriceForEditing;

/**
 * Handles @see GetSpecificPriceForEditing using legacy object model
 */
class GetCatalogPriceRuleListHandler implements GetCatalogPriceRuleListHandlerInterface
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
    public function handle(GetCatalogPriceRuleList $query): CatalogPriceRuleList
    {
        $catalogPriceRules = $this->catalogPriceRuleRepository->getAll();
        $return = [];
        foreach ($catalogPriceRules as $catalogPriceRule) {
            $return[] = new CatalogPriceRuleForListing(
                (int) $catalogPriceRule['id_specific_price_rule'],
                $catalogPriceRule['name'],
                (int) $catalogPriceRule['id_shop'],
                (int) $catalogPriceRule['id_currency'],
                (int) $catalogPriceRule['id_country'],
                (int) $catalogPriceRule['id_group']
            );
        }

        return new CatalogPriceRuleList($return, count($return));
    }
}
