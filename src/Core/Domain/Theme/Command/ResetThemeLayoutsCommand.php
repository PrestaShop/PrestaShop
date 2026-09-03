<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Theme\Command;

use PrestaShop\PrestaShop\Core\Domain\Theme\ValueObject\ThemeName;

/**
 * Class ResetThemeLayoutsCommand resets theme's page layouts to defaults.
 */
class ResetThemeLayoutsCommand
{
    /**
     * @var ThemeName
     */
    private $themeName;

    /**
     * @param string|ThemeName $themeName
     *
     * @deprecated Since 9.2 - The parameter $themeName will not support ThemeName as type in 10.0
     */
    public function __construct(string|ThemeName $themeName)
    {
        $this->themeName = is_object($themeName) ? $themeName : new ThemeName($themeName);
    }

    /**
     * @return ThemeName
     */
    public function getThemeName()
    {
        return $this->themeName;
    }
}
