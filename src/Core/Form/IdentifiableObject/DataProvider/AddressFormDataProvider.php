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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Address\Query\GetCustomerAddressForEditing;
use PrestaShop\PrestaShop\Core\Domain\Address\Query\GetRequiredFieldsForAddress;
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
            'dni' => $editableAddress->getIdNumber(),
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
            'required_fields' => $editableAddress->getRequiredFields(),
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
            'required_fields' => $this->queryBus->handle(new GetRequiredFieldsForAddress()),
        ];
    }
}
