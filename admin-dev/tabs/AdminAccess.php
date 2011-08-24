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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include_once(PS_ADMIN_DIR.'/../classes/AdminTab.php');

class AdminAccess extends AdminTab
{
	public function postProcess()
	{
		if (Tools::isSubmit('submitAddaccess') AND $action = Tools::getValue('action') AND $id_tab = (int)(Tools::getValue('id_tab')) AND $id_profile = (int)(Tools::getValue('id_profile')) AND $this->tabAccess['edit'] == 1)
		{
			if ($id_tab == -1 AND $action == 'all' AND (int)(Tools::getValue('perm')) == 0)
				Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'access` SET `view` = '.(int)(Tools::getValue('perm')).', `add` = '.(int)(Tools::getValue('perm')).', `edit` = '.(int)(Tools::getValue('perm')).', `delete` = '.(int)(Tools::getValue('perm')).' WHERE `id_profile` = '.(int)($id_profile).' AND `id_tab` != 31');
			elseif ($id_tab == -1 AND $action == 'all')
				Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'access` SET `view` = '.(int)(Tools::getValue('perm')).', `add` = '.(int)(Tools::getValue('perm')).', `edit` = '.(int)(Tools::getValue('perm')).', `delete` = '.(int)(Tools::getValue('perm')).' WHERE `id_profile` = '.(int)($id_profile));
			elseif ($id_tab == -1)
				Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'access` SET `'.pSQL($action).'` = '.(int)(Tools::getValue('perm')).' WHERE `id_profile` = '.(int)($id_profile));
			elseif ($action == 'all')
				Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'access` SET `view` = '.(int)(Tools::getValue('perm')).', `add` = '.(int)(Tools::getValue('perm')).', `edit` = '.(int)(Tools::getValue('perm')).', `delete` = '.(int)(Tools::getValue('perm')).' WHERE `id_tab` = '.(int)($id_tab).' AND `id_profile` = '.(int)($id_profile));
			else
				Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'access` SET `'.pSQL($action).'` = '.(int)(Tools::getValue('perm')).' WHERE `id_tab` = '.(int)($id_tab).' AND `id_profile` = '.(int)($id_profile));
		}
	}
	
	public function display()
	{
		$this->displayForm();
	}
	
	/**
	* Get the current profile id
	*
	* @return the $_GET['profile'] if valid, else 1 (the first profile id)
	*/
	function getCurrentProfileId()
	{
	 	return (isset($_GET['profile']) AND !empty($_GET['profile']) AND is_numeric($_GET['profile'])) ? (int)($_GET['profile']) : 1;
	}
	
	public function displayForm($isMainTab = true)
	{
		parent::displayForm();
	 	
	 	$currentProfile = (int)($this->getCurrentProfileId());
	 	$tabs = Tab::getTabs($this->context->language->id);
		$profiles = Profile::getProfiles($this->context->language->id);
		$accesses = Profile::getProfileAccesses($this->context->employee->id_profile);
		
		echo '
		<script type="text/javascript">
			setLang(Array(\''.$this->l('Profile updated').'\', \''.$this->l('Request failed!').'\', \''.$this->l('Update in progress. Please wait.').'\', \''.$this->l('Server connection failed!').'\'));
		</script>
		<div id="ajax_confirmation"></div>
		<table class="table float" cellspacing="0">
			<tr>
				<th '.($currentProfile == (int)_PS_ADMIN_PROFILE_ ? 'colspan="6"' : '').'>
					<select name="profile" onchange="redirect(\''.Tools::getHttpHost(true, true).self::$currentIndex.'&token='.$this->token.'&profile=\'+this.options[this.selectedIndex].value)">';
		if ($profiles)
			foreach ($profiles AS $profile)
				echo '<option value="'.(int)$profile['id_profile'].'" '.((int)$profile['id_profile'] == $currentProfile ? 'selected="selected"' : '').'>'.$profile['name'].'</option>';

		$tabsize = sizeof($tabs);
		foreach ($tabs AS $tab)
			if ($tab['id_tab'] > $tabsize)
				$tabsize = $tab['id_tab'];
		echo '		</select>
				</th>';
		
		if ($currentProfile != (int)(_PS_ADMIN_PROFILE_))
			echo '
				<th class="center">
					<input type="checkbox" name="1" id="viewall"
						'.($this->tabAccess['edit'] == 1 ? 'onclick="ajax_power(this, \'view\', -1, '.$currentProfile.', \''.$this->token.'\', \''.$tabsize.'\', \''.sizeof($tabs).'\')"' : 'disabled="disabled"').' />
					'.$this->l('View').'
				</th>
				<th class="center">
					<input type="checkbox" name="1" id="addall"
					'.($this->tabAccess['edit'] == 1 ? 'onclick="ajax_power(this, \'add\', -1, '.$currentProfile.', \''.$this->token.'\', \''.$tabsize.'\', \''.sizeof($tabs).'\')"' : 'disabled="disabled"').' />
					'.$this->l('Add').'
				</th>
				<th class="center">
					<input type="checkbox" name="1" id="editall"
					'.($this->tabAccess['edit'] == 1 ? 'onclick="ajax_power(this, \'edit\', -1, '.$currentProfile.', \''.$this->token.'\', \''.$tabsize.'\', \''.sizeof($tabs).'\')"' : 'disabled="disabled"').' />
					'.$this->l('Edit').'
				</th>
				<th class="center">
					<input type="checkbox" name="1" id="deleteall"
					'.($this->tabAccess['edit'] == 1 ? 'onclick="ajax_power(this, \'delete\', -1, '.$currentProfile.', \''.$this->token.'\', \''.$tabsize.'\', \''.sizeof($tabs).'\')"' : 'disabled="disabled"').' />
					'.$this->l('Delete').'
				</th>
				<th class="center">
					<input type="checkbox" name="1" id="allall"
					'.($this->tabAccess['edit'] == 1 ? 'onclick="ajax_power(this, \'all\', -1, '.$currentProfile.', \''.$this->token.'\', \''.$tabsize.'\', \''.sizeof($tabs).'\')"' : 'disabled="disabled"').' />
					'.$this->l('All').'
				</th>
			</tr>';

		if (!sizeof($tabs))
			echo '<tr><td colspan="5">'.$this->l('No tab').'</td></tr>';
		elseif ($currentProfile == (int)(_PS_ADMIN_PROFILE_))
			echo '<tr><td colspan="5">'.$this->l('Administrator permissions can\'t be modified.').'</td></tr>';
		else 
			foreach ($tabs AS $tab)
				if (!$tab['id_parent'] OR (int)($tab['id_parent']) == -1)
				{
					$this->printTabAccess((int)($currentProfile), $tab, $accesses[$tab['id_tab']], false, $tabsize, sizeof($tabs));
					foreach ($tabs AS $child)
						if ($child['id_parent'] === $tab['id_tab'])
							if (isset($accesses[$child['id_tab']]))
					 		$this->printTabAccess($currentProfile, $child, $accesses[$child['id_tab']], true, $tabsize, sizeof($tabs));
				}
		echo '</table>';
		
		if ($currentProfile != (int)(_PS_ADMIN_PROFILE_))
			$this->displayModuleAccesses($currentProfile);
		echo '<div class="clear">&nbsp;</div>';
	}
	
	private function printTabAccess($currentProfile, $tab, $access, $is_child, $tabsize, $tabnumber)
	{
		$result_accesses = 0;
		$perms = array('view', 'add', 'edit', 'delete');
		echo '<tr><td'.($is_child ? '' : ' class="bold"').'>'.($is_child ? ' &raquo; ' : '').$tab['name'].'</td>';
		foreach ($perms as $perm)
		{
			if ($this->tabAccess['edit'] == 1)
				echo '<td><input type="checkbox" name="1" id=\''.$perm.(int)($access['id_tab']).'\' class=\''.$perm.' '.(int)($access['id_tab']).'\' onclick="ajax_power(this, \''.$perm.'\', '.(int)($access['id_tab']).', '.(int)($access['id_profile']).', \''.$this->token.'\', \''.$tabsize.'\', \''.$tabnumber.'\')" '.((int)($access[$perm]) == 1 ? 'checked="checked"' : '').'/></td>';
			else
				echo '<td><input type="checkbox" name="1" disabled="disabled" '.((int)($access[$perm]) == 1 ? 'checked="checked"' : '').' /></td>';
			$result_accesses += $access[$perm];
		}
		echo '<td>
			<input type="checkbox" name="1" id=\'all'.(int)($access['id_tab']).'\' class=\'all '.(int)($access['id_tab']).'\'
				'.($this->tabAccess['edit'] == 1 ? 'onclick="ajax_power(this, \'all\', '.(int)($access['id_tab']).', '.(int)($access['id_profile']).', \''.$this->token.'\', \''.$tabsize.'\', \''.$tabnumber.'\')"' : 'disabled="disabled"').'
				'.($result_accesses == 4 ? 'checked="checked"' : '').'
			/>
		</td></tr>';
	}
	
	public function ajaxProcess()
	{
		if (Tools::isSubmit('changeModuleAccess'))
		{
			if ($action = Tools::getValue('action') AND $variable = Tools::getValue('variable') AND $id_module = (int)Tools::getValue('id_module') AND $id_profile = (int)Tools::getValue('id_profile') AND $this->tabAccess['edit'] == 1)
			{
				if (!in_array($variable, array('view', 'configure')))
					die (Tools::displayErrors('unknown variable'));
				$action = ($action == 'true' ? 1 : 0);
				if ($id_module == -1)
					Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'module_access` SET `'.pSQL($variable).'` = '.(int)$action.' WHERE `id_profile` = '.(int)$id_profile);
				else
					Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'module_access` SET `'.pSQL($variable).'` = '.(int)$action.' WHERE `id_module` = '.(int)$id_module.' AND `id_profile` = '.(int)$id_profile);
				die ('ok');
			}
			die ('inconsistent data');
		}
	}
	
	private function displayModuleAccesses($currentProfile)
	{
		echo '
		<script type="text/javascript">
			function changeModuleAccess(checkbox, id_module, variable)
			{
				getE(\'ajax_confirmation\').innerHTML = \'<span class="bold">\'+lang[2]+\'</span>\';
				$.post(
					\'ajax-tab.php?tab=AdminAccess&token='.Tools::getAdminTokenLite('AdminAccess').'&changeModuleAccess\',
					{id_profile:'.(int)$currentProfile.',id_module:id_module,action:checkbox.checked,variable:variable},
					function(r) {
						if (r != \'ok\')
							getE(\'ajax_confirmation\').innerHTML = \'<span class="bold">'.addslashes(Tools::displayError('An error occurred:')).' \'+lang[1]+\'</span>\';
						else
						{
							getE(\'ajax_confirmation\').innerHTML = \'<span class="bold">\'+lang[0]+\'</span>\';
							if (id_module == -1)
								$(\'.ajax-ma-\'+variable).each(function(key, value) {
									value.checked = checkbox.checked;
								});
							else if (!checkbox.checked)
								$(\'#ajax-ma-\'+variable+\'-master\').each(function(key, value) {
									value.checked = checkbox.checked;
								});
						}
					}
				);
			}
		</script>		
		<table class="table float" cellspacing="0" style="margin-left:20px">
		<tr>
			<th>'.$this->l('Modules').'</th>
			<th class="center"><input type="checkbox" id="ajax-ma-view-master" '.($this->tabAccess['edit'] == 1 ? 'onclick="changeModuleAccess(this, -1, \'view\');"' : 'disabled="disabled"').' /> '.$this->l('View').'</th>
			<th class="center"><input type="checkbox" id="ajax-ma-configure-master" '.($this->tabAccess['edit'] == 1 ? 'onclick="changeModuleAccess(this, -1, \'configure\');"' : 'disabled="disabled"').' /> '.$this->l('Configure').'</th>
		</tr>';

		$modules = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT ma.id_module, m.name, ma.`view`, ma.`configure`
		FROM '._DB_PREFIX_.'module_access ma
		LEFT JOIN '._DB_PREFIX_.'module m ON ma.id_module = m.id_module
		WHERE id_profile = '.(int)$currentProfile.'
		ORDER BY m.name');
		if (!sizeof($modules))
			echo '<tr><td colspan="2">'.$this->l('No modules installed').'</td></tr>';
		else 
			foreach ($modules AS $module)
				echo '<tr>
					<td>&raquo; '.$module['name'].'</td>
					<td>
						<input type="checkbox" class="ajax-ma-view"
							'.((int)$module['view'] == 1 ? 'checked="checked"' : '').'
							'.($this->tabAccess['edit'] == 1 ? 'onclick="changeModuleAccess(this, '.(int)$module['id_module'].', \'view\');"' : 'disabled="disabled"').'
						/>
					</td>
					<td>
						<input type="checkbox" class="ajax-ma-configure"
							'.((int)$module['configure'] == 1 ? 'checked="checked"' : '').'
							'.($this->tabAccess['edit'] == 1 ? 'onclick="changeModuleAccess(this, '.(int)$module['id_module'].', \'configure\');"' : 'disabled="disabled"').'
						/>
					</td>
				</tr>';

		echo '</table>';
	}
}


