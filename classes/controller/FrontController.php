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

class FrontControllerCore extends Controller
{
	public $errors = array();

	/**
	 * @deprecated Deprecated shortcuts as of 1.5 - Use $context->var instead
	 */
	protected static $smarty, $cookie, $link, $cart;

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

	public $display_column_left = true;
	public $display_column_right = true;

	public static $initialized = false;

	protected static $currentCustomerGroups;

	public $nb_items_per_page;

	public function __construct()
	{
		global $useSSL;

		parent::__construct();

		if (isset($useSSL))
			$this->ssl = $useSSL;
		else
			$useSSL = $this->ssl;
	}

	/**
	 * @see Controller::checkAccess()
	 *
	 * @return boolean
	 */
	public function checkAccess()
	{
		return true;
	}

	/**
	 * @see Controller::viewAccess
	 *
	 * @return boolean
	 */
	public function viewAccess()
	{
		return true;
	}

	public function init()
	{
		/*
		 * Globals are DEPRECATED as of version 1.5.
		 * Use the Context to access objects instead.
		 * Example: $this->context->cart
		 */
		global $useSSL, $cookie, $smarty, $cart, $iso, $defaultCountry, $protocol_link, $protocol_content, $link, $css_files, $js_files, $currency;

		if (self::$initialized)
			return;
		self::$initialized = true;

		parent::init();

		// For compatibility with globals, DEPRECATED as of version 1.5
		$css_files = $this->css_files;
		$js_files = $this->js_files;

		if ($this->ssl && !Tools::usingSecureMode() && Configuration::get('PS_SSL_ENABLED'))
		{
			header('HTTP/1.1 301 Moved Permanently');
			header('Cache-Control: no-cache');
			header('Location: '.Tools::getShopDomainSsl(true).$_SERVER['REQUEST_URI']);
			exit();
		}
		else if (Configuration::get('PS_SSL_ENABLED') && Tools::usingSecureMode() && !($this->ssl))
		{
			header('HTTP/1.1 301 Moved Permanently');
			header('Cache-Control: no-cache');
			header('Location: '.Tools::getShopDomain(true).$_SERVER['REQUEST_URI']);
			exit();
		}

		if ($this->ajax)
		{
			$this->display_header = false;
			$this->display_footer = false;
		}

		// if account created with the 2 steps register process, remove 'accoun_created' from cookie
		if (isset($this->context->cookie->account_created))
		{
			$this->context->smarty->assign('account_created', 1);
			unset($this->context->cookie->account_created);
		}

		ob_start();

		// Switch language if needed and init cookie language
		if (($iso = Tools::getValue('isolang')) && Validate::isLanguageIsoCode($iso) && ($id_lang = (int)Language::getIdByIso($iso)))
			$_GET['id_lang'] = $id_lang;

		Tools::switchLanguage();
		Tools::setCookieLanguage($this->context->cookie);
		$currency = Tools::setCurrency($this->context->cookie);

		$protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
		$useSSL = ((isset($this->ssl) && $this->ssl && Configuration::get('PS_SSL_ENABLED')) || Tools::usingSecureMode()) ? true : false;
		$protocol_content = ($useSSL) ? 'https://' : 'http://';
		$link = new Link($protocol_link, $protocol_content);
		$this->context->link = $link;

		if ($id_cart = (int)$this->recoverCart())
			$this->context->cookie->id_cart = (int)$id_cart;

		if ($this->auth && !$this->context->customer->isLogged($this->guestAllowed))
			Tools::redirect('index.php?controller=authentication'.($this->authRedirection ? '&back='.$this->authRedirection : ''));

		/* Theme is missing or maintenance */
		if (!is_dir(_PS_THEME_DIR_))
			die(sprintf(Tools::displayError('Current theme unavailable "%s". Please check your theme directory name and permissions.'), basename(rtrim(_PS_THEME_DIR_, '/\\'))));
		elseif (basename($_SERVER['PHP_SELF']) != 'disabled.php' && !(int)(Configuration::get('PS_SHOP_ENABLE')))
			$this->maintenance = true;
		elseif (Configuration::get('PS_GEOLOCATION_ENABLED'))
			if (($newDefault = $this->geolocationManagement($this->context->country)) && Validate::isLoadedObject($newDefault))
				$this->context->country = $newDefault;

		if (isset($_GET['logout']) || ($this->context->customer->logged && Customer::isBanned($this->context->customer->id)))
		{
			$this->context->customer->logout();

			// Login information have changed, so we check if the cart rules still apply
			CartRule::autoRemoveFromCart();

			Tools::redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);
		}
		elseif (isset($_GET['mylogout']))
		{
			$this->context->customer->mylogout();
			Tools::redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);
		}

		/* Cart already exists */
		if ((int)$this->context->cookie->id_cart)
		{
			$cart = new Cart($this->context->cookie->id_cart);
			if ($cart->OrderExists())
				unset($this->context->cookie->id_cart, $cart, $this->context->cookie->checkedTOS);
			/* Delete product of cart, if user can't make an order from his country */
			elseif (intval(Configuration::get('PS_GEOLOCATION_ENABLED')) &&
					!in_array(strtoupper($this->context->cookie->iso_code_country), explode(';', Configuration::get('PS_ALLOWED_COUNTRIES'))) &&
					$cart->nbProducts() && intval(Configuration::get('PS_GEOLOCATION_NA_BEHAVIOR')) != -1 &&
					!FrontController::isInWhitelistForGeolocation())
				unset($this->context->cookie->id_cart, $cart);
			// update cart values
			elseif ($this->context->cookie->id_customer != $cart->id_customer || $this->context->cookie->id_lang != $cart->id_lang || $currency->id != $cart->id_currency)
			{
				if ($this->context->cookie->id_customer)
					$cart->id_customer = (int)($this->context->cookie->id_customer);
				$cart->id_lang = (int)($this->context->cookie->id_lang);
				$cart->id_currency = (int)$currency->id;
				$cart->update();
			}
			/* Select an address if not set */
			if (isset($cart) && (!isset($cart->id_address_delivery) || $cart->id_address_delivery == 0 ||
				!isset($cart->id_address_invoice) || $cart->id_address_invoice == 0) && $this->context->cookie->id_customer)
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

		if (!isset($cart) || !$cart->id)
		{
			$cart = new Cart();
			$cart->id_lang = (int)($this->context->cookie->id_lang);
			$cart->id_currency = (int)($this->context->cookie->id_currency);
			$cart->id_guest = (int)($this->context->cookie->id_guest);
			$cart->id_group_shop = (int)$this->context->shop->getGroupID();
			$cart->id_shop = $this->context->shop->getID(true);
			if ($this->context->cookie->id_customer)
			{
				$cart->id_customer = (int)($this->context->cookie->id_customer);
				$cart->id_address_delivery = (int)(Address::getFirstCustomerAddressId($cart->id_customer));
				$cart->id_address_invoice = $cart->id_address_delivery;
			}
			else
			{
				$cart->id_address_delivery = 0;
				$cart->id_address_invoice = 0;
			}
		}

		$locale = strtolower(Configuration::get('PS_LOCALE_LANGUAGE')).'_'.strtoupper(Configuration::get('PS_LOCALE_COUNTRY').'.UTF-8');
		setlocale(LC_COLLATE, $locale);
		setlocale(LC_CTYPE, $locale);
		setlocale(LC_TIME, $locale);
		setlocale(LC_NUMERIC, 'en_US.UTF-8');

		/* get page name to display it in body id */
		
		// Are we in a payment module 
		$module_name = Tools::getValue('module');
		if (!empty($this->page_name))
			$page_name = $this->page_name;
		else if (!empty($this->php_self))
			$page_name = $this->php_self;
		else if (0&&Tools::getValue('fc') == 'module' && $module_name != '' && new $module_name() instanceof PaymentModule)
			$page_name = 'module-payment-submit';
		// @retrocompatibility Are we in a module ?
		else if (preg_match('#^'.preg_quote($this->context->shop->getPhysicalURI(), '#').'modules/([a-zA-Z0-9_-]+?)/(.*)$#', $_SERVER['REQUEST_URI'], $m))
			$page_name = 'module-'.$m[1].'-'.str_replace(array('.php', '/'), array('', '-'), $m[2]);
		else
		{
			$page_name = Dispatcher::getInstance()->getController();
			$page_name = (preg_match('/^[0-9]/', $page_name)) ? 'page_'.$page_name : $page_name;
		}

		$this->context->smarty->assign(Tools::getMetaTags($this->context->language->id, $page_name));
		$this->context->smarty->assign('request_uri', Tools::safeOutput(urldecode($_SERVER['REQUEST_URI'])));

		/* Breadcrumb */
		$navigationPipe = (Configuration::get('PS_NAVIGATION_PIPE') ? Configuration::get('PS_NAVIGATION_PIPE') : '>');
		$this->context->smarty->assign('navigationPipe', $navigationPipe);

		// Automatically redirect to the canonical URL if needed
		if (!empty($this->php_self) && !Tools::getValue('ajax'))
			$this->canonicalRedirection($this->context->link->getPageLink($this->php_self, $this->ssl, $this->context->language->id));

		Product::initPricesComputation();

		$display_tax_label = $this->context->country->display_tax_label;
		if ($cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')})
		{
			$infos = Address::getCountryAndState((int)($cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
			$country = new Country((int)$infos['id_country']);
			if (Validate::isLoadedObject($country))
				$display_tax_label = $country->display_tax_label;
		}

		$this->context->smarty->assign(array(
			'link' => $link,
			'cart' => $cart,
			'currency' => $currency,
			'cookie' => $this->context->cookie,
			'page_name' => $page_name,
			'base_dir' => _PS_BASE_URL_.__PS_BASE_URI__,
			'base_dir_ssl' => $protocol_link.Tools::getShopDomainSsl().__PS_BASE_URI__,
			'content_dir' => $protocol_content.Tools::getServerName().__PS_BASE_URI__,
			'tpl_dir' => _PS_THEME_DIR_,
			'modules_dir' => _MODULE_DIR_,
			'mail_dir' => _MAIL_DIR_,
			'lang_iso' => $this->context->language->iso_code,
			'come_from' => Tools::getHttpHost(true, true).Tools::htmlentitiesUTF8(str_replace('\'', '', urldecode($_SERVER['REQUEST_URI']))),
			'cart_qties' => (int)$cart->nbProducts(),
			'currencies' => Currency::getCurrencies(),
			'languages' => Language::getLanguages(true, $this->context->shop->getID()),
			'priceDisplay' => Product::getTaxCalculationMethod(),
			'add_prod_display' => (int)Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
			'shop_name' => Configuration::get('PS_SHOP_NAME'),
			'roundMode' => (int)Configuration::get('PS_PRICE_ROUND_MODE'),
			'use_taxes' => (int)Configuration::get('PS_TAX'),
			'display_tax_label' => (bool)$display_tax_label,
			'vat_management' => (int)Configuration::get('VATNUMBER_MANAGEMENT'),
			'opc' => (bool)Configuration::get('PS_ORDER_PROCESS_TYPE'),
			'PS_CATALOG_MODE' => (bool)Configuration::get('PS_CATALOG_MODE') || !(bool)Group::getCurrent()->show_prices,
			'b2b_enable' => (bool)Configuration::get('PS_B2B_ENABLE')
		));

		// Deprecated
		$this->context->smarty->assign(array(
			'id_currency_cookie' => (int)$currency->id,
			'logged' => $this->context->customer->isLogged(),
			'customerName' => ($this->context->customer->logged ? $this->context->cookie->customer_firstname.' '.$this->context->cookie->customer_lastname : false)
		));

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
			if (substr($assignValue, 0, 1) == '/' || $protocol_content == 'https://')
				$this->context->smarty->assign($assignKey, $protocol_content.Tools::getMediaServer($assignValue).$assignValue);
			else
				$this->context->smarty->assign($assignKey, $assignValue);

		/*
		 * These shortcuts are DEPRECATED as of version 1.5.
		 * Use the Context to access objects instead.
		 * Example: $this->context->cart
		 */
		self::$cookie = $this->context->cookie;
		self::$cart = $cart;
		self::$smarty = $this->context->smarty;
		self::$link = $link;
		$defaultCountry = $this->context->country;

		if ($this->maintenance)
			$this->displayMaintenancePage();
		if ($this->restrictedCountry)
			$this->displayRestrictedCountryPage();

		//live edit
		if (Tools::isSubmit('live_edit') && ($ad = Tools::getValue('ad')) && (Tools::getValue('liveToken') == sha1(Tools::getValue('ad')._COOKIE_KEY_)))
			if (!is_dir(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.$ad))
				die(Tools::displayError());

		$this->iso = $iso;
		$this->setMedia();

		$this->context->cart = $cart;
		$this->context->currency = $currency;
	}

	public function postProcess()
	{
		/*// For retrocompatibility with versions before 1.5, preProcess support will be removed on next release
		if (method_exists(get_class($this), 'preProcess'))
		{
			$reflection = new ReflectionClass($this);
			if (!in_array($reflection->getMethod('preProcess')->class, array('FrontController', 'FrontControllerCore')))
			{
				Tools::displayAsDeprecated('Method preProcess() is deprecated in controllers, use method postProcess() instead');
				$this->preProcess();
			}
		}*/

		//$this->preProcess();
	}

	public function preProcess()
	{
	}

	public function initContent()
	{
		$this->process();

		$this->context->smarty->assign(array(
			'HOOK_HEADER' => Hook::exec('displayHeader'),
			'HOOK_TOP' => Hook::exec('displayTop'),
			'HOOK_LEFT_COLUMN' => ($this->display_column_left ? Hook::exec('displayLeftColumn') : ''),
			'HOOK_RIGHT_COLUMN' => ($this->display_column_right ? Hook::exec('displayRightColumn', array('cart' => $this->context->cart)) : ''),
		));
	}

	/**
	 * @deprecated 1.5.0
	 */
	public function displayHeader($display = true)
	{
		// This method will be removed in 1.6
		Tools::displayAsDeprecated();
		$this->initHeader();
 		$hook_header = Hook::exec('displayHeader');
		if ((Configuration::get('PS_CSS_THEME_CACHE') || Configuration::get('PS_JS_THEME_CACHE')) && is_writable(_PS_THEME_DIR_.'cache'))
		{
			// CSS compressor management
			if (Configuration::get('PS_CSS_THEME_CACHE'))
				$this->css_files = Media::cccCSS($this->css_files);
			//JS compressor management
			if (Configuration::get('PS_JS_THEME_CACHE'))
				$this->js_files = Media::cccJs($this->js_files);
		}

 		$this->context->smarty->assign('css_files', $this->css_files);
		$this->context->smarty->assign('js_files', array_unique($this->js_files));

		$this->context->smarty->assign(array(
			'HOOK_HEADER' => $hook_header,
			'HOOK_TOP' => Hook::exec('displayTop'),
		));

		$this->display_header = $display;
		$this->context->smarty->display(_PS_THEME_DIR_.'header.tpl');

	}

	/**
	 * @deprecated 1.5.0
	 */
	public function displayFooter($display = true)
	{
		// This method will be removed in 1.6
		Tools::displayAsDeprecated();
		$this->context->smarty->assign(array(
			'HOOK_RIGHT_COLUMN' => Hook::exec('displayRightColumn', array('cart' => $this->context->cart)),
			'HOOK_FOOTER' => Hook::exec('displayFooter'),
		));
		$this->context->smarty->display(_PS_THEME_DIR_.'footer.tpl');
	}

	public function initCursedPage()
	{
		return $this->displayMaintenancePage();
	}

	public function process()
	{
	}

	public function redirect()
	{
		Tools::redirectLink($this->redirect_after);
	}

	public function display()
	{
		Tools::safePostVars();

		// assign css_files and js_files at the very last time
		if ((Configuration::get('PS_CSS_THEME_CACHE') || Configuration::get('PS_JS_THEME_CACHE')) && is_writable(_PS_THEME_DIR_.'cache'))
		{
			// CSS compressor management
			if (Configuration::get('PS_CSS_THEME_CACHE'))
				$this->css_files = Media::cccCSS($this->css_files);
			//JS compressor management
			if (Configuration::get('PS_JS_THEME_CACHE'))
				$this->js_files = Media::cccJs($this->js_files);
		}

 		$this->context->smarty->assign('css_files', $this->css_files);
		$this->context->smarty->assign('js_files', array_unique($this->js_files));
		$this->context->smarty->assign(array(
			'errors' => $this->errors,
			'display_header' => $this->display_header,
			'display_footer' => $this->display_footer,
		));

		if (Tools::isSubmit('live_edit'))
			$this->context->smarty->assign('live_edit', $this->getLiveEditFooter());
		
		// handle 1.4 theme (with layout.tpl missing)
		if (file_exists(_PS_THEME_DIR_.'layout.tpl'))
		{
			if ($this->template)
				$this->context->smarty->assign('template', $this->context->smarty->fetch($this->template));
			$this->context->smarty->display(_PS_THEME_DIR_.'layout.tpl');
		}
		else
		{
			// BEGIN - 1.4 retrocompatibility - will be removed in 1.6
			Tools::displayAsDeprecated('layout.tpl is missing in your theme directory');
			if ($this->display_header)
				$this->context->smarty->display(_PS_THEME_DIR_.'header.tpl');

			if ($this->template)
				$this->context->smarty->display($this->template);
		
			if ($this->display_footer)
				$this->context->smarty->display(_PS_THEME_DIR_.'footer.tpl');

			// live edit
			if (Tools::isSubmit('live_edit') && ($ad = Tools::getValue('ad')) && (Tools::getValue('liveToken') == sha1(Tools::getValue('ad')._COOKIE_KEY_)))
			{
				$this->context->smarty->assign(array('ad' => $ad, 'live_edit' => true));
				$this->context->smarty->display(_PS_ALL_THEMES_DIR_.'live_edit.tpl');
			}
			// END - 1.4 retrocompatibility - will be removed in 1.6
		}

		return true;
	}

	/* Display a maintenance page if shop is closed */
	protected function displayMaintenancePage()
	{
		if (!in_array(Tools::getRemoteAddr(), explode(',', Configuration::get('PS_MAINTENANCE_IP'))))
		{
			header('HTTP/1.1 503 temporarily overloaded');
			$this->context->smarty->assign('favicon_url', _PS_IMG_.Configuration::get('PS_FAVICON'));
			$this->context->smarty->display(_PS_THEME_DIR_.'maintenance.tpl');
			exit;
		}
	}

	/* Display a specific page if the user country is not allowed */
	protected function displayRestrictedCountryPage()
	{
		header('HTTP/1.1 503 temporarily overloaded');
		$this->context->smarty->assign('favicon_url', _PS_IMG_.Configuration::get('PS_FAVICON'));
		$this->context->smarty->display(_PS_THEME_DIR_.'restricted-country.tpl');
		exit;
	}

	protected function canonicalRedirection($canonicalURL = '')
	{
		if (!$canonicalURL || !Configuration::get('PS_CANONICAL_REDIRECT') || strtoupper($_SERVER['REQUEST_METHOD']) != 'GET')
			return;

		$matchUrl = (($this->ssl && Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$matchUrl = rawurldecode($matchUrl);
		if (!preg_match('/^'.Tools::pRegexp($canonicalURL, '/').'([&?].*)?$/', $matchUrl))
		{
			$params = array();
			$excludedKey = array('isolang', 'id_lang', 'controller', 'fc');
			foreach ($_GET as $key => $value)
				if (!in_array($key, $excludedKey))
					$params[] = $key.'='.$value;

			$strParams = '';
			if ($params)
				$strParams = ((strpos($canonicalURL, '?') === false) ? '?' : '&').implode('&', $params);

			header('HTTP/1.0 301 Moved');
			header('Cache-Control: no-cache');
			if (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_ && $_SERVER['REQUEST_URI'] != __PS_BASE_URI__)
				die('[Debug] This page has moved<br />Please use the following URL instead: <a href="'.$canonicalURL.Tools::safeOutput($strParams).'">'.$canonicalURL.Tools::safeOutput($strParams).'</a>');
			Tools::redirectLink($canonicalURL.$strParams);
		}
	}

	protected function geolocationManagement($defaultCountry)
	{
		if (!in_array($_SERVER['SERVER_NAME'], array('localhost', '127.0.0.1')))
		{
			/* Check if Maxmind Database exists */
			if (file_exists(_PS_GEOIP_DIR_.'GeoLiteCity.dat'))
			{
				if (!isset($this->context->cookie->iso_code_country) || (isset($this->context->cookie->iso_code_country) && !in_array(strtoupper($this->context->cookie->iso_code_country), explode(';', Configuration::get('PS_ALLOWED_COUNTRIES')))))
				{
					include_once(_PS_GEOIP_DIR_.'geoipcity.inc');
					include_once(_PS_GEOIP_DIR_.'geoipregionvars.php');

					$gi = geoip_open(realpath(_PS_GEOIP_DIR_.'GeoLiteCity.dat'), GEOIP_STANDARD);
					$record = geoip_record_by_addr($gi, '81.57.72.226');//Tools::getRemoteAddr());

					if (is_object($record))
					{
						if (!in_array(strtoupper($record->country_code), explode(';', Configuration::get('PS_ALLOWED_COUNTRIES'))) && !FrontController::isInWhitelistForGeolocation())
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
					if ($defaultCountry->iso_code != $this->context->cookie->iso_code_country)
						$defaultCountry = new Country($id_country);
					if (isset($hasBeenSet) && $hasBeenSet)
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

	public function setMedia()
	{
		$this->addCSS(_THEME_CSS_DIR_.'global.css', 'all');
		$this->addjquery();
		$this->addjqueryPlugin('easing');
		$this->addJS(_PS_JS_DIR_.'tools.js');

		if (Tools::isSubmit('live_edit') && Tools::getValue('ad') && (Tools::getValue('liveToken') == sha1(Tools::getValue('ad')._COOKIE_KEY_)))
		{
			$this->addJqueryUI('ui.sortable');
			$this->addjqueryPlugin('fancybox');
			$this->addJS(_PS_JS_DIR_.'hookLiveEdit.js');
			$this->addCSS(_PS_CSS_DIR_.'jquery.fancybox-1.3.4.css', 'all'); // @TODO
		}
		if ($this->context->language->is_rtl)
			$this->addCSS(_THEME_CSS_DIR_.'rtl.css');
	}

	public function initHeader()
	{
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
			'content_only' => (int)Tools::getValue('content_only'),
			'logo_url' => _PS_IMG_.Configuration::get('PS_LOGO').'?'.Configuration::get('PS_IMG_UPDATE_TIME'),
			'favicon_url' => _PS_IMG_.Configuration::get('PS_FAVICON'),
		));
	}

	public function initFooter()
	{
		$this->context->smarty->assign(array(
			'HOOK_FOOTER' => Hook::exec('displayFooter'),
		));

	}

	public function getLiveEditFooter()
	{
		if (Tools::isSubmit('live_edit')
			&& ($ad = Tools::getValue('ad'))
			&& (Tools::getValue('liveToken') == sha1(Tools::getValue('ad')._COOKIE_KEY_)))
		{
			$data = $this->context->smarty->createData();
			$data->assign(array(
				'ad' => $ad, 
				'live_edit' => true,
				'hook_list' => Hook::$executed_hooks,
				'id_shop' => $this->context->shop->getId(true)
			));
			return $this->context->smarty->createTemplate(_PS_ALL_THEMES_DIR_.'live_edit.tpl', $data)->fetch();
		}
		else 
			return '';
	}

	public function productSort()
	{
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
		elseif (!$this->context)
			$this->context = Context::getContext();

		$nArray = (int)(Configuration::get('PS_PRODUCTS_PER_PAGE')) != 10 ? array((int)(Configuration::get('PS_PRODUCTS_PER_PAGE')), 10, 20, 50) : array(10, 20, 50);
		// Clean duplicate values
		$nArray = array_unique($nArray);
		asort($nArray);
		$this->n = abs((int)(Tools::getValue('n', ((isset($this->context->cookie->nb_item_per_page) && $this->context->cookie->nb_item_per_page >= 10) ? $this->context->cookie->nb_item_per_page : (int)(Configuration::get('PS_PRODUCTS_PER_PAGE'))))));
		$this->p = abs((int)(Tools::getValue('p', 1)));

		$current_url = tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']);
		//delete parameter page
		$current_url = preg_replace('/(\?)?(&amp;)?p=\d+/', '$1', $current_url);

		$range = 2; /* how many pages around page selected */

		if ($this->p < 0)
			$this->p = 0;

		if (isset($this->context->cookie->nb_item_per_page) && $this->n != $this->context->cookie->nb_item_per_page && in_array($this->n, $nArray))
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
			'products_per_page' => (int)Configuration::get('PS_PRODUCTS_PER_PAGE'),
			'pages_nb' => $pages_nb,
			'p' => $this->p,
			'n' => $this->n,
			'nArray' => $nArray,
			'range' => $range,
			'start' => $start,
			'stop' => $stop,
			'current_url' => $current_url
		);
		$this->context->smarty->assign($pagination_infos);
	}

	public static function getCurrentCustomerGroups()
	{
		if (!Group::isFeatureActive())
			return array();

		$context = Context::getContext();
		if (!$context->customer->id)
			return array();
		if (!is_array(self::$currentCustomerGroups))
		{
			self::$currentCustomerGroups = array();
			$result = Db::getInstance()->executeS('SELECT id_group FROM '._DB_PREFIX_.'customer_group WHERE id_customer = '.(int)$context->customer->id);
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
		if (is_array($ips) && count($ips))
			foreach ($ips as $ip)
				if (!empty($ip) && strpos($userIp, $ip) === 0)
					$allowed = true;
		return $allowed;
	}

	/**
	 * Check if token is valid
	 *
	 * @since 1.5.0
	 * @return bool
	 */
	public function isTokenValid()
	{
		return Configuration::get('PS_TOKEN_ENABLE') && strcasecmp(Tools::getToken(false), Tools::getValue('token')) && $this->context->customer->isLogged();
	}

	/**
	 * Add one or several CSS for front, checking if css files are overriden in theme/css/modules/ directory
	 *
	 * @see Controller::addCSS()
	 */
	public function addCSS($css_uri, $css_media_type = 'all')
	{
		if (!is_array($css_uri))
			$css_uri = array($css_uri => $css_media_type);

		$list_uri = array();
		foreach ($css_uri as $file => $media)
		{
			$different = 0;
			$override_path = str_replace(__PS_BASE_URI__.'modules/', _PS_ROOT_DIR_.'/themes/'._THEME_NAME_.'/css/modules/', $file, $different);
			if ($different && file_exists($override_path))
				$file = str_replace(__PS_BASE_URI__.'modules/', __PS_BASE_URI__.'themes/'._THEME_NAME_.'/css/modules/', $file, $different);
			$list_uri[$file] = $media;
		}

		return parent::addCSS($list_uri, $css_media_type);
	}

	/**
	 * Add one or several JS files for front, checking if js files are overriden in theme/js/modules/ directory
	 *
	 * @see Controller::addJS()
	 */
	public function addJS($js_uri)
	{
		if (!is_array($js_uri))
			$js_uri = array($js_uri);

		foreach ($js_uri as $key => &$file)
		{
			if (!preg_match('/^http(s?):\/\//i', $file))
			{
				$different = 0;
				$override_path = str_replace(__PS_BASE_URI__.'modules/', _PS_ROOT_DIR_.'/themes/'._THEME_NAME_.'/js/modules/', $file, $different);
				if ($different && file_exists($override_path))
					$file = str_replace(__PS_BASE_URI__.'modules/', __PS_BASE_URI__.'themes/'._THEME_NAME_.'/js/modules/', $file, $different);
			}
		}

		return parent::addJS($js_uri);
	}

	protected function recoverCart()
	{
		if (($id_cart = (int)Tools::getValue('recover_cart')) && Tools::getValue('token_cart') == md5(_COOKIE_KEY_.'recover_cart_'.$id_cart))
		{
			$cart = new Cart((int)$id_cart);
			if (Validate::isLoadedObject($cart))
			{
				$customer = new Customer((int)$cart->id_customer);
				if (Validate::isLoadedObject($customer))
				{
					$this->context->cookie->id_customer = (int)$customer->id;
					$this->context->cookie->customer_lastname = $customer->lastname;
					$this->context->cookie->customer_firstname = $customer->firstname;
					$this->context->cookie->logged = 1;
					$this->context->cookie->is_guest = $customer->isGuest();
					$this->context->cookie->passwd = $customer->passwd;
					$this->context->cookie->email = $customer->email;
					return $id_cart;
				}
			}
		}
		else
			return false;
	}
}

