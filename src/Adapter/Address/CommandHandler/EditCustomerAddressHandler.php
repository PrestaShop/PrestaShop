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
use PrestaShop\PrestaShop\Adapter\Address\AbstractAddressHandler;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\EditCustomerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\CommandHandler\EditCustomerAddressHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\CannotUpdateAddressException;
use PrestaShopException;

/**
 * Handles update of customer address
 */
final class EditCustomerAddressHandler extends AbstractAddressHandler implements EditCustomerAddressHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws AddressException
     * @throws AddressConstraintException
     * @throws CannotUpdateAddressException
     */
    public function handle(EditCustomerAddressCommand $command): void
    {
        try {
            $address = $this->getAddressFromCommand($command);

            if (false === $address->validateFields(false)) {
                throw new AddressConstraintException('Address contains invalid field values');
            }

            if (false === $address->update()) {
                throw new CannotUpdateAddressException(sprintf('Failed to update address "%s"', $address->id));
            }
        } catch (PrestaShopException $e) {
            throw new AddressException(sprintf('An error occurred when updating address "%s"', $command->getAddressId()->getValue()));
        }
    }

    /**
     * @param EditCustomerAddressCommand $command
     *
     * @return Address
     *
     * @throws AddressException
     * @throws AddressNotFoundException
     */
    private function getAddressFromCommand(EditCustomerAddressCommand $command): Address
    {
        $address = $this->getAddress($command->getAddressId());

        if (null !== $command->getLastName()) {
            $address->lastname = $command->getLastName();
        }

        if (null !== $command->getFirstName()) {
            $address->firstname = $command->getFirstName();
        }

        if (null !== $command->getAddress()) {
            $address->address1 = $command->getAddress();
        }

        if (null !== $command->getPostCode()) {
            $address->postcode = $command->getPostCode();
        }

        if (null !== $command->getCountryId()) {
            $address->id_country = $command->getCountryId()->getValue();
        }

        if (null !== $command->getCity()) {
            $address->city = $command->getCity();
        }

        if (null !== $command->getAddressAlias()) {
            $address->alias = $command->getAddressAlias();
        }

        if (null !== $command->getAddress2()) {
            $address->address2 = $command->getAddress2();
        }

        if (null !== $command->getDni()) {
            $address->dni = $command->getDni();
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
