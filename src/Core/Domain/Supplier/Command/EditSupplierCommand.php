<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace PrestaShop\PrestaShop\Core\Domain\Supplier\Command;

use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;

/**
 * Edits supplier with provided data
 */
class EditSupplierCommand
{
    /**
     * @var SupplierId
     */
    private $supplierId;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string[]|null
     */
    private $localizedDescriptions;

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
    private $phone;

    /**
     * @var string|null
     */
    private $mobilePhone;

    /**
     * @var array|null
     */
    private $logoImage;

    /**
     * @var string[]|null
     */
    private $localizedMetaTitles;

    /**
     * @var string[]|null
     */
    private $localizedMetaDescriptions;

    /**
     * @var string[]|null
     */
    private $localizedMetaKeywords;

    /**
     * @var bool|null
     */
    private $enabled;

    /**
     * @var array|null
     */
    private $associatedShops;

    /**
     * @param int $supplierId
     *
     * @throws SupplierException
     */
    public function __construct($supplierId)
    {
        $this->supplierId = new SupplierId($supplierId);
    }

    /**
     * @return SupplierId
     */
    public function getSupplierId()
    {
        return $this->supplierId;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string[]
     */
    public function getLocalizedDescriptions()
    {
        return $this->localizedDescriptions;
    }

    /**
     * @param string[]|null $localizedDescriptions
     */
    public function setLocalizedDescriptions($localizedDescriptions)
    {
        $this->localizedDescriptions = $localizedDescriptions;
    }

    /**
     * @return string|null
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return string|null
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string|null
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * @param string|null $address2
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
    }

    /**
     * @return int|null
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * @param int|null $countryId
     */
    public function setCountryId($countryId)
    {
        $this->countryId = $countryId;
    }

    /**
     * @return string|null
     */
    public function getPostCode()
    {
        return $this->postCode;
    }

    /**
     * @param string|null $postCode
     */
    public function setPostCode($postCode)
    {
        $this->postCode = $postCode;
    }

    /**
     * @return int|null
     */
    public function getStateId()
    {
        return $this->stateId;
    }

    /**
     * @param int|null $stateId
     */
    public function setStateId($stateId)
    {
        $this->stateId = $stateId;
    }

    /**
     * @return string|null
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string|null
     */
    public function getMobilePhone()
    {
        return $this->mobilePhone;
    }

    /**
     * @param string|null $mobilePhone
     */
    public function setMobilePhone($mobilePhone)
    {
        $this->mobilePhone = $mobilePhone;
    }

    /**
     * @return array|null
     */
    public function getLogoImage()
    {
        return $this->logoImage;
    }

    /**
     * @param array|null $logoImage
     */
    public function setLogoImage($logoImage)
    {
        $this->logoImage = $logoImage;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedMetaTitles()
    {
        return $this->localizedMetaTitles;
    }

    /**
     * @param string[]|null $localizedMetaTitles
     */
    public function setLocalizedMetaTitles($localizedMetaTitles)
    {
        $this->localizedMetaTitles = $localizedMetaTitles;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedMetaDescriptions()
    {
        return $this->localizedMetaDescriptions;
    }

    /**
     * @param string[]|null $localizedMetaDescriptions
     */
    public function setLocalizedMetaDescriptions($localizedMetaDescriptions)
    {
        $this->localizedMetaDescriptions = $localizedMetaDescriptions;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedMetaKeywords()
    {
        return $this->localizedMetaKeywords;
    }

    /**
     * @param string[]|null $localizedMetaKeywords
     */
    public function setLocalizedMetaKeywords($localizedMetaKeywords)
    {
        $this->localizedMetaKeywords = $localizedMetaKeywords;
    }

    /**
     * @return bool|null
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool|null $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return array|null
     */
    public function getAssociatedShops()
    {
        return $this->associatedShops;
    }

    /**
     * @param array|null $associatedShops
     */
    public function setAssociatedShops($associatedShops)
    {
        $this->associatedShops = $associatedShops;
    }
}
