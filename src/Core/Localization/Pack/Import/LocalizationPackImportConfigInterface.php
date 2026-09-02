<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\Pack\Import;

/**
 * Interface LocalizationPackImportConfigInterface defines.
 */
interface LocalizationPackImportConfigInterface
{
    /**
     * Available content to import.
     */
    public const CONTENT_STATES = 'states';
    public const CONTENT_TAXES = 'taxes';
    public const CONTENT_CURRENCIES = 'currencies';
    public const CONTENT_LANGUAGES = 'languages';
    public const CONTENT_UNITS = 'units';
    public const CONTENT_GROUPS = 'groups';

    /**
     * Get country ISO code.
     *
     * @return string
     */
    public function getCountryIsoCode();

    /**
     * Get content to import.
     *
     * @return array
     */
    public function getContentToImport();

    /**
     * Whether pack data should be downloaded.
     *
     * @return bool
     */
    public function shouldDownloadPackData();
}
