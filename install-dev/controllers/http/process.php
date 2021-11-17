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

use PrestaShopBundle\Install\Install;
use PrestaShopBundle\Install\XmlLoader;

class InstallControllerHttpProcess extends InstallControllerHttp implements HttpConfigureInterface
{
    /** @var  Install */
    protected $model_install;
    public $process_steps = array();
    public $previous_button = false;

    public function init()
    {
        $this->model_install = new Install();
        $this->model_install->setTranslator($this->translator);
    }

    /**
     * @see HttpConfigureInterface::processNextStep()
     */
    public function processNextStep()
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

        Context::getContext()->shop = new Shop(1);
        Shop::setContext(Shop::CONTEXT_SHOP, 1);
        Configuration::loadConfiguration();
        Context::getContext()->language = new Language(Configuration::get('PS_LANG_DEFAULT'));
        Context::getContext()->country = new Country(Configuration::get('PS_COUNTRY_DEFAULT'));
        Context::getContext()->currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        Context::getContext()->cart = new Cart();
        Context::getContext()->employee = new Employee(1);
        define('_PS_SMARTY_FAST_LOAD_', true);
        require_once _PS_ROOT_DIR_.'/config/smarty.config.inc.php';

        Context::getContext()->smarty = $smarty;
    }

    public function process()
    {
        /* avoid exceptions on re-installation */
        $this->clearConfigXML() && $this->clearConfigThemes();

        if (!$this->session->process_validated) {
            $this->session->process_validated = array();
        }

        try {
            if (Tools::getValue('generateSettingsFile')) {
                $this->processGenerateSettingsFile();
            } elseif (Tools::getValue('installDatabase') && !empty($this->session->process_validated['generateSettingsFile'])) {
                $this->processInstallDatabase();
            } elseif (Tools::getValue('installDefaultData')) {
                $this->processInstallDefaultData();
            } elseif (Tools::getValue('populateDatabase') && !empty($this->session->process_validated['installDatabase'])) {
                $this->processPopulateDatabase();
            } elseif (Tools::getValue('configureShop') && !empty($this->session->process_validated['populateDatabase'])) {
                Language::getRtlStylesheetProcessor()
                    ->setIsInstall(true)
                    ->setLanguageCode($this->session->lang)
                    ->setProcessFOThemes(array('classic'))
                    ->process();
                $this->processConfigureShop();
            } elseif (Tools::getValue('installModules') && (!empty($this->session->process_validated['configureShop']) || $this->session->install_type != 'full')) {
                $this->processInstallModules();
            } elseif (Tools::getValue('installModulesAddons') && !empty($this->session->process_validated['installModules'])) {
                $this->processInstallAddonsModules();
            } elseif (Tools::getValue('installTheme') && !empty($this->session->process_validated['installModulesAddons'])) {
                $this->processInstallTheme();
            } elseif (Tools::getValue('installFixtures') && !empty($this->session->process_validated['installTheme'])) {
                $this->processInstallFixtures();
            }
        } catch (\Exception $e) {
            $this->ajaxJsonAnswer(false, $e->getMessage());
        }

        // With no parameters, we consider that we are doing a new install, so session where the last process step
        // was stored can be cleaned
        if (Tools::getValue('restart')) {
            $this->session->process_validated = array();
            $this->session->database_clear = true;
        } elseif (!Tools::getValue('submitNext')) {
            $this->session->step = 'configure';
            $this->session->last_step = 'configure';
            Tools::redirect('index.php');
        }
    }

    /**
     * PROCESS : generateSettingsFile
     */
    public function processGenerateSettingsFile()
    {
        $success = $this->model_install->generateSettingsFile(
            $this->session->database_server,
            $this->session->database_login,
            $this->session->database_password,
            $this->session->database_name,
            $this->session->database_prefix,
            $this->session->database_engine
        );

        if (!$success) {
            $this->ajaxJsonAnswer(false);
        }
        $this->session->process_validated = array_merge($this->session->process_validated, array('generateSettingsFile' => true));
        $this->ajaxJsonAnswer(true);
    }

    /**
     * PROCESS : installDatabase
     * Create database structure
     */
    public function processInstallDatabase()
    {
        if (!$this->model_install->installDatabase($this->session->database_clear) || $this->model_install->getErrors()) {
            $this->ajaxJsonAnswer(false, $this->model_install->getErrors());
        }
        $this->session->process_validated = array_merge($this->session->process_validated, array('installDatabase' => true));
        $this->ajaxJsonAnswer(true);
    }

    /**
     * PROCESS : installDefaultData
     * Create default shop and languages
     */
    public function processInstallDefaultData()
    {
        $result = $this->model_install->installDefaultData($this->session->shop_name, $this->session->shop_country, false, true);

        if (!$result || $this->model_install->getErrors()) {
            $this->ajaxJsonAnswer(false, $this->model_install->getErrors());
        }
        $this->ajaxJsonAnswer(true);
    }

    /**
     * PROCESS : populateDatabase
     * Populate database with default data
     */
    public function processPopulateDatabase()
    {
        $this->initializeContext();

        $this->model_install->xml_loader_ids = $this->session->xml_loader_ids;
        $result = $this->model_install->populateDatabase(Tools::getValue('entity'));
        if (!$result || $this->model_install->getErrors()) {
            $this->ajaxJsonAnswer(false, $this->model_install->getErrors());
        }
        $this->session->xml_loader_ids = $this->model_install->xml_loader_ids;
        $this->session->process_validated = array_merge($this->session->process_validated, array('populateDatabase' => true));
        $this->ajaxJsonAnswer(true);
    }

    /**
     * PROCESS : configureShop
     * Set default shop configuration
     */
    public function processConfigureShop()
    {
        $this->initializeContext();

        $success = $this->model_install->configureShop(array(
            'shop_name' =>              $this->session->shop_name,
            'shop_activity' =>          $this->session->shop_activity,
            'shop_country' =>           $this->session->shop_country,
            'shop_timezone' =>          $this->session->shop_timezone,
            'admin_firstname' =>        $this->session->admin_firstname,
            'admin_lastname' =>         $this->session->admin_lastname,
            'admin_password' =>         $this->session->admin_password,
            'admin_email' =>            $this->session->admin_email,
            'configuration_agrement' => $this->session->configuration_agrement,
            'enable_ssl' =>             $this->session->enable_ssl,
            'rewrite_engine' =>         $this->session->rewrite_engine,
        ));

        if (!$success || $this->model_install->getErrors()) {
            $this->ajaxJsonAnswer(false, $this->model_install->getErrors());
        }

        $this->session->process_validated = array_merge($this->session->process_validated, array('configureShop' => true));
        $this->ajaxJsonAnswer(true);
    }

    /**
     * PROCESS : installModules
     * Install all modules in ~/modules/ directory
     */
    public function processInstallModules()
    {
        $this->initializeContext();

        $result = $this->model_install->installModules(Tools::getValue('module', null));
        if (!$result || $this->model_install->getErrors()) {
            $this->ajaxJsonAnswer(false, $this->model_install->getErrors());
        }
        $this->session->process_validated = array_merge($this->session->process_validated, array('installModules' => true));
        $this->ajaxJsonAnswer(true);
    }

    /**
     * PROCESS : installModulesAddons
     * Install modules from addons
     */
    public function processInstallAddonsModules()
    {
        $this->initializeContext();
        if (($module = Tools::getValue('module')) && $id_module = Tools::getValue('id_module')) {
            $result = $this->model_install->installModulesAddons(array('name' => $module, 'id_module' => $id_module));
        } else {
            $result = $this->model_install->installModulesAddons();
        }
        if (!$result || $this->model_install->getErrors()) {
            $this->ajaxJsonAnswer(false, $this->model_install->getErrors());
        }
        $this->session->process_validated = array_merge($this->session->process_validated, array('installModulesAddons' => true));
        $this->ajaxJsonAnswer(true);
    }

    /**
     * PROCESS : installFixtures
     * Install fixtures (E.g. demo products)
     */
    public function processInstallFixtures()
    {
        $this->initializeContext();

        $this->model_install->xml_loader_ids = $this->session->xml_loader_ids;
        if (!$this->model_install->installFixtures(Tools::getValue('entity', null), array('shop_activity' => $this->session->shop_activity, 'shop_country' => $this->session->shop_country)) || $this->model_install->getErrors()) {
            $this->ajaxJsonAnswer(false, $this->model_install->getErrors());
        }
        $this->session->xml_loader_ids = $this->model_install->xml_loader_ids;
        $this->session->process_validated = array_merge($this->session->process_validated, array('installFixtures' => true));

        $this->ajaxJsonAnswer(true);
    }

    /**
     * PROCESS : installTheme
     * Install theme
     */
    public function processInstallTheme()
    {
        $this->initializeContext();
        Search::indexation(true);
        $this->model_install->installTheme();
        if ($this->model_install->getErrors()) {
            $this->ajaxJsonAnswer(false, $this->model_install->getErrors());
        }
        $this->session->process_validated = array_merge($this->session->process_validated, array('installTheme' => true));
        $this->ajaxJsonAnswer(true);
    }

    /**
     * @see HttpConfigureInterface::display()
     */
    public function display()
    {
        $memoryLimit = Tools::getMemoryLimit();
        // We fill the process step used for Ajax queries
        $this->process_steps[] = array('key' => 'generateSettingsFile', 'lang' => $this->translator->trans('Create file parameters', array(), 'Install'));
        $this->process_steps[] = array('key' => 'installDatabase', 'lang' => $this->translator->trans('Create database tables', array(), 'Install'));
        $this->process_steps[] = array('key' => 'installDefaultData', 'lang' => $this->translator->trans('Create default shop and languages', array(), 'Install'));

        $this->process_steps[] = array('key' => 'populateDatabase', 'lang' => $this->translator->trans('Populate database tables', array(), 'Install'));
        $this->process_steps[] = array('key' => 'configureShop', 'lang' => $this->translator->trans('Configure shop information', array(), 'Install'));

        $this->process_steps[] = array('key' => 'installModules', 'lang' => $this->translator->trans('Install modules', array(), 'Install'));
        $this->process_steps[] = array('key' => 'installModulesAddons', 'lang' => $this->translator->trans('Install Addons modules', array(), 'Install'));

        $this->process_steps[] = array('key' => 'installTheme', 'lang' => $this->translator->trans('Install theme', array(), 'Install'));

        if ($this->session->install_type == 'full') {
            $fixtures_step = array('key' => 'installFixtures', 'lang' => $this->translator->trans('Install demonstration data', array(), 'Install'));
            if ($this->hasLargeFixtures()) {
                $fixtures_step['subtasks'] = array();
                $xml_loader = new XmlLoader();
                $xml_loader->setTranslator($this->translator);
                $xml_loader->setFixturesPath();

                foreach ($xml_loader->getSortedEntities() as $entity) {
                    $fixtures_step['subtasks'][] = array('entity' => $entity);
                }
            }
            $this->process_steps[] = $fixtures_step;
        }

        $this->displayTemplate('process');
    }

    /**
     * Check if the fixtures directory is large
     *
     * return bool
     */
    private function hasLargeFixtures()
    {
        $size = 0;
        $fixtureDir = _PS_INSTALL_FIXTURES_PATH_.'fashion/data/';
        $dh = opendir($fixtureDir);
        if ($dh) {
            while (($xmlFile = readdir($dh)) !== false) {
                $size += filesize($fixtureDir.$xmlFile);
            }
            closedir($dh);
        }

        return $size > Tools::getOctets('10M');
    }

    private function clearConfigXML()
    {
        $configXMLPath = _PS_ROOT_DIR_.'/config/xml/';
        $cacheFiles = scandir($configXMLPath, SCANDIR_SORT_NONE);
        $excludes = ['.htaccess', 'index.php'];

        foreach ($cacheFiles as $file) {
            $filepath = $configXMLPath.$file;
            if (is_file($filepath) && !in_array($file, $excludes)) {
                unlink($filepath);
            }
        }
    }

    private function clearConfigThemes()
    {
        $themesPath = _PS_ROOT_DIR_.'/config/themes/';
        $cacheFiles = scandir($themesPath, SCANDIR_SORT_NONE);
        foreach ($cacheFiles as $file) {
            $file = $themesPath.$file;
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
