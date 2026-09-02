<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\CLDR\DataLayer;

use PrestaShop\PrestaShop\Core\Data\Layer\AbstractDataLayer;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleData;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleDataLayerInterface as CldrLocaleDataLayerInterface;
use PrestaShop\PrestaShop\Core\Localization\CLDR\ReaderInterface;

/**
 * Locale reference data layer.
 *
 * Provides reference (CLDR) data for locale, number specification, currencies...
 * Data comes from CLDR official data files, and is read only.
 */
class LocaleReference extends AbstractDataLayer implements CldrLocaleDataLayerInterface
{
    /**
     * CLDR files reader.
     *
     * Provides LocaleData objects
     *
     * @var ReaderInterface
     */
    protected $reader;

    public function __construct(ReaderInterface $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function setLowerLayer(CldrLocaleDataLayerInterface $lowerLayer)
    {
        $this->lowerDataLayer = $lowerLayer;

        return $this;
    }

    /**
     * Actually read a CLDR LocaleData object into the current layer.
     *
     * Data is read from official CLDR file (via the CLDR files reader)
     *
     * @param string $localeCode
     *                           The CLDR LocaleData object identifier
     *
     * @return LocaleData|null
     *                         The wanted CLDR LocaleData object (null if not found)
     */
    protected function doRead($localeCode)
    {
        return $this->reader->readLocaleData($localeCode);
    }

    /**
     * CLDR files are read only. Nothing can be written there.
     *
     * @param string $localeCode
     *                           The CLDR LocaleData object identifier
     * @param LocaleData $data
     *                         The CLDR LocaleData object to be written
     */
    protected function doWrite($localeCode, $data)
    {
        // Nothing.
    }
}
