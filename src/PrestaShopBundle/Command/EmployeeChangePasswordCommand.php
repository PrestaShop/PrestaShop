<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Command;

use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use PrestaShopBundle\Security\Admin\EmployeePasswordResetter;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(
    name: 'prestashop:employee:change-password',
    description: 'Change an employee password from the CLI.',
)]
final class EmployeeChangePasswordCommand extends Command
{
    use PasswordPromptTrait;

    public function __construct(
        private readonly EmployeeRepository $employeeRepository,
        private readonly EmployeePasswordResetter $passwordResetter,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp(<<<'HELP'
                Reset an employee password from the command line.
                If <info>email</info> is not provided, it is asked interactively.
                The new password is asked interactively (hidden) unless <info>--password</info> is provided.
                The employee will receive the standard "Your new password" notification email.
                HELP)
            ->addArgument('email', InputArgument::OPTIONAL, 'Employee email')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'New password (prefer the interactive prompt to avoid leaking it in shell history)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        if (empty($email)) {
            $email = $io->ask('Employee email', null, function (?string $value): string {
                $value = trim((string) $value);
                if ($value === '') {
                    throw new RuntimeException('Email cannot be empty.');
                }

                return $value;
            });
        }

        $employee = $this->loadEmployee((string) $email);
        if ($employee === null) {
            $io->error(sprintf('No employee found with email "%s".', $email));

            return Command::FAILURE;
        }

        $password = $input->getOption('password');
        if (empty($password)) {
            try {
                $password = $this->askPasswordTwice($io, 'New password');
            } catch (RuntimeException $e) {
                $io->error($e->getMessage());

                return Command::FAILURE;
            }
        }

        try {
            $this->passwordResetter->resetPassword($employee, (string) $password);
        } catch (Throwable $e) {
            $io->error(sprintf('Failed to change password: %s', $e->getMessage()));

            return Command::FAILURE;
        }

        $io->success(sprintf('Password updated for %s (notification email sent).', $email));

        return Command::SUCCESS;
    }

    private function loadEmployee(string $email): ?Employee
    {
        try {
            /** @var Employee|null $employee */
            $employee = $this->employeeRepository->loadEmployeeByIdentifier($email);
        } catch (Throwable) {
            return null;
        }

        return $employee ?: null;
    }
}
