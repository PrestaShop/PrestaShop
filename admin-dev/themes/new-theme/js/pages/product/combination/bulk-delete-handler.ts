import {ConfirmModal} from '@components/modal';
import ProductMap from '@pages/product/product-map';
import ProductEvents from '@pages/product/product-event-map';
import CombinationsService from '@pages/product/services/combinations-service';
import BulkChoicesSelector from '@pages/product/combination/bulk-choices-selector';
import {EventEmitter} from 'events';

const CombinationMap = ProductMap.combinations;
const CombinationEvents = ProductEvents.combinations;

export default class BulkDeleteHandler {
  readonly productId: number;

  private eventEmitter: EventEmitter;

  private combinationsService: CombinationsService;

  private bulkChoicesSelector: BulkChoicesSelector;

  constructor(productId: number, bulkChoicesSelector: BulkChoicesSelector) {
    this.productId = productId;
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.combinationsService = new CombinationsService();
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

  private bulkDelete(): JQuery.jqXHR {
    const combinationIds: number[] = [];
    this.bulkChoicesSelector.getSelectedCheckboxes().forEach((checkbox: HTMLInputElement) => {
      combinationIds.push(Number(checkbox.value));
    });

    return this.combinationsService.bulkDeleteCombinations(this.productId, combinationIds);
  }
}
