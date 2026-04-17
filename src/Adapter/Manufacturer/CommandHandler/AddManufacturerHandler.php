<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Manufacturer\CommandHandler;

use Manufacturer;
use PrestaShop\PrestaShop\Adapter\Manufacturer\AbstractManufacturerHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\AddManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\CommandHandler\AddManufacturerHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use PrestaShopDatabaseException;
use PrestaShopException;

/**
 * Handles command which adds new manufacturer using legacy object model
 */
#[AsCommandHandler]
final class AddManufacturerHandler extends AbstractManufacturerHandler implements AddManufacturerHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddManufacturerCommand $command)
    {
        $manufacturer = new Manufacturer();
        $this->fillLegacyManufacturerWithData($manufacturer, $command);

        try {
            if (false === $manufacturer->validateFields(false)) {
                throw new ManufacturerException('Manufacturer contains invalid field values');
            }

            if (!$manufacturer->add()) {
                throw new ManufacturerException(sprintf('Failed to add new manufacturer "%s"', $command->getName()));
            }
            $this->addShopAssociation($manufacturer, $command);
        } catch (PrestaShopException) {
            throw new ManufacturerException(sprintf('Failed to add new manufacturer "%s"', $command->getName()));
        }

        return new ManufacturerId((int) $manufacturer->id);
    }

    /**
     * Add manufacturer and shop association
     *
     * @param Manufacturer $manufacturer
     * @param AddManufacturerCommand $command
     *
     * @throws PrestaShopDatabaseException
     */
    private function addShopAssociation(Manufacturer $manufacturer, AddManufacturerCommand $command)
    {
        $this->associateWithShops(
            $manufacturer,
            $command->getShopAssociation()
        );
    }

    /**
     * @param Manufacturer $manufacturer
     * @param AddManufacturerCommand $command
     */
    private function fillLegacyManufacturerWithData(Manufacturer $manufacturer, AddManufacturerCommand $command)
    {
        $manufacturer->name = $command->getName();
        $manufacturer->short_description = $command->getLocalizedShortDescriptions();
        $manufacturer->description = $command->getLocalizedDescriptions();
        $manufacturer->meta_title = $command->getLocalizedMetaTitles();
        $manufacturer->meta_description = $command->getLocalizedMetaDescriptions();
        $manufacturer->active = $command->isEnabled();
    }
}
