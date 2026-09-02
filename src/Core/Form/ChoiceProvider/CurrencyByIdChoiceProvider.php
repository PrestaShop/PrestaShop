<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceAttributeProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class CurrencyByIdChoiceProvider provides currency choices with ID values.
 */
final class CurrencyByIdChoiceProvider implements FormChoiceProviderInterface, FormChoiceAttributeProviderInterface
{
    /**
     * @var CurrencyDataProviderInterface
     */
    private $currencyDataProvider;

    /**
     * @param CurrencyDataProviderInterface $currencyDataProvider
     */
    public function __construct(CurrencyDataProviderInterface $currencyDataProvider)
    {
        $this->currencyDataProvider = $currencyDataProvider;
    }

    /**
     * Get currency choices.
     *
     * @return array
     */
    public function getChoices(): array
    {
        $currencies = $this->getCurrencies();
        $choices = [];

        foreach ($currencies as $currency) {
            $currencyLabel = sprintf('%s (%s)', $currency['name'], $currency['iso_code']);
            $choices[$currencyLabel] = $currency['id_currency'];
        }

        return $choices;
    }

    public function getChoicesAttributes(): array
    {
        $currencies = $this->getCurrencies();
        $choicesAttributes = [];

        foreach ($currencies as $currency) {
            $currencyLabel = sprintf('%s (%s)', $currency['name'], $currency['iso_code']);
            $choicesAttributes[$currencyLabel]['symbol'] = $currency['symbol'];
        }

        return $choicesAttributes;
    }

    private function getCurrencies(): array
    {
        return $this->currencyDataProvider->getCurrencies(false, true, true);
    }
}
