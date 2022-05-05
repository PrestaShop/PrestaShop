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
import CombinationsListRenderer from '@pages/product/components/combination-list/combinations-list-renderer';
import CombinationsService from '@pages/product/services/combinations-service';
import DynamicPaginator from '@components/pagination/dynamic-paginator';
import ProductEventMap from '@pages/product/product-event-map';
import initCombinationModal from '@pages/product/components/combination-modal';
import initFilters, {FiltersVueApp} from '@pages/product/components/filters';
import {EventEmitter} from 'events';
import initCombinationGenerator from '@pages/product/components/generator';
import {getProductAttributeGroups} from '@pages/product/services/attribute-groups';
import BulkFormHandler from '@pages/product/components/combination-list/bulk-form-handler';
import PaginatedCombinationsService from '@pages/product/services/paginated-combinations-service';
import BulkDeleteHandler from '@pages/product/components/combination-list/bulk-delete-handler';
import BulkChoicesSelector from '@pages/product/components/combination-list/bulk-choices-selector';
import ProductFormModel from '@pages/product/edit/product-form-model';
import CombinationsListEditor from '@pages/product/components/combination-list/combinations-list-editor';
import Vue from '@node_modules/vue';
import RowDeleteHandler from '@pages/product/components/combination-list/row-delete-handler';

const {$} = window;
const CombinationEvents = ProductEventMap.combinations;
const CombinationsMap = ProductMap.combinations;

export default class CombinationsList {
  private readonly productId: number;

  private readonly eventEmitter: EventEmitter;

  private readonly externalCombinationTab: HTMLDivElement;

  private readonly $productForm: JQuery;

  private readonly $combinationsFormContainer: JQuery;

  private readonly $preloader: JQuery;

  private readonly $paginatedList: JQuery;

  private readonly $emptyState: JQuery;

  private readonly combinationsService: CombinationsService;

  private readonly paginatedCombinationsService: PaginatedCombinationsService;

  private readonly productFormModel: ProductFormModel;

  private filtersApp?: FiltersVueApp;

  private combinationModalApp?: Vue;

  private combinationGeneratorApp?: Vue;

  private paginator?: DynamicPaginator;

  private renderer?: CombinationsListRenderer;

  private editor?: CombinationsListEditor;

  private initialized: boolean;

  private productAttributeGroups: Array<Record<string, any>>;

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

    this.initialized = false;
    this.combinationsService = new CombinationsService();
    this.paginatedCombinationsService = new PaginatedCombinationsService(productId);
    this.productAttributeGroups = [];

    const bulkChoicesSelector = new BulkChoicesSelector(this.eventEmitter, this.externalCombinationTab);

    new BulkFormHandler(productId, this.eventEmitter, bulkChoicesSelector, this.combinationsService);
    new BulkDeleteHandler(productId, this.eventEmitter, bulkChoicesSelector, this.combinationsService);
    new RowDeleteHandler(this.eventEmitter, this.combinationsService);

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

    // We trigger the clearFilters which will be handled by the filters app, after clean the component will trigger
    // the updateAttributeGroups event which is caught by this manager which will in turn refresh the list to first page
    this.eventEmitter.emit(CombinationEvents.clearFilters);
    this.$preloader.addClass('d-none');

    const hasCombinations = this.productAttributeGroups && this.productAttributeGroups.length;
    this.$paginatedList.toggleClass('d-none', !hasCombinations);

    if (!hasCombinations && this.renderer) {
      // Empty list
      this.renderer.render({combinations: []});
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
    this.renderer = new CombinationsListRenderer(
      this.eventEmitter,
      this.productFormModel,
      (sortColumn: string, sorOrder: string) => this.sortList(sortColumn, sorOrder),
    );

    this.paginator = new DynamicPaginator(
      CombinationsMap.paginationContainer,
      this.paginatedCombinationsService,
      this.renderer,
      0,
    );

    this.editor = new CombinationsListEditor(
      this.productId,
      this.eventEmitter,
      this.renderer,
      this.combinationsService,
    );
  }

  private sortList(sortColumn: string, sortOrder: string): void {
    if (this.editor?.editionEnabled) {
      return;
    }

    this.paginatedCombinationsService.setOrderBy(sortColumn, sortOrder);
    if (this.paginator) {
      this.paginator.paginate(1);
    }
  }

  private watchEvents(): void {
    this.eventEmitter.on(CombinationEvents.refreshCombinationList, () => this.refreshCombinationList(false));
    this.eventEmitter.on(CombinationEvents.refreshPage, () => this.refreshPage());

    this.eventEmitter.on(CombinationEvents.updateAttributeGroups, (attributeGroups) => {
      const currentFilters = this.paginatedCombinationsService.getFilters();
      currentFilters.attributes = {};
      Object.keys(attributeGroups).forEach((attributeGroupId) => {
        currentFilters.attributes[attributeGroupId] = [];
        const attributes = attributeGroups[attributeGroupId];
        attributes.forEach((attribute: Record<string, any>) => {
          currentFilters.attributes[attributeGroupId].push(attribute.id);
        });
      });

      this.paginatedCombinationsService.setFilters(currentFilters);

      if (this.paginator) {
        this.paginator.paginate(1);
      }
    });

    this.eventEmitter.on(CombinationEvents.combinationGeneratorReady, () => {
      const $generateButtons = $(CombinationsMap.generateCombinationsButton);
      $generateButtons.prop('disabled', false);
      $('body').on(
        'click',
        CombinationsMap.generateCombinationsButton,
        (event) => {
          // Stop event or it will be caught by click-outside directive and automatically close the modal
          event.stopImmediatePropagation();
          this.eventEmitter.emit(CombinationEvents.openCombinationsGenerator);
        },
      );
    });

    this.eventEmitter.on(CombinationEvents.bulkUpdateFinished, () => this.refreshPage());
  }
}
