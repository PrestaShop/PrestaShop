<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Addon\Theme;

/**
 * Interface ThemeProviderInterface
 */
interface ThemeProviderInterface
{
    /**
     * Get currently used theme for context shop.
     *
     * @return Theme
     */
    public function getCurrentlyUsedTheme();

    /**
     * Get not used themes for context shop.
     *
     * @return Theme[]
     */
    public function getNotUsedThemes();
}
