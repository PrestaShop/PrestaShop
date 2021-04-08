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
function p15015_blockadvertising_extension()
{
    if (!defined('_PS_ROOT_DIR_')) {
        define('_PS_ROOT_DIR_', realpath(INSTALL_PATH . '/../'));
    }

    // Try to update with the extension of the image that exists in the module directory
    if (@file_exists(_PS_ROOT_DIR_ . '/modules/blockadvertising')) {
        foreach (@scandir(_PS_ROOT_DIR_ . '/modules/blockadvertising', SCANDIR_SORT_NONE) as $file) {
            if (in_array($file, ['advertising.jpg', 'advertising.gif', 'advertising.png'])) {
                $exist = Db::getInstance()->getValue('SELECT `id_configuration` FROM `' . _DB_PREFIX_ . 'configuration` WHERE `name` = \'BLOCKADVERT_IMG_EXT\'');
                if ($exist) {
                    Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'configuration` SET value = "' . pSQL(substr($file, strrpos($file, '.') + 1)) . '" WHERE `name` = \'BLOCKADVERT_IMG_EXT\'');
                } else {
                    Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'configuration` (name, value) VALUES ("BLOCKADVERT_IMG_EXT", "' . pSQL(substr($file, strrpos($file, '.') + 1)) . '"');
                }
            }
        }
    }

    return true;
}
