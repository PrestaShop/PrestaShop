<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Language;

/**
 * Interface LanguageValidatorInterface defines contract for LanguageValidator.
 */
interface LanguageValidatorInterface
{
    /**
     * Checks if language is installed by comparing locale.
     *
     * @param string $locale
     *
     * @return bool
     */
    public function isInstalledByLocale($locale);
}
