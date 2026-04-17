/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

/**
 * Class MetaPageNameOptionHandler is responsible for checking the index page condition - if index page is selected it
 * does not allow to enter url rewrite field by disabling that input. In another cases url rewrite field is mandatory to
 * enter.
 */
export default class MetaPageNameOptionHandler {
  constructor() {
    const pageNameSelector = '.js-meta-page-name';
    const currentPage = $(pageNameSelector).val();
    this.setUrlRewriteDisabledStatusByCurrentPage(<string>currentPage);

    $(document).on('change', pageNameSelector, (event: JQueryEventObject) => this.changePageNameEvent(event),
    );
  }

  /**
   * An event which is being called after the selector is being updated.
   * @param {object} event
   * @private
   */
  private changePageNameEvent(event: JQueryEventObject): void {
    const $this = $(event.currentTarget);
    const currentPage = $this.val();

    this.setUrlRewriteDisabledStatusByCurrentPage(<string>currentPage);
  }

  /**
   * Sets url rewrite form field to disabled or enabled according to current page value.
   * @param {string} currentPage
   * @private
   */
  private setUrlRewriteDisabledStatusByCurrentPage(currentPage: string): void {
    $('.js-url-rewrite input').prop('disabled', currentPage === 'index');
  }
}
