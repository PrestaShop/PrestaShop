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

class ReferralProgram extends Module
{
	public function __construct()
	{
		$this->name = 'referralprogram';
		$this->tab = 'advertising_marketing';
		$this->version = '1.5.1';
		$this->author = 'PrestaShop';

		parent::__construct();

		$this->confirmUninstall = $this->l('All sponsors and friends will be deleted. Are you sure you want to uninstall this module?');
		$this->displayName = $this->l('Customer referral program');
		$this->description = $this->l('Integrate a referral program system into your shop.');
		if (Configuration::get('REFERRAL_DISCOUNT_TYPE') == 1 AND !Configuration::get('REFERRAL_PERCENTAGE'))
			$this->warning = $this->l('Please specify an amount for referral program vouchers.');

		if ($this->id)
		{
			$this->_configuration = Configuration::getMultiple(array('REFERRAL_NB_FRIENDS', 'REFERRAL_ORDER_QUANTITY', 'REFERRAL_DISCOUNT_TYPE', 'REFERRAL_DISCOUNT_VALUE'));
			$this->_configuration['REFERRAL_DISCOUNT_DESCRIPTION'] = Configuration::getInt('REFERRAL_DISCOUNT_DESCRIPTION');
			$this->_xmlFile = dirname(__FILE__).'/referralprogram.xml';
		}
	}

	public function install()
	{
		$defaultTranslations = array('en' => 'Referral reward', 'fr' => 'Récompense parrainage');
		$desc = array((int)Configuration::get('PS_LANG_DEFAULT') => $this->l('Referral reward'));
		foreach (Language::getLanguages() AS $language)
			if (isset($defaultTranslations[$language['iso_code']]))
				$desc[(int)$language['id_lang']] = $defaultTranslations[$language['iso_code']];

		if (!parent::install() OR !$this->installDB() OR !Configuration::updateValue('REFERRAL_DISCOUNT_DESCRIPTION', $desc)
			OR !Configuration::updateValue('REFERRAL_ORDER_QUANTITY', 1) OR !Configuration::updateValue('REFERRAL_DISCOUNT_TYPE', 2)
			OR !Configuration::updateValue('REFERRAL_NB_FRIENDS', 5) OR !$this->registerHook('shoppingCart')
			OR !$this->registerHook('orderConfirmation') OR !$this->registerHook('updateOrderStatus')
			OR !$this->registerHook('adminCustomers') OR !$this->registerHook('createAccount')
			OR !$this->registerHook('createAccountForm') OR !$this->registerHook('customerAccount'))
			return false;

		/* Define a default value for fixed amount vouchers, for each currency */
		foreach (Currency::getCurrencies() AS $currency)
			Configuration::updateValue('REFERRAL_DISCOUNT_VALUE_'.(int)($currency['id_currency']), 5);

		/* Define a default value for the percentage vouchers */
		Configuration::updateValue('REFERRAL_PERCENTAGE', 5);

		/* This hook is optional */
		$this->registerHook('displayMyAccountBlock');

		return true;
	}

	public function installDB()
	{
		return Db::getInstance()->execute('
		CREATE TABLE `'._DB_PREFIX_.'referralprogram` (
			`id_referralprogram` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_sponsor` INT UNSIGNED NOT NULL,
			`email` VARCHAR(255) NOT NULL,
			`lastname` VARCHAR(128) NOT NULL,
			`firstname` VARCHAR(128) NOT NULL,
			`id_customer` INT UNSIGNED DEFAULT NULL,
			`id_cart_rule` INT UNSIGNED DEFAULT NULL,
			`id_cart_rule_sponsor` INT UNSIGNED DEFAULT NULL,
			`date_add` DATETIME NOT NULL,
			`date_upd` DATETIME NOT NULL,
			PRIMARY KEY (`id_referralprogram`),
			UNIQUE KEY `index_unique_referralprogram_email` (`email`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');
	}

	public function uninstall()
	{
		$result = true;
		foreach (Currency::getCurrencies() AS $currency)
			$result = $result AND Configuration::deleteByName('REFERRAL_DISCOUNT_VALUE_'.(int)($currency['id_currency']));
		if (!parent::uninstall() OR !$this->uninstallDB() OR !$this->removeMail() OR !$result
		OR !Configuration::deleteByName('REFERRAL_PERCENTAGE') OR !Configuration::deleteByName('REFERRAL_ORDER_QUANTITY')
		OR !Configuration::deleteByName('REFERRAL_DISCOUNT_TYPE') OR !Configuration::deleteByName('REFERRAL_NB_FRIENDS')
		OR !Configuration::deleteByName('REFERRAL_DISCOUNT_DESCRIPTION'))
			return false;
		return true;
	}

	public function uninstallDB()
	{
		return Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'referralprogram`;');
	}

	public function removeMail()
	{
		$langs = Language::getLanguages(false);
		foreach ($langs AS $lang)
			foreach (array('referralprogram-congratulations', 'referralprogram-invitation', 'referralprogram-voucher') AS $name)
				foreach (array('txt', 'html') AS $ext)
				{
					$file = _PS_MAIL_DIR_.$lang['iso_code'].'/'.$name.'.'.$ext;
					if (file_exists($file) AND !@unlink($file))
						$this->_errors[] = $this->l('Cannot delete this file:').' '.$file;
				}
		return true;
	}

	public static function displayDiscount($discountValue, $discountType, $currency = false)
	{
		if ((float)$discountValue AND (int)$discountType)
		{
			if ($discountType == 1)
				return $discountValue.chr(37); // ASCII #37 --> % (percent)
			elseif ($discountType == 2)
				return Tools::displayPrice($discountValue, $currency);
		}
		return ''; // return a string because it's a display method
	}
	
	private function _postProcess()
	{
		Configuration::updateValue('REFERRAL_ORDER_QUANTITY', (int)(Tools::getValue('order_quantity')));
		foreach (Tools::getValue('discount_value') AS $id_currency => $discount_value)
			Configuration::updateValue('REFERRAL_DISCOUNT_VALUE_'.(int)($id_currency), (float)($discount_value));
		Configuration::updateValue('REFERRAL_DISCOUNT_TYPE', (int)(Tools::getValue('discount_type')));
		Configuration::updateValue('REFERRAL_NB_FRIENDS', (int)(Tools::getValue('nb_friends')));
		Configuration::updateValue('REFERRAL_PERCENTAGE', (int)(Tools::getValue('discount_value_percentage')));
		foreach (Language::getLanguages(false) as $lang)
			Configuration::updateValue('REFERRAL_DISCOUNT_DESCRIPTION', array($lang['id_lang'] => Tools::getValue('discount_description_'.(int)$lang['id_lang'])));
		
		$this->_html .= $this->displayConfirmation($this->l('Configuration updated.'));
	}

	private function _postValidation()
	{
		$this->_errors = array();
		if (!(int)(Tools::getValue('order_quantity')) OR Tools::getValue('order_quantity') < 0)
			$this->_errors[] = $this->displayError($this->l('Order quantity is required/invalid.'));
		if (!is_array(Tools::getValue('discount_value')))
			$this->_errors[] = $this->displayError($this->l('Discount value is invalid.'));
		foreach (Tools::getValue('discount_value') AS $id_currency => $discount_value)
 			if ($discount_value == '')
				$this->_errors[] = $this->displayError(sprintf($this->l('Discount value for the currency #%d is empty.'), $id_currency));
 			elseif (!Validate::isUnsignedFloat($discount_value))
				$this->_errors[] = $this->displayError(sprintf($this->l('Discount value for the currency #%d is invalid.'), $id_currency));
		if (!(int)(Tools::getValue('discount_type')) OR Tools::getValue('discount_type') < 1 OR Tools::getValue('discount_type') > 2)
			$this->_errors[] = $this->displayError($this->l('Discount type is required/invalid.'));
		if (!(int)(Tools::getValue('nb_friends')) OR Tools::getValue('nb_friends') < 0)
			$this->_errors[] = $this->displayError($this->l('Number of friends is required/invalid.'));
		if (!(int)(Tools::getValue('discount_value_percentage')) OR (int)(Tools::getValue('discount_value_percentage')) < 0 OR (int)(Tools::getValue('discount_value_percentage')) > 100)
			$this->_errors[] = $this->displayError($this->l('Discount percentage is required/invalid.'));
	}

	private function _writeXml()
	{
		$forbiddenKey = array('submitUpdate'); // Forbidden key

		// Generate new XML data
		$newXml = '<'.'?xml version=\'1.0\' encoding=\'utf-8\' ?>'."\n";
		$newXml .= '<referralprogram>'."\n";
		$newXml .= "\t".'<body>';
		// Making body data
		foreach (Language::getLanguages(false) as $lang)
		{
			if ($line = $this->putContent($newXml, 'body_paragraph_'.(int)$lang['id_lang'], Tools::getValue('body_paragraph_'.(int)$lang['id_lang']), $forbiddenKey, 'body'))
				$newXml .= $line;
		}
		
		$newXml .= "\n\t".'</body>'."\n";
		$newXml .= '</referralprogram>'."\n";

		/* write it into the editorial xml file */
		if ($fd = @fopen($this->_xmlFile, 'w'))
		{
			if (!@fwrite($fd, $newXml))
				$this->_html .= $this->displayError($this->l('Unable to write to the xml file.'));
			if (!@fclose($fd))
				$this->_html .= $this->displayError($this->l('Cannot close the xml file.'));
		}
		else
			$this->_html .= $this->displayError($this->l('Unable to update the xml file. Please check the xml file\'s writing permissions.'));
	}

	public function putContent($xml_data, $key, $field, $forbidden, $section)
	{
		foreach ($forbidden AS $line)
			if ($key == $line)
				return 0;
		if (!preg_match('/^'.$section.'_/i', $key))
			return 0;
		$key = preg_replace('/^'.$section.'_/i', '', $key);
		$field = Tools::htmlentitiesDecodeUTF8(htmlspecialchars($field));
		if (!$field)
			return 0;
		return ("\n\t\t".'<'.$key.'><![CDATA['.$field.']]></'.$key.'>');
	}

	public function getContent()
	{
		if (Tools::isSubmit('submitReferralProgram'))
		{
			$this->_postValidation();
			if (!sizeof($this->_errors))
				$this->_postProcess();
			else
				foreach ($this->_errors AS $err)
					$this->_html .= $err;
		}
		elseif (Tools::isSubmit('submitText'))
			$this->_writeXml();

		$this->_html .= $this->renderForm();

		return $this->_html;
	}

	/**
	* Hook call when cart created and updated
	* Display the discount name if the sponsor friend have one
	*/
	public function hookShoppingCart($params)
	{
		include_once(dirname(__FILE__).'/ReferralProgramModule.php');

		if (!isset($params['cart']->id_customer))
			return false;
		if (!($id_referralprogram = ReferralProgramModule::isSponsorised((int)($params['cart']->id_customer), true)))
			return false;
		$referralprogram = new ReferralProgramModule($id_referralprogram);
		if (!Validate::isLoadedObject($referralprogram))
			return false;
		$cartRule = new CartRule($referralprogram->id_cart_rule);
		if (!Validate::isLoadedObject($cartRule))
			return false;

		//if ($cartRule->checkValidity($this->context) === false)
		//{
			$this->smarty->assign(array('discount_display' => ReferralProgram::displayDiscount($cartRule->reduction_percent ? $cartRule->reduction_percent : $cartRule->reduction_amount, $cartRule->reduction_percent ? 1 : 2, new Currency($params['cookie']->id_currency)), 'discount' => $cartRule));
			return $this->display(__FILE__, 'shopping-cart.tpl');
	//	}
		return false;
	}

	/**
	* Hook display on customer account page
	* Display an additional link on my-account and block my-account
	*/
	public function hookCustomerAccount($params)
	{
		return $this->display(__FILE__, 'my-account.tpl');
	}

	public function hookDisplayMyAccountBlock($params)
	{
		return $this->hookCustomerAccount($params);
	}

	/**
	* Hook display on form create account
	* Add an additional input on bottom for fill the sponsor's e-mail address
	*/
	public function hookCreateAccountForm($params)
	{
		include_once(dirname(__FILE__).'/ReferralProgramModule.php');

		if (Configuration::get('PS_CIPHER_ALGORITHM'))
			$cipherTool = new Rijndael(_RIJNDAEL_KEY_, _RIJNDAEL_IV_);
		else
			$cipherTool = new Blowfish(_COOKIE_KEY_, _COOKIE_IV_);
		$explodeResult = explode('|', $cipherTool->decrypt(urldecode(Tools::getValue('sponsor'))));
		if ($explodeResult AND count($explodeResult) > 1 AND list($id_referralprogram, $email) = $explodeResult AND (int)($id_referralprogram) AND !empty($email) AND Validate::isEmail($email) AND $id_referralprogram == ReferralProgramModule::isEmailExists($email))
		{
			$referralprogram = new ReferralProgramModule($id_referralprogram);
			if (Validate::isLoadedObject($referralprogram))
			{
				/* hack for display referralprogram information in form */
				$_POST['customer_firstname'] = $referralprogram->firstname;
				$_POST['firstname'] = $referralprogram->firstname;
				$_POST['customer_lastname'] = $referralprogram->lastname;
				$_POST['lastname'] = $referralprogram->lastname;
				$_POST['email'] = $referralprogram->email;
				$_POST['email_create'] = $referralprogram->email;
				$sponsor = new Customer((int)$referralprogram->id_sponsor);
				$_POST['referralprogram'] = $sponsor->email;
			}
		}
		return $this->display(__FILE__, 'authentication.tpl');
	}

	/**
	* Hook called on creation customer account
	* Create a discount for the customer if sponsorised
	*/
	public function hookCreateAccount($params)
	{
		$newCustomer = $params['newCustomer'];
		if (!Validate::isLoadedObject($newCustomer))
			return false;
		$postVars = $params['_POST'];
		if (empty($postVars) OR !isset($postVars['referralprogram']) OR empty($postVars['referralprogram']))
			return false;
		$sponsorEmail = $postVars['referralprogram'];
		if (!Validate::isEmail($sponsorEmail) OR $sponsorEmail == $newCustomer->email)
			return false;

		$sponsor = new Customer();
		if ($sponsor = $sponsor->getByEmail($sponsorEmail, NULL, $this->context))
		{
			include_once(dirname(__FILE__).'/ReferralProgramModule.php');

			/* If the customer was not invited by the sponsor, we create the invitation dynamically */
			if (!$id_referralprogram = ReferralProgramModule::isEmailExists($newCustomer->email, true, false))
			{
				$referralprogram = new ReferralProgramModule();
				$referralprogram->id_sponsor = (int)$sponsor->id;
				$referralprogram->firstname = $newCustomer->firstname;
				$referralprogram->lastname = $newCustomer->lastname;
				$referralprogram->email = $newCustomer->email;
				if (!$referralprogram->validateFields(false))
					return false;
				else
					$referralprogram->save();
			}
			else
				$referralprogram = new ReferralProgramModule((int)$id_referralprogram);

			if ($referralprogram->id_sponsor == $sponsor->id)
			{
				$referralprogram->id_customer = (int)$newCustomer->id;
				$referralprogram->save();
				if ($referralprogram->registerDiscountForSponsored((int)$params['cookie']->id_currency))
				{
					$cartRule = new CartRule((int)$referralprogram->id_cart_rule);
					if (Validate::isLoadedObject($cartRule))
					{
						$data = array(
							'{firstname}' => $newCustomer->firstname,
							'{lastname}' => $newCustomer->lastname,
							'{voucher_num}' => $cartRule->code,
							'{voucher_amount}' => (Configuration::get('REFERRAL_DISCOUNT_TYPE') == 2 ? Tools::displayPrice((float)Configuration::get('REFERRAL_DISCOUNT_VALUE_'.(int)$this->context->currency->id), (int)Configuration::get('PS_CURRENCY_DEFAULT')) : (float)Configuration::get('REFERRAL_PERCENTAGE').'%'));

						$cookie = $this->context->cookie;

						Mail::Send(
							(int)$cookie->id_lang,
							'referralprogram-voucher',
							Mail::l('Congratulations!', (int)$cookie->id_lang),
							$data,
							$newCustomer->email,
							$newCustomer->firstname.' '.$newCustomer->lastname,
							strval(Configuration::get('PS_SHOP_EMAIL')),
							strval(Configuration::get('PS_SHOP_NAME')),
							null,
							null,
							dirname(__FILE__).'/mails/'
						);
					}
				}
				return true;
			}
		}
		return false;
	}

	/**
	* Hook display in tab AdminCustomers on BO
	* Data table with all sponsors informations for a customer
	*/
	public function hookAdminCustomers($params)
	{
		include_once(dirname(__FILE__).'/ReferralProgramModule.php');

		$customer = new Customer((int)$params['id_customer']);
		if (!Validate::isLoadedObject($customer))
			die ($this->l('Incorrect Customer object.'));

		$friends = ReferralProgramModule::getSponsorFriend((int)$customer->id);
		if ($id_referralprogram = ReferralProgramModule::isSponsorised((int)$customer->id, true))
		{
			$referralprogram = new ReferralProgramModule((int)$id_referralprogram);
			$sponsor = new Customer((int)$referralprogram->id_sponsor);
		}

		$html = '
		<div class="clear">&nbsp;</div>
		<h2>'.$this->l('Referral program').' ('.count($friends).')</h2>
		<h3>'.(isset($sponsor) ? $this->l('Customer\'s sponsor:').' <a href="index.php?tab=AdminCustomers&id_customer='.(int)$sponsor->id.'&viewcustomer&token='.Tools::getAdminToken('AdminCustomers'.(int)(Tab::getIdFromClassName('AdminCustomers')).(int)$this->context->employee->id).'">'.$sponsor->firstname.' '.$sponsor->lastname.'</a>' : $this->l('No one has sponsored this customer.')).'</h3>';

		if ($friends AND sizeof($friends))
		{
			$html.= '<h3>'.sizeof($friends).' '.(sizeof($friends) > 1 ? $this->l('Sponsored customers:') : $this->l('Sponsored customer:')).'</h3>';
			$html.= '
			<table cellspacing="0" cellpadding="0" class="table">
				<tr>
					<th class="center">'.$this->l('ID').'</th>
					<th class="center">'.$this->l('Name').'</th>
					<th class="center">'.$this->l('Email').'</th>
					<th class="center">'.$this->l('Registration date').'</th>
					<th class="center">'.$this->l('Customers sponsored by this friend').'</th>
					<th class="center">'.$this->l('Placed orders').'</th>
					<th class="center">'.$this->l('Customer account created').'</th>
				</tr>';
				foreach ($friends AS $key => $friend)
				{
					$orders = Order::getCustomerOrders($friend['id_customer']);
					$html.= '
					<tr '.($key++ % 2 ? 'class="alt_row"' : '').' '.((int)($friend['id_customer']) ? 'style="cursor: pointer" onclick="document.location = \'?tab=AdminCustomers&id_customer='.$friend['id_customer'].'&viewcustomer&token='.Tools::getAdminToken('AdminCustomers'.(int)(Tab::getIdFromClassName('AdminCustomers')).(int)$this->context->employee->id).'\'"' : '').'>
						<td class="center">'.((int)($friend['id_customer']) ? $friend['id_customer'] : '--').'</td>
						<td>'.$friend['firstname'].' '.$friend['lastname'].'</td>
						<td>'.$friend['email'].'</td>
						<td>'.Tools::displayDate($friend['date_add'],null , true).'</td>
						<td align="right">'.sizeof(ReferralProgramModule::getSponsorFriend($friend['id_customer'])).'</td>
						<td align="right">'.($orders ? sizeof($orders) : 0).'</td>
						<td align="center">'.((int)$friend['id_customer'] ? '<img src="'._PS_ADMIN_IMG_.'enabled.gif" />' : '<img src="'._PS_ADMIN_IMG_.'disabled.gif" />').'</td>
					</tr>';
				}
			$html.= '
				</table>';
		}
		else
			$html.= sprintf($this->l('%1$s %2$s has not sponsored any friends yet.'), $customer->firstname, $customer->lastname);
		return $html.'<br/><br/>';
	}

	/**
	* Hook called when a order is confimed
	* display a message to customer about sponsor discount
	*/
	public function hookOrderConfirmation($params)
	{
		if ($params['objOrder'] AND !Validate::isLoadedObject($params['objOrder']))
			return die($this->l('Incorrect Order object.'));

		include_once(dirname(__FILE__).'/ReferralProgramModule.php');

		$customer = new Customer((int)$params['objOrder']->id_customer);
		$stats = $customer->getStats();
		$nbOrdersCustomer = (int)$stats['nb_orders'] + 1; // hack to count current order
		$referralprogram = new ReferralProgramModule(ReferralProgramModule::isSponsorised((int)$customer->id, true));
		if (!Validate::isLoadedObject($referralprogram))
			return false;
		$sponsor = new Customer((int)$referralprogram->id_sponsor);
		if ((int)$nbOrdersCustomer == (int)$this->_configuration['REFERRAL_ORDER_QUANTITY'])
		{
			$cartRule = new CartRule((int)$referralprogram->id_cart_rule_sponsor);
			if (!Validate::isLoadedObject($cartRule))
				return false;
			$this->smarty->assign(array('discount' => ReferralProgram::displayDiscount($cartRule->reduction_percent ? $cartRule->reduction_percent : $cartRule->reduction_amount, $cartRule->reduction_percent ? 1 : 2, new Currency((int)$params['objOrder']->id_currency)), 'sponsor_firstname' => $sponsor->firstname, 'sponsor_lastname' => $sponsor->lastname));
			return $this->display(__FILE__, 'order-confirmation.tpl');
		}
		return false;
	}

	/**
	* Hook called when order status changed
	* register a discount for sponsor and send him an e-mail
	*/
	public function hookUpdateOrderStatus($params)
	{
		if (!Validate::isLoadedObject($params['newOrderStatus']))
			die ($this->l('Missing parameters'));
		$orderState = $params['newOrderStatus'];
		$order = new Order((int)($params['id_order']));
		if ($order AND !Validate::isLoadedObject($order))
			die($this->l('Incorrect Order object.'));

		include_once(dirname(__FILE__).'/ReferralProgramModule.php');

		$customer = new Customer((int)$order->id_customer);
		$stats = $customer->getStats();
		$nbOrdersCustomer = (int)$stats['nb_orders'] + 1; // hack to count current order
		$referralprogram = new ReferralProgramModule(ReferralProgramModule::isSponsorised((int)($customer->id), true));
		if (!Validate::isLoadedObject($referralprogram))
			return false;
		$sponsor = new Customer((int)$referralprogram->id_sponsor);
		if ((int)$orderState->logable AND $nbOrdersCustomer >= (int)$this->_configuration['REFERRAL_ORDER_QUANTITY'] AND $referralprogram->registerDiscountForSponsor((int)$order->id_currency))
		{
			$cartRule = new CartRule((int)$referralprogram->id_cart_rule_sponsor);
			$currency = new Currency((int)$order->id_currency);
			$discount_display = ReferralProgram::displayDiscount( (float) $cartRule->reduction_percent ? (float) $cartRule->reduction_percent : (int) $cartRule->reduction_amount,  (float) $cartRule->reduction_percent ? 1 : 2, $currency);			$data = array('{sponsored_firstname}' => $customer->firstname, '{sponsored_lastname}' => $customer->lastname, '{discount_display}' => $discount_display, '{discount_name}' => $cartRule->code);
			Mail::Send((int)$order->id_lang, 'referralprogram-congratulations', Mail::l('Congratulations!', (int)$order->id_lang), $data, $sponsor->email, $sponsor->firstname.' '.$sponsor->lastname, strval(Configuration::get('PS_SHOP_EMAIL')), strval(Configuration::get('PS_SHOP_NAME')), NULL, NULL, dirname(__FILE__).'/mails/');
			return true;
		}
		return false;
	}
	
	public function renderForm()
	{

		$fields_form_1 = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Minimum number of orders a sponsored friend must place to get their voucher'),
						'name' => 'order_quantity',
					),
					array(
						'type' => 'text',
						'label' => $this->l('Number of friends in the referral program invitation form (customer account, referral program section):'),
						'name' => 'nb_friends',
					),
					array(
						'type' => 'radio',
						'label' => $this->l('Voucher type :'),
						'name' => 'discount_type',
						'values' => array(
							array(
								'id' => 'discount_type1',
								'value' => 1,
								'label' => $this->l('Voucher offering a percentage')),
							array(
								'id' => 'discount_type2',
								'value' => 2,
								'label' => $this->l('Voucher offering a fixed amount (by currency)')),
						),
					),
					array(
						'type' => 'text',
						'label' => $this->l('Percentage'),
						'name' => 'discount_value_percentage',
						'suffix' => '%'
					),
					array(
						'type' => 'discount_value',
						'label' => 	$this->l('Voucher amount'),
						'name' => 'discount_value',
					),
					array(
						'type' => 'text',
						'label' => $this->l('Voucher description'),
						'name' => 'discount_description',
						'lang' => true,
					)
				),
				'submit' => array(
					'title' => $this->l('Save'),
					'class' => 'btn btn-primary',
					'name' => 'submitReferralProgram',
					)
			),
		);
		
		$fields_form_2 = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Conditions of the referral program'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'textarea',
						'autoload_rte' => true,
						'label' => $this->l('Text'),
						'name' => 'body_paragraph',
						'lang' => true,
					)
				),
				'submit' => array(
					'title' => $this->l('Save'),
					'class' => 'btn btn-primary',
					'name' => 'submitText',
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
		$helper->submit_action = 'submitModule';
		$helper->module = $this;
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'currencies' => Currency::getCurrencies(),
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);
		
		$helper->override_folder = '/';
		
		return $this->renderJs().$helper->generateForm(array($fields_form_1, $fields_form_2));
	}
	
	public function getConfigFieldsValues()
	{	
		$fields_values = array(
			'order_quantity' => Tools::getValue('order_quantity', Configuration::get('REFERRAL_ORDER_QUANTITY')),
			'discount_type' => Tools::getValue('discount_type', Configuration::get('REFERRAL_DISCOUNT_TYPE')),
			'nb_friends' => Tools::getValue('nb_friends', Configuration::get('REFERRAL_NB_FRIENDS')),
			'discount_value_percentage' => Tools::getValue('discount_value_percentage', Configuration::get('REFERRAL_PERCENTAGE')),
		);
	
		$languages = Language::getLanguages(false);
		foreach ($languages as $lang)
			$fields_values['discount_description'][$lang['id_lang']] = Tools::getValue('discount_description_'.(int)$lang['id_lang'], Configuration::get('REFERRAL_DISCOUNT_DESCRIPTION', (int)$lang['id_lang']));
		
		$currencies = Currency::getCurrencies();
		foreach ($currencies as $currency)
			$fields_values['discount_value'][$currency['id_currency']] = Tools::getValue('discount_value['.(int)$currency['id_currency'].']', Configuration::get('REFERRAL_DISCOUNT_VALUE_'.(int)$currency['id_currency']));
		
		// xml loading
		$xml = false;
		if (file_exists($this->_xmlFile))
			if ($xml = @simplexml_load_file($this->_xmlFile))
				foreach ($languages as $lang)
				{
					$key = 'paragraph_'.$lang['id_lang'];
					$fields_values['body_paragraph'][$lang['id_lang']] = Tools::getValue('body_paragraph_'.(int)$lang['id_lang'] ,(string)$xml->body->$key);
				}
	
		return $fields_values;
	}
	
	public function renderJs()
	{
		return "
		<script>
			$(document).ready(function () {
				toggleVoucherType()
				$('input[name=discount_type]').click(function () {toggleVoucherType()});
			});
			
			function toggleVoucherType()
			{
				if ($('input[name=discount_type]:checked').val() == 2)
				{
					$('#discount_value_percentage').closest('.row').hide();
					$('#discount_value').closest('.row').show();
				}
				else
				{
					$('#discount_value_percentage').closest('.row').show();
					$('#discount_value').closest('.row').hide();
				}
			}
		</script>
		";
	}
}
