<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Address\CommandHandler;

use Address;
use PrestaShop\PrestaShop\Adapter\Address\AbstractAddressHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\AddCustomerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\CommandHandler\AddCustomerAddressHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\CannotAddAddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShopException;

#[AsCommandHandler]
final class AddCustomerAddressHandler extends AbstractAddressHandler implements AddCustomerAddressHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws AddressException
     * @throws AddressConstraintException
     * @throws CannotAddAddressException
     */
    public function handle(AddCustomerAddressCommand $command): AddressId
    {
        $address = $this->createAddressFromCommand($command);

        try {
            $this->validateAddress($address);

            if (false === $address->add()) {
                throw new CannotAddAddressException(sprintf('Failed to add new address "%s"', $command->getAddress()));
            }
        } catch (PrestaShopException) {
            throw new AddressException(sprintf('An error occurred when adding new address "%s"', $command->getAddress()));
        }

        return new AddressId((int) $address->id);
    }

    /**
     * @param AddCustomerAddressCommand $command
     *
     * @return Address
     */
    private function createAddressFromCommand(AddCustomerAddressCommand $command): Address
    {
        $address = new Address();

        $address->id_customer = $command->getCustomerId()->getValue();
        $address->lastname = $command->getLastName();
        $address->firstname = $command->getFirstName();
        $address->address1 = $command->getAddress();
        $address->id_country = $command->getCountryId()->getValue();
        $address->city = $command->getCity();
        $address->alias = $command->getAddressAlias();
        $address->postcode = $command->getPostCode();
        $address->address2 = $command->getAddress2();
        $address->dni = $command->getDni();
        $address->company = $command->getCompany();
        $address->vat_number = $command->getVatNumber();
        $address->id_state = $command->getStateId()->getValue();
        $address->phone = $command->getHomePhone();
        $address->phone_mobile = $command->getMobilePhone();
        $address->other = $command->getOther();

        return $address;
    }
}
