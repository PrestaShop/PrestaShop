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
use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Core\Currency\Exception\CurrencyFeedException;
use PrestaShop\PrestaShop\Core\Foundation\Filesystem\FileSystem;
use SimpleXMLElement;

/**
 * Retrieves the exchange rate of a currency (based on the default currency). It uses a circuit breaker
 * to avoid being blocked in case of network problems and it saves each of its request in a cache to be
 * able to have a fallback response.
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
    private $remoteServiceProvider;

    /** @var string */
    private $cacheDir;

    /** @var string */
    private $cacheFile;

    /** @var string */
    private $sourceIsoCode;

    /** @var array */
    private $currencies = [];

    /**
     * @param string $currencyFeedUrl
     * @param string $defaultCurrencyIsoCode
     * @param CircuitBreakerInterface $remoteServiceProvider
     * @param string $cacheDir
     */
    public function __construct(
        $currencyFeedUrl,
        $defaultCurrencyIsoCode,
        CircuitBreakerInterface $remoteServiceProvider,
        $cacheDir
    ) {
        $this->currencyFeedUrl = $currencyFeedUrl;
        $this->defaultCurrencyIsoCode = $defaultCurrencyIsoCode;
        $this->remoteServiceProvider = $remoteServiceProvider;
        $this->cacheDir = $cacheDir;
        $this->cacheFile = $cacheDir . '/currency_feed.xml';
    }

    /**
     * @param string $currencyIsoCode
     *
     * @return Number
     *
     * @throws CurrencyFeedException
     */
    public function getExchangeRate($currencyIsoCode)
    {
        $this->fetchCurrencyFeed();

        // Default feed currency (usually EUR)
        if ($this->defaultCurrencyIsoCode == $currencyIsoCode) {
            return new Number((string) self::DEFAULT_EXCHANGE_RATE);
        }

        /*
         * Search for the currency rate in the source feed, this represents the rate
         * relative to the source feed (compared to the feed default currency)
         */
        $sourceRate = null;
        if ($this->sourceIsoCode == $currencyIsoCode) {
            $sourceRate = new Number((string) 1.0);
        } else {
            foreach ($this->currencies as $currency) {
                if ($currency['iso_code'] == $currencyIsoCode) {
                    $sourceRate = $currency['rate'];

                    break;
                }
            }
        }

        if (null === $sourceRate) {
            throw new CurrencyFeedException(sprintf(
                'Exchange rate for currency with ISO code %s was not found',
                $currencyIsoCode
            ));
        }

        /*
         * Fetch the exchange rate of the default currency (compared to the source currency)
         * and finally compute the asked currency rate compared to the shop default currency rate
         */
        $defaultExchangeRate = $this->getDefaultCurrencyRelativeExchangeRate();

        return $sourceRate->dividedBy($defaultExchangeRate);
    }

    /**
     * Fetch the currency from its url using circuit breaker, if no content was fetched
     * fallback on the cache file. If the feed has already been fetch there is nothing to do.
     *
     * @throws CurrencyFeedException
     */
    private function fetchCurrencyFeed()
    {
        if (!empty($this->currencies)) {
            return;
        }

        $remoteFeedData = $this->remoteServiceProvider->call($this->currencyFeedUrl);
        $cachedFeedData = $this->getCachedCurrencyFeed();
        if (empty($remoteFeedData) && empty($cachedFeedData)) {
            throw new CurrencyFeedException('Currency feed could not be fetched');
        }

        $xmlFeed = $this->parseAndSaveXMLFeed($remoteFeedData);
        if (null === $xmlFeed) {
            $xmlFeed = $this->parseAndSaveXMLFeed($cachedFeedData);
        }

        if (null === $xmlFeed) {
            throw new CurrencyFeedException('Invalid currency XML feed');
        }

        $this->parseXmlFeed($xmlFeed);
    }

    /**
     * @return SimpleXMLElement|null
     */
    private function fetchRemoteFeed()
    {
        $remoteFeedData = $this->remoteServiceProvider->call($this->currencyFeedUrl);
        if (empty($remoteFeedData)) {
            return null;
        }

        return $this->parseAndSaveXMLFeed($remoteFeedData);
    }

    /**
     * @return SimpleXMLElement|null
     */
    private function fetchCachedFeed()
    {
        $cachedFeedData = $this->getCachedCurrencyFeed();
        if (empty($cachedFeedData)) {
            return null;
        }

        return $this->parseAndSaveXMLFeed($cachedFeedData);
    }

    /**
     * @param string $feedContent
     *
     * @return SimpleXMLElement|null
     */
    private function parseAndSaveXMLFeed($feedContent)
    {
        $xmlFeed = @simplexml_load_string($feedContent);
        if (!$xmlFeed || !$this->isValidXMLFeed($xmlFeed)) {
            return null;
        }

        //Cache the feed
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, FileSystem::DEFAULT_MODE_FOLDER);
        }
        file_put_contents($this->cacheFile, $feedContent);

        return $xmlFeed;
    }

    /**
     * @param SimpleXMLElement $xmlFeed
     */
    private function parseXmlFeed($xmlFeed)
    {
        $xmlCurrencies = $xmlFeed->list->currency;

        $this->sourceIsoCode = (string) ($xmlFeed->source['iso_code']);
        foreach ($xmlCurrencies as $currency) {
            $this->currencies[] = [
                'iso_code' => (string) $currency['iso_code'],
                'rate' => new Number((string) $currency['rate']),
            ];
        }
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
     * @param SimpleXMLElement $xmlFeed
     *
     * @return bool
     */
    private function isValidXMLFeed(SimpleXMLElement $xmlFeed)
    {
        return $xmlFeed && $xmlFeed->list && count($xmlFeed->list->currency) && $xmlFeed->source;
    }

    /**
     * @return Number
     *
     * @throws CurrencyFeedException
     */
    private function getDefaultCurrencyRelativeExchangeRate()
    {
        $defaultExchangeRate = null;
        if ($this->defaultCurrencyIsoCode == $this->sourceIsoCode) {
            $defaultExchangeRate = new Number('1.0');
        } else {
            foreach ($this->currencies as $currency) {
                if ($currency['iso_code'] == $this->defaultCurrencyIsoCode) {
                    $defaultExchangeRate = $currency['rate'];

                    break;
                }
            }
        }

        if (null === $defaultExchangeRate) {
            throw new CurrencyFeedException(sprintf(
                'Could not find default currency %s in the currency feed',
                $this->defaultCurrencyIsoCode
            ));
        }

        return $defaultExchangeRate;
    }
}
