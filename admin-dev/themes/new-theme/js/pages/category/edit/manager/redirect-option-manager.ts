/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import EntitySearchInput from '@components/entity-search-input';
import {EventEmitter} from 'events';
import CategoryMap from '@pages/category/category-map';

const {$} = window;

/**
 * This component is used in category page to selected where the redirection points to when the
 * category is disabled. It is composed on two inputs:
 * - a selection of the redirection type
 * - a rich component to select a category
 */
export default class RedirectOptionManager {
  eventEmitter: EventEmitter;

  $redirectTypeInput: JQuery;

  $redirectTargetInput: JQuery;

  $redirectTargetRow: JQuery;

  entitySearchInput!: EntitySearchInput;

  /**
   * @param {EventEmitter} eventEmitter
   */
  constructor(eventEmitter: EventEmitter) {
    this.eventEmitter = eventEmitter;
    this.$redirectTypeInput = $(CategoryMap.redirectOption.typeInput);
    this.$redirectTargetInput = $(CategoryMap.redirectOption.targetInput);

    // Target only inputs present in the redirect target row
    this.$redirectTargetRow = this.$redirectTargetInput.closest(CategoryMap.redirectOption.groupSelector);

    if (this.$redirectTargetInput.length) {
      this.entitySearchInput = new EntitySearchInput(this.$redirectTargetInput, {});
      this.watchRedirectType();
    }
  }

  /**
   * Watch the selected redirection type and adapt the inputs accordingly.
   *
   * @private
   */
  private watchRedirectType(): void {
    this.$redirectTypeInput.on('change', () => {
      const redirectType = this.$redirectTypeInput.val();

      switch (redirectType) {
        case '301':
        case '302':
          this.entitySearchInput.setValues([]);
          this.showTarget();
          break;
        case '404':
        case '410':
        default:
          this.entitySearchInput.setValues([]);
          this.hideTarget();
          break;
      }
    });
  }

  private showTarget(): void {
    this.$redirectTargetRow.removeClass('d-none');
  }

  private hideTarget(): void {
    this.$redirectTargetRow.addClass('d-none');
  }
}
