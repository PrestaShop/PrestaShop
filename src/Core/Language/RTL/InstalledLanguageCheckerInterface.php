<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Language\RTL;

/**
 * Interface InstalledRtlLanguageCheckerInterface
 */
interface InstalledLanguageCheckerInterface
{
    /**
     * Check if there are at least one RTL language installed in shop.
     *
     * @return bool
     */
    public function isInstalledRtlLanguage();
}
