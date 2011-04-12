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
		global $cookie, $currentIndex;
		parent::displayForm();
	 	
	 	$currentProfile = (int)($this->getCurrentProfileId());
	 	$tabs = Tab::getTabs($cookie->id_lang);
		$profiles = Profile::getProfiles((int)($cookie->id_lang));
		$accesses = Profile::getProfileAccesses((int)($currentProfile));
		
		echo '
		<script type="text/javascript">
			setLang(Array(\''.$this->l('Profile updated').'\', \''.$this->l('Request failed!').'\', \''.$this->l('Update in progress. Please wait.').'\', \''.$this->l('Server connection failed!').'\'));
		</script>
		<div id="ajax_confirmation"></div>
		<table class="table" cellspacing="0">
			<tr>
				<th '.($currentProfile == (int)(_PS_ADMIN_PROFILE_) ? 'colspan="6"' : '').'>
					<select name="profile" onchange="redirect(\''.Tools::getHttpHost(true, true).$currentIndex.'&token='.$this->token.'&profile=\'+this.options[this.selectedIndex].value)">';
		if ($profiles)
			foreach ($profiles AS $profile)
				echo '<option value="'.(int)($profile['id_profile']).'" '.((int)($profile['id_profile']) == $currentProfile ? 'selected="selected"' : '').'>'.$profile['name'].'</option>';

		$tabsize = sizeof($tabs);
		foreach ($tabs AS $tab)
			if ($tab['id_tab'] > $tabsize)
				$tabsize = $tab['id_tab'];
		echo '
					</select>
				</th>';
		
		if ($currentProfile != (int)(_PS_ADMIN_PROFILE_))
			echo '
				<th class="center">'.$this->l('View').'<br /><input type="checkbox" name="1" id="viewall" onclick="ajax_power(this, \'view\', -1, '.$currentProfile.', \''.$this->token.'\', \''.$tabsize.'\', \''.sizeof($tabs).'\')" /></th>
				<th class="center">'.$this->l('Add').'<br /><input type="checkbox" name="1" id="addall" onclick="ajax_power(this, \'add\', -1, '.$currentProfile.', \''.$this->token.'\', \''.$tabsize.'\', \''.sizeof($tabs).'\')" /></th>
				<th class="center">'.$this->l('Edit').'<br /><input type="checkbox" name="1" id="editall" onclick="ajax_power(this, \'edit\', -1, '.$currentProfile.', \''.$this->token.'\', \''.$tabsize.'\', \''.sizeof($tabs).'\')" /></th>
				<th class="center">'.$this->l('Delete').'<br /><input type="checkbox" name="1" id="deleteall" onclick="ajax_power(this, \'delete\', -1, '.$currentProfile.', \''.$this->token.'\', \''.$tabsize.'\', \''.sizeof($tabs).'\')" /></th>
				<th class="center">'.$this->l('All').'<br /><input type="checkbox" name="1" id="allall" onclick="ajax_power(this, \'all\', -1, '.$currentProfile.', \''.$this->token.'\', \''.$tabsize.'\', \''.sizeof($tabs).'\')" /></th>
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
					 		$this->printTabAccess($currentProfile, $child, $accesses[$child['id_tab']], true, $tabsize, sizeof($tabs));
				}
		echo '</table>';
	}
	
	private function printTabAccess($currentProfile, $tab, $access, $is_child, $tabsize, $tabnumber)
	{
		$result_accesses = 0;
		$perms = array('view', 'add', 'edit', 'delete');
		echo '<tr><td'.($is_child ? '' : ' class="bold"').'>'.($is_child ? ' &raquo; ' : '').$tab['name'].'</td>';
		foreach ($perms as $perm)
		{
			if($this->tabAccess['edit'] == 1)
				echo '<td class="center"><input type="checkbox" name="1" id=\''.$perm.(int)($access['id_tab']).'\' class=\''.$perm.' '.(int)($access['id_tab']).'\' onclick="ajax_power(this, \''.$perm.'\', '.(int)($access['id_tab']).', '.(int)($access['id_profile']).', \''.$this->token.'\', \''.$tabsize.'\', \''.$tabnumber.'\')" '.((int)($access[$perm]) == 1 ? 'checked="checked"' : '').'/></td>';
			else
				echo '<td class="center"><input type="checkbox" name="1" disabled="disabled" '.((int)($access[$perm]) == 1 ? 'checked="checked"' : '').' /></td>';
			$result_accesses += $access[$perm];
		}
		echo '<td class="center"><input type="checkbox" name="1" id=\'all'.(int)($access['id_tab']).'\' class=\'all '.(int)($access['id_tab']).'\' onclick="ajax_power(this, \'all\', '.(int)($access['id_tab']).', '.(int)($access['id_profile']).', \''.$this->token.'\', \''.$tabsize.'\', \''.$tabnumber.'\')" '.($result_accesses == 4 ? 'checked="checked"' : '').'/></td></tr>';
	 
	}
}


