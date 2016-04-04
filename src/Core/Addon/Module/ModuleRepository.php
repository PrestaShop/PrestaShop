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
use Psr\Log\LoggerInterface;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataUpdater;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilter;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterOrigin;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterStatus;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterType;
use Symfony\Component\Finder\Finder;

class ModuleRepository implements ModuleRepositoryInterface
{
    /**
     * Admin Module Data Provider
     * @var \PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider
     */
    private $adminModuleProvider;

    /**
     * Logger
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

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

    /**
     * Module Data Provider
     * @var \PrestaShop\PrestaShop\Adapter\Module\ModuleDataUpdater
     */
    private $cacheFilePath;

    /**
     * Contains data from cache file about modules on disk
     *
     * @var array $cache
     */
    private $cache;

    public function __construct(
        AdminModuleDataProvider $adminModulesProvider,
        ModuleDataProvider $modulesProvider,
        ModuleDataUpdater $modulesUpdater,
        LoggerInterface $logger
    ) {
        $this->adminModuleProvider = $adminModulesProvider;
        $this->logger = $logger;
        $this->moduleProvider      = $modulesProvider;
        $this->moduleUpdater       = $modulesUpdater;
        $this->finder              = new Finder();
        $this->cacheFilePath       = _PS_CACHE_DIR_.'modules.json';
        $this->cache               = $this->readCacheFile();
    }

    public function __destruct()
    {
        $this->generateCacheFile($this->cache);
    }

    public function clearCache()
    {
        @unlink($this->cacheFilePath);
        $this->cache = [];
    }

    /**
     * Get the **Legacy** Module object from its name
     *
     * @param  string $name The technical module name to instanciate
     * @return \Module|null         Instance of legacy Module, if valid
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
        if ($filter->status >= AddonListFilterStatus::ON_DISK
            && $filter->status != AddonListFilterStatus::ALL) {
            $modules = $this->getModulesOnDisk();
        } else {
            $modules = $this->getList();
        }

        foreach ($modules as $key => &$module) {

            // Part One : Removing addons not related to the selected product type
            if ($filter->type != AddonListFilterType::ALL) {
                if ($module->attributes->get('productType') == 'module') {
                    $productType = AddonListFilterType::MODULE;
                }
                if ($module->attributes->get('productType') == 'service') {
                    $productType = AddonListFilterType::SERVICE;
                }
                if (!isset($productType) || $productType & ~ $filter->type) {
                    unset($modules[$key]);
                    continue;
                }
            }

            // Part Two : Remove module not installed if specified
            if ($filter->status != AddonListFilterStatus::ALL) {
                if ($module->database->get('installed') == 1
                    && ($filter->hasStatus(AddonListFilterStatus::UNINSTALLED)
                    || !$filter->hasStatus(AddonListFilterStatus::INSTALLED))) {
                    unset($modules[$key]);
                    continue;
                }

                if ($module->database->get('installed') == 0
                    && (!$filter->hasStatus(AddonListFilterStatus::UNINSTALLED)
                    || $filter->hasStatus(AddonListFilterStatus::INSTALLED))) {
                    unset($modules[$key]);
                    continue;
                }

                if ($module->database->get('installed') == 1
                    && $module->database->get('active') == 1
                    && !$filter->hasStatus(AddonListFilterStatus::DISABLED)) {
                    unset($modules[$key]);
                    continue;
                }

                if ($module->database->get('installed') == 1
                    && $module->database->get('active') == 0
                    && !$filter->hasStatus(AddonListFilterStatus::ENABLED)) {
                    unset($modules[$key]);
                    continue;
                }
            }

            // Part Three : Remove addons not related to the proper source (ex Addons)
            if ($filter->origin != AddonListFilterOrigin::ALL) {
                if (!$module->attributes->has('origin_filter_value') &&
                    !$filter->hasOrigin(AddonListFilterOrigin::DISK)
                ) {
                    unset($modules[$key]);
                    continue;
                }
                if ($module->attributes->has('origin_filter_value') &&
                    !$filter->hasOrigin($module->attributes->get('origin_filter_value'))
                ) {
                    unset($modules[$key]);
                    continue;
                }
            }
        }
        return $modules;
    }

    /**
     * @return AddonInterface[] retrieve the universe of Modules
     */
    public function getList()
    {
        return array_merge(
            $this->getAddonsCatalogModules(),
            $this->getModulesOnDisk()
        );
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
                $this->logger->critical(sprintf('Parse error on module %s. %s', $name, $e->getMessage()));
            } catch (Exception $e) {
                $this->logger->critical(sprintf('Unexpected exception on module %s. %s', $name, $e->getMessage()));
            }
        }

        return $modules;
    }

    /**
     * Get the new module presenter class of the specified name provided.
     * It contains data from its instance, the disk, the database and from the marketplace if exists.
     *
     * @param string $name The technical name of the module
     * @return \PrestaShop\PrestaShop\Adapter\Module\Module
     */
    public function getModule($name)
    {
        $php_file_path = $this->getModulesDir().$name.'/'.$name.'.php';

        /* Data which design the module class */
        $attributes = ['name' => $name,];
        $disk       = [];
        $database   = [];

        // Get filemtime of module main class (We do this directly with an error suppressor to go faster)
        $current_filemtime = (int)@filemtime($php_file_path);

        // We check that we have data from the marketplace
        try {
            $module_catalog_data = $this->adminModuleProvider->getCatalogModules(['name' => $name]);
            $attributes = array_merge(
                $attributes,
                (array)array_shift($module_catalog_data)
            );
        } catch (Exception $e) {
            $this->logger->alert(sprintf('Loading data from Addons failed. %s', $e->getMessage()));
        }

        // Now, we check that cache is up to date
        if (isset($this->cache[$name]['disk']['filemtime']) && $this->cache[$name]['disk']['filemtime']
            === $current_filemtime) {
            // OK, cache can be loaded and used directly

            $attributes = array_merge($attributes, $this->cache[$name]['attributes']);
            $disk       = $this->cache[$name]['disk'];
        } else {
            // NOPE, we have to fulfil the cache with the module data

            $disk = [
                'filemtime' => $current_filemtime,
                'is_present' => (int)$this->moduleProvider->isOnDisk($name),
                'is_valid' => 0,
                'version' => null,
            ];

            if ($this->moduleProvider->isModuleMainClassValid($name)) {
                require_once $php_file_path;

                // We load the main class of the module, and get its properties
                $tmp_module = \PrestaShop\PrestaShop\Adapter\ServiceLocator::get($name);
                $main_class_attributes = [
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
                    'is_configurable' => $tmp_module->is_configurable = method_exists(
                        $tmp_module,
                        'getContent'
                        ) ? 1 : 0,
                    'need_instance' => isset($tmp_module->need_instance)?$tmp_module->need_instance
                            :0,
                    'productType' => 'module',
                ];

                $disk['is_valid'] = 1;
                $disk['version'] = $tmp_module->version;

                $this->cache[$name]['attributes'] = $main_class_attributes;
                $this->cache[$name]['disk']       = $disk;

                $attributes = array_merge($attributes, $main_class_attributes);
            } else {
                $attributes['warning'] = 'Invalid module class';
            }
        }

        // ToDo: We need to remove all the parts from this function.
        // A new class will be created in order to
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
        } else {
            $attributes['media'] = (object)$attributes['media'];
        }

        if (!isset($attributes['price'])) {
            $attributes['price']      = new \stdClass;
            $attributes['price']->EUR = 0;
            $attributes['price']->USD = 0;
            $attributes['price']->GBP = 0;
        }

        if (!isset($attributes['version'])) {
            $attributes['version'] = !empty($disk['version'])?$disk['version']:null;
        }

        foreach (['logo.png', 'logo.gif'] as $logo) {
            $logo_path = $this->getModulesDir().$name.DIRECTORY_SEPARATOR.$logo;
            if (file_exists($logo_path)) {
                $attributes['media']->img = __PS_BASE_URI__.basename($this->getModulesDir()).'/'.$name.'/'.$logo;
                break;
            }
        }

        // Get data from database
        $database = $this->moduleProvider->findByName($name);

        return new Module($attributes, $disk, $database);
    }

    /**
     * Function which returns the modules directory. Used for mock in tests/ folder.
     * @return string The modules directory
     */
    protected function getModulesDir()
    {
        return _PS_MODULE_DIR_;
    }

    /**
     * Instanciate every module present if the modules folder
     *
     * @return \PrestaShop\PrestaShop\Adapter\Module\Module[]
     */
    private function getModulesOnDisk()
    {
        $modules         = [];
        $modulesDirsList = $this->finder->directories()
                                    ->in($this->getModulesDir())
                                    ->depth('== 0')
                                    ->exclude(['__MACOSX'])
                                    ->ignoreVCS(true);

        $modulesDirsList = iterator_to_array($modulesDirsList);

        foreach ($modulesDirsList as $moduleDir) {
            $moduleName = $moduleDir->getFilename();
            try {
                $module = $this->getModule($moduleName);
                if ($module instanceof Module) {
                    $modules[$moduleName] = $module;
                }
            } catch (\ParseError $e) {
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
        $encoded_data          = json_encode($data);
        file_put_contents($this->cacheFilePath, $encoded_data);

        return $data;
    }

    /**
     * We load the file which contains cached data
     *
     * @return array         Module data loaded in file
     */
    private function readCacheFile()
    {
        // JSON file not found ? Generate it
        if (!file_exists($this->cacheFilePath)) {
            return [];
        }
        $data = json_decode(file_get_contents($this->cacheFilePath), true);
        return ($data == null)?[]:$data;
    }
}
