/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

/**
 * This handler displays use theme modal and handles the submit form logic.
 */
export default class UseThemeHandler {
  constructor() {
    $(document).on(
      'click',
      '.js-display-use-theme-modal',
      (e: JQueryEventObject) => this.displayUseThemeModal(e),
    );
  }

  /**
   * Displays modal with its own event handling.
   *
   * @param e
   * @private
   */
  private displayUseThemeModal(e: JQueryEventObject): void {
    const $modal = $('#use_theme_modal');

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
  private submitForm($modal: JQuery, originalButtonEvent: JQueryEventObject) {
    const $formButton = $(originalButtonEvent.currentTarget);

    $modal.on('click', '.js-submit-use-theme', () => {
      const $form = $formButton.closest('form');
      $form.submit();
    });
  }
}
