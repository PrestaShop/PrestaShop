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

import Router from '@components/router';
import {EventEmitter} from '@components/event-emitter';
import _ from 'lodash';
import createOrderMap from './create-order-map';
import CustomerManager from './customer-manager';
import ShippingRenderer from './shipping-renderer';
import CartProvider from './cart-provider';
import AddressesRenderer from './addresses-renderer';
import CartRulesRenderer from './cart-rules-renderer';
import CartEditor, {CartProduct} from './cart-editor';
import eventMap from './event-map';
import CartRuleManager from './cart-rule-manager';
import ProductManager from './product-manager';
import ProductRenderer from './product-renderer';
import SummaryRenderer from './summary-renderer';
import SummaryManager from './summary-manager';
import {ValidateAddresses} from './address-validator';

const {$} = window;

export interface CartAddress {
  addressId: number;
  alias: string;
  delivery: boolean;
  formattedAddress: string;
}

export interface CartInfo {
  addresses: Array<CartAddress>;
  cartId: number;
  cartRules: any;
  currencyId: number;
  langId: number;
  products: Array<CartInfoProduct>;
  shipping: CartShipping;
  summary: CartSummary;
}

export interface CartInfoProduct {
  attribute: string;
  attributeId: number;
  availableOutOfStock: boolean;
  availableStock: number;
  customization: Array<CustomizationField>;
  gift: boolean
  imageLink: string;
  name: string;
  price: string;
  productId: number;
  quantity: number;
  reference: string;
  unitPrice: string;
}

export interface CartShipping {
  deliveryOptions: Array<CartDeliveryOptions>;
  freeShipping: boolean;
  gift: boolean;
  giftMessage: string;
  recycledPackaging: boolean;
  selectedCarrierId: number | null | string;
  virtual: boolean;
}

export interface CartSummary {
  orderMessage: string;
  processOrderLink: string;
  totalDiscount: string;
  totalPriceWithTaxes: string;
  totalPriceWithoutTaxes: string;
  totalProductsPrice: string;
  totalShippingPrice: string;
  totalShippingWithoutTaxes: string;
}

export interface CartDeliveryOptions {
  carrierId: number;
  carrierName: string;
  carrierDelay: string;
}

export interface CartCustomization {
  customizationId: number;
  customizationFieldsData: Array<CustomizationField>;
}

export interface CustomizationField {
  type: number;
  name: string;
  value: string;
}

/**
 * Page Object for "Create order" page
 */
export default class CreateOrderPage {
  cartId: number | null;

  customerId: number | null;

  $container: JQuery;

  cartProvider: CartProvider;

  timeoutId: number | null | ReturnType<typeof setTimeout>;

  customerManager: CustomerManager;

  shippingRenderer: ShippingRenderer;

  addressesRenderer: AddressesRenderer;

  cartRulesRenderer: CartRulesRenderer;

  router: Router;

  cartEditor: CartEditor;

  cartRuleManager: CartRuleManager;

  productManager: ProductManager;

  productRenderer: ProductRenderer;

  summaryRenderer: SummaryRenderer;

  summaryManager: SummaryManager;

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
    this.timeoutId = 0;

    this.initListeners();
    this.loadCartFromUrlParams();
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
  static validateSelectedAddresses(addresses: Record<string, any>): boolean {
    let deliveryValid = false;
    let invoiceValid = false;

    const keys = Object.keys(addresses);

    for (let i = 0; i < keys.length; i += 1) {
      const address = addresses[keys[i]];

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
  hideCartInfo(): void {
    $(createOrderMap.cartInfoWrapper).addClass('d-none');
  }

  /**
   * Shows whole cart information wrapper
   */
  showCartInfo(): void {
    $(createOrderMap.cartInfoWrapper).removeClass('d-none');
  }

  /**
   * Loads cart if query params contains valid cartId
   *
   * @private
   */
  loadCartFromUrlParams(): void {
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
  private initListeners(): void {
    this.$container.on('input', createOrderMap.customerSearchInput, (e: JQueryEventObject) => this.initCustomerSearch(e),
    );
    this.$container.on('click', createOrderMap.chooseCustomerBtn, (e: JQueryEventObject) => this.initCustomerSelect(e),
    );
    this.$container.on('click', createOrderMap.useCartBtn, (e: JQueryEventObject) => this.initCartSelect(e),
    );
    this.$container.on('click', createOrderMap.useOrderBtn, (e: JQueryEventObject) => this.initDuplicateOrderCart(e),
    );
    this.$container.on('input', createOrderMap.productSearch, (e: JQueryEventObject) => this.initProductSearch(e),
    );
    this.$container.on('input', createOrderMap.cartRuleSearchInput, (e: JQueryEventObject) => this.initCartRuleSearch(e),
    );
    this.$container.on('blur', createOrderMap.cartRuleSearchInput, () => this.cartRuleManager.stopSearching(),
    );
    this.listenForCartEdit();
    this.onCartLoaded();
    this.onCustomersNotFound();
    this.onCustomerSelected();
    this.initAddressButtonsIframe();
    this.initCartRuleButtonsIframe();
  }

  /**
   * @private
   */
  private initAddressButtonsIframe(): void {
    $(createOrderMap.addressAddBtn).fancybox({
      type: 'iframe',
      width: '90%',
      height: '90%',
    });

    $(createOrderMap.invoiceAddressEditBtn).fancybox({
      type: 'iframe',
      width: '90%',
      height: '90%',
    });

    $(createOrderMap.deliveryAddressEditBtn).fancybox({
      type: 'iframe',
      width: '90%',
      height: '90%',
    });
  }

  initCartRuleButtonsIframe(): void {
    $('#js-add-cart-rule-btn').fancybox({
      type: 'iframe',
      width: '90%',
      height: '90%',
    });
  }

  /**
   * Delegates actions to events associated with cart update (e.g. change cart address)
   *
   * @private
   */
  private listenForCartEdit(): void {
    this.onCartAddressesChanged();
    this.onDeliveryOptionChanged();
    this.onDeliverySettingChanged();
    this.addCartRuleToCart();
    this.removeCartRuleFromCart();
    this.onCartCurrencyChanged();
    this.onCartLanguageChanged();

    this.$container.on('change', createOrderMap.deliveryOptionSelect, (e) => this.cartEditor.changeDeliveryOption(
        <number> this.cartId,
        e.currentTarget.value,
    ),
    );

    this.$container.on('change', createOrderMap.freeShippingSwitch, () => {
      this.cartEditor.updateDeliveryOptions(<number> this.cartId);
    });

    this.$container.on('change', createOrderMap.recycledPackagingSwitch, () => {
      this.cartEditor.updateDeliveryOptions(<number> this.cartId);
    });

    this.$container.on('change', createOrderMap.isAGiftSwitch, () => this.cartEditor.updateDeliveryOptions(<number> this.cartId),
    );

    this.$container.on('blur', createOrderMap.giftMessageField, () => this.cartEditor.updateDeliveryOptions(<number> this.cartId),
    );

    this.$container.on('click', createOrderMap.addToCartButton, () => this.productManager.addProductToCart(<number> this.cartId),
    );

    this.$container.on('change', createOrderMap.cartCurrencySelect, (e) => this.cartEditor.changeCartCurrency(
        <number> this.cartId,
        e.currentTarget.value,
    ),
    );

    this.$container.on('change', createOrderMap.cartLanguageSelect, (e) => this.cartEditor.changeCartLanguage(
        <number> this.cartId,
        e.currentTarget.value,
    ),
    );

    // eslint-disable-next-line
    this.$container.on('click', createOrderMap.sendProcessOrderEmailBtn, () => this.summaryManager.sendProcessOrderEmail(<number> this.cartId),
    );

    this.$container.on(
      'change',
      createOrderMap.listedProductUnitPriceInput,
      (e: JQueryEventObject) => this.initProductChangePrice(e),
    );
    this.$container.on(
      'change',
      createOrderMap.listedProductQtyInput,
      _.debounce((e: JQueryEventObject) => {
        const inputsQty = document.querySelectorAll(
          createOrderMap.listedProductQtyInput,
        );

        inputsQty.forEach((inputQty) => {
          inputQty.setAttribute('disabled', 'true');
        });
        this.initProductChangeQty(e);
      }, 500),
    );
    this.$container.on('change', createOrderMap.addressSelect, () => this.changeCartAddresses(),
    );
    this.$container.on(
      'click',
      createOrderMap.productRemoveBtn,
      (e: JQueryEventObject) => this.initProductRemoveFromCart(e),
    );
  }

  /**
   * Listens for event when cart is loaded
   *
   * @private
   */
  private onCartLoaded(): void {
    EventEmitter.on(eventMap.cartLoaded, (cartInfo) => {
      this.cartId = cartInfo.cartId;
      this.renderCartInfo(cartInfo);
      if (
        cartInfo.addresses.length !== 0
        && !ValidateAddresses(cartInfo.addresses)
      ) {
        this.changeCartAddresses();
      }
      this.customerManager.loadCustomerCarts(<string><unknown> this.cartId);
      this.customerManager.loadCustomerOrders();
    });
  }

  /**
   * Listens for event when no customers were found by search
   *
   * @private
   */
  private onCustomersNotFound(): void {
    EventEmitter.on(eventMap.customersNotFound, () => {
      this.hideCartInfo();
    });
  }

  /**
   * Listens for event when customer is selected
   *
   * @private
   */
  private onCustomerSelected() {
    EventEmitter.on(eventMap.customerSelected, () => {
      this.showCartInfo();
    });
  }

  /**
   * Listens for cart addresses update event
   *
   * @private
   */
  private onCartAddressesChanged() {
    EventEmitter.on(eventMap.cartAddressesChanged, (cartInfo) => {
      this.addressesRenderer.render(cartInfo.addresses, cartInfo.cartId);
      this.cartRulesRenderer.renderCartRulesBlock(
        cartInfo.cartRules,
        cartInfo.products.length === 0,
      );
      this.shippingRenderer.render(
        cartInfo.shipping,
        cartInfo.products.length === 0,
      );
      this.productRenderer.renderList(cartInfo.products);
      this.summaryRenderer.render(cartInfo);
    });
  }

  /**
   * Listens for cart delivery option update event
   *
   * @private
   */
  private onDeliveryOptionChanged() {
    EventEmitter.on(eventMap.cartDeliveryOptionChanged, (cartInfo) => {
      this.cartRulesRenderer.renderCartRulesBlock(
        cartInfo.cartRules,
        cartInfo.products.length === 0,
      );
      this.shippingRenderer.render(
        cartInfo.shipping,
        cartInfo.products.length === 0,
      );
      this.summaryRenderer.render(cartInfo);
      this.productRenderer.renderList(cartInfo.products);
    });
  }

  /**
   * Listens for cart delivery setting update event
   *
   * @private
   */
  private onDeliverySettingChanged() {
    EventEmitter.on(eventMap.cartDeliverySettingChanged, (cartInfo) => {
      this.cartRulesRenderer.renderCartRulesBlock(
        cartInfo.cartRules,
        cartInfo.products.length === 0,
      );
      this.shippingRenderer.render(
        cartInfo.shipping,
        cartInfo.products.length === 0,
      );
      this.summaryRenderer.render(cartInfo);
    });
  }

  /**
   * Listens for cart language update event
   *
   * @private
   */
  private onCartLanguageChanged() {
    EventEmitter.on(eventMap.cartLanguageChanged, (cartInfo: CartInfo) => {
      this.preselectCartLanguage(cartInfo.langId);
      this.renderCartInfo(cartInfo);
    });
  }

  /**
   * Listens for cart currency update event
   *
   * @private
   */
  private onCartCurrencyChanged() {
    // on success
    EventEmitter.on(eventMap.cartCurrencyChanged, (cartInfo: CartInfo) => {
      this.renderCartInfo(cartInfo);
      this.productRenderer.reset();
    });

    // on failure
    EventEmitter.on(eventMap.cartCurrencyChangeFailed, (response) => {
      this.productRenderer.renderCartBlockErrorAlert(
        response.responseJSON.message,
      );
    });
  }

  /**
   * Init customer searching
   *
   * @param event
   *
   * @private
   */
  private initCustomerSearch(event: JQueryEventObject) {
    clearTimeout(<number> this.timeoutId);
    this.timeoutId = setTimeout(
      () => this.customerManager.search(<string>$(event.currentTarget).val()),
      300,
    );
  }

  /**
   * Init selecting customer for which order is being created
   *
   * @param event
   *
   * @private
   */
  private initCustomerSelect(event: JQueryEventObject) {
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
  private initCartSelect(event: JQueryEventObject) {
    const cartId = $(event.currentTarget).data('cart-id');
    this.cartProvider.getCart(cartId);
  }

  /**
   * Inits duplicating order cart
   *
   * @private
   */
  private initDuplicateOrderCart(event: JQueryEventObject) {
    const orderId = $(event.currentTarget).data('order-id');
    this.cartProvider.duplicateOrderCart(orderId);
  }

  /**
   * Triggers cart rule searching
   *
   * @private
   */
  private initCartRuleSearch(event: JQueryEventObject) {
    const input = <HTMLInputElement>event.currentTarget;
    const searchPhrase = input.value;

    clearTimeout(<ReturnType<typeof setTimeout>> this.timeoutId);
    this.timeoutId = setTimeout(
      () => this.cartRuleManager.search(searchPhrase),
      300,
    );
  }

  /**
   * Triggers cart rule select
   *
   * @private
   */
  private addCartRuleToCart() {
    this.$container
      .on('mousedown', createOrderMap.foundCartRuleListItem, (event) => {
        // prevent blur event to allow selecting cart rule
        event.preventDefault();
        const cartRuleId = $(event.currentTarget).data('cart-rule-id');
        this.cartRuleManager.addCartRuleToCart(cartRuleId, <number> this.cartId);

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
  private removeCartRuleFromCart() {
    this.$container.on('click', createOrderMap.cartRuleDeleteBtn, (event) => {
      this.cartRuleManager.removeCartRuleFromCart(
        $(event.currentTarget).data('cart-rule-id'),
        <number> this.cartId,
      );
    });
  }

  /**
   * Inits product searching
   *
   * @param event
   *
   * @private
   */
  private initProductSearch(event: JQueryEventObject) {
    const $productSearchInput = $(event.currentTarget);
    const searchPhrase = $productSearchInput.val();
    clearTimeout(<ReturnType<typeof setTimeout>> this.timeoutId);

    this.timeoutId = setTimeout(
      () => this.productManager.search(<string>searchPhrase),
      300,
    );
  }

  /**
   * Inits product removing from cart
   *
   * @param event
   *
   * @private
   */
  private initProductRemoveFromCart(event: JQueryEventObject) {
    const productQty = Number(
      $(event.currentTarget)
        .parents()
        .find(createOrderMap.listedProductQtyInput)
        .val(),
    );
    const product = {
      productId: $(event.currentTarget).data('product-id'),
      attributeId: $(event.currentTarget).data('attribute-id'),
      customizationId: $(event.currentTarget).data('customization-id'),
      qtyToRemove: productQty,
    };

    this.productManager.removeProductFromCart(<number> this.cartId, product);
  }

  /**
   * Inits product in cart price change
   *
   * @param event
   *
   * @private
   */
  private initProductChangePrice(event: JQueryEventObject) {
    const product: CartProduct = {
      productId: $(event.currentTarget).data('product-id'),
      attributeId: $(event.currentTarget).data('attribute-id'),
      customizationId: $(event.currentTarget).data('customization-id'),
      price: <string>$(event.currentTarget).val(),
    };
    this.productManager.changeProductPrice(
      <number> this.cartId,
      <number> this.customerId,
      product,
    );
  }

  /**
   * Inits product in cart quantity update
   *
   * @param event
   *
   * @private
   */
  private initProductChangeQty(event: JQueryEventObject) {
    const product: CartProduct = {
      productId: $(event.currentTarget).data('product-id'),
      attributeId: $(event.currentTarget).data('attribute-id'),
      customizationId: $(event.currentTarget).data('customization-id'),
      newQty: <string>$(event.currentTarget).val(),
      prevQty: $(event.currentTarget).data('prev-qty'),
    };

    if (
      typeof product.productId !== 'undefined'
      && product.productId !== null
      && typeof product.attributeId !== 'undefined'
      && product.attributeId !== null
    ) {
      this.productManager.changeProductQty(<number> this.cartId, product);
    } else {
      const inputsQty = <NodeListOf<HTMLInputElement>>(
        document.querySelectorAll(createOrderMap.listedProductQtyInput)
      );

      inputsQty.forEach((inputQty: HTMLInputElement) => {
        // eslint-disable-next-line
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
  private renderCartInfo(cartInfo: CartInfo): void {
    this.addressesRenderer.render(cartInfo.addresses, cartInfo.cartId);
    this.cartRulesRenderer.renderCartRulesBlock(
      cartInfo.cartRules,
      cartInfo.products.length === 0,
    );
    this.shippingRenderer.render(
      cartInfo.shipping,
      cartInfo.products.length === 0,
    );
    this.productRenderer.cleanCartBlockAlerts();
    this.productRenderer.renderList(cartInfo.products);
    this.summaryRenderer.render(cartInfo);
    this.preselectCartCurrency(cartInfo.currencyId);
    this.preselectCartLanguage(cartInfo.langId);

    $(createOrderMap.cartBlock).removeClass('d-none');
    $(createOrderMap.cartBlock).data('cartId', cartInfo.cartId);
    if (cartInfo.shipping.virtual) {
      $(createOrderMap.shippingBlock).addClass('d-none');
    }
  }

  /**
   * Sets cart currency selection value
   *
   * @param currencyId
   *
   * @private
   */
  private preselectCartCurrency(currencyId: number) {
    $(createOrderMap.cartCurrencySelect).val(currencyId);
  }

  /**
   * Sets cart language selection value
   *
   * @param langId
   *
   * @private
   */
  private preselectCartLanguage(langId: number) {
    $(createOrderMap.cartLanguageSelect).val(langId);
  }

  /**
   * Changes cart addresses
   *
   * @private
   */
  private changeCartAddresses() {
    const addresses = {
      deliveryAddressId: <string>$(createOrderMap.deliveryAddressSelect).val(),
      invoiceAddressId: <string>$(createOrderMap.invoiceAddressSelect).val(),
    };

    this.cartEditor.changeCartAddresses(<number> this.cartId, addresses);
  }

  /**
   * Refresh addresses list
   *
   * @param {boolean} refreshCartAddresses optional
   */
  refreshAddressesList(refreshCartAddresses: boolean): void {
    const cartId = $(createOrderMap.cartBlock).data('cartId');
    $.get(this.router.generate('admin_carts_info', {cartId}))
      .then((cartInfo) => {
        this.addressesRenderer.render(cartInfo.addresses, cartInfo.cartId);

        if (refreshCartAddresses) {
          this.changeCartAddresses();
        }
      })
      .catch((e: Record<string, any>) => {
        window.showErrorMessage(e.responseJSON.message);
      });
  }

  search(string: string): void {
    this.customerManager.search(string);
  }

  /**
   * proxy to allow other scripts within the page to refresh addresses list
   */
  refreshCart(): void {
    const cartId = $(createOrderMap.cartBlock).data('cartId');
    this.cartProvider.getCart(cartId);
  }
}
