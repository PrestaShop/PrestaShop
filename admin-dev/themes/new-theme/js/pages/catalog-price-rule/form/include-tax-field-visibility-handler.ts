/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

/**
 * Shows/hides 'include_tax' field depending from 'reduction_type' field value
 *
 * @deprecated use @components/form/include-tax-field-toggle.ts
 */
export default class IncludeTaxFieldVisibilityHandler {
  $sourceSelector: JQuery;

  $targetSelector: JQuery;

  constructor(sourceSelector: string, targetSelector: string) {
    this.$sourceSelector = $(sourceSelector);
    this.$targetSelector = $(targetSelector);
    this.handle();
    this.$sourceSelector.on('change', () => this.handle());
  }

  /**
   * When source value is 'percentage', target field is shown, else hidden
   *
   * @private
   */
  private handle(): void {
    if (this.$sourceSelector.val() === 'percentage') {
      this.$targetSelector.fadeOut();
    } else {
      this.$targetSelector.fadeIn();
    }
  }
}
