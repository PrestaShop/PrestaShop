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

import createOrderPageMap from './create-order-map';
import CustomerRenderer from './customer-renderer';
import Router from '../../../components/router';

const $ = window.$;

/**
 * Responsible for customers managing. (search, select, get customer info etc.)
 */
export default class CustomerManager {
  constructor() {
    this.customerId = null;

    this.router = new Router();
    this.$container = $(createOrderPageMap.customerSearchBlock);
    this.$searchInput = $(createOrderPageMap.customerSearchInput);
    this.$customerSearchResultBlock = $(createOrderPageMap.customerSearchResultsBlock);
    this.renderer = new CustomerRenderer();

    return {
      onCustomerSearch: () => {
        this._search();
      },
      onCustomerSelect: event => this._selectCustomer(event),
      onCustomerChange: () => {
        this.renderer.showCustomerSearch();
      },
      loadCustomerCarts: (currentCartId) => {
        this._loadCustomerCarts(currentCartId);
      },
      loadCustomerOrders: () => {
        this._loadCustomerOrders();
      },
    };
  }

  /**
   * Gets customer carts
   * After Request is complete, emits event providing carts list
   *
   * @param currentCartId
   */
  _loadCustomerCarts(currentCartId) {
    const customerId = this.customerId;

    $.get(this.router.generate('admin_customers_carts', {customerId})).then((response) => {
      this.renderer.renderCarts(response.carts, currentCartId);
    }).catch((e) => {
      showErrorMessage(e.responseJSON.message);
    });
  }

  /**
   * Gets customer carts
   * After Request is complete, emits event providing orders list
   */
  _loadCustomerOrders() {
    const customerId = this.customerId;

    $.get(this.router.generate('admin_customers_orders', {customerId})).then((response) => {
      this.renderer.renderOrders(response.orders);
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
    const $chooseBtn = $(chooseCustomerEvent.currentTarget);
    this.customerId = $chooseBtn.data('customer-id');

    this.renderer.displaySelectedCustomerBlock($chooseBtn);

    return this.customerId;
  }

  /**
   * Searches for customers
   *@todo: fix showing not found customers and rerender after change customer
   * @private
   */
  _search() {
    const searchPhrase = this.$searchInput.val();

    if (searchPhrase.length < 3) {
      return;
    }

    $.get(this.router.generate('admin_customers_search'), {
      customer_search: searchPhrase,
    }).then((response) => {
      this.renderer.renderSearchResults(response.customers);
    });
  }
}

