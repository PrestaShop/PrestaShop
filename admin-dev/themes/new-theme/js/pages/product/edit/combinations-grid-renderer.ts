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
import ProductEventMap from '@pages/product/product-event-map';
import {EventEmitter} from 'events';
import ProductFormModel from '@pages/product/edit/product-form-model';

const {$} = window;

/**
 * Renders the list of combinations in product edit page
 */
export default class CombinationsGridRenderer {
  eventEmitter: EventEmitter;

  $loadingSpinner: JQuery;

  prototypeTemplate: string;

  prototypeName: string;

  productFormModel: ProductFormModel;

  constructor(productFormModel: ProductFormModel) {
    this.productFormModel = productFormModel;
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.$loadingSpinner = $(ProductMap.combinations.loadingSpinner);

    // We can't keep a reference on the table (or its content) since it can be updated via ajax, we always get it just in time
    const $combinationsTable = $(ProductMap.combinations.combinationsTable);
    this.prototypeTemplate = $combinationsTable.data('prototype');
    this.prototypeName = $combinationsTable.data('prototypeName');
  }

  /**
   * @param {Object} data expected structure: {combinations: [{Object}, {Object}...], total: {Number}}
   */
  render(data: Record<string, any>): void {
    this.renderCombinations(data.combinations);
  }

  /**
   * @param {Boolean} loading
   */
  toggleLoading(loading: boolean): void {
    this.$loadingSpinner.toggle(loading);
  }

  getFormData(): FormData {
    const combinationListForm = document.querySelector<HTMLFormElement>(ProductMap.combinations.list.form);

    if (!combinationListForm) {
      return new FormData();
    }

    return new FormData(combinationListForm);
  }

  /**
   * @param {Array} combinations
   *
   * @private
   */
  private renderCombinations(combinations: Array<Record<string, any>>): void {
    const $combinationsTable = $(ProductMap.combinations.combinationsTable);
    const $combinationsTableBody = $(ProductMap.combinations.combinationsTableBody);

    $combinationsTableBody.empty();
    $combinationsTable.find(ProductMap.combinations.bulkSelectAll).prop('checked', false);

    let rowIndex = 0;
    combinations.forEach((combination: Record<string, any>) => {
      const $row = $(this.getPrototypeRow(rowIndex, combination));
      // JS event to allow external module to change the row, add listeners, ... before it is added
      this.eventEmitter.emit(ProductEventMap.combinations.buildCombinationRow, {combination, $row});

      $combinationsTableBody.append($row);
      rowIndex += 1;
    });

    this.eventEmitter.emit(ProductEventMap.combinations.listRendered);
  }

  private getPrototypeRow(rowIndex: number, combination: Record<string, any>): string {
    let rowTemplate: string = this.prototypeTemplate.replace(new RegExp(this.prototypeName, 'g'), rowIndex.toString());
    Object.keys(combination).forEach((field: string) => {
      if (typeof combination[field] === 'boolean') {
        rowTemplate = rowTemplate.replace(new RegExp(`__${field}__`, 'g'), (combination[field] ? '1' : '0'));
      } else {
        rowTemplate = rowTemplate.replace(new RegExp(`__${field}__`, 'g'), combination[field]);
      }
    });

    return rowTemplate;
  }
}
