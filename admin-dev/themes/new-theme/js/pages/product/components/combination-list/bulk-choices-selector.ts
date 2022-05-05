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
import {isChecked} from '@PSTypes/typeguard';

const CombinationMap = ProductMap.combinations;
const CombinationEvents = ProductEvents.combinations;

/**
 * This component watches the changes on row checkboxes and the select all one thus allowing the components that need
 * the selected rows to interrogate this component via getSelectedCheckboxes.
 */
export default class BulkChoicesSelector {
  private eventEmitter: EventEmitter;

  private tabContainer: HTMLDivElement;

  constructor(eventEmitter: EventEmitter, tabContainer: HTMLDivElement) {
    this.eventEmitter = eventEmitter;
    this.tabContainer = tabContainer;
    this.init();
  }

  public getSelectedCheckboxes(): NodeListOf<HTMLInputElement> {
    return this.tabContainer.querySelectorAll<HTMLInputElement>(`${CombinationMap.tableRow.isSelectedCombination}:checked`);
  }

  private init() {
    this.listenCheckboxesChange();
    this.eventEmitter.on(CombinationEvents.listRendered, () => this.updateBulkButtonsState());
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

      const isBulkSelectAll = checkbox.matches(CombinationMap.bulkSelectAll);

      // don't proceed if its not one of the expected checkboxes
      if (!isBulkSelectAll && !checkbox.matches(CombinationMap.tableRow.isSelectedCombination)) {
        return;
      }

      if (isBulkSelectAll) {
        this.checkAll(checkbox.checked);
      }

      this.updateBulkButtonsState();
    });
  }

  private updateBulkButtonsState(): void {
    const selectAllCheckbox = document.querySelector(CombinationMap.bulkSelectAll);
    const dropdownBtn = this.tabContainer.querySelector<HTMLInputElement>(CombinationMap.bulkActionsDropdownBtn);
    const selectedCombinationsCount = this.getSelectedCheckboxes().length;
    const enable = isChecked(selectAllCheckbox) || selectedCombinationsCount !== 0;

    const bulkActionButtons = this.tabContainer.querySelectorAll<HTMLButtonElement>(CombinationMap.bulkActionBtn);

    bulkActionButtons.forEach((button: HTMLButtonElement) => {
      const label = button.dataset.btnLabel;

      if (!label) {
        console.error('Attribute "data-btn-label" is not defined for combinations bulk action button');
        return;
      }

      // eslint-disable-next-line no-param-reassign
      button.innerHTML = label.replace(/%combinations_number%/, String(selectedCombinationsCount));
      button?.toggleAttribute('disabled', !enable);
    });

    dropdownBtn?.toggleAttribute('disabled', !enable);
  }

  private checkAll(checked: boolean): void {
    const allCheckboxes = this.tabContainer.querySelectorAll<HTMLInputElement>(CombinationMap.tableRow.isSelectedCombination);

    allCheckboxes.forEach((checkbox: HTMLInputElement) => {
      // eslint-disable-next-line no-param-reassign
      checkbox.checked = checked;
    });
  }
}
