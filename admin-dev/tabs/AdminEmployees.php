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

class AdminEmployees extends AdminTab
{
 	/** @var array profiles list */
	private $profilesArray = array();
 
	public function __construct()
	{
	 	global $cookie;
	 	
	 	$this->table = 'employee';
	 	$this->className = 'Employee';
	 	$this->lang = false;
	 	$this->edit = true; 
	 	$this->delete = true;		
 		$this->_select = 'pl.`name` AS profile';
		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'profile` p ON a.`id_profile` = p.`id_profile` 
		LEFT JOIN `'._DB_PREFIX_.'profile_lang` pl ON (pl.`id_profile` = p.`id_profile` AND pl.`id_lang` = '.(int)($cookie->id_lang).')';
		
		$profiles = Profile::getProfiles((int)($cookie->id_lang));
		if (!$profiles)
			$this->_errors[] = Tools::displayError('No profile');
		else
			foreach ($profiles AS $profile)
				$this->profilesArray[$profile['name']] = $profile['name'];
		
		$this->fieldsDisplay = array(
		'id_employee' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
		'lastname' => array('title' => $this->l('Last name'), 'width' => 130),
		'firstname' => array('title' => $this->l('First name'), 'width' => 130),
		'email' => array('title' => $this->l('E-mail address'), 'width' => 180), 
		'profile' => array('title' => $this->l('Profile'), 'width' => 90, 'type' => 'select', 'select' => $this->profilesArray, 'filter_key' => 'pl!name'),
		'active' => array('title' => $this->l('Can log in'), 'align' => 'center', 'active' => 'status', 'type' => 'bool'));

		$this->optionTitle = $this->l('Employees options');
		$this->_fieldsOptions = array(
			'PS_PASSWD_TIME_BACK' => array('title' => $this->l('Password regenerate:'), 'desc' => $this->l('Security minimum time to wait to regenerate a new password'), 'cast' => 'intval', 'size' => 5, 'type' => 'text', 'suffix' => ' '.$this->l('minutes')),
			'PS_BO_ALLOW_EMPLOYEE_FORM_LANG' => array('title' => $this->l('Memorize form language:'), 'desc' => $this->l('Allow employees to save their own default form language'), 'cast' => 'intval', 'type' => 'select', 'identifier' => 'value', 'list' => array(
				'0' => array('value' => 0, 'name' => $this->l('No')), 
				'1' => array('value' => 1, 'name' => $this->l('Yes')) 
			))
		);

		parent::__construct();
	}
	
	protected function _childValidation() 
	{
		if (!($obj = $this->loadObject(true)))
			return false;
		$email = $this->getFieldValue($obj, 'email');
		if (!Validate::isEmail($email))
	 		$this->_errors[] = Tools::displayError('Invalid e-mail');
		else if (Employee::employeeExists($email) AND !Tools::getValue('id_employee'))
			$this->_errors[] = Tools::displayError('An account already exists for this e-mail address:').' '.$email;
	}

	public function displayForm($isMainTab = true)
	{
		global $currentIndex, $cookie;
		parent::displayForm();
		
		if (!($obj = $this->loadObject(true)))
			return;
		$profiles = Profile::getProfiles((int)($cookie->id_lang));

		echo '<script type="text/javascript" src="'._PS_JS_DIR_.'/jquery/jquery-colorpicker.js"></script>
		 	 <script type="text/javascript">
				var employeePage = true;
		 	 </script>

		
		<form action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.((int)$this->tabAccess['view'] ? '' : '&updateemployee&id_employee='.(int)$obj->id).'" method="post" enctype="multipart/form-data" autocomplete="off">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
		'.((int)$this->tabAccess['view'] ? '' : '<input type="hidden" name="back" value="'.$currentIndex.'&token='.$this->token.'&updateemployee&id_employee='.(int)$obj->id.'" />').'
			<fieldset><legend><img src="../img/admin/nav-user.gif" />'.$this->l('Employees').'</legend>
				<label>'.$this->l('Last name:').' </label>
				<div class="margin-form">
					<input type="text" size="33" name="lastname" value="'.htmlentities($this->getFieldValue($obj, 'lastname'), ENT_COMPAT, 'UTF-8').'" /> <sup>*</sup>
				</div>
				<label>'.$this->l('First name:').' </label>
				<div class="margin-form">
					<input type="text" size="33" name="firstname" value="'.htmlentities($this->getFieldValue($obj, 'firstname'), ENT_COMPAT, 'UTF-8').'" /> <sup>*</sup>
				</div>
				<label>'.$this->l('Password:').' </label>
				<div class="margin-form">
					<input type="password" size="33" name="passwd" value="" /> <sup>*</sup>
					<p>'.($obj->id ? $this->l('Leave blank if you do not want to change your password') : $this->l('Min. 8 characters; use only letters, numbers or').' -_').'</p>
				</div>
				<label>'.$this->l('E-mail address:').' </label>
				<div class="margin-form">
					<input type="text" size="33" name="email" value="'.htmlentities($this->getFieldValue($obj, 'email'), ENT_COMPAT, 'UTF-8').'" /> <sup>*</sup>
				</div><div class="clear">&nbsp;</div>
				<label>'.$this->l('Back office color:').' </label>
				<div class="margin-form">';
				// Note : width= fix Firefox 4 display bug related to colorpicker librarie
				echo '<input type="color" width="50px" data-hex="true" class="color mColorPickerInput" name="bo_color" value="'.htmlentities($this->getFieldValue($obj, 'bo_color'), ENT_COMPAT, 'UTF-8').'" />
					<p>'.$this->l('Back office background will be displayed in this color. HTML colors only (e.g.,').' "lightblue", "#CC6600")</p>
				</div><div class="clear">&nbsp;</div>
				<label>'.$this->l('Language:').' </label>
				<div class="margin-form">
					<select name="id_lang">';
		foreach (Language::getLanguages() as $lang)
			echo '		<option value="'.(int)$lang['id_lang'].'" '.($this->getFieldValue($obj, 'id_lang') == $lang['id_lang'] ? 'selected="selected"' : '').'>'.Tools::htmlentitiesUTF8($lang['name']).'</option>';
		echo '		</select> <sup>*</sup>
				</div><div class="clear">&nbsp;</div>
				<label>'.$this->l('Theme:').' </label>
				<div class="margin-form">
					<select name="bo_theme">';
		$path = dirname(__FILE__).'/../themes/';
		foreach (scandir($path) as $theme)
			if ($theme[0] != '.' AND file_exists($path.$theme.'/admin.css'))
				echo '	<option value="'.Tools::htmlentitiesUTF8($theme).'" '.($this->getFieldValue($obj, 'bo_theme') == $theme ? 'selected="selected"' : '').'>'.Tools::htmlentitiesUTF8($theme).'</option>';
		echo '		</select> <sup>*</sup>
				</div>';
		if ((int)$this->tabAccess['edit'])
		{
			echo '<div class="clear">&nbsp;</div>
				<label>'.$this->l('UI mode:').' </label>
				<div class="margin-form">
					<input type="radio" name="bo_uimode" id="uimode_on" value="hover" '.($this->getFieldValue($obj, 'bo_uimode') == 'hover' ? 'checked="checked" ' : '').'/>
					<label class="t" for="uimode_on">'.$this->l('Hover on tabs').'</label>
					<input type="radio" name="bo_uimode" id="uimode_off" value="click" '.($this->getFieldValue($obj, 'bo_uimode') == 'click' ? 'checked="checked" ' : '').'/>
					<label class="t" for="uimode_off">'.$this->l('Click on tabs').'</label>
				</div><div class="clear">&nbsp;</div>
				<label>'.$this->l('Status:').' </label>
				<div class="margin-form">
					<input type="radio" name="active" id="active_on" value="1" '.($this->getFieldValue($obj, 'active') ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="active" id="active_off" value="0" '.(!$this->getFieldValue($obj, 'active') ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					<p>'.$this->l('Allow or disallow this employee to log into this Back Office').'</p>
				</div>
				<label>'.$this->l('Profile:').' </label>
				<div class="margin-form">
					<select name="id_profile">
						<option value="">'.$this->l('-- Choose --').'</option>';
						foreach ($profiles AS $profile)
						 	echo '<option value="'.$profile['id_profile'].'"'.($profile['id_profile'] === $this->getFieldValue($obj, 'id_profile') ? ' selected="selected"' : '').'>'.$profile['name'].'</option>';
				echo '</select> <sup>*</sup>
				</div>';
		}
		echo '<div class="clear">&nbsp;</div>
				<div class="floatr">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div><div class="clear">&nbsp;</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>';
	}
	
	public function postProcess()
	{
		global $cookie;
		
		if (Tools::isSubmit('deleteemployee') OR Tools::isSubmit('status'))
		{
			if ($cookie->id_employee == Tools::getValue('id_employee'))
			{
				$this->_errors[] = Tools::displayError('You cannot disable or delete your own account.');
				return false;
			}
			
			$employee = new Employee(Tools::getValue('id_employee'));
			if ($employee->isLastAdmin()) 
			{
					$this->_errors[] = Tools::displayError('You cannot disable or delete the last administrator account.');
					return false;
			}

		}
		elseif (Tools::isSubmit('submitAddemployee'))
		{
			if ($cookie->id_employee == Tools::getValue('id_employee') && Tools::getvalue('active') == 0)
			{
				$this->_errors[] = Tools::displayError('You cannot disable or delete the last administrator account.');
				return false;
			}
		
			$employee = new Employee(Tools::getValue('id_employee'));
			if (!(int)$this->tabAccess['edit'])
				$_POST['id_profile'] = $_GET['id_profile'] = $employee->id_profile;
			
			if ($employee->isLastAdmin()) 
			{
				if  (Tools::getValue('id_profile') != (int)(_PS_ADMIN_PROFILE_)) 
				{
					$this->_errors[] = Tools::displayError('You should have at least one employee in the administrator group.');
					return false;
				}
				
				if (Tools::getvalue('active') == 0)
				{
					$this->_errors[] = Tools::displayError('You cannot disable or delete the last administrator account.');
					return false;
				}
			}
		}
		
		return parent::postProcess();
	}
}


