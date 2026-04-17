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
     * @param ThemeName $themeName
     */
    public function __construct(ThemeName $themeName)
    {
        $this->themeName = $themeName;
    }

    /**
     * @return ThemeName
     */
    public function getThemeName()
    {
        return $this->themeName;
    }
}
