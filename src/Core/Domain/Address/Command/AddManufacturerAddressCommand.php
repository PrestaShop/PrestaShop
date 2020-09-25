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

use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;

/**
 * Adds new address
 */
class AddManufacturerAddressCommand
{
    /**
     * @var int|null
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
     * @var string|null
     */
    private $dni;

    /**
     * @param string $lastName
     * @param string $firstName
     * @param string $address
     * @param int|null $countryId
     * @param string $city
     * @param int
     * @param string|null $address2
     * @param string|null $postCode
     * @param int|null $stateId
     * @param string|null $homePhone
     * @param string $mobilePhone
     * @param string|null $other
     * @param string|null $dni
     *
     * @throws AddressConstraintException
     */
    public function __construct(
        $lastName,
        $firstName,
        $address,
        $countryId,
        $city,
        $manufacturerId = null,
        $address2 = null,
        $postCode = null,
        $stateId = null,
        $homePhone = null,
        $mobilePhone = null,
        $other = null,
        $dni = null
    ) {
        $this->assertIsNullOrNonNegativeInt($manufacturerId);
        $this->manufacturerId = $manufacturerId;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->address = $address;
        $this->countryId = $countryId;
        $this->city = $city;
        $this->address2 = $address2;
        $this->postCode = $postCode;
        $this->stateId = $stateId;
        $this->homePhone = $homePhone;
        $this->mobilePhone = $mobilePhone;
        $this->other = $other;
        $this->dni = $dni;
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
     * @return string|null
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

    /**
     * @return string|null
     */
    public function getDni()
    {
        return $this->dni;
    }

    /**
     * @param $value
     *
     * @throws AddressConstraintException
     */
    private function assertIsNullOrNonNegativeInt($value)
    {
        if (null === $value || is_int($value) || 0 <= $value) {
            return;
        }

        throw new AddressConstraintException(sprintf('Invalid manufacturer id "%s" provided for address.', var_export($value, true)), AddressConstraintException::INVALID_MANUFACTURER_ID);
    }
}
