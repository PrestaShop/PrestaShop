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

import ComponentsMap from '@components/components-map';
import ProductMap from '@pages/product/product-map';
import ProductEventMap from '@pages/product/product-event-map';
import {EventEmitter} from 'events';

const {$} = window;

/**
 * Renders the list of combinations in product edit page
 */
export default class CombinationsGridRenderer {
  eventEmitter: EventEmitter;

  $combinationsTable: JQuery;

  $combinationsTableBody: JQuery;

  $loadingSpinner: JQuery;

  prototypeTemplate: string;

  prototypeName: string;

  /**
   * @returns {{render: (function(*=): void)}}
   */
  constructor() {
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.$combinationsTable = $(ProductMap.combinations.combinationsTable);
    this.$combinationsTableBody = $(ProductMap.combinations.combinationsTableBody);
    this.$loadingSpinner = $(ProductMap.combinations.loadingSpinner);
    this.prototypeTemplate = this.$combinationsTable.data('prototype');
    this.prototypeName = this.$combinationsTable.data('prototypeName');
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

  /**
   * @param {Array} combinations
   *
   * @private
   */
  private renderCombinations(combinations: Array<Record<string, any>>): void {
    this.$combinationsTableBody.empty();
    this.$combinationsTable.find(ProductMap.combinations.bulkSelectAllInPage).prop('checked', false);

    let rowIndex = 0;
    combinations.forEach((combination: Record<string, any>) => {
      const $row = $(this.getPrototypeRow(rowIndex));

      // fill inputs
      const $combinationCheckbox = $(ProductMap.combinations.tableRow.combinationCheckbox(rowIndex), $row);
      const $combinationIdInput = $(ProductMap.combinations.tableRow.combinationIdInput(rowIndex), $row);
      const $combinationNameInput = $(ProductMap.combinations.tableRow.combinationNameInput(rowIndex), $row);
      const $quantityInput = $(ProductMap.combinations.tableRow.quantityInput(rowIndex), $row);
      const $impactOnPriceInput = $(ProductMap.combinations.tableRow.impactOnPriceInput(rowIndex), $row);
      const $deltaQuantityContainer = $(ProductMap.combinations.tableRow.deltaQuantityWrapper, $row);
      const $referenceInput = $(ProductMap.combinations.tableRow.referenceInput(rowIndex), $row);
      // @todo final price should be calculated based on price impact and product price,
      //    so it doesnt need to be in api response
      const $finalPriceInput = $(ProductMap.combinations.tableRow.finalPriceTeInput(rowIndex), $row);
      $combinationIdInput.val(combination.id);
      $combinationCheckbox.val(combination.id);
      $combinationNameInput.val(combination.name);
      // This adds the ID in the checkbox label
      $combinationCheckbox.closest('label').append(combination.id);
      // This adds a text after the cell children (do not use text which replaces everything)
      $combinationNameInput.closest('td').append(combination.name);
      $finalPriceInput.closest('td').append(combination.finalPriceTe);
      $referenceInput.val(combination.reference);
      $referenceInput.data('initial-value', combination.reference);
      $quantityInput.val(combination.quantity);
      $quantityInput.data('initial-value', combination.quantity);
      $quantityInput.find(ComponentsMap.deltaQuantityInput.initialQuantityPreviewSelector).text(combination.quantity);
      $deltaQuantityContainer.data('initial-quantity', combination.quantity);
      $impactOnPriceInput.val(combination.impactOnPrice);
      $impactOnPriceInput.data('initial-value', combination.impactOnPrice);
      $(ProductMap.combinations.tableRow.editButton(rowIndex), $row).data('id', combination.id);
      $(ProductMap.combinations.tableRow.deleteButton(rowIndex), $row).data('id', combination.id);
      $(ProductMap.combinations.tableRow.combinationImg, $row)
        .attr('src', combination.imageUrl)
        .attr('alt', combination.name);

      if (combination.isDefault) {
        $(ProductMap.combinations.tableRow.isDefaultInput(rowIndex), $row).prop('checked', true);
      }

      this.$combinationsTableBody.append($row);
      rowIndex += 1;
    });
    this.eventEmitter.emit(ProductEventMap.combinations.listRendered);
  }

  /**
   * @param {Number} rowIndex
   *
   * @returns {String}
   *
   * @private
   */
  private getPrototypeRow(rowIndex: number): string {
    return this.prototypeTemplate.replace(new RegExp(this.prototypeName, 'g'), rowIndex.toString());
  }
}
