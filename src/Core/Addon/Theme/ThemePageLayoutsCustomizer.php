<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Addon\Theme;

use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;

/**
 * Class PagesLayoutCustomizer customizes pages layout for shop's Front Office theme.
 */
class ThemePageLayoutsCustomizer implements ThemePageLayoutsCustomizerInterface
{
    /**
     * @var Theme
     */
    private $theme;

    /**
     * @var ThemeManager
     */
    private $themeManager;

    /**
     * @var CacheClearerInterface
     */
    private $smartyCacheClearer;

    /**
     * @param Theme $theme
     * @param ThemeManager $themeManager
     * @param CacheClearerInterface $smartyCacheClearer
     */
    public function __construct(Theme $theme, ThemeManager $themeManager, CacheClearerInterface $smartyCacheClearer)
    {
        $this->theme = $theme;
        $this->themeManager = $themeManager;
        $this->smartyCacheClearer = $smartyCacheClearer;
    }

    /**
     * {@inheritdoc}
     */
    public function customize(array $pageLayouts)
    {
        $this->theme->setPageLayouts($pageLayouts);
        $this->themeManager->saveTheme($this->theme);

        $this->smartyCacheClearer->clear();
    }
}
