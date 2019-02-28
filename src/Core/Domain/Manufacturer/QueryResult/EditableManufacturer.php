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

namespace PrestaShop\PrestaShop\Core\Domain\Manufacturer\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;

/**
 * Transfers manufacturer data for editing
 */
class EditableManufacturer
{
    /**
     * @var ManufacturerId
     */
    private $manufacturerId;

    /**
     * @var string
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
     * @var bool
     */
    private $enabled;

    /**
     * @var array
     */
    private $associatedShops;

    /**
     * @param ManufacturerId $manufacturerId
     * @param string $name
     * @param bool $enabled
     * @param array|null $localizedShortDescriptions
     * @param array|null $localizedDescriptions
     * @param array|null $logoImage
     * @param array|null $localizedMetaTitles
     * @param array|null $localizedMetaDescriptions
     * @param array|null $localizedMetaKeywords
     * @param array $associatedShops
     */
    public function __construct(
        ManufacturerId $manufacturerId,
        $name,
        $enabled = false,
        array $localizedShortDescriptions = null,
        array $localizedDescriptions = null,
        array $localizedMetaTitles = null,
        array $localizedMetaDescriptions = null,
        array $localizedMetaKeywords = null,
        array $logoImage = null,
        array $associatedShops = []
    ) {
        $this->manufacturerId = $manufacturerId;
        $this->name = $name;
        $this->localizedShortDescriptions = $localizedShortDescriptions;
        $this->localizedDescriptions = $localizedDescriptions;
        $this->logoImage = $logoImage;
        $this->localizedMetaTitles = $localizedMetaTitles;
        $this->localizedMetaDescriptions = $localizedMetaDescriptions;
        $this->localizedMetaKeywords = $localizedMetaKeywords;
        $this->enabled = $enabled;
        $this->associatedShops = $associatedShops;
    }

    /**
     * @return ManufacturerId
     */
    public function getManufacturerId()
    {
        return $this->manufacturerId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * @return array|null
     */
    public function getLogoImage()
    {
        return $this->logoImage;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedMetaTitles()
    {
        return $this->localizedMetaTitles;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedMetaDescriptions()
    {
        return $this->localizedMetaDescriptions;
    }

    /**
     * @return string[]|null
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
