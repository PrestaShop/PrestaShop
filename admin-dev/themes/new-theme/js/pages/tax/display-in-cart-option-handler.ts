/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

/**
 * Responsible for 'display tax in cart' option presentation.
 */
export default class DisplayInCartOptionHandler {
  constructor() {
    this.handle();

    $('.js-enable-tax').on('change', () => this.handle());
  }

  /**
   * If tax is disabled, then display tax in shopping cart option must be disabled.
   *
   * @private
   */
  private handle(): void {
    const enabledVal = $('.js-enable-tax:checked').val();
    const isTaxEnabled = parseInt(<string>enabledVal, 10);

    $('.js-display-in-cart').prop('disabled', !isTaxEnabled);
  }
}
