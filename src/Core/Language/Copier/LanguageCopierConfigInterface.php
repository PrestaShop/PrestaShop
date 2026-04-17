<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Language\Copier;

/**
 * Interface LanguageCopierConfigInterface defines configuration, required for copying a language.
 */
interface LanguageCopierConfigInterface
{
    /**
     * Get the theme name that language will be copied from.
     *
     * @return string
     */
    public function getThemeFrom();

    /**
     * Get the language name to copy from.
     *
     * @return string
     */
    public function getLanguageFrom();

    /**
     * Get the theme name that language will be copied to.
     *
     * @return string
     */
    public function getThemeTo();

    /**
     * Get the language name to copy to.
     *
     * @return string
     */
    public function getLanguageTo();
}
