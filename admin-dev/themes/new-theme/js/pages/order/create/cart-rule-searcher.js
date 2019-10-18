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

import Router from '../../../components/router';
import createOrderMap from './create-order-map';
import CartRulesRenderer from './cart-rules-renderer';

const $ = window.$;

/**
 * Responsible for searching cart rules and managing cart rules search block
 */
export default class CartRuleSearcher {
  constructor() {
    this.router = new Router();
    this.$searchInput = $(createOrderMap.cartRuleSearchInput);
    this.$searchResultBox = $(createOrderMap.cartRulesSearchResultBox);
    this.cartRulesRenderer = new CartRulesRenderer();

    return {
      onCartRuleSearch: () => {
        this._search();
      },
      onCartRuleSelect: (cartRuleId, cartId) => {
        this._addCartRuleToCart(cartRuleId, cartId);
      },
      onDoneSearchingCartRule: () => {
        this._hideResultsDropdown();
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
      this._renderSearchResults(cartRules);
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
    $.post(this.router.generate('admin_carts_add_rule', {cartId}), {
      cart_rule_id: cartRuleId,
    }).then((cartInfo) => {
      this.cartRulesRenderer.render(cartInfo.cartRules, cartInfo.products.length === 0);
    }).catch((response) => {
      if (typeof response.responseJSON.message !== 'undefined') {
        this._displayErrorMessage(response.responseJSON.message);
      }
    });
  }

  /**
   * Displays error message
   *
   * @param message
   *
   * @private
   */
  _displayErrorMessage(message) {
    $(createOrderMap.cartRuleErrorText).text(message);
    this._showErrorBlock();
  }

  /**
   * Shows error block
   *
   * @private
   */
  _showErrorBlock() {
    $(createOrderMap.cartRuleErrorBlock).removeClass('d-none');
  }

  /**
   * Responsible for rendering search results dropdown
   *
   * @param searchResults
   *
   * @private
   */
  _renderSearchResults(searchResults) {
    this._clearSearchResults();
    if (searchResults.cart_rules.length === 0) {
      this._renderNotFound();

      return;
    }
    this._renderFoundCartRules(searchResults.cart_rules);
  }

  /**
   * Renders found cart rules after search
   *
   * @param cartRules
   *
   * @private
   */
  _renderFoundCartRules(cartRules) {
    const $cartRuleTemplate = $($(createOrderMap.foundCartRuleTemplate).html());
    for (const key in cartRules) {
      const $template = $cartRuleTemplate.clone();
      const cartRule = cartRules[key];
      $template.text(cartRule.name);

      $template.data('cart-rule-id', cartRule.cartRuleId);
      this.$searchResultBox.append($template);
    }

    this._showResultsDropdown();
  }

  /**
   * Renders warning that no cart rule was found
   *
   * @private
   */
  _renderNotFound() {
    const $template = $($(createOrderMap.cartRulesNotFoundTemplate).html()).clone();
    this.$searchResultBox.html($template);

    this._showResultsDropdown();
  }

  /**
   * Empties cart rule search results block
   *
   * @private
   */
  _clearSearchResults() {
    this.$searchResultBox.empty();
  }

  /**
   * Displays cart rules search result dropdown
   *
   * @private
   */
  _showResultsDropdown() {
    this.$searchResultBox.removeClass('d-none');
  }

  /**
   * Hides cart rules search result dropdown
   *
   * @private
   */
  _hideResultsDropdown() {
    this.$searchResultBox.addClass('d-none');
  }
}
