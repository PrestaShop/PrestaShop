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
  invalidField: '.is-invalid',
  productFormSubmitButton: 'button[name="product[save]"]',
  navigationBar: '#form-nav',
  dropzoneImagesContainer: '.product-image-dropzone',
  featureValues: {
    collectionContainer: '.feature-values-collection',
    collectionRowsContainer: '.feature-values-collection > .col-sm',
    collectionRow: 'div.row.product-feature',
    featureSelect: 'select.feature-selector',
    featureValueSelect: 'select.feature-value-selector',
    customValueInput: '.custom-values input',
    customFeatureIdInput: 'input.custom-value-id',
    deleteFeatureValue: 'button.delete-feature-value',
    addFeatureValue: '.feature-value-add-button',
  },
  customizations: {
    customizationsContainer: '.product-customizations-collection',
    customizationFieldsList: '.product-customizations-collection ul',
    addCustomizationBtn: '.add-customization-btn',
    removeCustomizationBtn: '.remove-customization-btn',
    customizationFieldRow: '.customization-field-row',
  },
  combinations: {
    navigationTab: '#combinations-tab-nav',
    combinationsContainer: `${combinationListId}`,
    combinationsFiltersContainer: '#combinations_filters',
    combinationsGeneratorContainer: '#product_combinations_generator',
    combinationsTable: `${combinationListId} table`,
    combinationsTableBody: `${combinationListId} table tbody`,
    combinationIdInputsSelector: '.combination-id-input',
    isDefaultInputsSelector: '.combination-is-default-input',
    removeCombinationSelector: '.remove-combination-item',
    paginationContainer: '#combinations-pagination',
    loadingSpinner: '#productCombinationsLoading',
    quantityInputWrapper: '.combination-quantity',
    impactOnPriceInputWrapper: '.combination-impact-on-price',
    referenceInputWrapper: '.combination-reference',
    sortableColumns: '.ps-sortable-column',
    combinationItemForm: {
      quantityKey: 'combination_item[quantity][value]',
      impactOnPriceKey: 'combination_item[impact_on_price][value]',
      referenceKey: 'combination_item[reference][value]',
      tokenKey: 'combination_item[_token]',
    },
    editionForm: 'form[name="combination_form"]',
    editCombinationButtons: '.edit-combination-item',
    tableRow: {
      combinationImg: '.combination-image',
      combinationCheckbox: (rowIndex) => `${combinationListId}_combinations_${rowIndex}_is_selected`,
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
    images: {
      selectorContainer: '.combination-images-selector',
      imageChoice: '.combination-image-choice',
      checkboxContainer: '.form-check',
      checkbox: 'input[type=checkbox]',
    },
    scrollBar: '.attributes-list-overflow',
    searchInput: '#product-combinations-generate .attributes-search',
  },
  virtualProduct: {
    container: '.virtual-product-file-container',
    fileContentContainer: '.virtual-product-file-content',
  },
  dropzone: {
    configuration: {
      fileManager: '.openfilemanager',
    },
    photoswipe: {
      element: '.pswp',
    },
    dzTemplate: '.dz-template',
    dzPreview: '.dz-preview',
    sortableContainer: '#product-images-dropzone',
    sortableItems: 'div.dz-preview:not(.disabled)',
    dropzoneContainer: '.dropzone-container',
    checkbox: '.md-checkbox input',
    shownTooltips: '.tooltip.show',
    savedImageContainer: (imageId) => `.dz-preview[data-id="${imageId}"]`,
    savedImage: (imageId) => `.dz-preview[data-id="${imageId}"] img`,
    coveredPreview: '.dz-preview.is-cover',
    windowFileManager: '.dropzone-window-filemanager',
  },
  suppliers: {
    productSuppliers: '#product_suppliers',
    combinationSuppliers: '#combination_form_suppliers',
  },
  seo: {
    container: '#product_seo_serp',
    defaultTitle: '.serp-default-title:input',
    watchedTitle: '.serp-watched-title:input',
    defaultDescription: '.serp-default-description',
    watchedDescription: '.serp-watched-description',
    watchedMetaUrl: '.serp-watched-url:input',
    redirectOption: {
      typeInput: '#product_seo_redirect_option_type',
      targetInput: '#product_seo_redirect_option_target',
    },
  },
  jsTabs: '.js-tabs',
  jsArrow: '.js-arrow',
  jsNavTabs: '.js-nav-tabs',
  toggleTab: '[data-toggle="tab"]',
  formContentTab: '#form_content > .form-contenttab',
  leftArrow: '.left-arrow',
  rightArrow: '.right-arrow',
};
