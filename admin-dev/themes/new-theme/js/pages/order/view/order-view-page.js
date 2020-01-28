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

import OrderProductManager from '@pages/order/view/order-product-manager';
import OrderViewPageMap from '@pages/order/OrderViewPageMap';
import OrderViewEventMap from '@pages/order/view/order-view-event-map';
import {EventEmitter} from '@components/event-emitter';
import OrderProductRenderer from '@pages/order/view/order-product-renderer';
import OrderPricesRefresher from '@pages/order/view/order-prices-refresher';
import OrderInvoicesRefresher from './order-invoices-refresher';

const {$} = window;

export default class OrderViewPage {
  constructor() {
    this.orderProductManager = new OrderProductManager();
    this.orderProductRenderer = new OrderProductRenderer();
    this.orderPricesRefresher = new OrderPricesRefresher();
    this.orderInvoicesRefresher = new OrderInvoicesRefresher();
    this.listenToEvents();
  }

  listenToEvents() {
    EventEmitter.on(OrderViewEventMap.productDeletedFromOrder, (event) => {
      // Remove the row
      $(OrderViewPageMap.productsTableRow(event.oldOrderDetailId)).remove();

      const $tablePagination = $(OrderViewPageMap.productsTablePagination);
      const numPages = $tablePagination.data('numPages');
      const numRowsPerPage = $tablePagination.data('numPerPage');
      const numRows = $(OrderViewPageMap.productsTable).find('tr[id^="orderProduct_"]:not(.d-none)').length;
      let currentPage = parseInt($(OrderViewPageMap.productsTablePaginationActive).html(), 10);
      const numProducts = parseInt($(OrderViewPageMap.productsCount).html(), 10);
      if ((numProducts - 1) % numRowsPerPage === 0) {
        this.orderProductRenderer.paginationRemovePage(numPages);
      }
      if (numRows === 1 && currentPage === numPages) {
        currentPage -= 1;
      }
      EventEmitter.emit(OrderViewEventMap.productListPaginated, {
        orderId: event.orderId,
        numPage: currentPage,
      });

      this.orderProductRenderer.updateNumProducts(numProducts - 1);
      this.orderPricesRefresher.refresh(event.orderId);
    });

    EventEmitter.on(OrderViewEventMap.productEditionCanceled, (event) => {
      this.orderProductRenderer.resetEditRow(event.orderDetailId);
      const editRowsLeft = $(OrderViewPageMap.productEditRow).not(OrderViewPageMap.productEditRowTemplate).length;
      if (editRowsLeft > 0) {
        return;
      }
      this.orderProductRenderer.moveProductPanelToOriginalPosition();
    });

    EventEmitter.on(OrderViewEventMap.productUpdated, (event) => {
      this.orderProductRenderer.addOrUpdateProductToList(
        $(OrderViewPageMap.productsTableRow(event.orderDetailId)),
        event.newRow,
      );
      this.orderProductRenderer.resetEditRow(event.orderDetailId);
      this.orderPricesRefresher.refresh(event.orderId);
      this.listenForProductDelete();
      this.listenForProductEdit();

      const editRowsLeft = $(OrderViewPageMap.productEditRow).not(OrderViewPageMap.productEditRowTemplate).length;
      if (editRowsLeft > 0) {
        return;
      }
      this.orderProductRenderer.moveProductPanelToOriginalPosition();
    });

    EventEmitter.on(OrderViewEventMap.productAddedToOrder, (event) => {
      const $tablePagination = $(OrderViewPageMap.productsTablePagination);
      const numRowsPerPage = $tablePagination.data('numPerPage');
      const numProducts = parseInt($(OrderViewPageMap.productsCount).html(), 10);

      this.orderProductRenderer.addOrUpdateProductToList(
        $(`#${$(event.newRow).find('tr').attr('id')}`),
        event.newRow,
      );
      this.listenForProductDelete();
      this.listenForProductEdit();

      if ($(OrderViewPageMap.productsTable).find('tr[id^="orderProduct_"]:not(.d-none)').length >= numRowsPerPage) {
        // Update pagination
        let numPages = $tablePagination.data('numPages');
        if (numProducts % numRowsPerPage === 0) {
          numPages += 1;
          this.orderProductRenderer.paginationAddPage(numPages);
        }
        // Move to last page
        EventEmitter.emit(OrderViewEventMap.productListPaginated, {
          orderId: event.orderId,
          numPage: numPages,
        });
      }
      this.orderProductRenderer.updateNumProducts(numProducts + 1);
      this.orderProductRenderer.resetAddRow();
      this.orderPricesRefresher.refresh(event.orderId);
      this.orderInvoicesRefresher.refresh(event.orderId);
      this.orderProductRenderer.moveProductPanelToOriginalPosition();
    });
  }

  listenForProductDelete() {
    $(OrderViewPageMap.productDeleteBtn)
      .off('click')
      .on('click', (event) => this.orderProductManager.handleDeleteProductEvent(event));
  }

  listenForProductEdit() {
    $(OrderViewPageMap.productEditBtn).off('click').on('click', (event) => {
      const $btn = $(event.currentTarget);
      this.orderProductRenderer.moveProductsPanelToModificationPosition();
      this.orderProductRenderer.editProductFromList(
        $btn.data('orderDetailId'),
        $btn.data('productQuantity'),
        $btn.data('productPriceTaxIncl'),
        $btn.data('productPriceTaxExcl'),
        $btn.data('taxRate'),
        $btn.data('location'),
        $btn.data('availableQuantity'),
        $btn.data('orderInvoiceId'),
      );
    });
  }

  listenForProductAdd() {
    $(OrderViewPageMap.productAddBtn).on(
      'click',
      () => {
        this.orderProductRenderer.toggleProductAddNewInvoiceInfo();
        this.orderProductRenderer.moveProductsPanelToModificationPosition(OrderViewPageMap.productSearchInput);
      },
    );
    $(OrderViewPageMap.productCancelAddBtn).on(
      'click', () => this.orderProductRenderer.moveProductPanelToOriginalPosition(),
    );
  }

  listenForProductPagination() {
    $(OrderViewPageMap.productsTablePagination).on('click', OrderViewPageMap.productsTablePaginationLink, (event) => {
      event.preventDefault();
      const $btn = $(event.currentTarget);
      EventEmitter.emit(OrderViewEventMap.productListPaginated, {
        orderId: $btn.data('orderId'),
        numPage: $btn.data('page'),
      });
    });
    $(OrderViewPageMap.productsTablePaginationNext).on('click', (event) => {
      event.preventDefault();
      const $btn = $(event.currentTarget);
      if ($btn.hasClass('disabled')) {
        return;
      }
      const activePage = this.getActivePage();
      EventEmitter.emit(OrderViewEventMap.productListPaginated, {
        orderId: $(activePage).data('orderId'),
        numPage: parseInt($(activePage).html(), 10) + 1,
      });
    });
    $(OrderViewPageMap.productsTablePaginationPrev).on('click', (event) => {
      event.preventDefault();
      const $btn = $(event.currentTarget);
      if ($btn.hasClass('disabled')) {
        return;
      }
      const activePage = this.getActivePage();
      EventEmitter.emit(OrderViewEventMap.productListPaginated, {
        orderId: $(activePage).data('orderId'),
        numPage: parseInt($(activePage).html(), 10) - 1,
      });
    });

    EventEmitter.on(OrderViewEventMap.productListPaginated, (event) => {
      this.orderProductRenderer.paginate(event.numPage);
      this.listenForProductDelete();
      this.listenForProductEdit();
    });
  }

  getActivePage() {
    return $(OrderViewPageMap.productsTablePagination).find('.active span').get(0);
  }
}
