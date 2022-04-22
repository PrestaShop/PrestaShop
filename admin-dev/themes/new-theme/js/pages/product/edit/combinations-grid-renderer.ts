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
import BigNumber from 'bignumber.js';

import ChangeEvent = JQuery.ChangeEvent;

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

  productFormModel: ProductFormModel;

  constructor(productFormModel: ProductFormModel) {
    this.productFormModel = productFormModel;
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.$combinationsTable = $(ProductMap.combinations.combinationsTable);
    this.$combinationsTableBody = $(ProductMap.combinations.combinationsTableBody);
    this.$loadingSpinner = $(ProductMap.combinations.loadingSpinner);
    this.prototypeTemplate = this.$combinationsTable.data('prototype');
    this.prototypeName = this.$combinationsTable.data('prototypeName');

    this.$combinationsTable.on('change', ProductMap.combinations.list.priceImpactTaxExcluded, (event: ChangeEvent) => {
      this.updateByPriceImpactTaxExcluded($(event.currentTarget));
    });

    this.$combinationsTable.on('change', ProductMap.combinations.list.priceImpactTaxIncluded, (event: ChangeEvent) => {
      this.updateByPriceImpactTaxIncluded($(event.currentTarget));
    });
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
      const $row = $(this.getPrototypeRow(rowIndex, combination));

      $(':input', $row).each((index, input) => {
        const $input = $(input);

        if ($input.val()) {
          // @ts-ignore
          $input.data('initialValue', $input.val());
        }
      });

      if (combination.isDefault) {
        $(ProductMap.combinations.tableRow.isDefaultInput(rowIndex), $row).prop('checked', true);
      }

      this.updateByPriceImpactTaxExcluded($(ProductMap.combinations.list.priceImpactTaxExcluded, $row));

      this.$combinationsTableBody.append($row);
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

  private updateByPriceImpactTaxExcluded($priceImpactTaxExcluded: JQuery): void {
    const $row = $priceImpactTaxExcluded.parents(ProductMap.combinations.list.combinationRow);
    const $priceImpactTaxIncluded = $(ProductMap.combinations.list.priceImpactTaxIncluded, $row);

    if (typeof $row === 'undefined' || typeof $priceImpactTaxIncluded === 'undefined') {
      return;
    }

    // @ts-ignore
    const priceImpactTaxExcluded: BigNumber = new BigNumber($priceImpactTaxExcluded.val());

    if (priceImpactTaxExcluded.isNaN()) {
      return;
    }

    $priceImpactTaxIncluded.val(this.productFormModel.addTax(priceImpactTaxExcluded));
    this.updateFinalPrice(priceImpactTaxExcluded, $row);
  }

  private updateByPriceImpactTaxIncluded($priceImpactTaxIncluded: JQuery): void {
    const $row = $priceImpactTaxIncluded.parents(ProductMap.combinations.list.combinationRow);
    const $priceImpactTaxExcluded = $(ProductMap.combinations.list.priceImpactTaxExcluded, $row);

    if (typeof $row === 'undefined' || typeof $priceImpactTaxExcluded === 'undefined') {
      return;
    }

    // @ts-ignore
    const priceImpactTaxIncluded: BigNumber = new BigNumber($priceImpactTaxIncluded.val());

    if (priceImpactTaxIncluded.isNaN()) {
      return;
    }

    $priceImpactTaxExcluded.val(this.productFormModel.removeTax(priceImpactTaxIncluded));
    const taxRatio = this.productFormModel.getTaxRatio();

    if (taxRatio.isNaN()) {
      return;
    }

    this.updateFinalPrice(priceImpactTaxIncluded.dividedBy(taxRatio), $row);
  }

  private updateFinalPrice(priceImpactTaxExcluded: BigNumber, $row: JQuery) {
    const productPrice = this.productFormModel.getPriceTaxExcluded();
    const $finalPrice = $(ProductMap.combinations.list.finalPrice, $row);
    const $finalPricePreview = $finalPrice.siblings(ProductMap.combinations.list.finalPricePreview);
    const finalPrice = this.productFormModel.displayPrice(productPrice.plus(priceImpactTaxExcluded));

    if (typeof $finalPrice !== 'undefined') {
      $finalPrice.val(finalPrice);
    }

    if (typeof $finalPricePreview !== 'undefined') {
      $finalPricePreview.html(finalPrice);
    }
  }
}
