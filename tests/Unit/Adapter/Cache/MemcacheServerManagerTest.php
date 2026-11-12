<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Cache;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Cache\MemcacheServerManager;

/**
 * testConfiguration() falls back to `new Memcache()` when the memcached extension is absent. With neither
 * extension installed that is an Error, on the very page whose job is to report that a server does not
 * work.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/38149
 */
class MemcacheServerManagerTest extends TestCase
{
    protected function setUp(): void
    {
        if (extension_loaded('memcache') || extension_loaded('memcached')) {
            $this->markTestSkipped('This case only exists when neither memcache extension is installed');
        }
    }

    public function testItReportsAFailureRatherThanRaisingWhenNoExtensionIsInstalled(): void
    {
        $manager = new MemcacheServerManager(
            $this->createMock(\Doctrine\DBAL\Connection::class),
            _DB_PREFIX_
        );

        $this->assertFalse($manager->testConfiguration('127.0.0.1', 11211));
    }
}
