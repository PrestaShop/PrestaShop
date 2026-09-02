<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Debug\DebugMode;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShopBundle\Command\DebugCommand;
use Symfony\Component\Console\Tester\CommandTester;

class DebugCommandTest extends TestCase
{
    public function testExecute(): void
    {
        $command = new DebugCommand(
            $this->mockCommandBus(),
            $this->mockDebugMode()
        );
        $commandTester = new CommandTester($command);

        $this->assertEquals(DebugCommand::STATUS_OK, $commandTester->execute([]));
        $this->assertEquals(DebugCommand::STATUS_OK, $commandTester->execute(['value' => 'off']));
        $this->assertStringContainsString('Debug mode is:', $commandTester->getDisplay());

        $this->assertEquals(DebugCommand::STATUS_OK, $commandTester->execute(['value' => 'on']));
        $this->assertStringContainsString('Debug mode is:', $commandTester->getDisplay());

        $this->assertEquals(DebugCommand::STATUS_ERROR, $commandTester->execute(['value' => 'asdf']));
        $this->assertStringContainsString('Input cannot be determined', $commandTester->getDisplay());
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
