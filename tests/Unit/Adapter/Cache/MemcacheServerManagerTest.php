<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Cache;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Cache\MemcacheServerManager;

class MemcacheServerManagerTest extends TestCase
{
    /**
     * The back office calls this when testing a server and before adding one. On a shop without any
     * memcache extension it used to instantiate a class that does not exist, so both requests answered
     * with a 500 instead of reporting an unreachable server.
     */
    public function testConfigurationReportsFailureInsteadOfCrashingWithoutExtension(): void
    {
        $manager = new MemcacheServerManager($this->createMock(Connection::class), 'ps_');

        // Port 1 is refused immediately, so no connection attempt hangs.
        $result = $manager->testConfiguration('127.0.0.1', 1);

        if (extension_loaded('memcached') || extension_loaded('memcache')) {
            // With an extension present the outcome depends on what answers on that port, so only the
            // absence of a fatal error can be asserted.
            $this->assertIsBool($result);

            return;
        }

        $this->assertFalse($result);
    }
}
