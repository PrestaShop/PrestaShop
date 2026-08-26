<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter;

use Db;
use DbDoctrineCore;
use DbMySQLiCore;
use DbPDOCore;
use Doctrine\DBAL\Connection;
use PDO;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\ConnectionSwitcher;
use PrestaShopException;

/**
 * Mocks its collaborators and boots no kernel, but still belongs to the Integration suite: the Unit suite's
 * bootstrap loads tests/Unit/Classes/Db/MockDb.php, which pre-declares `abstract class Db` with getInstance()
 * hardcoded to `return new MockDb()`. That bypasses Db::$instance entirely, so setInstanceForTesting() has no
 * effect there - and reading/replacing Db::$instance[0] is exactly what ConnectionSwitcher does. This needs the
 * real legacy Db class, which only the Integration bootstrap provides.
 */
class ConnectionSwitcherTest extends TestCase
{
    protected function tearDown(): void
    {
        Db::deleteTestingInstance();
        parent::tearDown();
    }

    /**
     * @return MockObject|PDO
     */
    private function getMockPDO()
    {
        return $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['exec', 'query', 'inTransaction'])
            ->getMock();
    }

    private function getConnectionMock(PDO $nativeConnection): Connection
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('getNativeConnection')->willReturn($nativeConnection);

        return $connection;
    }

    public function testSwitchConnectionReplacesTheLegacySingletonWithADbDoctrine(): void
    {
        Db::setInstanceForTesting(new DbPDOCore('', '', '', '', false));

        $db = (new ConnectionSwitcher($this->getConnectionMock($this->getMockPDO())))->switchConnection();

        $this->assertInstanceOf(DbDoctrineCore::class, $db);
        $this->assertSame($db, Db::getInstance());
    }

    public function testSwitchConnectionDoesNotReapplySessionSettingsOnSubsequentCalls(): void
    {
        Db::setInstanceForTesting(new DbPDOCore('', '', '', '', false));

        $statements = [];
        $pdo = $this->getMockPDO();
        $pdo->method('query')->willReturnCallback(function (string $sql) use (&$statements) {
            $statements[] = $sql;

            return false;
        });

        $connectionSwitcher = new ConnectionSwitcher($this->getConnectionMock($pdo));
        $first = $connectionSwitcher->switchConnection();
        $countAfterFirstCall = count($statements);
        $second = $connectionSwitcher->switchConnection();

        $this->assertSame($first, $second);
        $this->assertGreaterThan(0, $countAfterFirstCall);
        $this->assertCount($countAfterFirstCall, $statements);
    }

    public function testSwitchConnectionReturnsTheLegacyInstanceUntouchedWhenItIsNotPdoBased(): void
    {
        $mysqliDb = new DbMySQLiCore('', '', '', '', false);
        Db::setInstanceForTesting($mysqliDb);

        $db = (new ConnectionSwitcher($this->getConnectionMock($this->getMockPDO())))->switchConnection();

        $this->assertSame($mysqliDb, $db);
    }

    public function testSwitchConnectionThrowsWhenTheLegacySingletonHasAnUncommittedTransaction(): void
    {
        $existingLink = $this->getMockPDO();
        $existingLink->method('inTransaction')->willReturn(true);

        $existingDb = new DbPDOCore('', '', '', '', false);
        $existingDb->setPDO($existingLink);
        Db::setInstanceForTesting($existingDb);

        $this->expectException(PrestaShopException::class);

        (new ConnectionSwitcher($this->getConnectionMock($this->getMockPDO())))->switchConnection();
    }

    public function testSwitchConnectionRebuildsWhenTheSharedConnectionIsNoLongerTheCurrentOne(): void
    {
        // e.g. a Symfony kernel reboot (as happens between integration tests) builds a brand new
        // Doctrine connection service, but this legacy Db singleton survives across that reboot.
        $staleDb = new DbDoctrineCore($this->getConnectionMock($this->getMockPDO()));
        Db::setInstanceForTesting($staleDb);

        $currentPdo = $this->getMockPDO();
        $db = (new ConnectionSwitcher($this->getConnectionMock($currentPdo)))->switchConnection();

        $this->assertNotSame($staleDb, $db);
        $this->assertSame($currentPdo, $db->connect());
    }

    public function testSwitchConnectionThrowsWhenRebuildingWouldReplaceAConnectionWithAPendingTransaction(): void
    {
        // The pending transaction is on a *stale* connection (e.g. left over from a previous
        // kernel boot) distinct from the current one, so switchConnection() must rebuild rather
        // than reuse — and the rebuild path is exactly where this must be caught.
        $staleLink = $this->getMockPDO();
        $staleLink->method('inTransaction')->willReturn(true);

        $staleDb = new DbDoctrineCore($this->getConnectionMock($staleLink));
        $staleDb->connect();
        Db::setInstanceForTesting($staleDb);

        $this->expectException(PrestaShopException::class);

        (new ConnectionSwitcher($this->getConnectionMock($this->getMockPDO())))->switchConnection();
    }

    public function testSwitchConnectionDoesNotThrowOnAReentrantCallSharingTheSameConnection(): void
    {
        // A transactional handler dispatching another transactional command re-enters
        // switchConnection() while its own transaction is active on the shared link
        // (PDO::inTransaction() is true) — this must be a no-op reuse, not an error.
        $sharedPdo = $this->getMockPDO();
        $sharedPdo->method('inTransaction')->willReturn(true);

        $connection = $this->getConnectionMock($sharedPdo);
        $existingDb = new DbDoctrineCore($connection);
        $existingDb->connect();
        Db::setInstanceForTesting($existingDb);

        $db = (new ConnectionSwitcher($connection))->switchConnection();

        $this->assertSame($existingDb, $db);
    }
}
