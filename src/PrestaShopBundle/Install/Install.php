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


namespace PrestaShopBundle\Install;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use RandomLib;
use PrestaShop\PrestaShop\Adapter\Entity\FileLogger;
use PrestaShop\PrestaShop\Adapter\Entity\Tools;
use PrestaShop\PrestaShop\Adapter\Entity\Configuration;
use PrestaShop\PrestaShop\Adapter\Entity\Language as EntityLanguage;
use PrestaShop\PrestaShop\Adapter\Entity\Shop;
use PrestaShop\PrestaShop\Adapter\Entity\ShopGroup;
use PrestaShop\PrestaShop\Adapter\Entity\ShopUrl;
use PrestaShop\PrestaShop\Adapter\Entity\Context;
use PrestaShop\PrestaShop\Adapter\Entity\ImageType;
use PrestaShop\PrestaShop\Adapter\Entity\ImageManager;
use PrestaShop\PrestaShop\Adapter\Entity\Country;
use PrestaShop\PrestaShop\Adapter\Entity\Group;
use PrestaShop\PrestaShop\Adapter\Entity\LocalizationPack;
use PrestaShop\PrestaShop\Adapter\Entity\Employee;
use PrestaShop\PrestaShop\Adapter\Entity\PrestaShopCollection;
use PrestaShop\PrestaShop\Adapter\Entity\Module;
use PrestaShop\PrestaShop\Adapter\Entity\Search;
use InstallSession;
use Composer\Script\Event;
use PrestaShop\PrestaShop\Adapter\Entity\Db;
use PrestashopInstallerException;

use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManagerBuilder;
use PrestaShopBundle\Cache\LocalizationWarmer;
use Symfony\Component\Yaml\Yaml;

class Install extends AbstractInstall
{
    const SETTINGS_FILE = 'config/settings.inc.php';
    const BOOTSTRAP_FILE = 'config/bootstrap.php';

    protected $logger;

    public function setError($errors)
    {
        static $logger = null;

        if (null === $logger) {
            $cacheDir = _PS_ROOT_DIR_.'/app/logs/';
            $file = $cacheDir .(_PS_MODE_DEV_ ? 'dev' : 'prod').'_'.@date('Ymd').'_installation.log';
            $logger = new FileLogger();
            $logger->setFilename($file);
            $this->logger = $logger;
        }

        if (!is_array($errors)) {
            $errors = array($errors);
        }

        parent::setError($errors);

        foreach ($errors as $error) {
            $this->logger->logError($error);
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
            file_exists(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.self::SETTINGS_FILE)
            && !is_writable(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.self::SETTINGS_FILE)
        ) {
            $this->setError($this->translator->trans('%file% file is not writable (check permissions)', array('%file%' => self::SETTINGS_FILE), 'Install'));
            return false;
        } elseif (
            !file_exists(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.self::SETTINGS_FILE)
            && !is_writable(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.dirname(self::SETTINGS_FILE))
        ) {
            $this->setError($this->translator->trans(
                '%folder% folder is not writable (check permissions)',
                array('%folder%' => dirname(self::SETTINGS_FILE)), 'Install')
            );
            return false;
        }

        $secret = Tools::passwdGen(56);
        $cookie_key = defined('_COOKIE_KEY_')?_COOKIE_KEY_:Tools::passwdGen(56);
        $cookie_iv = defined('_COOKIE_IV_')?_COOKIE_IV_:Tools::passwdGen(8);
        $database_port = null;

        $splits = preg_split('#:#', $database_host);
        $nbSplits = count($splits);

        if ($nbSplits >= 2) {
            $database_port = array_pop($splits);
            $database_host = implode(':', $splits);
        }

        $key = \PhpEncryption::createNewRandomKey();

        $parameters = array(
            'parameters' => array(
                'database_host' => $database_host,
                'database_port' => $database_port,
                'database_user' => $database_user,
                'database_password' => $database_password,
                'database_name' => $database_name,
                'database_prefix' => $database_prefix,
                'database_engine' =>  $database_engine,
                'cookie_key' => $cookie_key,
                'cookie_iv' =>  $cookie_iv,
                'new_cookie_key' => $key,
                'ps_creation_date' => date('Y-m-d'),
                'secret' => $secret,
                'locale' => $this->language->getLanguage()->getLocale(),
            )
        );

        array_walk($parameters['parameters'], function (&$param) {
            $param = str_replace('%', '%%', $param);
        });

        $parameters = array_replace_recursive(
            Yaml::parse(file_get_contents(_PS_ROOT_DIR_.'/app/config/parameters.yml.dist')),
            $parameters
        );

        $settings_content = "<?php\n";
        $settings_content .= "//@deprecated 1.7";

        if (!file_put_contents(_PS_ROOT_DIR_.'/'.self::SETTINGS_FILE, $settings_content)) {
            $this->setError($this->translator->trans('Cannot write settings file', array(), 'Install'));
            return false;
        }

        if (!$this->processParameters($parameters)) {
            return false;
        }

        return true;
    }

    /**
     * Replace "parameters.yml" with "parameters.php" in "app/config"
     *
     * @param $parameters
     * @return bool|int
     */
    public function processParameters($parameters)
    {
        $parametersContent = sprintf('<?php return %s;', var_export($parameters, true));
        if (!file_put_contents(_PS_ROOT_DIR_ . '/app/config/parameters.php', $parametersContent)) {
            $this->setError($this->translator->trans('Cannot write app/config/parameters.php file', array(), 'Install'));

            return false;
        } else {
            return $this->emptyYamlParameters();
        }
    }

    /**
     * Prevent availability of YAML parameters
     */
    protected function emptyYamlParameters()
    {
        if (!file_put_contents(_PS_ROOT_DIR_ . '/app/config/parameters.yml', 'parameters:')) {
            $this->setError($this->translator->trans('Cannot write app/config/parameters.yml file', array(), 'Install'));

            return false;
        }

        return $this->clearCache();
    }

    protected function clearCache()
    {
        $output = Tools::clearSf2Cache('prod');

        if (0 !== $output['cache:clear']['exitCode']) {
            $this->setError(explode("\n", $output['cache:clear']['output']));
            return false;
        }

        $output = Tools::clearSf2Cache();

        if (0 !== $output['cache:clear']['exitCode']) {
            $this->setError(explode("\n", $output['cache:clear']['output']));
            return false;
        }

        return true;
    }

    /**
     * PROCESS : installDatabase
     * Generate settings file and create database structure
     */
    public function installDatabase($clear_database = false)
    {
        // Clear database (only tables with same prefix)
        require_once _PS_ROOT_DIR_.'/'.self::BOOTSTRAP_FILE;
        if ($clear_database) {
            $this->clearDatabase();
        }

        $allowed_collation = array('utf8_general_ci', 'utf8_unicode_ci');
        $collation_database = Db::getInstance()->getValue('SELECT @@collation_database');
        // Install database structure
        $sql_loader = new SqlLoader();
        $sql_loader->setMetaData(array(
            'PREFIX_' => _DB_PREFIX_,
            'ENGINE_TYPE' => _MYSQL_ENGINE_,
            'COLLATION' => (empty($collation_database) || !in_array($collation_database, $allowed_collation)) ? '' : 'COLLATE '.$collation_database,
        ));

        try {
            $sql_loader->parse_file(_PS_INSTALL_DATA_PATH_.'db_structure.sql');
        } catch (PrestashopInstallerException $e) {
            $this->setError($this->translator->trans('Database structure file not found', array(), 'Install'));
            return false;
        }

        if ($errors = $sql_loader->getErrors()) {
            foreach ($errors as $error) {
                $this->setError($this->translator->trans('SQL error on query <i>%query%</i>', array('%query%' => $error['error']), 'Install'));
            }
            return false;
        }

        return $this->generateSf2ProductionEnv();
    }

    /**
     * Pass SF2 to production
     * cache:clear
     * assetic:dump
     * doctrine:schema:update
     *
     * @return bool
     */
    public function generateSf2ProductionEnv()
    {
        $schemaUpgrade = new \PrestaShopBundle\Service\Database\Upgrade();
        $schemaUpgrade->addDoctrineSchemaUpdate();
        $output = $schemaUpgrade->execute();

        if (0 !== $output['prestashop:schema:update-without-foreign']['exitCode']) {
            $this->setError(explode("\n", $output['prestashop:schema:update-without-foreign']['output']));
            return false;
        }

        return true;
    }


    /**
     * Clear database (only tables with same prefix)
     *
     * @param bool $truncate If true truncate the table, if false drop the table
     */
    public function clearDatabase($truncate = false)
    {
        $instance = Db::getInstance();
        $instance->execute('SET FOREIGN_KEY_CHECKS=0');
        $sqlRequest = (($truncate) ? 'TRUNCATE' : 'DROP TABLE');
        foreach ($instance->executeS('SHOW TABLES') as $row) {
            $table = current($row);
            if (!_DB_PREFIX_ || preg_match('#^'._DB_PREFIX_.'#i', $table)) {
                $sqlRequest .= ' `'.$table.'`,';
            }
        }
        $instance->execute(rtrim($sqlRequest, ','));
        $instance->execute('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * PROCESS : installDefaultData
     * Create default shop and languages
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
                $iso_codes_to_install = array($this->language->getLanguageIso());
                if ($iso_country) {
                    $version = str_replace('.', '', _PS_VERSION_);
                    $version = substr($version, 0, 2);
                    $localization_file_content = $this->getLocalizationPackContent($version, $iso_country);

                    if ($xml = @simplexml_load_string($localization_file_content)) {
                        foreach ($xml->languages->language as $language) {
                            $iso_codes_to_install[] = (string)$language->attributes()->iso_code;
                        }
                    }
                }
            } else {
                $iso_codes_to_install = null;
            }
            $iso_codes_to_install = array_flip(array_flip($iso_codes_to_install));
            $languages = $this->installLanguages($iso_codes_to_install);
        } catch (PrestashopInstallerException $e) {
            $this->setError($e->getMessage());
            return false;
        }

        $flip_languages = array_flip($languages);
        $id_lang =  (!empty($flip_languages[$this->language->getLanguageIso()])) ? $flip_languages[$this->language->getLanguageIso()] : 1;
        Configuration::updateGlobalValue('PS_LANG_DEFAULT', $id_lang);
        Configuration::updateGlobalValue('PS_VERSION_DB', _PS_INSTALL_VERSION_);
        Configuration::updateGlobalValue('PS_INSTALL_VERSION', _PS_INSTALL_VERSION_);
        return true;
    }

    /**
     * PROCESS : populateDatabase
     * Populate database with default data
     */
    public function populateDatabase($entity = null)
    {
        $languages = array();
        foreach (EntityLanguage::getLanguages(true) as $lang) {
            $languages[$lang['id_lang']] = $lang['iso_code'];
        }

        // Install XML data (data/xml/ folder)
        $xml_loader = new XmlLoader();
        $xml_loader->setTranslator($this->translator);
        $xml_loader->setLanguages($languages);

        if (isset($this->xml_loader_ids) && $this->xml_loader_ids) {
            $xml_loader->setIds($this->xml_loader_ids);
        }

        if ($entity) {
            $xml_loader->populateEntity($entity);
        } else {
            $xml_loader->populateFromXmlFiles();
        }
        if ($errors = $xml_loader->getErrors()) {
            $this->setError($errors);
            return false;
        }

        // IDS from xmlLoader are stored in order to use them for fixtures
        $this->xml_loader_ids = $xml_loader->getIds();
        unset($xml_loader);

        // Install custom SQL data (db_data.sql file)
        if (file_exists(_PS_INSTALL_DATA_PATH_.'db_data.sql')) {
            $sql_loader = new SqlLoader();
            $sql_loader->setMetaData(array(
                'PREFIX_' => _DB_PREFIX_,
                'ENGINE_TYPE' => _MYSQL_ENGINE_,
            ));

            $sql_loader->parse_file(_PS_INSTALL_DATA_PATH_.'db_data.sql', false);
            if ($errors = $sql_loader->getErrors()) {
                $this->setError($errors);
                return false;
            }
        }

        // Copy language default images (we do this action after database in populated because we need image types information)
        foreach ($languages as $iso) {
            $this->copyLanguageImages($iso);
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
            $this->setError($this->translator->trans('Cannot create group shop', array(), 'Install').' / '.Db::getInstance()->getMsgError());
            return false;
        }

        // Create default shop
        $shop = new Shop();
        $shop->active = true;
        $shop->id_shop_group = $shop_group->id;
        $shop->id_category = 2;
        $shop->theme_name = _THEME_NAME_;
        $shop->name = $shop_name;
        if (!$shop->add()) {
            $this->setError($this->translator->trans('Cannot create shop', array(), 'Install').' / '.Db::getInstance()->getMsgError());
            return false;
        }
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
            $this->setError($this->translator->trans('Cannot create shop URL', array(), 'Install').' / '.Db::getInstance()->getMsgError());
            return false;
        }

        return true;
    }

    /**
     * Install languages
     *
     * @return array Association between ID and iso array(id_lang => iso, ...)
     */
    public function installLanguages($languages_list = null)
    {
        if ($languages_list == null || !is_array($languages_list) || !count($languages_list)) {
            $languages_list = $this->language->getIsoList();
        }

        $languages_list = array_unique($languages_list);

        $languages_available = $this->language->getIsoList();
        $languages = array();

        foreach ($languages_list as $iso) {
            if (!in_array($iso, $languages_available)) {
                EntityLanguage::downloadAndInstallLanguagePack($iso);
                continue;
            }
            if (!file_exists(_PS_INSTALL_LANGS_PATH_.$iso.'/language.xml')) {
                throw new PrestashopInstallerException($this->translator->trans('File "language.xml" not found for language iso "%iso%"', array('%iso%' => $iso), 'Install'));
            }

            if (!$xml = @simplexml_load_file(_PS_INSTALL_LANGS_PATH_.$iso.'/language.xml')) {
                throw new PrestashopInstallerException($this->translator->trans('File "language.xml" not valid for language iso "%iso%"', array('%iso%' => $iso), 'Install'));
            }

            $params_lang = array(
                'name' => (string)$xml->name,
                'iso_code' => substr((string)$xml->language_code, 0, 2),
                'allow_accented_chars_url' => (string)$xml->allow_accented_chars_url,
                'language_code' => (string)$xml->language_code,
                'locale' => (string)$xml->locale,
            );

            if (InstallSession::getInstance()->safe_mode) {
                EntityLanguage::checkAndAddLanguage($iso, false, true, $params_lang);
            } else {
                if (file_exists(_PS_TRANSLATIONS_DIR_.(string)$iso.'.gzip') == false) {
                    $language = EntityLanguage::downloadLanguagePack($iso, _PS_INSTALL_VERSION_);

                    if ($language == false) {
                        throw new PrestashopInstallerException($this->translator->trans('Cannot download language pack "%iso%"', array('%iso%' => $iso), 'Install'));
                    }
                }

                $errors = array();
                EntityLanguage::installLanguagePack($iso, $params_lang, $errors);
            }

            EntityLanguage::loadLanguages();

            Tools::clearCache();

            if (!$id_lang = EntityLanguage::getIdByIso($iso, true)) {
                throw new PrestashopInstallerException($this->translator->trans('Cannot install language "%iso%"', array('%iso%' => ($xml->name ? $xml->name : $iso) ), 'Install'));
            }

            $languages[$id_lang] = $iso;

            // Copy language flag
            if (is_writable(_PS_IMG_DIR_.'l/')) {
                if (!copy(_PS_INSTALL_LANGS_PATH_.$iso.'/flag.jpg', _PS_IMG_DIR_.'l/'.$id_lang.'.jpg')) {
                    throw new PrestashopInstallerException($this->translator->trans('Cannot copy flag language "%flag%"', array('%flag%' => _PS_INSTALL_LANGS_PATH_.$iso.'/flag.jpg => '._PS_IMG_DIR_.'l/'.$id_lang.'.jpg'), 'Install'));
                }
            }
        }

        return $languages;
    }

    public function copyLanguageImages($iso)
    {
        $img_path = _PS_INSTALL_LANGS_PATH_.$iso.'/img/';
        if (!is_dir($img_path)) {
            return;
        }

        $list = array(
            'products' => _PS_PROD_IMG_DIR_,
            'categories' => _PS_CAT_IMG_DIR_,
            'manufacturers' => _PS_MANU_IMG_DIR_,
            'suppliers' => _PS_SUPP_IMG_DIR_,
            'stores' => _PS_STORE_IMG_DIR_,
            null => _PS_IMG_DIR_.'l/', // Little trick to copy images in img/l/ path with all types
        );

        foreach ($list as $cat => $dst_path) {
            if (!is_writable($dst_path)) {
                continue;
            }

            copy($img_path.$iso.'.jpg', $dst_path.$iso.'.jpg');

            $types = ImageType::getImagesTypes($cat);
            foreach ($types as $type) {
                if (file_exists($img_path.$iso.'-default-'.$type['name'].'.jpg')) {
                    copy($img_path.$iso.'-default-'.$type['name'].'.jpg', $dst_path.$iso.'-default-'.$type['name'].'.jpg');
                } else {
                    ImageManager::resize($img_path.$iso.'.jpg', $dst_path.$iso.'-default-'.$type['name'].'.jpg', $type['width'], $type['height']);
                }
            }
        }
    }

    private static $_cache_localization_pack_content = null;
    public function getLocalizationPackContent($version, $country)
    {
        if (Install::$_cache_localization_pack_content === null || array_key_exists($country, Install::$_cache_localization_pack_content)) {
            $localizationWarmer = new LocalizationWarmer($version, $country);
            $localization_file_content  = $localizationWarmer->warmUp(_PS_CACHE_DIR_.'sandbox'.DIRECTORY_SEPARATOR);

            Install::$_cache_localization_pack_content[$country] = $localization_file_content;
        }

        return isset(Install::$_cache_localization_pack_content[$country]) ? Install::$_cache_localization_pack_content[$country] : false;
    }

    /**
     * PROCESS : configureShop
     * Set default shop configuration
     */
    public function configureShop(array $data = array())
    {
        //clear image cache in tmp folder
        if (file_exists(_PS_TMP_IMG_DIR_)) {
            foreach (scandir(_PS_TMP_IMG_DIR_) as $file) {
                if ($file[0] != '.' && $file != 'index.php') {
                    Tools::deleteFile(_PS_TMP_IMG_DIR_.$file);
                }
            }
        }

        $default_data = array(
            'shop_name' => 'My Shop',
            'shop_activity' => '',
            'shop_country' => 'us',
            'shop_timezone' => 'US/Eastern', // TODO : this timezone is deprecated
            'use_smtp' => false,
            'smtp_encryption' => 'off',
            'smtp_port' => 25,
            'rewrite_engine' => false,
        );

        foreach ($default_data as $k => $v) {
            if (!isset($data[$k])) {
                $data[$k] = $v;
            }
        }

        Context::getContext()->shop = new Shop(1);
        Configuration::loadConfiguration();

        $id_country = (int)Country::getByIso($data['shop_country']);

        // Set default configuration
        Configuration::updateGlobalValue('PS_SHOP_DOMAIN', Tools::getHttpHost());
        Configuration::updateGlobalValue('PS_SHOP_DOMAIN_SSL', Tools::getHttpHost());
        Configuration::updateGlobalValue('PS_INSTALL_VERSION', _PS_INSTALL_VERSION_);
        Configuration::updateGlobalValue('PS_LOCALE_LANGUAGE', $this->language->getLanguageIso());
        Configuration::updateGlobalValue('PS_SHOP_NAME', $data['shop_name']);
        Configuration::updateGlobalValue('PS_SHOP_ACTIVITY', $data['shop_activity']);
        Configuration::updateGlobalValue('PS_COUNTRY_DEFAULT', $id_country);
        Configuration::updateGlobalValue('PS_LOCALE_COUNTRY', $data['shop_country']);
        Configuration::updateGlobalValue('PS_TIMEZONE', $data['shop_timezone']);
        Configuration::updateGlobalValue('PS_CONFIGURATION_AGREMENT', (int)$data['configuration_agrement']);

        // Set mails configuration
        Configuration::updateGlobalValue('PS_MAIL_METHOD', ($data['use_smtp']) ? 2 : 1);
        Configuration::updateGlobalValue('PS_MAIL_SMTP_ENCRYPTION', $data['smtp_encryption']);
        Configuration::updateGlobalValue('PS_MAIL_SMTP_PORT', $data['smtp_port']);

        // Set default rewriting settings
        Configuration::updateGlobalValue('PS_REWRITING_SETTINGS', $data['rewrite_engine']);

        $groups = Group::getGroups((int)Configuration::get('PS_LANG_DEFAULT'));
        $groups_default = Db::getInstance()->executeS('SELECT `name` FROM '._DB_PREFIX_.'configuration WHERE `name` LIKE "PS_%_GROUP" ORDER BY `id_configuration`');
        foreach ($groups_default as &$group_default) {
            if (is_array($group_default) && isset($group_default['name'])) {
                $group_default = $group_default['name'];
            }
        }

        if (is_array($groups) && count($groups)) {
            foreach ($groups as $key => $group) {
                if (Configuration::get($groups_default[$key]) != $groups[$key]['id_group']) {
                    Configuration::updateGlobalValue($groups_default[$key], (int)$groups[$key]['id_group']);
                }
            }
        }

        $states = Db::getInstance()->executeS('SELECT `id_order_state` FROM '._DB_PREFIX_.'order_state ORDER by `id_order_state`');
        $states_default = Db::getInstance()->executeS('SELECT MIN(`id_configuration`), `name` FROM '._DB_PREFIX_.'configuration WHERE `name` LIKE "PS_OS_%" GROUP BY `value` ORDER BY`id_configuration`');

        foreach ($states_default as &$state_default) {
            if (is_array($state_default) && isset($state_default['name'])) {
                $state_default = $state_default['name'];
            }
        }

        if (is_array($states) && count($states)) {
            foreach ($states as $key => $state) {
                if (Configuration::get($states_default[$key]) != $states[$key]['id_order_state']) {
                    Configuration::updateGlobalValue($states_default[$key], (int)$states[$key]['id_order_state']);
                }
            }
            /* deprecated order state */
            Configuration::updateGlobalValue('PS_OS_OUTOFSTOCK_PAID', (int)Configuration::get('PS_OS_OUTOFSTOCK'));
        }

        // Set logo configuration
        if (file_exists(_PS_IMG_DIR_.'logo.png')) {
            list($width, $height) = getimagesize(_PS_IMG_DIR_.'logo.png');
            Configuration::updateGlobalValue('SHOP_LOGO_WIDTH', round($width));
            Configuration::updateGlobalValue('SHOP_LOGO_HEIGHT', round($height));
        }

        // Disable cache for debug mode
        if (_PS_MODE_DEV_) {
            Configuration::updateGlobalValue('PS_SMARTY_CACHE', 1);
        }

        // Active only the country selected by the merchant
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'country SET active = 0 WHERE id_country != '.(int)$id_country);

        // Set localization configuration
        $version = str_replace('.', '', _PS_VERSION_);
        $version = substr($version, 0, 2);
        $localization_file_content = $this->getLocalizationPackContent($version, $data['shop_country']);

        $locale = new LocalizationPack();
        $locale->loadLocalisationPack($localization_file_content, false, true);

        // Create default employee
        if (isset($data['admin_firstname']) && isset($data['admin_lastname']) && isset($data['admin_password']) && isset($data['admin_email'])) {
            $employee = new Employee();
            $employee->firstname = Tools::ucfirst($data['admin_firstname']);
            $employee->lastname = Tools::ucfirst($data['admin_lastname']);
            $employee->email = $data['admin_email'];
            $employee->setWsPasswd($data['admin_password']);
            $employee->last_passwd_gen = date('Y-m-d h:i:s', strtotime('-360 minutes'));
            $employee->bo_theme = 'default';
            $employee->default_tab = 1;
            $employee->active = true;
            $employee->optin = true;
            $employee->id_profile = 1;
            $employee->id_lang = Configuration::get('PS_LANG_DEFAULT');
            $employee->bo_menu = 1;
            if (!$employee->add()) {
                $this->setError($this->translator->trans('Cannot create admin account', array(), 'Install'));
                return false;
            }
        } else {
            $this->setError($this->translator->trans('Cannot create admin account', array(), 'Install'));
            return false;
        }

        // Update default contact
        if (isset($data['admin_email'])) {
            Configuration::updateGlobalValue('PS_SHOP_EMAIL', $data['admin_email']);

            $contacts = new PrestaShopCollection('Contact');
            foreach ($contacts as $contact) {
                $contact->email = $data['admin_email'];
                $contact->update();
            }
        }

        if (!@Tools::generateHtaccess(null, $data['rewrite_engine'])) {
            Configuration::updateGlobalValue('PS_REWRITING_SETTINGS', 0);
        }

        Tools::generateRobotsFile();

        return true;
    }

    public function getModulesList()
    {
        $modules = array();
        if (false) {
            foreach (scandir(_PS_MODULE_DIR_) as $module) {
                if ($module[0] != '.' && is_dir(_PS_MODULE_DIR_.$module) && file_exists(_PS_MODULE_DIR_.$module.'/'.$module.'.php')) {
                    $modules[] = $module;
                }
            }
        } else {
            $modules = array(
                'dashactivity',
                'dashtrends',
                'dashgoals',
                'dashproducts',
                'graphnvd3',
                'gridhtml',
                'ps_banner',
                'ps_categorytree',
                'ps_checkpayment',
                'ps_contactinfo',
                'ps_currencyselector',
                'ps_customeraccountlinks',
                'ps_customersignin',
                'ps_customtext',
                'ps_emailsubscription',
                'ps_facetedsearch',
                'ps_featuredproducts',
                'ps_imageslider',
                'ps_languageselector',
                'ps_linklist',
                'ps_mainmenu',
                'ps_searchbar',
                'ps_sharebuttons',
                'ps_shoppingcart',
                'ps_socialfollow',
                'ps_wirepayment',
                'pagesnotfound',
                'sekeywords',
                'statsbestcategories',
                'statsbestcustomers',
                'statsbestproducts',
                'statsbestsuppliers',
                'statsbestvouchers',
                'statscarrier',
                'statscatalog',
                'statscheckup',
                'statsdata',
                'statsequipment',
                'statsforecast',
                'statslive',
                'statsnewsletter',
                'statsorigin',
                'statspersonalinfos',
                'statsproduct',
                'statsregistrations',
                'statssales',
                'statssearch',
                'statsstock',
                'statsvisits',
                'welcome',
            );
        }
        return $modules;
    }

    public function getAddonsModulesList($params = array())
    {
        /**
         * TODO: Remove blacklist once 1.7 is out.
         */
        $blacklist = array(
            'bankwire',
            'blockadvertising',
            'blockbanner',
            'blockbestsellers',
            'blockcart',
            'blockcategories',
            'blockcms',
            'blockcmsinfo',
            'blockcontact',
            'blockcontactinfos',
            'blockcurrencies',
            'blockcustomerprivacy',
            'blockfacebook',
            'blocklanguages',
            'blocklayered',
            'blocklink',
            'blockmanufacturer',
            'blockmyaccount',
            'blockmyaccountfooter',
            'blocknewproducts',
            'blocknewsletter',
            'blockpaymentlogo',
            'blockpermanentlinks',
            'blockrss',
            'blocksearch',
            'blocksharefb',
            'blocksocial',
            'blockstore',
            'blockspecials',
            'blocksupplier',
            'blocktags',
            'blocktopmenu',
            'blockuserinfo',
            'blockviewed',
            'blockwishlist',
            'cheque',
            'crossselling',
            'homefeatured',
            'homeslider',
            'onboarding',
            'productscategory',
            'productcomments',
            'producttooltip',
            'sendtoafriend',
            'socialsharing',
        );

        $addons_modules = array();
        $content = Tools::addonsRequest('install-modules', $params);
        $xml = @simplexml_load_string($content, null, LIBXML_NOCDATA);

        if ($xml !== false && isset($xml->module)) {
            foreach ($xml->module as $modaddons) {
                if (in_array($modaddons->name, $blacklist)) {
                    continue;
                }
                $addons_modules[] = array('id_module' => $modaddons->id, 'name' => $modaddons->name);
            }
        }

        return $addons_modules;
    }

    /**
     * PROCESS : installModules
     * Download module from addons and Install all modules in ~/modules/ directory
     */
    public function installModulesAddons($module = null)
    {
        $addons_modules = $module ? array($module) : $this->getAddonsModulesList();
        $modules = array();

        foreach ($addons_modules as $addons_module) {
            if (file_put_contents(_PS_MODULE_DIR_.$addons_module['name'].'.zip', Tools::addonsRequest('module', array('id_module' => $addons_module['id_module'])))) {
                if (Tools::ZipExtract(_PS_MODULE_DIR_.$addons_module['name'].'.zip', _PS_MODULE_DIR_)) {
                    $modules[] = (string)$addons_module['name'];//if the module has been unziped we add the name in the modules list to install
                    unlink(_PS_MODULE_DIR_.$addons_module['name'].'.zip');
                }
            }
        }

        return count($modules) ? $this->installModules($modules) : true;
    }

    /**
     * PROCESS : installModules
     * Download module from addons and Install all modules in ~/modules/ directory
     */
    public function installModules($module = null)
    {
        if ($module && !is_array($module)) {
            $module = array($module);
        }

        $modules = $module ? $module : $this->getModulesList();

        Module::updateTranslationsAfterInstall(false);

        $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $moduleManager = $moduleManagerBuilder->build();

        $errors = array();
        foreach ($modules as $module_name) {
            if (!file_exists(_PS_MODULE_DIR_.$module_name.'/'.$module_name.'.php')) {
                continue;
            }

            if (!$moduleManager->install($module_name)) {
                /*$module_errors = $module->getErrors();
                if (empty($module_errors)) {*/
                $module_errors = [$this->translator->trans('Cannot install module "%module%"', array('%module%' => $module_name), 'Install')];
                /*}*/
                $errors[$module_name] = $module_errors;
            }
        }

        if ($errors) {
            $this->setError($errors);
            return false;
        }

        Module::updateTranslationsAfterInstall(true);
        EntityLanguage::updateModulesTranslations($modules);

        return true;
    }

    /**
     * PROCESS : installFixtures
     * Install fixtures (E.g. demo products)
     */
    public function installFixtures($entity = null, array $data = array())
    {
        $fixtures_path = _PS_INSTALL_FIXTURES_PATH_.'fashion/';
        $fixtures_name = 'fashion';
        $zip_file = _PS_ROOT_DIR_.'/download/fixtures.zip';
        $temp_dir = _PS_ROOT_DIR_.'/download/fixtures/';

        // Load class (use fixture class if one exists, or use InstallXmlLoader)
        if (file_exists($fixtures_path.'/install.php')) {
            require_once $fixtures_path.'/install.php';
            $class = 'InstallFixtures'.Tools::toCamelCase($fixtures_name);
            if (!class_exists($class, false)) {
                $this->setError($this->translator->trans('Fixtures class "%class%" not found', array('%class%' => $class), 'Install'));
                return false;
            }

            $xml_loader = new $class();
            if (!$xml_loader instanceof XmlLoader) {
                $this->setError($this->translator->trans('"%class%" must be an instance of "InstallXmlLoader"', array('%class%' => $class), 'Install'));
                return false;
            }
        } else {
            $xml_loader = new XmlLoader();
            $xml_loader->setTranslator($this->translator);
        }

        // Install XML data (data/xml/ folder)
        $xml_loader->setFixturesPath($fixtures_path);
        if (isset($this->xml_loader_ids) && $this->xml_loader_ids) {
            $xml_loader->setIds($this->xml_loader_ids);
        }

        $languages = array();
        foreach (EntityLanguage::getLanguages(false) as $lang) {
            $languages[$lang['id_lang']] = $lang['iso_code'];
        }
        $xml_loader->setLanguages($languages);

        if ($entity) {
            $xml_loader->populateEntity($entity);
        } else {
            $xml_loader->populateFromXmlFiles();
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

        // Index products in search tables
        Search::indexation(true);

        // Update fixtures lang
        foreach ($languages as $lang) {
            \Language::updateMultilangTable($lang);
        }

        return true;
    }

    public function installTheme($themeName = null)
    {
        $themeName = $themeName ?: _THEME_NAME_;
        $builder = new ThemeManagerBuilder(
            Context::getContext(),
            Db::getInstance()
        );

        $theme_manager = $builder->build();

        return $theme_manager->install($themeName) && $theme_manager->enable($themeName);
    }
}
