<?php
/**
 * 2007-2016 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Category;

use ObjectModel;
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

    /**
     * Return a simple array id/name of categories for a specified product
     * @param Product $product
     *
     * @return array Categories
     */
    public function getCategoriesByProduct(ObjectModel $product)
    {
        $allCategories = $this->getAllCategoriesName();
        $productCategories = $product->getCategories();

        $results = [];
        foreach ($allCategories as $category) {
            foreach($productCategories as $productCategory) {
                if ($productCategory == $category['id_category']) {
                    $results[] = [
                        'id' => $category['id_category'],
                        'name' => $category['name'],
                        'breadcrumb' => $this->getBreadCrumb($category['id_category'])
                    ];
                }
                $productCategories[$category['name']] = $category['id_category'];
            }

        }

        return $results;
    }

    /**
     * Return a simple array id/name of categories
     *
     * @return array Categories
     */
    public function getCategoriesWithBreadCrumb()
    {
        $allCategories = $this->getAllCategoriesName();

        $results = [];
        foreach ($allCategories as $category) {
            $results[] = [
                'id' => $category['id_category'],
                'name' => $category['name'],
                'breadcrumb' => $this->getBreadCrumb($category['id_category'])
            ];
        }

        return $results;
    }

    /**
     * Returns a simple breacrumb from a categoryId, the delimiter can be choosen
     * @param $categoryId
     * @param string $delimiter
     * @return string
     */
    public function getBreadCrumb($categoryId, $delimiter = " > ")
    {
        $currentCategory = new \Category($categoryId);
        $categories = $currentCategory->getParentsCategories();
        $categories = array_reverse($categories, true);
        $breadCrumb = '';

        foreach($categories as $category) {
            $breadCrumb .= ' > '.$category['name'];
        }

        return substr($breadCrumb, strlen($delimiter));
    }
}
