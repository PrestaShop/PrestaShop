<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 7048 $
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
			foreach ($this->_mails['name'] AS $name)
				foreach ($this->_mails['ext'] AS $ext)
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
		Configuration::updateValue('REFERRAL_DISCOUNT_DESCRIPTION', Tools::getValue('discount_description'));
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
		foreach ($_POST AS $key => $field)
			if ($line = $this->putContent($newXml, $key, $field, $forbiddenKey, 'body'))
				$newXml .= $line;
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
					$this->_html .= '<div class="errmsg">'.$err.'</div>';
		}
		elseif (Tools::isSubmit('submitText'))
		{
			foreach ($_POST AS $key => $value)
				if (!is_array(Tools::getValue($key)) && !Validate::isString(Tools::getValue($key)))
				{
					$this->_html .= $this->displayError($this->l('Invalid html field, javascript is forbidden'));
					$this->_displayForm();
					return $this->_html;
				}
			$this->_writeXml();
		}

		$this->_html .= '<h2>'.$this->displayName.'</h2>';
		$this->_displayForm();
		$this->_displayFormRules();
		return $this->_html;
	}

	private function _displayForm()
	{
		$divLangName = 'cpara¤dd';
		$currencies = Currency::getCurrencies();

		$this->_html .= '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
		<fieldset>
			<legend><img src="'._PS_ADMIN_IMG_.'prefs.gif" alt="'.$this->l('Settings').'" />'.$this->l('Settings').'</legend>
			<p>
				<label class="t" for="order_quantity">'.$this->l('Minimum number of orders a sponsored friend must place to get their voucher:').'</label>
				<input type="text" name="order_quantity" id="order_quantity" value="'.Tools::safeOutput(Tools::getValue('order_quantity', Configuration::get('REFERRAL_ORDER_QUANTITY'))).'" style="width: 50px; text-align: right;" />
			</p>
			<p>
				<label class="t" for="nb_friends">'.$this->l('Number of friends in the referral program invitation form (customer account, referral program section):').'</label>
				<input type="text" name="nb_friends" id="nb_friends" value="'.Tools::safeOutput(Tools::getValue('nb_friends', Configuration::get('REFERRAL_NB_FRIENDS'))).'" style="width: 50px; text-align: right;" />
			</p>
			<p>
				<label class="t">'.$this->l('Voucher type:').'</label>
				<input type="radio" name="discount_type" id="discount_type1" value="1" onclick="$(\'#voucherbycurrency\').hide(); $(\'#voucherbypercentage\').show();" '.(Tools::getValue('discount_type', Configuration::get('REFERRAL_DISCOUNT_TYPE')) == 1 ? 'checked="checked"' : '').' />
				<label class="t" for="discount_type1">'.$this->l('Voucher offering a percentage').'</label>
				&nbsp;
				<input type="radio" name="discount_type" id="discount_type2" value="2" onclick="$(\'#voucherbycurrency\').show(); $(\'#voucherbypercentage\').hide();" '.(Tools::getValue('discount_type', Configuration::get('REFERRAL_DISCOUNT_TYPE')) == 2 ? 'checked="checked"' : '').' />
				<label class="t" for="discount_type2">'.$this->l('Voucher offering a fixed amount (by currency)').'</label>
			</p>
			<p id="voucherbypercentage"'.(Configuration::get('REFERRAL_DISCOUNT_TYPE') == 2 ? ' style="display: none;"' : '').'><label class="t">'.$this->l('Percentage:').'</label> <input type="text" id="discount_value_percentage" name="discount_value_percentage" value="'.Tools::safeOutput(Tools::getValue('discount_value_percentage', Configuration::get('REFERRAL_PERCENTAGE'))).'" style="width: 50px; text-align: right;" /> %</p>
			<table id="voucherbycurrency" cellpadding="5" style="border: 1px solid #BBB;'.(Configuration::get('REFERRAL_DISCOUNT_TYPE') == 1 ? ' display: none;' : '').'" border="0">
				<tr>
					<th style="width: 80px;">'.$this->l('Currency').'</th>
					<th>'.$this->l('Voucher amount').'</th>
				</tr>';

		foreach ($currencies AS $currency)
			$this->_html .= '
			<tr>
				<td>'.(Configuration::get('PS_CURRENCY_DEFAULT') == $currency['id_currency'] ? '<span style="font-weight: bold;">' : '').htmlentities($currency['name'], ENT_NOQUOTES, 'utf-8').(Configuration::get('PS_CURRENCY_DEFAULT') == $currency['id_currency'] ? '<span style="font-weight: bold;">' : '').'</td>
				<td><input type="text" name="discount_value['.(int)($currency['id_currency']).']" id="discount_value['.(int)($currency['id_currency']).']" value="'.Tools::safeOutput(Tools::getValue('discount_value['.(int)($currency['id_currency']).']', Configuration::get('REFERRAL_DISCOUNT_VALUE_'.(int)($currency['id_currency'])))).'" style="width: 50px; text-align: right;" /> '.$currency['sign'].'</td>
			</tr>';

		$this->_html .= '
		</table>
			<p>
				 <div style="float: left"><label class="t" for="discount_description">'.$this->l('Voucher description:').'</label></div>';
			$id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
			$languages = Language::getLanguages(true);

			foreach ($languages AS $language)
				$this->_html .= '
				<div id="dd_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $id_lang_default ? 'block' : 'none').'; float: left; margin-left: 4px;">
					<input type="text" name="discount_description['.$language['id_lang'].']" id="discount_description['.$language['id_lang'].']" value="'.(isset($_POST['discount_description'][(int)($language['id_lang'])]) ? $_POST['discount_description'][(int)($language['id_lang'])] : $this->_configuration['REFERRAL_DISCOUNT_DESCRIPTION'][(int)($language['id_lang'])]).'" style="width: 200px;" />
				</div>';
			$this->_html .= $this->displayFlags($languages, $id_lang_default, $divLangName, 'dd', true);
			$this->_html .= '
			</p>
			<div class="clear center"><input class="button" style="margin-top: 10px" name="submitReferralProgram" id="submitReferralProgram" value="'.$this->l('Update settings').'" type="submit" /></div>
		</fieldset>
		</form><br/>';
	}

	private function _displayFormRules()
	{
		// Languages preliminaries
		$languages = Language::getLanguages();
		$iso = $this->context->language->iso_code;
		$divLangName = 'cpara¤dd';

		// xml loading
		$xml = false;
		if (file_exists($this->_xmlFile))
			if (!$xml = @simplexml_load_file($this->_xmlFile))
				$this->_html .= $this->displayError($this->l('Your text is empty.'));

		// TinyMCE
		$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
		$ad = dirname($_SERVER["PHP_SELF"]);
		$this->_html .= '
			<script type="text/javascript">
			var iso = \''.$isoTinyMCE.'\' ;
			var pathCSS = \''._THEME_CSS_DIR_.'\' ;
			var ad = \''.$ad.'\' ;
			</script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js"></script>
			<script language="javascript" type="text/javascript">id_language = Number('.$this->context->language->id.');</script>
		<form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data">
			<fieldset>
				<legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->l('Conditions of the referral program').'</legend>';
		foreach ($languages AS $language)
		{
			$this->_html .= '
			<div id="cpara_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->context->language->id ? 'block' : 'none').';float: left;">
				<textarea class="rte" cols="120" rows="25" id="body_paragraph_'.$language['id_lang'].'" name="body_paragraph_'.$language['id_lang'].'">'.($xml ? stripslashes(htmlspecialchars($xml->body->{'paragraph_'.$language['id_lang']})) : '').'</textarea>
			</div>';
		}
		$this->_html .= $this->displayFlags($languages, $this->context->language->id, $divLangName, 'cpara', true);

		$this->_html .= '
				<div class="clear center"><input type="submit" name="submitText" value="'.$this->l('Update text').'" class="button" style="margin-top: 10px" /></div>
			</fieldset>
		</form>';
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
						<td>'.Tools::displayDate($friend['date_add'], $this->context->language->id, true).'</td>
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
			$discount_display = ReferralProgram::displayDiscount($cartRule->reduction_percent ? $cartRule->reduction_percent : $cartRule->reduction_amount, $cartRule->reduction_percent ? 1 : 2, $currency);
			$data = array('{sponsored_firstname}' => $customer->firstname, '{sponsored_lastname}' => $customer->lastname, '{discount_display}' => $discount_display, '{discount_name}' => $cartRule->code);
			Mail::Send((int)$order->id_lang, 'referralprogram-congratulations', Mail::l('Congratulations!', (int)$order->id_lang), $data, $sponsor->email, $sponsor->firstname.' '.$sponsor->lastname, strval(Configuration::get('PS_SHOP_EMAIL')), strval(Configuration::get('PS_SHOP_NAME')), NULL, NULL, dirname(__FILE__).'/mails/');
			return true;
		}
		return false;
	}
}
