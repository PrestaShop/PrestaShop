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

import OrderProductManager from './order-product-manager';
import OrderViewPageMap from '../OrderViewPageMap';
import OrderViewEventMap from '../view/order-view-event-map';
import {EventEmitter} from '../../../components/event-emitter';
import OrderProductRenderer from './order-product-renderer';
import OrderPricesRefresher from './order-prices-refresher';

const $ = window.$;

export default class OrderViewPage {
  constructor() {
    this.orderProductManager = new OrderProductManager();
    this.orderProductRenderer = new OrderProductRenderer();
    this.orderPricesRefresher = new OrderPricesRefresher();
  }

  listenForProductDelete() {
    $(OrderViewPageMap.productDeleteBtn).unbind('click').on('click', event => this.orderProductManager.handleDeleteProductEvent(event));

    const callback = (event) => {
      this.orderProductRenderer.removeProductFromList(event.oldOrderDetailId);
      this.orderPricesRefresher.refresh(event.orderId);
    };

    EventEmitter.off(OrderViewEventMap.productDeletedFromOrder, callback)
      .on(OrderViewEventMap.productDeletedFromOrder, callback);
  }

  listenForProductEdit() {
    $(OrderViewPageMap.productEditBtn).on('click', event => {
      const $btn = $(event.currentTarget);
      this.orderProductRenderer.moveProductsPanelToModificationPosition();
      this.orderProductRenderer.editProductFromToList(
        $btn.attr('data-order-detail-id'),
        $btn.attr('data-product-quantity'),
        $btn.attr('data-product-price-tax-incl'),
        $btn.attr('data-product-price-tax-excl'),
        $btn.attr('data-tax-rate'),
      );
    });
    $(OrderViewPageMap.productCancelEditBtn).on('click', event => {
      const $btn = $(event.currentTarget);
      this.orderProductRenderer.resetEditRow($btn.attr('data-order-detail-id'));
      this.orderProductRenderer.moveProductPanelToOriginalPosition();
    });

    EventEmitter.on(OrderViewEventMap.productEditedToOrder, (event) => {
      this.orderProductRenderer.addOrUpdateProductFromToList(event.orderDetailId, event.newRow);
      this.orderProductRenderer.resetEditRow(event.orderDetailId);
      this.orderPricesRefresher.refresh(event.orderId);
      this.orderProductRenderer.moveProductPanelToOriginalPosition();
      this.listenForProductDelete();
      this.listenForProductEdit();
    });
  }

  listenForProductAdd() {
    $(OrderViewPageMap.productAddBtn).on('click', event => this.orderProductRenderer.moveProductsPanelToModificationPosition());
    $(OrderViewPageMap.productCancelAddBtn).on('click', event => this.orderProductRenderer.moveProductPanelToOriginalPosition());

    EventEmitter.on(OrderViewEventMap.productAddedToOrder, (event) => {
      this.orderProductRenderer.addOrUpdateProductFromToList(event.orderProductId, event.newRow);
      this.orderProductRenderer.resetAddRow();
      this.orderPricesRefresher.refresh(event.orderId);
      this.orderProductRenderer.moveProductPanelToOriginalPosition();
      this.listenForProductDelete();
      this.listenForProductEdit();
    });
  }
}
