<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Cache;

use CacheMemcached;
use Memcached;
use PHPUnit\Framework\TestCase;

class CacheMemcachedLifetimeTest extends TestCase
{
    protected function setUp(): void
    {
        if (!extension_loaded('memcached')) {
            $this->markTestSkipped('memcached is not available');
        }
    }

    /**
     * @dataProvider getRelativeLifetimes
     */
    public function testARelativeLifetimeIsKept(int $lifetime): void
    {
        $this->assertSame($lifetime, $this->getStoredLifetime($lifetime));
    }

    public static function getRelativeLifetimes(): iterable
    {
        yield 'no expiry' => [0];
        yield 'one hour' => [3600];
        yield '30 days' => [30 * 86400];
    }

    public function testALifetimeAboveThirtyDaysIsStoredAsATimestamp(): void
    {
        $before = time();
        $stored = $this->getStoredLifetime(60 * 86400);

        $this->assertGreaterThanOrEqual($before + 60 * 86400, $stored);
        $this->assertLessThanOrEqual(time() + 60 * 86400, $stored);
    }

    private function getStoredLifetime(int $lifetime): int
    {
        $storedLifetime = null;
        $memcached = $this->createMock(Memcached::class);
        $memcached->method('set')->willReturnCallback(
            static function (string $key, $value, int $expiration) use (&$storedLifetime): bool {
                $storedLifetime = $expiration;

                return true;
            }
        );

        $cache = new class($memcached) extends CacheMemcached {
            public function __construct(Memcached $memcached)
            {
                $this->memcached = $memcached;
                $this->is_connected = true;
            }

            public function __destruct()
            {
            }
        };
        $cache->set('key', 'value', $lifetime);

        return $storedLifetime;
    }
}
