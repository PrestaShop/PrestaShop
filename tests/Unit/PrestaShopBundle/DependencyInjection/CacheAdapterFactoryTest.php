<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\DependencyInjection;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\DependencyInjection\CacheAdapterFactory;
use ReflectionProperty;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
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

    public function getAdapterClassesForDriver(): array
    {
        return [
            ['apcu', ApcuAdapter::class],
            ['memcached', MemcachedAdapter::class],
            ['array', ArrayAdapter::class],
            ['other', ArrayAdapter::class],
        ];
    }

    /**
     * An adapter that outlives the request keeps an entry forever when its default lifetime is 0, so
     * the lifetime configured for the pool has to reach the adapter.
     */
    public function testTheConfiguredLifetimeReachesTheAdapter(): void
    {
        if (!ApcuAdapter::isSupported()) {
            $this->markTestSkipped('apcu is not supported');
        }

        $adapter = $this->cacheAdapterFactory->getCacheAdapter('apcu', 3600);

        // Reflect on the class that declares it: the property is private and comes from a trait used
        // by AbstractAdapter, so asking ApcuAdapter for it raises "property does not exist".
        $property = new ReflectionProperty(AbstractAdapter::class, 'defaultLifetime');
        $property->setAccessible(true);

        $this->assertSame(3600, (int) $property->getValue($adapter));
    }
}
