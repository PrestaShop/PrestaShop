/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

export default {
  multistoreDropdown: {
    searchInput: '.js-multistore-dropdown-search',
    scrollbar: '.js-multistore-scrollbar',
  },
  multistoreHeader: {
    modal: '.js-multishop-modal',
    modalDialog: '.js-multishop-modal-dialog',
    headerMultiShop: '.header-multishop',
    headerButton: '.js-header-multishop-open-modal',
    searchInput: '.js-multishop-modal-search',
    jsScrollbar: '.js-multishop-scrollbar',
    shopLinks: 'a.multishop-modal-shop-name',
    groupShopLinks: 'a.multishop-modal-group-name',
    setContextUrl: (
      location: string,
      urlLetter: string,
      itemId: string,
    ): string => `${location}&setShopContext=${urlLetter}-${itemId}`,
  },
  shopSelector: {
    container: '.shop-selector',
    selectInput: '.shop-selector-input',
    searchInput: '.js-shop-selector-search',
    shopItem: '.shop-selector-shop-item',
    selectedClass: 'selected-shop',
    currentClass: 'current-shop',
    shopStatus: '.shop-selector-status',
  },
  choiceTable: {
    selectAll: '.js-choice-table-select-all',
  },
  multipleChoiceTable: {
    selectColumn: '.js-multiple-choice-table-select-column',
    selectColumnCheckbox: (columnNum: string): string => `tbody tr td:nth-child(${columnNum}) input[type=checkbox]`,
  },
  formSubmitButton: '.js-form-submit-btn',
  moduleCard: {
    moduleItemList: (techName: string): string => `div.module-item-list[data-tech-name='${techName}']`,
    moduleItem: (techName: string): string => `.module-item[data-tech-name='${techName}']`,
  },
  confirmModal: (modalId: string): string => `#${modalId}`,
  translatableField: {
    toggleTab: '.translationsLocales.nav .nav-item a[data-toggle="tab"]',
    nav: '.translationsLocales.nav',
    select: '.translation-field',
    specificLocale: (selectedLocale: string): string => `.nav-item a[data-locale="${selectedLocale}"]`,
  },
  entitySearchInput: {
    searchInputSelector: '.entity-search-input',
    entitiesContainerSelector: '.entities-list',
    listContainerSelector: '.entities-list-container',
    entityItemSelector: '.entity-item',
    entityDeleteSelector: '.entity-item-delete',
    emptyStateSelector: '.empty-entity-list',
  },
  form: {
    selectChoice: (language: string): string => `select.translatable_choice[data-language="${language}"]`,
    selectLanguage: 'select.translatable_choice_language',
  },
  submittableInput: {
    inputSelector: '.submittable-input',
    buttonSelector: '.check-button',
  },
  deltaQuantityInput: {
    containerSelector: '.delta-quantity',
    quantityInputSelector: '.delta-quantity-quantity',
    deltaInputSelector: '.delta-quantity-delta',
    updateQuantitySelector: '.quantity-update',
    modifiedQuantityClass: 'quantity-modified',
    newQuantitySelector: '.new-quantity',
    initialQuantityPreviewSelector: '.initial-quantity',
  },
  disablingSwitch: {
    disablingSelector: '.ps-disabling-switch input.ps-switch',
  },
  currentLength: '.js-current-length',
  recommendedLengthInput: '.js-recommended-length-input',
  multistoreCheckbox: '.multistore-checkbox',
  formGroup: '.form-group',
  formControlInvalidClass: 'is-invalid',
  formControlInvalidFeedbackClass: 'invalid-feedback',
  inputNotCheckbox: ':input:not(.multistore-checkbox)',
  inputContainer: '.input-container',
  formControlLabel: '.form-control-label',
  tineMceEditor: {
    selector: '.autoload_rte',
    selectorClass: 'autoload_rte',
  },
  contextualNotification: {
    close: '.contextual-notification .close',
    messageBoxId: 'content-message-box',
    notificationBoxId: 'contextual-notification-box',
    notificationClass: 'contextual-notification',
  },
  ajaxConfirmation: '#ajax_confirmation',
  dateRange: {
    container: '.date-range',
    endDate: '.date-range-end-date',
    unlimitedCheckbox: '.date-range-unlimited',
  },
  progressModal: {
    classes: {
      modal: 'modal-progress',
      switchToErrorButton: 'switch-to-errors-button',
      progressPercent: 'progress-percent',
      stopProcessing: 'stop-processing',
      progressHeadline: 'progress-headline',
      progressMessage: 'progress-message',
      progressIcon: 'progress-icon',
      errorMessage: 'progress-error-message',
      errorContainer: 'progress-error-container',
      switchToProgressButton: 'switch-to-progress-button',
      downloadErrorLogButton: 'download-error-log',
      progressBarDone: 'modal_progressbar_done',
      closeModalButton: 'close-modal-button',
      progressModalError: 'progress-modal-error',
      progressStatusIcon: (status: string): string => `progress-${status}-icon`,
    },
  },
  emailInput: {
    inputSelector: '.email-input',
  },
};
