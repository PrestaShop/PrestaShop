<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Cache;

use ArrayObject;
use CacheMemcache;
use PHPUnit\Framework\TestCase;

/**
 * Each test defines the constants config/bootstrap.php would define, so it runs in its own process.
 *
 * @runTestsInSeparateProcesses
 *
 * @preserveGlobalState disabled
 */
class CacheQueryLifetimeTest extends TestCase
{
    private const QUERY = 'SELECT name FROM ps_product WHERE id_product = 1';

    public function testQueryResultsAreStoredWithTheConfiguredLifetime(): void
    {
        $this->defineCacheConstants();
        define('_PS_CACHE_LIFETIME_', 3600);

        [$cache, $lifetimes] = $this->storeQuery();

        $this->assertSame(3600, $lifetimes[$cache->getQueryHash(self::QUERY)]);
        $this->assertSame(0, $lifetimes[$cache->getTableMapCacheKey('ps_product')]);
    }

    public function testQueryResultsNeverExpireWithoutAConfiguredLifetime(): void
    {
        $this->defineCacheConstants();

        [$cache, $lifetimes] = $this->storeQuery();

        $this->assertSame(0, $lifetimes[$cache->getQueryHash(self::QUERY)]);
    }

    /**
     * @return array{0: CacheMemcache, 1: array<string, int>}
     */
    private function storeQuery(): array
    {
        /** @var ArrayObject<string, int> $lifetimes */
        $lifetimes = new ArrayObject();
        $cache = $this->getMockBuilder(CacheMemcache::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['_set', '_get', '_exists', '_delete', '_deleteMulti'])
            ->getMock();
        $cache->method('_get')->willReturn(false);
        $cache->method('_set')->willReturnCallback(
            static function (string $key, $value, int $ttl = 0) use ($lifetimes): bool {
                $lifetimes[$key] = $ttl;

                return true;
            }
        );

        $cache->setQuery(self::QUERY, [['name' => 'Mug']]);

        return [$cache, $lifetimes->getArrayCopy()];
    }

    private function defineCacheConstants(): void
    {
        if (!defined('_DB_PREFIX_')) {
            define('_DB_PREFIX_', 'ps_');
        }
        if (!defined('_COOKIE_IV_')) {
            define('_COOKIE_IV_', 'cookieIvValue');
        }
    }
}
