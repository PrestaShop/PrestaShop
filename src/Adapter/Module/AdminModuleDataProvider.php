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
namespace PrestaShop\PrestaShop\Adapter\Module;

use PrestaShop\PrestaShop\Core\Addon\AddonListFilterOrigin;
use PrestaShopBundle\Service\DataProvider\Admin\AddonsInterface;
use PrestaShopBundle\Service\DataProvider\Admin\CategoriesProvider;
use PrestaShopBundle\Service\DataProvider\Admin\ModuleInterface;
use Symfony\Component\Config\ConfigCacheFactory;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Routing\Router;

/**
 * Data provider for new Architecture, about Module object model.
 *
 * This class will provide data from DB / ORM about Modules for the Admin interface.
 * This is an Adapter that works with the Legacy code and persistence behaviors.
 */
class AdminModuleDataProvider implements ModuleInterface
{
    const _CACHEFILE_MODULES_ = '_addons_modules.json';

    const _DAY_IN_SECONDS_ = 86400; /* Cache for One Day */

    private $languageISO;
    private $router;
    private $addonsDataProvider;
    private $categoriesProvider;
    private $cache_dir = _PS_CACHE_DIR_;
    protected $catalog_modules = array();
    protected $catalog_modules_names;

    public function __construct(
        $languageISO,
        Router $router = null,
        AddonsInterface $addonsDataProvider,
        CategoriesProvider $categoriesProvider
    ) {
        $this->languageISO = $languageISO;
        $this->router = $router;
        $this->addonsDataProvider = $addonsDataProvider;
        $this->categoriesProvider = $categoriesProvider;
    }

    public function clearCatalogCache()
    {
        $this->clearCache(array($this->languageISO.self::_CACHEFILE_MODULES_));
        $this->catalog_modules = array();
    }

    public function getAllModules()
    {
        return \Module::getModulesOnDisk(true,
            $this->addonsDataProvider->isAddonsAuthenticated(),
            (int) \Context::getContext()->employee->id
        );
    }

    public function getCatalogModules(array $filters = array())
    {
        if (count($this->catalog_modules) === 0) {
            $this->loadCatalogData();
        }

        return $this->applyModuleFilters(
                $this->catalog_modules, $filters
        );
    }

    public function getCatalogModulesNames(array $filter = array())
    {
        return array_keys($this->getCatalogModules($filter));
    }

    public function generateAddonsUrls(array $addons)
    {
        foreach ($addons as &$addon) {
            $urls = array();
            foreach (array('install', 'uninstall', 'enable', 'disable', 'enable_mobile', 'disable_mobile', 'reset', 'upgrade') as $action) {
                $urls[$action] = $this->router->generate('admin_module_manage_action', array(
                    'action' => $action,
                    'module_name' => $addon->attributes->get('name'),
                ));
            }
            $urls['configure'] = $this->router->generate('admin_module_configure_action', array(
                'module_name' => $addon->attributes->get('name'),
            ));

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
                        $urls['install']
                    );
                } else {
                    $url_active = 'disable';
                    unset(
                        $urls['install'],
                        $urls['enable'],
                        $urls['configure']
                    );
                }

                if ($addon->attributes->get('is_configurable') == 0) {
                    unset($urls['configure']);
                }

                if ($addon->canBeUpgraded()) {
                    $url_active = 'upgrade';
                } else {
                    unset(
                        $urls['upgrade']
                    );
                }
                if ($addon->database->get('active_on_mobile') == 0) {
                    unset($urls['disable_mobile']);
                } else {
                    unset($urls['enable_mobile']);
                }
                if (!$addon->canBeUpgraded()) {
                    unset(
                        $urls['upgrade']
                    );
                }
            } elseif (
                !$addon->attributes->has('origin') ||
                $addon->disk->get('is_present') == true ||
                in_array($addon->attributes->get('origin'), array('native', 'native_all', 'partner', 'customer'))
            ) {
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

            $categoryParent = $this->categoriesProvider->getParentCategory($addon->attributes->get('categoryName'));
            $addon->attributes->set('categoryParent', $categoryParent);
        }

        return $addons;
    }

    public function getModuleAttributesById($moduleId)
    {
        return (array) $this->addonsDataProvider->request('module', array('id_module' => $moduleId));
    }

    protected function applyModuleFilters(array $modules, array $filters)
    {
        if (!count($filters)) {
            return $modules;
        }

        // We get our module IDs to keep
        foreach ($filters as $filter_name => $value) {
            $search_result = array();

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
            $path = $this->cache_dir.$file;
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }

    protected function loadCatalogData()
    {
        $this->catalog_modules = $this->getModuleCache($this->languageISO.self::_CACHEFILE_MODULES_);

        if (!$this->catalog_modules) {
            $params = array('format' => 'json');
            $requests = array(
                AddonListFilterOrigin::ADDONS_MUST_HAVE => 'must-have',
                AddonListFilterOrigin::ADDONS_SERVICE => 'service',
                AddonListFilterOrigin::ADDONS_NATIVE => 'native',
                AddonListFilterOrigin::ADDONS_NATIVE_ALL => 'native_all',
            );
            if ($this->addonsDataProvider->isAddonsAuthenticated()) {
                $requests[AddonListFilterOrigin::ADDONS_CUSTOMER] = 'customer';
            }

            try {
                $listAddons = array();
                // We execute each addons request
                foreach ($requests as $action_filter_value => $action) {
                    if (!$this->addonsDataProvider->isAddonsUp()) {
                        continue;
                    }
                    // We add the request name in each product returned by Addons,
                    // so we know whether is bought

                    $addons = $this->addonsDataProvider->request($action, $params);
                    foreach ($addons as $addonsType => $addon) {
                        $addon->origin = $action;
                        $addon->origin_filter_value = $action_filter_value;
                        $addon->categoryParent = $this->categoriesProvider
                            ->getParentCategory($addon->categoryName)
                        ;
                        if (! isset($addon->product_type)) {
                            $addon->productType = isset($addonsType)?rtrim($addonsType, 's'):'module';
                        } else {
                            $addon->productType = $addon->product_type;
                        }
                        $listAddons[$addon->name] = $addon;
                    }
                }

                $this->catalog_modules = $listAddons;
                $this->registerModuleCache($this->languageISO.self::_CACHEFILE_MODULES_, $this->catalog_modules);
            } catch (\Exception $e) {
                if (!$this->fallbackOnCatalogCache()) {
                    $this->catalog_modules = array();
                    throw new \Exception('Data from PrestaShop Addons is invalid, and cannot fallback on cache', 0, $e);
                }
            }
        }
    }

    protected function fallbackOnCatalogCache()
    {
        // Fallback on data from cache if exists
        $this->catalog_modules = $this->getModuleCache(self::_CACHEFILE_MODULES_, false);

        return $this->catalog_modules;
    }

    private function getModuleCache($file, $checkFreshness = true)
    {
        $cacheFile = $this->cache_dir.$file;

        if (!file_exists($cacheFile)) {
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

            $labeledCache = array();
            // We need to loop in the array to replace the current key, which is an integer, with the module name
            foreach (json_decode($cache) as $element) {
                $labeledCache[$element->name] = $element;
            }

            return $labeledCache;
        } catch (\Exception $e) {
            throw new \Exception('Cannot read from the cache file '.$file);
        }
    }

    private function registerModuleCache($file, $data)
    {
        try {
            $cache = (new ConfigCacheFactory(true))->cache($this->cache_dir.$file, function () {
            });
            $cache->write(json_encode($data));

            return $cache->getPath();
        } catch (IOException $e) {
            throw new \Exception('Cannot write in the cache file '.$file, $e->getCode(), $e);
        }
    }
}
