<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\Repository\VirtualProductFileRepository;
use PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\Update\VirtualProductUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\UpdateVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\CommandHandler\UpdateVirtualProductFileHandlerInterface;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
use ProductDownload as VirtualProductFile;

/**
 * Updates VirtualProductFile using legacy object model. (ProductDownload is referenced as VirtualProduct in core)
 */
#[AsCommandHandler]
final class UpdateVirtualProductFileHandler implements UpdateVirtualProductFileHandlerInterface
{
    /**
     * @var VirtualProductUpdater
     */
    private $virtualProductUpdater;

    /**
     * @var VirtualProductFileRepository
     */
    private $virtualProductFileRepository;

    /**
     * @param VirtualProductUpdater $virtualProductUpdater
     * @param VirtualProductFileRepository $virtualProductFileRepository
     */
    public function __construct(
        VirtualProductUpdater $virtualProductUpdater,
        VirtualProductFileRepository $virtualProductFileRepository
    ) {
        $this->virtualProductUpdater = $virtualProductUpdater;
        $this->virtualProductFileRepository = $virtualProductFileRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateVirtualProductFileCommand $command): void
    {
        $virtualProductFile = $this->virtualProductFileRepository->get($command->getVirtualProductFileId());
        $this->fillEntityWithCommandData($virtualProductFile, $command);

        $this->virtualProductUpdater->updateFile(
            $virtualProductFile,
            $command->getFilePath()
        );
    }

    /**
     * @param VirtualProductFile $virtualProductFile
     * @param UpdateVirtualProductFileCommand $command
     */
    private function fillEntityWithCommandData(VirtualProductFile $virtualProductFile, UpdateVirtualProductFileCommand $command): void
    {
        if (null !== $command->getDisplayName()) {
            $virtualProductFile->display_filename = $command->getDisplayName();
        }
        if (null !== $command->getAccessDays()) {
            $virtualProductFile->nb_days_accessible = $command->getAccessDays();
        }
        if (null !== $command->getDownloadTimesLimit()) {
            $virtualProductFile->nb_downloadable = $command->getDownloadTimesLimit();
        }
        if (null !== $command->getExpirationDate()) {
            $virtualProductFile->date_expiration = $command->getExpirationDate()->format(DateTime::DEFAULT_DATE_FORMAT);
        }
    }
}
