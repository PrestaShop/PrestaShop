/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import CurrencySymbolUpdater from '@components/form/currency-symbol-updater';
import SpecificPriceMap from '@pages/product/specific-price/specific-price-map';
import PriceReductionManager from '@components/form/price-reduction-manager';
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

  // this handles retail price symbols, other price inputs are handled by PriceReductionManager
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
    }),
  );
  new PriceReductionManager(
    SpecificPriceMap.reductionTypeSelect,
    SpecificPriceMap.includeTaxInputContainer,
    SpecificPriceMap.currencyId,
    SpecificPriceMap.reductionTypeAmountSymbol,
  );

  new CustomerSelector();
  new CombinationSelector(new Router(), Number($(SpecificPriceMap.productIdInput).val()));
});
