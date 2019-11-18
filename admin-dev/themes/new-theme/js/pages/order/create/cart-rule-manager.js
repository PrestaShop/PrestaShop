/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import CartEditor from './cart-editor';
import CartRulesRenderer from './cart-rules-renderer';
import createOrderMap from './create-order-map';
import {EventEmitter} from '../../../components/event-emitter';
import eventMap from './event-map';
import Router from '../../../components/router';

const $ = window.$;

/**
 * Responsible for searching cart rules and managing cart rules search block
 */
export default class CartRuleManager {
  constructor() {
    this.router = new Router();
    this.$searchInput = $(createOrderMap.cartRuleSearchInput);
    this.cartRulesRenderer = new CartRulesRenderer();
    this.cartEditor = new CartEditor();

    this._initListeners();

    return {
      search: () => this._search(),
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
  _initListeners() {
    this._onCartRuleSearch();
    this._onAddCartRuleToCart();
    this._onAddCartRuleToCartFailure();
    this._onRemoveCartRuleFromCart();
  }

  /**
   * Listens for cart rule search action
   *
   * @private
   */
  _onCartRuleSearch() {
    EventEmitter.on(eventMap.cartRuleSearched, (cartRules) => {
      this.cartRulesRenderer.renderSearchResults(cartRules);
    });
  }

  /**
   * Listens event of add cart rule to cart action
   *
   * @private
   */
  _onAddCartRuleToCart() {
    EventEmitter.on(eventMap.cartRuleAdded, (cartInfo) => {
      this.cartRulesRenderer.renderCartRulesBlock(cartInfo.cartRules, cartInfo.products.length === 0);
    });
  }

  /**
   * Listens event when add cart rule to cart fails
   *
   * @private
   */
  _onAddCartRuleToCartFailure() {
    EventEmitter.on(eventMap.cartRuleFailedToAdd, (message) => {
      this.cartRulesRenderer.displayErrorMessage(message);
    });
  }

  /**
   * Listens event for remove cart rule from cart action
   *
   * @private
   */
  _onRemoveCartRuleFromCart() {
    EventEmitter.on(eventMap.cartRuleRemoved, (cartInfo) => {
      this.cartRulesRenderer.renderCartRulesBlock(cartInfo.cartRules, cartInfo.products.length === 0);
    });
  }

  /**
   * Searches for cart rules by search phrase
   *
   * @private
   */
  _search(searchPhrase) {
    if (searchPhrase.length < 3) {
      return;
    }

    $.get(this.router.generate('admin_cart_rules_search'), {
      search_phrase: searchPhrase,
    }).then((cartRules) => {
      EventEmitter.emit(eventMap.cartRuleSearched, cartRules);
    }).catch((e) => {
      showErrorMessage(e.responseJSON.message);
    });
  }
}
