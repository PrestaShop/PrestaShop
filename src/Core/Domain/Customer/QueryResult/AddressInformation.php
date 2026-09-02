<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult;

/**
 * Class AddressInformation.
 */
class AddressInformation
{
    /**
     * @var string
     */
    private $company;

    /**
     * @var string
     */
    private $fullName;

    /**
     * @var string
     */
    private $fullAddress;

    /**
     * @var string
     */
    private $countryName;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $phoneMobile;

    /**
     * @var int
     */
    private $addressId;

    /**
     * @param int $addressId
     * @param string $company
     * @param string $fullName
     * @param string $fullAddress
     * @param string $countryName
     * @param string $phone
     * @param string $phoneMobile
     */
    public function __construct($addressId, $company, $fullName, $fullAddress, $countryName, $phone, $phoneMobile)
    {
        $this->addressId = $addressId;
        $this->company = $company;
        $this->fullName = $fullName;
        $this->fullAddress = $fullAddress;
        $this->countryName = $countryName;
        $this->phone = $phone;
        $this->phoneMobile = $phoneMobile;
    }

    /**
     * @return int
     */
    public function getAddressId()
    {
        return $this->addressId;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @return string
     */
    public function getFullAddress()
    {
        return $this->fullAddress;
    }

    /**
     * @return string
     */
    public function getCountryName()
    {
        return $this->countryName;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getPhoneMobile()
    {
        return $this->phoneMobile;
    }
}
