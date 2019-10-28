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
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;

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
     * @var int
     */
    private $manufacturerId;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $firstName;

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
    private $address2;

    /**
     * @var int
     */
    private $countryId;

    /**
     * @var string
     */
    private $postCode;

    /**
     * @var int
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
     * @var string
     */
    private $dni;

    /**
     * @param AddressId $addressId
     * @param string $lastName
     * @param string $firstName
     * @param string $address
     * @param string $city
     * @param int $manufacturerId
     * @param int $countryId
     * @param string $address2
     * @param string $postCode
     * @param int $stateId
     * @param string $homePhone
     * @param string $mobilePhone
     * @param string $other
     * @param string $dni
     */
    public function __construct(
        AddressId $addressId,
        $lastName,
        $firstName,
        $address,
        $city,
        $manufacturerId,
        $countryId,
        $address2,
        $postCode,
        $stateId,
        $homePhone,
        $mobilePhone,
        $other,
        $dni
    ) {
        $this->addressId = $addressId;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->address = $address;
        $this->city = $city;
        $this->manufacturerId = $manufacturerId;
        $this->countryId = $countryId;
        $this->address2 = $address2;
        $this->postCode = $postCode;
        $this->stateId = $stateId;
        $this->homePhone = $homePhone;
        $this->mobilePhone = $mobilePhone;
        $this->other = $other;
        $this->dni = $dni;
    }

    /**
     * @return AddressId
     */
    public function getAddressId()
    {
        return $this->addressId;
    }

    /**
     * @return int
     */
    public function getManufacturerId()
    {
        return $this->manufacturerId;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * @return int
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * @return string
     */
    public function getPostCode()
    {
        return $this->postCode;
    }

    /**
     * @return int
     */
    public function getStateId()
    {
        return $this->stateId;
    }

    /**
     * @return string
     */
    public function getHomePhone()
    {
        return $this->homePhone;
    }

    /**
     * @return string
     */
    public function getMobilePhone()
    {
        return $this->mobilePhone;
    }

    /**
     * @return string
     */
    public function getOther()
    {
        return $this->other;
    }

    /**
     * @return string
     */
    public function getDni()
    {
        return $this->dni;
    }
}
