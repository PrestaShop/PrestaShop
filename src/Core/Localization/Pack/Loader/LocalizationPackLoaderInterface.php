<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\Pack\Loader;

use SimpleXMLElement;

/**
 * Interface LocalizationPackLoaderInterface defines contract for localization pack loaders.
 */
interface LocalizationPackLoaderInterface
{
    /**
     * Get localization packs list.
     *
     * @return SimpleXMLElement|null SimpleXMLElement with localization packs data or null if packs are not available
     */
    public function getLocalizationPackList();

    /**
     * Get single localization pack data.
     *
     * @param string $countryIso Country ISO Alpha-2 code
     *
     * @return SimpleXMLElement|null
     */
    public function getLocalizationPack($countryIso);
}
