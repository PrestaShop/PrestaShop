<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Currency;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\CircuitBreaker\Contract\CircuitBreakerInterface;
use PrestaShop\PrestaShop\Core\Currency\Exception\CurrencyFeedException;
use PrestaShop\PrestaShop\Core\Currency\ExchangeRateProvider;
use Symfony\Component\Cache\Adapter\AdapterInterface as CacheInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class ExchangeRateProviderTest extends TestCase
{
    /** @var string */
    private $feedFilePath;

    /** @var string */
    private $feedContent;

    /** @var CacheInterface */
    private $cache;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->cache = new ArrayAdapter();
        $this->feedFilePath = _PS_ROOT_DIR_ . '/tests/Unit/Resources/currencies-feed/currencies.xml';
        $this->feedContent = file_get_contents($this->feedFilePath);
    }

    public function testGetRateFromFeed()
    {
        $circuitBreaker = $this->buildCircuitBreakerMock($this->feedContent, $this->feedFilePath);

        $exchangeRateProvider = new ExchangeRateProvider(
            $this->feedFilePath,
            'EUR',
            $circuitBreaker,
            $this->cache
        );

        $exchangeRate = $exchangeRateProvider->getExchangeRate('ALL');
        $this->assertEquals(121.098455, $exchangeRate->round(6));

        $exchangeRate = $exchangeRateProvider->getExchangeRate('EUR');
        $this->assertEquals(1.0, $exchangeRate->round(6));

        $exchangeRate = $exchangeRateProvider->getExchangeRate('USD');
        $this->assertEquals(1.12026, $exchangeRate->round(6));
    }

    public function testGetRateFromFeedWithOtherDefault()
    {
        $circuitBreaker = $this->buildCircuitBreakerMock($this->feedContent, $this->feedFilePath);

        $exchangeRateProvider = new ExchangeRateProvider(
            $this->feedFilePath,
            'USD',
            $circuitBreaker,
            $this->cache
        );

        $exchangeRate = $exchangeRateProvider->getExchangeRate('ALL');
        $this->assertEquals(108.098526, $exchangeRate->round(6));

        $exchangeRate = $exchangeRateProvider->getExchangeRate('EUR');
        $this->assertEquals(0.892649, $exchangeRate->round(6));

        $exchangeRate = $exchangeRateProvider->getExchangeRate('USD');
        $this->assertEquals(1.0, $exchangeRate->round(6));
    }

    public function testGetRateFromFeedWithDifferentDefaultSource()
    {
        // This is basically the same file, but USD is the source reference and 42 was appended to all rates
        $this->feedFilePath = _PS_ROOT_DIR_ . '/tests/Unit/Resources/currencies-feed/currencies-usd.xml';
        $this->feedContent = file_get_contents($this->feedFilePath);

        $circuitBreaker = $this->buildCircuitBreakerMock($this->feedContent, $this->feedFilePath);

        $exchangeRateProvider = new ExchangeRateProvider(
            $this->feedFilePath,
            'USD',
            $circuitBreaker,
            $this->cache
        );

        $exchangeRate = $exchangeRateProvider->getExchangeRate('ALL');
        $this->assertEquals(42121.098455, $exchangeRate->round(6));

        $exchangeRate = $exchangeRateProvider->getExchangeRate('EUR');
        $this->assertEquals(421.0, $exchangeRate->round(6));

        $exchangeRate = $exchangeRateProvider->getExchangeRate('USD');
        $this->assertEquals(1.0, $exchangeRate->round(6));
    }

    public function testGetRateFromFeedWithDifferentDefaultSourceAndDifferentLocaleDefault()
    {
        // This is basically the same file, but USD is the source reference and 42 was appended to all rates
        $this->feedFilePath = _PS_ROOT_DIR_ . '/tests/Unit/Resources/currencies-feed/currencies-usd.xml';
        $this->feedContent = file_get_contents($this->feedFilePath);

        $circuitBreaker = $this->buildCircuitBreakerMock($this->feedContent, $this->feedFilePath);

        $exchangeRateProvider = new ExchangeRateProvider(
            $this->feedFilePath,
            'AUD',
            $circuitBreaker,
            $this->cache
        );

        $exchangeRate = $exchangeRateProvider->getExchangeRate('ALL');
        $this->assertEquals(99.895639, $exchangeRate->round(6));

        $exchangeRate = $exchangeRateProvider->getExchangeRate('EUR');
        $this->assertEquals(0.998456, $exchangeRate->round(6));

        $exchangeRate = $exchangeRateProvider->getExchangeRate('USD');
        $this->assertEquals(0.002371, $exchangeRate->round(6));
    }

    public function testFeedIsCached()
    {
        $circuitBreaker = $this->buildCircuitBreakerMock($this->feedContent, $this->feedFilePath);

        $exchangeRateProvider = new ExchangeRateProvider(
            $this->feedFilePath,
            'EUR',
            $circuitBreaker,
            $this->cache
        );

        $cacheItem = $this->cache->getItem(ExchangeRateProvider::CACHE_KEY_XML);
        $this->assertFalse($cacheItem->isHit());
        $exchangeRate = $exchangeRateProvider->getExchangeRate('EUR');
        $this->assertEquals(1.0, $exchangeRate->round(6));

        $cacheItem = $this->cache->getItem(ExchangeRateProvider::CACHE_KEY_XML);
        $this->assertTrue($cacheItem->isHit());
        $this->assertEquals($this->feedContent, $cacheItem->get());
    }

    public function testCacheFallbackAfterUnknownCall()
    {
        $unknownFilePath = 'file:://unknown.file.path.to.simulate.circuit.breaker.fail';
        $cacheItem = $this->cache->getItem(ExchangeRateProvider::CACHE_KEY_XML);
        $cacheItem->set($this->feedContent);
        $this->cache->save($cacheItem);

        $circuitBreaker = $this->buildCircuitBreakerMock('', $unknownFilePath);
        $exchangeRateProvider = new ExchangeRateProvider(
            $unknownFilePath,
            'EUR',
            $circuitBreaker,
            $this->cache
        );

        $exchangeRate = $exchangeRateProvider->getExchangeRate('ALL');
        $this->assertEquals(121.098455, $exchangeRate->round(6));

        $exchangeRate = $exchangeRateProvider->getExchangeRate('EUR');
        $this->assertEquals(1.0, $exchangeRate->round(6));

        $exchangeRate = $exchangeRateProvider->getExchangeRate('USD');
        $this->assertEquals(1.12026, $exchangeRate->round(6));
    }

    public function testCacheFallbackAfterInvalidCall()
    {
        $cacheItem = $this->cache->getItem(ExchangeRateProvider::CACHE_KEY_XML);
        $cacheItem->set($this->feedContent);
        $this->cache->save($cacheItem);

        $circuitBreaker = $this->buildCircuitBreakerMock('invalid xml', $this->feedFilePath);
        $exchangeRateProvider = new ExchangeRateProvider(
            $this->feedFilePath,
            'EUR',
            $circuitBreaker,
            $this->cache
        );

        $exchangeRate = $exchangeRateProvider->getExchangeRate('ALL');
        $this->assertEquals(121.098455, $exchangeRate->round(6));

        $exchangeRate = $exchangeRateProvider->getExchangeRate('EUR');
        $this->assertEquals(1.0, $exchangeRate->round(6));

        $exchangeRate = $exchangeRateProvider->getExchangeRate('USD');
        $this->assertEquals(1.12026, $exchangeRate->round(6));
    }

    public function testNoFeedNoCache()
    {
        $this->expectException(CurrencyFeedException::class);
        $this->expectExceptionMessage('Currency feed could not be fetched');

        $unknownFilePath = 'file:://unknown.file.path.to.simulate.circuit.breaker.fail';
        $circuitBreaker = $this->buildCircuitBreakerMock('', $unknownFilePath);
        $exchangeRateProvider = new ExchangeRateProvider(
            $unknownFilePath,
            'EUR',
            $circuitBreaker,
            $this->cache
        );

        $exchangeRateProvider->getExchangeRate('ALL');
    }

    public function testInvalidFeedAndCache()
    {
        $this->expectException(CurrencyFeedException::class);
        $this->expectExceptionMessage('Invalid currency XML feed');

        $cacheItem = $this->cache->getItem(ExchangeRateProvider::CACHE_KEY_XML);
        $cacheItem->set('invalid xml');
        $this->cache->save($cacheItem);

        $circuitBreaker = $this->buildCircuitBreakerMock('invalid xml', $this->feedFilePath);
        $exchangeRateProvider = new ExchangeRateProvider(
            $this->feedFilePath,
            'EUR',
            $circuitBreaker,
            $this->cache
        );

        $exchangeRateProvider->getExchangeRate('ALL');
    }

    public function testUnknownCurrency()
    {
        $this->expectException(CurrencyFeedException::class);
        $this->expectExceptionMessage('Exchange rate for currency with ISO code XYZ was not found');

        $circuitBreaker = $this->buildCircuitBreakerMock($this->feedContent, $this->feedFilePath);

        $exchangeRateProvider = new ExchangeRateProvider(
            $this->feedFilePath,
            'EUR',
            $circuitBreaker,
            $this->cache
        );

        $exchangeRateProvider->getExchangeRate('XYZ');
    }

    public function testUnknownDefaultCurrency()
    {
        $this->expectException(CurrencyFeedException::class);
        $this->expectExceptionMessage('Exchange rate for currency with ISO code XYZ was not found');

        $circuitBreaker = $this->buildCircuitBreakerMock($this->feedContent, $this->feedFilePath);

        $exchangeRateProvider = new ExchangeRateProvider(
            $this->feedFilePath,
            'XYZ',
            $circuitBreaker,
            $this->cache
        );

        $exchangeRateProvider->getExchangeRate('ALL');
    }

    /**
     * @param string $feedContent
     * @param string $feedUrl
     *
     * @return MockObject|CircuitBreakerInterface
     */
    private function buildCircuitBreakerMock($feedContent, $feedUrl)
    {
        $circuitBreakerMock = $this->getMockBuilder(CircuitBreakerInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $circuitBreakerMock
            ->expects($this->once())
            ->method('call')
            ->with($this->equalTo($feedUrl))
            ->willReturn($feedContent)
        ;

        return $circuitBreakerMock;
    }
}
