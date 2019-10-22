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
    this.products = [];
    this.selectedProduct = {};
    this.combinations = {};
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
    $(createOrderPageMap.productSearch).on('input', event => this._handleProductSearch(event));
    $(createOrderPageMap.productSelect).on('change', event => this._handleProductSelect(event));
    $(createOrderPageMap.combinationsSelect).on('change', event => this._handleCombinationSelect(event));
  }

  /**
   * Searches for product
   *
   * @private
   */
  _handleProductSearch(event) {
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
      this.combinations = this.products[0].combinations;
    }).catch((response) => {
      if (typeof response.responseJSON !== 'undefined') {
        showErrorMessage(response.responseJSON.message);
      }
    });
  }

  /**
   * Handles use case when product is selected from search results
   *
   * @param event
   *
   * @private
   */
  _handleProductSelect(event) {
    const index = $(event.currentTarget).find(':selected').data('index');
    this.renderer.renderProductMetadata(this.products[index]);
    this.combinations = this.products[index].combinations;
  }

  /**
   * Handles use case when new combination is selected
   *
   * @param event
   *
   * @private
   */
  _handleCombinationSelect(event) {
    const combinationIndex = $(event.currentTarget).find(':selected').data('index');
    const combination = this.combinations[combinationIndex];
    debugger;
    this.renderer.renderStock(combination.stock);
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
    formData.append('product_id', $(createOrderPageMap.productSelect).find(':selected').val());
    formData.append('quantity', $(createOrderPageMap.quantityInput).val());

    if ($(createOrderPageMap.combinationsSelect).length !== 0) {
      const combinationId = $(createOrderPageMap.combinationsSelect).find(':selected').val();
      formData.append('combination_id', combinationId);
    }

    this._resolveCustomizationValuesForAddProduct(formData);

    return formData;
  }

  /**
   * Resolves product customization fields to be added to formData object
   *
   * @param {FormData} formData
   * @returns {FormData}
   * @private
   */
  _resolveCustomizationValuesForAddProduct(formData) {
    const customizationKey = 'customization';
    const customizedFields = $(createOrderPageMap.customizedFieldInput);

    $.each(customizedFields, (index, field) => {
      const customizationFieldId = $(field).data('customization-field-id');
      const formKey = `${customizationKey}[${customizationFieldId}]`;
      if ($(field).attr('type') === 'file') {
        formData.append(formKey, $(field)[0].files[0]);

        return;
      }

      formData.append(formKey, $(field).val());
    });

    return formData;
  }
}
