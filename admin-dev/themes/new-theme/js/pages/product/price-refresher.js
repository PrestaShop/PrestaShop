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
    this.taxIncludedInputs = productMap.priceTaxIncludedInputs;
    this.taxExcludedInputs = productMap.priceTaxExcludedInputs;
    this.taxRulesGroupSelections = productMap.taxRulesGroupSelections;

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
    this.listenToTaxRulesGroupChangesInDom();
  }

  /**
   * Subscribes to state mutations and updates the DOM.
   */
  listenToStateChanges() {
    this.store.subscribe((mutation) => {
      const subscribedMutationTypes = [
        types.SET_PRICE_TAX_INCLUDED,
        types.SET_PRICE_TAX_EXCLUDED,
        types.SET_TAX_RULES_GROUP,
      ];

      if (!subscribedMutationTypes.includes(mutation.type)) {
        return;
      }

      if (mutation.type === types.SET_PRICE_TAX_INCLUDED) {
        this.updatePricesInDom(this.taxIncludedInputs, true);
      } else if (mutation.type === types.SET_PRICE_TAX_EXCLUDED) {
        this.updatePricesInDom(this.taxExcludedInputs, false);
      } else {
        this.updateTaxRulesGroupInDom();
      }
    });
  }

  /**
   * Listens for price tax included/excluded inputs changes
   *
   * @param selector
   * @param forTaxIncluded
   */
  listenToPriceChangesInDom(selector, forTaxIncluded) {
    const elements = document.querySelectorAll(selector);

    elements.forEach((el) => {
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
   * Listens to tax rules selection changes
   */
  listenToTaxRulesGroupChangesInDom() {
    const elements = document.querySelectorAll(this.taxRulesGroupSelections);
    elements.forEach((el) => {
      el.addEventListener('change', (event) => {
        this.store.dispatch('updateTaxRulesGroup', {
          taxRulesGroup: {
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
   * @param selector
   * @param forTaxIncluded
   */
  updatePricesInDom(selector, forTaxIncluded) {
    const elements = document.querySelectorAll(selector);

    elements.forEach((el) => {
      if (forTaxIncluded) {
        el.value = this.store.state.priceTaxIncluded;
      } else {
        el.value = this.store.state.priceTaxExcluded;
      }
    });
  }

  /**
   * Updates tax rule group values in DOM
   */
  updateTaxRulesGroupInDom() {
    const elements = document.querySelectorAll(this.taxRulesGroupSelections);

    elements.forEach((el) => {
      el.value = this.store.state.taxRulesGroup.id;
    });
  }
}
