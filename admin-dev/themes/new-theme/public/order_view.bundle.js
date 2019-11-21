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
/******/ 	return __webpack_require__(__webpack_require__.s = 387);
/******/ })
/************************************************************************/
/******/ ({

/***/ 248:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

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

var $ = window.$;

/**
 * TextWithLengthCounter handles input with length counter UI.
 *
 * Usage:
 *
 * There must be an element that wraps both input & counter display with ".js-text-with-length-counter" class.
 * Counter display must have ".js-countable-text-display" class and input must have ".js-countable-text-input" class.
 * Text input must have "data-max-length" attribute.
 *
 * <div class="js-text-with-length-counter">
 *  <span class="js-countable-text"></span>
 *  <input class="js-countable-input" data-max-length="255">
 * </div>
 *
 * In Javascript you must enable this component:
 *
 * new TextWithLengthCounter();
 */

var TextWithLengthCounter = function TextWithLengthCounter() {
  var _this = this;

  _classCallCheck(this, TextWithLengthCounter);

  this.wrapperSelector = '.js-text-with-length-counter';
  this.textSelector = '.js-countable-text';
  this.inputSelector = '.js-countable-input';

  $(document).on('input', this.wrapperSelector + ' ' + this.inputSelector, function (e) {
    var $input = $(e.currentTarget);
    var remainingLength = $input.data('max-length') - $input.val().length;

    $input.closest(_this.wrapperSelector).find(_this.textSelector).text(remainingLength);
  });
};

exports.default = TextWithLengthCounter;

/***/ }),

/***/ 284:
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


var _OrderViewPageMap = __webpack_require__(61);

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

/***/ 285:
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

var _OrderViewPageMap = __webpack_require__(61);

var _OrderViewPageMap2 = _interopRequireDefault(_OrderViewPageMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.$;

/**
 * All actions for order view page messages are registered in this class.
 */

var OrderViewPageMessagesHandler = function () {
  function OrderViewPageMessagesHandler() {
    var _this = this;

    _classCallCheck(this, OrderViewPageMessagesHandler);

    this.$orderMessageChangeWarning = $(_OrderViewPageMap2.default.orderMessageChangeWarning);
    this.$messagesContainer = $(_OrderViewPageMap2.default.orderMessagesContainer);

    return {
      listenForPredefinedMessageSelection: function listenForPredefinedMessageSelection() {
        return _this._handlePredefinedMessageSelection();
      },
      listenForFullMessagesOpen: function listenForFullMessagesOpen() {
        return _this._onFullMessagesOpen();
      }
    };
  }

  /**
   * Handles predefined order message selection.
   *
   * @private
   */


  _createClass(OrderViewPageMessagesHandler, [{
    key: '_handlePredefinedMessageSelection',
    value: function _handlePredefinedMessageSelection() {
      var _this2 = this;

      $(document).on('change', _OrderViewPageMap2.default.orderMessageNameSelect, function (e) {
        var $currentItem = $(e.currentTarget);
        var valueId = $currentItem.val();

        if (!valueId) {
          return;
        }

        // @todo: check size if is over then max not allow?
        var message = _this2.$messagesContainer.find('div[data-id=' + valueId + ']').text().trim();
        var $orderMessage = $(_OrderViewPageMap2.default.orderMessage);
        var isSameMessage = $orderMessage.val().trim() === message;

        if (isSameMessage) {
          return;
        }

        if ($orderMessage.val() && !confirm(_this2.$orderMessageChangeWarning.text())) {
          return;
        }

        $orderMessage.val(message);
      });
    }

    /**
     * Listens for event when all messages modal is being opened
     *
     * @private
     */

  }, {
    key: '_onFullMessagesOpen',
    value: function _onFullMessagesOpen() {
      var _this3 = this;

      $(document).on('click', _OrderViewPageMap2.default.openAllMessagesBtn, function () {
        return _this3._scrollToMsgListBottom();
      });
    }

    /**
     * Scrolls down to the bottom of all messages list
     *
     * @private
     */

  }, {
    key: '_scrollToMsgListBottom',
    value: function _scrollToMsgListBottom() {
      var $msgModal = $(_OrderViewPageMap2.default.allMessagesModal);
      var msgList = document.querySelector(_OrderViewPageMap2.default.allMessagesList);

      var classCheckInterval = window.setInterval(function () {
        if ($msgModal.hasClass('show')) {
          msgList.scrollTop = msgList.scrollHeight;
          clearInterval(classCheckInterval);
        }
      }, 10);
    }
  }]);

  return OrderViewPageMessagesHandler;
}();

exports.default = OrderViewPageMessagesHandler;

/***/ }),

/***/ 286:
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


var _OrderViewPageMap = __webpack_require__(61);

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

/***/ 387:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _OrderViewPageMap = __webpack_require__(61);

var _OrderViewPageMap2 = _interopRequireDefault(_OrderViewPageMap);

var _orderShippingManager = __webpack_require__(286);

var _orderShippingManager2 = _interopRequireDefault(_orderShippingManager);

var _invoiceNoteManager = __webpack_require__(284);

var _invoiceNoteManager2 = _interopRequireDefault(_invoiceNoteManager);

var _orderViewPageMessagesHandler = __webpack_require__(285);

var _orderViewPageMessagesHandler2 = _interopRequireDefault(_orderViewPageMessagesHandler);

var _textWithLengthCounter = __webpack_require__(248);

var _textWithLengthCounter2 = _interopRequireDefault(_textWithLengthCounter);

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
  new _textWithLengthCounter2.default();

  handlePaymentDetailsToggle();
  handlePrivateNoteChange();
  handleUpdateOrderStatusButton();

  new _invoiceNoteManager2.default();
  var orderViewPageMessageHandler = new _orderViewPageMessagesHandler2.default();
  orderViewPageMessageHandler.listenForPredefinedMessageSelection();
  orderViewPageMessageHandler.listenForFullMessagesOpen();
  $(_OrderViewPageMap2.default.privateNoteToggleBtn).on('click', function (event) {
    event.preventDefault();
    togglePrivateNoteBlock();
  });

  initAddCartRuleFormHandler();
  initAddProductFormHandler();
  initChangeAddressFormHandler();
  initHookTabs();

  function initHookTabs() {
    $(_OrderViewPageMap2.default.orderHookTabsContainer).find('.nav-tabs li:first-child a').tab('show');
  }

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

  function initChangeAddressFormHandler() {
    var $modal = $(_OrderViewPageMap2.default.updateCustomerAddressModal);

    $(_OrderViewPageMap2.default.openOrderAddressUpdateModalBtn).on('click', function (event) {
      var $btn = $(event.currentTarget);
      $modal.find(_OrderViewPageMap2.default.updateOrderAddressTypeInput).val($btn.data('address-type'));
    });
  }

  $('a.partial-refund, a.partial_refund_cancel').on('click', function (e) {
    e.preventDefault();
    $('td.product_actions, th.product_actions, .partial_refund, .shipping-price').toggle();
  });
});

/***/ }),

/***/ 61:
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
  updateOrderShippingCurrentOrderCarrierIdInput: '#update_order_shipping_current_order_carrier_id',
  updateCustomerAddressModal: '#updateCustomerAddressModal',
  openOrderAddressUpdateModalBtn: '.js-update-customer-address-modal-btn',
  updateOrderAddressTypeInput: '#change_order_address_address_type',
  orderMessageNameSelect: '#order_message_order_message',
  orderMessagesContainer: '.js-order-messages-container',
  orderMessage: '#order_message_message',
  orderMessageChangeWarning: '.js-message-change-warning',
  allMessagesModal: '#view_all_messages_modal',
  allMessagesList: '#all-messages-list',
  openAllMessagesBtn: '.js-open-all-messages-btn',
  orderHookTabsContainer: '#order_hook_tabs'
};

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgYTBjMDM4NjgwYjU0YWIwMGEwZjciLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9mb3JtL3RleHQtd2l0aC1sZW5ndGgtY291bnRlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci9pbnZvaWNlLW5vdGUtbWFuYWdlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci9tZXNzYWdlL29yZGVyLXZpZXctcGFnZS1tZXNzYWdlcy1oYW5kbGVyLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL29yZGVyLXNoaXBwaW5nLW1hbmFnZXIuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvb3JkZXIvdmlldy5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci9PcmRlclZpZXdQYWdlTWFwLmpzIl0sIm5hbWVzIjpbIiQiLCJ3aW5kb3ciLCJUZXh0V2l0aExlbmd0aENvdW50ZXIiLCJ3cmFwcGVyU2VsZWN0b3IiLCJ0ZXh0U2VsZWN0b3IiLCJpbnB1dFNlbGVjdG9yIiwiZG9jdW1lbnQiLCJvbiIsImUiLCIkaW5wdXQiLCJjdXJyZW50VGFyZ2V0IiwicmVtYWluaW5nTGVuZ3RoIiwiZGF0YSIsInZhbCIsImxlbmd0aCIsImNsb3Nlc3QiLCJmaW5kIiwidGV4dCIsIkludm9pY2VOb3RlTWFuYWdlciIsIl9pbml0U2hvd05vdGVGb3JtRXZlbnRIYW5kbGVyIiwiX2luaXRDbG9zZU5vdGVGb3JtRXZlbnRIYW5kbGVyIiwiX2luaXRFbnRlclBheW1lbnRFdmVudEhhbmRsZXIiLCJldmVudCIsInByZXZlbnREZWZhdWx0IiwiJGJ0biIsIiRub3RlUm93Iiwic2libGluZ3MiLCJyZW1vdmVDbGFzcyIsImFkZENsYXNzIiwicGF5bWVudEFtb3VudCIsIk9yZGVyVmlld1BhZ2VNYXAiLCJ2aWV3T3JkZXJQYXltZW50c0Jsb2NrIiwiZ2V0Iiwic2Nyb2xsSW50b1ZpZXciLCJiZWhhdmlvciIsIm9yZGVyUGF5bWVudEZvcm1BbW91bnRJbnB1dCIsIk9yZGVyVmlld1BhZ2VNZXNzYWdlc0hhbmRsZXIiLCIkb3JkZXJNZXNzYWdlQ2hhbmdlV2FybmluZyIsIm9yZGVyTWVzc2FnZUNoYW5nZVdhcm5pbmciLCIkbWVzc2FnZXNDb250YWluZXIiLCJvcmRlck1lc3NhZ2VzQ29udGFpbmVyIiwibGlzdGVuRm9yUHJlZGVmaW5lZE1lc3NhZ2VTZWxlY3Rpb24iLCJfaGFuZGxlUHJlZGVmaW5lZE1lc3NhZ2VTZWxlY3Rpb24iLCJsaXN0ZW5Gb3JGdWxsTWVzc2FnZXNPcGVuIiwiX29uRnVsbE1lc3NhZ2VzT3BlbiIsIm9yZGVyTWVzc2FnZU5hbWVTZWxlY3QiLCIkY3VycmVudEl0ZW0iLCJ2YWx1ZUlkIiwibWVzc2FnZSIsInRyaW0iLCIkb3JkZXJNZXNzYWdlIiwib3JkZXJNZXNzYWdlIiwiaXNTYW1lTWVzc2FnZSIsImNvbmZpcm0iLCJvcGVuQWxsTWVzc2FnZXNCdG4iLCJfc2Nyb2xsVG9Nc2dMaXN0Qm90dG9tIiwiJG1zZ01vZGFsIiwiYWxsTWVzc2FnZXNNb2RhbCIsIm1zZ0xpc3QiLCJxdWVyeVNlbGVjdG9yIiwiYWxsTWVzc2FnZXNMaXN0IiwiY2xhc3NDaGVja0ludGVydmFsIiwic2V0SW50ZXJ2YWwiLCJoYXNDbGFzcyIsInNjcm9sbFRvcCIsInNjcm9sbEhlaWdodCIsImNsZWFySW50ZXJ2YWwiLCJPcmRlclNoaXBwaW5nTWFuYWdlciIsIl9pbml0T3JkZXJTaGlwcGluZ1VwZGF0ZUV2ZW50SGFuZGxlciIsInNob3dPcmRlclNoaXBwaW5nVXBkYXRlTW9kYWxCdG4iLCJ1cGRhdGVPcmRlclNoaXBwaW5nVHJhY2tpbmdOdW1iZXJJbnB1dCIsInVwZGF0ZU9yZGVyU2hpcHBpbmdDdXJyZW50T3JkZXJDYXJyaWVySWRJbnB1dCIsIkRJU0NPVU5UX1RZUEVfQU1PVU5UIiwiRElTQ09VTlRfVFlQRV9QRVJDRU5UIiwiRElTQ09VTlRfVFlQRV9GUkVFX1NISVBQSU5HIiwiaGFuZGxlUGF5bWVudERldGFpbHNUb2dnbGUiLCJoYW5kbGVQcml2YXRlTm90ZUNoYW5nZSIsImhhbmRsZVVwZGF0ZU9yZGVyU3RhdHVzQnV0dG9uIiwib3JkZXJWaWV3UGFnZU1lc3NhZ2VIYW5kbGVyIiwicHJpdmF0ZU5vdGVUb2dnbGVCdG4iLCJ0b2dnbGVQcml2YXRlTm90ZUJsb2NrIiwiaW5pdEFkZENhcnRSdWxlRm9ybUhhbmRsZXIiLCJpbml0QWRkUHJvZHVjdEZvcm1IYW5kbGVyIiwiaW5pdENoYW5nZUFkZHJlc3NGb3JtSGFuZGxlciIsImluaXRIb29rVGFicyIsIm9yZGVySG9va1RhYnNDb250YWluZXIiLCJ0YWIiLCJvcmRlclBheW1lbnREZXRhaWxzQnRuIiwiJHBheW1lbnREZXRhaWxSb3ciLCJuZXh0IiwidG9nZ2xlQ2xhc3MiLCIkYmxvY2siLCJwcml2YXRlTm90ZUJsb2NrIiwiaXNQcml2YXRlTm90ZU9wZW5lZCIsIiRpY29uIiwiJHN1Ym1pdEJ0biIsInByaXZhdGVOb3RlU3VibWl0QnRuIiwicHJpdmF0ZU5vdGVJbnB1dCIsIm5vdGUiLCJwcm9wIiwiJG1vZGFsIiwidXBkYXRlT3JkZXJQcm9kdWN0TW9kYWwiLCJ1cGRhdGVPcmRlclByb2R1Y3RQcmljZVRheEV4Y2xJbnB1dCIsInVwZGF0ZU9yZGVyUHJvZHVjdFByaWNlVGF4SW5jbElucHV0IiwidXBkYXRlT3JkZXJQcm9kdWN0UXVhbnRpdHlJbnB1dCIsImF0dHIiLCJhZGRDYXJ0UnVsZU1vZGFsIiwiJGZvcm0iLCIkdmFsdWVIZWxwIiwiY2FydFJ1bGVIZWxwVGV4dCIsIiRpbnZvaWNlU2VsZWN0IiwiYWRkQ2FydFJ1bGVJbnZvaWNlSWRTZWxlY3QiLCIkdmFsdWVJbnB1dCIsImFkZENhcnRSdWxlVmFsdWVJbnB1dCIsIiR2YWx1ZUZvcm1Hcm91cCIsImFkZENhcnRSdWxlQXBwbHlPbkFsbEludm9pY2VzQ2hlY2tib3giLCJpc0NoZWNrZWQiLCJpcyIsImFkZENhcnRSdWxlVHlwZVNlbGVjdCIsInNlbGVjdGVkQ2FydFJ1bGVUeXBlIiwidXBkYXRlT3JkZXJTdGF0dXNBY3Rpb25CdG4iLCJ1cGRhdGVPcmRlclN0YXR1c0FjdGlvbklucHV0Iiwic2VsZWN0ZWRPcmRlclN0YXR1c0lkIiwicGFyc2VJbnQiLCJ1cGRhdGVDdXN0b21lckFkZHJlc3NNb2RhbCIsIm9wZW5PcmRlckFkZHJlc3NVcGRhdGVNb2RhbEJ0biIsInVwZGF0ZU9yZGVyQWRkcmVzc1R5cGVJbnB1dCIsInRvZ2dsZSIsInVwZGF0ZU9yZGVyU3RhdHVzQWN0aW9uRm9ybSJdLCJtYXBwaW5ncyI6Ijs7QUFBQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7O0FBR0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0EsbURBQTJDLGNBQWM7O0FBRXpEO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsYUFBSztBQUNMO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsbUNBQTJCLDBCQUEwQixFQUFFO0FBQ3ZELHlDQUFpQyxlQUFlO0FBQ2hEO0FBQ0E7QUFDQTs7QUFFQTtBQUNBLDhEQUFzRCwrREFBK0Q7O0FBRXJIO0FBQ0E7O0FBRUE7QUFDQTs7Ozs7Ozs7Ozs7Ozs7Ozs7QUNoRUE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUF5QkEsSUFBTUEsSUFBSUMsT0FBT0QsQ0FBakI7O0FBRUE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7SUFrQnFCRSxxQixHQUNuQixpQ0FBYztBQUFBOztBQUFBOztBQUNaLE9BQUtDLGVBQUwsR0FBdUIsOEJBQXZCO0FBQ0EsT0FBS0MsWUFBTCxHQUFvQixvQkFBcEI7QUFDQSxPQUFLQyxhQUFMLEdBQXFCLHFCQUFyQjs7QUFFQUwsSUFBRU0sUUFBRixFQUFZQyxFQUFaLENBQWUsT0FBZixFQUEyQixLQUFLSixlQUFoQyxTQUFtRCxLQUFLRSxhQUF4RCxFQUF5RSxVQUFDRyxDQUFELEVBQU87QUFDOUUs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R2JKLDBCQUF3Qiw0QkFIWDtBQUliZ0Qsd0JBQXNCLDZCQUpUO0FBS2JhLG9CQUFrQix3QkFMTDtBQU1iSyxvQkFBa0Isb0JBTkw7QUFPYkQsd0JBQXNCLHNCQVBUO0FBUWJLLDJCQUF5QiwwQkFSWjtBQVNiQyx1Q0FBcUMsc0NBVHhCO0FBVWJDLHVDQUFxQyxzQ0FWeEI7QUFXYkMsbUNBQWlDLGdDQVhwQjtBQVliRSxvQkFBa0Isd0JBWkw7QUFhYlMseUNBQXVDLDRDQWIxQjtBQWNiSiw4QkFBNEIsaUNBZGY7QUFlYk8seUJBQXVCLDJCQWZWO0FBZ0JiTCx5QkFBdUIsNEJBaEJWO0FBaUJiSixvQkFBa0IsMEJBakJMO0FBa0JiVyw4QkFBNEIsaUNBbEJmO0FBbUJiQyxnQ0FBOEIsbUNBbkJqQjtBQW9CYk8sK0JBQTZCLGtDQXBCaEI7QUFxQmIzRCxtQ0FBaUMseUJBckJwQjtBQXNCYkMsMENBQXdDLHdDQXRCM0I7QUF1QmJDLGlEQUErQyxpREF2QmxDO0FBd0JicUQsOEJBQTRCLDZCQXhCZjtBQXlCYkMsa0NBQWdDLHVDQXpCbkI7QUEwQmJDLCtCQUE2QixvQ0ExQmhCO0FBMkJiakYsMEJBQXdCLDhCQTNCWDtBQTRCYkwsMEJBQXdCLDhCQTVCWDtBQTZCYlcsZ0JBQWMsd0JBN0JEO0FBOEJiYiw2QkFBMkIsNEJBOUJkO0FBK0JibUIsb0JBQWtCLDBCQS9CTDtBQWdDYkcsbUJBQWlCLG9CQWhDSjtBQWlDYk4sc0JBQW9CLDJCQWpDUDtBQWtDYitCLDBCQUF3QjtBQWxDWCxDIiwiZmlsZSI6Im9yZGVyX3ZpZXcuYnVuZGxlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gaWRlbnRpdHkgZnVuY3Rpb24gZm9yIGNhbGxpbmcgaGFybW9ueSBpbXBvcnRzIHdpdGggdGhlIGNvcnJlY3QgY29udGV4dFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5pID0gZnVuY3Rpb24odmFsdWUpIHsgcmV0dXJuIHZhbHVlOyB9O1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHtcbiBcdFx0XHRcdGNvbmZpZ3VyYWJsZTogZmFsc2UsXG4gXHRcdFx0XHRlbnVtZXJhYmxlOiB0cnVlLFxuIFx0XHRcdFx0Z2V0OiBnZXR0ZXJcbiBcdFx0XHR9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCJcIjtcblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSAzODcpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIHdlYnBhY2svYm9vdHN0cmFwIGEwYzAzODY4MGI1NGFiMDBhMGY3IiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIFRleHRXaXRoTGVuZ3RoQ291bnRlciBoYW5kbGVzIGlucHV0IHdpdGggbGVuZ3RoIGNvdW50ZXIgVUkuXG4gKlxuICogVXNhZ2U6XG4gKlxuICogVGhlcmUgbXVzdCBiZSBhbiBlbGVtZW50IHRoYXQgd3JhcHMgYm90aCBpbnB1dCAmIGNvdW50ZXIgZGlzcGxheSB3aXRoIFwiLmpzLXRleHQtd2l0aC1sZW5ndGgtY291bnRlclwiIGNsYXNzLlxuICogQ291bnRlciBkaXNwbGF5IG11c3QgaGF2ZSBcIi5qcy1jb3VudGFibGUtdGV4dC1kaXNwbGF5XCIgY2xhc3MgYW5kIGlucHV0IG11c3QgaGF2ZSBcIi5qcy1jb3VudGFibGUtdGV4dC1pbnB1dFwiIGNsYXNzLlxuICogVGV4dCBpbnB1dCBtdXN0IGhhdmUgXCJkYXRhLW1heC1sZW5ndGhcIiBhdHRyaWJ1dGUuXG4gKlxuICogPGRpdiBjbGFzcz1cImpzLXRleHQtd2l0aC1sZW5ndGgtY291bnRlclwiPlxuICogIDxzcGFuIGNsYXNzPVwianMtY291bnRhYmxlLXRleHRcIj48L3NwYW4+XG4gKiAgPGlucHV0IGNsYXNzPVwianMtY291bnRhYmxlLWlucHV0XCIgZGF0YS1tYXgtbGVuZ3RoPVwiMjU1XCI+XG4gKiA8L2Rpdj5cbiAqXG4gKiBJbiBKYXZhc2NyaXB0IHlvdSBtdXN0IGVuYWJsZSB0aGlzIGNvbXBvbmVudDpcbiAqXG4gKiBuZXcgVGV4dFdpdGhMZW5ndGhDb3VudGVyKCk7XG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFRleHRXaXRoTGVuZ3RoQ291bnRlciB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHRoaXMud3JhcHBlclNlbGVjdG9yID0gJy5qcy10ZXh0LXdpdGgtbGVuZ3RoLWNvdW50ZXInO1xuICAgIHRoaXMudGV4dFNlbGVjdG9yID0gJy5qcy1jb3VudGFibGUtdGV4dCc7XG4gICAgdGhpcy5pbnB1dFNlbGVjdG9yID0gJy5qcy1jb3VudGFibGUtaW5wdXQnO1xuXG4gICAgJChkb2N1bWVudCkub24oJ2lucHV0JywgYCR7dGhpcy53cmFwcGVyU2VsZWN0b3J9ICR7dGhpcy5pbnB1dFNlbGVjdG9yfWAsIChlKSA9PiB7XG4gICAgICBjb25zdCAkaW5wdXQgPSAkKGUuY3VycmVudFRhcmdldCk7XG4gICAgICBjb25zdCByZW1haW5pbmdMZW5ndGggPSAkaW5wdXQuZGF0YSgnbWF4LWxlbmd0aCcpIC0gJGlucHV0LnZhbCgpLmxlbmd0aDtcblxuICAgICAgJGlucHV0LmNsb3Nlc3QodGhpcy53cmFwcGVyU2VsZWN0b3IpLmZpbmQodGhpcy50ZXh0U2VsZWN0b3IpLnRleHQocmVtYWluaW5nTGVuZ3RoKTtcbiAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9mb3JtL3RleHQtd2l0aC1sZW5ndGgtY291bnRlci5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5pbXBvcnQgT3JkZXJWaWV3UGFnZU1hcCBmcm9tIFwiLi9PcmRlclZpZXdQYWdlTWFwXCI7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBNYW5hZ2VzIGFkZGluZy9lZGl0aW5nIG5vdGUgZm9yIGludm9pY2UgZG9jdW1lbnRzLlxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBJbnZvaWNlTm90ZU1hbmFnZXIge1xuXG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHRoaXMuX2luaXRTaG93Tm90ZUZvcm1FdmVudEhhbmRsZXIoKTtcbiAgICB0aGlzLl9pbml0Q2xvc2VOb3RlRm9ybUV2ZW50SGFuZGxlcigpO1xuICAgIHRoaXMuX2luaXRFbnRlclBheW1lbnRFdmVudEhhbmRsZXIoKTtcblxuICAgIHJldHVybiB7fTtcbiAgfVxuXG4gIF9pbml0U2hvd05vdGVGb3JtRXZlbnRIYW5kbGVyKCkge1xuICAgICQoJy5qcy1vcGVuLWludm9pY2Utbm90ZS1idG4nKS5vbignY2xpY2snLCAoZXZlbnQpID0+IHtcbiAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgIGNvbnN0ICRidG4gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgICAgY29uc3QgJG5vdGVSb3cgPSAkYnRuLmNsb3Nlc3QoJ3RyJykuc2libGluZ3MoJ3RyOmZpcnN0Jyk7XG5cbiAgICAgICRub3RlUm93LnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgICB9KTtcbiAgfVxuXG4gIF9pbml0Q2xvc2VOb3RlRm9ybUV2ZW50SGFuZGxlcigpIHtcbiAgICAkKCcuanMtY2FuY2VsLWludm9pY2Utbm90ZS1idG4nKS5vbignY2xpY2snLCAoZXZlbnQpID0+IHtcbiAgICAgICQoZXZlbnQuY3VycmVudFRhcmdldCkuY2xvc2VzdCgndHInKS5hZGRDbGFzcygnZC1ub25lJyk7XG4gICAgfSk7XG4gIH1cblxuICBfaW5pdEVudGVyUGF5bWVudEV2ZW50SGFuZGxlcigpIHtcbiAgICAkKCcuanMtZW50ZXItcGF5bWVudC1idG4nKS5vbignY2xpY2snLCAoZXZlbnQpID0+IHtcblxuICAgICAgY29uc3QgJGJ0biA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgICBsZXQgcGF5bWVudEFtb3VudCA9ICRidG4uZGF0YSgncGF5bWVudC1hbW91bnQnKTtcblxuICAgICAgJChPcmRlclZpZXdQYWdlTWFwLnZpZXdPcmRlclBheW1lbnRzQmxvY2spLmdldCgwKS5zY3JvbGxJbnRvVmlldyh7YmVoYXZpb3I6IFwic21vb3RoXCJ9KTtcbiAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC5vcmRlclBheW1lbnRGb3JtQW1vdW50SW5wdXQpLnZhbChwYXltZW50QW1vdW50KTtcbiAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvaW52b2ljZS1ub3RlLW1hbmFnZXIuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgT3JkZXJWaWV3UGFnZU1hcCBmcm9tICcuLi9PcmRlclZpZXdQYWdlTWFwJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIEFsbCBhY3Rpb25zIGZvciBvcmRlciB2aWV3IHBhZ2UgbWVzc2FnZXMgYXJlIHJlZ2lzdGVyZWQgaW4gdGhpcyBjbGFzcy5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgT3JkZXJWaWV3UGFnZU1lc3NhZ2VzSGFuZGxlciB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHRoaXMuJG9yZGVyTWVzc2FnZUNoYW5nZVdhcm5pbmcgPSAkKE9yZGVyVmlld1BhZ2VNYXAub3JkZXJNZXNzYWdlQ2hhbmdlV2FybmluZyk7XG4gICAgdGhpcy4kbWVzc2FnZXNDb250YWluZXIgPSAkKE9yZGVyVmlld1BhZ2VNYXAub3JkZXJNZXNzYWdlc0NvbnRhaW5lcik7XG5cbiAgICByZXR1cm4ge1xuICAgICAgbGlzdGVuRm9yUHJlZGVmaW5lZE1lc3NhZ2VTZWxlY3Rpb246ICgpID0+IHRoaXMuX2hhbmRsZVByZWRlZmluZWRNZXNzYWdlU2VsZWN0aW9uKCksXG4gICAgICBsaXN0ZW5Gb3JGdWxsTWVzc2FnZXNPcGVuOiAoKSA9PiB0aGlzLl9vbkZ1bGxNZXNzYWdlc09wZW4oKSxcbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqIEhhbmRsZXMgcHJlZGVmaW5lZCBvcmRlciBtZXNzYWdlIHNlbGVjdGlvbi5cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oYW5kbGVQcmVkZWZpbmVkTWVzc2FnZVNlbGVjdGlvbigpIHtcbiAgICAkKGRvY3VtZW50KS5vbignY2hhbmdlJywgT3JkZXJWaWV3UGFnZU1hcC5vcmRlck1lc3NhZ2VOYW1lU2VsZWN0LCAoZSkgPT4ge1xuICAgICAgY29uc3QgJGN1cnJlbnRJdGVtID0gJChlLmN1cnJlbnRUYXJnZXQpO1xuICAgICAgY29uc3QgdmFsdWVJZCA9ICRjdXJyZW50SXRlbS52YWwoKTtcblxuICAgICAgaWYgKCF2YWx1ZUlkKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cblxuICAgICAgLy8gQHRvZG86IGNoZWNrIHNpemUgaWYgaXMgb3ZlciB0aGVuIG1heCBub3QgYWxsb3c/XG4gICAgICBjb25zdCBtZXNzYWdlID0gdGhpcy4kbWVzc2FnZXNDb250YWluZXIuZmluZChgZGl2W2RhdGEtaWQ9JHt2YWx1ZUlkfV1gKS50ZXh0KCkudHJpbSgpO1xuICAgICAgY29uc3QgJG9yZGVyTWVzc2FnZSA9ICQoT3JkZXJWaWV3UGFnZU1hcC5vcmRlck1lc3NhZ2UpO1xuICAgICAgY29uc3QgaXNTYW1lTWVzc2FnZSA9ICRvcmRlck1lc3NhZ2UudmFsKCkudHJpbSgpID09PSBtZXNzYWdlO1xuXG4gICAgICBpZiAoaXNTYW1lTWVzc2FnZSkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG5cbiAgICAgIGlmICgkb3JkZXJNZXNzYWdlLnZhbCgpICYmICFjb25maXJtKHRoaXMuJG9yZGVyTWVzc2FnZUNoYW5nZVdhcm5pbmcudGV4dCgpKSkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG5cbiAgICAgICRvcmRlck1lc3NhZ2UudmFsKG1lc3NhZ2UpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIExpc3RlbnMgZm9yIGV2ZW50IHdoZW4gYWxsIG1lc3NhZ2VzIG1vZGFsIGlzIGJlaW5nIG9wZW5lZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uRnVsbE1lc3NhZ2VzT3BlbigpIHtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCBPcmRlclZpZXdQYWdlTWFwLm9wZW5BbGxNZXNzYWdlc0J0biwgKCkgPT4gdGhpcy5fc2Nyb2xsVG9Nc2dMaXN0Qm90dG9tKCkpO1xuICB9XG5cbiAgLyoqXG4gICAqIFNjcm9sbHMgZG93biB0byB0aGUgYm90dG9tIG9mIGFsbCBtZXNzYWdlcyBsaXN0XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc2Nyb2xsVG9Nc2dMaXN0Qm90dG9tKCkge1xuICAgIGNvbnN0ICRtc2dNb2RhbCA9ICQoT3JkZXJWaWV3UGFnZU1hcC5hbGxNZXNzYWdlc01vZGFsKTtcbiAgICBjb25zdCBtc2dMaXN0ID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcihPcmRlclZpZXdQYWdlTWFwLmFsbE1lc3NhZ2VzTGlzdCk7XG5cbiAgICBjb25zdCBjbGFzc0NoZWNrSW50ZXJ2YWwgPSB3aW5kb3cuc2V0SW50ZXJ2YWwoKCkgPT4ge1xuICAgICAgaWYgKCRtc2dNb2RhbC5oYXNDbGFzcygnc2hvdycpKSB7XG4gICAgICAgIG1zZ0xpc3Quc2Nyb2xsVG9wID0gbXNnTGlzdC5zY3JvbGxIZWlnaHQ7XG4gICAgICAgIGNsZWFySW50ZXJ2YWwoY2xhc3NDaGVja0ludGVydmFsKTtcbiAgICAgIH1cbiAgICB9LCAxMCk7XG5cblxuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci9tZXNzYWdlL29yZGVyLXZpZXctcGFnZS1tZXNzYWdlcy1oYW5kbGVyLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cbmltcG9ydCBPcmRlclZpZXdQYWdlTWFwIGZyb20gJy4vT3JkZXJWaWV3UGFnZU1hcCc7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuZXhwb3J0IGRlZmF1bHQgY2xhc3MgT3JkZXJTaGlwcGluZ01hbmFnZXIge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLl9pbml0T3JkZXJTaGlwcGluZ1VwZGF0ZUV2ZW50SGFuZGxlcigpO1xuICB9XG5cbiAgX2luaXRPcmRlclNoaXBwaW5nVXBkYXRlRXZlbnRIYW5kbGVyKCkge1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5zaG93T3JkZXJTaGlwcGluZ1VwZGF0ZU1vZGFsQnRuKS5vbignY2xpY2snLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0ICRidG4gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuXG4gICAgICAkKE9yZGVyVmlld1BhZ2VNYXAudXBkYXRlT3JkZXJTaGlwcGluZ1RyYWNraW5nTnVtYmVySW5wdXQpLnZhbCgkYnRuLmRhdGEoJ29yZGVyLXRyYWNraW5nLW51bWJlcicpKTtcbiAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC51cGRhdGVPcmRlclNoaXBwaW5nQ3VycmVudE9yZGVyQ2FycmllcklkSW5wdXQpLnZhbCgkYnRuLmRhdGEoJ29yZGVyLWNhcnJpZXItaWQnKSk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL29yZGVyLXNoaXBwaW5nLW1hbmFnZXIuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgT3JkZXJWaWV3UGFnZU1hcCBmcm9tICcuL09yZGVyVmlld1BhZ2VNYXAnO1xuaW1wb3J0IE9yZGVyU2hpcHBpbmdNYW5hZ2VyIGZyb20gJy4vb3JkZXItc2hpcHBpbmctbWFuYWdlcic7XG5pbXBvcnQgSW52b2ljZU5vdGVNYW5hZ2VyIGZyb20gJy4vaW52b2ljZS1ub3RlLW1hbmFnZXInO1xuaW1wb3J0IE9yZGVyVmlld1BhZ2VNZXNzYWdlc0hhbmRsZXIgZnJvbSAnLi9tZXNzYWdlL29yZGVyLXZpZXctcGFnZS1tZXNzYWdlcy1oYW5kbGVyJztcbmltcG9ydCBUZXh0V2l0aExlbmd0aENvdW50ZXIgZnJvbSBcIi4uLy4uL2NvbXBvbmVudHMvZm9ybS90ZXh0LXdpdGgtbGVuZ3RoLWNvdW50ZXJcIlxuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbiQoKCkgPT4ge1xuICBjb25zdCBESVNDT1VOVF9UWVBFX0FNT1VOVCA9ICdhbW91bnQnO1xuICBjb25zdCBESVNDT1VOVF9UWVBFX1BFUkNFTlQgPSAncGVyY2VudCc7XG4gIGNvbnN0IERJU0NPVU5UX1RZUEVfRlJFRV9TSElQUElORyA9ICdmcmVlX3NoaXBwaW5nJztcblxuICBuZXcgT3JkZXJTaGlwcGluZ01hbmFnZXIoKTtcbiAgbmV3IFRleHRXaXRoTGVuZ3RoQ291bnRlcigpO1xuXG4gIGhhbmRsZVBheW1lbnREZXRhaWxzVG9nZ2xlKCk7XG4gIGhhbmRsZVByaXZhdGVOb3RlQ2hhbmdlKCk7XG4gIGhhbmRsZVVwZGF0ZU9yZGVyU3RhdHVzQnV0dG9uKCk7XG5cbiAgbmV3IEludm9pY2VOb3RlTWFuYWdlcigpO1xuICBjb25zdCBvcmRlclZpZXdQYWdlTWVzc2FnZUhhbmRsZXIgPSBuZXcgT3JkZXJWaWV3UGFnZU1lc3NhZ2VzSGFuZGxlcigpO1xuICBvcmRlclZpZXdQYWdlTWVzc2FnZUhhbmRsZXIubGlzdGVuRm9yUHJlZGVmaW5lZE1lc3NhZ2VTZWxlY3Rpb24oKTtcbiAgb3JkZXJWaWV3UGFnZU1lc3NhZ2VIYW5kbGVyLmxpc3RlbkZvckZ1bGxNZXNzYWdlc09wZW4oKTtcbiAgJChPcmRlclZpZXdQYWdlTWFwLnByaXZhdGVOb3RlVG9nZ2xlQnRuKS5vbignY2xpY2snLCAoZXZlbnQpID0+IHtcbiAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgIHRvZ2dsZVByaXZhdGVOb3RlQmxvY2soKTtcbiAgfSk7XG5cbiAgaW5pdEFkZENhcnRSdWxlRm9ybUhhbmRsZXIoKTtcbiAgaW5pdEFkZFByb2R1Y3RGb3JtSGFuZGxlcigpO1xuICBpbml0Q2hhbmdlQWRkcmVzc0Zvcm1IYW5kbGVyKCk7XG4gIGluaXRIb29rVGFicygpO1xuXG4gIGZ1bmN0aW9uIGluaXRIb29rVGFicygpIHtcbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAub3JkZXJIb29rVGFic0NvbnRhaW5lcikuZmluZCgnLm5hdi10YWJzIGxpOmZpcnN0LWNoaWxkIGEnKS50YWIoJ3Nob3cnKTtcbiAgfVxuXG4gIGZ1bmN0aW9uIGhhbmRsZVBheW1lbnREZXRhaWxzVG9nZ2xlKCkge1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5vcmRlclBheW1lbnREZXRhaWxzQnRuKS5vbignY2xpY2snLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0ICRwYXltZW50RGV0YWlsUm93ID0gJChldmVudC5jdXJyZW50VGFyZ2V0KS5jbG9zZXN0KCd0cicpLm5leHQoJzpmaXJzdCcpO1xuXG4gICAgICAkcGF5bWVudERldGFpbFJvdy50b2dnbGVDbGFzcygnZC1ub25lJyk7XG4gICAgfSk7XG4gIH1cblxuICBmdW5jdGlvbiB0b2dnbGVQcml2YXRlTm90ZUJsb2NrKCkge1xuICAgIGNvbnN0ICRibG9jayA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcml2YXRlTm90ZUJsb2NrKTtcbiAgICBjb25zdCAkYnRuID0gJChPcmRlclZpZXdQYWdlTWFwLnByaXZhdGVOb3RlVG9nZ2xlQnRuKTtcbiAgICBjb25zdCBpc1ByaXZhdGVOb3RlT3BlbmVkID0gJGJ0bi5oYXNDbGFzcygnaXMtb3BlbmVkJyk7XG5cbiAgICBpZiAoaXNQcml2YXRlTm90ZU9wZW5lZCkge1xuICAgICAgJGJ0bi5yZW1vdmVDbGFzcygnaXMtb3BlbmVkJyk7XG4gICAgICAkYmxvY2suYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICAgIH0gZWxzZSB7XG4gICAgICAkYnRuLmFkZENsYXNzKCdpcy1vcGVuZWQnKTtcbiAgICAgICRibG9jay5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgfVxuXG4gICAgY29uc3QgJGljb24gPSAkYnRuLmZpbmQoJy5tYXRlcmlhbC1pY29ucycpO1xuICAgICRpY29uLnRleHQoaXNQcml2YXRlTm90ZU9wZW5lZCA/ICdhZGQnIDogJ3JlbW92ZScpO1xuICB9XG5cbiAgZnVuY3Rpb24gaGFuZGxlUHJpdmF0ZU5vdGVDaGFuZ2UoKSB7XG4gICAgY29uc3QgJHN1Ym1pdEJ0biA9ICQoT3JkZXJWaWV3UGFnZU1hcC5wcml2YXRlTm90ZVN1Ym1pdEJ0bik7XG5cbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAucHJpdmF0ZU5vdGVJbnB1dCkub24oJ2lucHV0JywgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCBub3RlID0gJChldmVudC5jdXJyZW50VGFyZ2V0KS52YWwoKTtcbiAgICAgICRzdWJtaXRCdG4ucHJvcCgnZGlzYWJsZWQnLCAhbm90ZSk7XG4gICAgfSk7XG4gIH1cblxuICBmdW5jdGlvbiBpbml0QWRkUHJvZHVjdEZvcm1IYW5kbGVyKCkge1xuICAgIGNvbnN0ICRtb2RhbCA9ICQoT3JkZXJWaWV3UGFnZU1hcC51cGRhdGVPcmRlclByb2R1Y3RNb2RhbCk7XG5cbiAgICAkbW9kYWwub24oJ2NsaWNrJywgJy5qcy1vcmRlci1wcm9kdWN0LXVwZGF0ZS1idG4nLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0ICRidG4gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuXG4gICAgICAkbW9kYWwuZmluZCgnLmpzLXVwZGF0ZS1wcm9kdWN0LW5hbWUnKS50ZXh0KCRidG4uZGF0YSgncHJvZHVjdC1uYW1lJykpO1xuICAgICAgJG1vZGFsLmZpbmQoT3JkZXJWaWV3UGFnZU1hcC51cGRhdGVPcmRlclByb2R1Y3RQcmljZVRheEV4Y2xJbnB1dCkudmFsKCRidG4uZGF0YSgncHJvZHVjdC1wcmljZS10YXgtZXhjbCcpKTtcbiAgICAgICRtb2RhbC5maW5kKE9yZGVyVmlld1BhZ2VNYXAudXBkYXRlT3JkZXJQcm9kdWN0UHJpY2VUYXhJbmNsSW5wdXQpLnZhbCgkYnRuLmRhdGEoJ3Byb2R1Y3QtcHJpY2UtdGF4LWluY2wnKSk7XG4gICAgICAkbW9kYWwuZmluZChPcmRlclZpZXdQYWdlTWFwLnVwZGF0ZU9yZGVyUHJvZHVjdFF1YW50aXR5SW5wdXQpLnZhbCgkYnRuLmRhdGEoJ3Byb2R1Y3QtcXVhbnRpdHknKSk7XG4gICAgICAkbW9kYWwuZmluZCgnZm9ybScpLmF0dHIoJ2FjdGlvbicsICRidG4uZGF0YSgndXBkYXRlLXVybCcpKTtcbiAgICB9KTtcbiAgfVxuXG4gIGZ1bmN0aW9uIGluaXRBZGRDYXJ0UnVsZUZvcm1IYW5kbGVyKCkge1xuICAgIGNvbnN0ICRtb2RhbCA9ICQoT3JkZXJWaWV3UGFnZU1hcC5hZGRDYXJ0UnVsZU1vZGFsKTtcbiAgICBjb25zdCAkZm9ybSA9ICRtb2RhbC5maW5kKCdmb3JtJyk7XG4gICAgY29uc3QgJHZhbHVlSGVscCA9ICRtb2RhbC5maW5kKE9yZGVyVmlld1BhZ2VNYXAuY2FydFJ1bGVIZWxwVGV4dCk7XG4gICAgY29uc3QgJGludm9pY2VTZWxlY3QgPSAkbW9kYWwuZmluZChPcmRlclZpZXdQYWdlTWFwLmFkZENhcnRSdWxlSW52b2ljZUlkU2VsZWN0KTtcbiAgICBjb25zdCAkdmFsdWVJbnB1dCA9ICRmb3JtLmZpbmQoT3JkZXJWaWV3UGFnZU1hcC5hZGRDYXJ0UnVsZVZhbHVlSW5wdXQpO1xuICAgIGNvbnN0ICR2YWx1ZUZvcm1Hcm91cCA9ICR2YWx1ZUlucHV0LmNsb3Nlc3QoJy5mb3JtLWdyb3VwJyk7XG5cbiAgICAkZm9ybS5maW5kKE9yZGVyVmlld1BhZ2VNYXAuYWRkQ2FydFJ1bGVBcHBseU9uQWxsSW52b2ljZXNDaGVja2JveCkub24oJ2NoYW5nZScsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgaXNDaGVja2VkID0gJChldmVudC5jdXJyZW50VGFyZ2V0KS5pcygnOmNoZWNrZWQnKTtcblxuICAgICAgJGludm9pY2VTZWxlY3QuYXR0cignZGlzYWJsZWQnLCBpc0NoZWNrZWQpO1xuICAgIH0pO1xuXG4gICAgJGZvcm0uZmluZChPcmRlclZpZXdQYWdlTWFwLmFkZENhcnRSdWxlVHlwZVNlbGVjdCkub24oJ2NoYW5nZScsIChldmVudCkgPT4ge1xuICAgICAgY29uc3Qgc2VsZWN0ZWRDYXJ0UnVsZVR5cGUgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLnZhbCgpO1xuXG4gICAgICBpZiAoc2VsZWN0ZWRDYXJ0UnVsZVR5cGUgPT09IERJU0NPVU5UX1RZUEVfQU1PVU5UKSB7XG4gICAgICAgICR2YWx1ZUhlbHAucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgJHZhbHVlSGVscC5hZGRDbGFzcygnZC1ub25lJyk7XG4gICAgICB9XG5cbiAgICAgIGlmIChzZWxlY3RlZENhcnRSdWxlVHlwZSA9PT0gRElTQ09VTlRfVFlQRV9GUkVFX1NISVBQSU5HKSB7XG4gICAgICAgICR2YWx1ZUZvcm1Hcm91cC5hZGRDbGFzcygnZC1ub25lJyk7XG4gICAgICAgICR2YWx1ZUlucHV0LmF0dHIoJ2Rpc2FibGVkJywgdHJ1ZSk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICAkdmFsdWVGb3JtR3JvdXAucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICAgICAgICAkdmFsdWVJbnB1dC5hdHRyKCdkaXNhYmxlZCcsIGZhbHNlKTtcbiAgICAgIH1cbiAgICB9KTtcbiAgfVxuXG4gIGZ1bmN0aW9uIGhhbmRsZVVwZGF0ZU9yZGVyU3RhdHVzQnV0dG9uKCkge1xuICAgIGNvbnN0ICRidG4gPSAkKE9yZGVyVmlld1BhZ2VNYXAudXBkYXRlT3JkZXJTdGF0dXNBY3Rpb25CdG4pO1xuXG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnVwZGF0ZU9yZGVyU3RhdHVzQWN0aW9uSW5wdXQpLm9uKCdjaGFuZ2UnLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0IHNlbGVjdGVkT3JkZXJTdGF0dXNJZCA9ICQoZXZlbnQuY3VycmVudFRhcmdldCkudmFsKCk7XG5cbiAgICAgICRidG4ucHJvcCgnZGlzYWJsZWQnLCBwYXJzZUludChzZWxlY3RlZE9yZGVyU3RhdHVzSWQsIDEwKSA9PT0gJGJ0bi5kYXRhKCdvcmRlci1zdGF0dXMtaWQnKSk7XG4gICAgfSk7XG4gIH1cblxuICBmdW5jdGlvbiBpbml0Q2hhbmdlQWRkcmVzc0Zvcm1IYW5kbGVyKCkge1xuICAgIGNvbnN0ICRtb2RhbCA9ICQoT3JkZXJWaWV3UGFnZU1hcC51cGRhdGVDdXN0b21lckFkZHJlc3NNb2RhbCk7XG5cbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAub3Blbk9yZGVyQWRkcmVzc1VwZGF0ZU1vZGFsQnRuKS5vbignY2xpY2snLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0ICRidG4gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgICAgJG1vZGFsLmZpbmQoT3JkZXJWaWV3UGFnZU1hcC51cGRhdGVPcmRlckFkZHJlc3NUeXBlSW5wdXQpLnZhbCgkYnRuLmRhdGEoJ2FkZHJlc3MtdHlwZScpKTtcbiAgICB9KTtcbiAgfVxuXG4gICQoJ2EucGFydGlhbC1yZWZ1bmQsIGEucGFydGlhbF9yZWZ1bmRfY2FuY2VsJykub24oJ2NsaWNrJywgZnVuY3Rpb24oZSkge1xuICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAkKCd0ZC5wcm9kdWN0X2FjdGlvbnMsIHRoLnByb2R1Y3RfYWN0aW9ucywgLnBhcnRpYWxfcmVmdW5kLCAuc2hpcHBpbmctcHJpY2UnKS50b2dnbGUoKTtcbiAgfSk7XG59KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL3ZpZXcuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5leHBvcnQgZGVmYXVsdCB7XG4gIG9yZGVyUGF5bWVudERldGFpbHNCdG46ICcuanMtcGF5bWVudC1kZXRhaWxzLWJ0bicsXG4gIG9yZGVyUGF5bWVudEZvcm1BbW91bnRJbnB1dDogJyNvcmRlcl9wYXltZW50X2Ftb3VudCcsXG4gIHZpZXdPcmRlclBheW1lbnRzQmxvY2s6ICcjdmlld19vcmRlcl9wYXltZW50c19ibG9jaycsXG4gIHByaXZhdGVOb3RlVG9nZ2xlQnRuOiAnLmpzLXByaXZhdGUtbm90ZS10b2dnbGUtYnRuJyxcbiAgcHJpdmF0ZU5vdGVCbG9jazogJy5qcy1wcml2YXRlLW5vdGUtYmxvY2snLFxuICBwcml2YXRlTm90ZUlucHV0OiAnI3ByaXZhdGVfbm90ZV9ub3RlJyxcbiAgcHJpdmF0ZU5vdGVTdWJtaXRCdG46ICcuanMtcHJpdmF0ZS1ub3RlLWJ0bicsXG4gIHVwZGF0ZU9yZGVyUHJvZHVjdE1vZGFsOiAnI3VwZGF0ZU9yZGVyUHJvZHVjdE1vZGFsJyxcbiAgdXBkYXRlT3JkZXJQcm9kdWN0UHJpY2VUYXhFeGNsSW5wdXQ6ICcjdXBkYXRlX29yZGVyX3Byb2R1Y3RfcHJpY2VfdGF4X2V4Y2wnLFxuICB1cGRhdGVPcmRlclByb2R1Y3RQcmljZVRheEluY2xJbnB1dDogJyN1cGRhdGVfb3JkZXJfcHJvZHVjdF9wcmljZV90YXhfaW5jbCcsXG4gIHVwZGF0ZU9yZGVyUHJvZHVjdFF1YW50aXR5SW5wdXQ6ICcjdXBkYXRlX29yZGVyX3Byb2R1Y3RfcXVhbnRpdHknLFxuICBhZGRDYXJ0UnVsZU1vZGFsOiAnI2FkZE9yZGVyRGlzY291bnRNb2RhbCcsXG4gIGFkZENhcnRSdWxlQXBwbHlPbkFsbEludm9pY2VzQ2hlY2tib3g6ICcjYWRkX29yZGVyX2NhcnRfcnVsZV9hcHBseV9vbl9hbGxfaW52b2ljZXMnLFxuICBhZGRDYXJ0UnVsZUludm9pY2VJZFNlbGVjdDogJyNhZGRfb3JkZXJfY2FydF9ydWxlX2ludm9pY2VfaWQnLFxuICBhZGRDYXJ0UnVsZVR5cGVTZWxlY3Q6ICcjYWRkX29yZGVyX2NhcnRfcnVsZV90eXBlJyxcbiAgYWRkQ2FydFJ1bGVWYWx1ZUlucHV0OiAnI2FkZF9vcmRlcl9jYXJ0X3J1bGVfdmFsdWUnLFxuICBjYXJ0UnVsZUhlbHBUZXh0OiAnLmpzLWNhcnQtcnVsZS12YWx1ZS1oZWxwJyxcbiAgdXBkYXRlT3JkZXJTdGF0dXNBY3Rpb25CdG46ICcjdXBkYXRlX29yZGVyX3N0YXR1c19hY3Rpb25fYnRuJyxcbiAgdXBkYXRlT3JkZXJTdGF0dXNBY3Rpb25JbnB1dDogJyN1cGRhdGVfb3JkZXJfc3RhdHVzX2FjdGlvbl9pbnB1dCcsXG4gIHVwZGF0ZU9yZGVyU3RhdHVzQWN0aW9uRm9ybTogJyN1cGRhdGVfb3JkZXJfc3RhdHVzX2FjdGlvbl9mb3JtJyxcbiAgc2hvd09yZGVyU2hpcHBpbmdVcGRhdGVNb2RhbEJ0bjogJy5qcy11cGRhdGUtc2hpcHBpbmctYnRuJyxcbiAgdXBkYXRlT3JkZXJTaGlwcGluZ1RyYWNraW5nTnVtYmVySW5wdXQ6ICcjdXBkYXRlX29yZGVyX3NoaXBwaW5nX3RyYWNraW5nX251bWJlcicsXG4gIHVwZGF0ZU9yZGVyU2hpcHBpbmdDdXJyZW50T3JkZXJDYXJyaWVySWRJbnB1dDogJyN1cGRhdGVfb3JkZXJfc2hpcHBpbmdfY3VycmVudF9vcmRlcl9jYXJyaWVyX2lkJyxcbiAgdXBkYXRlQ3VzdG9tZXJBZGRyZXNzTW9kYWw6ICcjdXBkYXRlQ3VzdG9tZXJBZGRyZXNzTW9kYWwnLFxuICBvcGVuT3JkZXJBZGRyZXNzVXBkYXRlTW9kYWxCdG46ICcuanMtdXBkYXRlLWN1c3RvbWVyLWFkZHJlc3MtbW9kYWwtYnRuJyxcbiAgdXBkYXRlT3JkZXJBZGRyZXNzVHlwZUlucHV0OiAnI2NoYW5nZV9vcmRlcl9hZGRyZXNzX2FkZHJlc3NfdHlwZScsXG4gIG9yZGVyTWVzc2FnZU5hbWVTZWxlY3Q6ICcjb3JkZXJfbWVzc2FnZV9vcmRlcl9tZXNzYWdlJyxcbiAgb3JkZXJNZXNzYWdlc0NvbnRhaW5lcjogJy5qcy1vcmRlci1tZXNzYWdlcy1jb250YWluZXInLFxuICBvcmRlck1lc3NhZ2U6ICcjb3JkZXJfbWVzc2FnZV9tZXNzYWdlJyxcbiAgb3JkZXJNZXNzYWdlQ2hhbmdlV2FybmluZzogJy5qcy1tZXNzYWdlLWNoYW5nZS13YXJuaW5nJyxcbiAgYWxsTWVzc2FnZXNNb2RhbDogJyN2aWV3X2FsbF9tZXNzYWdlc19tb2RhbCcsXG4gIGFsbE1lc3NhZ2VzTGlzdDogJyNhbGwtbWVzc2FnZXMtbGlzdCcsXG4gIG9wZW5BbGxNZXNzYWdlc0J0bjogJy5qcy1vcGVuLWFsbC1tZXNzYWdlcy1idG4nLFxuICBvcmRlckhvb2tUYWJzQ29udGFpbmVyOiAnI29yZGVyX2hvb2tfdGFicycsXG59O1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvT3JkZXJWaWV3UGFnZU1hcC5qcyJdLCJzb3VyY2VSb290IjoiIn0=