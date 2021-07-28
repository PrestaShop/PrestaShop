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
import ObjectFormMapper from '@components/form/form-object-mapper';
import ProductFormMapping from '@pages/product/edit/product-form-mapping';
import ProductEventMap from '@pages/product/product-event-map';

export default class ProductFormModel {
  constructor($form, eventEmitter) {
    this.eventEmitter = eventEmitter;

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

    // For now we get precision only in the component, but maybe it would deserve a more global configuration
    // BigNumber.set({DECIMAL_PLACES: someConfig}) But where can we define/inject this global config?
    const $priceTaxExcludedInput = this.mapper.getInputsFor('product.price.priceTaxExcluded');
    this.precision = $priceTaxExcludedInput.data('displayPricePrecision');

    // Listens to event for product modification (registered after the model is constructed, because events are
    // triggered during the initial parsing but don't need them at first).
    this.eventEmitter.on(ProductEventMap.updatedProductField, (event) => this.productFieldUpdated(event));

    return {
      getProduct: () => this.getProduct(),
      watch: (productModelKey, callback) => this.watchProductModel(productModelKey, callback),
    };
  }

  /**
   * @returns {Object}
   */
  getProduct() {
    return this.mapper.getModel().product;
  }

  /**
   * @param {string} productModelKey
   * @param {function} callback
   */
  watchProductModel(productModelKey, callback) {
    this.mapper.watch(`product.${productModelKey}`, callback);
  }

  /**
   * Handles modifications that have happened in the product
   *
   * @param {Object} event
   * @private
   */
  productFieldUpdated(event) {
    this.updateProductPrices(event);
  }

  /**
   * Specific handler for modifications related to the product price
   *
   * @param {Object} event
   * @private
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

    const $taxRulesGroupIdInput = this.mapper.getInputsFor('product.price.taxRulesGroupId');
    const $selectedTaxOption = $(':selected', $taxRulesGroupIdInput);

    let taxRate;
    try {
      taxRate = new BigNumber($selectedTaxOption.data('taxRate'));
    } catch (error) {
      taxRate = BigNumber.NaN;
    }
    if (taxRate.isNaN()) {
      taxRate = new BigNumber(0);
    }

    const taxRatio = taxRate.dividedBy(100).plus(1);

    switch (event.modelKey) {
      case 'product.price.priceTaxIncluded': {
        const priceTaxIncluded = new BigNumber(this.getProduct().price.priceTaxIncluded);
        this.mapper.set(
          'product.price.priceTaxExcluded',
          priceTaxIncluded.dividedBy(taxRatio).toFixed(this.precision),
        );
        break;
      }
      default: {
        const priceTaxExcluded = new BigNumber(this.getProduct().price.priceTaxExcluded);
        this.mapper.set('product.price.priceTaxIncluded', priceTaxExcluded.times(taxRatio).toFixed(this.precision));
        break;
      }
    }
  }
}
