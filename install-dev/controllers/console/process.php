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

use PrestaShopBundle\Install\Database;
use PrestaShopBundle\Install\Install;
use Symfony\Component\Filesystem\Filesystem;

class InstallControllerConsoleProcess extends InstallControllerConsole implements HttpConfigureInterface
{
    public $process_steps = [];
    public $previous_button = false;

    /**
     * @var Install
     */
    protected $model_install;

    /**
     * @var Database
     */
    protected $model_database;

    public function init()
    {
        $this->model_install = new Install();
        $this->model_install->setTranslator($this->translator);

        $this->model_database = new Database();
        $this->model_database->setTranslator($this->translator);
    }

    /**
     * @see HttpConfigureInterface::processNextStep()
     */
    public function processNextStep()
    {
    }

    public function display()
    {
    }

    /**
     * @see HttpConfigureInterface::validate()
     */
    public function validate()
    {
        return false;
    }

    public function initializeContext()
    {
        global $smarty;

        // Clean all cache values
        Cache::clean('*');

        Configuration::set('PS_SHOP_DEFAULT', 1);
        Shop::initialize();
        Context::getContext()->shop = new Shop(1);
        Shop::setContext(Shop::CONTEXT_SHOP, 1);
        Configuration::loadConfiguration();
        if (!isset(Context::getContext()->language) || !Validate::isLoadedObject(Context::getContext()->language)) {
            if ($id_lang = (int) Configuration::get('PS_LANG_DEFAULT')) {
                Context::getContext()->language = new Language($id_lang);
            }
        }
        if (!isset(Context::getContext()->country) || !Validate::isLoadedObject(Context::getContext()->country)) {
            if ($id_country = (int) Configuration::get('PS_COUNTRY_DEFAULT')) {
                Context::getContext()->country = new Country((int) $id_country);
            }
        }
        if (!isset(Context::getContext()->currency) || !Validate::isLoadedObject(Context::getContext()->currency)) {
            if ($id_currency = (int) Configuration::get('PS_CURRENCY_DEFAULT')) {
                Context::getContext()->currency = new Currency((int) $id_currency);
            }
        }

        Context::getContext()->cart = new Cart();
        Context::getContext()->employee = new Employee(1);
        if (!defined('_PS_SMARTY_FAST_LOAD_')) {
            define('_PS_SMARTY_FAST_LOAD_', true);
        }
        require_once _PS_ROOT_DIR_ . '/config/smarty.config.inc.php';

        Context::getContext()->smarty = $smarty;
    }

    public function process()
    {
        /* avoid exceptions on re-installation */
        $this->clearConfigXML() && $this->clearConfigThemes();
        $steps = explode(',', $this->datas->step);
        if (in_array('all', $steps)) {
            $steps = ['database', 'modules', 'theme', 'fixtures', 'postInstall'];
        }

        if (in_array('database', $steps)) {
            if (!$this->processGenerateSettingsFile()) {
                $this->printErrors();
            }

            if ($this->datas->database_create) {
                $this->model_database->createDatabase($this->datas->database_server, $this->datas->database_name, $this->datas->database_login, $this->datas->database_password);
            }

            if (!$this->model_database->testDatabaseSettings(
                $this->datas->database_server,
                $this->datas->database_name,
                $this->datas->database_login,
                $this->datas->database_password,
                $this->datas->database_prefix,
                $this->datas->database_clear
            )) {
                $this->printErrors();
            }

            // Deferred Kernel Init
            $this->initKernel();

            if (!$this->processInstallDatabase()) {
                $this->printErrors();
            }

            if (!$this->processInstallDefaultData()) {
                $this->printErrors();
            }
            if (!$this->processPopulateDatabase()) {
                $this->printErrors();
            }

            if (!$this->processConfigureShop()) {
                $this->printErrors();
            }
        }

        if (in_array('theme', $steps)) {
            if (!$this->processInstallTheme()) {
                $this->printErrors();
            }
        }

        if (in_array('modules', $steps)) {
            if (!$this->processInstallModules()) {
                $this->printErrors();
            }
        }

        if (in_array('fixtures', $steps) && $this->datas->fixtures) {
            if (!$this->processInstallFixtures()) {
                $this->printErrors();
            }
        }

        if (in_array('postInstall', $steps)) {
            if (!$this->processPostInstall()) {
                $this->printErrors();
            }
        }

        // Update fixtures lang
        $this->rebootWithoutTranslationsCache();
        foreach (Language::getLanguages() as $lang) {
            Language::updateMultilangTable($lang['iso_code']);
        }
    }

    /**
     * PROCESS : generateSettingsFile
     */
    public function processGenerateSettingsFile()
    {
        return $this->model_install->generateSettingsFile(
            $this->datas->database_server,
            $this->datas->database_login,
            $this->datas->database_password,
            $this->datas->database_name,
            $this->datas->database_prefix,
            $this->datas->database_engine
        );
    }

    /**
     * PROCESS : installDatabase
     * Create database structure
     */
    public function processInstallDatabase()
    {
        return $this->model_install->installDatabase($this->datas->database_clear);
    }

    /**
     * PROCESS : installDefaultData
     * Create default shop and languages
     */
    public function processInstallDefaultData()
    {
        $this->initializeContext();
        if (!$res = $this->model_install->installDefaultData($this->datas->shop_name, $this->datas->shop_country, (int) $this->datas->all_languages, true)) {
            return false;
        }

        if ($this->datas->base_uri != '/') {
            $shop_url = new ShopUrl(1);
            $shop_url->physical_uri = $this->datas->base_uri;
            $shop_url->save();
        }

        return $res;
    }

    /**
     * PROCESS : populateDatabase
     * Populate database with default data
     */
    public function processPopulateDatabase()
    {
        $this->initializeContext();

        $this->model_install->xml_loader_ids = $this->datas->xml_loader_ids;
        $result = $this->model_install->populateDatabase();
        $this->datas->xml_loader_ids = $this->model_install->xml_loader_ids;
        Configuration::updateValue('PS_INSTALL_XML_LOADERS_ID', json_encode($this->datas->xml_loader_ids));

        return $result;
    }

    /**
     * PROCESS : configureShop
     * Set default shop configuration
     */
    public function processConfigureShop()
    {
        $this->initializeContext();

        return $this->model_install->configureShop([
            'shop_name' => $this->datas->shop_name,
            'shop_activity' => $this->datas->shop_activity,
            'shop_country' => $this->datas->shop_country,
            'shop_timezone' => $this->datas->timezone,
            'use_smtp' => false,
            'admin_firstname' => $this->datas->admin_firstname,
            'admin_lastname' => $this->datas->admin_lastname,
            'admin_password' => $this->datas->admin_password,
            'admin_email' => $this->datas->admin_email,
            'configuration_agrement' => true,
            'enable_ssl' => $this->datas->enable_ssl,
            'rewrite_engine' => $this->datas->rewrite_engine,
        ]);
    }

    /**
     * PROCESS : installModules
     * Install all modules in ~/modules/ directory
     */
    public function processInstallModules()
    {
        $this->initializeContext();
        if (is_string($this->datas->modules)) {
            $modules = explode(',', $this->datas->modules);
        } else {
            $modules = array_keys($this->model_install->getModulesOnDisk());
        }

        return $this->model_install->installModules($modules);
    }

    /**
     * PROCESS : installFixtures
     * Install fixtures (E.g. demo products)
     */
    public function processInstallFixtures()
    {
        $this->initializeContext();

        if ((!$this->datas->xml_loader_ids || !is_array($this->datas->xml_loader_ids)) && ($xml_ids = json_decode(Configuration::get('PS_INSTALL_XML_LOADERS_ID'), true))) {
            $this->datas->xml_loader_ids = $xml_ids;
        }

        $this->model_install->xml_loader_ids = $this->datas->xml_loader_ids;
        $result = $this->model_install->installFixtures(null, ['shop_activity' => $this->datas->shop_activity, 'shop_country' => $this->datas->shop_country]);
        $this->datas->xml_loader_ids = $this->model_install->xml_loader_ids;

        return $result;
    }

    /**
     * Process post install execution
     */
    public function processPostInstall(): bool
    {
        return $this->model_install->postInstall();
    }

    /**
     * PROCESS : installTheme
     * Install theme
     */
    public function processInstallTheme()
    {
        $this->initializeContext();

        return $this->model_install->installTheme($this->datas->theme);
    }

    private function clearConfigXML()
    {
        $configXMLPath = _PS_ROOT_DIR_ . '/config/xml/';
        $cacheFiles = scandir($configXMLPath, SCANDIR_SORT_NONE);
        $excludes = ['.htaccess', 'index.php'];

        foreach ($cacheFiles as $file) {
            $filepath = $configXMLPath . $file;
            if (is_file($filepath) && !in_array($file, $excludes)) {
                unlink($filepath);
            }
        }
    }

    private function clearConfigThemes()
    {
        $themesPath = _PS_ROOT_DIR_ . '/config/themes/';
        $cacheFiles = scandir($themesPath, SCANDIR_SORT_NONE);
        foreach ($cacheFiles as $file) {
            $file = $themesPath . $file;
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Deferred initialization of Symfony Kernel
     */
    private function initKernel()
    {
        require_once _PS_CORE_DIR_ . '/config/bootstrap.php';

        global $kernel;
        $kernel = new AppKernel(_PS_ENV_, _PS_MODE_DEV_);
        $kernel->boot();
    }

    /**
     * Delete translations cache and reboot the kernel so newly installed languages are took into account
     *
     * This method is only useful in CLI as everything is done in a single call but not with the web ui
     * because the whole cache gets cleared before translating the fixtures
     */
    private function rebootWithoutTranslationsCache()
    {
        global $kernel;
        (new Filesystem())->remove($kernel->getCacheDir() . 'translations');
        $kernel->reboot($kernel->getCacheDir());
    }
}
