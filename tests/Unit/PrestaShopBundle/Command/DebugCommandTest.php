<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
