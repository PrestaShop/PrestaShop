/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * Encapsulates selectors for "Create order" page
 */
export default {
  productCustomizationFieldTypeFile: 0,
  productCustomizationFieldTypeText: 1,

  orderCreationContainer: '#order-creation-container',
  requiredFieldMark: '.js-required-field-mark',
  cartInfoWrapper: '#js-cart-info-wrapper',

  // selectors related to customer block
  customerSearchInput: '#customer-search-input',
  customerSearchResultsBlock: '.js-customer-search-results',
  customerSearchResultTemplate: '#customer-search-result-template',
  customerSearchEmptyResultWarning: '#customer-search-empty-result-warn',
  customerSearchLoadingNotice: '#customer-search-loading-notice',
  customerAddBtn: '#customer-add-btn',
  changeCustomerBtn: '.js-change-customer-btn',
  customerSearchRow: '.js-search-customer-row',
  chooseCustomerBtn: '.js-choose-customer-btn',
  notSelectedCustomerSearchResults: '.js-customer-search-result:not(.border-success)',
  customerSearchResultName: '.js-customer-name',
  customerSearchResultEmail: '.js-customer-email',
  customerSearchResultGroups: '.js-customer-groups',
  customerSearchResultId: '.js-customer-id',
  customerSearchResultBirthday: '.js-customer-birthday',
  customerSearchResultCompany: '.js-customer-company',
  customerDetailsBtn: '.js-details-customer-btn',
  customerSearchResultColumn: '.js-customer-search-result-col',
  customerSearchBlock: '#customer-search-block',
  customerCartsTab: '.js-customer-carts-tab',
  customerOrdersTab: '.js-customer-orders-tab',
  customerCartsTable: '#customer-carts-table',
  customerCartsTableRowTemplate: '#customer-carts-table-row-template',
  customerCheckoutHistory: '#customer-checkout-history',
  customerOrdersTable: '#customer-orders-table',
  customerOrdersTableRowTemplate: '#customer-orders-table-row-template',
  cartRulesTable: '#cart-rules-table',
  cartRulesTableRowTemplate: '#cart-rules-table-row-template',
  useCartBtn: '.js-use-cart-btn',
  cartDetailsBtn: '.js-cart-details-btn',
  cartIdField: '.js-cart-id',
  cartDateField: '.js-cart-date',
  cartTotalField: '.js-cart-total',
  useOrderBtn: '.js-use-order-btn',
  orderDetailsBtn: '.js-order-details-btn',
  orderIdField: '.js-order-id',
  orderDateField: '.js-order-date',
  orderProductsField: '.js-order-products',
  orderTotalField: '.js-order-total-paid',
  orderPaymentMethod: '.js-order-payment-method',
  orderStatusField: '.js-order-status',
  emptyListRowTemplate: '#js-empty-list-row',
  loadingListRowTemplate: '#js-loading-list-row',
  emptyListRow: '.js-empty-row',

  // selectors related to cartRules block
  cartRulesBlock: '#cart-rules-block',
  cartRuleSearchInput: '#search-cart-rules-input',
  cartRulesSearchResultBox: '#search-cart-rules-result-box',
  cartRulesNotFoundTemplate: '#cart-rules-not-found-template',
  foundCartRuleTemplate: '#found-cart-rule-template',
  foundCartRuleListItem: '.js-found-cart-rule',
  cartRuleNameField: '.js-cart-rule-name',
  cartRuleDescriptionField: '.js-cart-rule-description',
  cartRuleValueField: '.js-cart-rule-value',
  cartRuleDeleteBtn: '.js-cart-rule-delete-btn',
  cartRuleErrorBlock: '#js-cart-rule-error-block',
  cartRuleErrorText: '#js-cart-rule-error-text',

  // selectors related to addresses block
  addressesBlock: '#addresses-block',
  deliveryAddressDetails: '#delivery-address-details',
  invoiceAddressDetails: '#invoice-address-details',
  deliveryAddressSelect: '#delivery-address-select',
  invoiceAddressSelect: '#invoice-address-select',
  addressSelect: '.js-address-select',
  addressesContent: '#addresses-content',
  addressesWarning: '#addresses-warning',
  deliveryAddressEditBtn: '#js-delivery-address-edit-btn',
  invoiceAddressEditBtn: '#js-invoice-address-edit-btn',
  addressAddBtn: '#js-add-address-btn',

  // selectors related to summary block
  summaryBlock: '#summary-block',
  summaryTotalProducts: '.js-total-products',
  summaryTotalDiscount: '.js-total-discounts',
  summaryTotalShipping: '.js-total-shipping',
  summaryTotalTaxes: '.js-total-taxes',
  summaryTotalWithoutTax: '.js-total-without-tax',
  summaryTotalWithTax: '.js-total-with-tax',
  placeOrderCartIdField: '.js-place-order-cart-id',
  processOrderLinkTag: '#js-process-order-link',
  orderMessageField: '#js-order-message-wrap textarea',
  sendProcessOrderEmailBtn: '#js-send-process-order-email-btn',
  summarySuccessAlertBlock: '#js-summary-success-block',
  summaryErrorAlertBlock: '#js-summary-error-block',
  summarySuccessAlertText: '#js-summary-success-block .alert-text',
  summaryErrorAlertText: '#js-summary-error-block .alert-text',

  // selectors related to shipping block
  shippingBlock: '#shipping-block',
  shippingForm: '.js-shipping-form',
  noCarrierBlock: '.js-no-carrier-block',
  deliveryOptionSelect: '#delivery-option-select',
  totalShippingField: '.js-total-shipping-tax-inc',
  freeShippingSwitch: '.js-free-shipping-switch',
  recycledPackagingSwitch: '.js-recycled-packaging-switch',
  recycledPackagingSwitchValue: '.js-recycled-packaging-switch:checked',
  isAGiftSwitch: '.js-is-gift-switch',
  isAGiftSwitchValue: '.js-is-gift-switch:checked',
  giftMessageField: '#cart_gift_message',

  // selectors related to cart block
  cartBlock: '#cart-block',
  cartCurrencySelect: '#js-cart-currency-select',
  cartLanguageSelect: '#js-cart-language-select',
  productSearch: '#product-search',
  combinationsSelect: '#combination-select',
  productResultBlock: '#product-search-results',
  productSelect: '#product-select',
  quantityInput: '#quantity-input',
  inStockCounter: '.js-in-stock-counter',
  combinationsTemplate: '#combinations-template',
  combinationsRow: '.js-combinations-row',
  productSelectRow: '.js-product-select-row',
  productCustomFieldsContainer: '#js-custom-fields-container',
  productCustomizationContainer: '#js-customization-container',
  productCustomFileTemplate: '#js-product-custom-file-template',
  productCustomTextTemplate: '#js-product-custom-text-template',
  productCustomInputLabel: '.js-product-custom-input-label',
  productCustomInput: '.js-product-custom-input',
  quantityRow: '.js-quantity-row',
  addToCartButton: '#add-product-to-cart-btn',
  productsTable: '#products-table',
  productsTableRowTemplate: '#products-table-row-template',
  productsTableGiftRowTemplate: '#products-table-gift-row-template',
  listedProductImageField: '.js-product-image',
  listedProductNameField: '.js-product-name',
  listedProductAttrField: '.js-product-attr',
  listedProductReferenceField: '.js-product-ref',
  listedProductUnitPriceInput: '.js-product-unit-input',
  listedProductQtyInput: '.js-product-qty-input',
  listedProductQtyStock: '.js-product-qty-stock',
  listedProductGiftQty: '.js-product-gift-qty',
  productTotalPriceField: '.js-product-total-price',
  listedProductCustomizedTextTemplate: '#js-table-product-customized-text-template',
  listedProductCustomizedFileTemplate: '#js-table-product-customized-file-template',
  listedProductCustomizationName: '.js-customization-name',
  listedProductCustomizationValue: '.js-customization-value',
  listedProductDefinition: '.js-product-definition-td',
  productRemoveBtn: '.js-product-remove-btn',
  productTaxWarning: '.js-tax-warning',
  noProductsFoundWarning: '.js-no-products-found',
  searchingProductsNotice: '.js-searching-products',
  productAddForm: '#js-add-product-form',
  cartErrorAlertBlock: '#js-cart-error-block',
  cartErrorAlertText: '#js-cart-error-block .alert-text',
  createOrderButton: '#create-order-button',
};
