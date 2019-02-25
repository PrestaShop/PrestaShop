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

import CustomerSearcher from "./customer-searcher";

const $ = window.$;

/**
 *
 */
export default class OrderCreator {
  constructor() {
    this.data = {};
    this.$container = $('#orderCreationContainer');

    this.customerSearcher = new CustomerSearcher();

    this.$container.on('click', '.js-choose-customer-btn', (event) => {
      this._onCustomerChooseForOrderCreation(event);
    });
  }

  /**
   * Choses customer for which order is being created
   *
   * @param {Event} event
   *
   * @private
   */
  _onCustomerChooseForOrderCreation(event) {
    const $chooseBtn = $(event.currentTarget);
    const $customerCard = $chooseBtn.closest('.card');

    $chooseBtn.addClass('d-none');

    $customerCard.addClass('border-success');
    $customerCard.find('.js-change-customer-btn').removeClass('d-none');

    this.$container.find('.js-search-customer-block').addClass('d-none');
    this.$container.find('.js-customer-search-result:not(.border-success)').remove();

    this.data.customer_id = $chooseBtn.data('customer-id');
  }
}


