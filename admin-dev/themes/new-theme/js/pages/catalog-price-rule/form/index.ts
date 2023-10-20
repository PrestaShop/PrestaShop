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

import ReductionTaxFieldToggle from '@components/form/reduction-tax-field-toggle';
import CurrencySymbolUpdater from '@components/form/currency-symbol-updater';
import PriceFieldAvailabilityHandler from './price-field-availability-handler';

import CatalogPriceRuleFormMap from './catalog-price-rule-form-map';

const {$} = window;

$(() => {
  new CurrencySymbolUpdater(
    CatalogPriceRuleFormMap.currencyId,
    ((symbol: string): void => {
      if (symbol === '') {
        return;
      }

      // Reduction Amount
      const reductionTypeSelect = document.querySelector<HTMLSelectElement>(CatalogPriceRuleFormMap.reductionTypeSelect);

      if (reductionTypeSelect) {
        // Update the amount option innerHTML
        for (let i = 0; i < reductionTypeSelect.options.length; i += 1) {
          const reductionOption = reductionTypeSelect.options[i];

          if (reductionOption.value === 'amount') {
            reductionOption.innerHTML = symbol;
          }
        }

        const selectedReduction = reductionTypeSelect.options[reductionTypeSelect.selectedIndex].value;

        if (selectedReduction === 'amount') {
          const reductionTypeAmountSymbols = document.querySelectorAll(
            CatalogPriceRuleFormMap.reductionTypeAmountSymbol,
          );

          if (reductionTypeAmountSymbols.length) {
            reductionTypeAmountSymbols.forEach((value: Element) => {
              const elt = value;
              elt.innerHTML = symbol;
            });
          }
        }
      }
    }),
  );
  new PriceFieldAvailabilityHandler(
    CatalogPriceRuleFormMap.initialPrice,
    CatalogPriceRuleFormMap.price,
  );
  new ReductionTaxFieldToggle(
    CatalogPriceRuleFormMap.reductionTypeSelect,
    CatalogPriceRuleFormMap.includeTax,
    CatalogPriceRuleFormMap.currencyId,
    CatalogPriceRuleFormMap.reductionTypeAmountSymbol,
  );
});
