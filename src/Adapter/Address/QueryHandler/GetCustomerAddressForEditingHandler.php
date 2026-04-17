<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Address\QueryHandler;

use Customer;
use PrestaShop\PrestaShop\Adapter\Address\AbstractCustomerAddressHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Address\Query\GetCustomerAddressForEditing;
use PrestaShop\PrestaShop\Core\Domain\Address\QueryHandler\GetCustomerAddressForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\QueryResult\EditableCustomerAddress;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\NoStateId;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShopException;

/**
 * Handles query which gets customer address for editing
 */
#[AsQueryHandler]
final class GetCustomerAddressForEditingHandler extends AbstractCustomerAddressHandler implements GetCustomerAddressForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws AddressException
     * @throws AddressNotFoundException
     * @throws CustomerException
     * @throws CustomerNotFoundException
     * @throws CountryConstraintException
     * @throws StateConstraintException
     */
    public function handle(GetCustomerAddressForEditing $query): EditableCustomerAddress
    {
        $addressId = $query->getAddressId();
        $address = $this->getAddress($addressId);

        try {
            $customerId = new CustomerId((int) $address->id_customer);
            $customer = new Customer($customerId->getValue());
        } catch (PrestaShopException $e) {
            throw new CustomerException('Failed to get customer', 0, $e);
        }

        if ($customer->id !== $customerId->getValue()) {
            throw new CustomerNotFoundException(sprintf('Customer with id "%d" was not found.', $customerId->getValue()));
        }

        $editableCustomerAddress = new EditableCustomerAddress(
            $addressId,
            $customerId,
            $customer->email,
            $address->alias,
            $address->firstname,
            $address->lastname,
            $address->address1,
            $address->city,
            new CountryId((int) $address->id_country),
            $address->postcode,
            $address->dni,
            $address->company,
            $address->vat_number,
            $address->address2,
            (int) $address->id_state !== NoStateId::NO_STATE_ID_VALUE ? new StateId($address->id_state) : new NoStateId(),
            $address->phone,
            $address->phone_mobile,
            $address->other,
            $this->getRequiredFields()
        );

        return $editableCustomerAddress;
    }
}
