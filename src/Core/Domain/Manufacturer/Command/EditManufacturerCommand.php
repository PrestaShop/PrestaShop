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
     * @var string[]|null
     */
    private $localizedDescriptions;

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
     * @param int $manufacturerId
     */
    public function __construct($manufacturerId)
    {
        $this->manufacturerId = new ManufacturerId($manufacturerId);
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
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedShortDescriptions()
    {
        return $this->localizedShortDescriptions;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedDescriptions()
    {
        return $this->localizedDescriptions;
    }

    /**
     * @param string[]|null $localizedDescriptions
     *
     * @return self
     */
    public function setLocalizedDescriptions($localizedDescriptions)
    {
        $this->localizedDescriptions = $localizedDescriptions;

        return $this;
    }

    /**
     * @param string[]|null $localizedShortDescriptions
     *
     * @return self
     */
    public function setLocalizedShortDescriptions($localizedShortDescriptions)
    {
        $this->localizedShortDescriptions = $localizedShortDescriptions;

        return $this;
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
     *
     * @return self
     */
    public function setLogoImage($logoImage)
    {
        $this->logoImage = $logoImage;

        return $this;
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
     *
     * @return self
     */
    public function setLocalizedMetaTitles($localizedMetaTitles)
    {
        $this->localizedMetaTitles = $localizedMetaTitles;

        return $this;
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
     *
     * @return self
     */
    public function setLocalizedMetaDescriptions($localizedMetaDescriptions)
    {
        $this->localizedMetaDescriptions = $localizedMetaDescriptions;

        return $this;
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
     *
     * @return self
     */
    public function setLocalizedMetaKeywords($localizedMetaKeywords)
    {
        $this->localizedMetaKeywords = $localizedMetaKeywords;

        return $this;
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
     *
     * @return self
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return array
     */
    public function getAssociatedShops()
    {
        return $this->associatedShops;
    }

    /**
     * @param $associatedShops
     *
     * @return self
     */
    public function setAssociatedShops($associatedShops)
    {
        $this->associatedShops = $associatedShops;

        return $this;
    }
}
