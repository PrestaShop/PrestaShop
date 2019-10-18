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

/**
 * Encapsulates selectors for "Create order" page
 */
export default {
  orderCreationContainer: '#order-creation-container',

  // selectors related to customer block
  customerSearchInput: '#customer-search-input',
  customerSearchResultsBlock: '.js-customer-search-results',
  customerSearchResultTemplate: '#customer-search-result-template',
  changeCustomerBtn: '.js-change-customer-btn',
  customerSearchRow: '.js-search-customer-row',
  chooseCustomerBtn: '.js-choose-customer-btn',
  notSelectedCustomerSearchResults: '.js-customer-search-result:not(.border-success)',
  customerSearchResultName: '.js-customer-name',
  customerSearchResultEmail: '.js-customer-email',
  customerSearchResultId: '.js-customer-id',
  customerSearchResultBirthday: '.js-customer-birthday',
  customerDetailsBtn: '.js-details-customer-btn',
  useCartBtn: '.js-use-cart-btn',
  useOrderBtn: '.js-use-order-btn',
  customerSearchResultColumn: '.js-customer-search-result-col',
  customerSearchBlock: '#customer-search-block',
  customerCartsTable: '#customer-carts-table',
  customerCartsTableRowTemplate: '#customer-carts-table-row-template',
  customerCheckoutHistory: '#customer-checkout-history',
  customerOrdersTable: '#customer-orders-table',
  customerOrdersTableRowTemplate: '#customer-orders-table-row-template',
  vouchersTable: '#vouchers-table',
  vouchersTableRowTemplate: '#vouchers-table-row-template',

  // selectors related to cart block
  cartBlock: '#cart-block',

  // selectors related to vouchers block
  vouchersBlock: '#vouchers-block',

  // selectors related to addresses block
  addressesBlock: '#addresses-block',
  deliveryAddressDetails: '#delivery-address-details',
  invoiceAddressDetails: '#invoice-address-details',
  deliveryAddressSelect: '#delivery-address-select',
  invoiceAddressSelect: '#invoice-address-select',
  addressSelect: '.js-address-select',
  addressesContent: '#addresses-content',
  addressesWarning: '#addresses-warning',

  // selectors related to summary block
  summaryBlock: '#summary-block',

  // selectors related to shipping block
  shippingBlock: '#shipping-block',
  shippingForm: '.js-shipping-form',
  noCarrierBlock: '.js-no-carrier-block',
  deliveryOptionSelect: '#delivery-option-select',
  totalShippingField: '.js-total-shipping',
  freeShippingSwitch: '.js-free-shipping-switch',
};
