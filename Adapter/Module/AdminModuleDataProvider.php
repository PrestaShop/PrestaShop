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
 *  @author     PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Module;

use PrestaShop\PrestaShop\Adapter\Admin\AbstractAdminQueryBuilder;
use PrestaShopBundle\Service\DataProvider\Admin\ModuleInterface;
use Symfony\Component\Config\ConfigCacheFactory;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Data provider for new Architecture, about Module object model.
 *
 * This class will provide data from DB / ORM about Modules for the Admin interface.
 * This is an Adapter that works with the Legacy code and persistence behaviors.
 *
 * FIXME: rewrite persistence of filter parameters -> into DB
 */
class AdminModuleDataProvider extends AbstractAdminQueryBuilder implements ModuleInterface
{
    const _CACHEFILE_CATEGORIES_ = 'catalog_categories.json';
    const _CACHEFILE_MODULES_ = 'catalog_modules.json';

    /* Cache for One Day */
    const _WATCH_DOG_ = 86400;

    private $is_employee_addons_logged = false;
    private $kernel;

    protected $catalog_categories      = [];
    protected $catalog_modules         = [];

    public function __construct(\AppKernel $kernel)
    {
        $this->kernel = $kernel;
        $context = \Context::getContext();
        if (isset($context->cookie->username_addons) && isset($context->cookie->password_addons)
            && !empty($context->cookie->username_addons) && !empty($context->cookie->password_addons)) {
            $this->is_employee_addons_logged = true;
        }
    }

    public function getAllModules()
    {
        return \Module::getModulesOnDisk(true,
                (bool)$this->is_employee_addons_logged,
                (int)\Context::getContext()->employee->id);
    }

    public function getCatalogModules(array $filter = [])
    {
        if (count($this->catalog_modules) === 0) {
            $this->loadCatalogData();
        }

        return $this->applyModuleFilters($filter);
    }

    public function getCatalogCategories()
    {
        if (count($this->catalog_categories) === 0) {
            $this->loadCatalogData();
        }

        return $this->catalog_categories;
    }

    protected function applyModuleFilters(array $filters)
    {
        if (! count($filters)) {
            return $this->catalog_modules;
        }

        $module_ids = [];

        // We get our module IDs to keep
        foreach ($filters as $filter_name => $value) {
            $search_result = [];

            switch ($filter_name) {
                case 'category':
                    $ref = $this->getRefFromModuleCategoryName($value);
                    // We get the IDs list from the category
                    $search_result = isset($this->catalog_categories->categories->subMenu->{$ref}) ?
                        $this->catalog_categories->categories->subMenu->{$ref}->modulesRef :
                        [];
                    break;
                case 'search':
                    // We build our results array.
                    // We could remove directly the non-matching modules, but we will give that for the final loop of this function

                    foreach ($this->catalog_modules as $key => $module) {
                        if (strpos($module->displayName, $value) !== false
                            || strpos($module->name, $value) !== false
                            || strpos($module->description, $value) !== false) {
                            $search_result[] = $key;
                        }
                    }
                    break;
            }

            if (count($module_ids)) {
                $module_ids = array_intersect($search_result, $module_ids);
            } else {
                $module_ids = $search_result;
            }
        }

        // We apply the filter results
        foreach ($this->catalog_modules as $key => $module) {
            if (! in_array($key, $module_ids)) {
                unset($this->catalog_modules[$key]);
            }
        }

        return $this->catalog_modules;
    }

    protected function loadCatalogData()
    {
        $this->catalog_categories = $this->getModuleCache(self::_CACHEFILE_CATEGORIES_);
        $this->catalog_modules    = $this->getModuleCache(self::_CACHEFILE_MODULES_);

        if (!$this->catalog_categories || !$this->catalog_modules) {
            $params = ['format' => 'json'];
            $addons_modules = \Tools::addonsRequest('must-have', $params);
            $partners_modules = \Tools::addonsRequest('partner', $params);
            $natives_modules = \Tools::addonsRequest('native', $params);

            if ((!$addons_modules || !$partners_modules || !$natives_modules) && ! $this->fallbackOnCache()) {
                throw new \Exception("Cannot load data from PrestaShop Addons");
            }

            $json_addons_modules = json_decode($addons_modules);
            $json_partners_modules = json_decode($partners_modules);
            $json_natives_modules = json_decode($natives_modules);

            if ($json_addons_modules !== false && $json_partners_modules !== false && $json_natives_modules !== false) {
                $jsons = array_merge($json_addons_modules->modules, $json_natives_modules->modules, $json_partners_modules->products);

                $this->catalog_modules    = $this->convertJsonForNewCatalog($jsons);
                $this->catalog_categories = $this->getCategoriesFromModules();
                $this->registerModuleCache(self::_CACHEFILE_CATEGORIES_, $this->catalog_categories);
                $this->registerModuleCache(self::_CACHEFILE_MODULES_, $this->catalog_modules);
            } elseif (! $this->fallbackOnCache()) {
                throw new \Exception("Data from PrestaShop Addons is invalid, and cannot fallback on cache");
            }
        }
    }

    protected function getCategoriesFromModules()
    {
        $categories = new \stdClass;

        // Only Tab: Categories
        $categories->categories = $this->createMenuObject('categories',
            'Categories');

        foreach ($this->catalog_modules as $module_key => $module) {
            $name = $module->categoryName;
            $ref  = $this->getRefFromModuleCategoryName($name);

            if (!isset($ref, $categories->categories->subMenu->{$ref})) {
                $categories->categories->subMenu->{$ref} = $this->createMenuObject($ref,
                    $name);
            }

            $categories->categories->subMenu->{$ref}->modulesRef[] = $module_key;
        }

        return $categories;
    }

    protected function convertJsonForNewCatalog($original_json)
    {
        $remixed_json = [];
        $doublons = [];

        foreach ($original_json as $module) {
            if (in_array($module->name, $doublons)) {
                continue;
            }
            
            $doublons[] = $module->name;

            // Add un-implemented properties
            $module->refs       = (array)$this->getRefFromModuleCategoryName($module->categoryName);
            $module->conditions = [];
            $module->rating     = (object)[
                    'score' => 0.0,
                    'countReviews' => 0,
            ];
            $module->scoring    = 0;
            $module->media      = (object)[
                    'img' => $module->img,
                    'badges' => isset($module->badges)?$module->badges:[],
                    'cover' => isset($module->cover)?$module->cover:[],
                    'screenshotsUrls' => [],
                    'videoUrl' => null,
            ];
            unset($module->badges);
            //unset($module->categoryName);
            unset($module->cover);

            $remixed_json[] = $module;
        }

        usort($remixed_json, function ($module1, $module2) {
            return strnatcmp($module1->displayName, $module2->displayName);
        });

        return $remixed_json;
    }

    protected function createMenuObject($ref, $name)
    {
        return (object)[
                'name' => $name,
                'refMenu' => $ref,
                'subMenu' => new \stdClass,
                'modulesRef' => [],
        ];
    }

    protected function getRefFromModuleCategoryName($name)
    {
        return \Tools::replaceAccentedChars(str_replace([' '], ['_'],
                    strtolower($name)));
    }

    protected function fallbackOnCache()
    {
        // Fallback on data from cache if exists
        $this->catalog_categories = $this->getModuleCache(self::_CACHEFILE_CATEGORIES_, false);
        $this->catalog_modules    = $this->getModuleCache(self::_CACHEFILE_MODULES_, false);

        return ($this->catalog_categories && $this->catalog_modules);
    }

    private function getModuleCache($file, $check_freshness = true)
    {
        $cacheFile = $this->kernel->getCacheDir().'/modules/'.$file;
        if (! file_exists($cacheFile)) {
            return false;
        }

        try {
            if ($check_freshness && (filemtime($cacheFile) + self::_WATCH_DOG_) <= time()) {
                return false;
            }

            $fh = fopen($cacheFile, 'r');
            $cache = trim(fgets($fh));

            if ($cache) {
                return json_decode($cache);
            }
        } catch (\Exception $e) {
            throw new \Exception('Cannot read from the cache file '. $file);
        }
    }

    private function registerModuleCache($file, $data)
    {
        try {
            $cache = (new ConfigCacheFactory(true))->cache($this->kernel->getCacheDir().'/modules/'.$file, function () {});
            $cache->write(json_encode($data));

            return $cache->getPath();
        } catch (IOException $e) {
            throw new \Exception('Cannot write in the cache file '. $file, $e->getCode(), $e);
        }
    }
}
