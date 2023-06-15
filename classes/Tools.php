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

use Composer\CaBundle\CaBundle;
use PHPSQLParser\PHPSQLParser;
use PrestaShop\PrestaShop\Adapter\ContainerFinder;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;
use PrestaShop\PrestaShop\Core\Foundation\Filesystem\FileSystem as PsFileSystem;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository as LocaleRepository;
use PrestaShop\PrestaShop\Core\Security\Hashing;
use PrestaShop\PrestaShop\Core\Security\OpenSsl\OpenSSL;
use PrestaShop\PrestaShop\Core\Security\PasswordGenerator;
use PrestaShop\PrestaShop\Core\Util\String\StringModifier;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;

class ToolsCore
{
    public const CACERT_LOCATION = 'https://curl.haxx.se/ca/cacert.pem';
    public const SERVICE_LOCALE_REPOSITORY = 'prestashop.core.localization.locale.repository';
    public const CACHE_LIFETIME_SECONDS = 604800;

    public const PASSWORDGEN_FLAG_NUMERIC = PasswordGenerator::PASSWORDGEN_FLAG_NUMERIC;
    public const PASSWORDGEN_FLAG_NO_NUMERIC = PasswordGenerator::PASSWORDGEN_FLAG_NO_NUMERIC;
    public const PASSWORDGEN_FLAG_RANDOM = PasswordGenerator::PASSWORDGEN_FLAG_RANDOM;
    public const PASSWORDGEN_FLAG_ALPHANUMERIC = PasswordGenerator::PASSWORDGEN_FLAG_ALPHANUMERIC;

    public const LANGUAGE_EXTRACTOR_REGEXP = '#(?<=-)\w\w|\w\w(?!-)#';

    protected static $file_exists_cache = [];
    protected static $_forceCompile;
    protected static $_caching;
    protected static $_user_plateform;
    protected static $_user_browser;
    protected static $request;
    protected static $cldr_cache = [];
    protected static $colorBrightnessCalculator;
    protected static $fallbackParameters = [];

    public static $round_mode = null;

    /**
     * @param Request $request
     */
    public function __construct(Request $request = null)
    {
        if ($request) {
            self::$request = $request;
        }
    }

    /**
     * Properly clean static cache
     */
    public static function resetStaticCache()
    {
        static::$cldr_cache = [];
    }

    /**
     * Reset the request set during the first new Tools($request) call.
     */
    public static function resetRequest()
    {
        self::$request = null;
    }

    /**
     * Random password generator.
     *
     * @param int $length Desired length (optional)
     * @param string $flag Output type (NUMERIC, ALPHANUMERIC, NO_NUMERIC, RANDOM)
     *
     * @return string|false Password
     */
    public static function passwdGen($length = 8, $flag = self::PASSWORDGEN_FLAG_ALPHANUMERIC)
    {
        try {
            return (new PasswordGenerator(new OpenSSL()))->generatePassword($length, $flag);
        } catch (InvalidArgumentException $exception) {
            return false;
        }
    }

    /**
     * Replace text within a portion of a string.
     *
     * Replaces a string matching a search, (optionally) string from a certain position
     *
     * @param string $search The string to search in the input string
     * @param string $replace The replacement string
     * @param string $subject The input string
     * @param int $cur Starting position cursor for the search
     *
     * @return string the result string is returned
     */
    public static function strReplaceFirst($search, $replace, $subject, $cur = 0)
    {
        $strPos = strpos($subject, $search, $cur);

        return $strPos !== false ? substr_replace($subject, $replace, (int) $strPos, strlen($search)) : $subject;
    }

    /**
     * Redirect user to another page.
     *
     * Warning: uses exit
     *
     * @param string $url Desired URL
     * @param string $base_uri Base URI (optional)
     * @param Link|null $link
     * @param string|array $headers A list of headers to send before redirection
     */
    public static function redirect($url, $base_uri = __PS_BASE_URI__, Link $link = null, $headers = null)
    {
        if (!$link) {
            $link = Context::getContext()->link;
        }

        if (!preg_match('@^https?://@i', $url) && $link) {
            if (strpos($url, $base_uri) === 0) {
                $url = substr($url, strlen($base_uri));
            }
            if (strpos($url, 'index.php?controller=') === 0) {
                $url = substr($url, strlen('index.php?controller='));
                if (Configuration::get('PS_REWRITING_SETTINGS')) {
                    $url = Tools::strReplaceFirst('&', '?', $url);
                }
            }

            $explode = explode('?', $url);
            $url = $link->getPageLink($explode[0]);
            if (isset($explode[1])) {
                $url .= '?' . $explode[1];
            }
        }

        // Send additional headers
        if ($headers) {
            if (!is_array($headers)) {
                $headers = [$headers];
            }

            foreach ($headers as $header) {
                header($header);
            }
        }

        header('Location: ' . $url);
        exit;
    }

    /**
     * Redirect user to another page (using header Location)
     *
     * Warning: uses exit
     *
     * @param string $url Desired URL
     */
    public static function redirectAdmin($url)
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Returns the available protocol for the current shop in use
     * SSL if Configuration is set on and available for the server.
     *
     * @return string
     */
    public static function getShopProtocol()
    {
        $protocol = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';

        return $protocol;
    }

    /**
     * Returns the set protocol according to configuration (http[s]).
     *
     * @param bool $use_ssl true if require ssl
     *
     * @return string (http|https)
     */
    public static function getProtocol($use_ssl = null)
    {
        return null !== $use_ssl && $use_ssl ? 'https://' : 'http://';
    }

    /**
     * Returns the <b>current</b> host used, with the protocol (http or https) if $http is true
     * This function should not be used to choose http or https domain name.
     * Use Tools::getShopDomain() or Tools::getShopDomainSsl instead.
     *
     * @param bool $http
     * @param bool $entities
     * @param bool $ignore_port
     *
     * @return string host
     */
    public static function getHttpHost($http = false, $entities = false, $ignore_port = false)
    {
        $httpHost = '';
        if (array_key_exists('HTTP_HOST', $_SERVER)) {
            $httpHost = $_SERVER['HTTP_HOST'];
        }

        $host = (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $httpHost);
        if ($ignore_port && $pos = strpos($host, ':')) {
            $host = substr($host, 0, $pos);
        }
        if ($entities) {
            $host = htmlspecialchars($host, ENT_COMPAT, 'UTF-8');
        }
        if ($http) {
            $host = static::getProtocol((bool) Configuration::get('PS_SSL_ENABLED')) . $host;
        }

        return $host;
    }

    /**
     * Returns domain name according to configuration and ignoring ssl.
     *
     * @param bool $http if true, return domain name with protocol
     * @param bool $entities if true, convert special chars to HTML entities
     *
     * @return string domain
     */
    public static function getShopDomain($http = false, $entities = false)
    {
        if (!$domain = ShopUrl::getMainShopDomain()) {
            $domain = Tools::getHttpHost();
        }
        if ($entities) {
            $domain = htmlspecialchars($domain, ENT_COMPAT, 'UTF-8');
        }
        if ($http) {
            $domain = 'http://' . $domain;
        }

        return $domain;
    }

    /**
     * Returns domain name according to configuration and depending on ssl activation.
     *
     * @param bool $http if true, return domain name with protocol
     * @param bool $entities if true, convert special chars to HTML entities
     *
     * @return string domain
     */
    public static function getShopDomainSsl($http = false, $entities = false)
    {
        if (!$domain = ShopUrl::getMainShopDomainSSL()) {
            $domain = Tools::getHttpHost();
        }
        if ($entities) {
            $domain = htmlspecialchars($domain, ENT_COMPAT, 'UTF-8');
        }
        if ($http) {
            $domain = static::getProtocol((bool) Configuration::get('PS_SSL_ENABLED')) . $domain;
        }

        return $domain;
    }

    /**
     * Get the server variable SERVER_NAME.
     * Relies on $_SERVER
     *
     * @return string server name
     */
    public static function getServerName()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_SERVER']) && $_SERVER['HTTP_X_FORWARDED_SERVER']) {
            return $_SERVER['HTTP_X_FORWARDED_SERVER'];
        }

        return $_SERVER['SERVER_NAME'];
    }

    /**
     * Get the server variable REMOTE_ADDR, or the first ip of HTTP_X_FORWARDED_FOR (when using proxy).
     *
     * @return string $remote_addr ip of client
     */
    public static function getRemoteAddr()
    {
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
        } else {
            $headers = $_SERVER;
        }

        if (array_key_exists('X-Forwarded-For', $headers)) {
            $_SERVER['HTTP_X_FORWARDED_FOR'] = $headers['X-Forwarded-For'];
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && (!isset($_SERVER['REMOTE_ADDR'])
            || preg_match('/^127\..*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^172\.(1[6-9]|2\d|30|31)\..*/i', trim($_SERVER['REMOTE_ADDR']))
            || preg_match('/^192\.168\.*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^10\..*/i', trim($_SERVER['REMOTE_ADDR'])))) {
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
                $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

                return $ips[0];
            } else {
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    /**
     * Check if the current page use SSL connection on not.
     * Relies on $_SERVER global being filled
     *
     * @return bool true if SSL is used
     */
    public static function usingSecureMode()
    {
        if (isset($_SERVER['HTTPS'])) {
            return in_array(Tools::strtolower($_SERVER['HTTPS']), [1, 'on']);
        }
        // $_SERVER['SSL'] exists only in some specific configuration
        if (isset($_SERVER['SSL'])) {
            return in_array(Tools::strtolower($_SERVER['SSL']), [1, 'on']);
        }
        // $_SERVER['REDIRECT_HTTPS'] exists only in some specific configuration
        if (isset($_SERVER['REDIRECT_HTTPS'])) {
            return in_array(Tools::strtolower($_SERVER['REDIRECT_HTTPS']), [1, 'on']);
        }
        if (isset($_SERVER['HTTP_SSL'])) {
            return in_array(Tools::strtolower($_SERVER['HTTP_SSL']), [1, 'on']);
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            return Tools::strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https';
        }

        return false;
    }

    /**
     * Get the current url prefix protocol (https/http).
     *
     * @return string protocol
     */
    public static function getCurrentUrlProtocolPrefix()
    {
        if (Tools::usingSecureMode()) {
            return 'https://';
        } else {
            return 'http://';
        }
    }

    /**
     * Get the current url
     *
     * @return string current url
     */
    public static function getCurrentUrl(): string
    {
        return Tools::getCurrentUrlProtocolPrefix() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * Returns a safe URL referrer.
     *
     * @param string $referrer URL referrer
     *
     * @return string secured referrer
     */
    public static function secureReferrer($referrer)
    {
        if (static::urlBelongsToShop($referrer)) {
            return $referrer;
        }

        return __PS_BASE_URI__;
    }

    /**
     * Indicates if the provided URL belongs to this shop (relative urls count as belonging to the shop).
     *
     * @param string $url
     *
     * @return bool
     */
    public static function urlBelongsToShop($url)
    {
        $urlHost = Tools::extractHost($url);

        return empty($urlHost) || $urlHost === Tools::getServerName();
    }

    /**
     * Safely extracts the host part from an URL.
     *
     * @param string $url
     *
     * @return string
     */
    public static function extractHost($url)
    {
        $parsed = parse_url($url);
        if (!is_array($parsed)) {
            return $url;
        }
        if (empty($parsed['host']) || empty($parsed['scheme'])) {
            return '';
        }

        return $parsed['host'];
    }

    /**
     * Get a value from $_POST / $_GET
     * if unavailable, take a default value.
     *
     * @param string $key Value key
     * @param mixed $default_value (optional)
     *
     * @return mixed Value
     */
    public static function getValue($key, $default_value = false)
    {
        if (empty($key) || !is_string($key)) {
            return false;
        }

        if (getenv('kernel.environment') === 'test' && self::$request instanceof Request) {
            $value = self::$request->request->get($key, self::$request->query->get($key, $default_value));
        } elseif (isset($_POST[$key]) || isset($_GET[$key])) {
            $value = isset($_POST[$key]) ? $_POST[$key] : $_GET[$key];
        } elseif (isset(static::$fallbackParameters[$key])) {
            $value = static::$fallbackParameters[$key];
        }

        if (!isset($value)) {
            $value = $default_value;
        }

        if (is_string($value)) {
            return urldecode(preg_replace('/((\%5C0+)|(\%00+))/i', '', urlencode($value)));
        }

        return $value;
    }

    /**
     * Get all values from $_POST/$_GET.
     *
     * @return mixed
     */
    public static function getAllValues()
    {
        return $_POST + $_GET;
    }

    /**
     * Checks if a key exists either in $_POST or $_GET.
     *
     * @param string $key
     *
     * @return bool
     */
    public static function getIsset($key)
    {
        if (!is_string($key)) {
            return false;
        }

        return isset($_POST[$key]) || isset($_GET[$key]);
    }

    /**
     * Change language in cookie while clicking on a flag.
     *
     * @return string iso code
     */
    public static function setCookieLanguage($cookie = null)
    {
        if (!$cookie) {
            $cookie = Context::getContext()->cookie;
        }
        /* If language does not exist or is disabled, erase it */
        if ($cookie->id_lang) {
            $lang = new Language((int) $cookie->id_lang);
            if (!Validate::isLoadedObject($lang) || !$lang->active || !$lang->isAssociatedToShop()) {
                $cookie->id_lang = null;
            }
        }

        if (!Configuration::get('PS_DETECT_LANG')) {
            unset($cookie->detect_language);
        }

        /* Automatically detect language if not already defined, detect_language is set in Cookie::update */
        if (
            !Tools::getValue('isolang') &&
            !Tools::getValue('id_lang') &&
            (!$cookie->id_lang || isset($cookie->detect_language))
            && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])
        ) {
            $array = explode(',', Tools::strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']));
            $string = $array[0];

            if (Validate::isLanguageCode($string)) {
                $lang = Language::getLanguageByIETFCode($string);
                if (Validate::isLoadedObject($lang) && $lang->active && $lang->isAssociatedToShop()) {
                    Context::getContext()->language = $lang;
                    $cookie->id_lang = (int) $lang->id;
                }
            }
        }

        if (isset($cookie->detect_language)) {
            unset($cookie->detect_language);
        }

        /* If language file not present, you must use default language file */
        if (!$cookie->id_lang || !Validate::isUnsignedId($cookie->id_lang)) {
            $cookie->id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        }

        $iso = Language::getIsoById((int) $cookie->id_lang);
        @include_once _PS_THEME_DIR_ . 'lang/' . $iso . '.php';

        return $iso;
    }

    /**
     * If necessary change cookie language ID and context language.
     *
     * @param Context|null $context
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function switchLanguage(Context $context = null)
    {
        if (null === $context) {
            $context = Context::getContext();
        }

        // On PrestaShop installations Dispatcher::__construct() gets called (and so Tools::switchLanguage())
        // Stop in this case by checking the cookie
        if (!isset($context->cookie)) {
            return;
        }

        if (
            ($iso = Tools::getValue('isolang')) &&
            Validate::isLanguageIsoCode($iso) &&
            ($id_lang = (int) Language::getIdByIso($iso))
        ) {
            $_GET['id_lang'] = $id_lang;
        }

        // Only switch if new ID is different from old ID
        $newLanguageId = (int) Tools::getValue('id_lang');

        if (
            Validate::isUnsignedId($newLanguageId) &&
            $newLanguageId !== 0 &&
            $context->cookie->id_lang !== $newLanguageId
        ) {
            $context->cookie->id_lang = $newLanguageId;
            $language = new Language($newLanguageId);
            if (Validate::isLoadedObject($language) && $language->active && $language->isAssociatedToShop()) {
                $context->language = $language;
            }
        }

        Tools::setCookieLanguage($context->cookie);
    }

    public static function getCountry($address = null)
    {
        $countryId = Tools::getValue('id_country');
        if (
            Validate::isInt($countryId)
            && (int) $countryId > 0
            && !empty(Country::getIsoById((int) $countryId))
        ) {
            return (int) $countryId;
        }

        if (!empty($address->id_country) && (int) $address->id_country > 0) {
            return (int) $address->id_country;
        }

        return (int) Configuration::get('PS_COUNTRY_DEFAULT');
    }

    /**
     * Set cookie currency from POST or default currency.
     *
     * @return Currency|array
     */
    public static function setCurrency($cookie)
    {
        if (Tools::isSubmit('SubmitCurrency') && ($id_currency = Tools::getValue('id_currency'))) {
            /** @var Currency $currency */
            $currency = Currency::getCurrencyInstance((int) $id_currency);
            if (is_object($currency) && $currency->id && !$currency->deleted && $currency->isAssociatedToShop()) {
                $cookie->id_currency = (int) $currency->id;
            }
        }

        $currency = null;
        if ((int) $cookie->id_currency) {
            $currency = Currency::getCurrencyInstance((int) $cookie->id_currency);
        }
        if (!Validate::isLoadedObject($currency) || (bool) $currency->deleted || !(bool) $currency->active) {
            $currency = Currency::getCurrencyInstance(Currency::getDefaultCurrencyId());
        }

        $cookie->id_currency = (int) $currency->id;
        if ($currency->isAssociatedToShop()) {
            return $currency;
        } else {
            // get currency from context
            $currency = Shop::getEntityIds('currency', Context::getContext()->shop->id, true, true);
            if (isset($currency[0]) && $currency[0]['id_currency']) {
                $cookie->id_currency = $currency[0]['id_currency'];

                return Currency::getCurrencyInstance((int) $cookie->id_currency);
            }
        }

        return $currency;
    }

    /**
     * Return current locale
     *
     * @param Context $context
     *
     * @return Locale
     *
     * @throws Exception
     */
    public static function getContextLocale(Context $context)
    {
        $locale = $context->getCurrentLocale();
        if (null !== $locale) {
            return $locale;
        }

        $containerFinder = new ContainerFinder($context);
        $container = $containerFinder->getContainer();
        if (null === $context->container) {
            $context->container = $container;
        }

        /** @var LocaleRepository $localeRepository */
        $localeRepository = $container->get(self::SERVICE_LOCALE_REPOSITORY);
        $locale = $localeRepository->getLocale(
            $context->language->getLocale()
        );

        return $locale;
    }

    public static function displayPriceSmarty($params, &$smarty)
    {
        $context = Context::getContext();
        $locale = static::getContextLocale($context);
        if (array_key_exists('currency', $params)) {
            $currency = Currency::getCurrencyInstance((int) $params['currency']);
            if (Validate::isLoadedObject($currency)) {
                return $locale->formatPrice($params['price'], $currency->iso_code);
            }
        }

        return $locale->formatPrice($params['price'], $context->currency->iso_code);
    }

    /**
     * Return price converted.
     *
     * @param float|null $price Product price
     * @param array|Currency|int|null $currency Current currency object
     * @param bool $to_currency convert to currency or from currency to default currency
     * @param Context|null $context
     *
     * @return float|null Price
     */
    public static function convertPrice($price, $currency = null, $to_currency = true, Context $context = null)
    {
        $default_currency = Currency::getDefaultCurrencyId();

        if (!$context) {
            $context = Context::getContext();
        }
        if ($currency === null) {
            $currency = $context->currency;
        } elseif (is_numeric($currency)) {
            $currency = Currency::getCurrencyInstance($currency);
        }

        $c_id = (is_array($currency) ? $currency['id_currency'] : $currency->id);
        $c_rate = (is_array($currency) ? $currency['conversion_rate'] : $currency->conversion_rate);

        if ($c_id != $default_currency) {
            if ($to_currency) {
                $price *= $c_rate;
            } else {
                $price /= $c_rate;
            }
        }

        return $price;
    }

    /**
     * Convert amount from a currency to an other currency automatically.
     *
     * @param float $amount
     * @param Currency $currency_from if null we used the default currency
     * @param Currency $currency_to if null we used the default currency
     */
    public static function convertPriceFull($amount, Currency $currency_from = null, Currency $currency_to = null)
    {
        if ($currency_from == $currency_to) {
            return $amount;
        }

        if ($currency_from === null) {
            $currency_from = Currency::getDefaultCurrency();
        }

        if ($currency_to === null) {
            $currency_to = Currency::getDefaultCurrency();
        }

        if ($currency_from->id == Currency::getDefaultCurrencyId()) {
            $amount *= $currency_to->conversion_rate;
        } else {
            $conversion_rate = ($currency_from->conversion_rate == 0 ? 1 : $currency_from->conversion_rate);
            // Convert amount to default currency (using the old currency rate)
            $amount = $amount / $conversion_rate;
            // Convert to new currency
            $amount *= $currency_to->conversion_rate;
        }

        return Tools::ps_round($amount, Context::getContext()->getComputingPrecision());
    }

    /**
     * Display date regarding to language preferences.
     *
     * @param array $params Date, format...
     * @param object $smarty Smarty object for language preferences
     *
     * @return string Date
     */
    public static function dateFormat($params, &$smarty)
    {
        return Tools::displayDate($params['date'], (isset($params['full']) ? $params['full'] : false));
    }

    /**
     * Display date regarding to language preferences.
     *
     * @param string $date Date to display format UNIX
     * @param bool $full With time or not (optional)
     *
     * @return string Date
     */
    public static function displayDate($date, $full = false)
    {
        if (!$date || !($time = strtotime($date))) {
            return $date;
        }

        if ($date == '0000-00-00 00:00:00' || $date == '0000-00-00') {
            return '';
        }

        if (!Validate::isDate($date) || !Validate::isBool($full)) {
            throw new PrestaShopException('Invalid date');
        }

        $context = Context::getContext();
        $date_format = ($full ? $context->language->date_format_full : $context->language->date_format_lite);

        return date($date_format, $time);
    }

    /**
     * Get localized date format.
     *
     * @return string Date format
     */
    public static function getDateFormat()
    {
        $format = Context::getContext()->language->date_format_lite;
        $search = ['d', 'm', 'Y'];
        $replace = [
            Context::getContext()->getTranslator()->trans('DD', [], 'Shop.Forms.Help'),
            Context::getContext()->getTranslator()->trans('MM', [], 'Shop.Forms.Help'),
            Context::getContext()->getTranslator()->trans('YYYY', [], 'Shop.Forms.Help'),
        ];
        $format = str_replace($search, $replace, $format);

        return $format;
    }

    /**
     * Get formatted date.
     *
     * @param string $date_str Date string
     * @param bool $full With time or not (optional)
     *
     * @return string Formatted date
     */
    public static function formatDateStr($date_str, $full = false)
    {
        $time = strtotime($date_str);
        $context = Context::getContext();
        $date_format = ($full ? $context->language->date_format_full : $context->language->date_format_lite);
        $date = date($date_format, $time);

        return $date;
    }

    /**
     * Sanitize a string.
     *
     * @param string $string String to sanitize
     * @param bool $html String contains HTML or not (optional)
     *
     * @return string Sanitized string
     */
    public static function safeOutput($string, $html = false)
    {
        if (!$html) {
            $string = strip_tags($string);
        }

        return @Tools::htmlentitiesUTF8($string, ENT_QUOTES);
    }

    public static function htmlentitiesUTF8($string, $type = ENT_QUOTES)
    {
        if (is_array($string)) {
            return array_map(['Tools', 'htmlentitiesUTF8'], $string);
        }

        return htmlentities((string) $string, $type, 'utf-8');
    }

    public static function htmlentitiesDecodeUTF8($string)
    {
        if (is_array($string)) {
            $string = array_map(['Tools', 'htmlentitiesDecodeUTF8'], $string);

            return (string) array_shift($string);
        }

        return html_entity_decode((string) $string, ENT_QUOTES, 'utf-8');
    }

    /**
     * Delete directory and subdirectories.
     *
     * @param string $dirname Directory name
     */
    public static function deleteDirectory($dirname, $delete_self = true)
    {
        $dirname = rtrim($dirname, '/') . '/';
        if (file_exists($dirname)) {
            if ($files = scandir($dirname, SCANDIR_SORT_NONE)) {
                foreach ($files as $file) {
                    if ($file != '.' && $file != '..' && $file != '.svn') {
                        if (is_dir($dirname . $file)) {
                            Tools::deleteDirectory($dirname . $file);
                        } elseif (file_exists($dirname . $file)) {
                            unlink($dirname . $file);
                        }
                    }
                }

                if ($delete_self && file_exists($dirname)) {
                    if (!rmdir($dirname)) {
                        return false;
                    }
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Delete file.
     *
     * @param string $file File path
     * @param array $exclude_files Excluded files
     *
     * @return bool
     */
    public static function deleteFile($file, $exclude_files = [])
    {
        if (!is_array($exclude_files)) {
            $exclude_files = [$exclude_files];
        }

        if (file_exists($file) && is_file($file) && array_search(basename($file), $exclude_files) === false) {
            return unlink($file);
        }

        return false;
    }

    /**
     * Clear XML cache folder.
     */
    public static function clearXMLCache()
    {
        foreach (scandir(_PS_ROOT_DIR_ . '/config/xml', SCANDIR_SORT_NONE) as $file) {
            $path_info = pathinfo($file, PATHINFO_EXTENSION);
            if (($path_info == 'xml') && ($file != 'default.xml')) {
                self::deleteFile(_PS_ROOT_DIR_ . '/config/xml/' . $file);
            }
        }
    }

    /**
     * Depending on _PS_MODE_DEV_ throws an exception or returns a error message.
     *
     * @param string|null $errorMessage Error message (defaults to "Fatal error")
     * @param bool $htmlentities DEPRECATED since 1.7.4.0
     * @param Context|null $context DEPRECATED since 1.7.4.0
     *
     * @return string
     *
     * @throws PrestaShopException If _PS_MODE_DEV_ is enabled
     */
    public static function displayError($errorMessage = null, $htmlentities = null, Context $context = null)
    {
        header('HTTP/1.1 500 Internal Server Error', true, 500);
        if (null !== $htmlentities) {
            self::displayParameterAsDeprecated('htmlentities');
        }
        if (null !== $context) {
            self::displayParameterAsDeprecated('context');
        }

        if (null === $errorMessage) {
            $errorMessage = Context::getContext()
                ->getTranslator()
                ->trans('Fatal error', [], 'Admin.Notifications.Error');
        }

        if (_PS_MODE_DEV_) {
            throw new PrestaShopException($errorMessage);
        }

        /* @phpstan-ignore-next-line */
        return $errorMessage;
    }

    /**
     * Display an error with detailed object.
     *
     * @param mixed $object
     * @param bool $kill
     *
     * @return mixed
     */
    public static function dieObject($object, $kill = true)
    {
        dump($object);

        if ($kill) {
            die('END');
        }

        return $object;
    }

    public static function debug_backtrace($start = 0, $limit = null)
    {
        $backtrace = debug_backtrace();
        array_shift($backtrace);
        for ($i = 0; $i < $start; ++$i) {
            array_shift($backtrace);
        }

        echo '
        <div style="margin:10px;padding:10px;border:1px solid #666666">
            <ul>';
        $i = 0;
        foreach ($backtrace as $id => $trace) {
            if ((int) $limit && (++$i > $limit)) {
                break;
            }
            $relative_file = (isset($trace['file'])) ? 'in /' . ltrim(str_replace([_PS_ROOT_DIR_, '\\'], ['', '/'], $trace['file']), '/') : '';
            $current_line = (isset($trace['line'])) ? ':' . $trace['line'] : '';

            echo '<li>
                <b>' . ((isset($trace['class'])) ? $trace['class'] : '') . ((isset($trace['type'])) ? $trace['type'] : '') . $trace['function'] . '</b>
                ' . $relative_file . $current_line . '
            </li>';
        }
        echo '</ul>
        </div>';
    }

    /**
     * Prints object information into error log.
     *
     * @see error_log()
     *
     * @param mixed $object
     * @param int|null $message_type
     * @param string|null $destination
     * @param string|null $extra_headers
     *
     * @return bool
     */
    public static function error_log($object, $message_type = null, $destination = null, $extra_headers = null)
    {
        return error_log(print_r($object, true), $message_type, $destination, $extra_headers);
    }

    /**
     * Check if submit has been posted.
     *
     * @param string $submit submit name
     */
    public static function isSubmit($submit)
    {
        return
            isset($_POST[$submit]) || isset($_POST[$submit . '_x']) || isset($_POST[$submit . '_y'])
            || isset($_GET[$submit]) || isset($_GET[$submit . '_x']) || isset($_GET[$submit . '_y']);
    }

    /**
     * Hash password.
     *
     * @param string $passwd String to has
     *
     * @return string Hashed password
     *
     * @since 1.7.0
     */
    public static function hash($passwd)
    {
        return (new Hashing())->hash($passwd, _COOKIE_KEY_);
    }

    /**
     * Hash data string.
     *
     * @param string $data String to encrypt
     *
     * @return string Hashed IV
     *
     * @since 1.7.0
     */
    public static function hashIV($data)
    {
        return (new Hashing())->hash($data, _COOKIE_IV_);
    }

    /**
     * Get token to prevent CSRF.
     *
     * @param bool $page
     * @param Context|null $context
     *
     * @return string
     */
    public static function getToken($page = true, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        if ($page === true) {
            return Tools::hash($context->customer->id . $context->customer->passwd . $_SERVER['SCRIPT_NAME']);
        } else {
            return Tools::hash($context->customer->id . $context->customer->passwd . $page);
        }
    }

    /**
     * Tokenize a string.
     *
     * @param string $string String to encrypt
     *
     * @return string|bool false if given string is empty
     */
    public static function getAdminToken($string)
    {
        return !empty($string) ? Tools::hash($string) : false;
    }

    /**
     * @param string $tab
     * @param Context $context
     *
     * @return bool|string
     */
    public static function getAdminTokenLite($tab, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        return Tools::getAdminToken($tab . (int) Tab::getIdFromClassName($tab) . (int) $context->employee->id);
    }

    /**
     * @param array $params
     *
     * @return bool|string
     */
    public static function getAdminTokenLiteSmarty($params)
    {
        $context = Context::getContext();

        return Tools::getAdminToken($params['tab'] . (int) Tab::getIdFromClassName($params['tab']) . (int) $context->employee->id);
    }

    /**
     * Get a valid URL to use from BackOffice.
     *
     * @param string $url An URL to use in BackOffice
     * @param bool $entities Set to true to use htmlentities function on URL param
     *
     * @return string
     */
    public static function getAdminUrl($url = null, $entities = false)
    {
        $link = Tools::getHttpHost(true) . __PS_BASE_URI__;

        if (isset($url)) {
            $link .= ($entities ? Tools::htmlentitiesUTF8($url) : $url);
        }

        return $link;
    }

    /**
     * Get a valid image URL to use from BackOffice.
     *
     * @param string $image Image name
     * @param bool $entities Set to true to use htmlentities function on image param
     *
     * @return string
     */
    public static function getAdminImageUrl($image = null, $entities = false)
    {
        return Tools::getAdminUrl(basename(_PS_IMG_DIR_) . '/' . $image, $entities);
    }

    /**
     * Return a friendly url made from the provided string
     * If the mbstring library is available, the output is the same as the js function of the same name.
     *
     * @param string $str
     *
     * @return string|bool
     */
    public static function str2url($str)
    {
        return (new StringModifier())->str2url((string) $str);
    }

    /**
     * Replace all accented chars by their equivalent non accented chars.
     *
     * @param string $str
     *
     * @return string
     */
    public static function replaceAccentedChars($str)
    {
        return (new StringModifier())->replaceAccentedChars($str);
    }

    /**
     * Truncate strings.
     *
     * @param string $str
     * @param int $max_length Max length
     * @param string $suffix Suffix optional
     *
     * @return string $str truncated
     */
    /* CAUTION : Use it only on module hookEvents.
    ** For other purposes use the smarty function instead */
    public static function truncate($str, $max_length, $suffix = '...')
    {
        if (Tools::strlen($str) <= $max_length) {
            return $str;
        }
        $str = utf8_decode($str);

        return utf8_encode(substr($str, 0, $max_length - Tools::strlen($suffix)) . $suffix);
    }

    /*Copied from CakePHP String utility file*/
    public static function truncateString($text, $length = 120, $options = [])
    {
        $default = [
            'ellipsis' => '...', 'exact' => true, 'html' => true,
        ];

        $options = array_merge($default, $options);
        extract($options);
        if (isset($html)) {
            /* @var bool $exact */
            /* @var bool $html */
            if (Tools::strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
                return $text;
            }

            $total_length = Tools::strlen(strip_tags($ellipsis ?? ''));
            $open_tags = [];
            $truncate = '';
            preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);

            foreach ($tags as $tag) {
                if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])) {
                    if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
                        array_unshift($open_tags, $tag[2]);
                    } elseif (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $close_tag)) {
                        $pos = array_search($close_tag[1], $open_tags);
                        if ($pos !== false) {
                            array_splice($open_tags, $pos, 1);
                        }
                    }
                }
                $truncate .= $tag[1];
                $content_length = Tools::strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));

                if ($content_length + $total_length > $length) {
                    $left = $length - $total_length;
                    $entities_length = 0;

                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {
                        foreach ($entities[0] as $entity) {
                            if ($entity[1] + 1 - $entities_length <= $left) {
                                --$left;
                                $entities_length += Tools::strlen($entity[0]);
                            } else {
                                break;
                            }
                        }
                    }

                    $truncate .= Tools::substr($tag[3], 0, $left + $entities_length);

                    break;
                } else {
                    $truncate .= $tag[3];
                    $total_length += $content_length;
                }

                if ($total_length >= $length) {
                    break;
                }
            }
        } else {
            if (Tools::strlen($text) <= $length) {
                return $text;
            }

            $truncate = Tools::substr($text, 0, $length - Tools::strlen($ellipsis ?? ''));
        }

        if (!isset($exact) || !$exact) {
            $spacepos = Tools::strrpos($truncate ?? '', ' ');
            if (isset($html)) {
                $truncate_check = Tools::substr($truncate ?? '', 0, $spacepos);
                $last_open_tag = Tools::strrpos($truncate_check, '<');
                $last_close_tag = Tools::strrpos($truncate_check, '>');

                if ($last_open_tag > $last_close_tag) {
                    preg_match_all('/<[\w]+[^>]*>/s', $truncate ?? '', $last_tag_matches);
                    $last_tag = array_pop($last_tag_matches[0]);
                    $spacepos = Tools::strrpos($truncate, $last_tag) + Tools::strlen($last_tag);
                }

                $bits = Tools::substr($truncate ?? '', $spacepos);
                preg_match_all('/<\/([a-z]+)>/', $bits, $dropped_tags, PREG_SET_ORDER);

                if (!empty($dropped_tags)) {
                    if (!empty($open_tags)) {
                        foreach ($dropped_tags as $closing_tag) {
                            if (!in_array($closing_tag[1], $open_tags)) {
                                array_unshift($open_tags, $closing_tag[1]);
                            }
                        }
                    } else {
                        foreach ($dropped_tags as $closing_tag) {
                            $open_tags[] = $closing_tag[1];
                        }
                    }
                }
            }

            $truncate = Tools::substr($truncate, 0, $spacepos);
        }

        $truncate .= ($ellipsis ?? '');

        if (isset($html)) {
            $open_tags = $open_tags ?? [];
            foreach ($open_tags as $tag) {
                $truncate .= '</' . $tag . '>';
            }
        }

        return $truncate;
    }

    public static function normalizeDirectory($directory)
    {
        return rtrim($directory, '/\\') . DIRECTORY_SEPARATOR;
    }

    /**
     * Generate date form.
     *
     * @return array $tab html data with 3 cells :['days'], ['months'], ['years']
     */
    public static function dateYears()
    {
        $tab = [];
        for ($i = date('Y'); $i >= 1900; --$i) {
            $tab[] = $i;
        }

        return $tab;
    }

    public static function dateDays()
    {
        $tab = [];
        for ($i = 1; $i != 32; ++$i) {
            $tab[] = $i;
        }

        return $tab;
    }

    public static function dateMonths()
    {
        $tab = [];
        for ($i = 1; $i != 13; ++$i) {
            $tab[$i] = date('F', mktime(0, 0, 0, $i, (int) date('m'), (int) date('Y')));
        }

        return $tab;
    }

    public static function hourGenerate($hours, $minutes, $seconds)
    {
        return implode(':', [$hours, $minutes, $seconds]);
    }

    public static function dateFrom($date)
    {
        $tab = explode(' ', $date);
        if (!isset($tab[1])) {
            $date .= ' ' . Tools::hourGenerate(0, 0, 0);
        }

        return $date;
    }

    public static function dateTo($date)
    {
        $tab = explode(' ', $date);
        if (!isset($tab[1])) {
            $date .= ' ' . Tools::hourGenerate(23, 59, 59);
        }

        return $date;
    }

    public static function strtolower($str)
    {
        if (null === $str || is_array($str)) {
            return false;
        }

        return mb_strtolower($str, 'UTF-8');
    }

    public static function strlen($str, $encoding = 'UTF-8')
    {
        if (null === $str || is_array($str)) {
            return false;
        }

        $str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');

        return mb_strlen($str, $encoding);
    }

    public static function strtoupper($str)
    {
        if (is_array($str)) {
            return false;
        }

        return mb_strtoupper($str, 'utf-8');
    }

    public static function substr($str, $start, $length = false, $encoding = 'UTF-8')
    {
        if (is_array($str)) {
            return false;
        }

        return mb_substr($str, (int) $start, ($length === false ? null : (int) $length), $encoding);
    }

    public static function strpos($str, $find, $offset = 0, $encoding = 'UTF-8')
    {
        return mb_strpos($str, $find, $offset, $encoding);
    }

    public static function strrpos($str, $find, $offset = 0, $encoding = 'UTF-8')
    {
        return mb_strrpos($str, $find, $offset, $encoding);
    }

    public static function ucfirst($str)
    {
        return Tools::strtoupper(Tools::substr($str, 0, 1)) . Tools::substr($str, 1);
    }

    public static function ucwords($str)
    {
        return mb_convert_case($str, MB_CASE_TITLE);
    }

    public static function orderbyPrice(&$array, $order_way)
    {
        foreach ($array as &$row) {
            $row['price_tmp'] = (float) Product::getPriceStatic($row['id_product'], true, ((isset($row['id_product_attribute']) && !empty($row['id_product_attribute'])) ? (int) $row['id_product_attribute'] : null), 2);
        }
        unset($row);

        if (Tools::strtolower($order_way) == 'desc') {
            uasort($array, 'cmpPriceDesc');
        } else {
            uasort($array, 'cmpPriceAsc');
        }
        foreach ($array as &$row) {
            unset($row['price_tmp']);
        }
    }

    public static function iconv($from, $to, $string)
    {
        if (function_exists('iconv')) {
            return iconv($from, $to . '//TRANSLIT', str_replace('¥', '&yen;', str_replace('£', '&pound;', str_replace('€', '&euro;', $string))));
        }

        return html_entity_decode(htmlentities($string, ENT_NOQUOTES, $from), ENT_NOQUOTES, $to);
    }

    public static function isEmpty($field)
    {
        return $field === '' || $field === null;
    }

    /**
     * returns the rounded value of $value to specified precision, according to your configuration;.
     *
     * @note : PHP 5.3.0 introduce a 3rd parameter mode in round function
     *
     * @param float $value
     * @param int $precision
     *
     * @return float
     */
    public static function ps_round($value, $precision = 0, $round_mode = null)
    {
        if ($round_mode === null) {
            if (Tools::$round_mode == null) {
                Tools::$round_mode = (int) Configuration::get('PS_PRICE_ROUND_MODE');
            }

            $round_mode = Tools::$round_mode;
        }

        switch ($round_mode) {
            case PS_ROUND_UP:
                return Tools::ceilf($value, $precision);
            case PS_ROUND_DOWN:
                return Tools::floorf($value, $precision);
            case PS_ROUND_HALF_DOWN:
            case PS_ROUND_HALF_EVEN:
            case PS_ROUND_HALF_ODD:
                return Tools::math_round($value, $precision, $round_mode);
            case PS_ROUND_HALF_UP:
            default:
                return Tools::math_round($value, $precision, PS_ROUND_HALF_UP);
        }
    }

    /**
     * @param int|float $value
     * @param int|float $places
     * @param int<2,5> $mode (PS_ROUND_HALF_UP|PS_ROUND_HALF_DOWN|PS_ROUND_HALF_EVEN|PS_ROUND_HALF_ODD)
     *
     * @return false|float
     */
    public static function math_round($value, $places, $mode = PS_ROUND_HALF_UP)
    {
        //If PHP_ROUND_HALF_UP exist (PHP 5.3) use it and pass correct mode value (PrestaShop define - 1)
        if (defined('PHP_ROUND_HALF_UP')) {
            return round($value, $places, $mode - 1);
        }

        $precision_places = 14 - floor(log10(abs($value)));
        $f1 = 10.0 ** (float) abs($places);

        /* If the decimal precision guaranteed by FP arithmetic is higher than
        * the requested places BUT is small enough to make sure a non-zero value
        * is returned, pre-round the result to the precision */
        if ($precision_places > $places && $precision_places - $places < 15) {
            $f2 = 10.0 ** (float) abs($precision_places);

            if ($precision_places >= 0) {
                $tmp_value = $value * $f2;
            } else {
                $tmp_value = $value / $f2;
            }

            /* preround the result (tmp_value will always be something * 1e14,
            * thus never larger than 1e15 here) */
            $tmp_value = Tools::round_helper($tmp_value, $mode);
            /* now correctly move the decimal point */
            $f2 = 10.0 ** (float) abs($places - $precision_places);
            /* because places < precision_places */
            $tmp_value = $tmp_value / $f2;
        } else {
            /* adjust the value */
            if ($places >= 0) {
                $tmp_value = $value * $f1;
            } else {
                $tmp_value = $value / $f1;
            }

            /* This value is beyond our precision, so rounding it is pointless */
            if (abs($tmp_value) >= 1e15) {
                return $value;
            }
        }

        /* round the temp value */
        $tmp_value = Tools::round_helper($tmp_value, $mode);

        /* see if it makes sense to use simple division to round the value */
        if (abs($places) < 23) {
            if ($places > 0) {
                $tmp_value /= $f1;
            } else {
                $tmp_value *= $f1;
            }
        }

        return $tmp_value;
    }

    /**
     * @param float $value
     * @param int $mode
     *
     * @return float
     */
    public static function round_helper($value, $mode)
    {
        if ($value >= 0.0) {
            $tmp_value = floor($value + 0.5);

            if (
                ($mode == PS_ROUND_HALF_DOWN && $value == (-0.5 + $tmp_value)) ||
                ($mode == PS_ROUND_HALF_EVEN && $value == (0.5 + 2 * floor($tmp_value / 2.0))) ||
                ($mode == PS_ROUND_HALF_ODD && $value == (0.5 + 2 * floor($tmp_value / 2.0) - 1.0))
            ) {
                $tmp_value = $tmp_value - 1.0;
            }
        } else {
            $tmp_value = ceil($value - 0.5);

            if (
                ($mode == PS_ROUND_HALF_DOWN && $value == (0.5 + $tmp_value)) ||
                ($mode == PS_ROUND_HALF_EVEN && $value == (-0.5 + 2 * ceil($tmp_value / 2.0))) ||
                ($mode == PS_ROUND_HALF_ODD && $value == (-0.5 + 2 * ceil($tmp_value / 2.0) + 1.0))
            ) {
                $tmp_value = $tmp_value + 1.0;
            }
        }

        return $tmp_value;
    }

    /**
     * Returns the rounded value up of $value to specified precision.
     *
     * @param float $value
     * @param int $precision
     *
     * @return float
     */
    public static function ceilf($value, $precision = 0)
    {
        $precision_factor = $precision == 0 ? 1 : 10 ** $precision;
        $tmp = $value * $precision_factor;
        $tmp2 = (string) $tmp;
        // If the current value has already the desired precision
        if (strpos($tmp2, '.') === false) {
            return $value;
        }
        if ($tmp2[strlen($tmp2) - 1] == 0) {
            return $value;
        }

        return ceil($tmp) / $precision_factor;
    }

    /**
     * Returns the rounded value down of $value to specified precision.
     *
     * @param float $value
     * @param int $precision
     *
     * @return float
     */
    public static function floorf($value, $precision = 0)
    {
        $precision_factor = $precision == 0 ? 1 : 10 ** $precision;
        $tmp = $value * $precision_factor;
        $tmp2 = (string) $tmp;
        // If the current value has already the desired precision
        if (strpos($tmp2, '.') === false) {
            return $value;
        }
        if ($tmp2[strlen($tmp2) - 1] == 0) {
            return $value;
        }

        return floor($tmp) / $precision_factor;
    }

    /**
     * file_exists() wrapper with cache to speedup performance.
     *
     * @param string $filename File name
     *
     * @return bool Cached result of file_exists($filename)
     */
    public static function file_exists_cache($filename)
    {
        if (!isset(self::$file_exists_cache[$filename])) {
            self::$file_exists_cache[$filename] = file_exists($filename);
        }

        return self::$file_exists_cache[$filename];
    }

    /**
     * file_exists() wrapper with a call to clearstatcache prior.
     *
     * @param string $filename File name
     *
     * @return bool Cached result of file_exists($filename)
     */
    public static function file_exists_no_cache($filename)
    {
        clearstatcache();

        return file_exists($filename);
    }

    /**
     * Refresh local CACert file.
     */
    public static function refreshCACertFile()
    {
        if ((time() - @filemtime(_PS_CACHE_CA_CERT_FILE_) > 1296000)) {
            $stream_context = @stream_context_create(
                [
                    'http' => ['timeout' => 3],
                    'ssl' => [
                        'cafile' => CaBundle::getBundledCaBundlePath(),
                    ],
                ]
            );

            $ca_cert_content = @file_get_contents(Tools::CACERT_LOCATION, false, $stream_context);
            if (empty($ca_cert_content)) {
                $ca_cert_content = @file_get_contents(CaBundle::getBundledCaBundlePath());
            }

            if (
                preg_match('/(.*-----BEGIN CERTIFICATE-----.*-----END CERTIFICATE-----){50}$/Uims', $ca_cert_content) &&
                substr(rtrim($ca_cert_content), -1) == '-'
            ) {
                file_put_contents(_PS_CACHE_CA_CERT_FILE_, $ca_cert_content);
            }
        }
    }

    /**
     * @param string $url
     * @param int $curl_timeout
     * @param array $opts
     *
     * @return string|false
     *
     * @throws Exception
     */
    private static function file_get_contents_curl(
        $url,
        $curl_timeout,
        $opts
    ) {
        $content = false;

        if (function_exists('curl_init')) {
            Tools::refreshCACertFile();
            $curl = curl_init();

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($curl, CURLOPT_TIMEOUT, $curl_timeout);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_CAINFO, _PS_CACHE_CA_CERT_FILE_);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_MAXREDIRS, 5);

            if ($opts != null) {
                if (isset($opts['http']['method']) && Tools::strtolower($opts['http']['method']) == 'post') {
                    curl_setopt($curl, CURLOPT_POST, true);
                    if (isset($opts['http']['content'])) {
                        parse_str($opts['http']['content'], $post_data);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
                    }
                }
            }

            $content = curl_exec($curl);

            if (false === $content && _PS_MODE_DEV_) {
                $errorMessage = sprintf(
                    'file_get_contents_curl failed to download %s : (error code %d) %s',
                    $url,
                    curl_errno($curl),
                    curl_error($curl)
                );

                throw new \Exception($errorMessage);
            }

            curl_close($curl);
        }

        return $content;
    }

    private static function file_get_contents_fopen(
        $url,
        $use_include_path,
        $stream_context
    ) {
        $content = false;

        if (in_array(ini_get('allow_url_fopen'), ['On', 'on', '1'])) {
            $content = @file_get_contents($url, $use_include_path, $stream_context);
        }

        return $content;
    }

    /**
     * This method allows to get the content from either a URL or a local file.
     *
     * @param string $url the url to get the content from
     * @param bool $use_include_path second parameter of http://php.net/manual/en/function.file-get-contents.php
     * @param resource $stream_context third parameter of http://php.net/manual/en/function.file-get-contents.php
     * @param int $curl_timeout
     * @param bool $fallback whether or not to use the fallback if the main solution fails
     *
     * @return string|false false or the file content
     */
    public static function file_get_contents(
        $url,
        $use_include_path = false,
        $stream_context = null,
        $curl_timeout = 5,
        $fallback = false
    ) {
        $is_local_file = !preg_match('/^https?:\/\//', $url);
        $require_fopen = false;
        $opts = null;

        if ($stream_context) {
            $opts = stream_context_get_options($stream_context);
            if (isset($opts['http'])) {
                $require_fopen = true;
                $opts_layer = array_diff_key($opts, ['http' => null]);
                $http_layer = array_diff_key($opts['http'], ['method' => null, 'content' => null]);
                if (empty($opts_layer) && empty($http_layer)) {
                    $require_fopen = false;
                }
            }
        } elseif (!$is_local_file) {
            $stream_context = @stream_context_create(
                [
                    'http' => ['timeout' => $curl_timeout],
                    'ssl' => [
                        'verify_peer' => true,
                        'cafile' => CaBundle::getBundledCaBundlePath(),
                    ],
                ]
            );
        }

        if ($is_local_file) {
            $content = @file_get_contents($url, $use_include_path, $stream_context);
        } else {
            if ($require_fopen) {
                $content = Tools::file_get_contents_fopen($url, $use_include_path, $stream_context);
            } else {
                $content = Tools::file_get_contents_curl($url, $curl_timeout, $opts);
                if (empty($content) && $fallback) {
                    $content = Tools::file_get_contents_fopen($url, $use_include_path, $stream_context);
                }
            }
        }

        return $content;
    }

    /**
     * Create a local file from url
     * required because ZipArchive is unable to extract from remote files.
     *
     * @param string $url the remote location
     *
     * @return bool|string false if failure, else the local filename
     */
    public static function createFileFromUrl($url)
    {
        $remoteFile = fopen($url, 'rb');
        if (!$remoteFile) {
            return false;
        }
        $localFile = fopen(basename($url), 'wb');
        if (!$localFile) {
            return false;
        }

        while (!feof($remoteFile)) {
            $data = fread($remoteFile, 1024);
            fwrite($localFile, $data, 1024);
        }

        fclose($remoteFile);
        fclose($localFile);

        return basename($url);
    }

    public static function simplexml_load_file($url, $class_name = null)
    {
        $cache_id = 'Tools::simplexml_load_file' . $url;
        if (!Cache::isStored($cache_id)) {
            $result = @simplexml_load_string(Tools::file_get_contents($url), $class_name);
            Cache::store($cache_id, $result);

            return $result;
        }

        return Cache::retrieve($cache_id);
    }

    public static function copy($source, $destination, $stream_context = null)
    {
        if (null === $stream_context && !preg_match('/^https?:\/\//', $source)) {
            return @copy($source, $destination);
        }

        return @file_put_contents($destination, Tools::file_get_contents($source, false, $stream_context));
    }

    /**
     * Translates a string with underscores into camel case (e.g. first_name -> firstName).
     *
     * @prototype string public static function toCamelCase(string $str[, bool $capitalise_first_char = false])
     *
     * @param string $str Source string to convert in camel case
     * @param bool $capitaliseFirstChar Optionnal parameters to transform the first letter in upper case
     *
     * @return string The string in camel case
     */
    public static function toCamelCase($str, $capitaliseFirstChar = false)
    {
        $str = Tools::strtolower($str);
        $str = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $str)));
        if (!$capitaliseFirstChar) {
            $str = lcfirst($str);
        }

        return $str;
    }

    /**
     * Transform a CamelCase string to underscore_case string.
     *
     * 'CMSCategories' => 'cms_categories'
     * 'RangePrice' => 'range_price'
     *
     * @param string $string
     *
     * @return string
     */
    public static function toUnderscoreCase($string)
    {
        return Tools::strtolower(trim(preg_replace('/([A-Z][a-z])/', '_$1', $string), '_'));
    }

    /**
     * Converts SomethingLikeThis to something-like-this
     *
     * @param string $string
     *
     * @return string
     */
    public static function camelCaseToKebabCase($string)
    {
        return Tools::strtolower(
            preg_replace('/([a-z])([A-Z])/', '$1-$2', $string)
        );
    }

    public static function parserSQL($sql)
    {
        if (strlen($sql) > 0) {
            $parser = new PHPSQLParser($sql);

            return $parser->parsed;
        }

        return false;
    }

    protected static $_cache_nb_media_servers = null;

    /**
     * @return bool
     */
    public static function hasMediaServer(): bool
    {
        if (self::$_cache_nb_media_servers === null && defined('_MEDIA_SERVER_1_') && defined('_MEDIA_SERVER_2_') && defined('_MEDIA_SERVER_3_')) {
            if (_MEDIA_SERVER_1_ == '') {
                self::$_cache_nb_media_servers = 0;
            } elseif (_MEDIA_SERVER_2_ == '') {
                self::$_cache_nb_media_servers = 1;
            } elseif (_MEDIA_SERVER_3_ == '') {
                self::$_cache_nb_media_servers = 2;
            } else {
                self::$_cache_nb_media_servers = 3;
            }
        }

        return self::$_cache_nb_media_servers > 0;
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    public static function getMediaServer(string $filename): string
    {
        if (self::hasMediaServer()) {
            $id_media_server = abs(crc32($filename)) % self::$_cache_nb_media_servers + 1;

            return constant('_MEDIA_SERVER_' . $id_media_server . '_');
        }

        return Tools::usingSecureMode() ? Tools::getShopDomainSsl() : Tools::getShopDomain();
    }

    /**
     * Get domains information with physical and virtual paths
     *
     * e.g: [
     *  prestashop.localhost => [
     *    physical => "/",
     *    virtual => "",
     *    id_shop => "1",
     *  ]
     * ]
     *
     * @return array
     */
    public static function getDomains()
    {
        $domains = [];
        foreach (ShopUrl::getShopUrls() as $shop_url) {
            /** @var ShopUrl $shop_url */
            if (!isset($domains[$shop_url->domain])) {
                $domains[$shop_url->domain] = [];
            }

            $domains[$shop_url->domain][] = [
                'physical' => $shop_url->physical_uri,
                'virtual' => $shop_url->virtual_uri,
                'id_shop' => $shop_url->id_shop,
            ];

            if ($shop_url->domain == $shop_url->domain_ssl) {
                continue;
            }

            if (!isset($domains[$shop_url->domain_ssl])) {
                $domains[$shop_url->domain_ssl] = [];
            }

            $domains[$shop_url->domain_ssl][] = [
                'physical' => $shop_url->physical_uri,
                'virtual' => $shop_url->virtual_uri,
                'id_shop' => $shop_url->id_shop,
            ];
        }

        return $domains;
    }

    public static function generateHtaccess($path = null, $rewrite_settings = null, $cache_control = null, $specific = '', $disable_multiviews = null, $medias = false, $disable_modsec = null)
    {
        if (
            defined('_PS_IN_TEST_')
            || (defined('PS_INSTALLATION_IN_PROGRESS') && $rewrite_settings === null)
        ) {
            return true;
        }

        // Default values for parameters
        if (null === $path) {
            $path = _PS_ROOT_DIR_ . '/.htaccess';
        }

        if (null === $cache_control) {
            $cache_control = (int) Configuration::get('PS_HTACCESS_CACHE_CONTROL');
        }
        if (null === $disable_multiviews) {
            $disable_multiviews = (bool) Configuration::get('PS_HTACCESS_DISABLE_MULTIVIEWS');
        }

        if ($disable_modsec === null) {
            $disable_modsec = (int) Configuration::get('PS_HTACCESS_DISABLE_MODSEC');
        }

        // Check current content of .htaccess and save all code outside of prestashop comments
        $specific_before = $specific_after = '';
        if (file_exists($path)) {
            $content = file_get_contents($path);
            if (preg_match('#^(.*)\# ~~start~~.*\# ~~end~~[^\n]*(.*)$#s', $content, $m)) {
                $specific_before = $m[1];
                $specific_after = $m[2];
            } else {
                // For retrocompatibility
                if (preg_match('#\# http://www\.prestashop\.com - http://www\.prestashop\.com/forums\s*(.*)<IfModule mod_rewrite\.c>#si', $content, $m)) {
                    $specific_before = $m[1];
                } else {
                    $specific_before = $content;
                }
            }
        }

        // Write .htaccess data
        if (!$write_fd = @fopen($path, 'wb')) {
            return false;
        }
        if ($specific_before) {
            fwrite($write_fd, trim($specific_before) . "\n\n");
        }

        $domains = self::getDomains();

        // Write data in .htaccess file
        fwrite($write_fd, "# ~~start~~ Do not remove this comment, Prestashop will keep automatically the code outside this comment when .htaccess will be generated again\n");
        fwrite($write_fd, "# .htaccess automaticaly generated by PrestaShop e-commerce open-source solution\n");
        fwrite($write_fd, "# https://www.prestashop.com - https://www.prestashop.com/forums\n\n");

        if ($disable_modsec) {
            fwrite($write_fd, "<IfModule mod_security.c>\nSecFilterEngine Off\nSecFilterScanPOST Off\n</IfModule>\n\n");
        }

        // RewriteEngine
        fwrite($write_fd, "<IfModule mod_rewrite.c>\n");

        // Ensure HTTP_MOD_REWRITE variable is set in environment
        fwrite($write_fd, "<IfModule mod_env.c>\n");
        fwrite($write_fd, "SetEnv HTTP_MOD_REWRITE On\n");
        fwrite($write_fd, "</IfModule>\n\n");

        // Disable multiviews ?
        if ($disable_multiviews) {
            fwrite($write_fd, "\n# Disable Multiviews\nOptions -Multiviews\n\n");
        }

        fwrite($write_fd, "RewriteEngine on\n");

        if (
            !$medias
            && Configuration::getMultiShopValues('PS_MEDIA_SERVER_1')
            && Configuration::getMultiShopValues('PS_MEDIA_SERVER_2')
            && Configuration::getMultiShopValues('PS_MEDIA_SERVER_3')
        ) {
            $medias = [
                Configuration::getMultiShopValues('PS_MEDIA_SERVER_1'),
                Configuration::getMultiShopValues('PS_MEDIA_SERVER_2'),
                Configuration::getMultiShopValues('PS_MEDIA_SERVER_3'),
            ];
        }

        $media_domains = '';
        foreach ($medias as $media) {
            foreach ($media as $media_url) {
                if ($media_url) {
                    $media_domains .= 'RewriteCond %{HTTP_HOST} ^' . $media_url . '$ [OR]' . PHP_EOL;
                }
            }
        }

        if (Configuration::get('PS_WEBSERVICE_CGI_HOST')) {
            fwrite($write_fd, "RewriteCond %{HTTP:Authorization} ^(.*)\nRewriteRule . - [E=HTTP_AUTHORIZATION:%1]\n\n");
        }

        foreach ($domains as $domain => $list_uri) {
            // As we use regex in the htaccess, ipv6 surrounded by brackets must be escaped
            $domain = str_replace(['[', ']'], ['\[', '\]'], $domain);

            $domain_rewrite_cond = '';
            foreach ($list_uri as $uri) {
                fwrite($write_fd, PHP_EOL . PHP_EOL . '#Domain: ' . $domain . PHP_EOL);
                if (Shop::isFeatureActive()) {
                    fwrite($write_fd, 'RewriteCond %{HTTP_HOST} ^' . $domain . '$' . PHP_EOL);
                }
                fwrite($write_fd, 'RewriteRule . - [E=REWRITEBASE:' . $uri['physical'] . ']' . PHP_EOL);

                // Webservice
                fwrite($write_fd, 'RewriteRule ^api(?:/(.*))?$ %{ENV:REWRITEBASE}webservice/dispatcher.php?url=$1 [QSA,L]' . PHP_EOL);
                // upload folder
                fwrite($write_fd, 'RewriteRule ^upload/.+$ %{ENV:REWRITEBASE}index.php [QSA,L]' . "\n\n");

                if (!$rewrite_settings) {
                    $rewrite_settings = (int) Configuration::get('PS_REWRITING_SETTINGS', null, null, (int) $uri['id_shop']);
                }

                $domain_rewrite_cond = 'RewriteCond %{HTTP_HOST} ^' . $domain . '$' . PHP_EOL;
                // Rewrite virtual multishop uri
                if ($uri['virtual']) {
                    if (!$rewrite_settings) {
                        fwrite($write_fd, $media_domains);
                        fwrite($write_fd, $domain_rewrite_cond);
                        fwrite($write_fd, 'RewriteRule ^' . trim($uri['virtual'], '/') . '/?$ ' . $uri['physical'] . $uri['virtual'] . "index.php [L,R]\n");
                    } else {
                        fwrite($write_fd, $media_domains);
                        fwrite($write_fd, $domain_rewrite_cond);
                        fwrite($write_fd, 'RewriteRule ^' . trim($uri['virtual'], '/') . '$ ' . $uri['physical'] . $uri['virtual'] . " [L,R]\n");
                    }
                    fwrite($write_fd, $media_domains);
                    fwrite($write_fd, $domain_rewrite_cond);
                    fwrite($write_fd, 'RewriteRule ^' . ltrim($uri['virtual'], '/') . '(.*) ' . $uri['physical'] . "$1 [L]\n\n");
                }

                if ($rewrite_settings) {
                    // Compatibility with the old image filesystem
                    fwrite($write_fd, "# Images\n");

                    // Rewrite product images < 10 millions
                    $path_components = [];
                    for ($i = 1; $i <= 7; ++$i) {
                        $path_components[] = '$' . ($i + 1); // paths start on 2
                        $path = implode('/', $path_components);
                        fwrite($write_fd, $media_domains);
                        fwrite($write_fd, $domain_rewrite_cond);
                        fwrite($write_fd, 'RewriteRule ^(' . str_repeat('([\d])', $i) . '(?:\-[\w-]*)?)/.+(\.(?:jpe?g|webp|png|avif))$ %{ENV:REWRITEBASE}img/p/' . $path . '/$1$' . ($i + 2) . " [L]\n");
                    }
                    fwrite($write_fd, $media_domains);
                    fwrite($write_fd, $domain_rewrite_cond);
                    fwrite($write_fd, 'RewriteRule ^c/([\d]+)(\-[\.*\w-]*)/.+(\.(?:jpe?g|webp|png|avif))$ %{ENV:REWRITEBASE}img/c/$1$2$3 [L]' . PHP_EOL);
                    fwrite($write_fd, $media_domains);
                    fwrite($write_fd, $domain_rewrite_cond);
                    fwrite($write_fd, 'RewriteRule ^c/([a-zA-Z_-]+)(-[\d]+)?/.+(\.(?:jpe?g|webp|png|avif))$ %{ENV:REWRITEBASE}img/c/$1$2$3 [L]' . PHP_EOL);
                }

                fwrite($write_fd, "# AlphaImageLoader for IE and fancybox\n");
                if (Shop::isFeatureActive()) {
                    fwrite($write_fd, $domain_rewrite_cond);
                }
                fwrite($write_fd, 'RewriteRule ^images_ie/?([^/]+)\.(jpe?g|png|gif)$ %{ENV:REWRITEBASE}js/jquery/plugins/fancybox/images/$1.$2 [L]' . PHP_EOL);
            }
            // Redirections to dispatcher
            if ($rewrite_settings) {
                fwrite($write_fd, "\n# Dispatcher\n");
                fwrite($write_fd, "RewriteCond %{REQUEST_FILENAME} -s [OR]\n");
                fwrite($write_fd, "RewriteCond %{REQUEST_FILENAME} -l [OR]\n");
                fwrite($write_fd, "RewriteCond %{REQUEST_FILENAME} -d\n");
                if (Shop::isFeatureActive()) {
                    fwrite($write_fd, $domain_rewrite_cond);
                }
                fwrite($write_fd, "RewriteRule ^.*$ - [NC,L]\n");
                if (Shop::isFeatureActive()) {
                    fwrite($write_fd, $domain_rewrite_cond);
                }
                fwrite($write_fd, "RewriteRule ^.*\$ %{ENV:REWRITEBASE}index.php [NC,L]\n");
            }
        }

        fwrite($write_fd, "</IfModule>\n\n");

        fwrite($write_fd, "AddType application/vnd.ms-fontobject .eot\n");
        fwrite($write_fd, "AddType font/ttf .ttf\n");
        fwrite($write_fd, "AddType font/otf .otf\n");
        fwrite($write_fd, "AddType application/font-woff .woff\n");
        fwrite($write_fd, "AddType font/woff2 .woff2\n");
        fwrite($write_fd, "<IfModule mod_headers.c>
	<FilesMatch \"\.(ttf|ttc|otf|eot|woff|woff2|svg)$\">
		Header set Access-Control-Allow-Origin \"*\"
	</FilesMatch>
</IfModule>\n\n");
        fwrite($write_fd, '<Files composer.lock>
    # Apache 2.2
    <IfModule !mod_authz_core.c>
        Order deny,allow
        Deny from all
    </IfModule>

    # Apache 2.4
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
</Files>
');
        // Cache control
        if ($cache_control) {
            $cache_control = "<IfModule mod_expires.c>
	ExpiresActive On
    AddType image/webp .webp
    ExpiresByType image/webp \"access plus 1 month\"
    ExpiresByType image/avif \"access plus 1 month\"
	ExpiresByType image/gif \"access plus 1 month\"
	ExpiresByType image/jpeg \"access plus 1 month\"
	ExpiresByType image/png \"access plus 1 month\"
	ExpiresByType text/css \"access plus 1 week\"
	ExpiresByType text/javascript \"access plus 1 week\"
	ExpiresByType application/javascript \"access plus 1 week\"
	ExpiresByType application/x-javascript \"access plus 1 week\"
	ExpiresByType image/x-icon \"access plus 1 year\"
	ExpiresByType image/svg+xml \"access plus 1 year\"
	ExpiresByType image/vnd.microsoft.icon \"access plus 1 year\"
	ExpiresByType application/font-woff \"access plus 1 year\"
	ExpiresByType application/x-font-woff \"access plus 1 year\"
	ExpiresByType font/woff2 \"access plus 1 year\"
	ExpiresByType application/vnd.ms-fontobject \"access plus 1 year\"
	ExpiresByType font/opentype \"access plus 1 year\"
	ExpiresByType font/ttf \"access plus 1 year\"
	ExpiresByType font/otf \"access plus 1 year\"
	ExpiresByType application/x-font-ttf \"access plus 1 year\"
	ExpiresByType application/x-font-otf \"access plus 1 year\"
</IfModule>

<IfModule mod_headers.c>
    Header unset Etag
</IfModule>
FileETag none
<IfModule mod_deflate.c>
    <IfModule mod_filter.c>
        AddOutputFilterByType DEFLATE text/html text/css text/javascript application/javascript application/x-javascript font/ttf application/x-font-ttf font/otf application/x-font-otf font/opentype image/svg+xml
    </IfModule>
</IfModule>\n\n";
            fwrite($write_fd, $cache_control);
        }

        // In case the user hasn't rewrite mod enabled
        fwrite($write_fd, "#If rewrite mod isn't enabled\n");

        // Do not remove ($domains is already iterated upper)
        reset($domains);
        $domain = current($domains);
        fwrite($write_fd, 'ErrorDocument 404 ' . $domain[0]['physical'] . "index.php?controller=404\n\n");

        fwrite($write_fd, '# ~~end~~ Do not remove this comment, Prestashop will keep automatically the code outside this comment when .htaccess will be generated again');
        if ($specific_after) {
            fwrite($write_fd, "\n\n" . trim($specific_after));
        }
        fclose($write_fd);

        if (!defined('PS_INSTALLATION_IN_PROGRESS')) {
            Hook::exec('actionHtaccessCreate');
        }

        return true;
    }

    /**
     * @param bool $executeHook
     *
     * @return bool
     */
    public static function generateRobotsFile($executeHook = false)
    {
        $robots_file = _PS_ROOT_DIR_ . '/robots.txt';

        if (!$write_fd = @fopen($robots_file, 'wb')) {
            return false;
        }

        $robots_content = static::getRobotsContent();
        $languagesIsoIds = Language::getIsoIds();

        if (true === $executeHook) {
            Hook::exec('actionAdminMetaBeforeWriteRobotsFile', [
                'rb_data' => &$robots_content,
            ]);
        }

        // PS Comments
        fwrite($write_fd, "# robots.txt automatically generated by PrestaShop e-commerce open-source solution\n");
        fwrite($write_fd, "# https://www.prestashop.com - https://www.prestashop.com/forums\n");
        fwrite($write_fd, "# This file is to prevent the crawling and indexing of certain parts\n");
        fwrite($write_fd, "# of your site by web crawlers and spiders run by sites like Yahoo!\n");
        fwrite($write_fd, "# and Google. By telling these \"robots\" where not to go on your site,\n");
        fwrite($write_fd, "# you save bandwidth and server resources.\n");
        fwrite($write_fd, "# For more information about the robots.txt standard, see:\n");
        fwrite($write_fd, "# https://www.robotstxt.org/robotstxt.html\n");

        // User-Agent
        fwrite($write_fd, "User-agent: *\n");

        // Allow Directives
        if (count($robots_content['Allow'])) {
            fwrite($write_fd, "# Allow Directives\n");
            foreach ($robots_content['Allow'] as $allow) {
                fwrite($write_fd, 'Allow: ' . $allow . PHP_EOL);
            }
        }

        // Private pages
        if (count($robots_content['GB'])) {
            fwrite($write_fd, "# Private pages\n");
            foreach ($robots_content['GB'] as $gb) {
                fwrite($write_fd, 'Disallow: /*' . $gb . PHP_EOL);
            }
        }

        // Directories
        if (count($robots_content['Directories'])) {
            foreach (self::getDomains() as $domain => $uriList) {
                fwrite(
                    $write_fd,
                    sprintf(
                        '# Directories for %s%s',
                        $domain,
                        PHP_EOL
                    )
                );
                // Disallow multishop directories
                foreach ($uriList as $uri) {
                    foreach ($robots_content['Directories'] as $dir) {
                        fwrite($write_fd, 'Disallow: ' . $uri['physical'] . $dir . PHP_EOL);
                    }
                    // Disallow multilang directories
                    if (is_array($languagesIsoIds) && count($languagesIsoIds) > 1) {
                        foreach ($languagesIsoIds as $language) {
                            foreach ($robots_content['Directories'] as $dir) {
                                fwrite(
                                    $write_fd,
                                    sprintf(
                                        'Disallow: %s%s/%s%s',
                                        $uri['physical'],
                                        $language['iso_code'],
                                        $dir,
                                        PHP_EOL
                                    )
                                );
                            }
                        }
                    }
                    // Files
                    if (count($robots_content['Files'])) {
                        fwrite($write_fd, "# Files\n");
                        foreach ($robots_content['Files'] as $iso_code => $files) {
                            foreach ($files as $file) {
                                if (count($languagesIsoIds) > 1) {
                                    fwrite($write_fd, 'Disallow: /*' . $iso_code . '/' . $file . PHP_EOL);
                                } else {
                                    fwrite($write_fd, 'Disallow: ' . $uri['physical'] . $file . PHP_EOL);
                                }
                            }
                        }
                    }
                }
            }
        }

        if (null === Context::getContext()) {
            $sitemap_file = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'index_sitemap.xml';
        } else {
            $sitemap_file = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . Context::getContext()->shop->id . '_index_sitemap.xml';
        }

        // Sitemap
        if (file_exists($sitemap_file) && filesize($sitemap_file)) {
            fwrite($write_fd, "# Sitemap\n");
            $sitemap_filename = basename($sitemap_file);
            fwrite($write_fd, 'Sitemap: ' . static::getProtocol((bool) Configuration::get('PS_SSL_ENABLED')) . $_SERVER['SERVER_NAME']
                . __PS_BASE_URI__ . $sitemap_filename . PHP_EOL);
        }

        if (true === $executeHook) {
            Hook::exec('actionAdminMetaAfterWriteRobotsFile', [
                'rb_data' => $robots_content,
                'write_fd' => &$write_fd,
            ]);
        }

        fclose($write_fd);

        return true;
    }

    /**
     * @return array
     */
    public static function getRobotsContent()
    {
        $tab = [];

        // Special allow directives
        $tab['Allow'] = [
            '*/modules/*.css',
            '*/modules/*.js',
            '*/modules/*.png',
            '*/modules/*.jpg',
            '*/modules/*.gif',
            '*/modules/*.svg',
            '*/modules/*.webp',
            '/js/jquery/*',
        ];

        // Directories
        $tab['Directories'] = [
            'app/', 'cache/', 'classes/', 'config/', 'controllers/',
            'download/', 'js/', 'localization/', 'log/', 'mails/', 'modules/', 'override/',
            'pdf/', 'src/', 'tools/', 'translations/', 'upload/', 'var/', 'vendor/', 'webservice/',
        ];

        // Files
        $disallow_controllers = [
            'addresses', 'address', 'authentication', 'cart', 'discount', 'footer',
            'get-file', 'header', 'history', 'identity', 'images.inc', 'init', 'my-account', 'order',
            'order-slip', 'order-detail', 'order-follow', 'order-return', 'order-confirmation', 'pagination', 'password',
            'pdf-invoice', 'pdf-order-return', 'pdf-order-slip', 'product-sort', 'registration', 'search', 'statistics', 'attachment', 'guest-tracking',
        ];

        // Rewrite files
        $tab['Files'] = [];
        if (Configuration::get('PS_REWRITING_SETTINGS')) {
            $sql = 'SELECT DISTINCT ml.url_rewrite, l.iso_code
                FROM ' . _DB_PREFIX_ . 'meta m
                INNER JOIN ' . _DB_PREFIX_ . 'meta_lang ml ON ml.id_meta = m.id_meta
                INNER JOIN ' . _DB_PREFIX_ . 'lang l ON l.id_lang = ml.id_lang
                WHERE l.active = 1 AND m.page IN (\'' . implode('\', \'', $disallow_controllers) . '\')';
            if ($results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
                foreach ($results as $row) {
                    $tab['Files'][$row['iso_code']][] = $row['url_rewrite'];
                }
            }
        }

        $tab['GB'] = [
            '?order=', '?tag=', '?id_currency=', '?search_query=', '?back=', '?n=',
            '&order=', '&tag=', '&id_currency=', '&search_query=', '&back=', '&n=',
        ];

        foreach ($disallow_controllers as $controller) {
            $tab['GB'][] = 'controller=' . $controller;
        }

        return $tab;
    }

    /**
     * @return string php file to be run
     */
    public static function getDefaultIndexContent()
    {
        return '<?php
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

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

header("Location: ../");
exit;
';
    }

    /**
     * Return the directory list from the given $path.
     *
     * @param string $path
     *
     * @return array
     */
    public static function getDirectories($path)
    {
        if (function_exists('glob')) {
            return self::getDirectoriesWithGlob($path);
        }

        return self::getDirectoriesWithReaddir($path);
    }

    /**
     * Return the directory list from the given $path using php glob function.
     *
     * @param string $path
     *
     * @return array
     */
    public static function getDirectoriesWithGlob($path)
    {
        $directoryList = glob($path . '/*', GLOB_ONLYDIR | GLOB_NOSORT);
        if ($directoryList === false) {
            return [];
        }

        $directoryList = array_map(function ($path) {
            return substr($path, strrpos($path, '/') + 1);
        }, $directoryList);

        return $directoryList;
    }

    /**
     * Return the directory list from the given $path using php readdir function.
     *
     * @param string $path
     *
     * @return array
     */
    public static function getDirectoriesWithReaddir($path)
    {
        $directoryList = [];
        $dh = @opendir($path);
        if ($dh) {
            while (($file = @readdir($dh)) !== false) {
                if (is_dir($path . DIRECTORY_SEPARATOR . $file) && $file[0] != '.') {
                    $directoryList[] = $file;
                }
            }
            @closedir($dh);
        }

        return $directoryList;
    }

    /**
     * Display a warning message indicating that the method is deprecated.
     *
     * @param string $message
     */
    public static function displayAsDeprecated($message = null)
    {
        $backtrace = debug_backtrace();
        $callee = next($backtrace);
        $class = isset($callee['class']) ? $callee['class'] : null;

        if ($message === null) {
            $message = 'The function ' . $callee['function'] . ' (Line ' . $callee['line'] . ') is deprecated and will be removed in the next major version.';
        }

        $error = 'Function <b>' . $callee['function'] . '()</b> is deprecated in <b>' . $callee['file'] . '</b> on line <b>' . $callee['line'] . '</b><br />';

        Tools::throwDeprecated($error, $message, $class);
    }

    /**
     * Display a warning message indicating that the parameter is deprecated.
     */
    public static function displayParameterAsDeprecated($parameter)
    {
        $backtrace = debug_backtrace();
        $callee = next($backtrace);
        $error = 'Parameter <b>' . $parameter . '</b> in function <b>' . (isset($callee['function']) ? $callee['function'] : '') . '()</b> is deprecated in <b>' . $callee['file'] . '</b> on line <b>' . (isset($callee['line']) ? $callee['line'] : '(undefined)') . '</b><br />';
        $message = 'The parameter ' . $parameter . ' in function ' . $callee['function'] . ' (Line ' . (isset($callee['line']) ? $callee['line'] : 'undefined') . ') is deprecated and will be removed in the next major version.';
        $class = isset($callee['class']) ? $callee['class'] : null;

        Tools::throwDeprecated($error, $message, $class);
    }

    public static function displayFileAsDeprecated()
    {
        $backtrace = debug_backtrace();
        $callee = current($backtrace);
        $error = 'File <b>' . $callee['file'] . '</b> is deprecated<br />';
        $message = 'The file ' . $callee['file'] . ' is deprecated and will be removed in the next major version.';
        $class = isset($callee['class']) ? $callee['class'] : null;

        Tools::throwDeprecated($error, $message, $class);
    }

    protected static function throwDeprecated($error, $message, $class)
    {
        if (_PS_DISPLAY_COMPATIBILITY_WARNING_) {
            @trigger_error($error, E_USER_DEPRECATED);
            PrestaShopLogger::addLog($message, 3, $class);
        }
    }

    public static function enableCache($level = 1, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        $smarty = $context->smarty;
        if (!Configuration::get('PS_SMARTY_CACHE')) {
            return;
        }
        if ($smarty->force_compile == 0 && $smarty->caching == $level) {
            return;
        }
        self::$_forceCompile = (int) $smarty->force_compile;
        self::$_caching = (int) $smarty->caching;
        $smarty->force_compile = false;
        $smarty->caching = (int) $level;
        $smarty->cache_lifetime = 31536000; // 1 Year
    }

    public static function restoreCacheSettings(Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        if (isset(self::$_forceCompile)) {
            $context->smarty->force_compile = (bool) self::$_forceCompile;
        }
        if (isset(self::$_caching)) {
            $context->smarty->caching = (int) self::$_caching;
        }
    }

    public static function isCallable($function)
    {
        $disabled = explode(',', ini_get('disable_functions'));

        return !in_array($function, $disabled) && is_callable($function);
    }

    public static function pRegexp($s, $delim)
    {
        $s = str_replace($delim, '\\' . $delim, $s);
        foreach (['?', '[', ']', '(', ')', '{', '}', '-', '.', '+', '*', '^', '$', '`', '"', '%'] as $char) {
            $s = str_replace($char, '\\' . $char, $s);
        }

        return $s;
    }

    public static function str_replace_once($needle, $replace, $haystack)
    {
        $pos = false;
        if ($needle) {
            $pos = strpos($haystack, $needle);
        }
        if ($pos === false) {
            return $haystack;
        }

        return substr_replace($haystack, $replace, $pos, strlen($needle));
    }

    /**
     * Identify the version of php
     *
     * @return string
     */
    public static function checkPhpVersion()
    {
        if (defined('PHP_VERSION')) {
            $version = PHP_VERSION;
        } else {
            $version = phpversion('');
        }

        // Specific ubuntu usecase: php version returns 5.2.4-2ubuntu5.2
        if (strpos($version, '-') !== false) {
            $version = substr($version, 0, strpos($version, '-'));
        }

        return $version;
    }

    /**
     * Try to open a zip file in order to check if it's valid
     *
     * @param string $from_file
     *
     * @return bool success
     */
    public static function ZipTest($from_file)
    {
        $zip = new ZipArchive();

        return $zip->open($from_file, ZipArchive::CHECKCONS) === true;
    }

    /**
     * Extract a zip file to the given directory
     *
     * @param string $from_file
     * @param string $to_dir
     *
     * @return bool
     */
    public static function ZipExtract($from_file, $to_dir)
    {
        if (!file_exists($to_dir)) {
            mkdir($to_dir, PsFileSystem::DEFAULT_MODE_FOLDER);
        }

        $zip = new ZipArchive();
        if ($zip->open($from_file) === true && $zip->extractTo($to_dir) && $zip->close()) {
            return true;
        }

        return false;
    }

    /**
     * @param string $path
     * @param int $filemode
     *
     * @return bool
     */
    public static function chmodr($path, $filemode)
    {
        if (!is_dir($path)) {
            return @chmod($path, $filemode);
        }
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if ($file != '.' && $file != '..') {
                $fullpath = $path . '/' . $file;
                if (is_link($fullpath)) {
                    return false;
                } elseif (!is_dir($fullpath) && !@chmod($fullpath, $filemode)) {
                    return false;
                } elseif (!Tools::chmodr($fullpath, $filemode)) {
                    return false;
                }
            }
        }
        closedir($dh);
        if (@chmod($path, $filemode)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get products order field name for queries.
     *
     * @param string $type by|way
     * @param string|bool|null $value If no index given, use default order from admin -> pref -> products
     * @param bool|string $prefix
     *
     * @return string Order by sql clause
     */
    public static function getProductsOrder($type, $value = null, $prefix = false)
    {
        switch ($type) {
            case 'by':
                $list = [0 => 'name', 1 => 'price', 2 => 'date_add', 3 => 'date_upd', 4 => 'position', 5 => 'manufacturer_name', 6 => 'quantity', 7 => 'reference'];
                $value = (null === $value || $value === false || $value === '') ? (int) Configuration::get('PS_PRODUCTS_ORDER_BY') : $value;
                $value = (isset($list[$value])) ? $list[$value] : ((in_array($value, $list)) ? $value : 'position');
                $order_by_prefix = '';
                if ($prefix) {
                    if ($value == 'id_product' || $value == 'date_add' || $value == 'date_upd' || $value == 'price') {
                        $order_by_prefix = 'p.';
                    } elseif ($value == 'name') {
                        $order_by_prefix = 'pl.';
                    } elseif ($value == 'manufacturer_name') {
                        $order_by_prefix = 'm.';
                        $value = 'name';
                    } elseif ($value == 'position' || empty($value)) {
                        $order_by_prefix = 'cp.';
                    }
                }

                return $order_by_prefix . $value;

            case 'way':
                $value = (null === $value || $value === false || $value === '') ? (int) Configuration::get('PS_PRODUCTS_ORDER_WAY') : $value;
                $list = [0 => 'asc', 1 => 'desc'];

                return (isset($list[$value])) ? $list[$value] : ((in_array($value, $list)) ? $value : 'asc');
        }

        return '';
    }

    /**
     * Convert a shorthand byte value from a PHP configuration directive to an integer value.
     *
     * @param string $value value to convert
     *
     * @return int|string
     */
    public static function convertBytes($value)
    {
        if (is_numeric($value)) {
            return $value;
        } else {
            $value_length = strlen($value);
            $qty = (int) substr($value, 0, $value_length - 1);
            $unit = Tools::strtolower(substr($value, $value_length - 1));
            switch ($unit) {
                case 'k':
                    $qty *= 1024;

                    break;
                case 'm':
                    $qty *= 1048576;

                    break;
                case 'g':
                    $qty *= 1073741824;

                    break;
            }

            return $qty;
        }
    }

    /**
     * Concat $begin and $end, add ? or & between strings.
     *
     * @since 1.5.0
     *
     * @param string $begin
     * @param string $end
     *
     * @return string
     */
    public static function url($begin, $end)
    {
        return $begin . ((strpos($begin, '?') !== false) ? '&' : '?') . $end;
    }

    /**
     * Display error and dies or silently log the error.
     *
     * @param string $msg
     * @param bool $die
     *
     * @return bool success of logging
     */
    public static function dieOrLog($msg, $die = true)
    {
        if ($die || (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_)) {
            header('HTTP/1.1 500 Internal Server Error', true, 500);
            die($msg);
        }

        return PrestaShopLogger::addLog($msg);
    }

    /**
     * Convert \n and \r\n and \r to <br />.
     *
     * @param string|null $str String to transform
     *
     * @return string|null New string
     */
    public static function nl2br($str)
    {
        if (empty($str)) {
            return $str;
        }

        return str_replace(["\r\n", "\r", "\n", AddressFormat::FORMAT_NEW_LINE, PHP_EOL], '<br />', $str);
    }

    /**
     * Clear cache for Smarty.
     *
     * @param Smarty $smarty
     * @param bool|string $tpl
     * @param string $cache_id
     * @param string $compile_id
     *
     * @return int|null number of cache files deleted
     */
    public static function clearCache($smarty = null, $tpl = false, $cache_id = null, $compile_id = null)
    {
        if ($smarty === null) {
            $smarty = Context::getContext()->smarty;
        }

        if ($smarty === null) {
            return null;
        }

        if (!$tpl && $cache_id === null && $compile_id === null) {
            return $smarty->clearAllCache();
        }

        $ret = $smarty->clearCache($tpl, $cache_id, $compile_id);

        Hook::exec('actionClearCache');

        return $ret;
    }

    /**
     * Clear compile for Smarty.
     *
     * @param Smarty $smarty
     *
     * @return int|null number of template files deleted
     */
    public static function clearCompile($smarty = null)
    {
        if ($smarty === null) {
            $smarty = Context::getContext()->smarty;
        }

        if ($smarty === null) {
            return null;
        }

        $ret = $smarty->clearCompiledTemplate();

        Hook::exec('actionClearCompileCache');

        return $ret;
    }

    /**
     * Clear Smarty cache and compile folders.
     */
    public static function clearSmartyCache()
    {
        $smarty = Context::getContext()->smarty;
        Tools::clearCache($smarty);
        Tools::clearCompile($smarty);
    }

    /**
     * Clear Symfony cache.
     *
     * @param string $env
     */
    public static function clearSf2Cache($env = null)
    {
        // This is the legacy method to clear Symfony cache, but it can result in unexpected behaviour with Container rebuild
        // it should not be used anymore and will be removed. Until then, it fallbacks on the proper SymfonyCacheClearer service
        $container = SymfonyContainer::getInstance();
        if (null === $container) {
            self::removeSymfonyCache($env);

            return;
        }

        /** @var CacheClearerInterface|null $symfonyCacheClearer */
        $symfonyCacheClearer = $container->get('prestashop.adapter.cache.clearer.symfony_cache_clearer');
        if ($symfonyCacheClearer) {
            $symfonyCacheClearer->clear();
        }
    }

    private static function removeSymfonyCache(?string $env = null): void
    {
        if (null === $env) {
            $env = _PS_ENV_;
        }

        $dir = _PS_ROOT_DIR_ . '/var/cache/' . $env . '/';

        register_shutdown_function(function () use ($dir) {
            $fs = new Filesystem();
            $fs->remove($dir);
            Hook::exec('actionClearSf2Cache');
        });
    }

    /**
     * Clear both Smarty and Symfony cache.
     */
    public static function clearAllCache()
    {
        Tools::clearSmartyCache();
        Tools::clearSf2Cache();
    }

    /**
     * Allow to get the memory limit in octets.
     *
     * @since 1.4.5.0
     *
     * @return int|string the memory limit value in octet
     */
    public static function getMemoryLimit()
    {
        $memory_limit = @ini_get('memory_limit');

        return Tools::getOctets($memory_limit);
    }

    /**
     * Gets the value of a configuration option in octets.
     *
     * @since 1.5.0
     *
     * @param string $option
     *
     * @return int|string the value of a configuration option in octets
     */
    public static function getOctets($option)
    {
        if (preg_match('/[0-9]+k/i', $option)) {
            return 1024 * (int) $option;
        }

        if (preg_match('/[0-9]+m/i', $option)) {
            return 1024 * 1024 * (int) $option;
        }

        if (preg_match('/[0-9]+g/i', $option)) {
            return 1024 * 1024 * 1024 * (int) $option;
        }

        return $option;
    }

    /**
     * @return bool true if the server use 64bit arch
     */
    public static function isX86_64arch()
    {
        return PHP_INT_MAX == '9223372036854775807';
    }

    /**
     * @return bool true if php-cli is used
     */
    public static function isPHPCLI()
    {
        return defined('STDIN') || (Tools::strtolower(PHP_SAPI) == 'cli' && (!isset($_SERVER['REMOTE_ADDR']) || empty($_SERVER['REMOTE_ADDR'])));
    }

    public static function argvToGET($argc, $argv)
    {
        if ($argc <= 1) {
            return;
        }

        // get the first argument and parse it like a query string
        parse_str($argv[1], $args);
        if (!is_array($args) || !count($args)) {
            return;
        }
        $_GET = array_merge($args, $_GET);
        $_SERVER['QUERY_STRING'] = $argv[1];
    }

    /**
     * Get max file upload size considering server settings and optional max value.
     *
     * @param int $max_size optional max file size
     *
     * @return int max file size in bytes
     */
    public static function getMaxUploadSize($max_size = 0)
    {
        $values = [Tools::convertBytes(ini_get('upload_max_filesize'))];

        if ($max_size > 0) {
            $values[] = $max_size;
        }

        $post_max_size = Tools::convertBytes(ini_get('post_max_size'));
        if ($post_max_size > 0) {
            $values[] = $post_max_size;
        }

        return min($values);
    }

    /**
     * apacheModExists return true if the apache module $name is loaded.
     *
     * @TODO move this method in class Information (when it will exist)
     *
     * Notes: This method requires either apache_get_modules or phpinfo()
     * to be available. With CGI mod, we cannot get php modules
     *
     * @param string $name module name
     *
     * @return bool true if exists
     *
     * @since 1.4.5.0
     */
    public static function apacheModExists($name)
    {
        if (function_exists('apache_get_modules')) {
            static $apache_module_list = null;

            if (!is_array($apache_module_list)) {
                $apache_module_list = apache_get_modules();
            }

            // we need strpos (example, evasive can be evasive20)
            foreach ($apache_module_list as $module) {
                if (strpos($module, $name) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Fix native uasort see: http://php.net/manual/en/function.uasort.php#114535.
     *
     * @param array $array
     * @param callable $cmp_function
     */
    public static function uasort(&$array, $cmp_function)
    {
        if (count($array) < 2) {
            return;
        }
        $halfway = (int) (count($array) / 2);
        $array1 = array_slice($array, 0, $halfway, true);
        $array2 = array_slice($array, $halfway, null, true);

        self::uasort($array1, $cmp_function);
        self::uasort($array2, $cmp_function);
        if (call_user_func($cmp_function, end($array1), reset($array2)) < 1) {
            $array = $array1 + $array2;

            return;
        }
        $array = [];
        reset($array1);
        reset($array2);
        while (current($array1) && current($array2)) {
            if (call_user_func($cmp_function, current($array1), current($array2)) < 1) {
                $array[key($array1)] = current($array1);
                next($array1);
            } else {
                $array[key($array2)] = current($array2);
                next($array2);
            }
        }
        while (current($array1)) {
            $array[key($array1)] = current($array1);
            next($array1);
        }
        while (current($array2)) {
            $array[key($array2)] = current($array2);
            next($array2);
        }
    }

    /**
     * Copy the folder $src into $dst, $dst is created if it do not exist.
     *
     * @param string $src
     * @param string $dst
     * @param bool $del if true, delete the file after copy
     */
    public static function recurseCopy($src, $dst, $del = false)
    {
        if (!Tools::file_exists_cache($src)) {
            return false;
        }
        $dir = opendir($src);

        if (!Tools::file_exists_cache($dst)) {
            mkdir($dst);
        }
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . DIRECTORY_SEPARATOR . $file)) {
                    self::recurseCopy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file, $del);
                } else {
                    copy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
                    if ($del && is_writable($src . DIRECTORY_SEPARATOR . $file)) {
                        unlink($src . DIRECTORY_SEPARATOR . $file);
                    }
                }
            }
        }
        closedir($dir);
        if ($del && is_writable($src)) {
            rmdir($src);
        }
    }

    /**
     * @param string $path Path to scan
     * @param string $ext Extention to filter files
     * @param string $dir Add this to prefix output for example /path/dir/*
     *
     * @return array List of file found
     *
     * @since 1.5.0
     */
    public static function scandir($path, $ext = 'php', $dir = '', $recursive = false)
    {
        $path = rtrim(rtrim($path, '\\'), '/') . '/';
        $real_path = rtrim(rtrim($path . $dir, '\\'), '/') . '/';
        $files = scandir($real_path, SCANDIR_SORT_NONE);
        if (!$files) {
            return [];
        }

        $filtered_files = [];

        $real_ext = false;
        if (!empty($ext)) {
            $real_ext = '.' . $ext;
        }
        $real_ext_length = strlen($real_ext);

        $subdir = ($dir) ? $dir . '/' : '';
        foreach ($files as $file) {
            if (!$real_ext || (strpos($file, $real_ext) && strpos($file, $real_ext) == (strlen($file) - $real_ext_length))) {
                $filtered_files[] = $subdir . $file;
            }

            if ($recursive && $file[0] != '.' && is_dir($real_path . $file)) {
                foreach (Tools::scandir($path, $ext, $subdir . $file, $recursive) as $subfile) {
                    $filtered_files[] = $subfile;
                }
            }
        }

        return $filtered_files;
    }

    /**
     * Align version sent and use internal function.
     *
     * @param string $v1
     * @param string $v2
     * @param string $operator
     *
     * @return mixed
     */
    public static function version_compare($v1, $v2, $operator = '<')
    {
        Tools::alignVersionNumber($v1, $v2);

        return version_compare($v1, $v2, $operator);
    }

    /**
     * Align 2 version with the same number of sub version
     * version_compare will work better for its comparison :)
     * (Means: '1.8' to '1.9.3' will change '1.8' to '1.8.0').
     *
     * @param string $v1
     * @param string $v2
     */
    public static function alignVersionNumber(&$v1, &$v2)
    {
        $len1 = count(explode('.', trim($v1, '.')));
        $len2 = count(explode('.', trim($v2, '.')));
        $len = 0;
        $str = '';

        if ($len1 > $len2) {
            $len = $len1 - $len2;
            $str = &$v2;
        } elseif ($len2 > $len1) {
            $len = $len2 - $len1;
            $str = &$v1;
        }

        for ($len; $len > 0; --$len) {
            $str .= '.0';
        }
    }

    public static function modRewriteActive()
    {
        if (Tools::apacheModExists('mod_rewrite')) {
            return true;
        }
        if ((isset($_SERVER['HTTP_MOD_REWRITE']) && Tools::strtolower($_SERVER['HTTP_MOD_REWRITE']) == 'on') || Tools::strtolower(getenv('HTTP_MOD_REWRITE')) == 'on') {
            return true;
        }

        return false;
    }

    public static function unSerialize($serialized, $object = false)
    {
        if (is_string($serialized) && (strpos($serialized, 'O:') === false || !preg_match('/(^|;|{|})O:[0-9]+:"/', $serialized)) && !$object || $object) {
            return @unserialize($serialized);
        }

        return false;
    }

    /**
     * Reproduce array_unique working before php version 5.2.9.
     *
     * @param array $array
     *
     * @return array
     */
    public static function arrayUnique($array)
    {
        return array_unique($array, SORT_REGULAR);
    }

    /**
     * Returns an array containing information about
     * HTTP file upload variable ($_FILES).
     *
     * @param string $input File upload field name
     * @param bool $return_content If true, returns uploaded file contents
     *
     * @return array|null
     */
    public static function fileAttachment($input = 'fileUpload', $return_content = true)
    {
        $file_attachment = null;
        if (isset($_FILES[$input]['name']) && !empty($_FILES[$input]['name']) && !empty($_FILES[$input]['tmp_name'])) {
            $file_attachment['rename'] = uniqid() . Tools::strtolower(substr($_FILES[$input]['name'], -5));
            if ($return_content) {
                $file_attachment['content'] = file_get_contents($_FILES[$input]['tmp_name']);
            }
            $file_attachment['tmp_name'] = $_FILES[$input]['tmp_name'];
            $file_attachment['name'] = $_FILES[$input]['name'];
            $file_attachment['mime'] = $_FILES[$input]['type'];
            $file_attachment['error'] = $_FILES[$input]['error'];
            $file_attachment['size'] = $_FILES[$input]['size'];
        }

        return $file_attachment;
    }

    public static function changeFileMTime($file_name)
    {
        @touch($file_name);
    }

    public static function waitUntilFileIsModified($file_name, $timeout = 180)
    {
        @ini_set('max_execution_time', $timeout);
        $time_limit = ini_get('max_execution_time');
        if (!$time_limit) {
            $time_limit = 30;
        }

        $time_limit -= 5;
        $start_time = microtime(true);
        $last_modified = @filemtime($file_name);

        while (true) {
            if (((microtime(true) - $start_time) > $time_limit) || @filemtime($file_name) > $last_modified) {
                break;
            }
            clearstatcache();
            usleep(300);
        }
    }

    /**
     * Delete a substring from another one starting from the right.
     *
     * @param string $str
     * @param string $str_search
     *
     * @return string
     */
    public static function rtrimString($str, $str_search)
    {
        $length_str = strlen($str_search);
        if (strlen($str) >= $length_str && substr($str, -$length_str) == $str_search) {
            $str = substr($str, 0, -$length_str);
        }

        return $str;
    }

    /**
     * Format a number into a human readable format
     * e.g. 24962496 => 23.81M.
     *
     * @param float $size
     * @param int $precision
     *
     * @return string
     */
    public static function formatBytes($size, $precision = 2)
    {
        if (!$size) {
            return '0';
        }
        $base = log($size) / log(1024);
        $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];

        return round(1024 ** ($base - floor($base)), $precision) . Context::getContext()->getTranslator()->trans($suffixes[floor($base)], [], 'Shop.Theme.Catalog');
    }

    public static function boolVal($value)
    {
        if (empty($value)) {
            $value = false;
        }

        return (bool) $value;
    }

    public static function getUserPlatform()
    {
        if (isset(self::$_user_plateform)) {
            return self::$_user_plateform;
        }

        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        self::$_user_plateform = 'unknown';

        if (preg_match('/linux/i', $user_agent)) {
            self::$_user_plateform = 'Linux';
        } elseif (preg_match('/macintosh|mac os x/i', $user_agent)) {
            self::$_user_plateform = 'Mac';
        } elseif (preg_match('/windows|win32/i', $user_agent)) {
            self::$_user_plateform = 'Windows';
        }

        return self::$_user_plateform;
    }

    public static function getUserBrowser()
    {
        if (isset(self::$_user_browser)) {
            return self::$_user_browser;
        }

        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        self::$_user_browser = 'unknown';

        if (preg_match('/MSIE/i', $user_agent) && !preg_match('/Opera/i', $user_agent)) {
            self::$_user_browser = 'Internet Explorer';
        } elseif (preg_match('/Firefox/i', $user_agent)) {
            self::$_user_browser = 'Mozilla Firefox';
        } elseif (preg_match('/Chrome/i', $user_agent)) {
            self::$_user_browser = 'Google Chrome';
        } elseif (preg_match('/Safari/i', $user_agent)) {
            self::$_user_browser = 'Apple Safari';
        } elseif (preg_match('/Opera/i', $user_agent)) {
            self::$_user_browser = 'Opera';
        } elseif (preg_match('/Netscape/i', $user_agent)) {
            self::$_user_browser = 'Netscape';
        }

        return self::$_user_browser;
    }

    public static function purifyHTML($html, $uri_unescape = null, $allow_style = false)
    {
        static $use_html_purifier = null;
        static $purifier = null;

        if (defined('PS_INSTALLATION_IN_PROGRESS') || !Configuration::configurationIsLoaded()) {
            return $html;
        }

        if ($use_html_purifier === null) {
            $use_html_purifier = (bool) Configuration::get('PS_USE_HTMLPURIFIER');
        }

        if ($use_html_purifier) {
            if ($purifier === null) {
                $config = HTMLPurifier_Config::createDefault();

                $config->set('Attr.EnableID', true);
                $config->set('Attr.AllowedRel', ['nofollow']);
                $config->set('HTML.Trusted', true);
                $config->set('Cache.SerializerPath', _PS_CACHE_DIR_ . 'purifier');
                $config->set('Attr.AllowedFrameTargets', ['_blank', '_self', '_parent', '_top']);
                if (is_array($uri_unescape)) {
                    $config->set('URI.UnescapeCharacters', implode('', $uri_unescape));
                }

                if (Configuration::get('PS_ALLOW_HTML_IFRAME')) {
                    $config->set('HTML.SafeIframe', true);
                    $config->set('HTML.SafeObject', true);
                    $config->set('URI.SafeIframeRegexp', '/.*/');
                }

                // http://developers.whatwg.org/the-video-element.html#the-video-element
                if ($def = $config->getHTMLDefinition(true)) {
                    /* @var HTMLPurifier_HTMLDefinition|HTMLPurifier_HTMLModule $def */
                    $def->addElement('video', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', [
                        'src' => 'URI',
                        'type' => 'Text',
                        'width' => 'Length',
                        'height' => 'Length',
                        'poster' => 'URI',
                        'preload' => 'Enum#auto,metadata,none',
                        'controls' => 'Bool',
                    ]);
                    $def->addElement('source', 'Block', 'Flow', 'Common', [
                        'src' => 'URI',
                        'type' => 'Text',
                    ]);
                    if ($allow_style) {
                        $def->addElement('style', 'Block', 'Flow', 'Common', ['type' => 'Text']);
                    }
                }

                $purifier = new HTMLPurifier($config);
            }

            $html = $purifier->purify($html);
        }

        return $html;
    }

    /**
     * Check if a constant was already defined.
     *
     * @param string $constant Constant name
     * @param mixed $value Default value to set if not defined
     */
    public static function safeDefine($constant, $value)
    {
        if (!defined($constant)) {
            define($constant, $value);
        }
    }

    /**
     * Spread an amount on lines, adjusting the $column field,
     * with the biggest adjustments going to the rows having the
     * highest $sort_column.
     *
     * E.g.:
     * $rows = [['a' => 5.1], ['a' => 8.2]];
     * spreadAmount(0.3, 1, $rows, 'a');
     * => $rows is [['a' => 8.4], ['a' => 5.2]]
     *
     * @param float $amount The amount to spread across the rows
     * @param int $precision Rounding precision
     *                       e.g. if $amount is 1, $precision is 0 and $rows = [['a' => 2], ['a' => 1]]
     *                       then the resulting $rows will be [['a' => 3], ['a' => 1]]
     *                       But if $precision were 1, then the resulting $rows would be [['a' => 2.5], ['a' => 1.5]]
     * @param array $rows An array, associative or not, containing arrays that have at least $column and $sort_column fields
     * @param string $column The column on which to perform adjustments
     */
    public static function spreadAmount($amount, $precision, &$rows, $column)
    {
        if (!is_array($rows) || empty($rows)) {
            return;
        }

        $sort_function = function ($a, $b) use ($column) {
            return $b[$column] > $a[$column] ? 1 : -1;
        };

        uasort($rows, $sort_function);

        $unit = 10 ** $precision;

        $int_amount = (int) round($unit * $amount);

        $remainder = $int_amount % count($rows);
        $amount_to_spread = ($int_amount - $remainder) / count($rows) / $unit;

        $sign = ($amount >= 0 ? 1 : -1);
        $position = 0;
        foreach ($rows as &$row) {
            $adjustment_factor = $amount_to_spread;

            if ($position < abs($remainder)) {
                $adjustment_factor += $sign * 1 / $unit;
            }

            $row[$column] += $adjustment_factor;

            ++$position;
        }
        unset($row);
    }

    /**
     * Return path to a Product or a CMS category.
     *
     * @param string $url_base Start URL
     * @param int $id_category Start category
     * @param string $path Current path
     * @param string $highlight String to highlight (in XHTML/CSS)
     * @param string $category_type Category type (products/cms)
     * @param bool $home
     */
    public static function getPath($url_base, $id_category, $path = '', $highlight = '', $category_type = 'catalog', $home = false)
    {
        $context = Context::getContext();
        if ($category_type == 'catalog') {
            $category = Db::getInstance()->getRow('
		SELECT id_category, level_depth, nleft, nright
		FROM ' . _DB_PREFIX_ . 'category
		WHERE id_category = ' . (int) $id_category);
            if (isset($category['id_category'])) {
                $sql = 'SELECT c.id_category, cl.name, cl.link_rewrite
					FROM ' . _DB_PREFIX_ . 'category c
					LEFT JOIN ' . _DB_PREFIX_ . 'category_lang cl ON (cl.id_category = c.id_category' . Shop::addSqlRestrictionOnLang('cl') . ')
					WHERE c.nleft <= ' . (int) $category['nleft'] . '
						AND c.nright >= ' . (int) $category['nright'] . '
						AND cl.id_lang = ' . (int) $context->language->id .
                       ($home ? ' AND c.id_category=' . (int) $id_category : '') . '
						AND c.id_category != ' . (int) Category::getTopCategory()->id . '
					GROUP BY c.id_category
					ORDER BY c.level_depth ASC
					LIMIT ' . (!$home ? (int) $category['level_depth'] + 1 : 1);
                $categories = Db::getInstance()->executeS($sql);
                $full_path = '';
                $n = 1;
                $n_categories = (int) count($categories);
                foreach ($categories as $category) {
                    $action = (($category['id_category'] == (int) Configuration::get('PS_HOME_CATEGORY') || $home) ? 'index' : 'updatecategory');
                    $link_params = ['action' => $action, 'id_category' => (int) $category['id_category']];
                    $edit_link = Context::getContext()->link->getAdminLink('AdminCategories', true, $link_params);
                    $link_params['action'] = 'index';
                    $index_link = Context::getContext()->link->getAdminLink('AdminCategories', true, $link_params);
                    $edit = '<a href="' . Tools::safeOutput($edit_link) . '" title="' . ($category['id_category'] == Category::getRootCategory()->id_category ? 'Home' : 'Modify') . '"><i class="icon-' . (($category['id_category'] == Category::getRootCategory()->id_category || $home) ? 'home' : 'pencil') . '"></i></a> ';
                    $full_path .= $edit .
                        ($n < $n_categories ? '<a href="' . Tools::safeOutput($index_link) . '" title="' . htmlentities($category['name'], ENT_NOQUOTES, 'UTF-8') . '">' : '') .
                        (!empty($highlight) ? str_ireplace($highlight, '<span class="highlight">' . htmlentities($highlight, ENT_NOQUOTES, 'UTF-8') . '</span>', $category['name']) : $category['name']) .
                        ($n < $n_categories ? '</a>' : '') .
                        (($n++ != $n_categories || !empty($path)) ? ' > ' : '');
                }

                return $full_path . $path;
            }
        } elseif ($category_type == 'cms') {
            $category = new CMSCategory($id_category, $context->language->id);
            if (!$category->id) {
                return $path;
            }
            $name = ($highlight != null) ? str_ireplace($highlight, '<span class="highlight">' . $highlight . '</span>', CMSCategory::hideCMSCategoryPosition($category->name)) : CMSCategory::hideCMSCategoryPosition($category->name);
            $edit = '<a href="' . Tools::safeOutput($url_base . '&id_cms_category=' . $category->id . '&updatecms_category&token=' . Tools::getAdminToken('AdminCmsContent' . (int) Tab::getIdFromClassName('AdminCmsContent') . (int) $context->employee->id)) . '">
				<i class="icon-pencil"></i></a> ';
            if ($category->id == 1) {
                $edit = '<li><a href="' . Tools::safeOutput($url_base . '&id_cms_category=' . $category->id . '&viewcategory&token=' . Tools::getAdminToken('AdminCmsContent' . (int) Tab::getIdFromClassName('AdminCmsContent') . (int) $context->employee->id)) . '">
					<i class="icon-home"></i></a></li> ';
            }
            $path = $edit . '<li><a href="' . Tools::safeOutput($url_base . '&id_cms_category=' . $category->id . '&viewcategory&token=' . Tools::getAdminToken('AdminCmsContent' . (int) Tab::getIdFromClassName('AdminCmsContent') . (int) $context->employee->id)) . '">
		' . $name . '</a></li> > ' . $path;
            if ($category->id == 1) {
                return substr($path, 0, strlen($path) - 3);
            }

            return Tools::getPath($url_base, $category->id_parent, $path, '', 'cms');
        }
    }

    public static function redirectToInstall()
    {
        if (file_exists(__DIR__ . '/../install')) {
            if (defined('_PS_ADMIN_DIR_')) {
                header('Location: ../install/');
            } else {
                header('Location: install/');
            }
        } elseif (file_exists(__DIR__ . '/../install-dev')) {
            if (defined('_PS_ADMIN_DIR_')) {
                header('Location: ../install-dev/');
            } else {
                header('Location: install-dev/');
            }
        } else {
            die('Error: "install" directory is missing');
        }
        exit;
    }

    /**
     * @param array $fallbackParameters
     */
    public static function setFallbackParameters(array $fallbackParameters): void
    {
        static::$fallbackParameters = $fallbackParameters;
    }

    /**
     * @param string $file_to_refresh
     * @param string $external_file
     *
     * @return bool
     */
    public static function refreshFile(string $file_to_refresh, string $external_file): bool
    {
        return (bool) static::copy($external_file, _PS_ROOT_DIR_ . $file_to_refresh);
    }

    /**
     * @param string $file
     * @param int $timeout
     *
     * @return bool
     */
    public static function isFileFresh(string $file, int $timeout = self::CACHE_LIFETIME_SECONDS): bool
    {
        if (($time = @filemtime(_PS_ROOT_DIR_ . $file)) && filesize(_PS_ROOT_DIR_ . $file) > 0) {
            return (time() - $time) < $timeout;
        }

        return false;
    }

    /**
     * @return bool
     */
    public static function isCountryFromBrowserAvailable(): bool
    {
        $languageAvailable = static::getCountryIsoCodeFromHeader();
        if ($languageAvailable === null) {
            return false;
        }

        return Configuration::get('PS_DETECT_COUNTRY')
            && Validate::isLanguageIsoCode($languageAvailable);
    }

    /**
     * @return string|null
     */
    public static function getCountryIsoCodeFromHeader(): ?string
    {
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return null;
        }

        preg_match(static::LANGUAGE_EXTRACTOR_REGEXP, $_SERVER['HTTP_ACCEPT_LANGUAGE'], $languages);

        return $languages[0] ?? null;
    }

    /**
     * Inserts a new element in array after a given index
     *
     * @param array $array Array to modify
     * @param string $targetKey Key to search for
     * @param string $newDataKey Key for an added data
     * @param array $newDataArray New data to insert
     *
     * @return array
     */
    public static function arrayInsertElementAfterKey(array $array, string $targetKey, string $newDataKey, array $newDataArray): array
    {
        if (array_key_exists($targetKey, $array)) {
            $newArray = [];
            foreach ($array as $k => $value) {
                $newArray[$k] = $value;
                if ($k === $targetKey) {
                    $newArray[$newDataKey] = $newDataArray;
                }
            }

            return $newArray;
        }

        return $array;
    }

    /**
     * Generate html data attributes by given array
     *
     * @param array $dataAttributes list of html data attributes
     *
     * @return string
     */
    public static function makeHtmlDataAttributes(array $dataAttributes): string
    {
        $htmlDataAttributes = '';

        if (empty($dataAttributes)) {
            return $htmlDataAttributes;
        }

        $attributesIterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($dataAttributes));

        foreach ($attributesIterator as $attributeName => $value) {
            if (!Validate::isString($attributeName)) {
                @trigger_error(
                    'Tools::makeHtmlDataAttributes() dataAttributes key must be string',
                    E_USER_WARNING
                );
                continue;
            }

            for ($i = $attributesIterator->getDepth() - 1; $i >= 0; --$i) {
                $attributeName = $attributesIterator->getSubIterator($i)->key() . '-' . $attributeName;
            }

            $htmlDataAttributes .= ' data-' . $attributeName . '="' . $attributesIterator->current() . '"';
        }

        return trim($htmlDataAttributes);
    }
}

/**
 * Compare 2 prices to sort products.
 *
 * @param array{"price_tmp": float} $a
 * @param array{"price_tmp": float} $b
 *
 * @return int
 */
function cmpPriceAsc($a, $b)
{
    return $a['price_tmp'] <=> $b['price_tmp'];
}

/**
 * @param array{"price_tmp": float} $a
 * @param array{"price_tmp": float} $b
 *
 * @return int
 */
function cmpPriceDesc($a, $b)
{
    return $b['price_tmp'] <=> $a['price_tmp'];
}
