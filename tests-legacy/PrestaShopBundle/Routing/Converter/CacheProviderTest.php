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

namespace Tests\PrestaShopBundle\Routing\Converter;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Routing\Converter\CacheKeyGeneratorInterface;
use PrestaShopBundle\Routing\Converter\CacheProvider;
use PrestaShopBundle\Routing\Converter\LegacyRoute;
use PrestaShopBundle\Routing\Converter\LegacyRouteProviderInterface;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class CacheProviderTest extends TestCase
{
    const CACHE_KEY = 'test_cache_key';

    /**
     * @var array
     */
    private $expectedFlattenArray;

    /**
     * @var string
     */
    private $expectedCacheValue;

    /**
     * @var LegacyRoute[]
     */
    private $legacyRoutes;

    public function setUp()
    {
        parent::setUp();
        $this->expectedFlattenArray = [
            [
                'route_name' => 'admin_products',
                'legacy_links' => [
                    'AdminProducts',
                    'AdminProducts:index',
                    'AdminProducts:list',
                ],
                'legacy_parameters' => [],
            ],
            [
                'route_name' => 'admin_products_create',
                'legacy_links' => [
                    'AdminProducts:create',
                    'AdminProducts:new',
                ],
                'legacy_parameters' => [
                    'id_product' => 'productId',
                ],
            ],
        ];
        $this->expectedCacheValue = json_encode($this->expectedFlattenArray);

        $this->legacyRoutes = [
            'admin_products' => new LegacyRoute(
                'admin_products',
                [
                    'AdminProducts',
                    'AdminProducts:index',
                    'AdminProducts:list',
                ],
                []
            ),
            'admin_products_create' => new LegacyRoute(
                'admin_products_create',
                [
                    'AdminProducts:create',
                    'AdminProducts:new',
                ],
                [
                    'id_product' => 'productId',
                ]
            )
        ];
    }

    public function testGetLegacyRoutesAndSaveCache()
    {
        $mockProvider = $this->buildMockRouterProvider($this->legacyRoutes);
        $cacheProvider = new CacheProvider($mockProvider, $this->buildSavingCache(), $this->buildCacheKeyGenerator());

        $legacyRoutes = $cacheProvider->getLegacyRoutes();
        $this->assertCount(2, $legacyRoutes);
        $this->assertNotEmpty($legacyRoutes['admin_products']);
        $this->assertNotEmpty($legacyRoutes['admin_products_create']);

        $legacyRoutes = $cacheProvider->getLegacyRoutes();
        $this->assertCount(2, $legacyRoutes);
    }

    public function testGetFromCache()
    {
        $cacheProvider = new CacheProvider($this->buildCachedRouterProvider(), $this->buildExistingCache(), $this->buildCacheKeyGenerator());
        $legacyRoutes = $cacheProvider->getLegacyRoutes();
        $this->assertCount(2, $legacyRoutes);
        $this->assertNotEmpty($legacyRoutes['admin_products']);
        $this->assertNotEmpty($legacyRoutes['admin_products_create']);

        $legacyRoutes = $cacheProvider->getLegacyRoutes();
        $this->assertCount(2, $legacyRoutes);
    }

    public function testWithRealCache()
    {
        $cache = new ArrayAdapter();
        $mockProvider = $this->buildMockRouterProvider($this->legacyRoutes);
        $cacheProvider = new CacheProvider($mockProvider, $cache, $this->buildCacheKeyGenerator());

        $this->assertFalse($cache->hasItem(self::CACHE_KEY));
        //Just perform the test twice to be sure the result and the cache are correct
        for ($i = 0; $i < 2; $i++) {
            $legacyRoutes = $cacheProvider->getLegacyRoutes();
            $this->assertCount(2, $legacyRoutes);
            $this->assertNotEmpty($legacyRoutes['admin_products']);
            $this->assertNotEmpty($legacyRoutes['admin_products_create']);
            $this->assertTrue($cache->hasItem(self::CACHE_KEY));
            $cacheItem = $cache->getItem(self::CACHE_KEY);
            $this->assertEquals($this->expectedCacheValue, $cacheItem->get());
        }

        //Now empty the private field to force CacheProvider to call the cache
        $reflectionClass = new \ReflectionClass(CacheProvider::class);
        $routesProperty = $reflectionClass->getProperty('legacyRoutes');
        $routesProperty->setAccessible(true);
        $routesProperty->setValue($cacheProvider, null);
        $routesProperty->setAccessible(false);

        //Retry to get the value, the cache will be used hence the mockRouterProvider won't be called
        $legacyRoutes = $cacheProvider->getLegacyRoutes();
        $this->assertCount(2, $legacyRoutes);
        $this->assertNotEmpty($legacyRoutes['admin_products']);
        $this->assertNotEmpty($legacyRoutes['admin_products_create']);
    }

    public function testGetControllersActions()
    {
        $cache = new ArrayAdapter();
        $mockProvider = $this->buildMockRouterProvider($this->legacyRoutes);
        $cacheProvider = new CacheProvider($mockProvider, $cache, $this->buildCacheKeyGenerator());

        $this->assertFalse($cache->hasItem(self::CACHE_KEY));
        //Just perform the test twice to be sure the result and the cache are correct
        for ($i = 0; $i < 2; $i++) {
            $controllerActions = $cacheProvider->getControllersActions();
            $this->assertCount(1, $controllerActions);
            $this->assertNotEmpty($controllerActions['AdminProducts']);
            $this->assertNotEmpty($controllerActions['AdminProducts']['index']);
            $this->assertTrue($cache->hasItem(self::CACHE_KEY));
            $cacheItem = $cache->getItem(self::CACHE_KEY);
            $this->assertEquals($this->expectedCacheValue, $cacheItem->get());
        }

        //Now empty the private field to force CacheProvider to call the cache
        $reflectionClass = new \ReflectionClass(CacheProvider::class);
        $routesProperty = $reflectionClass->getProperty('legacyRoutes');
        $routesProperty->setAccessible(true);
        $routesProperty->setValue($cacheProvider, null);
        $routesProperty->setAccessible(false);

        //Retry to get the value, the cache will be used hence the mockRouterProvider won't be called
        $controllerActions = $cacheProvider->getControllersActions();
        $this->assertCount(1, $controllerActions);
        $this->assertNotEmpty($controllerActions['AdminProducts']);
        $this->assertNotEmpty($controllerActions['AdminProducts']['index']);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|AdapterInterface
     */
    private function buildExistingCache()
    {
        //CacheItem mock
        $itemMock = $this
            ->getMockBuilder(CacheItemInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $itemMock
            ->expects($this->once())
            ->method('isHit')
            ->willReturn(true)
        ;

        $itemMock
            ->expects($this->never())
            ->method('set')
        ;

        $itemMock
            ->expects($this->once())
            ->method('get')
            ->willReturn($this->expectedCacheValue)
        ;

        //AdapterInterface mock
        $cacheMock = $this
            ->getMockBuilder(AdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $cacheMock
            ->expects($this->once())
            ->method('getItem')
            ->willReturn($itemMock)
        ;

        $cacheMock
            ->expects($this->never())
            ->method('save')
        ;

        return $cacheMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|AdapterInterface
     */
    private function buildSavingCache()
    {
        //CacheItem mock
        $itemMock = $this
            ->getMockBuilder(CacheItemInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $itemMock
            ->expects($this->once())
            ->method('isHit')
            ->willReturn(false)
        ;

        $itemMock
            ->expects($this->once())
            ->method('set')
            ->with($this->expectedCacheValue)
        ;

        $itemMock
            ->expects($this->never())
            ->method('get')
        ;

        //AdapterInterface mock
        $cacheMock = $this
            ->getMockBuilder(AdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $cacheMock
            ->expects($this->once())
            ->method('getItem')
            ->willReturn($itemMock)
        ;

        $cacheMock
            ->expects($this->once())
            ->method('save')
        ;

        return $cacheMock;
    }

    /**
     * @param array $legacyRoutes
     * @return \PHPUnit_Framework_MockObject_MockObject|LegacyRouteProviderInterface
     */
    private function buildMockRouterProvider(array $legacyRoutes)
    {
        $providerMock = $this
            ->getMockBuilder(LegacyRouteProviderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $providerMock
            ->expects($this->once()) //Very important to assert this method is only called once to create the cache
            ->method('getLegacyRoutes')
            ->willReturn($legacyRoutes)
        ;

        return $providerMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LegacyRouteProviderInterface
     */
    private function buildCachedRouterProvider()
    {
        $providerMock = $this
            ->getMockBuilder(LegacyRouteProviderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $providerMock
            ->expects($this->never()) //Very important to assert this method is only called once to create the cache
            ->method('getLegacyRoutes')
        ;

        return $providerMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|CacheKeyGeneratorInterface
     */
    private function buildCacheKeyGenerator()
    {
        $generatorMock = $this
            ->getMockBuilder(CacheKeyGeneratorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $generatorMock
            ->method('getCacheKey')
            ->willReturn(self::CACHE_KEY)
        ;

        return $generatorMock;
    }
}
