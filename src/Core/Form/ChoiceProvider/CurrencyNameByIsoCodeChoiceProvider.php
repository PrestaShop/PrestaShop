<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Localization\CLDR\CurrencyData;

/**
 * Class CurrencyNameByIsoCodeChoiceProvider is responsible for retrieving currency names from cldr library.
 */
final class CurrencyNameByIsoCodeChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var CurrencyData[]
     */
    private $cldrAllCurrencies;

    /**
     * @param CurrencyData[] $cldrAllCurrencies
     */
    public function __construct(array $cldrAllCurrencies)
    {
        $this->cldrAllCurrencies = $cldrAllCurrencies;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $result = [];
        foreach ($this->cldrAllCurrencies as $cldrCurrency) {
            // filter only on active currency
            // we dont need here currencies which were deactivated in all territories
            if (!$cldrCurrency->isActive()) {
                continue;
            }
            $currencyNames = $cldrCurrency->getDisplayNames();
            $isoCode = $cldrCurrency->getIsoCode();
            if (!empty($currencyNames['default'])) {
                $displayName = sprintf('%s (%s)', $currencyNames['default'], $isoCode);
            } else {
                $displayName = $isoCode;
            }

            $result[$displayName] = $isoCode;
        }
        ksort($result);

        return $result;
    }
}
