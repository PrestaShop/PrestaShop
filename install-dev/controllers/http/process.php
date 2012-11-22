<?php
/*
* 2007-2012 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class InstallControllerHttpProcess extends InstallControllerHttp
{
	const SETTINGS_FILE = 'config/settings.inc.php';

	/**
	 * @var InstallModelInstall
	 */
	protected $model_install;
	
	public $process_steps = array();
	
	public $previous_button = false;

	public function init()
	{
		require_once _PS_INSTALL_MODELS_PATH_.'install.php';
		$this->model_install = new InstallModelInstall();
	}

	/**
	 * @see InstallAbstractModel::processNextStep()
	 */
	public function processNextStep()
	{
	}

	/**
	 * @see InstallAbstractModel::validate()
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
		Context::getContext()->country = new Country('PS_COUNTRY_DEFAULT');
		Context::getContext()->cart = new Cart();
		define('_PS_SMARTY_FAST_LOAD_', true);
		require_once _PS_ROOT_DIR_.'/config/smarty.config.inc.php';

		Context::getContext()->smarty = $smarty;
	}

	public function process()
	{
		if (file_exists(_PS_ROOT_DIR_.'/'.self::SETTINGS_FILE))
			@require_once _PS_ROOT_DIR_.'/'.self::SETTINGS_FILE;

		if (!$this->session->process_validated)
			$this->session->process_validated = array();

		if (Tools::getValue('generateSettingsFile'))
			$this->processGenerateSettingsFile();
		else if (Tools::getValue('installDatabase') && !empty($this->session->process_validated['generateSettingsFile']))
			$this->processInstallDatabase();
		else if (Tools::getValue('installDefaultData'))
			$this->processInstallDefaultData();
		else if (Tools::getValue('populateDatabase') && !empty($this->session->process_validated['installDatabase']))
			$this->processPopulateDatabase();
		else if (Tools::getValue('configureShop') && !empty($this->session->process_validated['populateDatabase']))
			$this->processConfigureShop();
		else if (Tools::getValue('installModules') && !empty($this->session->process_validated['configureShop']))
			$this->processInstallModules();
		else if (Tools::getValue('installFixtures') && !empty($this->session->process_validated['installModules']))
			$this->processInstallFixtures();
		else if (Tools::getValue('installTheme') && !empty($this->session->process_validated['installModules']))
			$this->processInstallTheme();
		else if (Tools::getValue('sendEmail') && !empty($this->session->process_validated['installTheme']))
			$this->processSendEmail();
		else
		{
			// With no parameters, we consider that we are doing a new install, so session where the last process step
			// was stored can be cleaned
			if (Tools::getValue('restart'))
				$this->session->process_validated = array();
			else if (!Tools::getValue('submitNext'))
			{
				$this->session->step = 'configure';
				$this->session->last_step = 'configure';
				Tools::redirect('index.php');
			}
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

		if (!$success)
			$this->ajaxJsonAnswer(false);
		$this->session->process_validated = array_merge($this->session->process_validated, array('generateSettingsFile' => true));
		$this->ajaxJsonAnswer(true);
	}

	/**
	 * PROCESS : installDatabase
	 * Create database structure
	 */
	public function processInstallDatabase()
	{
		if (!$this->model_install->installDatabase($this->session->database_clear) || $this->model_install->getErrors())
			$this->ajaxJsonAnswer(false, $this->model_install->getErrors());
		$this->session->process_validated = array_merge($this->session->process_validated, array('installDatabase' => true));
		$this->ajaxJsonAnswer(true);
	}

	/**
	 * PROCESS : installDefaultData
	 * Create default shop and languages
	 */
	public function processInstallDefaultData()
	{
		// @todo remove true in populateDatabase for 1.5.0 RC version
		$result = $this->model_install->installDefaultData($this->session->shop_name, true);

		if (!$result || $this->model_install->getErrors())
			$this->ajaxJsonAnswer(false, $this->model_install->getErrors());
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
		if (!$result || $this->model_install->getErrors())
			$this->ajaxJsonAnswer(false, $this->model_install->getErrors());
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
			'shop_name' =>				$this->session->shop_name,
			'shop_activity' =>			$this->session->shop_activity,
			'shop_country' =>			$this->session->shop_country,
			'shop_timezone' =>			$this->session->shop_timezone,
			'use_smtp' =>				$this->session->use_smtp,
			'smtp_server' =>			$this->session->smtp_server,
			'smtp_login' =>				$this->session->smtp_login,
			'smtp_password' =>			$this->session->smtp_password,
			'smtp_encryption' =>		$this->session->smtp_encryption,
			'smtp_port' =>				$this->session->smtp_port,
			'admin_firstname' =>		$this->session->admin_firstname,
			'admin_lastname' =>			$this->session->admin_lastname,
			'admin_password' =>			$this->session->admin_password,
			'admin_email' =>			$this->session->admin_email,
			'configuration_agrement' =>	$this->session->configuration_agrement,
		));

		if (!$success || $this->model_install->getErrors())
			$this->ajaxJsonAnswer(false, $this->model_install->getErrors());
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

		$result = $this->model_install->installModules(Tools::getValue('module'));
		if (!$result || $this->model_install->getErrors())
			$this->ajaxJsonAnswer(false, $this->model_install->getErrors());
		$this->session->process_validated = array_merge($this->session->process_validated, array('installModules' => true));
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
		if (!$this->model_install->installFixtures(Tools::getValue('entity')) || $this->model_install->getErrors())
			$this->ajaxJsonAnswer(false, $this->model_install->getErrors());
		$this->session->xml_loader_ids = $this->model_install->xml_loader_ids;
		$this->ajaxJsonAnswer(true);
	}

	/**
	 * PROCESS : installTheme
	 * Install theme
	 */
	public function processInstallTheme()
	{
		$this->initializeContext();

		$this->model_install->installTheme();
		if ($this->model_install->getErrors())
			$this->ajaxJsonAnswer(false, $this->model_install->getErrors());

		$this->session->process_validated = array_merge($this->session->process_validated, array('installTheme' => true));
		$this->ajaxJsonAnswer(true);
	}

	/**
	 * PROCESS : sendEmail
	 * Send information e-mail
	 */
	public function processSendEmail()
	{
		require_once _PS_INSTALL_MODELS_PATH_.'mail.php';
		$mail = new InstallModelMail(
			$this->session->use_smtp,
			$this->session->smtp_server,
			$this->session->smtp_login,
			$this->session->smtp_password,
			$this->session->smtp_port,
			$this->session->smtp_encryption,
			$this->session->admin_email
		);

		if (file_exists(_PS_INSTALL_LANGS_PATH_.$this->language->getLanguageIso().'/mail_identifiers.txt'))
			$content = file_get_contents(_PS_INSTALL_LANGS_PATH_.$this->language->getLanguageIso().'/mail_identifiers.txt');
		else
			$content = file_get_contents(_PS_INSTALL_LANGS_PATH_.InstallLanguages::DEFAULT_ISO.'/mail_identifiers.txt');

		$vars = array(
			'{firstname}' => $this->session->admin_firstname,
			'{lastname}' => $this->session->admin_lastname,
			'{shop_name}' => $this->session->shop_name,
			'{passwd}' => $this->session->admin_password,
			'{email}' => $this->session->admin_email,
			'{shop_url}' => Tools::getHttpHost(true).__PS_BASE_URI__,
		);
		$content = str_replace(array_keys($vars), array_values($vars), $content);

		$mail->send(
			$this->l('%s - Login information', $this->session->shop_name),
			$content
		);

		// If last step is fine, we store the fact PrestaShop is installed
		$this->session->last_step = 'configure';
		$this->session->step = 'configure';

		$this->ajaxJsonAnswer(true);
	}

	/**
	 * @see InstallAbstractModel::display()
	 */
	public function display()
	{
		// The installer SHOULD take less than 32M, but may take up to 35/36M sometimes. So 42M is a good value :)
		$low_memory = Tools::getMemoryLimit() < Tools::getOctets('42M');

		// We fill the process step used for Ajax queries
		$this->process_steps[] = array('key' => 'generateSettingsFile', 'lang' => $this->l('Create settings.inc file'));
		$this->process_steps[] = array('key' => 'installDatabase', 'lang' => $this->l('Create database tables'));
		$this->process_steps[] = array('key' => 'installDefaultData', 'lang' => $this->l('Create default shop and languages'));

		// If low memory, create subtasks for populateDatabase step (entity per entity)
		$populate_step = array('key' => 'populateDatabase', 'lang' => $this->l('Populate database tables'));
		if ($low_memory)
		{
			$populate_step['subtasks'] = array();
			$xml_loader = new InstallXmlLoader();
			foreach ($xml_loader->getSortedEntities() as $entity)
				$populate_step['subtasks'][] = array('entity' => $entity);
		}

		$this->process_steps[] = $populate_step;
		$this->process_steps[] = array('key' => 'configureShop', 'lang' => $this->l('Configure shop information'));


		$install_modules = array('key' => 'installModules', 'lang' => $this->l('Install modules'));
		if ($low_memory)
			foreach ($this->model_install->getModulesList() as $module)
				$install_modules['subtasks'][] = array('module' => $module);
		$this->process_steps[] = $install_modules;

		// Fixtures are installed only if option is selected
		if ($this->session->install_type == 'full')
		{
			// If low memory, create subtasks for installFixtures step (entity per entity)
			$fixtures_step = array('key' => 'installFixtures', 'lang' => $this->l('Install demonstration data'));
			if ($low_memory)
			{
				$fixtures_step['subtasks'] = array();
				$xml_loader = new InstallXmlLoader();
				$xml_loader->setFixturesPath();
				foreach ($xml_loader->getSortedEntities() as $entity)
					$fixtures_step['subtasks'][] = array('entity' => $entity);
			}
			$this->process_steps[] = $fixtures_step;
		}

		$this->process_steps[] = array('key' => 'installTheme', 'lang' => $this->l('Install theme'));

		// Mail is send only if option is selected
		if ($this->session->send_informations)
			$this->process_steps[] = array('key' => 'sendEmail', 'lang' => $this->l('Send information e-mail'));

		$this->displayTemplate('process');
	}
}

