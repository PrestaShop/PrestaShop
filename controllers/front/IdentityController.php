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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class IdentityControllerCore extends FrontController
{
	public $auth = true;
	public $php_self = 'identity';
	public $authRedirection = 'identity';
	public $ssl = true;

	public function init()
	{
		parent::init();
		$this->customer = $this->context->customer;
	}

	/**
	 * Start forms process
	 * @see FrontController::postProcess()
	 */
	public function postProcess()
	{
		$origin_newsletter = (bool)$this->customer->newsletter;

		if (isset($_POST['years']) && isset($_POST['months']) && isset($_POST['days']))
			$this->customer->birthday = (int)($_POST['years']).'-'.(int)($_POST['months']).'-'.(int)($_POST['days']);

		if (Tools::isSubmit('submitIdentity'))
		{
			if (!@checkdate(Tools::getValue('months'), Tools::getValue('days'), Tools::getValue('years')) &&
				!(Tools::getValue('months') == '' && Tools::getValue('days') == '' && Tools::getValue('years') == ''))
				$this->errors[] = Tools::displayError('Invalid date of birth');
			else
			{
				$this->customer->birthday = (empty($_POST['years']) ? '' : (int)$_POST['years'].'-'.(int)$_POST['months'].'-'.(int)$_POST['days']);

				if (Customer::customerExists(Tools::getValue('email'), true) && $this->customer->email != Tools::getValue('email'))
					$this->errors[] = Tools::displayError('An account is already registered with this e-mail.');

				$_POST['old_passwd'] = trim($_POST['old_passwd']);
				if (empty($_POST['old_passwd']) || (Tools::encrypt($_POST['old_passwd']) != $this->context->cookie->passwd))
					$this->errors[] = Tools::displayError('Your password is incorrect.');
				else if ($_POST['passwd'] != $_POST['confirmation'])
					$this->errors[] = Tools::displayError('Password and confirmation do not match');
				else
				{
					$prev_id_default_group = $this->customer->id_default_group;

					// Merge all errors of this file and of the Object Model
					$this->errors = array_merge($this->errors, $this->customer->validateController());
				}

				if (!count($this->errors))
				{
					$this->customer->id_default_group = (int)$prev_id_default_group;
					$this->customer->firstname = Tools::ucfirst(Tools::strtolower($this->customer->firstname));

					if (!isset($_POST['newsletter']))
						$this->customer->newsletter = 0;
					elseif (!$origin_newsletter && isset($_POST['newsletter']))
						if ($module_newsletter = Module::getInstanceByName('blocknewsletter'))
							if ($module_newsletter->active)
								$module_newsletter->confirmSubscription($this->customer->email);

					if (!isset($_POST['optin']))
						$this->customer->optin = 0;
					if (Tools::getValue('passwd'))
						$this->context->cookie->passwd = $this->customer->passwd;
					if ($this->customer->update())
					{
						$this->context->cookie->customer_lastname = $this->customer->lastname;
						$this->context->cookie->customer_firstname = $this->customer->firstname;
						$this->context->smarty->assign('confirmation', 1);
					}
					else
						$this->errors[] = Tools::displayError('Cannot update information');
				}
			}
		}
		else
			$_POST = array_map('stripslashes', $this->customer->getFields());

		return $this->customer;
	}
	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		if ($this->customer->birthday)
			$birthday = explode('-', $this->customer->birthday);
		else
			$birthday = array('-', '-', '-');

		/* Generate years, months and days */
		$this->context->smarty->assign(array(
				'years' => Tools::dateYears(),
				'sl_year' => $birthday[0],
				'months' => Tools::dateMonths(),
				'sl_month' => $birthday[1],
				'days' => Tools::dateDays(),
				'sl_day' => $birthday[2],
				'errors' => $this->errors,
				'genders' => Gender::getGenders(),
			));

		$this->context->smarty->assign('newsletter', (int)Module::getInstanceByName('blocknewsletter')->active);

		$this->setTemplate(_PS_THEME_DIR_.'identity.tpl');
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addCSS(_THEME_CSS_DIR_.'identity.css');
	}

}
