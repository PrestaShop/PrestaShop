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

namespace PrestaShop\PrestaShop\Core\Domain\Address\Command;

use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;

/**
 * Adds new customer address
 */
class AddCustomerAddressCommand
{
    /**
     * @var CustomerId
     */
    private $customerId;

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
     * @var string|null
     */
    private $postCode;

    /**
     * @var CountryId
     */
    private $countryId;

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
     * @param int $customerId
     * @param string $addressAlias
     * @param string $firstName
     * @param string $lastName
     * @param string $address
     * @param string $city
     * @param int $countryId
     *
     * @throws CountryConstraintException
     * @throws CustomerException
     */
    public function __construct(
        int $customerId,
        string $addressAlias,
        string $firstName,
        string $lastName,
        string $address,
        string $city,
        int $countryId
    ) {
        $this->customerId = new CustomerId($customerId);
        $this->addressAlias = $addressAlias;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->address = $address;
        $this->city = $city;
        $this->countryId = new CountryId($countryId);
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
     * @return string|null
     */
    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    /**
     * @param string $postCode
     *
     * @return AddCustomerAddressCommand
     */
    public function setPostCode(string $postCode): AddCustomerAddressCommand
    {
        $this->postCode = $postCode;

        return $this;
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
    public function getIdNumber(): ?string
    {
        return $this->idNumber;
    }

    /**
     * @param string $idNumber
     *
     * @return AddCustomerAddressCommand
     */
    public function setIdNumber(string $idNumber): AddCustomerAddressCommand
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
     * @return AddCustomerAddressCommand
     */
    public function setCompany(string $company): AddCustomerAddressCommand
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
     * @return AddCustomerAddressCommand
     */
    public function setVatNumber(string $vatNumber): AddCustomerAddressCommand
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
     * @return AddCustomerAddressCommand
     */
    public function setAddress2(string $address2): AddCustomerAddressCommand
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
     * @param int $stateId
     *
     * @return AddCustomerAddressCommand
     *
     * @throws StateConstraintException
     */
    public function setStateId(int $stateId): AddCustomerAddressCommand
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
     * @return AddCustomerAddressCommand
     */
    public function setHomePhone(string $homePhone): AddCustomerAddressCommand
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
     * @return AddCustomerAddressCommand
     */
    public function setMobilePhone(string $mobilePhone): AddCustomerAddressCommand
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
     * @return AddCustomerAddressCommand
     */
    public function setOther(string $other): AddCustomerAddressCommand
    {
        $this->other = $other;

        return $this;
    }
}
