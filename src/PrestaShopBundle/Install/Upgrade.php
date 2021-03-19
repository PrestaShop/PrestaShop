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

namespace {
    $root_dir = realpath(__DIR__ . '/../../..');

    require_once $root_dir . '/vendor/paragonie/random_compat/lib/random.php';

    if (!class_exists('PhpEncryptionEngine')) {
        require_once $root_dir . '/classes/PhpEncryptionEngine.php';
        class PhpEncryptionEngine extends \PhpEncryptionEngineCore
        {
        }
    }

    if (!class_exists('PhpEncryptionLegacyEngine')) {
        require_once $root_dir . '/classes/PhpEncryptionLegacyEngine.php';
        class PhpEncryptionLegacyEngine extends \PhpEncryptionLegacyEngineCore
        {
        }
    }

    if (!class_exists('PhpEncryption')) {
        require_once $root_dir . '/classes/PhpEncryption.php';
        class PhpEncryption extends \PhpEncryptionCore
        {
        }
    }
}

namespace PrestaShopBundle\Install {
    use AppKernel;
    use Cache;
    use Cart;
    use Composer\Script\Event;
    use Configuration;
    use Context;
    use Country;
    use Db;
    use Employee;
    use FileLogger;
    use Language;
    use Module;
    use PhpEncryption;
    use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
    use PrestaShop\PrestaShop\Core\Addon\AddonListFilter;
    use PrestaShop\PrestaShop\Core\Addon\AddonListFilterOrigin;
    use PrestaShop\PrestaShop\Core\Addon\AddonListFilterStatus;
    use PrestaShop\PrestaShop\Core\Addon\AddonListFilterType;
    use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
    use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManagerBuilder;
    use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
    use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Command\GenerateThemeMailTemplatesCommand;
    use PrestaShop\PrestaShop\Core\Exception\CoreException;
    use PrestaShopBundle\Service\Database\Upgrade as UpgradeDatabase;
    use RandomLib;
    use Shop;
    use Symfony\Component\Filesystem\Exception\IOException;
    use Symfony\Component\Filesystem\Filesystem;
    use Symfony\Component\Yaml\Yaml;
    use Tools;
    use Validate;

    class Upgrade
    {
        /** @var \FileLogger */
        private $logger;
        private $infoList = [];
        private $warningList = [];
        private $failureList = [];
        private $nextQuickInfo = [];
        private $nextErrors = [];
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

        public const FILE_PREFIX = 'PREFIX_';
        public const ENGINE_TYPE = 'ENGINE_TYPE';
        public const DB_NAME = 'DB_NAME';

        private static $classes14 = ['Cache', 'CacheFS', 'CarrierModule', 'Db', 'FrontController', 'Helper', 'ImportModule',
            'MCached', 'Module', 'ModuleGraph', 'ModuleGraphEngine', 'ModuleGrid', 'ModuleGridEngine',
            'MySQL', 'Order', 'OrderDetail', 'OrderDiscount', 'OrderHistory', 'OrderMessage', 'OrderReturn',
            'OrderReturnState', 'OrderSlip', 'OrderState', 'PDF', 'RangePrice', 'RangeWeight', 'StockMvt',
            'StockMvtReason', 'SubDomain', 'Shop', 'Tax', 'TaxRule', 'TaxRulesGroup', 'WebserviceKey', 'WebserviceRequest', '', ];

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
            'productpaymentlogos',
            'sendtoafriend',
            'themeconfigurator',
        ];

        public function __construct($cacheDir, $installDir)
        {
            $this->logger = new FileLogger();
            $this->logger->setFilename($cacheDir . @date('Ymd') . '_upgrade.log');
            $this->installDir = $installDir;
            $this->db = Db::getInstance();
        }

        public function setDisableCustomModules($value)
        {
            $this->disableCustomModules = (bool) $value;
        }

        public function setUpdateDefaultTheme($value)
        {
            $this->updateDefaultTheme = (bool) $value;
        }

        public function setAdminDir($value)
        {
            $this->adminDir = $value;
        }

        public function setIdEmployee($id)
        {
            $this->idEmployee = (int) $id;
        }

        public function setChangeToDefaultTheme($value)
        {
            $this->changeToDefaultTheme = (bool) $value;
        }

        private function defineConst()
        {
            // retrocompatibility (is present in some upgrade scripts)
            if (!defined('INSTALL_PATH')) {
                define('INSTALL_PATH', $this->installDir);
            }
            require_once INSTALL_PATH . 'install_version.php';
            // needed for upgrade before 1.5
            if (!defined('__PS_BASE_URI__')) {
                define('__PS_BASE_URI__', str_replace('//', '/', '/' . trim(preg_replace('#/(install(-dev)?/upgrade)$#', '/', str_replace('\\', '/', dirname($_SERVER['REQUEST_URI']))), '/') . '/'));
            }
            if (!defined('_THEME_NAME_')) {
                define('_THEME_NAME_', 'default');
            }
            if (!defined('_PS_SMARTY_FAST_LOAD_')) {
                define('_PS_SMARTY_FAST_LOAD_', true);
            }

            // if _PS_ROOT_DIR_ is defined, use it instead of "guessing" the module dir.
            if (defined('_PS_ROOT_DIR_') && !defined('_PS_MODULE_DIR_')) {
                define('_PS_MODULE_DIR_', _PS_ROOT_DIR_ . '/modules/');
            } elseif (!defined('_PS_MODULE_DIR_')) {
                define('_PS_MODULE_DIR_', _PS_INSTALL_PATH_ . '/../modules/');
            }

            if (!defined('_PS_INSTALLER_PHP_UPGRADE_DIR_')) {
                define('_PS_INSTALLER_PHP_UPGRADE_DIR_', _PS_INSTALL_PATH_ . 'upgrade/php/');
            }

            if (!defined('_PS_INSTALLER_SQL_UPGRADE_DIR_')) {
                define('_PS_INSTALLER_SQL_UPGRADE_DIR_', _PS_INSTALL_PATH_ . 'upgrade/sql/');
            }

            if (!defined('_THEMES_DIR_')) {
                define('_THEMES_DIR_', __PS_BASE_URI__ . 'themes/');
            }
            if (!defined('_PS_IMG_')) {
                define('_PS_IMG_', __PS_BASE_URI__ . 'img/');
            }
            if (!defined('_PS_JS_DIR_')) {
                define('_PS_JS_DIR_', __PS_BASE_URI__ . 'js/');
            }
            if (!defined('_PS_CSS_DIR_')) {
                define('_PS_CSS_DIR_', __PS_BASE_URI__ . 'css/');
            }

            $this->oldVersion = Configuration::get('PS_VERSION_DB');
            if (empty($this->oldVersion)) {
                $this->oldVersion = Configuration::get('PS_INSTALL_VERSION');
            }
            // Since 1.4.4.0
            // Fix complete version number if there is not all 4 numbers
            // Eg. replace 1.4.3 by 1.4.3.0
            // Will result in file 1.4.3.0.sql will be skipped if oldversion is 1.4.3
            $arrayVersion = preg_split('#\.#', $this->oldVersion);
            $versionNumbers = count($arrayVersion);

            if ($versionNumbers != 4) {
                $arrayVersion = array_pad($arrayVersion, 4, '0');
            }

            $this->oldVersion = implode('.', $arrayVersion);
            // End of fix

            if (!defined('_PS_CACHE_ENABLED_')) {
                define('_PS_CACHE_ENABLED_', '0');
            }
            if (!defined('_MYSQL_ENGINE_')) {
                define('_MYSQL_ENGINE_', 'MyISAM');
            }

            if (!defined('_PS_TOOL_DIR_')) {
                define('_PS_TOOL_DIR_', _PS_ROOT_DIR_ . '/tools/');
            }
            if (!defined('_PS_TRANSLATIONS_DIR_')) {
                define('_PS_TRANSLATIONS_DIR_', _PS_ROOT_DIR_ . '/translations/');
            }
            if (!defined('_PS_MODULE_DIR_')) {
                define('_PS_MODULE_DIR_', _PS_ROOT_DIR_ . '/modules/');
            }
            if (!defined('_PS_MAILS_DIR_')) {
                define('_PS_MAILS_DIR_', _PS_ROOT_DIR_ . '/mails/');
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
                $idLang = (int) $this->getConfValue('PS_LANG_DEFAULT');
                Context::getContext()->language = new Language($idLang ? $idLang : null);
            }
            if (!isset(Context::getContext()->country) || !Validate::isLoadedObject(Context::getContext()->country)) {
                if ($id_country = (int) $this->getConfValue('PS_COUNTRY_DEFAULT')) {
                    Context::getContext()->country = new Country((int) $id_country);
                }
            }

            Context::getContext()->cart = new Cart();
            Context::getContext()->employee = new Employee(1);

            require_once _PS_ROOT_DIR_ . '/config/smarty.config.inc.php';

            Context::getContext()->smarty = $smarty;
            Language::loadLanguages();

            $this->translator = Context::getContext()->getTranslator();
        }

        private function getConfValue($name)
        {
            $sql = 'SELECT IF(cl.`id_lang` IS NULL, c.`value`, cl.`value`) AS value
			FROM `' . _DB_PREFIX_ . 'configuration` c
			LEFT JOIN `' . _DB_PREFIX_ . 'configuration_lang` cl ON (c.`id_configuration` = cl.`id_configuration`)
			WHERE c.`name`=\'' . pSQL($name) . '\'';

            $id_shop = Shop::getContextShopID(true);
            $id_shop_group = Shop::getContextShopGroupID(true);
            if ($id_shop) {
                $sql .= ' AND c.`id_shop` = ' . (int) $id_shop;
            }
            if ($id_shop_group) {
                $sql .= ' AND c.`id_shop_group` = ' . (int) $id_shop_group;
            }

            return $this->db->getValue($sql);
        }

        private function getThemeManager($idEmployee)
        {
            $context = Context::getContext();
            $context->employee = new Employee((int) $idEmployee);

            return (new ThemeManagerBuilder($context, Db::getInstance()))->build();
        }

        private function checkVersion()
        {
            $versionCompare = version_compare(_PS_INSTALL_VERSION_, $this->oldVersion);
            if ($versionCompare == '-1') {
                $this->logError('Current version: %current%. Version to install: %future%.', 27, ['%current%' => $this->oldVersion, '%future%' => _PS_INSTALL_VERSION_]);
            } elseif ($versionCompare == 0) {
                $this->logError('You already have the %future% version.', 28, ['%future%' => _PS_INSTALL_VERSION_]);
            }

            if (strpos(_PS_INSTALL_VERSION_, '.') === false) {
                $this->logError(
                    '%install_version% is not a valid version number.',
                    40,
                    ['%install_version%' => _PS_INSTALL_VERSION_]
                );
            }
        }

        private function getSQLFiles()
        {
            //custom sql file creation
            $neededUpgradeFiles = [];
            if (!$this->hasFailure()) {
                $upgradeFiles = [];
                if (!file_exists(_PS_INSTALLER_SQL_UPGRADE_DIR_)) {
                    $this->logError('Unable to find upgrade directory in the installation path.', 31);
                }

                if ($handle = opendir(_PS_INSTALLER_SQL_UPGRADE_DIR_)) {
                    while (false !== ($file = readdir($handle))) {
                        if (!in_array($file, ['.', '..', 'index.php'])) {
                            $upgradeFiles[] = str_replace('.sql', '', $file);
                        }
                    }
                    closedir($handle);
                }
                if (empty($upgradeFiles)) {
                    $this->logError('Cannot find the SQL upgrade files. Please verify that the %folder% folder is not empty)', 31, ['%folder%' => _PS_INSTALLER_SQL_UPGRADE_DIR_]);
                }
                natcasesort($upgradeFiles);

                $neededUpgradeFiles = [];
                foreach ($upgradeFiles as $version) {
                    if (version_compare($version, $this->oldVersion) == 1 && version_compare(_PS_INSTALL_VERSION_, $version) != -1) {
                        $neededUpgradeFiles[] = $version;
                    }
                }
            }
            if (!$this->hasFailure() && empty($neededUpgradeFiles)) {
                $this->logError('No upgrade is possible.', 32);
            }

            $sqlContentVersion = [];
            $mysqlEngine = (defined('_MYSQL_ENGINE_') ? _MYSQL_ENGINE_ : 'MyISAM');
            if (!$this->hasFailure()) {
                foreach ($neededUpgradeFiles as $version) {
                    $file = _PS_INSTALLER_SQL_UPGRADE_DIR_ . $version . '.sql';
                    if (!file_exists($file)) {
                        $this->logError('Error while loading SQL upgrade file "%file%.sql".', 33, ['%file%' => $version]);
                    }
                    if (!$sqlContent = file_get_contents($file)) {
                        $this->logError('Error while loading SQL upgrade file "%file%.sql".', 33, ['%file%' => $version]);
                    }
                    $sqlContent .= "\n";
                    $sqlContent = str_replace([self::FILE_PREFIX, self::ENGINE_TYPE, self::DB_NAME], [_DB_PREFIX_, $mysqlEngine, _DB_NAME_], $sqlContent);
                    $sqlContent = preg_split("/;\s*[\r\n]+/", $sqlContent);

                    $sqlContentVersion[$version] = $sqlContent;
                }
            }

            return $sqlContentVersion;
        }

        private function upgradeDoctrineSchema()
        {
            $schemaUpgrade = new UpgradeDatabase();
            $schemaUpgrade->addDoctrineSchemaUpdate();
            $output = $schemaUpgrade->execute();
            if (0 !== $output['prestashop:schema:update-without-foreign']['exitCode']) {
                $msgErrors = explode("\n", $output['prestashop:schema:update-without-foreign']['output']);
                $this->logError('Error upgrading doctrine schema', 43);
                foreach ($msgErrors as $msgError) {
                    $this->logError('Doctrine SQL Error : ' . $msgError, 43);
                }
            }
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
                                $parameters = [];
                            }
                            if (is_array($parameters)) {
                                foreach ($parameters as &$parameter) {
                                    $parameter = str_replace('\'', '', $parameter);
                                }
                                unset($parameter);
                            }

                            $phpRes = null;
                            /* Call a simple function */
                            if (strpos($phpString, '::') === false) {
                                $func_name = str_replace($pattern[0], '', $php[0]);
                                if (!file_exists(_PS_INSTALLER_PHP_UPGRADE_DIR_ . strtolower($func_name) . '.php')) {
                                    $this->logWarning('[ERROR] ' . $version . ' PHP - missing file ' . $query, 41, [], true);
                                } else {
                                    require_once _PS_INSTALLER_PHP_UPGRADE_DIR_ . Tools::strtolower($func_name) . '.php';
                                    $phpRes = call_user_func_array($func_name, $parameters);
                                }
                            } else {
                                /* Or an object method, not supported */
                                $this->logWarning('[ERROR] ' . $version . ' PHP - Object Method call is forbidden (' . $php[0] . '::' . str_replace($pattern[0], '', $php[1]) . ')', 42, [], true);
                            }
                            if ((is_array($phpRes) && !empty($phpRes['error'])) || $phpRes === false) {
                                $this->logWarning('[ERROR] PHP ' . $version . ' ' . $query . "\n" . '
								' . (empty($phpRes['error']) ? '' : $phpRes['error'] . "\n") . '
								' . (empty($phpRes['msg']) ? '' : ' - ' . $phpRes['msg']), $version, [], true);
                            } else {
                                $this->logInfo('[OK] PHP ' . $version . ' : ' . $query, $version, [], true);
                            }
                        } else {
                            if (!$db->execute($query)) {
                                $error = $db->getMsgError();
                                $error_number = $db->getNumberError();

                                $duplicates = ['1050', '1054', '1060', '1061', '1062', '1091'];
                                if (!in_array($error_number, $duplicates)) {
                                    $this->logWarning('SQL ' . $version . '
								' . $error_number . ' in ' . $query . ': ' . $error, $version, [], true);
                                } else {
                                    $this->logInfo('SQL ' . $version . '
								' . $error_number . ' in ' . $query . ': ' . $error, $version, [], true);
                                }
                            } else {
                                $this->logInfo('[OK] SQL ' . $version . ' : ' . $query, $version, [], true);
                            }
                        }
                    }
                }
            }
            // reload config after DB upgrade
            Configuration::loadConfiguration();
        }

        private function disableCustomModules()
        {
            $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
            $moduleRepository = $moduleManagerBuilder->buildRepository();
            $moduleRepository->clearCache();

            $filters = new AddonListFilter();
            $filters->setType(AddonListFilterType::MODULE)
                ->removeStatus(AddonListFilterStatus::UNINSTALLED);

            $installedProducts = $moduleRepository->getFilteredList($filters);
            /** @var \PrestaShop\PrestaShop\Adapter\Module\Module $installedProduct */
            foreach ($installedProducts as $installedProduct) {
                if (!(
                        $installedProduct->attributes->has('origin_filter_value')
                        && in_array(
                            $installedProduct->attributes->get('origin_filter_value'),
                            [
                                AddonListFilterOrigin::ADDONS_NATIVE,
                                AddonListFilterOrigin::ADDONS_NATIVE_ALL,
                            ]
                        )
                        && 'PrestaShop' === $installedProduct->attributes->get('author')
                    )
                    && 'autoupgrade' !== $installedProduct->attributes->get('name')) {
                    $moduleName = $installedProduct->attributes->get('name');
                    $this->logInfo('Disabling custom module ' . $moduleName);
                    Module::disableAllByName($moduleName);
                }
            }

            return true;
        }

        private function disableIncompatibleModules()
        {
            $fs = new Filesystem();

            $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
            $moduleManagerRepository = $moduleManagerBuilder->buildRepository();
            $moduleManagerRepository->clearCache();

            $filters = new AddonListFilter();
            $filters->setStatus(AddonListFilterStatus::ON_DISK | AddonListFilterStatus::INSTALLED);

            $list = $moduleManagerRepository->getFilteredList($filters, true);
            /** @var string $moduleName */
            /** @var \PrestaShop\PrestaShop\Adapter\Module\Module $module */
            foreach ($list as $moduleName => $module) {
                if (in_array($moduleName, self::$incompatibleModules)) {
                    $this->logInfo("Uninstalling module $moduleName, not supported in this PrestaShop version.");
                    $module->onUninstall();
                    $fs->remove(_PS_MODULE_DIR_ . $moduleName);
                } else {
                    $attributes = $module->attributes;
                    if ($attributes->get('compatibility')) {
                        $maxVersion = $attributes->get('compatibility')->to;
                        if (version_compare($maxVersion, _PS_INSTALL_VERSION_) == -1 && Module::isEnabled($moduleName)) {
                            $this->logInfo("Disabling module $moduleName. Max supported version : " . $maxVersion);
                            Module::disableAllByName($moduleName);
                        }
                    }
                }
            }

            return true;
        }

        private function enableNativeModules()
        {
            $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
            $moduleManagerRepository = $moduleManagerBuilder->buildRepository();
            $moduleManagerRepository->clearCache();

            $filters = new AddonListFilter();
            $filters->setOrigin(AddonListFilterOrigin::ADDONS_NATIVE | AddonListFilterOrigin::ADDONS_NATIVE_ALL);

            $list = $moduleManagerRepository->getFilteredList($filters, true);
            /** @var string $moduleName */
            /** @var \PrestaShop\PrestaShop\Adapter\Module\Module $module */
            foreach ($list as $moduleName => $module) {
                if ('PrestaShop' === $module->attributes->get('author')) {
                    if (!$moduleManagerBuilder->build()->isInstalled($moduleName)) {
                        $this->logInfo('Installing native module ' . $moduleName);
                        $module = $moduleManagerRepository->getModule($moduleName);
                        $module->onInstall();
                        $module->onEnable();
                    } else {
                        $this->logInfo('Native module ' . $moduleName . ' already installed');
                    }
                }
            }

            return true;
        }

        private function cleanCache()
        {
            // Settings updated, compile and cache directories must be emptied
            $install_dir = realpath(rtrim(_PS_INSTALL_PATH_, '\\/') . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            $tools_dir = $install_dir . 'tools' . DIRECTORY_SEPARATOR;
            $arrayToClean = [
                $tools_dir . 'smarty' . DIRECTORY_SEPARATOR . 'cache',
                $tools_dir . 'smarty' . DIRECTORY_SEPARATOR . 'compile',
                $tools_dir . 'smarty_v2' . DIRECTORY_SEPARATOR . 'cache',
                $tools_dir . 'smarty_v2' . DIRECTORY_SEPARATOR . 'compile',
                $install_dir . 'app' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR,
                $install_dir . 'cache' . DIRECTORY_SEPARATOR . 'smarty' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR,
                $install_dir . 'cache' . DIRECTORY_SEPARATOR . 'smarty' . DIRECTORY_SEPARATOR . 'compile' . DIRECTORY_SEPARATOR,
            ];

            foreach ($arrayToClean as $dir) {
                if (file_exists($dir)) {
                    foreach (scandir($dir, SCANDIR_SORT_NONE) as $file) {
                        if ($file[0] != '.' && $file != 'index.php' && $file != '.htaccess') {
                            if (is_file($dir . $file)) {
                                unlink($dir . $file);
                            } elseif (is_dir($dir . $file . DIRECTORY_SEPARATOR)) {
                                //\Tools14::deleteDirectory($dir . $file . DIRECTORY_SEPARATOR, true);
                            }
                            // To more log
                            //$this->logInfo('[CLEANING CACHE] File %file% removed', null, array('%file%' => $file));
                        }
                    }
                } else {
                    $this->logInfo('[SKIP] directory "%directory%" does not exist and cannot be emptied.', null, ['%directory%' => str_replace($tools_dir, '', $dir)]);
                }
            }

            if (file_exists(_PS_ROOT_DIR_ . '/var/cache/dev/class_index.php')) {
                unlink(_PS_ROOT_DIR_ . '/var/cache/dev/class_index.php');
            }
            if (file_exists(_PS_ROOT_DIR_ . '/var/cache/prod/class_index.php')) {
                unlink(_PS_ROOT_DIR_ . '/var/cache/prod/class_index.php');
            }

            // Clear XML files
            if (file_exists(_PS_ROOT_DIR_ . '/config/xml/blog-fr.xml')) {
                unlink(_PS_ROOT_DIR_ . '/config/xml/blog-fr.xml');
            }
            if (file_exists(_PS_ROOT_DIR_ . '/config/xml/default_country_modules_list.xml')) {
                unlink(_PS_ROOT_DIR_ . '/config/xml/default_country_modules_list.xml');
            }
            if (file_exists(_PS_ROOT_DIR_ . '/config/xml/modules_list.xml')) {
                unlink(_PS_ROOT_DIR_ . '/config/xml/modules_list.xml');
            }
            if (file_exists(_PS_ROOT_DIR_ . '/config/xml/modules_native_addons.xml')) {
                unlink(_PS_ROOT_DIR_ . '/config/xml/modules_native_addons.xml');
            }
            if (file_exists(_PS_ROOT_DIR_ . '/config/xml/must_have_modules_list.xml')) {
                unlink(_PS_ROOT_DIR_ . '/config/xml/must_have_modules_list.xml');
            }
            if (file_exists(_PS_ROOT_DIR_ . '/config/xml/tab_modules_list.xml')) {
                unlink(_PS_ROOT_DIR_ . '/config/xml/tab_modules_list.xml');
            }
            if (file_exists(_PS_ROOT_DIR_ . '/config/xml/trusted_modules_list.xml')) {
                unlink(_PS_ROOT_DIR_ . '/config/xml/trusted_modules_list.xml');
            }
            if (file_exists(_PS_ROOT_DIR_ . '/config/xml/untrusted_modules_list.xml')) {
                unlink(_PS_ROOT_DIR_ . '/config/xml/untrusted_modules_list.xml');
            }
        }

        private function cleanDefaultThemeCache()
        {
            $separator = addslashes(DIRECTORY_SEPARATOR);
            $file = _PS_ROOT_DIR_ . $separator . 'themes' . $separator . _THEME_NAME_ . $separator . 'cache' . $separator;
            if (file_exists($file)) {
                foreach (scandir($file, SCANDIR_SORT_NONE) as $cache) {
                    if ($cache[0] != '.' && $cache != 'index.php' && $cache != '.htaccess' && file_exists($file . $cache) && !is_dir($file . $cache)) {
                        if (file_exists($file . $cache)) {
                            unlink($file . $cache);
                        }
                    }
                }
            }
        }

        private function updateDbImagesLegacy()
        {
            $db = $this->db;
            $db->execute('UPDATE `' . _DB_PREFIX_ . 'configuration` SET `name` = \'PS_LEGACY_IMAGES\' WHERE name LIKE \'0\' AND `value` = 1');
            $db->execute('UPDATE `' . _DB_PREFIX_ . 'configuration` SET `value` = 0 WHERE `name` LIKE \'PS_LEGACY_IMAGES\'');
            if ($db->getValue('SELECT COUNT(id_product_download) FROM `' . _DB_PREFIX_ . 'product_download` WHERE `active` = 1') > 0) {
                $db->execute('UPDATE `' . _DB_PREFIX_ . 'configuration` SET `value` = 1 WHERE `name` LIKE \'PS_VIRTUAL_PROD_FEATURE_ACTIVE\'');
            }
        }

        private function cleanupOldDirectories()
        {
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
            $langs = $this->db->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'lang` WHERE `active` = 1');

            if (is_array($langs)) {
                foreach ($langs as $lang) {
                    $isoCode = $lang['iso_code'];

                    if (Validate::isLangIsoCode($isoCode)) {
                        $errorsLanguage = [];

                        Language::downloadLanguagePack($isoCode, AppKernel::VERSION, $errorsLanguage);

                        $lang_pack = Language::getLangDetails($isoCode);
                        Language::installSfLanguagePack($lang_pack['locale'], $errorsLanguage);
                        self::generateEmailsLanguagePack($lang_pack, $errorsLanguage);

                        if (empty($errorsLanguage)) {
                            Language::loadLanguages();
                        } else {
                            $this->logError('Error updating translations', 44);
                        }

                        Language::updateMultilangTable($isoCode);
                    }
                }
            }
        }

        /**
         * @param array $langPack
         * @param array $errors
         */
        private static function generateEmailsLanguagePack($langPack, &$errors = [])
        {
            $locale = $langPack['locale'];
            $sfContainer = SymfonyContainer::getInstance();
            if (null === $sfContainer) {
                $errors[] = Context::getContext()->getTranslator()->trans(
                    'Cannot generate emails because the Symfony container is unavailable.',
                    [],
                    'Admin.Notifications.Error'
                );

                return;
            }

            $mailTheme = Configuration::get('PS_MAIL_THEME');
            /** @var GenerateThemeMailTemplatesCommand $generateCommand */
            $generateCommand = new GenerateThemeMailTemplatesCommand(
                $mailTheme,
                $locale,
                false,
                '',
                ''
            );
            /** @var CommandBusInterface $commandBus */
            $commandBus = $sfContainer->get('prestashop.core.command_bus');
            try {
                $commandBus->handle($generateCommand);
            } catch (CoreException $e) {
                $errors[] = Context::getContext()->getTranslator()->trans(
                    'Cannot generate email templates: %s.',
                    [$e->getMessage()],
                    'Admin.Notifications.Error'
                );
            }
        }

        private function updateHtaccess()
        {
            if (!class_exists('\Tools2', false) && class_exists('\ToolsCore')) {
                eval('class Tools2 extends \ToolsCore{}');
            }

            /* @phpstan-ignore-next-line */
            if (class_exists('\Tools2') && method_exists('\Tools2', 'generateHtaccess')) {
                $url_rewrite = (bool) $this->db->getValue('SELECT `value` FROM `' . _DB_PREFIX_ . 'configuration` WHERE name=\'PS_REWRITING_SETTINGS\'');

                \Tools2::generateHtaccess(null, $url_rewrite);
            }
        }

        private function updateTheme()
        {
            $themeManager = $this->getThemeManager($this->idEmployee);
            $themeName = ($this->changeToDefaultTheme ? 'classic' : _THEME_NAME_);

            $isThemeEnabled = $themeManager->enable($themeName, true);
            if (!$isThemeEnabled) {
                $themeErrors = $themeManager->getErrors($themeName);
                $this->logError($themeErrors, 45);
            }
        }

        public function run()
        {
            Tools::clearAllCache();

            $this->defineConst();
            $this->initContext();
            $this->checkVersion();

            $sqlContentVersion = $this->getSQLFiles();

            if (!$this->hasFailure()) {
                $this->disableIncompatibleModules();

                if ($this->disableCustomModules) {
                    $this->disableCustomModules();
                }

                $this->upgradeDb($sqlContentVersion);
                $this->upgradeDoctrineSchema();

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

        public function doUpgradeDb()
        {
            Tools::clearAllCache();

            $this->defineConst();
            $this->initContext();
            $this->checkVersion();

            $sqlContentVersion = $this->getSQLFiles();

            if (!$this->hasFailure()) {
                $this->upgradeDb($sqlContentVersion);
                $this->upgradeDoctrineSchema();
            }

            $this->next = 'DisableModules';
            $this->nextDesc = $this->getTranslator()->trans('Database upgrade completed.', [], 'Install');
            $this->nextQuickInfo[] = $this->getTranslator()->trans('Database upgrade completed.', [], 'Install');
            $this->nextQuickInfo[] = $this->getTranslator()->trans('Disabling modules now...', [], 'Install');
        }

        public function doDisableModules()
        {
            $this->defineConst();
            $this->initContext();

            $this->disableIncompatibleModules();

            if ($this->disableCustomModules) {
                $this->disableCustomModules();
            }

            $this->next = 'EnableModules';
            $this->nextDesc = $this->getTranslator()->trans('Modules successfully disabled.', [], 'Install');
            $this->nextQuickInfo[] = $this->getTranslator()->trans('Modules successfully disabled.', [], 'Install');
            $this->nextQuickInfo[] = $this->getTranslator()->trans('Enabling modules now...', [], 'Install');
        }

        public function doEnableModules()
        {
            $this->defineConst();
            $this->initContext();

            $this->enableNativeModules();

            $this->next = 'UpdateImage';
            $this->nextDesc = $this->getTranslator()->trans('Modules successfully enabled.', [], 'Install');
            $this->nextQuickInfo[] = $this->getTranslator()->trans('Modules successfully enabled.', [], 'Install');
            $this->nextQuickInfo[] = $this->getTranslator()->trans('Upgrading images now...', [], 'Install');
        }

        public function doUpdateImage()
        {
            $this->defineConst();
            $this->initContext();

            $this->cleanCache();

            $this->updateDbImagesLegacy();
            if ($this->updateDefaultTheme) {
                $this->cleanDefaultThemeCache();
            }
            $this->cleanupOldDirectories();

            $this->next = 'UpdateLangHtaccess';
            $this->nextDesc = $this->getTranslator()->trans('Images successfully upgraded.', [], 'Install');
            $this->nextQuickInfo[] = $this->getTranslator()->trans('Images successfully upgraded.', [], 'Install');
            $this->nextQuickInfo[] = $this->getTranslator()->trans('Upgrading languages now...', [], 'Install');
        }

        public function doUpdateLangHtaccess()
        {
            $this->defineConst();
            $this->initContext();

            $this->updateLangs();
            $this->updateHtaccess();

            $this->next = 'UpdateTheme';
            $this->nextDesc = $this->getTranslator()->trans('Languages successfully upgraded.', [], 'Install');
            $this->nextQuickInfo[] = $this->getTranslator()->trans('Languages successfully upgraded.', [], 'Install');
            $this->nextQuickInfo[] = $this->getTranslator()->trans('Upgrading theme now...', [], 'Install');
        }

        public function doUpdateTheme()
        {
            $this->defineConst();
            $this->initContext();

            if ($this->idEmployee) {
                $this->updateTheme();
            }

            $this->next = 'UpgradeComplete';
            $this->nextDesc = $this->getTranslator()->trans('Theme successfully upgraded.', [], 'Install');
            $this->nextQuickInfo[] = $this->getTranslator()->trans('Theme successfully upgraded.', [], 'Install');
        }

        public function getTranslator()
        {
            return $this->translator;
        }

        public function logInfo($quickInfo, $id = null, $transVariables = [], $dbInfo = false)
        {
            $info = $this->getTranslator()->trans($quickInfo, $transVariables, 'Install');
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

        public function logWarning($quickInfo, $id, $transVariables = [], $dbInfo = false)
        {
            $info = $this->getTranslator()->trans($quickInfo, $transVariables, 'Install');
            if ($this->inAutoUpgrade) {
                if ($dbInfo) {
                    $this->nextQuickInfo[] = '<div class="upgradeDbError">' . $info . '</div>';
                } else {
                    $this->nextQuickInfo[] = $info;
                }
                $this->nextErrors[] = $info;
                $this->warningList[] = $info;
                if (empty($this->failureList)) {
                    $this->nextDesc = $this->getTranslator()->trans('Warning detected during upgrade.', [], 'Install');
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

        public function logError($quickInfo, $id, $transVariables = [], $dbInfo = false)
        {
            $info = $this->getTranslator()->trans($quickInfo, $transVariables, 'Install');
            if ($this->inAutoUpgrade) {
                if ($dbInfo) {
                    $this->nextQuickInfo[] = '<div class="upgradeDbError">' . $info . '</div>';
                } else {
                    $this->nextQuickInfo[] = $info;
                }
                $this->nextErrors[] = $info;
                $this->failureList[] = $info;
                $this->nextDesc = $this->getTranslator()->trans('Error detected during upgrade.', [], 'Install');
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

        public const SETTINGS_FILE = 'config/settings.inc.php';

        /* @phpstan-ignore-next-line */
        public static function migrateSettingsFile(Event $event = null)
        {
            if ($event !== null) {
                /* @phpstan-ignore-next-line */
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
                        /* @phpstan-ignore-next-line */
                        $event->getIO()->write('parameters file already exists!');
                        /* @phpstan-ignore-next-line */
                        $event->getIO()->write('Finished...');
                    }

                    return false;
                }
            }

            if (!file_exists($phpParametersFilepath) && !file_exists($root_dir . '/app/config/parameters.yml')
                && !file_exists($root_dir . '/' . self::SETTINGS_FILE)) {
                if ($event !== null) {
                    /* @phpstan-ignore-next-line */
                    $event->getIO()->write('No file to migrate!');
                    /* @phpstan-ignore-next-line */
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
                    /* @phpstan-ignore-next-line */
                    $event->getIO()->write('parameters file already exists!');
                    /* @phpstan-ignore-next-line */
                    $event->getIO()->write("add new parameter 'new_cookie_key'");
                    /* @phpstan-ignore-next-line */
                    $event->getIO()->write('Finished...');
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
                $secret = $generator->generateString(64);

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

                $parameters = [
                    'parameters' => [
                        'database_host' => $db_server,
                        'database_port' => $db_port,
                        'database_user' => _LEGACY_DB_USER_,
                        'database_password' => _LEGACY_DB_PASSWD_,
                        'database_name' => _LEGACY_DB_NAME_,
                        'database_prefix' => _LEGACY_DB_PREFIX_,
                        'database_engine' => defined(_LEGACY_MYSQL_ENGINE_) ? _LEGACY_MYSQL_ENGINE_ : 'InnoDB',
                        'cookie_key' => _LEGACY_COOKIE_KEY_,
                        'cookie_iv' => _LEGACY_COOKIE_IV_,
                        'new_cookie_key' => _LEGACY_NEW_COOKIE_KEY_,
                        'ps_caching' => defined(_LEGACY_PS_CACHING_SYSTEM_) ? _LEGACY_PS_CACHING_SYSTEM_ : 'CacheMemcache',
                        'ps_cache_enable' => defined(_LEGACY_PS_CACHE_ENABLED_) ? _LEGACY_PS_CACHE_ENABLED_ : false,
                        'ps_creation_date' => defined(_LEGACY_PS_CREATION_DATE_) ? _LEGACY_PS_CREATION_DATE_ : date('Y-m-d H:i:s'),
                        'secret' => $secret,
                        'mailer_transport' => 'smtp',
                        'mailer_host' => '127.0.0.1',
                        'mailer_user' => '',
                        'mailer_password' => '',
                    ] + $default_parameters['parameters'],
                ];
            } elseif (file_exists($root_dir . '/app/config/parameters.yml')) {
                $parameters = Yaml::parse(file_get_contents($root_dir . '/app/config/parameters.yml'));
                if (empty($parameters['parameters'])) {
                    $parameters['parameters'] = [];
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
                    /* @phpstan-ignore-next-line */
                    $event->getIO()->write('No old config file present!');
                }
                /* @phpstan-ignore-next-line */
                $event->getIO()->write('Finished...');
            }

            return true;
        }
    }
}
