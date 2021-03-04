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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Address\Query\GetManufacturerAddressForEditing;
use PrestaShop\PrestaShop\Core\Domain\Address\QueryResult\EditableManufacturerAddress;

/**
 * Provides data for address add/edit form
 */
final class ManufacturerAddressFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var int
     */
    private $defaultCountryId;

    /**
     * @param CommandBusInterface $queryBus
     * @param int $defaultCountryId
     */
    public function __construct(
        CommandBusInterface $queryBus,
        $defaultCountryId
    ) {
        $this->queryBus = $queryBus;
        $this->defaultCountryId = $defaultCountryId;
    }

    /**
     * {@inheritdoc}
     *
     * @throws AddressConstraintException
     */
    public function getData($addressId)
    {
        /** @var EditableManufacturerAddress $editableAddress */
        $editableAddress = $this->queryBus->handle(new GetManufacturerAddressForEditing((int) $addressId));
        $manufacturerId = $editableAddress->getManufacturerId();

        return [
            'id_manufacturer' => $manufacturerId,
            'last_name' => $editableAddress->getLastName(),
            'first_name' => $editableAddress->getFirstName(),
            'address' => $editableAddress->getAddress(),
            'city' => $editableAddress->getCity(),
            'address2' => $editableAddress->getAddress2(),
            'id_country' => $editableAddress->getCountryId(),
            'post_code' => $editableAddress->getPostCode(),
            'id_state' => $editableAddress->getStateId(),
            'home_phone' => $editableAddress->getHomePhone(),
            'mobile_phone' => $editableAddress->getMobilePhone(),
            'other' => $editableAddress->getOther(),
            'dni' => $editableAddress->getDni(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return [
            'id_country' => $this->defaultCountryId,
        ];
    }
}
