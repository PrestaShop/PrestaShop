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

include_once(_PS_ADMIN_DIR_.'/../tools/tar/Archive_Tar.php');

class AdminModulesControllerCore extends AdminController
{
	/** @var array map with $_GET keywords and their callback */
	private $map = array(
		'install' => 'install',
		'uninstall' => 'uninstall',
		'configure' => 'getContent',
		'delete' => 'delete'
	);

	private $list_modules_categories = array();
	private $list_partners_modules = array();
	private $list_natives_modules = array();
	private $cache_file_modules_list = '/config/modules_list.xml';
 	private $xml_modules_list = 'http://www.prestashop.com/xml/modules_list.xml';

	private $nb_modules_total = 0;
	private $nb_modules_installed = 0;
	private $nb_modules_activated = 0;

	private $serial_modules = '';
	private $modules_authors = array();

	public function __construct()
	{
		parent::__construct();

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

		// Load cache file modules list (natives and partners modules)
		if (file_exists(_PS_ROOT_DIR_.$this->cache_file_modules_list))
			$xmlModules = @simplexml_load_file(_PS_ROOT_DIR_.$this->cache_file_modules_list);
		else
			$xmlModules = false;
		if ($xmlModules)
		{
			foreach($xmlModules->children() as $xmlModule)
			{
				if ($xmlModule->attributes() == 'native')
					foreach($xmlModule->children() as $module)
						foreach($module->attributes() as $key => $value)
							if ($key == 'name')
								$this->list_natives_modules[] = (string)$value;

				if ($xmlModule->attributes() == 'partner')
					foreach ($xmlModule->children() as $module)
						foreach ($module->attributes() as $key => $value)
							if ($key == 'name')
								$this->list_partners_modules[] = (string)$value;
			}
		}
	}

	/** if modules_list.xml is outdated,
	 * this function will re-upload it from prestashop.com
	 *
	 * @return null
	 */
	public function ajaxProcessRefreshModuleList()
	{
		//refresh modules_list.xml every week
		if (!$this->isFresh())
		{
			if ($this->refresh())
				$this->status = 'refresh';
			else
				$this->status = 'error';
		}
		else
			$this->status = 'cache';
	}


	public function isFresh($timeout = 604800000)
	{
		if (file_exists(_PS_ROOT_DIR_ . $this->_moduleCacheFile))
			return ((time() - filemtime(_PS_ROOT_DIR_ . $this->_moduleCacheFile)) < $timeout);
		else
			return false;
	}

	public function refresh()
	{
		return file_put_contents(_PS_ROOT_DIR_.$this->_moduleCacheFile, Tools::file_get_contents($this->xml_modules_list));
	}

	public function displayAjaxRefreshModuleList()
	{
		echo Tools::jsonEncode(array('status' => $this->status));
	}

	private function setFilterModules($module_type, $country_module_value, $module_install, $module_status)
	{
		$this->context = Context::getContext();
		Configuration::updateValue('PS_SHOW_TYPE_MODULES_'.(int)$this->context->employee->id, $module_type);
		Configuration::updateValue('PS_SHOW_COUNTRY_MODULES_'.(int)$this->context->employee->id, $country_module_value);
		Configuration::updateValue('PS_SHOW_INSTALLED_MODULES_'.(int)$this->context->employee->id, $module_install);
		Configuration::updateValue('PS_SHOW_ENABLED_MODULES_'.(int)$this->context->employee->id, $module_status);
	}

	private function resetFilterModules()
	{
		$this->context = Context::getContext();
		Configuration::updateValue('PS_SHOW_TYPE_MODULES_'.(int)$this->context->employee->id, 'allModules');
		Configuration::updateValue('PS_SHOW_COUNTRY_MODULES_'.(int)$this->context->employee->id, 0);
		Configuration::updateValue('PS_SHOW_INSTALLED_MODULES_'.(int)$this->context->employee->id, 'installedUninstalled');
		Configuration::updateValue('PS_SHOW_ENABLED_MODULES_'.(int)$this->context->employee->id, 'enabledDisabled');
	}

	/**
	 * Get current URL
	 *
	 * @param array $remove List of keys to remove from URL
	 * @return string
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

	private function _getSubmitedModuleAuthor($value)
	{
		$value = str_replace('authorModules[', '', $value);
		$value = str_replace("\'", "'", $value);

		$value = substr($value, 0, -1);
		return $value;
	}

	public function postProcess()
	{
		$id_employee = (int)$this->context->employee->id;
		$filter_conf = Configuration::getMultiple(array(
			'PS_SHOW_TYPE_MODULES_'.$id_employee,
			'PS_SHOW_COUNTRY_MODULES_'.$id_employee,
			'PS_SHOW_INSTALLED_MODULES_'.$id_employee,
			'PS_SHOW_ENABLED_MODULES_'.$id_employee
		));

		if (Tools::isSubmit('desactive') && isset($filter_conf['PS_SHOW_ENABLED_MODULES_'.$id_employee]) && $filter_conf['PS_SHOW_ENABLED_MODULES_'.$id_employee] != 'enabledDisabled')
			$this->setFilterModules($filter_conf['PS_SHOW_TYPE_MODULES_'.$id_employee], $filter_conf['PS_SHOW_COUNTRY_MODULES_'.$id_employee], $filter_conf['PS_SHOW_INSTALLED_MODULES_'.$id_employee], 'disabled');

		if (Tools::isSubmit('active') && isset($filter_conf['PS_SHOW_ENABLED_MODULES_'.$id_employee]) && $filter_conf['PS_SHOW_ENABLED_MODULES_'.$id_employee] != 'enabledDisabled')
			$this->setFilterModules($filter_conf['PS_SHOW_TYPE_MODULES_'.$id_employee], $filter_conf['PS_SHOW_COUNTRY_MODULES_'.$id_employee], $filter_conf['PS_SHOW_INSTALLED_MODULES_'.$id_employee], 'enabled');

		if (Tools::isSubmit('uninstall') && isset($filter_conf['PS_SHOW_INSTALLED_MODULES_'.$id_employee]) && $filter_conf['PS_SHOW_INSTALLED_MODULES_'.$id_employee] != 'installedUninstalled')
			$this->setFilterModules($filter_conf['PS_SHOW_TYPE_MODULES_'.$id_employee], $filter_conf['PS_SHOW_COUNTRY_MODULES_'.$id_employee], 'unistalled', $filter_conf['PS_SHOW_ENABLED_MODULES_'.$id_employee]);

		if (Tools::isSubmit('install') && isset($filter_conf['PS_SHOW_INSTALLED_MODULES_'.$id_employee]) && $filter_conf['PS_SHOW_INSTALLED_MODULES_'.$id_employee] != 'installedUninstalled')
			$this->setFilterModules($filter_conf['PS_SHOW_TYPE_MODULES_'.$id_employee], $filter_conf['PS_SHOW_COUNTRY_MODULES_'.$id_employee], 'installed', $filter_conf['PS_SHOW_ENABLED_MODULES_'.$id_employee]);

		if (Tools::getValue('filterCategory') != '')
		{
			$newFilterCategory = Tools::getValue('filterCategory');
			$filterCategories = explode('|', Configuration::get('PS_SHOW_CAT_MODULES_'.(int)$this->context->employee->id));
			
			// Check if category is not already filtered
			foreach ($filterCategories as $fc)
				if ($fc == $newFilterCategory)
					$newFilterCategory = '';

			// Add the new filter
			if ($newFilterCategory != '')
				$filterCategories[] = $newFilterCategory;
			$filterCategories = implode('|', $filterCategories);
			
			Configuration::updateValue('PS_SHOW_CAT_MODULES_'.(int)$this->context->employee->id, $filterCategories);
			Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
		}

		if (Tools::getValue('unfilterCategory') != '')
		{
			$unfilterCategory = Tools::getValue('unfilterCategory');
			$filterCategories = explode('|', Configuration::get('PS_SHOW_CAT_MODULES_'.(int)$this->context->employee->id));
			
			// Remove the category
			foreach ($filterCategories as $k => $fc)
				if ($fc == $unfilterCategory)
					unset($filterCategories[$k]);

			$filterCategories = implode('|', $filterCategories);
			Configuration::updateValue('PS_SHOW_CAT_MODULES_'.(int)$this->context->employee->id, $filterCategories);
			Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
		}

		if (Tools::isSubmit('filterModules'))
		{
			$this->setFilterModules(Tools::getValue('module_type'), Tools::getValue('country_module_value'), Tools::getValue('module_install'), Tools::getValue('module_status'));
			Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
		}
		elseif (Tools::isSubmit('resetFilterModules'))
		{
			$this->resetFilterModules();
			Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
		}

		if (Tools::isSubmit('active'))
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
						$module->enable();
						Tools::redirectAdmin(self::$currentIndex.'&conf=5&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.$module->name);
					}
				}
				else
					$this->_errors[] = Tools::displayError('Cannot load module object');
			} else
				$this->_errors[] = Tools::displayError('You do not have permission to add here.');
		}
		elseif (Tools::isSubmit('desactive'))
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
						$module->disable();
						Tools::redirectAdmin(self::$currentIndex.'&conf=5&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.$module->name);
					}
				}
				else
					$this->_errors[] = Tools::displayError('Cannot load module object');
			} else
				$this->_errors[] = Tools::displayError('You do not have permission to add here.');
		}
		elseif (Tools::isSubmit('reset'))
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
								Tools::redirectAdmin(self::$currentIndex.'&conf=21'.'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.$module->name);
							else
								$this->_errors[] = Tools::displayError('Cannot install module');
						else
							$this->_errors[] = Tools::displayError('Cannot uninstall module');
					}
				}
				else
					$this->_errors[] = Tools::displayError('Cannot load module object');
			} else
				$this->_errors[] = Tools::displayError('You do not have permission to add here.');

		}
		/* Automatically copy a module from external URL and unarchive it in the appropriated directory */
		if (Tools::isSubmit('submitDownload2'))
		{
		 	/* PrestaShop demo mode */
			if (_PS_MODE_DEMO_)
			{
				$this->_errors[] = Tools::displayError('This functionnality has been disabled.');
				return;
			}
			/* PrestaShop demo mode*/
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

		// Enable / disable module
		if (Tools::getValue('enable') !== false)
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

		if (Tools::isSubmit('deleteModule'))
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

		/* Call appropriate module callback */
		else
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
								Context::getContext()->tmpOldShop = clone(Context::getContext()->shop);
								Context::getContext()->shop = new Shop();
								Configuration::updateValue('RSS_FEED_TITLE', 'lol');
							}

							if (((method_exists($module, $method) && ($echo = $module->{$method}())) || ($echo = ' ')) AND $key == 'configure' AND Module::isInstalled($module->name))
							{
								$backlink = self::$currentIndex.'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.$module->name;
								$hooklink = 'index.php?tab=AdminModulesPositions&token='.Tools::getAdminTokenLite('AdminModulesPositions').'&show_modules='.(int)$module->id;
								$tradlink = 'index.php?tab=AdminTranslations&token='.Tools::getAdminTokenLite('AdminTranslations').'&type=modules&lang=';

								$toolbar = '
								<table class="table" cellpadding="0" cellspacing="0" style="margin:auto;text-align:center">
									<tr>
										<th>'.$this->l('Module').' <span style="color: green;">'.$module->name.'</span></th>
										<th><a href="'.$backlink.'" style="padding:5px 10px">'.$this->l('Back').'</a></th>
										<th><a href="'.$hooklink.'" style="padding:5px 10px">'.$this->l('Manage hooks').'</a></th>
										<th style="padding:5px 10px">'.$this->l('Manage translations:').' ';
										foreach (Language::getLanguages(false) AS $language)
											$toolbar .= '<a href="'.$tradlink.$language['iso_code'].'#'.$module->name.'" style="margin-left:5px"><img src="'._THEME_LANG_DIR_.$language['id_lang'].'.jpg" alt="'.$language['iso_code'].'" title="'.$language['iso_code'].'" /></a>';
								$toolbar .= '
										</th>
									</tr>';

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

								// Display configure page
								// TODO : MAKE SOMETHING CLEANER
								$this->context->smarty->assign('module_content', $toolbar.'<div class="clear">&nbsp;</div>'.$echo.'<div class="clear">&nbsp;</div>'.$toolbar);
							}
							elseif($echo)
								$return = ($method == 'install' ? 12 : 13);
							elseif ($echo === false)
								$module_errors[] = $name;

							if (Shop::isFeatureActive() && Context::shop() != Shop::CONTEXT_ALL && isset(Context::getContext()->tmpOldShop))
							{
								Context::getContext()->shop = clone(Context::getContext()->tmpOldShop);
								unset(Context::getContext()->tmpOldShop);
							}
						}
						if ($key != 'configure' && isset($_GET['bpay']))
							Tools::redirectAdmin('index.php?tab=AdminPayment&token='.Tools::getAdminToken('AdminPayment'.(int)(Tab::getIdFromClassName('AdminPayment')).(int)$this->context->employee->id));
					}
				if (count($module_errors))
				{
					// If error during module installation, no redirection
					$html_error = '<ul>';
					foreach ($module_errors as $module_error)
						$html_error .= '<li>'.$module_error.'</li>';
					$html_error .= '</ul>';

					$this->_errors[] = sprintf(Tools::displayError('The following module(s) were not installed successfully: %s'), $html_error);
				}
			}
			if ($return)
				Tools::redirectAdmin(self::$currentIndex.'&conf='.$return.'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.$module->name);
		}
	}

	public function extractArchive($file)
	{
		$success = false;
		if (substr($file, -4) == '.zip')
		{
			if (!Tools::ZipExtract($file, _PS_MODULE_DIR_))
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
		if ($success)
			Tools::redirectAdmin(self::$currentIndex.'&conf=8'.'&token='.$this->token);
	}

	public static function sortModule($a, $b)
	{
	    if (sizeof($a) == sizeof($b)) {
	        return 0;
	    }
	    return (sizeof($a) < sizeof($b)) ? -1 : 1;
	}



	public function recursiveDeleteOnDisk($dir)
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



	// TODO : CHANGE THESE TWO METHODS INTO TPL
	public function displayModuleOptions($module)
	{		
		$return = '';
		$href = self::$currentIndex.'&token='.$this->token.'&module_name='.urlencode($module->name).'&tab_module='.$module->tab;
		if ($module->id)
			$return .= '<span class="desactive-module"><a class="action_module" '.($module->active && method_exists($module, 'onclickOption')? 'onclick="'.$module->onclickOption('desactive', $href).'"' : '').' href="'.self::$currentIndex.'&token='.$this->token.'&module_name='.urlencode($module->name).'&'.($module->active ? 'enable=0' : 'enable=1').'&tab_module='.$module->tab.'" '.((Shop::isFeatureActive()) ? 'title="'.htmlspecialchars($module->active ? $this->l('Disable this module') : $this->l('Enable this module for all shops')).'"' : '').'>'.($module->active ? $this->l('Disable') : $this->l('Enable')).'</a></span>';

		if ($module->id AND $module->active)
			$return .= (!empty($result) ? '|' : '').'<span class="reset-module"><a class="action_module" '.(method_exists($module, 'onclickOption')? 'onclick="'.$module->onclickOption('reset', $href).'"' : '').' href="'.self::$currentIndex.'&token='.$this->token.'&module_name='.urlencode($module->name).'&reset&tab_module='.$module->tab.'">'.$this->l('Reset').'</a></span>';

		if ($module->id AND (method_exists($module, 'getContent') OR (isset($module->is_configurable) AND $module->is_configurable) OR Shop::isFeatureActive()))
			$return .= (!empty($result) ? '|' : '').'<span class="configure-module"><a class="action_module" '.(method_exists($module, 'onclickOption')? 'onclick="'.$module->onclickOption('configure', $href).'"' : '').' href="'.self::$currentIndex.'&configure='.urlencode($module->name).'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.urlencode($module->name).'">'.$this->l('Configure').'</a></span>';

		$hrefDelete = self::$currentIndex.'&deleteModule='.urlencode($module->name).'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.urlencode($module->name);
		$return .= (!empty($result) ? '|' : '').'<span class="delete-module"><a class="action_module" '.(method_exists($module, 'onclickOption')? 'onclick="'.$module->onclickOption('delete', $hrefDelete).'"' : '').' onclick="return confirm(\''.$this->l('This action will permanently remove the module from the server. Are you sure you want to do this ?').'\');" href="'.$hrefDelete.'">'.$this->l('Delete').'</a></span>';

		return $return;
	}
	private function displayModuleAuthorsOptGroup($authors, $fieldName = "UNDEFINED")
	{
		$out = '<optgroup label="'.$this->l('Authors').'">';
		foreach($authors as $author_item => $status)
		{
			$author_item = Tools::htmlentitiesUTF8($author_item);
			$disp_author = ((strlen($author_item) > 20) ? substr($author_item, 0, 20).'...' : $author_item);

			$out .= '<option value="'.$fieldName.'['.$author_item. ']"'. (($status === "selected") ? ' selected>' : '>').$disp_author .'</option>';
		}
		$out .= '</optgroup>';
		return $out;
	}



	public function initModulesList($modules)
	{
		foreach ($modules AS $module)
		{
			if (!in_array($module->name, $this->list_natives_modules))
				$this->serial_modules .= $module->name.' '.$module->version.'-'.($module->active ? 'a' : 'i')."\n";
			$module_author = $module->author;
			if (!empty($module_author)&& ($module_author != ""))
				$this->modules_authors[(string)$module_author] = true;
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

	public function moduleFiltered($module)
	{
		$showTypeModules = Configuration::get('PS_SHOW_TYPE_MODULES_'.(int)$this->context->employee->id);
		$showInstalledModules = Configuration::get('PS_SHOW_INSTALLED_MODULES_'.(int)$this->context->employee->id);
		$showEnabledModules = Configuration::get('PS_SHOW_ENABLED_MODULES_'.(int)$this->context->employee->id);
		$showCountryModules = Configuration::get('PS_SHOW_COUNTRY_MODULES_'.(int)$this->context->employee->id);
		$nameCountryDefault = Country::getNameById($this->context->language->id, Configuration::get('PS_COUNTRY_DEFAULT'));
		$isoCountryDefault = $this->context->country->iso_code;
		$filterName = Tools::getValue('filtername');

		$categoryFiltered = array();
		$filterCategories = explode('|', Configuration::get('PS_SHOW_CAT_MODULES_'.(int)$this->context->employee->id));
		if (count($filterCategories) > 0)
			foreach ($filterCategories as $fc)
				if (!empty($fc))
					$categoryFiltered[$fc] = 1;
		if (count($categoryFiltered) > 0 && !isset($categoryFiltered[$module->tab]))
			return true;
		
		

		// beware $module could be an instance of Module or stdClass, that explain the static call
		if ($module->id AND !Module::getPermissionStatic($module->id, 'view') AND !Module::getPermissionStatic($module->id, 'configure'))
			return true;

		switch ($showTypeModules)
		{
			case 'nativeModules':
				if (!in_array($module->name, $this->list_natives_modules))
					return true;
			break;
			case 'partnerModules':
				if (!in_array($module->name, $this->list_partners_modules))
					return true;
			break;
			case 'otherModules':
				if (in_array($module->name, $this->list_partners_modules) OR in_array($module->name, $this->list_natives_modules))
					return true;
			break;
			default:
				if (strpos($showTypeModules, 'authorModules[') !== false)
				{
					$author_selected = $this->_getSubmitedModuleAuthor($showTypeModules);
					$modulesAuthors[$author_selected] = 'selected';		// setting selected author in authors set
					if (empty($module->author) || $module->author != $author_selected)
						return true;
				}
			break;
		}

		switch ($showInstalledModules)
		{
			case 'installed':
				if (!$module->id)
					return true;
			break;
			case 'unistalled':
				if ($module->id)
					return true;
			break;
		}

		switch ($showEnabledModules)
		{
			case 'enabled':
				if (!$module->active)
					return true;
			break;
			case 'disabled':
				if ($module->active)
					return true;
			break;
		}

		if ($showCountryModules AND (isset($module->limited_countries) AND !empty($module->limited_countries) AND ((is_array($module->limited_countries) AND sizeof($module->limited_countries) AND !in_array(strtolower($isoCountryDefault), $module->limited_countries)) OR (!is_array($module->limited_countries) AND strtolower($isoCountryDefault) != strval($module->limited_countries)))))
			return true;

		if (!empty($filterName) AND (stristr($module->name, $filterName) === false AND stristr($module->displayName, $filterName) === false AND stristr($module->description, $filterName) === false))
			return true;
		
		return false;
	}

	public function initContent()
	{
		// Adding Css
		$this->addCSS(__PS_BASE_URI__.str_replace(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR, '', _PS_ADMIN_DIR_).'/themes/default/modules.css', 'all');

		// Init
		$smarty = $this->context->smarty;
		$autocompleteList = 'var moduleList = [';
		$showTypeModules = Configuration::get('PS_SHOW_TYPE_MODULES_'.(int)$this->context->employee->id);
		$showInstalledModules = Configuration::get('PS_SHOW_INSTALLED_MODULES_'.(int)$this->context->employee->id);
		$showEnabledModules = Configuration::get('PS_SHOW_ENABLED_MODULES_'.(int)$this->context->employee->id);
		$showCountryModules = Configuration::get('PS_SHOW_COUNTRY_MODULES_'.(int)$this->context->employee->id);
		$nameCountryDefault = Country::getNameById($this->context->language->id, Configuration::get('PS_COUNTRY_DEFAULT'));
		$isoCountryDefault = $this->context->country->iso_code;
		$categoryFiltered = array();
		$filterCategories = explode('|', Configuration::get('PS_SHOW_CAT_MODULES_'.(int)$this->context->employee->id));
		if (count($filterCategories) > 0)
			foreach ($filterCategories as $fc)
				$categoryFiltered[$fc] = 1;
				
		foreach ($this->list_modules_categories as $k => $v)
			$this->list_modules_categories[$k]['nb'] = 0;
		
		// Retrieve Modules List
		$modules = Module::getModulesOnDisk(true);
		$this->initModulesList($modules);
		$this->nb_modules_total = count($modules);

		// Browse modules list
		foreach ($modules as $km => $module)
		{
			// Make modules stats
			$this->makeModulesStats($module);

			// Apply filter
			if ($this->moduleFiltered($module))
				unset($modules[$km]);
			else
			{
				// Fill module data
				$modules[$km]->logo = 'logo.gif';
				if (file_exists('../modules/'.$module->name.'/logo.png'))
					$modules[$km]->logo = 'logo.png';
				$modules[$km]->optionsHtml = $this->displayModuleOptions($module);
				$modules[$km]->categoryName = (isset($this->list_modules_categories[$module->tab]['name']) ? $this->list_modules_categories[$module->tab]['name'] : $this->list_modules_categories['others']['name']);
				$modules[$km]->options['install_url'] = self::$currentIndex.'&install='.urlencode($module->name).'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.$module->name.'#anchor'.ucfirst($module->name);
				$modules[$km]->options['uninstall_url'] = self::$currentIndex.'&uninstall='.urlencode($module->name).'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.$module->name.'#anchor'.ucfirst($module->name);
				$modules[$km]->options['uninstall_onclick'] = ((!method_exists($module, 'onclickOption')) ? ((empty($module->confirmUninstall)) ? '' : 'if(confirm(\''.addslashes($module->confirmUninstall).'\')) ').'document.location.href=\''.$modules[$km]->options['uninstall_url'].'\'' : $module->onclickOption('uninstall', $modules[$km]->options['uninstall_url']));
				if (Tools::getValue('module_name') == $module->name && (int)Tools::getValue('conf') > 0)
					$modules[$km]->message = $this->_conf[(int)Tools::getValue('conf')];

				// AutoComplete array
				$autocompleteList .= Tools::jsonEncode(array(
					'displayName' => (string)$module->displayName,
					'desc' => (string)$module->description,
					'name' => (string)$module->name,
					'author' => (string)$module->author,
					'option' => $this->displayModuleOptions($module)
				)).', ';
			}
		}

		// Init tpl vars for smarty
		$tpl_vars = array();
		
		$tpl_vars['token'] = $this->token;
		$tpl_vars['currentIndex'] = self::$currentIndex;
		$tpl_vars['dirNameCurrentIndex'] = dirname(self::$currentIndex);
		$tpl_vars['ajaxCurrentIndex'] = str_replace('index', 'ajax-tab', self::$currentIndex);
		$tpl_vars['autocompleteList'] = rtrim($autocompleteList, ' ,').'];';

		$tpl_vars['showTypeModules'] = $showTypeModules;
		$tpl_vars['showInstalledModules'] = $showInstalledModules;
		$tpl_vars['showEnabledModules'] = $showEnabledModules;
		$tpl_vars['showCountryModules'] = $showCountryModules;
		$tpl_vars['nameCountryDefault'] = $nameCountryDefault;
		$tpl_vars['isoCountryDefault'] = $isoCountryDefault;

		$tpl_vars['authorsOptionsList'] = $this->displayModuleAuthorsOptGroup($this->modules_authors, 'authorModules');
		$tpl_vars['addonsUrl'] = 'index.php?tab=AdminAddonsMyAccount&token='.Tools::getAdminTokenLite('AdminAddonsMyAccount');
		$tpl_vars['categoryFiltered'] = $categoryFiltered;

		$tpl_vars['modules'] = $modules;
		$tpl_vars['nb_modules'] = $this->nb_modules_total;
		$tpl_vars['nb_modules_installed'] = $this->nb_modules_installed;
		$tpl_vars['nb_modules_uninstalled'] = $tpl_vars['nb_modules'] - $tpl_vars['nb_modules_installed'];
		$tpl_vars['nb_modules_activated'] = $this->nb_modules_activated;
		$tpl_vars['nb_modules_unactivated'] = $tpl_vars['nb_modules_installed'] - $tpl_vars['nb_modules_activated'];
		$tpl_vars['list_modules_categories'] = $this->list_modules_categories;

		$smarty->assign($tpl_vars);
	}

}
