/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

export default {
  formWrapper: '#carrier-form',
  nameInput: 'input[name="carrier[step_general][name][__LANG__]"]',
  rangePriceLabel: '.js-range-label-case-price',
  rangeWeightLabel: '.js-range-label-case-weight',
  rangeRow: '.js-range-row',
  billingChoice: '.js-billing',
  freeShippingChoice: '.js-free-shipping',
  handlingCostChoice: '.js-handling-cost',
  rangesTable: '#js-carrier-ranges table',
  rangePriceTemplate: '#js-price-template > div',
  rangeFromTemplate: '#js-range-from-template > div',
  rangeToTemplate: '#js-range-to-template > div',
  addRangeBtn: '.js-add-range',
  removeRangeBtn: '.js-remove-range',
  rangeRemovingBtnRow: '.js-rm-buttons',
  transitTimeInput: 'input[name="carrier[step_general][transit_time][__LANG__]"]',
  taxRuleSelect: '.js-tax-rule',
  outrangedBehaviorSelect: '.js-outranged',
  zoneCheckbox: '.js-zone',
  groupAccessTable: '.js-group-access table',
  shopAssociation: '#carrier_step_multi_shop_shop_association',
  nameSummary: '.js-name-summary',
  rangesSummaryWrapper: '#js-ranges-summary',
  rangeSummary: '#js-range-summary',
  zonesSummaryTarget: '#js-zones-summary',
  groupsSummaryTarget: '#js-groups-summary',
  shopsSummaryTarget: '#js-shops-summary',
  transitSummaryCasePriced: '#js-priced-carrier-transit',
  transitSummaryCaseFree: '#js-free-carrier-transit',
  shippingCostSummaryCasePrice: '#js-carrier-shipping-cost-price',
  shippingCostSummaryCaseWeight: '#js-carrier-shipping-cost-weight',
  outrangedBehaviorSummaryCaseHighest: '#js-outranged-highest',
  outrangedBehaviorSummaryCaseDisable: '#js-outranged-disable',
  imageTarget: '#carrier-logo img',
  imageUploadBlock: '#js-carrier-logo-upload',
};
