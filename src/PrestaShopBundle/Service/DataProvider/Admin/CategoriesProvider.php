<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShopBundle\Service\DataProvider\Admin;

use GuzzleHttp\Exception\RequestException;
use PrestaShopBundle\Service\DataProvider\Marketplace\ApiClient;
use Psr\Log\LoggerInterface;

/**
 * Provide the categories used to order modules and themes on https://addons.prestashop.com.
 */
class CategoriesProvider
{
    private $apiClient;
    private $logger;

    public static $categories;
    public static $categoriesFromApi;

    public function __construct(ApiClient $apiClient, LoggerInterface $logger)
    {
        $this->apiClient = $apiClient;
        $this->logger = $logger;
    }

    public function getCategories()
    {
        if (null === self::$categoriesFromApi) {
            try {
                self::$categoriesFromApi = $this->apiClient->getCategories();
            } catch (RequestException $e) {
                $this->logger->error('Module & services categories could not be loaded from marketplace API');
                self::$categoriesFromApi = array();
            }
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
                $categoryTab = isset($category->tab) ? $category->tab : null;
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
                    $categories['categories']->subMenu[$categoryName] = $this->createMenuObject(
                        $categoryName,
                        $categoryName,
                        $moduleIds,
                        $categoryTab
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
     *
     * @param $menu
     * @param $name
     * @param array $moduleIds
     * @param null $tab
     *
     * @return object
     */
    private function createMenuObject($menu, $name, $moduleIds = array(), $tab = null)
    {
        return (object) array(
            'tab' => $tab,
            'name' => $name,
            'refMenu' => $menu,
            'modules' => $moduleIds,
            'subMenu' => array(),
        );
    }
}
