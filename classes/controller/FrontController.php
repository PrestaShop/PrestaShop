<?php
/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
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
		$this->controller_type = 'front';

		global $useSSL;

		parent::__construct();

		if (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'))
			$this->ssl = true;

		if (isset($useSSL))
			$this->ssl = $useSSL;
		else
			$useSSL = $this->ssl;

		if (isset($this->php_self)  && is_object(Context::getContext()->theme))
		{
			$colums = Context::getContext()->theme->hasColumns($this->php_self);
			// don't use theme tables if not configurated in DB
			if ($colums)
			{
				$this->display_column_left = $colums['left_column'];
				$this->display_column_right = $colums['right_column'];
			}
		}
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

		// If current URL use SSL, set it true (used a lot for module redirect)
		if (Tools::usingSecureMode())
			$useSSL = true;

		// For compatibility with globals, DEPRECATED as of version 1.5
		$css_files = $this->css_files;
		$js_files = $this->js_files;

		// If we call a SSL controller without SSL or a non SSL controller with SSL, we redirect with the right protocol
		if (Configuration::get('PS_SSL_ENABLED') && $_SERVER['REQUEST_METHOD'] != 'POST' && $this->ssl != Tools::usingSecureMode())
		{	
			header('HTTP/1.1 301 Moved Permanently');
			header('Cache-Control: no-cache');
			if ($this->ssl)					
				header('Location: '.Tools::getShopDomainSsl(true).$_SERVER['REQUEST_URI']);
			else						
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

		// Init cookie language
		// @TODO This method must be moved into switchLanguage
		Tools::setCookieLanguage($this->context->cookie);

		$protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
		$useSSL = ((isset($this->ssl) && $this->ssl && Configuration::get('PS_SSL_ENABLED')) || Tools::usingSecureMode()) ? true : false;
		$protocol_content = ($useSSL) ? 'https://' : 'http://';
		$link = new Link($protocol_link, $protocol_content);
		$this->context->link = $link;

		if ($id_cart = (int)$this->recoverCart())
			$this->context->cookie->id_cart = (int)$id_cart;

		if ($this->auth && !$this->context->customer->isLogged($this->guestAllowed))
			Tools::redirect('index.php?controller=authentication'.($this->authRedirection ? '&back='.$this->authRedirection : ''));

		/* Theme is missing */
		if (!is_dir(_PS_THEME_DIR_))
			throw new PrestaShopException((sprintf(Tools::displayError('Current theme unavailable "%s". Please check your theme directory name and permissions.'), basename(rtrim(_PS_THEME_DIR_, '/\\')))));

		if (Configuration::get('PS_GEOLOCATION_ENABLED'))
			if (($newDefault = $this->geolocationManagement($this->context->country)) && Validate::isLoadedObject($newDefault))
				$this->context->country = $newDefault;

		$currency = Tools::setCurrency($this->context->cookie);

		if (isset($_GET['logout']) || ($this->context->customer->logged && Customer::isBanned($this->context->customer->id)))
		{
			$this->context->customer->logout();

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
			{
				unset($this->context->cookie->id_cart, $cart, $this->context->cookie->checkedTOS);
				$this->context->cookie->check_cgv = false;
			}
			/* Delete product of cart, if user can't make an order from his country */
			elseif (intval(Configuration::get('PS_GEOLOCATION_ENABLED')) &&
					!in_array(strtoupper($this->context->cookie->iso_code_country), explode(';', Configuration::get('PS_ALLOWED_COUNTRIES'))) &&
					$cart->nbProducts() && intval(Configuration::get('PS_GEOLOCATION_NA_BEHAVIOR')) != -1 &&
					!FrontController::isInWhitelistForGeolocation() &&
					!in_array($_SERVER['SERVER_NAME'], array('localhost', '127.0.0.1')))
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
			$cart->id_shop_group = (int)$this->context->shop->id_shop_group;
			$cart->id_shop = $this->context->shop->id;
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

			// Needed if the merchant want to give a free product to every visitors
			$this->context->cart = $cart;
			CartRule::autoAddToCart($this->context);
		}
		else
			$this->context->cart = $cart;	

		/* get page name to display it in body id */

		// Are we in a payment module
		$module_name = '';
		if (Validate::isModuleName(Tools::getValue('module')))
			$module_name = Tools::getValue('module');

		if (!empty($this->page_name))
			$page_name = $this->page_name;
		elseif (!empty($this->php_self))
			$page_name = $this->php_self;
		elseif (Tools::getValue('fc') == 'module' && $module_name != '' && (Module::getInstanceByName($module_name) instanceof PaymentModule))
			$page_name = 'module-payment-submit';
		// @retrocompatibility Are we in a module ?
		elseif (preg_match('#^'.preg_quote($this->context->shop->physical_uri, '#').'modules/([a-zA-Z0-9_-]+?)/(.*)$#', $_SERVER['REQUEST_URI'], $m))
			$page_name = 'module-'.$m[1].'-'.str_replace(array('.php', '/'), array('', '-'), $m[2]);
		else
		{
			$page_name = Dispatcher::getInstance()->getController();
			$page_name = (preg_match('/^[0-9]/', $page_name) ? 'page_'.$page_name : $page_name);
		}

		$this->context->smarty->assign(Meta::getMetaTags($this->context->language->id, $page_name));
		$this->context->smarty->assign('request_uri', Tools::safeOutput(urldecode($_SERVER['REQUEST_URI'])));

		/* Breadcrumb */
		$navigationPipe = (Configuration::get('PS_NAVIGATION_PIPE') ? Configuration::get('PS_NAVIGATION_PIPE') : '>');
		$this->context->smarty->assign('navigationPipe', $navigationPipe);

		// Automatically redirect to the canonical URL if needed
		if (!empty($this->php_self) && !Tools::getValue('ajax'))
			$this->canonicalRedirection($this->context->link->getPageLink($this->php_self, $this->ssl, $this->context->language->id));

		Product::initPricesComputation();

		$display_tax_label = $this->context->country->display_tax_label;
		if (isset($cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) && $cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')})
		{
			$infos = Address::getCountryAndState((int)($cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
			$country = new Country((int)$infos['id_country']);
			$this->context->country = $country;
			if (Validate::isLoadedObject($country))
				$display_tax_label = $country->display_tax_label;
		}

		$languages = Language::getLanguages(true, $this->context->shop->id);
		$meta_language = array();
		foreach ($languages as $lang)
			$meta_language[] = $lang['iso_code'];

		$compared_products = array();
		if (Configuration::get('PS_COMPARATOR_MAX_ITEM') && isset($this->context->cookie->id_compare))
			$compared_products = CompareProduct::getCompareProducts($this->context->cookie->id_compare);

		$this->context->smarty->assign(array(
			// Usefull for layout.tpl
			'mobile_device' => $this->context->getMobileDevice(),
			'link' => $link,
			'cart' => $cart,
			'currency' => $currency,
			'cookie' => $this->context->cookie,
			'page_name' => $page_name,
			'hide_left_column' => !$this->display_column_left,
			'hide_right_column' => !$this->display_column_right,
			'base_dir' => _PS_BASE_URL_.__PS_BASE_URI__,
			'base_dir_ssl' => $protocol_link.Tools::getShopDomainSsl().__PS_BASE_URI__,
			'content_dir' => $protocol_content.Tools::getHttpHost().__PS_BASE_URI__,
			'base_uri' => $protocol_content.Tools::getHttpHost().__PS_BASE_URI__.(!Configuration::get('PS_REWRITING_SETTINGS') ? 'index.php' : ''),
			'tpl_dir' => _PS_THEME_DIR_,
			'modules_dir' => _MODULE_DIR_,
			'mail_dir' => _MAIL_DIR_,
			'lang_iso' => $this->context->language->iso_code,
			'come_from' => Tools::getHttpHost(true, true).Tools::htmlentitiesUTF8(str_replace(array('\'', '\\'), '', urldecode($_SERVER['REQUEST_URI']))),
			'cart_qties' => (int)$cart->nbProducts(),
			'currencies' => Currency::getCurrencies(),
			'languages' => $languages,
			'meta_language' => implode(',', $meta_language),
			'priceDisplay' => Product::getTaxCalculationMethod((int)$this->context->cookie->id_customer),
			'is_logged' => (bool)$this->context->customer->isLogged(),
			'is_guest' => (bool)$this->context->customer->isGuest(),
			'add_prod_display' => (int)Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
			'shop_name' => Configuration::get('PS_SHOP_NAME'),
			'roundMode' => (int)Configuration::get('PS_PRICE_ROUND_MODE'),
			'use_taxes' => (int)Configuration::get('PS_TAX'),
			'show_taxes' => (int)(Configuration::get('PS_TAX_DISPLAY') == 1 && (int)Configuration::get('PS_TAX')),
			'display_tax_label' => (bool)$display_tax_label,
			'vat_management' => (int)Configuration::get('VATNUMBER_MANAGEMENT'),
			'opc' => (bool)Configuration::get('PS_ORDER_PROCESS_TYPE'),
			'PS_CATALOG_MODE' => (bool)Configuration::get('PS_CATALOG_MODE') || !(bool)Group::getCurrent()->show_prices,
			'b2b_enable' => (bool)Configuration::get('PS_B2B_ENABLE'),
			'request' => $link->getPaginationLink(false, false, false, true),
			'PS_STOCK_MANAGEMENT' => Configuration::get('PS_STOCK_MANAGEMENT'),
			'quick_view' => (bool)Configuration::get('PS_QUICK_VIEW'),
			'shop_phone' => Configuration::get('PS_SHOP_PHONE'),
			'compared_products' => is_array($compared_products) ? $compared_products : array(),
			'comparator_max_item' => (int)Configuration::get('PS_COMPARATOR_MAX_ITEM')
		));

		// Add the tpl files directory for mobile
		if ($this->useMobileTheme())
			$this->context->smarty->assign(array(
				'tpl_mobile_uri' => _PS_THEME_MOBILE_DIR_,
			));

		// Deprecated
		$this->context->smarty->assign(array(
			'id_currency_cookie' => (int)$currency->id,
			'logged' => $this->context->customer->isLogged(),
			'customerName' => ($this->context->customer->logged ? $this->context->cookie->customer_firstname.' '.$this->context->cookie->customer_lastname : false)
		));

		$assign_array = array(
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

		// Add the images directory for mobile
		if ($this->useMobileTheme())
			$assign_array['img_mobile_dir'] = _THEME_MOBILE_IMG_DIR_;

		// Add the CSS directory for mobile
		if ($this->useMobileTheme())
			$assign_array['css_mobile_dir'] = _THEME_MOBILE_CSS_DIR_;

		foreach ($assign_array as $assign_key => $assign_value)
			if (substr($assign_value, 0, 1) == '/' || $protocol_content == 'https://')
				$this->context->smarty->assign($assign_key, $protocol_content.Tools::getMediaServer($assign_value).$assign_value);
			else
				$this->context->smarty->assign($assign_key, $assign_value);

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

		$this->displayMaintenancePage();
		if ($this->restrictedCountry)
			$this->displayRestrictedCountryPage();

		if (Tools::isSubmit('live_edit') && !$this->checkLiveEditAccess())
			Tools::redirect('index.php?controller=404');

		$this->iso = $iso;

		$this->context->cart = $cart;
		$this->context->currency = $currency;
	}
	
	public function postProcess()
	{
	}

	public function initContent()
	{
		$this->process();
		if (!isset($this->context->cart))
			$this->context->cart = new Cart();
		if (!$this->useMobileTheme())
		{
			// These hooks aren't used for the mobile theme.
			// Needed hooks are called in the tpl files.
			$this->context->smarty->assign(array(
				'HOOK_HEADER' => Hook::exec('displayHeader'),
				'HOOK_TOP' => Hook::exec('displayTop'),
				'HOOK_LEFT_COLUMN' => ($this->display_column_left ? Hook::exec('displayLeftColumn') : ''),
				'HOOK_RIGHT_COLUMN' => ($this->display_column_right ? Hook::exec('displayRightColumn', array('cart' => $this->context->cart)) : ''),
			));
		}
		else
			$this->context->smarty->assign('HOOK_MOBILE_HEADER', Hook::exec('displayMobileHeader'));
	}

	/**
	 * @deprecated 1.5.0
	 */
	public function displayHeader($display = true)
	{
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

		// Call hook before assign of css_files and js_files in order to include correctly all css and javascript files
		$this->context->smarty->assign(array(
			'HOOK_HEADER' => $hook_header,
			'HOOK_TOP' => Hook::exec('displayTop'),
			'HOOK_LEFT_COLUMN' => ($this->display_column_left ? Hook::exec('displayLeftColumn') : ''),
			'HOOK_RIGHT_COLUMN' => ($this->display_column_right ? Hook::exec('displayRightColumn', array('cart' => $this->context->cart)) : ''),
			'HOOK_FOOTER' => Hook::exec('displayFooter')
		));

		$this->context->smarty->assign(array(
			'css_files' => $this->css_files,
			'js_files' => $this->getLayout() ? array() : $this->js_files
		));

		$this->display_header = $display;
		$this->smartyOutputContent(_PS_THEME_DIR_.'header.tpl');

	}

	/**
	 * @deprecated 1.5.0
	 */
	public function displayFooter($display = true)
	{
		Tools::displayAsDeprecated();
		$this->smartyOutputContent(_PS_THEME_DIR_.'footer.tpl');
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

	/**
	 * 1.4 retrocompatibility
	 */
	public function displayContent()
	{
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
			if (Configuration::get('PS_JS_THEME_CACHE') && !$this->useMobileTheme())
				$this->js_files = Media::cccJs($this->js_files);
		}

		$this->context->smarty->assign(array(
			'css_files' => $this->css_files,
			'js_files' => $this->getLayout() ? array() : $this->js_files,
			'errors' => $this->errors,
			'display_header' => $this->display_header,
			'display_footer' => $this->display_footer,
		));
				
		$layout = $this->getLayout();
		if ($layout)
		{
			if ($this->template)
				$template = $this->context->smarty->fetch($this->template);
			else // For retrocompatibility with 1.4 controller
			{
				ob_start();
				$this->displayContent();
				$template = ob_get_contents();
				ob_clean();

			}
			$template = $this->context->smarty->assign('template', $template);
			$this->smartyOutputContent($layout);
		}
		else
		{
			Tools::displayAsDeprecated('layout.tpl is missing in your theme directory');
			if ($this->display_header)
				$this->smartyOutputContent(_PS_THEME_DIR_.'header.tpl');

			if ($this->template)
				$this->smartyOutputContent($this->template);
			else // For retrocompatibility with 1.4 controller
				$this->displayContent();

			if ($this->display_footer)
				$this->smartyOutputContent(_PS_THEME_DIR_.'footer.tpl');
		}
		return true;
	}

	/* Display a maintenance page if shop is closed */
	protected function displayMaintenancePage()
	{
		if ($this->maintenance == true || !(int)Configuration::get('PS_SHOP_ENABLE'))
		{
			$this->maintenance = true;
			if (!in_array(Tools::getRemoteAddr(), explode(',', Configuration::get('PS_MAINTENANCE_IP'))))
			{
				header('HTTP/1.1 503 temporarily overloaded');
				
				$this->context->smarty->assign($this->initLogoAndFavicon());
				$this->context->smarty->assign(array(
					'HOOK_MAINTENANCE' => Hook::exec('displayMaintenance', array()),
				));

				$this->smartyOutputContent($this->getTemplatePath($this->getThemeDir().'maintenance.tpl'));
				exit;
			}
		}
	}

	/* Display a specific page if the user country is not allowed */
	protected function displayRestrictedCountryPage()
	{
		header('HTTP/1.1 503 temporarily overloaded');
		$this->context->smarty->assign(array(
			'shop_name' => Context::getContext()->shop->name,
			'favicon_url' => _PS_IMG_.Configuration::get('PS_FAVICON'),
			'logo_url' =>  self::$link->getMediaLink(_PS_IMG_.Configuration::get('PS_LOGO'))
		));
		$this->smartyOutputContent($this->getTemplatePath($this->getThemeDir().'restricted-country.tpl'));
		exit;
	}

	protected function canonicalRedirection($canonical_url = '')
	{
		if (!$canonical_url || !Configuration::get('PS_CANONICAL_REDIRECT') || strtoupper($_SERVER['REQUEST_METHOD']) != 'GET' || Tools::getValue('live_edit'))
			return;

		$match_url = rawurldecode(Tools::getCurrentUrlProtocolPrefix().$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		if (!preg_match('/^'.Tools::pRegexp(rawurldecode($canonical_url), '/').'([&?].*)?$/', $match_url))
		{
			$params = array();
			$str_params = '';
			$url_details = parse_url($canonical_url);

			if (!empty($url_details['query']))
			{
				parse_str($url_details['query'], $query);
				foreach ($query as $key => $value)
					$params[Tools::safeOutput($key)] = Tools::safeOutput($value);
			}
			$excluded_key = array('isolang', 'id_lang', 'controller', 'fc', 'id_product', 'id_category', 'id_manufacturer', 'id_supplier', 'id_cms');
			foreach ($_GET as $key => $value)
				if (!in_array($key, $excluded_key) && Validate::isUrl($key) && Validate::isUrl($value))
					$params[Tools::safeOutput($key)] = Tools::safeOutput($value);

			$str_params = http_build_query($params, '', '&');
			if (!empty($str_params))
				$final_url = preg_replace('/^([^?]*)?.*$/', '$1', $canonical_url).'?'.$str_params;
			else
				$final_url = preg_replace('/^([^?]*)?.*$/', '$1', $canonical_url);

			// Don't send any cookie
			Context::getContext()->cookie->disallowWriting();

			if (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_ && $_SERVER['REQUEST_URI'] != __PS_BASE_URI__)
				die('[Debug] This page has moved<br />Please use the following URL instead: <a href="'.$final_url.'">'.$final_url.'</a>');

			$redirect_type = Configuration::get('PS_CANONICAL_REDIRECT') == 2 ? '301' : '302';
			header('HTTP/1.0 '.$redirect_type.' Moved');
			header('Cache-Control: no-cache');
			Tools::redirectLink($final_url);
		}
	}

	protected function geolocationManagement($default_country)
	{
		if (!in_array($_SERVER['SERVER_NAME'], array('localhost', '127.0.0.1')))
		{
			/* Check if Maxmind Database exists */
			if (file_exists(_PS_GEOIP_DIR_.'GeoLiteCity.dat'))
			{
				if (!isset($this->context->cookie->iso_code_country) || (isset($this->context->cookie->iso_code_country) && !in_array(strtoupper($this->context->cookie->iso_code_country), explode(';', Configuration::get('PS_ALLOWED_COUNTRIES')))))
				{
					include_once(_PS_GEOIP_DIR_.'geoipcity.inc');

					$gi = geoip_open(realpath(_PS_GEOIP_DIR_.'GeoLiteCity.dat'), GEOIP_STANDARD);
					$record = geoip_record_by_addr($gi, Tools::getRemoteAddr());

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
							$has_been_set = !isset($this->context->cookie->iso_code_country);
							$this->context->cookie->iso_code_country = strtoupper($record->country_code);
						}
					}
				}

				if (isset($this->context->cookie->iso_code_country) && $this->context->cookie->iso_code_country && !Validate::isLanguageIsoCode($this->context->cookie->iso_code_country))
					$this->context->cookie->iso_code_country = Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT'));
				if (isset($this->context->cookie->iso_code_country) && ($id_country = Country::getByIso(strtoupper($this->context->cookie->iso_code_country))))
				{
					/* Update defaultCountry */
					if ($default_country->iso_code != $this->context->cookie->iso_code_country)
						$default_country = new Country($id_country);
					if (isset($has_been_set) && $has_been_set)
						$this->context->cookie->id_currency = (int)(Currency::getCurrencyInstance($default_country->id_currency ? (int)$default_country->id_currency : Configuration::get('PS_CURRENCY_DEFAULT'))->id);
					return $default_country;
				}
				elseif (Configuration::get('PS_GEOLOCATION_NA_BEHAVIOR') == _PS_GEOLOCATION_NO_CATALOG_ && !FrontController::isInWhitelistForGeolocation())
					$this->restrictedCountry = true;
				elseif (Configuration::get('PS_GEOLOCATION_NA_BEHAVIOR') == _PS_GEOLOCATION_NO_ORDER_ && !FrontController::isInWhitelistForGeolocation())
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

	/**
	 * Specific medias for mobile device.
	 * if autoload directory is present in the mobile theme, these files will not be loaded
	 */
	public function setMobileMedia()
	{
		$this->addjquery();

		if (!file_exists($this->getThemeDir().'js/autoload/'))
		{
			$this->addJS(_THEME_MOBILE_JS_DIR_.'jquery.mobile-1.3.0.min.js');
			$this->addJS(_THEME_MOBILE_JS_DIR_.'jqm-docs.js');
			$this->addJS(_PS_JS_DIR_.'tools.js');
			$this->addJS(_THEME_MOBILE_JS_DIR_.'global.js');
			$this->addjqueryPlugin('fancybox');
		}

		if (!file_exists($this->getThemeDir().'js/autoload/'))
		{
			$this->addCSS(_THEME_MOBILE_CSS_DIR_.'jquery.mobile-1.3.0.min.css', 'all');
			$this->addCSS(_THEME_MOBILE_CSS_DIR_.'jqm-docs.css', 'all');
			$this->addCSS(_THEME_MOBILE_CSS_DIR_.'global.css', 'all');
		}
	}

	public function setMedia()
	{
		// if website is accessed by mobile device
		// @see FrontControllerCore::setMobileMedia()
		if ($this->useMobileTheme())
		{
			$this->setMobileMedia();
			return true;
		}

		$this->addCSS(_THEME_CSS_DIR_.'grid_prestashop.css', 'all');  // retro compat themes 1.5
		$this->addCSS(_THEME_CSS_DIR_.'global.css', 'all');
		$this->addjquery();
		$this->addjqueryPlugin('easing');
		$this->addJS(_PS_JS_DIR_.'tools.js');
		$this->addJS(_THEME_JS_DIR_.'global.js');

		// Automatically add js files from js/autoload directory in the template
		if (@filemtime($this->getThemeDir().'js/autoload/'))
			foreach (scandir($this->getThemeDir().'js/autoload/', 0) as $file)
				if (preg_match('/^[^.].*\.js$/', $file))
					$this->addJS($this->getThemeDir().'js/autoload/'.$file);
		// Automatically add css files from css/autoload directory in the template
		if (@filemtime($this->getThemeDir().'css/autoload/'))
			foreach (scandir($this->getThemeDir().'css/autoload', 0) as $file)
				if (preg_match('/^[^.].*\.css$/', $file))
					$this->addCSS($this->getThemeDir().'css/autoload/'.$file);

		if (Tools::isSubmit('live_edit') && Tools::getValue('ad') && Tools::getAdminToken('AdminModulesPositions'.(int)Tab::getIdFromClassName('AdminModulesPositions').(int)Tools::getValue('id_employee')))
		{
			$this->addJqueryUI('ui.sortable');
			$this->addjqueryPlugin('fancybox');
			$this->addJS(_PS_JS_DIR_.'hookLiveEdit.js');
		}

		if (Configuration::get('PS_QUICK_VIEW'))
			$this->addjqueryPlugin('fancybox');

		if (Configuration::get('PS_COMPARATOR_MAX_ITEM') > 0)
			$this->addJS(_THEME_JS_DIR_.'products-comparison.js');

		// Execute Hook FrontController SetMedia
		Hook::exec('actionFrontControllerSetMedia', array());
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
			'priceDisplayPrecision' => _PS_PRICE_DISPLAY_PRECISION_,
			'content_only' => (int)Tools::getValue('content_only'),
		));
				
		$this->context->smarty->assign($this->initLogoAndFavicon());
	}

	public function initFooter()
	{
		$this->context->smarty->assign(array(
			'HOOK_FOOTER' => Hook::exec('displayFooter'),
			'conditions' => Configuration::get('PS_CONDITIONS'),
			'id_cgv' => Configuration::get('PS_CONDITIONS_CMS_ID'),
			'PS_SHOP_NAME' => Configuration::get('PS_SHOP_NAME'),
			'PS_ALLOW_MOBILE_DEVICE' => isset($_SERVER['HTTP_USER_AGENT']) && (bool)Configuration::get('PS_ALLOW_MOBILE_DEVICE') && @filemtime(_PS_THEME_MOBILE_DIR_)
		));
		//RTL support
		//rtl.css overrides theme css files for rtl
		//iso_code.css overrides default font for every language (optional)
		if ($this->context->language->is_rtl)
		{
			$this->addCSS(_THEME_CSS_DIR_.'rtl.css');
			$this->addCSS(_THEME_CSS_DIR_.$this->context->language->iso_code.'.css');
		}

	}
	
	public function checkLiveEditAccess()
	{
		if (!Tools::isSubmit('live_edit') || !Tools::getValue('ad') || !Tools::getValue('liveToken'))
			return false;
		if (Tools::getValue('liveToken') != Tools::getAdminToken('AdminModulesPositions'.(int)Tab::getIdFromClassName('AdminModulesPositions').(int)Tools::getValue('id_employee')))
			return false;
		return is_dir(_PS_CORE_DIR_.DIRECTORY_SEPARATOR.Tools::getValue('ad'));
	}
	
	public function getLiveEditFooter()
	{
		if ($this->checkLiveEditAccess())
		{
			$data = $this->context->smarty->createData();
			$data->assign(array(
				'ad' => Tools::getValue('ad'),
				'live_edit' => true,
				'hook_list' => Hook::$executed_hooks,
				'id_shop' => $this->context->shop->id
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

		$stock_management = Configuration::get('PS_STOCK_MANAGEMENT') ? true : false; // no display quantity order if stock management disabled
		$order_by_values = array(0 => 'name', 1 => 'price', 2 => 'date_add', 3 => 'date_upd', 4 => 'position', 5 => 'manufacturer_name', 6 => 'quantity', 7 => 'reference');
		$order_way_values = array(0 => 'asc', 1 => 'desc');
		$this->orderBy = Tools::strtolower(Tools::getValue('orderby', $order_by_values[(int)Configuration::get('PS_PRODUCTS_ORDER_BY')]));
		$this->orderWay = Tools::strtolower(Tools::getValue('orderway', $order_way_values[(int)Configuration::get('PS_PRODUCTS_ORDER_WAY')]));
		if (!in_array($this->orderBy, $order_by_values))
			$this->orderBy = $order_by_values[0];
		if (!in_array($this->orderWay, $order_way_values))
			$this->orderWay = $order_way_values[0];

		$this->context->smarty->assign(array(
			'orderby' => $this->orderBy,
			'orderway' => $this->orderWay,
			'orderbydefault' => $order_by_values[(int)Configuration::get('PS_PRODUCTS_ORDER_BY')],
			'orderwayposition' => $order_way_values[(int)Configuration::get('PS_PRODUCTS_ORDER_WAY')], // Deprecated: orderwayposition
			'orderwaydefault' => $order_way_values[(int)Configuration::get('PS_PRODUCTS_ORDER_WAY')],
			'stock_management' => (int)$stock_management));
	}

	public function pagination($total_products = null)
	{
		if (!self::$initialized)
			$this->init();
		elseif (!$this->context)
			$this->context = Context::getContext();

		// Retrieve the default number of products per page and the other available selections
		$default_products_per_page = max(1, (int)Configuration::get('PS_PRODUCTS_PER_PAGE'));
		$nArray = array($default_products_per_page, $default_products_per_page * 2, $default_products_per_page * 5);

		if ((int)Tools::getValue('n') && (int)$total_products > 0)
			$nArray[] = $total_products;
		// Retrieve the current number of products per page (either the default, the GET parameter or the one in the cookie)
		$this->n = $default_products_per_page;
		if ((int)Tools::getValue('n') && in_array((int)Tools::getValue('n'), $nArray))
		{
			$this->n = (int)Tools::getValue('n');
			if (isset($this->context->cookie->nb_item_per_page) && in_array($this->context->cookie->nb_item_per_page, $nArray))
				$this->n = (int)$this->context->cookie->nb_item_per_page;
		}

		// Retrieve the page number (either the GET parameter or the first page)
		$this->p = (int)Tools::getValue('p', 1);
		// If the parameter is not correct then redirect (do not merge with the previous line, the redirect is required in order to avoid duplicate content)
		if (!is_numeric($this->p) || $this->p < 1)
			Tools::redirect(self::$link->getPaginationLink(false, false, $this->n, false, 1, false));

		// Remove the page parameter in order to get a clean URL for the pagination template
		$current_url = preg_replace('/(\?)?(&amp;)?p=\d+/', '$1', Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']));

		if ($this->n != $default_products_per_page)
			$this->context->cookie->nb_item_per_page = $this->n;

		$pages_nb = ceil($total_products / (int)$this->n);
		if ($this->p > $pages_nb && $total_products != 0)
			Tools::redirect(self::$link->getPaginationLink(false, false, $this->n, false, $pages_nb, false));

		$range = 2; /* how many pages around page selected */
		$start = (int)($this->p - $range);
		if ($start < 1)
			$start = 1;
		$stop = (int)($this->p + $range);
		if ($stop > $pages_nb)
			$stop = (int)$pages_nb;

		$this->context->smarty->assign(array(
			'nb_products' => $total_products,
			'products_per_page' => $this->n,
			'pages_nb' => $pages_nb,
			'p' => $this->p,
			'n' => $this->n,
			'nArray' => $nArray,
			'range' => $range,
			'start' => $start,
			'stop' => $stop,
			'current_url' => $current_url
		));
	}

	public static function getCurrentCustomerGroups()
	{
		if (!Group::isFeatureActive())
			return array();

		$context = Context::getContext();
		if (!isset($context->customer) || !$context->customer->id)
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
		$user_ip = Tools::getRemoteAddr();
		$ips = array();
		// retrocompatibility
		$ips_old = explode(';', Configuration::get('PS_GEOLOCATION_WHITELIST'));
		if (is_array($ips_old) && count($ips_old))
			foreach ($ips_old as $ip)
				$ips = array_merge($ips, explode("\n", $ip));
		$ips = array_map('trim', $ips);
		if (is_array($ips) && count($ips))
			foreach ($ips as $ip)
				if (!empty($ip) && preg_match('/^'.$ip.'.*/', $user_ip))
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
		if (!Configuration::get('PS_TOKEN_ENABLE'))
			return true;

		return (strcasecmp(Tools::getToken(false), Tools::getValue('token')) == 0);
	}

	public function addMedia($media_uri, $css_media_type = null, $offset = null, $remove = false)
	{
		if (!is_array($media_uri))
		{
			if ($css_media_type)
				$media_uri = array($media_uri => $css_media_type);
			else
				$media_uri = array($media_uri);
		}

		$list_uri = array();
		foreach ($media_uri as $file => $media)
		{
			if (!preg_match('/^http(s?):\/\//i', $media))
			{
				$different = 0;
				$different_css = 0;
				$type = 'css';
				if (!$css_media_type)
				{
					$type = 'js';
					$file = $media;
				}
				$override_path = str_replace(__PS_BASE_URI__.'modules/', _PS_ROOT_DIR_.'/themes/'._THEME_NAME_.'/'.$type.'/modules/', $file, $different);

				$override_path_css = str_replace(basename ($file), $type.'/'.basename ($file), str_replace(__PS_BASE_URI__, _PS_ROOT_DIR_.'/', $file), $different_css );

				if ($different && file_exists($override_path))
					$file = str_replace(__PS_BASE_URI__.'modules/', __PS_BASE_URI__.'themes/'._THEME_NAME_.'/'.$type.'/modules/', $file, $different);
				elseif ($different_css && file_exists($override_path_css))
					$file = $override_path_css;

				if ($css_media_type)
					$list_uri[$file] = $media;
				else
					$list_uri[] = $file;
			}
			else
				$list_uri[$file] = $media;
		}

		if ($remove)
		{
			if ($css_media_type)
				return parent::removeCSS($list_uri, $css_media_type);
			return parent::removeJS($list_uri);
		}

		if ($css_media_type)
			return parent::addCSS($list_uri, $css_media_type, $offset);
		return parent::addJS($list_uri);
	}

	public function removeMedia($media_uri, $css_media_type = null)
	{
		Frontcontroller::addMedia($media_uri, $css_media_type, null, true);
	}

	/**
	 * Add one or several CSS for front, checking if css files are overriden in theme/css/modules/ directory
	 *
	 * @see Controller::addCSS()
	 */
	public function addCSS($css_uri, $css_media_type = 'all', $offset = null)
	{
		return Frontcontroller::addMedia($css_uri, $css_media_type, $offset = null);
	}

	public function removeCSS($css_uri, $css_media_type = 'all')
	{
		return Frontcontroller::removeMedia($css_uri, $css_media_type);
	}

	/**
	 * Add one or several JS files for front, checking if js files are overriden in theme/js/modules/ directory
	 *
	 * @see Controller::addJS()
	 */
	public function addJS($js_uri)
	{
		return Frontcontroller::addMedia($js_uri);
	}

	public function removeJS($js_uri)
	{
		return Frontcontroller::removeMedia($js_uri);
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
					$customer->logged = 1;
					$this->context->customer = $customer;
					$this->context->cookie->id_customer = (int)$customer->id;
					$this->context->cookie->customer_lastname = $customer->lastname;
					$this->context->cookie->customer_firstname = $customer->firstname;
					$this->context->cookie->logged = 1;
					$this->context->cookie->check_cgv = 1;
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

	/**
	 * This is overrided to manage is behaviour
	 * if a customer access to the site with mobile device.
	 */
	public function setTemplate($default_template)
	{
		if ($this->useMobileTheme())
			$this->setMobileTemplate($default_template);
		else
		{
			$template = $this->getOverrideTemplate();
			if ($template)
				parent::setTemplate($template);
			else
				parent::setTemplate($default_template);
		}
	}

	/**
	 * Returns the template corresponding to the current page.
	 * By default this method return false but could easily be overridden in a specific controller
	 *
	* @since 1.5
	* @return bool
	*/
	public function getOverrideTemplate()
	{
		return Hook::exec('DisplayOverrideTemplate', array('controller' => $this));
	}
	
	protected function useMobileTheme()
	{
		static $use_mobile_template = null;

		// The mobile theme must have a layout to be used
		if ($use_mobile_template === null)
			$use_mobile_template = ($this->context->getMobileDevice() && file_exists(_PS_THEME_MOBILE_DIR_.'layout.tpl'));

		return $use_mobile_template;
	}
	
	protected function getThemeDir()
	{
		return $this->useMobileTheme() ? _PS_THEME_MOBILE_DIR_ : _PS_THEME_DIR_;
	}
	
	protected function getOverrideThemeDir()
	{
		return $this->useMobileTheme() ? _PS_THEME_MOBILE_OVERRIDE_DIR_ : _PS_THEME_OVERRIDE_DIR_;
	}
	
	/**
	 * Returns the layout corresponding to the current page by using the override system
	 * Ex:
	 * On the url: http://localhost/index.php?id_product=1&controller=product, this method will
	 * check if the layout exists in the following files (in that order), and return the first found:
	 * - /themes/default/override/layout-product-1.tpl
	 * - /themes/default/override/layout-product.tpl
	 * - /themes/default/layout.tpl
	 *
	 * @since 1.5
	 * @return bool|string
	 */
	public function getLayout()
	{
		$entity = $this->php_self;
		$id_item = (int)Tools::getValue('id_'.$entity);

		$layout_dir = $this->getThemeDir();
		$layout_override_dir  = $this->getOverrideThemeDir();

		$layout = false;
		if ($entity)
		{
			if ($id_item > 0 && file_exists($layout_override_dir.'layout-'.$entity.'-'.$id_item.'.tpl'))
				$layout = $layout_override_dir.'layout-'.$entity.'-'.$id_item.'.tpl';
			elseif (file_exists($layout_override_dir.'layout-'.$entity.'.tpl'))
				$layout = $layout_override_dir.'layout-'.$entity.'.tpl';
		}

		if (!$layout && file_exists($layout_dir.'layout.tpl'))
			$layout = $layout_dir.'layout.tpl';

		return $layout;
	}

	public function getTemplatePath($template)
	{
		if (!$this->useMobileTheme())
			return $template;

		$tpl_file = basename($template);
		$dirname = dirname($template).(substr(dirname($template), -1, 1) == '/' ? '' : '/');

		if ($dirname == _PS_THEME_DIR_)
		{
			if (file_exists(_PS_THEME_MOBILE_DIR_.$tpl_file))
				$template = _PS_THEME_MOBILE_DIR_.$tpl_file;
		}
		elseif ($dirname == _PS_THEME_MOBILE_DIR_)
		{
			if (!file_exists(_PS_THEME_MOBILE_DIR_.$tpl_file) && file_exists(_PS_THEME_DIR_.$tpl_file))
				$template = _PS_THEME_DIR_.$tpl_file;
		}
		
		return $template;
	}
	
	/**
	 * This checks if the template set is available for mobile themes,
	 * otherwise the front template is choosen.
	 */
	public function setMobileTemplate($template)
	{
		// Needed for site map
		$blockmanufacturer = Module::getInstanceByName('blockmanufacturer');
		$blocksupplier = Module::getInstanceByName('blocksupplier');
		$this->context->smarty->assign('categoriesTree', Category::getRootCategory()->recurseLiteCategTree(0));
		$this->context->smarty->assign('categoriescmsTree', CMSCategory::getRecurseCategory($this->context->language->id, 1, 1, 1));
		$this->context->smarty->assign('voucherAllowed', (int)CartRule::isFeatureActive());
		$this->context->smarty->assign('display_manufacturer_link', (bool)$blockmanufacturer->active);
		$this->context->smarty->assign('display_supplier_link', (bool)$blocksupplier->active);
		$this->context->smarty->assign('PS_DISPLAY_SUPPLIERS', Configuration::get('PS_DISPLAY_SUPPLIERS'));
		$this->context->smarty->assign('PS_DISPLAY_BEST_SELLERS', Configuration::get('PS_DISPLAY_BEST_SELLERS'));
		$this->context->smarty->assign('display_store', Configuration::get('PS_STORES_DISPLAY_SITEMAP'));
		$this->context->smarty->assign('conditions', Configuration::get('PS_CONDITIONS'));
		$this->context->smarty->assign('id_cgv', Configuration::get('PS_CONDITIONS_CMS_ID'));
		$this->context->smarty->assign('PS_SHOP_NAME', Configuration::get('PS_SHOP_NAME'));

		$template = $this->getTemplatePath($template);

		$assign = array();
		$assign['tpl_file'] = basename($template, '.tpl');
		if (isset($this->php_self))
			$assign['controller_name'] = $this->php_self;

		$this->context->smarty->assign($assign);
		$this->template = $template;
	}
	
	/**
	 * Return an array with specific logo and favicon, 
	 * if mobile device
	 *
	 * @since 1.5
	 * @return array
	 */
	public function initLogoAndFavicon()
	{
		$mobile_device = $this->context->getMobileDevice();
		
		if ($mobile_device && Configuration::get('PS_LOGO_MOBILE'))
			$logo = self::$link->getMediaLink(_PS_IMG_.Configuration::get('PS_LOGO_MOBILE').'?'.Configuration::get('PS_IMG_UPDATE_TIME'));
		else
			$logo = self::$link->getMediaLink(_PS_IMG_.Configuration::get('PS_LOGO'));
		
		return array(
 				'favicon_url' => _PS_IMG_.Configuration::get('PS_FAVICON'),
	            'logo_image_width' => ($mobile_device == false ? Configuration::get('SHOP_LOGO_WIDTH') : Configuration::get('SHOP_LOGO_MOBILE_WIDTH')),
	            'logo_image_height' => ($mobile_device == false ? Configuration::get('SHOP_LOGO_HEIGHT') : Configuration::get('SHOP_LOGO_MOBILE_HEIGHT')),
	            'logo_url' => $logo
  				);
	}
	
	public function addColorsToProductList(&$products)
	{
		if (!is_array($products) || !count($products) || !file_exists(_PS_THEME_DIR_.'product-list-colors.tpl'))
			return;

		$products_need_cache = array();
		foreach ($products as &$product)
			if (!$this->isCached(_PS_THEME_DIR_.'product-list-colors.tpl', $this->getColorsListCacheId($product['id_product'])))
				$products_need_cache[] = (int)$product['id_product']; 

		$colors = false;
		if (count($products_need_cache))
			$colors = Product::getAttributesColorList($products_need_cache);

		Tools::enableCache();
		foreach ($products as &$product)
		{
			$tpl = $this->context->smarty->createTemplate(_PS_THEME_DIR_.'product-list-colors.tpl');
			if (isset($colors[$product['id_product']]))
					$tpl->assign(array(
						'id_product' => $product['id_product'],
						'colors_list' => $colors[$product['id_product']],
						'link' => Context::getContext()->link
					));
			if (!in_array($product['id_product'], $products_need_cache) || isset($colors[$product['id_product']]))
				$product['color_list'] = $tpl->fetch(_PS_THEME_DIR_.'product-list-colors.tpl', $this->getColorsListCacheId($product['id_product']));
			else
				$product['color_list'] = '';
		}
		Tools::restoreCacheSettings();
	}
	
	protected function getColorsListCacheId($id_product)
	{
		return Product::getColorsListCacheId($id_product);
	}
}
