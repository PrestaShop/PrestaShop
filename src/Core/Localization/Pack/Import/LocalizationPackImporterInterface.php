<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\Pack\Import;

/**
 * Interface LocalizationPackImporterInterface defines contract for localization importer.
 */
interface LocalizationPackImporterInterface
{
    /**
     * Import localization pack.
     *
     * @param LocalizationPackImportConfig $config
     *
     * @return array Returns errors if any or empty array otherwise
     */
    public function import(LocalizationPackImportConfig $config);
}
