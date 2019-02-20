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
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
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
/******/ 	__webpack_require__.p = "/admin-dev/themes/new-theme/public/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./js/pages/product-preferences/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./js/components/translatable-input.js":
/*!*********************************************!*\
  !*** ./js/components/translatable-input.js ***!
  \*********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

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
var $ = window.$;

var TranslatableInput =
/*#__PURE__*/
function () {
  function TranslatableInput(options) {
    _classCallCheck(this, TranslatableInput);

    options = options || {};
    this.localeItemSelector = options.localeItemSelector || '.js-locale-item';
    this.localeButtonSelector = options.localeButtonSelector || '.js-locale-btn';
    this.localeInputSelector = options.localeInputSelector || '.js-locale-input';
    $('body').on('click', this.localeItemSelector, this.toggleInputs.bind(this));
  }
  /**
   * Toggle all translatable inputs in form in which locale was changed
   *
   * @param {Event} event
   */


  _createClass(TranslatableInput, [{
    key: "toggleInputs",
    value: function toggleInputs(event) {
      var localeItem = $(event.target);
      var form = localeItem.closest('form');
      var selectedLocale = localeItem.data('locale');
      form.find(this.localeButtonSelector).text(selectedLocale);
      form.find(this.localeInputSelector).addClass('d-none');
      form.find(this.localeInputSelector + '.js-locale-' + selectedLocale).removeClass('d-none');
    }
  }]);

  return TranslatableInput;
}();

/* harmony default export */ __webpack_exports__["default"] = (TranslatableInput);

/***/ }),

/***/ "./js/pages/product-preferences/index.js":
/*!***********************************************!*\
  !*** ./js/pages/product-preferences/index.js ***!
  \***********************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _components_translatable_input__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../components/translatable-input */ "./js/components/translatable-input.js");
/* harmony import */ var _stock_management_option_handler__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./stock-management-option-handler */ "./js/pages/product-preferences/stock-management-option-handler.js");
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


var $ = window.$;
$(function () {
  new _components_translatable_input__WEBPACK_IMPORTED_MODULE_0__["default"]();
  new _stock_management_option_handler__WEBPACK_IMPORTED_MODULE_1__["default"]();
});

/***/ }),

/***/ "./js/pages/product-preferences/stock-management-option-handler.js":
/*!*************************************************************************!*\
  !*** ./js/pages/product-preferences/stock-management-option-handler.js ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

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
var $ = window.$;

var StockManagementOptionHandler =
/*#__PURE__*/
function () {
  function StockManagementOptionHandler() {
    var _this = this;

    _classCallCheck(this, StockManagementOptionHandler);

    this.handle();
    $('input[name="form[stock][stock_management]"]').on('change', function () {
      return _this.handle();
    });
  }

  _createClass(StockManagementOptionHandler, [{
    key: "handle",
    value: function handle() {
      var stockManagementVal = $('input[name="form[stock][stock_management]"]:checked').val();
      var isStockManagementEnabled = parseInt(stockManagementVal);
      this.handleAllowOrderingOutOfStockOption(isStockManagementEnabled);
      this.handleDisplayAvailableQuantitiesOption(isStockManagementEnabled);
    }
    /**
     * If stock managament is disabled
     * then 'Allow ordering of out-of-stock products' option must be Yes and disabled
     * otherwise it should be enabled
     *
     * @param {int} isStockManagementEnabled
     */

  }, {
    key: "handleAllowOrderingOutOfStockOption",
    value: function handleAllowOrderingOutOfStockOption(isStockManagementEnabled) {
      var allowOrderingOosRadios = $('input[name="form[stock][allow_ordering_oos]"]');

      if (isStockManagementEnabled) {
        allowOrderingOosRadios.removeAttr('disabled');
      } else {
        allowOrderingOosRadios.val([1]);
        allowOrderingOosRadios.attr('disabled', 'disabled');
      }
    }
    /**
     * If stock managament is disabled
     * then 'Display available quantities on the product page' option must be No and disabled
     * otherwise it should be enabled
     *
     * @param {int} isStockManagementEnabled
     */

  }, {
    key: "handleDisplayAvailableQuantitiesOption",
    value: function handleDisplayAvailableQuantitiesOption(isStockManagementEnabled) {
      var displayQuantitiesRadio = $('input[name="form[page][display_quantities]"]');

      if (isStockManagementEnabled) {
        displayQuantitiesRadio.removeAttr('disabled');
      } else {
        displayQuantitiesRadio.val([0]);
        displayQuantitiesRadio.attr('disabled', 'disabled');
      }
    }
  }]);

  return StockManagementOptionHandler;
}();

/* harmony default export */ __webpack_exports__["default"] = (StockManagementOptionHandler);

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy90cmFuc2xhdGFibGUtaW5wdXQuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvcHJvZHVjdC1wcmVmZXJlbmNlcy9pbmRleC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9wcm9kdWN0LXByZWZlcmVuY2VzL3N0b2NrLW1hbmFnZW1lbnQtb3B0aW9uLWhhbmRsZXIuanMiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsIlRyYW5zbGF0YWJsZUlucHV0Iiwib3B0aW9ucyIsImxvY2FsZUl0ZW1TZWxlY3RvciIsImxvY2FsZUJ1dHRvblNlbGVjdG9yIiwibG9jYWxlSW5wdXRTZWxlY3RvciIsIm9uIiwidG9nZ2xlSW5wdXRzIiwiYmluZCIsImV2ZW50IiwibG9jYWxlSXRlbSIsInRhcmdldCIsImZvcm0iLCJjbG9zZXN0Iiwic2VsZWN0ZWRMb2NhbGUiLCJkYXRhIiwiZmluZCIsInRleHQiLCJhZGRDbGFzcyIsInJlbW92ZUNsYXNzIiwiU3RvY2tNYW5hZ2VtZW50T3B0aW9uSGFuZGxlciIsImhhbmRsZSIsInN0b2NrTWFuYWdlbWVudFZhbCIsInZhbCIsImlzU3RvY2tNYW5hZ2VtZW50RW5hYmxlZCIsInBhcnNlSW50IiwiaGFuZGxlQWxsb3dPcmRlcmluZ091dE9mU3RvY2tPcHRpb24iLCJoYW5kbGVEaXNwbGF5QXZhaWxhYmxlUXVhbnRpdGllc09wdGlvbiIsImFsbG93T3JkZXJpbmdPb3NSYWRpb3MiLCJyZW1vdmVBdHRyIiwiYXR0ciIsImRpc3BsYXlRdWFudGl0aWVzUmFkaW8iXSwibWFwcGluZ3MiOiI7QUFBQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7O0FBR0E7QUFDQTs7QUFFQTtBQUNBOztBQUVB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QSxXQUFLRyxzQ0FBTCxDQUE0Q0gsd0JBQTVDO0FBQ0Q7QUFFRDs7Ozs7Ozs7Ozt3REFPb0NBLHdCLEVBQTBCO0FBQzVELFVBQU1JLHNCQUFzQixHQUFHN0IsQ0FBQyxDQUFDLCtDQUFELENBQWhDOztBQUVBLFVBQUl5Qix3QkFBSixFQUE4QjtBQUMxQkksOEJBQXNCLENBQUNDLFVBQXZCLENBQWtDLFVBQWxDO0FBQ0gsT0FGRCxNQUVPO0FBQ0hELDhCQUFzQixDQUFDTCxHQUF2QixDQUEyQixDQUFDLENBQUQsQ0FBM0I7QUFDQUssOEJBQXNCLENBQUNFLElBQXZCLENBQTRCLFVBQTVCLEVBQXdDLFVBQXhDO0FBQ0g7QUFDRjtBQUVEOzs7Ozs7Ozs7OzJEQU91Q04sd0IsRUFBMEI7QUFDL0QsVUFBTU8sc0JBQXNCLEdBQUdoQyxDQUFDLENBQUMsOENBQUQsQ0FBaEM7O0FBRUEsVUFBSXlCLHdCQUFKLEVBQThCO0FBQzFCTyw4QkFBc0IsQ0FBQ0YsVUFBdkIsQ0FBa0MsVUFBbEM7QUFDSCxPQUZELE1BRU87QUFDSEUsOEJBQXNCLENBQUNSLEdBQXZCLENBQTJCLENBQUMsQ0FBRCxDQUEzQjtBQUNBUSw4QkFBc0IsQ0FBQ0QsSUFBdkIsQ0FBNEIsVUFBNUIsRUFBd0MsVUFBeEM7QUFDSDtBQUNGOzs7Ozs7QUFHWVYsMkZBQWYsRSIsImZpbGUiOiJwcm9kdWN0X3ByZWZlcmVuY2VzLmJ1bmRsZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwgeyBlbnVtZXJhYmxlOiB0cnVlLCBnZXQ6IGdldHRlciB9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZGVmaW5lIF9fZXNNb2R1bGUgb24gZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5yID0gZnVuY3Rpb24oZXhwb3J0cykge1xuIFx0XHRpZih0eXBlb2YgU3ltYm9sICE9PSAndW5kZWZpbmVkJyAmJiBTeW1ib2wudG9TdHJpbmdUYWcpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgU3ltYm9sLnRvU3RyaW5nVGFnLCB7IHZhbHVlOiAnTW9kdWxlJyB9KTtcbiBcdFx0fVxuIFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgJ19fZXNNb2R1bGUnLCB7IHZhbHVlOiB0cnVlIH0pO1xuIFx0fTtcblxuIFx0Ly8gY3JlYXRlIGEgZmFrZSBuYW1lc3BhY2Ugb2JqZWN0XG4gXHQvLyBtb2RlICYgMTogdmFsdWUgaXMgYSBtb2R1bGUgaWQsIHJlcXVpcmUgaXRcbiBcdC8vIG1vZGUgJiAyOiBtZXJnZSBhbGwgcHJvcGVydGllcyBvZiB2YWx1ZSBpbnRvIHRoZSBuc1xuIFx0Ly8gbW9kZSAmIDQ6IHJldHVybiB2YWx1ZSB3aGVuIGFscmVhZHkgbnMgb2JqZWN0XG4gXHQvLyBtb2RlICYgOHwxOiBiZWhhdmUgbGlrZSByZXF1aXJlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnQgPSBmdW5jdGlvbih2YWx1ZSwgbW9kZSkge1xuIFx0XHRpZihtb2RlICYgMSkgdmFsdWUgPSBfX3dlYnBhY2tfcmVxdWlyZV9fKHZhbHVlKTtcbiBcdFx0aWYobW9kZSAmIDgpIHJldHVybiB2YWx1ZTtcbiBcdFx0aWYoKG1vZGUgJiA0KSAmJiB0eXBlb2YgdmFsdWUgPT09ICdvYmplY3QnICYmIHZhbHVlICYmIHZhbHVlLl9fZXNNb2R1bGUpIHJldHVybiB2YWx1ZTtcbiBcdFx0dmFyIG5zID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5yKG5zKTtcbiBcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KG5zLCAnZGVmYXVsdCcsIHsgZW51bWVyYWJsZTogdHJ1ZSwgdmFsdWU6IHZhbHVlIH0pO1xuIFx0XHRpZihtb2RlICYgMiAmJiB0eXBlb2YgdmFsdWUgIT0gJ3N0cmluZycpIGZvcih2YXIga2V5IGluIHZhbHVlKSBfX3dlYnBhY2tfcmVxdWlyZV9fLmQobnMsIGtleSwgZnVuY3Rpb24oa2V5KSB7IHJldHVybiB2YWx1ZVtrZXldOyB9LmJpbmQobnVsbCwga2V5KSk7XG4gXHRcdHJldHVybiBucztcbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiL2FkbWluLWRldi90aGVtZXMvbmV3LXRoZW1lL3B1YmxpYy9cIjtcblxuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IFwiLi9qcy9wYWdlcy9wcm9kdWN0LXByZWZlcmVuY2VzL2luZGV4LmpzXCIpO1xuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG5jbGFzcyBUcmFuc2xhdGFibGVJbnB1dCB7XG4gICAgY29uc3RydWN0b3Iob3B0aW9ucykge1xuICAgICAgICBvcHRpb25zID0gb3B0aW9ucyB8fCB7fTtcblxuICAgICAgICB0aGlzLmxvY2FsZUl0ZW1TZWxlY3RvciA9IG9wdGlvbnMubG9jYWxlSXRlbVNlbGVjdG9yIHx8wqAnLmpzLWxvY2FsZS1pdGVtJztcbiAgICAgICAgdGhpcy5sb2NhbGVCdXR0b25TZWxlY3RvciA9IG9wdGlvbnMubG9jYWxlQnV0dG9uU2VsZWN0b3IgfHzCoCcuanMtbG9jYWxlLWJ0bic7XG4gICAgICAgIHRoaXMubG9jYWxlSW5wdXRTZWxlY3RvciA9IG9wdGlvbnMubG9jYWxlSW5wdXRTZWxlY3RvciB8fMKgJy5qcy1sb2NhbGUtaW5wdXQnO1xuXG4gICAgICAgICQoJ2JvZHknKS5vbignY2xpY2snLCB0aGlzLmxvY2FsZUl0ZW1TZWxlY3RvciwgdGhpcy50b2dnbGVJbnB1dHMuYmluZCh0aGlzKSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogVG9nZ2xlIGFsbCB0cmFuc2xhdGFibGUgaW5wdXRzIGluIGZvcm0gaW4gd2hpY2ggbG9jYWxlIHdhcyBjaGFuZ2VkXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge0V2ZW50fSBldmVudFxuICAgICAqL1xuICAgIHRvZ2dsZUlucHV0cyhldmVudCkge1xuICAgICAgICBjb25zdCBsb2NhbGVJdGVtID0gJChldmVudC50YXJnZXQpO1xuICAgICAgICBjb25zdCBmb3JtID0gbG9jYWxlSXRlbS5jbG9zZXN0KCdmb3JtJyk7XG4gICAgICAgIGNvbnN0IHNlbGVjdGVkTG9jYWxlID0gbG9jYWxlSXRlbS5kYXRhKCdsb2NhbGUnKTtcblxuICAgICAgICBmb3JtLmZpbmQodGhpcy5sb2NhbGVCdXR0b25TZWxlY3RvcikudGV4dChzZWxlY3RlZExvY2FsZSk7XG4gICAgICAgIGZvcm0uZmluZCh0aGlzLmxvY2FsZUlucHV0U2VsZWN0b3IpLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgICAgICAgZm9ybS5maW5kKHRoaXMubG9jYWxlSW5wdXRTZWxlY3RvcisnLmpzLWxvY2FsZS0nICsgc2VsZWN0ZWRMb2NhbGUpLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgICB9XG59XG5cbmV4cG9ydCBkZWZhdWx0IFRyYW5zbGF0YWJsZUlucHV0O1xuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IFRyYW5zbGF0YWJsZUlucHV0IGZyb20gJy4uLy4uL2NvbXBvbmVudHMvdHJhbnNsYXRhYmxlLWlucHV0JztcbmltcG9ydCBTdG9ja01hbmFnZW1lbnRPcHRpb25IYW5kbGVyIGZyb20gJy4vc3RvY2stbWFuYWdlbWVudC1vcHRpb24taGFuZGxlcic7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuJCgoKSA9PiB7XG4gIG5ldyBUcmFuc2xhdGFibGVJbnB1dCgpO1xuICBuZXcgU3RvY2tNYW5hZ2VtZW50T3B0aW9uSGFuZGxlcigpO1xufSk7XG4iLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbmNsYXNzIFN0b2NrTWFuYWdlbWVudE9wdGlvbkhhbmRsZXIge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLmhhbmRsZSgpO1xuXG4gICAgJCgnaW5wdXRbbmFtZT1cImZvcm1bc3RvY2tdW3N0b2NrX21hbmFnZW1lbnRdXCJdJykub24oJ2NoYW5nZScsICgpID0+IHRoaXMuaGFuZGxlKCkpO1xuICB9XG5cbiAgaGFuZGxlKCkge1xuICAgIGNvbnN0IHN0b2NrTWFuYWdlbWVudFZhbCA9ICQoJ2lucHV0W25hbWU9XCJmb3JtW3N0b2NrXVtzdG9ja19tYW5hZ2VtZW50XVwiXTpjaGVja2VkJykudmFsKCk7XG4gICAgY29uc3QgaXNTdG9ja01hbmFnZW1lbnRFbmFibGVkID0gcGFyc2VJbnQoc3RvY2tNYW5hZ2VtZW50VmFsKTtcblxuICAgIHRoaXMuaGFuZGxlQWxsb3dPcmRlcmluZ091dE9mU3RvY2tPcHRpb24oaXNTdG9ja01hbmFnZW1lbnRFbmFibGVkKTtcbiAgICB0aGlzLmhhbmRsZURpc3BsYXlBdmFpbGFibGVRdWFudGl0aWVzT3B0aW9uKGlzU3RvY2tNYW5hZ2VtZW50RW5hYmxlZCk7XG4gIH1cblxuICAvKipcbiAgICogSWYgc3RvY2sgbWFuYWdhbWVudCBpcyBkaXNhYmxlZFxuICAgKiB0aGVuICdBbGxvdyBvcmRlcmluZyBvZiBvdXQtb2Ytc3RvY2sgcHJvZHVjdHMnIG9wdGlvbiBtdXN0IGJlIFllcyBhbmQgZGlzYWJsZWRcbiAgICogb3RoZXJ3aXNlIGl0IHNob3VsZCBiZSBlbmFibGVkXG4gICAqXG4gICAqIEBwYXJhbSB7aW50fSBpc1N0b2NrTWFuYWdlbWVudEVuYWJsZWRcbiAgICovXG4gIGhhbmRsZUFsbG93T3JkZXJpbmdPdXRPZlN0b2NrT3B0aW9uKGlzU3RvY2tNYW5hZ2VtZW50RW5hYmxlZCkge1xuICAgIGNvbnN0IGFsbG93T3JkZXJpbmdPb3NSYWRpb3MgPSAkKCdpbnB1dFtuYW1lPVwiZm9ybVtzdG9ja11bYWxsb3dfb3JkZXJpbmdfb29zXVwiXScpO1xuXG4gICAgaWYgKGlzU3RvY2tNYW5hZ2VtZW50RW5hYmxlZCkge1xuICAgICAgICBhbGxvd09yZGVyaW5nT29zUmFkaW9zLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XG4gICAgfSBlbHNlIHtcbiAgICAgICAgYWxsb3dPcmRlcmluZ09vc1JhZGlvcy52YWwoWzFdKTtcbiAgICAgICAgYWxsb3dPcmRlcmluZ09vc1JhZGlvcy5hdHRyKCdkaXNhYmxlZCcsICdkaXNhYmxlZCcpO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBJZiBzdG9jayBtYW5hZ2FtZW50IGlzIGRpc2FibGVkXG4gICAqIHRoZW4gJ0Rpc3BsYXkgYXZhaWxhYmxlIHF1YW50aXRpZXMgb24gdGhlIHByb2R1Y3QgcGFnZScgb3B0aW9uIG11c3QgYmUgTm8gYW5kIGRpc2FibGVkXG4gICAqIG90aGVyd2lzZSBpdCBzaG91bGQgYmUgZW5hYmxlZFxuICAgKlxuICAgKiBAcGFyYW0ge2ludH0gaXNTdG9ja01hbmFnZW1lbnRFbmFibGVkXG4gICAqL1xuICBoYW5kbGVEaXNwbGF5QXZhaWxhYmxlUXVhbnRpdGllc09wdGlvbihpc1N0b2NrTWFuYWdlbWVudEVuYWJsZWQpIHtcbiAgICBjb25zdCBkaXNwbGF5UXVhbnRpdGllc1JhZGlvID0gJCgnaW5wdXRbbmFtZT1cImZvcm1bcGFnZV1bZGlzcGxheV9xdWFudGl0aWVzXVwiXScpO1xuXG4gICAgaWYgKGlzU3RvY2tNYW5hZ2VtZW50RW5hYmxlZCkge1xuICAgICAgICBkaXNwbGF5UXVhbnRpdGllc1JhZGlvLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XG4gICAgfSBlbHNlIHtcbiAgICAgICAgZGlzcGxheVF1YW50aXRpZXNSYWRpby52YWwoWzBdKTtcbiAgICAgICAgZGlzcGxheVF1YW50aXRpZXNSYWRpby5hdHRyKCdkaXNhYmxlZCcsICdkaXNhYmxlZCcpO1xuICAgIH1cbiAgfVxufVxuXG5leHBvcnQgZGVmYXVsdCBTdG9ja01hbmFnZW1lbnRPcHRpb25IYW5kbGVyO1xuIl0sInNvdXJjZVJvb3QiOiIifQ==