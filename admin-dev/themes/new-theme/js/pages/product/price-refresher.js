/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import productEditMap from './product-edit-map';
import * as types from './store/mutation-types';

export default class PriceRefresher {
  constructor(store) {
    this.store = store;
    this.taxIncludedInputs = [productEditMap.priceTaxIncludedInput1];
    this.taxExcludedInputs = [productEditMap.priceTaxExcludedInput1];
    this.taxRateInputs = [productEditMap.taxRateInput1];

    this.init();
  }

  init() {
    this.listenToDOM();
    this.listenToStore();
  }

  listenToDOM() {
    this.listenToPriceInputsDOM(this.taxIncludedInputs, true);
    this.listenToPriceInputsDOM(this.taxExcludedInputs, false);
  }

  listenToStore() {
    this.store.subscribe((mutation) => {
      const subscribedMutationTypes = [
        types.SET_PRICE_TAX_INCLUDED,
        types.SET_PRICE_TAX_EXCLUDED,
        types.SET_TAX_RATE,
      ];

      if (!subscribedMutationTypes.includes(mutation.type)) {
        return;
      }

      if (mutation.type === types.SET_PRICE_TAX_INCLUDED) {
        this.updatePricesDOM(this.taxIncludedInputs, true);
      } else if (mutation.type === types.SET_PRICE_TAX_EXCLUDED) {
        this.updatePricesDOM(this.taxExcludedInputs, false);
      } else {
        this.updateTaxRatesDOM(this.taxRateInputs);
      }
    });
  }

  listenToPriceInputsDOM(selectors, forTaxIncluded) {
    selectors.forEach((selector) => {
      const el = document.querySelector(selector);
      el.addEventListener('change', (event) => {
        if (forTaxIncluded) {
          this.store.dispatch('updatePriceTaxIncluded', {
            priceTaxIncluded: event.currentTarget.value,
            rate: this.store.state.taxRate,
          });
        } else {
          this.store.dispatch('updatePriceTaxExcluded', {
            priceTaxExcluded: event.currentTarget.value,
            rate: this.store.state.taxRate,
          });
        }
      });
    });
  }

  listenToTaxInputsDOM(selectors) {
    selectors.forEach((selector) => {
      const el = document.querySelector(selector);
      el.addEventListener('change', (event) => {
        this.store.dispatch('updateTaxRate', {
          rate: event.currentTarget.value,
          priceTaxExcluded: this.store.state.priceTaxExcluded,
          priceTaxIncluded: this.store.state.priceTaxIncluded,
        });
      });
    });
  }

  updatePricesDOM(selectors, forTaxIncluded) {
    selectors.forEach((selector) => {
      const el = document.querySelector(selector);

      if (forTaxIncluded) {
        el.value = this.store.state.priceTaxIncluded;
      } else {
        el.value = this.store.state.priceTaxExcluded;
      }
    });
  }

  updateTaxRatesDOM(selectors) {
    selectors.forEach((selector) => {
      const el = document.querySelector(selector);
      //@todo: tax rate selectbox value should be id of selected element but not the RATE itself
      el.value = this.store.state.taxRate;
    });
  }
}
