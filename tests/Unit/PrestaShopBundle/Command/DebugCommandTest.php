<?php

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Debug\DebugMode;
use PrestaShop\PrestaShop\Adapter\LegacyContextLoader;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShopBundle\Command\DebugCommand;
use Symfony\Component\Console\Tester\CommandTester;

class DebugCommandTest extends TestCase
{
    public function testExecute(): void
    {
        $command = new DebugCommand(
            $this->mockCommandBus(),
            $this->mockDebugMode(),
            $this->mockLegacyContextLoader()
        );
        $commandTester = new CommandTester($command);

        $this->assertEquals(DebugCommand::STATUS_OK, $commandTester->execute([]));
        $this->assertEquals(DebugCommand::STATUS_OK, $commandTester->execute(['value' => 'off']));
        $this->assertEquals(DebugCommand::STATUS_OK, $commandTester->execute(['value' => 'on']));
        $this->assertEquals(DebugCommand::STATUS_ERROR, $commandTester->execute(['value' => 'asdf']));
    }

    protected function mockLegacyContextLoader(): LegacyContextLoader
    {
        $legacyContextLoaderMock = $this->getMockBuilder(LegacyContextLoader::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $legacyContextLoaderMock;
    }

    protected function mockDebugMode(): DebugMode
    {
        $debugModeMock = $this->getMockBuilder(DebugMode::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $debugModeMock;
    }

    protected function mockCommandBus(): CommandBusInterface
    {
        $commandBusMock = $this->getMockBuilder(CommandBusInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $commandBusMock;
    }
}
