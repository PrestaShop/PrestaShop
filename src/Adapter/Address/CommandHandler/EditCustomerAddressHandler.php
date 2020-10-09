<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Address\CommandHandler;

use Address;
use Country;
use PrestaShop\PrestaShop\Adapter\Address\AbstractAddressHandler;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\EditCustomerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\CommandHandler\EditCustomerAddressHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\CannotAddAddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\CannotUpdateAddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\DeleteAddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
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
    public function handle(EditCustomerAddressCommand $command): AddressId
    {
        try {
            $editedAddress = $this->getAddressFromCommand($command);
            $this->validateAddress($editedAddress);

            // The address is used by an order so it is not edited directly, instead a copy is created and
            if ($editedAddress->isUsed()) {
                // Get a copy of current address
                $copyAddress = new Address($editedAddress->id);

                // Reset ID to force recreating a new address
                $editedAddress->id = $editedAddress->id_address = null;

                // We consider this address as necessarily NOT deleted, in case you were editing a deleted address
                // from an order then the newly edited address should not be deleted, so that you can select it
                $editedAddress->deleted = 0;
                if (false === $editedAddress->save()) {
                    throw new CannotAddAddressException(sprintf('Failed to add new address "%s"', $command->getAddress()));
                }
                // Soft delete the former address
                if (false === $copyAddress->delete()) {
                    throw new DeleteAddressException(sprintf('Cannot delete Address object with id "%s".', $copyAddress->id), DeleteAddressException::FAILED_DELETE);
                }
            } elseif (false === $editedAddress->update()) {
                throw new CannotUpdateAddressException(sprintf('Failed to update address "%s"', $editedAddress->id));
            }
        } catch (PrestaShopException $e) {
            throw new AddressException(sprintf('An error occurred when updating address "%s"', $command->getAddressId()->getValue()));
        }

        return new AddressId((int) $editedAddress->id);
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

        if (null !== $command->getStateId()) {
            $address->id_state = $command->getStateId()->getValue();
        } elseif (null !== $command->getCountryId()) {
            // If country was changed but not state we check if state value needs to be reset
            $country = new Country($command->getCountryId()->getValue());
            if (!$country->contains_states) {
                $address->id_state = 0;
            }
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
