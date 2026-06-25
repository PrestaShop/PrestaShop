<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Command;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Command\EmployeeChangePasswordCommand;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use PrestaShopBundle\Security\Admin\EmployeePasswordResetter;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class EmployeeChangePasswordCommandTest extends TestCase
{
    public function testChangesPasswordWithEmailArgumentAndPasswordOption(): void
    {
        $employee = $this->createMock(Employee::class);

        $repository = $this->createMock(EmployeeRepository::class);
        $repository
            ->expects($this->once())
            ->method('loadEmployeeByIdentifier')
            ->with('admin@example.com')
            ->willReturn($employee);

        $resetter = $this->createMock(EmployeePasswordResetter::class);
        $resetter
            ->expects($this->once())
            ->method('resetPassword')
            ->with($employee, 'NewP@ssw0rd!');

        $tester = $this->getCommandTester($repository, $resetter);

        $exitCode = $tester->execute([
            'email' => 'admin@example.com',
            '--password' => 'NewP@ssw0rd!',
        ]);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString('Password updated for admin@example.com', $tester->getDisplay());
    }

    public function testFailsWhenEmployeeNotFound(): void
    {
        $repository = $this->createMock(EmployeeRepository::class);
        $repository
            ->method('loadEmployeeByIdentifier')
            ->with('ghost@example.com')
            ->willReturn(null);

        $resetter = $this->createMock(EmployeePasswordResetter::class);
        $resetter->expects($this->never())->method('resetPassword');

        $tester = $this->getCommandTester($repository, $resetter);

        $exitCode = $tester->execute([
            'email' => 'ghost@example.com',
            '--password' => 'whatever',
        ]);

        $this->assertSame(Command::FAILURE, $exitCode);
        $this->assertStringContainsString('No employee found with email "ghost@example.com"', $tester->getDisplay());
    }

    public function testFailsWhenRepositoryThrows(): void
    {
        $repository = $this->createMock(EmployeeRepository::class);
        $repository
            ->method('loadEmployeeByIdentifier')
            ->willThrowException(new RuntimeException('boom'));

        $resetter = $this->createMock(EmployeePasswordResetter::class);
        $resetter->expects($this->never())->method('resetPassword');

        $tester = $this->getCommandTester($repository, $resetter);

        $exitCode = $tester->execute([
            'email' => 'broken@example.com',
            '--password' => 'whatever',
        ]);

        $this->assertSame(Command::FAILURE, $exitCode);
        $this->assertStringContainsString('No employee found', $tester->getDisplay());
    }

    public function testFailsWhenPasswordsDoNotMatch(): void
    {
        $employee = $this->createMock(Employee::class);

        $repository = $this->createMock(EmployeeRepository::class);
        $repository->method('loadEmployeeByIdentifier')->willReturn($employee);

        $resetter = $this->createMock(EmployeePasswordResetter::class);
        $resetter->expects($this->never())->method('resetPassword');

        $tester = $this->getCommandTester($repository, $resetter);
        $tester->setInputs(['first-password', 'different-password']);

        $exitCode = $tester->execute(['email' => 'admin@example.com']);

        $this->assertSame(Command::FAILURE, $exitCode);
        $this->assertStringContainsString('Passwords do not match', $tester->getDisplay());
    }

    public function testFailsWhenInteractivePasswordIsEmpty(): void
    {
        $employee = $this->createMock(Employee::class);

        $repository = $this->createMock(EmployeeRepository::class);
        $repository->method('loadEmployeeByIdentifier')->willReturn($employee);

        $resetter = $this->createMock(EmployeePasswordResetter::class);
        $resetter->expects($this->never())->method('resetPassword');

        $tester = $this->getCommandTester($repository, $resetter);
        $tester->setInputs(['', '']);

        $exitCode = $tester->execute(['email' => 'admin@example.com']);

        $this->assertSame(Command::FAILURE, $exitCode);
        $this->assertStringContainsString('Password cannot be empty', $tester->getDisplay());
    }

    public function testPromptsForEmailAndPasswordWhenNotProvided(): void
    {
        $employee = $this->createMock(Employee::class);

        $repository = $this->createMock(EmployeeRepository::class);
        $repository
            ->expects($this->once())
            ->method('loadEmployeeByIdentifier')
            ->with('admin@example.com')
            ->willReturn($employee);

        $resetter = $this->createMock(EmployeePasswordResetter::class);
        $resetter
            ->expects($this->once())
            ->method('resetPassword')
            ->with($employee, 'NewP@ssw0rd!');

        $tester = $this->getCommandTester($repository, $resetter);
        $tester->setInputs(['admin@example.com', 'NewP@ssw0rd!', 'NewP@ssw0rd!']);

        $exitCode = $tester->execute([]);

        $this->assertSame(Command::SUCCESS, $exitCode);
    }

    public function testFailsWhenResetterThrows(): void
    {
        $employee = $this->createMock(Employee::class);

        $repository = $this->createMock(EmployeeRepository::class);
        $repository->method('loadEmployeeByIdentifier')->willReturn($employee);

        $resetter = $this->createMock(EmployeePasswordResetter::class);
        $resetter
            ->method('resetPassword')
            ->willThrowException(new RuntimeException('Unable to send reset email.'));

        $tester = $this->getCommandTester($repository, $resetter);

        $exitCode = $tester->execute([
            'email' => 'admin@example.com',
            '--password' => 'NewP@ssw0rd!',
        ]);

        $this->assertSame(Command::FAILURE, $exitCode);
        $this->assertStringContainsString('Failed to change password: Unable to send reset email.', $tester->getDisplay());
    }

    private function getCommandTester(
        EmployeeRepository $repository,
        EmployeePasswordResetter $resetter,
    ): CommandTester {
        return new CommandTester(new EmployeeChangePasswordCommand($repository, $resetter));
    }
}
