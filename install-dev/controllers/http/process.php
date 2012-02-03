<?php
/*
* 2007-2011 PrestaShop
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision$
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
		Configuration::loadConfiguration();
		Context::getContext()->language = new Language(Configuration::get('PS_LANG_DEFAULT'));
		Context::getContext()->country = new Country('PS_COUNTRY_DEFAULT');
		Context::getContext()->cart = new Cart();

		require_once _PS_ROOT_DIR_.'/config/smarty.config.inc.php';
		Context::getContext()->smarty = $smarty;
	}

	public function process()
	{
		if (file_exists(_PS_ROOT_DIR_.'/'.self::SETTINGS_FILE))
			require_once _PS_ROOT_DIR_.'/'.self::SETTINGS_FILE;

		if (Tools::getValue('generateSettingsFile'))
			$this->processGenerateSettingsFile();
		else if (Tools::getValue('installDatabase') && !empty($this->session->process_validated['generateSettingsFile']))
			$this->processInstallDatabase();
		else if (Tools::getValue('populateDatabase') && !empty($this->session->process_validated['installDatabase']))
			$this->processPopulateDatabase();
		else if (Tools::getValue('configureShop') && !empty($this->session->process_validated['populateDatabase']))
			$this->processConfigureShop();
		else if (Tools::getValue('installModules') && !empty($this->session->process_validated['configureShop']))
			$this->processInstallModules();
		else if (Tools::getValue('installFixtures') && !empty($this->session->process_validated['installModules']))
			$this->processInstallFixtures();
		else if (Tools::getValue('installTheme') && !empty($this->session->process_validated['installFixtures']))
			$this->processInstallTheme();
		else
		{
			// With no parameters, we consider that we are doing a new install, so session where the last process step
			// was stored can be cleaned
			$this->session->process_validated = array();
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
		$this->session->process_validated['generateSettingsFile'] = true;
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
		$this->session->process_validated['installDatabase'] = true;
		$this->ajaxJsonAnswer(true);
	}

	/**
	 * PROCESS : populateDatabase
	 * Populate database with default data
	 */
	public function processPopulateDatabase()
	{
		$this->initializeContext();

		// @todo remove true in populateDatabase for 1.5.0 RC version
		$result = $this->model_install->populateDatabase(true, array(
			'shop_name' => $this->session->shop_name
		));

		if (!$result || $this->model_install->getErrors())
			$this->ajaxJsonAnswer(false, $this->model_install->getErrors());
		$this->session->xml_loader_ids = $this->model_install->xml_loader_ids;
		$this->session->process_validated['populateDatabase'] = true;
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
		$this->session->process_validated['configureShop'] = true;
		$this->ajaxJsonAnswer(true);
	}

	/**
	 * PROCESS : installModules
	 * Install all modules in ~/modules/ directory
	 */
	public function processInstallModules()
	{
		$this->initializeContext();

		// Remove all modules from module table, just in case
		Db::getInstance()->delete(_DB_PREFIX_.'module');

		if (!$this->model_install->installModules() || $this->model_install->getErrors())
			$this->ajaxJsonAnswer(false, $this->model_install->getErrors());
		$this->session->process_validated['installModules'] = true;
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
		if (!$this->model_install->installFixtures() || $this->model_install->getErrors())
			$this->ajaxJsonAnswer(false, $this->model_install->getErrors());
		$this->session->process_validated['installFixtures'] = true;
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
		$this->ajaxJsonAnswer(true);
	}

	/**
	 * PROCESS : preactivation
	 * (currently not used)
	 */
	public function processPreactivation()
	{
		foreach ($this->session->partners as $partner => $data)
		{
			/*$stream_context = @stream_context_create(array('http' => array('method'=> 'GET', 'timeout' => 5)));
			$url = 'http://api.prestashop.com/partner/preactivation/actions.php?version=1.0&partner='.addslashes($_GET['partner']);

			// Protect fields
			foreach ($_GET as $key => $value)
				$_GET[$key] = strip_tags(str_replace(array('\'', '"'), '', trim($value)));

			// Encore Get, Send It and Get Answers
			@require_once('../config/settings.inc.php');
			foreach ($_GET as $key => $val)
				$url .= '&'.$key.'='.urlencode($val);
			$url .= '&security='.md5($_GET['email']._COOKIE_IV_);*/
		}

		$this->ajaxJsonAnswer(true);
	}

	/**
	 * @see InstallAbstractModel::display()
	 */
	public function display()
	{
		$this->process_steps = array();
		$this->process_steps[] = array('key' => 'generateSettingsFile', 'lang' => $this->l('Create settings.inc file'));
		$this->process_steps[] = array('key' => 'installDatabase', 'lang' => $this->l('Create database tables'));
		$this->process_steps[] = array('key' => 'populateDatabase', 'lang' => $this->l('Populate database tables'));
		$this->process_steps[] = array('key' => 'configureShop', 'lang' => $this->l('Configure shop informations'));
		$this->process_steps[] = array('key' => 'installModules', 'lang' => $this->l('Install modules'));
		if ($this->session->install_type == 'full')
			$this->process_steps[] = array('key' => 'installFixtures', 'lang' => $this->l('Install demonstration data'));
		$this->process_steps[] = array('key' => 'installTheme', 'lang' => $this->l('Install theme'));

		$this->displayTemplate('process');
	}
}
