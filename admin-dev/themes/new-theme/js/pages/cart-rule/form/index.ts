/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import CartRuleMap from '@pages/cart-rule/cart-rule-map';
import FormFieldToggler from '@components/form/form-field-toggler';
import CartRuleEventMap from '@pages/cart-rule/cart-rule-event-map';
import CustomerSearchInput from '@components/form/customer-search-input';
import DiscountManager from '@pages/cart-rule/form/discount-manager';
import ProductSearchInput from '@components/form/product-search-input';

$(() => {
  window.prestashop.component.initComponents([
    'TranslatableField',
    'TranslatableInput',
    'EventEmitter',
  ]);

  // It is important that discountManager is initialized before DisablingSwitch
  // or else it won't find reduction type value when it is disabled therefore not toggling some inputs correctly on init
  new DiscountManager();

  window.prestashop.component.initComponents([
    'DisablingSwitch',
    'GeneratableInput',
  ]);

  window.prestashop.instance.generatableInput.attachOn(CartRuleMap.codeGeneratorBtn);
  new FormFieldToggler({
    disablingInputSelector: CartRuleMap.codeInput,
    targetSelector: CartRuleMap.highlightSwitchContainer,
    matchingValue: '',
  });

  new CustomerSearchInput(
    CartRuleMap.customerSearchContainer,
    CartRuleMap.customerItem,
    // use all shops constraint
    () => null,
    CartRuleEventMap.switchCustomer,
  );

  new ProductSearchInput(CartRuleMap.giftProductSearchContainer);
});
