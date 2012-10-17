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
*  @version  Release: $Revision: 7471 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class PasswordControllerCore extends FrontController
{
	public $php_self = 'password';

	/**
	 * Start forms process
	 * @see FrontController::postProcess()
	 */
	public function postProcess()
	{
		if (Tools::isSubmit('email'))
		{
			if (!($email = Tools::getValue('email')) || !Validate::isEmail($email))
				$this->errors[] = Tools::displayError('Invalid e-mail address');
			else
			{
				$customer = new Customer();
				$customer->getByemail($email);
				if (!Validate::isLoadedObject($customer))
					$this->errors[] = Tools::displayError('There is no account registered to this e-mail address.');
				else
				{
					if ((strtotime($customer->last_passwd_gen.'+'.(int)($min_time = Configuration::get('PS_PASSWD_TIME_FRONT')).' minutes') - time()) > 0)
						$this->errors[] = sprintf(
							Tools::displayError('You can regenerate your password only every %d minute(s)'),
							(int)$min_time
						);
					else
					{
						if (Mail::Send($this->context->language->id, 'password_query', Mail::l('Password query confirmation'),
						array(
							'{email}' => $customer->email,
							'{lastname}' => $customer->lastname,
							'{firstname}' => $customer->firstname,
							'{url}' => $this->context->link->getPageLink('password', true, null, 'token='.$customer->secure_key.'&id_customer='.(int)$customer->id)),
						$customer->email,
						$customer->firstname.' '.$customer->lastname))
							$this->context->smarty->assign(array('confirmation' => 2, 'email' => $customer->email));
						else
							$this->errors[] = Tools::displayError('Error occurred while sending the e-mail.');
					}
				}
			}
		}
		else if (($token = Tools::getValue('token')) && ($id_customer = (int)(Tools::getValue('id_customer'))))
		{
			$email = Db::getInstance()->getValue('SELECT `email` FROM '._DB_PREFIX_.'customer c WHERE c.`secure_key` = \''.pSQL($token).'\' AND c.id_customer='.(int)$id_customer);
			if ($email)
			{
				$customer = new Customer();
				$customer->getByemail($email);
				if ((strtotime($customer->last_passwd_gen.'+'.(int)($min_time = Configuration::get('PS_PASSWD_TIME_FRONT')).' minutes') - time()) > 0)
					Tools::redirect('index.php?controller=authentication&error_regen_pwd');
				else
				{
					$customer->passwd = Tools::encrypt($password = Tools::passwdGen(MIN_PASSWD_LENGTH));
					$customer->last_passwd_gen = date('Y-m-d H:i:s', time());
					if ($customer->update())
					{
						Hook::exec('actionPasswordRenew', array('customer' => $customer, 'password' => $password));
						if (Mail::Send($this->context->language->id, 'password', Mail::l('Your new password'),
						array(
							'{email}' => $customer->email,
							'{lastname}' => $customer->lastname,
							'{firstname}' => $customer->firstname,
							'{passwd}' => $password),
						$customer->email,
						$customer->firstname.' '.$customer->lastname))
							$this->context->smarty->assign(array('confirmation' => 1, 'email' => $customer->email));
						else
							$this->errors[] = Tools::displayError('Error occurred while sending the e-mail.');
					}
					else
						$this->errors[] = Tools::displayError('An error occurred with your account and your new password cannot be sent to your e-mail. Please report your problem using the contact form.');
				}
			}
			else
				$this->errors[] = Tools::displayError('We cannot regenerate your password with the data you submitted');
		}
		else if (($token = Tools::getValue('token')) || ($id_customer = Tools::getValue('id_customer')))
			$this->errors[] = Tools::displayError('We cannot regenerate your password with the data you submitted');
	}

	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		$this->setTemplate(_PS_THEME_DIR_.'password.tpl');
	}
}

