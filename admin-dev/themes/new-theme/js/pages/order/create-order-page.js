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

import createOrderPageMap from './create-order-map';
import CustomerSearcherComponent from './customer-searcher-component';
import ShippingRenderer from './shipping-renderer';

const $ = window.$;

/**
 * Page Object for "Create order" page
 */
export default class CreateOrderPage {
  constructor() {
    this.data = {};
    this.$container = $(createOrderPageMap.orderCreationContainer);

    this.customerSearcher = new CustomerSearcherComponent();
    this.shippingRenderer = new ShippingRenderer();

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

      this._loadCartInfoAfterChoosingCustomer();
    });

    this.$container.on('click', createOrderPageMap.changeCustomerBtn, () => this.customerSearcher.onCustomerChange());
    this.$container.on('change', createOrderPageMap.addressSelect, () => this._changeCartAddresses());

    this.$container.on('click', '.js-use-cart-btn', () => {
      const cartId = $(event.target).data('cart-id');

      this._choosePreviousCart(cartId);
    });
  }

  /**
   * Loads cart summary with customer's carts & orders history.
   *
   * @private
   */
  _loadCartInfoAfterChoosingCustomer() {
    $.ajax(this.$container.data('last-empty-cart-url'), {
      method: 'POST',
      data: {
        customer_id: this.data.customer_id,
      },
      dataType: 'json',
    }).then((response) => {
      this.data.cart_id = response.cartId;

      this._loadCustomerCarts();
      this._loadCustomerOrders();

      // this._renderCheckoutHistory(checkoutHistory);
      this._renderCartInfo(response);
    });
  }

  _loadCustomerCarts() {
    $.ajax(this.$container.data('customer-carts-url'), {
      method: 'GET',
      data: {
        customer_id: this.data.customer_id,
      },
      dataType: 'json',
    }).then((response) => {
      this._renderCustomerCarts(response.carts);
      $(createOrderPageMap.customerCheckoutHistory).removeClass('d-none');
    });
  }

  _loadCustomerOrders() {
    $.ajax(this.$container.data('customer-orders-url'), {
      method: 'GET',
      data: {
        customer_id: this.data.customer_id,
      },
      dataType: 'json',
    }).then((response) => {
      this._renderCustomerOrders(response.orders);
      $(createOrderPageMap.customerCheckoutHistory).removeClass('d-none');
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
    // this._renderCustomerCarts(checkoutHistory.carts);
    this._renderCustomerOrders(checkoutHistory.orders);

    $(createOrderPageMap.customerCheckoutHistory).removeClass('d-none');
  }

  /**
   * Renders customer carts from checkout history
   *
   * @param {Object} carts
   *
   * @private
   */
  _renderCustomerCarts(carts) {
    const $cartsTable = $(createOrderPageMap.customerCartsTable);
    const $cartsTableRowTemplate = $($(createOrderPageMap.customerCartsTableRowTemplate).html());

    $cartsTable.find('tbody').empty();

    if (!carts) {
      return;
    }

    for (const key in carts) {
      const cart = carts[key];
      if (cart.cartId === this.data.cart_id) {
        continue;
      }
      const $template = $cartsTableRowTemplate.clone();

      $template.find('.js-cart-id').text(cart.cartId);
      $template.find('.js-cart-date').text(cart.cartCreationDate);
      $template.find('.js-cart-total').text(cart.cartTotal);

      $template.find('.js-use-cart-btn').data('cart-id', cart.cartId);

      $cartsTable.find('tbody').append($template);
    }

    $(createOrderPageMap.customerCheckoutHistory).removeClass('d-none');
  }

  /**
   * Renders cart summary on the page
   *
   * @param {Object} cartInfo
   *
   * @private
   */
  _renderCartInfo(cartInfo) {
    this._renderAddressesSelect(cartInfo.addresses);

    // render Summary block when at least 1 product is in cart
    // and delivery options are available

    this._showCartInfo();
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

    if (!orders) {
      return;
    }

    for (const key in Object.keys(orders)) {
      if (!orders.hasOwnProperty(key)) {
        continue;
      }

      const order = orders[key];
      const $template = $rowTemplate.clone();

      $template.find('.js-order-id').text(order.orderId);
      $template.find('.js-order-date').text(order.orderPlacedDate);
      $template.find('.js-order-products').text(order.totalProductsCount);
      $template.find('.js-order-total-paid').text(order.totalPaid);
      $template.find('.js-order-status').text(order.orderStatus);

      $ordersTable.find('tbody').append($template);
    }
  }

  /**
   * Shows Cart, Vouchers, Addresses blocks
   *
   * @private
   */
  _showCartInfo() {
    $(createOrderPageMap.cartBlock).removeClass('d-none');
    $(createOrderPageMap.vouchersBlock).removeClass('d-none');
    $(createOrderPageMap.addressesBlock).removeClass('d-none');
  }

  /**
   * Renders Delivery & Invoice addresses select
   *
   * @param {Array} addresses
   *
   * @private
   */
  _renderAddressesSelect(addresses) {
    let deliveryAddressDetailsContent = '';
    let invoiceAddressDetailsContent = '';

    const $deliveryAddressDetails = $(createOrderPageMap.deliveryAddressDetails);
    const $invoiceAddressDetails = $(createOrderPageMap.invoiceAddressDetails);
    const $deliveryAddressSelect = $(createOrderPageMap.deliveryAddressSelect);
    const $invoiceAddressSelect = $(createOrderPageMap.invoiceAddressSelect);

    const $addressesContent = $(createOrderPageMap.addressesContent);
    const $addressesWarningContent = $(createOrderPageMap.addressesWarning);

    $deliveryAddressDetails.empty();
    $invoiceAddressDetails.empty();
    $deliveryAddressSelect.empty();
    $invoiceAddressSelect.empty();

    if (addresses.length === 0) {
      $addressesWarningContent.removeClass('d-none');
      $addressesContent.addClass('d-none');

      return;
    }

    $addressesContent.removeClass('d-none');
    $addressesWarningContent.addClass('d-none');

    for (const key in Object.keys(addresses)) {
      const address = addresses[key];

      const deliveryAddressOption = {
        value: address.addressId,
        text: address.alias,
      };

      const invoiceAddressOption = {
        value: address.addressId,
        text: address.alias,
      };

      if (address.delivery) {
        deliveryAddressDetailsContent = address.formattedAddress;
        deliveryAddressOption.selected = 'selected';
      }

      if (address.invoice) {
        invoiceAddressDetailsContent = address.formattedAddress;
        invoiceAddressOption.selected = 'selected';
      }

      $deliveryAddressSelect.append($('<option>', deliveryAddressOption));
      $invoiceAddressSelect.append($('<option>', invoiceAddressOption));
    }

    if (deliveryAddressDetailsContent) {
      $(createOrderPageMap.deliveryAddressDetails).html(deliveryAddressDetailsContent);
    }

    if (invoiceAddressDetailsContent) {
      $(createOrderPageMap.invoiceAddressDetails).html(invoiceAddressDetailsContent);
    }
  }

  /**
   * Changes cart addresses
   *
   * @private
   */
  _changeCartAddresses() {
    $.ajax(this.$container.data('edit-address-url'), {
      method: 'POST',
      data: {
        customer_id: this.data.customer_id,
        cart_id: this.data.cart_id,
        delivery_address_id: $(createOrderPageMap.deliveryAddressSelect).val(),
        invoice_address_id: $(createOrderPageMap.invoiceAddressSelect).val(),
      },
      dataType: 'json',
    }).then((response) => {
      // this._persistCartInfoData(response);

      this._renderAddressesSelect(response.addresses);
    });
  }

  /**
   * Stores cart summary into "session" like variable
   *
   * @param {Object} cartInfo
   *
   * @private
   */
  _persistCartInfoData(cartInfo) {
    this.data.cart_id = cartInfo.cart.id;
    this.data.delivery_address_id = cartInfo.cart.id_address_delivery;
    this.data.invoice_address_id = cartInfo.cart.id_address_invoice;
  }

  /**
   * Choses previous cart from which order will be created
   *
   * @param {Number} cartId
   *
   * @private
   */
  _choosePreviousCart(cartId) {
    $.ajax(this.$container.data('cart-summary-url'), {
      method: 'POST',
      data: {
        id_cart: cartId,
        id_customer: this.data.customer_id,
      },
      dataType: 'json',
    }).then((response) => {
      this._persistCartInfoData(response);

      this._renderCartInfo(response);
    });
  }
}
