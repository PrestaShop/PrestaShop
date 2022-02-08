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

import ConfirmModal from '@components/modal';
import ProductMap from '@pages/product/product-map';
import ComponentsMap from '@components/components-map';
import ProductEvents from '@pages/product/product-event-map';
import CombinationsService from '@pages/product/services/combinations-service';
import {EventEmitter} from 'events';
import DisablingToggler from '@components/form/disabling-toggler';

const CombinationMap = ProductMap.combinations;
const CombinationEvents = ProductEvents.combinations;

export default class BulkFormHandler {
  private combinationsService: CombinationsService;

  private eventEmitter: EventEmitter;

  private tabContainer: HTMLDivElement;

  constructor() {
    this.combinationsService = new CombinationsService();
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.tabContainer = document.querySelector(CombinationMap.externalCombinationTab) as HTMLDivElement;
    this.init();
  }

  private init() {
    this.listenSelections();
    this.eventEmitter.on(CombinationEvents.listRendered, () => this.toggleBulkAvailability());

    const bulkFormBtn = document.querySelector(CombinationMap.bulkCombinationFormBtn) as HTMLButtonElement;
    bulkFormBtn.addEventListener('click', () => this.showFormModal());
  }

  private showProgressModal() {
    //@todo: Replace with new progress modal introduced in #26004.
    const modal = new ConfirmModal(
      {
        id: CombinationMap.bulkProgressModalId,
        confirmMessage: '<div>Updating combinations: <p class="progress-increment"></p></div>',
        closable: true,
      },
      () => null,
    );

    modal.show();

    return modal;
  }

  private showFormModal() {
    const template = document.querySelector(CombinationMap.bulkCombinationFormTemplate) as HTMLScriptElement;
    const content = template.innerHTML;

    const modal = new ConfirmModal(
      {
        id: CombinationMap.bulkCombinationModalId,
        confirmMessage: content,
      },
      () => this.submitForm(),
    );

    modal.show();
    const form = document.querySelector(CombinationMap.bulkCombinationForm) as HTMLFormElement;
    const disablingToggles = form.querySelectorAll(ComponentsMap.disablingToggle.wrapper) as NodeListOf<HTMLDivElement>;

    disablingToggles.forEach((element) => {
      const {disablingToggleName} = element.dataset;
      new DisablingToggler(
        `${CombinationMap.bulkCombinationForm} [data-disabling-toggle-name="${disablingToggleName}"] input`,
        '0',
        `${CombinationMap.bulkCombinationForm} [data-toggled-by="${disablingToggleName}"]`,
      );
    });
  }

  private listenSelections(): void {
    this.tabContainer.addEventListener('change', (e) => {
      if (!(e.target instanceof HTMLInputElement)) {
        return;
      }

      if (e.target.id === CombinationMap.bulkSelectAllInPageId) {
        this.checkAll(e.target.checked);
      }
      this.toggleBulkAvailability();
    });
  }

  private checkAll(checked: boolean) {
    const allCheckboxes = this.tabContainer
      .querySelectorAll(CombinationMap.tableRow.isSelectedCombination) as NodeListOf<HTMLInputElement>;

    allCheckboxes.forEach((checkbox) => {
      // eslint-disable-next-line no-param-reassign
      checkbox.checked = checked;
    });
  }

  private toggleBulkAvailability() {
    const selectAllCheckbox = document.getElementById(CombinationMap.bulkSelectAllInPageId);
    const btn = this.tabContainer.querySelector(CombinationMap.bulkCombinationFormBtn) as HTMLButtonElement;
    const enable = (selectAllCheckbox instanceof HTMLInputElement && selectAllCheckbox.checked)
      || this.getSelectedCheckboxes().length !== 0;

    btn.toggleAttribute('disabled', !enable);
  }

  private async submitForm() {
    const form = document.querySelector(CombinationMap.bulkCombinationForm) as HTMLFormElement;
    const progressModal = this.showProgressModal();

    const checkboxes = this.getSelectedCheckboxes();
    const progressModalElement = document.getElementById(CombinationMap.bulkProgressModalId);

    let progress = 1;

    for (let i = 0; i < checkboxes.length; i += 1) {
      const checkbox = checkboxes[i];
      // eslint-disable-next-line no-await-in-loop
      await this.combinationsService.bulkUpdate(Number(checkbox.value), $(form).serializeArray());

      const progressContent = progressModalElement?.querySelector('.progress-increment') as HTMLParagraphElement;
      progressContent.innerHTML = String(progress);
      //@todo: also related with temporary progress modal. Needs to be fixed according to new progress modal once its merged
      progress += 1;
    }

    progressModal.hide();

    this.eventEmitter.emit(CombinationEvents.bulkUpdateFinished);
  }

  private getSelectedCheckboxes(): NodeListOf<HTMLInputElement> {
    return this.tabContainer
      .querySelectorAll(`${CombinationMap.tableRow.isSelectedCombination}:checked`) as NodeListOf<HTMLInputElement>;
  }
}
