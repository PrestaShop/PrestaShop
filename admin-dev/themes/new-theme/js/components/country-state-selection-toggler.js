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

    this.$countryInput.on('change', () => this.change());

    return {};
  }

  /**
   * Change State selection
   *
   * @private
   */
  change() {
    const countryId = this.$countryInput.val();

    if (countryId === '') {
      return;
    }
    $.get({
      url: this.$countryInput.data('states-url'),
      dataType: 'json',
      data: {
        id_country: countryId,
      },
    })
      .then((response) => {
        this.$countryStateSelector.empty();

        Object.keys(response.states).forEach((value) => {
          this.$countryStateSelector.append(
            $('<option></option>')
              .attr('value', response.states[value])
              .text(value),
          );
        });

        this.toggle();
      })
      .catch((response) => {
        if (typeof response.responseJSON !== 'undefined') {
          window.showErrorMessage(response.responseJSON.message);
        }
      });
  }

  toggle() {
    this.$stateSelectionBlock.toggleClass('d-none', !this.$countryStateSelector.find('option').length > 0);
  }
}
