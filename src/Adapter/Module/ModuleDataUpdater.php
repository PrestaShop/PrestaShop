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

namespace PrestaShop\PrestaShop\Adapter\Module;

use Module as LegacyModule;
use PrestaShopBundle\Service\DataProvider\Admin\AddonsInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Responsible of managing updates of modules.
 */
class ModuleDataUpdater
{
    /**
     * @var AddonsInterface
     */
    private $addonsDataProvider;

    /**
     * @var AdminModuleDataProvider
     */
    private $adminModuleDataProvider;

    public function __construct(AddonsInterface $addonsDataProvider, AdminModuleDataProvider $adminModuleDataProvider)
    {
        $this->addonsDataProvider = $addonsDataProvider;
        $this->adminModuleDataProvider = $adminModuleDataProvider;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function setModuleOnDiskFromAddons($name)
    {
        // Note : Data caching should be handled by the addons data provider
        // Check if the module can be downloaded from addons
        foreach ($this->adminModuleDataProvider->getCatalogModules(['name' => $name]) as $catalog_module) {
            if ($catalog_module->name == $name && in_array($catalog_module->origin, ['native', 'native_all', 'must-have', 'customer'])) {
                return $this->addonsDataProvider->downloadModule($catalog_module->id);
            }
        }

        return false;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function removeModuleFromDisk($name)
    {
        $fs = new FileSystem();

        try {
            $fs->remove(_PS_MODULE_DIR_ . '/' . $name);

            return true;
        } catch (IOException $e) {
            return false;
        }
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function upgrade($name)
    {
        // Calling this function will init legacy module data
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
            } elseif (LegacyModule::getUpgradeStatus($name)) {
                return true;
            }

            return true;
        }

        return false;
    }
}
