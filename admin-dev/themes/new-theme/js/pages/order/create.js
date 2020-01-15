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
import CreateOrderPage from './create/create-order-page';

const $ = window.$;
let orderPageManager = null;

/**
 * proxy to allow other scripts within the page to trigger the search
 * @param string
 */
function searchCustomerByString(string) {
  if (orderPageManager !== null) {
    orderPageManager.search(string);
  } else {
    console.log('Error: Could not search customer as orderPageManager is null');
  }
}

/**
 * proxy to allow other scripts within the page to refresh addresses list
 */
function refreshAddressesList() {
  if (orderPageManager !== null) {
    orderPageManager.refreshAddressesList();
  } else {
    console.log('Error: Could not refresh addresses list as orderPageManager is null');
  }
}


$(document).ready(() => {
  orderPageManager = new CreateOrderPage();
});

export {searchCustomerByString}
export {refreshAddressesList}
