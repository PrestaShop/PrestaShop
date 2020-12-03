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

    for (const key in products) {
      const product = products[key];

      const $template = this.cloneProductTemplate(product);

      let customizationId = 0;

      if (product.customization) {
        customizationId = product.customization.customizationId;
        this._renderListedProductCustomization(product.customization, $template);
      }

      $template.find(createOrderMap.listedProductImageField).prop('src', product.imageLink);
      $template.find(createOrderMap.listedProductNameField).text(product.name);
      $template.find(createOrderMap.listedProductAttrField).text(product.attribute);
      $template.find(createOrderMap.listedProductReferenceField).text(product.reference);

      if (product.gift !== true) {
        $template.find(createOrderMap.listedProductUnitPriceInput).val(product.unitPrice);
        $template.find(createOrderMap.listedProductUnitPriceInput).data('product-id', product.productId);
        $template.find(createOrderMap.listedProductUnitPriceInput).data('attribute-id', product.attributeId);
        $template.find(createOrderMap.listedProductUnitPriceInput).data('customization-id', customizationId);
        $template.find(createOrderMap.listedProductQtyInput).val(product.quantity);
        $template.find(createOrderMap.listedProductQtyInput).data('product-id', product.productId);
        $template.find(createOrderMap.listedProductQtyInput).data('attribute-id', product.attributeId);
        $template.find(createOrderMap.listedProductQtyInput).data('customization-id', customizationId);
        $template.find(createOrderMap.listedProductQtyInput).data('prev-qty', product.quantity);
        $template.find(createOrderMap.productTotalPriceField).text(product.price);
        $template.find(createOrderMap.productRemoveBtn).data('product-id', product.productId);
        $template.find(createOrderMap.productRemoveBtn).data('attribute-id', product.attributeId);
        $template.find(createOrderMap.productRemoveBtn).data('customization-id', customizationId);
      } else {
        $template.find(createOrderMap.listedProductGiftQty).text(product.quantity);
      }

      this.$productsTable.find('tbody').append($template);
    }

    this._showTaxWarning();
    this._showProductsList();
  }

  /**
   * Renders customization data for listed product
   *
   * @param customization
   * @param $productRowTemplate
   *
   * @private
   */
  _renderListedProductCustomization(customization, $productRowTemplate) {
    const $customizedTextTemplate = $($(createOrderMap.listedProductCustomizedTextTemplate).html());
    const $customizedFileTemplate = $($(createOrderMap.listedProductCustomizedFileTemplate).html());

    for (const key in customization.customizationFieldsData) {
      const customizedData = customization.customizationFieldsData[key];

      let $customizationTemplate = $customizedTextTemplate.clone();

      if (customizedData.type === createOrderMap.productCustomizationFieldTypeFile) {
        $customizationTemplate = $customizedFileTemplate.clone();
        $customizationTemplate.find(createOrderMap.listedProductCustomizationName).text(customizedData.name);
        $customizationTemplate
          .find(`${createOrderMap.listedProductCustomizationValue} img`)
          .prop('src', customizedData.value);
      } else {
        $customizationTemplate.find(createOrderMap.listedProductCustomizationName).text(customizedData.name);
        $customizationTemplate.find(createOrderMap.listedProductCustomizationValue).text(customizedData.value);
      }

      $productRowTemplate.find(createOrderMap.listedProductDefinition).append($customizationTemplate);
    }
  }

  renderSearching() {
    this.reset();
    this.toggleSearchingNotice(true);
  }

  /**
   * Renders cart products search results block
   *
   * @param foundProducts
   */
  renderSearchResults(foundProducts) {
    this._cleanSearchResults();
    this.toggleSearchingNotice(false);
    if (foundProducts.length === 0) {
      this._showNotFound();
      this._hideTaxWarning();

      return;
    }

    this._renderFoundProducts(foundProducts);

    this._hideNotFound();
    this._showTaxWarning();
    this._showResultBlock();
  }

  reset() {
    this._cleanSearchResults();
    this._hideTaxWarning();
    this._hideResultBlock();
    this.toggleSearchingNotice(false);
  }

  /**
   * Renders available fields related to selected product
   *
   * @param {object} product
   */
  renderProductMetadata(product) {
    this.renderStock(product.stock, product.availableOutOfStock || (product.stock <= 0));
    this._renderCombinations(product.combinations);
    this._renderCustomizations(product.customizationFields);
  }

  /**
   * Updates stock text helper value
   *
   * @param {number} stock
   * @param {boolean} infinitMax
   */
  renderStock(stock, infinitMax) {
    $(createOrderMap.inStockCounter).text(stock);

    if (!infinitMax) {
      $(createOrderMap.quantityInput).attr('max', stock);
    } else {
      $(createOrderMap.quantityInput).removeAttr('max');
    }
  }

  /**
   * @param product
   *
   * @private
   */
  cloneProductTemplate(product) {
    return product.gift === true
      ? $($(createOrderMap.productsTableGiftRowTemplate).html()).clone()
      : $($(createOrderMap.productsTableRowTemplate).html()).clone();
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
        name += ` - ${product.formattedPrice}`;
      }

      $(createOrderMap.productSelect).append(`<option value="${product.productId}">${name}</option>`);
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
          value="${combination.attributeCombinationId}">
          ${combination.attribute} - ${combination.formattedPrice}
        </option>`
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
    // represents customization field type "file".
    const fieldTypeFile = createOrderMap.productCustomizationFieldTypeFile;
    // represents customization field type "text".
    const fieldTypeText = createOrderMap.productCustomizationFieldTypeText;

    this._cleanCustomizations();
    if (customizationFields.length === 0) {
      this._hideCustomizations();

      return;
    }

    const $customFieldsContainer = $(createOrderMap.productCustomFieldsContainer);
    const $fileInputTemplate = $($(createOrderMap.productCustomFileTemplate).html());
    const $textInputTemplate = $($(createOrderMap.productCustomTextTemplate).html());

    const templateTypeMap = {
      [fieldTypeFile]: $fileInputTemplate,
      [fieldTypeText]: $textInputTemplate,
    };

    for (const key in customizationFields) {
      const customField = customizationFields[key];
      const $template = templateTypeMap[customField.type].clone();

      if (customField.type === fieldTypeFile) {
        $template.on('change', (e) => {
          const fileName = e.target.files[0].name;

          $(e.target).next('.custom-file-label').html(fileName);
        });
      }

      $template
        .find(createOrderMap.productCustomInput)
        .attr('name', `customizations[${customField.customizationFieldId}]`)
        .data('customization-field-id', customField.customizationFieldId);
      $template
        .find(createOrderMap.productCustomInputLabel)
        .attr('for', `customizations[${customField.customizationFieldId}]`)
        .text(customField.name);

      if (customField.required === true) {
        $template.find(createOrderMap.requiredFieldMark).removeClass('d-none');
      }

      $customFieldsContainer.append($template);
    }

    this._showCustomizations();
  }

  /**
   * Renders error alert for cart block
   *
   * @param message
   */
  renderCartBlockErrorAlert(message) {
    $(createOrderMap.cartErrorAlertText).text(message);
    this._showCartBlockError();
  }

  /**
   * Cleans cart block alerts content and hides them
   */
  cleanCartBlockAlerts() {
    $(createOrderMap.cartErrorAlertText).text('');
    this._hideCartBlockError();
  }

  /**
   * Shows error alert block of cart block
   *
   * @private
   */
  _showCartBlockError() {
    $(createOrderMap.cartErrorAlertBlock).removeClass('d-none');
  }

  /**
   * Hides error alert block of cart block
   *
   * @private
   */
  _hideCartBlockError() {
    $(createOrderMap.cartErrorAlertBlock).addClass('d-none');
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

  /**
   * Toggles searching product notice
   *
   * @private
   */
  toggleSearchingNotice(visible) {
    $(createOrderMap.searchingProductsNotice).toggleClass('d-none', !visible);
  }
}
