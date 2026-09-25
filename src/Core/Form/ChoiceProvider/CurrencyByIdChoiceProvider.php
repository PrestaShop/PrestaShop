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
    /**
     * @var bool whether currencies that are not enabled are offered as well
     */
    private $includeDisabled;

    public function __construct(CurrencyDataProviderInterface $currencyDataProvider, bool $includeDisabled = false)
    {
        $this->currencyDataProvider = $currencyDataProvider;
        $this->includeDisabled = $includeDisabled;
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
        // Grouping by id_currency matters in multistore, where the shop association join returns the
        // same currency once per shop it belongs to and the list would otherwise repeat it.
        return $this->currencyDataProvider->getCurrencies(false, !$this->includeDisabled, true);
    }
}
