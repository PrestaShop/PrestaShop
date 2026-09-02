/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ProductMap from '@pages/product/product-map';
import ProductEventMap from '@pages/product/product-event-map';
import {EventEmitter} from 'events';
import {deleteCombination} from '@pages/product/service/combination';
import ConfirmModal from '@components/modal/confirm-modal';

const {$} = window;
const CombinationsMap = ProductMap.combinations;
const CombinationEvents = ProductEventMap.combinations;

/**
 * This components handles the row deletion of the combination list.
 */
export default class RowDeleteHandler {
  private eventEmitter: EventEmitter;

  constructor(
    eventEmitter: EventEmitter,
  ) {
    this.eventEmitter = eventEmitter;

    const $combinationsFormContainer = $(CombinationsMap.combinationsFormContainer);
    $combinationsFormContainer.on('click', CombinationsMap.deleteCombinationSelector, async (e) => {
      await this.deleteCombination(e.currentTarget, false);
    });
    $combinationsFormContainer.on('click', CombinationsMap.deleteCombinationAllShopsSelector, async (e) => {
      await this.deleteCombination(e.currentTarget, true);
    });
  }

  /**
   * @param {HTMLElement} button
   * @param {boolean} allShops
   *
   * @private
   */
  private async deleteCombination(button: HTMLButtonElement, allShops: boolean): Promise<void> {
    try {
      const $deleteButton = $(button);
      const modal = new ConfirmModal({
        id: 'modal-confirm-delete-combination',
        confirmTitle: $deleteButton.data('modal-title'),
        confirmMessage: $deleteButton.data('modal-message'),
        confirmButtonLabel: $deleteButton.data('modal-apply'),
        closeButtonLabel: $deleteButton.data('modal-cancel'),
        confirmButtonClass: 'btn-danger',
        closable: true,
      },
      async () => {
        const response = await deleteCombination(
          this.findCombinationId(button),
          allShops ? null : <number> <unknown> button.dataset.shopId,
        );
        $.growl({message: response.message});
        this.eventEmitter.emit(CombinationEvents.combinationDeleted);
      });
      modal.show();
    } catch (error: any) {
      const errorMessage = error.response?.JSON ?? error;
      $.growl.error({message: errorMessage});
    }
  }

  /**
   * @param {HTMLElement} input of the same table row
   *
   * @returns {Number}
   *
   * @private
   */
  private findCombinationId(input: HTMLElement): number {
    return Number($(input)
      .closest('tr')
      .find(CombinationsMap.combinationIdInputsSelector)
      .val());
  }
}
