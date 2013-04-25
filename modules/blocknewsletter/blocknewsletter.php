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
		if (parent::install() == false || $this->registerHook('leftColumn') == false || $this->registerHook('header') == false)
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
		if (!parent::uninstall())
			return false;
		return Db::getInstance()->execute('DROP TABLE '._DB_PREFIX_.'newsletter');
	}

	public function getContent()
	{
		$this->_html = '<h2>'.$this->displayName.'</h2>';

		if (Tools::isSubmit('submitUpdate'))
		{
			if (isset($_POST['new_page']) && Validate::isBool((int)$_POST['new_page']))
				Configuration::updateValue('NW_CONFIRMATION_NEW_PAGE', $_POST['new_page']);

			if (isset($_POST['conf_email']) && Validate::isBool((int)$_POST['conf_email']))
				Configuration::updateValue('NW_CONFIRMATION_EMAIL', pSQL($_POST['conf_email']));

			if (isset($_POST['verif_email']) && Validate::isBool((int)$_POST['verif_email']))
				Configuration::updateValue('NW_VERIFICATION_EMAIL', (int)$_POST['verif_email']);

			if (!empty($_POST['voucher']) && !Validate::isDiscountName($_POST['voucher']))
				$this->_html .= '<div class="alert">'.$this->l('The coucher code is invalid.').'</div>';
			else
			{
				Configuration::updateValue('NW_VOUCHER_CODE', pSQL($_POST['voucher']));
				$this->_html .= '<div class="conf ok">'.$this->l('Updated').'</div>';
			}
		}
		return $this->_displayForm();
	}

	private function _displayForm()
	{
		$this->_html .= '
		<form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'">
			<fieldset>
				<legend><img src="'.$this->_path.'logo.gif" />'.$this->l('Settings').'</legend>
				<label>'.$this->l('Woulld you like to display configuration in a new page?').'</label>
				<div class="margin-form">
					<input type="radio" name="new_page" value="1" '.(Configuration::get('NW_CONFIRMATION_NEW_PAGE') ? 'checked="checked" ' : '').'/>'.$this->l('Yes').'
					<input type="radio" name="new_page" value="0" '.(!Configuration::get('NW_CONFIRMATION_NEW_PAGE') ? 'checked="checked" ' : '').'/>'.$this->l('No').'
				</div>
				<div class="clear"></div>
				<label>'.$this->l('Would you like to send a verification email after subscription?').'</label>
				<div class="margin-form">
					<input type="radio" name="verif_email" value="1" '.(Configuration::get('NW_VERIFICATION_EMAIL') ? 'checked="checked" ' : '').'/>'.$this->l('Yes').'
					<input type="radio" name="verif_email" value="0" '.(!Configuration::get('NW_VERIFICATION_EMAIL') ? 'checked="checked" ' : '').'/>'.$this->l('No').'
				</div>
				<div class="clear"></div>
				<label>'.$this->l('Would you like to send a confirmation email after subscription?').'</label>
				<div class="margin-form">
					<input type="radio" name="conf_email" value="1" '.(Configuration::get('NW_CONFIRMATION_EMAIL') ? 'checked="checked" ' : '').'/>'.$this->l('Yes').'
					<input type="radio" name="conf_email" value="0" '.(!Configuration::get('NW_CONFIRMATION_EMAIL') ? 'checked="checked" ' : '').'/>'.$this->l('No').'
				</div>
				<div class="clear"></div>
				<label>'.$this->l('Welcome voucher code').'</label>
				<div class="margin-form">
					<input type="text" name="voucher" value="'.Configuration::get('NW_VOUCHER_CODE').'" />
					<p>'.$this->l('Leave blank to disable by default.').'</p>
				</div>
				<div class="margin-form clear pspace"><input type="submit" name="submitUpdate" value="'.$this->l('Update').'" class="button" /></div>
			</fieldset>
		</form>';

		return $this->_html;
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
			else if ($register_status == self::GUEST_REGISTERED)
			{
				if (!Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'newsletter WHERE `email` = \''.pSQL($_POST['email']).'\' AND id_shop = '.$this->context->shop->id))
					return $this->error = $this->l('An error occurred while attempting to unsubscribe.');
				return $this->valid = $this->l('Unsubscription successful');
			}
			else if ($register_status == self::CUSTOMER_REGISTERED)
			{
				if (!Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'customer SET `newsletter` = 0 WHERE `email` = \''.pSQL($_POST['email']).'\' AND id_shop = '.$this->context->shop->id))
					return $this->error = $this->l('An error occurred while attempting to unsubscribe.');
				return $this->valid = $this->l('Unsubscription successful');
			}
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
		if ($register_status == self::GUEST_NOT_REGISTERED)
		{
			if (!$this->registerGuest(Tools::getValue('email')))
				return false;
		}
		else if ($register_status == self::CUSTOMER_NOT_REGISTERED)
		{
		 	if (!$this->registerUser(Tools::getValue('email')))
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
		return Mail::Send($this->context->language->id, 'newsletter_voucher', Mail::l('Newsletter voucher', $this->context->language->id), array('{discount}' => $code), $email, null, null, null, null, null, dirname(__FILE__).'/mails/');
	}

	/**
	 * Send a confirmation email
	 * @param string $email
	 * @return bool
	 */
	protected function sendConfirmationEmail($email)
	{
		return	Mail::Send($this->context->language->id, 'newsletter_conf', Mail::l('Newsletter confirmation', $this->context->language->id), array(), pSQL($email), null, null, null, null, null, dirname(__FILE__).'/mails/');
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
		return Mail::Send($this->context->language->id, 'newsletter_verif', Mail::l('Email verification', $this->context->language->id), array('{verif_url}' => $verif_url), $email, null, null, null, null, null, dirname(__FILE__).'/mails/');
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
}
