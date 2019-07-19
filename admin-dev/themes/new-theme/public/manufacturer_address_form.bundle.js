window["manufacturer_address_form"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 337);
/******/ })
/************************************************************************/
/******/ ({

/***/ 235:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

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

/**
 * Displays, fills or hides State selection block depending on selected country.
 *
 * Usage:
 *
 * <!-- Country select must have unique identifier & url for states API -->
 * <select name="id_country" id="id_country" states-url="path/to/states/api">
 *   ...
 * </select>
 *
 * <!-- If selected country does not have states, then this block will be hidden -->
 * <div class="js-state-selection-block">
 *   <select name="id_state">
 *     ...
 *   </select>
 * </div>
 *
 * In JS:
 *
 * new CountryStateSelectionToggler('#id_country', '#id_state', '.js-state-selection-block');
 */

var CountryStateSelectionToggler = function () {
  function CountryStateSelectionToggler(countryInputSelector, countryStateSelector, stateSelectionBlockSelector) {
    var _this2 = this;

    _classCallCheck(this, CountryStateSelectionToggler);

    this.$stateSelectionBlock = $(stateSelectionBlockSelector);
    this.$countryStateSelector = $(countryStateSelector);
    this.$countryInput = $(countryInputSelector);

    this.$countryInput.on('change', function () {
      return _this2._toggle();
    });

    // toggle on page load
    this._toggle(true);

    return {};
  }

  /**
   * Toggles State selection
   *
   * @private
   */


  _createClass(CountryStateSelectionToggler, [{
    key: '_toggle',
    value: function _toggle() {
      var _this3 = this;

      var isFirstToggle = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;

      $.ajax({
        url: this.$countryInput.data('states-url'),
        method: 'GET',
        dataType: 'json',
        data: {
          id_country: this.$countryInput.val()
        }
      }).then(function (response) {
        if (response.states.length === 0) {
          _this3.$stateSelectionBlock.fadeOut();

          return;
        }

        _this3.$stateSelectionBlock.fadeIn();

        if (isFirstToggle === false) {
          _this3.$countryStateSelector.empty();
          var _this = _this3;
          $.each(response.states, function (index, value) {
            _this.$countryStateSelector.append($('<option></option>').attr('value', value).text(index));
          });
        }
      }).catch(function (response) {
        if (typeof response.responseJSON !== 'undefined') {
          showErrorMessage(response.responseJSON.message);
        }
      });
    }
  }]);

  return CountryStateSelectionToggler;
}();

exports.default = CountryStateSelectionToggler;

/***/ }),

/***/ 259:
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

exports.default = {
  manufacturerAddressCountrySelect: '#manufacturer_address_id_country',
  manufacturerAddressStateSelect: '#manufacturer_address_id_state',
  manufacturerAddressStateBlock: '.js-manufacturer-address-state'
};

/***/ }),

/***/ 337:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _countryStateSelectionToggler = __webpack_require__(235);

var _countryStateSelectionToggler2 = _interopRequireDefault(_countryStateSelectionToggler);

var _manufacturerAddressMap = __webpack_require__(259);

var _manufacturerAddressMap2 = _interopRequireDefault(_manufacturerAddressMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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

$(document).ready(function () {
  new _countryStateSelectionToggler2.default(_manufacturerAddressMap2.default.manufacturerAddressCountrySelect, _manufacturerAddressMap2.default.manufacturerAddressStateSelect, _manufacturerAddressMap2.default.manufacturerAddressStateBlock);
});

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNWQ5OTkwOTRkMTFhZWZmMGI1ODI/M2YxNioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2NvdW50cnktc3RhdGUtc2VsZWN0aW9uLXRvZ2dsZXIuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvbWFudWZhY3R1cmVyL21hbnVmYWN0dXJlci1hZGRyZXNzLW1hcC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9tYW51ZmFjdHVyZXIvbWFudWZhY3R1cmVyX2FkZHJlc3NfZm9ybS5qcyJdLCJuYW1lcyI6WyIkIiwid2luZG93IiwiQ291bnRyeVN0YXRlU2VsZWN0aW9uVG9nZ2xlciIsImNvdW50cnlJbnB1dFNlbGVjdG9yIiwiY291bnRyeVN0YXRlU2VsZWN0b3IiLCJzdGF0ZVNlbGVjdGlvbkJsb2NrU2VsZWN0b3IiLCIkc3RhdGVTZWxlY3Rpb25CbG9jayIsIiRjb3VudHJ5U3RhdGVTZWxlY3RvciIsIiRjb3VudHJ5SW5wdXQiLCJvbiIsIl90b2dnbGUiLCJpc0ZpcnN0VG9nZ2xlIiwiYWpheCIsInVybCIsImRhdGEiLCJtZXRob2QiLCJkYXRhVHlwZSIsImlkX2NvdW50cnkiLCJ2YWwiLCJ0aGVuIiwicmVzcG9uc2UiLCJzdGF0ZXMiLCJsZW5ndGgiLCJmYWRlT3V0IiwiZmFkZUluIiwiZW1wdHkiLCJfdGhpcyIsImVhY2giLCJpbmRleCIsInZhbHVlIiwiYXBwZW5kIiwiYXR0ciIsInRleHQiLCJjYXRjaCIsInJlc3BvbnNlSlNPTiIsInNob3dFcnJvck1lc3NhZ2UiLCJtZXNzYWdlIiwibWFudWZhY3R1cmVyQWRkcmVzc0NvdW50cnlTZWxlY3QiLCJtYW51ZmFjdHVyZXJBZGRyZXNzU3RhdGVTZWxlY3QiLCJtYW51ZmFjdHVyZXJBZGRyZXNzU3RhdGVCbG9jayIsImRvY3VtZW50IiwicmVhZHkiLCJNYW51ZmFjdHVyZXJBZGRyZXNz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O0FBMUJBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBNEJBLElBQU12QyxJQUFJQyxPQUFPRCxDQUFqQjs7QUFFQUEsRUFBRXdDLFFBQUYsRUFBWUMsS0FBWixDQUFrQixZQUFNO0FBQ3RCLE1BQUl2QyxzQ0FBSixDQUNFd0MsaUNBQXVCTCxnQ0FEekIsRUFFRUssaUNBQXVCSiw4QkFGekIsRUFHRUksaUNBQXVCSCw2QkFIekI7QUFLRCxDQU5ELEUiLCJmaWxlIjoibWFudWZhY3R1cmVyX2FkZHJlc3NfZm9ybS5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBpZGVudGl0eSBmdW5jdGlvbiBmb3IgY2FsbGluZyBoYXJtb255IGltcG9ydHMgd2l0aCB0aGUgY29ycmVjdCBjb250ZXh0XG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmkgPSBmdW5jdGlvbih2YWx1ZSkgeyByZXR1cm4gdmFsdWU7IH07XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDMzNyk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gd2VicGFjay9ib290c3RyYXAgNWQ5OTkwOTRkMTFhZWZmMGI1ODIiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogRGlzcGxheXMsIGZpbGxzIG9yIGhpZGVzIFN0YXRlIHNlbGVjdGlvbiBibG9jayBkZXBlbmRpbmcgb24gc2VsZWN0ZWQgY291bnRyeS5cbiAqXG4gKiBVc2FnZTpcbiAqXG4gKiA8IS0tIENvdW50cnkgc2VsZWN0IG11c3QgaGF2ZSB1bmlxdWUgaWRlbnRpZmllciAmIHVybCBmb3Igc3RhdGVzIEFQSSAtLT5cbiAqIDxzZWxlY3QgbmFtZT1cImlkX2NvdW50cnlcIiBpZD1cImlkX2NvdW50cnlcIiBzdGF0ZXMtdXJsPVwicGF0aC90by9zdGF0ZXMvYXBpXCI+XG4gKiAgIC4uLlxuICogPC9zZWxlY3Q+XG4gKlxuICogPCEtLSBJZiBzZWxlY3RlZCBjb3VudHJ5IGRvZXMgbm90IGhhdmUgc3RhdGVzLCB0aGVuIHRoaXMgYmxvY2sgd2lsbCBiZSBoaWRkZW4gLS0+XG4gKiA8ZGl2IGNsYXNzPVwianMtc3RhdGUtc2VsZWN0aW9uLWJsb2NrXCI+XG4gKiAgIDxzZWxlY3QgbmFtZT1cImlkX3N0YXRlXCI+XG4gKiAgICAgLi4uXG4gKiAgIDwvc2VsZWN0PlxuICogPC9kaXY+XG4gKlxuICogSW4gSlM6XG4gKlxuICogbmV3IENvdW50cnlTdGF0ZVNlbGVjdGlvblRvZ2dsZXIoJyNpZF9jb3VudHJ5JywgJyNpZF9zdGF0ZScsICcuanMtc3RhdGUtc2VsZWN0aW9uLWJsb2NrJyk7XG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIENvdW50cnlTdGF0ZVNlbGVjdGlvblRvZ2dsZXIge1xuICBjb25zdHJ1Y3Rvcihjb3VudHJ5SW5wdXRTZWxlY3RvciwgY291bnRyeVN0YXRlU2VsZWN0b3IsIHN0YXRlU2VsZWN0aW9uQmxvY2tTZWxlY3Rvcikge1xuICAgIHRoaXMuJHN0YXRlU2VsZWN0aW9uQmxvY2sgPSAkKHN0YXRlU2VsZWN0aW9uQmxvY2tTZWxlY3Rvcik7XG4gICAgdGhpcy4kY291bnRyeVN0YXRlU2VsZWN0b3IgPSAkKGNvdW50cnlTdGF0ZVNlbGVjdG9yKTtcbiAgICB0aGlzLiRjb3VudHJ5SW5wdXQgPSAkKGNvdW50cnlJbnB1dFNlbGVjdG9yKTtcblxuICAgIHRoaXMuJGNvdW50cnlJbnB1dC5vbignY2hhbmdlJywgKCkgPT4gdGhpcy5fdG9nZ2xlKCkpO1xuXG4gICAgLy8gdG9nZ2xlIG9uIHBhZ2UgbG9hZFxuICAgIHRoaXMuX3RvZ2dsZSh0cnVlKTtcblxuICAgIHJldHVybiB7fTtcbiAgfVxuXG4gIC8qKlxuICAgKiBUb2dnbGVzIFN0YXRlIHNlbGVjdGlvblxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3RvZ2dsZShpc0ZpcnN0VG9nZ2xlID0gZmFsc2UpIHtcbiAgICAkLmFqYXgoe1xuICAgICAgdXJsOiB0aGlzLiRjb3VudHJ5SW5wdXQuZGF0YSgnc3RhdGVzLXVybCcpLFxuICAgICAgbWV0aG9kOiAnR0VUJyxcbiAgICAgIGRhdGFUeXBlOiAnanNvbicsXG4gICAgICBkYXRhOiB7XG4gICAgICAgIGlkX2NvdW50cnk6IHRoaXMuJGNvdW50cnlJbnB1dC52YWwoKSxcbiAgICAgIH1cbiAgICB9KS50aGVuKChyZXNwb25zZSkgPT4ge1xuICAgICAgaWYgKHJlc3BvbnNlLnN0YXRlcy5sZW5ndGggPT09IDApIHtcbiAgICAgICAgdGhpcy4kc3RhdGVTZWxlY3Rpb25CbG9jay5mYWRlT3V0KCk7XG5cbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICB0aGlzLiRzdGF0ZVNlbGVjdGlvbkJsb2NrLmZhZGVJbigpO1xuXG4gICAgICBpZiAoaXNGaXJzdFRvZ2dsZSA9PT0gZmFsc2UpIHtcbiAgICAgICAgdGhpcy4kY291bnRyeVN0YXRlU2VsZWN0b3IuZW1wdHkoKTtcbiAgICAgICAgdmFyIF90aGlzID0gdGhpcztcbiAgICAgICAgJC5lYWNoKHJlc3BvbnNlLnN0YXRlcywgZnVuY3Rpb24gKGluZGV4LCB2YWx1ZSkge1xuICAgICAgICAgIF90aGlzLiRjb3VudHJ5U3RhdGVTZWxlY3Rvci5hcHBlbmQoJCgnPG9wdGlvbj48L29wdGlvbj4nKS5hdHRyKCd2YWx1ZScsIHZhbHVlKS50ZXh0KGluZGV4KSk7XG4gICAgICAgIH0pXG4gICAgICB9XG4gICAgfSkuY2F0Y2goKHJlc3BvbnNlKSA9PiB7XG4gICAgICBpZiAodHlwZW9mIHJlc3BvbnNlLnJlc3BvbnNlSlNPTiAhPT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICAgc2hvd0Vycm9yTWVzc2FnZShyZXNwb25zZS5yZXNwb25zZUpTT04ubWVzc2FnZSk7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvY291bnRyeS1zdGF0ZS1zZWxlY3Rpb24tdG9nZ2xlci5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmV4cG9ydCBkZWZhdWx0IHtcbiAgbWFudWZhY3R1cmVyQWRkcmVzc0NvdW50cnlTZWxlY3Q6ICcjbWFudWZhY3R1cmVyX2FkZHJlc3NfaWRfY291bnRyeScsXG4gIG1hbnVmYWN0dXJlckFkZHJlc3NTdGF0ZVNlbGVjdDogJyNtYW51ZmFjdHVyZXJfYWRkcmVzc19pZF9zdGF0ZScsXG4gIG1hbnVmYWN0dXJlckFkZHJlc3NTdGF0ZUJsb2NrOiAnLmpzLW1hbnVmYWN0dXJlci1hZGRyZXNzLXN0YXRlJyxcbn07XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9tYW51ZmFjdHVyZXIvbWFudWZhY3R1cmVyLWFkZHJlc3MtbWFwLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IENvdW50cnlTdGF0ZVNlbGVjdGlvblRvZ2dsZXIgZnJvbSAnLi4vLi4vY29tcG9uZW50cy9jb3VudHJ5LXN0YXRlLXNlbGVjdGlvbi10b2dnbGVyJztcbmltcG9ydCBNYW51ZmFjdHVyZXJBZGRyZXNzTWFwIGZyb20gJy4vbWFudWZhY3R1cmVyLWFkZHJlc3MtbWFwJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4kKGRvY3VtZW50KS5yZWFkeSgoKSA9PiB7XG4gIG5ldyBDb3VudHJ5U3RhdGVTZWxlY3Rpb25Ub2dnbGVyKFxuICAgIE1hbnVmYWN0dXJlckFkZHJlc3NNYXAubWFudWZhY3R1cmVyQWRkcmVzc0NvdW50cnlTZWxlY3QsXG4gICAgTWFudWZhY3R1cmVyQWRkcmVzc01hcC5tYW51ZmFjdHVyZXJBZGRyZXNzU3RhdGVTZWxlY3QsXG4gICAgTWFudWZhY3R1cmVyQWRkcmVzc01hcC5tYW51ZmFjdHVyZXJBZGRyZXNzU3RhdGVCbG9ja1xuICApO1xufSk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9tYW51ZmFjdHVyZXIvbWFudWZhY3R1cmVyX2FkZHJlc3NfZm9ybS5qcyJdLCJzb3VyY2VSb290IjoiIn0=