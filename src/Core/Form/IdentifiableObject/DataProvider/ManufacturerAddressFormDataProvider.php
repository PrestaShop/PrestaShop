<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
