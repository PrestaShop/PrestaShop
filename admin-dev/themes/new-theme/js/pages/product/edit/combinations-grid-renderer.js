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
  constructor(productId) {
    this.productId = productId;
    this.router = new Router();
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.$combinationsTable = $(ProductMap.combinations.combinationsTable);
    this.$combinationsTableBody = $(ProductMap.combinations.combinationsTableBody);
    this.prototypeTemplate = this.$combinationsTable.data('prototype');
    this.prototypeName = this.$combinationsTable.data('prototypeName');
  }

  render(page, limit) {
    this.fetchData(page, limit);
    this.eventEmitter.on('combinationsDataFetched', (data) => {
      this.renderCombinations(data.combinations);
    });
  }

  renderCombinations(combinations) {
    this.cleanTable();

    let rowIndex = 0;
    combinations.forEach((combination) => {
      const row = this.getPrototypeRow(rowIndex);
      this.$combinationsTableBody.append(row);

      // fill inputs
      $(ProductMap.combinations.tableRow.combinationNameCell(rowIndex)).val(combination.name);
      $(ProductMap.combinations.tableRow.impactOnPriceCell(rowIndex)).val(combination.impactOnPrice);
      $(ProductMap.combinations.tableRow.finalPriceTeCell(rowIndex)).val(combination.finalPriceTe);
      $(ProductMap.combinations.tableRow.quantityCell(rowIndex)).val(combination.quantity);
      $(ProductMap.combinations.tableRow.isDefaultCell(rowIndex)).val(combination.isDefault);

      $(ProductMap.combinations.tableRow.editButton(rowIndex)).data('id', combination.id);
      $(ProductMap.combinations.tableRow.deleteButton(rowIndex)).data('id', combination.id);
      rowIndex += 1;
    });
  }

  fetchData(page, limit) {
    // @todo: data provider class?

    $.get(this.router.generate('admin_products_combinations', {
      productId: this.productId,
      page,
      limit,
    })).then((response) => {
      //@todo: event map
      this.eventEmitter.emit('combinationsDataFetched', {combinations: response.combinations});
    });
  }

  cleanTable() {
    this.$combinationsTableBody.empty();
  }

  getPrototypeRow(rowIndex) {
    return this.prototypeTemplate.replace(new RegExp(this.prototypeName, 'g'), rowIndex);
  }
}
