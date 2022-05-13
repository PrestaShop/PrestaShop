/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

import ProductMap from '@pages/product/product-map';
import ProductEventMap from '@pages/product/product-event-map';
import {EventEmitter} from 'events';
import CombinationsService from '@pages/product/services/combinations-service';
import ConfirmModal from '@components/modal/confirm-modal';

const {$} = window;
const CombinationsMap = ProductMap.combinations;
const CombinationEvents = ProductEventMap.combinations;

/**
 * This components handles the row deletion of the combination list.
 */
export default class RowDeleteHandler {
  private eventEmitter: EventEmitter;

  private combinationsService: CombinationsService;

  constructor(
    eventEmitter: EventEmitter,
    combinationsService: CombinationsService,
  ) {
    this.eventEmitter = eventEmitter;
    this.combinationsService = combinationsService;

    const $combinationsFormContainer = $(CombinationsMap.combinationsFormContainer);
    $combinationsFormContainer.on('click', CombinationsMap.deleteCombinationSelector, async (e) => {
      await this.deleteCombination(e.currentTarget);
    });
  }

  /**
   * @param {HTMLElement} button
   *
   * @private
   */
  private async deleteCombination(button: HTMLButtonElement): Promise<void> {
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
        const response = await this.combinationsService.deleteCombination(
          this.findCombinationId(button),
        );
        $.growl({message: response.message});
        this.eventEmitter.emit(CombinationEvents.combinationDeleted);
      });
      modal.show();
    } catch (error) {
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
