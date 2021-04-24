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
 * This class triggers events required for turning on or off exchange rates scheduler an displaying the right text
 * below the switch.
 */
export default class ExchangeRatesUpdateScheduler {
  constructor() {
    this.initEvents();

    return {};
  }

  initEvents() {
    $(document).on('change', '.js-live-exchange-rate', (event) => this.initLiveExchangeRate(event));
  }

  /**
   * @param {Object} event
   *
   * @private
   */
  initLiveExchangeRate(event) {
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
          window.showErrorMessage(response.message);
          this.changeTextByCurrentSwitchValue($liveExchangeRatesSwitch.val());

          return;
        }

        window.showSuccessMessage(response.message);
        this.changeTextByCurrentSwitchValue($liveExchangeRatesSwitch.val());
      },
      ).fail((response) => {
        if (typeof response.responseJSON !== 'undefined') {
          window.showErrorMessage(response.responseJSON.message);
          this.changeTextByCurrentSwitchValue($liveExchangeRatesSwitch.val());
        }
      });
  }

  changeTextByCurrentSwitchValue(switchValue) {
    const valueParsed = parseInt(switchValue, 10);
    $('.js-exchange-rate-text-when-disabled').toggleClass('d-none', valueParsed !== 0);
    $('.js-exchange-rate-text-when-enabled').toggleClass('d-none', valueParsed !== 1);
  }
}
