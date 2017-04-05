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
namespace PrestaShop\PrestaShop\Core\Addon\Module;

use Doctrine\Common\Cache\CacheProvider;
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
use Symfony\Component\Translation\TranslatorInterface;

class ModuleRepository implements ModuleRepositoryInterface
{
    const NATIVE_AUTHOR = 'PrestaShop';

    const PARTNER_AUTHOR = 'PrestaShop Partners';

    /**
     * Admin Module Data Provider.
     *
     * @var \PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider
     */
    private $adminModuleProvider;

    /**
     * Logger.
     *
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Module Data Provider.
     *
     * @var \PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider
     */
    private $moduleProvider;

    /**
     * Module Data Provider.
     *
     * @var \PrestaShop\PrestaShop\Adapter\Module\ModuleDataUpdater
     */
    private $moduleUpdater;

    /**
     * Translator.
     *
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * Module Data Provider.
     *
     * @var \PrestaShop\PrestaShop\Adapter\Module\ModuleDataUpdater
     */
    private $cacheFilePath;

    /**
     * Contains data from cache file about modules on disk.
     *
     * @var array
     */
    private $cache = array();

    /**
     * Optionnal Doctrine cache provider
     *
     * @var \Doctrine\Common\Cache\CacheProvider
     */
    private $cacheProvider;

    public function __construct(
        AdminModuleDataProvider $adminModulesProvider,
        ModuleDataProvider $modulesProvider,
        ModuleDataUpdater $modulesUpdater,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        $languageISO,
        CacheProvider $cacheProvider = null
    ) {
        $this->adminModuleProvider = $adminModulesProvider;
        $this->logger = $logger;
        $this->moduleProvider = $modulesProvider;
        $this->moduleUpdater = $modulesUpdater;
        $this->translator = $translator;
        $this->finder = new Finder();

        // Cache related variables
        $this->cacheFilePath = $languageISO.'_local_modules';
        $this->cacheProvider = $cacheProvider;

        if ($this->cacheProvider && $this->cacheProvider->contains($this->cacheFilePath)) {
            $this->cache = $this->cacheProvider->fetch($this->cacheFilePath);
        }
    }

    public function __destruct()
    {
        if ($this->cacheProvider) {
            $this->cacheProvider->save($this->cacheFilePath, $this->cache);
        }
    }

    public function clearCache()
    {
        if ($this->cacheProvider) {
            $this->cacheProvider->delete($this->cacheFilePath);
        }
        $this->cache = array();
    }

    /**
     * Get the **Legacy** Module object from its name.
     *
     * @param string $name The technical module name to instanciate
     *
     * @return \Module|null Instance of legacy Module, if valid
     */
    public function getInstanceByName($name)
    {
        // Return legacy instance !
        return $this->getModule($name)->getInstance();
    }

    /**
     * @param AddonListFilter $filter
     *
     * @param bool            $skip_main_class_attributes
     *
     * @return AddonInterface[] retrieve a list of addons, regarding the $filter used
     */
    public function getFilteredList(AddonListFilter $filter, $skip_main_class_attributes = false)
    {
        if ($filter->status >= AddonListFilterStatus::ON_DISK
            && $filter->status != AddonListFilterStatus::ALL) {
            $modules = $this->getModulesOnDisk($skip_main_class_attributes);
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
                if (!isset($productType) || $productType & ~$filter->type) {
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
                    && !$filter->hasStatus(AddonListFilterStatus::DISABLED)
                    && $filter->hasStatus(AddonListFilterStatus::ENABLED)) {
                    unset($modules[$key]);
                    continue;
                }

                if ($module->database->get('installed') == 1
                    && $module->database->get('active') == 0
                    && !$filter->hasStatus(AddonListFilterStatus::ENABLED)
                    && $filter->hasStatus(AddonListFilterStatus::DISABLED)) {
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

    /**
     * @return AddonInterface[] retrieve the list of native modules
     */
    public function getNativeModules()
    {
        static $nativeModules = null;

        if (null === $nativeModules) {
            $filter = new AddonListFilter();
            $filter->setOrigin(AddonListFilterOrigin::ADDONS_NATIVE);

            $nativeModules = $this->getFilteredList($filter);

            foreach ($nativeModules as $key => $module) {
                $moduleAuthor = $module->attributes->get('author');
                if (self::NATIVE_AUTHOR !== $moduleAuthor) {
                    unset($nativeModules[$key]);
                }
            }
        }

        return $nativeModules;
    }

    /**
     * @return AddonInterface[] retrieve the list of partners modules
     */
    public function getPartnersModules()
    {
        $filter = new AddonListFilter();
        $filter->setOrigin(AddonListFilterOrigin::ADDONS_NATIVE);

        $partnersModules = $this->getFilteredList($filter);

        foreach ($partnersModules as $key => $module) {
            $moduleAuthor = $module->attributes->get('author');
            if (self::PARTNER_AUTHOR !== $moduleAuthor) {
                unset($partnersModules[$key]);
            }
        }

        return $partnersModules;
    }

    /**
     * @return AddonInterface[] get the list of installed partners modules
     */
    public function getInstalledPartnersModules()
    {
        $partnersModules = $this->getPartnersModules();

        foreach ($partnersModules as $key => $module) {
            if (1 !== $module->database->get('installed')) {
                unset($partnersModules[$key]);
            }
        }

        return $partnersModules;
    }

    /**
     * @return AddonInterface[] get the list of not installed partners modules
     */
    public function getNotInstalledPartnersModules()
    {
        $partnersModules = $this->getPartnersModules();

        foreach ($partnersModules as $key => $module) {
            if (0 !== $module->database->get('installed')) {
                unset($partnersModules[$key]);
            }
        }

        return $partnersModules;
    }

    private function getAddonsCatalogModules()
    {
        $modules = array();
        foreach ($this->adminModuleProvider->getCatalogModulesNames() as $name) {
            try {
                $module = $this->getModule($name);
                if ($module instanceof Module) {
                    $modules[$name] = $module;
                }
            } catch (\ParseError $e) {
                $this->logger->critical(
                    $this->translator->trans(
                        'Parse error on module %module%. %error_details%',
                        array(
                            '%module%' => $name,
                            '%error_details%' => $e->getMessage(), ),
                        'Admin.Modules.Notification'));
            } catch (Exception $e) {
                $this->logger->critical(
                    $this->translator->trans(
                        'Unexpected exception on module %module%. %error_details%',
                        array(
                            '%module%' => $name,
                            '%error_details%' => $e->getMessage(), ),
                        'Admin.Modules.Notification'));
            }
        }
        return $modules;
    }

    /**
     * Get the new module presenter class of the specified name provided.
     * It contains data from its instance, the disk, the database and from the marketplace if exists.
     *
     * @param string $name The technical name of the module
     *
     * @param bool   $skip_main_class_attributes
     *
     * @return Module
     */
    public function getModule($name, $skip_main_class_attributes = false)
    {
        $php_file_path = _PS_MODULE_DIR_.$name.'/'.$name.'.php';

        /* Data which design the module class */
        $attributes = array('name' => $name);
        $disk = array();
        $database = array();

        // Get filemtime of module main class (We do this directly with an error suppressor to go faster)
        $current_filemtime = (int) @filemtime($php_file_path);

        // We check that we have data from the marketplace
        try {
            $module_catalog_data = $this->adminModuleProvider->getCatalogModules(array('name' => $name));
            $attributes = array_merge(
                $attributes,
                (array) array_shift($module_catalog_data)
            );
        } catch (Exception $e) {
            $this->logger->alert(
                $this->translator->trans(
                    'Loading data from Addons failed. %error_details%',
                    array('%error_details%' => $e->getMessage()),
                    'Admin.Modules.Notification'));
        }

        // Now, we check that cache is up to date
        if (isset($this->cache[$name]['disk']['filemtime']) && $this->cache[$name]['disk']['filemtime']
            === $current_filemtime) {
            // OK, cache can be loaded and used directly

            $attributes = array_merge($attributes, $this->cache[$name]['attributes']);
            $disk = $this->cache[$name]['disk'];
        } else {
            // NOPE, we have to fulfil the cache with the module data

            $disk = array(
                'filemtime' => $current_filemtime,
                'is_present' => (int) $this->moduleProvider->isOnDisk($name),
                'is_valid' => 0,
                'version' => null,
            );
            $main_class_attributes = array();

            if (!$skip_main_class_attributes && $this->moduleProvider->isModuleMainClassValid($name)) {
                require_once $php_file_path;

                // We load the main class of the module, and get its properties
                $tmp_module = \PrestaShop\PrestaShop\Adapter\ServiceLocator::get($name);
                foreach (array('warning', 'name', 'tab', 'displayName', 'description', 'author', 'author_uri',
                    'limited_countries', 'need_instance', ) as $data_to_get) {
                    if (isset($tmp_module->{$data_to_get})) {
                        $main_class_attributes[$data_to_get] = $tmp_module->{$data_to_get};
                    }
                }

                $main_class_attributes['parent_class'] = get_parent_class($name);
                $main_class_attributes['is_configurable'] = (int) method_exists($tmp_module, 'getContent');

                $disk['is_valid'] = 1;
                $disk['version'] = $tmp_module->version;

                $attributes = array_merge($attributes, $main_class_attributes);
            } else if (!$skip_main_class_attributes) {
                $main_class_attributes['warning'] = 'Invalid module class';
            } else {
                $disk['is_valid'] = 1;
            }

            $this->cache[$name]['attributes'] = $main_class_attributes;
            $this->cache[$name]['disk'] = $disk;
        }

        foreach (array('logo.png', 'logo.gif') as $logo) {
            $logo_path = _PS_MODULE_DIR_.$name.DIRECTORY_SEPARATOR.$logo;
            if (file_exists($logo_path)) {
                $attributes['img'] = __PS_BASE_URI__.basename(_PS_MODULE_DIR_).'/'.$name.'/'.$logo;
                break;
            }
        }

        // Get data from database
        $database = $this->moduleProvider->findByName($name);

        return new Module($attributes, $disk, $database);
    }

    public function getModuleAttributes($name)
    {
        $module = $this->getModule($name);

        return $module->attributes;
    }

    /**
     * Send request to get module details on the marketplace, then merge the data received in Module instance
     * @param $moduleId
     * @return Module
     */
    public function getModuleById($moduleId)
    {
        $moduleAttributes = $this->adminModuleProvider->getModuleAttributesById($moduleId);
        $module = $this->getModule($moduleAttributes['name']);

        foreach ($moduleAttributes as $name => $value) {
            if (!$module->attributes->has($name)) {
                $module->attributes->set($name, $value);
            }
        }

        return $module;
    }

    /**
     * Instanciate every module present in the modules folder.
     *
     * @param bool $skip_main_class_attributes
     *
     * @return \PrestaShop\PrestaShop\Adapter\Module\Module[]
     */
    private function getModulesOnDisk($skip_main_class_attributes = false)
    {
        $modules = array();
        $modulesDirsList = $this->finder->directories()
            ->in(_PS_MODULE_DIR_)
            ->depth('== 0')
            ->exclude(array('__MACOSX'))
            ->ignoreVCS(true);

        foreach ($modulesDirsList as $moduleDir) {
            $moduleName = $moduleDir->getFilename();
            if (!file_exists(_PS_MODULE_DIR_.$moduleName.'/'.$moduleName.'.php')) {
                continue;
            }
            try {
                $module = $this->getModule($moduleName, $skip_main_class_attributes);
                if ($module instanceof Module) {
                    $modules[$moduleName] = $module;
                }
            } catch (\ParseError $e) {
                $this->logger->critical(
                    $this->translator->trans(
                        'Parse error detected in module %module%. %error_details%.',
                        array(
                            '%module%' => $moduleName,
                            '%error_details%' => $e->getMessage(), ),
                        'Admin.Modules.Notification'));
            } catch (Exception $e) {
                $this->logger->critical(
                    $this->translator->trans(
                        'Exception detected while loading module %module%. %error_details%.',
                        array(
                            '%module%' => $moduleName,
                            '%error_details%' => $e->getMessage(), ),
                        'Admin.Modules.Notification'));
            }
        }

        return $modules;
    }

    /**
     * Function loading all installed modules on the shop. Can be used as example for AddonListFilter use.
     * @return array
     */
    public function getInstalledModules()
    {
        $filters = new AddonListFilter();
        $filters->setType(AddonListFilterType::MODULE | AddonListFilterType::SERVICE)
            ->setStatus(AddonListFilterStatus::INSTALLED);

        return $this->getFilteredList($filters);
    }
}
