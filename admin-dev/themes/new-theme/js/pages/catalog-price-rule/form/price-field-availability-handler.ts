/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

/**
 * Enables/disables 'price' field depending from 'leave_initial_price' field checkbox value
 */
export default class PriceFieldAvailabilityHandler {
  $sourceSelector: JQuery;

  $targetSelector: JQuery;

  constructor(checkboxSelector: string, targetSelector: string) {
    this.$sourceSelector = $(checkboxSelector);
    this.$targetSelector = $(targetSelector);
    this.handle();
    this.$sourceSelector.on('change', () => this.handle());
  }

  /**
   * When checkbox value is 1, target field is disabled, else enabled
   *
   * @private
   */
  private handle(): void {
    const checkboxVal = this.$sourceSelector.is(':checked');

    this.$targetSelector.prop('disabled', checkboxVal);
  }
}
