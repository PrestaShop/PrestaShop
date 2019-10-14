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

import createOrderPageMap from './create-order-map';
import CustomerSearcherComponent from './customer-searcher-component';
import ShippingRenderer from './shipping-renderer';
import CartProvider from './cart-provider';
import CustomerInfoProvider from './customer-info-provider';
import CartsRenderer from './carts-renderer';
import OrdersRenderer from './orders-renderer';
import AddressesRenderer from './addresses-renderer';
import VouchersRenderer from './vouchers-renderer';
import Router from '../../../components/router';

const $ = window.$;

/**
 * Page Object for "Create order" page
 */
export default class CreateOrderPage {
  constructor() {
    this.data = {};
    this.$container = $(createOrderPageMap.orderCreationContainer);

    this.cartProvider = new CartProvider();
    this.customerInfoProvider = new CustomerInfoProvider();
    this.customerSearcher = new CustomerSearcherComponent();
    this.shippingRenderer = new ShippingRenderer();
    this.cartsRenderer = new CartsRenderer();
    this.ordersRenderer = new OrdersRenderer();
    this.addressesRenderer = new AddressesRenderer();
    this.vouchersRenderer = new VouchersRenderer();
    this.router = new Router();

    return {
      listenForCustomerSearch: () => this._handleCustomerSearch(),
      listenForCustomerSelect: () => this._handleCustomerChooseForOrderCreation(),
      listenForCartSelect: () => this._handleUseCartForOrderCreation(),
      listenForOrderSelect: () => this._handleDuplicateOrderCart(),
      listenForCartUpdate: () => this._handleCartUpdate(),
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
      const customerId = this.customerSearcher.onCustomerChooseForOrderCreation(event);
      this.data.customer_id = customerId;

      const self = this;
      this.cartProvider.loadEmptyCart(customerId).then((response) => {
        self.data.cart_id = response.cartId;
        self._renderCartInfo(response);
      });

      this._loadCustomerCarts(customerId);
      this._loadCustomerOrders(customerId);
    });

    this.$container.on('click', createOrderPageMap.changeCustomerBtn, () => this.customerSearcher.onCustomerChange());
  }

  /**
   * Handles use case when cart is selected for order creation
   *
   * @private
   */
  _handleUseCartForOrderCreation() {
    this.$container.on('click', '.js-use-cart-btn', (e) => {
      const cartId = $(e.currentTarget).data('cart-id');

      const self = this;
      this.cartProvider.getCart(cartId).then((response) => {
        self.data.cart_id = response.cartId;
        self._renderCartInfo(response);
      });
    });
  }

  /**
   * Handles use case when order is selected for cart duplication
   *
   * @private
   */
  _handleDuplicateOrderCart() {
    this.$container.on('click', '.js-use-order-btn', (e) => {
      const orderId = $(e.currentTarget).data('order-id');

      const self = this;
      this.cartProvider.duplicateOrderCart(orderId).then((response) => {
        self._renderCartInfo(response);
        self.shippingRenderer.render(response.shipping);
        self.data.cart_id = response.cartId;
      });
    });
  }

  /**
   * Delegates actions to events associated with cart update (e.g. change cart address)
   *
   * @private
   */
  _handleCartUpdate() {
    // @todo: add other actions
    this.$container.on('change', createOrderPageMap.addressSelect, () => this._changeCartAddresses());
    this.$container.on('change', createOrderPageMap.deliveryOptionSelect, e => this._editDeliveryOption(e));
    this.$container.on('change', createOrderPageMap.freeShippingSwitch, e => this._editFreeShipping(e));
  }

  /**
   * Gets and renders customer carts
   *
   * @param customerId
   *
   * @private
   */
  _loadCustomerCarts(customerId) {
    const self = this;
    this.customerInfoProvider.getCustomerCarts(customerId).then((response) => {
      self.cartsRenderer.render({
        carts: response.carts,
        currentCartId: self.data.cart_id,
      });
      $(createOrderPageMap.customerCheckoutHistory).removeClass('d-none');
    });
  }

  /**
   * Gets and renders customer orders
   *
   * @param customerId
   *
   * @private
   */
  _loadCustomerOrders(customerId) {
    const self = this;
    this.customerInfoProvider.getCustomerOrders(customerId).then((response) => {
      self.ordersRenderer.render(response.orders);
      $(createOrderPageMap.customerCheckoutHistory).removeClass('d-none');
    });
  }

  /**
   * Renders cart summary on the page
   *
   * @param {Object} cartInfo
   *
   * @private
   */
  _renderCartInfo(cartInfo) {
    this.addressesRenderer.render(cartInfo.addresses);
    this.vouchersRenderer.render(cartInfo.cartRules);
    this.shippingRenderer.render(cartInfo.shipping, cartInfo.products.length === 0);
    // @todo: render Summary block when at least 1 product is in cart
    // and delivery options are available

    $(createOrderPageMap.cartBlock).removeClass('d-none');
  }

  /**
   * Changes cart addresses
   *
   * @private
   */
  _changeCartAddresses() {
    const self = this;
    $.ajax(this.router.generate('admin_carts_edit_address', {cartId: this.data.cart_id}), {
      method: 'POST',
      data: {
        delivery_address_id: $(createOrderPageMap.deliveryAddressSelect).val(),
        invoice_address_id: $(createOrderPageMap.invoiceAddressSelect).val(),
      },
      dataType: 'json',
    }).then((response) => {
      // this._persistCartInfoData(response);

      self.addressesRenderer.render(response.addresses);
      self.shippingRenderer.render(response.shipping);
    });
  }

  _editDeliveryOption(e) {
    const self = this;
    $.ajax(this.router.generate('admin_carts_edit_carrier', {cartId: this.data.cart_id}), {
      method: 'POST',
      data: {
        carrier_id: e.currentTarget.value,
      },
      dataType: 'json',
    }).then((response) => {
      self.shippingRenderer.render(response.shipping);
    });
  }

  _editFreeShipping(e) {
    const self = this;
    $.ajax(this.router.generate('admin_carts_edit_free_shipping', {cartId: this.data.cart_id}), {
      method: 'POST',
      data: {
        free_shipping: e.currentTarget.value,
      },
      dataType: 'json',
    }).then((response) => {
      self.shippingRenderer.render(response.shipping);
    });
  }

  /**
   * @todo
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
    const self = this;
    $.ajax(this.$container.data('cart-summary-url'), {
      method: 'POST',
      data: {
        id_cart: cartId,
        id_customer: this.data.customer_id,
      },
      dataType: 'json',
    }).then((response) => {
      self._persistCartInfoData(response);

      self._renderCartInfo(response);
    });
  }
}
