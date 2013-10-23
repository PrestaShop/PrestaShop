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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminLoginControllerCore extends AdminController
{
	public function __construct()
	{
		$this->bootstrap = true;
	 	$this->errors = array();
	 	$this->context = Context::getContext();
	 	$this->display_header = false;
	 	$this->display_footer = false;

		$this->meta_title = $this->l('Administration panel');

		parent::__construct();
	}

	public function setMedia()
	{
		$admin_webpath = str_ireplace(_PS_ROOT_DIR_, '', _PS_ADMIN_DIR_);
		$admin_webpath = preg_replace('/^'.preg_quote(DIRECTORY_SEPARATOR, '/').'/', '', $admin_webpath);
		$this->addJquery();
		$this->addCSS('/'.$admin_webpath.'/themes/'.$this->bo_theme.'/css/admin-theme.css');
		$this->addJS(_PS_JS_DIR_.'vendor/jquery.validate.js');
		$this->addJS(_PS_JS_DIR_.'vendor/spin.js');
		$this->addJS(_PS_JS_DIR_.'vendor/ladda.js');
		$this->addJS(_PS_JS_DIR_.'login.js');
	}
	
	public function initContent()
	{
		if ((empty($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) == 'off') && Configuration::get('PS_SSL_ENABLED'))
		{
			// You can uncomment these lines if you want to force https even from localhost and automatically redirect
			// header('HTTP/1.1 301 Moved Permanently');
			// header('Location: '.Tools::getShopDomainSsl(true).$_SERVER['REQUEST_URI']);
			// exit();
			$clientIsMaintenanceOrLocal = in_array(Tools::getRemoteAddr(), array_merge(array('127.0.0.1'), explode(',', Configuration::get('PS_MAINTENANCE_IP'))));
			// If ssl is enabled, https protocol is required. Exception for maintenance and local (127.0.0.1) IP
			if ($clientIsMaintenanceOrLocal)
				$this->errors[] = Tools::displayError('SSL is activated. However, your IP is allowed to enter unsecure mode for maintenance or local IP issues.');
			else
			{	
				$url = 'https://'.Tools::safeOutput(Tools::getServerName()).Tools::safeOutput($_SERVER['REQUEST_URI']);
				$warningSslMessage = sprintf(Tools::displayError('SSL is activated. Please connect using the following link to <a href="%s">log into secure mode (https://)</a>'), $url);
				$this->context->smarty->assign(array('warningSslMessage' => $warningSslMessage));
			}
		}

		if (file_exists(_PS_ADMIN_DIR_.'/../install'))
			$this->context->smarty->assign('wrong_install_name', true);
		
		if (basename(_PS_ADMIN_DIR_) == 'admin' && file_exists(_PS_ADMIN_DIR_.'/../admin/'))
		{	
			$rand = 'admin'.sprintf('%04d', rand(0, 9999)).'/';
			if (@rename(_PS_ADMIN_DIR_.'/../admin/', _PS_ADMIN_DIR_.'/../'.$rand))
				Tools::redirectAdmin('../'.$rand);
			else
				$this->context->smarty->assign(array(
					'wrong_folder_name' => true
				));
		}
		else
			$rand = basename(_PS_ADMIN_DIR_).'/';

		$this->context->smarty->assign(array(
			'randomNb' => $rand,
			'adminUrl' => Tools::getCurrentUrlProtocolPrefix().Tools::getShopDomain().__PS_BASE_URI__.$rand
		));

		// Redirect to admin panel
		if (Tools::isSubmit('redirect') && Validate::isControllerName(Tools::getValue('redirect')))
			$this->context->smarty->assign('redirect', Tools::getValue('redirect'));
		else
		{
			$tab = new Tab((int)$this->context->employee->default_tab);
			$this->context->smarty->assign('redirect', $this->context->link->getAdminLink($tab->class_name));
		}

		if ($nb_errors = count($this->errors))
			$this->context->smarty->assign(array(
				'errors' => $this->errors,
				'nbErrors' => $nb_errors,
				'shop_name' => Tools::safeOutput(Configuration::get('PS_SHOP_NAME')),
				'disableDefaultErrorOutPut' => true,
			));

		$this->setMedia();
		$this->initHeader();
		parent::initContent();
		$this->initFooter();
	}
	
	public function checkToken()
	{
		return true;
	}

	/**
	 * All BO users can access the login page
	 *
	 * @return bool
	 */
	public function viewAccess()
	{
		return true;
	}
	
	public function postProcess()
	{
		if (Tools::isSubmit('submitLogin'))
			$this->processLogin();
		elseif (Tools::isSubmit('submitForgot'))
			$this->processForgot();
	}
	
	public function processLogin()
	{
		/* Check fields validity */
		$passwd = trim(Tools::getValue('passwd'));
		$email = trim(Tools::getValue('email'));
		if (empty($email))
			$this->errors[] = Tools::displayError('Email is empty.');
		elseif (!Validate::isEmail($email))
			$this->errors[] = Tools::displayError('Invalid email address.');

		if (empty($passwd))
			$this->errors[] = Tools::displayError('The password field is blank.');
		elseif (!Validate::isPasswd($passwd))
			$this->errors[] = Tools::displayError('Invalid password.');
			
		if (!count($this->errors))
		{
			// Find employee
			$this->context->employee = new Employee();
			$is_employee_loaded = $this->context->employee->getByEmail($email, $passwd);
			$employee_associated_shop = $this->context->employee->getAssociatedShops();
			if (!$is_employee_loaded)
			{
				$this->errors[] = Tools::displayError('The Employee does not exist, or the password provided is incorrect.');
				$this->context->employee->logout();
			}
			elseif (empty($employee_associated_shop) && !$this->context->employee->isSuperAdmin())
			{
				$this->errors[] = Tools::displayError('This employee does not manage the shop anymore (Either the shop has been deleted or permissions have been revoked).');
				$this->context->employee->logout();
			}
			else
			{
				$this->context->employee->remote_addr = ip2long(Tools::getRemoteAddr());
				// Update cookie
				$cookie = Context::getContext()->cookie;
				$cookie->id_employee = $this->context->employee->id;
				$cookie->email = $this->context->employee->email;
				$cookie->profile = $this->context->employee->id_profile;
				$cookie->passwd = $this->context->employee->passwd;
				$cookie->remote_addr = $this->context->employee->remote_addr;

				if (!Tools::getValue('stay_logged_in'))
					$cookie->last_activity = time();

				$cookie->write();

				// If there is a valid controller name submitted, redirect to it
				if (isset($_POST['redirect']) && Validate::isControllerName($_POST['redirect']))
					$url = $this->context->link->getAdminLink($_POST['redirect']);
				else
				{
					$tab = new Tab((int)$this->context->employee->default_tab);
					$url = $this->context->link->getAdminLink($tab->class_name);
				}

				if (Tools::isSubmit('ajax'))
					die(Tools::jsonEncode(array('hasErrors' => false, 'redirect' => $url)));
				else
					$this->redirect_after = $url;
			}
		}
		if (Tools::isSubmit('ajax'))
			die(Tools::jsonEncode(array('hasErrors' => true, 'errors' => $this->errors)));
	}
	
	public function processForgot()
	{
		if (_PS_MODE_DEMO_)
			$this->errors[] = Tools::displayError('This functionality has been disabled.');
		elseif (!($email = trim(Tools::getValue('email_forgot'))))
			$this->errors[] = Tools::displayError('Email is empty.');
		elseif (!Validate::isEmail($email))
			$this->errors[] = Tools::displayError('Invalid email address.');
		else
		{
			$employee = new Employee();
			if (!$employee->getByEmail($email) || !$employee)
				$this->errors[] = Tools::displayError('This account does not exist.');
			elseif ((strtotime($employee->last_passwd_gen.'+'.Configuration::get('PS_PASSWD_TIME_BACK').' minutes') - time()) > 0)
				$this->errors[] = sprintf(
					Tools::displayError('You can regenerate your password only every %d minute(s)'),
					Configuration::get('PS_PASSWD_TIME_BACK')
				);
		}

		if (!count($this->errors))
		{	
			$pwd = Tools::passwdGen();
			$employee->passwd = md5(pSQL(_COOKIE_KEY_.$pwd));
			$employee->last_passwd_gen = date('Y-m-d H:i:s', time());

			$params = array(
				'{email}' => $employee->email,
				'{lastname}' => $employee->lastname,
				'{firstname}' => $employee->firstname,
				'{passwd}' => $pwd
			);
						
			if (Mail::Send($employee->id_lang, 'password', Mail::l('Your new password', $employee->id_lang), $params, $employee->email, $employee->firstname.' '.$employee->lastname))
			{
				// Update employee only if the mail can be sent
				$result = $employee->update();
				if (!$result)
					$this->errors[] = Tools::displayError('An error occurred while attempting to change your password.');
				else
					die(Tools::jsonEncode(array(
						'hasErrors' => false,
						'confirm' => $this->l('Your password has been emailed to you.', 'AdminTab', false, false)
					)));
			}
			else
				die(Tools::jsonEncode(array(
					'hasErrors' => true,
					'errors' => array(Tools::displayError('An error occurred while attempting to change your password.'))
				)));
		
		}
		else if (Tools::isSubmit('ajax'))
			die(Tools::jsonEncode(array('hasErrors' => true, 'errors' => $this->errors)));
	}
}
