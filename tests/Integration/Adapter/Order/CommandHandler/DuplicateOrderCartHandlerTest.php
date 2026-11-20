<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Adapter\Order\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\DuplicateOrderCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DuplicateOrderCartHandlerTest extends KernelTestCase
{
    /**
     * @var object|CommandBusInterface|null
     */
    private $commandBus;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->commandBus = self::getContainer()->get('prestashop.core.command_bus');
    }

    /**
     * Regression: Cart::getCartByOrderId() returns false for an unknown order id.
     * The handler must translate that into an OrderNotFoundException instead of
     * letting a TypeError bubble up from ContextStateManager::setCart(?Cart).
     */
    public function testUnknownOrderIdThrowsOrderNotFoundException(): void
    {
        $unknownOrderId = PHP_INT_MAX;

        $this->expectException(OrderNotFoundException::class);

        $this->commandBus->handle(new DuplicateOrderCartCommand($unknownOrderId));
    }
}
