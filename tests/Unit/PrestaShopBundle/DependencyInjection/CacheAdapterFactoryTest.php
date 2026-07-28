<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\DependencyInjection;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\DependencyInjection\CacheAdapterFactory;
use ReflectionMethod;
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

    /**
     * The servers configured in Advanced Parameters > Performance decide where the adapter connects.
     * Before, the DSN was the localhost literal whatever they were set to.
     */
    public function testConfiguredServersBecomeTheConnectionDsn(): void
    {
        $servers = [
            ['ip' => '10.0.0.5', 'port' => '11222'],
            ['ip' => 'cache.internal', 'port' => 11333],
        ];

        $this->assertSame(
            ['memcached://10.0.0.5:11222', 'memcached://cache.internal:11333'],
            $this->buildServerDsn($servers)
        );
    }

    public function testLocalhostIsUsedWhenNoServerIsConfigured(): void
    {
        $this->assertSame('memcached://localhost', $this->buildServerDsn([]));
    }

    /**
     * @param array<int, array{ip: string, port: int|string}> $servers
     *
     * @return string[]|string
     */
    private function buildServerDsn(array $servers)
    {
        $method = new ReflectionMethod(CacheAdapterFactory::class, 'buildServerDsn');
        $method->setAccessible(true);

        return $method->invoke($this->cacheAdapterFactory, $servers);
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
