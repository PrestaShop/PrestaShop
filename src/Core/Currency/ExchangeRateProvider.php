<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Currency;

use PrestaShop\CircuitBreaker\Contract\CircuitBreakerInterface;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Currency\Exception\CurrencyFeedException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\ExchangeRate;
use SimpleXMLElement;
use Symfony\Component\Cache\Adapter\AdapterInterface as CacheInterface;

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
    public const CURRENCY_FEED_URL = 'http://api.prestashop.com/xml/currencies.xml';

    public const CLOSED_ALLOWED_FAILURES = 3;
    public const CLOSED_TIMEOUT_SECONDS = 1;

    public const OPEN_ALLOWED_FAILURES = 3;
    public const OPEN_TIMEOUT_SECONDS = 2;
    public const OPEN_THRESHOLD_SECONDS = 3600; // 1 hour

    public const CACHE_KEY_XML = 'currency_feed.xml';

    /** @var string */
    private $currencyFeedUrl;

    /** @var string */
    private $defaultCurrencyIsoCode;

    /** @var CircuitBreakerInterface */
    private $remoteServiceProvider;

    /** @var CacheInterface */
    private $cache;

    /** @var string */
    private $sourceIsoCode;

    /** @var array */
    private $currencies = [];

    /**
     * @param string $currencyFeedUrl
     * @param string $defaultCurrencyIsoCode
     * @param CircuitBreakerInterface $remoteServiceProvider
     * @param CacheInterface $cache
     */
    public function __construct(
        $currencyFeedUrl,
        $defaultCurrencyIsoCode,
        CircuitBreakerInterface $remoteServiceProvider,
        CacheInterface $cache
    ) {
        $this->currencyFeedUrl = $currencyFeedUrl;
        $this->defaultCurrencyIsoCode = $defaultCurrencyIsoCode;
        $this->remoteServiceProvider = $remoteServiceProvider;
        $this->cache = $cache;
    }

    /**
     * @param string $currencyIsoCode
     *
     * @return DecimalNumber
     *
     * @throws CurrencyFeedException
     */
    public function getExchangeRate($currencyIsoCode)
    {
        $this->fetchCurrencyFeed();

        // Default feed currency (usually EUR)
        if ($this->defaultCurrencyIsoCode == $currencyIsoCode) {
            return ExchangeRate::getDefaultExchangeRate();
        }

        /*
         * Search for the currency rate in the source feed, this represents the rate
         * relative to the source feed (compared to the feed default currency)
         */
        $sourceRate = $this->getExchangeRateFromFeed($currencyIsoCode);

        /*
         * Fetch the exchange rate of the default currency (compared to the source currency)
         * and finally compute the asked currency rate compared to the shop default currency rate
         */
        $defaultExchangeRate = $this->getExchangeRateFromFeed($this->defaultCurrencyIsoCode);

        return $sourceRate->dividedBy($defaultExchangeRate);
    }

    /**
     * @param string $currencyIsoCode
     *
     * @return DecimalNumber
     *
     * @throws CurrencyFeedException
     */
    private function getExchangeRateFromFeed(string $currencyIsoCode)
    {
        if ($this->sourceIsoCode == $currencyIsoCode) {
            return new DecimalNumber('1.0');
        }

        if (!isset($this->currencies[$currencyIsoCode])) {
            throw new CurrencyFeedException(sprintf('Exchange rate for currency with ISO code %s was not found', $currencyIsoCode));
        }

        return $this->currencies[$currencyIsoCode];
    }

    /**
     * Fetch the currency from its url using circuit breaker, if no content was fetched
     * fallback on the cache file. This is only performed once per process, if the currencies
     * are already present then there is nothing to do.
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

        // Cache the feed
        $cacheItem = $this->cache->getItem(self::CACHE_KEY_XML);
        $cacheItem->set($feedContent);
        $this->cache->save($cacheItem);

        return $xmlFeed;
    }

    /**
     * @param SimpleXMLElement $xmlFeed
     */
    private function parseXmlFeed($xmlFeed)
    {
        $xmlCurrencies = $xmlFeed->list->currency;

        $this->sourceIsoCode = (string) $xmlFeed->source['iso_code'];
        foreach ($xmlCurrencies as $currency) {
            $this->currencies[(string) $currency['iso_code']] = new DecimalNumber((string) $currency['rate']);
        }
    }

    /**
     * @return string
     */
    private function getCachedCurrencyFeed()
    {
        $cacheItem = $this->cache->getItem(self::CACHE_KEY_XML);
        if (!$cacheItem->isHit()) {
            return '';
        }

        $feedContent = $cacheItem->get();

        return !empty($feedContent) ? $feedContent : '';
    }

    /**
     * @param SimpleXMLElement $xmlFeed
     *
     * @return bool
     */
    private function isValidXMLFeed(SimpleXMLElement $xmlFeed): bool
    {
        return (bool) count($xmlFeed->list->currency);
    }
}
