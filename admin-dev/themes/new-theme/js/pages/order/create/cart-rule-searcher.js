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

export default class CartRuleSearcher {
  constructor() {
    this.router = new Router();
    this.$searchInput = $(createOrderMap.cartRuleSearchInput);
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

  _search() {
    $.get(this.router.generate('admin_cart_rules_search'), {
      search_phrase: this.$searchInput.val(),
    }).then((cartRules) => {
      this._renderSearchResults(cartRules);
    });
  }

  _addCartRuleToCart(cartRuleId, cartId) {
    $.post(this.router.generate('admin_carts_add_rule', {cartId})).then((cartInfo) => {
      this.cartRulesRenderer.render(cartInfo.cartRules, cartInfo.products.length === 0);
    });
  }

  _renderSearchResults(searchResults) {
    this._clearSearchResults();
    if (searchResults.cart_rules.length === 0) {
      this._renderNotFound();

      return;
    }
    this._renderCartRules(searchResults.cart_rules);
  }

  _renderCartRules(cartRules) {
    const $cartRuleTemplate = $($(createOrderMap.foundCartRuleTemplate).html());
    for (const key in cartRules) {
      const $template = $cartRuleTemplate.clone();
      const cartRule = cartRules[key];
      $template.text(cartRule.name);

      $template.data('cart-rule-id', cartRule.cartRuleId);
      $(createOrderMap.cartRulesSearchResultBox).append($template);
    }

    this._showResultsDropdown();
  }

  _renderNotFound() {
    const $template = $($(createOrderMap.cartRulesNotFoundTemplate).html()).clone();
    $(createOrderMap.cartRulesSearchResultBox).html($template);

    this._showResultsDropdown();
  }

  _clearSearchResults() {
    $(createOrderMap.cartRulesSearchResultBox).empty();
  }

  _showResultsDropdown() {
    $(createOrderMap.cartRulesSearchResultBox).removeClass('d-none');
  }

  _hideResultsDropdown() {
    $(createOrderMap.cartRulesSearchResultBox).addClass('d-none');
  }
}
