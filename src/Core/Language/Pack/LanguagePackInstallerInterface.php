<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Language\Pack;

/**
 * Interface LanguagePackInstallerInterface defines contract for language pack installer.
 */
interface LanguagePackInstallerInterface
{
    /**
     * Downloads and installs language pack data.
     *
     * @param string $iso -3166-2 alpha-2 format code
     *
     * @return array - if not empty then it contains errors. In another case the function
     *               has been executed successfully
     */
    public function downloadAndInstallLanguagePack($iso);
}
