<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Doctrine\Middleware;

use DateTime;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Connection;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Doctrine\Middleware\SetSessionTimeZoneMiddleware;

class SetSessionTimeZoneMiddlewareTest extends TestCase
{
    public function testItSetsTheSessionTimeZoneToTheCurrentPhpOffsetOnConnect(): void
    {
        $expectedStatement = "SET SESSION time_zone = '" . (new DateTime())->format('P') . "'";

        $connection = $this->createMock(Connection::class);
        $connection
            ->expects($this->once())
            ->method('exec')
            ->with($expectedStatement);

        $wrappedDriver = $this->createMock(Driver::class);
        $wrappedDriver
            ->expects($this->once())
            ->method('connect')
            ->willReturn($connection);

        $driver = (new SetSessionTimeZoneMiddleware())->wrap($wrappedDriver);
        $result = $driver->connect([]);

        // The middleware must return the very connection produced by the wrapped driver.
        $this->assertSame($connection, $result);
    }
}
