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

  readonly tabContainer!: HTMLDivElement;

  private eventEmitter: EventEmitter;

  private combinationsService: CombinationsService;

  private bulkChoicesSelector: BulkChoicesSelector;

  constructor(productId: number) {
    this.productId = productId;
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.combinationsService = new CombinationsService();
    this.tabContainer = document.querySelector<HTMLDivElement>(CombinationMap.externalCombinationTab)!;
    this.bulkChoicesSelector = new BulkChoicesSelector(this.tabContainer);

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
        const modal = new (ConfirmModal as any)(
          {
            id: 'modal-confirm-delete-combination',
            // @todo: add translation data attributes
            // confirmTitle: $deleteButton.data('modal-title'),
            // confirmMessage: $deleteButton.data('modal-message'),
            // confirmButtonLabel: $deleteButton.data('modal-apply'),
            // closeButtonLabel: $deleteButton.data('modal-cancel'),
            // confirmButtonClass: 'btn-danger',
            // closable: true,
          },
          async () => {
            const response = await this.bulkDelete();
            $.growl({message: response.message});
            this.eventEmitter.emit(CombinationEvents.refreshCombinationList);
          },
        );
        modal.show();
      } catch (error) {
        const errorMessage = error.responseJSON
          ? error.responseJSON.error
          : error;
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
