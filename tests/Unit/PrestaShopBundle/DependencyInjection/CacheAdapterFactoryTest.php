<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\DependencyInjection;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Cache\MemcacheServerManager;
use PrestaShopBundle\DependencyInjection\CacheAdapterFactory;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;

class CacheAdapterFactoryTest extends TestCase
{
    /**
     * @var CacheAdapterFactory
     */
    private $cacheAdapterFactory;

    public function setUp(): void
    {
        $this->cacheAdapterFactory = new CacheAdapterFactory();
    }

    /**
     * @dataProvider getAdapterClassesForDriver
     */
    public function testReturnValue(string $driver, string $expectedClass): void
    {
        if (
            $driver === 'apcu' && !ApcuAdapter::isSupported()
            || $driver === 'memcached' && !MemcachedAdapter::isSupported()
        ) {
            $this->markTestSkipped('apcu is not supported');
        }
        $this->assertTrue($this->cacheAdapterFactory->getCacheAdapter($driver) instanceof $expectedClass);
    }

    public function testTheMemcachedConnectionUsesTheConfiguredServers(): void
    {
        if (!MemcachedAdapter::isSupported()) {
            $this->markTestSkipped('memcached is not supported');
        }

        $serverManager = $this->createMock(MemcacheServerManager::class);
        $serverManager->method('getServers')->willReturn([
            ['id_memcached_server' => '1', 'ip' => '10.0.0.5', 'port' => '11222', 'weight' => '3'],
            ['id_memcached_server' => '2', 'ip' => 'cache.internal', 'port' => '11333', 'weight' => '1'],
        ]);

        $servers = $this->cacheAdapterFactory->createMemcachedConnection($serverManager)->getServerList();

        $this->assertSame(
            [['10.0.0.5', 11222], ['cache.internal', 11333]],
            array_map(static fn (array $server): array => [$server['host'], $server['port']], $servers)
        );
    }

    public function testLocalhostIsUsedWhenNoServerIsConfigured(): void
    {
        if (!MemcachedAdapter::isSupported()) {
            $this->markTestSkipped('memcached is not supported');
        }

        $serverManager = $this->createMock(MemcacheServerManager::class);
        $serverManager->method('getServers')->willReturn([]);

        foreach ([$serverManager, null] as $manager) {
            $servers = $this->cacheAdapterFactory->createMemcachedConnection($manager)->getServerList();
            $this->assertSame([['localhost', 11211]], array_map(static fn (array $server): array => [$server['host'], $server['port']], $servers));
        }
    }

    public function getAdapterClassesForDriver(): array
    {
        return [
            ['apcu', ApcuAdapter::class],
            ['memcached', MemcachedAdapter::class],
            ['array', ArrayAdapter::class],
            ['other', ArrayAdapter::class],
        ];
    }
}
