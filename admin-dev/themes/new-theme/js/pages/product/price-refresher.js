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

export default class PriceRefresher {
  constructor(store) {
    this.store = store;
    this.taxIncludedInputs = [productMap.priceTaxIncludedInput1, productMap.priceTaxIncludedInput2];
    this.taxExcludedInputs = [productMap.priceTaxExcludedInput1, productMap.priceTaxExcludedInput2];
    this.taxRuleInputs = [productMap.taxRuleInput1, productMap.taxRuleInput2];

    this.init();
  }

  init() {
    this.initState();
    this.listenToDomChanges();
    this.listenToStateChanges();
  }

  initState() {
    const priceTaxExcluded = document.querySelector(this.taxExcludedInputs[0]).value;
    const priceTaxIncluded = document.querySelector(this.taxIncludedInputs[0]).value;
    const taxRuleSelection = document.querySelector(this.taxRuleInputs[0]);

    this.store.commit(types.SET_PRICE_TAX_INCLUDED, priceTaxIncluded);
    this.store.commit(types.SET_PRICE_TAX_EXCLUDED, priceTaxExcluded);
    this.store.commit(types.SET_TAX_RULE, {
      id: taxRuleSelection.value,
      rate: taxRuleSelection.options[taxRuleSelection.selectedIndex].dataset.taxRate,
    });
  }

  listenToDomChanges() {
    this.listenToPriceChangesInDom(this.taxIncludedInputs, true);
    this.listenToPriceChangesInDom(this.taxExcludedInputs, false);
    this.listenToTaxChangesInDom();
  }

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

  listenToPriceChangesInDom(selectors, forTaxIncluded) {
    selectors.forEach((selector) => {
      const el = document.querySelector(selector);
      el.addEventListener('change', (event) => {
        if (forTaxIncluded) {
          this.store.dispatch('updatePriceTaxIncluded', {
            priceTaxIncluded: event.currentTarget.value,
            taxRule: this.store.state.taxRule,
          });
        } else {
          this.store.dispatch('updatePriceTaxExcluded', {
            priceTaxExcluded: event.currentTarget.value,
            taxRule: this.store.state.taxRule,
          });
        }
      });
    });
  }

  listenToTaxChangesInDom() {
    this.taxRuleInputs.forEach((selector) => {
      const el = document.querySelector(selector);
      el.addEventListener('change', (event) => {
        this.store.dispatch('updateTaxRule', {
          taxRule: {
            id: event.currentTarget.value,
            rate: event.currentTarget.options[event.currentTarget.selectedIndex].dataset.taxRate,
          },
          priceTaxExcluded: this.store.state.priceTaxExcluded,
          priceTaxIncluded: this.store.state.priceTaxIncluded,
        });
      });
    });
  }

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

  updateTaxRulesInDom(selectors) {
    selectors.forEach((selector) => {
      const el = document.querySelector(selector);
      el.value = this.store.state.taxRule.id;
    });
  }
}
