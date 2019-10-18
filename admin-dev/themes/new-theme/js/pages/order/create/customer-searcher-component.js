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
import Router from '../../../components/router';

const $ = window.$;

/**
 * Searches customers for which order is being created
 */
export default class CustomerSearcherComponent {
  constructor() {
    this.router = new Router();
    this.$container = $(createOrderPageMap.customerSearchBlock);
    this.$searchInput = $(createOrderPageMap.customerSearchInput);
    this.$customerSearchResultBlock = $(createOrderPageMap.customerSearchResultsBlock);

    return {
      onCustomerSearch: () => {
        this._doSearch();
      },
      onCustomerChooseForOrderCreation: event => this._chooseCustomerForOrderCreation(event),
      onCustomerChange: () => {
        this._showCustomerSearch();
      },
    };
  }

  /**
   *
   * @param {Event} chooseCustomerEvent
   *
   * @return {Number}
   */
  _chooseCustomerForOrderCreation(chooseCustomerEvent) {
    const $chooseBtn = $(chooseCustomerEvent.currentTarget);
    const $customerCard = $chooseBtn.closest('.card');

    $chooseBtn.addClass('d-none');

    $customerCard.addClass('border-success');
    $customerCard.find(createOrderPageMap.changeCustomerBtn).removeClass('d-none');

    this.$container.find(createOrderPageMap.customerSearchRow).addClass('d-none');
    this.$container.find(createOrderPageMap.notSelectedCustomerSearchResults)
      .closest(createOrderPageMap.customerSearchResultColumn)
      .remove()
    ;

    return $chooseBtn.data('customer-id');
  }

  /**
   * Searches for customers
   *
   * @private
   */
  _doSearch() {
    const name = this.$searchInput.val();

    if (name.length < 4) {
      return;
    }

    $.get(this.router.generate('admin_customers_search'), {
      customer_search: name,
    }).then((response) => {
      this._clearShownCustomers();

      if (!response.found) {
        this._showNotFoundCustomers();

        return;
      }

      for (const customerId in response.customers) {
        const customerResult = response.customers[customerId];
        const customer = {
          id: customerId,
          first_name: customerResult.firstname,
          last_name: customerResult.lastname,
          email: customerResult.email,
          birthday: customerResult.birthday !== '0000-00-00' ? customerResult.birthday : ' ',
        };

        this._showCustomer(customer);
      }
    });
  }

  /**
   * Get template as jQuery object with customer data
   *
   * @param {Object} customer
   *
   * @return {jQuery}
   *
   * @private
   */
  _showCustomer(customer) {
    const $customerSearchResultTemplate = $($(createOrderPageMap.customerSearchResultTemplate).html());
    const $template = $customerSearchResultTemplate.clone();

    $template.find(createOrderPageMap.customerSearchResultName).text(`${customer.first_name} ${customer.last_name}`);
    $template.find(createOrderPageMap.customerSearchResultEmail).text(customer.email);
    $template.find(createOrderPageMap.customerSearchResultId).text(customer.id);
    $template.find(createOrderPageMap.customerSearchResultBirthday).text(customer.birthday);

    $template.find(createOrderPageMap.customerDetailsBtn).data('customer-id', customer.id);
    $template.find(createOrderPageMap.chooseCustomerBtn).data('customer-id', customer.id);

    return this.$customerSearchResultBlock.append($template);
  }

  /**
   * Shows empty result when customer is not found
   *
   * @private
   */
  _showNotFoundCustomers() {
    const $emptyResultTemplate = $($('#customerSearchEmptyResultTemplate').html());

    this.$customerSearchResultBlock.append($emptyResultTemplate);
  }

  /**
   * Clears shown customers
   *
   * @private
   */
  _clearShownCustomers() {
    this.$customerSearchResultBlock.empty();
  }

  /**
   * Shows customer search block
   *
   * @private
   */
  _showCustomerSearch() {
    this.$container.find(createOrderPageMap.customerSearchRow).removeClass('d-none');
  }
}

