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

use PrestaShop\PrestaShop\Adapter\Module\Module as ApiModule;
use GuzzleHttp\Exception\RequestException;
use PrestaShopBundle\Service\DataProvider\Marketplace\ApiClient;
use Psr\Log\LoggerInterface;
use stdClass;

/**
 * Provide the categories used to order modules and themes on https://addons.prestashop.com.
 */
class CategoriesProvider
{
    const CATEGORY_OTHER = 'other';
    const CATEGORY_OTHER_NAME = 'Other';

    const CATEGORY_THEME = 'theme_modules';
    const CATEGORY_THEME_NAME = 'Theme modules';

    const CATEGORY_MY_MODULES = 'my_modules';
    const CATEGORY_MY_MODULES_NAME = 'My Modules';

    private $apiClient;
    private $logger;
    private $modulesTheme;

    public static $categories;
    public static $categoriesFromApi;

    public function __construct(ApiClient $apiClient, LoggerInterface $logger, array $addonsCategories, array $modulesTheme)
    {
        $this->apiClient = $apiClient;
        $this->logger = $logger;
        $this->modulesTheme = $modulesTheme;
        // We now avoid calling the API. This data is loaded from a local YML file
        self::$categoriesFromApi = empty($addonsCategories) ? null : $this->sortCategories($addonsCategories);
    }

    /**
     * Get categories from API.
     *
     * @return array
     */
    public function getCategories()
    {
        if (null === self::$categoriesFromApi) {
            try {
                self::$categoriesFromApi = $this->apiClient->getCategories();
            } catch (RequestException $e) {
                $this->logger->error('Modules categories could not be loaded from marketplace API');
                self::$categoriesFromApi = [];
            }
        }

        return self::$categoriesFromApi;
    }

    /**
     * Return the list of categories with the associated modules.
     *
     * @param array|AddonsCollection the list of modules
     *
     * @return array the list of categories
     */
    public function getCategoriesMenu($modules)
    {
        if (null === self::$categories) {
            // The Root category is "Categories"
            $categoriesListing = $this->getCategories();
            $categories = $this->initializeCategories($categoriesListing);
            foreach ($modules as $module) {
                if (empty($categoriesListing)) {
                    // No result from Addons, check module type
                    $category = $this->findModuleType($module, $this->modulesTheme);
                } else {
                    $category = $this->findModuleCategory($module, $categories);
                }

                $categories['categories']->subMenu[$category]->modules[] = $module;
            }

            // Clear custom categories if there is no module inside
            foreach ([self::CATEGORY_THEME, self::CATEGORY_MY_MODULES] as $category) {
                if (empty($categories['categories']->subMenu[$category]->modules)) {
                    unset($categories['categories']->subMenu[$category]);
                }
            }

            self::$categories = $categories;
        }

        return self::$categories;
    }

    /**
     * Initialize categories from API or if this one is empty,
     * use theme and my modules categories.
     *
     * @param array|stdClass $categoriesListing Category listing
     *
     * @return array
     */
    private function initializeCategories($categoriesListing)
    {
        $categories = [
            'categories' => $this->createMenuObject('categories', 'Categories'),
        ];

        if (empty($categoriesListing)) {
            $categories['categories']->subMenu[self::CATEGORY_THEME] = $this->createMenuObject(
                self::CATEGORY_THEME,
                self::CATEGORY_THEME_NAME,
                [],
                self::CATEGORY_THEME
            );
            $categories['categories']->subMenu[self::CATEGORY_MY_MODULES] = $this->createMenuObject(
                self::CATEGORY_MY_MODULES,
                self::CATEGORY_MY_MODULES_NAME,
                [],
                self::CATEGORY_MY_MODULES
            );

            return $categories;
        }

        foreach ($categoriesListing as $category) {
            $categories['categories']->subMenu[$category->name] = $this->createMenuObject(
                $category->id_category,
                $category->name,
                [],
                isset($category->tab) ? $category->tab : null
            );
        }

        $categories['categories']->subMenu[self::CATEGORY_THEME] = $this->createMenuObject(
            self::CATEGORY_THEME,
            self::CATEGORY_THEME_NAME,
            [],
            self::CATEGORY_THEME
        );
        $categories['categories']->subMenu[self::CATEGORY_OTHER] = $this->createMenuObject(
            self::CATEGORY_OTHER,
            self::CATEGORY_OTHER_NAME,
            [],
            self::CATEGORY_OTHER
        );

        return $categories;
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
    private function createMenuObject($menu, $name, $moduleIds = [], $tab = null)
    {
        return (object) array(
            'tab' => $tab,
            'name' => $name,
            'refMenu' => $menu,
            'modules' => $moduleIds,
            'subMenu' => [],
        );
    }

    /**
     * Find module type.
     *
     * @param ApiModule $installedProduct Installed product
     * @param array $modulesTheme Modules theme
     */
    private function findModuleType(ApiModule $installedProduct, array $modulesTheme)
    {
        if (in_array($installedProduct->attributes->get('name'), $modulesTheme)) {
            return self::CATEGORY_THEME;
        }

        return self::CATEGORY_MY_MODULES;
    }

    /**
     * Find module category.
     *
     * @param ApiModule $installedProduct Installed product
     * @param array $categories Available categories
     */
    private function findModuleCategory(ApiModule $installedProduct, array $categories)
    {
        $moduleCategory = $installedProduct->attributes->get('categoryName');
        $moduleCategoryParent = $this->getParentCategory($moduleCategory);
        if (!isset($categories['categories']->subMenu[$moduleCategoryParent])) {
            if (in_array($installedProduct->attributes->get('name'), $this->modulesTheme)) {
                $moduleCategoryParent = self::CATEGORY_THEME;
            } else {
                $moduleCategoryParent = self::CATEGORY_OTHER;
            }
        }

        if (array_key_exists($moduleCategoryParent, $categories['categories']->subMenu)) {
            return $moduleCategoryParent;
        }

        return CategoriesProvider::CATEGORY_OTHER;
    }

    /**
     * Sort addons categories by order field.
     *
     * @param array $categories
     */
    private function sortCategories(array $categories)
    {
        uasort(
            $categories,
            function ($a, $b) {
                $a = !isset($a['order']) ? 0 : $a['order'];
                $b = !isset($b['order']) ? 0 : $b['order'];

                if ($a === $b) {
                    return 0;
                }

                return ($a < $b) ? -1 : 1;
            }
        );

        // Convert array to object to be consistent with current API call
        $categories = json_decode(json_encode($categories));

        return $categories;
    }
}
