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

namespace PrestaShop\PrestaShop\Adapter\Address\CommandHandler;

use Address;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\AddCustomerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\CommandHandler\AddCustomerAddressHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\CannotAddAddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShopException;

final class AddCustomerAddressHandler implements AddCustomerAddressHandlerInterface
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
            if (false === $address->validateFields(false)) {
                throw new AddressConstraintException(
                    'Address contains invalid field values',
                    AddressConstraintException::INVALID_FIELDS
                );
            }

            if (false === $address->add()) {
                throw new CannotAddAddressException(
                    sprintf('Failed to add new address "%s"', $command->getAddress())
                );
            }
        } catch (PrestaShopException $e) {
            throw new AddressException(
                sprintf('An error occurred when adding new address "%s"', $command->getAddress())
            );
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

        if (null !== $command->getPostCode()) {
            $address->postcode = $command->getPostCode();
        }

        if (null !== $command->getAddress2()) {
            $address->address2 = $command->getAddress2();
        }

        if (null !== $command->getIdNumber()) {
            $address->dni = $command->getIdNumber();
        }

        if (null !== $command->getCompany()) {
            $address->company = $command->getCompany();
        }

        if (null !== $command->getVatNumber()) {
            $address->vat_number = $command->getVatNumber();
        }

        if (null !== $command->getStateId()) {
            $address->id_state = $command->getStateId()->getValue();
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

        return $address;
    }
}
