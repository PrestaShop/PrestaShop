<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\Step;

use PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\Repository\VirtualProductFileRepository;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\AddVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\UpdateVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\FileDownloadException;
use PrestaShop\PrestaShop\Core\Import\Engine\FileDownloader;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ValueParser;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * A product can only ever hold ONE virtual file, so re-importing a row is an
 * UPDATE: VirtualProductUpdater::addFile() throws ALREADY_HAS_A_FILE when the
 * product already has one, and because that lands in the row catch-all it
 * would fail the whole row and get its accessories dropped in the
 * association phase.
 *
 * Must run after the type switch (ProductTypeStep): AddVirtualProductFileCommand
 * requires a virtual product.
 */
class VirtualProductFileStep extends AbstractProductRowStep
{
    public function __construct(
        ValueParser $valueParser,
        protected readonly FileDownloader $fileDownloader,
        protected readonly VirtualProductFileRepository $virtualProductFileRepository,
        protected readonly CommandBusInterface $commandBus,
        protected readonly Filesystem $filesystem,
        protected readonly TranslatorInterface $translator,
    ) {
        parent::__construct($valueParser);
    }

    public function supports(array $row): bool
    {
        return $this->hasValue($row, 'file_url') && $this->isVirtual($row);
    }

    public function apply(array $row, int $rowIndex, int $productId, bool $isCreation, int $languageId, ImportRunContext $context): array
    {
        $fileUrl = $row['file_url'] ?? '';

        try {
            $temporaryFile = $this->fileDownloader->download($fileUrl);
        } catch (FileDownloadException $e) {
            return [new ImportMessage(
                ImportMessage::SEVERITY_WARNING,
                ImportPhaseDefinition::PHASE_DATABASE,
                $this->translator->trans('Virtual product file "%url%" could not be fetched and was skipped: %error%', ['%url%' => $fileUrl, '%error%' => $e->getMessage()], 'Admin.Advparameters.Notification'),
                [$rowIndex],
                'file_url'
            )];
        }

        try {
            $accessDays = $this->valueParser->parseCount($row['nb_days_accessible'] ?? '');
            $downloadTimesLimit = $this->valueParser->parseCount($row['nb_downloadable'] ?? '');
            $expirationDate = $this->valueParser->parseDate($row['date_expiration'] ?? '');
            $displayName = basename(parse_url($fileUrl, PHP_URL_PATH) ?: $fileUrl) ?: 'file';

            $existingFileId = $this->findExistingVirtualProductFileId($productId);
            if (null !== $existingFileId) {
                $updateCommand = new UpdateVirtualProductFileCommand($existingFileId);
                $updateCommand->setFilePath($temporaryFile);
                $updateCommand->setDisplayName($displayName);
                $updateCommand->setAccessDays($accessDays);
                $updateCommand->setDownloadTimesLimit($downloadTimesLimit);
                $updateCommand->setExpirationDate($expirationDate);
                $this->commandBus->handle($updateCommand);

                return [];
            }

            $this->commandBus->handle(new AddVirtualProductFileCommand(
                $productId,
                $temporaryFile,
                $displayName,
                $accessDays,
                $downloadTimesLimit,
                $expirationDate
            ));
        } finally {
            $this->filesystem->remove($temporaryFile);
        }

        return [];
    }

    protected function findExistingVirtualProductFileId(int $productId): ?int
    {
        return $this->virtualProductFileRepository->findIdByProductId(new ProductId($productId))?->getValue();
    }
}
