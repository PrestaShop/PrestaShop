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
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminEmployeesControllerCore extends AdminController
{
 	/** @var array profiles list */
	private $profiles_array = array();

	/** @var array themes list*/
	private $themes = array();

	public function __construct()
	{
	 	$this->table = 'employee';
		$this->className = 'Employee';
	 	$this->lang = false;

		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->requiredDatabase = true;

		$this->context = Context::getContext();

	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));
		/*
		check if there are more than one superAdmin
		if it's the case then we can delete a superAdmin
		*/
		$super_admin = Employee::countProfile(_PS_ADMIN_PROFILE_, true);
		if ($super_admin == 1)
		{
			$super_admin_array = Employee::getEmployeesByProfile(_PS_ADMIN_PROFILE_, true);
			$super_admin_id = array();
			foreach ($super_admin_array as $key => $val)
				$super_admin_id[] = $val['id_employee'];
			$this->addRowActionSkipList('delete', $super_admin_id);
		}

		$profiles = Profile::getProfiles($this->context->language->id);
		if (!$profiles)
			$this->errors[] = Tools::displayError('No profile');
		else
			foreach ($profiles as $profile)
				$this->profiles_array[$profile['name']] = $profile['name'];

		$this->fieldsDisplay = array(
			'id_employee' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'lastname' => array('title' => $this->l('Last name'), 'width' => 'auto'),
			'firstname' => array('title' => $this->l('First name'), 'width' => 130),
			'email' => array('title' => $this->l('E-mail address'), 'width' => 180),
			'profile' => array('title' => $this->l('Profile'), 'width' => 90, 'type' => 'select', 'list' => $this->profiles_array, 'filter_key' => 'pl!name'),
			'active' => array('title' => $this->l('Can log in'), 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'width' => 30),
		);

		$this->options = array(
			'general' => array(
				'title' =>	$this->l('Employees options'),
				'fields' =>	array(
					'PS_PASSWD_TIME_BACK' => array(
						'title' => $this->l('Password regenerate:'),
						'desc' => $this->l('Security minimum time to wait to regenerate a new password'),
						'cast' => 'intval',
						'size' => 5,
						'type' => 'text',
						'suffix' => ' '.$this->l('minutes'),
						'visibility' => Shop::CONTEXT_ALL
					),
					'PS_BO_ALLOW_EMPLOYEE_FORM_LANG' => array(
						'title' => $this->l('Memorize form language:'),
						'desc' => $this->l('Allow employees to save their own default form language'),
						'cast' => 'intval',
						'type' => 'select',
						'identifier' => 'value',
						'list' => array(
							'0' => array('value' => 0, 'name' => $this->l('No')),
							'1' => array('value' => 1, 'name' => $this->l('Yes')
						)
					), 'visibility' => Shop::CONTEXT_ALL)
				),
				'submit' => array()
			)
		);

		$path = _PS_ADMIN_DIR_.'/themes/';
		foreach (scandir($path) as $theme)
			if (file_exists($path.$theme.'/css/admin.css'))
				$this->themes[] = $theme;

		parent::__construct();

		// An employee can edit its own profile
		if ($this->context->employee->id == Tools::getValue('id_employee'))
		{
			$this->tabAccess['view'] = '1';
			$this->tabAccess['edit'] = '1';
		}
	}

	public function renderList()
	{
 		$this->_select = 'pl.`name` AS profile';
		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'profile` p ON a.`id_profile` = p.`id_profile`
		LEFT JOIN `'._DB_PREFIX_.'profile_lang` pl ON (pl.`id_profile` = p.`id_profile` AND pl.`id_lang` = '.(int)$this->context->language->id.')';

		return parent::renderList();
	}

	public function renderForm()
	{
		if (!($obj = $this->loadObject(true)))
			return;

		$available_profiles = Profile::getProfiles($this->context->language->id);

		if ($obj->id_profile == _PS_ADMIN_PROFILE_ && $this->context->employee->id_profile != _PS_ADMIN_PROFILE_)
		{
			$this->errors[] = Tools::displayError('You cannot edit SuperAdmin profile.');
			return parent::renderForm();
		}

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Employees'),
				'image' => '../img/admin/nav-user.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('First name:'),
					'name' => 'firstname',
					'size' => 33,
					'required' => true
				),
				array(
					'type' => 'text',
					'label' => $this->l('Last name:'),
					'name' => 'lastname',
					'size' => 33,
					'required' => true
				),
				array(
					'type' => 'password',
					'label' => $this->l('Password:'),
					'name' => 'passwd',
					'required' => true,
					'size' => 33,
					'desc' => ($obj->id ?
								$this->l('Leave blank if you do not want to change your password') :
									$this->l('Min. 8 characters; use only letters, numbers or').' -_')
				),
				array(
					'type' => 'text',
					'label' => $this->l('E-mail address:'),
					'name' => 'email',
					'size' => 33,
					'required' => true
				),
				array(
					'type' => 'color',
					'label' => $this->l('Back office color:'),
					'name' => 'bo_color',
					'class' => 'color mColorPickerInput',
					'size' => 20,
					'desc' => $this->l('Back office background will be displayed in this color. HTML colors only (e.g.,').' "lightblue", "#CC6600")'
				),
				array(
					'type' => 'select',
					'label' => $this->l('Language:'),
					'name' => 'id_lang',
					'required' => true,
					'options' => array(
						'query' => Language::getLanguages(),
						'id' => 'id_lang',
						'name' => 'name'
					)
				),
				array(
					'type' => 'select_theme',
					'label' => $this->l('Theme:'),
					'name' => 'bo_theme',
					'options' => array('query' => $this->themes),
					'desc' => $this->l('Back office theme')
				)
			)
		);

		if ((int)$this->tabAccess['edit'])
		{
			$this->fields_form['input'][] = array(
				'type' => 'radio',
				'label' => $this->l('Show screencast:'),
				'name' => 'bo_show_screencast',
				'required' => false,
				'class' => 't',
				'is_bool' => true,
				'values' => array(
					array(
						'id' => 'bo_show_screencast_on',
						'value' => 1,
						'label' => $this->l('Enabled')
					),
					array(
						'id' => 'bo_show_screencast_off',
						'value' => 0,
						'label' => $this->l('Disabled')
					)
				),
				'desc' => $this->l('Show the welcome video on the dashbord of the back office')
			);

			$this->fields_form['input'][] = array(
				'type' => 'radio',
				'label' => $this->l('Status:'),
				'name' => 'active',
				'required' => false,
				'class' => 't',
				'is_bool' => true,
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Enabled')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('Disabled')
					)
				),
				'desc' => $this->l('Allow or disallow this employee to log into this Back Office')
			);

			// if employee is not SuperAdmin (id_profile = 1), don't make it possible to select the admin profile
			if ($this->context->employee->id_profile != _PS_ADMIN_PROFILE_)
				 foreach ($available_profiles as $i => $profile)
				 	if ($available_profiles[$i]['id_profile'] == _PS_ADMIN_PROFILE_)
					{
						unset($available_profiles[$i]);
						break;
					}
			$this->fields_form['input'][] = array(
				'type' => 'select',
				'label' => $this->l('Profile:'),
				'name' => 'id_profile',
				'required' => true,
				'options' => array(
					'query' => $available_profiles,
					'id' => 'id_profile',
					'name' => 'name',
					'default' => array(
						'value' => '',
						'label' => $this->l('-- Choose --')
					)
				)
			);

			if (Shop::isFeatureActive())
			{
				$this->context->smarty->assign('_PS_ADMIN_PROFILE_', (int)_PS_ADMIN_PROFILE_);
				$this->fields_form['input'][] = array(
					'type' => 'shop',
					'label' => $this->l('Shop association:'),
					'name' => 'checkBoxShopAsso',
					'values' => Shop::getTree()
				);
			}
		}

		$this->fields_form['submit'] = array(
			'title' => $this->l('   Save   '),
			'class' => 'button'
		);

		$this->fields_value['passwd'] = false;

		return parent::renderForm();
	}

	protected function _childValidation()
	{
		if (!($obj = $this->loadObject(true)))
			return false;
		$email = $this->getFieldValue($obj, 'email');
		if (!Validate::isEmail($email))
	 		$this->errors[] = Tools::displayError('Invalid e-mail');
		else if (Employee::employeeExists($email) && !Tools::getValue('id_employee'))
			$this->errors[] = Tools::displayError('An account already exists for this e-mail address:').' '.$email;
	}

	public function postProcess()
	{
		if (Tools::isSubmit('deleteemployee') || Tools::isSubmit('status') || Tools::isSubmit('statusemployee'))
		{
			/* PrestaShop demo mode */
			if (_PS_MODE_DEMO_ && $id_employee = Tools::getValue('id_employee') && (int)$id_employee == _PS_DEMO_MAIN_BO_ACCOUNT_)
			{
				$this->errors[] = Tools::displayError('This functionnality has been disabled.');
				return;
			}
			/* PrestaShop demo mode*/

			if ($this->context->employee->id == Tools::getValue('id_employee'))
			{
				$this->errors[] = Tools::displayError('You cannot disable or delete your own account.');
				return false;
			}

			$employee = new Employee(Tools::getValue('id_employee'));
			if ($employee->isLastAdmin())
			{
					$this->errors[] = Tools::displayError('You cannot disable or delete the last administrator account.');
					return false;
			}

			// It is not possible to delete an employee if he manages warehouses
			$warehouses = Warehouse::getWarehousesByEmployee((int)Tools::getValue('id_employee'));
			if (Tools::isSubmit('deleteemployee') && count($warehouses) > 0)
			{
				$this->errors[] = Tools::displayError('You cannot delete this account since it manages warehouses. Check your warehouses first.');
				return false;
			}
		}
		else if (Tools::isSubmit('submitAddemployee'))
		{
			$employee = new Employee((int)Tools::getValue('id_employee'));
			if (!(int)$this->tabAccess['edit'])
				$_POST['id_profile'] = $_GET['id_profile'] = $employee->id_profile;

			if ($employee->isLastAdmin())
			{
				if (Tools::getValue('id_profile') != (int)_PS_ADMIN_PROFILE_)
				{
					$this->errors[] = Tools::displayError('You should have at least one employee in the administrator group.');
					return false;
				}

				if (Tools::getvalue('active') == 0)
				{
					$this->errors[] = Tools::displayError('You cannot disable or delete the last administrator account.');
					return false;
				}
			}

			if (!in_array(Tools::getValue('bo_theme'), $this->themes))
			{
				$this->errors[] = Tools::displayError('Invalid theme.');
					return false;
			}

			$assos = $this->getAssoShop($this->table);

			if (count($assos[0]) == 0 && $this->table = 'employee')
				if (Shop::isFeatureActive() && _PS_ADMIN_PROFILE_ != $_POST['id_profile'])
					$this->errors[] = Tools::displayError('The employee must be associated with at least one shop');
		}
		return parent::postProcess();
	}

	public function initContent()
	{
		if ($this->context->employee->id == Tools::getValue('id_employee'))
			$this->display = 'edit';

		return parent::initContent();
	}
}


