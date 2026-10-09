<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Command;

use Context;
use Employee;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Tests\Resources\DatabaseDump;

/**
 * Some commands need an employee in the legacy context so that module hooks work (see LegacyHookSubscriber).
 * This employee must be anonymous, the actions performed via CLI must never be attributed to an existing employee.
 */
class CommandEmployeeContextTest extends KernelTestCase
{
    /**
     * Id previously hard-coded in the commands, an existing employee with this id must not be loaded.
     */
    private const FORMERLY_USED_EMPLOYEE_ID = 42;

    private const MAIL_TEMPLATES_OUTPUT_FOLDER = 'mail_templates_employee_context';

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();

        $fileSystem = new Filesystem();
        $fileSystem->remove(self::getMailTemplatesOutputFolder());
        $fileSystem->mkdir(self::getMailTemplatesOutputFolder());

        $employee = new Employee();
        $employee->id = self::FORMERLY_USED_EMPLOYEE_ID;
        $employee->force_id = true;
        $employee->firstname = 'Forty';
        $employee->lastname = 'Two';
        $employee->email = 'employee42@prestashop.com';
        $employee->setWsPasswd('employee42Password');
        $employee->id_profile = _PS_ADMIN_PROFILE_;
        $employee->id_lang = (int) Context::getContext()->language->id;
        $employee->add();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(['employee', 'employee_shop']);
        (new Filesystem())->remove(self::getMailTemplatesOutputFolder());
    }

    /**
     * @dataProvider getCommands
     */
    public function testCommandUsesAnonymousEmployee(string $commandName, array $arguments): void
    {
        $this->assertTrue(Employee::existsInDatabase(self::FORMERLY_USED_EMPLOYEE_ID, 'employee'));

        $context = Context::getContext();
        $previousEmployee = $context->employee;
        $context->employee = null;

        try {
            $application = new Application(self::bootKernel());
            $commandTester = new CommandTester($application->find($commandName));
            $commandTester->execute($arguments);

            $this->assertInstanceOf(Employee::class, $context->employee);
            $this->assertEmpty($context->employee->id);
        } finally {
            $context->employee = $previousEmployee;
        }
    }

    public static function getCommands(): iterable
    {
        yield 'module list' => [
            'prestashop:module:list',
            ['--simple' => true],
        ];

        // The unknown action makes the command stop right after the context initialization
        yield 'module action' => [
            'prestashop:module',
            ['action' => 'unknown_action', 'module name' => 'ps_banner'],
        ];

        yield 'mail templates generation' => [
            'prestashop:mail:generate',
            ['theme' => 'classic', 'locale' => 'en', 'coreOutputFolder' => self::getMailTemplatesOutputFolder()],
        ];
    }

    private static function getMailTemplatesOutputFolder(): string
    {
        return implode(DIRECTORY_SEPARATOR, [sys_get_temp_dir(), self::MAIL_TEMPLATES_OUTPUT_FOLDER]);
    }
}
