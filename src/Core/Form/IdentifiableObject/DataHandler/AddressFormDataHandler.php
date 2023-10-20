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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
            $data['alias'],
            $data['first_name'],
            $data['last_name'],
            $data['address1'],
            $data['city'],
            (int) $data['id_country'],
            $data['postcode'],
            $data['dni'],
            $data['company'],
            $data['vat_number'],
            $data['address2'],
            (int) $data['id_state'],
            $data['phone'],
            $data['phone_mobile'] ?? null,
            $data['other']
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
