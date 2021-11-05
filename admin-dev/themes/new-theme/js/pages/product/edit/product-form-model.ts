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

import BigNumber from 'bignumber.js';
import EventEmitter from '@components/event-emitter';
import FormObjectMapper, {FormUpdateEvent} from '@components/form/form-object-mapper';
import ProductFormMapping from '@pages/product/edit/product-form-mapping';
import ProductEventMap from '@pages/product/product-event-map';

export default class ProductFormModel {
  eventEmitter: typeof EventEmitter;

  mapper: FormObjectMapper;

  precision: number;

  constructor($form: JQuery, eventEmitter: typeof EventEmitter) {
    this.eventEmitter = eventEmitter;

    // Init form mapper
    this.mapper = new FormObjectMapper(
      $form,
      ProductFormMapping,
      eventEmitter,
      {
        modelUpdated: ProductEventMap.productModelUpdated,
        updateModel: ProductEventMap.updatedProductModel,
        modelFieldUpdated: ProductEventMap.updatedProductField,
      },
    );

    // For now we get precision only in the component, but maybe it would deserve a more global configuration
    // BigNumber.set({DECIMAL_PLACES: someConfig}) But where can we define/inject this global config?
    const $priceTaxExcludedInput = this.mapper.getInputsFor('product.price.priceTaxExcluded');
    this.precision = <number>$priceTaxExcludedInput?.data('displayPricePrecision');

    // Listens to event for product modification (registered after the model is constructed, because events are
    // triggered during the initial parsing but don't need them at first).
    this.eventEmitter.on(ProductEventMap.updatedProductField, (event) => this.productFieldUpdated(event));
  }

  /**
   * @returns {Object}
   *
   * @private
   */
  getProduct(): Record<string, any> {
    return this.mapper.getModel().product;
  }

  /**
   * @param {string} productModelKey
   * @param {function} callback
   *
   * @private
   */
  watchProductModel(productModelKey: string, callback: (event: FormUpdateEvent) => void): void {
    this.mapper.watch(`product.${productModelKey}`, callback);
  }

  /**
   * @param {string} productModelKey
   * @param {*} value
   */
  setProductValue(productModelKey: string, value: any): void {
    this.mapper.set(`product.${productModelKey}`, value);
  }

  /**
   * Handles modifications that have happened in the product
   *
   * @param {Object} event
   *
   * @private
   */
  private productFieldUpdated(event: FormUpdateEvent): void {
    this.updateProductPrices(event);
  }

  /**
   * Specific handler for modifications related to the product price
   *
   * @param {Object} event
   * @private
   */
  private updateProductPrices(event: Record<string, any>) {
    const pricesFields = [
      'product.price.priceTaxIncluded',
      'product.price.priceTaxExcluded',
      'product.price.taxRulesGroupId',
      'product.price.unitPriceTaxIncluded',
      'product.price.unitPriceTaxExcluded',
    ];

    if (!pricesFields.includes(event.modelKey)) {
      return;
    }

    const $taxRulesGroupIdInput = this.mapper.getInputsFor('product.price.taxRulesGroupId');
    const $selectedTaxOption = $(':selected', $taxRulesGroupIdInput);

    let taxRate;
    try {
      taxRate = new BigNumber($selectedTaxOption.data('taxRate'));
    } catch (error) {
      taxRate = new BigNumber(NaN);
    }
    if (taxRate.isNaN()) {
      taxRate = new BigNumber(0);
    }

    const taxRatio = taxRate.dividedBy(100).plus(1);

    // eslint-disable-next-line default-case
    switch (event.modelKey) {
      case 'product.price.priceTaxIncluded': {
        const priceTaxIncluded = this.mapper.getBigNumber('product.price.priceTaxIncluded');
        this.mapper.set('product.price.priceTaxExcluded', this.removeTax(priceTaxIncluded, taxRatio));
        break;
      }
      case 'product.price.priceTaxExcluded': {
        const priceTaxExcluded = this.mapper.getBigNumber('product.price.priceTaxExcluded');
        this.mapper.set('product.price.priceTaxIncluded', this.addTax(priceTaxExcluded, taxRatio));
        break;
      }

      case 'product.price.unitPriceTaxIncluded': {
        const unitPriceTaxIncluded = this.mapper.getBigNumber('product.price.unitPriceTaxIncluded');
        this.mapper.set('product.price.unitPriceTaxExcluded', this.removeTax(unitPriceTaxIncluded, taxRatio));
        break;
      }
      case 'product.price.unitPriceTaxExcluded': {
        const unitPriceTaxExcluded = this.mapper.getBigNumber('product.price.unitPriceTaxExcluded');
        this.mapper.set('product.price.unitPriceTaxIncluded', this.addTax(unitPriceTaxExcluded, taxRatio));
        break;
      }

      case 'product.price.taxRulesGroupId': {
        const priceTaxExcluded = this.mapper.getBigNumber('product.price.priceTaxExcluded');
        this.mapper.set('product.price.priceTaxIncluded', this.addTax(priceTaxExcluded, taxRatio));
        const unitPriceTaxExcluded = this.mapper.getBigNumber('product.price.unitPriceTaxExcluded');
        this.mapper.set('product.price.unitPriceTaxIncluded', this.addTax(unitPriceTaxExcluded, taxRatio));
        break;
      }
    }
  }

  /**
   * @param {BigNumber} price
   * @param {BigNumber} taxRatio
   *
   * @returns {string}
   */
  removeTax(price, taxRatio) {
    return price.dividedBy(taxRatio).toFixed(this.precision);
  }

  /**
   * @param {BigNumber} price
   * @param {BigNumber} taxRatio
   *
   * @returns {string}
   */
  addTax(price, taxRatio) {
    return price.times(taxRatio).toFixed(this.precision);
  }
}
