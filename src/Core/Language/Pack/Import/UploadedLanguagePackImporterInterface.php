<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Language\Pack\Import;

/**
 * Installs a translation pack the shop was given, as opposed to one fetched from the translation
 * service by LanguagePackImporterInterface.
 */
interface UploadedLanguagePackImporterInterface
{
    /**
     * @param string $archivePath path to the uploaded translation pack
     *
     * @return string[] the errors met, empty when the pack was installed
     */
    public function import(string $archivePath): array;
}
