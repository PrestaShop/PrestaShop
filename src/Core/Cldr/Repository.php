<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Cldr;

use ICanBoogie\CLDR\FileProvider;
use ICanBoogie\CLDR\RunTimeProvider;
use ICanBoogie\CLDR\WebProvider;
use ICanBoogie\CLDR\Currency;
use ICanBoogie\CLDR\Numbers;
use ICanBoogie\CLDR\NumberFormatter;
use ICanBoogie\CLDR\Repository as cldrRepository;
use PrestaShop\PrestaShop\Core\Cldr\Localize;

class Repository
{
    protected $cldrCacheFolder;
    protected $repository;
    protected $localeRepository;
    protected $region;
    protected $locale;
    protected $contextLanguage;
    protected $oldUmask;
    protected $non_iso_relational_language = array(
        'an-es' => 'en-GB',
        'az-az' => 'az-Cyrl-AZ',
        'bs-ba' => 'bs-Cyrl-BA',
        'en-pt' => 'en-GB',
        'en-ud' => 'en-US',
        'eo-uy' => 'eo',
        'fr-qc' => 'fr-CA',
        'ku-tr' => 'en-GB',
        'ms-my' => 'ms-Latn-MY',
        'no-no' => 'nb-NO',
        'sr-cs' => 'sr-Latn-RS',
        'tlh-aa' => 'en-GB',
        'tt-ru' => 'en-GB',
        'ug-cn' => 'ug-Arab-CN',
        'zh-cn' => 'zh-Hans-CN',
        'zh-tw' => 'zh-Hant-TW',
    );

    public function __construct($contextLanguage = null)
    {
        if ($contextLanguage) {
            $contextLanguage = strtolower(is_object($contextLanguage) ? $contextLanguage->locale : $contextLanguage);
            $contextLanguage = isset($this->non_iso_relational_language[$contextLanguage]) ? $this->non_iso_relational_language[$contextLanguage] : $contextLanguage;
        }

        $this->contextLanguage = $contextLanguage;
        $this->cldrCacheFolder = _PS_TRANSLATIONS_DIR_.'cldr';

        $this->oldUmask = umask(0000);

        if (!is_dir($this->cldrCacheFolder)) {
            try {
                mkdir($this->cldrCacheFolder.DIRECTORY_SEPARATOR.'datas', 0777, true);
            } catch (\Exception $e) {
                throw new \Exception('Cldr cache folder can\'t be created');
            }
        }

        $provider = new RunTimeProvider(
            new FileProvider(new WebProvider('http://i18n.prestashop.com/cldr/json-full/'), $this->cldrCacheFolder)
        );

        //if contextLanguage is define, set locale/region from it
        if ($contextLanguage) {
            $this->localeConversion($contextLanguage);
        } else {
            $locale = new Localize();
            $this->locale = $locale->getLanguage();
            $this->region = $locale->getRegion();
        }

        if ($this->locale == 'en' && $this->region == 'EN') {
            $this->region = 'US';
        }

        $this->repository = new cldrRepository($provider);
        $this->localeRepository = $this->repository->locales[$this->getCulture()];
    }

    private function localeConversion($locale)
    {
        $locale = explode('-', $locale);
        if (count($locale) == 3) {
            $this->locale = $locale[0];
            $this->region = $locale[1].'-'.strtoupper($locale[2]);
        } else {
            $this->locale = $locale[0];
            if (!empty($locale[1])) {
                $this->region = strtoupper($locale[1]);
            } else {
                $this->region = strtoupper($this->locale);
            }
        }
    }

    public function __destruct()
    {
        umask($this->oldUmask);
    }

    /*
     * get the current culture
     */
    public function getCulture()
    {
        return $this->locale.($this->region ? '-'.$this->region : '');
    }

    /**
     * set a locale
     *
     * @param string $locale Locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        $this->localeRepository = $this->repository->locales[$this->getCulture()];
    }

    /**
     * set a region
     *
     * @param string $region Region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * Return current cldr repository
     *
     * @return object cldr repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * get all available currencies
     *
     * @return array currencies
     */
    public function getAllCurrencies()
    {
        $currencies = $this->repository->supplemental['codeMappings'];
        $datas = array();
        foreach (array_keys($currencies) as $currency_code) {
            if ($currency_code === 'XTS' || strlen($currency_code) !== 3) {
                continue;
            }
            $currency = $this->getCurrency($currency_code);
            $datas[] = array(
                'name' => ucfirst($currency['name']).' ('.$currency_code.')',
                'code' => $currency_code,
                'iso_code' => $currency['iso_code']
            );
        }

        //sort array naturally
        uasort($datas, function ($x, $y) {
            return strnatcmp($x["name"], $y["name"]);
        });

        return $datas;
    }

    /**
     * Return a iso code num currency by iso code
     *
     * @param string $code currency iso code
     *
     * @return int|null iso code num
     */
    public function getCurrencyIsoCodeNum($code)
    {
        $currencies = $this->repository->supplemental['codeMappings'];

        if (!empty($currencies[$code]) && !empty($currencies[$code]['_numeric'])) {
            return $currencies[$code]['_numeric'];
        }

        return null;
    }

    /**
     * Return a currency
     *
     * @param string $code currency iso code
     *
     * @return array currency
     */
    public function getCurrency($code = null)
    {
        if (!$code) {
            $territory = $this->repository->territories[$this->region];
            $code = (string)$territory->currency;
        } elseif (!$this->isCurrencyValid($code)) {
            return array(
                'name' => $code,
                'symbol' => '',
                'code' => $code,
                'iso_code' => ''
            );
        }

        $currency = new Currency($this->repository, $code);
        $localized_currency = $currency->localize($this->getCulture());

        return array(
            'name' => $localized_currency->name,
            'symbol' => $this->getCurrencySymbol($code),
            'code' => $code,
            'iso_code' => $this->getCurrencyIsoCodeNum($code)
        );
    }

    /**
     * Return a currency symbol
     *
     * @param string $code currency iso code
     *
     * @return string symbol
     */
    public function getCurrencySymbol($code)
    {
        $locale = $this->repository->locales[$this->getCulture()];
        $currency = $locale['currencies'][$code];

        return !empty($currency['symbol-alt-narrow']) ? $currency['symbol-alt-narrow'] : $currency['symbol'];
    }

    /**
     * Return a formatted price
     *
     * @param float $price
     * @param string $code iso code
     *
     * @return string well formatted price
     */
    public function getPrice($price, $code)
    {
        $currency = new Currency($this->repository, $code);
        $localized_currency = $currency->localize($this->getCulture());

        return $localized_currency->format($price);
    }

    /**
     * Return a formatted number
     *
     * @param float $number
     *
     * @return string well formatted number
     */
    public function getNumber($number)
    {
        $formatter = new NumberFormatter($this->repository);
        $localized_formatter = $formatter->localize($this->getCulture());

        return $localized_formatter($number);
    }

    /**
     * Check if a curency iso code is valid
     *
     * @param string $str iso code
     *
     * @return bool
     */
    private function isCurrencyValid($str)
    {
        if ($str === 'XTS' || strlen($str) !==3 || empty($this->repository->supplemental['codeMappings'][$str])) {
            return false;
        }

        return true;
    }

    /**
     * Get currency format pattern
     *
     * @param string $locale locale
     *
     * @return string pattern
     */
    public function getCurrencyFormatPattern($locale = null)
    {
        $locale = $this->repository->locales[$locale ? $locale : $this->getCulture()];
        return $locale['numbers']['currencyFormats-numberSystem-latn']['standard'];
    }
}
