<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Addon\Theme;

use PrestaShop\PrestaShop\Core\Shop\ShopThemesNamesProviderInterface;

/**
 * Class ThemeProvider
 */
final class ThemeProvider implements ThemeProviderInterface
{
    /**
     * @var ThemeRepository
     */
    private $themeRepository;

    /**
     * @var Theme
     */
    private $theme;

    /**
     * @var ShopThemesNamesProviderInterface
     */
    private $shopThemesRepository;

    /**
     * @param ThemeRepository $themeRepository
     * @param Theme $theme
     */
    public function __construct(ThemeRepository $themeRepository, Theme $theme, ShopThemesNamesProviderInterface $shopThemesRepository)
    {
        $this->themeRepository = $themeRepository;
        $this->theme = $theme;
        $this->shopThemesRepository = $shopThemesRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentlyUsedTheme()
    {
        return $this->theme;
    }

    /**
     * {@inheritdoc}
     */
    public function getNotUsedThemes()
    {
        return $this->themeRepository->getListExcluding([
            $this->getCurrentlyUsedTheme()->getName(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getNotDeletableThemes(): array
    {
        // Get all parent themes because they are not deletable
        $parentThemes = $this->themeRepository->getParentThemes();
        // Add the themes used by shop(s)
        $shopThemes = $this->shopThemesRepository->getShopThemesNames();

        $notDeletableThemes = array_merge($parentThemes, $shopThemes);

        return array_unique($notDeletableThemes);
    }
}
