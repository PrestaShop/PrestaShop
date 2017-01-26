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


use PrestaShopBundle\Install\Upgrade;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilter;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterStatus;

$filePrefix = 'PREFIX_';
$engineType = 'ENGINE_TYPE';
define('PS_IN_UPGRADE', 1);

$incompatibleModules = [
    'bankwire',
    'blockbanner',
    'blockcart',
    'blockcategories',
    'blockcms',
    'blockcmsinfo',
    'blockcontact',
    'blockcurrencies',
    'blocklanguages',
    'blocklayered',
    'blockmyaccount',
    'blocknewsletter',
    'blocksearch',
    'blocksocial',
    'blocktopmenu',
    'blockuserinfo',
    'cheque',
    'homefeatured',
    'homeslider',
    'onboarding',
    'socialsharing',
    'vatnumber',
    'blockadvertising',
    'blockbestsellers',
    'blockcustomerprivacy',
    'blocklink',
    'blockmanufacturer',
    'blocknewproducts',
    'blockpermanentlinks',
    'blockrss',
    'blocksharefb',
    'blockspecials',
    'blocksupplier',
    'blockviewed',
    'crossselling',
    'followup',
    'productscategory',
    'producttooltip',
    'mailalert',
    'blockcontactinfos',
    'blockfacebook',
    'blockmyaccountfooter',
    'blockpaymentlogo',
    'blockstore',
    'blocktags',
    'blockwishlist',
    'productcomments',
    'productpaymentlogos',
    'sendtoafriend',
    'themeconfigurator'
];

// remove old unsupported classes
@unlink(__DIR__.'/../../classes/db/MySQL.php');

// Set execution time and time_limit to infinite if available
@set_time_limit(0);
@ini_set('max_execution_time', '0');

// setting the memory limit to 128M only if current is lower
$memory_limit = ini_get('memory_limit');
if (substr($memory_limit, -1) != 'G'
    and ((substr($memory_limit, -1) == 'M' and substr($memory_limit, 0, -1) < 128)
    or is_numeric($memory_limit) and (intval($memory_limit) < 131072) and $memory_limit > 0)
) {
    @ini_set('memory_limit', '128M');
}

// redefine REQUEST_URI if empty (on some webservers...)
if (!isset($_SERVER['REQUEST_URI']) || $_SERVER['REQUEST_URI'] == '') {
    if (!isset($_SERVER['SCRIPT_NAME']) && isset($_SERVER['SCRIPT_FILENAME'])) {
        $_SERVER['SCRIPT_NAME'] = $_SERVER['SCRIPT_FILENAME'];
    } else {
        $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
    }
}

if ($tmp = strpos($_SERVER['REQUEST_URI'], '?')) {
    $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 0, $tmp);
}
$_SERVER['REQUEST_URI'] = str_replace('//', '/', $_SERVER['REQUEST_URI']);

// retrocompatibility (is present in some upgrade scripts)
define('INSTALL_PATH', dirname(dirname(__FILE__)).'/');

require_once(INSTALL_PATH . 'install_version.php');

// need for upgrade before 1.5
if (!defined('__PS_BASE_URI__')) {
    define('__PS_BASE_URI__', str_replace('//', '/', '/'.trim(preg_replace('#/(install(-dev)?/upgrade)$#', '/', str_replace('\\', '/', dirname($_SERVER['REQUEST_URI']))), '/').'/'));
}

// need for upgrade before 1.5
if (!defined('_THEME_NAME_')) {
    define('_THEME_NAME_', 'default');
}

require_once(dirname(__FILE__).'/../init.php');
Upgrade::migrateSettingsFile();
require_once(_PS_CONFIG_DIR_.'bootstrap.php');

$logDir = _PS_ROOT_DIR_.'/'.(_PS_MODE_DEV_ ? 'dev' : 'prod').'/log/';
@mkdir($logDir, 0777, true);

Cache::clean('*');

Context::getContext()->shop = new Shop(1);
Shop::setContext(Shop::CONTEXT_SHOP, 1);

if (!isset(Context::getContext()->language) || !Validate::isLoadedObject(Context::getContext()->language)) {
    if ($id_lang = (int)getConfValue('PS_LANG_DEFAULT')) {
        Context::getContext()->language = new Language($id_lang);
    }
}
if (!isset(Context::getContext()->country) || !Validate::isLoadedObject(Context::getContext()->country)) {
    if ($id_country = (int)getConfValue('PS_COUNTRY_DEFAULT')) {
        Context::getContext()->country = new Country((int)$id_country);
    }
}

Context::getContext()->cart = new Cart();
Context::getContext()->employee = new Employee(1);
if (!defined('_PS_SMARTY_FAST_LOAD_')) {
    define('_PS_SMARTY_FAST_LOAD_', true);
}
require_once _PS_ROOT_DIR_.'/config/smarty.config.inc.php';

Context::getContext()->smarty = $smarty;

$upgrade = new Upgrade($logDir);
if (isset($_GET['autoupgrade']) && $_GET['autoupgrade'] == 1) {
    $upgrade->setInAutoUpgrade(true);
}

if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set('Europe/Paris');
}

// if _PS_ROOT_DIR_ is defined, use it instead of "guessing" the module dir.
if (defined('_PS_ROOT_DIR_') and !defined('_PS_MODULE_DIR_')) {
    define('_PS_MODULE_DIR_', _PS_ROOT_DIR_.'/modules/');
} elseif (!defined('_PS_MODULE_DIR_')) {
    define('_PS_MODULE_DIR_', _PS_INSTALL_PATH_.'/../modules/');
}

if (!defined('_PS_INSTALLER_PHP_UPGRADE_DIR_')) {
    define('_PS_INSTALLER_PHP_UPGRADE_DIR_', _PS_INSTALL_PATH_.'upgrade/php/');
}

if (!defined('_PS_INSTALLER_SQL_UPGRADE_DIR_')) {
    define('_PS_INSTALLER_SQL_UPGRADE_DIR_', _PS_INSTALL_PATH_.'upgrade/sql/');
}

if (!defined('_THEMES_DIR_')) {
    define('_THEMES_DIR_', __PS_BASE_URI__.'themes/');
}
if (!defined('_PS_IMG_')) {
    define('_PS_IMG_', __PS_BASE_URI__.'img/');
}
if (!defined('_PS_JS_DIR_')) {
    define('_PS_JS_DIR_', __PS_BASE_URI__.'js/');
}
if (!defined('_PS_CSS_DIR_')) {
    define('_PS_CSS_DIR_', __PS_BASE_URI__.'css/');
}

$oldversion = Configuration::get('PS_VERSION_DB');
if (empty($oldversion)) {
    $oldversion = Configuration::get('PS_INSTALL_VERSION');
}

$versionCompare =  version_compare(_PS_INSTALL_VERSION_, $oldversion);

// refresh conf file
require_once(_PS_INSTALL_PATH_.'upgrade/classes/AddConfToFile.php');

$oldLevel = error_reporting(E_ALL);
$mysqlEngine = (defined('_MYSQL_ENGINE_') ? _MYSQL_ENGINE_ : 'MyISAM');

if (defined('_PS_CACHING_SYSTEM_') and _PS_CACHING_SYSTEM_ == 'CacheFS') {
    $cache_engine = 'CacheFs';
} elseif (defined('_PS_CACHING_SYSTEM_') and _PS_CACHING_SYSTEM_ != 'CacheMemcache' and _PS_CACHING_SYSTEM_ != 'CacheMemcached') {
    $cache_engine = _PS_CACHING_SYSTEM_;
} else {
    $cache_engine = 'CacheMemcache';
}
$datas = array(
    array('_DB_SERVER_', _DB_SERVER_),
    array('_DB_NAME_', _DB_NAME_),
    array('_DB_USER_', _DB_USER_),
    array('_DB_PASSWD_', _DB_PASSWD_),
    array('_DB_PREFIX_', _DB_PREFIX_),
    array('_MYSQL_ENGINE_', $mysqlEngine),
    array('_PS_CACHING_SYSTEM_', $cache_engine),
    array('_PS_CACHE_ENABLED_', defined('_PS_CACHE_ENABLED_') ? _PS_CACHE_ENABLED_ : '0'),
    // 1.4 only
    // array('__PS_BASE_URI__', __PS_BASE_URI__),
    // 1.4 only
    // array('_THEME_NAME_', _THEME_NAME_),
    array('_PS_DIRECTORY_', __PS_BASE_URI__),
    array('_COOKIE_KEY_', _COOKIE_KEY_),
    array('_COOKIE_IV_', _COOKIE_IV_),
    array('_PS_CREATION_DATE_', defined("_PS_CREATION_DATE_") ? _PS_CREATION_DATE_ : date('Y-m-d')),
    array('_PS_VERSION_', _PS_INSTALL_VERSION_)
);

if (defined('_RIJNDAEL_KEY_')) {
    $datas[] = array('_RIJNDAEL_KEY_', _RIJNDAEL_KEY_);
}
if (defined('_RIJNDAEL_IV_')) {
    $datas[] = array('_RIJNDAEL_IV_', _RIJNDAEL_IV_);
}
if (!defined('_PS_CACHE_ENABLED_')) {
    define('_PS_CACHE_ENABLED_', '0');
}
if (!defined('_MYSQL_ENGINE_')) {
    define('_MYSQL_ENGINE_', 'MyISAM');
}

if ($versionCompare == '-1') {
    $upgrade->logError('Current version: %current%. Version to install: %future%.', 27, array('%current%' => $oldversion, '%future%' => _PS_INSTALL_VERSION_));
} elseif ($versionCompare == 0) {
    $upgrade->logError('You already have the %future% version.', 28, array('%future%' => _PS_INSTALL_VERSION_));
} elseif ($versionCompare === false) {
    $upgrade->logError('There is no older version. Did you delete or rename the app/config/parameters.php file?', 29);
}

//
//custom sql file creation
$upgradeFiles = array();
if (!$upgrade->hasFailure()) {
    if (!file_exists(_PS_INSTALLER_SQL_UPGRADE_DIR_)) {
        $upgrade->logError('Unable to find upgrade directory in the installation path.', 31);
    }

    if ($handle = opendir(_PS_INSTALLER_SQL_UPGRADE_DIR_)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' and $file != '..') {
                $upgradeFiles[] = str_replace(".sql", "", $file);
            }
        }
        closedir($handle);
    }
    if (empty($upgradeFiles)) {
        $upgrade->logError('Cannot find the SQL upgrade files. Please verify that the %folder% folder is not empty)', 31, array('%folder%' => _PS_INSTALLER_SQL_UPGRADE_DIR_));
    }
    natcasesort($upgradeFiles);
}

// fix : complete version number if there is not all 4 numbers
// for example replace 1.4.3 by 1.4.3.0
// consequences : file 1.4.3.0.sql will be skipped if oldversion = 1.4.3
// @since 1.4.4.0
$arrayVersion = preg_split('#\.#', $oldversion);
$versionNumbers = sizeof($arrayVersion);

if ($versionNumbers != 4) {
    $arrayVersion = array_pad($arrayVersion, 4, '0');
}

$oldversion = implode('.', $arrayVersion);
// end of fix
$neededUpgradeFiles = array();
foreach ($upgradeFiles as $version) {
    if (version_compare($version, $oldversion) == 1 and version_compare(_PS_INSTALL_VERSION_, $version) != -1) {
        $neededUpgradeFiles[] = $version;
    }
}

if (strpos(_PS_INSTALL_VERSION_, '.') === false) {
    $upgrade->logError('%install_version% is not a valid version number.', 40,
        array('%install_version%' => _PS_INSTALL_VERSION_));
}

if (!$upgrade->hasFailure() && empty($neededUpgradeFiles)) {
    $upgrade->logError('No upgrade is possible.', 32);
}

$sqlContentVersion = array();
if (!$upgrade->hasFailure()) {
    foreach ($neededUpgradeFiles as $version) {
        $file = _PS_INSTALLER_SQL_UPGRADE_DIR_.$version.'.sql';
        if (!file_exists($file)) {
            $upgrade->logError('Error while loading SQL upgrade file "%file%.sql".', 33, array('%file%' => $version));
        }
        if (!$sqlContent = file_get_contents($file)) {
            $upgrade->logError('Error while loading SQL upgrade file "%file%.sql".', 33, array('%file%' => $version));
        }
        $sqlContent .= "\n";
        $sqlContent = str_replace(array($filePrefix, $engineType), array(_DB_PREFIX_, $mysqlEngine), $sqlContent);
        $sqlContent = preg_split("/;\s*[\r\n]+/", $sqlContent);

        $sqlContentVersion[$version] = $sqlContent;
    }
}

$sf2Refresh = new \PrestaShopBundle\Service\Cache\Refresh();
$sf2Refresh->addDoctrineSchemaUpdate();
$output = $sf2Refresh->execute();

if (0 !== $output['doctrine:schema:update']['exitCode']) {
    $msgErrors = explode("\n", $output['doctrine:schema:update']['output']);
    $upgrade->logError('Error upgrading doctrine schema : '.$msgErrors, 43);
}

if (!$upgrade->hasFailure()) {
    Language::loadLanguages();

    if (isset($_GET['deactivateCustomModule']) and $_GET['deactivateCustomModule'] == '1') {
        require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'deactivate_custom_modules.php');
        deactivate_custom_modules();
    }

    // Disable the old incompatible modules
    $disableModules = function() use ($incompatibleModules, $upgrade)
    {
        $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $moduleManagerRepository = $moduleManagerBuilder->buildRepository();
        $moduleManagerRepository->clearCache();

        $filter = new AddonListFilter();
        $filter->setStatus(AddonListFilterStatus::ON_DISK|AddonListFilterStatus::INSTALLED);

        $list = $moduleManagerRepository->getFilteredList($filter, true);
        /**
         * @var $module \PrestaShop\PrestaShop\Adapter\Module\Module
         */
        foreach ($list as $moduleName => $module) {
            if (in_array($moduleName, $incompatibleModules)) {
                $upgrade->logInfo("Uninstalling module $moduleName, not supported in this prestashop version.");
                $module->onUninstall();
            } else {
                $moduleInfo = $moduleManagerRepository->getModule($moduleName, true);
                /** @var \Symfony\Component\HttpFoundation\ParameterBag $attributes */
                $attributes = $module->attributes;
                if ($attributes->get('compatibility')) {
                    $maxVersion = $attributes->get('compatibility')->to;
                    if (version_compare($maxVersion, _PS_INSTALL_VERSION_) == -1 && Module::isEnabled($moduleName)) {
                        $upgrade->logInfo("Disabling module $moduleName. Max supported version : ".$maxVersion);
                        Module::disableAllByName($moduleName);
                    }
                }
            }
        }
        ////
    };
    $disableModules();
    $db = Db::getInstance();

    foreach ($sqlContentVersion as $version => $sqlContent) {
        foreach ($sqlContent as $query) {
            $query = trim($query);
            if (!empty($query)) {
                /* If php code have to be executed */
                if (strpos($query, '/* PHP:') !== false) {
                    /* Parsing php code */
                    $pos = strpos($query, '/* PHP:') + strlen('/* PHP:');
                    $phpString = substr($query, $pos, strlen($query) - $pos - strlen(' */;'));
                    $php = explode('::', $phpString);
                    preg_match('/\((.*)\)/', $phpString, $pattern);
                    $paramsString = trim($pattern[0], '()');
                    preg_match_all('/([^,]+),? ?/', $paramsString, $parameters);
                    if (isset($parameters[1])) {
                        $parameters = $parameters[1];
                    } else {
                        $parameters = array();
                    }
                    if (is_array($parameters)) {
                        foreach ($parameters as &$parameter) {
                            $parameter = str_replace('\'', '', $parameter);
                        }
                    }

                    $phpRes = null;
                    /* Call a simple function */
                    if (strpos($phpString, '::') === false) {
                        $func_name = str_replace($pattern[0], '', $php[0]);
                        if (!file_exists(_PS_INSTALLER_PHP_UPGRADE_DIR_.strtolower($func_name).'.php'))
                        {
                            $upgrade->logWarning('[ERROR] '.$version.' PHP - missing file '.$query, 41, array(), true);
                        } else {
                            require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_ . Tools::strtolower($func_name) . '.php');
                            $phpRes = call_user_func_array($func_name, $parameters);
                        }
                    } else {
                        /* Or an object method, not supported */
                        $upgrade->logWarning('[ERROR] '.$version.' PHP - Object Method call is forbidden ('.$php[0].'::'.str_replace($pattern[0], '', $php[1]).')', 42, array(), true);
                    }
                    if ((is_array($phpRes) and !empty($phpRes['error'])) || $phpRes === false) {
                        $upgrade->logWarning('[ERROR] PHP '.$version.' '.$query."\n".'
								'.(empty($phpRes['error']) ? '' : $phpRes['error']."\n").'
								'.(empty($phpRes['msg']) ? '' : ' - '.$phpRes['msg']), $version, array(), true);
                    } else {
                        $upgrade->logInfo('[OK] PHP '.$version.' : '.$query, $version, array(), true);
                    }
                } else {
                    if (!$db->execute($query)) {
                        $error = $db->getMsgError();
                        $error_number = $db->getNumberError();

                        $duplicates = array('1050', '1054', '1060', '1061', '1062', '1091');
                        if (!in_array($error_number, $duplicates))
                        {
                            $upgrade->logWarning('SQL '.$version.'
								'.$error_number.' in '.$query.': '.$error, $version, array(), true);
                        } else {
                            $upgrade->logInfo('SQL '.$version.'
								'.$error_number.' in '.$query.': '.$error, $version, array(), true);
                        }
                    } else {
                        $upgrade->logInfo('[OK] SQL '.$version.' : '.$query, $version, array(), true);
                    }
                }
            }
        }
    }
    Configuration::loadConfiguration();

    $enableNativeModules = function() use ($upgrade) {
        $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $moduleManagerRepository = $moduleManagerBuilder->buildRepository();
        $moduleManagerRepository->clearCache();

        $catalog = $moduleManagerBuilder::$adminModuleDataProvider->getCatalogModules();
        foreach ($catalog as $moduleName => $module) {
            if ($module->categoryName == 'Natif') {
                if (!$moduleManagerBuilder->build()->isInstalled($moduleName)) {
                    $upgrade->logInfo("Installing native module ".$moduleName);
                    $module = $moduleManagerRepository->getModule($moduleName);
                    $module->onInstall();
                } else {
                    $upgrade->logInfo("Native module ".$moduleName." already installed");
                }
            }
        }
    };

    $enableNativeModules();

    // Settings updated, compile and cache directories must be emptied
    $tools_dir = rtrim(_PS_INSTALL_PATH_, '\\/').DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'tools'.DIRECTORY_SEPARATOR;
    $arrayToClean = array(
        $tools_dir.'smarty'.DIRECTORY_SEPARATOR.'cache',
        $tools_dir.'smarty'.DIRECTORY_SEPARATOR.'compile',
        $tools_dir.'smarty_v2'.DIRECTORY_SEPARATOR.'cache',
        $tools_dir.'smarty_v2'.DIRECTORY_SEPARATOR.'compile',
        $tools_dir.'app/cache/',
        $tools_dir.'cache/smarty/cache/',
        $tools_dir.'cache/smarty/compile/'
    );

    foreach ($arrayToClean as $dir) {
        if (file_exists($dir)) {
            foreach (scandir($dir) as $file) {
                if ($file[0] != '.' and $file != 'index.php' and $file != '.htaccess') {
                    if (is_file($dir.$file)) {
                        unlink($dir . $file);
                    } elseif (is_dir($dir.$file.DIRECTORY_SEPARATOR)) {
                        Tools14::deleteDirectory($dir . $file . DIRECTORY_SEPARATOR, true);
                    }
                    $upgrade->logInfo('[CLEANING CACHE] File %file% removed', null, array('%file%' => $file));
                }
            }
        } else {
            $upgrade->logWarning('[SKIP] directory "%directory%" does not exist and cannot be emptied.', null, array('%directory%' => str_replace($tools_dir, '', $dir)));
        }
    }

    $db->execute('UPDATE `'._DB_PREFIX_.'configuration` SET `name` = \'PS_LEGACY_IMAGES\' WHERE name LIKE \'0\' AND `value` = 1');
    $db->execute('UPDATE `'._DB_PREFIX_.'configuration` SET `value` = 0 WHERE `name` LIKE \'PS_LEGACY_IMAGES\'');
    if ($db->getValue('SELECT COUNT(id_product_download) FROM `'._DB_PREFIX_.'product_download` WHERE `active` = 1') > 0)
        $db->execute('UPDATE `'._DB_PREFIX_.'configuration` SET `value` = 1 WHERE `name` LIKE \'PS_VIRTUAL_PROD_FEATURE_ACTIVE\'');

    if (defined('_THEME_NAME_') && isset($_GET['updateDefaultTheme']) && $_GET['updateDefaultTheme']
        && 'classic' === _THEME_NAME_)
    {
        $separator = addslashes(DIRECTORY_SEPARATOR);
        $file = _PS_ROOT_DIR_.$separator.'themes'.$separator._THEME_NAME_.$separator.'cache'.$separator;
        if (file_exists($file)) {
            foreach (scandir($file) as $cache) {
                if ($cache[0] != '.' && $cache != 'index.php' && $cache != '.htaccess' && file_exists($file.$cache) && !is_dir($file.$cache)) {
                    if (file_exists($file.$cache)) {
                        unlink($file.$cache);
                    }
                }
            }
        }
    }

    // Upgrade languages
    if (!defined('_PS_TOOL_DIR_')) {
        define('_PS_TOOL_DIR_', _PS_ROOT_DIR_.'/tools/');
    }
    if (!defined('_PS_TRANSLATIONS_DIR_')) {
        define('_PS_TRANSLATIONS_DIR_', _PS_ROOT_DIR_.'/translations/');
    }
    if (!defined('_PS_MODULES_DIR_')) {
        define('_PS_MODULES_DIR_', _PS_ROOT_DIR_.'/modules/');
    }
    if (!defined('_PS_MAILS_DIR_')) {
        define('_PS_MAILS_DIR_', _PS_ROOT_DIR_.'/mails/');
    }

    $langs = $db->executeS('SELECT * FROM `'._DB_PREFIX_.'lang` WHERE `active` = 1');

    if (is_array($langs)) {
        foreach ($langs as $lang) {
            $isoCode = $lang['iso_code'];

            if (Validate::isLangIsoCode($isoCode)) {
                $errorsLanguage = array();

                Language::downloadLanguagePack($isoCode, _PS_VERSION_, $errorsLanguage);

                $lang_pack = Language::getLangDetails($isoCode);
                Language::installSfLanguagePack($lang_pack['locale'], $errorsLanguage);

                if (isset($_GET['keepMails']) && !$_GET['keepMails']) {
                    Language::installEmailsLanguagePack($lang_pack, $errorsLanguage);
                }

                if (empty($errorsLanguage)) {
                    Language::loadLanguages();

                    // TODO: Update AdminTranslationsController::addNewTabs to install tabs translated

                    $cldrUpdate = new \PrestaShop\PrestaShop\Core\Cldr\Update(_PS_TRANSLATIONS_DIR_);
                    $cldrUpdate->fetchLocale(Language::getLocaleByIso($isoCode));
                } else {
                    $upgrade->logError('Error updating translations', 44);
                }
            }
        }
    }

    if (file_exists(_PS_ROOT_DIR_.'/classes/Tools.php')) {
        require_once(_PS_ROOT_DIR_.'/classes/Tools.php');
    }
    if (!class_exists('Tools2', false) and class_exists('ToolsCore')) {
        eval('class Tools2 extends ToolsCore{}');
    }

    if (class_exists('Tools2') && method_exists('Tools2', 'generateHtaccess')) {
        $url_rewrite = (bool)$db->getvalue('SELECT `value` FROM `'._DB_PREFIX_.'configuration` WHERE name=\'PS_REWRITING_SETTINGS\'');

        if (!defined('_MEDIA_SERVER_1_')) {
            define('_MEDIA_SERVER_1_', '');
        }

        if (!defined('_PS_USE_SQL_SLAVE_')) {
            define('_PS_USE_SQL_SLAVE_', false);
        }

        Tools2::generateHtaccess(null, $url_rewrite);
    }

    if (isset($_GET['adminDir']) && $_GET['adminDir']) {
        $adminDir = base64_decode($_GET['adminDir']);
        $path = $adminDir . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR
                . 'template' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'modules'
                . DIRECTORY_SEPARATOR . 'header.tpl';
        if (file_exists($path)) {
            unlink($path);
        }
    }

    if (file_exists(_PS_ROOT_DIR_.'/app/cache/dev/class_index.php')) {
        unlink(_PS_ROOT_DIR_.'/app/cache/dev/class_index.php');
    }
    if (file_exists(_PS_ROOT_DIR_.'/app/cache/prod/class_index.php')) {
        unlink(_PS_ROOT_DIR_.'/app/cache/prod/class_index.php');
    }

    // Clear XML files
    if (file_exists(_PS_ROOT_DIR_.'/config/xml/blog-fr.xml')) {
        unlink(_PS_ROOT_DIR_.'/config/xml/blog-fr.xml');
    }
    if (file_exists(_PS_ROOT_DIR_.'/config/xml/default_country_modules_list.xml')) {
        unlink(_PS_ROOT_DIR_.'/config/xml/default_country_modules_list.xml');
    }
    if (file_exists(_PS_ROOT_DIR_.'/config/xml/modules_list.xml')) {
        unlink(_PS_ROOT_DIR_.'/config/xml/modules_list.xml');
    }
    if (file_exists(_PS_ROOT_DIR_.'/config/xml/modules_native_addons.xml')) {
        unlink(_PS_ROOT_DIR_.'/config/xml/modules_native_addons.xml');
    }
    if (file_exists(_PS_ROOT_DIR_.'/config/xml/must_have_modules_list.xml')) {
        unlink(_PS_ROOT_DIR_.'/config/xml/must_have_modules_list.xml');
    }
    if (file_exists(_PS_ROOT_DIR_.'/config/xml/tab_modules_list.xml')) {
        unlink(_PS_ROOT_DIR_.'/config/xml/tab_modules_list.xml');
    }
    if (file_exists(_PS_ROOT_DIR_.'/config/xml/trusted_modules_list.xml')) {
        unlink(_PS_ROOT_DIR_.'/config/xml/trusted_modules_list.xml');
    }
    if (file_exists(_PS_ROOT_DIR_.'/config/xml/untrusted_modules_list.xml')) {
        unlink(_PS_ROOT_DIR_.'/config/xml/untrusted_modules_list.xml');
    }

    if (isset($_GET['deactivateCustomModule']) && $_GET['deactivateCustomModule'] == 1) {
        $exist = $db->getValue('SELECT `id_configuration` FROM `'._DB_PREFIX_.'configuration` WHERE `name` LIKE \'PS_DISABLE_OVERRIDES\'');
        if ($exist) {
            $db->execute('UPDATE `'._DB_PREFIX_.'configuration` SET value = 1 WHERE `name` LIKE \'PS_DISABLE_OVERRIDES\'');
        } else {
            $db->execute('INSERT INTO `'._DB_PREFIX_.'configuration` (name, value, date_add, date_upd) VALUES ("PS_DISABLE_OVERRIDES", 1, NOW(), NOW())');
        }

        if (class_exists('PrestaShopAutoload') && method_exists('PrestaShopAutoload', 'generateIndex')) {
            PrestaShopAutoload::getInstance()->_include_override_path = false;
            PrestaShopAutoload::getInstance()->generateIndex();
        }
    }

    if (isset($_GET['idEmployee'])) {
        $themeManager = getThemeManager($_GET['idEmployee']);
        $themeName = ((isset($_GET['changeToDefaultTheme']) && $_GET['changeToDefaultTheme'] == 1) ? 'classic' : _THEME_NAME_);

        $isThemeEnabled = $themeManager->enable($themeName);
        if (!$isThemeEnabled) {
            $themeErrors = $themeManager->getErrors($themeName);
            $upgrade->logError($themeErrors, 45);
        } else {
            Tools::clearCache();
        }
    }

}
$result = '<?xml version="1.0" encoding="UTF-8"?>';
if (empty($upgrade->hasFailure())) {
    Configuration::updateValue('PS_HIDE_OPTIMIZATION_TIPS', 0);
    Configuration::updateValue('PS_NEED_REBUILD_INDEX', 1);
    Configuration::updateValue('PS_VERSION_DB', _PS_INSTALL_VERSION_);
    $result .= '<action result="ok" id="">'."\n";
    foreach($upgrade->getInfoList() as $info) {
        $result .= $info."\n";
    }

    foreach($upgrade->getWarningList() as $warning) {
        $result .= $warning."\n";
    }
} else {
    foreach($upgrade->getFailureList() as $failure) {
        $result .= $failure."\n";
    }
}

if ($upgrade->getInAutoUpgrade()) {
    header('Content-Type: application/json');
    echo json_encode(array('nextQuickInfo' => $upgrade->getNextQuickInfo(), 'nextErrors' => $upgrade->getNextErrors(),
                            'next' => $upgrade->getNext(), 'next_desc' => $upgrade->getNextDesc()));
} else {
    header('Content-Type: text/xml');
    echo $result;
}

function getConfValue($name)
{
    $full = version_compare('1.5.0.10', _PS_VERSION_) < 0;

    $sql = 'SELECT IF(cl.`id_lang` IS NULL, c.`value`, cl.`value`) AS value
			FROM `'._DB_PREFIX_.'configuration` c
			LEFT JOIN `'._DB_PREFIX_.'configuration_lang` cl ON (c.`id_configuration` = cl.`id_configuration`)
			WHERE c.`name`=\''.pSQL($name).'\'';

    if ($full) {
        $id_shop = Shop::getContextShopID(true);
        $id_shop_group = Shop::getContextShopGroupID(true);
        if ($id_shop) {
            $sql .= ' AND c.`id_shop` = '.(int)$id_shop;
        }
        if ($id_shop_group) {
            $sql .= ' AND c.`id_shop_group` = '.(int)$id_shop_group;
        }
    }
    return Db::getInstance()->getValue($sql);
}

function getThemeManager($id_employee)
{
    $context = Context::getContext();
    $context->employee = new Employee((int) $id_employee);

    return (new \PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManagerBuilder($context, Db::getInstance()))->build();
}