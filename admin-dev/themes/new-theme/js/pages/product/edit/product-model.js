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

import {Decimal} from 'decimal.js';
import ObjectFormMapper from '@components/form/form-object-mapper';
import ProductFormMapping from '@pages/product/edit/product-form-mapping';
import ProductEventMap from '@pages/product/product-event-map';
import ProductMap from '@pages/product/product-map';

export default class ProductModel {
  constructor($form, eventEmitter) {
    this.eventEmitter = eventEmitter;

    // For now we get precision only in the component, but maybe it would deserve a more global configuration
    // Decimal.set({precision: someConfig}) But where can we define/inject this global config?
    this.precision = $(ProductMap.price.priceTaxExcludedInput).data('displayPricePrecision');

    // Init form mapper
    this.mapper = new ObjectFormMapper(
      $form,
      ProductFormMapping,
      eventEmitter,
      {
        modelUpdated: ProductEventMap.productModelUpdated,
        updateModel: ProductEventMap.updatedProductModel,
        modelFieldUpdated: ProductEventMap.updatedProductField,
      },
    );

    // Listens to event for product modification (registered after the model is constructed, because events are
    // triggered during the initial parsing but don't need them at first).
    this.eventEmitter.on(ProductEventMap.updatedProductField, (event) => this.productFieldUpdated(event));
  }

  /**
   * @returns {Object}
   */
  getProduct() {
    return this.mapper.getObject().product;
  }

  /**
   * @param modelKey (string)
   *
   * @returns {*|{}}
   */
  get(modelKey) {
    return this.mapper.get(modelKey);
  }

  /**
   * @param modelKey {string}
   * @param value {*|{}}
   */
  set(modelKey, value) {
    this.mapper.set(modelKey, value);
  }

  /**
   * Handles modifications that have happened in the product
   * @param event {Object}
   */
  productFieldUpdated(event) {
    this.updateProductPrices(event);
  }

  /**
   * Specific handler for modifications related to the product price
   *
   * @param event
   */
  updateProductPrices(event) {
    const pricesFields = [
      'product.price.priceTaxIncluded',
      'product.price.priceTaxExcluded',
      'product.price.taxRulesGroupId',
    ];

    if (!pricesFields.includes(event.modelKey)) {
      return;
    }

    const $taxRulesGroupIdInput = this.mapper.getInput('product.price.taxRulesGroupId');
    const $selectedTaxOption = $(':selected', $taxRulesGroupIdInput);
    let taxRate;
    try {
      taxRate = new Decimal($selectedTaxOption.data('taxRate'));
    } catch (error) {
      taxRate = new Decimal(0);
    }

    const taxRatio = taxRate.dividedBy(100).plus(1);

    switch (event.modelKey) {
      case 'product.price.priceTaxIncluded': {
        const priceTaxIncluded = new Decimal(this.mapper.get('product.price.priceTaxIncluded'));
        this.mapper.set(
          'product.price.priceTaxExcluded',
          priceTaxIncluded.dividedBy(taxRatio).toFixed(this.precision),
        );
        break;
      }
      default: {
        const priceTaxExcluded = new Decimal(this.mapper.get('product.price.priceTaxExcluded'));
        this.mapper.set('product.price.priceTaxIncluded', priceTaxExcluded.times(taxRatio).toFixed(this.precision));
        break;
      }
    }
  }
}
