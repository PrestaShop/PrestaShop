<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Manufacturer\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\BulkToggleManufacturerStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\CommandHandler\BulkToggleManufacturerStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\UpdateManufacturerException;

/**
 * Handles command which toggles manufacturer status in bulk action
 */
#[AsCommandHandler]
final class BulkToggleManufacturerStatusHandler extends AbstractManufacturerCommandHandler implements BulkToggleManufacturerStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkToggleManufacturerStatusCommand $command)
    {
        foreach ($command->getManufacturerIds() as $manufacturerId) {
            $manufacturer = $this->getManufacturer($manufacturerId);

            if (!$this->toggleManufacturerStatus($manufacturer, $command->getExpectedStatus())) {
                throw new UpdateManufacturerException(sprintf('Unable to toggle manufacturer status with id "%s"', $manufacturer->id), UpdateManufacturerException::FAILED_BULK_UPDATE_STATUS);
            }
        }
    }
}
