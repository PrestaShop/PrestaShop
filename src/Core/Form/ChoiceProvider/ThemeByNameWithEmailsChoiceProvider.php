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
 * Class ThemeByNameChoiceProvider provides theme choices with name values, but it
 * filters themes which haven't overridden any email templates.
 */
final class ThemeByNameWithEmailsChoiceProvider implements FormChoiceProviderInterface
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
            $coreMailsFolder = $theme->getDirectory() . '/mails';
            $modulesMailFolder = $theme->getDirectory() . '/modules';
            if (is_dir($coreMailsFolder) || is_dir($modulesMailFolder)) {
                $themeChoices[$theme->getName()] = $theme->getDirectory();
            }
        }

        return $themeChoices;
    }
}
