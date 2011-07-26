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
*  @version  Release: $Revision: 7483 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class FrontControllerCore
{
	public $errors = array();
	/**
	 * @var Context
	 */
	protected $context;
	
	/* Deprecated shortcuts as of 1.5 - Use $context->var instead */
	protected static $smarty;
	protected static $cookie;
	protected static $link;
	protected static $cart;
	
	public $iso;

	public $orderBy;
	public $orderWay;
	public $p;
	public $n;

	public $auth = false;
	public $guestAllowed = false;
	public $authRedirection = false;
	public $ssl = false;

	protected $restrictedCountry = false;
	protected $maintenance = false;
	protected $id_current_shop;
	protected $id_current_group_shop;

	public static $initialized = false;

	protected static $currentCustomerGroups;
	
	public $css_files;
	public $js_files;
	public $nb_items_per_page;

	public function __construct()
	{
		global $useSSL;

		$useSSL = $this->ssl;
	}

	public function run()
	{
		$this->init();
		$this->preProcess();
		$this->displayHeader();
		$this->process();
		$this->displayContent();
		$this->displayFooter();
	}

	public function init()
	{
		/*
		 * Globals are DEPRECATED as of version 1.5.
		 * Use the Context to access objects instead.
		 * Example: $this->context->cart
		 */
		global $cookie, $smarty, $cart, $iso, $defaultCountry, $protocol_link, $protocol_content, $link, $css_files, $js_files, $currency;

		if (self::$initialized)
			return;
		self::$initialized = true;

		$this->context = Context::getContext();
		
		$protocol_link = (Configuration::get('PS_SSL_ENABLED') OR (!empty($_SERVER['HTTPS']) AND strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';
		$protocol_content = ((isset($useSSL) AND $useSSL AND Configuration::get('PS_SSL_ENABLED')) OR (!empty($_SERVER['HTTPS']) AND strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';
		$link = new Link($protocol_link, $protocol_content);
		$this->context->link = $link;
		
		$this->id_current_shop = Context::getContext()->shop->getID();
		$this->id_current_group_shop = Context::getContext()->shop->getGroupID();
		
		$this->css_files = array();
		$this->js_files = array();
		
		// For compatibility with globals, DEPRECATED as of version 1.5
		$css_files = $this->css_files;
		$js_files = $this->js_files;
		
		if ($this->ssl AND (empty($_SERVER['HTTPS']) OR strtolower($_SERVER['HTTPS']) == 'off') AND Configuration::get('PS_SSL_ENABLED'))
		{
			header('HTTP/1.1 301 Moved Permanently');
			header('Location: '.Tools::getShopDomainSsl(true).$_SERVER['REQUEST_URI']);
			exit();
		}

		ob_start();

		$cookie = new Cookie('ps');
		$this->context->cookie = $cookie;
		
		if ($this->auth AND !$cookie->isLogged($this->guestAllowed))
			Tools::redirect('index.php?controller=authentication'.($this->authRedirection ? '&back='.$this->authRedirection : ''));

		/* Theme is missing or maintenance */
		if (!is_dir(_PS_THEME_DIR_))
			die(Tools::displayError('Current theme unavailable. Please check your theme directory name and permissions.'));
		elseif (basename($_SERVER['PHP_SELF']) != 'disabled.php' AND !(int)(Configuration::get('PS_SHOP_ENABLE')))
			$this->maintenance = true;
		elseif (Configuration::get('PS_GEOLOCATION_ENABLED'))
			if (($newDefault = $this->geolocationManagement($defaultCountry)) && Validate::isLoadedObject($newDefault))
				$defaultCountry = $newDefault;
			
		// Switch language if needed and init cookie language
		if ($iso = Tools::getValue('isolang') AND Validate::isLanguageIsoCode($iso) AND ($id_lang = (int)(Language::getIdByIso($iso))))
			$_GET['id_lang'] = $id_lang;

		Tools::switchLanguage();
		Tools::setCookieLanguage($cookie);

		if (isset($_GET['logout']) OR ($cookie->logged AND Customer::isBanned((int)$cookie->id_customer)))
		{
			$cookie->logout();
			Tools::redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL);
		}
		elseif (isset($_GET['mylogout']))
		{
			$cookie->mylogout();
			Tools::redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL);
		}

		$currency = Tools::setCurrency($cookie);
		$_MODULES = array();
		/* Cart already exists */
		if ((int)$cookie->id_cart)
		{
			$cart = new Cart($cookie->id_cart);
			if ($cart->OrderExists())
				unset($cookie->id_cart, $cart, $cookie->checkedTOS);
			/* Delete product of cart, if user can't make an order from his country */
			elseif (intval(Configuration::get('PS_GEOLOCATION_ENABLED')) AND 
					!in_array(strtoupper($cookie->iso_code_country), explode(';', Configuration::get('PS_ALLOWED_COUNTRIES'))) AND 
					$cart->nbProducts() AND intval(Configuration::get('PS_GEOLOCATION_NA_BEHAVIOR')) != -1 AND
					!self::isInWhitelistForGeolocation())
				unset($cookie->id_cart, $cart);
			// update cart values
			elseif ($cookie->id_customer != $cart->id_customer OR $cookie->id_lang != $cart->id_lang OR $currency->id != $cart->id_currency)
			{
				if ($cookie->id_customer)
					$cart->id_customer = (int)($cookie->id_customer);
				$cart->id_lang = (int)($cookie->id_lang);
				$cart->id_currency = (int)$currency->id;
				$cart->update();
			}
			/* Select an address if not set */
			if (isset($cart) && (!isset($cart->id_address_delivery) || $cart->id_address_delivery == 0 || 
				!isset($cart->id_address_invoice) || $cart->id_address_invoice == 0) && $cookie->id_customer)
			{
				$to_update = false;
				if (!isset($cart->id_address_delivery) || $cart->id_address_delivery == 0)
				{
					$to_update = true;
					$cart->id_address_delivery = (int)Address::getFirstCustomerAddressId($cart->id_customer);
				}
				if (!isset($cart->id_address_invoice) || $cart->id_address_invoice == 0)
				{
					$to_update = true;
					$cart->id_address_invoice = (int)Address::getFirstCustomerAddressId($cart->id_customer);
				}
				if ($to_update)
					$cart->update();
			}
		}

		if (!isset($cart) OR !$cart->id)
		{
			$cart = new Cart();
			$cart->id_lang = (int)($cookie->id_lang);
			$cart->id_currency = (int)($cookie->id_currency);
			$cart->id_guest = (int)($cookie->id_guest);
			$cart->id_group_shop = (int)$this->id_current_group_shop;
			$cart->id_shop = $this->id_current_shop;
			if ($cookie->id_customer)
			{
				$cart->id_customer = (int)($cookie->id_customer);
				$cart->id_address_delivery = (int)(Address::getFirstCustomerAddressId($cart->id_customer));
				$cart->id_address_invoice = $cart->id_address_delivery;
			}
			else
			{
				$cart->id_address_delivery = 0;
				$cart->id_address_invoice = 0;
			}
		}
		if (!$cart->nbProducts())
			$cart->id_carrier = NULL;

		$locale = strtolower(Configuration::get('PS_LOCALE_LANGUAGE')).'_'.strtoupper(Configuration::get('PS_LOCALE_COUNTRY').'.UTF-8');
		setlocale(LC_COLLATE, $locale);
		setlocale(LC_CTYPE, $locale);
		setlocale(LC_TIME, $locale);
		setlocale(LC_NUMERIC, 'en_US.UTF-8');
		
		if (Validate::isLoadedObject($currency))
			$smarty->ps_currency = $currency;
		if (!Validate::isLoadedObject($language = new Language($cookie->id_lang)))
			$language = new Language(Configuration::get('PS_LANG_DEFAULT'));
		$smarty->ps_language = $language;
		$this->context->language = $language;

		/* get page name to display it in body id */
		$pathinfo = pathinfo(__FILE__);
		$page_name = Dispatcher::getInstance()->getController();
		$page_name = (preg_match('/^[0-9]/', $page_name)) ? 'page_'.$page_name : $page_name;
		$smarty->assign(Tools::getMetaTags($language->id, $page_name));
		$smarty->assign('request_uri', Tools::safeOutput(urldecode($_SERVER['REQUEST_URI'])));

		/* Breadcrumb */
		$navigationPipe = (Configuration::get('PS_NAVIGATION_PIPE') ? Configuration::get('PS_NAVIGATION_PIPE') : '>');
		$smarty->assign('navigationPipe', $navigationPipe);

		if (!defined('_PS_BASE_URL_'))
			define('_PS_BASE_URL_', Tools::getShopDomain(true));
		if (!defined('_PS_BASE_URL_SSL_'))
			define('_PS_BASE_URL_SSL_', Tools::getShopDomainSsl(true));

		$link->preloadPageLinks();
		$this->canonicalRedirection();

		Product::initPricesComputation();

		$display_tax_label = $defaultCountry->display_tax_label;
		if ($cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')})
		{
			$infos = Address::getCountryAndState((int)($cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
			$country = new Country((int)$infos['id_country']);
			if (Validate::isLoadedObject($country))
				$display_tax_label = $country->display_tax_label;
		}
		$smarty->assign(array(
			'link' => $link,
			'cart' => $cart,
			'currency' => $currency,
			'cookie' => $cookie,
			'page_name' => $page_name,
			'base_dir' => _PS_BASE_URL_.__PS_BASE_URI__,
			'base_dir_ssl' => $protocol_link.Tools::getShopDomainSsl().__PS_BASE_URI__,
			'content_dir' => $protocol_content.Tools::getShopDomain().__PS_BASE_URI__,
			'tpl_dir' => _PS_THEME_DIR_,
			'modules_dir' => _MODULE_DIR_,
			'mail_dir' => _MAIL_DIR_,
			'lang_iso' => $language->iso_code,
			'come_from' => Tools::getHttpHost(true, true).Tools::htmlentitiesUTF8(str_replace('\'', '', urldecode($_SERVER['REQUEST_URI']))),
			'cart_qties' => (int)$cart->nbProducts(),
			'currencies' => Currency::getCurrencies(),
			'languages' => Language::getLanguages(),
			'priceDisplay' => Product::getTaxCalculationMethod(),
			'add_prod_display' => (int)Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
			'shop_name' => Configuration::get('PS_SHOP_NAME'),
			'roundMode' => (int)Configuration::get('PS_PRICE_ROUND_MODE'),
			'use_taxes' => (int)Configuration::get('PS_TAX'),
			'display_tax_label' => (bool)$display_tax_label,
			'vat_management' => (int)Configuration::get('VATNUMBER_MANAGEMENT'),
			'opc' => (bool)Configuration::get('PS_ORDER_PROCESS_TYPE'),
			'PS_CATALOG_MODE' => (bool)Configuration::get('PS_CATALOG_MODE'),
			'id_current_shop' => $this->id_current_shop,
			'id_current_group_shop' => (int)$this->id_current_group_shop
		));

		// Deprecated
		$smarty->assign(array(
			'id_currency_cookie' => (int)$currency->id,
			'logged' => $cookie->isLogged(),
			'customerName' => ($cookie->logged ? $cookie->customer_firstname.' '.$cookie->customer_lastname : false)
		));

		// TODO for better performances (cache usage), remove these assign and use a smarty function to get the right media server in relation to the full ressource name
		$assignArray = array(
			'img_ps_dir' => _PS_IMG_,
			'img_cat_dir' => _THEME_CAT_DIR_,
			'img_lang_dir' => _THEME_LANG_DIR_,
			'img_prod_dir' => _THEME_PROD_DIR_,
			'img_manu_dir' => _THEME_MANU_DIR_,
			'img_sup_dir' => _THEME_SUP_DIR_,
			'img_ship_dir' => _THEME_SHIP_DIR_,
			'img_store_dir' => _THEME_STORE_DIR_,
			'img_col_dir' => _THEME_COL_DIR_,
			'img_dir' => _THEME_IMG_DIR_,
			'css_dir' => _THEME_CSS_DIR_,
			'js_dir' => _THEME_JS_DIR_,
			'pic_dir' => _THEME_PROD_PIC_DIR_
		);

		foreach ($assignArray as $assignKey => $assignValue)
			if (substr($assignValue, 0, 1) == '/' OR $protocol_content == 'https://')
				$smarty->assign($assignKey, $protocol_content.Tools::getMediaServer($assignValue).$assignValue);
			else
				$smarty->assign($assignKey, $assignValue);

		/*
		 * These shortcuts are DEPRECATED as of version 1.5.
		 * Use the Context to access objects instead.
		 * Example: $this->context->cart
		 */
		self::$cookie = $cookie;
		self::$cart = $cart;
		self::$smarty = $smarty;
		self::$link = $link;

		if ($this->maintenance)
			$this->displayMaintenancePage();
		if ($this->restrictedCountry)
			$this->displayRestrictedCountryPage();

		//live edit
		if (Tools::isSubmit('live_edit') AND $ad = Tools::getValue('ad') AND (Tools::getValue('liveToken') == sha1(Tools::getValue('ad')._COOKIE_KEY_)))
			if (!is_dir(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.$ad))
				die(Tools::displayError());

		$this->iso = $iso;
		$this->setMedia();
		
		if (isset($cookie->id_customer) && (int)$cookie->id_customer)
		{
			$customer = new Customer($cookie->id_customer);
			$customer->logged = $cookie->logged;
		}
		else
			$customer = new Customer();
		
		if($cookie->id_country)
			$customer->geoloc_id_country = (int)$cookie->id_country;
		if($cookie->id_state)
			$customer->geoloc_id_state = (int)$cookie->id_state;
		if($cookie->postcode)
			$customer->geoloc_postcode = (int)$cookie->postcode;
		

		$this->context->customer = $customer;
		$this->context->cart = $cart;
		$this->context->currency = $currency;
		$this->context->controller = $this;
		$this->context->country = $defaultCountry;
	}

	/* Display a maintenance page if shop is closed */
	protected function displayMaintenancePage()
	{
		if (!in_array(Tools::getRemoteAddr(), explode(',', Configuration::get('PS_MAINTENANCE_IP'))))
		{
			header('HTTP/1.1 503 temporarily overloaded');
			$this->context->smarty->display(_PS_THEME_DIR_.'maintenance.tpl');
			exit;
		}
	}

	/* Display a specific page if the user country is not allowed */
	protected function displayRestrictedCountryPage()
	{
		header('HTTP/1.1 503 temporarily overloaded');
		$this->context->smarty->display(_PS_THEME_DIR_.'restricted-country.tpl');
		exit;
	}

	protected function canonicalRedirection()
	{
		// Automatically redirect to the canonical URL if needed
		if (isset($this->php_self) AND !empty($this->php_self))
		{
			// $_SERVER['HTTP_HOST'] must be replaced by the real canonical domain
			$canonicalURL = $this->context->link->getPageLink($this->php_self, $this->ssl, $this->context->language->id);
			if (!preg_match('/^'.Tools::pRegexp($canonicalURL, '/').'([&?].*)?$/i', (($this->ssl AND Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']))
			{
				header('HTTP/1.0 301 Moved');
				$params = '';
				$excludedKey = array('isolang', 'id_lang');
				foreach ($_GET as $key => $value)
					if (!in_array($key, $excludedKey))
						$params .= ($params == '' ? '?' : '&').$key.'='.$value;
				if (defined('_PS_MODE_DEV_') AND _PS_MODE_DEV_ AND $_SERVER['REQUEST_URI'] != __PS_BASE_URI__)
					die('[Debug] This page has moved<br />Please use the following URL instead: <a href="'.$canonicalURL.$params.'">'.$canonicalURL.$params.'</a>');
				Tools::redirectLink($canonicalURL.$params);
			}
		}
	}

	protected function geolocationManagement($defaultCountry)
	{
		if (!in_array($_SERVER['SERVER_NAME'], array('localhost', '127.0.0.1')))
		{
			/* Check if Maxmind Database exists */
			if (file_exists(_PS_GEOIP_DIR_.'GeoLiteCity.dat'))
			{
				if (!isset($this->context->cookie->iso_code_country) OR (isset($this->context->cookie->iso_code_country) AND !in_array(strtoupper($this->context->cookie->iso_code_country), explode(';', Configuration::get('PS_ALLOWED_COUNTRIES')))))
				{
          			include_once(_PS_GEOIP_DIR_.'geoipcity.inc');
					include_once(_PS_GEOIP_DIR_.'geoipregionvars.php');

					$gi = geoip_open(realpath(_PS_GEOIP_DIR_.'GeoLiteCity.dat'), GEOIP_STANDARD);
					$record = geoip_record_by_addr($gi, '81.57.72.226');//Tools::getRemoteAddr());

					if (is_object($record)) 
					{
						if (!in_array(strtoupper($record->country_code), explode(';', Configuration::get('PS_ALLOWED_COUNTRIES'))) AND !self::isInWhitelistForGeolocation())
						{
							if (Configuration::get('PS_GEOLOCATION_BEHAVIOR') == _PS_GEOLOCATION_NO_CATALOG_)
								$this->restrictedCountry = true;
							elseif (Configuration::get('PS_GEOLOCATION_BEHAVIOR') == _PS_GEOLOCATION_NO_ORDER_)
								$this->context->smarty->assign(array(
									'restricted_country_mode' => true,
									'geolocation_country' => $record->country_name
								));
						}
						else
						{
							$this->context->cookie->iso_code_country = strtoupper($record->country_code);
							$hasBeenSet = true;
						}
					}
				}

				if (isset($this->context->cookie->iso_code_country) && ($id_country = Country::getByIso(strtoupper($this->context->cookie->iso_code_country))))
				{
					/* Update defaultCountry */
					if($defaultCountry->iso_code != $this->context->cookie->iso_code_country)
						$defaultCountry = new Country($id_country);
					if (isset($hasBeenSet) AND $hasBeenSet)
						$this->context->cookie->id_currency = (int)(Currency::getCurrencyInstance($defaultCountry->id_currency ? (int)$defaultCountry->id_currency : Configuration::get('PS_CURRENCY_DEFAULT'))->id);
					return $defaultCountry;
				}
				elseif (Configuration::get('PS_GEOLOCATION_NA_BEHAVIOR') == _PS_GEOLOCATION_NO_CATALOG_)
					$this->restrictedCountry = true;
				elseif (Configuration::get('PS_GEOLOCATION_NA_BEHAVIOR') == _PS_GEOLOCATION_NO_ORDER_)
					$this->context->smarty->assign(array(
						'restricted_country_mode' => true,
						'geolocation_country' => 'Undefined'
					));
			}
			/* If not exists we disabled the geolocation feature */
			else
				Configuration::updateValue('PS_GEOLOCATION_ENABLED', 0);
		}
		return false;
	}

	public function preProcess()
	{
	}

	public function setMedia()
	{
		$this->addCSS(_THEME_CSS_DIR_.'global.css', 'all');
		$this->addJS(array(_PS_JS_DIR_.'jquery/jquery-1.4.4.min.js', _PS_JS_DIR_.'jquery/jquery.easing.1.3.js', _PS_JS_DIR_.'tools.js'));
		if (Tools::isSubmit('live_edit') AND $ad = Tools::getValue('ad') AND (Tools::getValue('liveToken') == sha1(Tools::getValue('ad')._COOKIE_KEY_)))
		{
			$this->addJS(array(
							_PS_JS_DIR_.'jquery/jquery-ui-1.8.10.custom.min.js',
							_PS_JS_DIR_.'jquery/jquery.fancybox-1.3.4.js',
							_PS_JS_DIR_.'hookLiveEdit.js')
							);
			$this->addCSS(_PS_CSS_DIR_.'jquery.fancybox-1.3.4.css');
		}
	}

	public function process()
	{
	}

	public function displayContent()
	{
		Tools::safePostVars();
		$this->context->smarty->assign('errors', $this->errors);
	}

	public function displayHeader()
	{
		if (!self::$initialized)
			$this->init();
		// P3P Policies (http://www.w3.org/TR/2002/REC-P3P-20020416/#compact_policies)
		header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');

		/* Hooks are volontary out the initialize array (need those variables already assigned) */
		$this->context->smarty->assign(array(
			'time' => time(),
			'img_update_time' => Configuration::get('PS_IMG_UPDATE_TIME'),
			'static_token' => Tools::getToken(false),
			'token' => Tools::getToken(),
			'logo_image_width' => Configuration::get('SHOP_LOGO_WIDTH'),
			'logo_image_height' => Configuration::get('SHOP_LOGO_HEIGHT'),
			'priceDisplayPrecision' => _PS_PRICE_DISPLAY_PRECISION_,
			'content_only' => (int)Tools::getValue('content_only')
		));
		$this->context->smarty->assign(array(
			'HOOK_HEADER' => Module::hookExec('header'),
			'HOOK_TOP' => Module::hookExec('top'),
			'HOOK_LEFT_COLUMN' => Module::hookExec('leftColumn')
		));

		if ((Configuration::get('PS_CSS_THEME_CACHE') OR Configuration::get('PS_JS_THEME_CACHE')) AND is_writable(_PS_THEME_DIR_.'cache'))
		{
			// CSS compressor management
			if (Configuration::get('PS_CSS_THEME_CACHE'))
				$this->css_files = Tools::cccCSS($this->css_files);

			//JS compressor management
			if (Configuration::get('PS_JS_THEME_CACHE'))
				$this->js_files = Tools::cccJs($this->js_files);
		}

		$this->context->smarty->assign('css_files', $this->css_files);
		$this->context->smarty->assign('js_files', array_unique($this->js_files));
		$this->context->smarty->display(_PS_THEME_DIR_.'header.tpl');
	}

	public function displayFooter()
	{
		if (!$this->context)
			$this->context = Context::getContext();

		$this->context->smarty->assign(array(
			'HOOK_RIGHT_COLUMN' => Module::hookExec('rightColumn', array('cart' => $this->context->cart)),
			'HOOK_FOOTER' => Module::hookExec('footer'),
			'content_only' => (int)(Tools::getValue('content_only'))));
		$this->context->smarty->display(_PS_THEME_DIR_.'footer.tpl');
		//live edit
		if (Tools::isSubmit('live_edit') AND $ad = Tools::getValue('ad') AND (Tools::getValue('liveToken') == sha1(Tools::getValue('ad')._COOKIE_KEY_)))
		{
			$this->context->smarty->assign(array('ad' => $ad, 'live_edit' => true));
			$this->context->smarty->display(_PS_ALL_THEMES_DIR_.'live_edit.tpl');
		}
		else
			Tools::displayError();
	}

	public function productSort()
	{
		if (!self::$initialized)
			$this->init();

		// $this->orderBy = Tools::getProductsOrder('by', Tools::getValue('orderby'));
		// $this->orderWay = Tools::getProductsOrder('way', Tools::getValue('orderway'));
		// 'orderbydefault' => Tools::getProductsOrder('by'),
		// 'orderwayposition' => Tools::getProductsOrder('way'), // Deprecated: orderwayposition
		// 'orderwaydefault' => Tools::getProductsOrder('way'),
			
		$stock_management = (int)(Configuration::get('PS_STOCK_MANAGEMENT')) ? true : false; // no display quantity order if stock management disabled
		$orderByValues = array(0 => 'name', 1 => 'price', 2 => 'date_add', 3 => 'date_upd', 4 => 'position', 5 => 'manufacturer_name', 6 => 'quantity');
		$orderWayValues = array(0 => 'asc', 1 => 'desc');
		$this->orderBy = Tools::strtolower(Tools::getValue('orderby', $orderByValues[(int)(Configuration::get('PS_PRODUCTS_ORDER_BY'))]));
		$this->orderWay = Tools::strtolower(Tools::getValue('orderway', $orderWayValues[(int)(Configuration::get('PS_PRODUCTS_ORDER_WAY'))]));
		if (!in_array($this->orderBy, $orderByValues))
			$this->orderBy = $orderByValues[0];
		if (!in_array($this->orderWay, $orderWayValues))
			$this->orderWay = $orderWayValues[0];

		$this->context->smarty->assign(array(
			'orderby' => $this->orderBy,
			'orderway' => $this->orderWay,
			'orderbydefault' => $orderByValues[(int)(Configuration::get('PS_PRODUCTS_ORDER_BY'))],
			'orderwayposition' => $orderWayValues[(int)(Configuration::get('PS_PRODUCTS_ORDER_WAY'))], // Deprecated: orderwayposition
			'orderwaydefault' => $orderWayValues[(int)(Configuration::get('PS_PRODUCTS_ORDER_WAY'))],
			'stock_management' => (int)($stock_management)));
	}

	public function pagination($nbProducts = 10)
	{
		if (!self::$initialized)
			$this->init();

		$nArray = (int)(Configuration::get('PS_PRODUCTS_PER_PAGE')) != 10 ? array((int)(Configuration::get('PS_PRODUCTS_PER_PAGE')), 10, 20, 50) : array(10, 20, 50);
		asort($nArray);
		$this->n = abs((int)(Tools::getValue('n', ((isset($this->context->cookie->nb_item_per_page) AND $this->context->cookie->nb_item_per_page >= 10) ? self::$this->context->cookie->nb_item_per_page : (int)(Configuration::get('PS_PRODUCTS_PER_PAGE'))))));
		$this->p = abs((int)(Tools::getValue('p', 1)));
		$range = 2; /* how many pages around page selected */

		if ($this->p < 0)
			$this->p = 0;

		if (isset($this->context->cookie->nb_item_per_page) AND $this->n != $this->context->cookie->nb_item_per_page AND in_array($this->n, $nArray))
			$this->context->cookie->nb_item_per_page = $this->n;

		if ($this->p > ($nbProducts / $this->n))
			$this->p = ceil($nbProducts / $this->n);
		$pages_nb = ceil($nbProducts / (int)($this->n));

		$start = (int)($this->p - $range);
		if ($start < 1)
			$start = 1;
		$stop = (int)($this->p + $range);
		if ($stop > $pages_nb)
			$stop = (int)($pages_nb);
		$this->context->smarty->assign('nb_products', $nbProducts);
		$pagination_infos = array(
			'pages_nb' => (int)($pages_nb),
			'p' => (int)($this->p),
			'n' => (int)($this->n),
			'nArray' => $nArray,
			'range' => (int)($range),
			'start' => (int)($start),
			'stop' => (int)($stop)
		);
		$this->context->smarty->assign($pagination_infos);
	}

	public static function getCurrentCustomerGroups()
	{
		$context = Context::getContext();
		if (!$context->customer->id)
			return array();
		if (!is_array(self::$currentCustomerGroups))
		{
			self::$currentCustomerGroups = array();
			$result = Db::getInstance()->ExecuteS('SELECT id_group FROM '._DB_PREFIX_.'customer_group WHERE id_customer = '.(int)$context->customer->id);
			foreach ($result as $row)
				self::$currentCustomerGroups[] = $row['id_group'];
		}
		return self::$currentCustomerGroups;
	}

	protected static function isInWhitelistForGeolocation()
	{
		$allowed = false;
		$userIp = Tools::getRemoteAddr();
		$ips = explode(';', Configuration::get('PS_GEOLOCATION_WHITELIST'));
		if (is_array($ips) AND sizeof($ips))
			foreach ($ips AS $ip)
				if (!empty($ip) AND strpos($userIp, $ip) === 0)
					$allowed = true;
		return $allowed;
	}
	
	/**
	 * addCSS allows you to add stylesheet at any time.
	 *
	 * @param mixed $css_uri
	 * @param string $css_media_type
	 * @return true
	 */
	public function addCSS($css_uri, $css_media_type = 'all')
	{
		if (is_array($css_uri))
		{
			foreach ($css_uri as $file => $media_type)
				$this->addCSS($file, $media_type);
			return true;
		}
		
		//overriding of modules css files
		$different = 0;
		$override_path = str_replace(__PS_BASE_URI__.'modules/', _PS_ROOT_DIR_.'/themes/'._THEME_NAME_.'/css/modules/', $css_uri, $different);
		if ($different && file_exists($override_path))
			$css_uri = str_replace(__PS_BASE_URI__.'modules/', __PS_BASE_URI__.'themes/'._THEME_NAME_.'/css/modules/', $css_uri, $different);
		else
		{
			// remove PS_BASE_URI on _PS_ROOT_DIR_ for the following
			$url_data = parse_url($css_uri);
			$file_uri = _PS_ROOT_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $url_data['path']);
			// check if css files exists
			if (!file_exists($file_uri))
				return true;
		}

		// detect mass add
		$css_uri = array($css_uri => $css_media_type);

		// adding file to the big array...
		if (is_array($this->css_files))
			$this->css_files = array_merge($this->css_files, $css_uri);
		else
			$this->css_files = $css_uri;

		return true;
	}
	
	/**
	 * addJS load a javascript file in the header
	 *
	 * @param mixed $js_uri
	 * @return void
	 */
	public function addJS($js_uri)
	{
		if(!isset($this->js_files))
			$this->js_files = array();
		// avoid useless operation...
		if (in_array($js_uri, $this->js_files))
			return true;

		// detect mass add
		if (!is_array($js_uri) && !in_array($js_uri, $this->js_files))
			$js_uri = array($js_uri);
		else
			foreach($js_uri as $key => $js)
				if (in_array($js, $this->js_files))
					unset($js_uri[$key]);

		//overriding of modules js files
		foreach ($js_uri AS $key => &$file)
		{
			if (!preg_match('/^http(s?):\/\//i', $file))
			{
				$different = 0;
				$override_path = str_replace(__PS_BASE_URI__.'modules/', _PS_ROOT_DIR_.'/themes/'._THEME_NAME_.'/js/modules/', $file, $different);
				if ($different && file_exists($override_path))
					$file = str_replace(__PS_BASE_URI__.'modules/', __PS_BASE_URI__.'themes/'._THEME_NAME_.'/js/modules/', $file, $different);
				else
				{
					// remove PS_BASE_URI on _PS_ROOT_DIR_ for the following
					$url_data = parse_url($file);
					$file_uri = _PS_ROOT_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $url_data['path']);
					// check if js files exists
					if (!file_exists($file_uri))
						unset($js_uri[$key]);
				}
			}
		}

		// adding file to the big array...
		$this->js_files = array_merge($this->js_files, $js_uri);

		return true;
	}
	
}

