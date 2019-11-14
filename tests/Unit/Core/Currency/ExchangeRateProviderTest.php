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

namespace Tests\Unit\Core\Currency;

use PHPUnit\Framework\TestCase;
use PrestaShop\CircuitBreaker\Contract\CircuitBreakerInterface;
use PrestaShop\PrestaShop\Core\Currency\ExchangeRateProvider;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Cache\Adapter\AdapterInterface as CacheInterface;

class ExchangeRateProviderTest extends TestCase
{
    /** @var string */
    private $cacheDir;

    /** @var string */
    private $cacheFile;

    /** @var string */
    private $feedFilePath;

    /** @var string */
    private $feedContent;

    /** @var Filesystem */
    private $fileSystem;

    /** @var CacheInterface */
    private $cache;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
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

    /**
     * @expectedException \PrestaShop\PrestaShop\Core\Currency\Exception\CurrencyFeedException
     * @expectedExceptionMessage Currency feed could not be fetched
     */
    public function testNoFeedNoCache()
    {
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

    /**
     * @expectedException \PrestaShop\PrestaShop\Core\Currency\Exception\CurrencyFeedException
     * @expectedExceptionMessage Invalid currency XML feed
     */
    public function testInvalidFeedAndCache()
    {
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

    /**
     * @expectedException \PrestaShop\PrestaShop\Core\Currency\Exception\CurrencyFeedException
     * @expectedExceptionMessage Exchange rate for currency with ISO code XYZ was not found
     */
    public function testUnknownCurrency()
    {
        $circuitBreaker = $this->buildCircuitBreakerMock($this->feedContent, $this->feedFilePath);

        $exchangeRateProvider = new ExchangeRateProvider(
            $this->feedFilePath,
            'EUR',
            $circuitBreaker,
            $this->cache
        );

        $exchangeRateProvider->getExchangeRate('XYZ');
    }

    /**
     * @expectedException \PrestaShop\PrestaShop\Core\Currency\Exception\CurrencyFeedException
     * @expectedExceptionMessage Exchange rate for currency with ISO code XYZ was not found
     */
    public function testUnknownDefaultCurrency()
    {
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
     * @return \PHPUnit_Framework_MockObject_MockObject|CircuitBreakerInterface
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
