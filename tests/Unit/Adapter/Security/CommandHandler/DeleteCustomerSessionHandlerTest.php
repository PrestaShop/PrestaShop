<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Security\CommandHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Security\CommandHandler\DeleteCustomerSessionHandler;
use PrestaShop\PrestaShop\Adapter\Session\Repository\CustomerSessionRepository;
use PrestaShop\PrestaShop\Core\Domain\Security\Command\DeleteCustomerSessionCommand;

class DeleteCustomerSessionHandlerTest extends TestCase
{
    public function testHandleDeleteShouldBeCalledOnlyOnce(): void
    {
        $sessionId = new DeleteCustomerSessionCommand(1);

        $repo = $this->getMockBuilder(CustomerSessionRepository::class)
            ->onlyMethods([
                'delete',
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $repo->expects($this->once())
            ->method('delete')
            ->with($sessionId->getCustomerSessionId());

        $commandHandler = new DeleteCustomerSessionHandler($repo);
        $commandHandler->handle($sessionId);
    }
}
