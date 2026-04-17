/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$}: Window = window;

export default class AutocompleteWithEmail {
  map: Record<string, any>;

  $emailInput: JQuery;

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
            $(this.map[key]).val(response[key]);
          }
        });
      })
      .catch((response: AjaxError) => {
        if (typeof response.responseJSON !== 'undefined') {
          window.showErrorMessage(response.responseJSON.message);
        }
      });
  }
}
