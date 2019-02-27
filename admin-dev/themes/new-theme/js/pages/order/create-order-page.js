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

import createOrderPageMap from "./create-order-map";
import CustomerSearcherComponent from "./customer-searcher-component";

const $ = window.$;

/**
 * Page Object for "Create order" page
 */
export default class CreateOrderPage {
  constructor() {
    this.data = {};
    this.$container = $(createOrderPageMap.orderCreationContainer);

    this.customerSearcher = new CustomerSearcherComponent();

    return {
      listenForCustomerSearch: () => this._handleCustomerSearch(),
      listenForCustomerChooseForOrderCreation: () => this._handleCustomerChooseForOrderCreation(),
    };
  }

  /**
   * Searches for customer
   *
   * @private
   */
  _handleCustomerSearch() {
    this.$container.on('input', createOrderPageMap.customerSearchInput, () => {
      this.customerSearcher.onCustomerSearch();
    });
  }

  /**
   * Chooses customer for which order is being created
   *
   * @private
   */
  _handleCustomerChooseForOrderCreation() {
    this.$container.on('click', createOrderPageMap.chooseCustomerBtn, (event) => {
      this.data.customer_id = this.customerSearcher.onCustomerChooseForOrderCreation(event);

      this._loadCartSummaryAfterChoosingCustomer();
    });

    this.$container.on('click', createOrderPageMap.changeCustomerBtn, () => {
      this.customerSearcher.onCustomerChange();
    });
  }

  /**
   * Loads cart summary with customer's carts & orders history.
   *
   * @private
   */
  _loadCartSummaryAfterChoosingCustomer() {
    $.ajax(this.$container.data('cart-summary-url'), {
      method: 'POST',
      data: {
        'id_customer': this.data.customer_id,
      },
      dataType: 'json'
    }).then((response) => {
      const checkoutHistory = {
        carts: response.carts,
        orders: response.orders
      };

      this._renderCheckoutHistory(checkoutHistory);
      //this._renderCheckoutHistory();
    });
  }

  /**
   * Renders previous Carts & Orders from customer history
   *
   * @param {Object} checkoutHistory
   *
   * @private
   */
  _renderCheckoutHistory(checkoutHistory) {
    const $cartsTable = $(createOrderPageMap.customerCartsTable);
    const $cartsTableRowTemplate = $($(createOrderPageMap.customerCartsTableRowTemplate).html());

    $cartsTable.find('tbody').empty();

    for (let key in checkoutHistory.carts) {
      if (!checkoutHistory.carts.hasOwnProperty(key)) {
        continue;
      }

      const cart = checkoutHistory.carts[key];
      const $template = $cartsTableRowTemplate.clone();

      $template.find('td:first').text(cart.id_cart);
      $template.find('td:nth-child(2)').text(cart.date_add);
      $template.find('td:nth-child(3)').text(cart.total_price);

      $cartsTable.find('tbody').append($template);
    }

    $(createOrderPageMap.customerCheckoutHistory).removeClass('d-none');
  }

  /**
   * Renders cart summary on the page
   *
   * @param {Object} cartSummary
   * @private
   */
  _renderCartSummary(cartSummary) {

  }
}
