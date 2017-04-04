<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Service\DataProvider\Admin;

use PrestaShopBundle\Service\DataProvider\Marketplace\ApiClient;

/**
 * Provide the categories used to order modules and themes on https://addons.prestashop.com.
 */
class CategoriesProvider
{
    private $apiClient;

    static $categories;
    static $categoriesFromApi;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function getCategories()
    {
        if (null === self::$categoriesFromApi) {
            self::$categoriesFromApi = $this->apiClient->getCategories();
        }

        return self::$categoriesFromApi;
    }

    /**
     * Return the list of categories with the number of associated modules.
     *
     * @param array the list of modules
     *
     * @return array the list of categories
     */
    public function getCategoriesMenu(array $modules)
    {

        if (null === self::$categories) {
            // The Root category is "Categories"
            $categories['categories'] = $this->createMenuObject('categories', 'Categories');

            foreach ($this->getCategories() as $category) {
                $categoryName = $category->name;
                $moduleIds = array();

                foreach ($modules as $module) {
                    $moduleCategory = $module->attributes->get('categoryName');
                    $moduleCategoryParent = $this->getParentCategory($moduleCategory);

                    if ($moduleCategoryParent === $categoryName) {
                        $moduleIds[] = $module->attributes->get('id');
                    }
                }

                if (count($moduleIds)) {
                    $categories['categories']->subMenu[$categoryName] = $this->createMenuObject($categoryName,
                        $categoryName,
                        $moduleIds
                    );
                }
            }

            usort($categories['categories']->subMenu, function ($a, $b) {
                return strcmp($a->name, $b->name);
            });

            self::$categories = $categories;
        }

        return self::$categories;
    }

    /**
     * Considering a category name, return his category parent name.
     *
     * @param string the category
     *
     * @return string the category
     */
    public function getParentCategory($categoryName)
    {
        foreach ($this->getCategories() as $parentCategory) {
            foreach ($parentCategory->categories as $childCategory) {
                if ($childCategory->name === $categoryName) {
                    return $parentCategory->name;
                }
            }
        }
        return $categoryName;
    }

    /**
     * Re-organize category data into a Menu item.
     */
    private function createMenuObject($menu, $name, $moduleIds = array())
    {
        return (object) array(
            'name' => $name,
            'refMenu' => $menu,
            'modules' => $moduleIds,
            'subMenu' => array(),
        );
    }
}
