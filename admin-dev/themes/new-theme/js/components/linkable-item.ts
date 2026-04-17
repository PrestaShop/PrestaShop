/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

/**
 * Takes link from clicked item and redirects to it.
 */
export default class LinkableItem {
  constructor() {
    $(document).on('click', '.js-linkable-item', (event: JQueryEventObject) => {
      window.location = $(event.currentTarget).data('linkable-href');
    });
  }
}
