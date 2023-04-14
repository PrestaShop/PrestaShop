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

import GeneratableInput from '@components/generatable-input';
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
  ]);

  new GeneratableInput().attachOn(CartRuleMap.codeGeneratorBtn);
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
