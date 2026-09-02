<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Loader;

use PrestaShop\PrestaShop\Core\Translation\Exception\UnsupportedLocaleException;
use PrestaShop\PrestaShop\Core\Translation\Locale\Converter;

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
    public function load(string $path, string $locale): array
    {
        // Each legacy file declare this variable to store the translations
        $_MODULE = [];

        $shopLocale = $this->localeConverter->toLegacyLocale($locale);

        $filePath = $path . "$shopLocale.php";

        if (!file_exists($filePath)) {
            throw UnsupportedLocaleException::fileNotFound($filePath, $locale);
        }

        // Load a global array $_MODULE
        include_once $filePath;

        return $_MODULE;
    }
}
