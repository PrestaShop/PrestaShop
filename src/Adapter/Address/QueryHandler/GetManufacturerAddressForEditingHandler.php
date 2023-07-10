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

namespace PrestaShop\PrestaShop\Adapter\Address\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Address\AbstractAddressHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Address\Query\GetManufacturerAddressForEditing;
use PrestaShop\PrestaShop\Core\Domain\Address\QueryHandler\GetManufacturerAddressForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\QueryResult\EditableManufacturerAddress;

/**
 * Handles query which gets manufacturer address for editing
 */
#[AsQueryHandler]
final class GetManufacturerAddressForEditingHandler extends AbstractAddressHandler implements GetManufacturerAddressForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetManufacturerAddressForEditing $query)
    {
        $addressId = $query->getAddressId();

        $address = $this->getAddress($addressId);

        return new EditableManufacturerAddress(
            $addressId,
            $address->lastname,
            $address->firstname,
            $address->address1,
            $address->city,
            (int) $address->id_manufacturer,
            (int) $address->id_country,
            $address->address2,
            $address->postcode,
            (int) $address->id_state,
            $address->phone,
            $address->phone_mobile,
            $address->other,
            $address->dni
        );
    }
}
