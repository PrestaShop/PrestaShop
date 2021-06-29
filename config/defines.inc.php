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

/* Debug only */
if (!defined('_PS_MODE_DEV_')) {
    define('_PS_MODE_DEV_', true);
}
/* Compatibility warning */
define('_PS_DISPLAY_COMPATIBILITY_WARNING_', true);
if (_PS_MODE_DEV_ === true) {
    $errorReportingLevel = E_ALL | E_STRICT;
    if (_PS_DISPLAY_COMPATIBILITY_WARNING_ === false) {
        $errorReportingLevel = $errorReportingLevel & ~E_DEPRECATED & ~E_USER_DEPRECATED;
    }
    @ini_set('display_errors', 'on');
    @error_reporting($errorReportingLevel);
    define('_PS_DEBUG_SQL_', true);
} else {
    @ini_set('display_errors', 'off');
    define('_PS_DEBUG_SQL_', false);
}

if (!defined('_PS_DEBUG_PROFILING_')) {
    define('_PS_DEBUG_PROFILING_', false);
}
if (!defined('_PS_MODE_DEMO_')) {
    define('_PS_MODE_DEMO_', false);
}

$currentDir = dirname(__FILE__);

if (!defined('_PS_HOST_MODE_') && (getenv('_PS_HOST_MODE_') || getenv('REDIRECT__PS_HOST_MODE_'))) {
    define('_PS_HOST_MODE_', getenv('_PS_HOST_MODE_') ? getenv('_PS_HOST_MODE_') : getenv('REDIRECT__PS_HOST_MODE_'));
}

if (!defined('_PS_ROOT_DIR_') && (getenv('_PS_ROOT_DIR_') || getenv('REDIRECT__PS_ROOT_DIR_'))) {
    define('_PS_ROOT_DIR_', getenv('_PS_ROOT_DIR_') ? getenv('_PS_ROOT_DIR_') : getenv('REDIRECT__PS_ROOT_DIR_'));
}

/* Directories */
if (!defined('_PS_ROOT_DIR_')) {
    define('_PS_ROOT_DIR_', realpath($currentDir.'/..'));
}

if (!defined('_PS_CORE_DIR_')) {
    define('_PS_CORE_DIR_', realpath($currentDir.'/..'));
}

define('_PS_ALL_THEMES_DIR_', _PS_ROOT_DIR_.'/themes/');
/* BO THEMES */
if (defined('_PS_ADMIN_DIR_')) {
    define('_PS_BO_ALL_THEMES_DIR_', _PS_ADMIN_DIR_.'/themes/');
}

// Find if we are running under a Symfony command
$cliEnvValue = null;
if (isset($argv) && is_array($argv)) {
    if (in_array('--env', $argv)) {
        $cliEnvValue = $argv[array_search('--env', $argv) + 1];
    } elseif (in_array('-e', $argv)) {
        $cliEnvValue = $argv[array_search('-e', $argv) + 1];
    }
}

if ((defined('_PS_IN_TEST_') && _PS_IN_TEST_)
    || $cliEnvValue === 'test'
) {
    define('_PS_ENV_', 'test');
} else {
    define('_PS_ENV_', _PS_MODE_DEV_ ? 'dev': 'prod');
}

if (!defined('_PS_CACHE_DIR_')) {
    define('_PS_CACHE_DIR_', _PS_ROOT_DIR_.'/var/cache/' . _PS_ENV_ . DIRECTORY_SEPARATOR);
}

define('_PS_CONFIG_DIR_', _PS_CORE_DIR_.'/config/');
define('_PS_CUSTOM_CONFIG_FILE_', _PS_CONFIG_DIR_.'settings_custom.inc.php');
define('_PS_CLASS_DIR_', _PS_CORE_DIR_.'/classes/');
if (!defined('_PS_DOWNLOAD_DIR_')) {
    $dir = (defined('_PS_IN_TEST_') && _PS_IN_TEST_) ? '/tests/Resources/download/' : '/download/';
    define('_PS_DOWNLOAD_DIR_', _PS_ROOT_DIR_.$dir);
}
define('_PS_MAIL_DIR_', _PS_CORE_DIR_.'/mails/');
if (!defined('_PS_MODULE_DIR_')) {
    define('_PS_MODULE_DIR_', _PS_ROOT_DIR_.'/modules/');
}
if (!defined('_PS_OVERRIDE_DIR_')) {
    define('_PS_OVERRIDE_DIR_', _PS_ROOT_DIR_.'/override/');
}
define('_PS_PDF_DIR_', _PS_CORE_DIR_.'/pdf/');
define('_PS_TRANSLATIONS_DIR_', _PS_ROOT_DIR_.'/translations/');
if (!defined('_PS_UPLOAD_DIR_')) {
    define('_PS_UPLOAD_DIR_', _PS_ROOT_DIR_.'/upload/');
}
define('_PS_CONTROLLER_DIR_', _PS_CORE_DIR_.'/controllers/');
define('_PS_ADMIN_CONTROLLER_DIR_', _PS_CORE_DIR_.'/controllers/admin/');
define('_PS_FRONT_CONTROLLER_DIR_', _PS_CORE_DIR_.'/controllers/front/');

define('_PS_TOOL_DIR_', _PS_CORE_DIR_.'/tools/');
if (!defined('_PS_GEOIP_DIR_')) {
    define('_PS_GEOIP_DIR_', _PS_CORE_DIR_.'/app/Resources/geoip/');
}
if (!defined('_PS_GEOIP_CITY_FILE_')) {
    define('_PS_GEOIP_CITY_FILE_', 'GeoLite2-City.mmdb');
}

define('_PS_VENDOR_DIR_', _PS_CORE_DIR_.'/vendor/');
define('_PS_PEAR_XML_PARSER_PATH_', _PS_TOOL_DIR_.'pear_xml_parser/');
define('_PS_SWIFT_DIR_', _PS_TOOL_DIR_.'swift/');
define('_PS_TAASC_PATH_', _PS_TOOL_DIR_.'taasc/');
define('_PS_TCPDF_PATH_', _PS_TOOL_DIR_.'tcpdf/');

define('_PS_IMG_SOURCE_DIR_', _PS_ROOT_DIR_.'/img/');
if (!defined('_PS_IMG_DIR_')) {
    $dir = (defined('_PS_IN_TEST_') && _PS_IN_TEST_) ? '/tests/Resources/img/' : '/img/';
    define('_PS_IMG_DIR_', _PS_ROOT_DIR_.$dir);
}

if (!defined('_PS_HOST_MODE_')) {
    define('_PS_CORE_IMG_DIR_', _PS_CORE_DIR_.'/img/');
} else {
    define('_PS_CORE_IMG_DIR_', _PS_ROOT_DIR_.'/img/');
}

define('_PS_CAT_IMG_DIR_', _PS_IMG_DIR_.'c/');
define('_PS_COL_IMG_DIR_', _PS_IMG_DIR_.'co/');
define('_PS_EMPLOYEE_IMG_DIR_', _PS_IMG_DIR_.'e/');
define('_PS_GENDERS_DIR_', _PS_IMG_DIR_.'genders/');
define('_PS_LANG_IMG_DIR_', _PS_IMG_DIR_.'l/');
define('_PS_MANU_IMG_DIR_', _PS_IMG_DIR_.'m/');
define('_PS_ORDER_STATE_IMG_DIR_', _PS_IMG_DIR_.'os/');
define('_PS_PROD_IMG_DIR_', _PS_IMG_DIR_.'p/');
define('_PS_PROFILE_IMG_DIR_', _PS_IMG_DIR_.'pr/');
define('_PS_SHIP_IMG_DIR_', _PS_IMG_DIR_.'s/');
define('_PS_STORE_IMG_DIR_', _PS_IMG_DIR_.'st/');
define('_PS_SUPP_IMG_DIR_', _PS_IMG_DIR_.'su/');
define('_PS_TMP_IMG_DIR_', _PS_IMG_DIR_.'tmp/');

/* settings php */
define('_PS_TRANS_PATTERN_', '(.*[^\\\\])');
define('_PS_MIN_TIME_GENERATE_PASSWD_', '360');

if (!defined('_PS_MAGIC_QUOTES_GPC_')) {
    define('_PS_MAGIC_QUOTES_GPC_', false);
}

define('_CAN_LOAD_FILES_', 1);

/* Order statuses
Order statuses have been moved into config.inc.php file for backward compatibility reasons */

/* Tax behavior */
define('PS_PRODUCT_TAX', 0);
define('PS_STATE_TAX', 1);
define('PS_BOTH_TAX', 2);

define('PS_TAX_EXC', 1);
define('PS_TAX_INC', 0);

define('PS_ROUND_UP', 0);
define('PS_ROUND_DOWN', 1);
define('PS_ROUND_HALF_UP', 2);
define('PS_ROUND_HALF_DOWN', 3);
define('PS_ROUND_HALF_EVEN', 4);
define('PS_ROUND_HALF_ODD', 5);

/* Backward compatibility */
define('PS_ROUND_HALF', PS_ROUND_HALF_UP);

/* Carrier::getCarriers() filter */
// these defines are DEPRECATED since 1.4.5 version
define('PS_CARRIERS_ONLY', 1);
define('CARRIERS_MODULE', 2);
define('CARRIERS_MODULE_NEED_RANGE', 3);
define('PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE', 4);
define('ALL_CARRIERS', 5);

/* SQL Replication management */
define('_PS_USE_SQL_SLAVE_', false);

/* PS Technical configuration */
define('_PS_ADMIN_PROFILE_', 1);

/* Stock Movement */
define('_STOCK_MOVEMENT_ORDER_REASON_', 3);
define('_STOCK_MOVEMENT_MISSING_REASON_', 4);

define('_PS_CACHEFS_DIRECTORY_', _PS_ROOT_DIR_.'/cache/cachefs/');

/* Geolocation */
define('_PS_GEOLOCATION_NO_CATALOG_', 0);
define('_PS_GEOLOCATION_NO_ORDER_', 1);

define('MIN_PASSWD_LENGTH', 8);

define('_PS_SMARTY_NO_COMPILE_', 0);
define('_PS_SMARTY_CHECK_COMPILE_', 1);
define('_PS_SMARTY_FORCE_COMPILE_', 2);

define('_PS_SMARTY_CONSOLE_CLOSE_', 0);
define('_PS_SMARTY_CONSOLE_OPEN_BY_URL_', 1);
define('_PS_SMARTY_CONSOLE_OPEN_', 2);

if (!defined('_PS_JQUERY_VERSION_')) {
    define('_PS_JQUERY_VERSION_', '3.4.1');
}

define('_PS_CACHE_CA_CERT_FILE_', _PS_CACHE_DIR_.'cacert.pem');
