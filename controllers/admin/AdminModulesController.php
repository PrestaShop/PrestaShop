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
*  @version  Release: $Revision: 9790 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminModulesControllerCore extends AdminController
{

	/*
	** @var array map with $_GET keywords and their callback
	*/
	private $map = array(
		'install' => 'install',
		'uninstall' => 'uninstall',
		'configure' => 'getContent',
		'delete' => 'delete'
	);

	private $list_modules_categories = array();
	private $list_partners_modules = array();
	private $list_natives_modules = array();

	private $nb_modules_total = 0;
	private $nb_modules_installed = 0;
	private $nb_modules_activated = 0;

	private $serial_modules = '';
	private $modules_authors = array();

	private $id_employee;
	private $iso_default_country;
	private $filter_configuration = array();

 	private $xml_modules_list = 'https://api.prestashop.com/xml/modules_list.xml';
	private $addons_url_http = 'http://api.addons.prestashop.com/151/';
	private $addons_url = 'https://api.addons.prestashop.com/151/';
	private $logged_on_addons = false;
	private $cache_file_modules_list = '/config/xml/modules_list.xml';
	private $cache_file_default_country_modules_list = '/config/xml/default_country_modules_list.xml';
	private $cache_file_customer_modules_list = '/config/xml/customer_modules_list.xml';

	/*
	** Admin Modules Controller Constructor
	** Init list modules categories
	** Load id employee
	** Load filter configuration
	** Load cache file
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
		$this->list_modules_categories['front_office_features']['name'] = $this->l('Front Office Features');
		$this->list_modules_categories['i18n_localization']['name'] = $this->l('I18n & Localization');
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
		$this->list_modules_categories['market_place']['name'] = $this->l('Market Place');
		$this->list_modules_categories['social_networks']['name'] = $this->l('Social Networks');
		$this->list_modules_categories['others']['name'] = $this->l('Other Modules');

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
		if (file_exists(_PS_ROOT_DIR_.$this->cache_file_modules_list))
			$xmlModules = @simplexml_load_file(_PS_ROOT_DIR_.$this->cache_file_modules_list);
		if ($xmlModules)
			foreach($xmlModules->children() as $xmlModule)
				foreach($xmlModule->children() as $module)
					foreach($module->attributes() as $key => $value)
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

	/*
	** Ajax Request Methods
	**
	** if modules_list.xml is outdated,
	** this function will re-upload it from prestashop.com
	**
	** @return null
	*/

	public function isFresh($file, $timeout = 604800000)
	{
		if (file_exists(_PS_ROOT_DIR_.$file))
			return ((time() - filemtime(_PS_ROOT_DIR_.$this->cache_file_modules_list)) < $timeout);
		else
			return false;
	}

	public function refresh($file_to_refresh, $external_file)
	{
		$content = Tools::file_get_contents($external_file);
		if ($content)
			return file_put_contents(_PS_ROOT_DIR_.$file_to_refresh, $content);
		return false;
	}


	public function ajaxProcessRefreshModuleList()
	{
		// Refresh modules_list.xml every week
		if (!$this->isFresh($this->cache_file_modules_list, 604800))
		{
			if ($this->refresh($this->cache_file_modules_list, $this->xml_modules_list))
				$this->status = 'refresh';
			else
				$this->status = 'error';
		}
		else
			$this->status = 'cache';


		// If logged to Addons Webservices, refresh default country native modules list every day
		if ($this->status != 'error')
		{
			if (!$this->isFresh($this->cache_file_default_country_modules_list, 86400))
			{
				if ($this->refresh($this->cache_file_default_country_modules_list, $this->addons_url_http.'listing/native/'.strtolower(Configuration::get('PS_LOCALE_COUNTRY'))))
					$this->status = 'refresh';
				else if ($this->refresh($this->cache_file_default_country_modules_list, $this->addons_url.'listing/native/'.strtolower(Configuration::get('PS_LOCALE_COUNTRY'))))
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
			if (!$this->isFresh($this->cache_file_customer_modules_list, 60))
			{
				if ($this->refresh($this->cache_file_customer_modules_list, $this->addons_url.'listing/customer/'.pSQL(trim($this->context->cookie->username_addons)).'/'.pSQL(trim($this->context->cookie->password_addons))))
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
		$content = Tools::file_get_contents($this->addons_url.'check_customer/'.pSQL(trim(Tools::getValue('username_addons'))).'/'.pSQL(trim(Tools::getValue('password_addons'))));
		$xml = @simplexml_load_string($content, NULL, LIBXML_NOCDATA);
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

	public function ajaxProcessReloadModulesList()
	{
		if (Tools::getValue('filterCategory'))
			Configuration::updateValue('PS_SHOW_CAT_MODULES_'.(int)$this->id_employee, Tools::getValue('filterCategory'));
		if (Tools::getValue('unfilterCategory'))
			Configuration::updateValue('PS_SHOW_CAT_MODULES_'.(int)$this->id_employee, '');

		$this->initContent();
		$this->context->smarty->display('modules/list.tpl');
		die('OK');
	}

	public function ajaxProcessSetFilter()
	{
		$this->setFilterModules(Tools::getValue('module_type'), Tools::getValue('country_module_value'), Tools::getValue('module_install'), Tools::getValue('module_status'));
		die('OK');
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

		$url = preg_replace('#(?<=&|\?)(' . implode('|', $remove) . ')=.*?(&|$)#i', '', $url);
		$len = strlen($url);
		if ($url[$len - 1] == '&')
			$url = substr($url, 0, $len - 1);
		return $url;
	}

	private function extractArchive($file, $redirect = true)
	{
		$success = false;
		if (substr($file, -4) == '.zip')
		{
			if (Tools::ZipExtract($file, _PS_MODULE_DIR_))
				$success = true;
			else
				$this->_errors[] = Tools::displayError('Error while extracting module (file may be corrupted).');
		}
		else
		{
			$archive = new Archive_Tar($file);
			if ($archive->extract(_PS_MODULE_DIR_))
				$success = true;
			else
				$this->_errors[] = Tools::displayError('Error while extracting module (file may be corrupted).');
		}

		@unlink($file);
		if ($success && $redirect)
			Tools::redirectAdmin(self::$currentIndex.'&conf=8'.'&token='.$this->token);
	}

	private function recursiveDeleteOnDisk($dir)
	{
		if (strpos(realpath($dir), realpath(_PS_MODULE_DIR_)) === false)
			return ;
		if (is_dir($dir))
		{
			$objects = scandir($dir);
			foreach ($objects as $object)
				if ($object != "." && $object != "..")
				{
					if (filetype($dir."/".$object) == "dir")
						$this->recursiveDeleteOnDisk($dir."/".$object);
					else
						unlink($dir."/".$object);
				}
			reset($objects);
			rmdir($dir);
		}
	}






	/*
	** Filter Configuration Methods
	** Set and reset filter configuration
	*/

	private function setFilterModules($module_type, $country_module_value, $module_install, $module_status)
	{
		Configuration::updateValue('PS_SHOW_TYPE_MODULES_'.(int)$this->id_employee, $module_type);
		Configuration::updateValue('PS_SHOW_COUNTRY_MODULES_'.(int)$this->id_employee, $country_module_value);
		Configuration::updateValue('PS_SHOW_INSTALLED_MODULES_'.(int)$this->id_employee, $module_install);
		Configuration::updateValue('PS_SHOW_ENABLED_MODULES_'.(int)$this->id_employee, $module_status);
	}

	private function resetFilterModules()
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
					$this->_errors[] = Tools::displayError('You do not have the permission to use this module');
				else
				{
					if ($module->uninstall())
						if ($module->install())
							Tools::redirectAdmin(self::$currentIndex.'&conf=21'.'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.$module->name.'&anchor=anchor'.ucfirst($module->name));
						else
							$this->_errors[] = Tools::displayError('Cannot install module');
					else
						$this->_errors[] = Tools::displayError('Cannot uninstall module');
				}
			}
			else
				$this->_errors[] = Tools::displayError('Cannot load module object');
		}
		else
			$this->_errors[] = Tools::displayError('You do not have permission to add here.');
	}

	public function postProcessDownload()
	{
	 	// PrestaShop demo mode
		if (_PS_MODE_DEMO_)
		{
			$this->_errors[] = Tools::displayError('This functionnality has been disabled.');
			return;
		}

		// Try to upload and unarchive the module
	 	if ($this->tabAccess['add'] === '1')
		{
			if (!isset($_FILES['file']['tmp_name']) OR empty($_FILES['file']['tmp_name']))
				$this->_errors[] = $this->l('no file selected');
			elseif (substr($_FILES['file']['name'], -4) != '.tar' AND substr($_FILES['file']['name'], -4) != '.zip' AND substr($_FILES['file']['name'], -4) != '.tgz' AND substr($_FILES['file']['name'], -7) != '.tar.gz')
				$this->_errors[] = Tools::displayError('Unknown archive type');
			elseif (!@copy($_FILES['file']['tmp_name'], _PS_MODULE_DIR_.$_FILES['file']['name']))
				$this->_errors[] = Tools::displayError('An error occurred while copying archive to module directory.');
			else
				$this->extractArchive(_PS_MODULE_DIR_.$_FILES['file']['name']);
		}
		else
			$this->_errors[] = Tools::displayError('You do not have permission to add here.');
	}

	public function postProcessEnable()
	{
	 	if ($this->tabAccess['edit'] === '1')
		{
			$module = Module::getInstanceByName(Tools::getValue('module_name'));
			if (Validate::isLoadedObject($module))
			{
				if (!$module->getPermission('configure'))
					$this->_errors[] = Tools::displayError('You do not have the permission to use this module');
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
				$this->_errors[] = Tools::displayError('Cannot load module object');
		}
		else
			$this->_errors[] = Tools::displayError('You do not have permission to add here.');
	}

	public function postProcessDelete()
	{
		 	if ($this->tabAccess['delete'] === '1')
			{
				if (Tools::getValue('module_name') != '')
				{
					$module = Module::getInstanceByName(Tools::getValue('module_name'));
					if (Validate::isLoadedObject($module) AND !$module->getPermission('configure'))
						$this->_errors[] = Tools::displayError('You do not have the permission to use this module');
					else
					{
						$moduleDir = _PS_MODULE_DIR_.str_replace(array('.', '/', '\\'), array('', '', ''), Tools::getValue('module_name'));
						$this->recursiveDeleteOnDisk($moduleDir);
						Tools::redirectAdmin(self::$currentIndex.'&conf=22&token='.$this->token.'&tab_module='.Tools::getValue('tab_module').'&module_name='.Tools::getValue('module_name'));
					}
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
	}

	public function postProcessCallback()
	{
	 	$return = false;
		foreach ($this->map as $key => $method)
		{
			$modules = Tools::getValue($key);
			if (strpos($modules, '|'))
				$modules = explode('|', $modules);
			else
				$modules = empty($modules) ? false : array($modules);
			$module_errors = array();
			if ($modules)
				foreach ($modules AS $name)
				{
					// If Addons module, download and unzip it before installing it
					if (!is_dir('../modules/'.$name.'/'))
					{
						$filesList = array(
							array('type' => 'addonsNative', 'file' => $this->cache_file_default_country_modules_list, 'loggedOnAddons' => 0),
							array('type' => 'addonsBought', 'file' => $this->cache_file_customer_modules_list, 'loggedOnAddons' => 1),
						);
						foreach ($filesList as $f)
							if (file_exists(_PS_ROOT_DIR_.$f['file']))
							{
								$file = $f['file'];
								$content = Tools::file_get_contents(_PS_ROOT_DIR_.$file);
								$xml = @simplexml_load_string($content, NULL, LIBXML_NOCDATA);
								foreach ($xml->module as $modaddons)
									if ($name == $modaddons->name && isset($modaddons->id) && ($this->logged_on_addons || $f['loggedOnAddons'] == 0))
									{
										if ($f['loggedOnAddons'] == 0)
										{
											if (@copy($this->addons_url_http.'module/'.pSQL($modaddons->id).'/', '../modules/'.$modaddons->name.'.zip'))
												$this->extractArchive('../modules/'.$modaddons->name.'.zip', false);
											else if (@copy($this->addons_url.'module/'.pSQL($modaddons->id).'/', '../modules/'.$modaddons->name.'.zip'))
												$this->extractArchive('../modules/'.$modaddons->name.'.zip', false);
										}
										if ($f['loggedOnAddons'] == 1 && $this->logged_on_addons)
											if (@copy($this->addons_url.'module/'.pSQL($modaddons->id).'/'.pSQL(trim($this->context->cookie->username_addons)).'/'.pSQL(trim($this->context->cookie->password_addons)), '../modules/'.$modaddons->name.'.zip'))
												$this->extractArchive('../modules/'.$modaddons->name.'.zip', false);
									}
							}

					}

					// Check potential error
					if (!($module = Module::getInstanceByName(urldecode($name))))
						$this->_errors[] = $this->l('module not found');
					elseif ($key == 'install' AND $this->tabAccess['add'] !== '1')
						$this->_errors[] = Tools::displayError('You do not have permission to install a module.');
					elseif ($key == 'uninstall' AND ($this->tabAccess['delete'] !== '1' OR !$module->getPermission('configure')))
						$this->_errors[] = Tools::displayError('You do not have permission to delete this module.');
					elseif ($key == 'configure' AND ($this->tabAccess['edit'] !== '1' OR !$module->getPermission('configure')))
						$this->_errors[] = Tools::displayError('You do not have permission to configure this module.');
					elseif ($key == 'install' AND Module::isInstalled($module->name))
						$this->_errors[] = Tools::displayError('This module is already installed:').' '.$module->name;
					elseif ($key == 'uninstall' AND !Module::isInstalled($module->name))
						$this->_errors[] = Tools::displayError('This module is already uninstalled:').' '.$module->name;
					else
					{
						// If we install a module, force temporary global context for multishop
						if (Shop::isFeatureActive() && Context::shop() != Shop::CONTEXT_ALL)
						{
							// If we install a module, force temporary global context for multishop
							if (Shop::isFeatureActive() && Context::shop() != Shop::CONTEXT_ALL && $method != 'getContent')
							{
								Context::getContext()->tmpOldShop = clone(Context::getContext()->shop);
								Context::getContext()->shop = new Shop();
								Configuration::updateValue('RSS_FEED_TITLE', 'lol');
							}
						}

						if (((method_exists($module, $method) && ($echo = $module->{$method}()))) AND $key == 'configure' AND Module::isInstalled($module->name))
						{
							$backlink = self::$currentIndex.'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.$module->name;
							$hooklink = 'index.php?tab=AdminModulesPositions&token='.Tools::getAdminTokenLite('AdminModulesPositions').'&show_modules='.(int)$module->id;
							$tradlink = 'index.php?tab=AdminTranslations&token='.Tools::getAdminTokenLite('AdminTranslations').'&type=modules&lang=';

							$toolbar = '<table class="table" cellpadding="0" cellspacing="0" style="margin:auto;text-align:center"><tr>
									<th>'.$this->l('Module').' <span style="color: green;">'.$module->name.'</span></th>
									<th><a href="'.$backlink.'" style="padding:5px 10px">'.$this->l('Back').'</a></th>
									<th><a href="'.$hooklink.'" style="padding:5px 10px">'.$this->l('Manage hooks').'</a></th>
									<th style="padding:5px 10px">'.$this->l('Manage translations:').' ';
									foreach (Language::getLanguages(false) AS $language)
										$toolbar .= '<a href="'.$tradlink.$language['iso_code'].'#'.$module->name.'" style="margin-left:5px"><img src="'._THEME_LANG_DIR_.$language['id_lang'].'.jpg" alt="'.$language['iso_code'].'" title="'.$language['iso_code'].'" /></a>';
							$toolbar .= '</th></tr>';

							// Display checkbox in toolbar if multishop
							if (Shop::isFeatureActive())
							{
								$activateOnclick = 'onclick="location.href = \''.$this->getCurrentUrl('enable').'&enable=\'+(($(this).attr(\'checked\')) ? 1 : 0)"';
								$toolbar .= '<tr>
										<th colspan="4">
											<input type="checkbox" name="activateModule" value="1" '.(($module->active) ? 'checked="checked"' : '').' '.$activateOnclick.' /> '.$this->l('Activate module for').' ';
								if ($this->context->shop->getContextType() == Shop::CONTEXT_SHOP)
									$toolbar .= 'shop <b>'.$this->context->shop->name.'</b>';
								elseif ($this->context->shop->getContextType() == Shop::CONTEXT_GROUP)
									$toolbar .= 'all shops of group shop <b>'.$this->context->shop->getGroup()->name.'</b>';
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
						elseif($echo === true)
							$return = ($method == 'install' ? 12 : 13);
						elseif ($echo === false)
							$module_errors[] = array('name' => $name, 'message' => $module->getErrors());

						if (Shop::isFeatureActive() && Context::shop() != Shop::CONTEXT_ALL && isset(Context::getContext()->tmpOldShop))
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
				$this->_errors[] = sprintf(Tools::displayError('The following module(s) were not installed successfully: %s'), $html_error);
			}
		}
		if ($return)
			Tools::redirectAdmin(self::$currentIndex.'&conf='.$return.'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.$module->name.'&anchor=anchor'.ucfirst($module->name));
	}
	
	public function postProcess()
	{
		// Parent Post Process
		parent::postProcess();

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
	private function generateHtmlMessage($module_errors)
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
	private $translationsTab = array();
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
			$this->translationsTab['This action will permanently remove the module from the server. Are you sure you want to do this ?'] = $this->l('This action will permanently remove the module from the server. Are you sure you want to do this ?');
		}	
			
		$return = '';
		$href = self::$currentIndex.'&token='.$this->token.'&module_name='.urlencode($module->name).'&tab_module='.$module->tab;
		if ($module->id)
			$return .= ' <span class="desactive-module"><a class="action_module" '.($module->active && method_exists($module, 'onclickOption')? 'onclick="'.$module->onclickOption('desactive', $href).'"' : '').' href="'.self::$currentIndex.'&token='.$this->token.'&module_name='.urlencode($module->name).'&'.($module->active ? 'enable=0' : 'enable=1').'&tab_module='.$module->tab.'" '.((Shop::isFeatureActive()) ? 'title="'.htmlspecialchars($module->active ? $this->translationsTab['Disable this module'] : $this->translationsTab['Enable this module for all shops']).'"' : '').'>'.($module->active ? $this->translationsTab['Disable'] : $this->translationsTab['Enable']).'</a></span>';

		if ($module->id AND $module->active)
			$return .= (!empty($result) ? '|' : '').' <span class="reset-module"><a class="action_module" '.(method_exists($module, 'onclickOption')? 'onclick="'.$module->onclickOption('reset', $href).'"' : '').' href="'.self::$currentIndex.'&token='.$this->token.'&module_name='.urlencode($module->name).'&reset&tab_module='.$module->tab.'">'.$this->translationsTab['Reset'].'</a></span>';

		if ($module->id AND (method_exists($module, 'getContent') OR (isset($module->is_configurable) AND $module->is_configurable)))
			$return .= (!empty($result) ? '|' : '').' <span class="configure-module"><a class="action_module" '.(method_exists($module, 'onclickOption')? 'onclick="'.$module->onclickOption('configure', $href).'"' : '').' href="'.self::$currentIndex.'&configure='.urlencode($module->name).'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.urlencode($module->name).'">'.$this->translationsTab['Configure'].'</a></span>';

		$hrefDelete = self::$currentIndex.'&delete='.urlencode($module->name).'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.urlencode($module->name);
		$return .= (!empty($result) ? '|' : '').' <span class="delete-module"><a class="action_module" '.(method_exists($module, 'onclickOption')? 'onclick="'.$module->onclickOption('delete', $hrefDelete).'"' : '').' onclick="return confirm(\''.$this->translationsTab['This action will permanently remove the module from the server. Are you sure you want to do this ?'].'\');" href="'.$hrefDelete.'">'.$this->translationsTab['Delete'].'</a></span>';

		return $return;
	}

	public function initModulesList($modules)
	{
		foreach ($modules AS $module)
		{
			if (!in_array($module->name, $this->list_natives_modules))
				$this->serial_modules .= $module->name.' '.$module->version.'-'.($module->active ? 'a' : 'i')."\n";
			$module_author = $module->author;
			if (!empty($module_author)&& ($module_author != ""))
				$this->modules_authors[(string)$module_author] = 'notselected';
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
		// Beware $module could be an instance of Module or stdClass, that explain the static call
		if ($module->id AND !Module::getPermissionStatic($module->id, 'view') AND !Module::getPermissionStatic($module->id, 'configure'))
			return true;


		// Filter on module category
		$categoryFiltered = array();
		$filterCategories = explode('|', Configuration::get('PS_SHOW_CAT_MODULES_'.(int)$this->id_employee));
		if (count($filterCategories) > 0)
			foreach ($filterCategories as $fc)
				if (!empty($fc))
					$categoryFiltered[$fc] = 1;
		if (count($categoryFiltered) > 0 && !isset($categoryFiltered[$module->tab]))
			return true;


		// Filter on module type and author
		$show_type_modules = $this->filter_configuration['PS_SHOW_TYPE_MODULES_'.(int)$this->id_employee];
		if ($show_type_modules == 'nativeModules' && !in_array($module->name, $this->list_natives_modules))
			return true;
		else if ($show_type_modules == 'partnerModules' && !in_array($module->name, $this->list_partners_modules))
			return true;
		else if ($show_type_modules == 'otherModules' && (in_array($module->name, $this->list_partners_modules) OR in_array($module->name, $this->list_natives_modules)))
			return true;
		else if (strpos($show_type_modules, 'authorModules[') !== false)
		{
			// setting selected author in authors set
			$author_selected = substr(str_replace(array('authorModules[', "\'"), array('', "'"), $show_type_modules), 0, -1);
			$this->modules_authors[$author_selected] = 'selected';
			if (empty($module->author) || $module->author != $author_selected)
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
		if ($show_country_modules AND (isset($module->limited_countries) AND !empty($module->limited_countries) AND ((is_array($module->limited_countries) AND sizeof($module->limited_countries) AND !in_array(strtolower($this->iso_default_country), $module->limited_countries)) OR (!is_array($module->limited_countries) AND strtolower($this->iso_default_country) != strval($module->limited_countries)))))
			return true;


		// Filter on module name
		$filter_name = Tools::getValue('filtername');
		if (!empty($filter_name) AND (stristr($module->name, $filter_name) === false AND stristr($module->displayName, $filter_name) === false AND stristr($module->description, $filter_name) === false))
			return true;


		// Module has not been filtered		
		return false;
	}

	public function initContent()
	{
		// Adding Css
		$this->addCSS(__PS_BASE_URI__.str_replace(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR, '', _PS_ADMIN_DIR_).'/themes/default/modules.css', 'all');

		// Init
		$smarty = $this->context->smarty;
		$autocompleteList = 'var moduleList = [';
		$nameCountryDefault = Country::getNameById($this->context->language->id, Configuration::get('PS_COUNTRY_DEFAULT'));
		$categoryFiltered = array();
		$filterCategories = explode('|', Configuration::get('PS_SHOW_CAT_MODULES_'.(int)$this->id_employee));
		if (count($filterCategories) > 0)
			foreach ($filterCategories as $fc)
				$categoryFiltered[$fc] = 1;
				
		foreach ($this->list_modules_categories as $k => $v)
			$this->list_modules_categories[$k]['nb'] = 0;
		
		// Retrieve Modules List
		$modules = Module::getModulesOnDisk(true, $this->logged_on_addons);
		$this->initModulesList($modules);
		$this->nb_modules_total = count($modules);

		$module_errors = array();
		$module_success = array();

		// Browse modules list
		foreach ($modules as $km => $module)
		{
			// Upgrade Module process, init check if a module could be upgraded
			if (Module::initUpgradeModule($module->name, $module->version))
				if ($object = new $module->name())
				{
					$object->runUpgradeModule();
					if ((count($errors_module_list = $object->getErrors())))
						$module_errors[] = array('name' => $module->name, 'message' => $errors_module_list);
					else if ((count($conf_module_list = $object->getConfirmations())))
						$module_success[] = array('name' => $module->name, 'message' => $conf_module_list);
				}

			// Make modules stats
			$this->makeModulesStats($module);

			// Assign warnings
			if (isset($module->warning) && !empty($module->warning))
				$this->warnings[] = '<b>'.$module->displayName.' :</b> '.$module->warning;

			// Apply filter
			if ($this->isModuleFiltered($module))
				unset($modules[$km]);
			else
			{
				// Fill module data
				$modules[$km]->logo = '../../img/questionmark.png';
				if (file_exists('../modules/'.$module->name.'/logo.gif'))
					$modules[$km]->logo = 'logo.gif';
				if (file_exists('../modules/'.$module->name.'/logo.png'))
					$modules[$km]->logo = 'logo.png';
				$modules[$km]->optionsHtml = $this->displayModuleOptions($module);
				$modules[$km]->categoryName = (isset($this->list_modules_categories[$module->tab]['name']) ? $this->list_modules_categories[$module->tab]['name'] : $this->list_modules_categories['others']['name']);
				$modules[$km]->options['install_url'] = self::$currentIndex.'&install='.urlencode($module->name).'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.$module->name.'&anchor=anchor'.ucfirst($module->name);
				$modules[$km]->options['uninstall_url'] = self::$currentIndex.'&uninstall='.urlencode($module->name).'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.$module->name.'&anchor=anchor'.ucfirst($module->name);
				$modules[$km]->options['uninstall_onclick'] = ((!method_exists($module, 'onclickOption')) ? ((empty($module->confirmUninstall)) ? '' : 'return confirm(\''.addslashes($module->confirmUninstall).'\');') : $module->onclickOption('uninstall', $modules[$km]->options['uninstall_url']));
				if (Tools::getValue('module_name') == $module->name && (int)Tools::getValue('conf') > 0)
					$modules[$km]->message = $this->_conf[(int)Tools::getValue('conf')];

				// AutoComplete array
				$autocompleteList .= Tools::jsonEncode(array(
					'displayName' => (string)$module->displayName,
					'desc' => (string)$module->description,
					'name' => (string)$module->name,
					'author' => (string)$module->author,
					'image' => (isset($module->image) ? (string)$module->image : ''),
					//'option' => $this->displayModuleOptions($module),
					'option' => '',
				)).', ';
			}
			unset($object);
		}

		// Actually used for the report of the upgraded errors
		if (count($module_errors))
		{
			$html = $this->generateHtmlMessage($module_errors);
			$this->_errors[] = sprintf(Tools::displayError('The following module(s) were not upgraded successfully: %s'), $html);
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

		$tpl_vars['addonsUrl'] = 'index.php?tab=AdminAddonsMyAccount&token='.Tools::getAdminTokenLite('AdminAddonsMyAccount');
		$tpl_vars['categoryFiltered'] = $categoryFiltered;

		$tpl_vars['modules'] = $modules;
		$tpl_vars['nb_modules'] = $this->nb_modules_total;
		$tpl_vars['nb_modules_installed'] = $this->nb_modules_installed;
		$tpl_vars['nb_modules_uninstalled'] = $tpl_vars['nb_modules'] - $tpl_vars['nb_modules_installed'];
		$tpl_vars['nb_modules_activated'] = $this->nb_modules_activated;
		$tpl_vars['nb_modules_unactivated'] = $tpl_vars['nb_modules_installed'] - $tpl_vars['nb_modules_activated'];
		$tpl_vars['list_modules_categories'] = $this->list_modules_categories;
		$tpl_vars['list_modules_authors'] = $this->modules_authors;

		if ($this->logged_on_addons)
			$tpl_vars['logged_on_addons'] = 1;

		$smarty->assign($tpl_vars);
	}

}
