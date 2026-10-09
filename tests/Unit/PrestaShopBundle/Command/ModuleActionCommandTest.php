<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Command;

use Context;
use Employee;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Context\ContextBuilderPreparer;
use PrestaShop\PrestaShop\Core\Module\ModuleManager;
use PrestaShopBundle\Command\DeleteModuleCommand;
use PrestaShopBundle\Command\DisableModuleCommand;
use PrestaShopBundle\Command\EnableModuleCommand;
use PrestaShopBundle\Command\InstallModuleCommand;
use PrestaShopBundle\Command\ResetModuleCommand;
use PrestaShopBundle\Command\UninstallModuleCommand;
use PrestaShopBundle\Command\UpgradeModuleCommand;
use ReflectionClass;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Contracts\Translation\TranslatorInterface;

class ModuleActionCommandTest extends TestCase
{
    public function testInstallOneModule(): void
    {
        [$translator, $context, $preparer, $configuration] = $this->dependencies();
        $manager = $this->getMockBuilder(ModuleManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects($this->once())->method('install')->with('first')->willReturn(true);

        $tester = $this->createCommandTester(new InstallModuleCommand($translator, $context, $preparer, $configuration, $manager));
        $this->assertSame(Command::SUCCESS, $tester->execute(['modules' => ['first']]));
        $this->assertStringContainsString('first', $tester->getDisplay());
    }

    public function testBulkActionContinuesAfterFailure(): void
    {
        [$translator, $context, $preparer, $configuration] = $this->dependencies();
        $manager = $this->getMockBuilder(ModuleManager::class)->disableOriginalConstructor()->getMock();
        $seen = [];
        $manager->method('install')->willReturnCallback(static function (string $module) use (&$seen): bool {
            $seen[] = $module;

            return $module !== 'broken';
        });
        $manager->method('getError')->willReturn('failed');

        $tester = $this->createCommandTester(new InstallModuleCommand($translator, $context, $preparer, $configuration, $manager));
        $this->assertSame(Command::FAILURE, $tester->execute(['modules' => ['first', 'broken', 'last']]));
        $this->assertSame(['first', 'broken', 'last'], $seen);
        foreach (['first', 'broken', 'last'] as $name) {
            $this->assertStringContainsString($name, $tester->getDisplay());
        }
    }

    public function testBulkActionContinuesAfterException(): void
    {
        [$translator, $context, $preparer, $configuration] = $this->dependencies();
        $manager = $this->getMockBuilder(ModuleManager::class)->disableOriginalConstructor()->getMock();
        $seen = [];
        $manager->method('install')->willReturnCallback(static function (string $module) use (&$seen): bool {
            $seen[] = $module;
            if ($module === 'broken') {
                throw new RuntimeException('expected failure');
            }

            return true;
        });

        $tester = $this->createCommandTester(new InstallModuleCommand($translator, $context, $preparer, $configuration, $manager));
        $this->assertSame(Command::FAILURE, $tester->execute(['modules' => ['broken', 'last']]));
        $this->assertSame(['broken', 'last'], $seen);
        $this->assertStringContainsString('expected failure', $tester->getDisplay());
    }

    public function testSkipOverridesIsRestoredAfterAction(): void
    {
        [$translator, $context, $preparer, $configuration] = $this->dependencies();
        $changes = [];
        $configuration->expects($this->exactly(2))->method('setTemporary')
            ->willReturnCallback(static function (string $key, $value) use (&$changes): void {
                $changes[] = [$key, $value];
            });
        $manager = $this->getMockBuilder(ModuleManager::class)->disableOriginalConstructor()->getMock();
        $manager->method('disable')->willReturn(true);

        $tester = $this->createCommandTester(new DisableModuleCommand($translator, $context, $preparer, $configuration, $manager));
        $this->assertSame(Command::SUCCESS, $tester->execute(['modules' => ['first'], '--skip-overrides' => true]));
        $this->assertSame([
            ['PS_DISABLE_MODULE_OVERRIDES', 1],
            ['PS_DISABLE_MODULE_OVERRIDES', 0],
        ], $changes);
    }

    /** @dataProvider moduleActionsProvider */
    public function testEachConcreteCommandCallsItsMatchingManagerAction(string $commandClass, string $action): void
    {
        [$translator, $context, $preparer, $configuration] = $this->dependencies();
        $manager = $this->getMockBuilder(ModuleManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects($this->once())->method($action)->with('first')->willReturn(true);

        $tester = $this->createCommandTester(new $commandClass($translator, $context, $preparer, $configuration, $manager));
        $this->assertSame(Command::SUCCESS, $tester->execute(['modules' => ['first']]));
        $this->assertStringContainsString('first', $tester->getDisplay());
    }

    public static function moduleActionsProvider(): iterable
    {
        yield 'install' => [InstallModuleCommand::class, 'install'];
        yield 'uninstall' => [UninstallModuleCommand::class, 'uninstall'];
        yield 'enable' => [EnableModuleCommand::class, 'enable'];
        yield 'disable' => [DisableModuleCommand::class, 'disable'];
        yield 'reset' => [ResetModuleCommand::class, 'reset'];
        yield 'upgrade' => [UpgradeModuleCommand::class, 'upgrade'];
        yield 'delete' => [DeleteModuleCommand::class, 'delete'];
    }

    public function testBulkActionSuccess(): void
    {
        [$translator, $context, $preparer, $configuration] = $this->dependencies();
        $manager = $this->getMockBuilder(ModuleManager::class)->disableOriginalConstructor()->getMock();
        $seen = [];
        $manager->method('install')->willReturnCallback(static function (string $module) use (&$seen): bool {
            $seen[] = $module;

            return true;
        });

        $tester = $this->createCommandTester(new InstallModuleCommand($translator, $context, $preparer, $configuration, $manager));
        $this->assertSame(Command::SUCCESS, $tester->execute(['modules' => ['first', 'second']]));
        $this->assertSame(['first', 'second'], $seen);
        $this->assertStringContainsString('first', $tester->getDisplay());
        $this->assertStringContainsString('second', $tester->getDisplay());
    }

    public function testSkipOverridesIsRestoredAfterException(): void
    {
        [$translator, $context, $preparer, $configuration] = $this->dependencies();
        $changes = [];
        $configuration->expects($this->exactly(2))->method('setTemporary')
            ->willReturnCallback(static function (string $key, $value) use (&$changes): void {
                $changes[] = [$key, $value];
            });
        $manager = $this->getMockBuilder(ModuleManager::class)->disableOriginalConstructor()->getMock();
        $manager->method('disable')->willThrowException(new RuntimeException('expected failure'));

        $tester = $this->createCommandTester(new DisableModuleCommand($translator, $context, $preparer, $configuration, $manager));
        $this->assertSame(Command::FAILURE, $tester->execute(['modules' => ['broken'], '--skip-overrides' => true]));
        $this->assertSame([
            ['PS_DISABLE_MODULE_OVERRIDES', 1],
            ['PS_DISABLE_MODULE_OVERRIDES', 0],
        ], $changes);
        $this->assertStringContainsString('expected failure', $tester->getDisplay());
    }

    public function testManagerErrorIsShownForFailedModule(): void
    {
        [$translator, $context, $preparer, $configuration] = $this->dependencies();
        $manager = $this->getMockBuilder(ModuleManager::class)->disableOriginalConstructor()->getMock();
        $manager->method('install')->willReturn(false);
        $manager->expects($this->once())->method('getError')->with('broken')->willReturn('manager error details');

        $tester = $this->createCommandTester(new InstallModuleCommand($translator, $context, $preparer, $configuration, $manager));
        $this->assertSame(Command::FAILURE, $tester->execute(['modules' => ['broken']]));
        $this->assertStringContainsString('broken', $tester->getDisplay());
        $this->assertStringContainsString('manager error details', $tester->getDisplay());
    }

    private function createCommandTester(Command $command): CommandTester
    {
        $command->setHelperSet(new HelperSet([new FormatterHelper()]));

        return new CommandTester($command);
    }

    private function dependencies(): array
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnCallback(
            static fn (string $message, array $parameters = []): string => strtr($message, $parameters)
        );

        $legacyContext = $this->getMockBuilder(LegacyContext::class)->disableOriginalConstructor()->getMock();
        $context = (new ReflectionClass(Context::class))->newInstanceWithoutConstructor();
        $context->employee = (new ReflectionClass(Employee::class))->newInstanceWithoutConstructor();
        $legacyContext->method('getContext')->willReturn($context);

        $preparer = $this->getMockBuilder(ContextBuilderPreparer::class)->disableOriginalConstructor()->getMock();
        $configuration = $this->getMockBuilder(Configuration::class)->disableOriginalConstructor()->getMock();
        $configuration->method('get')->willReturnCallback(static fn (string $key) => $key === 'PS_LANG_DEFAULT' ? 1 : 0);

        return [$translator, $legacyContext, $preparer, $configuration];
    }
}
