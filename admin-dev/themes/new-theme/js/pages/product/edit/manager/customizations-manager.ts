/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {EventEmitter} from 'events';
import ProductMap from '@pages/product/product-map';
import ProductEventMap from '@pages/product/product-event-map';
import ConfirmModal from '@components/modal';

const {$} = window;

export default class CustomizationsManager {
  $customizationsContainer: JQuery;

  $customizationFieldsList: JQuery;

  eventEmitter: EventEmitter;

  prototypeTemplate: string;

  prototypeName: string;

  constructor() {
    this.$customizationsContainer = $(ProductMap.customizations.customizationsContainer);
    this.$customizationFieldsList = $(ProductMap.customizations.customizationFieldsList);
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.prototypeTemplate = this.$customizationFieldsList.data('prototype');
    this.prototypeName = this.$customizationFieldsList.data('prototypeName');

    this.init();
  }

  private init(): void {
    this.$customizationsContainer.on('click', ProductMap.customizations.addCustomizationBtn, () => {
      this.addCustomizationField();
    });
    this.$customizationsContainer.on('click', ProductMap.customizations.removeCustomizationBtn, (e) => {
      this.removeCustomizationField(e);
    });
  }

  private addCustomizationField(): void {
    // The container keeps track of the next index to use, we increment it right away
    const rowIndex = this.$customizationFieldsList.data('rowIndex');
    this.$customizationFieldsList.data('rowIndex', rowIndex + 1);

    const newItem = this.prototypeTemplate.replace(new RegExp(this.prototypeName, 'g'), rowIndex);

    this.$customizationFieldsList.append(newItem);
    window.prestaShopUiKit.initToolTips();
    const {translatableInput} = window.prestashop.instance;
    translatableInput.refreshFormInputs(this.$customizationsContainer.closest('form'));
    this.eventEmitter.emit(ProductEventMap.customizations.rowAdded, {index: rowIndex});
  }

  private removeCustomizationField(event: JQuery.ClickEvent): void {
    const $deleteButton = $(<HTMLElement>event.currentTarget);
    const modal = new (ConfirmModal as any)(
      {
        id: 'modal-confirm-delete-customization',
        confirmTitle: $deleteButton.data('modal-title'),
        confirmMessage: $deleteButton.data('modal-message'),
        confirmButtonLabel: $deleteButton.data('modal-apply'),
        closeButtonLabel: $deleteButton.data('modal-cancel'),
        confirmButtonClass: 'btn-danger',
        closable: true,
      },
      () => {
        $deleteButton
          .closest(ProductMap.customizations.customizationFieldRow)
          .remove();
        this.eventEmitter.emit(ProductEventMap.customizations.rowRemoved);
      },
    );
    modal.show();
  }
}
