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

namespace PrestaShop\PrestaShop\Core\Domain\Address\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;
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
    private $idNumber;

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
     * @param AddressId $addressId
     * @param CustomerId $customerId
     * @param string $customerEmail
     * @param string $addressAlias
     * @param string $firstName
     * @param string $lastName
     * @param string $address
     * @param string $city
     * @param CountryId $countryId
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
        CountryId $countryId
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
     * @return string|null
     */
    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    /**
     * @param string $postCode
     *
     * @return EditableCustomerAddress
     */
    public function setPostCode(string $postCode): EditableCustomerAddress
    {
        $this->postCode = $postCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIdNumber(): ?string
    {
        return $this->idNumber;
    }

    /**
     * @param string $idNumber
     *
     * @return EditableCustomerAddress
     */
    public function setIdNumber(string $idNumber): EditableCustomerAddress
    {
        $this->idNumber = $idNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * @param string $company
     *
     * @return EditableCustomerAddress
     */
    public function setCompany(string $company): EditableCustomerAddress
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    /**
     * @param string $vatNumber
     *
     * @return EditableCustomerAddress
     */
    public function setVatNumber(string $vatNumber): EditableCustomerAddress
    {
        $this->vatNumber = $vatNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    /**
     * @param string $address2
     *
     * @return EditableCustomerAddress
     */
    public function setAddress2(string $address2): EditableCustomerAddress
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * @return StateId|null
     */
    public function getStateId(): ?StateId
    {
        return $this->stateId;
    }

    /**
     * @param string $stateId
     *
     * @return EditableCustomerAddress
     *
     * @throws StateConstraintException
     */
    public function setStateId(string $stateId): EditableCustomerAddress
    {
        $this->stateId = new StateId($stateId);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHomePhone(): ?string
    {
        return $this->homePhone;
    }

    /**
     * @param string $homePhone
     *
     * @return EditableCustomerAddress
     */
    public function setHomePhone(string $homePhone): EditableCustomerAddress
    {
        $this->homePhone = $homePhone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMobilePhone(): ?string
    {
        return $this->mobilePhone;
    }

    /**
     * @param string $mobilePhone
     *
     * @return EditableCustomerAddress
     */
    public function setMobilePhone(string $mobilePhone): EditableCustomerAddress
    {
        $this->mobilePhone = $mobilePhone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOther(): ?string
    {
        return $this->other;
    }

    /**
     * @param string $other
     *
     * @return EditableCustomerAddress
     */
    public function setOther(string $other): EditableCustomerAddress
    {
        $this->other = $other;

        return $this;
    }
}
