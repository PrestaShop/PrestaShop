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
import OrderViewEventMap from '../view/order-view-event-map';

const $ = window.$;

export default class OrderProductManager {
  constructor() {
    this.router = new Router();
  }

  handleDeleteProductEvent(event) {
    event.preventDefault();

    const $btn = $(event.currentTarget);

    const confirmed = confirm($btn.data('deleteMessage'));

    if (!confirmed) {
      return;
    }

    $btn.attr('disabled', 'disabled');
    this.deleteProduct($btn.data('orderId'), $btn.data('orderDetailId'));
  }

  deleteProduct(orderId, orderDetailId) {
    $.ajax(this.router.generate('admin_orders_delete_product', {orderId, orderDetailId}), {
      method: 'POST',
    }).then(() => {
      EventEmitter.emit(OrderViewEventMap.productDeletedFromOrder, {
        oldOrderDetailId: orderDetailId,
        orderId,
      });
    }, (response) => {
      if (response.message) {
        $.growl.error({message: response.message});
      }
    });
  }

  paginate(orderId, numPage) {
    const numRows = 8;
    const offset = (numPage - 1) * numRows;
    $.ajax(this.router.generate('admin_orders_get_paginated_products', {orderId}), {
      method: 'GET',
      data: {
        offset,
        limit: numRows,
      },
    }).then((results) => {
      EventEmitter.emit(OrderViewEventMap.productListPaginated, {
        orderId,
        numPage,
        results,
      });
    });
  }
}
