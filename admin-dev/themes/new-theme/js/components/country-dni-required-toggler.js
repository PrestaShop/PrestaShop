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

const {$} = window;

/**
 * Toggle DNI input requirement on country selection
 *
 * Usage:
 *
 * <!-- Country select options must have need_dni attribute when needed -->
 * <select name="id_country" id="id_country" states-url="path/to/states/api">
 *   ...
 *   <option value="6" need_dni="1">Spain</value>
 *   ...
 * </select>
 *
 * In JS:
 *
 * new CountryDniRequiredToggler('#id_country', '#id_country_dni', 'label[for="id_country_dni"]');
 */
export default class CountryDniRequiredToggler {
  constructor(countryInputSelector, countryDniInput, countryDniInputLabel) {
    this.$countryDniInput = $(countryDniInput);
    this.$countryDniInputLabel = $(countryDniInputLabel);
    this.$countryInput = $(countryInputSelector);
    this.countryInputSelectedSelector = `${countryInputSelector}>option:selected`;
    this.countryDniInputLabelDangerSelector = `${countryDniInputLabel}>span.text-danger`;

    // If field is required regardless of the country
    // keep it required
    if (this.$countryDniInput.attr('required')) {
      return;
    }

    this.$countryInput.on('change', () => this.toggle());

    // toggle on page load
    this.toggle();
  }

  /**
   * Toggles DNI input required
   *
   * @private
   */
  toggle() {
    $(this.countryDniInputLabelDangerSelector).remove();
    this.$countryDniInput.prop('required', false);
    if (parseInt($(this.countryInputSelectedSelector).attr('need_dni'), 10) === 1) {
      this.$countryDniInput.prop('required', true);
      this.$countryDniInputLabel.prepend($('<span class="text-danger">*</span>'));
    }
  }
}
