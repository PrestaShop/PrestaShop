<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Address\CommandHandler;

use Address;
use PrestaShop\PrestaShop\Adapter\Address\AbstractAddressHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\AddManufacturerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\CommandHandler\AddManufacturerAddressHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShopException;

/**
 * Adds manufacturer address using legacy object model
 */
#[AsCommandHandler]
final class AddManufacturerAddressHandler extends AbstractAddressHandler implements AddManufacturerAddressHandlerInterface
{
    /**
     * @param AddManufacturerAddressCommand $command
     *
     * @return AddressId
     *
     * @throws AddressException
     */
    public function handle(AddManufacturerAddressCommand $command)
    {
        $address = $this->createAddressFromCommand($command);

        try {
            $this->validateAddress($address);
            if (false === $address->add()) {
                throw new AddressException(sprintf('Failed to add new address "%s"', $command->getAddress()));
            }
        } catch (PrestaShopException) {
            throw new AddressException(sprintf('An error occurred when adding new address "%s"', $command->getAddress()));
        }

        return new AddressId((int) $address->id);
    }

    /**
     * @param AddManufacturerAddressCommand $command
     *
     * @return Address
     */
    private function createAddressFromCommand(AddManufacturerAddressCommand $command)
    {
        $address = new Address();
        $address->id_manufacturer = $command->getManufacturerId();
        $address->lastname = $command->getLastName();
        $address->firstname = $command->getFirstName();
        $address->address1 = $command->getAddress();
        $address->address2 = $command->getAddress2();
        $address->postcode = $command->getPostCode();
        $address->id_country = $command->getCountryId();
        $address->city = $command->getCity();
        $address->id_state = $command->getStateId();
        $address->phone = $command->getHomePhone();
        $address->phone_mobile = $command->getMobilePhone();
        $address->other = $command->getOther();
        $address->dni = $command->getDni();
        $address->alias = 'manufacturer';

        return $address;
    }
}
