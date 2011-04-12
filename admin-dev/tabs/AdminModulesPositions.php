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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminModulesPositions extends AdminTab
{
	private $displayKey = 0;

	public function postProcess()
	{
		global	$currentIndex;

		// Getting key value for display
		if (Tools::getValue('show_modules') AND strval(Tools::getValue('show_modules')) != 'all')
			$this->displayKey = (int)(Tools::getValue('show_modules'));

		// Change position in hook
		if (array_key_exists('changePosition', $_GET))
		{
			if ($this->tabAccess['edit'] === '1')
		 	{
				$id_module = (int)(Tools::getValue('id_module'));
				$id_hook = (int)(Tools::getValue('id_hook'));
				$module = Module::getInstanceById($id_module);
				if (Validate::isLoadedObject($module))
				{
					$module->updatePosition($id_hook, (int)(Tools::getValue('direction')));
					Tools::redirectAdmin($currentIndex.($this->displayKey ? '&show_modules='.$this->displayKey : '').'&token='.$this->token);
				}
				else
					$this->_errors[] = Tools::displayError('module cannot be loaded');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}

		// Add new module in hook
		elseif (Tools::isSubmit('submitAddToHook'))
		{
		 	if ($this->tabAccess['add'] === '1')
			{
				// Getting vars...
				$id_module = (int)(Tools::getValue('id_module'));
				$module = Module::getInstanceById($id_module);
				$id_hook = (int)(Tools::getValue('id_hook'));
				$hook = new Hook($id_hook);
				$excepts = explode(',', str_replace(' ', '', Tools::getValue('exceptions')));

				// Checking vars...
				foreach ($excepts AS $except)
					if (!Validate::isFileName($except))
						$this->_errors[] = Tools::displayError('No valid value for field exceptions');
				if (!$id_module OR !Validate::isLoadedObject($module))
					$this->_errors[] = Tools::displayError('module cannot be loaded');
				elseif (!$id_hook OR !Validate::isLoadedObject($hook))
					$this->_errors[] = Tools::displayError('Hook cannot be loaded.');
				elseif (Hook::getModuleFromHook($id_hook, $id_module))
					$this->_errors[] = Tools::displayError('This module is already transplanted to this hook.');
				elseif (!$module->isHookableOn($hook->name))
					$this->_errors[] = Tools::displayError('This module can\'t be transplanted to this hook.');
				// Adding vars...
				elseif (!$module->registerHook($hook->name))
					$this->_errors[] = Tools::displayError('An error occurred while transplanting module to hook.');
				elseif (!$module->registerExceptions($id_hook, $excepts))
					$this->_errors[] = Tools::displayError('An error occurred while transplanting module to hook.');
				else
					Tools::redirectAdmin($currentIndex.'&conf=16'.($this->displayKey ? '&show_modules='.$this->displayKey : '').'&token='.$this->token);
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to add here.');
		}
		
		// Edit module from hook
		elseif (Tools::isSubmit('submitEditGraft'))
		{
		 	if ($this->tabAccess['add'] === '1')
			{
				// Getting vars...
				$id_module = (int)(Tools::getValue('id_module'));
				$module = Module::getInstanceById($id_module);
				$id_hook = (int)(Tools::getValue('id_hook'));
				$hook = new Hook($id_hook);
				$excepts = explode(',', str_replace(' ', '', Tools::getValue('exceptions')));

				// Checking vars...
				foreach ($excepts AS $except)
					if (!Validate::isFileName($except))
						$this->_errors[] = Tools::displayError('No valid value for field exceptions');
				if (!$id_module OR !Validate::isLoadedObject($module))
					$this->_errors[] = Tools::displayError('module cannot be loaded');
				elseif (!$id_hook OR !Validate::isLoadedObject($hook))
					$this->_errors[] = Tools::displayError('Hook cannot be loaded.');

				// Adding vars...
				if (!$module->editExceptions($id_hook, $excepts))
					$this->_errors[] = Tools::displayError('An error occurred while transplanting module to hook.');
				else
					Tools::redirectAdmin($currentIndex.'&conf=16'.($this->displayKey ? '&show_modules='.$this->displayKey : '').'&token='.$this->token);
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to add here.');
		}

		// Delete module from hook
		elseif (array_key_exists('deleteGraft', $_GET))
		{
		 	if ($this->tabAccess['delete'] === '1')
		 	{
				$id_module = (int)(Tools::getValue('id_module'));
				$module = Module::getInstanceById($id_module);
				$id_hook = (int)(Tools::getValue('id_hook'));
				$hook = new Hook($id_hook);
				if (!Validate::isLoadedObject($module))
					$this->_errors[] = Tools::displayError('module cannot be loaded');
				elseif (!$id_hook OR !Validate::isLoadedObject($hook))
					$this->_errors[] = Tools::displayError('Hook cannot be loaded.');
				else
				{
					$position = Db::getInstance()->getValue('SELECT `position` FROM `'._DB_PREFIX_.'hook_module` hm WHERE hm.`id_hook` = '.$id_hook.' AND hm.`id_module` = '.$id_module);
					if (!$module->unregisterHook($id_hook) OR !$module->unregisterExceptions($id_hook))
						$this->_errors[] = Tools::displayError('An error occurred while deleting module from hook.');
					else
					{
						$this->placeCorrectlyOtherModules($id_hook, $position);
						Tools::redirectAdmin($currentIndex.'&conf=17'.($this->displayKey ? '&show_modules='.$this->displayKey : '').'&token='.$this->token);
					}
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		elseif (Tools::isSubmit('unhookform'))
		{
			if (!($unhooks = Tools::getValue('unhooks')) OR !is_array($unhooks))
				$this->_errors[] = Tools::displayError('Select a module to unhook.');
			else
			{
				foreach ($unhooks as $unhook)
				{
					$explode = explode('_', $unhook);
					$id_hook = $explode[0];
					$id_module = $explode[1];
					$module = Module::getInstanceById((int)($id_module));
					$hook = new Hook((int)($id_hook));
					if (!Validate::isLoadedObject($module))
						$this->_errors[] = Tools::displayError('module cannot be loaded');
					elseif (!$id_hook OR !Validate::isLoadedObject($hook))
						$this->_errors[] = Tools::displayError('Hook cannot be loaded.');
					else
					{
						$position = Db::getInstance()->getValue('SELECT `position` FROM `'._DB_PREFIX_.'hook_module` hm WHERE hm.`id_hook` = '.(int)($id_hook).' AND hm.`id_module` = '.(int)($id_module));
						if (!$module->unregisterHook((int)($id_hook)) OR !$module->unregisterExceptions((int)($id_hook)))
							$this->_errors[] = Tools::displayError('An error occurred while deleting module from hook.');
						else
							$this->placeCorrectlyOtherModules((int)($id_hook), (int)($position));
					}
				}
				if (!sizeof($this->_errors))
					Tools::redirectAdmin($currentIndex.'&conf=17'.($this->displayKey ? '&show_modules='.$this->displayKey : '').'&token='.$this->token);
			}
		}
	}

	public function display()
	{
		if (array_key_exists('addToHook', $_GET) OR array_key_exists('editGraft', $_GET) OR (Tools::isSubmit('submitAddToHook') AND $this->_errors))
			$this->displayForm();
		else
			$this->displayList();
	}

	public function displayList()
	{
		global $currentIndex;
		$link = new Link();
		$admin_dir = dirname($_SERVER['PHP_SELF']);
		$admin_dir = substr($admin_dir, strrpos($admin_dir,'/') + 1);
		
		echo '
		<script type="text/javascript" src="../js/jquery/jquery.tablednd_0_5.js"></script>
		<script type="text/javascript">
			var token = \''.$this->token.'\';
			var come_from = \'AdminModulesPositions\';
		</script>
		<script type="text/javascript" src="../js/admin-dnd.js"></script>
		';
		echo '<a href="'.$currentIndex.'&addToHook'.($this->displayKey ? '&show_modules='.$this->displayKey : '').'&token='.$this->token.'"><img src="../img/admin/add.gif" border="0" /> <b>'.$this->l('Transplant a module').'</b></a><br /><br />';

		// Print select list
		echo '
		<form>
			'.$this->l('Show').' :
			<select id="show_modules" onChange="autoUrl(\'show_modules\', \''.$currentIndex.'&token='.$this->token.'&show_modules=\')">
				<option value="all">'.$this->l('All modules').'&nbsp;</option>
				<option>---------------</option>';
				$modules = Module::getModulesInstalled();

				foreach ($modules AS $module)
					if ($tmpInstance = Module::getInstanceById((int)($module['id_module'])))
						$cm[$tmpInstance->displayName] = $tmpInstance;
				ksort($cm);
				foreach ($cm AS $module)
					echo '
					<option value="'.(int)($module->id).'" '.($this->displayKey == $module->id ? 'selected="selected" ' : '').'>'.$module->displayName.'</option>';
			echo '
			</select><br /><br />
			<input type="checkbox" id="hook_position" onclick="autoUrlNoList(\'hook_position\', \''.$currentIndex.'&token='.$this->token.'&show_modules='.(int)(Tools::getValue('show_modules')).'&hook_position=\')" '.(Tools::getValue('hook_position') ? 'checked="checked" ' : '').' />&nbsp;<label class="t" for="hook_position">'.$this->l('Display non-positionable hook').'</label>
		</form>
		
		<fieldset style="width:250px;float:right"><legend>'.$this->l('Live edit').'</legend>
				<p>'.$this->l('By clicking here you will be redirected to the front office of your shop to move and delete modules directly.').'</p>
				<br>
				<a href="'.$link->getPageLink('index.php').'?live_edit&ad='.$admin_dir.'&liveToken='.sha1($admin_dir._COOKIE_KEY_).'" target="_blank" class="button">'.$this->l('Run LiveEdit').'</a>
		</fieldset>
		';

		// Print hook list
		echo '<form method="post" action="'.$currentIndex.'&token='.$this->token.'">';
		$irow = 0;
		$hooks = Hook::getHooks(!(int)(Tools::getValue('hook_position')));
		echo '<div id="unhook_button_position_top"><input class="button floatr" type="submit" name="unhookform" value="'.$this->l('Unhook the selection').'"/></div>';
		foreach ($hooks AS $hook)
		{
			$modules = array();
			if (!$this->displayKey)
				$modules = Hook::getModulesFromHook($hook['id_hook']);
			elseif ($res = Hook::getModuleFromHook($hook['id_hook'], $this->displayKey))
					$modules[0] = $res;
			$nbModules = sizeof($modules);
			echo '
			<a name="'.$hook['name'].'"/>
			<table cellpadding="0" cellspacing="0" class="table width3 space'.($nbModules >= 2? ' tableDnD' : '' ).'" id="'.$hook['id_hook'].'">
			<tr class="nodrag nodrop"><th colspan="4">'.$hook['title'].' - <span style="color: red">'.$nbModules.'</span> '.(($nbModules > 1) ? $this->l('modules') : $this->l('module'));
			if ($nbModules)
				echo '<input type="checkbox" id="Ghook'.$hook['id_hook'].'" class="floatr" style="margin-right: 2px;" onclick="hookCheckboxes('.$hook['id_hook'].', 0, this)"/>';
			if (!empty($hook['description']))
				echo '&nbsp;<span style="font-size:0.8em; font-weight: normal">['.$hook['description'].']</span>';
			echo ' <sub style="color:grey;"><i>('.$this->l('Technical name: ').$hook['name'].')</i></sub></th></tr>';

			// Print modules list
		
			if ($nbModules)
			{
				$instances = array();
				foreach ($modules AS $module)
					if ($tmpInstance = Module::getInstanceById((int)($module['id_module'])))
						$instances[$tmpInstance->getPosition($hook['id_hook'])] = $tmpInstance;
				ksort($instances);
				foreach ($instances AS $position => $instance)
				{
					echo '
					<tr id="'.$hook['id_hook'].'_'.$instance->id.'"'.($irow++ % 2 ? ' class="alt_row"' : '').' style="height: 42px;">';
					if (!$this->displayKey)
					{
						echo '
						<td class="positions" width="40">'.(int)($position).'</td>
						<td'.($nbModules >= 2? ' class="dragHandle"' : '').' id="td_'.$hook['id_hook'].'_'.$instance->id.'" width="40">
						<a'.($position == 1 ? ' style="display: none;"' : '' ).' href="'.$currentIndex.'&id_module='.$instance->id.'&id_hook='.$hook['id_hook'].'&direction=0&token='.$this->token.'&changePosition='.rand().'#'.$hook['name'].'"><img src="../img/admin/up.gif" alt="'.$this->l('Up').'" title="'.$this->l('Up').'" /></a><br />
							<a '.($position == sizeof($instances) ? ' style="display: none;"' : '').'href="'.$currentIndex.'&id_module='.$instance->id.'&id_hook='.$hook['id_hook'].'&direction=1&token='.$this->token.'&changePosition='.rand().'#'.$hook['name'].'"><img src="../img/admin/down.gif" alt="'.$this->l('Down').'" title="'.$this->l('Down').'" /></a>
						</td>
						<td style="padding-left: 10px;"><label class="lab_modules_positions" for="mod'.$hook['id_hook'].'_'.$instance->id.'">
						';
					}
					else
						echo '<td style="padding-left: 10px;" colspan="3"><label class="lab_modules_positions" for="'.$hook['id_hook'].'_'.$instance->id.'">';
					echo '
					<img src="../modules/'.$instance->name.'/logo.gif" alt="'.stripslashes($instance->name).'" /> <strong>'.stripslashes($instance->displayName).'</strong>
						'.($instance->version ? ' v'.((int)($instance->version) == $instance->version? sprintf('%.1f', $instance->version) : (float)($instance->version)) : '').'<br />'.$instance->description.'
					</label></td>
						<td width="60">
							<a href="'.$currentIndex.'&id_module='.$instance->id.'&id_hook='.$hook['id_hook'].'&editGraft'.($this->displayKey ? '&show_modules='.$this->displayKey : '').'&token='.$this->token.'"><img src="../img/admin/edit.gif" border="0" alt="'.$this->l('Edit').'" title="'.$this->l('Edit').'" /></a>
							<a href="'.$currentIndex.'&id_module='.$instance->id.'&id_hook='.$hook['id_hook'].'&deleteGraft'.($this->displayKey ? '&show_modules='.$this->displayKey : '').'&token='.$this->token.'"><img src="../img/admin/delete.gif" border="0" alt="'.$this->l('Delete').'" title="'.$this->l('Delete').'" /></a>
							<input type="checkbox" id="mod'.$hook['id_hook'].'_'.$instance->id.'" class="hook'.$hook['id_hook'].'" onclick="hookCheckboxes('.$hook['id_hook'].', 1, this)" name="unhooks[]" value="'.$hook['id_hook'].'_'.$instance->id.'"/>
						</td>
					</tr>';
				}
			} else
				echo '<tr><td colspan="4">'.$this->l('No module for this hook').'</td></tr>';
			echo '</table>';
		}
		echo '<div id="unhook_button_position_bottom"><input class="button floatr" type="submit" name="unhookform" value="'.$this->l('Unhook the selection').'"/></div></form>';
	}

	public function displayForm($isMainTab = true)
	{
		global $currentIndex;
		parent::displayForm();

		$id_module = (int)(Tools::getValue('id_module'));
		$id_hook = (int)(Tools::getValue('id_hook'));
		if ($id_module AND $id_hook AND Tools::isSubmit('editGraft'))
		{
			$slModule = Module::getInstanceById($id_module);
			$exceptsList = $slModule->getExceptions($id_hook);
			$excepts = '';
			foreach ($exceptsList as $key => $except)
				$excepts .= ($key ? ',' : '').$except['file_name'];
		}
		$excepts = strval(Tools::getValue('exceptions', ((isset($slModule) AND Validate::isLoadedObject($slModule)) ? $excepts : '')));
		$modules = Module::getModulesInstalled(0);

		$instances = array();
		foreach ($modules AS $module)
			if ($tmpInstance = Module::getInstanceById($module['id_module']))
				$instances[$tmpInstance->displayName] = $tmpInstance;
		ksort($instances);
		$modules = $instances;
		$hooks = Hook::getHooks(0);
		echo '
		<form action="'.$currentIndex.'&token='.$this->token.'" method="post">';
		if ($this->displayKey)
			echo '<input type="hidden" name="show_modules" value="'.$this->displayKey.'" />';
		echo '<fieldset style="width:700px"><legend><img src="../img/t/AdminModulesPositions.gif" />'.$this->l('Transplant a module').'</legend>
				<label>'.$this->l('Module').' :</label>
				<div class="margin-form">
					<select name="id_module"'.(Tools::isSubmit('editGraft') ? ' disabled="disabled"' : '').'>';
					foreach ($modules AS $module)
						echo '
						<option value="'.$module->id.'" '.($id_module == $module->id ? 'selected="selected" ' : '').'>'.stripslashes($module->displayName).'</option>';
					echo '
					</select><sup> *</sup>
				</div>
				<label>'.$this->l('Hook into').' :</label>
				<div class="margin-form">
					<select name="id_hook"'.(Tools::isSubmit('editGraft') ? ' disabled="disabled"' : '').'>';
					foreach ($hooks AS $hook)
						echo '
						<option value="'.$hook['id_hook'].'" '.($id_hook == $hook['id_hook'] ? 'selected="selected" ' : '').'>'.$hook['title'].'</option>';
					echo '
					</select><sup> *</sup>
				</div>
				<label>'.$this->l('Exceptions').' :</label>
				<div class="margin-form">
					<input type="text" name="exceptions" size="40" '.(!empty($excepts) ? 'value="'.$excepts.'"' : '').'><br />Ex: identity.php, history.php, order.php, product.php<br /><br />
					'.$this->l('Please specify those files for which you do not want the module to be displayed').'.<br />
					'.$this->l('These files are located in your base directory').', '.$this->l('e.g., ').' <b>identity.php</b>.<br />
					'.$this->l('Please type each filename separated by a comma').'.
					<br /><br />
				</div>
				<div class="margin-form">
				';
				if (Tools::isSubmit('editGraft'))
				{
					echo '
					<input type="hidden" name="id_module" value="'.$id_module.'" />
					<input type="hidden" name="id_hook" value="'.$id_hook.'" />';
				}
				echo '
					<input type="submit" value="'.$this->l('Save').'" name="'.(Tools::isSubmit('editGraft') ? 'submitEditGraft' : 'submitAddToHook').'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>';
	}

	private function placeCorrectlyOtherModules($id_hook, $position)
	{
		return Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'hook_module` hm SET hm.`position`= hm.`position` - 1 WHERE hm.`id_hook` = '.(int)($id_hook).' AND hm.`position` > '.(int)($position));
	}
}


