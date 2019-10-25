import createOrderMap from './create-order-map';

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

const $ = window.$;

/**
 * Responsible for customer information rendering
 */
export default class CustomerRenderer {
  constructor() {
    this.$container = $(createOrderMap.customerSearchBlock);
    this.$customerSearchResultBlock = $(createOrderMap.customerSearchResultsBlock);
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
      .remove()
    ;
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

    if (carts.length === 0) {
      return;
    }

    this._showCheckoutHistoryBlock();

    for (const key in carts) {
      const cart = carts[key];
      // do not render current cart
      if (cart.cartId === currentCartId) {
        continue;
      }
      const $template = $cartsTableRowTemplate.clone();

      $template.find('.js-cart-id').text(cart.cartId);
      $template.find('.js-cart-date').text(cart.creationDate);
      $template.find('.js-cart-total').text(cart.totalPrice);

      $template.find('.js-use-cart-btn').data('cart-id', cart.cartId);

      $cartsTable.find('tbody').append($template);
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

    if (orders.length === 0) {
      return;
    }

    this._showCheckoutHistoryBlock();

    for (const key in Object.keys(orders)) {
      const order = orders[key];
      const $template = $rowTemplate.clone();

      $template.find('.js-order-id').text(order.orderId);
      $template.find('.js-order-date').text(order.orderPlacedDate);
      $template.find('.js-order-products').text(order.totalProductsCount);
      $template.find('.js-order-total-paid').text(order.totalPaid);
      $template.find('.js-order-status').text(order.orderStatus);

      $template.find('.js-use-order-btn').data('order-id', order.orderId);

      $ordersTable.find('tbody').append($template);
    }
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
    const $customerSearchResultTemplate = $($(createOrderMap.customerSearchResultTemplate).html());
    const $template = $customerSearchResultTemplate.clone();

    $template.find(createOrderMap.customerSearchResultName).text(`${customer.firstName} ${customer.lastName}`);
    $template.find(createOrderMap.customerSearchResultEmail).text(customer.email);
    $template.find(createOrderMap.customerSearchResultId).text(customer.id);
    $template.find(createOrderMap.customerSearchResultBirthday).text(customer.birthday);

    $template.find(createOrderMap.customerDetailsBtn).data('customer-id', customer.id);
    $template.find(createOrderMap.chooseCustomerBtn).data('customer-id', customer.id);

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
    const $emptyResultTemplate = $($('#customerSearchEmptyResultTemplate').html());

    this.$customerSearchResultBlock.append($emptyResultTemplate);
  }
}
