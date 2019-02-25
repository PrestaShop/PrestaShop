/**
 * 2007-2019 PrestaShop and Contributors
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

import * as createOrderPageMap from "./create-order-map";

const $ = window.$;

/**
 * Searches customers for which order is being created
 */
export default class CustomerSearcherComponent {
  constructor() {
    this.$searchInput = $(createOrderPageMap.customerSearchInput);
    this.$customerSearchResultBlock = $(createOrderPageMap.customerSearchResultsBlock);

    this.$searchInput.on('input', () => {
      this._doSearch();
    });

    return {};
  }

  /**
   *
   * @param {Event} chooseCustomerEvent
   *
   * @return {Number}
   */
  chooseCustomerForOrderCreation(chooseCustomerEvent) {
    const $chooseBtn = $(chooseCustomerEvent.currentTarget);
    const $customerCard = $chooseBtn.closest('.card');
    const $container = $(createOrderPageMap.createOrderContainer);

    $chooseBtn.addClass('d-none');

    $customerCard.addClass('border-success');
    $customerCard.find(createOrderPageMap.changeCustomerBtn).removeClass('d-none');

    $container.find(createOrderPageMap.customerSearchBlock).addClass('d-none');
    $container.find('.js-customer-search-result:not(.border-success)').remove();

    return $chooseBtn.data('customer-id');
  }

  /**
   * Searches for customers
   *
   * @private
   */
  _doSearch() {
    const name = this.$searchInput.val();

    if (4 > name.length) {
      return;
    }

    $.ajax(this.$searchInput.data('url'), {
      method: 'GET',
      data: {
        'action': 'searchCustomers',
        'ajax': 1,
        'customer_search': name
      }
    }).then((response) => {
      const result = JSON.parse(response);

      this._clearShownCustomers();

      if (!result.hasOwnProperty('customers')) {
        this._showNotFoundCustomers();

        return;
      }

      for (let customerId in result.customers) {
        let customerResult = result.customers[customerId];
        let customer = {
          id: customerId,
          first_name: customerResult.firstname,
          last_name: customerResult.lastname,
          email: customerResult.email,
          birthday: customerResult.birthday !== '0000-00-00' ? customerResult.birthday : ' '
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

    $template.find('.js-customer-name').text(`${customer.first_name} ${customer.last_name}`);
    $template.find('.js-customer-email').text(customer.email);
    $template.find('.js-customer-id').text(customer.id);
    $template.find('.js-customer-birthday').text(customer.birthday);

    $template.find('.js-details-customer-btn').data('customer-id', customer.id);
    $template.find('.js-choose-customer-btn').data('customer-id', customer.id);

    return this.$customerSearchResultBlock.append($template);
  }

  /**
   * Shows empty result when customer is not found
   *
   * @private
   */
  _showNotFoundCustomers() {
    const $emptyResultTemplate = $($('#customerSearchEmptyResultTemplate').html());

    this.$customerSearchResultBlock.append($emptyResultTemplate)
  }

  /**
   * Clears shown customers
   *
   * @private
   */
  _clearShownCustomers() {
    this.$customerSearchResultBlock.empty();
  }
}

