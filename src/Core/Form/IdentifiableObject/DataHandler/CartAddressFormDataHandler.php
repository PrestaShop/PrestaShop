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
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\EditCartAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidAddressTypeException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;

class CartAddressFormDataHandler implements FormDataHandlerInterface
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
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        // Not used for creation, only edition
    }

    /**
     * {@inheritdoc}
     *
     * @throws CartConstraintException
     * @throws CountryConstraintException
     * @throws StateConstraintException
     * @throws InvalidAddressTypeException
     */
    public function update($cartId, array $data)
    {
        $editAddressCommand = new EditCartAddressCommand($cartId, $data['address_type']);

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
