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
use PrestaShop\PrestaShop\Core\Domain\Address\Command\AddManufacturerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\CommandHandler\AddManufacturerAddressHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\InvalidAddressFieldException;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShopException;

/**
 * Adds manufacturer address using legacy object model
 */
final class AddManufacturerAddressHandler implements AddManufacturerAddressHandlerInterface
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
            if (false === $address->validateFields(false) || false === $address->validateFieldsLang(false)) {
                throw new InvalidAddressFieldException('Address contains invalid field values');
            }
            if (false === $address->add()) {
                throw new AddressException(
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
