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

import CartEditor from '@pages/order/create/cart-editor';
import createOrderMap from '@pages/order/create/create-order-map';
import eventMap from '@pages/order/create/event-map';
import {EventEmitter} from '@components/event-emitter';
import ProductRenderer from '@pages/order/create/product-renderer';
import Router from '@components/router';

const {$} = window;

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

    this.initListeners();

    return {
      search: (searchPhrase) => this.search(searchPhrase),
      addProductToCart: (cartId) => this.cartEditor.addProduct(cartId, this.getProductData()),
      removeProductFromCart: (cartId, product) => this.cartEditor.removeProductFromCart(cartId, product),
      changeProductPrice: (cartId, customerId, updatedProduct) => this.cartEditor.changeProductPrice(
        cartId,
        customerId,
        updatedProduct,
      ),
      changeProductQty: (cartId, updatedProduct) => this.cartEditor.changeProductQty(cartId, updatedProduct),
    };
  }

  /**
   * Initializes event listeners
   *
   * @private
   */
  initListeners() {
    $(createOrderMap.productSelect).on('change', (e) => this.initProductSelect(e));
    $(createOrderMap.combinationsSelect).on('change', (e) => this.initCombinationSelect(e));

    this.onProductSearch();
    this.onAddProductToCart();
    this.onRemoveProductFromCart();
    this.onProductPriceChange();
    this.onProductQtyChange();
  }

  /**
   * Listens for product search event
   *
   * @private
   */
  onProductSearch() {
    EventEmitter.on(eventMap.productSearched, (response) => {
      this.products = response.products;
      this.productRenderer.renderSearchResults(this.products);
      this.selectFirstResult();
    });
  }

  /**
   * Listens for add product to cart event
   *
   * @private
   */
  onAddProductToCart() {
    // on success
    EventEmitter.on(eventMap.productAddedToCart, (cartInfo) => {
      this.productRenderer.cleanCartBlockAlerts();
      EventEmitter.emit(eventMap.cartLoaded, cartInfo);
    });

    // on failure
    EventEmitter.on(eventMap.productAddToCartFailed, (errorMessage) => {
      this.productRenderer.renderCartBlockErrorAlert(errorMessage);
    });
  }

  /**
   * Listens for remove product from cart event
   *
   * @private
   */
  onRemoveProductFromCart() {
    EventEmitter.on(eventMap.productRemovedFromCart, (cartInfo) => {
      EventEmitter.emit(eventMap.cartLoaded, cartInfo);
    });
  }

  /**
   * Listens for product price change in cart event
   *
   * @private
   */
  onProductPriceChange() {
    EventEmitter.on(eventMap.productPriceChanged, (cartInfo) => {
      this.productRenderer.cleanCartBlockAlerts();
      EventEmitter.emit(eventMap.cartLoaded, cartInfo);
    });
  }

  /**
   * Listens for product quantity change in cart success/failure event
   *
   * @private
   */
  onProductQtyChange() {
    // on success
    EventEmitter.on(eventMap.productQtyChanged, (cartInfo) => {
      this.productRenderer.cleanCartBlockAlerts();
      EventEmitter.emit(eventMap.cartLoaded, cartInfo);
    });

    // on failure
    EventEmitter.on(eventMap.productQtyChangeFailed, (e) => {
      this.productRenderer.renderCartBlockErrorAlert(e.responseJSON.message);
    });
  }

  /**
   * Initializes product select
   *
   * @param event
   *
   * @private
   */
  initProductSelect(event) {
    const productId = Number($(event.currentTarget).find(':selected').val());
    this.selectProduct(productId);
  }

  /**
   * Initializes combination select
   *
   * @param event
   *
   * @private
   */
  initCombinationSelect(event) {
    const combinationId = Number($(event.currentTarget).find(':selected').val());
    this.selectCombination(combinationId);
  }

  /**
   * Searches for product
   *
   * @private
   */
  search(searchPhrase) {
    if (searchPhrase.length < 3) {
      return;
    }

    if (this.activeSearchRequest !== null) {
      this.activeSearchRequest.abort();
    }

    const params = {
      search_phrase: searchPhrase,
    };

    if ($(createOrderMap.cartCurrencySelect).data('selectedCurrencyId') !== undefined) {
      params.currency_id = $(createOrderMap.cartCurrencySelect).data('selectedCurrencyId');
    }

    const $searchRequest = $.get(this.router.generate('admin_products_search'), params);
    this.activeSearchRequest = $searchRequest;

    $searchRequest.then((response) => {
      EventEmitter.emit(eventMap.productSearched, response);
    }).catch((response) => {
      if (response.statusText === 'abort') {
        return;
      }

      window.showErrorMessage(response.responseJSON.message);
    });
  }

  /**
   * Initiate first result dataset after search
   *
   * @private
   */
  selectFirstResult() {
    this.unsetProduct();

    if (this.products.length !== 0) {
      this.selectProduct(this.products[0].productId);
    }
  }

  /**
   * Handles use case when product is selected from search results
   *
   * @private
   *
   * @param {Number} productId
   */
  selectProduct(productId) {
    this.unsetCombination();

    const selectedProduct = Object.values(this.products).find((product) => product.productId === productId);
    if (selectedProduct) {
      this.selectedProduct = selectedProduct;
    }

    this.productRenderer.renderProductMetadata(this.selectedProduct);
    // if product has combinations select the first else leave it null
    if (this.selectedProduct.combinations.length !== 0) {
      this.selectCombination(Object.keys(this.selectedProduct.combinations)[0]);
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
  selectCombination(combinationId) {
    const combination = this.selectedProduct.combinations[combinationId];

    this.selectedCombinationId = combinationId;
    this.productRenderer.renderStock(combination.stock);

    return combination;
  }

  /**
   * Sets the selected combination id to null
   *
   * @private
   */
  unsetCombination() {
    this.selectedCombinationId = null;
  }

  /**
   * Sets the selected product to null
   *
   * @private
   */
  unsetProduct() {
    this.selectedProduct = null;
  }

  /**
   * Retrieves product data from product search result block fields
   *
   * @returns {Object}
   *
   * @private
   */
  getProductData() {
    const $fileInputs = $(createOrderMap.productCustomizationContainer).find('input[type="file"]');
    const formData = new FormData(document.querySelector(createOrderMap.productAddForm));
    const fileSizes = {};

    // adds key value pairs {input name: file size} of each file in separate object
    // in case formData size exceeds server settings.
    $.each($fileInputs, (key, input) => {
      if (input.files.length !== 0) {
        fileSizes[$(input).data('customization-field-id')] = input.files[0].size;
      }
    });

    return {
      product: formData,
      fileSizes,
    };
  }
}
