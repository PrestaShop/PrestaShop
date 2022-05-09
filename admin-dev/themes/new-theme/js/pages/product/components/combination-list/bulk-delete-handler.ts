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

import {ConfirmModal} from '@components/modal';
import ProductMap from '@pages/product/product-map';
import ProductEvents from '@pages/product/product-event-map';
import CombinationsService from '@pages/product/services/combinations-service';
import BulkChoicesSelector from '@pages/product/components/combination-list/bulk-choices-selector';
import {EventEmitter} from 'events';

const CombinationMap = ProductMap.combinations;
const CombinationEvents = ProductEvents.combinations;

/**
 * This components handles the bulk deletion of the combination list.
 */
export default class BulkDeleteHandler {
  private readonly productId: number;

  private readonly eventEmitter: EventEmitter;

  private readonly combinationsService: CombinationsService;

  private readonly bulkChoicesSelector: BulkChoicesSelector;

  constructor(
    productId: number,
    eventEmitter: EventEmitter,
    bulkChoicesSelector: BulkChoicesSelector,
    combinationsService: CombinationsService,
  ) {
    this.productId = productId;
    this.eventEmitter = eventEmitter;
    this.combinationsService = combinationsService;
    this.bulkChoicesSelector = bulkChoicesSelector;

    this.init();
  }

  private init(): void {
    const bulkDeleteBtn = document.querySelector<HTMLButtonElement>(CombinationMap.bulkDeleteBtn);

    if (!(bulkDeleteBtn instanceof HTMLButtonElement)) {
      console.error(`${CombinationMap.bulkDeleteBtn} must be a HTMLButtonElement`);

      return;
    }

    bulkDeleteBtn.addEventListener('click', () => {
      try {
        const selectedCombinationsCount = this.bulkChoicesSelector.getSelectedCheckboxes().length;
        const confirmLabel = bulkDeleteBtn.dataset.modalConfirmLabel
          ?.replace(/%combinations_number%/, String(selectedCombinationsCount));

        const modal = new ConfirmModal(
          {
            id: 'modal-confirm-delete-combinations',
            confirmTitle: bulkDeleteBtn.innerHTML,
            confirmMessage: bulkDeleteBtn.dataset.modalMessage,
            confirmButtonLabel: confirmLabel,
            closeButtonLabel: bulkDeleteBtn.dataset.modalCancelLabel,
            closable: true,
          },
          async () => {
            const response = await this.bulkDelete();
            $.growl({message: response.message});
            this.eventEmitter.emit(CombinationEvents.refreshCombinationList);
          },
        );
        modal.show();
      } catch (error) {
        const errorMessage = error.response?.JSON ?? error;
        $.growl.error({message: errorMessage});
      }
    });
  }

  /**
   * @todo: the bulk delete action should be displayed with a progress modal once it is ready.
   */
  private bulkDelete(): JQuery.jqXHR {
    const combinationIds: number[] = [];
    this.bulkChoicesSelector.getSelectedCheckboxes().forEach((checkbox: HTMLInputElement) => {
      combinationIds.push(Number(checkbox.value));
    });

    return this.combinationsService.bulkDeleteCombinations(this.productId, combinationIds);
  }
}
