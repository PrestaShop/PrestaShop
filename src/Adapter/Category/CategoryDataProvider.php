<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Category;

/**
 * This class will provide data from DB / ORM about Category
 */
class CategoryDataProvider
{
    /**
     * Get all nested categories
     *
     * @param int|null $root_category
     * @param bool|int $id_lang
     * @param bool $active
     * @param int|null $groups
     * @param bool $use_shop_restriction
     * @param string $sql_filter
     * @param string $sql_sort
     * @param string $sql_limit
     *
     * @return array categories
     */
    public function getNestedCategories($root_category = null, $id_lang = false, $active = true, $groups = null, $use_shop_restriction = true, $sql_filter = '', $sql_sort = '', $sql_limit = '')
    {
        return \CategoryCore::getNestedCategories($root_category, $id_lang, $active, $groups, $use_shop_restriction, $sql_filter, $sql_sort, $sql_limit);
    }

    /**
     * Return available categories Names - excluding Root category
     *
     * @param int|null $root_category
     * @param bool|int $id_lang
     * @param bool $active return only active categories
     * @param $groups
     * @param bool $use_shop_restriction
     * @param string $sql_filter
     * @param string $sql_sort
     * @param string $sql_limit
     * @return array Categories
     */
    public function getAllCategoriesName($root_category = null, $id_lang = false, $active = true, $groups = null, $use_shop_restriction = true, $sql_filter = '', $sql_sort = '', $sql_limit = '')
    {
        $categories = \CategoryCore::getAllCategoriesName($root_category, $id_lang, $active, $groups, $use_shop_restriction, $sql_filter, $sql_sort, $sql_limit);
        array_shift($categories);
        return $categories;
    }
}
