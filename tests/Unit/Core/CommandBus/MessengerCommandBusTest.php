<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\CommandBus;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShopBundle\CommandBus\MessengerCommandBus;
use Symfony\Component\Messenger\MessageBusInterface;

class MessengerCommandBusTest extends TestCase
{
    public function testIsValidImplementation()
    {
        $commandBudAdapter = new MessengerCommandBus($this->createMock(MessageBusInterface::class));

        $this->assertInstanceOf(CommandBusInterface::class, $commandBudAdapter);
    }
}
