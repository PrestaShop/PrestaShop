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

import {FormIframeModal} from '@components/modal';
import ProductMap from '@pages/product/product-map';
import ProductEventMap from '@pages/product/product-event-map';
import {EventEmitter} from 'events';
import SpecificPriceListRenderer from '@pages/product/components/specific-price/specific-price-list-renderer';
import Router from '@components/router';
import FormFieldToggler from '@components/form/form-field-toggler';
import FormFieldDisabler from '@components/form/form-field-disabler';
import CatalogPriceRuleList from '@pages/product/components/catalog-price-rule/catalog-price-rule-list';
import {isUndefined} from '@PSTypes/typeguard';
import PaginatedSpecificPricesService from '@pages/product/services/paginated-specific-prices-service';
import DynamicPaginator from '@components/pagination/dynamic-paginator';

import ClickEvent = JQuery.ClickEvent;

const SpecificPriceMap = ProductMap.specificPrice;
const CatalogPriceRulesMap = ProductMap.catalogPriceRule;
const PriorityMap = SpecificPriceMap.priority;

export default class SpecificPricesManager {
  eventEmitter: EventEmitter;

  productId: number;

  listContainer: HTMLElement;

  router: Router;

  catalogPriceRuleList: CatalogPriceRuleList;

  paginator!: DynamicPaginator;

  constructor(
    productId: number,
  ) {
    this.router = new Router();
    this.productId = productId;
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.specificPriceList = new SpecificPriceList(productId);
    this.catalogPriceRuleList = new CatalogPriceRuleList();

    this.listContainer = document.querySelector<HTMLElement>(SpecificPriceMap.listContainer)!;
    this.initComponents();
    this.specificPriceList.renderList();
    this.initListeners();
  }

  private initListeners(): void {
    this.eventEmitter.on(ProductEventMap.specificPrice.listUpdated, () => {
      this.paginator.paginate(1);
    });
  }

  private initComponents() {
    this.paginator = new DynamicPaginator(
      SpecificPriceMap.paginationContainer,
      new PaginatedSpecificPricesService(this.productId),
      new SpecificPriceListRenderer(this.productId),
      1,
    );

    this.initSpecificPriceModals();
    this.initCatalogPriceRules();
    // Enable/disabled the priority selectors depending on the priority type selected (global or custom)
    new FormFieldDisabler({
      disablingInputSelector: PriorityMap.priorityTypeCheckboxesSelector,
      matchingValue: '0',
      targetSelector: PriorityMap.priorityListWrapper,
    });
  }

  private initCatalogPriceRules()
  {
    const showCatalogPriceRulesButton = document.querySelector<HTMLElement>(CatalogPriceRulesMap.showCatalogPriceRules);
    const hideCatalogPriceRulesButton = document.querySelector<HTMLElement>(CatalogPriceRulesMap.hideCatalogPriceRules);
    const catalogPriceRulesContainer = document.querySelector<HTMLElement>(CatalogPriceRulesMap.blockContainer);

    let listRendered = false;

    if (showCatalogPriceRulesButton === null || hideCatalogPriceRulesButton === null || catalogPriceRulesContainer === null) {
      return;
    }

    /** This should be the form container for the whole form element, so we can hide the whole block */
    const formContainer = <HTMLElement>catalogPriceRulesContainer.parentNode;

    if (formContainer === null) {
      return;
    }

    showCatalogPriceRulesButton.addEventListener('click', (e) => {
      formContainer.classList.remove('d-none');
      hideCatalogPriceRulesButton.classList.remove('d-none');
      showCatalogPriceRulesButton.classList.add('d-none');
      if (!listRendered) {
        this.catalogPriceRuleList.renderList();
      } else {
        this.catalogPriceRuleList.toggleListVisibility(true);
      }
      listRendered = true;

    });

    hideCatalogPriceRulesButton.addEventListener('click', (e) => {
      this.catalogPriceRuleList.toggleListVisibility(false);
      formContainer.classList.add('d-none');
      hideCatalogPriceRulesButton.classList.add('d-none');
      showCatalogPriceRulesButton.classList.remove('d-none');

    });
  }

  private initSpecificPriceModals() {
    // Delegate listener for each edit buttons in the list (even future added ones)
    $(this.listContainer).on('click', SpecificPriceMap.listFields.editBtn, (event: ClickEvent) => {
      if (!(event.currentTarget instanceof HTMLElement)) {
        return;
      }

      const editButton = event.currentTarget;
      const {specificPriceId} = editButton.dataset;

      if (isUndefined(specificPriceId)) {
        return;
      }

      const url = this.router.generate(
        'admin_products_specific_prices_edit',
        {
          specificPriceId,
          liteDisplaying: 1,
        },
      );
      this.renderSpecificPriceModal(
        url,
        editButton.dataset.modalTitle || 'Edit specific price',
        editButton.dataset.confirmButtonLabel || 'Save and publish',
        editButton.dataset.cancelButtonLabel || 'Cancel',
      );
    });

    // Creation modal on single add button
    const addButton = document.querySelector<HTMLElement>(SpecificPriceMap.addSpecificPriceBtn);

    if (addButton === null) {
      return;
    }

    addButton.addEventListener('click', (e) => {
      e.stopImmediatePropagation();
      const url = this.router.generate(
        'admin_products_specific_prices_create',
        {
          productId: this.productId,
          liteDisplaying: 1,
        },
      );
      this.renderSpecificPriceModal(
        url,
        addButton.dataset.modalTitle || 'Add new specific price',
        addButton.dataset.confirmButtonLabel || 'Save and publish',
        addButton.dataset.cancelButtonLabel || 'Cancel',
      );
    });
  }

  private renderSpecificPriceModal(
    formUrl: string,
    modalTitle: string,
    confirmButtonLabel: string,
    closeButtonLabel: string,
  ) {
    const iframeModal = new FormIframeModal({
      id: 'modal-specific-price-form',
      formSelector: 'form[name="specific_price"]',
      formUrl,
      closable: true,
      modalTitle,
      closeButtonLabel,
      confirmButtonLabel,
      closeOnConfirm: false,
      onFormLoaded: (form: HTMLFormElement, formData: FormData, dataAttributes: DOMStringMap | null): void => {
        if (dataAttributes && dataAttributes.alertsSuccess === '1') {
          this.eventEmitter.emit(ProductEventMap.specificPrice.listUpdated);
        }
      },
      formConfirmCallback: (form: HTMLFormElement): void => {
        form.submit();
      },
    });
    iframeModal.show();
  }
}
