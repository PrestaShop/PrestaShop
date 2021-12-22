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

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product\Combination;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetEditableCombinationsList;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationListForEditing;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductCombinationFilters;
use Tests\Integration\Behaviour\Features\Context\Domain\Product\AbstractProductFeatureContext;

abstract class AbstractCombinationFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @param string $productReference
     * @param ProductCombinationFilters|null $combinationFilters
     *
     * @return CombinationListForEditing
     */
    protected function getCombinationsList(string $productReference, ?ProductCombinationFilters $combinationFilters = null): CombinationListForEditing
    {
        return $this->getQueryBus()->handle(new GetEditableCombinationsList(
            $this->getSharedStorage()->get($productReference),
            $this->getDefaultLangId(),
            $combinationFilters ? $combinationFilters->getLimit() : null,
            $combinationFilters ? $combinationFilters->getOffset() : null,
            $combinationFilters ? $combinationFilters->getOrderBy() : null,
            $combinationFilters ? $combinationFilters->getOrderWay() : null,
            $combinationFilters ? $combinationFilters->getFilters() : []
        ));
    }

    /**
     * @param string $combinationReference
     *
     * @return CombinationForEditing
     */
    protected function getCombinationForEditing(string $combinationReference): CombinationForEditing
    {
        return $this->getQueryBus()->handle(new GetCombinationForEditing(
            $this->getSharedStorage()->get($combinationReference)
        ));
    }
}
