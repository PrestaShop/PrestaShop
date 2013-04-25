<?php
/*
* 2007-2013 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>o
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminModulesPositionsControllerCore extends AdminController
{
	protected $display_key = 0;

	public function postProcess()
	{
		// Getting key value for display
		if (Tools::getValue('show_modules') && strval(Tools::getValue('show_modules')) != 'all')
			$this->display_key = (int)Tools::getValue('show_modules');

		// Change position in hook
		if (array_key_exists('changePosition', $_GET))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				$id_module = (int)Tools::getValue('id_module');
				$id_hook = (int)Tools::getValue('id_hook');
				$module = Module::getInstanceById($id_module);
				if (Validate::isLoadedObject($module))
				{
					$module->updatePosition($id_hook, (int)Tools::getValue('direction'));
					Tools::redirectAdmin(self::$currentIndex.($this->display_key ? '&show_modules='.$this->display_key : '').'&token='.$this->token);
				}
				else
					$this->errors[] = Tools::displayError('This module cannot be loaded');
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit this.');
		}

		// Add new module in hook
		elseif (Tools::isSubmit('submitAddToHook'))
		{
			if ($this->tabAccess['add'] === '1')
			{
				// Getting vars...
				$id_module = (int)Tools::getValue('id_module');
				$module = Module::getInstanceById($id_module);
				$id_hook = (int)Tools::getValue('id_hook');
				$hook = new Hook($id_hook);

				if (!$id_module || !Validate::isLoadedObject($module))
					$this->errors[] = Tools::displayError('This module cannot be loaded');
				elseif (!$id_hook || !Validate::isLoadedObject($hook))
					$this->errors[] = Tools::displayError('Hook cannot be loaded.');
				elseif (Hook::getModulesFromHook($id_hook, $id_module))
					$this->errors[] = Tools::displayError('This module has already been transplanted to this hook');
				elseif (!$module->isHookableOn($hook->name))
					$this->errors[] = Tools::displayError('This module cannot be transplanted to this hook.');
				// Adding vars...
				else
				{
					if (!$module->registerHook($hook->name, Shop::getContextListShopID()))
						$this->errors[] = Tools::displayError('An error occurred while transplanting the module to its hook.');
					else
					{
						$exceptions = Tools::getValue('exceptions');
						$exceptions = (isset($exceptions[0])) ? $exceptions[0] : array();
						$exceptions = explode(',', str_replace(' ', '', $exceptions));

						foreach ($exceptions as $key => $except)
						{
							if (empty($except))
								unset($exceptions[$key]);
							else if (!Validate::isFileName($except))
								$this->errors[] = Tools::displayError('No valid value for field exceptions has been defined.');
						}
						if (!$this->errors && !$module->registerExceptions($id_hook, $exceptions, Shop::getContextListShopID()))
							$this->errors[] = Tools::displayError('An error occurred while transplanting the module to its hook.');
					}
					if (!$this->errors)
						Tools::redirectAdmin(self::$currentIndex.'&conf=16'.($this->display_key ? '&show_modules='.$this->display_key : '').'&token='.$this->token);
				}
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to add this.');
		}

		// Edit module from hook
		elseif (Tools::isSubmit('submitEditGraft'))
		{
			if ($this->tabAccess['add'] === '1')
			{
				// Getting vars...
				$id_module = (int)Tools::getValue('id_module');
				$module = Module::getInstanceById($id_module);
				$id_hook = (int)Tools::getValue('id_hook');
				$hook = new Hook($id_hook);

				if (!$id_module || !Validate::isLoadedObject($module))
					$this->errors[] = Tools::displayError('This module cannot be loaded');
				elseif (!$id_hook || !Validate::isLoadedObject($hook))
					$this->errors[] = Tools::displayError('Hook cannot be loaded.');
				else
				{
					$exceptions = Tools::getValue('exceptions');
					if (is_array($exceptions))
					{
						foreach ($exceptions as $id => $exception)
						{
							$exception = explode(',', str_replace(' ', '', $exception));

							// Check files name
							foreach ($exception as $except)
								if (!Validate::isFileName($except))
									$this->errors[] = Tools::displayError('No valid value for field exceptions has been defined.');

							$exceptions[$id] = $exception;
						}

						// Add files exceptions
						if (!$module->editExceptions($id_hook, $exceptions))
							$this->errors[] = Tools::displayError('An error occurred while transplanting the module to its hook.');

						if (!$this->errors)
							Tools::redirectAdmin(self::$currentIndex.'&conf=16'.($this->display_key ? '&show_modules='.$this->display_key : '').'&token='.$this->token);
					}
					else
					{
						$exceptions = explode(',', str_replace(' ', '', $exceptions));

						// Check files name
						foreach ($exceptions as $except)
							if (!Validate::isFileName($except))
								$this->errors[] = Tools::displayError('No valid value for field exceptions has been defined.');

						// Add files exceptions
						if (!$module->editExceptions($id_hook, $exceptions, Shop::getContextListShopID()))
							$this->errors[] = Tools::displayError('An error occurred while transplanting the module to its hook.');
						else
							Tools::redirectAdmin(self::$currentIndex.'&conf=16'.($this->display_key ? '&show_modules='.$this->display_key : '').'&token='.$this->token);
					}
				}
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to add this.');
		}

		// Delete module from hook
		elseif (array_key_exists('deleteGraft', $_GET))
		{
			if ($this->tabAccess['delete'] === '1')
			{
				$id_module = (int)Tools::getValue('id_module');
				$module = Module::getInstanceById($id_module);
				$id_hook = (int)Tools::getValue('id_hook');
				$hook = new Hook($id_hook);
				if (!Validate::isLoadedObject($module))
					$this->errors[] = Tools::displayError('This module cannot be loaded');
				elseif (!$id_hook || !Validate::isLoadedObject($hook))
					$this->errors[] = Tools::displayError('Hook cannot be loaded.');
				else
				{
					if (!$module->unregisterHook($id_hook, Shop::getContextListShopID())
						|| !$module->unregisterExceptions($id_hook, Shop::getContextListShopID()))
						$this->errors[] = Tools::displayError('An error occurred while deleting the module from its hook.');
					else
						Tools::redirectAdmin(self::$currentIndex.'&conf=17'.($this->display_key ? '&show_modules='.$this->display_key : '').'&token='.$this->token);
				}
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to delete this.');
		}
		elseif (Tools::isSubmit('unhookform'))
		{
			if (!($unhooks = Tools::getValue('unhooks')) || !is_array($unhooks))
				$this->errors[] = Tools::displayError('Please select a module to unhook.');
			else
			{
				foreach ($unhooks as $unhook)
				{
					$explode = explode('_', $unhook);
					$id_hook = $explode[0];
					$id_module = $explode[1];
					$module = Module::getInstanceById((int)$id_module);
					$hook = new Hook((int)$id_hook);
					if (!Validate::isLoadedObject($module))
						$this->errors[] = Tools::displayError('This module cannot be loaded');
					elseif (!$id_hook || !Validate::isLoadedObject($hook))
						$this->errors[] = Tools::displayError('Hook cannot be loaded.');
					else
					{
						if (!$module->unregisterHook((int)$id_hook) || !$module->unregisterExceptions((int)$id_hook))
							$this->errors[] = Tools::displayError('An error occurred while deleting the module from its hook.');
					}
				}
				if (!count($this->errors))
					Tools::redirectAdmin(self::$currentIndex.'&conf=17'.($this->display_key ? '&show_modules='.$this->display_key : '').'&token='.$this->token);
			}
		}
		else
			parent::postProcess();
	}

	public function initContent()
	{
		if (array_key_exists('addToHook', $_GET) || array_key_exists('editGraft', $_GET) || (Tools::isSubmit('submitAddToHook') && $this->errors))
		{
			$this->display = 'edit';
			$this->content .= $this->renderForm();
		}
		else
			$this->content .= $this->initMain();

		$this->context->smarty->assign(array(
			'content' => $this->content
		));
	}

	public function initMain()
	{
		// Init toolbar
		$this->initToolbarTitle();

		$admin_dir = basename(_PS_ADMIN_DIR_);
		$modules = Module::getModulesInstalled();

		$assoc_modules_id = array();
		foreach ($modules as $module)
			if ($tmp_instance = Module::getInstanceById((int)$module['id_module']))
			{
				// We want to be able to sort modules by display name
				$module_instances[$tmp_instance->displayName] = $tmp_instance;
				// But we also want to associate hooks to modules using the modules IDs
				$assoc_modules_id[(int)$module['id_module']] = $tmp_instance->displayName;
			}
		ksort($module_instances);
		$hooks = Hook::getHooks(!(int)Tools::getValue('hook_position'));
		foreach ($hooks as $key => $hook)
		{
			// Get all modules for this hook or only the filtered module
			$hooks[$key]['modules'] = Hook::getModulesFromHook($hook['id_hook'], $this->display_key);
			$hooks[$key]['module_count'] = count($hooks[$key]['modules']);
			// If modules were found, link to the previously created Module instances
			if (is_array($hooks[$key]['modules']) && !empty($hooks[$key]['modules']))
				foreach ($hooks[$key]['modules'] as $module_key => $module)
					if (isset($assoc_modules_id[$module['id_module']]))
						$hooks[$key]['modules'][$module_key]['instance'] = $module_instances[$assoc_modules_id[$module['id_module']]];
		}

		$this->addJqueryPlugin('tablednd');

		$this->toolbar_btn['save'] = array(
			'href' => self::$currentIndex.'&addToHook'.($this->display_key ? '&show_modules='.$this->display_key : '').'&token='.$this->token,
			'desc' => $this->l('Transplant a module')
		);
		
		$live_edit_params = array(
									'live_edit' => true, 
									'ad' => $admin_dir, 
									'liveToken' => $this->token,
									'id_employee' => (int)$this->context->employee->id
									);

		$this->context->smarty->assign(array(
			'show_toolbar' => true,
			'toolbar_btn' => $this->toolbar_btn,
			'title' => $this->toolbar_title,
			'toolbar_scroll' => 'false',
			'token' => $this->token,
			'url_show_modules' => self::$currentIndex.'&token='.$this->token.'&show_modules=',
			'modules' => $module_instances,
			'url_show_invisible' => self::$currentIndex.'&token='.$this->token.'&show_modules='.(int)Tools::getValue('show_modules').'&hook_position=',
			'hook_position' => Tools::getValue('hook_position'),
			'live_edit' => Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP,
			'url_live_edit' => $this->getLiveEditUrl($live_edit_params),
			'display_key' => $this->display_key,
			'hooks' => $hooks,
			'url_submit' => self::$currentIndex.'&token='.$this->token,
			'can_move' => (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) ? false : true,
		));

		return $this->createTemplate('list_modules.tpl')->fetch();
	}
	
	public function getLiveEditUrl($live_edit_params)
	{
		$url = $this->context->shop->getBaseURL().Dispatcher::getInstance()->createUrl('index', (int)$this->context->language->id, $live_edit_params);
		if (Configuration::get('PS_REWRITING_SETTINGS'))
			$url = str_replace('index.php', ((count(Language::getLanguages(true)) > 1)? Language::getIsoById($this->context->employee->id_lang).'/' : ''), $url);
		return $url;
	}
	
	public function renderForm()
	{
		// Init toolbar
		$this->initToolbarTitle();
		// toolbar (save, cancel, new, ..)
		$this->initToolbar();
		$id_module = (int)Tools::getValue('id_module');
		$id_hook = (int)Tools::getValue('id_hook');
		if (Tools::isSubmit('editGraft'))
		{
			// Check auth for this page
			if (!$id_module || !$id_hook)
				Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);

			$sql = 'SELECT id_module
					FROM '._DB_PREFIX_.'hook_module
					WHERE id_module = '.$id_module.'
						AND id_hook = '.$id_hook.'
						AND id_shop IN('.implode(', ', Shop::getContextListShopID()).')';
			if (!Db::getInstance()->getValue($sql))
				Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);

			$sl_module = Module::getInstanceById($id_module);
			$excepts_list = $sl_module->getExceptions($id_hook, true);
			$excepts_diff = false;
			$excepts = '';
			if ($excepts_list)
			{
				$first = current($excepts_list);
				foreach ($excepts_list as $k => $v)
					if (array_diff($v, $first) || array_diff($first, $v))
						$excepts_diff = true;

				if (!$excepts_diff)
					$excepts = implode(', ', $first);
			}
		}
		else
		{
			$excepts_diff = false;
			$excepts_list = Tools::getValue('exceptions', array(array()));
		}
		$modules = Module::getModulesInstalled(0);

		$instances = array();
		foreach ($modules as $module)
			if ($tmp_instance = Module::getInstanceById($module['id_module']))
				$instances[$tmp_instance->displayName] = $tmp_instance;
		ksort($instances);
		$modules = $instances;
		$hooks = Hook::getHooks(0);

		$exception_list_diff = array();
		foreach ($excepts_list as $shop_id => $file_list)
			$exception_list_diff[] = $this->displayModuleExceptionList($file_list, $shop_id);

		$tpl = $this->createTemplate('form.tpl');
		$tpl->assign(array(
			'url_submit' => self::$currentIndex.'&token='.$this->token,
			'edit_graft' => Tools::isSubmit('editGraft'),
			'id_module' => (int)Tools::getValue('id_module'),
			'id_hook' => (int)Tools::getValue('id_hook'),
			'show_modules' => Tools::getValue('show_modules'),
			'hooks' => $hooks,
			'exception_list' => $this->displayModuleExceptionList(array_shift($excepts_list), 0),
			'exception_list_diff' => $exception_list_diff,
			'except_diff' => isset($excepts_diff) ? $excepts_diff : null,
			'display_key' => $this->display_key,
			'modules' => $modules,
			'show_toolbar' => true,
			'toolbar_btn' => $this->toolbar_btn,
			'toolbar_scroll' => $this->toolbar_scroll,
			'title' => $this->toolbar_title,
			'table' => 'hook_module',
		));

		return $tpl->fetch();
	}

	public function displayModuleExceptionList($file_list, $shop_id)
	{
		if (!is_array($file_list))
			$file_list = ($file_list) ? array($file_list) : array();

		$content = '<input type="text" name="exceptions['.$shop_id.']" size="40" value="'.implode(', ', $file_list).'" id="em_text_'.$shop_id.'">';
		if ($shop_id)
		{
			$shop = new Shop($shop_id);
			$content .= ' ('.$shop->name.')';
		}
		$content .= '<br /><select id="em_list_'.$shop_id.'">';

		// @todo do something better with controllers
		$controllers = Dispatcher::getControllers(_PS_FRONT_CONTROLLER_DIR_);
		ksort($controllers);
		foreach ($controllers as $k => $v)
			$content .= '<option value="'.$k.'">'.$k.'</option>';
		$content .= '</select> <input type="button" class="button" value="'.$this->l('Add').'" onclick="position_exception_add('.$shop_id.')" />
				<input type="button" class="button" value="'.$this->l('Remove').'" onclick="position_exception_remove('.$shop_id.')" /><br /><br />';

		return $content;
	}

	public function ajaxProcessUpdatePositions()
	{
		if ($this->tabAccess['edit'] === '1')
		{
			$id_module = (int)(Tools::getValue('id_module'));
			$id_hook = (int)(Tools::getValue('id_hook'));
			$way = (int)(Tools::getValue('way'));
			$positions = Tools::getValue(strval($id_hook));
			$position = (is_array($positions)) ? array_search($id_hook.'_'.$id_module, $positions) : null;
			$module = Module::getInstanceById($id_module);
			if (Validate::isLoadedObject($module))
				if ($module->updatePosition($id_hook, $way, $position))
					die(true);
				else
					die('{"hasError" : true, "errors" : "Can not update module position"}');
			else
				die('{"hasError" : true, "errors" : "This module can not be loaded"}');
		}
	}
	
	public function ajaxProcessGetHookableList()
	{
		if ($this->tabAccess['view'] === '1')
		{
			/* PrestaShop demo mode */
			if (_PS_MODE_DEMO_)
				die('{"hasError" : true, "errors" : ["Live Edit : This functionnality has been disabled"]}');

			if (!count(Tools::getValue('hooks_list')))
				die('{"hasError" : true, "errors" : ["Live Edit : no module on this page"]}');

			$modules_list = Tools::getValue('modules_list');
			$hooks_list = Tools::getValue('hooks_list');
			$hookableList = array();

			foreach ($modules_list as $module)
			{
				$module = trim($module);
				if (!$module)
					continue;

				if (!Validate::isModuleName($module))
						die('{"hasError" : true, "errors" : ["Live Edit : module is invalid"]}');
						
				$moduleInstance = Module::getInstanceByName($module);
				foreach ($hooks_list as $hook_name)
				{
					$hook_name = trim($hook_name);
					if (!$hook_name)
						continue;
					if (!array_key_exists($hook_name, $hookableList))
						$hookableList[$hook_name] = array();
					if ($moduleInstance->isHookableOn($hook_name))
						array_push($hookableList[$hook_name], str_replace('_', '-', $module));
				}

			}
			$hookableList['hasError'] = false;
			die(Tools::jsonEncode($hookableList));
		}
	}

	public function ajaxProcessGetHookableModuleList()
	{
		if ($this->tabAccess['view'] === '1')
		{
			/* PrestaShop demo mode */
			if (_PS_MODE_DEMO_)
				die('{"hasError" : true, "errors" : ["Live Edit : This functionnality has been disabled"]}');
			/* PrestaShop demo mode*/

			$hook_name = Tools::getValue('hook');
			$hookableModulesList = array();
			$modules = Db::getInstance()->executeS('SELECT id_module, name FROM `'._DB_PREFIX_.'module` ');
			foreach ($modules as $module)
			{
				if (!Validate::isModuleName($module['name']))
					continue;
				if (file_exists(_PS_MODULE_DIR_.$module['name'].'/'.$module['name'].'.php'))
				{
					include_once(_PS_MODULE_DIR_.$module['name'].'/'.$module['name'].'.php');
					$mod = new $module['name']();
					if ($mod->isHookableOn($hook_name))
						$hookableModulesList[] = array('id' => (int)$mod->id, 'name' => $mod->displayName, 'display' => Hook::exec($hook_name, array(), (int)$mod->id));
				}
			}
			die(Tools::jsonEncode($hookableModulesList));
		}
	}
	public function ajaxProcessSaveHook()
	{
		if ($this->tabAccess['edit'] === '1')
		{
				/* PrestaShop demo mode */
			if (_PS_MODE_DEMO_)
				die('{"hasError" : true, "errors" : ["Live Edit : This functionnality has been disabled"]}');

			$hooks_list = explode(',', Tools::getValue('hooks_list'));
			$id_shop = (int)Tools::getValue('id_shop');
			if (!$id_shop)
				$id_shop = Context::getContext()->shop->id;

			$res = true;
			$hookableList = array();
			// $_POST['hook'] is an array of id_module
			$hooks_list = Tools::getValue('hook');
			foreach ($hooks_list as $id_hook => $modules)
			{
				// 1st, drop all previous hooked modules
				$sql = 'DELETE FROM `'._DB_PREFIX_.'hook_module`
					WHERE `id_hook` =  '.(int)$id_hook.'
					AND id_shop = '.(int)$id_shop;
				$res &= Db::getInstance()->execute($sql);

				$i = 1;
				$value = '';
				$ids = array();
				// then prepare sql query to rehook all chosen modules(id_module, id_shop, id_hook, position)
				// position is i (autoincremented)
				foreach ($modules as $id_module)
				{
					if (!in_array($id_module, $ids))
					{
						$ids[] = (int)$id_module;
						$value .= '('.(int)$id_module.', '.(int)$id_shop.', '.(int)$id_hook.', '.(int)$i.'),';
					}
					$i++;
				}
				$value = rtrim($value, ',');
				$res &= Db::getInstance()->execute('INSERT INTO  `'._DB_PREFIX_.'hook_module`
					(id_module, id_shop, id_hook, position)
					VALUES '.$value);

			}
			if ($res)
				$hasError = true;
			else
				$hasError = false;
			die('{"hasError" : false, "errors" : ""}');
		}
	}
}
