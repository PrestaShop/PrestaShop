/**
 * 2007-2019 PrestaShop and Contributors
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

import createOrderPageMap from "./create-order-map";
import CustomerSearcherComponent from "./customer-searcher-component";

const $ = window.$;

/**
 * Page Object for "Create order" page
 */
export default class CreateOrderPage {
  constructor() {
    this.data = {};
    this.$container = $(createOrderPageMap.orderCreationContainer);

    this.customerSearcher = new CustomerSearcherComponent();

    return {
      listenForCustomerSearch: () => this._handleCustomerSearch(),
      listenForCustomerChooseForOrderCreation: () => this._handleCustomerChooseForOrderCreation(),
    };
  }

  /**
   * Searches for customer
   *
   * @private
   */
  _handleCustomerSearch() {
    this.$container.on('input', createOrderPageMap.customerSearchInput, () => {
      this.customerSearcher.onCustomerSearch();
    });
  }

  /**
   * Chooses customer for which order is being created
   *
   * @private
   */
  _handleCustomerChooseForOrderCreation() {
    this.$container.on('click', createOrderPageMap.chooseCustomerBtn, (event) => {
      this.data.customer_id = this.customerSearcher.onCustomerChooseForOrderCreation(event);

      this._loadCartSummaryAfterChoosingCustomer();
    });

    this.$container.on('click', createOrderPageMap.changeCustomerBtn, () => {
      this.customerSearcher.onCustomerChange();
    });
  }

  /**
   * Loads cart summary with customer's carts & orders history.
   *
   * @private
   */
  _loadCartSummaryAfterChoosingCustomer() {
    $.ajax(this.$container.data('cart-summary-url'), {
      method: 'POST',
      data: {
        'id_customer': this.data.customer_id,
      },
      dataType: 'json'
    }).then((response) => {
      const checkoutHistory = {
        carts: response.carts,
        orders: response.orders
      };

      console.log('Cart summary with checkout history', response);

      this._renderCheckoutHistory(checkoutHistory);
      this._renderCartSummary(response);
    });
  }

  /**
   * Renders previous Carts & Orders from customer history
   *
   * @param {Object} checkoutHistory
   *
   * @private
   */
  _renderCheckoutHistory(checkoutHistory) {
    this._renderCustomerCarts(checkoutHistory.carts);
    this._renderCustomerOrders(checkoutHistory.orders);

    $(createOrderPageMap.customerCheckoutHistory).removeClass('d-none');
  }

  /**
   * Renders customer carts
   *
   * @param {Object} carts
   *
   * @private
   */
  _renderCustomerCarts(carts) {
    const $cartsTable = $(createOrderPageMap.customerCartsTable);
    const $cartsTableRowTemplate = $($(createOrderPageMap.customerCartsTableRowTemplate).html());

    $cartsTable.find('tbody').empty();

    for (let key in carts) {
      if (!carts.hasOwnProperty(key)) {
        continue;
      }

      const cart = carts[key];
      const $template = $cartsTableRowTemplate.clone();

      $template.find('.js-cart-id').text(cart.id_cart);
      $template.find('.js-cart-date').text(cart.date_add);
      $template.find('.js-cart-total').text(cart.total_price);

      $cartsTable.find('tbody').append($template);
    }

    $(createOrderPageMap.customerCheckoutHistory).removeClass('d-none');
  }

  /**
   * Renders cart summary on the page
   *
   * @param {Object} cartSummary
   *
   * @private
   */
  _renderCartSummary(cartSummary) {
    this._renderAddressesSelect(cartSummary);

    this._showCartSummary();
  }

  /**
   * Renders customer orders
   *
   * @param {Object} orders
   *
   * @private
   */
  _renderCustomerOrders(orders) {
    const $ordersTable = $(createOrderPageMap.customerOrdersTable);
    const $rowTemplate = $($(createOrderPageMap.customerOrdersTableRowTemplate).html());

    $ordersTable.find('tbody').empty();

    console.log(orders);

    for (let key in Object.keys(orders)) {
      if (!orders.hasOwnProperty(key)) {
        continue;
      }

      const order = orders[key];
      const $template = $rowTemplate.clone();

      $template.find('.js-order-id').text(order.id_order);
      $template.find('.js-order-date').text(order.date_add);
      $template.find('.js-order-products').text(order.nb_products);
      $template.find('.js-order-total-paid').text(order.total_paid_real);
      $template.find('.js-order-status').text(order.order_state);

      $ordersTable.find('tbody').append($template);
    }
  }

  /**
   * Shows Cart, Vouchers, Addresses blocks
   *
   * @private
   */
  _showCartSummary() {
    $(createOrderPageMap.cartBlock).removeClass('d-none');
    $(createOrderPageMap.vouchersBlock).removeClass('d-none');
    $(createOrderPageMap.addressesBlock).removeClass('d-none');
  }

  /**
   * Renders Delivery & Invoice addresses select
   *
   * @param {Object} cartSummary
   *
   * @private
   */
  _renderAddressesSelect(cartSummary) {
    let deliveryAddressDetailsContent = '';
    let invoiceAddressDetailsContent = '';

    const $deliveryAddressSelect = $(createOrderPageMap.deliveryAddressSelect);
    const $invoiceAddressSelect = $(createOrderPageMap.invoiceAddressSelect);

    for (let key in Object.keys(cartSummary.addresses)) {
      if (!cartSummary.addresses.hasOwnProperty(key)) {
        continue;
      }

      let address = cartSummary.addresses[key];

      let deliveryAddressOption = {
        value: address.id_address,
        text: address.alias
      };

      let invoiceAddressOption = {
        value: address.id_address,
        text: address.alias
      };

      if (cartSummary.cart.id_address_delivery === address.id_address) {
        deliveryAddressDetailsContent = address.formated_address;
        deliveryAddressOption.selected = 'selected';
      }

      if (cartSummary.cart.id_address_invoice === address.id_address) {
        invoiceAddressDetailsContent = address.formated_address;
        invoiceAddressOption.selected = 'selected';
      }

      $deliveryAddressSelect.append($('<option>', deliveryAddressOption));
      $invoiceAddressSelect.append($('<option>', invoiceAddressOption));
    }

    if (deliveryAddressDetailsContent) {
      $(createOrderPageMap.deliveryAddressDetails).html(deliveryAddressDetailsContent);
      $(createOrderPageMap.invoiceAddressDetails).html(invoiceAddressDetailsContent);
    }
  }
}
