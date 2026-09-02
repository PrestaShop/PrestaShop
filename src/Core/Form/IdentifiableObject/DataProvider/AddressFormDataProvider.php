<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Address\Query\GetCustomerAddressForEditing;
use PrestaShop\PrestaShop\Core\Domain\Address\QueryResult\EditableCustomerAddress;

/**
 * Provides data for address add/edit form
 */
final class AddressFormDataProvider implements FormDataProviderInterface
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
    public function __construct(CommandBusInterface $queryBus, int $defaultCountryId)
    {
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
        /** @var EditableCustomerAddress $editableAddress */
        $editableAddress = $this->queryBus->handle(new GetCustomerAddressForEditing((int) $addressId));

        $data = [
            'id_customer' => $editableAddress->getCustomerId()->getValue(),
            'customer_email' => $editableAddress->getCustomerEmail(),
            'dni' => $editableAddress->getDni(),
            'alias' => $editableAddress->getAddressAlias(),
            'first_name' => $editableAddress->getFirstName(),
            'last_name' => $editableAddress->getLastName(),
            'company' => $editableAddress->getCompany(),
            'vat_number' => $editableAddress->getVatNumber(),
            'address1' => $editableAddress->getAddress(),
            'address2' => $editableAddress->getAddress2(),
            'city' => $editableAddress->getCity(),
            'postcode' => $editableAddress->getPostCode(),
            'id_country' => $editableAddress->getCountryId()->getValue(),
            'id_state' => $editableAddress->getStateId()->getValue(),
            'phone' => $editableAddress->getHomePhone(),
            'phone_mobile' => $editableAddress->getMobilePhone(),
            'other' => $editableAddress->getOther(),
        ];

        return $data;
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
