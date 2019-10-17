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

import createOrderPageMap from './create-order-map';

const $ = window.$;

/**
 * Renders cart rules (cartRules) block
 */
export default class CartRulesRenderer {
  constructor() {
    this.$cartRulesBlock = $(createOrderPageMap.cartRulesBlock);
    this.$cartRulesTable = $(createOrderPageMap.cartRulesTable);
  }
  /**
   * Responsible for rendering cartRules (a.k.a cart rules/discounts) block
   *
   * @param {Array} cartRules
   * @param {Boolean} emptyCart
   */
  render(cartRules, emptyCart) {
    // do not render cart rules block at all if cart has no products
    if (emptyCart) {
      this._hideCartRulesBlock();
      return;
    }
    this._showCartRulesBlock();


    // do not render cart rules list when there are no cart rules
    if (cartRules.length === 0) {
      this._hideCartRulesList();

      return;
    }

    this._renderList(cartRules);
  }

  /**
   * Responsible for rendering the list of cart rules
   *
   * @param {Array} cartRules
   *
   * @private
   */
  _renderList(cartRules) {
    const $cartRulesTableRowTemplate = $($(createOrderPageMap.cartRulesTableRowTemplate).html());

    for (const key in cartRules) {
      const cartRule = cartRules[key];
      const $template = $cartRulesTableRowTemplate.clone();

      $template.find(createOrderPageMap.cartRuleNameField).text(cartRule.name);
      $template.find(createOrderPageMap.cartRuleDescriptionField).text(cartRule.description);
      $template.find(createOrderPageMap.cartRuleValueField).text(cartRule.value);
      $template.find(createOrderPageMap.cartRuleDeleteBtn).data('cart-rule-id', cartRule.cartRuleId);

      this.$cartRulesTable.find('tbody').append($template);
    }

    this._showCartRulesList();
  }

  /**
   * Shows cartRules block
   *
   * @private
   */
  _showCartRulesBlock() {
    this.$cartRulesBlock.removeClass('d-none');
  }

  /**
   * hide cartRules block
   *
   * @private
   */
  _hideCartRulesBlock() {
    this.$cartRulesBlock.addClass('d-none');
  }

  /**
   * Display the list block of cart rules
   *
   * @private
   */
  _showCartRulesList() {
    this.$cartRulesTable.removeClass('d-none');
  }

  /**
   * Hide list block of cart rules
   *
   * @private
   */
  _hideCartRulesList() {
    this.$cartRulesTable.addClass('d-none');
  }

  /**
   * remove items in cart rules list
   *
   * @private
   */
  _cleanCartRulesList() {
    this.$cartRulesTable.find('tbody').empty();
  }
}
