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

    this.init();
  }

  init() {
    this.listenToDOM(this.taxIncludedInputs, true);
    this.listenToDOM(this.taxExcludedInputs, false);
    this.listenToStore(this.taxIncludedInputs, true);
    this.listenToStore(this.taxExcludedInputs, false);
  }

  listenToDOM(selectors, forTaxIncluded) {
    selectors.forEach((selector) => {
      const el = document.querySelector(selector);
      el.addEventListener('change', (event) => {
        if (forTaxIncluded) {
          this.store.dispatch('updatePriceTaxIncluded', event.currentTarget.value);
        } else {
          this.store.dispatch('updatePriceTaxExcluded', event.currentTarget.value);
        }
      });
    });
  }

  listenToStore(selectors, forTaxIncluded) {
    this.store.subscribe((mutation, state) => {
      const priceMutationTypes = [types.SET_PRICE_TAX_INCLUDED, types.SET_PRICE_TAX_EXCLUDED];

      if (!priceMutationTypes.includes(mutation.type)) {
        return;
      }

      selectors.forEach((selector) => {
        const el = document.querySelector(selector);

        if (forTaxIncluded && mutation.type === types.SET_PRICE_TAX_INCLUDED) {
          el.value = state.priceTaxIncluded;
        } else if (!forTaxIncluded && mutation.type === types.SET_PRICE_TAX_EXCLUDED) {
          el.value = state.priceTaxExcluded;
        }
      });
    });
  }
}
