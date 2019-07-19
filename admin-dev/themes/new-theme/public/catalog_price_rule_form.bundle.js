window["catalog_price_rule_form"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 314);
/******/ })
/************************************************************************/
/******/ ({

/***/ 249:
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
 * Shows/hides 'include_tax' field depending from 'reduction_type' field value
 */

var IncludeTaxFieldVisibilityHandler = function () {
  function IncludeTaxFieldVisibilityHandler(sourceSelector, targetSelector) {
    var _this = this;

    _classCallCheck(this, IncludeTaxFieldVisibilityHandler);

    this.$sourceSelector = sourceSelector;
    this.$targetSelector = targetSelector;
    this._handle();
    $(sourceSelector).on('change', function () {
      return _this._handle();
    });

    return {};
  }

  /**
   * When source value is 'percentage', target field is shown, else hidden
   *
   * @private
   */


  _createClass(IncludeTaxFieldVisibilityHandler, [{
    key: '_handle',
    value: function _handle() {
      $(this.$targetSelector).fadeIn();

      if ($('' + this.$sourceSelector).val() === 'percentage') {
        $(this.$targetSelector).fadeOut();
      }
    }
  }]);

  return IncludeTaxFieldVisibilityHandler;
}();

exports.default = IncludeTaxFieldVisibilityHandler;

/***/ }),

/***/ 250:
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
 * Enables/disables 'price' field depending from 'leave_initial_price' field checkbox value
 */

var PriceFieldAvailabilityHandler = function () {
  function PriceFieldAvailabilityHandler(checkboxSelector, targetSelector) {
    var _this = this;

    _classCallCheck(this, PriceFieldAvailabilityHandler);

    this.$sourceSelector = checkboxSelector;
    this.$targetSelector = targetSelector;
    this._handle();
    $(checkboxSelector).on('change', function () {
      return _this._handle();
    });

    return {};
  }

  /**
   * When checkbox value is 1, target field is disabled, else enabled
   *
   * @private
   */


  _createClass(PriceFieldAvailabilityHandler, [{
    key: '_handle',
    value: function _handle() {
      var checkboxVal = $('' + this.$sourceSelector).is(':checked');

      $(this.$targetSelector).prop('disabled', checkboxVal);
    }
  }]);

  return PriceFieldAvailabilityHandler;
}();

exports.default = PriceFieldAvailabilityHandler;

/***/ }),

/***/ 314:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _priceFieldAvailabilityHandler = __webpack_require__(250);

var _priceFieldAvailabilityHandler2 = _interopRequireDefault(_priceFieldAvailabilityHandler);

var _includeTaxFieldVisibilityHandler = __webpack_require__(249);

var _includeTaxFieldVisibilityHandler2 = _interopRequireDefault(_includeTaxFieldVisibilityHandler);

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

$(function () {
  new _priceFieldAvailabilityHandler2.default('#catalog_price_rule_leave_initial_price', '#catalog_price_rule_price');
  new _includeTaxFieldVisibilityHandler2.default('.js-reduction-type-source', '.js-include-tax-target');
});

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNWQ5OTkwOTRkMTFhZWZmMGI1ODI/M2YxNioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL2NhdGFsb2ctcHJpY2UtcnVsZS9mb3JtL2luY2x1ZGUtdGF4LWZpZWxkLXZpc2liaWxpdHktaGFuZGxlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9jYXRhbG9nLXByaWNlLXJ1bGUvZm9ybS9wcmljZS1maWVsZC1hdmFpbGFiaWxpdHktaGFuZGxlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9jYXRhbG9nLXByaWNlLXJ1bGUvZm9ybS9pbmRleC5qcyJdLCJuYW1lcyI6WyIkIiwid2luZG93IiwiSW5jbHVkZVRheEZpZWxkVmlzaWJpbGl0eUhhbmRsZXIiLCJzb3VyY2VTZWxlY3RvciIsInRhcmdldFNlbGVjdG9yIiwiJHNvdXJjZVNlbGVjdG9yIiwiJHRhcmdldFNlbGVjdG9yIiwiX2hhbmRsZSIsIm9uIiwiZmFkZUluIiwidmFsIiwiZmFkZU91dCIsIlByaWNlRmllbGRBdmFpbGFiaWxpdHlIYW5kbGVyIiwiY2hlY2tib3hTZWxlY3RvciIsImNoZWNrYm94VmFsIiwiaXMiLCJwcm9wIl0sIm1hcHBpbmdzIjoiOztBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQSxtREFBMkMsY0FBYzs7QUFFekQ7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxhQUFLO0FBQ0w7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxtQ0FBMkIsMEJBQTBCLEVBQUU7QUFDdkQseUNBQWlDLGVBQWU7QUFDaEQ7QUFDQTtBQUNBOztBQUVBO0FBQ0EsOERBQXNELCtEQUErRDs7QUFFckg7QUFDQTs7QUFFQTtBQUNBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FDaEVBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBeUJBLElBQU1BLElBQUlDLE9BQU9ELENBQWpCOztBQUVBOzs7O0lBR3FCRSxnQztBQUNuQiw0Q0FBWUMsY0FBWixFQUE0QkMsY0FBNUIsRUFBNEM7QUFBQTs7QUFBQTs7QUFDMUMsU0FBS0MsZUFBTCxHQUF1QkYsY0FBdkI7QUFDQSxTQUFLRyxlQUFMLEdBQXVCRixjQUF2QjtBQUNBLFNBQUtHLE9BQUw7QUFDQVAsTUFBRUcsY0FBRixFQUFrQkssRUFBbEIsQ0FBcUIsUUFBckIsRUFBK0I7QUFBQSxhQUFNLE1BQUtELE9BQUwsRUFBTjtBQUFBLEtBQS9COztBQUVBLFdBQU8sRUFBUDtBQUNEOztBQUVEOzs7Ozs7Ozs7OEJBS1U7QUFDUlAsUUFBRSxLQUFLTSxlQUFQLEVBQXdCRyxNQUF4Qjs7QUFFQSxVQUFJVCxPQUFLLEtBQUtLLGVBQVYsRUFBNkJLLEdBQTdCLE9BQXVDLFlBQTNDLEVBQXlEO0FBQ3ZEVixVQUFFLEtBQUtNLGVBQVAsRUFBd0JLLE9BQXhCO0FBQ0Q7QUFDRjs7Ozs7O2tCQXJCa0JULGdDOzs7Ozs7Ozs7Ozs7Ozs7Ozs7QUM5QnJCOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBeUJBLElBQU1GLElBQUlDLE9BQU9ELENBQWpCOztBQUVBOzs7O0lBR3FCWSw2QjtBQUNuQix5Q0FBWUMsZ0JBQVosRUFBOEJULGNBQTlCLEVBQThDO0FBQUE7O0FBQUE7O0FBQzVDLFNBQUtDLGVBQUwsR0FBdUJRLGdCQUF2QjtBQUNBLFNBQUtQLGVBQUwsR0FBdUJGLGNBQXZCO0FBQ0EsU0FBS0csT0FBTDtBQUNBUCxNQUFFYSxnQkFBRixFQUFvQkwsRUFBcEIsQ0FBdUIsUUFBdkIsRUFBaUM7QUFBQSxhQUFNLE1BQUtELE9BQUwsRUFBTjtBQUFBLEtBQWpDOztBQUVBLFdBQU8sRUFBUDtBQUNEOztBQUVEOzs7Ozs7Ozs7OEJBS1U7QUFDUixVQUFNTyxjQUFjZCxPQUFLLEtBQUtLLGVBQVYsRUFBNkJVLEVBQTdCLENBQWdDLFVBQWhDLENBQXBCOztBQUVBZixRQUFFLEtBQUtNLGVBQVAsRUFBd0JVLElBQXhCLENBQTZCLFVBQTdCLEVBQXlDRixXQUF6QztBQUNEOzs7Ozs7a0JBbkJrQkYsNkI7Ozs7Ozs7Ozs7QUNMckI7Ozs7QUFHQTs7Ozs7O0FBNUJBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBK0JBLElBQU1aLElBQUlDLE9BQU9ELENBQWpCOztBQUVBQSxFQUFFLFlBQU07QUFDTixNQUFJWSx1Q0FBSixDQUFrQyx5Q0FBbEMsRUFBNkUsMkJBQTdFO0FBQ0EsTUFBSVYsMENBQUosQ0FBcUMsMkJBQXJDLEVBQWtFLHdCQUFsRTtBQUNELENBSEQsRSIsImZpbGUiOiJjYXRhbG9nX3ByaWNlX3J1bGVfZm9ybS5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBpZGVudGl0eSBmdW5jdGlvbiBmb3IgY2FsbGluZyBoYXJtb255IGltcG9ydHMgd2l0aCB0aGUgY29ycmVjdCBjb250ZXh0XG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmkgPSBmdW5jdGlvbih2YWx1ZSkgeyByZXR1cm4gdmFsdWU7IH07XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDMxNCk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gd2VicGFjay9ib290c3RyYXAgNWQ5OTkwOTRkMTFhZWZmMGI1ODIiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogU2hvd3MvaGlkZXMgJ2luY2x1ZGVfdGF4JyBmaWVsZCBkZXBlbmRpbmcgZnJvbSAncmVkdWN0aW9uX3R5cGUnIGZpZWxkIHZhbHVlXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEluY2x1ZGVUYXhGaWVsZFZpc2liaWxpdHlIYW5kbGVyIHtcbiAgY29uc3RydWN0b3Ioc291cmNlU2VsZWN0b3IsIHRhcmdldFNlbGVjdG9yKSB7XG4gICAgdGhpcy4kc291cmNlU2VsZWN0b3IgPSBzb3VyY2VTZWxlY3RvcjtcbiAgICB0aGlzLiR0YXJnZXRTZWxlY3RvciA9IHRhcmdldFNlbGVjdG9yO1xuICAgIHRoaXMuX2hhbmRsZSgpO1xuICAgICQoc291cmNlU2VsZWN0b3IpLm9uKCdjaGFuZ2UnLCAoKSA9PiB0aGlzLl9oYW5kbGUoKSk7XG5cbiAgICByZXR1cm4ge307XG4gIH1cblxuICAvKipcbiAgICogV2hlbiBzb3VyY2UgdmFsdWUgaXMgJ3BlcmNlbnRhZ2UnLCB0YXJnZXQgZmllbGQgaXMgc2hvd24sIGVsc2UgaGlkZGVuXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGFuZGxlKCkge1xuICAgICQodGhpcy4kdGFyZ2V0U2VsZWN0b3IpLmZhZGVJbigpO1xuXG4gICAgaWYgKCQoYCR7dGhpcy4kc291cmNlU2VsZWN0b3J9YCkudmFsKCkgPT09ICdwZXJjZW50YWdlJykge1xuICAgICAgJCh0aGlzLiR0YXJnZXRTZWxlY3RvcikuZmFkZU91dCgpO1xuICAgIH1cbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvY2F0YWxvZy1wcmljZS1ydWxlL2Zvcm0vaW5jbHVkZS10YXgtZmllbGQtdmlzaWJpbGl0eS1oYW5kbGVyLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIEVuYWJsZXMvZGlzYWJsZXMgJ3ByaWNlJyBmaWVsZCBkZXBlbmRpbmcgZnJvbSAnbGVhdmVfaW5pdGlhbF9wcmljZScgZmllbGQgY2hlY2tib3ggdmFsdWVcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgUHJpY2VGaWVsZEF2YWlsYWJpbGl0eUhhbmRsZXIge1xuICBjb25zdHJ1Y3RvcihjaGVja2JveFNlbGVjdG9yLCB0YXJnZXRTZWxlY3Rvcikge1xuICAgIHRoaXMuJHNvdXJjZVNlbGVjdG9yID0gY2hlY2tib3hTZWxlY3RvcjtcbiAgICB0aGlzLiR0YXJnZXRTZWxlY3RvciA9IHRhcmdldFNlbGVjdG9yO1xuICAgIHRoaXMuX2hhbmRsZSgpO1xuICAgICQoY2hlY2tib3hTZWxlY3Rvcikub24oJ2NoYW5nZScsICgpID0+IHRoaXMuX2hhbmRsZSgpKTtcblxuICAgIHJldHVybiB7fTtcbiAgfVxuXG4gIC8qKlxuICAgKiBXaGVuIGNoZWNrYm94IHZhbHVlIGlzIDEsIHRhcmdldCBmaWVsZCBpcyBkaXNhYmxlZCwgZWxzZSBlbmFibGVkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGFuZGxlKCkge1xuICAgIGNvbnN0IGNoZWNrYm94VmFsID0gJChgJHt0aGlzLiRzb3VyY2VTZWxlY3Rvcn1gKS5pcygnOmNoZWNrZWQnKTtcblxuICAgICQodGhpcy4kdGFyZ2V0U2VsZWN0b3IpLnByb3AoJ2Rpc2FibGVkJywgY2hlY2tib3hWYWwpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9jYXRhbG9nLXByaWNlLXJ1bGUvZm9ybS9wcmljZS1maWVsZC1hdmFpbGFiaWxpdHktaGFuZGxlci5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBQcmljZUZpZWxkQXZhaWxhYmlsaXR5SGFuZGxlclxuICBmcm9tICcuL3ByaWNlLWZpZWxkLWF2YWlsYWJpbGl0eS1oYW5kbGVyJztcblxuaW1wb3J0IEluY2x1ZGVUYXhGaWVsZFZpc2liaWxpdHlIYW5kbGVyXG4gIGZyb20gJy4vaW5jbHVkZS10YXgtZmllbGQtdmlzaWJpbGl0eS1oYW5kbGVyJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4kKCgpID0+IHtcbiAgbmV3IFByaWNlRmllbGRBdmFpbGFiaWxpdHlIYW5kbGVyKCcjY2F0YWxvZ19wcmljZV9ydWxlX2xlYXZlX2luaXRpYWxfcHJpY2UnLCAnI2NhdGFsb2dfcHJpY2VfcnVsZV9wcmljZScpO1xuICBuZXcgSW5jbHVkZVRheEZpZWxkVmlzaWJpbGl0eUhhbmRsZXIoJy5qcy1yZWR1Y3Rpb24tdHlwZS1zb3VyY2UnLCAnLmpzLWluY2x1ZGUtdGF4LXRhcmdldCcpO1xufSk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9jYXRhbG9nLXByaWNlLXJ1bGUvZm9ybS9pbmRleC5qcyJdLCJzb3VyY2VSb290IjoiIn0=