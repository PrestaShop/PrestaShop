<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Address\QueryHandler;

use Customer;
use PrestaShop\PrestaShop\Adapter\Address\AbstractAddressHandler;
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
use PrestaShopException;

/**
 * Handles query which gets customer address for editing
 */
final class GetCustomerAddressForEditingHandler extends AbstractAddressHandler implements GetCustomerAddressForEditingHandlerInterface
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
            throw new CustomerNotFoundException(
                $customerId,
                sprintf('Customer with id "%s" was not found.', $customerId->getValue())
            );
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
            $this->getRequiredFields()
        );

        if (null !== $address->postcode) {
            $editableCustomerAddress->setPostCode($address->postcode);
        }

        if (null !== $address->dni) {
            $editableCustomerAddress->setIdNumber($address->dni);
        }

        if (null !== $address->company) {
            $editableCustomerAddress->setCompany($address->company);
        }

        if (null !== $address->vat_number) {
            $editableCustomerAddress->setVatNumber($address->vat_number);
        }

        if (null !== $address->address2) {
            $editableCustomerAddress->setAddress2($address->address2);
        }

        if (null !== $address->id_state && $address->id_state > 0) {
            $editableCustomerAddress->setStateId($address->id_state);
        }

        if (null !== $address->phone) {
            $editableCustomerAddress->setHomePhone($address->phone);
        }

        if (null !== $address->phone_mobile) {
            $editableCustomerAddress->setMobilePhone($address->phone_mobile);
        }

        if (null !== $address->other) {
            $editableCustomerAddress->setOther($address->other);
        }

        return $editableCustomerAddress;
    }
}
