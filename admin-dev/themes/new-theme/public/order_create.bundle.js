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
/******/ 	return __webpack_require__(__webpack_require__.s = 344);
/******/ })
/************************************************************************/
/******/ ({

/***/ 264:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }(); /**
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

var _createOrderMap = __webpack_require__(67);

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

var _customerSearcherComponent = __webpack_require__(345);

var _customerSearcherComponent2 = _interopRequireDefault(_customerSearcherComponent);

var _shippingRenderer = __webpack_require__(348);

var _shippingRenderer2 = _interopRequireDefault(_shippingRenderer);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.$;

/**
 * Page Object for "Create order" page
 */

var CreateOrderPage = function () {
  function CreateOrderPage() {
    var _this = this;

    _classCallCheck(this, CreateOrderPage);

    this.data = {};
    this.$container = $(_createOrderMap2.default.orderCreationContainer);

    this.customerSearcher = new _customerSearcherComponent2.default();
    this.shippingRenderer = new _shippingRenderer2.default();

    return {
      listenForCustomerSearch: function listenForCustomerSearch() {
        return _this._handleCustomerSearch();
      },
      listenForCustomerChooseForOrderCreation: function listenForCustomerChooseForOrderCreation() {
        return _this._handleCustomerChooseForOrderCreation();
      }
    };
  }

  /**
   * Searches for customer
   *
   * @private
   */


  _createClass(CreateOrderPage, [{
    key: '_handleCustomerSearch',
    value: function _handleCustomerSearch() {
      var _this2 = this;

      this.$container.on('input', _createOrderMap2.default.customerSearchInput, function () {
        _this2.customerSearcher.onCustomerSearch();
      });
    }

    /**
     * Chooses customer for which order is being created
     *
     * @private
     */

  }, {
    key: '_handleCustomerChooseForOrderCreation',
    value: function _handleCustomerChooseForOrderCreation() {
      var _this3 = this;

      this.$container.on('click', _createOrderMap2.default.chooseCustomerBtn, function (event) {
        _this3.data.customer_id = _this3.customerSearcher.onCustomerChooseForOrderCreation(event);

        _this3._loadCartSummaryAfterChoosingCustomer();
      });

      this.$container.on('click', _createOrderMap2.default.changeCustomerBtn, function () {
        return _this3.customerSearcher.onCustomerChange();
      });
      this.$container.on('change', _createOrderMap2.default.addressSelect, function () {
        return _this3._changeCartAddresses();
      });

      this.$container.on('click', '.js-use-cart-btn', function () {
        var cartId = $(event.target).data('cart-id');

        _this3._choosePreviousCart(cartId);
      });
    }

    /**
     * Loads cart summary with customer's carts & orders history.
     *
     * @private
     */

  }, {
    key: '_loadCartSummaryAfterChoosingCustomer',
    value: function _loadCartSummaryAfterChoosingCustomer() {
      var _this4 = this;

      $.ajax(this.$container.data('last-empty-cart-url'), {
        method: 'POST',
        data: {
          id_customer: this.data.customer_id
        },
        dataType: 'json'
      }).then(function (response) {
        _this4.data.cart_id = response.cart.id_cart;

        var checkoutHistory = {
          carts: typeof response.carts !== 'undefined' ? response.carts : [],
          orders: typeof response.orders !== 'undefined' ? response.orders : []
        };

        _this4._renderCheckoutHistory(checkoutHistory);
        _this4._renderCartSummary(response);
      });
    }

    /**
     * Renders previous Carts & Orders from customer history
     *
     * @param {Object} checkoutHistory
     *
     * @private
     */

  }, {
    key: '_renderCheckoutHistory',
    value: function _renderCheckoutHistory(checkoutHistory) {
      this._renderCustomerCarts(checkoutHistory.carts);
      this._renderCustomerOrders(checkoutHistory.orders);

      $(_createOrderMap2.default.customerCheckoutHistory).removeClass('d-none');
    }

    /**
     * Renders customer carts from checkout history
     *
     * @param {Object} carts
     *
     * @private
     */

  }, {
    key: '_renderCustomerCarts',
    value: function _renderCustomerCarts(carts) {
      var $cartsTable = $(_createOrderMap2.default.customerCartsTable);
      var $cartsTableRowTemplate = $($(_createOrderMap2.default.customerCartsTableRowTemplate).html());

      $cartsTable.find('tbody').empty();

      if (!carts) {
        return;
      }

      for (var key in carts) {
        if (!carts.hasOwnProperty(key)) {
          continue;
        }

        var cart = carts[key];
        var $template = $cartsTableRowTemplate.clone();

        $template.find('.js-cart-id').text(cart.id_cart);
        $template.find('.js-cart-date').text(cart.date_add);
        $template.find('.js-cart-total').text(cart.total_price);

        $template.find('.js-use-cart-btn').data('cart-id', cart.id_cart);

        $cartsTable.find('tbody').append($template);
      }

      $(_createOrderMap2.default.customerCheckoutHistory).removeClass('d-none');
    }

    /**
     * Renders cart summary on the page
     *
     * @param {Object} cartSummary
     *
     * @private
     */

  }, {
    key: '_renderCartSummary',
    value: function _renderCartSummary(cartSummary) {
      this._renderAddressesSelect(cartSummary);

      // render Summary block when at least 1 product is in cart
      // and delivery options are available

      this._showCartSummary();
    }

    /**
     * Renders customer orders
     *
     * @param {Object} orders
     *
     * @private
     */

  }, {
    key: '_renderCustomerOrders',
    value: function _renderCustomerOrders(orders) {
      var $ordersTable = $(_createOrderMap2.default.customerOrdersTable);
      var $rowTemplate = $($(_createOrderMap2.default.customerOrdersTableRowTemplate).html());

      $ordersTable.find('tbody').empty();

      if (!orders) {
        return;
      }

      for (var key in Object.keys(orders)) {
        if (!orders.hasOwnProperty(key)) {
          continue;
        }

        var order = orders[key];
        var $template = $rowTemplate.clone();

        $template.find('.js-order-id').text(order.id_order);
        $template.find('.js-order-date').text(order.date_add);
        $template.find('.js-order-products').text(order.nb_products);
        $template.find('.js-order-total-paid').text(order.total_paid_real);
        $template.find('.js-order-status').text(order.order_state);

        $ordersTable.find('tbody').append($template);
      }
    }

    /**
     * Shows Cart, Vouchers, Addresses blocks
     *
     * @private
     */

  }, {
    key: '_showCartSummary',
    value: function _showCartSummary() {
      $(_createOrderMap2.default.cartBlock).removeClass('d-none');
      $(_createOrderMap2.default.vouchersBlock).removeClass('d-none');
      $(_createOrderMap2.default.addressesBlock).removeClass('d-none');
    }

    /**
     * Renders Delivery & Invoice addresses select
     *
     * @param {Object} cartSummary
     *
     * @private
     */

  }, {
    key: '_renderAddressesSelect',
    value: function _renderAddressesSelect(cartSummary) {
      var deliveryAddressDetailsContent = '';
      var invoiceAddressDetailsContent = '';

      var $deliveryAddressDetails = $(_createOrderMap2.default.deliveryAddressDetails);
      var $invoiceAddressDetails = $(_createOrderMap2.default.invoiceAddressDetails);
      var $deliveryAddressSelect = $(_createOrderMap2.default.deliveryAddressSelect);
      var $invoiceAddressSelect = $(_createOrderMap2.default.invoiceAddressSelect);

      var $addressesContent = $(_createOrderMap2.default.addressesContent);
      var $addressesWarningContent = $(_createOrderMap2.default.addressesWarning);

      $deliveryAddressDetails.empty();
      $invoiceAddressDetails.empty();
      $deliveryAddressSelect.empty();
      $invoiceAddressSelect.empty();

      if (cartSummary.addresses.length === 0) {
        $addressesWarningContent.removeClass('d-none');
        $addressesContent.addClass('d-none');

        return;
      }

      $addressesContent.removeClass('d-none');
      $addressesWarningContent.addClass('d-none');

      for (var key in Object.keys(cartSummary.addresses)) {
        if (!cartSummary.addresses.hasOwnProperty(key)) {
          continue;
        }

        var address = cartSummary.addresses[key];

        var deliveryAddressOption = {
          value: address.id_address,
          text: address.alias
        };

        var invoiceAddressOption = {
          value: address.id_address,
          text: address.alias
        };

        if (parseInt(cartSummary.cart.id_address_delivery) === parseInt(address.id_address)) {
          deliveryAddressDetailsContent = address.formated_address;
          deliveryAddressOption.selected = 'selected';
        }

        if (parseInt(cartSummary.cart.id_address_invoice) === parseInt(address.id_address)) {
          invoiceAddressDetailsContent = address.formated_address;
          invoiceAddressOption.selected = 'selected';
        }

        $deliveryAddressSelect.append($('<option>', deliveryAddressOption));
        $invoiceAddressSelect.append($('<option>', invoiceAddressOption));
      }

      if (deliveryAddressDetailsContent) {
        $(_createOrderMap2.default.deliveryAddressDetails).html(deliveryAddressDetailsContent);
      }

      if (invoiceAddressDetailsContent) {
        $(_createOrderMap2.default.invoiceAddressDetails).html(invoiceAddressDetailsContent);
      }
    }

    /**
     * Changes cart addresses
     *
     * @private
     */

  }, {
    key: '_changeCartAddresses',
    value: function _changeCartAddresses() {
      var _this5 = this;

      $.ajax(this.$container.data('cart-addresses-url'), {
        data: {
          id_customer: this.data.customer_id,
          id_cart: this.data.cart_id,
          id_address_delivery: $(_createOrderMap2.default.deliveryAddressSelect).val(),
          id_address_invoice: $(_createOrderMap2.default.invoiceAddressSelect).val()
        },
        dataType: 'json'
      }).then(function (response) {
        _this5._persistCartSummaryData(response);

        _this5._renderAddressesSelect(response);
      });
    }

    /**
     * Stores cart summary into "session" like variable
     *
     * @param {Object} cartSummary
     *
     * @private
     */

  }, {
    key: '_persistCartSummaryData',
    value: function _persistCartSummaryData(cartSummary) {
      this.data.cart_id = cartSummary.cart.id;
      this.data.delivery_address_id = cartSummary.cart.id_address_delivery;
      this.data.invoice_address_id = cartSummary.cart.id_address_invoice;
    }

    /**
     * Choses previous cart from which order will be created
     *
     * @param {Number} cartId
     *
     * @private
     */

  }, {
    key: '_choosePreviousCart',
    value: function _choosePreviousCart(cartId) {
      var _this6 = this;

      $.ajax(this.$container.data('cart-summary-url'), {
        method: 'POST',
        data: {
          id_cart: cartId,
          id_customer: this.data.customer_id
        },
        dataType: 'json'
      }).then(function (response) {
        _this6._persistCartSummaryData(response);

        _this6._renderCartSummary(response);
      });
    }
  }]);

  return CreateOrderPage;
}();

exports.default = CreateOrderPage;

/***/ }),

/***/ 344:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _createOrderPage = __webpack_require__(264);

var _createOrderPage2 = _interopRequireDefault(_createOrderPage);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$; /**
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


$(document).ready(function () {
  var createOrderPage = new _createOrderPage2.default();

  createOrderPage.listenForCustomerSearch();
  createOrderPage.listenForCustomerChooseForOrderCreation();
});

/***/ }),

/***/ 345:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }(); /**
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

var _createOrderMap = __webpack_require__(67);

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.$;

/**
 * Searches customers for which order is being created
 */

var CustomerSearcherComponent = function () {
  function CustomerSearcherComponent() {
    var _this = this;

    _classCallCheck(this, CustomerSearcherComponent);

    this.$container = $(_createOrderMap2.default.customerSearchBlock);
    this.$searchInput = $(_createOrderMap2.default.customerSearchInput);
    this.$customerSearchResultBlock = $(_createOrderMap2.default.customerSearchResultsBlock);

    return {
      onCustomerSearch: function onCustomerSearch() {
        _this._doSearch();
      },
      onCustomerChooseForOrderCreation: function onCustomerChooseForOrderCreation(event) {
        return _this._chooseCustomerForOrderCreation(event);
      },
      onCustomerChange: function onCustomerChange() {
        _this._showCustomerSearch();
      }
    };
  }

  /**
   *
   * @param {Event} chooseCustomerEvent
   *
   * @return {Number}
   */


  _createClass(CustomerSearcherComponent, [{
    key: '_chooseCustomerForOrderCreation',
    value: function _chooseCustomerForOrderCreation(chooseCustomerEvent) {
      var $chooseBtn = $(chooseCustomerEvent.currentTarget);
      var $customerCard = $chooseBtn.closest('.card');

      $chooseBtn.addClass('d-none');

      $customerCard.addClass('border-success');
      $customerCard.find(_createOrderMap2.default.changeCustomerBtn).removeClass('d-none');

      this.$container.find(_createOrderMap2.default.customerSearchRow).addClass('d-none');
      this.$container.find(_createOrderMap2.default.notSelectedCustomerSearchResults).closest(_createOrderMap2.default.customerSearchResultColumn).remove();

      return $chooseBtn.data('customer-id');
    }

    /**
     * Searches for customers
     *
     * @private
     */

  }, {
    key: '_doSearch',
    value: function _doSearch() {
      var _this2 = this;

      var name = this.$searchInput.val();

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
      }).then(function (response) {
        var result = JSON.parse(response);

        _this2._clearShownCustomers();

        if (!result.hasOwnProperty('customers')) {
          _this2._showNotFoundCustomers();

          return;
        }

        for (var customerId in result.customers) {
          var customerResult = result.customers[customerId];
          var customer = {
            id: customerId,
            first_name: customerResult.firstname,
            last_name: customerResult.lastname,
            email: customerResult.email,
            birthday: customerResult.birthday !== '0000-00-00' ? customerResult.birthday : ' '
          };

          _this2._showCustomer(customer);
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

  }, {
    key: '_showCustomer',
    value: function _showCustomer(customer) {
      var $customerSearchResultTemplate = $($(_createOrderMap2.default.customerSearchResultTemplate).html());
      var $template = $customerSearchResultTemplate.clone();

      $template.find(_createOrderMap2.default.customerSearchResultName).text(customer.first_name + ' ' + customer.last_name);
      $template.find(_createOrderMap2.default.customerSearchResultEmail).text(customer.email);
      $template.find(_createOrderMap2.default.customerSearchResultId).text(customer.id);
      $template.find(_createOrderMap2.default.customerSearchResultBirthday).text(customer.birthday);

      $template.find(_createOrderMap2.default.customerDetailsBtn).data('customer-id', customer.id);
      $template.find(_createOrderMap2.default.chooseCustomerBtn).data('customer-id', customer.id);

      return this.$customerSearchResultBlock.append($template);
    }

    /**
     * Shows empty result when customer is not found
     *
     * @private
     */

  }, {
    key: '_showNotFoundCustomers',
    value: function _showNotFoundCustomers() {
      var $emptyResultTemplate = $($('#customerSearchEmptyResultTemplate').html());

      this.$customerSearchResultBlock.append($emptyResultTemplate);
    }

    /**
     * Clears shown customers
     *
     * @private
     */

  }, {
    key: '_clearShownCustomers',
    value: function _clearShownCustomers() {
      this.$customerSearchResultBlock.empty();
    }

    /**
     * Shows customer search block
     *
     * @private
     */

  }, {
    key: '_showCustomerSearch',
    value: function _showCustomerSearch() {
      this.$container.find(_createOrderMap2.default.customerSearchRow).removeClass('d-none');
    }
  }]);

  return CustomerSearcherComponent;
}();

exports.default = CustomerSearcherComponent;

/***/ }),

/***/ 348:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }(); /**
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

var _createOrderMap = __webpack_require__(67);

var _createOrderMap2 = _interopRequireDefault(_createOrderMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.$;

/**
 * Manupulates UI of Shipping block in Order creation page
 */

var ShippingRenderer = function () {
  function ShippingRenderer() {
    _classCallCheck(this, ShippingRenderer);

    this.$container = $(_createOrderMap2.default.shippingBlock);
  }

  _createClass(ShippingRenderer, [{
    key: 'show',
    value: function show() {
      this.$container.removeClass('d-none');
    }
  }, {
    key: 'hide',
    value: function hide() {
      this.$container.addClass('d-none');
    }
  }]);

  return ShippingRenderer;
}();

exports.default = ShippingRenderer;

/***/ }),

/***/ 67:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
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

/**
 * Encapsulates selectors for "Create order" page
 */
exports.default = {
  orderCreationContainer: '#orderCreationContainer',

  // selectors related to customer block
  customerSearchInput: '#customerSearchInput',
  customerSearchResultsBlock: '.js-customer-search-results',
  customerSearchResultTemplate: '#customerSearchResultTemplate',
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
  customerSearchBlock: '#customerSearchBlock',
  customerCartsTable: '#customerCartsTable',
  customerCartsTableRowTemplate: '#customerCartsTableRowTemplate',
  customerCheckoutHistory: '#customerCheckoutHistory',
  customerOrdersTable: '#customerOrdersTable',
  customerOrdersTableRowTemplate: '#customerOrdersTableRowTemplate',

  // selectors related to cart block
  cartBlock: '#cartBlock',

  // selectors related to vouchers block
  vouchersBlock: '#vouchersBlock',

  // selectors related to addresses block
  addressesBlock: '#addressesBlock',
  deliveryAddressDetails: '#deliveryAddressDetails',
  invoiceAddressDetails: '#invoiceAddressDetails',
  deliveryAddressSelect: '#deliveryAddressSelect',
  invoiceAddressSelect: '#invoiceAddressSelect',
  addressSelect: '.js-address-select',
  addressesContent: '#addressesContent',
  addressesWarning: '#addressesWarning',

  // selectors related to summary block
  summaryBlock: '#summaryBlock',

  // selectors related to shipping block
  shippingBlock: '#shippingBlock'
};

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgMGI2NjNhYTgyMWE5NzdlMTRmN2YiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvb3JkZXIvY3JlYXRlLW9yZGVyLXBhZ2UuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvb3JkZXIvY3JlYXRlLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL2N1c3RvbWVyLXNlYXJjaGVyLWNvbXBvbmVudC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci9zaGlwcGluZy1yZW5kZXJlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUtb3JkZXItbWFwLmpzIl0sIm5hbWVzIjpbIiQiLCJ3aW5kb3ciLCJDcmVhdGVPcmRlclBhZ2UiLCJkYXRhIiwiJGNvbnRhaW5lciIsImNyZWF0ZU9yZGVyUGFnZU1hcCIsIm9yZGVyQ3JlYXRpb25Db250YWluZXIiLCJjdXN0b21lclNlYXJjaGVyIiwiQ3VzdG9tZXJTZWFyY2hlckNvbXBvbmVudCIsInNoaXBwaW5nUmVuZGVyZXIiLCJTaGlwcGluZ1JlbmRlcmVyIiwibGlzdGVuRm9yQ3VzdG9tZXJTZWFyY2giLCJfaGFuZGxlQ3VzdG9tZXJTZWFyY2giLCJsaXN0ZW5Gb3JDdXN0b21lckNob29zZUZvck9yZGVyQ3JlYXRpb24iLCJfaGFuZGxlQ3VzdG9tZXJDaG9vc2VGb3JPcmRlckNyZWF0aW9uIiwib24iLCJjdXN0b21lclNlYXJjaElucHV0Iiwib25DdXN0b21lclNlYXJjaCIsImNob29zZUN1c3RvbWVyQnRuIiwiZXZlbnQiLCJjdXN0b21lcl9pZCIsIm9uQ3VzdG9tZXJDaG9vc2VGb3JPcmRlckNyZWF0aW9uIiwiX2xvYWRDYXJ0U3VtbWFyeUFmdGVyQ2hvb3NpbmdDdXN0b21lciIsImNoYW5nZUN1c3RvbWVyQnRuIiwib25DdXN0b21lckNoYW5nZSIsImFkZHJlc3NTZWxlY3QiLCJfY2hhbmdlQ2FydEFkZHJlc3NlcyIsImNhcnRJZCIsInRhcmdldCIsIl9jaG9vc2VQcmV2aW91c0NhcnQiLCJhamF4IiwibWV0aG9kIiwiaWRfY3VzdG9tZXIiLCJkYXRhVHlwZSIsInRoZW4iLCJyZXNwb25zZSIsImNhcnRfaWQiLCJjYXJ0IiwiaWRfY2FydCIsImNoZWNrb3V0SGlzdG9yeSIsImNhcnRzIiwib3JkZXJzIiwiX3JlbmRlckNoZWNrb3V0SGlzdG9yeSIsIl9yZW5kZXJDYXJ0U3VtbWFyeSIsIl9yZW5kZXJDdXN0b21lckNhcnRzIiwiX3JlbmRlckN1c3RvbWVyT3JkZXJzIiwiY3VzdG9tZXJDaGVja291dEhpc3RvcnkiLCJyZW1vdmVDbGFzcyIsIiRjYXJ0c1RhYmxlIiwiY3VzdG9tZXJDYXJ0c1RhYmxlIiwiJGNhcnRzVGFibGVSb3dUZW1wbGF0ZSIsImN1c3RvbWVyQ2FydHNUYWJsZVJvd1RlbXBsYXRlIiwiaHRtbCIsImZpbmQiLCJlbXB0eSIsImtleSIsImhhc093blByb3BlcnR5IiwiJHRlbXBsYXRlIiwiY2xvbmUiLCJ0ZXh0IiwiZGF0ZV9hZGQiLCJ0b3RhbF9wcmljZSIsImFwcGVuZCIsImNhcnRTdW1tYXJ5IiwiX3JlbmRlckFkZHJlc3Nlc1NlbGVjdCIsIl9zaG93Q2FydFN1bW1hcnkiLCIkb3JkZXJzVGFibGUiLCJjdXN0b21lck9yZGVyc1RhYmxlIiwiJHJvd1RlbXBsYXRlIiwiY3VzdG9tZXJPcmRlcnNUYWJsZVJvd1RlbXBsYXRlIiwiT2JqZWN0Iiwia2V5cyIsIm9yZGVyIiwiaWRfb3JkZXIiLCJuYl9wcm9kdWN0cyIsInRvdGFsX3BhaWRfcmVhbCIsIm9yZGVyX3N0YXRlIiwiY2FydEJsb2NrIiwidm91Y2hlcnNCbG9jayIsImFkZHJlc3Nlc0Jsb2NrIiwiZGVsaXZlcnlBZGRyZXNzRGV0YWlsc0NvbnRlbnQiLCJpbnZvaWNlQWRkcmVzc0RldGFpbHNDb250ZW50IiwiJGRlbGl2ZXJ5QWRkcmVzc0RldGFpbHMiLCJkZWxpdmVyeUFkZHJlc3NEZXRhaWxzIiwiJGludm9pY2VBZGRyZXNzRGV0YWlscyIsImludm9pY2VBZGRyZXNzRGV0YWlscyIsIiRkZWxpdmVyeUFkZHJlc3NTZWxlY3QiLCJkZWxpdmVyeUFkZHJlc3NTZWxlY3QiLCIkaW52b2ljZUFkZHJlc3NTZWxlY3QiLCJpbnZvaWNlQWRkcmVzc1NlbGVjdCIsIiRhZGRyZXNzZXNDb250ZW50IiwiYWRkcmVzc2VzQ29udGVudCIsIiRhZGRyZXNzZXNXYXJuaW5nQ29udGVudCIsImFkZHJlc3Nlc1dhcm5pbmciLCJhZGRyZXNzZXMiLCJsZW5ndGgiLCJhZGRDbGFzcyIsImFkZHJlc3MiLCJkZWxpdmVyeUFkZHJlc3NPcHRpb24iLCJ2YWx1ZSIsImlkX2FkZHJlc3MiLCJhbGlhcyIsImludm9pY2VBZGRyZXNzT3B0aW9uIiwicGFyc2VJbnQiLCJpZF9hZGRyZXNzX2RlbGl2ZXJ5IiwiZm9ybWF0ZWRfYWRkcmVzcyIsInNlbGVjdGVkIiwiaWRfYWRkcmVzc19pbnZvaWNlIiwidmFsIiwiX3BlcnNpc3RDYXJ0U3VtbWFyeURhdGEiLCJpZCIsImRlbGl2ZXJ5X2FkZHJlc3NfaWQiLCJpbnZvaWNlX2FkZHJlc3NfaWQiLCJkb2N1bWVudCIsInJlYWR5IiwiY3JlYXRlT3JkZXJQYWdlIiwiY3VzdG9tZXJTZWFyY2hCbG9jayIsIiRzZWFyY2hJbnB1dCIsIiRjdXN0b21lclNlYXJjaFJlc3VsdEJsb2NrIiwiY3VzdG9tZXJTZWFyY2hSZXN1bHRzQmxvY2siLCJfZG9TZWFyY2giLCJfY2hvb3NlQ3VzdG9tZXJGb3JPcmRlckNyZWF0aW9uIiwiX3Nob3dDdXN0b21lclNlYXJjaCIsImNob29zZUN1c3RvbWVyRXZlbnQiLCIkY2hvb3NlQnRuIiwiY3VycmVudFRhcmdldCIsIiRjdXN0b21lckNhcmQiLCJjbG9zZXN0IiwiY3VzdG9tZXJTZWFyY2hSb3ciLCJub3RTZWxlY3RlZEN1c3RvbWVyU2VhcmNoUmVzdWx0cyIsImN1c3RvbWVyU2VhcmNoUmVzdWx0Q29sdW1uIiwicmVtb3ZlIiwibmFtZSIsInJlc3VsdCIsIkpTT04iLCJwYXJzZSIsIl9jbGVhclNob3duQ3VzdG9tZXJzIiwiX3Nob3dOb3RGb3VuZEN1c3RvbWVycyIsImN1c3RvbWVySWQiLCJjdXN0b21lcnMiLCJjdXN0b21lclJlc3VsdCIsImN1c3RvbWVyIiwiZmlyc3RfbmFtZSIsImZpcnN0bmFtZSIsImxhc3RfbmFtZSIsImxhc3RuYW1lIiwiZW1haWwiLCJiaXJ0aGRheSIsIl9zaG93Q3VzdG9tZXIiLCIkY3VzdG9tZXJTZWFyY2hSZXN1bHRUZW1wbGF0ZSIsImN1c3RvbWVyU2VhcmNoUmVzdWx0VGVtcGxhdGUiLCJjdXN0b21lclNlYXJjaFJlc3VsdE5hbWUiLCJjdXN0b21lclNlYXJjaFJlc3VsdEVtYWlsIiwiY3VzdG9tZXJTZWFyY2hSZXN1bHRJZCIsImN1c3RvbWVyU2VhcmNoUmVzdWx0QmlydGhkYXkiLCJjdXN0b21lckRldGFpbHNCdG4iLCIkZW1wdHlSZXN1bHRUZW1wbGF0ZSIsInNoaXBwaW5nQmxvY2siLCJzdW1tYXJ5QmxvY2siXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7OztBQUdBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBLG1EQUEyQyxjQUFjOztBQUV6RDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGFBQUs7QUFDTDtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLG1DQUEyQiwwQkFBMEIsRUFBRTtBQUN2RCx5Q0FBaUMsZUFBZTtBQUNoRDtBQUNBO0FBQ0E7O0FBRUE7QUFDQSw4REFBc0QsK0RBQStEOztBQUVySDtBQUNBOztBQUVBO0FBQ0E7Ozs7Ozs7Ozs7Ozs7OztxakJDaEVBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBeUJBOzs7O0FBQ0E7Ozs7QUFDQTs7Ozs7Ozs7QUFFQSxJQUFNQSxJQUFJQyxPQUFPRCxDQUFqQjs7QUFFQTs7OztJQUdxQkUsZTtBQUNuQiw2QkFBYztB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bUJBWGI7QUFZYkMsNkJBQTJCLG9CQVpkO0FBYWJDLDBCQUF3QixpQkFiWDtBQWNiQyxnQ0FBOEIsdUJBZGpCO0FBZWJDLHNCQUFvQiwwQkFmUDtBQWdCYnpCLDhCQUE0QixnQ0FoQmY7QUFpQmJkLHVCQUFxQixzQkFqQlI7QUFrQmJuRSxzQkFBb0IscUJBbEJQO0FBbUJiRSxpQ0FBK0IsZ0NBbkJsQjtBQW9CYkwsMkJBQXlCLDBCQXBCWjtBQXFCYnFCLHVCQUFxQixzQkFyQlI7QUFzQmJFLGtDQUFnQyxpQ0F0Qm5COztBQXdCYjtBQUNBUSxhQUFXLFlBekJFOztBQTJCYjtBQUNBQyxpQkFBZSxnQkE1QkY7O0FBOEJiO0FBQ0FDLGtCQUFnQixpQkEvQkg7QUFnQ2JJLDBCQUF3Qix5QkFoQ1g7QUFpQ2JFLHlCQUF1Qix3QkFqQ1Y7QUFrQ2JFLHlCQUF1Qix3QkFsQ1Y7QUFtQ2JFLHdCQUFzQix1QkFuQ1Q7QUFvQ2JoRSxpQkFBZSxvQkFwQ0Y7QUFxQ2JrRSxvQkFBa0IsbUJBckNMO0FBc0NiRSxvQkFBa0IsbUJBdENMOztBQXdDYjtBQUNBaUUsZ0JBQWMsZUF6Q0Q7O0FBMkNiO0FBQ0FELGlCQUFlO0FBNUNGLEMiLCJmaWxlIjoib3JkZXJfY3JlYXRlLmJ1bmRsZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGlkZW50aXR5IGZ1bmN0aW9uIGZvciBjYWxsaW5nIGhhcm1vbnkgaW1wb3J0cyB3aXRoIHRoZSBjb3JyZWN0IGNvbnRleHRcbiBcdF9fd2VicGFja19yZXF1aXJlX18uaSA9IGZ1bmN0aW9uKHZhbHVlKSB7IHJldHVybiB2YWx1ZTsgfTtcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7XG4gXHRcdFx0XHRjb25maWd1cmFibGU6IGZhbHNlLFxuIFx0XHRcdFx0ZW51bWVyYWJsZTogdHJ1ZSxcbiBcdFx0XHRcdGdldDogZ2V0dGVyXG4gXHRcdFx0fSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gMzQ0KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyB3ZWJwYWNrL2Jvb3RzdHJhcCAwYjY2M2FhODIxYTk3N2UxNGY3ZiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBjcmVhdGVPcmRlclBhZ2VNYXAgZnJvbSAnLi9jcmVhdGUtb3JkZXItbWFwJztcbmltcG9ydCBDdXN0b21lclNlYXJjaGVyQ29tcG9uZW50IGZyb20gJy4vY3VzdG9tZXItc2VhcmNoZXItY29tcG9uZW50JztcbmltcG9ydCBTaGlwcGluZ1JlbmRlcmVyIGZyb20gJy4vc2hpcHBpbmctcmVuZGVyZXInO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogUGFnZSBPYmplY3QgZm9yIFwiQ3JlYXRlIG9yZGVyXCIgcGFnZVxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBDcmVhdGVPcmRlclBhZ2Uge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLmRhdGEgPSB7fTtcbiAgICB0aGlzLiRjb250YWluZXIgPSAkKGNyZWF0ZU9yZGVyUGFnZU1hcC5vcmRlckNyZWF0aW9uQ29udGFpbmVyKTtcblxuICAgIHRoaXMuY3VzdG9tZXJTZWFyY2hlciA9IG5ldyBDdXN0b21lclNlYXJjaGVyQ29tcG9uZW50KCk7XG4gICAgdGhpcy5zaGlwcGluZ1JlbmRlcmVyID0gbmV3IFNoaXBwaW5nUmVuZGVyZXIoKTtcblxuICAgIHJldHVybiB7XG4gICAgICBsaXN0ZW5Gb3JDdXN0b21lclNlYXJjaDogKCkgPT4gdGhpcy5faGFuZGxlQ3VzdG9tZXJTZWFyY2goKSxcbiAgICAgIGxpc3RlbkZvckN1c3RvbWVyQ2hvb3NlRm9yT3JkZXJDcmVhdGlvbjogKCkgPT4gdGhpcy5faGFuZGxlQ3VzdG9tZXJDaG9vc2VGb3JPcmRlckNyZWF0aW9uKCksXG4gICAgfTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTZWFyY2hlcyBmb3IgY3VzdG9tZXJcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oYW5kbGVDdXN0b21lclNlYXJjaCgpIHtcbiAgICB0aGlzLiRjb250YWluZXIub24oJ2lucHV0JywgY3JlYXRlT3JkZXJQYWdlTWFwLmN1c3RvbWVyU2VhcmNoSW5wdXQsICgpID0+IHtcbiAgICAgIHRoaXMuY3VzdG9tZXJTZWFyY2hlci5vbkN1c3RvbWVyU2VhcmNoKCk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQ2hvb3NlcyBjdXN0b21lciBmb3Igd2hpY2ggb3JkZXIgaXMgYmVpbmcgY3JlYXRlZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hhbmRsZUN1c3RvbWVyQ2hvb3NlRm9yT3JkZXJDcmVhdGlvbigpIHtcbiAgICB0aGlzLiRjb250YWluZXIub24oJ2NsaWNrJywgY3JlYXRlT3JkZXJQYWdlTWFwLmNob29zZUN1c3RvbWVyQnRuLCAoZXZlbnQpID0+IHtcbiAgICAgIHRoaXMuZGF0YS5jdXN0b21lcl9pZCA9IHRoaXMuY3VzdG9tZXJTZWFyY2hlci5vbkN1c3RvbWVyQ2hvb3NlRm9yT3JkZXJDcmVhdGlvbihldmVudCk7XG5cbiAgICAgIHRoaXMuX2xvYWRDYXJ0U3VtbWFyeUFmdGVyQ2hvb3NpbmdDdXN0b21lcigpO1xuICAgIH0pO1xuXG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjbGljaycsIGNyZWF0ZU9yZGVyUGFnZU1hcC5jaGFuZ2VDdXN0b21lckJ0biwgKCkgPT4gdGhpcy5jdXN0b21lclNlYXJjaGVyLm9uQ3VzdG9tZXJDaGFuZ2UoKSk7XG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjaGFuZ2UnLCBjcmVhdGVPcmRlclBhZ2VNYXAuYWRkcmVzc1NlbGVjdCwgKCkgPT4gdGhpcy5fY2hhbmdlQ2FydEFkZHJlc3NlcygpKTtcblxuICAgIHRoaXMuJGNvbnRhaW5lci5vbignY2xpY2snLCAnLmpzLXVzZS1jYXJ0LWJ0bicsICgpID0+IHtcbiAgICAgIGNvbnN0IGNhcnRJZCA9ICQoZXZlbnQudGFyZ2V0KS5kYXRhKCdjYXJ0LWlkJyk7XG5cbiAgICAgIHRoaXMuX2Nob29zZVByZXZpb3VzQ2FydChjYXJ0SWQpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIExvYWRzIGNhcnQgc3VtbWFyeSB3aXRoIGN1c3RvbWVyJ3MgY2FydHMgJiBvcmRlcnMgaGlzdG9yeS5cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9sb2FkQ2FydFN1bW1hcnlBZnRlckNob29zaW5nQ3VzdG9tZXIoKSB7XG4gICAgJC5hamF4KHRoaXMuJGNvbnRhaW5lci5kYXRhKCdsYXN0LWVtcHR5LWNhcnQtdXJsJyksIHtcbiAgICAgIG1ldGhvZDogJ1BPU1QnLFxuICAgICAgZGF0YToge1xuICAgICAgICBpZF9jdXN0b21lcjogdGhpcy5kYXRhLmN1c3RvbWVyX2lkLFxuICAgICAgfSxcbiAgICAgIGRhdGFUeXBlOiAnanNvbicsXG4gICAgfSkudGhlbigocmVzcG9uc2UpID0+IHtcbiAgICAgIHRoaXMuZGF0YS5jYXJ0X2lkID0gcmVzcG9uc2UuY2FydC5pZF9jYXJ0O1xuXG4gICAgICBjb25zdCBjaGVja291dEhpc3RvcnkgPSB7XG4gICAgICAgIGNhcnRzOiB0eXBlb2YgcmVzcG9uc2UuY2FydHMgIT09ICd1bmRlZmluZWQnID8gcmVzcG9uc2UuY2FydHMgOiBbXSxcbiAgICAgICAgb3JkZXJzOiB0eXBlb2YgcmVzcG9uc2Uub3JkZXJzICE9PSAndW5kZWZpbmVkJyA/IHJlc3BvbnNlLm9yZGVycyA6IFtdLFxuICAgICAgfTtcblxuICAgICAgdGhpcy5fcmVuZGVyQ2hlY2tvdXRIaXN0b3J5KGNoZWNrb3V0SGlzdG9yeSk7XG4gICAgICB0aGlzLl9yZW5kZXJDYXJ0U3VtbWFyeShyZXNwb25zZSk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogUmVuZGVycyBwcmV2aW91cyBDYXJ0cyAmIE9yZGVycyBmcm9tIGN1c3RvbWVyIGhpc3RvcnlcbiAgICpcbiAgICogQHBhcmFtIHtPYmplY3R9IGNoZWNrb3V0SGlzdG9yeVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3JlbmRlckNoZWNrb3V0SGlzdG9yeShjaGVja291dEhpc3RvcnkpIHtcbiAgICB0aGlzLl9yZW5kZXJDdXN0b21lckNhcnRzKGNoZWNrb3V0SGlzdG9yeS5jYXJ0cyk7XG4gICAgdGhpcy5fcmVuZGVyQ3VzdG9tZXJPcmRlcnMoY2hlY2tvdXRIaXN0b3J5Lm9yZGVycyk7XG5cbiAgICAkKGNyZWF0ZU9yZGVyUGFnZU1hcC5jdXN0b21lckNoZWNrb3V0SGlzdG9yeSkucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICB9XG5cbiAgLyoqXG4gICAqIFJlbmRlcnMgY3VzdG9tZXIgY2FydHMgZnJvbSBjaGVja291dCBoaXN0b3J5XG4gICAqXG4gICAqIEBwYXJhbSB7T2JqZWN0fSBjYXJ0c1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3JlbmRlckN1c3RvbWVyQ2FydHMoY2FydHMpIHtcbiAgICBjb25zdCAkY2FydHNUYWJsZSA9ICQoY3JlYXRlT3JkZXJQYWdlTWFwLmN1c3RvbWVyQ2FydHNUYWJsZSk7XG4gICAgY29uc3QgJGNhcnRzVGFibGVSb3dUZW1wbGF0ZSA9ICQoJChjcmVhdGVPcmRlclBhZ2VNYXAuY3VzdG9tZXJDYXJ0c1RhYmxlUm93VGVtcGxhdGUpLmh0bWwoKSk7XG5cbiAgICAkY2FydHNUYWJsZS5maW5kKCd0Ym9keScpLmVtcHR5KCk7XG5cbiAgICBpZiAoIWNhcnRzKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgZm9yIChjb25zdCBrZXkgaW4gY2FydHMpIHtcbiAgICAgIGlmICghY2FydHMuaGFzT3duUHJvcGVydHkoa2V5KSkge1xuICAgICAgICBjb250aW51ZTtcbiAgICAgIH1cblxuICAgICAgY29uc3QgY2FydCA9IGNhcnRzW2tleV07XG4gICAgICBjb25zdCAkdGVtcGxhdGUgPSAkY2FydHNUYWJsZVJvd1RlbXBsYXRlLmNsb25lKCk7XG5cbiAgICAgICR0ZW1wbGF0ZS5maW5kKCcuanMtY2FydC1pZCcpLnRleHQoY2FydC5pZF9jYXJ0KTtcbiAgICAgICR0ZW1wbGF0ZS5maW5kKCcuanMtY2FydC1kYXRlJykudGV4dChjYXJ0LmRhdGVfYWRkKTtcbiAgICAgICR0ZW1wbGF0ZS5maW5kKCcuanMtY2FydC10b3RhbCcpLnRleHQoY2FydC50b3RhbF9wcmljZSk7XG5cbiAgICAgICR0ZW1wbGF0ZS5maW5kKCcuanMtdXNlLWNhcnQtYnRuJykuZGF0YSgnY2FydC1pZCcsIGNhcnQuaWRfY2FydCk7XG5cbiAgICAgICRjYXJ0c1RhYmxlLmZpbmQoJ3Rib2R5JykuYXBwZW5kKCR0ZW1wbGF0ZSk7XG4gICAgfVxuXG4gICAgJChjcmVhdGVPcmRlclBhZ2VNYXAuY3VzdG9tZXJDaGVja291dEhpc3RvcnkpLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZW5kZXJzIGNhcnQgc3VtbWFyeSBvbiB0aGUgcGFnZVxuICAgKlxuICAgKiBAcGFyYW0ge09iamVjdH0gY2FydFN1bW1hcnlcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9yZW5kZXJDYXJ0U3VtbWFyeShjYXJ0U3VtbWFyeSkge1xuICAgIHRoaXMuX3JlbmRlckFkZHJlc3Nlc1NlbGVjdChjYXJ0U3VtbWFyeSk7XG5cbiAgICAvLyByZW5kZXIgU3VtbWFyeSBibG9jayB3aGVuIGF0IGxlYXN0IDEgcHJvZHVjdCBpcyBpbiBjYXJ0XG4gICAgLy8gYW5kIGRlbGl2ZXJ5IG9wdGlvbnMgYXJlIGF2YWlsYWJsZVxuXG4gICAgdGhpcy5fc2hvd0NhcnRTdW1tYXJ5KCk7XG4gIH1cblxuICAvKipcbiAgICogUmVuZGVycyBjdXN0b21lciBvcmRlcnNcbiAgICpcbiAgICogQHBhcmFtIHtPYmplY3R9IG9yZGVyc1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3JlbmRlckN1c3RvbWVyT3JkZXJzKG9yZGVycykge1xuICAgIGNvbnN0ICRvcmRlcnNUYWJsZSA9ICQoY3JlYXRlT3JkZXJQYWdlTWFwLmN1c3RvbWVyT3JkZXJzVGFibGUpO1xuICAgIGNvbnN0ICRyb3dUZW1wbGF0ZSA9ICQoJChjcmVhdGVPcmRlclBhZ2VNYXAuY3VzdG9tZXJPcmRlcnNUYWJsZVJvd1RlbXBsYXRlKS5odG1sKCkpO1xuXG4gICAgJG9yZGVyc1RhYmxlLmZpbmQoJ3Rib2R5JykuZW1wdHkoKTtcblxuICAgIGlmICghb3JkZXJzKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgZm9yIChjb25zdCBrZXkgaW4gT2JqZWN0LmtleXMob3JkZXJzKSkge1xuICAgICAgaWYgKCFvcmRlcnMuaGFzT3duUHJvcGVydHkoa2V5KSkge1xuICAgICAgICBjb250aW51ZTtcbiAgICAgIH1cblxuICAgICAgY29uc3Qgb3JkZXIgPSBvcmRlcnNba2V5XTtcbiAgICAgIGNvbnN0ICR0ZW1wbGF0ZSA9ICRyb3dUZW1wbGF0ZS5jbG9uZSgpO1xuXG4gICAgICAkdGVtcGxhdGUuZmluZCgnLmpzLW9yZGVyLWlkJykudGV4dChvcmRlci5pZF9vcmRlcik7XG4gICAgICAkdGVtcGxhdGUuZmluZCgnLmpzLW9yZGVyLWRhdGUnKS50ZXh0KG9yZGVyLmRhdGVfYWRkKTtcbiAgICAgICR0ZW1wbGF0ZS5maW5kKCcuanMtb3JkZXItcHJvZHVjdHMnKS50ZXh0KG9yZGVyLm5iX3Byb2R1Y3RzKTtcbiAgICAgICR0ZW1wbGF0ZS5maW5kKCcuanMtb3JkZXItdG90YWwtcGFpZCcpLnRleHQob3JkZXIudG90YWxfcGFpZF9yZWFsKTtcbiAgICAgICR0ZW1wbGF0ZS5maW5kKCcuanMtb3JkZXItc3RhdHVzJykudGV4dChvcmRlci5vcmRlcl9zdGF0ZSk7XG5cbiAgICAgICRvcmRlcnNUYWJsZS5maW5kKCd0Ym9keScpLmFwcGVuZCgkdGVtcGxhdGUpO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93cyBDYXJ0LCBWb3VjaGVycywgQWRkcmVzc2VzIGJsb2Nrc1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3Nob3dDYXJ0U3VtbWFyeSgpIHtcbiAgICAkKGNyZWF0ZU9yZGVyUGFnZU1hcC5jYXJ0QmxvY2spLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgICAkKGNyZWF0ZU9yZGVyUGFnZU1hcC52b3VjaGVyc0Jsb2NrKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgJChjcmVhdGVPcmRlclBhZ2VNYXAuYWRkcmVzc2VzQmxvY2spLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZW5kZXJzIERlbGl2ZXJ5ICYgSW52b2ljZSBhZGRyZXNzZXMgc2VsZWN0XG4gICAqXG4gICAqIEBwYXJhbSB7T2JqZWN0fSBjYXJ0U3VtbWFyeVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3JlbmRlckFkZHJlc3Nlc1NlbGVjdChjYXJ0U3VtbWFyeSkge1xuICAgIGxldCBkZWxpdmVyeUFkZHJlc3NEZXRhaWxzQ29udGVudCA9ICcnO1xuICAgIGxldCBpbnZvaWNlQWRkcmVzc0RldGFpbHNDb250ZW50ID0gJyc7XG5cbiAgICBjb25zdCAkZGVsaXZlcnlBZGRyZXNzRGV0YWlscyA9ICQoY3JlYXRlT3JkZXJQYWdlTWFwLmRlbGl2ZXJ5QWRkcmVzc0RldGFpbHMpO1xuICAgIGNvbnN0ICRpbnZvaWNlQWRkcmVzc0RldGFpbHMgPSAkKGNyZWF0ZU9yZGVyUGFnZU1hcC5pbnZvaWNlQWRkcmVzc0RldGFpbHMpO1xuICAgIGNvbnN0ICRkZWxpdmVyeUFkZHJlc3NTZWxlY3QgPSAkKGNyZWF0ZU9yZGVyUGFnZU1hcC5kZWxpdmVyeUFkZHJlc3NTZWxlY3QpO1xuICAgIGNvbnN0ICRpbnZvaWNlQWRkcmVzc1NlbGVjdCA9ICQoY3JlYXRlT3JkZXJQYWdlTWFwLmludm9pY2VBZGRyZXNzU2VsZWN0KTtcblxuICAgIGNvbnN0ICRhZGRyZXNzZXNDb250ZW50ID0gJChjcmVhdGVPcmRlclBhZ2VNYXAuYWRkcmVzc2VzQ29udGVudCk7XG4gICAgY29uc3QgJGFkZHJlc3Nlc1dhcm5pbmdDb250ZW50ID0gJChjcmVhdGVPcmRlclBhZ2VNYXAuYWRkcmVzc2VzV2FybmluZyk7XG5cbiAgICAkZGVsaXZlcnlBZGRyZXNzRGV0YWlscy5lbXB0eSgpO1xuICAgICRpbnZvaWNlQWRkcmVzc0RldGFpbHMuZW1wdHkoKTtcbiAgICAkZGVsaXZlcnlBZGRyZXNzU2VsZWN0LmVtcHR5KCk7XG4gICAgJGludm9pY2VBZGRyZXNzU2VsZWN0LmVtcHR5KCk7XG5cbiAgICBpZiAoY2FydFN1bW1hcnkuYWRkcmVzc2VzLmxlbmd0aCA9PT0gMCkge1xuICAgICAgJGFkZHJlc3Nlc1dhcm5pbmdDb250ZW50LnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgICAgICRhZGRyZXNzZXNDb250ZW50LmFkZENsYXNzKCdkLW5vbmUnKTtcblxuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgICRhZGRyZXNzZXNDb250ZW50LnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgICAkYWRkcmVzc2VzV2FybmluZ0NvbnRlbnQuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuXG4gICAgZm9yIChjb25zdCBrZXkgaW4gT2JqZWN0LmtleXMoY2FydFN1bW1hcnkuYWRkcmVzc2VzKSkge1xuICAgICAgaWYgKCFjYXJ0U3VtbWFyeS5hZGRyZXNzZXMuaGFzT3duUHJvcGVydHkoa2V5KSkge1xuICAgICAgICBjb250aW51ZTtcbiAgICAgIH1cblxuICAgICAgY29uc3QgYWRkcmVzcyA9IGNhcnRTdW1tYXJ5LmFkZHJlc3Nlc1trZXldO1xuXG4gICAgICBjb25zdCBkZWxpdmVyeUFkZHJlc3NPcHRpb24gPSB7XG4gICAgICAgIHZhbHVlOiBhZGRyZXNzLmlkX2FkZHJlc3MsXG4gICAgICAgIHRleHQ6IGFkZHJlc3MuYWxpYXMsXG4gICAgICB9O1xuXG4gICAgICBjb25zdCBpbnZvaWNlQWRkcmVzc09wdGlvbiA9IHtcbiAgICAgICAgdmFsdWU6IGFkZHJlc3MuaWRfYWRkcmVzcyxcbiAgICAgICAgdGV4dDogYWRkcmVzcy5hbGlhcyxcbiAgICAgIH07XG5cbiAgICAgIGlmIChwYXJzZUludChjYXJ0U3VtbWFyeS5jYXJ0LmlkX2FkZHJlc3NfZGVsaXZlcnkpID09PSBwYXJzZUludChhZGRyZXNzLmlkX2FkZHJlc3MpKSB7XG4gICAgICAgIGRlbGl2ZXJ5QWRkcmVzc0RldGFpbHNDb250ZW50ID0gYWRkcmVzcy5mb3JtYXRlZF9hZGRyZXNzO1xuICAgICAgICBkZWxpdmVyeUFkZHJlc3NPcHRpb24uc2VsZWN0ZWQgPSAnc2VsZWN0ZWQnO1xuICAgICAgfVxuXG4gICAgICBpZiAocGFyc2VJbnQoY2FydFN1bW1hcnkuY2FydC5pZF9hZGRyZXNzX2ludm9pY2UpID09PSBwYXJzZUludChhZGRyZXNzLmlkX2FkZHJlc3MpKSB7XG4gICAgICAgIGludm9pY2VBZGRyZXNzRGV0YWlsc0NvbnRlbnQgPSBhZGRyZXNzLmZvcm1hdGVkX2FkZHJlc3M7XG4gICAgICAgIGludm9pY2VBZGRyZXNzT3B0aW9uLnNlbGVjdGVkID0gJ3NlbGVjdGVkJztcbiAgICAgIH1cblxuICAgICAgJGRlbGl2ZXJ5QWRkcmVzc1NlbGVjdC5hcHBlbmQoJCgnPG9wdGlvbj4nLCBkZWxpdmVyeUFkZHJlc3NPcHRpb24pKTtcbiAgICAgICRpbnZvaWNlQWRkcmVzc1NlbGVjdC5hcHBlbmQoJCgnPG9wdGlvbj4nLCBpbnZvaWNlQWRkcmVzc09wdGlvbikpO1xuICAgIH1cblxuICAgIGlmIChkZWxpdmVyeUFkZHJlc3NEZXRhaWxzQ29udGVudCkge1xuICAgICAgJChjcmVhdGVPcmRlclBhZ2VNYXAuZGVsaXZlcnlBZGRyZXNzRGV0YWlscykuaHRtbChkZWxpdmVyeUFkZHJlc3NEZXRhaWxzQ29udGVudCk7XG4gICAgfVxuXG4gICAgaWYgKGludm9pY2VBZGRyZXNzRGV0YWlsc0NvbnRlbnQpIHtcbiAgICAgICQoY3JlYXRlT3JkZXJQYWdlTWFwLmludm9pY2VBZGRyZXNzRGV0YWlscykuaHRtbChpbnZvaWNlQWRkcmVzc0RldGFpbHNDb250ZW50KTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogQ2hhbmdlcyBjYXJ0IGFkZHJlc3Nlc1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2NoYW5nZUNhcnRBZGRyZXNzZXMoKSB7XG4gICAgJC5hamF4KHRoaXMuJGNvbnRhaW5lci5kYXRhKCdjYXJ0LWFkZHJlc3Nlcy11cmwnKSwge1xuICAgICAgZGF0YToge1xuICAgICAgICBpZF9jdXN0b21lcjogdGhpcy5kYXRhLmN1c3RvbWVyX2lkLFxuICAgICAgICBpZF9jYXJ0OiB0aGlzLmRhdGEuY2FydF9pZCxcbiAgICAgICAgaWRfYWRkcmVzc19kZWxpdmVyeTogJChjcmVhdGVPcmRlclBhZ2VNYXAuZGVsaXZlcnlBZGRyZXNzU2VsZWN0KS52YWwoKSxcbiAgICAgICAgaWRfYWRkcmVzc19pbnZvaWNlOiAkKGNyZWF0ZU9yZGVyUGFnZU1hcC5pbnZvaWNlQWRkcmVzc1NlbGVjdCkudmFsKCksXG4gICAgICB9LFxuICAgICAgZGF0YVR5cGU6ICdqc29uJyxcbiAgICB9KS50aGVuKChyZXNwb25zZSkgPT4ge1xuICAgICAgdGhpcy5fcGVyc2lzdENhcnRTdW1tYXJ5RGF0YShyZXNwb25zZSk7XG5cbiAgICAgIHRoaXMuX3JlbmRlckFkZHJlc3Nlc1NlbGVjdChyZXNwb25zZSk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogU3RvcmVzIGNhcnQgc3VtbWFyeSBpbnRvIFwic2Vzc2lvblwiIGxpa2UgdmFyaWFibGVcbiAgICpcbiAgICogQHBhcmFtIHtPYmplY3R9IGNhcnRTdW1tYXJ5XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfcGVyc2lzdENhcnRTdW1tYXJ5RGF0YShjYXJ0U3VtbWFyeSkge1xuICAgIHRoaXMuZGF0YS5jYXJ0X2lkID0gY2FydFN1bW1hcnkuY2FydC5pZDtcbiAgICB0aGlzLmRhdGEuZGVsaXZlcnlfYWRkcmVzc19pZCA9IGNhcnRTdW1tYXJ5LmNhcnQuaWRfYWRkcmVzc19kZWxpdmVyeTtcbiAgICB0aGlzLmRhdGEuaW52b2ljZV9hZGRyZXNzX2lkID0gY2FydFN1bW1hcnkuY2FydC5pZF9hZGRyZXNzX2ludm9pY2U7XG4gIH1cblxuICAvKipcbiAgICogQ2hvc2VzIHByZXZpb3VzIGNhcnQgZnJvbSB3aGljaCBvcmRlciB3aWxsIGJlIGNyZWF0ZWRcbiAgICpcbiAgICogQHBhcmFtIHtOdW1iZXJ9IGNhcnRJZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2Nob29zZVByZXZpb3VzQ2FydChjYXJ0SWQpIHtcbiAgICAkLmFqYXgodGhpcy4kY29udGFpbmVyLmRhdGEoJ2NhcnQtc3VtbWFyeS11cmwnKSwge1xuICAgICAgbWV0aG9kOiAnUE9TVCcsXG4gICAgICBkYXRhOiB7XG4gICAgICAgIGlkX2NhcnQ6IGNhcnRJZCxcbiAgICAgICAgaWRfY3VzdG9tZXI6IHRoaXMuZGF0YS5jdXN0b21lcl9pZCxcbiAgICAgIH0sXG4gICAgICBkYXRhVHlwZTogJ2pzb24nLFxuICAgIH0pLnRoZW4oKHJlc3BvbnNlKSA9PiB7XG4gICAgICB0aGlzLl9wZXJzaXN0Q2FydFN1bW1hcnlEYXRhKHJlc3BvbnNlKTtcblxuICAgICAgdGhpcy5fcmVuZGVyQ2FydFN1bW1hcnkocmVzcG9uc2UpO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci9jcmVhdGUtb3JkZXItcGFnZS5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5pbXBvcnQgQ3JlYXRlT3JkZXJQYWdlIGZyb20gJy4vY3JlYXRlLW9yZGVyLXBhZ2UnO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbiQoZG9jdW1lbnQpLnJlYWR5KCgpID0+IHtcbiAgY29uc3QgY3JlYXRlT3JkZXJQYWdlID0gbmV3IENyZWF0ZU9yZGVyUGFnZSgpO1xuXG4gIGNyZWF0ZU9yZGVyUGFnZS5saXN0ZW5Gb3JDdXN0b21lclNlYXJjaCgpO1xuICBjcmVhdGVPcmRlclBhZ2UubGlzdGVuRm9yQ3VzdG9tZXJDaG9vc2VGb3JPcmRlckNyZWF0aW9uKCk7XG59KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBjcmVhdGVPcmRlclBhZ2VNYXAgZnJvbSBcIi4vY3JlYXRlLW9yZGVyLW1hcFwiO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogU2VhcmNoZXMgY3VzdG9tZXJzIGZvciB3aGljaCBvcmRlciBpcyBiZWluZyBjcmVhdGVkXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEN1c3RvbWVyU2VhcmNoZXJDb21wb25lbnQge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLiRjb250YWluZXIgPSAkKGNyZWF0ZU9yZGVyUGFnZU1hcC5jdXN0b21lclNlYXJjaEJsb2NrKTtcbiAgICB0aGlzLiRzZWFyY2hJbnB1dCA9ICQoY3JlYXRlT3JkZXJQYWdlTWFwLmN1c3RvbWVyU2VhcmNoSW5wdXQpO1xuICAgIHRoaXMuJGN1c3RvbWVyU2VhcmNoUmVzdWx0QmxvY2sgPSAkKGNyZWF0ZU9yZGVyUGFnZU1hcC5jdXN0b21lclNlYXJjaFJlc3VsdHNCbG9jayk7XG5cbiAgICByZXR1cm4ge1xuICAgICAgb25DdXN0b21lclNlYXJjaDogKCkgPT4ge1xuICAgICAgICB0aGlzLl9kb1NlYXJjaCgpO1xuICAgICAgfSxcbiAgICAgIG9uQ3VzdG9tZXJDaG9vc2VGb3JPcmRlckNyZWF0aW9uOiAoZXZlbnQpID0+IHtcbiAgICAgICAgcmV0dXJuIHRoaXMuX2Nob29zZUN1c3RvbWVyRm9yT3JkZXJDcmVhdGlvbihldmVudCk7XG4gICAgICB9LFxuICAgICAgb25DdXN0b21lckNoYW5nZTogKCkgPT4ge1xuICAgICAgICB0aGlzLl9zaG93Q3VzdG9tZXJTZWFyY2goKTtcbiAgICAgIH1cbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqXG4gICAqIEBwYXJhbSB7RXZlbnR9IGNob29zZUN1c3RvbWVyRXZlbnRcbiAgICpcbiAgICogQHJldHVybiB7TnVtYmVyfVxuICAgKi9cbiAgX2Nob29zZUN1c3RvbWVyRm9yT3JkZXJDcmVhdGlvbihjaG9vc2VDdXN0b21lckV2ZW50KSB7XG4gICAgY29uc3QgJGNob29zZUJ0biA9ICQoY2hvb3NlQ3VzdG9tZXJFdmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICBjb25zdCAkY3VzdG9tZXJDYXJkID0gJGNob29zZUJ0bi5jbG9zZXN0KCcuY2FyZCcpO1xuXG4gICAgJGNob29zZUJ0bi5hZGRDbGFzcygnZC1ub25lJyk7XG5cbiAgICAkY3VzdG9tZXJDYXJkLmFkZENsYXNzKCdib3JkZXItc3VjY2VzcycpO1xuICAgICRjdXN0b21lckNhcmQuZmluZChjcmVhdGVPcmRlclBhZ2VNYXAuY2hhbmdlQ3VzdG9tZXJCdG4pLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcblxuICAgIHRoaXMuJGNvbnRhaW5lci5maW5kKGNyZWF0ZU9yZGVyUGFnZU1hcC5jdXN0b21lclNlYXJjaFJvdykuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICAgIHRoaXMuJGNvbnRhaW5lci5maW5kKGNyZWF0ZU9yZGVyUGFnZU1hcC5ub3RTZWxlY3RlZEN1c3RvbWVyU2VhcmNoUmVzdWx0cylcbiAgICAgIC5jbG9zZXN0KGNyZWF0ZU9yZGVyUGFnZU1hcC5jdXN0b21lclNlYXJjaFJlc3VsdENvbHVtbilcbiAgICAgIC5yZW1vdmUoKVxuICAgIDtcblxuICAgIHJldHVybiAkY2hvb3NlQnRuLmRhdGEoJ2N1c3RvbWVyLWlkJyk7XG4gIH1cblxuICAvKipcbiAgICogU2VhcmNoZXMgZm9yIGN1c3RvbWVyc1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2RvU2VhcmNoKCkge1xuICAgIGNvbnN0IG5hbWUgPSB0aGlzLiRzZWFyY2hJbnB1dC52YWwoKTtcblxuICAgIGlmICg0ID4gbmFtZS5sZW5ndGgpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICAkLmFqYXgodGhpcy4kc2VhcmNoSW5wdXQuZGF0YSgndXJsJyksIHtcbiAgICAgIG1ldGhvZDogJ0dFVCcsXG4gICAgICBkYXRhOiB7XG4gICAgICAgICdhY3Rpb24nOiAnc2VhcmNoQ3VzdG9tZXJzJyxcbiAgICAgICAgJ2FqYXgnOiAxLFxuICAgICAgICAnY3VzdG9tZXJfc2VhcmNoJzogbmFtZVxuICAgICAgfVxuICAgIH0pLnRoZW4oKHJlc3BvbnNlKSA9PiB7XG4gICAgICBjb25zdCByZXN1bHQgPSBKU09OLnBhcnNlKHJlc3BvbnNlKTtcblxuICAgICAgdGhpcy5fY2xlYXJTaG93bkN1c3RvbWVycygpO1xuXG4gICAgICBpZiAoIXJlc3VsdC5oYXNPd25Qcm9wZXJ0eSgnY3VzdG9tZXJzJykpIHtcbiAgICAgICAgdGhpcy5fc2hvd05vdEZvdW5kQ3VzdG9tZXJzKCk7XG5cbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICBmb3IgKGxldCBjdXN0b21lcklkIGluIHJlc3VsdC5jdXN0b21lcnMpIHtcbiAgICAgICAgbGV0IGN1c3RvbWVyUmVzdWx0ID0gcmVzdWx0LmN1c3RvbWVyc1tjdXN0b21lcklkXTtcbiAgICAgICAgbGV0IGN1c3RvbWVyID0ge1xuICAgICAgICAgIGlkOiBjdXN0b21lcklkLFxuICAgICAgICAgIGZpcnN0X25hbWU6IGN1c3RvbWVyUmVzdWx0LmZpcnN0bmFtZSxcbiAgICAgICAgICBsYXN0X25hbWU6IGN1c3RvbWVyUmVzdWx0Lmxhc3RuYW1lLFxuICAgICAgICAgIGVtYWlsOiBjdXN0b21lclJlc3VsdC5lbWFpbCxcbiAgICAgICAgICBiaXJ0aGRheTogY3VzdG9tZXJSZXN1bHQuYmlydGhkYXkgIT09ICcwMDAwLTAwLTAwJyA/IGN1c3RvbWVyUmVzdWx0LmJpcnRoZGF5IDogJyAnXG4gICAgICAgIH07XG5cbiAgICAgICAgdGhpcy5fc2hvd0N1c3RvbWVyKGN1c3RvbWVyKTtcbiAgICAgIH1cbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGVtcGxhdGUgYXMgalF1ZXJ5IG9iamVjdCB3aXRoIGN1c3RvbWVyIGRhdGFcbiAgICpcbiAgICogQHBhcmFtIHtPYmplY3R9IGN1c3RvbWVyXG4gICAqXG4gICAqIEByZXR1cm4ge2pRdWVyeX1cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zaG93Q3VzdG9tZXIoY3VzdG9tZXIpIHtcbiAgICBjb25zdCAkY3VzdG9tZXJTZWFyY2hSZXN1bHRUZW1wbGF0ZSA9ICQoJChjcmVhdGVPcmRlclBhZ2VNYXAuY3VzdG9tZXJTZWFyY2hSZXN1bHRUZW1wbGF0ZSkuaHRtbCgpKTtcbiAgICBjb25zdCAkdGVtcGxhdGUgPSAkY3VzdG9tZXJTZWFyY2hSZXN1bHRUZW1wbGF0ZS5jbG9uZSgpO1xuXG4gICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJQYWdlTWFwLmN1c3RvbWVyU2VhcmNoUmVzdWx0TmFtZSkudGV4dChgJHtjdXN0b21lci5maXJzdF9uYW1lfSAke2N1c3RvbWVyLmxhc3RfbmFtZX1gKTtcbiAgICAkdGVtcGxhdGUuZmluZChjcmVhdGVPcmRlclBhZ2VNYXAuY3VzdG9tZXJTZWFyY2hSZXN1bHRFbWFpbCkudGV4dChjdXN0b21lci5lbWFpbCk7XG4gICAgJHRlbXBsYXRlLmZpbmQoY3JlYXRlT3JkZXJQYWdlTWFwLmN1c3RvbWVyU2VhcmNoUmVzdWx0SWQpLnRleHQoY3VzdG9tZXIuaWQpO1xuICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyUGFnZU1hcC5jdXN0b21lclNlYXJjaFJlc3VsdEJpcnRoZGF5KS50ZXh0KGN1c3RvbWVyLmJpcnRoZGF5KTtcblxuICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyUGFnZU1hcC5jdXN0b21lckRldGFpbHNCdG4pLmRhdGEoJ2N1c3RvbWVyLWlkJywgY3VzdG9tZXIuaWQpO1xuICAgICR0ZW1wbGF0ZS5maW5kKGNyZWF0ZU9yZGVyUGFnZU1hcC5jaG9vc2VDdXN0b21lckJ0bikuZGF0YSgnY3VzdG9tZXItaWQnLCBjdXN0b21lci5pZCk7XG5cbiAgICByZXR1cm4gdGhpcy4kY3VzdG9tZXJTZWFyY2hSZXN1bHRCbG9jay5hcHBlbmQoJHRlbXBsYXRlKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTaG93cyBlbXB0eSByZXN1bHQgd2hlbiBjdXN0b21lciBpcyBub3QgZm91bmRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zaG93Tm90Rm91bmRDdXN0b21lcnMoKSB7XG4gICAgY29uc3QgJGVtcHR5UmVzdWx0VGVtcGxhdGUgPSAkKCQoJyNjdXN0b21lclNlYXJjaEVtcHR5UmVzdWx0VGVtcGxhdGUnKS5odG1sKCkpO1xuXG4gICAgdGhpcy4kY3VzdG9tZXJTZWFyY2hSZXN1bHRCbG9jay5hcHBlbmQoJGVtcHR5UmVzdWx0VGVtcGxhdGUpXG4gIH1cblxuICAvKipcbiAgICogQ2xlYXJzIHNob3duIGN1c3RvbWVyc1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2NsZWFyU2hvd25DdXN0b21lcnMoKSB7XG4gICAgdGhpcy4kY3VzdG9tZXJTZWFyY2hSZXN1bHRCbG9jay5lbXB0eSgpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNob3dzIGN1c3RvbWVyIHNlYXJjaCBibG9ja1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3Nob3dDdXN0b21lclNlYXJjaCgpIHtcbiAgICB0aGlzLiRjb250YWluZXIuZmluZChjcmVhdGVPcmRlclBhZ2VNYXAuY3VzdG9tZXJTZWFyY2hSb3cpLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxufVxuXG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci9jdXN0b21lci1zZWFyY2hlci1jb21wb25lbnQuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgY3JlYXRlT3JkZXJQYWdlTWFwIGZyb20gJy4vY3JlYXRlLW9yZGVyLW1hcCc7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBNYW51cHVsYXRlcyBVSSBvZiBTaGlwcGluZyBibG9jayBpbiBPcmRlciBjcmVhdGlvbiBwYWdlXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFNoaXBwaW5nUmVuZGVyZXIge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLiRjb250YWluZXIgPSAkKGNyZWF0ZU9yZGVyUGFnZU1hcC5zaGlwcGluZ0Jsb2NrKTtcbiAgfVxuXG4gIHNob3coKSB7XG4gICAgdGhpcy4kY29udGFpbmVyLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG4gIGhpZGUoKSB7XG4gICAgdGhpcy4kY29udGFpbmVyLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgfVxuXG5cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL3NoaXBwaW5nLXJlbmRlcmVyLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuLyoqXG4gKiBFbmNhcHN1bGF0ZXMgc2VsZWN0b3JzIGZvciBcIkNyZWF0ZSBvcmRlclwiIHBhZ2VcbiAqL1xuZXhwb3J0IGRlZmF1bHQge1xuICBvcmRlckNyZWF0aW9uQ29udGFpbmVyOiAnI29yZGVyQ3JlYXRpb25Db250YWluZXInLFxuXG4gIC8vIHNlbGVjdG9ycyByZWxhdGVkIHRvIGN1c3RvbWVyIGJsb2NrXG4gIGN1c3RvbWVyU2VhcmNoSW5wdXQ6ICcjY3VzdG9tZXJTZWFyY2hJbnB1dCcsXG4gIGN1c3RvbWVyU2VhcmNoUmVzdWx0c0Jsb2NrOiAnLmpzLWN1c3RvbWVyLXNlYXJjaC1yZXN1bHRzJyxcbiAgY3VzdG9tZXJTZWFyY2hSZXN1bHRUZW1wbGF0ZTogJyNjdXN0b21lclNlYXJjaFJlc3VsdFRlbXBsYXRlJyxcbiAgY2hhbmdlQ3VzdG9tZXJCdG46ICcuanMtY2hhbmdlLWN1c3RvbWVyLWJ0bicsXG4gIGN1c3RvbWVyU2VhcmNoUm93OiAnLmpzLXNlYXJjaC1jdXN0b21lci1yb3cnLFxuICBjaG9vc2VDdXN0b21lckJ0bjogJy5qcy1jaG9vc2UtY3VzdG9tZXItYnRuJyxcbiAgbm90U2VsZWN0ZWRDdXN0b21lclNlYXJjaFJlc3VsdHM6ICcuanMtY3VzdG9tZXItc2VhcmNoLXJlc3VsdDpub3QoLmJvcmRlci1zdWNjZXNzKScsXG4gIGN1c3RvbWVyU2VhcmNoUmVzdWx0TmFtZTogJy5qcy1jdXN0b21lci1uYW1lJyxcbiAgY3VzdG9tZXJTZWFyY2hSZXN1bHRFbWFpbDogJy5qcy1jdXN0b21lci1lbWFpbCcsXG4gIGN1c3RvbWVyU2VhcmNoUmVzdWx0SWQ6ICcuanMtY3VzdG9tZXItaWQnLFxuICBjdXN0b21lclNlYXJjaFJlc3VsdEJpcnRoZGF5OiAnLmpzLWN1c3RvbWVyLWJpcnRoZGF5JyxcbiAgY3VzdG9tZXJEZXRhaWxzQnRuOiAnLmpzLWRldGFpbHMtY3VzdG9tZXItYnRuJyxcbiAgY3VzdG9tZXJTZWFyY2hSZXN1bHRDb2x1bW46ICcuanMtY3VzdG9tZXItc2VhcmNoLXJlc3VsdC1jb2wnLFxuICBjdXN0b21lclNlYXJjaEJsb2NrOiAnI2N1c3RvbWVyU2VhcmNoQmxvY2snLFxuICBjdXN0b21lckNhcnRzVGFibGU6ICcjY3VzdG9tZXJDYXJ0c1RhYmxlJyxcbiAgY3VzdG9tZXJDYXJ0c1RhYmxlUm93VGVtcGxhdGU6ICcjY3VzdG9tZXJDYXJ0c1RhYmxlUm93VGVtcGxhdGUnLFxuICBjdXN0b21lckNoZWNrb3V0SGlzdG9yeTogJyNjdXN0b21lckNoZWNrb3V0SGlzdG9yeScsXG4gIGN1c3RvbWVyT3JkZXJzVGFibGU6ICcjY3VzdG9tZXJPcmRlcnNUYWJsZScsXG4gIGN1c3RvbWVyT3JkZXJzVGFibGVSb3dUZW1wbGF0ZTogJyNjdXN0b21lck9yZGVyc1RhYmxlUm93VGVtcGxhdGUnLFxuXG4gIC8vIHNlbGVjdG9ycyByZWxhdGVkIHRvIGNhcnQgYmxvY2tcbiAgY2FydEJsb2NrOiAnI2NhcnRCbG9jaycsXG5cbiAgLy8gc2VsZWN0b3JzIHJlbGF0ZWQgdG8gdm91Y2hlcnMgYmxvY2tcbiAgdm91Y2hlcnNCbG9jazogJyN2b3VjaGVyc0Jsb2NrJyxcblxuICAvLyBzZWxlY3RvcnMgcmVsYXRlZCB0byBhZGRyZXNzZXMgYmxvY2tcbiAgYWRkcmVzc2VzQmxvY2s6ICcjYWRkcmVzc2VzQmxvY2snLFxuICBkZWxpdmVyeUFkZHJlc3NEZXRhaWxzOiAnI2RlbGl2ZXJ5QWRkcmVzc0RldGFpbHMnLFxuICBpbnZvaWNlQWRkcmVzc0RldGFpbHM6ICcjaW52b2ljZUFkZHJlc3NEZXRhaWxzJyxcbiAgZGVsaXZlcnlBZGRyZXNzU2VsZWN0OiAnI2RlbGl2ZXJ5QWRkcmVzc1NlbGVjdCcsXG4gIGludm9pY2VBZGRyZXNzU2VsZWN0OiAnI2ludm9pY2VBZGRyZXNzU2VsZWN0JyxcbiAgYWRkcmVzc1NlbGVjdDogJy5qcy1hZGRyZXNzLXNlbGVjdCcsXG4gIGFkZHJlc3Nlc0NvbnRlbnQ6ICcjYWRkcmVzc2VzQ29udGVudCcsXG4gIGFkZHJlc3Nlc1dhcm5pbmc6ICcjYWRkcmVzc2VzV2FybmluZycsXG5cbiAgLy8gc2VsZWN0b3JzIHJlbGF0ZWQgdG8gc3VtbWFyeSBibG9ja1xuICBzdW1tYXJ5QmxvY2s6ICcjc3VtbWFyeUJsb2NrJyxcblxuICAvLyBzZWxlY3RvcnMgcmVsYXRlZCB0byBzaGlwcGluZyBibG9ja1xuICBzaGlwcGluZ0Jsb2NrOiAnI3NoaXBwaW5nQmxvY2snLFxufTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL2NyZWF0ZS1vcmRlci1tYXAuanMiXSwic291cmNlUm9vdCI6IiJ9