<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeCatalogInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeCollectionInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeInterface;

/**
 * Class MailThemeChoiceProvider is responsible to provide a list of available mail themes.
 */
class MailThemeChoiceProvider implements FormChoiceProviderInterface
{
    /** @var array */
    private $choices;

    /** @var ThemeCatalogInterface */
    private $themeCatalog;

    /**
     * @param ThemeCatalogInterface $themeCatalog
     */
    public function __construct(ThemeCatalogInterface $themeCatalog)
    {
        $this->themeCatalog = $themeCatalog;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        if (null === $this->choices) {
            $this->choices = [];

            /** @var ThemeCollectionInterface $collection */
            $collection = $this->themeCatalog->listThemes();

            /** @var ThemeInterface $theme */
            foreach ($collection as $theme) {
                $this->choices[$theme->getName()] = $theme->getName();
            }
        }

        return $this->choices;
    }
}
