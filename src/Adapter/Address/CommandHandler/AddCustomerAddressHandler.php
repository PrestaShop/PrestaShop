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
        } catch (PrestaShopException $e) {
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
