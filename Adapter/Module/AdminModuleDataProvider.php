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

use PrestaShop\PrestaShop\Adapter\Addons\AddonsDataProvider;
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

    private $cache_dir;

    protected $catalog_categories      = [];
    protected $catalog_modules         = [];

    public function __construct(\AppKernel $kernel)
    {
        $this->kernel = $kernel;
        $this->cache_dir = $this->kernel->getCacheDir().'/modules/';
        $context = \Context::getContext();
        if (isset($context->cookie->username_addons) && isset($context->cookie->password_addons)
            && !empty($context->cookie->username_addons) && !empty($context->cookie->password_addons)) {
            $this->is_employee_addons_logged = true;
        }
    }

    public function clearCache()
    {
        foreach ([self::_CACHEFILE_CATEGORIES_, self::_CACHEFILE_MODULES_] as $file) {
            $path = $this->cache_dir . $file;
            if (file_exists($path)) {
                unlink($path);
            }
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

        shuffle($this->catalog_modules);

        return $this->applyModuleFilters($filter);
    }

    public function getCatalogCategories()
    {
        if (count($this->catalog_categories) === 0) {
            $this->loadCatalogData();
        }

        return $this->catalog_categories;
    }

    public function getModule($name)
    {
        if ($this->isModuleOnDisk($name)) {
            return \PrestaShop\PrestaShop\Adapter\ServiceLocator::get($name);
        }

        throw new \Exception('Module '.$name.' not found');
    }

    public function isModuleOnDisk($name)
    {
        if (file_exists(_PS_MODULE_DIR_.$name.'/'.$name.'.php')) {
            include_once(_PS_MODULE_DIR_.$name.'/'.$name.'.php');
            
            return (bool)\PrestaShop\PrestaShop\Adapter\ServiceLocator::get($name);
        }

        return false;
    }

    public function setModuleOnDiskFromAddons($name)
    {
        // Note : Data caching should be handled by the addons data provider

        $addons_provider = new AddonsDataProvider();
        // Check if the module can be downloaded from addons
        foreach ($this->getCatalogModules() as $catalog_module) {
            if ($catalog_module->name == $name) {
                return $addons_provider->downloadModule($catalog_module->id);
            }
        }

        return false;
    }

    protected function applyModuleFilters(array $filters)
    {
        if (! count($filters)) {
            return $this->catalog_modules;
        }

        $module_ids = array_keys($this->catalog_modules);

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

                    foreach (explode(' ', $value) as $keyword) {
                        if (empty($keyword)) {
                            continue;
                        }

                        // Instead of looping on the whole module list, we use $module_ids which can already be reduced
                        // thanks to the previous array_intersect(...)
                        foreach ($module_ids as $key) {
                            $module = $this->catalog_modules[$key];
                            if (strpos($module->displayName, $keyword) !== false
                                || strpos($module->name, $keyword) !== false
                                || strpos($module->description, $keyword) !== false) {
                                $search_result[] = $key;
                            }
                        }
                    }
                    break;
                default:
                    // "the switch statement is considered a looping structure for the purposes of continue."
                    continue 2;
            }

            $module_ids = array_intersect($search_result, $module_ids);
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

        $addons_provider = new AddonsDataProvider();

        if (!$this->catalog_categories || !$this->catalog_modules) {
            $params = ['format' => 'json'];
            $requests = ['must-have', 'service', 'partner', 'native', 'native_all'];
            if ($addons_provider->isAddonsAuthenticated()) {
                // customer is more important, so we set it at the beginning of the array
                array_unshift($requests, 'customer');
            }

            try {
                $jsons = [];
                // We execute each addons request
                foreach ($requests as $action) {
                    // We add the request name in each product returned by Addons,
                    // so we know whether is bought
                    $jsons = array_merge_recursive($jsons, array_map(function ($array) use ($action) {
                        foreach ($array as $elem) {
                            $elem->origin = $action;
                        }
                        return $array;
                    }, (array) $addons_provider->request($action, $params)));
                }

                $this->catalog_modules    = $this->convertJsonForNewCatalog($jsons);
                $this->catalog_categories = $this->getCategoriesFromModules();
                $this->registerModuleCache(self::_CACHEFILE_CATEGORIES_, $this->catalog_categories);
                $this->registerModuleCache(self::_CACHEFILE_MODULES_, $this->catalog_modules);
            } catch (\Exception $e) {
                if (! $this->fallbackOnCache()) {
                    throw new \Exception("Data from PrestaShop Addons is invalid, and cannot fallback on cache", 0, $e);
                }
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
                    $name
                );
            }

            $categories->categories->subMenu->{$ref}->modulesRef[] = $module_key;
        }

        return $categories;
    }

    protected function convertJsonForNewCatalog($original_json)
    {
        $remixed_json = [];
        $duplicates = [];

        foreach ($original_json as $json_key => $products) {
            foreach ($products as $product) {
                if (in_array($product->name, $duplicates)) {
                    continue;
                }

                $duplicates[] = $product->name;

                // Add un-implemented properties
                $product->refs       = (array)$this->getRefFromModuleCategoryName($product->categoryName);
                if (! isset($product->product_type)) {
                    $product->productType = isset($json_key)?rtrim($json_key, 's'):'module';
                } else {
                    $product->productType = $product->product_type;
                    unset($product->product_type);
                }
                $product->conditions = [];
                $product->rating     = (object)[
                        'score' => 0.0,
                        'countReviews' => 0,
                ];
                $product->scoring    = 0;
                $product->media      = (object)[
                        'img' => $product->img,
                        'badges' => isset($product->badges)?$product->badges:[],
                        'cover' => isset($product->cover)?$product->cover:[],
                        'screenshotsUrls' => [],
                        'videoUrl' => null,
                ];
                unset($product->badges);
                //unset($module->categoryName);
                unset($product->cover);

                $remixed_json[] = $product;
            }
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
        return str_replace([' '], ['_'], strtolower(\Tools::replaceAccentedChars($name)));
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
        $cacheFile = $this->cache_dir.$file;
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
            $cache = (new ConfigCacheFactory(true))->cache($this->cache_dir.$file, function () {});
            $cache->write(json_encode($data));

            return $cache->getPath();
        } catch (IOException $e) {
            throw new \Exception('Cannot write in the cache file '. $file, $e->getCode(), $e);
        }
    }
}
