<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Manufacturer\CommandHandler;

use Manufacturer;
use PrestaShop\PrestaShop\Adapter\Manufacturer\AbstractManufacturerHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\EditManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\CommandHandler\EditManufacturerHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerException;
use PrestaShopException;

/**
 * Handles command which edits manufacturer using legacy object model
 */
#[AsCommandHandler]
final class EditManufacturerHandler extends AbstractManufacturerHandler implements EditManufacturerHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws ManufacturerException
     */
    public function handle(EditManufacturerCommand $command)
    {
        $manufacturerId = $command->getManufacturerId();
        $manufacturer = $this->getManufacturer($manufacturerId);
        $this->populateManufacturerWithData($manufacturer, $command);

        try {
            if (false === $manufacturer->validateFields(false)) {
                throw new ManufacturerException('Manufacturer contains invalid field values');
            }

            if (!$manufacturer->update()) {
                throw new ManufacturerException(sprintf('Cannot update manufacturer with id "%s"', $manufacturer->id));
            }

            if (null !== $command->getAssociatedShops()) {
                $this->associateWithShops($manufacturer, $command->getAssociatedShops());
            }
        } catch (PrestaShopException) {
            throw new ManufacturerException(sprintf('Cannot update manufacturer with id "%s"', $manufacturer->id));
        }
    }

    /**
     * Populates Manufacturer object with given data
     *
     * @param Manufacturer $manufacturer
     * @param EditManufacturerCommand $command
     */
    private function populateManufacturerWithData(Manufacturer $manufacturer, EditManufacturerCommand $command)
    {
        if (null !== $command->getName()) {
            $manufacturer->name = $command->getName();
        }
        if (null !== $command->getLocalizedShortDescriptions()) {
            $manufacturer->short_description = $command->getLocalizedShortDescriptions();
        }
        if (null !== $command->getLocalizedDescriptions()) {
            $manufacturer->description = $command->getLocalizedDescriptions();
        }
        if (null !== $command->getLocalizedMetaDescriptions()) {
            $manufacturer->meta_description = $command->getLocalizedMetaDescriptions();
        }
        if (null !== $command->getLocalizedMetaTitles()) {
            $manufacturer->meta_title = $command->getLocalizedMetaTitles();
        }
        if (null !== $command->isEnabled()) {
            $manufacturer->active = $command->isEnabled();
        }
    }
}
