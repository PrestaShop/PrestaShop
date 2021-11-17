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

const {$} = window;

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
    this.cleanProductsList();

    if (products.length === 0) {
      this.hideProductsList();

      return;
    }

    Object.values(products).forEach((product) => {
      const $template = this.cloneProductTemplate(product);

      let customizationId = 0;

      if (product.customization) {
        ({customizationId} = product.customization);
        this.renderListedProductCustomization(product.customization, $template);
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
        this.renderStock(
          $template.find(createOrderMap.listedProductQtyStock),
          $template.find(createOrderMap.listedProductQtyInput),
          product.availableStock,
          product.availableOutOfStock || (product.availableStock <= 0),
        );
        $template.find(createOrderMap.productTotalPriceField).text(product.price);
        $template.find(createOrderMap.productRemoveBtn).data('product-id', product.productId);
        $template.find(createOrderMap.productRemoveBtn).data('attribute-id', product.attributeId);
        $template.find(createOrderMap.productRemoveBtn).data('customization-id', customizationId);
      } else {
        $template.find(createOrderMap.listedProductGiftQty).text(product.quantity);
      }

      this.$productsTable.find('tbody').append($template);
    });

    this.showTaxWarning();
    this.showProductsList();
  }

  /**
   * Renders customization data for listed product
   *
   * @param customization
   * @param $productRowTemplate
   *
   * @private
   */
  renderListedProductCustomization(customization, $productRowTemplate) {
    const $customizedTextTemplate = $($(createOrderMap.listedProductCustomizedTextTemplate).html());
    const $customizedFileTemplate = $($(createOrderMap.listedProductCustomizedFileTemplate).html());

    Object.values(customization.customizationFieldsData).forEach((customizedData) => {
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
    });
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
    this.cleanSearchResults();
    this.toggleSearchingNotice(false);
    if (foundProducts.length === 0) {
      this.showNotFound();
      this.hideTaxWarning();

      return;
    }

    this.renderFoundProducts(foundProducts);

    this.hideNotFound();
    this.showTaxWarning();
    this.showResultBlock();
  }

  reset() {
    this.cleanSearchResults();
    this.hideTaxWarning();
    this.hideResultBlock();
    this.toggleSearchingNotice(false);
  }

  /**
   * Renders available fields related to selected product
   *
   * @param {object} product
   */
  renderProductMetadata(product) {
    this.renderStock(
      $(createOrderMap.inStockCounter),
      $(createOrderMap.quantityInput),
      product.stock,
      product.availableOutOfStock || (product.stock <= 0),
    );
    this.renderCombinations(product.combinations);
    this.renderCustomizations(product.customizationFields);
  }

  /**
   * Updates stock text helper value
   *
   * @param {object} inputStockCounter Text Help with the stock counter
   * @param {object} inputQuantity Input for the stock
   * @param {number} stock Available stock for the product
   * @param {boolean} infiniteMax If the product order has no limits
   */
  renderStock(inputStockCounter, inputQuantity, stock, infiniteMax) {
    inputStockCounter.text(stock);

    if (!infiniteMax) {
      inputQuantity.attr('max', stock);
    } else {
      inputQuantity.removeAttr('max');
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
  renderFoundProducts(foundProducts) {
    Object.values(foundProducts).forEach((product) => {
      let {name} = product;

      if (product.combinations.length === 0) {
        name += ` - ${product.formattedPrice}`;
      }

      $(createOrderMap.productSelect).append(`<option value="${product.productId}">${name}</option>`);
    });
  }

  /**
   * Cleans product search result fields
   *
   * @private
   */
  cleanSearchResults() {
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
  renderCombinations(combinations) {
    this.cleanCombinations();

    if (combinations.length === 0) {
      this.hideCombinations();

      return;
    }

    Object.values(combinations).forEach((combination) => {
      $(createOrderMap.combinationsSelect).append(
        `<option
          value="${combination.attributeCombinationId}">
          ${combination.attribute} - ${combination.formattedPrice}
        </option>`,
      );
    });

    this.showCombinations();
  }

  /**
   * Resolves weather to add customization fields to result block and adds them if needed
   *
   * @param customizationFields
   *
   * @private
   */
  renderCustomizations(customizationFields) {
    // represents customization field type "file".
    const fieldTypeFile = createOrderMap.productCustomizationFieldTypeFile;
    // represents customization field type "text".
    const fieldTypeText = createOrderMap.productCustomizationFieldTypeText;

    this.cleanCustomizations();
    if (customizationFields.length === 0) {
      this.hideCustomizations();

      return;
    }

    const $customFieldsContainer = $(createOrderMap.productCustomFieldsContainer);
    const $fileInputTemplate = $($(createOrderMap.productCustomFileTemplate).html());
    const $textInputTemplate = $($(createOrderMap.productCustomTextTemplate).html());

    const templateTypeMap = {
      [fieldTypeFile]: $fileInputTemplate,
      [fieldTypeText]: $textInputTemplate,
    };

    Object.values(customizationFields).forEach((customField) => {
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
    });

    this.showCustomizations();
  }

  /**
   * Renders error alert for cart block
   *
   * @param message
   */
  renderCartBlockErrorAlert(message) {
    $(createOrderMap.cartErrorAlertText).text(message);
    this.showCartBlockError();
  }

  /**
   * Cleans cart block alerts content and hides them
   */
  cleanCartBlockAlerts() {
    $(createOrderMap.cartErrorAlertText).text('');
    this.hideCartBlockError();
  }

  /**
   * Shows error alert block of cart block
   *
   * @private
   */
  showCartBlockError() {
    $(createOrderMap.cartErrorAlertBlock).removeClass('d-none');
  }

  /**
   * Hides error alert block of cart block
   *
   * @private
   */
  hideCartBlockError() {
    $(createOrderMap.cartErrorAlertBlock).addClass('d-none');
  }

  /**
   * Shows product customization container
   *
   * @private
   */
  showCustomizations() {
    $(createOrderMap.productCustomizationContainer).removeClass('d-none');
  }

  /**
   * Hides product customization container
   *
   * @private
   */
  hideCustomizations() {
    $(createOrderMap.productCustomizationContainer).addClass('d-none');
  }

  /**
   * Empties customization fields container
   *
   * @private
   */
  cleanCustomizations() {
    $(createOrderMap.productCustomFieldsContainer).empty();
  }

  /**
   * Shows result block
   *
   * @private
   */
  showResultBlock() {
    $(createOrderMap.productResultBlock).removeClass('d-none');
  }

  /**
   * Hides result block
   *
   * @private
   */
  hideResultBlock() {
    $(createOrderMap.productResultBlock).addClass('d-none');
  }

  /**
   * Shows products list
   *
   * @private
   */
  showProductsList() {
    this.$productsTable.removeClass('d-none');
  }

  /**
   * Hides products list
   *
   * @private
   */
  hideProductsList() {
    this.$productsTable.addClass('d-none');
  }

  /**
   * Empties products list
   *
   * @private
   */
  cleanProductsList() {
    this.$productsTable.find('tbody').empty();
  }

  /**
   * Empties combinations select
   *
   * @private
   */
  cleanCombinations() {
    $(createOrderMap.combinationsSelect).empty();
  }

  /**
   * Shows combinations row
   *
   * @private
   */
  showCombinations() {
    $(createOrderMap.combinationsRow).removeClass('d-none');
  }

  /**
   * Hides combinations row
   *
   * @private
   */
  hideCombinations() {
    $(createOrderMap.combinationsRow).addClass('d-none');
  }

  /**
   * Shows warning of tax included/excluded
   *
   * @private
   */
  showTaxWarning() {
    $(createOrderMap.productTaxWarning).removeClass('d-none');
  }

  /**
   * Hides warning of tax included/excluded
   *
   * @private
   */
  hideTaxWarning() {
    $(createOrderMap.productTaxWarning).addClass('d-none');
  }

  /**
   * Shows product not found warning
   *
   * @private
   */
  showNotFound() {
    $(createOrderMap.noProductsFoundWarning).removeClass('d-none');
  }

  /**
   * Hides product not found warning
   *
   * @private
   */
  hideNotFound() {
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
