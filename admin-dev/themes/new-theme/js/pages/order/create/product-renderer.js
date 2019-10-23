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

import createOrderMap from './create-order-map';

const $ = window.$;

export default class ProductRenderer {
  constructor() {
    this.$productsTable = $(createOrderMap.productsTable);
  }

  /**
   * Renders cart products list
   *
   * @param products
   */
  renderList(products) {
    this._cleanProductsList();

    if (products.length === 0) {
      this._hideProductsList();

      return;
    }

    const $productsTableRowTemplate = $($(createOrderMap.productsTableRowTemplate).html());

    for (const key in products) {
      const product = products[key];
      const $template = $productsTableRowTemplate.clone();

      $template.find(createOrderMap.productImageField).text(product.imageLink);
      $template.find(createOrderMap.productNameField).text(product.name);
      $template.find(createOrderMap.productAttrField).text(product.attribute);
      $template.find(createOrderMap.productReferenceField).text(product.reference);
      $template.find(createOrderMap.productUnitPriceInput).text(product.unitPrice);
      $template.find(createOrderMap.productTotalPriceField).text(product.price);
      $template.find(createOrderMap.productRemoveBtn).data('product-id', product.productId);

      this.$productsTable.find('tbody').append($template);
    }

    this._showTaxWarning();
    this._showProductsList();
  }

  /**
   * Renders cart products search results block
   *
   * @param foundProducts
   */
  renderSearchResults(foundProducts) {
    this._cleanSearchResults();
    if (foundProducts.length === 0) {
      this._showNotFound();
      this._hideTaxWarning();

      return null;
    }

    this._renderFoundProducts(foundProducts);

    this._hideNotFound();
    this._showTaxWarning();
    this._showResultBlock();
  }

  /**
   * Renders available fields related to selected product
   *
   * @param product
   */
  renderProductMetadata(product) {
    this.renderStock(product.stock);
    this._renderCombinations(product.combinations);
    this._renderCustomizations(product.customization_fields);
  }

  /**
   * Updates stock text helper value
   *
   * @param stock
   */
  renderStock(stock) {
    $(createOrderMap.inStockCounter).text(stock);
    $(createOrderMap.quantityInput).attr('max', stock);
  }

  /**
   * Renders found products select
   *
   * @param foundProducts
   *
   * @private
   */
  _renderFoundProducts(foundProducts) {
    for (const key in foundProducts) {
      const product = foundProducts[key];

      let name = product.name;
      if (product.combinations.length === 0) {
        name += ` - ${product.formatted_price}`;
      }

      $(createOrderMap.productSelect).append(`<option value="${product.product_id}">${name}</option>`);
    }
  }

  /**
   * Cleans product search result fields
   *
   * @private
   */
  _cleanSearchResults() {
    $(createOrderMap.productSelect).empty();
    $(createOrderMap.combinationsSelect).empty();
    $(createOrderMap.quantityInput).empty();
  }

  /**
   * Renders combinations row with select options
   *
   * @param {Array} combinations
   *
   * @private
   */
  _renderCombinations(combinations) {
    this._cleanCombinations();

    if (combinations.length === 0) {
      this._hideCombinations();

      return;
    }

    for (const key in combinations) {
      const combination = combinations[key];

      $(createOrderMap.combinationsSelect).append(
        `<option
          value="${combination.attribute_combination_id}">
          ${combination.attribute} - ${combination.formatted_price}
        </option>`,
      );
    }

    this._showCombinations();
  }

  /**
   * Resolves weather to add customization fields to result block and adds them if needed
   *
   * @param customizationFields
   *
   * @private
   */
  _renderCustomizations(customizationFields) {
    this._cleanCustomizations();
    if (customizationFields.length === 0) {
      this._hideCustomizations();

      return;
    }

    const $customFieldsContainer = $(createOrderMap.productCustomFieldsContainer);

    const $fileInputTemplate = $($(createOrderMap.productCustomFileTemplate).html());
    const $textInputTemplate = $($(createOrderMap.productCustomTextTemplate).html());

    const templateTypeMap = {
      0: $fileInputTemplate,
      1: $textInputTemplate,
    };

    for (const key in customizationFields) {
      const customField = customizationFields[key];
      const $template = templateTypeMap[customField.type].clone();

      $template.find(createOrderMap.productCustomInput)
        .attr('name', `customization[${customField.customization_field_id}]`);
      $template.find(createOrderMap.productCustomInputLabel)
        .attr('for', `customization[${customField.customization_field_id}]`)
        .text(customField.name);

      $customFieldsContainer.append($template);
    }

    this._showCustomizations();
  }

  /**
   * Shows product customization container
   *
   * @private
   */
  _showCustomizations() {
    $(createOrderMap.productCustomizationContainer).removeClass('d-none');
  }

  /**
   * Hides product customization container
   *
   * @private
   */
  _hideCustomizations() {
    $(createOrderMap.productCustomizationContainer).addClass('d-none');
  }

  /**
   * Empties customization fields container
   *
   * @private
   */
  _cleanCustomizations() {
    $(createOrderMap.productCustomFieldsContainer).empty();
  }

  /**
   * Shows result block
   *
   * @private
   */
  _showResultBlock() {
    $(createOrderMap.productResultBlock).removeClass('d-none');
  }

  /**
   * Hides result block
   *
   * @private
   */
  _hideResultBlock() {
    $(createOrderMap.productResultBlock).addClass('d-none');
  }


  /**
   * Shows products list
   *
   * @private
   */
  _showProductsList() {
    this.$productsTable.removeClass('d-none');
  }

  /**
   * Hides products list
   *
   * @private
   */
  _hideProductsList() {
    this.$productsTable.addClass('d-none');
  }

  /**
   * Empties products list
   *
   * @private
   */
  _cleanProductsList() {
    this.$productsTable.find('tbody').empty();
  }

  /**
   * Empties combinations select
   *
   * @private
   */
  _cleanCombinations() {
    $(createOrderMap.combinationsSelect).empty();
  }

  /**
   * Shows combinations row
   *
   * @private
   */
  _showCombinations() {
    $(createOrderMap.combinationsRow).removeClass('d-none');
  }

  /**
   * Hides combinations row
   *
   * @private
   */
  _hideCombinations() {
    $(createOrderMap.combinationsRow).addClass('d-none');
  }

  /**
   * Shows warning of tax included/excluded
   *
   * @private
   */
  _showTaxWarning() {
    $(createOrderMap.productTaxWarning).removeClass('d-none');
  }

  /**
   * Hides warning of tax included/excluded
   *
   * @private
   */
  _hideTaxWarning() {
    $(createOrderMap.productTaxWarning).addClass('d-none');
  }

  /**
   * Shows product not found warning
   *
   * @private
   */
  _showNotFound() {
    $(createOrderMap.noProductsFoundWarning).removeClass('d-none');
  }

  /**
   * Hides product not found warning
   *
   * @private
   */
  _hideNotFound() {
    $(createOrderMap.noProductsFoundWarning).addClass('d-none');
  }
}
