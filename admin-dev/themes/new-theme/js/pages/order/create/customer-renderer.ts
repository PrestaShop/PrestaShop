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

import createOrderMap from '@pages/order/create/create-order-map';
import Router from '@components/router';
import eventMap from '@pages/order/create/event-map';
import {EventEmitter} from '@components/event-emitter';

const {$} = window;

/* eslint-disable camelcase */
export interface CustomerGroup {
  id_group: string;
  name: string;
  default: boolean;
}

export interface CustomerResult {
  [key: number]: any;
  firstname: string,
  lastname: string,
  email: string,
  groups: Record<number, CustomerGroup>;
  birthday: string;
  company: string;
}
/* eslint-disable camelcase */

/**
 * Responsible for customer information rendering
 */
export default class CustomerRenderer {
  $container: JQuery;

  $customerSearchResultBlock: JQuery;

  router: Router;

  constructor() {
    this.$container = $(createOrderMap.customerSearchBlock);
    this.$customerSearchResultBlock = $(
      createOrderMap.customerSearchResultsBlock,
    );
    this.router = new Router();
  }

  /**
   * Renders customer search results
   *
   * @param foundCustomers
   */
  renderSearchResults(foundCustomers: Record<number, CustomerResult>): void {
    if (Object.entries(foundCustomers).length === 0) {
      EventEmitter.emit(eventMap.customersNotFound);

      return;
    }

    Object.entries(foundCustomers).forEach(([customerId, customerResult]) => {
      const customer = {
        id: customerId,
        firstName: customerResult.firstname,
        lastName: customerResult.lastname,
        email: customerResult.email,
        groups: '',
        birthday:
          customerResult.birthday !== '0000-00-00'
            ? customerResult.birthday
            : ' ',
        company: customerResult.company,
      };

      if (Object.keys(customerResult.groups).length > 1) {
        const output: string[] = [];

        Object.values(customerResult.groups).forEach((customerGroup) => {
          if (customerGroup.default === true) {
            output.push(`${customerGroup.name} (${window.translate_javascripts['Customer search - group default']})`);
          } else {
            output.push(customerGroup.name);
          }
        });
        customer.groups = `${window.translate_javascripts['Customer search - group label multiple']}: ${output.join(', ')}`;
      } else if (Object.keys(customerResult.groups).length > 0) {
        // eslint-disable-next-line
        customer.groups = `${window.translate_javascripts['Customer search - group label single']}: ${Object.values(customerResult.groups)[0].name}`;
      }

      this.renderFoundCustomer(customer);
    });

    // Show customer details in fancy box
    $(createOrderMap.customerDetailsBtn).fancybox({
      type: 'iframe',
      width: '90%',
      height: '90%',
    });
  }

  /**
   * Responsible for displaying customer block after customer select
   *
   * @param $targetedBtn
   */
  displaySelectedCustomerBlock($targetedBtn: JQuery): void {
    this.showCheckoutHistoryBlock();

    $targetedBtn.addClass('d-none');

    const $customerCard = $targetedBtn.closest('.card');

    $customerCard.addClass('border-success');
    $customerCard.find(createOrderMap.changeCustomerBtn).removeClass('d-none');

    this.$container.find(createOrderMap.customerSearchRow).addClass('d-none');
    this.$container
      .find(createOrderMap.notSelectedCustomerSearchResults)
      .closest(createOrderMap.customerSearchResultColumn)
      .remove();

    // Initial display of the customer, the cart is gonna be created then customer's carts
    // and orders are going to be fetched, but we can display the loading messages right now
    this.showLoadingCarts();
    this.showLoadingOrders();
  }

  /**
   * Shows customer search block
   */
  showCustomerSearch(): void {
    this.$container
      .find(createOrderMap.customerSearchRow)
      .removeClass('d-none');
  }

  /**
   * Empty the cart list and display a loading message.
   */
  showLoadingCarts(): void {
    const $cartsTable = $(createOrderMap.customerCartsTable);
    $cartsTable.find('tbody').empty();
    this.renderLoading($cartsTable);
  }

  /**
   * Renders customer carts list
   *
   * @param {Array} carts
   * @param {Int} currentCartId
   */
  renderCarts(carts: Record<string, any>, currentCartId: string): void {
    const $cartsTable = $(createOrderMap.customerCartsTable);
    const $cartsTableRowTemplate = $(
      $(createOrderMap.customerCartsTableRowTemplate).html(),
    );

    $cartsTable.find('tbody').empty();
    this.showCheckoutHistoryBlock();
    this.removeEmptyListRowFromTable($cartsTable);

    Object.values(carts).forEach((cart) => {
      // do not render current cart
      if (cart.cartId === currentCartId) {
        // render 'No records found' warn if carts only contain current cart
        if (carts.length === 1) {
          this.renderEmptyList($cartsTable);
        }

        return;
      }

      const $cartsTableRow = $cartsTableRowTemplate.clone();

      $cartsTableRow.find(createOrderMap.cartIdField).text(cart.cartId);
      $cartsTableRow.find(createOrderMap.cartDateField).text(cart.creationDate);
      $cartsTableRow.find(createOrderMap.cartTotalField).text(cart.totalPrice);
      $cartsTableRow.find(createOrderMap.cartDetailsBtn).prop(
        'href',
        this.router.generate('admin_carts_view', {
          cartId: cart.cartId,
          liteDisplaying: 1,
        }),
      );

      $cartsTableRow
        .find(createOrderMap.useCartBtn)
        .data('cart-id', cart.cartId);

      $cartsTable.find('thead').removeClass('d-none');
      $cartsTable.find('tbody').append($cartsTableRow);
    });

    // Show cart details in fancy box
    $(createOrderMap.cartDetailsBtn).fancybox({
      type: 'iframe',
      width: '90%',
      height: '90%',
    });
  }

  /**
   * Empty the order list and display a loading message.
   */
  showLoadingOrders(): void {
    const $ordersTable = $(createOrderMap.customerOrdersTable);
    $ordersTable.find('tbody').empty();
    this.renderLoading($ordersTable);
  }

  /**
   * Renders customer orders list
   *
   * @param {Array} orders
   */
  renderOrders(orders: Record<string, any>): void {
    const $ordersTable = $(createOrderMap.customerOrdersTable);
    const $rowTemplate = $(
      $(createOrderMap.customerOrdersTableRowTemplate).html(),
    );

    $ordersTable.find('tbody').empty();
    this.showCheckoutHistoryBlock();
    this.removeEmptyListRowFromTable($ordersTable);

    // render 'No records found' when list is empty
    if (orders.length === 0) {
      this.renderEmptyList($ordersTable);

      return;
    }

    Object.values(orders).forEach((order) => {
      const $template = $rowTemplate.clone();

      $template.find(createOrderMap.orderIdField).text(order.orderId);
      $template.find(createOrderMap.orderDateField).text(order.orderPlacedDate);
      $template
        .find(createOrderMap.orderProductsField)
        .text(order.orderProductsCount);
      $template.find(createOrderMap.orderTotalField).text(order.totalPaid);
      $template
        .find(createOrderMap.orderPaymentMethod)
        .text(order.paymentMethodName);
      $template.find(createOrderMap.orderStatusField).text(order.orderStatus);
      $template.find(createOrderMap.orderDetailsBtn).prop(
        'href',
        this.router.generate('admin_orders_view', {
          orderId: order.orderId,
          liteDisplaying: 1,
        }),
      );

      $template
        .find(createOrderMap.useOrderBtn)
        .data('order-id', order.orderId);

      $ordersTable.find('thead').removeClass('d-none');
      $ordersTable.find('tbody').append($template);
    });

    // Show order details in fancy box
    $(createOrderMap.orderDetailsBtn).fancybox({
      type: 'iframe',
      width: '90%',
      height: '90%',
    });
  }

  /**
   * Shows empty result when customer is not found
   */
  showNotFoundCustomers(): void {
    $(createOrderMap.customerSearchEmptyResultWarning).removeClass('d-none');
  }

  /**
   * Hides not found customers warning
   */
  hideNotFoundCustomers(): void {
    $(createOrderMap.customerSearchEmptyResultWarning).addClass('d-none');
  }

  /**
   * Hides checkout history block where carts and orders are rendered
   */
  hideCheckoutHistoryBlock(): void {
    $(createOrderMap.customerCheckoutHistory).addClass('d-none');
  }

  /**
   * Shows searching customers notice during request
   */
  showSearchingCustomers(): void {
    $(createOrderMap.customerSearchLoadingNotice).removeClass('d-none');
  }

  /**
   * Hide searching notice
   */
  hideSearchingCustomers(): void {
    $(createOrderMap.customerSearchLoadingNotice).addClass('d-none');
  }

  /**
   * Renders 'No records' warning in list
   *
   * @param $table
   *
   * @private
   */
  private renderEmptyList($table: JQuery): void {
    const $emptyTableRow = $(
      $(createOrderMap.emptyListRowTemplate).html(),
    ).clone();
    $table.find('tbody').append($emptyTableRow);
  }

  /**
   * Renders 'Loading' message in list
   *
   * @param $table
   *
   * @private
   */
  private renderLoading($table: JQuery): void {
    const $emptyTableRow = $(
      $(createOrderMap.loadingListRowTemplate).html(),
    ).clone();
    $table.find('tbody').append($emptyTableRow);
  }

  /**
   * Removes empty list row in case it was rendered
   */
  removeEmptyListRowFromTable($table: JQuery): void {
    $table.find(createOrderMap.emptyListRow).remove();
  }

  /**
   * Renders customer information after search action
   *
   * @param {Object} customer
   *
   * @return {jQuery}
   *
   * @private
   */
  private renderFoundCustomer(customer: Record<string, any>): JQuery {
    this.hideNotFoundCustomers();

    const $customerSearchResultTemplate = $(
      $(createOrderMap.customerSearchResultTemplate).html(),
    );
    const $template = $customerSearchResultTemplate.clone();

    $template
      .find(createOrderMap.customerSearchResultName)
      .text(`${customer.firstName} ${customer.lastName}`);
    $template
      .find(createOrderMap.customerSearchResultEmail)
      .text(customer.email);
    $template.find(createOrderMap.customerSearchResultId).text(customer.id);
    $template.find(createOrderMap.customerSearchResultGroups).text(customer.groups);
    $template
      .find(createOrderMap.customerSearchResultBirthday)
      .text(customer.birthday);
    $template
      .find(createOrderMap.customerSearchResultCompany)
      .text(customer.company);
    $template
      .find(createOrderMap.chooseCustomerBtn)
      .data('customer-id', customer.id);
    $template.find(createOrderMap.customerDetailsBtn).prop(
      'href',
      this.router.generate('admin_customers_view', {
        customerId: customer.id,
        liteDisplaying: 1,
      }),
    );

    return this.$customerSearchResultBlock.append($template);
  }

  /**
   * Shows checkout history block where carts and orders are rendered
   *
   * @private
   */
  private showCheckoutHistoryBlock(): void {
    $(createOrderMap.customerCheckoutHistory).removeClass('d-none');
  }

  /**
   * Clears shown customers
   */
  clearShownCustomers(): void {
    this.$customerSearchResultBlock.empty();
  }
}
