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
/******/ 	return __webpack_require__(__webpack_require__.s = 362);
/******/ })
/************************************************************************/
/******/ ({

/***/ 272:
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
  manufacturerAddressCountrySelect: '#manufacturer_address_id_country',
  manufacturerAddressStateSelect: '#manufacturer_address_id_state',
  manufacturerAddressStateBlock: '.js-manufacturer-address-state'
};

/***/ }),

/***/ 362:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _countryStateSelectionToggler = __webpack_require__(62);

var _countryStateSelectionToggler2 = _interopRequireDefault(_countryStateSelectionToggler);

var _manufacturerAddressMap = __webpack_require__(272);

var _manufacturerAddressMap2 = _interopRequireDefault(_manufacturerAddressMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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

$(document).ready(function () {
  new _countryStateSelectionToggler2.default(_manufacturerAddressMap2.default.manufacturerAddressCountrySelect, _manufacturerAddressMap2.default.manufacturerAddressStateSelect, _manufacturerAddressMap2.default.manufacturerAddressStateBlock);
});

/***/ }),

/***/ 62:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

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

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgMWU2NjI2MzkwMGU5NjZkZmJiZjA/ODU5MCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL21hbnVmYWN0dXJlci9tYW51ZmFjdHVyZXItYWRkcmVzcy1tYXAuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvbWFudWZhY3R1cmVyL21hbnVmYWN0dXJlcl9hZGRyZXNzX2Zvcm0uanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9jb3VudHJ5LXN0YXRlLXNlbGVjdGlvbi10b2dnbGVyLmpzPzc2NzAiXSwibmFtZXMiOlsibWFudWZhY3R1cmVyQWRkcmVzc0NvdW50cnlTZWxlY3QiLCJtYW51ZmFjdHVyZXJBZGRyZXNzU3RhdGVTZWxlY3QiLCJtYW51ZmFjdHVyZXJBZGRyZXNzU3RhdGVCbG9jayIsIiQiLCJ3aW5kb3ciLCJkb2N1bWVudCIsInJlYWR5IiwiQ291bnRyeVN0YXRlU2VsZWN0aW9uVG9nZ2xlciIsIk1hbnVmYWN0dXJlckFkZHJlc3NNYXAiLCJjb3VudHJ5SW5wdXRTZWxlY3RvciIsImNvdW50cnlTdGF0ZVNlbGVjdG9yIiwic3RhdGVTZWxlY3Rpb25CbG9ja1NlbGVjdG9yIiwiJHN0YXRlU2VsZWN0aW9uQmxvY2siLCIkY291bnRyeVN0YXRlU2VsZWN0b3IiLCIkY291bnRyeUlucHV0Iiwib24iLCJfdG9nZ2xlIiwiaXNGaXJzdFRvZ2dsZSIsImFqYXgiLCJ1cmwiLCJkYXRhIiwibWV0aG9kIiwiZGF0YVR5cGUiLCJpZF9jb3VudHJ5IiwidmFsIiwidGhlbiIsInJlc3BvbnNlIiwic3RhdGVzIiwibGVuZ3RoIiwiZmFkZU91dCIsImZhZGVJbiIsImVtcHR5IiwiX3RoaXMiLCJlYWNoIiwiaW5kZXgiLCJ2YWx1ZSIsImFwcGVuZCIsImF0dHIiLCJ0ZXh0IiwiY2F0Y2giLCJyZXNwb25zZUpTT04iLCJzaG93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QUE1QyxDQUFpREosS0FBakQsQ0FBbkM7QUFDRCxXQUZEO0FBR0Q7QUFDRixPQXZCRCxFQXVCR0ssS0F2QkgsQ0F1QlMsVUFBQ2IsUUFBRCxFQUFjO0FBQ3JCLFlBQUksT0FBT0EsU0FBU2MsWUFBaEIsS0FBaUMsV0FBckMsRUFBa0Q7QUFDaERDLDJCQUFpQmYsU0FBU2MsWUFBVCxDQUFzQkUsT0FBdkM7QUFDRDtBQUNGLE9BM0JEO0FBNEJEOzs7Ozs7a0JBaERrQm5DLDRCIiwiZmlsZSI6Im1hbnVmYWN0dXJlcl9hZGRyZXNzX2Zvcm0uYnVuZGxlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gaWRlbnRpdHkgZnVuY3Rpb24gZm9yIGNhbGxpbmcgaGFybW9ueSBpbXBvcnRzIHdpdGggdGhlIGNvcnJlY3QgY29udGV4dFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5pID0gZnVuY3Rpb24odmFsdWUpIHsgcmV0dXJuIHZhbHVlOyB9O1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHtcbiBcdFx0XHRcdGNvbmZpZ3VyYWJsZTogZmFsc2UsXG4gXHRcdFx0XHRlbnVtZXJhYmxlOiB0cnVlLFxuIFx0XHRcdFx0Z2V0OiBnZXR0ZXJcbiBcdFx0XHR9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCJcIjtcblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSAzNjIpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIHdlYnBhY2svYm9vdHN0cmFwIDFlNjYyNjM5MDBlOTY2ZGZiYmYwIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuZXhwb3J0IGRlZmF1bHQge1xuICBtYW51ZmFjdHVyZXJBZGRyZXNzQ291bnRyeVNlbGVjdDogJyNtYW51ZmFjdHVyZXJfYWRkcmVzc19pZF9jb3VudHJ5JyxcbiAgbWFudWZhY3R1cmVyQWRkcmVzc1N0YXRlU2VsZWN0OiAnI21hbnVmYWN0dXJlcl9hZGRyZXNzX2lkX3N0YXRlJyxcbiAgbWFudWZhY3R1cmVyQWRkcmVzc1N0YXRlQmxvY2s6ICcuanMtbWFudWZhY3R1cmVyLWFkZHJlc3Mtc3RhdGUnLFxufTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL21hbnVmYWN0dXJlci9tYW51ZmFjdHVyZXItYWRkcmVzcy1tYXAuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgQ291bnRyeVN0YXRlU2VsZWN0aW9uVG9nZ2xlciBmcm9tICcuLi8uLi9jb21wb25lbnRzL2NvdW50cnktc3RhdGUtc2VsZWN0aW9uLXRvZ2dsZXInO1xuaW1wb3J0IE1hbnVmYWN0dXJlckFkZHJlc3NNYXAgZnJvbSAnLi9tYW51ZmFjdHVyZXItYWRkcmVzcy1tYXAnO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbiQoZG9jdW1lbnQpLnJlYWR5KCgpID0+IHtcbiAgbmV3IENvdW50cnlTdGF0ZVNlbGVjdGlvblRvZ2dsZXIoXG4gICAgTWFudWZhY3R1cmVyQWRkcmVzc01hcC5tYW51ZmFjdHVyZXJBZGRyZXNzQ291bnRyeVNlbGVjdCxcbiAgICBNYW51ZmFjdHVyZXJBZGRyZXNzTWFwLm1hbnVmYWN0dXJlckFkZHJlc3NTdGF0ZVNlbGVjdCxcbiAgICBNYW51ZmFjdHVyZXJBZGRyZXNzTWFwLm1hbnVmYWN0dXJlckFkZHJlc3NTdGF0ZUJsb2NrXG4gICk7XG59KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL21hbnVmYWN0dXJlci9tYW51ZmFjdHVyZXJfYWRkcmVzc19mb3JtLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIERpc3BsYXlzLCBmaWxscyBvciBoaWRlcyBTdGF0ZSBzZWxlY3Rpb24gYmxvY2sgZGVwZW5kaW5nIG9uIHNlbGVjdGVkIGNvdW50cnkuXG4gKlxuICogVXNhZ2U6XG4gKlxuICogPCEtLSBDb3VudHJ5IHNlbGVjdCBtdXN0IGhhdmUgdW5pcXVlIGlkZW50aWZpZXIgJiB1cmwgZm9yIHN0YXRlcyBBUEkgLS0+XG4gKiA8c2VsZWN0IG5hbWU9XCJpZF9jb3VudHJ5XCIgaWQ9XCJpZF9jb3VudHJ5XCIgc3RhdGVzLXVybD1cInBhdGgvdG8vc3RhdGVzL2FwaVwiPlxuICogICAuLi5cbiAqIDwvc2VsZWN0PlxuICpcbiAqIDwhLS0gSWYgc2VsZWN0ZWQgY291bnRyeSBkb2VzIG5vdCBoYXZlIHN0YXRlcywgdGhlbiB0aGlzIGJsb2NrIHdpbGwgYmUgaGlkZGVuIC0tPlxuICogPGRpdiBjbGFzcz1cImpzLXN0YXRlLXNlbGVjdGlvbi1ibG9ja1wiPlxuICogICA8c2VsZWN0IG5hbWU9XCJpZF9zdGF0ZVwiPlxuICogICAgIC4uLlxuICogICA8L3NlbGVjdD5cbiAqIDwvZGl2PlxuICpcbiAqIEluIEpTOlxuICpcbiAqIG5ldyBDb3VudHJ5U3RhdGVTZWxlY3Rpb25Ub2dnbGVyKCcjaWRfY291bnRyeScsICcjaWRfc3RhdGUnLCAnLmpzLXN0YXRlLXNlbGVjdGlvbi1ibG9jaycpO1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBDb3VudHJ5U3RhdGVTZWxlY3Rpb25Ub2dnbGVyIHtcbiAgY29uc3RydWN0b3IoY291bnRyeUlucHV0U2VsZWN0b3IsIGNvdW50cnlTdGF0ZVNlbGVjdG9yLCBzdGF0ZVNlbGVjdGlvbkJsb2NrU2VsZWN0b3IpIHtcbiAgICB0aGlzLiRzdGF0ZVNlbGVjdGlvbkJsb2NrID0gJChzdGF0ZVNlbGVjdGlvbkJsb2NrU2VsZWN0b3IpO1xuICAgIHRoaXMuJGNvdW50cnlTdGF0ZVNlbGVjdG9yID0gJChjb3VudHJ5U3RhdGVTZWxlY3Rvcik7XG4gICAgdGhpcy4kY291bnRyeUlucHV0ID0gJChjb3VudHJ5SW5wdXRTZWxlY3Rvcik7XG5cbiAgICB0aGlzLiRjb3VudHJ5SW5wdXQub24oJ2NoYW5nZScsICgpID0+IHRoaXMuX3RvZ2dsZSgpKTtcblxuICAgIC8vIHRvZ2dsZSBvbiBwYWdlIGxvYWRcbiAgICB0aGlzLl90b2dnbGUodHJ1ZSk7XG5cbiAgICByZXR1cm4ge307XG4gIH1cblxuICAvKipcbiAgICogVG9nZ2xlcyBTdGF0ZSBzZWxlY3Rpb25cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF90b2dnbGUoaXNGaXJzdFRvZ2dsZSA9IGZhbHNlKSB7XG4gICAgJC5hamF4KHtcbiAgICAgIHVybDogdGhpcy4kY291bnRyeUlucHV0LmRhdGEoJ3N0YXRlcy11cmwnKSxcbiAgICAgIG1ldGhvZDogJ0dFVCcsXG4gICAgICBkYXRhVHlwZTogJ2pzb24nLFxuICAgICAgZGF0YToge1xuICAgICAgICBpZF9jb3VudHJ5OiB0aGlzLiRjb3VudHJ5SW5wdXQudmFsKCksXG4gICAgICB9XG4gICAgfSkudGhlbigocmVzcG9uc2UpID0+IHtcbiAgICAgIGlmIChyZXNwb25zZS5zdGF0ZXMubGVuZ3RoID09PSAwKSB7XG4gICAgICAgIHRoaXMuJHN0YXRlU2VsZWN0aW9uQmxvY2suZmFkZU91dCgpO1xuXG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cblxuICAgICAgdGhpcy4kc3RhdGVTZWxlY3Rpb25CbG9jay5mYWRlSW4oKTtcblxuICAgICAgaWYgKGlzRmlyc3RUb2dnbGUgPT09IGZhbHNlKSB7XG4gICAgICAgIHRoaXMuJGNvdW50cnlTdGF0ZVNlbGVjdG9yLmVtcHR5KCk7XG4gICAgICAgIHZhciBfdGhpcyA9IHRoaXM7XG4gICAgICAgICQuZWFjaChyZXNwb25zZS5zdGF0ZXMsIGZ1bmN0aW9uIChpbmRleCwgdmFsdWUpIHtcbiAgICAgICAgICBfdGhpcy4kY291bnRyeVN0YXRlU2VsZWN0b3IuYXBwZW5kKCQoJzxvcHRpb24+PC9vcHRpb24+JykuYXR0cigndmFsdWUnLCB2YWx1ZSkudGV4dChpbmRleCkpO1xuICAgICAgICB9KVxuICAgICAgfVxuICAgIH0pLmNhdGNoKChyZXNwb25zZSkgPT4ge1xuICAgICAgaWYgKHR5cGVvZiByZXNwb25zZS5yZXNwb25zZUpTT04gIT09ICd1bmRlZmluZWQnKSB7XG4gICAgICAgIHNob3dFcnJvck1lc3NhZ2UocmVzcG9uc2UucmVzcG9uc2VKU09OLm1lc3NhZ2UpO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2NvdW50cnktc3RhdGUtc2VsZWN0aW9uLXRvZ2dsZXIuanMiXSwic291cmNlUm9vdCI6IiJ9