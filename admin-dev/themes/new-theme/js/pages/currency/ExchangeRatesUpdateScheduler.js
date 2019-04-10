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
 * This class triggers events required for turning on or off exchange rates scheduler an displaying the right text
 * below the switch.
 */
export default class ExchangeRatesUpdateScheduler {
  constructor() {
   this._initEvents();

   return {};
  }

  _initEvents() {
    $(document).on('change', '.js-live-exchange-rate', (event) => this._initLiveExchangeRate(event));
  }

  /**
   * @param {Object} event
   *
   * @private
   */
  _initLiveExchangeRate(event) {
    const $liveExchangeRatesSwitch = $(event.currentTarget);
    const $form = $liveExchangeRatesSwitch.closest('form');
    const formItems = $form.serialize();

    $.ajax({
      type: 'POST',
      url: $liveExchangeRatesSwitch.attr('data-url'),
      data: formItems,
    })
      .then((response) => {
        if (!response.status) {
          showErrorMessage(response.message);
          this._changeTextByCurrentSwitchValue($liveExchangeRatesSwitch.val());

          return;
        }

        showSuccessMessage(response.message);
        this._changeTextByCurrentSwitchValue($liveExchangeRatesSwitch.val());
      }
    ).fail((response) => {
      if (typeof response.responseJSON !== 'undefined') {
        showErrorMessage(response.responseJSON.message);
        this._changeTextByCurrentSwitchValue($liveExchangeRatesSwitch.val());
      }
    });
  }

  _changeTextByCurrentSwitchValue(switchValue) {
    const valueParsed = parseInt(switchValue);
    $('.js-exchange-rate-text-when-disabled').toggleClass('d-none', 0 !== valueParsed);
    $('.js-exchange-rate-text-when-enabled').toggleClass('d-none', 1 !== valueParsed);
  }
}
