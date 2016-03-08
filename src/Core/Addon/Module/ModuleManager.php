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
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataUpdater;
use PrestaShop\PrestaShop\Core\Addon\AddonManagerInterface;
use Symfony\Component\Filesystem\Filesystem;

class ModuleManager implements AddonManagerInterface
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

    /**
     * Module Repository
     * @var \PrestaShop\PrestaShop\Adapter\Module\ModuleDataUpdater
     */
    private $moduleRepository;

    private $msgs_success;
    private $msgs_warning;
    private $msgs_error;


    public function __construct(AdminModuleDataProvider $adminModulesProvider,
        ModuleDataProvider $modulesProvider,
        ModuleDataUpdater $modulesUpdater,
        ModuleRepository $moduleRepository)
    {
        $this->adminModuleProvider = $adminModulesProvider;
        $this->moduleProvider = $modulesProvider;
        $this->moduleUpdater = $modulesUpdater;
        $this->moduleRepository = $moduleRepository;
        $this->msgs_success = $this->msgs_warning = $this->msgs_error = [];
    }

    /**
     * Add new module from zipball. This will unzip the file and move the content
     * to the right locations.
     * A theme can bundle modules, resources, docuementation, email templates and so on.
     *
     * @param string $source The source can be a module name (installed from either local disk or addons.prestashop.com).
     * or a location (url or path to the zip file)
     * @return bool true for success
     */
    public function install($name)
    {
        if ($this->moduleProvider->isInstalled($name)) {
            throw new Exception('This module is already installed');
        }

        if (! $this->moduleProvider->isOnDisk($name)) {
            $this->moduleUpdater->setModuleOnDiskFromAddons($name);
        }

        $module = $this->moduleRepository->getModule($name);
        return $module->onInstall();
    }

    /**
     * Remove all theme files, resources, documentation and specific modules
     *
     * @param string $name The source can be a module name (installed from either local disk or addons.prestashop.com).
     * or a location (url or path to the zip file)
     * @return bool true for success
     */
    public function uninstall($name, $file_deletion = false)
    {
        // Check permissions:
        // * Employee can delete
        // * Employee can delete this specific module

        // Is module installed ?
        if (! $this->moduleProvider->isInstalled($name)) {
            return false;
        }

        // Get module instance and uninstall it
        $module = $this->moduleRepository->getModule($name);
        try {
            $result = $module->onUninstall();
        } catch (Exception $e) {
            $this->msgs_error[$name][] = 'Error on module uninstallation. '. $e->getMessage();
            $result = false;
        }

        if ($result && (bool)$file_deletion) {
            $fs = new Filesystem();
            try {
                $fs->remove(_PS_MODULE_DIR_.$name);
            } catch (IOException $e) {
                $result &= false;
            }
        }

        return $result;
    }

    /**
    * Download new files from source, backup old files, replace files with new ones
    * and execute all necessary migration scripts form current version to the new one.
    *
    * @param Addon $name the theme you want to upgrade
    * @param string $version the version you want to up upgrade to
    * @param string $source if the upgrade is not coming from addons, you need to specify the path to the zipball
    * @return bool true for success
    */
    public function upgrade($name, $version = 'latest', $source = null)
    {
        if (! $this->moduleProvider->isInstalled($name)) {
            $this->msgs_error[$name][] = 'This module must be installed';
            return false;
        }

        // Get new module
        // 1- From source
        if ($source != null) {
        }
        // 2- From Addons
        else {
        }

        // If files properly inserted on the module folder
        Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'module` m
            SET m.version = \''.pSQL($version).'\'
            WHERE m.name = \''.pSQL($name).'\'');

        // Load and execute upgrade files
        return true;
    }

    /**
     * Disable a module without uninstalling it.
     * Allows the merchant to temporarly remove a module without uninstalling it
     *
     * @param  string $name The module name to disable
     * @return bool         True for success
     */
    public function disable($name)
    {
        $module = $this->moduleRepository->getModule($name);
        try {
            return $module->onDisable();
        } catch (Exception $e) {
            $this->msgs_error[$name][] = 'Error when disabling module. '. $e->getMessage();
            return false;
        }
    }

    /**
     * Enable a module previously disabled
     *
     * @param  string $name The module name to enable
     * @return bool         True for success
     */
    public function enable($name)
    {
        $module = $this->moduleRepository->getModule($name);
        try {
            return $module->onEnable();
        } catch (Exception $e) {
            $this->msgs_error[$name][] = 'Error when enabling module. '. $e->getMessage();
            return false;
        }
    }

    /**
     * Actions to perform to restaure default settings
     *
     * @param  string $name The theme name to reset
     * @return bool         True for success
     */
    public function reset($name, $keep_data = false)
    {
        $module = $this->moduleRepository->getModule($name);
        if ((bool)$keep_data && method_exists($this, 'reset')) {
            $status = $module->onReset();
        } else {
            $status = ($module->onUninstall() && $module->onInstall());
        }
        return $status;
    }
}
