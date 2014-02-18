<?php
/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class InstallControllerConsoleProcess extends InstallControllerConsole
{
	const SETTINGS_FILE = 'config/settings.inc.php';

	protected $model_install;
	public $process_steps = array();
	public $previous_button = false;

	public function init()
	{
		require_once _PS_INSTALL_MODELS_PATH_.'install.php';
		require_once _PS_INSTALL_MODELS_PATH_.'database.php';
		$this->model_install = new InstallModelInstall();
		$this->model_database = new InstallModelDatabase();
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

		// Clean all cache values
		Cache::clean('*');

		Context::getContext()->shop = new Shop(1);
		Shop::setContext(Shop::CONTEXT_SHOP, 1);
		Configuration::loadConfiguration();
		if (!isset(Context::getContext()->language) || !Validate::isLoadedObject(Context::getContext()->language))
			if ($id_lang = (int)Configuration::get('PS_LANG_DEFAULT'))
				Context::getContext()->language = new Language($id_lang);
		if (!isset(Context::getContext()->country) || !Validate::isLoadedObject(Context::getContext()->country))
			if ($id_country = (int)Configuration::get('PS_COUNTRY_DEFAULT'))
				Context::getContext()->country = new Country((int)$id_country);

		Context::getContext()->cart = new Cart();
		Context::getContext()->employee = new Employee(1);
		if (!defined('_PS_SMARTY_FAST_LOAD_'))
			define('_PS_SMARTY_FAST_LOAD_', true);
		require_once _PS_ROOT_DIR_.'/config/smarty.config.inc.php';

		Context::getContext()->smarty = $smarty;
	}

	public function process()
	{
		if (!$this->processGenerateSettingsFile())
			$this->printErrors();

		if ($this->datas->database_create)
			$this->model_database->createDatabase($this->datas->database_server, $this->datas->database_name, $this->datas->database_login, $this->datas->database_password);
		
		if (!$this->model_database->testDatabaseSettings($this->datas->database_server, $this->datas->database_name, $this->datas->database_login, $this->datas->database_password, $this->datas->database_prefix, $this->datas->database_engine, $this->datas->database_clear))
			$this->printErrors();
		if (!$this->processInstallDatabase())
			$this->printErrors();
		if (!$this->processInstallDefaultData())
			$this->printErrors();
		if (!$this->processPopulateDatabase())
			$this->printErrors();
		if (!$this->processConfigureShop())
			$this->printErrors();
		if (!$this->processInstallFixtures())
			$this->printErrors();
		if (!$this->processInstallModules())
			$this->printErrors();
		if (!$this->processInstallAddonsModules())
			$this->printErrors();
		if (!$this->processInstallTheme())
			$this->printErrors();

		if ($this->datas->newsletter)
		{
			$params = http_build_query(array(
					'email' => $this->datas->admin_email,
					'method' => 'addMemberToNewsletter',
					'language' => $this->datas->lang,
					'visitorType' => 1,
					'source' => 'installer'
				));
			Tools::file_get_contents('http://www.prestashop.com/ajax/controller.php?'.$params);
		}

		if ($this->datas->send_email)
	      if (!$this->processSendEmail())
	        $this->printErrors();
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
		return $this->model_install->installDefaultData($this->datas->shop_name, true);
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
		return $result;
	}

	/**
	 * PROCESS : configureShop
	 * Set default shop configuration
	 */
	public function processConfigureShop()
	{
		$this->initializeContext();

		return $this->model_install->configureShop(array(
			'shop_name' =>				$this->datas->shop_name,
			'shop_activity' =>			$this->datas->shop_activity,
			'shop_country' =>			$this->datas->shop_country,
			'shop_timezone' =>			$this->datas->timezone,
			'use_smtp' =>				false,
			'admin_firstname' =>		$this->datas->admin_firstname,
			'admin_lastname' =>			$this->datas->admin_lastname,
			'admin_password' =>			$this->datas->admin_password,
			'admin_email' =>			$this->datas->admin_email,
			'configuration_agrement' =>	true,
			'send_informations' => true,
		));
		
	}

	/**
	 * PROCESS : installModules
	 * Install all modules in ~/modules/ directory
	 */
	public function processInstallModules()
	{
		$this->initializeContext();

		return $this->model_install->installModules();
	}

	/**
	 * PROCESS : installFixtures
	 * Install fixtures (E.g. demo products)
	 */
	public function processInstallFixtures()
	{
		$this->initializeContext();

		$this->model_install->xml_loader_ids = $this->datas->xml_loader_ids;
		$result = $this->model_install->installFixtures(null, array('shop_activity' => $this->datas->shop_activity, 'shop_country' => $this->datas->shop_country));
		$this->datas->xml_loader_ids = $this->model_install->xml_loader_ids;
		return $result;
	}

	/**
	 * PROCESS : installTheme
	 * Install theme
	 */
	public function processInstallTheme()
	{
		$this->initializeContext();

		return $this->model_install->installTheme();
	}

	/**
	 * PROCESS : installModulesAddons
	 * Install modules from addons
	 */
	public function processInstallAddonsModules()
	{
		return $this->model_install->installModulesAddons();
	}
  
  /**
  * PROCESS : sendEmail
  * Send information e-mail
  */
  public function processSendEmail()
  {
    require_once _PS_INSTALL_MODELS_PATH_.'mail.php';
    $mail = new InstallModelMail(
      false,
      $this->datas->smtp_server,
      $this->datas->smtp_login,
      $this->datas->smtp_password,
      $this->datas->smtp_port,
      $this->datas->smtp_encryption,
      $this->datas->admin_email
    );

    if (file_exists(_PS_INSTALL_LANGS_PATH_.$this->language->getLanguageIso().'/mail_identifiers.txt'))
      $content = file_get_contents(_PS_INSTALL_LANGS_PATH_.$this->language->getLanguageIso().'/mail_identifiers.txt');
    else
      $content = file_get_contents(_PS_INSTALL_LANGS_PATH_.InstallLanguages::DEFAULT_ISO.'/mail_identifiers.txt');

    $vars = array(
      '{firstname}' => $this->datas->admin_firstname,
      '{lastname}' => $this->datas->admin_lastname,
      '{shop_name}' => $this->datas->shop_name,
      '{passwd}' => $this->datas->admin_password,
      '{email}' => $this->datas->admin_email,
      '{shop_url}' => Tools::getHttpHost(true).__PS_BASE_URI__,
    );
    $content = str_replace(array_keys($vars), array_values($vars), $content);

    $mail->send(
      $this->l('%s Login information', $this->datas->shop_name),
      $content
    );

    return true;
  }
}
