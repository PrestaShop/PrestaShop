<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Manufacturer\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\ToggleManufacturerStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\CommandHandler\ToggleManufacturerStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\UpdateManufacturerException;

/**
 * Handles command which toggles manufacturer status
 */
#[AsCommandHandler]
final class ToggleManufacturerStatusHandler extends AbstractManufacturerCommandHandler implements ToggleManufacturerStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(ToggleManufacturerStatusCommand $command)
    {
        $manufacturer = $this->getManufacturer($command->getManufacturerId());

        if (!$this->toggleManufacturerStatus($manufacturer, $command->getExpectedStatus())) {
            throw new UpdateManufacturerException(sprintf('Unable to toggle manufacturer status with id "%s"', $manufacturer->id), UpdateManufacturerException::FAILED_UPDATE_STATUS);
        }
    }
}
