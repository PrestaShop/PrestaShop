<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\db;

use Db;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Ensures both database connection layers (legacy Db and Doctrine DBAL) apply the same MySQL session
 * sql_mode, taken from _PS_MYSQL_SESSION_SQL_MODE_. A shop runs legacy and Doctrine writes against the
 * same tables - and since 9.2 they can even share one physical connection - so a different sql_mode per
 * layer would mean the same data is validated by different rules depending on which layer writes it.
 */
class DbSetSqlModeTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    public function testLegacyConnectionUsesTheConfiguredSqlMode(): void
    {
        $db = Db::getInstance();
        // Re-apply explicitly so the test does not depend on bootstrap ordering.
        $db->setSqlMode();

        $this->assertSame(
            _PS_MYSQL_SESSION_SQL_MODE_,
            $db->getValue('SELECT @@session.sql_mode')
        );
    }

    public function testDoctrineConnectionUsesTheConfiguredSqlMode(): void
    {
        /** @var Connection $connection */
        $connection = self::getContainer()->get('doctrine.dbal.default_connection');

        $this->assertSame(
            _PS_MYSQL_SESSION_SQL_MODE_,
            $connection->fetchOne('SELECT @@session.sql_mode')
        );
    }

    public function testBothLayersAgreeOnTheSqlMode(): void
    {
        $db = Db::getInstance();
        $db->setSqlMode();

        /** @var Connection $connection */
        $connection = self::getContainer()->get('doctrine.dbal.default_connection');

        $this->assertSame(
            $db->getValue('SELECT @@session.sql_mode'),
            $connection->fetchOne('SELECT @@session.sql_mode')
        );
    }
}
