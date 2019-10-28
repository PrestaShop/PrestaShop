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

namespace PrestaShop\PrestaShop\Adapter\Address\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Address\AbstractManufacturerAddressHandler;
use PrestaShop\PrestaShop\Core\Domain\Address\Query\GetManufacturerAddressForEditing;
use PrestaShop\PrestaShop\Core\Domain\Address\QueryHandler\GetManufacturerAddressForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\QueryResult\EditableManufacturerAddress;

/**
 * Handles query which gets manufacturer address for editing
 */
final class GetManufacturerAddressForEditingHandler extends AbstractManufacturerAddressHandler implements GetManufacturerAddressForEditingHandlerInterface
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
