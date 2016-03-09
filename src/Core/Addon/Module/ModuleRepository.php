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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Addon\Module;

use Exception;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataUpdater;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilter;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterOrigin;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterStatus;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterType;
use PrestaShop\PrestaShop\Core\Addon\AddonRepositoryInterface;

class ModuleRepository implements AddonRepositoryInterface
{
    /**
     * Admin Module Data Provider
     * @var \PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider
     */
    private $adminModuleProvider;

    /**
     * Module Data Provider
     * @var \PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider
     */
    private $moduleProvider;

    /**
     * Module Data Provider
     * @var \PrestaShop\PrestaShop\Adapter\Module\ModuleDataUpdater
     */
    private $moduleUpdater;

    const CACHE_FILE = _PS_CACHE_DIR_.'modules.json';

    /**
     * Contains data from cache file about modules on disk
     *
     * @var array $cache
     */
    private $cache;

    public function __construct(AdminModuleDataProvider $adminModulesProvider,
                                ModuleDataProvider $modulesProvider,
                                ModuleDataUpdater $modulesUpdater)
    {
        $this->adminModuleProvider = $adminModulesProvider;
        $this->moduleProvider      = $modulesProvider;
        $this->moduleUpdater       = $modulesUpdater;
        $this->cache               = $this->readCacheFile();
    }

    public function __destruct()
    {
        $this->generateCacheFile($this->cache);
    }

    /**
     * Get the Module object from its name
     * = findByName($name)
     *
     * @param  string $name The technical module name to instanciate
     * @return \PrestaShop\PrestaShop\Adapter\Module\Module|null         Instance of legacy Module, if valid
     */
    public function getInstanceByName($name)
    {
        // Return legacy instance !
        return $this->getModule($name)->getInstance();
    }

    /**
     * @param AddonListFilter $filter
     * @return AddonInterface[] retrieve a list of addons, regarding the $filter used
     */
    public function getFilteredList(AddonListFilter $filter)
    {
        // Init missing values
        if (empty($filter->type)) {
            $filter->setType(AddonListFilterType::MODULE);
        }
        if (empty($filter->status)) {
            $filter->setStatus(AddonListFilterStatus::ALL);
        }
        if (empty($filter->origin)) {
            $filter->setOrigin(AddonListFilterOrigin::ALL);
        }

        if ($filter->status >= AddonListFilterStatus::ON_DISK
            && $filter->status != AddonListFilterStatus::ALL) {
            $modules = $this->getModulesOnDisk();
        } else {
            $modules = $this->getList();
        }

        foreach ($modules as $key => &$module) {

            // Part One : Removing addons not related to the selected product type
            if ($module->attributes->get('productType') == 'Module') {
                $productType = AddonListFilterType::MODULE;
            }
            if ($module->attributes->get('productType') == 'Service') {
                $productType = AddonListFilterType::SERVICE;
            }
            if (isset($productType) && $productType & ~ $filter->type) {
                unset($modules[$key]);
                continue;
            }

            // Part Two : Remove module not installed if specified
            if ($filter->status != AddonListFilterStatus::ALL) {
                if ($module->database->get('installed') == 1 && $filter->status & ~ AddonListFilterStatus::INSTALLED) {
                    unset($modules[$key]);
                    continue;
                }

                if ($module->database->get('installed') == 0 && $filter->status & AddonListFilterStatus::INSTALLED) {
                    unset($modules[$key]);
                    continue;
                }

                if ($module->database->get('installed') == 1
                    && $module->database->get('active') == 1
                    && $filter->status & ~ AddonListFilterStatus::ENABLED) {
                    unset($modules[$key]);
                    continue;
                }

                if ($module->database->get('installed') == 1
                    && $module->database->get('active') == 0
                    && $filter->status & AddonListFilterStatus::ENABLED) {
                    unset($modules[$key]);
                    continue;
                }
            }

            // Part Three : Remove addons not related to the proper source (ex Addons)
            if (!$module->attributes->has('origin_filter_value') &&
                ~ $filter->origin & AddonListFilterOrigin::DISK
            ) {
                unset($modules[$key]);
                continue;
            }
            if ($module->attributes->has('origin_filter_value') &&
                !($module->attributes->get('origin_filter_value') & $filter->origin)) {
                unset($modules[$key]);
                continue;
            }
        }
        return $modules;
    }

    /**
     * @return AddonInterface[] retrieve the universe of Modules
     */
    public function getList()
    {
        return array_merge($this->getAddonsCatalogModules(),
            $this->getModulesOnDisk());
    }

    private function getAddonsCatalogModules()
    {
        $modules = [];
        foreach ($this->adminModuleProvider->getCatalogModulesNames() as $name) {
            try {
                $module = $this->getModule($name);
                if ($module instanceof Module) {
                    $modules[$name] = $module;
                }
            } catch (\ParseError $e) {
                // Bypass this module
            } catch (Exception $e) {
                // Bypass this module
            }
        }

        return $modules;
    }

    public function getModule($name)
    {
        $php_file_path = _PS_MODULE_DIR_.$name.'/'.$name.'.php';

        /* Data which design the module class */
        $attributes = ['name' => $name,];
        $disk       = [];
        $database   = [];

        // Get filemtime of module main class (We do this directly with an error suppressor to go faster)
        $current_filemtime = (int)@filemtime($php_file_path);

        // Check that cache is up to date
        if (isset($this->cache[$name]['disk']['filemtime']) && $this->cache[$name]['disk']['filemtime']
            === $current_filemtime) {
            // OK, cache can be loaded and used directly

            $attributes = $this->cache[$name]['attributes'];
            $disk       = $this->cache[$name]['disk'];
        } else {
            // NOPE, we have to fulfil the cache with the module data

            $disk = [
                'filemtime' => $current_filemtime,
                'is_present' => (int)$this->moduleProvider->isOnDisk($name),
                'is_valid' => 0,
                'version' => null,
            ];

            $module_catalog_data = $this->adminModuleProvider->getCatalogModules(['name' => $name]);

            $attributes = array_merge($attributes,
                (array)array_shift($module_catalog_data));

            if ($this->moduleProvider->isModuleMainClassValid($name)) {
                require_once $php_file_path;

                // We load the main class of the module, and get its properties
                $tmp_module = \PrestaShop\PrestaShop\Adapter\ServiceLocator::get($name);
                $attributes = array_merge($attributes,
                    [
                    'warning' => $tmp_module->warning,
                    'name' => $tmp_module->name,
                    'tab' => $tmp_module->tab,
                    'displayName' => $tmp_module->displayName,
                    'description' => stripslashes($tmp_module->description),
                    'author' => $tmp_module->author,
                    'author_uri' => (isset($tmp_module->author_uri) && $tmp_module->author_uri)
                            ?$tmp_module->author_uri:false,
                    'limited_countries' => $tmp_module->limited_countries,
                    'parent_class' => get_parent_class($name),
                    'is_configurable' => $tmp_module->is_configurable = method_exists($tmp_module,
                        'getContent')?1:0,
                    'need_instance' => isset($tmp_module->need_instance)?$tmp_module->need_instance
                            :0,
                    'productType' => 'Module',
                ]);

                if (!isset($attributes['refs'])) {
                    $attributes['refs'] = ['unknown'];
                }

                if (!isset($attributes['media'])) {
                    $attributes['media'] = (object)[
                            'img' => '../../img/questionmark.png',
                            'badges' => [],
                            'cover' => [],
                            'screenshotsUrls' => [],
                            'videoUrl' => null,
                    ];
                }
                if (!isset($attributes['price'])) {
                    $attributes['price']      = new \stdClass;
                    $attributes['price']->EUR = 0;
                    $attributes['price']->USD = 0;
                    $attributes['price']->GBP = 0;
                }

                foreach (['logo.png', 'logo.gif'] as $logo) {
                    $logo_path = _PS_MODULE_DIR_.$tmp_module->name.DIRECTORY_SEPARATOR.$logo;
                    if (file_exists($logo_path)) {
                        $attributes['media']->img = __PS_BASE_URI__.basename(_PS_MODULE_DIR_).'/'.$tmp_module->name.'/'.$logo;
                        break;
                    }
                }
                $disk['is_valid'] = 1;
                $disk['version'] = $tmp_module->version;
            }

            $this->cache[$name]['attributes'] = $attributes;
            $this->cache[$name]['disk']       = $disk;
        }

        // Get data from database
        $database = $this->moduleProvider->findByName($name);

        return new Module($attributes, $disk, $database);
    }

    private function getModulesOnDisk()
    {
        $modules         = [];
        $all_module_dirs = glob(_PS_MODULE_DIR_.'*', GLOB_ONLYDIR);

        foreach ($all_module_dirs as $dir) {
            $name = basename($dir);
            try {
                $module = $this->getModule($name);
                if ($module instanceof Module) {
                    $modules[$name] = $module;
                }
            } catch (\ParseError $e) {
                // Bypass this module
            } catch (Exception $e) {
                // Bypass this module
            }
        }
        return $modules;
    }
    /*
     * PROTECTED FUNCTIONS
     */

    /**
     * In order to avoid class parsing, we generate a cache file which will keep mandatory data of modules
     *
     * @param  string $name The technical module name to find
     * @return array         Module data stored in file
     */
    private function generateCacheFile($data)
    {
        $yml_file_path = self::CACHE_FILE; //_PS_MODULE_DIR_.$name.'/'.$name.'.yml';
        /* $dumper = new Dumper();

          $yaml = $dumper->dump($data, 3); */
        $yaml          = json_encode($data);

        file_put_contents($yml_file_path, $yaml);

        return $data;
    }

    /**
     * We load the file which contains cached data
     *
     * @return array         Module data loaded in file
     */
    private function readCacheFile()
    {
        $file_path = self::CACHE_FILE;

        // YML file not found ? Generate it
        if (!file_exists($file_path)) {
            return [];
        }
        $data = json_decode(file_get_contents($file_path), true);
        return ($data == null)?[]:$data;
    }
}
