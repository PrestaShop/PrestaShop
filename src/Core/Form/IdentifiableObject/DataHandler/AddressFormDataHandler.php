<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Adapter\Customer\CustomerDataProvider;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\AddCustomerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\EditCustomerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;

/**
 * Handles submitted address form data
 */
final class AddressFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var CustomerDataProvider
     */
    private $customerDataProvider;

    /**
     * @param CommandBusInterface $commandBus
     * @param CustomerDataProvider $customerDataProvider
     */
    public function __construct(CommandBusInterface $commandBus, CustomerDataProvider $customerDataProvider)
    {
        $this->commandBus = $commandBus;
        $this->customerDataProvider = $customerDataProvider;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CountryConstraintException
     * @throws StateConstraintException
     */
    public function create(array $data)
    {
        if (!empty($data['id_customer'])) {
            $customerId = $data['id_customer'];
        } else {
            $customerId = $this->customerDataProvider->getIdByEmail($data['customer_email']);
        }

        $addAddressCommand = new AddCustomerAddressCommand(
            $customerId,
            $data['alias'] ?? '',
            $data['first_name'] ?? '',
            $data['last_name'] ?? '',
            $data['address1'] ?? '',
            $data['city'] ?? '',
            (int) $data['id_country'],
            $data['postcode'] ?? '',
            $data['dni'] ?? '',
            $data['company'] ?? '',
            $data['vat_number'] ?? '',
            $data['address2'] ?? '',
            (int) $data['id_state'],
            $data['phone'] ?? null,
            $data['phone_mobile'] ?? null,
            $data['other'] ?? ''
        );

        /** @var AddressId $addressId */
        $addressId = $this->commandBus->handle($addAddressCommand);

        return $addressId->getValue();
    }

    /**
     * {@inheritdoc}
     *
     * @throws AddressConstraintException
     * @throws CountryConstraintException
     * @throws StateConstraintException
     */
    public function update($addressId, array $data)
    {
        $editAddressCommand = new EditCustomerAddressCommand($addressId);

        if (isset($data['alias'])) {
            $editAddressCommand->setAddressAlias($data['alias']);
        }

        if (isset($data['first_name'])) {
            $editAddressCommand->setFirstName($data['first_name']);
        }

        if (isset($data['last_name'])) {
            $editAddressCommand->setLastName($data['last_name']);
        }

        if (isset($data['address1'])) {
            $editAddressCommand->setAddress($data['address1']);
        }

        if (isset($data['city'])) {
            $editAddressCommand->setCity($data['city']);
        }

        if (isset($data['id_country'])) {
            $editAddressCommand->setCountryId((int) $data['id_country']);
        }

        if (isset($data['postcode'])) {
            $editAddressCommand->setPostCode($data['postcode']);
        }

        if (isset($data['dni'])) {
            $editAddressCommand->setDni($data['dni']);
        }

        if (isset($data['company'])) {
            $editAddressCommand->setCompany($data['company']);
        }

        if (isset($data['vat_number'])) {
            $editAddressCommand->setVatNumber($data['vat_number']);
        }

        if (isset($data['address2'])) {
            $editAddressCommand->setAddress2($data['address2']);
        }

        if (isset($data['id_state'])) {
            $editAddressCommand->setStateId((int) $data['id_state']);
        }

        if (isset($data['phone'])) {
            $editAddressCommand->setHomePhone($data['phone']);
        }

        if (isset($data['phone_mobile'])) {
            $editAddressCommand->setMobilePhone($data['phone_mobile']);
        }

        if (isset($data['other'])) {
            $editAddressCommand->setOther($data['other']);
        }

        $this->commandBus->handle($editAddressCommand);
    }
}
