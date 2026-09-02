<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Language\Pack\Import;

/**
 * Interface LanguagePackImporterInterface contract for language importer.
 */
interface LanguagePackImporterInterface
{
    /**
     * Imports language pack.
     *
     * @param string $isoCode
     *
     * @return array - returns array with error messages or an empty array on success case
     */
    public function import($isoCode);
}
