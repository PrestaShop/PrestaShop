window["order_create"] =
/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// identity function for calling harmony imports with the correct context
/******/ 	__webpack_require__.i = function(value) { return value; };
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 508);
/******/ })
/************************************************************************/
/******/ ({

/***/ 0:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

exports.default = function (instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
};

/***/ }),

/***/ 1:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

var _defineProperty = __webpack_require__(19);

var _defineProperty2 = _interopRequireDefault(_defineProperty);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = function () {
  function defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      (0, _defineProperty2.default)(target, descriptor.key, descriptor);
    }
  }

  return function (Constructor, protoProps, staticProps) {
    if (protoProps) defineProperties(Constructor.prototype, protoProps);
    if (staticProps) defineProperties(Constructor, staticProps);
    return Constructor;
  };
}();

/***/ }),

/***/ 10:
/***/ (function(module, exports, __webpack_require__) {

var dP         = __webpack_require__(6)
  , createDesc = __webpack_require__(12);
module.exports = __webpack_require__(2) ? function(object, key, value){
  return dP.f(object, key, createDesc(1, value));
} : function(object, key, value){
  object[key] = value;
  return object;
};

/***/ }),

/***/ 106:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Encapsulates selectors for "Create order" page
 */
exports.default = {
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
  customerAddBtn: '#customer-add-btn',
  changeCustomerBtn: '.js-change-customer-btn',
  customerSearchRow: '.js-search-customer-row',
  chooseCustomerBtn: '.js-choose-customer-btn',
  notSelectedCustomerSearchResults: '.js-customer-search-result:not(.border-success)',
  customerSearchResultName: '.js-customer-name',
  customerSearchResultEmail: '.js-customer-email',
  customerSearchResultId: '.js-customer-id',
  customerSearchResultBirthday: '.js-customer-birthday',
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
  totalShippingField: '.js-total-shipping',
  freeShippingSwitch: '.js-free-shipping-switch',

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
  listedProductImageField: '.js-product-image',
  listedProductNameField: '.js-product-name',
  listedProductAttrField: '.js-product-attr',
  listedProductReferenceField: '.js-product-ref',
  listedProductUnitPriceInput: '.js-product-unit-input',
  listedProductQtyInput: '.js-product-qty-input',
  productTotalPriceField: '.js-product-total-price',
  listedProductCustomizedTextTemplate: '#js-table-product-customized-text-template',
  listedProductCustomizedFileTemplate: '#js-table-product-customized-file-template',
  listedProductCustomizationName: '.js-customization-name',
  listedProductCustomizationValue: '.js-customization-value',
  listedProductDefinition: '.js-product-definition-td',
  productRemoveBtn: '.js-product-remove-btn',
  productTaxWarning: '.js-tax-warning',
  noProductsFoundWarning: '.js-no-products-found',
  productAddForm: '#js-add-product-form',
  cartErrorAlertBlock: '#js-cart-error-block',
  cartErrorAlertText: '#js-cart-error-block .alert-text'
};

/***/ }),

/***/ 11:
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__(4);
module.exports = function(it){
  if(!isObject(it))throw TypeError(it + ' is not an object!');
  return it;
};

/***/ }),

/***/ 12:
/***/ (function(module, exports) {

module.exports = function(bitmap, value){
  return {
    enumerable  : !(bitmap & 1),
    configurable: !(bitmap & 2),
    writable    : !(bitmap & 4),
    value       : value
  };
};

/***/ }),

/***/ 13:
/***/ (function(module, exports, __webpack_require__) {

// 7.1.1 ToPrimitive(input [, PreferredType])
var isObject = __webpack_require__(4);
// instead of the ES6 spec version, we didn't implement @@toPrimitive case
// and the second argument - flag - preferred type is a string
module.exports = function(it, S){
  if(!isObject(it))return it;
  var fn, val;
  if(S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it)))return val;
  if(typeof (fn = it.valueOf) == 'function' && !isObject(val = fn.call(it)))return val;
  if(!S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it)))return val;
  throw TypeError("Can't convert object to primitive value");
};

/***/ }),

/***/ 133:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Encapsulates js events used in create order page
 */
exports.default = {
  // when customer search action is done
  customerSearched: 'OrderCreateCustomerSearched',
  // when new customer is selected
  customerSelected: 'OrderCreateCustomerSelected',
  // when no customers found by search
  customersNotFound: 'OrderCreateSearchCustomerNotFound',
  // when new cart is loaded,
  //  no matter if its empty, selected from carts list or duplicated by order.
  cartLoaded: 'OrderCreateCartLoaded',
  // when cart currency has been changed
  cartCurrencyChanged: 'OrderCreateCartCurrencyChanged',
  // when cart currency changing fails
  cartCurrencyChangeFailed: 'OrderCreateCartCurrencyChangeFailed',
  // when cart language has been changed
  cartLanguageChanged: 'OrderCreateCartLanguageChanged',
  // when cart addresses information has been changed
  cartAddressesChanged: 'OrderCreateCartAddressesChanged',
  // when cart delivery option has been changed
  cartDeliveryOptionChanged: 'OrderCreateCartDeliveryOptionChanged',
  // when cart free shipping value has been changed
  cartFreeShippingSet: 'OrderCreateCartFreeShippingSet',
  // when cart rules search action is done
  cartRuleSearched: 'OrderCreateCartRuleSearched',
  // when cart rule is removed from cart
  cartRuleRemoved: 'OrderCreateCartRuleRemoved',
  // when cart rule is added to cart
  cartRuleAdded: 'OrderCreateCartRuleAdded',
  // when cart rule cannot be added to cart
  cartRuleFailedToAdd: 'OrderCreateCartRuleFailedToAdd',
  // when product search action is done
  productSearched: 'OrderCreateProductSearched',
  // when product is added to cart
  productAddedToCart: 'OrderCreateProductAddedToCart',
  // when adding product to cart fails
  productAddToCartFailed: 'OrderCreateProductAddToCartFailed',
  // when product is removed from cart
  productRemovedFromCart: 'OrderCreateProductRemovedFromCart',
  // when product in cart price has been changed
  productPriceChanged: 'OrderCreateProductPriceChanged',
  // when product quantity in cart has been changed
  productQtyChanged: 'OrderCreateProductQtyChanged',
  // when changing product quantity in cart failed
  productQtyChangeFailed: 'OrderCreateProductQtyChangeFailed',
  // when order process email has been sent to customer
  processOrderEmailSent: 'OrderCreateProcessOrderEmailSent',
  // when order process email sending failed
  processOrderEmailFailed: 'OrderCreateProcessOrderEmailFailed'
};

/***/ }),

/***/ 15:
/***/ (function(module, exports, __webpack_require__) {

// optional / simple context binding
var aFunction = __webpack_require__(18);
module.exports = function(fn, that, length){
  aFunction(fn);
  if(that === undefined)return fn;
  switch(length){
    case 1: return function(a){
      return fn.call(that, a);
    };
    case 2: return function(a, b){
      return fn.call(that, a, b);
    };
    case 3: return function(a, b, c){
      return fn.call(that, a, b, c);
    };
  }
  return function(/* ...args */){
    return fn.apply(that, arguments);
  };
};

/***/ }),

/***/ 16:
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__(4)
  , document = __webpack_require__(5).document
  // in old IE typeof document.createElement is 'object'
  , is = isObject(document) && isObject(document.createElement);
module.exports = function(it){
  return is ? document.createElement(it) : {};
};

/***/ }),

/***/ 161:
/***/ (function(module, exports) {

module.exports = {"base_url":"","routes":{"admin_product_form":{"tokens":[["variable","/","\\d+","id"],["text","/sell/catalog/products"]],"defaults":[],"requirements":{"id":"\\d+"},"hosttokens":[],"methods":["GET","POST"],"schemes":[]},"admin_products_search":{"tokens":[["text","/sell/catalog/products/search"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_cart_rules_search":{"tokens":[["text","/sell/catalog/cart-rules/search"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_customers_view":{"tokens":[["text","/view"],["variable","/","\\d+","customerId"],["text","/sell/customers"]],"defaults":[],"requirements":{"customerId":"\\d+"},"hosttokens":[],"methods":["GET","POST"],"schemes":[]},"admin_customers_search":{"tokens":[["text","/sell/customers/search"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_customers_carts":{"tokens":[["text","/carts"],["variable","/","\\d+","customerId"],["text","/sell/customers"]],"defaults":[],"requirements":{"customerId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_customers_orders":{"tokens":[["text","/orders"],["variable","/","\\d+","customerId"],["text","/sell/customers"]],"defaults":[],"requirements":{"customerId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_addresses_create":{"tokens":[["text","/sell/addresses/new"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["GET","POST"],"schemes":[]},"admin_addresses_edit":{"tokens":[["text","/edit"],["variable","/","\\d+","addressId"],["text","/sell/addresses"]],"defaults":[],"requirements":{"addressId":"\\d+"},"hosttokens":[],"methods":["GET","POST"],"schemes":[]},"admin_carts_view":{"tokens":[["text","/view"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_carts_info":{"tokens":[["text","/info"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_carts_create":{"tokens":[["text","/sell/orders/carts/new"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_edit_addresses":{"tokens":[["text","/addresses"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_edit_carrier":{"tokens":[["text","/carrier"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_edit_currency":{"tokens":[["text","/currency"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_edit_language":{"tokens":[["text","/language"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_set_free_shipping":{"tokens":[["text","/rules/free-shipping"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_add_cart_rule":{"tokens":[["text","/cart-rules"],["variable","/","[^/]++","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_delete_cart_rule":{"tokens":[["text","/delete"],["variable","/","[^/]++","cartRuleId"],["text","/cart-rules"],["variable","/","[^/]++","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_add_product":{"tokens":[["text","/products"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_edit_product_price":{"tokens":[["text","/price"],["variable","/","\\d+","productId"],["text","/products"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+","productId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_edit_product_quantity":{"tokens":[["text","/quantity"],["variable","/","\\d+","productId"],["text","/products"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+","productId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_carts_delete_product":{"tokens":[["text","/delete-product"],["variable","/","\\d+","cartId"],["text","/sell/orders/carts"]],"defaults":[],"requirements":{"cartId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_place":{"tokens":[["text","/sell/orders/orders/place"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_view":{"tokens":[["text","/view"],["variable","/","\\d+","orderId"],["text","/sell/orders/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET","POST"],"schemes":[]},"admin_orders_duplicate_cart":{"tokens":[["text","/duplicate-cart"],["variable","/","\\d+","orderId"],["text","/sell/orders/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_update_product":{"tokens":[["variable","/","\\d+","orderDetailId"],["text","/products"],["variable","/","\\d+","orderId"],["text","/sell/orders/orders"]],"defaults":[],"requirements":{"orderId":"\\d+","orderDetailId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_partial_refund":{"tokens":[["text","/partial-refund"],["variable","/","\\d+","orderId"],["text","/sell/orders/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_standard_refund":{"tokens":[["text","/standard-refund"],["variable","/","\\d+","orderId"],["text","/sell/orders/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_return_product":{"tokens":[["text","/return-product"],["variable","/","\\d+","orderId"],["text","/sell/orders/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_add_product":{"tokens":[["text","/products"],["variable","/","\\d+","orderId"],["text","/sell/orders/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_delete_product":{"tokens":[["text","/delete"],["variable","/","\\d+","orderDetailId"],["text","/products"],["variable","/","\\d+","orderId"],["text","/sell/orders/orders"]],"defaults":[],"requirements":{"orderId":"\\d+","orderDetailId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]},"admin_orders_get_prices":{"tokens":[["text","/prices"],["variable","/","\\d+","orderId"],["text","/sell/orders/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_orders_get_paginated_products":{"tokens":[["text","/products"],["variable","/","\\d+","orderId"],["text","/sell/orders/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_orders_get_invoices":{"tokens":[["text","/invoices"],["variable","/","\\d+","orderId"],["text","/sell/orders/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"admin_orders_cancellation":{"tokens":[["text","/cancellation"],["variable","/","\\d+","orderId"],["text","/sell/orders/orders"]],"defaults":[],"requirements":{"orderId":"\\d+"},"hosttokens":[],"methods":["POST"],"schemes":[]}},"prefix":"","host":"localhost","port":"","scheme":"http","locale":[]}

/***/ }),

/***/ 167:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

var _defineProperty = __webpack_require__(19);

var _defineProperty2 = _interopRequireDefault(_defineProperty);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = function (obj, key, value) {
  if (key in obj) {
    (0, _defineProperty2.default)(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
};

/***/ }),

/***/ 17:
/***/ (function(module, exports, __webpack_require__) {

module.exports = !__webpack_require__(2) && !__webpack_require__(7)(function(){
  return Object.defineProperty(__webpack_require__(16)('div'), 'a', {get: function(){ return 7; }}).a != 7;
});

/***/ }),

/***/ 178:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
var _extends=Object.assign||function(a){for(var b,c=1;c<arguments.length;c++)for(var d in b=arguments[c],b)Object.prototype.hasOwnProperty.call(b,d)&&(a[d]=b[d]);return a},_typeof='function'==typeof Symbol&&'symbol'==typeof Symbol.iterator?function(a){return typeof a}:function(a){return a&&'function'==typeof Symbol&&a.constructor===Symbol&&a!==Symbol.prototype?'symbol':typeof a};function _classCallCheck(a,b){if(!(a instanceof b))throw new TypeError('Cannot call a class as a function')}var Routing=function a(){var b=this;_classCallCheck(this,a),this.setRoutes=function(a){b.routesRouting=a||[]},this.getRoutes=function(){return b.routesRouting},this.setBaseUrl=function(a){b.contextRouting.base_url=a},this.getBaseUrl=function(){return b.contextRouting.base_url},this.setPrefix=function(a){b.contextRouting.prefix=a},this.setScheme=function(a){b.contextRouting.scheme=a},this.getScheme=function(){return b.contextRouting.scheme},this.setHost=function(a){b.contextRouting.host=a},this.getHost=function(){return b.contextRouting.host},this.buildQueryParams=function(a,c,d){var e=new RegExp(/\[]$/);c instanceof Array?c.forEach(function(c,f){e.test(a)?d(a,c):b.buildQueryParams(a+'['+('object'===('undefined'==typeof c?'undefined':_typeof(c))?f:'')+']',c,d)}):'object'===('undefined'==typeof c?'undefined':_typeof(c))?Object.keys(c).forEach(function(e){return b.buildQueryParams(a+'['+e+']',c[e],d)}):d(a,c)},this.getRoute=function(a){var c=b.contextRouting.prefix+a;if(!!b.routesRouting[c])return b.routesRouting[c];else if(!b.routesRouting[a])throw new Error('The route "'+a+'" does not exist.');return b.routesRouting[a]},this.generate=function(a,c,d){var e=b.getRoute(a),f=c||{},g=_extends({},f),h='_scheme',i='',j=!0,k='';if((e.tokens||[]).forEach(function(b){if('text'===b[0])return i=b[1]+i,void(j=!1);if('variable'===b[0]){var c=(e.defaults||{})[b[3]];if(!1==j||!c||(f||{})[b[3]]&&f[b[3]]!==e.defaults[b[3]]){var d;if((f||{})[b[3]])d=f[b[3]],delete g[b[3]];else if(c)d=e.defaults[b[3]];else{if(j)return;throw new Error('The route "'+a+'" requires the parameter "'+b[3]+'".')}var h=!0===d||!1===d||''===d;if(!h||!j){var k=encodeURIComponent(d).replace(/%2F/g,'/');'null'===k&&null===d&&(k=''),i=b[1]+k+i}j=!1}else c&&delete g[b[3]];return}throw new Error('The token type "'+b[0]+'" is not supported.')}),''==i&&(i='/'),(e.hosttokens||[]).forEach(function(a){var b;return'text'===a[0]?void(k=a[1]+k):void('variable'===a[0]&&((f||{})[a[3]]?(b=f[a[3]],delete g[a[3]]):e.defaults[a[3]]&&(b=e.defaults[a[3]]),k=a[1]+b+k))}),i=b.contextRouting.base_url+i,e.requirements[h]&&b.getScheme()!==e.requirements[h]?i=e.requirements[h]+'://'+(k||b.getHost())+i:k&&b.getHost()!==k?i=b.getScheme()+'://'+k+i:!0===d&&(i=b.getScheme()+'://'+b.getHost()+i),0<Object.keys(g).length){var l=[],m=function(a,b){var c=b;c='function'==typeof c?c():c,c=null===c?'':c,l.push(encodeURIComponent(a)+'='+encodeURIComponent(c))};Object.keys(g).forEach(function(a){return b.buildQueryParams(a,g[a],m)}),i=i+'?'+l.join('&').replace(/%20/g,'+')}return i},this.setData=function(a){b.setBaseUrl(a.base_url),b.setRoutes(a.routes),'prefix'in a&&b.setPrefix(a.prefix),b.setHost(a.host),b.setScheme(a.scheme)},this.contextRouting={base_url:'',prefix:'',host:'',scheme:''}};module.exports=new Routing;

/***/ }),

/***/ 18:
/***/ (function(module, exports) {

module.exports = function(it){
  if(typeof it != 'function')throw TypeError(it + ' is not a function!');
  return it;
};

/***/ }),

/***/ 19:
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(20), __esModule: true };

/***/ }),

/***/ 193:
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(205), __esModule: true };

/***/ }),

/***/ 196:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _stringify = __webpack_require__(583);

var _stringify2 = _interopRequireDefault(_stringify);

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _router = __webpack_require__(73);

var _router2 = _interopRequireDefault(_router);

var _eventEmitter = __webpack_require__(36);

var _eventMap = __webpack_require__(133);

var _eventMap2 = _interopRequireDefault(_eventMap);

var _createOrderMap = __webpack_require__(106);

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

var $ = window.$;

/**
 * Provides ajax calls for cart editing actions
 * Each method emits an event with updated cart information after success.
 */

var CartEditor = function () {
  function CartEditor() {
    (0, _classCallCheck3.default)(this, CartEditor);

    this.router = new _router2.default();
  }

  /**
   * Changes cart addresses
   *
   * @param {Number} cartId
   * @param {Object} addresses
   */


  (0, _createClass3.default)(CartEditor, [{
    key: 'changeCartAddresses',
    value: function changeCartAddresses(cartId, addresses) {
      $.post(this.router.generate('admin_carts_edit_addresses', { cartId: cartId }), addresses).then(function (cartInfo) {
        return _eventEmitter.EventEmitter.emit(_eventMap2.default.cartAddressesChanged, cartInfo);
      }).catch(function (response) {
        return showErrorMessage(response.responseJSON.message);
      });
    }

    /**
     * Modifies cart delivery option
     *
     * @param {Number} cartId
     * @param {Number} value
     */

  }, {
    key: 'changeDeliveryOption',
    value: function changeDeliveryOption(cartId, value) {
      $.post(this.router.generate('admin_carts_edit_carrier', { cartId: cartId }), {
        carrierId: value
      }).then(function (cartInfo) {
        return _eventEmitter.EventEmitter.emit(_eventMap2.default.cartDeliveryOptionChanged, cartInfo);
      }).catch(function (response) {
        return showErrorMessage(response.responseJSON.message);
      });
    }

    /**
     * Changes cart free shipping value
     *
     * @param {Number} cartId
     * @param {Boolean} value
     */

  }, {
    key: 'setFreeShipping',
    value: function setFreeShipping(cartId, value) {
      $.post(this.router.generate('admin_carts_set_free_shipping', { cartId: cartId }), {
        freeShipping: value
      }).then(function (cartInfo) {
        return _eventEmitter.EventEmitter.emit(_eventMap2.default.cartFreeShippingSet, cartInfo);
      }).catch(function (response) {
        return showErrorMessage(response.responseJSON.message);
      });
    }

    /**
     * Adds cart rule to cart
     *
     * @param {Number} cartRuleId
     * @param {Number} cartId
     */

  }, {
    key: 'addCartRuleToCart',
    value: function addCartRuleToCart(cartRuleId, cartId) {
      $.post(this.router.generate('admin_carts_add_cart_rule', { cartId: cartId }), {
        cartRuleId: cartRuleId
      }).then(function (cartInfo) {
        return _eventEmitter.EventEmitter.emit(_eventMap2.default.cartRuleAdded, cartInfo);
      }).catch(function (response) {
        return _eventEmitter.EventEmitter.emit(_eventMap2.default.cartRuleFailedToAdd, response.responseJSON.message);
      });
    }

    /**
     * Removes cart rule from cart
     *
     * @param {Number} cartRuleId
     * @param {Number} cartId
     */

  }, {
    key: 'removeCartRuleFromCart',
    value: function removeCartRuleFromCart(cartRuleId, cartId) {
      $.post(this.router.generate('admin_carts_delete_cart_rule', {
        cartId: cartId,
        cartRuleId: cartRuleId
      })).then(function (cartInfo) {
        return _eventEmitter.EventEmitter.emit(_eventMap2.default.cartRuleRemoved, cartInfo);
      }).catch(function (response) {
        return showErrorMessage(response.responseJSON.message);
      });
    }

    /**
     * Adds product to cart
     *
     * @param {Number} cartId
     * @param {Object} data
     */

  }, {
    key: 'addProduct',
    value: function addProduct(cartId, data) {
      var fileSizeHeader = '';
      if (!$.isEmptyObject(data.fileSizes)) {
        fileSizeHeader = (0, _stringify2.default)(data.fileSizes);
      }

      $.ajax(this.router.generate('admin_carts_add_product', { cartId: cartId }), {
        headers: {
          // Adds custom headers with submitted file sizes, to track if all files reached server side.
          'file-sizes': fileSizeHeader
        },
        method: 'POST',
        data: data.product,
        processData: false,
        contentType: false
      }).then(function (cartInfo) {
        return _eventEmitter.EventEmitter.emit(_eventMap2.default.productAddedToCart, cartInfo);
      }).catch(function (response) {
        return _eventEmitter.EventEmitter.emit(_eventMap2.default.productAddToCartFailed, response.responseJSON.message);
      });
    }

    /**
     * Removes product from cart
     *
     * @param {Number} cartId
     * @param {Object} product
     */

  }, {
    key: 'removeProductFromCart',
    value: function removeProductFromCart(cartId, product) {
      $.post(this.router.generate('admin_carts_delete_product', { cartId: cartId }), {
        productId: product.productId,
        attributeId: product.attributeId,
        customizationId: product.customizationId
      }).then(function (cartInfo) {
        return _eventEmitter.EventEmitter.emit(_eventMap2.default.productRemovedFromCart, cartInfo);
      }).catch(function (response) {
        return showErrorMessage(response.responseJSON.message);
      });
    }

    /**
     * Changes product price in cart
     *
     * @param {Number} cartId
     * @param {Number} customerId
     * @param {Object} product the updated product
     */

  }, {
    key: 'changeProductPrice',
    value: function changeProductPrice(cartId, customerId, product) {
      $.post(this.router.generate('admin_carts_edit_product_price', {
        cartId: cartId,
        productId: product.productId,
        productAttributeId: product.attributeId
      }), {
        newPrice: product.price,
        customerId: customerId
      }).then(function (cartInfo) {
        return _eventEmitter.EventEmitter.emit(_eventMap2.default.productPriceChanged, cartInfo);
      }).catch(function (response) {
        return showErrorMessage(response.responseJSON.message);
      });
    }

    /**
     * Updates product quantity in cart
     *
     * @param cartId
     * @param product
     */

  }, {
    key: 'changeProductQty',
    value: function changeProductQty(cartId, product) {
      $.post(this.router.generate('admin_carts_edit_product_quantity', {
        cartId: cartId,
        productId: product.productId
      }), {
        newQty: product.newQty,
        attributeId: product.attributeId,
        customizationId: product.customizationId
      }).then(function (cartInfo) {
        return _eventEmitter.EventEmitter.emit(_eventMap2.default.productQtyChanged, cartInfo);
      }).catch(function (response) {
        return _eventEmitter.EventEmitter.emit(_eventMap2.default.productQtyChangeFailed, response);
      });
    }

    /**
     * Changes cart currency
     *
     * @param {Number} cartId
     * @param {Number} currencyId
     */

  }, {
    key: 'changeCartCurrency',
    value: function changeCartCurrency(cartId, currencyId) {
      $(_createOrderMap2.default.cartCurrencySelect).data('selectedCurrencyId', currencyId);

      $.post(this.router.generate('admin_carts_edit_currency', { cartId: cartId }), {
        currencyId: currencyId
      }).then(function (cartInfo) {
        return _eventEmitter.EventEmitter.emit(_eventMap2.default.cartCurrencyChanged, cartInfo);
      }).catch(function (response) {
        return _eventEmitter.EventEmitter.emit(_eventMap2.default.cartCurrencyChangeFailed, response);
      });
    }

    /**
     * Changes cart language
     *
     * @param {Number} cartId
     * @param {Number} languageId
     */

  }, {
    key: 'changeCartLanguage',
    value: function changeCartLanguage(cartId, languageId) {
      $.post(this.router.generate('admin_carts_edit_language', { cartId: cartId }), {
        languageId: languageId
      }).then(function (cartInfo) {
        return _eventEmitter.EventEmitter.emit(_eventMap2.default.cartLanguageChanged, cartInfo);
      }).catch(function (response) {
        return showErrorMessage(response.responseJSON.message);
      });
    }
  }]);
  return CartEditor;
}();

exports.default = CartEditor;

/***/ }),

/***/ 197:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _createOrderMap = __webpack_require__(106);

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

var _createOrderPage = __webpack_require__(220);

var _createOrderPage2 = _interopRequireDefault(_createOrderPage);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

var $ = window.$;

/**
 * Responsible for summary block rendering
 */

var SummaryRenderer = function () {
  function SummaryRenderer() {
    (0, _classCallCheck3.default)(this, SummaryRenderer);

    this.$totalProducts = $(_createOrderMap2.default.summaryTotalProducts);
    this.$totalDiscount = $(_createOrderMap2.default.summaryTotalDiscount);
    this.$totalShipping = $(_createOrderMap2.default.totalShippingField);
    this.$totalTaxes = $(_createOrderMap2.default.summaryTotalTaxes);
    this.$totalWithoutTax = $(_createOrderMap2.default.summaryTotalWithoutTax);
    this.$totalWithTax = $(_createOrderMap2.default.summaryTotalWithTax);
    this.$placeOrderCartIdField = $(_createOrderMap2.default.placeOrderCartIdField);
    this.$orderMessageField = $(_createOrderMap2.default.orderMessageField);
    this.$processOrderLink = $(_createOrderMap2.default.processOrderLinkTag);
  }

  /**
   * Renders summary block
   *
   * @param {Object} cartInfo
   */


  (0, _createClass3.default)(SummaryRenderer, [{
    key: 'render',
    value: function render(cartInfo) {
      this._cleanSummary();
      var noProducts = cartInfo.products.length === 0;
      var noShippingOptions = cartInfo.shipping === null;
      var addressesAreValid = _createOrderPage2.default.validateSelectedAddresses(cartInfo.addresses);

      if (noProducts || noShippingOptions || !addressesAreValid) {
        this._hideSummaryBlock();

        return;
      }
      var cartSummary = cartInfo.summary;

      this.$totalProducts.text(cartSummary.totalProductsPrice);
      this.$totalDiscount.text(cartSummary.totalDiscount);
      this.$totalShipping.text(cartSummary.totalShippingPrice);
      this.$totalTaxes.text(cartSummary.totalTaxes);
      this.$totalWithoutTax.text(cartSummary.totalPriceWithoutTaxes);
      this.$totalWithTax.text(cartSummary.totalPriceWithTaxes);
      this.$processOrderLink.prop('href', cartSummary.processOrderLink);
      this.$orderMessageField.text(cartSummary.orderMessage);
      this.$placeOrderCartIdField.val(cartInfo.cartId);

      this._showSummaryBlock();
    }

    /**
     * Renders summary success message
     *
     * @param message
     */

  }, {
    key: 'renderSuccessMessage',
    value: function renderSuccessMessage(message) {
      $(_createOrderMap2.default.summarySuccessAlertText).text(message);
      this._showSummarySuccessAlertBlock();
    }

    /**
     * Renders summary error message
     *
     * @param message
     */

  }, {
    key: 'renderErrorMessage',
    value: function renderErrorMessage(message) {
      $(_createOrderMap2.default.summaryErrorAlertText).text(message);
      this._showSummaryErrorAlertBlock();
    }

    /**
     * Cleans content of success/error summary alerts and hides them
     */

  }, {
    key: 'cleanAlerts',
    value: function cleanAlerts() {
      $(_createOrderMap2.default.summarySuccessAlertText).text('');
      $(_createOrderMap2.default.summaryErrorAlertText).text('');
      this._hideSummarySuccessAlertBlock();
      this._hideSummaryErrorAlertBlock();
    }

    /**
     * Shows summary block
     *
     * @private
     */

  }, {
    key: '_showSummaryBlock',
    value: function _showSummaryBlock() {
      $(_createOrderMap2.default.summaryBlock).removeClass('d-none');
    }

    /**
     * Hides summary block
     *
     * @private
     */

  }, {
    key: '_hideSummaryBlock',
    value: function _hideSummaryBlock() {
      $(_createOrderMap2.default.summaryBlock).addClass('d-none');
    }

    /**
     * Shows error alert of summary block
     *
     * @private
     */

  }, {
    key: '_showSummaryErrorAlertBlock',
    value: function _showSummaryErrorAlertBlock() {
      $(_createOrderMap2.default.summaryErrorAlertBlock).removeClass('d-none');
    }

    /**
     * Hides error alert of summary block
     *
     * @private
     */

  }, {
    key: '_hideSummaryErrorAlertBlock',
    value: function _hideSummaryErrorAlertBlock() {
      $(_createOrderMap2.default.summaryErrorAlertBlock).addClass('d-none');
    }

    /**
     * Shows success alert of summary block
     *
     * @private
     */

  }, {
    key: '_showSummarySuccessAlertBlock',
    value: function _showSummarySuccessAlertBlock() {
      $(_createOrderMap2.default.summarySuccessAlertBlock).removeClass('d-none');
    }

    /**
     * Hides success alert of summary block
     *
     * @private
     */

  }, {
    key: '_hideSummarySuccessAlertBlock',
    value: function _hideSummarySuccessAlertBlock() {
      $(_createOrderMap2.default.summarySuccessAlertBlock).addClass('d-none');
    }

    /**
     * Empties cart summary fields
     */

  }, {
    key: '_cleanSummary',
    value: function _cleanSummary() {
      this.$totalProducts.empty();
      this.$totalDiscount.empty();
      this.$totalShipping.empty();
      this.$totalTaxes.empty();
      this.$totalWithoutTax.empty();
      this.$totalWithTax.empty();
      this.$processOrderLink.prop('href', '');
      this.$orderMessageField.text('');
      this.cleanAlerts();
    }
  }]);
  return SummaryRenderer;
}();

exports.default = SummaryRenderer;

/***/ }),

/***/ 2:
/***/ (function(module, exports, __webpack_require__) {

// Thank's IE8 for his funny defineProperty
module.exports = !__webpack_require__(7)(function(){
  return Object.defineProperty({}, 'a', {get: function(){ return 7; }}).a != 7;
});

/***/ }),

/***/ 20:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(21);
var $Object = __webpack_require__(3).Object;
module.exports = function defineProperty(it, key, desc){
  return $Object.defineProperty(it, key, desc);
};

/***/ }),

/***/ 205:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(207);
module.exports = __webpack_require__(3).Object.values;

/***/ }),

/***/ 206:
/***/ (function(module, exports, __webpack_require__) {

var getKeys   = __webpack_require__(34)
  , toIObject = __webpack_require__(22)
  , isEnum    = __webpack_require__(52).f;
module.exports = function(isEntries){
  return function(it){
    var O      = toIObject(it)
      , keys   = getKeys(O)
      , length = keys.length
      , i      = 0
      , result = []
      , key;
    while(length > i)if(isEnum.call(O, key = keys[i++])){
      result.push(isEntries ? [key, O[key]] : O[key]);
    } return result;
  };
};

/***/ }),

/***/ 207:
/***/ (function(module, exports, __webpack_require__) {

// https://github.com/tc39/proposal-object-values-entries
var $export = __webpack_require__(8)
  , $values = __webpack_require__(206)(false);

$export($export.S, 'Object', {
  values: function values(it){
    return $values(it);
  }
});

/***/ }),

/***/ 21:
/***/ (function(module, exports, __webpack_require__) {

var $export = __webpack_require__(8);
// 19.1.2.4 / 15.2.3.6 Object.defineProperty(O, P, Attributes)
$export($export.S + $export.F * !__webpack_require__(2), 'Object', {defineProperty: __webpack_require__(6).f});

/***/ }),

/***/ 22:
/***/ (function(module, exports, __webpack_require__) {

// to indexed object, toObject with fallback for non-array-like ES3 strings
var IObject = __webpack_require__(51)
  , defined = __webpack_require__(38);
module.exports = function(it){
  return IObject(defined(it));
};

/***/ }),

/***/ 220:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _createOrderMap = __webpack_require__(106);

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

var _customerManager = __webpack_require__(512);

var _customerManager2 = _interopRequireDefault(_customerManager);

var _shippingRenderer = __webpack_require__(227);

var _shippingRenderer2 = _interopRequireDefault(_shippingRenderer);

var _cartProvider = __webpack_require__(510);

var _cartProvider2 = _interopRequireDefault(_cartProvider);

var _addressesRenderer = __webpack_require__(509);

var _addressesRenderer2 = _interopRequireDefault(_addressesRenderer);

var _cartRulesRenderer = __webpack_require__(225);

var _cartRulesRenderer2 = _interopRequireDefault(_cartRulesRenderer);

var _router = __webpack_require__(73);

var _router2 = _interopRequireDefault(_router);

var _eventEmitter = __webpack_require__(36);

var _cartEditor = __webpack_require__(196);

var _cartEditor2 = _interopRequireDefault(_cartEditor);

var _eventMap = __webpack_require__(133);

var _eventMap2 = _interopRequireDefault(_eventMap);

var _cartRuleManager = __webpack_require__(511);

var _cartRuleManager2 = _interopRequireDefault(_cartRuleManager);

var _productManager = __webpack_require__(514);

var _productManager2 = _interopRequireDefault(_productManager);

var _productRenderer = __webpack_require__(226);

var _productRenderer2 = _interopRequireDefault(_productRenderer);

var _summaryRenderer = __webpack_require__(197);

var _summaryRenderer2 = _interopRequireDefault(_summaryRenderer);

var _summaryManager = __webpack_require__(515);

var _summaryManager2 = _interopRequireDefault(_summaryManager);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$;

/**
 * Page Object for "Create order" page
 */
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

var CreateOrderPage = function () {
  function CreateOrderPage() {
    var _this = this;

    (0, _classCallCheck3.default)(this, CreateOrderPage);

    this.cartId = null;
    this.customerId = null;
    this.$container = $(_createOrderMap2.default.orderCreationContainer);

    this.cartProvider = new _cartProvider2.default();
    this.customerManager = new _customerManager2.default();
    this.shippingRenderer = new _shippingRenderer2.default();
    this.addressesRenderer = new _addressesRenderer2.default();
    this.cartRulesRenderer = new _cartRulesRenderer2.default();
    this.router = new _router2.default();
    this.cartEditor = new _cartEditor2.default();
    this.cartRuleManager = new _cartRuleManager2.default();
    this.productManager = new _productManager2.default();
    this.productRenderer = new _productRenderer2.default();
    this.summaryRenderer = new _summaryRenderer2.default();
    this.summaryManager = new _summaryManager2.default();

    this._initListeners();
    this._loadCartFromUrlParams();

    return {
      refreshAddressesList: function refreshAddressesList(refreshCartAddresses) {
        return _this.refreshAddressesList(refreshCartAddresses);
      },
      search: function search(string) {
        return _this.customerManager.search(string);
      }
    };
  }

  /**
   * Checks if correct addresses are selected.
   * There is a case when options list cannot contain cart addresses 'selected' values
   *  because those are outdated in db (e.g. deleted after cart creation or country is disabled)
   *
   * @param {Array} addresses
   *
   * @returns {boolean}
   */


  (0, _createClass3.default)(CreateOrderPage, [{
    key: 'hideCartInfo',


    /**
     * Hides whole cart information wrapper
     */
    value: function hideCartInfo() {
      $(_createOrderMap2.default.cartInfoWrapper).addClass('d-none');
    }

    /**
     * Shows whole cart information wrapper
     */

  }, {
    key: 'showCartInfo',
    value: function showCartInfo() {
      $(_createOrderMap2.default.cartInfoWrapper).removeClass('d-none');
    }

    /**
     * Loads cart if query params contains valid cartId
     *
     * @private
     */

  }, {
    key: '_loadCartFromUrlParams',
    value: function _loadCartFromUrlParams() {
      var urlParams = new URLSearchParams(window.location.search);
      var cartId = Number(urlParams.get('cartId'));

      if (!isNaN(cartId) && cartId !== 0) {
        this.cartProvider.getCart(cartId);
      }
    }

    /**
     * Initializes event listeners
     *
     * @private
     */

  }, {
    key: '_initListeners',
    value: function _initListeners() {
      var _this2 = this;

      this.$container.on('input', _createOrderMap2.default.customerSearchInput, function (e) {
        return _this2._initCustomerSearch(e);
      });
      this.$container.on('click', _createOrderMap2.default.chooseCustomerBtn, function (e) {
        return _this2._initCustomerSelect(e);
      });
      this.$container.on('click', _createOrderMap2.default.useCartBtn, function (e) {
        return _this2._initCartSelect(e);
      });
      this.$container.on('click', _createOrderMap2.default.useOrderBtn, function (e) {
        return _this2._initDuplicateOrderCart(e);
      });
      this.$container.on('input', _createOrderMap2.default.productSearch, function (e) {
        return _this2._initProductSearch(e);
      });
      this.$container.on('input', _createOrderMap2.default.cartRuleSearchInput, function (e) {
        return _this2._initCartRuleSearch(e);
      });
      this.$container.on('blur', _createOrderMap2.default.cartRuleSearchInput, function () {
        return _this2.cartRuleManager.stopSearching();
      });
      this._listenForCartEdit();
      this._onCartLoaded();
      this.onCustomersNotFound();
      this._onCustomerSelected();
      this.initAddressButtonsIframe();
      this.initCustomerDetailsIframe();
    }

    /**
     * @private
     */

  }, {
    key: 'initAddressButtonsIframe',
    value: function initAddressButtonsIframe() {
      $(_createOrderMap2.default.addressAddBtn).fancybox({
        'type': 'iframe',
        'width': '90%',
        'height': '90%'
      });

      $(_createOrderMap2.default.invoiceAddressEditBtn).fancybox({
        'type': 'iframe',
        'width': '90%',
        'height': '90%'
      });

      $(_createOrderMap2.default.deliveryAddressEditBtn).fancybox({
        'type': 'iframe',
        'width': '90%',
        'height': '90%'
      });
    }

    /**
     * init of iframe used when creating new Order -> Search for a customer -> Details
     */

  }, {
    key: 'initCustomerDetailsIframe',
    value: function initCustomerDetailsIframe() {
      $('#js-details-customer-btn').fancybox({
        'type': 'iframe',
        'width': '90%',
        'height': '90%'
      });
    }

    /**
     * Delegates actions to events associated with cart update (e.g. change cart address)
     *
     * @private
     */

  }, {
    key: '_listenForCartEdit',
    value: function _listenForCartEdit() {
      var _this3 = this;

      this._onCartAddressesChanged();
      this._onDeliveryOptionChanged();
      this._onFreeShippingChanged();
      this._addCartRuleToCart();
      this._removeCartRuleFromCart();
      this._onCartCurrencyChanged();
      this._onCartLanguageChanged();

      this.$container.on('change', _createOrderMap2.default.deliveryOptionSelect, function (e) {
        return _this3.cartEditor.changeDeliveryOption(_this3.cartId, e.currentTarget.value);
      });

      this.$container.on('change', _createOrderMap2.default.freeShippingSwitch, function (e) {
        return _this3.cartEditor.setFreeShipping(_this3.cartId, e.currentTarget.value);
      });

      this.$container.on('click', _createOrderMap2.default.addToCartButton, function () {
        return _this3.productManager.addProductToCart(_this3.cartId);
      });

      this.$container.on('change', _createOrderMap2.default.cartCurrencySelect, function (e) {
        return _this3.cartEditor.changeCartCurrency(_this3.cartId, e.currentTarget.value);
      });

      this.$container.on('change', _createOrderMap2.default.cartLanguageSelect, function (e) {
        return _this3.cartEditor.changeCartLanguage(_this3.cartId, e.currentTarget.value);
      });

      this.$container.on('click', _createOrderMap2.default.sendProcessOrderEmailBtn, function () {
        return _this3.summaryManager.sendProcessOrderEmail(_this3.cartId);
      });

      this.$container.on('change', _createOrderMap2.default.listedProductUnitPriceInput, function (e) {
        return _this3._initProductChangePrice(e);
      });
      this.$container.on('change', _createOrderMap2.default.listedProductQtyInput, function (e) {
        return _this3._initProductChangeQty(e);
      });
      this.$container.on('change', _createOrderMap2.default.addressSelect, function () {
        return _this3._changeCartAddresses();
      });
      this.$container.on('click', _createOrderMap2.default.productRemoveBtn, function (e) {
        return _this3._initProductRemoveFromCart(e);
      });
    }

    /**
     * Listens for event when cart is loaded
     *
     * @private
     */

  }, {
    key: '_onCartLoaded',
    value: function _onCartLoaded() {
      var _this4 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.cartLoaded, function (cartInfo) {
        _this4.cartId = cartInfo.cartId;
        _this4._renderCartInfo(cartInfo);
        if (cartInfo.addresses.length !== 0 && !CreateOrderPage.validateSelectedAddresses(cartInfo.addresses)) {
          _this4._changeCartAddresses();
        }
        _this4.customerManager.loadCustomerCarts(_this4.cartId);
        _this4.customerManager.loadCustomerOrders();
      });
    }

    /**
     * Listens for event when no customers were found by search
     *
     * @private
     */

  }, {
    key: 'onCustomersNotFound',
    value: function onCustomersNotFound() {
      var _this5 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.customersNotFound, function () {
        _this5.hideCartInfo();
      });
    }

    /**
     * Listens for event when customer is selected
     *
     * @private
     */

  }, {
    key: '_onCustomerSelected',
    value: function _onCustomerSelected() {
      var _this6 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.customerSelected, function () {
        _this6.showCartInfo();
      });
    }

    /**
     * Listens for cart addresses update event
     *
     * @private
     */

  }, {
    key: '_onCartAddressesChanged',
    value: function _onCartAddressesChanged() {
      var _this7 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.cartAddressesChanged, function (cartInfo) {
        _this7.addressesRenderer.render(cartInfo.addresses);
        _this7.shippingRenderer.render(cartInfo.shipping, cartInfo.products.length === 0);
        _this7.summaryRenderer.render(cartInfo);
      });
    }

    /**
     * Listens for cart delivery option update event
     *
     * @private
     */

  }, {
    key: '_onDeliveryOptionChanged',
    value: function _onDeliveryOptionChanged() {
      var _this8 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.cartDeliveryOptionChanged, function (cartInfo) {
        _this8.shippingRenderer.render(cartInfo.shipping, cartInfo.products.length === 0);
        _this8.summaryRenderer.render(cartInfo);
      });
    }

    /**
     * Listens for cart free shipping update event
     *
     * @private
     */

  }, {
    key: '_onFreeShippingChanged',
    value: function _onFreeShippingChanged() {
      var _this9 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.cartFreeShippingSet, function (cartInfo) {
        _this9.cartRulesRenderer.renderCartRulesBlock(cartInfo.cartRules, cartInfo.products.length === 0);
        _this9.shippingRenderer.render(cartInfo.shipping, cartInfo.products.length === 0);
        _this9.summaryRenderer.render(cartInfo);
      });
    }

    /**
     * Listens for cart language update event
     *
     * @private
     */

  }, {
    key: '_onCartLanguageChanged',
    value: function _onCartLanguageChanged() {
      var _this10 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.cartLanguageChanged, function (cartInfo) {
        _this10._preselectCartLanguage(cartInfo.langId);
      });
    }

    /**
     * Listens for cart currency update event
     *
     * @private
     */

  }, {
    key: '_onCartCurrencyChanged',
    value: function _onCartCurrencyChanged() {
      var _this11 = this;

      // on success
      _eventEmitter.EventEmitter.on(_eventMap2.default.cartCurrencyChanged, function (cartInfo) {
        _this11._renderCartInfo(cartInfo);
        _this11.productRenderer.reset();
      });

      // on failure
      _eventEmitter.EventEmitter.on(_eventMap2.default.cartCurrencyChangeFailed, function (response) {
        _this11.productRenderer.renderCartBlockErrorAlert(response.responseJSON.message);
      });
    }

    /**
     * Init customer searching
     *
     * @param event
     *
     * @private
     */

  }, {
    key: '_initCustomerSearch',
    value: function _initCustomerSearch(event) {
      var _this12 = this;

      clearTimeout(this.timeoutId);
      this.timeoutId = setTimeout(function () {
        return _this12.customerManager.search($(event.currentTarget).val());
      }, 300);
    }

    /**
     * Init selecting customer for which order is being created
     *
     * @param event
     *
     * @private
     */

  }, {
    key: '_initCustomerSelect',
    value: function _initCustomerSelect(event) {
      var customerId = this.customerManager.selectCustomer(event);
      this.customerId = customerId;
      this.cartProvider.loadEmptyCart(customerId);
    }

    /**
     * Inits selecting cart to load
     *
     * @param event
     *
     * @private
     */

  }, {
    key: '_initCartSelect',
    value: function _initCartSelect(event) {
      var cartId = $(event.currentTarget).data('cart-id');
      this.cartProvider.getCart(cartId);
    }

    /**
     * Inits duplicating order cart
     *
     * @private
     */

  }, {
    key: '_initDuplicateOrderCart',
    value: function _initDuplicateOrderCart(event) {
      var orderId = $(event.currentTarget).data('order-id');
      this.cartProvider.duplicateOrderCart(orderId);
    }

    /**
     * Triggers cart rule searching
     *
     * @private
     */

  }, {
    key: '_initCartRuleSearch',
    value: function _initCartRuleSearch(event) {
      var _this13 = this;

      var searchPhrase = event.currentTarget.value;

      clearTimeout(this.timeoutId);
      this.timeoutId = setTimeout(function () {
        return _this13.cartRuleManager.search(searchPhrase);
      }, 300);
    }

    /**
     * Triggers cart rule select
     *
     * @private
     */

  }, {
    key: '_addCartRuleToCart',
    value: function _addCartRuleToCart() {
      var _this14 = this;

      this.$container.on('mousedown', _createOrderMap2.default.foundCartRuleListItem, function (event) {
        // prevent blur event to allow selecting cart rule
        event.preventDefault();
        var cartRuleId = $(event.currentTarget).data('cart-rule-id');
        _this14.cartRuleManager.addCartRuleToCart(cartRuleId, _this14.cartId);

        // manually fire blur event after cart rule is selected.
      }).on('click', _createOrderMap2.default.foundCartRuleListItem, function () {
        $(_createOrderMap2.default.cartRuleSearchInput).blur();
      });
    }

    /**
     * Triggers cart rule removal from cart
     *
     * @private
     */

  }, {
    key: '_removeCartRuleFromCart',
    value: function _removeCartRuleFromCart() {
      var _this15 = this;

      this.$container.on('click', _createOrderMap2.default.cartRuleDeleteBtn, function (event) {
        _this15.cartRuleManager.removeCartRuleFromCart($(event.currentTarget).data('cart-rule-id'), _this15.cartId);
      });
    }

    /**
     * Inits product searching
     *
     * @param event
     *
     * @private
     */

  }, {
    key: '_initProductSearch',
    value: function _initProductSearch(event) {
      var _this16 = this;

      var $productSearchInput = $(event.currentTarget);
      var searchPhrase = $productSearchInput.val();
      clearTimeout(this.timeoutId);

      this.timeoutId = setTimeout(function () {
        return _this16.productManager.search(searchPhrase);
      }, 300);
    }

    /**
     * Inits product removing from cart
     *
     * @param event
     *
     * @private
     */

  }, {
    key: '_initProductRemoveFromCart',
    value: function _initProductRemoveFromCart(event) {
      var product = {
        productId: $(event.currentTarget).data('product-id'),
        attributeId: $(event.currentTarget).data('attribute-id'),
        customizationId: $(event.currentTarget).data('customization-id')
      };

      this.productManager.removeProductFromCart(this.cartId, product);
    }

    /**
     * Inits product in cart price change
     *
     * @param event
     *
     * @private
     */

  }, {
    key: '_initProductChangePrice',
    value: function _initProductChangePrice(event) {
      var product = {
        productId: $(event.currentTarget).data('product-id'),
        attributeId: $(event.currentTarget).data('attribute-id'),
        customizationId: $(event.currentTarget).data('customization-id'),
        price: $(event.currentTarget).val()
      };

      this.productManager.changeProductPrice(this.cartId, this.customerId, product);
    }

    /**
     * Inits product in cart quantity update
     *
     * @param event
     *
     * @private
     */

  }, {
    key: '_initProductChangeQty',
    value: function _initProductChangeQty(event) {
      var product = {
        productId: $(event.currentTarget).data('product-id'),
        attributeId: $(event.currentTarget).data('attribute-id'),
        customizationId: $(event.currentTarget).data('customization-id'),
        newQty: $(event.currentTarget).val()
      };

      this.productManager.changeProductQty(this.cartId, product);
    }

    /**
     * Renders cart summary on the page
     *
     * @param {Object} cartInfo
     *
     * @private
     */

  }, {
    key: '_renderCartInfo',
    value: function _renderCartInfo(cartInfo) {
      this.addressesRenderer.render(cartInfo.addresses);
      this.cartRulesRenderer.renderCartRulesBlock(cartInfo.cartRules, cartInfo.products.length === 0);
      this.shippingRenderer.render(cartInfo.shipping, cartInfo.products.length === 0);
      this.productRenderer.cleanCartBlockAlerts();
      this.productRenderer.renderList(cartInfo.products);
      this.summaryRenderer.render(cartInfo);
      this._preselectCartCurrency(cartInfo.currencyId);
      this._preselectCartLanguage(cartInfo.langId);

      $(_createOrderMap2.default.cartBlock).removeClass('d-none');
      $(_createOrderMap2.default.cartBlock).data('cartId', cartInfo.cartId);
    }

    /**
     * Sets cart currency selection value
     *
     * @param currencyId
     *
     * @private
     */

  }, {
    key: '_preselectCartCurrency',
    value: function _preselectCartCurrency(currencyId) {
      $(_createOrderMap2.default.cartCurrencySelect).val(currencyId);
    }

    /**
     * Sets cart language selection value
     *
     * @param langId
     *
     * @private
     */

  }, {
    key: '_preselectCartLanguage',
    value: function _preselectCartLanguage(langId) {
      $(_createOrderMap2.default.cartLanguageSelect).val(langId);
    }

    /**
     * Changes cart addresses
     *
     * @private
     */

  }, {
    key: '_changeCartAddresses',
    value: function _changeCartAddresses() {
      var addresses = {
        deliveryAddressId: $(_createOrderMap2.default.deliveryAddressSelect).val(),
        invoiceAddressId: $(_createOrderMap2.default.invoiceAddressSelect).val()
      };

      this.cartEditor.changeCartAddresses(this.cartId, addresses);
    }

    /**
     * Refresh addresses list
     *
     * @param {boolean} refreshCartAddresses optional
     *
     * @private
     */

  }, {
    key: 'refreshAddressesList',
    value: function refreshAddressesList(refreshCartAddresses) {
      var _this17 = this;

      var cartId = $(_createOrderMap2.default.cartBlock).data('cartId');
      $.get(this.router.generate('admin_carts_info', { cartId: cartId })).then(function (cartInfo) {
        _this17.addressesRenderer.render(cartInfo.addresses);

        if (refreshCartAddresses) {
          _this17._changeCartAddresses();
        }
      }).catch(function (e) {
        showErrorMessage(e.responseJSON.message);
      });
    }
  }], [{
    key: 'validateSelectedAddresses',
    value: function validateSelectedAddresses(addresses) {
      var deliveryValid = false;
      var invoiceValid = false;

      for (var key in addresses) {
        var address = addresses[key];

        if (address.delivery) {
          deliveryValid = true;
        }

        if (address.invoice) {
          invoiceValid = true;
        }

        if (deliveryValid && invoiceValid) {
          return true;
        }
      }

      return false;
    }
  }]);
  return CreateOrderPage;
}();

exports.default = CreateOrderPage;

/***/ }),

/***/ 225:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _createOrderMap = __webpack_require__(106);

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$;

/**
 * Renders cart rules (cartRules) block
 */
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

var CartRulesRenderer = function () {
  function CartRulesRenderer() {
    (0, _classCallCheck3.default)(this, CartRulesRenderer);

    this.$cartRulesBlock = $(_createOrderMap2.default.cartRulesBlock);
    this.$cartRulesTable = $(_createOrderMap2.default.cartRulesTable);
    this.$searchResultBox = $(_createOrderMap2.default.cartRulesSearchResultBox);
  }

  /**
   * Responsible for rendering cartRules (a.k.a cart rules/discounts) block
   *
   * @param {Array} cartRules
   * @param {Boolean} emptyCart
   */


  (0, _createClass3.default)(CartRulesRenderer, [{
    key: 'renderCartRulesBlock',
    value: function renderCartRulesBlock(cartRules, emptyCart) {
      this._hideErrorBlock();
      // do not render cart rules block at all if cart has no products
      if (emptyCart) {
        this._hideCartRulesBlock();
        return;
      }
      this._showCartRulesBlock();

      // do not render cart rules list when there are no cart rules
      if (cartRules.length === 0) {
        this._hideCartRulesList();

        return;
      }

      this._renderList(cartRules);
    }

    /**
     * Responsible for rendering search results dropdown
     *
     * @param searchResults
     */

  }, {
    key: 'renderSearchResults',
    value: function renderSearchResults(searchResults) {
      this._clearSearchResults();

      if (searchResults.cart_rules.length === 0) {
        this._renderNotFound();
      } else {
        this._renderFoundCartRules(searchResults.cart_rules);
      }

      this._showResultsDropdown();
    }

    /**
     * Displays error message bellow search input
     *
     * @param message
     */

  }, {
    key: 'displayErrorMessage',
    value: function displayErrorMessage(message) {
      $(_createOrderMap2.default.cartRuleErrorText).text(message);
      this._showErrorBlock();
    }

    /**
     * Hides cart rules search result dropdown
     */

  }, {
    key: 'hideResultsDropdown',
    value: function hideResultsDropdown() {
      this.$searchResultBox.addClass('d-none');
    }

    /**
     * Displays cart rules search result dropdown
     *
     * @private
     */

  }, {
    key: '_showResultsDropdown',
    value: function _showResultsDropdown() {
      this.$searchResultBox.removeClass('d-none');
    }

    /**
     * Renders warning that no cart rule was found
     *
     * @private
     */

  }, {
    key: '_renderNotFound',
    value: function _renderNotFound() {
      var $template = $($(_createOrderMap2.default.cartRulesNotFoundTemplate).html()).clone();
      this.$searchResultBox.html($template);
    }

    /**
     * Empties cart rule search results block
     *
     * @private
     */

  }, {
    key: '_clearSearchResults',
    value: function _clearSearchResults() {
      this.$searchResultBox.empty();
    }

    /**
     * Renders found cart rules after search
     *
     * @param cartRules
     *
     * @private
     */

  }, {
    key: '_renderFoundCartRules',
    value: function _renderFoundCartRules(cartRules) {
      var $cartRuleTemplate = $($(_createOrderMap2.default.foundCartRuleTemplate).html());
      for (var key in cartRules) {
        var $template = $cartRuleTemplate.clone();
        var cartRule = cartRules[key];

        var cartRuleName = cartRule.name;
        if (cartRule.code !== '') {
          cartRuleName = cartRule.name + ' - ' + cartRule.code;
        }

        $template.text(cartRuleName);
        $template.data('cart-rule-id', cartRule.cartRuleId);
        this.$searchResultBox.append($template);
      }
    }

    /**
     * Responsible for rendering the list of cart rules
     *
     * @param {Array} cartRules
     *
     * @private
     */

  }, {
    key: '_renderList',
    value: function _renderList(cartRules) {
      this._cleanCartRulesList();
      var $cartRulesTableRowTemplate = $($(_createOrderMap2.default.cartRulesTableRowTemplate).html());

      for (var key in cartRules) {
        var cartRule = cartRules[key];
        var $template = $cartRulesTableRowTemplate.clone();

        $template.find(_createOrderMap2.default.cartRuleNameField).text(cartRule.name);
        $template.find(_createOrderMap2.default.cartRuleDescriptionField).text(cartRule.description);
        $template.find(_createOrderMap2.default.cartRuleValueField).text(cartRule.value);
        $template.find(_createOrderMap2.default.cartRuleDeleteBtn).data('cart-rule-id', cartRule.cartRuleId);

        this.$cartRulesTable.find('tbody').append($template);
      }

      this._showCartRulesList();
    }

    /**
     * Shows error block
     *
     * @private
     */

  }, {
    key: '_showErrorBlock',
    value: function _showErrorBlock() {
      $(_createOrderMap2.default.cartRuleErrorBlock).removeClass('d-none');
    }

    /**
     * Hides error block
     *
     * @private
     */

  }, {
    key: '_hideErrorBlock',
    value: function _hideErrorBlock() {
      $(_createOrderMap2.default.cartRuleErrorBlock).addClass('d-none');
    }

    /**
     * Shows cartRules block
     *
     * @private
     */

  }, {
    key: '_showCartRulesBlock',
    value: function _showCartRulesBlock() {
      this.$cartRulesBlock.removeClass('d-none');
    }

    /**
     * hide cartRules block
     *
     * @private
     */

  }, {
    key: '_hideCartRulesBlock',
    value: function _hideCartRulesBlock() {
      this.$cartRulesBlock.addClass('d-none');
    }

    /**
     * Display the list block of cart rules
     *
     * @private
     */

  }, {
    key: '_showCartRulesList',
    value: function _showCartRulesList() {
      this.$cartRulesTable.removeClass('d-none');
    }

    /**
     * Hide list block of cart rules
     *
     * @private
     */

  }, {
    key: '_hideCartRulesList',
    value: function _hideCartRulesList() {
      this.$cartRulesTable.addClass('d-none');
    }

    /**
     * remove items in cart rules list
     *
     * @private
     */

  }, {
    key: '_cleanCartRulesList',
    value: function _cleanCartRulesList() {
      this.$cartRulesTable.find('tbody').empty();
    }
  }]);
  return CartRulesRenderer;
}();

exports.default = CartRulesRenderer;

/***/ }),

/***/ 226:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _defineProperty2 = __webpack_require__(167);

var _defineProperty3 = _interopRequireDefault(_defineProperty2);

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _createOrderMap = __webpack_require__(106);

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$; /**
                   * 2007-2020 PrestaShop SA and Contributors
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
                   * @copyright 2007-2020 PrestaShop SA and Contributors
                   * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
                   * International Registered Trademark & Property of PrestaShop SA
                   */

var ProductRenderer = function () {
  function ProductRenderer() {
    (0, _classCallCheck3.default)(this, ProductRenderer);

    this.$productsTable = $(_createOrderMap2.default.productsTable);
  }

  /**
   * Renders cart products list
   *
   * @param products
   */


  (0, _createClass3.default)(ProductRenderer, [{
    key: 'renderList',
    value: function renderList(products) {
      this._cleanProductsList();

      if (products.length === 0) {
        this._hideProductsList();

        return;
      }

      var $productsTableRowTemplate = $($(_createOrderMap2.default.productsTableRowTemplate).html());

      for (var key in products) {
        var product = products[key];
        var $template = $productsTableRowTemplate.clone();
        var customizationId = 0;

        if (product.customization) {
          customizationId = product.customization.customizationId;
          this._renderListedProductCustomization(product.customization, $template);
        }

        $template.find(_createOrderMap2.default.listedProductImageField).prop('src', product.imageLink);
        $template.find(_createOrderMap2.default.listedProductNameField).text(product.name);
        $template.find(_createOrderMap2.default.listedProductAttrField).text(product.attribute);
        $template.find(_createOrderMap2.default.listedProductReferenceField).text(product.reference);
        $template.find(_createOrderMap2.default.listedProductUnitPriceInput).val(product.unitPrice);
        $template.find(_createOrderMap2.default.listedProductUnitPriceInput).data('product-id', product.productId);
        $template.find(_createOrderMap2.default.listedProductUnitPriceInput).data('attribute-id', product.attributeId);
        $template.find(_createOrderMap2.default.listedProductUnitPriceInput).data('customization-id', customizationId);
        $template.find(_createOrderMap2.default.listedProductQtyInput).val(product.quantity);
        $template.find(_createOrderMap2.default.listedProductQtyInput).data('product-id', product.productId);
        $template.find(_createOrderMap2.default.listedProductQtyInput).data('attribute-id', product.attributeId);
        $template.find(_createOrderMap2.default.listedProductQtyInput).data('customization-id', customizationId);
        $template.find(_createOrderMap2.default.listedProductQtyInput).data('prev-qty', product.quantity);
        $template.find(_createOrderMap2.default.productTotalPriceField).text(product.price);
        $template.find(_createOrderMap2.default.productRemoveBtn).data('product-id', product.productId);
        $template.find(_createOrderMap2.default.productRemoveBtn).data('attribute-id', product.attributeId);
        $template.find(_createOrderMap2.default.productRemoveBtn).data('customization-id', customizationId);

        this.$productsTable.find('tbody').append($template);
      }

      this._showTaxWarning();
      this._showProductsList();
    }

    /**
     * Renders customization data for listed product
     *
     * @param customization
     * @param $productRowTemplate
     *
     * @private
     */

  }, {
    key: '_renderListedProductCustomization',
    value: function _renderListedProductCustomization(customization, $productRowTemplate) {
      var $customizedTextTemplate = $($(_createOrderMap2.default.listedProductCustomizedTextTemplate).html());
      var $customizedFileTemplate = $($(_createOrderMap2.default.listedProductCustomizedFileTemplate).html());

      for (var key in customization.customizationFieldsData) {
        var customizedData = customization.customizationFieldsData[key];

        var $customizationTemplate = $customizedTextTemplate.clone();

        if (customizedData.type === _createOrderMap2.default.productCustomizationFieldTypeFile) {
          $customizationTemplate = $customizedFileTemplate.clone();
          $customizationTemplate.find(_createOrderMap2.default.listedProductCustomizationName).text(customizedData.name);
          $customizationTemplate.find(_createOrderMap2.default.listedProductCustomizationValue + ' img').prop('src', customizedData.value);
        } else {
          $customizationTemplate.find(_createOrderMap2.default.listedProductCustomizationName).text(customizedData.name);
          $customizationTemplate.find(_createOrderMap2.default.listedProductCustomizationValue).text(customizedData.value);
        }

        $productRowTemplate.find(_createOrderMap2.default.listedProductDefinition).append($customizationTemplate);
      }
    }

    /**
     * Renders cart products search results block
     *
     * @param foundProducts
     */

  }, {
    key: 'renderSearchResults',
    value: function renderSearchResults(foundProducts) {
      this._cleanSearchResults();
      if (foundProducts.length === 0) {
        this._showNotFound();
        this._hideTaxWarning();

        return;
      }

      this._renderFoundProducts(foundProducts);

      this._hideNotFound();
      this._showTaxWarning();
      this._showResultBlock();
    }
  }, {
    key: 'reset',
    value: function reset() {
      this._cleanSearchResults();
      this._hideTaxWarning();
      this._hideResultBlock();
    }

    /**
     * Renders available fields related to selected product
     *
     * @param product
     */

  }, {
    key: 'renderProductMetadata',
    value: function renderProductMetadata(product) {
      this.renderStock(product.stock);
      this._renderCombinations(product.combinations);
      this._renderCustomizations(product.customizationFields);
    }

    /**
     * Updates stock text helper value
     *
     * @param stock
     */

  }, {
    key: 'renderStock',
    value: function renderStock(stock) {
      $(_createOrderMap2.default.inStockCounter).text(stock);
      $(_createOrderMap2.default.quantityInput).attr('max', stock);
    }

    /**
     * Renders found products select
     *
     * @param foundProducts
     *
     * @private
     */

  }, {
    key: '_renderFoundProducts',
    value: function _renderFoundProducts(foundProducts) {
      for (var key in foundProducts) {
        var product = foundProducts[key];

        var name = product.name;
        if (product.combinations.length === 0) {
          name += ' - ' + product.formattedPrice;
        }

        $(_createOrderMap2.default.productSelect).append('<option value="' + product.productId + '">' + name + '</option>');
      }
    }

    /**
     * Cleans product search result fields
     *
     * @private
     */

  }, {
    key: '_cleanSearchResults',
    value: function _cleanSearchResults() {
      $(_createOrderMap2.default.productSelect).empty();
      $(_createOrderMap2.default.combinationsSelect).empty();
      $(_createOrderMap2.default.quantityInput).empty();
    }

    /**
     * Renders combinations row with select options
     *
     * @param {Array} combinations
     *
     * @private
     */

  }, {
    key: '_renderCombinations',
    value: function _renderCombinations(combinations) {
      this._cleanCombinations();

      if (combinations.length === 0) {
        this._hideCombinations();

        return;
      }

      for (var key in combinations) {
        var combination = combinations[key];

        $(_createOrderMap2.default.combinationsSelect).append('<option\n          value="' + combination.attributeCombinationId + '">\n          ' + combination.attribute + ' - ' + combination.formattedPrice + '\n        </option>');
      }

      this._showCombinations();
    }

    /**
     * Resolves weather to add customization fields to result block and adds them if needed
     *
     * @param customizationFields
     *
     * @private
     */

  }, {
    key: '_renderCustomizations',
    value: function _renderCustomizations(customizationFields) {
      var _templateTypeMap;

      // represents customization field type "file".
      var fieldTypeFile = _createOrderMap2.default.productCustomizationFieldTypeFile;
      // represents customization field type "text".
      var fieldTypeText = _createOrderMap2.default.productCustomizationFieldTypeText;

      this._cleanCustomizations();
      if (customizationFields.length === 0) {
        this._hideCustomizations();

        return;
      }

      var $customFieldsContainer = $(_createOrderMap2.default.productCustomFieldsContainer);
      var $fileInputTemplate = $($(_createOrderMap2.default.productCustomFileTemplate).html());
      var $textInputTemplate = $($(_createOrderMap2.default.productCustomTextTemplate).html());

      var templateTypeMap = (_templateTypeMap = {}, (0, _defineProperty3.default)(_templateTypeMap, fieldTypeFile, $fileInputTemplate), (0, _defineProperty3.default)(_templateTypeMap, fieldTypeText, $textInputTemplate), _templateTypeMap);

      for (var key in customizationFields) {
        var customField = customizationFields[key];
        var $template = templateTypeMap[customField.type].clone();

        $template.find(_createOrderMap2.default.productCustomInput).attr('name', 'customizations[' + customField.customizationFieldId + ']').data('customization-field-id', customField.customizationFieldId);
        $template.find(_createOrderMap2.default.productCustomInputLabel).attr('for', 'customizations[' + customField.customizationFieldId + ']').text(customField.name);

        if (customField.required === true) {
          $template.find(_createOrderMap2.default.requiredFieldMark).removeClass('d-none');
        }

        $customFieldsContainer.append($template);
      }

      this._showCustomizations();
    }

    /**
     * Renders error alert for cart block
     *
     * @param message
     */

  }, {
    key: 'renderCartBlockErrorAlert',
    value: function renderCartBlockErrorAlert(message) {
      $(_createOrderMap2.default.cartErrorAlertText).text(message);
      this._showCartBlockError();
    }

    /**
     * Cleans cart block alerts content and hides them
     */

  }, {
    key: 'cleanCartBlockAlerts',
    value: function cleanCartBlockAlerts() {
      $(_createOrderMap2.default.cartErrorAlertText).text('');
      this._hideCartBlockError();
    }

    /**
     * Shows error alert block of cart block
     *
     * @private
     */

  }, {
    key: '_showCartBlockError',
    value: function _showCartBlockError() {
      $(_createOrderMap2.default.cartErrorAlertBlock).removeClass('d-none');
    }

    /**
     * Hides error alert block of cart block
     *
     * @private
     */

  }, {
    key: '_hideCartBlockError',
    value: function _hideCartBlockError() {
      $(_createOrderMap2.default.cartErrorAlertBlock).addClass('d-none');
    }

    /**
     * Shows product customization container
     *
     * @private
     */

  }, {
    key: '_showCustomizations',
    value: function _showCustomizations() {
      $(_createOrderMap2.default.productCustomizationContainer).removeClass('d-none');
    }

    /**
     * Hides product customization container
     *
     * @private
     */

  }, {
    key: '_hideCustomizations',
    value: function _hideCustomizations() {
      $(_createOrderMap2.default.productCustomizationContainer).addClass('d-none');
    }

    /**
     * Empties customization fields container
     *
     * @private
     */

  }, {
    key: '_cleanCustomizations',
    value: function _cleanCustomizations() {
      $(_createOrderMap2.default.productCustomFieldsContainer).empty();
    }

    /**
     * Shows result block
     *
     * @private
     */

  }, {
    key: '_showResultBlock',
    value: function _showResultBlock() {
      $(_createOrderMap2.default.productResultBlock).removeClass('d-none');
    }

    /**
     * Hides result block
     *
     * @private
     */

  }, {
    key: '_hideResultBlock',
    value: function _hideResultBlock() {
      $(_createOrderMap2.default.productResultBlock).addClass('d-none');
    }

    /**
     * Shows products list
     *
     * @private
     */

  }, {
    key: '_showProductsList',
    value: function _showProductsList() {
      this.$productsTable.removeClass('d-none');
    }

    /**
     * Hides products list
     *
     * @private
     */

  }, {
    key: '_hideProductsList',
    value: function _hideProductsList() {
      this.$productsTable.addClass('d-none');
    }

    /**
     * Empties products list
     *
     * @private
     */

  }, {
    key: '_cleanProductsList',
    value: function _cleanProductsList() {
      this.$productsTable.find('tbody').empty();
    }

    /**
     * Empties combinations select
     *
     * @private
     */

  }, {
    key: '_cleanCombinations',
    value: function _cleanCombinations() {
      $(_createOrderMap2.default.combinationsSelect).empty();
    }

    /**
     * Shows combinations row
     *
     * @private
     */

  }, {
    key: '_showCombinations',
    value: function _showCombinations() {
      $(_createOrderMap2.default.combinationsRow).removeClass('d-none');
    }

    /**
     * Hides combinations row
     *
     * @private
     */

  }, {
    key: '_hideCombinations',
    value: function _hideCombinations() {
      $(_createOrderMap2.default.combinationsRow).addClass('d-none');
    }

    /**
     * Shows warning of tax included/excluded
     *
     * @private
     */

  }, {
    key: '_showTaxWarning',
    value: function _showTaxWarning() {
      $(_createOrderMap2.default.productTaxWarning).removeClass('d-none');
    }

    /**
     * Hides warning of tax included/excluded
     *
     * @private
     */

  }, {
    key: '_hideTaxWarning',
    value: function _hideTaxWarning() {
      $(_createOrderMap2.default.productTaxWarning).addClass('d-none');
    }

    /**
     * Shows product not found warning
     *
     * @private
     */

  }, {
    key: '_showNotFound',
    value: function _showNotFound() {
      $(_createOrderMap2.default.noProductsFoundWarning).removeClass('d-none');
    }

    /**
     * Hides product not found warning
     *
     * @private
     */

  }, {
    key: '_hideNotFound',
    value: function _hideNotFound() {
      $(_createOrderMap2.default.noProductsFoundWarning).addClass('d-none');
    }
  }]);
  return ProductRenderer;
}();

exports.default = ProductRenderer;

/***/ }),

/***/ 227:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _keys = __webpack_require__(67);

var _keys2 = _interopRequireDefault(_keys);

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _createOrderMap = __webpack_require__(106);

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$;

/**
 * Manipulates UI of Shipping block in Order creation page
 */
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

var ShippingRenderer = function () {
  function ShippingRenderer() {
    (0, _classCallCheck3.default)(this, ShippingRenderer);

    this.$container = $(_createOrderMap2.default.shippingBlock);
    this.$form = $(_createOrderMap2.default.shippingForm);
    this.$noCarrierBlock = $(_createOrderMap2.default.noCarrierBlock);
  }

  /**
   * @param {Object} shipping
   * @param {Boolean} emptyCart
   */


  (0, _createClass3.default)(ShippingRenderer, [{
    key: 'render',
    value: function render(shipping, emptyCart) {
      if (emptyCart) {
        this._hideContainer();
      } else if (shipping !== null) {
        this._displayForm(shipping);
      } else {
        this._displayNoCarriersWarning();
      }
    }

    /**
     * Show form block with rendered delivery options instead of warning message
     *
     * @param shipping
     *
     * @private
     */

  }, {
    key: '_displayForm',
    value: function _displayForm(shipping) {
      this._hideNoCarrierBlock();
      this._renderDeliveryOptions(shipping.deliveryOptions, shipping.selectedCarrierId);
      this._renderTotalShipping(shipping.shippingPrice);
      this._renderFreeShippingSwitch(shipping.freeShipping);
      this._showForm();
      this._showContainer();
    }

    /**
     * Renders free shipping switch depending on free shipping value
     *
     * @param isFreeShipping
     *
     * @private
     */

  }, {
    key: '_renderFreeShippingSwitch',
    value: function _renderFreeShippingSwitch(isFreeShipping) {
      $(_createOrderMap2.default.freeShippingSwitch).each(function (key, input) {
        if (input.value === '1') {
          input.checked = isFreeShipping;
        } else {
          input.checked = !isFreeShipping;
        }
      });
    }

    /**
     * Show warning message that no carriers are available and hide form block
     *
     * @private
     */

  }, {
    key: '_displayNoCarriersWarning',
    value: function _displayNoCarriersWarning() {
      this._showContainer();
      this._hideForm();
      this._showNoCarrierBlock();
    }

    /**
     * Renders delivery options selection block
     *
     * @param deliveryOptions
     * @param selectedVal
     *
     * @private
     */

  }, {
    key: '_renderDeliveryOptions',
    value: function _renderDeliveryOptions(deliveryOptions, selectedVal) {
      var $deliveryOptionSelect = $(_createOrderMap2.default.deliveryOptionSelect);
      $deliveryOptionSelect.empty();

      for (var key in (0, _keys2.default)(deliveryOptions)) {
        var option = deliveryOptions[key];

        var deliveryOption = {
          value: option.carrierId,
          text: option.carrierName + ' - ' + option.carrierDelay
        };

        if (selectedVal === deliveryOption.value) {
          deliveryOption.selected = 'selected';
        }

        $deliveryOptionSelect.append($('<option>', deliveryOption));
      }
    }

    /**
     * Renders dynamic value of shipping price
     *
     * @param shippingPrice
     *
     * @private
     */

  }, {
    key: '_renderTotalShipping',
    value: function _renderTotalShipping(shippingPrice) {
      var $totalShippingField = $(_createOrderMap2.default.totalShippingField);
      $totalShippingField.empty();

      $totalShippingField.append(shippingPrice);
    }

    /**
     * Show whole shipping container
     *
     * @private
     */

  }, {
    key: '_showContainer',
    value: function _showContainer() {
      this.$container.removeClass('d-none');
    }

    /**
     * Hide whole shipping container
     *
     * @private
     */

  }, {
    key: '_hideContainer',
    value: function _hideContainer() {
      this.$container.addClass('d-none');
    }

    /**
     * Show form block
     *
     * @private
     */

  }, {
    key: '_showForm',
    value: function _showForm() {
      this.$form.removeClass('d-none');
    }

    /**
     * Hide form block
     *
     * @private
     */

  }, {
    key: '_hideForm',
    value: function _hideForm() {
      this.$form.addClass('d-none');
    }

    /**
     * Show warning message block which warns that no carriers are available
     *
     * @private
     */

  }, {
    key: '_showNoCarrierBlock',
    value: function _showNoCarrierBlock() {
      this.$noCarrierBlock.removeClass('d-none');
    }

    /**
     * Hide warning message block which warns that no carriers are available
     *
     * @private
     */

  }, {
    key: '_hideNoCarrierBlock',
    value: function _hideNoCarrierBlock() {
      this.$noCarrierBlock.addClass('d-none');
    }
  }]);
  return ShippingRenderer;
}();

exports.default = ShippingRenderer;

/***/ }),

/***/ 27:
/***/ (function(module, exports) {

var hasOwnProperty = {}.hasOwnProperty;
module.exports = function(it, key){
  return hasOwnProperty.call(it, key);
};

/***/ }),

/***/ 3:
/***/ (function(module, exports) {

var core = module.exports = {version: '2.4.0'};
if(typeof __e == 'number')__e = core; // eslint-disable-line no-undef

/***/ }),

/***/ 34:
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.14 / 15.2.3.14 Object.keys(O)
var $keys       = __webpack_require__(55)
  , enumBugKeys = __webpack_require__(48);

module.exports = Object.keys || function keys(O){
  return $keys(O, enumBugKeys);
};

/***/ }),

/***/ 36:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.EventEmitter = undefined;

var _events = __webpack_require__(53);

var _events2 = _interopRequireDefault(_events);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * We instanciate one EventEmitter (restricted via a const) so that every components
 * register/dispatch on the same one and can communicate with each other.
 */
var EventEmitter = exports.EventEmitter = new _events2.default(); /**
                                                                   * 2007-2020 PrestaShop SA and Contributors
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
                                                                   * @copyright 2007-2020 PrestaShop SA and Contributors
                                                                   * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
                                                                   * International Registered Trademark & Property of PrestaShop SA
                                                                   */

/***/ }),

/***/ 38:
/***/ (function(module, exports) {

// 7.2.1 RequireObjectCoercible(argument)
module.exports = function(it){
  if(it == undefined)throw TypeError("Can't call method on  " + it);
  return it;
};

/***/ }),

/***/ 39:
/***/ (function(module, exports) {

// 7.1.4 ToInteger
var ceil  = Math.ceil
  , floor = Math.floor;
module.exports = function(it){
  return isNaN(it = +it) ? 0 : (it > 0 ? floor : ceil)(it);
};

/***/ }),

/***/ 4:
/***/ (function(module, exports) {

module.exports = function(it){
  return typeof it === 'object' ? it !== null : typeof it === 'function';
};

/***/ }),

/***/ 43:
/***/ (function(module, exports) {

var id = 0
  , px = Math.random();
module.exports = function(key){
  return 'Symbol('.concat(key === undefined ? '' : key, ')_', (++id + px).toString(36));
};

/***/ }),

/***/ 45:
/***/ (function(module, exports, __webpack_require__) {

// 7.1.13 ToObject(argument)
var defined = __webpack_require__(38);
module.exports = function(it){
  return Object(defined(it));
};

/***/ }),

/***/ 46:
/***/ (function(module, exports, __webpack_require__) {

var shared = __webpack_require__(49)('keys')
  , uid    = __webpack_require__(43);
module.exports = function(key){
  return shared[key] || (shared[key] = uid(key));
};

/***/ }),

/***/ 47:
/***/ (function(module, exports) {

var toString = {}.toString;

module.exports = function(it){
  return toString.call(it).slice(8, -1);
};

/***/ }),

/***/ 48:
/***/ (function(module, exports) {

// IE 8- don't enum bug keys
module.exports = (
  'constructor,hasOwnProperty,isPrototypeOf,propertyIsEnumerable,toLocaleString,toString,valueOf'
).split(',');

/***/ }),

/***/ 49:
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__(5)
  , SHARED = '__core-js_shared__'
  , store  = global[SHARED] || (global[SHARED] = {});
module.exports = function(key){
  return store[key] || (store[key] = {});
};

/***/ }),

/***/ 5:
/***/ (function(module, exports) {

// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
var global = module.exports = typeof window != 'undefined' && window.Math == Math
  ? window : typeof self != 'undefined' && self.Math == Math ? self : Function('return this')();
if(typeof __g == 'number')__g = global; // eslint-disable-line no-undef

/***/ }),

/***/ 508:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.refreshAddressesList = exports.searchCustomerByString = undefined;

var _createOrderPage = __webpack_require__(220);

var _createOrderPage2 = _interopRequireDefault(_createOrderPage);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$; /**
                   * 2007-2020 PrestaShop SA and Contributors
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
                   * @copyright 2007-2020 PrestaShop SA and Contributors
                   * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
                   * International Registered Trademark & Property of PrestaShop SA
                   */

var orderPageManager = null;

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
function refreshAddressesList(refreshCartAddresses) {
  if (orderPageManager !== null) {
    orderPageManager.refreshAddressesList(refreshCartAddresses);
  } else {
    console.log('Error: Could not refresh addresses list as orderPageManager is null');
  }
}

$(document).ready(function () {
  orderPageManager = new _createOrderPage2.default();
});

exports.searchCustomerByString = searchCustomerByString;
exports.refreshAddressesList = refreshAddressesList;

/***/ }),

/***/ 509:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _createOrderMap = __webpack_require__(106);

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

var _router = __webpack_require__(73);

var _router2 = _interopRequireDefault(_router);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

var $ = window.$;

/**
 * Renders Delivery & Invoice addresses select
 */

var AddressesRenderer = function () {
  function AddressesRenderer() {
    (0, _classCallCheck3.default)(this, AddressesRenderer);

    this.router = new _router2.default();
  }
  /**
   * @param {Array} addresses
   */


  (0, _createClass3.default)(AddressesRenderer, [{
    key: 'render',
    value: function render(addresses) {
      this._cleanAddresses();
      if (addresses.length === 0) {
        this._hideAddressesContent();
        this._showEmptyAddressesWarning();
        this._showAddressesBlock();

        return;
      }

      this._showAddressesContent();
      this._hideEmptyAddressesWarning();

      for (var key in addresses) {
        var address = addresses[key];

        this._renderDeliveryAddress(address);
        this._renderInvoiceAddress(address);
      }

      this._showAddressesBlock();
    }

    /**
     * Renders delivery address content
     *
     * @param address
     *
     * @private
     */

  }, {
    key: '_renderDeliveryAddress',
    value: function _renderDeliveryAddress(address) {
      var deliveryAddressOption = {
        value: address.addressId,
        text: address.alias
      };

      if (address.delivery) {
        $(_createOrderMap2.default.deliveryAddressDetails).html(address.formattedAddress);
        deliveryAddressOption.selected = 'selected';
        $(_createOrderMap2.default.deliveryAddressEditBtn).prop('href', this.router.generate('admin_addresses_edit', {
          addressId: address.addressId,
          liteDisplaying: 1,
          submitFormAjax: 1
        }));
      }

      $(_createOrderMap2.default.deliveryAddressSelect).append($('<option>', deliveryAddressOption));
    }

    /**
     * Renders invoice address content
     *
     * @param address
     *
     * @private
     */

  }, {
    key: '_renderInvoiceAddress',
    value: function _renderInvoiceAddress(address) {
      var invoiceAddressOption = {
        value: address.addressId,
        text: address.alias
      };

      if (address.invoice) {
        $(_createOrderMap2.default.invoiceAddressDetails).html(address.formattedAddress);
        invoiceAddressOption.selected = 'selected';
        $(_createOrderMap2.default.invoiceAddressEditBtn).prop('href', this.router.generate('admin_addresses_edit', {
          addressId: address.addressId,
          liteDisplaying: 1,
          submitFormAjax: 1
        }));
      }

      $(_createOrderMap2.default.invoiceAddressSelect).append($('<option>', invoiceAddressOption));
    }

    /**
     * Shows addresses block
     *
     * @private
     */

  }, {
    key: '_showAddressesBlock',
    value: function _showAddressesBlock() {
      $(_createOrderMap2.default.addressesBlock).removeClass('d-none');
    }

    /**
     * Empties addresses content
     *
     * @private
     */

  }, {
    key: '_cleanAddresses',
    value: function _cleanAddresses() {
      $(_createOrderMap2.default.deliveryAddressDetails).empty();
      $(_createOrderMap2.default.deliveryAddressSelect).empty();
      $(_createOrderMap2.default.invoiceAddressDetails).empty();
      $(_createOrderMap2.default.invoiceAddressSelect).empty();
    }

    /**
     * Shows addresses content and hides warning
     *
     * @private
     */

  }, {
    key: '_showAddressesContent',
    value: function _showAddressesContent() {
      $(_createOrderMap2.default.addressesContent).removeClass('d-none');
      $(_createOrderMap2.default.addressesWarning).addClass('d-none');
    }

    /**
     * Hides addresses content and shows warning
     *
     * @private
     */

  }, {
    key: '_hideAddressesContent',
    value: function _hideAddressesContent() {
      $(_createOrderMap2.default.addressesContent).addClass('d-none');
      $(_createOrderMap2.default.addressesWarning).removeClass('d-none');
    }

    /**
     * Shows warning empty addresses warning
     *
     * @private
     */

  }, {
    key: '_showEmptyAddressesWarning',
    value: function _showEmptyAddressesWarning() {
      $(_createOrderMap2.default.addressesWarning).removeClass('d-none');
    }

    /**
     * Hides empty addresses warning
     *
     * @private
     */

  }, {
    key: '_hideEmptyAddressesWarning',
    value: function _hideEmptyAddressesWarning() {
      $(_createOrderMap2.default.addressesWarning).addClass('d-none');
    }
  }]);
  return AddressesRenderer;
}();

exports.default = AddressesRenderer;

/***/ }),

/***/ 51:
/***/ (function(module, exports, __webpack_require__) {

// fallback for non-array-like ES3 and non-enumerable old V8 strings
var cof = __webpack_require__(47);
module.exports = Object('z').propertyIsEnumerable(0) ? Object : function(it){
  return cof(it) == 'String' ? it.split('') : Object(it);
};

/***/ }),

/***/ 510:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _createOrderMap = __webpack_require__(106);

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

var _router = __webpack_require__(73);

var _router2 = _interopRequireDefault(_router);

var _eventEmitter = __webpack_require__(36);

var _eventMap = __webpack_require__(133);

var _eventMap2 = _interopRequireDefault(_eventMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

var $ = window.$;

/**
 * Provides ajax calls for getting cart information
 */

var CartProvider = function () {
  function CartProvider() {
    (0, _classCallCheck3.default)(this, CartProvider);

    this.$container = $(_createOrderMap2.default.orderCreationContainer);
    this.router = new _router2.default();
  }

  /**
   * Gets cart information
   *
   * @param cartId
   *
   * @returns {jqXHR}. Object with cart information in response.
   */


  (0, _createClass3.default)(CartProvider, [{
    key: 'getCart',
    value: function getCart(cartId) {
      $.get(this.router.generate('admin_carts_info', { cartId: cartId })).then(function (cartInfo) {
        _eventEmitter.EventEmitter.emit(_eventMap2.default.cartLoaded, cartInfo);
      });
    }

    /**
     * Gets existing empty cart or creates new empty cart for customer.
     *
     * @param customerId
     *
     * @returns {jqXHR}. Object with cart information in response
     */

  }, {
    key: 'loadEmptyCart',
    value: function loadEmptyCart(customerId) {
      $.post(this.router.generate('admin_carts_create'), {
        customerId: customerId
      }).then(function (cartInfo) {
        _eventEmitter.EventEmitter.emit(_eventMap2.default.cartLoaded, cartInfo);
      });
    }

    /**
     * Duplicates cart from provided order
     *
     * @param orderId
     *
     * @returns {jqXHR}. Object with cart information in response
     */

  }, {
    key: 'duplicateOrderCart',
    value: function duplicateOrderCart(orderId) {
      $.post(this.router.generate('admin_orders_duplicate_cart', { orderId: orderId })).then(function (cartInfo) {
        _eventEmitter.EventEmitter.emit(_eventMap2.default.cartLoaded, cartInfo);
      });
    }
  }]);
  return CartProvider;
}();

exports.default = CartProvider;

/***/ }),

/***/ 511:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _cartEditor = __webpack_require__(196);

var _cartEditor2 = _interopRequireDefault(_cartEditor);

var _cartRulesRenderer = __webpack_require__(225);

var _cartRulesRenderer2 = _interopRequireDefault(_cartRulesRenderer);

var _createOrderMap = __webpack_require__(106);

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

var _eventEmitter = __webpack_require__(36);

var _eventMap = __webpack_require__(133);

var _eventMap2 = _interopRequireDefault(_eventMap);

var _router = __webpack_require__(73);

var _router2 = _interopRequireDefault(_router);

var _summaryRenderer = __webpack_require__(197);

var _summaryRenderer2 = _interopRequireDefault(_summaryRenderer);

var _shippingRenderer = __webpack_require__(227);

var _shippingRenderer2 = _interopRequireDefault(_shippingRenderer);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

var $ = window.$;

/**
 * Responsible for searching cart rules and managing cart rules search block
 */

var CartRuleManager = function () {
  function CartRuleManager() {
    var _this = this;

    (0, _classCallCheck3.default)(this, CartRuleManager);

    this.activeSearchRequest = null;

    this.router = new _router2.default();
    this.$searchInput = $(_createOrderMap2.default.cartRuleSearchInput);
    this.cartRulesRenderer = new _cartRulesRenderer2.default();
    this.cartEditor = new _cartEditor2.default();
    this.summaryRenderer = new _summaryRenderer2.default();
    this.shippingRenderer = new _shippingRenderer2.default();

    this._initListeners();

    return {
      search: function search(searchPhrase) {
        return _this._search(searchPhrase);
      },
      stopSearching: function stopSearching() {
        return _this.cartRulesRenderer.hideResultsDropdown();
      },
      addCartRuleToCart: function addCartRuleToCart(cartRuleId, cartId) {
        return _this.cartEditor.addCartRuleToCart(cartRuleId, cartId);
      },
      removeCartRuleFromCart: function removeCartRuleFromCart(cartRuleId, cartId) {
        return _this.cartEditor.removeCartRuleFromCart(cartRuleId, cartId);
      }
    };
  }

  /**
   * Initiates event listeners for cart rule actions
   *
   * @private
   */


  (0, _createClass3.default)(CartRuleManager, [{
    key: '_initListeners',
    value: function _initListeners() {
      this._onCartRuleSearch();
      this._onAddCartRuleToCart();
      this._onAddCartRuleToCartFailure();
      this._onRemoveCartRuleFromCart();
    }

    /**
     * Listens for cart rule search action
     *
     * @private
     */

  }, {
    key: '_onCartRuleSearch',
    value: function _onCartRuleSearch() {
      var _this2 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.cartRuleSearched, function (cartRules) {
        _this2.cartRulesRenderer.renderSearchResults(cartRules);
      });
    }

    /**
     * Listens event of add cart rule to cart action
     *
     * @private
     */

  }, {
    key: '_onAddCartRuleToCart',
    value: function _onAddCartRuleToCart() {
      var _this3 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.cartRuleAdded, function (cartInfo) {
        var cartIsEmpty = cartInfo.products.length === 0;
        _this3.cartRulesRenderer.renderCartRulesBlock(cartInfo.cartRules, cartIsEmpty);
        _this3.shippingRenderer.render(cartInfo.shipping, cartIsEmpty);
        _this3.summaryRenderer.render(cartInfo);
      });
    }

    /**
     * Listens event when add cart rule to cart fails
     *
     * @private
     */

  }, {
    key: '_onAddCartRuleToCartFailure',
    value: function _onAddCartRuleToCartFailure() {
      var _this4 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.cartRuleFailedToAdd, function (message) {
        _this4.cartRulesRenderer.displayErrorMessage(message);
      });
    }

    /**
     * Listens event for remove cart rule from cart action
     *
     * @private
     */

  }, {
    key: '_onRemoveCartRuleFromCart',
    value: function _onRemoveCartRuleFromCart() {
      var _this5 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.cartRuleRemoved, function (cartInfo) {
        var cartIsEmpty = cartInfo.products.length === 0;
        _this5.shippingRenderer.render(cartInfo.shipping, cartIsEmpty);
        _this5.cartRulesRenderer.renderCartRulesBlock(cartInfo.cartRules, cartIsEmpty);
        _this5.summaryRenderer.render(cartInfo);
      });
    }

    /**
     * Searches for cart rules by search phrase
     *
     * @private
     */

  }, {
    key: '_search',
    value: function _search(searchPhrase) {
      if (this.activeSearchRequest !== null) {
        this.activeSearchRequest.abort();
      }

      this.activeSearchRequest = $.get(this.router.generate('admin_cart_rules_search'), {
        search_phrase: searchPhrase
      });

      this.activeSearchRequest.then(function (cartRules) {
        _eventEmitter.EventEmitter.emit(_eventMap2.default.cartRuleSearched, cartRules);
      }).catch(function (e) {
        if (e.statusText === 'abort') {
          return;
        }

        showErrorMessage(e.responseJSON.message);
      });
    }
  }]);
  return CartRuleManager;
}();

exports.default = CartRuleManager;

/***/ }),

/***/ 512:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _createOrderMap = __webpack_require__(106);

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

var _customerRenderer = __webpack_require__(513);

var _customerRenderer2 = _interopRequireDefault(_customerRenderer);

var _eventEmitter = __webpack_require__(36);

var _eventMap = __webpack_require__(133);

var _eventMap2 = _interopRequireDefault(_eventMap);

var _router = __webpack_require__(73);

var _router2 = _interopRequireDefault(_router);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$;

/**
 * Responsible for customers managing. (search, select, get customer info etc.)
 */
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

var CustomerManager = function () {
  function CustomerManager() {
    var _this = this;

    (0, _classCallCheck3.default)(this, CustomerManager);

    this.customerId = null;
    this.activeSearchRequest = null;

    this.router = new _router2.default();
    this.$container = $(_createOrderMap2.default.customerSearchBlock);
    this.$searchInput = $(_createOrderMap2.default.customerSearchInput);
    this.$customerSearchResultBlock = $(_createOrderMap2.default.customerSearchResultsBlock);
    this.customerRenderer = new _customerRenderer2.default();

    this._initListeners();
    this.initAddCustomerIframe();

    return {
      search: function search(searchPhrase) {
        return _this._search(searchPhrase);
      },
      selectCustomer: function selectCustomer(event) {
        return _this._selectCustomer(event);
      },
      loadCustomerCarts: function loadCustomerCarts(currentCartId) {
        return _this._loadCustomerCarts(currentCartId);
      },
      loadCustomerOrders: function loadCustomerOrders() {
        return _this._loadCustomerOrders();
      }
    };
  }

  /**
   * Initializes event listeners
   *
   * @private
   */


  (0, _createClass3.default)(CustomerManager, [{
    key: '_initListeners',
    value: function _initListeners() {
      var _this2 = this;

      this.$container.on('click', _createOrderMap2.default.changeCustomerBtn, function () {
        return _this2._changeCustomer();
      });
      this._onCustomerSearch();
      this._onCustomerSelect();
      this.onCustomersNotFound();
    }

    /**
     * @private
     */

  }, {
    key: 'initAddCustomerIframe',
    value: function initAddCustomerIframe() {
      $(_createOrderMap2.default.customerAddBtn).fancybox({
        'type': 'iframe',
        'width': '90%',
        'height': '90%'
      });
    }

    /**
     * Listens for customer search event
     *
     * @private
     */

  }, {
    key: '_onCustomerSearch',
    value: function _onCustomerSearch() {
      var _this3 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.customerSearched, function (response) {
        _this3.activeSearchRequest = null;
        _this3.customerRenderer.clearShownCustomers();

        if (response.customers.length === 0) {
          _eventEmitter.EventEmitter.emit(_eventMap2.default.customersNotFound);

          return;
        }

        _this3.customerRenderer.renderSearchResults(response.customers);
      });
    }

    /**
     * Listens for event of when no customers were found by search
     *
     * @private
     */

  }, {
    key: 'onCustomersNotFound',
    value: function onCustomersNotFound() {
      var _this4 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.customersNotFound, function () {
        _this4.customerRenderer.showNotFoundCustomers();
        _this4.customerRenderer.hideCheckoutHistoryBlock();
      });
    }

    /**
     * Listens for customer select event
     *
     * @private
     */

  }, {
    key: '_onCustomerSelect',
    value: function _onCustomerSelect() {
      var _this5 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.customerSelected, function (event) {
        var $chooseBtn = $(event.currentTarget);
        _this5.customerId = $chooseBtn.data('customer-id');

        var createAddressUrl = _this5.router.generate('admin_addresses_create', {
          'liteDisplaying': 1,
          'submitFormAjax': 1,
          'id_customer': _this5.customerId
        });
        $(_createOrderMap2.default.addressAddBtn).attr('href', createAddressUrl);

        _this5.customerRenderer.displaySelectedCustomerBlock($chooseBtn);
      });
    }

    /**
     * Handles use case when customer is changed
     *
     * @private
     */

  }, {
    key: '_changeCustomer',
    value: function _changeCustomer() {
      this.customerRenderer.showCustomerSearch();
    }

    /**
     * Loads customer carts list
     *
     * @param currentCartId
     */

  }, {
    key: '_loadCustomerCarts',
    value: function _loadCustomerCarts(currentCartId) {
      var _this6 = this;

      var customerId = this.customerId;

      $.get(this.router.generate('admin_customers_carts', { customerId: customerId })).then(function (response) {
        _this6.customerRenderer.renderCarts(response.carts, currentCartId);
      }).catch(function (e) {
        showErrorMessage(e.responseJSON.message);
      });
    }

    /**
     * Loads customer orders list
     */

  }, {
    key: '_loadCustomerOrders',
    value: function _loadCustomerOrders() {
      var _this7 = this;

      var customerId = this.customerId;

      $.get(this.router.generate('admin_customers_orders', { customerId: customerId })).then(function (response) {
        _this7.customerRenderer.renderOrders(response.orders);
      }).catch(function (e) {
        showErrorMessage(e.responseJSON.message);
      });
    }

    /**
     * @param {Event} chooseCustomerEvent
     *
     * @return {Number}
     */

  }, {
    key: '_selectCustomer',
    value: function _selectCustomer(chooseCustomerEvent) {
      _eventEmitter.EventEmitter.emit(_eventMap2.default.customerSelected, chooseCustomerEvent);

      return this.customerId;
    }

    /**
     * Searches for customers
     *
     * @private
     */

  }, {
    key: '_search',
    value: function _search(searchPhrase) {
      if (searchPhrase.length === 0) {
        return;
      }

      if (this.activeSearchRequest !== null) {
        this.activeSearchRequest.abort();
      }

      var $searchRequest = $.get(this.router.generate('admin_customers_search'), {
        customer_search: searchPhrase
      });
      this.activeSearchRequest = $searchRequest;

      $searchRequest.then(function (response) {
        _eventEmitter.EventEmitter.emit(_eventMap2.default.customerSearched, response);
      }).catch(function (response) {
        if (response.statusText === 'abort') {
          return;
        }

        showErrorMessage(response.responseJSON.message);
      });
    }
  }]);
  return CustomerManager;
}();

exports.default = CustomerManager;

/***/ }),

/***/ 513:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _keys = __webpack_require__(67);

var _keys2 = _interopRequireDefault(_keys);

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _createOrderMap = __webpack_require__(106);

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

var _router = __webpack_require__(73);

var _router2 = _interopRequireDefault(_router);

var _eventMap = __webpack_require__(133);

var _eventMap2 = _interopRequireDefault(_eventMap);

var _eventEmitter = __webpack_require__(36);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

var _window = window,
    $ = _window.$;

/**
 * Responsible for customer information rendering
 */

var CustomerRenderer = function () {
  function CustomerRenderer() {
    (0, _classCallCheck3.default)(this, CustomerRenderer);

    this.$container = $(_createOrderMap2.default.customerSearchBlock);
    this.$customerSearchResultBlock = $(_createOrderMap2.default.customerSearchResultsBlock);
    this.router = new _router2.default();
  }

  /**
   * Renders customer search results
   *
   * @param foundCustomers
   */


  (0, _createClass3.default)(CustomerRenderer, [{
    key: 'renderSearchResults',
    value: function renderSearchResults(foundCustomers) {
      if (foundCustomers.length === 0) {
        _eventEmitter.EventEmitter.emit(_eventMap2.default.customersNotFound);

        return;
      }

      for (var customerId in foundCustomers) {
        var customerResult = foundCustomers[customerId];
        var customer = {
          id: customerId,
          firstName: customerResult.firstname,
          lastName: customerResult.lastname,
          email: customerResult.email,
          birthday: customerResult.birthday !== '0000-00-00' ? customerResult.birthday : ' '
        };

        this._renderFoundCustomer(customer);
      }
    }

    /**
     * Responsible for displaying customer block after customer select
     *
     * @param $targetedBtn
     */

  }, {
    key: 'displaySelectedCustomerBlock',
    value: function displaySelectedCustomerBlock($targetedBtn) {
      this.showCheckoutHistoryBlock();

      $targetedBtn.addClass('d-none');

      var $customerCard = $targetedBtn.closest('.card');

      $customerCard.addClass('border-success');
      $customerCard.find(_createOrderMap2.default.changeCustomerBtn).removeClass('d-none');

      this.$container.find(_createOrderMap2.default.customerSearchRow).addClass('d-none');
      this.$container.find(_createOrderMap2.default.notSelectedCustomerSearchResults).closest(_createOrderMap2.default.customerSearchResultColumn).remove();
    }

    /**
     * Shows customer search block
     */

  }, {
    key: 'showCustomerSearch',
    value: function showCustomerSearch() {
      this.$container.find(_createOrderMap2.default.customerSearchRow).removeClass('d-none');
    }

    /**
     * Renders customer carts list
     *
     * @param {Array} carts
     * @param {Int} currentCartId
     */

  }, {
    key: 'renderCarts',
    value: function renderCarts(carts, currentCartId) {
      var $cartsTable = $(_createOrderMap2.default.customerCartsTable);
      var $cartsTableRowTemplate = $($(_createOrderMap2.default.customerCartsTableRowTemplate).html());

      $cartsTable.find('tbody').empty();
      this.showCheckoutHistoryBlock();
      this._removeEmptyListRowFromTable($cartsTable);

      for (var key in carts) {
        var cart = carts[key];

        // do not render current cart
        if (cart.cartId === currentCartId) {
          // render 'No records found' warn if carts only contain current cart
          if (carts.length === 1) {
            this._renderEmptyList($cartsTable);
          }

          continue;
        }

        var $cartsTableRow = $cartsTableRowTemplate.clone();

        $cartsTableRow.find(_createOrderMap2.default.cartIdField).text(cart.cartId);
        $cartsTableRow.find(_createOrderMap2.default.cartDateField).text(cart.creationDate);
        $cartsTableRow.find(_createOrderMap2.default.cartTotalField).text(cart.totalPrice);
        $cartsTableRow.find(_createOrderMap2.default.cartDetailsBtn).prop('href', this.router.generate('admin_carts_view', { cartId: cart.cartId }));

        $cartsTableRow.find(_createOrderMap2.default.useCartBtn).data('cart-id', cart.cartId);

        $cartsTable.find('thead').removeClass('d-none');
        $cartsTable.find('tbody').append($cartsTableRow);
      }
    }

    /**
     * Renders customer orders list
     *
     * @param {Array} orders
     */

  }, {
    key: 'renderOrders',
    value: function renderOrders(orders) {
      var $ordersTable = $(_createOrderMap2.default.customerOrdersTable);
      var $rowTemplate = $($(_createOrderMap2.default.customerOrdersTableRowTemplate).html());

      $ordersTable.find('tbody').empty();
      this.showCheckoutHistoryBlock();
      this._removeEmptyListRowFromTable($ordersTable);

      //render 'No records found' when list is empty
      if (orders.length === 0) {
        this._renderEmptyList($ordersTable);

        return;
      }

      for (var key in (0, _keys2.default)(orders)) {
        var order = orders[key];
        var $template = $rowTemplate.clone();

        $template.find(_createOrderMap2.default.orderIdField).text(order.orderId);
        $template.find(_createOrderMap2.default.orderDateField).text(order.orderPlacedDate);
        $template.find(_createOrderMap2.default.orderProductsField).text(order.orderProductsCount);
        $template.find(_createOrderMap2.default.orderTotalField).text(order.totalPaid);
        $template.find(_createOrderMap2.default.orderPaymentMethod).text(order.paymentMethodName);
        $template.find(_createOrderMap2.default.orderStatusField).text(order.orderStatus);
        $template.find(_createOrderMap2.default.orderDetailsBtn).prop('href', this.router.generate('admin_orders_view', { orderId: order.orderId }));

        $template.find(_createOrderMap2.default.useOrderBtn).data('order-id', order.orderId);

        $ordersTable.find('thead').removeClass('d-none');
        $ordersTable.find('tbody').append($template);
      }
    }

    /**
     * Shows empty result when customer is not found
     */

  }, {
    key: 'showNotFoundCustomers',
    value: function showNotFoundCustomers() {
      $(_createOrderMap2.default.customerSearchEmptyResultWarning).removeClass('d-none');
    }

    /**
     * Hides not found customers warning
     */

  }, {
    key: 'hideNotFoundCustomers',
    value: function hideNotFoundCustomers() {
      $(_createOrderMap2.default.customerSearchEmptyResultWarning).addClass('d-none');
    }

    /**
     * Shows checkout history block where carts and orders are rendered
     *
     * @private
     */

  }, {
    key: 'showCheckoutHistoryBlock',
    value: function showCheckoutHistoryBlock() {
      $(_createOrderMap2.default.customerCheckoutHistory).removeClass('d-none');
    }

    /**
     * Hides checkout history block where carts and orders are rendered
     */

  }, {
    key: 'hideCheckoutHistoryBlock',
    value: function hideCheckoutHistoryBlock() {
      $(_createOrderMap2.default.customerCheckoutHistory).addClass('d-none');
    }

    /**
     * Renders 'No records' warning in list
     *
     * @param $table
     *
     * @private
     */

  }, {
    key: '_renderEmptyList',
    value: function _renderEmptyList($table) {
      var $emptyTableRow = $($(_createOrderMap2.default.emptyListRowTemplate).html()).clone();
      $table.find('tbody').append($emptyTableRow);
    }

    /**
     * Removes empty list row in case it was rendered
     */

  }, {
    key: '_removeEmptyListRowFromTable',
    value: function _removeEmptyListRowFromTable($table) {
      $table.find(_createOrderMap2.default.emptyListRow).remove();
    }

    /**
     * Renders customer information after search action
     *
     * @param {Object} customer
     *
     * @return {jQuery}
     *
     * @private
     */

  }, {
    key: '_renderFoundCustomer',
    value: function _renderFoundCustomer(customer) {
      this.hideNotFoundCustomers();

      var $customerSearchResultTemplate = $($(_createOrderMap2.default.customerSearchResultTemplate).html());
      var $template = $customerSearchResultTemplate.clone();

      $template.find(_createOrderMap2.default.customerSearchResultName).text(customer.firstName + ' ' + customer.lastName);
      $template.find(_createOrderMap2.default.customerSearchResultEmail).text(customer.email);
      $template.find(_createOrderMap2.default.customerSearchResultId).text(customer.id);
      $template.find(_createOrderMap2.default.customerSearchResultBirthday).text(customer.birthday);
      $template.find(_createOrderMap2.default.chooseCustomerBtn).data('customer-id', customer.id);
      $template.find(_createOrderMap2.default.customerDetailsBtn).prop('href', this.router.generate('admin_customers_view', { customerId: customer.id }));

      return this.$customerSearchResultBlock.append($template);
    }

    /**
     * Clears shown customers
     */

  }, {
    key: 'clearShownCustomers',
    value: function clearShownCustomers() {
      this.$customerSearchResultBlock.empty();
    }
  }]);
  return CustomerRenderer;
}();

exports.default = CustomerRenderer;

/***/ }),

/***/ 514:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _keys = __webpack_require__(67);

var _keys2 = _interopRequireDefault(_keys);

var _values = __webpack_require__(193);

var _values2 = _interopRequireDefault(_values);

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _cartEditor = __webpack_require__(196);

var _cartEditor2 = _interopRequireDefault(_cartEditor);

var _createOrderMap = __webpack_require__(106);

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

var _eventMap = __webpack_require__(133);

var _eventMap2 = _interopRequireDefault(_eventMap);

var _eventEmitter = __webpack_require__(36);

var _productRenderer = __webpack_require__(226);

var _productRenderer2 = _interopRequireDefault(_productRenderer);

var _router = __webpack_require__(73);

var _router2 = _interopRequireDefault(_router);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

var $ = window.$;

/**
 * Product component Object for "Create order" page
 */

var ProductManager = function () {
  function ProductManager() {
    var _this = this;

    (0, _classCallCheck3.default)(this, ProductManager);

    this.products = [];
    this.selectedProduct = null;
    this.selectedCombinationId = null;
    this.activeSearchRequest = null;

    this.productRenderer = new _productRenderer2.default();
    this.router = new _router2.default();
    this.cartEditor = new _cartEditor2.default();

    this._initListeners();

    return {
      search: function search(searchPhrase) {
        return _this._search(searchPhrase);
      },

      addProductToCart: function addProductToCart(cartId) {
        return _this.cartEditor.addProduct(cartId, _this._getProductData());
      },

      removeProductFromCart: function removeProductFromCart(cartId, product) {
        return _this.cartEditor.removeProductFromCart(cartId, product);
      },

      changeProductPrice: function changeProductPrice(cartId, customerId, updatedProduct) {
        return _this.cartEditor.changeProductPrice(cartId, customerId, updatedProduct);
      },

      changeProductQty: function changeProductQty(cartId, updatedProduct) {
        return _this.cartEditor.changeProductQty(cartId, updatedProduct);
      }
    };
  }

  /**
   * Initializes event listeners
   *
   * @private
   */


  (0, _createClass3.default)(ProductManager, [{
    key: '_initListeners',
    value: function _initListeners() {
      var _this2 = this;

      $(_createOrderMap2.default.productSelect).on('change', function (e) {
        return _this2._initProductSelect(e);
      });
      $(_createOrderMap2.default.combinationsSelect).on('change', function (e) {
        return _this2._initCombinationSelect(e);
      });

      this._onProductSearch();
      this._onAddProductToCart();
      this._onRemoveProductFromCart();
      this._onProductPriceChange();
      this._onProductQtyChange();
    }

    /**
     * Listens for product search event
     *
     * @private
     */

  }, {
    key: '_onProductSearch',
    value: function _onProductSearch() {
      var _this3 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.productSearched, function (response) {
        _this3.products = response.products;
        _this3.productRenderer.renderSearchResults(_this3.products);
        _this3._selectFirstResult();
      });
    }

    /**
     * Listens for add product to cart event
     *
     * @private
     */

  }, {
    key: '_onAddProductToCart',
    value: function _onAddProductToCart() {
      var _this4 = this;

      // on success
      _eventEmitter.EventEmitter.on(_eventMap2.default.productAddedToCart, function (cartInfo) {
        _this4.productRenderer.cleanCartBlockAlerts();
        _eventEmitter.EventEmitter.emit(_eventMap2.default.cartLoaded, cartInfo);
      });

      // on failure
      _eventEmitter.EventEmitter.on(_eventMap2.default.productAddToCartFailed, function (errorMessage) {
        _this4.productRenderer.renderCartBlockErrorAlert(errorMessage);
      });
    }

    /**
     * Listens for remove product from cart event
     *
     * @private
     */

  }, {
    key: '_onRemoveProductFromCart',
    value: function _onRemoveProductFromCart() {
      _eventEmitter.EventEmitter.on(_eventMap2.default.productRemovedFromCart, function (cartInfo) {
        _eventEmitter.EventEmitter.emit(_eventMap2.default.cartLoaded, cartInfo);
      });
    }

    /**
     * Listens for product price change in cart event
     *
     * @private
     */

  }, {
    key: '_onProductPriceChange',
    value: function _onProductPriceChange() {
      var _this5 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.productPriceChanged, function (cartInfo) {
        _this5.productRenderer.cleanCartBlockAlerts();
        _eventEmitter.EventEmitter.emit(_eventMap2.default.cartLoaded, cartInfo);
      });
    }

    /**
     * Listens for product quantity change in cart success/failure event
     *
     * @private
     */

  }, {
    key: '_onProductQtyChange',
    value: function _onProductQtyChange() {
      var _this6 = this;

      // on success
      _eventEmitter.EventEmitter.on(_eventMap2.default.productQtyChanged, function (cartInfo) {
        _this6.productRenderer.cleanCartBlockAlerts();
        _eventEmitter.EventEmitter.emit(_eventMap2.default.cartLoaded, cartInfo);
      });

      // on failure
      _eventEmitter.EventEmitter.on(_eventMap2.default.productQtyChangeFailed, function (e) {
        _this6.productRenderer.renderCartBlockErrorAlert(e.responseJSON.message);
      });
    }

    /**
     * Initializes product select
     *
     * @param event
     *
     * @private
     */

  }, {
    key: '_initProductSelect',
    value: function _initProductSelect(event) {
      var productId = Number($(event.currentTarget).find(':selected').val());
      this._selectProduct(productId);
    }

    /**
     * Initializes combination select
     *
     * @param event
     *
     * @private
     */

  }, {
    key: '_initCombinationSelect',
    value: function _initCombinationSelect(event) {
      var combinationId = Number($(event.currentTarget).find(':selected').val());
      this._selectCombination(combinationId);
    }

    /**
     * Searches for product
     *
     * @private
     */

  }, {
    key: '_search',
    value: function _search(searchPhrase) {
      if (searchPhrase.length < 3) {
        return;
      }

      if (this.activeSearchRequest !== null) {
        this.activeSearchRequest.abort();
      }

      var params = {
        search_phrase: searchPhrase
      };
      if ($(_createOrderMap2.default.cartCurrencySelect).data('selectedCurrencyId') != undefined) {
        params.currency_id = $(_createOrderMap2.default.cartCurrencySelect).data('selectedCurrencyId');
      }

      var $searchRequest = $.get(this.router.generate('admin_products_search'), params);
      this.activeSearchRequest = $searchRequest;

      $searchRequest.then(function (response) {
        _eventEmitter.EventEmitter.emit(_eventMap2.default.productSearched, response);
      }).catch(function (response) {
        if (response.statusText === 'abort') {
          return;
        }

        showErrorMessage(response.responseJSON.message);
      });
    }

    /**
     * Initiate first result dataset after search
     *
     * @private
     */

  }, {
    key: '_selectFirstResult',
    value: function _selectFirstResult() {
      this._unsetProduct();

      var values = (0, _values2.default)(this.products);

      if (values.length !== 0) {
        this._selectProduct(values[0].productId);
      }
    }

    /**
     * Handles use case when product is selected from search results
     *
     * @private
     *
     * @param {Number} productId
     */

  }, {
    key: '_selectProduct',
    value: function _selectProduct(productId) {
      this._unsetCombination();

      for (var key in this.products) {
        if (this.products[key].productId === productId) {
          this.selectedProduct = this.products[key];

          break;
        }
      }

      this.productRenderer.renderProductMetadata(this.selectedProduct);
      // if product has combinations select the first else leave it null
      if (this.selectedProduct.combinations.length !== 0) {
        this._selectCombination((0, _keys2.default)(this.selectedProduct.combinations)[0]);
      }

      return this.selectedProduct;
    }

    /**
     * Handles use case when new combination is selected
     *
     * @param combinationId
     *
     * @private
     */

  }, {
    key: '_selectCombination',
    value: function _selectCombination(combinationId) {
      var combination = this.selectedProduct.combinations[combinationId];

      this.selectedCombinationId = combinationId;
      this.productRenderer.renderStock(combination.stock);

      return combination;
    }

    /**
     * Sets the selected combination id to null
     *
     * @private
     */

  }, {
    key: '_unsetCombination',
    value: function _unsetCombination() {
      this.selectedCombinationId = null;
    }

    /**
     * Sets the selected product to null
     *
     * @private
     */

  }, {
    key: '_unsetProduct',
    value: function _unsetProduct() {
      this.selectedProduct = null;
    }

    /**
     * Retrieves product data from product search result block fields
     *
     * @returns {Object}
     *
     * @private
     */

  }, {
    key: '_getProductData',
    value: function _getProductData() {
      var $fileInputs = $(_createOrderMap2.default.productCustomizationContainer).find('input[type="file"]');
      var formData = new FormData(document.querySelector(_createOrderMap2.default.productAddForm));
      var fileSizes = {};

      // adds key value pairs {input name: file size} of each file in separate object in case formData size exceeds server settings.
      $.each($fileInputs, function (key, input) {
        if (input.files.length !== 0) {
          fileSizes[$(input).data('customization-field-id')] = input.files[0].size;
        }
      });

      return {
        product: formData,
        fileSizes: fileSizes
      };
    }
  }]);
  return ProductManager;
}();

exports.default = ProductManager;

/***/ }),

/***/ 515:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _eventEmitter = __webpack_require__(36);

var _eventMap = __webpack_require__(133);

var _eventMap2 = _interopRequireDefault(_eventMap);

var _summaryRenderer = __webpack_require__(197);

var _summaryRenderer2 = _interopRequireDefault(_summaryRenderer);

var _router = __webpack_require__(73);

var _router2 = _interopRequireDefault(_router);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

var $ = window.$;

/**
 * Manages summary block
 */

var SummaryManager = function () {
  function SummaryManager() {
    var _this = this;

    (0, _classCallCheck3.default)(this, SummaryManager);

    this.router = new _router2.default();
    this.summaryRenderer = new _summaryRenderer2.default();
    this._initListeners();

    return {
      sendProcessOrderEmail: function sendProcessOrderEmail(cartId) {
        return _this._sendProcessOrderEmail(cartId);
      }
    };
  }

  /**
   * Inits event listeners
   *
   * @private
   */


  (0, _createClass3.default)(SummaryManager, [{
    key: '_initListeners',
    value: function _initListeners() {
      this._onProcessOrderEmailError();
      this._onProcessOrderEmailSuccess();
    }

    /**
     * Listens for process order email sending success event
     *
     * @private
     */

  }, {
    key: '_onProcessOrderEmailSuccess',
    value: function _onProcessOrderEmailSuccess() {
      var _this2 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.processOrderEmailSent, function (response) {
        _this2.summaryRenderer.cleanAlerts();
        _this2.summaryRenderer.renderSuccessMessage(response.message);
      });
    }

    /**
     * Listens for process order email failed event
     *
     * @private
     */

  }, {
    key: '_onProcessOrderEmailError',
    value: function _onProcessOrderEmailError() {
      var _this3 = this;

      _eventEmitter.EventEmitter.on(_eventMap2.default.processOrderEmailFailed, function (response) {
        _this3.summaryRenderer.cleanAlerts();
        _this3.summaryRenderer.renderErrorMessage(response.responseJSON.message);
      });
    }

    /**
     * Sends email to customer with link of order processing
     *
     * @param {Number} cartId
     */

  }, {
    key: '_sendProcessOrderEmail',
    value: function _sendProcessOrderEmail(cartId) {
      $.post(this.router.generate('admin_orders_send_process_order_email'), {
        cartId: cartId
      }).then(function (response) {
        return _eventEmitter.EventEmitter.emit(_eventMap2.default.processOrderEmailSent, response);
      }).catch(function (e) {
        _eventEmitter.EventEmitter.emit(_eventMap2.default.processOrderEmailFailed, e);
      });
    }
  }]);
  return SummaryManager;
}();

exports.default = SummaryManager;

/***/ }),

/***/ 52:
/***/ (function(module, exports) {

exports.f = {}.propertyIsEnumerable;

/***/ }),

/***/ 53:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
// Copyright Joyent, Inc. and other Node contributors.
//
// Permission is hereby granted, free of charge, to any person obtaining a
// copy of this software and associated documentation files (the
// "Software"), to deal in the Software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of the Software, and to permit
// persons to whom the Software is furnished to do so, subject to the
// following conditions:
//
// The above copyright notice and this permission notice shall be included
// in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
// USE OR OTHER DEALINGS IN THE SOFTWARE.



var R = typeof Reflect === 'object' ? Reflect : null
var ReflectApply = R && typeof R.apply === 'function'
  ? R.apply
  : function ReflectApply(target, receiver, args) {
    return Function.prototype.apply.call(target, receiver, args);
  }

var ReflectOwnKeys
if (R && typeof R.ownKeys === 'function') {
  ReflectOwnKeys = R.ownKeys
} else if (Object.getOwnPropertySymbols) {
  ReflectOwnKeys = function ReflectOwnKeys(target) {
    return Object.getOwnPropertyNames(target)
      .concat(Object.getOwnPropertySymbols(target));
  };
} else {
  ReflectOwnKeys = function ReflectOwnKeys(target) {
    return Object.getOwnPropertyNames(target);
  };
}

function ProcessEmitWarning(warning) {
  if (console && console.warn) console.warn(warning);
}

var NumberIsNaN = Number.isNaN || function NumberIsNaN(value) {
  return value !== value;
}

function EventEmitter() {
  EventEmitter.init.call(this);
}
module.exports = EventEmitter;

// Backwards-compat with node 0.10.x
EventEmitter.EventEmitter = EventEmitter;

EventEmitter.prototype._events = undefined;
EventEmitter.prototype._eventsCount = 0;
EventEmitter.prototype._maxListeners = undefined;

// By default EventEmitters will print a warning if more than 10 listeners are
// added to it. This is a useful default which helps finding memory leaks.
var defaultMaxListeners = 10;

Object.defineProperty(EventEmitter, 'defaultMaxListeners', {
  enumerable: true,
  get: function() {
    return defaultMaxListeners;
  },
  set: function(arg) {
    if (typeof arg !== 'number' || arg < 0 || NumberIsNaN(arg)) {
      throw new RangeError('The value of "defaultMaxListeners" is out of range. It must be a non-negative number. Received ' + arg + '.');
    }
    defaultMaxListeners = arg;
  }
});

EventEmitter.init = function() {

  if (this._events === undefined ||
      this._events === Object.getPrototypeOf(this)._events) {
    this._events = Object.create(null);
    this._eventsCount = 0;
  }

  this._maxListeners = this._maxListeners || undefined;
};

// Obviously not all Emitters should be limited to 10. This function allows
// that to be increased. Set to zero for unlimited.
EventEmitter.prototype.setMaxListeners = function setMaxListeners(n) {
  if (typeof n !== 'number' || n < 0 || NumberIsNaN(n)) {
    throw new RangeError('The value of "n" is out of range. It must be a non-negative number. Received ' + n + '.');
  }
  this._maxListeners = n;
  return this;
};

function $getMaxListeners(that) {
  if (that._maxListeners === undefined)
    return EventEmitter.defaultMaxListeners;
  return that._maxListeners;
}

EventEmitter.prototype.getMaxListeners = function getMaxListeners() {
  return $getMaxListeners(this);
};

EventEmitter.prototype.emit = function emit(type) {
  var args = [];
  for (var i = 1; i < arguments.length; i++) args.push(arguments[i]);
  var doError = (type === 'error');

  var events = this._events;
  if (events !== undefined)
    doError = (doError && events.error === undefined);
  else if (!doError)
    return false;

  // If there is no 'error' event listener then throw.
  if (doError) {
    var er;
    if (args.length > 0)
      er = args[0];
    if (er instanceof Error) {
      // Note: The comments on the `throw` lines are intentional, they show
      // up in Node's output if this results in an unhandled exception.
      throw er; // Unhandled 'error' event
    }
    // At least give some kind of context to the user
    var err = new Error('Unhandled error.' + (er ? ' (' + er.message + ')' : ''));
    err.context = er;
    throw err; // Unhandled 'error' event
  }

  var handler = events[type];

  if (handler === undefined)
    return false;

  if (typeof handler === 'function') {
    ReflectApply(handler, this, args);
  } else {
    var len = handler.length;
    var listeners = arrayClone(handler, len);
    for (var i = 0; i < len; ++i)
      ReflectApply(listeners[i], this, args);
  }

  return true;
};

function _addListener(target, type, listener, prepend) {
  var m;
  var events;
  var existing;

  if (typeof listener !== 'function') {
    throw new TypeError('The "listener" argument must be of type Function. Received type ' + typeof listener);
  }

  events = target._events;
  if (events === undefined) {
    events = target._events = Object.create(null);
    target._eventsCount = 0;
  } else {
    // To avoid recursion in the case that type === "newListener"! Before
    // adding it to the listeners, first emit "newListener".
    if (events.newListener !== undefined) {
      target.emit('newListener', type,
                  listener.listener ? listener.listener : listener);

      // Re-assign `events` because a newListener handler could have caused the
      // this._events to be assigned to a new object
      events = target._events;
    }
    existing = events[type];
  }

  if (existing === undefined) {
    // Optimize the case of one listener. Don't need the extra array object.
    existing = events[type] = listener;
    ++target._eventsCount;
  } else {
    if (typeof existing === 'function') {
      // Adding the second element, need to change to array.
      existing = events[type] =
        prepend ? [listener, existing] : [existing, listener];
      // If we've already got an array, just append.
    } else if (prepend) {
      existing.unshift(listener);
    } else {
      existing.push(listener);
    }

    // Check for listener leak
    m = $getMaxListeners(target);
    if (m > 0 && existing.length > m && !existing.warned) {
      existing.warned = true;
      // No error code for this since it is a Warning
      // eslint-disable-next-line no-restricted-syntax
      var w = new Error('Possible EventEmitter memory leak detected. ' +
                          existing.length + ' ' + String(type) + ' listeners ' +
                          'added. Use emitter.setMaxListeners() to ' +
                          'increase limit');
      w.name = 'MaxListenersExceededWarning';
      w.emitter = target;
      w.type = type;
      w.count = existing.length;
      ProcessEmitWarning(w);
    }
  }

  return target;
}

EventEmitter.prototype.addListener = function addListener(type, listener) {
  return _addListener(this, type, listener, false);
};

EventEmitter.prototype.on = EventEmitter.prototype.addListener;

EventEmitter.prototype.prependListener =
    function prependListener(type, listener) {
      return _addListener(this, type, listener, true);
    };

function onceWrapper() {
  var args = [];
  for (var i = 0; i < arguments.length; i++) args.push(arguments[i]);
  if (!this.fired) {
    this.target.removeListener(this.type, this.wrapFn);
    this.fired = true;
    ReflectApply(this.listener, this.target, args);
  }
}

function _onceWrap(target, type, listener) {
  var state = { fired: false, wrapFn: undefined, target: target, type: type, listener: listener };
  var wrapped = onceWrapper.bind(state);
  wrapped.listener = listener;
  state.wrapFn = wrapped;
  return wrapped;
}

EventEmitter.prototype.once = function once(type, listener) {
  if (typeof listener !== 'function') {
    throw new TypeError('The "listener" argument must be of type Function. Received type ' + typeof listener);
  }
  this.on(type, _onceWrap(this, type, listener));
  return this;
};

EventEmitter.prototype.prependOnceListener =
    function prependOnceListener(type, listener) {
      if (typeof listener !== 'function') {
        throw new TypeError('The "listener" argument must be of type Function. Received type ' + typeof listener);
      }
      this.prependListener(type, _onceWrap(this, type, listener));
      return this;
    };

// Emits a 'removeListener' event if and only if the listener was removed.
EventEmitter.prototype.removeListener =
    function removeListener(type, listener) {
      var list, events, position, i, originalListener;

      if (typeof listener !== 'function') {
        throw new TypeError('The "listener" argument must be of type Function. Received type ' + typeof listener);
      }

      events = this._events;
      if (events === undefined)
        return this;

      list = events[type];
      if (list === undefined)
        return this;

      if (list === listener || list.listener === listener) {
        if (--this._eventsCount === 0)
          this._events = Object.create(null);
        else {
          delete events[type];
          if (events.removeListener)
            this.emit('removeListener', type, list.listener || listener);
        }
      } else if (typeof list !== 'function') {
        position = -1;

        for (i = list.length - 1; i >= 0; i--) {
          if (list[i] === listener || list[i].listener === listener) {
            originalListener = list[i].listener;
            position = i;
            break;
          }
        }

        if (position < 0)
          return this;

        if (position === 0)
          list.shift();
        else {
          spliceOne(list, position);
        }

        if (list.length === 1)
          events[type] = list[0];

        if (events.removeListener !== undefined)
          this.emit('removeListener', type, originalListener || listener);
      }

      return this;
    };

EventEmitter.prototype.off = EventEmitter.prototype.removeListener;

EventEmitter.prototype.removeAllListeners =
    function removeAllListeners(type) {
      var listeners, events, i;

      events = this._events;
      if (events === undefined)
        return this;

      // not listening for removeListener, no need to emit
      if (events.removeListener === undefined) {
        if (arguments.length === 0) {
          this._events = Object.create(null);
          this._eventsCount = 0;
        } else if (events[type] !== undefined) {
          if (--this._eventsCount === 0)
            this._events = Object.create(null);
          else
            delete events[type];
        }
        return this;
      }

      // emit removeListener for all listeners on all events
      if (arguments.length === 0) {
        var keys = Object.keys(events);
        var key;
        for (i = 0; i < keys.length; ++i) {
          key = keys[i];
          if (key === 'removeListener') continue;
          this.removeAllListeners(key);
        }
        this.removeAllListeners('removeListener');
        this._events = Object.create(null);
        this._eventsCount = 0;
        return this;
      }

      listeners = events[type];

      if (typeof listeners === 'function') {
        this.removeListener(type, listeners);
      } else if (listeners !== undefined) {
        // LIFO order
        for (i = listeners.length - 1; i >= 0; i--) {
          this.removeListener(type, listeners[i]);
        }
      }

      return this;
    };

function _listeners(target, type, unwrap) {
  var events = target._events;

  if (events === undefined)
    return [];

  var evlistener = events[type];
  if (evlistener === undefined)
    return [];

  if (typeof evlistener === 'function')
    return unwrap ? [evlistener.listener || evlistener] : [evlistener];

  return unwrap ?
    unwrapListeners(evlistener) : arrayClone(evlistener, evlistener.length);
}

EventEmitter.prototype.listeners = function listeners(type) {
  return _listeners(this, type, true);
};

EventEmitter.prototype.rawListeners = function rawListeners(type) {
  return _listeners(this, type, false);
};

EventEmitter.listenerCount = function(emitter, type) {
  if (typeof emitter.listenerCount === 'function') {
    return emitter.listenerCount(type);
  } else {
    return listenerCount.call(emitter, type);
  }
};

EventEmitter.prototype.listenerCount = listenerCount;
function listenerCount(type) {
  var events = this._events;

  if (events !== undefined) {
    var evlistener = events[type];

    if (typeof evlistener === 'function') {
      return 1;
    } else if (evlistener !== undefined) {
      return evlistener.length;
    }
  }

  return 0;
}

EventEmitter.prototype.eventNames = function eventNames() {
  return this._eventsCount > 0 ? ReflectOwnKeys(this._events) : [];
};

function arrayClone(arr, n) {
  var copy = new Array(n);
  for (var i = 0; i < n; ++i)
    copy[i] = arr[i];
  return copy;
}

function spliceOne(list, index) {
  for (; index + 1 < list.length; index++)
    list[index] = list[index + 1];
  list.pop();
}

function unwrapListeners(arr) {
  var ret = new Array(arr.length);
  for (var i = 0; i < ret.length; ++i) {
    ret[i] = arr[i].listener || arr[i];
  }
  return ret;
}


/***/ }),

/***/ 55:
/***/ (function(module, exports, __webpack_require__) {

var has          = __webpack_require__(27)
  , toIObject    = __webpack_require__(22)
  , arrayIndexOf = __webpack_require__(58)(false)
  , IE_PROTO     = __webpack_require__(46)('IE_PROTO');

module.exports = function(object, names){
  var O      = toIObject(object)
    , i      = 0
    , result = []
    , key;
  for(key in O)if(key != IE_PROTO)has(O, key) && result.push(key);
  // Don't enum bug & hidden keys
  while(names.length > i)if(has(O, key = names[i++])){
    ~arrayIndexOf(result, key) || result.push(key);
  }
  return result;
};

/***/ }),

/***/ 56:
/***/ (function(module, exports, __webpack_require__) {

// 7.1.15 ToLength
var toInteger = __webpack_require__(39)
  , min       = Math.min;
module.exports = function(it){
  return it > 0 ? min(toInteger(it), 0x1fffffffffffff) : 0; // pow(2, 53) - 1 == 9007199254740991
};

/***/ }),

/***/ 57:
/***/ (function(module, exports) {

exports.f = Object.getOwnPropertySymbols;

/***/ }),

/***/ 58:
/***/ (function(module, exports, __webpack_require__) {

// false -> Array#indexOf
// true  -> Array#includes
var toIObject = __webpack_require__(22)
  , toLength  = __webpack_require__(56)
  , toIndex   = __webpack_require__(59);
module.exports = function(IS_INCLUDES){
  return function($this, el, fromIndex){
    var O      = toIObject($this)
      , length = toLength(O.length)
      , index  = toIndex(fromIndex, length)
      , value;
    // Array#includes uses SameValueZero equality algorithm
    if(IS_INCLUDES && el != el)while(length > index){
      value = O[index++];
      if(value != value)return true;
    // Array#toIndex ignores holes, Array#includes - not
    } else for(;length > index; index++)if(IS_INCLUDES || index in O){
      if(O[index] === el)return IS_INCLUDES || index || 0;
    } return !IS_INCLUDES && -1;
  };
};

/***/ }),

/***/ 583:
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(587), __esModule: true };

/***/ }),

/***/ 587:
/***/ (function(module, exports, __webpack_require__) {

var core  = __webpack_require__(3)
  , $JSON = core.JSON || (core.JSON = {stringify: JSON.stringify});
module.exports = function stringify(it){ // eslint-disable-line no-unused-vars
  return $JSON.stringify.apply($JSON, arguments);
};

/***/ }),

/***/ 59:
/***/ (function(module, exports, __webpack_require__) {

var toInteger = __webpack_require__(39)
  , max       = Math.max
  , min       = Math.min;
module.exports = function(index, length){
  index = toInteger(index);
  return index < 0 ? max(index + length, 0) : min(index, length);
};

/***/ }),

/***/ 6:
/***/ (function(module, exports, __webpack_require__) {

var anObject       = __webpack_require__(11)
  , IE8_DOM_DEFINE = __webpack_require__(17)
  , toPrimitive    = __webpack_require__(13)
  , dP             = Object.defineProperty;

exports.f = __webpack_require__(2) ? Object.defineProperty : function defineProperty(O, P, Attributes){
  anObject(O);
  P = toPrimitive(P, true);
  anObject(Attributes);
  if(IE8_DOM_DEFINE)try {
    return dP(O, P, Attributes);
  } catch(e){ /* empty */ }
  if('get' in Attributes || 'set' in Attributes)throw TypeError('Accessors not supported!');
  if('value' in Attributes)O[P] = Attributes.value;
  return O;
};

/***/ }),

/***/ 67:
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(84), __esModule: true };

/***/ }),

/***/ 7:
/***/ (function(module, exports) {

module.exports = function(exec){
  try {
    return !!exec();
  } catch(e){
    return true;
  }
};

/***/ }),

/***/ 73:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _assign = __webpack_require__(82);

var _assign2 = _interopRequireDefault(_assign);

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _fosRouting = __webpack_require__(178);

var _fosRouting2 = _interopRequireDefault(_fosRouting);

var _fos_js_routes = __webpack_require__(161);

var _fos_js_routes2 = _interopRequireDefault(_fos_js_routes);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

var $ = window.$;

/**
 * Wraps FOSJsRoutingbundle with exposed routes.
 * To expose route add option `expose: true` in .yml routing config
 *
 * e.g.
 *
 * `my_route
 *    path: /my-path
 *    options:
 *      expose: true
 * `
 * And run `bin/console fos:js-routing:dump --format=json --target=admin-dev/themes/new-theme/js/fos_js_routes.json`
 */

var Router = function () {
  function Router() {
    (0, _classCallCheck3.default)(this, Router);

    _fosRouting2.default.setData(_fos_js_routes2.default);
    _fosRouting2.default.setBaseUrl($(document).find('body').data('base-url'));

    return this;
  }

  /**
   * Decorated "generate" method, with predefined security token in params
   *
   * @param route
   * @param params
   *
   * @returns {String}
   */


  (0, _createClass3.default)(Router, [{
    key: 'generate',
    value: function generate(route) {
      var params = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

      var tokenizedParams = (0, _assign2.default)(params, { _token: $(document).find('body').data('token') });

      return _fosRouting2.default.generate(route, tokenizedParams);
    }
  }]);
  return Router;
}();

exports.default = Router;

/***/ }),

/***/ 77:
/***/ (function(module, exports, __webpack_require__) {

// most Object methods by ES6 should accept primitives
var $export = __webpack_require__(8)
  , core    = __webpack_require__(3)
  , fails   = __webpack_require__(7);
module.exports = function(KEY, exec){
  var fn  = (core.Object || {})[KEY] || Object[KEY]
    , exp = {};
  exp[KEY] = exec(fn);
  $export($export.S + $export.F * fails(function(){ fn(1); }), 'Object', exp);
};

/***/ }),

/***/ 8:
/***/ (function(module, exports, __webpack_require__) {

var global    = __webpack_require__(5)
  , core      = __webpack_require__(3)
  , ctx       = __webpack_require__(15)
  , hide      = __webpack_require__(10)
  , PROTOTYPE = 'prototype';

var $export = function(type, name, source){
  var IS_FORCED = type & $export.F
    , IS_GLOBAL = type & $export.G
    , IS_STATIC = type & $export.S
    , IS_PROTO  = type & $export.P
    , IS_BIND   = type & $export.B
    , IS_WRAP   = type & $export.W
    , exports   = IS_GLOBAL ? core : core[name] || (core[name] = {})
    , expProto  = exports[PROTOTYPE]
    , target    = IS_GLOBAL ? global : IS_STATIC ? global[name] : (global[name] || {})[PROTOTYPE]
    , key, own, out;
  if(IS_GLOBAL)source = name;
  for(key in source){
    // contains in native
    own = !IS_FORCED && target && target[key] !== undefined;
    if(own && key in exports)continue;
    // export native or passed
    out = own ? target[key] : source[key];
    // prevent global pollution for namespaces
    exports[key] = IS_GLOBAL && typeof target[key] != 'function' ? source[key]
    // bind timers to global for call from export context
    : IS_BIND && own ? ctx(out, global)
    // wrap global constructors for prevent change them in library
    : IS_WRAP && target[key] == out ? (function(C){
      var F = function(a, b, c){
        if(this instanceof C){
          switch(arguments.length){
            case 0: return new C;
            case 1: return new C(a);
            case 2: return new C(a, b);
          } return new C(a, b, c);
        } return C.apply(this, arguments);
      };
      F[PROTOTYPE] = C[PROTOTYPE];
      return F;
    // make static versions for prototype methods
    })(out) : IS_PROTO && typeof out == 'function' ? ctx(Function.call, out) : out;
    // export proto methods to core.%CONSTRUCTOR%.methods.%NAME%
    if(IS_PROTO){
      (exports.virtual || (exports.virtual = {}))[key] = out;
      // export proto methods to core.%CONSTRUCTOR%.prototype.%NAME%
      if(type & $export.R && expProto && !expProto[key])hide(expProto, key, out);
    }
  }
};
// type bitmap
$export.F = 1;   // forced
$export.G = 2;   // global
$export.S = 4;   // static
$export.P = 8;   // proto
$export.B = 16;  // bind
$export.W = 32;  // wrap
$export.U = 64;  // safe
$export.R = 128; // real proto method for `library` 
module.exports = $export;

/***/ }),

/***/ 82:
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(83), __esModule: true };

/***/ }),

/***/ 83:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(87);
module.exports = __webpack_require__(3).Object.assign;

/***/ }),

/***/ 84:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(88);
module.exports = __webpack_require__(3).Object.keys;

/***/ }),

/***/ 85:
/***/ (function(module, exports, __webpack_require__) {

"use strict";

// 19.1.2.1 Object.assign(target, source, ...)
var getKeys  = __webpack_require__(34)
  , gOPS     = __webpack_require__(57)
  , pIE      = __webpack_require__(52)
  , toObject = __webpack_require__(45)
  , IObject  = __webpack_require__(51)
  , $assign  = Object.assign;

// should work with symbols and should have deterministic property order (V8 bug)
module.exports = !$assign || __webpack_require__(7)(function(){
  var A = {}
    , B = {}
    , S = Symbol()
    , K = 'abcdefghijklmnopqrst';
  A[S] = 7;
  K.split('').forEach(function(k){ B[k] = k; });
  return $assign({}, A)[S] != 7 || Object.keys($assign({}, B)).join('') != K;
}) ? function assign(target, source){ // eslint-disable-line no-unused-vars
  var T     = toObject(target)
    , aLen  = arguments.length
    , index = 1
    , getSymbols = gOPS.f
    , isEnum     = pIE.f;
  while(aLen > index){
    var S      = IObject(arguments[index++])
      , keys   = getSymbols ? getKeys(S).concat(getSymbols(S)) : getKeys(S)
      , length = keys.length
      , j      = 0
      , key;
    while(length > j)if(isEnum.call(S, key = keys[j++]))T[key] = S[key];
  } return T;
} : $assign;

/***/ }),

/***/ 87:
/***/ (function(module, exports, __webpack_require__) {

// 19.1.3.1 Object.assign(target, source)
var $export = __webpack_require__(8);

$export($export.S + $export.F, 'Object', {assign: __webpack_require__(85)});

/***/ }),

/***/ 88:
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.14 Object.keys(O)
var toObject = __webpack_require__(45)
  , $keys    = __webpack_require__(34);

__webpack_require__(77)('keys', function(){
  return function keys(it){
    return $keys(toObject(it));
  };
});

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgMDJkZmU4ZmE2YzAzMTAwYzc2OTUiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2hlbHBlcnMvY2xhc3NDYWxsQ2hlY2suanMiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2hlbHBlcnMvY3JlYXRlQ2xhc3MuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faGlkZS5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvY3JlYXRlLW9yZGVyLW1hcC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19hbi1vYmplY3QuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fcHJvcGVydHktZGVzYy5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL190by1wcmltaXRpdmUuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvb3JkZXIvY3JlYXRlL2V2ZW50LW1hcC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19jdHguanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZG9tLWNyZWF0ZS5qcyIsIndlYnBhY2s6Ly8vLi9qcy9mb3NfanNfcm91dGVzLmpzb24iLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2hlbHBlcnMvZGVmaW5lUHJvcGVydHkuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faWU4LWRvbS1kZWZpbmUuanMiLCJ3ZWJwYWNrOi8vLy4vfi9mb3Mtcm91dGluZy9kaXN0L3JvdXRpbmcuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fYS1mdW5jdGlvbi5qcyIsIndlYnBhY2s6Ly8vLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9vYmplY3QvZGVmaW5lLXByb3BlcnR5LmpzIiwid2VicGFjazovLy8uL34vYmFiZWwtcnVudGltZS9jb3JlLWpzL29iamVjdC92YWx1ZXMuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvb3JkZXIvY3JlYXRlL2NhcnQtZWRpdG9yLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9zdW1tYXJ5LXJlbmRlcmVyLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2Rlc2NyaXB0b3JzLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC9kZWZpbmUtcHJvcGVydHkuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L3ZhbHVlcy5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtdG8tYXJyYXkuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczcub2JqZWN0LnZhbHVlcy5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5vYmplY3QuZGVmaW5lLXByb3BlcnR5LmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3RvLWlvYmplY3QuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvb3JkZXIvY3JlYXRlL2NyZWF0ZS1vcmRlci1wYWdlLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9jYXJ0LXJ1bGVzLXJlbmRlcmVyLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9wcm9kdWN0LXJlbmRlcmVyLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9zaGlwcGluZy1yZW5kZXJlci5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19oYXMuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fY29yZS5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3Qta2V5cy5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2V2ZW50LWVtaXR0ZXIuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZGVmaW5lZC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL190by1pbnRlZ2VyLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2lzLW9iamVjdC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL191aWQuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fdG8tb2JqZWN0LmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3NoYXJlZC1rZXkuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fY29mLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2VudW0tYnVnLWtleXMuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fc2hhcmVkLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2dsb2JhbC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvb3JkZXIvY3JlYXRlL2FkZHJlc3Nlcy1yZW5kZXJlci5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pb2JqZWN0LmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9jYXJ0LXByb3ZpZGVyLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9jYXJ0LXJ1bGUtbWFuYWdlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvY3VzdG9tZXItbWFuYWdlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvY3VzdG9tZXItcmVuZGVyZXIuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvb3JkZXIvY3JlYXRlL3Byb2R1Y3QtbWFuYWdlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvc3VtbWFyeS1tYW5hZ2VyLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1waWUuanMiLCJ3ZWJwYWNrOi8vLy4vfi9ldmVudHMvZXZlbnRzLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1rZXlzLWludGVybmFsLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3RvLWxlbmd0aC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtZ29wcy5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19hcnJheS1pbmNsdWRlcy5qcyIsIndlYnBhY2s6Ly8vLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9qc29uL3N0cmluZ2lmeS5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9mbi9qc29uL3N0cmluZ2lmeS5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL190by1pbmRleC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtZHAuanMiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvb2JqZWN0L2tleXMuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZmFpbHMuanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9yb3V0ZXIuanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LXNhcC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19leHBvcnQuanMiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvb2JqZWN0L2Fzc2lnbi5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9mbi9vYmplY3QvYXNzaWduLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC9rZXlzLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1hc3NpZ24uanMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczYub2JqZWN0LmFzc2lnbi5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5vYmplY3Qua2V5cy5qcyJdLCJuYW1lcyI6WyJwcm9kdWN0Q3VzdG9taXphdGlvbkZpZWxkVHlwZUZpbGUiLCJwcm9kdWN0Q3VzdG9taXphdGlvbkZpZWxkVHlwZVRleHQiLCJvcmRlckNyZWF0aW9uQ29udGFpbmVyIiwicmVxdWlyZWRGaWVsZE1hcmsiLCJjYXJ0SW5mb1dyYXBwZXIiLCJjdXN0b21lclNlYXJjaElucHV0IiwiY3VzdG9tZXJTZWFyY2hSZXN1bHRzQmxvY2siLCJjdXN0b21lclNlYXJjaFJlc3VsdFRlbXBsYXRlIiwiY3VzdG9tZXJTZWFyY2hFbXB0eVJlc3VsdFdhcm5pbmciLCJjdXN0b21lckFkZEJ0biIsImNoYW5nZUN1c3RvbWVyQnRuIiwiY3VzdG9tZXJTZWFyY2hSb3ciLCJjaG9vc2VDdXN0b21lckJ0biIsIm5vdFNlbGVjdGVkQ3VzdG9tZXJTZWFyY2hSZXN1bHRzIiwiY3VzdG9tZXJTZWFyY2hSZXN1bHROYW1lIiwiY3VzdG9tZXJTZWFyY2hSZXN1bHRFbWFpbCIsImN1c3RvbWVyU2VhcmNoUmVzdWx0SWQiLCJjdXN0b21lclNlYXJjaFJlc3VsdEJpcnRoZGF5IiwiY3VzdG9tZXJEZXRhaWxzQnRuIiwiY3VzdG9tZXJTZWFyY2hSZXN1bHRDb2x1bW4iLCJjdXN0b21lclNlYXJjaEJsb2NrIiwiY3VzdG9tZXJDYXJ0c1RhYiIsImN1c3RvbWVyT3JkZXJzVGFiIiwiY3VzdG9tZXJDYXJ0c1RhYmxlIiwiY3VzdG9tZXJDYXJ0c1RhYmxlUm93VGVtcGxhdGUiLCJjdXN0b21lckNoZWNrb3V0SGlzdG9yeSIsImN1c3RvbWVyT3JkZXJzVGFibGUiLCJjdXN0b21lck9yZGVyc1RhYmxlUm93VGVtcGxhdGUiLCJjYXJ0UnVsZXNUYWJsZSIsImNhcnRSdWxlc1RhYmxlUm93VGVtcGxhdGUiLCJ1c2VDYXJ0QnRuIiwiY2FydERldGFpbHNCdG4iLCJjYXJ0SWRGaWVsZCIsImNhcnREYXRlRmllbGQiLCJjYXJ0VG90YWxGaWVsZCIsInVzZU9yZGVyQnRuIiwib3JkZXJEZXRhaWxzQnRuIiwib3JkZXJJZEZpZWxkIiwib3JkZXJEYXRlRmllbGQiLCJvcmRlclByb2R1Y3RzRmllbGQiLCJvcmRlclRvdGFsRmllbGQiLCJvcmRlclBheW1lbnRNZXRob2QiLCJvcmRlclN0YXR1c0ZpZWxkIiwiZW1wdHlMaXN0Um93VGVtcGxhdGUiLCJlbXB0eUxpc3RSb3ciLCJjYXJ0UnVsZXNCbG9jayIsImNhcnRSdWxlU2VhcmNoSW5wdXQiLCJjYXJ0UnVsZXNTZWFyY2hSZXN1bHRCb3giLCJjYXJ0UnVsZXNOb3RGb3VuZFRlbXBsYXRlIiwiZm91bmRDYXJ0UnVsZVRlbXBsYXRlIiwiZm91bmRDYXJ0UnVsZUxpc3RJdGVtIiwiY2FydFJ1bGVOYW1lRmllbGQiLCJjYXJ0UnVsZURlc2NyaXB0aW9uRmllbGQiLCJjYXJ0UnVsZVZhbHVlRmllbGQiLCJjYXJ0UnVsZURlbGV0ZUJ0biIsImNhcnRSdWxlRXJyb3JCbG9jayIsImNhcnRSdWxlRXJyb3JUZXh0IiwiYWRkcmVzc2VzQmxvY2siLCJkZWxpdmVyeUFkZHJlc3NEZXRhaWxzIiwiaW52b2ljZUFkZHJlc3NEZXRhaWxzIiwiZGVsaXZlcnlBZGRyZXNzU2VsZWN0IiwiaW52b2ljZUFkZHJlc3NTZWxlY3QiLCJhZGRyZXNzU2VsZWN0IiwiYWRkcmVzc2VzQ29udGVudCIsImFkZHJlc3Nlc1dhcm5pbmciLCJkZWxpdmVyeUFkZHJlc3NFZGl0QnRuIiwiaW52b2ljZUFkZHJlc3NFZGl0QnRuIiwiYWRkcmVzc0FkZEJ0biIsInN1bW1hcnlCbG9jayIsInN1bW1hcnlUb3RhbFByb2R1Y3RzIiwic3VtbWFyeVRvdGFsRGlzY291bnQiLCJzdW1tYXJ5VG90YWxTaGlwcGluZyIsInN1bW1hcnlUb3RhbFRheGVzIiwic3VtbWFyeVRvdGFsV2l0aG91dFRheCIsInN1bW1hcnlUb3RhbFdpdGhUYXgiLCJwbGFjZU9yZGVyQ2FydElkRmllbGQiLCJwcm9jZXNzT3JkZXJMaW5rVGFnIiwib3JkZXJNZXNzYWdlRmllbGQiLCJzZW5kUHJvY2Vzc09yZGVyRW1haWxCdG4iLCJzdW1tYXJ5U3VjY2Vzc0FsZXJ0QmxvY2siLCJzdW1tYXJ5RXJyb3JBbGVydEJsb2NrIiwic3VtbWFyeVN1Y2Nlc3NBbGVydFRleHQiLCJzdW1tYXJ5RXJyb3JBbGVydFRleHQiLCJzaGlwcGluZ0Jsb2NrIiwic2hpcHBpbmdGb3JtIiwibm9DYXJyaWVyQmxvY2siLCJkZWxpdmVyeU9wdGlvblNlbGVjdCIsInRvdGFsU2hpcHBpbmdGaWVsZCIsImZyZWVTaGlwcGluZ1N3aXRjaCIsImNhcnRCbG9jayIsImNhcnRDdXJyZW5jeVNlbGVjdCIsImNhcnRMYW5ndWFnZVNlbGVjdCIsInByb2R1Y3RTZWFyY2giLCJjb21iaW5hdGlvbnNTZWxlY3QiLCJwcm9kdWN0UmVzdWx0QmxvY2siLCJwcm9kdWN0U2VsZWN0IiwicXVhbnRpdHlJbnB1dCIsImluU3RvY2tDb3VudGVyIiwiY29tYmluYXRpb25zVGVtcGxhdGUiLCJjb21iaW5hdGlvbnNSb3ciLCJwcm9kdWN0U2VsZWN0Um93IiwicHJvZHVjdEN1c3RvbUZpZWxkc0NvbnRhaW5lciIsInByb2R1Y3RDdXN0b21pemF0aW9uQ29udGFpbmVyIiwicHJvZHVjdEN1c3RvbUZpbGVUZW1wbGF0ZSIsInByb2R1Y3RDdXN0b21UZXh0VGVtcGxhdGUiLCJwcm9kdWN0Q3VzdG9tSW5wdXRMYWJlbCIsInByb2R1Y3RDdXN0b21JbnB1dCIsInF1YW50aXR5Um93IiwiYWRkVG9DYXJ0QnV0dG9uIiwicHJvZHVjdHNUYWJsZSIsInByb2R1Y3RzVGFibGVSb3dUZW1wbGF0ZSIsImxpc3RlZFByb2R1Y3RJbWFnZUZpZWxkIiwibGlzdGVkUHJvZHVjdE5hbWVGaWVsZCIsImxpc3RlZFByb2R1Y3RBdHRyRmllbGQiLCJsaXN0ZWRQcm9kdWN0UmVmZXJlbmNlRmllbGQiLCJsaXN0ZWRQcm9kdWN0VW5pdFByaWNlSW5wdXQiLCJsaXN0ZWRQcm9kdWN0UXR5SW5wdXQiLCJwcm9kdWN0VG90YWxQcmljZUZpZWxkIiwibGlzdGVkUHJvZHVjdEN1c3RvbWl6ZWRUZXh0VGVtcGxhdGUiLCJsaXN0ZWRQcm9kdWN0Q3VzdG9taXplZEZpbGVUZW1wbGF0ZSIsImxpc3RlZFByb2R1Y3RDdXN0b21pemF0aW9uTmFtZSIsImxpc3RlZFByb2R1Y3RDdXN0b21pemF0aW9uVmFsdWUiLCJsaXN0ZWRQcm9kdWN0RGVmaW5pdGlvbiIsInByb2R1Y3RSZW1vdmVCdG4iLCJwcm9kdWN0VGF4V2FybmluZyIsIm5vUHJvZHVjdHNGb3VuZFdhcm5pbmciLCJwcm9kdWN0QWRkRm9ybSIsImNhcnRFcnJvckFsZXJ0QmxvY2siLCJjYXJ0RXJyb3JBbGVydFRleHQiLCJjdXN0b21lclNlYXJjaGVkIiwiY3VzdG9tZXJTZWxlY3RlZCIsImN1c3RvbWVyc05vdEZvdW5kIiwiY2FydExvYWRlZCIsImNhcnRDdXJyZW5jeUNoYW5nZWQiLCJjYXJ0Q3VycmVuY3lDaGFuZ2VGYWlsZWQiLCJjYXJ0TGFuZ3VhZ2VDaGFuZ2VkIiwiY2FydEFkZHJlc3Nlc0NoYW5nZWQiLCJjYXJ0RGVsaXZlcnlPcHRpb25DaGFuZ2VkIiwiY2FydEZyZWVTaGlwcGluZ1NldCIsImNhcnRSdWxlU2VhcmNoZWQiLCJjYXJ0UnVsZVJlbW92ZWQiLCJjYXJ0UnVsZUFkZGVkIiwiY2FydFJ1bGVGYWlsZWRUb0FkZCIsInByb2R1Y3RTZWFyY2hlZCIsInByb2R1Y3RBZGRlZFRvQ2FydCIsInByb2R1Y3RBZGRUb0NhcnRGYWlsZWQiLCJwcm9kdWN0UmVtb3ZlZEZyb21DYXJ0IiwicHJvZHVjdFByaWNlQ2hhbmdlZCIsInByb2R1Y3RRdHlDaGFuZ2VkIiwicHJvZHVjdFF0eUNoYW5nZUZhaWxlZCIsInByb2Nlc3NPcmRlckVtYWlsU2VudCIsInByb2Nlc3NPcmRlckVtYWlsRmFpbGVkIiwiJCIsIndpbmRvdyIsIkNhcnRFZGl0b3IiLCJyb3V0ZXIiLCJSb3V0ZXIiLCJjYXJ0SWQiLCJhZGRyZXNzZXMiLCJwb3N0IiwiZ2VuZXJhdGUiLCJ0aGVuIiwiRXZlbnRFbWl0dGVyIiwiZW1pdCIsImV2ZW50TWFwIiwiY2FydEluZm8iLCJjYXRjaCIsInNob3dFcnJvck1lc3NhZ2UiLCJyZXNwb25zZSIsInJlc3BvbnNlSlNPTiIsIm1lc3NhZ2UiLCJ2YWx1ZSIsImNhcnJpZXJJZCIsImZyZWVTaGlwcGluZyIsImNhcnRSdWxlSWQiLCJkYXRhIiwiZmlsZVNpemVIZWFkZXIiLCJpc0VtcHR5T2JqZWN0IiwiZmlsZVNpemVzIiwiYWpheCIsImhlYWRlcnMiLCJtZXRob2QiLCJwcm9kdWN0IiwicHJvY2Vzc0RhdGEiLCJjb250ZW50VHlwZSIsInByb2R1Y3RJZCIsImF0dHJpYnV0ZUlkIiwiY3VzdG9taXphdGlvbklkIiwiY3VzdG9tZXJJZCIsInByb2R1Y3RBdHRyaWJ1dGVJZCIsIm5ld1ByaWNlIiwicHJpY2UiLCJuZXdRdHkiLCJjdXJyZW5jeUlkIiwiY3JlYXRlT3JkZXJNYXAiLCJsYW5ndWFnZUlkIiwiU3VtbWFyeVJlbmRlcmVyIiwiJHRvdGFsUHJvZHVjdHMiLCIkdG90YWxEaXNjb3VudCIsIiR0b3RhbFNoaXBwaW5nIiwiJHRvdGFsVGF4ZXMiLCIkdG90YWxXaXRob3V0VGF4IiwiJHRvdGFsV2l0aFRheCIsIiRwbGFjZU9yZGVyQ2FydElkRmllbGQiLCIkb3JkZXJNZXNzYWdlRmllbGQiLCIkcHJvY2Vzc09yZGVyTGluayIsIl9jbGVhblN1bW1hcnkiLCJub1Byb2R1Y3RzIiwicHJvZHVjdHMiLCJsZW5ndGgiLCJub1NoaXBwaW5nT3B0aW9ucyIsInNoaXBwaW5nIiwiYWRkcmVzc2VzQXJlVmFsaWQiLCJDcmVhdGVPcmRlclBhZ2UiLCJ2YWxpZGF0ZVNlbGVjdGVkQWRkcmVzc2VzIiwiX2hpZGVTdW1tYXJ5QmxvY2siLCJjYXJ0U3VtbWFyeSIsInN1bW1hcnkiLCJ0ZXh0IiwidG90YWxQcm9kdWN0c1ByaWNlIiwidG90YWxEaXNjb3VudCIsInRvdGFsU2hpcHBpbmdQcmljZSIsInRvdGFsVGF4ZXMiLCJ0b3RhbFByaWNlV2l0aG91dFRheGVzIiwidG90YWxQcmljZVdpdGhUYXhlcyIsInByb3AiLCJwcm9jZXNzT3JkZXJMaW5rIiwib3JkZXJNZXNzYWdlIiwidmFsIiwiX3Nob3dTdW1tYXJ5QmxvY2siLCJfc2hvd1N1bW1hcnlTdWNjZXNzQWxlcnRCbG9jayIsIl9zaG93U3VtbWFyeUVycm9yQWxlcnRCbG9jayIsIl9oaWRlU3VtbWFyeVN1Y2Nlc3NBbGVydEJsb2NrIiwiX2hpZGVTdW1tYXJ5RXJyb3JBbGVydEJsb2NrIiwicmVtb3ZlQ2xhc3MiLCJhZGRDbGFzcyIsImVtcHR5IiwiY2xlYW5BbGVydHMiLCIkY29udGFpbmVyIiwiY2FydFByb3ZpZGVyIiwiQ2FydFByb3ZpZGVyIiwiY3VzdG9tZXJNYW5hZ2VyIiwiQ3VzdG9tZXJNYW5hZ2VyIiwic2hpcHBpbmdSZW5kZXJlciIsIlNoaXBwaW5nUmVuZGVyZXIiLCJhZGRyZXNzZXNSZW5kZXJlciIsIkFkZHJlc3Nlc1JlbmRlcmVyIiwiY2FydFJ1bGVzUmVuZGVyZXIiLCJDYXJ0UnVsZXNSZW5kZXJlciIsImNhcnRFZGl0b3IiLCJjYXJ0UnVsZU1hbmFnZXIiLCJDYXJ0UnVsZU1hbmFnZXIiLCJwcm9kdWN0TWFuYWdlciIsIlByb2R1Y3RNYW5hZ2VyIiwicHJvZHVjdFJlbmRlcmVyIiwiUHJvZHVjdFJlbmRlcmVyIiwic3VtbWFyeVJlbmRlcmVyIiwic3VtbWFyeU1hbmFnZXIiLCJTdW1tYXJ5TWFuYWdlciIsIl9pbml0TGlzdGVuZXJzIiwiX2xvYWRDYXJ0RnJvbVVybFBhcmFtcyIsInJlZnJlc2hBZGRyZXNzZXNMaXN0IiwicmVmcmVzaENhcnRBZGRyZXNzZXMiLCJzZWFyY2giLCJzdHJpbmciLCJ1cmxQYXJhbXMiLCJVUkxTZWFyY2hQYXJhbXMiLCJsb2NhdGlvbiIsIk51bWJlciIsImdldCIsImlzTmFOIiwiZ2V0Q2FydCIsIm9uIiwiX2luaXRDdXN0b21lclNlYXJjaCIsImUiLCJfaW5pdEN1c3RvbWVyU2VsZWN0IiwiX2luaXRDYXJ0U2VsZWN0IiwiX2luaXREdXBsaWNhdGVPcmRlckNhcnQiLCJfaW5pdFByb2R1Y3RTZWFyY2giLCJfaW5pdENhcnRSdWxlU2VhcmNoIiwic3RvcFNlYXJjaGluZyIsIl9saXN0ZW5Gb3JDYXJ0RWRpdCIsIl9vbkNhcnRMb2FkZWQiLCJvbkN1c3RvbWVyc05vdEZvdW5kIiwiX29uQ3VzdG9tZXJTZWxlY3RlZCIsImluaXRBZGRyZXNzQnV0dG9uc0lmcmFtZSIsImluaXRDdXN0b21lckRldGFpbHNJZnJhbWUiLCJmYW5jeWJveCIsIl9vbkNhcnRBZGRyZXNzZXNDaGFuZ2VkIiwiX29uRGVsaXZlcnlPcHRpb25DaGFuZ2VkIiwiX29uRnJlZVNoaXBwaW5nQ2hhbmdlZCIsIl9hZGRDYXJ0UnVsZVRvQ2FydCIsIl9yZW1vdmVDYXJ0UnVsZUZyb21DYXJ0IiwiX29uQ2FydEN1cnJlbmN5Q2hhbmdlZCIsIl9vbkNhcnRMYW5ndWFnZUNoYW5nZWQiLCJjaGFuZ2VEZWxpdmVyeU9wdGlvbiIsImN1cnJlbnRUYXJnZXQiLCJzZXRGcmVlU2hpcHBpbmciLCJhZGRQcm9kdWN0VG9DYXJ0IiwiY2hhbmdlQ2FydEN1cnJlbmN5IiwiY2hhbmdlQ2FydExhbmd1YWdlIiwic2VuZFByb2Nlc3NPcmRlckVtYWlsIiwiX2luaXRQcm9kdWN0Q2hhbmdlUHJpY2UiLCJfaW5pdFByb2R1Y3RDaGFuZ2VRdHkiLCJfY2hhbmdlQ2FydEFkZHJlc3NlcyIsIl9pbml0UHJvZHVjdFJlbW92ZUZyb21DYXJ0IiwiX3JlbmRlckNhcnRJbmZvIiwibG9hZEN1c3RvbWVyQ2FydHMiLCJsb2FkQ3VzdG9tZXJPcmRlcnMiLCJoaWRlQ2FydEluZm8iLCJzaG93Q2FydEluZm8iLCJyZW5kZXIiLCJyZW5kZXJDYXJ0UnVsZXNCbG9jayIsImNhcnRSdWxlcyIsIl9wcmVzZWxlY3RDYXJ0TGFuZ3VhZ2UiLCJsYW5nSWQiLCJyZXNldCIsInJlbmRlckNhcnRCbG9ja0Vycm9yQWxlcnQiLCJldmVudCIsImNsZWFyVGltZW91dCIsInRpbWVvdXRJZCIsInNldFRpbWVvdXQiLCJzZWxlY3RDdXN0b21lciIsImxvYWRFbXB0eUNhcnQiLCJvcmRlcklkIiwiZHVwbGljYXRlT3JkZXJDYXJ0Iiwic2VhcmNoUGhyYXNlIiwicHJldmVudERlZmF1bHQiLCJhZGRDYXJ0UnVsZVRvQ2FydCIsImJsdXIiLCJyZW1vdmVDYXJ0UnVsZUZyb21DYXJ0IiwiJHByb2R1Y3RTZWFyY2hJbnB1dCIsInJlbW92ZVByb2R1Y3RGcm9tQ2FydCIsImNoYW5nZVByb2R1Y3RQcmljZSIsImNoYW5nZVByb2R1Y3RRdHkiLCJjbGVhbkNhcnRCbG9ja0FsZXJ0cyIsInJlbmRlckxpc3QiLCJfcHJlc2VsZWN0Q2FydEN1cnJlbmN5IiwiZGVsaXZlcnlBZGRyZXNzSWQiLCJpbnZvaWNlQWRkcmVzc0lkIiwiY2hhbmdlQ2FydEFkZHJlc3NlcyIsImRlbGl2ZXJ5VmFsaWQiLCJpbnZvaWNlVmFsaWQiLCJrZXkiLCJhZGRyZXNzIiwiZGVsaXZlcnkiLCJpbnZvaWNlIiwiJGNhcnRSdWxlc0Jsb2NrIiwiJGNhcnRSdWxlc1RhYmxlIiwiJHNlYXJjaFJlc3VsdEJveCIsImVtcHR5Q2FydCIsIl9oaWRlRXJyb3JCbG9jayIsIl9oaWRlQ2FydFJ1bGVzQmxvY2siLCJfc2hvd0NhcnRSdWxlc0Jsb2NrIiwiX2hpZGVDYXJ0UnVsZXNMaXN0IiwiX3JlbmRlckxpc3QiLCJzZWFyY2hSZXN1bHRzIiwiX2NsZWFyU2VhcmNoUmVzdWx0cyIsImNhcnRfcnVsZXMiLCJfcmVuZGVyTm90Rm91bmQiLCJfcmVuZGVyRm91bmRDYXJ0UnVsZXMiLCJfc2hvd1Jlc3VsdHNEcm9wZG93biIsIl9zaG93RXJyb3JCbG9jayIsIiR0ZW1wbGF0ZSIsImh0bWwiLCJjbG9uZSIsIiRjYXJ0UnVsZVRlbXBsYXRlIiwiY2FydFJ1bGUiLCJjYXJ0UnVsZU5hbWUiLCJuYW1lIiwiY29kZSIsImFwcGVuZCIsIl9jbGVhbkNhcnRSdWxlc0xpc3QiLCIkY2FydFJ1bGVzVGFibGVSb3dUZW1wbGF0ZSIsImZpbmQiLCJkZXNjcmlwdGlvbiIsIl9zaG93Q2FydFJ1bGVzTGlzdCIsIiRwcm9kdWN0c1RhYmxlIiwiX2NsZWFuUHJvZHVjdHNMaXN0IiwiX2hpZGVQcm9kdWN0c0xpc3QiLCIkcHJvZHVjdHNUYWJsZVJvd1RlbXBsYXRlIiwiY3VzdG9taXphdGlvbiIsIl9yZW5kZXJMaXN0ZWRQcm9kdWN0Q3VzdG9taXphdGlvbiIsImltYWdlTGluayIsImF0dHJpYnV0ZSIsInJlZmVyZW5jZSIsInVuaXRQcmljZSIsInF1YW50aXR5IiwiX3Nob3dUYXhXYXJuaW5nIiwiX3Nob3dQcm9kdWN0c0xpc3QiLCIkcHJvZHVjdFJvd1RlbXBsYXRlIiwiJGN1c3RvbWl6ZWRUZXh0VGVtcGxhdGUiLCIkY3VzdG9taXplZEZpbGVUZW1wbGF0ZSIsImN1c3RvbWl6YXRpb25GaWVsZHNEYXRhIiwiY3VzdG9taXplZERhdGEiLCIkY3VzdG9taXphdGlvblRlbXBsYXRlIiwidHlwZSIsImZvdW5kUHJvZHVjdHMiLCJfY2xlYW5TZWFyY2hSZXN1bHRzIiwiX3Nob3dOb3RGb3VuZCIsIl9oaWRlVGF4V2FybmluZyIsIl9yZW5kZXJGb3VuZFByb2R1Y3RzIiwiX2hpZGVOb3RGb3VuZCIsIl9zaG93UmVzdWx0QmxvY2siLCJfaGlkZVJlc3VsdEJsb2NrIiwicmVuZGVyU3RvY2siLCJzdG9jayIsIl9yZW5kZXJDb21iaW5hdGlvbnMiLCJjb21iaW5hdGlvbnMiLCJfcmVuZGVyQ3VzdG9taXphdGlvbnMiLCJjdXN0b21pemF0aW9uRmllbGRzIiwiYXR0ciIsImZvcm1hdHRlZFByaWNlIiwiX2NsZWFuQ29tYmluYXRpb25zIiwiX2hpZGVDb21iaW5hdGlvbnMiLCJjb21iaW5hdGlvbiIsImF0dHJpYnV0ZUNvbWJpbmF0aW9uSWQiLCJfc2hvd0NvbWJpbmF0aW9ucyIsImZpZWxkVHlwZUZpbGUiLCJmaWVsZFR5cGVUZXh0IiwiX2NsZWFuQ3VzdG9taXphdGlvbnMiLCJfaGlkZUN1c3RvbWl6YXRpb25zIiwiJGN1c3RvbUZpZWxkc0NvbnRhaW5lciIsIiRmaWxlSW5wdXRUZW1wbGF0ZSIsIiR0ZXh0SW5wdXRUZW1wbGF0ZSIsInRlbXBsYXRlVHlwZU1hcCIsImN1c3RvbUZpZWxkIiwiY3VzdG9taXphdGlvbkZpZWxkSWQiLCJyZXF1aXJlZCIsIl9zaG93Q3VzdG9taXphdGlvbnMiLCJfc2hvd0NhcnRCbG9ja0Vycm9yIiwiX2hpZGVDYXJ0QmxvY2tFcnJvciIsIiRmb3JtIiwiJG5vQ2FycmllckJsb2NrIiwiX2hpZGVDb250YWluZXIiLCJfZGlzcGxheUZvcm0iLCJfZGlzcGxheU5vQ2FycmllcnNXYXJuaW5nIiwiX2hpZGVOb0NhcnJpZXJCbG9jayIsIl9yZW5kZXJEZWxpdmVyeU9wdGlvbnMiLCJkZWxpdmVyeU9wdGlvbnMiLCJzZWxlY3RlZENhcnJpZXJJZCIsIl9yZW5kZXJUb3RhbFNoaXBwaW5nIiwic2hpcHBpbmdQcmljZSIsIl9yZW5kZXJGcmVlU2hpcHBpbmdTd2l0Y2giLCJfc2hvd0Zvcm0iLCJfc2hvd0NvbnRhaW5lciIsImlzRnJlZVNoaXBwaW5nIiwiZWFjaCIsImlucHV0IiwiY2hlY2tlZCIsIl9oaWRlRm9ybSIsIl9zaG93Tm9DYXJyaWVyQmxvY2siLCJzZWxlY3RlZFZhbCIsIiRkZWxpdmVyeU9wdGlvblNlbGVjdCIsIm9wdGlvbiIsImRlbGl2ZXJ5T3B0aW9uIiwiY2Fycmllck5hbWUiLCJjYXJyaWVyRGVsYXkiLCJzZWxlY3RlZCIsIiR0b3RhbFNoaXBwaW5nRmllbGQiLCJFdmVudEVtaXR0ZXJDbGFzcyIsIm9yZGVyUGFnZU1hbmFnZXIiLCJzZWFyY2hDdXN0b21lckJ5U3RyaW5nIiwiY29uc29sZSIsImxvZyIsImRvY3VtZW50IiwicmVhZHkiLCJfY2xlYW5BZGRyZXNzZXMiLCJfaGlkZUFkZHJlc3Nlc0NvbnRlbnQiLCJfc2hvd0VtcHR5QWRkcmVzc2VzV2FybmluZyIsIl9zaG93QWRkcmVzc2VzQmxvY2siLCJfc2hvd0FkZHJlc3Nlc0NvbnRlbnQiLCJfaGlkZUVtcHR5QWRkcmVzc2VzV2FybmluZyIsIl9yZW5kZXJEZWxpdmVyeUFkZHJlc3MiLCJfcmVuZGVySW52b2ljZUFkZHJlc3MiLCJkZWxpdmVyeUFkZHJlc3NPcHRpb24iLCJhZGRyZXNzSWQiLCJhbGlhcyIsImZvcm1hdHRlZEFkZHJlc3MiLCJsaXRlRGlzcGxheWluZyIsInN1Ym1pdEZvcm1BamF4IiwiaW52b2ljZUFkZHJlc3NPcHRpb24iLCJjcmVhdGVPcmRlclBhZ2VNYXAiLCJhY3RpdmVTZWFyY2hSZXF1ZXN0IiwiJHNlYXJjaElucHV0IiwiX3NlYXJjaCIsImhpZGVSZXN1bHRzRHJvcGRvd24iLCJfb25DYXJ0UnVsZVNlYXJjaCIsIl9vbkFkZENhcnRSdWxlVG9DYXJ0IiwiX29uQWRkQ2FydFJ1bGVUb0NhcnRGYWlsdXJlIiwiX29uUmVtb3ZlQ2FydFJ1bGVGcm9tQ2FydCIsInJlbmRlclNlYXJjaFJlc3VsdHMiLCJjYXJ0SXNFbXB0eSIsImRpc3BsYXlFcnJvck1lc3NhZ2UiLCJhYm9ydCIsInNlYXJjaF9waHJhc2UiLCJzdGF0dXNUZXh0IiwiJGN1c3RvbWVyU2VhcmNoUmVzdWx0QmxvY2siLCJjdXN0b21lclJlbmRlcmVyIiwiQ3VzdG9tZXJSZW5kZXJlciIsImluaXRBZGRDdXN0b21lcklmcmFtZSIsIl9zZWxlY3RDdXN0b21lciIsIl9sb2FkQ3VzdG9tZXJDYXJ0cyIsImN1cnJlbnRDYXJ0SWQiLCJfbG9hZEN1c3RvbWVyT3JkZXJzIiwiX2NoYW5nZUN1c3RvbWVyIiwiX29uQ3VzdG9tZXJTZWFyY2giLCJfb25DdXN0b21lclNlbGVjdCIsImNsZWFyU2hvd25DdXN0b21lcnMiLCJjdXN0b21lcnMiLCJzaG93Tm90Rm91bmRDdXN0b21lcnMiLCJoaWRlQ2hlY2tvdXRIaXN0b3J5QmxvY2siLCIkY2hvb3NlQnRuIiwiY3JlYXRlQWRkcmVzc1VybCIsImRpc3BsYXlTZWxlY3RlZEN1c3RvbWVyQmxvY2siLCJzaG93Q3VzdG9tZXJTZWFyY2giLCJyZW5kZXJDYXJ0cyIsImNhcnRzIiwicmVuZGVyT3JkZXJzIiwib3JkZXJzIiwiY2hvb3NlQ3VzdG9tZXJFdmVudCIsIiRzZWFyY2hSZXF1ZXN0IiwiY3VzdG9tZXJfc2VhcmNoIiwiZm91bmRDdXN0b21lcnMiLCJjdXN0b21lclJlc3VsdCIsImN1c3RvbWVyIiwiaWQiLCJmaXJzdE5hbWUiLCJmaXJzdG5hbWUiLCJsYXN0TmFtZSIsImxhc3RuYW1lIiwiZW1haWwiLCJiaXJ0aGRheSIsIl9yZW5kZXJGb3VuZEN1c3RvbWVyIiwiJHRhcmdldGVkQnRuIiwic2hvd0NoZWNrb3V0SGlzdG9yeUJsb2NrIiwiJGN1c3RvbWVyQ2FyZCIsImNsb3Nlc3QiLCJyZW1vdmUiLCIkY2FydHNUYWJsZSIsIiRjYXJ0c1RhYmxlUm93VGVtcGxhdGUiLCJfcmVtb3ZlRW1wdHlMaXN0Um93RnJvbVRhYmxlIiwiY2FydCIsIl9yZW5kZXJFbXB0eUxpc3QiLCIkY2FydHNUYWJsZVJvdyIsImNyZWF0aW9uRGF0ZSIsInRvdGFsUHJpY2UiLCIkb3JkZXJzVGFibGUiLCIkcm93VGVtcGxhdGUiLCJvcmRlciIsIm9yZGVyUGxhY2VkRGF0ZSIsIm9yZGVyUHJvZHVjdHNDb3VudCIsInRvdGFsUGFpZCIsInBheW1lbnRNZXRob2ROYW1lIiwib3JkZXJTdGF0dXMiLCIkdGFibGUiLCIkZW1wdHlUYWJsZVJvdyIsImhpZGVOb3RGb3VuZEN1c3RvbWVycyIsIiRjdXN0b21lclNlYXJjaFJlc3VsdFRlbXBsYXRlIiwic2VsZWN0ZWRQcm9kdWN0Iiwic2VsZWN0ZWRDb21iaW5hdGlvbklkIiwiYWRkUHJvZHVjdCIsIl9nZXRQcm9kdWN0RGF0YSIsInVwZGF0ZWRQcm9kdWN0IiwiX2luaXRQcm9kdWN0U2VsZWN0IiwiX2luaXRDb21iaW5hdGlvblNlbGVjdCIsIl9vblByb2R1Y3RTZWFyY2giLCJfb25BZGRQcm9kdWN0VG9DYXJ0IiwiX29uUmVtb3ZlUHJvZHVjdEZyb21DYXJ0IiwiX29uUHJvZHVjdFByaWNlQ2hhbmdlIiwiX29uUHJvZHVjdFF0eUNoYW5nZSIsIl9zZWxlY3RGaXJzdFJlc3VsdCIsImVycm9yTWVzc2FnZSIsIl9zZWxlY3RQcm9kdWN0IiwiY29tYmluYXRpb25JZCIsIl9zZWxlY3RDb21iaW5hdGlvbiIsInBhcmFtcyIsInVuZGVmaW5lZCIsImN1cnJlbmN5X2lkIiwiX3Vuc2V0UHJvZHVjdCIsInZhbHVlcyIsIl91bnNldENvbWJpbmF0aW9uIiwicmVuZGVyUHJvZHVjdE1ldGFkYXRhIiwiJGZpbGVJbnB1dHMiLCJmb3JtRGF0YSIsIkZvcm1EYXRhIiwicXVlcnlTZWxlY3RvciIsImZpbGVzIiwic2l6ZSIsIl9zZW5kUHJvY2Vzc09yZGVyRW1haWwiLCJfb25Qcm9jZXNzT3JkZXJFbWFpbEVycm9yIiwiX29uUHJvY2Vzc09yZGVyRW1haWxTdWNjZXNzIiwicmVuZGVyU3VjY2Vzc01lc3NhZ2UiLCJyZW5kZXJFcnJvck1lc3NhZ2UiLCJSb3V0aW5nIiwic2V0RGF0YSIsInJvdXRlcyIsInNldEJhc2VVcmwiLCJyb3V0ZSIsInRva2VuaXplZFBhcmFtcyIsIl90b2tlbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtB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RUE7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLENBQUM7QUFDRDtBQUNBO0FBQ0E7QUFDQTtBQUNBLENBQUM7QUFDRDtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLEdBQUc7QUFDSDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxDQUFDOztBQUVEOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBLGlCQUFpQixzQkFBc0I7QUFDdkM7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsZUFBZTtBQUNmO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsY0FBYztBQUNkOztBQUVBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBLEdBQUc7QUFDSDtBQUNBO0FBQ0EsbUJBQW1CLFNBQVM7QUFDNUI7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLEdBQUc7QUFDSDtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLEdBQUc7QUFDSDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsS0FBSztBQUNMO0FBQ0EsS0FBSztBQUNMO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtB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QUFDZCxjQUFjO0FBQ2QsY0FBYztBQUNkLGNBQWM7QUFDZCxlQUFlO0FBQ2YsZUFBZTtBQUNmLGVBQWU7QUFDZixnQkFBZ0I7QUFDaEIseUI7Ozs7Ozs7QUM1REEsa0JBQWtCLHdEOzs7Ozs7O0FDQWxCO0FBQ0Esc0Q7Ozs7Ozs7QUNEQTtBQUNBLG9EOzs7Ozs7OztBQ0RBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxrQ0FBa0MsVUFBVSxFQUFFO0FBQzlDLG1CQUFtQixzQ0FBc0M7QUFDekQsQ0FBQyxvQ0FBb0M7QUFDckM7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsR0FBRztBQUNILENBQUMsVzs7Ozs7OztBQ2hDRDtBQUNBOztBQUVBLDBDQUEwQyxnQ0FBb0MsRTs7Ozs7OztBQ0g5RTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQSxDQUFDLEUiLCJmaWxlIjoib3JkZXJfY3JlYXRlLmJ1bmRsZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGlkZW50aXR5IGZ1bmN0aW9uIGZvciBjYWxsaW5nIGhhcm1vbnkgaW1wb3J0cyB3aXRoIHRoZSBjb3JyZWN0IGNvbnRleHRcbiBcdF9fd2VicGFja19yZXF1aXJlX18uaSA9IGZ1bmN0aW9uKHZhbHVlKSB7IHJldHVybiB2YWx1ZTsgfTtcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7XG4gXHRcdFx0XHRjb25maWd1cmFibGU6IGZhbHNlLFxuIFx0XHRcdFx0ZW51bWVyYWJsZTogdHJ1ZSxcbiBcdFx0XHRcdGdldDogZ2V0dGVyXG4gXHRcdFx0fSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gNTA4KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyB3ZWJwYWNrL2Jvb3RzdHJhcCAwMmRmZThmYTZjMDMxMDBjNzY5NSIsIlwidXNlIHN0cmljdFwiO1xuXG5leHBvcnRzLl9fZXNNb2R1bGUgPSB0cnVlO1xuXG5leHBvcnRzLmRlZmF1bHQgPSBmdW5jdGlvbiAoaW5zdGFuY2UsIENvbnN0cnVjdG9yKSB7XG4gIGlmICghKGluc3RhbmNlIGluc3RhbmNlb2YgQ29uc3RydWN0b3IpKSB7XG4gICAgdGhyb3cgbmV3IFR5cGVFcnJvcihcIkNhbm5vdCBjYWxsIGEgY2xhc3MgYXMgYSBmdW5jdGlvblwiKTtcbiAgfVxufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vYmFiZWwtcnVudGltZS9oZWxwZXJzL2NsYXNzQ2FsbENoZWNrLmpzXG4vLyBtb2R1bGUgaWQgPSAwXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiXCJ1c2Ugc3RyaWN0XCI7XG5cbmV4cG9ydHMuX19lc01vZHVsZSA9IHRydWU7XG5cbnZhciBfZGVmaW5lUHJvcGVydHkgPSByZXF1aXJlKFwiLi4vY29yZS1qcy9vYmplY3QvZGVmaW5lLXByb3BlcnR5XCIpO1xuXG52YXIgX2RlZmluZVByb3BlcnR5MiA9IF9pbnRlcm9wUmVxdWlyZURlZmF1bHQoX2RlZmluZVByb3BlcnR5KTtcblxuZnVuY3Rpb24gX2ludGVyb3BSZXF1aXJlRGVmYXVsdChvYmopIHsgcmV0dXJuIG9iaiAmJiBvYmouX19lc01vZHVsZSA/IG9iaiA6IHsgZGVmYXVsdDogb2JqIH07IH1cblxuZXhwb3J0cy5kZWZhdWx0ID0gZnVuY3Rpb24gKCkge1xuICBmdW5jdGlvbiBkZWZpbmVQcm9wZXJ0aWVzKHRhcmdldCwgcHJvcHMpIHtcbiAgICBmb3IgKHZhciBpID0gMDsgaSA8IHByb3BzLmxlbmd0aDsgaSsrKSB7XG4gICAgICB2YXIgZGVzY3JpcHRvciA9IHByb3BzW2ldO1xuICAgICAgZGVzY3JpcHRvci5lbnVtZXJhYmxlID0gZGVzY3JpcHRvci5lbnVtZXJhYmxlIHx8IGZhbHNlO1xuICAgICAgZGVzY3JpcHRvci5jb25maWd1cmFibGUgPSB0cnVlO1xuICAgICAgaWYgKFwidmFsdWVcIiBpbiBkZXNjcmlwdG9yKSBkZXNjcmlwdG9yLndyaXRhYmxlID0gdHJ1ZTtcbiAgICAgICgwLCBfZGVmaW5lUHJvcGVydHkyLmRlZmF1bHQpKHRhcmdldCwgZGVzY3JpcHRvci5rZXksIGRlc2NyaXB0b3IpO1xuICAgIH1cbiAgfVxuXG4gIHJldHVybiBmdW5jdGlvbiAoQ29uc3RydWN0b3IsIHByb3RvUHJvcHMsIHN0YXRpY1Byb3BzKSB7XG4gICAgaWYgKHByb3RvUHJvcHMpIGRlZmluZVByb3BlcnRpZXMoQ29uc3RydWN0b3IucHJvdG90eXBlLCBwcm90b1Byb3BzKTtcbiAgICBpZiAoc3RhdGljUHJvcHMpIGRlZmluZVByb3BlcnRpZXMoQ29uc3RydWN0b3IsIHN0YXRpY1Byb3BzKTtcbiAgICByZXR1cm4gQ29uc3RydWN0b3I7XG4gIH07XG59KCk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvaGVscGVycy9jcmVhdGVDbGFzcy5qc1xuLy8gbW9kdWxlIGlkID0gMVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciBkUCAgICAgICAgID0gcmVxdWlyZSgnLi9fb2JqZWN0LWRwJylcbiAgLCBjcmVhdGVEZXNjID0gcmVxdWlyZSgnLi9fcHJvcGVydHktZGVzYycpO1xubW9kdWxlLmV4cG9ydHMgPSByZXF1aXJlKCcuL19kZXNjcmlwdG9ycycpID8gZnVuY3Rpb24ob2JqZWN0LCBrZXksIHZhbHVlKXtcbiAgcmV0dXJuIGRQLmYob2JqZWN0LCBrZXksIGNyZWF0ZURlc2MoMSwgdmFsdWUpKTtcbn0gOiBmdW5jdGlvbihvYmplY3QsIGtleSwgdmFsdWUpe1xuICBvYmplY3Rba2V5XSA9IHZhbHVlO1xuICByZXR1cm4gb2JqZWN0O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2hpZGUuanNcbi8vIG1vZHVsZSBpZCA9IDEwXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIi8qKlxuICogMjAwNy0yMDIwIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMjAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbi8qKlxuICogRW5jYXBzdWxhdGVzIHNlbGVjdG9ycyBmb3IgXCJDcmVhdGUgb3JkZXJcIiBwYWdlXG4gKi9cbmV4cG9ydCBkZWZhdWx0IHtcbiAgcHJvZHVjdEN1c3RvbWl6YXRpb25GaWVsZFR5cGVGaWxlOiAwLFxuICBwcm9kdWN0Q3VzdG9taXphdGlvbkZpZWxkVHlwZVRleHQ6IDEsXG5cbiAgb3JkZXJDcmVhdGlvbkNvbnRhaW5lcjogJyNvcmRlci1jcmVhdGlvbi1jb250YWluZXInLFxuICByZXF1aXJlZEZpZWxkTWFyazogJy5qcy1yZXF1aXJlZC1maWVsZC1tYXJrJyxcbiAgY2FydEluZm9XcmFwcGVyOiAnI2pzLWNhcnQtaW5mby13cmFwcGVyJyxcblxuICAvLyBzZWxlY3RvcnMgcmVsYXRlZCB0byBjdXN0b21lciBibG9ja1xuICBjdXN0b21lclNlYXJjaElucHV0OiAnI2N1c3RvbWVyLXNlYXJjaC1pbnB1dCcsXG4gIGN1c3RvbWVyU2VhcmNoUmVzdWx0c0Jsb2NrOiAnLmpzLWN1c3RvbWVyLXNlYXJjaC1yZXN1bHRzJyxcbiAgY3VzdG9tZXJTZWFyY2hSZXN1bHRUZW1wbGF0ZTogJyNjdXN0b21lci1zZWFyY2gtcmVzdWx0LXRlbXBsYXRlJyxcbiAgY3VzdG9tZXJTZWFyY2hFbXB0eVJlc3VsdFdhcm5pbmc6ICcjY3VzdG9tZXItc2VhcmNoLWVtcHR5LXJlc3VsdC13YXJuJyxcbiAgY3VzdG9tZXJBZGRCdG46ICcjY3VzdG9tZXItYWRkLWJ0bicsXG4gIGNoYW5nZUN1c3RvbWVyQnRuOiAnLmpzLWNoYW5nZS1jdXN0b21lci1idG4nLFxuICBjdXN0b21lclNlYXJjaFJvdzogJy5qcy1zZWFyY2gtY3VzdG9tZXItcm93JyxcbiAgY2hvb3NlQ3VzdG9tZXJCdG46ICcuanMtY2hvb3NlLWN1c3RvbWVyLWJ0bicsXG4gIG5vdFNlbGVjdGVkQ3VzdG9tZXJTZWFyY2hSZXN1bHRzOiAnLmpzLWN1c3RvbWVyLXNlYXJjaC1yZXN1bHQ6bm90KC5ib3JkZXItc3VjY2VzcyknLFxuICBjdXN0b21lclNlYXJjaFJlc3VsdE5hbWU6ICcuanMtY3VzdG9tZXItbmFtZScsXG4gIGN1c3RvbWVyU2VhcmNoUmVzdWx0RW1haWw6ICcuanMtY3VzdG9tZXItZW1haWwnLFxuICBjdXN0b21lclNlYXJjaFJlc3VsdElkOiAnLmpzLWN1c3RvbWVyLWlkJyxcbiAgY3VzdG9tZXJTZWFyY2hSZXN1bHRCaXJ0aGRheTogJy5qcy1jdXN0b21lci1iaXJ0aGRheScsXG4gIGN1c3RvbWVyRGV0YWlsc0J0bjogJy5qcy1kZXRhaWxzLWN1c3RvbWVyLWJ0bicsXG4gIGN1c3RvbWVyU2VhcmNoUmVzdWx0Q29sdW1uOiAnLmpzLWN1c3RvbWVyLXNlYXJjaC1yZXN1bHQtY29sJyxcbiAgY3VzdG9tZXJTZWFyY2hCbG9jazogJyNjdXN0b21lci1zZWFyY2gtYmxvY2snLFxuICBjdXN0b21lckNhcnRzVGFiOiAnLmpzLWN1c3RvbWVyLWNhcnRzLXRhYicsXG4gIGN1c3RvbWVyT3JkZXJzVGFiOiAnLmpzLWN1c3RvbWVyLW9yZGVycy10YWInLFxuICBjdXN0b21lckNhcnRzVGFibGU6ICcjY3VzdG9tZXItY2FydHMtdGFibGUnLFxuICBjdXN0b21lckNhcnRzVGFibGVSb3dUZW1wbGF0ZTogJyNjdXN0b21lci1jYXJ0cy10YWJsZS1yb3ctdGVtcGxhdGUnLFxuICBjdXN0b21lckNoZWNrb3V0SGlzdG9yeTogJyNjdXN0b21lci1jaGVja291dC1oaXN0b3J5JyxcbiAgY3VzdG9tZXJPcmRlcnNUYWJsZTogJyNjdXN0b21lci1vcmRlcnMtdGFibGUnLFxuICBjdXN0b21lck9yZGVyc1RhYmxlUm93VGVtcGxhdGU6ICcjY3VzdG9tZXItb3JkZXJzLXRhYmxlLXJvdy10ZW1wbGF0ZScsXG4gIGNhcnRSdWxlc1RhYmxlOiAnI2NhcnQtcnVsZXMtdGFibGUnLFxuICBjYXJ0UnVsZXNUYWJsZVJvd1RlbXBsYXRlOiAnI2NhcnQtcnVsZXMtdGFibGUtcm93LXRlbXBsYXRlJyxcbiAgdXNlQ2FydEJ0bjogJy5qcy11c2UtY2FydC1idG4nLFxuICBjYXJ0RGV0YWlsc0J0bjogJy5qcy1jYXJ0LWRldGFpbHMtYnRuJyxcbiAgY2FydElkRmllbGQ6ICcuanMtY2FydC1pZCcsXG4gIGNhcnREYXRlRmllbGQ6ICcuanMtY2FydC1kYXRlJyxcbiAgY2FydFRvdGFsRmllbGQ6ICcuanMtY2FydC10b3RhbCcsXG4gIHVzZU9yZGVyQnRuOiAnLmpzLXVzZS1vcmRlci1idG4nLFxuICBvcmRlckRldGFpbHNCdG46ICcuanMtb3JkZXItZGV0YWlscy1idG4nLFxuICBvcmRlcklkRmllbGQ6ICcuanMtb3JkZXItaWQnLFxuICBvcmRlckRhdGVGaWVsZDogJy5qcy1vcmRlci1kYXRlJyxcbiAgb3JkZXJQcm9kdWN0c0ZpZWxkOiAnLmpzLW9yZGVyLXByb2R1Y3RzJyxcbiAgb3JkZXJUb3RhbEZpZWxkOiAnLmpzLW9yZGVyLXRvdGFsLXBhaWQnLFxuICBvcmRlclBheW1lbnRNZXRob2Q6ICcuanMtb3JkZXItcGF5bWVudC1tZXRob2QnLFxuICBvcmRlclN0YXR1c0ZpZWxkOiAnLmpzLW9yZGVyLXN0YXR1cycsXG4gIGVtcHR5TGlzdFJvd1RlbXBsYXRlOiAnI2pzLWVtcHR5LWxpc3Qtcm93JyxcbiAgZW1wdHlMaXN0Um93OiAnLmpzLWVtcHR5LXJvdycsXG5cbiAgLy8gc2VsZWN0b3JzIHJlbGF0ZWQgdG8gY2FydFJ1bGVzIGJsb2NrXG4gIGNhcnRSdWxlc0Jsb2NrOiAnI2NhcnQtcnVsZXMtYmxvY2snLFxuICBjYXJ0UnVsZVNlYXJjaElucHV0OiAnI3NlYXJjaC1jYXJ0LXJ1bGVzLWlucHV0JyxcbiAgY2FydFJ1bGVzU2VhcmNoUmVzdWx0Qm94OiAnI3NlYXJjaC1jYXJ0LXJ1bGVzLXJlc3VsdC1ib3gnLFxuICBjYXJ0UnVsZXNOb3RGb3VuZFRlbXBsYXRlOiAnI2NhcnQtcnVsZXMtbm90LWZvdW5kLXRlbXBsYXRlJyxcbiAgZm91bmRDYXJ0UnVsZVRlbXBsYXRlOiAnI2ZvdW5kLWNhcnQtcnVsZS10ZW1wbGF0ZScsXG4gIGZvdW5kQ2FydFJ1bGVMaXN0SXRlbTogJy5qcy1mb3VuZC1jYXJ0LXJ1bGUnLFxuICBjYXJ0UnVsZU5hbWVGaWVsZDogJy5qcy1jYXJ0LXJ1bGUtbmFtZScsXG4gIGNhcnRSdWxlRGVzY3JpcHRpb25GaWVsZDogJy5qcy1jYXJ0LXJ1bGUtZGVzY3JpcHRpb24nLFxuICBjYXJ0UnVsZVZhbHVlRmllbGQ6ICcuanMtY2FydC1ydWxlLXZhbHVlJyxcbiAgY2FydFJ1bGVEZWxldGVCdG46ICcuanMtY2FydC1ydWxlLWRlbGV0ZS1idG4nLFxuICBjYXJ0UnVsZUVycm9yQmxvY2s6ICcjanMtY2FydC1ydWxlLWVycm9yLWJsb2NrJyxcbiAgY2FydFJ1bGVFcnJvclRleHQ6ICcjanMtY2FydC1ydWxlLWVycm9yLXRleHQnLFxuXG4gIC8vIHNlbGVjdG9ycyByZWxhdGVkIHRvIGFkZHJlc3NlcyBibG9ja1xuICBhZGRyZXNzZXNCbG9jazogJyNhZGRyZXNzZXMtYmxvY2snLFxuICBkZWxpdmVyeUFkZHJlc3NEZXRhaWxzOiAnI2RlbGl2ZXJ5LWFkZHJlc3MtZGV0YWlscycsXG4gIGludm9pY2VBZGRyZXNzRGV0YWlsczogJyNpbnZvaWNlLWFkZHJlc3MtZGV0YWlscycsXG4gIGRlbGl2ZXJ5QWRkcmVzc1NlbGVjdDogJyNkZWxpdmVyeS1hZGRyZXNzLXNlbGVjdCcsXG4gIGludm9pY2VBZGRyZXNzU2VsZWN0OiAnI2ludm9pY2UtYWRkcmVzcy1zZWxlY3QnLFxuICBhZGRyZXNzU2VsZWN0OiAnLmpzLWFkZHJlc3Mtc2VsZWN0JyxcbiAgYWRkcmVzc2VzQ29udGVudDogJyNhZGRyZXNzZXMtY29udGVudCcsXG4gIGFkZHJlc3Nlc1dhcm5pbmc6ICcjYWRkcmVzc2VzLXdhcm5pbmcnLFxuICBkZWxpdmVyeUFkZHJlc3NFZGl0QnRuOiAnI2pzLWRlbGl2ZXJ5LWFkZHJlc3MtZWRpdC1idG4nLFxuICBpbnZvaWNlQWRkcmVzc0VkaXRCdG46ICcjanMtaW52b2ljZS1hZGRyZXNzLWVkaXQtYnRuJyxcbiAgYWRkcmVzc0FkZEJ0bjogJyNqcy1hZGQtYWRkcmVzcy1idG4nLFxuXG4gIC8vIHNlbGVjdG9ycyByZWxhdGVkIHRvIHN1bW1hcnkgYmxvY2tcbiAgc3VtbWFyeUJsb2NrOiAnI3N1bW1hcnktYmxvY2snLFxuICBzdW1tYXJ5VG90YWxQcm9kdWN0czogJy5qcy10b3RhbC1wcm9kdWN0cycsXG4gIHN1bW1hcnlUb3RhbERpc2NvdW50OiAnLmpzLXRvdGFsLWRpc2NvdW50cycsXG4gIHN1bW1hcnlUb3RhbFNoaXBwaW5nOiAnLmpzLXRvdGFsLXNoaXBwaW5nJyxcbiAgc3VtbWFyeVRvdGFsVGF4ZXM6ICcuanMtdG90YWwtdGF4ZXMnLFxuICBzdW1tYXJ5VG90YWxXaXRob3V0VGF4OiAnLmpzLXRvdGFsLXdpdGhvdXQtdGF4JyxcbiAgc3VtbWFyeVRvdGFsV2l0aFRheDogJy5qcy10b3RhbC13aXRoLXRheCcsXG4gIHBsYWNlT3JkZXJDYXJ0SWRGaWVsZDogJy5qcy1wbGFjZS1vcmRlci1jYXJ0LWlkJyxcbiAgcHJvY2Vzc09yZGVyTGlua1RhZzogJyNqcy1wcm9jZXNzLW9yZGVyLWxpbmsnLFxuICBvcmRlck1lc3NhZ2VGaWVsZDogJyNqcy1vcmRlci1tZXNzYWdlLXdyYXAgdGV4dGFyZWEnLFxuICBzZW5kUHJvY2Vzc09yZGVyRW1haWxCdG46ICcjanMtc2VuZC1wcm9jZXNzLW9yZGVyLWVtYWlsLWJ0bicsXG4gIHN1bW1hcnlTdWNjZXNzQWxlcnRCbG9jazogJyNqcy1zdW1tYXJ5LXN1Y2Nlc3MtYmxvY2snLFxuICBzdW1tYXJ5RXJyb3JBbGVydEJsb2NrOiAnI2pzLXN1bW1hcnktZXJyb3ItYmxvY2snLFxuICBzdW1tYXJ5U3VjY2Vzc0FsZXJ0VGV4dDogJyNqcy1zdW1tYXJ5LXN1Y2Nlc3MtYmxvY2sgLmFsZXJ0LXRleHQnLFxuICBzdW1tYXJ5RXJyb3JBbGVydFRleHQ6ICcjanMtc3VtbWFyeS1lcnJvci1ibG9jayAuYWxlcnQtdGV4dCcsXG5cbiAgLy8gc2VsZWN0b3JzIHJlbGF0ZWQgdG8gc2hpcHBpbmcgYmxvY2tcbiAgc2hpcHBpbmdCbG9jazogJyNzaGlwcGluZy1ibG9jaycsXG4gIHNoaXBwaW5nRm9ybTogJy5qcy1zaGlwcGluZy1mb3JtJyxcbiAgbm9DYXJyaWVyQmxvY2s6ICcuanMtbm8tY2Fycmllci1ibG9jaycsXG4gIGRlbGl2ZXJ5T3B0aW9uU2VsZWN0OiAnI2RlbGl2ZXJ5LW9wdGlvbi1zZWxlY3QnLFxuICB0b3RhbFNoaXBwaW5nRmllbGQ6ICcuanMtdG90YWwtc2hpcHBpbmcnLFxuICBmcmVlU2hpcHBpbmdTd2l0Y2g6ICcuanMtZnJlZS1zaGlwcGluZy1zd2l0Y2gnLFxuXG4gIC8vIHNlbGVjdG9ycyByZWxhdGVkIHRvIGNhcnQgYmxvY2tcbiAgY2FydEJsb2NrOiAnI2NhcnQtYmxvY2snLFxuICBjYXJ0Q3VycmVuY3lTZWxlY3Q6ICcjanMtY2FydC1jdXJyZW5jeS1zZWxlY3QnLFxuICBjYXJ0TGFuZ3VhZ2VTZWxlY3Q6ICcjanMtY2FydC1sYW5ndWFnZS1zZWxlY3QnLFxuICBwcm9kdWN0U2VhcmNoOiAnI3Byb2R1Y3Qtc2VhcmNoJyxcbiAgY29tYmluYXRpb25zU2VsZWN0OiAnI2NvbWJpbmF0aW9uLXNlbGVjdCcsXG4gIHByb2R1Y3RSZXN1bHRCbG9jazogJyNwcm9kdWN0LXNlYXJjaC1yZXN1bHRzJyxcbiAgcHJvZHVjdFNlbGVjdDogJyNwcm9kdWN0LXNlbGVjdCcsXG4gIHF1YW50aXR5SW5wdXQ6ICcjcXVhbnRpdHktaW5wdXQnLFxuICBpblN0b2NrQ291bnRlcjogJy5qcy1pbi1zdG9jay1jb3VudGVyJyxcbiAgY29tYmluYXRpb25zVGVtcGxhdGU6ICcjY29tYmluYXRpb25zLXRlbXBsYXRlJyxcbiAgY29tYmluYXRpb25zUm93OiAnLmpzLWNvbWJpbmF0aW9ucy1yb3cnLFxuICBwcm9kdWN0U2VsZWN0Um93OiAnLmpzLXByb2R1Y3Qtc2VsZWN0LXJvdycsXG4gIHByb2R1Y3RDdXN0b21GaWVsZHNDb250YWluZXI6ICcjanMtY3VzdG9tLWZpZWxkcy1jb250YWluZXInLFxuICBwcm9kdWN0Q3VzdG9taXphdGlvbkNvbnRhaW5lcjogJyNqcy1jdXN0b21pemF0aW9uLWNvbnRhaW5lcicsXG4gIHByb2R1Y3RDdXN0b21GaWxlVGVtcGxhdGU6ICcjanMtcHJvZHVjdC1jdXN0b20tZmlsZS10ZW1wbGF0ZScsXG4gIHByb2R1Y3RDdXN0b21UZXh0VGVtcGxhdGU6ICcjanMtcHJvZHVjdC1jdXN0b20tdGV4dC10ZW1wbGF0ZScsXG4gIHByb2R1Y3RDdXN0b21JbnB1dExhYmVsOiAnLmpzLXByb2R1Y3QtY3VzdG9tLWlucHV0LWxhYmVsJyxcbiAgcHJvZHVjdEN1c3RvbUlucHV0OiAnLmpzLXByb2R1Y3QtY3VzdG9tLWlucHV0JyxcbiAgcXVhbnRpdHlSb3c6ICcuanMtcXVhbnRpdHktcm93JyxcbiAgYWRkVG9DYXJ0QnV0dG9uOiAnI2FkZC1wcm9kdWN0LXRvLWNhcnQtYnRuJyxcbiAgcHJvZHVjdHNUYWJsZTogJyNwcm9kdWN0cy10YWJsZScsXG4gIHByb2R1Y3RzVGFibGVSb3dUZW1wbGF0ZTogJyNwcm9kdWN0cy10YWJsZS1yb3ctdGVtcGxhdGUnLFxuICBsaXN0ZWRQcm9kdWN0SW1hZ2VGaWVsZDogJy5qcy1wcm9kdWN0LWltYWdlJyxcbiAgbGlzdGVkUHJvZHVjdE5hbWVGaWVsZDogJy5qcy1wcm9kdWN0LW5hbWUnLFxuICBsaXN0ZWRQcm9kdWN0QXR0ckZpZWxkOiAnLmpzLXByb2R1Y3QtYXR0cicsXG4gIGxpc3RlZFByb2R1Y3RSZWZlcmVuY2VGaWVsZDogJy5qcy1wcm9kdWN0LXJlZicsXG4gIGxpc3RlZFByb2R1Y3RVbml0UHJpY2VJbnB1dDogJy5qcy1wcm9kdWN0LXVuaXQtaW5wdXQnLFxuICBsaXN0ZWRQcm9kdWN0UXR5SW5wdXQ6ICcuanMtcHJvZHVjdC1xdHktaW5wdXQnLFxuICBwcm9kdWN0VG90YWxQcmljZUZpZWxkOiAnLmpzLXByb2R1Y3QtdG90YWwtcHJpY2UnLFxuICBsaXN0ZWRQcm9kdWN0Q3VzdG9taXplZFRleHRUZW1wbGF0ZTogJyNqcy10YWJsZS1wcm9kdWN0LWN1c3RvbWl6ZWQtdGV4dC10ZW1wbGF0ZScsXG4gIGxpc3RlZFByb2R1Y3RDdXN0b21pemVkRmlsZVRlbXBsYXRlOiAnI2pzLXRhYmxlLXByb2R1Y3QtY3VzdG9taXplZC1maWxlLXRlbXBsYXRlJyxcbiAgbGlzdGVkUHJvZHVjdEN1c3RvbWl6YXRpb25OYW1lOiAnLmpzLWN1c3RvbWl6YXRpb24tbmFtZScsXG4gIGxpc3RlZFByb2R1Y3RDdXN0b21pemF0aW9uVmFsdWU6ICcuanMtY3VzdG9taXphdGlvbi12YWx1ZScsXG4gIGxpc3RlZFByb2R1Y3REZWZpbml0aW9uOiAnLmpzLXByb2R1Y3QtZGVmaW5pdGlvbi10ZCcsXG4gIHByb2R1Y3RSZW1vdmVCdG46ICcuanMtcHJvZHVjdC1yZW1vdmUtYnRuJyxcbiAgcHJvZHVjdFRheFdhcm5pbmc6ICcuanMtdGF4LXdhcm5pbmcnLFxuICBub1Byb2R1Y3RzRm91bmRXYXJuaW5nOiAnLmpzLW5vLXByb2R1Y3RzLWZvdW5kJyxcbiAgcHJvZHVjdEFkZEZvcm06ICcjanMtYWRkLXByb2R1Y3QtZm9ybScsXG4gIGNhcnRFcnJvckFsZXJ0QmxvY2s6ICcjanMtY2FydC1lcnJvci1ibG9jaycsXG4gIGNhcnRFcnJvckFsZXJ0VGV4dDogJyNqcy1jYXJ0LWVycm9yLWJsb2NrIC5hbGVydC10ZXh0Jyxcbn07XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvY3JlYXRlLW9yZGVyLW1hcC5qcyIsInZhciBpc09iamVjdCA9IHJlcXVpcmUoJy4vX2lzLW9iamVjdCcpO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCl7XG4gIGlmKCFpc09iamVjdChpdCkpdGhyb3cgVHlwZUVycm9yKGl0ICsgJyBpcyBub3QgYW4gb2JqZWN0IScpO1xuICByZXR1cm4gaXQ7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fYW4tb2JqZWN0LmpzXG4vLyBtb2R1bGUgaWQgPSAxMVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJtb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGJpdG1hcCwgdmFsdWUpe1xuICByZXR1cm4ge1xuICAgIGVudW1lcmFibGUgIDogIShiaXRtYXAgJiAxKSxcbiAgICBjb25maWd1cmFibGU6ICEoYml0bWFwICYgMiksXG4gICAgd3JpdGFibGUgICAgOiAhKGJpdG1hcCAmIDQpLFxuICAgIHZhbHVlICAgICAgIDogdmFsdWVcbiAgfTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19wcm9wZXJ0eS1kZXNjLmpzXG4vLyBtb2R1bGUgaWQgPSAxMlxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCIvLyA3LjEuMSBUb1ByaW1pdGl2ZShpbnB1dCBbLCBQcmVmZXJyZWRUeXBlXSlcbnZhciBpc09iamVjdCA9IHJlcXVpcmUoJy4vX2lzLW9iamVjdCcpO1xuLy8gaW5zdGVhZCBvZiB0aGUgRVM2IHNwZWMgdmVyc2lvbiwgd2UgZGlkbid0IGltcGxlbWVudCBAQHRvUHJpbWl0aXZlIGNhc2Vcbi8vIGFuZCB0aGUgc2Vjb25kIGFyZ3VtZW50IC0gZmxhZyAtIHByZWZlcnJlZCB0eXBlIGlzIGEgc3RyaW5nXG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0LCBTKXtcbiAgaWYoIWlzT2JqZWN0KGl0KSlyZXR1cm4gaXQ7XG4gIHZhciBmbiwgdmFsO1xuICBpZihTICYmIHR5cGVvZiAoZm4gPSBpdC50b1N0cmluZykgPT0gJ2Z1bmN0aW9uJyAmJiAhaXNPYmplY3QodmFsID0gZm4uY2FsbChpdCkpKXJldHVybiB2YWw7XG4gIGlmKHR5cGVvZiAoZm4gPSBpdC52YWx1ZU9mKSA9PSAnZnVuY3Rpb24nICYmICFpc09iamVjdCh2YWwgPSBmbi5jYWxsKGl0KSkpcmV0dXJuIHZhbDtcbiAgaWYoIVMgJiYgdHlwZW9mIChmbiA9IGl0LnRvU3RyaW5nKSA9PSAnZnVuY3Rpb24nICYmICFpc09iamVjdCh2YWwgPSBmbi5jYWxsKGl0KSkpcmV0dXJuIHZhbDtcbiAgdGhyb3cgVHlwZUVycm9yKFwiQ2FuJ3QgY29udmVydCBvYmplY3QgdG8gcHJpbWl0aXZlIHZhbHVlXCIpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3RvLXByaW1pdGl2ZS5qc1xuLy8gbW9kdWxlIGlkID0gMTNcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiLyoqXG4gKiAyMDA3LTIwMjAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAyMCBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuLyoqXG4gKiBFbmNhcHN1bGF0ZXMganMgZXZlbnRzIHVzZWQgaW4gY3JlYXRlIG9yZGVyIHBhZ2VcbiAqL1xuZXhwb3J0IGRlZmF1bHQge1xuICAvLyB3aGVuIGN1c3RvbWVyIHNlYXJjaCBhY3Rpb24gaXMgZG9uZVxuICBjdXN0b21lclNlYXJjaGVkOiAnT3JkZXJDcmVhdGVDdXN0b21lclNlYXJjaGVkJyxcbiAgLy8gd2hlbiBuZXcgY3VzdG9tZXIgaXMgc2VsZWN0ZWRcbiAgY3VzdG9tZXJTZWxlY3RlZDogJ09yZGVyQ3JlYXRlQ3VzdG9tZXJTZWxlY3RlZCcsXG4gIC8vIHdoZW4gbm8gY3VzdG9tZXJzIGZvdW5kIGJ5IHNlYXJjaFxuICBjdXN0b21lcnNOb3RGb3VuZDogJ09yZGVyQ3JlYXRlU2VhcmNoQ3VzdG9tZXJOb3RGb3VuZCcsXG4gIC8vIHdoZW4gbmV3IGNhcnQgaXMgbG9hZGVkLFxuICAvLyAgbm8gbWF0dGVyIGlmIGl0cyBlbXB0eSwgc2VsZWN0ZWQgZnJvbSBjYXJ0cyBsaXN0IG9yIGR1cGxpY2F0ZWQgYnkgb3JkZXIuXG4gIGNhcnRMb2FkZWQ6ICdPcmRlckNyZWF0ZUNhcnRMb2FkZWQnLFxuICAvLyB3aGVuIGNhcnQgY3VycmVuY3kgaGFzIGJlZW4gY2hhbmdlZFxuICBjYXJ0Q3VycmVuY3lDaGFuZ2VkOiAnT3JkZXJDcmVhdGVDYXJ0Q3VycmVuY3lDaGFuZ2VkJyxcbiAgLy8gd2hlbiBjYXJ0IGN1cnJlbmN5IGNoYW5naW5nIGZhaWxzXG4gIGNhcnRDdXJyZW5jeUNoYW5nZUZhaWxlZDogJ09yZGVyQ3JlYXRlQ2FydEN1cnJlbmN5Q2hhbmdlRmFpbGVkJyxcbiAgLy8gd2hlbiBjYXJ0IGxhbmd1YWdlIGhhcyBiZWVuIGNoYW5nZWRcbiAgY2FydExhbmd1YWdlQ2hhbmdlZDogJ09yZGVyQ3JlYXRlQ2FydExhbmd1YWdlQ2hhbmdlZCcsXG4gIC8vIHdoZW4gY2FydCBhZGRyZXNzZXMgaW5mb3JtYXRpb24gaGFzIGJlZW4gY2hhbmdlZFxuICBjYXJ0QWRkcmVzc2VzQ2hhbmdlZDogJ09yZGVyQ3JlYXRlQ2FydEFkZHJlc3Nlc0NoYW5nZWQnLFxuICAvLyB3aGVuIGNhcnQgZGVsaXZlcnkgb3B0aW9uIGhhcyBiZWVuIGNoYW5nZWRcbiAgY2FydERlbGl2ZXJ5T3B0aW9uQ2hhbmdlZDogJ09yZGVyQ3JlYXRlQ2FydERlbGl2ZXJ5T3B0aW9uQ2hhbmdlZCcsXG4gIC8vIHdoZW4gY2FydCBmcmVlIHNoaXBwaW5nIHZhbHVlIGhhcyBiZWVuIGNoYW5nZWRcbiAgY2FydEZyZWVTaGlwcGluZ1NldDogJ09yZGVyQ3JlYXRlQ2FydEZyZWVTaGlwcGluZ1NldCcsXG4gIC8vIHdoZW4gY2FydCBydWxlcyBzZWFyY2ggYWN0aW9uIGlzIGRvbmVcbiAgY2FydFJ1bGVTZWFyY2hlZDogJ09yZGVyQ3JlYXRlQ2FydFJ1bGVTZWFyY2hlZCcsXG4gIC8vIHdoZW4gY2FydCBydWxlIGlzIHJlbW92ZWQgZnJvbSBjYXJ0XG4gIGNhcnRSdWxlUmVtb3ZlZDogJ09yZGVyQ3JlYXRlQ2FydFJ1bGVSZW1vdmVkJyxcbiAgLy8gd2hlbiBjYXJ0IHJ1bGUgaXMgYWRkZWQgdG8gY2FydFxuICBjYXJ0UnVsZUFkZGVkOiAnT3JkZXJDcmVhdGVDYXJ0UnVsZUFkZGVkJyxcbiAgLy8gd2hlbiBjYXJ0IHJ1bGUgY2Fubm90IGJlIGFkZGVkIHRvIGNhcnRcbiAgY2FydFJ1bGVGYWlsZWRUb0FkZDogJ09yZGVyQ3JlYXRlQ2FydFJ1bGVGYWlsZWRUb0FkZCcsXG4gIC8vIHdoZW4gcHJvZHVjdCBzZWFyY2ggYWN0aW9uIGlzIGRvbmVcbiAgcHJvZHVjdFNlYXJjaGVkOiAnT3JkZXJDcmVhdGVQcm9kdWN0U2VhcmNoZWQnLFxuICAvLyB3aGVuIHByb2R1Y3QgaXMgYWRkZWQgdG8gY2FydFxuICBwcm9kdWN0QWRkZWRUb0NhcnQ6ICdPcmRlckNyZWF0ZVByb2R1Y3RBZGRlZFRvQ2FydCcsXG4gIC8vIHdoZW4gYWRkaW5nIHByb2R1Y3QgdG8gY2FydCBmYWlsc1xuICBwcm9kdWN0QWRkVG9DYXJ0RmFpbGVkOiAnT3JkZXJDcmVhdGVQcm9kdWN0QWRkVG9DYXJ0RmFpbGVkJyxcbiAgLy8gd2hlbiBwcm9kdWN0IGlzIHJlbW92ZWQgZnJvbSBjYXJ0XG4gIHByb2R1Y3RSZW1vdmVkRnJvbUNhcnQ6ICdPcmRlckNyZWF0ZVByb2R1Y3RSZW1vdmVkRnJvbUNhcnQnLFxuICAvLyB3aGVuIHByb2R1Y3QgaW4gY2FydCBwcmljZSBoYXMgYmVlbiBjaGFuZ2VkXG4gIHByb2R1Y3RQcmljZUNoYW5nZWQ6ICdPcmRlckNyZWF0ZVByb2R1Y3RQcmljZUNoYW5nZWQnLFxuICAvLyB3aGVuIHByb2R1Y3QgcXVhbnRpdHkgaW4gY2FydCBoYXMgYmVlbiBjaGFuZ2VkXG4gIHByb2R1Y3RRdHlDaGFuZ2VkOiAnT3JkZXJDcmVhdGVQcm9kdWN0UXR5Q2hhbmdlZCcsXG4gIC8vIHdoZW4gY2hhbmdpbmcgcHJvZHVjdCBxdWFudGl0eSBpbiBjYXJ0IGZhaWxlZFxuICBwcm9kdWN0UXR5Q2hhbmdlRmFpbGVkOiAnT3JkZXJDcmVhdGVQcm9kdWN0UXR5Q2hhbmdlRmFpbGVkJyxcbiAgLy8gd2hlbiBvcmRlciBwcm9jZXNzIGVtYWlsIGhhcyBiZWVuIHNlbnQgdG8gY3VzdG9tZXJcbiAgcHJvY2Vzc09yZGVyRW1haWxTZW50OiAnT3JkZXJDcmVhdGVQcm9jZXNzT3JkZXJFbWFpbFNlbnQnLFxuICAvLyB3aGVuIG9yZGVyIHByb2Nlc3MgZW1haWwgc2VuZGluZyBmYWlsZWRcbiAgcHJvY2Vzc09yZGVyRW1haWxGYWlsZWQ6ICdPcmRlckNyZWF0ZVByb2Nlc3NPcmRlckVtYWlsRmFpbGVkJyxcbn07XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvZXZlbnQtbWFwLmpzIiwiLy8gb3B0aW9uYWwgLyBzaW1wbGUgY29udGV4dCBiaW5kaW5nXG52YXIgYUZ1bmN0aW9uID0gcmVxdWlyZSgnLi9fYS1mdW5jdGlvbicpO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihmbiwgdGhhdCwgbGVuZ3RoKXtcbiAgYUZ1bmN0aW9uKGZuKTtcbiAgaWYodGhhdCA9PT0gdW5kZWZpbmVkKXJldHVybiBmbjtcbiAgc3dpdGNoKGxlbmd0aCl7XG4gICAgY2FzZSAxOiByZXR1cm4gZnVuY3Rpb24oYSl7XG4gICAgICByZXR1cm4gZm4uY2FsbCh0aGF0LCBhKTtcbiAgICB9O1xuICAgIGNhc2UgMjogcmV0dXJuIGZ1bmN0aW9uKGEsIGIpe1xuICAgICAgcmV0dXJuIGZuLmNhbGwodGhhdCwgYSwgYik7XG4gICAgfTtcbiAgICBjYXNlIDM6IHJldHVybiBmdW5jdGlvbihhLCBiLCBjKXtcbiAgICAgIHJldHVybiBmbi5jYWxsKHRoYXQsIGEsIGIsIGMpO1xuICAgIH07XG4gIH1cbiAgcmV0dXJuIGZ1bmN0aW9uKC8qIC4uLmFyZ3MgKi8pe1xuICAgIHJldHVybiBmbi5hcHBseSh0aGF0LCBhcmd1bWVudHMpO1xuICB9O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2N0eC5qc1xuLy8gbW9kdWxlIGlkID0gMTVcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwidmFyIGlzT2JqZWN0ID0gcmVxdWlyZSgnLi9faXMtb2JqZWN0JylcbiAgLCBkb2N1bWVudCA9IHJlcXVpcmUoJy4vX2dsb2JhbCcpLmRvY3VtZW50XG4gIC8vIGluIG9sZCBJRSB0eXBlb2YgZG9jdW1lbnQuY3JlYXRlRWxlbWVudCBpcyAnb2JqZWN0J1xuICAsIGlzID0gaXNPYmplY3QoZG9jdW1lbnQpICYmIGlzT2JqZWN0KGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQpO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCl7XG4gIHJldHVybiBpcyA/IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoaXQpIDoge307XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZG9tLWNyZWF0ZS5qc1xuLy8gbW9kdWxlIGlkID0gMTZcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwibW9kdWxlLmV4cG9ydHMgPSB7XCJiYXNlX3VybFwiOlwiXCIsXCJyb3V0ZXNcIjp7XCJhZG1pbl9wcm9kdWN0X2Zvcm1cIjp7XCJ0b2tlbnNcIjpbW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiaWRcIl0sW1widGV4dFwiLFwiL3NlbGwvY2F0YWxvZy9wcm9kdWN0c1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wiaWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIixcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fcHJvZHVjdHNfc2VhcmNoXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9zZWxsL2NhdGFsb2cvcHJvZHVjdHMvc2VhcmNoXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjpbXSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY2FydF9ydWxlc19zZWFyY2hcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3NlbGwvY2F0YWxvZy9jYXJ0LXJ1bGVzL3NlYXJjaFwiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6W10sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiR0VUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2N1c3RvbWVyc192aWV3XCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi92aWV3XCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcImN1c3RvbWVySWRcIl0sW1widGV4dFwiLFwiL3NlbGwvY3VzdG9tZXJzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJjdXN0b21lcklkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiR0VUXCIsXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2N1c3RvbWVyc19zZWFyY2hcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3NlbGwvY3VzdG9tZXJzL3NlYXJjaFwiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6W10sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiR0VUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2N1c3RvbWVyc19jYXJ0c1wiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvY2FydHNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiY3VzdG9tZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9jdXN0b21lcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImN1c3RvbWVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY3VzdG9tZXJzX29yZGVyc1wiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvb3JkZXJzXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcImN1c3RvbWVySWRcIl0sW1widGV4dFwiLFwiL3NlbGwvY3VzdG9tZXJzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJjdXN0b21lcklkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiR0VUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2FkZHJlc3Nlc19jcmVhdGVcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3NlbGwvYWRkcmVzc2VzL25ld1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6W10sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiR0VUXCIsXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2FkZHJlc3Nlc19lZGl0XCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9lZGl0XCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcImFkZHJlc3NJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9hZGRyZXNzZXNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImFkZHJlc3NJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIkdFVFwiLFwiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jYXJ0c192aWV3XCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi92aWV3XCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcImNhcnRJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvY2FydHNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImNhcnRJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIkdFVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jYXJ0c19pbmZvXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9pbmZvXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcImNhcnRJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvY2FydHNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImNhcnRJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIkdFVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jYXJ0c19jcmVhdGVcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL2NhcnRzL25ld1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6W10sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jYXJ0c19lZGl0X2FkZHJlc3Nlc1wiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvYWRkcmVzc2VzXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcImNhcnRJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvY2FydHNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImNhcnRJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY2FydHNfZWRpdF9jYXJyaWVyXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9jYXJyaWVyXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcImNhcnRJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvY2FydHNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImNhcnRJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY2FydHNfZWRpdF9jdXJyZW5jeVwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvY3VycmVuY3lcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiY2FydElkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVycy9jYXJ0c1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wiY2FydElkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jYXJ0c19lZGl0X2xhbmd1YWdlXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9sYW5ndWFnZVwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJjYXJ0SWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL2NhcnRzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJjYXJ0SWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2NhcnRzX3NldF9mcmVlX3NoaXBwaW5nXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9ydWxlcy9mcmVlLXNoaXBwaW5nXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcImNhcnRJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvY2FydHNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImNhcnRJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY2FydHNfYWRkX2NhcnRfcnVsZVwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvY2FydC1ydWxlc1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiW14vXSsrXCIsXCJjYXJ0SWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL2NhcnRzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjpbXSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2NhcnRzX2RlbGV0ZV9jYXJ0X3J1bGVcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL2RlbGV0ZVwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiW14vXSsrXCIsXCJjYXJ0UnVsZUlkXCJdLFtcInRleHRcIixcIi9jYXJ0LXJ1bGVzXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJbXi9dKytcIixcImNhcnRJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvY2FydHNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOltdLFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY2FydHNfYWRkX3Byb2R1Y3RcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3Byb2R1Y3RzXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcImNhcnRJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvY2FydHNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImNhcnRJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fY2FydHNfZWRpdF9wcm9kdWN0X3ByaWNlXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9wcmljZVwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJwcm9kdWN0SWRcIl0sW1widGV4dFwiLFwiL3Byb2R1Y3RzXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcImNhcnRJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvY2FydHNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcImNhcnRJZFwiOlwiXFxcXGQrXCIsXCJwcm9kdWN0SWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX2NhcnRzX2VkaXRfcHJvZHVjdF9xdWFudGl0eVwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvcXVhbnRpdHlcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwicHJvZHVjdElkXCJdLFtcInRleHRcIixcIi9wcm9kdWN0c1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJjYXJ0SWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL2NhcnRzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJjYXJ0SWRcIjpcIlxcXFxkK1wiLFwicHJvZHVjdElkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9jYXJ0c19kZWxldGVfcHJvZHVjdFwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvZGVsZXRlLXByb2R1Y3RcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwiY2FydElkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVycy9jYXJ0c1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wiY2FydElkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9vcmRlcnNfcGxhY2VcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL29yZGVycy9wbGFjZVwiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6W10sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9vcmRlcnNfdmlld1wiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvdmlld1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVycy9vcmRlcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcIm9yZGVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIixcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX2R1cGxpY2F0ZV9jYXJ0XCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9kdXBsaWNhdGUtY2FydFwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVycy9vcmRlcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcIm9yZGVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX29yZGVyc191cGRhdGVfcHJvZHVjdFwiOntcInRva2Vuc1wiOltbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlckRldGFpbElkXCJdLFtcInRleHRcIixcIi9wcm9kdWN0c1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVycy9vcmRlcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcIm9yZGVySWRcIjpcIlxcXFxkK1wiLFwib3JkZXJEZXRhaWxJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX3BhcnRpYWxfcmVmdW5kXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9wYXJ0aWFsLXJlZnVuZFwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVycy9vcmRlcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcIm9yZGVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX29yZGVyc19zdGFuZGFyZF9yZWZ1bmRcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3N0YW5kYXJkLXJlZnVuZFwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVycy9vcmRlcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcIm9yZGVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX29yZGVyc19yZXR1cm5fcHJvZHVjdFwiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvcmV0dXJuLXByb2R1Y3RcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwib3JkZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvb3JkZXJzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJvcmRlcklkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiUE9TVFwiXSxcInNjaGVtZXNcIjpbXX0sXCJhZG1pbl9vcmRlcnNfYWRkX3Byb2R1Y3RcIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL3Byb2R1Y3RzXCJdLFtcInZhcmlhYmxlXCIsXCIvXCIsXCJcXFxcZCtcIixcIm9yZGVySWRcIl0sW1widGV4dFwiLFwiL3NlbGwvb3JkZXJzL29yZGVyc1wiXV0sXCJkZWZhdWx0c1wiOltdLFwicmVxdWlyZW1lbnRzXCI6e1wib3JkZXJJZFwiOlwiXFxcXGQrXCJ9LFwiaG9zdHRva2Vuc1wiOltdLFwibWV0aG9kc1wiOltcIlBPU1RcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX2RlbGV0ZV9wcm9kdWN0XCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9kZWxldGVcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwib3JkZXJEZXRhaWxJZFwiXSxbXCJ0ZXh0XCIsXCIvcHJvZHVjdHNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwib3JkZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvb3JkZXJzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJvcmRlcklkXCI6XCJcXFxcZCtcIixcIm9yZGVyRGV0YWlsSWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX29yZGVyc19nZXRfcHJpY2VzXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9wcmljZXNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwib3JkZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvb3JkZXJzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJvcmRlcklkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiR0VUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX29yZGVyc19nZXRfcGFnaW5hdGVkX3Byb2R1Y3RzXCI6e1widG9rZW5zXCI6W1tcInRleHRcIixcIi9wcm9kdWN0c1wiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVycy9vcmRlcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcIm9yZGVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJHRVRcIl0sXCJzY2hlbWVzXCI6W119LFwiYWRtaW5fb3JkZXJzX2dldF9pbnZvaWNlc1wiOntcInRva2Vuc1wiOltbXCJ0ZXh0XCIsXCIvaW52b2ljZXNcIl0sW1widmFyaWFibGVcIixcIi9cIixcIlxcXFxkK1wiLFwib3JkZXJJZFwiXSxbXCJ0ZXh0XCIsXCIvc2VsbC9vcmRlcnMvb3JkZXJzXCJdXSxcImRlZmF1bHRzXCI6W10sXCJyZXF1aXJlbWVudHNcIjp7XCJvcmRlcklkXCI6XCJcXFxcZCtcIn0sXCJob3N0dG9rZW5zXCI6W10sXCJtZXRob2RzXCI6W1wiR0VUXCJdLFwic2NoZW1lc1wiOltdfSxcImFkbWluX29yZGVyc19jYW5jZWxsYXRpb25cIjp7XCJ0b2tlbnNcIjpbW1widGV4dFwiLFwiL2NhbmNlbGxhdGlvblwiXSxbXCJ2YXJpYWJsZVwiLFwiL1wiLFwiXFxcXGQrXCIsXCJvcmRlcklkXCJdLFtcInRleHRcIixcIi9zZWxsL29yZGVycy9vcmRlcnNcIl1dLFwiZGVmYXVsdHNcIjpbXSxcInJlcXVpcmVtZW50c1wiOntcIm9yZGVySWRcIjpcIlxcXFxkK1wifSxcImhvc3R0b2tlbnNcIjpbXSxcIm1ldGhvZHNcIjpbXCJQT1NUXCJdLFwic2NoZW1lc1wiOltdfX0sXCJwcmVmaXhcIjpcIlwiLFwiaG9zdFwiOlwibG9jYWxob3N0XCIsXCJwb3J0XCI6XCJcIixcInNjaGVtZVwiOlwiaHR0cFwiLFwibG9jYWxlXCI6W119XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9qcy9mb3NfanNfcm91dGVzLmpzb25cbi8vIG1vZHVsZSBpZCA9IDE2MVxuLy8gbW9kdWxlIGNodW5rcyA9IDMgMTAgMTMiLCJcInVzZSBzdHJpY3RcIjtcblxuZXhwb3J0cy5fX2VzTW9kdWxlID0gdHJ1ZTtcblxudmFyIF9kZWZpbmVQcm9wZXJ0eSA9IHJlcXVpcmUoXCIuLi9jb3JlLWpzL29iamVjdC9kZWZpbmUtcHJvcGVydHlcIik7XG5cbnZhciBfZGVmaW5lUHJvcGVydHkyID0gX2ludGVyb3BSZXF1aXJlRGVmYXVsdChfZGVmaW5lUHJvcGVydHkpO1xuXG5mdW5jdGlvbiBfaW50ZXJvcFJlcXVpcmVEZWZhdWx0KG9iaikgeyByZXR1cm4gb2JqICYmIG9iai5fX2VzTW9kdWxlID8gb2JqIDogeyBkZWZhdWx0OiBvYmogfTsgfVxuXG5leHBvcnRzLmRlZmF1bHQgPSBmdW5jdGlvbiAob2JqLCBrZXksIHZhbHVlKSB7XG4gIGlmIChrZXkgaW4gb2JqKSB7XG4gICAgKDAsIF9kZWZpbmVQcm9wZXJ0eTIuZGVmYXVsdCkob2JqLCBrZXksIHtcbiAgICAgIHZhbHVlOiB2YWx1ZSxcbiAgICAgIGVudW1lcmFibGU6IHRydWUsXG4gICAgICBjb25maWd1cmFibGU6IHRydWUsXG4gICAgICB3cml0YWJsZTogdHJ1ZVxuICAgIH0pO1xuICB9IGVsc2Uge1xuICAgIG9ialtrZXldID0gdmFsdWU7XG4gIH1cblxuICByZXR1cm4gb2JqO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vYmFiZWwtcnVudGltZS9oZWxwZXJzL2RlZmluZVByb3BlcnR5LmpzXG4vLyBtb2R1bGUgaWQgPSAxNjdcbi8vIG1vZHVsZSBjaHVua3MgPSAyIDYgMTAiLCJtb2R1bGUuZXhwb3J0cyA9ICFyZXF1aXJlKCcuL19kZXNjcmlwdG9ycycpICYmICFyZXF1aXJlKCcuL19mYWlscycpKGZ1bmN0aW9uKCl7XG4gIHJldHVybiBPYmplY3QuZGVmaW5lUHJvcGVydHkocmVxdWlyZSgnLi9fZG9tLWNyZWF0ZScpKCdkaXYnKSwgJ2EnLCB7Z2V0OiBmdW5jdGlvbigpeyByZXR1cm4gNzsgfX0pLmEgIT0gNztcbn0pO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faWU4LWRvbS1kZWZpbmUuanNcbi8vIG1vZHVsZSBpZCA9IDE3XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIid1c2Ugc3RyaWN0Jzt2YXIgX2V4dGVuZHM9T2JqZWN0LmFzc2lnbnx8ZnVuY3Rpb24oYSl7Zm9yKHZhciBiLGM9MTtjPGFyZ3VtZW50cy5sZW5ndGg7YysrKWZvcih2YXIgZCBpbiBiPWFyZ3VtZW50c1tjXSxiKU9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChiLGQpJiYoYVtkXT1iW2RdKTtyZXR1cm4gYX0sX3R5cGVvZj0nZnVuY3Rpb24nPT10eXBlb2YgU3ltYm9sJiYnc3ltYm9sJz09dHlwZW9mIFN5bWJvbC5pdGVyYXRvcj9mdW5jdGlvbihhKXtyZXR1cm4gdHlwZW9mIGF9OmZ1bmN0aW9uKGEpe3JldHVybiBhJiYnZnVuY3Rpb24nPT10eXBlb2YgU3ltYm9sJiZhLmNvbnN0cnVjdG9yPT09U3ltYm9sJiZhIT09U3ltYm9sLnByb3RvdHlwZT8nc3ltYm9sJzp0eXBlb2YgYX07ZnVuY3Rpb24gX2NsYXNzQ2FsbENoZWNrKGEsYil7aWYoIShhIGluc3RhbmNlb2YgYikpdGhyb3cgbmV3IFR5cGVFcnJvcignQ2Fubm90IGNhbGwgYSBjbGFzcyBhcyBhIGZ1bmN0aW9uJyl9dmFyIFJvdXRpbmc9ZnVuY3Rpb24gYSgpe3ZhciBiPXRoaXM7X2NsYXNzQ2FsbENoZWNrKHRoaXMsYSksdGhpcy5zZXRSb3V0ZXM9ZnVuY3Rpb24oYSl7Yi5yb3V0ZXNSb3V0aW5nPWF8fFtdfSx0aGlzLmdldFJvdXRlcz1mdW5jdGlvbigpe3JldHVybiBiLnJvdXRlc1JvdXRpbmd9LHRoaXMuc2V0QmFzZVVybD1mdW5jdGlvbihhKXtiLmNvbnRleHRSb3V0aW5nLmJhc2VfdXJsPWF9LHRoaXMuZ2V0QmFzZVVybD1mdW5jdGlvbigpe3JldHVybiBiLmNvbnRleHRSb3V0aW5nLmJhc2VfdXJsfSx0aGlzLnNldFByZWZpeD1mdW5jdGlvbihhKXtiLmNvbnRleHRSb3V0aW5nLnByZWZpeD1hfSx0aGlzLnNldFNjaGVtZT1mdW5jdGlvbihhKXtiLmNvbnRleHRSb3V0aW5nLnNjaGVtZT1hfSx0aGlzLmdldFNjaGVtZT1mdW5jdGlvbigpe3JldHVybiBiLmNvbnRleHRSb3V0aW5nLnNjaGVtZX0sdGhpcy5zZXRIb3N0PWZ1bmN0aW9uKGEpe2IuY29udGV4dFJvdXRpbmcuaG9zdD1hfSx0aGlzLmdldEhvc3Q9ZnVuY3Rpb24oKXtyZXR1cm4gYi5jb250ZXh0Um91dGluZy5ob3N0fSx0aGlzLmJ1aWxkUXVlcnlQYXJhbXM9ZnVuY3Rpb24oYSxjLGQpe3ZhciBlPW5ldyBSZWdFeHAoL1xcW10kLyk7YyBpbnN0YW5jZW9mIEFycmF5P2MuZm9yRWFjaChmdW5jdGlvbihjLGYpe2UudGVzdChhKT9kKGEsYyk6Yi5idWlsZFF1ZXJ5UGFyYW1zKGErJ1snKygnb2JqZWN0Jz09PSgndW5kZWZpbmVkJz09dHlwZW9mIGM/J3VuZGVmaW5lZCc6X3R5cGVvZihjKSk/ZjonJykrJ10nLGMsZCl9KTonb2JqZWN0Jz09PSgndW5kZWZpbmVkJz09dHlwZW9mIGM/J3VuZGVmaW5lZCc6X3R5cGVvZihjKSk/T2JqZWN0LmtleXMoYykuZm9yRWFjaChmdW5jdGlvbihlKXtyZXR1cm4gYi5idWlsZFF1ZXJ5UGFyYW1zKGErJ1snK2UrJ10nLGNbZV0sZCl9KTpkKGEsYyl9LHRoaXMuZ2V0Um91dGU9ZnVuY3Rpb24oYSl7dmFyIGM9Yi5jb250ZXh0Um91dGluZy5wcmVmaXgrYTtpZighIWIucm91dGVzUm91dGluZ1tjXSlyZXR1cm4gYi5yb3V0ZXNSb3V0aW5nW2NdO2Vsc2UgaWYoIWIucm91dGVzUm91dGluZ1thXSl0aHJvdyBuZXcgRXJyb3IoJ1RoZSByb3V0ZSBcIicrYSsnXCIgZG9lcyBub3QgZXhpc3QuJyk7cmV0dXJuIGIucm91dGVzUm91dGluZ1thXX0sdGhpcy5nZW5lcmF0ZT1mdW5jdGlvbihhLGMsZCl7dmFyIGU9Yi5nZXRSb3V0ZShhKSxmPWN8fHt9LGc9X2V4dGVuZHMoe30sZiksaD0nX3NjaGVtZScsaT0nJyxqPSEwLGs9Jyc7aWYoKGUudG9rZW5zfHxbXSkuZm9yRWFjaChmdW5jdGlvbihiKXtpZigndGV4dCc9PT1iWzBdKXJldHVybiBpPWJbMV0raSx2b2lkKGo9ITEpO2lmKCd2YXJpYWJsZSc9PT1iWzBdKXt2YXIgYz0oZS5kZWZhdWx0c3x8e30pW2JbM11dO2lmKCExPT1qfHwhY3x8KGZ8fHt9KVtiWzNdXSYmZltiWzNdXSE9PWUuZGVmYXVsdHNbYlszXV0pe3ZhciBkO2lmKChmfHx7fSlbYlszXV0pZD1mW2JbM11dLGRlbGV0ZSBnW2JbM11dO2Vsc2UgaWYoYylkPWUuZGVmYXVsdHNbYlszXV07ZWxzZXtpZihqKXJldHVybjt0aHJvdyBuZXcgRXJyb3IoJ1RoZSByb3V0ZSBcIicrYSsnXCIgcmVxdWlyZXMgdGhlIHBhcmFtZXRlciBcIicrYlszXSsnXCIuJyl9dmFyIGg9ITA9PT1kfHwhMT09PWR8fCcnPT09ZDtpZighaHx8IWope3ZhciBrPWVuY29kZVVSSUNvbXBvbmVudChkKS5yZXBsYWNlKC8lMkYvZywnLycpOydudWxsJz09PWsmJm51bGw9PT1kJiYoaz0nJyksaT1iWzFdK2sraX1qPSExfWVsc2UgYyYmZGVsZXRlIGdbYlszXV07cmV0dXJufXRocm93IG5ldyBFcnJvcignVGhlIHRva2VuIHR5cGUgXCInK2JbMF0rJ1wiIGlzIG5vdCBzdXBwb3J0ZWQuJyl9KSwnJz09aSYmKGk9Jy8nKSwoZS5ob3N0dG9rZW5zfHxbXSkuZm9yRWFjaChmdW5jdGlvbihhKXt2YXIgYjtyZXR1cm4ndGV4dCc9PT1hWzBdP3ZvaWQoaz1hWzFdK2spOnZvaWQoJ3ZhcmlhYmxlJz09PWFbMF0mJigoZnx8e30pW2FbM11dPyhiPWZbYVszXV0sZGVsZXRlIGdbYVszXV0pOmUuZGVmYXVsdHNbYVszXV0mJihiPWUuZGVmYXVsdHNbYVszXV0pLGs9YVsxXStiK2spKX0pLGk9Yi5jb250ZXh0Um91dGluZy5iYXNlX3VybCtpLGUucmVxdWlyZW1lbnRzW2hdJiZiLmdldFNjaGVtZSgpIT09ZS5yZXF1aXJlbWVudHNbaF0/aT1lLnJlcXVpcmVtZW50c1toXSsnOi8vJysoa3x8Yi5nZXRIb3N0KCkpK2k6ayYmYi5nZXRIb3N0KCkhPT1rP2k9Yi5nZXRTY2hlbWUoKSsnOi8vJytrK2k6ITA9PT1kJiYoaT1iLmdldFNjaGVtZSgpKyc6Ly8nK2IuZ2V0SG9zdCgpK2kpLDA8T2JqZWN0LmtleXMoZykubGVuZ3RoKXt2YXIgbD1bXSxtPWZ1bmN0aW9uKGEsYil7dmFyIGM9YjtjPSdmdW5jdGlvbic9PXR5cGVvZiBjP2MoKTpjLGM9bnVsbD09PWM/Jyc6YyxsLnB1c2goZW5jb2RlVVJJQ29tcG9uZW50KGEpKyc9JytlbmNvZGVVUklDb21wb25lbnQoYykpfTtPYmplY3Qua2V5cyhnKS5mb3JFYWNoKGZ1bmN0aW9uKGEpe3JldHVybiBiLmJ1aWxkUXVlcnlQYXJhbXMoYSxnW2FdLG0pfSksaT1pKyc/JytsLmpvaW4oJyYnKS5yZXBsYWNlKC8lMjAvZywnKycpfXJldHVybiBpfSx0aGlzLnNldERhdGE9ZnVuY3Rpb24oYSl7Yi5zZXRCYXNlVXJsKGEuYmFzZV91cmwpLGIuc2V0Um91dGVzKGEucm91dGVzKSwncHJlZml4J2luIGEmJmIuc2V0UHJlZml4KGEucHJlZml4KSxiLnNldEhvc3QoYS5ob3N0KSxiLnNldFNjaGVtZShhLnNjaGVtZSl9LHRoaXMuY29udGV4dFJvdXRpbmc9e2Jhc2VfdXJsOicnLHByZWZpeDonJyxob3N0OicnLHNjaGVtZTonJ319O21vZHVsZS5leHBvcnRzPW5ldyBSb3V0aW5nO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9mb3Mtcm91dGluZy9kaXN0L3JvdXRpbmcuanNcbi8vIG1vZHVsZSBpZCA9IDE3OFxuLy8gbW9kdWxlIGNodW5rcyA9IDMgMTAgMTMiLCJtb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgaWYodHlwZW9mIGl0ICE9ICdmdW5jdGlvbicpdGhyb3cgVHlwZUVycm9yKGl0ICsgJyBpcyBub3QgYSBmdW5jdGlvbiEnKTtcbiAgcmV0dXJuIGl0O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2EtZnVuY3Rpb24uanNcbi8vIG1vZHVsZSBpZCA9IDE4XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIm1vZHVsZS5leHBvcnRzID0geyBcImRlZmF1bHRcIjogcmVxdWlyZShcImNvcmUtanMvbGlicmFyeS9mbi9vYmplY3QvZGVmaW5lLXByb3BlcnR5XCIpLCBfX2VzTW9kdWxlOiB0cnVlIH07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9vYmplY3QvZGVmaW5lLXByb3BlcnR5LmpzXG4vLyBtb2R1bGUgaWQgPSAxOVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJtb2R1bGUuZXhwb3J0cyA9IHsgXCJkZWZhdWx0XCI6IHJlcXVpcmUoXCJjb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L3ZhbHVlc1wiKSwgX19lc01vZHVsZTogdHJ1ZSB9O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvb2JqZWN0L3ZhbHVlcy5qc1xuLy8gbW9kdWxlIGlkID0gMTkzXG4vLyBtb2R1bGUgY2h1bmtzID0gMyAxMCIsIi8qKlxuICogMjAwNy0yMDIwIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMjAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBSb3V0ZXIgZnJvbSAnQGNvbXBvbmVudHMvcm91dGVyJztcbmltcG9ydCB7RXZlbnRFbWl0dGVyfSBmcm9tICdAY29tcG9uZW50cy9ldmVudC1lbWl0dGVyJztcbmltcG9ydCBldmVudE1hcCBmcm9tICdAcGFnZXMvb3JkZXIvY3JlYXRlL2V2ZW50LW1hcCc7XG5pbXBvcnQgY3JlYXRlT3JkZXJNYXAgZnJvbSBcIi4vY3JlYXRlLW9yZGVyLW1hcFwiO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogUHJvdmlkZXMgYWpheCBjYWxscyBmb3IgY2FydCBlZGl0aW5nIGFjdGlvbnNcbiAqIEVhY2ggbWV0aG9kIGVtaXRzIGFuIGV2ZW50IHdpdGggdXBkYXRlZCBjYXJ0IGluZm9ybWF0aW9uIGFmdGVyIHN1Y2Nlc3MuXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIENhcnRFZGl0b3Ige1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLnJvdXRlciA9IG5ldyBSb3V0ZXIoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBDaGFuZ2VzIGNhcnQgYWRkcmVzc2VzXG4gICAqXG4gICAqIEBwYXJhbSB7TnVtYmVyfSBjYXJ0SWRcbiAgICogQHBhcmFtIHtPYmplY3R9IGFkZHJlc3Nlc1xuICAgKi9cbiAgY2hhbmdlQ2FydEFkZHJlc3NlcyhjYXJ0SWQsIGFkZHJlc3Nlcykge1xuICAgICQucG9zdCh0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fY2FydHNfZWRpdF9hZGRyZXNzZXMnLCB7Y2FydElkfSksIGFkZHJlc3NlcylcbiAgICAgIC50aGVuKGNhcnRJbmZvID0+IEV2ZW50RW1pdHRlci5lbWl0KGV2ZW50TWFwLmNhcnRBZGRyZXNzZXNDaGFuZ2VkLCBjYXJ0SW5mbykpXG4gICAgICAuY2F0Y2gocmVzcG9uc2UgPT4gc2hvd0Vycm9yTWVzc2FnZShyZXNwb25zZS5yZXNwb25zZUpTT04ubWVzc2FnZSkpO1xuICB9XG5cbiAgLyoqXG4gICAqIE1vZGlmaWVzIGNhcnQgZGVsaXZlcnkgb3B0aW9uXG4gICAqXG4gICAqIEBwYXJhbSB7TnVtYmVyfSBjYXJ0SWRcbiAgICogQHBhcmFtIHtOdW1iZXJ9IHZhbHVlXG4gICAqL1xuICBjaGFuZ2VEZWxpdmVyeU9wdGlvbihjYXJ0SWQsIHZhbHVlKSB7XG4gICAgJC5wb3N0KHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9jYXJ0c19lZGl0X2NhcnJpZXInLCB7Y2FydElkfSksIHtcbiAgICAgIGNhcnJpZXJJZDogdmFsdWUsXG4gICAgfSkudGhlbihjYXJ0SW5mbyA9PiBFdmVudEVtaXR0ZXIuZW1pdChldmVudE1hcC5jYXJ0RGVsaXZlcnlPcHRpb25DaGFuZ2VkLCBjYXJ0SW5mbykpXG4gICAgICAuY2F0Y2gocmVzcG9uc2UgPT4gc2hvd0Vycm9yTWVzc2FnZShyZXNwb25zZS5yZXNwb25zZUpTT04ubWVzc2FnZSkpO1xuICB9XG5cbiAgLyoqXG4gICAqIENoYW5nZXMgY2FydCBmcmVlIHNoaXBwaW5nIHZhbHVlXG4gICAqXG4gICAqIEBwYXJhbSB7TnVtYmVyfSBjYXJ0SWRcbiAgICogQHBhcmFtIHtCb29sZWFufSB2YWx1ZVxuICAgKi9cbiAgc2V0RnJlZVNoaXBwaW5nKGNhcnRJZCwgdmFsdWUpIHtcbiAgICAkLnBvc3QodGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX2NhcnRzX3NldF9mcmVlX3NoaXBwaW5nJywge2NhcnRJZH0pLCB7XG4gICAgICBmcmVlU2hpcHBpbmc6IHZhbHVlLFxuICAgIH0pLnRoZW4oY2FydEluZm8gPT4gRXZlbnRFbWl0dGVyLmVtaXQoZXZlbnRNYXAuY2FydEZyZWVTaGlwcGluZ1NldCwgY2FydEluZm8pKVxuICAgICAgLmNhdGNoKHJlc3BvbnNlID0+IHNob3dFcnJvck1lc3NhZ2UocmVzcG9uc2UucmVzcG9uc2VKU09OLm1lc3NhZ2UpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBBZGRzIGNhcnQgcnVsZSB0byBjYXJ0XG4gICAqXG4gICAqIEBwYXJhbSB7TnVtYmVyfSBjYXJ0UnVsZUlkXG4gICAqIEBwYXJhbSB7TnVtYmVyfSBjYXJ0SWRcbiAgICovXG4gIGFkZENhcnRSdWxlVG9DYXJ0KGNhcnRSdWxlSWQsIGNhcnRJZCkge1xuICAgICQucG9zdCh0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fY2FydHNfYWRkX2NhcnRfcnVsZScsIHtjYXJ0SWR9KSwge1xuICAgICAgY2FydFJ1bGVJZCxcbiAgICB9KS50aGVuKGNhcnRJbmZvID0+IEV2ZW50RW1pdHRlci5lbWl0KGV2ZW50TWFwLmNhcnRSdWxlQWRkZWQsIGNhcnRJbmZvKSlcbiAgICAgIC5jYXRjaChyZXNwb25zZSA9PiBFdmVudEVtaXR0ZXIuZW1pdChldmVudE1hcC5jYXJ0UnVsZUZhaWxlZFRvQWRkLCByZXNwb25zZS5yZXNwb25zZUpTT04ubWVzc2FnZSkpO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlbW92ZXMgY2FydCBydWxlIGZyb20gY2FydFxuICAgKlxuICAgKiBAcGFyYW0ge051bWJlcn0gY2FydFJ1bGVJZFxuICAgKiBAcGFyYW0ge051bWJlcn0gY2FydElkXG4gICAqL1xuICByZW1vdmVDYXJ0UnVsZUZyb21DYXJ0KGNhcnRSdWxlSWQsIGNhcnRJZCkge1xuICAgICQucG9zdCh0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fY2FydHNfZGVsZXRlX2NhcnRfcnVsZScsIHtcbiAgICAgIGNhcnRJZCxcbiAgICAgIGNhcnRSdWxlSWQsXG4gICAgfSkpLnRoZW4oY2FydEluZm8gPT4gRXZlbnRFbWl0dGVyLmVtaXQoZXZlbnRNYXAuY2FydFJ1bGVSZW1vdmVkLCBjYXJ0SW5mbykpXG4gICAgICAuY2F0Y2gocmVzcG9uc2UgPT4gc2hvd0Vycm9yTWVzc2FnZShyZXNwb25zZS5yZXNwb25zZUpTT04ubWVzc2FnZSkpO1xuICB9XG5cbiAgLyoqXG4gICAqIEFkZHMgcHJvZHVjdCB0byBjYXJ0XG4gICAqXG4gICAqIEBwYXJhbSB7TnVtYmVyfSBjYXJ0SWRcbiAgICogQHBhcmFtIHtPYmplY3R9IGRhdGFcbiAgICovXG4gIGFkZFByb2R1Y3QoY2FydElkLCBkYXRhKSB7XG4gICAgbGV0IGZpbGVTaXplSGVhZGVyID0gJyc7XG4gICAgaWYgKCEkLmlzRW1wdHlPYmplY3QoZGF0YS5maWxlU2l6ZXMpKSB7XG4gICAgICBmaWxlU2l6ZUhlYWRlciA9IEpTT04uc3RyaW5naWZ5KGRhdGEuZmlsZVNpemVzKTtcbiAgICB9XG5cbiAgICAkLmFqYXgodGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX2NhcnRzX2FkZF9wcm9kdWN0Jywge2NhcnRJZH0pLCB7XG4gICAgICBoZWFkZXJzOiB7XG4gICAgICAgIC8vIEFkZHMgY3VzdG9tIGhlYWRlcnMgd2l0aCBzdWJtaXR0ZWQgZmlsZSBzaXplcywgdG8gdHJhY2sgaWYgYWxsIGZpbGVzIHJlYWNoZWQgc2VydmVyIHNpZGUuXG4gICAgICAgICdmaWxlLXNpemVzJzogZmlsZVNpemVIZWFkZXIsXG4gICAgICB9LFxuICAgICAgbWV0aG9kOiAnUE9TVCcsXG4gICAgICBkYXRhOiBkYXRhLnByb2R1Y3QsXG4gICAgICBwcm9jZXNzRGF0YTogZmFsc2UsXG4gICAgICBjb250ZW50VHlwZTogZmFsc2UsXG4gICAgfSkudGhlbihjYXJ0SW5mbyA9PiBFdmVudEVtaXR0ZXIuZW1pdChldmVudE1hcC5wcm9kdWN0QWRkZWRUb0NhcnQsIGNhcnRJbmZvKSlcbiAgICAgIC5jYXRjaChyZXNwb25zZSA9PiBFdmVudEVtaXR0ZXIuZW1pdChldmVudE1hcC5wcm9kdWN0QWRkVG9DYXJ0RmFpbGVkLCByZXNwb25zZS5yZXNwb25zZUpTT04ubWVzc2FnZSkpO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlbW92ZXMgcHJvZHVjdCBmcm9tIGNhcnRcbiAgICpcbiAgICogQHBhcmFtIHtOdW1iZXJ9IGNhcnRJZFxuICAgKiBAcGFyYW0ge09iamVjdH0gcHJvZHVjdFxuICAgKi9cbiAgcmVtb3ZlUHJvZHVjdEZyb21DYXJ0KGNhcnRJZCwgcHJvZHVjdCkge1xuICAgICQucG9zdCh0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fY2FydHNfZGVsZXRlX3Byb2R1Y3QnLCB7Y2FydElkfSksIHtcbiAgICAgIHByb2R1Y3RJZDogcHJvZHVjdC5wcm9kdWN0SWQsXG4gICAgICBhdHRyaWJ1dGVJZDogcHJvZHVjdC5hdHRyaWJ1dGVJZCxcbiAgICAgIGN1c3RvbWl6YXRpb25JZDogcHJvZHVjdC5jdXN0b21pemF0aW9uSWQsXG4gICAgfSkudGhlbihjYXJ0SW5mbyA9PiBFdmVudEVtaXR0ZXIuZW1pdChldmVudE1hcC5wcm9kdWN0UmVtb3ZlZEZyb21DYXJ0LCBjYXJ0SW5mbykpXG4gICAgICAuY2F0Y2gocmVzcG9uc2UgPT4gc2hvd0Vycm9yTWVzc2FnZShyZXNwb25zZS5yZXNwb25zZUpTT04ubWVzc2FnZSkpO1xuICB9XG5cbiAgLyoqXG4gICAqIENoYW5nZXMgcHJvZHVjdCBwcmljZSBpbiBjYXJ0XG4gICAqXG4gICAqIEBwYXJhbSB7TnVtYmVyfSBjYXJ0SWRcbiAgICogQHBhcmFtIHtOdW1iZXJ9IGN1c3RvbWVySWRcbiAgICogQHBhcmFtIHtPYmplY3R9IHByb2R1Y3QgdGhlIHVwZGF0ZWQgcHJvZHVjdFxuICAgKi9cbiAgY2hhbmdlUHJvZHVjdFByaWNlKGNhcnRJZCwgY3VzdG9tZXJJZCwgcHJvZHVjdCkge1xuICAgICQucG9zdCh0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fY2FydHNfZWRpdF9wcm9kdWN0X3ByaWNlJywge1xuICAgICAgY2FydElkLFxuICAgICAgcHJvZHVjdElkOiBwcm9kdWN0LnByb2R1Y3RJZCxcbiAgICAgIHByb2R1Y3RBdHRyaWJ1dGVJZDogcHJvZHVjdC5hdHRyaWJ1dGVJZCxcbiAgICB9KSwge1xuICAgICAgbmV3UHJpY2U6IHByb2R1Y3QucHJpY2UsXG4gICAgICBjdXN0b21lcklkLFxuICAgIH0pLnRoZW4oY2FydEluZm8gPT4gRXZlbnRFbWl0dGVyLmVtaXQoZXZlbnRNYXAucHJvZHVjdFByaWNlQ2hhbmdlZCwgY2FydEluZm8pKVxuICAgICAgLmNhdGNoKHJlc3BvbnNlID0+IHNob3dFcnJvck1lc3NhZ2UocmVzcG9uc2UucmVzcG9uc2VKU09OLm1lc3NhZ2UpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBVcGRhdGVzIHByb2R1Y3QgcXVhbnRpdHkgaW4gY2FydFxuICAgKlxuICAgKiBAcGFyYW0gY2FydElkXG4gICAqIEBwYXJhbSBwcm9kdWN0XG4gICAqL1xuICBjaGFuZ2VQcm9kdWN0UXR5KGNhcnRJZCwgcHJvZHVjdCkge1xuICAgICQucG9zdCh0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fY2FydHNfZWRpdF9wcm9kdWN0X3F1YW50aXR5Jywge1xuICAgICAgY2FydElkLFxuICAgICAgcHJvZHVjdElkOiBwcm9kdWN0LnByb2R1Y3RJZFxuICAgIH0pLCB7XG4gICAgICBuZXdRdHk6IHByb2R1Y3QubmV3UXR5LFxuICAgICAgYXR0cmlidXRlSWQ6IHByb2R1Y3QuYXR0cmlidXRlSWQsXG4gICAgICBjdXN0b21pemF0aW9uSWQ6IHByb2R1Y3QuY3VzdG9taXphdGlvbklkLFxuICAgIH0pLnRoZW4oY2FydEluZm8gPT4gRXZlbnRFbWl0dGVyLmVtaXQoZXZlbnRNYXAucHJvZHVjdFF0eUNoYW5nZWQsIGNhcnRJbmZvKSlcbiAgICAgIC5jYXRjaChyZXNwb25zZSA9PiBFdmVudEVtaXR0ZXIuZW1pdChldmVudE1hcC5wcm9kdWN0UXR5Q2hhbmdlRmFpbGVkLCByZXNwb25zZSkpO1xuICB9XG5cbiAgLyoqXG4gICAqIENoYW5nZXMgY2FydCBjdXJyZW5jeVxuICAgKlxuICAgKiBAcGFyYW0ge051bWJlcn0gY2FydElkXG4gICAqIEBwYXJhbSB7TnVtYmVyfSBjdXJyZW5jeUlkXG4gICAqL1xuICBjaGFuZ2VDYXJ0Q3VycmVuY3koY2FydElkLCBjdXJyZW5jeUlkKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5jYXJ0Q3VycmVuY3lTZWxlY3QpLmRhdGEoJ3NlbGVjdGVkQ3VycmVuY3lJZCcsIGN1cnJlbmN5SWQpO1xuXG4gICAgJC5wb3N0KHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9jYXJ0c19lZGl0X2N1cnJlbmN5Jywge2NhcnRJZH0pLCB7XG4gICAgICBjdXJyZW5jeUlkLFxuICAgIH0pLnRoZW4oY2FydEluZm8gPT4gRXZlbnRFbWl0dGVyLmVtaXQoZXZlbnRNYXAuY2FydEN1cnJlbmN5Q2hhbmdlZCwgY2FydEluZm8pKVxuICAgICAgLmNhdGNoKHJlc3BvbnNlID0+IEV2ZW50RW1pdHRlci5lbWl0KGV2ZW50TWFwLmNhcnRDdXJyZW5jeUNoYW5nZUZhaWxlZCwgcmVzcG9uc2UpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBDaGFuZ2VzIGNhcnQgbGFuZ3VhZ2VcbiAgICpcbiAgICogQHBhcmFtIHtOdW1iZXJ9IGNhcnRJZFxuICAgKiBAcGFyYW0ge051bWJlcn0gbGFuZ3VhZ2VJZFxuICAgKi9cbiAgY2hhbmdlQ2FydExhbmd1YWdlKGNhcnRJZCwgbGFuZ3VhZ2VJZCkge1xuICAgICQucG9zdCh0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fY2FydHNfZWRpdF9sYW5ndWFnZScsIHtjYXJ0SWR9KSwge1xuICAgICAgbGFuZ3VhZ2VJZCxcbiAgICB9KS50aGVuKGNhcnRJbmZvID0+IEV2ZW50RW1pdHRlci5lbWl0KGV2ZW50TWFwLmNhcnRMYW5ndWFnZUNoYW5nZWQsIGNhcnRJbmZvKSlcbiAgICAgIC5jYXRjaChyZXNwb25zZSA9PiBzaG93RXJyb3JNZXNzYWdlKHJlc3BvbnNlLnJlc3BvbnNlSlNPTi5tZXNzYWdlKSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9jYXJ0LWVkaXRvci5qcyIsIi8qKlxuICogMjAwNy0yMDIwIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMjAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBjcmVhdGVPcmRlck1hcCBmcm9tICcuL2NyZWF0ZS1vcmRlci1tYXAnO1xuaW1wb3J0IENyZWF0ZU9yZGVyUGFnZSBmcm9tIFwiLi9jcmVhdGUtb3JkZXItcGFnZVwiO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogUmVzcG9uc2libGUgZm9yIHN1bW1hcnkgYmxvY2sgcmVuZGVyaW5nXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFN1bW1hcnlSZW5kZXJlciB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHRoaXMuJHRvdGFsUHJvZHVjdHMgPSAkKGNyZWF0ZU9yZGVyTWFwLnN1bW1hcnlUb3RhbFByb2R1Y3RzKTtcbiAgICB0aGlzLiR0b3RhbERpc2NvdW50ID0gJChjcmVhdGVPcmRlck1hcC5zdW1tYXJ5VG90YWxEaXNjb3VudCk7XG4gICAgdGhpcy4kdG90YWxTaGlwcGluZyA9ICQoY3JlYXRlT3JkZXJNYXAudG90YWxTaGlwcGluZ0ZpZWxkKTtcbiAgICB0aGlzLiR0b3RhbFRheGVzID0gJChjcmVhdGVPcmRlck1hcC5zdW1tYXJ5VG90YWxUYXhlcyk7XG4gICAgdGhpcy4kdG90YWxXaXRob3V0VGF4ID0gJChjcmVhdGVPcmRlck1hcC5zdW1tYXJ5VG90YWxXaXRob3V0VGF4KTtcbiAgICB0aGlzLiR0b3RhbFdpdGhUYXggPSAkKGNyZWF0ZU9yZGVyTWFwLnN1bW1hcnlUb3RhbFdpdGhUYXgpO1xuICAgIHRoaXMuJHBsYWNlT3JkZXJDYXJ0SWRGaWVsZCA9ICQoY3JlYXRlT3JkZXJNYXAucGxhY2VPcmRlckNhcnRJZEZpZWxkKTtcbiAgICB0aGlzLiRvcmRlck1lc3NhZ2VGaWVsZCA9ICQoY3JlYXRlT3JkZXJNYXAub3JkZXJNZXNzYWdlRmllbGQpO1xuICAgIHRoaXMuJHByb2Nlc3NPcmRlckxpbmsgPSAkKGNyZWF0ZU9yZGVyTWFwLnByb2Nlc3NPcmRlckxpbmtUYWcpO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlbmRlcnMgc3VtbWFyeSBibG9ja1xuICAgKlxuICAgKiBAcGFyYW0ge09iamVjdH0gY2FydEluZm9cbiAgICovXG4gIHJlbmRlcihjYXJ0SW5mbykge1xuICAgIHRoaXMuX2NsZWFuU3VtbWFyeSgpO1xuICAgIGNvbnN0IG5vUHJvZHVjdHMgPSBjYXJ0SW5mby5wcm9kdWN0cy5sZW5ndGggPT09IDA7XG4gICAgY29uc3Qgbm9TaGlwcGluZ09wdGlvbnMgPSBjYXJ0SW5mby5zaGlwcGluZyA9PT0gbnVsbDtcbiAgICBjb25zdCBhZGRyZXNzZXNBcmVWYWxpZCA9IENyZWF0ZU9yZGVyUGFnZS52YWxpZGF0ZVNlbGVjdGVkQWRkcmVzc2VzKGNhcnRJbmZvLmFkZHJlc3Nlcyk7XG5cbiAgICBpZiAobm9Qcm9kdWN0cyB8fCBub1NoaXBwaW5nT3B0aW9ucyB8fCAhYWRkcmVzc2VzQXJlVmFsaWQpIHtcbiAgICAgIHRoaXMuX2hpZGVTdW1tYXJ5QmxvY2soKTtcblxuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICBjb25zdCBjYXJ0U3VtbWFyeSA9IGNhcnRJbmZvLnN1bW1hcnk7XG5cbiAgICB0aGlzLiR0b3RhbFByb2R1Y3RzLnRleHQoY2FydFN1bW1hcnkudG90YWxQcm9kdWN0c1ByaWNlKTtcbiAgICB0aGlzLiR0b3RhbERpc2NvdW50LnRleHQoY2FydFN1bW1hcnkudG90YWxEaXNjb3VudCk7XG4gICAgdGhpcy4kdG90YWxTaGlwcGluZy50ZXh0KGNhcnRTdW1tYXJ5LnRvdGFsU2hpcHBpbmdQcmljZSk7XG4gICAgdGhpcy4kdG90YWxUYXhlcy50ZXh0KGNhcnRTdW1tYXJ5LnRvdGFsVGF4ZXMpO1xuICAgIHRoaXMuJHRvdGFsV2l0aG91dFRheC50ZXh0KGNhcnRTdW1tYXJ5LnRvdGFsUHJpY2VXaXRob3V0VGF4ZXMpO1xuICAgIHRoaXMuJHRvdGFsV2l0aFRheC50ZXh0KGNhcnRTdW1tYXJ5LnRvdGFsUHJpY2VXaXRoVGF4ZXMpO1xuICAgIHRoaXMuJHByb2Nlc3NPcmRlckxpbmsucHJvcCgnaHJlZicsIGNhcnRTdW1tYXJ5LnByb2Nlc3NPcmRlckxpbmspO1xuICAgIHRoaXMuJG9yZGVyTWVzc2FnZUZpZWxkLnRleHQoY2FydFN1bW1hcnkub3JkZXJNZXNzYWdlKTtcbiAgICB0aGlzLiRwbGFjZU9yZGVyQ2FydElkRmllbGQudmFsKGNhcnRJbmZvLmNhcnRJZCk7XG5cbiAgICB0aGlzLl9zaG93U3VtbWFyeUJsb2NrKCk7XG4gIH1cblxuICAvKipcbiAgICogUmVuZGVycyBzdW1tYXJ5IHN1Y2Nlc3MgbWVzc2FnZVxuICAgKlxuICAgKiBAcGFyYW0gbWVzc2FnZVxuICAgKi9cbiAgcmVuZGVyU3VjY2Vzc01lc3NhZ2UobWVzc2FnZSkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAuc3VtbWFyeVN1Y2Nlc3NBbGVydFRleHQpLnRleHQobWVzc2FnZSk7XG4gICAgdGhpcy5fc2hvd1N1bW1hcnlTdWNjZXNzQWxlcnRCbG9jaygpO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlbmRlcnMgc3VtbWFyeSBlcnJvciBtZXNzYWdlXG4gICAqXG4gICAqIEBwYXJhbSBtZXNzYWdlXG4gICAqL1xuICByZW5kZXJFcnJvck1lc3NhZ2UobWVzc2FnZSkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAuc3VtbWFyeUVycm9yQWxlcnRUZXh0KS50ZXh0KG1lc3NhZ2UpO1xuICAgIHRoaXMuX3Nob3dTdW1tYXJ5RXJyb3JBbGVydEJsb2NrKCk7XG4gIH1cblxuICAvKipcbiAgICogQ2xlYW5zIGNvbnRlbnQgb2Ygc3VjY2Vzcy9lcnJvciBzdW1tYXJ5IGFsZXJ0cyBhbmQgaGlkZXMgdGhlbVxuICAgKi9cbiAgY2xlYW5BbGVydHMoKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5zdW1tYXJ5U3VjY2Vzc0FsZXJ0VGV4dCkudGV4dCgnJyk7XG4gICAgJChjcmVhdGVPcmRlck1hcC5zdW1tYXJ5RXJyb3JBbGVydFRleHQpLnRleHQoJycpO1xuICAgIHRoaXMuX2hpZGVTdW1tYXJ5U3VjY2Vzc0FsZXJ0QmxvY2soKTtcbiAgICB0aGlzLl9oaWRlU3VtbWFyeUVycm9yQWxlcnRCbG9jaygpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNob3dzIHN1bW1hcnkgYmxvY2tcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zaG93U3VtbWFyeUJsb2NrKCkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAuc3VtbWFyeUJsb2NrKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZXMgc3VtbWFyeSBibG9ja1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hpZGVTdW1tYXJ5QmxvY2soKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5zdW1tYXJ5QmxvY2spLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93cyBlcnJvciBhbGVydCBvZiBzdW1tYXJ5IGJsb2NrXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2hvd1N1bW1hcnlFcnJvckFsZXJ0QmxvY2soKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5zdW1tYXJ5RXJyb3JBbGVydEJsb2NrKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZXMgZXJyb3IgYWxlcnQgb2Ygc3VtbWFyeSBibG9ja1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hpZGVTdW1tYXJ5RXJyb3JBbGVydEJsb2NrKCkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAuc3VtbWFyeUVycm9yQWxlcnRCbG9jaykuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNob3dzIHN1Y2Nlc3MgYWxlcnQgb2Ygc3VtbWFyeSBibG9ja1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3Nob3dTdW1tYXJ5U3VjY2Vzc0FsZXJ0QmxvY2soKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5zdW1tYXJ5U3VjY2Vzc0FsZXJ0QmxvY2spLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIaWRlcyBzdWNjZXNzIGFsZXJ0IG9mIHN1bW1hcnkgYmxvY2tcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oaWRlU3VtbWFyeVN1Y2Nlc3NBbGVydEJsb2NrKCkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAuc3VtbWFyeVN1Y2Nlc3NBbGVydEJsb2NrKS5hZGRDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogRW1wdGllcyBjYXJ0IHN1bW1hcnkgZmllbGRzXG4gICAqL1xuICBfY2xlYW5TdW1tYXJ5KCkge1xuICAgIHRoaXMuJHRvdGFsUHJvZHVjdHMuZW1wdHkoKTtcbiAgICB0aGlzLiR0b3RhbERpc2NvdW50LmVtcHR5KCk7XG4gICAgdGhpcy4kdG90YWxTaGlwcGluZy5lbXB0eSgpO1xuICAgIHRoaXMuJHRvdGFsVGF4ZXMuZW1wdHkoKTtcbiAgICB0aGlzLiR0b3RhbFdpdGhvdXRUYXguZW1wdHkoKTtcbiAgICB0aGlzLiR0b3RhbFdpdGhUYXguZW1wdHkoKTtcbiAgICB0aGlzLiRwcm9jZXNzT3JkZXJMaW5rLnByb3AoJ2hyZWYnLCAnJyk7XG4gICAgdGhpcy4kb3JkZXJNZXNzYWdlRmllbGQudGV4dCgnJyk7XG4gICAgdGhpcy5jbGVhbkFsZXJ0cygpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvc3VtbWFyeS1yZW5kZXJlci5qcyIsIi8vIFRoYW5rJ3MgSUU4IGZvciBoaXMgZnVubnkgZGVmaW5lUHJvcGVydHlcbm1vZHVsZS5leHBvcnRzID0gIXJlcXVpcmUoJy4vX2ZhaWxzJykoZnVuY3Rpb24oKXtcbiAgcmV0dXJuIE9iamVjdC5kZWZpbmVQcm9wZXJ0eSh7fSwgJ2EnLCB7Z2V0OiBmdW5jdGlvbigpeyByZXR1cm4gNzsgfX0pLmEgIT0gNztcbn0pO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZGVzY3JpcHRvcnMuanNcbi8vIG1vZHVsZSBpZCA9IDJcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwicmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9lczYub2JqZWN0LmRlZmluZS1wcm9wZXJ0eScpO1xudmFyICRPYmplY3QgPSByZXF1aXJlKCcuLi8uLi9tb2R1bGVzL19jb3JlJykuT2JqZWN0O1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbiBkZWZpbmVQcm9wZXJ0eShpdCwga2V5LCBkZXNjKXtcbiAgcmV0dXJuICRPYmplY3QuZGVmaW5lUHJvcGVydHkoaXQsIGtleSwgZGVzYyk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2RlZmluZS1wcm9wZXJ0eS5qc1xuLy8gbW9kdWxlIGlkID0gMjBcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwicmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9lczcub2JqZWN0LnZhbHVlcycpO1xubW9kdWxlLmV4cG9ydHMgPSByZXF1aXJlKCcuLi8uLi9tb2R1bGVzL19jb3JlJykuT2JqZWN0LnZhbHVlcztcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC92YWx1ZXMuanNcbi8vIG1vZHVsZSBpZCA9IDIwNVxuLy8gbW9kdWxlIGNodW5rcyA9IDMgMTAiLCJ2YXIgZ2V0S2V5cyAgID0gcmVxdWlyZSgnLi9fb2JqZWN0LWtleXMnKVxuICAsIHRvSU9iamVjdCA9IHJlcXVpcmUoJy4vX3RvLWlvYmplY3QnKVxuICAsIGlzRW51bSAgICA9IHJlcXVpcmUoJy4vX29iamVjdC1waWUnKS5mO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpc0VudHJpZXMpe1xuICByZXR1cm4gZnVuY3Rpb24oaXQpe1xuICAgIHZhciBPICAgICAgPSB0b0lPYmplY3QoaXQpXG4gICAgICAsIGtleXMgICA9IGdldEtleXMoTylcbiAgICAgICwgbGVuZ3RoID0ga2V5cy5sZW5ndGhcbiAgICAgICwgaSAgICAgID0gMFxuICAgICAgLCByZXN1bHQgPSBbXVxuICAgICAgLCBrZXk7XG4gICAgd2hpbGUobGVuZ3RoID4gaSlpZihpc0VudW0uY2FsbChPLCBrZXkgPSBrZXlzW2krK10pKXtcbiAgICAgIHJlc3VsdC5wdXNoKGlzRW50cmllcyA/IFtrZXksIE9ba2V5XV0gOiBPW2tleV0pO1xuICAgIH0gcmV0dXJuIHJlc3VsdDtcbiAgfTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtdG8tYXJyYXkuanNcbi8vIG1vZHVsZSBpZCA9IDIwNlxuLy8gbW9kdWxlIGNodW5rcyA9IDMgMTAiLCIvLyBodHRwczovL2dpdGh1Yi5jb20vdGMzOS9wcm9wb3NhbC1vYmplY3QtdmFsdWVzLWVudHJpZXNcbnZhciAkZXhwb3J0ID0gcmVxdWlyZSgnLi9fZXhwb3J0JylcbiAgLCAkdmFsdWVzID0gcmVxdWlyZSgnLi9fb2JqZWN0LXRvLWFycmF5JykoZmFsc2UpO1xuXG4kZXhwb3J0KCRleHBvcnQuUywgJ09iamVjdCcsIHtcbiAgdmFsdWVzOiBmdW5jdGlvbiB2YWx1ZXMoaXQpe1xuICAgIHJldHVybiAkdmFsdWVzKGl0KTtcbiAgfVxufSk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNy5vYmplY3QudmFsdWVzLmpzXG4vLyBtb2R1bGUgaWQgPSAyMDdcbi8vIG1vZHVsZSBjaHVua3MgPSAzIDEwIiwidmFyICRleHBvcnQgPSByZXF1aXJlKCcuL19leHBvcnQnKTtcbi8vIDE5LjEuMi40IC8gMTUuMi4zLjYgT2JqZWN0LmRlZmluZVByb3BlcnR5KE8sIFAsIEF0dHJpYnV0ZXMpXG4kZXhwb3J0KCRleHBvcnQuUyArICRleHBvcnQuRiAqICFyZXF1aXJlKCcuL19kZXNjcmlwdG9ycycpLCAnT2JqZWN0Jywge2RlZmluZVByb3BlcnR5OiByZXF1aXJlKCcuL19vYmplY3QtZHAnKS5mfSk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5vYmplY3QuZGVmaW5lLXByb3BlcnR5LmpzXG4vLyBtb2R1bGUgaWQgPSAyMVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCIvLyB0byBpbmRleGVkIG9iamVjdCwgdG9PYmplY3Qgd2l0aCBmYWxsYmFjayBmb3Igbm9uLWFycmF5LWxpa2UgRVMzIHN0cmluZ3NcbnZhciBJT2JqZWN0ID0gcmVxdWlyZSgnLi9faW9iamVjdCcpXG4gICwgZGVmaW5lZCA9IHJlcXVpcmUoJy4vX2RlZmluZWQnKTtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXQpe1xuICByZXR1cm4gSU9iamVjdChkZWZpbmVkKGl0KSk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fdG8taW9iamVjdC5qc1xuLy8gbW9kdWxlIGlkID0gMjJcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwiLyoqXG4gKiAyMDA3LTIwMjAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAyMCBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IGNyZWF0ZU9yZGVyTWFwIGZyb20gJy4vY3JlYXRlLW9yZGVyLW1hcCc7XG5pbXBvcnQgQ3VzdG9tZXJNYW5hZ2VyIGZyb20gJy4vY3VzdG9tZXItbWFuYWdlcic7XG5pbXBvcnQgU2hpcHBpbmdSZW5kZXJlciBmcm9tICcuL3NoaXBwaW5nLXJlbmRlcmVyJztcbmltcG9ydCBDYXJ0UHJvdmlkZXIgZnJvbSAnLi9jYXJ0LXByb3ZpZGVyJztcbmltcG9ydCBBZGRyZXNzZXNSZW5kZXJlciBmcm9tICcuL2FkZHJlc3Nlcy1yZW5kZXJlcic7XG5pbXBvcnQgQ2FydFJ1bGVzUmVuZGVyZXIgZnJvbSAnLi9jYXJ0LXJ1bGVzLXJlbmRlcmVyJztcbmltcG9ydCBSb3V0ZXIgZnJvbSAnLi4vLi4vLi4vY29tcG9uZW50cy9yb3V0ZXInO1xuaW1wb3J0IHtFdmVudEVtaXR0ZXJ9IGZyb20gJy4uLy4uLy4uL2NvbXBvbmVudHMvZXZlbnQtZW1pdHRlcic7XG5pbXBvcnQgQ2FydEVkaXRvciBmcm9tICcuL2NhcnQtZWRpdG9yJztcbmltcG9ydCBldmVudE1hcCBmcm9tICcuL2V2ZW50LW1hcCc7XG5pbXBvcnQgQ2FydFJ1bGVNYW5hZ2VyIGZyb20gJy4vY2FydC1ydWxlLW1hbmFnZXInO1xuaW1wb3J0IFByb2R1Y3RNYW5hZ2VyIGZyb20gJy4vcHJvZHVjdC1tYW5hZ2VyJztcbmltcG9ydCBQcm9kdWN0UmVuZGVyZXIgZnJvbSAnLi9wcm9kdWN0LXJlbmRlcmVyJztcbmltcG9ydCBTdW1tYXJ5UmVuZGVyZXIgZnJvbSAnLi9zdW1tYXJ5LXJlbmRlcmVyJztcbmltcG9ydCBTdW1tYXJ5TWFuYWdlciBmcm9tICcuL3N1bW1hcnktbWFuYWdlcic7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBQYWdlIE9iamVjdCBmb3IgXCJDcmVhdGUgb3JkZXJcIiBwYWdlXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIENyZWF0ZU9yZGVyUGFnZSB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHRoaXMuY2FydElkID0gbnVsbDtcbiAgICB0aGlzLmN1c3RvbWVySWQgPSBudWxsO1xuICAgIHRoaXMuJGNvbnRhaW5lciA9ICQoY3JlYXRlT3JkZXJNYXAub3JkZXJDcmVhdGlvbkNvbnRhaW5lcik7XG5cbiAgICB0aGlzLmNhcnRQcm92aWRlciA9IG5ldyBDYXJ0UHJvdmlkZXIoKTtcbiAgICB0aGlzLmN1c3RvbWVyTWFuYWdlciA9IG5ldyBDdXN0b21lck1hbmFnZXIoKTtcbiAgICB0aGlzLnNoaXBwaW5nUmVuZGVyZXIgPSBuZXcgU2hpcHBpbmdSZW5kZXJlcigpO1xuICAgIHRoaXMuYWRkcmVzc2VzUmVuZGVyZXIgPSBuZXcgQWRkcmVzc2VzUmVuZGVyZXIoKTtcbiAgICB0aGlzLmNhcnRSdWxlc1JlbmRlcmVyID0gbmV3IENhcnRSdWxlc1JlbmRlcmVyKCk7XG4gICAgdGhpcy5yb3V0ZXIgPSBuZXcgUm91dGVyKCk7XG4gICAgdGhpcy5jYXJ0RWRpdG9yID0gbmV3IENhcnRFZGl0b3IoKTtcbiAgICB0aGlzLmNhcnRSdWxlTWFuYWdlciA9IG5ldyBDYXJ0UnVsZU1hbmFnZXIoKTtcbiAgICB0aGlzLnByb2R1Y3RNYW5hZ2VyID0gbmV3IFByb2R1Y3RNYW5hZ2VyKCk7XG4gICAgdGhpcy5wcm9kdWN0UmVuZGVyZXIgPSBuZXcgUHJvZHVjdFJlbmRlcmVyKCk7XG4gICAgdGhpcy5zdW1tYXJ5UmVuZGVyZXIgPSBuZXcgU3VtbWFyeVJlbmRlcmVyKCk7XG4gICAgdGhpcy5zdW1tYXJ5TWFuYWdlciA9IG5ldyBTdW1tYXJ5TWFuYWdlcigpO1xuXG4gICAgdGhpcy5faW5pdExpc3RlbmVycygpO1xuICAgIHRoaXMuX2xvYWRDYXJ0RnJvbVVybFBhcmFtcygpO1xuXG4gICAgcmV0dXJuIHtcbiAgICAgIHJlZnJlc2hBZGRyZXNzZXNMaXN0OiAocmVmcmVzaENhcnRBZGRyZXNzZXMpID0+IHRoaXMucmVmcmVzaEFkZHJlc3Nlc0xpc3QocmVmcmVzaENhcnRBZGRyZXNzZXMpLFxuICAgICAgc2VhcmNoOiAoc3RyaW5nKSA9PiB0aGlzLmN1c3RvbWVyTWFuYWdlci5zZWFyY2goc3RyaW5nKSxcbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqIENoZWNrcyBpZiBjb3JyZWN0IGFkZHJlc3NlcyBhcmUgc2VsZWN0ZWQuXG4gICAqIFRoZXJlIGlzIGEgY2FzZSB3aGVuIG9wdGlvbnMgbGlzdCBjYW5ub3QgY29udGFpbiBjYXJ0IGFkZHJlc3NlcyAnc2VsZWN0ZWQnIHZhbHVlc1xuICAgKiAgYmVjYXVzZSB0aG9zZSBhcmUgb3V0ZGF0ZWQgaW4gZGIgKGUuZy4gZGVsZXRlZCBhZnRlciBjYXJ0IGNyZWF0aW9uIG9yIGNvdW50cnkgaXMgZGlzYWJsZWQpXG4gICAqXG4gICAqIEBwYXJhbSB7QXJyYXl9IGFkZHJlc3Nlc1xuICAgKlxuICAgKiBAcmV0dXJucyB7Ym9vbGVhbn1cbiAgICovXG4gIHN0YXRpYyB2YWxpZGF0ZVNlbGVjdGVkQWRkcmVzc2VzKGFkZHJlc3Nlcykge1xuICAgIGxldCBkZWxpdmVyeVZhbGlkID0gZmFsc2U7XG4gICAgbGV0IGludm9pY2VWYWxpZCA9IGZhbHNlO1xuXG4gICAgZm9yIChjb25zdCBrZXkgaW4gYWRkcmVzc2VzKSB7XG4gICAgICBjb25zdCBhZGRyZXNzID0gYWRkcmVzc2VzW2tleV07XG5cbiAgICAgIGlmIChhZGRyZXNzLmRlbGl2ZXJ5KSB7XG4gICAgICAgIGRlbGl2ZXJ5VmFsaWQgPSB0cnVlO1xuICAgICAgfVxuXG4gICAgICBpZiAoYWRkcmVzcy5pbnZvaWNlKSB7XG4gICAgICAgIGludm9pY2VWYWxpZCA9IHRydWU7XG4gICAgICB9XG5cbiAgICAgIGlmIChkZWxpdmVyeVZhbGlkICYmIGludm9pY2VWYWxpZCkge1xuICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgIH1cbiAgICB9XG5cbiAgICByZXR1cm4gZmFsc2U7XG4gIH1cblxuICAvKipcbiAgICogSGlkZXMgd2hvbGUgY2FydCBpbmZvcm1hdGlvbiB3cmFwcGVyXG4gICAqL1xuICBoaWRlQ2FydEluZm8oKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5jYXJ0SW5mb1dyYXBwZXIpLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93cyB3aG9sZSBjYXJ0IGluZm9ybWF0aW9uIHdyYXBwZXJcbiAgICovXG4gIHNob3dDYXJ0SW5mbygpIHtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLmNhcnRJbmZvV3JhcHBlcikucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIExvYWRzIGNhcnQgaWYgcXVlcnkgcGFyYW1zIGNvbnRhaW5zIHZhbGlkIGNhcnRJZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2xvYWRDYXJ0RnJvbVVybFBhcmFtcygpIHtcbiAgICBjb25zdCB1cmxQYXJhbXMgPSBuZXcgVVJMU2VhcmNoUGFyYW1zKHdpbmRvdy5sb2NhdGlvbi5zZWFyY2gpO1xuICAgIGNvbnN0IGNhcnRJZCA9IE51bWJlcih1cmxQYXJhbXMuZ2V0KCdjYXJ0SWQnKSk7XG5cbiAgICBpZiAoIWlzTmFOKGNhcnRJZCkgJiYgY2FydElkICE9PSAwKSB7XG4gICAgICB0aGlzLmNhcnRQcm92aWRlci5nZXRDYXJ0KGNhcnRJZCk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIEluaXRpYWxpemVzIGV2ZW50IGxpc3RlbmVyc1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2luaXRMaXN0ZW5lcnMoKSB7XG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdpbnB1dCcsIGNyZWF0ZU9yZGVyTWFwLmN1c3RvbWVyU2VhcmNoSW5wdXQsIGUgPT4gdGhpcy5faW5pdEN1c3RvbWVyU2VhcmNoKGUpKTtcbiAgICB0aGlzLiRjb250YWluZXIub24oJ2NsaWNrJywgY3JlYXRlT3JkZXJNYXAuY2hvb3NlQ3VzdG9tZXJCdG4sIGUgPT4gdGhpcy5faW5pdEN1c3RvbWVyU2VsZWN0KGUpKTtcbiAgICB0aGlzLiRjb250YWluZXIub24oJ2NsaWNrJywgY3JlYXRlT3JkZXJNYXAudXNlQ2FydEJ0biwgZSA9PiB0aGlzLl9pbml0Q2FydFNlbGVjdChlKSk7XG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjbGljaycsIGNyZWF0ZU9yZGVyTWFwLnVzZU9yZGVyQnRuLCBlID0+IHRoaXMuX2luaXREdXBsaWNhdGVPcmRlckNhcnQoZSkpO1xuICAgIHRoaXMuJGNvbnRhaW5lci5vbignaW5wdXQnLCBjcmVhdGVPcmRlck1hcC5wcm9kdWN0U2VhcmNoLCBlID0+IHRoaXMuX2luaXRQcm9kdWN0U2VhcmNoKGUpKTtcbiAgICB0aGlzLiRjb250YWluZXIub24oJ2lucHV0JywgY3JlYXRlT3JkZXJNYXAuY2FydFJ1bGVTZWFyY2hJbnB1dCwgZSA9PiB0aGlzLl9pbml0Q2FydFJ1bGVTZWFyY2goZSkpO1xuICAgIHRoaXMuJGNvbnRhaW5lci5vbignYmx1cicsIGNyZWF0ZU9yZGVyTWFwLmNhcnRSdWxlU2VhcmNoSW5wdXQsICgpID0+IHRoaXMuY2FydFJ1bGVNYW5hZ2VyLnN0b3BTZWFyY2hpbmcoKSk7XG4gICAgdGhpcy5fbGlzdGVuRm9yQ2FydEVkaXQoKTtcbiAgICB0aGlzLl9vbkNhcnRMb2FkZWQoKTtcbiAgICB0aGlzLm9uQ3VzdG9tZXJzTm90Rm91bmQoKTtcbiAgICB0aGlzLl9vbkN1c3RvbWVyU2VsZWN0ZWQoKTtcbiAgICB0aGlzLmluaXRBZGRyZXNzQnV0dG9uc0lmcmFtZSgpO1xuICAgIHRoaXMuaW5pdEN1c3RvbWVyRGV0YWlsc0lmcmFtZSgpO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBpbml0QWRkcmVzc0J1dHRvbnNJZnJhbWUoKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5hZGRyZXNzQWRkQnRuKS5mYW5jeWJveCh7XG4gICAgICAndHlwZSc6ICdpZnJhbWUnLFxuICAgICAgJ3dpZHRoJzogJzkwJScsXG4gICAgICAnaGVpZ2h0JzogJzkwJScsXG4gICAgfSk7XG5cbiAgICAkKGNyZWF0ZU9yZGVyTWFwLmludm9pY2VBZGRyZXNzRWRpdEJ0bikuZmFuY3lib3goe1xuICAgICAgJ3R5cGUnOiAnaWZyYW1lJyxcbiAgICAgICd3aWR0aCc6ICc5MCUnLFxuICAgICAgJ2hlaWdodCc6ICc5MCUnLFxuICAgIH0pO1xuXG4gICAgJChjcmVhdGVPcmRlck1hcC5kZWxpdmVyeUFkZHJlc3NFZGl0QnRuKS5mYW5jeWJveCh7XG4gICAgICAndHlwZSc6ICdpZnJhbWUnLFxuICAgICAgJ3dpZHRoJzogJzkwJScsXG4gICAgICAnaGVpZ2h0JzogJzkwJScsXG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogaW5pdCBvZiBpZnJhbWUgdXNlZCB3aGVuIGNyZWF0aW5nIG5ldyBPcmRlciAtPiBTZWFyY2ggZm9yIGEgY3VzdG9tZXIgLT4gRGV0YWlsc1xuICAgKi9cbiAgaW5pdEN1c3RvbWVyRGV0YWlsc0lmcmFtZSgpIHtcbiAgICAkKCcjanMtZGV0YWlscy1jdXN0b21lci1idG4nKS5mYW5jeWJveCh7XG4gICAgICAndHlwZSc6ICdpZnJhbWUnLFxuICAgICAgJ3dpZHRoJzogJzkwJScsXG4gICAgICAnaGVpZ2h0JzogJzkwJScsXG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogRGVsZWdhdGVzIGFjdGlvbnMgdG8gZXZlbnRzIGFzc29jaWF0ZWQgd2l0aCBjYXJ0IHVwZGF0ZSAoZS5nLiBjaGFuZ2UgY2FydCBhZGRyZXNzKVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2xpc3RlbkZvckNhcnRFZGl0KCkge1xuICAgIHRoaXMuX29uQ2FydEFkZHJlc3Nlc0NoYW5nZWQoKTtcbiAgICB0aGlzLl9vbkRlbGl2ZXJ5T3B0aW9uQ2hhbmdlZCgpO1xuICAgIHRoaXMuX29uRnJlZVNoaXBwaW5nQ2hhbmdlZCgpO1xuICAgIHRoaXMuX2FkZENhcnRSdWxlVG9DYXJ0KCk7XG4gICAgdGhpcy5fcmVtb3ZlQ2FydFJ1bGVGcm9tQ2FydCgpO1xuICAgIHRoaXMuX29uQ2FydEN1cnJlbmN5Q2hhbmdlZCgpO1xuICAgIHRoaXMuX29uQ2FydExhbmd1YWdlQ2hhbmdlZCgpO1xuXG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjaGFuZ2UnLCBjcmVhdGVPcmRlck1hcC5kZWxpdmVyeU9wdGlvblNlbGVjdCwgZSA9PlxuICAgICAgdGhpcy5jYXJ0RWRpdG9yLmNoYW5nZURlbGl2ZXJ5T3B0aW9uKHRoaXMuY2FydElkLCBlLmN1cnJlbnRUYXJnZXQudmFsdWUpLFxuICAgICk7XG5cbiAgICB0aGlzLiRjb250YWluZXIub24oJ2NoYW5nZScsIGNyZWF0ZU9yZGVyTWFwLmZyZWVTaGlwcGluZ1N3aXRjaCwgZSA9PlxuICAgICAgdGhpcy5jYXJ0RWRpdG9yLnNldEZyZWVTaGlwcGluZyh0aGlzLmNhcnRJZCwgZS5jdXJyZW50VGFyZ2V0LnZhbHVlKSxcbiAgICApO1xuXG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjbGljaycsIGNyZWF0ZU9yZGVyTWFwLmFkZFRvQ2FydEJ1dHRvbiwgKCkgPT5cbiAgICAgIHRoaXMucHJvZHVjdE1hbmFnZXIuYWRkUHJvZHVjdFRvQ2FydCh0aGlzLmNhcnRJZCksXG4gICAgKTtcblxuICAgIHRoaXMuJGNvbnRhaW5lci5vbignY2hhbmdlJywgY3JlYXRlT3JkZXJNYXAuY2FydEN1cnJlbmN5U2VsZWN0LCAoZSkgPT5cbiAgICAgIHRoaXMuY2FydEVkaXRvci5jaGFuZ2VDYXJ0Q3VycmVuY3kodGhpcy5jYXJ0SWQsIGUuY3VycmVudFRhcmdldC52YWx1ZSlcbiAgICApO1xuXG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjaGFuZ2UnLCBjcmVhdGVPcmRlck1hcC5jYXJ0TGFuZ3VhZ2VTZWxlY3QsIChlKSA9PlxuICAgICAgdGhpcy5jYXJ0RWRpdG9yLmNoYW5nZUNhcnRMYW5ndWFnZSh0aGlzLmNhcnRJZCwgZS5jdXJyZW50VGFyZ2V0LnZhbHVlKVxuICAgICk7XG5cbiAgICB0aGlzLiRjb250YWluZXIub24oJ2NsaWNrJywgY3JlYXRlT3JkZXJNYXAuc2VuZFByb2Nlc3NPcmRlckVtYWlsQnRuLCAoKSA9PlxuICAgICAgdGhpcy5zdW1tYXJ5TWFuYWdlci5zZW5kUHJvY2Vzc09yZGVyRW1haWwodGhpcy5jYXJ0SWQpXG4gICAgKTtcblxuICAgIHRoaXMuJGNvbnRhaW5lci5vbignY2hhbmdlJywgY3JlYXRlT3JkZXJNYXAubGlzdGVkUHJvZHVjdFVuaXRQcmljZUlucHV0LCAoZSkgPT4gdGhpcy5faW5pdFByb2R1Y3RDaGFuZ2VQcmljZShlKSk7XG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjaGFuZ2UnLCBjcmVhdGVPcmRlck1hcC5saXN0ZWRQcm9kdWN0UXR5SW5wdXQsIGUgPT4gdGhpcy5faW5pdFByb2R1Y3RDaGFuZ2VRdHkoZSkpO1xuICAgIHRoaXMuJGNvbnRhaW5lci5vbignY2hhbmdlJywgY3JlYXRlT3JkZXJNYXAuYWRkcmVzc1NlbGVjdCwgKCkgPT4gdGhpcy5fY2hhbmdlQ2FydEFkZHJlc3NlcygpKTtcbiAgICB0aGlzLiRjb250YWluZXIub24oJ2NsaWNrJywgY3JlYXRlT3JkZXJNYXAucHJvZHVjdFJlbW92ZUJ0biwgZSA9PiB0aGlzLl9pbml0UHJvZHVjdFJlbW92ZUZyb21DYXJ0KGUpKTtcblxuICB9XG5cbiAgLyoqXG4gICAqIExpc3RlbnMgZm9yIGV2ZW50IHdoZW4gY2FydCBpcyBsb2FkZWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9vbkNhcnRMb2FkZWQoKSB7XG4gICAgRXZlbnRFbWl0dGVyLm9uKGV2ZW50TWFwLmNhcnRMb2FkZWQsIChjYXJ0SW5mbykgPT4ge1xuICAgICAgdGhpcy5jYXJ0SWQgPSBjYXJ0SW5mby5jYXJ0SWQ7XG4gICAgICB0aGlzLl9yZW5kZXJDYXJ0SW5mbyhjYXJ0SW5mbyk7XG4gICAgICBpZiAoY2FydEluZm8uYWRkcmVzc2VzLmxlbmd0aCAhPT0gMCAmJiAhQ3JlYXRlT3JkZXJQYWdlLnZhbGlkYXRlU2VsZWN0ZWRBZGRyZXNzZXMoY2FydEluZm8uYWRkcmVzc2VzKSkge1xuICAgICAgICB0aGlzLl9jaGFuZ2VDYXJ0QWRkcmVzc2VzKCk7XG4gICAgICB9XG4gICAgICB0aGlzLmN1c3RvbWVyTWFuYWdlci5sb2FkQ3VzdG9tZXJDYXJ0cyh0aGlzLmNhcnRJZCk7XG4gICAgICB0aGlzLmN1c3RvbWVyTWFuYWdlci5sb2FkQ3VzdG9tZXJPcmRlcnMoKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBMaXN0ZW5zIGZvciBldmVudCB3aGVuIG5vIGN1c3RvbWVycyB3ZXJlIGZvdW5kIGJ5IHNlYXJjaFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgb25DdXN0b21lcnNOb3RGb3VuZCgpIHtcbiAgICBFdmVudEVtaXR0ZXIub24oZXZlbnRNYXAuY3VzdG9tZXJzTm90Rm91bmQsICgpID0+IHtcbiAgICAgIHRoaXMuaGlkZUNhcnRJbmZvKCk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogTGlzdGVucyBmb3IgZXZlbnQgd2hlbiBjdXN0b21lciBpcyBzZWxlY3RlZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uQ3VzdG9tZXJTZWxlY3RlZCgpIHtcbiAgICBFdmVudEVtaXR0ZXIub24oZXZlbnRNYXAuY3VzdG9tZXJTZWxlY3RlZCwgKCkgPT4ge1xuICAgICAgdGhpcy5zaG93Q2FydEluZm8oKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBMaXN0ZW5zIGZvciBjYXJ0IGFkZHJlc3NlcyB1cGRhdGUgZXZlbnRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9vbkNhcnRBZGRyZXNzZXNDaGFuZ2VkKCkge1xuICAgIEV2ZW50RW1pdHRlci5vbihldmVudE1hcC5jYXJ0QWRkcmVzc2VzQ2hhbmdlZCwgKGNhcnRJbmZvKSA9PiB7XG4gICAgICB0aGlzLmFkZHJlc3Nlc1JlbmRlcmVyLnJlbmRlcihjYXJ0SW5mby5hZGRyZXNzZXMpO1xuICAgICAgdGhpcy5zaGlwcGluZ1JlbmRlcmVyLnJlbmRlcihjYXJ0SW5mby5zaGlwcGluZywgY2FydEluZm8ucHJvZHVjdHMubGVuZ3RoID09PSAwKTtcbiAgICAgIHRoaXMuc3VtbWFyeVJlbmRlcmVyLnJlbmRlcihjYXJ0SW5mbyk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogTGlzdGVucyBmb3IgY2FydCBkZWxpdmVyeSBvcHRpb24gdXBkYXRlIGV2ZW50XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfb25EZWxpdmVyeU9wdGlvbkNoYW5nZWQoKSB7XG4gICAgRXZlbnRFbWl0dGVyLm9uKGV2ZW50TWFwLmNhcnREZWxpdmVyeU9wdGlvbkNoYW5nZWQsIChjYXJ0SW5mbykgPT4ge1xuICAgICAgdGhpcy5zaGlwcGluZ1JlbmRlcmVyLnJlbmRlcihjYXJ0SW5mby5zaGlwcGluZywgY2FydEluZm8ucHJvZHVjdHMubGVuZ3RoID09PSAwKTtcbiAgICAgIHRoaXMuc3VtbWFyeVJlbmRlcmVyLnJlbmRlcihjYXJ0SW5mbyk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogTGlzdGVucyBmb3IgY2FydCBmcmVlIHNoaXBwaW5nIHVwZGF0ZSBldmVudFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uRnJlZVNoaXBwaW5nQ2hhbmdlZCgpIHtcbiAgICBFdmVudEVtaXR0ZXIub24oZXZlbnRNYXAuY2FydEZyZWVTaGlwcGluZ1NldCwgKGNhcnRJbmZvKSA9PiB7XG4gICAgICB0aGlzLmNhcnRSdWxlc1JlbmRlcmVyLnJlbmRlckNhcnRSdWxlc0Jsb2NrKGNhcnRJbmZvLmNhcnRSdWxlcywgY2FydEluZm8ucHJvZHVjdHMubGVuZ3RoID09PSAwKTtcbiAgICAgIHRoaXMuc2hpcHBpbmdSZW5kZXJlci5yZW5kZXIoY2FydEluZm8uc2hpcHBpbmcsIGNhcnRJbmZvLnByb2R1Y3RzLmxlbmd0aCA9PT0gMCk7XG4gICAgICB0aGlzLnN1bW1hcnlSZW5kZXJlci5yZW5kZXIoY2FydEluZm8pO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIExpc3RlbnMgZm9yIGNhcnQgbGFuZ3VhZ2UgdXBkYXRlIGV2ZW50XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfb25DYXJ0TGFuZ3VhZ2VDaGFuZ2VkKCkge1xuICAgIEV2ZW50RW1pdHRlci5vbihldmVudE1hcC5jYXJ0TGFuZ3VhZ2VDaGFuZ2VkLCAoY2FydEluZm8pID0+IHtcbiAgICAgIHRoaXMuX3ByZXNlbGVjdENhcnRMYW5ndWFnZShjYXJ0SW5mby5sYW5nSWQpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIExpc3RlbnMgZm9yIGNhcnQgY3VycmVuY3kgdXBkYXRlIGV2ZW50XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfb25DYXJ0Q3VycmVuY3lDaGFuZ2VkKCkge1xuICAgIC8vIG9uIHN1Y2Nlc3NcbiAgICBFdmVudEVtaXR0ZXIub24oZXZlbnRNYXAuY2FydEN1cnJlbmN5Q2hhbmdlZCwgKGNhcnRJbmZvKSA9PiB7XG4gICAgICB0aGlzLl9yZW5kZXJDYXJ0SW5mbyhjYXJ0SW5mbyk7XG4gICAgICB0aGlzLnByb2R1Y3RSZW5kZXJlci5yZXNldCgpO1xuICAgIH0pO1xuXG4gICAgLy8gb24gZmFpbHVyZVxuICAgIEV2ZW50RW1pdHRlci5vbihldmVudE1hcC5jYXJ0Q3VycmVuY3lDaGFuZ2VGYWlsZWQsIChyZXNwb25zZSkgPT4ge1xuICAgICAgdGhpcy5wcm9kdWN0UmVuZGVyZXIucmVuZGVyQ2FydEJsb2NrRXJyb3JBbGVydChyZXNwb25zZS5yZXNwb25zZUpTT04ubWVzc2FnZSlcbiAgICB9KVxuICB9XG5cbiAgLyoqXG4gICAqIEluaXQgY3VzdG9tZXIgc2VhcmNoaW5nXG4gICAqXG4gICAqIEBwYXJhbSBldmVudFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2luaXRDdXN0b21lclNlYXJjaChldmVudCkge1xuICAgIGNsZWFyVGltZW91dCh0aGlzLnRpbWVvdXRJZCk7XG4gICAgdGhpcy50aW1lb3V0SWQgPSBzZXRUaW1lb3V0KCgpID0+IHRoaXMuY3VzdG9tZXJNYW5hZ2VyLnNlYXJjaCgkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLnZhbCgpKSwgMzAwKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJbml0IHNlbGVjdGluZyBjdXN0b21lciBmb3Igd2hpY2ggb3JkZXIgaXMgYmVpbmcgY3JlYXRlZFxuICAgKlxuICAgKiBAcGFyYW0gZXZlbnRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9pbml0Q3VzdG9tZXJTZWxlY3QoZXZlbnQpIHtcbiAgICBjb25zdCBjdXN0b21lcklkID0gdGhpcy5jdXN0b21lck1hbmFnZXIuc2VsZWN0Q3VzdG9tZXIoZXZlbnQpO1xuICAgIHRoaXMuY3VzdG9tZXJJZCA9IGN1c3RvbWVySWQ7XG4gICAgdGhpcy5jYXJ0UHJvdmlkZXIubG9hZEVtcHR5Q2FydChjdXN0b21lcklkKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJbml0cyBzZWxlY3RpbmcgY2FydCB0byBsb2FkXG4gICAqXG4gICAqIEBwYXJhbSBldmVudFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2luaXRDYXJ0U2VsZWN0KGV2ZW50KSB7XG4gICAgY29uc3QgY2FydElkID0gJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdjYXJ0LWlkJyk7XG4gICAgdGhpcy5jYXJ0UHJvdmlkZXIuZ2V0Q2FydChjYXJ0SWQpO1xuICB9XG5cbiAgLyoqXG4gICAqIEluaXRzIGR1cGxpY2F0aW5nIG9yZGVyIGNhcnRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9pbml0RHVwbGljYXRlT3JkZXJDYXJ0KGV2ZW50KSB7XG4gICAgY29uc3Qgb3JkZXJJZCA9ICQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgnb3JkZXItaWQnKTtcbiAgICB0aGlzLmNhcnRQcm92aWRlci5kdXBsaWNhdGVPcmRlckNhcnQob3JkZXJJZCk7XG4gIH1cblxuICAvKipcbiAgICogVHJpZ2dlcnMgY2FydCBydWxlIHNlYXJjaGluZ1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2luaXRDYXJ0UnVsZVNlYXJjaChldmVudCkge1xuICAgIGNvbnN0IHNlYXJjaFBocmFzZSA9IGV2ZW50LmN1cnJlbnRUYXJnZXQudmFsdWU7XG5cbiAgICBjbGVhclRpbWVvdXQodGhpcy50aW1lb3V0SWQpO1xuICAgIHRoaXMudGltZW91dElkID0gc2V0VGltZW91dCgoKSA9PiB0aGlzLmNhcnRSdWxlTWFuYWdlci5zZWFyY2goc2VhcmNoUGhyYXNlKSwgMzAwKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBUcmlnZ2VycyBjYXJ0IHJ1bGUgc2VsZWN0XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfYWRkQ2FydFJ1bGVUb0NhcnQoKSB7XG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdtb3VzZWRvd24nLCBjcmVhdGVPcmRlck1hcC5mb3VuZENhcnRSdWxlTGlzdEl0ZW0sIChldmVudCkgPT4ge1xuICAgICAgLy8gcHJldmVudCBibHVyIGV2ZW50IHRvIGFsbG93IHNlbGVjdGluZyBjYXJ0IHJ1bGVcbiAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICBjb25zdCBjYXJ0UnVsZUlkID0gJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdjYXJ0LXJ1bGUtaWQnKTtcbiAgICAgIHRoaXMuY2FydFJ1bGVNYW5hZ2VyLmFkZENhcnRSdWxlVG9DYXJ0KGNhcnRSdWxlSWQsIHRoaXMuY2FydElkKTtcblxuICAgICAgLy8gbWFudWFsbHkgZmlyZSBibHVyIGV2ZW50IGFmdGVyIGNhcnQgcnVsZSBpcyBzZWxlY3RlZC5cbiAgICB9KS5vbignY2xpY2snLCBjcmVhdGVPcmRlck1hcC5mb3VuZENhcnRSdWxlTGlzdEl0ZW0sICgpID0+IHtcbiAgICAgICQoY3JlYXRlT3JkZXJNYXAuY2FydFJ1bGVTZWFyY2hJbnB1dCkuYmx1cigpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIFRyaWdnZXJzIGNhcnQgcnVsZSByZW1vdmFsIGZyb20gY2FydFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3JlbW92ZUNhcnRSdWxlRnJvbUNhcnQoKSB7XG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjbGljaycsIGNyZWF0ZU9yZGVyTWFwLmNhcnRSdWxlRGVsZXRlQnRuLCAoZXZlbnQpID0+IHtcbiAgICAgIHRoaXMuY2FydFJ1bGVNYW5hZ2VyLnJlbW92ZUNhcnRSdWxlRnJvbUNhcnQoJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdjYXJ0LXJ1bGUtaWQnKSwgdGhpcy5jYXJ0SWQpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEluaXRzIHByb2R1Y3Qgc2VhcmNoaW5nXG4gICAqXG4gICAqIEBwYXJhbSBldmVudFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2luaXRQcm9kdWN0U2VhcmNoKGV2ZW50KSB7XG4gICAgY29uc3QgJHByb2R1Y3RTZWFyY2hJbnB1dCA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgY29uc3Qgc2VhcmNoUGhyYXNlID0gJHByb2R1Y3RTZWFyY2hJbnB1dC52YWwoKTtcbiAgICBjbGVhclRpbWVvdXQodGhpcy50aW1lb3V0SWQpO1xuXG4gICAgdGhpcy50aW1lb3V0SWQgPSBzZXRUaW1lb3V0KCgpID0+IHRoaXMucHJvZHVjdE1hbmFnZXIuc2VhcmNoKHNlYXJjaFBocmFzZSksIDMwMCk7XG4gIH1cblxuICAvKipcbiAgICogSW5pdHMgcHJvZHVjdCByZW1vdmluZyBmcm9tIGNhcnRcbiAgICpcbiAgICogQHBhcmFtIGV2ZW50XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaW5pdFByb2R1Y3RSZW1vdmVGcm9tQ2FydChldmVudCkge1xuICAgIGNvbnN0IHByb2R1Y3QgPSB7XG4gICAgICBwcm9kdWN0SWQ6ICQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgncHJvZHVjdC1pZCcpLFxuICAgICAgYXR0cmlidXRlSWQ6ICQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgnYXR0cmlidXRlLWlkJyksXG4gICAgICBjdXN0b21pemF0aW9uSWQ6ICQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgnY3VzdG9taXphdGlvbi1pZCcpLFxuICAgIH07XG5cbiAgICB0aGlzLnByb2R1Y3RNYW5hZ2VyLnJlbW92ZVByb2R1Y3RGcm9tQ2FydCh0aGlzLmNhcnRJZCwgcHJvZHVjdCk7XG4gIH1cblxuICAvKipcbiAgICogSW5pdHMgcHJvZHVjdCBpbiBjYXJ0IHByaWNlIGNoYW5nZVxuICAgKlxuICAgKiBAcGFyYW0gZXZlbnRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9pbml0UHJvZHVjdENoYW5nZVByaWNlKGV2ZW50KSB7XG4gICAgY29uc3QgcHJvZHVjdCA9IHtcbiAgICAgIHByb2R1Y3RJZDogJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdwcm9kdWN0LWlkJyksXG4gICAgICBhdHRyaWJ1dGVJZDogJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdhdHRyaWJ1dGUtaWQnKSxcbiAgICAgIGN1c3RvbWl6YXRpb25JZDogJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdjdXN0b21pemF0aW9uLWlkJyksXG4gICAgICBwcmljZTogJChldmVudC5jdXJyZW50VGFyZ2V0KS52YWwoKSxcbiAgICB9O1xuXG4gICAgdGhpcy5wcm9kdWN0TWFuYWdlci5jaGFuZ2VQcm9kdWN0UHJpY2UodGhpcy5jYXJ0SWQsIHRoaXMuY3VzdG9tZXJJZCwgcHJvZHVjdCk7XG4gIH1cblxuICAvKipcbiAgICogSW5pdHMgcHJvZHVjdCBpbiBjYXJ0IHF1YW50aXR5IHVwZGF0ZVxuICAgKlxuICAgKiBAcGFyYW0gZXZlbnRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9pbml0UHJvZHVjdENoYW5nZVF0eShldmVudCkge1xuICAgIGNvbnN0IHByb2R1Y3QgPSB7XG4gICAgICBwcm9kdWN0SWQ6ICQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgncHJvZHVjdC1pZCcpLFxuICAgICAgYXR0cmlidXRlSWQ6ICQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgnYXR0cmlidXRlLWlkJyksXG4gICAgICBjdXN0b21pemF0aW9uSWQ6ICQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgnY3VzdG9taXphdGlvbi1pZCcpLFxuICAgICAgbmV3UXR5OiAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLnZhbCgpLFxuICAgIH07XG5cbiAgICB0aGlzLnByb2R1Y3RNYW5hZ2VyLmNoYW5nZVByb2R1Y3RRdHkodGhpcy5jYXJ0SWQsIHByb2R1Y3QpO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlbmRlcnMgY2FydCBzdW1tYXJ5IG9uIHRoZSBwYWdlXG4gICAqXG4gICAqIEBwYXJhbSB7T2JqZWN0fSBjYXJ0SW5mb1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3JlbmRlckNhcnRJbmZvKGNhcnRJbmZvKSB7XG4gICAgdGhpcy5hZGRyZXNzZXNSZW5kZXJlci5yZW5kZXIoY2FydEluZm8uYWRkcmVzc2VzKTtcbiAgICB0aGlzLmNhcnRSdWxlc1JlbmRlcmVyLnJlbmRlckNhcnRSdWxlc0Jsb2NrKGNhcnRJbmZvLmNhcnRSdWxlcywgY2FydEluZm8ucHJvZHVjdHMubGVuZ3RoID09PSAwKTtcbiAgICB0aGlzLnNoaXBwaW5nUmVuZGVyZXIucmVuZGVyKGNhcnRJbmZvLnNoaXBwaW5nLCBjYXJ0SW5mby5wcm9kdWN0cy5sZW5ndGggPT09IDApO1xuICAgIHRoaXMucHJvZHVjdFJlbmRlcmVyLmNsZWFuQ2FydEJsb2NrQWxlcnRzKCk7XG4gICAgdGhpcy5wcm9kdWN0UmVuZGVyZXIucmVuZGVyTGlzdChjYXJ0SW5mby5wcm9kdWN0cyk7XG4gICAgdGhpcy5zdW1tYXJ5UmVuZGVyZXIucmVuZGVyKGNhcnRJbmZvKTtcbiAgICB0aGlzLl9wcmVzZWxlY3RDYXJ0Q3VycmVuY3koY2FydEluZm8uY3VycmVuY3lJZCk7XG4gICAgdGhpcy5fcHJlc2VsZWN0Q2FydExhbmd1YWdlKGNhcnRJbmZvLmxhbmdJZCk7XG5cbiAgICAkKGNyZWF0ZU9yZGVyTWFwLmNhcnRCbG9jaykucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICAgICQoY3JlYXRlT3JkZXJNYXAuY2FydEJsb2NrKS5kYXRhKCdjYXJ0SWQnLCBjYXJ0SW5mby5jYXJ0SWQpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNldHMgY2FydCBjdXJyZW5jeSBzZWxlY3Rpb24gdmFsdWVcbiAgICpcbiAgICogQHBhcmFtIGN1cnJlbmN5SWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9wcmVzZWxlY3RDYXJ0Q3VycmVuY3koY3VycmVuY3lJZCkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAuY2FydEN1cnJlbmN5U2VsZWN0KS52YWwoY3VycmVuY3lJZCk7XG4gIH1cblxuICAvKipcbiAgICogU2V0cyBjYXJ0IGxhbmd1YWdlIHNlbGVjdGlvbiB2YWx1ZVxuICAgKlxuICAgKiBAcGFyYW0gbGFuZ0lkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfcHJlc2VsZWN0Q2FydExhbmd1YWdlKGxhbmdJZCkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAuY2FydExhbmd1YWdlU2VsZWN0KS52YWwobGFuZ0lkKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBDaGFuZ2VzIGNhcnQgYWRkcmVzc2VzXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfY2hhbmdlQ2FydEFkZHJlc3NlcygpIHtcbiAgICBjb25zdCBhZGRyZXNzZXMgPSB7XG4gICAgICBkZWxpdmVyeUFkZHJlc3NJZDogJChjcmVhdGVPcmRlck1hcC5kZWxpdmVyeUFkZHJlc3NTZWxlY3QpLnZhbCgpLFxuICAgICAgaW52b2ljZUFkZHJlc3NJZDogJChjcmVhdGVPcmRlck1hcC5pbnZvaWNlQWRkcmVzc1NlbGVjdCkudmFsKCksXG4gICAgfTtcblxuICAgIHRoaXMuY2FydEVkaXRvci5jaGFuZ2VDYXJ0QWRkcmVzc2VzKHRoaXMuY2FydElkLCBhZGRyZXNzZXMpO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlZnJlc2ggYWRkcmVzc2VzIGxpc3RcbiAgICpcbiAgICogQHBhcmFtIHtib29sZWFufSByZWZyZXNoQ2FydEFkZHJlc3NlcyBvcHRpb25hbFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgcmVmcmVzaEFkZHJlc3Nlc0xpc3QocmVmcmVzaENhcnRBZGRyZXNzZXMpIHtcbiAgICBjb25zdCBjYXJ0SWQgPSAkKGNyZWF0ZU9yZGVyTWFwLmNhcnRCbG9jaykuZGF0YSgnY2FydElkJyk7XG4gICAgJC5nZXQodGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX2NhcnRzX2luZm8nLCB7Y2FydElkfSkpLnRoZW4oKGNhcnRJbmZvKSA9PiB7XG4gICAgICB0aGlzLmFkZHJlc3Nlc1JlbmRlcmVyLnJlbmRlcihjYXJ0SW5mby5hZGRyZXNzZXMpO1xuXG4gICAgICBpZiAocmVmcmVzaENhcnRBZGRyZXNzZXMpIHtcbiAgICAgICAgdGhpcy5fY2hhbmdlQ2FydEFkZHJlc3NlcygpO1xuICAgICAgfVxuICAgIH0pLmNhdGNoKChlKSA9PiB7XG4gICAgICBzaG93RXJyb3JNZXNzYWdlKGUucmVzcG9uc2VKU09OLm1lc3NhZ2UpO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvY3JlYXRlLW9yZGVyLXBhZ2UuanMiLCIvKipcbiAqIDIwMDctMjAyMCBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDIwIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgY3JlYXRlT3JkZXJNYXAgZnJvbSAnLi9jcmVhdGUtb3JkZXItbWFwJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIFJlbmRlcnMgY2FydCBydWxlcyAoY2FydFJ1bGVzKSBibG9ja1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBDYXJ0UnVsZXNSZW5kZXJlciB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHRoaXMuJGNhcnRSdWxlc0Jsb2NrID0gJChjcmVhdGVPcmRlck1hcC5jYXJ0UnVsZXNCbG9jayk7XG4gICAgdGhpcy4kY2FydFJ1bGVzVGFibGUgPSAkKGNyZWF0ZU9yZGVyTWFwLmNhcnRSdWxlc1RhYmxlKTtcbiAgICB0aGlzLiRzZWFyY2hSZXN1bHRCb3ggPSAkKGNyZWF0ZU9yZGVyTWFwLmNhcnRSdWxlc1NlYXJjaFJlc3VsdEJveCk7XG4gIH1cblxuICAvKipcbiAgICogUmVzcG9uc2libGUgZm9yIHJlbmRlcmluZyBjYXJ0UnVsZXMgKGEuay5hIGNhcnQgcnVsZXMvZGlzY291bnRzKSBibG9ja1xuICAgKlxuICAgKiBAcGFyYW0ge0FycmF5fSBjYXJ0UnVsZXNcbiAgICogQHBhcmFtIHtCb29sZWFufSBlbXB0eUNhcnRcbiAgICovXG4gIHJlbmRlckNhcnRSdWxlc0Jsb2NrKGNhcnRSdWxlcywgZW1wdHlDYXJ0KSB7XG4gICAgdGhpcy5faGlkZUVycm9yQmxvY2soKTtcbiAgICAvLyBkbyBub3QgcmVuZGVyIGNhcnQgcnVsZXMgYmxvY2sgYXQgYWxsIGlmIGNhcnQgaGFzIG5vIHByb2R1Y3RzXG4gICAgaWYgKGVtcHR5Q2FydCkge1xuICAgICAgdGhpcy5faGlkZUNhcnRSdWxlc0Jsb2NrKCk7XG4gICAgICByZXR1cm47XG4gICAgfVxuICAgIHRoaXMuX3Nob3dDYXJ0UnVsZXNCbG9jaygpO1xuXG4gICAgLy8gZG8gbm90IHJlbmRlciBjYXJ0IHJ1bGVzIGxpc3Qgd2hlbiB0aGVyZSBhcmUgbm8gY2FydCBydWxlc1xuICAgIGlmIChjYXJ0UnVsZXMubGVuZ3RoID09PSAwKSB7XG4gICAgICB0aGlzLl9oaWRlQ2FydFJ1bGVzTGlzdCgpO1xuXG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgdGhpcy5fcmVuZGVyTGlzdChjYXJ0UnVsZXMpO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlc3BvbnNpYmxlIGZvciByZW5kZXJpbmcgc2VhcmNoIHJlc3VsdHMgZHJvcGRvd25cbiAgICpcbiAgICogQHBhcmFtIHNlYXJjaFJlc3VsdHNcbiAgICovXG4gIHJlbmRlclNlYXJjaFJlc3VsdHMoc2VhcmNoUmVzdWx0cykge1xuICAgIHRoaXMuX2NsZWFyU2VhcmNoUmVzdWx0cygpO1xuXG4gICAgaWYgKHNlYXJjaFJlc3VsdHMuY2FydF9ydWxlcy5sZW5ndGggPT09IDApIHtcbiAgICAgIHRoaXMuX3JlbmRlck5vdEZvdW5kKCk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHRoaXMuX3JlbmRlckZvdW5kQ2FydFJ1bGVzKHNlYXJjaFJlc3VsdHMuY2FydF9ydWxlcyk7XG4gICAgfVxuXG4gICAgdGhpcy5fc2hvd1Jlc3VsdHNEcm9wZG93bigpO1xuICB9XG5cbiAgLyoqXG4gICAqIERpc3BsYXlzIGVycm9yIG1lc3NhZ2UgYmVsbG93IHNlYXJjaCBpbnB1dFxuICAgKlxuICAgKiBAcGFyYW0gbWVzc2FnZVxuICAgKi9cbiAgZGlzcGxheUVycm9yTWVzc2FnZShtZXNzYWdlKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5jYXJ0UnVsZUVycm9yVGV4dCkudGV4dChtZXNzYWdlKTtcbiAgICB0aGlzLl9zaG93RXJyb3JCbG9jaygpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhpZGVzIGNhcnQgcnVsZXMgc2VhcmNoIHJlc3VsdCBkcm9wZG93blxuICAgKi9cbiAgaGlkZVJlc3VsdHNEcm9wZG93bigpIHtcbiAgICB0aGlzLiRzZWFyY2hSZXN1bHRCb3guYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIERpc3BsYXlzIGNhcnQgcnVsZXMgc2VhcmNoIHJlc3VsdCBkcm9wZG93blxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3Nob3dSZXN1bHRzRHJvcGRvd24oKSB7XG4gICAgdGhpcy4kc2VhcmNoUmVzdWx0Qm94LnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZW5kZXJzIHdhcm5pbmcgdGhhdCBubyBjYXJ0IHJ1bGUgd2FzIGZvdW5kXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfcmVuZGVyTm90Rm91bmQoKSB7XG4gICAgY29uc3QgJHRlbXBsYXRlID0gJCgkKGNyZWF0ZU9yZGVyTWFwLmNhcnRSdWxlc05vdEZvdW5kVGVtcGxhdGUpLmh0bWwoKSkuY2xvbmUoKTtcbiAgICB0aGlzLiRzZWFyY2hSZXN1bHRCb3guaHRtbCgkdGVtcGxhdGUpO1xuICB9XG5cblxuICAvKipcbiAgICogRW1wdGllcyBjYXJ0IHJ1bGUgc2VhcmNoIHJlc3VsdHMgYmxvY2tcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9jbGVhclNlYXJjaFJlc3VsdHMoKSB7XG4gICAgdGhpcy4kc2VhcmNoUmVzdWx0Qm94LmVtcHR5KCk7XG4gIH1cblxuICAvKipcbiAgICogUmVuZGVycyBmb3VuZCBjYXJ0IHJ1bGVzIGFmdGVyIHNlYXJjaFxuICAgKlxuICAgKiBAcGFyYW0gY2FydFJ1bGVzXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfcmVuZGVyRm91bmRDYXJ0UnVsZXMoY2FydFJ1bGVzKSB7XG4gICAgY29uc3QgJGNhcnRSdWxlVGVtcGxhdGUgPSAkKCQoY3JlYXRlT3JkZXJNYXAuZm91bmRDYXJ0UnVsZVRlbXBsYXRlKS5odG1sKCkpO1xuICAgIGZvciAoY29uc3Qga2V5IGluIGNhcnRSdWxlcykge1xuICAgICAgY29uc3QgJHRlbXBsYXRlID0gJGNhcnRSdWxlVGVtcGxhdGUuY2xvbmUoKTtcbiAgICAgIGNvbnN0IGNhcnRSdWxlID0gY2FydFJ1bGVzW2tleV07XG5cbiAgICAgIGxldCBjYXJ0UnVsZU5hbWUgPSBjYXJ0UnVsZS5uYW1lO1xuICAgICAgaWYgKGNhcnRSdWxlLmNvZGUgIT09ICcnKSB7XG4gICAgICAgIGNhcnRSdWxlTmFtZSA9IGAke2NhcnRSdWxlLm5hbWV9IC0gJHtjYXJ0UnVsZS5jb2RlfWA7XG4gICAgICB9XG5cbiAgICAgICR0ZW1wbGF0ZS50ZXh0KGNhcnRSdWxlTmFtZSk7XG4gICAgICAkdGVtcGxhdGUuZGF0YSgnY2FydC1ydWxlLWlkJywgY2FydFJ1bGUuY2FydFJ1bGVJZCk7XG4gICAgICB0aGlzLiRzZWFyY2hSZXN1bHRCb3guYXBwZW5kKCR0ZW1wbGF0ZSk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIFJlc3BvbnNpYmxlIGZvciByZW5kZXJpbmcgdGhlIGxpc3Qgb2YgY2FydCBydWxlc1xuICAgKlxuICAgKiBAcGFyYW0ge0FycmF5fSBjYXJ0UnVsZXNcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9yZW5kZXJMaXN0KGNhcnRSdWxlcykge1xuICAgIHRoaXMuX2NsZWFuQ2FydFJ1bGVzTGlzdCgpO1xuICAgIGNvbnN0ICRjYXJ0UnVsZXNUYWJsZVJvd1RlbXBsYXRlID0gJCgkKGNyZWF0ZU9yZGVyTWFwLmNhcnRSdWxlc1RhYmxlUm93VGVtcGxhdGUpLmh0bWwoKSk7XG5cbiAgICBmb3IgKGNvbnN0IGtleSBpbiBjYXJ0UnVsZXMpIHtcbiAgICAgIGNvbnN0IGNhcnRSdWxlID0gY2FydFJ1bGVzW2tleV07XG4gICAgICBjb25zdCAkdGVtcGxhdGUgPSAkY2FydFJ1bGVzVGFibGVSb3dUZW1wbGF0ZS5jbG9uZSgpO1xuXG4gICAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5jYXJ0UnVsZU5hbWVGaWVsZCkudGV4dChjYXJ0UnVsZS5uYW1lKTtcbiAgICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyTWFwLmNhcnRSdWxlRGVzY3JpcHRpb25GaWVsZCkudGV4dChjYXJ0UnVsZS5kZXNjcmlwdGlvbik7XG4gICAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5jYXJ0UnVsZVZhbHVlRmllbGQpLnRleHQoY2FydFJ1bGUudmFsdWUpO1xuICAgICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAuY2FydFJ1bGVEZWxldGVCdG4pLmRhdGEoJ2NhcnQtcnVsZS1pZCcsIGNhcnRSdWxlLmNhcnRSdWxlSWQpO1xuXG4gICAgICB0aGlzLiRjYXJ0UnVsZXNUYWJsZS5maW5kKCd0Ym9keScpLmFwcGVuZCgkdGVtcGxhdGUpO1xuICAgIH1cblxuICAgIHRoaXMuX3Nob3dDYXJ0UnVsZXNMaXN0KCk7XG4gIH1cblxuICAvKipcbiAgICogU2hvd3MgZXJyb3IgYmxvY2tcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zaG93RXJyb3JCbG9jaygpIHtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLmNhcnRSdWxlRXJyb3JCbG9jaykucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhpZGVzIGVycm9yIGJsb2NrXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGlkZUVycm9yQmxvY2soKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5jYXJ0UnVsZUVycm9yQmxvY2spLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93cyBjYXJ0UnVsZXMgYmxvY2tcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zaG93Q2FydFJ1bGVzQmxvY2soKSB7XG4gICAgdGhpcy4kY2FydFJ1bGVzQmxvY2sucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIGhpZGUgY2FydFJ1bGVzIGJsb2NrXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGlkZUNhcnRSdWxlc0Jsb2NrKCkge1xuICAgIHRoaXMuJGNhcnRSdWxlc0Jsb2NrLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBEaXNwbGF5IHRoZSBsaXN0IGJsb2NrIG9mIGNhcnQgcnVsZXNcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zaG93Q2FydFJ1bGVzTGlzdCgpIHtcbiAgICB0aGlzLiRjYXJ0UnVsZXNUYWJsZS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZSBsaXN0IGJsb2NrIG9mIGNhcnQgcnVsZXNcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oaWRlQ2FydFJ1bGVzTGlzdCgpIHtcbiAgICB0aGlzLiRjYXJ0UnVsZXNUYWJsZS5hZGRDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogcmVtb3ZlIGl0ZW1zIGluIGNhcnQgcnVsZXMgbGlzdFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2NsZWFuQ2FydFJ1bGVzTGlzdCgpIHtcbiAgICB0aGlzLiRjYXJ0UnVsZXNUYWJsZS5maW5kKCd0Ym9keScpLmVtcHR5KCk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9jYXJ0LXJ1bGVzLXJlbmRlcmVyLmpzIiwiLyoqXG4gKiAyMDA3LTIwMjAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAyMCBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IGNyZWF0ZU9yZGVyTWFwIGZyb20gJy4vY3JlYXRlLW9yZGVyLW1hcCc7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuZXhwb3J0IGRlZmF1bHQgY2xhc3MgUHJvZHVjdFJlbmRlcmVyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy4kcHJvZHVjdHNUYWJsZSA9ICQoY3JlYXRlT3JkZXJNYXAucHJvZHVjdHNUYWJsZSk7XG4gIH1cblxuICAvKipcbiAgICogUmVuZGVycyBjYXJ0IHByb2R1Y3RzIGxpc3RcbiAgICpcbiAgICogQHBhcmFtIHByb2R1Y3RzXG4gICAqL1xuICByZW5kZXJMaXN0KHByb2R1Y3RzKSB7XG4gICAgdGhpcy5fY2xlYW5Qcm9kdWN0c0xpc3QoKTtcblxuICAgIGlmIChwcm9kdWN0cy5sZW5ndGggPT09IDApIHtcbiAgICAgIHRoaXMuX2hpZGVQcm9kdWN0c0xpc3QoKTtcblxuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIGNvbnN0ICRwcm9kdWN0c1RhYmxlUm93VGVtcGxhdGUgPSAkKCQoY3JlYXRlT3JkZXJNYXAucHJvZHVjdHNUYWJsZVJvd1RlbXBsYXRlKS5odG1sKCkpO1xuXG4gICAgZm9yIChjb25zdCBrZXkgaW4gcHJvZHVjdHMpIHtcbiAgICAgIGNvbnN0IHByb2R1Y3QgPSBwcm9kdWN0c1trZXldO1xuICAgICAgY29uc3QgJHRlbXBsYXRlID0gJHByb2R1Y3RzVGFibGVSb3dUZW1wbGF0ZS5jbG9uZSgpO1xuICAgICAgbGV0IGN1c3RvbWl6YXRpb25JZCA9IDA7XG5cbiAgICAgIGlmIChwcm9kdWN0LmN1c3RvbWl6YXRpb24pIHtcbiAgICAgICAgY3VzdG9taXphdGlvbklkID0gcHJvZHVjdC5jdXN0b21pemF0aW9uLmN1c3RvbWl6YXRpb25JZDtcbiAgICAgICAgdGhpcy5fcmVuZGVyTGlzdGVkUHJvZHVjdEN1c3RvbWl6YXRpb24ocHJvZHVjdC5jdXN0b21pemF0aW9uLCAkdGVtcGxhdGUpO1xuICAgICAgfVxuXG4gICAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5saXN0ZWRQcm9kdWN0SW1hZ2VGaWVsZCkucHJvcCgnc3JjJywgcHJvZHVjdC5pbWFnZUxpbmspO1xuICAgICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAubGlzdGVkUHJvZHVjdE5hbWVGaWVsZCkudGV4dChwcm9kdWN0Lm5hbWUpO1xuICAgICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAubGlzdGVkUHJvZHVjdEF0dHJGaWVsZCkudGV4dChwcm9kdWN0LmF0dHJpYnV0ZSk7XG4gICAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5saXN0ZWRQcm9kdWN0UmVmZXJlbmNlRmllbGQpLnRleHQocHJvZHVjdC5yZWZlcmVuY2UpO1xuICAgICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAubGlzdGVkUHJvZHVjdFVuaXRQcmljZUlucHV0KS52YWwocHJvZHVjdC51bml0UHJpY2UpO1xuICAgICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAubGlzdGVkUHJvZHVjdFVuaXRQcmljZUlucHV0KS5kYXRhKCdwcm9kdWN0LWlkJywgcHJvZHVjdC5wcm9kdWN0SWQpO1xuICAgICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAubGlzdGVkUHJvZHVjdFVuaXRQcmljZUlucHV0KS5kYXRhKCdhdHRyaWJ1dGUtaWQnLCBwcm9kdWN0LmF0dHJpYnV0ZUlkKTtcbiAgICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyTWFwLmxpc3RlZFByb2R1Y3RVbml0UHJpY2VJbnB1dCkuZGF0YSgnY3VzdG9taXphdGlvbi1pZCcsIGN1c3RvbWl6YXRpb25JZCk7XG4gICAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5saXN0ZWRQcm9kdWN0UXR5SW5wdXQpLnZhbChwcm9kdWN0LnF1YW50aXR5KTtcbiAgICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyTWFwLmxpc3RlZFByb2R1Y3RRdHlJbnB1dCkuZGF0YSgncHJvZHVjdC1pZCcsIHByb2R1Y3QucHJvZHVjdElkKTtcbiAgICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyTWFwLmxpc3RlZFByb2R1Y3RRdHlJbnB1dCkuZGF0YSgnYXR0cmlidXRlLWlkJywgcHJvZHVjdC5hdHRyaWJ1dGVJZCk7XG4gICAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5saXN0ZWRQcm9kdWN0UXR5SW5wdXQpLmRhdGEoJ2N1c3RvbWl6YXRpb24taWQnLCBjdXN0b21pemF0aW9uSWQpO1xuICAgICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAubGlzdGVkUHJvZHVjdFF0eUlucHV0KS5kYXRhKCdwcmV2LXF0eScsIHByb2R1Y3QucXVhbnRpdHkpO1xuICAgICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAucHJvZHVjdFRvdGFsUHJpY2VGaWVsZCkudGV4dChwcm9kdWN0LnByaWNlKTtcbiAgICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyTWFwLnByb2R1Y3RSZW1vdmVCdG4pLmRhdGEoJ3Byb2R1Y3QtaWQnLCBwcm9kdWN0LnByb2R1Y3RJZCk7XG4gICAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5wcm9kdWN0UmVtb3ZlQnRuKS5kYXRhKCdhdHRyaWJ1dGUtaWQnLCBwcm9kdWN0LmF0dHJpYnV0ZUlkKTtcbiAgICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyTWFwLnByb2R1Y3RSZW1vdmVCdG4pLmRhdGEoJ2N1c3RvbWl6YXRpb24taWQnLCBjdXN0b21pemF0aW9uSWQpO1xuXG4gICAgICB0aGlzLiRwcm9kdWN0c1RhYmxlLmZpbmQoJ3Rib2R5JykuYXBwZW5kKCR0ZW1wbGF0ZSk7XG4gICAgfVxuXG4gICAgdGhpcy5fc2hvd1RheFdhcm5pbmcoKTtcbiAgICB0aGlzLl9zaG93UHJvZHVjdHNMaXN0KCk7XG4gIH1cblxuICAvKipcbiAgICogUmVuZGVycyBjdXN0b21pemF0aW9uIGRhdGEgZm9yIGxpc3RlZCBwcm9kdWN0XG4gICAqXG4gICAqIEBwYXJhbSBjdXN0b21pemF0aW9uXG4gICAqIEBwYXJhbSAkcHJvZHVjdFJvd1RlbXBsYXRlXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfcmVuZGVyTGlzdGVkUHJvZHVjdEN1c3RvbWl6YXRpb24oY3VzdG9taXphdGlvbiwgJHByb2R1Y3RSb3dUZW1wbGF0ZSkge1xuICAgIGNvbnN0ICRjdXN0b21pemVkVGV4dFRlbXBsYXRlID0gJCgkKGNyZWF0ZU9yZGVyTWFwLmxpc3RlZFByb2R1Y3RDdXN0b21pemVkVGV4dFRlbXBsYXRlKS5odG1sKCkpO1xuICAgIGNvbnN0ICRjdXN0b21pemVkRmlsZVRlbXBsYXRlID0gJCgkKGNyZWF0ZU9yZGVyTWFwLmxpc3RlZFByb2R1Y3RDdXN0b21pemVkRmlsZVRlbXBsYXRlKS5odG1sKCkpO1xuXG4gICAgZm9yIChjb25zdCBrZXkgaW4gY3VzdG9taXphdGlvbi5jdXN0b21pemF0aW9uRmllbGRzRGF0YSkge1xuICAgICAgY29uc3QgY3VzdG9taXplZERhdGEgPSAgY3VzdG9taXphdGlvbi5jdXN0b21pemF0aW9uRmllbGRzRGF0YVtrZXldO1xuXG4gICAgICBsZXQgJGN1c3RvbWl6YXRpb25UZW1wbGF0ZSA9ICRjdXN0b21pemVkVGV4dFRlbXBsYXRlLmNsb25lKCk7XG5cbiAgICAgIGlmIChjdXN0b21pemVkRGF0YS50eXBlID09PSBjcmVhdGVPcmRlck1hcC5wcm9kdWN0Q3VzdG9taXphdGlvbkZpZWxkVHlwZUZpbGUpIHtcbiAgICAgICAgJGN1c3RvbWl6YXRpb25UZW1wbGF0ZSA9ICRjdXN0b21pemVkRmlsZVRlbXBsYXRlLmNsb25lKCk7XG4gICAgICAgICRjdXN0b21pemF0aW9uVGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5saXN0ZWRQcm9kdWN0Q3VzdG9taXphdGlvbk5hbWUpLnRleHQoY3VzdG9taXplZERhdGEubmFtZSk7XG4gICAgICAgICRjdXN0b21pemF0aW9uVGVtcGxhdGVcbiAgICAgICAgICAuZmluZChgJHtjcmVhdGVPcmRlck1hcC5saXN0ZWRQcm9kdWN0Q3VzdG9taXphdGlvblZhbHVlfSBpbWdgKVxuICAgICAgICAgIC5wcm9wKCdzcmMnLCBjdXN0b21pemVkRGF0YS52YWx1ZSlcbiAgICAgICAgO1xuXG4gICAgICB9IGVsc2Uge1xuICAgICAgICAkY3VzdG9taXphdGlvblRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAubGlzdGVkUHJvZHVjdEN1c3RvbWl6YXRpb25OYW1lKS50ZXh0KGN1c3RvbWl6ZWREYXRhLm5hbWUpO1xuICAgICAgICAkY3VzdG9taXphdGlvblRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAubGlzdGVkUHJvZHVjdEN1c3RvbWl6YXRpb25WYWx1ZSkudGV4dChjdXN0b21pemVkRGF0YS52YWx1ZSk7XG4gICAgICB9XG5cbiAgICAgICRwcm9kdWN0Um93VGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5saXN0ZWRQcm9kdWN0RGVmaW5pdGlvbikuYXBwZW5kKCRjdXN0b21pemF0aW9uVGVtcGxhdGUpO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBSZW5kZXJzIGNhcnQgcHJvZHVjdHMgc2VhcmNoIHJlc3VsdHMgYmxvY2tcbiAgICpcbiAgICogQHBhcmFtIGZvdW5kUHJvZHVjdHNcbiAgICovXG4gIHJlbmRlclNlYXJjaFJlc3VsdHMoZm91bmRQcm9kdWN0cykge1xuICAgIHRoaXMuX2NsZWFuU2VhcmNoUmVzdWx0cygpO1xuICAgIGlmIChmb3VuZFByb2R1Y3RzLmxlbmd0aCA9PT0gMCkge1xuICAgICAgdGhpcy5fc2hvd05vdEZvdW5kKCk7XG4gICAgICB0aGlzLl9oaWRlVGF4V2FybmluZygpO1xuXG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgdGhpcy5fcmVuZGVyRm91bmRQcm9kdWN0cyhmb3VuZFByb2R1Y3RzKTtcblxuICAgIHRoaXMuX2hpZGVOb3RGb3VuZCgpO1xuICAgIHRoaXMuX3Nob3dUYXhXYXJuaW5nKCk7XG4gICAgdGhpcy5fc2hvd1Jlc3VsdEJsb2NrKCk7XG4gIH1cblxuICByZXNldCgpIHtcbiAgICB0aGlzLl9jbGVhblNlYXJjaFJlc3VsdHMoKTtcbiAgICB0aGlzLl9oaWRlVGF4V2FybmluZygpO1xuICAgIHRoaXMuX2hpZGVSZXN1bHRCbG9jaygpO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlbmRlcnMgYXZhaWxhYmxlIGZpZWxkcyByZWxhdGVkIHRvIHNlbGVjdGVkIHByb2R1Y3RcbiAgICpcbiAgICogQHBhcmFtIHByb2R1Y3RcbiAgICovXG4gIHJlbmRlclByb2R1Y3RNZXRhZGF0YShwcm9kdWN0KSB7XG4gICAgdGhpcy5yZW5kZXJTdG9jayhwcm9kdWN0LnN0b2NrKTtcbiAgICB0aGlzLl9yZW5kZXJDb21iaW5hdGlvbnMocHJvZHVjdC5jb21iaW5hdGlvbnMpO1xuICAgIHRoaXMuX3JlbmRlckN1c3RvbWl6YXRpb25zKHByb2R1Y3QuY3VzdG9taXphdGlvbkZpZWxkcyk7XG4gIH1cblxuICAvKipcbiAgICogVXBkYXRlcyBzdG9jayB0ZXh0IGhlbHBlciB2YWx1ZVxuICAgKlxuICAgKiBAcGFyYW0gc3RvY2tcbiAgICovXG4gIHJlbmRlclN0b2NrKHN0b2NrKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5pblN0b2NrQ291bnRlcikudGV4dChzdG9jayk7XG4gICAgJChjcmVhdGVPcmRlck1hcC5xdWFudGl0eUlucHV0KS5hdHRyKCdtYXgnLCBzdG9jayk7XG4gIH1cblxuICAvKipcbiAgICogUmVuZGVycyBmb3VuZCBwcm9kdWN0cyBzZWxlY3RcbiAgICpcbiAgICogQHBhcmFtIGZvdW5kUHJvZHVjdHNcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9yZW5kZXJGb3VuZFByb2R1Y3RzKGZvdW5kUHJvZHVjdHMpIHtcbiAgICBmb3IgKGNvbnN0IGtleSBpbiBmb3VuZFByb2R1Y3RzKSB7XG4gICAgICBjb25zdCBwcm9kdWN0ID0gZm91bmRQcm9kdWN0c1trZXldO1xuXG4gICAgICBsZXQgbmFtZSA9IHByb2R1Y3QubmFtZTtcbiAgICAgIGlmIChwcm9kdWN0LmNvbWJpbmF0aW9ucy5sZW5ndGggPT09IDApIHtcbiAgICAgICAgbmFtZSArPSBgIC0gJHtwcm9kdWN0LmZvcm1hdHRlZFByaWNlfWA7XG4gICAgICB9XG5cbiAgICAgICQoY3JlYXRlT3JkZXJNYXAucHJvZHVjdFNlbGVjdCkuYXBwZW5kKGA8b3B0aW9uIHZhbHVlPVwiJHtwcm9kdWN0LnByb2R1Y3RJZH1cIj4ke25hbWV9PC9vcHRpb24+YCk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIENsZWFucyBwcm9kdWN0IHNlYXJjaCByZXN1bHQgZmllbGRzXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfY2xlYW5TZWFyY2hSZXN1bHRzKCkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAucHJvZHVjdFNlbGVjdCkuZW1wdHkoKTtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLmNvbWJpbmF0aW9uc1NlbGVjdCkuZW1wdHkoKTtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLnF1YW50aXR5SW5wdXQpLmVtcHR5KCk7XG4gIH1cblxuICAvKipcbiAgICogUmVuZGVycyBjb21iaW5hdGlvbnMgcm93IHdpdGggc2VsZWN0IG9wdGlvbnNcbiAgICpcbiAgICogQHBhcmFtIHtBcnJheX0gY29tYmluYXRpb25zXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfcmVuZGVyQ29tYmluYXRpb25zKGNvbWJpbmF0aW9ucykge1xuICAgIHRoaXMuX2NsZWFuQ29tYmluYXRpb25zKCk7XG5cbiAgICBpZiAoY29tYmluYXRpb25zLmxlbmd0aCA9PT0gMCkge1xuICAgICAgdGhpcy5faGlkZUNvbWJpbmF0aW9ucygpO1xuXG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgZm9yIChjb25zdCBrZXkgaW4gY29tYmluYXRpb25zKSB7XG4gICAgICBjb25zdCBjb21iaW5hdGlvbiA9IGNvbWJpbmF0aW9uc1trZXldO1xuXG4gICAgICAkKGNyZWF0ZU9yZGVyTWFwLmNvbWJpbmF0aW9uc1NlbGVjdCkuYXBwZW5kKFxuICAgICAgICBgPG9wdGlvblxuICAgICAgICAgIHZhbHVlPVwiJHtjb21iaW5hdGlvbi5hdHRyaWJ1dGVDb21iaW5hdGlvbklkfVwiPlxuICAgICAgICAgICR7Y29tYmluYXRpb24uYXR0cmlidXRlfSAtICR7Y29tYmluYXRpb24uZm9ybWF0dGVkUHJpY2V9XG4gICAgICAgIDwvb3B0aW9uPmAsXG4gICAgICApO1xuICAgIH1cblxuICAgIHRoaXMuX3Nob3dDb21iaW5hdGlvbnMoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZXNvbHZlcyB3ZWF0aGVyIHRvIGFkZCBjdXN0b21pemF0aW9uIGZpZWxkcyB0byByZXN1bHQgYmxvY2sgYW5kIGFkZHMgdGhlbSBpZiBuZWVkZWRcbiAgICpcbiAgICogQHBhcmFtIGN1c3RvbWl6YXRpb25GaWVsZHNcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9yZW5kZXJDdXN0b21pemF0aW9ucyhjdXN0b21pemF0aW9uRmllbGRzKSB7XG4gICAgLy8gcmVwcmVzZW50cyBjdXN0b21pemF0aW9uIGZpZWxkIHR5cGUgXCJmaWxlXCIuXG4gICAgY29uc3QgZmllbGRUeXBlRmlsZSA9IGNyZWF0ZU9yZGVyTWFwLnByb2R1Y3RDdXN0b21pemF0aW9uRmllbGRUeXBlRmlsZTtcbiAgICAvLyByZXByZXNlbnRzIGN1c3RvbWl6YXRpb24gZmllbGQgdHlwZSBcInRleHRcIi5cbiAgICBjb25zdCBmaWVsZFR5cGVUZXh0ID0gY3JlYXRlT3JkZXJNYXAucHJvZHVjdEN1c3RvbWl6YXRpb25GaWVsZFR5cGVUZXh0O1xuXG4gICAgdGhpcy5fY2xlYW5DdXN0b21pemF0aW9ucygpO1xuICAgIGlmIChjdXN0b21pemF0aW9uRmllbGRzLmxlbmd0aCA9PT0gMCkge1xuICAgICAgdGhpcy5faGlkZUN1c3RvbWl6YXRpb25zKCk7XG5cbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBjb25zdCAkY3VzdG9tRmllbGRzQ29udGFpbmVyID0gJChjcmVhdGVPcmRlck1hcC5wcm9kdWN0Q3VzdG9tRmllbGRzQ29udGFpbmVyKTtcbiAgICBjb25zdCAkZmlsZUlucHV0VGVtcGxhdGUgPSAkKCQoY3JlYXRlT3JkZXJNYXAucHJvZHVjdEN1c3RvbUZpbGVUZW1wbGF0ZSkuaHRtbCgpKTtcbiAgICBjb25zdCAkdGV4dElucHV0VGVtcGxhdGUgPSAkKCQoY3JlYXRlT3JkZXJNYXAucHJvZHVjdEN1c3RvbVRleHRUZW1wbGF0ZSkuaHRtbCgpKTtcblxuICAgIGNvbnN0IHRlbXBsYXRlVHlwZU1hcCA9IHtcbiAgICAgIFtmaWVsZFR5cGVGaWxlXTogJGZpbGVJbnB1dFRlbXBsYXRlLFxuICAgICAgW2ZpZWxkVHlwZVRleHRdOiAkdGV4dElucHV0VGVtcGxhdGUsXG4gICAgfTtcblxuICAgIGZvciAoY29uc3Qga2V5IGluIGN1c3RvbWl6YXRpb25GaWVsZHMpIHtcbiAgICAgIGNvbnN0IGN1c3RvbUZpZWxkID0gY3VzdG9taXphdGlvbkZpZWxkc1trZXldO1xuICAgICAgY29uc3QgJHRlbXBsYXRlID0gdGVtcGxhdGVUeXBlTWFwW2N1c3RvbUZpZWxkLnR5cGVdLmNsb25lKCk7XG5cbiAgICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyTWFwLnByb2R1Y3RDdXN0b21JbnB1dClcbiAgICAgICAgLmF0dHIoJ25hbWUnLCBgY3VzdG9taXphdGlvbnNbJHtjdXN0b21GaWVsZC5jdXN0b21pemF0aW9uRmllbGRJZH1dYClcbiAgICAgICAgLmRhdGEoJ2N1c3RvbWl6YXRpb24tZmllbGQtaWQnLCBjdXN0b21GaWVsZC5jdXN0b21pemF0aW9uRmllbGRJZCk7XG4gICAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5wcm9kdWN0Q3VzdG9tSW5wdXRMYWJlbClcbiAgICAgICAgLmF0dHIoJ2ZvcicsIGBjdXN0b21pemF0aW9uc1ske2N1c3RvbUZpZWxkLmN1c3RvbWl6YXRpb25GaWVsZElkfV1gKVxuICAgICAgICAudGV4dChjdXN0b21GaWVsZC5uYW1lKTtcblxuICAgICAgaWYgKGN1c3RvbUZpZWxkLnJlcXVpcmVkID09PSB0cnVlKSB7XG4gICAgICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyTWFwLnJlcXVpcmVkRmllbGRNYXJrKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgICB9XG5cbiAgICAgICRjdXN0b21GaWVsZHNDb250YWluZXIuYXBwZW5kKCR0ZW1wbGF0ZSk7XG4gICAgfVxuXG4gICAgdGhpcy5fc2hvd0N1c3RvbWl6YXRpb25zKCk7XG4gIH1cblxuICAvKipcbiAgICogUmVuZGVycyBlcnJvciBhbGVydCBmb3IgY2FydCBibG9ja1xuICAgKlxuICAgKiBAcGFyYW0gbWVzc2FnZVxuICAgKi9cbiAgcmVuZGVyQ2FydEJsb2NrRXJyb3JBbGVydChtZXNzYWdlKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5jYXJ0RXJyb3JBbGVydFRleHQpLnRleHQobWVzc2FnZSk7XG4gICAgdGhpcy5fc2hvd0NhcnRCbG9ja0Vycm9yKClcbiAgfVxuXG4gIC8qKlxuICAgKiBDbGVhbnMgY2FydCBibG9jayBhbGVydHMgY29udGVudCBhbmQgaGlkZXMgdGhlbVxuICAgKi9cbiAgY2xlYW5DYXJ0QmxvY2tBbGVydHMoKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5jYXJ0RXJyb3JBbGVydFRleHQpLnRleHQoJycpO1xuICAgIHRoaXMuX2hpZGVDYXJ0QmxvY2tFcnJvcigpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNob3dzIGVycm9yIGFsZXJ0IGJsb2NrIG9mIGNhcnQgYmxvY2tcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zaG93Q2FydEJsb2NrRXJyb3IoKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5jYXJ0RXJyb3JBbGVydEJsb2NrKS5yZW1vdmVDbGFzcygnZC1ub25lJylcbiAgfVxuXG4gIC8qKlxuICAgKiBIaWRlcyBlcnJvciBhbGVydCBibG9jayBvZiBjYXJ0IGJsb2NrXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGlkZUNhcnRCbG9ja0Vycm9yKCkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAuY2FydEVycm9yQWxlcnRCbG9jaykuYWRkQ2xhc3MoJ2Qtbm9uZScpXG4gIH1cblxuICAvKipcbiAgICogU2hvd3MgcHJvZHVjdCBjdXN0b21pemF0aW9uIGNvbnRhaW5lclxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3Nob3dDdXN0b21pemF0aW9ucygpIHtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLnByb2R1Y3RDdXN0b21pemF0aW9uQ29udGFpbmVyKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZXMgcHJvZHVjdCBjdXN0b21pemF0aW9uIGNvbnRhaW5lclxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hpZGVDdXN0b21pemF0aW9ucygpIHtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLnByb2R1Y3RDdXN0b21pemF0aW9uQ29udGFpbmVyKS5hZGRDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogRW1wdGllcyBjdXN0b21pemF0aW9uIGZpZWxkcyBjb250YWluZXJcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9jbGVhbkN1c3RvbWl6YXRpb25zKCkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAucHJvZHVjdEN1c3RvbUZpZWxkc0NvbnRhaW5lcikuZW1wdHkoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93cyByZXN1bHQgYmxvY2tcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zaG93UmVzdWx0QmxvY2soKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5wcm9kdWN0UmVzdWx0QmxvY2spLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIaWRlcyByZXN1bHQgYmxvY2tcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oaWRlUmVzdWx0QmxvY2soKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5wcm9kdWN0UmVzdWx0QmxvY2spLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG5cbiAgLyoqXG4gICAqIFNob3dzIHByb2R1Y3RzIGxpc3RcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zaG93UHJvZHVjdHNMaXN0KCkge1xuICAgIHRoaXMuJHByb2R1Y3RzVGFibGUucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhpZGVzIHByb2R1Y3RzIGxpc3RcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oaWRlUHJvZHVjdHNMaXN0KCkge1xuICAgIHRoaXMuJHByb2R1Y3RzVGFibGUuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIEVtcHRpZXMgcHJvZHVjdHMgbGlzdFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2NsZWFuUHJvZHVjdHNMaXN0KCkge1xuICAgIHRoaXMuJHByb2R1Y3RzVGFibGUuZmluZCgndGJvZHknKS5lbXB0eSgpO1xuICB9XG5cbiAgLyoqXG4gICAqIEVtcHRpZXMgY29tYmluYXRpb25zIHNlbGVjdFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2NsZWFuQ29tYmluYXRpb25zKCkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAuY29tYmluYXRpb25zU2VsZWN0KS5lbXB0eSgpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNob3dzIGNvbWJpbmF0aW9ucyByb3dcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zaG93Q29tYmluYXRpb25zKCkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAuY29tYmluYXRpb25zUm93KS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZXMgY29tYmluYXRpb25zIHJvd1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hpZGVDb21iaW5hdGlvbnMoKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5jb21iaW5hdGlvbnNSb3cpLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93cyB3YXJuaW5nIG9mIHRheCBpbmNsdWRlZC9leGNsdWRlZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3Nob3dUYXhXYXJuaW5nKCkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAucHJvZHVjdFRheFdhcm5pbmcpLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIaWRlcyB3YXJuaW5nIG9mIHRheCBpbmNsdWRlZC9leGNsdWRlZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hpZGVUYXhXYXJuaW5nKCkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAucHJvZHVjdFRheFdhcm5pbmcpLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93cyBwcm9kdWN0IG5vdCBmb3VuZCB3YXJuaW5nXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2hvd05vdEZvdW5kKCkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAubm9Qcm9kdWN0c0ZvdW5kV2FybmluZykucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhpZGVzIHByb2R1Y3Qgbm90IGZvdW5kIHdhcm5pbmdcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oaWRlTm90Rm91bmQoKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5ub1Byb2R1Y3RzRm91bmRXYXJuaW5nKS5hZGRDbGFzcygnZC1ub25lJyk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9wcm9kdWN0LXJlbmRlcmVyLmpzIiwiLyoqXG4gKiAyMDA3LTIwMjAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAyMCBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IGNyZWF0ZU9yZGVyTWFwIGZyb20gJy4vY3JlYXRlLW9yZGVyLW1hcCc7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBNYW5pcHVsYXRlcyBVSSBvZiBTaGlwcGluZyBibG9jayBpbiBPcmRlciBjcmVhdGlvbiBwYWdlXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFNoaXBwaW5nUmVuZGVyZXIge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLiRjb250YWluZXIgPSAkKGNyZWF0ZU9yZGVyTWFwLnNoaXBwaW5nQmxvY2spO1xuICAgIHRoaXMuJGZvcm0gPSAkKGNyZWF0ZU9yZGVyTWFwLnNoaXBwaW5nRm9ybSk7XG4gICAgdGhpcy4kbm9DYXJyaWVyQmxvY2sgPSAkKGNyZWF0ZU9yZGVyTWFwLm5vQ2FycmllckJsb2NrKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcGFyYW0ge09iamVjdH0gc2hpcHBpbmdcbiAgICogQHBhcmFtIHtCb29sZWFufSBlbXB0eUNhcnRcbiAgICovXG4gIHJlbmRlcihzaGlwcGluZywgZW1wdHlDYXJ0KSB7XG4gICAgaWYgKGVtcHR5Q2FydCkge1xuICAgICAgdGhpcy5faGlkZUNvbnRhaW5lcigpO1xuICAgIH0gZWxzZSBpZiAoc2hpcHBpbmcgIT09IG51bGwpIHtcbiAgICAgIHRoaXMuX2Rpc3BsYXlGb3JtKHNoaXBwaW5nKTtcbiAgICB9IGVsc2Uge1xuICAgICAgdGhpcy5fZGlzcGxheU5vQ2FycmllcnNXYXJuaW5nKCk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIFNob3cgZm9ybSBibG9jayB3aXRoIHJlbmRlcmVkIGRlbGl2ZXJ5IG9wdGlvbnMgaW5zdGVhZCBvZiB3YXJuaW5nIG1lc3NhZ2VcbiAgICpcbiAgICogQHBhcmFtIHNoaXBwaW5nXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfZGlzcGxheUZvcm0oc2hpcHBpbmcpIHtcbiAgICB0aGlzLl9oaWRlTm9DYXJyaWVyQmxvY2soKTtcbiAgICB0aGlzLl9yZW5kZXJEZWxpdmVyeU9wdGlvbnMoc2hpcHBpbmcuZGVsaXZlcnlPcHRpb25zLCBzaGlwcGluZy5zZWxlY3RlZENhcnJpZXJJZCk7XG4gICAgdGhpcy5fcmVuZGVyVG90YWxTaGlwcGluZyhzaGlwcGluZy5zaGlwcGluZ1ByaWNlKTtcbiAgICB0aGlzLl9yZW5kZXJGcmVlU2hpcHBpbmdTd2l0Y2goc2hpcHBpbmcuZnJlZVNoaXBwaW5nKTtcbiAgICB0aGlzLl9zaG93Rm9ybSgpO1xuICAgIHRoaXMuX3Nob3dDb250YWluZXIoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZW5kZXJzIGZyZWUgc2hpcHBpbmcgc3dpdGNoIGRlcGVuZGluZyBvbiBmcmVlIHNoaXBwaW5nIHZhbHVlXG4gICAqXG4gICAqIEBwYXJhbSBpc0ZyZWVTaGlwcGluZ1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3JlbmRlckZyZWVTaGlwcGluZ1N3aXRjaChpc0ZyZWVTaGlwcGluZykge1xuICAgICQoY3JlYXRlT3JkZXJNYXAuZnJlZVNoaXBwaW5nU3dpdGNoKS5lYWNoKChrZXksIGlucHV0KSA9PiB7XG4gICAgICBpZiAoaW5wdXQudmFsdWUgPT09ICcxJykge1xuICAgICAgICBpbnB1dC5jaGVja2VkID0gaXNGcmVlU2hpcHBpbmc7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBpbnB1dC5jaGVja2VkID0gIWlzRnJlZVNoaXBwaW5nO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIFNob3cgd2FybmluZyBtZXNzYWdlIHRoYXQgbm8gY2FycmllcnMgYXJlIGF2YWlsYWJsZSBhbmQgaGlkZSBmb3JtIGJsb2NrXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfZGlzcGxheU5vQ2FycmllcnNXYXJuaW5nKCkge1xuICAgIHRoaXMuX3Nob3dDb250YWluZXIoKTtcbiAgICB0aGlzLl9oaWRlRm9ybSgpO1xuICAgIHRoaXMuX3Nob3dOb0NhcnJpZXJCbG9jaygpO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlbmRlcnMgZGVsaXZlcnkgb3B0aW9ucyBzZWxlY3Rpb24gYmxvY2tcbiAgICpcbiAgICogQHBhcmFtIGRlbGl2ZXJ5T3B0aW9uc1xuICAgKiBAcGFyYW0gc2VsZWN0ZWRWYWxcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9yZW5kZXJEZWxpdmVyeU9wdGlvbnMoZGVsaXZlcnlPcHRpb25zLCBzZWxlY3RlZFZhbCkge1xuICAgIGNvbnN0ICRkZWxpdmVyeU9wdGlvblNlbGVjdCA9ICQoY3JlYXRlT3JkZXJNYXAuZGVsaXZlcnlPcHRpb25TZWxlY3QpO1xuICAgICRkZWxpdmVyeU9wdGlvblNlbGVjdC5lbXB0eSgpO1xuXG4gICAgZm9yIChjb25zdCBrZXkgaW4gT2JqZWN0LmtleXMoZGVsaXZlcnlPcHRpb25zKSkge1xuICAgICAgY29uc3Qgb3B0aW9uID0gZGVsaXZlcnlPcHRpb25zW2tleV07XG5cbiAgICAgIGNvbnN0IGRlbGl2ZXJ5T3B0aW9uID0ge1xuICAgICAgICB2YWx1ZTogb3B0aW9uLmNhcnJpZXJJZCxcbiAgICAgICAgdGV4dDogYCR7b3B0aW9uLmNhcnJpZXJOYW1lfSAtICR7b3B0aW9uLmNhcnJpZXJEZWxheX1gLFxuICAgICAgfTtcblxuICAgICAgaWYgKHNlbGVjdGVkVmFsID09PSBkZWxpdmVyeU9wdGlvbi52YWx1ZSkge1xuICAgICAgICBkZWxpdmVyeU9wdGlvbi5zZWxlY3RlZCA9ICdzZWxlY3RlZCc7XG4gICAgICB9XG5cbiAgICAgICRkZWxpdmVyeU9wdGlvblNlbGVjdC5hcHBlbmQoJCgnPG9wdGlvbj4nLCBkZWxpdmVyeU9wdGlvbikpO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBSZW5kZXJzIGR5bmFtaWMgdmFsdWUgb2Ygc2hpcHBpbmcgcHJpY2VcbiAgICpcbiAgICogQHBhcmFtIHNoaXBwaW5nUHJpY2VcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9yZW5kZXJUb3RhbFNoaXBwaW5nKHNoaXBwaW5nUHJpY2UpIHtcbiAgICBjb25zdCAkdG90YWxTaGlwcGluZ0ZpZWxkID0gJChjcmVhdGVPcmRlck1hcC50b3RhbFNoaXBwaW5nRmllbGQpO1xuICAgICR0b3RhbFNoaXBwaW5nRmllbGQuZW1wdHkoKTtcblxuICAgICR0b3RhbFNoaXBwaW5nRmllbGQuYXBwZW5kKHNoaXBwaW5nUHJpY2UpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNob3cgd2hvbGUgc2hpcHBpbmcgY29udGFpbmVyXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2hvd0NvbnRhaW5lcigpIHtcbiAgICB0aGlzLiRjb250YWluZXIucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhpZGUgd2hvbGUgc2hpcHBpbmcgY29udGFpbmVyXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGlkZUNvbnRhaW5lcigpIHtcbiAgICB0aGlzLiRjb250YWluZXIuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNob3cgZm9ybSBibG9ja1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3Nob3dGb3JtKCkge1xuICAgIHRoaXMuJGZvcm0ucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhpZGUgZm9ybSBibG9ja1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hpZGVGb3JtKCkge1xuICAgIHRoaXMuJGZvcm0uYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNob3cgd2FybmluZyBtZXNzYWdlIGJsb2NrIHdoaWNoIHdhcm5zIHRoYXQgbm8gY2FycmllcnMgYXJlIGF2YWlsYWJsZVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3Nob3dOb0NhcnJpZXJCbG9jaygpIHtcbiAgICB0aGlzLiRub0NhcnJpZXJCbG9jay5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZSB3YXJuaW5nIG1lc3NhZ2UgYmxvY2sgd2hpY2ggd2FybnMgdGhhdCBubyBjYXJyaWVycyBhcmUgYXZhaWxhYmxlXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGlkZU5vQ2FycmllckJsb2NrKCkge1xuICAgIHRoaXMuJG5vQ2FycmllckJsb2NrLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvY3JlYXRlL3NoaXBwaW5nLXJlbmRlcmVyLmpzIiwidmFyIGhhc093blByb3BlcnR5ID0ge30uaGFzT3duUHJvcGVydHk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0LCBrZXkpe1xuICByZXR1cm4gaGFzT3duUHJvcGVydHkuY2FsbChpdCwga2V5KTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19oYXMuanNcbi8vIG1vZHVsZSBpZCA9IDI3XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxOCAxOSAyMCIsInZhciBjb3JlID0gbW9kdWxlLmV4cG9ydHMgPSB7dmVyc2lvbjogJzIuNC4wJ307XG5pZih0eXBlb2YgX19lID09ICdudW1iZXInKV9fZSA9IGNvcmU7IC8vIGVzbGludC1kaXNhYmxlLWxpbmUgbm8tdW5kZWZcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2NvcmUuanNcbi8vIG1vZHVsZSBpZCA9IDNcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiLy8gMTkuMS4yLjE0IC8gMTUuMi4zLjE0IE9iamVjdC5rZXlzKE8pXG52YXIgJGtleXMgICAgICAgPSByZXF1aXJlKCcuL19vYmplY3Qta2V5cy1pbnRlcm5hbCcpXG4gICwgZW51bUJ1Z0tleXMgPSByZXF1aXJlKCcuL19lbnVtLWJ1Zy1rZXlzJyk7XG5cbm1vZHVsZS5leHBvcnRzID0gT2JqZWN0LmtleXMgfHwgZnVuY3Rpb24ga2V5cyhPKXtcbiAgcmV0dXJuICRrZXlzKE8sIGVudW1CdWdLZXlzKTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3Qta2V5cy5qc1xuLy8gbW9kdWxlIGlkID0gMzRcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwiLyoqXG4gKiAyMDA3LTIwMjAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAyMCBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IEV2ZW50RW1pdHRlckNsYXNzIGZyb20gJ2V2ZW50cyc7XG5cbi8qKlxuICogV2UgaW5zdGFuY2lhdGUgb25lIEV2ZW50RW1pdHRlciAocmVzdHJpY3RlZCB2aWEgYSBjb25zdCkgc28gdGhhdCBldmVyeSBjb21wb25lbnRzXG4gKiByZWdpc3Rlci9kaXNwYXRjaCBvbiB0aGUgc2FtZSBvbmUgYW5kIGNhbiBjb21tdW5pY2F0ZSB3aXRoIGVhY2ggb3RoZXIuXG4gKi9cbmV4cG9ydCBjb25zdCBFdmVudEVtaXR0ZXIgPSBuZXcgRXZlbnRFbWl0dGVyQ2xhc3MoKTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZXZlbnQtZW1pdHRlci5qcyIsIi8vIDcuMi4xIFJlcXVpcmVPYmplY3RDb2VyY2libGUoYXJndW1lbnQpXG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgaWYoaXQgPT0gdW5kZWZpbmVkKXRocm93IFR5cGVFcnJvcihcIkNhbid0IGNhbGwgbWV0aG9kIG9uICBcIiArIGl0KTtcbiAgcmV0dXJuIGl0O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2RlZmluZWQuanNcbi8vIG1vZHVsZSBpZCA9IDM4XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxOCAxOSAyMCIsIi8vIDcuMS40IFRvSW50ZWdlclxudmFyIGNlaWwgID0gTWF0aC5jZWlsXG4gICwgZmxvb3IgPSBNYXRoLmZsb29yO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCl7XG4gIHJldHVybiBpc05hTihpdCA9ICtpdCkgPyAwIDogKGl0ID4gMCA/IGZsb29yIDogY2VpbCkoaXQpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3RvLWludGVnZXIuanNcbi8vIG1vZHVsZSBpZCA9IDM5XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxOCAxOSAyMCIsIm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXQpe1xuICByZXR1cm4gdHlwZW9mIGl0ID09PSAnb2JqZWN0JyA/IGl0ICE9PSBudWxsIDogdHlwZW9mIGl0ID09PSAnZnVuY3Rpb24nO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2lzLW9iamVjdC5qc1xuLy8gbW9kdWxlIGlkID0gNFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJ2YXIgaWQgPSAwXG4gICwgcHggPSBNYXRoLnJhbmRvbSgpO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihrZXkpe1xuICByZXR1cm4gJ1N5bWJvbCgnLmNvbmNhdChrZXkgPT09IHVuZGVmaW5lZCA/ICcnIDoga2V5LCAnKV8nLCAoKytpZCArIHB4KS50b1N0cmluZygzNikpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3VpZC5qc1xuLy8gbW9kdWxlIGlkID0gNDNcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwiLy8gNy4xLjEzIFRvT2JqZWN0KGFyZ3VtZW50KVxudmFyIGRlZmluZWQgPSByZXF1aXJlKCcuL19kZWZpbmVkJyk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgcmV0dXJuIE9iamVjdChkZWZpbmVkKGl0KSk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fdG8tb2JqZWN0LmpzXG4vLyBtb2R1bGUgaWQgPSA0NVxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTggMTkgMjAiLCJ2YXIgc2hhcmVkID0gcmVxdWlyZSgnLi9fc2hhcmVkJykoJ2tleXMnKVxuICAsIHVpZCAgICA9IHJlcXVpcmUoJy4vX3VpZCcpO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihrZXkpe1xuICByZXR1cm4gc2hhcmVkW2tleV0gfHwgKHNoYXJlZFtrZXldID0gdWlkKGtleSkpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3NoYXJlZC1rZXkuanNcbi8vIG1vZHVsZSBpZCA9IDQ2XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxOCAxOSAyMCIsInZhciB0b1N0cmluZyA9IHt9LnRvU3RyaW5nO1xuXG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgcmV0dXJuIHRvU3RyaW5nLmNhbGwoaXQpLnNsaWNlKDgsIC0xKTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19jb2YuanNcbi8vIG1vZHVsZSBpZCA9IDQ3XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxOCAxOSAyMCIsIi8vIElFIDgtIGRvbid0IGVudW0gYnVnIGtleXNcbm1vZHVsZS5leHBvcnRzID0gKFxuICAnY29uc3RydWN0b3IsaGFzT3duUHJvcGVydHksaXNQcm90b3R5cGVPZixwcm9wZXJ0eUlzRW51bWVyYWJsZSx0b0xvY2FsZVN0cmluZyx0b1N0cmluZyx2YWx1ZU9mJ1xuKS5zcGxpdCgnLCcpO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZW51bS1idWcta2V5cy5qc1xuLy8gbW9kdWxlIGlkID0gNDhcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwidmFyIGdsb2JhbCA9IHJlcXVpcmUoJy4vX2dsb2JhbCcpXG4gICwgU0hBUkVEID0gJ19fY29yZS1qc19zaGFyZWRfXydcbiAgLCBzdG9yZSAgPSBnbG9iYWxbU0hBUkVEXSB8fCAoZ2xvYmFsW1NIQVJFRF0gPSB7fSk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGtleSl7XG4gIHJldHVybiBzdG9yZVtrZXldIHx8IChzdG9yZVtrZXldID0ge30pO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3NoYXJlZC5qc1xuLy8gbW9kdWxlIGlkID0gNDlcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwiLy8gaHR0cHM6Ly9naXRodWIuY29tL3psb2lyb2NrL2NvcmUtanMvaXNzdWVzLzg2I2lzc3VlY29tbWVudC0xMTU3NTkwMjhcbnZhciBnbG9iYWwgPSBtb2R1bGUuZXhwb3J0cyA9IHR5cGVvZiB3aW5kb3cgIT0gJ3VuZGVmaW5lZCcgJiYgd2luZG93Lk1hdGggPT0gTWF0aFxuICA/IHdpbmRvdyA6IHR5cGVvZiBzZWxmICE9ICd1bmRlZmluZWQnICYmIHNlbGYuTWF0aCA9PSBNYXRoID8gc2VsZiA6IEZ1bmN0aW9uKCdyZXR1cm4gdGhpcycpKCk7XG5pZih0eXBlb2YgX19nID09ICdudW1iZXInKV9fZyA9IGdsb2JhbDsgLy8gZXNsaW50LWRpc2FibGUtbGluZSBuby11bmRlZlxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZ2xvYmFsLmpzXG4vLyBtb2R1bGUgaWQgPSA1XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIi8qKlxuICogMjAwNy0yMDIwIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMjAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5pbXBvcnQgQ3JlYXRlT3JkZXJQYWdlIGZyb20gJy4vY3JlYXRlL2NyZWF0ZS1vcmRlci1wYWdlJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xubGV0IG9yZGVyUGFnZU1hbmFnZXIgPSBudWxsO1xuXG4vKipcbiAqIHByb3h5IHRvIGFsbG93IG90aGVyIHNjcmlwdHMgd2l0aGluIHRoZSBwYWdlIHRvIHRyaWdnZXIgdGhlIHNlYXJjaFxuICogQHBhcmFtIHN0cmluZ1xuICovXG5mdW5jdGlvbiBzZWFyY2hDdXN0b21lckJ5U3RyaW5nKHN0cmluZykge1xuICBpZiAob3JkZXJQYWdlTWFuYWdlciAhPT0gbnVsbCkge1xuICAgIG9yZGVyUGFnZU1hbmFnZXIuc2VhcmNoKHN0cmluZyk7XG4gIH0gZWxzZSB7XG4gICAgY29uc29sZS5sb2coJ0Vycm9yOiBDb3VsZCBub3Qgc2VhcmNoIGN1c3RvbWVyIGFzIG9yZGVyUGFnZU1hbmFnZXIgaXMgbnVsbCcpO1xuICB9XG59XG5cbi8qKlxuICogcHJveHkgdG8gYWxsb3cgb3RoZXIgc2NyaXB0cyB3aXRoaW4gdGhlIHBhZ2UgdG8gcmVmcmVzaCBhZGRyZXNzZXMgbGlzdFxuICovXG5mdW5jdGlvbiByZWZyZXNoQWRkcmVzc2VzTGlzdChyZWZyZXNoQ2FydEFkZHJlc3Nlcykge1xuICBpZiAob3JkZXJQYWdlTWFuYWdlciAhPT0gbnVsbCkge1xuICAgIG9yZGVyUGFnZU1hbmFnZXIucmVmcmVzaEFkZHJlc3Nlc0xpc3QocmVmcmVzaENhcnRBZGRyZXNzZXMpO1xuICB9IGVsc2Uge1xuICAgIGNvbnNvbGUubG9nKCdFcnJvcjogQ291bGQgbm90IHJlZnJlc2ggYWRkcmVzc2VzIGxpc3QgYXMgb3JkZXJQYWdlTWFuYWdlciBpcyBudWxsJyk7XG4gIH1cbn1cblxuXG4kKGRvY3VtZW50KS5yZWFkeSgoKSA9PiB7XG4gIG9yZGVyUGFnZU1hbmFnZXIgPSBuZXcgQ3JlYXRlT3JkZXJQYWdlKCk7XG59KTtcblxuZXhwb3J0IHtzZWFyY2hDdXN0b21lckJ5U3RyaW5nfVxuZXhwb3J0IHtyZWZyZXNoQWRkcmVzc2VzTGlzdH1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS5qcyIsIi8qKlxuICogMjAwNy0yMDIwIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMjAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBjcmVhdGVPcmRlck1hcCBmcm9tICcuL2NyZWF0ZS1vcmRlci1tYXAnO1xuaW1wb3J0IFJvdXRlciBmcm9tICcuLi8uLi8uLi9jb21wb25lbnRzL3JvdXRlcic7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBSZW5kZXJzIERlbGl2ZXJ5ICYgSW52b2ljZSBhZGRyZXNzZXMgc2VsZWN0XG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEFkZHJlc3Nlc1JlbmRlcmVyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy5yb3V0ZXIgPSBuZXcgUm91dGVyKCk7XG4gIH1cbiAgLyoqXG4gICAqIEBwYXJhbSB7QXJyYXl9IGFkZHJlc3Nlc1xuICAgKi9cbiAgcmVuZGVyKGFkZHJlc3Nlcykge1xuICAgIHRoaXMuX2NsZWFuQWRkcmVzc2VzKCk7XG4gICAgaWYgKGFkZHJlc3Nlcy5sZW5ndGggPT09IDApIHtcbiAgICAgIHRoaXMuX2hpZGVBZGRyZXNzZXNDb250ZW50KCk7XG4gICAgICB0aGlzLl9zaG93RW1wdHlBZGRyZXNzZXNXYXJuaW5nKCk7XG4gICAgICB0aGlzLl9zaG93QWRkcmVzc2VzQmxvY2soKTtcblxuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIHRoaXMuX3Nob3dBZGRyZXNzZXNDb250ZW50KCk7XG4gICAgdGhpcy5faGlkZUVtcHR5QWRkcmVzc2VzV2FybmluZygpO1xuXG4gICAgZm9yIChjb25zdCBrZXkgaW4gYWRkcmVzc2VzKSB7XG4gICAgICBjb25zdCBhZGRyZXNzID0gYWRkcmVzc2VzW2tleV07XG5cbiAgICAgIHRoaXMuX3JlbmRlckRlbGl2ZXJ5QWRkcmVzcyhhZGRyZXNzKTtcbiAgICAgIHRoaXMuX3JlbmRlckludm9pY2VBZGRyZXNzKGFkZHJlc3MpO1xuICAgIH1cblxuICAgIHRoaXMuX3Nob3dBZGRyZXNzZXNCbG9jaygpO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlbmRlcnMgZGVsaXZlcnkgYWRkcmVzcyBjb250ZW50XG4gICAqXG4gICAqIEBwYXJhbSBhZGRyZXNzXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfcmVuZGVyRGVsaXZlcnlBZGRyZXNzKGFkZHJlc3MpIHtcbiAgICBjb25zdCBkZWxpdmVyeUFkZHJlc3NPcHRpb24gPSB7XG4gICAgICB2YWx1ZTogYWRkcmVzcy5hZGRyZXNzSWQsXG4gICAgICB0ZXh0OiBhZGRyZXNzLmFsaWFzLFxuICAgIH07XG5cbiAgICBpZiAoYWRkcmVzcy5kZWxpdmVyeSkge1xuICAgICAgJChjcmVhdGVPcmRlck1hcC5kZWxpdmVyeUFkZHJlc3NEZXRhaWxzKS5odG1sKGFkZHJlc3MuZm9ybWF0dGVkQWRkcmVzcyk7XG4gICAgICBkZWxpdmVyeUFkZHJlc3NPcHRpb24uc2VsZWN0ZWQgPSAnc2VsZWN0ZWQnO1xuICAgICAgJChjcmVhdGVPcmRlck1hcC5kZWxpdmVyeUFkZHJlc3NFZGl0QnRuKS5wcm9wKCdocmVmJywgdGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX2FkZHJlc3Nlc19lZGl0Jywge1xuICAgICAgICBhZGRyZXNzSWQ6IGFkZHJlc3MuYWRkcmVzc0lkLFxuICAgICAgICBsaXRlRGlzcGxheWluZzogMSxcbiAgICAgICAgc3VibWl0Rm9ybUFqYXg6IDEsXG4gICAgICB9KSk7XG4gICAgfVxuXG4gICAgJChjcmVhdGVPcmRlck1hcC5kZWxpdmVyeUFkZHJlc3NTZWxlY3QpLmFwcGVuZCgkKCc8b3B0aW9uPicsIGRlbGl2ZXJ5QWRkcmVzc09wdGlvbikpO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlbmRlcnMgaW52b2ljZSBhZGRyZXNzIGNvbnRlbnRcbiAgICpcbiAgICogQHBhcmFtIGFkZHJlc3NcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9yZW5kZXJJbnZvaWNlQWRkcmVzcyhhZGRyZXNzKSB7XG4gICAgY29uc3QgaW52b2ljZUFkZHJlc3NPcHRpb24gPSB7XG4gICAgICB2YWx1ZTogYWRkcmVzcy5hZGRyZXNzSWQsXG4gICAgICB0ZXh0OiBhZGRyZXNzLmFsaWFzLFxuICAgIH07XG5cbiAgICBpZiAoYWRkcmVzcy5pbnZvaWNlKSB7XG4gICAgICAkKGNyZWF0ZU9yZGVyTWFwLmludm9pY2VBZGRyZXNzRGV0YWlscykuaHRtbChhZGRyZXNzLmZvcm1hdHRlZEFkZHJlc3MpO1xuICAgICAgaW52b2ljZUFkZHJlc3NPcHRpb24uc2VsZWN0ZWQgPSAnc2VsZWN0ZWQnO1xuICAgICAgJChjcmVhdGVPcmRlck1hcC5pbnZvaWNlQWRkcmVzc0VkaXRCdG4pLnByb3AoJ2hyZWYnLCB0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fYWRkcmVzc2VzX2VkaXQnLCB7XG4gICAgICAgIGFkZHJlc3NJZDogYWRkcmVzcy5hZGRyZXNzSWQsXG4gICAgICAgIGxpdGVEaXNwbGF5aW5nOiAxLFxuICAgICAgICBzdWJtaXRGb3JtQWpheDogMSxcbiAgICAgIH0pKTtcbiAgICB9XG5cbiAgICAkKGNyZWF0ZU9yZGVyTWFwLmludm9pY2VBZGRyZXNzU2VsZWN0KS5hcHBlbmQoJCgnPG9wdGlvbj4nLCBpbnZvaWNlQWRkcmVzc09wdGlvbikpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNob3dzIGFkZHJlc3NlcyBibG9ja1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3Nob3dBZGRyZXNzZXNCbG9jaygpIHtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLmFkZHJlc3Nlc0Jsb2NrKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogRW1wdGllcyBhZGRyZXNzZXMgY29udGVudFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2NsZWFuQWRkcmVzc2VzKCkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAuZGVsaXZlcnlBZGRyZXNzRGV0YWlscykuZW1wdHkoKTtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLmRlbGl2ZXJ5QWRkcmVzc1NlbGVjdCkuZW1wdHkoKTtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLmludm9pY2VBZGRyZXNzRGV0YWlscykuZW1wdHkoKTtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLmludm9pY2VBZGRyZXNzU2VsZWN0KS5lbXB0eSgpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNob3dzIGFkZHJlc3NlcyBjb250ZW50IGFuZCBoaWRlcyB3YXJuaW5nXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2hvd0FkZHJlc3Nlc0NvbnRlbnQoKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5hZGRyZXNzZXNDb250ZW50KS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgJChjcmVhdGVPcmRlck1hcC5hZGRyZXNzZXNXYXJuaW5nKS5hZGRDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZXMgYWRkcmVzc2VzIGNvbnRlbnQgYW5kIHNob3dzIHdhcm5pbmdcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oaWRlQWRkcmVzc2VzQ29udGVudCgpIHtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLmFkZHJlc3Nlc0NvbnRlbnQpLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLmFkZHJlc3Nlc1dhcm5pbmcpLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93cyB3YXJuaW5nIGVtcHR5IGFkZHJlc3NlcyB3YXJuaW5nXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2hvd0VtcHR5QWRkcmVzc2VzV2FybmluZygpIHtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLmFkZHJlc3Nlc1dhcm5pbmcpLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIaWRlcyBlbXB0eSBhZGRyZXNzZXMgd2FybmluZ1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hpZGVFbXB0eUFkZHJlc3Nlc1dhcm5pbmcoKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5hZGRyZXNzZXNXYXJuaW5nKS5hZGRDbGFzcygnZC1ub25lJyk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9hZGRyZXNzZXMtcmVuZGVyZXIuanMiLCIvLyBmYWxsYmFjayBmb3Igbm9uLWFycmF5LWxpa2UgRVMzIGFuZCBub24tZW51bWVyYWJsZSBvbGQgVjggc3RyaW5nc1xudmFyIGNvZiA9IHJlcXVpcmUoJy4vX2NvZicpO1xubW9kdWxlLmV4cG9ydHMgPSBPYmplY3QoJ3onKS5wcm9wZXJ0eUlzRW51bWVyYWJsZSgwKSA/IE9iamVjdCA6IGZ1bmN0aW9uKGl0KXtcbiAgcmV0dXJuIGNvZihpdCkgPT0gJ1N0cmluZycgPyBpdC5zcGxpdCgnJykgOiBPYmplY3QoaXQpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2lvYmplY3QuanNcbi8vIG1vZHVsZSBpZCA9IDUxXG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxOCAxOSAyMCIsIi8qKlxuICogMjAwNy0yMDIwIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMjAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBjcmVhdGVPcmRlclBhZ2VNYXAgZnJvbSAnQHBhZ2VzL29yZGVyL2NyZWF0ZS9jcmVhdGUtb3JkZXItbWFwJztcbmltcG9ydCBSb3V0ZXIgZnJvbSAnQGNvbXBvbmVudHMvcm91dGVyJztcbmltcG9ydCB7RXZlbnRFbWl0dGVyfSBmcm9tICdAY29tcG9uZW50cy9ldmVudC1lbWl0dGVyJztcbmltcG9ydCBldmVudE1hcCBmcm9tICdAcGFnZXMvb3JkZXIvY3JlYXRlL2V2ZW50LW1hcCc7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBQcm92aWRlcyBhamF4IGNhbGxzIGZvciBnZXR0aW5nIGNhcnQgaW5mb3JtYXRpb25cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQ2FydFByb3ZpZGVyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy4kY29udGFpbmVyID0gJChjcmVhdGVPcmRlclBhZ2VNYXAub3JkZXJDcmVhdGlvbkNvbnRhaW5lcik7XG4gICAgdGhpcy5yb3V0ZXIgPSBuZXcgUm91dGVyKCk7XG4gIH1cblxuICAvKipcbiAgICogR2V0cyBjYXJ0IGluZm9ybWF0aW9uXG4gICAqXG4gICAqIEBwYXJhbSBjYXJ0SWRcbiAgICpcbiAgICogQHJldHVybnMge2pxWEhSfS4gT2JqZWN0IHdpdGggY2FydCBpbmZvcm1hdGlvbiBpbiByZXNwb25zZS5cbiAgICovXG4gIGdldENhcnQoY2FydElkKSB7XG4gICAgJC5nZXQodGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX2NhcnRzX2luZm8nLCB7Y2FydElkfSkpLnRoZW4oKGNhcnRJbmZvKSA9PiB7XG4gICAgICBFdmVudEVtaXR0ZXIuZW1pdChldmVudE1hcC5jYXJ0TG9hZGVkLCBjYXJ0SW5mbyk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogR2V0cyBleGlzdGluZyBlbXB0eSBjYXJ0IG9yIGNyZWF0ZXMgbmV3IGVtcHR5IGNhcnQgZm9yIGN1c3RvbWVyLlxuICAgKlxuICAgKiBAcGFyYW0gY3VzdG9tZXJJZFxuICAgKlxuICAgKiBAcmV0dXJucyB7anFYSFJ9LiBPYmplY3Qgd2l0aCBjYXJ0IGluZm9ybWF0aW9uIGluIHJlc3BvbnNlXG4gICAqL1xuICBsb2FkRW1wdHlDYXJ0KGN1c3RvbWVySWQpIHtcbiAgICAkLnBvc3QodGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX2NhcnRzX2NyZWF0ZScpLCB7XG4gICAgICBjdXN0b21lcklkLFxuICAgIH0pLnRoZW4oKGNhcnRJbmZvKSA9PiB7XG4gICAgICBFdmVudEVtaXR0ZXIuZW1pdChldmVudE1hcC5jYXJ0TG9hZGVkLCBjYXJ0SW5mbyk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogRHVwbGljYXRlcyBjYXJ0IGZyb20gcHJvdmlkZWQgb3JkZXJcbiAgICpcbiAgICogQHBhcmFtIG9yZGVySWRcbiAgICpcbiAgICogQHJldHVybnMge2pxWEhSfS4gT2JqZWN0IHdpdGggY2FydCBpbmZvcm1hdGlvbiBpbiByZXNwb25zZVxuICAgKi9cbiAgZHVwbGljYXRlT3JkZXJDYXJ0KG9yZGVySWQpIHtcbiAgICAkLnBvc3QodGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX29yZGVyc19kdXBsaWNhdGVfY2FydCcsIHtvcmRlcklkfSkpLnRoZW4oKGNhcnRJbmZvKSA9PiB7XG4gICAgICBFdmVudEVtaXR0ZXIuZW1pdChldmVudE1hcC5jYXJ0TG9hZGVkLCBjYXJ0SW5mbyk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9jYXJ0LXByb3ZpZGVyLmpzIiwiLyoqXG4gKiAyMDA3LTIwMjAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAyMCBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IENhcnRFZGl0b3IgZnJvbSAnQHBhZ2VzL29yZGVyL2NyZWF0ZS9jYXJ0LWVkaXRvcic7XG5pbXBvcnQgQ2FydFJ1bGVzUmVuZGVyZXIgZnJvbSAnQHBhZ2VzL29yZGVyL2NyZWF0ZS9jYXJ0LXJ1bGVzLXJlbmRlcmVyJztcbmltcG9ydCBjcmVhdGVPcmRlck1hcCBmcm9tICdAcGFnZXMvb3JkZXIvY3JlYXRlL2NyZWF0ZS1vcmRlci1tYXAnO1xuaW1wb3J0IHtFdmVudEVtaXR0ZXJ9IGZyb20gJ0Bjb21wb25lbnRzL2V2ZW50LWVtaXR0ZXInO1xuaW1wb3J0IGV2ZW50TWFwIGZyb20gJ0BwYWdlcy9vcmRlci9jcmVhdGUvZXZlbnQtbWFwJztcbmltcG9ydCBSb3V0ZXIgZnJvbSAnQGNvbXBvbmVudHMvcm91dGVyJztcbmltcG9ydCBTdW1tYXJ5UmVuZGVyZXIgZnJvbSAnQHBhZ2VzL29yZGVyL2NyZWF0ZS9zdW1tYXJ5LXJlbmRlcmVyJztcbmltcG9ydCBTaGlwcGluZ1JlbmRlcmVyIGZyb20gJ0BwYWdlcy9vcmRlci9jcmVhdGUvc2hpcHBpbmctcmVuZGVyZXInO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogUmVzcG9uc2libGUgZm9yIHNlYXJjaGluZyBjYXJ0IHJ1bGVzIGFuZCBtYW5hZ2luZyBjYXJ0IHJ1bGVzIHNlYXJjaCBibG9ja1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBDYXJ0UnVsZU1hbmFnZXIge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLmFjdGl2ZVNlYXJjaFJlcXVlc3QgPSBudWxsO1xuXG4gICAgdGhpcy5yb3V0ZXIgPSBuZXcgUm91dGVyKCk7XG4gICAgdGhpcy4kc2VhcmNoSW5wdXQgPSAkKGNyZWF0ZU9yZGVyTWFwLmNhcnRSdWxlU2VhcmNoSW5wdXQpO1xuICAgIHRoaXMuY2FydFJ1bGVzUmVuZGVyZXIgPSBuZXcgQ2FydFJ1bGVzUmVuZGVyZXIoKTtcbiAgICB0aGlzLmNhcnRFZGl0b3IgPSBuZXcgQ2FydEVkaXRvcigpO1xuICAgIHRoaXMuc3VtbWFyeVJlbmRlcmVyID0gbmV3IFN1bW1hcnlSZW5kZXJlcigpO1xuICAgIHRoaXMuc2hpcHBpbmdSZW5kZXJlciA9IG5ldyBTaGlwcGluZ1JlbmRlcmVyKCk7XG5cbiAgICB0aGlzLl9pbml0TGlzdGVuZXJzKCk7XG5cbiAgICByZXR1cm4ge1xuICAgICAgc2VhcmNoOiBzZWFyY2hQaHJhc2UgPT4gdGhpcy5fc2VhcmNoKHNlYXJjaFBocmFzZSksXG4gICAgICBzdG9wU2VhcmNoaW5nOiAoKSA9PiB0aGlzLmNhcnRSdWxlc1JlbmRlcmVyLmhpZGVSZXN1bHRzRHJvcGRvd24oKSxcbiAgICAgIGFkZENhcnRSdWxlVG9DYXJ0OiAoY2FydFJ1bGVJZCwgY2FydElkKSA9PiB0aGlzLmNhcnRFZGl0b3IuYWRkQ2FydFJ1bGVUb0NhcnQoY2FydFJ1bGVJZCwgY2FydElkKSxcbiAgICAgIHJlbW92ZUNhcnRSdWxlRnJvbUNhcnQ6IChjYXJ0UnVsZUlkLCBjYXJ0SWQpID0+IHRoaXMuY2FydEVkaXRvci5yZW1vdmVDYXJ0UnVsZUZyb21DYXJ0KGNhcnRSdWxlSWQsIGNhcnRJZCksXG4gICAgfTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJbml0aWF0ZXMgZXZlbnQgbGlzdGVuZXJzIGZvciBjYXJ0IHJ1bGUgYWN0aW9uc1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2luaXRMaXN0ZW5lcnMoKSB7XG4gICAgdGhpcy5fb25DYXJ0UnVsZVNlYXJjaCgpO1xuICAgIHRoaXMuX29uQWRkQ2FydFJ1bGVUb0NhcnQoKTtcbiAgICB0aGlzLl9vbkFkZENhcnRSdWxlVG9DYXJ0RmFpbHVyZSgpO1xuICAgIHRoaXMuX29uUmVtb3ZlQ2FydFJ1bGVGcm9tQ2FydCgpO1xuICB9XG5cbiAgLyoqXG4gICAqIExpc3RlbnMgZm9yIGNhcnQgcnVsZSBzZWFyY2ggYWN0aW9uXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfb25DYXJ0UnVsZVNlYXJjaCgpIHtcbiAgICBFdmVudEVtaXR0ZXIub24oZXZlbnRNYXAuY2FydFJ1bGVTZWFyY2hlZCwgKGNhcnRSdWxlcykgPT4ge1xuICAgICAgdGhpcy5jYXJ0UnVsZXNSZW5kZXJlci5yZW5kZXJTZWFyY2hSZXN1bHRzKGNhcnRSdWxlcyk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogTGlzdGVucyBldmVudCBvZiBhZGQgY2FydCBydWxlIHRvIGNhcnQgYWN0aW9uXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfb25BZGRDYXJ0UnVsZVRvQ2FydCgpIHtcbiAgICBFdmVudEVtaXR0ZXIub24oZXZlbnRNYXAuY2FydFJ1bGVBZGRlZCwgKGNhcnRJbmZvKSA9PiB7XG4gICAgICBjb25zdCBjYXJ0SXNFbXB0eSA9IGNhcnRJbmZvLnByb2R1Y3RzLmxlbmd0aCA9PT0gMDtcbiAgICAgIHRoaXMuY2FydFJ1bGVzUmVuZGVyZXIucmVuZGVyQ2FydFJ1bGVzQmxvY2soY2FydEluZm8uY2FydFJ1bGVzLCBjYXJ0SXNFbXB0eSk7XG4gICAgICB0aGlzLnNoaXBwaW5nUmVuZGVyZXIucmVuZGVyKGNhcnRJbmZvLnNoaXBwaW5nLCBjYXJ0SXNFbXB0eSk7XG4gICAgICB0aGlzLnN1bW1hcnlSZW5kZXJlci5yZW5kZXIoY2FydEluZm8pO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIExpc3RlbnMgZXZlbnQgd2hlbiBhZGQgY2FydCBydWxlIHRvIGNhcnQgZmFpbHNcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9vbkFkZENhcnRSdWxlVG9DYXJ0RmFpbHVyZSgpIHtcbiAgICBFdmVudEVtaXR0ZXIub24oZXZlbnRNYXAuY2FydFJ1bGVGYWlsZWRUb0FkZCwgKG1lc3NhZ2UpID0+IHtcbiAgICAgIHRoaXMuY2FydFJ1bGVzUmVuZGVyZXIuZGlzcGxheUVycm9yTWVzc2FnZShtZXNzYWdlKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBMaXN0ZW5zIGV2ZW50IGZvciByZW1vdmUgY2FydCBydWxlIGZyb20gY2FydCBhY3Rpb25cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9vblJlbW92ZUNhcnRSdWxlRnJvbUNhcnQoKSB7XG4gICAgRXZlbnRFbWl0dGVyLm9uKGV2ZW50TWFwLmNhcnRSdWxlUmVtb3ZlZCwgKGNhcnRJbmZvKSA9PiB7XG4gICAgICBjb25zdCBjYXJ0SXNFbXB0eSA9IGNhcnRJbmZvLnByb2R1Y3RzLmxlbmd0aCA9PT0gMDtcbiAgICAgIHRoaXMuc2hpcHBpbmdSZW5kZXJlci5yZW5kZXIoY2FydEluZm8uc2hpcHBpbmcsIGNhcnRJc0VtcHR5KTtcbiAgICAgIHRoaXMuY2FydFJ1bGVzUmVuZGVyZXIucmVuZGVyQ2FydFJ1bGVzQmxvY2soY2FydEluZm8uY2FydFJ1bGVzLCBjYXJ0SXNFbXB0eSk7XG4gICAgICB0aGlzLnN1bW1hcnlSZW5kZXJlci5yZW5kZXIoY2FydEluZm8pO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIFNlYXJjaGVzIGZvciBjYXJ0IHJ1bGVzIGJ5IHNlYXJjaCBwaHJhc2VcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zZWFyY2goc2VhcmNoUGhyYXNlKSB7XG4gICAgaWYgKHRoaXMuYWN0aXZlU2VhcmNoUmVxdWVzdCAhPT0gbnVsbCkge1xuICAgICAgdGhpcy5hY3RpdmVTZWFyY2hSZXF1ZXN0LmFib3J0KCk7XG4gICAgfVxuXG4gICAgdGhpcy5hY3RpdmVTZWFyY2hSZXF1ZXN0ID0gJC5nZXQodGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX2NhcnRfcnVsZXNfc2VhcmNoJyksIHtcbiAgICAgIHNlYXJjaF9waHJhc2U6IHNlYXJjaFBocmFzZSxcbiAgICB9KTtcblxuICAgIHRoaXMuYWN0aXZlU2VhcmNoUmVxdWVzdC50aGVuKChjYXJ0UnVsZXMpID0+IHtcbiAgICAgIEV2ZW50RW1pdHRlci5lbWl0KGV2ZW50TWFwLmNhcnRSdWxlU2VhcmNoZWQsIGNhcnRSdWxlcyk7XG4gICAgfSkuY2F0Y2goKGUpID0+IHtcbiAgICAgIGlmIChlLnN0YXR1c1RleHQgPT09ICdhYm9ydCcpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICBzaG93RXJyb3JNZXNzYWdlKGUucmVzcG9uc2VKU09OLm1lc3NhZ2UpO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvY2FydC1ydWxlLW1hbmFnZXIuanMiLCIvKipcbiAqIDIwMDctMjAyMCBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDIwIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgY3JlYXRlT3JkZXJNYXAgZnJvbSAnQHBhZ2VzL29yZGVyL2NyZWF0ZS9jcmVhdGUtb3JkZXItbWFwJztcbmltcG9ydCBDdXN0b21lclJlbmRlcmVyIGZyb20gJ0BwYWdlcy9vcmRlci9jcmVhdGUvY3VzdG9tZXItcmVuZGVyZXInO1xuaW1wb3J0IHtFdmVudEVtaXR0ZXJ9IGZyb20gJ0Bjb21wb25lbnRzL2V2ZW50LWVtaXR0ZXInO1xuaW1wb3J0IGV2ZW50TWFwIGZyb20gJ0BwYWdlcy9vcmRlci9jcmVhdGUvZXZlbnQtbWFwJztcbmltcG9ydCBSb3V0ZXIgZnJvbSAnQGNvbXBvbmVudHMvcm91dGVyJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIFJlc3BvbnNpYmxlIGZvciBjdXN0b21lcnMgbWFuYWdpbmcuIChzZWFyY2gsIHNlbGVjdCwgZ2V0IGN1c3RvbWVyIGluZm8gZXRjLilcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQ3VzdG9tZXJNYW5hZ2VyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy5jdXN0b21lcklkID0gbnVsbDtcbiAgICB0aGlzLmFjdGl2ZVNlYXJjaFJlcXVlc3QgPSBudWxsO1xuXG4gICAgdGhpcy5yb3V0ZXIgPSBuZXcgUm91dGVyKCk7XG4gICAgdGhpcy4kY29udGFpbmVyID0gJChjcmVhdGVPcmRlck1hcC5jdXN0b21lclNlYXJjaEJsb2NrKTtcbiAgICB0aGlzLiRzZWFyY2hJbnB1dCA9ICQoY3JlYXRlT3JkZXJNYXAuY3VzdG9tZXJTZWFyY2hJbnB1dCk7XG4gICAgdGhpcy4kY3VzdG9tZXJTZWFyY2hSZXN1bHRCbG9jayA9ICQoY3JlYXRlT3JkZXJNYXAuY3VzdG9tZXJTZWFyY2hSZXN1bHRzQmxvY2spO1xuICAgIHRoaXMuY3VzdG9tZXJSZW5kZXJlciA9IG5ldyBDdXN0b21lclJlbmRlcmVyKCk7XG5cbiAgICB0aGlzLl9pbml0TGlzdGVuZXJzKCk7XG4gICAgdGhpcy5pbml0QWRkQ3VzdG9tZXJJZnJhbWUoKTtcblxuICAgIHJldHVybiB7XG4gICAgICBzZWFyY2g6IHNlYXJjaFBocmFzZSA9PiB0aGlzLl9zZWFyY2goc2VhcmNoUGhyYXNlKSxcbiAgICAgIHNlbGVjdEN1c3RvbWVyOiBldmVudCA9PiB0aGlzLl9zZWxlY3RDdXN0b21lcihldmVudCksXG4gICAgICBsb2FkQ3VzdG9tZXJDYXJ0czogY3VycmVudENhcnRJZCA9PiB0aGlzLl9sb2FkQ3VzdG9tZXJDYXJ0cyhjdXJyZW50Q2FydElkKSxcbiAgICAgIGxvYWRDdXN0b21lck9yZGVyczogKCkgPT4gdGhpcy5fbG9hZEN1c3RvbWVyT3JkZXJzKCksXG4gICAgfTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJbml0aWFsaXplcyBldmVudCBsaXN0ZW5lcnNcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9pbml0TGlzdGVuZXJzKCkge1xuICAgIHRoaXMuJGNvbnRhaW5lci5vbignY2xpY2snLCBjcmVhdGVPcmRlck1hcC5jaGFuZ2VDdXN0b21lckJ0biwgKCkgPT4gdGhpcy5fY2hhbmdlQ3VzdG9tZXIoKSk7XG4gICAgdGhpcy5fb25DdXN0b21lclNlYXJjaCgpO1xuICAgIHRoaXMuX29uQ3VzdG9tZXJTZWxlY3QoKTtcbiAgICB0aGlzLm9uQ3VzdG9tZXJzTm90Rm91bmQoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgaW5pdEFkZEN1c3RvbWVySWZyYW1lKCkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAuY3VzdG9tZXJBZGRCdG4pLmZhbmN5Ym94KHtcbiAgICAgICd0eXBlJzogJ2lmcmFtZScsXG4gICAgICAnd2lkdGgnOiAnOTAlJyxcbiAgICAgICdoZWlnaHQnOiAnOTAlJyxcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBMaXN0ZW5zIGZvciBjdXN0b21lciBzZWFyY2ggZXZlbnRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9vbkN1c3RvbWVyU2VhcmNoKCkge1xuICAgIEV2ZW50RW1pdHRlci5vbihldmVudE1hcC5jdXN0b21lclNlYXJjaGVkLCAocmVzcG9uc2UpID0+IHtcbiAgICAgIHRoaXMuYWN0aXZlU2VhcmNoUmVxdWVzdCA9IG51bGw7XG4gICAgICB0aGlzLmN1c3RvbWVyUmVuZGVyZXIuY2xlYXJTaG93bkN1c3RvbWVycygpO1xuXG4gICAgICBpZiAocmVzcG9uc2UuY3VzdG9tZXJzLmxlbmd0aCA9PT0gMCkge1xuICAgICAgICBFdmVudEVtaXR0ZXIuZW1pdChldmVudE1hcC5jdXN0b21lcnNOb3RGb3VuZCk7XG5cbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICB0aGlzLmN1c3RvbWVyUmVuZGVyZXIucmVuZGVyU2VhcmNoUmVzdWx0cyhyZXNwb25zZS5jdXN0b21lcnMpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIExpc3RlbnMgZm9yIGV2ZW50IG9mIHdoZW4gbm8gY3VzdG9tZXJzIHdlcmUgZm91bmQgYnkgc2VhcmNoXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBvbkN1c3RvbWVyc05vdEZvdW5kKCkge1xuICAgIEV2ZW50RW1pdHRlci5vbihldmVudE1hcC5jdXN0b21lcnNOb3RGb3VuZCwgKCkgPT4ge1xuICAgICAgdGhpcy5jdXN0b21lclJlbmRlcmVyLnNob3dOb3RGb3VuZEN1c3RvbWVycygpO1xuICAgICAgdGhpcy5jdXN0b21lclJlbmRlcmVyLmhpZGVDaGVja291dEhpc3RvcnlCbG9jaygpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIExpc3RlbnMgZm9yIGN1c3RvbWVyIHNlbGVjdCBldmVudFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uQ3VzdG9tZXJTZWxlY3QoKSB7XG4gICAgRXZlbnRFbWl0dGVyLm9uKGV2ZW50TWFwLmN1c3RvbWVyU2VsZWN0ZWQsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgJGNob29zZUJ0biA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgICB0aGlzLmN1c3RvbWVySWQgPSAkY2hvb3NlQnRuLmRhdGEoJ2N1c3RvbWVyLWlkJyk7XG5cbiAgICAgIGNvbnN0IGNyZWF0ZUFkZHJlc3NVcmwgPSB0aGlzLnJvdXRlci5nZW5lcmF0ZShcbiAgICAgICAgJ2FkbWluX2FkZHJlc3Nlc19jcmVhdGUnLFxuICAgICAgICB7XG4gICAgICAgICAgJ2xpdGVEaXNwbGF5aW5nJzogMSxcbiAgICAgICAgICAnc3VibWl0Rm9ybUFqYXgnOiAxLFxuICAgICAgICAgICdpZF9jdXN0b21lcic6IHRoaXMuY3VzdG9tZXJJZCxcbiAgICAgICAgfVxuICAgICAgKTtcbiAgICAgICQoY3JlYXRlT3JkZXJNYXAuYWRkcmVzc0FkZEJ0bikuYXR0cignaHJlZicsIGNyZWF0ZUFkZHJlc3NVcmwpO1xuXG4gICAgICB0aGlzLmN1c3RvbWVyUmVuZGVyZXIuZGlzcGxheVNlbGVjdGVkQ3VzdG9tZXJCbG9jaygkY2hvb3NlQnRuKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGVzIHVzZSBjYXNlIHdoZW4gY3VzdG9tZXIgaXMgY2hhbmdlZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2NoYW5nZUN1c3RvbWVyKCkge1xuICAgIHRoaXMuY3VzdG9tZXJSZW5kZXJlci5zaG93Q3VzdG9tZXJTZWFyY2goKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBMb2FkcyBjdXN0b21lciBjYXJ0cyBsaXN0XG4gICAqXG4gICAqIEBwYXJhbSBjdXJyZW50Q2FydElkXG4gICAqL1xuICBfbG9hZEN1c3RvbWVyQ2FydHMoY3VycmVudENhcnRJZCkge1xuICAgIGNvbnN0IGN1c3RvbWVySWQgPSB0aGlzLmN1c3RvbWVySWQ7XG5cbiAgICAkLmdldCh0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fY3VzdG9tZXJzX2NhcnRzJywge2N1c3RvbWVySWR9KSkudGhlbigocmVzcG9uc2UpID0+IHtcbiAgICAgIHRoaXMuY3VzdG9tZXJSZW5kZXJlci5yZW5kZXJDYXJ0cyhyZXNwb25zZS5jYXJ0cywgY3VycmVudENhcnRJZCk7XG4gICAgfSkuY2F0Y2goKGUpID0+IHtcbiAgICAgIHNob3dFcnJvck1lc3NhZ2UoZS5yZXNwb25zZUpTT04ubWVzc2FnZSk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogTG9hZHMgY3VzdG9tZXIgb3JkZXJzIGxpc3RcbiAgICovXG4gIF9sb2FkQ3VzdG9tZXJPcmRlcnMoKSB7XG4gICAgY29uc3QgY3VzdG9tZXJJZCA9IHRoaXMuY3VzdG9tZXJJZDtcblxuICAgICQuZ2V0KHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9jdXN0b21lcnNfb3JkZXJzJywge2N1c3RvbWVySWR9KSkudGhlbigocmVzcG9uc2UpID0+IHtcbiAgICAgIHRoaXMuY3VzdG9tZXJSZW5kZXJlci5yZW5kZXJPcmRlcnMocmVzcG9uc2Uub3JkZXJzKTtcbiAgICB9KS5jYXRjaCgoZSkgPT4ge1xuICAgICAgc2hvd0Vycm9yTWVzc2FnZShlLnJlc3BvbnNlSlNPTi5tZXNzYWdlKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcGFyYW0ge0V2ZW50fSBjaG9vc2VDdXN0b21lckV2ZW50XG4gICAqXG4gICAqIEByZXR1cm4ge051bWJlcn1cbiAgICovXG4gIF9zZWxlY3RDdXN0b21lcihjaG9vc2VDdXN0b21lckV2ZW50KSB7XG4gICAgRXZlbnRFbWl0dGVyLmVtaXQoZXZlbnRNYXAuY3VzdG9tZXJTZWxlY3RlZCwgY2hvb3NlQ3VzdG9tZXJFdmVudCk7XG5cbiAgICByZXR1cm4gdGhpcy5jdXN0b21lcklkO1xuICB9XG5cbiAgLyoqXG4gICAqIFNlYXJjaGVzIGZvciBjdXN0b21lcnNcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zZWFyY2goc2VhcmNoUGhyYXNlKSB7XG4gICAgaWYgKHNlYXJjaFBocmFzZS5sZW5ndGggPT09IDApIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBpZiAodGhpcy5hY3RpdmVTZWFyY2hSZXF1ZXN0ICE9PSBudWxsKSB7XG4gICAgICB0aGlzLmFjdGl2ZVNlYXJjaFJlcXVlc3QuYWJvcnQoKTtcbiAgICB9XG5cbiAgICBjb25zdCAkc2VhcmNoUmVxdWVzdCA9ICQuZ2V0KHRoaXMucm91dGVyLmdlbmVyYXRlKCdhZG1pbl9jdXN0b21lcnNfc2VhcmNoJyksIHtcbiAgICAgIGN1c3RvbWVyX3NlYXJjaDogc2VhcmNoUGhyYXNlLFxuICAgIH0pO1xuICAgIHRoaXMuYWN0aXZlU2VhcmNoUmVxdWVzdCA9ICRzZWFyY2hSZXF1ZXN0O1xuXG4gICAgJHNlYXJjaFJlcXVlc3QudGhlbigocmVzcG9uc2UpID0+IHtcbiAgICAgIEV2ZW50RW1pdHRlci5lbWl0KGV2ZW50TWFwLmN1c3RvbWVyU2VhcmNoZWQsIHJlc3BvbnNlKTtcbiAgICB9KS5jYXRjaCgocmVzcG9uc2UpID0+IHtcbiAgICAgIGlmIChyZXNwb25zZS5zdGF0dXNUZXh0ID09PSAnYWJvcnQnKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cblxuICAgICAgc2hvd0Vycm9yTWVzc2FnZShyZXNwb25zZS5yZXNwb25zZUpTT04ubWVzc2FnZSk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9jdXN0b21lci1tYW5hZ2VyLmpzIiwiLyoqXG4gKiAyMDA3LTIwMjAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAyMCBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IGNyZWF0ZU9yZGVyTWFwIGZyb20gJ0BwYWdlcy9vcmRlci9jcmVhdGUvY3JlYXRlLW9yZGVyLW1hcCc7XG5pbXBvcnQgUm91dGVyIGZyb20gJ0Bjb21wb25lbnRzL3JvdXRlcic7XG5pbXBvcnQgZXZlbnRNYXAgZnJvbSAnQHBhZ2VzL29yZGVyL2NyZWF0ZS9ldmVudC1tYXAnO1xuaW1wb3J0IHtFdmVudEVtaXR0ZXJ9IGZyb20gJ0Bjb21wb25lbnRzL2V2ZW50LWVtaXR0ZXInO1xuXG5jb25zdCB7JH0gPSB3aW5kb3c7XG5cbi8qKlxuICogUmVzcG9uc2libGUgZm9yIGN1c3RvbWVyIGluZm9ybWF0aW9uIHJlbmRlcmluZ1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBDdXN0b21lclJlbmRlcmVyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy4kY29udGFpbmVyID0gJChjcmVhdGVPcmRlck1hcC5jdXN0b21lclNlYXJjaEJsb2NrKTtcbiAgICB0aGlzLiRjdXN0b21lclNlYXJjaFJlc3VsdEJsb2NrID0gJChjcmVhdGVPcmRlck1hcC5jdXN0b21lclNlYXJjaFJlc3VsdHNCbG9jayk7XG4gICAgdGhpcy5yb3V0ZXIgPSBuZXcgUm91dGVyKCk7XG4gIH1cblxuICAvKipcbiAgICogUmVuZGVycyBjdXN0b21lciBzZWFyY2ggcmVzdWx0c1xuICAgKlxuICAgKiBAcGFyYW0gZm91bmRDdXN0b21lcnNcbiAgICovXG4gIHJlbmRlclNlYXJjaFJlc3VsdHMoZm91bmRDdXN0b21lcnMpIHtcbiAgICBpZiAoZm91bmRDdXN0b21lcnMubGVuZ3RoID09PSAwKSB7XG4gICAgICBFdmVudEVtaXR0ZXIuZW1pdChldmVudE1hcC5jdXN0b21lcnNOb3RGb3VuZCk7XG5cbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBmb3IgKGNvbnN0IGN1c3RvbWVySWQgaW4gZm91bmRDdXN0b21lcnMpIHtcbiAgICAgIGNvbnN0IGN1c3RvbWVyUmVzdWx0ID0gZm91bmRDdXN0b21lcnNbY3VzdG9tZXJJZF07XG4gICAgICBjb25zdCBjdXN0b21lciA9IHtcbiAgICAgICAgaWQ6IGN1c3RvbWVySWQsXG4gICAgICAgIGZpcnN0TmFtZTogY3VzdG9tZXJSZXN1bHQuZmlyc3RuYW1lLFxuICAgICAgICBsYXN0TmFtZTogY3VzdG9tZXJSZXN1bHQubGFzdG5hbWUsXG4gICAgICAgIGVtYWlsOiBjdXN0b21lclJlc3VsdC5lbWFpbCxcbiAgICAgICAgYmlydGhkYXk6IGN1c3RvbWVyUmVzdWx0LmJpcnRoZGF5ICE9PSAnMDAwMC0wMC0wMCcgPyBjdXN0b21lclJlc3VsdC5iaXJ0aGRheSA6ICcgJyxcbiAgICAgIH07XG5cbiAgICAgIHRoaXMuX3JlbmRlckZvdW5kQ3VzdG9tZXIoY3VzdG9tZXIpO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBSZXNwb25zaWJsZSBmb3IgZGlzcGxheWluZyBjdXN0b21lciBibG9jayBhZnRlciBjdXN0b21lciBzZWxlY3RcbiAgICpcbiAgICogQHBhcmFtICR0YXJnZXRlZEJ0blxuICAgKi9cbiAgZGlzcGxheVNlbGVjdGVkQ3VzdG9tZXJCbG9jaygkdGFyZ2V0ZWRCdG4pIHtcbiAgICB0aGlzLnNob3dDaGVja291dEhpc3RvcnlCbG9jaygpO1xuXG4gICAgJHRhcmdldGVkQnRuLmFkZENsYXNzKCdkLW5vbmUnKTtcblxuICAgIGNvbnN0ICRjdXN0b21lckNhcmQgPSAkdGFyZ2V0ZWRCdG4uY2xvc2VzdCgnLmNhcmQnKTtcblxuICAgICRjdXN0b21lckNhcmQuYWRkQ2xhc3MoJ2JvcmRlci1zdWNjZXNzJyk7XG4gICAgJGN1c3RvbWVyQ2FyZC5maW5kKGNyZWF0ZU9yZGVyTWFwLmNoYW5nZUN1c3RvbWVyQnRuKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG5cbiAgICB0aGlzLiRjb250YWluZXIuZmluZChjcmVhdGVPcmRlck1hcC5jdXN0b21lclNlYXJjaFJvdykuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICAgIHRoaXMuJGNvbnRhaW5lci5maW5kKGNyZWF0ZU9yZGVyTWFwLm5vdFNlbGVjdGVkQ3VzdG9tZXJTZWFyY2hSZXN1bHRzKVxuICAgICAgLmNsb3Nlc3QoY3JlYXRlT3JkZXJNYXAuY3VzdG9tZXJTZWFyY2hSZXN1bHRDb2x1bW4pXG4gICAgICAucmVtb3ZlKClcbiAgICA7XG4gIH1cblxuICAvKipcbiAgICogU2hvd3MgY3VzdG9tZXIgc2VhcmNoIGJsb2NrXG4gICAqL1xuICBzaG93Q3VzdG9tZXJTZWFyY2goKSB7XG4gICAgdGhpcy4kY29udGFpbmVyLmZpbmQoY3JlYXRlT3JkZXJNYXAuY3VzdG9tZXJTZWFyY2hSb3cpLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZW5kZXJzIGN1c3RvbWVyIGNhcnRzIGxpc3RcbiAgICpcbiAgICogQHBhcmFtIHtBcnJheX0gY2FydHNcbiAgICogQHBhcmFtIHtJbnR9IGN1cnJlbnRDYXJ0SWRcbiAgICovXG4gIHJlbmRlckNhcnRzKGNhcnRzLCBjdXJyZW50Q2FydElkKSB7XG4gICAgY29uc3QgJGNhcnRzVGFibGUgPSAkKGNyZWF0ZU9yZGVyTWFwLmN1c3RvbWVyQ2FydHNUYWJsZSk7XG4gICAgY29uc3QgJGNhcnRzVGFibGVSb3dUZW1wbGF0ZSA9ICQoJChjcmVhdGVPcmRlck1hcC5jdXN0b21lckNhcnRzVGFibGVSb3dUZW1wbGF0ZSkuaHRtbCgpKTtcblxuICAgICRjYXJ0c1RhYmxlLmZpbmQoJ3Rib2R5JykuZW1wdHkoKTtcbiAgICB0aGlzLnNob3dDaGVja291dEhpc3RvcnlCbG9jaygpO1xuICAgIHRoaXMuX3JlbW92ZUVtcHR5TGlzdFJvd0Zyb21UYWJsZSgkY2FydHNUYWJsZSk7XG5cbiAgICBmb3IgKGNvbnN0IGtleSBpbiBjYXJ0cykge1xuICAgICAgY29uc3QgY2FydCA9IGNhcnRzW2tleV07XG5cbiAgICAgIC8vIGRvIG5vdCByZW5kZXIgY3VycmVudCBjYXJ0XG4gICAgICBpZiAoY2FydC5jYXJ0SWQgPT09IGN1cnJlbnRDYXJ0SWQpIHtcbiAgICAgICAgLy8gcmVuZGVyICdObyByZWNvcmRzIGZvdW5kJyB3YXJuIGlmIGNhcnRzIG9ubHkgY29udGFpbiBjdXJyZW50IGNhcnRcbiAgICAgICAgaWYgKGNhcnRzLmxlbmd0aCA9PT0gMSkge1xuICAgICAgICAgIHRoaXMuX3JlbmRlckVtcHR5TGlzdCgkY2FydHNUYWJsZSk7XG4gICAgICAgIH1cblxuICAgICAgICBjb250aW51ZTtcbiAgICAgIH1cblxuICAgICAgY29uc3QgJGNhcnRzVGFibGVSb3cgPSAkY2FydHNUYWJsZVJvd1RlbXBsYXRlLmNsb25lKCk7XG5cbiAgICAgICRjYXJ0c1RhYmxlUm93LmZpbmQoY3JlYXRlT3JkZXJNYXAuY2FydElkRmllbGQpLnRleHQoY2FydC5jYXJ0SWQpO1xuICAgICAgJGNhcnRzVGFibGVSb3cuZmluZChjcmVhdGVPcmRlck1hcC5jYXJ0RGF0ZUZpZWxkKS50ZXh0KGNhcnQuY3JlYXRpb25EYXRlKTtcbiAgICAgICRjYXJ0c1RhYmxlUm93LmZpbmQoY3JlYXRlT3JkZXJNYXAuY2FydFRvdGFsRmllbGQpLnRleHQoY2FydC50b3RhbFByaWNlKTtcbiAgICAgICRjYXJ0c1RhYmxlUm93LmZpbmQoY3JlYXRlT3JkZXJNYXAuY2FydERldGFpbHNCdG4pLnByb3AoXG4gICAgICAgICdocmVmJyxcbiAgICAgICAgdGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX2NhcnRzX3ZpZXcnLCB7Y2FydElkOiBjYXJ0LmNhcnRJZH0pLFxuICAgICAgKTtcblxuICAgICAgJGNhcnRzVGFibGVSb3cuZmluZChjcmVhdGVPcmRlck1hcC51c2VDYXJ0QnRuKS5kYXRhKCdjYXJ0LWlkJywgY2FydC5jYXJ0SWQpO1xuXG4gICAgICAkY2FydHNUYWJsZS5maW5kKCd0aGVhZCcpLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgICAgICRjYXJ0c1RhYmxlLmZpbmQoJ3Rib2R5JykuYXBwZW5kKCRjYXJ0c1RhYmxlUm93KTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogUmVuZGVycyBjdXN0b21lciBvcmRlcnMgbGlzdFxuICAgKlxuICAgKiBAcGFyYW0ge0FycmF5fSBvcmRlcnNcbiAgICovXG4gIHJlbmRlck9yZGVycyhvcmRlcnMpIHtcbiAgICBjb25zdCAkb3JkZXJzVGFibGUgPSAkKGNyZWF0ZU9yZGVyTWFwLmN1c3RvbWVyT3JkZXJzVGFibGUpO1xuICAgIGNvbnN0ICRyb3dUZW1wbGF0ZSA9ICQoJChjcmVhdGVPcmRlck1hcC5jdXN0b21lck9yZGVyc1RhYmxlUm93VGVtcGxhdGUpLmh0bWwoKSk7XG5cbiAgICAkb3JkZXJzVGFibGUuZmluZCgndGJvZHknKS5lbXB0eSgpO1xuICAgIHRoaXMuc2hvd0NoZWNrb3V0SGlzdG9yeUJsb2NrKCk7XG4gICAgdGhpcy5fcmVtb3ZlRW1wdHlMaXN0Um93RnJvbVRhYmxlKCRvcmRlcnNUYWJsZSk7XG5cbiAgICAvL3JlbmRlciAnTm8gcmVjb3JkcyBmb3VuZCcgd2hlbiBsaXN0IGlzIGVtcHR5XG4gICAgaWYgKG9yZGVycy5sZW5ndGggPT09IDApIHtcbiAgICAgIHRoaXMuX3JlbmRlckVtcHR5TGlzdCgkb3JkZXJzVGFibGUpO1xuXG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgZm9yIChjb25zdCBrZXkgaW4gT2JqZWN0LmtleXMob3JkZXJzKSkge1xuICAgICAgY29uc3Qgb3JkZXIgPSBvcmRlcnNba2V5XTtcbiAgICAgIGNvbnN0ICR0ZW1wbGF0ZSA9ICRyb3dUZW1wbGF0ZS5jbG9uZSgpO1xuXG4gICAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5vcmRlcklkRmllbGQpLnRleHQob3JkZXIub3JkZXJJZCk7XG4gICAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5vcmRlckRhdGVGaWVsZCkudGV4dChvcmRlci5vcmRlclBsYWNlZERhdGUpO1xuICAgICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAub3JkZXJQcm9kdWN0c0ZpZWxkKS50ZXh0KG9yZGVyLm9yZGVyUHJvZHVjdHNDb3VudCk7XG4gICAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5vcmRlclRvdGFsRmllbGQpLnRleHQob3JkZXIudG90YWxQYWlkKTtcbiAgICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyTWFwLm9yZGVyUGF5bWVudE1ldGhvZCkudGV4dChvcmRlci5wYXltZW50TWV0aG9kTmFtZSk7XG4gICAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5vcmRlclN0YXR1c0ZpZWxkKS50ZXh0KG9yZGVyLm9yZGVyU3RhdHVzKTtcbiAgICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyTWFwLm9yZGVyRGV0YWlsc0J0bikucHJvcChcbiAgICAgICAgJ2hyZWYnLFxuICAgICAgICB0aGlzLnJvdXRlci5nZW5lcmF0ZSgnYWRtaW5fb3JkZXJzX3ZpZXcnLCB7b3JkZXJJZDogb3JkZXIub3JkZXJJZH0pLFxuICAgICAgKTtcblxuICAgICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAudXNlT3JkZXJCdG4pLmRhdGEoJ29yZGVyLWlkJywgb3JkZXIub3JkZXJJZCk7XG5cbiAgICAgICRvcmRlcnNUYWJsZS5maW5kKCd0aGVhZCcpLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgICAgICRvcmRlcnNUYWJsZS5maW5kKCd0Ym9keScpLmFwcGVuZCgkdGVtcGxhdGUpO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93cyBlbXB0eSByZXN1bHQgd2hlbiBjdXN0b21lciBpcyBub3QgZm91bmRcbiAgICovXG4gIHNob3dOb3RGb3VuZEN1c3RvbWVycygpIHtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLmN1c3RvbWVyU2VhcmNoRW1wdHlSZXN1bHRXYXJuaW5nKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZXMgbm90IGZvdW5kIGN1c3RvbWVycyB3YXJuaW5nXG4gICAqL1xuICBoaWRlTm90Rm91bmRDdXN0b21lcnMoKSB7XG4gICAgJChjcmVhdGVPcmRlck1hcC5jdXN0b21lclNlYXJjaEVtcHR5UmVzdWx0V2FybmluZykuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNob3dzIGNoZWNrb3V0IGhpc3RvcnkgYmxvY2sgd2hlcmUgY2FydHMgYW5kIG9yZGVycyBhcmUgcmVuZGVyZWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIHNob3dDaGVja291dEhpc3RvcnlCbG9jaygpIHtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLmN1c3RvbWVyQ2hlY2tvdXRIaXN0b3J5KS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gIH1cblxuICAvKipcbiAgICogSGlkZXMgY2hlY2tvdXQgaGlzdG9yeSBibG9jayB3aGVyZSBjYXJ0cyBhbmQgb3JkZXJzIGFyZSByZW5kZXJlZFxuICAgKi9cbiAgaGlkZUNoZWNrb3V0SGlzdG9yeUJsb2NrKCkge1xuICAgICQoY3JlYXRlT3JkZXJNYXAuY3VzdG9tZXJDaGVja291dEhpc3RvcnkpLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZW5kZXJzICdObyByZWNvcmRzJyB3YXJuaW5nIGluIGxpc3RcbiAgICpcbiAgICogQHBhcmFtICR0YWJsZVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3JlbmRlckVtcHR5TGlzdCgkdGFibGUpIHtcbiAgICBjb25zdCAkZW1wdHlUYWJsZVJvdyA9ICQoJChjcmVhdGVPcmRlck1hcC5lbXB0eUxpc3RSb3dUZW1wbGF0ZSkuaHRtbCgpKS5jbG9uZSgpO1xuICAgICR0YWJsZS5maW5kKCd0Ym9keScpLmFwcGVuZCgkZW1wdHlUYWJsZVJvdyk7XG4gIH1cblxuICAvKipcbiAgICogUmVtb3ZlcyBlbXB0eSBsaXN0IHJvdyBpbiBjYXNlIGl0IHdhcyByZW5kZXJlZFxuICAgKi9cbiAgX3JlbW92ZUVtcHR5TGlzdFJvd0Zyb21UYWJsZSgkdGFibGUpIHtcbiAgICAkdGFibGUuZmluZChjcmVhdGVPcmRlck1hcC5lbXB0eUxpc3RSb3cpLnJlbW92ZSgpO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlbmRlcnMgY3VzdG9tZXIgaW5mb3JtYXRpb24gYWZ0ZXIgc2VhcmNoIGFjdGlvblxuICAgKlxuICAgKiBAcGFyYW0ge09iamVjdH0gY3VzdG9tZXJcbiAgICpcbiAgICogQHJldHVybiB7alF1ZXJ5fVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3JlbmRlckZvdW5kQ3VzdG9tZXIoY3VzdG9tZXIpIHtcbiAgICB0aGlzLmhpZGVOb3RGb3VuZEN1c3RvbWVycygpO1xuXG4gICAgY29uc3QgJGN1c3RvbWVyU2VhcmNoUmVzdWx0VGVtcGxhdGUgPSAkKCQoY3JlYXRlT3JkZXJNYXAuY3VzdG9tZXJTZWFyY2hSZXN1bHRUZW1wbGF0ZSkuaHRtbCgpKTtcbiAgICBjb25zdCAkdGVtcGxhdGUgPSAkY3VzdG9tZXJTZWFyY2hSZXN1bHRUZW1wbGF0ZS5jbG9uZSgpO1xuXG4gICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAuY3VzdG9tZXJTZWFyY2hSZXN1bHROYW1lKS50ZXh0KGAke2N1c3RvbWVyLmZpcnN0TmFtZX0gJHtjdXN0b21lci5sYXN0TmFtZX1gKTtcbiAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5jdXN0b21lclNlYXJjaFJlc3VsdEVtYWlsKS50ZXh0KGN1c3RvbWVyLmVtYWlsKTtcbiAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5jdXN0b21lclNlYXJjaFJlc3VsdElkKS50ZXh0KGN1c3RvbWVyLmlkKTtcbiAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5jdXN0b21lclNlYXJjaFJlc3VsdEJpcnRoZGF5KS50ZXh0KGN1c3RvbWVyLmJpcnRoZGF5KTtcbiAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlck1hcC5jaG9vc2VDdXN0b21lckJ0bikuZGF0YSgnY3VzdG9tZXItaWQnLCBjdXN0b21lci5pZCk7XG4gICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJNYXAuY3VzdG9tZXJEZXRhaWxzQnRuKS5wcm9wKFxuICAgICAgJ2hyZWYnLFxuICAgICAgdGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX2N1c3RvbWVyc192aWV3Jywge2N1c3RvbWVySWQ6IGN1c3RvbWVyLmlkfSksXG4gICAgKTtcblxuICAgIHJldHVybiB0aGlzLiRjdXN0b21lclNlYXJjaFJlc3VsdEJsb2NrLmFwcGVuZCgkdGVtcGxhdGUpO1xuICB9XG5cbiAgLyoqXG4gICAqIENsZWFycyBzaG93biBjdXN0b21lcnNcbiAgICovXG4gIGNsZWFyU2hvd25DdXN0b21lcnMoKSB7XG4gICAgdGhpcy4kY3VzdG9tZXJTZWFyY2hSZXN1bHRCbG9jay5lbXB0eSgpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvY3VzdG9tZXItcmVuZGVyZXIuanMiLCIvKipcbiAqIDIwMDctMjAyMCBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDIwIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgQ2FydEVkaXRvciBmcm9tICdAcGFnZXMvb3JkZXIvY3JlYXRlL2NhcnQtZWRpdG9yJztcbmltcG9ydCBjcmVhdGVPcmRlck1hcCBmcm9tICdAcGFnZXMvb3JkZXIvY3JlYXRlL2NyZWF0ZS1vcmRlci1tYXAnO1xuaW1wb3J0IGV2ZW50TWFwIGZyb20gJ0BwYWdlcy9vcmRlci9jcmVhdGUvZXZlbnQtbWFwJztcbmltcG9ydCB7RXZlbnRFbWl0dGVyfSBmcm9tICdAY29tcG9uZW50cy9ldmVudC1lbWl0dGVyJztcbmltcG9ydCBQcm9kdWN0UmVuZGVyZXIgZnJvbSAnQHBhZ2VzL29yZGVyL2NyZWF0ZS9wcm9kdWN0LXJlbmRlcmVyJztcbmltcG9ydCBSb3V0ZXIgZnJvbSAnQGNvbXBvbmVudHMvcm91dGVyJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIFByb2R1Y3QgY29tcG9uZW50IE9iamVjdCBmb3IgXCJDcmVhdGUgb3JkZXJcIiBwYWdlXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFByb2R1Y3RNYW5hZ2VyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy5wcm9kdWN0cyA9IFtdO1xuICAgIHRoaXMuc2VsZWN0ZWRQcm9kdWN0ID0gbnVsbDtcbiAgICB0aGlzLnNlbGVjdGVkQ29tYmluYXRpb25JZCA9IG51bGw7XG4gICAgdGhpcy5hY3RpdmVTZWFyY2hSZXF1ZXN0ID0gbnVsbDtcblxuICAgIHRoaXMucHJvZHVjdFJlbmRlcmVyID0gbmV3IFByb2R1Y3RSZW5kZXJlcigpO1xuICAgIHRoaXMucm91dGVyID0gbmV3IFJvdXRlcigpO1xuICAgIHRoaXMuY2FydEVkaXRvciA9IG5ldyBDYXJ0RWRpdG9yKCk7XG5cbiAgICB0aGlzLl9pbml0TGlzdGVuZXJzKCk7XG5cbiAgICByZXR1cm4ge1xuICAgICAgc2VhcmNoOiBzZWFyY2hQaHJhc2UgPT4gdGhpcy5fc2VhcmNoKHNlYXJjaFBocmFzZSksXG5cbiAgICAgIGFkZFByb2R1Y3RUb0NhcnQ6IGNhcnRJZCA9PiB0aGlzLmNhcnRFZGl0b3IuYWRkUHJvZHVjdChjYXJ0SWQsIHRoaXMuX2dldFByb2R1Y3REYXRhKCkpLFxuXG4gICAgICByZW1vdmVQcm9kdWN0RnJvbUNhcnQ6IChjYXJ0SWQsIHByb2R1Y3QpID0+XG4gICAgICAgIHRoaXMuY2FydEVkaXRvci5yZW1vdmVQcm9kdWN0RnJvbUNhcnQoY2FydElkLCBwcm9kdWN0KSxcblxuICAgICAgY2hhbmdlUHJvZHVjdFByaWNlOiAoY2FydElkLCBjdXN0b21lcklkLCB1cGRhdGVkUHJvZHVjdCkgPT5cbiAgICAgICAgdGhpcy5jYXJ0RWRpdG9yLmNoYW5nZVByb2R1Y3RQcmljZShjYXJ0SWQsIGN1c3RvbWVySWQsIHVwZGF0ZWRQcm9kdWN0KSxcblxuICAgICAgY2hhbmdlUHJvZHVjdFF0eTogKGNhcnRJZCwgdXBkYXRlZFByb2R1Y3QpID0+XG4gICAgICAgIHRoaXMuY2FydEVkaXRvci5jaGFuZ2VQcm9kdWN0UXR5KGNhcnRJZCwgdXBkYXRlZFByb2R1Y3QpLFxuICAgIH07XG4gIH1cblxuICAvKipcbiAgICogSW5pdGlhbGl6ZXMgZXZlbnQgbGlzdGVuZXJzXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaW5pdExpc3RlbmVycygpIHtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLnByb2R1Y3RTZWxlY3QpLm9uKCdjaGFuZ2UnLCBlID0+IHRoaXMuX2luaXRQcm9kdWN0U2VsZWN0KGUpKTtcbiAgICAkKGNyZWF0ZU9yZGVyTWFwLmNvbWJpbmF0aW9uc1NlbGVjdCkub24oJ2NoYW5nZScsIGUgPT4gdGhpcy5faW5pdENvbWJpbmF0aW9uU2VsZWN0KGUpKTtcblxuICAgIHRoaXMuX29uUHJvZHVjdFNlYXJjaCgpO1xuICAgIHRoaXMuX29uQWRkUHJvZHVjdFRvQ2FydCgpO1xuICAgIHRoaXMuX29uUmVtb3ZlUHJvZHVjdEZyb21DYXJ0KCk7XG4gICAgdGhpcy5fb25Qcm9kdWN0UHJpY2VDaGFuZ2UoKTtcbiAgICB0aGlzLl9vblByb2R1Y3RRdHlDaGFuZ2UoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBMaXN0ZW5zIGZvciBwcm9kdWN0IHNlYXJjaCBldmVudFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uUHJvZHVjdFNlYXJjaCgpIHtcbiAgICBFdmVudEVtaXR0ZXIub24oZXZlbnRNYXAucHJvZHVjdFNlYXJjaGVkLCAocmVzcG9uc2UpID0+IHtcbiAgICAgIHRoaXMucHJvZHVjdHMgPSByZXNwb25zZS5wcm9kdWN0cztcbiAgICAgIHRoaXMucHJvZHVjdFJlbmRlcmVyLnJlbmRlclNlYXJjaFJlc3VsdHModGhpcy5wcm9kdWN0cyk7XG4gICAgICB0aGlzLl9zZWxlY3RGaXJzdFJlc3VsdCgpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIExpc3RlbnMgZm9yIGFkZCBwcm9kdWN0IHRvIGNhcnQgZXZlbnRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9vbkFkZFByb2R1Y3RUb0NhcnQoKSB7XG4gICAgLy8gb24gc3VjY2Vzc1xuICAgIEV2ZW50RW1pdHRlci5vbihldmVudE1hcC5wcm9kdWN0QWRkZWRUb0NhcnQsIChjYXJ0SW5mbykgPT4ge1xuICAgICAgdGhpcy5wcm9kdWN0UmVuZGVyZXIuY2xlYW5DYXJ0QmxvY2tBbGVydHMoKTtcbiAgICAgIEV2ZW50RW1pdHRlci5lbWl0KGV2ZW50TWFwLmNhcnRMb2FkZWQsIGNhcnRJbmZvKTtcbiAgICB9KTtcblxuICAgIC8vIG9uIGZhaWx1cmVcbiAgICBFdmVudEVtaXR0ZXIub24oZXZlbnRNYXAucHJvZHVjdEFkZFRvQ2FydEZhaWxlZCwgKGVycm9yTWVzc2FnZSkgPT4ge1xuICAgICAgdGhpcy5wcm9kdWN0UmVuZGVyZXIucmVuZGVyQ2FydEJsb2NrRXJyb3JBbGVydChlcnJvck1lc3NhZ2UpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIExpc3RlbnMgZm9yIHJlbW92ZSBwcm9kdWN0IGZyb20gY2FydCBldmVudFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uUmVtb3ZlUHJvZHVjdEZyb21DYXJ0KCkge1xuICAgIEV2ZW50RW1pdHRlci5vbihldmVudE1hcC5wcm9kdWN0UmVtb3ZlZEZyb21DYXJ0LCAoY2FydEluZm8pID0+IHtcbiAgICAgIEV2ZW50RW1pdHRlci5lbWl0KGV2ZW50TWFwLmNhcnRMb2FkZWQsIGNhcnRJbmZvKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBMaXN0ZW5zIGZvciBwcm9kdWN0IHByaWNlIGNoYW5nZSBpbiBjYXJ0IGV2ZW50XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfb25Qcm9kdWN0UHJpY2VDaGFuZ2UoKSB7XG4gICAgRXZlbnRFbWl0dGVyLm9uKGV2ZW50TWFwLnByb2R1Y3RQcmljZUNoYW5nZWQsIChjYXJ0SW5mbykgPT4ge1xuICAgICAgdGhpcy5wcm9kdWN0UmVuZGVyZXIuY2xlYW5DYXJ0QmxvY2tBbGVydHMoKTtcbiAgICAgIEV2ZW50RW1pdHRlci5lbWl0KGV2ZW50TWFwLmNhcnRMb2FkZWQsIGNhcnRJbmZvKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBMaXN0ZW5zIGZvciBwcm9kdWN0IHF1YW50aXR5IGNoYW5nZSBpbiBjYXJ0IHN1Y2Nlc3MvZmFpbHVyZSBldmVudFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uUHJvZHVjdFF0eUNoYW5nZSgpIHtcbiAgICAvLyBvbiBzdWNjZXNzXG4gICAgRXZlbnRFbWl0dGVyLm9uKGV2ZW50TWFwLnByb2R1Y3RRdHlDaGFuZ2VkLCAoY2FydEluZm8pID0+IHtcbiAgICAgIHRoaXMucHJvZHVjdFJlbmRlcmVyLmNsZWFuQ2FydEJsb2NrQWxlcnRzKCk7XG4gICAgICBFdmVudEVtaXR0ZXIuZW1pdChldmVudE1hcC5jYXJ0TG9hZGVkLCBjYXJ0SW5mbyk7XG4gICAgfSk7XG5cbiAgICAvLyBvbiBmYWlsdXJlXG4gICAgRXZlbnRFbWl0dGVyLm9uKGV2ZW50TWFwLnByb2R1Y3RRdHlDaGFuZ2VGYWlsZWQsIChlKSA9PiB7XG4gICAgICB0aGlzLnByb2R1Y3RSZW5kZXJlci5yZW5kZXJDYXJ0QmxvY2tFcnJvckFsZXJ0KGUucmVzcG9uc2VKU09OLm1lc3NhZ2UpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEluaXRpYWxpemVzIHByb2R1Y3Qgc2VsZWN0XG4gICAqXG4gICAqIEBwYXJhbSBldmVudFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2luaXRQcm9kdWN0U2VsZWN0KGV2ZW50KSB7XG4gICAgY29uc3QgcHJvZHVjdElkID0gTnVtYmVyKCQoZXZlbnQuY3VycmVudFRhcmdldCkuZmluZCgnOnNlbGVjdGVkJykudmFsKCkpO1xuICAgIHRoaXMuX3NlbGVjdFByb2R1Y3QocHJvZHVjdElkKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJbml0aWFsaXplcyBjb21iaW5hdGlvbiBzZWxlY3RcbiAgICpcbiAgICogQHBhcmFtIGV2ZW50XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaW5pdENvbWJpbmF0aW9uU2VsZWN0KGV2ZW50KSB7XG4gICAgY29uc3QgY29tYmluYXRpb25JZCA9IE51bWJlcigkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmZpbmQoJzpzZWxlY3RlZCcpLnZhbCgpKTtcbiAgICB0aGlzLl9zZWxlY3RDb21iaW5hdGlvbihjb21iaW5hdGlvbklkKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTZWFyY2hlcyBmb3IgcHJvZHVjdFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3NlYXJjaChzZWFyY2hQaHJhc2UpIHtcbiAgICBpZiAoc2VhcmNoUGhyYXNlLmxlbmd0aCA8IDMpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBpZiAodGhpcy5hY3RpdmVTZWFyY2hSZXF1ZXN0ICE9PSBudWxsKSB7XG4gICAgICB0aGlzLmFjdGl2ZVNlYXJjaFJlcXVlc3QuYWJvcnQoKTtcbiAgICB9XG5cbiAgICBjb25zdCBwYXJhbXMgPSB7XG4gICAgICBzZWFyY2hfcGhyYXNlOiBzZWFyY2hQaHJhc2UsXG4gICAgfTtcbiAgICBpZiAoJChjcmVhdGVPcmRlck1hcC5jYXJ0Q3VycmVuY3lTZWxlY3QpLmRhdGEoJ3NlbGVjdGVkQ3VycmVuY3lJZCcpICE9IHVuZGVmaW5lZCkge1xuICAgICAgcGFyYW1zLmN1cnJlbmN5X2lkID0gJChjcmVhdGVPcmRlck1hcC5jYXJ0Q3VycmVuY3lTZWxlY3QpLmRhdGEoJ3NlbGVjdGVkQ3VycmVuY3lJZCcpO1xuICAgIH1cblxuICAgIGNvbnN0ICRzZWFyY2hSZXF1ZXN0ID0gJC5nZXQodGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX3Byb2R1Y3RzX3NlYXJjaCcpLCBwYXJhbXMpO1xuICAgIHRoaXMuYWN0aXZlU2VhcmNoUmVxdWVzdCA9ICRzZWFyY2hSZXF1ZXN0O1xuXG4gICAgJHNlYXJjaFJlcXVlc3QudGhlbigocmVzcG9uc2UpID0+IHtcbiAgICAgIEV2ZW50RW1pdHRlci5lbWl0KGV2ZW50TWFwLnByb2R1Y3RTZWFyY2hlZCwgcmVzcG9uc2UpO1xuICAgIH0pLmNhdGNoKChyZXNwb25zZSkgPT4ge1xuICAgICAgaWYgKHJlc3BvbnNlLnN0YXR1c1RleHQgPT09ICdhYm9ydCcpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICBzaG93RXJyb3JNZXNzYWdlKHJlc3BvbnNlLnJlc3BvbnNlSlNPTi5tZXNzYWdlKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJbml0aWF0ZSBmaXJzdCByZXN1bHQgZGF0YXNldCBhZnRlciBzZWFyY2hcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zZWxlY3RGaXJzdFJlc3VsdCgpIHtcbiAgICB0aGlzLl91bnNldFByb2R1Y3QoKTtcblxuICAgIGNvbnN0IHZhbHVlcyA9IE9iamVjdC52YWx1ZXModGhpcy5wcm9kdWN0cyk7XG5cbiAgICBpZiAodmFsdWVzLmxlbmd0aCAhPT0gMCkge1xuICAgICAgdGhpcy5fc2VsZWN0UHJvZHVjdCh2YWx1ZXNbMF0ucHJvZHVjdElkKTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogSGFuZGxlcyB1c2UgY2FzZSB3aGVuIHByb2R1Y3QgaXMgc2VsZWN0ZWQgZnJvbSBzZWFyY2ggcmVzdWx0c1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKlxuICAgKiBAcGFyYW0ge051bWJlcn0gcHJvZHVjdElkXG4gICAqL1xuICBfc2VsZWN0UHJvZHVjdChwcm9kdWN0SWQpIHtcbiAgICB0aGlzLl91bnNldENvbWJpbmF0aW9uKCk7XG5cbiAgICBmb3IgKGNvbnN0IGtleSBpbiB0aGlzLnByb2R1Y3RzKSB7XG4gICAgICBpZiAodGhpcy5wcm9kdWN0c1trZXldLnByb2R1Y3RJZCA9PT0gcHJvZHVjdElkKSB7XG4gICAgICAgIHRoaXMuc2VsZWN0ZWRQcm9kdWN0ID0gdGhpcy5wcm9kdWN0c1trZXldO1xuXG4gICAgICAgIGJyZWFrO1xuICAgICAgfVxuICAgIH1cblxuICAgIHRoaXMucHJvZHVjdFJlbmRlcmVyLnJlbmRlclByb2R1Y3RNZXRhZGF0YSh0aGlzLnNlbGVjdGVkUHJvZHVjdCk7XG4gICAgLy8gaWYgcHJvZHVjdCBoYXMgY29tYmluYXRpb25zIHNlbGVjdCB0aGUgZmlyc3QgZWxzZSBsZWF2ZSBpdCBudWxsXG4gICAgaWYgKHRoaXMuc2VsZWN0ZWRQcm9kdWN0LmNvbWJpbmF0aW9ucy5sZW5ndGggIT09IDApIHtcbiAgICAgIHRoaXMuX3NlbGVjdENvbWJpbmF0aW9uKE9iamVjdC5rZXlzKHRoaXMuc2VsZWN0ZWRQcm9kdWN0LmNvbWJpbmF0aW9ucylbMF0pO1xuICAgIH1cblxuICAgIHJldHVybiB0aGlzLnNlbGVjdGVkUHJvZHVjdDtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGVzIHVzZSBjYXNlIHdoZW4gbmV3IGNvbWJpbmF0aW9uIGlzIHNlbGVjdGVkXG4gICAqXG4gICAqIEBwYXJhbSBjb21iaW5hdGlvbklkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2VsZWN0Q29tYmluYXRpb24oY29tYmluYXRpb25JZCkge1xuICAgIGNvbnN0IGNvbWJpbmF0aW9uID0gdGhpcy5zZWxlY3RlZFByb2R1Y3QuY29tYmluYXRpb25zW2NvbWJpbmF0aW9uSWRdO1xuXG4gICAgdGhpcy5zZWxlY3RlZENvbWJpbmF0aW9uSWQgPSBjb21iaW5hdGlvbklkO1xuICAgIHRoaXMucHJvZHVjdFJlbmRlcmVyLnJlbmRlclN0b2NrKGNvbWJpbmF0aW9uLnN0b2NrKTtcblxuICAgIHJldHVybiBjb21iaW5hdGlvbjtcbiAgfVxuXG4gIC8qKlxuICAgKiBTZXRzIHRoZSBzZWxlY3RlZCBjb21iaW5hdGlvbiBpZCB0byBudWxsXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfdW5zZXRDb21iaW5hdGlvbigpIHtcbiAgICB0aGlzLnNlbGVjdGVkQ29tYmluYXRpb25JZCA9IG51bGw7XG4gIH1cblxuICAvKipcbiAgICogU2V0cyB0aGUgc2VsZWN0ZWQgcHJvZHVjdCB0byBudWxsXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfdW5zZXRQcm9kdWN0KCkge1xuICAgIHRoaXMuc2VsZWN0ZWRQcm9kdWN0ID0gbnVsbDtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZXRyaWV2ZXMgcHJvZHVjdCBkYXRhIGZyb20gcHJvZHVjdCBzZWFyY2ggcmVzdWx0IGJsb2NrIGZpZWxkc1xuICAgKlxuICAgKiBAcmV0dXJucyB7T2JqZWN0fVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2dldFByb2R1Y3REYXRhKCkge1xuICAgIGNvbnN0ICRmaWxlSW5wdXRzID0gJChjcmVhdGVPcmRlck1hcC5wcm9kdWN0Q3VzdG9taXphdGlvbkNvbnRhaW5lcikuZmluZCgnaW5wdXRbdHlwZT1cImZpbGVcIl0nKTtcbiAgICBjb25zdCBmb3JtRGF0YSA9IG5ldyBGb3JtRGF0YShkb2N1bWVudC5xdWVyeVNlbGVjdG9yKGNyZWF0ZU9yZGVyTWFwLnByb2R1Y3RBZGRGb3JtKSk7XG4gICAgY29uc3QgZmlsZVNpemVzID0ge307XG5cbiAgICAvLyBhZGRzIGtleSB2YWx1ZSBwYWlycyB7aW5wdXQgbmFtZTogZmlsZSBzaXplfSBvZiBlYWNoIGZpbGUgaW4gc2VwYXJhdGUgb2JqZWN0IGluIGNhc2UgZm9ybURhdGEgc2l6ZSBleGNlZWRzIHNlcnZlciBzZXR0aW5ncy5cbiAgICAkLmVhY2goJGZpbGVJbnB1dHMsIChrZXksIGlucHV0KSA9PiB7XG4gICAgICBpZiAoaW5wdXQuZmlsZXMubGVuZ3RoICE9PSAwKSB7XG4gICAgICAgIGZpbGVTaXplc1skKGlucHV0KS5kYXRhKCdjdXN0b21pemF0aW9uLWZpZWxkLWlkJyldID0gaW5wdXQuZmlsZXNbMF0uc2l6ZTtcbiAgICAgIH1cbiAgICB9KTtcblxuICAgIHJldHVybiB7XG4gICAgICBwcm9kdWN0OiBmb3JtRGF0YSxcbiAgICAgIGZpbGVTaXplcyxcbiAgICB9O1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUvcHJvZHVjdC1tYW5hZ2VyLmpzIiwiLyoqXG4gKiAyMDA3LTIwMjAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAyMCBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IHtFdmVudEVtaXR0ZXJ9IGZyb20gJy4uLy4uLy4uL2NvbXBvbmVudHMvZXZlbnQtZW1pdHRlcic7XG5pbXBvcnQgZXZlbnRNYXAgZnJvbSAnLi9ldmVudC1tYXAnO1xuaW1wb3J0IFN1bW1hcnlSZW5kZXJlciBmcm9tICcuL3N1bW1hcnktcmVuZGVyZXInO1xuaW1wb3J0IFJvdXRlciBmcm9tICcuLi8uLi8uLi9jb21wb25lbnRzL3JvdXRlcic7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBNYW5hZ2VzIHN1bW1hcnkgYmxvY2tcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgU3VtbWFyeU1hbmFnZXIge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLnJvdXRlciA9IG5ldyBSb3V0ZXIoKTtcbiAgICB0aGlzLnN1bW1hcnlSZW5kZXJlciA9IG5ldyBTdW1tYXJ5UmVuZGVyZXIoKTtcbiAgICB0aGlzLl9pbml0TGlzdGVuZXJzKCk7XG5cbiAgICByZXR1cm4ge1xuICAgICAgc2VuZFByb2Nlc3NPcmRlckVtYWlsOiBjYXJ0SWQgPT4gdGhpcy5fc2VuZFByb2Nlc3NPcmRlckVtYWlsKGNhcnRJZCksXG4gICAgfTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJbml0cyBldmVudCBsaXN0ZW5lcnNcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9pbml0TGlzdGVuZXJzKCkge1xuICAgIHRoaXMuX29uUHJvY2Vzc09yZGVyRW1haWxFcnJvcigpO1xuICAgIHRoaXMuX29uUHJvY2Vzc09yZGVyRW1haWxTdWNjZXNzKCk7XG4gIH1cblxuICAvKipcbiAgICogTGlzdGVucyBmb3IgcHJvY2VzcyBvcmRlciBlbWFpbCBzZW5kaW5nIHN1Y2Nlc3MgZXZlbnRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9vblByb2Nlc3NPcmRlckVtYWlsU3VjY2VzcygpIHtcbiAgICBFdmVudEVtaXR0ZXIub24oZXZlbnRNYXAucHJvY2Vzc09yZGVyRW1haWxTZW50LCAocmVzcG9uc2UpID0+IHtcbiAgICAgIHRoaXMuc3VtbWFyeVJlbmRlcmVyLmNsZWFuQWxlcnRzKCk7XG4gICAgICB0aGlzLnN1bW1hcnlSZW5kZXJlci5yZW5kZXJTdWNjZXNzTWVzc2FnZShyZXNwb25zZS5tZXNzYWdlKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBMaXN0ZW5zIGZvciBwcm9jZXNzIG9yZGVyIGVtYWlsIGZhaWxlZCBldmVudFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uUHJvY2Vzc09yZGVyRW1haWxFcnJvcigpIHtcbiAgICBFdmVudEVtaXR0ZXIub24oZXZlbnRNYXAucHJvY2Vzc09yZGVyRW1haWxGYWlsZWQsIChyZXNwb25zZSkgPT4ge1xuICAgICAgdGhpcy5zdW1tYXJ5UmVuZGVyZXIuY2xlYW5BbGVydHMoKTtcbiAgICAgIHRoaXMuc3VtbWFyeVJlbmRlcmVyLnJlbmRlckVycm9yTWVzc2FnZShyZXNwb25zZS5yZXNwb25zZUpTT04ubWVzc2FnZSk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogU2VuZHMgZW1haWwgdG8gY3VzdG9tZXIgd2l0aCBsaW5rIG9mIG9yZGVyIHByb2Nlc3NpbmdcbiAgICpcbiAgICogQHBhcmFtIHtOdW1iZXJ9IGNhcnRJZFxuICAgKi9cbiAgX3NlbmRQcm9jZXNzT3JkZXJFbWFpbChjYXJ0SWQpIHtcbiAgICAkLnBvc3QodGhpcy5yb3V0ZXIuZ2VuZXJhdGUoJ2FkbWluX29yZGVyc19zZW5kX3Byb2Nlc3Nfb3JkZXJfZW1haWwnKSwge1xuICAgICAgY2FydElkLFxuICAgIH0pLnRoZW4ocmVzcG9uc2UgPT4gRXZlbnRFbWl0dGVyLmVtaXQoZXZlbnRNYXAucHJvY2Vzc09yZGVyRW1haWxTZW50LCByZXNwb25zZSkpLmNhdGNoKChlKSA9PiB7XG4gICAgICBFdmVudEVtaXR0ZXIuZW1pdChldmVudE1hcC5wcm9jZXNzT3JkZXJFbWFpbEZhaWxlZCwgZSk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS9zdW1tYXJ5LW1hbmFnZXIuanMiLCJleHBvcnRzLmYgPSB7fS5wcm9wZXJ0eUlzRW51bWVyYWJsZTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1waWUuanNcbi8vIG1vZHVsZSBpZCA9IDUyXG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNSAxNiAxOCIsIi8vIENvcHlyaWdodCBKb3llbnQsIEluYy4gYW5kIG90aGVyIE5vZGUgY29udHJpYnV0b3JzLlxuLy9cbi8vIFBlcm1pc3Npb24gaXMgaGVyZWJ5IGdyYW50ZWQsIGZyZWUgb2YgY2hhcmdlLCB0byBhbnkgcGVyc29uIG9idGFpbmluZyBhXG4vLyBjb3B5IG9mIHRoaXMgc29mdHdhcmUgYW5kIGFzc29jaWF0ZWQgZG9jdW1lbnRhdGlvbiBmaWxlcyAodGhlXG4vLyBcIlNvZnR3YXJlXCIpLCB0byBkZWFsIGluIHRoZSBTb2Z0d2FyZSB3aXRob3V0IHJlc3RyaWN0aW9uLCBpbmNsdWRpbmdcbi8vIHdpdGhvdXQgbGltaXRhdGlvbiB0aGUgcmlnaHRzIHRvIHVzZSwgY29weSwgbW9kaWZ5LCBtZXJnZSwgcHVibGlzaCxcbi8vIGRpc3RyaWJ1dGUsIHN1YmxpY2Vuc2UsIGFuZC9vciBzZWxsIGNvcGllcyBvZiB0aGUgU29mdHdhcmUsIGFuZCB0byBwZXJtaXRcbi8vIHBlcnNvbnMgdG8gd2hvbSB0aGUgU29mdHdhcmUgaXMgZnVybmlzaGVkIHRvIGRvIHNvLCBzdWJqZWN0IHRvIHRoZVxuLy8gZm9sbG93aW5nIGNvbmRpdGlvbnM6XG4vL1xuLy8gVGhlIGFib3ZlIGNvcHlyaWdodCBub3RpY2UgYW5kIHRoaXMgcGVybWlzc2lvbiBub3RpY2Ugc2hhbGwgYmUgaW5jbHVkZWRcbi8vIGluIGFsbCBjb3BpZXMgb3Igc3Vic3RhbnRpYWwgcG9ydGlvbnMgb2YgdGhlIFNvZnR3YXJlLlxuLy9cbi8vIFRIRSBTT0ZUV0FSRSBJUyBQUk9WSURFRCBcIkFTIElTXCIsIFdJVEhPVVQgV0FSUkFOVFkgT0YgQU5ZIEtJTkQsIEVYUFJFU1Ncbi8vIE9SIElNUExJRUQsIElOQ0xVRElORyBCVVQgTk9UIExJTUlURUQgVE8gVEhFIFdBUlJBTlRJRVMgT0Zcbi8vIE1FUkNIQU5UQUJJTElUWSwgRklUTkVTUyBGT1IgQSBQQVJUSUNVTEFSIFBVUlBPU0UgQU5EIE5PTklORlJJTkdFTUVOVC4gSU5cbi8vIE5PIEVWRU5UIFNIQUxMIFRIRSBBVVRIT1JTIE9SIENPUFlSSUdIVCBIT0xERVJTIEJFIExJQUJMRSBGT1IgQU5ZIENMQUlNLFxuLy8gREFNQUdFUyBPUiBPVEhFUiBMSUFCSUxJVFksIFdIRVRIRVIgSU4gQU4gQUNUSU9OIE9GIENPTlRSQUNULCBUT1JUIE9SXG4vLyBPVEhFUldJU0UsIEFSSVNJTkcgRlJPTSwgT1VUIE9GIE9SIElOIENPTk5FQ1RJT04gV0lUSCBUSEUgU09GVFdBUkUgT1IgVEhFXG4vLyBVU0UgT1IgT1RIRVIgREVBTElOR1MgSU4gVEhFIFNPRlRXQVJFLlxuXG4ndXNlIHN0cmljdCc7XG5cbnZhciBSID0gdHlwZW9mIFJlZmxlY3QgPT09ICdvYmplY3QnID8gUmVmbGVjdCA6IG51bGxcbnZhciBSZWZsZWN0QXBwbHkgPSBSICYmIHR5cGVvZiBSLmFwcGx5ID09PSAnZnVuY3Rpb24nXG4gID8gUi5hcHBseVxuICA6IGZ1bmN0aW9uIFJlZmxlY3RBcHBseSh0YXJnZXQsIHJlY2VpdmVyLCBhcmdzKSB7XG4gICAgcmV0dXJuIEZ1bmN0aW9uLnByb3RvdHlwZS5hcHBseS5jYWxsKHRhcmdldCwgcmVjZWl2ZXIsIGFyZ3MpO1xuICB9XG5cbnZhciBSZWZsZWN0T3duS2V5c1xuaWYgKFIgJiYgdHlwZW9mIFIub3duS2V5cyA9PT0gJ2Z1bmN0aW9uJykge1xuICBSZWZsZWN0T3duS2V5cyA9IFIub3duS2V5c1xufSBlbHNlIGlmIChPYmplY3QuZ2V0T3duUHJvcGVydHlTeW1ib2xzKSB7XG4gIFJlZmxlY3RPd25LZXlzID0gZnVuY3Rpb24gUmVmbGVjdE93bktleXModGFyZ2V0KSB7XG4gICAgcmV0dXJuIE9iamVjdC5nZXRPd25Qcm9wZXJ0eU5hbWVzKHRhcmdldClcbiAgICAgIC5jb25jYXQoT2JqZWN0LmdldE93blByb3BlcnR5U3ltYm9scyh0YXJnZXQpKTtcbiAgfTtcbn0gZWxzZSB7XG4gIFJlZmxlY3RPd25LZXlzID0gZnVuY3Rpb24gUmVmbGVjdE93bktleXModGFyZ2V0KSB7XG4gICAgcmV0dXJuIE9iamVjdC5nZXRPd25Qcm9wZXJ0eU5hbWVzKHRhcmdldCk7XG4gIH07XG59XG5cbmZ1bmN0aW9uIFByb2Nlc3NFbWl0V2FybmluZyh3YXJuaW5nKSB7XG4gIGlmIChjb25zb2xlICYmIGNvbnNvbGUud2FybikgY29uc29sZS53YXJuKHdhcm5pbmcpO1xufVxuXG52YXIgTnVtYmVySXNOYU4gPSBOdW1iZXIuaXNOYU4gfHwgZnVuY3Rpb24gTnVtYmVySXNOYU4odmFsdWUpIHtcbiAgcmV0dXJuIHZhbHVlICE9PSB2YWx1ZTtcbn1cblxuZnVuY3Rpb24gRXZlbnRFbWl0dGVyKCkge1xuICBFdmVudEVtaXR0ZXIuaW5pdC5jYWxsKHRoaXMpO1xufVxubW9kdWxlLmV4cG9ydHMgPSBFdmVudEVtaXR0ZXI7XG5cbi8vIEJhY2t3YXJkcy1jb21wYXQgd2l0aCBub2RlIDAuMTAueFxuRXZlbnRFbWl0dGVyLkV2ZW50RW1pdHRlciA9IEV2ZW50RW1pdHRlcjtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5fZXZlbnRzID0gdW5kZWZpbmVkO1xuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5fZXZlbnRzQ291bnQgPSAwO1xuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5fbWF4TGlzdGVuZXJzID0gdW5kZWZpbmVkO1xuXG4vLyBCeSBkZWZhdWx0IEV2ZW50RW1pdHRlcnMgd2lsbCBwcmludCBhIHdhcm5pbmcgaWYgbW9yZSB0aGFuIDEwIGxpc3RlbmVycyBhcmVcbi8vIGFkZGVkIHRvIGl0LiBUaGlzIGlzIGEgdXNlZnVsIGRlZmF1bHQgd2hpY2ggaGVscHMgZmluZGluZyBtZW1vcnkgbGVha3MuXG52YXIgZGVmYXVsdE1heExpc3RlbmVycyA9IDEwO1xuXG5PYmplY3QuZGVmaW5lUHJvcGVydHkoRXZlbnRFbWl0dGVyLCAnZGVmYXVsdE1heExpc3RlbmVycycsIHtcbiAgZW51bWVyYWJsZTogdHJ1ZSxcbiAgZ2V0OiBmdW5jdGlvbigpIHtcbiAgICByZXR1cm4gZGVmYXVsdE1heExpc3RlbmVycztcbiAgfSxcbiAgc2V0OiBmdW5jdGlvbihhcmcpIHtcbiAgICBpZiAodHlwZW9mIGFyZyAhPT0gJ251bWJlcicgfHwgYXJnIDwgMCB8fCBOdW1iZXJJc05hTihhcmcpKSB7XG4gICAgICB0aHJvdyBuZXcgUmFuZ2VFcnJvcignVGhlIHZhbHVlIG9mIFwiZGVmYXVsdE1heExpc3RlbmVyc1wiIGlzIG91dCBvZiByYW5nZS4gSXQgbXVzdCBiZSBhIG5vbi1uZWdhdGl2ZSBudW1iZXIuIFJlY2VpdmVkICcgKyBhcmcgKyAnLicpO1xuICAgIH1cbiAgICBkZWZhdWx0TWF4TGlzdGVuZXJzID0gYXJnO1xuICB9XG59KTtcblxuRXZlbnRFbWl0dGVyLmluaXQgPSBmdW5jdGlvbigpIHtcblxuICBpZiAodGhpcy5fZXZlbnRzID09PSB1bmRlZmluZWQgfHxcbiAgICAgIHRoaXMuX2V2ZW50cyA9PT0gT2JqZWN0LmdldFByb3RvdHlwZU9mKHRoaXMpLl9ldmVudHMpIHtcbiAgICB0aGlzLl9ldmVudHMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuICAgIHRoaXMuX2V2ZW50c0NvdW50ID0gMDtcbiAgfVxuXG4gIHRoaXMuX21heExpc3RlbmVycyA9IHRoaXMuX21heExpc3RlbmVycyB8fCB1bmRlZmluZWQ7XG59O1xuXG4vLyBPYnZpb3VzbHkgbm90IGFsbCBFbWl0dGVycyBzaG91bGQgYmUgbGltaXRlZCB0byAxMC4gVGhpcyBmdW5jdGlvbiBhbGxvd3Ncbi8vIHRoYXQgdG8gYmUgaW5jcmVhc2VkLiBTZXQgdG8gemVybyBmb3IgdW5saW1pdGVkLlxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5zZXRNYXhMaXN0ZW5lcnMgPSBmdW5jdGlvbiBzZXRNYXhMaXN0ZW5lcnMobikge1xuICBpZiAodHlwZW9mIG4gIT09ICdudW1iZXInIHx8IG4gPCAwIHx8IE51bWJlcklzTmFOKG4pKSB7XG4gICAgdGhyb3cgbmV3IFJhbmdlRXJyb3IoJ1RoZSB2YWx1ZSBvZiBcIm5cIiBpcyBvdXQgb2YgcmFuZ2UuIEl0IG11c3QgYmUgYSBub24tbmVnYXRpdmUgbnVtYmVyLiBSZWNlaXZlZCAnICsgbiArICcuJyk7XG4gIH1cbiAgdGhpcy5fbWF4TGlzdGVuZXJzID0gbjtcbiAgcmV0dXJuIHRoaXM7XG59O1xuXG5mdW5jdGlvbiAkZ2V0TWF4TGlzdGVuZXJzKHRoYXQpIHtcbiAgaWYgKHRoYXQuX21heExpc3RlbmVycyA9PT0gdW5kZWZpbmVkKVxuICAgIHJldHVybiBFdmVudEVtaXR0ZXIuZGVmYXVsdE1heExpc3RlbmVycztcbiAgcmV0dXJuIHRoYXQuX21heExpc3RlbmVycztcbn1cblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5nZXRNYXhMaXN0ZW5lcnMgPSBmdW5jdGlvbiBnZXRNYXhMaXN0ZW5lcnMoKSB7XG4gIHJldHVybiAkZ2V0TWF4TGlzdGVuZXJzKHRoaXMpO1xufTtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5lbWl0ID0gZnVuY3Rpb24gZW1pdCh0eXBlKSB7XG4gIHZhciBhcmdzID0gW107XG4gIGZvciAodmFyIGkgPSAxOyBpIDwgYXJndW1lbnRzLmxlbmd0aDsgaSsrKSBhcmdzLnB1c2goYXJndW1lbnRzW2ldKTtcbiAgdmFyIGRvRXJyb3IgPSAodHlwZSA9PT0gJ2Vycm9yJyk7XG5cbiAgdmFyIGV2ZW50cyA9IHRoaXMuX2V2ZW50cztcbiAgaWYgKGV2ZW50cyAhPT0gdW5kZWZpbmVkKVxuICAgIGRvRXJyb3IgPSAoZG9FcnJvciAmJiBldmVudHMuZXJyb3IgPT09IHVuZGVmaW5lZCk7XG4gIGVsc2UgaWYgKCFkb0Vycm9yKVxuICAgIHJldHVybiBmYWxzZTtcblxuICAvLyBJZiB0aGVyZSBpcyBubyAnZXJyb3InIGV2ZW50IGxpc3RlbmVyIHRoZW4gdGhyb3cuXG4gIGlmIChkb0Vycm9yKSB7XG4gICAgdmFyIGVyO1xuICAgIGlmIChhcmdzLmxlbmd0aCA+IDApXG4gICAgICBlciA9IGFyZ3NbMF07XG4gICAgaWYgKGVyIGluc3RhbmNlb2YgRXJyb3IpIHtcbiAgICAgIC8vIE5vdGU6IFRoZSBjb21tZW50cyBvbiB0aGUgYHRocm93YCBsaW5lcyBhcmUgaW50ZW50aW9uYWwsIHRoZXkgc2hvd1xuICAgICAgLy8gdXAgaW4gTm9kZSdzIG91dHB1dCBpZiB0aGlzIHJlc3VsdHMgaW4gYW4gdW5oYW5kbGVkIGV4Y2VwdGlvbi5cbiAgICAgIHRocm93IGVyOyAvLyBVbmhhbmRsZWQgJ2Vycm9yJyBldmVudFxuICAgIH1cbiAgICAvLyBBdCBsZWFzdCBnaXZlIHNvbWUga2luZCBvZiBjb250ZXh0IHRvIHRoZSB1c2VyXG4gICAgdmFyIGVyciA9IG5ldyBFcnJvcignVW5oYW5kbGVkIGVycm9yLicgKyAoZXIgPyAnICgnICsgZXIubWVzc2FnZSArICcpJyA6ICcnKSk7XG4gICAgZXJyLmNvbnRleHQgPSBlcjtcbiAgICB0aHJvdyBlcnI7IC8vIFVuaGFuZGxlZCAnZXJyb3InIGV2ZW50XG4gIH1cblxuICB2YXIgaGFuZGxlciA9IGV2ZW50c1t0eXBlXTtcblxuICBpZiAoaGFuZGxlciA9PT0gdW5kZWZpbmVkKVxuICAgIHJldHVybiBmYWxzZTtcblxuICBpZiAodHlwZW9mIGhhbmRsZXIgPT09ICdmdW5jdGlvbicpIHtcbiAgICBSZWZsZWN0QXBwbHkoaGFuZGxlciwgdGhpcywgYXJncyk7XG4gIH0gZWxzZSB7XG4gICAgdmFyIGxlbiA9IGhhbmRsZXIubGVuZ3RoO1xuICAgIHZhciBsaXN0ZW5lcnMgPSBhcnJheUNsb25lKGhhbmRsZXIsIGxlbik7XG4gICAgZm9yICh2YXIgaSA9IDA7IGkgPCBsZW47ICsraSlcbiAgICAgIFJlZmxlY3RBcHBseShsaXN0ZW5lcnNbaV0sIHRoaXMsIGFyZ3MpO1xuICB9XG5cbiAgcmV0dXJuIHRydWU7XG59O1xuXG5mdW5jdGlvbiBfYWRkTGlzdGVuZXIodGFyZ2V0LCB0eXBlLCBsaXN0ZW5lciwgcHJlcGVuZCkge1xuICB2YXIgbTtcbiAgdmFyIGV2ZW50cztcbiAgdmFyIGV4aXN0aW5nO1xuXG4gIGlmICh0eXBlb2YgbGlzdGVuZXIgIT09ICdmdW5jdGlvbicpIHtcbiAgICB0aHJvdyBuZXcgVHlwZUVycm9yKCdUaGUgXCJsaXN0ZW5lclwiIGFyZ3VtZW50IG11c3QgYmUgb2YgdHlwZSBGdW5jdGlvbi4gUmVjZWl2ZWQgdHlwZSAnICsgdHlwZW9mIGxpc3RlbmVyKTtcbiAgfVxuXG4gIGV2ZW50cyA9IHRhcmdldC5fZXZlbnRzO1xuICBpZiAoZXZlbnRzID09PSB1bmRlZmluZWQpIHtcbiAgICBldmVudHMgPSB0YXJnZXQuX2V2ZW50cyA9IE9iamVjdC5jcmVhdGUobnVsbCk7XG4gICAgdGFyZ2V0Ll9ldmVudHNDb3VudCA9IDA7XG4gIH0gZWxzZSB7XG4gICAgLy8gVG8gYXZvaWQgcmVjdXJzaW9uIGluIHRoZSBjYXNlIHRoYXQgdHlwZSA9PT0gXCJuZXdMaXN0ZW5lclwiISBCZWZvcmVcbiAgICAvLyBhZGRpbmcgaXQgdG8gdGhlIGxpc3RlbmVycywgZmlyc3QgZW1pdCBcIm5ld0xpc3RlbmVyXCIuXG4gICAgaWYgKGV2ZW50cy5uZXdMaXN0ZW5lciAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICB0YXJnZXQuZW1pdCgnbmV3TGlzdGVuZXInLCB0eXBlLFxuICAgICAgICAgICAgICAgICAgbGlzdGVuZXIubGlzdGVuZXIgPyBsaXN0ZW5lci5saXN0ZW5lciA6IGxpc3RlbmVyKTtcblxuICAgICAgLy8gUmUtYXNzaWduIGBldmVudHNgIGJlY2F1c2UgYSBuZXdMaXN0ZW5lciBoYW5kbGVyIGNvdWxkIGhhdmUgY2F1c2VkIHRoZVxuICAgICAgLy8gdGhpcy5fZXZlbnRzIHRvIGJlIGFzc2lnbmVkIHRvIGEgbmV3IG9iamVjdFxuICAgICAgZXZlbnRzID0gdGFyZ2V0Ll9ldmVudHM7XG4gICAgfVxuICAgIGV4aXN0aW5nID0gZXZlbnRzW3R5cGVdO1xuICB9XG5cbiAgaWYgKGV4aXN0aW5nID09PSB1bmRlZmluZWQpIHtcbiAgICAvLyBPcHRpbWl6ZSB0aGUgY2FzZSBvZiBvbmUgbGlzdGVuZXIuIERvbid0IG5lZWQgdGhlIGV4dHJhIGFycmF5IG9iamVjdC5cbiAgICBleGlzdGluZyA9IGV2ZW50c1t0eXBlXSA9IGxpc3RlbmVyO1xuICAgICsrdGFyZ2V0Ll9ldmVudHNDb3VudDtcbiAgfSBlbHNlIHtcbiAgICBpZiAodHlwZW9mIGV4aXN0aW5nID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICAvLyBBZGRpbmcgdGhlIHNlY29uZCBlbGVtZW50LCBuZWVkIHRvIGNoYW5nZSB0byBhcnJheS5cbiAgICAgIGV4aXN0aW5nID0gZXZlbnRzW3R5cGVdID1cbiAgICAgICAgcHJlcGVuZCA/IFtsaXN0ZW5lciwgZXhpc3RpbmddIDogW2V4aXN0aW5nLCBsaXN0ZW5lcl07XG4gICAgICAvLyBJZiB3ZSd2ZSBhbHJlYWR5IGdvdCBhbiBhcnJheSwganVzdCBhcHBlbmQuXG4gICAgfSBlbHNlIGlmIChwcmVwZW5kKSB7XG4gICAgICBleGlzdGluZy51bnNoaWZ0KGxpc3RlbmVyKTtcbiAgICB9IGVsc2Uge1xuICAgICAgZXhpc3RpbmcucHVzaChsaXN0ZW5lcik7XG4gICAgfVxuXG4gICAgLy8gQ2hlY2sgZm9yIGxpc3RlbmVyIGxlYWtcbiAgICBtID0gJGdldE1heExpc3RlbmVycyh0YXJnZXQpO1xuICAgIGlmIChtID4gMCAmJiBleGlzdGluZy5sZW5ndGggPiBtICYmICFleGlzdGluZy53YXJuZWQpIHtcbiAgICAgIGV4aXN0aW5nLndhcm5lZCA9IHRydWU7XG4gICAgICAvLyBObyBlcnJvciBjb2RlIGZvciB0aGlzIHNpbmNlIGl0IGlzIGEgV2FybmluZ1xuICAgICAgLy8gZXNsaW50LWRpc2FibGUtbmV4dC1saW5lIG5vLXJlc3RyaWN0ZWQtc3ludGF4XG4gICAgICB2YXIgdyA9IG5ldyBFcnJvcignUG9zc2libGUgRXZlbnRFbWl0dGVyIG1lbW9yeSBsZWFrIGRldGVjdGVkLiAnICtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgZXhpc3RpbmcubGVuZ3RoICsgJyAnICsgU3RyaW5nKHR5cGUpICsgJyBsaXN0ZW5lcnMgJyArXG4gICAgICAgICAgICAgICAgICAgICAgICAgICdhZGRlZC4gVXNlIGVtaXR0ZXIuc2V0TWF4TGlzdGVuZXJzKCkgdG8gJyArXG4gICAgICAgICAgICAgICAgICAgICAgICAgICdpbmNyZWFzZSBsaW1pdCcpO1xuICAgICAgdy5uYW1lID0gJ01heExpc3RlbmVyc0V4Y2VlZGVkV2FybmluZyc7XG4gICAgICB3LmVtaXR0ZXIgPSB0YXJnZXQ7XG4gICAgICB3LnR5cGUgPSB0eXBlO1xuICAgICAgdy5jb3VudCA9IGV4aXN0aW5nLmxlbmd0aDtcbiAgICAgIFByb2Nlc3NFbWl0V2FybmluZyh3KTtcbiAgICB9XG4gIH1cblxuICByZXR1cm4gdGFyZ2V0O1xufVxuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLmFkZExpc3RlbmVyID0gZnVuY3Rpb24gYWRkTGlzdGVuZXIodHlwZSwgbGlzdGVuZXIpIHtcbiAgcmV0dXJuIF9hZGRMaXN0ZW5lcih0aGlzLCB0eXBlLCBsaXN0ZW5lciwgZmFsc2UpO1xufTtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5vbiA9IEV2ZW50RW1pdHRlci5wcm90b3R5cGUuYWRkTGlzdGVuZXI7XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUucHJlcGVuZExpc3RlbmVyID1cbiAgICBmdW5jdGlvbiBwcmVwZW5kTGlzdGVuZXIodHlwZSwgbGlzdGVuZXIpIHtcbiAgICAgIHJldHVybiBfYWRkTGlzdGVuZXIodGhpcywgdHlwZSwgbGlzdGVuZXIsIHRydWUpO1xuICAgIH07XG5cbmZ1bmN0aW9uIG9uY2VXcmFwcGVyKCkge1xuICB2YXIgYXJncyA9IFtdO1xuICBmb3IgKHZhciBpID0gMDsgaSA8IGFyZ3VtZW50cy5sZW5ndGg7IGkrKykgYXJncy5wdXNoKGFyZ3VtZW50c1tpXSk7XG4gIGlmICghdGhpcy5maXJlZCkge1xuICAgIHRoaXMudGFyZ2V0LnJlbW92ZUxpc3RlbmVyKHRoaXMudHlwZSwgdGhpcy53cmFwRm4pO1xuICAgIHRoaXMuZmlyZWQgPSB0cnVlO1xuICAgIFJlZmxlY3RBcHBseSh0aGlzLmxpc3RlbmVyLCB0aGlzLnRhcmdldCwgYXJncyk7XG4gIH1cbn1cblxuZnVuY3Rpb24gX29uY2VXcmFwKHRhcmdldCwgdHlwZSwgbGlzdGVuZXIpIHtcbiAgdmFyIHN0YXRlID0geyBmaXJlZDogZmFsc2UsIHdyYXBGbjogdW5kZWZpbmVkLCB0YXJnZXQ6IHRhcmdldCwgdHlwZTogdHlwZSwgbGlzdGVuZXI6IGxpc3RlbmVyIH07XG4gIHZhciB3cmFwcGVkID0gb25jZVdyYXBwZXIuYmluZChzdGF0ZSk7XG4gIHdyYXBwZWQubGlzdGVuZXIgPSBsaXN0ZW5lcjtcbiAgc3RhdGUud3JhcEZuID0gd3JhcHBlZDtcbiAgcmV0dXJuIHdyYXBwZWQ7XG59XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUub25jZSA9IGZ1bmN0aW9uIG9uY2UodHlwZSwgbGlzdGVuZXIpIHtcbiAgaWYgKHR5cGVvZiBsaXN0ZW5lciAhPT0gJ2Z1bmN0aW9uJykge1xuICAgIHRocm93IG5ldyBUeXBlRXJyb3IoJ1RoZSBcImxpc3RlbmVyXCIgYXJndW1lbnQgbXVzdCBiZSBvZiB0eXBlIEZ1bmN0aW9uLiBSZWNlaXZlZCB0eXBlICcgKyB0eXBlb2YgbGlzdGVuZXIpO1xuICB9XG4gIHRoaXMub24odHlwZSwgX29uY2VXcmFwKHRoaXMsIHR5cGUsIGxpc3RlbmVyKSk7XG4gIHJldHVybiB0aGlzO1xufTtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5wcmVwZW5kT25jZUxpc3RlbmVyID1cbiAgICBmdW5jdGlvbiBwcmVwZW5kT25jZUxpc3RlbmVyKHR5cGUsIGxpc3RlbmVyKSB7XG4gICAgICBpZiAodHlwZW9mIGxpc3RlbmVyICE9PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgIHRocm93IG5ldyBUeXBlRXJyb3IoJ1RoZSBcImxpc3RlbmVyXCIgYXJndW1lbnQgbXVzdCBiZSBvZiB0eXBlIEZ1bmN0aW9uLiBSZWNlaXZlZCB0eXBlICcgKyB0eXBlb2YgbGlzdGVuZXIpO1xuICAgICAgfVxuICAgICAgdGhpcy5wcmVwZW5kTGlzdGVuZXIodHlwZSwgX29uY2VXcmFwKHRoaXMsIHR5cGUsIGxpc3RlbmVyKSk7XG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9O1xuXG4vLyBFbWl0cyBhICdyZW1vdmVMaXN0ZW5lcicgZXZlbnQgaWYgYW5kIG9ubHkgaWYgdGhlIGxpc3RlbmVyIHdhcyByZW1vdmVkLlxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5yZW1vdmVMaXN0ZW5lciA9XG4gICAgZnVuY3Rpb24gcmVtb3ZlTGlzdGVuZXIodHlwZSwgbGlzdGVuZXIpIHtcbiAgICAgIHZhciBsaXN0LCBldmVudHMsIHBvc2l0aW9uLCBpLCBvcmlnaW5hbExpc3RlbmVyO1xuXG4gICAgICBpZiAodHlwZW9mIGxpc3RlbmVyICE9PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgIHRocm93IG5ldyBUeXBlRXJyb3IoJ1RoZSBcImxpc3RlbmVyXCIgYXJndW1lbnQgbXVzdCBiZSBvZiB0eXBlIEZ1bmN0aW9uLiBSZWNlaXZlZCB0eXBlICcgKyB0eXBlb2YgbGlzdGVuZXIpO1xuICAgICAgfVxuXG4gICAgICBldmVudHMgPSB0aGlzLl9ldmVudHM7XG4gICAgICBpZiAoZXZlbnRzID09PSB1bmRlZmluZWQpXG4gICAgICAgIHJldHVybiB0aGlzO1xuXG4gICAgICBsaXN0ID0gZXZlbnRzW3R5cGVdO1xuICAgICAgaWYgKGxpc3QgPT09IHVuZGVmaW5lZClcbiAgICAgICAgcmV0dXJuIHRoaXM7XG5cbiAgICAgIGlmIChsaXN0ID09PSBsaXN0ZW5lciB8fCBsaXN0Lmxpc3RlbmVyID09PSBsaXN0ZW5lcikge1xuICAgICAgICBpZiAoLS10aGlzLl9ldmVudHNDb3VudCA9PT0gMClcbiAgICAgICAgICB0aGlzLl9ldmVudHMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuICAgICAgICBlbHNlIHtcbiAgICAgICAgICBkZWxldGUgZXZlbnRzW3R5cGVdO1xuICAgICAgICAgIGlmIChldmVudHMucmVtb3ZlTGlzdGVuZXIpXG4gICAgICAgICAgICB0aGlzLmVtaXQoJ3JlbW92ZUxpc3RlbmVyJywgdHlwZSwgbGlzdC5saXN0ZW5lciB8fCBsaXN0ZW5lcik7XG4gICAgICAgIH1cbiAgICAgIH0gZWxzZSBpZiAodHlwZW9mIGxpc3QgIT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgcG9zaXRpb24gPSAtMTtcblxuICAgICAgICBmb3IgKGkgPSBsaXN0Lmxlbmd0aCAtIDE7IGkgPj0gMDsgaS0tKSB7XG4gICAgICAgICAgaWYgKGxpc3RbaV0gPT09IGxpc3RlbmVyIHx8IGxpc3RbaV0ubGlzdGVuZXIgPT09IGxpc3RlbmVyKSB7XG4gICAgICAgICAgICBvcmlnaW5hbExpc3RlbmVyID0gbGlzdFtpXS5saXN0ZW5lcjtcbiAgICAgICAgICAgIHBvc2l0aW9uID0gaTtcbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIGlmIChwb3NpdGlvbiA8IDApXG4gICAgICAgICAgcmV0dXJuIHRoaXM7XG5cbiAgICAgICAgaWYgKHBvc2l0aW9uID09PSAwKVxuICAgICAgICAgIGxpc3Quc2hpZnQoKTtcbiAgICAgICAgZWxzZSB7XG4gICAgICAgICAgc3BsaWNlT25lKGxpc3QsIHBvc2l0aW9uKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmIChsaXN0Lmxlbmd0aCA9PT0gMSlcbiAgICAgICAgICBldmVudHNbdHlwZV0gPSBsaXN0WzBdO1xuXG4gICAgICAgIGlmIChldmVudHMucmVtb3ZlTGlzdGVuZXIgIT09IHVuZGVmaW5lZClcbiAgICAgICAgICB0aGlzLmVtaXQoJ3JlbW92ZUxpc3RlbmVyJywgdHlwZSwgb3JpZ2luYWxMaXN0ZW5lciB8fCBsaXN0ZW5lcik7XG4gICAgICB9XG5cbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUub2ZmID0gRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5yZW1vdmVMaXN0ZW5lcjtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5yZW1vdmVBbGxMaXN0ZW5lcnMgPVxuICAgIGZ1bmN0aW9uIHJlbW92ZUFsbExpc3RlbmVycyh0eXBlKSB7XG4gICAgICB2YXIgbGlzdGVuZXJzLCBldmVudHMsIGk7XG5cbiAgICAgIGV2ZW50cyA9IHRoaXMuX2V2ZW50cztcbiAgICAgIGlmIChldmVudHMgPT09IHVuZGVmaW5lZClcbiAgICAgICAgcmV0dXJuIHRoaXM7XG5cbiAgICAgIC8vIG5vdCBsaXN0ZW5pbmcgZm9yIHJlbW92ZUxpc3RlbmVyLCBubyBuZWVkIHRvIGVtaXRcbiAgICAgIGlmIChldmVudHMucmVtb3ZlTGlzdGVuZXIgPT09IHVuZGVmaW5lZCkge1xuICAgICAgICBpZiAoYXJndW1lbnRzLmxlbmd0aCA9PT0gMCkge1xuICAgICAgICAgIHRoaXMuX2V2ZW50cyA9IE9iamVjdC5jcmVhdGUobnVsbCk7XG4gICAgICAgICAgdGhpcy5fZXZlbnRzQ291bnQgPSAwO1xuICAgICAgICB9IGVsc2UgaWYgKGV2ZW50c1t0eXBlXSAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICAgICAgaWYgKC0tdGhpcy5fZXZlbnRzQ291bnQgPT09IDApXG4gICAgICAgICAgICB0aGlzLl9ldmVudHMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuICAgICAgICAgIGVsc2VcbiAgICAgICAgICAgIGRlbGV0ZSBldmVudHNbdHlwZV07XG4gICAgICAgIH1cbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgICB9XG5cbiAgICAgIC8vIGVtaXQgcmVtb3ZlTGlzdGVuZXIgZm9yIGFsbCBsaXN0ZW5lcnMgb24gYWxsIGV2ZW50c1xuICAgICAgaWYgKGFyZ3VtZW50cy5sZW5ndGggPT09IDApIHtcbiAgICAgICAgdmFyIGtleXMgPSBPYmplY3Qua2V5cyhldmVudHMpO1xuICAgICAgICB2YXIga2V5O1xuICAgICAgICBmb3IgKGkgPSAwOyBpIDwga2V5cy5sZW5ndGg7ICsraSkge1xuICAgICAgICAgIGtleSA9IGtleXNbaV07XG4gICAgICAgICAgaWYgKGtleSA9PT0gJ3JlbW92ZUxpc3RlbmVyJykgY29udGludWU7XG4gICAgICAgICAgdGhpcy5yZW1vdmVBbGxMaXN0ZW5lcnMoa2V5KTtcbiAgICAgICAgfVxuICAgICAgICB0aGlzLnJlbW92ZUFsbExpc3RlbmVycygncmVtb3ZlTGlzdGVuZXInKTtcbiAgICAgICAgdGhpcy5fZXZlbnRzID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiAgICAgICAgdGhpcy5fZXZlbnRzQ291bnQgPSAwO1xuICAgICAgICByZXR1cm4gdGhpcztcbiAgICAgIH1cblxuICAgICAgbGlzdGVuZXJzID0gZXZlbnRzW3R5cGVdO1xuXG4gICAgICBpZiAodHlwZW9mIGxpc3RlbmVycyA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICB0aGlzLnJlbW92ZUxpc3RlbmVyKHR5cGUsIGxpc3RlbmVycyk7XG4gICAgICB9IGVsc2UgaWYgKGxpc3RlbmVycyAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICAgIC8vIExJRk8gb3JkZXJcbiAgICAgICAgZm9yIChpID0gbGlzdGVuZXJzLmxlbmd0aCAtIDE7IGkgPj0gMDsgaS0tKSB7XG4gICAgICAgICAgdGhpcy5yZW1vdmVMaXN0ZW5lcih0eXBlLCBsaXN0ZW5lcnNbaV0pO1xuICAgICAgICB9XG4gICAgICB9XG5cbiAgICAgIHJldHVybiB0aGlzO1xuICAgIH07XG5cbmZ1bmN0aW9uIF9saXN0ZW5lcnModGFyZ2V0LCB0eXBlLCB1bndyYXApIHtcbiAgdmFyIGV2ZW50cyA9IHRhcmdldC5fZXZlbnRzO1xuXG4gIGlmIChldmVudHMgPT09IHVuZGVmaW5lZClcbiAgICByZXR1cm4gW107XG5cbiAgdmFyIGV2bGlzdGVuZXIgPSBldmVudHNbdHlwZV07XG4gIGlmIChldmxpc3RlbmVyID09PSB1bmRlZmluZWQpXG4gICAgcmV0dXJuIFtdO1xuXG4gIGlmICh0eXBlb2YgZXZsaXN0ZW5lciA9PT0gJ2Z1bmN0aW9uJylcbiAgICByZXR1cm4gdW53cmFwID8gW2V2bGlzdGVuZXIubGlzdGVuZXIgfHwgZXZsaXN0ZW5lcl0gOiBbZXZsaXN0ZW5lcl07XG5cbiAgcmV0dXJuIHVud3JhcCA/XG4gICAgdW53cmFwTGlzdGVuZXJzKGV2bGlzdGVuZXIpIDogYXJyYXlDbG9uZShldmxpc3RlbmVyLCBldmxpc3RlbmVyLmxlbmd0aCk7XG59XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUubGlzdGVuZXJzID0gZnVuY3Rpb24gbGlzdGVuZXJzKHR5cGUpIHtcbiAgcmV0dXJuIF9saXN0ZW5lcnModGhpcywgdHlwZSwgdHJ1ZSk7XG59O1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLnJhd0xpc3RlbmVycyA9IGZ1bmN0aW9uIHJhd0xpc3RlbmVycyh0eXBlKSB7XG4gIHJldHVybiBfbGlzdGVuZXJzKHRoaXMsIHR5cGUsIGZhbHNlKTtcbn07XG5cbkV2ZW50RW1pdHRlci5saXN0ZW5lckNvdW50ID0gZnVuY3Rpb24oZW1pdHRlciwgdHlwZSkge1xuICBpZiAodHlwZW9mIGVtaXR0ZXIubGlzdGVuZXJDb3VudCA9PT0gJ2Z1bmN0aW9uJykge1xuICAgIHJldHVybiBlbWl0dGVyLmxpc3RlbmVyQ291bnQodHlwZSk7XG4gIH0gZWxzZSB7XG4gICAgcmV0dXJuIGxpc3RlbmVyQ291bnQuY2FsbChlbWl0dGVyLCB0eXBlKTtcbiAgfVxufTtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5saXN0ZW5lckNvdW50ID0gbGlzdGVuZXJDb3VudDtcbmZ1bmN0aW9uIGxpc3RlbmVyQ291bnQodHlwZSkge1xuICB2YXIgZXZlbnRzID0gdGhpcy5fZXZlbnRzO1xuXG4gIGlmIChldmVudHMgIT09IHVuZGVmaW5lZCkge1xuICAgIHZhciBldmxpc3RlbmVyID0gZXZlbnRzW3R5cGVdO1xuXG4gICAgaWYgKHR5cGVvZiBldmxpc3RlbmVyID09PSAnZnVuY3Rpb24nKSB7XG4gICAgICByZXR1cm4gMTtcbiAgICB9IGVsc2UgaWYgKGV2bGlzdGVuZXIgIT09IHVuZGVmaW5lZCkge1xuICAgICAgcmV0dXJuIGV2bGlzdGVuZXIubGVuZ3RoO1xuICAgIH1cbiAgfVxuXG4gIHJldHVybiAwO1xufVxuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLmV2ZW50TmFtZXMgPSBmdW5jdGlvbiBldmVudE5hbWVzKCkge1xuICByZXR1cm4gdGhpcy5fZXZlbnRzQ291bnQgPiAwID8gUmVmbGVjdE93bktleXModGhpcy5fZXZlbnRzKSA6IFtdO1xufTtcblxuZnVuY3Rpb24gYXJyYXlDbG9uZShhcnIsIG4pIHtcbiAgdmFyIGNvcHkgPSBuZXcgQXJyYXkobik7XG4gIGZvciAodmFyIGkgPSAwOyBpIDwgbjsgKytpKVxuICAgIGNvcHlbaV0gPSBhcnJbaV07XG4gIHJldHVybiBjb3B5O1xufVxuXG5mdW5jdGlvbiBzcGxpY2VPbmUobGlzdCwgaW5kZXgpIHtcbiAgZm9yICg7IGluZGV4ICsgMSA8IGxpc3QubGVuZ3RoOyBpbmRleCsrKVxuICAgIGxpc3RbaW5kZXhdID0gbGlzdFtpbmRleCArIDFdO1xuICBsaXN0LnBvcCgpO1xufVxuXG5mdW5jdGlvbiB1bndyYXBMaXN0ZW5lcnMoYXJyKSB7XG4gIHZhciByZXQgPSBuZXcgQXJyYXkoYXJyLmxlbmd0aCk7XG4gIGZvciAodmFyIGkgPSAwOyBpIDwgcmV0Lmxlbmd0aDsgKytpKSB7XG4gICAgcmV0W2ldID0gYXJyW2ldLmxpc3RlbmVyIHx8IGFycltpXTtcbiAgfVxuICByZXR1cm4gcmV0O1xufVxuXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2V2ZW50cy9ldmVudHMuanNcbi8vIG1vZHVsZSBpZCA9IDUzXG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDcgMTAgMTEgMTIgMTUgMTYgMTcgMTggMjEgMjMgMjQgMjUgMzQgNDEgNDMgNDYgNDggNTAgNTEiLCJ2YXIgaGFzICAgICAgICAgID0gcmVxdWlyZSgnLi9faGFzJylcbiAgLCB0b0lPYmplY3QgICAgPSByZXF1aXJlKCcuL190by1pb2JqZWN0JylcbiAgLCBhcnJheUluZGV4T2YgPSByZXF1aXJlKCcuL19hcnJheS1pbmNsdWRlcycpKGZhbHNlKVxuICAsIElFX1BST1RPICAgICA9IHJlcXVpcmUoJy4vX3NoYXJlZC1rZXknKSgnSUVfUFJPVE8nKTtcblxubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihvYmplY3QsIG5hbWVzKXtcbiAgdmFyIE8gICAgICA9IHRvSU9iamVjdChvYmplY3QpXG4gICAgLCBpICAgICAgPSAwXG4gICAgLCByZXN1bHQgPSBbXVxuICAgICwga2V5O1xuICBmb3Ioa2V5IGluIE8paWYoa2V5ICE9IElFX1BST1RPKWhhcyhPLCBrZXkpICYmIHJlc3VsdC5wdXNoKGtleSk7XG4gIC8vIERvbid0IGVudW0gYnVnICYgaGlkZGVuIGtleXNcbiAgd2hpbGUobmFtZXMubGVuZ3RoID4gaSlpZihoYXMoTywga2V5ID0gbmFtZXNbaSsrXSkpe1xuICAgIH5hcnJheUluZGV4T2YocmVzdWx0LCBrZXkpIHx8IHJlc3VsdC5wdXNoKGtleSk7XG4gIH1cbiAgcmV0dXJuIHJlc3VsdDtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3Qta2V5cy1pbnRlcm5hbC5qc1xuLy8gbW9kdWxlIGlkID0gNTVcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwiLy8gNy4xLjE1IFRvTGVuZ3RoXG52YXIgdG9JbnRlZ2VyID0gcmVxdWlyZSgnLi9fdG8taW50ZWdlcicpXG4gICwgbWluICAgICAgID0gTWF0aC5taW47XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgcmV0dXJuIGl0ID4gMCA/IG1pbih0b0ludGVnZXIoaXQpLCAweDFmZmZmZmZmZmZmZmZmKSA6IDA7IC8vIHBvdygyLCA1MykgLSAxID09IDkwMDcxOTkyNTQ3NDA5OTFcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL190by1sZW5ndGguanNcbi8vIG1vZHVsZSBpZCA9IDU2XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxOCAxOSAyMCIsImV4cG9ydHMuZiA9IE9iamVjdC5nZXRPd25Qcm9wZXJ0eVN5bWJvbHM7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtZ29wcy5qc1xuLy8gbW9kdWxlIGlkID0gNTdcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE1IDE2IDE4IiwiLy8gZmFsc2UgLT4gQXJyYXkjaW5kZXhPZlxuLy8gdHJ1ZSAgLT4gQXJyYXkjaW5jbHVkZXNcbnZhciB0b0lPYmplY3QgPSByZXF1aXJlKCcuL190by1pb2JqZWN0JylcbiAgLCB0b0xlbmd0aCAgPSByZXF1aXJlKCcuL190by1sZW5ndGgnKVxuICAsIHRvSW5kZXggICA9IHJlcXVpcmUoJy4vX3RvLWluZGV4Jyk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKElTX0lOQ0xVREVTKXtcbiAgcmV0dXJuIGZ1bmN0aW9uKCR0aGlzLCBlbCwgZnJvbUluZGV4KXtcbiAgICB2YXIgTyAgICAgID0gdG9JT2JqZWN0KCR0aGlzKVxuICAgICAgLCBsZW5ndGggPSB0b0xlbmd0aChPLmxlbmd0aClcbiAgICAgICwgaW5kZXggID0gdG9JbmRleChmcm9tSW5kZXgsIGxlbmd0aClcbiAgICAgICwgdmFsdWU7XG4gICAgLy8gQXJyYXkjaW5jbHVkZXMgdXNlcyBTYW1lVmFsdWVaZXJvIGVxdWFsaXR5IGFsZ29yaXRobVxuICAgIGlmKElTX0lOQ0xVREVTICYmIGVsICE9IGVsKXdoaWxlKGxlbmd0aCA+IGluZGV4KXtcbiAgICAgIHZhbHVlID0gT1tpbmRleCsrXTtcbiAgICAgIGlmKHZhbHVlICE9IHZhbHVlKXJldHVybiB0cnVlO1xuICAgIC8vIEFycmF5I3RvSW5kZXggaWdub3JlcyBob2xlcywgQXJyYXkjaW5jbHVkZXMgLSBub3RcbiAgICB9IGVsc2UgZm9yKDtsZW5ndGggPiBpbmRleDsgaW5kZXgrKylpZihJU19JTkNMVURFUyB8fCBpbmRleCBpbiBPKXtcbiAgICAgIGlmKE9baW5kZXhdID09PSBlbClyZXR1cm4gSVNfSU5DTFVERVMgfHwgaW5kZXggfHwgMDtcbiAgICB9IHJldHVybiAhSVNfSU5DTFVERVMgJiYgLTE7XG4gIH07XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fYXJyYXktaW5jbHVkZXMuanNcbi8vIG1vZHVsZSBpZCA9IDU4XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxOCAxOSAyMCIsIm1vZHVsZS5leHBvcnRzID0geyBcImRlZmF1bHRcIjogcmVxdWlyZShcImNvcmUtanMvbGlicmFyeS9mbi9qc29uL3N0cmluZ2lmeVwiKSwgX19lc01vZHVsZTogdHJ1ZSB9O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvanNvbi9zdHJpbmdpZnkuanNcbi8vIG1vZHVsZSBpZCA9IDU4M1xuLy8gbW9kdWxlIGNodW5rcyA9IDEwIiwidmFyIGNvcmUgID0gcmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9fY29yZScpXG4gICwgJEpTT04gPSBjb3JlLkpTT04gfHwgKGNvcmUuSlNPTiA9IHtzdHJpbmdpZnk6IEpTT04uc3RyaW5naWZ5fSk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uIHN0cmluZ2lmeShpdCl7IC8vIGVzbGludC1kaXNhYmxlLWxpbmUgbm8tdW51c2VkLXZhcnNcbiAgcmV0dXJuICRKU09OLnN0cmluZ2lmeS5hcHBseSgkSlNPTiwgYXJndW1lbnRzKTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9mbi9qc29uL3N0cmluZ2lmeS5qc1xuLy8gbW9kdWxlIGlkID0gNTg3XG4vLyBtb2R1bGUgY2h1bmtzID0gMTAiLCJ2YXIgdG9JbnRlZ2VyID0gcmVxdWlyZSgnLi9fdG8taW50ZWdlcicpXG4gICwgbWF4ICAgICAgID0gTWF0aC5tYXhcbiAgLCBtaW4gICAgICAgPSBNYXRoLm1pbjtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaW5kZXgsIGxlbmd0aCl7XG4gIGluZGV4ID0gdG9JbnRlZ2VyKGluZGV4KTtcbiAgcmV0dXJuIGluZGV4IDwgMCA/IG1heChpbmRleCArIGxlbmd0aCwgMCkgOiBtaW4oaW5kZXgsIGxlbmd0aCk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fdG8taW5kZXguanNcbi8vIG1vZHVsZSBpZCA9IDU5XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxOCAxOSAyMCIsInZhciBhbk9iamVjdCAgICAgICA9IHJlcXVpcmUoJy4vX2FuLW9iamVjdCcpXG4gICwgSUU4X0RPTV9ERUZJTkUgPSByZXF1aXJlKCcuL19pZTgtZG9tLWRlZmluZScpXG4gICwgdG9QcmltaXRpdmUgICAgPSByZXF1aXJlKCcuL190by1wcmltaXRpdmUnKVxuICAsIGRQICAgICAgICAgICAgID0gT2JqZWN0LmRlZmluZVByb3BlcnR5O1xuXG5leHBvcnRzLmYgPSByZXF1aXJlKCcuL19kZXNjcmlwdG9ycycpID8gT2JqZWN0LmRlZmluZVByb3BlcnR5IDogZnVuY3Rpb24gZGVmaW5lUHJvcGVydHkoTywgUCwgQXR0cmlidXRlcyl7XG4gIGFuT2JqZWN0KE8pO1xuICBQID0gdG9QcmltaXRpdmUoUCwgdHJ1ZSk7XG4gIGFuT2JqZWN0KEF0dHJpYnV0ZXMpO1xuICBpZihJRThfRE9NX0RFRklORSl0cnkge1xuICAgIHJldHVybiBkUChPLCBQLCBBdHRyaWJ1dGVzKTtcbiAgfSBjYXRjaChlKXsgLyogZW1wdHkgKi8gfVxuICBpZignZ2V0JyBpbiBBdHRyaWJ1dGVzIHx8ICdzZXQnIGluIEF0dHJpYnV0ZXMpdGhyb3cgVHlwZUVycm9yKCdBY2Nlc3NvcnMgbm90IHN1cHBvcnRlZCEnKTtcbiAgaWYoJ3ZhbHVlJyBpbiBBdHRyaWJ1dGVzKU9bUF0gPSBBdHRyaWJ1dGVzLnZhbHVlO1xuICByZXR1cm4gTztcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtZHAuanNcbi8vIG1vZHVsZSBpZCA9IDZcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwibW9kdWxlLmV4cG9ydHMgPSB7IFwiZGVmYXVsdFwiOiByZXF1aXJlKFwiY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC9rZXlzXCIpLCBfX2VzTW9kdWxlOiB0cnVlIH07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9vYmplY3Qva2V5cy5qc1xuLy8gbW9kdWxlIGlkID0gNjdcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA4IDkgMTAgMTUgMTkgMjAiLCJtb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGV4ZWMpe1xuICB0cnkge1xuICAgIHJldHVybiAhIWV4ZWMoKTtcbiAgfSBjYXRjaChlKXtcbiAgICByZXR1cm4gdHJ1ZTtcbiAgfVxufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2ZhaWxzLmpzXG4vLyBtb2R1bGUgaWQgPSA3XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIi8qKlxuICogMjAwNy0yMDIwIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMjAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBSb3V0aW5nIGZyb20gJ2Zvcy1yb3V0aW5nJztcbmltcG9ydCByb3V0ZXMgZnJvbSAnQGpzL2Zvc19qc19yb3V0ZXMuanNvbic7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBXcmFwcyBGT1NKc1JvdXRpbmdidW5kbGUgd2l0aCBleHBvc2VkIHJvdXRlcy5cbiAqIFRvIGV4cG9zZSByb3V0ZSBhZGQgb3B0aW9uIGBleHBvc2U6IHRydWVgIGluIC55bWwgcm91dGluZyBjb25maWdcbiAqXG4gKiBlLmcuXG4gKlxuICogYG15X3JvdXRlXG4gKiAgICBwYXRoOiAvbXktcGF0aFxuICogICAgb3B0aW9uczpcbiAqICAgICAgZXhwb3NlOiB0cnVlXG4gKiBgXG4gKiBBbmQgcnVuIGBiaW4vY29uc29sZSBmb3M6anMtcm91dGluZzpkdW1wIC0tZm9ybWF0PWpzb24gLS10YXJnZXQ9YWRtaW4tZGV2L3RoZW1lcy9uZXctdGhlbWUvanMvZm9zX2pzX3JvdXRlcy5qc29uYFxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBSb3V0ZXIge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICBSb3V0aW5nLnNldERhdGEocm91dGVzKTtcbiAgICBSb3V0aW5nLnNldEJhc2VVcmwoJChkb2N1bWVudCkuZmluZCgnYm9keScpLmRhdGEoJ2Jhc2UtdXJsJykpO1xuXG4gICAgcmV0dXJuIHRoaXM7XG4gIH1cblxuICAvKipcbiAgICogRGVjb3JhdGVkIFwiZ2VuZXJhdGVcIiBtZXRob2QsIHdpdGggcHJlZGVmaW5lZCBzZWN1cml0eSB0b2tlbiBpbiBwYXJhbXNcbiAgICpcbiAgICogQHBhcmFtIHJvdXRlXG4gICAqIEBwYXJhbSBwYXJhbXNcbiAgICpcbiAgICogQHJldHVybnMge1N0cmluZ31cbiAgICovXG4gIGdlbmVyYXRlKHJvdXRlLCBwYXJhbXMgPSB7fSkge1xuICAgIGNvbnN0IHRva2VuaXplZFBhcmFtcyA9IE9iamVjdC5hc3NpZ24ocGFyYW1zLCB7X3Rva2VuOiAkKGRvY3VtZW50KS5maW5kKCdib2R5JykuZGF0YSgndG9rZW4nKX0pO1xuXG4gICAgcmV0dXJuIFJvdXRpbmcuZ2VuZXJhdGUocm91dGUsIHRva2VuaXplZFBhcmFtcyk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvcm91dGVyLmpzIiwiLy8gbW9zdCBPYmplY3QgbWV0aG9kcyBieSBFUzYgc2hvdWxkIGFjY2VwdCBwcmltaXRpdmVzXG52YXIgJGV4cG9ydCA9IHJlcXVpcmUoJy4vX2V4cG9ydCcpXG4gICwgY29yZSAgICA9IHJlcXVpcmUoJy4vX2NvcmUnKVxuICAsIGZhaWxzICAgPSByZXF1aXJlKCcuL19mYWlscycpO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihLRVksIGV4ZWMpe1xuICB2YXIgZm4gID0gKGNvcmUuT2JqZWN0IHx8IHt9KVtLRVldIHx8IE9iamVjdFtLRVldXG4gICAgLCBleHAgPSB7fTtcbiAgZXhwW0tFWV0gPSBleGVjKGZuKTtcbiAgJGV4cG9ydCgkZXhwb3J0LlMgKyAkZXhwb3J0LkYgKiBmYWlscyhmdW5jdGlvbigpeyBmbigxKTsgfSksICdPYmplY3QnLCBleHApO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1zYXAuanNcbi8vIG1vZHVsZSBpZCA9IDc3XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgOCA5IDEwIDE1IDE5IDIwIiwidmFyIGdsb2JhbCAgICA9IHJlcXVpcmUoJy4vX2dsb2JhbCcpXG4gICwgY29yZSAgICAgID0gcmVxdWlyZSgnLi9fY29yZScpXG4gICwgY3R4ICAgICAgID0gcmVxdWlyZSgnLi9fY3R4JylcbiAgLCBoaWRlICAgICAgPSByZXF1aXJlKCcuL19oaWRlJylcbiAgLCBQUk9UT1RZUEUgPSAncHJvdG90eXBlJztcblxudmFyICRleHBvcnQgPSBmdW5jdGlvbih0eXBlLCBuYW1lLCBzb3VyY2Upe1xuICB2YXIgSVNfRk9SQ0VEID0gdHlwZSAmICRleHBvcnQuRlxuICAgICwgSVNfR0xPQkFMID0gdHlwZSAmICRleHBvcnQuR1xuICAgICwgSVNfU1RBVElDID0gdHlwZSAmICRleHBvcnQuU1xuICAgICwgSVNfUFJPVE8gID0gdHlwZSAmICRleHBvcnQuUFxuICAgICwgSVNfQklORCAgID0gdHlwZSAmICRleHBvcnQuQlxuICAgICwgSVNfV1JBUCAgID0gdHlwZSAmICRleHBvcnQuV1xuICAgICwgZXhwb3J0cyAgID0gSVNfR0xPQkFMID8gY29yZSA6IGNvcmVbbmFtZV0gfHwgKGNvcmVbbmFtZV0gPSB7fSlcbiAgICAsIGV4cFByb3RvICA9IGV4cG9ydHNbUFJPVE9UWVBFXVxuICAgICwgdGFyZ2V0ICAgID0gSVNfR0xPQkFMID8gZ2xvYmFsIDogSVNfU1RBVElDID8gZ2xvYmFsW25hbWVdIDogKGdsb2JhbFtuYW1lXSB8fCB7fSlbUFJPVE9UWVBFXVxuICAgICwga2V5LCBvd24sIG91dDtcbiAgaWYoSVNfR0xPQkFMKXNvdXJjZSA9IG5hbWU7XG4gIGZvcihrZXkgaW4gc291cmNlKXtcbiAgICAvLyBjb250YWlucyBpbiBuYXRpdmVcbiAgICBvd24gPSAhSVNfRk9SQ0VEICYmIHRhcmdldCAmJiB0YXJnZXRba2V5XSAhPT0gdW5kZWZpbmVkO1xuICAgIGlmKG93biAmJiBrZXkgaW4gZXhwb3J0cyljb250aW51ZTtcbiAgICAvLyBleHBvcnQgbmF0aXZlIG9yIHBhc3NlZFxuICAgIG91dCA9IG93biA/IHRhcmdldFtrZXldIDogc291cmNlW2tleV07XG4gICAgLy8gcHJldmVudCBnbG9iYWwgcG9sbHV0aW9uIGZvciBuYW1lc3BhY2VzXG4gICAgZXhwb3J0c1trZXldID0gSVNfR0xPQkFMICYmIHR5cGVvZiB0YXJnZXRba2V5XSAhPSAnZnVuY3Rpb24nID8gc291cmNlW2tleV1cbiAgICAvLyBiaW5kIHRpbWVycyB0byBnbG9iYWwgZm9yIGNhbGwgZnJvbSBleHBvcnQgY29udGV4dFxuICAgIDogSVNfQklORCAmJiBvd24gPyBjdHgob3V0LCBnbG9iYWwpXG4gICAgLy8gd3JhcCBnbG9iYWwgY29uc3RydWN0b3JzIGZvciBwcmV2ZW50IGNoYW5nZSB0aGVtIGluIGxpYnJhcnlcbiAgICA6IElTX1dSQVAgJiYgdGFyZ2V0W2tleV0gPT0gb3V0ID8gKGZ1bmN0aW9uKEMpe1xuICAgICAgdmFyIEYgPSBmdW5jdGlvbihhLCBiLCBjKXtcbiAgICAgICAgaWYodGhpcyBpbnN0YW5jZW9mIEMpe1xuICAgICAgICAgIHN3aXRjaChhcmd1bWVudHMubGVuZ3RoKXtcbiAgICAgICAgICAgIGNhc2UgMDogcmV0dXJuIG5ldyBDO1xuICAgICAgICAgICAgY2FzZSAxOiByZXR1cm4gbmV3IEMoYSk7XG4gICAgICAgICAgICBjYXNlIDI6IHJldHVybiBuZXcgQyhhLCBiKTtcbiAgICAgICAgICB9IHJldHVybiBuZXcgQyhhLCBiLCBjKTtcbiAgICAgICAgfSByZXR1cm4gQy5hcHBseSh0aGlzLCBhcmd1bWVudHMpO1xuICAgICAgfTtcbiAgICAgIEZbUFJPVE9UWVBFXSA9IENbUFJPVE9UWVBFXTtcbiAgICAgIHJldHVybiBGO1xuICAgIC8vIG1ha2Ugc3RhdGljIHZlcnNpb25zIGZvciBwcm90b3R5cGUgbWV0aG9kc1xuICAgIH0pKG91dCkgOiBJU19QUk9UTyAmJiB0eXBlb2Ygb3V0ID09ICdmdW5jdGlvbicgPyBjdHgoRnVuY3Rpb24uY2FsbCwgb3V0KSA6IG91dDtcbiAgICAvLyBleHBvcnQgcHJvdG8gbWV0aG9kcyB0byBjb3JlLiVDT05TVFJVQ1RPUiUubWV0aG9kcy4lTkFNRSVcbiAgICBpZihJU19QUk9UTyl7XG4gICAgICAoZXhwb3J0cy52aXJ0dWFsIHx8IChleHBvcnRzLnZpcnR1YWwgPSB7fSkpW2tleV0gPSBvdXQ7XG4gICAgICAvLyBleHBvcnQgcHJvdG8gbWV0aG9kcyB0byBjb3JlLiVDT05TVFJVQ1RPUiUucHJvdG90eXBlLiVOQU1FJVxuICAgICAgaWYodHlwZSAmICRleHBvcnQuUiAmJiBleHBQcm90byAmJiAhZXhwUHJvdG9ba2V5XSloaWRlKGV4cFByb3RvLCBrZXksIG91dCk7XG4gICAgfVxuICB9XG59O1xuLy8gdHlwZSBiaXRtYXBcbiRleHBvcnQuRiA9IDE7ICAgLy8gZm9yY2VkXG4kZXhwb3J0LkcgPSAyOyAgIC8vIGdsb2JhbFxuJGV4cG9ydC5TID0gNDsgICAvLyBzdGF0aWNcbiRleHBvcnQuUCA9IDg7ICAgLy8gcHJvdG9cbiRleHBvcnQuQiA9IDE2OyAgLy8gYmluZFxuJGV4cG9ydC5XID0gMzI7ICAvLyB3cmFwXG4kZXhwb3J0LlUgPSA2NDsgIC8vIHNhZmVcbiRleHBvcnQuUiA9IDEyODsgLy8gcmVhbCBwcm90byBtZXRob2QgZm9yIGBsaWJyYXJ5YCBcbm1vZHVsZS5leHBvcnRzID0gJGV4cG9ydDtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2V4cG9ydC5qc1xuLy8gbW9kdWxlIGlkID0gOFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJtb2R1bGUuZXhwb3J0cyA9IHsgXCJkZWZhdWx0XCI6IHJlcXVpcmUoXCJjb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2Fzc2lnblwiKSwgX19lc01vZHVsZTogdHJ1ZSB9O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvb2JqZWN0L2Fzc2lnbi5qc1xuLy8gbW9kdWxlIGlkID0gODJcbi8vIG1vZHVsZSBjaHVua3MgPSAzIDcgMTAgMTEgMTIgMTMgMTUgMTYgMTgiLCJyZXF1aXJlKCcuLi8uLi9tb2R1bGVzL2VzNi5vYmplY3QuYXNzaWduJyk7XG5tb2R1bGUuZXhwb3J0cyA9IHJlcXVpcmUoJy4uLy4uL21vZHVsZXMvX2NvcmUnKS5PYmplY3QuYXNzaWduO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2Fzc2lnbi5qc1xuLy8gbW9kdWxlIGlkID0gODNcbi8vIG1vZHVsZSBjaHVua3MgPSAzIDcgMTAgMTEgMTIgMTMgMTUgMTYgMTgiLCJyZXF1aXJlKCcuLi8uLi9tb2R1bGVzL2VzNi5vYmplY3Qua2V5cycpO1xubW9kdWxlLmV4cG9ydHMgPSByZXF1aXJlKCcuLi8uLi9tb2R1bGVzL19jb3JlJykuT2JqZWN0LmtleXM7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9mbi9vYmplY3Qva2V5cy5qc1xuLy8gbW9kdWxlIGlkID0gODRcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA4IDkgMTAgMTUgMTkgMjAiLCIndXNlIHN0cmljdCc7XG4vLyAxOS4xLjIuMSBPYmplY3QuYXNzaWduKHRhcmdldCwgc291cmNlLCAuLi4pXG52YXIgZ2V0S2V5cyAgPSByZXF1aXJlKCcuL19vYmplY3Qta2V5cycpXG4gICwgZ09QUyAgICAgPSByZXF1aXJlKCcuL19vYmplY3QtZ29wcycpXG4gICwgcElFICAgICAgPSByZXF1aXJlKCcuL19vYmplY3QtcGllJylcbiAgLCB0b09iamVjdCA9IHJlcXVpcmUoJy4vX3RvLW9iamVjdCcpXG4gICwgSU9iamVjdCAgPSByZXF1aXJlKCcuL19pb2JqZWN0JylcbiAgLCAkYXNzaWduICA9IE9iamVjdC5hc3NpZ247XG5cbi8vIHNob3VsZCB3b3JrIHdpdGggc3ltYm9scyBhbmQgc2hvdWxkIGhhdmUgZGV0ZXJtaW5pc3RpYyBwcm9wZXJ0eSBvcmRlciAoVjggYnVnKVxubW9kdWxlLmV4cG9ydHMgPSAhJGFzc2lnbiB8fCByZXF1aXJlKCcuL19mYWlscycpKGZ1bmN0aW9uKCl7XG4gIHZhciBBID0ge31cbiAgICAsIEIgPSB7fVxuICAgICwgUyA9IFN5bWJvbCgpXG4gICAgLCBLID0gJ2FiY2RlZmdoaWprbG1ub3BxcnN0JztcbiAgQVtTXSA9IDc7XG4gIEsuc3BsaXQoJycpLmZvckVhY2goZnVuY3Rpb24oayl7IEJba10gPSBrOyB9KTtcbiAgcmV0dXJuICRhc3NpZ24oe30sIEEpW1NdICE9IDcgfHwgT2JqZWN0LmtleXMoJGFzc2lnbih7fSwgQikpLmpvaW4oJycpICE9IEs7XG59KSA/IGZ1bmN0aW9uIGFzc2lnbih0YXJnZXQsIHNvdXJjZSl7IC8vIGVzbGludC1kaXNhYmxlLWxpbmUgbm8tdW51c2VkLXZhcnNcbiAgdmFyIFQgICAgID0gdG9PYmplY3QodGFyZ2V0KVxuICAgICwgYUxlbiAgPSBhcmd1bWVudHMubGVuZ3RoXG4gICAgLCBpbmRleCA9IDFcbiAgICAsIGdldFN5bWJvbHMgPSBnT1BTLmZcbiAgICAsIGlzRW51bSAgICAgPSBwSUUuZjtcbiAgd2hpbGUoYUxlbiA+IGluZGV4KXtcbiAgICB2YXIgUyAgICAgID0gSU9iamVjdChhcmd1bWVudHNbaW5kZXgrK10pXG4gICAgICAsIGtleXMgICA9IGdldFN5bWJvbHMgPyBnZXRLZXlzKFMpLmNvbmNhdChnZXRTeW1ib2xzKFMpKSA6IGdldEtleXMoUylcbiAgICAgICwgbGVuZ3RoID0ga2V5cy5sZW5ndGhcbiAgICAgICwgaiAgICAgID0gMFxuICAgICAgLCBrZXk7XG4gICAgd2hpbGUobGVuZ3RoID4gailpZihpc0VudW0uY2FsbChTLCBrZXkgPSBrZXlzW2orK10pKVRba2V5XSA9IFNba2V5XTtcbiAgfSByZXR1cm4gVDtcbn0gOiAkYXNzaWduO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWFzc2lnbi5qc1xuLy8gbW9kdWxlIGlkID0gODVcbi8vIG1vZHVsZSBjaHVua3MgPSAzIDcgMTAgMTEgMTIgMTMgMTUgMTYgMTgiLCIvLyAxOS4xLjMuMSBPYmplY3QuYXNzaWduKHRhcmdldCwgc291cmNlKVxudmFyICRleHBvcnQgPSByZXF1aXJlKCcuL19leHBvcnQnKTtcblxuJGV4cG9ydCgkZXhwb3J0LlMgKyAkZXhwb3J0LkYsICdPYmplY3QnLCB7YXNzaWduOiByZXF1aXJlKCcuL19vYmplY3QtYXNzaWduJyl9KTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM2Lm9iamVjdC5hc3NpZ24uanNcbi8vIG1vZHVsZSBpZCA9IDg3XG4vLyBtb2R1bGUgY2h1bmtzID0gMyA3IDEwIDExIDEyIDEzIDE1IDE2IDE4IiwiLy8gMTkuMS4yLjE0IE9iamVjdC5rZXlzKE8pXG52YXIgdG9PYmplY3QgPSByZXF1aXJlKCcuL190by1vYmplY3QnKVxuICAsICRrZXlzICAgID0gcmVxdWlyZSgnLi9fb2JqZWN0LWtleXMnKTtcblxucmVxdWlyZSgnLi9fb2JqZWN0LXNhcCcpKCdrZXlzJywgZnVuY3Rpb24oKXtcbiAgcmV0dXJuIGZ1bmN0aW9uIGtleXMoaXQpe1xuICAgIHJldHVybiAka2V5cyh0b09iamVjdChpdCkpO1xuICB9O1xufSk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5vYmplY3Qua2V5cy5qc1xuLy8gbW9kdWxlIGlkID0gODhcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA4IDkgMTAgMTUgMTkgMjAiXSwic291cmNlUm9vdCI6IiJ9