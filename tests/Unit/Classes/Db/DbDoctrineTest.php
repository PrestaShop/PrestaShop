<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Db;

use DbDoctrineCore;
use Doctrine\DBAL\Connection;
use PDO;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShopException;
use stdClass;

class DbDoctrineTest extends TestCase
{
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

    /**
     * @return MockObject|Connection
     */
    private function getConnectionMock(PDO $nativeConnection)
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('getNativeConnection')->willReturn($nativeConnection);

        return $connection;
    }

    /**
     * Records every statement the given link is asked to run.
     *
     * @param MockObject|PDO $pdo
     *
     * @return string[] filled in as statements are executed
     */
    private function &recordStatements($pdo): array
    {
        $statements = [];
        $pdo->method('query')->willReturnCallback(function (string $sql) use (&$statements) {
            $statements[] = $sql;

            return false;
        });

        return $statements;
    }

    public function testConnectSharesTheNativePdoConnectionAndAlignsItsSessionSettings(): void
    {
        $pdo = $this->getMockPDO();
        $statements = &$this->recordStatements($pdo);

        $db = new DbDoctrineCore($this->getConnectionMock($pdo));

        $this->assertSame($pdo, $db->connect());
        // The shared connection was not opened by connect(), so it only gets the sql_mode and time
        // zone the legacy layer expects if they are applied explicitly here.
        $this->assertContains("SET SESSION sql_mode = ''", $statements);
        $this->assertNotEmpty(preg_grep("/^SET SESSION time_zone = '[+-]\d{2}:\d{2}'$/", $statements));
    }

    public function testConnectThrowsWhenNativeConnectionIsNotPdo(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('getNativeConnection')->willReturn(new stdClass());

        $this->expectException(PrestaShopException::class);

        (new DbDoctrineCore($connection))->connect();
    }

    public function testConnectingTwiceWithTheSameLinkDoesNotReapplySessionSettings(): void
    {
        $pdo = $this->getMockPDO();
        $statements = &$this->recordStatements($pdo);

        $db = new DbDoctrineCore($this->getConnectionMock($pdo));
        $db->connect();
        $countAfterFirstConnect = count($statements);
        $db->connect();

        $this->assertGreaterThan(0, $countAfterFirstConnect);
        $this->assertCount($countAfterFirstConnect, $statements);
    }

    public function testIsSharingDistinguishesTheInjectedConnectionFromAnotherOne(): void
    {
        $connection = $this->getConnectionMock($this->getMockPDO());
        $otherConnection = $this->getConnectionMock($this->getMockPDO());

        $db = new DbDoctrineCore($connection);

        $this->assertTrue($db->isSharing($connection));
        $this->assertFalse($db->isSharing($otherConnection));
    }
}
