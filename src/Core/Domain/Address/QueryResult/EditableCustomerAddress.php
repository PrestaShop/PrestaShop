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
     * @var string
     */
    private $postCode;

    /**
     * @var CountryId
     */
    private $countryId;

    /**
     * @var string
     */
    private $idNumber;

    /**
     * @var string
     */
    private $company;

    /**
     * @var string
     */
    private $vatNumber;

    /**
     * @var string
     */
    private $address2;

    /**
     * @var StateId
     */
    private $stateId;

    /**
     * @var string
     */
    private $homePhone;

    /**
     * @var string
     */
    private $mobilePhone;

    /**
     * @var string
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
     * @param string $postCode
     * @param CountryId $countryId
     * @param string $idNumber
     * @param string $company
     * @param string $vatNumber
     * @param string $address2
     * @param StateId $stateId
     * @param string $homePhone
     * @param string $mobilePhone
     * @param string $other
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
        string $postCode,
        CountryId $countryId,
        string $idNumber,
        string $company,
        string $vatNumber,
        string $address2,
        StateId $stateId,
        string $homePhone,
        string $mobilePhone,
        string $other
    ) {
        $this->addressId = $addressId;
        $this->customerId = $customerId;
        $this->customerEmail = $customerEmail;
        $this->addressAlias = $addressAlias;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->address = $address;
        $this->city = $city;
        $this->postCode = $postCode;
        $this->countryId = $countryId;
        $this->idNumber = $idNumber;
        $this->company = $company;
        $this->vatNumber = $vatNumber;
        $this->address2 = $address2;
        $this->stateId = $stateId;
        $this->homePhone = $homePhone;
        $this->mobilePhone = $mobilePhone;
        $this->other = $other;
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
     * @return string
     */
    public function getPostCode(): string
    {
        return $this->postCode;
    }

    /**
     * @return CountryId
     */
    public function getCountryId(): CountryId
    {
        return $this->countryId;
    }

    /**
     * @return string
     */
    public function getIdNumber(): string
    {
        return $this->idNumber;
    }

    /**
     * @return string
     */
    public function getCompany(): string
    {
        return $this->company;
    }

    /**
     * @return string
     */
    public function getVatNumber(): string
    {
        return $this->vatNumber;
    }

    /**
     * @return string
     */
    public function getAddress2(): string
    {
        return $this->address2;
    }

    /**
     * @return StateId
     */
    public function getStateId(): StateId
    {
        return $this->stateId;
    }

    /**
     * @return string
     */
    public function getHomePhone(): string
    {
        return $this->homePhone;
    }

    /**
     * @return string
     */
    public function getMobilePhone(): string
    {
        return $this->mobilePhone;
    }

    /**
     * @return string
     */
    public function getOther(): string
    {
        return $this->other;
    }
}
