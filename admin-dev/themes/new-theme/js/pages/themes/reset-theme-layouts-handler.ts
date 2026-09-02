/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

/**
 * Handles "Reset to defaults" action submitting on button click.
 */
export default class ResetThemeLayoutsHandler {
  constructor() {
    $(document).on(
      'click',
      '.js-reset-theme-layouts-btn',
      (e: JQueryEventObject) => this.handleResetting(e),
    );
  }

  /**
   * @param {Event} event
   *
   * @private
   */
  private handleResetting(event: JQueryEventObject): void {
    const $btn = $(event.currentTarget);

    const $form = $('<form>', {
      action: $btn.data('submit-url'),
      method: 'POST',
    }).append(
      $('<input>', {
        name: 'token',
        value: $btn.data('csrf-token'),
        type: 'hidden',
      }),
    );

    $form.appendTo('body');
    $form.submit();
  }
}
