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
  render(products) {
    if (products.length === 0) {
      this._hideProductsList();

      return;
    }
    this._renderList(products);
  }

  _renderList(products) {
    this._cleanProductsList();
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

    this._showProductsList();
  }

  renderSearchResults(foundProducts) {
    $(createOrderMap.productSelect).empty();

    for (const [index, product] of foundProducts.entries()) {
      if (index === 0) {
        this._fillFieldsRelatedToProduct(product);
      }

      let name = product.name;
      const combinationsCollection = product.combinations;

      if (combinationsCollection === null) {
        name += ` - ${  product.formatted_price}`;
      }

      const shouldUpdateStockValue = combinationsCollection === null && index === 0;

      if (shouldUpdateStockValue) {
        this._updateStock(product.stock);
      }

      $(createOrderMap.productSelect).append(
        $('<option></option>').attr('value', product.product_id).text(name).attr('data-index', index),
      );
    }

    const $noRecordsRow = $(createOrderMap.noRecordsFound).find(
      createOrderMap.noRecordsFoundRow,
    );

    if ($noRecordsRow.length !== 0) {
      $noRecordsRow.remove();
    }

    this._showResultBlock();
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

    this.combinations = combinations;

    if ($(createOrderMap.combinationsRow).length === 0) {
      const $combinationsTemplate = $($(createOrderMap.combinationsTemplate).html());
      $(createOrderMap.productSelectRow).after($combinationsTemplate);
    }

    const $combinationsSelect = $(createOrderMap.combinationsSelect);
    $combinationsSelect.empty();

    const entries = Object.entries(combinations);

    let i = 0; // This is needed because index in this case is attribute combination id

    for (const [id, combination] of entries) {
      if (i === 0) {
        this._updateStock(combination.stock);
      }

      const name = `${combination.attribute  } - ${  combination.formatted_price}`;
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

    const $customizedFieldTemplate = $(createOrderMap.cartProductCustomizedFieldTemplate);
    let $customizedFieldTemplateContent = $($customizedFieldTemplate.html());

    const customizationLabel = $(createOrderMap.productSelect).data('customization-label');

    $customizedFieldTemplateContent.find(createOrderMap.customizedLabelClass)
      .text(customizationLabel);

    $(createOrderMap.quantityRow).before($customizedFieldTemplateContent);

    $.each(customizationFields.product_customization_fields, (index, field) => {
      const $fieldTemplate = $($(createOrderMap.customizedFieldTypes[field.type]).html());

      $customizedFieldTemplateContent = $($customizedFieldTemplate.html());
      if (field.required) {
        $customizedFieldTemplateContent.find(createOrderMap.customizedLabelClass)
          .append('<span class="text-danger">*</span>');
      }

      $customizedFieldTemplateContent.find(createOrderMap.customizedLabelClass)
        .append(field.name);

      const $fieldWrapper = $customizedFieldTemplateContent
        .find(createOrderMap.customizedFieldInputWrapper);

      $fieldWrapper.append($fieldTemplate);
      $fieldWrapper.find(createOrderMap.customizedFieldInput)
        .attr('data-customization-field-id', field.customization_field_id);

      $(createOrderMap.quantityRow).before($customizedFieldTemplateContent);
    });
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
   * Removes combination select for products with no combinations
   *
   * @private
   */
  _removeCombinationSelect() {
    $(createOrderMap.combinationsRow).remove();
  }

  /**
   * Removes customized fields select for products with no customized fields
   *
   * @private
   */
  _removeCustomizedFields() {
    $(createOrderMap.customizedFieldContainer).remove();
  }

  /**
   * Handles product select change
   *
   * @param event
   * @private
   */
  _handleProductChange(product) {
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
   * Updates stock text helper value
   *
   * @param stock
   * @private
   */
  _updateStock(stock) {
    $(createOrderMap.inStockCounter).text(stock);
    $(createOrderMap.quantityInput).attr('max', stock);
  }

  /**
   * Shows result block
   *
   * @private
   */
  _showResultBlock() {
    $(createOrderMap.productResultBlock).show();
    $(createOrderMap.productResultBlock).removeClass('d-none');
  }

  /**
   * Hides result block
   *
   * @private
   */
  _hideResultBlock() {
    $(createOrderMap.productResultBlock).hide();
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
   * Emptes products list
   *
   * @private
   */
  _cleanProductsList() {
    this.$productsTable.find('tbody').empty();
  }
}
