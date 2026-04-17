<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Security\CommandHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Security\CommandHandler\BulkDeleteEmployeeSessionsHandler;
use PrestaShop\PrestaShop\Adapter\Session\Repository\EmployeeSessionRepository;
use PrestaShop\PrestaShop\Core\Domain\Security\Command\BulkDeleteEmployeeSessionsCommand;

class BulkDeleteEmployeesSessionHandlerTest extends TestCase
{
    public function testHandleDeleteShouldBeCalledOnlyOnce(): void
    {
        $sessionId = new BulkDeleteEmployeeSessionsCommand([1, 2, 3]);

        $repo = $this->getMockBuilder(EmployeeSessionRepository::class)
            ->onlyMethods([
                'bulkDelete',
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $repo->expects($this->once())
            ->method('bulkDelete')
            ->with($sessionId->getEmployeeSessionIds());

        $commandHandler = new BulkDeleteEmployeeSessionsHandler($repo);
        $commandHandler->handle($sessionId);
    }
}
