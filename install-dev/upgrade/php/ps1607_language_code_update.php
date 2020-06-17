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

function ps1607_language_code_update()
{
    if (defined('_PS_VERSION_')) {
        $langs = Db::getInstance()->executeS('SELECT `id_lang`, `iso_code`, `language_code` FROM `'._DB_PREFIX_.'lang`');
        if (is_array($langs) && $langs) {
            foreach ($langs as $lang) {
                if (Tools::strlen($lang['language_code']) == 2) {
                    $result = json_decode(Tools::file_get_contents('https://www.prestashop.com/download/lang_packs/get_language_pack.php?version='._PS_VERSION_.'&iso_lang='.Tools::strtolower($lang['iso_code'])));
                    if ($result && !isset($result->error) && Tools::strlen($result->language_code) > 2) {
                        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'lang` SET `language_code` = \''.pSQL($result->language_code).'\' WHERE `id_lang` = '.(int)$lang['id_lang']).' LIMIT 1';
                    }
                }
            }
        }
    }

    return true;
}
