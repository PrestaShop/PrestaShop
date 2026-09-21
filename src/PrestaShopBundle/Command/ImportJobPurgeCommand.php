<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Command;

use DateTimeImmutable;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Import\Command\PurgeImportJobsCommand;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportException;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportJobPurgeSummary;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Deletes finished import jobs and the working files nothing owns any more.
 *
 * A thin wrapper: the collection itself is a CQRS command, so a cron running this is doing exactly
 * what the Admin API or a back-office button would do.
 */
#[AsCommand(
    name: 'prestashop:import:purge-jobs',
    description: 'Delete finished import jobs and their leftover working files.'
)]
class ImportJobPurgeCommand extends Command
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'days',
            null,
            InputOption::VALUE_REQUIRED,
            'Keep jobs finished within the last N days. Defaults to the retention window.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $days = $input->getOption('days');

        if (null !== $days && !ctype_digit((string) $days)) {
            $style->error('The --days option expects a positive number of days.');

            return self::INVALID;
        }

        $expirationDate = null === $days
            ? null
            : (new DateTimeImmutable())->modify(sprintf('-%d days', (int) $days));

        try {
            /** @var ImportJobPurgeSummary $summary */
            $summary = $this->commandBus->handle(new PurgeImportJobsCommand($expirationDate));
        } catch (ImportException $exception) {
            $style->error($exception->getMessage());

            return self::FAILURE;
        }

        $style->success(sprintf(
            '%d import job(s) deleted, %d working file(s) removed.',
            $summary->getPurgedJobCount(),
            $summary->getRemovedWorkingFileCount()
        ));

        return self::SUCCESS;
    }
}
