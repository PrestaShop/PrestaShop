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
  productTypeSelector: '#product_header_type',
  productType: {
    STANDARD: 'standard',
    PACK: 'pack',
    VIRTUAL: 'virtual',
    COMBINATIONS: 'combinations',
  },
  invalidField: '.is-invalid',
  productFormSubmitButton: '.product-form-save-button',
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
    externalCombinationTab: '#external-combination-tab',
    preloader: '#combinations-preloader',
    emptyState: '#combinations-empty-state',
    combinationsPaginatedList: '#combinations-paginated-list',
    combinationsContainer: `${combinationListId}`,
    combinationsFiltersContainer: '#combinations_filters',
    combinationsGeneratorContainer: '#product_combinations_generator',
    combinationsTable: `${combinationListId} table`,
    combinationsTableBody: `${combinationListId} table tbody`,
    combinationIdInputsSelector: '.combination-id-input',
    isDefaultInputsSelector: '.combination-is-default-input',
    removeCombinationSelector: '.remove-combination-item',
    combinationName: 'form .card-header span',
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
    editionFormInputs:
      // eslint-disable-next-line
      'form[name="combination_form"] input, form[name="combination_form"] textarea, form[name="combination_form"] select',
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
    generateCombinationsButton: '.generate-combinations-button',
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
    productSuppliers: '#product_options_suppliers',
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
  footer: {
    previewUrlButton: '.preview-url-button',
    deleteProductButton: '.delete-product-button',
  },
  categories: {
    categoriesContainer: '.js-categories-container',
    categoryTree: '.js-categories-tree',
    treeElement: '.category-tree-element',
    treeElementInputs: '.category-tree-inputs',
    checkboxInput: '[type=checkbox]',
    checkedCheckboxInputs: '[type=checkbox]:checked',
    checkboxName: (categoryId) => `product[categories][product_categories][${categoryId}][is_associated]`,
    materialCheckbox: '.md-checkbox',
    radioInput: '[type=radio]',
    defaultRadioInput: '[type=radio]:checked',
    radioName: (categoryId) => `product[categories][product_categories][${categoryId}][is_default]`,
    tagsContainer: '#categories-tags-container',
    searchInput: '#ps-select-product-category',
    fieldset: '.tree-fieldset',
    loader: '.categories-tree-loader',
    childrenList: '.children-list',
    everyItems: '.less, .more',
    expandAllButton: '#categories-tree-expand',
    reduceAllButton: '#categories-tree-reduce',
  },
  modules: {
    previewContainer: '.module-render-container.all-modules',
    previewButton: '.modules-list-button',
    selectorContainer: '.module-selection',
    moduleSelector: '.modules-list-select',
    selectorPreviews: '.module-selection .module-render-container',
    selectorPreview: (moduleId) => `.module-selection .module-render-container.${moduleId}`,
    contentContainer: '.module-contents',
    moduleContents: '.module-contents .module-render-container',
    moduleContent: (moduleId) => `.module-contents .module-render-container.${moduleId}`,
  },
};
