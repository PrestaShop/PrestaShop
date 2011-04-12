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

include_once(dirname(__FILE__).'/../../classes/AdminTab.php');

class AdminWebservice extends AdminTab
{

	public function __construct()
	{
	 	$this->table = 'webservice_account';
	 	$this->className = 'WebserviceKey';
	 	$this->lang = false;
	 	$this->edit = true;
	 	$this->delete = true;
	 	
 		$this->id_lang_default = Configuration::get('PS_LANG_DEFAULT');
		
		$this->fieldsDisplay = array(
		'key' => array('title' => $this->l('Key'), 'align' => 'center', 'width' => 32),
		'active' => array('title' => $this->l('Enabled'), 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false),
		'description' => array('title' => $this->l('Key description'), 'align' => 'center', 'orderby' => false)
		);
		
		$this->optionTitle = $this->l('Configuration');
		if (file_exists(_PS_ROOT_DIR_.'/.htaccess'))
			$this->_fieldsOptions = array('PS_WEBSERVICE' => array('title' => $this->l('Enable PrestaShop Webservice:'), 
																	'desc' => $this->l('Before activating the webservice, you must be sure to: ').
																						'<ol><li>'.$this->l('be certain URL rewrite is available on this server').
																						'</li><li>'.$this->l('be certain that the 5 methods GET, POST, PUT, DELETE and HEAD are supported by this server').
																						'</li></ol>', 
																	'cast' => 'intval',
																	'type' => 'bool'));
		parent::__construct();
	}
	
	protected function afterAdd($object) {
		WebserviceKey::setPermissionForAccount($object->id, Tools::getValue('resources', array()));
	}
	
	protected function afterUpdate($object) {
		WebserviceKey::setPermissionForAccount($object->id, Tools::getValue('resources', array()));
	}
	
	public function displayList()
	{
		global $cookie, $currentIndex;
		$warnings = array();
		if (!file_exists(_PS_ROOT_DIR_.'/.htaccess'))
			$warnings[] = $this->l('In order to enable the PrestaShop Webservice, please generate the .htaccess file via the "Generators" tab (in the "Tools" tab).');
		if (strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') === false)
			$warnings[] = $this->l('To avoid operating problems, please use an Apache server.');
		{
			if (function_exists('apache_get_modules'))
			{
				$apache_modules = apache_get_modules();
				if (!in_array('mod_auth_basic', $apache_modules))
					$warnings[] = $this->l('Please activate the Apache module \'mod_auth_basic\' to allow authentication of PrestaShop webservice.');
				if (!in_array('mod_rewrite', $apache_modules))
					$warnings[] = $this->l('Please activate the Apache module \'mod_rewrite\' to allow using the PrestaShop webservice.');
			}
			else
			{
				$warnings[] = $this->l('We could not check if basic authentication and rewrite extensions are activated. Please manually check if they are activated in order to use the PrestaShop webservice.');
			}
		}
		if (!extension_loaded('SimpleXML'))
			$warnings[] = $this->l('Please activate the PHP extension \'SimpleXML\' to allow testing of PrestaShop webservice.');
		if (!configuration::get('PS_SSL_ENABLED'))
			$warnings[] = $this->l('If possible, it is preferable to use SSL (https) for webservice calls, as it avoids the security issues of type "man in the middle".');
		
		$this->displayWarning($warnings);
		
		parent::displayList();
	}
	
	public function displayForm($isMainTab = true)
	{
		global $currentIndex;
		parent::displayForm();
		
		if (!($obj = $this->loadObject(true)))
			return;
		echo '
		<form action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" method="post" enctype="multipart/form-data">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset><legend><img src="../img/admin/access.png" />'.$this->l('Webservice Accounts').'</legend>
				<label>'.$this->l('Key:').'</label>
				<div class="margin-form">
					<input type="text" size="32" name="key" id="code" value="'.htmlentities(Tools::getValue('key', $obj->key), ENT_COMPAT, 'UTF-8').'" />
					<input type="button" value="'.$this->l('   Generate!   ').'" class="button" onclick="gencode(32)" />
					<sup>*</sup>
					<p class="clear">'.$this->l('Webservice account key').'</p>
				</div>
				<label>'.$this->l('Key description').'</label>
				<div class="margin-form">
					<textarea rows="3" style="width:400px" name="description">'.htmlentities(Tools::getValue('description', $obj->description), ENT_COMPAT, 'UTF-8').'</textarea>
					<p class="clear">'.$this->l('Key description').'</p>
				</div>
				<label>'.$this->l('Status:').' </label>
				<div class="margin-form">
					<input type="radio" name="active" id="active_on" value="1" '.((!$obj->id OR Tools::getValue('active', $obj->active)) ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="active" id="active_off" value="0" '.((!Tools::getValue('active', $obj->active) AND $obj->id) ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
				</div>
				<label>'.$this->l('Permissions:').' </label>
				<div class="margin-form">
					<p>'.$this->l('Set the resource permissions for this key:').'</p>
					<table border="0" cellspacing="0" cellpadding="0" class="permissions">
						<thead>
							<tr>
								<th>'.$this->l('Resource').'</th>
								<th width="30"></th>
								<th width="50">'.$this->l('View (GET)').'</th>
								<th width="50">'.$this->l('Modify (PUT)').'</th>
								<th width="50">'.$this->l('Add (POST)').'</th>
								<th width="50">'.$this->l('Delete (DELETE)').'</th>
								<th width="50">'.$this->l('Fast view (HEAD)').'</th>
							</tr>
							
						</thead>
						<tbody>
						<tr class="all" style="vertical-align:cen">
								<th></th>
								<th></th>
								<th><input type="checkbox" class="all_get get " /></th>
								<th><input type="checkbox" class="all_put put " /></th>
								<th><input type="checkbox" class="all_post post " /></th>
								<th><input type="checkbox" class="all_delete delete" /></th>
								<th><input type="checkbox" class="all_head head" /></th>
							</tr>
						';
$ressources = WebserviceRequest::getResources();
$permissions = WebserviceKey::getPermissionForAccount($obj->key);
foreach ($ressources as $resourceName => $resource)
echo '
							<tr>
								<th>'.$resourceName.'</th>
								<th><input type="checkbox" class="all"/></th>
								<td><input type="checkbox" class="get" name="resources['.$resourceName.'][GET]" '.(isset($permissions[$resourceName]) && in_array('GET', $permissions[$resourceName]) ? 'checked="checked"' : '').' /></td>
								<td><input type="checkbox" class="put" name="resources['.$resourceName.'][PUT]" '.(isset($permissions[$resourceName]) && in_array('PUT', $permissions[$resourceName]) ? 'checked="checked"' : '').'/></td>
								<td><input type="checkbox" class="post" name="resources['.$resourceName.'][POST]" '.(isset($permissions[$resourceName]) && in_array('POST', $permissions[$resourceName]) ? 'checked="checked"' : '').'/></td>
								<td><input type="checkbox" class="delete" name="resources['.$resourceName.'][DELETE]" '.(isset($permissions[$resourceName]) && in_array('DELETE', $permissions[$resourceName]) ? 'checked="checked"' : '').'/></td>
								<td><input type="checkbox" class="head" name="resources['.$resourceName.'][HEAD]" '.(isset($permissions[$resourceName]) && in_array('HEAD', $permissions[$resourceName]) ? 'checked="checked"' : '').'/></td>
							</tr>';
echo '
						</tbody>
					</table>
					<script>';?>
				
						$(function() {
							$('table.permissions input.all').click(function() {
								if($(this).is(':checked'))
									$(this).parent().parent().find('input.get:not(:checked), input.put:not(:checked), input.post:not(:checked), input.delete:not(:checked), input.head:not(:checked)').click();
								else
									$(this).parent().parent().find('input.get:checked, input.put:checked, input.post:checked, input.delete:checked, input.head:checked').click();
							});
							$('table.permissions .all_get').click(function() {
								if($(this).is(':checked'))
									$(this).parent().parent().parent().find('input.get:not(:checked)').click();
								else
									$(this).parent().parent().parent().find('input.get:checked').click();
							});
							$('table.permissions .all_put').click(function() {
								if($(this).is(':checked'))
									$(this).parent().parent().parent().find('input.put:not(:checked)').click();
								else
									$(this).parent().parent().parent().find('input.put:checked').click();
							});
							$('table.permissions .all_post').click(function() {
								if($(this).is(':checked'))
									$(this).parent().parent().parent().find('input.post:not(:checked)').click();
								else
									$(this).parent().parent().parent().find('input.post:checked').click();
							});
							$('table.permissions .all_delete').click(function() {
								if($(this).is(':checked'))
									$(this).parent().parent().parent().find('input.delete:not(:checked)').click();
								else
									$(this).parent().parent().parent().find('input.delete:checked').click();
							});
							$('table.permissions .all_head').click(function() {
								if($(this).is(':checked'))
									$(this).parent().parent().parent().find('input.head:not(:checked)').click();
								else
									$(this).parent().parent().parent().find('input.head:checked').click();
							});
						});
				<?php echo '
					</script>
				</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>';
	}
	public function postProcess()
	{
		if (Tools::getValue('key') && strlen(Tools::getValue('key')) < 32)
			$this->_errors[] = Tools::displayError($this->l('Key length must be 32 character long'));
		return parent::postProcess();
	}
}
