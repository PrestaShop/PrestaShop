/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {GridExtension} from '@PSTypes/grid';
import GridMap from '@components/grid/grid-map';

const {$}: Window = window;

/**
 * Class is responsible for handling Grid events
 */
export default class Grid {
  id: string;

  $container: JQuery;

  /**
   * Grid id
   *
   * @param {string} id
   */
  constructor(id: string) {
    this.id = id;
    this.$container = $(GridMap.grid(this.id));
  }

  /**
   * Get grid id
   *
   * @returns {string}
   */
  getId(): string {
    return this.id;
  }

  /**
   * Get grid container
   *
   * @returns {jQuery}
   */
  getContainer(): JQuery {
    return this.$container;
  }

  /**
   * Get grid header container
   *
   * @returns {jQuery}
   */
  getHeaderContainer(): JQuery {
    return this.$container.closest(GridMap.gridPanel).find(GridMap.gridHeader);
  }

  /**
   * Extend grid with external extensions
   *
   * @param {object} extension
   */
  addExtension(extension: GridExtension): void {
    extension.extend(this);
  }
}
