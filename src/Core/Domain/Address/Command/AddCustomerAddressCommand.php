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

namespace PrestaShop\PrestaShop\Core\Domain\Address\Command;

use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\NoStateId;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateIdInterface;

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
     * @var StateIdInterface
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
     * @param string $postcode
     * @param string $dni
     * @param string $company
     * @param string $vat_number
     * @param string $address2
     * @param int $id_state
     * @param string $phone
     * @param string $phone_mobile
     * @param string $other
     *
     * @throws CountryConstraintException
     * @throws StateConstraintException
     */
    public function __construct(
        int $customerId,
        string $addressAlias,
        string $firstName,
        string $lastName,
        string $address,
        string $city,
        int $countryId,
        string $postcode,
        string $dni = null,
        string $company = null,
        string $vat_number = null,
        string $address2 = null,
        int $id_state = 0,
        string $phone = null,
        ?string $phone_mobile = null,
        string $other = null
    ) {
        $this->customerId = new CustomerId($customerId);
        $this->addressAlias = $addressAlias;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->address = $address;
        $this->city = $city;
        $this->countryId = new CountryId($countryId);
        $this->postCode = $postcode;
        $this->dni = $dni;
        $this->company = $company;
        $this->vatNumber = $vat_number;
        $this->address2 = $address2;
        $this->homePhone = $phone;
        $this->mobilePhone = $phone_mobile;
        $this->other = $other;
        $this->stateId = $id_state === NoStateId::NO_STATE_ID_VALUE ? new NoStateId() : new StateId($id_state);
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
     * @return CountryId
     */
    public function getCountryId(): CountryId
    {
        return $this->countryId;
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
     * @return StateIdInterface
     */
    public function getStateId(): StateIdInterface
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
