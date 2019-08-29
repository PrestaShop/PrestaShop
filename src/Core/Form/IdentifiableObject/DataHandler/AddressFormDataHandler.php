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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\AddCustomerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\EditCustomerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerForAddressCreation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\AddressCreationCustomer;
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
     * @param CommandBusInterface $commandBus
     */
    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * {@inheritdoc]
     *
     * @throws CountryConstraintException
     * @throws CustomerException
     * @throws StateConstraintException
     */
    public function create(array $data)
    {
        /** @var AddressCreationCustomer $addressCustomer */
        $addressCustomer = $this->commandBus->handle(new GetCustomerForAddressCreation($data['customer_email']));

        $addAddressCommand = new AddCustomerAddressCommand(
            $addressCustomer->getCustomerId()->getValue(),
            $data['alias'],
            $data['first_name'],
            $data['last_name'],
            $data['address1'],
            $data['city'],
            (int) $data['id_country']
        );

        if (null !== $data['postcode']) {
            $addAddressCommand->setPostCode($data['postcode']);
        }

        if (null !== $data['dni']) {
            $addAddressCommand->setIdNumber($data['dni']);
        }

        if (null !== $data['company']) {
            $addAddressCommand->setCompany($data['company']);
        }

        if (null !== $data['vat_number']) {
            $addAddressCommand->setVatNumber($data['vat_number']);
        }

        if (null !== $data['address2']) {
            $addAddressCommand->setAddress2($data['address2']);
        }

        if (null !== $data['id_state']) {
            $addAddressCommand->setStateId((int) $data['id_state']);
        }

        if (null !== $data['phone']) {
            $addAddressCommand->setHomePhone($data['phone']);
        }

        if (isset($data['phone_mobile']) && null !== $data['phone_mobile']) {
            $addAddressCommand->setMobilePhone($data['phone_mobile']);
        }

        if (null !== $data['other']) {
            $addAddressCommand->setOther($data['other']);
        }

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

        if (null !== $data['alias']) {
            $editAddressCommand->setAddressAlias($data['alias']);
        }

        if (null !== $data['first_name']) {
            $editAddressCommand->setFirstName($data['first_name']);
        }

        if (null !== $data['last_name']) {
            $editAddressCommand->setLastName($data['last_name']);
        }

        if (null !== $data['address1']) {
            $editAddressCommand->setAddress($data['address1']);
        }

        if (null !== $data['city']) {
            $editAddressCommand->setCity($data['city']);
        }

        if (null !== $data['id_country']) {
            $editAddressCommand->setCountryId((int) $data['id_country']);
        }

        if (null !== $data['postcode']) {
            $editAddressCommand->setPostCode($data['postcode']);
        }

        if (null !== $data['dni']) {
            $editAddressCommand->setIdNumber($data['dni']);
        }

        if (null !== $data['company']) {
            $editAddressCommand->setCompany($data['company']);
        }

        if (null !== $data['vat_number']) {
            $editAddressCommand->setVatNumber($data['vat_number']);
        }

        if (null !== $data['address2']) {
            $editAddressCommand->setAddress2($data['address2']);
        }

        if (null !== $data['id_state']) {
            $editAddressCommand->setStateId((int) $data['id_state']);
        }

        if (null !== $data['phone']) {
            $editAddressCommand->setHomePhone($data['phone']);
        }

        if (isset($data['phone_mobile']) && null !== $data['phone_mobile']) {
            $editAddressCommand->setMobilePhone($data['phone_mobile']);
        }

        if (null !== $data['other']) {
            $editAddressCommand->setOther($data['other']);
        }

        $this->commandBus->handle($editAddressCommand);
    }
}
