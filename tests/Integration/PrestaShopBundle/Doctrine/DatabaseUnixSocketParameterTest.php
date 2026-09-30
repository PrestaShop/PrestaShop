<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Doctrine;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The socket itself cannot be exercised here - the test suite reaches MySQL over TCP - so what
 * is pinned instead is the property that keeps every existing shop working: with no socket
 * configured the option must vanish from the DSN entirely.
 */
class DatabaseUnixSocketParameterTest extends KernelTestCase
{
    public function testTheParameterIsNullWhenNoSocketIsConfigured(): void
    {
        self::bootKernel();

        // parameters.php predates this option and carries no such key, and the installer writes
        // an unset value as ''. Container compilation must survive both and resolve to null.
        $this->assertNull(self::getContainer()->getParameter('database_unix_socket'));
    }

    public function testTheConnectionCarriesNoSocketWhenNoneIsConfigured(): void
    {
        self::bootKernel();

        /** @var Connection $connection */
        $connection = self::getContainer()->get('doctrine.dbal.default_connection');

        // Doctrine builds the DSN with isset(): null is skipped, but '' would append
        // "unix_socket=;" and break every TCP connection.
        $this->assertNull(
            $connection->getParams()['unix_socket'] ?? null,
            'A shop with no socket configured must produce the same DSN as before.'
        );
    }
}
