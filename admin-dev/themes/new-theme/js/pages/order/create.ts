/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import CreateOrderPage from './create/create-order-page';

const {$} = window;
let orderPageManager: CreateOrderPage | null = null;

/**
 * proxy to allow other scripts within the page to trigger the search
 * @param string
 */
function searchCustomerByString(string: string): void {
  if (orderPageManager !== null) {
    orderPageManager.search(string);
  } else {
    console.log('Error: Could not search customer as orderPageManager is null');
  }
}

/**
 * proxy to allow other scripts within the page to refresh addresses list
 */
function refreshAddressesList(refreshCartAddresses: boolean): void {
  if (orderPageManager !== null) {
    orderPageManager.refreshAddressesList(refreshCartAddresses);
  } else {
    console.log('Error: Could not refresh addresses list as orderPageManager is null');
  }
}

/**
 * proxy to allow other scripts within the Create Order page to refresh cart
 */
function refreshCart(): void {
  if (orderPageManager === null) {
    console.log('Error: Could not refresh addresses list as orderPageManager is null');
    return;
  }
  orderPageManager.refreshCart();
}

$(() => {
  orderPageManager = new CreateOrderPage();
});

export {searchCustomerByString};
export {refreshAddressesList};
export {refreshCart};
