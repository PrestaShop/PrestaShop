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
namespace PrestaShop\PrestaShop\Core\Addon\Module;

use Employee;
use Exception;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataUpdater;
use PrestaShop\PrestaShop\Adapter\Module\ModuleZipManager;
use PrestaShop\PrestaShop\Core\Addon\AddonManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Tools;

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
     * @var \PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository
     */
    private $moduleRepository;

    /**
     * Module Zip Manager
     * @var \PrestaShop\PrestaShop\Adapter\Module\ModuleZipManager
     */
    private $moduleZipManager;

    /**
     * Translator
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    private $employee;

    public function __construct(AdminModuleDataProvider $adminModulesProvider,
        ModuleDataProvider $modulesProvider,
        ModuleDataUpdater $modulesUpdater,
        ModuleRepository $moduleRepository,
        ModuleZipManager $moduleZipManager,
        TranslatorInterface $translator,
        Employee $employee = null)
    {
        $this->adminModuleProvider = $adminModulesProvider;
        $this->moduleProvider = $modulesProvider;
        $this->moduleUpdater = $modulesUpdater;
        $this->moduleRepository = $moduleRepository;
        $this->moduleZipManager = $moduleZipManager;
        $this->translator = $translator;
        $this->employee = $employee;
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
    public function install($source)
    {
        // in CLI mode, there is no employee set up
        if (!Tools::isPHPCLI()) {
            if (!$this->employee->can('add', 'AdminModules')) {
                throw new Exception(
                    $this->translator->trans(
                        'You are not allowed to install modules.',
                        array(),
                        'Admin.Modules.Notification'));
            }
        }

        if (is_file($source)) {
            $name = $this->moduleZipManager->getName($source);
        } else {
            $name = $source;
            $source = null;
        }

        if ($this->moduleProvider->isInstalled($name)) {
            return $this->upgrade($name, 'latest', $source);
        }

        if (! $this->moduleProvider->isOnDisk($name)) {
            if (!empty($source)) {
                $this->moduleZipManager->storeInModulesFolder($source);
            } else {
                $this->moduleUpdater->setModuleOnDiskFromAddons($name);
            }
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
        if (!$this->employee->can('delete', 'AdminModules')
            || !$this->moduleProvider->can('uninstall', $name)) {
            throw new Exception(
                $this->translator->trans(
                    'You are not allowed to uninstall this module.',
                    array(),
                    'Admin.Modules.Notification'));
        }

        // Is module installed ?
        if (! $this->moduleProvider->isInstalled($name)) {
            return false;
        }

        // Get module instance and uninstall it
        $module = $this->moduleRepository->getModule($name);
        $result = $module->onUninstall();

        if ($result && (bool)$file_deletion) {
            $result &= $this->removeModuleFromDisk($name);
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
        if (!$this->employee->can('edit', 'AdminModules')
            || !$this->moduleProvider->can('configure', $name)) {
            throw new Exception(
                $this->translator->trans(
                    'You are not allowed to upgrade this module.',
                    array(),
                    'Admin.Modules.Notification'));
        }

        if (! $this->moduleProvider->isInstalled($name)) {
            throw new Exception(
                $this->translator->trans(
                    'The module %module% must be installed first',
                    array('%module%' => $name),
                'Admin.Modules.Notification'));
        }

        $result = true;

        // Get new module
        // 1- From source
        if ($source != null) {
            $this->moduleZipManager->storeInModulesFolder($source);
        }
        // 2- From Addons
        else {
            $result &= $this->moduleUpdater->setModuleOnDiskFromAddons($name);
        }

        if ($result) {
            // Load and execute upgrade files
            $result &= $this->moduleUpdater->upgrade($name);
        }

        return (bool) $result;
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
        if (!$this->employee->can('edit', 'AdminModules')
            || !$this->moduleProvider->can('configure', $name)) {
            throw new Exception(
                $this->translator->trans(
                    'You are not allowed to disable this module.',
                    array(),
                    'Admin.Modules.Notification'));
        }

        $module = $this->moduleRepository->getModule($name);
        try {
            return $module->onDisable();
        } catch (Exception $e) {
            throw new Exception(
                $this->translator->trans(
                    'Error when disabling module %module%. %error_details%.',
                    array(
                        '%module%' => $name,
                        '%error_details%' => $e->getMessage()),
                    'Admin.Modules.Notification'),
                0, $e);
        }

        return true;
    }

    /**
     * Enable a module previously disabled
     *
     * @param  string $name The module name to enable
     * @return bool         True for success
     */
    public function enable($name)
    {
        if (!$this->employee->can('edit', 'AdminModules')
            || !$this->moduleProvider->can('configure', $name)) {
            throw new Exception(
                $this->translator->trans(
                    'You are not allowed to enable this module.',
                    array(),
                    'Admin.Modules.Notification'));
        }

        $module = $this->moduleRepository->getModule($name);
        try {
            return $module->onEnable();
        } catch (Exception $e) {
            throw new Exception(
                $this->translator->trans(
                    'Error when enabling module %module%. %error_details%.',
                    array('%module%' => $name,
                        '%error_details%' => $e->getMessage()),
                    'Admin.Modules.Notification'), 0, $e);
        }

        return true;
    }

    /**
     * Disable a module specifically on mobile.
     * Not written in camel case because the route and the displayed action in the template
     * are related to this function name.
     *
     * @param  string $name The module name to disable
     * @return bool         True for success
     */
    public function disable_mobile($name)
    {
        if (!$this->employee->can('edit', 'AdminModules')
            || !$this->moduleProvider->can('configure', $name)) {
            throw new Exception(
                $this->translator->trans(
                    'You are not allowed to disable this module on mobile.',
                    array(),
                    'Admin.Modules.Notification'));
        }

        $module = $this->moduleRepository->getModule($name);
        try {
            return $module->onMobileDisable();
        } catch (Exception $e) {
            throw new Exception(
                $this->translator->trans(
                    'Error when disabling module %module% on mobile. %error_details%',
                    array(
                        '%module%' => $name,
                        '%error_details%' => $e->getMessage()),
                    'Admin.Modules.Notification'),
                0, $e);
        }
    }

    /**
     * Enable a module previously disabled on mobile
     * Not written in camel case because the route and the displayed action in the template
     * are related to this function name.
     *
     * @param  string $name The module name to enable
     * @return bool         True for success
     */
    public function enable_mobile($name)
    {
        if (!$this->employee->can('edit', 'AdminModules')
            || !$this->moduleProvider->can('configure', $name)) {
            throw new Exception(
                $this->translator->trans(
                    'You are not allowed to enable this module on mobile.',
                    array(),
                    'Admin.Modules.Notification'));
        }

        $module = $this->moduleRepository->getModule($name);
        try {
            return $module->onMobileEnable();
        } catch (Exception $e) {
            throw new Exception(
                $this->translator->trans(
                    'Error when enabling module %module% on mobile. %error_details%',
                    array(
                        '%module%' => $name,
                        '%error_details%' => $e->getMessage()),
                    'Admin.Modules.Notification'), 0, $e);
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
        if (!$this->employee->can('add', 'AdminModules')
            || !$this->employee->can('delete', 'AdminModules')
            || !$this->moduleProvider->can('uninstall', $name)) {
            throw new Exception(
                $this->translator->trans(
                    'You are not allowed to reset this module.',
                    array(),
                    'Admin.Modules.Notification'));
        }

        $module = $this->moduleRepository->getModule($name);
        try {
            if ((bool)$keep_data && method_exists($this, 'reset')) {
                $status = $module->onReset();
            } else {
                $status = ($module->onUninstall() && $module->onInstall());
            }
            return $status;
        } catch (Exception $e) {
            throw new Exception(
                $this->translator->trans(
                    'Error when resetting module %module%. %error_details%',
                    array(
                        '%module%' => $name,
                        '%error_details%' => $e->getMessage()),
                    'Admin.Modules.Notification'),
                0, $e);
        }
    }

    /**
     * Shortcut to the module data provider in order to know if a module is enabled
     *
     * @param string $name The technical module name
     * @return bool
     */
    public function isEnabled($name)
    {
        return $this->moduleProvider->isEnabled($name);
    }

    /**
     * Shortcut to the module data provider in order to know if a module is installed
     *
     * @param string $name The technical module name
     * @return bool True is installed
    */
    public function isInstalled($name)
    {
        return $this->moduleProvider->isInstalled($name);
    }

    /**
     * Shortcut to the module data updater to remove the module from the disk
     *
     * @param string $name The technical module name
     * @return bool True if files were properly removed
     */
    public function removeModuleFromDisk($name)
    {
        return $this->moduleUpdater->removeModuleFromDisk($name);
    }

    /**
     * Returns the last error, if found
     *
     * @param string $name The technical module name
     * @return string|null The last error added to the module if found
     */
    public function getError($name)
    {
        $message = null;
        $module = $this->moduleRepository->getModule($name);
        if ($module->hasValidInstance()) {
            $errors = $module->getInstance()->getErrors();
            $message = array_pop($errors);
        }

        if (empty($message)) {
            $message = $this->translator->trans('Unfortunately, the module did not return additional details.',
                array(),
                'Admin.Modules.Notification');
        }
        
        return $message;
    }
}
