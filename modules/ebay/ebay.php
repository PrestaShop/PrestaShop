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

// Security
if (!defined('_PS_VERSION_'))
	exit;

// Loading eBay Class Request
if (file_exists(dirname(__FILE__).'/eBayRequest.php'))
	require_once(dirname(__FILE__).'/eBayRequest.php');


// Checking compatibility with older PrestaShop and fixing it
if (!defined('_MYSQL_ENGINE_'))
	define('_MYSQL_ENGINE_', 'MyISAM');


class Ebay extends Module
{
	private $_html = '';
	private $_postErrors = array();
	private $_shippingMethod = array();
	private $_webserviceTestResult = '';
	private $_webserviceError = '';
	private $_fieldsList = array();
	private $_moduleName = 'ebay';
	private $id_lang;


	/******************************************************************/
	/** Construct Method **********************************************/
	/******************************************************************/

	public function __construct()
	{
		$this->name = 'ebay';
		$this->tab = 'market_place';
		$this->version = '1.3.5';
		$this->author = 'PrestaShop';
		parent::__construct ();
		$this->displayName = $this->l('eBay');
		$this->description = $this->l('Open your shop on the eBay market place !');
		$this->id_lang = Language::getIdByIso('fr');

		// Check the country and ask the bypass if not 'fr'
		if (strtolower($this->context->country->iso_code) != 'fr' && !isset($this->context->cookie->ebay_country_default_fr))
		{
			$this->warning = $this->l('eBay module currently works only for eBay.fr');
			return false;
		}

		// Checking Extension
		if (!extension_loaded('curl') || !ini_get('allow_url_fopen'))
		{
			if (!extension_loaded('curl') && !ini_get('allow_url_fopen'))
				$this->warning = $this->l('You must enable cURL extension and allow_url_fopen option on your server if you want to use this module.');
			else if (!extension_loaded('curl'))
				$this->warning = $this->l('You must enable cURL extension on your server if you want to use this module.');
			else if (!ini_get('allow_url_fopen'))
				$this->warning = $this->l('You must enable allow_url_fopen option on your server if you want to use this module.');
			return false;
		}

		// Checking compatibility with older PrestaShop and fixing it
		if (!Configuration::get('PS_SHOP_DOMAIN'))
			Configuration::updateValue('PS_SHOP_DOMAIN', $_SERVER['HTTP_HOST']);

		// Generate eBay Security Token if not exists
		if (!Configuration::get('EBAY_SECURITY_TOKEN'))
			Configuration::updateValue('EBAY_SECURITY_TOKEN', Tools::passwdGen(30));

		// For 1.4.3 and less compatibility
		$updateConfig = array('PS_OS_CHEQUE' => 1, 'PS_OS_PAYMENT' => 2, 'PS_OS_PREPARATION' => 3, 'PS_OS_SHIPPING' => 4, 'PS_OS_DELIVERED' => 5, 'PS_OS_CANCELED' => 6,
				      'PS_OS_REFUND' => 7, 'PS_OS_ERROR' => 8, 'PS_OS_OUTOFSTOCK' => 9, 'PS_OS_BANKWIRE' => 10, 'PS_OS_PAYPAL' => 11, 'PS_OS_WS_PAYMENT' => 12);
		foreach ($updateConfig as $u => $v)
			if (!Configuration::get($u) || (int)Configuration::get($u) < 1)
			{
				if (defined('_'.$u.'_') && (int)constant('_'.$u.'_') > 0)
					Configuration::updateValue($u, constant('_'.$u.'_'));
				else
					Configuration::updateValue($u, $v);
			}

		// Check if installed
		if (self::isInstalled($this->name))
		{
			// Upgrade eBay module
			if (Configuration::get('EBAY_VERSION') != $this->version)
				$this->upgrade();

			// Generate warnings
			if (!Configuration::get('EBAY_API_TOKEN'))
				$this->warning = $this->l('You must register your module on eBay.');

			// Loading Shipping Method
			$this->loadShippingMethod();

			// Warning uninstall
			$this->confirmUninstall = $this->l('Are you sure you want uninstall this module ? All your configuration will be lost.');
		}
	}


	public function loadShippingMethod()
	{
			// Shipping methods
			$this->_shippingMethod = array(
					7104 => array('description' => 'Colissimo', 'shippingService' => 'FR_ColiposteColissimo', 'shippingServiceID' => '7104'),
					7112 => array('description' => 'Ecopli', 'shippingService' => 'FR_Ecopli', 'shippingServiceID' => '7112'),
					57104 => array('description' => 'La Poste - Courrier International Prioritaire', 'shippingService' => 'FR_LaPosteInternationalPriorityCourier', 'shippingServiceID' => '57104'),
					7101 => array('description' => 'Lettre', 'shippingService' => 'FR_PostOfficeLetter', 'shippingServiceID' => '7101'),
					57105 => array('description' => 'La Poste - Courrier International Economique', 'shippingService' => 'FR_LaPosteInternationalEconomyCourier', 'shippingServiceID' => '57105'),
					57106 => array('description' => 'La Poste - Colissimo International', 'shippingService' => 'FR_LaPosteColissimoInternational', 'shippingServiceID' => '57106'),
					7102 => array('description' => 'Lettre avec suivi', 'shippingService' => 'FR_PostOfficeLetterFollowed', 'shippingServiceID' => '7102'),
					57107 => array('description' => 'La Poste - Colis Economique International', 'shippingService' => 'FR_LaPosteColisEconomiqueInternational', 'shippingServiceID' => '57107'),
					7103 => array('description' => 'Lettre recommand&eacute;e', 'shippingService' => 'FR_PostOfficeLetterRecommended', 'shippingServiceID' => '7103'),
					7121 => array('description' => 'Lettre Max', 'shippingService' => 'FR_LaPosteLetterMax', 'shippingServiceID' => '7121'),
					7113 => array('description' => 'Coli&eacute;co', 'shippingService' => 'FR_Colieco', 'shippingServiceID' => '7113'),
					57108 => array('description' => 'La Poste - Colissimo Emballage International', 'shippingService' => 'FR_LaPosteColissimoEmballageInternational', 'shippingServiceID' => '57108'),
					57114 => array('description' => 'Chronopost Express International', 'shippingService' => 'FR_ChronopostExpressInternational', 'shippingServiceID' => '57114'),
					7106 => array('description' => 'Colissimo Recommand&eacute;', 'shippingService' => 'FR_ColiposteColissimoRecommended', 'shippingServiceID' => '7106'),
					57109 => array('description' => 'Chronopost Classic International', 'shippingService' => 'FR_ChronopostClassicInternational', 'shippingServiceID' => '57109'),
					57110 => array('description' => 'Chronopost Premium International', 'shippingService' => 'FR_ChronopostPremiumInternational', 'shippingServiceID' => '57110'),
					7117 => array('description' => 'Chronopost - Chrono Relais', 'shippingService' => 'FR_ChronopostChronoRelais', 'shippingServiceID' => '7117'),
					57111 => array('description' => 'UPS Standard', 'shippingService' => 'FR_UPSStandardInternational', 'shippingServiceID' => '57111'),
					7111 => array('description' => 'Autre mode d\'envoi de courrier', 'shippingService' => 'FR_Autre', 'shippingServiceID' => '7111'),
					57112 => array('description' => 'UPS Express', 'shippingService' => 'FR_UPSExpressInternational', 'shippingServiceID' => '57112'),
					7114 => array('description' => 'Autre mode d\'envoi de colis', 'shippingService' => 'FR_AuteModeDenvoiDeColis', 'shippingServiceID' => '7114'),
					57113 => array('description' => 'DHL', 'shippingService' => 'FR_DHLInternational', 'shippingServiceID' => '57113'),
					57101 => array('description' => 'Frais de livraison internationale fixes', 'shippingService' => 'FR_StandardInternational', 'shippingServiceID' => '57101'),
					7116 => array('description' => 'Chronopost', 'shippingService' => 'FR_Chronopost', 'shippingServiceID' => '7116'),
					57102 => array('description' => 'Frais fixes pour livraison internationale express', 'shippingService' => 'FR_ExpeditedInternational', 'shippingServiceID' => '57102'),
					57103 => array('description' => 'Autres livraisons internationales (voir description)', 'shippingService' => 'FR_OtherInternational', 'shippingServiceID' => '57103'),
					7118 => array('description' => 'Chrono 10', 'shippingService' => 'FR_Chrono10', 'shippingServiceID' => '7118'),
					7119 => array('description' => 'Chrono 13', 'shippingService' => 'FR_Chrono13', 'shippingServiceID' => '7119'),
					7120 => array('description' => 'Chrono 18', 'shippingService' => 'FR_Chrono18', 'shippingServiceID' => '7120'),
					7105 => array('description' => 'Coliposte - Colissimo Direct', 'shippingService' => 'FR_ColiposteColissimoDirect', 'shippingServiceID' => '7105'),
					7107 => array('description' => 'Chronoposte - Chrono Classic International', 'shippingService' => 'FR_ChronoposteInternationalClassic', 'shippingServiceID' => '7107'),
					7108 => array('description' => 'DHL - Express Europack', 'shippingService' => 'FR_DHLExpressEuropack', 'shippingServiceID' => '7108'),
					7109 => array('description' => 'UPS - Standard', 'shippingService' => 'FR_UPSStandard', 'shippingServiceID' => '7109'),
			);
		}


	/******************************************************************/
	/** Install / Uninstall Methods ***********************************/
	/******************************************************************/

	public function install()
	{
		// Install SQL
		include(dirname(__FILE__).'/sql-install.php');
		foreach ($sql as $s)
			if (!Db::getInstance()->execute($s))
				return false;

		// Install Module
		if (!parent::install() ||
		    !$this->registerHook('addproduct') ||
		    !$this->registerHook('updateproduct') ||
		    !$this->registerHook('updateProductAttribute') ||
		    !$this->registerHook('deleteproduct') ||
		    !$this->registerHook('newOrder') ||
		    !$this->registerHook('backOfficeTop') ||
		    !$this->registerHook('backOfficeFooter') ||
		    !$this->registerHook('header') ||
		    !$this->registerHook('updateOrderStatus'))
			return false;

		// Generate Product Template
		$content = file_get_contents(dirname(__FILE__).'/template/ebay.tpl');
		$content = str_replace('{SHOP_NAME}', Configuration::get('PS_SHOP_NAME'), $content);
		$content = str_replace('{SHOP_URL}', 'http://'.Configuration::get('PS_SHOP_DOMAIN').'/'.__PS_BASE_URI__.'/', $content);
		$content = str_replace('{MODULE_URL}', 'http://'.Configuration::get('PS_SHOP_DOMAIN').'/'.__PS_BASE_URI__.'/modules/ebay/', $content);
		Configuration::updateValue('EBAY_PRODUCT_TEMPLATE', '');
		Configuration::updateValue('EBAY_PRODUCT_TEMPLATE', $content, true);

		// Init
		Configuration::updateValue('EBAY_VERSION', $this->version);

		return true;
	}

	public function uninstall()
	{
		// Uninstall Config
		foreach ($this->_fieldsList as $keyConfiguration => $name)
			if (!Configuration::deleteByName($keyConfiguration))
				return false;

		// Uninstall SQL
		include(dirname(__FILE__).'/sql-uninstall.php');
		foreach ($sql as $s)
			if (!Db::getInstance()->execute($s))
				return false;
		Configuration::deleteByName('EBAY_API_SESSION');
		Configuration::deleteByName('EBAY_API_USERNAME');
		Configuration::deleteByName('EBAY_API_TOKEN');
		Configuration::deleteByName('EBAY_IDENTIFIER');
		Configuration::deleteByName('EBAY_SHOP');
		Configuration::deleteByName('EBAY_PAYPAL_EMAIL');
		Configuration::deleteByName('EBAY_SHIPPING_CARRIER_ID');
		Configuration::deleteByName('EBAY_SHIPPING_COST');
		Configuration::deleteByName('EBAY_SHIPPING_COST_CURRENCY');
		Configuration::deleteByName('EBAY_SHOP_POSTALCODE');
		Configuration::deleteByName('EBAY_CATEGORY_LOADED');
		Configuration::deleteByName('EBAY_CATEGORY_LOADED_DATE');
		Configuration::deleteByName('EBAY_PRODUCT_TEMPLATE');
		Configuration::deleteByName('EBAY_SYNC_MODE');
		Configuration::deleteByName('EBAY_ORDER_LAST_UPDATE');
		Configuration::deleteByName('EBAY_VERSION');
		Configuration::deleteByName('EBAY_SECURITY_TOKEN');

		// Uninstall Module
		if (!parent::uninstall() ||
		    !$this->unregisterHook('addproduct') ||
		    !$this->unregisterHook('updateproduct') ||
		    !$this->unregisterHook('updateProductAttribute') ||
                    !$this->unregisterHook('deleteproduct') ||
                    !$this->unregisterHook('newOrder') ||
		    !$this->unregisterHook('backOfficeTop') ||
		    !$this->unregisterHook('backOfficeFooter') ||
		    !$this->unregisterHook('header') ||
		    !$this->unregisterHook('updateOrderStatus'))
			return false;

		// Clean Cookie
		$this->context->cookie->eBaySession = '';
		$this->context->cookie->eBayUsername = '';

		return true;
	}

	public function upgrade()
	{
		$version = Configuration::get('EBAY_VERSION');
		if ($version == '1.1' || empty($version))
		{
			// Upgrade SQL
			include(dirname(__FILE__).'/sql-upgrade-1-2.php');
			foreach ($sql as $s)
				if (!Db::getInstance()->execute($s))
					return false;
			Configuration::updateValue('EBAY_VERSION', $this->version);
		}
	}


	/******************************************************************/
	/** Hook Methods **************************************************/
	/******************************************************************/

	public function hookNewOrder($params)
	{
		if ((int)$params['cart']->id < 1)
			return false;

		$sql = '`id_product` IN (SELECT `id_product` FROM `'._DB_PREFIX_.'cart_product` WHERE `id_cart` = '.(int)$params['cart']->id.')';
		if (Configuration::get('EBAY_SYNC_MODE') == 'A')
		{
			// Retrieve product list for eBay (which have matched categories) AND Send each product on eBay
			$productsList = Db::getInstance()->executeS('SELECT `id_product` FROM `'._DB_PREFIX_.'product` WHERE '.$sql.' AND `active` = 1 AND `id_category_default` IN (SELECT `id_category` FROM `'._DB_PREFIX_.'ebay_category_configuration` WHERE `id_category` > 0 AND `id_ebay_category` > 0)');
			foreach ($productList as $k => $v)
				$productList[$k]['noPriceUpdate'] = 1;
			if ($productsList)
				$this->_syncProducts($productsList);
		}
		else if (Configuration::get('EBAY_SYNC_MODE') == 'B')
		{
			// Select the sync Categories and Retrieve product list for eBay (which have matched and sync categories) AND Send each product on eBay
			$productsList = Db::getInstance()->executeS('SELECT `id_product` FROM `'._DB_PREFIX_.'product` WHERE '.$sql.' AND `active` = 1 AND `id_category_default` IN (SELECT `id_category` FROM `'._DB_PREFIX_.'ebay_category_configuration` WHERE `id_category` > 0 AND `id_ebay_category` > 0 AND `sync` = 1)');
			foreach ($productList as $k => $v)
				$productList[$k]['noPriceUpdate'] = 1;
			if ($productsList)
				$this->_syncProducts($productsList);
		}

	}

	public function hookaddproduct($params)
	{
		if (!isset($params['product']->id))
			return false;
		$id_product = $params['product']->id;
		if ((int)$id_product < 1)
			return false;

		if (Configuration::get('EBAY_SYNC_MODE') == 'A')
		{
			// Retrieve product list for eBay (which have matched categories) AND Send each product on eBay
			$productsList = Db::getInstance()->executeS('SELECT `id_product` FROM `'._DB_PREFIX_.'product` WHERE `id_product` = '.(int)$id_product.' AND `active` = 1 AND `id_category_default` IN (SELECT `id_category` FROM `'._DB_PREFIX_.'ebay_category_configuration` WHERE `id_category` > 0 AND `id_ebay_category` > 0)');
			if ($productsList)
				$this->_syncProducts($productsList);
		}
		else if (Configuration::get('EBAY_SYNC_MODE') == 'B')
		{
			// Select the sync Categories and Retrieve product list for eBay (which have matched and sync categories) AND Send each product on eBay
			$productsList = Db::getInstance()->executeS('SELECT `id_product` FROM `'._DB_PREFIX_.'product` WHERE `id_product` = '.(int)$id_product.' AND `active` = 1 AND `id_category_default` IN (SELECT `id_category` FROM `'._DB_PREFIX_.'ebay_category_configuration` WHERE `id_category` > 0 AND `id_ebay_category` > 0 AND `sync` = 1)');
			if ($productsList)
				$this->_syncProducts($productsList);
		}
	}

	public function hookbackOfficeTop($params)
	{
		// Check if the module is configured
		if (!Configuration::get('EBAY_PAYPAL_EMAIL'))
			return false;

		// Fix hook update product attribute
		$this->hookupdateProductAttributeEbay();

		// If no update yet
		if (!Configuration::get('EBAY_ORDER_LAST_UPDATE'))
			Configuration::updateValue('EBAY_ORDER_LAST_UPDATE', date('Y-m-d').'T'.date('H:i:s').'.000Z');

		// init Var
		$dateNew = date('Y-m-d').'T'.date('H:i:s').'.000Z';
		if (Configuration::get('EBAY_ORDER_LAST_UPDATE') < date('Y-m-d', strtotime('-30 minutes')).'T'.date('H:i:s', strtotime('-30 minutes')).'.000Z')
		{
			// Lock
			Configuration::updateValue('EBAY_ORDER_LAST_UPDATE', $dateNew);

			// eBay Request
			$ebay = new eBayRequest();

			$page = 1;
			$orderList = array();
			$orderCount = 0;
			$orderCountTmp = 100;
			while ($orderCountTmp == 100 && $page < 10)
			{
				$orderListTmp = $ebay->getOrders(date('Y-m-d', strtotime('-30 days')).'T'.date('H:i:s', strtotime('-30 days')).'.000Z', $dateNew, $page);
				$orderCountTmp = count($orderListTmp);
				$orderList = array_merge((array)$orderList, (array)$orderListTmp);
				$orderCount += $orderCountTmp;
				$page++;
			}

			if ($orderList)
			{
				foreach ($orderList as $korder => $order)
				{
					if ($order['status'] == 'Complete' && $order['amount'] > 0.1 && isset($order['product_list']) && count($order['product_list']))
					{
						if (!Db::getInstance()->getValue('SELECT `id_ebay_order` FROM `'._DB_PREFIX_.'ebay_order` WHERE `id_order_ref` = \''.pSQL($order['id_order_ref']).'\''))
						{
							// Check for empty name
							$order['firstname'] = trim($order['firstname']);
							$order['familyname'] = trim($order['familyname']);
							if (empty($order['familyname']))
								$order['familyname'] = $order['firstname'];
							if (empty($order['firstname']))
								$order['firstname'] = $order['familyname'];
							if (empty($order['phone']) || !Validate::isPhoneNumber($order['phone']))
								$order['phone'] = '0100000000';

							if (Validate::isEmail($order['email']) && !empty($order['firstname']) && !empty($order['familyname']))
							{
								// Getting the customer
								$id_customer = (int)Db::getInstance()->getValue('SELECT `id_customer` FROM `'._DB_PREFIX_.'customer` WHERE `active` = 1 AND `email` = \''.pSQL($order['email']).'\' AND `deleted` = 0'.(substr(_PS_VERSION_, 0, 3) == '1.3' ? '' : ' AND `is_guest` = 0'));

						// Add customer if he doesn't exist
						if ($id_customer < 1)
						{
							$customer = new Customer();
							$customer->id_gender = 0;
							$customer->id_default_group = 1;
							$customer->secure_key = md5(uniqid(rand(), true));
							$customer->email = $order['email'];
							$customer->passwd = md5(pSQL(_COOKIE_KEY_.rand()));
							$customer->last_passwd_gen = pSQL(date('Y-m-d H:i:s'));
							$customer->newsletter = 0;
							$customer->lastname = pSQL($order['familyname']);
							$customer->firstname = pSQL($order['firstname']);
							$customer->active = 1;
							$customer->add();
							$id_customer = $customer->id;
						}

								// Search if address exists
								$id_address = (int)Db::getInstance()->getValue('SELECT `id_address` FROM `'._DB_PREFIX_.'address` WHERE `id_customer` = '.(int)$id_customer.' AND `alias` = \'eBay\'');
								if ($id_address > 0)
									$address = new Address((int)$id_address);
								else
								{
						$address = new Address();
						$address->id_customer = (int)$id_customer;
								}
						$address->id_country = (int)Country::getByIso($order['country_iso_code']);
								$address->alias = 'eBay';
						$address->lastname = pSQL($order['familyname']);
						$address->firstname = pSQL($order['firstname']);
						$address->address1 = pSQL($order['address1']);
						$address->address2 = pSQL($order['address2']);
						$address->postcode = pSQL($order['postalcode']);
						$address->city = pSQL($order['city']);
						$address->phone = pSQL($order['phone']);
						$address->active = 1;
								if ($id_address > 0 && Validate::isLoadedObject($address))
									$address->update();
								else
						$address->add();
						$id_address = $address->id;

						$flag = 1;
						foreach ($order['product_list'] as $product)
						{
							if ((int)$product['id_product'] < 1 || !Db::getInstance()->getValue('SELECT `id_product` FROM `'._DB_PREFIX_.'product` WHERE `id_product` = '.(int)$product['id_product']))
								$flag = 0;
									if (isset($product['id_product_attribute']) && $product['id_product_attribute'] > 0 && !Db::getInstance()->getValue('SELECT `id_product_attribute` FROM `'._DB_PREFIX_.'product_attribute` WHERE `id_product` = '.(int)$product['id_product'].' AND `id_product_attribute` = '.(int)$product['id_product_attribute']))
								$flag = 0;
						}

						if ($flag == 1)
						{
									$cartNbProducts = 0;
	 						$cartAdd = new Cart();
							$cartAdd->id_customer = $id_customer;
							$cartAdd->id_address_invoice = $id_address;
							$cartAdd->id_address_delivery = $id_address;
									$cartAdd->id_carrier = 0;
							$cartAdd->id_lang = $this->id_lang;
							$cartAdd->id_currency = Currency::getIdByIsoCode('EUR');
									$cartAdd->recyclable = 0;
									$cartAdd->gift = 0;
		 					$cartAdd->add();
							foreach ($order['product_list'] as $product)
										if ($cartAdd->updateQty((int)($product['quantity']), (int)($product['id_product']), ((isset($product['id_product_attribute']) && $product['id_product_attribute'] > 0) ? $product['id_product_attribute'] : NULL)))
											$cartNbProducts++;
							$cartAdd->update();

									// Check number of products in the cart
									if ($cartNbProducts > 0 && !Db::getInstance()->getValue('SELECT `id_ebay_order` FROM `'._DB_PREFIX_.'ebay_order` WHERE `id_order_ref` = \''.pSQL($order['id_order_ref']).'\''))
									{
							// Fix on sending e-mail
							Db::getInstance()->autoExecute(_DB_PREFIX_.'customer', array('email' => 'NOSEND-EBAY'), 'UPDATE', '`id_customer` = '.(int)$id_customer);
							$customerClear = new Customer();
							if (method_exists($customerClear, 'clearCache'))
								$customerClear->clearCache(true);

							// Validate order
							$paiement = new eBayPayment();
										$paiement->validateOrder(intval($cartAdd->id), Configuration::get('PS_OS_PAYMENT'), floatval($cartAdd->getOrderTotal(true, 3)), 'eBay '.$order['payment_method'].' '.$order['id_order_seller'], NULL, array(), intval($cartAdd->id_currency));
							$id_order = $paiement->currentOrder;

										// Fix on date
										Db::getInstance()->autoExecute(_DB_PREFIX_.'orders', array('date_add' => pSQL($order['date_add'])), 'UPDATE', '`id_order` = '.(int)$id_order);

							// Fix on sending e-mail
							Db::getInstance()->autoExecute(_DB_PREFIX_.'customer', array('email' => pSQL($order['email'])), 'UPDATE', '`id_customer` = '.(int)$id_customer);

							// Update price (because of possibility of price impact)
										foreach ($order['product_list'] as $product)
										{
											$tax_rate = Db::getInstance()->getValue('SELECT `tax_rate` FROM `'._DB_PREFIX_.'order_detail` WHERE `id_order` = '.(int)$id_order.' AND `product_id` = '.(int)$product['id_product'].' AND `product_attribute_id` = '.(int)$product['id_product_attribute']);
											Db::getInstance()->autoExecute(_DB_PREFIX_.'order_detail', array('product_price' => floatval($product['price'] / (1 + ($tax_rate / 100))), 'reduction_percent' => 0), 'UPDATE', '`id_order` = '.(int)$id_order.' AND `product_id` = '.(int)$product['id_product'].' AND `product_attribute_id` = '.(int)$product['id_product_attribute']);
										}
							$updateOrder = array(
								'total_paid' => floatval($order['amount']),
								'total_paid_real' => floatval($order['amount']),
											'total_products' => floatval(Db::getInstance()->getValue('SELECT SUM(`product_price`) FROM `'._DB_PREFIX_.'order_detail` WHERE `id_order` = '.(int)$id_order)),
											'total_products_wt' => floatval($order['amount'] - $order['shippingServiceCost']),
								'total_shipping' => floatval($order['shippingServiceCost']),
							);
							Db::getInstance()->autoExecute(_DB_PREFIX_.'orders', $updateOrder, 'UPDATE', '`id_order` = '.(int)$id_order);

									// Register the ebay order ref
									Db::getInstance()->autoExecute(_DB_PREFIX_.'ebay_order', array('id_order_ref' => pSQL($order['id_order_ref']), 'id_order' => (int)$id_order), 'INSERT');
						}
									else
									{
										$cartAdd->delete();
										$orderList[$korder]['errors'][] = $this->l('Could not add product to cart (maybe your stock quantity is 0)');
					}
								}
								else
									$orderList[$korder]['errors'][] = $this->l('Could not found products in database');
						}
							else
								$orderList[$korder]['errors'][] = $this->l('Invalid e-mail');
					}
						else
							$orderList[$korder]['errors'][] = $this->l('Order already imported');
					}
					else
						$orderList[$korder]['errors'][] = $this->l('Status not complete or amount less than 0.1 or no product matching');
		}
				file_put_contents(dirname(__FILE__).'/log/orders.php', "<?php\n\n".'$dateLastImport = '."'".date('d/m/Y H:i:s')."';\n\n".'$orderList = '.var_export($orderList, true).";\n\n");
	}
		}
	}


	public function hookupdateOrderStatus($params)
	{
	}


	// Alias
	public function hookupdateproduct($params) { $this->hookaddproduct($params); }
	public function hookupdateProductAttribute($params) { }
	public function hookupdateProductAttributeEbay()
	{
		if (isset($_POST['submitProductAttribute']) && isset($_POST['id_product_attribute']))
		{
			$params = array();
			$params['id_product_attribute'] = (int)$_POST['id_product_attribute'];
			if ($params['id_product_attribute'] > 0)
			{
		$id_product = Db::getInstance()->getValue('SELECT `id_product` FROM `'._DB_PREFIX_.'product_attribute` WHERE `id_product_attribute` = '.(int)$params['id_product_attribute']);
		$params['product'] = new Product($id_product);
		$this->hookaddproduct($params);
	}
		}
	}
	public function hookdeleteproduct($params) { $this->hookaddproduct($params); }
	public function hookheader($params) { $this->hookbackOfficeTop($params); }
	public function hookbackOfficeFooter($params) { $this->hookbackOfficeTop($params); }


	/******************************************************************/
	/** Main Form Methods *********************************************/
	/******************************************************************/

	public function getContent()
	{
		$this->_html .= '<h2>' . $this->l('eBay').'</h2>';

		// Checking Country
		if (Tools::getValue('ebay_country_default_fr') == 'ok')
			$this->context->cookie->ebay_country_default_fr = true;
		if (strtolower($this->context->country->iso_code) != 'fr' && !isset($this->context->cookie->ebay_country_default_fr))
			return $this->_html.$this->displayError($this->l('eBay module currently works only for eBay.fr').'. <a href="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'&ebay_country_default_fr=ok">'.$this->l('Continue anyway ?').'</a>');


		// Checking Extension
		if (!extension_loaded('curl') || !ini_get('allow_url_fopen'))
		{
			if (!extension_loaded('curl') && !ini_get('allow_url_fopen'))
				return $this->_html.$this->displayError($this->l('You must enable cURL extension and allow_url_fopen option on your server if you want to use this module.'));
			else if (!extension_loaded('curl'))
				return $this->_html.$this->displayError($this->l('You must enable cURL extension on your server if you want to use this module.'));
			else if (!ini_get('allow_url_fopen'))
				return $this->_html.$this->displayError($this->l('You must enable allow_url_fopen option on your server if you want to use this module.'));
		}


		// If isset Post Var, post process else display form
		if (!empty($_POST) && (Tools::isSubmit('submitSave') || Tools::isSubmit('submitSave1') || Tools::isSubmit('submitSave2')))
		{
			$this->_postValidation();
			if (!sizeof($this->_postErrors))
				$this->_postProcess();
			else
				foreach ($this->_postErrors AS $err)
					$this->_html .= '<div class="alert error"><img src="../modules/ebay/forbbiden.gif" alt="nok" />&nbsp;'.$err.'</div>';
		}
		$this->_displayForm();
		return $this->_html;
	}

	private function _displayForm()
	{
		// Test alert
		$alert = array();
		if (!Configuration::get('EBAY_API_TOKEN'))
			$alert['registration'] = 1;
		if (!ini_get('allow_url_fopen'))
			$alert['allowurlfopen'] = 1;
		if (!extension_loaded('curl'))
			$alert['curl'] = 1;


		// Displaying Information from Prestashop
		$stream_context = @stream_context_create(array('http' => array('method'=>"GET", 'timeout' => 2)));
		$prestashopContent = @file_get_contents('http://www.prestashop.com/partner/modules/ebay.php?version='.$this->version.'&shop='.urlencode(Configuration::get('PS_SHOP_NAME')).'&registered='.($alert['registration'] == 1 ? 'no' : 'yes').'&url='.urlencode($_SERVER['HTTP_HOST']).'&iso_country='.$isoCountry.'&iso_lang='.Tools::strtolower($isoUser).'&id_lang='.(int)$this->context->language->id.'&email='.urlencode(Configuration::get('PS_SHOP_EMAIL')).'&security='.md5(Configuration::get('PS_SHOP_EMAIL')._COOKIE_IV_), false, $stream_context);
		if (!Validate::isCleanHtml($prestashopContent))
			$prestashopContent = '';

		// Displaying page
		$this->_html .= '<fieldset>
		<legend><img src="'.$this->_path.'logo.gif" alt="" /> '.$this->l('eBay Module Status').'</legend>';
		$this->_html .= '<div style="float: left; width: 45%">';
		if (!count($alert))
			$this->_html .= '<img src="../modules/ebay/valid.png" /><strong>'.$this->l('eBay Module is configured and online!').'</strong>';
		else
		{
			$this->_html .= '<img src="../modules/ebay/warn.png" /><strong>'.$this->l('eBay Module is not configured yet, you must:').'</strong>';
			$this->_html .= '<br />'.(isset($alert['registration']) ? '<img src="../modules/ebay/warn.png" />' : '<img src="../modules/ebay/valid.png" />').' 1) '.$this->l('Register the module on eBay');
			$this->_html .= '<br />'.(isset($alert['allowurlfopen']) ? '<img src="../modules/ebay/warn.png" />' : '<img src="../modules/ebay/valid.png" />').' 2) '.$this->l('Allow url fopen');
			$this->_html .= '<br />'.(isset($alert['curl']) ? '<img src="../modules/ebay/warn.png" />' : '<img src="../modules/ebay/valid.png" />').' 3) '.$this->l('Enable cURL');
		}
		$this->_html .= '</div><div style="float: right; width: 45%">'.$prestashopContent.'</div>';


		$this->_html .= '</fieldset><div class="clear">&nbsp;</div>';

		if (!Configuration::get('EBAY_API_TOKEN'))
			$this->_html .= $this->_displayFormRegister();
		else
			$this->_html .= $this->_displayFormConfig();
	}

	private function _postValidation()
	{
		if (!Configuration::get('EBAY_API_TOKEN'))
			$this->_postValidationRegister();
		else if (Tools::getValue('section') == 'parameters')
			$this->_postValidationParameters();
		else if (Tools::getValue('section') == 'category')
			$this->_postValidationCategory();
		else if (Tools::getValue('section') == 'template')
			$this->_postValidationTemplateManager();
		else if (Tools::getValue('section') == 'sync')
			$this->_postValidationEbaySync();
	}

	private function _postProcess()
	{
		if (!Configuration::get('EBAY_API_TOKEN'))
			$this->_postProcessRegister();
		else if (Tools::getValue('section') == 'parameters')
			$this->_postProcessParameters();
		else if (Tools::getValue('section') == 'category')
			$this->_postProcessCategory();
		else if (Tools::getValue('section') == 'template')
			$this->_postProcessTemplateManager();
		else if (Tools::getValue('section') == 'sync')
			$this->_postProcessEbaySync();
	}


	/******************************************************************/
	/** Register Form Config Methods **********************************/
	/******************************************************************/

	private function _displayFormRegister()
	{
		$ebay = new eBayRequest();

		if (!empty($this->context->cookie->eBaySession) && isset($_GET['action']) && $_GET['action'] == 'logged')
		{
			if (isset($_POST['eBayUsername']))
			{
				$this->context->cookie->eBayUsername = $_POST['eBayUsername'];
				Configuration::updateValue('EBAY_API_USERNAME', $_POST['eBayUsername']);
			}
			$ebay->session = $this->context->cookie->eBaySession;
			$ebay->username = $this->context->cookie->eBayUsername;

			$html = '
			<script>
				function checkToken()
				{
					$.ajax({
					  url: \''._MODULE_DIR_.'ebay/ajax/checkToken.php?token='.Configuration::get('EBAY_SECURITY_TOKEN').'&time='.pSQL(date('Ymdhis')).'\',
					  success: function(data)
					  {
						if (data == \'OK\')
							window.location.href = \'index.php?tab='.Tools::safeOutput($_GET['tab']).'&configure='.Tools::safeOutput($_GET['configure']).'&token='.Tools::safeOutput($_GET['token']).'&tab_module='.Tools::safeOutput($_GET['tab_module']).'&module_name='.Tools::safeOutput($_GET['module_name']).'&action=validateToken\';
						else
							setTimeout ("checkToken()", 5000);
					  }
					});
				}
				checkToken();
			</script>';
			$html .= '<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Register the module on eBay').'</legend>';
			$html .= '<p align="center"><img src="'.$this->_path.'loading.gif" alt="'.$this->l('Loading').'" title="'.$this->l('Loading').'" /></p>';
			$html .= '<p align="center">'.$this->l('Once you sign in from the new eBay window, the module will automatically finish the installation a few seconds later').'</p>';
			$html .= '</fieldset>';
		}
		else
		{
			if (empty($this->context->cookie->eBaySession))
			{
				$ebay->login();
				$this->context->cookie->eBaySession = $ebay->session;
				Configuration::updateValue('EBAY_API_SESSION', $ebay->session);
			}

			$html = '
			<style>#button_ebay{background-image:url('.$this->_path.'ebay.gif);background-repeat:no-repeat;background-position:center 100px;width:385px;height:191px;cursor:pointer;padding-bottom:70px;font-weight:bold;font-size:22px;}</style>
			<script>
				$(document).ready(function() {
					$(\'#button_ebay\').click(function() {
						if ($(\'#eBayUsername\').val() == \'\')
						{
							alert(\''.$this->l('You have to set your eBay User ID').'\');
							return false;
						}
						else
							window.open(\''.$ebay->getLoginUrl().'?SignIn&runame='.$ebay->runame.'&SessID='.$this->context->cookie->eBaySession.'\');
					});
				});
			</script>
			<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'&action=logged" method="post">
				<fieldset>
					<legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Register the module on eBay').'</legend>
					<label>'.$this->l('Click on the button below').'</label>
					<div class="margin-form">
						<br class="clear"/>
						<label for="eBayUsername">'.$this->l('eBay User ID').'&nbsp;&nbsp;</label><input id="eBayUsername" type="text" name="eBayUsername" value="'.$this->context->cookie->eBayUsername.'" />
						<br class="clear"/><br />
						<input type="submit" id="button_ebay" class="button" value="'.$this->l('Register the module on eBay').'" />
					</div>
				</fieldset>
			</form>';
		}

		return $html;
	}

	private function _postValidationRegister()
	{

	}

	private function _postProcessRegister()
	{

	}


	/******************************************************************/
	/** Parameters Form Config Methods ********************************/
	/******************************************************************/

	private function _displayFormConfig()
	{
		$html = '
		<ul id="menuTab">
				<li id="menuTab1" class="menuTabButton selected">1. '.$this->l('Parameters').'</li>
				<li id="menuTab2" class="menuTabButton">2. '.$this->l('Categories settings').'</li>
				<li id="menuTab3" class="menuTabButton">3. '.$this->l('Template manager').'</li>
				<li id="menuTab4" class="menuTabButton">4. '.$this->l('eBay Sync').'</li>
				<li id="menuTab5" class="menuTabButton">5. '.$this->l('Orders history').'</li>
				<li id="menuTab6" class="menuTabButton">6. '.$this->l('Help').'</li>
			</ul>
			<div id="tabList">
				<div id="menuTab1Sheet" class="tabItem selected">'.$this->_displayFormParameters().'</div>
				<div id="menuTab2Sheet" class="tabItem">'.$this->_displayFormCategory().'</div>
				<div id="menuTab3Sheet" class="tabItem">'.$this->_displayFormTemplateManager().'</div>
				<div id="menuTab4Sheet" class="tabItem">'.$this->_displayFormEbaySync().'</div>
				<div id="menuTab5Sheet" class="tabItem">'.$this->_displayOrdersHistory().'</div>
				<div id="menuTab6Sheet" class="tabItem">'.$this->_displayHelp().'</div>
			</div>
			<br clear="left" />
			<br />
			<style>
				#menuTab { float: left; padding: 0; margin: 0; text-align: left; }
				#menuTab li { text-align: left; float: left; display: inline; padding: 5px; padding-right: 10px; background: #EFEFEF; font-weight: bold; cursor: pointer; border-left: 1px solid #EFEFEF; border-right: 1px solid #EFEFEF; border-top: 1px solid #EFEFEF; }
				#menuTab li.menuTabButton.selected { background: #FFF6D3; border-left: 1px solid #CCCCCC; border-right: 1px solid #CCCCCC; border-top: 1px solid #CCCCCC; }
				#tabList { clear: left; }
				.tabItem { display: none; }
				.tabItem.selected { display: block; background: #FFFFF0; border: 1px solid #CCCCCC; padding: 10px; padding-top: 20px; }
			</style>
			<script>
				$(".menuTabButton").click(function () {
				  $(".menuTabButton.selected").removeClass("selected");
				  $(this).addClass("selected");
				  $(".tabItem.selected").removeClass("selected");
				  $("#" + this.id + "Sheet").addClass("selected");
				});
			</script>
		';
		if (isset($_GET['id_tab']))
			$html .= '<script>
				  $(".menuTabButton.selected").removeClass("selected");
				  $("#menuTab'.Tools::safeOutput($_GET['id_tab']).'").addClass("selected");
				  $(".tabItem.selected").removeClass("selected");
				  $("#menuTab'.Tools::safeOutput($_GET['id_tab']).'Sheet").addClass("selected");
			</script>';
		return $html;
	}

	private function _displayFormParameters()
	{
		// Loading config currency
		$configCurrency = new Currency((int)(Configuration::get('PS_CURRENCY_DEFAULT')));


		// Display Form
		$html = '<form action="index.php?tab='.Tools::safeOutput($_GET['tab']).'&configure='.Tools::safeOutput($_GET['configure']).'&token='.Tools::safeOutput($_GET['token']).'&tab_module='.Tools::safeOutput($_GET['tab_module']).'&module_name='.Tools::safeOutput($_GET['module_name']).'&id_tab=1&section=parameters" method="post" class="form" id="configForm1">
				<fieldset style="border: 0">
					<h4>'.$this->l('To export your products on eBay, you have to create a pro account on eBay (see Help) and configure your eBay-Prestashop module.').'</h4>
					<label>'.$this->l('eBay Identifier').' : </label>
					<div class="margin-form">
						<input type="text" size="20" name="ebay_identifier" value="'.Tools::safeOutput(Tools::getValue('ebay_identifier', Configuration::get('EBAY_IDENTIFIER'))).'" />
						<p>'.(Configuration::get('EBAY_IDENTIFIER') ? '<a href="http://shop.ebay.fr/'.Configuration::get('EBAY_IDENTIFIER').'/m.html?_ipg=50&_sop=12&_rdc=1" target="_blank">'.$this->l('Your products on eBay').'</a>' : $this->l('Your eBay identifier')).'</p>
					</div>
					<label>'.$this->l('eBay shop').' : </label>
					<div class="margin-form">
						<input type="text" size="20" name="ebay_shop" value="'.Tools::safeOutput(Tools::getValue('ebay_shop', Configuration::get('EBAY_SHOP'))).'" />
						<p>'.(Configuration::get('EBAY_SHOP') ? '<a href="http://stores.ebay.fr/'.Configuration::get('EBAY_SHOP').'" target="_blank">'.$this->l('Your shop on eBay').'</a>' : $this->l('Your eBay shop name')).'</p>
					</div>
					<label>'.$this->l('Paypal Identifier (e-mail)').' : </label>
					<div class="margin-form">
						<input type="text" size="20" name="ebay_paypal_email" value="'.Tools::safeOutput(Tools::getValue('ebay_paypal_email', Configuration::get('EBAY_PAYPAL_EMAIL'))).'" />
						<p>'.$this->l('You have to set your PayPal e-mail account, it\'s the only payment available with this module').'</p>
					</div>
					<label>'.$this->l('Shipping method').' : </label>
					<div class="margin-form">
						<select name="ebay_shipping_carrier_id">';
					foreach ($this->_shippingMethod as $id => $val)
						$html .= '<option value="'.$id.'" '.(Tools::getValue('ebay_shipping_carrier_id', Configuration::get('EBAY_SHIPPING_CARRIER_ID')) == $id ? 'selected="selected"' : '').'>'.$val['description'].'</option>';
		$html .= '			</select>
						<p>'.$this->l('Shipping cost configuration for your products on eBay').'</p>
					</div>
					<label>'.$this->l('Shipping cost').' : </label>
					<div class="margin-form">
						<input type="text" size="20" name="ebay_shipping_cost" value="'.Tools::safeOutput(Tools::getValue('ebay_shipping_cost', Configuration::get('EBAY_SHIPPING_COST'))).'" /> '.$configCurrency->sign.'
						<p>'.$this->l('Shipping cost configuration for your products on eBay').'</p>
					</div>
					<label>'.$this->l('Shop postal code').' : </label>
					<div class="margin-form">
						<input type="text" size="20" name="ebay_shop_postalcode" value="'.Tools::safeOutput(Tools::getValue('ebay_shop_postalcode', Configuration::get('EBAY_SHOP_POSTALCODE'))).'" />
						<p>'.$this->l('Your shop\'s postal code').'</p>
					</div>
				</fieldset>
				<div class="margin-form" id="buttonEbayParameters"><input class="button" name="submitSave" type="submit" id="save_ebay_parameters" value="'.$this->l('Save').'" /></div>
				<div class="margin-form" id="categoriesProgression" style="font-weight: bold;"></div>
			</form>';

		if (!Configuration::get('EBAY_CATEGORY_LOADED'))
		{
			$html .= '
			<script>
				percent = 0;
				function checkCategories()
				{
					percent++;
					if (percent > 100)
						percent = 100;
					$("#categoriesProgression").html("'.$this->l('Categories loading').' : " + percent + " %");
					if (percent < 100)
						setTimeout ("checkCategories()", 1000);
				}
				$(document).ready(function() {
					$("#save_ebay_parameters").click(function() {
						$("#buttonEbayParameters").hide();
						checkCategories();
					});
				});
			</script>';
		}

		return $html;
	}

	private function _postValidationParameters()
	{
		// Check configuration values
		if (Tools::getValue('ebay_identifier') == NULL)
			$this->_postErrors[]  = $this->l('Your eBay identifier account is not specified or is invalid');
		if (Tools::getValue('ebay_paypal_email') == NULL OR !Validate::isEmail(Tools::getValue('ebay_paypal_email')))
			$this->_postErrors[]  = $this->l('Your Paypal E-mail account is not specified or is invalid');
		if (Tools::getValue('ebay_shipping_cost') == '' OR !is_numeric(Tools::getValue('ebay_shipping_cost')))
			$this->_postErrors[]  = $this->l('Your shipping cost is not specified or is invalid');
		if (Tools::getValue('ebay_shop_postalcode') == '' OR !Validate::isPostCode(Tools::getValue('ebay_shop_postalcode')))
			$this->_postErrors[]  = $this->l('Your shop\'s postal code is not specified or is invalid');
	}

	private function _postProcessParameters()
	{
		// Saving new configurations
		if (Configuration::updateValue('EBAY_PAYPAL_EMAIL', pSQL(Tools::getValue('ebay_paypal_email'))) &&
		    Configuration::updateValue('EBAY_IDENTIFIER', pSQL(Tools::getValue('ebay_identifier'))) &&
		    Configuration::updateValue('EBAY_SHOP', pSQL(Tools::getValue('ebay_shop'))) &&
		    Configuration::updateValue('EBAY_SHIPPING_COST', (float)(Tools::getValue('ebay_shipping_cost'))) &&
		    Configuration::updateValue('EBAY_SHIPPING_COST_CURRENCY', (int)(Configuration::get('PS_CURRENCY_DEFAULT'))) &&
		    Configuration::updateValue('EBAY_SHIPPING_CARRIER_ID', pSQL(Tools::getValue('ebay_shipping_carrier_id'))) &&
		    Configuration::updateValue('EBAY_SHOP_POSTALCODE', pSQL(Tools::getValue('ebay_shop_postalcode'))))
			$this->_html .= $this->displayConfirmation($this->l('Settings updated'));
		else
			$this->_html .= $this->displayError($this->l('Settings failed'));
	}


	/******************************************************************/
	/** Category Form Config Methods **********************************/
	/******************************************************************/

	private function _getChildCategories($categories, $id, $path = array(), $pathAdd = '')
	{
		$categoryTmp = array();
		$categoryTab = array();
		if ($pathAdd != '')
			$path[] = $pathAdd;
		if (isset($categories[$id]))
			foreach ($categories[$id] as $idc => $cc)
			{
				$name = '';
				if ($path)
					foreach ($path as $p)
						$name .= $p.' > ';
				$name .= $cc['infos']['name'];
				$categoryTab[] = array('id_category' => $cc['infos']['id_category'], 'name' => $name);
				$categoryTmp = $this->_getChildCategories($categories, $idc, $path, $cc['infos']['name']);
				$categoryTab = array_merge($categoryTab, $categoryTmp);
			}
		return $categoryTab;
	}

	private function _displayFormCategory()
	{
		// Check if the module is configured
		if (!Configuration::get('EBAY_PAYPAL_EMAIL'))
			return '<p><b>'.$this->l('You have to configure "General Settings" tab before using this tab.').'</b></p><br />';

		// Load categories only if necessary
		if (Db::getInstance()->getValue('SELECT COUNT(`id_ebay_category_configuration`) FROM `'._DB_PREFIX_.'ebay_category_configuration`') >= 1 && Tools::getValue('section') != 'category')
			return '<p align="center"><b>'.$this->l('Your categories have already been configured.').'</b></p>
			<form action="index.php?tab='.Tools::safeOutput($_GET['tab']).'&configure='.Tools::safeOutput($_GET['configure']).'&token='.Tools::safeOutput($_GET['token']).'&tab_module='.Tools::safeOutput($_GET['tab_module']).'&module_name='.Tools::safeOutput($_GET['module_name']).'&id_tab=2&section=category" method="post" class="form">
			<p align="center"><input class="button" name="submitSave" type="submit" value="'.$this->l('See Categories').'" /></p></form>';

		// Display eBay Categories
		if (!Configuration::get('EBAY_CATEGORY_LOADED'))
		{
			$ebay = new eBayRequest();
			$ebay->saveCategories();
			Configuration::updateValue('EBAY_CATEGORY_LOADED', 1);
			Configuration::updateValue('EBAY_CATEGORY_LOADED_DATE', date('Y-m-d H:i:s'));
		}


		// Loading categories
		$categoryConfigList = array();
		$categoryConfigListTmp = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'ebay_category_configuration`');
		foreach ($categoryConfigListTmp as $c)
			$categoryConfigList[$c['id_category']] = $c;
		$categoryList = $this->_getChildCategories(Category::getCategories($this->context->language->id), 0);
		$eBayCategoryList = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'ebay_category` WHERE `id_category_ref` = `id_category_ref_parent`');


		// Display header
		$html = '<p><b>'.$this->l('To export your products on eBay, you have to associate each one of your shop categories to an eBay category. You can also define an impact of your price on eBay.').'</b></p><br />

		<form action="index.php?tab='.Tools::safeOutput($_GET['tab']).'&configure='.Tools::safeOutput($_GET['configure']).'&token='.Tools::safeOutput($_GET['token']).'&tab_module='.Tools::safeOutput($_GET['tab_module']).'&module_name='.Tools::safeOutput($_GET['module_name']).'&id_tab=2&section=category&action=suggestCategories" method="post" class="form" id="configForm2SuggestedCategories">
			<p><b>'.$this->l('You can use the button below to associate automatically the categories which have no association for the moment with an eBay suggested category.').'</b>
			<input class="button" name="submitSave" type="submit" value="'.$this->l('Suggest Categories').'" />
			</p><br />
		</form>

		<form action="index.php?tab='.Tools::safeOutput($_GET['tab']).'&configure='.Tools::safeOutput($_GET['configure']).'&token='.Tools::safeOutput($_GET['token']).'&tab_module='.Tools::safeOutput($_GET['tab_module']).'&module_name='.Tools::safeOutput($_GET['module_name']).'&id_tab=2&section=category" method="post" class="form" id="configForm2">
		<table class="table tableDnD" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr class="nodrag nodrop">
					<th style="width:120px">'.$this->l('Category').'</th>
					<th>'.$this->l('eBay Category').'</th>
					<th style="width:80px">'.$this->l('Price impact').'</th>
				</tr>
			</thead>
			<tbody>';


		// Displaying categories
		if (!$categoryList)
			$html .= '<tr><td colspan="4">'.$this->l('No category found.').'</td></tr>';
		foreach ($categoryList as $k => $c)
		{
			// Display line
			$alt = 0;
			if ($k % 2 != 0)
				$alt = ' class="alt_row"';
			$html .= '<tr'.$alt.'>
				<td>'.$c['name'].'</td>
				<td id="categoryPath'.$c['id_category'].'">
					<select name="category'.$c['id_category'].'" id="categoryLevel1-'.$c['id_category'].'" rel="'.$c['id_category'].'" style="font-size: 12px; width: 160px;" OnChange="changeCategoryMatch(1, '.$c['id_category'].');">
						<option value="0">'.$this->l('No category selected').'</option>';
						foreach ($eBayCategoryList as $ec)
							$html .= '<option value="'.$ec['id_ebay_category'].'">'.$ec['name'].($ec['is_multi_sku'] == 1 ? ' *' : '').'</option>';
					$html .= '</select>';
					if (isset($categoryConfigList[$c['id_category']]))
						$html .= '<script>$(document).ready(function() { loadCategoryMatch('.$c['id_category'].'); });</script>';
				$html .= '</td>
				<td><select name="percent'.$c['id_category'].'" id="percent'.$c['id_category'].'" rel="'.$c['id_category'].'" style="font-size: 12px;">';
			for ($i = 5; $i >= -80; $i--)
				$html .= '<option value="'.$i.'" '.((isset($categoryConfigList[$c['id_category']]) && $categoryConfigList[$c['id_category']]['percent'] == $i) || (!isset($categoryConfigList[$c['id_category']]) && $i == 0) ? 'selected="selected"' : '').'>'.(($i >= 0) ? $i : '- '.($i * -1)).' %</option>';
			$html .= '</select></td></tr>';
		}

		$html .= '
			</tbody>
		</table><br />
		<div class="margin-form"><input class="button" name="submitSave" type="submit" value="'.$this->l('Save').'" /></div>
		</form>

		<p><b>'.$this->l('Beware : Only product default categories are used for this configuration.').'</b></p><br />

		<p align="left">
			* Certaines catÃ©gories bÃ©nÃ©ficient du nouveau format dâ€™annonces multi-versions qui permet de publier 1 seule annonce pour plusieurs versions du mÃªme produit.<br />
			Pour les catÃ©gories ne bÃ©nÃ©ficiant pas de ce format multi-versions, une annonce sera crÃ©Ã©e pour chaque version du produit.<br />
			<a href="http://sellerupdate.ebay.fr/may2011/multi-variation-listings.html" target="_blank">Cliquez ici pour plus dâ€™informations sur les catÃ©gories multi-versions</a>
		</p><br /><br />

		<script>
			function loadCategoryMatch(id_category)
			{
				$.ajax({
				  async: false,
				  url: "'._MODULE_DIR_.'ebay/ajax/loadCategoryMatch.php?token='.Configuration::get('EBAY_SECURITY_TOKEN').'&id_category=" + id_category + "&time='.pSQL(date('Ymdhis')).'",
				  success: function(data) { $("#categoryPath" + id_category).html(data); }
				});
			}
			function changeCategoryMatch(level, id_category)
			{
				var levelParams = "&level1=" + $("#categoryLevel1-" + id_category).val();
				if (level > 1) levelParams += "&level2=" + $("#categoryLevel2-" + id_category).val();
				if (level > 2) levelParams += "&level3=" + $("#categoryLevel3-" + id_category).val();
				if (level > 3) levelParams += "&level4=" + $("#categoryLevel4-" + id_category).val();
				if (level > 4) levelParams += "&level5=" + $("#categoryLevel5-" + id_category).val();

				$.ajax({
				  url: "'._MODULE_DIR_.'ebay/ajax/changeCategoryMatch.php?token='.Configuration::get('EBAY_SECURITY_TOKEN').'&time='.pSQL(date('Ymdhis')).'&id_category=" + id_category + "&level=" + level + levelParams,
				  success: function(data) { $("#categoryPath" + id_category).html(data); }
				});
			}
		</script>';


		return $html;
	}

	private function _postValidationCategory()
	{
	}

	private function _postProcessCategory()
	{
		// Init Var
		$date = date('Y-m-d H:i:s');
		$services = Tools::getValue('service');

		if (Tools::getValue('action') == 'suggestCategories')
		{
			// Loading categories
			$ebay = new eBayRequest();
			$categoryConfigList = array();
			$categoryConfigListTmp = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'ebay_category_configuration`');
			foreach ($categoryConfigListTmp as $c)
				$categoryConfigList[$c['id_category']] = $c;
			$categoryList = Db::getInstance()->executeS('SELECT `id_category`, `name` FROM `'._DB_PREFIX_.'category_lang` WHERE `id_lang` = '.(int)$this->id_lang.$this->context->shop->addSqlRestrictionOnLang('cl'));

			foreach ($categoryList as $k => $c)
				if (!isset($categoryConfigList[$c['id_category']]))
				{
					$productTest = Db::getInstance()->getRow('
					SELECT pl.`name`, pl.`description`
					FROM `'._DB_PREFIX_.'product` p LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.`id_product` = p.`id_product` AND pl.`id_lang` = '.(int)$this->id_lang.$this->context->shop->addSqlRestrictionOnLang('pl').')
					WHERE `id_category_default` = '.(int)$c['id_category']);
					$id_category_ref_suggested = $ebay->getSuggestedCategories($c['name'].' '.$productTest['name']);
					$id_ebay_category_suggested = Db::getInstance()->getValue('SELECT `id_ebay_category` FROM `'._DB_PREFIX_.'ebay_category` WHERE `id_category_ref` = '.(int)$id_category_ref_suggested);
					if ((int)$id_ebay_category_suggested > 0)
						Db::getInstance()->autoExecute(_DB_PREFIX_.'ebay_category_configuration', array('id_country' => 8, 'id_ebay_category' => (int)$id_ebay_category_suggested, 'id_category' => (int)$c['id_category'], 'percent' => 0, 'date_add' => pSQL($date), 'date_upd' => pSQL($date)), 'INSERT');
				}

			$this->_html .= $this->displayConfirmation($this->l('Settings updated'));
			return true;
		}


		// Sort post datas
		$postValue = array();
		foreach ($_POST as $k => $v)
		{
			if (strlen($k) > 8 && substr($k, 0, 8) == 'category')
				$postValue[substr($k, 8, strlen($k) - 8)]['id_ebay_category'] = $v;
			if (strlen($k) > 7 && substr($k, 0, 7) == 'percent')
				$postValue[substr($k, 7, strlen($k) - 7)]['percent'] = $v;
		}

		// Insert and update configuration
		foreach ($postValue as $id_category => $tab)
		{
			$arraySQL = array();
			$date = date('Y-m-d H:i:s');
			if ($tab['id_ebay_category'])
				$arraySQL = array('id_country' => 8, 'id_ebay_category' => (int)$tab['id_ebay_category'], 'id_category' => (int)$id_category, 'percent' => pSQL($tab['percent']), 'date_upd' => pSQL($date));
			$id_ebay_category_configuration = Db::getInstance()->getValue('SELECT `id_ebay_category_configuration` FROM `'._DB_PREFIX_.'ebay_category_configuration` WHERE `id_category` = '.(int)$id_category);
			if ($id_ebay_category_configuration > 0)
			{
				if ($arraySQL)
					Db::getInstance()->autoExecute(_DB_PREFIX_.'ebay_category_configuration', $arraySQL, 'UPDATE', '`id_category` = '.(int)$id_category);
				else
					Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ebay_category_configuration` WHERE `id_category` = '.(int)$id_category);
			}
			elseif ($arraySQL)
			{
				$arraySQL['date_add'] = $date;
				Db::getInstance()->autoExecute(_DB_PREFIX_.'ebay_category_configuration', $arraySQL, 'INSERT');
			}
		}

		$this->_html .= $this->displayConfirmation($this->l('Settings updated'));
	}



	/******************************************************************/
	/** Template Manager Form Config Methods **************************/
	/******************************************************************/

	private function _displayFormTemplateManager()
	{
		// Check if the module is configured
		if (!Configuration::get('EBAY_PAYPAL_EMAIL'))
			return '<p><b>'.$this->l('You have to configure "General Settings" tab before using this tab.').'</b></p><br />';

		$iso = $context->language->iso_code;
		$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
		$ad = dirname($_SERVER["PHP_SELF"]);

		// Display Form
		$forbiddenJs = array('textarea', 'script', 'onmousedown', 'onmousemove', 'onmmouseup', 'onmouseover', 'onmouseout', 'onload', 'onunload', 'onfocus', 'onblur', 'onchange', 'onsubmit', 'ondblclick', 'onclick', 'onkeydown', 'onkeyup', 'onkeypress', 'onmouseenter', 'onmouseleave', 'onerror');
		$html = '<form action="index.php?tab='.Tools::safeOutput($_GET['tab']).'&configure='.Tools::safeOutput($_GET['configure']).'&token='.Tools::safeOutput($_GET['token']).'&tab_module='.Tools::safeOutput($_GET['tab_module']).'&module_name='.Tools::safeOutput($_GET['module_name']).'&id_tab=3&section=template" method="post" class="form" id="configForm3">
				<fieldset style="border: 0">
					<h4>'.$this->l('You can customise the template for your products page on eBay').' :</h4>
					<textarea class="rte" cols="100" rows="50" name="ebay_product_template">'.str_replace($forbiddenJs, '', Tools::getValue('ebay_product_template', Configuration::get('EBAY_PRODUCT_TEMPLATE'))).'</textarea><br />

					'.(substr(_PS_VERSION_, 0, 3) == '1.3' ? '
					<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
					<script type="text/javascript">
					tinyMCE.init({
						mode : "textareas",
						theme : "advanced",
						plugins : "safari,pagebreak,style,layer,table,advimage,advlink,inlinepopups,media,searchreplace,contextmenu,paste,directionality,fullscreen",
						// Theme options
						theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
						theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,,|,forecolor,backcolor",
						theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,media,|,ltr,rtl,|,fullscreen",
						theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,pagebreak",
						theme_advanced_toolbar_location : "top",
						theme_advanced_toolbar_align : "left",
						theme_advanced_statusbar_location : "bottom",
						theme_advanced_resizing : false,
						content_css : "'.__PS_BASE_URI__.'themes/'._THEME_NAME_.'/css/global.css",
						document_base_url : "'.__PS_BASE_URI__.'",
						width: "850",
						height: "800",
						font_size_style_values : "8pt, 10pt, 12pt, 14pt, 18pt, 24pt, 36pt",
						// Drop lists for link/image/media/template dialogs
						template_external_list_url : "lists/template_list.js",
						external_link_list_url : "lists/link_list.js",
						external_image_list_url : "lists/image_list.js",
						media_external_list_url : "lists/media_list.js",
						elements : "nourlconvert",
						convert_urls : false,
						language : "'.(file_exists(_PS_ROOT_DIR_.'/js/tinymce/jscripts/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en').'"
					});
					</script>
					' : '
					<script type="text/javascript">
					var iso = \''.$isoTinyMCE.'\';
					var pathCSS = \''._THEME_CSS_DIR_.'\';
					var ad = \''.$ad.'\';
					</script>
					<script type="text/javascript" src="'._PS_JS_DIR_.'/tiny_mce/tiny_mce.js"></script>
					<script type="text/javascript" src="'._PS_JS_DIR_.'/tinymce.inc.js"></script>
					<script>
						tinyMCE.settings.width = 850;
						tinyMCE.settings.height = 800;
						tinyMCE.settings.extended_valid_elements = "iframe[id|class|title|style|align|frameborder|height|longdesc|marginheight|marginwidth|name|scrolling|src|width]";
						tinyMCE.settings.extended_valid_elements = "link[href|type|rel|id|class|title|style|align|frameborder|height|longdesc|marginheight|marginwidth|name|scrolling|src|width]";
					</script>').'

				</fieldset>
				<div class="margin-form"><input class="button" name="submitSave" value="'.$this->l('Save').'" type="submit"></div>
			</form>';


		return $html;
	}

	private function _postValidationTemplateManager()
	{
	}

	private function _postProcessTemplateManager()
	{
		// Saving new configurations
		if (Configuration::updateValue('EBAY_PRODUCT_TEMPLATE', Tools::getValue('ebay_product_template'), true))
			$this->_html .= $this->displayConfirmation($this->l('Settings updated'));
		else
			$this->_html .= $this->displayError($this->l('Settings failed'));
	}



	/******************************************************************/
	/** Ebay Sync Form Config Methods **************************/
	/******************************************************************/

	private function _displayFormEbaySync()
	{
		// Check if the module is configured
		if (!Configuration::get('EBAY_PAYPAL_EMAIL'))
			return '<p><b>'.$this->l('You have to configure "General Settings" tab before using this tab.').'</b></p><br />';
		if (Db::getInstance()->getValue('SELECT COUNT(`id_ebay_category_configuration`) as nb FROM `'._DB_PREFIX_.'ebay_category_configuration`') < 1)
			return '<p><b>'.$this->l('You have to configure "Categories Settings" tab before using this tab.').'</b></p><br />';

		$nbProductsModeA = Db::getInstance()->getValue('
		SELECT COUNT(`id_product`) as nb
		FROM `'._DB_PREFIX_.'product`
		WHERE `quantity` > 0 AND `active` = 1
		AND `id_category_default` IN (SELECT `id_category` FROM `'._DB_PREFIX_.'ebay_category_configuration` WHERE `id_ebay_category` > 0)');
		$nbProductsModeB = Db::getInstance()->getValue('
		SELECT COUNT(`id_product`) as nb
		FROM `'._DB_PREFIX_.'product`
		WHERE `quantity` > 0 AND `active` = 1
		AND `id_category_default` IN (SELECT `id_category` FROM `'._DB_PREFIX_.'ebay_category_configuration` WHERE `id_ebay_category` > 0 AND `sync` = 1)');

		$nbProducts = $nbProductsModeA;
		if (Configuration::get('EBAY_SYNC_MODE') == 'B')
			$nbProducts = $nbProductsModeB;


		// Display Form
		$html = '<style>
			#button_ebay_sync1{background-image:url('.$this->_path.'ebay.gif);background-repeat:no-repeat;background-position:center 90px;width:400px;height:191px;cursor:pointer;padding-bottom:100px;font-weight:bold;font-size:25px;}
			#button_ebay_sync2{background-image:url('.$this->_path.'ebay.gif);background-repeat:no-repeat;background-position:center 90px;width:400px;height:191px;cursor:pointer;padding-bottom:100px;font-weight:bold;font-size:25px;}
		</style>
		<script>
			var nbProducts = '.$nbProducts.';
			var nbProductsModeA = '.$nbProductsModeA.';
			var nbProductsModeB = '.$nbProductsModeB.';
			$(document).ready(function() {
				$(".categorySync").click(function() {

					var params = "";
					if ($(this).attr("value") > 0)
						params = "&id_category=" + $(this).attr("value");
					if ($(this).attr("checked"))
						params = params + "&action=1";
					else
						params = params + "&action=0";

					$.ajax({
						url: "'._MODULE_DIR_.'ebay/ajax/getNbProductsSync.php?token='.Configuration::get('EBAY_SECURITY_TOKEN').'&time='.pSQL(date('Ymdhis')).'" + params,
						success: function(data) {
					  		nbProducts = data;
					  		nbProductsModeB = data;
							$("#button_ebay_sync1").attr("value", "'.$this->l('Sync with eBay').'\n(" + data + " '.$this->l('products').')");
							$("#button_ebay_sync2").attr("value", "'.$this->l('Sync with eBay').'\n'.$this->l('and update').'\n(" + data + " '.$this->l('products').')");
						}
					});
				});
			});


			$(document).ready(function() {
				$("#ebay_sync_mode1").click(function() {
					nbProducts = nbProductsModeA;
					$("#catSync").hide("slow");
					$("#button_ebay_sync1").attr("value", "'.$this->l('Sync with eBay').'\n(" + nbProducts + " '.$this->l('products').')");
					$("#button_ebay_sync2").attr("value", "'.$this->l('Sync with eBay').'\n'.$this->l('and update').'\n(" + nbProducts + " '.$this->l('products').')");
				});
				$("#ebay_sync_mode2").click(function() {
					nbProducts = nbProductsModeB;
					$("#catSync").show("slow");
					$("#button_ebay_sync1").attr("value", "'.$this->l('Sync with eBay').'\n(" + nbProducts + " '.$this->l('products').')");
					$("#button_ebay_sync2").attr("value", "'.$this->l('Sync with eBay').'\n'.$this->l('and update').'\n(" + nbProducts + " '.$this->l('products').')");
				});
			});

			function eBaySync(option)
			{
				$(".categorySync").attr("disabled", "true");
				$("#ebay_sync_mode1").attr("disabled", "true");
				$("#ebay_sync_mode2").attr("disabled", "true");
				$("#ebay_sync_option_resync").attr("disabled", "true");
				$("#button_ebay_sync1").attr("disabled", "true");
				$("#button_ebay_sync1").css("background-color", "#D5D5D5");
				$("#button_ebay_sync2").attr("disabled", "true");
				$("#button_ebay_sync2").css("background-color", "#D5D5D5");
				$("#resultSync").html("<img src=\"../modules/ebay/loading-small.gif\" border=\"0\" />");
				eBaySyncProduct(option);
			}

			var counter = 0;
			function eBaySyncProduct(option)
			{
				counter++;
				$.ajax({
				  url: \''._MODULE_DIR_.'ebay/ajax/eBaySyncProduct.php?token='.Configuration::get('EBAY_SECURITY_TOKEN').'&option=\'+option+\'&time='.pSQL(date('Ymdhis')).'\'+counter,
				  success: function(data)
				  {
					tab = data.split("|");
					$("#resultSync").html(tab[1]);
					if (tab[0] != "OK")
						eBaySyncProduct(option);
				  }
				});
			}
		</script>

		<div id="resultSync" style="text-align: center; font-weight: bold; font-size: 14px;"></div>

		<form action="index.php?tab='.Tools::safeOutput($_GET['tab']).'&configure='.Tools::safeOutput($_GET['configure']).'&token='.Tools::safeOutput($_GET['token']).'&tab_module='.Tools::safeOutput($_GET['tab_module']).'&module_name='.Tools::safeOutput($_GET['module_name']).'&id_tab=4&section=sync" method="post" class="form" id="configForm4">
				<fieldset style="border: 0">
					<h4>'.$this->l('You will now push your products on eBay.').' <b>'.$this->l('Reminder,').'</b> '.$this->l('you will not have to pay any fees if you have a shop on eBay.').'</h4>
					<label style="width: 250px;">'.$this->l('Sync Mode').' : </label><br clear="left" /><br /><br />
					<div class="margin-form">
						<input type="radio" size="20" name="ebay_sync_mode" id="ebay_sync_mode1" value="A" checked="checked" /> '.$this->l('Option A').' : '.$this->l('Sync all your products with eBay').'
					</div>
					<div class="margin-form">
						<input type="radio" size="20" name="ebay_sync_mode" id="ebay_sync_mode2" value="B" /> '.$this->l('Option B').' : '.$this->l('Sync the products only from selected categories').'
					</div>
					<label style="width: 250px;">'.$this->l('Option').' : </label><br clear="left" /><br /><br />
					<div class="margin-form">
						<input type="checkbox" size="20" name="ebay_sync_option_resync" id="ebay_sync_option_resync" value="1" '.(Configuration::get('EBAY_SYNC_OPTION_RESYNC') == 1 ? 'checked="checked"' : '').' /> '.$this->l('When update a product, resync only price and quantity').'
					</div>
					<div style="display: none;" id="catSync">';


		// Loading categories
		$categoryConfigList = array();
		$categoryConfigListTmp = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'ebay_category_configuration`');
		foreach ($categoryConfigListTmp as $c)
			$categoryConfigList[$c['id_category']] = $c;
		$categoryList = $this->_getChildCategories(Category::getCategories($this->context->language->id), 0);
		$html .= '<table class="table tableDnD" cellpadding="0" cellspacing="0" width="90%">
			<thead>
				<tr class="nodrag nodrop">
					<th>'.$this->l('Select').'</th>
					<th>'.$this->l('Category').'</th>
				</tr>
			</thead>
			<tbody>';
			if (!$categoryList)
			$html .= '<tr><td colspan="2">'.$this->l('No category found.').'</td></tr>';
			$i = 0;
			foreach ($categoryList as $k => $c)
			{
				// Display line
				$alt = 0;
				if ($i % 2 != 0)
					$alt = ' class="alt_row"';
				if (isset($categoryConfigList[$c['id_category']]['id_ebay_category']) && $categoryConfigList[$c['id_category']]['id_ebay_category'] > 0)
				{
					$html .= '<tr'.$alt.'><td><input type="checkbox" class="categorySync" name="category[]" value="'.$c['id_category'].'" '.($categoryConfigList[$c['id_category']]['sync'] == 1 ? 'checked="checked"' : '').' /><td>'.$c['name'].'</td></tr>';
					$i++;
				}
			}
			$html .= '</tbody></table>';
			if (Tools::getValue('section') == 'sync' && Tools::getValue('submitSave1') != '')
				$html .= '<script>$(document).ready(function() { eBaySync(1); });</script>';
			if (Tools::getValue('section') == 'sync' && Tools::getValue('submitSave2') != '')
				$html .= '<script>$(document).ready(function() { eBaySync(2); });</script>';

			if (Configuration::get('EBAY_SYNC_MODE') == 'B')
			{
				$html .= '<script>
					$(document).ready(function() {
						$("#catSync").show("slow");
						$("#ebay_sync_mode2").attr("checked", true);
					});
				</script>';
			}

			$html .= '
					</div>
				</fieldset>
				<h4>'.$this->l('Beware ! If some of your categories are not multi sku compliant, some of your products may create more than one product on eBay.').'</h4>

				<table>
					<tr>
						<td style="color: #268CCD"><h4>'.$this->l('"Sync with eBay" option will only sync the products that are not already sync with eBay : ').'</h4></td>
						<td style="width: 50px">&nbsp;</td>
						<td style="color: #268CCD"><h4>'.$this->l('"Sync and update with eBay" will sync the products that are not already sync with eBay and update the products already sync with eBay : ').'</h4></td>
					</tr>
					<tr>
						<td><input id="button_ebay_sync1" class="button" name="submitSave1" value="'.$this->l('Sync with eBay')."\n".'('.$nbProducts.' '.$this->l('products').')" OnClick="return confirm(\''.$this->l('You will push').' \' + nbProducts + \' '.$this->l('products on eBay. Do you want to confirm ?').'\');" type="submit"></td>
						<td style="width: 50px">&nbsp;</td>
						<td><input id="button_ebay_sync2" class="button" name="submitSave2" value="'.$this->l('Sync with eBay')."\n".$this->l('and update')."\n".'('.$nbProducts.' '.$this->l('products').')" OnClick="return confirm(\''.$this->l('You will push').' \' + nbProducts + \' '.$this->l('products on eBay. Do you want to confirm ?').'\');" type="submit"></td>
					</tr>
				</table>
				<br />
			</form>';


		return $html;
	}

	private function _postValidationEbaySync()
	{
	}

	private function _postProcessEbaySync()
	{
		// Update Sync Option
		Configuration::updateValue('EBAY_SYNC_OPTION_RESYNC', (Tools::getValue('ebay_sync_option_resync') == 1 ? 1 : 0));

		// Empty error result
		Configuration::updateValue('EBAY_SYNC_LAST_PRODUCT', 0);
		@unlink(dirname(__FILE__).'/log/syncError.php');

		if ($_POST['ebay_sync_mode'] == 'A')
		{
			// Update Sync Mod
			Configuration::updateValue('EBAY_SYNC_MODE', 'A');
		}
		else
		{
			// Update Sync Mod
			Configuration::updateValue('EBAY_SYNC_MODE', 'B');

			// Select the sync Categories and Retrieve product list for eBay (which have matched and sync categories)
			Db::getInstance()->autoExecute(_DB_PREFIX_.'ebay_category_configuration', array('sync' => 0), 'UPDATE', '');
			foreach ($_POST['category'] as $id_category)
				Db::getInstance()->autoExecute(_DB_PREFIX_.'ebay_category_configuration', array('sync' => 1), 'UPDATE', '`id_category` = '.(int)$id_category);
		}
	}

	public function ajaxProductSync()
	{
		$whereOption1 = 'AND `id_product` NOT IN (SELECT `id_product` FROM `'._DB_PREFIX_.'ebay_product`)';

		if (Configuration::get('EBAY_SYNC_MODE') == 'A')
		{
			// Retrieve total nb products for eBay (which have matched categories)
			$nbProductsTotal = Db::getInstance()->getValue('
			SELECT COUNT(`id_product`)
			FROM `'._DB_PREFIX_.'product`
			WHERE `quantity` > 0 AND `active` = 1
			AND `id_category_default` IN (SELECT `id_category` FROM `'._DB_PREFIX_.'ebay_category_configuration` WHERE `id_category` > 0 AND `id_ebay_category` > 0)');

			// Retrieve products list for eBay (which have matched categories)
			$productsList = Db::getInstance()->executeS('
			SELECT `id_product` FROM `'._DB_PREFIX_.'product`
			WHERE `quantity` > 0 AND `active` = 1
			AND `id_category_default` IN (SELECT `id_category` FROM `'._DB_PREFIX_.'ebay_category_configuration` WHERE `id_category` > 0 AND `id_ebay_category` > 0)
			'.(Tools::getValue('option') == 1 ? $whereOption1 : '').'
			AND `id_product` > '.(int)Configuration::get('EBAY_SYNC_LAST_PRODUCT').'
			ORDER BY `id_product`
			LIMIT 1');

			// How Many Product Less ?
			$nbProductsLess = Db::getInstance()->getValue('
			SELECT COUNT(`id_product`) FROM `'._DB_PREFIX_.'product`
			WHERE `quantity` > 0 AND `active` = 1
			AND `id_category_default` IN (SELECT `id_category` FROM `'._DB_PREFIX_.'ebay_category_configuration` WHERE `id_category` > 0 AND `id_ebay_category` > 0)
			'.(Tools::getValue('option') == 1 ? $whereOption1 : '').'
			AND `id_product` > '.(int)Configuration::get('EBAY_SYNC_LAST_PRODUCT'));
		}
		else
		{
			// Retrieve total nb products for eBay (which have matched categories)
			$nbProductsTotal = Db::getInstance()->getValue('
			SELECT COUNT(`id_product`)
			FROM `'._DB_PREFIX_.'product`
			WHERE `quantity` > 0 AND `active` = 1
			AND `id_category_default` IN (SELECT `id_category` FROM `'._DB_PREFIX_.'ebay_category_configuration` WHERE `id_category` > 0 AND `id_ebay_category` > 0 AND `sync` = 1)');

			// Retrieve products list for eBay (which have matched categories)
			$productsList = Db::getInstance()->executeS('
			SELECT `id_product` FROM `'._DB_PREFIX_.'product`
			WHERE `quantity` > 0 AND `active` = 1
			AND `id_category_default` IN (SELECT `id_category` FROM `'._DB_PREFIX_.'ebay_category_configuration` WHERE `id_category` > 0 AND `id_ebay_category` > 0 AND `sync` = 1)
			'.(Tools::getValue('option') == 1 ? $whereOption1 : '').'
			AND `id_product` > '.(int)Configuration::get('EBAY_SYNC_LAST_PRODUCT').'
			ORDER BY `id_product`
			LIMIT 1');

			// How Many Product Less ?
			$nbProductsLess = Db::getInstance()->getValue('
			SELECT COUNT(`id_product`) FROM `'._DB_PREFIX_.'product`
			WHERE `quantity` > 0 AND `active` = 1
			AND `id_category_default` IN (SELECT `id_category` FROM `'._DB_PREFIX_.'ebay_category_configuration` WHERE `id_category` > 0 AND `id_ebay_category` > 0 AND `sync` = 1)
			'.(Tools::getValue('option') == 1 ? $whereOption1 : '').'
			AND `id_product` > '.(int)Configuration::get('EBAY_SYNC_LAST_PRODUCT'));
		}

			// Send each product on eBay
		if (count($productsList) >= 1)
		{
			// Save the last product
			Configuration::updateValue('EBAY_SYNC_LAST_PRODUCT', (int)$productsList[0]['id_product']);

			// Sync product
			$this->_syncProducts($productsList);

			echo 'KO|<br /><br /> <img src="../modules/ebay/loading-small.gif" border="0" /> Produits : '.($nbProductsTotal - $nbProductsLess + 1).' / '.$nbProductsTotal.'<br /><br />';
		}
		else
		{
			echo 'OK|'.$this->displayConfirmation($this->l('Settings updated').' ('.$this->l('Option').' '.Configuration::get('EBAY_SYNC_MODE').' : '.($nbProductsTotal - $nbProductsLess).' / '.$nbProductsTotal.' '.$this->l('product(s) sync with eBay').')');
			if (file_exists(dirname(__FILE__).'/log/syncError.php'))
			{
				global $tab_error;
				include(dirname(__FILE__).'/log/syncError.php');
				foreach ($tab_error as $error)
				{
					$productsDetails = '<br /><u>'.$this->l('Product(s) concerned').' :</u>';
					foreach ($error['products'] as $product)
						$productsDetails .= '<br />- '.$product;
					echo $this->displayError($error['msg'].'<br />'.$productsDetails);
	}
				echo '<style>#content .alert { text-align: left; width: 875px; }</style>';
				@unlink(dirname(__FILE__).'/log/syncError.php');
			}
		}
	}

	public function findIfCategoryParentIsMultiSku($id_category_ref)
	{
		$row = Db::getInstance()->getRow('SELECT `id_category_ref_parent`, `is_multi_sku` FROM `'._DB_PREFIX_.'ebay_category` WHERE `id_category_ref` = '.(int)$id_category_ref);
		if ($row['id_category_ref_parent'] != $id_category_ref)
			return $this->findIfCategoryParentIsMultiSku($row['id_category_ref_parent']);
		return $row['is_multi_sku'];
	}

	private function _syncProducts($productsList)
	{
		$fees = 0;
		$count = 0;
		$count_success = 0;
		$count_error = 0;
		$tab_error = array();
		$date = date('Y-m-d H:i:s');
		$ebay = new eBayRequest();
		$categoryDefaultCache = array();

		// Get errors back
		if (file_exists(dirname(__FILE__).'/log/syncError.php'))
		{
			global $tab_error;
			include(dirname(__FILE__).'/log/syncError.php');
		}

		// Up the time limit
		@set_time_limit(3600);

		// Run the products list
		foreach ($productsList as $p)
		{
			// Product instanciation
			$product = new Product((int)$p['id_product'], true, $this->id_lang);
			if (Validate::isLoadedObject($product) && $product->id_category_default > 0)
			{
				// Load default category matched in cache
				if (!isset($categoryDefaultCache[$product->id_category_default]))
					$categoryDefaultCache[$product->id_category_default] = Db::getInstance()->getRow('SELECT ec.`id_category_ref`, ec.`is_multi_sku`, ecc.`percent` FROM `'._DB_PREFIX_.'ebay_category` ec LEFT JOIN `'._DB_PREFIX_.'ebay_category_configuration` ecc ON (ecc.`id_ebay_category` = ec.`id_ebay_category`) WHERE ecc.`id_category` = '.(int)$product->id_category_default);
				if ($categoryDefaultCache[$product->id_category_default]['is_multi_sku'] != 1)
					$categoryDefaultCache[$product->id_category_default]['is_multi_sku'] = $this->findIfCategoryParentIsMultiSku($categoryDefaultCache[$product->id_category_default]['id_category_ref']);

				// Load Pictures
				$pictures = array();
				$picturesMedium = array();
				$picturesLarge = array();
				$prefix = (substr(_PS_VERSION_, 0, 3) == '1.3' ? 'http://'.Configuration::get('PS_SHOP_DOMAIN').'/' : '');
				$images = $product->getImages($this->id_lang);
				foreach ($images as $image)
				{
					$pictures[] = str_replace('https://', 'http://', $prefix.$this->context->link->getImageLink('ebay', $product->id.'-'.$image['id_image'], 'large'));
					$picturesMedium[] = str_replace('https://', 'http://', $prefix.$this->context->link->getImageLink('ebay', $product->id.'-'.$image['id_image'], 'medium'));
					$picturesLarge[] = str_replace('https://', 'http://', $prefix.$this->context->link->getImageLink('ebay', $product->id.'-'.$image['id_image'], 'large'));
				}

				// Load Variations
				$variations = array();
				$variationsList = array();
				$combinations = $product->getAttributeCombinaisons($this->id_lang);
				if (isset($combinations))
					foreach ($combinations as $c)
					{
						$variationsList[$c['group_name']][$c['attribute_name']] = 1;
						$variations[$c['id_product'].'-'.$c['id_product_attribute']]['id_attribute'] = $c['id_product_attribute'];
						$variations[$c['id_product'].'-'.$c['id_product_attribute']]['reference'] = $c['reference'];
						$variations[$c['id_product'].'-'.$c['id_product_attribute']]['quantity'] = $c['quantity'];
						$variations[$c['id_product'].'-'.$c['id_product_attribute']]['variations'][] = array('name' => $c['group_name'], 'value' => $c['attribute_name']);
						$variations[$c['id_product'].'-'.$c['id_product_attribute']]['price_static'] = Product::getPriceStatic((int)$c['id_product'], true, (int)$c['id_product_attribute']);

						$price = $variations[$c['id_product'].'-'.$c['id_product_attribute']]['price_static'];
						$price_original = $price;
						if ($categoryDefaultCache[$product->id_category_default]['percent'] > 0)
							$price *= (1 + ($categoryDefaultCache[$product->id_category_default]['percent'] / 100));
						else if ($categoryDefaultCache[$product->id_category_default]['percent'] < 0)
							$price *= (1 - ($categoryDefaultCache[$product->id_category_default]['percent'] / (-100)));

						$variations[$c['id_product'].'-'.$c['id_product_attribute']]['price'] = round($price, 2);
						if ($categoryDefaultCache[$product->id_category_default]['percent'] < 0)
						{
							$variations[$c['id_product'].'-'.$c['id_product_attribute']]['price_original'] = round($price_original, 2);
							$variations[$c['id_product'].'-'.$c['id_product_attribute']]['price_percent'] = round($categoryDefaultCache[$product->id_category_default]['percent']);
						}
					}

				// Load Variations Pictures
				$combinationsImages = $product->getCombinationImages(2);
				if (isset($combinationsImages) && !empty($combinationsImages) && count($combinationsImages) > 0)
					foreach ($combinationsImages as $ci)
						foreach ($ci as $i)
							$variations[$product->id.'-'.$i['id_product_attribute']]['pictures'][] = $prefix.$this->context->link->getImageLink('', $product->id.'-'.$i['id_image'], NULL);


				// Load basic price
				$price = Product::getPriceStatic((int)$product->id, true);
				$price_original = $price;
				if ($categoryDefaultCache[$product->id_category_default]['percent'] > 0)
					$price *= (1 + ($categoryDefaultCache[$product->id_category_default]['percent'] / 100));
				else if ($categoryDefaultCache[$product->id_category_default]['percent'] < 0)
					$price *= (1 - ($categoryDefaultCache[$product->id_category_default]['percent'] / (-100)));
				$price = round($price, 2);


				// Loading Shipping Method
				if (!isset($this->_shippingMethod[Configuration::get('EBAY_SHIPPING_CARRIER_ID')]['shippingService']))
					$this->loadShippingMethod();


				// Generate array and try insert in database
				$datas = array(
					'id_product' => $product->id,
					'reference' => $product->reference,
					'name' => str_replace('&', '&amp;', $product->name),
					'brand' => $product->manufacturer_name,
					'description' => $product->description,
					'description_short' => $product->description_short,
					'price' => $price,
					'quantity' => $product->quantity,
					'categoryId' => $categoryDefaultCache[$product->id_category_default]['id_category_ref'],
					'shippingService' => $this->_shippingMethod[Configuration::get('EBAY_SHIPPING_CARRIER_ID')]['shippingService'],
					'shippingCost' => Configuration::get('EBAY_SHIPPING_COST'),
					'variationsList' => $variationsList,
					'variations' => $variations,
					'pictures' => $pictures,
					'picturesMedium' => $picturesMedium,
					'picturesLarge' => $picturesLarge,
				);

				// Fix hook update product
				if (isset($this->context->employee) && $this->context->employee->id > 0 && isset($_POST['submitProductAttribute']) && isset($_POST['id_product_attribute']) && isset($_POST['attribute_mvt_quantity']) && isset($_POST['id_mvt_reason']))
				{
					if (substr(_PS_VERSION_, 0, 3) == '1.3')
					{
						$id_product_attribute_fix = (int)$_POST['id_product_attribute'];
						$quantity_fix = (int)$_POST['attribute_quantity'];
						if ($id_product_attribute_fix > 0 && $quantity_fix > 0 && isset($datas['variations'][$product->id.'-'.$id_product_attribute_fix]['quantity']))
							$datas['variations'][$product->id.'-'.$id_product_attribute_fix]['quantity'] = (int)$quantity_fix;
					}
					else
					{
						$action = Db::getInstance()->getValue('SELECT `sign` FROM `'._DB_PREFIX_.'stock_mvt_reason` WHERE `id_stock_mvt_reason` = '.(int)$_POST['id_mvt_reason']);
						$id_product_attribute_fix = (int)$_POST['id_product_attribute'];
						$quantity_fix = (int)$_POST['attribute_mvt_quantity'];
						if ($id_product_attribute_fix > 0 && $quantity_fix > 0 && isset($datas['variations'][$product->id.'-'.$id_product_attribute_fix]['quantity']))
						{
							if ($action > 0)
								$datas['variations'][$product->id.'-'.$id_product_attribute_fix]['quantity'] += (int)$quantity_fix;
							if ($action < 0)
								$datas['variations'][$product->id.'-'.$id_product_attribute_fix]['quantity'] -= (int)$quantity_fix;
						}
					}
				}

				// Price Update
				if (isset($p['noPriceUpdate']))
					$datas['noPriceUpdate'] = $p['noPriceUpdate'];

				// Save percent and price discount
				if ($categoryDefaultCache[$product->id_category_default]['percent'] < 0)
				{
					$datas['price_original'] = round($price_original, 2);
					$datas['price_percent'] = round($categoryDefaultCache[$product->id_category_default]['percent']);
				}


				// Load eBay Description
				$features = $product->getFrontFeatures((int)($this->id_lang));
				$featuresHtml = '';
				if (isset($features))
					foreach ($features as $f)
						$featuresHtml .= '<b>'.$f['name'].'</b> : '.$f['value'].'<br/>';
				$datas['description'] = str_replace(
					array('{DESCRIPTION_SHORT}', '{DESCRIPTION}', '{FEATURES}', '{EBAY_IDENTIFIER}', '{EBAY_SHOP}', '{SLOGAN}', '{PRODUCT_NAME}'),
					array($datas['description_short'], $datas['description'], $featuresHtml, Configuration::get('EBAY_IDENTIFIER'), Configuration::get('EBAY_SHOP'), '', $product->name),
					Configuration::get('EBAY_PRODUCT_TEMPLATE')
				);


				// Export on eBay
				if (count($datas['variations']) > 0)
				{
					// Variations Case
					if ($categoryDefaultCache[$product->id_category_default]['is_multi_sku'] == 1)
					{
						// Load eBay Description
						$datas['description'] = str_replace(
							array('{MAIN_IMAGE}', '{MEDIUM_IMAGE_1}', '{MEDIUM_IMAGE_2}', '{MEDIUM_IMAGE_3}', '{PRODUCT_PRICE}', '{PRODUCT_PRICE_DISCOUNT}'),
							array(
								(isset($datas['picturesLarge'][0]) ? '<img src="'.$datas['picturesLarge'][0].'" class="bodyMainImageProductPrestashop" />' : ''),
								(isset($datas['picturesMedium'][1]) ? '<img src="'.$datas['picturesMedium'][1].'" class="bodyFirstMediumImageProductPrestashop" />' : ''),
								(isset($datas['picturesMedium'][2]) ? '<img src="'.$datas['picturesMedium'][2].'" class="bodyMediumImageProductPrestashop" />' : ''),
								(isset($datas['picturesMedium'][3]) ? '<img src="'.$datas['picturesMedium'][3].'" class="bodyMediumImageProductPrestashop" />' : ''),
								'',
								''
							),
							$datas['description']
						);

						// Multi Sku case
						// Check if product exists on eBay
						$itemID = Db::getInstance()->getValue('SELECT `id_product_ref` FROM `'._DB_PREFIX_.'ebay_product` WHERE `id_product` = '.(int)$product->id);
						if ($itemID)
						{
							// Update
							$datas['itemID'] = $itemID;
							if ($ebay->reviseFixedPriceItemMultiSku($datas))
								Db::getInstance()->autoExecute(_DB_PREFIX_.'ebay_product', array('date_upd' => pSQL($date)), 'UPDATE', '`id_product_ref` = '.(int)$itemID);

							// if product not on eBay we add it
							if ($ebay->errorCode == 291)
							{
								// We delete from DB and Add it on eBay
								Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ebay_product` WHERE `id_product_ref` = \''.pSQL($datas['itemID']).'\'');
								$ebay->addFixedPriceItemMultiSku($datas);
								if ($ebay->itemID > 0)
									Db::getInstance()->autoExecute(_DB_PREFIX_.'ebay_product', array('id_country' => 8, 'id_product' => (int)$product->id, 'id_attribute' => 0, 'id_product_ref' => pSQL($ebay->itemID), 'date_add' => pSQL($date), 'date_upd' => pSQL($date)), 'INSERT');
							}
						}
						else
						{
							// Add
							$ebay->addFixedPriceItemMultiSku($datas);
							if ($ebay->itemID > 0)
								Db::getInstance()->autoExecute(_DB_PREFIX_.'ebay_product', array('id_country' => 8, 'id_product' => (int)$product->id, 'id_attribute' => 0, 'id_product_ref' => pSQL($ebay->itemID), 'date_add' => pSQL($date), 'date_upd' => pSQL($date)), 'INSERT');
						}
					}
					else
					{
						// No Multi Sku case
						foreach ($datas['variations'] as $v)
						{
							$datasTmp = $datas;
							if (isset($v['pictures']) && count($v['pictures']) > 0)
								$datasTmp['pictures'] = $v['pictures'];
							if (isset($v['picturesMedium']) && count($v['picturesMedium']) > 0)
								$datasTmp['picturesMedium'] = $v['picturesMedium'];
							if (isset($v['picturesLarge']) && count($v['picturesLarge']) > 0)
								$datasTmp['picturesLarge'] = $v['picturesLarge'];
							foreach ($v['variations'] as $vLabel)
							{
								$datasTmp['name'] .= ' '.$vLabel['value'];
								$datasTmp['attributes'][$vLabel['name']] = $vLabel['value'];
							}
							$datasTmp['price'] = $v['price'];
							if (isset($v['price_original']))
							{
								$datasTmp['price_original'] = $v['price_original'];
								$datasTmp['price_percent'] = $v['price_percent'];
							}
							$datasTmp['quantity'] = $v['quantity'];
							$datasTmp['id_attribute'] = $v['id_attribute'];
							unset($datasTmp['variations']);
							unset($datasTmp['variationsList']);

							// Load eBay Description
							$datasTmp['description'] = str_replace(
								array('{MAIN_IMAGE}', '{MEDIUM_IMAGE_1}', '{MEDIUM_IMAGE_2}', '{MEDIUM_IMAGE_3}', '{PRODUCT_PRICE}', '{PRODUCT_PRICE_DISCOUNT}'),
								array(
									(isset($datasTmp['picturesLarge'][0]) ? '<img src="'.$datasTmp['picturesLarge'][0].'" class="bodyMainImageProductPrestashop" />' : ''),
									(isset($datasTmp['picturesMedium'][1]) ? '<img src="'.$datasTmp['picturesMedium'][1].'" class="bodyFirstMediumImageProductPrestashop" />' : ''),
									(isset($datasTmp['picturesMedium'][2]) ? '<img src="'.$datasTmp['picturesMedium'][2].'" class="bodyMediumImageProductPrestashop" />' : ''),
									(isset($datasTmp['picturesMedium'][3]) ? '<img src="'.$datasTmp['picturesMedium'][3].'" class="bodyMediumImageProductPrestashop" />' : ''),
									Tools::displayPrice($datasTmp['price']),
									(isset($datasTmp['price_original']) ? 'au lieu de <del>'.Tools::displayPrice($datasTmp['price_original']).'</del> (remise de '.round($datasTmp['price_percent']).'%)' : ''),
								),
								$datas['description']
							);

							$datasTmp['id_product'] = (int)$product->id.'-'.(int)$datasTmp['id_attribute'];

							// Check if product exists on eBay
							$itemID = Db::getInstance()->getValue('SELECT `id_product_ref` FROM `'._DB_PREFIX_.'ebay_product` WHERE `id_product` = '.(int)$product->id.' AND `id_attribute` = '.(int)$datasTmp['id_attribute']);
							if ($itemID)
							{
								// Get Item ID
								$datasTmp['itemID'] = $itemID;

								// Delete or Update
								if ($datasTmp['quantity'] < 1)
								{
									// Delete
									if ($ebay->endFixedPriceItem($datasTmp))
										Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ebay_product` WHERE `id_product_ref` = \''.pSQL($datasTmp['itemID']).'\'');
								}
								else
								{
								// Update
								if ($ebay->reviseFixedPriceItem($datasTmp))
									Db::getInstance()->autoExecute(_DB_PREFIX_.'ebay_product', array('date_upd' => pSQL($date)), 'UPDATE', '`id_product_ref` = '.(int)$itemID);

								// if product not on eBay we add it
								if ($ebay->errorCode == 291)
								{
									// We delete from DB and Add it on eBay
									Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ebay_product` WHERE `id_product_ref` = \''.pSQL($datasTmp['itemID']).'\'');
									$ebay->addFixedPriceItem($datasTmp);
									if ($ebay->itemID > 0)
										Db::getInstance()->autoExecute(_DB_PREFIX_.'ebay_product', array('id_country' => 8, 'id_product' => (int)$product->id, 'id_attribute' => (int)$datasTmp['id_attribute'], 'id_product_ref' => pSQL($ebay->itemID), 'date_add' => pSQL($date), 'date_upd' => pSQL($date)), 'INSERT');
								}
							}
							}
							else
							{
								// Add
								$ebay->addFixedPriceItem($datasTmp);
								if ($ebay->itemID > 0)
									Db::getInstance()->autoExecute(_DB_PREFIX_.'ebay_product', array('id_country' => 8, 'id_product' => (int)$product->id, 'id_attribute' => (int)$datasTmp['id_attribute'], 'id_product_ref' => pSQL($ebay->itemID), 'date_add' => pSQL($date), 'date_upd' => pSQL($date)), 'INSERT');
							}
						}
					}
				}
				else
				{
					// No variations case

					// Load eBay Description
					$datas['description'] = str_replace(
						array('{MAIN_IMAGE}', '{MEDIUM_IMAGE_1}', '{MEDIUM_IMAGE_2}', '{MEDIUM_IMAGE_3}', '{PRODUCT_PRICE}', '{PRODUCT_PRICE_DISCOUNT}'),
						array(
							(isset($datas['picturesLarge'][0]) ? '<img src="'.$datas['picturesLarge'][0].'" class="bodyMainImageProductPrestashop" />' : ''),
							(isset($datas['picturesMedium'][1]) ? '<img src="'.$datas['picturesMedium'][1].'" class="bodyFirstMediumImageProductPrestashop" />' : ''),
							(isset($datas['picturesMedium'][2]) ? '<img src="'.$datas['picturesMedium'][2].'" class="bodyMediumImageProductPrestashop" />' : ''),
							(isset($datas['picturesMedium'][3]) ? '<img src="'.$datas['picturesMedium'][3].'" class="bodyMediumImageProductPrestashop" />' : ''),
							Tools::displayPrice($datas['price']),
							(isset($datas['price_original']) ? 'au lieu de <del>'.Tools::displayPrice($datas['price_original']).'</del> (remise de '.round($datas['price_percent']).'%)' : ''),
						),
						$datas['description']
					);

					// Check if product exists on eBay
					$itemID = Db::getInstance()->getValue('SELECT `id_product_ref` FROM `'._DB_PREFIX_.'ebay_product` WHERE `id_product` = '.(int)$product->id);

					if ($itemID)
					{
						// Get Item ID
						$datas['itemID'] = $itemID;

						// Delete or Update
						if ($datas['quantity'] < 1)
						{
							// Delete
							if ($ebay->endFixedPriceItem($datas))
								Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ebay_product` WHERE `id_product_ref` = \''.pSQL($datas['itemID']).'\'');
						}
						else
						{
						// Update
						if ($ebay->reviseFixedPriceItem($datas))
							Db::getInstance()->autoExecute(_DB_PREFIX_.'ebay_product', array('date_upd' => pSQL($date)), 'UPDATE', '`id_product_ref` = '.(int)$itemID);

						// if product not on eBay we add it
						if ($ebay->errorCode == 291)
						{
							// We delete from DB and Add it on eBay
							Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ebay_product` WHERE `id_product_ref` = \''.pSQL($datas['itemID']).'\'');
							$ebay->addFixedPriceItem($datas);
							if ($ebay->itemID > 0)
								Db::getInstance()->autoExecute(_DB_PREFIX_.'ebay_product', array('id_country' => 8, 'id_product' => (int)$product->id, 'id_attribute' => 0, 'id_product_ref' => pSQL($ebay->itemID), 'date_add' => pSQL($date), 'date_upd' => pSQL($date)), 'INSERT');
						}
					}
					}
					else
					{
						// Add
						$ebay->addFixedPriceItem($datas);
						if ($ebay->itemID > 0)
							Db::getInstance()->autoExecute(_DB_PREFIX_.'ebay_product', array('id_country' => 8, 'id_product' => (int)$product->id, 'id_attribute' => 0, 'id_product_ref' => pSQL($ebay->itemID), 'date_add' => pSQL($date), 'date_upd' => pSQL($date)), 'INSERT');
					}
				}



				// Check error
				if (!empty($ebay->error))
				{
					$tab_error[md5($ebay->error)]['msg'] = $ebay->error;
					if (!isset($tab_error[md5($ebay->error)]['products']))
						$tab_error[md5($ebay->error)]['products'] = array();
					if (count($tab_error[md5($ebay->error)]['products']) < 10)
						$tab_error[md5($ebay->error)]['products'][] = $datas['name'];
					if (count($tab_error[md5($ebay->error)]['products']) == 10)
						$tab_error[md5($ebay->error)]['products'][] = '...';
					$count_error++;
				}
				else
					$count_success++;
				$count++;
			}
		}

		if ($count_error > 0)
			file_put_contents(dirname(__FILE__).'/log/syncError.php', '<?php $tab_error = '.var_export($tab_error, true).'; ?>');
			}




	/******************************************************************/
	/** Orders History Methods *******************************************/
	/******************************************************************/

	private function _displayOrdersHistory()
	{
		// Check if the module is configured
		if (!Configuration::get('EBAY_PAYPAL_EMAIL'))
			return '<p><b>'.$this->l('You have to configure "General Settings" tab before using this tab.').'</b></p><br />';


		$dateLastImport = '-';
		if (file_exists(dirname(__FILE__).'/log/orders.php'))
			include(dirname(__FILE__).'/log/orders.php');
		$html = '<h2>'.$this->l('Last orders Importation').' :</h2><p><b>'.$dateLastImport.'</b></p><br /><br />
		<h2>'.$this->l('Orders History').' :</h2>';

		if (isset($orderList) && count($orderList) > 0)
			foreach ($orderList as $order)
			{
				$html .= '<style>
						.orderImportTd1 {border-right:1px solid #000}
						.orderImportTd2 {border-right:1px solid #000;border-top:1px solid #000}
						.orderImportTd3 {border-top:1px solid #000}
					</style>
					<p>
					<b>'.$this->l('Order Ref eBay').' :</b> '.$order['id_order_ref'].'<br />
					<b>'.$this->l('Id Order Seller').' :</b> '.$order['id_order_seller'].'<br />
					<b>'.$this->l('Amount').' :</b> '.$order['amount'].'<br />
					<b>'.$this->l('Status').' :</b> '.$order['status'].'<br />
					<b>'.$this->l('Date').' :</b> '.$order['date'].'<br />
					<b>'.$this->l('E-mail').' :</b> '.$order['email'].'<br />
					<b>'.$this->l('Products').' :</b><br />';
				if (isset($order['product_list']) && count($order['product_list']) > 0)
				{
					$html .= '<table border="0" cellpadding="4" cellspacing="0"><tr>
						<td class="orderImportTd1"><b>'.$this->l('Id Product').'</b></td>
						<td class="orderImportTd1"><b>'.$this->l('Id Product Attribute').'</b></td>
						<td class="orderImportTd1"><b>'.$this->l('Quantity').'</b></td><td><b>'.$this->l('Price').'</b></td></tr>';
					foreach ($order['product_list'] as $product)
						$html .= '<tr><td class="orderImportTd2">'.$product['id_product'].'</td>
							  <td class="orderImportTd2">'.$product['id_product'].'</td>
							  <td class="orderImportTd2">'.$product['quantity'].'</td>
							  <td class="orderImportTd3">'.$product['price'].'</td></tr>';
					$html .= '</table>';
				}
				if (isset($order['errors']) && count($order['errors']) > 0)
				{
					$html .= '<b>'.$this->l('Status Import').' :</b> KO<br />';
					$html .= '<b>'.$this->l('Failure details').' :</b><br />';
					foreach ($order['errors'] as $error)
						$html .= $error.'t<br />';
				}
				else
					$html .= '<b>'.$this->l('Status Import').' :</b> OK';
				$html .= '</p><br />';
			}


		return $html;
	}



	/******************************************************************/
	/** Help Config Methods *******************************************/
	/******************************************************************/

	private function _displayHelp()
	{
		return '<p><b>Si vous avez des suggestions sur le module eBay, veuillez les postez sur le Forum <a href="http://www.prestashop.com/forums/viewforum/134/ebay/" target="_blank">http://www.prestashop.com/forums/viewforum/134/ebay/</a></b></p><br />

		<h2>Sommaire</h2>
		<h4><u><a href="#EbayHelpPart1">Comment lancer lancer sur eBay ?</a></u></h4>
		<ol>
		  <li><a href="#EbayHelpPart1-1">Sâ€™inscrire en tant que vendeur professionnel</a></li>
		  <li><a href="#EbayHelpPart1-2">VÃ©rification du statut de professionnel</a></li>
		  <li><a href="#EbayHelpPart1-3">Lier son compte PayPal Ã  son compte eBay</a></li>
		  <li><a href="#EbayHelpPart1-4">Configurer son compte vendeur et sa boutique eBay</a></li>
		</ol>
		<h4><u><a href="#EbayHelpPart2">Le Module Prestashop, comment Ã§a marcheÂ ?</a></u></h4>
		<ul>
		  <li><a href="#EbayHelpPart2-1">Onglet ParamÃ¨tres</li>
		  <li><a href="#EbayHelpPart2-2">Onglet Configuration des catÃ©gories</a></li>
		  <li><a href="#EbayHelpPart2-3">Onglet Template de la fiche produit</a></li>
		  <li><a href="#EbayHelpPart2-4">Onglet Mise en ligne des produits</a></li>
		</ul>
		<h4><u><a href="#EbayHelpPart3">Conseils & Astuces pour bien vendre sur eBay</a></u></h4>
		<ul>
		  <li><a href="#EbayHelpPart3-1">Conseil NÂ°1Â : Avoir une bonne fiche produit</a></li>
		  <li><a href="#EbayHelpPart3-2">Conseil NÂ°2Â : Avoir un bon Profil Vendeur</a></li>
		</ul>


		<br />
		<hr />
		<br />


		<h2 id="EbayHelpPart1">Comment se lancer sur eBayÂ ?</h2>
		<p>Plus dâ€™infos et tous les liens sur <a href="http://www.inscriptionpro.com" target="_blank"><u>www.inscriptionpro.com</u></a></p><br />

		<h3><u>4 Etapes pour sâ€™inscrire sur eBay</u></h3>
		<p>A noter : dÃ¨s votre inscription en tant que professionnel sur eBay.fr, vous recevrez automatiquement un email (dans les 48h) de notre service dÃ©diÃ© Ã  lâ€™intÃ©gration afin de vous aider dans cette dÃ©marche.<br />
		Vous pouvez contacter directement notre service â€œInscription Proâ€� par email : <a href="mailto:inscriptionpro@ebay.com">inscriptionpro@ebay.com</a></p>
		<br />

		<h3 id="EbayHelpPart1-1"><a href="https://ebayfr.backpackit.com/pub/2214807-guide-pour-les-vendeurs-professionnels-comment-vendre-sur-la-place-de-march-ebay-france-marketplace-ouverture-du-compte-ebay-et-paramtrage-initial-page-2-5"><u>1) Inscription en tant que vendeur professionnel</u></a></h3>
		<p>Lâ€™inscription se fait directement sur eBay.fr via un <a href="https://scgi.ebay.fr/ws/eBayISAPI.dll?RegisterEnterInfo&siteid=71&bizflow=2" target="_blank"><u>formulaire</u></a>. Choisissez un pseudo, un mot de passe, saisissez vos informations personnelles (adresse, tÃ©lÃ©phoneâ€¦) et le tour est jouÃ©.</p>
		<br />

		<h3 id="EbayHelpPart1-2"><a href="https://ebayfr.backpackit.com/pub/2214807-guide-pour-les-vendeurs-professionnels-comment-vendre-sur-la-place-de-march-ebay-france-marketplace-ouverture-du-compte-ebay-et-paramtrage-initial-page-2-5"><u>2) VÃ©rification de votre statut professionnel</u></a></h3>
		<p>Envoyez Ã  notre service clients les documents justifiant <a href="http://pages.ebay.fr/help/sell/business/existingbusinessvetting.html" target="_blank"><u>votre statut professionnel</u></a>.</p>
		<br />

		<h3 id="EbayHelpPart1-3"><a href="https://ebayfr.backpackit.com/pub/2214807-guide-pour-les-vendeurs-professionnels-comment-vendre-sur-la-place-de-march-ebay-france-marketplace-ouverture-du-compte-ebay-et-paramtrage-initial-page-2-5"><u>3) Liez votre compte PayPal Ã  votre compte eBay</u></a></h3>
		<p>Si vous nâ€™avez pas de compte PayPal Business, il faut dâ€™abord vous en crÃ©er un directement sur le site de PayPalÂ : <a href="http://altfarm.mediaplex.com/ad/ck/3484-80712-8030-7" target="_blank"><u>crÃ©ez votre compte PayPal business</u></a><br />
		Si vous avez dÃ©jÃ  un compte PayPal, liez-le Ã  votre compte eBay pour <a href="https://signin.ebay.fr/ws/eBayISAPI.dll?SignIn&UsingSSL=1&pUserId=&co_partnerId=2&siteid=71&ru=http%3A%2F%2Fmy.ebay.fr%2Fws%2FeBayISAPI.dll%3FMyeBay%26%26guest%3D1%26CurrentPage%3DMyeBayPayPalAccounts%26guest%3D1&pageType=1883" target="_blank"><u>recevoir les paiements</u></a> des acheteurs et <a href="https://signin.ebay.fr/ws/eBayISAPI.dll?SignIn&UsingSSL=1&pUserId=&co_partnerId=2&siteid=71&ru=https%3A%2F%2Farbd.ebay.fr%2Fws%2FeBayISAPI.dll%3FPaymentSelectionShowV4%26%26guest%3D1%26guest%3D1&pp=pass&pageType=4098&i1=0" target="_blank"><u>payer automatiquement les frais eBay</u></a>.</p>
		<br />

		<h3 id="EbayHelpPart1-4"><a href="https://ebayfr.backpackit.com/pub/2214807-guide-pour-les-vendeurs-professionnels-comment-vendre-sur-la-place-de-march-ebay-france-marketplace-ouverture-du-compte-ebay-et-paramtrage-initial-page-2-5"><u>4) Configurez votre compte vendeur et votre Boutique eBay</u></a></h3>
		<ul>
			<li><a href="http://cgi4.ebay.fr/ws/eBayISAPI.dll?RegisterBizSellerInfo&amp;guest=1" target="_blank"><u>Configurez votre compte</u></a> vendeur pro (coordonnÃ©es, CGV,â€¦).</li>
			<li><a href="http://cgi3.ebay.fr/ws/eBayISAPI.dll?CreateProductSubscription&amp;productId=3" target="_blank"><u>Ouvrez votre Boutique eBay</u></a> et <a href="http://cgi6.ebay.fr/ws/eBayISAPI.dll?StoreCategoryMgmt" target="_blank"><u>paramÃ©trez-la</u></a> pour mettre en avant vos produits dans un espace dÃ©diÃ©.</li>
			<li><a href="http://cgi3.ebay.fr/ws/eBayISAPI.dll?CreateProductSubscription&amp;productId=7" target="_blank"><u>Inscrivez-vous au Gestionnaire de Ventes Pro</u></a>, tableau de bord indispensable au pilotage de votre activitÃ©.</li>
		</ul>
		<br />

		<h3 align="center">Vous nâ€™avez plus quâ€™Ã  mettre en ligne vos produits avec le module eBay de PrestashopÂ !</h3>


		<br />
		<hr />
		<br />


		<h2 id="EbayHelpPart2">Le module eBay de Prestashop : comment Ã§a marche ?</h2>

		<h3 id="EbayHelpPart2-1">1) Onglet Â« ParamÃ¨tres Â»</h3>
		<p>Cette section est Ã  configurer lors de la premiÃ¨re utilisation du module. <br />Vous devez dÃ©finir votre <strong>compte PayPal</strong> comme <strong>moyen de paiement de produits sur eBay</strong> en renseignant lâ€™email que vous utilisez pour votre compte PayPal. <br />Si vous nâ€™en avez pas, vous devez <a href="https://www.paypal.com/fr/cgi-bin/webscr?cmd=_flow&amp;SESSION=85gB6zaK7zA5l_Y0UnNe_eJTaw1Al_e4hmrEfOLhrEiojJMJZGG-Cw9amIq&amp;dispatch=5885d80a13c0db1f8e263663d3faee8d5863a909c4bb5aeebb52c6e1151bdaa9" target="_blank"><u>souscrire Ã  un compte PayPal Business</u></a>.<br />Vous devez dÃ©finir le <strong>moyen et les frais de livraison</strong> qui seront appliquÃ©s Ã  vos produits sur eBay.</p>


		<h3 id="EbayHelpPart2-2">2) Onglet Â« Configuration des catÃ©gories Â»</h3>
		<p>Avant de publier vos produits sur eBay, vous devez associer les catÃ©gories de produits de votre boutique Prestashop avec celles dâ€™eBay. Vous pouvez Ã©galement choisir de vendre les produits de votre boutique Prestashop Ã  un prix diffÃ©rent sur eBay. Cet impact sur le prix est dÃ©fini en %.</p>
		<p><u>NBÂ :</u> Certaines catÃ©gories bÃ©nÃ©ficient du nouveau format dâ€™annonce multi-versions.</p>
		<br />

		<h3 id="EbayHelpPart2-3">3) Onglet Â« Template de la fiche produit Â»</h3>
		<p>Afin dâ€™optimiser le <strong>design de vos fiches produits</strong> sur eBay, vous pouvez <strong>personnaliser</strong> le header et le footer de vos annonces en crÃ©ant un template qui sâ€™appliquera Ã  lâ€™ensemble de vos produits sur eBay. En designant vos annonces selon votre charte graphique (logo, couleursâ€¦), vous dÃ©veloppez votre <strong>notoriÃ©tÃ©</strong> et votre <strong>visibilitÃ©</strong> sur eBay. De plus, un template dâ€™annonce bien travaillÃ© et prÃ©sentÃ© de maniÃ¨re agrÃ©able et professionnelle fait souvent la diffÃ©rence auprÃ¨s des acheteurs.</p>
		<br />

		<h3 id="EbayHelpPart2-4">4) Onglet Â« Mise en ligne des produits Â»</h3>
		<p>Cette section vous permet de mettre effectivement en ligne vos produits sur eBay. Vous avez le choix de placer la totalitÃ© des produits de votre boutique Prestashop sur eBay (option recommandÃ©e) ou seulement certaines catÃ©gories.</p>
		<p><u>Rappel :</u> Si vous avez une Boutique eBay, vous ne paierez aucun frais dâ€™insertion pour mettre vos produits en ligne sur eBay.</p>
		<br />


		<br />
		<hr />
		<br />


		<h2 id="EbayHelpPart3">Conseils &amp; astuces pour bien vendre sur eBay.fr </h2>

		<h3 id="EbayHelpPart3-1">Conseil NÂ°1 : Avoir une bonne fiche produit</h3>
		<p>Sur eBay, comme ailleurs, il faut soigner la prÃ©sentation de ses produits sous peine de ne pas atteindre le niveau de ventes attendu. Un produit mal photographiÃ© et mal dÃ©crit ne se vendra pas. Il y a donc certaines normes Ã  respecter avant de mettre en ligne ses produits sur eBay.fr. Cela vous permettra de bÃ©nÃ©ficier dâ€™un bon rÃ©fÃ©rencement de vos produits sur eBay, dâ€™optimiser vos ventes et ainsi de dÃ©velopper dâ€™une visibilitÃ© optimale.</p>
		<ul>
			<li><strong>Titre</strong> (champ limitÃ© Ã  55 caractÃ¨res)<br />Un bon titre doit obligatoirement comporter ces informations : Type de produit &gt; ModÃ¨le &gt; CaractÃ©ristiques importantes &gt; Marque<br />Il doit Ã©galement utiliser des mots clÃ©s pertinents : dÃ©terminer prÃ©alablement les mots clÃ©s les plus recherchÃ©s par les utilisateurs<br />A EVITER : abrÃ©viations, titre coupÃ© car dÃ©passant 55 caractÃ¨res, rÃ©fÃ©rences techniques trop poussÃ©es, ponctuationâ€¦</li>
			<li><strong>Attributs spÃ©cifiques</strong><br />Renseigner la totalitÃ© des attributs produits car ils sont pris en compte par les filtres de recherche et peuvent, par leur absence, exclure vos produits des rÃ©sultats de recherche.</li>
			<li><strong>Prix &amp; frais de port</strong><br />Le prix de vos objets, ainsi que vos frais de port, doivent Ãªtre adaptÃ©s Ã  lâ€™offre gÃ©nÃ©rale prÃ©sente sur eBay sous peine de vous voir pÃ©naliser dans lâ€™algorithme de recherche.<br />La gratuitÃ© des frais de port (frais de port inclus) permet de bÃ©nÃ©ficier dâ€™une visibilitÃ© privilÃ©giÃ©e dans les pages de rÃ©sultats.</li>
			<li><strong>Description / dÃ©tails du produit</strong><br />La description de vos produits doit Ãªtre claire et prÃ©cise, elle doit mentionner les caractÃ©ristiques clÃ©s de vos produits, les garanties, indiquer clairement les conditions et modalitÃ©s de livraison et de retour. Enfin une annonce doit Ãªtre mise en forme de maniÃ¨re attractive (images, logosâ€¦) et adaptÃ©e Ã  votre communication en tant que vendeur.</li>
			<li><strong>QualitÃ© des photos</strong><br />3 photos minimum (1 principale + 2 dans la description), elles doivent Ãªtre de bonne qualitÃ© et sur fond blanc. Photos des dÃ©tails si nÃ©cessaire. Les photos en disent autant quâ€™une belle description et constituent un Ã©lÃ©ment important dans la dÃ©cision de lâ€™acheteur.</li>
		</ul>
		<br />

		<h3 id="EbayHelpPart3-2">Conseil NÂ°2 : Avoir un bon profil vendeur</h3>
		<p>eBay est la seule place de marchÃ© en France Ã  vous donner la propriÃ©tÃ© du client. Vous Ãªtes donc responsable de la relation client avec vos acheteurs qui vous Ã©valuent en tant que vendeur. Pour avoir donc un bon profil vendeur, de bonnes Ã©valuations et ainsi augmentez la confiance de vos acheteurs, il vous faut Ã©videmment remplir vos obligations de vendeur mais aussi soigner votre relation client.</p>
		<p>Vous devez avoir un <strong>objectif</strong> en taux <strong>de satisfaction</strong> (Ã©valuations) de votre profil vendeur de minimum <strong>4.8/5</strong>.<br /><strong>Cette notation influence beaucoup le rÃ©fÃ©rencement de vos annonces dans eBay.</strong><br />Voici comment les <strong>4 critÃ¨res</strong> sur lesquels vous devez soigner votre niveau de service client pour atteindre cet objectif de 4.8 de taux de satisfaction sur votre profil vendeur :</p>
		<ul>
			<li><u>Objet conforme Ã  la description de lâ€™annonce :</u> cf. Conseil NÂ°1 Description produit</li>
			<li><u>Communication :</u> rÃ©ponse rapide aux questions prÃ© ET post-ventes (Messages dans Â« Mon eBay Â»)</li>
			<li><u>DÃ©lai de livraison :</u> assurer un service de livraison entre 48 et 72h maximum. Attention Ã  la gestion des stocks (dÃ©lai Ã  rallonge avec un produit indisponible)</li>
			<li><u>Frais de port :</u> la gratuitÃ© des frais de port permet dâ€™obtenir 5/5 Ã  ce critÃ¨re</li>
		</ul>

		<p><u>NB :</u> Lâ€™outil Â« Gestionnaire de Ventes Pro Â» vous permet dâ€™automatiser un certain nombre de ses tÃ¢ches de relation client et ainsi de vous faire gagner du temps.<br /><a href="http://pages.ebay.fr/outils-vendeurs/gestionnaire-de-ventes/pro.html" target="_blank"><u>Souscrire au Gestionnaire de ventes Pro</u></a></p>';
	}

	public function displayInfoByCart()
	{
	}

	private function _displayFormProduct() { }
	private function _displayFormAssociation() { }

}


