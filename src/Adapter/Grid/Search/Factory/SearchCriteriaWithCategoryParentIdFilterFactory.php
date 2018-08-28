<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Grid\Search\Factory;

use Category;
use Configuration;
use Context;
use PrestaShop\PrestaShop\Core\Grid\Search\Factory\DecoratedSearchCriteriaFactory;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteria;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Shop;
use Tools;

/**
 * Class SearchCriteriaWithCategoryParentIdFilterFactory
 */
final class SearchCriteriaWithCategoryParentIdFilterFactory implements DecoratedSearchCriteriaFactory
{
    /**
     * {@inheritdoc}
     */
    public function createFrom(SearchCriteriaInterface $searchCriteria)
    {
        $categoryParentId = $this->resolveCategoryParentId();

        $filters = array_merge(
            ['id_category_parent' => $categoryParentId],
            $searchCriteria->getFilters()
        );

        return new SearchCriteria(
            $filters,
            $searchCriteria->getOrderBy(),
            $searchCriteria->getOrderWay(),
            $searchCriteria->getOffset(),
            $searchCriteria->getLimit()
        );
    }

    /**
     * @return int Category parent id
     */
    private function resolveCategoryParentId()
    {
        if (Tools::isSubmit('id_category')) {
            return (int) Tools::getValue('id_category');
        }

        $categoriesCountWithoutParent = count(Category::getCategoriesWithoutParent());
        $isMultiShopFeatureActive = Shop::isFeatureActive();

        if (!$isMultiShopFeatureActive && $categoriesCountWithoutParent > 1) {
            return (int) Configuration::get('PS_ROOT_CATEGORY');
        }

        if ($isMultiShopFeatureActive && 1 === $categoriesCountWithoutParent) {
            return (int) Configuration::get('PS_HOME_CATEGORY');
        }

        if ($isMultiShopFeatureActive
            && $categoriesCountWithoutParent > 1
            && Shop::getContext() !== Shop::CONTEXT_SHOP
        ) {
            if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')
                && count(Shop::getShops(true, null, true)) === 1
            ) {
                return Context::getContext()->shop->id_category;
            }

            return (int) Configuration::get('PS_ROOT_CATEGORY');
        }

        return Context::getContext()->shop->id_category;
    }
}
