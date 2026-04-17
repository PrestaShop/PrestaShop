<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeCollection;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class ThemeByNameChoiceProvider provides theme choices with name values.
 */
final class ThemeByNameChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var ThemeCollection collection of themes
     */
    private $themeCollection;

    /**
     * @param ThemeCollection $themeCollection
     */
    public function __construct(ThemeCollection $themeCollection)
    {
        $this->themeCollection = $themeCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $themeChoices = [];

        /** @var Theme $theme */
        foreach ($this->themeCollection as $theme) {
            $themeChoices[$theme->getName()] = $theme->getName();
        }

        return $themeChoices;
    }
}
