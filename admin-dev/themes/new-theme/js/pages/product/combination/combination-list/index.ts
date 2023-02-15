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
import CombinationsListRenderer from '@pages/product/combination/combination-list/combinations-list-renderer';
import DynamicPaginator from '@components/pagination/dynamic-paginator';
import ProductEventMap from '@pages/product/product-event-map';
import initCombinationModal from '@pages/product/combination/combination-modal';
import initFilters from '@pages/product/combination/filters';
import {EventEmitter} from 'events';
import initCombinationGenerator from '@pages/product/combination/generator';
import {getProductAttributeGroups} from '@pages/product/service/attribute-group';
import BulkEditionHandler from '@pages/product/combination/combination-list/bulk-edition-handler';
import PaginatedCombinationsService from '@pages/product/service/paginated-combinations-service';
import BulkDeleteHandler from '@pages/product/combination/combination-list/bulk-delete-handler';
import BulkChoicesSelector from '@pages/product/combination/combination-list/bulk-choices-selector';
import ProductFormModel from '@pages/product/edit/product-form-model';
import CombinationsListEditor from '@pages/product/combination/combination-list/combinations-list-editor';
import {App} from 'vue';
import RowDeleteHandler from '@pages/product/combination/combination-list/row-delete-handler';
import {AttributeGroup} from '@pages/product/combination/types';

const {$} = window;
const CombinationEvents = ProductEventMap.combinations;
const CombinationsMap = ProductMap.combinations;

/**
  * This is the main class driving the combinations list, it orchestrates the initialisation of the several components
  * and it also handles their interaction so that each component remains as independent as possible. Most of the interactions
  * are driven via the event system.
  */
export default class CombinationsList {
  private readonly productId: number;

  private readonly shopId: number;

  private readonly eventEmitter: EventEmitter;

  private readonly combinationManagerWidget: HTMLDivElement;

  private readonly $productForm: JQuery;

  private readonly $combinationsFormContainer: JQuery;

  private readonly $preloader: JQuery;

  private readonly $paginatedList: JQuery;

  private readonly $emptyState: JQuery;

  private readonly $emptyFiltersState: JQuery;

  private readonly paginatedCombinationsService: PaginatedCombinationsService;

  private readonly productFormModel: ProductFormModel;

  private filtersApp?: App | null;

  private combinationModalApp?: App | null;

  private combinationGeneratorApp?: App | null;

  private paginator?: DynamicPaginator;

  private renderer?: CombinationsListRenderer;

  private editor?: CombinationsListEditor;

  private initialized: boolean;

  private productAttributeGroups: Array<AttributeGroup>;

  constructor(productId: number, productFormModel: ProductFormModel, shopId: number) {
    this.shopId = shopId;
    this.productId = productId;
    this.productFormModel = productFormModel;
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.$productForm = $(ProductMap.productForm);
    this.$combinationsFormContainer = $(CombinationsMap.combinationsFormContainer);
    this.combinationManagerWidget = document.querySelector<HTMLDivElement>(CombinationsMap.combinationManager)!;

    this.$preloader = $(CombinationsMap.preloader);
    this.$paginatedList = $(CombinationsMap.combinationsPaginatedList);
    this.$emptyState = $(CombinationsMap.emptyState);
    this.$emptyFiltersState = $(CombinationsMap.emptyFiltersState);

    this.initialized = false;
    this.paginatedCombinationsService = new PaginatedCombinationsService(productId, shopId);
    this.productAttributeGroups = [];

    new RowDeleteHandler(this.eventEmitter);

    this.init();
  }

  private init(): void {
    // Paginate to first page when tab is shown
    this.$productForm
      .find(CombinationsMap.navigationTab)
      .on('shown.bs.tab', () => this.initializeComponents());

    // Finally watch events related to combination listing
    this.watchEvents();
  }

  private watchEvents(): void {
    this.eventEmitter.on(CombinationEvents.refreshCombinationList, () => this.refreshCombinationList());
    this.eventEmitter.on(CombinationEvents.refreshPage, () => this.refreshPage());

    this.eventEmitter.on(CombinationEvents.updateAttributeFilters, (attributeIdsByGroupId: Array<number[]>) => {
      const currentFilters = this.paginatedCombinationsService.getFilters();
      currentFilters.attributes = attributeIdsByGroupId.filter((attributeIds: number[]) => attributeIds.length !== 0);
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

    this.eventEmitter.on(CombinationEvents.combinationDeleted, () => this.refreshPage());
    this.eventEmitter.on(CombinationEvents.bulkDeleteFinished, () => this.refreshPage());
    this.eventEmitter.on(CombinationEvents.bulkUpdateFinished, () => this.refreshPage());
  }

  private initializeComponents(): void {
    if (this.initialized) {
      return;
    }

    // Preloader is only shown on first load
    this.$preloader.toggleClass('d-none', false);
    this.initialized = true;

    // External vue components
    this.combinationGeneratorApp = initCombinationGenerator(
      CombinationsMap.combinationsGeneratorContainer,
      this.eventEmitter,
      this.productId,
      this.shopId,
    );
    this.combinationModalApp = initCombinationModal(
      CombinationsMap.editModal,
      this.paginatedCombinationsService,
      this.eventEmitter,
    );
    this.filtersApp = initFilters(
      CombinationsMap.combinationsFiltersContainer,
      this.eventEmitter,
      this.productAttributeGroups,
    );

    // List related components
    this.renderer = new CombinationsListRenderer(
      this.eventEmitter,
      this.productFormModel,
      (sortColumn: string, sorOrder: string) => this.sortList(sortColumn, sorOrder),
      (isEmpty: boolean) => this.emptyStateCallback(isEmpty),
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
    );
    const bulkChoicesSelector = new BulkChoicesSelector(
      this.eventEmitter,
      this.combinationManagerWidget,
      this.paginatedCombinationsService,
      this.paginator,
    );

    new BulkEditionHandler(this.productId, this.eventEmitter, bulkChoicesSelector);
    new BulkDeleteHandler(this.productId, this.eventEmitter, bulkChoicesSelector);

    this.refreshCombinationList();
  }

  private async refreshCombinationList(): Promise<void> {
    // Wait for product attributes to adapt rendering depending on their number
    this.productAttributeGroups = await getProductAttributeGroups(this.productId, this.shopId);

    if (this.filtersApp) {
      this.filtersApp = initFilters(
        CombinationsMap.combinationsFiltersContainer,
        this.eventEmitter,
        this.productAttributeGroups,
      );
    }

    // We trigger the clearFilters which will be handled by the filters app, after clean the component will trigger
    // the updateAttributeGroups event which is caught by this manager which will in turn refresh the list to first page
    this.eventEmitter.emit(CombinationEvents.clearFilters);
  }

  private refreshPage(): void {
    if (this.paginator) {
      this.paginator.paginate(this.paginator.getCurrentPage());
    }
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

  private emptyStateCallback(isEmpty: boolean): void {
    const hasFilters = Object.keys(this.paginatedCombinationsService.getFilters().attributes).length !== 0;
    const $combinationsTable = $(CombinationsMap.combinationsTable);

    if (isEmpty) {
      // Toggle empty state. There are 2 different empty states:
      //   1. when product has no combinations at all
      //   2. when combinations are not found by certain filters
      this.$emptyState.toggleClass('d-none', hasFilters);
      this.$paginatedList.toggleClass('d-none', !hasFilters);
      this.$emptyFiltersState.toggleClass('d-none', !hasFilters);
    } else {
      // reset everything if combinations list is not empty
      this.$paginatedList.removeClass('d-none');
      this.$emptyState.addClass('d-none');
      this.$emptyFiltersState.addClass('d-none');
      $combinationsTable.removeClass('d-none');
    }

    // After init preloader is always empty
    this.$preloader.toggleClass('d-none', true);
  }
}
