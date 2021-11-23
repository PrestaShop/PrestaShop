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

import ProductMap from '@pages/product/product-map';
import CombinationsGridRenderer from '@pages/product/edit/combinations-grid-renderer';
import CombinationsService from '@pages/product/services/combinations-service';
import DynamicPaginator from '@components/pagination/dynamic-paginator';
import SubmittableInput from '@components/form/submittable-input';
import ProductEventMap from '@pages/product/product-event-map';
import initCombinationModal from '@pages/product/components/combination-modal';
import initFilters from '@pages/product/components/filters';
import ConfirmModal from '@components/modal';
import initCombinationGenerator from '@pages/product/components/generator';
import {getProductAttributeGroups} from '@pages/product/services/attribute-groups';

const {$} = window;
const CombinationEvents = ProductEventMap.combinations;
const CombinationsMap = ProductMap.combinations;

export default class CombinationsManager {
  /**
   * @param {int} productId
   * @returns {{}}
   */
  constructor(productId) {
    this.productId = productId;
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.$productForm = $(ProductMap.productForm);
    this.$combinationsContainer = $(
      ProductMap.combinations.combinationsContainer,
    );
    this.combinationIdInputsSelector = ProductMap.combinations.combinationIdInputsSelector;
    this.$externalCombinationTab = $(
      ProductMap.combinations.externalCombinationTab,
    );

    this.$preloader = $(ProductMap.combinations.preloader);
    this.$paginatedList = $(CombinationsMap.combinationsPaginatedList);
    this.$emptyState = $(CombinationsMap.emptyState);

    this.paginator = null;
    this.combinationsRenderer = null;
    this.filtersApp = null;
    this.combinationModalApp = null;
    this.combinationGeneratorApp = null;

    this.initialized = false;
    this.combinationsService = new CombinationsService(this.productId);
    this.productAttributeGroups = [];

    this.init();

    return {};
  }

  /**
   * @private
   */
  init() {
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
  showCombinationTab() {
    this.$externalCombinationTab.removeClass('d-none');
    this.firstInit();
  }

  /**
   * @private
   */
  hideCombinationTab() {
    this.$externalCombinationTab.addClass('d-none');
  }

  /**
   * @private
   */
  firstInit() {
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
  async refreshCombinationList(firstTime) {
    // Preloader is only shown on first load
    this.$preloader.toggleClass('d-none', !firstTime);
    this.$paginatedList.toggleClass('d-none', firstTime);
    this.$emptyState.addClass('d-none');

    // Wait for product attributes to adapt rendering depending on their number
    this.productAttributeGroups = await getProductAttributeGroups(
      this.productId,
    );
    this.filtersApp.filters = this.productAttributeGroups;

    // When attributes are refreshed we show first page (the component will trigger a updateAttributeGroups event
    // which will itself be caught by this manager which will in turn refresh to first page)
    this.eventEmitter.emit(CombinationEvents.clearFilters);
    this.$preloader.addClass('d-none');

    const hasCombinations = this.productAttributeGroups && this.productAttributeGroups.length;
    this.$paginatedList.toggleClass('d-none', !hasCombinations);

    if (!hasCombinations) {
      // Empty list
      this.combinationsRenderer.render({combinations: []});
      this.$emptyState.removeClass('d-none');
    }
  }

  /**
   * @private
   */
  refreshPage() {
    this.paginator.paginate(this.paginator.getCurrentPage());
  }

  /**
   * @private
   */
  initPaginatedList() {
    this.combinationsRenderer = new CombinationsGridRenderer();
    // Initial page is zero, we will load the first page after several other init functions
    this.paginator = new DynamicPaginator(
      CombinationsMap.paginationContainer,
      this.combinationsService,
      this.combinationsRenderer,
      0,
    );

    this.initSubmittableInputs();

    this.$combinationsContainer.on(
      'change',
      CombinationsMap.isDefaultInputsSelector,
      async (e) => {
        if (!e.currentTarget.checked) {
          return;
        }
        await this.updateDefaultCombination(e.currentTarget);
      },
    );

    this.$combinationsContainer.on(
      'click',
      CombinationsMap.deleteCombinationSelector,
      async (e) => {
        await this.deleteCombination(e.currentTarget);
      },
    );

    this.initSortingColumns();
  }

  /**
   * @private
   */
  watchEvents() {
    /* eslint-disable */
    this.eventEmitter.on(CombinationEvents.refreshCombinationList, () =>
      this.refreshCombinationList(false)
    );
    this.eventEmitter.on(CombinationEvents.refreshPage, () =>
      this.refreshPage()
    );
    /* eslint-disable */
    this.eventEmitter.on(
      CombinationEvents.updateAttributeGroups,
      attributeGroups => {
        const currentFilters = this.combinationsService.getFilters();
        currentFilters.attributes = {};
        Object.keys(attributeGroups).forEach(attributeGroupId => {
          currentFilters.attributes[attributeGroupId] = [];
          const attributes = attributeGroups[attributeGroupId];
          attributes.forEach(attribute => {
            currentFilters.attributes[attributeGroupId].push(attribute.id);
          });
        });

        this.combinationsService.setFilters(currentFilters);
        this.paginator.paginate(1);
      }
    );

    this.eventEmitter.on(CombinationEvents.combinationGeneratorReady, () => {
      const $generateButtons = $(
        ProductMap.combinations.generateCombinationsButton
      );
      $generateButtons.prop('disabled', false);
      $('body').on(
        'click',
        ProductMap.combinations.generateCombinationsButton,
        event => {
          // Stop event or it will be caught by click-outside directive and automatically close the modal
          event.stopImmediatePropagation();
          this.eventEmitter.emit(CombinationEvents.openCombinationsGenerator);
        }
      );
    });
  }

  /**
   * @private
   */
  initSubmittableInputs() {
    const combinationToken = this.getCombinationToken();
    const { quantityKey } = CombinationsMap.combinationItemForm;
    const { impactOnPriceKey } = CombinationsMap.combinationItemForm;
    const { referenceKey } = CombinationsMap.combinationItemForm;
    const { tokenKey } = CombinationsMap.combinationItemForm;

    /* eslint-disable */
    new SubmittableInput(CombinationsMap.quantityInputWrapper, input =>
      this.combinationsService.updateListedCombination(
        this.findCombinationId(input),
        {
          [quantityKey]: input.value,
          [tokenKey]: combinationToken
        }
      )
    );

    new SubmittableInput(CombinationsMap.impactOnPriceInputWrapper, input =>
      this.combinationsService.updateListedCombination(
        this.findCombinationId(input),
        {
          [impactOnPriceKey]: input.value,
          [tokenKey]: combinationToken
        }
      )
    );

    new SubmittableInput(CombinationsMap.referenceInputWrapper, input =>
      this.combinationsService.updateListedCombination(
        this.findCombinationId(input),
        {
          [referenceKey]: input.value,
          [tokenKey]: combinationToken
        }
      )
    );
    /* eslint-enable */
  }

  /**
   * @private
   */
  initSortingColumns() {
    this.$combinationsContainer.on(
      'click',
      CombinationsMap.sortableColumns,
      (event) => {
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
          this.$combinationsContainer,
        ).removeData('sortIsCurrent');
        $(
          CombinationsMap.sortableColumns,
          this.$combinationsContainer,
        ).removeData('sortDirection');
        $(
          CombinationsMap.sortableColumns,
          this.$combinationsContainer,
        ).removeAttr('data-sort-is-current');
        $(
          CombinationsMap.sortableColumns,
          this.$combinationsContainer,
        ).removeAttr('data-sort-direction');

        // Set correct data in current column, we need to force the attributes for CSS matching
        $sortableColumn.data('sortIsCurrent', 'true');
        $sortableColumn.data('sortDirection', direction);
        $sortableColumn.attr('data-sort-is-current', 'true');
        $sortableColumn.attr('data-sort-direction', direction);

        // Finally update list
        this.combinationsService.setOrderBy(columnName, direction);
        this.paginator.paginate(1);
      },
    );
  }

  /**
   * @param {HTMLElement} button
   *
   * @private
   */
  async deleteCombination(button) {
    try {
      const $deleteButton = $(button);
      const modal = new ConfirmModal(
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
   * @param {HTMLElement} checkedInput
   *
   * @private
   */
  async updateDefaultCombination(checkedInput) {
    const checkedInputs = this.$combinationsContainer.find(
      `${CombinationsMap.isDefaultInputsSelector}:checked`,
    );
    const checkedDefaultId = this.findCombinationId(checkedInput);

    await this.combinationsService.updateListedCombination(checkedDefaultId, {
      'combination_item[is_default]': checkedInput.value,
      'combination_item[_token]': this.getCombinationToken(),
    });

    $.each(checkedInputs, (index, input) => {
      if (this.findCombinationId(input) !== checkedDefaultId) {
        $(input).prop('checked', false);
      }
    });
  }

  /**
   * @returns {String}
   */
  getCombinationToken() {
    return $(CombinationsMap.combinationsContainer).data('combinationToken');
  }

  /**
   * @param {HTMLElement} input of the same table row
   *
   * @returns {Number}
   *
   * @private
   */
  findCombinationId(input) {
    return $(input)
      .closest('tr')
      .find(this.combinationIdInputsSelector)
      .val();
  }
}
