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

import CartEditor from './cart-editor';
import createOrderMap from './create-order-map';
import eventMap from './event-map';
import {EventEmitter} from '../../../components/event-emitter';
import ProductRenderer from './product-renderer';
import Router from '../../../components/router';

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

      removeProductFromCart: (cartId, product) =>
        this.cartEditor.removeProductFromCart(cartId, product),

      changeProductPrice: (cartId, customerId, updatedProduct) =>
        this.cartEditor.changeProductPrice(cartId, customerId, updatedProduct),

      changeProductQty: (cartId, updatedProduct) =>
        this.cartEditor.changeProductQty(cartId, updatedProduct),
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
    EventEmitter.on(eventMap.productSearched, (response) => {
      this.products = JSON.parse(response);
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
    EventEmitter.on(eventMap.productAddedToCart, (cartInfo) => {
      EventEmitter.emit(eventMap.cartLoaded, cartInfo);
    });
  }

  /**
   * Listens for remove product from cart event
   *
   * @private
   */
  _onRemoveProductFromCart() {
    EventEmitter.on(eventMap.productRemovedFromCart, (cartInfo) => {
      EventEmitter.emit(eventMap.cartLoaded, cartInfo);
    });
  }

  /**
   * Listens for product price change in cart event
   *
   * @private
   */
  _onProductPriceChange() {
    // on success
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
  _onProductQtyChange() {
    // on success
    EventEmitter.on(eventMap.productQtyChanged, (cartInfo) => {
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
  _initProductSelect(event) {
    const productId = Number($(event.currentTarget).find(':selected').val());
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
    const combinationId = Number($(event.currentTarget).find(':selected').val());
    this._selectCombination(combinationId);
  }

  /**
   * Searches for product
   *
   * @private
   */
  _search(searchPhrase) {
    if (searchPhrase.length < 3) {
      return;
    }

    if (this.activeSearchRequest !== null) {
      this.activeSearchRequest.abort();
    }

    const $searchRequest = $.get(this.router.generate('admin_products_search'), {
      search_phrase: searchPhrase,
    });
    this.activeSearchRequest = $searchRequest;

    $searchRequest.then((response) => {
      EventEmitter.emit(eventMap.productSearched, response);
    }).catch((response) => {
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

    if (this.products.length !== 0) {
      this._selectProduct(this.products[0].product_id);
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
      if (this.products[key].product_id === productId) {
        this.selectedProduct = this.products[key];
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
    this.productRenderer.renderStock(combination.stock);

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
   * @returns {FormData}
   *
   * @private
   */
  _getProductData() {
    return new FormData(document.querySelector(createOrderMap.productAddForm));
  }
}
