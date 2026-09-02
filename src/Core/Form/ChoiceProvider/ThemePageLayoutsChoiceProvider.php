<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class ThemePageLayoutsChoiceProvider proves page layout choices for given theme.
 */
final class ThemePageLayoutsChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var Theme
     */
    private $theme;

    /**
     * @param Theme $theme
     */
    public function __construct(Theme $theme)
    {
        $this->theme = $theme;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $choices = [];

        foreach ($this->theme->getAvailableLayouts() as $layoutId => $availableLayout) {
            $choices[sprintf('%s - %s', $availableLayout['name'], $availableLayout['description'])] = $layoutId;
        }

        return $choices;
    }
}
