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

const $ = window.$;

/**
 * Toggle Postcode input requirement on country selection
 *
 * Usage:
 *
 * <!-- Country select options must have need_postcode attribute when needed -->
 * <select name="id_country" id="id_country" states-url="path/to/states/api">
 *   ...
 *   <option value="6" need_postcode="1">Spain</value>
 *   ...
 * </select>
 *
 * In JS:
 *
 * new CountryPostcodeRequiredToggler('#id_country', '#id_country_postcode', 'label[for="id_country_postcode"]');
 */
export default class CountryPostcodeRequiredToggler {
  constructor(countryInputSelector, countryPostcodeInput, countryPostcodeInputLabel) {
    this.$countryPostcodeInput = $(countryPostcodeInput);
    this.$countryPostcodeInputLabel = $(countryPostcodeInputLabel);
    this.$countryInput = $(countryInputSelector);
    this.countryInputSelectedSelector = `${countryInputSelector}>option:selected`;
    this.countryPostcodeInputLabelDangerSelector = `${countryPostcodeInputLabel}>span.text-danger`;

    // If field is required regardless of the country
    // keep it required
    if (this.$countryPostcodeInput.attr('required')) {
      return;
    }

    this.$countryInput.on('change', () => this._toggle());

    // toggle on page load
    this._toggle();
  }

  /**
   * Toggles Postcode input required
   *
   * @private
   */
  _toggle() {
    const $countrySelectedOption = $(this.countryInputSelectedSelector);
    $(this.countryPostcodeInputLabelDangerSelector).remove();
    this.$countryPostcodeInput.attr('required', false);
    if (1 === parseInt($countrySelectedOption.attr('need_postcode'), 10)) {
      this.$countryPostcodeInput.attr('required', true);
      this.$countryPostcodeInputLabel.prepend($('<span class="text-danger">*</span>'));
    }
  }
}
