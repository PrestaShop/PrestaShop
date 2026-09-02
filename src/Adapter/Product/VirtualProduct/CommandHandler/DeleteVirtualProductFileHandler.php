<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\Update\VirtualProductUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\DeleteVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\CommandHandler\DeleteVirtualProductFileHandlerInterface;

/**
 * Deletes virtual product file using legacy object model
 */
#[AsCommandHandler]
class DeleteVirtualProductFileHandler implements DeleteVirtualProductFileHandlerInterface
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
    public function handle(DeleteVirtualProductFileCommand $command): void
    {
        $this->virtualProductUpdater->deleteFile($command->getVirtualProductFileId());
    }
}
