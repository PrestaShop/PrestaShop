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

const combinationListFormId = '#combination_list';
const attachmentsBlockId = '#product_specifications_attachments';
// It does not include "#" so it can be selected by getElementById
const isSelectedCombinationClass = 'combination-is-selected';
const commonBulkSelectAllClass = 'bulk-select-all';
const bulkCombinationSelectAllInPageId = 'bulk-select-all-in-page';
const progressModalId = 'bulk-combination-progress-modal';

export default {
  productForm: 'form[name=product]',
  productLocalizedNameInput: 'input[name^="product[header][name]"]',
  productLocalizedLinkRewriteInput: 'input[name^="product[seo][link_rewrite]"]',
  productTypePreview: '.product-type-preview',
  productType: {
    headerSelector: '#product_header_type',
    headerPreviewButton: '.product-type-preview',
    switchModalId: 'switch-product-type-modal',
    switchModalSelector: '#switch-product-type-modal .header-product-type-selector',
    switchModalContent: '#product-type-selector-modal-content',
    switchModalButton: '#switch-product-type-modal .btn-confirm-submit',
    productTypeSelector: {
      choicesContainer: '.product-type-choices',
      typeChoices: '.product-type-choice',
      defaultChoiceClass: 'btn-outline-secondary',
      selectedChoiceClass: 'btn-primary',
      typeDescription: '.product-type-description-content',
    },
  },
  create: {
    newProductButton: '.new-product-button',
    createModalSelector: '#product_type',
  },
  invalidField: '.is-invalid',
  productFormSubmitButton: '.product-form-save-button',
  navigationBar: '#form-nav',
  dropzoneImagesContainer: '.product-image-dropzone',
  featureValues: {
    collectionContainer: '.feature-values-collection',
    collectionRowsContainer: '.feature-values-collection > .col-sm',
    collectionRow: 'div.product-feature',
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
    combinationManager: '#product_combinations_combination_manager',
    preloader: '#combinations-preloader',
    emptyState: '#combinations-empty-state',
    emptyFiltersState: '#combinations-empty-filters-state',
    combinationsPaginatedList: '#combinations-paginated-list',
    combinationsFormContainer: '#combinations-list-form-container',
    combinationsFiltersContainer: '#combinations_filters',
    filtersSelectorButtons: '.combinations-filters-dropdown button',
    combinationsGeneratorContainer: '#product_combinations_generator',
    combinationsTable: `${combinationListFormId}`,
    combinationsTableBody: `${combinationListFormId} tbody`,
    combinationIdInputsSelector: '.combination-id-input',
    deleteCombinationSelector: '.delete-combination-item',
    combinationName: 'form .combination-name-row .text-preview',
    paginationContainer: '#combinations-pagination',
    loadingSpinner: '#productCombinationsLoading',
    impactOnPriceInputWrapper: '.combination-impact-on-price',
    referenceInputWrapper: '.combination-reference',
    sortableColumns: '.ps-sortable-column',
    combinationItemForm: {
      isDefaultKey: 'combination_item[is_default]',
      deltaQuantityKey: 'combination_item[delta_quantity][delta]',
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
      isSelectedCombination: `.${isSelectedCombinationClass}`,
      combinationImg: '.combination-image',
      deltaQuantityWrapper: '.delta-quantity',
      deltaQuantityInput: (rowIndex: number): string => `${combinationListFormId}_combinations_${rowIndex}_delta_quantity_delta`,
      combinationCheckbox: (rowIndex: number): string => `${combinationListFormId}_combinations_${rowIndex}_is_selected`,
      combinationIdInput: (rowIndex: number): string => `${combinationListFormId}_combinations_${rowIndex}_combination_id`,
      combinationNameInput: (rowIndex: number): string => `${combinationListFormId}_combinations_${rowIndex}_name`,
      referenceInput: (rowIndex: number): string => `${combinationListFormId}_combinations_${rowIndex}_reference_value`,
      impactOnPriceInput: (rowIndex: number): string => `${combinationListFormId}_combinations_${rowIndex}_impact_on_price_value`,
      finalPriceTeInput: (rowIndex: number): string => `${combinationListFormId}_combinations_${rowIndex}_final_price_te`,
      quantityInput: (rowIndex: number): string => `${combinationListFormId}_combinations_${rowIndex}_delta_quantity_quantity`,
      isDefaultInput: (rowIndex: number): string => `${combinationListFormId}_combinations_${rowIndex}_is_default`,
      editButton: (rowIndex: number): string => `${combinationListFormId}_combinations_${rowIndex}_edit`,
      deleteButton: (rowIndex: number): string => `${combinationListFormId}_combinations_${rowIndex}_delete`,
    },
    list: {
      combinationRow: '.combination-list-row',
      priceImpactTaxExcluded: '.combination-impact-on-price-tax-excluded',
      priceImpactTaxIncluded: '.combination-impact-on-price-tax-included',
      isDefault: '.combination-is-default-input',
      finalPrice: '.combination-final-price',
      finalPricePreview: '.text-preview',
      modifiedFieldClass: 'combination-value-changed',
      invalidClass: 'is-invalid',
      editionModeClass: 'edition-mode',
      fieldInputs: `.combination-list-row :input:not(.${commonBulkSelectAllClass}):not(.${isSelectedCombinationClass})`,
      errorAlerts: '.combination-list-row .alert-danger',
      rowActionButtons: '.combination-row-actions button',
      footer: {
        cancel: '#cancel-combinations-edition',
        reset: '#reset-combinations-edition',
        save: '#save-combinations-edition',
      },
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
    bulkCombinationFormBtn: '#combination-bulk-form-btn',
    bulkDeleteBtn: '#combination-bulk-delete-btn',
    bulkActionBtn: '.bulk-action-btn',
    bulkActionsDropdownBtn: '#combination-bulk-actions-btn',
    bulkAllPreviewInput: '#bulk-all-preview',
    bulkSelectAll: '#bulk-select-all',
    bulkCheckboxesDropdownButton: '#bulk-all-selection-dropdown-button',
    commonBulkAllSelector: `.${commonBulkSelectAllClass}`,
    bulkSelectAllInPage: `#${bulkCombinationSelectAllInPageId}`,
    bulkSelectAllInPageId: bulkCombinationSelectAllInPageId,
    bulkProgressModalId: progressModalId,
    bulkFormModalId: 'bulk-combination-form-modal',
    bulkForm: 'form[name="bulk_combination"]',
    bulkDeltaQuantitySwitchName: 'bulk_combination[stock][disabling_switch_delta_quantity]',
    bulkFixedQuantitySwitchName: 'bulk_combination[stock][disabling_switch_fixed_quantity]',
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
    savedImageContainer: (imageId: string): string => `.dz-preview[data-id="${imageId}"]`,
    savedImage: (imageId: string): string => `.dz-preview[data-id="${imageId}"] img`,
    coveredPreview: '.dz-preview.is-cover',
    windowFileManager: '.dropzone-window-filemanager',
  },
  options: {
    availableForOrderInput: 'input[name="product[options][visibility][available_for_order]"]',
    showPriceInput: 'input[name="product[options][visibility][show_price]"]',
    showPriceSwitchContainer: '.show-price-switch-container',
  },
  suppliers: {
    productSuppliers: '#product_options_product_suppliers',
    supplierIdsInput: '#product_options_suppliers_supplier_ids',
    defaultSupplierInput: '#product_options_suppliers_default_supplier_id',
  },
  shipping: {
    deliveryTimeTypeInput: 'input[name="product[shipping][delivery_time_note_type]"]',
    deliveryTimeNotesBlock: '#product_shipping_delivery_time_notes',
  },
  seo: {
    container: '#product_seo_serp',
    defaultTitle: '.serp-default-title:input',
    watchedTitle: '.serp-watched-title:input',
    defaultDescription: '.serp-default-description',
    watchedDescription: '.serp-watched-description',
    watchedMetaUrl: '.serp-watched-url:input',
    // @TODO(NeOMakinG): This feels weird, we would prefer selecting a js- class only instead
    // But it's linked to a class duplicate in the taggable field markup not linked to the current PR
    tagFields: 'input.js-taggable-field',
    redirectOption: {
      typeInput: '#product_seo_redirect_option_type',
      targetInput: '#product_seo_redirect_option_target',
      groupSelector: '.form-group',
      labelSelector: 'label',
      helpSelector: 'small.form-text',
    },
    resetLinkRewriteBtn: '.reset-link-rewrite',
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
    deleteProductModalId: 'delete-product-footer-modal',
    duplicateProductButton: '.duplicate-product-button',
    duplicateProductModalId: 'duplicate-product-footer-modal',
    newProductButton: '.new-product-button',
    goToCatalogButton: '.go-to-catalog-button',
    cancelButton: '.cancel-button',
  },
  categories: {
    categoriesContainer: '#product_description_categories',
    categoriesModalTemplate: '#categories-modal-template',
    modalContentContainer: '#categories-modal-content',
    categoriesModalId: 'categories-modal',
    applyCategoriesBtn: '.js-apply-categories-btn',
    cancelCategoriesBtn: '.js-cancel-categories-btn',
    categoryTree: '.js-category-tree-list',
    treeElement: '.category-tree-element',
    treeElementInputs: '.category-tree-inputs',
    treeCheckboxInput: '.tree-checkbox-input',
    checkboxInput: '[type=checkbox]',
    checkedCheckboxInputs: '[type=checkbox]:checked',
    // eslint-disable-next-line
    checkboxName: (categoryId: string): string => `product[description][categories][product_categories][${categoryId}][is_associated]`,
    inputByValue: (value: number): string => `input[value="${value}"]`,
    defaultCategorySelectInput: '#product_description_categories_default_category_id',
    materialCheckbox: '.md-checkbox',
    radioInput: '[type=radio]',
    defaultRadioInput: '[type=radio]:checked',
    radioName: (categoryId: string): string => `product[description][categories][product_categories][${categoryId}][is_default]`,
    tagsContainer: '.pstaggerTagsWrapper',
    tagRemoveBtn: '.pstaggerClosingCross',
    tagCategoryIdInput: '.category-id-input',
    tagItem: '.tag-item',
    categoryNamePreview: '.category-name-preview',
    categoryNameInput: '.category-name-input',
    searchInput: '#ps-select-product-category',
    fieldset: '.tree-fieldset',
    loader: '.categories-tree-loader',
    childrenList: '.children-list',
    addCategoriesBtn: '.add-categories-btn',
    categoryFilter: {
      container: '.product_list_category_filter',
      categoryRadio: '.category-label input:radio',
      filterForm: '#product_filter_form',
      categoryInput: 'input[name="product[id_category]"]',
      expandedClass: 'less',
      collapsedClass: 'more',
      categoryChildren: '.category-children',
      categoryLabel: '.category-label',
      categoryLabelClass: 'category-label',
      categoryNode: '.category-node',
      expandAll: '.category_tree_filter_expand',
      collapseAll: '.category_tree_filter_collapse',
      resetFilter: '.category_tree_filter_reset',
    },
  },
  modules: {
    previewContainer: '.module-render-container.all-modules',
    previewButton: '.modules-list-button',
    selectorContainer: '.module-selection',
    moduleSelector: '.modules-list-select',
    selectorPreviews: '.module-selection .module-render-container',
    selectorPreview: (moduleId: string): string => `.module-selection .module-render-container.${moduleId}`,
    contentContainer: '.module-contents',
    moduleContents: '.module-contents .module-render-container',
    moduleContent: (moduleId: string): string => `.module-contents .module-render-container.${moduleId}`,
  },
  attachments: {
    attachmentsContainer: attachmentsBlockId,
    searchAttributeInput: `${attachmentsBlockId}_attached_files`,
    addAttachmentBtn: '.add-attachment',
  },
  relatedProducts: {
    searchInput: '#product_description_related_products',
  },
  priceSummary: {
    container: '.price-summary-widget',
    priceTaxExcluded: '.price-tax-excluded-value',
    priceTaxIncluded: '.price-tax-included-value',
    unitPrice: '.unit-price-value',
    margin: '.margin-value',
    marginRate: '.margin-rate-value',
    wholesalePrice: '.wholesale-price-value',
  },
  specificPrice: {
    container: '#specific-prices-container',
    paginationContainer: '#specific-prices-pagination',
    loadingSpinner: '#specific-prices-loading',
    listTable: '#specific-prices-list-table',
    modalTemplate: '#specific-price-modal-template',
    modalContentId: 'specific-price-modal',
    addSpecificPriceBtn: '.js-add-specific-price-btn',
    form: 'form[name="specific_price"]',
    listContainer: '#specific-price-list-container',
    listRowTemplate: '#specific-price-tr-template',
    deletionModalId: 'modal-confirm-delete-combination',
    listFields: {
      specificPriceId: '.specific-price-id',
      combination: '.combination',
      currency: '.currency',
      country: '.country',
      group: '.group',
      shop: '.shop',
      customer: '.customer',
      price: '.price',
      impact: '.impact',
      period: '.period',
      from: '.period .from',
      to: '.period .to',
      fromQuantity: '.from-qty',
      editBtn: '.js-edit-specific-price-btn',
      deleteBtn: '.js-delete-specific-price-btn',
    },
    priority: {
      priorityListWrapper: '.specific-price-priority-list',
      priorityTypeCheckboxesSelector: 'input[name="product[pricing][priority_management][use_custom_priority]"]',
    },
  },
};
