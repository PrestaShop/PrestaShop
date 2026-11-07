<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\db;

use Db;
use PHPUnit\Framework\TestCase;

class DbTest extends TestCase
{
    /**
     * @var Db
     */
    private $firstSlave;

    /**
     * @var Db
     */
    private $secondSlave;

    /**
     * @var Db
     */
    private $master;

    protected function tearDown(): void
    {
        Db::$_slave_servers_loaded = false;
        Db::$_servers = [];
        Db::$instance = [];
    }

    public function testGetInstanceShouldLoadSlavesCorrectlyEvenWhenMasterIsCalled(): void
    {
        $this->loadSlaves(2);

        $this->master = Db::getInstance();

        // When
        $this->firstSlave = Db::getInstance((bool) _PS_USE_SQL_SLAVE_);
        $this->secondSlave = Db::getInstance((bool) _PS_USE_SQL_SLAVE_);

        // Then
        $this->assertNotSame($this->firstSlave, $this->secondSlave);
        $this->assertNotSame($this->master, $this->secondSlave);
        $this->assertNotSame($this->master, $this->firstSlave);

        $this->assertTwoCallsOnFirst_ThenOneOnSecondSlave();

        $this->assertSame($this->master, Db::getInstance());

        $this->assertTwoCallsOnFirst_ThenOneOnSecondSlave();
        $this->assertTwoCallsOnFirst_ThenOneOnSecondSlave();
    }

    /**
     * Insert_ID() is PDO::lastInsertId(), a per-connection property: only the connection that
     * performed the INSERT knows the generated id. Reading it from a slave instance yields 0,
     * silently losing the id — which is why an INSERT and its Insert_ID() must share a connection.
     *
     * No replication is needed to show this: the slave here points at the same server, so the
     * result depends solely on which connection is asked.
     */
    public function testInsertIdIsOnlyKnownByTheConnectionThatInserted(): void
    {
        $this->loadSlaves(1);

        $master = Db::getInstance();
        $slave = Db::getInstance((bool) _PS_USE_SQL_SLAVE_);
        $this->assertNotSame($master, $slave);

        $table = _DB_PREFIX_ . 'test_insert_id';
        $master->execute('DROP TABLE IF EXISTS `' . $table . '`');
        $master->execute(
            'CREATE TABLE `' . $table . '` (`id` INT AUTO_INCREMENT PRIMARY KEY, `label` VARCHAR(8))'
            . ' ENGINE=' . _MYSQL_ENGINE_
        );

        try {
            $master->execute('INSERT INTO `' . $table . "` (`label`) VALUES ('a')");

            $this->assertGreaterThan(0, (int) $master->Insert_ID());
            $this->assertSame(0, (int) $slave->Insert_ID());
        } finally {
            $master->execute('DROP TABLE IF EXISTS `' . $table . '`');
        }
    }

    private function assertTwoCallsOnFirst_ThenOneOnSecondSlave(): void
    {
        // Third and fourth calls are on first slave
        $this->assertSame($this->firstSlave, Db::getInstance((bool) _PS_USE_SQL_SLAVE_));
        $this->assertSame($this->firstSlave, Db::getInstance((bool) _PS_USE_SQL_SLAVE_));

        // Fifth call is on second slave
        $this->assertSame($this->secondSlave, Db::getInstance(_PS_USE_SQL_SLAVE_));
    }

    private function loadSlaves(int $nbServers = 0): void
    {
        Db::$_slave_servers_loaded = true;
        Db::$_servers = [];
        for ($i = 0; $i <= $nbServers; ++$i) {
            Db::$_servers[] = ['server' => _DB_SERVER_, 'user' => _DB_USER_, 'password' => _DB_PASSWD_, 'database' => _DB_NAME_];
        }
    }
}
