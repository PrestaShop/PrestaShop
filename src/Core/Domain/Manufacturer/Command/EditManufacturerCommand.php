<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
     * @var string[]|null
     */
    private $localizedMetaTitles;

    /**
     * @var string[]|null
     */
    private $localizedMetaDescriptions;

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
     * @param array $associatedShops
     *
     * @return self
     */
    public function setAssociatedShops($associatedShops)
    {
        $this->associatedShops = $associatedShops;

        return $this;
    }
}
