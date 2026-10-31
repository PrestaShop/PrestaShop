<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Command;

use Context;
use Employee;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Module\Configuration\ModuleSelfConfigurator;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\ContextBuilderPreparer;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\ModuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Module\Query\GetModuleInfos;
use PrestaShop\PrestaShop\Core\Domain\Module\QueryResult\ModuleInfos;
use PrestaShop\PrestaShop\Core\Module\ModuleManager;
use PrestaShopBundle\Command\ModuleCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Contracts\Translation\TranslatorInterface;

class ModuleCommandTest extends TestCase
{
    private CommandBusInterface&MockObject $queryBus;
    private CommandTester $tester;

    protected function setUp(): void
    {
        $this->queryBus = $this->createMock(CommandBusInterface::class);

        $legacyContext = $this->createMock(LegacyContext::class);
        $context = $this->createMock(Context::class);
        // Set an employee so the command does not try to build a legacy one.
        $context->employee = $this->createMock(Employee::class);
        $legacyContext->method('getContext')->willReturn($context);

        $configuration = $this->createMock(Configuration::class);
        $configuration->method('get')->with('PS_LANG_DEFAULT')->willReturn(1);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnCallback(
            static fn (string $id, array $parameters = []) => strtr($id, $parameters)
        );

        $command = new ModuleCommand(
            $translator,
            $legacyContext,
            $this->createMock(ModuleSelfConfigurator::class),
            $this->createMock(ModuleManager::class),
            $this->createMock(ContextBuilderPreparer::class),
            $configuration,
            $this->queryBus,
        );
        // The command relies on the "formatter" helper provided by the default helper set.
        $command->setApplication(new Application());

        $this->tester = new CommandTester($command);
    }

    public function testStatusDisplaysAnInstalledAndEnabledModule(): void
    {
        $this->expectQueryFor('ps_facetedsearch', new ModuleInfos(42, 'ps_facetedsearch', '3.16.1', '3.16.0', true, true));

        $exitCode = $this->tester->execute(['action' => 'status', 'module name' => 'ps_facetedsearch']);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $display = $this->tester->getDisplay();
        $this->assertStringContainsString('ps_facetedsearch', $display);
        $this->assertMatchesRegularExpression('/Installed\s+\|\s+yes/', $display);
        $this->assertMatchesRegularExpression('/Enabled\s+\|\s+yes/', $display);
        $this->assertMatchesRegularExpression('/Version \(disk\)\s+\|\s+3\.16\.1/', $display);
        $this->assertMatchesRegularExpression('/Version \(installed\)\s+\|\s+3\.16\.0/', $display);
    }

    public function testStatusDisplaysDashesForAModuleOnDiskButNotInstalled(): void
    {
        $this->expectQueryFor('ps_checkout', new ModuleInfos(null, 'ps_checkout', '2.0.0', null, false, false));

        $exitCode = $this->tester->execute(['action' => 'status', 'module name' => 'ps_checkout']);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $display = $this->tester->getDisplay();
        $this->assertMatchesRegularExpression('/Module ID\s+\|\s+-/', $display);
        $this->assertMatchesRegularExpression('/Installed\s+\|\s+no/', $display);
        $this->assertMatchesRegularExpression('/Enabled\s+\|\s+no/', $display);
        $this->assertMatchesRegularExpression('/Version \(installed\)\s+\|\s+-/', $display);
    }

    public function testStatusOutputsJsonWhenTheJsonOptionIsPassed(): void
    {
        $this->expectQueryFor('ps_facetedsearch', new ModuleInfos(42, 'ps_facetedsearch', '3.16.1', '3.16.0', false, true));

        $exitCode = $this->tester->execute(['action' => 'status', 'module name' => 'ps_facetedsearch', '--json' => true]);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertSame(
            [
                'technical_name' => 'ps_facetedsearch',
                'module_id' => 42,
                'installed' => true,
                'enabled' => false,
                'version' => '3.16.1',
                'installed_version' => '3.16.0',
            ],
            json_decode(trim($this->tester->getDisplay()), true)
        );
    }

    public function testStatusFailsWhenTheModuleIsNotFoundOnDisk(): void
    {
        $this->queryBus
            ->expects($this->once())
            ->method('handle')
            ->willThrowException(new ModuleNotFoundException());

        $exitCode = $this->tester->execute(['action' => 'status', 'module name' => 'unknown_module']);

        $this->assertSame(Command::FAILURE, $exitCode);
        $this->assertStringContainsString('Module unknown_module was not found on disk.', $this->tester->getDisplay());
    }

    public function testItRejectsAnUnknownAction(): void
    {
        $this->queryBus->expects($this->never())->method('handle');

        $exitCode = $this->tester->execute(['action' => 'explode', 'module name' => 'ps_facetedsearch']);

        $this->assertSame(Command::FAILURE, $exitCode);
        $this->assertStringContainsString('Unknown module action', $this->tester->getDisplay());
    }

    private function expectQueryFor(string $technicalName, ModuleInfos $moduleInfos): void
    {
        $this->queryBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->callback(
                static fn (GetModuleInfos $query) => $query->getTechnicalName()->getValue() === $technicalName
            ))
            ->willReturn($moduleInfos);
    }
}
