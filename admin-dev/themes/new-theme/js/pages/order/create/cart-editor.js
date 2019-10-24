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
import {EventEmitter} from '../../../components/event-emitter';
import eventMap from './event-map';

const $ = window.$;

/**
 * Provides ajax calls for cart editing actions
 * Each method emits an event with updated cart information after success.
 */
export default class CartEditor {
  constructor() {
    this.router = new Router();
  }

  /**
   * Changes cart addresses
   */
  changeCartAddresses(cartId, addresses) {
    $.post(this.router.generate('admin_carts_edit_addresses', {cartId}), addresses).then((cartInfo) => {
      EventEmitter.emit(eventMap.cartAddressesChanged, cartInfo);
    });
  }

  /**
   * Modifies cart delivery option
   *
   * @param cartId
   * @param value
   */
  changeDeliveryOption(cartId, value) {
    $.post(this.router.generate('admin_carts_edit_carrier', {cartId}), {
      carrier_id: value,
    }).then((cartInfo) => {
      EventEmitter.emit(eventMap.cartDeliveryOptionChanged, cartInfo);
    });
  }

  /**
   * Changes cart free shipping value
   *
   * @param {Number} cartId
   * @param {Boolean} value
   */
  setFreeShipping(cartId, value) {
    $.post(this.router.generate('admin_carts_set_free_shipping', {cartId}), {
      free_shipping: value,
    }).then((cartInfo) => {
      EventEmitter.emit(eventMap.cartFreeShippingSet, cartInfo);
    });
  }

  /**
   * Adds cart rule to cart
   *
   * @param cartRuleId
   * @param cartId
   */
  addCartRuleToCart(cartRuleId, cartId) {
    $.post(this.router.generate('admin_carts_add_cart_rule', {cartId}), {
      cart_rule_id: cartRuleId,
    }).then((cartInfo) => {
      EventEmitter.emit(eventMap.cartRuleAdded, cartInfo);
    }).catch((response) => {
      EventEmitter.emit(eventMap.cartRuleFailedToAdd, response.responseJSON.message);
    });
  }

  /**
   * Removes cart rule from cart
   *
   * @param cartRuleId
   * @param cartId
   */
  removeCartRuleFromCart(cartRuleId, cartId) {
    $.post(this.router.generate('admin_carts_delete_cart_rule', {
      cartId,
      cartRuleId,
    })).then((cartInfo) => {
      EventEmitter.emit(eventMap.cartRuleRemoved, cartInfo);
    }).catch((response) => {
      showErrorMessage(response.responseJSON.message);
    });
  }

  /**
   * Adds product to cart
   *
   * @param {Number} cartId
   * @param {FormData} product
   */
  addProduct(cartId, product) {
    $.ajax(this.router.generate('admin_carts_add_product', {cartId}), {
      method: 'POST',
      data: product,
      processData: false,
      contentType: false,
      cache: false,
    }).then((cartInfo) => {
      EventEmitter.emit(eventMap.productAddedToCart, cartInfo);
    }).catch((response) => {
      showErrorMessage(response.responseJSON.message);
    });
  }
}
