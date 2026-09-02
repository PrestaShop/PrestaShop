<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Security\CommandHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Security\CommandHandler\BulkDeleteCustomerSessionsHandler;
use PrestaShop\PrestaShop\Adapter\Session\Repository\CustomerSessionRepository;
use PrestaShop\PrestaShop\Core\Domain\Security\Command\BulkDeleteCustomerSessionsCommand;

class BulkDeleteCustomersSessionHandlerTest extends TestCase
{
    public function testHandleDeleteShouldBeCalledOnlyOnce(): void
    {
        $sessionId = new BulkDeleteCustomerSessionsCommand([1, 2, 3]);

        $repo = $this->getMockBuilder(CustomerSessionRepository::class)
            ->onlyMethods([
                'bulkDelete',
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $repo->expects($this->once())
            ->method('bulkDelete')
            ->with($sessionId->getCustomerSessionIds());

        $commandHandler = new BulkDeleteCustomerSessionsHandler($repo);
        $commandHandler->handle($sessionId);
    }
}
