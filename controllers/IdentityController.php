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

class IdentityControllerCore extends FrontController
{
	public function __construct()
	{
		$this->auth = true;
		$this->php_self = 'identity.php';
		$this->authRedirection = 'identity.php';
		$this->ssl = true;
	
		parent::__construct();
	}
	
	public function preProcess()
	{
		parent::preProcess();
		
		$customer = new Customer((int)(self::$cookie->id_customer));

		if (sizeof($_POST))
		{
			$exclusion = array('secure_key', 'old_passwd', 'passwd', 'active', 'date_add', 'date_upd', 'last_passwd_gen', 'newsletter_date_add', 'id_default_group');
			$fields = $customer->getFields();
			foreach ($fields AS $key => $value)
				if (!in_array($key, $exclusion))
					$customer->{$key} = key_exists($key, $_POST) ? trim($_POST[$key]) : 0;
		}

		if (isset($_POST['years']) AND isset($_POST['months']) AND isset($_POST['days']))
			$customer->birthday = (int)($_POST['years']).'-'.(int)($_POST['months']).'-'.(int)($_POST['days']);

		if (Tools::isSubmit('submitIdentity'))
		{
			if (!@checkdate(Tools::getValue('months'), Tools::getValue('days'), Tools::getValue('years')) AND
			!(Tools::getValue('months') == '' AND Tools::getValue('days') == '' AND Tools::getValue('years') == ''))
				$this->errors[] = Tools::displayError('Invalid date of birth');
			else
			{
				$customer->birthday = (empty($_POST['years']) ? '' : (int)($_POST['years']).'-'.(int)($_POST['months']).'-'.(int)($_POST['days']));

				$_POST['old_passwd'] = trim($_POST['old_passwd']);
				if (empty($_POST['old_passwd']) OR (Tools::encrypt($_POST['old_passwd']) != self::$cookie->passwd))
					$this->errors[] = Tools::displayError('Your password is incorrect.');
				elseif ($_POST['passwd'] != $_POST['confirmation'])
					$this->errors[] = Tools::displayError('Password and confirmation do not match');
				else
				{
					$prev_id_default_group = $customer->id_default_group;
					$this->errors = $customer->validateControler();
				}
				if (!sizeof($this->errors))
				{
					$customer->id_default_group = (int)($prev_id_default_group);
					$customer->firstname = Tools::ucfirst(Tools::strtolower($customer->firstname));
					if (Tools::getValue('passwd'))
						self::$cookie->passwd = $customer->passwd;
					if ($customer->update())
					{
						self::$cookie->customer_lastname = $customer->lastname;
						self::$cookie->customer_firstname = $customer->firstname;
						self::$smarty->assign('confirmation', 1);
					}
					else
						$this->errors[] = Tools::displayError('Cannot update information');
				}
			}
		}
		else
			$_POST = array_map('stripslashes', $customer->getFields());

		if ($customer->birthday)
			$birthday = explode('-', $customer->birthday);
		else
			$birthday = array('-', '-', '-');

		/* Generate years, months and days */
		self::$smarty->assign(array(
			'years' => Tools::dateYears(),
			'sl_year' => $birthday[0],
			'months' => Tools::dateMonths(),
			'sl_month' => $birthday[1],
			'days' => Tools::dateDays(),
			'sl_day' => $birthday[2],
			'errors' => $this->errors
		));
		
		self::$smarty->assign('newsletter', (int)Module::getInstanceByName('blocknewsletter')->active);
	}
	
	public function setMedia()
	{
		parent::setMedia();
		Tools::addCSS(_THEME_CSS_DIR_.'identity.css');
	}
	
	public function displayContent()
	{
		parent::displayContent();
		self::$smarty->display(_PS_THEME_DIR_.'identity.tpl');
	}
}


