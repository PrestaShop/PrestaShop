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
