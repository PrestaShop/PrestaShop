<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use PrestaShopBundle\Install\Install;
use PrestaShopBundle\Install\XmlLoader;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\Context\ContextBuilderPreparer;

class InstallControllerHttpProcess extends InstallControllerHttp implements HttpConfigureInterface
{
    /** @var Install */
    protected $model_install;
    public $process_steps = [];
    public $previous_button = false;

    public function init(): void
    {
        $this->model_install = new Install();
        $this->model_install->setTranslator($this->translator);
    }

    /**
     * @see HttpConfigureInterface::processNextStep()
     */
    public function processNextStep(): void
    {
    }

    /**
     * @see HttpConfigureInterface::validate()
     */
    public function validate(): bool
    {
        return false;
    }

    public function initializeContext()
    {
        global $smarty;

        Context::getContext()->shop = new Shop(1);
        Shop::setContext(Shop::CONTEXT_SHOP, 1);
        Configuration::loadConfiguration();
        Context::getContext()->language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        Context::getContext()->country = new Country((int) Configuration::get('PS_COUNTRY_DEFAULT'));
        Context::getContext()->currency = Currency::getDefaultCurrency();
        Context::getContext()->cart = new Cart();
        Context::getContext()->employee = new Employee(1);
        define('_PS_SMARTY_FAST_LOAD_', true);
        require_once _PS_ROOT_DIR_ . '/config/smarty.config.inc.php';

        Context::getContext()->smarty = $smarty;

        $container = SymfonyContainer::getInstance();
        /** @var ContextBuilderPreparer $preparer */
        $preparer = $container->get(ContextBuilderPreparer::class);
        $preparer->prepareFromLegacyContext(Context::getContext());
    }

    public function process(): void
    {
        /* avoid exceptions on re-installation */
        $this->clearConfigXML() && $this->clearConfigThemes();

        if (!$this->session->process_validated) {
            $this->session->process_validated = [];
        }

        try {
            $validateFixturesInstallation = $this->session->content_install_fixtures;
            $fixturesInstalled = !empty($this->session->process_validated['installFixtures']);

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
                    ->setLanguageCode($this->session->lang)
                    ->setProcessFOThemes([Theme::getDefaultTheme()])
                    ->process();
                $this->processConfigureShop();
            } elseif (Tools::getValue('installModules') && (!empty($this->session->process_validated['configureShop']) || !$validateFixturesInstallation)) {
                $this->processInstallModules();
            } elseif (Tools::getValue('installTheme') && !empty($this->session->process_validated['installModules'])) {
                $this->processInstallTheme();
            } elseif (Tools::getValue('installFixtures') && !empty($this->session->process_validated['installTheme'])) {
                $this->processInstallFixtures();
            } elseif (Tools::getValue('postInstall') && (!$validateFixturesInstallation || $fixturesInstalled)) {
                $this->processPostInstall();
            } elseif (Tools::getValue('finalize') && !empty($this->session->process_validated['postInstall'])) {
                $this->processFinalize();
            }
        } catch (\Exception $e) {
            if (_PS_MODE_DEV_) {
                // display stack trace
                $message = (string) $e;
            } else {
                // log stack trace and display message
                $this->model_install->setError((string) $e);
                $message = $e->getMessage();
            }
            $this->ajaxJsonAnswer(false, $message);
        }

        // With no parameters, we consider that we are doing a new install, so session where the last process step
        // was stored can be cleaned
        if (Tools::getValue('restart')) {
            $this->session->process_validated = [];
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
        // Set the install lock file
        file_put_contents(PS_INSTALLATION_LOCK_FILE, '1');

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
        $this->session->process_validated = array_merge($this->session->process_validated, ['generateSettingsFile' => true]);
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
        $this->session->process_validated = array_merge($this->session->process_validated, ['installDatabase' => true]);
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
        $this->session->process_validated = array_merge($this->session->process_validated, ['populateDatabase' => true]);
        $this->ajaxJsonAnswer(true);
    }

    /**
     * PROCESS : configureShop
     * Set default shop configuration
     */
    public function processConfigureShop()
    {
        $this->initializeContext();

        $success = $this->model_install->configureShop([
            'shop_name' => $this->session->shop_name,
            'shop_country' => $this->session->shop_country,
            'shop_timezone' => $this->session->shop_timezone,
            'admin_firstname' => $this->session->admin_firstname,
            'admin_lastname' => $this->session->admin_lastname,
            'admin_password' => $this->session->admin_password,
            'admin_email' => $this->session->admin_email,
            'configuration_agrement' => $this->session->configuration_agrement,
            'enable_ssl' => $this->session->enable_ssl,
            'rewrite_engine' => $this->session->rewrite_engine,
        ]);

        if (!$success || $this->model_install->getErrors()) {
            $this->ajaxJsonAnswer(false, $this->model_install->getErrors());
        }

        $this->session->process_validated = array_merge($this->session->process_validated, ['configureShop' => true]);
        $this->ajaxJsonAnswer(true);
    }

    /**
     * PROCESS : installModules
     * Install all modules in ~/modules/ directory
     */
    public function processInstallModules()
    {
        $this->initializeContext();

        $result = $this->model_install->installModules($this->session->content_modules);
        if (!$result || $this->model_install->getErrors()) {
            $this->ajaxJsonAnswer(false, $this->model_install->getErrors());
        }

        $this->session->process_validated = array_merge($this->session->process_validated, ['installModules' => true]);
        $this->ajaxJsonAnswer(true);
    }

    /**
     * PROCESS: Post installation execution
     */
    public function processPostInstall(): void
    {
        $this->initializeContext();

        $result = $this->model_install->postInstall();
        if (!$result || $this->model_install->getErrors()) {
            $this->ajaxJsonAnswer(false, $this->model_install->getErrors());
        }

        $this->session->process_validated = array_merge($this->session->process_validated, ['postInstall' => true]);
        $this->ajaxJsonAnswer(true);
    }

    public function processFinalize(): void
    {
        $this->initializeContext();
        $result = $this->model_install->finalize($this->session->adminFolderName);

        if (!$result || $this->model_install->getErrors()) {
            $this->ajaxJsonAnswer(false, $this->model_install->getErrors());
        }

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
        if (!$this->model_install->installFixtures(Tools::getValue('entity', null), ['shop_country' => $this->session->shop_country]) || $this->model_install->getErrors()) {
            $this->ajaxJsonAnswer(false, $this->model_install->getErrors());
        }
        $this->session->xml_loader_ids = $this->model_install->xml_loader_ids;
        $this->session->process_validated = array_merge($this->session->process_validated, ['installFixtures' => true]);

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
        $this->model_install->installTheme($this->session->content_theme);
        if ($this->model_install->getErrors()) {
            $this->ajaxJsonAnswer(false, $this->model_install->getErrors());
        }
        $this->session->process_validated = array_merge($this->session->process_validated, ['installTheme' => true]);
        $warning = '';
        if (!empty($this->model_install->getWarnings())) {
            $warning = implode('<br />', $this->model_install->getWarnings());
            $this->model_install->resetWarnings();
        }
        $this->ajaxJsonAnswer(true, '', $warning);
    }

    /**
     * @see HttpConfigureInterface::display()
     */
    public function display(): void
    {
        // We fill the process step used for Ajax queries
        $this->process_steps[] = ['key' => 'generateSettingsFile', 'lang' => $this->translator->trans('Create file parameters', [], 'Install')];
        $this->process_steps[] = ['key' => 'installDatabase', 'lang' => $this->translator->trans('Create database tables', [], 'Install')];
        $this->process_steps[] = ['key' => 'installDefaultData', 'lang' => $this->translator->trans('Create default shop and languages', [], 'Install')];

        $this->process_steps[] = ['key' => 'populateDatabase', 'lang' => $this->translator->trans('Populate database tables', [], 'Install')];
        $this->process_steps[] = ['key' => 'configureShop', 'lang' => $this->translator->trans('Configure shop information', [], 'Install')];

        // We need to install modules first, then enable the theme which may in turn enable/disable some modules
        $this->process_steps[] = ['key' => 'installModules', 'lang' => $this->translator->trans('Install modules', [], 'Install')];
        $this->process_steps[] = ['key' => 'installTheme', 'lang' => $this->translator->trans('Install theme', [], 'Install')];

        if ($this->session->content_install_fixtures) {
            $fixtures_step = ['key' => 'installFixtures', 'lang' => $this->translator->trans('Install demonstration data', [], 'Install')];
            if ($this->hasLargeFixtures()) {
                $fixtures_step['subtasks'] = [];
                $xml_loader = new XmlLoader();
                $xml_loader->setTranslator($this->translator);
                $xml_loader->setFixturesPath();

                foreach ($xml_loader->getSortedEntities() as $entity) {
                    $fixtures_step['subtasks'][] = ['entity' => $entity];
                }
            }
            $this->process_steps[] = $fixtures_step;
        }

        $this->process_steps[] = ['key' => 'postInstall', 'lang' => $this->translator->trans('Post installation scripts', [], 'Install')];
        $this->process_steps[] = ['key' => 'finalize', 'lang' => $this->translator->trans('Finalization', [], 'Install')];

        $this->displayContent('process');
    }

    /**
     * Check if the fixtures directory is large
     *
     * return bool
     */
    private function hasLargeFixtures()
    {
        $size = 0;
        $fixtureDir = _PS_INSTALL_FIXTURES_PATH_ . 'fashion/data/';
        $dh = opendir($fixtureDir);
        if ($dh) {
            while (($xmlFile = readdir($dh)) !== false) {
                $size += filesize($fixtureDir . $xmlFile);
            }
            closedir($dh);
        }

        return $size > Tools::getOctets('10M');
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
}
