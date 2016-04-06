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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter\Module;

use PrestaShop\PrestaShop\Adapter\Shop\Context;

class ModuleDataProvider
{
    public function findByName($name)
    {
        $result = \Db::getInstance()->getRow('SELECT `id_module` as `id`, `active`, `version` FROM `'._DB_PREFIX_.'module` WHERE `name` = "'.pSQL($name).'"');
        if ($result) {
            $result['installed'] = 1;
            $result['active'] = $this->isEnabled($name);
            return $result;
        }

        return ['installed' => 0];
    }

    /**
     * Check current employee permission on a given module
     * @param string $action
     * @param string $name
     * @return bool True if allowed
     */
    public function can($action, $name)
    {
        return \Module::getPermissionStatic(
            \Module::getModuleIdByName($name),
            $action
        );
    }

    public function isEnabled($name)
    {
        $id_shops = (new Context())->getContextListShopID();
        // ToDo: Load list of all installed modules ?

        $result = \Db::getInstance()->getRow('SELECT m.`id_module` as `active`, ms.`id_module` as `shop_active`
        FROM `'._DB_PREFIX_.'module` m
        LEFT JOIN `'._DB_PREFIX_.'module_shop` ms ON m.`id_module` = ms.`id_module`
        WHERE `name` = "'. pSQL($name) .'"
        AND ms.`id_shop` IN ('.implode(',', array_map('intval', $id_shops)).')');
        if ($result) {
            return (bool)($result['active'] && $result['shop_active']);
        } else {
            return false;
        }
    }


    public function isInstalled($name)
    {
        // ToDo: Load list of all installed modules ?
        return (bool)\Db::getInstance()->getValue('SELECT `id_module` FROM `'._DB_PREFIX_.'module` WHERE `name` = "'.pSQL($name).'"');
    }


    /**
     * We won't load an invalid class. This function will check any potential parse error
     *
     * @param  string $name The technical module name to check
     * @return bool true if valid
     */
    public function isModuleMainClassValid($name)
    {
        $file_path = $this->getModulesDir().$name.'/'.$name.'.php';
        if (!file_exists($file_path)) {
            return false;
        }


        $file = trim(file_get_contents($file_path));

        if (substr($file, 0, 5) == '<?php') {
            $file = substr($file, 5);
        }

        if (substr($file, -2) == '?>') {
            $file = substr($file, 0, -2);
        }

        // We check any parse error before including the file.
        // If (false) is a trick to not load the class with "eval".
        // This way require_once will works correctly
        // But namespace and use statements need to be removed
        $content = preg_replace('/\n[\s\t]*?use\s.*?;/', '', $file);
        $content = preg_replace('/\n[\s\t]*?namespace\s.*?;/', '', $content);
        return (eval('if (false){	'.$content.' }') !== false);
    }

    /**
     * Check if the module is in the modules folder, with a valid class
     *
     * @param  string $name The technical module name to find
     * @return bool         True if found
     */
    public function isOnDisk($name)
    {
        $path = $this->getModulesDir().$name.'/'.$name.'.php';
        return file_exists($path);
    }

    /**
     * Function which returns the modules directory. Used for mock in tests/ folder.
     * @return string The modules directory
     */
    protected function getModulesDir()
    {
        return _PS_MODULE_DIR_;
    }
}
