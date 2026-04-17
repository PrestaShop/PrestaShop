<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\CLDR;

/**
 * CLDR Locale data layer classes interface.
 *
 * Describes the behavior of CldrLocaleDataLayer classes
 */
interface LocaleDataLayerInterface
{
    /**
     * Read CLDR locale data by locale code.
     *
     * @param string $localeCode The locale code (simplified IETF tag syntax)
     *                           Combination of ISO 639-1 (2-letters language code) and ISO 3166-2 (2-letters region code)
     *                           eg: fr-FR, en-US
     *
     * @return LocaleData|null The searched locale's CLDR data
     */
    public function read($localeCode);

    /**
     * Write a locale's CLDR data object into the data source.
     *
     * @param string $localeCode The locale code (simplified IETF tag syntax)
     *                           Combination of ISO 639-1 (2-letters language code) and ISO 3166-2 (2-letters region code)
     *                           eg: fr-FR, en-US
     * @param LocaleData $localeData The locale's CLDR data to write
     *
     * @return LocaleData
     *                    The locale's CLDR data to be written by the upper data layer
     */
    public function write($localeCode, $localeData);

    /**
     * Set the lower layer.
     * When reading data, if nothing is found then it will try to read in the lower data layer
     * When writing data, the data will also be written in the lower data layer.
     *
     * @param LocaleDataLayerInterface $lowerLayer The lower data layer
     *
     * @return self
     */
    public function setLowerLayer(LocaleDataLayerInterface $lowerLayer);
}
