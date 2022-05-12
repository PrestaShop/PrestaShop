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
import DynamicPaginator from '@components/pagination/dynamic-paginator';

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

  private paginator: DynamicPaginator;

  constructor(
    eventEmitter: EventEmitter,
    tabContainer: HTMLDivElement,
    paginatedCombinationsService: PaginatedCombinationsService,
    paginator: DynamicPaginator,
  ) {
    this.eventEmitter = eventEmitter;
    this.tabContainer = tabContainer;
    this.paginatedCombinationsService = paginatedCombinationsService;
    this.paginator = paginator;

    this.init();
  }

  public async getSelectedIds(): Promise<number[]> {
    const allSelected = this.tabContainer.querySelector<HTMLInputElement>(`${CombinationMap.bulkSelectAll}:checked`);

    if (allSelected) {
      const response: JQuery.jqXHR = await this.paginatedCombinationsService.getCombinationIds();

      return response;
    }

    const combinationIds: number[] = [];
    const selectedCheckboxes = this.tabContainer
      .querySelectorAll<HTMLInputElement>(`${CombinationMap.tableRow.isSelectedCombination}:checked`);

    selectedCheckboxes.forEach((checkbox: HTMLInputElement) => {
      combinationIds.push(Number(checkbox.value));
    });

    return combinationIds;
  }

  /**
   * Uncheck the "select all" checkboxes, so component acts as a simple manual selection
   * Does not uncheck all combinations (just bulk selections)!
   *
   * (e.g. when user "selects all", but then unchecks one of checkboxes)
   */
  public uncheckBulkAllSelection(): void {
    const bulkSelectAllCheckboxes = this.tabContainer.querySelectorAll<HTMLInputElement>(CombinationMap.commonBulkAllSelector);

    bulkSelectAllCheckboxes.forEach((checkbox: HTMLInputElement) => {
      // eslint-disable-next-line no-param-reassign
      checkbox.checked = false;
    });
  }

  private init() {
    this.eventEmitter.on(CombinationEvents.listRendered, () => {
      this.listenCheckboxesChange();
      this.uncheckBulkAllSelection();
      this.updateBulkAllSelectionLabels();
      this.updateBulkActionButtons();
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

      const isBulkSelectAll = checkbox.matches(`${CombinationMap.bulkSelectAll}, ${CombinationMap.bulkSelectAllInPage}`);

      // don't proceed if its not one of the expected checkboxes
      if (!isBulkSelectAll && !checkbox.matches(CombinationMap.tableRow.isSelectedCombination)) {
        return;
      }

      if (isBulkSelectAll) {
        const bulkSelectAllCheckboxes = this.tabContainer.querySelectorAll(CombinationMap.commonBulkAllSelector);
        // this makes sure that only one checkbox is checked at a time.
        // Radio buttons are not an option, because we also need to uncheck the box once user clicks on the checked one.
        bulkSelectAllCheckboxes.forEach((input) => {
          if (checkbox.id !== input.id && input instanceof HTMLInputElement) {
            // eslint-disable-next-line no-param-reassign
            input.checked = false;
          }
        });
        this.checkAllCombinations(checkbox.checked);
      } else {
        this.uncheckBulkAllSelection();
      }

      this.updateBulkActionButtons();
    });
  }

  private async updateBulkActionButtons(): Promise<void> {
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

  private updateBulkAllSelectionLabels(): void {
    const bulkSelectAllInputs = this.tabContainer
      .querySelectorAll(`${CombinationMap.bulkSelectAll}, ${CombinationMap.bulkSelectAllInPage}`);

    bulkSelectAllInputs.forEach((input) => {
      if (!(input instanceof HTMLInputElement)) {
        console.error(`Input ${CombinationMap.bulkSelectAll} not found`);
        return;
      }

      const labelElement = input.parentNode?.querySelector<HTMLLabelElement>(`label[for=${input.id}]`);

      if (!labelElement) {
        console.error('Missing <label> for bulk all selection input');
        return;
      }

      const {label} = labelElement.dataset;

      if (!label) {
        console.error('Attribute "data-label" is not defined on bulk selection label');
        return;
      }

      labelElement.innerHTML = label.replace(
        /%combinations_number%/,
        String(input.matches(CombinationMap.bulkSelectAll) ? this.paginator.getTotal() : this.paginator.getTotalInPage()),
      );
    });
  }

  private checkAllCombinations(checked: boolean): void {
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
}
