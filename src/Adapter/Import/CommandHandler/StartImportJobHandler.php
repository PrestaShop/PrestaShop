<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Import\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Import\ImportTruncator;
use PrestaShop\PrestaShop\Adapter\Import\Job\ImportJobPurger;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Import\Command\StartImportJobCommand;
use PrestaShop\PrestaShop\Core\Domain\Import\CommandHandler\StartImportJobHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\CannotStartImportJobException;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportJobUuid;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporterRegistry;
use PrestaShop\PrestaShop\Core\Import\Engine\File\CsvImportFileNormalizer;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportJobOptions;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\ImportDirectory;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolverInterface;
use PrestaShopBundle\Entity\ImportJob;
use PrestaShopBundle\Entity\Repository\ImportJobRepository;
use Psr\Log\LoggerInterface;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

/**
 * Handles @see StartImportJobCommand.
 *
 * Every check the command could not make happens here, once, before a job exists — an importer for
 * the entity type, a usable shop scope, a language, a readable file inside the allowed roots. The
 * alternative is one error per row for a job that was never viable.
 */
#[AsCommandHandler]
final class StartImportJobHandler implements StartImportJobHandlerInterface
{
    public function __construct(
        private readonly EntityImporterRegistry $importerRegistry,
        private readonly ImportJobRepository $importJobRepository,
        private readonly ShopListResolverInterface $shopListResolver,
        private readonly LanguageRepositoryInterface $languageRepository,
        private readonly ImportTruncator $truncator,
        private readonly ImportDirectory $importDirectory,
        private readonly CsvImportFileNormalizer $normalizer,
        private readonly ImportJobPurger $purger,
        private readonly Filesystem $filesystem,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function handle(StartImportJobCommand $command): ImportJobUuid
    {
        $entityType = $command->getEntityType()->getValue();
        if (!$this->importerRegistry->has($entityType)) {
            throw new CannotStartImportJobException(
                sprintf('No importer is registered for entity type "%s".', $entityType),
                CannotStartImportJobException::UNKNOWN_ENTITY_TYPE
            );
        }

        $options = ImportJobOptions::fromArray($command->getOptions());
        $shopId = $this->resolveSingleShopId($command);

        if ($options->truncate && !$this->truncator->supports($entityType)) {
            throw new CannotStartImportJobException(
                sprintf('Existing "%s" data cannot be deleted before an import.', $entityType),
                CannotStartImportJobException::UNSUPPORTED_TRUNCATE
            );
        }

        if ($options->dryRun && !$this->declaresValidationPhase($entityType)) {
            throw new CannotStartImportJobException(
                sprintf('The "%s" importer has no validation phase, so it cannot run in dry run.', $entityType),
                CannotStartImportJobException::UNSUPPORTED_DRY_RUN
            );
        }

        if (null === $this->languageRepository->getOneByIsoCode($command->getLangIso())) {
            throw new CannotStartImportJobException(
                sprintf('Language "%s" is not installed.', $command->getLangIso()),
                CannotStartImportJobException::UNKNOWN_LANGUAGE
            );
        }

        $sourceIsOurs = $this->assertReadableSource($command->getSourceFilePath());
        $sourceFile = new SplFileInfo((string) realpath($command->getSourceFilePath()));
        $importJobUuid = ImportJobUuid::generate();
        $workingFilePath = $this->importDirectory->getWorkingFile($importJobUuid->getValue());

        // the directory ships with the shop; this only covers a deploy that dropped it
        $this->filesystem->mkdir($this->importDirectory->getWorkingDir());

        $normalized = $this->normalizer->normalize(
            $sourceFile,
            $workingFilePath,
            $command->getCsvSeparator(),
            $command->getSkipRows()
        );

        if ($normalized->dataRecordCount <= 0) {
            $this->filesystem->remove($workingFilePath);

            throw new CannotStartImportJobException(
                'The import file holds no data record.',
                CannotStartImportJobException::EMPTY_SOURCE_FILE
            );
        }

        $importJob = new ImportJob(
            $importJobUuid->getValue(),
            $entityType,
            $shopId,
            $command->getFileName(),
            $command->getSkipRows(),
            $normalized->dataRecordCount,
            [
                'langIso' => $command->getLangIso(),
                'multipleValueSeparator' => $command->getMultipleValueSeparator(),
                'fieldMapping' => $command->getFieldMapping()->getValue(),
            ],
            $options->toArray()
        );
        $this->importJobRepository->save($importJob);

        // only now, so a rejected file can be retried without being uploaded again — and only a
        // file that is ours: an upload in a temp directory is PHP's to remove when the request
        // ends, a file a script pointed at is the script's
        if ($sourceIsOurs && !$options->keepSourceFile) {
            $this->filesystem->remove($sourceFile->getPathname());
        }

        $this->collectGarbage();

        return $importJobUuid;
    }

    /**
     * The scope is validated once, here, rather than at every call site.
     *
     * Two scopes are refused: one that resolves to no usable shop, which would otherwise reach
     * commands as shop id 0, and one wider than a single shop, which the product importer cannot
     * serve yet (#42423). Whoever relaxes the second check has to revisit the paths that are
     * arithmetically single-shop rather than merely scoped — reading current stock from one
     * representative shop and applying one delta is silently wrong for the others, and forced-id
     * creation picks one shop to own the id.
     */
    private function resolveSingleShopId(StartImportJobCommand $command): int
    {
        $shopIds = $this->shopListResolver->resolveShopIds($command->getShopConstraint());

        if ([] === $shopIds) {
            throw new CannotStartImportJobException(
                'The import shop scope resolves to no usable shop.',
                CannotStartImportJobException::EMPTY_SHOP_SCOPE
            );
        }

        if (count($shopIds) > 1) {
            throw new CannotStartImportJobException(
                'Importing into more than one shop at a time is not supported yet.',
                CannotStartImportJobException::UNSUPPORTED_SHOP_SCOPE
            );
        }

        return $shopIds[0];
    }

    private function declaresValidationPhase(string $entityType): bool
    {
        foreach ($this->importerRegistry->get($entityType)->getPhases() as $phase) {
            if (ImportPhaseDefinition::PHASE_VALIDATION === $phase->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * A path lets a caller name any file PHP can read, and imported content is quoted back in
     * messages, so only two kinds of root are allowed. The import directory, where the back office
     * uploads land. And the temp directories PHP writes uploads to (upload_tmp_dir, the system
     * temp), because the Admin API hands the command UploadedFile::getPathname() as is — nothing
     * sits between the request and this handler that could move the file first. Under a web SAPI
     * a source in a temp directory must be an upload of the current request (is_uploaded_file()),
     * which rules out every other file the PHP user owns there, sessions included; under the CLI
     * the check could never pass and adds nothing, since whoever runs a console command or a test
     * already has the filesystem.
     *
     * Working files are excluded: they belong to other jobs, and normalizing one would also delete
     * it.
     *
     * @return bool whether the file is also ours to delete once normalized — only a source inside
     *              the import directory is: PHP removes a request upload itself when the request
     *              ends, and a file a script pointed at is the script's
     *
     * @throws CannotStartImportJobException when the file may not be read at all
     */
    private function assertReadableSource(string $sourceFilePath): bool
    {
        $realPath = realpath($sourceFilePath);
        if (false === $realPath || !is_file($realPath)) {
            throw new CannotStartImportJobException(
                sprintf('Import file "%s" was not found.', $sourceFilePath),
                CannotStartImportJobException::SOURCE_FILE_NOT_FOUND
            );
        }

        if ($this->isUnder($realPath, $this->importDirectory->getWorkingDir())) {
            throw new CannotStartImportJobException(
                'An import working file cannot be used as a source.',
                CannotStartImportJobException::SOURCE_FILE_OUT_OF_BOUNDS
            );
        }

        if ($this->isUnder($realPath, $this->importDirectory->getDir())) {
            return true;
        }

        foreach (array_unique([(string) ini_get('upload_tmp_dir'), sys_get_temp_dir()]) as $temporaryDirectory) {
            if ('' === $temporaryDirectory || !$this->isUnder($realPath, $temporaryDirectory)) {
                continue;
            }

            // the raw path first: realpath() rewrites a symlinked temp directory (macOS /var), and
            // is_uploaded_file() matches the string PHP recorded
            if ('cli' !== PHP_SAPI && !is_uploaded_file($sourceFilePath) && !is_uploaded_file($realPath)) {
                throw new CannotStartImportJobException(
                    'A file in a temp directory must have been uploaded by the current request.',
                    CannotStartImportJobException::SOURCE_FILE_OUT_OF_BOUNDS
                );
            }

            return false;
        }

        throw new CannotStartImportJobException(
            'The import file is outside the directories imports may read.',
            CannotStartImportJobException::SOURCE_FILE_OUT_OF_BOUNDS
        );
    }

    private function isUnder(string $realPath, string $directory): bool
    {
        $root = realpath($directory);

        return false !== $root && str_starts_with($realPath, rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
    }

    /**
     * Opportunistic and never fatal: a shop that cannot collect its old jobs must still be able to
     * start a new one.
     */
    private function collectGarbage(): void
    {
        try {
            $this->purger->purge();
        } catch (Throwable $throwable) {
            $this->logger->warning('Could not purge old import jobs', ['exception' => $throwable]);
        }
    }
}
