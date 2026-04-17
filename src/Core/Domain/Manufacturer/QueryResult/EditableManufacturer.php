<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
     * @var string[]
     */
    private $localizedShortDescriptions;

    /**
     * @var string[]
     */
    private $localizedDescriptions;

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
     * @param array $localizedShortDescriptions
     * @param array $localizedDescriptions
     * @param array $localizedMetaTitles
     * @param array $localizedMetaDescriptions
     * @param array|null $logoImage
     * @param array $associatedShops
     */
    public function __construct(
        ManufacturerId $manufacturerId,
        $name,
        $enabled,
        array $localizedShortDescriptions,
        array $localizedDescriptions,
        array $localizedMetaTitles,
        array $localizedMetaDescriptions,
        $logoImage,
        array $associatedShops
    ) {
        $this->manufacturerId = $manufacturerId;
        $this->name = $name;
        $this->localizedShortDescriptions = $localizedShortDescriptions;
        $this->localizedDescriptions = $localizedDescriptions;
        $this->logoImage = $logoImage;
        $this->localizedMetaTitles = $localizedMetaTitles;
        $this->localizedMetaDescriptions = $localizedMetaDescriptions;
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
     * @return string[]
     */
    public function getLocalizedShortDescriptions()
    {
        return $this->localizedShortDescriptions;
    }

    /**
     * @return string[]
     */
    public function getLocalizedDescriptions()
    {
        return $this->localizedDescriptions;
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
