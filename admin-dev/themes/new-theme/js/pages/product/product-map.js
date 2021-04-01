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

const productSuppliersId = '#product_suppliers_product_suppliers';
const productSupplierInputId = (supplierIndex, inputName) => `${productSuppliersId}_${supplierIndex}_${inputName}`;

const combinationListId = '#combination_list';

export default {
  productForm: 'form[name=product]',
  productTypeSelector: '#product_basic_type',
  productType: {
    STANDARD: 'standard',
    PACK: 'pack',
    VIRTUAL: 'virtual',
    COMBINATIONS: 'combinations',
  },
  productFormSubmitButton: 'button[name="product[save]"]',
  navigationBar: '#form-nav',
  dropzoneImagesContainer: '#product_images',
  suppliers: {
    productSuppliersCollection: `${productSuppliersId}`,
    supplierIdsInput: '#product_suppliers_supplier_ids',
    defaultSupplierInput: '#product_suppliers_default_supplier_id',
    // @todo: why its called productsTable and not productSuppliers|suppliers table? is it a mistake?
    productsTable: `${productSuppliersId} table`,
    productsTableBody: `${productSuppliersId} table tbody`,
    productSupplierRow: {
      supplierNameCell: (supplierIndex) => `#product_supplier_row_${supplierIndex} .supplier_name`,
      supplierIdInput: (supplierIndex) => productSupplierInputId(supplierIndex, 'supplier_id'),
      supplierNameInput: (supplierIndex) => productSupplierInputId(supplierIndex, 'supplier_name'),
      productSupplierIdInput: (supplierIndex) => productSupplierInputId(supplierIndex, 'product_supplier_id'),
      referenceInput: (supplierIndex) => productSupplierInputId(supplierIndex, 'reference'),
      priceInput: (supplierIndex) => productSupplierInputId(supplierIndex, 'price_tax_excluded'),
      currencyIdInput: (supplierIndex) => productSupplierInputId(supplierIndex, 'currency_id'),
    },
  },
  redirectOption: {
    typeInput: '#product_redirect_option_type',
    targetInput: '#product_redirect_option_target',
  },
  featureValues: {
    collectionContainer: '#product_features_feature_values',
    collectionRowsContainer: '#product_features_feature_values > .col-sm',
    collectionRow: 'div.row.product-feature',
    featureSelect: 'select.feature-selector',
    featureValueSelect: 'select.feature-value-selector',
    customValueInput: '.custom-values input',
    customFeatureIdInput: 'input.custom-value-id',
    deleteFeatureValue: 'button.delete-feature-value',
    addFeatureValue: '#product_features_add_feature',
  },
  customizations: {
    customizationsContainer: '#product_customizations',
    customizationFieldsList: '#product_customizations ul',
    addCustomizationBtn: '#product_customizations_add_customization_field',
    removeCustomizationBtn: '.remove-customization-btn',
    customizationFieldRow: '.customization-field-row',
  },
  combinations: {
    navigationTab: '#combinations-tab-nav',
    combinationsContainer: `${combinationListId}`,
    combinationsTable: `${combinationListId} table`,
    combinationsTableBody: `${combinationListId} table tbody`,
    combinationIdInputsSelector: '.combination-id-input',
    isDefaultInputsSelector: '.combination-is-default-input',
    paginationContainer: '#combinations-pagination',
    loadingSpinner: '#productCombinationsLoading',
    quantityInputWrapper: '.combination-quantity',
    impactOnPriceInputWrapper: '.combination-impact-on-price',
    referenceInputWrapper: '.combination-reference',
    combinationItemForm: {
      quantityKey: 'combination_item[quantity][value]',
      impactOnPriceKey: 'combination_item[impact_on_price][value]',
      referenceKey: 'combination_item[reference][value]',
      tokenKey: 'combination_item[_token]',
    },
    editCombinationButtons: '.edit-combination-item',
    tableRow: {
      combinationIdInput: (rowIndex) => `${combinationListId}_combinations_${rowIndex}_combination_id`,
      combinationNameInput: (rowIndex) => `${combinationListId}_combinations_${rowIndex}_name`,
      referenceInput: (rowIndex) => `${combinationListId}_combinations_${rowIndex}_reference_value`,
      impactOnPriceInput: (rowIndex) => `${combinationListId}_combinations_${rowIndex}_impact_on_price_value`,
      finalPriceTeInput: (rowIndex) => `${combinationListId}_combinations_${rowIndex}_final_price_te`,
      quantityInput: (rowIndex) => `${combinationListId}_combinations_${rowIndex}_quantity_value`,
      isDefaultInput: (rowIndex) => `${combinationListId}_combinations_${rowIndex}_is_default`,
      editButton: (rowIndex) => `${combinationListId}_combinations_${rowIndex}_edit`,
      deleteButton: (rowIndex) => `${combinationListId}_combinations_${rowIndex}_delete`,
    },
    editModal: '#combination-edit-modal',
  },
  dropzone: {
    configuration: {
      fileManager: '.openfilemanager',
    },
  },
};
