<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class LanguageChoiceProvider provides languages choices with ID values.
 *
 * @todo this class could be merged with \PrestaShop\PrestaShop\Core\Form\ChoiceProvider\LanguageByIdChoiceProvider
 *       as this class can fully achieve the same behavior as the LanguageByIdChoiceProvider.
 *       It would break BC though, so couldn't be done at the moment.
 */
final class LanguageChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var array
     */
    private $languages;

    /**
     * @param array $languages
     */
    public function __construct(array $languages)
    {
        $this->languages = $languages;
    }

    /**
     * Get active language choices for form.
     *
     * @return array
     */
    public function getChoices()
    {
        return FormChoiceFormatter::formatFormChoices(
            $this->languages,
            'id_lang',
            'name'
        );
    }
}
