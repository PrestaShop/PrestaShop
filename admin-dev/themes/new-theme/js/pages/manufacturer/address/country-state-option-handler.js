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
 * Responsible for form select 'state' handling
 */
export default class CountryStateOptionHandler {
  constructor() {
    this._handle();

    $('.js-country').on('change', () => this._handle());
  }

  /**
   * Handles state select field presentation
   *
   * @private
   */
  _handle() {
    const countrySelector = $('.js-country');
    $.ajax({
      url: `${countrySelector.data('states-url')}&id_country=${countrySelector.val()}`,
      method: 'GET',
      success: (response) => {
        this._handleCountryState(response.states);
      },
    });
  }

  /**
   * When country which has no states is selected, state select row is hidden
   *
   * @param states
   *
   * @private
   */
  _handleCountryState(states) {
    const stateRow = $('.js-country-state');
    stateRow.show();

    if (states.length === 0) {
      stateRow.hide();
    }
  }
}
