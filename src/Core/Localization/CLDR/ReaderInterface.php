<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\CLDR;

interface ReaderInterface
{
    /**
     * Read locale data by locale code.
     *
     * @param string $localeCode The locale code (simplified IETF tag syntax)
     *                           Combination of ISO 639-1 (2-letters language code) and ISO 3166-2 (2-letters region code)
     *                           eg: fr-FR, en-US
     *
     * @return LocaleData A LocaleData object
     */
    public function readLocaleData($localeCode);
}
