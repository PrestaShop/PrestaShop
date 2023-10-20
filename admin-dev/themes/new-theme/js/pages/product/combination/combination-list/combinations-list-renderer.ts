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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

import ProductMap from '@pages/product/product-map';
import ProductEventMap from '@pages/product/product-event-map';
import {EventEmitter} from 'events';
import ProductFormModel from '@pages/product/edit/product-form-model';
import BigNumber from '@node_modules/bignumber.js';
import {isUndefined} from '@PSTypes/typeguard';

import ChangeEvent = JQuery.ChangeEvent;

const {$} = window;
const CombinationsMap = ProductMap.combinations;

export type SortGridCallback = (sortColumn: string, sortOrder: string) => void;
export type EmptyStateCallback = (isEmpty: boolean) => void;

/**
 * Renders the list of combinations in product edit page, it is also responsible for automatic updates
 * of the list fields (like price editions). It handles the sort controls although it is not responsible for
 * the sort query, the query is performed externally via the sortListCallback.
 */
export default class CombinationsListRenderer {
  private readonly eventEmitter: EventEmitter;

  private readonly productFormModel: ProductFormModel;

  private readonly sortListCallback: SortGridCallback;

  private readonly emptyStateCallback: EmptyStateCallback;

  private readonly $loadingSpinner: JQuery;

  private readonly prototypeTemplate: string;

  private readonly prototypeName: string;

  private readonly $combinationsListContainer: JQuery;

  private sortingEnabled = true;

  constructor(
    eventEmitter: EventEmitter,
    productFormModel: ProductFormModel,
    sortListCallback: SortGridCallback,
    emptyStateCallback: EmptyStateCallback,
  ) {
    this.eventEmitter = eventEmitter;
    this.productFormModel = productFormModel;
    this.sortListCallback = sortListCallback;
    this.emptyStateCallback = emptyStateCallback;
    this.$loadingSpinner = $(ProductMap.combinations.loadingSpinner);
    this.$combinationsListContainer = $(ProductMap.combinations.combinationsFormContainer);

    // We can't keep a reference on the table (or its content) since it can be updated via ajax, we always get it just in time
    const $combinationsTable = this.getCombinationsTable();
    this.prototypeTemplate = $combinationsTable.data('prototype');
    this.prototypeName = $combinationsTable.data('prototypeName');

    this.initListeners();
  }

  /**
   * @param {Object} data Expected structure: {combinations: [{Object}, {Object}...], total: {Number}}
   */
  render(data: Record<string, any>): void {
    this.renderCombinations(data.combinations);
  }

  /**
   * @param {Boolean} loading
   */
  setLoading(loading: boolean): void {
    this.$loadingSpinner.toggle(loading);
  }

  setSorting(sortingEnabled: boolean): void {
    this.sortingEnabled = sortingEnabled;
  }

  private initListeners(): void {
    this.$combinationsListContainer.on('change', CombinationsMap.list.priceImpactTaxExcluded, (event: ChangeEvent) => {
      this.updateByPriceImpactTaxExcluded($(event.currentTarget));
    });

    this.$combinationsListContainer.on('change', CombinationsMap.list.priceImpactTaxIncluded, (event: ChangeEvent) => {
      this.updateByPriceImpactTaxIncluded($(event.currentTarget));
    });

    this.$combinationsListContainer.on('click', CombinationsMap.list.isDefault, (event) => {
      const clickedDefaultId = event.currentTarget.id;
      $(`${CombinationsMap.list.isDefault}:not(#${clickedDefaultId})`).prop('checked', false).val(0);
      $(`#${clickedDefaultId}`).prop('checked', true).val(1);
    });

    this.initSortingColumns();
  }

  private initSortingColumns(): void {
    this.$combinationsListContainer.on(
      'click',
      CombinationsMap.sortableColumns,
      (event) => {
        if (!this.sortingEnabled) {
          return;
        }

        const $sortableColumn = $(event.currentTarget);
        const columnName = $sortableColumn.data('sortColName');

        if (!columnName) {
          return;
        }

        let direction = $sortableColumn.data('sortDirection');

        if (!direction || direction === 'desc') {
          direction = 'asc';
        } else {
          direction = 'desc';
        }

        // Reset all columns, we need to force the attributes for CSS matching
        $(
          CombinationsMap.sortableColumns,
          this.$combinationsListContainer,
        ).removeData('sortIsCurrent');
        $(
          CombinationsMap.sortableColumns,
          this.$combinationsListContainer,
        ).removeData('sortDirection');
        $(
          CombinationsMap.sortableColumns,
          this.$combinationsListContainer,
        ).removeAttr('data-sort-is-current');
        $(
          CombinationsMap.sortableColumns,
          this.$combinationsListContainer,
        ).removeAttr('data-sort-direction');

        // Set correct data in current column, we need to force the attributes for CSS matching
        $sortableColumn.data('sortIsCurrent', 'true');
        $sortableColumn.data('sortDirection', direction);
        $sortableColumn.attr('data-sort-is-current', 'true');
        $sortableColumn.attr('data-sort-direction', direction);

        // Finally update list
        this.sortListCallback(columnName, direction);
      },
    );
  }

  /**
   * This container can be updated/replaced outside of this class so we can't keep a reference in the class
   * instead we need to use this getter each time we need to get the container so that it always returns a valid
   * just-in-time existing dom element.
   */
  private getCombinationsTable(): JQuery {
    return $(CombinationsMap.combinationsTable);
  }

  /**
   * @param {Array} combinations
   *
   * @private
   */
  private renderCombinations(combinations: Array<Record<string, any>>): void {
    const $combinationsTableBody = $(CombinationsMap.combinationsTableBody);

    $combinationsTableBody.empty();
    this.emptyStateCallback(combinations.length === 0);

    let rowIndex = 0;
    combinations.forEach((combination: Record<string, any>) => {
      const $row = $(this.getPrototypeRow(rowIndex, combination));

      if (combination.is_default) {
        // Init first default, and handle radio behaviour amongst lines
        $(CombinationsMap.list.isDefault, $row).prop('checked', true);
      }
      this.updateByPriceImpactTaxExcluded($(CombinationsMap.list.priceImpactTaxExcluded, $row));

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

  private updateByPriceImpactTaxExcluded($priceImpactTaxExcluded: JQuery): void {
    const $row = $priceImpactTaxExcluded.parents(CombinationsMap.list.combinationRow);
    const $priceImpactTaxIncluded = $(CombinationsMap.list.priceImpactTaxIncluded, $row);

    if (isUndefined($row) || isUndefined($priceImpactTaxIncluded)) {
      return;
    }

    const priceImpactTaxExcluded: BigNumber = new BigNumber(Number($priceImpactTaxExcluded.val()));

    if (priceImpactTaxExcluded.isNaN()) {
      $priceImpactTaxExcluded.val(0);
      return;
    }

    $priceImpactTaxIncluded.val(this.productFormModel.addTax(priceImpactTaxExcluded));
    $priceImpactTaxIncluded.addClass(CombinationsMap.list.modifiedFieldClass);
    this.updateFinalPrice(priceImpactTaxExcluded, $row);
  }

  private updateByPriceImpactTaxIncluded($priceImpactTaxIncluded: JQuery): void {
    const $row = $priceImpactTaxIncluded.parents(CombinationsMap.list.combinationRow);
    const $priceImpactTaxExcluded = $(CombinationsMap.list.priceImpactTaxExcluded, $row);

    if (isUndefined($row) || isUndefined($priceImpactTaxExcluded)) {
      return;
    }

    const priceImpactTaxIncluded: BigNumber = new BigNumber(Number($priceImpactTaxIncluded.val()));

    if (priceImpactTaxIncluded.isNaN()) {
      $priceImpactTaxIncluded.val(0);
      return;
    }

    $priceImpactTaxExcluded.val(this.productFormModel.removeTax(priceImpactTaxIncluded));
    $priceImpactTaxExcluded.addClass(CombinationsMap.list.modifiedFieldClass);
    const taxRatio = this.productFormModel.getTaxRatio();

    if (taxRatio.isNaN()) {
      return;
    }

    this.updateFinalPrice(priceImpactTaxIncluded.dividedBy(taxRatio), $row);
  }

  private updateFinalPrice(priceImpactTaxExcluded: BigNumber, $row: JQuery) {
    const productPrice = this.productFormModel.getPriceTaxExcluded();
    const $finalPrice = $(CombinationsMap.list.finalPrice, $row);
    const $finalPricePreview = $finalPrice.siblings(CombinationsMap.list.finalPricePreview);
    let combinationPrice = productPrice.plus(priceImpactTaxExcluded);

    const combinationEcoTax = $(CombinationsMap.list.ecoTax, $row).val();

    let ecoTax;

    if (combinationEcoTax !== undefined && combinationEcoTax > 0) {
      ecoTax = new BigNumber(combinationEcoTax.toString());
    } else {
      ecoTax = this.productFormModel.getBigNumber('price.ecotaxTaxExcluded') ?? new BigNumber(0);
    }

    if (!ecoTax.isNaN()) {
      combinationPrice = combinationPrice.plus(new BigNumber(ecoTax.toString()));
    }

    const finalPrice = this.productFormModel.displayPrice(combinationPrice);

    if (typeof $finalPrice !== 'undefined') {
      $finalPrice.val(finalPrice);
    }

    if (typeof $finalPricePreview !== 'undefined') {
      $finalPricePreview.html(finalPrice);
    }
  }
}
