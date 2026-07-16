<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Import\CommandHandler;

use Exception;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Import\Command\StartImportRunCommand;
use PrestaShop\PrestaShop\Core\Domain\Import\CommandHandler\StartImportRunHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\CannotStartImportRunException;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportRunId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Import\File\CsvFileReader;
use PrestaShop\PrestaShop\Core\Import\File\FileOpenerInterface;
use PrestaShop\PrestaShop\Core\Import\ImportDirectory;
use PrestaShopBundle\Entity\ImportRun;
use PrestaShopBundle\Entity\Repository\ImportRunRepository;
use SplFileInfo;

/**
 * @internal
 */
#[AsCommandHandler]
final class StartImportRunHandler implements StartImportRunHandlerInterface
{
    /**
     * Batch size used when the command leaves it unset (0). The wizard normally passes an explicit
     * size computed client-side; this keeps non-UI callers (Behat, API) working without one.
     */
    private const DEFAULT_BATCH_SIZE = 100;

    public function __construct(
        private readonly ImportRunRepository $importRunRepository,
        private readonly FileOpenerInterface $fileOpener,
        private readonly ImportDirectory $importDirectory
    ) {
    }

    public function handle(StartImportRunCommand $command): ImportRunId
    {
        $options = $command->getImportOptions();
        $rawOptions = $command->getOptions();

        $uuid = $this->generateUuid();
        $importRun = new ImportRun(
            $uuid,
            $command->getEntityType()->getValue(),
            $command->getFilename(),
            $command->getLangIso(),
            $options->getSeparator(),
            $options->getMultipleValueSeparator(),
            $options->getSkipRows(),
            $command->getColumnMapping()->getValue(),
            [
                'truncate' => $options->truncate(),
                'match_ref' => $options->matchReferences(),
                'forceIDs' => $options->forceIds(),
                'regenerate' => $options->skipThumbnailRegeneration(),
                'sendemail' => !empty($rawOptions['sendemail']),
            ],
            $command->isValidateOnly(),
            $command->getLimit() > 0 ? $command->getLimit() : self::DEFAULT_BATCH_SIZE,
            $this->resolveShopId($command->getShopConstraint())
        );

        try {
            // The total is counted once, up front, from the uploaded file and the frozen config, so the
            // run knows its size from the start (batches only advance the offset). Reading here uses the
            // run's frozen separator rather than the session, keeping this handler web-context free.
            $importRun->setTotalRows($this->countRows($importRun));
            $this->importRunRepository->add($importRun);
        } catch (Exception $e) {
            throw new CannotStartImportRunException(sprintf('Failed to start import run "%s": %s', $uuid, $e->getMessage()), 0, $e);
        }

        return new ImportRunId($uuid);
    }

    private function countRows(ImportRun $importRun): int
    {
        $file = new SplFileInfo($this->importDirectory . $importRun->getFilename());

        $reader = new CsvFileReader($this->fileOpener, $importRun->getSeparator());
        $count = iterator_count($reader->read($file));

        return max(0, $count - $importRun->getSkipRows());
    }

    private function resolveShopId(?ShopConstraint $shopConstraint): ?int
    {
        if (null === $shopConstraint) {
            return null;
        }

        $shopId = $shopConstraint->getShopId();

        return null === $shopId ? null : $shopId->getValue();
    }

    /**
     * Generates an RFC 4122 v4 UUID (matches the {@see ImportRunId} pattern) without pulling in a
     * UUID library — none is installed in vendor.
     */
    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0F | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3F | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
