<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminLoginControllerCore extends AdminController
{
	public function __construct()
	{
	 	$this->errors = array();
	 	$this->context = Context::getContext();
	 	$this->display_header = false;
	 	$this->display_footer = false;

		$this->meta_title = $this->l('Administration panel');

		parent::__construct();
	}
	
	
	public function setMedia()
	{
		$this->addJquery();
		$this->addCSS(_PS_CSS_DIR_.'login.css');
		$this->addJS(_PS_JS_DIR_.'login.js');
		$this->addJqueryUI('ui.widget');
		$this->addJqueryUI('effects.shake');
		$this->addJqueryUI('effects.slide');
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
				$this->errors[] = Tools::displayError('SSL is activated. However, your IP is allowed to use unsecure mode (Maintenance or local IP).');
			else
			{
				$warningSslMessage = Tools::displayError('SSL is activated. Please connect using the following url to log in in secure mode (https).');
				$warningSslMessage .= '<a href="https://'.Tools::safeOutput(Tools::getServerName()).Tools::safeOutput($_SERVER['REQUEST_URI']).'">https://'.Tools::safeOutput(Tools::getServerName()).Tools::safeOutput($_SERVER['REQUEST_URI']).'</a>';
				$this->context->smarty->assign(array('warningSslMessage' => $warningSslMessage));
			}
		}

		if (file_exists(_PS_ADMIN_DIR_.'/../install') || file_exists(_PS_ADMIN_DIR_.'/../admin'))
			$this->context->smarty->assign(array(
				'randomNb' => rand(100, 999),
				'wrong_folder_name' => true
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
			$this->errors[] = Tools::displayError('E-mail is empty');
		elseif (!Validate::isEmail($email))
			$this->errors[] = Tools::displayError('Invalid e-mail address');

		if (empty($passwd))
			$this->errors[] = Tools::displayError('Password is blank');
		elseif (!Validate::isPasswd($passwd))
			$this->errors[] = Tools::displayError('Invalid password');
			
		if (!count($this->errors))
		{
			// Find employee
			$this->context->employee = new Employee();
			$is_employee_loaded = $this->context->employee->getByemail($email, $passwd);
			$employee_associated_shop = $this->context->employee->getAssociatedShops();
			if (!$is_employee_loaded)
			{
				$this->errors[] = Tools::displayError('Employee does not exist or password is incorrect.');
				$this->context->employee->logout();
			}
			elseif (empty($employee_associated_shop) && !$this->context->employee->isSuperAdmin())
			{
				$this->errors[] = Tools::displayError('Employee does not manage any shop anymore (shop has been deleted or permissions have been removed).');
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
			$this->errors[] = Tools::displayError('E-mail is empty');
		elseif (!Validate::isEmail($email))
			$this->errors[] = Tools::displayError('Invalid e-mail address');
		else
		{
			$employee = new Employee();
			if (!$employee->getByemail($email) || !$employee)
				$this->errors[] = Tools::displayError('This account does not exist');
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
						
			if (Mail::Send((int)Configuration::get('PS_LANG_DEFAULT'), 'password', Mail::l('Your new password', (int)Configuration::get('PS_LANG_DEFAULT')), $params, $employee->email, $employee->firstname.' '.$employee->lastname))
			{
				// Update employee only if the mail can be sent
				$result = $employee->update();
				if (!$result)
					$this->errors[] = Tools::displayError('An error occurred during your password change.');
				else
					die(Tools::jsonEncode(array(
						'hasErrors' => false,
						'confirm' => $this->l('Your password has been e-mailed to you', 'AdminTab', false, false)
					)));
			}
			else
				die(Tools::jsonEncode(array(
					'hasErrors' => true,
					'errors' => array(Tools::displayError('An error occurred during your password change.'))
				)));
		
		}
		else if (Tools::isSubmit('ajax'))
			die(Tools::jsonEncode(array('hasErrors' => true, 'errors' => $this->errors)));
	}
}
