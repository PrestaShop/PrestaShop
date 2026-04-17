<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
