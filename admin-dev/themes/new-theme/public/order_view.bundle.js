window["order_view"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 378);
/******/ })
/************************************************************************/
/******/ ({

/***/ 278:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }(); /**
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


var _OrderViewPageMap = __webpack_require__(71);

var _OrderViewPageMap2 = _interopRequireDefault(_OrderViewPageMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.$;

/**
 * Manages adding/editing note for invoice documents.
 */

var InvoiceNoteManager = function () {
  function InvoiceNoteManager() {
    _classCallCheck(this, InvoiceNoteManager);

    this._initShowNoteFormEventHandler();
    this._initCloseNoteFormEventHandler();
    this._initEnterPaymentEventHandler();

    return {};
  }

  _createClass(InvoiceNoteManager, [{
    key: '_initShowNoteFormEventHandler',
    value: function _initShowNoteFormEventHandler() {
      $('.js-open-invoice-note-btn').on('click', function (event) {
        event.preventDefault();

        var $btn = $(event.currentTarget);
        var $noteRow = $btn.closest('tr').siblings('tr:first');

        $noteRow.removeClass('d-none');
      });
    }
  }, {
    key: '_initCloseNoteFormEventHandler',
    value: function _initCloseNoteFormEventHandler() {
      $('.js-cancel-invoice-note-btn').on('click', function (event) {
        $(event.currentTarget).closest('tr').addClass('d-none');
      });
    }
  }, {
    key: '_initEnterPaymentEventHandler',
    value: function _initEnterPaymentEventHandler() {
      $('.js-enter-payment-btn').on('click', function (event) {

        var $btn = $(event.currentTarget);
        var paymentAmount = $btn.data('payment-amount');

        $(_OrderViewPageMap2.default.viewOrderPaymentsBlock).get(0).scrollIntoView({ behavior: "smooth" });
        $(_OrderViewPageMap2.default.orderPaymentFormAmountInput).val(paymentAmount);
      });
    }
  }]);

  return InvoiceNoteManager;
}();

exports.default = InvoiceNoteManager;

/***/ }),

/***/ 279:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }(); /**
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


var _OrderViewPageMap = __webpack_require__(71);

var _OrderViewPageMap2 = _interopRequireDefault(_OrderViewPageMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.$;

var OrderShippingManager = function () {
  function OrderShippingManager() {
    _classCallCheck(this, OrderShippingManager);

    this._initOrderShippingUpdateEventHandler();
  }

  _createClass(OrderShippingManager, [{
    key: '_initOrderShippingUpdateEventHandler',
    value: function _initOrderShippingUpdateEventHandler() {
      $(_OrderViewPageMap2.default.showOrderShippingUpdateModalBtn).on('click', function (event) {
        var $btn = $(event.currentTarget);

        $(_OrderViewPageMap2.default.updateOrderShippingTrackingNumberInput).val($btn.data('order-tracking-number'));
        $(_OrderViewPageMap2.default.updateOrderShippingCurrentOrderCarrierIdInput).val($btn.data('order-carrier-id'));
      });
    }
  }]);

  return OrderShippingManager;
}();

exports.default = OrderShippingManager;

/***/ }),

/***/ 378:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _OrderViewPageMap = __webpack_require__(71);

var _OrderViewPageMap2 = _interopRequireDefault(_OrderViewPageMap);

var _orderShippingManager = __webpack_require__(279);

var _orderShippingManager2 = _interopRequireDefault(_orderShippingManager);

var _invoiceNoteManager = __webpack_require__(278);

var _invoiceNoteManager2 = _interopRequireDefault(_invoiceNoteManager);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$; /**
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

$(function () {
  var DISCOUNT_TYPE_AMOUNT = 'amount';
  var DISCOUNT_TYPE_PERCENT = 'percent';
  var DISCOUNT_TYPE_FREE_SHIPPING = 'free_shipping';

  new _orderShippingManager2.default();

  handlePaymentDetailsToggle();
  handlePrivateNoteChange();
  handleUpdateOrderStatusButton();

  new _invoiceNoteManager2.default();

  $(_OrderViewPageMap2.default.privateNoteToggleBtn).on('click', function (event) {
    event.preventDefault();
    togglePrivateNoteBlock();
  });

  initAddCartRuleFormHandler();
  initAddProductFormHandler();

  function handlePaymentDetailsToggle() {
    $(_OrderViewPageMap2.default.orderPaymentDetailsBtn).on('click', function (event) {
      var $paymentDetailRow = $(event.currentTarget).closest('tr').next(':first');

      $paymentDetailRow.toggleClass('d-none');
    });
  }

  function togglePrivateNoteBlock() {
    var $block = $(_OrderViewPageMap2.default.privateNoteBlock);
    var $btn = $(_OrderViewPageMap2.default.privateNoteToggleBtn);
    var isPrivateNoteOpened = $btn.hasClass('is-opened');

    if (isPrivateNoteOpened) {
      $btn.removeClass('is-opened');
      $block.addClass('d-none');
    } else {
      $btn.addClass('is-opened');
      $block.removeClass('d-none');
    }

    var $icon = $btn.find('.material-icons');
    $icon.text(isPrivateNoteOpened ? 'add' : 'remove');
  }

  function handlePrivateNoteChange() {
    var $submitBtn = $(_OrderViewPageMap2.default.privateNoteSubmitBtn);

    $(_OrderViewPageMap2.default.privateNoteInput).on('input', function (event) {
      var note = $(event.currentTarget).val();
      $submitBtn.prop('disabled', !note);
    });
  }

  function initAddProductFormHandler() {
    var $modal = $(_OrderViewPageMap2.default.updateOrderProductModal);

    $modal.on('click', '.js-order-product-update-btn', function (event) {
      var $btn = $(event.currentTarget);

      $modal.find('.js-update-product-name').text($btn.data('product-name'));
      $modal.find(_OrderViewPageMap2.default.updateOrderProductPriceTaxExclInput).val($btn.data('product-price-tax-excl'));
      $modal.find(_OrderViewPageMap2.default.updateOrderProductPriceTaxInclInput).val($btn.data('product-price-tax-incl'));
      $modal.find(_OrderViewPageMap2.default.updateOrderProductQuantityInput).val($btn.data('product-quantity'));
      $modal.find('form').attr('action', $btn.data('update-url'));
    });
  }

  function initAddCartRuleFormHandler() {
    var $modal = $(_OrderViewPageMap2.default.addCartRuleModal);
    var $form = $modal.find('form');
    var $valueHelp = $modal.find(_OrderViewPageMap2.default.cartRuleHelpText);
    var $invoiceSelect = $modal.find(_OrderViewPageMap2.default.addCartRuleInvoiceIdSelect);
    var $valueInput = $form.find(_OrderViewPageMap2.default.addCartRuleValueInput);
    var $valueFormGroup = $valueInput.closest('.form-group');

    $form.find(_OrderViewPageMap2.default.addCartRuleApplyOnAllInvoicesCheckbox).on('change', function (event) {
      var isChecked = $(event.currentTarget).is(':checked');

      $invoiceSelect.attr('disabled', isChecked);
    });

    $form.find(_OrderViewPageMap2.default.addCartRuleTypeSelect).on('change', function (event) {
      var selectedCartRuleType = $(event.currentTarget).val();

      if (selectedCartRuleType === DISCOUNT_TYPE_AMOUNT) {
        $valueHelp.removeClass('d-none');
      } else {
        $valueHelp.addClass('d-none');
      }

      if (selectedCartRuleType === DISCOUNT_TYPE_FREE_SHIPPING) {
        $valueFormGroup.addClass('d-none');
        $valueInput.attr('disabled', true);
      } else {
        $valueFormGroup.removeClass('d-none');
        $valueInput.attr('disabled', false);
      }
    });
  }

  function handleUpdateOrderStatusButton() {
    var $btn = $(_OrderViewPageMap2.default.updateOrderStatusActionBtn);

    $(_OrderViewPageMap2.default.updateOrderStatusActionInput).on('change', function (event) {
      var selectedOrderStatusId = $(event.currentTarget).val();

      $btn.prop('disabled', parseInt(selectedOrderStatusId, 10) === $btn.data('order-status-id'));
    });
  }

  $('a.partial-refund, a.partial_refund_cancel').on('click', function (e) {
    e.preventDefault();
    $('td.product_actions, th.product_actions, .partial_refund, .shipping-price').toggle();
  });
});

/***/ }),

/***/ 71:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
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

exports.default = {
  orderPaymentDetailsBtn: '.js-payment-details-btn',
  orderPaymentFormAmountInput: '#order_payment_amount',
  viewOrderPaymentsBlock: '#view_order_payments_block',
  privateNoteToggleBtn: '.js-private-note-toggle-btn',
  privateNoteBlock: '.js-private-note-block',
  privateNoteInput: '#private_note_note',
  privateNoteSubmitBtn: '.js-private-note-btn',
  updateOrderProductModal: '#updateOrderProductModal',
  updateOrderProductPriceTaxExclInput: '#update_order_product_price_tax_excl',
  updateOrderProductPriceTaxInclInput: '#update_order_product_price_tax_incl',
  updateOrderProductQuantityInput: '#update_order_product_quantity',
  addCartRuleModal: '#addOrderDiscountModal',
  addCartRuleApplyOnAllInvoicesCheckbox: '#add_order_cart_rule_apply_on_all_invoices',
  addCartRuleInvoiceIdSelect: '#add_order_cart_rule_invoice_id',
  addCartRuleTypeSelect: '#add_order_cart_rule_type',
  addCartRuleValueInput: '#add_order_cart_rule_value',
  cartRuleHelpText: '.js-cart-rule-value-help',
  updateOrderStatusActionBtn: '#update_order_status_action_btn',
  updateOrderStatusActionInput: '#update_order_status_action_input',
  updateOrderStatusActionForm: '#update_order_status_action_form',
  showOrderShippingUpdateModalBtn: '.js-update-shipping-btn',
  updateOrderShippingTrackingNumberInput: '#update_order_shipping_tracking_number',
  updateOrderShippingCurrentOrderCarrierIdInput: '#update_order_shipping_current_order_carrier_id'
};

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgM2M4N2QxZTI0NjE2N2YxMjcxNzciLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvb3JkZXIvaW52b2ljZS1ub3RlLW1hbmFnZXIuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvb3JkZXIvb3JkZXItc2hpcHBpbmctbWFuYWdlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci92aWV3LmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL09yZGVyVmlld1BhZ2VNYXAuanMiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsIkludm9pY2VOb3RlTWFuYWdlciIsIl9pbml0U2hvd05vdGVGb3JtRXZlbnRIYW5kbGVyIiwiX2luaXRDbG9zZU5vdGVGb3JtRXZlbnRIYW5kbGVyIiwiX2luaXRFbnRlclBheW1lbnRFdmVudEhhbmRsZXIiLCJvbiIsImV2ZW50IiwicHJldmVudERlZmF1bHQiLCIkYnRuIiwiY3VycmVudFRhcmdldCIsIiRub3RlUm93IiwiY2xvc2VzdCIsInNpYmxpbmdzIiwicmVtb3ZlQ2xhc3MiLCJhZGRDbGFzcyIsInBheW1lbnRBbW91bnQiLCJkYXRhIiwiT3JkZXJWaWV3UGFnZU1hcCIsInZpZXdPcmRlclBheW1lbnRzQmxvY2siLCJnZXQiLCJzY3JvbGxJbnRvVmlldyIsImJlaGF2aW9yIiwib3JkZXJQYXltZW50Rm9ybUFtb3VudElucHV0IiwidmFsIiwiT3JkZXJTaGlwcGluZ01hbmFnZXIiLCJfaW5pdE9yZGVyU2hpcHBpbmdVcGRhdGVFdmVudEhhbmRsZXIiLCJzaG93T3JkZXJTaGlwcGluZ1VwZGF0ZU1vZGFsQnRuIiwidXBkYXRlT3JkZXJTaGlwcGluZ1RyYWNraW5nTnVtYmVySW5wdXQiLCJ1cGRhdGVPcmRlclNoaXBwaW5nQ3VycmVudE9yZGVyQ2FycmllcklkSW5wdXQiLCJESVNDT1VOVF9UWVBFX0FNT1VOVCIsIkRJU0NPVU5UX1RZUEVfUEVSQ0VOVCIsIkRJU0NPVU5UX1RZUEVfRlJFRV9TSElQUElORyIsImhhbmRsZVBheW1lbnREZXRhaWxzVG9nZ2xlIiwiaGFuZGxlUHJpdmF0ZU5vdGVDaGFuZ2UiLCJoYW5kbGVVcGRhdGVPcmRlclN0YXR1c0J1dHRvbiIsInByaXZhdGVOb3RlVG9nZ2xlQnRuIiwidG9nZ2xlUHJpdmF0ZU5vdGVCbG9jayIsImluaXRBZGRDYXJ0UnVsZUZvcm1IYW5kbGVyIiwiaW5pdEFkZFByb2R1Y3RGb3JtSGFuZGxlciIsIm9yZGVyUGF5bWVudERldGFpbHNCdG4iLCIkcGF5bWVudERldGFpbFJvdyIsIm5leHQiLCJ0b2dnbGVDbGFzcyIsIiRibG9jayIsInByaXZhdGVOb3RlQmxvY2siLCJpc1ByaXZhdGVOb3RlT3BlbmVkIiwiaGFzQ2xhc3MiLCIkaWNvbiIsImZpbmQiLCJ0ZXh0IiwiJHN1Ym1pdEJ0biIsInByaXZhdGVOb3RlU3VibWl0QnRuIiwicHJpdmF0ZU5vdGVJbnB1dCIsIm5vdGUiLCJwcm9wIiwiJG1vZGFsIiwidXBkYXRlT3JkZXJQcm9kdWN0TW9kYWwiLCJ1cGRhdGVPcmRlclByb2R1Y3RQcmljZVRheEV4Y2xJbnB1dCIsInVwZGF0ZU9yZGVyUHJvZHVjdFByaWNlVGF4SW5jbElucHV0IiwidXBkYXRlT3JkZXJQcm9kdWN0UXVhbnRpdHlJbnB1dCIsImF0dHIiLCJhZGRDYXJ0UnVsZU1vZGFsIiwiJGZvcm0iLCIkdmFsdWVIZWxwIiwiY2FydFJ1bGVIZWxwVGV4dCIsIiRpbnZvaWNlU2VsZWN0IiwiYWRkQ2FydFJ1bGVJbnZvaWNlSWRTZWxlY3QiLCIkdmFsdWVJbnB1dCIsImFkZENhcnRSdWxlVmFsdWVJbnB1dCIsIiR2YWx1ZUZvcm1Hcm91cCIsImFkZENhcnRSdWxlQXBwbHlPbkFsbEludm9pY2VzQ2hlY2tib3giLCJpc0NoZWNrZWQiLCJpcyIsImFkZENhcnRSdWxlVHlwZVNlbGVjdCIsInNlbGVjdGVkQ2FydFJ1bGVUeXBlIiwidXBkYXRlT3JkZXJTdGF0dXNBY3Rpb25CdG4iLCJ1cGRhdGVPcmRlclN0YXR1c0FjdGlvbklucHV0Iiwic2VsZWN0ZWRPcmRlclN0YXR1c0lkIiwicGFyc2VJbnQiLCJlIiwidG9nZ2xlIiwidXBkYXRlT3JkZXJTdGF0dXNBY3Rpb25Gb3JtIl0sIm1hcHBpbmdzIjoiOztBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQSxtREFBMkMsY0FBYzs7QUFFekQ7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxhQUFLO0FBQ0w7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxtQ0FBMkIsMEJBQTBCLEVBQUU7QUFDdkQseUNBQWlDLGVBQWU7QUFDaEQ7QUFDQTtBQUNBOztBQUVBO0FBQ0EsOERBQXNELCtEQUErRDs7QUFFckg7QUFDQTs7QUFFQTtBQUNBOzs7Ozs7Ozs7Ozs7Ozs7cWpCQ2hFQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUF3QkE7Ozs7Ozs7O0FBRUEsSUFBTUEsSUFBSUMsT0FBT0QsQ0FBakI7O0FBRUE7Ozs7SUFHcUJFLGtCO0FBRW5CLGdDQUFjO0FBQUE7O0FBQ1osU0FBS0MsNkJBQUw7QUFDQSxTQUFLQyw4QkFBTDtBQUNBLFNBQUtDLDZCQUFMOztBQUVBLFdBQU8sRUFBUDtBQUNEOzs7O29EQUUrQjtBQUM5QkwsUUFBRSwyQkFBRixFQUErQk0sRUFBL0IsQ0FBa0MsT0FBbEMsRUFBMkMsVUFBQ0MsS0FBRCxFQUFXO0FBQ3BEQSxjQUFNQyxjQUFOOztBQUVBLFlBQU1DLE9BQU9ULEVBQUVPLE1BQU1HLGFBQVIsQ0FBYjtBQUNBLFlBQU1DLFdBQVdGLEtBQUtHLE9BQUwsQ0FBYSxJQUFiLEVBQW1CQyxRQUFuQixDQUE0QixVQUE1QixDQUFqQjs7QUFFQUYsaUJBQVNHLFdBQVQsQ0FBcUIsUUFBckI7QUFDRCxPQVBEO0FBUUQ7OztxREFFZ0M7QUFDL0Jk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NEJBSFg7QUFJYmlCLHdCQUFzQiw2QkFKVDtBQUtiUyxvQkFBa0Isd0JBTEw7QUFNYlEsb0JBQWtCLG9CQU5MO0FBT2JELHdCQUFzQixzQkFQVDtBQVFiSywyQkFBeUIsMEJBUlo7QUFTYkMsdUNBQXFDLHNDQVR4QjtBQVViQyx1Q0FBcUMsc0NBVnhCO0FBV2JDLG1DQUFpQyxnQ0FYcEI7QUFZYkUsb0JBQWtCLHdCQVpMO0FBYWJTLHlDQUF1Qyw0Q0FiMUI7QUFjYkosOEJBQTRCLGlDQWRmO0FBZWJPLHlCQUF1QiwyQkFmVjtBQWdCYkwseUJBQXVCLDRCQWhCVjtBQWlCYkosb0JBQWtCLDBCQWpCTDtBQWtCYlcsOEJBQTRCLGlDQWxCZjtBQW1CYkMsZ0NBQThCLG1DQW5CakI7QUFvQmJLLCtCQUE2QixrQ0FwQmhCO0FBcUJidkQsbUNBQWlDLHlCQXJCcEI7QUFzQmJDLDBDQUF3Qyx3Q0F0QjNCO0FBdUJiQyxpREFBK0M7QUF2QmxDLEMiLCJmaWxlIjoib3JkZXJfdmlldy5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBpZGVudGl0eSBmdW5jdGlvbiBmb3IgY2FsbGluZyBoYXJtb255IGltcG9ydHMgd2l0aCB0aGUgY29ycmVjdCBjb250ZXh0XG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmkgPSBmdW5jdGlvbih2YWx1ZSkgeyByZXR1cm4gdmFsdWU7IH07XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDM3OCk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gd2VicGFjay9ib290c3RyYXAgM2M4N2QxZTI0NjE2N2YxMjcxNzciLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuaW1wb3J0IE9yZGVyVmlld1BhZ2VNYXAgZnJvbSBcIi4vT3JkZXJWaWV3UGFnZU1hcFwiO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogTWFuYWdlcyBhZGRpbmcvZWRpdGluZyBub3RlIGZvciBpbnZvaWNlIGRvY3VtZW50cy5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgSW52b2ljZU5vdGVNYW5hZ2VyIHtcblxuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLl9pbml0U2hvd05vdGVGb3JtRXZlbnRIYW5kbGVyKCk7XG4gICAgdGhpcy5faW5pdENsb3NlTm90ZUZvcm1FdmVudEhhbmRsZXIoKTtcbiAgICB0aGlzLl9pbml0RW50ZXJQYXltZW50RXZlbnRIYW5kbGVyKCk7XG5cbiAgICByZXR1cm4ge307XG4gIH1cblxuICBfaW5pdFNob3dOb3RlRm9ybUV2ZW50SGFuZGxlcigpIHtcbiAgICAkKCcuanMtb3Blbi1pbnZvaWNlLW5vdGUtYnRuJykub24oJ2NsaWNrJywgKGV2ZW50KSA9PiB7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICBjb25zdCAkYnRuID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICAgIGNvbnN0ICRub3RlUm93ID0gJGJ0bi5jbG9zZXN0KCd0cicpLnNpYmxpbmdzKCd0cjpmaXJzdCcpO1xuXG4gICAgICAkbm90ZVJvdy5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgfSk7XG4gIH1cblxuICBfaW5pdENsb3NlTm90ZUZvcm1FdmVudEhhbmRsZXIoKSB7XG4gICAgJCgnLmpzLWNhbmNlbC1pbnZvaWNlLW5vdGUtYnRuJykub24oJ2NsaWNrJywgKGV2ZW50KSA9PiB7XG4gICAgICAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmNsb3Nlc3QoJ3RyJykuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICAgIH0pO1xuICB9XG5cbiAgX2luaXRFbnRlclBheW1lbnRFdmVudEhhbmRsZXIoKSB7XG4gICAgJCgnLmpzLWVudGVyLXBheW1lbnQtYnRuJykub24oJ2NsaWNrJywgKGV2ZW50KSA9PiB7XG5cbiAgICAgIGNvbnN0ICRidG4gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgICAgbGV0IHBheW1lbnRBbW91bnQgPSAkYnRuLmRhdGEoJ3BheW1lbnQtYW1vdW50Jyk7XG5cbiAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC52aWV3T3JkZXJQYXltZW50c0Jsb2NrKS5nZXQoMCkuc2Nyb2xsSW50b1ZpZXcoe2JlaGF2aW9yOiBcInNtb290aFwifSk7XG4gICAgICAkKE9yZGVyVmlld1BhZ2VNYXAub3JkZXJQYXltZW50Rm9ybUFtb3VudElucHV0KS52YWwocGF5bWVudEFtb3VudCk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL2ludm9pY2Utbm90ZS1tYW5hZ2VyLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cbmltcG9ydCBPcmRlclZpZXdQYWdlTWFwIGZyb20gJy4vT3JkZXJWaWV3UGFnZU1hcCc7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuZXhwb3J0IGRlZmF1bHQgY2xhc3MgT3JkZXJTaGlwcGluZ01hbmFnZXIge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLl9pbml0T3JkZXJTaGlwcGluZ1VwZGF0ZUV2ZW50SGFuZGxlcigpO1xuICB9XG5cbiAgX2luaXRPcmRlclNoaXBwaW5nVXBkYXRlRXZlbnRIYW5kbGVyKCkge1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5zaG93T3JkZXJTaGlwcGluZ1VwZGF0ZU1vZGFsQnRuKS5vbignY2xpY2snLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0ICRidG4gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuXG4gICAgICAkKE9yZGVyVmlld1BhZ2VNYXAudXBkYXRlT3JkZXJTaGlwcGluZ1RyYWNraW5nTnVtYmVySW5wdXQpLnZhbCgkYnRuLmRhdGEoJ29yZGVyLXRyYWNraW5nLW51bWJlcicpKTtcbiAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC51cGRhdGVPcmRlclNoaXBwaW5nQ3VycmVudE9yZGVyQ2FycmllcklkSW5wdXQpLnZhbCgkYnRuLmRhdGEoJ29yZGVyLWNhcnJpZXItaWQnKSk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL29yZGVyLXNoaXBwaW5nLW1hbmFnZXIuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgT3JkZXJWaWV3UGFnZU1hcCBmcm9tICcuL09yZGVyVmlld1BhZ2VNYXAnO1xuaW1wb3J0IE9yZGVyU2hpcHBpbmdNYW5hZ2VyIGZyb20gJy4vb3JkZXItc2hpcHBpbmctbWFuYWdlcic7XG5pbXBvcnQgSW52b2ljZU5vdGVNYW5hZ2VyIGZyb20gJy4vaW52b2ljZS1ub3RlLW1hbmFnZXInO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbiQoKCkgPT4ge1xuICBjb25zdCBESVNDT1VOVF9UWVBFX0FNT1VOVCA9ICdhbW91bnQnO1xuICBjb25zdCBESVNDT1VOVF9UWVBFX1BFUkNFTlQgPSAncGVyY2VudCc7XG4gIGNvbnN0IERJU0NPVU5UX1RZUEVfRlJFRV9TSElQUElORyA9ICdmcmVlX3NoaXBwaW5nJztcblxuICBuZXcgT3JkZXJTaGlwcGluZ01hbmFnZXIoKTtcblxuICBoYW5kbGVQYXltZW50RGV0YWlsc1RvZ2dsZSgpO1xuICBoYW5kbGVQcml2YXRlTm90ZUNoYW5nZSgpO1xuICBoYW5kbGVVcGRhdGVPcmRlclN0YXR1c0J1dHRvbigpO1xuXG4gIG5ldyBJbnZvaWNlTm90ZU1hbmFnZXIoKTtcblxuICAkKE9yZGVyVmlld1BhZ2VNYXAucHJpdmF0ZU5vdGVUb2dnbGVCdG4pLm9uKCdjbGljaycsIChldmVudCkgPT4ge1xuICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgdG9nZ2xlUHJpdmF0ZU5vdGVCbG9jaygpO1xuICB9KTtcblxuICBpbml0QWRkQ2FydFJ1bGVGb3JtSGFuZGxlcigpO1xuICBpbml0QWRkUHJvZHVjdEZvcm1IYW5kbGVyKCk7XG5cbiAgZnVuY3Rpb24gaGFuZGxlUGF5bWVudERldGFpbHNUb2dnbGUoKSB7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLm9yZGVyUGF5bWVudERldGFpbHNCdG4pLm9uKCdjbGljaycsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgJHBheW1lbnREZXRhaWxSb3cgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmNsb3Nlc3QoJ3RyJykubmV4dCgnOmZpcnN0Jyk7XG5cbiAgICAgICRwYXltZW50RGV0YWlsUm93LnRvZ2dsZUNsYXNzKCdkLW5vbmUnKTtcbiAgICB9KTtcbiAgfVxuXG4gIGZ1bmN0aW9uIHRvZ2dsZVByaXZhdGVOb3RlQmxvY2soKSB7XG4gICAgY29uc3QgJGJsb2NrID0gJChPcmRlclZpZXdQYWdlTWFwLnByaXZhdGVOb3RlQmxvY2spO1xuICAgIGNvbnN0ICRidG4gPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJpdmF0ZU5vdGVUb2dnbGVCdG4pO1xuICAgIGNvbnN0IGlzUHJpdmF0ZU5vdGVPcGVuZWQgPSAkYnRuLmhhc0NsYXNzKCdpcy1vcGVuZWQnKTtcblxuICAgIGlmIChpc1ByaXZhdGVOb3RlT3BlbmVkKSB7XG4gICAgICAkYnRuLnJlbW92ZUNsYXNzKCdpcy1vcGVuZWQnKTtcbiAgICAgICRibG9jay5hZGRDbGFzcygnZC1ub25lJyk7XG4gICAgfSBlbHNlIHtcbiAgICAgICRidG4uYWRkQ2xhc3MoJ2lzLW9wZW5lZCcpO1xuICAgICAgJGJsb2NrLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgICB9XG5cbiAgICBjb25zdCAkaWNvbiA9ICRidG4uZmluZCgnLm1hdGVyaWFsLWljb25zJyk7XG4gICAgJGljb24udGV4dChpc1ByaXZhdGVOb3RlT3BlbmVkID8gJ2FkZCcgOiAncmVtb3ZlJyk7XG4gIH1cblxuICBmdW5jdGlvbiBoYW5kbGVQcml2YXRlTm90ZUNoYW5nZSgpIHtcbiAgICBjb25zdCAkc3VibWl0QnRuID0gJChPcmRlclZpZXdQYWdlTWFwLnByaXZhdGVOb3RlU3VibWl0QnRuKTtcblxuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcml2YXRlTm90ZUlucHV0KS5vbignaW5wdXQnLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0IG5vdGUgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLnZhbCgpO1xuICAgICAgJHN1Ym1pdEJ0bi5wcm9wKCdkaXNhYmxlZCcsICFub3RlKTtcbiAgICB9KTtcbiAgfVxuXG4gIGZ1bmN0aW9uIGluaXRBZGRQcm9kdWN0Rm9ybUhhbmRsZXIoKSB7XG4gICAgY29uc3QgJG1vZGFsID0gJChPcmRlclZpZXdQYWdlTWFwLnVwZGF0ZU9yZGVyUHJvZHVjdE1vZGFsKTtcblxuICAgICRtb2RhbC5vbignY2xpY2snLCAnLmpzLW9yZGVyLXByb2R1Y3QtdXBkYXRlLWJ0bicsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgJGJ0biA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG5cbiAgICAgICRtb2RhbC5maW5kKCcuanMtdXBkYXRlLXByb2R1Y3QtbmFtZScpLnRleHQoJGJ0bi5kYXRhKCdwcm9kdWN0LW5hbWUnKSk7XG4gICAgICAkbW9kYWwuZmluZChPcmRlclZpZXdQYWdlTWFwLnVwZGF0ZU9yZGVyUHJvZHVjdFByaWNlVGF4RXhjbElucHV0KS52YWwoJGJ0bi5kYXRhKCdwcm9kdWN0LXByaWNlLXRheC1leGNsJykpO1xuICAgICAgJG1vZGFsLmZpbmQoT3JkZXJWaWV3UGFnZU1hcC51cGRhdGVPcmRlclByb2R1Y3RQcmljZVRheEluY2xJbnB1dCkudmFsKCRidG4uZGF0YSgncHJvZHVjdC1wcmljZS10YXgtaW5jbCcpKTtcbiAgICAgICRtb2RhbC5maW5kKE9yZGVyVmlld1BhZ2VNYXAudXBkYXRlT3JkZXJQcm9kdWN0UXVhbnRpdHlJbnB1dCkudmFsKCRidG4uZGF0YSgncHJvZHVjdC1xdWFudGl0eScpKTtcbiAgICAgICRtb2RhbC5maW5kKCdmb3JtJykuYXR0cignYWN0aW9uJywgJGJ0bi5kYXRhKCd1cGRhdGUtdXJsJykpO1xuICAgIH0pO1xuICB9XG5cbiAgZnVuY3Rpb24gaW5pdEFkZENhcnRSdWxlRm9ybUhhbmRsZXIoKSB7XG4gICAgY29uc3QgJG1vZGFsID0gJChPcmRlclZpZXdQYWdlTWFwLmFkZENhcnRSdWxlTW9kYWwpO1xuICAgIGNvbnN0ICRmb3JtID0gJG1vZGFsLmZpbmQoJ2Zvcm0nKTtcbiAgICBjb25zdCAkdmFsdWVIZWxwID0gJG1vZGFsLmZpbmQoT3JkZXJWaWV3UGFnZU1hcC5jYXJ0UnVsZUhlbHBUZXh0KTtcbiAgICBjb25zdCAkaW52b2ljZVNlbGVjdCA9ICRtb2RhbC5maW5kKE9yZGVyVmlld1BhZ2VNYXAuYWRkQ2FydFJ1bGVJbnZvaWNlSWRTZWxlY3QpO1xuICAgIGNvbnN0ICR2YWx1ZUlucHV0ID0gJGZvcm0uZmluZChPcmRlclZpZXdQYWdlTWFwLmFkZENhcnRSdWxlVmFsdWVJbnB1dCk7XG4gICAgY29uc3QgJHZhbHVlRm9ybUdyb3VwID0gJHZhbHVlSW5wdXQuY2xvc2VzdCgnLmZvcm0tZ3JvdXAnKTtcblxuICAgICRmb3JtLmZpbmQoT3JkZXJWaWV3UGFnZU1hcC5hZGRDYXJ0UnVsZUFwcGx5T25BbGxJbnZvaWNlc0NoZWNrYm94KS5vbignY2hhbmdlJywgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCBpc0NoZWNrZWQgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmlzKCc6Y2hlY2tlZCcpO1xuXG4gICAgICAkaW52b2ljZVNlbGVjdC5hdHRyKCdkaXNhYmxlZCcsIGlzQ2hlY2tlZCk7XG4gICAgfSk7XG5cbiAgICAkZm9ybS5maW5kKE9yZGVyVmlld1BhZ2VNYXAuYWRkQ2FydFJ1bGVUeXBlU2VsZWN0KS5vbignY2hhbmdlJywgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCBzZWxlY3RlZENhcnRSdWxlVHlwZSA9ICQoZXZlbnQuY3VycmVudFRhcmdldCkudmFsKCk7XG5cbiAgICAgIGlmIChzZWxlY3RlZENhcnRSdWxlVHlwZSA9PT0gRElTQ09VTlRfVFlQRV9BTU9VTlQpIHtcbiAgICAgICAgJHZhbHVlSGVscC5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICAkdmFsdWVIZWxwLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgICAgIH1cblxuICAgICAgaWYgKHNlbGVjdGVkQ2FydFJ1bGVUeXBlID09PSBESVNDT1VOVF9UWVBFX0ZSRUVfU0hJUFBJTkcpIHtcbiAgICAgICAgJHZhbHVlRm9ybUdyb3VwLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgICAgICAgJHZhbHVlSW5wdXQuYXR0cignZGlzYWJsZWQnLCB0cnVlKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgICR2YWx1ZUZvcm1Hcm91cC5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgICAgICR2YWx1ZUlucHV0LmF0dHIoJ2Rpc2FibGVkJywgZmFsc2UpO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG5cbiAgZnVuY3Rpb24gaGFuZGxlVXBkYXRlT3JkZXJTdGF0dXNCdXR0b24oKSB7XG4gICAgY29uc3QgJGJ0biA9ICQoT3JkZXJWaWV3UGFnZU1hcC51cGRhdGVPcmRlclN0YXR1c0FjdGlvbkJ0bik7XG5cbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAudXBkYXRlT3JkZXJTdGF0dXNBY3Rpb25JbnB1dCkub24oJ2NoYW5nZScsIChldmVudCkgPT4ge1xuICAgICAgY29uc3Qgc2VsZWN0ZWRPcmRlclN0YXR1c0lkID0gJChldmVudC5jdXJyZW50VGFyZ2V0KS52YWwoKTtcblxuICAgICAgJGJ0bi5wcm9wKCdkaXNhYmxlZCcsIHBhcnNlSW50KHNlbGVjdGVkT3JkZXJTdGF0dXNJZCwgMTApID09PSAkYnRuLmRhdGEoJ29yZGVyLXN0YXR1cy1pZCcpKTtcbiAgICB9KTtcbiAgfVxuXG4gICQoJ2EucGFydGlhbC1yZWZ1bmQsIGEucGFydGlhbF9yZWZ1bmRfY2FuY2VsJykub24oJ2NsaWNrJywgZnVuY3Rpb24oZSkge1xuICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAkKCd0ZC5wcm9kdWN0X2FjdGlvbnMsIHRoLnByb2R1Y3RfYWN0aW9ucywgLnBhcnRpYWxfcmVmdW5kLCAuc2hpcHBpbmctcHJpY2UnKS50b2dnbGUoKTtcbiAgfSk7XG59KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL3ZpZXcuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5leHBvcnQgZGVmYXVsdCB7XG4gIG9yZGVyUGF5bWVudERldGFpbHNCdG46ICcuanMtcGF5bWVudC1kZXRhaWxzLWJ0bicsXG4gIG9yZGVyUGF5bWVudEZvcm1BbW91bnRJbnB1dDogJyNvcmRlcl9wYXltZW50X2Ftb3VudCcsXG4gIHZpZXdPcmRlclBheW1lbnRzQmxvY2s6ICcjdmlld19vcmRlcl9wYXltZW50c19ibG9jaycsXG4gIHByaXZhdGVOb3RlVG9nZ2xlQnRuOiAnLmpzLXByaXZhdGUtbm90ZS10b2dnbGUtYnRuJyxcbiAgcHJpdmF0ZU5vdGVCbG9jazogJy5qcy1wcml2YXRlLW5vdGUtYmxvY2snLFxuICBwcml2YXRlTm90ZUlucHV0OiAnI3ByaXZhdGVfbm90ZV9ub3RlJyxcbiAgcHJpdmF0ZU5vdGVTdWJtaXRCdG46ICcuanMtcHJpdmF0ZS1ub3RlLWJ0bicsXG4gIHVwZGF0ZU9yZGVyUHJvZHVjdE1vZGFsOiAnI3VwZGF0ZU9yZGVyUHJvZHVjdE1vZGFsJyxcbiAgdXBkYXRlT3JkZXJQcm9kdWN0UHJpY2VUYXhFeGNsSW5wdXQ6ICcjdXBkYXRlX29yZGVyX3Byb2R1Y3RfcHJpY2VfdGF4X2V4Y2wnLFxuICB1cGRhdGVPcmRlclByb2R1Y3RQcmljZVRheEluY2xJbnB1dDogJyN1cGRhdGVfb3JkZXJfcHJvZHVjdF9wcmljZV90YXhfaW5jbCcsXG4gIHVwZGF0ZU9yZGVyUHJvZHVjdFF1YW50aXR5SW5wdXQ6ICcjdXBkYXRlX29yZGVyX3Byb2R1Y3RfcXVhbnRpdHknLFxuICBhZGRDYXJ0UnVsZU1vZGFsOiAnI2FkZE9yZGVyRGlzY291bnRNb2RhbCcsXG4gIGFkZENhcnRSdWxlQXBwbHlPbkFsbEludm9pY2VzQ2hlY2tib3g6ICcjYWRkX29yZGVyX2NhcnRfcnVsZV9hcHBseV9vbl9hbGxfaW52b2ljZXMnLFxuICBhZGRDYXJ0UnVsZUludm9pY2VJZFNlbGVjdDogJyNhZGRfb3JkZXJfY2FydF9ydWxlX2ludm9pY2VfaWQnLFxuICBhZGRDYXJ0UnVsZVR5cGVTZWxlY3Q6ICcjYWRkX29yZGVyX2NhcnRfcnVsZV90eXBlJyxcbiAgYWRkQ2FydFJ1bGVWYWx1ZUlucHV0OiAnI2FkZF9vcmRlcl9jYXJ0X3J1bGVfdmFsdWUnLFxuICBjYXJ0UnVsZUhlbHBUZXh0OiAnLmpzLWNhcnQtcnVsZS12YWx1ZS1oZWxwJyxcbiAgdXBkYXRlT3JkZXJTdGF0dXNBY3Rpb25CdG46ICcjdXBkYXRlX29yZGVyX3N0YXR1c19hY3Rpb25fYnRuJyxcbiAgdXBkYXRlT3JkZXJTdGF0dXNBY3Rpb25JbnB1dDogJyN1cGRhdGVfb3JkZXJfc3RhdHVzX2FjdGlvbl9pbnB1dCcsXG4gIHVwZGF0ZU9yZGVyU3RhdHVzQWN0aW9uRm9ybTogJyN1cGRhdGVfb3JkZXJfc3RhdHVzX2FjdGlvbl9mb3JtJyxcbiAgc2hvd09yZGVyU2hpcHBpbmdVcGRhdGVNb2RhbEJ0bjogJy5qcy11cGRhdGUtc2hpcHBpbmctYnRuJyxcbiAgdXBkYXRlT3JkZXJTaGlwcGluZ1RyYWNraW5nTnVtYmVySW5wdXQ6ICcjdXBkYXRlX29yZGVyX3NoaXBwaW5nX3RyYWNraW5nX251bWJlcicsXG4gIHVwZGF0ZU9yZGVyU2hpcHBpbmdDdXJyZW50T3JkZXJDYXJyaWVySWRJbnB1dDogJyN1cGRhdGVfb3JkZXJfc2hpcHBpbmdfY3VycmVudF9vcmRlcl9jYXJyaWVyX2lkJyxcbn07XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci9PcmRlclZpZXdQYWdlTWFwLmpzIl0sInNvdXJjZVJvb3QiOiIifQ==