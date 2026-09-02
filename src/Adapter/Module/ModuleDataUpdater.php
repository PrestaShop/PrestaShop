<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Module;

use Module as LegacyModule;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Responsible of managing updates of modules. This class is currently used only by autoupgrade module.
 * Core has it's own solution in PrestaShop\PrestaShop\Core\Module\ModuleManager.
 * In the future, autoupgrade module upgrade process should be unified with the core, so we can remove
 * this duplicate code.
 */
class ModuleDataUpdater
{
    /**
     * @param string $name
     *
     * @return bool
     */
    public function removeModuleFromDisk($name)
    {
        $fs = new Filesystem();

        try {
            $fs->remove(_PS_MODULE_DIR_ . '/' . $name);

            return true;
        } catch (IOException) {
            return false;
        }
    }

    /**
     * @param string $name
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
