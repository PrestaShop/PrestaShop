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

import createOrderMap from '@pages/order/create/create-order-map';
import Router from '@components/router';

const {$} = window;

/**
 * Responsible for customer information rendering
 */
export default class CustomerRenderer {
  constructor() {
    this.$container = $(createOrderMap.customerSearchBlock);
    this.$customerSearchResultBlock = $(createOrderMap.customerSearchResultsBlock);
    this.router = new Router();
  }

  /**
   * Renders customer search results
   *
   * @param foundCustomers
   */
  renderSearchResults(foundCustomers) {
    this._clearShownCustomers();

    if (foundCustomers.length === 0) {
      this._showNotFoundCustomers();

      return;
    }

    for (const customerId in foundCustomers) {
      const customerResult = foundCustomers[customerId];
      const customer = {
        id: customerId,
        firstName: customerResult.firstname,
        lastName: customerResult.lastname,
        email: customerResult.email,
        birthday: customerResult.birthday !== '0000-00-00' ? customerResult.birthday : ' ',
      };

      this._renderFoundCustomer(customer);
    }
  }

  /**
   * Responsible for displaying customer block after customer select
   *
   * @param $targetedBtn
   */
  displaySelectedCustomerBlock($targetedBtn) {
    $targetedBtn.addClass('d-none');

    const $customerCard = $targetedBtn.closest('.card');

    $customerCard.addClass('border-success');
    $customerCard.find(createOrderMap.changeCustomerBtn).removeClass('d-none');

    this.$container.find(createOrderMap.customerSearchRow).addClass('d-none');
    this.$container.find(createOrderMap.notSelectedCustomerSearchResults)
      .closest(createOrderMap.customerSearchResultColumn)
      .remove();
  }

  /**
   * Shows customer search block
   */
  showCustomerSearch() {
    this.$container.find(createOrderMap.customerSearchRow).removeClass('d-none');
  }

  /**
   * Renders customer carts list
   *
   * @param {Array} carts
   * @param {Int} currentCartId
   */
  renderCarts(carts, currentCartId) {
    const $cartsTable = $(createOrderMap.customerCartsTable);
    const $cartsTableRowTemplate = $($(createOrderMap.customerCartsTableRowTemplate).html());

    $cartsTable.find('tbody').empty();
    this._showCheckoutHistoryBlock();
    this._removeEmptyListRowFromTable($cartsTable);

    for (const key in carts) {
      const cart = carts[key];

      // do not render current cart
      if (cart.cartId === currentCartId) {
        // render 'No records found' warn if carts only contain current cart
        if (carts.length === 1) {
          this._renderEmptyList($cartsTable);
        }

        continue;
      }

      const $cartsTableRow = $cartsTableRowTemplate.clone();

      $cartsTableRow.find(createOrderMap.cartIdField).text(cart.cartId);
      $cartsTableRow.find(createOrderMap.cartDateField).text(cart.creationDate);
      $cartsTableRow.find(createOrderMap.cartTotalField).text(cart.totalPrice);
      $cartsTableRow.find(createOrderMap.cartDetailsBtn).prop(
        'href',
        this.router.generate('admin_carts_view', {cartId: cart.cartId}),
      );

      $cartsTableRow.find(createOrderMap.useCartBtn).data('cart-id', cart.cartId);

      $cartsTable.find('thead').removeClass('d-none');
      $cartsTable.find('tbody').append($cartsTableRow);
    }
  }

  /**
   * Renders customer orders list
   *
   * @param {Array} orders
   */
  renderOrders(orders) {
    const $ordersTable = $(createOrderMap.customerOrdersTable);
    const $rowTemplate = $($(createOrderMap.customerOrdersTableRowTemplate).html());

    $ordersTable.find('tbody').empty();
    this._showCheckoutHistoryBlock();
    this._removeEmptyListRowFromTable($ordersTable);

    //render 'No records found' when list is empty
    if (orders.length === 0) {
      this._renderEmptyList($ordersTable);

      return;
    }

    for (const key in Object.keys(orders)) {
      const order = orders[key];
      const $template = $rowTemplate.clone();

      $template.find(createOrderMap.orderIdField).text(order.orderId);
      $template.find(createOrderMap.orderDateField).text(order.orderPlacedDate);
      $template.find(createOrderMap.orderProductsField).text(order.totalProductsCount);
      $template.find(createOrderMap.orderTotalField).text(order.totalPaid);
      $template.find(createOrderMap.orderStatusField).text(order.orderStatus);
      $template.find(createOrderMap.orderDetailsBtn).prop(
        'href',
        this.router.generate('admin_orders_view', {orderId: order.orderId}),
      );

      $template.find(createOrderMap.useOrderBtn).data('order-id', order.orderId);

      $ordersTable.find('thead').removeClass('d-none');
      $ordersTable.find('tbody').append($template);
    }
  }

  /**
   * Renders 'No records' warning in list
   *
   * @param $table
   *
   * @private
   */
  _renderEmptyList($table) {
    const $emptyTableRow = $($(createOrderMap.emptyListRowTemplate).html()).clone();
    $table.find('tbody').append($emptyTableRow);
  }

  /**
   * Removes empty list row in case it was rendered
   */
  _removeEmptyListRowFromTable($table) {
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
  _renderFoundCustomer(customer) {
    this._hideNotFoundCustomers();
    const $customerSearchResultTemplate = $($(createOrderMap.customerSearchResultTemplate).html());
    const $template = $customerSearchResultTemplate.clone();

    $template.find(createOrderMap.customerSearchResultName).text(`${customer.firstName} ${customer.lastName}`);
    $template.find(createOrderMap.customerSearchResultEmail).text(customer.email);
    $template.find(createOrderMap.customerSearchResultId).text(customer.id);
    $template.find(createOrderMap.customerSearchResultBirthday).text(customer.birthday);
    $template.find(createOrderMap.chooseCustomerBtn).data('customer-id', customer.id);
    $template.find(createOrderMap.customerDetailsBtn).prop(
      'href',
      this.router.generate('admin_customers_view', {customerId: customer.id}),
    );

    return this.$customerSearchResultBlock.append($template);
  }

  /**
   * Shows checkout history block where carts and orders are rendered
   *
   * @private
   */
  _showCheckoutHistoryBlock() {
    $(createOrderMap.customerCheckoutHistory).removeClass('d-none');
  }

  /**
   * Clears shown customers
   *
   * @private
   */
  _clearShownCustomers() {
    this.$customerSearchResultBlock.empty();
  }

  /**
   * Shows empty result when customer is not found
   *
   * @private
   */
  _showNotFoundCustomers() {
    $(createOrderMap.customerSearchEmptyResultWarning).removeClass('d-none');
  }

  /**
   * Hides not found customers warning
   *
   * @private
   */
  _hideNotFoundCustomers() {
    $(createOrderMap.customerSearchEmptyResultWarning).addClass('d-none');
  }
}
