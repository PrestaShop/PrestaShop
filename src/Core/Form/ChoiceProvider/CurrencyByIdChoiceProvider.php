<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Currency\CurrencyDataProvider;
use PrestaShop\PrestaShop\Core\Form\FormChoiceAttributeProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class CurrencyByIdChoiceProvider provides currency choices with ID values.
 */
final class CurrencyByIdChoiceProvider implements FormChoiceProviderInterface, FormChoiceAttributeProviderInterface
{
    /**
     * @var CurrencyDataProvider
     */
    private $currencyDataProvider;

    /**
     * @param CurrencyDataProvider $currencyDataProvider
     */
    public function __construct(CurrencyDataProvider $currencyDataProvider)
    {
        $this->currencyDataProvider = $currencyDataProvider;
    }

    /**
     * Get currency choices.
     *
     * @return array
     */
    public function getChoices()
    {
        $currencies = $this->getCurrencies();
        $choices = [];

        foreach ($currencies as $currency) {
            $currencyLabel = sprintf('%s (%s)', $currency['name'], $currency['iso_code']);
            $choices[$currencyLabel] = $currency['id_currency'];
        }

        return $choices;
    }

    public function getChoicesAttributes()
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
