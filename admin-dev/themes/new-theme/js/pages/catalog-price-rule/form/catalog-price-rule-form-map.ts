/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Defines all selectors that are used in catalog price rule add/edit form.
 */
export default {
  // mapping for price-field-availability-handler
  initialPrice: '#catalog_price_rule_leave_initial_price',
  price: '#catalog_price_rule_price',
  currencyId: '#catalog_price_rule_id_currency',
  reductionTypeSelect: '#catalog_price_rule_reduction_type',
  reductionTypeAmountSymbol: '.price-reduction-value .input-group .input-group-append .input-group-text, '
    + '.price-reduction-value .input-group .input-group-prepend .input-group-text',

  // mapping for include-tax-field-visibility-handler
  reductionType: '.js-reduction-type-source',
  includeTax: '.js-include-tax-row',
};
