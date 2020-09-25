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

import createOrderMap from '@pages/order/create/create-order-map';
import CustomerRenderer from '@pages/order/create/customer-renderer';
import {EventEmitter} from '@components/event-emitter';
import eventMap from '@pages/order/create/event-map';
import Router from '@components/router';

const {$} = window;

/**
 * Responsible for customers managing. (search, select, get customer info etc.)
 */
export default class CustomerManager {
  constructor() {
    this.customerId = null;
    this.activeSearchRequest = null;

    this.router = new Router();
    this.$container = $(createOrderMap.customerSearchBlock);
    this.$searchInput = $(createOrderMap.customerSearchInput);
    this.$customerSearchResultBlock = $(createOrderMap.customerSearchResultsBlock);
    this.customerRenderer = new CustomerRenderer();

    this.initListeners();
    this.initAddCustomerIframe();

    return {
      search: (searchPhrase) => this.search(searchPhrase),
      selectCustomer: (event) => this.selectCustomer(event),
      loadCustomerCarts: (currentCartId) => this.loadCustomerCarts(currentCartId),
      loadCustomerOrders: () => this.loadCustomerOrders(),
    };
  }

  /**
   * Initializes event listeners
   *
   * @private
   */
  initListeners() {
    this.$container.on('click', createOrderMap.changeCustomerBtn, () => this.changeCustomer());
    this.onCustomerSearch();
    this.onCustomerSelect();
    this.onCustomersNotFound();
  }

  /**
   * @private
   */
  initAddCustomerIframe() {
    $(createOrderMap.customerAddBtn).fancybox({
      type: 'iframe',
      width: '90%',
      height: '90%',
    });
  }

  /**
   * Listens for customer search event
   *
   * @private
   */
  onCustomerSearch() {
    EventEmitter.on(eventMap.customerSearched, (response) => {
      this.activeSearchRequest = null;
      this.customerRenderer.hideSearchingCustomers();

      if (response.customers.length === 0) {
        EventEmitter.emit(eventMap.customersNotFound);

        return;
      }

      this.customerRenderer.renderSearchResults(response.customers);
    });
  }

  /**
   * Listens for event of when no customers were found by search
   *
   * @private
   */
  onCustomersNotFound() {
    EventEmitter.on(eventMap.customersNotFound, () => {
      this.customerRenderer.showNotFoundCustomers();
      this.customerRenderer.hideCheckoutHistoryBlock();
    });
  }

  /**
   * Listens for customer select event
   *
   * @private
   */
  onCustomerSelect() {
    EventEmitter.on(eventMap.customerSelected, (event) => {
      const $chooseBtn = $(event.currentTarget);
      this.customerId = $chooseBtn.data('customer-id');

      const createAddressUrl = this.router.generate(
        'admin_addresses_create',
        {
          liteDisplaying: 1,
          submitFormAjax: 1,
          id_customer: this.customerId,
        },
      );
      $(createOrderMap.addressAddBtn).attr('href', createAddressUrl);

      this.customerRenderer.displaySelectedCustomerBlock($chooseBtn);
    });
  }

  /**
   * Handles use case when customer is changed
   *
   * @private
   */
  changeCustomer() {
    this.customerRenderer.showCustomerSearch();
  }

  /**
   * Loads customer carts list
   *
   * @param currentCartId
   */
  loadCustomerCarts(currentCartId) {
    const {customerId} = this;

    this.customerRenderer.showLoadingCarts();
    $.get(this.router.generate('admin_customers_carts', {customerId})).then((response) => {
      this.customerRenderer.renderCarts(response.carts, currentCartId);
    }).catch((e) => {
      window.showErrorMessage(e.responseJSON.message);
    });
  }

  /**
   * Loads customer orders list
   */
  loadCustomerOrders() {
    const {customerId} = this;

    this.customerRenderer.showLoadingOrders();
    $.get(this.router.generate('admin_customers_orders', {customerId})).then((response) => {
      this.customerRenderer.renderOrders(response.orders);
    }).catch((e) => {
      window.showErrorMessage(e.responseJSON.message);
    });
  }

  /**
   * @param {Event} chooseCustomerEvent
   *
   * @return {Number}
   */
  selectCustomer(chooseCustomerEvent) {
    EventEmitter.emit(eventMap.customerSelected, chooseCustomerEvent);

    return this.customerId;
  }

  /**
   * Searches for customers
   *
   * @private
   */
  search(searchPhrase) {
    if (searchPhrase.length === 0) {
      return;
    }

    if (this.activeSearchRequest !== null) {
      this.activeSearchRequest.abort();
    }

    this.customerRenderer.clearShownCustomers();
    this.customerRenderer.hideNotFoundCustomers();
    this.customerRenderer.showSearchingCustomers();
    const $searchRequest = $.get(this.router.generate('admin_customers_search'), {
      customer_search: searchPhrase,
    });
    this.activeSearchRequest = $searchRequest;

    $searchRequest.then((response) => {
      EventEmitter.emit(eventMap.customerSearched, response);
    }).catch((response) => {
      if (response.statusText === 'abort') {
        return;
      }

      window.showErrorMessage(response.responseJSON.message);
    });
  }
}
