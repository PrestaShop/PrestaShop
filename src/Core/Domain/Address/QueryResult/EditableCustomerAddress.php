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

namespace PrestaShop\PrestaShop\Core\Domain\Address\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;

/**
 * Transfers customer address data for editing
 */
class EditableCustomerAddress
{
    /**
     * @var AddressId
     */
    private $addressId;

    /**
     * @var CustomerId
     */
    private $customerId;

    /**
     * @var string
     */
    private $customerEmail;

    /**
     * @var string
     */
    private $addressAlias;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $city;

    /**
     * @var CountryId
     */
    private $countryId;

    /**
     * @var string|null
     */
    private $postCode;

    /**
     * @var string|null
     */
    private $dni;

    /**
     * @var string|null
     */
    private $company;

    /**
     * @var string|null
     */
    private $vatNumber;

    /**
     * @var string|null
     */
    private $address2;

    /**
     * @var StateId|null
     */
    private $stateId;

    /**
     * @var string|null
     */
    private $homePhone;

    /**
     * @var string|null
     */
    private $mobilePhone;

    /**
     * @var string|null
     */
    private $other;

    /**
     * @var string[]
     */
    private $requiredFields;

    /**
     * @param AddressId $addressId
     * @param CustomerId $customerId
     * @param string $customerEmail
     * @param string $addressAlias
     * @param string $firstName
     * @param string $lastName
     * @param string $address
     * @param string $city
     * @param CountryId $countryId
     * @param string $postCode
     * @param string $dni
     * @param string $company
     * @param string $vatNumber
     * @param string $address2
     * @param StateId $stateId
     * @param string $homePhone
     * @param string $mobilePhone
     * @param string $other
     * @param string[] $requiredFields
     */
    public function __construct(
        AddressId $addressId,
        CustomerId $customerId,
        string $customerEmail,
        string $addressAlias,
        string $firstName,
        string $lastName,
        string $address,
        string $city,
        CountryId $countryId,
        string $postCode,
        string $dni,
        string $company,
        string $vatNumber,
        string $address2,
        StateId $stateId,
        string $homePhone,
        string $mobilePhone,
        string $other,
        array $requiredFields
    ) {
        $this->addressId = $addressId;
        $this->customerId = $customerId;
        $this->customerEmail = $customerEmail;
        $this->addressAlias = $addressAlias;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->address = $address;
        $this->city = $city;
        $this->countryId = $countryId;
        $this->postCode = $postCode;
        $this->dni = $dni;
        $this->company = $company;
        $this->vatNumber = $vatNumber;
        $this->address2 = $address2;
        $this->stateId = $stateId;
        $this->homePhone = $homePhone;
        $this->mobilePhone = $mobilePhone;
        $this->other = $other;
        $this->requiredFields = $requiredFields;
    }

    /**
     * @return AddressId
     */
    public function getAddressId(): AddressId
    {
        return $this->addressId;
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId(): CustomerId
    {
        return $this->customerId;
    }

    /**
     * @return string
     */
    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }

    /**
     * @return string
     */
    public function getAddressAlias(): string
    {
        return $this->addressAlias;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return CountryId
     */
    public function getCountryId(): CountryId
    {
        return $this->countryId;
    }

    /**
     * @return string[]
     */
    public function getRequiredFields(): array
    {
        return $this->requiredFields;
    }

    /**
     * @return string|null
     */
    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    /**
     * @return string|null
     */
    public function getDni(): ?string
    {
        return $this->dni;
    }

    /**
     * @return string|null
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * @return string|null
     */
    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    /**
     * @return string|null
     */
    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    /**
     * @return StateId|null
     */
    public function getStateId(): ?StateId
    {
        return $this->stateId;
    }

    /**
     * @return string|null
     */
    public function getHomePhone(): ?string
    {
        return $this->homePhone;
    }

    /**
     * @return string|null
     */
    public function getMobilePhone(): ?string
    {
        return $this->mobilePhone;
    }

    /**
     * @return string|null
     */
    public function getOther(): ?string
    {
        return $this->other;
    }
}
