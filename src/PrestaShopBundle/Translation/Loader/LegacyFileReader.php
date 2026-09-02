<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Loader;

use PrestaShop\PrestaShop\Core\Translation\Locale\Converter;
use PrestaShopBundle\Translation\Exception\UnsupportedLocaleException;

/**
 * Reads legacy locale files
 */
class LegacyFileReader
{
    /**
     * @var Converter Converts IETF language tags into two-letter language code
     */
    private $localeConverter;

    public function __construct(Converter $converter)
    {
        $this->localeConverter = $converter;
    }

    /**
     * Loads legacy translations from a file
     *
     * @param string $path Path where the locale file should be looked up
     * @param string $locale IETF language tag
     *
     * @return array Translation tokens
     */
    public function load($path, $locale)
    {
        // Each legacy file declare this variable to store the translations
        $_MODULE = [];

        $shopLocale = $this->localeConverter->toLegacyLocale($locale);

        $filePath = $path . "$shopLocale.php";

        if (!file_exists($filePath)) {
            throw UnsupportedLocaleException::fileNotFound($filePath, $locale);
        }

        // Load a global array $_MODULE (use include instead of include_once or the method will only work once)
        include $filePath;

        return $_MODULE;
    }
}
