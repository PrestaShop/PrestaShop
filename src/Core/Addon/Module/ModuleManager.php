<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Addon\Module;

use Exception;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataUpdater;
use PrestaShop\PrestaShop\Adapter\Module\ModuleZipManager;
use PrestaShop\PrestaShop\Core\Addon\AddonManagerInterface;
use PrestaShop\PrestaShop\Core\Addon\AddonsCollection;
use PrestaShop\PrestaShop\Core\Addon\Module\Exception\UnconfirmedModuleActionException;
use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;
use PrestaShopBundle\Event\ModuleManagementEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Translation\TranslatorInterface;

class ModuleManager implements AddonManagerInterface
{
    /**
     * Admin Module Data Provider.
     *
     * @var \PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider
     */
    private $adminModuleProvider;
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
     * Module Repository.
     *
     * @var \PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository
     */
    private $moduleRepository;

    /**
     * Module Zip Manager.
     *
     * @var \PrestaShop\PrestaShop\Adapter\Module\ModuleZipManager
     */
    private $moduleZipManager;

    /**
     * Translator.
     *
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * Additionnal data used for module actions.
     *
     * @var ParameterBag
     */
    private $actionParams;

    /**
     * @var CacheClearerInterface
     */
    private $symfonyCacheClearer;

    /**
     * Used to check if the cache has already been cleaned.
     *
     * @var bool
     */
    private $cacheCleared = false;

    /**
     * @param AdminModuleDataProvider $adminModuleProvider
     * @param ModuleDataProvider $modulesProvider
     * @param ModuleDataUpdater $modulesUpdater
     * @param ModuleRepository $moduleRepository
     * @param ModuleZipManager $moduleZipManager
     * @param TranslatorInterface $translator
     * @param EventDispatcherInterface $eventDispatcher
     * @param CacheClearerInterface $symfonyCacheClearer
     */
    public function __construct(
        AdminModuleDataProvider $adminModuleProvider,
        ModuleDataProvider $modulesProvider,
        ModuleDataUpdater $modulesUpdater,
        ModuleRepository $moduleRepository,
        ModuleZipManager $moduleZipManager,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        CacheClearerInterface $symfonyCacheClearer
    ) {
        $this->adminModuleProvider = $adminModuleProvider;
        $this->moduleProvider = $modulesProvider;
        $this->moduleUpdater = $modulesUpdater;
        $this->moduleRepository = $moduleRepository;
        $this->moduleZipManager = $moduleZipManager;
        $this->translator = $translator;
        $this->eventDispatcher = $eventDispatcher;
        $this->symfonyCacheClearer = $symfonyCacheClearer;
        $this->actionParams = new ParameterBag();
    }

    /**
     * For some actions, you may need to add params like confirmation details.
     * This setter is the way to register them in the manager.
     *
     * @param array $actionParams
     *
     * @return $this
     */
    public function setActionParams(array $actionParams)
    {
        $this->actionParams->replace($actionParams);

        return $this;
    }

    /**
     * @param callable $modulesPresenter
     *
     * @return object
     */
    public function getModulesWithNotifications(callable $modulesPresenter)
    {
        $modules = $this->groupModulesByInstallationProgress();

        $modulesProvider = $this->adminModuleProvider;
        foreach ($modules as $moduleLabel => $modulesPart) {
            $collection = AddonsCollection::createFrom($modulesPart);
            $modulesProvider->generateAddonsUrls($collection, str_replace('to_', '', $moduleLabel));
            $modules->{$moduleLabel} = $modulesPresenter($collection);
        }

        return $modules;
    }

    /**
     * Returns the total of module notifications
     * Not used anymore, but kept for backward compatibility.
     *
     * @return int
     *
     * @deprecated since 1.7.5.0
     */
    public function countModulesWithNotifications()
    {
        $modules = (array) $this->groupModulesByInstallationProgress();

        return array_reduce($modules, function ($carry, $item) {
            return $carry + count($item);
        }, 0);
    }

    /**
     * Detailed array of number of modules per notification type.
     *
     * @return array
     */
    public function countModulesWithNotificationsDetailed()
    {
        $notificationCounts = array(
            'count' => 0,
        );

        foreach ((array) $this->groupModulesByInstallationProgress() as $key => $modules) {
            $count = count($modules);
            $notificationCounts[$key] = $count;
            $notificationCounts['count'] += $count;
        }

        return $notificationCounts;
    }

    /**
     * @return object
     */
    protected function groupModulesByInstallationProgress()
    {
        $installedProducts = $this->moduleRepository->getInstalledModules();

        $modules = (object) array(
            'to_configure' => array(),
            'to_update' => array(),
        );

        /*
         * @var \PrestaShop\PrestaShop\Adapter\Module\Module
         */
        foreach ($installedProducts as $installedProduct) {
            if ($this->shouldRecommendConfigurationForModule($installedProduct)) {
                $modules->to_configure[] = (object) $installedProduct;
            }

            if ($installedProduct->canBeUpgraded()) {
                $modules->to_update[] = (object) $installedProduct;
            }
        }

        return $modules;
    }

    /**
     * @param Module $installedProduct
     *
     * @return bool
     */
    protected function shouldRecommendConfigurationForModule(Module $installedProduct)
    {
        $warnings = $this->getModuleInstallationWarnings($installedProduct);

        return !empty($warnings);
    }

    /**
     * @param Module $installedProduct
     *
     * @return array
     */
    protected function getModuleInstallationWarnings(Module $installedProduct)
    {
        if ($installedProduct->hasValidInstance()) {
            return $installedProduct->getInstance()->warning;
        }

        return array();
    }

    /**
     * Add new module from zipball. This will unzip the file and move the content
     * to the right locations.
     * A theme can bundle modules, resources, documentation, email templates and so on.
     *
     * @param string $source The source can be a module name (installed from either local disk or addons.prestashop.com).
     *                       or a location (url or path to the zip file)
     *
     * @return bool true for success
     */
    public function install($source)
    {
        // in CLI mode, there is no employee set up
        if (!$this->adminModuleProvider->isAllowedAccess(__FUNCTION__)) {
            throw new Exception(
                $this->translator->trans(
                    'You are not allowed to install modules.',
                    array(),
                    'Admin.Modules.Notification'
                )
            );
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

        if (!empty($source)) {
            $this->moduleZipManager->storeInModulesFolder($source);
        } elseif (!$this->moduleProvider->isOnDisk($name)) {
            $this->moduleUpdater->setModuleOnDiskFromAddons($name);
        }

        $module = $this->moduleRepository->getModule($name);
        $this->checkConfirmationGiven(__FUNCTION__, $module);
        $result = $module->onInstall();

        $this->checkAndClearCache($result);
        $this->dispatch(ModuleManagementEvent::INSTALL, $module);

        return $result;
    }

    /**
     * Remove all theme files, resources, documentation and specific modules.
     *
     * @param string $name The source can be a module name (installed from either local disk or addons.prestashop.com).
     *                     or a location (url or path to the zip file)
     *
     * @return bool true for success
     */
    public function uninstall($name)
    {
        // Check permissions:
        // * Employee can delete
        // * Employee can delete this specific module
        if (!$this->adminModuleProvider->isAllowedAccess(__FUNCTION__, $name)) {
            throw new Exception(
                $this->translator->trans(
                    'You are not allowed to uninstall the module %module%.',
                    array(
                        '%module%' => $name,
                    ),
                    'Admin.Modules.Notification'
                )
            );
        }

        $this->checkIsInstalled($name);

        // Get module instance and uninstall it
        $module = $this->moduleRepository->getModule($name);
        $result = $module->onUninstall();

        if ($result && $this->actionParams->get('deletion', false)) {
            $result = $result && $this->removeModuleFromDisk($name);
        }

        $this->checkAndClearCache($result);
        $this->dispatch(ModuleManagementEvent::UNINSTALL, $module);

        return $result;
    }

    /**
     * Download new files from source, backup old files, replace files with new ones
     * and execute all necessary migration scripts form current version to the new one.
     *
     * @param Addon $name the theme you want to upgrade
     * @param string $version the version you want to up upgrade to
     * @param string $source if the upgrade is not coming from addons, you need to specify the path to the zipball
     *
     * @return bool true for success
     */
    public function upgrade($name, $version = 'latest', $source = null)
    {
        if (!$this->adminModuleProvider->isAllowedAccess(__FUNCTION__, $name)) {
            throw new Exception(
                $this->translator->trans(
                    'You are not allowed to upgrade the module %module%.',
                    array(
                        '%module%' => $name,
                    ),
                    'Admin.Modules.Notification'
                )
            );
        }

        $this->checkIsInstalled($name);
        $module = $this->moduleRepository->getModule($name);

        // Get new module
        // 1- From source
        if ($source != null) {
            $this->moduleZipManager->storeInModulesFolder($source);
        } elseif ($module->canBeUpgradedFromAddons()) {
            // 2- From Addons
            // This step is not mandatory (in case of local module),
            // we do not check the result
            $this->moduleUpdater->setModuleOnDiskFromAddons($name);
        }

        // Load and execute upgrade files
        $result = $this->moduleUpdater->upgrade($name) && $module->onUpgrade($version);

        $this->checkAndClearCache($result);
        $this->dispatch(ModuleManagementEvent::UPGRADE, $module);

        return $result;
    }

    /**
     * Disable a module without uninstalling it.
     * Allows the merchant to temporarly remove a module without uninstalling it.
     *
     * @param string $name The module name to disable
     *
     * @return bool True for success
     */
    public function disable($name)
    {
        if (!$this->adminModuleProvider->isAllowedAccess(__FUNCTION__, $name)) {
            throw new Exception(
                $this->translator->trans(
                    'You are not allowed to disable the module %module%.',
                    array(
                        '%module%' => $name,
                    ),
                    'Admin.Modules.Notification'
                )
            );
        }

        $this->checkIsInstalled($name);

        $module = $this->moduleRepository->getModule($name);

        try {
            $result = $module->onDisable();
        } catch (Exception $e) {
            throw new Exception(
                $this->translator->trans(
                    'Error when disabling module %module%. %error_details%.',
                    array(
                        '%module%' => $name,
                        '%error_details%' => $e->getMessage(),
                    ),
                    'Admin.Modules.Notification'
                ),
                0,
                $e
            );
        }

        $this->checkAndClearCache($result);
        $this->dispatch(ModuleManagementEvent::DISABLE, $module);

        return $result;
    }

    /**
     * Enable a module previously disabled.
     *
     * @param string $name The module name to enable
     *
     * @return bool True for success
     */
    public function enable($name)
    {
        if (!$this->adminModuleProvider->isAllowedAccess(__FUNCTION__, $name)) {
            throw new Exception(
                $this->translator->trans(
                    'You are not allowed to enable the module %module%.',
                    array(
                        '%module%' => $name,
                    ),
                    'Admin.Modules.Notification'
                )
            );
        }

        $this->checkIsInstalled($name);

        $module = $this->moduleRepository->getModule($name);

        try {
            $result = $module->onEnable();
        } catch (Exception $e) {
            throw new Exception(
                $this->translator->trans(
                    'Error when enabling module %module%. %error_details%.',
                    array(
                        '%module%' => $name,
                        '%error_details%' => $e->getMessage(),
                    ),
                    'Admin.Modules.Notification'
                ),
                0,
                $e
            );
        }

        $this->checkAndClearCache($result);
        $this->dispatch(ModuleManagementEvent::ENABLE, $module);

        return $result;
    }

    /**
     * Disable a module specifically on mobile.
     * Not written in camel case because the route and the displayed action in the template
     * are related to this function name.
     *
     * @deprecated use disableMobile()
     *
     * @param string $name The module name to disable
     *
     * @return bool True for success
     */
    public function disable_mobile($name)
    {
        return $this->disableMobile($name);
    }

    /**
     * Disable a module specifically on mobile.
     *
     * @param string $name The module name to disable
     *
     * @return bool True for success
     */
    public function disableMobile($name)
    {
        if (!$this->adminModuleProvider->isAllowedAccess(__FUNCTION__, $name)) {
            throw new Exception(
                $this->translator->trans(
                    'You are not allowed to disable the module %module% on mobile.',
                    array(
                        '%module%' => $name,
                    ),
                    'Admin.Modules.Notification'
                )
            );
        }

        $this->checkIsInstalled($name);

        $module = $this->moduleRepository->getModule($name);

        try {
            $result = $module->onMobileDisable();
        } catch (Exception $e) {
            throw new Exception(
                $this->translator->trans(
                    'Error when disabling module %module% on mobile. %error_details%',
                    array(
                        '%module%' => $name,
                        '%error_details%' => $e->getMessage(),
                    ),
                    'Admin.Modules.Notification'
                ),
                0,
                $e
            );
        }

        $this->checkAndClearCache($result);

        return $result;
    }

    /**
     * Enable a module previously disabled on mobile
     * Not written in camel case because the route and the displayed action in the template
     * are related to this function name.
     *
     * @deprecated use enableMobile
     *
     * @param string $name The module name to enable
     *
     * @return bool True for success
     */
    public function enable_mobile($name)
    {
        return $this->enableMobile($name);
    }

    /**
     * Enable a module previously disabled on mobile.
     *
     * @param string $name The module name to enable
     *
     * @return bool True for success
     */
    public function enableMobile($name)
    {
        if (!$this->adminModuleProvider->isAllowedAccess(__FUNCTION__, $name)) {
            throw new Exception(
                $this->translator->trans(
                    'You are not allowed to enable the module %module% on mobile.',
                    array(
                        '%module%' => $name,
                    ),
                    'Admin.Modules.Notification'
                )
            );
        }

        $this->checkIsInstalled($name);

        $module = $this->moduleRepository->getModule($name);

        try {
            $result = $module->onMobileEnable();
        } catch (Exception $e) {
            throw new Exception(
                $this->translator->trans(
                    'Error when enabling module %module% on mobile. %error_details%',
                    array(
                        '%module%' => $name,
                        '%error_details%' => $e->getMessage(),
                    ),
                    'Admin.Modules.Notification'
                ),
                0,
                $e
            );
        }

        $this->checkAndClearCache($result);

        return $result;
    }

    /**
     * Actions to perform to restaure default settings.
     *
     * @param string $name The theme name to reset
     *
     * @return bool True for success
     */
    public function reset($name, $keep_data = false)
    {
        if (!$this->adminModuleProvider->isAllowedAccess('install') || !$this->adminModuleProvider->isAllowedAccess('uninstall', $name)) {
            throw new Exception(
                $this->translator->trans(
                    'You are not allowed to reset the module %module%.',
                    array(
                        '%module%' => $name,
                    ),
                    'Admin.Modules.Notification'
                )
            );
        }

        $this->checkIsInstalled($name);

        $module = $this->moduleRepository->getModule($name);

        try {
            if ((bool) $keep_data && method_exists($module->getInstance(), 'reset')) {
                $this->dispatch(ModuleManagementEvent::UNINSTALL, $module);
                $status = $module->onReset();
                $this->dispatch(ModuleManagementEvent::INSTALL, $module);
            } else {
                $status = ($this->uninstall($name) && $this->install($name));
            }
        } catch (Exception $e) {
            throw new Exception(
                $this->translator->trans(
                    'Error when resetting module %module%. %error_details%',
                    array(
                        '%module%' => $name,
                        '%error_details%' => $e->getMessage(),
                    ),
                    'Admin.Modules.Notification'
                ),
                0,
                $e
            );
        }

        return $status;
    }

    /**
     * Shortcut to the module data provider in order to know if a module is enabled.
     *
     * @param string $name The technical module name
     *
     * @return bool
     */
    public function isEnabled($name)
    {
        return $this->moduleProvider->isEnabled($name);
    }

    /**
     * Shortcut to the module data provider in order to know if a module is installed.
     *
     * @param string $name The technical module name
     *
     * @return bool True is installed
     */
    public function isInstalled($name)
    {
        return $this->moduleProvider->isInstalled($name);
    }

    /**
     * Shortcut to the module data provider in order to know the module id depends
     * on its name.
     *
     * @param string $name The technical module name
     *
     * @return int the Module Id, or 0 if not found
     */
    public function getModuleIdByName($name)
    {
        return $this->moduleProvider->getModuleIdByName($name);
    }

    /**
     * Shortcut to the module data updater to remove the module from the disk.
     *
     * @param string $name The technical module name
     *
     * @return bool True if files were properly removed
     */
    public function removeModuleFromDisk($name)
    {
        return $this->moduleUpdater->removeModuleFromDisk($name);
    }

    /**
     * Returns the last error, if found.
     *
     * @param string $name The technical module name
     *
     * @return string|null The last error added to the module if found
     */
    public function getError($name)
    {
        $message = null;
        $module = $this->moduleRepository->getModule($name);
        if ($module->hasValidInstance()) {
            $errors = $module->getInstance()->getErrors();
            $message = array_pop($errors);
        } else {
            // Invalid instance: Missing or with syntax error
            $message = $this->translator->trans(
                'The module is invalid and cannot be loaded.',
                array(),
                'Admin.Modules.Notification'
            );
        }

        if (empty($message)) {
            $message = $this->translator->trans(
                'Unfortunately, the module did not return additional details.',
                array(),
                'Admin.Modules.Notification'
            );
        }

        return $message;
    }

    /**
     * This function is a refacto of the event dispatching.
     *
     * @param string $event
     * @param \PrestaShop\PrestaShop\Core\Addon\Module\Module $module
     */
    private function dispatch($event, $module)
    {
        $this->eventDispatcher->dispatch($event, new ModuleManagementEvent($module));
    }

    private function checkIsInstalled($name)
    {
        if (!$this->moduleProvider->isInstalled($name)) {
            throw new Exception(
                $this->translator->trans(
                    'The module %module% must be installed first',
                    array('%module%' => $name),
                    'Admin.Modules.Notification'
                )
            );
        }
    }

    /**
     * We check the module does not ask for pre-requisites to be respected prior the action being executed.
     *
     * @param string $action
     * @param Module $module
     *
     * @throws UnconfirmedModuleActionException
     */
    private function checkConfirmationGiven($action, Module $module)
    {
        if ($action === 'install') {
            if ($module->attributes->has('prestatrust') && !$this->actionParams->has('confirmPrestaTrust')) {
                throw (new UnconfirmedModuleActionException())->setModule($module)
                    ->setAction($action)
                    ->setSubject('PrestaTrust');
            }
        }
    }

    /**
     * @param bool $result
     */
    private function checkAndClearCache($result)
    {
        if ($result && $this->actionParams->get('cacheClearEnabled', true)) {
            $this->clearCache();
        }
    }

    /**
     * Clear smarty and Symfony cache (the sf2 cache is remove on the process shutdown).
     */
    private function clearCache()
    {
        if ($this->cacheCleared) {
            return;
        }

        $this->symfonyCacheClearer->clear();
        $this->cacheCleared = true;
    }
}
