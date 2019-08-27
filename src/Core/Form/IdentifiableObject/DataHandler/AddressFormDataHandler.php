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
        $addressCustomer = $this->commandBus->handle(new GetCustomerForAddressCreation($data['alias']));

        $addAddressCommand = new AddCustomerAddressCommand(
            $addressCustomer->getCustomerId()->getValue(),
            $data['alias'],
            $data['first_name'],
            $data['last_name'],
            $data['address1'],
            $data['city'],
            $data['postcode'],
            (int) $data['id_country']
        );

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

        if (null !== $data['phone_mobile']) {
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
     */
    public function update($countryId, array $data)
    {
    }
}
