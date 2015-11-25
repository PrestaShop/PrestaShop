<?php
/**
 * 2007-2015 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class FrontControllerCore extends Controller
{
    /**
     * @deprecated Deprecated shortcuts as of 1.5.0.1 - Use $context->smarty instead
     * @var $smarty Smarty
     */
    protected static $smarty;

    /**
     * @deprecated Deprecated shortcuts as of 1.5.0.1 - Use $context->cookie instead
     * @var $cookie Cookie
     */
    protected static $cookie;

    /**
     * @deprecated Deprecated shortcuts as of 1.5.0.1 - Use $context->link instead
     * @var $link Link
     */
    protected static $link;

    /**
     * @deprecated Deprecated shortcuts as of 1.5.0.1 - Use $context->cart instead
     * @var $cart Cart
     */
    protected static $cart;

    /** @var array Controller errors */
    public $errors = array();

    /** @var array Controller warning notifications */
    public $warning = array();

    /** @var array Controller success notifications */
    public $success = array();

    /** @var array Controller info notifications */
    public $info = array();

    /** @var string Language ISO code */
    public $iso;

    /** @var string ORDER BY field */
    public $orderBy;

    /** @var string Order way string ('ASC', 'DESC') */
    public $orderWay;

    /** @var int Current page number */
    public $p;

    /** @var int Items (products) per page */
    public $n;

    /** @var bool If set to true, will redirected user to login page during init function. */
    public $auth = false;

    /**
     * If set to true, user can be logged in as guest when checking if logged in.
     *
     * @see $auth
     * @var bool
     */
    public $guestAllowed = false;

    /**
     * Route of PrestaShop page to redirect to after forced login.
     *
     * @see $auth
     * @var bool
     */
    public $authRedirection = false;

    /** @var bool SSL connection flag */
    public $ssl = false;

    /** @var bool If true, switches display to restricted country page during init. */
    protected $restrictedCountry = false;
    protected $restricted_country_mode = false;

    /** @var bool If true, forces display to maintenance page. */
    protected $maintenance = false;

    /** @var bool If false, does not build left page column content and hides it. */
    public $display_column_left = true;

    /** @var bool If false, does not build right page column content and hides it. */
    public $display_column_right = true;

    /**
     * True if controller has already been initialized.
     * Prevents initializing controller more than once.
     *
     * @var bool
     */
    public static $initialized = false;

    /**
     * @var array Holds current customer's groups.
     */
    protected static $currentCustomerGroups;

    /**
     * @var int
     */
    public $nb_items_per_page;

    /**
     * @var object Adapter_ObjectSerializer
     */
    public $objectSerializer;

    /**
     * Controller constructor
     *
     * @global bool $useSSL SSL connection flag
     */
    public function __construct()
    {
        $this->controller_type = 'front';

        global $useSSL;

        parent::__construct();

        if (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) {
            $this->ssl = true;
        }

        if (isset($useSSL)) {
            $this->ssl = $useSSL;
        } else {
            $useSSL = $this->ssl;
        }

        if (isset($this->php_self) && is_object(Context::getContext()->theme)) {
            $columns = Context::getContext()->theme->hasColumns($this->php_self);

            // Don't use theme tables if not configured in DB
            if ($columns) {
                $this->display_column_left  = $columns['left_column'];
                $this->display_column_right = $columns['right_column'];
            }
        }

        $this->objectSerializer = new Adapter_ObjectSerializer();
    }

    /**
     * Check if the controller is available for the current user/visitor
     *
     * @see Controller::checkAccess()
     * @return bool
     */
    public function checkAccess()
    {
        return true;
    }

    /**
     * Check if the current user/visitor has valid view permissions
     *
     * @see Controller::viewAccess
     * @return bool
     */
    public function viewAccess()
    {
        return true;
    }

    /**
     * Initializes front controller: sets smarty variables,
     * class properties, redirects depending on context, etc.
     *
     * @global bool     $useSSL           SSL connection flag
     * @global Cookie   $cookie           Visitor's cookie
     * @global Smarty   $smarty
     * @global Cart     $cart             Visitor's cart
     * @global string   $iso              Language ISO
     * @global Country  $defaultCountry   Visitor's country object
     * @global string   $protocol_link
     * @global string   $protocol_content
     * @global Link     $link
     * @global array    $css_files
     * @global array    $js_files
     * @global Currency $currency         Visitor's selected currency
     *
     * @throws PrestaShopException
     */
    public function init()
    {
        if (_PS_MODE_DEV_) {
            $m = [];
            foreach (Tools::getAllValues() as $key => $value) {
                if (preg_match('/^debug-set-configuration-(.*)$/', $key, $m)) {
                    $configurationKey = $m[1];
                    Configuration::set($configurationKey, $value);
                }
            }
        }

        /**
         * Globals are DEPRECATED as of version 1.5.0.1
         * Use the Context object to access objects instead.
         * Example: $this->context->cart
         */
        global $useSSL, $cookie, $smarty, $cart, $iso, $defaultCountry, $protocol_link, $protocol_content, $link, $css_files, $js_files, $currency;

        if (self::$initialized) {
            return;
        }

        self::$initialized = true;

        parent::init();

        // If current URL use SSL, set it true (used a lot for module redirect)
        if (Tools::usingSecureMode()) {
            $useSSL = true;
        }

        // For compatibility with globals, DEPRECATED as of version 1.5.0.1
        $css_files = $this->css_files;
        $js_files = $this->js_files;

        $this->sslRedirection();

        if ($this->ajax) {
            $this->display_header = false;
            $this->display_footer = false;
        }

        // If account created with the 2 steps register process, remove 'account_created' from cookie
        if (isset($this->context->cookie->account_created)) {
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

        if ($id_cart = (int)$this->recoverCart()) {
            $this->context->cookie->id_cart = (int)$id_cart;
        }

        if ($this->auth && !$this->context->customer->isLogged($this->guestAllowed)) {
            Tools::redirect('index.php?controller=authentication'.($this->authRedirection ? '&back='.$this->authRedirection : ''));
        }

        /* Theme is missing */
        if (!is_dir(_PS_THEME_DIR_)) {
            throw new PrestaShopException((sprintf(Tools::displayError('Current theme unavailable "%s". Please check your theme directory name and permissions.'), basename(rtrim(_PS_THEME_DIR_, '/\\')))));
        }

        if (Configuration::get('PS_GEOLOCATION_ENABLED')) {
            if (($new_default = $this->geolocationManagement($this->context->country)) && Validate::isLoadedObject($new_default)) {
                $this->context->country = $new_default;
            }
        } elseif (Configuration::get('PS_DETECT_COUNTRY')) {
            $has_currency = isset($this->context->cookie->id_currency) && (int)$this->context->cookie->id_currency;
            $has_country = isset($this->context->cookie->iso_code_country) && $this->context->cookie->iso_code_country;
            $has_address_type = false;

            if ((int)$this->context->cookie->id_cart && ($cart = new Cart($this->context->cookie->id_cart)) && Validate::isLoadedObject($cart)) {
                $has_address_type = isset($cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) && $cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
            }

            if ((!$has_currency || $has_country) && !$has_address_type) {
                $id_country = $has_country && !Validate::isLanguageIsoCode($this->context->cookie->iso_code_country) ?
                    (int)Country::getByIso(strtoupper($this->context->cookie->iso_code_country)) : (int)Tools::getCountry();

                $country = new Country($id_country, (int)$this->context->cookie->id_lang);

                if (!$has_currency && validate::isLoadedObject($country) && $this->context->country->id !== $country->id) {
                    $this->context->country = $country;
                    $this->context->cookie->id_currency = (int)Currency::getCurrencyInstance($country->id_currency ? (int)$country->id_currency : (int)Configuration::get('PS_CURRENCY_DEFAULT'))->id;
                    $this->context->cookie->iso_code_country = strtoupper($country->iso_code);
                }
            }
        }

        $currency = Tools::setCurrency($this->context->cookie);

        if (isset($_GET['logout']) || ($this->context->customer->logged && Customer::isBanned($this->context->customer->id))) {
            $this->context->customer->logout();

            Tools::redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);
        } elseif (isset($_GET['mylogout'])) {
            $this->context->customer->mylogout();
            Tools::redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);
        }

        /* Cart already exists */
        if ((int)$this->context->cookie->id_cart) {
            if (!isset($cart)) {
                $cart = new Cart($this->context->cookie->id_cart);
            }

            if (Validate::isLoadedObject($cart) && $cart->OrderExists()) {
                PrestaShopLogger::addLog('Frontcontroller::init - Cart cannot be loaded or an order has already been placed using this cart', 1, null, 'Cart', (int)$this->context->cookie->id_cart, true);
                unset($this->context->cookie->id_cart, $cart, $this->context->cookie->checkedTOS);
                $this->context->cookie->check_cgv = false;
            } elseif (intval(Configuration::get('PS_GEOLOCATION_ENABLED'))
                && !in_array(strtoupper($this->context->cookie->iso_code_country), explode(';', Configuration::get('PS_ALLOWED_COUNTRIES')))
                && $cart->nbProducts()
                && intval(Configuration::get('PS_GEOLOCATION_NA_BEHAVIOR')) != -1
                && !FrontController::isInWhitelistForGeolocation()
                && !in_array($_SERVER['SERVER_NAME'], array('localhost', '127.0.0.1'))
            ) {
                /* Delete product of cart, if user can't make an order from his country */
                PrestaShopLogger::addLog('Frontcontroller::init - GEOLOCATION is deleting a cart', 1, null, 'Cart', (int)$this->context->cookie->id_cart, true);
                unset($this->context->cookie->id_cart, $cart);
            } elseif ($this->context->cookie->id_customer != $cart->id_customer || $this->context->cookie->id_lang != $cart->id_lang || $currency->id != $cart->id_currency) {
                // update cart values
                if ($this->context->cookie->id_customer) {
                    $cart->id_customer = (int)$this->context->cookie->id_customer;
                }
                $cart->id_lang = (int)$this->context->cookie->id_lang;
                $cart->id_currency = (int)$currency->id;
                $cart->update();
            }
            /* Select an address if not set */
            if (isset($cart) && (!isset($cart->id_address_delivery) || $cart->id_address_delivery == 0 ||
                !isset($cart->id_address_invoice) || $cart->id_address_invoice == 0) && $this->context->cookie->id_customer) {
                $to_update = false;
                if (!isset($cart->id_address_delivery) || $cart->id_address_delivery == 0) {
                    $to_update = true;
                    $cart->id_address_delivery = (int)Address::getFirstCustomerAddressId($cart->id_customer);
                }
                if (!isset($cart->id_address_invoice) || $cart->id_address_invoice == 0) {
                    $to_update = true;
                    $cart->id_address_invoice = (int)Address::getFirstCustomerAddressId($cart->id_customer);
                }
                if ($to_update) {
                    $cart->update();
                }
            }
        }

        if (!isset($cart) || !$cart->id) {
            $cart = new Cart();
            $cart->id_lang = (int)$this->context->cookie->id_lang;
            $cart->id_currency = (int)$this->context->cookie->id_currency;
            $cart->id_guest = (int)$this->context->cookie->id_guest;
            $cart->id_shop_group = (int)$this->context->shop->id_shop_group;
            $cart->id_shop = $this->context->shop->id;
            if ($this->context->cookie->id_customer) {
                $cart->id_customer = (int)$this->context->cookie->id_customer;
                $cart->id_address_delivery = (int)Address::getFirstCustomerAddressId($cart->id_customer);
                $cart->id_address_invoice = (int)$cart->id_address_delivery;
            } else {
                $cart->id_address_delivery = 0;
                $cart->id_address_invoice = 0;
            }

            // Needed if the merchant want to give a free product to every visitors
            $this->context->cart = $cart;
            CartRule::autoAddToCart($this->context);
        } else {
            $this->context->cart = $cart;
        }

        $this->context->smarty->assign('request_uri', Tools::safeOutput(urldecode($_SERVER['REQUEST_URI'])));

        /* Breadcrumb */
        $navigation_pipe = (Configuration::get('PS_NAVIGATION_PIPE') ? Configuration::get('PS_NAVIGATION_PIPE') : '>');
        $this->context->smarty->assign('navigationPipe', $navigation_pipe);

        // Automatically redirect to the canonical URL if needed
        if (!empty($this->php_self) && !Tools::getValue('ajax')) {
            $this->canonicalRedirection($this->context->link->getPageLink($this->php_self, $this->ssl, $this->context->language->id));
        }

        Product::initPricesComputation();

        $display_tax_label = $this->context->country->display_tax_label;
        if (isset($cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) && $cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) {
            $infos = Address::getCountryAndState((int)$cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
            $country = new Country((int)$infos['id_country']);
            $this->context->country = $country;
            if (Validate::isLoadedObject($country)) {
                $display_tax_label = $country->display_tax_label;
            }
        }

        $languages = Language::getLanguages(true, $this->context->shop->id);
        $meta_language = array();
        foreach ($languages as $lang) {
            $meta_language[] = $lang['iso_code'];
        }

        $compared_products = array();
        if (Configuration::get('PS_COMPARATOR_MAX_ITEM') && isset($this->context->cookie->id_compare)) {
            $compared_products = CompareProduct::getCompareProducts($this->context->cookie->id_compare);
        }

        /* StarterTheme: Legacy - Should we remove old smarty vars */
        $this->context->smarty->assign(array(
            // Useful for layout.tpl
            'mobile_device'       => $this->context->getMobileDevice(),
            'link'                => $link,
            'cart'                => $cart,
            'cookie'              => $this->context->cookie,
            'hide_left_column'    => !$this->display_column_left,
            'hide_right_column'   => !$this->display_column_right,
            'base_dir'            => _PS_BASE_URL_.__PS_BASE_URI__,
            'base_dir_ssl'        => $protocol_link.Tools::getShopDomainSsl().__PS_BASE_URI__,
            'force_ssl'           => Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'),
            'content_dir'         => $protocol_content.Tools::getHttpHost().__PS_BASE_URI__,
            'base_uri'            => $protocol_content.Tools::getHttpHost().__PS_BASE_URI__.(!Configuration::get('PS_REWRITING_SETTINGS') ? 'index.php' : ''),
            'tpl_dir'             => _PS_THEME_DIR_,
            'tpl_uri'             => _THEME_DIR_,
            'modules_dir'         => _MODULE_DIR_,
            'mail_dir'            => _MAIL_DIR_,
            'language_code'       => $this->context->language->language_code ? $this->context->language->language_code : $this->context->language->iso_code, // done
            'come_from'           => Tools::getHttpHost(true, true).Tools::htmlentitiesUTF8(str_replace(array('\'', '\\'), '', urldecode($_SERVER['REQUEST_URI']))),
            'cart_qties'          => (int)$cart->nbProducts(),
            'priceDisplay'        => Product::getTaxCalculationMethod((int)$this->context->cookie->id_customer),
            'add_prod_display'    => (int)Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
            'roundMode'           => (int)Configuration::get('PS_PRICE_ROUND_MODE'),
            'use_taxes'           => (int)Configuration::get('PS_TAX'),
            'show_taxes'          => (int)(Configuration::get('PS_TAX_DISPLAY') == 1 && (int)Configuration::get('PS_TAX')),
            'display_tax_label'   => (bool)$display_tax_label,
            'vat_management'      => (int)Configuration::get('VATNUMBER_MANAGEMENT'),
            'PS_CATALOG_MODE'     => (bool)Configuration::get('PS_CATALOG_MODE') || (Group::isFeatureActive() && !(bool)Group::getCurrent()->show_prices),
            'b2b_enable'          => (bool)Configuration::get('PS_B2B_ENABLE'),
            'request'             => $link->getPaginationLink(false, false, false, true),
            'PS_STOCK_MANAGEMENT' => Configuration::get('PS_STOCK_MANAGEMENT'),
            'quick_view'          => (bool)Configuration::get('PS_QUICK_VIEW'),
            'compared_products'   => is_array($compared_products) ? $compared_products : array(),
            'comparator_max_item' => (int)Configuration::get('PS_COMPARATOR_MAX_ITEM'),
        ));

        /**
         * These shortcuts are DEPRECATED as of version 1.5.0.1
         * Use the Context to access objects instead.
         * Example: $this->context->cart
         */
        self::$cookie = $this->context->cookie;
        self::$cart   = $cart;
        self::$smarty = $this->context->smarty;
        self::$link   = $link;
        $defaultCountry = $this->context->country;

        $this->displayMaintenancePage();

        if ($this->restrictedCountry) {
            $this->displayRestrictedCountryPage();
        }

        if (Tools::isSubmit('live_edit') && !$this->checkLiveEditAccess()) {
            Tools::redirect('index.php?controller=404');
        }

        $this->iso               = $iso;
        $this->context->cart     = $cart;
        $this->context->currency = $currency;

        $customer = $this->getTemplateVarCustomer();
        $urls = $this->getTemplateVarUrls();

        /**
         * Template vars assignation
         */
        $this->context->smarty->assign([
            'currency' => $this->getTemplateVarCurrency(),
            'customer' => $customer,
            'language' => $this->objectSerializer->toArray($this->context->language),
            'page' => $this->getTemplateVarPage(),
            'shop' => $this->getTemplateVarShop(),
            'urls' => $urls,
            'feature_active' => $this->getTemplateVarFeatureActive(),
            'field_required' => $this->context->customer->validateFieldsRequiredDatabase(),
        ]);

        Media::addJsDef(['prestashop' => [
            'customer' => $customer,
            'cart' => ['id_address_delivery' => 1, 'id_address_invoice' => 1, ], // StarterTheme: Set proposer ids
            'urls' => $urls,
        ]]);
    }

    /**
     * Method that is executed after init() and checkAccess().
     * Used to process user input.
     *
     * @see Controller::run()
     */
    public function postProcess()
    {
    }

    /**
     * Initializes common front page content: header, footer and side columns
     */
    public function initContent()
    {
        $this->process();

        if (!isset($this->context->cart)) {
            $this->context->cart = new Cart();
        }

        $this->context->smarty->assign(array(
            'HOOK_HEADER'       => Hook::exec('displayHeader'),
            'HOOK_TOP'          => Hook::exec('displayTop'),
            'HOOK_LEFT_COLUMN'  => ($this->display_column_left  ? Hook::exec('displayLeftColumn') : ''),
            'HOOK_RIGHT_COLUMN' => ($this->display_column_right ? Hook::exec('displayRightColumn', array('cart' => $this->context->cart)) : ''),
        ));
    }

    /**
     * Renders and outputs maintenance page and ends controller process.
     */
    public function initCursedPage()
    {
        $this->displayMaintenancePage();
    }

    /**
     * Called before compiling common page sections (header, footer, columns).
     * Good place to modify smarty variables.
     *
     * @see FrontController::initContent()
     */
    public function process()
    {
    }

    /**
     * Non-static translation method for frontoffice
     *
     * @param string  $string Term or expression in english
     * @param false|string  $specific Specific name, only for ModuleFrontController
     * @param string|null $class Name of the class
     * @param bool $addslashes If set to true, the return value will pass through addslashes(). Otherwise, stripslashes().
     * @param bool $htmlentities If set to true(default), the return value will pass through htmlentities($string, ENT_QUOTES, 'utf-8')
     * @return string The translation if available, or the english default text.
     */
    protected function l($string, $specific = false, $class = null, $addslashes = false, $htmlentities = true)
    {
        if ($class === null) {
            $class = get_class($this);
        }

        if (is_a($this, 'ModuleFrontController')) {
            // ModuleFrontController must assign $this->module
            if (isset($this->module) && is_a($this->module, 'Module')) {
                return $this->module->l($string, $specific);
            } else {
                return $string;
            }
        }

        return Translate::getFrontTranslation($string, $class, $addslashes, $htmlentities);
    }

    /**
     * Redirects to redirect_after link
     *
     * @see $redirect_after
     */
    protected function redirect()
    {
        Tools::redirectLink($this->redirect_after);
    }

    protected function redirectWithNotifications()
    {
        $notifications = json_encode([
            'error' => $this->errors,
            'warning' => $this->warning,
            'success' => $this->success,
            'info' => $this->info,
        ]);

        if (session_status() == PHP_SESSION_ACTIVE) {
            $_SESSION['notifications'] = $notifications;
        } elseif (session_status() == PHP_SESSION_NONE) {
            session_start();
            $_SESSION['notifications'] = $notifications;
        } else {
            setcookie('notifications', $notifications);
        }

        return call_user_func_array(['Tools', 'redirect'], func_get_args());
    }

    /**
     * Renders page content.
     * Used for retrocompatibility with PS 1.4
     */
    public function displayContent()
    {
    }

    /**
     * Compiles and outputs full page content
     *
     * @return bool
     * @throws Exception
     * @throws SmartyException
     */
    public function display()
    {
        Tools::safePostVars();

        // assign css_files and js_files at the very last time
        if ((Configuration::get('PS_CSS_THEME_CACHE') || Configuration::get('PS_JS_THEME_CACHE')) && is_writable(_PS_THEME_DIR_.'cache')) {
            // CSS compressor management
            if (Configuration::get('PS_CSS_THEME_CACHE')) {
                $this->css_files = Media::cccCss($this->css_files);
            }
            //JS compressor management
            if (Configuration::get('PS_JS_THEME_CACHE')) {
                $this->js_files = Media::cccJs($this->js_files);
            }
        }

        $this->context->smarty->assign(array(
            'layout'         => $this->getLayout(),
            'css_files'      => $this->css_files,
            'js_files'       => ($this->getLayout() && (bool)Configuration::get('PS_JS_DEFER')) ? array() : $this->js_files,
            'js_defer'       => (bool)Configuration::get('PS_JS_DEFER'),
            'notifications'  => $this->prepareNotifications(),
            'display_header' => $this->display_header,
            'display_footer' => $this->display_footer,
        ));

        $this->smartyOutputContent($this->template);

        return true;
    }

    protected function prepareNotifications()
    {
        $notifications = [
            'error' => $this->errors,
            'warning' => $this->warning,
            'success' => $this->success,
            'info' => $this->info,
        ];

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (session_status() == PHP_SESSION_ACTIVE && isset($_SESSION['notifications'])) {
            $notifications = array_merge($notifications, json_decode($_SESSION['notifications'], true));
            unset($_SESSION['notifications']);
        } elseif (isset($_COOKIE['notifications'])) {
            $notifications = array_merge($notifications, json_decode($_COOKIE['notifications'], true));
            unset($_COOKIE['notifications']);
        }

        return $notifications;
    }

    /**
     * Displays maintenance page if shop is closed.
     */
    protected function displayMaintenancePage()
    {
        if ($this->maintenance == true || !(int)Configuration::get('PS_SHOP_ENABLE')) {
            $this->maintenance = true;
            if (!in_array(Tools::getRemoteAddr(), explode(',', Configuration::get('PS_MAINTENANCE_IP')))) {
                header('HTTP/1.1 503 Service Unavailable');
                header('Retry-After: 3600');

                $this->context->smarty->assign(array(
                    'shop' => $this->getTemplateVarShop(),
                    'HOOK_MAINTENANCE' => Hook::exec('displayMaintenance', array()),
                ));

                $this->smartyOutputContent('errors/maintenance.tpl');
                exit;
            }
        }
    }

    /**
     * Displays 'country restricted' page if user's country is not allowed.
     */
    protected function displayRestrictedCountryPage()
    {
        header('HTTP/1.1 403 Forbidden');
        $this->context->smarty->assign(array(
            'shop'   => $this->getTemplateVarShop(),
        ));
        $this->smartyOutputContent('errors/restricted-country.tpl');
        exit;
    }

    /**
     * Redirects to correct protocol if settings and request methods don't match.
     */
    protected function sslRedirection()
    {
        // If we call a SSL controller without SSL or a non SSL controller with SSL, we redirect with the right protocol
        if (Configuration::get('PS_SSL_ENABLED') && $_SERVER['REQUEST_METHOD'] != 'POST' && $this->ssl != Tools::usingSecureMode()) {
            $this->context->cookie->disallowWriting();
            header('HTTP/1.1 301 Moved Permanently');
            header('Cache-Control: no-cache');
            if ($this->ssl) {
                header('Location: '.Tools::getShopDomainSsl(true).$_SERVER['REQUEST_URI']);
            } else {
                header('Location: '.Tools::getShopDomain(true).$_SERVER['REQUEST_URI']);
            }
            exit();
        }
    }

    /**
     * Redirects to canonical URL
     *
     * @param string $canonical_url
     */
    protected function canonicalRedirection($canonical_url = '')
    {
        if (!$canonical_url || !Configuration::get('PS_CANONICAL_REDIRECT') || strtoupper($_SERVER['REQUEST_METHOD']) != 'GET' || Tools::getValue('live_edit')) {
            return;
        }

        $canonical_url = preg_replace('/#.*$/', '', $canonical_url);

        $match_url = rawurldecode(Tools::getCurrentUrlProtocolPrefix().$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        if (!preg_match('/^'.Tools::pRegexp(rawurldecode($canonical_url), '/').'([&?].*)?$/', $match_url)) {
            $params = array();
            $url_details = parse_url($canonical_url);

            if (!empty($url_details['query'])) {
                parse_str($url_details['query'], $query);
                foreach ($query as $key => $value) {
                    $params[Tools::safeOutput($key)] = Tools::safeOutput($value);
                }
            }
            $excluded_key = array('isolang', 'id_lang', 'controller', 'fc', 'id_product', 'id_category', 'id_manufacturer', 'id_supplier', 'id_cms');
            foreach ($_GET as $key => $value) {
                if (!in_array($key, $excluded_key) && Validate::isUrl($key) && Validate::isUrl($value)) {
                    $params[Tools::safeOutput($key)] = Tools::safeOutput($value);
                }
            }

            $str_params = http_build_query($params, '', '&');
            if (!empty($str_params)) {
                $final_url = preg_replace('/^([^?]*)?.*$/', '$1', $canonical_url).'?'.$str_params;
            } else {
                $final_url = preg_replace('/^([^?]*)?.*$/', '$1', $canonical_url);
            }

            // Don't send any cookie
            Context::getContext()->cookie->disallowWriting();
            if (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_ && $_SERVER['REQUEST_URI'] != __PS_BASE_URI__) {
                die('[Debug] This page has moved<br />Please use the following URL instead: <a href="'.$final_url.'">'.$final_url.'</a>');
            }

            $redirect_type = Configuration::get('PS_CANONICAL_REDIRECT') == 2 ? '301' : '302';
            header('HTTP/1.0 '.$redirect_type.' Moved');
            header('Cache-Control: no-cache');
            Tools::redirectLink($final_url);
        }
    }

    /**
     * Geolocation management
     *
     * @param Country $default_country
     * @return Country|false
     */
    protected function geolocationManagement($default_country)
    {
        if (!in_array($_SERVER['SERVER_NAME'], array('localhost', '127.0.0.1'))) {
            /* Check if Maxmind Database exists */
            if (@filemtime(_PS_GEOIP_DIR_._PS_GEOIP_CITY_FILE_)) {
                if (!isset($this->context->cookie->iso_code_country) || (isset($this->context->cookie->iso_code_country) && !in_array(strtoupper($this->context->cookie->iso_code_country), explode(';', Configuration::get('PS_ALLOWED_COUNTRIES'))))) {
                    include_once(_PS_GEOIP_DIR_.'geoipcity.inc');

                    $gi = geoip_open(realpath(_PS_GEOIP_DIR_._PS_GEOIP_CITY_FILE_), GEOIP_STANDARD);
                    $record = geoip_record_by_addr($gi, Tools::getRemoteAddr());

                    if (is_object($record)) {
                        if (!in_array(strtoupper($record->country_code), explode(';', Configuration::get('PS_ALLOWED_COUNTRIES'))) && !FrontController::isInWhitelistForGeolocation()) {
                            if (Configuration::get('PS_GEOLOCATION_BEHAVIOR') == _PS_GEOLOCATION_NO_CATALOG_) {
                                $this->restrictedCountry = true;
                            } elseif (Configuration::get('PS_GEOLOCATION_BEHAVIOR') == _PS_GEOLOCATION_NO_ORDER_) {
                                $this->warning[] = sprintf($this->l('You cannot place a new order from your country (%s).'), $record->country_name);
                            }
                        } else {
                            $has_been_set = !isset($this->context->cookie->iso_code_country);
                            $this->context->cookie->iso_code_country = strtoupper($record->country_code);
                        }
                    }
                }

                if (isset($this->context->cookie->iso_code_country) && $this->context->cookie->iso_code_country && !Validate::isLanguageIsoCode($this->context->cookie->iso_code_country)) {
                    $this->context->cookie->iso_code_country = Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT'));
                }

                if (isset($this->context->cookie->iso_code_country) && ($id_country = (int)Country::getByIso(strtoupper($this->context->cookie->iso_code_country)))) {
                    /* Update defaultCountry */
                    if ($default_country->iso_code != $this->context->cookie->iso_code_country) {
                        $default_country = new Country($id_country);
                    }
                    if (isset($has_been_set) && $has_been_set) {
                        $this->context->cookie->id_currency = (int)($default_country->id_currency ? (int)$default_country->id_currency : (int)Configuration::get('PS_CURRENCY_DEFAULT'));
                    }
                    return $default_country;
                } elseif (Configuration::get('PS_GEOLOCATION_NA_BEHAVIOR') == _PS_GEOLOCATION_NO_CATALOG_ && !FrontController::isInWhitelistForGeolocation()) {
                    $this->restrictedCountry = true;
                } elseif (Configuration::get('PS_GEOLOCATION_NA_BEHAVIOR') == _PS_GEOLOCATION_NO_ORDER_ && !FrontController::isInWhitelistForGeolocation()) {
                    $this->warning[] = sprintf($this->l('You cannot place a new order from your country (%s).'), (isset($record->country_name) && $record->country_name) ? $record->country_name : $this->l('Undefined'));
                }
            }
        }
        return false;
    }

    /**
     * Sets controller CSS and JS files.
     *
     * @return bool
     */
    public function setMedia()
    {
        $this->addCSS([
            _THEME_CSS_DIR_ . 'theme.css',
            _THEME_CSS_DIR_ . 'custom.css',
        ]);

        $this->addJS([
            _THEME_JS_DIR_.'theme.js',
            _THEME_JS_DIR_.'custom.js',
        ]);

        if (Configuration::get('PS_COMPARATOR_MAX_ITEM') > 0) {
            $this->addJS(_THEME_JS_DIR_.'products-comparison.js');
        }

        Media::addJsDef(array('full_language_code' => $this->context->language->language_code));

        // Execute Hook FrontController SetMedia
        Hook::exec('actionFrontControllerSetMedia', array());

        return true;
    }

    /**
     * Initializes page header variables
     */
    public function initHeader()
    {
        /** @see P3P Policies (http://www.w3.org/TR/2002/REC-P3P-20020416/#compact_policies) */
        header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
        header('Powered-By: PrestaShop');

        // Hooks are voluntary out the initialize array (need those variables already assigned)
        $this->context->smarty->assign(array(
            'time'                  => time(),
            'static_token'          => Tools::getToken(false),
            'token'                 => Tools::getToken(),
            'priceDisplayPrecision' => _PS_PRICE_DISPLAY_PRECISION_,
            'content_only'          => (int)Tools::getValue('content_only')
        ));
    }

    /**
     * Initializes page footer variables
     */
    public function initFooter()
    {
        $this->context->smarty->assign(array(
            'HOOK_FOOTER'            => Hook::exec('displayFooter'),
            'conditions'             => Configuration::get('PS_CONDITIONS'),
            'id_cgv'                 => Configuration::get('PS_CONDITIONS_CMS_ID'),
            'PS_SHOP_NAME'           => Configuration::get('PS_SHOP_NAME'),
            'PS_ALLOW_MOBILE_DEVICE' => isset($_SERVER['HTTP_USER_AGENT']) && (bool)Configuration::get('PS_ALLOW_MOBILE_DEVICE') && @filemtime(_PS_THEME_MOBILE_DIR_)
        ));

        /**
         * RTL support
         * rtl.css overrides theme css files for RTL
         * iso_code.css overrides default font for every language (optional)
         */
        if ($this->context->language->is_rtl) {
            $this->addCSS(_THEME_CSS_DIR_.'rtl.css');
            $this->addCSS(_THEME_CSS_DIR_.$this->context->language->iso_code.'.css');
        }
    }

    /**
     * Checks if the user can use Live Edit feature
     *
     * @return bool
     */
    public function checkLiveEditAccess()
    {
        if (!Tools::isSubmit('live_edit') || !Tools::getValue('ad') || !Tools::getValue('liveToken')) {
            return false;
        }

        if (Tools::getValue('liveToken') != Tools::getAdminToken('AdminModulesPositions'.(int)Tab::getIdFromClassName('AdminModulesPositions').(int)Tools::getValue('id_employee'))) {
            return false;
        }

        return is_dir(_PS_CORE_DIR_.DIRECTORY_SEPARATOR.Tools::getValue('ad'));
    }

    /**
     * Renders Live Edit widget
     *
     * @return string HTML
     */
    public function getLiveEditFooter()
    {
        if ($this->checkLiveEditAccess()) {
            $data = $this->context->smarty->createData();
            $data->assign(array(
                'ad'        => Tools::getValue('ad'),
                'live_edit' => true,
                'hook_list' => Hook::$executed_hooks,
                'id_shop'   => $this->context->shop->id
            ));
            return $this->context->smarty->createTemplate(_PS_ALL_THEMES_DIR_.'live_edit.tpl', $data)->fetch();
        } else {
            return '';
        }
    }

    /**
     * Assigns product list page sorting variables
     */
    public function productSort()
    {
        // $this->orderBy = Tools::getProductsOrder('by', Tools::getValue('orderby'));
        // $this->orderWay = Tools::getProductsOrder('way', Tools::getValue('orderway'));
        // 'orderbydefault' => Tools::getProductsOrder('by'),
        // 'orderwayposition' => Tools::getProductsOrder('way'), // Deprecated: orderwayposition
        // 'orderwaydefault' => Tools::getProductsOrder('way'),

        $stock_management = Configuration::get('PS_STOCK_MANAGEMENT') ? true : false; // no display quantity order if stock management disabled
        $order_by_values  = array(0 => 'name', 1 => 'price', 2 => 'date_add', 3 => 'date_upd', 4 => 'position', 5 => 'manufacturer_name', 6 => 'quantity', 7 => 'reference');
        $order_way_values = array(0 => 'asc', 1 => 'desc');

        $this->orderBy  = Tools::strtolower(Tools::getValue('orderby', $order_by_values[(int)Configuration::get('PS_PRODUCTS_ORDER_BY')]));
        $this->orderWay = Tools::strtolower(Tools::getValue('orderway', $order_way_values[(int)Configuration::get('PS_PRODUCTS_ORDER_WAY')]));

        if (!in_array($this->orderBy, $order_by_values)) {
            $this->orderBy = $order_by_values[0];
        }

        if (!in_array($this->orderWay, $order_way_values)) {
            $this->orderWay = $order_way_values[0];
        }

        $this->context->smarty->assign(array(
            'orderby'          => $this->orderBy,
            'orderway'         => $this->orderWay,
            'orderbydefault'   => $order_by_values[(int)Configuration::get('PS_PRODUCTS_ORDER_BY')],
            'orderwayposition' => $order_way_values[(int)Configuration::get('PS_PRODUCTS_ORDER_WAY')], // Deprecated: orderwayposition
            'orderwaydefault'  => $order_way_values[(int)Configuration::get('PS_PRODUCTS_ORDER_WAY')],
            'stock_management' => (int)$stock_management
        ));
    }

    /**
     * Assigns product list page pagination variables
     *
     * @param int|null $total_products
     * @throws PrestaShopException
     */
    public function pagination($total_products = null)
    {
        if (!self::$initialized) {
            $this->init();
        } elseif (!$this->context) {
            $this->context = Context::getContext();
        }

        // Retrieve the default number of products per page and the other available selections
        $default_products_per_page = max(1, (int)Configuration::get('PS_PRODUCTS_PER_PAGE'));
        $n_array = array($default_products_per_page, $default_products_per_page * 2, $default_products_per_page * 5);

        if ((int)Tools::getValue('n') && (int)$total_products > 0) {
            $n_array[] = $total_products;
        }
        // Retrieve the current number of products per page (either the default, the GET parameter or the one in the cookie)
        $this->n = $default_products_per_page;
        if (isset($this->context->cookie->nb_item_per_page) && in_array($this->context->cookie->nb_item_per_page, $n_array)) {
            $this->n = (int)$this->context->cookie->nb_item_per_page;
        }

        if ((int)Tools::getValue('n') && in_array((int)Tools::getValue('n'), $n_array)) {
            $this->n = (int)Tools::getValue('n');
        }

        // Retrieve the page number (either the GET parameter or the first page)
        $this->p = (int)Tools::getValue('p', 1);
        // If the parameter is not correct then redirect (do not merge with the previous line, the redirect is required in order to avoid duplicate content)
        if (!is_numeric($this->p) || $this->p < 1) {
            Tools::redirect($this->context->link->getPaginationLink(false, false, $this->n, false, 1, false));
        }

        // Remove the page parameter in order to get a clean URL for the pagination template
        $current_url = preg_replace('/(?:(\?)|&amp;)p=\d+/', '$1', Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']));

        if ($this->n != $default_products_per_page || isset($this->context->cookie->nb_item_per_page)) {
            $this->context->cookie->nb_item_per_page = $this->n;
        }

        $pages_nb = ceil($total_products / (int)$this->n);
        if ($this->p > $pages_nb && $total_products != 0) {
            Tools::redirect($this->context->link->getPaginationLink(false, false, $this->n, false, $pages_nb, false));
        }

        $range = 2; /* how many pages around page selected */
        $start = (int)($this->p - $range);
        if ($start < 1) {
            $start = 1;
        }

        $stop = (int)($this->p + $range);
        if ($stop > $pages_nb) {
            $stop = (int)$pages_nb;
        }

        $this->context->smarty->assign(array(
            'nb_products'       => $total_products,
            'products_per_page' => $this->n,
            'pages_nb'          => $pages_nb,
            'p'                 => $this->p,
            'n'                 => $this->n,
            'nArray'            => $n_array,
            'range'             => $range,
            'start'             => $start,
            'stop'              => $stop,
            'current_url'       => $current_url,
        ));
    }

    /**
     * Sets and returns customer groups that the current customer(visitor) belongs to.
     *
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public static function getCurrentCustomerGroups()
    {
        if (!Group::isFeatureActive()) {
            return array();
        }

        $context = Context::getContext();
        if (!isset($context->customer) || !$context->customer->id) {
            return array();
        }

        if (!is_array(self::$currentCustomerGroups)) {
            self::$currentCustomerGroups = array();
            $result = Db::getInstance()->executeS('SELECT id_group FROM '._DB_PREFIX_.'customer_group WHERE id_customer = '.(int)$context->customer->id);
            foreach ($result as $row) {
                self::$currentCustomerGroups[] = $row['id_group'];
            }
        }

        return self::$currentCustomerGroups;
    }

    /**
     * Checks if user's location is whitelisted.
     *
     * @staticvar bool|null $allowed
     * @return bool
     */
    protected static function isInWhitelistForGeolocation()
    {
        static $allowed = null;

        if ($allowed !== null) {
            return $allowed;
        }

        $allowed = false;
        $user_ip = Tools::getRemoteAddr();
        $ips = array();

        // retrocompatibility
        $ips_old = explode(';', Configuration::get('PS_GEOLOCATION_WHITELIST'));
        if (is_array($ips_old) && count($ips_old)) {
            foreach ($ips_old as $ip) {
                $ips = array_merge($ips, explode("\n", $ip));
            }
        }

        $ips = array_map('trim', $ips);
        if (is_array($ips) && count($ips)) {
            foreach ($ips as $ip) {
                if (!empty($ip) && preg_match('/^'.$ip.'.*/', $user_ip)) {
                    $allowed = true;
                }
            }
        }

        return $allowed;
    }

    /**
     * Checks if token is valid
     *
     * @since 1.5.0.1
     * @return bool
     */
    public function isTokenValid()
    {
        if (!Configuration::get('PS_TOKEN_ENABLE')) {
            return true;
        }

        return (strcasecmp(Tools::getToken(false), Tools::getValue('token')) == 0);
    }

    /**
     * Adds a media file(s) (CSS, JS) to page header
     *
     * @param string|array $media_uri Path to file, or an array of paths like: array(array(uri => media_type), ...)
     * @param string|null $css_media_type CSS media type
     * @param int|null $offset
     * @param bool $remove If True, removes media files
     * @param bool $check_path If true, checks if files exists
     * @return true|void
     */
    public function addMedia($media_uri, $css_media_type = null, $offset = null, $remove = false, $check_path = true)
    {
        if (!is_array($media_uri)) {
            if ($css_media_type) {
                $media_uri = array($media_uri => $css_media_type);
            } else {
                $media_uri = array($media_uri);
            }
        }

        $list_uri = array();
        foreach ($media_uri as $file => $media) {
            if (!Validate::isAbsoluteUrl($media)) {
                $different = 0;
                $different_css = 0;
                $type = 'css';
                if (!$css_media_type) {
                    $type = 'js';
                    $file = $media;
                }
                if (strpos($file, __PS_BASE_URI__.'modules/') === 0) {
                    $override_path = str_replace(__PS_BASE_URI__.'modules/', _PS_ROOT_DIR_.'/themes/'._THEME_NAME_.'/'.$type.'/modules/', $file, $different);
                    if (strrpos($override_path, $type.'/'.basename($file)) !== false) {
                        $override_path_css = str_replace($type.'/'.basename($file), basename($file), $override_path, $different_css);
                    }

                    if ($different && @filemtime($override_path)) {
                        $file = str_replace(__PS_BASE_URI__.'modules/', __PS_BASE_URI__.'themes/'._THEME_NAME_.'/'.$type.'/modules/', $file, $different);
                    } elseif ($different_css && @filemtime($override_path_css)) {
                        $file = $override_path_css;
                    }
                    if ($css_media_type) {
                        $list_uri[$file] = $media;
                    } else {
                        $list_uri[] = $file;
                    }
                } else {
                    $list_uri[$file] = $media;
                }
            } else {
                $list_uri[$file] = $media;
            }
        }

        if ($remove) {
            if ($css_media_type) {
                return parent::removeCSS($list_uri, $css_media_type);
            }
            return parent::removeJS($list_uri);
        }

        if ($css_media_type) {
            return parent::addCSS($list_uri, $css_media_type, $offset, $check_path);
        }

        return parent::addJS($list_uri, $check_path);
    }

    /**
     * Removes media file(s) from page header
     *
     * @param string|array $media_uri Path to file, or an array paths of like: array(array(uri => media_type), ...)
     * @param string|null $css_media_type CSS media type
     * @param bool $check_path If true, checks if files exists
     */
    public function removeMedia($media_uri, $css_media_type = null, $check_path = true)
    {
        FrontController::addMedia($media_uri, $css_media_type, null, true, $check_path);
    }

    /**
     * Add one or several CSS for front, checking if css files are overridden in theme/css/modules/ directory
     * @see Controller::addCSS()
     *
     * @param array|string $css_uri $media_uri Path to file, or an array of paths like: array(array(uri => media_type), ...)
     * @param string $css_media_type CSS media type
     * @param int|null $offset
     * @param bool $check_path If true, checks if files exists
     * @return true|void
     */
    public function addCSS($css_uri, $css_media_type = 'all', $offset = null, $check_path = true)
    {
        return FrontController::addMedia($css_uri, $css_media_type, $offset = null, false, $check_path);
    }

    /**
     * Removes CSS file(s) from page header
     *
     * @param array|string $css_uri $media_uri Path to file, or an array of paths like: array(array(uri => media_type), ...)
     * @param string $css_media_type CSS media type
     * @param bool $check_path If true, checks if files exists
     */
    public function removeCSS($css_uri, $css_media_type = 'all', $check_path = true)
    {
        return FrontController::removeMedia($css_uri, $css_media_type, $check_path);
    }

    /**
     * Add one or several JS files for front, checking if js files are overridden in theme/js/modules/ directory
     * @see Controller::addJS()
     *
     * @param array|string $js_uri Path to file, or an array of paths
     * @param bool $check_path If true, checks if files exists
     * @return true|void
     */
    public function addJS($js_uri, $check_path = true)
    {
        if (_PS_MODE_DEV_ && Tools::getValue('debug-disable-javascript')) {
            return;
        }
        return Frontcontroller::addMedia($js_uri, null, null, false, $check_path);
    }

    /**
     * Removes JS file(s) from page header
     *
     * @param array|string $js_uri Path to file, or an array of paths
     * @param bool $check_path If true, checks if files exists
     */
    public function removeJS($js_uri, $check_path = true)
    {
        return FrontController::removeMedia($js_uri, null, $check_path);
    }

    /**
     * Recovers cart information
     *
     * @return int|false
     */
    protected function recoverCart()
    {
        if (($id_cart = (int)Tools::getValue('recover_cart')) && Tools::getValue('token_cart') == md5(_COOKIE_KEY_.'recover_cart_'.$id_cart)) {
            $cart = new Cart((int)$id_cart);
            if (Validate::isLoadedObject($cart)) {
                $customer = new Customer((int)$cart->id_customer);
                if (Validate::isLoadedObject($customer)) {
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
        } else {
            return false;
        }
    }

    /**
     * Sets template file for page content output
     *
     * @param string $default_template
     */
    public function setTemplate($default_template)
    {
        $template = $this->getOverrideTemplate();
        if ($template) {
            parent::setTemplate($template);
        } else {
            parent::setTemplate($default_template);
        }
    }

    /**
     * Returns an overridden template path (if any) for this controller.
     * If not overridden, will return false. This method can be easily overriden in a
     * specific controller.
     *
    * @since 1.5.0.13
    * @return string|bool
    */
    public function getOverrideTemplate()
    {
        return Hook::exec('DisplayOverrideTemplate', array('controller' => $this));
    }

    /**
     * Removed in PrestaShop 1.7
     *
     * @return bool
     */
    protected function useMobileTheme()
    {
        return false;
    }

    /**
     * Returns theme directory (regular or mobile)
     *
     * @return string
     */
    protected function getThemeDir()
    {
        return _PS_THEME_DIR_;
    }


    /**
     * Returns theme override directory (regular or mobile)
     *
     * @return string
     */
    protected function getOverrideThemeDir()
    {
        return _PS_THEME_OVERRIDE_DIR_;
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
     * @since 1.5.0.13
     * @return bool|string
     */
    public function getLayout()
    {
        $layout = 'layout/layout-full-width.tpl';

        if ($this->display_column_left && $this->display_column_right) {
            $layout = 'layout/layout-both-columns.tpl';
        } elseif ($this->display_column_left) {
            $layout = 'layout/layout-left-column.tpl';
        } elseif ($this->display_column_right) {
            $layout = 'layout/layout-right-column.tpl';
        }

        $entity = $this->php_self;
        $id_item = (int)Tools::getValue('id_'.$entity);

        $layout_override_dir  = $this->getOverrideThemeDir();
        if ($entity) {
            if ($id_item > 0 && file_exists($layout_override_dir.'layout-'.$entity.'-'.$id_item.'.tpl')) {
                $layout = basename($layout_override_dir).'/layout-'.$entity.'-'.$id_item.'.tpl';
            } elseif (file_exists($layout_override_dir.'layout-'.$entity.'.tpl')) {
                $layout = basename($layout_override_dir).'/layout-'.$entity.'.tpl';
            }
        }

        return $layout;
    }

    /**
     * Returns template path
     *
     * @param string $template
     * @return string
     */
    public function getTemplatePath($template)
    {
        return $template;
    }

    /**
     * Renders and adds color list HTML for each product in a list
     *
     * @param array $products
     */
    public function addColorsToProductList(&$products)
    {
        if (!is_array($products) || !count($products) || !file_exists(_PS_THEME_DIR_.'product-list-colors.tpl')) {
            return;
        }

        $products_need_cache = array();
        foreach ($products as &$product) {
            if (!$this->isCached(_PS_THEME_DIR_.'product-list-colors.tpl', $this->getColorsListCacheId($product['id_product']))) {
                $products_need_cache[] = (int)$product['id_product'];
            }
        }

        unset($product);

        $colors = false;
        if (count($products_need_cache)) {
            $colors = Product::getAttributesColorList($products_need_cache);
        }

        Tools::enableCache();
        foreach ($products as &$product) {
            $tpl = $this->context->smarty->createTemplate(_PS_THEME_DIR_.'product-list-colors.tpl', $this->getColorsListCacheId($product['id_product']));
            if (isset($colors[$product['id_product']])) {
                $tpl->assign(array(
                    'id_product'  => $product['id_product'],
                    'colors_list' => $colors[$product['id_product']],
                    'link'        => Context::getContext()->link,
                    'img_col_dir' => _THEME_COL_DIR_,
                    'col_img_dir' => _PS_COL_IMG_DIR_
                ));
            }

            if (!in_array($product['id_product'], $products_need_cache) || isset($colors[$product['id_product']])) {
                $product['color_list'] = $tpl->fetch(_PS_THEME_DIR_.'product-list-colors.tpl', $this->getColorsListCacheId($product['id_product']));
            } else {
                $product['color_list'] = '';
            }
        }
        Tools::restoreCacheSettings();
    }

    /**
     * Returns cache ID for product color list
     *
     * @param int $id_product
     * @return string
     */
    protected function getColorsListCacheId($id_product)
    {
        return Product::getColorsListCacheId($id_product);
    }

    public function getTemplateVarUrls()
    {
        $http = Tools::getCurrentUrlProtocolPrefix();

        $urls = [
            'base_url' => $this->context->shop->getBaseURL(true, true),
        ];

        /* StarterTheme: Are these URLs really useful ? */
        $assign_array = array(
            'img_ps_url'    => _PS_IMG_,
            'img_cat_url'   => _THEME_CAT_DIR_,
            'img_lang_url'  => _THEME_LANG_DIR_,
            'img_prod_url'  => _THEME_PROD_DIR_,
            'img_manu_url'  => _THEME_MANU_DIR_,
            'img_sup_url'   => _THEME_SUP_DIR_,
            'img_ship_url'  => _THEME_SHIP_DIR_,
            'img_store_url' => _THEME_STORE_DIR_,
            'img_col_url'   => _THEME_COL_DIR_,
            'img_url'       => _THEME_IMG_DIR_,
            'css_url'       => _THEME_CSS_DIR_,
            'js_url'        => _THEME_JS_DIR_,
            'pic_url'       => _THEME_PROD_PIC_DIR_
        );

        foreach ($assign_array as $assign_key => $assign_value) {
            if (substr($assign_value, 0, 1) == '/' || $this->ssl) {
                $urls[$assign_key] = $http.Tools::getMediaServer($assign_value).$assign_value;
            } else {
                $urls[$assign_key] = $assign_value;
            }
        }

        $pages = [];
        $p = [
            'address', 'addresses', 'authentication', 'cart', 'category', 'cms', 'contact',
            'discount', 'guest-tracking', 'history', 'identity', 'index', 'my-account',
            'order-confirmation', 'order-detail', 'order-follow', 'order', 'order-return',
            'order-slip', 'pagenotfound', 'password', 'pdf-invoice', 'pdf-order-return', 'pdf-order-slip',
            'prices-drop', 'product', 'search', 'sitemap', 'stores', 'supplier'
        ];
        foreach ($p as $page_name) {
            $index = str_replace('-', '_', $page_name);
            $pages[$index] = $this->context->link->getPageLink($page_name, true);
        }
        $pages['register'] = $this->context->link->getPageLink('authentication', true, null, ['create_account' => '1']);
        $pages['order_login'] = $this->context->link->getPageLink('order', true, null, ['login' => '1']);
        $urls['pages'] = $pages;

        $urls['theme_assets'] = __PS_BASE_URI__ . 'themes/' . $this->context->theme->directory . '/assets/';

        $urls['actions'] = [
            'logout' => $this->context->link->getPageLink('index', true, null, 'mylogout'),
        ];

        return $urls;
    }

    public function getTemplateVarFeatureActive()
    {
        return [
            'b2b' => (bool)Configuration::get('PS_B2B_ENABLE'),
            'optin' => (bool)Configuration::get('PS_CUSTOMER_OPTIN'),
            'newsletter' => Configuration::get('PS_CUSTOMER_NWSL') || (Module::isInstalled('blocknewsletter') && Module::getInstanceByName('blocknewsletter')->active),
        ];
    }

    public function getTemplateVarCurrency()
    {
        $curr = [];
        $fields = ['name', 'iso_code', 'iso_code_num', 'sign'];
        foreach ($fields as $field_name) {
            $curr[$field_name] = $this->context->currency->{$field_name};
        }

        return $curr;
    }

    public function getTemplateVarCustomer($customer = null)
    {
        if (Validate::isLoadedObject($customer)) {
            $cust = $this->objectSerializer->toArray($customer);
        } else {
            $cust = $this->objectSerializer->toArray($this->context->customer);
        }

        unset($cust['secure_key']);
        unset($cust['passwd']);
        unset($cust['show_public_prices']);
        unset($cust['deleted']);
        unset($cust['id_lang']);

        $cust['is_logged'] = $this->context->customer->isLogged(true);

        $cust['gender'] = $this->objectSerializer->toArray(new Gender($cust['id_gender']));
        unset($cust['id_gender']);

        $cust['risk'] = $this->objectSerializer->toArray(new Risk($cust['id_risk']));
        unset($cust['id_risk']);

        $addresses = $this->context->customer->getSimpleAddresses();
        foreach ($addresses as &$a) {
            $a['formatted'] = AddressFormat::generateAddress(new Address($a['id']), array(), '<br />');
        }
        $cust['addresses'] = $addresses;

        return $cust;
    }

    public function getTemplateVarShop()
    {
        $address = $this->context->shop->getAddress();

        $shop = [
            'name' => Configuration::get('PS_SHOP_NAME'),
            'email' => Configuration::get('PS_SHOP_EMAIL'),
            'registration_number' => Configuration::get('PS_SHOP_DETAILS'),

            'long' =>Configuration::get('PS_STORES_CENTER_LONG'),
            'lat' =>Configuration::get('PS_STORES_CENTER_LAT'),

            'logo' => (Configuration::get('PS_LOGO')) ? _PS_IMG_.Configuration::get('PS_LOGO') : '',
            'stores_icon' => (Configuration::get('PS_STORES_ICON')) ? _PS_IMG_.Configuration::get('PS_STORES_ICON') : '',
            'favicon' => (Configuration::get('PS_FAVICON')) ? _PS_IMG_.Configuration::get('PS_FAVICON') : '',
            'favicon_update_time' => Configuration::get('PS_IMG_UPDATE_TIME'),

            'address' => [
                'formatted' => AddressFormat::generateAddress($address, array(), '<br />'),
                'address1' => $address->address1,
                'address2' => $address->address2,
                'postcode' => $address->postcode,
                'city' => $address->city,
                'state' => (new State($address->id_state))->name[$this->context->language->id],
                'country' => (new Country($address->id_country))->name[$this->context->language->id],
            ],
            'phone' => Configuration::get('PS_SHOP_PHONE'),
            'fax' => Configuration::get('PS_SHOP_FAX'),
        ];

        return $shop;
    }

    public function getTemplateVarPage()
    {
        $page_name = $this->getPageName();
        $meta_tags = Meta::getMetaTags($this->context->language->id, $page_name);

        $page = [
            'title' => $meta_tags['meta_title'],
            'description' => $meta_tags['meta_description'],
            'keywords' => $meta_tags['meta_keywords'],
            'page_name' => $page_name,
            'body_classes' => '',
        ];

        return $page;
    }

    public function getPageName()
    {
        // Are we in a payment module
        $module_name = '';
        if (Validate::isModuleName(Tools::getValue('module'))) {
            $module_name = Tools::getValue('module');
        }

        if (!empty($this->page_name)) {
            $page_name = $this->page_name;
        } elseif (!empty($this->php_self)) {
            $page_name = $this->php_self;
        } elseif (Tools::getValue('fc') == 'module' && $module_name != '' && (Module::getInstanceByName($module_name) instanceof PaymentModule)) {
            $page_name = 'module-payment-submit';
        } elseif (preg_match('#^'.preg_quote($this->context->shop->physical_uri, '#').'modules/([a-zA-Z0-9_-]+?)/(.*)$#', $_SERVER['REQUEST_URI'], $m)) {
            // @retrocompatibility Are we in a module ?
            $page_name = 'module-'.$m[1].'-'.str_replace(array('.php', '/'), array('', '-'), $m[2]);
        } else {
            $page_name = Dispatcher::getInstance()->getController();
            $page_name = (preg_match('/^[0-9]/', $page_name) ? 'page_'.$page_name : $page_name);
        }

        return $page_name;
    }

    protected function render($template, $params = [])
    {
        $scope = $this->context->smarty->createData(
            $this->context->smarty
        );

        $scope->assign($params);
        $tpl = $this->context->smarty->createTemplate(
            $template,
            $scope
        );

        return $tpl->fetch();
    }
}
