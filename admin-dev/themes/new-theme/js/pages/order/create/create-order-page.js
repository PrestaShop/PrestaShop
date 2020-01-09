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

import Router from '@components/router';
import {EventEmitter} from '@components/event-emitter';
import createOrderMap from './create-order-map';
import CustomerManager from './customer-manager';
import ShippingRenderer from './shipping-renderer';
import CartProvider from './cart-provider';
import AddressesRenderer from './addresses-renderer';
import CartRulesRenderer from './cart-rules-renderer';
import CartEditor from './cart-editor';
import eventMap from './event-map';
import CartRuleManager from './cart-rule-manager';
import ProductManager from './product-manager';
import ProductRenderer from './product-renderer';
import SummaryRenderer from './summary-renderer';
import SummaryManager from './summary-manager';
import {ValidateAddresses} from './address-validator';

const {$} = window;

/**
 * Page Object for "Create order" page
 */
export default class CreateOrderPage {
  constructor() {
    this.cartId = null;
    this.customerId = null;
    this.$container = $(createOrderMap.orderCreationContainer);

    this.cartProvider = new CartProvider();
    this.customerManager = new CustomerManager();
    this.shippingRenderer = new ShippingRenderer();
    this.addressesRenderer = new AddressesRenderer();
    this.cartRulesRenderer = new CartRulesRenderer();
    this.router = new Router();
    this.cartEditor = new CartEditor();
    this.cartRuleManager = new CartRuleManager();
    this.productManager = new ProductManager();
    this.productRenderer = new ProductRenderer();
    this.summaryRenderer = new SummaryRenderer();
    this.summaryManager = new SummaryManager();

    this.initListeners();
    this.loadCartFromUrlParams();
  }

  /**
   * Loads cart if query params contains valid cartId
   *
   * @private
   */
  loadCartFromUrlParams() {
    const urlParams = new URLSearchParams(window.location.search);
    const cartId = Number(urlParams.get('cartId'));

    if (!Number.isNaN(cartId) && cartId !== 0) {
      this.cartProvider.getCart(cartId);
    }
  }

  /**
   * Initializes event listeners
   *
   * @private
   */
  initListeners() {
    this.$container.on('input', createOrderMap.customerSearchInput, (e) => this.initCustomerSearch(e));
    this.$container.on('click', createOrderMap.chooseCustomerBtn, (e) => this.initCustomerSelect(e));
    this.$container.on('click', createOrderMap.useCartBtn, (e) => this.initCartSelect(e));
    this.$container.on('click', createOrderMap.useOrderBtn, (e) => this.initDuplicateOrderCart(e));
    this.$container.on('input', createOrderMap.productSearch, (e) => this.initProductSearch(e));
    this.$container.on('input', createOrderMap.cartRuleSearchInput, (e) => this.initCartRuleSearch(e));
    this.$container.on('blur', createOrderMap.cartRuleSearchInput, () => this.cartRuleManager.stopSearching());
    this.listenForCartEdit();
    this.onCartLoaded();
  }

  /**
   * Delegates actions to events associated with cart update (e.g. change cart address)
   *
   * @private
   */
  listenForCartEdit() {
    this.onCartAddressesChanged();
    this.onDeliveryOptionChanged();
    this.onFreeShippingChanged();
    this.addCartRuleToCart();
    this.removeCartRuleFromCart();
    this.onCartCurrencyChanged();
    this.onCartLanguageChanged();

    this.$container.on(
      'change',
      createOrderMap.deliveryOptionSelect,
      (e) => this.cartEditor.changeDeliveryOption(this.cartId, e.currentTarget.value),
    );

    this.$container.on(
      'change',
      createOrderMap.freeShippingSwitch,
      (e) => this.cartEditor.setFreeShipping(this.cartId, e.currentTarget.value),
    );

    this.$container.on(
      'click',
      createOrderMap.addToCartButton,
      () => this.productManager.addProductToCart(this.cartId),
    );

    this.$container.on(
      'change',
      createOrderMap.cartCurrencySelect,
      (e) => this.cartEditor.changeCartCurrency(this.cartId, e.currentTarget.value),
    );

    this.$container.on(
      'change',
      createOrderMap.cartLanguageSelect,
      (e) => this.cartEditor.changeCartLanguage(this.cartId, e.currentTarget.value),
    );

    this.$container.on(
      'click',
      createOrderMap.sendProcessOrderEmailBtn,
      () => this.summaryManager.sendProcessOrderEmail(this.cartId),
    );

    this.$container.on('change', createOrderMap.listedProductUnitPriceInput, (e) => this.initProductChangePrice(e));
    this.$container.on('change', createOrderMap.listedProductQtyInput, (e) => this.initProductChangeQty(e));
    this.$container.on('change', createOrderMap.addressSelect, () => this.nchangeCartAddresses());
    this.$container.on('click', createOrderMap.productRemoveBtn, (e) => this.initProductRemoveFromCart(e));
  }

  /**
   * Listens for event when cart is loaded
   *
   * @private
   */
  onCartLoaded() {
    EventEmitter.on(eventMap.cartLoaded, (cartInfo) => {
      this.cartId = cartInfo.cartId;
      this.renderCartInfo(cartInfo);
      if (cartInfo.addresses.length !== 0 && !ValidateAddresses(cartInfo.addresses)) {
        this.changeCartAddresses();
      }
      this.customerManager.loadCustomerCarts(this.cartId);
      this.customerManager.loadCustomerOrders();
    });
  }

  /**
   * Listens for cart addresses update event
   *
   * @private
   */
  onCartAddressesChanged() {
    EventEmitter.on(eventMap.cartAddressesChanged, (cartInfo) => {
      this.addressesRenderer.render(cartInfo.addresses);
      this.shippingRenderer.render(cartInfo.shipping, cartInfo.products.length === 0);
      this.summaryRenderer.render(cartInfo);
    });
  }

  /**
   * Listens for cart delivery option update event
   *
   * @private
   */
  onDeliveryOptionChanged() {
    EventEmitter.on(eventMap.cartDeliveryOptionChanged, (cartInfo) => {
      this.shippingRenderer.render(cartInfo.shipping, cartInfo.products.length === 0);
      this.summaryRenderer.render(cartInfo);
    });
  }

  /**
   * Listens for cart free shipping update event
   *
   * @private
   */
  onFreeShippingChanged() {
    EventEmitter.on(eventMap.cartFreeShippingSet, (cartInfo) => {
      this.cartRulesRenderer.renderCartRulesBlock(cartInfo.cartRules, cartInfo.products.length === 0);
      this.shippingRenderer.render(cartInfo.shipping, cartInfo.products.length === 0);
      this.summaryRenderer.render(cartInfo);
    });
  }

  /**
   * Listens for cart language update event
   *
   * @private
   */
  onCartLanguageChanged() {
    EventEmitter.on(eventMap.cartLanguageChanged, (cartInfo) => {
      this.preselectCartLanguage(cartInfo.langId);
    });
  }

  /**
   * Listens for cart currency update event
   *
   * @private
   */
  onCartCurrencyChanged() {
    // on success
    EventEmitter.on(eventMap.cartCurrencyChanged, (cartInfo) => {
      this.renderCartInfo(cartInfo);
      this.productRenderer.reset();
    });

    // on failure
    EventEmitter.on(eventMap.cartCurrencyChangeFailed, (response) => {
      this.productRenderer.renderCartBlockErrorAlert(response.responseJSON.message);
    });
  }

  /**
   * Init customer searching
   *
   * @param event
   *
   * @private
   */
  initCustomerSearch(event) {
    clearTimeout(this.timeoutId);
    this.timeoutId = setTimeout(() => this.customerManager.search($(event.currentTarget).val()), 300);
  }

  /**
   * Init selecting customer for which order is being created
   *
   * @param event
   *
   * @private
   */
  initCustomerSelect(event) {
    const customerId = this.customerManager.selectCustomer(event);
    this.customerId = customerId;
    this.cartProvider.loadEmptyCart(customerId);
  }

  /**
   * Inits selecting cart to load
   *
   * @param event
   *
   * @private
   */
  initCartSelect(event) {
    const cartId = $(event.currentTarget).data('cart-id');
    this.cartProvider.getCart(cartId);
  }

  /**
   * Inits duplicating order cart
   *
   * @private
   */
  initDuplicateOrderCart(event) {
    const orderId = $(event.currentTarget).data('order-id');
    this.cartProvider.duplicateOrderCart(orderId);
  }

  /**
   * Triggers cart rule searching
   *
   * @private
   */
  initCartRuleSearch(event) {
    const searchPhrase = event.currentTarget.value;

    clearTimeout(this.timeoutId);
    this.timeoutId = setTimeout(() => this.cartRuleManager.search(searchPhrase), 300);
  }

  /**
   * Triggers cart rule select
   *
   * @private
   */
  addCartRuleToCart() {
    this.$container.on('mousedown', createOrderMap.foundCartRuleListItem, (event) => {
      // prevent blur event to allow selecting cart rule
      event.preventDefault();
      const cartRuleId = $(event.currentTarget).data('cart-rule-id');
      this.cartRuleManager.addCartRuleToCart(cartRuleId, this.cartId);

      // manually fire blur event after cart rule is selected.
    }).on('click', createOrderMap.foundCartRuleListItem, () => {
      $(createOrderMap.cartRuleSearchInput).blur();
    });
  }

  /**
   * Triggers cart rule removal from cart
   *
   * @private
   */
  removeCartRuleFromCart() {
    this.$container.on('click', createOrderMap.cartRuleDeleteBtn, (event) => {
      this.cartRuleManager.removeCartRuleFromCart($(event.currentTarget).data('cart-rule-id'), this.cartId);
    });
  }

  /**
   * Inits product searching
   *
   * @param event
   *
   * @private
   */
  initProductSearch(event) {
    const $productSearchInput = $(event.currentTarget);
    const searchPhrase = $productSearchInput.val();
    clearTimeout(this.timeoutId);

    this.timeoutId = setTimeout(() => this.productManager.search(searchPhrase), 300);
  }

  /**
   * Inits product removing from cart
   *
   * @param event
   *
   * @private
   */
  initProductRemoveFromCart(event) {
    const product = {
      productId: $(event.currentTarget).data('product-id'),
      attributeId: $(event.currentTarget).data('attribute-id'),
      customizationId: $(event.currentTarget).data('customization-id'),
    };

    this.productManager.removeProductFromCart(this.cartId, product);
  }

  /**
   * Inits product in cart price change
   *
   * @param event
   *
   * @private
   */
  initProductChangePrice(event) {
    const product = {
      productId: $(event.currentTarget).data('product-id'),
      attributeId: $(event.currentTarget).data('attribute-id'),
      customizationId: $(event.currentTarget).data('customization-id'),
      price: $(event.currentTarget).val(),
    };

    this.productManager.changeProductPrice(this.cartId, this.customerId, product);
  }

  /**
   * Inits product in cart quantity update
   *
   * @param event
   *
   * @private
   */
  initProductChangeQty(event) {
    const product = {
      productId: $(event.currentTarget).data('product-id'),
      attributeId: $(event.currentTarget).data('attribute-id'),
      customizationId: $(event.currentTarget).data('customization-id'),
      newQty: $(event.currentTarget).val(),
    };

    this.productManager.changeProductQty(this.cartId, product);
  }

  /**
   * Renders cart summary on the page
   *
   * @param {Object} cartInfo
   *
   * @private
   */
  renderCartInfo(cartInfo) {
    this.addressesRenderer.render(cartInfo.addresses);
    this.cartRulesRenderer.renderCartRulesBlock(cartInfo.cartRules, cartInfo.products.length === 0);
    this.shippingRenderer.render(cartInfo.shipping, cartInfo.products.length === 0);
    this.productRenderer.cleanCartBlockAlerts();
    this.productRenderer.renderList(cartInfo.products);
    this.summaryRenderer.render(cartInfo);
    this.preselectCartCurrency(cartInfo.currencyId);
    this.preselectCartLanguage(cartInfo.langId);

    $(createOrderMap.cartBlock).removeClass('d-none');
  }

  /**
   * Sets cart currency selection value
   *
   * @param currencyId
   *
   * @private
   */
  preselectCartCurrency(currencyId) {
    $(createOrderMap.cartCurrencySelect).val(currencyId);
  }

  /**
   * Sets cart language selection value
   *
   * @param langId
   *
   * @private
   */
  preselectCartLanguage(langId) {
    $(createOrderMap.cartLanguageSelect).val(langId);
  }

  /**
   * Changes cart addresses
   *
   * @private
   */
  changeCartAddresses() {
    const addresses = {
      deliveryAddressId: $(createOrderMap.deliveryAddressSelect).val(),
      invoiceAddressId: $(createOrderMap.invoiceAddressSelect).val(),
    };

    this.cartEditor.changeCartAddresses(this.cartId, addresses);
  }
}
