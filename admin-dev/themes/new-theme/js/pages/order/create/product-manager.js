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

import CartEditor from '@pages/order/create/cart-editor';
import createOrderMap from '@pages/order/create/create-order-map';
import eventMap from '@pages/order/create/event-map';
import {EventEmitter} from '@components/event-emitter';
import ProductRenderer from '@pages/order/create/product-renderer';
import Router from '@components/router';

const $ = window.$;

/**
 * Product component Object for "Create order" page
 */
export default class ProductManager {
  constructor() {
    this.products = [];
    this.selectedProduct = null;
    this.selectedCombinationId = null;
    this.activeSearchRequest = null;

    this.productRenderer = new ProductRenderer();
    this.router = new Router();
    this.cartEditor = new CartEditor();

    this._initListeners();

    return {
      search: searchPhrase => this._search(searchPhrase),

      addProductToCart: cartId => this.cartEditor.addProduct(cartId, this._getProductData()),

      removeProductFromCart: (cartId, product) => this.cartEditor.removeProductFromCart(cartId, product),

      changeProductPrice: (cartId, customerId, updatedProduct) =>
        this.cartEditor.changeProductPrice(cartId, customerId, updatedProduct),

      changeProductQty: (cartId, updatedProduct) => this.cartEditor.changeProductQty(cartId, updatedProduct)
    };
  }

  /**
   * Initializes event listeners
   *
   * @private
   */
  _initListeners() {
    $(createOrderMap.productSelect).on('change', e => this._initProductSelect(e));
    $(createOrderMap.combinationsSelect).on('change', e => this._initCombinationSelect(e));

    this._onProductSearch();
    this._onAddProductToCart();
    this._onRemoveProductFromCart();
    this._onProductPriceChange();
    this._onProductQtyChange();
  }

  /**
   * Listens for product search event
   *
   * @private
   */
  _onProductSearch() {
    EventEmitter.on(eventMap.productSearched, response => {
      this.products = response.products;
      this.productRenderer.renderSearchResults(this.products);
      this._selectFirstResult();
    });
  }

  /**
   * Listens for add product to cart event
   *
   * @private
   */
  _onAddProductToCart() {
    // on success
    EventEmitter.on(eventMap.productAddedToCart, cartInfo => {
      this.productRenderer.cleanCartBlockAlerts();
      EventEmitter.emit(eventMap.cartLoaded, cartInfo);
    });

    // on failure
    EventEmitter.on(eventMap.productAddToCartFailed, errorMessage => {
      this.productRenderer.renderCartBlockErrorAlert(errorMessage);
    });
  }

  /**
   * Listens for remove product from cart event
   *
   * @private
   */
  _onRemoveProductFromCart() {
    EventEmitter.on(eventMap.productRemovedFromCart, cartInfo => {
      EventEmitter.emit(eventMap.cartLoaded, cartInfo);
    });
  }

  /**
   * Listens for product price change in cart event
   *
   * @private
   */
  _onProductPriceChange() {
    EventEmitter.on(eventMap.productPriceChanged, cartInfo => {
      this.productRenderer.cleanCartBlockAlerts();
      EventEmitter.emit(eventMap.cartLoaded, cartInfo);
    });
  }

  /**
   * Listens for product quantity change in cart success/failure event
   *
   * @private
   */
  _onProductQtyChange() {
    const enableQtyInputs = () => {
      const inputsQty = document.querySelectorAll(createOrderMap.listedProductQtyInput);

      inputsQty.forEach(inputQty => {
        inputQty.disabled = false;
      });
    };

    // on success
    EventEmitter.on(eventMap.productQtyChanged, cartInfo => {
      this.productRenderer.cleanCartBlockAlerts();
      $(createOrderMap.createOrderButton).prop('disabled', false);
      EventEmitter.emit(eventMap.cartLoaded, cartInfo);

      enableQtyInputs();
    });

    // on failure
    EventEmitter.on(eventMap.productQtyChangeFailed, e => {
      this.productRenderer.renderCartBlockErrorAlert(e.responseJSON.message);
      $(createOrderMap.createOrderButton).prop('disabled', true);
      enableQtyInputs();
    });
  }

  /**
   * Initializes product select
   *
   * @param event
   *
   * @private
   */
  _initProductSelect(event) {
    const productId = Number(
      $(event.currentTarget)
        .find(':selected')
        .val()
    );
    this._selectProduct(productId);
  }

  /**
   * Initializes combination select
   *
   * @param event
   *
   * @private
   */
  _initCombinationSelect(event) {
    const combinationId = Number(
      $(event.currentTarget)
        .find(':selected')
        .val()
    );
    this._selectCombination(combinationId);
  }

  /**
   * Searches for product
   *
   * @private
   */
  _search(searchPhrase) {
    if (searchPhrase.length < 2) {
      return;
    }

    this.productRenderer.renderSearching();
    if (this.activeSearchRequest !== null) {
      this.activeSearchRequest.abort();
    }

    const params = {
      search_phrase: searchPhrase
    };
    if ($(createOrderMap.cartCurrencySelect).data('selectedCurrencyId') != undefined) {
      params.currency_id = $(createOrderMap.cartCurrencySelect).data('selectedCurrencyId');
    }

    const $searchRequest = $.get(this.router.generate('admin_orders_products_search'), params);
    this.activeSearchRequest = $searchRequest;

    $searchRequest
      .then(response => {
        EventEmitter.emit(eventMap.productSearched, response);
      })
      .catch(response => {
        if (response.statusText === 'abort') {
          return;
        }

        showErrorMessage(response.responseJSON.message);
      });
  }

  /**
   * Initiate first result dataset after search
   *
   * @private
   */
  _selectFirstResult() {
    this._unsetProduct();

    const values = Object.values(this.products);

    if (values.length !== 0) {
      this._selectProduct(values[0].productId);
    }
  }

  /**
   * Handles use case when product is selected from search results
   *
   * @private
   *
   * @param {Number} productId
   */
  _selectProduct(productId) {
    this._unsetCombination();

    for (const key in this.products) {
      if (this.products[key].productId === productId) {
        this.selectedProduct = this.products[key];

        break;
      }
    }

    this.productRenderer.renderProductMetadata(this.selectedProduct);
    // if product has combinations select the first else leave it null
    if (this.selectedProduct.combinations.length !== 0) {
      this._selectCombination(Object.keys(this.selectedProduct.combinations)[0]);
    }

    return this.selectedProduct;
  }

  /**
   * Handles use case when new combination is selected
   *
   * @param combinationId
   *
   * @private
   */
  _selectCombination(combinationId) {
    const combination = this.selectedProduct.combinations[combinationId];

    this.selectedCombinationId = combinationId;
    this.productRenderer.renderStock(
      $(createOrderMap.inStockCounter),
      $(createOrderMap.quantityInput),
      combination.stock,
      this.selectedProduct.availableOutOfStock || combination.stock <= 0
    );

    return combination;
  }

  /**
   * Sets the selected combination id to null
   *
   * @private
   */
  _unsetCombination() {
    this.selectedCombinationId = null;
  }

  /**
   * Sets the selected product to null
   *
   * @private
   */
  _unsetProduct() {
    this.selectedProduct = null;
  }

  /**
   * Retrieves product data from product search result block fields
   *
   * @returns {Object}
   *
   * @private
   */
  _getProductData() {
    const $fileInputs = $(createOrderMap.productCustomizationContainer).find('input[type="file"]');
    const formData = new FormData(document.querySelector(createOrderMap.productAddForm));
    const fileSizes = {};

    // adds key value pairs {input name: file size} of each file in separate object in case formData size exceeds server settings.
    $.each($fileInputs, (key, input) => {
      if (input.files.length !== 0) {
        fileSizes[$(input).data('customization-field-id')] = input.files[0].size;
      }
    });

    return {
      product: formData,
      fileSizes
    };
  }
}
