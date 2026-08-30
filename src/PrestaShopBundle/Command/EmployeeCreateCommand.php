<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Command;

use PrestaShop\PrestaShop\Adapter\Employee\EmployeeContextInitializer;
use PrestaShop\PrestaShop\Adapter\Shop\Context as ShopContextAdapter;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\AddEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;
use PrestaShop\PrestaShop\Core\Security\PasswordPolicyConfiguration;
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
    name: 'prestashop:employee:create-admin',
    description: 'Create a new SuperAdmin back-office employee.',
)]
final class EmployeeCreateCommand extends Command
{
    use PasswordPromptTrait;

    private const DEFAULT_PAGE_ID = 1;

    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly ConfigurationInterface $configuration,
        private readonly ShopContextAdapter $shopContext,
        private readonly EmployeeContextInitializer $employeeContextInitializer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp(<<<'HELP'
                Create a new back-office <comment>SuperAdmin</comment> employee from the CLI.

                The CLI intentionally only exposes the bare minimum (email, first/last
                name, password). The new employee is always created as a SuperAdmin
                with full back-office access, active, on the default shop language,
                associated to every shop, no Gravatar.

                For more granular employee management, use the back-office Team page.

                Missing values for <info>email</info>, <info>--first-name</info>, <info>--last-name</info> and <info>--password</info>
                are prompted interactively. The password prompt is hidden and asked twice for confirmation.
                HELP)
            ->addArgument('email', InputArgument::OPTIONAL, 'Employee email')
            ->addOption('first-name', null, InputOption::VALUE_REQUIRED, 'First name')
            ->addOption('last-name', null, InputOption::VALUE_REQUIRED, 'Last name')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'New password (prefer the interactive prompt to avoid leaking it in shell history)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = (string) ($input->getArgument('email') ?: $this->askNonEmpty($io, 'Employee email'));
        $firstName = (string) ($input->getOption('first-name') ?: $this->askNonEmpty($io, 'First name'));
        $lastName = (string) ($input->getOption('last-name') ?: $this->askNonEmpty($io, 'Last name'));

        $password = $input->getOption('password');
        if (empty($password)) {
            try {
                $password = $this->askPasswordTwice($io);
            } catch (RuntimeException $e) {
                $io->error($e->getMessage());

                return Command::FAILURE;
            }
        }

        $profileId = (int) $this->configuration->get('_PS_ADMIN_PROFILE_');
        $languageId = (int) $this->configuration->get('PS_LANG_DEFAULT');
        /** @var array<int, int> $shopAssociation */
        $shopAssociation = array_map('intval', $this->shopContext->getShops(true, true));

        if ($this->employeeContextInitializer->initializeWithFirstSuperAdmin() === null) {
            $io->error('No active super-administrator exists yet. Create one through the installer first.');

            return Command::FAILURE;
        }

        $command = new AddEmployeeCommand(
            $firstName,
            $lastName,
            $email,
            (string) $password,
            self::DEFAULT_PAGE_ID,
            $languageId,
            true,
            $profileId,
            $shopAssociation,
            false,
            (int) $this->configuration->get(PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_LENGTH),
            (int) $this->configuration->get(PasswordPolicyConfiguration::CONFIGURATION_MAXIMUM_LENGTH),
            (int) $this->configuration->get(PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_SCORE),
        );

        try {
            /** @var EmployeeId $employeeId */
            $employeeId = $this->commandBus->handle($command);
        } catch (Throwable $e) {
            $io->error(sprintf('Failed to create employee: %s', $e->getMessage()));

            return Command::FAILURE;
        }

        $io->success(sprintf(
            'SuperAdmin employee #%d (%s) created with full back-office access.',
            $employeeId->getValue(),
            $email,
        ));
        $io->warning('This account has SuperAdmin privileges. Share the credentials carefully.');

        return Command::SUCCESS;
    }

    private function askNonEmpty(SymfonyStyle $io, string $label): string
    {
        return (string) $io->ask($label, null, static function (?string $value) use ($label): string {
            $value = trim((string) $value);
            if ($value === '') {
                throw new RuntimeException(sprintf('%s cannot be empty.', $label));
            }

            return $value;
        });
    }
}
