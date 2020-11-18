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
