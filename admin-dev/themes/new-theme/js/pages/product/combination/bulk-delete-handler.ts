import {ConfirmModal, IframeModal} from '@components/modal';
import ProductMap from '@pages/product/product-map';
import ProductEvents from '@pages/product/product-event-map';
import CombinationsService from '@pages/product/services/combinations-service';
import BulkChoicesSelector from '@pages/product/combination/bulk-choices-selector';

const CombinationMap = ProductMap.combinations;


export default class BulkDeleteHandler {
  readonly productId: number;

  private combinationsService: CombinationsService;

  private tabContainer!: HTMLDivElement;

  private bulkChoicesSelector: BulkChoicesSelector;

  constructor(productId: number) {
    this.productId = productId;
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

    bulkDeleteBtn.addEventListener('click', (e) => {
      const modal = new ConfirmModal(
        {
          id: 'bulk-delete-confirm-modal',
        },
        () => this.bulkDelete(),
      );

      modal.show();
    });
  }

  private bulkDelete(): void {
    const combinationIds: number[] = [];
    this.bulkChoicesSelector.getSelectedCheckboxes().forEach((checkbox: HTMLInputElement) => {
      combinationIds.push(Number(checkbox.value));
    });

    //@todo call combination service
    console.log(combinationIds);
  }
}
