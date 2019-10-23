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
import ProductRenderer from './product-renderer';
import Router from '../../../components/router';

const $ = window.$;

/**
 * Product component Object for "Create order" page
 */
export default class ProductManager {
  constructor() {
    this.products = {};
    this.selectedProductId = null;
    this.selectedCombinationId = null;

    this.renderer = new ProductRenderer();
    this.router = new Router();

    this._initEvents();

    return {
      onAddProductToCart: (cartId) => {
        this._addProductToCart(cartId);
      },
    };
  }

  /**
   * Initialize page's events.
   *
   * @private
   */
  _initEvents() {
    $(createOrderPageMap.productSearch).on('input', event => this._search(event));
    $(createOrderPageMap.productSelect).on('change', (event) => {
      const productId = Number($(event.currentTarget).find(':selected').val());
      this._selectProduct(productId);
    });
    $(createOrderPageMap.combinationsSelect).on('change', (event) => {
      const combinationId = Number($(event.currentTarget).find(':selected').val());
      this.selectCombination(combinationId);
    });
  }

  /**
   * Searches for product
   *
   * @private
   */
  _search(event) {
    const minSearchPhraseLength = 3;
    const $productSearchInput = $(event.currentTarget);
    const name = $productSearchInput.val();

    if (name.length < minSearchPhraseLength) {
      return;
    }

    $.get(this.router.generate('admin_products_search'), {
      search_phrase: name,
    }).then((response) => {
      this.products = JSON.parse(response);
      this.renderer.renderSearchResults(this.products);
      this._selectFirstResult();
    }).catch((response) => {
      if (typeof response.responseJSON !== 'undefined') {
        showErrorMessage(response.responseJSON.message);
      }
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
      this._selectProduct(Object.keys(this.products)[0]);
    }
  }

  /**
   * Handles use case when product is selected from search results
   *
   * @private
   *
   * @param productId
   */
  _selectProduct(productId) {
    this._unsetCombination();

    this.selectedProductId = productId;
    const product = this.products[productId];

    this.renderer.renderProductMetadata(product);

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
   */
  selectCombination(combinationId) {
    const combination = this.products[this.selectedProductId].combinations[combinationId];

    this.selectedCombinationId = combinationId;
    this.renderer.renderStock(combination.stock);

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
   * Sets the selected product id to null
   *
   * @private
   */
  _unsetProduct() {
    this.selectedProductId = null;
  }

  /**
   * Adds selected product to current cart
   *
   * @private
   */
  _addProductToCart(cartId) {
    $.ajax($(createOrderPageMap.addToCartButton).data('add-product-url'), {
      method: 'POST',
      data: this._getProductData(cartId),
      processData: false,
      contentType: false,
      cache: false,
    }).then((response) => {
      this.renderer.renderList(response.products);
    }).catch((response) => {
      if (typeof response.responseJSON !== 'undefined') {
        showErrorMessage(response.responseJSON.message);
      }
    });
  }

  /**
   * Retrieves product data from product search result block fields
   *
   * @returns {FormData}
   * @private
   */
  _getProductData(cartId) {
    const formData = new FormData();

    formData.append('cart_id', cartId);
    formData.append('product_id', this.selectedProductId);
    formData.append('quantity', $(createOrderPageMap.quantityInput).val());
    formData.append('combination_id', this.selectedCombinationId);

    this._getCustomFieldsData(formData);

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
  _getCustomFieldsData(formData) {
    const $customFields = $(createOrderPageMap.productCustomInput);

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
