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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\AddManufacturerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\EditManufacturerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;

/**
 * Handles submitted manufacturer address form data
 */
final class ManufacturerAddressFormDataHandler implements FormDataHandlerInterface
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
        /** @var AddressId $addressId */
        $addressId = $this->commandBus->handle(new AddManufacturerAddressCommand(
            $data['last_name'],
            $data['first_name'],
            $data['address'],
            $data['id_country'],
            $data['city'],
            $data['id_manufacturer'],
            $data['address2'],
            $data['post_code'],
            $data['id_state'],
            $data['home_phone'],
            $data['mobile_phone'],
            $data['other'],
            $data['dni']
        ));

        return $addressId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($addressId, array $data)
    {
        $command = new EditManufacturerAddressCommand((int) $addressId);
        $this->fillCommandWithData($command, $data);

        $this->commandBus->handle($command);
    }

    /**
     * Fills EditManufacturerAddressCommand with form data
     *
     * @param EditManufacturerAddressCommand $command
     * @param array $data
     *
     * @throws AddressConstraintException
     */
    private function fillCommandWithData(EditManufacturerAddressCommand $command, array $data)
    {
        if (null !== $data['id_manufacturer']) {
            $command->setManufacturerId($data['id_manufacturer']);
        }
        if (null !== $data['last_name']) {
            $command->setLastName($data['last_name']);
        }
        if (null !== $data['first_name']) {
            $command->setFirstName($data['first_name']);
        }
        if (null !== $data['address']) {
            $command->setAddress($data['address']);
        }
        if (null !== $data['id_country']) {
            $command->setCountryId($data['id_country']);
        }
        if (null !== $data['city']) {
            $command->setCity($data['city']);
        }
        if (null !== $data['address2']) {
            $command->setAddress2($data['address2']);
        }
        if (null !== $data['post_code']) {
            $command->setPostCode($data['post_code']);
        }
        if (null !== $data['id_state']) {
            $command->setStateId($data['id_state']);
        }
        if (null !== $data['home_phone']) {
            $command->setHomePhone($data['home_phone']);
        }
        if (null !== $data['mobile_phone']) {
            $command->setMobilePhone($data['mobile_phone']);
        }
        if (null !== $data['other']) {
            $command->setOther($data['other']);
        }
        if (null !== $data['dni']) {
            $command->setDni($data['dni']);
        }
    }
}
