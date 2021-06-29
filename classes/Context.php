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

use PrestaShop\PrestaShop\Adapter\ContainerFinder;
use PrestaShop\PrestaShop\Adapter\Module\Repository\ModuleRepository;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\Exception\ContainerNotFoundException;
use PrestaShop\PrestaShop\Core\Localization\CLDR\ComputingPrecision;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShopBundle\Install\Language as InstallLanguage;
use PrestaShopBundle\Translation\TranslatorComponent as Translator;
use PrestaShopBundle\Translation\TranslatorLanguageLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class ContextCore.
 *
 * @since 1.5.0.1
 */
class ContextCore
{
    /** @var Context */
    protected static $instance;

    /** @var Cart */
    public $cart;

    /** @var Customer */
    public $customer;

    /** @var Cookie */
    public $cookie;

    /** @var SessionInterface|null */
    public $session;

    /** @var Link */
    public $link;

    /** @var Country */
    public $country;

    /** @var Employee|null */
    public $employee;

    /** @var AdminController|FrontController */
    public $controller;

    /** @var string */
    public $override_controller_name_for_translations;

    /** @var Language|InstallLanguage */
    public $language;

    /** @var Currency|null */
    public $currency;

    /**
     * Current locale instance.
     *
     * @var Locale|null
     */
    public $currentLocale;

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

    /** @var ContainerBuilder */
    public $container;

    /** @var Translator */
    protected $translator = null;

    /** @var int */
    protected $priceComputingPrecision = null;

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
     * @return Locale|null
     */
    public function getCurrentLocale()
    {
        return $this->currentLocale;
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

        return isset($_SERVER['HTTP_USER_AGENT'], Context::getContext()->cookie)
            && (bool) Configuration::get('PS_ALLOW_MOBILE_DEVICE')
            && defined('_PS_THEME_MOBILE_DIR_')
            && @filemtime(_PS_THEME_MOBILE_DIR_)
            && !Context::getContext()->cookie->no_mobile;
    }

    /**
     * Get a singleton instance of Context object.
     *
     * @return Context|null
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

        if (Configuration::get('PS_CART_FOLLOWING') && (empty($this->cookie->id_cart) || Cart::getNbProducts($this->cookie->id_cart) == 0) && $idCart = (int) Cart::lastNoneOrderedCart($this->customer->id)) {
            $this->cart = new Cart($idCart);
            $this->cart->secure_key = $customer->secure_key;
        } else {
            $idCarrier = (int) $this->cart->id_carrier;
            $this->cart->secure_key = $customer->secure_key;
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

        $this->cookie->registerSession(new CustomerSession());
    }

    /**
     * Returns a translator depending on service container availability and if the method
     * is called by the installer or not.
     *
     * @param bool $isInstaller Set to true if the method is called by the installer
     *
     * @return Translator
     */
    public function getTranslator($isInstaller = false)
    {
        if (null !== $this->translator && $this->language->locale === $this->translator->getLocale()) {
            return $this->translator;
        }

        $sfContainer = SymfonyContainer::getInstance();

        if ($isInstaller || null === $sfContainer) {
            // symfony's container isn't available in front office, so we load and configure the translator component
            $this->translator = $this->getTranslatorFromLocale($this->language->locale);
        } else {
            $this->translator = $sfContainer->get('translator');
            // We need to set the locale here because in legacy BO pages, the translator is used
            // before the TranslatorListener does its job of setting the locale according to the Request object
            $this->translator->setLocale($this->language->locale);
        }

        return $this->translator;
    }

    /**
     * Returns a new instance of Translator for the provided locale code.
     *
     * @param string $locale IETF language tag (eg. "en-US")
     *
     * @return Translator
     */
    public function getTranslatorFromLocale($locale)
    {
        $cacheDir = _PS_CACHE_DIR_ . 'translations';
        $translator = new Translator($locale, null, $cacheDir, false);

        // In case we have at least 1 translated message, we return the current translator.
        // If some translations are missing, clear cache
        if ($locale === '' || null === $locale || count($translator->getCatalogue($locale)->all())) {
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

        $translator->clearLanguage($locale);

        $adminContext = defined('_PS_ADMIN_DIR_');
        // Do not load DB translations when $this->language is InstallLanguage
        // because it means that we're looking for the installer translations, so we're not yet connected to the DB
        $withDB = !$this->language instanceof InstallLanguage;
        $theme = $this->shop !== null ? $this->shop->theme : null;

        try {
            $containerFinder = new ContainerFinder($this);
            $containerFinder->getContainer()->get('prestashop.translation.translator_language_loader')
                ->setIsAdminContext($adminContext)
                ->loadLanguage($translator, $locale, $withDB, $theme);
        } catch (ContainerNotFoundException $exception) {
            // If a container is still not found, instantiate manually the translator loader
            // This will happen in the Front as we have legacy controllers, the Sf container won't be available.
            // As we get the translator in the controller's constructor and the container is built in the init method, we won't find it here
            (new TranslatorLanguageLoader(new ModuleRepository()))
                ->setIsAdminContext($adminContext)
                ->loadLanguage($translator, $locale, $withDB, $theme);
        }

        return $translator;
    }

    /**
     * @return array
     */
    protected function getTranslationResourcesDirectories()
    {
        $locations = [_PS_ROOT_DIR_ . '/translations'];

        if (null !== $this->shop) {
            $activeThemeLocation = _PS_ROOT_DIR_ . '/themes/' . $this->shop->theme_name . '/translations';
            if (is_dir($activeThemeLocation)) {
                $locations[] = $activeThemeLocation;
            }
        }

        return $locations;
    }

    /**
     * Returns the computing precision according to the current currency
     *
     * @return int
     */
    public function getComputingPrecision()
    {
        if ($this->priceComputingPrecision === null) {
            $computingPrecision = new ComputingPrecision();
            $this->priceComputingPrecision = $computingPrecision->getPrecision($this->currency->precision);
        }

        return $this->priceComputingPrecision;
    }
}
