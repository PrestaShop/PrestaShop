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

        $pdo = $this->getMockPDO();
        $pdo->expects($this->once())->method('exec');
        $pdo->expects($this->once())->method('query');

        $connectionSwitcher = new ConnectionSwitcher($this->getConnectionMock($pdo));
        $first = $connectionSwitcher->switchConnection();
        $second = $connectionSwitcher->switchConnection();

        $this->assertSame($first, $second);
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

    public function testSwitchConnectionThrowsEvenWhenAlreadySharingADoctrineConnectionWithAPendingTransaction(): void
    {
        $sharedPdo = $this->getMockPDO();
        $sharedPdo->method('inTransaction')->willReturn(true);

        $connection = $this->getConnectionMock($sharedPdo);
        $existingDb = new DbDoctrineCore($connection);
        $existingDb->connect();
        Db::setInstanceForTesting($existingDb);

        $this->expectException(PrestaShopException::class);

        (new ConnectionSwitcher($connection))->switchConnection();
    }
}
