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

function update_image_size_in_db()
{
    $logo_name = Db::getInstance()->getValue('SELECT `value` FROM `'._DB_PREFIX_.'configuration` WHERE `name` = \'PS_LOGO\'');
    $logo_name = (!empty($logo_name)) ? $logo_name : 'logo.jpg';

    if (file_exists(realpath(INSTALL_PATH.'/../img').'/'.$logo_name)) {
        list($width, $height, $type, $attr) = getimagesize(realpath(INSTALL_PATH.'/../img').'/'.$logo_name);
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'configuration` SET value = "'.(int)round($width).'" WHERE `name` = \'SHOP_LOGO_WIDTH\'');
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'configuration` SET value = "'.(int)round($height).'" WHERE `name` = \'SHOP_LOGO_HEIGHT\'');
    }
    if (file_exists(realpath(INSTALL_PATH.'/../modules/editorial').'/homepage_logo.jpg')) {
        list($width, $height, $type, $attr) = getimagesize(realpath(INSTALL_PATH.'/../modules/editorial').'/homepage_logo.jpg');
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'configuration` SET value = "'.(int)round($width).'" WHERE `name` = \'EDITORIAL_IMAGE_WIDTH\'');
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'configuration` SET value = "'.(int)round($height).'" WHERE `name` = \'EDITORIAL_IMAGE_HEIGHT\'');
    }
}
