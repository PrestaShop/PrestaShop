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
      listenForCustomerSearch: () => {
        this.$container.on('input', createOrderPageMap.customerSearchInput, () => {
          this.customerSearcher.onCustomerSearch();
        });
      },
      listenForCustomerChooseForOrderCreation: () => {
        this.$container.on('click', createOrderPageMap.chooseCustomerBtn, (event) => {
          this.data.customer_id = this.customerSearcher.onCustomerChooseForOrderCreation(event);
        });

        this.$container.on('click', createOrderPageMap.changeCustomerBtn, () => {
          this.customerSearcher.onCustomerChange();
        });
      }
    };
  }

  _loadCartSummary() {
    $.ajax(this.$container.data('cart-summary-url'), {

    }).then(() => {

    });
  }
}
