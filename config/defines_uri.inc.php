<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/* Theme URLs */
define('_PS_DEFAULT_THEME_NAME_', 'classic');
define('_PS_THEME_DIR_', _PS_ROOT_DIR_.'/themes/'._THEME_NAME_.'/');
define('_PS_THEME_URI_', __PS_BASE_URI__.'themes/'._THEME_NAME_.'/');

if (defined('_PARENT_THEME_NAME_') && _PARENT_THEME_NAME_) {
    define('_PS_PARENT_THEME_DIR_', _PS_ROOT_DIR_.'/themes/'._PARENT_THEME_NAME_.'/');
    define('_PS_PARENT_THEME_URI_', __PS_BASE_URI__.'themes/'._PARENT_THEME_NAME_.'/');
} else {
    define('_PS_PARENT_THEME_DIR_', '');
    define('_PS_PARENT_THEME_URI_', '');
}
define('_THEMES_DIR_', __PS_BASE_URI__.'themes/');
define('_THEME_DIR_', _THEMES_DIR_._THEME_NAME_.'/');
define('_THEME_IMG_DIR_', _THEME_DIR_.'assets/img/');
define('_THEME_CSS_DIR_', _THEME_DIR_.'assets/css/');
define('_THEME_JS_DIR_', _THEME_DIR_.'assets/js/');

/* Image URLs */
define('_PS_IMG_', __PS_BASE_URI__.'img/');
define('_PS_ADMIN_IMG_', _PS_IMG_.'admin/');
define('_PS_TMP_IMG_', _PS_IMG_.'tmp/');
define('_THEME_CAT_DIR_', _PS_IMG_.'c/');
define('_THEME_PROD_DIR_', _PS_IMG_.'p/');
define('_THEME_MANU_DIR_', _PS_IMG_.'m/');
define('_THEME_SUP_DIR_', _PS_IMG_.'su/');
define('_THEME_SHIP_DIR_', _PS_IMG_.'s/');
define('_THEME_STORE_DIR_', _PS_IMG_.'st/');
define('_THEME_LANG_DIR_', _PS_IMG_.'l/');
define('_THEME_COL_DIR_', _PS_IMG_.'co/');
define('_THEME_GENDERS_DIR_', _PS_IMG_.'genders/');
define('_PS_PROD_IMG_', _PS_IMG_.'p/');

/* Other URLs */
define('_PS_JS_DIR_', __PS_BASE_URI__.'js/');
define('_PS_CSS_DIR_', __PS_BASE_URI__.'css/');
define('_THEME_PROD_PIC_DIR_', __PS_BASE_URI__.'upload/');
define('_MAIL_DIR_', __PS_BASE_URI__.'mails/');
define('_MODULE_DIR_', __PS_BASE_URI__.'modules/');

/* Define API URLs if not defined before */
Tools::safeDefine('_PS_API_DOMAIN_', 'api.prestashop.com');
Tools::safeDefine('_PS_API_URL_', 'http://'._PS_API_DOMAIN_);
Tools::safeDefine('_PS_TAB_MODULE_LIST_URL_', _PS_API_URL_.'/xml/tab_modules_list_17.xml');
Tools::safeDefine('_PS_API_MODULES_LIST_16_', _PS_API_DOMAIN_.'/xml/modules_list_16.xml');
Tools::safeDefine('_PS_CURRENCY_FEED_URL_', _PS_API_URL_.'/xml/currencies.xml');
