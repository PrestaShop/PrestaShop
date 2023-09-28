<?php

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use PrestaShop\PrestaShop\Adapter\Configuration as ConfigurationAdapter;
use PrestaShop\PrestaShop\Adapter\ContainerBuilder;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartPresenter;
use PrestaShop\PrestaShop\Adapter\Presenter\Object\ObjectPresenter;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\Security\PasswordPolicyConfiguration;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\IpUtils;

class FrontControllerCore extends Controller
{
    /** @var array Controller warning notifications */
    public $warning = [];

    /** @var array Controller success notifications */
    public $success = [];

    /** @var array Controller info notifications */
    public $info = [];

    /** @var string Language ISO code */
    public $iso;

    /** @var bool If set to true, will redirected user to login page during init function. */
    public $auth = false;

    /**
     * Route of PrestaShop page to redirect to after forced login.
     *
     * @see $auth
     *
     * @var bool|string
     */
    public $authRedirection = false;

    /** @var bool SSL connection flag */
    public $ssl = false;

    /** @var int If Country::GEOLOC_CATALOG_MODE, switches display to restricted country page during init. */
    protected $restrictedCountry = Country::GEOLOC_ALLOWED;

    /** @var bool If true, forces display to maintenance page. */
    protected $maintenance = false;

    /** @var string[] Adds excluded `$_GET` keys for redirection */
    protected $redirectionExtraExcludedKeys = [];

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
     * @var ObjectPresenter
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
     * @var array Contains the result of getTemplateVarUrls method
     */
    protected $urls;

    /**
     * Set this parameter to false if you don't want cart's invoice address
     * to be set automatically (this behavior is kept for legacy and BC purpose)
     *
     * @var bool automaticallyAllocateInvoiceAddress
     */
    protected $automaticallyAllocateInvoiceAddress = true;

    /**
     * Set this parameter to false if you don't want cart's delivery address
     * to be set automatically (this behavior is kept for legacy and BC purpose)
     *
     * @var bool automaticallyAllocateDeliveryAddress
     */
    protected $automaticallyAllocateDeliveryAddress = true;

    /** @var string Page name */
    public $page_name;

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

        // Prepare presenters that we will require on every page
        $this->objectPresenter = new ObjectPresenter();
        $this->cart_presenter = new CartPresenter();

        $this->templateFinder = new TemplateFinder($this->context->smarty->getTemplateDir(), '.tpl');
        $this->stylesheetManager = new StylesheetManager(
            [_PS_THEME_URI_, _PS_PARENT_THEME_URI_, __PS_BASE_URI__],
            new ConfigurationAdapter()
        );
        $this->javascriptManager = new JavascriptManager(
            [_PS_THEME_URI_, _PS_PARENT_THEME_URI_, __PS_BASE_URI__],
            new ConfigurationAdapter()
        );
        $this->cccReducer = new CccReducer(
            _PS_THEME_DIR_ . 'assets/cache/',
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
     * Currently not used in any native controller.
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
     *
     * @throws PrestaShopException
     */
    public function init()
    {
        Hook::exec(
            'actionFrontControllerInitBefore',
            [
                'controller' => $this,
            ]
        );

        /*
         * Globals are DEPRECATED as of version 1.5.0.1
         * Use the Context object to access objects instead.
         * Example: $this->context->cart
         */
        global $useSSL;

        if (self::$initialized) {
            return;
        }

        self::$initialized = true;

        parent::init();

        // If current URL use SSL, set it true (used a lot for module redirect)
        if (Tools::usingSecureMode()) {
            $useSSL = true;
        }

        // Redirect to SSL variant of the page if required and visited in non-ssl mode
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

        // Initialize URL provider in context, depending on SSL mode
        $protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
        $useSSL = ($this->ssl && Configuration::get('PS_SSL_ENABLED')) || Tools::usingSecureMode();
        $protocol_content = ($useSSL) ? 'https://' : 'http://';
        $link = new Link($protocol_link, $protocol_content);
        $this->context->link = $link;

        // Attempt to recover cart, if the user is using recovery link
        // This is used by abandoned cart modules or when sending a prepared order to customer from backoffice
        $this->recoverCart();

        // Redirect user to login page, if the controller requires authentication
        if ($this->auth && !$this->context->customer->isLogged()) {
            Tools::redirect('index.php?controller=authentication' . ($this->authRedirection ? '&back=' . $this->authRedirection : ''));
        }

        // If the theme is missing, we need to throw an Exception
        if (!is_dir(_PS_THEME_DIR_)) {
            throw new PrestaShopException($this->trans('Current theme is unavailable. Please check your theme\'s directory name ("%s") and permissions.', [htmlspecialchars(basename(rtrim(_PS_THEME_DIR_, '/\\')))], 'Admin.Design.Notification'));
        }

        if (Configuration::get('PS_GEOLOCATION_ENABLED')) {
            if (($new_default = $this->geolocationManagement($this->context->country)) && Validate::isLoadedObject($new_default)) {
                $this->context->country = $new_default;
            }
        } elseif (Configuration::get('PS_DETECT_COUNTRY')) {
            $has_currency = isset($this->context->cookie->id_currency) && (int) $this->context->cookie->id_currency;
            $has_country = isset($this->context->cookie->iso_code_country) && $this->context->cookie->iso_code_country;
            $has_address_type = false;

            if ((int) $this->context->cookie->id_cart) {
                $cart = new Cart((int) $this->context->cookie->id_cart);
                if (Validate::isLoadedObject($cart)) {
                    $has_address_type = isset($cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) && $cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
                }
            }

            if ((!$has_currency || $has_country) && !$has_address_type) {
                if ($has_country && Validate::isLanguageIsoCode($this->context->cookie->iso_code_country)) {
                    $id_country = (int) Country::getByIso(strtoupper($this->context->cookie->iso_code_country));
                } elseif (Tools::isCountryFromBrowserAvailable()) {
                    $id_country = (int) Country::getByIso(Tools::getCountryIsoCodeFromHeader(), true);
                } else {
                    $id_country = Tools::getCountry();
                }

                $country = new Country($id_country, (int) $this->context->cookie->id_lang);

                if (!$has_currency && Validate::isLoadedObject($country) && $this->context->country->id !== $country->id) {
                    $this->context->country = $country;
                    $this->context->cookie->id_currency = (int) Currency::getCurrencyInstance($country->id_currency ? (int) $country->id_currency : Currency::getDefaultCurrencyId())->id;
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
                $cart = new Cart((int) $this->context->cookie->id_cart);
            }

            if (!Validate::isLoadedObject($cart) || $cart->orderExists()) {
                PrestaShopLogger::addLog('Frontcontroller::init - Cart cannot be loaded or an order has already been placed using this cart', 1, null, 'Cart', (int) $this->context->cookie->id_cart, true);
                unset($this->context->cookie->id_cart, $cart, $this->context->cookie->checkedTOS);
                $this->context->cookie->check_cgv = false;
            } elseif (
                (int) (Configuration::get('PS_GEOLOCATION_ENABLED'))
                && !in_array(strtoupper($this->context->cookie->iso_code_country), explode(';', Configuration::get('PS_ALLOWED_COUNTRIES')))
                && $cart->nbProducts()
                && (int) (Configuration::get('PS_GEOLOCATION_NA_BEHAVIOR')) != -1
                && !FrontController::isInWhitelistForGeolocation()
                && !in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1', '::1'])
            ) {
                /* Delete product of cart, if user can't make an order from his country */
                PrestaShopLogger::addLog('Frontcontroller::init - GEOLOCATION is deleting a cart', 1, null, 'Cart', (int) $this->context->cookie->id_cart, true);
                unset($this->context->cookie->id_cart, $cart);
            } elseif (
                $this->context->cookie->id_customer != $cart->id_customer
                || $this->context->cookie->id_lang != $cart->id_lang
                || $currency->id != $cart->id_currency
            ) {
                // update cart values
                if ($this->context->cookie->id_customer) {
                    $cart->id_customer = (int) $this->context->cookie->id_customer;
                }
                $cart->id_lang = (int) $this->context->cookie->id_lang;
                $cart->id_currency = (int) $currency->id;
                $cart->update();
            }
            /* Select an address if not set */
            if (
                isset($cart)
                && (!isset($cart->id_address_delivery) || $cart->id_address_delivery == 0 || !isset($cart->id_address_invoice) || $cart->id_address_invoice == 0)
                && $this->context->cookie->id_customer
            ) {
                $to_update = false;
                if ($this->automaticallyAllocateDeliveryAddress && (!isset($cart->id_address_delivery) || $cart->id_address_delivery == 0)) {
                    $to_update = true;
                    $cart->id_address_delivery = (int) Address::getFirstCustomerAddressId($cart->id_customer);
                }
                if ($this->automaticallyAllocateInvoiceAddress && (!isset($cart->id_address_invoice) || $cart->id_address_invoice == 0)) {
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
            $this->context->cart->checkAndUpdateAddresses();
        }

        $this->context->smarty->assign('request_uri', Tools::safeOutput(urldecode($_SERVER['REQUEST_URI'])));

        // Automatically redirect to the canonical URL if needed
        if (!empty($this->php_self) && !Tools::getValue('ajax')) {
            $this->canonicalRedirection($this->context->link->getPageLink($this->php_self, $this->ssl, $this->context->language->id));
        }

        Product::initPricesComputation();

        if (isset($cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) && $cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) {
            $infos = Address::getCountryAndState((int) $cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
            $country = new Country((int) $infos['id_country']);
            $this->context->country = $country;
        }

        $this->displayMaintenancePage();

        if (Country::GEOLOC_FORBIDDEN == $this->restrictedCountry) {
            $this->displayRestrictedCountryPage();
        }

        $this->context->cart = $cart;
        $this->context->currency = $currency;

        Hook::exec(
            'actionFrontControllerInitAfter',
            [
                'controller' => $this,
            ]
        );
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
     * Initializes a set of commonly used variables, available for use in the template.
     */
    protected function assignGeneralPurposeVariables()
    {
        if (Validate::isLoadedObject($this->context->cart)) {
            $cart = $this->context->cart;
        } else {
            $cart = new Cart();
        }

        $templateVars = [
            'cart' => $this->cart_presenter->present($cart),
            'currency' => $this->getTemplateVarCurrency(),
            'customer' => $this->getTemplateVarCustomer(),
            'country' => $this->objectPresenter->present($this->context->country),
            'language' => $this->objectPresenter->present($this->context->language),
            'page' => $this->getTemplateVarPage(),
            'shop' => $this->getTemplateVarShop(),
            'core_js_public_path' => $this->getCoreJsPublicPath(),
            'urls' => $this->getTemplateVarUrls(),
            'configuration' => $this->getTemplateVarConfiguration(),
            'field_required' => $this->context->customer->validateFieldsRequiredDatabase(),
            'breadcrumb' => $this->getBreadcrumb(),
            'link' => $this->context->link,
            'time' => time(),
            'static_token' => Tools::getToken(false),
            'token' => Tools::getToken(),
            'debug' => _PS_MODE_DEV_,
        ];

        // An array [module_name => module_output] will be returned
        $modulesVariables = Hook::exec(
            'actionFrontControllerSetVariables',
            [
                'templateVars' => &$templateVars,
            ],
            null,
            true
        );

        if (is_array($modulesVariables)) {
            foreach ($modulesVariables as $moduleName => $variables) {
                $templateVars['modules'][$moduleName] = $variables;
            }
        }

        $this->context->smarty->assign($templateVars);

        Media::addJsDef([
            'prestashop' => $this->buildFrontEndObject($templateVars),
        ]);
    }

    /**
     * Builds the "prestashop" javascript object that will be sent to the front end.
     *
     * @param array $object Variables inserted in the template (see FrontController::assignGeneralPurposeVariables)
     *
     * @return array Variables to be inserted in the "prestashop" javascript object
     *
     * @throws \PrestaShop\PrestaShop\Core\Filter\FilterException
     * @throws PrestaShopException
     */
    protected function buildFrontEndObject($object)
    {
        $object = $this->get('prestashop.core.filter.front_end_object.main')
            ->filter($object);

        Hook::exec('actionBuildFrontEndObject', [
            'obj' => &$object,
        ]);

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

        $this->context->smarty->assign([
            'HOOK_HEADER' => Hook::exec('displayHeader'),
        ]);
    }

    public function initFooter()
    {
    }

    /**
     * Initialize the invalid doom page of death.
     */
    public function initCursedPage()
    {
        header('HTTP/1.1 403 Forbidden');

        $this->registerStylesheet('theme-error', '/assets/css/error.css', ['media' => 'all', 'priority' => 50]);
        $this->context->smarty->assign([
            'layout' => 'layouts/layout-error.tpl',
            'urls' => $this->getTemplateVarUrls(),
            'shop' => $this->getTemplateVarShop(),
            'stylesheets' => $this->getStylesheets(),
        ]);
        $this->layout = 'errors/forbidden.tpl';
    }

    /**
     * Called before compiling common page sections (header, footer, columns).
     * Good place to modify smarty variables. Currently not used by any native controller.
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
        Tools::redirect($this->redirect_after);
    }

    public function redirectWithNotifications()
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
        $this->context->smarty->assign([
            'layout' => $this->getLayout(),
            'stylesheets' => $this->getStylesheets(),
            'javascript' => $this->getJavascript(),
            'js_custom_vars' => Media::getJsDef(),
            'notifications' => $this->prepareNotifications(),
        ]);

        $this->smartyOutputContent($this->template);

        return true;
    }

    protected function smartyOutputContent($content)
    {
        $this->context->cookie->write();

        $html = '';

        $theme = $this->context->shop->theme->getName();

        if (is_array($content)) {
            foreach ($content as $tpl) {
                $html .= $this->context->smarty->fetch($tpl, null, $theme . $this->getLayout());
            }
        } else {
            $html = $this->context->smarty->fetch($content, null, $theme . $this->getLayout());
        }

        Hook::exec('actionOutputHTMLBefore', ['html' => &$html]);
        echo trim($html);
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
            $notifications = array_merge_recursive($notifications, json_decode($_SESSION['notifications'], true));
            unset($_SESSION['notifications']);
        } elseif (isset($_COOKIE['notifications'])) {
            $notifications = array_merge_recursive($notifications, json_decode($_COOKIE['notifications'], true));
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

            $is_admin = (int) (new Cookie('psAdmin'))->id_employee;
            $maintenance_allow_admins = (bool) Configuration::get('PS_MAINTENANCE_ALLOW_ADMINS');
            if ($is_admin && $maintenance_allow_admins) {
                return;
            }

            $allowed_ips = array_map('trim', explode(',', Configuration::get('PS_MAINTENANCE_IP')));
            if (!IpUtils::checkIp(Tools::getRemoteAddr(), $allowed_ips)) {
                header('HTTP/1.1 503 Service Unavailable');
                header('Retry-After: 3600');

                $this->registerStylesheet('theme-error', '/assets/css/error.css', ['media' => 'all', 'priority' => 50]);
                $this->context->smarty->assign([
                    'urls' => $this->getTemplateVarUrls(),
                    'shop' => $this->getTemplateVarShop(),
                    'HOOK_MAINTENANCE' => Hook::exec('displayMaintenance', []),
                    'maintenance_text' => Configuration::get('PS_MAINTENANCE_TEXT', (int) $this->context->language->id),
                    'stylesheets' => $this->getStylesheets(),
                ]);
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
        $this->context->smarty->assign([
            'urls' => $this->getTemplateVarUrls(),
            'shop' => $this->getTemplateVarShop(),
            'stylesheets' => $this->getStylesheets(),
        ]);
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
                header('Location: ' . Tools::getShopDomainSsl(true) . $_SERVER['REQUEST_URI']);
            } else {
                header('Location: ' . Tools::getShopDomain(true) . $_SERVER['REQUEST_URI']);
            }
            exit();
        }
    }

    /**
     * Redirects to canonical URL.
     *
     * @param string $canonical_url
     */
    protected function canonicalRedirection(string $canonical_url = '')
    {
        if (!$canonical_url || !Configuration::get('PS_CANONICAL_REDIRECT') || strtoupper($_SERVER['REQUEST_METHOD']) != 'GET') {
            return;
        }

        $canonical_url = preg_replace('/#.*$/', '', $canonical_url);

        $match_url = rawurldecode(Tools::getCurrentUrlProtocolPrefix() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        if (!preg_match('/^' . Tools::pRegexp(rawurldecode($canonical_url), '/') . '([&?].*)?$/', $match_url)) {
            $final_url = $this->sanitizeUrl($canonical_url);

            // Don't send any cookie
            Context::getContext()->cookie->disallowWriting();
            if (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_ && $_SERVER['REQUEST_URI'] != __PS_BASE_URI__) {
                die('[Debug] This page has moved<br />Please use the following URL instead: <a href="' . $final_url . '">' . $final_url . '</a>');
            }

            $redirect_type = Configuration::get('PS_CANONICAL_REDIRECT') == 2 ? '301' : '302';
            header('HTTP/1.0 ' . $redirect_type . ' Moved');
            header('Cache-Control: no-cache');
            Tools::redirect($final_url);
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
        if (!in_array(Tools::getRemoteAddr(), ['127.0.0.1', '::1'])) {
            /* Check if Maxmind Database exists */
            if (@filemtime(_PS_GEOIP_DIR_ . _PS_GEOIP_CITY_FILE_)) {
                if (!isset($this->context->cookie->iso_code_country) || (isset($this->context->cookie->iso_code_country) && !in_array(strtoupper($this->context->cookie->iso_code_country), explode(';', Configuration::get('PS_ALLOWED_COUNTRIES'))))) {
                    $reader = new GeoIp2\Database\Reader(_PS_GEOIP_DIR_ . _PS_GEOIP_CITY_FILE_);

                    try {
                        $record = $reader->city(Tools::getRemoteAddr());
                    } catch (\GeoIp2\Exception\AddressNotFoundException $e) {
                        $record = null;
                    }

                    if (is_object($record) && Validate::isLanguageIsoCode($record->country->isoCode) && (int) Country::getByIso(strtoupper($record->country->isoCode)) != 0) {
                        if (!in_array(strtoupper($record->country->isoCode), explode(';', Configuration::get('PS_ALLOWED_COUNTRIES'))) && !FrontController::isInWhitelistForGeolocation()) {
                            if (Configuration::get('PS_GEOLOCATION_BEHAVIOR') == _PS_GEOLOCATION_NO_CATALOG_) {
                                $this->restrictedCountry = Country::GEOLOC_FORBIDDEN;
                            } elseif (Configuration::get('PS_GEOLOCATION_BEHAVIOR') == _PS_GEOLOCATION_NO_ORDER_) {
                                $this->restrictedCountry = Country::GEOLOC_CATALOG_MODE;
                                $this->warning[] = $this->trans('You cannot place a new order from your country (%s).', [htmlspecialchars($record->country->name)], 'Shop.Notifications.Warning');
                            }
                        } else {
                            $hasBeenSet = !isset($this->context->cookie->iso_code_country);
                            $this->context->cookie->iso_code_country = strtoupper($record->country->isoCode);
                        }
                    }
                }

                if (isset($this->context->cookie->iso_code_country) && $this->context->cookie->iso_code_country && !Validate::isLanguageIsoCode($this->context->cookie->iso_code_country)) {
                    $this->context->cookie->iso_code_country = Country::getIsoById((int) Configuration::get('PS_COUNTRY_DEFAULT'));
                }

                if (isset($this->context->cookie->iso_code_country) && ($idCountry = (int) Country::getByIso(strtoupper($this->context->cookie->iso_code_country)))) {
                    /* Update defaultCountry */
                    if ($defaultCountry->iso_code != $this->context->cookie->iso_code_country) {
                        $defaultCountry = new Country($idCountry);
                    }
                    if (isset($hasBeenSet) && $hasBeenSet) {
                        $this->context->cookie->id_currency = (int) ($defaultCountry->id_currency ? (int) $defaultCountry->id_currency : Currency::getDefaultCurrencyId());
                    }

                    return $defaultCountry;
                } elseif (Configuration::get('PS_GEOLOCATION_NA_BEHAVIOR') == _PS_GEOLOCATION_NO_CATALOG_ && !FrontController::isInWhitelistForGeolocation()) {
                    $this->restrictedCountry = Country::GEOLOC_FORBIDDEN;
                } elseif (Configuration::get('PS_GEOLOCATION_NA_BEHAVIOR') == _PS_GEOLOCATION_NO_ORDER_ && !FrontController::isInWhitelistForGeolocation()) {
                    $this->restrictedCountry = Country::GEOLOC_CATALOG_MODE;
                    $countryName = $this->trans('Undefined', [], 'Shop.Theme.Global');
                    if (isset($record->country->name) && $record->country->name) {
                        $countryName = $record->country->name;
                    }
                    $this->warning[] = $this->trans(
                        'You cannot place a new order from your country (%s).',
                        [htmlspecialchars($countryName)],
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

        if ($this->context->shop->theme->requiresCoreScripts()) {
            $this->registerJavascript('corejs', '/themes/core.js', ['position' => 'bottom', 'priority' => 0]);
        }
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
        Hook::exec('actionFrontControllerSetMedia', []);

        return true;
    }

    /**
     * Initializes page header variables.
     */
    public function initHeader()
    {
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
            return [];
        }

        $context = Context::getContext();
        if (!isset($context->customer) || !$context->customer->id) {
            return [];
        }

        if (!is_array(self::$currentCustomerGroups)) {
            self::$currentCustomerGroups = [];
            $result = Db::getInstance()->executeS('SELECT id_group FROM ' . _DB_PREFIX_ . 'customer_group WHERE id_customer = ' . (int) $context->customer->id);
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
        $ips = [];

        // retrocompatibility
        $ips_old = explode(';', Configuration::get('PS_GEOLOCATION_WHITELIST'));
        foreach ($ips_old as $ip) {
            $ips = array_merge($ips, explode("\n", $ip));
        }

        $ips = array_map('trim', $ips);
        foreach ($ips as $ip) {
            if (!empty($ip) && preg_match('/^' . $ip . '.*/', $user_ip)) {
                $allowed = true;
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

    public function registerStylesheet($id, $relativePath, $params = [])
    {
        if (!is_array($params)) {
            $params = [];
        }

        $default_params = [
            'media' => AbstractAssetManager::DEFAULT_MEDIA,
            'priority' => AbstractAssetManager::DEFAULT_PRIORITY,
            'inline' => false,
            'server' => 'local',
            'version' => null,
            'needRtl' => true,
        ];
        $params = array_merge($default_params, $params);

        if (Tools::hasMediaServer() && !Configuration::get('PS_CSS_THEME_CACHE')) {
            $relativePath = Tools::getCurrentUrlProtocolPrefix() . Tools::getMediaServer($relativePath)
                . ($this->stylesheetManager->getFullPath($relativePath) ?? $relativePath);
            $params['server'] = 'remote';
        }

        $this->stylesheetManager->register($id, $relativePath, $params['media'], $params['priority'], $params['inline'], $params['server'], $params['needRtl'], $params['version']);
    }

    public function unregisterStylesheet($id)
    {
        $this->stylesheetManager->unregisterById($id);
    }

    public function registerJavascript($id, $relativePath, $params = [])
    {
        if (!is_array($params)) {
            $params = [];
        }

        $default_params = [
            'position' => AbstractAssetManager::DEFAULT_JS_POSITION,
            'priority' => AbstractAssetManager::DEFAULT_PRIORITY,
            'inline' => false,
            'attributes' => null,
            'server' => 'local',
            'version' => null,
        ];
        $params = array_merge($default_params, $params);

        if (Tools::hasMediaServer() && !Configuration::get('PS_JS_THEME_CACHE')) {
            $relativePath = Tools::getCurrentUrlProtocolPrefix() . Tools::getMediaServer($relativePath)
                . ($this->javascriptManager->getFullPath($relativePath) ?? $relativePath);
            $params['server'] = 'remote';
        }
        $this->javascriptManager->register($id, $relativePath, $params['position'], $params['priority'], $params['inline'], $params['attributes'], $params['server'], $params['version']);
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
     * Adds jQuery UI component(s) to queued JS file list.
     *
     * @param string|array $component
     * @param string $theme
     * @param bool $check_dependencies
     */
    public function addJqueryUI($component, $theme = 'base', $check_dependencies = true)
    {
        // If the component does not start with ui. prefix, we need to add it
        if (substr($component, 0, 3) !== 'ui.') {
            $component = 'ui.' . $component;
        }

        if (!is_array($component)) {
            $component = [$component];
        }

        foreach ($component as $ui) {
            $ui_path = Media::getJqueryUIPath($ui, $theme, $check_dependencies);
            foreach ($ui_path['css'] as $uiPathCss => $uiMediaCss) {
                $this->registerStylesheet(
                    str_replace(_PS_JS_DIR_ . 'jquery/ui/themes/' . $theme . '/', '', $uiPathCss),
                    str_replace(_PS_JS_DIR_, '/js/', $uiPathCss),
                    ['media' => $uiMediaCss, 'priority' => 95]
                );
            }
            foreach ($ui_path['js'] as $uiPathJs) {
                $this->registerJavascript(
                    str_replace(_PS_JS_DIR_ . 'jquery/ui/', '', $uiPathJs),
                    str_replace(_PS_JS_DIR_, '/js/', $uiPathJs),
                    ['position' => 'bottom', 'priority' => 49]
                );
            }
        }
    }

    /**
     * Add Library not included with classic theme.
     */
    public function requireAssets(array $libraries)
    {
        foreach ($libraries as $library) {
            if ($assets = PrestashopAssetsLibraries::getAssetsLibraries($library)) {
                foreach ($assets as $asset) {
                    $this->{$asset['type']}($library, $asset['path'], $asset['params']);
                }
            }
        }
    }

    /**
     * Adds jQuery plugin(s) to queued JS file list.
     *
     * @param string|array $name
     * @param string|null $folder
     * @param bool $css
     */
    public function addJqueryPlugin($name, $folder = null, $css = true)
    {
        if (!is_array($name)) {
            $name = [$name];
        }

        foreach ($name as $plugin) {
            $plugin_path = Media::getJqueryPluginPath($plugin, $folder);

            if (!empty($plugin_path['js'])) {
                $this->registerJavascript(
                    str_replace(_PS_JS_DIR_ . 'jquery/plugins/', '', $plugin_path['js']),
                    str_replace(_PS_JS_DIR_, 'js/', $plugin_path['js']),
                    ['position' => 'bottom', 'priority' => 100]
                );
            }
            if ($css && !empty($plugin_path['css'])) {
                $this->registerStylesheet(
                    str_replace(_PS_JS_DIR_ . 'jquery/plugins/', '', key($plugin_path['css'])),
                    str_replace(_PS_JS_DIR_, 'js/', key($plugin_path['css'])),
                    ['media' => 'all', 'priority' => 100]
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
        if (!Tools::isSubmit('recover_cart')) {
            return false;
        }

        // Get ID cart from URL
        $id_cart = (int) Tools::getValue('recover_cart');

        // Check if token in URL matches, otherwise, ignore it, probably malicious intentions
        if (Tools::getValue('token_cart') != md5(_COOKIE_KEY_ . 'recover_cart_' . $id_cart)) {
            return false;
        }

        // Create cart object and check if it's still valid. It can be deleted by automated cleaners or manually.
        $cart = new Cart($id_cart);
        if (!Validate::isLoadedObject($cart)) {
            $this->errors[] = $this->trans('This cart has expired.', [], 'Shop.Notifications.Error');

            return false;
        }

        // Customer - same scenario. It can be deleted by automated cleaners or manually.
        $customer = new Customer((int) $cart->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            $this->errors[] = $this->trans('This cart has expired.', [], 'Shop.Notifications.Error');

            return false;
        }

        // Check if there is already a finished order with this cart, we notify the customer nicely
        if ($cart->orderExists()) {
            $this->errors[] = $this->trans('This cart was already used in an order and has expired.', [], 'Shop.Notifications.Error');

            return false;
        }

        // Initialize this data into cookie, FrontController will use it later
        $customer->logged = true;
        $this->context->customer = $customer;
        $this->context->cookie->id_customer = (int) $customer->id;
        $this->context->cookie->customer_lastname = $customer->lastname;
        $this->context->cookie->customer_firstname = $customer->firstname;
        $this->context->cookie->logged = true;
        $this->context->cookie->check_cgv = 1;
        $this->context->cookie->is_guest = $customer->isGuest();
        $this->context->cookie->passwd = $customer->passwd;
        $this->context->cookie->email = $customer->email;
        $this->context->cookie->id_guest = (int) $cart->id_guest;
        $this->context->cookie->id_cart = $id_cart;

        // Return the value for backward compatibility
        return $id_cart;
    }

    /**
     * Sets template file for page content output.
     *
     * @param string $template
     */
    public function setTemplate($template, $params = [], $locale = null)
    {
        parent::setTemplate(
            $this->getTemplateFile($template, $params, $locale)
        );
    }

    /**
     * Returns theme directory
     *
     * @return string
     */
    protected function getThemeDir()
    {
        return _PS_THEME_DIR_;
    }

    /**
     * Returns the layout's full path corresponding to the current page by using the override system
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
        // Primary identifier to search for a template is php_self property,
        // For modules, we will use page_name
        $entity = $this->php_self;
        if (empty($entity)) {
            $entity = $this->getPageName();
        }

        // Get layout set in prestashop configuration
        $layout = $this->context->shop->theme->getLayoutNameForPage($entity);

        // Check if we are in content_only mode (used for displaying terms and conditions in a popup for example)
        $content_only = (int) Tools::getValue('content_only');

        // If a module provides its own custom layout, we ignore what is set in configuration
        if ($overridden_layout = Hook::exec(
            'overrideLayoutTemplate',
            [
                'default_layout' => $layout,
                'entity' => $entity,
                'locale' => $this->context->language->locale,
                'controller' => $this,
                'content_only' => $content_only,
            ]
        )) {
            return $overridden_layout;
        }

        // When using content_only, there will be no header, footer and sidebars
        if ($content_only) {
            $layout = 'layout-content-only';
        }

        return $this->context->shop->theme->getLayoutPath($layout);
    }

    /**
     * Returns layout name for the current controller. Used to display layout name in <body> tag.
     *
     * @return string layout name
     */
    protected function getLayoutName()
    {
        return str_replace(['.tpl'], '', basename($this->getLayout()));
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

    public function getTemplateFile($template, $params = [], $locale = null)
    {
        if (!isset($params['entity'])) {
            $params['entity'] = null;
        }
        if (!isset($params['id'])) {
            $params['id'] = null;
        }

        if (null === $locale) {
            $locale = $this->context->language->locale;
        }

        if ($overridden_template = Hook::exec(
            'displayOverrideTemplate',
            [
                'controller' => $this,
                'template_file' => $template,
                'entity' => $params['entity'],
                'id' => $params['id'],
                'locale' => $locale,
            ]
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
     * Initializes a set of commonly used urls of pages, image folders and others, available for use
     * in the template. @see FrontController::assignGeneralPurposeVariables for more information.
     *
     * @return array
     */
    public function getTemplateVarUrls()
    {
        if ($this->urls === null) {
            $http = Tools::getCurrentUrlProtocolPrefix();
            $base_url = $this->context->shop->getBaseURL(true, true);

            $urls = [
                'base_url' => $base_url,
                'current_url' => $this->context->shop->getBaseURL(true, false) . $_SERVER['REQUEST_URI'],
                'shop_domain_url' => $this->context->shop->getBaseURL(true, false),
            ];

            $assign_array = [
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
                'theme_assets' => _THEME_DIR_ . 'assets/',
                'theme_dir' => $this->getThemeDir(),
            ];

            $themeAssetsConfig = $this->context->shop->theme->get('assets', false);

            if (!empty($themeAssetsConfig['use_parent_assets'])) {
                $assign_array['theme_assets'] = _PS_PARENT_THEME_URI_ . 'assets/';
                $assign_array['img_url'] = $assign_array['theme_assets'] . 'img/';
                $assign_array['css_url'] = $assign_array['theme_assets'] . 'css/';
                $assign_array['js_url'] = $assign_array['theme_assets'] . 'js/';
                $assign_array['child_theme_assets'] = _THEME_DIR_ . 'assets/';
                $assign_array['child_img_url'] = $assign_array['child_theme_assets'] . 'img/';
                $assign_array['child_css_url'] = $assign_array['child_theme_assets'] . 'css/';
                $assign_array['child_js_url'] = $assign_array['child_theme_assets'] . 'js/';
            }

            foreach ($assign_array as $assign_key => $assign_value) {
                $urls[$assign_key] = $http . Tools::getMediaServer($assign_value) . $assign_value;
            }

            $pages = [];
            $p = [
                'address', 'addresses', 'authentication', 'manufacturer', 'cart', 'category', 'cms', 'contact',
                'discount', 'guest-tracking', 'history', 'identity', 'index', 'my-account',
                'order-confirmation', 'order-detail', 'order-follow', 'order', 'order-return',
                'order-slip', 'pagenotfound', 'password', 'pdf-invoice', 'pdf-order-return', 'pdf-order-slip',
                'prices-drop', 'product', 'registration', 'search', 'sitemap', 'stores', 'supplier', 'new-products',
            ];
            foreach ($p as $page_name) {
                $index = str_replace('-', '_', $page_name);
                $pages[$index] = $this->context->link->getPageLink($page_name, $this->ssl);
            }
            $pages['brands'] = $pages['manufacturer'];
            $pages['register'] = $this->context->link->getPageLink('registration', true);
            $pages['order_login'] = $this->context->link->getPageLink('order', true, null, ['login' => '1']);
            $urls['pages'] = $pages;

            $urls['alternative_langs'] = $this->getAlternativeLangsUrl();

            $urls['actions'] = [
                'logout' => $this->context->link->getPageLink('index', true, null, 'mylogout'),
            ];

            $imageRetriever = new ImageRetriever($this->context->link);
            $urls['no_picture_image'] = $imageRetriever->getNoPictureImage($this->context->language);

            $this->urls = $urls;
        }

        return $this->urls;
    }

    /**
     * Initializes a set of commonly used variables of current configuration, available for use
     * in the template. @see FrontController::assignGeneralPurposeVariables for more information.
     *
     * @return array
     */
    public function getTemplateVarConfiguration()
    {
        $quantity_discount_price = Configuration::get('PS_DISPLAY_DISCOUNT_PRICE');

        return [
            'display_taxes_label' => $this->getDisplayTaxesLabel(),
            'display_prices_tax_incl' => (bool) (new TaxConfiguration())->includeTaxes(),
            'taxes_enabled' => (bool) Configuration::get('PS_TAX'),
            'low_quantity_threshold' => (int) Configuration::get('PS_LAST_QTIES'),
            'is_b2b' => (bool) Configuration::get('PS_B2B_ENABLE'),
            'is_catalog' => (bool) Configuration::isCatalogMode(),
            'show_prices' => (bool) Configuration::showPrices(),
            'opt_in' => [
                'partner' => (bool) Configuration::get('PS_CUSTOMER_OPTIN'),
            ],
            'quantity_discount' => [
                'type' => ($quantity_discount_price) ? 'price' : 'discount',
                'label' => ($quantity_discount_price)
                    ? $this->getTranslator()->trans('Unit price', [], 'Shop.Theme.Catalog')
                    : $this->getTranslator()->trans('Unit discount', [], 'Shop.Theme.Catalog'),
            ],
            'voucher_enabled' => (int) CartRule::isFeatureActive(),
            'return_enabled' => (int) Configuration::get('PS_ORDER_RETURN'),
            'number_of_days_for_return' => (int) Configuration::get('PS_ORDER_RETURN_NB_DAYS'),
            'password_policy' => [
                'minimum_length' => (int) Configuration::get(PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_LENGTH),
                'maximum_length' => (int) Configuration::get(PasswordPolicyConfiguration::CONFIGURATION_MAXIMUM_LENGTH),
                'minimum_score' => (int) Configuration::get(PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_SCORE),
            ],
        ];
    }

    protected function getDisplayTaxesLabel()
    {
        return (Module::isEnabled('ps_legalcompliance') && (bool) Configuration::get('AEUC_LABEL_TAX_INC_EXC')) || $this->context->country->display_tax_label;
    }

    /**
     * Initializes information about currently used currency, available for use in the template.
     *
     * @see FrontController::assignGeneralPurposeVariables for more information.
     *
     * @return array
     */
    public function getTemplateVarCurrency()
    {
        $curr = [];
        $fields = ['id', 'name', 'iso_code', 'iso_code_num', 'sign'];
        foreach ($fields as $field_name) {
            $curr[$field_name] = $this->context->currency->{$field_name};
        }

        return $curr;
    }

    /**
     * Initializes information about current customer, available for use in the template.
     *
     * @see FrontController::assignGeneralPurposeVariables for more information.
     *
     * @return array
     */
    public function getTemplateVarCustomer($customer = null)
    {
        if (Validate::isLoadedObject($customer)) {
            $cust = $this->objectPresenter->present($customer);
        } else {
            $cust = $this->objectPresenter->present($this->context->customer);
        }

        unset(
            $cust['secure_key'],
            $cust['passwd'],
            $cust['show_public_prices'],
            $cust['deleted'],
            $cust['id_lang']
        );

        $cust['id'] = $this->context->customer->id;
        $cust['is_logged'] = $this->context->customer->isLogged();

        $cust['gender'] = $this->objectPresenter->present(new Gender($cust['id_gender']));
        unset($cust['id_gender']);

        $cust['risk'] = $this->objectPresenter->present(new Risk($cust['id_risk']));
        unset($cust['id_risk']);

        $addresses = $this->context->customer->getSimpleAddresses();
        foreach ($addresses as &$a) {
            $a['formatted'] = AddressFormat::generateAddress(new Address($a['id']), [], '<br>');
        }
        $cust['addresses'] = $addresses;

        return $cust;
    }

    /**
     * Get the shop logo with its dimensions
     *
     * @return array<string, string|int>
     */
    public function getShopLogo(): array
    {
        $logoFileName = Configuration::get('PS_LOGO');
        if (!$logoFileName) {
            return [];
        }

        $logoFileDir = _PS_IMG_DIR_ . $logoFileName;

        if (!file_exists($logoFileDir)) {
            return [];
        }

        list($logoWidth, $logoHeight) = getimagesize($logoFileDir);

        return [
            'src' => ($this->getTemplateVarUrls()['img_ps_url'] ?? _PS_IMG_) . $logoFileName,
            'width' => $logoWidth,
            'height' => $logoHeight,
        ];
    }

    public function getCoreJsPublicPath()
    {
        return $this->context->shop->physical_uri . 'themes/';
    }

    /**
     * Initializes a set of commonly used information about the shop, available for use
     * in the template. @see FrontController::assignGeneralPurposeVariables for more information.
     *
     * @return array
     */
    public function getTemplateVarShop()
    {
        $address = $this->context->shop->getAddress();

        $urls = $this->getTemplateVarUrls();
        $psImageUrl = $urls['img_ps_url'] ?? _PS_IMG_;

        $shop = [
            'id' => $this->context->shop->id,
            'name' => Configuration::get('PS_SHOP_NAME'),
            'email' => Configuration::get('PS_SHOP_EMAIL'),
            'registration_number' => Configuration::get('PS_SHOP_DETAILS'),

            'long' => Configuration::get('PS_STORES_CENTER_LONG'),
            'lat' => Configuration::get('PS_STORES_CENTER_LAT'),

            'logo' => self::configuredImageUrl('PS_LOGO', $psImageUrl),
            'logo_details' => $this->getShopLogo(),
            'stores_icon' => self::configuredImageUrl('PS_STORES_ICON', $psImageUrl),
            'favicon' => self::configuredImageUrl('PS_STORES_ICON', $psImageUrl),
            'favicon_update_time' => Configuration::get('PS_IMG_UPDATE_TIME'),

            'address' => [
                'formatted' => AddressFormat::generateAddress($address, [], '<br>'),
                'address1' => $address->address1,
                'address2' => $address->address2,
                'postcode' => $address->postcode,
                'city' => $address->city,
                'state' => (new State($address->id_state))->name,
                'country' => (new Country($address->id_country))->name[$this->context->language->id],
            ],
            'phone' => Configuration::get('PS_SHOP_PHONE'),
            'fax' => Configuration::get('PS_SHOP_FAX'),
        ];

        return $shop;
    }

    private static function configuredImageUrl(string $configKey, $psImageUrl)
    {
        $image = Configuration::get($configKey);

        return $image ? $psImageUrl . $image : '';
    }

    /**
     * Initializes a set of commonly used variables related to the current page, available for use
     * in the template. @see FrontController::assignGeneralPurposeVariables for more information.
     *
     * @return array
     */
    public function getTemplateVarPage()
    {
        $page_name = $this->getPageName();
        $meta_tags = Meta::getMetaTags($this->context->language->id, $page_name);

        $my_account_controllers = [
            'address',
            'authentication',
            'discount',
            'history',
            'identity',
            'order-follow',
            'order-slip',
            'password',
            'registration',
            'guest-tracking',
        ];

        $body_classes = [
            'lang-' . $this->context->language->iso_code => true,
            'lang-rtl' => (bool) $this->context->language->is_rtl,
            'country-' . $this->context->country->iso_code => true,
            'currency-' . $this->context->currency->iso_code => true,
            $this->getLayoutName() => true,
            'page-' . $this->php_self => true,
            'tax-display-' . ($this->getDisplayTaxesLabel() ? 'enabled' : 'disabled') => true,
            'page-customer-account' => false,
        ];

        $page = [
            'title' => '',
            'canonical' => $this->getCanonicalURL(),
            'meta' => [
                'title' => $meta_tags['meta_title'],
                'description' => $meta_tags['meta_description'],
                'keywords' => $meta_tags['meta_keywords'],
                'robots' => 'index',
            ],
            'page_name' => $page_name,
            'body_classes' => $body_classes,
            'admin_notifications' => [],
            'password-policy' => [
                'feedbacks' => [
                    0 => $this->getTranslator()->trans('Very weak', [], 'Shop.Theme.Global'),
                    1 => $this->getTranslator()->trans('Weak', [], 'Shop.Theme.Global'),
                    2 => $this->getTranslator()->trans('Average', [], 'Shop.Theme.Global'),
                    3 => $this->getTranslator()->trans('Strong', [], 'Shop.Theme.Global'),
                    4 => $this->getTranslator()->trans('Very strong', [], 'Shop.Theme.Global'),
                    'Straight rows of keys are easy to guess' => $this->getTranslator()->trans('Straight rows of keys are easy to guess', [], 'Shop.Theme.Global'),
                    'Short keyboard patterns are easy to guess' => $this->getTranslator()->trans('Short keyboard patterns are easy to guess', [], 'Shop.Theme.Global'),
                    'Use a longer keyboard pattern with more turns' => $this->getTranslator()->trans('Use a longer keyboard pattern with more turns', [], 'Shop.Theme.Global'),
                    'Repeats like "aaa" are easy to guess' => $this->getTranslator()->trans('Repeats like "aaa" are easy to guess', [], 'Shop.Theme.Global'),
                    'Repeats like "abcabcabc" are only slightly harder to guess than "abc"' => $this->getTranslator()->trans('Repeats like "abcabcabc" are only slightly harder to guess than "abc"', [], 'Shop.Theme.Global'),
                    'Sequences like abc or 6543 are easy to guess' => $this->getTranslator()->trans('Sequences like "abc" or "6543" are easy to guess', [], 'Shop.Theme.Global'),
                    'Recent years are easy to guess' => $this->getTranslator()->trans('Recent years are easy to guess', [], 'Shop.Theme.Global'),
                    'Dates are often easy to guess' => $this->getTranslator()->trans('Dates are often easy to guess', [], 'Shop.Theme.Global'),
                    'This is a top-10 common password' => $this->getTranslator()->trans('This is a top-10 common password', [], 'Shop.Theme.Global'),
                    'This is a top-100 common password' => $this->getTranslator()->trans('This is a top-100 common password', [], 'Shop.Theme.Global'),
                    'This is a very common password' => $this->getTranslator()->trans('This is a very common password', [], 'Shop.Theme.Global'),
                    'This is similar to a commonly used password' => $this->getTranslator()->trans('This is similar to a commonly used password', [], 'Shop.Theme.Global'),
                    'A word by itself is easy to guess' => $this->getTranslator()->trans('A word by itself is easy to guess', [], 'Shop.Theme.Global'),
                    'Names and surnames by themselves are easy to guess' => $this->getTranslator()->trans('Names and surnames by themselves are easy to guess', [], 'Shop.Theme.Global'),
                    'Common names and surnames are easy to guess' => $this->getTranslator()->trans('Common names and surnames are easy to guess', [], 'Shop.Theme.Global'),
                    'Use a few words, avoid common phrases' => $this->getTranslator()->trans('Use a few words, avoid common phrases', [], 'Shop.Theme.Global'),
                    'No need for symbols, digits, or uppercase letters' => $this->getTranslator()->trans('No need for symbols, digits, or uppercase letters', [], 'Shop.Theme.Global'),
                    'Avoid repeated words and characters' => $this->getTranslator()->trans('Avoid repeated words and characters', [], 'Shop.Theme.Global'),
                    'Avoid sequences' => $this->getTranslator()->trans('Avoid sequences', [], 'Shop.Theme.Global'),
                    'Avoid recent years' => $this->getTranslator()->trans('Avoid recent years', [], 'Shop.Theme.Global'),
                    'Avoid years that are associated with you' => $this->getTranslator()->trans('Avoid years that are associated with you', [], 'Shop.Theme.Global'),
                    'Avoid dates and years that are associated with you' => $this->getTranslator()->trans('Avoid dates and years that are associated with you', [], 'Shop.Theme.Global'),
                    'Capitalization doesn\'t help very much' => $this->getTranslator()->trans('Capitalization doesn\'t help very much', [], 'Shop.Theme.Global'),
                    'All-uppercase is almost as easy to guess as all-lowercase' => $this->getTranslator()->trans('All-uppercase is almost as easy to guess as all-lowercase', [], 'Shop.Theme.Global'),
                    'Reversed words aren\'t much harder to guess' => $this->getTranslator()->trans('Reversed words aren\'t much harder to guess', [], 'Shop.Theme.Global'),
                    'Predictable substitutions like \'@\' instead of \'a\' don\'t help very much' => $this->getTranslator()->trans('Predictable substitutions like "@" instead of "a" don\'t help very much', [], 'Shop.Theme.Global'),
                    'Add another word or two. Uncommon words are better.' => $this->getTranslator()->trans('Add another word or two. Uncommon words are better.', [], 'Shop.Theme.Global'),
                ],
            ],
        ];

        if (in_array($this->php_self, $my_account_controllers)) {
            $page['body_classes']['page-customer-account'] = true;
        }

        return $page;
    }

    /**
     * Returns a list of breadcrumbs with their count to show on the current page.
     *
     * @return array
     */
    public function getBreadcrumb()
    {
        $breadcrumb = $this->getBreadcrumbLinks();
        $breadcrumb['count'] = count($breadcrumb['links']);

        return $breadcrumb;
    }

    /**
     * Returns an array of breadcrumbs for current controller. Inheriting controllers should
     * extend this list in most cases.
     *
     * @return array
     */
    protected function getBreadcrumbLinks()
    {
        $breadcrumb = [];

        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Home', [], 'Shop.Theme.Global'),
            'url' => $this->context->link->getPageLink('index', true),
        ];

        return $breadcrumb;
    }

    protected function getCategoryPath($category)
    {
        if ($category->id_parent != 0 && !$category->is_root_category) {
            return [
                'title' => $category->name,
                'url' => $this->context->link->getCategoryLink($category),
            ];
        }
    }

    /**
     * Returns breadcrumb step for "Your account" page.
     *
     * @return array
     */
    protected function addMyAccountToBreadcrumb()
    {
        return [
            'title' => $this->getTranslator()->trans('Your account', [], 'Shop.Theme.Customeraccount'),
            'url' => $this->context->link->getPageLink('my-account', true),
        ];
    }

    /**
     * Generate the canonical URL of the current page
     *
     * Mainly used for ProductController and CategoryController
     * but can be implemented by other classes inheriting from FrontController
     *
     * @return string|void
     */
    public function getCanonicalURL()
    {
    }

    /**
     * @deprecated Since 9.0 and will be removed in the next major.
     */
    protected function updateQueryString(array $extraParams = null)
    {
        return Tools::updateCurrentQueryString($extraParams);
    }

    protected function getCurrentURL()
    {
        return Tools::getCurrentUrl();
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
        } elseif (preg_match('#^' . preg_quote($this->context->shop->physical_uri, '#') . 'modules/([a-zA-Z0-9_-]+?)/(.*)$#', $_SERVER['REQUEST_URI'], $m)) {
            /** @retrocompatibility Are we in a module ? */
            $page_name = 'module-' . $m[1] . '-' . str_replace(['.php', '/'], ['', '-'], $m[2]);
        } else {
            $page_name = Dispatcher::getInstance()->getController();
            $page_name = (preg_match('/^[0-9]/', $page_name) ? 'page_' . $page_name : $page_name);
        }

        return $page_name;
    }

    protected function render($template, array $params = [])
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
                $scope->assign(['notifications' => $this->prepareNotifications()]);

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
            ->setPartnerOptinRequired($customer->isFieldRequired('optin'));

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

    /**
     * {@inheritdoc}
     */
    protected function buildContainer(): ContainerInterface
    {
        /* @phpstan-ignore-next-line */
        if (FRONT_LEGACY_CONTEXT) {
            return ContainerBuilder::getContainer('front', _PS_MODE_DEV_);
        }

        return SymfonyContainer::getInstance();
    }

    /**
     * @return array containing the URLs of the same page but for different languages
     */
    protected function getAlternativeLangsUrl()
    {
        $alternativeLangs = [];
        $languages = Language::getLanguages(true, $this->context->shop->id);

        if (count($languages) < 2) {
            // No need to display alternative lang if there is only one enabled
            return $alternativeLangs;
        }

        foreach ($languages as $lang) {
            $langUrl = $this->context->link->getLanguageLink($lang['id_lang']);
            $alternativeLangs[$lang['language_code']] = $this->sanitizeUrl($langUrl);
        }

        return $alternativeLangs;
    }

    /**
     * Sanitize / Clean params of an URL
     *
     * @param string $url URL to clean
     *
     * @return string cleaned URL
     */
    protected function sanitizeUrl(string $url): string
    {
        $params = [];

        // Extract all parts of the URL
        $url_details = parse_url($url);
        if (!empty($url_details['query'])) {
            parse_str($url_details['query'], $query);
            $params = $this->sanitizeQueryOutput($query);
        }

        // Build a list of parameters we won't be sanitizing
        $excludedKeys = array_merge(
            ['isolang', 'id_lang', 'controller', 'fc', 'id_product', 'id_category', 'id_manufacturer', 'id_supplier', 'id_cms'],
            $this->redirectionExtraExcludedKeys
        );

        // Go through each parameter we got from dispatcher and sanitize it
        foreach ($params as $key => $value) {
            if (
                in_array($key, $excludedKeys)
                || !Validate::isUrl($key)
                || !$this->validateInputAsUrl($value)
            ) {
                continue;
            }

            $params[Tools::safeOutput($key)] = is_array($value) ? array_walk_recursive($value, 'Tools::safeOutput') : Tools::safeOutput($value);
        }

        // Build back the query
        $str_params = http_build_query($params, '', '&');
        $sanitizedUrl = preg_replace('/^([^?]*)?.*$/', '$1', $url) . (!empty($str_params) ? '?' . $str_params : '');

        return $sanitizedUrl;
    }

    /**
     * Recursively sanitize output query
     *
     * @param array $query URL query
     *
     * @return array
     */
    protected function sanitizeQueryOutput(array $query): array
    {
        $params = [];
        foreach ($query as $key => $value) {
            if (is_array($value)) {
                $params[Tools::safeOutput($key)] = $this->sanitizeQueryOutput($value);
            } else {
                $params[Tools::safeOutput($key)] = Tools::safeOutput($value);
            }
        }

        return $params;
    }

    /**
     * Validate data recursively to be sure it's URL compliant
     *
     * @return bool
     */
    protected function validateInputAsUrl($data): bool
    {
        if (is_array($data)) {
            $returnStatement = true;
            foreach ($data as $value) {
                $returnStatement = $returnStatement && $this->validateInputAsUrl($value);
            }

            return $returnStatement;
        }

        return Validate::isUrl($data);
    }
}
