/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {ShowcaseExtension} from '@PSTypes/showcase';

const {$} = window;

/**
 * Class ShowcaseCard is responsible for handling events related with showcase card.
 */
export default class ShowcaseCard {
  id: string;

  $container: JQuery;

  /**
   * Showcase card id.
   *
   * @param {string} id
   */
  constructor(id: string) {
    this.id = id;
    this.$container = $(`#${this.id}`);
  }

  /**
   * Get showcase card container.
   *
   * @returns {jQuery}
   */
  getContainer(): JQuery {
    return this.$container;
  }

  /**
   * Extend showcase card with external extensions.
   *
   * @param {object} extension
   */
  addExtension(extension: ShowcaseExtension): void {
    extension.extend(this);
  }
}
