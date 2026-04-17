<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use Currency;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;

/**
 * Provides currency choices where currency is represented by symbol (e.g. € for euro) and value is currency id.
 */
final class CurrencySymbolByIdChoiceProvider implements ConfigurableFormChoiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getChoices(array $options): array
    {
        return FormChoiceFormatter::formatFormChoices(
            Currency::getCurrenciesByIdShop($options['id_shop']),
            'id_currency',
            'symbol'
        );
    }
}
