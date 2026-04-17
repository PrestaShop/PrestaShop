<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Language;

/**
 * Interface LanguageActivatorInterface defines contract for language activator.
 */
interface LanguageActivatorInterface
{
    /**
     * Activate language.
     *
     * @param int $langId
     */
    public function enable($langId);

    /**
     * Deactivate language.
     *
     * @param int $langId
     */
    public function disable($langId);
}
