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

use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;

/**
 * Command responsible for holding edits customer address data
 */
class EditCustomerAddressCommand
{
    /**
     * @var AddressId
     */
    private $addressId;

    /**
     * @var string|null
     */
    private $addressAlias;

    /**
     * @var string|null
     */
    private $firstName;

    /**
     * @var string|null
     */
    private $lastName;

    /**
     * @var string|null
     */
    private $address;

    /**
     * @var string|null
     */
    private $city;

    /**
     * @var string|null
     */
    private $postCode;

    /**
     * @var CountryId|null
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
     * @param int $addressId
     *
     * @throws AddressConstraintException
     */
    public function __construct(int $addressId)
    {
        $this->addressId = new AddressId($addressId);
    }

    /**
     * @return AddressId
     */
    public function getAddressId(): AddressId
    {
        return $this->addressId;
    }

    /**
     * @return string|null
     */
    public function getAddressAlias(): ?string
    {
        return $this->addressAlias;
    }

    /**
     * @param string $addressAlias
     *
     * @return EditCustomerAddressCommand
     */
    public function setAddressAlias(string $addressAlias): EditCustomerAddressCommand
    {
        $this->addressAlias = $addressAlias;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return EditCustomerAddressCommand
     */
    public function setFirstName(string $firstName): EditCustomerAddressCommand
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return EditCustomerAddressCommand
     */
    public function setLastName(string $lastName): EditCustomerAddressCommand
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string $address
     *
     * @return EditCustomerAddressCommand
     */
    public function setAddress(string $address): EditCustomerAddressCommand
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string $city
     *
     * @return EditCustomerAddressCommand
     */
    public function setCity(string $city): EditCustomerAddressCommand
    {
        $this->city = $city;

        return $this;
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
     * @return EditCustomerAddressCommand
     */
    public function setPostCode(string $postCode): EditCustomerAddressCommand
    {
        $this->postCode = $postCode;

        return $this;
    }

    /**
     * @return CountryId|null
     */
    public function getCountryId(): ?CountryId
    {
        return $this->countryId;
    }

    /**
     * @param int|null $countryId
     *
     * @return EditCustomerAddressCommand
     *
     * @throws CountryConstraintException
     */
    public function setCountryId(int $countryId): EditCustomerAddressCommand
    {
        $this->countryId = new CountryId($countryId);

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
     * @return EditCustomerAddressCommand
     */
    public function setIdNumber(string $idNumber): EditCustomerAddressCommand
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
     * @return EditCustomerAddressCommand
     */
    public function setCompany(string $company): EditCustomerAddressCommand
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
     * @return EditCustomerAddressCommand
     */
    public function setVatNumber(string $vatNumber): EditCustomerAddressCommand
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
     * @return EditCustomerAddressCommand
     */
    public function setAddress2(string $address2): EditCustomerAddressCommand
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
     * @return EditCustomerAddressCommand
     *
     * @throws StateConstraintException
     */
    public function setStateId(int $stateId): EditCustomerAddressCommand
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
     * @param string|null $homePhone
     *
     * @return EditCustomerAddressCommand
     */
    public function setHomePhone(string $homePhone): EditCustomerAddressCommand
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
     * @return EditCustomerAddressCommand
     */
    public function setMobilePhone(string $mobilePhone): EditCustomerAddressCommand
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
     * @return EditCustomerAddressCommand
     */
    public function setOther(string $other): EditCustomerAddressCommand
    {
        $this->other = $other;

        return $this;
    }
}
