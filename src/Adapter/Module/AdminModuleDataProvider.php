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
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterOrigin;
use PrestaShopBundle\Service\DataProvider\Admin\ModuleInterface;
use Symfony\Component\Config\ConfigCacheFactory;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Routing\Router;

/**
 * Data provider for new Architecture, about Module object model.
 *
 * This class will provide data from DB / ORM about Modules for the Admin interface.
 * This is an Adapter that works with the Legacy code and persistence behaviors.
 *
 */
class AdminModuleDataProvider implements ModuleInterface
{
    const _CACHEFILE_MODULES_ = '_catalog_modules.json';
    const _DAY_IN_SECONDS_ = 86400; /* Cache for One Day */

    private $languageISO;
    private $router;
    private $addonsDataProvider;
    private $cache_dir = _PS_CACHE_DIR_;
    protected $catalog_modules = [];
    protected $catalog_modules_names;

    public function __construct($languageISO, Router $router = null, AddonsDataProvider $addonsDataProvider)
    {
        $this->languageISO = $languageISO;
        $this->router = $router;
        $this->addonsDataProvider = $addonsDataProvider;
    }

    public function clearCatalogCache()
    {
        $this->clearCache([$this->languageISO.self::_CACHEFILE_MODULES_]);
        $this->catalog_modules = [];
    }

    public function getAllModules()
    {
        return \Module::getModulesOnDisk(true,
            $this->addonsDataProvider->isAddonsAuthenticated(),
            (int)\Context::getContext()->employee->id
        );
    }

    public function getCatalogModules(array $filters = [])
    {
        if (count($this->catalog_modules) === 0) {
            $this->loadCatalogData();
        }

        return $this->applyModuleFilters(
                $this->catalog_modules, $filters
        );
    }

    public function getCatalogModulesNames(array $filter = [])
    {
        return array_keys($this->getCatalogModules($filter));
    }

    public function generateAddonsUrls(array $addons)
    {
        foreach ($addons as &$addon) {
            $urls = [];
            foreach (['install', 'uninstall', 'enable', 'disable', 'enable_mobile', 'disable_mobile', 'reset', 'upgrade'] as $action) {
                $urls[$action] = $this->router->generate('admin_module_manage_action', [
                    'action' => $action,
                    'module_name' => $addon->attributes->get('name'),
                ]);
            }
            $urls['configure'] = $this->router->generate('admin_module_configure_action', [
                'module_name' => $addon->attributes->get('name'),
            ]);

            // Which button should be displayed first ?
            $url_active = '';
            if ($addon->database->has('installed') && $addon->database->get('installed') == 1) {
                if ($addon->database->get('active') == 0) {
                    $url_active = 'enable';
                    unset(
                        $urls['install'],
                        $urls['disable']
                    );
                } elseif ($addon->attributes->get('is_configurable') == 1) {
                    $url_active = 'configure';
                    unset(
                        $urls['enable'],
                        $urls['install'],
                        $urls['upgrade']
                    );
                } else {
                    $url_active = 'disable';
                    unset(
                        $urls['upgrade'],
                        $urls['install'],
                        $urls['enable'],
                        $urls['configure']
                    );
                }
                if ($addon->database->get('active_on_mobile') == 0) {
                    unset($urls['disable_mobile']);
                } else {
                    unset($urls['enable_mobile']);
                }
                if ($addon->database->get('installed') == 0 || version_compare($addon->database->get('version'), $addon->disk->get('version'), '<=')
                    && version_compare($addon->attributes->get('version'), $addon->database->get('version'), '<=')) {
                    unset(
                        $urls['upgrade']
                    );
                }
            } elseif (!$addon->attributes->has('origin') || in_array($addon->attributes->get('origin'), ['native', 'native_all', 'partner', 'customer'])) {
                $url_active = 'install';
                unset(
                    $urls['uninstall'],
                    $urls['enable'],
                    $urls['disable'],
                    $urls['enable_mobile'],
                    $urls['disable_mobile'],
                    $urls['reset'],
                    $urls['upgrade'],
                    $urls['configure']
                );
            } else {
                $url_active = 'buy';
            }
            if (count($urls)) {
                $addon->attributes->set('urls', $urls);
            }
            $addon->attributes->set('url_active', $url_active);
        }

        return $addons;
    }

    public function getCategoriesFromModules(&$modules)
    {
        $categories = [];

        // Only Tab: Categories
        $categories['categories'] = $this->createMenuObject('categories',
            'Categories');

        foreach ($modules as &$module) {
            $refs = [];
            foreach ($module->attributes->get('refs') as $key => $name) {
                $ref  = $this->getRefFromModuleCategoryName($name);

                if (!isset($categories['categories']->subMenu[$ref])) {
                    $categories['categories']->subMenu[$ref] = $this->createMenuObject($ref,
                        $name
                    );
                }

                $categories['categories']->subMenu[$ref]->modulesRef[] = $module->attributes->get('name');
                $refs[] = $ref;
            }
            $module->attributes->set('refs', $refs);
        }

        usort($categories['categories']->subMenu, function ($a, $b) {
            return strcmp($a->name, $b->name);
        });

        return $categories;
    }

    protected function applyModuleFilters(array $modules, array $filters)
    {
        if (! count($filters)) {
            return $modules;
        }

        // We get our module IDs to keep
        foreach ($filters as $filter_name => $value) {
            $search_result = [];

            switch ($filter_name) {
                case 'search':
                    // We build our results array.
                    // We could remove directly the non-matching modules, but we will give that for the final loop of this function

                    foreach (explode(' ', $value) as $keyword) {
                        if (empty($keyword)) {
                            continue;
                        }

                        // Instead of looping on the whole module list, we use $module_ids which can already be reduced
                        // thanks to the previous array_intersect(...)
                        foreach ($modules as $key => $module) {
                            if (strpos($module->displayName, $keyword) !== false
                                || strpos($module->name, $keyword) !== false
                                || strpos($module->description, $keyword) !== false) {
                                $search_result[] = $key;
                            }
                        }
                    }
                    break;
                case 'name':
                    // exact given name (should return 0 or 1 result)
                    $search_result[] = $value;
                    break;
                default:
                    // "the switch statement is considered a looping structure for the purposes of continue."
                    continue 2;
            }

            $modules = array_intersect_key($modules, array_flip($search_result));
        }

        return $modules;
    }

    protected function clearCache(array $files)
    {
        foreach ($files as $file) {
            $path = $this->cache_dir . $file;
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }

    protected function loadCatalogData()
    {
        $this->catalog_modules    = $this->getModuleCache($this->languageISO.self::_CACHEFILE_MODULES_);

        if (!$this->catalog_modules) {
            $params = ['format' => 'json'];
            $requests = [
                AddonListFilterOrigin::ADDONS_MUST_HAVE => 'must-have',
                AddonListFilterOrigin::ADDONS_SERVICE => 'service',
                AddonListFilterOrigin::ADDONS_NATIVE => 'native',
                AddonListFilterOrigin::ADDONS_NATIVE_ALL => 'native_all'
            ];
            if ($this->addonsDataProvider->isAddonsAuthenticated()) {
                $requests[AddonListFilterOrigin::ADDONS_CUSTOMER] = 'customer';
            }

            try {
                $jsons = [];
                // We execute each addons request
                foreach ($requests as $action_filter_value => $action) {
                    if (!$this->addonsDataProvider->isAddonsUp()) {
                        continue;
                    }
                    // We add the request name in each product returned by Addons,
                    // so we know whether is bought
                    $jsons = array_merge_recursive($jsons, array_map(function ($array) use ($action_filter_value, $action) {
                        foreach ($array as $elem) {
                            $elem->origin = $action;
                            $elem->origin_filter_value = $action_filter_value;
                        }
                        return $array;
                    }, (array) $this->addonsDataProvider->request($action, $params)));
                }

                $this->catalog_modules    = $this->convertJsonForNewCatalog($jsons);
                $this->registerModuleCache($this->languageISO.self::_CACHEFILE_MODULES_, $this->catalog_modules);
            } catch (\Exception $e) {
                if (! $this->fallbackOnCatalogCache()) {
                    $this->catalog_modules = [];
                    throw new \Exception("Data from PrestaShop Addons is invalid, and cannot fallback on cache", 0, $e);
                }
            }
        }
    }

    protected function convertJsonForNewCatalog($original_json)
    {
        $remixed_json = [];
        $duplicates = [];

        foreach ($original_json as $json_key => $products) {
            foreach ($products as $product) {
                if (is_array($product)) {
                    $product = (object)$product;
                }

                if (in_array($product->name, $duplicates)) {
                    continue;
                }

                $duplicates[] = $product->name;

                // Add un-implemented properties
                $product->refs       = (array)(!empty($product->categoryName)
                    ?$product->categoryName
                    :'unknown'
                );
                if (! isset($product->product_type)) {
                    $product->productType = isset($json_key)?rtrim($json_key, 's'):'module';
                } else {
                    $product->productType = $product->product_type;
                }

                $remixed_json[$product->name] = $product;
            }
        }

        usort($remixed_json, function ($module1, $module2) {
            return strnatcasecmp($module1->displayName, $module2->displayName);
        });

        return $remixed_json;
    }

    protected function createMenuObject($ref, $name)
    {
        return (object)[
                'name' => $name,
                'refMenu' => $ref,
                'subMenu' => [],
                'modulesRef' => [],
        ];
    }

    protected function getRefFromModuleCategoryName($name)
    {
        return str_replace([' ', '(', ')'], ['_', '', ''], strtolower(\Tools::replaceAccentedChars($name)));
    }

    protected function fallbackOnCatalogCache()
    {
        // Fallback on data from cache if exists
        $this->catalog_modules    = $this->getModuleCache(self::_CACHEFILE_MODULES_, false);

        return ($this->catalog_modules);
    }

    private function getModuleCache($file, $checkFreshness = true)
    {
        $cacheFile = $this->cache_dir.$file;
        if (! file_exists($cacheFile)) {
            return false;
        }

        try {
            if ($checkFreshness && (filemtime($cacheFile) + self::_DAY_IN_SECONDS_) <= time()) {
                return false;
            }

            $fh = fopen($cacheFile, 'r');
            $cache = trim(fgets($fh));

            if (!$cache) {
                return false;
            }

            $labeledCache = [];
            // We need to loop in the array to replace the current key, which is an integer, with the module name
            foreach (json_decode($cache) as $element) {
                $labeledCache[$element->name] = $element;
            }
            return $labeledCache;
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
