<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class Blocknewsletter extends Module
{
	const GUEST_NOT_REGISTERED = -1;
	const CUSTOMER_NOT_REGISTERED = 0;
	const GUEST_REGISTERED = 1;
	const CUSTOMER_REGISTERED = 2;

	public function __construct()
	{
		$this->name = 'blocknewsletter';
		$this->tab = 'front_office_features';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();	

		$this->displayName = $this->l('Newsletter block');
		$this->description = $this->l('Adds a block for newsletter subscription.');
		$this->confirmUninstall = $this->l('Are you sure that you want to delete all of your contacts?');

		$this->version = '1.4';
		$this->author = 'PrestaShop';
		$this->error = false;
		$this->valid = false;
		$this->_files = array(
			'name' => array('newsletter_conf', 'newsletter_voucher'),
			'ext' => array(
				0 => 'html',
				1 => 'txt'
			)
		);
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook(array('header', 'footer', 'actionCustomerAccountAdd')))
			return false;

		Configuration::updateValue('NW_SALT', Tools::passwdGen(16));

		return Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter` (
			`id` int(6) NOT NULL AUTO_INCREMENT,
			`id_shop` INTEGER UNSIGNED NOT NULL DEFAULT \'1\',
			`id_shop_group` INTEGER UNSIGNED NOT NULL DEFAULT \'1\',
			`email` varchar(255) NOT NULL,
			`newsletter_date_add` DATETIME NULL,
			`ip_registration_newsletter` varchar(15) NOT NULL,
			`http_referer` VARCHAR(255) NULL,
			`active` TINYINT(1) NOT NULL DEFAULT \'0\',
			PRIMARY KEY(`id`)
		) ENGINE='._MYSQL_ENGINE_.' default CHARSET=utf8');
	}

	public function uninstall()
	{
		Db::getInstance()->execute('DROP TABLE '._DB_PREFIX_.'newsletter');
		return parent::uninstall();
	}

	public function getContent()
	{
		$this->_html = '';

		if (Tools::isSubmit('submitUpdate'))
		{
			$conf_email = Tools::getValue('NW_CONFIRMATION_EMAIL');
			if ($conf_email && Validate::isBool((int)$conf_email))
				Configuration::updateValue('NW_CONFIRMATION_EMAIL', (int)$conf_email);

			$verif_email = Tools::getValue('NW_VERIFICATION_EMAIL');
			if ($verif_email && Validate::isBool((int)$verif_email))
				Configuration::updateValue('NW_VERIFICATION_EMAIL', (int)$verif_email);
			
			$voucher = Tools::getValue('NW_VOUCHER_CODE');
			if ($voucher && !Validate::isDiscountName($voucher))
				$this->_html .= $this->displayError($this->l('The coucher code is invalid.'));
			else
			{
				Configuration::updateValue('NW_VOUCHER_CODE', pSQL($voucher));
				$this->_html .= $this->displayConfirmation($this->l('Settings updated'));
			}
		}
		return $this->_html.$this->renderForm();
	}

	/**
	 * Check if this mail is registered for newsletters
	 *
	 * @param unknown_type $customerEmail
	 * @return int -1 = not a customer and not registered
	 * 				0 = customer not registered
	 * 				1 = registered in block
	 * 				2 = registered in customer
	 */
	private function isNewsletterRegistered($customerEmail)
	{
		$sql = 'SELECT `email`
				FROM '._DB_PREFIX_.'newsletter
				WHERE `email` = \''.pSQL($customerEmail).'\'
				AND id_shop = '.$this->context->shop->id;

		if (Db::getInstance()->getRow($sql))
			return self::GUEST_REGISTERED;

		$sql = 'SELECT `newsletter`
				FROM '._DB_PREFIX_.'customer
				WHERE `email` = \''.pSQL($customerEmail).'\'
				AND id_shop = '.$this->context->shop->id;

		if (!$registered = Db::getInstance()->getRow($sql))
			return self::GUEST_NOT_REGISTERED;

		if ($registered['newsletter'] == '1')
			return self::CUSTOMER_REGISTERED;

		return self::CUSTOMER_NOT_REGISTERED;
	}

	/**
	 * Register in block newsletter
	 */
	private function newsletterRegistration()
	{
		if (empty($_POST['email']) || !Validate::isEmail($_POST['email']))
			return $this->error = $this->l('Invalid email address');

		/* Unsubscription */
		else if ($_POST['action'] == '1')
		{
			$register_status = $this->isNewsletterRegistered($_POST['email']);

			if ($register_status < 1)
				return $this->error = $this->l('This email address is not registered.');

			if (!$this->unregister($_POST['email'], $register_status))
				return $this->error = $this->l('An error occurred while attempting to unsubscribe.');

			return $this->valid = $this->l('Unsubscription successful');
		}
		/* Subscription */
		else if ($_POST['action'] == '0')
		{
			$register_status = $this->isNewsletterRegistered($_POST['email']);
			if ($register_status > 0)
				return $this->error = $this->l('This email address is already registered.');

			$email = pSQL($_POST['email']);
			if (!$this->isRegistered($register_status))
			{
				if (Configuration::get('NW_VERIFICATION_EMAIL'))
				{
					// create an unactive entry in the newsletter database
					if ($register_status == self::GUEST_NOT_REGISTERED)
						$this->registerGuest($email, false);

					if (!$token = $this->getToken($email, $register_status))
						return $this->error = $this->l('An error occurred during the subscription process.');

					$this->sendVerificationEmail($email, $token);

					return $this->valid = $this->l('A verification email has been sent. Please check your inbox.');
				}
				else
				{
					if ($this->register($email, $register_status))
						$this->valid = $this->l('You have successfully subscribed to this newsletter.');
					else
						return $this->error = $this->l('An error occurred during the subscription process.');

					if ($code = Configuration::get('NW_VOUCHER_CODE'))
						$this->sendVoucher($email, $code);

					if (Configuration::get('NW_CONFIRMATION_EMAIL'))
						$this->sendConfirmationEmail($email);
				}
			}
		}
	}

	/**
	 * Return true if the registered status correspond to a registered user
	 * @param int $register_status
	 * @return bool
	 */
	protected function isRegistered($register_status)
	{
		return in_array(
					$register_status,
					array(self::GUEST_REGISTERED, self::CUSTOMER_REGISTERED)
				);
	}


	/**
	 * Subscribe an email to the newsletter. It will create an entry in the newsletter table
	 * or update the customer table depending of the register status
	 *
	 * @param unknown_type $email
	 * @param unknown_type $register_status
	 */
	protected function register($email, $register_status)
	{
		if ($register_status == self::GUEST_NOT_REGISTERED) {
			return $this->registerGuest($email);
		}

		if ($register_status == self::CUSTOMER_NOT_REGISTERED) {
	 		return $this->registerUser($email);
		}

		return false;
	}

	protected function unregister($email, $register_status) {
		if ($register_status == self::GUEST_REGISTERED) {
			$sql = 'DELETE FROM '._DB_PREFIX_.'newsletter WHERE `email` = \''.pSQL($_POST['email']).'\' AND id_shop = '.$this->context->shop->id;
		} else if ($register_status == self::CUSTOMER_REGISTERED) {
			$sql = 'UPDATE '._DB_PREFIX_.'customer SET `newsletter` = 0 WHERE `email` = \''.pSQL($_POST['email']).'\' AND id_shop = '.$this->context->shop->id;
		}

		if (!isset($sql) || !Db::getInstance()->execute($sql)) {
			return false;
		}

		return true;
	}

	/**
	 * Subscribe a customer to the newsletter
	 *
	 * @param string $email
	 * @return bool
	 */
	protected function registerUser($email)
	{
		$sql = 'UPDATE '._DB_PREFIX_.'customer
				SET `newsletter` = 1, newsletter_date_add = NOW(), `ip_registration_newsletter` = \''.pSQL(Tools::getRemoteAddr()).'\'
				WHERE `email` = \''.pSQL($email).'\'
				AND id_shop = '.$this->context->shop->id;

	 	return Db::getInstance()->execute($sql);
	}

	/**
	 * Subscribe a guest to the newsletter
	 *
	 * @param string $email
	 * @param bool $active
	 * @return bool
	 */
	protected function registerGuest($email, $active = true)
	{
		$sql = 'INSERT INTO '._DB_PREFIX_.'newsletter (id_shop, id_shop_group, email, newsletter_date_add, ip_registration_newsletter, http_referer, active)
				VALUES
				('.$this->context->shop->id.',
				'.$this->context->shop->id_shop_group.',
				\''.pSQL($email).'\',
				NOW(),
				\''.pSQL(Tools::getRemoteAddr()).'\',
				(
					SELECT c.http_referer
					FROM '._DB_PREFIX_.'connections c
					WHERE c.id_guest = '.(int)$this->context->customer->id.'
					ORDER BY c.date_add DESC LIMIT 1
				),
				'.(int)$active.'
				)';

		return Db::getInstance()->execute($sql);
	}


	public function activateGuest($email)
	{
		return Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'newsletter`
						SET `active` = 1
						WHERE `email` = \''.pSQL($email).'\''
				);
	}

	/**
	 * Returns a guest email by token
	 * @param string $token
	 * @return string email
	 */
	protected function getGuestEmailByToken($token)
	{
		$sql = 'SELECT `email`
				FROM `'._DB_PREFIX_.'newsletter`
				WHERE MD5(CONCAT( `email` , `newsletter_date_add`, \''.pSQL(Configuration::get('NW_SALT')).'\')) = \''.pSQL($token).'\'
				AND `active` = 0';

		return Db::getInstance()->getValue($sql);
	}

	/**
	 * Returns a customer email by token
	 * @param string $token
	 * @return string email
	 */
	protected function getUserEmailByToken($token)
	{
		$sql = 'SELECT `email`
				FROM `'._DB_PREFIX_.'customer`
				WHERE MD5(CONCAT( `email` , `date_add`, \''.pSQL(Configuration::get('NW_SALT')).'\')) = \''.pSQL($token).'\'
				AND `newsletter` = 0';

		return Db::getInstance()->getValue($sql);
	}

	/**
	 * Return a token associated to an user
	 *
	 * @param string $email
	 * @param string $register_status
	 */
	protected function getToken($email, $register_status)
	{
		if (in_array($register_status, array(self::GUEST_NOT_REGISTERED, self::GUEST_REGISTERED)))
		{
			$sql = 'SELECT MD5(CONCAT( `email` , `newsletter_date_add`, \''.pSQL(Configuration::get('NW_SALT')).'\')) as token
					FROM `'._DB_PREFIX_.'newsletter`
					WHERE `active` = 0
					AND `email` = \''.pSQL($email).'\'';
		}
		else if ($register_status == self::CUSTOMER_NOT_REGISTERED)
		{
			$sql = 'SELECT MD5(CONCAT( `email` , `date_add`, \''.pSQL(Configuration::get('NW_SALT')).'\' )) as token
					FROM `'._DB_PREFIX_.'customer`
					WHERE `newsletter` = 0
					AND `email` = \''.pSQL($email).'\'';
		}

		return Db::getInstance()->getValue($sql);
	}

	/**
	 * Ends the registration process to the newsletter
	 *
	 * @param string $token
	 */
	public function confirmEmail($token)
	{
		$activated = false;

		if ($email = $this->getGuestEmailByToken($token))
			$activated = $this->activateGuest($email);
		else if ($email = $this->getUserEmailByToken($token))
			$activated = $this->registerUser($email);

		if (!$activated)
			return $this->l('This email is already registered and/or invalid.');

		if ($discount = Configuration::get('NW_VOUCHER_CODE'))
			$this->sendVoucher($email, $discount);

		if (Configuration::get('NW_CONFIRMATION_EMAIL'))
			$this->sendConfirmationEmail($email);

		return $this->l('Thank you for subscribing to our newsletter.');
	}

	/**
	 * Send the confirmation mails to the given $email address if needed.
	 *
	 * @param string $email Email where to send the confirmation
	 *
	 * @note the email has been verified and might not yet been registered. Called by AuthController::processCustomerNewsletter
	 *
	 */
	public function confirmSubscription($email)
	{
		if ($email)
		{
			if ($discount = Configuration::get('NW_VOUCHER_CODE'))
				$this->sendVoucher($email, $discount);

			if (Configuration::get('NW_CONFIRMATION_EMAIL'))
				$this->sendConfirmationEmail($email);
		}
	}

	/**
	 * Send an email containing a voucher code
	 * @param string $email
	 * @param string $discount
	 * @return bool
	 */
	protected function sendVoucher($email, $code)
	{
		return Mail::Send($this->context->language->id, 'newsletter_voucher', Mail::l('Newsletter voucher', $this->context->language->id), array('{discount}' => $code), $email, null, null, null, null, null, dirname(__FILE__).'/mails/', false, $this->context->shop->id);
	}

	/**
	 * Send a confirmation email
	 * @param string $email
	 * @return bool
	 */
	protected function sendConfirmationEmail($email)
	{
		return	Mail::Send($this->context->language->id, 'newsletter_conf', Mail::l('Newsletter confirmation', $this->context->language->id), array(), pSQL($email), null, null, null, null, null, dirname(__FILE__).'/mails/', false, $this->context->shop->id);
	}

	/**
	 * Send a verification email
	 * @param string $email
	 * @param string $token
	 * @return bool
	 */
	protected function sendVerificationEmail($email, $token)
	{
		$verif_url = Context::getContext()->link->getModuleLink('blocknewsletter', 'verification', array(
			'token' => $token,
		));
		return Mail::Send($this->context->language->id, 'newsletter_verif', Mail::l('Email verification', $this->context->language->id), array('{verif_url}' => $verif_url), $email, null, null, null, null, null, dirname(__FILE__).'/mails/', false, $this->context->shop->id);
	}

	public function hookDisplayRightColumn($params)
	{
		return $this->hookDisplayLeftColumn($params);
	}

	private function _prepareHook($params)
	{
		if (Tools::isSubmit('submitNewsletter'))
		{
			$this->newsletterRegistration();
			if ($this->error)
			{
				$this->smarty->assign(array('color' => 'red',
						'msg' => $this->error,
						'nw_value' => isset($_POST['email']) ? pSQL($_POST['email']) : false,
						'nw_error' => true,
						'action' => $_POST['action'])
				);
			}
			else if ($this->valid)
			{
				$this->smarty->assign(array('color' => 'green',
						'msg' => $this->valid,
						'nw_error' => false)
				);
			}
		}
		$this->smarty->assign('this_path', $this->_path);
	}

	public function hookDisplayLeftColumn($params)
	{
		$this->_prepareHook($params);
		return $this->display(__FILE__, 'blocknewsletter.tpl');
	}
	
	public function hookFooter($params)
	{
		return $this->hookDisplayLeftColumn($params);
	}

	public function hookDisplayHeader($params)
	{
		$this->context->controller->addCSS($this->_path.'blocknewsletter.css', 'all');
	}

	/**
	* Deletes duplicates email in newsletter table
	* @param $params
	* @return bool
	*/
	public function hookActionCustomerAccountAdd($params)
	{
		//if e-mail of the created user address has already been added to the newsletter through the blocknewsletter module,
		//we delete it from blocknewsletter table to prevent duplicates
		$id_shop = $params['newCustomer']->id_shop;
		$email = $params['newCustomer']->email;
		$newsletter = $params['newCustomer']->newsletter;
		if ($newsletter && Validate::isEmail($email))
			return (bool)Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'newsletter WHERE id_shop='.(int)$id_shop.' AND email=\''.pSQL($email)."'");
		return true;
	}
	
	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'switch',
						'label' => $this->l('Would you like to send a verification email after subscription?'),
						'name' => 'NW_VERIFICATION_EMAIL',
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
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Would you like to send a confirmation email after subscription?'),
						'name' => 'NW_CONFIRMATION_EMAIL',
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
					),
					array(
						'type' => 'text',
						'label' => $this->l('Welcome voucher code'),
						'name' => 'NW_VOUCHER_CODE',
						'class' => 'fixed-width-md',
						'desc' => $this->l('Leave blank to disable by default.')
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);
		
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitUpdate';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}
	
	public function getConfigFieldsValues()
	{		
		return array(
			'NW_VERIFICATION_EMAIL' => Tools::getValue('NW_VERIFICATION_EMAIL', Configuration::get('NW_VERIFICATION_EMAIL')),
			'NW_CONFIRMATION_EMAIL' => Tools::getValue('NW_CONFIRMATION_EMAIL', Configuration::get('NW_CONFIRMATION_EMAIL')),
			'NW_VOUCHER_CODE' => Tools::getValue('NW_VOUCHER_CODE', Configuration::get('NW_VOUCHER_CODE')),
		);
	}
}
