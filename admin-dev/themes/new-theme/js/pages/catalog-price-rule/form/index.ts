/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import PriceReductionManager from '@components/form/price-reduction-manager';
import PriceFieldAvailabilityHandler from './price-field-availability-handler';

import CatalogPriceRuleFormMap from './catalog-price-rule-form-map';

const {$} = window;

$(() => {
  new PriceFieldAvailabilityHandler(
    CatalogPriceRuleFormMap.initialPrice,
    CatalogPriceRuleFormMap.price,
  );
  new PriceReductionManager(
    CatalogPriceRuleFormMap.reductionTypeSelect,
    CatalogPriceRuleFormMap.includeTax,
    CatalogPriceRuleFormMap.currencyId,
    CatalogPriceRuleFormMap.reductionTypeAmountSymbol,
  );
});
