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

namespace PrestaShop\PrestaShop\Core\Domain\Supplier\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;

/**
 * Transfers supplier data for editing
 */
class EditableSupplier
{
    /**
     * @var SupplierId
     */
    private $supplierId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string[]
     */
    private $localizedDescriptions;

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
    private $phone;

    /**
     * @var string
     */
    private $mobilePhone;

    /**
     * @var array
     */
    private $logoImage;

    /**
     * @var string[]
     */
    private $localizedMetaTitles;

    /**
     * @var string[]
     */
    private $localizedMetaDescriptions;

    /**
     * @var string[]
     */
    private $localizedMetaKeywords;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var array
     */
    private $associatedShops;

    /**
     * @param SupplierId $supplierId
     * @param string $name
     * @param string[] $localizedDescriptions
     * @param string $address
     * @param string $city
     * @param string $address2
     * @param int $countryId
     * @param string $postCode
     * @param int $stateId
     * @param string $phone
     * @param string $mobilePhone
     * @param string[] $localizedMetaTitles
     * @param string[] $localizedMetaDescriptions
     * @param string[] $localizedMetaKeywords
     * @param bool $enabled
     * @param array $associatedShops
     * @param array|null $logoImage
     */
    public function __construct(
        SupplierId $supplierId,
        $name,
        array $localizedDescriptions,
        $address,
        $city,
        $address2,
        $countryId,
        $postCode,
        $stateId,
        $phone,
        $mobilePhone,
        array $localizedMetaTitles,
        array $localizedMetaDescriptions,
        array $localizedMetaKeywords,
        $enabled,
        array $associatedShops,
        array $logoImage = null
    ) {
        $this->supplierId = $supplierId;
        $this->name = $name;
        $this->localizedDescriptions = $localizedDescriptions;
        $this->address = $address;
        $this->city = $city;
        $this->address2 = $address2;
        $this->countryId = $countryId;
        $this->postCode = $postCode;
        $this->stateId = $stateId;
        $this->phone = $phone;
        $this->mobilePhone = $mobilePhone;
        $this->logoImage = $logoImage;
        $this->localizedMetaTitles = $localizedMetaTitles;
        $this->localizedMetaDescriptions = $localizedMetaDescriptions;
        $this->localizedMetaKeywords = $localizedMetaKeywords;
        $this->enabled = $enabled;
        $this->associatedShops = $associatedShops;
    }

    /**
     * @return SupplierId
     */
    public function getSupplierId()
    {
        return $this->supplierId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getLocalizedDescriptions()
    {
        return $this->localizedDescriptions;
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
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getMobilePhone()
    {
        return $this->mobilePhone;
    }

    /**
     * @return array
     */
    public function getLogoImage()
    {
        return $this->logoImage;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaTitles()
    {
        return $this->localizedMetaTitles;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaDescriptions()
    {
        return $this->localizedMetaDescriptions;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaKeywords()
    {
        return $this->localizedMetaKeywords;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return array
     */
    public function getAssociatedShops()
    {
        return $this->associatedShops;
    }
}
