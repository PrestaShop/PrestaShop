/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

import OrderProductManager from '@pages/order/view/order-product-manager';
import OrderViewPageMap from '@pages/order/OrderViewPageMap';
import OrderViewEventMap from '@pages/order/view/order-view-event-map';
import {EventEmitter} from '@components/event-emitter';
import OrderDiscountsRefresher from '@pages/order/view/order-discounts-refresher';
import OrderProductRenderer from '@pages/order/view/order-product-renderer';
import OrderPricesRefresher from '@pages/order/view/order-prices-refresher';
import Router from '@components/router';
import OrderInvoicesRefresher from './order-invoices-refresher';
import OrderProductCancel from './order-product-cancel';
import OrderDocumentsRefresher from "./order-documents-refresher";

const $ = window.$;

export default class OrderViewPage {
  constructor() {
    this.orderDiscountsRefresher = new OrderDiscountsRefresher();
    this.orderProductManager = new OrderProductManager();
    this.orderProductRenderer = new OrderProductRenderer();
    this.orderPricesRefresher = new OrderPricesRefresher();
    this.orderDocumentsRefresher = new OrderDocumentsRefresher();
    this.orderInvoicesRefresher = new OrderInvoicesRefresher();
    this.orderProductCancel = new OrderProductCancel();
    this.router = new Router();
    this.listenToEvents();
  }

  listenToEvents() {

    $(OrderViewPageMap.invoiceAddressEditBtn).fancybox({
      'type': 'iframe',
      'width': '90%',
      'height': '90%',
    });
    $(OrderViewPageMap.deliveryAddressEditBtn).fancybox({
      'type': 'iframe',
      'width': '90%',
      'height': '90%',
    });

    EventEmitter.on(OrderViewEventMap.productDeletedFromOrder, (event) => {
      // Remove the row
      const $row = $(OrderViewPageMap.productsTableRow(event.oldOrderDetailId));
      const $next = $row.next();
      $row.remove();
      if ($next.hasClass('order-product-customization')) {
        $next.remove();
      }

      const $tablePagination = $(OrderViewPageMap.productsTablePagination);
      let numPages = $tablePagination.data('numPages');
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
        numPage: currentPage
      });

      this.orderProductRenderer.updateNumProducts(numProducts - 1);
      this.orderPricesRefresher.refresh(event.orderId);
      this.orderDiscountsRefresher.refresh(event.orderId);
      this.orderDocumentsRefresher.refresh(event.orderId);
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
      const $productRow = $(OrderViewPageMap.productsTableRow(event.orderDetailId));
      if ('' === event.newRow) {
        $productRow.remove();
      } else {
        this.orderProductRenderer.addOrUpdateProductToList(
          $productRow,
          event.newRow
        );
      }
      this.orderProductRenderer.resetEditRow(event.orderDetailId);
      this.orderPricesRefresher.refresh(event.orderId);
      this.orderPricesRefresher.refreshProductPrices(event.orderId);
      this.orderDiscountsRefresher.refresh(event.orderId);
      this.orderInvoicesRefresher.refresh(event.orderId);
      this.orderDocumentsRefresher.refresh(event.orderId);
      this.listenForProductDelete();
      this.listenForProductEdit();
      this.resetToolTips();

      const editRowsLeft = $(OrderViewPageMap.productEditRow).not(OrderViewPageMap.productEditRowTemplate).length;
      if (editRowsLeft > 0) {
        return;
      }
      this.orderProductRenderer.moveProductPanelToOriginalPosition();
    });

    EventEmitter.on(OrderViewEventMap.productAddedToOrder, (event) => {
      const $tablePagination = $(OrderViewPageMap.productsTablePagination);
      const numRowsPerPage = $tablePagination.data('numPerPage');
      const initialNumProducts = parseInt($(OrderViewPageMap.productsCount).html(), 10);

      this.orderProductRenderer.addOrUpdateProductToList(
        $(`#${$(event.newRow).find('tr').attr('id')}`),
        event.newRow
      );
      this.listenForProductDelete();
      this.listenForProductEdit();
      this.resetToolTips();

      const newNumProducts = $(OrderViewPageMap.productsTableRows).length;
      const initialPagesNum = Math.max(Math.ceil(initialNumProducts / numRowsPerPage), 1);
      const newPagesNum = Math.ceil(newNumProducts / numRowsPerPage);

      // Update pagination
      if (newPagesNum > initialPagesNum) {
        this.orderProductRenderer.paginationAddPage(newPagesNum);
      }

      this.orderProductRenderer.updateNumProducts(newNumProducts);
      this.orderProductRenderer.resetAddRow();
      this.orderPricesRefresher.refreshProductPrices(event.orderId);
      this.orderPricesRefresher.refresh(event.orderId);
      this.orderDiscountsRefresher.refresh(event.orderId);
      this.orderInvoicesRefresher.refresh(event.orderId);
      this.orderDocumentsRefresher.refresh(event.orderId);
      this.orderProductRenderer.moveProductPanelToOriginalPosition();

      // Move to last page to see the added product
      EventEmitter.emit(OrderViewEventMap.productListPaginated, {
        numPage: newPagesNum
      });
    });
  }

  listenForProductDelete() {
    $(OrderViewPageMap.productDeleteBtn)
      .off('click')
      .on('click', event => {
        this.orderProductManager.handleDeleteProductEvent(event);
      }
    );
  }

  resetToolTips() {
    $(OrderViewPageMap.productEditButtons).pstooltip();
    $(OrderViewPageMap.productDeleteBtn).pstooltip();
  }

  listenForProductEdit() {
    $(OrderViewPageMap.productEditButtons).off('click').on('click', (event) => {
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
        $btn.data('availableOutOfStock'),
        $btn.data('orderInvoiceId'),
      );
    });
  }

  listenForProductPack() {
    $(OrderViewPageMap.productPackModal.modal).on('show.bs.modal', (event) => {
      const button = $(event.relatedTarget);
      const packItems = button.data('packItems');
      const modal = $(OrderViewPageMap.productPackModal.modal);
      $(OrderViewPageMap.productPackModal.rows).remove();
      packItems.forEach(item => {
        const $item = $(OrderViewPageMap.productPackModal.template).clone();
        $item.attr('id', `productpack_${item.id}`).removeClass('d-none');
        $item.find(OrderViewPageMap.productPackModal.product.img).attr('src', item.imagePath);
        $item.find(OrderViewPageMap.productPackModal.product.name).html(item.name);
        $item.find(OrderViewPageMap.productPackModal.product.link).attr('href', this.router.generate('admin_product_form', {'id': item.id}));
        if (item.reference !== '') {
          $item.find(OrderViewPageMap.productPackModal.product.ref).append(item.reference);
        } else {
          $item.find(OrderViewPageMap.productPackModal.product.ref).remove();
        }
        if (item.supplierReference !== '') {
          $item.find(OrderViewPageMap.productPackModal.product.supplierRef).append(item.supplierReference);
        } else {
          $item.find(OrderViewPageMap.productPackModal.product.supplierRef).remove();
        }
        if (item.quantity > 1) {
          $item.find(`${OrderViewPageMap.productPackModal.product.quantity} span`).html(item.quantity);
        } else {
          $item.find(OrderViewPageMap.productPackModal.product.quantity).html(item.quantity);
        }
        $item.find(OrderViewPageMap.productPackModal.product.availableQuantity).html(item.availableQuantity);
        $(OrderViewPageMap.productPackModal.template).before($item);
      });
    });
  }

  listenForProductAdd() {
    $(OrderViewPageMap.productAddBtn).on(
      'click',
      event => {
        this.orderProductRenderer.toggleProductAddNewInvoiceInfo();
        this.orderProductRenderer.moveProductsPanelToModificationPosition(OrderViewPageMap.productSearchInput);
      }
    );
    $(OrderViewPageMap.productCancelAddBtn).on(
      'click', event => this.orderProductRenderer.moveProductPanelToOriginalPosition()
    );
  }

  listenForProductPagination() {
    $(OrderViewPageMap.productsTablePagination).on('click', OrderViewPageMap.productsTablePaginationLink, (event) => {
      event.preventDefault();
      const $btn = $(event.currentTarget);
      EventEmitter.emit(OrderViewEventMap.productListPaginated, {
        numPage: $btn.data('page')
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
        numPage: parseInt($(activePage).html(), 10) + 1
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
        numPage: parseInt($(activePage).html(), 10) - 1
      });
    });
    $(OrderViewPageMap.productsTablePaginationNumberSelector).on('change', (event) => {
      event.preventDefault();
      const $select = $(event.currentTarget);
      const numPerPage = parseInt($select.val(), 10);
      EventEmitter.emit(OrderViewEventMap.productListNumberPerPage, {
        numPerPage: numPerPage
      });
    });

    EventEmitter.on(OrderViewEventMap.productListPaginated, (event) => {
      this.orderProductRenderer.paginate(event.numPage);
      this.listenForProductDelete();
      this.listenForProductEdit();
      this.resetToolTips();
    });

    EventEmitter.on(OrderViewEventMap.productListNumberPerPage, (event) => {
      // Update pagination num per page (page links are regenerated)
      this.orderProductRenderer.updateNumPerPage(event.numPerPage);

      // Paginate to page 1
      EventEmitter.emit(OrderViewEventMap.productListPaginated, {
        numPage: 1
      });

      // Save new config
      $.ajax({
        url: this.router.generate('admin_orders_configure_product_pagination'),
        method: 'POST',
        data: {numPerPage: event.numPerPage},
      });
    });
  }

  listenForRefund() {
    $(OrderViewPageMap.cancelProduct.buttons.partialRefund).on('click', () => {
      this.orderProductRenderer.moveProductsPanelToRefundPosition();
      this.orderProductCancel.showPartialRefund();
    });

    $(OrderViewPageMap.cancelProduct.buttons.standardRefund).on('click', () => {
      this.orderProductRenderer.moveProductsPanelToRefundPosition();
      this.orderProductCancel.showStandardRefund();
    });

    $(OrderViewPageMap.cancelProduct.buttons.returnProduct).on('click', () => {
      this.orderProductRenderer.moveProductsPanelToRefundPosition();
      this.orderProductCancel.showReturnProduct();
    });

    $(OrderViewPageMap.cancelProduct.buttons.abort).on('click', () => {
      this.orderProductRenderer.moveProductPanelToOriginalPosition();
      this.orderProductCancel.hideRefund();
    });
  }

  listenForCancelProduct() {
    $(OrderViewPageMap.cancelProduct.buttons.cancelProducts).on('click', (event) => {
      this.orderProductRenderer.moveProductsPanelToRefundPosition();
      this.orderProductCancel.showCancelProductForm();
    });
  }

  getActivePage() {
    return $(OrderViewPageMap.productsTablePagination).find('.active span').get(0);
  }
}
