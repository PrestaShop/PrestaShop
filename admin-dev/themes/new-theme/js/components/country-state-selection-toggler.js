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
 * Displays, fills or hides State selection block depending on selected country.
 *
 * Usage:
 *
 * <!-- Country select must have unique identifier & url for states API -->
 * <select name="id_country" id="id_country" states-url="path/to/states/api">
 *   ...
 * </select>
 *
 * <!-- If selected country does not have states, then this block will be hidden -->
 * <div class="js-state-selection-block">
 *   <select name="id_state">
 *     ...
 *   </select>
 * </div>
 *
 * In JS:
 *
 * new CountryStateSelectionToggler('#id_country', '#id_state', '.js-state-selection-block');
 */
export default class CountryStateSelectionToggler {
  constructor(countryInputSelector, countryStateSelector, stateSelectionBlockSelector) {
    this.$stateSelectionBlock = $(stateSelectionBlockSelector);
    this.$countryStateSelector = $(countryStateSelector);
    this.$countryInput = $(countryInputSelector);

    this.$countryInput.on('change', () => this._toggle());

    // toggle on page load
    this._toggle(true);

    return {};
  }

  /**
   * Toggles State selection
   *
   * @private
   */
  _toggle(isFirstToggle = false) {
    $.ajax({
      url: this.$countryInput.data('states-url'),
      method: 'GET',
      dataType: 'json',
      data: {
        id_country: this.$countryInput.val(),
      }
    }).then((response) => {
      if (response.states.length === 0) {
        this.$stateSelectionBlock.fadeOut();

        return;
      }

      this.$stateSelectionBlock.fadeIn();

      if (isFirstToggle === false) {
        this.$countryStateSelector.empty();
        var _this = this;
        $.each(response.states, function (index, value) {
          _this.$countryStateSelector.append($('<option></option>').attr('value', value).text(index));
        })
      }
    }).catch((response) => {
      if (typeof response.responseJSON !== 'undefined') {
        showErrorMessage(response.responseJSON.message);
      }
    });
  }
}
