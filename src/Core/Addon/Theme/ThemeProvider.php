<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Addon\Theme;

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
     * @param ThemeRepository $themeRepository
     * @param Theme $theme
     */
    public function __construct(ThemeRepository $themeRepository, Theme $theme)
    {
        $this->themeRepository = $themeRepository;
        $this->theme = $theme;
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
}
