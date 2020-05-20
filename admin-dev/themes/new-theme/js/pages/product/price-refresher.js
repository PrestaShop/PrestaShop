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

import productMap from './product-map';
import * as types from './store/mutation-types';

/**
 * Responsible for price tax excluded/included and tax rate selection inputs interaction
 */
export default class PriceRefresher {
  constructor(store) {
    this.store = store;
    this.taxIncludedInputs = [productMap.priceTaxIncludedInput1, productMap.priceTaxIncludedInput2];
    this.taxExcludedInputs = [productMap.priceTaxExcludedInput1, productMap.priceTaxExcludedInput2];
    this.taxRuleInputs = [productMap.taxRuleInput1, productMap.taxRuleInput2];

    this.init();
  }

  /**
   * Initializes two way binding between DOM and state.
   */
  init() {
    this.listenToDomChanges();
    this.listenToStateChanges();
  }

  /**
   * Listens to DOM elements changes and mutates the state accordingly.
   */
  listenToDomChanges() {
    this.listenToPriceChangesInDom(this.taxIncludedInputs, true);
    this.listenToPriceChangesInDom(this.taxExcludedInputs, false);
    this.listenToTaxChangesInDom();
  }

  /**
   * Subscribes to state mutations and updates the DOM.
   */
  listenToStateChanges() {
    this.store.subscribe((mutation) => {
      const subscribedMutationTypes = [
        types.SET_PRICE_TAX_INCLUDED,
        types.SET_PRICE_TAX_EXCLUDED,
        types.SET_TAX_RULE,
      ];

      if (!subscribedMutationTypes.includes(mutation.type)) {
        return;
      }

      if (mutation.type === types.SET_PRICE_TAX_INCLUDED) {
        this.updatePricesInDom(this.taxIncludedInputs, true);
      } else if (mutation.type === types.SET_PRICE_TAX_EXCLUDED) {
        this.updatePricesInDom(this.taxExcludedInputs, false);
      } else {
        this.updateTaxRulesInDom(this.taxRuleInputs);
      }
    });
  }

  /**
   * Listens for price tax included/excluded inputs changes
   *
   * @param selectors
   * @param forTaxIncluded
   */
  listenToPriceChangesInDom(selectors, forTaxIncluded) {
    selectors.forEach((selector) => {
      const el = document.querySelector(selector);
      el.addEventListener('change', (event) => {
        if (forTaxIncluded) {
          this.store.dispatch('updatePriceTaxIncluded', {
            priceTaxIncluded: event.currentTarget.value,
          });
        } else {
          this.store.dispatch('updatePriceTaxExcluded', {
            priceTaxExcluded: event.currentTarget.value,
          });
        }
      });
    });
  }

  /**
   * Listens to tax rule selection changes
   */
  listenToTaxChangesInDom() {
    this.taxRuleInputs.forEach((selector) => {
      const el = document.querySelector(selector);
      el.addEventListener('change', (event) => {
        this.store.dispatch('updateTaxRule', {
          taxRule: {
            id: event.currentTarget.value,
            rate: event.currentTarget.options[event.currentTarget.selectedIndex].dataset.taxRate,
          },
        });
      });
    });
  }

  /**
   * Updates price values in DOM
   *
   * @param selectors
   * @param forTaxIncluded
   */
  updatePricesInDom(selectors, forTaxIncluded) {
    selectors.forEach((selector) => {
      const el = document.querySelector(selector);

      if (forTaxIncluded) {
        el.value = this.store.state.priceTaxIncluded;
      } else {
        el.value = this.store.state.priceTaxExcluded;
      }
    });
  }

  /**
   * Updates tax rule values in DOM
   *
   * @param selectors
   */
  updateTaxRulesInDom(selectors) {
    selectors.forEach((selector) => {
      const el = document.querySelector(selector);
      el.value = this.store.state.taxRule.id;
    });
  }
}
