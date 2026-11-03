/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$}: Window = window;

export default class AutocompleteWithEmail {
  map: Record<string, any>;

  $emailInput: JQuery;

  // Last value written in each field by the prefill, to tell it apart from a value typed by hand
  private prefilledValues: Record<string, string> = {};

  constructor(emailInputSelector: string, map: Record<string, any> = {}) {
    this.map = map;
    this.$emailInput = $(emailInputSelector);
    this.$emailInput.on('change', () => this.change());
  }

  private change(): void {
    $.get({
      url: this.$emailInput.data('customer-information-url'),
      dataType: 'json',
      data: {
        email: this.$emailInput.val(),
      },
    })
      .then((response) => {
        Object.keys(this.map).forEach((key: string) => {
          if (response[key] !== undefined) {
            this.prefill(key, response[key]);
          }
        });
      })
      .catch((response: AjaxError) => {
        if (typeof response.responseJSON !== 'undefined') {
          window.showErrorMessage(response.responseJSON.message);
        }
      });
  }

  /**
   * The response comes back asynchronously, possibly after the field was filled by hand:
   * only an empty field, or one still holding the previous prefill, is overwritten.
   */
  private prefill(key: string, value: string | null): void {
    const $field = $(this.map[key]);
    const currentValue = String($field.val() ?? '');

    if (currentValue !== '' && currentValue !== this.prefilledValues[key]) {
      return;
    }

    const newValue = value ?? '';
    $field.val(newValue);
    this.prefilledValues[key] = newValue;
  }
}
