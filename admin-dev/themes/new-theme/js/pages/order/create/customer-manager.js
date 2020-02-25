/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import createOrderMap from '@pages/order/create/create-order-map';
import CustomerRenderer from '@pages/order/create/customer-renderer';
import {EventEmitter} from '@components/event-emitter';
import eventMap from '@pages/order/create/event-map';
import Router from '@components/router';

const $ = window.$;

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

    this._initListeners();

    return {
      search: searchPhrase => this._search(searchPhrase),
      selectCustomer: event => this._selectCustomer(event),
      loadCustomerCarts: currentCartId => this._loadCustomerCarts(currentCartId),
      loadCustomerOrders: () => this._loadCustomerOrders(),
    };
  }

  /**
   * Initializes event listeners
   *
   * @private
   */
  _initListeners() {
    this.$container.on('click', createOrderMap.changeCustomerBtn, () => this._changeCustomer());
    this._onCustomerSearch();
    this._onCustomerSelect();
  }

  /**
   * Listens for customer search event
   *
   * @private
   */
  _onCustomerSearch() {
    EventEmitter.on(eventMap.customerSearched, (response) => {
      this.activeSearchRequest = null;
      this.customerRenderer.renderSearchResults(response.customers);
    });
  }

  /**
   * Listens for customer select event
   *
   * @private
   */
  _onCustomerSelect() {
    EventEmitter.on(eventMap.customerSelected, (event) => {
      const $chooseBtn = $(event.currentTarget);
      this.customerId = $chooseBtn.data('customer-id');

      this.customerRenderer.displaySelectedCustomerBlock($chooseBtn);
    });
  }

  /**
   * Handles use case when customer is changed
   *
   * @private
   */
  _changeCustomer() {
    this.customerRenderer.showCustomerSearch();
  }

  /**
   * Loads customer carts list
   *
   * @param currentCartId
   */
  _loadCustomerCarts(currentCartId) {
    const customerId = this.customerId;

    $.get(this.router.generate('admin_customers_carts', {customerId})).then((response) => {
      this.customerRenderer.renderCarts(response.carts, currentCartId);
    }).catch((e) => {
      showErrorMessage(e.responseJSON.message);
    });
  }

  /**
   * Loads customer orders list
   */
  _loadCustomerOrders() {
    const customerId = this.customerId;

    $.get(this.router.generate('admin_customers_orders', {customerId})).then((response) => {
      this.customerRenderer.renderOrders(response.orders);
    }).catch((e) => {
      showErrorMessage(e.responseJSON.message);
    });
  }

  /**
   * @param {Event} chooseCustomerEvent
   *
   * @return {Number}
   */
  _selectCustomer(chooseCustomerEvent) {
    EventEmitter.emit(eventMap.customerSelected, chooseCustomerEvent);

    return this.customerId;
  }

  /**
   * Searches for customers
   * @todo: fix showing not found customers and rerender after change customer
   * @private
   */
  _search(searchPhrase) {
    if (searchPhrase.length < 3) {
      return;
    }

    if (this.activeSearchRequest !== null) {
      this.activeSearchRequest.abort();
    }

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

      showErrorMessage(response.responseJSON.message);
    });
  }
}
