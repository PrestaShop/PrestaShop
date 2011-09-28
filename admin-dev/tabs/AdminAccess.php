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

include_once(_PS_ADMIN_DIR_.'/../classes/AdminTab.php');

class AdminAccess extends AdminTab
{
	private $return_status;
	private $return_message;
	
	public function processSubmitAddAccess()
	{
		$perm = Tools::getValue('perm') ;
		if (!in_array($perm, array('view', 'add', 'edit', 'delete', 'all')))
			throw new PrestashopException('permission not exists');

		$enabled = (int)Tools::getValue('enabled') ;
		$id_tab = (int)(Tools::getValue('id_tab')); 
		$id_profile = (int)(Tools::getValue('id_profile'));
		$res = true;

		if ($id_tab == -1 AND $perm == 'all' AND $enabled == 0)
			$res &= Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'access` SET `view` = '.$enabled.', `add` = '.$enabled.', `edit` = '.$enabled.', `delete` = '.$enabled.' WHERE `id_profile` = '.(int)($id_profile).' AND `id_tab` != 31');
		else if ($id_tab == -1 AND $perm == 'all')
			$res &= Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'access` SET `view` = '.$enabled.', `add` = '.$enabled.', `edit` = '.$enabled.', `delete` = '.$enabled.' WHERE `id_profile` = '.(int)($id_profile));
		else if ($id_tab == -1)
			$res &= Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'access` SET `'.pSQL($perm).'` = '.$enabled.' WHERE `id_profile` = '.(int)($id_profile));
		else if ($perm == 'all')
			$res &= Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'access` SET `view` = '.$enabled.', `add` = '.$enabled.', `edit` = '.$enabled.', `delete` = '.$enabled.' WHERE `id_tab` = '.(int)($id_tab).' AND `id_profile` = '.(int)($id_profile));
		else
			$res &= Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'access` SET `'.pSQL($perm).'` = '.$enabled.' WHERE `id_tab` = '.(int)($id_tab).' AND `id_profile` = '.(int)($id_profile));
		
		$this->return_status = $res?'ok':'error';
		if ($res)
			$this->return_message = $this->l('Access successfully updated');
		else
			$this->return_message = $this->l('An error when updating access');
	}

	public function processChangeModuleAccess()
	{
		$perm = Tools::getValue('perm');
		$enabled = (int)Tools::getValue('enabled');
		$id_module = (int)Tools::getValue('id_module');
		$id_profile = (int)Tools::getValue('id_profile');
		$res = true;

		if (!in_array($perm, array('view', 'configure')))
			throw new PrestashopException('permission not exists');
			
		if ($id_module == -1)
			$res &= Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'module_access` SET `'.pSQL($perm).'` = '.(int)$enabled.' WHERE `id_profile` = '.(int)$id_profile);
		else
			$res &= Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'module_access` SET `'.pSQL($perm).'` = '.(int)$enabled.' WHERE `id_module` = '.(int)$id_module.' AND `id_profile` = '.(int)$id_profile);
		
		$this->return_status = $res?'ok':'error';
		if ($res)
			$this->return_message = $this->l('Access successfully updated.');
		else
			$this->return_message = $this->l('An error when updating access.');
	}
	
	
	public function displayAjax()
	{
		$return = array('result'=>$this->return_status,'msg'=>$this->return_message);
		 
		echo Tools::jsonEncode($return);
	}
	public function display()
	{
		$this->displayForm();
		echo '<script type="text/javascript">
				$(document).ready(function(){
					$(".ajaxPower").change(function(){
						var tout = $(this).attr("rel").split("||"); 
						var id_tab = tout[0];
						var id_profile = tout[1];
						var perm = tout[2];
						var enabled = $(this).is(":checked")? 1 : 0;
						var tabsize = tout[3];
						var tabnumber = tout[4];
						
						perfect_access_js_gestion(this, perm, id_tab, tabsize, tabnumber);
						
						$.ajax({
							type:"POST",
							url : "ajax-tab.php",
							async: true,
							data : {
								id_tab:id_tab,
								id_profile:id_profile,
								perm:perm,
								enabled:enabled,
								submitAddaccess:"1",
								ajaxMode : "1",
								token : "'.$this->token.'",
								controller:"AdminAccess"
							},
							success : function(res,textStatus,jqXHR)
							{
								try
								{
									res = $.parseJSON(res);
									if (res.result == "ok")
										showSuccessMessage(res.msg);
									else
										showErrorMessage(res.msg);
								}
								catch(e)
								{
									alert("oups");
								}
							}
						});
					});
				});
				
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
		$accesses = Profile::getProfileAccesses($currentProfile);
		
		echo '
		<script type="text/javascript">
			setLang(Array(\''.$this->l('Profile updated').'\', \''.$this->l('Request failed!').'\', \''.$this->l('Update in progress. Please wait.').'\', \''.$this->l('Server connection failed!').'\'));
		</script>
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
						'.($this->tabAccess['edit'] == 1 ? ' rel="-1||'.$currentProfile.'||view||'.$tabsize.'||'.sizeof($tabs).'" class="ajaxPower"' : 'disabled="disabled"').' />
					'.$this->l('View').'
				</th>
				<th class="center">
					<input type="checkbox" name="1" id="addall"
					'.($this->tabAccess['edit'] == 1 ? ' rel="-1||'.$currentProfile.'||add||'.$tabsize.'||'.sizeof($tabs).'" class="ajaxPower"' : 'disabled="disabled"').' />
					'.$this->l('Add').'
				</th>
				<th class="center">
					<input type="checkbox" name="1" id="editall" 
					'.($this->tabAccess['edit'] == 1 ? ' rel="-1||'.$currentProfile.'||edit||'.$tabsize.'||'.sizeof($tabs).'" class="ajaxPower"' : 'disabled="disabled"').' />
					'.$this->l('Edit').'
				</th>
				<th class="center">
					<input type="checkbox" name="1" id="deleteall" 
					'.($this->tabAccess['edit'] == 1 ? ' rel="-1||'.$currentProfile.'||delete||'.$tabsize.'||'.sizeof($tabs).'" class="ajaxPower"' : 'disabled="disabled"').' />
					'.$this->l('Delete').'
				</th>
				<th class="center">
					<input type="checkbox" name="1" id="allall" 
					'.($this->tabAccess['edit'] == 1 ? ' rel="-1||'.$currentProfile.'||all||'.$tabsize.'||'.sizeof($tabs).'" class="ajaxPower"' : 'disabled="disabled"').' />
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
							{
							
								$this->printTabAccess($currentProfile, $child, $accesses[$child['id_tab']], true, $tabsize, sizeof($tabs));
							}
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
				echo '<td><input type="checkbox" name="1" id="'.$perm.(int)($access['id_tab']).'" rel="'.(int)($access['id_tab']).'||'.(int)($currentProfile).'||'.$perm.'||'.$tabsize.'||'.$tabnumber.'" class="ajaxPower '.$perm.' '.(int)($access['id_tab']).'" '.((int)($access[$perm]) == 1 ? 'checked="checked"' : '').'/></td>';
			else
				echo '<td><input type="checkbox" name="1" disabled="disabled" '.((int)($access[$perm]) == 1 ? 'checked="checked"' : '').' /></td>';
			$result_accesses += $access[$perm];
		}
		echo '<td>
			<input type="checkbox" name="1" id=\'all'.(int)($access['id_tab']).'\'
				'.($this->tabAccess['edit'] == 1 ? ' rel="'.(int)($access['id_tab']).'||'.(int)($currentProfile).'||all||'.$tabsize.'||'.$tabnumber.'" class="ajaxPower all '.(int)($access['id_tab']).'"' : '  class="all '.(int)($access['id_tab']).'" disabled="disabled"').'
				'.($result_accesses == 4 ? 'checked="checked"' : '').'
			/>
		</td></tr>';
	}
	
	public function ajaxProcess()
	{
		if ($this->tabAccess['edit'] == 1)
		{
			if (Tools::isSubmit('submitAddaccess'))
				$this->processSubmitAddAccess();
			if (Tools::isSubmit('changeModuleAccess'))
					$this->processChangeModuleAccess();
		}
	}
	
	private function displayModuleAccesses($currentProfile)
	{
		echo '
		<script type="text/javascript">
			$(document).ready(function(){
				$(".changeModuleAccess").change(function(){
					var tout = $(this).attr("rel").split("||");
					var id_module = tout[0];
					var perm = tout[1];
					var enabled = $(this).is(":checked")? 1 : 0;
					
					if (id_module == -1)
						$(\'.ajax-ma-\'+perm).each(function(key, value) {
							$(this).attr("checked", enabled);
						});
					else if (!enabled)
						$(\'#ajax-ma-\'+perm+\'-master\').each(function(key, value) {
							$(this).attr("checked", enabled);
						});

					$.ajax({
						type:"POST",
						url : "ajax-tab.php",
						async: true,
						data : {
							ajaxMode : "1",
							id_module:id_module,
							perm:perm,
							enabled:enabled,
							id_profile:'.(int)$currentProfile.',
							changeModuleAccess:"1",
							token : "'.$this->token.'",
							controller:"AdminAccess"
						},
						success : function(res,textStatus,jqXHR)
						{
							try
							{
								res = $.parseJSON(res);
								if (res.result == "ok")
									showSuccessMessage(res.msg);
								else
									showErrorMessage(res.msg);
							}
							catch(e)
							{
								alert("oups");
							}
						}
					});
				});
			});
		</script>		
		<table class="table float" cellspacing="0" style="margin-left:20px">
		<tr>
			<th>'.$this->l('Modules').'</th>
			<th class="center"><input type="checkbox" id="ajax-ma-view-master" '.($this->tabAccess['edit'] == 1 ? 'class="changeModuleAccess" rel="-1||view"' : 'disabled="disabled"').' /> '.$this->l('View').'</th>
			<th class="center"><input type="checkbox" id="ajax-ma-configure-master" '.($this->tabAccess['edit'] == 1 ? 'class="changeModuleAccess" rel="-1||configure"' : 'disabled="disabled"').' /> '.$this->l('Configure').'</th>
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
						<input type="checkbox"
							'.((int)$module['view'] == 1 ? 'checked="checked"' : '').'
							'.($this->tabAccess['edit'] == 1 ? 'class="ajax-ma-view changeModuleAccess" rel="'.(int)$module['id_module'].'||view"' : ' class="ajax-ma-view" disabled="disabled"').'
						/>
					</td>
					<td>
						<input type="checkbox"
							'.((int)$module['configure'] == 1 ? 'checked="checked"' : '').'
							'.($this->tabAccess['edit'] == 1 ? ' class="ajax-ma-configure changeModuleAccess" rel="'.(int)$module['id_module'].'||configure"' : ' class="ajax-ma-configure" disabled="disabled"').'
						/>
					</td>
				</tr>';

		echo '</table>';
	}
}


