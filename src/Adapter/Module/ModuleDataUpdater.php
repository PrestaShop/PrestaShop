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
namespace PrestaShop\PrestaShop\Adapter\Module;

use PrestaShopBundle\Service\DataProvider\Admin\AddonsInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Addons\AddonsDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\Translation\TranslatorInterface;

use Tab;

class ModuleDataUpdater
{
    private $addonsDataProvider;
    private $adminModuleDataProvider;
    /**
     *
     * @var PrestaShop\PrestaShop\Adapter\LegacyContext
     */
    private $context;
    /**
     * @var Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    public function __construct(AddonsInterface $addonsDataProvider, AdminModuleDataProvider $adminModuleDataProvider, LegacyContext $context, LoggerInterface $logger, TranslatorInterface $translator)
    {
        $this->addonsDataProvider = $addonsDataProvider;
        $this->adminModuleDataProvider = $adminModuleDataProvider;
        $this->context = $context;
        $this->logger = $logger;
        $this->translator = $translator;
    }

    public function setModuleOnDiskFromAddons($name)
    {
        // Note : Data caching should be handled by the addons data provider
        // Check if the module can be downloaded from addons
        foreach ($this->adminModuleDataProvider->getCatalogModules(['name' => $name]) as $catalog_module) {
            if ($catalog_module->name == $name && in_array($catalog_module->origin, ['native', 'native_all', 'partner', 'customer'])) {
                return $this->addonsDataProvider->downloadModule($catalog_module->id);
            }
        }

        return false;
    }

    public function removeModuleFromDisk($name)
    {
        $fs = new FileSystem();
        try {
            $fs->remove(_PS_MODULE_DIR_ .'/'. $name);
            return true;
        } catch (IOException $e) {
            return false;
        }
    }

    /*
     * MODULE TABS
     */
    
    /**
     * Install all module-defined tabs.
     *
     * This is done automatically as part of the module installation.
     * @param Module $module
     * @return bool Returns true if the tabs were successfully installed, false otherwise.
     */
    public function installTabs(Module $module)
    {
        $tabs = $module->getInstance()->getTabs();

        foreach ($tabs as $tab_info) {
            if (!$this->installTab($tab_info)) {
                // Something failed, remove already added tabs.
                $this->uninstallTabs($module);
                return false;
            }
        }
        return true;
    }


    /**
     * Uninstall all module-defined tabs.
     *
     * This is done automatically as part of the module uninstallation.
     *
     * @return bool Returns true if the module tabs were successfully uninstalled, false if any of them failed to do so.
     */
    public function uninstallTabs(Module $module)
    {
        $tabs = $module->getInstance()->getTabs();

        $success = true;
        foreach ($tabs as $tab_info) {
            $success &= $this->uninstallTab($tab_info);
        }
        return $success;
    }

    public function upgrade($name)
    {
        // Calling this function will init legacy module data
        $module_list = \ModuleCore::getModulesOnDisk();

        foreach ($module_list as $module) {
            if ($module->name != $name) {
                continue;
            }

            if (\ModuleCore::initUpgradeModule($module)) {
                $legacy_instance = \ModuleCore::getInstanceByName($name);
                $legacy_instance->runUpgradeModule();

                \ModuleCore::upgradeModuleVersion($name, $module->version);

                return (!count($legacy_instance->getErrors()));
            } elseif (\ModuleCore::getUpgradeStatus($name)) {
                return true;
            }
            return true;
        }

        return false;
    }

    /**
     * Install a tab according to its defined structure
     *
     * @param array $tab_info The structure of the tab.
     *
     * @return bool   true if the tab was installed successfully, false otherwise
     *                (and a descriptive error will be added to $_errors)
     */
    private function installTab($tab_info)
    {
        $class_name = isset($tab_info['class_name']) ? $tab_info['class_name'] : null;
        if (!$class_name) {
            $this->logger->warning(
                $this->translator->trans(
                    'Cannot register tab. Missing class name for "%name%".',
                    array(
                        '%name%' => $tab_info['name'],
                    ),
                    'Admin.Modules.Notification'));
            return false;
        }

        $tab = new Tab();
        $tab->module = $this->name;
        $tab->class_name = $class_name;

        // Default to active if it's not specified
        $tab->active = (!isset($tab_info['active']) || $tab_info['active'] ? 1 : 0);

        $tab->name = array();
        foreach ($this->context->getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = $tab_info['name'];
        }

        // Setup the parent relationship
        if (isset($tab_info['parent_class'])) {
            $tab->id_parent = (int)Tab::getIdFromClassName($tab_info['parent_class']);
            if (!$tab->id_parent) {
                $this->logger->warning(
                    $this->translator->trans(
                        'Failed to find tab parent class "%parent_class%".',
                        array(
                            '%parent_class%' => $tab_info['parent_class'],
                        ),
                        'Admin.Modules.Notification'));
                return false;
            }
        } elseif (isset($tab_info['hidden']) && $tab_info['hidden']) {
            $tab->id_parent = -1;
        } else {
            $tab->id_parent = 0;
        }

        $success = $tab->add();
        if (!$success) {
            $this->logger->warning(
                $this->translator->trans(
                    'Failed to install admin tab "%name%".',
                    array(
                        '%name%' => $tab_info['name'],
                    ),
                    'Admin.Modules.Notification'));
        }
        return $success;
    }

    /**
     * Uninstalls a tab given its defined structure.
     *
     * @param array $tab_info The structure of the tab.
     *
     * @return bool Returns true if the specified tab was successfully deleted, false otherwise
     *              (and a descriptive error will be added to $_errors).
     */
    private function uninstallTab($tab_info)
    {
        $class_name = isset($tab_info['class_name']) ? $tab_info['class_name'] : null;
        if (!$class_name) {
            $this->logger->warning(
                $this->translator->trans(
                    'Cannot unregister tab. Missing class name for "%name%".',
                    array(
                        '%name%' => $tab_info['name'],
                    ),
                    'Admin.Modules.Notification'));
            return false;
        }

        while ($id_tab = (int)Tab::getIdFromClassName($class_name)) {
            $tab = new Tab($id_tab);
            if (!$tab->delete()) {
                $this->logger->warning(
                    $this->translator->trans(
                        'Failed to uninstall admin tab "%name%".',
                        array(
                            '%name%' => $tab_info['name'],
                        ),
                        'Admin.Modules.Notification'));
                return false;
            }
        }

        return true;
    }
}
