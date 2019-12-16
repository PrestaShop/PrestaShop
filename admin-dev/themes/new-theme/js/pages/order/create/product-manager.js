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
    this.products = {};
    this.selectedProductId = null;
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
  }

  /**
   * Listens for product search event
   *
   * @private
   */
  onProductSearch() {
    EventEmitter.on(eventMap.productSearched, (response) => {
      this.products = JSON.parse(response);
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
    EventEmitter.on(eventMap.productAddedToCart, (cartInfo) => {
      EventEmitter.emit(eventMap.cartLoaded, cartInfo);
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

    $.get(this.router.generate('admin_products_search'), {
      search_phrase: searchPhrase,
    }).then((response) => {
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
      this.selectProduct(Object.keys(this.products)[0]);
    }
  }

  /**
   * Handles use case when product is selected from search results
   *
   * @private
   *
   * @param productId
   */
  selectProduct(productId) {
    this.unsetCombination();

    this.selectedProductId = productId;
    const product = this.products[productId];

    this.productRenderer.renderProductMetadata(product);

    // if product has combinations select the first else leave it null
    if (product.combinations.length !== 0) {
      this.selectCombination(Object.keys(product.combinations)[0]);
    }

    return product;
  }

  /**
   * Handles use case when new combination is selected
   *
   * @param combinationId
   *
   * @private
   */
  selectCombination(combinationId) {
    const combination = this.products[this.selectedProductId].combinations[combinationId];

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
   * Sets the selected product id to null
   *
   * @private
   */
  unsetProduct() {
    this.selectedProductId = null;
  }

  /**
   * Retrieves product data from product search result block fields
   *
   * @returns {FormData}
   * @private
   */
  getProductData() {
    const formData = new FormData();

    formData.append('productId', this.selectedProductId);
    formData.append('quantity', $(createOrderMap.quantityInput).val());
    formData.append('combinationId', this.selectedCombinationId);

    this.getCustomFieldsData(formData);

    return formData;
  }

  /**
   * Resolves product customization fields to be added to formData object
   *
   * @param {FormData} formData
   *
   * @returns {FormData}
   *
   * @private
   */
  getCustomFieldsData(formData) {
    const $customFields = $(createOrderMap.productCustomInput);

    $customFields.each((key, field) => {
      const $field = $(field);
      const name = $field.attr('name');

      if ($field.attr('type') === 'file') {
        formData.append(name, $field[0].files[0]);
      } else {
        formData.append(name, $field.val());
      }
    });

    return formData;
  }
}
