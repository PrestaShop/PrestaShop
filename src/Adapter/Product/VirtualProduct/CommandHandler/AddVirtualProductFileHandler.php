<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\Update\VirtualProductUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\AddVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\CommandHandler\AddVirtualProductFileHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\ValueObject\VirtualProductFileId;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
use ProductDownload as VirtualProductFile;

/**
 * Handles @see AddVirtualProductFileCommand using legacy object model
 *
 * Legacy object ProductDownload is referred as VirtualProductFile in Core
 */
#[AsCommandHandler]
final class AddVirtualProductFileHandler implements AddVirtualProductFileHandlerInterface
{
    /**
     * @var VirtualProductUpdater
     */
    private $virtualProductUpdater;

    /**
     * @param VirtualProductUpdater $virtualProductUpdater
     */
    public function __construct(
        VirtualProductUpdater $virtualProductUpdater
    ) {
        $this->virtualProductUpdater = $virtualProductUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddVirtualProductFileCommand $command): VirtualProductFileId
    {
        return $this->virtualProductUpdater->addFile(
            $command->getProductId(),
            $command->getFilePath(),
            $this->buildObjectModel($command)
        );
    }

    /**
     * @param AddVirtualProductFileCommand $command
     *
     * @return VirtualProductFile
     */
    private function buildObjectModel(AddVirtualProductFileCommand $command): VirtualProductFile
    {
        $virtualProductFile = new VirtualProductFile();
        $virtualProductFile->display_filename = $command->getDisplayName();
        $virtualProductFile->nb_days_accessible = $command->getAccessDays() ?: 0;
        $virtualProductFile->nb_downloadable = $command->getDownloadTimesLimit() ?: 0;
        $virtualProductFile->date_expiration = $command->getExpirationDate() ?
            $command->getExpirationDate()->format(DateTime::DEFAULT_DATETIME_FORMAT) :
            null
        ;

        return $virtualProductFile;
    }
}
