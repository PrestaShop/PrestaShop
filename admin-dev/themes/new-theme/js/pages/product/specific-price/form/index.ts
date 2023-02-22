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
import CurrencySymbolUpdater from '@components/form/currency-symbol-updater';
import SpecificPriceMap from '@pages/product/specific-price/specific-price-map';
import SpecificPriceEventMap from '@pages/product/specific-price/specific-price-event-map';
import ReductionTaxFieldToggle from '@components/form/reduction-tax-field-toggle';
import CombinationSelector from '@pages/product/specific-price/form/combination-selector';
import Router from '@components/router';
import CustomerSelector from '@pages/product/specific-price/form/customer-selector';

const {$} = window;

$(() => {
  window.prestashop.component.initComponents([
    'EventEmitter',
    'DisablingSwitch',
    'DateRange',
  ]);

  const {eventEmitter} = window.prestashop.instance;

  new CurrencySymbolUpdater(
    SpecificPriceMap.currencyId,
    ((symbol: string): void => {
      if (symbol === '') {
        return;
      }

      // Specific Price
      const priceSymbols = document.querySelectorAll(SpecificPriceMap.fixedPriceSymbol);

      if (priceSymbols.length) {
        priceSymbols.forEach((value: Element) => {
          const elt = value;
          elt.innerHTML = symbol;
        });
      }

      // Reduction Amount
      const reductionTypeSelect = document.querySelector<HTMLSelectElement>(SpecificPriceMap.reductionTypeSelect);

      if (reductionTypeSelect) {
        // Update the amount option innerHTML
        for (let i = 0; i < reductionTypeSelect.options.length; i += 1) {
          const reductionOption = reductionTypeSelect.options[i];

          if (reductionOption.value === 'amount') {
            reductionOption.innerHTML = symbol;
          }
        }

        const selectedReduction = reductionTypeSelect.options[reductionTypeSelect.selectedIndex].value;

        // If amount reduction type is selected update the reduction value symbol
        if (selectedReduction === 'amount') {
          const reductionTypeAmountSymbols = document.querySelectorAll(SpecificPriceMap.reductionTypeAmountSymbol);

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
  new ReductionTaxFieldToggle(
    SpecificPriceMap.reductionTypeSelect,
    SpecificPriceMap.includeTaxInputContainer,
    SpecificPriceMap.currencyId,
    SpecificPriceMap.reductionTypeAmountSymbol,
  );

  new CustomerSelector();

  // When customer search is disabled we also disable the selected item (if present)
  eventEmitter.on(SpecificPriceEventMap.switchCustomer, (event: any) => {
    $(SpecificPriceMap.customerItem).toggleClass('disabled', event.disable);
  });

  new CombinationSelector(new Router(), Number($(SpecificPriceMap.productIdInput).val()));
});
