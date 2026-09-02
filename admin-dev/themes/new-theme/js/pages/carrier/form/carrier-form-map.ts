/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

export default {
  form: 'form[name="carrier"]',
  navigationBar: '#form-nav',
  freeShippingInput: 'input[name="carrier[shipping_settings][is_free]"]',
  zonesInput: '#carrier_shipping_settings_zones',
  zoneIdOption: (zoneId: number|string): string => `option[value="${zoneId}"]`,
  rangesInput: '#carrier_shipping_settings_ranges_data',
  rangesSelectionAppId: '#carrier_shipping_settings_ranges-app',
  addRangeButton: '.js-add-carrier-ranges-btn',
  shippingMethodRow: '#carrier_shipping_settings_shipping_method',
  shippingMethodInput: 'input[name="carrier[shipping_settings][shipping_method]"]',
  deleteZoneButton: '.js-carrier-delete-zone',
  zonesContainer: '#carrier_shipping_settings_ranges_costs',
  rangesContainer: '.js-carrier-range-container',
  rangesContainerBody: '.js-carrier-range-container-body',
  zoneRow: '.js-carrier-zone-row',
  zoneIdInput: 'input[name$="[zoneId]"]',
  rangeNamePreview: '.js-carrier-range-name .text-preview-value',
  rangeNameInput: '.js-carrier-range-name input[type="hidden"]',
  rangeRow: '.js-carrier-range-row',
  zoneNamePreview: '.card-title .text-preview-value',
  rangeFromInput: 'input[name$="[from]"]',
  rangeToInput: 'input[name$="[to]"]',
  rangePriceInput: 'input[name$="[price]"]',
  shippingControls: [
    '#carrier_shipping_settings_id_tax_rule_group',
    '#carrier_shipping_settings_has_additional_handling_fee',
    '#carrier_shipping_settings_shipping_method',
    '#carrier_shipping_settings_range_behavior',
    '#carrier_shipping_settings_ranges',
    '#carrier_shipping_settings_ranges_costs',
  ],
};
