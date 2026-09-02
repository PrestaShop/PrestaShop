/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import OrderProductManager from '@pages/order/view/order-product-manager';
import OrderViewPageMap from '@pages/order/OrderViewPageMap';
import OrderViewEventMap from '@pages/order/view/order-view-event-map';
import {EventEmitter} from '@components/event-emitter';
import OrderDiscountsRefresher from '@pages/order/view/order-discounts-refresher';
import OrderProductRenderer from '@pages/order/view/order-product-renderer';
import OrderPricesRefresher from '@pages/order/view/order-prices-refresher';
import OrderPaymentsRefresher from '@pages/order/view/order-payments-refresher';
import OrderShippingRefresher from '@pages/order/view/order-shipping-refresher';
import Router from '@components/router';
import OrderProductAutocomplete from '@pages/order/view/order-product-add-autocomplete';
import OrderProductAdd from '@pages/order/view/order-product-add';
import OrderInvoicesRefresher from './order-invoices-refresher';
import OrderProductCancel from './order-product-cancel';
import OrderDocumentsRefresher from './order-documents-refresher';
import OrderShipmentsRefresher from './order-shipments-refresher';

const {$} = window;

export default class OrderViewPage {
  orderDiscountsRefresher: OrderDiscountsRefresher;

  orderProductManager: OrderProductManager;

  orderProductRenderer: OrderProductRenderer;

  orderPricesRefresher: OrderPricesRefresher;

  orderPaymentsRefresher: OrderPaymentsRefresher;

  orderShippingRefresher: OrderShippingRefresher;

  orderShipmentsRefresher: OrderShipmentsRefresher;

  orderDocumentsRefresher: OrderDocumentsRefresher;

  orderInvoicesRefresher: OrderInvoicesRefresher;

  orderProductCancel: OrderProductCancel;

  router: Router;

  isMultishipmentIsEnabled: boolean;

  constructor() {
    this.orderDiscountsRefresher = new OrderDiscountsRefresher();
    this.orderProductManager = new OrderProductManager();
    this.orderProductRenderer = new OrderProductRenderer();
    this.orderPricesRefresher = new OrderPricesRefresher();
    this.orderPaymentsRefresher = new OrderPaymentsRefresher();
    this.orderShippingRefresher = new OrderShippingRefresher();
    this.orderShipmentsRefresher = new OrderShipmentsRefresher();
    this.orderDocumentsRefresher = new OrderDocumentsRefresher();
    this.orderInvoicesRefresher = new OrderInvoicesRefresher();
    this.orderProductCancel = new OrderProductCancel();
    this.router = new Router();
    // eslint-disable-next-line max-len
    this.isMultishipmentIsEnabled = document.querySelector<HTMLElement>(OrderViewPageMap.productsTable)?.dataset.multishipmentEnabled === '1';
    this.listenToEvents();
  }

  listenToEvents(): void {
    $(OrderViewPageMap.invoiceAddressEditBtn).fancybox({
      type: 'iframe',
      width: '90%',
      height: '90%',
    });
    $(OrderViewPageMap.deliveryAddressEditBtn).fancybox({
      type: 'iframe',
      width: '90%',
      height: '90%',
    });

    EventEmitter.on(OrderViewEventMap.productDeletedFromOrder, (event) => {
      this.orderPricesRefresher.refresh(event.orderId);
      this.orderPaymentsRefresher.refresh(event.orderId);
      this.refreshProductsList(event.orderId);
      this.orderDiscountsRefresher.refresh(event.orderId);
      this.orderDocumentsRefresher.refresh(event.orderId);
      this.orderShippingRefresher.refresh(event.orderId);
      this.orderShipmentsRefresher.refresh(event.orderId);
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
      this.orderProductRenderer.resetEditRow(event.orderDetailId);
      this.orderPricesRefresher.refresh(event.orderId);
      this.orderPricesRefresher.refreshProductPrices(event.orderId);
      this.refreshProductsList(event.orderId);
      this.orderPaymentsRefresher.refresh(event.orderId);
      this.orderDiscountsRefresher.refresh(event.orderId);
      this.orderInvoicesRefresher.refresh(event.orderId);
      this.orderDocumentsRefresher.refresh(event.orderId);
      this.orderShippingRefresher.refresh(event.orderId);
      this.listenForProductDelete();
      this.listenForProductEdit();
      this.resetToolTips();

      const editRowsLeft = $(OrderViewPageMap.productEditRow).not(OrderViewPageMap.productEditRowTemplate).length;

      if (editRowsLeft > 0) {
        return;
      }
      this.orderProductRenderer.moveProductPanelToOriginalPosition();
      // Initialize tooltips
      $(OrderViewPageMap.productEditButtons).pstooltip();
      this.orderShipmentsRefresher.refresh(event.orderId);
    });

    EventEmitter.on(OrderViewEventMap.productAddedToOrder, (event) => {
      if (!this.isMultishipmentIsEnabled) {
        this.orderProductRenderer.resetAddRow();
      }
      this.orderPricesRefresher.refreshProductPrices(event.orderId);
      this.orderPricesRefresher.refresh(event.orderId);
      this.refreshProductsList(event.orderId);
      this.orderPaymentsRefresher.refresh(event.orderId);
      this.orderDiscountsRefresher.refresh(event.orderId);
      this.orderInvoicesRefresher.refresh(event.orderId);
      this.orderDocumentsRefresher.refresh(event.orderId);
      this.orderShippingRefresher.refresh(event.orderId);
      this.orderShipmentsRefresher.refresh(event.orderId);
      this.orderProductRenderer.moveProductPanelToOriginalPosition();
    });
  }

  listenForProductDelete(): void {
    $(OrderViewPageMap.productDeleteBtn)
      .off('click')
      .on('click', (event: JQueryEventObject) => this.orderProductManager.handleDeleteProductEvent(event));
  }

  resetToolTips(): void {
    $(OrderViewPageMap.productEditButtons).pstooltip();
    $(OrderViewPageMap.productDeleteBtn).pstooltip();
  }

  listenForProductEdit(): void {
    $(OrderViewPageMap.productEditButtons).off('click').on('click', async (event) => {
      const $btn = $(event.currentTarget);

      if (this.isMultishipmentIsEnabled) {
        await this.getEditProductForm(Number($btn.data('orderDetailId')));
      } else {
        this.orderProductRenderer.moveProductsPanelToModificationPosition();
      }
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
        $btn.data('isOrderTaxIncluded'),
      );
    });
  }

  listenForProductPack(): void {
    $(OrderViewPageMap.productPackModal.modal).on('show.bs.modal', (event: JQueryEventObject) => {
      const button = $(event.relatedTarget);
      const packItems = button.data('packItems');
      $(OrderViewPageMap.productPackModal.rows).remove();
      packItems.forEach((item: Record<string, any>) => {
        const $item = $(OrderViewPageMap.productPackModal.template).clone();
        $item.attr('id', `productpack_${item.id}`).removeClass('d-none');
        $item.find(OrderViewPageMap.productPackModal.product.img).attr('src', item.imagePath);
        $item.find(OrderViewPageMap.productPackModal.product.name).html(item.name);
        $item.find(OrderViewPageMap.productPackModal.product.link).attr(
          'href',
          this.router.generate('admin_products_edit', {productId: item.id}),
        );
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

  listenForProductAdd(): void {
    $(OrderViewPageMap.productAddBtn).on(
      'click',
      () => {
        this.orderProductRenderer.toggleProductAddNewInvoiceInfo();
        if (!this.isMultishipmentIsEnabled) {
          this.orderProductRenderer.moveProductsPanelToModificationPosition(OrderViewPageMap.productSearchInput);
        } else {
          this.getAddProductForm();
        }
      },
    );
    $(OrderViewPageMap.productCancelAddBtn).on(
      'click', () => this.orderProductRenderer.moveProductPanelToOriginalPosition(),
    );
  }

  listenForProductPagination(): void {
    $(OrderViewPageMap.productsTablePagination).on('click', OrderViewPageMap.productsTablePaginationLink, (event) => {
      event.preventDefault();
      const $btn = $(event.currentTarget);
      EventEmitter.emit(OrderViewEventMap.productListPaginated, {
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
        numPage: parseInt($(activePage).html(), 10) - 1,
      });
    });
    $(OrderViewPageMap.productsTablePaginationNumberSelector).on('change', (event) => {
      event.preventDefault();
      const $select = $(event.currentTarget);
      const numPerPage = parseInt(<string>$select.val(), 10);
      EventEmitter.emit(OrderViewEventMap.productListNumberPerPage, {
        numPerPage,
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
        numPage: 1,
      });

      // Save new config
      $.ajax({
        url: this.router.generate('admin_orders_configure_product_pagination'),
        method: 'POST',
        data: {numPerPage: event.numPerPage},
      });
    });
  }

  listenForRefund(): void {
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

  listenForCancelProduct(): void {
    $(OrderViewPageMap.cancelProduct.buttons.cancelProducts).on('click', () => {
      this.orderProductRenderer.moveProductsPanelToRefundPosition();
      this.orderProductCancel.showCancelProductForm();
    });
  }

  getActivePage(): HTMLElement {
    return $(OrderViewPageMap.productsTablePagination).find('.active span').get(0)!;
  }

  refreshProductsList(orderId: number): void {
    $(OrderViewPageMap.refreshProductsListLoadingSpinner).show();

    const $tablePagination = $(OrderViewPageMap.productsTablePagination);
    const numRowsPerPage = $tablePagination.data('numPerPage');
    const initialNumProducts = $(OrderViewPageMap.productsTableRows).length;
    const currentPage = parseInt($(OrderViewPageMap.productsTablePaginationActive).html(), 10);

    $.ajax(this.router.generate('admin_orders_get_products', {orderId}))
      .done((response) => {
        // Delete previous product lines
        $(OrderViewPageMap.productsTable).find(OrderViewPageMap.productsTableRows).remove();
        $(OrderViewPageMap.productsTableCustomizationRows).remove();

        $(`${OrderViewPageMap.productsTable} tbody`).prepend(response);

        $(OrderViewPageMap.refreshProductsListLoadingSpinner).hide();

        const newNumProducts = $(OrderViewPageMap.productsTableRows).length;
        const newPagesNum = Math.ceil(newNumProducts / numRowsPerPage);

        this.orderProductRenderer.updateNumProducts(newNumProducts);
        this.orderProductRenderer.updatePaginationControls();

        let numPage = 1;
        let message = '';

        // Display alert
        if (initialNumProducts > newNumProducts) { // product deleted
          message = (initialNumProducts - newNumProducts === 1)
            ? window.translate_javascripts['The product was successfully removed.']
            : window.translate_javascripts['[1] products were successfully removed.']
              .replace('[1]', (initialNumProducts - newNumProducts));

          // Set target page to the page of the deleted item
          numPage = (newPagesNum === 1) ? 1 : currentPage;
        } else if (initialNumProducts < newNumProducts) { // product added
          message = (newNumProducts - initialNumProducts === 1)
            ? window.translate_javascripts['The product was successfully added.']
            : window.translate_javascripts['[1] products were successfully added.']
              .replace('[1]', (newNumProducts - initialNumProducts));

          // Move to first page to see the added product
          numPage = 1;
        }

        if (message !== '') {
          $.growl.notice({
            title: '',
            message,
          });
        }

        // Move to page of the modified item
        EventEmitter.emit(OrderViewEventMap.productListPaginated, {
          numPage,
        });

        // Bind hover on product rows buttons
        this.resetToolTips();
      })
      .fail(() => {
        $.growl.error({
          title: '',
          message: 'Failed to reload the products list. Please reload the page',
        });
      });
  }

  modal(type: 'add' | 'edit'): HTMLDivElement {
    let container = '';

    if (type === 'add') {
      container = OrderViewPageMap.productAddModal;
    } else {
      container = OrderViewPageMap.productEditModal;
    }

    const modal = document.querySelector(container) as HTMLDivElement;

    if (!modal) {
      throw new Error(`${type} product modal not found`);
    }
    return modal;
  }

  async getAddProductForm(): Promise<void> {
    const modal = this.modal('add');
    modal.dataset.state = 'loading';
    const orderId = Number(modal.dataset.orderId);

    try {
      const response = await fetch(this.router.generate('admin_orders_get_add_product_form', {
        orderId,
      }), {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
        },
      });

      if (!response.ok) {
        throw new Error(await response.text());
      }
      const formContainer = document.querySelector<HTMLElement>(OrderViewPageMap.addProductModalContainer);
      formContainer!.innerHTML = await response.text();

      const orderAddAutocomplete = new OrderProductAutocomplete($(OrderViewPageMap.productSearchInput));
      const orderAdd = new OrderProductAdd();

      orderAddAutocomplete.listenForSearch();
      orderAddAutocomplete.onItemClickedCallback = (p: Record<string, any> | undefined): void => orderAdd.setProduct(p);

      modal.addEventListener('hidden.bs.modal', () => orderAddAutocomplete.removeListener(), {once: true});
      modal.dataset.state = 'loaded';
    } catch (error) {
      console.error(error);
    }
  }

  async getEditProductForm(orderDetailId: number): Promise<void> {
    const modal = this.modal('edit');
    modal.dataset.state = 'loading';
    const orderId = Number(modal.dataset.orderId);

    try {
      const response = await fetch(this.router.generate('admin_orders_get_edit_product_form', {
        orderId,
        orderDetailId,
      }), {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
        },
      });

      if (!response.ok) {
        throw new Error(await response.text());
      }
      const formContainer = document.querySelector(OrderViewPageMap.editProductModalContainer) as HTMLElement;
      formContainer!.innerHTML = await response.text();
      modal.dataset.state = 'loaded';
    } catch (error) {
      console.error(error);
    }
  }
}
