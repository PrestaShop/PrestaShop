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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
