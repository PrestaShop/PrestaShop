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

namespace PrestaShopBundle\Install;

use FileLogger as LegacyFileLogger;
use Language as LanguageLegacy;
use PhpEncryption;
use PrestaShop\PrestaShop\Adapter\Entity\Cache;
use PrestaShop\PrestaShop\Adapter\Entity\Cart;
use PrestaShop\PrestaShop\Adapter\Entity\Category;
use PrestaShop\PrestaShop\Adapter\Entity\Configuration;
use PrestaShop\PrestaShop\Adapter\Entity\Context;
use PrestaShop\PrestaShop\Adapter\Entity\Cookie;
use PrestaShop\PrestaShop\Adapter\Entity\Country;
use PrestaShop\PrestaShop\Adapter\Entity\Currency;
use PrestaShop\PrestaShop\Adapter\Entity\Db;
use PrestaShop\PrestaShop\Adapter\Entity\Employee;
use PrestaShop\PrestaShop\Adapter\Entity\Group;
use PrestaShop\PrestaShop\Adapter\Entity\ImageManager;
use PrestaShop\PrestaShop\Adapter\Entity\ImageType;
use PrestaShop\PrestaShop\Adapter\Entity\Language as EntityLanguage;
use PrestaShop\PrestaShop\Adapter\Entity\LocalizationPack;
use PrestaShop\PrestaShop\Adapter\Entity\Module as ModuleEntity;
use PrestaShop\PrestaShop\Adapter\Entity\PrestaShopCollection;
use PrestaShop\PrestaShop\Adapter\Entity\Search;
use PrestaShop\PrestaShop\Adapter\Entity\Shop;
use PrestaShop\PrestaShop\Adapter\Entity\ShopGroup;
use PrestaShop\PrestaShop\Adapter\Entity\ShopUrl;
use PrestaShop\PrestaShop\Adapter\Entity\Tools;
use PrestaShop\PrestaShop\Adapter\Entity\Validate;
use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManagerBuilder;
use PrestaShop\PrestaShop\Core\Module\ConfigReader as ModuleConfigReader;
use PrestaShop\PrestaShop\Core\Theme\ConfigReader as ThemeConfigReader;
use PrestaShop\PrestaShop\Core\Version;
use PrestaShopBundle\Cache\LocalizationWarmer;
use PrestaShopBundle\Service\Database\Upgrade as UpgradeDatabase;
use PrestaShopException;
use PrestashopInstallerException;
use PrestaShopLoggerInterface;
use PSRLoggerAdapter;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class Install extends AbstractInstall
{
    public const SETTINGS_FILE = 'config/settings.inc.php';
    public const BOOTSTRAP_FILE = 'config/bootstrap.php';

    public const DEFAULT_THEME = 'classic';

    /**
     * The path of the bootsrap file we want to use for the installation.
     *
     * @var string
     */
    protected $bootstrapFile = null;

    /**
     * @var array
     */
    public $xml_loader_ids = [];

    /**
     * The path of the settings file we want to use for the installation.
     *
     * @var string
     */
    protected $settingsFile = null;

    /**
     * @var bool
     */
    protected $isDebug = null;

    /**
     * @param string|null $settingsFile
     * @param string|null $bootstrapFile
     * @param PrestaShopLoggerInterface $logger
     */
    public function __construct($settingsFile = null, $bootstrapFile = null, $logger = null)
    {
        if ($bootstrapFile === null) {
            $bootstrapFile = static::BOOTSTRAP_FILE;
        }

        if ($settingsFile === null) {
            $settingsFile = static::SETTINGS_FILE;
        }

        $this->settingsFile = $settingsFile;
        $this->bootstrapFile = $bootstrapFile;
        $this->isDebug = _PS_MODE_DEV_;

        if (null === $logger) {
            $this->logger = new LegacyFileLogger();
            $this->logger->setFilename(
                _PS_ROOT_DIR_ . '/var/logs/' . _PS_ENV_ . '_' . @date('Ymd') . '_installation.log'
            );
        } else {
            $this->setLogger($logger);
        }

        parent::__construct();
    }

    public function setError($errors)
    {
        if (!is_array($errors)) {
            $errors = [$errors];
        }

        parent::setError($errors);

        foreach ($errors as $error) {
            $this->getLogger()->logError($error);
        }
    }

    /**
     * Generate the settings file.
     */
    public function generateSettingsFile(
        $database_host,
        $database_user,
        $database_password,
        $database_name,
        $database_prefix,
        $database_engine
    ) {
        // Check permissions for settings file
        if (
            file_exists(_PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . $this->settingsFile)
            && !is_writable(_PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . $this->settingsFile)
        ) {
            $this->setError($this->translator->trans('%file% file is not writable (check permissions)', ['%file%' => $this->settingsFile], 'Install'));

            return false;
        } elseif (
            !file_exists(_PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . $this->settingsFile)
            && !is_writable(_PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . dirname($this->settingsFile))
        ) {
            $this->setError(
                $this->translator->trans(
                '%folder% folder is not writable (check permissions)',
                ['%folder%' => dirname($this->settingsFile)],
                'Install'
            )
            );

            return false;
        }

        $secret = Tools::passwdGen(64);
        $cookie_key = defined('_COOKIE_KEY_') ? _COOKIE_KEY_ : Tools::passwdGen(64);
        $cookie_iv = defined('_COOKIE_IV_') ? _COOKIE_IV_ : Tools::passwdGen(32);
        $database_port = null;

        $splits = preg_split('#:#', $database_host);
        $nbSplits = count($splits);

        if ($nbSplits >= 2) {
            $database_port = array_pop($splits);
            $database_host = implode(':', $splits);
        }

        $key = PhpEncryption::createNewRandomKey();
        $privateKey = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        openssl_pkey_export($privateKey, $apiPrivateKey);
        $apiPublicKey = openssl_pkey_get_details($privateKey)['key'];

        $parameters = [
            'parameters' => [
                'database_host' => $database_host,
                'database_port' => $database_port,
                'database_user' => $database_user,
                'database_password' => $database_password,
                'database_name' => $database_name,
                'database_prefix' => $database_prefix,
                'database_engine' => $database_engine,
                'cookie_key' => $cookie_key,
                'cookie_iv' => $cookie_iv,
                'new_cookie_key' => $key,
                'api_public_key' => $apiPublicKey,
                'api_private_key' => $apiPrivateKey,
                'ps_creation_date' => date('Y-m-d'),
                'secret' => $secret,
                'locale' => $this->language->getLanguage()->getLocale(),
            ],
        ];

        array_walk($parameters['parameters'], function (&$param) {
            $param = str_replace('%', '%%', $param ?? '');
        });

        $parameters = array_replace_recursive(
            Yaml::parse(file_get_contents(_PS_ROOT_DIR_ . '/app/config/parameters.yml.dist')),
            $parameters
        );

        $settings_content = "<?php\n";
        $settings_content .= '//@deprecated 1.7';

        if (!file_put_contents(_PS_ROOT_DIR_ . '/' . $this->settingsFile, $settings_content)) {
            $this->setError($this->translator->trans('Cannot write settings file', [], 'Install'));

            return false;
        }

        if (!$this->processParameters($parameters)) {
            return false;
        }

        return true;
    }

    /**
     * Replace "parameters.yml" with "parameters.php" in "app/config".
     *
     * @param array $parameters
     *
     * @return bool|int
     */
    public function processParameters($parameters)
    {
        $parametersContent = sprintf('<?php return %s;', var_export($parameters, true));
        if (!file_put_contents(_PS_ROOT_DIR_ . '/app/config/parameters.php', $parametersContent)) {
            $this->setError($this->translator->trans('Cannot write app/config/parameters.php file', [], 'Install'));

            return false;
        } else {
            return $this->emptyYamlParameters();
        }
    }

    /**
     * Prevent availability of YAML parameters.
     */
    protected function emptyYamlParameters()
    {
        if (!file_put_contents(_PS_ROOT_DIR_ . '/app/config/parameters.yml', 'parameters:')) {
            $this->setError($this->translator->trans('Cannot write app/config/parameters.yml file', [], 'Install'));

            return false;
        }

        return $this->clearCache();
    }

    protected function clearCache()
    {
        if (defined('_PS_IN_TEST_')) {
            return true;
        }

        try {
            Tools::clearSf2Cache('prod');
            Tools::clearSf2Cache('dev');
        } catch (\Exception $e) {
            $this->setError($e->getMessage());

            return false;
        }

        return true;
    }

    /**
     * PROCESS : installDatabase
     * Generate settings file and create database structure.
     */
    public function installDatabase($clear_database = false)
    {
        // Clear database (only tables with same prefix)
        require_once _PS_ROOT_DIR_ . '/' . $this->bootstrapFile;
        if ($clear_database) {
            $this->clearDatabase();
        }

        $allowed_collation = ['utf8mb4_general_ci', 'utf8mb4_unicode_ci'];
        $collation_database = Db::getInstance()->getValue('SELECT @@collation_database');
        // Install database structure
        $sql_loader = new SqlLoader();
        $sql_loader->setMetaData([
            'PREFIX_' => _DB_PREFIX_,
            'ENGINE_TYPE' => _MYSQL_ENGINE_,
            'COLLATION' => (empty($collation_database) || !in_array($collation_database, $allowed_collation)) ? '' : 'COLLATE ' . $collation_database,
        ]);

        try {
            $sql_loader->parse_file(_PS_INSTALL_DATA_PATH_ . 'db_structure.sql');
        } catch (PrestashopInstallerException $e) {
            $this->setError($this->translator->trans('Database structure file not found', [], 'Install'));

            return false;
        }

        if ($errors = $sql_loader->getErrors()) {
            foreach ($errors as $error) {
                $this->setError($this->translator->trans('SQL error on query <i>%query%</i>', ['%query%' => $error['error']], 'Install'));
            }

            return false;
        }

        return $this->updateSchema();
    }

    /**
     * cache:clear
     * assetic:dump
     * doctrine:schema:update.
     *
     * @return bool
     */
    public function updateSchema()
    {
        $schemaUpgrade = new UpgradeDatabase();
        $schemaUpgrade->addDoctrineSchemaUpdate();
        $output = $schemaUpgrade->execute();

        if (0 !== $output['prestashop:schema:update-without-foreign']['exitCode']) {
            $this->setError(explode("\n", $output['prestashop:schema:update-without-foreign']['output']));

            return false;
        }

        return true;
    }

    /**
     * Clear database (only tables with same prefix).
     *
     * @param bool $truncate If true truncate the table, if false drop the table
     */
    public function clearDatabase($truncate = false)
    {
        $instance = Db::getInstance();
        $instance->execute('SET FOREIGN_KEY_CHECKS=0');
        foreach ($instance->executeS('SHOW TABLES') as $row) {
            $table = current($row);
            if (empty(_DB_PREFIX_) || preg_match('#^' . _DB_PREFIX_ . '#i', $table)) {
                $instance->execute(($truncate ? 'TRUNCATE TABLE ' : 'DROP TABLE ') . '`' . $table . '`');
            }
        }

        $instance->execute('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Initialize the prestashop context with default values during tests.
     */
    public function initializeTestContext()
    {
        $smarty = null;
        // Clean all cache values
        Cache::clean('*');

        $_SERVER['HTTP_HOST'] = 'localhost';
        $this->language->setLanguage('en');
        $context = Context::getContext();
        $context->shop = new Shop(1);
        Shop::setContext(Shop::CONTEXT_SHOP, 1);
        Configuration::loadConfiguration();
        if (!isset($context->language) || !Validate::isLoadedObject($context->language)) {
            $context->language = new Language('en');
        }

        if (!isset($context->country) || !Validate::isLoadedObject($context->country)) {
            if ($id_country = (int) Configuration::get('PS_COUNTRY_DEFAULT')) {
                $context->country = new Country((int) $id_country);
            }
        }
        if (!isset($context->currency) || !Validate::isLoadedObject($context->currency)) {
            if ($id_currency = Currency::getDefaultCurrencyId()) {
                $context->currency = new Currency((int) $id_currency);
            }
        }

        /* Instantiate cookie */
        $cookie_lifetime = defined('_PS_ADMIN_DIR_') ? (int) Configuration::get('PS_COOKIE_LIFETIME_BO') : (int) Configuration::get('PS_COOKIE_LIFETIME_FO');
        if ($cookie_lifetime > 0) {
            $cookie_lifetime = time() + (max($cookie_lifetime, 1) * 3600);
        }

        $cookie = new Cookie('ps-s' . $context->shop->id, '', $cookie_lifetime, 'localhost', false, false);

        $context->cookie = $cookie;

        $context->cart = new Cart();
        $context->employee = new Employee(1);
        if (!defined('_PS_SMARTY_FAST_LOAD_')) {
            define('_PS_SMARTY_FAST_LOAD_', true);
        }
        require_once _PS_ROOT_DIR_ . '/config/smarty.config.inc.php';

        $context->smarty = $smarty;
    }

    /**
     * PROCESS : installDefaultData
     * Create default shop and languages.
     */
    public function installDefaultData($shop_name, $iso_country = false, $all_languages = false, $clear_database = false)
    {
        if ($clear_database) {
            $this->clearDatabase(true);
        }

        // Install first shop
        if (!$this->createShop($shop_name)) {
            return false;
        }

        // Install languages
        try {
            if (!$all_languages) {
                $iso_codes_to_install = [$this->language->getLanguageIso()];
                if ($iso_country) {
                    $version = str_replace('.', '', Version::VERSION);
                    $version = substr($version, 0, 2);
                    $localization_file_content = $this->getLocalizationPackContent($version, $iso_country);

                    if ($xml = @simplexml_load_string($localization_file_content)) {
                        foreach ($xml->languages->language as $language) {
                            $iso_codes_to_install[] = (string) $language->attributes()->iso_code;
                        }
                    }
                }
                $languages = $this->installLanguages(array_unique($iso_codes_to_install));
            } else {
                $languages = $this->installLanguages();
            }
        } catch (PrestashopInstallerException $e) {
            $this->setError($e->getMessage());

            return false;
        }

        $flip_languages = array_flip($languages);
        $id_lang = (!empty($flip_languages[$this->language->getLanguageIso()])) ? $flip_languages[$this->language->getLanguageIso()] : 1;
        Configuration::updateGlobalValue('PS_LANG_DEFAULT', $id_lang);
        Configuration::updateGlobalValue('PS_VERSION_DB', _PS_INSTALL_VERSION_);
        Configuration::updateGlobalValue('PS_INSTALL_VERSION', _PS_INSTALL_VERSION_);

        Context::getContext()->language = new LanguageLegacy($id_lang);

        return true;
    }

    /**
     * PROCESS : populateDatabase
     * Populate database with default data.
     *
     * @param string|null $entity [default=null] If provided, entity to populate
     *
     * @return bool
     */
    public function populateDatabase($entity = null)
    {
        $languages = [];
        foreach (EntityLanguage::getLanguages(true) as $lang) {
            $languages[$lang['id_lang']] = $lang['iso_code'];
        }

        // Install XML data (data/xml/ folder)
        $xml_loader = new XmlLoader();
        $xml_loader->setTranslator($this->translator);
        $xml_loader->setLanguages($languages);

        if ($this->xml_loader_ids) {
            $xml_loader->setIds($this->xml_loader_ids);
        }

        try {
            if ($entity) {
                $this->callWithUnityAutoincrement(function () use ($xml_loader, $entity) {
                    $xml_loader->populateEntity($entity);
                });
            } else {
                $this->callWithUnityAutoincrement(function () use ($xml_loader) {
                    $xml_loader->populateFromXmlFiles();
                });
            }
            if ($errors = $xml_loader->getErrors()) {
                $this->setError($errors);

                return false;
            }

            // IDS from xmlLoader are stored in order to use them for fixtures
            $this->xml_loader_ids = $xml_loader->getIds();
            unset($xml_loader);

            // Install custom SQL data (db_data.sql file)
            if (file_exists(_PS_INSTALL_DATA_PATH_ . 'db_data.sql')) {
                $sql_loader = new SqlLoader();
                $sql_loader->setMetaData([
                    'PREFIX_' => _DB_PREFIX_,
                    'ENGINE_TYPE' => _MYSQL_ENGINE_,
                ]);

                $sql_loader->parse_file(_PS_INSTALL_DATA_PATH_ . 'db_data.sql', false);
                if ($errors = $sql_loader->getErrors()) {
                    $this->setError($errors);

                    return false;
                }
            }
        } catch (PrestashopInstallerException $e) {
            $this->setError($e->getMessage());

            return false;
        }

        return true;
    }

    public function createShop($shop_name)
    {
        // Create default group shop
        $shop_group = new ShopGroup();
        $shop_group->name = 'Default';
        $shop_group->active = true;
        if (!$shop_group->add()) {
            $this->setError($this->translator->trans('Cannot create group shop', [], 'Install') . ' / ' . Db::getInstance()->getMsgError());

            return false;
        }

        // Create default shop
        $shop = new Shop();
        $shop->id = 1;
        $shop->force_id = true;
        $shop->active = true;
        $shop->id_shop_group = $shop_group->id;
        $shop->id_category = 2;
        $shop->theme_name = _THEME_NAME_;
        $shop->name = $shop_name;
        if (!$shop->add()) {
            $this->setError($this->translator->trans('Cannot create shop', [], 'Install') . ' / ' . Db::getInstance()->getMsgError());

            return false;
        }
        $shop->setTheme();
        Context::getContext()->shop = $shop;

        // Create default shop URL
        $shop_url = new ShopUrl();
        $shop_url->domain = Tools::getHttpHost();
        $shop_url->domain_ssl = Tools::getHttpHost();
        $shop_url->physical_uri = __PS_BASE_URI__;
        $shop_url->id_shop = $shop->id;
        $shop_url->main = true;
        $shop_url->active = true;
        if (!$shop_url->add()) {
            $this->setError($this->translator->trans('Cannot create shop URL', [], 'Install') . ' / ' . Db::getInstance()->getMsgError());

            return false;
        }

        return true;
    }

    /**
     * Install languages.
     *
     * @param array|null $languages_list
     *
     * @return array Association between ID and iso array(id_lang => iso, ...)
     */
    public function installLanguages($languages_list = null)
    {
        if ($languages_list === null || (is_array($languages_list) && !count($languages_list))) {
            $languages_list = $this->language->getIsoList();
        }

        $languages_list = array_unique($languages_list);

        $languages_available = $this->language->getIsoList();
        $languages = [];

        foreach ($languages_list as $iso) {
            if (!in_array($iso, $languages_available)) {
                $this->callWithUnityAutoincrement(function () use ($iso) {
                    EntityLanguage::downloadAndInstallLanguagePack($iso);
                });

                continue;
            }

            if (!file_exists(_PS_INSTALL_LANGS_PATH_ . $iso . '/language.xml')) {
                throw new PrestashopInstallerException($this->translator->trans('File "language.xml" not found for language iso "%iso%"', ['%iso%' => $iso], 'Install'));
            }

            if (!$xml = @simplexml_load_file(_PS_INSTALL_LANGS_PATH_ . $iso . '/language.xml')) {
                throw new PrestashopInstallerException($this->translator->trans('File "language.xml" not valid for language iso "%iso%"', ['%iso%' => $iso], 'Install'));
            }

            $params_lang = [
                'name' => (string) $xml->name,
                'iso_code' => substr((string) $xml->language_code, 0, 2),
                'allow_accented_chars_url' => (string) $xml->allow_accented_chars_url,
                'language_code' => (string) $xml->language_code,
                'locale' => (string) $xml->locale,
            ];

            if (file_exists(_PS_TRANSLATIONS_DIR_ . (string) $iso . '.gzip') == false) {
                $language = EntityLanguage::downloadLanguagePack($iso, _PS_INSTALL_VERSION_);

                if ($language == false) {
                    throw new PrestashopInstallerException($this->translator->trans('Cannot download language pack "%iso%"', ['%iso%' => $iso], 'Install'));
                }
            }

            $errors = [];
            $locale = $params_lang['locale'];

            /* @todo check if a newer pack is available */
            if (!EntityLanguage::translationPackIsInCache($locale)) {
                EntityLanguage::downloadXLFLanguagePack($locale, $errors);

                if (!empty($errors)) {
                    throw new PrestashopInstallerException($this->translator->trans('Cannot download language pack "%iso%"', ['%iso%' => $iso], 'Install'));
                }
            }

            $this->callWithUnityAutoincrement(function () use ($iso, $params_lang, &$errors) {
                EntityLanguage::installFirstLanguagePack($iso, $params_lang, $errors);
            });

            if (!$id_lang = EntityLanguage::getIdByIso($iso, true)) {
                throw new PrestashopInstallerException($this->translator->trans(
                    'Cannot install language "%iso%"',
                    ['%iso%' => (string) $xml->name],
                    'Install'
                ));
            }

            $languages[$id_lang] = $iso;

            // Copy language flag
            if (is_writable(_PS_IMG_DIR_ . 'l/')) {
                if (!copy(_PS_INSTALL_LANGS_PATH_ . $iso . '/flag.jpg', _PS_IMG_DIR_ . 'l/' . $id_lang . '.jpg')) {
                    throw new PrestashopInstallerException($this->translator->trans('Cannot copy flag language "%flag%"', ['%flag%' => _PS_INSTALL_LANGS_PATH_ . $iso . '/flag.jpg => ' . _PS_IMG_DIR_ . 'l/' . $id_lang . '.jpg'], 'Install'));
                }
            }
        }

        return $languages;
    }

    public function copyLanguageImages($iso)
    {
        $img_path = _PS_INSTALL_LANGS_PATH_ . $iso . '/img/';
        if (!is_dir($img_path)) {
            return;
        }

        $list = [
            'products' => _PS_PRODUCT_IMG_DIR_,
            'categories' => _PS_CAT_IMG_DIR_,
            'manufacturers' => _PS_MANU_IMG_DIR_,
            'suppliers' => _PS_SUPP_IMG_DIR_,
            'stores' => _PS_STORE_IMG_DIR_,
            null => _PS_IMG_DIR_ . 'l/', // Little trick to copy images in img/l/ path with all types
        ];

        foreach ($list as $cat => $dst_path) {
            if (!is_writable($dst_path)) {
                continue;
            }

            copy($img_path . $iso . '.jpg', $dst_path . $iso . '.jpg');

            $types = ImageType::getImagesTypes($cat);
            foreach ($types as $type) {
                if (file_exists($img_path . $iso . '-default-' . $type['name'] . '.jpg')) {
                    copy(
                        $img_path . $iso . '-default-' . $type['name'] . '.jpg',
                        $dst_path . $iso . '-default-' . $type['name'] . '.jpg'
                    );
                } else {
                    ImageManager::resize(
                        $img_path . $iso . '.jpg',
                        $dst_path . $iso . '-default-' . $type['name'] . '.jpg',
                        $type['width'],
                        $type['height']
                    );
                }
            }
        }
    }

    private static $_cache_localization_pack_content = null;

    public function getLocalizationPackContent($version, $country)
    {
        if (self::$_cache_localization_pack_content === null || array_key_exists($country, self::$_cache_localization_pack_content)) {
            $localizationWarmer = new LocalizationWarmer($version, $country);
            $localization_file_content = $localizationWarmer->warmUp(_PS_CACHE_DIR_ . 'sandbox' . DIRECTORY_SEPARATOR);

            self::$_cache_localization_pack_content[$country] = $localization_file_content[0];
        }

        return self::$_cache_localization_pack_content[$country] ?? false;
    }

    /**
     * PROCESS : configureShop
     * Set default shop configuration.
     */
    public function configureShop(array $data = [])
    {
        //clear image cache in tmp folder
        if (file_exists(_PS_TMP_IMG_DIR_)) {
            foreach (scandir(_PS_TMP_IMG_DIR_, SCANDIR_SORT_NONE) as $file) {
                if ($file[0] != '.' && $file != 'index.php') {
                    Tools::deleteFile(_PS_TMP_IMG_DIR_ . $file);
                }
            }
        }

        $default_data = [
            'shop_name' => 'My Shop',
            'shop_country' => 'us',
            'shop_timezone' => 'US/Eastern', // TODO : this timezone is deprecated
            'use_smtp' => false,
            'smtp_encryption' => 'off',
            'smtp_port' => 25,
            'rewrite_engine' => false,
            'enable_ssl' => false,
        ];

        foreach ($default_data as $k => $v) {
            if (!isset($data[$k])) {
                $data[$k] = $v;
            }
        }

        Context::getContext()->shop = new Shop(1);
        Configuration::loadConfiguration();

        $id_country = (int) Country::getByIso($data['shop_country']);

        // Set default configuration
        Configuration::updateGlobalValue('PS_SHOP_DOMAIN', Tools::getHttpHost());
        Configuration::updateGlobalValue('PS_SHOP_DOMAIN_SSL', Tools::getHttpHost());
        Configuration::updateGlobalValue('PS_INSTALL_VERSION', _PS_INSTALL_VERSION_);
        Configuration::updateGlobalValue('PS_LOCALE_LANGUAGE', $this->language->getLanguageIso());
        Configuration::updateGlobalValue('PS_SHOP_NAME', $data['shop_name']);
        Configuration::updateGlobalValue('PS_COUNTRY_DEFAULT', $id_country);
        Configuration::updateGlobalValue('PS_LOCALE_COUNTRY', $data['shop_country']);
        Configuration::updateGlobalValue('PS_TIMEZONE', $data['shop_timezone']);
        Configuration::updateGlobalValue('PS_CONFIGURATION_AGREMENT', (int) $data['configuration_agrement']);

        // Set SSL configuration
        Configuration::updateGlobalValue('PS_SSL_ENABLED', (int) $data['enable_ssl']);
        Configuration::updateGlobalValue('PS_SSL_ENABLED_EVERYWHERE', (int) $data['enable_ssl']);

        // Set mails configuration
        Configuration::updateGlobalValue('PS_MAIL_METHOD', ($data['use_smtp']) ? 2 : 1);
        Configuration::updateGlobalValue('PS_MAIL_SMTP_ENCRYPTION', $data['smtp_encryption']);
        Configuration::updateGlobalValue('PS_MAIL_SMTP_PORT', $data['smtp_port']);

        // Set default rewriting settings
        Configuration::updateGlobalValue('PS_REWRITING_SETTINGS', $data['rewrite_engine']);

        $groups = Group::getGroups((int) Configuration::get('PS_LANG_DEFAULT'));
        $groups_default = Db::getInstance()->executeS('SELECT `name` FROM ' . _DB_PREFIX_ . 'configuration WHERE `name` LIKE "PS_%_GROUP" ORDER BY `id_configuration`');
        foreach ($groups_default as &$group_default) {
            if (is_array($group_default) && isset($group_default['name'])) {
                $group_default = $group_default['name'];
            }
        }
        unset($group_default);

        if (is_array($groups) && count($groups)) {
            foreach ($groups as $key => $group) {
                if (Configuration::get($groups_default[$key]) != $groups[$key]['id_group']) {
                    Configuration::updateGlobalValue($groups_default[$key], (int) $groups[$key]['id_group']);
                }
            }
        }

        $states = Db::getInstance()->executeS('SELECT `id_order_state` FROM ' . _DB_PREFIX_ . 'order_state ORDER by `id_order_state`');
        $states_default = Db::getInstance()->executeS('SELECT MIN(`id_configuration`), `name` FROM ' . _DB_PREFIX_ . 'configuration WHERE `name` LIKE "PS_OS_%" GROUP BY `value` ORDER BY`id_configuration`');

        foreach ($states_default as &$state_default) {
            if (is_array($state_default) && isset($state_default['name'])) {
                $state_default = $state_default['name'];
            }
        }
        unset($state_default);

        if (is_array($states) && count($states)) {
            foreach ($states as $key => $state) {
                if (Configuration::get($states_default[$key]) != $states[$key]['id_order_state']) {
                    Configuration::updateGlobalValue($states_default[$key], (int) $states[$key]['id_order_state']);
                }
            }
            /* deprecated order state */
            Configuration::updateGlobalValue('PS_OS_OUTOFSTOCK_PAID', (int) Configuration::get('PS_OS_OUTOFSTOCK'));
        }

        // Set logo configuration
        if (file_exists(_PS_IMG_DIR_ . 'logo.png')) {
            [$width, $height] = getimagesize(_PS_IMG_DIR_ . 'logo.png');
            Configuration::updateGlobalValue('SHOP_LOGO_WIDTH', round($width));
            Configuration::updateGlobalValue('SHOP_LOGO_HEIGHT', round($height));
        }

        // Disable cache for debug mode
        if ($this->isDebug) {
            Configuration::updateGlobalValue('PS_SMARTY_CACHE', 1);
        }

        // Active only the country selected by the merchant
        Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'country SET active = 0 WHERE id_country != ' . (int) $id_country);

        // Set localization configuration
        $version = str_replace('.', '', Version::VERSION);
        $version = substr($version, 0, 2);
        $localization_file_content = $this->getLocalizationPackContent($version, $data['shop_country']);

        $locale = new LocalizationPack();
        $this->callWithUnityAutoincrement(function () use ($locale, $localization_file_content) {
            $locale->loadLocalisationPack($localization_file_content, [], true);
        });

        // Create default employee
        if (isset($data['admin_firstname'], $data['admin_lastname'], $data['admin_password'], $data['admin_email'])) {
            $employee = new Employee();
            $employee->id = 1;
            $employee->force_id = true;
            $employee->firstname = Tools::ucfirst($data['admin_firstname']);
            $employee->lastname = Tools::ucfirst($data['admin_lastname']);
            $employee->email = $data['admin_email'];
            $employee->setWsPasswd($data['admin_password']);
            $employee->last_passwd_gen = date('Y-m-d h:i:s', strtotime('-360 minutes'));
            $employee->bo_theme = 'default';
            $employee->default_tab = 1;
            $employee->active = true;
            $employee->id_profile = 1;
            $employee->id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
            $employee->bo_menu = true;
            if (!$employee->add()) {
                $this->setError($this->translator->trans('Cannot create admin account', [], 'Install'));

                return false;
            }
            Context::getContext()->employee = $employee;
        } else {
            $this->setError($this->translator->trans('Cannot create admin account', [], 'Install'));

            return false;
        }

        // Update default contact
        Configuration::updateGlobalValue('PS_SHOP_EMAIL', $data['admin_email']);
        Configuration::updateGlobalValue('PS_LOGS_EMAIL_RECEIVERS', $data['admin_email']);

        $contacts = new PrestaShopCollection('Contact');
        /** @var \Contact $contact */
        foreach ($contacts as $contact) {
            $contact->email = $data['admin_email'];
            $contact->update();
        }

        if (!@Tools::generateHtaccess(null, $data['rewrite_engine'])) {
            Configuration::updateGlobalValue('PS_REWRITING_SETTINGS', 0);
        }

        Tools::generateRobotsFile();

        return true;
    }

    /**
     * Get all modules present on the disk
     */
    public function getModulesOnDisk(): array
    {
        $modulesOnDisk = (new Finder())->directories()->depth('== 0')->in(_PS_MODULE_DIR_);

        $configReader = new ModuleConfigReader(_PS_MODULE_DIR_);
        $isoCode = Context::getContext()->language->iso_code;

        $modules = [];
        foreach ($modulesOnDisk as $module) {
            $moduleData = $configReader->read(
                $module->getFileName(),
                $isoCode
            );

            if ($moduleData !== null) {
                if ($moduleData->get('name') !== $module->getFilename()) {
                    continue;
                }

                $modules[$module->getFileName()] = $moduleData;
            }
        }

        return $modules;
    }

    /**
     * Get all themes present on the disk
     */
    public function getThemesOnDisk(): array
    {
        $themesOnDisk = (new Finder())->directories()->depth('== 0')->in(_PS_ALL_THEMES_DIR_);
        $configReader = new ThemeConfigReader(_PS_ALL_THEMES_DIR_);
        $themes = [];
        foreach ($themesOnDisk as $theme) {
            $themeConfig = $configReader->read(
                $theme->getFileName()
            );

            if ($themeConfig !== null) {
                $themes[] = $themeConfig;
            }
        }

        return $this->sortThemesByDisplayname($themes);
    }

    /**
     * Sort addons categories by order field.
     *
     * @param array $themes
     *
     * @return array
     */
    private function sortThemesByDisplayName(array $themes): array
    {
        uasort(
            $themes,
            function ($a, $b) {
                $a = !isset($a['display_name']) ? 0 : $a['display_name'];
                $b = !isset($b['display_name']) ? 0 : $b['display_name'];

                return $a <=> $b;
            }
        );

        // Convert array to object to be consistent with current API call
        return $themes;
    }

    /**
     * PROCESS : installModules
     * Download module from addons and Install all modules in ~/modules/ directory.
     *
     * @param array $modules Modules to  install
     *
     * @return bool
     */
    public function installModules(array $modules): bool
    {
        ModuleEntity::updateTranslationsAfterInstall(false);

        $result = $this->executeAction(
            $modules,
            'install',
            $this->translator->trans(
                'Cannot install module "%module%"',
                ['%module%' => '%module%'],
                'Install'
            )
        );
        if ($result === false) {
            return false;
        }

        ModuleEntity::updateTranslationsAfterInstall(true);
        EntityLanguage::updateModulesTranslations($modules);

        return true;
    }

    public function postInstall(): bool
    {
        $moduleCollection = ModuleManagerBuilder::getInstance()->buildRepository()->getInstalledModules();
        $modules = array_map(function (Module $module): string {
            return $module->get('name');
        }, iterator_to_array($moduleCollection));

        if (!$this->executeAction(
            $modules,
            'postInstall',
            $this->translator->trans(
                'Cannot execute post install on module "%module%"',
                ['%module%' => '%module%'],
                'Install'
            )
        )) {
            return false;
        }

        // Remove the install lock file
        return Tools::deleteFile(PS_INSTALLATION_LOCK_FILE);
    }

    protected function executeAction(array $modules, string $action, string $errorMessage): bool
    {
        $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $moduleManager = $moduleManagerBuilder->build();

        $errors = [];
        foreach ($modules as $module_name) {
            $moduleException = null;

            if ($action === 'install' && $moduleManager->isInstalled($module_name)) {
                continue;
            }

            try {
                $moduleActionIsExecuted = $moduleManager->{$action}($module_name);
            } catch (PrestaShopException $e) {
                $moduleActionIsExecuted = false;
                $moduleException = $e->getMessage();
            }

            if (!$moduleActionIsExecuted) {
                $moduleErrors = [
                    str_replace(
                        '%module%',
                        $module_name,
                        $errorMessage
                    ),
                ];

                if (!empty($moduleException)) {
                    $moduleErrors[] = $moduleException;
                }

                $errors[$module_name] = $moduleErrors;
            }
        }

        if (count($errors) > 0) {
            $this->setError($errors);

            return false;
        }

        ModuleEntity::updateTranslationsAfterInstall(true);

        return true;
    }

    /**
     * PROCESS : installFixtures
     * Install fixtures (E.g. demo products).
     */
    public function installFixtures($entity = null, array $data = [])
    {
        $fixtures_path = _PS_INSTALL_FIXTURES_PATH_ . 'fashion/';
        $fixtures_name = 'fashion';
        $zip_file = _PS_ROOT_DIR_ . '/download/fixtures.zip';
        $temp_dir = _PS_ROOT_DIR_ . '/download/fixtures/';

        // Load class (use fixture class if one exists, or use InstallXmlLoader)
        if (file_exists($fixtures_path . '/install.php')) {
            require_once $fixtures_path . '/install.php';
            $class = 'InstallFixtures' . Tools::toCamelCase($fixtures_name);
            if (!class_exists($class, false)) {
                $this->setError($this->translator->trans('Fixtures class "%class%" not found', ['%class%' => $class], 'Install'));

                return false;
            }

            $xml_loader = new $class();
            if (!$xml_loader instanceof XmlLoader) {
                $this->setError($this->translator->trans('"%class%" must be an instance of "InstallXmlLoader"', ['%class%' => $class], 'Install'));

                return false;
            }
        } else {
            $xml_loader = new XmlLoader();
        }
        $xml_loader->setTranslator($this->translator);

        // Install XML data (data/xml/ folder)
        $xml_loader->setFixturesPath($fixtures_path);
        if ($this->xml_loader_ids) {
            $xml_loader->setIds($this->xml_loader_ids);
        }

        $languages = [];
        foreach (EntityLanguage::getLanguages(false) as $lang) {
            $languages[$lang['id_lang']] = $lang['iso_code'];
        }
        $xml_loader->setLanguages($languages);

        if ($entity) {
            $this->callWithUnityAutoincrement(function () use ($xml_loader, $entity) {
                $xml_loader->populateEntity($entity);
            });
        } else {
            $this->callWithUnityAutoincrement(function () use ($xml_loader) {
                $xml_loader->populateFromXmlFiles();
            });
            Tools::deleteDirectory($temp_dir, true);
            @unlink($zip_file);
        }

        if ($errors = $xml_loader->getErrors()) {
            $this->setError($errors);

            return false;
        }

        // IDS from xmlLoader are stored in order to use them for fixtures
        $this->xml_loader_ids = $xml_loader->getIds();
        unset($xml_loader);

        if ($entity === 'category' || $entity === null) {
            Category::regenerateEntireNtree();
        }

        if ($entity === null) {
            Search::indexation(true);
        }

        // Update fixtures lang
        foreach ($languages as $lang) {
            LanguageLegacy::updateMultilangTable($lang);
        }

        return true;
    }

    public function installTheme(string $themeName = null): bool
    {
        $themeName = $themeName ?: _THEME_NAME_;
        $builder = new ThemeManagerBuilder(
            Context::getContext(),
            Db::getInstance(),
            null,
            new PSRLoggerAdapter($this->getLogger())
        );

        $theme_manager = $builder->build();

        if (!($theme_manager->install($themeName) && $theme_manager->enable($themeName))) {
            $this->getLogger()->log('Could not install theme');

            return false;
        }

        /*
         * Copy language default images.
         * We do this action after install theme because we
         * need image types information.
         */
        $languages = $this->language->getIsoList();
        foreach ($languages as $iso) {
            $this->copyLanguageImages($iso);
        }

        return true;
    }

    /**
     * Call callback with database connection temporary
     * configured with auto increment value and offset to 1.
     */
    public function callWithUnityAutoincrement(callable $callback, ...$args)
    {
        $db = Db::getInstance();

        $backupAiIncrement = $db->executeS('SELECT @@SESSION.auto_increment_increment AS v;', true, false)[0]['v'];
        $backupAiOffset = $db->executeS('SELECT @@SESSION.auto_increment_offset AS v;', true, false)[0]['v'];
        if ($backupAiIncrement > 1 || $backupAiOffset > 1) {
            $db->execute('SET SESSION auto_increment_offset = 1', false);
            $db->execute('SET SESSION auto_increment_increment = 1', false);
        }
        try {
            return $callback(...$args);
        } finally {
            if ($backupAiIncrement > 1 || $backupAiOffset > 1) {
                $db->execute('SET SESSION auto_increment_offset = ' . (int) $backupAiOffset, false);
                $db->execute('SET SESSION auto_increment_increment = ' . (int) $backupAiIncrement, false);
            }
        }
    }
}
