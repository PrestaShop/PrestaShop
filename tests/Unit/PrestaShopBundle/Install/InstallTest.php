<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Install;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Install\Install;

class InstallTest extends TestCase
{
    /**
     * @dataProvider provideDatabaseServers
     */
    public function testItSplitsTheDatabaseServerString(string $server, array $expected): void
    {
        $this->assertSame($expected, Install::parseDatabaseServer($server));
    }

    public static function provideDatabaseServers(): iterable
    {
        yield 'host only' => [
            'mysql',
            ['host' => 'mysql', 'port' => null, 'socket' => null],
        ];

        yield 'host and port' => [
            'mysql:3306',
            ['host' => 'mysql', 'port' => '3306', 'socket' => null],
        ];

        // The socket path must not be taken for the port. Written to database_port it reaches
        // Doctrine as "port=/var/run/...", which PDO ignores - so Doctrine falls back to the
        // default socket while the legacy connection uses the configured one.
        yield 'host and socket' => [
            'localhost:/var/run/mysqld/mysqld.sock',
            ['host' => 'localhost', 'port' => null, 'socket' => '/var/run/mysqld/mysqld.sock'],
        ];

        // A colon is legal in a socket path, and DbPDO reads this string the same way.
        yield 'socket path containing a colon' => [
            'localhost:/tmp/my:sql.sock',
            ['host' => 'localhost', 'port' => null, 'socket' => '/tmp/my:sql.sock'],
        ];
    }
}
