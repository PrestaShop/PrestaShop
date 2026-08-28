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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Command;

use DateTime;
use Exception;
use PrestaShop\PrestaShop\Adapter\PDF\PDFGenerator;
use PrestaShop\PrestaShop\Core\Order\OrderInvoiceDataProviderInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'prestashop:pdf:export',
    description: 'Export a PDF document (currently: invoices) generated over a date range'
)]
class ExportPdfCommand extends Command
{
    private const TYPE_INVOICE = 'invoice';

    private const SUPPORTED_TYPES = [self::TYPE_INVOICE];

    public function __construct(
        private readonly OrderInvoiceDataProviderInterface $orderInvoiceDataProvider,
        private readonly PDFGenerator $invoicePdfGenerator,
        private readonly Filesystem $filesystem,
        private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('from', InputArgument::REQUIRED, 'Start date (YYYY-MM-DD)')
            ->addArgument('to', InputArgument::REQUIRED, 'End date (YYYY-MM-DD)')
            ->addOption(
                'type',
                't',
                InputOption::VALUE_REQUIRED,
                sprintf('Document type (%s)', implode(', ', self::SUPPORTED_TYPES)),
                self::TYPE_INVOICE
            )
            ->addOption(
                'output',
                'o',
                InputOption::VALUE_REQUIRED,
                'Output PDF file path (default: <project>/pdf/export/invoices.pdf)'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $type = (string) $input->getOption('type');
        if (!in_array($type, self::SUPPORTED_TYPES, true)) {
            $io->error(sprintf('Unsupported type "%s". Supported: %s', $type, implode(', ', self::SUPPORTED_TYPES)));

            return Command::INVALID;
        }

        try {
            $from = new DateTime((string) $input->getArgument('from'));
            $to = new DateTime((string) $input->getArgument('to'));
        } catch (Exception $e) {
            $io->error(sprintf('Invalid date: %s', $e->getMessage()));

            return Command::INVALID;
        }

        $collection = $this->orderInvoiceDataProvider->getByDateInterval($from, $to);
        if (empty($collection)) {
            $io->warning('No records found for the given date range.');

            return Command::SUCCESS;
        }

        $outputPath = (string) ($input->getOption('output') ?? $this->projectDir . '/pdf/export/invoices.pdf');
        $this->filesystem->mkdir(dirname($outputPath));

        $this->invoicePdfGenerator->generatePDF($collection, false, $outputPath);

        $io->success(sprintf('Exported %d record(s) to %s', count($collection), $outputPath));

        return Command::SUCCESS;
    }
}
