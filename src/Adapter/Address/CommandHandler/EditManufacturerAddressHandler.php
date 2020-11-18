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
use PrestaShop\PrestaShop\Adapter\Address\AbstractManufacturerAddressHandler;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\EditManufacturerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\CommandHandler\EditManufacturerAddressHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\InvalidAddressFieldException;
use PrestaShopException;

/**
 * Handles command which edits manufacturer address
 */
final class EditManufacturerAddressHandler extends AbstractManufacturerAddressHandler implements EditManufacturerAddressHandlerInterface
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
            if (false === $address->validateFields(false) || false === $address->validateFieldsLang(false)) {
                throw new InvalidAddressFieldException('Address contains invalid field values');
            }
            if (!$address->update()) {
                throw new AddressException(
                    sprintf('Cannot update address with id "%s"', $address->id)
                );
            }
        } catch (PrestaShopException $e) {
            throw new AddressException(
                sprintf('Cannot update address with id "%s"', $address->id)
            );
        }
    }

    /**
     * Populates Address object with given data
     *
     * @param $address
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
