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

  $(_OrderViewPageMap2.default.displayPartialRefundBtn + ', ' + _OrderViewPageMap2.default.cancelPartialRefundBtn).on('click', function (event) {
    event.preventDefault();
    $('td.product_actions, th.product_actions, .partial-refund:not(.hidden), .shipping-price, .refund-checkboxes-container').toggle();
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
  orderHookTabsContainer: '#order_hook_tabs',
  displayPartialRefundBtn: 'button.partial-refund-display',
  cancelPartialRefundBtn: 'button.partial-refund-cancel'
};

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgMDcyNjY3NmRkYmQzMWE2MTBlNDAiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9mb3JtL3RleHQtd2l0aC1sZW5ndGgtY291bnRlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci9pbnZvaWNlLW5vdGUtbWFuYWdlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci9tZXNzYWdlL29yZGVyLXZpZXctcGFnZS1tZXNzYWdlcy1oYW5kbGVyLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL29yZGVyL29yZGVyLXNoaXBwaW5nLW1hbmFnZXIuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvb3JkZXIvdmlldy5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9vcmRlci9PcmRlclZpZXdQYWdlTWFwLmpzIl0sIm5hbWVzIjpbIiQiLCJ3aW5kb3ciLCJUZXh0V2l0aExlbmd0aENvdW50ZXIiLCJ3cmFwcGVyU2VsZWN0b3IiLCJ0ZXh0U2VsZWN0b3IiLCJpbnB1dFNlbGVjdG9yIiwiZG9jdW1lbnQiLCJvbiIsImUiLCIkaW5wdXQiLCJjdXJyZW50VGFyZ2V0IiwicmVtYWluaW5nTGVuZ3RoIiwiZGF0YSIsInZhbCIsImxlbmd0aCIsImNsb3Nlc3QiLCJmaW5kIiwidGV4dCIsIkludm9pY2VOb3RlTWFuYWdlciIsIl9pbml0U2hvd05vdGVGb3JtRXZlbnRIYW5kbGVyIiwiX2luaXRDbG9zZU5vdGVGb3JtRXZlbnRIYW5kbGVyIiwiX2luaXRFbnRlclBheW1lbnRFdmVudEhhbmRsZXIiLCJldmVudCIsInByZXZlbnREZWZhdWx0IiwiJGJ0biIsIiRub3RlUm93Iiwic2libGluZ3MiLCJyZW1vdmVDbGFzcyIsImFkZENsYXNzIiwicGF5bWVudEFtb3VudCIsIk9yZGVyVmlld1BhZ2VNYXAiLCJ2aWV3T3JkZXJQYXltZW50c0Jsb2NrIiwiZ2V0Iiwic2Nyb2xsSW50b1ZpZXciLCJiZWhhdmlvciIsIm9yZGVyUGF5bWVudEZvcm1BbW91bnRJbnB1dCIsIk9yZGVyVmlld1BhZ2VNZXNzYWdlc0hhbmRsZXIiLCIkb3JkZXJNZXNzYWdlQ2hhbmdlV2FybmluZyIsIm9yZGVyTWVzc2FnZUNoYW5nZVdhcm5pbmciLCIkbWVzc2FnZXNDb250YWluZXIiLCJvcmRlck1lc3NhZ2VzQ29udGFpbmVyIiwibGlzdGVuRm9yUHJlZGVmaW5lZE1lc3NhZ2VTZWxlY3Rpb24iLCJfaGFuZGxlUHJlZGVmaW5lZE1lc3NhZ2VTZWxlY3Rpb24iLCJsaXN0ZW5Gb3JGdWxsTWVzc2FnZXNPcGVuIiwiX29uRnVsbE1lc3NhZ2VzT3BlbiIsIm9yZGVyTWVzc2FnZU5hbWVTZWxlY3QiLCIkY3VycmVudEl0ZW0iLCJ2YWx1ZUlkIiwibWVzc2FnZSIsInRyaW0iLCIkb3JkZXJNZXNzYWdlIiwib3JkZXJNZXNzYWdlIiwiaXNTYW1lTWVzc2FnZSIsImNvbmZpcm0iLCJvcGVuQWxsTWVzc2FnZXNCdG4iLCJfc2Nyb2xsVG9Nc2dMaXN0Qm90dG9tIiwiJG1zZ01vZGFsIiwiYWxsTWVzc2FnZXNNb2RhbCIsIm1zZ0xpc3QiLCJxdWVyeVNlbGVjdG9yIiwiYWxsTWVzc2FnZXNMaXN0IiwiY2xhc3NDaGVja0ludGVydmFsIiwic2V0SW50ZXJ2YWwiLCJoYXNDbGFzcyIsInNjcm9sbFRvcCIsInNjcm9sbEhlaWdodCIsImNsZWFySW50ZXJ2YWwiLCJPcmRlclNoaXBwaW5nTWFuYWdlciIsIl9pbml0T3JkZXJTaGlwcGluZ1VwZGF0ZUV2ZW50SGFuZGxlciIsInNob3dPcmRlclNoaXBwaW5nVXBkYXRlTW9kYWxCdG4iLCJ1cGRhdGVPcmRlclNoaXBwaW5nVHJhY2tpbmdOdW1iZXJJbnB1dCIsInVwZGF0ZU9yZGVyU2hpcHBpbmdDdXJyZW50T3JkZXJDYXJyaWVySWRJbnB1dCIsIkRJU0NPVU5UX1RZUEVfQU1PVU5UIiwiRElTQ09VTlRfVFlQRV9QRVJDRU5UIiwiRElTQ09VTlRfVFlQRV9GUkVFX1NISVBQSU5HIiwiaGFuZGxlUGF5bWVudERldGFpbHNUb2dnbGUiLCJoYW5kbGVQcml2YXRlTm90ZUNoYW5nZSIsImhhbmRsZVVwZGF0ZU9yZGVyU3RhdHVzQnV0dG9uIiwib3JkZXJWaWV3UGFnZU1lc3NhZ2VIYW5kbGVyIiwicHJpdmF0ZU5vdGVUb2dnbGVCdG4iLCJ0b2dnbGVQcml2YXRlTm90ZUJsb2NrIiwiaW5pdEFkZENhcnRSdWxlRm9ybUhhbmRsZXIiLCJpbml0QWRkUHJvZHVjdEZvcm1IYW5kbGVyIiwiaW5pdENoYW5nZUFkZHJlc3NGb3JtSGFuZGxlciIsImluaXRIb29rVGFicyIsIm9yZGVySG9va1RhYnNDb250YWluZXIiLCJ0YWIiLCJvcmRlclBheW1lbnREZXRhaWxzQnRuIiwiJHBheW1lbnREZXRhaWxSb3ciLCJuZXh0IiwidG9nZ2xlQ2xhc3MiLCIkYmxvY2siLCJwcml2YXRlTm90ZUJsb2NrIiwiaXNQcml2YXRlTm90ZU9wZW5lZCIsIiRpY29uIiwiJHN1Ym1pdEJ0biIsInByaXZhdGVOb3RlU3VibWl0QnRuIiwicHJpdmF0ZU5vdGVJbnB1dCIsIm5vdGUiLCJwcm9wIiwiJG1vZGFsIiwidXBkYXRlT3JkZXJQcm9kdWN0TW9kYWwiLCJ1cGRhdGVPcmRlclByb2R1Y3RQcmljZVRheEV4Y2xJbnB1dCIsInVwZGF0ZU9yZGVyUHJvZHVjdFByaWNlVGF4SW5jbElucHV0IiwidXBkYXRlT3JkZXJQcm9kdWN0UXVhbnRpdHlJbnB1dCIsImF0dHIiLCJhZGRDYXJ0UnVsZU1vZGFsIiwiJGZvcm0iLCIkdmFsdWVIZWxwIiwiY2FydFJ1bGVIZWxwVGV4dCIsIiRpbnZvaWNlU2VsZWN0IiwiYWRkQ2FydFJ1bGVJbnZvaWNlSWRTZWxlY3QiLCIkdmFsdWVJbnB1dCIsImFkZENhcnRSdWxlVmFsdWVJbnB1dCIsIiR2YWx1ZUZvcm1Hcm91cCIsImFkZENhcnRSdWxlQXBwbHlPbkFsbEludm9pY2VzQ2hlY2tib3giLCJpc0NoZWNrZWQiLCJpcyIsImFkZENhcnRSdWxlVHlwZVNlbGVjdCIsInNlbGVjdGVkQ2FydFJ1bGVUeXBlIiwidXBkYXRlT3JkZXJTdGF0dXNBY3Rpb25CdG4iLCJ1cGRhdGVPcmRlclN0YXR1c0FjdGlvbklucHV0Iiwic2VsZWN0ZWRPcmRlclN0YXR1c0lkIiwicGFyc2VJbnQiLCJ1cGRhdGVDdXN0b21lckFkZHJlc3NNb2RhbCIsIm9wZW5PcmRlckFkZHJlc3NVcGRhdGVNb2RhbEJ0biIsInVwZGF0ZU9yZGVyQWRkcmVzc1R5cGVJbnB1dCIsImRpc3BsYXlQYXJ0aWFsUmVmdW5kQnRuIiwiY2FuY2VsUGFydGlhbFJlZnVuZEJ0biIsInRvZ2dsZSIsInVwZGF0ZU9yZGVyU3RhdHVzQWN0aW9uRm9ybSJdLCJtYXBwaW5ncyI6Ijs7QUFBQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7O0FBR0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0EsbURBQTJDLGNBQWM7O0FBRXpEO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsYUFBSztBQUNMO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsbUNBQTJCLDBCQUEwQixFQUFFO0FBQ3ZELHlDQUFpQyxlQUFlO0FBQ2hEO0FBQ0E7QUFDQTs7QUFFQTtBQUNBLDhEQUFzRCwrREFBK0Q7O0FBRXJIO0FBQ0E7O0FBRUE7QUFDQTs7Ozs7Ozs7Ozs7Ozs7Ozs7QUNoRUE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUF5QkEsSUFBTUEsSUFBSUMsT0FBT0QsQ0FBakI7O0FBRUE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7SUFrQnFCRSxxQixHQUNuQixpQ0FBYztBQUFBOztBQUFBOztBQUNaLE9BQUtDLGVBQUwsR0FBdUIsOEJBQXZCO0FBQ0EsT0FBS0MsWUFBTCxHQUFvQixvQkFBcEI7QUFDQSxPQUFLQyxhQUFMLEdBQXFCLHFCQUFyQjs7QUFFQUwsSUFBRU0sUUFBRixFQUFZQyxFQUFaLENBQWUsT0FBZixFQUEyQixLQUFLSixl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Ozs7Ozs7OztBQ2pDQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztrQkF5QmU7QUFDYjFDLDBCQUF3Qix5QkFEWDtBQUVicEQsK0JBQTZCLHVCQUZoQjtBQUdiSiwwQkFBd0IsNEJBSFg7QUFJYmdELHdCQUFzQiw2QkFKVDtBQUtiYSxvQkFBa0Isd0JBTEw7QUFNYkssb0JBQWtCLG9CQU5MO0FBT2JELHdCQUFzQixzQkFQVDtBQVFiSywyQkFBeUIsMEJBUlo7QUFTYkMsdUNBQXFDLHNDQVR4QjtBQVViQyx1Q0FBcUMsc0NBVnhCO0FBV2JDLG1DQUFpQyxnQ0FYcEI7QUFZYkUsb0JBQWtCLHdCQVpMO0FBYWJTLHlDQUF1Qyw0Q0FiMUI7QUFjYkosOEJBQTRCLGlDQWRmO0FBZWJPLHlCQUF1QiwyQkFmVjtBQWdCYkwseUJBQXVCLDRCQWhCVjtBQWlCYkosb0JBQWtCLDBCQWpCTDtBQWtCYlcsOEJBQTRCLGlDQWxCZjtBQW1CYkMsZ0NBQThCLG1DQW5CakI7QUFvQmJTLCtCQUE2QixrQ0FwQmhCO0FBcUJiN0QsbUNBQWlDLHlCQXJCcEI7QUFzQmJDLDBDQUF3Qyx3Q0F0QjNCO0FBdUJiQyxpREFBK0MsaURBdkJsQztBQXdCYnFELDhCQUE0Qiw2QkF4QmY7QUF5QmJDLGtDQUFnQyx1Q0F6Qm5CO0FBMEJiQywrQkFBNkIsb0NBMUJoQjtBQTJCYmpGLDBCQUF3Qiw4QkEzQlg7QUE0QmJMLDBCQUF3Qiw4QkE1Qlg7QUE2QmJXLGdCQUFjLHdCQTdCRDtBQThCYmIsNkJBQTJCLDRCQTlCZDtBQStCYm1CLG9CQUFrQiwwQkEvQkw7QUFnQ2JHLG1CQUFpQixvQkFoQ0o7QUFpQ2JOLHNCQUFvQiwyQkFqQ1A7QUFrQ2IrQiwwQkFBd0Isa0JBbENYO0FBbUNiMEMsMkJBQXlCLCtCQW5DWjtBQW9DYkMsMEJBQXdCO0FBcENYLEMiLCJmaWxlIjoib3JkZXJfdmlldy5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBpZGVudGl0eSBmdW5jdGlvbiBmb3IgY2FsbGluZyBoYXJtb255IGltcG9ydHMgd2l0aCB0aGUgY29ycmVjdCBjb250ZXh0XG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmkgPSBmdW5jdGlvbih2YWx1ZSkgeyByZXR1cm4gdmFsdWU7IH07XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDM4Nyk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gd2VicGFjay9ib290c3RyYXAgMDcyNjY3NmRkYmQzMWE2MTBlNDAiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogVGV4dFdpdGhMZW5ndGhDb3VudGVyIGhhbmRsZXMgaW5wdXQgd2l0aCBsZW5ndGggY291bnRlciBVSS5cbiAqXG4gKiBVc2FnZTpcbiAqXG4gKiBUaGVyZSBtdXN0IGJlIGFuIGVsZW1lbnQgdGhhdCB3cmFwcyBib3RoIGlucHV0ICYgY291bnRlciBkaXNwbGF5IHdpdGggXCIuanMtdGV4dC13aXRoLWxlbmd0aC1jb3VudGVyXCIgY2xhc3MuXG4gKiBDb3VudGVyIGRpc3BsYXkgbXVzdCBoYXZlIFwiLmpzLWNvdW50YWJsZS10ZXh0LWRpc3BsYXlcIiBjbGFzcyBhbmQgaW5wdXQgbXVzdCBoYXZlIFwiLmpzLWNvdW50YWJsZS10ZXh0LWlucHV0XCIgY2xhc3MuXG4gKiBUZXh0IGlucHV0IG11c3QgaGF2ZSBcImRhdGEtbWF4LWxlbmd0aFwiIGF0dHJpYnV0ZS5cbiAqXG4gKiA8ZGl2IGNsYXNzPVwianMtdGV4dC13aXRoLWxlbmd0aC1jb3VudGVyXCI+XG4gKiAgPHNwYW4gY2xhc3M9XCJqcy1jb3VudGFibGUtdGV4dFwiPjwvc3Bhbj5cbiAqICA8aW5wdXQgY2xhc3M9XCJqcy1jb3VudGFibGUtaW5wdXRcIiBkYXRhLW1heC1sZW5ndGg9XCIyNTVcIj5cbiAqIDwvZGl2PlxuICpcbiAqIEluIEphdmFzY3JpcHQgeW91IG11c3QgZW5hYmxlIHRoaXMgY29tcG9uZW50OlxuICpcbiAqIG5ldyBUZXh0V2l0aExlbmd0aENvdW50ZXIoKTtcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgVGV4dFdpdGhMZW5ndGhDb3VudGVyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy53cmFwcGVyU2VsZWN0b3IgPSAnLmpzLXRleHQtd2l0aC1sZW5ndGgtY291bnRlcic7XG4gICAgdGhpcy50ZXh0U2VsZWN0b3IgPSAnLmpzLWNvdW50YWJsZS10ZXh0JztcbiAgICB0aGlzLmlucHV0U2VsZWN0b3IgPSAnLmpzLWNvdW50YWJsZS1pbnB1dCc7XG5cbiAgICAkKGRvY3VtZW50KS5vbignaW5wdXQnLCBgJHt0aGlzLndyYXBwZXJTZWxlY3Rvcn0gJHt0aGlzLmlucHV0U2VsZWN0b3J9YCwgKGUpID0+IHtcbiAgICAgIGNvbnN0ICRpbnB1dCA9ICQoZS5jdXJyZW50VGFyZ2V0KTtcbiAgICAgIGNvbnN0IHJlbWFpbmluZ0xlbmd0aCA9ICRpbnB1dC5kYXRhKCdtYXgtbGVuZ3RoJykgLSAkaW5wdXQudmFsKCkubGVuZ3RoO1xuXG4gICAgICAkaW5wdXQuY2xvc2VzdCh0aGlzLndyYXBwZXJTZWxlY3RvcikuZmluZCh0aGlzLnRleHRTZWxlY3RvcikudGV4dChyZW1haW5pbmdMZW5ndGgpO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2Zvcm0vdGV4dC13aXRoLWxlbmd0aC1jb3VudGVyLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cbmltcG9ydCBPcmRlclZpZXdQYWdlTWFwIGZyb20gXCIuL09yZGVyVmlld1BhZ2VNYXBcIjtcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIE1hbmFnZXMgYWRkaW5nL2VkaXRpbmcgbm90ZSBmb3IgaW52b2ljZSBkb2N1bWVudHMuXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEludm9pY2VOb3RlTWFuYWdlciB7XG5cbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy5faW5pdFNob3dOb3RlRm9ybUV2ZW50SGFuZGxlcigpO1xuICAgIHRoaXMuX2luaXRDbG9zZU5vdGVGb3JtRXZlbnRIYW5kbGVyKCk7XG4gICAgdGhpcy5faW5pdEVudGVyUGF5bWVudEV2ZW50SGFuZGxlcigpO1xuXG4gICAgcmV0dXJuIHt9O1xuICB9XG5cbiAgX2luaXRTaG93Tm90ZUZvcm1FdmVudEhhbmRsZXIoKSB7XG4gICAgJCgnLmpzLW9wZW4taW52b2ljZS1ub3RlLWJ0bicpLm9uKCdjbGljaycsIChldmVudCkgPT4ge1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgY29uc3QgJGJ0biA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgICBjb25zdCAkbm90ZVJvdyA9ICRidG4uY2xvc2VzdCgndHInKS5zaWJsaW5ncygndHI6Zmlyc3QnKTtcblxuICAgICAgJG5vdGVSb3cucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICAgIH0pO1xuICB9XG5cbiAgX2luaXRDbG9zZU5vdGVGb3JtRXZlbnRIYW5kbGVyKCkge1xuICAgICQoJy5qcy1jYW5jZWwtaW52b2ljZS1ub3RlLWJ0bicpLm9uKCdjbGljaycsIChldmVudCkgPT4ge1xuICAgICAgJChldmVudC5jdXJyZW50VGFyZ2V0KS5jbG9zZXN0KCd0cicpLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgICB9KTtcbiAgfVxuXG4gIF9pbml0RW50ZXJQYXltZW50RXZlbnRIYW5kbGVyKCkge1xuICAgICQoJy5qcy1lbnRlci1wYXltZW50LWJ0bicpLm9uKCdjbGljaycsIChldmVudCkgPT4ge1xuXG4gICAgICBjb25zdCAkYnRuID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICAgIGxldCBwYXltZW50QW1vdW50ID0gJGJ0bi5kYXRhKCdwYXltZW50LWFtb3VudCcpO1xuXG4gICAgICAkKE9yZGVyVmlld1BhZ2VNYXAudmlld09yZGVyUGF5bWVudHNCbG9jaykuZ2V0KDApLnNjcm9sbEludG9WaWV3KHtiZWhhdmlvcjogXCJzbW9vdGhcIn0pO1xuICAgICAgJChPcmRlclZpZXdQYWdlTWFwLm9yZGVyUGF5bWVudEZvcm1BbW91bnRJbnB1dCkudmFsKHBheW1lbnRBbW91bnQpO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci9pbnZvaWNlLW5vdGUtbWFuYWdlci5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBPcmRlclZpZXdQYWdlTWFwIGZyb20gJy4uL09yZGVyVmlld1BhZ2VNYXAnO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQWxsIGFjdGlvbnMgZm9yIG9yZGVyIHZpZXcgcGFnZSBtZXNzYWdlcyBhcmUgcmVnaXN0ZXJlZCBpbiB0aGlzIGNsYXNzLlxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBPcmRlclZpZXdQYWdlTWVzc2FnZXNIYW5kbGVyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgdGhpcy4kb3JkZXJNZXNzYWdlQ2hhbmdlV2FybmluZyA9ICQoT3JkZXJWaWV3UGFnZU1hcC5vcmRlck1lc3NhZ2VDaGFuZ2VXYXJuaW5nKTtcbiAgICB0aGlzLiRtZXNzYWdlc0NvbnRhaW5lciA9ICQoT3JkZXJWaWV3UGFnZU1hcC5vcmRlck1lc3NhZ2VzQ29udGFpbmVyKTtcblxuICAgIHJldHVybiB7XG4gICAgICBsaXN0ZW5Gb3JQcmVkZWZpbmVkTWVzc2FnZVNlbGVjdGlvbjogKCkgPT4gdGhpcy5faGFuZGxlUHJlZGVmaW5lZE1lc3NhZ2VTZWxlY3Rpb24oKSxcbiAgICAgIGxpc3RlbkZvckZ1bGxNZXNzYWdlc09wZW46ICgpID0+IHRoaXMuX29uRnVsbE1lc3NhZ2VzT3BlbigpLFxuICAgIH07XG4gIH1cblxuICAvKipcbiAgICogSGFuZGxlcyBwcmVkZWZpbmVkIG9yZGVyIG1lc3NhZ2Ugc2VsZWN0aW9uLlxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hhbmRsZVByZWRlZmluZWRNZXNzYWdlU2VsZWN0aW9uKCkge1xuICAgICQoZG9jdW1lbnQpLm9uKCdjaGFuZ2UnLCBPcmRlclZpZXdQYWdlTWFwLm9yZGVyTWVzc2FnZU5hbWVTZWxlY3QsIChlKSA9PiB7XG4gICAgICBjb25zdCAkY3VycmVudEl0ZW0gPSAkKGUuY3VycmVudFRhcmdldCk7XG4gICAgICBjb25zdCB2YWx1ZUlkID0gJGN1cnJlbnRJdGVtLnZhbCgpO1xuXG4gICAgICBpZiAoIXZhbHVlSWQpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICAvLyBAdG9kbzogY2hlY2sgc2l6ZSBpZiBpcyBvdmVyIHRoZW4gbWF4IG5vdCBhbGxvdz9cbiAgICAgIGNvbnN0IG1lc3NhZ2UgPSB0aGlzLiRtZXNzYWdlc0NvbnRhaW5lci5maW5kKGBkaXZbZGF0YS1pZD0ke3ZhbHVlSWR9XWApLnRleHQoKS50cmltKCk7XG4gICAgICBjb25zdCAkb3JkZXJNZXNzYWdlID0gJChPcmRlclZpZXdQYWdlTWFwLm9yZGVyTWVzc2FnZSk7XG4gICAgICBjb25zdCBpc1NhbWVNZXNzYWdlID0gJG9yZGVyTWVzc2FnZS52YWwoKS50cmltKCkgPT09IG1lc3NhZ2U7XG5cbiAgICAgIGlmIChpc1NhbWVNZXNzYWdlKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cblxuICAgICAgaWYgKCRvcmRlck1lc3NhZ2UudmFsKCkgJiYgIWNvbmZpcm0odGhpcy4kb3JkZXJNZXNzYWdlQ2hhbmdlV2FybmluZy50ZXh0KCkpKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cblxuICAgICAgJG9yZGVyTWVzc2FnZS52YWwobWVzc2FnZSk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogTGlzdGVucyBmb3IgZXZlbnQgd2hlbiBhbGwgbWVzc2FnZXMgbW9kYWwgaXMgYmVpbmcgb3BlbmVkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfb25GdWxsTWVzc2FnZXNPcGVuKCkge1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIE9yZGVyVmlld1BhZ2VNYXAub3BlbkFsbE1lc3NhZ2VzQnRuLCAoKSA9PiB0aGlzLl9zY3JvbGxUb01zZ0xpc3RCb3R0b20oKSk7XG4gIH1cblxuICAvKipcbiAgICogU2Nyb2xscyBkb3duIHRvIHRoZSBib3R0b20gb2YgYWxsIG1lc3NhZ2VzIGxpc3RcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zY3JvbGxUb01zZ0xpc3RCb3R0b20oKSB7XG4gICAgY29uc3QgJG1zZ01vZGFsID0gJChPcmRlclZpZXdQYWdlTWFwLmFsbE1lc3NhZ2VzTW9kYWwpO1xuICAgIGNvbnN0IG1zZ0xpc3QgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKE9yZGVyVmlld1BhZ2VNYXAuYWxsTWVzc2FnZXNMaXN0KTtcblxuICAgIGNvbnN0IGNsYXNzQ2hlY2tJbnRlcnZhbCA9IHdpbmRvdy5zZXRJbnRlcnZhbCgoKSA9PiB7XG4gICAgICBpZiAoJG1zZ01vZGFsLmhhc0NsYXNzKCdzaG93JykpIHtcbiAgICAgICAgbXNnTGlzdC5zY3JvbGxUb3AgPSBtc2dMaXN0LnNjcm9sbEhlaWdodDtcbiAgICAgICAgY2xlYXJJbnRlcnZhbChjbGFzc0NoZWNrSW50ZXJ2YWwpO1xuICAgICAgfVxuICAgIH0sIDEwKTtcblxuXG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL21lc3NhZ2Uvb3JkZXItdmlldy1wYWdlLW1lc3NhZ2VzLWhhbmRsZXIuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuaW1wb3J0IE9yZGVyVmlld1BhZ2VNYXAgZnJvbSAnLi9PcmRlclZpZXdQYWdlTWFwJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBPcmRlclNoaXBwaW5nTWFuYWdlciB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHRoaXMuX2luaXRPcmRlclNoaXBwaW5nVXBkYXRlRXZlbnRIYW5kbGVyKCk7XG4gIH1cblxuICBfaW5pdE9yZGVyU2hpcHBpbmdVcGRhdGVFdmVudEhhbmRsZXIoKSB7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLnNob3dPcmRlclNoaXBwaW5nVXBkYXRlTW9kYWxCdG4pLm9uKCdjbGljaycsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgJGJ0biA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG5cbiAgICAgICQoT3JkZXJWaWV3UGFnZU1hcC51cGRhdGVPcmRlclNoaXBwaW5nVHJhY2tpbmdOdW1iZXJJbnB1dCkudmFsKCRidG4uZGF0YSgnb3JkZXItdHJhY2tpbmctbnVtYmVyJykpO1xuICAgICAgJChPcmRlclZpZXdQYWdlTWFwLnVwZGF0ZU9yZGVyU2hpcHBpbmdDdXJyZW50T3JkZXJDYXJyaWVySWRJbnB1dCkudmFsKCRidG4uZGF0YSgnb3JkZXItY2Fycmllci1pZCcpKTtcbiAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvb3JkZXIvb3JkZXItc2hpcHBpbmctbWFuYWdlci5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBPcmRlclZpZXdQYWdlTWFwIGZyb20gJy4vT3JkZXJWaWV3UGFnZU1hcCc7XG5pbXBvcnQgT3JkZXJTaGlwcGluZ01hbmFnZXIgZnJvbSAnLi9vcmRlci1zaGlwcGluZy1tYW5hZ2VyJztcbmltcG9ydCBJbnZvaWNlTm90ZU1hbmFnZXIgZnJvbSAnLi9pbnZvaWNlLW5vdGUtbWFuYWdlcic7XG5pbXBvcnQgT3JkZXJWaWV3UGFnZU1lc3NhZ2VzSGFuZGxlciBmcm9tICcuL21lc3NhZ2Uvb3JkZXItdmlldy1wYWdlLW1lc3NhZ2VzLWhhbmRsZXInO1xuaW1wb3J0IFRleHRXaXRoTGVuZ3RoQ291bnRlciBmcm9tIFwiLi4vLi4vY29tcG9uZW50cy9mb3JtL3RleHQtd2l0aC1sZW5ndGgtY291bnRlclwiXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuJCgoKSA9PiB7XG4gIGNvbnN0IERJU0NPVU5UX1RZUEVfQU1PVU5UID0gJ2Ftb3VudCc7XG4gIGNvbnN0IERJU0NPVU5UX1RZUEVfUEVSQ0VOVCA9ICdwZXJjZW50JztcbiAgY29uc3QgRElTQ09VTlRfVFlQRV9GUkVFX1NISVBQSU5HID0gJ2ZyZWVfc2hpcHBpbmcnO1xuXG4gIG5ldyBPcmRlclNoaXBwaW5nTWFuYWdlcigpO1xuICBuZXcgVGV4dFdpdGhMZW5ndGhDb3VudGVyKCk7XG5cbiAgaGFuZGxlUGF5bWVudERldGFpbHNUb2dnbGUoKTtcbiAgaGFuZGxlUHJpdmF0ZU5vdGVDaGFuZ2UoKTtcbiAgaGFuZGxlVXBkYXRlT3JkZXJTdGF0dXNCdXR0b24oKTtcblxuICBuZXcgSW52b2ljZU5vdGVNYW5hZ2VyKCk7XG4gIGNvbnN0IG9yZGVyVmlld1BhZ2VNZXNzYWdlSGFuZGxlciA9IG5ldyBPcmRlclZpZXdQYWdlTWVzc2FnZXNIYW5kbGVyKCk7XG4gIG9yZGVyVmlld1BhZ2VNZXNzYWdlSGFuZGxlci5saXN0ZW5Gb3JQcmVkZWZpbmVkTWVzc2FnZVNlbGVjdGlvbigpO1xuICBvcmRlclZpZXdQYWdlTWVzc2FnZUhhbmRsZXIubGlzdGVuRm9yRnVsbE1lc3NhZ2VzT3BlbigpO1xuICAkKE9yZGVyVmlld1BhZ2VNYXAucHJpdmF0ZU5vdGVUb2dnbGVCdG4pLm9uKCdjbGljaycsIChldmVudCkgPT4ge1xuICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgdG9nZ2xlUHJpdmF0ZU5vdGVCbG9jaygpO1xuICB9KTtcblxuICBpbml0QWRkQ2FydFJ1bGVGb3JtSGFuZGxlcigpO1xuICBpbml0QWRkUHJvZHVjdEZvcm1IYW5kbGVyKCk7XG4gIGluaXRDaGFuZ2VBZGRyZXNzRm9ybUhhbmRsZXIoKTtcbiAgaW5pdEhvb2tUYWJzKCk7XG5cbiAgZnVuY3Rpb24gaW5pdEhvb2tUYWJzKCkge1xuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5vcmRlckhvb2tUYWJzQ29udGFpbmVyKS5maW5kKCcubmF2LXRhYnMgbGk6Zmlyc3QtY2hpbGQgYScpLnRhYignc2hvdycpO1xuICB9XG5cbiAgZnVuY3Rpb24gaGFuZGxlUGF5bWVudERldGFpbHNUb2dnbGUoKSB7XG4gICAgJChPcmRlclZpZXdQYWdlTWFwLm9yZGVyUGF5bWVudERldGFpbHNCdG4pLm9uKCdjbGljaycsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgJHBheW1lbnREZXRhaWxSb3cgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmNsb3Nlc3QoJ3RyJykubmV4dCgnOmZpcnN0Jyk7XG5cbiAgICAgICRwYXltZW50RGV0YWlsUm93LnRvZ2dsZUNsYXNzKCdkLW5vbmUnKTtcbiAgICB9KTtcbiAgfVxuXG4gIGZ1bmN0aW9uIHRvZ2dsZVByaXZhdGVOb3RlQmxvY2soKSB7XG4gICAgY29uc3QgJGJsb2NrID0gJChPcmRlclZpZXdQYWdlTWFwLnByaXZhdGVOb3RlQmxvY2spO1xuICAgIGNvbnN0ICRidG4gPSAkKE9yZGVyVmlld1BhZ2VNYXAucHJpdmF0ZU5vdGVUb2dnbGVCdG4pO1xuICAgIGNvbnN0IGlzUHJpdmF0ZU5vdGVPcGVuZWQgPSAkYnRuLmhhc0NsYXNzKCdpcy1vcGVuZWQnKTtcblxuICAgIGlmIChpc1ByaXZhdGVOb3RlT3BlbmVkKSB7XG4gICAgICAkYnRuLnJlbW92ZUNsYXNzKCdpcy1vcGVuZWQnKTtcbiAgICAgICRibG9jay5hZGRDbGFzcygnZC1ub25lJyk7XG4gICAgfSBlbHNlIHtcbiAgICAgICRidG4uYWRkQ2xhc3MoJ2lzLW9wZW5lZCcpO1xuICAgICAgJGJsb2NrLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgICB9XG5cbiAgICBjb25zdCAkaWNvbiA9ICRidG4uZmluZCgnLm1hdGVyaWFsLWljb25zJyk7XG4gICAgJGljb24udGV4dChpc1ByaXZhdGVOb3RlT3BlbmVkID8gJ2FkZCcgOiAncmVtb3ZlJyk7XG4gIH1cblxuICBmdW5jdGlvbiBoYW5kbGVQcml2YXRlTm90ZUNoYW5nZSgpIHtcbiAgICBjb25zdCAkc3VibWl0QnRuID0gJChPcmRlclZpZXdQYWdlTWFwLnByaXZhdGVOb3RlU3VibWl0QnRuKTtcblxuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5wcml2YXRlTm90ZUlucHV0KS5vbignaW5wdXQnLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0IG5vdGUgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLnZhbCgpO1xuICAgICAgJHN1Ym1pdEJ0bi5wcm9wKCdkaXNhYmxlZCcsICFub3RlKTtcbiAgICB9KTtcbiAgfVxuXG4gIGZ1bmN0aW9uIGluaXRBZGRQcm9kdWN0Rm9ybUhhbmRsZXIoKSB7XG4gICAgY29uc3QgJG1vZGFsID0gJChPcmRlclZpZXdQYWdlTWFwLnVwZGF0ZU9yZGVyUHJvZHVjdE1vZGFsKTtcblxuICAgICRtb2RhbC5vbignY2xpY2snLCAnLmpzLW9yZGVyLXByb2R1Y3QtdXBkYXRlLWJ0bicsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgJGJ0biA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG5cbiAgICAgICRtb2RhbC5maW5kKCcuanMtdXBkYXRlLXByb2R1Y3QtbmFtZScpLnRleHQoJGJ0bi5kYXRhKCdwcm9kdWN0LW5hbWUnKSk7XG4gICAgICAkbW9kYWwuZmluZChPcmRlclZpZXdQYWdlTWFwLnVwZGF0ZU9yZGVyUHJvZHVjdFByaWNlVGF4RXhjbElucHV0KS52YWwoJGJ0bi5kYXRhKCdwcm9kdWN0LXByaWNlLXRheC1leGNsJykpO1xuICAgICAgJG1vZGFsLmZpbmQoT3JkZXJWaWV3UGFnZU1hcC51cGRhdGVPcmRlclByb2R1Y3RQcmljZVRheEluY2xJbnB1dCkudmFsKCRidG4uZGF0YSgncHJvZHVjdC1wcmljZS10YXgtaW5jbCcpKTtcbiAgICAgICRtb2RhbC5maW5kKE9yZGVyVmlld1BhZ2VNYXAudXBkYXRlT3JkZXJQcm9kdWN0UXVhbnRpdHlJbnB1dCkudmFsKCRidG4uZGF0YSgncHJvZHVjdC1xdWFudGl0eScpKTtcbiAgICAgICRtb2RhbC5maW5kKCdmb3JtJykuYXR0cignYWN0aW9uJywgJGJ0bi5kYXRhKCd1cGRhdGUtdXJsJykpO1xuICAgIH0pO1xuICB9XG5cbiAgZnVuY3Rpb24gaW5pdEFkZENhcnRSdWxlRm9ybUhhbmRsZXIoKSB7XG4gICAgY29uc3QgJG1vZGFsID0gJChPcmRlclZpZXdQYWdlTWFwLmFkZENhcnRSdWxlTW9kYWwpO1xuICAgIGNvbnN0ICRmb3JtID0gJG1vZGFsLmZpbmQoJ2Zvcm0nKTtcbiAgICBjb25zdCAkdmFsdWVIZWxwID0gJG1vZGFsLmZpbmQoT3JkZXJWaWV3UGFnZU1hcC5jYXJ0UnVsZUhlbHBUZXh0KTtcbiAgICBjb25zdCAkaW52b2ljZVNlbGVjdCA9ICRtb2RhbC5maW5kKE9yZGVyVmlld1BhZ2VNYXAuYWRkQ2FydFJ1bGVJbnZvaWNlSWRTZWxlY3QpO1xuICAgIGNvbnN0ICR2YWx1ZUlucHV0ID0gJGZvcm0uZmluZChPcmRlclZpZXdQYWdlTWFwLmFkZENhcnRSdWxlVmFsdWVJbnB1dCk7XG4gICAgY29uc3QgJHZhbHVlRm9ybUdyb3VwID0gJHZhbHVlSW5wdXQuY2xvc2VzdCgnLmZvcm0tZ3JvdXAnKTtcblxuICAgICRmb3JtLmZpbmQoT3JkZXJWaWV3UGFnZU1hcC5hZGRDYXJ0UnVsZUFwcGx5T25BbGxJbnZvaWNlc0NoZWNrYm94KS5vbignY2hhbmdlJywgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCBpc0NoZWNrZWQgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmlzKCc6Y2hlY2tlZCcpO1xuXG4gICAgICAkaW52b2ljZVNlbGVjdC5hdHRyKCdkaXNhYmxlZCcsIGlzQ2hlY2tlZCk7XG4gICAgfSk7XG5cbiAgICAkZm9ybS5maW5kKE9yZGVyVmlld1BhZ2VNYXAuYWRkQ2FydFJ1bGVUeXBlU2VsZWN0KS5vbignY2hhbmdlJywgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCBzZWxlY3RlZENhcnRSdWxlVHlwZSA9ICQoZXZlbnQuY3VycmVudFRhcmdldCkudmFsKCk7XG5cbiAgICAgIGlmIChzZWxlY3RlZENhcnRSdWxlVHlwZSA9PT0gRElTQ09VTlRfVFlQRV9BTU9VTlQpIHtcbiAgICAgICAgJHZhbHVlSGVscC5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICAkdmFsdWVIZWxwLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgICAgIH1cblxuICAgICAgaWYgKHNlbGVjdGVkQ2FydFJ1bGVUeXBlID09PSBESVNDT1VOVF9UWVBFX0ZSRUVfU0hJUFBJTkcpIHtcbiAgICAgICAgJHZhbHVlRm9ybUdyb3VwLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgICAgICAgJHZhbHVlSW5wdXQuYXR0cignZGlzYWJsZWQnLCB0cnVlKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgICR2YWx1ZUZvcm1Hcm91cC5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgICAgICR2YWx1ZUlucHV0LmF0dHIoJ2Rpc2FibGVkJywgZmFsc2UpO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG5cbiAgZnVuY3Rpb24gaGFuZGxlVXBkYXRlT3JkZXJTdGF0dXNCdXR0b24oKSB7XG4gICAgY29uc3QgJGJ0biA9ICQoT3JkZXJWaWV3UGFnZU1hcC51cGRhdGVPcmRlclN0YXR1c0FjdGlvbkJ0bik7XG5cbiAgICAkKE9yZGVyVmlld1BhZ2VNYXAudXBkYXRlT3JkZXJTdGF0dXNBY3Rpb25JbnB1dCkub24oJ2NoYW5nZScsIChldmVudCkgPT4ge1xuICAgICAgY29uc3Qgc2VsZWN0ZWRPcmRlclN0YXR1c0lkID0gJChldmVudC5jdXJyZW50VGFyZ2V0KS52YWwoKTtcblxuICAgICAgJGJ0bi5wcm9wKCdkaXNhYmxlZCcsIHBhcnNlSW50KHNlbGVjdGVkT3JkZXJTdGF0dXNJZCwgMTApID09PSAkYnRuLmRhdGEoJ29yZGVyLXN0YXR1cy1pZCcpKTtcbiAgICB9KTtcbiAgfVxuXG4gIGZ1bmN0aW9uIGluaXRDaGFuZ2VBZGRyZXNzRm9ybUhhbmRsZXIoKSB7XG4gICAgY29uc3QgJG1vZGFsID0gJChPcmRlclZpZXdQYWdlTWFwLnVwZGF0ZUN1c3RvbWVyQWRkcmVzc01vZGFsKTtcblxuICAgICQoT3JkZXJWaWV3UGFnZU1hcC5vcGVuT3JkZXJBZGRyZXNzVXBkYXRlTW9kYWxCdG4pLm9uKCdjbGljaycsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgJGJ0biA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgICAkbW9kYWwuZmluZChPcmRlclZpZXdQYWdlTWFwLnVwZGF0ZU9yZGVyQWRkcmVzc1R5cGVJbnB1dCkudmFsKCRidG4uZGF0YSgnYWRkcmVzcy10eXBlJykpO1xuICAgIH0pO1xuICB9XG5cbiAgJChgJHtPcmRlclZpZXdQYWdlTWFwLmRpc3BsYXlQYXJ0aWFsUmVmdW5kQnRufSwgJHtPcmRlclZpZXdQYWdlTWFwLmNhbmNlbFBhcnRpYWxSZWZ1bmRCdG59YCkub24oJ2NsaWNrJywgKGV2ZW50KSA9PiB7XG4gICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAkKCd0ZC5wcm9kdWN0X2FjdGlvbnMsIHRoLnByb2R1Y3RfYWN0aW9ucywgLnBhcnRpYWwtcmVmdW5kOm5vdCguaGlkZGVuKSwgLnNoaXBwaW5nLXByaWNlLCAucmVmdW5kLWNoZWNrYm94ZXMtY29udGFpbmVyJykudG9nZ2xlKCk7XG4gIH0pO1xufSk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9vcmRlci92aWV3LmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuZXhwb3J0IGRlZmF1bHQge1xuICBvcmRlclBheW1lbnREZXRhaWxzQnRuOiAnLmpzLXBheW1lbnQtZGV0YWlscy1idG4nLFxuICBvcmRlclBheW1lbnRGb3JtQW1vdW50SW5wdXQ6ICcjb3JkZXJfcGF5bWVudF9hbW91bnQnLFxuICB2aWV3T3JkZXJQYXltZW50c0Jsb2NrOiAnI3ZpZXdfb3JkZXJfcGF5bWVudHNfYmxvY2snLFxuICBwcml2YXRlTm90ZVRvZ2dsZUJ0bjogJy5qcy1wcml2YXRlLW5vdGUtdG9nZ2xlLWJ0bicsXG4gIHByaXZhdGVOb3RlQmxvY2s6ICcuanMtcHJpdmF0ZS1ub3RlLWJsb2NrJyxcbiAgcHJpdmF0ZU5vdGVJbnB1dDogJyNwcml2YXRlX25vdGVfbm90ZScsXG4gIHByaXZhdGVOb3RlU3VibWl0QnRuOiAnLmpzLXByaXZhdGUtbm90ZS1idG4nLFxuICB1cGRhdGVPcmRlclByb2R1Y3RNb2RhbDogJyN1cGRhdGVPcmRlclByb2R1Y3RNb2RhbCcsXG4gIHVwZGF0ZU9yZGVyUHJvZHVjdFByaWNlVGF4RXhjbElucHV0OiAnI3VwZGF0ZV9vcmRlcl9wcm9kdWN0X3ByaWNlX3RheF9leGNsJyxcbiAgdXBkYXRlT3JkZXJQcm9kdWN0UHJpY2VUYXhJbmNsSW5wdXQ6ICcjdXBkYXRlX29yZGVyX3Byb2R1Y3RfcHJpY2VfdGF4X2luY2wnLFxuICB1cGRhdGVPcmRlclByb2R1Y3RRdWFudGl0eUlucHV0OiAnI3VwZGF0ZV9vcmRlcl9wcm9kdWN0X3F1YW50aXR5JyxcbiAgYWRkQ2FydFJ1bGVNb2RhbDogJyNhZGRPcmRlckRpc2NvdW50TW9kYWwnLFxuICBhZGRDYXJ0UnVsZUFwcGx5T25BbGxJbnZvaWNlc0NoZWNrYm94OiAnI2FkZF9vcmRlcl9jYXJ0X3J1bGVfYXBwbHlfb25fYWxsX2ludm9pY2VzJyxcbiAgYWRkQ2FydFJ1bGVJbnZvaWNlSWRTZWxlY3Q6ICcjYWRkX29yZGVyX2NhcnRfcnVsZV9pbnZvaWNlX2lkJyxcbiAgYWRkQ2FydFJ1bGVUeXBlU2VsZWN0OiAnI2FkZF9vcmRlcl9jYXJ0X3J1bGVfdHlwZScsXG4gIGFkZENhcnRSdWxlVmFsdWVJbnB1dDogJyNhZGRfb3JkZXJfY2FydF9ydWxlX3ZhbHVlJyxcbiAgY2FydFJ1bGVIZWxwVGV4dDogJy5qcy1jYXJ0LXJ1bGUtdmFsdWUtaGVscCcsXG4gIHVwZGF0ZU9yZGVyU3RhdHVzQWN0aW9uQnRuOiAnI3VwZGF0ZV9vcmRlcl9zdGF0dXNfYWN0aW9uX2J0bicsXG4gIHVwZGF0ZU9yZGVyU3RhdHVzQWN0aW9uSW5wdXQ6ICcjdXBkYXRlX29yZGVyX3N0YXR1c19hY3Rpb25faW5wdXQnLFxuICB1cGRhdGVPcmRlclN0YXR1c0FjdGlvbkZvcm06ICcjdXBkYXRlX29yZGVyX3N0YXR1c19hY3Rpb25fZm9ybScsXG4gIHNob3dPcmRlclNoaXBwaW5nVXBkYXRlTW9kYWxCdG46ICcuanMtdXBkYXRlLXNoaXBwaW5nLWJ0bicsXG4gIHVwZGF0ZU9yZGVyU2hpcHBpbmdUcmFja2luZ051bWJlcklucHV0OiAnI3VwZGF0ZV9vcmRlcl9zaGlwcGluZ190cmFja2luZ19udW1iZXInLFxuICB1cGRhdGVPcmRlclNoaXBwaW5nQ3VycmVudE9yZGVyQ2FycmllcklkSW5wdXQ6ICcjdXBkYXRlX29yZGVyX3NoaXBwaW5nX2N1cnJlbnRfb3JkZXJfY2Fycmllcl9pZCcsXG4gIHVwZGF0ZUN1c3RvbWVyQWRkcmVzc01vZGFsOiAnI3VwZGF0ZUN1c3RvbWVyQWRkcmVzc01vZGFsJyxcbiAgb3Blbk9yZGVyQWRkcmVzc1VwZGF0ZU1vZGFsQnRuOiAnLmpzLXVwZGF0ZS1jdXN0b21lci1hZGRyZXNzLW1vZGFsLWJ0bicsXG4gIHVwZGF0ZU9yZGVyQWRkcmVzc1R5cGVJbnB1dDogJyNjaGFuZ2Vfb3JkZXJfYWRkcmVzc19hZGRyZXNzX3R5cGUnLFxuICBvcmRlck1lc3NhZ2VOYW1lU2VsZWN0OiAnI29yZGVyX21lc3NhZ2Vfb3JkZXJfbWVzc2FnZScsXG4gIG9yZGVyTWVzc2FnZXNDb250YWluZXI6ICcuanMtb3JkZXItbWVzc2FnZXMtY29udGFpbmVyJyxcbiAgb3JkZXJNZXNzYWdlOiAnI29yZGVyX21lc3NhZ2VfbWVzc2FnZScsXG4gIG9yZGVyTWVzc2FnZUNoYW5nZVdhcm5pbmc6ICcuanMtbWVzc2FnZS1jaGFuZ2Utd2FybmluZycsXG4gIGFsbE1lc3NhZ2VzTW9kYWw6ICcjdmlld19hbGxfbWVzc2FnZXNfbW9kYWwnLFxuICBhbGxNZXNzYWdlc0xpc3Q6ICcjYWxsLW1lc3NhZ2VzLWxpc3QnLFxuICBvcGVuQWxsTWVzc2FnZXNCdG46ICcuanMtb3Blbi1hbGwtbWVzc2FnZXMtYnRuJyxcbiAgb3JkZXJIb29rVGFic0NvbnRhaW5lcjogJyNvcmRlcl9ob29rX3RhYnMnLFxuICBkaXNwbGF5UGFydGlhbFJlZnVuZEJ0bjogJ2J1dHRvbi5wYXJ0aWFsLXJlZnVuZC1kaXNwbGF5JyxcbiAgY2FuY2VsUGFydGlhbFJlZnVuZEJ0bjogJ2J1dHRvbi5wYXJ0aWFsLXJlZnVuZC1jYW5jZWwnLFxufTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL29yZGVyL09yZGVyVmlld1BhZ2VNYXAuanMiXSwic291cmNlUm9vdCI6IiJ9