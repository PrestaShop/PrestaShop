<?php
/**
 * 2007-2018 PrestaShop.
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use PrestaShopBundle\Translation\TranslatorComponent as Translator;
use PrestaShopBundle\Translation\Loader\SqlTranslationLoader;

/**
 * Class ContextCore.
 *
 * @since 1.5.0.1
 */
class ContextCore
{
    /* @var Context */
    protected static $instance;

    /** @var Cart */
    public $cart;

    /** @var Customer */
    public $customer;

    /** @var Cookie */
    public $cookie;

    /** @var Link */
    public $link;

    /** @var Country */
    public $country;

    /** @var Employee */
    public $employee;

    /** @var AdminController|FrontController */
    public $controller;

    /** @var string $override_controller_name_for_translations */
    public $override_controller_name_for_translations;

    /** @var Language */
    public $language;

    /** @var Currency */
    public $currency;

    /** @var Tab */
    public $tab;

    /** @var Shop */
    public $shop;

    /** @var Smarty */
    public $smarty;

    /** @var \Mobile_Detect */
    public $mobile_detect;

    /** @var int */
    public $mode;

    /** @var Translator */
    protected $translator = null;

    /**
     * Mobile device of the customer.
     *
     * @var bool|null
     */
    protected $mobile_device = null;

    /** @var bool|null */
    protected $is_mobile = null;

    /** @var bool|null */
    protected $is_tablet = null;

    /** @var int */
    const DEVICE_COMPUTER = 1;

    /** @var int */
    const DEVICE_TABLET = 2;

    /** @var int */
    const DEVICE_MOBILE = 4;

    /** @var int */
    const MODE_STD = 1;

    /** @var int */
    const MODE_STD_CONTRIB = 2;

    /** @var int */
    const MODE_HOST_CONTRIB = 4;

    /** @var int */
    const MODE_HOST = 8;

    /**
     * Sets Mobile_Detect tool object.
     *
     * @return Mobile_Detect
     */
    public function getMobileDetect()
    {
        if ($this->mobile_detect === null) {
            $this->mobile_detect = new Mobile_Detect();
        }

        return $this->mobile_detect;
    }

    /**
     * Checks if visitor's device is a mobile device.
     *
     * @return bool
     */
    public function isMobile()
    {
        if ($this->is_mobile === null) {
            $mobileDetect = $this->getMobileDetect();
            $this->is_mobile = $mobileDetect->isMobile();
        }

        return $this->is_mobile;
    }

    /**
     * Checks if visitor's device is a tablet device.
     *
     * @return bool
     */
    public function isTablet()
    {
        if ($this->is_tablet === null) {
            $mobileDetect = $this->getMobileDetect();
            $this->is_tablet = $mobileDetect->isTablet();
        }

        return $this->is_tablet;
    }

    /**
     * Sets mobile_device context variable.
     *
     * @return bool
     */
    public function getMobileDevice()
    {
        if ($this->mobile_device === null) {
            $this->mobile_device = false;
            if ($this->checkMobileContext()) {
                if (isset(Context::getContext()->cookie->no_mobile) && Context::getContext()->cookie->no_mobile == false && (int) Configuration::get('PS_ALLOW_MOBILE_DEVICE') != 0) {
                    $this->mobile_device = true;
                } else {
                    switch ((int) Configuration::get('PS_ALLOW_MOBILE_DEVICE')) {
                        case 1: // Only for mobile device
                            if ($this->isMobile() && !$this->isTablet()) {
                                $this->mobile_device = true;
                            }
                            break;
                        case 2: // Only for touchpads
                            if ($this->isTablet() && !$this->isMobile()) {
                                $this->mobile_device = true;
                            }
                            break;
                        case 3: // For touchpad or mobile devices
                            if ($this->isMobile() || $this->isTablet()) {
                                $this->mobile_device = true;
                            }
                            break;
                    }
                }
            }
        }

        return $this->mobile_device;
    }

    /**
     * Returns mobile device type.
     *
     * @return int
     */
    public function getDevice()
    {
        static $device = null;

        if ($device === null) {
            if ($this->isTablet()) {
                $device = Context::DEVICE_TABLET;
            } elseif ($this->isMobile()) {
                $device = Context::DEVICE_MOBILE;
            } else {
                $device = Context::DEVICE_COMPUTER;
            }
        }

        return $device;
    }

    /**
     * Checks if mobile context is possible.
     *
     * @return bool
     *
     * @throws PrestaShopException
     */
    protected function checkMobileContext()
    {
        // Check mobile context
        if (Tools::isSubmit('no_mobile_theme')) {
            Context::getContext()->cookie->no_mobile = true;
            if (Context::getContext()->cookie->id_guest) {
                $guest = new Guest(Context::getContext()->cookie->id_guest);
                $guest->mobile_theme = false;
                $guest->update();
            }
        } elseif (Tools::isSubmit('mobile_theme_ok')) {
            Context::getContext()->cookie->no_mobile = false;
            if (Context::getContext()->cookie->id_guest) {
                $guest = new Guest(Context::getContext()->cookie->id_guest);
                $guest->mobile_theme = true;
                $guest->update();
            }
        }

        return isset($_SERVER['HTTP_USER_AGENT'])
            && isset(Context::getContext()->cookie)
            && (bool) Configuration::get('PS_ALLOW_MOBILE_DEVICE')
            && @filemtime(_PS_THEME_MOBILE_DIR_)
            && !Context::getContext()->cookie->no_mobile;
    }

    /**
     * Get a singleton instance of Context object.
     *
     * @return Context
     */
    public static function getContext()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Context();
        }

        return self::$instance;
    }

    /**
     * @param $testInstance Context
     * Unit testing purpose only
     */
    public static function setInstanceForTesting($testInstance)
    {
        self::$instance = $testInstance;
    }

    /**
     * Unit testing purpose only.
     */
    public static function deleteTestingInstance()
    {
        self::$instance = null;
    }

    /**
     * Clone current context object.
     *
     * @return Context
     */
    public function cloneContext()
    {
        return clone $this;
    }

    /**
     * Update context after customer login.
     *
     * @param Customer $customer Created customer
     */
    public function updateCustomer(Customer $customer)
    {
        $this->customer = $customer;
        $this->cookie->id_customer = (int) $customer->id;
        $this->cookie->customer_lastname = $customer->lastname;
        $this->cookie->customer_firstname = $customer->firstname;
        $this->cookie->passwd = $customer->passwd;
        $this->cookie->logged = 1;
        $customer->logged = 1;
        $this->cookie->email = $customer->email;
        $this->cookie->is_guest = $customer->isGuest();
        $this->cart->secure_key = $customer->secure_key;

        if (Configuration::get('PS_CART_FOLLOWING') && (empty($this->cookie->id_cart) || Cart::getNbProducts($this->cookie->id_cart) == 0) && $idCart = (int) Cart::lastNoneOrderedCart($this->customer->id)) {
            $this->cart = new Cart($idCart);
        } else {
            $idCarrier = (int) $this->cart->id_carrier;
            $this->cart->id_carrier = 0;
            $this->cart->setDeliveryOption(null);
            $this->cart->updateAddressId($this->cart->id_address_delivery, (int) Address::getFirstCustomerAddressId((int) ($customer->id)));
            $this->cart->id_address_delivery = (int) Address::getFirstCustomerAddressId((int) ($customer->id));
            $this->cart->id_address_invoice = (int) Address::getFirstCustomerAddressId((int) ($customer->id));
        }
        $this->cart->id_customer = (int) $customer->id;

        if (isset($idCarrier) && $idCarrier) {
            $deliveryOption = [$this->cart->id_address_delivery => $idCarrier . ','];
            $this->cart->setDeliveryOption($deliveryOption);
        }

        $this->cart->save();
        $this->cookie->id_cart = (int) $this->cart->id;
        $this->cookie->write();
        $this->cart->autosetProductAddress();
    }

    /**
     * @return Translator
     */
    public function getTranslator()
    {
        if (null !== $this->translator) {
            return $this->translator;
        }

        $translator = $this->getTranslatorFromLocale($this->language->locale);
        // In case we have at least 1 translated message, we return the current translator.
        if (count($translator->getCatalogue($this->language->locale)->all())) {
            $this->translator = $translator;
        }

        return $translator;
    }

    /**
     * Returns a new instance of Translator for the provided locale code.
     *
     * @param string $locale 5-letter iso code
     *
     * @return Translator
     */
    public function getTranslatorFromLocale($locale)
    {
        $cacheDir = _PS_CACHE_DIR_ . 'translations';
        $translator = new Translator($locale, null, $cacheDir, false);

        // In case we have at least 1 translated message, we return the current translator.
        // If some translations are missing, clear cache
        if (count($translator->getCatalogue($locale)->all())) {
            $this->translator = $translator;

            return $translator;
        }

        // However, in some case, even empty catalog were stored in the cache and then used as-is.
        // For this one, we drop the cache and try to regenerate it.
        if (is_dir($cacheDir)) {
            $cache_file = Finder::create()
                ->files()
                ->in($cacheDir)
                ->depth('==0')
                ->name('*.' . $locale . '.*');
            (new Filesystem())->remove($cache_file);
        }

        $adminContext = defined('_PS_ADMIN_DIR_');
        $translator->addLoader('xlf', new XliffFileLoader());

        $sqlTranslationLoader = new SqlTranslationLoader();
        if (!is_null($this->shop)) {
            $sqlTranslationLoader->setTheme($this->shop->theme);
        }

        $translator->addLoader('db', $sqlTranslationLoader);
        $notName = $adminContext ? '^Shop*' : '^Admin*';

        $finder = Finder::create()
            ->files()
            ->name('*.' . $locale . '.xlf')
            ->notName($notName)
            ->in($this->getTranslationResourcesDirectories())
        ;

        foreach ($finder as $file) {
            list($domain, $locale, $format) = explode('.', $file->getBasename(), 3);

            $translator->addResource($format, $file, $locale, $domain);
            if (!is_a($this->language, 'PrestashopBundle\Install\Language')) {
                $translator->addResource('db', $domain . '.' . $locale . '.db', $locale, $domain);
            }
        }

        return $translator;
    }

    /**
     * @return array
     */
    protected function getTranslationResourcesDirectories()
    {
        $locations = array(_PS_ROOT_DIR_ . '/app/Resources/translations');

        if (!is_null($this->shop)) {
            $activeThemeLocation = _PS_ROOT_DIR_ . '/themes/' . $this->shop->theme_name . '/translations';
            if (is_dir($activeThemeLocation)) {
                $locations[] = $activeThemeLocation;
            }
        }

        return $locations;
    }
}
