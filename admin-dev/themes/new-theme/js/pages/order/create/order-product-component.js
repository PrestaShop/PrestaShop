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

const $ = window.$;

/**
 * Product component Object for "Create order" page
 */
export default class OrderProductComponent {
  constructor() {
    this.products = [];
    this.combinations = [];

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
    $(createOrderPageMap.productSearch).on('input', (event) => this._handleProductSearch(event));
    $(createOrderPageMap.productSelect).on('change', (event) => this._handleProductChange(event));
    $(createOrderPageMap.productResultBlock).on(
        'change',
        createOrderPageMap.combinationsSelect,
        (event) => this._handleCombinationChange(event)
    );
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

    $.ajax($productSearchInput.data('product-search-url'), {
      method: 'GET',
      data: {
        product_search_phrase: name,
      },
    }).then((response) => {
      this.products = JSON.parse(response);
      if (this.products.length === 0) {
        this._showNotFoundProducts();

        return;
      }

      this._renderProductSearchResult();
    }).catch((response) => {
      if (typeof response.responseJSON !== 'undefined') {
        showErrorMessage(response.responseJSON.message);
      }
    });
  }

  /**
   * Renders product result block
   *
   * @private
   */
  _renderProductSearchResult() {
    $(createOrderPageMap.productSelect).empty();

    for (const [index, product] of this.products.entries()) {
      if (index === 0) {
        this._fillFieldsRelatedToProduct(product);
      }

      let name = product.name;
      const combinationsCollection = product.combinations;

      if (combinationsCollection === null) {
        name += ' - ' + product.formatted_price;
      }

      const shouldUpdateStockValue = combinationsCollection === null && index === 0;

      if (shouldUpdateStockValue) {
        this._updateStock(product.stock);
      }

      $(createOrderPageMap.productSelect).append(
        $('<option></option>').attr('value', product.product_id).text(name).attr('data-index', index)
      );
    }

    const $noRecordsRow = $(createOrderPageMap.noRecordsFound).find(
      createOrderPageMap.noRecordsFoundRow
    );

    if ($noRecordsRow.length !== 0) {
      $noRecordsRow.remove();
    }

    this._showResultBlock();
  }

  /**
   * Updates stock text helper value
   *
   * @param stock
   * @private
   */
  _updateStock(stock) {
    $(createOrderPageMap.inStockCounter).text(stock);
    $(createOrderPageMap.quantityInput).attr('max', stock);
  }

  /**
   * Adds available fields related to selected product
   *
   * @param product
   * @private
   */
  _fillFieldsRelatedToProduct(product) {
    this._fillCombinations(product.combinations);
    this._resolveCustomizationFields(product.customization_fields);
  }

  /**
   * Handles product select change
   *
   * @param event
   * @private
   */
  _handleProductChange(event) {
    const index = $(event.currentTarget).find(':selected').data('index');
    const product = this.products[index];

    this._fillFieldsRelatedToProduct(product);

    if (product.combinations === null) {
      this.combinations = [];
      this._updateStock(product.stock);
    }

    this._fillCombinations(product.combinations);
  }

  /**
   * Handles combination select change
   *
   * @param event
   * @private
   */
  _handleCombinationChange(event) {
    const index = $(event.currentTarget).find(':selected').val();
    const combination = this.combinations[index];

    this._updateStock(combination.stock);
  }

  /**
   * Fills combination select with options
   *
   * @param combinations
   * @private
   */
  _fillCombinations(combinations) {
    if (combinations === null) {
      this._removeCombinationSelect();

      return;
    }

    this.combinations = combinations.combinations;

    if ($(createOrderPageMap.combinationsRow).length === 0) {
      const $combinationsTemplate = $($(createOrderPageMap.combinationsTemplate).html());
      $(createOrderPageMap.productSelectRow).after($combinationsTemplate);
    }

    const $combinationsSelect = $(createOrderPageMap.combinationsSelect);
    $combinationsSelect.empty();

    const entries = Object.entries(combinations.combinations);

    let i = 0; // This is needed because index in this case is attribute combination id

    for (const [id, combination] of entries) {
      if (i === 0) {
        this._updateStock(combination.stock);
      }

      const name = combination.attribute + ' - ' + combination.formatted_price;
      $combinationsSelect.append($('<option></option>')
        .attr('value', id).text(name));

      i += 1;
    }
  }

  /**
   * Resolves weather to add customization fields to result block and adds them if needed
   *
   * @param customizationFields
   * @private
   */
  _resolveCustomizationFields(customizationFields) {
    this._removeCustomizedFields();
    if (customizationFields === null) {
      return;
    }

    const $customizedFieldTemplate = $(createOrderPageMap.cartProductCustomizedFieldTemplate);
    let $customizedFieldTemplateContent = $($customizedFieldTemplate.html());

    const customizationLabel = $(createOrderPageMap.productSelect).data('customization-label');

    $customizedFieldTemplateContent.find(createOrderPageMap.customizedLabelClass)
      .text(customizationLabel);

    $(createOrderPageMap.quantityRow).before($customizedFieldTemplateContent);

    $.each(customizationFields.product_customization_fields, function(index, field) {
      const $fieldTemplate = $($(createOrderPageMap.customizedFieldTypes[field.type]).html());

      $customizedFieldTemplateContent = $($customizedFieldTemplate.html());
      if (field.required) {
        $customizedFieldTemplateContent.find(createOrderPageMap.customizedLabelClass)
          .append('<span class="text-danger">*</span>');
      }

      $customizedFieldTemplateContent.find(createOrderPageMap.customizedLabelClass)
        .append(field.name);

      const $fieldWrapper = $customizedFieldTemplateContent
        .find(createOrderPageMap.customizedFieldInputWrapper);

      $fieldWrapper.append($fieldTemplate);
      $fieldWrapper.find(createOrderPageMap.customizedFieldInput)
        .attr('data-customization-field-id', field.customization_field_id);

      $(createOrderPageMap.quantityRow).before($customizedFieldTemplateContent);
    });
  }

  /**
   * Removes combination select for products with no combinations
   *
   * @private
   */
  _removeCombinationSelect() {
    $(createOrderPageMap.combinationsRow).remove();
  }

  /**
   * Removes customized fields select for products with no customized fields
   *
   * @private
   */
  _removeCustomizedFields() {
    $(createOrderPageMap.customizedFieldContainer).remove();
  }

  /**
   * Shows empty result when product is not found
   *
   * @private
   */
  _showNotFoundProducts() {
    const $emptyResultTemplate = $($(createOrderPageMap.productSearchEmptyResultTemplate).html());

    this._hideResultBlock();

    const $noRecordsElement = $(createOrderPageMap.noRecordsFound);

    if ($noRecordsElement.find(createOrderPageMap.noRecordsFoundRow).length === 0) {
      $noRecordsElement.append($emptyResultTemplate);
    }
  }

  /**
   * Shows result block
   *
   * @private
   */
  _showResultBlock() {
    $(createOrderPageMap.productResultBlock).show();
    $(createOrderPageMap.productResultBlock).removeClass('d-none');
  }

  /**
   * Hides result block
   *
   * @private
   */
  _hideResultBlock() {
    $(createOrderPageMap.productResultBlock).hide();
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
      //TODO add product to list
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
