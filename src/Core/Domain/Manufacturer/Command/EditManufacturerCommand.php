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

namespace PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command;

use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;

/**
 * Edits manufacturer with provided data
 */
class EditManufacturerCommand
{
    /**
     * @var ManufacturerId
     */
    private $manufacturerId;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string[]|null
     */
    private $localizedShortDescriptions;

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
     * @param ManufacturerId $manufacturerId
     */
    public function __construct(ManufacturerId $manufacturerId)
    {
        $this->manufacturerId = $manufacturerId;
    }

    /**
     * @return ManufacturerId
     */
    public function getManufacturerId()
    {
        return $this->manufacturerId;
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
     * @return string[]|null
     */
    public function getLocalizedShortDescriptions()
    {
        return $this->localizedShortDescriptions;
    }

    /**
     * @param string[]|null $localizedShortDescriptions
     */
    public function setLocalizedShortDescriptions($localizedShortDescriptions)
    {
        $this->localizedShortDescriptions = $localizedShortDescriptions;
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
    public function getEnabled()
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
