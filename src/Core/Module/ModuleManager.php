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
use Language as LegacyLanguage;
use Module as LegacyModule;
use PrestaShop\PrestaShop\Adapter\HookManager;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Core\Module\SourceHandler\SourceHandlerFactory;
use PrestaShopBundle\Entity\Repository\LangRepository;
use PrestaShopBundle\Event\ModuleManagementEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;
use Validate as LegacyValidate;

/**
 * Responsible for handling all actions with modules.
 *
 * If you want to refactor this in the future and searching for usage of some methods,
 * beware that they are called magically from ModuleController::moduleAction method.
 */
class ModuleManager implements ModuleManagerInterface
{
    /** @var Filesystem */
    private $filesystem;

    public function __construct(
        private readonly ModuleRepository $moduleRepository,
        private readonly ModuleDataProvider $moduleDataProvider,
        private readonly AdminModuleDataProvider $adminModuleDataProvider,
        private readonly SourceHandlerFactory $sourceFactory,
        private readonly TranslatorInterface $translator,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly HookManager $hookManager,
        private readonly string $modulesDir,
        private readonly XliffFileLoader $xliffFileLoader,
        private readonly ?LangRepository $languageRepository = null,
    ) {
        $this->filesystem = new Filesystem();
    }

    public function upload(string $source): string
    {
        if (!$this->adminModuleDataProvider->isAllowedAccess(__FUNCTION__)) {
            throw new Exception($this->translator->trans(
                'You are not allowed to upload modules.',
                [],
                'Admin.Modules.Notification'
            ));
        }

        $handler = $this->sourceFactory->getHandler($source);
        $handler->handle($source);
        $moduleName = $handler->getModuleName($source);
        $module = $this->moduleRepository->getModule($moduleName);
        $this->dispatch(ModuleManagementEvent::UPLOAD, $module);

        return $moduleName;
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

        $this->updateTranslatorCatalogues($name);

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
            $this->dispatch(ModuleManagementEvent::DELETE, $module);
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

    public function reset(string $name, bool $keepData = false): bool
    {
        if (
            !$this->adminModuleDataProvider->isAllowedAccess('install')
            || !$this->adminModuleDataProvider->isAllowedAccess('uninstall', $name)
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

        if ($keepData && method_exists($module->getInstance(), 'reset')) {
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
            if (empty($errors)) {
                $error = $this->translator->trans(
                    'Unfortunately, the module %module% did not return additional details.',
                    ['%module%' => $name],
                    'Admin.Modules.Notification'
                );
            } else {
                $error = implode(', ', $errors);
            }
        } else {
            $error = $this->translator->trans(
                'The module %module% is invalid and cannot be loaded.',
                ['%module%' => $name],
                'Admin.Modules.Notification'
            );

            $validityErrors = [];
            if (!LegacyValidate::isModuleName($name)) {
                $validityErrors[] = $name . ' module name is invalid';
            } else {
                try {
                    LegacyModule::getInstanceByName($name);
                } catch (Throwable $e) {
                    $validityErrors[] = $e->getMessage();
                }
            }

            if (!empty($validityErrors)) {
                $error .= ' Errors details: ' . implode(', ', $validityErrors);
            }
        }

        return $error;
    }

    /**
     * Load the module catalog in the translator (initial load only includes modules present at the beginning of the process,
     * so we manually add it in case the module has just been uploaded)
     *
     * @param string $moduleName
     *
     * @return void
     */
    protected function updateTranslatorCatalogues(string $moduleName): void
    {
        if ($this->translator instanceof TranslatorBagInterface) {
            $translationFolder = $this->modulesDir . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR . 'translations';
            if (is_dir($translationFolder)) {
                foreach ($this->getInstalledLocales() as $locale) {
                    $catalogue = $this->translator->getCatalogue($locale);
                    $languageFolder = $translationFolder . DIRECTORY_SEPARATOR . $locale;
                    if (!is_dir($languageFolder)) {
                        continue;
                    }

                    $finder = new Finder();
                    foreach ($finder->files()->in($languageFolder) as $xlfFile) {
                        $fileParts = explode('.', $xlfFile->getFilename());
                        if (count($fileParts) === 3 && $fileParts[count($fileParts) - 1] === 'xlf') {
                            $catalogueDomain = $fileParts[0];
                            $catalogue->addCatalogue($this->xliffFileLoader->load($xlfFile->getRealPath(), $locale, $catalogueDomain));
                        }
                    }
                }
            }
        }
    }

    private function getInstalledLocales(): array
    {
        if ($this->languageRepository) {
            $languages = $this->languageRepository->getMapping();
        } else {
            $languages = LegacyLanguage::getLanguages(false);
        }

        return array_map(function (array $language) {
            return $language['locale'];
        }, $languages);
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
