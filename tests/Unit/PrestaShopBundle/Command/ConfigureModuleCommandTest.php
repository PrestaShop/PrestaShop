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
use PrestaShop\PrestaShop\Adapter\Shop\Context as ShopContext;
use PrestaShop\PrestaShop\Core\Context\ContextBuilderPreparer;
use PrestaShopBundle\Command\ConfigureModuleCommand;
use ReflectionClass;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConfigureModuleCommandTest extends TestCase
{
    public function testDefaultContextIsAllShops(): void
    {
        $configurator = $this->configurator();
        $configurator->method('validate')->willReturn([]);
        $configurator->method('configure')->willReturn(true);
        [, , , , $shopContext] = $this->dependencies();
        $shopContext->expects($this->once())->method('setAllContext')->with(null);

        $this->assertSame(Command::SUCCESS, $this->tester($configurator, $shopContext)->execute(['modules' => ['first']]));
    }

    public function testExplicitShopGroupContextIsPreserved(): void
    {
        $configurator = $this->configurator();
        $configurator->method('validate')->willReturn([]);
        $configurator->method('configure')->willReturn(true);
        [, , , , $shopContext] = $this->dependencies();
        $shopContext->expects($this->never())->method('setAllContext');

        $command = $this->command($configurator, $shopContext);
        $command->addOption('id_shop_group', null, InputOption::VALUE_OPTIONAL);
        $tester = new CommandTester($command);

        $this->assertSame(Command::SUCCESS, $tester->execute(['modules' => ['first'], '--id_shop_group' => 2]));
    }

    public function testSingleModuleWithoutExplicitFile(): void
    {
        $configurator = $this->configurator();
        $configurator->expects($this->once())->method('module')->with('first');
        $configurator->expects($this->never())->method('file');
        $configurator->method('validate')->willReturn([]);
        $configurator->method('configure')->willReturn(true);

        $tester = $this->tester($configurator);
        $this->assertSame(Command::SUCCESS, $tester->execute(['modules' => ['first']]));
        $this->assertStringContainsString('first', $tester->getDisplay());
    }

    public function testSingleModuleWithExplicitFile(): void
    {
        $configurator = $this->configurator();
        $configurator->expects($this->once())->method('file')->with('/tmp/first.yml');
        $configurator->method('validate')->willReturn([]);
        $configurator->method('configure')->willReturn(true);

        $this->assertSame(Command::SUCCESS, $this->tester($configurator)->execute([
            'modules' => ['first'], '--config-file' => ['/tmp/first.yml'],
        ]));
    }

    public function testBulkFileMapping(): void
    {
        $configurator = $this->configurator();
        $configurator->expects($this->exactly(2))->method('module');
        $configurator->expects($this->exactly(2))->method('file');
        $configurator->method('validate')->willReturn([]);
        $configurator->method('configure')->willReturn(true);

        $tester = $this->tester($configurator);
        $this->assertSame(Command::SUCCESS, $tester->execute([
            'modules' => ['first', 'second'],
            '--config-file' => ['first:/tmp/first.yml', 'second:/tmp/second.yml'],
        ]));
        $this->assertStringContainsString('first', $tester->getDisplay());
        $this->assertStringContainsString('second', $tester->getDisplay());
    }

    /** @dataProvider invalidFilesProvider */
    public function testInvalidFileOptions(array $modules, array $files, string $expectedMessage): void
    {
        $configurator = $this->configurator();
        $configurator->expects($this->never())->method('validate');
        $tester = $this->tester($configurator);

        $this->assertSame(Command::INVALID, $tester->execute([
            'modules' => $modules, '--config-file' => $files,
        ]));
        $this->assertStringContainsString($expectedMessage, $tester->getDisplay());
    }

    public static function invalidFilesProvider(): iterable
    {
        yield 'missing module prefix in bulk' => [['first', 'second'], ['/tmp/first.yml'], 'When configuring multiple modules'];
        yield 'empty module name' => [['first', 'second'], [':/tmp/first.yml'], 'Invalid --config-file value'];
        yield 'empty path' => [['first', 'second'], ['first:'], 'Invalid --config-file value'];
        yield 'unknown module' => [['first', 'second'], ['other:/tmp/other.yml'], 'unknown module'];
        yield 'duplicate module' => [['first', 'second'], ['first:/tmp/one.yml', 'first:/tmp/two.yml'], 'already been provided'];
    }

    public function testValidationFailureIdentifiesModuleAndContinues(): void
    {
        $configurator = $this->configurator();
        $counter = 0;
        $configurator->method('validate')->willReturnCallback(static function () use (&$counter): array {
            return ++$counter === 1 ? ['invalid setting'] : [];
        });
        $configurator->method('configure')->willReturn(true);

        $tester = $this->tester($configurator);
        $this->assertSame(Command::FAILURE, $tester->execute(['modules' => ['broken', 'working']]));
        $this->assertStringContainsString('failed for module broken', $tester->getDisplay());
        $this->assertStringContainsString('invalid setting', $tester->getDisplay());
        $this->assertStringContainsString('applied to module working', $tester->getDisplay());
    }

    public function testConfigureFailureReturnsFailure(): void
    {
        $configurator = $this->configurator();
        $configurator->method('validate')->willReturn([]);
        $configurator->method('configure')->willReturn(false);
        $tester = $this->tester($configurator);
        $this->assertSame(Command::FAILURE, $tester->execute(['modules' => ['broken']]));
        $this->assertStringContainsString('Cannot configure module broken', $tester->getDisplay());
    }

    public function testExceptionIdentifiesModuleAndContinues(): void
    {
        $configurator = $this->configurator();
        $counter = 0;
        $configurator->method('validate')->willReturnCallback(static function () use (&$counter): array {
            if (++$counter === 1) {
                throw new RuntimeException('test exception');
            }

            return [];
        });
        $configurator->method('configure')->willReturn(true);
        $tester = $this->tester($configurator);
        $this->assertSame(Command::FAILURE, $tester->execute(['modules' => ['broken', 'working']]));
        $this->assertStringContainsString('Cannot configure module broken', $tester->getDisplay());
        $this->assertStringContainsString('applied to module working', $tester->getDisplay());
    }

    public function testBulkConfigurationWithoutFiles(): void
    {
        $configurator = $this->configurator();
        $seen = [];
        $configurator->method('module')->willReturnCallback(static function (string $module) use (&$seen): void {
            $seen[] = $module;
        });
        $configurator->expects($this->never())->method('file');
        $configurator->method('validate')->willReturn([]);
        $configurator->method('configure')->willReturn(true);

        $tester = $this->tester($configurator);
        $this->assertSame(Command::SUCCESS, $tester->execute(['modules' => ['first', 'second']]));
        $this->assertSame(['first', 'second'], $seen);
    }

    public function testBulkConfigurationWithPartialFileMapping(): void
    {
        $configurator = $this->configurator();
        $seenModules = [];
        $seenFiles = [];
        $configurator->method('module')->willReturnCallback(static function (string $module) use (&$seenModules): void {
            $seenModules[] = $module;
        });
        $configurator->method('file')->willReturnCallback(static function (string $file) use (&$seenFiles): void {
            $seenFiles[] = $file;
        });
        $configurator->method('validate')->willReturn([]);
        $configurator->method('configure')->willReturn(true);

        $tester = $this->tester($configurator);
        $this->assertSame(Command::SUCCESS, $tester->execute([
            'modules' => ['first', 'second'],
            '--config-file' => ['first:/tmp/first.yml'],
        ]));
        $this->assertSame(['first', 'second'], $seenModules);
        $this->assertSame(['/tmp/first.yml'], $seenFiles);
    }

    public function testBulkConfigurationKeepsModuleFileMappingsSeparated(): void
    {
        $configurator = $this->configurator();
        $seenModules = [];
        $seenFiles = [];
        $configurator->method('module')->willReturnCallback(static function (string $module) use (&$seenModules): void {
            $seenModules[] = $module;
        });
        $configurator->method('file')->willReturnCallback(static function (string $file) use (&$seenFiles): void {
            $seenFiles[] = $file;
        });
        $configurator->method('validate')->willReturn([]);
        $configurator->method('configure')->willReturn(true);

        $tester = $this->tester($configurator);
        $this->assertSame(Command::SUCCESS, $tester->execute([
            'modules' => ['first', 'second'],
            '--config-file' => ['first:/tmp/first.yml', 'second:/tmp/second.yml'],
        ]));
        $this->assertSame(['first', 'second'], $seenModules);
        $this->assertSame(['/tmp/first.yml', '/tmp/second.yml'], $seenFiles);
    }

    public function testBulkSuccessMessagesIdentifyEachModule(): void
    {
        $configurator = $this->configurator();
        $configurator->method('validate')->willReturn([]);
        $configurator->method('configure')->willReturn(true);

        $tester = $this->tester($configurator);
        $this->assertSame(Command::SUCCESS, $tester->execute(['modules' => ['first', 'second']]));
        $this->assertStringContainsString('Configuration successfully applied to module first.', $tester->getDisplay());
        $this->assertStringContainsString('Configuration successfully applied to module second.', $tester->getDisplay());
    }

    private function configurator(): ModuleSelfConfigurator&MockObject
    {
        return $this->getMockBuilder(ModuleSelfConfigurator::class)->disableOriginalConstructor()->getMock();
    }

    private function tester(ModuleSelfConfigurator $configurator, ?ShopContext $shopContext = null): CommandTester
    {
        $command = $this->command($configurator, $shopContext);

        return new CommandTester($command);
    }

    private function command(ModuleSelfConfigurator $configurator, ?ShopContext $shopContext = null): ConfigureModuleCommand
    {
        [$translator, $context, $preparer, $configuration, $defaultShopContext] = $this->dependencies();
        $command = new ConfigureModuleCommand($translator, $context, $preparer, $configuration, $shopContext ?? $defaultShopContext, $configurator);
        $command->setHelperSet(new HelperSet([new FormatterHelper()]));

        return $command;
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
        $shopContext = $this->getMockBuilder(ShopContext::class)->disableOriginalConstructor()->getMock();

        return [$translator, $legacyContext, $preparer, $configuration, $shopContext];
    }
}
