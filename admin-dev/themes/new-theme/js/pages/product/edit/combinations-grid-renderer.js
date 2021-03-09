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
import Router from '@components/router';

const {$} = window;

export default class CombinationsGridRenderer {
  constructor() {
    this.router = new Router();
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.$combinationsTable = $(ProductMap.combinations.combinationsTable);
    this.$combinationsTableBody = $(ProductMap.combinations.combinationsTableBody);
    this.prototypeTemplate = this.$combinationsTable.data('prototype');
    this.prototypeName = this.$combinationsTable.data('prototypeName');
  }

  render(data) {
    this.renderCombinations(data.combinations);
  }

  renderCombinations(combinations) {
    this.$combinationsTableBody.empty();

    let rowIndex = 0;
    combinations.forEach((combination) => {
      const row = this.getPrototypeRow(rowIndex);
      this.$combinationsTableBody.append(row);

      // fill inputs
      const $combinationIdCell = $(ProductMap.combinations.tableRow.combinationIdCell(rowIndex));
      const $combinationNameCell = $(ProductMap.combinations.tableRow.combinationNameCell(rowIndex));
      const $finalPriceCell = $(ProductMap.combinations.tableRow.finalPriceTeCell(rowIndex));
      $combinationIdCell.val(combination.id);
      $combinationIdCell.parent().text(combination.id);
      $combinationNameCell.val(combination.name);
      $combinationNameCell.parent().text(combination.name);
      $finalPriceCell.val(combination.finalPriceTe);
      $finalPriceCell.parent().text(combination.finalPriceTe);
      $(ProductMap.combinations.tableRow.impactOnPriceCell(rowIndex)).val(combination.impactOnPrice);
      $(ProductMap.combinations.tableRow.quantityCell(rowIndex)).val(combination.quantity);
      $(ProductMap.combinations.tableRow.isDefaultCell(rowIndex)).val(combination.isDefault);
      $(ProductMap.combinations.tableRow.editButton(rowIndex)).data('id', combination.id);
      $(ProductMap.combinations.tableRow.deleteButton(rowIndex)).data('id', combination.id);
      rowIndex += 1;
    });
  }

  getPrototypeRow(rowIndex) {
    return this.prototypeTemplate.replace(new RegExp(this.prototypeName, 'g'), rowIndex);
  }
}
