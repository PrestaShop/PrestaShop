/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

/**
 * Responsible for opening another page with specified url.
 * For example used in 'Save and preview' cms page create/edit actions.
 *
 * Usage: In selector element attr 'data-preview-url' provide page url.
 * The page will be opened once provided 'open_preview' parameter in query url
 */
export default class PreviewOpener {
  previewUrl: string;

  constructor(previewUrlSelector: string) {
    this.previewUrl = $(previewUrlSelector).data('preview-url');
    this.open();
  }

  /**
   * Opens new page of provided url
   *
   * @private
   */
  private open(): void {
    const urlParams = new URLSearchParams(window.location.search);

    if (this.previewUrl && urlParams.has('open_preview')) {
      window.open(this.previewUrl, '_blank');
    }
  }
}
