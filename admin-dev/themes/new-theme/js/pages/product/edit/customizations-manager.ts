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
    const index = this.getIndex();
    const newItem = this.prototypeTemplate.replace(new RegExp(this.prototypeName, 'g'), this.getIndex());

    this.$customizationFieldsList.append(newItem);
    window.prestaShopUiKit.initToolTips();
    const {translatableInput} = window.prestashop.instance;
    translatableInput.refreshFormInputs(this.$customizationsContainer.closest('form'));
    this.eventEmitter.emit(ProductEventMap.customizations.rowAdded, {index});
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

  private getIndex(): string {
    return this.$customizationFieldsList.find(ProductMap.customizations.customizationFieldRow).length.toString();
  }
}
