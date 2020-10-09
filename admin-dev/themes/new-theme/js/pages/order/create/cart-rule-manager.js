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

import CartEditor from '@pages/order/create/cart-editor';
import CartRulesRenderer from '@pages/order/create/cart-rules-renderer';
import {EventEmitter} from '@components/event-emitter';
import eventMap from '@pages/order/create/event-map';
import Router from '@components/router';
import SummaryRenderer from '@pages/order/create/summary-renderer';
import ShippingRenderer from '@pages/order/create/shipping-renderer';
import ProductRenderer from '@pages/order/create/product-renderer';

const {$} = window;

/**
 * Responsible for searching cart rules and managing cart rules search block
 */
export default class CartRuleManager {
  constructor() {
    this.activeSearchRequest = null;

    this.router = new Router();
    this.cartRulesRenderer = new CartRulesRenderer();
    this.cartEditor = new CartEditor();
    this.summaryRenderer = new SummaryRenderer();
    this.shippingRenderer = new ShippingRenderer();
    this.productRenderer = new ProductRenderer();

    this.initListeners();

    return {
      search: (searchPhrase) => this.search(searchPhrase),
      stopSearching: () => this.cartRulesRenderer.hideResultsDropdown(),
      addCartRuleToCart: (cartRuleId, cartId) => this.cartEditor.addCartRuleToCart(cartRuleId, cartId),
      removeCartRuleFromCart: (cartRuleId, cartId) => this.cartEditor.removeCartRuleFromCart(cartRuleId, cartId),
    };
  }

  /**
   * Initiates event listeners for cart rule actions
   *
   * @private
   */
  initListeners() {
    this.onCartRuleSearch();
    this.onAddCartRuleToCart();
    this.onAddCartRuleToCartFailure();
    this.onRemoveCartRuleFromCart();
  }

  /**
   * Listens for cart rule search action
   *
   * @private
   */
  onCartRuleSearch() {
    EventEmitter.on(eventMap.cartRuleSearched, (cartRules) => {
      this.cartRulesRenderer.renderSearchResults(cartRules);
    });
  }

  /**
   * Listens event of add cart rule to cart action
   *
   * @private
   */
  onAddCartRuleToCart() {
    EventEmitter.on(eventMap.cartRuleAdded, (cartInfo) => {
      const cartIsEmpty = cartInfo.products.length === 0;
      this.cartRulesRenderer.renderCartRulesBlock(cartInfo.cartRules, cartIsEmpty);
      this.productRenderer.renderList(cartInfo.products);
      this.shippingRenderer.render(cartInfo.shipping, cartIsEmpty);
      this.summaryRenderer.render(cartInfo);
    });
  }

  /**
   * Listens event when add cart rule to cart fails
   *
   * @private
   */
  onAddCartRuleToCartFailure() {
    EventEmitter.on(eventMap.cartRuleFailedToAdd, (message) => {
      this.cartRulesRenderer.displayErrorMessage(message);
    });
  }

  /**
   * Listens event for remove cart rule from cart action
   *
   * @private
   */
  onRemoveCartRuleFromCart() {
    EventEmitter.on(eventMap.cartRuleRemoved, (cartInfo) => {
      const cartIsEmpty = cartInfo.products.length === 0;
      this.shippingRenderer.render(cartInfo.shipping, cartIsEmpty);
      this.cartRulesRenderer.renderCartRulesBlock(cartInfo.cartRules, cartIsEmpty);
      this.summaryRenderer.render(cartInfo);
      this.productRenderer.renderList(cartInfo.products);
    });
  }

  /**
   * Searches for cart rules by search phrase
   *
   * @private
   */
  search(searchPhrase) {
    if (this.activeSearchRequest !== null) {
      this.activeSearchRequest.abort();
    }

    this.activeSearchRequest = $.get(this.router.generate('admin_cart_rules_search'), {
      search_phrase: searchPhrase,
    });

    this.activeSearchRequest.then((cartRules) => {
      EventEmitter.emit(eventMap.cartRuleSearched, cartRules);
    }).catch((e) => {
      if (e.statusText === 'abort') {
        return;
      }

      window.showErrorMessage(e.responseJSON.message);
    });
  }
}
