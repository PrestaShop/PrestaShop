<?php
/*
* 2007-2011 PrestaShop
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class dibs extends PaymentModule
{
	/**
	 * @var string set the merchant id sent by DIBS e-mail after subscription
	 * @staticvar
	 */
	public static $ID_MERCHANT;
	
	/**
	 * The URL of the page to be displayed if the purchase is approved.
	 * @var string
	 * @staticvar
	 */
	private static $ACCEPTED_URL = '';
	
	/**
	 * The URL of the page to be displayed if the customer cancels the payment.
	 * @var string
	 * @staticvar
	 */
	private static $CANCELLED_URL = '';
	
	/**
	 * Set the testing mode.
	 * @var string
	 */
	private static $TESTING;

	/**
	 * define more settings values, set for new version which probably.
	 * @var array
	 */
	public static $MORE_SETTINGS;
	
	/**
	 * @var string
	 * @staticvar
	 */
	private static $site_url;
	/**
	 * Set the smarty object
	 * @var Smarty
	 */
	private $smarty;
	
	/**
	 * Only this langs array are allowed in DIBS API
	 * @var array
	 */
	private static $accepted_lang = array('da','en','es','fi','fo','fr','it','nl','no','pl','sv');
	
	/**
	 * Formular link to DIBS subscription
	 * @var array
	 */
	public static $dibs_subscription_link = array(
		'en'	=> 'http://www.dibspayment.com/order/uk_request/',
		'da'	=> 'http://www.dibs.dk/bestil/step1/?productid=234&producttype=240',
		'sv'	=> 'http://www.dibs.se/bestall/step1/?productid=339&producttype=340',
		'no'	=> 'http://www.dibs.no/bestill/step1/?productid=349&producttype=350',
	);
	public function __construct()
	{
		global $smarty;
		$this->smarty = $smarty;
		$this->name = 'dibs';
		$this->tab = 'payments_gateways';
		$this->version = '1.0';

		parent::__construct();

		$this->displayName = $this->l('DIBS');
		$this->description = $this->l('DIBS payment API');
		
		if (self::$site_url === NULL)
			self::$site_url = Tools::htmlentitiesutf8(Tools::getProtocol().$_SERVER['HTTP_HOST'].__PS_BASE_URI__);
		
		self::$ID_MERCHANT = Configuration::get('DIBS_ID_MERCHANT');
		self::$ACCEPTED_URL = Configuration::get('DIBS_ACCEPTED_URL');
		self::$CANCELLED_URL = Configuration::get('DIBS_CANCELLED_URL');
		self::$TESTING = (int)Configuration::get('DIBS_TESTING');
		self::$MORE_SETTINGS = Configuration::get('DIBS_MORE_SETTINGS') != '' ? unserialize(Tools::htmlentitiesDecodeUTF8(Configuration::get('DIBS_MORE_SETTINGS'))) : array();
		
		if (!isset(self::$MORE_SETTINGS['k1'])
			OR (isset(self::$MORE_SETTINGS['k1']) AND (self::$MORE_SETTINGS['k1'] === '' OR self::$MORE_SETTINGS['k2'] === '') ))
			$this->warning = $this->l('For security reasons, you must set key #1 and key #2 used by MD5 control of DIBS API.');
		if (!self::$ID_MERCHANT OR self::$ID_MERCHANT === '')
			$this->warning = $this->l('You have to set your merchant ID to use DIBS API.');
	}
	
	public function install()
	{
		return (parent::install() 
			AND $this->registerHook('orderConfirmation') 
			AND $this->registerHook('payment') 
			AND Configuration::updateValue('DIBS_ACCEPTED_URL', self::$site_url.(substr(trim(self::$site_url), -1, 1) === '/' ? '' : '/').'order-confirmation.php')
			AND Configuration::updateValue('DIBS_CANCELLED_URL', self::$site_url)
			AND Configuration::updateValue('DIBS_TESTING', 1)
			AND Configuration::updateValue('DIBS_MORE_SETTINGS', Tools::htmlentitiesUTF8(serialize(array('flexwin_color' => 'blue', 'logo_color' => 'black', 'k1' => '', 'k2' => ''))), true));
	}
	
	public function uninstall()
	{
		return (parent::uninstall()
			AND Configuration::deleteByName('DIBS_ACCEPTED_URL')
			AND Configuration::deleteByName('DIBS_ID_MERCHANT')
			AND Configuration::deleteByName('DIBS_CANCELLED_URL')
			AND Configuration::deleteByName('DIBS_TESTING')
			AND Configuration::deleteByName('DIBS_MORE_SETTINGS'));
	}

   	public function hookOrderConfirmation($params)
	{
		if (!$this->active)
			return;
		if ($params['objOrder']->module != $this->name)
			return;
		
		if ($params['objOrder']->valid)
			$this->smarty->assign(array('status' => 'ok', 'id_order' => $params['objOrder']->id));
		else
			$this->smarty->assign('status', 'failed');
		return $this->display(__FILE__, 'hookorderconfirmation.tpl');
	}
	
	private function preProcess()
	{
		if (Tools::isSubmit('submitModule'))
		{
			self::$ID_MERCHANT = (Tools::getValue('idMerchant') !== '' ? Tools::getValue('idMerchant') : self::$ID_MERCHANT);
			self::$ACCEPTED_URL = ((Validate::isUrl(Tools::getValue('acceptedUrl'))) ? Tools::getValue('acceptedUrl') : self::$ACCEPTED_URL);
			self::$CANCELLED_URL = ((Validate::isUrl(Tools::getValue('cancelledUrl'))) ? Tools::getValue('cancelledUrl') : self::$CANCELLED_URL);
			self::$TESTING = (int)isset($_POST['testing']);
			self::$MORE_SETTINGS['flexwin_color'] = Tools::getValue('flexwin_color');
			self::$MORE_SETTINGS['logo_color'] = Tools::getValue('logo_color');
			self::$MORE_SETTINGS['k1'] = Tools::getValue('k1');
			self::$MORE_SETTINGS['k2'] = Tools::getValue('k2');
			
			Configuration::updateValue('DIBS_ID_MERCHANT', self::$ID_MERCHANT);
			Configuration::updateValue('DIBS_ACCEPTED_URL', self::$ACCEPTED_URL);
			Configuration::updateValue('DIBS_CANCELLED_URL', self::$CANCELLED_URL);
			Configuration::updateValue('DIBS_TESTING', self::$TESTING);
			Configuration::updateValue('DIBS_MORE_SETTINGS', Tools::htmlentitiesUTF8(serialize(self::$MORE_SETTINGS)));
			
			$data_sync = '';
			if(self::$ID_MERCHANT !== '' AND self::$TESTING !== 1 AND self::$MORE_SETTINGS['k1'] !== '' AND self::$MORE_SETTINGS['k2'] !== '')
				$data_sync = '<img src="http://www.prestashop.com/modules/dibs.png?site_id='.urlencode(self::$ID_MERCHANT).'" style="float:right" />';
			
			echo '<div class="conf confirm"><img src="../img/admin/ok.gif"/>'.$this->l('Configuration updated').$data_sync.'</div>';
		}
	}
	private function _displayPresentation()
	{
		$href = '';
		if (isset(dibs::$dibs_subscription_link[Configuration::get('PS_LANG_DEFAULT')]))
			$href = dibs::$dibs_subscription_link[Configuration::get('PS_LANG_DEFAULT')];
		else
			$href = dibs::$dibs_subscription_link['en'];
		$out = '
		<fieldset class="width2">
			<legend><img src="../img/admin/contact.gif" />'.$this->l('Get a DIBS account').'</legend>
			<p>
				'.$this->l('Please click on the following link to access of DIBS formular subscription:')
				.' <a href="'.$href.'" class="link" target="_blank" >&raquo; '.$this->l('Link').' &laquo;</a>
			</p>
			<p>'
				.$this->l('Depending on language and country rules, formular subscription may be different.').'<br />'
				.$this->l('Please click on the appropriate flags:').'&nbsp;';
		foreach (dibs::$dibs_subscription_link as $lang=>$url)
		{
			$out .= '<a href="'.$url.'" title="'.$lang.'" class="link" target="_blank" ><img src="'.dibs::$site_url.'modules/dibs/img/'.$lang.'.jpg" /></a>';
		}
		$out .= '
			</p>
		</fieldset>';
		return $out;
	}
	public function getContent()
	{
		$this->preProcess();
		
		$flexwin_colors = array('sand', 'grey', 'blue');
		$logo_colors = array('yellow', 'grey', 'blue', 'black', 'purple', 'green');
		$str = '<h2>'.$this->displayName.'</h2>'
		.$this->_displayPresentation()
		.'<br />
		<form action="'.Tools::htmlentitiesutf8($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset class="width2">
				<legend><img src="../img/admin/contact.gif" />'.$this->l('Settings').'</legend>
				<label>'.$this->l('Logo color').'<br /><br />
				<img src="'.self::$site_url.'modules/dibs/logos/dibs_'.self::$MORE_SETTINGS['logo_color'].'.jpg" />
				</label>
				<div class="margin-form">';
		foreach ($logo_colors as $logo_color)
			$str .= '<input type="radio" name="logo_color" '.(self::$MORE_SETTINGS['logo_color'] === $logo_color ? 'checked ' : '').'value="'.$logo_color.'" /> '.$logo_color.'<br />';
		$str .= '	<p>'.$this->l('The basic color of the logo which appears on the payment page.').'</p>
				</div>
				<label>'.$this->l('Merchant ID').' <sup>*</sup></label>
				<div class="margin-form">
					<input type="text" size="20" name="idMerchant" value="'.self::$ID_MERCHANT.'" />
					<p>'.$this->l('See the e-mail sent by DIBS.').'</p>
				</div>
				<label>'.$this->l('Secure Key #1').' <sup>*</sup></label>
				<div class="margin-form">
					<input type="text" size="20" name="k1" value="'.self::$MORE_SETTINGS['k1'].'" />
				</div>
				<label>'.$this->l('Secure Key #2').' <sup>*</sup></label>
				<div class="margin-form">
					<input type="text" size="20" name="k2" value="'.self::$MORE_SETTINGS['k2'].'" />
					<p>'.$this->l('These keys are used for security improvement.').'<br />'.$this->l('To obtain these keys, go to the DIBS administration interface and under \'Integration\', select the MD5 Keys menu. Please ensure the MD5 control is activated, otherwise the module will not work.').'</p>
				</div>
				<label>'.$this->l('Use DIBS test module').'</label>
				<div class="margin-form">
					<input type="checkbox" name="testing" '.(self::$TESTING === 1 ? 'checked ' : '').'value="testing" />
					<p>'.$this->l('When this field is declared, the transaction is not dispatched to the card issuer, but is instead handled by the DIBS test module.')
					.$this->l('See also Step 5 of the 10 Step Guide for more information.')
					.' <a href="http://tech.dibs.dk/10_step_guide/your_own_test/" title="'.$this->l('See also Step 5 of the 10 Step Guide for more information.').'" target="_blank">> '.$this->l('Link').'</a><br />'
					.$this->l('During the initial integration with DIBS, there is no need to insert this parameter, since all default transactions will reach the DIBS test system until DIBS has approved integration. Should the test system be used at a later date, this will be activated at DIBS (contact DIBS support for reactivating the test mode of your shop).').'</p>
				</div>
				<label>'.$this->l('Flexwin color').'</label>
				<div class="margin-form">';
		foreach ($flexwin_colors as $flexwin_color)
			$str .= '<input type="radio" name="flexwin_color" '.(self::$MORE_SETTINGS['flexwin_color'] === $flexwin_color ? 'checked ' : '').'value="'.$flexwin_color.'" /> '.$flexwin_color.'<br />';
		$str .= '	<p>'.$this->l('The basic color theme of FlexWin.').'</p>
				</div>
				<label>'.$this->l('Accepted url').' <sup>*</sup></label>
				<div class="margin-form">
					<input type="text" size="20" name="acceptedUrl" value="'.self::$ACCEPTED_URL.'" />
					<p>'.$this->l('URL of the page to be displayed if the purchase is approved.').'</p>
				</div>
				<label>'.$this->l('Cancelled url').' <sup>*</sup></label>
				<div class="margin-form">
					<input type="text" size="20" name="cancelledUrl" value="'.self::$CANCELLED_URL.'" />
					<p>'.$this->l('URL of the page to be displayed if the customer cancels the payment.').'</p>
				</div>
				<br /><center><input type="submit" name="submitModule" value="'.$this->l('Update settings').'" class="button" /></center>
			</fieldset>
		</form>';
		return $str;
	}
	public function hookPayment($params)
	{
		if ((self::$ID_MERCHANT === false || self::$ID_MERCHANT === '' || self::$ID_MERCHANT === NULL) 
		|| (self::$ACCEPTED_URL === false || self::$ACCEPTED_URL === '' || self::$ACCEPTED_URL === NULL))
	 		return '';

		$currency = new Currency(intval($params['cart']->id_currency));
		$lang = new Language(intval($params['cart']->id_lang));
		$customer = new Customer(intval($params['cart']->id_customer));
		$address = new Address(intval($params['cart']->id_address_invoice));
		$country = new Country(intval($address->id_country), intval($params['cart']->id_lang));
		$products = $params['cart']->getProducts();
		
		$dibsParams = array();
		
		// Required
		$dibsParams['merchant']		= self::$ID_MERCHANT; // id merchant send from DIBS e-mail
			
		// don't cast to int !! It has strange behaviour (really strange) 
		// for example : When calculate a total amount of 557.05, the result is 55704 after casting !!
		$dibsParams['amount']		= $params['cart']->getOrderTotal(true, Cart::BOTH) * 100; // The smallest unit of an amount, cent for EUR
		$dibsParams['accepturl']	= self::$ACCEPTED_URL.'?id_cart='.(int)($params['cart']->id).'&id_module='.(int)($this->id).'&key='.$customer->secure_key; // The URL of the page to be displayed if the purchase is approved.
		$dibsParams['orderid']		= $params['cart']->id.'_'.date('YmdHis'); // The shop's order number for this particular puchase. It can be seen later when payment is captured, and will in some instances appear on the customer's bank statement (max. 50 characters, both numerals and letters may be used).
		$currency_num = 0;
		
		// for 1.3 compatibility
		if(!isset($currency->iso_code_num) OR $currency->iso_code_num == '')
		{
			$array_currency_iso_num = array(
				'DKK'	=> 208,
				'EUR'	=> 978,
				'USD'	=> 840,
				'GBP'	=> 826,
				'SEK'	=> 752,
				'AUD'	=> 036,
				'CAD'	=> 124,
				'ISK'	=> 352,
				'JPY'	=> 392,
				'NZD'	=> 554,
				'NOK'	=> 578,
				'CHF'	=> 756,
				'TRY'	=> 949,
			);
			$currency_num = $array_currency_iso_num[$currency->iso_code];
		}
		else
			$currency_num = $currency->iso_code_num;
		$dibsParams['currency']		= (int)$currency_num; // Currency specification as indicated in ISO4217 where the EUR is no. 978
		
		// optional
		$dibsParams['test']			= (self::$TESTING === 1) ? 'yes' : 'no'; // optional - This field is used when tests are being conducted on the shop (e.g. test=yes). When this field is declared, the transaction is not dispatched to the card issuer, but is instead handled by the DIBS test module. See also Step 5 of the 10 Step Guide for more information. During your initial integration with DIBS, there is no need to insert this parameter, since all default transactions will hit the DIBS test system until DIBS has approved integration. Should the test system be used at a later date, this will be activated at DIBS (contact DIBS support for reactivating the test mode of your shop).
		$dibsParams['lang']			= in_array(strtolower($lang->iso_code), self::$accepted_lang) ? $lang->iso_code : ''; // optional - This parameter determines the language in which the page will be opened. The following values are accepted: da=Danish en=English es=Spanish fi=Finnish fo=Faroese fr=French it=Italian nl=Dutch no=Norwegian pl=Polish (simplified) sv=Swedish Default language is Danish.
		$dibsParams['color']		= self::$MORE_SETTINGS['flexwin_color']; // optional - The basic color theme of FlexWin. There is currently a choice of "sand", "grey" and "blue". The default value is "blue". 
		$dibsParams['cancelurl']	= self::$CANCELLED_URL; // optional - The URL of the page to be displayed if the customer cancels the payment.
		$dibsParams['uniqueoid']	= (int)($params['cart']->id).'_'.date('YmdHis').'_'.$params['cart']->secure_key; // optional - If this field exists, the orderid-field must be unique, i.e. there is no existing transaction with DIBS with the same order number. If such a transaction already exists, payment will be rejected with reason=7. Unless you are unable to generate unique order numbers, we strongly urge you to utilize this field.Note: Order numbers can be composed of a maximum of 50 characters (DIBS automatically removes surplus characters) and that uniqueoid is therefore unable to work as intended if order numbers consisting of more than 50 characters are used.
		$dibsParams['callbackurl']	= self::$site_url.'modules/'.$this->name.'/validation.php'; // optional - An optional �server-to-server� call which tells the shop�s server that payment was a success. Can be used for many purposes, the most important of these being the ability to register the order in your own system without depending on the customer�s browser hitting a specific page of the shop. See also HTTP_COOKIE.
		$dibsParams['HTTP_COOKIE']	= urlencode((string)serialize($params['cookie'])); // optional - Cookies/sessions which are to be sent to callbackurl. Must be sent along if you are using callbackurl and depend on cookies/sessions for keeping track of the user. Read how this is carried out in the description of automatic call-back further down this page.
		$md5_params = 'merchant='.self::$ID_MERCHANT.'&orderid='.$dibsParams['orderid'].'&currency='.$dibsParams['currency'].'&amount='.$dibsParams['amount'];
		$dibsParams['md5key']		= md5(self::$MORE_SETTINGS['k2'].md5(self::$MORE_SETTINGS['k1'].$md5_params)); // optional - This variable enables a MD5 key control of the values received by DIBS. This control  confirms that the values sent to DIBS has not been tampered with during the transfer. The MD5 key is calculated as:  MD5(key2 + MD5(key1 + "merchant=&orderid=&transact="))  Where key1 and key2 are shop specific keys available through the DIBS administration interface, and + is the concatenation operator. NB! MD5 key check must also be enabled through the DIBS administration interface in order to work. Further details on MD5-key control.  
		
		// @todo need more infos.
		$dibsParams['account']		= ''; // optional - If multiple departments utilize the company's acquirer agreement with PBS, it may prove practical to keep the transactions separate at DIBS. An "account number" may be inserted in this field, so as to separate transactions at DIBS.
		$dibsParams['calcfee']		= ''; // optional - If this parameter is set (e.g. calcfee=foo), the charge due to the transaction will automatically be calculated and affixed, i.e., the charge payable to the acquirer (e.g. PBS)
		$dibsParams['capturenow']	= ''; // optional - If this field exists, an "instant capture" is carried out, i.e. the amount is immediately transferred from the customer's account to the shop's account. This function can only be utilized in the event that there is no actual physical delivery of any items. Contact DIBS when using this function. (Note that instant capture requires unique order numbers - also see the description of uniqueoid above).
		$dibsParams['ip']			= ''; // optional - DIBS retains the IP-number from which a card transaction is carried out. The IP-number is used for �fraud control�, etc. Some implementations may send the IP number of the shop to DIBS rather than that of the customer's machine. In order to provide the same services to shops which utilize such a program for their DIBS hookup, we offer the option of sending the "ip" parameter.
		$dibsParams['paytype']		= ''; // optional - Regarding the start-up of the DIBS FlexWin, the user can be limited to the use of just one particular payment form. This is accomplished by using the parameter "paytype".  This function can be used if you wish for example to use integration method 3 for payment cards and method 1 for eDankort. Furthermore, this function can be used if you wish to control the user's selections of method of payment from your own website.  You can also specify a list of payment methods that will be shown in the Flexwin. This list should be a comma separated with no spaces in between.  Example:  See our list of possible paytypes.
		$dibsParams['maketicket']	= ''; // optional - This parameter is intended for FlexWin, and actually performs two transactions. First it performs a regular authorisation. If, and only if, it is accepted, it is followed by a ticket registration.  Both a transaction and a ticket value are returned to "accepturl" if it is specified.  If "callbackurl" is specified, DIBS will perform two separate calls, corresponding to performing two transactions - one call to the regular authorisation, and another to the ticket registration. Both cases return a "transact" parameter value (e.g. transact="78901234"). In calls to "callbackurl" containing "preauth", the ticket value is composed of the "transact" parameter value.  "maketicket" implicitly sets the "preauth" parameter - however, you should avoid to explicitly specify any "preauth" parameter.  You cannot use "uniqueoid", "capturenow" or "md5key" along with "maketicket". Currently "maketicket" does not work with 3Dsecure. 
		$dibsParams['postype']		= ''; // optional - "postype" (one 't') is used when one wishes to register the transaction origin. For normal internet transaction it is not required to include "postype", as it is automatically set to SSL. Possible values are:  ssl = internet transactions, magnetic = magnetic stripe read, and signature is available, magnosig = magnetic stripe read, and no signature is available, mail = mail order, manual = manually entered, phone = phone order, signature = card and signature available, manually entered.
		$dibsParams['ticketrule']	= ''; // optional - Set the value of this parameter to the same as defined by you in DIBS Admin.
		$dibsParams['preauth']		= ''; // optional - When preauth=true is sent as part of the request to auth.cgi the DIBS server identifies the authorisation as a ticket authorisation rather than a normal transaction. Please note that the pre-authorised transaction is NOT available among the transactions in the DIBS administration interface. When using MD5 the Authkey must be calculated from the string transact=12345678&preauth=true&currency=123
		
		// @todo Since Prestashop manage vouchers, ask if necessary to use this params 
		$dibsParams['voucher']		= ''; // optional - If set to "yes", then the list of payment types on the first page of FlexWin will contain vouchers, too. If FlexWin is called with a paytype, which would lead directly to the payment form, the customer is given the choice of entering a voucher code first.
		$dibsParams['split']		= ''; // optional - "split" is used for splitting up a transaction into two or more sub-transactions. This enables part of an order to be paid for when shipped in part. It requires that the amount and currency of the part payments are known at the time of the order, and are posted to the DIBS server as:  split=2&amount1=&amount2=
		
		// to erase optional params which are not filled
		$dibsParams = array_filter($dibsParams);
		
		/* Order Information as "complex model" :
		 * -------------------------------------- */
		
		// delivery params
		$dibsParams['delivery1.Name'] = $address->firstname.' '.$address->lastname;
		$dibsParams['delivery2.Address'] = $address->address1;
		$dibsParams['delivery3.Address2'] = $address->address2;
		$dibsParams['delivery4.Postcode'] = $address->postcode;
		$dibsParams['delivery5.City'] = $address->city;
		$dibsParams['delivery6.Comment'] = $address->other;
		$dibsParams['delivery7.Phone'] = $address->phone;
		$dibsParams['delivery8.Company'] = $address->company;
		
		// order line (product list)
		$dibsParams['ordline0-1'] = 'Product ref / Product Id';
		$dibsParams['ordline0-2'] = 'Name';
		$dibsParams['ordline0-3'] = 'Description';
		$dibsParams['ordline0-4'] = 'Unit price with tax';
		$dibsParams['ordline0-5'] = 'Total price with tax';
		$dibsParams['ordline0-6'] = 'Quantity';
		$dibsParams['ordline0-7'] = 'Weight';
		$dibsParams['ordline0-8'] = 'ecotax';
		
		$count_products = 1;
		foreach ($products as $key => $product)
		{
			$dibsParams['ordline'.$count_products.'-1'] = 'ref.'.($product['reference'] != '' ? $product['reference'] : 'NC' ).'/id.'.$product['id_product'];
			$dibsParams['ordline'.$count_products.'-2'] = $product['name'];
			$dibsParams['ordline'.$count_products.'-3'] = strip_tags($product['description_short']);
			$dibsParams['ordline'.$count_products.'-4'] = $product['price_wt'];
			$dibsParams['ordline'.$count_products.'-5'] = $product['total_wt'];
			$dibsParams['ordline'.$count_products.'-6'] = $product['cart_quantity'];
			$dibsParams['ordline'.$count_products.'-7'] = $product['weight'];
			$dibsParams['ordline'.$count_products.'-8'] = $product['ecotax'];
			$count_products++;
		}
		
		// Price info
		$dibsParams['priceinfo1.Deliverycosts'] = $params['cart']->getOrderTotal(true, Cart::ONLY_SHIPPING);
		$dibsParams['priceinfo2.ProductsAmount'] = $params['cart']->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);
		$dibsParams['priceinfo3.AmountWithoutTax'] = $params['cart']->getOrderTotal(false, Cart::BOTH);
		$dibsParams['priceinfo4.AmountTotalTax'] = (float)($params['cart']->getOrderTotal(true, Cart::BOTH) - $params['cart']->getOrderTotal(false, Cart::BOTH));
		$this->smarty->assign('p', $dibsParams);
		$this->smarty->assign('logo_color', self::$MORE_SETTINGS['logo_color']);
		return $this->display(__FILE__, 'dibs.tpl');
	}
}
