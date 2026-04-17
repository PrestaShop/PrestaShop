/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import CartRuleMap from '@pages/cart-rule/cart-rule-map';
import PriceReductionManager from '@components/form/price-reduction-manager';
import DiscountApplicationManager from '@pages/cart-rule/form/discount-application-manager';

export default class DiscountManager {
  constructor() {
    this.init();
  }

  private init(): void {
    new DiscountApplicationManager();
    new PriceReductionManager(
      CartRuleMap.reductionTypeSelect,
      CartRuleMap.includeTaxInput,
      CartRuleMap.currencySelect,
      CartRuleMap.reductionValueSymbol,
    );
    this.toggleCurrency();
    document.querySelector(CartRuleMap.reductionTypeSelect)?.addEventListener('change', this.toggleCurrency);
  }

  private toggleCurrency(): void {
    if ($(CartRuleMap.reductionTypeSelect).val() === 'percentage') {
      $(CartRuleMap.currencySelect).fadeOut();
    } else {
      $(CartRuleMap.currencySelect).fadeIn();
    }
  }
}
