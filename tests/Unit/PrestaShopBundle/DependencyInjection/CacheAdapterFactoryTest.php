<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\DependencyInjection;

use PHPUnit\Framework\TestCase;
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
