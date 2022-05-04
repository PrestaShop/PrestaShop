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

import BigNumber from 'bignumber.js';
import ProductMap from '@pages/product/product-map';
import CombinationsGridRenderer from '@pages/product/edit/combinations-grid-renderer';
import CombinationsService from '@pages/product/services/combinations-service';
import DynamicPaginator from '@components/pagination/dynamic-paginator';
import ProductEventMap from '@pages/product/product-event-map';
import initCombinationModal from '@pages/product/components/combination-modal';
import initFilters from '@pages/product/components/filters';
import ConfirmModal from '@components/modal';
import {EventEmitter} from 'events';
import initCombinationGenerator from '@pages/product/components/generator';
import {getProductAttributeGroups} from '@pages/product/services/attribute-groups';
import BulkFormHandler from '@pages/product/combination/bulk-form-handler';
import PaginatedCombinationsService from '@pages/product/services/paginated-combinations-service';
import BulkDeleteHandler from '@pages/product/combination/bulk-delete-handler';
import BulkChoicesSelector from '@pages/product/combination/bulk-choices-selector';
import ProductFormModel from '@pages/product/edit/product-form-model';
import {notifyFormErrors} from '@components/form/form-notification';
import {isUndefined} from '@PSTypes/typeguard';

import ChangeEvent = JQuery.ChangeEvent;

const {$} = window;
const CombinationEvents = ProductEventMap.combinations;
const CombinationsMap = ProductMap.combinations;

export default class CombinationsManager {
  productId: number;

  eventEmitter: EventEmitter;

  externalCombinationTab: HTMLDivElement;

  $productForm: JQuery;

  $combinationsFormContainer: JQuery;

  $preloader: JQuery;

  $paginatedList: JQuery;

  $emptyState: JQuery;

  paginator?: DynamicPaginator;

  combinationsRenderer?: CombinationsGridRenderer;

  filtersApp?: Record<string, any>;

  combinationModalApp: Record<string, any> | null;

  combinationGeneratorApp!: Record<string, any>;

  initialized: boolean;

  combinationsService: CombinationsService;

  paginatedCombinationsService: PaginatedCombinationsService;

  productAttributeGroups: Array<Record<string, any>>;

  productFormModel: ProductFormModel;

  editionMode: boolean = false;

  savedInputValues: Record<string, any>;

  editionDisabledElements: string[] = [
    CombinationsMap.bulkActionsBtn,
    CombinationsMap.tableRow.isSelectedCombination,
    ProductMap.combinations.bulkSelectAllInPage,
    ProductMap.combinations.filtersDropdown,
    ProductMap.combinations.generateCombinationsButton,
  ];

  constructor(productId: number, productFormModel: ProductFormModel) {
    this.productId = productId;
    this.productFormModel = productFormModel;
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.$productForm = $(ProductMap.productForm);
    this.$combinationsFormContainer = $(CombinationsMap.combinationsFormContainer);
    this.externalCombinationTab = document.querySelector<HTMLDivElement>(CombinationsMap.externalCombinationTab)!;

    this.$preloader = $(CombinationsMap.preloader);
    this.$paginatedList = $(CombinationsMap.combinationsPaginatedList);
    this.$emptyState = $(CombinationsMap.emptyState);

    this.combinationModalApp = null;

    this.initialized = false;
    this.combinationsService = new CombinationsService();
    this.paginatedCombinationsService = new PaginatedCombinationsService(productId);
    this.productAttributeGroups = [];
    this.savedInputValues = {};

    const bulkChoicesSelector = new BulkChoicesSelector(this.externalCombinationTab);
    new BulkFormHandler(productId, bulkChoicesSelector);
    new BulkDeleteHandler(productId, bulkChoicesSelector);

    this.init();
  }

  private init(): void {
    // Paginate to first page when tab is shown
    this.$productForm
      .find(CombinationsMap.navigationTab)
      .on('shown.bs.tab', () => this.showCombinationTab());
    this.$productForm
      .find(CombinationsMap.navigationTab)
      .on('hidden.bs.tab', () => this.hideCombinationTab());

    // Finally watch events related to combination listing
    this.watchEvents();
  }

  /**
   * @private
   */
  private showCombinationTab(): void {
    this.externalCombinationTab.classList.remove('d-none');
    this.firstInit();
  }

  /**
   * @private
   */
  private hideCombinationTab(): void {
    this.externalCombinationTab.classList.add('d-none');
  }

  /**
   * @private
   */
  private firstInit(): void {
    if (this.initialized) {
      return;
    }

    this.initialized = true;

    this.combinationGeneratorApp = initCombinationGenerator(
      CombinationsMap.combinationsGeneratorContainer,
      this.eventEmitter,
      this.productId,
    );
    this.combinationModalApp = initCombinationModal(
      CombinationsMap.editModal,
      this.productId,
      this.eventEmitter,
    );
    this.filtersApp = initFilters(
      CombinationsMap.combinationsFiltersContainer,
      this.eventEmitter,
      this.productAttributeGroups,
    );
    this.initPaginatedList();

    this.refreshCombinationList(true);
  }

  /**
   * @param {boolean} firstTime
   * @returns {Promise<void>}
   *
   * @private
   */
  private async refreshCombinationList(firstTime: boolean): Promise<void> {
    // Preloader is only shown on first load
    this.$preloader.toggleClass('d-none', !firstTime);
    this.$paginatedList.toggleClass('d-none', firstTime);
    this.$emptyState.addClass('d-none');

    // Wait for product attributes to adapt rendering depending on their number
    this.productAttributeGroups = await getProductAttributeGroups(this.productId);

    if (this.filtersApp) {
      this.filtersApp.filters = this.productAttributeGroups;
    }

    // When attributes are refreshed we show first page (the component will trigger a updateAttributeGroups event
    // which will itself be caught by this manager which will in turn refresh to first page)
    this.eventEmitter.emit(CombinationEvents.clearFilters);
    this.$preloader.addClass('d-none');

    const hasCombinations = this.productAttributeGroups && this.productAttributeGroups.length;
    this.$paginatedList.toggleClass('d-none', !hasCombinations);

    if (!hasCombinations && this.combinationsRenderer) {
      // Empty list
      this.combinationsRenderer.render({combinations: []});
      this.$emptyState.removeClass('d-none');
    }
  }

  /**
   * @private
   */
  private refreshPage(): void {
    if (this.paginator) {
      this.paginator.paginate(this.paginator.getCurrentPage());
    }
  }

  /**
   * @private
   */
  private initPaginatedList(): void {
    this.combinationsRenderer = new CombinationsGridRenderer(this.productFormModel);
    // Initial page is zero, we will load the first page after several other init functions
    this.paginator = new DynamicPaginator(
      CombinationsMap.paginationContainer,
      this.paginatedCombinationsService,
      this.combinationsRenderer,
      0,
    );

    this.$combinationsFormContainer.on(
      'click',
      CombinationsMap.deleteCombinationSelector,
      async (e) => {
        await this.deleteCombination(e.currentTarget);
      },
    );

    this.$combinationsFormContainer.on('change', CombinationsMap.list.fieldInputs, (event: ChangeEvent) => {
      const $input = $(event.currentTarget);

      if (
        typeof $input.val() !== 'undefined'
        && typeof $input.data('initialValue') !== 'undefined'
        && $input.val() !== $input.data('initialValue')
      ) {
        this.enableEditionMode();
      }
    });

    this.$combinationsFormContainer.on('change', ProductMap.combinations.list.priceImpactTaxExcluded, (event: ChangeEvent) => {
      this.updateByPriceImpactTaxExcluded($(event.currentTarget));
    });

    this.$combinationsFormContainer.on('change', ProductMap.combinations.list.priceImpactTaxIncluded, (event: ChangeEvent) => {
      this.updateByPriceImpactTaxIncluded($(event.currentTarget));
    });

    this.$combinationsFormContainer.on('click', ProductMap.combinations.list.isDefault, (event) => {
      const clickedDefaultId = event.currentTarget.id;
      $(`${ProductMap.combinations.list.isDefault}:not(#${clickedDefaultId})`).prop('checked', false).val(0);
      $(`#${clickedDefaultId}`).prop('checked', true).val(1);
    });

    $(CombinationsMap.list.footer.cancel).on('click', () => {
      this.cancelEdition();
    });

    $(CombinationsMap.list.footer.reset).on('click', () => {
      this.resetEdition();
    });

    $(CombinationsMap.list.footer.save).on('click', () => {
      this.saveEdition();
    });

    this.initSortingColumns();
  }

  private watchEvents(): void {
    // Build combination row
    this.eventEmitter.on(CombinationEvents.buildCombinationRow, ({combination, $row}) => {
      // Init first default, and handle radio behaviour amongst lines
      if (combination.is_default) {
        $(ProductMap.combinations.list.isDefault, $row).prop('checked', true);
      }
      this.updateByPriceImpactTaxExcluded($(ProductMap.combinations.list.priceImpactTaxExcluded, $row));
    });

    // Preset initial data attribute after each list rendering
    this.eventEmitter.on(CombinationEvents.listRendered, () => {
      // Reset saved values we only keep the last one rendered
      this.savedInputValues = {};

      $(ProductMap.combinations.list.fieldInputs, this.$combinationsFormContainer).each((index, input) => {
        const $input = $(input);
        const inputValue = $input.val();

        if (!isUndefined(inputValue) && !isUndefined($input.prop('name'))) {
          this.savedInputValues[$input.prop('name')] = inputValue;
          this.watchInputChange($input, inputValue);
        }
      });
    });

    this.eventEmitter.on(CombinationEvents.refreshCombinationList, () => this.refreshCombinationList(false));
    this.eventEmitter.on(CombinationEvents.refreshPage, () => this.refreshPage());
    /* eslint-disable */
    this.eventEmitter.on(
      CombinationEvents.updateAttributeGroups,
      attributeGroups => {
        const currentFilters = this.paginatedCombinationsService.getFilters();
        currentFilters.attributes = {};
        Object.keys(attributeGroups).forEach(attributeGroupId => {
          currentFilters.attributes[attributeGroupId] = [];
          const attributes = attributeGroups[attributeGroupId];
          attributes.forEach((attribute: Record<string, any>) => {
            currentFilters.attributes[attributeGroupId].push(attribute.id);
          });
        });

        this.paginatedCombinationsService.setFilters(currentFilters);
        if(this.paginator) {
          this.paginator.paginate(1);
        }
      }
    );

    this.eventEmitter.on(CombinationEvents.combinationGeneratorReady, () => {
      const $generateButtons = $(
        CombinationsMap.generateCombinationsButton
      );
      $generateButtons.prop('disabled', false);
      $('body').on(
        'click',
        CombinationsMap.generateCombinationsButton,
        event => {
          // Stop event or it will be caught by click-outside directive and automatically close the modal
          event.stopImmediatePropagation();
          this.eventEmitter.emit(CombinationEvents.openCombinationsGenerator);
        }
      );
    });

    this.eventEmitter.on(CombinationEvents.bulkUpdateFinished, () => this.refreshPage());
  }

  private watchInputChange($input: JQuery, initialValue: string | number | string[]): void {
    $input.data('initialValue', initialValue);
    $input.toggleClass(ProductMap.combinations.list.modifiedFieldClass, $input.val() !== initialValue);
    $input.on('change', () => {
      $input.toggleClass(ProductMap.combinations.list.modifiedFieldClass, $input.val() !== $input.data('initialValue'));
    });
  }

  private updateByPriceImpactTaxExcluded($priceImpactTaxExcluded: JQuery): void {
    const $row = $priceImpactTaxExcluded.parents(ProductMap.combinations.list.combinationRow);
    const $priceImpactTaxIncluded = $(ProductMap.combinations.list.priceImpactTaxIncluded, $row);

    if (typeof $row === 'undefined' || typeof $priceImpactTaxIncluded === 'undefined') {
      return;
    }

    // @ts-ignore
    const priceImpactTaxExcluded: BigNumber = new BigNumber($priceImpactTaxExcluded.val());

    if (priceImpactTaxExcluded.isNaN()) {
      return;
    }

    $priceImpactTaxIncluded.val(this.productFormModel.addTax(priceImpactTaxExcluded));
    this.updateFinalPrice(priceImpactTaxExcluded, $row);
  }

  private updateByPriceImpactTaxIncluded($priceImpactTaxIncluded: JQuery): void {
    const $row = $priceImpactTaxIncluded.parents(ProductMap.combinations.list.combinationRow);
    const $priceImpactTaxExcluded = $(ProductMap.combinations.list.priceImpactTaxExcluded, $row);

    if (typeof $row === 'undefined' || typeof $priceImpactTaxExcluded === 'undefined') {
      return;
    }

    // @ts-ignore
    const priceImpactTaxIncluded: BigNumber = new BigNumber($priceImpactTaxIncluded.val());

    if (priceImpactTaxIncluded.isNaN()) {
      return;
    }

    $priceImpactTaxExcluded.val(this.productFormModel.removeTax(priceImpactTaxIncluded));
    const taxRatio = this.productFormModel.getTaxRatio();

    if (taxRatio.isNaN()) {
      return;
    }

    this.updateFinalPrice(priceImpactTaxIncluded.dividedBy(taxRatio), $row);
  }

  private updateFinalPrice(priceImpactTaxExcluded: BigNumber, $row: JQuery) {
    const productPrice = this.productFormModel.getPriceTaxExcluded();
    const $finalPrice = $(ProductMap.combinations.list.finalPrice, $row);
    const $finalPricePreview = $finalPrice.siblings(ProductMap.combinations.list.finalPricePreview);
    const finalPrice = this.productFormModel.displayPrice(productPrice.plus(priceImpactTaxExcluded));

    if (typeof $finalPrice !== 'undefined') {
      $finalPrice.val(finalPrice);
    }

    if (typeof $finalPricePreview !== 'undefined') {
      $finalPricePreview.html(finalPrice);
    }
  }

  /**
   * @private
   */
  private initSortingColumns(): void {
    this.$combinationsFormContainer.on(
      'click',
      CombinationsMap.sortableColumns,
      (event) => {
        if (this.editionMode) {
          return;
        }

        const $sortableColumn = $(event.currentTarget);
        const columnName = $sortableColumn.data('sortColName');

        if (!columnName) {
          return;
        }

        let direction = $sortableColumn.data('sortDirection');

        if (!direction || direction === 'desc') {
          direction = 'asc';
        } else {
          direction = 'desc';
        }

        // Reset all columns, we need to force the attributes for CSS matching
        $(
          CombinationsMap.sortableColumns,
          this.$combinationsFormContainer,
        ).removeData('sortIsCurrent');
        $(
          CombinationsMap.sortableColumns,
          this.$combinationsFormContainer,
        ).removeData('sortDirection');
        $(
          CombinationsMap.sortableColumns,
          this.$combinationsFormContainer,
        ).removeAttr('data-sort-is-current');
        $(
          CombinationsMap.sortableColumns,
          this.$combinationsFormContainer,
        ).removeAttr('data-sort-direction');

        // Set correct data in current column, we need to force the attributes for CSS matching
        $sortableColumn.data('sortIsCurrent', 'true');
        $sortableColumn.data('sortDirection', direction);
        $sortableColumn.attr('data-sort-is-current', 'true');
        $sortableColumn.attr('data-sort-direction', direction);

        // Finally update list
        this.paginatedCombinationsService.setOrderBy(columnName, direction);
        if (this.paginator) {
          this.paginator.paginate(1);
        }
      },
    );
  }

  /**
   * @param {HTMLElement} button
   *
   * @private
   */
  private async deleteCombination(button: HTMLButtonElement): Promise<void> {
    try {
      const $deleteButton = $(button);
      const modal = new (ConfirmModal as any)(
        {
          id: 'modal-confirm-delete-combination',
          confirmTitle: $deleteButton.data('modal-title'),
          confirmMessage: $deleteButton.data('modal-message'),
          confirmButtonLabel: $deleteButton.data('modal-apply'),
          closeButtonLabel: $deleteButton.data('modal-cancel'),
          confirmButtonClass: 'btn-danger',
          closable: true,
        },
        async () => {
          const response = await this.combinationsService.deleteCombination(
            this.findCombinationId(button),
          );
          $.growl({message: response.message});
          this.eventEmitter.emit(CombinationEvents.refreshCombinationList);
        },
      );
      modal.show();
    } catch (error) {
      const errorMessage = error.responseJSON
        ? error.responseJSON.error
        : error;
      $.growl.error({message: errorMessage});
    }
  }

  /**
   * @param {HTMLElement} input of the same table row
   *
   * @returns {Number}
   *
   * @private
   */
  private findCombinationId(input: HTMLElement): any {
    return $(input)
      .closest('tr')
      .find(CombinationsMap.combinationIdInputsSelector)
      .val();
  }

  private enableEditionMode(): void {
    this.editionMode = true;
    this.$paginatedList.addClass(CombinationsMap.list.editionModeClass);

    // Disabled elements (bulk actions, filters, ...)
    this.editionDisabledElements.forEach((disabledSelector: string) => {
      const $disabledElement = this.$paginatedList.find(disabledSelector);
      $disabledElement.data('initialDisabled', $disabledElement.is(':disabled'));
      $disabledElement.prop('disabled', true);
    });
  }

  private disableEditionMode(): void {
    this.$paginatedList.removeClass(CombinationsMap.list.editionModeClass);

    // Re-enabled disabled elements
    this.editionDisabledElements.forEach((disabledSelector: string) => {
      const $disabledElement = this.$paginatedList.find(disabledSelector);
      $disabledElement.prop('disabled', $disabledElement.data('initialDisabled'));
    });
    this.editionMode = false;
  }

  private resetEdition(): void {
    $(CombinationsMap.list.fieldInputs, this.$combinationsFormContainer).each((index, input) => {
      const $input = $(input);

      if (
        typeof $input.val() !== 'undefined'
        && typeof $input.data('initialValue') !== 'undefined'
        && $input.val() !== $input.data('initialValue')
      ) {
        $input.val($input.data('initialValue')).trigger('change');
        // Remove modified class to reset display UX
        $input.removeClass(ProductMap.combinations.list.modifiedFieldClass);
        // Remove invalid class in case the field is an output with form errors
        $input.removeClass(ProductMap.combinations.list.invalidClass);
      }
    });

    // Clean all the alerts that may come from an output with form errors
    $(CombinationsMap.list.errorAlerts, this.$combinationsFormContainer).remove();
  }

  private cancelEdition(): void {
    this.resetEdition();
    this.disableEditionMode();
  }

  private async saveEdition(): Promise<void> {
    if (this.combinationsRenderer) {
      this.combinationsRenderer.toggleLoading(true);
      const formData = this.combinationsRenderer.getFormData();

      const response = await this.combinationsService.updateCombinationList(this.productId, formData);
      const jsonResponse = await response.json();

      if (jsonResponse.errors) {
        // If formContent is available we can replace the content to display the inline errors
        if (jsonResponse.formContent) {
          // Replace form content with output from response
          this.$combinationsFormContainer.html(jsonResponse.formContent);

          // Now re-watch the new inputs and set their initial value
          Object.keys(this.savedInputValues).forEach((inputName: string) => {
            const $input = $(`[name="${inputName}"]`, this.$combinationsFormContainer);
            this.watchInputChange($input, this.savedInputValues[inputName]);
          });
        } else {
          notifyFormErrors(jsonResponse);
        }
        this.combinationsRenderer.toggleLoading(false);
      } else if (jsonResponse.message) {
        $.growl({message: jsonResponse.message});
        this.disableEditionMode();
        this.eventEmitter.emit(CombinationEvents.refreshCombinationList);
      }
    }
  }
}
