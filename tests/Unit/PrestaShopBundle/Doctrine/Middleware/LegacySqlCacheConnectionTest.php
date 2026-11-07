<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Doctrine\Middleware;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Cache\LegacySqlCacheInvalidatorInterface;
use PrestaShopBundle\Doctrine\Middleware\LegacySqlCacheConnection;

class LegacySqlCacheConnectionTest extends TestCase
{
    private const UPDATE = 'UPDATE ps_link_block SET id_hook = 2 WHERE id_link_block = 1';
    private const SELECT = 'SELECT id_link_block FROM ps_link_block WHERE id_hook = 2';

    /**
     * A statement with no parameter reaches the driver through exec().
     */
    public function testItInvalidatesAStatementExecutedDirectly(): void
    {
        $invalidator = $this->createMock(LegacySqlCacheInvalidatorInterface::class);
        $invalidator->expects($this->once())->method('invalidate')->with(self::UPDATE);

        $wrapped = $this->createMock(Connection::class);
        $wrapped->method('exec')->willReturn(1);

        $this->assertSame(1, (new LegacySqlCacheConnection($wrapped, $invalidator))->exec(self::UPDATE));
    }

    /**
     * A statement carrying parameters reaches the driver through prepare() and Statement::execute().
     * This is the path a Doctrine QueryBuilder update takes, which is the reported case.
     */
    public function testItInvalidatesAPreparedStatementWhenItIsExecuted(): void
    {
        $invalidator = $this->createMock(LegacySqlCacheInvalidatorInterface::class);
        $invalidator->method('shouldInvalidate')->willReturn(true);
        $invalidator->expects($this->once())->method('invalidate')->with(self::UPDATE);

        $result = $this->createMock(Result::class);
        $statement = $this->createMock(Statement::class);
        $statement->method('execute')->willReturn($result);

        $wrapped = $this->createMock(Connection::class);
        $wrapped->method('prepare')->willReturn($statement);

        $prepared = (new LegacySqlCacheConnection($wrapped, $invalidator))->prepare(self::UPDATE);

        $this->assertSame($result, $prepared->execute());
    }

    /**
     * Preparing a write must not invalidate on its own: nothing has changed until it is executed.
     */
    public function testPreparingAWriteInvalidatesNothingUntilItRuns(): void
    {
        $invalidator = $this->createMock(LegacySqlCacheInvalidatorInterface::class);
        $invalidator->method('shouldInvalidate')->willReturn(true);
        $invalidator->expects($this->never())->method('invalidate');

        $wrapped = $this->createMock(Connection::class);
        $wrapped->method('prepare')->willReturn($this->createMock(Statement::class));

        (new LegacySqlCacheConnection($wrapped, $invalidator))->prepare(self::UPDATE);
    }

    /**
     * A read changes nothing, so it must be handed back untouched rather than wrapped.
     */
    public function testItLeavesAPreparedReadAlone(): void
    {
        $invalidator = $this->createMock(LegacySqlCacheInvalidatorInterface::class);
        $invalidator->method('shouldInvalidate')->willReturn(false);
        $invalidator->expects($this->never())->method('invalidate');

        $statement = $this->createMock(Statement::class);
        $statement->method('execute')->willReturn($this->createMock(Result::class));

        $wrapped = $this->createMock(Connection::class);
        $wrapped->method('prepare')->willReturn($statement);

        $prepared = (new LegacySqlCacheConnection($wrapped, $invalidator))->prepare(self::SELECT);
        $prepared->execute();

        $this->assertSame($statement, $prepared);
    }
}
