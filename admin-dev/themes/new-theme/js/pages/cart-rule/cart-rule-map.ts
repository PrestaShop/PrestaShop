/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
const discountContainer = '.discount-container';

export default {
  codeGeneratorBtn: '#cart_rule_information .js-generator-btn',
  codeInput: '#cart_rule_information_code',
  currencySelect: '#cart_rule_actions_discount_reduction_currency',
  customerItem: '#cart_rule_conditions_customer_list .entity-item',
  customerSearchContainer: '#cart_rule_conditions_customer',
  discountApplicationSelect: '#cart_rule_actions_discount_discount_application',
  discountContainer,
  giftProductSearchContainer: '#cart_rule_actions_gift_product',
  applyToDiscountedProductsContainer: '.apply-to-discounted-products',
  highlightSwitchContainer: '.js-highlight-switch-container',
  includeTaxInput: '#cart_rule_actions_discount_reduction_include_tax',
  reductionTypeSelect: '#cart_rule_actions_discount_reduction_type',
  // eslint-disable-next-line max-len
  reductionValueSymbol: `${discountContainer} .price-reduction-value .input-group .input-group-append .input-group-text, .price-reduction-value .input-group .input-group-prepend .input-group-text`,
  specificProductSearchComponent: '#cart_rule_actions_discount_specific_product',
  specificProductSearchContainer: '.specific-product-search-container',
};
