<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Language\LanguageDataProvider;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;

/**
 * Class LanguageByIdChoiceProvider provides active language choices with ID values.
 */
final class LanguageByIdChoiceProvider implements ConfigurableFormChoiceProviderInterface
{
    /**
     * @var LanguageDataProvider
     */
    private $languageDataProvider;

    /**
     * LanguageByIdChoiceProvider constructor.
     *
     * @param LanguageDataProvider $languageDataProvider
     */
    public function __construct(LanguageDataProvider $languageDataProvider)
    {
        $this->languageDataProvider = $languageDataProvider;
    }

    /**
     * Get active language choices for form.
     *
     * @return array
     */
    public function getChoices(array $options = [])
    {
        $shopId = isset($options['shop_id']) && (int) $options['shop_id'] > 0 ? $options['shop_id'] : false;

        return FormChoiceFormatter::formatFormChoices(
            $this->languageDataProvider->getLanguages(true, $shopId),
            'id_lang',
            'name'
        );
    }
}
