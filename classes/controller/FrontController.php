<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
use PrestaShop\PrestaShop\Adapter\Cart\CartPresenter;
use PrestaShop\PrestaShop\Adapter\ObjectPresenter;
use PrestaShop\PrestaShop\Adapter\Configuration as ConfigurationAdapter;
use PrestaShop\PrestaShop\Core\Filter\CollectionFilter;
use PrestaShop\PrestaShop\Core\Filter\FrontEndObject\ConfigurationFilter;
use PrestaShop\PrestaShop\Core\Filter\FrontEndObject\CustomerFilter;
use PrestaShop\PrestaShop\Core\Filter\FrontEndObject\ShopFilter;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Debug\Debug;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class FrontControllerCore extends Controller
{
    /**
     * @deprecated Deprecated shortcuts as of 1.5.0.1 - Use $context->smarty instead
     *
     * @var Smarty
     */
    protected static $smarty;

    /**
     * @deprecated Deprecated shortcuts as of 1.5.0.1 - Use $context->cookie instead
     *
     * @var Cookie
     */
    protected static $cookie;

    /**
     * @deprecated Deprecated shortcuts as of 1.5.0.1 - Use $context->link instead
     *
     * @var Link
     */
    protected static $link;

    /**
     * @deprecated Deprecated shortcuts as of 1.5.0.1 - Use $context->cart instead
     *
     * @var Cart
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
     *
     * @var bool
     */
    public $guestAllowed = false;

    /**
     * Route of PrestaShop page to redirect to after forced login.
     *
     * @see $auth
     *
     * @var bool
     */
    public $authRedirection = false;

    /** @var bool SSL connection flag */
    public $ssl = false;

    /** @var bool If true, switches display to restricted country page during init. */
    protected $restrictedCountry = Country::GEOLOC_ALLOWED;

    /** @var bool If true, forces display to maintenance page. */
    protected $maintenance = false;

    /** @var string[] Adds excluded $_GET keys for redirection */
    protected $redirectionExtraExcludedKeys = array();

    /**
     * True if controller has already been initialized.
     * Prevents initializing controller more than once.
     *
     * @var bool
     */
    public static $initialized = false;

    /**
     * @var array Holds current customer's groups
     */
    protected static $currentCustomerGroups;

    /**
     * @var int
     */
    public $nb_items_per_page;

    /**
     * @var object ObjectSerializer
     */
    public $objectPresenter;

    /**
     * @var object CartPresenter
     */
    public $cart_presenter;

    /**
     * @var object TemplateFinder
     */
    private $templateFinder;

    /**
     * @var object StylesheetManager
     */
    protected $stylesheetManager;

    /**
     * @var object JavascriptManager
     */
    protected $javascriptManager;

    /**
     * @var object CccReducer
     */
    protected $cccReducer;

    /**
     * Controller constructor.
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

        $this->objectPresenter = new ObjectPresenter();
        $this->cart_presenter = new CartPresenter();
        $this->templateFinder = new TemplateFinder($this->context->smarty->getTemplateDir(), '.tpl');
        $this->stylesheetManager = new StylesheetManager(
            array(_PS_THEME_URI_, _PS_PARENT_THEME_URI_, __PS_BASE_URI__),
            new ConfigurationAdapter()
        );
        $this->javascriptManager = new JavascriptManager(
            array(_PS_THEME_URI_, _PS_PARENT_THEME_URI_, __PS_BASE_URI__),
            new ConfigurationAdapter()
        );
        $this->cccReducer = new CccReducer(
            _PS_THEME_DIR_.'assets/cache/',
            new ConfigurationAdapter(),
            new Filesystem()
        );
    }

    /**
     * Check if the controller is available for the current user/visitor.
     *
     * @see Controller::checkAccess()
     *
     * @return bool
     */
    public function checkAccess()
    {
        return true;
    }

    /**
     * Check if the current user/visitor has valid view permissions.
     *
     * @see Controller::viewAccess
     *
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
        /*
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

        // enable Symfony error handler if debug mode enabled
        $this->initDebugguer();

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

        if ($id_cart = (int) $this->recoverCart()) {
            $this->context->cookie->id_cart = (int) $id_cart;
        }

        if ($this->auth && !$this->context->customer->isLogged($this->guestAllowed)) {
            Tools::redirect('index.php?controller=authentication'.($this->authRedirection ? '&back='.$this->authRedirection : ''));
        }

        /* Theme is missing */
        if (!is_dir(_PS_THEME_DIR_)) {
            throw new PrestaShopException(
                $this->trans(
                    'Current theme is unavailable. Please check your theme\'s directory name ("%s") and permissions.',
                    array(basename(rtrim(_PS_THEME_DIR_, '/\\'))),
                    'Admin.Design.Notification'
                ));
        }

        if (Configuration::get('PS_GEOLOCATION_ENABLED')) {
            if (($new_default = $this->geolocationManagement($this->context->country)) && Validate::isLoadedObject($new_default)) {
                $this->context->country = $new_default;
            }
        } elseif (Configuration::get('PS_DETECT_COUNTRY')) {
            $has_currency = isset($this->context->cookie->id_currency) && (int) $this->context->cookie->id_currency;
            $has_country = isset($this->context->cookie->iso_code_country) && $this->context->cookie->iso_code_country;
            $has_address_type = false;

            if ((int) $this->context->cookie->id_cart && ($cart = new Cart($this->context->cookie->id_cart)) && Validate::isLoadedObject($cart)) {
                $has_address_type = isset($cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) && $cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
            }

            if ((!$has_currency || $has_country) && !$has_address_type) {
                $id_country = $has_country && !Validate::isLanguageIsoCode($this->context->cookie->iso_code_country) ?
                    (int) Country::getByIso(strtoupper($this->context->cookie->iso_code_country)) : (int) Tools::getCountry();

                $country = new Country($id_country, (int) $this->context->cookie->id_lang);

                if (!$has_currency && validate::isLoadedObject($country) && $this->context->country->id !== $country->id) {
                    $this->context->country = $country;
                    $this->context->cookie->id_currency = (int) Currency::getCurrencyInstance($country->id_currency ? (int) $country->id_currency : (int) Configuration::get('PS_CURRENCY_DEFAULT'))->id;
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
        if ((int) $this->context->cookie->id_cart) {
            if (!isset($cart)) {
                $cart = new Cart($this->context->cookie->id_cart);
            }

            if (Validate::isLoadedObject($cart) && $cart->OrderExists()) {
                PrestaShopLogger::addLog('Frontcontroller::init - Cart cannot be loaded or an order has already been placed using this cart', 1, null, 'Cart', (int) $this->context->cookie->id_cart, true);
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
                PrestaShopLogger::addLog('Frontcontroller::init - GEOLOCATION is deleting a cart', 1, null, 'Cart', (int) $this->context->cookie->id_cart, true);
                unset($this->context->cookie->id_cart, $cart);
            } elseif ($this->context->cookie->id_customer != $cart->id_customer || $this->context->cookie->id_lang != $cart->id_lang || $currency->id != $cart->id_currency) {
                // update cart values
                if ($this->context->cookie->id_customer) {
                    $cart->id_customer = (int) $this->context->cookie->id_customer;
                }
                $cart->id_lang = (int) $this->context->cookie->id_lang;
                $cart->id_currency = (int) $currency->id;
                $cart->update();
            }
            /* Select an address if not set */
            if (isset($cart) && (!isset($cart->id_address_delivery) || $cart->id_address_delivery == 0 ||
                !isset($cart->id_address_invoice) || $cart->id_address_invoice == 0) && $this->context->cookie->id_customer) {
                $to_update = false;
                if (!isset($cart->id_address_delivery) || $cart->id_address_delivery == 0) {
                    $to_update = true;
                    $cart->id_address_delivery = (int) Address::getFirstCustomerAddressId($cart->id_customer);
                }
                if (!isset($cart->id_address_invoice) || $cart->id_address_invoice == 0) {
                    $to_update = true;
                    $cart->id_address_invoice = (int) Address::getFirstCustomerAddressId($cart->id_customer);
                }
                if ($to_update) {
                    $cart->update();
                }
            }
        }

        if (!isset($cart) || !$cart->id) {
            $cart = new Cart();
            $cart->id_lang = (int) $this->context->cookie->id_lang;
            $cart->id_currency = (int) $this->context->cookie->id_currency;
            $cart->id_guest = (int) $this->context->cookie->id_guest;
            $cart->id_shop_group = (int) $this->context->shop->id_shop_group;
            $cart->id_shop = $this->context->shop->id;
            if ($this->context->cookie->id_customer) {
                $cart->id_customer = (int) $this->context->cookie->id_customer;
                $cart->id_address_delivery = (int) Address::getFirstCustomerAddressId($cart->id_customer);
                $cart->id_address_invoice = (int) $cart->id_address_delivery;
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

        $this->context->cart->checkAndUpdateAddresses();

        $this->context->smarty->assign('request_uri', Tools::safeOutput(urldecode($_SERVER['REQUEST_URI'])));

        // Automatically redirect to the canonical URL if needed
        if (!empty($this->php_self) && !Tools::getValue('ajax')) {
            $this->canonicalRedirection($this->context->link->getPageLink($this->php_self, $this->ssl, $this->context->language->id));
        }

        Product::initPricesComputation();

        $display_tax_label = $this->context->country->display_tax_label;
        if (isset($cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) && $cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) {
            $infos = Address::getCountryAndState((int) $cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
            $country = new Country((int) $infos['id_country']);
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

        /*
         * These shortcuts are DEPRECATED as of version 1.5.0.1
         * Use the Context to access objects instead.
         * Example: $this->context->cart
         */
        self::$cookie = $this->context->cookie;
        self::$cart = $cart;
        self::$smarty = $this->context->smarty;
        self::$link = $link;
        $defaultCountry = $this->context->country;

        $this->displayMaintenancePage();

        if (Country::GEOLOC_FORBIDDEN == $this->restrictedCountry) {
            $this->displayRestrictedCountryPage();
        }

        $this->iso = $iso;
        $this->context->cart = $cart;
        $this->context->currency = $currency;
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

    protected function assignGeneralPurposeVariables()
    {
        $templateVars = array(
            'cart' => $this->cart_presenter->present($this->context->cart),
            'currency' => $this->getTemplateVarCurrency(),
            'customer' => $this->getTemplateVarCustomer(),
            'language' => $this->objectPresenter->present($this->context->language),
            'page' => $this->getTemplateVarPage(),
            'shop' => $this->getTemplateVarShop(),
            'urls' => $this->getTemplateVarUrls(),
            'configuration' => $this->getTemplateVarConfiguration(),
            'field_required' => $this->context->customer->validateFieldsRequiredDatabase(),
            'breadcrumb' => $this->getBreadcrumb(),
            'link' => $this->context->link,
            'time' => time(),
            'static_token' => Tools::getToken(false),
            'token' => Tools::getToken(),
        );

        $this->context->smarty->assign($templateVars);

        Media::addJsDef(array (
            'prestashop' => $this->buildFrontEndObject($templateVars)
        ));
    }

    /**
     * Builds the "prestashop" javascript object that will be sent to the front end
     *
     * @param array $object Variables inserted in the template (see FrontController::assignGeneralPurposeVariables)
     *
     * @return array Variables to be inserted in the "prestashop" javascript object
     * @throws \PrestaShop\PrestaShop\Core\Filter\FilterException
     * @throws PrestaShopException
     */
    protected function buildFrontEndObject($object)
    {
        // keep whitelisted cart product data only
        if (isset($object['cart']['products']) && is_array($object['cart']['products'])) {
            $object['cart']['products'] = $this
                ->getProductListOutputFilter()
                ->filter($object['cart']['products']);
        }

        // keep whitelisted customer data only
        if (isset($object['customer']) && is_array($object['customer'])) {
            $object['customer'] = $this
                ->getCustomerOutputFilter()
                ->filter($object['customer']);
        }

        // keep whitelisted shop data only
        if (isset($object['shop']) && is_array($object['shop'])) {
            $object['shop'] = $this
                ->getShopOutputFilter()
                ->filter($object['shop']);
        }

        // keep whitelisted configuration data only
        if (isset($object['configuration']) && is_array($object['configuration'])) {
            $object['configuration'] = $this
                ->getConfigurationOutputFilter()
                ->filter($object['configuration']);
        }

        Hook::exec('actionBuildFrontEndObject', array(
            'obj' => &$object
        ));

        return $object;
    }

    /**
     * Initializes common front page content: header, footer and side columns.
     */
    public function initContent()
    {
        $this->assignGeneralPurposeVariables();
        $this->process();

        if (!isset($this->context->cart)) {
            $this->context->cart = new Cart();
        }

        $this->context->smarty->assign(array(
            'HOOK_HEADER' => Hook::exec('displayHeader'),
        ));
    }

    public function initFooter()
    {
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
     * @return mixed
     */
    public function getStylesheets()
    {
        $cssFileList = $this->stylesheetManager->getList();

        if (Configuration::get('PS_CSS_THEME_CACHE')) {
            $cssFileList = $this->cccReducer->reduceCss($cssFileList);
        }

        return $cssFileList;
    }

    /**
     * @return mixed
     */
    public function getJavascript()
    {
        $jsFileList = $this->javascriptManager->getList();

        if (Configuration::get('PS_JS_THEME_CACHE')) {
            $jsFileList = $this->cccReducer->reduceJs($jsFileList);
        }

        return $jsFileList;
    }

    /**
     * Redirects to redirect_after link.
     *
     * @see $redirect_after
     */
    protected function redirect()
    {
        Tools::redirectLink($this->redirect_after);
    }

    public function redirectWithNotifications()
    {
        $notifications = json_encode(array(
            'error' => $this->errors,
            'warning' => $this->warning,
            'success' => $this->success,
            'info' => $this->info,
        ));

        if (session_status() == PHP_SESSION_ACTIVE) {
            $_SESSION['notifications'] = $notifications;
        } elseif (session_status() == PHP_SESSION_NONE) {
            session_start();
            $_SESSION['notifications'] = $notifications;
        } else {
            setcookie('notifications', $notifications);
        }

        return call_user_func_array(array('Tools', 'redirect'), func_get_args());
    }

    /**
     * Renders page content.
     * Used for retrocompatibility with PS 1.4.
     */
    public function displayContent()
    {
    }

    /**
     * Compiles and outputs full page content.
     *
     * @return bool
     *
     * @throws Exception
     * @throws SmartyException
     */
    public function display()
    {
        $this->context->smarty->assign(array(
            'layout' => $this->getLayout(),
            'stylesheets' => $this->getStylesheets(),
            'javascript' => $this->getJavascript(),
            'js_custom_vars' => Media::getJsDef(),
            'notifications' => $this->prepareNotifications(),
        ));

        $this->smartyOutputContent($this->template);

        return true;
    }

    protected function smartyOutputContent($content)
    {
        $this->context->cookie->write();

        $html = '';

        if (is_array($content)) {
            foreach ($content as $tpl) {
                $html .= $this->context->smarty->fetch($tpl, null, $this->getLayout());
            }
        } else {
            $html = $this->context->smarty->fetch($content, null, $this->getLayout());
        }

        Hook::exec('actionOutputHTMLBefore', array('html' => &$html));
        echo trim($html);
    }

    protected function prepareNotifications()
    {
        $notifications = array(
            'error' => $this->errors,
            'warning' => $this->warning,
            'success' => $this->success,
            'info' => $this->info,
        );

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
        if ($this->maintenance == true || !(int) Configuration::get('PS_SHOP_ENABLE')) {
            $this->maintenance = true;
            if (!in_array(Tools::getRemoteAddr(), explode(',', Configuration::get('PS_MAINTENANCE_IP')))) {
                header('HTTP/1.1 503 Service Unavailable');
                header('Retry-After: 3600');

                $this->registerStylesheet('theme-error', '/assets/css/error.css', ['media' => 'all', 'priority' => 50]);
                $this->context->smarty->assign(array(
                    'urls' => $this->getTemplateVarUrls(),
                    'shop' => $this->getTemplateVarShop(),
                    'HOOK_MAINTENANCE' => Hook::exec('displayMaintenance', array()),
                    'maintenance_text' => Configuration::get('PS_MAINTENANCE_TEXT', (int) $this->context->language->id),
                    'stylesheets' => $this->getStylesheets(),
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

        $this->registerStylesheet('theme-error', '/assets/css/error.css', ['media' => 'all', 'priority' => 50]);
        $this->context->smarty->assign(array(
            'urls' => $this->getTemplateVarUrls(),
            'shop' => $this->getTemplateVarShop(),
            'stylesheets' => $this->getStylesheets(),
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
     * Redirects to canonical URL.
     *
     * @param string $canonical_url
     */
    protected function canonicalRedirection($canonical_url = '')
    {
        if (!$canonical_url || !Configuration::get('PS_CANONICAL_REDIRECT') || strtoupper($_SERVER['REQUEST_METHOD']) != 'GET') {
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
            $excluded_key = array_merge($excluded_key, $this->redirectionExtraExcludedKeys);
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
     * Geolocation management.
     *
     * @param Country $defaultCountry
     *
     * @return Country|false
     */
    protected function geolocationManagement($defaultCountry)
    {
        if (!in_array(Tools::getRemoteAddr(), array('localhost', '127.0.0.1'))) {
            /* Check if Maxmind Database exists */
            if (@filemtime(_PS_GEOIP_DIR_._PS_GEOIP_CITY_FILE_)) {
                if (!isset($this->context->cookie->iso_code_country) || (isset($this->context->cookie->iso_code_country) && !in_array(strtoupper($this->context->cookie->iso_code_country), explode(';', Configuration::get('PS_ALLOWED_COUNTRIES'))))) {
                    $reader = new GeoIp2\Database\Reader(_PS_GEOIP_DIR_._PS_GEOIP_CITY_FILE_);
                    try {
                        $record = $reader->city(Tools::getRemoteAddr());
                    } catch (\GeoIp2\Exception\AddressNotFoundException $e) {
                        $record = null;
                    }

                    if (is_object($record) && Validate::isLanguageIsoCode($record->country->isoCode) && (int)Country::getByIso(strtoupper($record->country->isoCode)) != 0) {
                        if (!in_array(strtoupper($record->country->isoCode), explode(';', Configuration::get('PS_ALLOWED_COUNTRIES'))) && !FrontController::isInWhitelistForGeolocation()) {
                            if (Configuration::get('PS_GEOLOCATION_BEHAVIOR') == _PS_GEOLOCATION_NO_CATALOG_) {
                                $this->restrictedCountry = Country::GEOLOC_FORBIDDEN;
                            } elseif (Configuration::get('PS_GEOLOCATION_BEHAVIOR') == _PS_GEOLOCATION_NO_ORDER_) {
                                $this->restrictedCountry = Country::GEOLOC_CATALOG_MODE;
                                $this->warning[] = $this->trans('You cannot place a new order from your country (%s).', array($record->country->name), 'Shop.Notifications.Warning');
                            }
                        } else {
                            $hasBeenSet = !isset($this->context->cookie->iso_code_country);
                            $this->context->cookie->iso_code_country = strtoupper($record->country->isoCode);
                        }
                    }
                }

                if (isset($this->context->cookie->iso_code_country) && $this->context->cookie->iso_code_country && !Validate::isLanguageIsoCode($this->context->cookie->iso_code_country)) {
                    $this->context->cookie->iso_code_country = Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT'));
                }

                if (isset($this->context->cookie->iso_code_country) && ($idCountry = (int) Country::getByIso(strtoupper($this->context->cookie->iso_code_country)))) {
                    /* Update defaultCountry */
                    if ($defaultCountry->iso_code != $this->context->cookie->iso_code_country) {
                        $defaultCountry = new Country($idCountry);
                    }
                    if (isset($hasBeenSet) && $hasBeenSet) {
                        $this->context->cookie->id_currency = (int) ($defaultCountry->id_currency ? (int) $defaultCountry->id_currency : (int) Configuration::get('PS_CURRENCY_DEFAULT'));
                    }

                    return $defaultCountry;
                } elseif (Configuration::get('PS_GEOLOCATION_NA_BEHAVIOR') == _PS_GEOLOCATION_NO_CATALOG_ && !FrontController::isInWhitelistForGeolocation()) {
                    $this->restrictedCountry = Country::GEOLOC_FORBIDDEN;
                } elseif (Configuration::get('PS_GEOLOCATION_NA_BEHAVIOR') == _PS_GEOLOCATION_NO_ORDER_ && !FrontController::isInWhitelistForGeolocation()) {
                    $this->restrictedCountry = Country::GEOLOC_CATALOG_MODE;
                    $countryName = $this->trans('Undefined', array(), 'Shop.Theme.Global');
                    if (isset($record->country->name) && $record->country->name) {
                        $countryName = $record->country->name;
                    }
                    $this->warning[] = $this->trans(
                        'You cannot place a new order from your country (%s).',
                        array($countryName),
                        'Shop.Notifications.Warning'
                    );
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
        $this->registerStylesheet('theme-main', '/assets/css/theme.css', ['media' => 'all', 'priority' => 50]);
        $this->registerStylesheet('theme-custom', '/assets/css/custom.css', ['media' => 'all', 'priority' => 1000]);

        if ($this->context->language->is_rtl) {
            $this->registerStylesheet('theme-rtl', '/assets/css/rtl.css', ['media' => 'all', 'priority' => 900]);
        }

        $this->registerJavascript('corejs', '/themes/core.js', ['position' => 'bottom', 'priority' => 0]);
        $this->registerJavascript('theme-main', '/assets/js/theme.js', ['position' => 'bottom', 'priority' => 50]);
        $this->registerJavascript('theme-custom', '/assets/js/custom.js', ['position' => 'bottom', 'priority' => 1000]);

        $assets = $this->context->shop->theme->getPageSpecificAssets($this->php_self);
        if (!empty($assets)) {
            foreach ($assets['css'] as $css) {
                $this->registerStylesheet($css['id'], $css['path'], $css);
            }
            foreach ($assets['js'] as $js) {
                $this->registerJavascript($js['id'], $js['path'], $js);
            }
        }

        // Execute Hook FrontController SetMedia
        Hook::exec('actionFrontControllerSetMedia', array());

        return true;
    }

    /**
     * Initializes page header variables.
     */
    public function initHeader()
    {
        /* @see P3P Policies (http://www.w3.org/TR/2002/REC-P3P-20020416/#compact_policies) */
        header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
        header('Powered-By: PrestaShop');
    }

    /**
     * Sets and returns customer groups that the current customer(visitor) belongs to.
     *
     * @return array
     *
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
            $result = Db::getInstance()->executeS('SELECT id_group FROM '._DB_PREFIX_.'customer_group WHERE id_customer = '.(int) $context->customer->id);
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
     *
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
     * Checks if token is valid.
     *
     * @since 1.5.0.1
     *
     * @return bool
     */
    public function isTokenValid()
    {
        if (!Configuration::get('PS_TOKEN_ENABLE')) {
            return true;
        }

        return strcasecmp(Tools::getToken(false), Tools::getValue('token')) == 0;
    }

    /**
     * @deprecated 1.7 use $this->registerJavascript() and $this->registerStylesheet() to manage your assets.
     */
    public function addMedia($media_uri, $css_media_type = null, $offset = null, $remove = false, $check_path = true)
    {
        /*
        This function has no effect in PrestaShop 1.7 theme, use $this->registerJavascript() and
        $this->registerStylesheet() to manage your assets.
         */
    }

    /**
     * @deprecated 1.7 this method has not effect with PrestaShop 1.7+
     */
    public function removeMedia($media_uri, $css_media_type = null, $check_path = true)
    {
        /*
        This function has no effect in PrestaShop 1.7 theme, use $this->registerJavascript() and
        $this->registerStylesheet() to manage your assets.
         */
    }

    public function registerStylesheet($id, $relativePath, $params = array())
    {
        if (!is_array($params)) {
            $params = array();
        }

        $default_params = [
            'media' => AbstractAssetManager::DEFAULT_MEDIA,
            'priority' => AbstractAssetManager::DEFAULT_PRIORITY,
            'inline' => false,
            'server' => 'local',
        ];

        $params = array_merge($default_params, $params);

        $this->stylesheetManager->register($id, $relativePath, $params['media'], $params['priority'], $params['inline'], $params['server']);
    }

    public function unregisterStylesheet($id)
    {
        $this->stylesheetManager->unregisterById($id);
    }

    public function registerJavascript($id, $relativePath, $params = array())
    {
        if (!is_array($params)) {
            $params = array();
        }

        $default_params = [
            'position' => AbstractAssetManager::DEFAULT_JS_POSITION,
            'priority' => AbstractAssetManager::DEFAULT_PRIORITY,
            'inline' => false,
            'attributes' => null,
            'server' => 'local',
        ];

        $params = array_merge($default_params, $params);

        $this->javascriptManager->register($id, $relativePath, $params['position'], $params['priority'], $params['inline'], $params['attributes'], $params['server']);
    }

    public function unregisterJavascript($id)
    {
        $this->javascriptManager->unregisterById($id);
    }

    /**
     * @deprecated 1.7 This function shouldn't be used, use $this->registerStylesheet() instead
     */
    public function addCSS($css_uri, $css_media_type = 'all', $offset = null, $check_path = true)
    {
        /*
        This is deprecated in PrestaShop 1.7 and has no effect in PrestaShop 1.7 theme.
        You should use registerStylesheet($id, $path, $params)
        */

        if (!is_array($css_uri)) {
            $css_uri = (array) $css_uri;
        }

        foreach ($css_uri as $legacy_uri) {
            if ($uri = $this->getAssetUriFromLegacyDeprecatedMethod($legacy_uri)) {
                $this->registerStylesheet(sha1($uri), $uri, ['media' => $css_media_type, 'priority' => 80]);
            }
        }
    }

    /**
     * @deprecated 1.7 This function has no effect in PrestaShop 1.7 theme, use $this->unregisterStylesheet() instead
     */
    public function removeCSS($css_uri, $css_media_type = 'all', $check_path = true)
    {
        /*
        This is deprecated in PrestaShop 1.7 and has no effect in PrestaShop 1.7 theme.
        You should use unregisterStylesheet($id)
        */

        if (!is_array($css_uri)) {
            $css_uri = (array) $css_uri;
        }

        foreach ($css_uri as $legacy_uri) {
            if ($uri = $this->getAssetUriFromLegacyDeprecatedMethod($legacy_uri)) {
                $this->unregisterStylesheet(sha1($uri));
            }
        }
    }

    /**
     * @deprecated 1.7 This function has no effect in PrestaShop 1.7 theme, use $this->registerJavascript() instead
     */
    public function addJS($js_uri, $check_path = true)
    {
        /*
        This is deprecated in PrestaShop 1.7 and has no effect in PrestaShop 1.7 theme.
        You should use registerJavascript($id, $path, $params)
        */

        if (!is_array($js_uri)) {
            $js_uri = (array) $js_uri;
        }

        foreach ($js_uri as $legacy_uri) {
            if ($uri = $this->getAssetUriFromLegacyDeprecatedMethod($legacy_uri)) {
                $this->registerJavascript(sha1($uri), $uri, ['position' => 'bottom', 'priority' => 80]);
            }
        }
    }

    /**
     * @deprecated 1.7 This function has no effect in PrestaShop 1.7 theme, use $this->unregisterJavascript() instead
     */
    public function removeJS($js_uri, $check_path = true)
    {
        /*
        This is deprecated in PrestaShop 1.7 and has no effect in PrestaShop 1.7 theme.
        You should use unregisterJavascript($id)
        */

        if (!is_array($js_uri)) {
            $js_uri = (array) $js_uri;
        }

        foreach ($js_uri as $legacy_uri) {
            if ($uri = $this->getAssetUriFromLegacyDeprecatedMethod($legacy_uri)) {
                $this->unregisterJavascript(sha1($uri));
            }
        }
    }

    /**
     * @deprecated 1.7  This function has no effect in PrestaShop 1.7 theme. jQuery2 is register by the core on every theme.
     *                  Have a look at the /themes/_core folder.
     */
    public function addJquery($version = null, $folder = null, $minifier = true)
    {
        /*
        This is deprecated in PrestaShop 1.7 and has no effect in PrestaShop 1.7 theme.
        jQuery2 is register by the core on every theme. Have a look at the /themes/_core folder.
        */
    }

    /**
     * Adds jQuery UI component(s) to queued JS file list
     *
     * @param string|array $component
     * @param string $theme
     * @param bool $check_dependencies
     */
    public function addJqueryUI($component, $theme = 'base', $check_dependencies = true)
    {
        $css_theme_path = '/js/jquery/ui/themes/'.$theme.'/minified/jquery.ui.theme.min.css';
        $css_path = '/js/jquery/ui/themes/'.$theme.'/minified/jquery-ui.min.css';
        $js_path = '/js/jquery/ui/jquery-ui.min.js';

        $this->registerStylesheet('jquery-ui-theme', $css_theme_path, ['media' => 'all', 'priority' => 95]);
        $this->registerStylesheet('jquery-ui', $css_path, ['media' => 'all', 'priority' => 90]);
        $this->registerJavascript('jquery-ui', $js_path, ['position' => 'bottom', 'priority' => 90]);
    }

    /**
     * Add Library not included with classic theme
     */
    public function requireAssets(array $libraries)
    {
        foreach ($libraries as $library) {
            if ($assets = PrestashopAssetsLibraries::getAssetsLibraries($library)) {
                foreach ($assets as $asset) {
                    $this->$asset['type']($library, $asset['path'], $asset['params']);
                }
            }
        }
    }


    /**
     * Adds jQuery plugin(s) to queued JS file list
     *
     * @param string|array $name
     * @param string null $folder
     * @param bool $css
     */
    public function addJqueryPlugin($name, $folder = null, $css = true)
    {
        if (!is_array($name)) {
            $name = array($name);
        }

        foreach ($name as $plugin) {
            $plugin_path = Media::getJqueryPluginPath($plugin, $folder);

            if (!empty($plugin_path['js'])) {
                $this->registerJavascript(
                    str_replace(_PS_JS_DIR_.'jquery/plugins/', '', $plugin_path['js']),
                    str_replace(_PS_JS_DIR_, 'js/', $plugin_path['js']),
                    array('position' => 'bottom', 'priority' => 100)
                );
            }
            if ($css && !empty($plugin_path['css'])) {
                $this->registerStylesheet(
                    str_replace(_PS_JS_DIR_.'jquery/plugins/', '', key($plugin_path['css'])),
                    str_replace(_PS_JS_DIR_, 'js/', key($plugin_path['css'])),
                    array('media' => 'all', 'priority' => 100)
                );
            }
        }
    }

    /**
     * Recovers cart information.
     *
     * @return int|false
     */
    protected function recoverCart()
    {
        if (($id_cart = (int) Tools::getValue('recover_cart')) && Tools::getValue('token_cart') == md5(_COOKIE_KEY_.'recover_cart_'.$id_cart)) {
            $cart = new Cart((int) $id_cart);
            if (Validate::isLoadedObject($cart)) {
                $customer = new Customer((int) $cart->id_customer);
                if (Validate::isLoadedObject($customer)) {
                    $customer->logged = 1;
                    $this->context->customer = $customer;
                    $this->context->cookie->id_customer = (int) $customer->id;
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
     * Sets template file for page content output.
     *
     * @param string $default_template
     */
    public function setTemplate($template, $params = array(), $locale = null)
    {
        parent::setTemplate(
            $this->getTemplateFile($template, $params, $locale)
        );
    }

    /**
     * Removed in PrestaShop 1.7.
     *
     * @return bool
     */
    protected function useMobileTheme()
    {
        return false;
    }

    /**
     * Returns theme directory (regular or mobile).
     *
     * @return string
     */
    protected function getThemeDir()
    {
        return _PS_THEME_DIR_;
    }

    /**
     * Returns the layout corresponding to the current page by using the override system
     * Ex:
     * On the url: http://localhost/index.php?id_product=1&controller=product, this method will
     * check if the layout exists in the following files (in that order), and return the first found:
     * - /themes/default/override/layout-product-1.tpl
     * - /themes/default/override/layout-product.tpl
     * - /themes/default/layout.tpl.
     *
     * @since 1.5.0.13
     *
     * @return bool|string
     */
    public function getLayout()
    {
        $entity = $this->php_self;
        if (empty($entity)) {
            $entity = $this->getPageName();
        }

        $layout = $this->context->shop->theme->getLayoutRelativePathForPage($entity);

        $content_only = (int) Tools::getValue('content_only');

        if ($overridden_layout = Hook::exec(
            'overrideLayoutTemplate',
            array(
                'default_layout' => $layout,
                'entity' => $entity,
                'locale' => $this->context->language->locale,
                'controller' => $this,
                'content_only' => $content_only,
            )
        )) {
            return $overridden_layout;
        }

        if ($content_only) {
            $layout = 'layouts/layout-content-only.tpl';
        }

        return $layout;
    }

    /**
     * Returns template path.
     *
     * @param string $template
     *
     * @return string
     */
    public function getTemplatePath($template)
    {
        return $template;
    }

    public function getTemplateFile($template, $params = array(), $locale = null)
    {
        if (!isset($params['entity'])) {
            $params['entity'] = null;
        }
        if (!isset($params['id'])) {
            $params['id'] = null;
        }

        if (is_null($locale)) {
            $locale = $this->context->language->locale;
        }

        if ($overridden_template = Hook::exec(
            'DisplayOverrideTemplate',
            array(
                'controller' => $this,
                'template_file' => $template,
                'id' => $params['id'],
                'locale' => $locale,
            )
        )) {
            return $overridden_template;
        }

        return $this->getTemplateFinder()->getTemplate(
            $template,
            $params['entity'],
            $params['id'],
            $locale
        );
    }

    /**
     * Renders and adds color list HTML for each product in a list.
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
                $products_need_cache[] = (int) $product['id_product'];
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
            $tpl->assign(array(
                'id_product' => $product['id_product'],
                'colors_list' => isset($colors[$product['id_product']]) ? $colors[$product['id_product']] : null,
                'link' => Context::getContext()->link,
                'img_col_dir' => _THEME_COL_DIR_,
                'col_img_dir' => _PS_COL_IMG_DIR_,
            ));
            $product['color_list'] = $tpl->fetch(_PS_THEME_DIR_.'product-list-colors.tpl', $this->getColorsListCacheId($product['id_product']));
        }
        Tools::restoreCacheSettings();
    }

    /**
     * Returns cache ID for product color list.
     *
     * @param int $id_product
     *
     * @return string
     */
    protected function getColorsListCacheId($id_product)
    {
        return Product::getColorsListCacheId($id_product);
    }

    public function getTemplateVarUrls()
    {
        $http = Tools::getCurrentUrlProtocolPrefix();
        $base_url = $this->context->shop->getBaseURL(true, true);

        $urls = array(
            'base_url' => $base_url,
            'current_url' => $this->context->shop->getBaseURL(true, false).$_SERVER['REQUEST_URI'],
            'shop_domain_url' => $this->context->shop->getBaseURL(true, false),
        );

        $assign_array = array(
            'img_ps_url' => _PS_IMG_,
            'img_cat_url' => _THEME_CAT_DIR_,
            'img_lang_url' => _THEME_LANG_DIR_,
            'img_prod_url' => _THEME_PROD_DIR_,
            'img_manu_url' => _THEME_MANU_DIR_,
            'img_sup_url' => _THEME_SUP_DIR_,
            'img_ship_url' => _THEME_SHIP_DIR_,
            'img_store_url' => _THEME_STORE_DIR_,
            'img_col_url' => _THEME_COL_DIR_,
            'img_url' => _THEME_IMG_DIR_,
            'css_url' => _THEME_CSS_DIR_,
            'js_url' => _THEME_JS_DIR_,
            'pic_url' => _THEME_PROD_PIC_DIR_,
        );

        foreach ($assign_array as $assign_key => $assign_value) {
            if (substr($assign_value, 0, 1) == '/' || $this->ssl) {
                $urls[$assign_key] = $http.Tools::getMediaServer($assign_value).$assign_value;
            } else {
                $urls[$assign_key] = $assign_value;
            }
        }

        $pages = array();
        $p = array(
            'address', 'addresses', 'authentication', 'cart', 'category', 'cms', 'contact',
            'discount', 'guest-tracking', 'history', 'identity', 'index', 'my-account',
            'order-confirmation', 'order-detail', 'order-follow', 'order', 'order-return',
            'order-slip', 'pagenotfound', 'password', 'pdf-invoice', 'pdf-order-return', 'pdf-order-slip',
            'prices-drop', 'product', 'search', 'sitemap', 'stores', 'supplier',
        );
        foreach ($p as $page_name) {
            $index = str_replace('-', '_', $page_name);
            $pages[$index] = $this->context->link->getPageLink($page_name, $this->ssl);
        }
        $pages['register'] = $this->context->link->getPageLink('authentication', true, null, array('create_account' => '1'));
        $pages['order_login'] = $this->context->link->getPageLink('order', true, null, array('login' => '1'));
        $urls['pages'] = $pages;

        $urls['theme_assets'] = __PS_BASE_URI__.'themes/'.$this->context->shop->theme->getName().'/assets/';

        $urls['actions'] = array(
            'logout' => $this->context->link->getPageLink('index', true, null, 'mylogout'),
        );

        return $urls;
    }

    public function getTemplateVarConfiguration()
    {
        $quantity_discount_price = Configuration::get('PS_DISPLAY_DISCOUNT_PRICE');

        return array(
            'display_taxes_label' => $this->getDisplayTaxesLabel(),
            'low_quantity_threshold' => (int) Configuration::get('PS_LAST_QTIES'),
            'is_b2b' => (bool) Configuration::get('PS_B2B_ENABLE'),
            'is_catalog' => (bool) Configuration::isCatalogMode(),
            'show_prices' => (bool) Configuration::showPrices(),
            'opt_in' => array(
                'partner' => (bool) Configuration::get('PS_CUSTOMER_OPTIN'),
            ),
            'quantity_discount' => array(
                'type' => ($quantity_discount_price) ? 'price' : 'discount',
                'label' => ($quantity_discount_price)
                    ? $this->getTranslator()->trans('Price', array(), 'Shop.Theme.Catalog')
                    : $this->getTranslator()->trans('Discount', array(), 'Shop.Theme.Catalog'),
            ),
            'voucher_enabled' => (int) CartRule::isFeatureActive(),
            'return_enabled' => (int) Configuration::get('PS_ORDER_RETURN'),
            'number_of_days_for_return' => (int) Configuration::get('PS_ORDER_RETURN_NB_DAYS'),

        );
    }

    protected function getDisplayTaxesLabel()
    {
        return (Module::isEnabled('ps_legalcompliance') && (bool) Configuration::get('AEUC_LABEL_TAX_INC_EXC')) || $this->context->country->display_tax_label;
    }

    public function getTemplateVarCurrency()
    {
        $curr = array();
        $fields = array('name', 'iso_code', 'iso_code_num', 'sign');
        foreach ($fields as $field_name) {
            $curr[$field_name] = $this->context->currency->{$field_name};
        }

        return $curr;
    }

    public function getTemplateVarCustomer($customer = null)
    {
        if (Validate::isLoadedObject($customer)) {
            $cust = $this->objectPresenter->present($customer);
        } else {
            $cust = $this->objectPresenter->present($this->context->customer);
        }

        unset($cust['secure_key']);
        unset($cust['passwd']);
        unset($cust['show_public_prices']);
        unset($cust['deleted']);
        unset($cust['id_lang']);

        $cust['is_logged'] = $this->context->customer->isLogged(true);

        $cust['gender'] = $this->objectPresenter->present(new Gender($cust['id_gender']));
        unset($cust['id_gender']);

        $cust['risk'] = $this->objectPresenter->present(new Risk($cust['id_risk']));
        unset($cust['id_risk']);

        $addresses = $this->context->customer->getSimpleAddresses();
        foreach ($addresses as &$a) {
            $a['formatted'] = AddressFormat::generateAddress(new Address($a['id']), array(), '<br>');
        }
        $cust['addresses'] = $addresses;

        return $cust;
    }

    public function getTemplateVarShop()
    {
        $address = $this->context->shop->getAddress();

        $shop = array(
            'name' => Configuration::get('PS_SHOP_NAME'),
            'email' => Configuration::get('PS_SHOP_EMAIL'),
            'registration_number' => Configuration::get('PS_SHOP_DETAILS'),

            'long' => Configuration::get('PS_STORES_CENTER_LONG'),
            'lat' => Configuration::get('PS_STORES_CENTER_LAT'),

            'logo' => (Configuration::get('PS_LOGO')) ? _PS_IMG_.Configuration::get('PS_LOGO') : '',
            'stores_icon' => (Configuration::get('PS_STORES_ICON')) ? _PS_IMG_.Configuration::get('PS_STORES_ICON') : '',
            'favicon' => (Configuration::get('PS_FAVICON')) ? _PS_IMG_.Configuration::get('PS_FAVICON') : '',
            'favicon_update_time' => Configuration::get('PS_IMG_UPDATE_TIME'),

            'address' => array(
                'formatted' => AddressFormat::generateAddress($address, array(), '<br>'),
                'address1' => $address->address1,
                'address2' => $address->address2,
                'postcode' => $address->postcode,
                'city' => $address->city,
                'state' => (new State($address->id_state))->name,
                'country' => (new Country($address->id_country))->name[$this->context->language->id],
            ),
            'phone' => Configuration::get('PS_SHOP_PHONE'),
            'fax' => Configuration::get('PS_SHOP_FAX'),
        );

        return $shop;
    }

    public function getTemplateVarPage()
    {
        $page_name = $this->getPageName();
        $meta_tags = Meta::getMetaTags($this->context->language->id, $page_name);

        $my_account_controllers = array(
            'address',
            'authentication',
            'discount',
            'history',
            'identity',
            'order-follow',
            'order-slip',
            'password',
            'guest-tracking',
        );

        $body_classes = array(
            'lang-'.$this->context->language->iso_code => true,
            'lang-rtl' => (bool) $this->context->language->is_rtl,
            'country-'.$this->context->country->iso_code => true,
            'currency-'.$this->context->currency->iso_code => true,
            $this->context->shop->theme->getLayoutNameForPage($this->php_self) => true,
            'page-'.$this->php_self => true,
            'tax-display-'.($this->getDisplayTaxesLabel() ? 'enabled' : 'disabled') => true,
        );

        if (in_array($this->php_self, $my_account_controllers)) {
            $body_classes['page-customer-account'] = true;
        }

        $page = array(
            'title' => '',
            'canonical' => $this->getCanonicalURL(),
            'meta' => array(
                'title' => $meta_tags['meta_title'],
                'description' => $meta_tags['meta_description'],
                'keywords' => $meta_tags['meta_keywords'],
                'robots' => 'index',
            ),
            'page_name' => $page_name,
            'body_classes' => $body_classes,
            'admin_notifications' => array(),
        );

        return $page;
    }

    public function getBreadcrumb()
    {
        $breadcrumb = $this->getBreadcrumbLinks();
        $breadcrumb['count'] = count($breadcrumb['links']);

        return $breadcrumb;
    }

    protected function getBreadcrumbLinks()
    {
        $breadcrumb = array();

        $breadcrumb['links'][] = array(
            'title' => $this->getTranslator()->trans('Home', array(), 'Shop.Theme.Global'),
            'url' => $this->context->link->getPageLink('index', true),
        );

        return $breadcrumb;
    }

    protected function getCategoryPath($category)
    {
        if ($category->id_parent != 0 && !$category->is_root_category) {
            return array(
                'title' => $category->name,
                'url' => $this->context->link->getCategoryLink($category),
            );
        }
    }

    protected function addMyAccountToBreadcrumb()
    {
        return array(
            'title' => $this->getTranslator()->trans('Your account', array(), 'Shop.Theme.Customeraccount'),
            'url' => $this->context->link->getPageLink('my-account', true),
        );
    }

    public function getCanonicalURL()
    {
        return;
    }

    /**
     * Generate a URL corresponding to the current page but
     * with the query string altered.
     *
     * If $extraParams is set to NULL, then all query params are stripped.
     *
     * Otherwise, params from $extraParams that have a null value are stripped,
     * and other params are added. Params not in $extraParams are unchanged.
     */
    protected function updateQueryString(array $extraParams = null)
    {
        $uriWithoutParams = explode('?', $_SERVER['REQUEST_URI'])[0];
        $url = Tools::getCurrentUrlProtocolPrefix().$_SERVER['HTTP_HOST'].$uriWithoutParams;
        $params = array();
        parse_str($_SERVER['QUERY_STRING'], $params);

        if (null !== $extraParams) {
            foreach ($extraParams as $key => $value) {
                if (null === $value) {
                    unset($params[$key]);
                } else {
                    $params[$key] = $value;
                }
            }
        }

        ksort($params);

        if (null !== $extraParams) {
            foreach ($params as $key => $param) {
                if (null === $param || '' === $param) {
                    unset($params[$key]);
                }
            }
        } else {
            $params = array();
        }

        $queryString = str_replace('%2F', '/', http_build_query($params, '', '&'));

        return $url.($queryString ? "?$queryString" : '');
    }

    protected function getCurrentURL()
    {
        return Tools::getCurrentUrlProtocolPrefix().$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
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

    protected function render($template, array $params = array())
    {
        $templateContent = '';
        $scope = $this->context->smarty->createData(
            $this->context->smarty
        );

        $scope->assign($params);

        try {
            $tpl = $this->context->smarty->createTemplate(
                $this->getTemplateFile($template),
                $scope
            );

            $templateContent = $tpl->fetch();
        } catch (PrestaShopException $e) {
            PrestaShopLogger::addLog($e->getMessage());

            if (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_) {
                $this->warning[] = $e->getMessage();
                $scope->assign(array('notifications' => $this->prepareNotifications()));

                $tpl = $this->context->smarty->createTemplate(
                    $this->getTemplateFile('_partials/notifications'),
                    $scope
                );

                $templateContent = $tpl->fetch();
            }
        }

        return $templateContent;
    }

    protected function getTranslator()
    {
        return $this->translator;
    }

    protected function makeLoginForm()
    {
        $form = new CustomerLoginForm(
            $this->context->smarty,
            $this->context,
            $this->getTranslator(),
            new CustomerLoginFormatter($this->getTranslator()),
            $this->getTemplateVarUrls()
        );

        $form->setAction($this->getCurrentURL());

        return $form;
    }

    protected function makeCustomerFormatter()
    {
        $formatter = new CustomerFormatter(
            $this->getTranslator(),
            $this->context->language
        );

        $customer = new Customer();

        $formatter
            ->setAskForPartnerOptin(Configuration::get('PS_CUSTOMER_OPTIN'))
            ->setAskForBirthdate(Configuration::get('PS_CUSTOMER_BIRTHDATE'))
            ->setPartnerOptinRequired($customer->isFieldRequired('optin'))
        ;

        return $formatter;
    }

    protected function makeCustomerForm()
    {
        $guestAllowedCheckout = Configuration::get('PS_GUEST_CHECKOUT_ENABLED');
        $form = new CustomerForm(
            $this->context->smarty,
            $this->context,
            $this->getTranslator(),
            $this->makeCustomerFormatter(),
            new CustomerPersister(
                $this->context,
                $this->get('hashing'),
                $this->getTranslator(),
                $guestAllowedCheckout
            ),
            $this->getTemplateVarUrls()
        );

        $form->setGuestAllowed($guestAllowedCheckout);

        $form->setAction($this->getCurrentURL());

        return $form;
    }

    protected function makeAddressPersister()
    {
        return new CustomerAddressPersister(
            $this->context->customer,
            $this->context->cart,
            Tools::getToken(true, $this->context)
        );
    }

    protected function makeAddressForm()
    {
        if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
            $availableCountries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
        } else {
            $availableCountries = Country::getCountries($this->context->language->id, true);
        }

        $form = new CustomerAddressForm(
            $this->context->smarty,
            $this->context->language,
            $this->getTranslator(),
            $this->makeAddressPersister(),
            new CustomerAddressFormatter(
                $this->context->country,
                $this->getTranslator(),
                $availableCountries
            )
        );

        $form->setAction($this->getCurrentURL());

        return $form;
    }

    private function initDebugguer()
    {
        if (true === _PS_MODE_DEV_) {
            Debug::enable();
        }
    }

    /**
     * Get templateFinder.
     *
     * @return object
     */
    public function getTemplateFinder()
    {
        return $this->templateFinder;
    }

    public function getRestrictedCountry()
    {
        return $this->restrictedCountry;
    }

    public function getAssetUriFromLegacyDeprecatedMethod($legacy_uri)
    {
        $success = preg_match('/modules\/.*/', $legacy_uri, $matches);
        if (!$success) {
            Tools::displayAsDeprecated(
                'Backward compatibility for this method couldn\'t be handled. Use $this->registerJavascript() instead'
            );
            return false;
        } else {
            return $matches[0];
        }
    }

    protected function buildContainer()
    {
        $container = new ContainerBuilder();
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__));
        $env = _PS_MODE_DEV_ === true ? 'dev' : 'prod';
        $loader->load(_PS_CONFIG_DIR_.'services/front/services_'. $env .'.yml');
        $container->compile();

        return $container;
    }

    /**
     * Returns an object that filters the Product list to be sent to the browser
     *
     * @return CollectionFilter
     */
    protected function getProductListOutputFilter()
    {
        return $this->get('prestashop.core.filter.front_end_object.product_collection');
    }

    /**
     * Returns an object that filters the Customer object to be sent to the browser
     *
     * @return CustomerFilter
     */
    protected function getCustomerOutputFilter()
    {
        return $this->get('prestashop.core.filter.front_end_object.customer');
    }

    /**
     * Returns an object that filters the Shop object to be sent to the browser
     *
     * @return ShopFilter
     */
    protected function getShopOutputFilter()
    {
        return $this->get('prestashop.core.filter.front_end_object.shop');
    }

    /**
     * Returns an object that filters the Configuration object to be sent to the browser
     *
     * @return ConfigurationFilter
     */
    protected function getConfigurationOutputFilter()
    {
        return $this->get('prestashop.core.filter.front_end_object.configuration');
    }
}
