/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

/**
 * This handler displays delete theme modal and handles the submit action.
 */
export default class DeleteThemeHandler {
  constructor() {
    $(document).on(
      'click',
      '.js-display-delete-theme-modal',
      (e: JQueryEventObject) => this.displayDeleteThemeModal(e),
    );
  }

  /**
   * Displays modal with its own event handling.
   *
   * @param e
   * @private
   */
  private displayDeleteThemeModal(e: JQueryEventObject): void {
    const $modal = $('#delete_theme_modal');

    $modal.modal('show');

    this.submitForm($modal, e);
  }

  /**
   * Submits form by adding click event listener for modal and calling original form event.
   *
   * @param $modal
   * @param originalButtonEvent
   *
   * @private
   */
  private submitForm(
    $modal: JQuery,
    originalButtonEvent: JQueryEventObject,
  ): void {
    const $formButton = $(originalButtonEvent.currentTarget);

    $modal.on('click', '.js-submit-delete-theme', () => {
      const $form = $formButton.closest('form');
      $form.submit();
    });
  }
}
