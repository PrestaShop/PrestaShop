<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Address\CommandHandler;

use Address;
use Country;
use PrestaShop\PrestaShop\Adapter\Address\AbstractAddressHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\EditManufacturerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\CommandHandler\EditManufacturerAddressHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressException;
use PrestaShopException;

/**
 * Handles command which edits manufacturer address
 */
#[AsCommandHandler]
final class EditManufacturerAddressHandler extends AbstractAddressHandler implements EditManufacturerAddressHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(EditManufacturerAddressCommand $command)
    {
        $addressId = $command->getAddressId();
        $address = $this->getAddress($addressId);
        $this->populateAddressWithData($address, $command);

        try {
            $this->validateAddress($address);
            if (!$address->update()) {
                throw new AddressException(sprintf('Cannot update address with id "%s"', $address->id));
            }
        } catch (PrestaShopException) {
            throw new AddressException(sprintf('Cannot update address with id "%s"', $address->id));
        }
    }

    /**
     * Populates Address object with given data
     *
     * @param Address $address
     * @param EditManufacturerAddressCommand $command
     */
    private function populateAddressWithData(Address $address, $command)
    {
        if (null !== $command->getManufacturerId()) {
            $address->id_manufacturer = $command->getManufacturerId();
        }
        if (null !== $command->getLastName()) {
            $address->lastname = $command->getLastName();
        }
        if (null !== $command->getFirstName()) {
            $address->firstname = $command->getFirstName();
        }
        if (null !== $command->getAddress()) {
            $address->address1 = $command->getAddress();
        }
        if (null !== $command->getAddress2()) {
            $address->address2 = $command->getAddress2();
        }
        if (null !== $command->getPostCode()) {
            $address->postcode = $command->getPostCode();
        }
        if (null !== $command->getCity()) {
            $address->city = $command->getCity();
        }
        if (null !== $command->getCountryId()) {
            $address->id_country = $command->getCountryId();
        }
        if (null !== $command->getStateId()) {
            $address->id_state = $command->getStateId();
        } elseif (null !== $command->getCountryId()) {
            // If country was changed but not state we check if state value needs to be reset
            $country = new Country($command->getCountryId());
            if (!$country->contains_states) {
                $address->id_state = 0;
            }
        }
        if (null !== $command->getHomePhone()) {
            $address->phone = $command->getHomePhone();
        }
        if (null !== $command->getMobilePhone()) {
            $address->phone_mobile = $command->getMobilePhone();
        }
        if (null !== $command->getOther()) {
            $address->other = $command->getOther();
        }
        if (null !== $command->getDni()) {
            $address->dni = $command->getDni();
        }
    }
}
