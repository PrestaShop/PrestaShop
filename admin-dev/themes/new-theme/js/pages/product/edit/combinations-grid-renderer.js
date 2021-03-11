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

const {$} = window;

/**
 * Renders the list of combinations in product edit page
 */
export default class CombinationsGridRenderer {
  /**
   * @returns {{render: (function(*=): void)}}
   */
  constructor() {
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.$combinationsTable = $(ProductMap.combinations.combinationsTable);
    this.$combinationsTableBody = $(ProductMap.combinations.combinationsTableBody);
    this.prototypeTemplate = this.$combinationsTable.data('prototype');
    this.prototypeName = this.$combinationsTable.data('prototypeName');

    return {
      render: (data) => this.render(data),
    };
  }

  /**
   * @param {Object} data expected structure: {combinations: [{Object}, {Object}...], total: {Number}}
   */
  render(data) {
    this.renderCombinations(data.combinations);
  }

  /**
   * @todo: handle on change of new inputs of price quantity and is_default when they are ready
   * @todo: they should call related endpoints to update combination
   * @param {Array} combinations
   *
   * @private
   */
  renderCombinations(combinations) {
    this.$combinationsTableBody.empty();

    let rowIndex = 0;
    combinations.forEach((combination) => {
      const row = this.getPrototypeRow(rowIndex);
      this.$combinationsTableBody.append(row);

      // fill inputs
      const $combinationIdInput = $(ProductMap.combinations.tableRow.combinationIdInput(rowIndex));
      const $combinationNameInput = $(ProductMap.combinations.tableRow.combinationNameInput(rowIndex));
      const $finalPriceInput = $(ProductMap.combinations.tableRow.finalPriceTeInput(rowIndex));
      $combinationIdInput.val(combination.id);
      $combinationNameInput.val(combination.name);
      $finalPriceInput.val(combination.finalPriceTe);

      // This adds a text after the cell children (do not use text which replaces everything)
      $combinationIdInput.closest('td').append(combination.id);
      $combinationNameInput.closest('td').append(combination.name);
      $finalPriceInput.closest('td').append(combination.finalPriceTe);

      $(ProductMap.combinations.tableRow.impactOnPriceInput(rowIndex)).val(combination.impactOnPrice);
      $(ProductMap.combinations.tableRow.quantityInput(rowIndex)).val(combination.quantity);
      $(ProductMap.combinations.tableRow.isDefaultInput(rowIndex)).val(combination.isDefault);
      $(ProductMap.combinations.tableRow.editButton(rowIndex)).data('id', combination.id);
      $(ProductMap.combinations.tableRow.deleteButton(rowIndex)).data('id', combination.id);
      rowIndex += 1;
    });
  }

  /**
   * @param {Number} rowIndex
   *
   * @returns {String}
   *
   * @private
   */
  getPrototypeRow(rowIndex) {
    return this.prototypeTemplate.replace(new RegExp(this.prototypeName, 'g'), rowIndex);
  }
}
