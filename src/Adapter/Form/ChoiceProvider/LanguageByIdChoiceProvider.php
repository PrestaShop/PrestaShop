<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use Language;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class LanguageByIdChoiceProvider provides language choices with label as name and value as language id.
 */
final class LanguageByIdChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $languages = Language::getLanguages();
        $choices = [];

        /** @var Language $language */
        foreach ($languages as $language) {
            $choices[$language['name']] = $language['id_lang'];
        }

        return $choices;
    }
}
