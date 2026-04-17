<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\MailTemplate;

use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

/**
 * Interface ThemeCatalogInterface is used to list the available themes to generate
 * mail templates, each one containing its own layouts. Layouts are divided in two
 * categories "core" and "modules" layouts. You can change the themes collection or
 * modify a theme's layout collection via the hook:
 *  ThemeCatalogInterface::LIST_MAIL_THEMES_HOOK = actionListMailThemes
 */
interface ThemeCatalogInterface
{
    public const LIST_MAIL_THEMES_HOOK = 'actionListMailThemes';

    /**
     * Returns the list of existing themes.
     *
     * @return ThemeCollectionInterface
     */
    public function listThemes();

    /**
     * @param string $theme
     *
     * @return ThemeInterface
     *
     * @throws InvalidArgumentException
     */
    public function getByName($theme);
}
