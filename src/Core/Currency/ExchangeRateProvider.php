<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Currency;

use PrestaShop\CircuitBreaker\Contract\CircuitBreakerInterface;
use PrestaShop\PrestaShop\Core\Currency\Exception\CurrencyFeedException;
use PrestaShop\PrestaShop\Core\Foundation\Filesystem\FileSystem;
use SimpleXMLElement;

/**
 * Class ExchangeRateProvider is used to get the exchange rate of a currency (based on the default
 * currency). It uses a circuit breaker to avoid being blocked in case of network problems and it
 * saves each of its request in a cache to be able to have a fallback response.
 */
class ExchangeRateProvider
{
    /**
     * This url was set in the _PS_CURRENCY_FEED_URL_ const but it is not accessible in every
     * context because it is weirdly defined in defines_uri.inc.php So it is safer to define
     * it properly here.
     */
    const CURRENCY_FEED_URL = 'http://api.prestashop.com/xml/currencies.xml';

    const DEFAULT_EXCHANGE_RATE = 1.0;

    const CLOSED_ALLOWED_FAILURES = 3;
    const CLOSED_TIMEOUT_SECONDS = 1;

    const OPEN_ALLOWED_FAILURES = 3;
    const OPEN_TIMEOUT_SECONDS = 2;
    const OPEN_THRESHOLD_SECONDS = 3600; // 1 hour

    /** @var string */
    private $currencyFeedUrl;

    /** @var string */
    private $defaultCurrencyIsoCode;

    /** @var CircuitBreakerInterface */
    private $circuitBreaker;

    /** @var string */
    private $cacheDir;

    /** @var string */
    private $cacheFile;

    /** @var SimpleXMLElement */
    private $xmlFeed;

    /** @var SimpleXMLElement */
    private $xmlCurrencies;

    /**
     * @param string $currencyFeedUrl
     * @param string $defaultCurrencyIsoCode
     * @param CircuitBreakerInterface $circuitBreaker
     * @param string $cacheDir
     */
    public function __construct(
        $currencyFeedUrl,
        $defaultCurrencyIsoCode,
        CircuitBreakerInterface $circuitBreaker,
        $cacheDir
    ) {
        $this->currencyFeedUrl = $currencyFeedUrl;
        $this->defaultCurrencyIsoCode = $defaultCurrencyIsoCode;
        $this->circuitBreaker = $circuitBreaker;
        $this->cacheDir = $cacheDir;
        $this->cacheFile = $cacheDir . '/currency_feed.xml';
    }

    /**
     * @param string $currencyIsoCode
     *
     * @return float
     *
     * @throws CurrencyFeedException
     */
    public function getExchangeRate($currencyIsoCode)
    {
        $this->fetchCurrencyFeed();

        // Default feed currency (usually EUR)
        $sourceIsoCode = (string) ($this->xmlFeed->source['iso_code']);
        if ($this->defaultCurrencyIsoCode == $currencyIsoCode) {
            return self::DEFAULT_EXCHANGE_RATE;
        }

        // Search for the currency rate in the source feed
        if ($sourceIsoCode == $currencyIsoCode) {
            $sourceRate = 1.0;
        } else {
            foreach ($this->xmlCurrencies as $obj) {
                if ((string) ($obj['iso_code']) == $currencyIsoCode) {
                    $sourceRate = (float) $obj['rate'];

                    break;
                }
            }
        }

        if (!isset($sourceRate)) {
            throw new CurrencyFeedException(sprintf(
                'Exchange rate for currency with ISO code %s was not found',
                $currencyIsoCode
            ));
        }

        // Fetch the exchange rate of the default currency (compared to the source currency)
        $defaultExchangeRate = $this->getDefaultCurrencyRelativeExchangeRate();

        return round($sourceRate / $defaultExchangeRate, 6);
    }

    /**
     * Fetch the currency from its url using circuit breaker, if no content was fetched
     * fallback on the cache file.
     *
     * @throws CurrencyFeedException
     */
    private function fetchCurrencyFeed()
    {
        if ($this->hasValidFeed()) {
            return;
        }

        $feedData = $this->circuitBreaker->call($this->currencyFeedUrl);
        $cachedFeedData = $this->getCachedCurrencyFeed();
        if (empty($feedData) && empty($cachedFeedData)) {
            throw new CurrencyFeedException('Currency feed could not be fetched');
        }

        $this->xmlFeed = @simplexml_load_string($feedData);
        if ($this->hasValidFeed()) {
            if (!empty($feedData)) {
                if (!is_dir($this->cacheDir)) {
                    mkdir($this->cacheDir, FileSystem::DEFAULT_MODE_FOLDER);
                }
                file_put_contents($this->cacheFile, $feedData);
            }
        } else {
            //Fallback on cached feed if needed
            $this->xmlFeed = @simplexml_load_string($cachedFeedData);
        }

        if (!$this->hasValidFeed()) {
            throw new CurrencyFeedException('Invalid currency XML feed');
        }

        $this->xmlCurrencies = $this->xmlFeed->list->currency;
    }

    /**
     * @return string
     */
    private function getCachedCurrencyFeed()
    {
        if (!file_exists($this->cacheFile)) {
            return '';
        }

        $feedContent = file_get_contents($this->cacheFile);

        return false !== $feedContent ? $feedContent : '';
    }

    /**
     * @return bool
     */
    private function hasValidFeed()
    {
        return $this->xmlFeed && $this->xmlFeed->list && count($this->xmlFeed->list->currency) && $this->xmlFeed->source;
    }

    /**
     * @return float
     *
     * @throws CurrencyFeedException
     */
    private function getDefaultCurrencyRelativeExchangeRate()
    {
        $sourceIsoCode = (string) ($this->xmlFeed->source['iso_code']);

        if ($this->defaultCurrencyIsoCode == $sourceIsoCode) {
            $defaultExchangeRate = 1.0;
        } else {
            foreach ($this->xmlCurrencies as $currency) {
                if ($currency['iso_code'] == $this->defaultCurrencyIsoCode) {
                    $defaultExchangeRate = round((float) $currency['rate'], 6);

                    break;
                }
            }
        }

        if (!isset($defaultExchangeRate)) {
            throw new CurrencyFeedException(sprintf(
                'Could not find default currency %s in the currency feed',
                $this->defaultCurrencyIsoCode
            ));
        }

        return $defaultExchangeRate;
    }
}
