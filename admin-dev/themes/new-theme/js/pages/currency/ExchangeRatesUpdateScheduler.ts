/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

/**
 * This class triggers events required for turning on or off exchange rates scheduler an displaying the right text
 * below the switch.
 */
export default class ExchangeRatesUpdateScheduler {
  constructor() {
    this.initEvents();
  }

  private initEvents() {
    $(document).on(
      'change',
      '.js-live-exchange-rate',
      (event: JQueryEventObject) => this.initLiveExchangeRate(event),
    );
  }

  /**
   * @param {Object} event
   *
   * @private
   */
  private initLiveExchangeRate(event: JQueryEventObject) {
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
          this.changeTextByCurrentSwitchValue(
            <string>$liveExchangeRatesSwitch.val(),
          );

          return;
        }

        window.showSuccessMessage(response.message);
        this.changeTextByCurrentSwitchValue(
          <string>$liveExchangeRatesSwitch.val(),
        );
      })
      .fail((response: AjaxResponse) => {
        if (typeof response.responseJSON !== 'undefined') {
          window.showErrorMessage(response.responseJSON.message);
          this.changeTextByCurrentSwitchValue(
            <string>$liveExchangeRatesSwitch.val(),
          );
        }
      });
  }

  changeTextByCurrentSwitchValue(switchValue: string): void {
    const valueParsed = parseInt(switchValue, 10);
    $('.js-exchange-rate-text-when-disabled').toggleClass(
      'd-none',
      valueParsed !== 0,
    );
    $('.js-exchange-rate-text-when-enabled').toggleClass(
      'd-none',
      valueParsed !== 1,
    );
  }
}
