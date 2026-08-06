/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
const discountContainer = '.discount-container';

export default {
  reductionTypeSelect: '#discount_value_reduction_type',
  reductionCurrencySelect: '#discount_value_reduction_value_currency',
  reductionCurrencyAppend: `${discountContainer} .currency-money .input-group-append`,
  reductionIncludeTaxRow: '#discount_value_reduction_include_tax',
  reductionPercentSpan: '.currency-money-percent',
  discountContainer,
  freeGiftProductSearchContainer: '#discount_free_gift_product',
  discountTypeRadios: '#discount_type_selector_discount_type_selector input[type="radio"]',
  discountTypeSubmit: '#discountTypeSubmit',
  specificProductsSearchContainer: '#discount_conditions_product_specific_products',
  specificProductItem: '.specific-product-item',
  specificProductId: '.specific-product-id',
  specificProductType: '.specific-product-type',
  specificCombinationId: '.specific-combination-choice',
  carriersSelect: '#discount_conditions_delivery_carriers',
  countriesSelect: '#discount_conditions_delivery_country',
  categoryTree: '#discount_conditions_product_product_segment_category',
  customerSearchContainer: '#discount_customer_eligibility_eligibility_single_customer',
  productSegmentAttributes: '#discount_conditions_product_product_segment_attributes',
  productSegmentFeatures: '#discount_conditions_product_product_segment_features',
  quantityPerCustomerInput: '#discount_usability_quantity_per_customer',
  customerEligibilityInput: '#discount_customer_eligibility_eligibility',
  periodNeverExpiresCheckbox: 'input[name*="[period_never_expires]"]',
  periodExpiryDateFormGroup: '.date-range.row .col:has(.date-range-end-date)',
};
