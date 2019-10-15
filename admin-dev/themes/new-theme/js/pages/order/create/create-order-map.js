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
  orderCreationContainer: '#order_creation_container',

  // selectors related to customer block
  customerSearchInput: '#customer_search_input',
  customerSearchResultsBlock: '.js-customer-search-results',
  customerSearchResultTemplate: '#customer_search_result_template',
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
  customerSearchBlock: '#customer_search_block',
  customerCartsTable: '#customer_carts_table',
  customerCartsTableRowTemplate: '#customer_carts_table_row_template',
  customerCheckoutHistory: '#customer_checkout_history',
  customerOrdersTable: '#customer_orders_table',
  customerOrdersTableRowTemplate: '#customer_orders_table_row_template',
  vouchersTable: '#vouchers_table',
  vouchersTableRowTemplate: '#vouchers_table_row_template',

  // selectors related to cart block
  cartBlock: '#cart_block',

  // selectors related to vouchers block
  vouchersBlock: '#vouchers_block',

  // selectors related to addresses block
  addressesBlock: '#addresses_block',
  deliveryAddressDetails: '#delivery_address_details',
  invoiceAddressDetails: '#invoice_address_details',
  deliveryAddressSelect: '#delivery_address_select',
  invoiceAddressSelect: '#invoice_address_select',
  addressSelect: '.js-address-select',
  addressesContent: '#addresses_content',
  addressesWarning: '#addresses_warning',

  // selectors related to summary block
  summaryBlock: '#summary_block',

  // selectors related to shipping block
  shippingBlock: '#shipping_block',
  shippingForm: '.js-shipping-form',
  noCarrierBlock: '.js-no-carrier-block',
  deliveryOptionSelect: '#delivery_option_select',
  totalShippingField: '.js-total-shipping',
  freeShippingSwitch: '.js-free-shipping-switch',
};
