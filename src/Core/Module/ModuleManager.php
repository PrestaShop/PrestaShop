<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Module;

use Exception;
use Module as LegacyModule;
use PrestaShop\PrestaShop\Adapter\HookManager;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Core\Module\SourceHandler\SourceHandlerFactory;
use PrestaShopBundle\Event\ModuleManagementEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Translation\TranslatorInterface;

class ModuleManager implements ModuleManagerInterface
{
    /** @var ModuleRepository */
    private $moduleRepository;

    /** @var ModuleDataProvider */
    private $moduleDataProvider;

    /** @var AdminModuleDataProvider */
    private $adminModuleDataProvider;

    /** @var SourceHandlerFactory */
    private $sourceFactory;

    /** @var TranslatorInterface */
    private $translator;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var HookManager */
    private $hookManager;

    /** @var Filesystem */
    private $filesystem;

    public function __construct(
        ModuleRepository $moduleRepository,
        ModuleDataProvider $moduleDataProvider,
        AdminModuleDataProvider $adminModuleDataProvider,
        SourceHandlerFactory $sourceFactory,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        HookManager $hookManager
    ) {
        $this->filesystem = new Filesystem();
        $this->moduleRepository = $moduleRepository;
        $this->moduleDataProvider = $moduleDataProvider;
        $this->adminModuleDataProvider = $adminModuleDataProvider;
        $this->sourceFactory = $sourceFactory;
        $this->translator = $translator;
        $this->eventDispatcher = $eventDispatcher;
        $this->hookManager = $hookManager;
    }

    public function install(string $name, $source = null): bool
    {
        if (!$this->adminModuleDataProvider->isAllowedAccess(__FUNCTION__)) {
            throw new Exception($this->translator->trans(
                'You are not allowed to install modules.',
                [],
                'Admin.Modules.Notification'
            ));
        }

        if ($this->isInstalled($name)) {
            return $this->upgrade($name, $source);
        }

        if ($source !== null) {
            $handler = $this->sourceFactory->getHandler($source);
            $handler->handle($source);
        }

        $this->hookManager->exec('actionBeforeInstallModule', ['moduleName' => $name, 'source' => $source]);

        $module = $this->moduleRepository->getModule($name);
        $installed = $module->onInstall();
        if ($installed) {
            // Only trigger install event if install has succeeded otherwise it could automatically add tabs linked to a
            // module not installed (@see ModuleTabManagementSubscriber) or other unwanted automatic actions.
            $this->dispatch(ModuleManagementEvent::INSTALL, $module);
        }

        return $installed;
    }

    public function postInstall(string $name): bool
    {
        if (!$this->isInstalled($name)) {
            return false;
        }

        if (!$this->moduleDataProvider->isOnDisk($name)) {
            return false;
        }

        $this->hookManager->exec('actionBeforePostInstallModule', ['moduleName' => $name]);

        $module = $this->moduleRepository->getModule($name);
        $result = $module->onPostInstall();

        $this->dispatch(ModuleManagementEvent::POST_INSTALL, $module);

        return $result;
    }

    public function uninstall(string $name, bool $deleteFiles = false): bool
    {
        if (!$this->adminModuleDataProvider->isAllowedAccess(__FUNCTION__, $name)) {
            throw new Exception($this->translator->trans(
                'You are not allowed to uninstall the module %module%.',
                ['%module%' => $name],
                'Admin.Modules.Notification'
            ));
        }

        $this->assertIsInstalled($name);

        $this->hookManager->exec('actionBeforeUninstallModule', ['moduleName' => $name]);

        $module = $this->moduleRepository->getModule($name);
        $uninstalled = $module->onUninstall();

        if ($deleteFiles && $path = $this->moduleRepository->getModulePath($name)) {
            $this->filesystem->remove($path);
        }

        $this->dispatch(ModuleManagementEvent::UNINSTALL, $module);

        return $uninstalled;
    }

    public function delete(string $name): bool
    {
        if (!$this->adminModuleDataProvider->isAllowedAccess(__FUNCTION__, $name)) {
            throw new Exception($this->translator->trans(
                'You are not allowed to delete the module %module%.',
                ['%module%' => $name],
                'Admin.Modules.Notification'
            ));
        }

        $module = $this->moduleRepository->getModule($name);

        $path = $this->moduleRepository->getModulePath($name);
        $this->filesystem->remove($path);

        $this->dispatch(ModuleManagementEvent::DELETE, $module);

        return true;
    }

    public function upgrade(string $name, $source = null): bool
    {
        if (!$this->adminModuleDataProvider->isAllowedAccess(__FUNCTION__, $name)) {
            throw new Exception($this->translator->trans(
                'You are not allowed to update the module %module%.',
                ['%module%' => $name],
                'Admin.Modules.Notification'
            ));
        }

        $this->assertIsInstalled($name);

        if ($source !== null) {
            $handler = $this->sourceFactory->getHandler($source);
            $handler->handle($source);
        }

        $this->hookManager->disableHooksForModule($this->moduleDataProvider->getModuleIdByName($name));

        $this->hookManager->exec('actionBeforeUpgradeModule', ['moduleName' => $name, 'source' => $source]);

        $module = $this->moduleRepository->getModule($name);
        $upgraded = $this->upgradeMigration($name) && $module->onUpgrade($module->get('version'));

        $this->dispatch(ModuleManagementEvent::UPGRADE, $module);

        return $upgraded;
    }

    public function enable(string $name): bool
    {
        if (!$this->adminModuleDataProvider->isAllowedAccess(__FUNCTION__, $name)) {
            throw new Exception($this->translator->trans(
                'You are not allowed to enable the module %module%.',
                ['%module%' => $name],
                'Admin.Modules.Notification'
            ));
        }

        $this->assertIsInstalled($name);

        $this->hookManager->exec('actionBeforeEnableModule', ['moduleName' => $name]);

        $module = $this->moduleRepository->getModule($name);
        $enabled = $module->onEnable();
        $this->dispatch(ModuleManagementEvent::ENABLE, $module);

        return $enabled;
    }

    public function disable(string $name): bool
    {
        if (!$this->adminModuleDataProvider->isAllowedAccess(__FUNCTION__, $name)) {
            throw new Exception($this->translator->trans(
                'You are not allowed to disable the module %module%.',
                ['%module%' => $name],
                'Admin.Modules.Notification'
            ));
        }

        $this->assertIsInstalled($name);

        $this->hookManager->exec('actionBeforeDisableModule', ['moduleName' => $name]);

        $module = $this->moduleRepository->getModule($name);
        $disabled = $module->onDisable();
        $this->dispatch(ModuleManagementEvent::DISABLE, $module);

        return $disabled;
    }

    /**
     * @deprecated since 9.0.0 - This functionality was disabled. Function will be completely removed
     * in the next major. There is no replacement, all clients should have the same experience.
     */
    public function enableMobile(string $name): bool
    {
        @trigger_error(
            sprintf(
                '%s is deprecated since version 9.0.0. There is no replacement.',
                __METHOD__
            ),
            E_USER_DEPRECATED
        );

        return true;
    }

    /**
     * @deprecated since 9.0.0 - This functionality was disabled. Function will be completely removed
     * in the next major. There is no replacement, all clients should have the same experience.
     */
    public function disableMobile(string $name): bool
    {
        @trigger_error(
            sprintf(
                '%s is deprecated since version 9.0.0. There is no replacement.',
                __METHOD__
            ),
            E_USER_DEPRECATED
        );

        return true;
    }

    public function reset(string $name, bool $keepData = false): bool
    {
        if (
            !$this->adminModuleDataProvider->isAllowedAccess('install') ||
            !$this->adminModuleDataProvider->isAllowedAccess('uninstall', $name)
        ) {
            throw new Exception($this->translator->trans(
                'You are not allowed to reset the module %module%.',
                ['%module%' => $name],
                'Admin.Modules.Notification'
            ));
        }

        $this->assertIsInstalled($name);

        $this->hookManager->exec('actionBeforeResetModule', ['moduleName' => $name]);

        $module = $this->moduleRepository->getModule($name);

        if ($keepData && method_exists($module, 'reset')) {
            $reset = $module->onReset();
            $this->dispatch(ModuleManagementEvent::RESET, $module);
        } else {
            $reset = $this->uninstall($name) && $this->install($name);
        }

        return $reset;
    }

    public function isInstalled(string $name): bool
    {
        return $this->moduleDataProvider->isInstalled($name);
    }

    public function isInstalledAndActive(string $name): bool
    {
        return $this->moduleDataProvider->isInstalledAndActive($name);
    }

    public function isEnabled(string $name): bool
    {
        return $this->moduleDataProvider->isEnabled($name);
    }

    public function getError(string $name): string
    {
        $module = $this->moduleRepository->getModule($name);
        if ($module->hasValidInstance()) {
            $errors = array_filter($module->getInstance()->getErrors());
            $error = array_pop($errors);
            if (empty($error)) {
                $error = $this->translator->trans(
                    'Unfortunately, the module did not return additional details.',
                    [],
                    'Admin.Modules.Notification'
                );
            }
        } else {
            $error = $this->translator->trans(
                'The module is invalid and cannot be loaded.',
                [],
                'Admin.Modules.Notification'
            );
        }

        return $error;
    }

    protected function upgradeMigration(string $name): bool
    {
        $module_list = LegacyModule::getModulesOnDisk();

        foreach ($module_list as $module) {
            if ($module->name != $name) {
                continue;
            }

            if (LegacyModule::initUpgradeModule($module)) {
                $legacy_instance = LegacyModule::getInstanceByName($name);
                $legacy_instance->runUpgradeModule();

                LegacyModule::upgradeModuleVersion($name, $module->version);

                return !count($legacy_instance->getErrors());
            }

            return true;
        }

        return false;
    }

    private function assertIsInstalled(string $name): void
    {
        if (!$this->isInstalled($name)) {
            throw new Exception($this->translator->trans(
                'The module %module% must be installed first',
                ['%module%' => $name],
                'Admin.Modules.Notification'
            ));
        }
    }

    private function dispatch(string $event, ModuleInterface $module): void
    {
        $this->eventDispatcher->dispatch(new ModuleManagementEvent($module), $event);
    }
}
