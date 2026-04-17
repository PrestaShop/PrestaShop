<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command;

/**
 * Creates manufacturer with provided data
 */
class AddManufacturerCommand
{
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
    private $shopAssociation;

    /**
     * @param string $name
     * @param bool $enabled
     * @param string[] $localizedShortDescriptions
     * @param string[] $localizedDescriptions
     * @param string[] $localizedMetaTitles
     * @param string[] $localizedMetaDescriptions
     * @param array $shopAssociation
     */
    public function __construct(
        string $name,
        bool $enabled,
        array $localizedShortDescriptions,
        array $localizedDescriptions,
        array $localizedMetaTitles,
        array $localizedMetaDescriptions,
        array $shopAssociation
    ) {
        $this->name = $name;
        $this->enabled = $enabled;
        $this->localizedShortDescriptions = $localizedShortDescriptions;
        $this->localizedDescriptions = $localizedDescriptions;
        $this->localizedMetaTitles = $localizedMetaTitles;
        $this->localizedMetaDescriptions = $localizedMetaDescriptions;
        $this->shopAssociation = $shopAssociation;
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
    public function getShopAssociation()
    {
        return $this->shopAssociation;
    }
}
