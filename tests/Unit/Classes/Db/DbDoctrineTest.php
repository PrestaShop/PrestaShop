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

    public function testConnectSharesTheNativePdoConnection(): void
    {
        $pdo = $this->getMockPDO();
        $pdo->expects($this->once())->method('exec')->with('SET SESSION sql_mode = \'\'');
        $pdo->expects($this->once())->method('query');

        $db = new DbDoctrineCore($this->getConnectionMock($pdo));

        $this->assertSame($pdo, $db->connect());
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
        // Session settings (exec + query) must only run once, on the first connect().
        $pdo->expects($this->once())->method('exec');
        $pdo->expects($this->once())->method('query');

        $db = new DbDoctrineCore($this->getConnectionMock($pdo));
        $db->connect();
        $db->connect();
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
