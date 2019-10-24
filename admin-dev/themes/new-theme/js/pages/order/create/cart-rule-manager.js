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
    this.renderer = new CartRulesRenderer();
    this.cartEditor = new CartEditor();

    return {
      onCartRuleSearch: () => {
        this._search();
      },
      onCartRuleSelect: (cartRuleId, cartId) => {
        this._addCartRuleToCart(cartRuleId, cartId);
      },
      onDoneSearchingCartRule: () => {
        this.renderer.hideResultsDropdown();
      },
      onCartRuleRemove: (cartRuleId, cartId) => {
        this._removeCartRuleFromCart(cartRuleId, cartId);
      },
    };
  }

  /**
   * Searches for cart rules by search phrase
   *
   * @private
   */
  _search() {
    const searchPhrase = this.$searchInput.val();
    if (searchPhrase.length < 3) {
      return;
    }

    $.get(this.router.generate('admin_cart_rules_search'), {
      search_phrase: searchPhrase,
    }).then((cartRules) => {
      this.renderer.renderSearchResults(cartRules);
    }).catch((e) => {
      showErrorMessage(e.responseJSON.message);
    });
  }

  /**
   * Adds cart rule to cart
   *
   * @param cartRuleId
   * @param cartId
   *
   * @private
   */
  _addCartRuleToCart(cartRuleId, cartId) {
    this.cartEditor.addCartRuleToCart(cartRuleId, cartId);

    EventEmitter.on(eventMap.cartRuleAdded, (cartInfo) => {
      this.renderer.renderCartRulesBlock(cartInfo.cartRules, cartInfo.products.length === 0);
    });
    EventEmitter.on(eventMap.cartRuleFailedToAdd, (message) => {
      this.renderer.displayErrorMessage(message);
    });
  }

  /**
   * Removes cart rule from cart
   *
   * @param cartRuleId
   * @param cartId
   *
   * @private
   */
  _removeCartRuleFromCart(cartRuleId, cartId) {
    this.cartEditor.removeCartRuleFromCart(cartRuleId, cartId);

    EventEmitter.on(eventMap.cartRuleRemoved, (cartInfo) => {
      this.renderer.renderCartRulesBlock(cartInfo.cartRules, cartInfo.products.length === 0);
    });
  }
}
