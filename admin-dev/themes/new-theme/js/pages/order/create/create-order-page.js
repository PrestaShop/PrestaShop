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
import CustomerManager from './customer-manager';
import ShippingRenderer from './shipping-renderer';
import CartProvider from './cart-provider';
import CartsRenderer from './carts-renderer';
import OrdersRenderer from './orders-renderer';
import AddressesRenderer from './addresses-renderer';
import CartRulesRenderer from './cart-rules-renderer';
import Router from '../../../components/router';
import {EventEmitter} from '../../../components/event-emitter';
import CartEditor from './cart-editor';
import eventMap from './event-map';
import CartRuleManager from './cart-rule-manager';
import ProductManager from './product-manager';

const $ = window.$;

/**
 * Page Object for "Create order" page
 */
export default class CreateOrderPage {
  constructor() {
    this.data = {};
    this.$container = $(createOrderPageMap.orderCreationContainer);

    this.cartProvider = new CartProvider();
    this.customerManager = new CustomerManager();
    this.shippingRenderer = new ShippingRenderer();
    this.cartsRenderer = new CartsRenderer();
    this.ordersRenderer = new OrdersRenderer();
    this.addressesRenderer = new AddressesRenderer();
    this.cartRulesRenderer = new CartRulesRenderer();
    this.router = new Router();
    this.cartEditor = new CartEditor();
    this.cartRuleManager = new CartRuleManager();
    this.cartProducts = new ProductManager();

    return {
      listenForCustomerSearch: () => this._handleCustomerSearch(),
      listenForCustomerSelect: () => this._handleCustomerChooseForOrderCreation(),
      listenForCartSelect: () => this._handleUseCartForOrderCreation(),
      listenForOrderSelect: () => this._handleDuplicateOrderCart(),
      listenForCartEdit: () => this._handleCartEdit(),
      listenForCartLoading: () => this._onCartLoaded(),
      listenForCartRuleSearch: () => this._handleCartRuleSearch(),
    };
  }

  /**
   * Handles event when cart is loaded.
   *
   * @private
   */
  _onCartLoaded() {
    EventEmitter.on(eventMap.cartLoaded, (cartInfo) => {
      this.data.cart_id = cartInfo.cartId;
      this._renderCartInfo(cartInfo);
      this._loadCustomerCarts(this.data.customer_id);
    });
  }

  /**
   * Searches for customer
   *
   * @private
   */
  _handleCustomerSearch() {
    this.$container.on('input', createOrderPageMap.customerSearchInput, () => {
      this.customerManager.onCustomerSearch();
    });
  }

  /**
   * Chooses customer for which order is being created
   *
   * @private
   */
  _handleCustomerChooseForOrderCreation() {
    this.$container.on('click', createOrderPageMap.chooseCustomerBtn, (event) => {
      const customerId = this.customerManager.onCustomerChooseForOrderCreation(event);
      this.data.customer_id = customerId;

      this.cartProvider.loadEmptyCart(customerId);
      this._loadCustomerCarts(customerId);
      this._loadCustomerOrders(customerId);
    });

    this.$container.on('click', createOrderPageMap.changeCustomerBtn, () => this.customerManager.onCustomerChange());
  }

  /**
   * Handles use case when cart is selected for order creation
   *
   * @private
   */
  _handleUseCartForOrderCreation() {
    this.$container.on('click', createOrderPageMap.useCartBtn, (e) => {
      const cartId = $(e.currentTarget).data('cart-id');
      this.cartProvider.getCart(cartId);
    });
  }

  /**
   * Handles use case when order is selected for cart duplication
   *
   * @private
   */
  _handleDuplicateOrderCart() {
    this.$container.on('click', createOrderPageMap.useOrderBtn, (e) => {
      const orderId = $(e.currentTarget).data('order-id');
      this.cartProvider.duplicateOrderCart(orderId);
    });
  }

  /**
   * Delegates actions to events associated with cart update (e.g. change cart address)
   *
   * @private
   */
  _handleCartEdit() {
    // @todo: add other actions
    this.$container.on('change', createOrderPageMap.addressSelect, () => this._changeCartAddresses());
    this.$container.on('change', createOrderPageMap.deliveryOptionSelect, e => this._changeDeliveryOption(e));
    this.$container.on('change', createOrderPageMap.freeShippingSwitch, e => this._setFreeShipping(e));
    this.$container.on('click', createOrderPageMap.addToCartButton, () => this.cartProducts.onAddProductToCart(this.data.cart_id));
    this._selectCartRule();
    this._removeCartRule();
  }

  /**
   * Triggers cart rule searching
   *
   * @private
   */
  _handleCartRuleSearch() {
    this.$container.on('input', createOrderPageMap.cartRuleSearchInput, () => {
      this.cartRuleManager.onCartRuleSearch();
    });
    this.$container.on('blur', createOrderPageMap.cartRuleSearchInput, () => {
      this.cartRuleManager.onDoneSearchingCartRule();
    });
  }

  /**
   * Triggers cart rule select
   *
   * @private
   */
  _selectCartRule() {
    this.$container.on('mousedown', createOrderPageMap.foundCartRuleListItem, (event) => {
      // prevent blur event to allow selecting cart rule
      event.preventDefault();
      const cartRuleId = $(event.currentTarget).data('cart-rule-id');
      this.cartRuleManager.onCartRuleSelect(cartRuleId, this.data.cart_id);

      // manually fire blur event after cart rule is selected.
    }).on('click', createOrderPageMap.foundCartRuleListItem, () => {
      $(createOrderPageMap.cartRuleSearchInput).blur();
    });
  }

  /**
   * Triggers cart rule removal from cart
   *
   * @private
   */
  _removeCartRule() {
    this.$container.on('click', createOrderPageMap.cartRuleDeleteBtn, (event) => {
      this.cartRuleManager.onCartRuleRemove(
        $(event.currentTarget).data('cart-rule-id'),
        this.data.cart_id
      );
      EventEmitter.on(eventMap.cartRuleRemoved, (cartInfo) => {
        this.cartRulesRenderer.render(cartInfo.cartRules, cartInfo.products.length === 0);
      });
    });
  }

  /**
   * Gets and renders customer carts
   *
   * @param customerId
   *
   * @private
   */
  _loadCustomerCarts(customerId) {
    this.customerManager.getCustomerCarts(customerId);
    EventEmitter.on(eventMap.customerCartsLoaded, (cartInfo) => {
      this.cartsRenderer.render({
        carts: cartInfo.carts,
        currentCartId: this.data.cart_id,
      });
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
    this.customerManager.getCustomerOrders(customerId);
    EventEmitter.on(eventMap.customerOrdersLoaded, (cartInfo) => {
      this.ordersRenderer.render(cartInfo.orders);
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
    this.cartRulesRenderer.render(cartInfo.cartRules, cartInfo.products.length === 0);
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
    const addresses = {
      delivery_address_id: $(createOrderPageMap.deliveryAddressSelect).val(),
      invoice_address_id: $(createOrderPageMap.invoiceAddressSelect).val(),
    };

    this.cartEditor.changeCartAddresses(this.data.cart_id, addresses);
    EventEmitter.on(eventMap.cartAddressesChanged, (cartInfo) => {
      this.addressesRenderer.render(cartInfo.addresses);
      this.shippingRenderer.render(cartInfo.shipping, cartInfo.products.length === 0);
    });
  }

  /**
   * Modifies cart delivery option
   *
   * @param event
   *
   * @private
   */
  _changeDeliveryOption(event) {
    this.cartEditor.changeDeliveryOption(this.data.cart_id, event.currentTarget.value);
    EventEmitter.on(eventMap.cartDeliveryOptionChanged, (cartInfo) => {
      this.shippingRenderer.render(cartInfo.shipping, cartInfo.products.length === 0);
    });
  }

  /**
   * Sets free shipping value of cart
   *
   * @param event
   *
   * @private
   */
  _setFreeShipping(event) {
    this.cartEditor.setFreeShipping(this.data.cart_id, event.currentTarget.value);
    EventEmitter.on(eventMap.cartFreeShippingSet, (cartInfo) => {
      this.shippingRenderer.render(cartInfo.shipping, cartInfo.products.length === 0);
    });
  }

  /**
   * @todo: for cart to order convertion
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
   * @todo: for cart to order convertion
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
    }).then((response) => {
      this._persistCartInfoData(response);
      this._renderCartInfo(response);
    });
  }
}
