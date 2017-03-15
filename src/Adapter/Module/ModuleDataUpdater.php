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
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;

class ModuleDataUpdater
{
    private $addonsDataProvider;
    private $adminModuleDataProvider;

    public function __construct(AddonsInterface $addonsDataProvider, AdminModuleDataProvider $adminModuleDataProvider)
    {
        $this->addonsDataProvider = $addonsDataProvider;
        $this->adminModuleDataProvider = $adminModuleDataProvider;
    }

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
}
