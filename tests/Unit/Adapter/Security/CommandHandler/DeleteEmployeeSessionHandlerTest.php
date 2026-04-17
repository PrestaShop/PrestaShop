<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Security\CommandHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Security\CommandHandler\DeleteEmployeeSessionHandler;
use PrestaShop\PrestaShop\Adapter\Session\Repository\EmployeeSessionRepository;
use PrestaShop\PrestaShop\Core\Domain\Security\Command\DeleteEmployeeSessionCommand;

class DeleteEmployeeSessionHandlerTest extends TestCase
{
    public function testHandleDeleteShouldBeCalledOnlyOnce(): void
    {
        $sessionId = new DeleteEmployeeSessionCommand(1);

        $repo = $this->getMockBuilder(EmployeeSessionRepository::class)
            ->onlyMethods([
                'delete',
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $repo->expects($this->once())
            ->method('delete')
            ->with($sessionId->getEmployeeSessionId());

        $commandHandler = new DeleteEmployeeSessionHandler($repo);
        $commandHandler->handle($sessionId);
    }
}
