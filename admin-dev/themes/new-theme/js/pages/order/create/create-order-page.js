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

import createOrderMap from './create-order-map';
import CustomerManager from './customer-manager';
import ShippingRenderer from './shipping-renderer';
import CartProvider from './cart-provider';
import AddressesRenderer from './addresses-renderer';
import CartRulesRenderer from './cart-rules-renderer';
import Router from '../../../components/router';
import {EventEmitter} from '../../../components/event-emitter';
import CartEditor from './cart-editor';
import eventMap from './event-map';
import CartRuleManager from './cart-rule-manager';
import ProductManager from './product-manager';
import ProductRenderer from './product-renderer';
import SummaryRenderer from './summary-renderer';
import SummaryManager from './summary-manager';
import _ from 'lodash';

const $ = window.$;

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

    this._initListeners();
    this._loadCartFromUrlParams();

    return {
      refreshAddressesList: refreshCartAddresses => this.refreshAddressesList(refreshCartAddresses),
      refreshCart: refreshCart => this.refreshCart(refreshCart),
      search: string => this.customerManager.search(string)
    };
  }

  /**
   * Checks if correct addresses are selected.
   * There is a case when options list cannot contain cart addresses 'selected' values
   *  because those are outdated in db (e.g. deleted after cart creation or country is disabled)
   *
   * @param {Array} addresses
   *
   * @returns {boolean}
   */
  static validateSelectedAddresses(addresses) {
    let deliveryValid = false;
    let invoiceValid = false;

    for (const key in addresses) {
      const address = addresses[key];

      if (address.delivery) {
        deliveryValid = true;
      }

      if (address.invoice) {
        invoiceValid = true;
      }

      if (deliveryValid && invoiceValid) {
        return true;
      }
    }

    return false;
  }

  /**
   * Hides whole cart information wrapper
   */
  hideCartInfo() {
    $(createOrderMap.cartInfoWrapper).addClass('d-none');
  }

  /**
   * Shows whole cart information wrapper
   */
  showCartInfo() {
    $(createOrderMap.cartInfoWrapper).removeClass('d-none');
  }

  /**
   * Loads cart if query params contains valid cartId
   *
   * @private
   */
  _loadCartFromUrlParams() {
    const urlParams = new URLSearchParams(window.location.search);
    const cartId = Number(urlParams.get('cartId'));

    if (!isNaN(cartId) && cartId !== 0) {
      this.cartProvider.getCart(cartId);
    }
  }

  /**
   * Initializes event listeners
   *
   * @private
   */
  _initListeners() {
    this.$container.on('input', createOrderMap.customerSearchInput, e => this._initCustomerSearch(e));
    this.$container.on('click', createOrderMap.chooseCustomerBtn, e => this._initCustomerSelect(e));
    this.$container.on('click', createOrderMap.useCartBtn, e => this._initCartSelect(e));
    this.$container.on('click', createOrderMap.useOrderBtn, e => this._initDuplicateOrderCart(e));
    this.$container.on('input', createOrderMap.productSearch, e => this._initProductSearch(e));
    this.$container.on('input', createOrderMap.cartRuleSearchInput, e => this._initCartRuleSearch(e));
    this.$container.on('blur', createOrderMap.cartRuleSearchInput, () => this.cartRuleManager.stopSearching());
    this._listenForCartEdit();
    this._onCartLoaded();
    this.onCustomersNotFound();
    this._onCustomerSelected();
    this.initAddressButtonsIframe();
    this.initCartRuleButtonsIframe();
  }

  /**
   * @private
   */
  initAddressButtonsIframe() {
    $(createOrderMap.addressAddBtn).fancybox({
      type: 'iframe',
      width: '90%',
      height: '90%'
    });

    $(createOrderMap.invoiceAddressEditBtn).fancybox({
      type: 'iframe',
      width: '90%',
      height: '90%'
    });

    $(createOrderMap.deliveryAddressEditBtn).fancybox({
      type: 'iframe',
      width: '90%',
      height: '90%'
    });
  }

  initCartRuleButtonsIframe() {
    $('#js-add-cart-rule-btn').fancybox({
      type: 'iframe',
      width: '90%',
      height: '90%'
    });
  }

  /**
   * Delegates actions to events associated with cart update (e.g. change cart address)
   *
   * @private
   */
  _listenForCartEdit() {
    this._onCartAddressesChanged();
    this._onDeliveryOptionChanged();
    this._onDeliverySettingChanged();
    this._addCartRuleToCart();
    this._removeCartRuleFromCart();
    this._onCartCurrencyChanged();
    this._onCartLanguageChanged();

    this.$container.on('change', createOrderMap.deliveryOptionSelect, e =>
      this.cartEditor.changeDeliveryOption(this.cartId, e.currentTarget.value)
    );

    this.$container.on('change', createOrderMap.freeShippingSwitch, e =>
      this.cartEditor.updateDeliveryOptions(this.cartId)
    );

    this.$container.on('change', createOrderMap.recycledPackagingSwitch, e =>
      this.cartEditor.updateDeliveryOptions(this.cartId)
    );

    this.$container.on('change', createOrderMap.isAGiftSwitch, e => this.cartEditor.updateDeliveryOptions(this.cartId));

    this.$container.on('blur', createOrderMap.giftMessageField, e =>
      this.cartEditor.updateDeliveryOptions(this.cartId)
    );

    this.$container.on('click', createOrderMap.addToCartButton, () =>
      this.productManager.addProductToCart(this.cartId)
    );

    this.$container.on('change', createOrderMap.cartCurrencySelect, e =>
      this.cartEditor.changeCartCurrency(this.cartId, e.currentTarget.value)
    );

    this.$container.on('change', createOrderMap.cartLanguageSelect, e =>
      this.cartEditor.changeCartLanguage(this.cartId, e.currentTarget.value)
    );

    this.$container.on('click', createOrderMap.sendProcessOrderEmailBtn, () =>
      this.summaryManager.sendProcessOrderEmail(this.cartId)
    );

    this.$container.on('change', createOrderMap.listedProductUnitPriceInput, e => this._initProductChangePrice(e));
    this.$container.on(
      'change',
      createOrderMap.listedProductQtyInput,
      _.debounce(e => {
        const inputsQty = document.querySelectorAll(createOrderMap.listedProductQtyInput);

        inputsQty.forEach(inputQty => {
          inputQty.setAttribute('disabled', true);
        });
        this._initProductChangeQty(e);
      }, 500)
    );
    this.$container.on('change', createOrderMap.addressSelect, () => this._changeCartAddresses());
    this.$container.on('click', createOrderMap.productRemoveBtn, e => this._initProductRemoveFromCart(e));
  }

  /**
   * Listens for event when cart is loaded
   *
   * @private
   */
  _onCartLoaded() {
    EventEmitter.on(eventMap.cartLoaded, cartInfo => {
      this.cartId = cartInfo.cartId;
      this._renderCartInfo(cartInfo);
      if (cartInfo.addresses.length !== 0 && !CreateOrderPage.validateSelectedAddresses(cartInfo.addresses)) {
        this._changeCartAddresses();
      }
      this.customerManager.loadCustomerCarts(this.cartId);
      this.customerManager.loadCustomerOrders();
    });
  }

  /**
   * Listens for event when no customers were found by search
   *
   * @private
   */
  onCustomersNotFound() {
    EventEmitter.on(eventMap.customersNotFound, () => {
      this.hideCartInfo();
    });
  }

  /**
   * Listens for event when customer is selected
   *
   * @private
   */
  _onCustomerSelected() {
    EventEmitter.on(eventMap.customerSelected, () => {
      this.showCartInfo();
    });
  }

  /**
   * Listens for cart addresses update event
   *
   * @private
   */
  _onCartAddressesChanged() {
    EventEmitter.on(eventMap.cartAddressesChanged, cartInfo => {
      this.addressesRenderer.render(cartInfo.addresses, cartInfo.cartId);
      this.cartRulesRenderer.renderCartRulesBlock(cartInfo.cartRules, cartInfo.products.length === 0);
      this.shippingRenderer.render(cartInfo.shipping, cartInfo.products.length === 0);
      this.productRenderer.renderList(cartInfo.products);
      this.summaryRenderer.render(cartInfo);
    });
  }

  /**
   * Listens for cart delivery option update event
   *
   * @private
   */
  _onDeliveryOptionChanged() {
    EventEmitter.on(eventMap.cartDeliveryOptionChanged, cartInfo => {
      this.cartRulesRenderer.renderCartRulesBlock(cartInfo.cartRules, cartInfo.products.length === 0);
      this.shippingRenderer.render(cartInfo.shipping, cartInfo.products.length === 0);
      this.summaryRenderer.render(cartInfo);
      this.productRenderer.renderList(cartInfo.products);
    });
  }

  /**
   * @private
   */
  _onDeliverySettingChanged() {
    EventEmitter.on(eventMap.cartDeliverySettingChanged, cartInfo => {
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
  _onCartLanguageChanged() {
    EventEmitter.on(eventMap.cartLanguageChanged, cartInfo => {
      this._preselectCartLanguage(cartInfo.langId);
      this._renderCartInfo(cartInfo);
    });
  }

  /**
   * Listens for cart currency update event
   *
   * @private
   */
  _onCartCurrencyChanged() {
    // on success
    EventEmitter.on(eventMap.cartCurrencyChanged, cartInfo => {
      this._renderCartInfo(cartInfo);
      this.productRenderer.reset();
    });

    // on failure
    EventEmitter.on(eventMap.cartCurrencyChangeFailed, response => {
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
  _initCustomerSearch(event) {
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
  _initCustomerSelect(event) {
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
  _initCartSelect(event) {
    const cartId = $(event.currentTarget).data('cart-id');
    this.cartProvider.getCart(cartId);
  }

  /**
   * Inits duplicating order cart
   *
   * @private
   */
  _initDuplicateOrderCart(event) {
    const orderId = $(event.currentTarget).data('order-id');
    this.cartProvider.duplicateOrderCart(orderId);
  }

  /**
   * Triggers cart rule searching
   *
   * @private
   */
  _initCartRuleSearch(event) {
    const searchPhrase = event.currentTarget.value;

    clearTimeout(this.timeoutId);
    this.timeoutId = setTimeout(() => this.cartRuleManager.search(searchPhrase), 300);
  }

  /**
   * Triggers cart rule select
   *
   * @private
   */
  _addCartRuleToCart() {
    this.$container
      .on('mousedown', createOrderMap.foundCartRuleListItem, event => {
        // prevent blur event to allow selecting cart rule
        event.preventDefault();
        const cartRuleId = $(event.currentTarget).data('cart-rule-id');
        this.cartRuleManager.addCartRuleToCart(cartRuleId, this.cartId);

        // manually fire blur event after cart rule is selected.
      })
      .on('click', createOrderMap.foundCartRuleListItem, () => {
        $(createOrderMap.cartRuleSearchInput).blur();
      });
  }

  /**
   * Triggers cart rule removal from cart
   *
   * @private
   */
  _removeCartRuleFromCart() {
    this.$container.on('click', createOrderMap.cartRuleDeleteBtn, event => {
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
  _initProductSearch(event) {
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
  _initProductRemoveFromCart(event) {
    const product = {
      productId: $(event.currentTarget).data('product-id'),
      attributeId: $(event.currentTarget).data('attribute-id'),
      customizationId: $(event.currentTarget).data('customization-id')
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
  _initProductChangePrice(event) {
    const product = {
      productId: $(event.currentTarget).data('product-id'),
      attributeId: $(event.currentTarget).data('attribute-id'),
      customizationId: $(event.currentTarget).data('customization-id'),
      price: $(event.currentTarget).val()
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
  _initProductChangeQty(event) {
    const product = {
      productId: $(event.currentTarget).data('product-id'),
      attributeId: $(event.currentTarget).data('attribute-id'),
      customizationId: $(event.currentTarget).data('customization-id'),
      newQty: $(event.currentTarget).val()
    };

    if (
      typeof product.productId !== 'undefined' &&
      product.productId !== null &&
      typeof product.attributeId !== 'undefined' &&
      product.attributeId !== null
    ) {
      this.productManager.changeProductQty(this.cartId, product);
    } else {
      const inputsQty = document.querySelectorAll(createOrderMap.listedProductQtyInput);

      inputsQty.forEach(inputQty => {
        inputQty.disabled = false;
      });
    }
  }

  /**
   * Renders cart summary on the page
   *
   * @param {Object} cartInfo
   *
   * @private
   */
  _renderCartInfo(cartInfo) {
    this.addressesRenderer.render(cartInfo.addresses, cartInfo.cartId);
    this.cartRulesRenderer.renderCartRulesBlock(cartInfo.cartRules, cartInfo.products.length === 0);
    this.shippingRenderer.render(cartInfo.shipping, cartInfo.products.length === 0);
    this.productRenderer.cleanCartBlockAlerts();
    this.productRenderer.renderList(cartInfo.products);
    this.summaryRenderer.render(cartInfo);
    this._preselectCartCurrency(cartInfo.currencyId);
    this._preselectCartLanguage(cartInfo.langId);

    $(createOrderMap.cartBlock).removeClass('d-none');
    $(createOrderMap.cartBlock).data('cartId', cartInfo.cartId);
  }

  /**
   * Sets cart currency selection value
   *
   * @param currencyId
   *
   * @private
   */
  _preselectCartCurrency(currencyId) {
    $(createOrderMap.cartCurrencySelect).val(currencyId);
  }

  /**
   * Sets cart language selection value
   *
   * @param langId
   *
   * @private
   */
  _preselectCartLanguage(langId) {
    $(createOrderMap.cartLanguageSelect).val(langId);
  }

  /**
   * Changes cart addresses
   *
   * @private
   */
  _changeCartAddresses() {
    const addresses = {
      deliveryAddressId: $(createOrderMap.deliveryAddressSelect).val(),
      invoiceAddressId: $(createOrderMap.invoiceAddressSelect).val()
    };

    this.cartEditor.changeCartAddresses(this.cartId, addresses);
  }

  /**
   * Refresh addresses list
   *
   * @param {boolean} refreshCartAddresses optional
   *
   * @private
   */
  refreshAddressesList(refreshCartAddresses) {
    const cartId = $(createOrderMap.cartBlock).data('cartId');
    $.get(this.router.generate('admin_carts_info', {cartId}))
      .then(cartInfo => {
        this.addressesRenderer.render(cartInfo.addresses, cartInfo.cartId);

        if (refreshCartAddresses) {
          this._changeCartAddresses();
        }
      })
      .catch(e => {
        showErrorMessage(e.responseJSON.message);
      });
  }

  /**
   * proxy to allow other scripts within the page to refresh addresses list
   */
  refreshCart() {
    const cartId = $(createOrderMap.cartBlock).data('cartId');
    this.cartProvider.getCart(cartId);
  }
}
