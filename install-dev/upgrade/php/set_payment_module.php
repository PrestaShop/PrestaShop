<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

function set_payment_module()
{
    // Get all modules then select only payment ones
    $modules = Module::getModulesInstalled();
    foreach ($modules as $module) {
        $file = _PS_MODULE_DIR_ . $module['name'] . '/' . $module['name'] . '.php';
        if (!file_exists($file)) {
            continue;
        }
        $fd = fopen($file, 'r');
        if (!$fd) {
            continue ;
        }
        $content = fread($fd, filesize($file));
        if (preg_match_all('/extends PaymentModule/U', $content, $matches)) {
            Db::getInstance()->execute('
			INSERT INTO `' . _DB_PREFIX_ . 'module_country` (id_module, id_country)
			SELECT ' . (int)($module['id_module']) . ', id_country FROM `' . _DB_PREFIX_ . 'country` WHERE active = 1');
            Db::getInstance()->execute('
			INSERT INTO `' . _DB_PREFIX_ . 'module_currency` (id_module, id_currency)
			SELECT ' . (int)($module['id_module']) . ', id_currency FROM `' . _DB_PREFIX_ . 'currency` WHERE deleted = 0');
        }
        fclose($fd);
    }
}
