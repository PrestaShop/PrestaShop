<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\Pack\Import;

/**
 * Class LocalizationPackImportConfig is value object which is responsible
 * for storing localization pack configuration for import.
 */
final class LocalizationPackImportConfig implements LocalizationPackImportConfigInterface
{
    /**
     * @var string
     */
    private $countryIso;

    /**
     * @var array
     */
    private $contentToImport;

    /**
     * @var bool
     */
    private $downloadPackData;

    /**
     * @param string $countryIso Country ISO code
     * @param array $contentToImport Content that should be impoerted (e.g states, taxes & etc)
     * @param bool $downloadPackData Whether pack data should be downloaded from prestashop.com server
     */
    public function __construct($countryIso, array $contentToImport, $downloadPackData)
    {
        $this->countryIso = (string) $countryIso;
        $this->contentToImport = $contentToImport;
        $this->downloadPackData = (bool) $downloadPackData;
    }

    /**
     * Get country ISO code.
     *
     * @return string
     */
    public function getCountryIsoCode()
    {
        return $this->countryIso;
    }

    /**
     * Get content to import.
     *
     * @return array
     */
    public function getContentToImport()
    {
        return $this->contentToImport;
    }

    /**
     * Whether pack data should be downloaded.
     *
     * @return bool
     */
    public function shouldDownloadPackData()
    {
        return $this->downloadPackData;
    }
}
