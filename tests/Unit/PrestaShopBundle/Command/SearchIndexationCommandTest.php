<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Search\Command\SearchIndexationCommand as SearchIndexationCommandBus;
use PrestaShopBundle\Command\SearchIndexationCommand;
use Symfony\Component\Console\Tester\CommandTester;

class SearchIndexationCommandTest extends TestCase
{
    public function testExecute(): void
    {
        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->callback(function ($command) {
                return $command instanceof SearchIndexationCommandBus
                    && $command->isFull() === false
                    && $command->getShopConstraint()->getShopId() === null
                    && $command->getShopConstraint()->getShopGroupId() === null
                    && $command->getProductId() === null;
            }));

        $command = new SearchIndexationCommand($commandBus);
        $commandTester = new CommandTester($command);

        $this->assertSame(0, $commandTester->execute([]));
    }

    public function testExecuteWithFullOption(): void
    {
        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->callback(function ($command) {
                return $command instanceof SearchIndexationCommandBus
                    && $command->isFull() === true
                    && $command->getShopConstraint()->getShopId() === null
                    && $command->getShopConstraint()->getShopGroupId() === null
                    && $command->getProductId() === null;
            }));

        $command = new SearchIndexationCommand($commandBus);
        $commandTester = new CommandTester($command);

        $this->assertSame(0, $commandTester->execute([
            '--full' => true,
        ]));
    }

    public function testExecuteWithShopIdOption(): void
    {
        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->callback(function ($command) {
                return $command instanceof SearchIndexationCommandBus
                    && $command->isFull() === false
                    && $command->getShopConstraint()->getShopId()?->getValue() === 1
                    && $command->getShopConstraint()->getShopGroupId() === null
                    && $command->getProductId() === null;
            }));

        $command = new SearchIndexationCommand($commandBus);
        $commandTester = new CommandTester($command);

        $this->assertSame(0, $commandTester->execute([
            '--shop-id' => 1,
        ]));
    }

    public function testExecuteWithShopGroupOption(): void
    {
        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->callback(function ($command) {
                return $command instanceof SearchIndexationCommandBus
                    && $command->isFull() === false
                    && $command->getShopConstraint()->getShopId() === null
                    && $command->getShopConstraint()->getShopGroupId()?->getValue() === 2
                    && $command->getProductId() === null;
            }));

        $command = new SearchIndexationCommand($commandBus);
        $commandTester = new CommandTester($command);

        $this->assertSame(0, $commandTester->execute([
            '--shop-group-id' => 2,
        ]));
    }

    public function testExecuteWithProductIdOption(): void
    {
        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->callback(function ($command) {
                return $command instanceof SearchIndexationCommandBus
                    && $command->isFull() === false
                    && $command->getShopConstraint()->getShopId() === null
                    && $command->getShopConstraint()->getShopGroupId() === null
                    && $command->getProductId() instanceof ProductId
                    && $command->getProductId()->getValue() === 123;
            }));

        $command = new SearchIndexationCommand($commandBus);
        $commandTester = new CommandTester($command);

        $this->assertSame(0, $commandTester->execute([
            '--product-id' => 123,
        ]));
    }

    public function testExecuteWithAllOptions(): void
    {
        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->callback(function ($command) {
                return $command instanceof SearchIndexationCommandBus
                    && $command->isFull() === true
                    && $command->getShopConstraint()->getShopId()?->getValue() === 1
                    && $command->getShopConstraint()->getShopGroupId() === null
                    && $command->getProductId() instanceof ProductId
                    && $command->getProductId()->getValue() === 456;
            }));

        $command = new SearchIndexationCommand($commandBus);
        $commandTester = new CommandTester($command);

        $this->assertSame(0, $commandTester->execute([
            '--full' => true,
            '--shop-id' => 1,
            '--product-id' => 456,
        ]));
    }

    public function testExecuteWithShopGroupAndProductOptions(): void
    {
        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->callback(function ($command) {
                return $command instanceof SearchIndexationCommandBus
                    && $command->isFull() === false
                    && $command->getShopConstraint()->getShopId() === null
                    && $command->getShopConstraint()->getShopGroupId()?->getValue() === 3
                    && $command->getProductId() instanceof ProductId
                    && $command->getProductId()->getValue() === 789;
            }));

        $command = new SearchIndexationCommand($commandBus);
        $commandTester = new CommandTester($command);

        $this->assertSame(0, $commandTester->execute([
            '--shop-group-id' => 3,
            '--product-id' => 789,
        ]));
    }
}
