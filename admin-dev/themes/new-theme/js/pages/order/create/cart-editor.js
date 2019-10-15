import createOrderPageMap from "./create-order-map";

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
      // this._persistCartInfoData(response);
      EventEmitter.emit('cartAddressesChanged', cartInfo);
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
      EventEmitter.emit('deliveryOptionChanged', cartInfo);
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
      EventEmitter.emit('freeShippingChanged', cartInfo);
    });
  }
}
