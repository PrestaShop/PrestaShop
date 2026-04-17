<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class ThemeChoiceProvider provides available themes as choices.
 */
final class ThemeChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var ThemeProviderInterface
     */
    private $themeProvider;

    /**
     * @param ThemeProviderInterface $themeProvider
     */
    public function __construct(ThemeProviderInterface $themeProvider)
    {
        $this->themeProvider = $themeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $usedTheme = $this->themeProvider->getCurrentlyUsedTheme();
        $notUsedThemes = $this->themeProvider->getNotUsedThemes();

        $choices = [];
        $choices[$usedTheme->getName()] = $usedTheme->getName();

        foreach ($notUsedThemes as $theme) {
            $choices[$theme->getName()] = $theme->getName();
        }

        return $choices;
    }
}
