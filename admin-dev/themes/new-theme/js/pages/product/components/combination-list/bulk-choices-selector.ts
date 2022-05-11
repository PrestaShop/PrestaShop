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
import ProductEvents from '@pages/product/product-event-map';
import {EventEmitter} from 'events';
import PaginatedCombinationsService from '@pages/product/services/paginated-combinations-service';

const CombinationMap = ProductMap.combinations;
const CombinationEvents = ProductEvents.combinations;

/**
 * This component watches the changes on row checkboxes and the select all one thus allowing the components that need
 * the selected rows to interrogate this component via getSelectedCheckboxes.
 */
export default class BulkChoicesSelector {
  private eventEmitter: EventEmitter;

  private tabContainer: HTMLDivElement;

  private paginatedCombinationsService: PaginatedCombinationsService

  private allCombinationsCount: number;

  private paginatedCombinationsCount: number;

  constructor(
    eventEmitter: EventEmitter,
    tabContainer: HTMLDivElement,
    paginatedCombinationsService: PaginatedCombinationsService,
  ) {
    this.eventEmitter = eventEmitter;
    this.tabContainer = tabContainer;
    this.paginatedCombinationsService = paginatedCombinationsService;
    this.allCombinationsCount = 0;
    this.paginatedCombinationsCount = 0;
    this.init();
  }

  //@todo: may become private or unused
  public getSelectedCheckboxes(): NodeListOf<HTMLInputElement> {
    return this.tabContainer.querySelectorAll<HTMLInputElement>(`${CombinationMap.tableRow.isSelectedCombination}:checked`);
  }

  //@todo: could as well return count?
  public async getSelectedIds(): Promise<number[]> {
    const allSelected = this.tabContainer.querySelector<HTMLInputElement>(`${CombinationMap.bulkSelectAll}:checked`);

    if (allSelected) {
      const response: JQuery.jqXHR = await this.paginatedCombinationsService.getCombinationIds();

      return response;
    }

    const combinationIds: number[] = [];
    this.getSelectedCheckboxes().forEach((checkbox: HTMLInputElement) => {
      combinationIds.push(Number(checkbox.value));
    });

    return combinationIds;
  }

  private init() {
    this.eventEmitter.on(CombinationEvents.listRendered, () => {
      this.resetCheckAll();
      this.updateBulkButtonsState();
      this.refreshSelectableCombinationsCount().then(() => {
        this.listenCheckboxesChange();
        const bulkSelectedAll = this.tabContainer
          .querySelector<HTMLInputElement>(`${CombinationMap.bulkSelectAllCheckboxes}:checked`);
        this.checkAll(!!bulkSelectedAll);
        this.updateBulkSelectAllState();
      });
    });
  }

  /**
   * Delegated event listener on tabContainer, because every checkbox is re-rendered with dynamic pagination
   */
  private listenCheckboxesChange(): void {
    this.tabContainer.addEventListener('change', (e) => {
      const checkbox = e.target;

      if (!(checkbox instanceof HTMLInputElement)) {
        return;
      }

      const isBulkSelectAll = checkbox.matches(`${CombinationMap.bulkSelectAllCheckboxes}`);

      // don't proceed if its not one of the expected checkboxes
      if (!isBulkSelectAll && !checkbox.matches(CombinationMap.tableRow.isSelectedCombination)) {
        return;
      }

      if (isBulkSelectAll) {
        const bulkSelectAllCheckboxes = this.tabContainer.querySelectorAll(CombinationMap.bulkSelectAllCheckboxes);
        //@todo: this loop allows only checking one checkbox at a time, but it seems too complicated
        //       need to check refactoring options, html could probably handle this by its own
        bulkSelectAllCheckboxes.forEach((input) => {
          if (checkbox.id !== input.id) {
            if (input instanceof HTMLInputElement) {
              // eslint-disable-next-line no-param-reassign
              input.checked = false;
            }
          }
        });
        this.checkAll(checkbox.checked);
      } else {
        this.resetCheckAll();
      }

      this.updateBulkButtonsState();
    });
  }

  private async updateBulkButtonsState(): Promise<void> {
    const dropdownBtn = this.tabContainer.querySelector<HTMLInputElement>(CombinationMap.bulkActionsDropdownBtn);
    const selectedCombinationIds = await this.getSelectedIds();

    const selectedCombinationsCount = selectedCombinationIds.length;
    const bulkActionButtons = this.tabContainer.querySelectorAll<HTMLButtonElement>(CombinationMap.bulkActionBtn);

    bulkActionButtons.forEach((button: HTMLButtonElement) => {
      const label = button.dataset.btnLabel;

      if (!label) {
        console.error('Attribute "data-btn-label" is not defined for combinations bulk action button');
        return;
      }

      // eslint-disable-next-line no-param-reassign
      button.innerHTML = label.replace(/%combinations_number%/, String(selectedCombinationsCount));
      button?.toggleAttribute('disabled', !selectedCombinationsCount);
    });

    dropdownBtn?.toggleAttribute('disabled', !selectedCombinationsCount);
  }

  private checkAll(checked: boolean): void {
    const allDisplayCheckbox = this.tabContainer.querySelector<HTMLInputElement>(CombinationMap.bulkSelectAllDisplay);

    if (allDisplayCheckbox instanceof HTMLInputElement) {
      allDisplayCheckbox.checked = checked;
    }

    const allCheckboxes = this.tabContainer.querySelectorAll<HTMLInputElement>(CombinationMap.tableRow.isSelectedCombination);

    allCheckboxes.forEach((checkbox: HTMLInputElement) => {
      // eslint-disable-next-line no-param-reassign
      checkbox.checked = checked;
    });
  }

  /**
   * Uncheck the "select all" checkboxes, so component acts as a simple manual selection
   * (e.g. when user "selects all", but then unchecks one of checkboxes)
   *
   * @private
   */
  private resetCheckAll(): void {
    const allDisplayCheckbox = this.tabContainer.querySelector<HTMLInputElement>(CombinationMap.bulkSelectAllDisplay);

    if (allDisplayCheckbox instanceof HTMLInputElement) {
      allDisplayCheckbox.checked = false;
    }

    const bulkSelectAllCheckboxes = this.tabContainer.querySelectorAll<HTMLInputElement>(CombinationMap.bulkSelectAllCheckboxes);

    bulkSelectAllCheckboxes.forEach((checkbox: HTMLInputElement) => {
      // eslint-disable-next-line no-param-reassign
      checkbox.checked = false;
    });
  }

  private updateBulkSelectAllState(): void {
    const bulkSelectAllInputs = this.tabContainer
      .querySelectorAll(CombinationMap.bulkSelectAllCheckboxes);

    bulkSelectAllInputs.forEach((input) => {
      if (!input) {
        console.error(`Input ${CombinationMap.bulkSelectAll} not found`);
        return;
      }

      const labelElement = input.parentNode?.querySelector<HTMLLabelElement>(`label[for=${input.id}]`);

      if (!labelElement) {
        console.error('Missing <label> for bulk all selection checkbox');
        return;
      }

      const {label} = labelElement.dataset;

      if (!label) {
        console.error(`Attribute "data-label" is not defined on one of ${CombinationMap.bulkSelectAllCheckboxes}`);
        return;
      }

      labelElement.innerHTML = label.replace(
        /%combinations_number%/,
        String(input.matches(CombinationMap.bulkSelectAll) ? this.allCombinationsCount : this.paginatedCombinationsCount),
      );
    });
  }

  private async refreshSelectableCombinationsCount(): Promise<void> {
    //@todo: endpoint could return count to make it more performant (especially when selecting across all pages)
    await this.paginatedCombinationsService.getCombinationIds().then((combinationIds) => {
      this.allCombinationsCount = combinationIds.length;
    });

    const selectableCheckboxes = this.tabContainer
      .querySelectorAll<HTMLInputElement>(`${CombinationMap.tableRow.isSelectedCombination}`);

    this.paginatedCombinationsCount = selectableCheckboxes.length;
  }
}
