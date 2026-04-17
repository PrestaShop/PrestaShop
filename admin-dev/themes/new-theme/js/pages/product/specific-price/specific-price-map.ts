/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

export default {
  productIdInput: '#specific_price_product_id',
  formContainer: 'form[name="specific_price"]',
  currencyId: '#specific_price_groups_currency_id',
  customerSearchContainer: '#specific_price_customer',
  priceInput: '#specific_price_fixed_price',
  fixedPriceSymbol: '.js-fixed-price-row .input-group.money-type .input-group-append .input-group-text, '
    + '.js-fixed-price-row .input-group.money-type .input-group-prepend .input-group-text',
  leaveInitialPriceCheckbox: '#specific_price_leave_initial_price',
  reductionTypeSelect: '#specific_price_impact_reduction_type',
  reductionTypeAmountSymbol: '.price-reduction-value .input-group .input-group-append .input-group-text, '
    + '.price-reduction-value .input-group .input-group-prepend .input-group-text',
  includeTaxInputContainer: '.js-include-tax-row',
  customerItem: '#specific_price_customer_list .entity-item',
  switchReductionName: 'specific_price[impact][disabling_switch_reduction]',
  switchFixedName: 'specific_price[impact][disabling_switch_fixed_price_tax_excluded]',
  shopIdSelect: '#specific_price_groups_shop_id',
  combinationIdSelect: '#specific_price_combination_id',
};
