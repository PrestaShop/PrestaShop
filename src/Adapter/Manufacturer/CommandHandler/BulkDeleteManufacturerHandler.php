<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Manufacturer\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\BulkDeleteManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\CommandHandler\BulkDeleteManufacturerHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\DeleteManufacturerException;

/**
 * Handles command which deletes manufacturers in bulk action
 */
#[AsCommandHandler]
final class BulkDeleteManufacturerHandler extends AbstractManufacturerCommandHandler implements BulkDeleteManufacturerHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteManufacturerCommand $command)
    {
        foreach ($command->getManufacturerIds() as $manufacturerId) {
            $manufacturer = $this->getManufacturer($manufacturerId);

            if (!$this->deleteManufacturer($manufacturer)) {
                throw new DeleteManufacturerException(sprintf('Cannot delete Manufacturer object with id "%s".', $manufacturer->id), DeleteManufacturerException::FAILED_BULK_DELETE);
            }
        }
    }
}
