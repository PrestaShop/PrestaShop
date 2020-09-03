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

namespace LegacyTests\TestCase;

define('_RESSOURCE_MODULE_DIR_', realpath(dirname(__FILE__).'/../resources/module/'));

class Module
{
    /**
     * Copy the directory in resources which get the name $module_dir_name in the module directory
     *
     * @var module_dir_name take the directory name of a module contain in /home/prestashop/tests/resources/module
     */
    public static function addModule($module_dir_name)
    {
        if (is_dir(_RESSOURCE_MODULE_DIR_.'/'.$module_dir_name)) {
            File::recurseCopy(_RESSOURCE_MODULE_DIR_.'/'.$module_dir_name, _PS_MODULE_DIR_.'/'.$module_dir_name);

            return true;
        }

        return false;
    }

    /**
     * Delete the directory in /home/prestashop/module which get the name $module_dir_name
     *
     * @var module_dir_name take the directory name of a module contain in /home/prestashop/module
     */
    public static function removeModule($module_dir_name)
    {
        if (is_dir(_PS_MODULE_DIR_.'/'.$module_dir_name)) {
            File::recurseDelete(_PS_MODULE_DIR_.'/'.$module_dir_name);

            return true;
        }

        return false;
    }
}
