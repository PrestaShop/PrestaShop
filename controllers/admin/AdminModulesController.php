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
*  @version  Release: $Revision: 9790 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminModulesControllerCore extends AdminController
{

	/*
	** @var array map with $_GET keywords and their callback
	*/
	protected $map = array(
		'install' => 'install',
		'uninstall' => 'uninstall',
		'configure' => 'getContent',
		'update' => 'update',
		'delete' => 'delete'
	);

	protected $list_modules_categories = array();
	protected $list_partners_modules = array();
	protected $list_natives_modules = array();

	protected $nb_modules_total = 0;
	protected $nb_modules_installed = 0;
	protected $nb_modules_activated = 0;

	protected $serial_modules = '';
	protected $modules_authors = array();

	protected $id_employee;
	protected $iso_default_country;
	protected $filter_configuration = array();

 	protected $xml_modules_list = 'api.prestashop.com/xml/modules_list_15.xml';
	protected $logged_on_addons = false;

	/**
	 * Admin Modules Controller Constructor
	 * Init list modules categories
	 * Load id employee
	 * Load filter configuration
	 * Load cache file
	 */

	public function __construct()
	{
		parent::__construct();

		include_once(_PS_ADMIN_DIR_.'/../tools/tar/Archive_Tar.php');

		// Set the modules categories
		$this->list_modules_categories['administration']['name'] = $this->l('Administration');
		$this->list_modules_categories['advertising_marketing']['name'] = $this->l('Advertising & Marketing');
		$this->list_modules_categories['analytics_stats']['name'] = $this->l('Analytics & Stats');
		$this->list_modules_categories['billing_invoicing']['name'] = $this->l('Billing & Invoicing');
		$this->list_modules_categories['checkout']['name'] = $this->l('Checkout');
		$this->list_modules_categories['content_management']['name'] = $this->l('Content Management');
		$this->list_modules_categories['export']['name'] = $this->l('Export');
		$this->list_modules_categories['emailing']['name'] = $this->l('E-mailing');
		$this->list_modules_categories['front_office_features']['name'] = $this->l('Front Office Features');
		$this->list_modules_categories['i18n_localization']['name'] = $this->l('Internationalization & Localization');
		$this->list_modules_categories['merchandizing']['name'] = $this->l('Merchandizing');
		$this->list_modules_categories['migration_tools']['name'] = $this->l('Migration Tools');
		$this->list_modules_categories['payments_gateways']['name'] = $this->l('Payments & Gateways');
		$this->list_modules_categories['payment_security']['name'] = $this->l('Payment Security');
		$this->list_modules_categories['pricing_promotion']['name'] = $this->l('Pricing & Promotion');
		$this->list_modules_categories['quick_bulk_update']['name'] = $this->l('Quick / Bulk update');
		$this->list_modules_categories['search_filter']['name'] = $this->l('Search & Filter');
		$this->list_modules_categories['seo']['name'] = $this->l('SEO');
		$this->list_modules_categories['shipping_logistics']['name'] = $this->l('Shipping & Logistics');
		$this->list_modules_categories['slideshows']['name'] = $this->l('Slideshows');
		$this->list_modules_categories['smart_shopping']['name'] = $this->l('Smart Shopping');
		$this->list_modules_categories['market_place']['name'] = $this->l('Marketplace');
		$this->list_modules_categories['social_networks']['name'] = $this->l('Social Networks');
		$this->list_modules_categories['others']['name'] = $this->l('Other Modules');
		$this->list_modules_categories['mobile']['name'] = $this->l('Mobile');

		// Set Id Employee, Iso Default Country and Filter Configuration
		$this->id_employee = (int)$this->context->employee->id;
		$this->iso_default_country = $this->context->country->iso_code;
		$this->filter_configuration = Configuration::getMultiple(array(
			'PS_SHOW_TYPE_MODULES_'.(int)$this->id_employee,
			'PS_SHOW_COUNTRY_MODULES_'.(int)$this->id_employee,
			'PS_SHOW_INSTALLED_MODULES_'.(int)$this->id_employee,
			'PS_SHOW_ENABLED_MODULES_'.(int)$this->id_employee,
			'PS_SHOW_CAT_MODULES_'.(int)$this->id_employee,
		));

		// Load cache file modules list (natives and partners modules)
		$xmlModules = false;
		if (file_exists(_PS_ROOT_DIR_.Module::CACHE_FILE_MODULES_LIST))
			$xmlModules = @simplexml_load_file(_PS_ROOT_DIR_.Module::CACHE_FILE_MODULES_LIST);
		if ($xmlModules)
			foreach ($xmlModules->children() as $xmlModule)
				foreach ($xmlModule->children() as $module)
					foreach ($module->attributes() as $key => $value)
					{
						if ($xmlModule->attributes() == 'native' && $key == 'name')
							$this->list_natives_modules[] = (string)$value;
						if ($xmlModule->attributes() == 'partner' && $key == 'name')
							$this->list_partners_modules[] = (string)$value;
					}

		// Check if logged on Addons
		if (isset($this->context->cookie->username_addons) && isset($this->context->cookie->password_addons) && !empty($this->context->cookie->username_addons) && !empty($this->context->cookie->password_addons))
			$this->logged_on_addons = true;
	}

	public function ajaxProcessRefreshModuleList()
	{
		// Refresh modules_list.xml every week
		if (!$this->isFresh(Module::CACHE_FILE_MODULES_LIST, 604800))
		{
			if ($this->refresh(Module::CACHE_FILE_MODULES_LIST, 'https://'.$this->xml_modules_list))
				$this->status = 'refresh';
			elseif ($this->refresh(Module::CACHE_FILE_MODULES_LIST, 'http://'.$this->xml_modules_list))
				$this->status = 'refresh';
			else
				$this->status = 'error';
		}
		else
			$this->status = 'cache';


		// If logged to Addons Webservices, refresh default country native modules list every day
		if ($this->status != 'error')
		{
			if (!$this->isFresh(Module::CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST, 86400))
			{
				if (file_put_contents(_PS_ROOT_DIR_.Module::CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST, $this->addonsRequest('native')))
					$this->status = 'refresh';
				else
					$this->status = 'error';
			}
			else
				$this->status = 'cache';
			
			if (!$this->isFresh(Module::CACHE_FILE_MUST_HAVE_MODULES_LIST, 86400))
			{
				if (file_put_contents(_PS_ROOT_DIR_.Module::CACHE_FILE_MUST_HAVE_MODULES_LIST, $this->addonsRequest('must-have')))
					$this->status = 'refresh';
				else
					$this->status = 'error';
			}
			else
				$this->status = 'cache';
		}

		// If logged to Addons Webservices, refresh customer modules list every day
		if ($this->logged_on_addons && $this->status != 'error')
		{
			if (!$this->isFresh(Module::CACHE_FILE_CUSTOMER_MODULES_LIST, 60))
			{
				if (file_put_contents(_PS_ROOT_DIR_.Module::CACHE_FILE_CUSTOMER_MODULES_LIST, $this->addonsRequest('customer')))
					$this->status = 'refresh';
				else
					$this->status = 'error';
			}
			else
				$this->status = 'cache';
		}
	}

	public function displayAjaxRefreshModuleList()
	{
		echo Tools::jsonEncode(array('status' => $this->status));
	}


	public function ajaxProcessLogOnAddonsWebservices()
	{
		$content = $this->addonsRequest('check_customer', array('username_addons' => pSQL(trim(Tools::getValue('username_addons'))), 'password_addons' => pSQL(trim(Tools::getValue('password_addons')))));
		$xml = @simplexml_load_string($content, null, LIBXML_NOCDATA);
		if (!$xml)
			die('KO');
		$result = strtoupper((string)$xml->success);
		if (!in_array($result, array('OK', 'KO')))
			die ('KO');
		if ($result == 'OK')
		{
			$this->context->cookie->username_addons = pSQL(trim(Tools::getValue('username_addons')));
			$this->context->cookie->password_addons = pSQL(trim(Tools::getValue('password_addons')));
		}
		die($result);
	}

	public function ajaxProcessLogOutAddonsWebservices()
	{
		$this->context->cookie->username_addons = '';
		$this->context->cookie->password_addons = '';
		die('OK');
	}

	public function ajaxProcessReloadModulesList()
	{
		if (Tools::getValue('filterCategory'))
			Configuration::updateValue('PS_SHOW_CAT_MODULES_'.(int)$this->id_employee, Tools::getValue('filterCategory'));
		if (Tools::getValue('unfilterCategory'))
			Configuration::updateValue('PS_SHOW_CAT_MODULES_'.(int)$this->id_employee, '');

		$this->initContent();
		$this->smartyOutputContent('controllers/modules/list.tpl');
		exit;
	}

	public function ajaxProcessSetFilter()
	{
		$this->setFilterModules(Tools::getValue('module_type'), Tools::getValue('country_module_value'), Tools::getValue('module_install'), Tools::getValue('module_status'));
		die('OK');
	}

	public function ajaxProcessSaveFavoritePreferences()
	{
		$action = Tools::getValue('action_pref');
		$value = Tools::getValue('value_pref');
		$module = Tools::getValue('module_pref');
		$id_module_preference = (int)Db::getInstance()->getValue('SELECT `id_module_preference` FROM `'._DB_PREFIX_.'module_preference` WHERE `id_employee` = '.(int)$this->id_employee.' AND `module` = \''.pSQL($module).'\'');
		if ($id_module_preference > 0)
		{
			if ($action == 'i')
				$update = array('interest' => ($value == '' ? null : (int)$value));
			if ($action == 'f')
				$update = array('favorite' => ($value == '' ? null : (int)$value));
			Db::getInstance()->update('module_preference', $update, '`id_employee` = '.(int)$this->id_employee.' AND `module` = \''.pSQL($module).'\'', 0, true);
		}
		else
		{
			$insert = array('id_employee' => (int)$this->id_employee, 'module' => pSQL($module), 'interest' => null, 'favorite' => null);
			if ($action == 'i')
				$insert['interest'] = ($value == '' ? null : (int)$value);
			if ($action == 'f')
				$insert['favorite'] = ($value == '' ? null : (int)$value);
			Db::getInstance()->insert('module_preference', $insert, true);
		}
		die('OK');
	}


	private function sendStatisticRequest($object_key)
	{
		$post_data = http_build_query(array(
			'key' => urlencode($object_key),
			'url' => urlencode(Tools::getShopDomain()),
			'mail' => urlencode(Configuration::get('PS_SHOP_EMAIL')),
			'version' => urlencode(_PS_VERSION_),
			'method' => 'product_key'
		));

		$opts = array(
			'http' => array(
				'method' => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => $post_data
			)
		);

		$context = stream_context_create($opts);
		file_get_contents('http://api.addons.prestashop.com/', false, $context);
	}

	/**
	 * Ajax call for statistic
	 *
	 * @result : die the request
	 */
	public function ajaxProcessWsModuleCall()
	{
		if (($list = Tools::getValue('modules_list')) && is_array($list))
			foreach ($list as $id)
				if (($obj = Module::getInstanceById($id)) && (isset($obj->module_key)))
						$this->sendStatisticRequest($obj->module_key);
		die();
	}

	/**
	 * Ajax call for statistic
	 *
	 * @result : die the request
	 */
	public function ajaxProcessWsThemeCall()
	{
		// Theme list contains just the key for each theme
		if (($list = Tools::getValue('theme_list')) && is_array($list))
			foreach ($list as $theme_key)
				if (!empty($theme_key))
					$this->sendStatisticRequest($theme_key);
		die();
	}

	/*
	** Get current URL
	**
	** @param array $remove List of keys to remove from URL
	** @return string
	*/

	protected function getCurrentUrl($remove = array())
	{
		$url = $_SERVER['REQUEST_URI'];
		if (!$remove)
			return $url;

		if (!is_array($remove))
			$remove = array($remove);

		$url = preg_replace('#(?<=&|\?)('.implode('|', $remove).')=.*?(&|$)#i', '', $url);
		$len = strlen($url);
		if ($url[$len - 1] == '&')
			$url = substr($url, 0, $len - 1);
		return $url;
	}

	protected function extractArchive($file, $redirect = true)
	{
		$success = false;
		if (substr($file, -4) == '.zip')
		{
			if (Tools::ZipExtract($file, _PS_MODULE_DIR_))
				$success = true;
			else
				$this->errors[] = Tools::displayError('Error while extracting module (file may be corrupted).');
		}
		else
		{
			$archive = new Archive_Tar($file);
			if ($archive->extract(_PS_MODULE_DIR_))
				$success = true;
			else
				$this->errors[] = Tools::displayError('Error while extracting module (file may be corrupted).');
		}

		@unlink($file);
		if ($success && $redirect)
			Tools::redirectAdmin(self::$currentIndex.'&conf=8'.'&token='.$this->token);
	}

	protected function recursiveDeleteOnDisk($dir)
	{
		if (strpos(realpath($dir), realpath(_PS_MODULE_DIR_)) === false)
			return;
		if (is_dir($dir))
		{
			$objects = scandir($dir);
			foreach ($objects as $object)
				if ($object != '.' && $object != '..')
				{
					if (filetype($dir.'/'.$object) == 'dir')
						$this->recursiveDeleteOnDisk($dir.'/'.$object);
					else
						unlink($dir.'/'.$object);
				}
			reset($objects);
			rmdir($dir);
		}
	}

	/*
	** Filter Configuration Methods
	** Set and reset filter configuration
	*/

	protected function setFilterModules($module_type, $country_module_value, $module_install, $module_status)
	{
		Configuration::updateValue('PS_SHOW_TYPE_MODULES_'.(int)$this->id_employee, $module_type);
		Configuration::updateValue('PS_SHOW_COUNTRY_MODULES_'.(int)$this->id_employee, $country_module_value);
		Configuration::updateValue('PS_SHOW_INSTALLED_MODULES_'.(int)$this->id_employee, $module_install);
		Configuration::updateValue('PS_SHOW_ENABLED_MODULES_'.(int)$this->id_employee, $module_status);
	}

	protected function resetFilterModules()
	{
		Configuration::updateValue('PS_SHOW_TYPE_MODULES_'.(int)$this->id_employee, 'allModules');
		Configuration::updateValue('PS_SHOW_COUNTRY_MODULES_'.(int)$this->id_employee, 0);
		Configuration::updateValue('PS_SHOW_INSTALLED_MODULES_'.(int)$this->id_employee, 'installedUninstalled');
		Configuration::updateValue('PS_SHOW_ENABLED_MODULES_'.(int)$this->id_employee, 'enabledDisabled');
		Configuration::updateValue('PS_SHOW_CAT_MODULES_'.(int)$this->id_employee, '');
	}

	/*
	** Post Process Filter
	**
	*/

	public function postProcessFilterModules()
	{
		$this->setFilterModules(Tools::getValue('module_type'), Tools::getValue('country_module_value'), Tools::getValue('module_install'), Tools::getValue('module_status'));
		Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
	}

	public function postProcessResetFilterModules()
	{
		$this->resetFilterModules();
		Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
	}

	public function postProcessFilterCategory()
	{
		// Save configuration and redirect employee
		Configuration::updateValue('PS_SHOW_CAT_MODULES_'.(int)$this->id_employee, Tools::getValue('filterCategory'));
		Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
	}

	public function postProcessUnfilterCategory()
	{
		// Save configuration and redirect employee
		Configuration::updateValue('PS_SHOW_CAT_MODULES_'.(int)$this->id_employee, '');
		Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
	}

	/*
	** Post Process Module CallBack
	**
	*/

	public function postProcessReset()
	{
		if ($this->tabAccess['edit'] === '1')
		{
			$module = Module::getInstanceByName(Tools::getValue('module_name'));
			if (Validate::isLoadedObject($module))
			{
				if (!$module->getPermission('configure'))
					$this->errors[] = Tools::displayError('You do not have the permission to use this module');
				else
				{
					if ($module->uninstall())
						if ($module->install())
							Tools::redirectAdmin(self::$currentIndex.'&conf=21'.'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.$module->name.'&anchor=anchor'.ucfirst($module->name));
						else
							$this->errors[] = Tools::displayError('Cannot install module');
					else
						$this->errors[] = Tools::displayError('Cannot uninstall module');
				}
			}
			else
				$this->errors[] = Tools::displayError('Cannot load module object');
		}
		else
			$this->errors[] = Tools::displayError('You do not have permission to add here.');
	}

	public function postProcessDownload()
	{
	 	// PrestaShop demo mode
		if (_PS_MODE_DEMO_)
		{
			$this->errors[] = Tools::displayError('This functionality has been disabled.');
			return;
		}

		// Try to upload and unarchive the module
	 	if ($this->tabAccess['add'] === '1')
		{
			if (!isset($_FILES['file']['tmp_name']) || empty($_FILES['file']['tmp_name']))
				$this->errors[] = $this->l('no file selected');
			elseif (substr($_FILES['file']['name'], -4) != '.tar' && substr($_FILES['file']['name'], -4) != '.zip'
				&& substr($_FILES['file']['name'], -4) != '.tgz' && substr($_FILES['file']['name'], -7) != '.tar.gz')
				$this->errors[] = Tools::displayError('Unknown archive type');
			elseif (!@copy($_FILES['file']['tmp_name'], _PS_MODULE_DIR_.$_FILES['file']['name']))
				$this->errors[] = Tools::displayError('An error occurred while copying archive to module directory.');
			else
				$this->extractArchive(_PS_MODULE_DIR_.$_FILES['file']['name']);
		}
		else
			$this->errors[] = Tools::displayError('You do not have permission to add here.');
	}

	public function postProcessEnable()
	{
	 	if ($this->tabAccess['edit'] === '1')
		{
			$module = Module::getInstanceByName(Tools::getValue('module_name'));
			if (Validate::isLoadedObject($module))
			{
				if (!$module->getPermission('configure'))
					$this->errors[] = Tools::displayError('You do not have the permission to use this module');
				else
				{
					if (Tools::getValue('enable'))
						$module->enable();
					else
						$module->disable();
					Tools::redirectAdmin($this->getCurrentUrl('enable'));
				}
			}
			else
				$this->errors[] = Tools::displayError('Cannot load module object');
		}
		else
			$this->errors[] = Tools::displayError('You do not have permission to add here.');
	}

	public function postProcessDelete()
	{
		 	if ($this->tabAccess['delete'] === '1')
			{
				if (Tools::getValue('module_name') != '')
				{
					$module = Module::getInstanceByName(Tools::getValue('module_name'));
					if (Validate::isLoadedObject($module) && !$module->getPermission('configure'))
						$this->errors[] = Tools::displayError('You do not have the permission to use this module');
					else
					{
						$moduleDir = _PS_MODULE_DIR_.str_replace(array('.', '/', '\\'), array('', '', ''), Tools::getValue('module_name'));
						$this->recursiveDeleteOnDisk($moduleDir);
						Tools::redirectAdmin(self::$currentIndex.'&conf=22&token='.$this->token.'&tab_module='.Tools::getValue('tab_module').'&module_name='.Tools::getValue('module_name'));
					}
				}
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to delete here.');
	}

	public function postProcessCallback()
	{
		$return = false;
		$installed_modules = array();

		foreach ($this->map as $key => $method)
		{
			$modules = Tools::getValue($key);
			if (strpos($modules, '|'))
			{
				$modules_list_save = $modules;
				$modules = explode('|', $modules);
			}
			else
				$modules = empty($modules) ? false : array($modules);
			$module_errors = array();
			if ($modules)
				foreach ($modules as $name)
				{
					if ($key == 'update')
						Tools::deleteDirectory('../modules/'.$name.'/');

					// If Addons module, download and unzip it before installing it
					if (!is_dir('../modules/'.$name.'/'))
					{
						$filesList = array(
							array('type' => 'addonsNative', 'file' => Module::CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST, 'loggedOnAddons' => 0),
							array('type' => 'addonsBought', 'file' => Module::CACHE_FILE_CUSTOMER_MODULES_LIST, 'loggedOnAddons' => 1),
						);
						foreach ($filesList as $f)
							if (file_exists(_PS_ROOT_DIR_.$f['file']))
							{
								$file = $f['file'];
								$content = Tools::file_get_contents(_PS_ROOT_DIR_.$file);
								$xml = @simplexml_load_string($content, null, LIBXML_NOCDATA);
								foreach ($xml->module as $modaddons)
									if ($name == $modaddons->name && isset($modaddons->id) && ($this->logged_on_addons || $f['loggedOnAddons'] == 0))
									{
										if ($f['loggedOnAddons'] == 0)
											if (file_put_contents('../modules/'.$modaddons->name.'.zip', $this->addonsRequest('module', array('id_module' => pSQL($modaddons->id)))))
												$this->extractArchive('../modules/'.$modaddons->name.'.zip', false);
										if ($f['loggedOnAddons'] == 1 && $this->logged_on_addons)
											if (file_put_contents('../modules/'.$modaddons->name.'.zip', $this->addonsRequest('module', array('id_module' => pSQL($modaddons->id), 'username_addons' => pSQL(trim($this->context->cookie->username_addons)), 'password_addons' => pSQL(trim($this->context->cookie->password_addons))))))
												$this->extractArchive('../modules/'.$modaddons->name.'.zip', false);
									}
							}

					}
					// Check potential error
					if (!($module = Module::getInstanceByName(urldecode($name))))
						$this->errors[] = $this->l('module not found');
					elseif ($key == 'install' && $this->tabAccess['add'] !== '1')
						$this->errors[] = Tools::displayError('You do not have permission to install a module.');
					elseif ($key == 'uninstall' && ($this->tabAccess['delete'] !== '1' || !$module->getPermission('configure')))
						$this->errors[] = Tools::displayError('You do not have permission to delete this module.');
					elseif ($key == 'configure' && ($this->tabAccess['edit'] !== '1' || !$module->getPermission('configure') || !Module::isInstalled(urldecode($name))))
						$this->errors[] = Tools::displayError('You do not have permission to configure this module.');
					elseif ($key == 'install' && Module::isInstalled($module->name))
						$this->errors[] = Tools::displayError('This module is already installed:').' '.$module->name;
					elseif ($key == 'uninstall' && !Module::isInstalled($module->name))
						$this->errors[] = Tools::displayError('This module is already uninstalled:').' '.$module->name;
					else if ($key == 'update' && !Module::isInstalled($module->name))
						$this->errors[] = Tools::displayError('This module need to be installed to be updated:').' '.$module->name;
					else
					{
						// If we install a module, force temporary global context for multishop
						if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL && $method != 'getContent')
						{
							Context::getContext()->tmpOldShop = clone(Context::getContext()->shop);
							Context::getContext()->shop = new Shop();
						}

						//retrocompatibility
						if (Tools::getValue('controller') != '')
							$_POST['tab'] = Tools::safeOutput(Tools::getValue('controller'));

						$echo = '';
						if ($key != 'update')
						{
						// We check if method of module exists
							if (!method_exists($module, $method))
								throw new PrestaShopException('Method of module can\'t be found');

							// Get the return value of current method
							$echo = $module->{$method}();
						}
						
						// If the method called is "configure" (getContent method), we show the html code of configure page
						if ($key == 'configure' && Module::isInstalled($module->name))
						{
							if (isset($module->multishop_context))
								$this->multishop_context = $module->multishop_context;
							
							$backlink = self::$currentIndex.'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.$module->name;
							$hooklink = 'index.php?tab=AdminModulesPositions&token='.Tools::getAdminTokenLite('AdminModulesPositions').'&show_modules='.(int)$module->id;
							$tradlink = 'index.php?tab=AdminTranslations&token='.Tools::getAdminTokenLite('AdminTranslations').'&type=modules&lang=';

							$toolbar = '<table class="table" cellpadding="0" cellspacing="0" style="margin:auto;text-align:center"><tr>
									<th>'.$this->l('Module').' <span style="color: green;">'.$module->name.'</span></th>
									<th><a href="'.$backlink.'" style="padding:5px 10px">'.$this->l('Back').'</a></th>
									<th><a href="'.$hooklink.'" style="padding:5px 10px">'.$this->l('Manage hooks').'</a></th>
									<th style="padding:5px 10px">'.$this->l('Manage translations:').' ';
									foreach (Language::getLanguages(false) as $language)
										$toolbar .= '<a href="'.$tradlink.$language['iso_code'].'#'.$module->name.'" style="margin-left:5px"><img src="'._THEME_LANG_DIR_.$language['id_lang'].'.jpg" alt="'.$language['iso_code'].'" title="'.$language['iso_code'].'" /></a>';
							$toolbar .= '</th></tr>';

							// Display checkbox in toolbar if multishop
							if (Shop::isFeatureActive())
							{
								$activateOnclick = 'onclick="location.href = \''.$this->getCurrentUrl('enable').'&enable=\'+(($(this).attr(\'checked\')) ? 1 : 0)"';
								$toolbar .= '<tr>
										<th colspan="4">
											<input type="checkbox" name="activateModule" value="1" '.(($module->active) ? 'checked="checked"' : '').' '.$activateOnclick.' /> '.$this->l('Activate module for').' ';
								if (Shop::getContext() == Shop::CONTEXT_SHOP)
									$toolbar .= 'shop <b>'.$this->context->shop->name.'</b>';
								elseif (Shop::getContext() == Shop::CONTEXT_GROUP)
								{
									$shop_group = new ShopGroup((int)Shop::getContextShopGroupID());
									$toolbar .= 'all shops of group shop <b>'.$shop_group->name.'</b>';
								}
								else
									$toolbar .= 'all shops';
								$toolbar .= '</th>
								</tr>';
							}
							$toolbar .= '</table>';


							if (Shop::isFeatureActive() && isset(Context::getContext()->tmpOldShop))
							{
								Context::getContext()->shop = clone(Context::getContext()->tmpOldShop);
								unset(Context::getContext()->tmpOldShop);
							}
							// Display module configuration
							$this->context->smarty->assign('module_content', $toolbar.'<div class="clear">&nbsp;</div>'.$echo.'<div class="clear">&nbsp;</div>'.$toolbar);
						}
						elseif ($echo === true)
						{
							$return = 13;
							if ($method == 'install')
							{
								$return = 12;
								$installed_modules[] = $module->id;
							}
						}
						elseif ($echo === false)
							$module_errors[] = array('name' => $name, 'message' => $module->getErrors());
						if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL && isset(Context::getContext()->tmpOldShop))
						{
							Context::getContext()->shop = clone(Context::getContext()->tmpOldShop);
							unset(Context::getContext()->tmpOldShop);
						}
					}
					if ($key != 'configure' && isset($_GET['bpay']))
						Tools::redirectAdmin('index.php?tab=AdminPayment&token='.Tools::getAdminToken('AdminPayment'.(int)(Tab::getIdFromClassName('AdminPayment')).(int)$this->id_employee));
				}
			if (count($module_errors))
			{
				// If error during module installation, no redirection
				$html_error = $this->generateHtmlMessage($module_errors);
				$this->errors[] = sprintf(Tools::displayError('The following module(s) were not installed properly: %s'), $html_error);
				$this->context->smarty->assign('error_module', 'true');
			}
		}
		if ($return)
		{
			$params = (count($installed_modules)) ? '&installed_modules='.implode('|', $installed_modules) : '';

			// If redirect parameter is present and module installed with success, we redirect on configuration module page
			if (Tools::getValue('redirect') == 'config' && Tools::getValue('module_name') != '' && $return == '12' && Module::isInstalled(pSQL(Tools::getValue('module_name'))))
				Tools::redirectAdmin('index.php?controller=adminmodules&configure='.Tools::getValue('module_name').'&token='.Tools::getValue('token').'&module_name='.Tools::getValue('module_name').$params);
			Tools::redirectAdmin(self::$currentIndex.'&conf='.$return.'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.$module->name.'&anchor=anchor'.ucfirst($module->name).(isset($modules_list_save) ? '&modules_list='.$modules_list_save : '').$params);
		}
	}
	
	public function postProcess()
	{
		// Parent Post Process
		parent::postProcess();


		// Get the list of installed module ans prepare it for ajax call.
		if (($list = Tools::getValue('installed_modules')))
			Context::getContext()->smarty->assign('installed_modules', Tools::jsonEncode(explode('|', $list)));

		// If redirect parameter is present and module already installed, we redirect on configuration module page
		if (Tools::getValue('redirect') == 'config' && Tools::getValue('module_name') != '' && Module::isInstalled(pSQL(Tools::getValue('module_name'))))
			Tools::redirectAdmin('index.php?controller=adminmodules&configure='.Tools::getValue('module_name').'&token='.Tools::getValue('token').'&module_name='.Tools::getValue('module_name'));

		// Execute filter or callback methods
		$filterMethods = array('filterModules', 'resetFilterModules', 'filterCategory', 'unfilterCategory');
		$callbackMethods = array('reset', 'download', 'enable', 'delete');
		$postProcessMethodsList = array_merge((array)$filterMethods, (array)$callbackMethods);
		foreach ($postProcessMethodsList as $ppm)
			if (Tools::isSubmit($ppm))
			{
				$ppm = 'postProcess'.ucfirst($ppm);
				if (method_exists($this, $ppm))
					$ppmReturn = $this->$ppm();
			}

		// Call appropriate module callback
		if (!isset($ppmReturn))
			$this->postProcessCallback();
	}

	/**
	 * Generate html errors for a module process
	 *
	 * @param $module_errors
	 * @return string
	 */
	protected function generateHtmlMessage($module_errors)
	{
		$html_error = '';

		if (count($module_errors))
		{
			$html_error = '<ul style="line-height:20px">';
			foreach ($module_errors as $module_error)
			{
				$html_error_description = '';
				if (count($module_error['message']) > 0)
					foreach ($module_error['message'] as $e)
						$html_error_description .= '<br />'.$e;
				$html_error .= '<li><b>- '.$module_error['name'].'</b> : '.$html_error_description.'</li>';
			}
			$html_error .= '</ul>';
		}
		return $html_error;
	}

	/*
	** Display Modules Lists
	**
	*/
	protected $translationsTab = array();
	public function displayModuleOptions($module)
	{	
		if (!isset($this->translationsTab['Disable this module']))
		{
			$this->translationsTab['Disable this module'] = $this->l('Disable this module');
			$this->translationsTab['Enable this module for all shops'] = $this->l('Enable this module for all shops');
			$this->translationsTab['Disable'] = $this->l('Disable');
			$this->translationsTab['Enable'] = $this->l('Enable');
			$this->translationsTab['Reset'] = $this->l('Reset');
			$this->translationsTab['Configure'] = $this->l('Configure');
			$this->translationsTab['Delete'] = $this->l('Delete');
			$this->translationsTab['This action will permanently remove the module from the server. Are you sure you want to do this?'] = $this->l('This action will permanently remove the module from the server. Are you sure you want to do this?');
		}	
			
		$return = '';
		$href = self::$currentIndex.'&token='.$this->token.'&module_name='.urlencode($module->name).'&tab_module='.$module->tab;
		if ($module->id)
			$return .= ' <span class="desactive-module"><a class="action_module" '.($module->active && method_exists($module, 'onclickOption')? 'onclick="'.$module->onclickOption('desactive', $href).'"' : '').' href="'.self::$currentIndex.'&token='.$this->token.'&module_name='.urlencode($module->name).'&'.($module->active ? 'enable=0' : 'enable=1').'&tab_module='.$module->tab.'" '.((Shop::isFeatureActive()) ? 'title="'.htmlspecialchars($module->active ? $this->translationsTab['Disable this module'] : $this->translationsTab['Enable this module for all shops']).'"' : '').'>'.($module->active ? $this->translationsTab['Disable'] : $this->translationsTab['Enable']).'</a></span>';

		if ($module->id && $module->active)
			$return .= (!empty($result) ? '|' : '').' <span class="reset-module"><a class="action_module" '.(method_exists($module, 'onclickOption')? 'onclick="'.$module->onclickOption('reset', $href).'"' : '').' href="'.self::$currentIndex.'&token='.$this->token.'&module_name='.urlencode($module->name).'&reset&tab_module='.$module->tab.'">'.$this->translationsTab['Reset'].'</a></span>';

		if ($module->id && isset($module->is_configurable) && $module->is_configurable)
			$return .= (!empty($result) ? '|' : '').' <span class="configure-module"><a class="action_module" '.(method_exists($module, 'onclickOption')? 'onclick="'.$module->onclickOption('configure', $href).'"' : '').' href="'.self::$currentIndex.'&configure='.urlencode($module->name).'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.urlencode($module->name).'">'.$this->translationsTab['Configure'].'</a></span>';

		$hrefDelete = self::$currentIndex.'&delete='.urlencode($module->name).'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.urlencode($module->name);
		$return .= (!empty($result) ? '|' : '').' <span class="delete-module"><a class="action_module" '.(method_exists($module, 'onclickOption')? 'onclick="'.$module->onclickOption('delete', $hrefDelete).'"' : '').' onclick="return confirm(\''.$this->translationsTab['This action will permanently remove the module from the server. Are you sure you want to do this?'].'\');" href="'.$hrefDelete.'">'.$this->translationsTab['Delete'].'</a></span>';

		return $return;
	}

	public function initModulesList(&$modules)
	{
		foreach ($modules as $k => $module)
		{
			// Check add permissions, if add permissions not set, addons modules and uninstalled modules will not be displayed
			if ($this->tabAccess['add'] !== '1' && isset($module->type) && ($module->type != 'addonsNative' || $module->type != 'addonsBought'))
				unset($modules[$k]);
			else if ($this->tabAccess['add'] !== '1' && (!isset($module->id) || $module->id < 1))
				unset($modules[$k]);
			else if ($module->id && !Module::getPermissionStatic($module->id, 'view') && !Module::getPermissionStatic($module->id, 'configure'))
				unset($modules[$k]);
			else
			{
				// Init serial and modules author list
				if (!in_array($module->name, $this->list_natives_modules))
					$this->serial_modules .= $module->name.' '.$module->version.'-'.($module->active ? 'a' : 'i')."\n";
				$module_author = $module->author;
				if (!empty($module_author) && ($module_author != ''))
					$this->modules_authors[strtolower($module_author)] = 'notselected';
			}
		}
		$this->serial_modules = urlencode($this->serial_modules);
	}

	public function makeModulesStats($module)
	{
		// Count Installed Modules
		if (isset($module->id) && $module->id > 0)
			$this->nb_modules_installed++;
		
		// Count Activated Modules
		if (isset($module->id) && $module->id > 0 && $module->active > 0)
			$this->nb_modules_activated++;
		
		// Count Modules By Category
		if (isset($this->list_modules_categories[$module->tab]['nb']))
			$this->list_modules_categories[$module->tab]['nb']++;
		else
			$this->list_modules_categories['others']['nb']++;
	}

	public function isModuleFiltered($module)
	{
		// If action on module, we display it
		if (Tools::getValue('module_name') != '' && Tools::getValue('module_name') == $module->name)
			return false;


		// Filter on module name
		$filter_name = Tools::getValue('filtername');
		if (!empty($filter_name))
		{
			if (stristr($module->name, $filter_name) === false && stristr($module->displayName, $filter_name) === false && stristr($module->description, $filter_name) === false)
				return true;
			return false;
		}

		// Filter on interest
		if ((int)Db::getInstance()->getValue('SELECT `id_module_preference` FROM `'._DB_PREFIX_.'module_preference` WHERE `module` = \''.pSQL($module->name).'\' AND `id_employee` = '.(int)$this->id_employee.' AND `interest` = 0') > 0)
				return true;

		// Filter on favorites
		if (Configuration::get('PS_SHOW_CAT_MODULES_'.(int)$this->id_employee) == 'favorites')
		{
			if ((int)Db::getInstance()->getValue('SELECT `id_module_preference` FROM `'._DB_PREFIX_.'module_preference` WHERE `module` = \''.pSQL($module->name).'\' AND `id_employee` = '.(int)$this->id_employee.' AND `favorite` = 1 AND (`interest` = 1 OR `interest` IS NULL)') < 1)
				return true;
		}
		else
		{
			// Handle "others" category
			if (!isset($this->list_modules_categories[$module->tab]))
				$module->tab = 'others';

			// Filter on module category
			$categoryFiltered = array();
			$filterCategories = explode('|', Configuration::get('PS_SHOW_CAT_MODULES_'.(int)$this->id_employee));
			if (count($filterCategories) > 0)
				foreach ($filterCategories as $fc)
					if (!empty($fc))
						$categoryFiltered[$fc] = 1;
			if (count($categoryFiltered) > 0 && !isset($categoryFiltered[$module->tab]))
				return true;
		}

		// Filter on module type and author
		$show_type_modules = $this->filter_configuration['PS_SHOW_TYPE_MODULES_'.(int)$this->id_employee];
		if ($show_type_modules == 'nativeModules' && !in_array($module->name, $this->list_natives_modules))
			return true;
		else if ($show_type_modules == 'partnerModules' && !in_array($module->name, $this->list_partners_modules))
			return true;
		else if ($show_type_modules == 'addonsModules' && (!isset($module->type) || $module->type != 'addonsBought'))
			return true;
		else if ($show_type_modules == 'mustHaveModules' && (!isset($module->type) || $module->type != 'addonsMustHave'))
			return true;
		else if ($show_type_modules == 'otherModules' && (in_array($module->name, $this->list_partners_modules) || in_array($module->name, $this->list_natives_modules)))
			return true;
		else if (strpos($show_type_modules, 'authorModules[') !== false)
		{
			// setting selected author in authors set
			$author_selected = substr(str_replace(array('authorModules[', "\'"), array('', "'"), $show_type_modules), 0, -1);
			$this->modules_authors[$author_selected] = 'selected';
			if (empty($module->author) || strtolower($module->author) != $author_selected)
				return true;
		}

		// Filter on install status
		$show_installed_modules = $this->filter_configuration['PS_SHOW_INSTALLED_MODULES_'.(int)$this->id_employee];
		if ($show_installed_modules == 'installed' && !$module->id)
			return true;
		if ($show_installed_modules == 'uninstalled' && $module->id)
			return true;


		// Filter on active status
		$show_enabled_modules = $this->filter_configuration['PS_SHOW_ENABLED_MODULES_'.(int)$this->id_employee];
		if ($show_enabled_modules == 'enabled' && !$module->active)
			return true;
		if ($show_enabled_modules == 'disabled' && $module->active)
			return true;

		// Filter on country
		$show_country_modules = $this->filter_configuration['PS_SHOW_COUNTRY_MODULES_'.(int)$this->id_employee];
		if ($show_country_modules && (isset($module->limited_countries) && !empty($module->limited_countries)
				&& ((is_array($module->limited_countries) && count($module->limited_countries)
				&& !in_array(strtolower($this->iso_default_country), $module->limited_countries))
				|| (!is_array($module->limited_countries) && strtolower($this->iso_default_country) != strval($module->limited_countries)))))
			return true;

		// Module has not been filtered		
		return false;
	}

	public function initContent()
	{
		// Adding Css
		$this->addCSS(__PS_BASE_URI__.str_replace(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR, '', _PS_ADMIN_DIR_).'/themes/'.$this->bo_theme.'/css/modules.css', 'all');

		// If we are on a module configuration, no need to load all modules
		if (Tools::getValue('configure') != '')
			return true;

		// Init
		$smarty = $this->context->smarty;
		$autocompleteList = 'var moduleList = [';
		$nameCountryDefault = Country::getNameById($this->context->language->id, Configuration::get('PS_COUNTRY_DEFAULT'));
		$categoryFiltered = array();
		$filterCategories = explode('|', Configuration::get('PS_SHOW_CAT_MODULES_'.(int)$this->id_employee));
		if (count($filterCategories) > 0)
			foreach ($filterCategories as $fc)
				if (!empty($fc))
					$categoryFiltered[$fc] = 1;

		foreach ($this->list_modules_categories as $k => $v)
			$this->list_modules_categories[$k]['nb'] = 0;

		// Retrieve Modules Preferences
		$modules_preferences = '';
		$modules_preferences_tmp = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'module_preference` WHERE `id_employee` = '.(int)$this->id_employee);
		foreach ($modules_preferences_tmp as $k => $v)
		{
			if ($v['interest'] == null)
				unset($v['interest']);
			if ($v['favorite'] == null)
				unset($v['favorite']);
			$modules_preferences[$v['module']] = $v;
		}

		// Retrieve Modules List
		$modules = Module::getModulesOnDisk(true, $this->logged_on_addons, $this->id_employee);
		$this->initModulesList($modules);
		$this->nb_modules_total = count($modules);
		$module_errors = array();
		$module_success = array();

		// Browse modules list
		foreach ($modules as $km => $module)
		{
			// Upgrade Module process, init check if a module could be upgraded
			if (Module::initUpgradeModule($module))
			{
				// When the XML cache file is up-to-date, the module may not be loaded yet
				if (!class_exists($module->name))
				{
					if (!file_exists(_PS_MODULE_DIR_.$module->name.'/'.$module->name.'.php'))
						continue;
					require_once(_PS_MODULE_DIR_.$module->name.'/'.$module->name.'.php');
				}
				if ($object = new $module->name())
				{
					$object->runUpgradeModule();
					if ((count($errors_module_list = $object->getErrors())))
						$module_errors[] = array('name' => $module->name, 'message' => $errors_module_list);
					else if ((count($conf_module_list = $object->getConfirmations())))
						$module_success[] = array('name' => $module->name, 'message' => $conf_module_list);
					unset($object);
				}
			}
			// Module can't be upgraded if not file exist but can change the database version...
			// User has to be prevented
			elseif (Module::getUpgradeStatus($module->name))
			{
				// When the XML cache file is up-to-date, the module may not be loaded yet
				if (!class_exists($module->name))
					if (file_exists(_PS_MODULE_DIR_.$module->name.'/'.$module->name.'.php'))
					{
						require_once(_PS_MODULE_DIR_.$module->name.'/'.$module->name.'.php');
						$object = new $module->name();
						$module_success[] = array('name' => $module->name, 'message' => array(
							0 => $this->l('Current version:').$object->version,
							1 => $this->l('No file upgrades applied (none exist)'))
						);
					}
					else
						continue;
				unset($object);
			}

			// Make modules stats
			$this->makeModulesStats($module);

			// Assign warnings
			if ($module->active && isset($module->warning) && !empty($module->warning))
				$this->warnings[] = sprintf($this->l('%1$s: %2$s'), $module->displayName, $module->warning);

			// AutoComplete array
			$autocompleteList .= Tools::jsonEncode(array(
				'displayName' => (string)$module->displayName,
				'desc' => (string)$module->description,
				'name' => (string)$module->name,
				'author' => (string)$module->author,
				'image' => (isset($module->image) ? (string)$module->image : ''),
				'option' => '',
			)).', ';

			// Apply filter
			if ($this->isModuleFiltered($module) && Tools::getValue('select') != 'favorites')
				unset($modules[$km]);
			else
			{
				$obj = null;
				if ($module->onclick_option)
					$obj = new $module->name();

				// Fill module data
				$modules[$km]->logo = '../../img/questionmark.png';
				if (file_exists('../modules/'.$module->name.'/logo.gif'))
					$modules[$km]->logo = 'logo.gif';
				if (file_exists('../modules/'.$module->name.'/logo.png'))
					$modules[$km]->logo = 'logo.png';
				$modules[$km]->optionsHtml = $this->displayModuleOptions($module);
				$modules[$km]->categoryName = (isset($this->list_modules_categories[$module->tab]['name']) ? $this->list_modules_categories[$module->tab]['name'] : $this->list_modules_categories['others']['name']);
				$modules[$km]->options['install_url'] = self::$currentIndex.'&install='.urlencode($module->name).'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.$module->name.'&anchor=anchor'.ucfirst($module->name);
				$modules[$km]->options['update_url'] = self::$currentIndex.'&update='.urlencode($module->name).'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.$module->name.'&anchor=anchor'.ucfirst($module->name);
				$modules[$km]->options['uninstall_url'] = self::$currentIndex.'&uninstall='.urlencode($module->name).'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.$module->name.'&anchor=anchor'.ucfirst($module->name);

				$modules[$km]->options['uninstall_onclick'] = ((!$module->onclick_option) ?
					((empty($module->confirmUninstall)) ? '' : 'return confirm(\''.addslashes($module->confirmUninstall).'\');') :
					$obj->onclickOption('uninstall', $modules[$km]->options['uninstall_url']));

				if ((Tools::getValue('module_name') == $module->name || in_array($module->name, explode('|', Tools::getValue('modules_list')))) && (int)Tools::getValue('conf') > 0)
					$modules[$km]->message = $this->_conf[(int)Tools::getValue('conf')];

				if ((Tools::getValue('module_name') == $module->name || in_array($module->name, explode('|', Tools::getValue('modules_list')))) && (int)Tools::getValue('conf') > 0)

				if (isset($modules_preferences[$modules[$km]->name]))
					$modules[$km]->preferences = $modules_preferences[$modules[$km]->name];
				unset($obj);
			}
			unset($object);
		}

		// Actually used for the report of the upgraded errors
		if (count($module_errors))
		{
			$html = $this->generateHtmlMessage($module_errors);
			$this->errors[] = sprintf(Tools::displayError('The following module(s) were not upgraded successfully: %s'), $html);
		}
		if (count($module_success))
		{
			$html = $this->generateHtmlMessage($module_success);
			$this->confirmations[] = sprintf($this->l('The following module(s) were upgraded successfully:').' %s', $html);
		}

		// Init tpl vars for smarty
		$tpl_vars = array();

		$tpl_vars['token'] = $this->token;
		$tpl_vars['currentIndex'] = self::$currentIndex;
		$tpl_vars['dirNameCurrentIndex'] = dirname(self::$currentIndex);
		$tpl_vars['ajaxCurrentIndex'] = str_replace('index', 'ajax-tab', self::$currentIndex);
		$tpl_vars['autocompleteList'] = rtrim($autocompleteList, ' ,').'];';

		$tpl_vars['showTypeModules'] = $this->filter_configuration['PS_SHOW_TYPE_MODULES_'.(int)$this->id_employee];
		$tpl_vars['showCountryModules'] = $this->filter_configuration['PS_SHOW_COUNTRY_MODULES_'.(int)$this->id_employee];
		$tpl_vars['showInstalledModules'] = $this->filter_configuration['PS_SHOW_INSTALLED_MODULES_'.(int)$this->id_employee];
		$tpl_vars['showEnabledModules'] = $this->filter_configuration['PS_SHOW_ENABLED_MODULES_'.(int)$this->id_employee];
		$tpl_vars['nameCountryDefault'] = Country::getNameById($this->context->language->id, Configuration::get('PS_COUNTRY_DEFAULT'));
		$tpl_vars['isoCountryDefault'] = $this->iso_default_country;

		$tpl_vars['categoryFiltered'] = $categoryFiltered;

		$tpl_vars['modules'] = $modules;
		$tpl_vars['nb_modules'] = $this->nb_modules_total;
		$tpl_vars['nb_modules_favorites'] = Db::getInstance()->getValue('SELECT COUNT(`id_module_preference`) FROM `'._DB_PREFIX_.'module_preference` WHERE `id_employee` = '.(int)$this->id_employee.' AND `favorite` = 1 AND (`interest` = 1 OR `interest` IS NULL)');
		$tpl_vars['nb_modules_installed'] = $this->nb_modules_installed;
		$tpl_vars['nb_modules_uninstalled'] = $tpl_vars['nb_modules'] - $tpl_vars['nb_modules_installed'];
		$tpl_vars['nb_modules_activated'] = $this->nb_modules_activated;
		$tpl_vars['nb_modules_unactivated'] = $tpl_vars['nb_modules_installed'] - $tpl_vars['nb_modules_activated'];
		$tpl_vars['list_modules_categories'] = $this->list_modules_categories;
		$tpl_vars['list_modules_authors'] = $this->modules_authors;

		$tpl_vars['check_url_fopen'] = (ini_get('allow_url_fopen') ? 'ok' : 'ko');
		$tpl_vars['check_openssl'] = (extension_loaded('openssl') ? 'ok' : 'ko');

		$tpl_vars['add_permission'] = $this->tabAccess['add'];

		if ($this->logged_on_addons)
		{
			$tpl_vars['logged_on_addons'] = 1;
			$tpl_vars['username_addons'] = $this->context->cookie->username_addons;
		}
		$smarty->assign($tpl_vars);
	}
}
