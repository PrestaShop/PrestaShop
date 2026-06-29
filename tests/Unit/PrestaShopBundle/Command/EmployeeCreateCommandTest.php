<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Employee\EmployeeContextInitializer;
use PrestaShop\PrestaShop\Adapter\Shop\Context as ShopContextAdapter;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\AddEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmailAlreadyUsedException;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;
use PrestaShop\PrestaShop\Core\Security\PasswordPolicyConfiguration;
use PrestaShopBundle\Command\EmployeeCreateCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class EmployeeCreateCommandTest extends TestCase
{
    public function testCreatesSuperAdminEmployeeWithExplicitOptions(): void
    {
        $configuration = $this->mockConfiguration();

        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (AddEmployeeCommand $cmd): bool {
                $this->assertSame('John', $cmd->getFirstName()->getValue());
                $this->assertSame('Doe', $cmd->getLastName()->getValue());
                $this->assertSame('john@example.com', $cmd->getEmail()->getValue());
                // Always SuperAdmin (_PS_ADMIN_PROFILE_ = 1 in the mock), always default lang (1),
                // always all shops returned by ShopContextAdapter, always active, never gravatar.
                $this->assertSame(1, $cmd->getProfileId());
                $this->assertSame(1, $cmd->getLanguageId());
                $this->assertTrue($cmd->isActive());
                $this->assertSame([1, 2], $cmd->getShopAssociation());
                $this->assertFalse($cmd->hasEnabledGravatar());

                return true;
            }))
            ->willReturn(new EmployeeId(42));

        $tester = $this->getCommandTester($commandBus, $configuration);

        $exitCode = $tester->execute([
            'email' => 'john@example.com',
            '--first-name' => 'John',
            '--last-name' => 'Doe',
            '--password' => 'Str0ng!Pass',
        ]);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString('SuperAdmin employee #42 (john@example.com) created', $tester->getDisplay());
        $this->assertStringContainsString('SuperAdmin privileges', $tester->getDisplay());
    }

    public function testFailsWhenPasswordsDoNotMatch(): void
    {
        $configuration = $this->mockConfiguration();

        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus->expects($this->never())->method('handle');

        $tester = $this->getCommandTester($commandBus, $configuration);
        $tester->setInputs(['first', 'different']);

        $exitCode = $tester->execute([
            'email' => 'admin@example.com',
            '--first-name' => 'Admin',
            '--last-name' => 'User',
        ]);

        $this->assertSame(Command::FAILURE, $exitCode);
        $this->assertStringContainsString('Passwords do not match', $tester->getDisplay());
    }

    public function testFailsWhenCommandBusThrows(): void
    {
        $configuration = $this->mockConfiguration();

        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus
            ->method('handle')
            ->willThrowException(new EmailAlreadyUsedException('admin@example.com'));

        $tester = $this->getCommandTester($commandBus, $configuration);

        $exitCode = $tester->execute([
            'email' => 'admin@example.com',
            '--first-name' => 'Admin',
            '--last-name' => 'User',
            '--password' => 'Str0ng!Pass',
        ]);

        $this->assertSame(Command::FAILURE, $exitCode);
        $this->assertStringContainsString('Failed to create employee', $tester->getDisplay());
    }

    public function testFailsWhenNoSuperAdminExistsYet(): void
    {
        $configuration = $this->mockConfiguration();

        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus->expects($this->never())->method('handle');

        $shopContext = $this->createMock(ShopContextAdapter::class);
        $shopContext->method('getShops')->willReturn([1, 2]);

        $contextInitializer = $this->createMock(EmployeeContextInitializer::class);
        $contextInitializer->method('initializeWithFirstSuperAdmin')->willReturn(null);

        $tester = new CommandTester(new EmployeeCreateCommand($commandBus, $configuration, $shopContext, $contextInitializer));

        $exitCode = $tester->execute([
            'email' => 'admin@example.com',
            '--first-name' => 'Admin',
            '--last-name' => 'User',
            '--password' => 'Str0ng!Pass',
        ]);

        $this->assertSame(Command::FAILURE, $exitCode);
        $this->assertStringContainsString('No active super-administrator', $tester->getDisplay());
    }

    public function testPromptsForMissingMandatoryValues(): void
    {
        $configuration = $this->mockConfiguration();

        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (AddEmployeeCommand $cmd): bool {
                $this->assertSame('Jane', $cmd->getFirstName()->getValue());
                $this->assertSame('Roe', $cmd->getLastName()->getValue());
                $this->assertSame('jane@example.com', $cmd->getEmail()->getValue());

                return true;
            }))
            ->willReturn(new EmployeeId(2));

        $tester = $this->getCommandTester($commandBus, $configuration);
        // Order: email, first name, last name, password, password confirm.
        $tester->setInputs(['jane@example.com', 'Jane', 'Roe', 'Str0ng!Pass', 'Str0ng!Pass']);

        $exitCode = $tester->execute([]);

        $this->assertSame(Command::SUCCESS, $exitCode);
    }

    private function getCommandTester(
        CommandBusInterface $commandBus,
        ConfigurationInterface $configuration,
    ): CommandTester {
        $shopContext = $this->createMock(ShopContextAdapter::class);
        $shopContext->method('getShops')->willReturn([1, 2]);

        $contextInitializer = $this->createMock(EmployeeContextInitializer::class);
        $contextInitializer->method('initializeWithFirstSuperAdmin')->willReturn(1);

        return new CommandTester(new EmployeeCreateCommand($commandBus, $configuration, $shopContext, $contextInitializer));
    }

    private function mockConfiguration(): ConfigurationInterface
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->willReturnMap([
            ['_PS_ADMIN_PROFILE_', 1],
            ['PS_LANG_DEFAULT', 1],
            [PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_LENGTH, 8],
            [PasswordPolicyConfiguration::CONFIGURATION_MAXIMUM_LENGTH, 72],
            [PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_SCORE, 0],
        ]);

        return $configuration;
    }
}
