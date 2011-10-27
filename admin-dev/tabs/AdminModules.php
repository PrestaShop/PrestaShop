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
*  @version  Release: $Revision: 7451 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include_once(_PS_ADMIN_DIR_.'/../tools/tar/Archive_Tar.php');

class AdminModules extends AdminTab
{
	/** @var array map with $_GET keywords and their callback */
	private $map = array(
		'install' => 'install',
		'uninstall' => 'uninstall',
		'configure' => 'getContent',
		'delete' => 'delete'
	);

	private $listTabModules;
	private $listPartnerModules = array();
	private $listNativeModules = array();
	private $_moduleCacheFile = '/config/modules_list.xml';
 	public $xml_modules_list = 'http://www.prestashop.com/xml/modules_list.xml';
	static private $MAX_DISP_AUTHOR = 20;		// maximum length to display


	public function __construct()
	{
		parent::__construct();

		$this->listTabModules['administration'] = $this->l('Administration');
		$this->listTabModules['advertising_marketing'] = $this->l('Advertising & Marketing');
		$this->listTabModules['analytics_stats'] = $this->l('Analytics & Stats');
		$this->listTabModules['billing_invoicing'] = $this->l('Billing & Invoicing');
		$this->listTabModules['checkout'] = $this->l('Checkout');
		$this->listTabModules['content_management'] = $this->l('Content Management');
		$this->listTabModules['export'] = $this->l('Export');
		$this->listTabModules['front_office_features'] = $this->l('Front Office Features');
		$this->listTabModules['i18n_localization'] = $this->l('I18n & Localization');
		$this->listTabModules['merchandizing'] = $this->l('Merchandizing');
		$this->listTabModules['migration_tools'] = $this->l('Migration Tools');
		$this->listTabModules['payments_gateways'] = $this->l('Payments & Gateways');
		$this->listTabModules['payment_security'] = $this->l('Payment Security');
		$this->listTabModules['pricing_promotion'] = $this->l('Pricing & Promotion');
		$this->listTabModules['quick_bulk_update'] = $this->l('Quick / Bulk update');
		$this->listTabModules['search_filter'] = $this->l('Search & Filter');
		$this->listTabModules['seo'] = $this->l('SEO');
		$this->listTabModules['shipping_logistics'] = $this->l('Shipping & Logistics');
		$this->listTabModules['slideshows'] = $this->l('Slideshows');
		$this->listTabModules['smart_shopping'] = $this->l('Smart Shopping');
		$this->listTabModules['market_place'] = $this->l('Market Place');
		$this->listTabModules['social_networks'] = $this->l('Social Networks');
		$this->listTabModules['others'] = $this->l('Other Modules');

		if (file_exists(_PS_ROOT_DIR_.$this->_moduleCacheFile))
			$xmlModules = @simplexml_load_file(_PS_ROOT_DIR_.$this->_moduleCacheFile);
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
								$this->listNativeModules[] = (string)$value;

				if ($xmlModule->attributes() == 'partner')
					foreach ($xmlModule->children() as $module)
						foreach ($module->attributes() as $key => $value)
							if ($key == 'name')
								$this->listPartnerModules[] = (string)$value;
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

	public function displayAjaxRefreshModuleList()
	{
		echo Tools::jsonEncode(array('status' => $this->status));
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
		if (Tools::isSubmit('submitDownload'))
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
				if (Validate::isModuleUrl($url = Tools::getValue('url'), $this->_errors))
				{
					if (!@copy($url, _PS_MODULE_DIR_.basename($url)))
						$this->_errors[] = Tools::displayError('404 Module not found');
					else
						$this->extractArchive(_PS_MODULE_DIR_.basename($url));
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to add here.');
		}
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
								echo $toolbar.'
								<div class="clear">&nbsp;</div>'.$echo.'<div class="clear">&nbsp;</div>
								'.$toolbar;
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

	public function display()
	{
		if (!isset($_GET['configure']) && !isset($_GET['delete']) || count($this->_errors))
			$this->displayList();
	}

	public function displayJavascript()
	{
		echo '<script type="text/javascript" src="'._PS_JS_DIR_.'jquery/plugins/autocomplete/jquery.autocomplete.js"></script>
			<script type="text/javascript" src="'._PS_JS_DIR_.'jquery/plugins/fancybox/jquery.fancybox.js"></script>

		<script type="text/javascript">
			function getPrestaStore(){if (getE("prestastore").style.display!=\'block\')return;$.post("'.dirname(self::$currentIndex).'/ajax.php",{page:"prestastore"},function(a){getE("prestastore-content").innerHTML=a;})}
			function truncate_author(author)
			{
				return ((author.length > '.self::$MAX_DISP_AUTHOR.') ? author.substring(0, '.self::$MAX_DISP_AUTHOR.')+"..." : author);
			}
			function modules_management(action)
			{
				var modules = document.getElementsByName(\'modules\');
				var module_list = \'\';
				for (var i = 0; i < modules.length; i++)
				{
					if (modules[i].checked == true)
					{
						rel = modules[i].getAttribute(\'rel\');
						if (rel != "false" && action == "uninstall")
						{
							if (!confirm(rel))
								return false;
						}
						module_list += \'|\'+modules[i].value;
					}
				}
				document.location.href=\''.self::$currentIndex.'&token='.$this->token.'&\'+action+\'=\'+module_list.substring(1, module_list.length);
			}
			$(\'document\').ready( function() {
				$(\'input[name="filtername"]\').autocomplete(moduleList, {
					minChars: 0,
					width: 310,
					matchContains: true,
					highlightItem: true,
					formatItem: function(row, i, max, term) {
						return "<img src=\"../modules/"+row.name+"/logo.gif\" style=\"float:left;margin:5px\"><strong>" + row.displayName + "</strong>"+((row.author != \'\') ? " '.$this->l("by").' "+ truncate_author(row.author) :"") + "<br /><span style=\'font-size: 80%;\'>"+ row.desc +"</span><br/><div style=\"height:15px;padding-top:5px\">"+ row.option +"</div>";
					},
					formatResult: function(row) {
						return row.displayName;
					}
				});
				$(\'input[name="filtername"]\').result(function(event, data, formatted) {
				 $(\'#filternameForm\').submit();
				});
			});';

			// the following to get modules_list.xml from prestashop.com
			echo '$(document).ready(function(){
				try
				{
					resAjax = $.ajax({
							type:"POST",
							url : "'. str_replace('index','ajax-tab',self::$currentIndex) . '",
							async: true,
							data : {
								ajaxMode : "1",
								ajax : "1",
								token : "'.$this->token.'",
								tab : "AdminModules",
								action : "refreshModuleList"
							},
							success : function(res,textStatus,jqXHR)
							{
								// res.status  = cache or refresh
							},
							error: function(res,textStatus,jqXHR)
							{
								alert("TECHNICAL ERROR"+res);
							}
			});
				}
				catch(e){}
			});
		</script>';
	}

	public static function sortModule($a, $b)
	{
	    if (sizeof($a) == sizeof($b)) {
	        return 0;
	    }
	    return (sizeof($a) < sizeof($b)) ? -1 : 1;
	}


	/**
	 * Used for retreiving author name from submited field authorModules[name]
	 * @param String $value value to be clean
	 *
	 * @return String cleant value: name
	 */
	private function _getSubmitedModuleAuthor($value)
	{
		$value = str_replace('authorModules[', '', $value);
		$value = str_replace("\'", "'", $value);

		$value = substr($value, 0, -1);
		return $value;
	}

	/**
	 * Used for building option group
	 * @param Array $authors contains modules authors
	 * @param String $fieldName name of optiongroup
	 * @return String built comp
	 */

	private function _buildModuleAuthorsOptGroup($authors, $fieldName = "UNDEFINED")
	{
		$out = '<optgroup label="'.$this->l('Authors').'">';
		foreach($authors as $author_item => $status)
		{
			$author_item = Tools::htmlentitiesUTF8($author_item);
			$disp_author = $this->_getDispAuthor($author_item);

			$out .= '<option value="'.$fieldName.'['.$author_item. ']"'. (($status === "selected") ? ' selected>' : '>').$disp_author .'</option>';
		}
		$out .= '</optgroup>';
		return $out;
	}

	/**
	 * Used for truncating  author name to display it nicely
	 * @param String $author original  author
	 * @return String truncated author name
	 */
	private function _getDispAuthor($author)
	{
		return ((strlen($author) > self::$MAX_DISP_AUTHOR) ? substr($author, 0, self::$MAX_DISP_AUTHOR).'...' : $author);
	}


	public function displayList()
	{
		$modulesAuthors = array();
		$autocompleteList = 'var moduleList = [';

		$showTypeModules = Configuration::get('PS_SHOW_TYPE_MODULES_'.(int)$this->context->employee->id);
		$showInstalledModules = Configuration::get('PS_SHOW_INSTALLED_MODULES_'.(int)$this->context->employee->id);
		$showEnabledModules = Configuration::get('PS_SHOW_ENABLED_MODULES_'.(int)$this->context->employee->id);
		$showCountryModules = Configuration::get('PS_SHOW_COUNTRY_MODULES_'.(int)$this->context->employee->id);

		$nameCountryDefault = Country::getNameById($this->context->language->id, Configuration::get('PS_COUNTRY_DEFAULT'));
		$isoCountryDefault = $this->context->country->iso_code;

		$serialModules = '';
		$modules = Module::getModulesOnDisk(true);

		foreach ($modules AS $module)
		{
			if (!in_array($module->name, $this->listNativeModules))
				$serialModules .= $module->name.' '.$module->version.'-'.($module->active ? 'a' : 'i')."\n";

			$moduleAuthor = $module->author;
			if (!empty($moduleAuthor)&& ($moduleAuthor != ""))
				$modulesAuthors[(string)$moduleAuthor] = true;
		}

		$serialModules = urlencode($serialModules);

		$filterName = Tools::getValue('filtername');
		if (!empty($filterName))
		{
			echo '
			<script type="text/javascript">
				$(document).ready(function() {
					$(\'#all_open\').hide();
					$(\'#all_close\').show();
					$(\'.tab_module_content\').each(function(){
						$(this).slideDown();
						$(\'.header_module_img\').each(function(){
							$(this).attr(\'src\', \'../img/admin/less.png\');
						});
					});
				});
			</script>';
		}

		// Filter module list
		foreach ($modules as $key => $module)
		{
			// beware $module could be an instance of Module or stdClass, that explain the static call
			if ($module->id AND !Module::getPermissionStatic($module->id, 'view') AND !Module::getPermissionStatic($module->id, 'configure'))
			{
				unset($modules[$key]);
				continue;
			}

			switch ($showTypeModules)
			{
				case 'nativeModules':
					if (!in_array($module->name, $this->listNativeModules))
					{
						unset($modules[$key]);
						continue;
					}
				break;
				case 'partnerModules':
					if (!in_array($module->name, $this->listPartnerModules))
					{
						unset($modules[$key]);
						continue;
					}
				break;
				case 'otherModules':
					if (in_array($module->name, $this->listPartnerModules) OR in_array($module->name, $this->listNativeModules))
					{
						unset($modules[$key]);
						continue;
					}
				break;
				default:
					if (strpos($showTypeModules, 'authorModules[') !== false)
					{
						$author_selected = $this->_getSubmitedModuleAuthor($showTypeModules);
						$modulesAuthors[$author_selected] = 'selected';		// setting selected author in authors set
						if (empty($module->author) || $module->author != $author_selected)
						{
							unset($modules[$key]);
							continue;
						}
					}

				break;

			}

			switch ($showInstalledModules)
			{
				case 'installed':
					if (!$module->id)
					{
						unset($modules[$key]);
						continue;
					}
				break;
				case 'unistalled':
					if ($module->id)
					{
						unset($modules[$key]);
						continue;
					}
				break;
			}

			switch ($showEnabledModules)
			{
				case 'enabled':
					if (!$module->active)
					{
						unset($modules[$key]);
						continue;
					}
				break;
				case 'disabled':
					if ($module->active)
					{
						unset($modules[$key]);
						continue;
					}
				break;
			}

			if ($showCountryModules AND (isset($module->limited_countries) AND !empty($module->limited_countries) AND ((is_array($module->limited_countries) AND sizeof($module->limited_countries) AND !in_array(strtolower($isoCountryDefault), $module->limited_countries)) OR (!is_array($module->limited_countries) AND strtolower($isoCountryDefault) != strval($module->limited_countries)))))
			{
				unset($modules[$key]);
				continue;
			}

			if (!empty($filterName) AND (stristr($module->name, $filterName) === false AND stristr($module->displayName, $filterName) === false AND stristr($module->description, $filterName) === false))
			{
				unset($modules[$key]);
				continue;
			}
		}

		foreach($modules as $module)
			$autocompleteList .= Tools::jsonEncode(array(
				'displayName' => (string)$module->displayName,
				'desc' => (string)$module->description,
				'name' => (string)$module->name,
				'author' => (string)$module->author,
				'option' => $this->displayOptions($module)
			)).', ';

		$autocompleteList = rtrim($autocompleteList, ' ,').'];';
		// Display CSS Fancy Box
		echo '<link href="'._PS_CSS_DIR_.'jquery.fancybox-1.3.4.css" rel="stylesheet" type="text/css" media="screen" />';
		echo '<script type="text/javascript">'.$autocompleteList.'</script>';
		$this->displayJavascript();

		echo '
		<span onclick="$(\'#module_install\').slideToggle()" style="cursor:pointer"><img src="../img/admin/add.gif" alt="'.$this->l('Add a new module').'" class="middle" />
			'.$this->l('Add a module from my computer').'
		</span>
		&nbsp;|&nbsp;';
		echo '<a href="index.php?tab=AdminAddonsMyAccount&token='.Tools::getAdminTokenLite('AdminAddonsMyAccount').'">
			<img src="https://addons.prestashop.com/modules.php?'.(isset($_SERVER['SERVER_ADDR']) ? 'server='.ip2long($_SERVER['SERVER_ADDR']).'&' : '').'mods='.$serialModules.'" alt="Add" class="middle" />
			'.$this->l('Add a module from PrestaShop Addons').'
		</a>';
		echo '<form action="'.self::$currentIndex.'&token='.$this->token.'" method="post" id="filternameForm" style="float:right"><input type="text" name="filtername" value="'.Tools::htmlentitiesUTF8(Tools::getValue('filtername')).'" /> <input type="submit" value="'.$this->l('Search').'" class="button" /></form>
		<div class="clear">&nbsp;</div>
		<div id="module_install" style="width:900px; '.((Tools::isSubmit('submitDownload') OR Tools::isSubmit('submitDownload2')) ? '' : 'display: none;').'">
			<fieldset>
				<legend><img src="../img/admin/add.gif" alt="'.$this->l('Add a new module').'" class="middle" /> '.$this->l('Add a new module').'</legend>
				<p>'.$this->l('The module must be either a zip file or a tarball.').'</p>
				<hr />
				<div style="float:right;margin-right:50px;border-left:solid 1px #DFD5C3">
					<form action="'.self::$currentIndex.'&token='.$this->token.'" method="post" enctype="multipart/form-data">
						<label style="width: 100px">'.$this->l('Module file').'</label>
						<div class="margin-form" style="padding-left: 140px">
							<input type="file" name="file" />
							<p>'.$this->l('Upload the module from your computer.').'</p>
						</div>
						<div class="margin-form" style="padding-left: 140px">
							<input type="submit" name="submitDownload2" value="'.$this->l('Upload this module').'" class="button" />
						</div>
					</form>
				</div>
				<div>
				<form action="'.self::$currentIndex.'&token='.$this->token.'" method="post">
					<label style="width: 100px">'.$this->l('Module URL').'</label>
					<div class="margin-form" style="padding-left: 140px">
						<input type="text" name="url" style="width: 200px;" value="'.(Tools::getValue('url') ? Tools::getValue('url') : 'http://').'" />
						<p>'.$this->l('Download the module directly from a website.').'</p>
					</div>
					<div class="margin-form" style="padding-left: 140px">
						<input type="submit" name="submitDownload" value="'.$this->l('Download this module').'" class="button" />
					</div>
				</form>
				</div>
			</fieldset>
			<br />
		</div>';
		if (Configuration::get('PRESTASTORE_LIVE'))
			echo '
			<div id="prestastore" style="margin-left:40px; display:none; float: left" class="width1">
			</div>';

		/* Scan modules directories and load modules classes */
		$warnings = array();
		$orderModule = array();
	    $irow = 0;
		foreach ($modules AS $module)
			$orderModule[(isset($module->tab) AND !empty($module->tab) AND array_key_exists(strval($module->tab), $this->listTabModules)) ? strval($module->tab) : 'others' ][] = $module;
		uasort($orderModule,array('AdminModules', 'sortModule'));

		$concatWarning = array();
		foreach ($orderModule AS $tabModule)
			foreach ($tabModule AS $module)
				if ($module->active AND isset($module->warning) && $module->warning)
					$warnings[] ='<a href="'.self::$currentIndex.'&configure='.urlencode($module->name).'&token='.$this->token.'">'.$module->displayName.'</a> - '.stripslashes(pSQL($module->warning));
		$this->displayWarning($warnings);
		echo '<form method="POST">
			<table cellpadding="0" cellspacing="0" style="width:100%;;margin-bottom:5px;">
				<tr>
					<th style="border-right:solid 1px;border:inherit">
						<span class="button" style="padding:0.4em;">
							<a id="all_open" class="module_toggle_all" style="display:inherit;text-decoration:none;" href="#">
								<span style="padding-right:0.5em">
									<img src="../img/admin/more.png" alt="" />
								</span>
								<span id="all_open">'.$this->l('Open all tabs').'</span>
							</a>
							<a id="all_close" class="module_toggle_all" style="display:none;text-decoration:none;" href="#">
								<span style="padding-right:0.5em">
									<img src="../img/admin/less.png" alt="" />
								</span>
								<span id="all_open">'.$this->l('Close all tabs').'</span>
							</a>
						</span>
					</th>
					<th colspan="3" style="border:inherit">
						<select name="module_type">
							<option value="allModules" '.($showTypeModules == 'allModules' ? 'selected="selected"' : '').'>'.$this->l('All Modules').'</option>
							<option value="nativeModules" '.($showTypeModules == 'nativeModules' ? 'selected="selected"' : '').'>'.$this->l('Native Modules').'</option>
							<option value="partnerModules" '.($showTypeModules == 'partnerModules' ? 'selected="selected"' : '').'>'.$this->l('Partners Modules').'</option>'
.$this->_buildModuleAuthorsOptGroup($modulesAuthors, 'authorModules')
.'
							<option value="otherModules" '.($showTypeModules == 'otherModules' ? 'selected="selected"' : '').'>'.$this->l('Others Modules').'</option>
						</select>
						&nbsp;
						<select name="module_install">
							<option value="installedUninstalled" '.($showInstalledModules == 'installedUninstalled' ? 'selected="selected"' : '').'>'.$this->l('Installed & Uninstalled').'</option>
							<option value="installed" '.($showInstalledModules == 'installed' ? 'selected="selected"' : '').'>'.$this->l('Installed Modules').'</option>
							<option value="unistalled" '.($showInstalledModules == 'unistalled' ? 'selected="selected"' : '').'>'.$this->l('Uninstalled Modules').'</option>
						</select>
						&nbsp;
						<select name="module_status">
							<option value="enabledDisabled" '.($showEnabledModules == 'enabledDisabled' ? 'selected="selected"' : '').'>'.$this->l('Enabled & Disabled').'</option>
							<option value="enabled" '.($showEnabledModules == 'enabled' ? 'selected="selected"' : '').'>'.$this->l('Enabled Modules').'</option>
							<option value="disabled" '.($showEnabledModules == 'disabled' ? 'selected="selected"' : '').'>'.$this->l('Disabled Modules').'</option>
						</select>
						&nbsp;
						<select name="country_module_value">
							<option value="0" >'.$this->l('All countries').'</option>
							<option value="1" '.($showCountryModules == 1 ? 'selected="selected"' : '').'>'.$this->l('Current country:').' '.$nameCountryDefault.'</option>
						</select>
					</th>
					<th style="border:inherit">
						<div style="float:right">
							<input type="submit" class="button" name="resetFilterModules" value="'.$this->l('Reset').'">
							<input type="submit" class="button" name="filterModules" value="'.$this->l('Filter').'">
						</div>
					</th>
			  	</tr>
			</table>
			</form>';
		echo $this->displaySelectedFilter();
		if ($tab_module = Tools::getValue('tab_module'))
			if (array_key_exists($tab_module, $this->listTabModules))
				$goto = $tab_module;
			else
				$goto = 'others';
		else
			$goto = false;

		echo '
  		<script src="'.__PS_BASE_URI__.'js/jquery/plugins/jquery.scrollTo.js"></script>
		<script>
		 $(document).ready(function() {

		 $(\'.header_module_toggle, .module_toggle_all\').unbind(\'click\').click(function(){
		 	var id = $(this).attr(\'id\');
			if (id == \'all_open\')
				$(\'.tab_module_content\').each(function(){
					$(this).slideDown();
					$(\'#all_open\').hide();
					$(\'#all_close\').show();
					$(\'.header_module_img\').each(function(){
						$(this).attr(\'src\', \'../img/admin/less.png\');
					});
				});
			else if (id == \'all_close\')
				$(\'.tab_module_content\').each(function(){
					$(\'#all_open\').show();
					$(\'#all_close\').hide();
					$(this).slideUp();
					$(\'.header_module_img\').each(function(){
						$(this).attr(\'src\', \'../img/admin/more.png\');
					});
				});
			else
			{
				if ($(\'#\'+id+\'_content\').css(\'display\') == \'none\')
		 			$(\'#\'+id+\'_img\').attr(\'src\', \'../img/admin/less.png\');
		 		else
		 			$(\'#\'+id+\'_img\').attr(\'src\', \'../img/admin/more.png\');

		 		$(\'#\'+$(this).attr(\'id\')+\'_content\').slideToggle();
		 	}
		 	return false;
		 });
		'.(!$goto ? '': 'if ($(\'#'.$goto.'_content\').length > 0) $(\'#'.$goto.'_content\').slideToggle( function (){
		$(\'#'.$goto.'_img\').attr(\'src\', \'../img/admin/less.png\');
		'.(!$goto ? '' : 'if ($("#modgo_'.Tools::getValue('module_name').'").length > 0) $.scrollTo($("#modgo_'.Tools::getValue('module_name').'"), 300 ,
		{onAfter:function(){
			$("#modgo_'.Tools::getValue('module_name').'").fadeTo(100, 0, function (){
				$(this).fadeTo(100, 0, function (){
					$(this).fadeTo(50, 1, function (){
						$(this).fadeTo(50, 0, function (){
							$(this).fadeTo(50, 1 )}
								)}
							)}
						)}
					)}
				});').'
		});').'

			});
		 </script>';
		if (!empty($orderModule))
		{
			/* Browse modules by tab type */
			foreach ($orderModule AS $tab => $tabModule)
			{
				echo '
				<div id="'.$tab.'" class="header_module">
				<span class="nbr_module" style="width:100px;text-align:right">'.sizeof($tabModule).' '.((sizeof($tabModule) > 1) ? $this->l('modules') : $this->l('module')).'</span>
					<a class="header_module_toggle" id="'.$tab.'" href="modgo_'.$tab.'" style="margin-left: 5px;">
						<span style="padding-right:0.5em">
						<img class="header_module_img" id="'.$tab.'_img" src="../img/admin/more.png" alt="" />
						</span>'.$this->listTabModules[$tab].'</a>
				</div>
				<div id="'.$tab.'_content" class="tab_module_content" style="display:none;border:solid 1px #CCC">';
				/* Display modules for each tab type */
				foreach ($tabModule as $module)
				{
					echo '<div id="modgo_'.$module->name.'" title="' . $module->name . '">';
					if ($module->id)
					{
						$img = '<img src="../img/admin/module_install.png" alt="'.$this->l('Module enabled').'" title="'.$this->l('Module enabled').'" />';
						if ($module->warning)
							$img = '<img src="../img/admin/module_warning.png" alt="'.$this->l('Module installed but with warnings').'" title="'.$this->l('Module installed but with warnings').'" />';
						if (!$module->active)
							$img = '<img src="../img/admin/module_disabled.png" alt="'.$this->l('Module disabled').'" title="'.$this->l('Module disabled').'" />';
					} else
						$img = '<img src="../img/admin/module_notinstall.png" alt="'.$this->l('Module not installed').'" title="'.$this->l('Module not installed').'" />';
					$disp_author = $this->_getDispAuthor($module->author);
					$disp_author = (empty($disp_author)) ? '' :  ' '.$this->l('by').' <i>'.Tools::htmlentitiesUTF8($disp_author).'</i>';
					echo '<table style="width:100%" cellpadding="0" cellspacing="0" >
					<tr'.($irow % 2 ? ' class="alt_row"' : '').' style="height: 42px;">
						<td style="padding-right: 10px;padding-left:10px;width:30px">
							<input type="checkbox" name="modules" value="'.urlencode($module->name).'" '.(empty($module->confirmUninstall) ? 'rel="false"' : 'rel="'.addslashes($module->confirmUninstall).'"').' />
						</td>
						<td style="padding:2px 4px 2px 10px;width:500px"><img src="../modules/'.$module->name.'/logo.gif" alt="" /> <b>'.stripslashes($module->displayName).'</b>'.($module->version ? ' v'.$module->version.(strpos($module->version, '.') !== false ? '' : '.0') : '').$disp_author.'<br />'.stripslashes($module->description).'</td>
						<td rowspan="2">';
						if (Tools::getValue('module_name') == $module->name)
							$this->displayConf();
						echo '</td>
						<td class="center" style="width:60px" rowspan="2">';
					if ($module->id)
						echo '<a href="'.self::$currentIndex.'&token='.$this->token.'&module_name='.$module->name.'&'.($module->active ? 'enable=0' : 'enable=1').'">';
					echo $img;
					if ($module->id)
						'</a>';
					$href = self::$currentIndex.'&uninstall='.urlencode($module->name).'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.$module->name;
					echo '
						</td>
						<td class="center" width="120" rowspan="2">'.((!$module->id)
						? '<input type="button" class="button small" name="Install" value="'.$this->l('Install').'"
						onclick="javascript:document.location.href=\''.self::$currentIndex.'&install='.urlencode($module->name).'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.$module->name.'\'">'
						: '<input type="button" class="button small" name="Uninstall" value="'.$this->l('Uninstall').'"
						onclick="'.((!method_exists($module, 'onclickOption')) ? ((empty($module->confirmUninstall)) ? '' : 'if(confirm(\''.addslashes($module->confirmUninstall).'\')) ').'document.location.href=\''.$href.'\'' : $module->onclickOption('uninstall', $href)).'">').'</td>

					</tr>
					<tr'.($irow++ % 2 ? ' class="alt_row"' : '').'>
						<td style="padding-left:50px;padding-bottom:5px;padding-top:5px" colspan="2">'.$this->displayOptions($module).'</td>
					</tr>
					</table>
					</div>';
				}
				echo '</div>';
			}
			echo '
				<div style="margin-top: 12px; width:600px;">
					<input type="button" class="button big" value="'.$this->l('Install the selection').'" onclick="modules_management(\'install\')"/>
					<input type="button" class="button big" value="'.$this->l('Uninstall the selection').'" onclick="modules_management(\'uninstall\')" />
				</div>
				<br />
				<table cellpadding="0" cellspacing="0" class="table" style="width:100%;">
					<tr style="height:35px;background-color:#EEEEEE">
						<td><strong>'.$this->l('Icon legend').' : </strong></td>
						<td style="text-align:center;border-right:solid 1px gray"><img src="../img/admin/module_install.png" />&nbsp;&nbsp;'.$this->l('Module installed and enabled').'</td>
						<td style="text-align:center;border-right:solid 1px gray"><img src="../img/admin/module_disabled.png" />&nbsp;&nbsp;'.$this->l('Module installed but disabled').'</td>
						<td style="text-align:center;border-right:solid 1px gray"><img src="../img/admin/module_warning.png" />&nbsp;&nbsp;'.$this->l('Module installed but with warnings').'</td>
						<td style="text-align:center"><img src="../img/admin/module_notinstall.png" />&nbsp;&nbsp;'.$this->l('Module not installed').'</td>
					</tr>
				</table>
			<div style="clear:both">&nbsp;</div>';
		}
		else
			echo '<table cellpadding="0" cellspacing="0" class="table" style="width:100%;"><tr><td align="center">'.$this->l('No module found').'</td></tr></table>';
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

	public function displayOptions($module)
	{
		$return = '';
		$href = self::$currentIndex.'&token='.$this->token.'&module_name='.urlencode($module->name).'&tab_module='.$module->tab;
		if ($module->id)
			$return .= '<a class="action_module" '.($module->active && method_exists($module, 'onclickOption')? 'onclick="'.$module->onclickOption('desactive', $href).'"' : '').' href="'.self::$currentIndex.'&token='.$this->token.'&module_name='.urlencode($module->name).'&'.($module->active ? 'enable=0' : 'enable=1').'&tab_module='.$module->tab.'" '.((Shop::isFeatureActive()) ? 'title="'.htmlspecialchars($module->active ? $this->l('Disable this module') : $this->l('Enable this module for all shops')).'"' : '').'>'.($module->active ? $this->l('Disable') : $this->l('Enable')).'</a>&nbsp;&nbsp;';

		if ($module->id AND $module->active)
			$return .= '<a class="action_module" '.(method_exists($module, 'onclickOption')? 'onclick="'.$module->onclickOption('reset', $href).'"' : '').' href="'.self::$currentIndex.'&token='.$this->token.'&module_name='.urlencode($module->name).'&reset&tab_module='.$module->tab.'">'.$this->l('Reset').'</a>&nbsp;&nbsp;';

		if ($module->id AND (method_exists($module, 'getContent') OR (isset($module->is_configurable) AND $module->is_configurable) OR Shop::isFeatureActive()))
			$return .= '<a class="action_module" '.(method_exists($module, 'onclickOption')? 'onclick="'.$module->onclickOption('configure', $href).'"' : '').' href="'.self::$currentIndex.'&configure='.urlencode($module->name).'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.urlencode($module->name).'">'.$this->l('Configure').'</a>&nbsp;&nbsp;';

		$hrefDelete = self::$currentIndex.'&deleteModule='.urlencode($module->name).'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.urlencode($module->name);
		$return .= '<a class="action_module" '.(method_exists($module, 'onclickOption')? 'onclick="'.$module->onclickOption('delete', $hrefDelete).'"' : '').' onclick="return confirm(\''.$this->l('This action will permanently remove the module from the server. Are you sure you want to do this ?').'\');" href="'.$hrefDelete.'">'.$this->l('Delete').'</a>&nbsp;&nbsp;';

		return $return;
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

	public function displaySelectedFilter()
	{
		$selected_filter = '';
		$id_employee = (int)$this->context->employee->id;

		$showTypeModules = Configuration::get('PS_SHOW_TYPE_MODULES_'.(int)$this->context->employee->id);
		$showInstalledModules = Configuration::get('PS_SHOW_INSTALLED_MODULES_'.(int)$this->context->employee->id);
		$showEnabledModules = Configuration::get('PS_SHOW_ENABLED_MODULES_'.(int)$this->context->employee->id);
		$showCountryModules = Configuration::get('PS_SHOW_COUNTRY_MODULES_'.(int)$this->context->employee->id);
		$selected_filter .= ($showTypeModules == 'allModules' ? $this->l('All Modules').' - ' : '').
							($showTypeModules == 'nativeModules' ? $this->l('Native Modules').' - ' : '').
							($showTypeModules == 'partnerModules' ? $this->l('Partners Modules').' - ' : '').
							($showTypeModules == 'otherModules' ? $this->l('Others Modules').' - ' : '').
							($showInstalledModules == 'installedUninstalled' ? $this->l('Installed & Uninstalled').' - ' : '').
							($showInstalledModules == 'installed' ? $this->l('Installed Modules').' - ' : '').
							($showInstalledModules == 'unistalled' ? $this->l('Uninstalled Modules').' - ' : '').
							($showEnabledModules == 'enabledDisabled' ? $this->l('Enabled & Disabled').' - ' : '').
							($showEnabledModules == 'enabled' ? $this->l('Enabled Modules').' - ' : '').
							($showEnabledModules == 'disabled' ? $this->l('Disabled Modules').' - ' : '').
							($showCountryModules === 1 ? $this->l('Current country:').' '.$nameCountryDefault.' - ' : '').
							($showCountryModules === 0 ? $this->l('All countries').' - ' : '');

		if (strlen($selected_filter) != 0)
			$selected_filter = '<div class="hint" style="display:block;background:#DDE9F7 no-repeat 6px 5px url(../img/admin/filter.png);"><b>'.$this->l('Selected filters').' : </b>'.rtrim($selected_filter, ' - ').'</div>';
		return $selected_filter;
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

}
