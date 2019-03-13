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

/**
 * Transfers manufacturer address data for editing
 */
class EditableManufacturerAddress
{
    /**
     * @var AddressId
     */
    private $addressId;

    /**
     * @var int|null
     */
    private $manufacturerId;

    /**
     * @var string|null
     */
    private $lastName;

    /**
     * @var string|null
     */
    private $firstName;

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
    private $address2;

    /**
     * @var int|null
     */
    private $countryId;

    /**
     * @var string|null
     */
    private $postCode;

    /**
     * @var int|null
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
     * @param int $manufacturerId
     * @param string $lastName
     * @param string $firstName
     * @param string $address
     * @param string $city
     * @param int $countryId
     * @param string $address2
     * @param string $postCode
     * @param int $stateId
     * @param string $homePhone
     * @param string $mobilePhone
     * @param string $other
     */
    public function __construct(
        AddressId $addressId,
        $manufacturerId,
        $lastName,
        $firstName,
        $address,
        $city,
        $countryId,
        $address2,
        $postCode,
        $stateId,
        $homePhone,
        $mobilePhone,
        $other
    ) {
        $this->addressId = $addressId;
        $this->manufacturerId = $manufacturerId;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->address = $address;
        $this->city = $city;
        $this->countryId = $countryId;
        $this->address2 = $address2;
        $this->postCode = $postCode;
        $this->stateId = $stateId;
        $this->homePhone = $homePhone;
        $this->mobilePhone = $mobilePhone;
        $this->other = $other;
    }

    /**
     * @return AddressId
     */
    public function getAddressId()
    {
        return $this->addressId;
    }

    /**
     * @return int|null
     */
    public function getManufacturerId()
    {
        return $this->manufacturerId;
    }

    /**
     * @return string|null
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string|null
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string|null
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return string|null
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string|null
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * @return int|null
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * @return string|null
     */
    public function getPostCode()
    {
        return $this->postCode;
    }

    /**
     * @return int|null
     */
    public function getStateId()
    {
        return $this->stateId;
    }

    /**
     * @return string|null
     */
    public function getHomePhone()
    {
        return $this->homePhone;
    }

    /**
     * @return string|null
     */
    public function getMobilePhone()
    {
        return $this->mobilePhone;
    }

    /**
     * @return string|null
     */
    public function getOther()
    {
        return $this->other;
    }
}
