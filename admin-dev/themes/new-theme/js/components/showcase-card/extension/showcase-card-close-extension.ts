/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {ShowcaseCard} from '@PSTypes/showcase';

const {$} = window;

/**
 * Class ShowcaseCardCloseExtension is responsible for providing helper block closing behavior
 */
export default class ShowcaseCardCloseExtension {
  /**
   * Extend helper block.
   *
   * @param {ShowcaseCard} helperBlock
   */
  extend(helperBlock: ShowcaseCard): void {
    const container = helperBlock.getContainer();
    container.on('click', '.js-remove-helper-block', (evt: JQuery.ClickEvent) => {
      container.remove();

      const $btn = $(evt.target);
      const url = $btn.data('closeUrl');
      const cardName = $btn.data('cardName');

      if (url) {
        // notify the card was closed
        $.post(url, {
          close: 1,
          name: cardName,
        });
      }
    });
  }
}
