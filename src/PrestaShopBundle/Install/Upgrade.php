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

namespace {
    $root_dir = realpath(__DIR__.'/../../..');

    require_once $root_dir.'/vendor/paragonie/random_compat/lib/random.php';

    if (!class_exists('PhpEncryptionEngine')) {
        require_once $root_dir.'/classes/PhpEncryptionEngine.php';
        class PhpEncryptionEngine extends \PhpEncryptionEngineCore
        {
        }
    }

    if (!class_exists('PhpEncryptionLegacyEngine')) {
        require_once $root_dir.'/classes/PhpEncryptionLegacyEngine.php';
        class PhpEncryptionLegacyEngine extends \PhpEncryptionLegacyEngineCore
        {
        }
    }

    if (!class_exists('PhpEncryption')) {
        require_once $root_dir.'/classes/PhpEncryption.php';
        class PhpEncryption extends \PhpEncryptionCore
        {
        }
    }
}

namespace PrestaShopBundle\Install {

    use Symfony\Component\Yaml\Yaml;
    use Symfony\Component\Filesystem\Filesystem;
    use Symfony\Component\Filesystem\Exception\IOException;
    use Context;
    use Cache;
    use Shop;
    use Validate;
    use Country;
    use Cart;
    use Employee;
    use RandomLib;
    use Language;
    use Configuration;
    use Composer\Script\Event;
    use PhpEncryption;
    use Db;
    use Tools;
    use Module;
    use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
    use PrestaShop\PrestaShop\Core\Addon\AddonListFilter;
    use PrestaShop\PrestaShop\Core\Addon\AddonListFilterStatus;

    class Upgrade
    {
        /** @var \FileLogger */
        private $logger;
        private $infoList = array();
        private $warningList = array();
        private $failureList = array();
        private $nextQuickInfo = array();
        private $nextErrors = array();
        private $next;
        private $nextDesc;
        private $inAutoUpgrade = false;
        private $translator;
        private $installDir;
        private $adminDir = null;
        private $oldVersion;
        private $db;
        private $idEmployee = 0;
        private $disableCustomModules = false;
        private $changeToDefaultTheme = false;
        private $updateDefaultTheme = false;
        // used for translations
        public static $l_cache;

        const FILE_PREFIX = 'PREFIX_';
        const ENGINE_TYPE = 'ENGINE_TYPE';

        private static $classes14 = ['Cache', 'CacheFS', 'CarrierModule', 'Db', 'FrontController', 'Helper','ImportModule',
            'MCached', 'Module', 'ModuleGraph', 'ModuleGraphEngine', 'ModuleGrid', 'ModuleGridEngine',
            'MySQL', 'Order', 'OrderDetail', 'OrderDiscount', 'OrderHistory', 'OrderMessage', 'OrderReturn',
            'OrderReturnState', 'OrderSlip', 'OrderState', 'PDF', 'RangePrice', 'RangeWeight', 'StockMvt',
            'StockMvtReason', 'SubDomain', 'Shop', 'Tax', 'TaxRule', 'TaxRulesGroup', 'WebserviceKey', 'WebserviceRequest', ''];


        private static $incompatibleModules = [
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


        public function __construct($cacheDir, $installDir)
        {
            $this->logger = new \FileLogger();
            $this->logger->setFilename($cacheDir.@date('Ymd').'_upgrade.log');
            $this->installDir = $installDir;
            $this->db = Db::getInstance();
        }

        public function setDisableCustomModules($value)
        {
            $this->disableCustomModules = (bool)$value;
        }

        public function setUpdateDefaultTheme($value)
        {
            $this->updateDefaultTheme = (bool)$value;
        }

        public function setAdminDir($value)
        {
            $this->adminDir = $value;
        }

        public function setIdEmployee($id)
        {
            $this->idEmployee = (int)$id;
        }

        public function setChangeToDefaultTheme($value)
        {
            $this->changeToDefaultTheme = (bool)$value;
        }

        private function defineConst()
        {
            // retrocompatibility (is present in some upgrade scripts)
            define('INSTALL_PATH', $this->installDir);
            require_once(INSTALL_PATH . 'install_version.php');
            // needed for upgrade before 1.5
            if (!defined('__PS_BASE_URI__')) {
                define('__PS_BASE_URI__', str_replace('//', '/', '/'.trim(preg_replace('#/(install(-dev)?/upgrade)$#', '/', str_replace('\\', '/', dirname($_SERVER['REQUEST_URI']))), '/').'/'));
            }
            if (!defined('_THEME_NAME_')) {
                define('_THEME_NAME_', 'default');
            }
            if (!defined('_PS_SMARTY_FAST_LOAD_')) {
                define('_PS_SMARTY_FAST_LOAD_', true);
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

            $this->oldVersion = Configuration::get('PS_VERSION_DB');
            if (empty($this->oldVersion)) {
                $this->oldVersion = Configuration::get('PS_INSTALL_VERSION');
            }
            // fix : complete version number if there is not all 4 numbers
            // for example replace 1.4.3 by 1.4.3.0
            // consequences : file 1.4.3.0.sql will be skipped if oldversion = 1.4.3
            // @since 1.4.4.0
            $arrayVersion = preg_split('#\.#', $this->oldVersion);
            $versionNumbers = sizeof($arrayVersion);

            if ($versionNumbers != 4) {
                $arrayVersion = array_pad($arrayVersion, 4, '0');
            }

            $this->oldVersion = implode('.', $arrayVersion);
            // end of fix

            if (!defined('_PS_CACHE_ENABLED_')) {
                define('_PS_CACHE_ENABLED_', '0');
            }
            if (!defined('_MYSQL_ENGINE_')) {
                define('_MYSQL_ENGINE_', 'MyISAM');
            }

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
            if (!defined('_MEDIA_SERVER_1_')) {
                define('_MEDIA_SERVER_1_', '');
            }

            if (!defined('_PS_USE_SQL_SLAVE_')) {
                define('_PS_USE_SQL_SLAVE_', false);
            }
        }

        private function initContext()
        {
            $smarty = null;
            Cache::clean('*');

            Context::getContext()->shop = new Shop(1);
            Shop::setContext(Shop::CONTEXT_SHOP, 1);

            if (!isset(Context::getContext()->language) || !Validate::isLoadedObject(Context::getContext()->language)) {
                if ($id_lang = (int)$this->getConfValue('PS_LANG_DEFAULT')) {
                    Context::getContext()->language = new Language($id_lang);
                }
            }
            if (!isset(Context::getContext()->country) || !Validate::isLoadedObject(Context::getContext()->country)) {
                if ($id_country = (int)$this->getConfValue('PS_COUNTRY_DEFAULT')) {
                    Context::getContext()->country = new Country((int)$id_country);
                }
            }

            Context::getContext()->cart = new Cart();
            Context::getContext()->employee = new Employee(1);

            require_once _PS_ROOT_DIR_.'/config/smarty.config.inc.php';

            Context::getContext()->smarty = $smarty;
            Language::loadLanguages();

            $this->translator = Context::getContext()->getTranslator();
            $this->nextDesc = $this->getTranslator()->trans('Database upgrade completed.', array(), 'Install');
        }

        private function getConfValue($name)
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
            return $this->db->getValue($sql);
        }

        private function getThemeManager($idEmployee)
        {
            $context = Context::getContext();
            $context->employee = new Employee((int) $idEmployee);

            return (new \PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManagerBuilder($context, Db::getInstance()))->build();
        }

        private function checkVersion()
        {
            $versionCompare =  version_compare(_PS_INSTALL_VERSION_, $this->oldVersion);
            if ($versionCompare == '-1') {
                $this->logError('Current version: %current%. Version to install: %future%.', 27, array('%current%' => $this->oldVersion, '%future%' => _PS_INSTALL_VERSION_));
            } elseif ($versionCompare == 0) {
                $this->logError('You already have the %future% version.', 28, array('%future%' => _PS_INSTALL_VERSION_));
            } elseif ($versionCompare === false) {
                $this->logError('There is no older version. Did you delete or rename the app/config/parameters.php file?', 29);
            }

            if (strpos(_PS_INSTALL_VERSION_, '.') === false) {
                $this->logError('%install_version% is not a valid version number.', 40,
                    array('%install_version%' => _PS_INSTALL_VERSION_));
            }
        }

        private function getSQLFiles()
        {
            //custom sql file creation
            $neededUpgradeFiles = array();
            if (!$this->hasFailure()) {
                $upgradeFiles = array();
                if (!file_exists(_PS_INSTALLER_SQL_UPGRADE_DIR_)) {
                    $this->logError('Unable to find upgrade directory in the installation path.', 31);
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
                    $this->logError('Cannot find the SQL upgrade files. Please verify that the %folder% folder is not empty)', 31, array('%folder%' => _PS_INSTALLER_SQL_UPGRADE_DIR_));
                }
                natcasesort($upgradeFiles);

                $neededUpgradeFiles = array();
                foreach ($upgradeFiles as $version) {
                    if (version_compare($version, $this->oldVersion) == 1 && version_compare(_PS_INSTALL_VERSION_, $version) != -1) {
                        $neededUpgradeFiles[] = $version;
                    }
                }
            }
            if (!$this->hasFailure() && empty($neededUpgradeFiles)) {
                $this->logError('No upgrade is possible.', 32);
            }

            $sqlContentVersion = array();
            $mysqlEngine = (defined('_MYSQL_ENGINE_') ? _MYSQL_ENGINE_ : 'MyISAM');
            if (!$this->hasFailure()) {
                foreach ($neededUpgradeFiles as $version) {
                    $file = _PS_INSTALLER_SQL_UPGRADE_DIR_.$version.'.sql';
                    if (!file_exists($file)) {
                        $this->logError('Error while loading SQL upgrade file "%file%.sql".', 33, array('%file%' => $version));
                    }
                    if (!$sqlContent = file_get_contents($file)) {
                        $this->logError('Error while loading SQL upgrade file "%file%.sql".', 33, array('%file%' => $version));
                    }
                    $sqlContent .= "\n";
                    $sqlContent = str_replace(array(self::FILE_PREFIX, self::ENGINE_TYPE), array(_DB_PREFIX_, $mysqlEngine), $sqlContent);
                    $sqlContent = preg_split("/;\s*[\r\n]+/", $sqlContent);

                    $sqlContentVersion[$version] = $sqlContent;
                }
            }

            return $sqlContentVersion;
        }

        private function upgradeDoctrineSchema()
        {
            $i = 0;
            do {
                $sf2Refresh = new \PrestaShopBundle\Service\Cache\Refresh();
                $sf2Refresh->addDoctrineSchemaUpdate();
                $output = $sf2Refresh->execute();
                $i++;
                // Doctrine could need several tries before being able to properly upgrade the schema...
            } while((0 !== $output['doctrine:schema:update']['exitCode']) && $i < 10);

            if (0 !== $output['doctrine:schema:update']['exitCode']) {
                $msgErrors = explode("\n", $output['doctrine:schema:update']['output']);
                $this->logError('Error upgrading doctrine schema', 43);
                foreach($msgErrors as $msgError) {
                    $this->logError('Doctrine SQL Error : '.$msgError, 43);
                }
            }
        }

        private function disableCustomModules()
        {
            $db = Db::getInstance();
            $modulesDirOnDisk = array();
            $modules = scandir(_PS_MODULE_DIR_);
            foreach ($modules as $name) {
                if (!in_array($name, array('.', '..', 'index.php', '.htaccess')) && @is_dir(_PS_MODULE_DIR_ . $name . DIRECTORY_SEPARATOR) && @file_exists(_PS_MODULE_DIR_ . $name . DIRECTORY_SEPARATOR . $name . '.php')) {
                    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $name)) {
                        die(Tools::displayError() . ' (Module ' . $name . ')');
                    }
                    $modulesDirOnDisk[] = $name;
                }
            }

            $module_list_xml = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'xml' . DIRECTORY_SEPARATOR . 'modules_list.xml';

            if (!file_exists($module_list_xml)) {
                $module_list_xml = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'modules_list.xml';
                if (!file_exists($module_list_xml)) {
                    return false;
                }
            }

            $nativeModules = @simplexml_load_file($module_list_xml);
            if ($nativeModules) {
                $nativeModules = $nativeModules->modules;
            }
            $arrNativeModules = array();
            if (is_array($nativeModules)) {
                foreach ($nativeModules as $nativeModulesType) {
                    if (in_array($nativeModulesType['type'], array('native', 'partner'))) {
                        $arrNativeModules[] = '""';
                        foreach ($nativeModulesType->module as $module) {
                            $arrNativeModules[] = '"' . pSQL($module['name']) . '"';
                        }
                    }
                }
            }
            $arrNonNative = array();
            if ($arrNativeModules) {
                $arrNonNative = $db->executeS('
    		SELECT *
    		FROM `' . _DB_PREFIX_ . 'module` m
    		WHERE name NOT IN (' . implode(',', $arrNativeModules) . ') ');
            }

            $uninstallMe = array("undefined-modules");
            if (is_array($arrNonNative)) {
                foreach ($arrNonNative as $k => $aModule) {
                    $uninstallMe[(int)$aModule['id_module']] = $aModule['name'];
                }
            }

            if (!is_array($uninstallMe)) {
                $uninstallMe = array($uninstallMe);
            }

            foreach ($uninstallMe as $k => $v) {
                $uninstallMe[$k] = '"' . pSQL($v) . '"';
            }

            $return = Db::getInstance()->execute('
				UPDATE `' . _DB_PREFIX_ . 'module` SET `active` = 0 WHERE `name` IN (' . implode(',', $uninstallMe) . ')');

            if (count(Db::getInstance()->executeS('SHOW TABLES LIKE \'' . _DB_PREFIX_ . 'module_shop\'')) > 0) {
                foreach ($uninstallMe as $k => $uninstall) {
                    $return &= Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'module_shop` WHERE `id_module` = ' . (int)$k);
                }
            }

            $exist = $db->getValue('SELECT `id_configuration` FROM `'._DB_PREFIX_.'configuration` WHERE `name` LIKE \'PS_DISABLE_OVERRIDES\'');
            if ($exist) {
                $db->execute('UPDATE `'._DB_PREFIX_.'configuration` SET value = 1 WHERE `name` LIKE \'PS_DISABLE_OVERRIDES\'');
            } else {
                $db->execute('INSERT INTO `'._DB_PREFIX_.'configuration` (name, value, date_add, date_upd) VALUES ("PS_DISABLE_OVERRIDES", 1, NOW(), NOW())');
            }

            if (class_exists('\PrestaShopAutoload') && method_exists('\PrestaShopAutoload', 'generateIndex')) {
                \PrestaShopAutoload::getInstance()->_include_override_path = false;
                \PrestaShopAutoload::getInstance()->generateIndex();
            }

            return $return;
        }

        private function disableIncompatibleModules()
        {
            $disableModules = function () {
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
                    if (in_array($moduleName, self::$incompatibleModules)) {
                        $this->logInfo("Uninstalling module $moduleName, not supported in this prestashop version.");
                        $module->onUninstall();
                    } else {
                        $moduleInfo = $moduleManagerRepository->getModule($moduleName, true);
                        /** @var \Symfony\Component\HttpFoundation\ParameterBag $attributes */
                        $attributes = $module->attributes;
                        if ($attributes->get('compatibility')) {
                            $maxVersion = $attributes->get('compatibility')->to;
                            if (version_compare($maxVersion, _PS_INSTALL_VERSION_) == -1 && Module::isEnabled($moduleName)) {
                                $this->logInfo("Disabling module $moduleName. Max supported version : ".$maxVersion);
                                Module::disableAllByName($moduleName);
                            }
                        }
                    }
                }
            };
            $disableModules();
        }

        public function upgradeDb($sqlContentVersion)
        {
            $db = $this->db;
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
                                if (!file_exists(_PS_INSTALLER_PHP_UPGRADE_DIR_.strtolower($func_name).'.php')) {
                                    $this->logWarning('[ERROR] '.$version.' PHP - missing file '.$query, 41, array(), true);
                                } else {
                                    require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_ . Tools::strtolower($func_name) . '.php');
                                    $phpRes = call_user_func_array($func_name, $parameters);
                                }
                            } else {
                                /* Or an object method, not supported */
                                $this->logWarning('[ERROR] '.$version.' PHP - Object Method call is forbidden ('.$php[0].'::'.str_replace($pattern[0], '', $php[1]).')', 42, array(), true);
                            }
                            if ((is_array($phpRes) and !empty($phpRes['error'])) || $phpRes === false) {
                                $this->logWarning('[ERROR] PHP '.$version.' '.$query."\n".'
								'.(empty($phpRes['error']) ? '' : $phpRes['error']."\n").'
								'.(empty($phpRes['msg']) ? '' : ' - '.$phpRes['msg']), $version, array(), true);
                            } else {
                                $this->logInfo('[OK] PHP '.$version.' : '.$query, $version, array(), true);
                            }
                        } else {
                            if (!$db->execute($query)) {
                                $error = $db->getMsgError();
                                $error_number = $db->getNumberError();

                                $duplicates = array('1050', '1054', '1060', '1061', '1062', '1091');
                                if (!in_array($error_number, $duplicates)) {
                                    $this->logWarning('SQL '.$version.'
								'.$error_number.' in '.$query.': '.$error, $version, array(), true);
                                } else {
                                    $this->logInfo('SQL '.$version.'
								'.$error_number.' in '.$query.': '.$error, $version, array(), true);
                                }
                            } else {
                                $this->logInfo('[OK] SQL '.$version.' : '.$query, $version, array(), true);
                            }
                        }
                    }
                }
            }
            // reload config after DB upgrade
            Configuration::loadConfiguration();
        }

        private function enableNativeModules()
        {
            $enableNativeModules = function () {
                $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
                $moduleManagerRepository = $moduleManagerBuilder->buildRepository();
                $moduleManagerRepository->clearCache();

                $catalog = $moduleManagerBuilder::$adminModuleDataProvider->getCatalogModules();
                foreach ($catalog as $moduleName => $module) {
                    if ($module->categoryName == 'Natif') {
                        if (!$moduleManagerBuilder->build()->isInstalled($moduleName)) {
                            $this->logInfo("Installing native module ".$moduleName);
                            $module = $moduleManagerRepository->getModule($moduleName);
                            $module->onInstall();
                        } else {
                            $this->logInfo("Native module ".$moduleName." already installed");
                        }
                    }
                }
            };

            $enableNativeModules();
        }

        private function cleanCache()
        {
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
                                \Tools14::deleteDirectory($dir . $file . DIRECTORY_SEPARATOR, true);
                            }
                            $this->logInfo('[CLEANING CACHE] File %file% removed', null, array('%file%' => $file));
                        }
                    }
                } else {
                    $this->logWarning('[SKIP] directory "%directory%" does not exist and cannot be emptied.', null, array('%directory%' => str_replace($tools_dir, '', $dir)));
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
        }

        private function cleanDefaultThemeCache()
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

        private function updateDbImagesLegacy()
        {
            $db = $this->db;
            $db->execute('UPDATE `'._DB_PREFIX_.'configuration` SET `name` = \'PS_LEGACY_IMAGES\' WHERE name LIKE \'0\' AND `value` = 1');
            $db->execute('UPDATE `'._DB_PREFIX_.'configuration` SET `value` = 0 WHERE `name` LIKE \'PS_LEGACY_IMAGES\'');
            if ($db->getValue('SELECT COUNT(id_product_download) FROM `'._DB_PREFIX_.'product_download` WHERE `active` = 1') > 0) {
                $db->execute('UPDATE `'._DB_PREFIX_.'configuration` SET `value` = 1 WHERE `name` LIKE \'PS_VIRTUAL_PROD_FEATURE_ACTIVE\'');
            }
        }

        private function cleanupOldDirectories()
        {
            if (version_compare(_PS_VERSION_, '1.5.0.0', '<=')) {
                $dir = _PS_ROOT_DIR_ . '/controllers/';
                if (file_exists($dir)) {
                    foreach (scandir($dir) as $file) {
                        if (!is_dir($file) && $file[0] != '.' && $file != 'index.php' && $file != '.htaccess') {
                            if (file_exists($dir . basename(str_replace('.php', '', $file) . '.php'))) {
                                unlink($dir . basename($file));
                            }
                        }
                    }
                }

                $dir = _PS_ROOT_DIR_ . '/classes/';
                foreach (self::$classes14 as $class) {
                    if (file_exists($dir . basename($class) . '.php')) {
                        unlink($dir . basename($class) . '.php');
                    }
                }

                $dir = _PS_ADMIN_DIR_ . '/tabs/';
                if (file_exists($dir)) {
                    foreach (scandir($dir) as $file) {
                        if (!is_dir($file) && $file[0] != '.' && $file != 'index.php' && $file != '.htaccess') {
                            if (file_exists($dir . basename(str_replace('.php', '', $file) . '.php'))) {
                                unlink($dir . basename($file));
                            }
                        }
                    }
                }
            }

            if ($this->adminDir) {
                $path = $this->adminDir . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR
                    . 'template' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'modules'
                    . DIRECTORY_SEPARATOR . 'header.tpl';
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }

        private function updateLangs()
        {
            $langs = $this->db->executeS('SELECT * FROM `'._DB_PREFIX_.'lang` WHERE `active` = 1');

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
                            $this->logError('Error updating translations', 44);
                        }
                    }
                }
            }
        }

        private function updateHtaccess()
        {
            if (!class_exists('\Tools2', false) && class_exists('\ToolsCore')) {
                eval('class Tools2 extends \ToolsCore{}');
            }

            if (class_exists('\Tools2') && method_exists('\Tools2', 'generateHtaccess')) {
                $url_rewrite = (bool)$this->db->getvalue('SELECT `value` FROM `'._DB_PREFIX_.'configuration` WHERE name=\'PS_REWRITING_SETTINGS\'');

                \Tools2::generateHtaccess(null, $url_rewrite);
            }
        }

        private function updateTheme()
        {
            $themeManager = $this->getThemeManager($this->idEmployee);
            $themeName = ($this->changeToDefaultTheme ? 'classic' : _THEME_NAME_);

            $isThemeEnabled = $themeManager->enable($themeName);
            if (!$isThemeEnabled) {
                $themeErrors = $themeManager->getErrors($themeName);
                $this->logError($themeErrors, 45);
            } else {
                Tools::clearCache();
            }
        }

        public function run()
        {
            $this->defineConst();
            $this->initContext();
            $this->checkVersion();
            $sqlContentVersion = $this->getSQLFiles();
            $this->upgradeDoctrineSchema();
            if (!$this->hasFailure()) {
                if ($this->disableCustomModules) {
                    $this->disableCustomModules();
                }
                $this->disableIncompatibleModules();
                $this->upgradeDb($sqlContentVersion);
                $this->enableNativeModules();
                $this->cleanCache();
                $this->updateDbImagesLegacy();
                if ($this->updateDefaultTheme) {
                    $this->cleanDefaultThemeCache();
                }
                $this->cleanupOldDirectories();
                $this->updateLangs();
                $this->updateHtaccess();

                if ($this->idEmployee) {
                    $this->updateTheme();
                }
            }
        }

        public function getTranslator()
        {
            return $this->translator;
        }

        public function logInfo($quickInfo, $id = null,
                                $transVariables = array(), $dbInfo = false)
        {
            $info = $this->getTranslator()->trans($quickInfo, $transVariables,
                'Install.Upgrade.Error');
            if ($this->inAutoUpgrade) {
                if ($dbInfo) {
                    $this->nextQuickInfo[] = '<div class="upgradeDbOk">' . $info . '</div>';
                } else {
                    $this->nextQuickInfo[] = $info;
                }
                $this->infoList[] = $info;
            } else {
                if (!empty($quickInfo)) {
                    $this->logger->logInfo($info);
                }
                if ($id !== null) {
                    if (!is_numeric($id)) {
                        $customInfo = '<action result="info" id="' . $id . '"><![CDATA[' . htmlentities($info) . "]]></action>\n";
                    } else {
                        $customInfo = '<action result="info" id="' . $id . '" />' . "\n";
                    }
                    $this->infoList[] = $customInfo;
                }
            }
        }

        public function logWarning($quickInfo, $id,
                                   $transVariables = array(), $dbInfo = false)
        {
            $info = $this->getTranslator()->trans($quickInfo, $transVariables,
                'Install.Upgrade.Error');
            if ($this->inAutoUpgrade) {
                if ($dbInfo) {
                    $this->nextQuickInfo[] = '<div class="upgradeDbError">' . $info . '</div>';
                } else {
                    $this->nextQuickInfo[] = $info;
                }
                $this->nextErrors[] = $info;
                $this->warningList[] = $info;
                if (empty($this->failureList)) {
                    $this->nextDesc = $this->getTranslator()->trans('Warning detected during upgrade.', array(), 'Install');
                }
            } else {
                if (!empty($quickInfo)) {
                    $this->logger->logWarning($info);
                }
                if ($id !== null) {
                    if (!is_numeric($id)) {
                        $customWarning = '<action result="warning" id="' . $id . '"><![CDATA[' . htmlentities($info) . "]]></action>\n";
                    } else {
                        $customWarning = '<action result="warning" id="' . $id . '" />' . "\n";
                    }
                    $this->warningList[] = $customWarning;
                }
            }
        }

        public function logError($quickInfo, $id,
                                 $transVariables = array(), $dbInfo = false)
        {
            $info = $this->getTranslator()->trans($quickInfo, $transVariables,
                'Install.Upgrade.Error');
            if ($this->inAutoUpgrade) {
                if ($dbInfo) {
                    $this->nextQuickInfo[] = '<div class="upgradeDbError">' . $info . '</div>';
                } else {
                    $this->nextQuickInfo[] = $info;
                }
                $this->nextErrors[] = $info;
                $this->failureList[] = $info;
                $this->nextDesc = $this->getTranslator()->trans('Error detected during upgrade.', array(), 'Install');
                $this->next = 'error';
            } else {
                if (!empty($quickInfo)) {
                    $this->logger->logError($info);
                }
                if ($id !== null) {
                    if (!is_numeric($id)) {
                        $customError = '<action result="error" id="' . $id . '"><![CDATA[' . htmlentities($info) . "]]></action>\n";
                    } else {
                        $customError = '<action result="error" id="' . $id . '" />' . "\n";
                    }
                    $this->failureList[] = $customError;
                }
            }
        }

        public function getInAutoUpgrade()
        {
            return $this->inAutoUpgrade;
        }

        public function setInAutoUpgrade($value)
        {
            $this->inAutoUpgrade = $value;
        }

        public function getNext()
        {
            return $this->next;
        }

        public function getNextDesc()
        {
            return $this->nextDesc;
        }

        public function getInfoList()
        {
            return $this->infoList;
        }

        public function getWarningList()
        {
            return $this->warningList;
        }

        public function getFailureList()
        {
            return $this->failureList;
        }

        public function getNextQuickInfo()
        {
            return $this->nextQuickInfo;
        }

        public function getNextErrors()
        {
            return $this->nextErrors;
        }

        public function hasInfo()
        {
            return !empty($this->infoList);
        }

        public function hasWarning()
        {
            return !empty($this->warningList);
        }

        public function hasFailure()
        {
            return !empty($this->failureList);
        }

        const SETTINGS_FILE = 'config/settings.inc.php';

        public static function migrateSettingsFile(Event $event = null)
        {
            if ($event !== null) {
                $event->getIO()->write('Migrating old setting file...');
            }

            $root_dir = realpath(__DIR__ . '/../../../');

            $phpParametersFilepath = $root_dir . '/app/config/parameters.php';
            $addNewCookieKey = false;
            if (file_exists($phpParametersFilepath)) {
                $default_parameters = require $phpParametersFilepath;
                if (!array_key_exists('new_cookie_key', $default_parameters['parameters'])) {
                    $addNewCookieKey = true;
                } else {
                    if ($event !== null) {
                        $event->getIO()->write('parameters file already exists!');
                        $event->getIO()->write('Finished...');
                    }
                    return false;
                }
            }

            if (!file_exists($phpParametersFilepath) && !file_exists($root_dir.'/app/config/parameters.yml')
                && !file_exists($root_dir.'/'.self::SETTINGS_FILE)) {
                if ($event !== null) {
                    $event->getIO()->write('No file to migrate!');
                    $event->getIO()->write('Finished...');
                }
                return false;
            }

            $filesystem = new Filesystem();
            $exportPhpConfigFile = function ($config, $destination) use ($filesystem) {
                try {
                    $filesystem->dumpFile($destination, '<?php return ' . var_export($config, true) . ';' . "\n");
                } catch (IOException $e) {
                    return false;
                }

                return true;
            };

            $fileMigrated = false;
            if (!$addNewCookieKey) {
                $default_parameters = Yaml::parse(file_get_contents($root_dir . '/app/config/parameters.yml.dist'));
            }
            $default_parameters['parameters']['new_cookie_key'] = PhpEncryption::createNewRandomKey();

            if ($addNewCookieKey) {
                $exportPhpConfigFile($default_parameters, $phpParametersFilepath);
                if ($event !== null) {
                    $event->getIO()->write("parameters file already exists!");
                    $event->getIO()->write("add new parameter 'new_cookie_key'");
                    $event->getIO()->write("Finished...");
                }
                return false;
            }

            if (file_exists($root_dir . '/' . self::SETTINGS_FILE)) {
                $tmp_settings = file_get_contents($root_dir . '/' . self::SETTINGS_FILE);
            } else {
                $tmp_settings = null;
            }

            if (!file_exists($root_dir . '/app/config/parameters.yml') && $tmp_settings && strpos($tmp_settings, '_DB_SERVER_') !== false) {
                $tmp_settings = preg_replace('/(\'|")\_/', '$1_LEGACY_', $tmp_settings);
                $tmp_settings_file = str_replace('/settings', '/tmp_settings', $root_dir . '/' . self::SETTINGS_FILE);
                file_put_contents($tmp_settings_file, $tmp_settings);
                include $tmp_settings_file;
                @unlink($tmp_settings_file);
                $factory = new RandomLib\Factory();
                $generator = $factory->getLowStrengthGenerator();
                $secret = $generator->generateString(56);

                if (!defined('_LEGACY_NEW_COOKIE_KEY_')) {
                    define('_LEGACY_NEW_COOKIE_KEY_', $default_parameters['parameters']['new_cookie_key']);
                }

                $db_server_port = explode(':', _LEGACY_DB_SERVER_);
                if (count($db_server_port) == 1) {
                    $db_server = $db_server_port[0];
                    $db_port = 3306;
                } else {
                    $db_server = $db_server_port[0];
                    $db_port = $db_server_port[1];
                }

                $parameters = array(
                    'parameters' => array(
                            'database_host' => $db_server,
                            'database_port' => $db_port,
                            'database_user' => _LEGACY_DB_USER_,
                            'database_password' => _LEGACY_DB_PASSWD_,
                            'database_name' => _LEGACY_DB_NAME_,
                            'database_prefix' => _LEGACY_DB_PREFIX_,
                            'database_engine' => _LEGACY_MYSQL_ENGINE_,
                            'cookie_key' => _LEGACY_COOKIE_KEY_,
                            'cookie_iv' => _LEGACY_COOKIE_IV_,
                            'new_cookie_key' => _LEGACY_NEW_COOKIE_KEY_,
                            'ps_caching' => _LEGACY_PS_CACHING_SYSTEM_,
                            'ps_cache_enable' => _LEGACY_PS_CACHE_ENABLED_,
                            'ps_creation_date' => _LEGACY_PS_CREATION_DATE_,
                            'secret' => $secret,
                            'mailer_transport' => 'smtp',
                            'mailer_host' => '127.0.0.1',
                            'mailer_user' => '',
                            'mailer_password' => '',
                        ) + $default_parameters['parameters'],
                );
            } elseif (file_exists($root_dir . '/app/config/parameters.yml')) {
                $parameters = Yaml::parse(file_get_contents($root_dir . '/app/config/parameters.yml'));
                if (empty($parameters['parameters'])) {
                    $parameters['parameters'] = array();
                }
                // add potentially missing default entries
                $parameters['parameters'] = $parameters['parameters'] + $default_parameters['parameters'];
            } else {
                $parameters = $default_parameters;
            }

            if (!empty($parameters) && $exportPhpConfigFile($parameters, $phpParametersFilepath)) {
                $fileMigrated = true;
                $settings_content = "<?php\n";
                $settings_content .= '//@deprecated 1.7';

                file_put_contents($root_dir . '/' . self::SETTINGS_FILE, $settings_content);
                file_put_contents($root_dir . '/app/config/parameters.yml', 'parameters:');
            }

            if ($event !== null) {
                if (!$fileMigrated) {
                    $event->getIO()->write('No old config file present!');
                }
                $event->getIO()->write('Finished...');
            }
            return true;
        }
    }
}
