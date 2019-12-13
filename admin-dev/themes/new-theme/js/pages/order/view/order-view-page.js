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
      const $tablePagination = $(OrderViewPageMap.productsTablePagination);
      const numPages = $tablePagination.data('numPages');
      const numRowsPerPage = $tablePagination.data('numPerPage');
      const numRows = $(OrderViewPageMap.productsTable).find('tr[id^="orderProduct_"]').length;
      let currentPage = parseInt($(OrderViewPageMap.productsTablePaginationActive).html(), 10);
      const numProducts = parseInt($(OrderViewPageMap.productsCount).html(), 10);
      if ((numProducts - 1) % numRowsPerPage === 0) {
        this.orderProductRenderer.paginationRemovePage(numPages);
      }
      if (numRows === 1 && currentPage === numPages) {
        currentPage -= 1;
      }
      this.orderProductManager.paginate(
        event.orderId,
        currentPage
      );

      this.orderProductRenderer.updateNumProducts(numProducts - 1);
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
        $btn.data('orderDetailId'),
        $btn.data('productQuantity'),
        $btn.data('productPriceTaxIncl'),
        $btn.data('productPriceTaxExcl'),
        $btn.data('taxRate'),
        $btn.data('location'),
        $btn.data('availableQuantity'),
      );
    });
    $(OrderViewPageMap.productCancelEditBtn).on('click', event => {
      const $btn = $(event.currentTarget);
      this.orderProductRenderer.resetEditRow($btn.data('orderDetailId'));
      this.orderProductRenderer.moveProductPanelToOriginalPosition();
    });

    EventEmitter.on(OrderViewEventMap.productEditedToOrder, (event) => {
      this.orderProductRenderer.addOrUpdateProductToList(event.orderDetailId, event.newRow);
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
      const $tablePagination = $(OrderViewPageMap.productsTablePagination);
      const numRowsPerPage = $tablePagination.data('numPerPage');
      const numProducts = parseInt($(OrderViewPageMap.productsCount).html(), 10);
      if ($(OrderViewPageMap.productsTable).find('tr[id^="orderProduct_"]').length < numRowsPerPage) {
        this.orderProductRenderer.addOrUpdateProductToList(event.orderProductId, event.newRow);
      } else {
        // Update pagination
        let numPages = $tablePagination.data('numPages');
        if (numProducts % numRowsPerPage === 0) {
          console.log('There');
          numPages += 1;
          this.orderProductRenderer.paginationAddPage(numPages);
        }
        // Move to last page
        this.orderProductManager.paginate(
          event.orderId,
          numPages
        );
      }
      this.orderProductRenderer.updateNumProducts(numProducts + 1);
      this.orderProductRenderer.resetAddRow();
      this.orderPricesRefresher.refresh(event.orderId);
      this.orderProductRenderer.moveProductPanelToOriginalPosition();
      this.listenForProductDelete();
      this.listenForProductEdit();
    });
  }

  listenForProductPagination() {
    $(OrderViewPageMap.productsTablePagination).on('click', OrderViewPageMap.productsTablePaginationLink, (event) => {
      event.preventDefault();
      const $btn = $(event.currentTarget);
      this.orderProductManager.paginate(
        $btn.data('orderId'),
        $btn.data('page')
      );
    });
    $(OrderViewPageMap.productsTablePaginationNext).on('click', (event) => {
      event.preventDefault();
      const $btn = $(event.currentTarget);
      if ($btn.hasClass('disabled')) {
        return;
      }
      const activePage = this.getActivePage();
      this.orderProductManager.paginate(
        $(activePage).data('orderId'),
        parseInt($(activePage).html(), 10) + 1
      );
    });
    $(OrderViewPageMap.productsTablePaginationPrev).on('click', (event) => {
      event.preventDefault();
      const $btn = $(event.currentTarget);
      if ($btn.hasClass('disabled')) {
        return;
      }
      const activePage = this.getActivePage();
      this.orderProductManager.paginate(
        $(activePage).data('orderId'),
        parseInt($(activePage).html(), 10) - 1
      );
    });

    EventEmitter.on(OrderViewEventMap.productListPaginated, (event) => {
      this.orderProductRenderer.paginate(event.orderId, event.numPage, event.results);
      this.listenForProductDelete();
      this.listenForProductEdit();
    });
  }

  getActivePage() {
    return $(OrderViewPageMap.productsTablePagination).find('.active span').get(0);
  }
}
