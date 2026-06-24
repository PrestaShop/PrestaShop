<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Import\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Import\LegacyImportExecutor;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Import\Command\ImportCsvFromFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Import\CommandHandler\ImportCsvFromFileHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Import\Result\ImportResult;
use PrestaShop\PrestaShop\Core\Import\Configuration\ImportConfig;
use PrestaShop\PrestaShop\Core\Import\Configuration\ImportRuntimeConfig;
use PrestaShop\PrestaShop\Core\Import\Exception\NotSupportedImportTypeException;
use PrestaShop\PrestaShop\Core\Import\Handler\ImportHandlerFinderInterface;
use PrestaShop\PrestaShop\Core\Import\ImporterInterface;
use PrestaShop\PrestaShop\Core\Import\ImportSettings;

/**
 * Façade handler routing a CSV import either to the modern Importer (for entity
 * types that own a handler) or to the legacy AdminImportController (for the rest),
 * returning a single executor-agnostic ImportResult.
 *
 * This is the only production wiring added by the import safety-net story; the
 * interface stays stable so a later story can swap the internals for the
 * ImportRun aggregate without touching the callers or the Behat scenarios.
 *
 * @internal
 */
#[AsCommandHandler]
final class ImportCsvFromFileHandler implements ImportCsvFromFileHandlerInterface
{
    /**
     * Number of rows processed per Importer iteration; the loop advances the
     * offset until the import reports completion.
     */
    private const BATCH_SIZE = 100;

    /**
     * Safety bound to avoid an infinite loop if the import never completes.
     */
    private const MAX_ITERATIONS = 100000;

    public function __construct(
        private readonly ImporterInterface $importer,
        private readonly ImportHandlerFinderInterface $importHandlerFinder,
        private readonly LegacyImportExecutor $legacyImportExecutor,
    ) {
    }

    public function handle(ImportCsvFromFileCommand $command): ImportResult
    {
        try {
            $importHandler = $this->importHandlerFinder->find($command->getEntityType());
        } catch (NotSupportedImportTypeException) {
            // No modern handler for this entity type: fall back to the legacy controller.
            return $this->legacyImportExecutor->execute($command);
        }

        return $this->runModernImport($command, $importHandler);
    }

    private function runModernImport(ImportCsvFromFileCommand $command, $importHandler): ImportResult
    {
        $importConfig = $this->buildImportConfig($command);
        $mapping = $command->getDataMapping();

        $errors = [];
        $warnings = [];
        $notices = [];
        $doneCount = 0;
        $totalCount = 0;
        $sharedData = [];
        $offset = 0;
        $finished = false;
        $iteration = 0;

        do {
            $runtimeConfig = new ImportRuntimeConfig(
                $command->isValidateOnly(),
                $offset,
                self::BATCH_SIZE,
                $sharedData,
                $mapping
            );

            $this->importer->import($importConfig, $runtimeConfig, $importHandler);

            $report = $runtimeConfig->toArray();
            $sharedData = $runtimeConfig->getSharedData();
            $errors = $report['errors'] ?? [];
            $warnings = $report['warnings'] ?? [];
            $notices = $report['notices'] ?? [];
            $doneCount = (int) ($report['doneCount'] ?? $doneCount);
            $totalCount = max($totalCount, (int) ($report['totalCount'] ?? 0), $doneCount);

            $finished = $runtimeConfig->isFinished();
            $offset = $doneCount;
        } while (!$finished && ++$iteration < self::MAX_ITERATIONS);

        return new ImportResult(
            $this->normalizeMessages($errors),
            $this->normalizeMessages($warnings),
            $this->normalizeMessages($notices),
            $doneCount,
            $totalCount
        );
    }

    private function buildImportConfig(ImportCsvFromFileCommand $command): ImportConfig
    {
        $options = $command->getOptions();

        return new ImportConfig(
            $command->getFilename(),
            $command->getEntityType(),
            $command->getLangIso(),
            $options['separator'] ?? ImportSettings::DEFAULT_SEPARATOR,
            $options['multiple_value_separator'] ?? ImportSettings::DEFAULT_MULTIVALUE_SEPARATOR,
            !empty($options['truncate']),
            !empty($options['regenerate']),
            !empty($options['match_ref']),
            !empty($options['forceIDs']),
            false,
            (int) ($options['skip'] ?? 0)
        );
    }

    /**
     * @param mixed $messages
     *
     * @return string[]
     */
    private function normalizeMessages($messages): array
    {
        if (!is_array($messages)) {
            return [];
        }

        return array_values(array_map(static fn ($message): string => (string) $message, $messages));
    }
}
