<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
     * @param int $manufacturerId
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
     * @param mixed $value
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
