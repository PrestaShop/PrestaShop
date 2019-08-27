window["customer_address_form"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 313);
/******/ })
/************************************************************************/
/******/ ({

/***/ 249:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function($) {

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

var _addressFormMap = __webpack_require__(312);

var _addressFormMap2 = _interopRequireDefault(_addressFormMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * Class responsible for javascript actions in customer address add/edit form.
 */
var CustomerAddressForm = function () {
  function CustomerAddressForm() {
    _classCallCheck(this, CustomerAddressForm);

    this.countrySelect = _addressFormMap2.default.countrySelect;
    this.stateSelect = _addressFormMap2.default.stateSelect;
    this.stateFormRowSelect = _addressFormMap2.default.stateFormRowSelect;
    this.emailInput = _addressFormMap2.default.customerEmail;

    this.firstName = _addressFormMap2.default.firstName;
    this.lastName = _addressFormMap2.default.lastName;
    this.company = _addressFormMap2.default.company;

    this._initEvents();

    return {};
  }

  /**
   * Initialize page's events.
   *
   * @private
   */


  _createClass(CustomerAddressForm, [{
    key: '_initEvents',
    value: function _initEvents() {
      var _this = this;

      var $countryDropdown = $(this.countrySelect);
      var $emailInput = $(this.emailInput);

      // Initial check for country states. Should be handled in backend
      this._handleCountryChange();

      $countryDropdown.on('change', function () {
        return _this._handleCountryChange();
      });
      $emailInput.on('blur', function (event) {
        return _this._handleEmailChange(event);
      });
    }

    /**
     * Hide state select if country doesnt have states, show it otherwise and fill with data.
     *
     * @private
     */

  }, {
    key: '_handleCountryChange',
    value: function _handleCountryChange() {
      var countryDropdown = $(this.countrySelect);
      var getCountryStateUrl = countryDropdown.data('states-url');
      var stateDropdown = $(this.stateSelect);
      var stateFormRowSelect = $(this.stateFormRowSelect);

      $.ajax({
        url: getCountryStateUrl,
        method: 'GET',
        dataType: 'json',
        data: {
          id_country: countryDropdown.val()
        }
      }).then(function (response) {
        if (response.states.length === 0) {
          stateFormRowSelect.fadeOut();
          stateDropdown.attr('disabled', 'disabled');

          return;
        }

        stateDropdown.removeAttr('disabled');
        stateFormRowSelect.fadeIn();

        stateDropdown.empty();
        $.each(response.states, function (index, value) {
          stateDropdown.append($('<option></option>').attr('value', value).text(index));
        });
      }).catch(function (response) {
        if (typeof response.responseJSON !== 'undefined') {
          showErrorMessage(response.responseJSON.message);
        }
      });
    }

    /**
     * Handles email change event to get customer data for customer fields
     *
     * @param event
     *
     * @private
     */

  }, {
    key: '_handleEmailChange',
    value: function _handleEmailChange(event) {
      var _this2 = this;

      var emailInput = $(event.target);
      var getFillCustomerDataUrl = emailInput.data('customer-information-url');
      var email = emailInput.val();

      if (email.length > 5) {
        $.ajax({
          url: getFillCustomerDataUrl,
          data: {
            email: email
          },
          dataType: 'json'
        }).then(function (response) {
          _this2._setCustomerInformation(response);
        });
      }
    }

    /**
     * Fills customer fields with response data
     *
     * @param data
     *
     * @private
     */

  }, {
    key: '_setCustomerInformation',
    value: function _setCustomerInformation(data) {
      var firstNameSelector = $(this.firstName);
      var lastNameSelector = $(this.lastName);
      var companySelector = $(this.company);

      if (0 > data.first_name.length) {
        firstNameSelector.val(data.first_name);
      }

      if (0 > data.last_name.length) {
        lastNameSelector.val(data.last_name);
      }

      if (0 > data.company.length) {
        companySelector.val(data.company);
      }
    }
  }]);

  return CustomerAddressForm;
}();

exports.default = CustomerAddressForm;
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(4)))

/***/ }),

/***/ 312:
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
 * Defines all selectors that are used in customers address add/edit form.
 */
exports.default = {
  firstName: '#customer_address_first_name',
  lastName: '#customer_address_last_name',
  company: '#customer_address_company',
  countrySelect: '#customer_address_id_country',
  stateSelect: '#customer_address_id_state',
  customerEmail: '#customer_address_customer_email',
  stateFormRowSelect: '.js-address-state-select'
};

/***/ }),

/***/ 313:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function($) {

var _CustomerAddressForm = __webpack_require__(249);

var _CustomerAddressForm2 = _interopRequireDefault(_CustomerAddressForm);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

$(function () {
  new _CustomerAddressForm2.default();
}); /**
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
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(4)))

/***/ }),

/***/ 4:
/***/ (function(module, exports) {

(function() { module.exports = window["jQuery"]; }());

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNjYyZmE5ODJiYjgzNjIwMDM1M2IiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvYWRkcmVzcy9DdXN0b21lckFkZHJlc3NGb3JtLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL2FkZHJlc3MvYWRkcmVzcy1mb3JtLW1hcC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9hZGRyZXNzL2Zvcm0uanMiLCJ3ZWJwYWNrOi8vL2V4dGVybmFsIFwialF1ZXJ5XCIiXSwibmFtZXMiOlsiQ3VzdG9tZXJBZGRyZXNzRm9ybSIsImNvdW50cnlTZWxlY3QiLCJhZGRyZXNzRm9ybU1hcCIsInN0YXRlU2VsZWN0Iiwic3RhdGVGb3JtUm93U2VsZWN0IiwiZW1haWxJbnB1dCIsImN1c3RvbWVyRW1haWwiLCJmaXJzdE5hbWUiLCJsYXN0TmFtZSIsImNvbXBhbnkiLCJfaW5pdEV2ZW50cyIsIiRjb3VudHJ5RHJvcGRvd24iLCIkIiwiJGVtYWlsSW5wdXQiLCJfaGFuZGxlQ291bnRyeUNoYW5nZSIsIm9uIiwiZXZlbnQiLCJfaGFuZGxlRW1haWxDaGFuZ2UiLCJjb3VudHJ5RHJvcGRvd24iLCJnZXRDb3VudHJ5U3RhdGVVcmwiLCJkYXRhIiwic3RhdGVEcm9wZG93biIsImFqYXgiLCJ1cmwiLCJtZXRob2QiLCJkYXRhVHlwZSIsImlkX2NvdW50cnkiLCJ2YWwiLCJ0aGVuIiwicmVzcG9uc2UiLCJzdGF0ZXMiLCJsZW5ndGgiLCJmYWRlT3V0IiwiYXR0ciIsInJlbW92ZUF0dHIiLCJmYWRlSW4iLCJlbXB0eSIsImVhY2giLCJpbmRleCIsInZhbHVlIiwiYXBwZW5kIiwidGV4dCIsImNhdGNoIiwicmVzcG9uc2VKU09OIiwic2hvd0Vycm9yTWVzc2FnZSIsIm1lc3NhZ2UiLCJ0YXJnZXQiLCJnZXRGaWxsQ3VzdG9tZXJEYXRhVXJsIiwiZW1haWwiLCJfc2V0Q3VzdG9tZXJJbmZvcm1hdGlvbiIsImZpcnN0TmFtZVNlbGVjdG9yIiwibGFzdE5hbWVTZWxlY3RvciIsImNvbXBhbnlTZWxlY3RvciIsImZpcnN0X25hbWUiLCJsYXN0X25hbWUiXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7OztBQUdBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBLG1EQUEyQyxjQUFjOztBQUV6RDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGFBQUs7QUFDTDtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLG1DQUEyQiwwQkFBMEIsRUFBRTtBQUN2RCx5Q0FBaUMsZUFBZTtBQUNoRDtBQUNBO0FBQ0E7O0FBRUE7QUFDQSw4REFBc0QsK0RBQStEOztBQUVySDtBQUNBOztBQUVBO0FBQ0E7Ozs7Ozs7Ozs7Ozs7OztxakJDaEVBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBeUJBOzs7Ozs7OztBQUVBOzs7SUFHcUJBLG1CO0FBQ25CLGlDQUFjO0FBQUE7O0FBQ1osU0FBS0MsYUFBTCxHQUFxQkMseUJBQWVELGFBQXBDO0FBQ0EsU0FBS0UsV0FBTCxHQUFtQkQseUJBQWVDLFdBQWxDO0FBQ0EsU0FBS0Msa0JBQUwsR0FBMEJGLHlCQUFlRSxrQkFBekM7QUFDQSxTQUFLQyxVQUFMLEdBQWtCSCx5QkFBZUksYUFBakM7O0FBRUEsU0FBS0MsU0FBTCxHQUFpQkwseUJBQWVLLFNBQWhDO0FBQ0EsU0FBS0MsUUFBTCxHQUFnQk4seUJBQWVNLFFBQS9CO0FBQ0EsU0FBS0MsT0FBTCxHQUFlUCx5QkFBZU8sT0FBOUI7O0FBRUEsU0FBS0MsV0FBTDs7QUFFQSxXQUFPLEVBQVA7QUFDRDs7QUFFRDs7Ozs7Ozs7O2tDQUtjO0FBQUE7O0FBQ1osVUFBTUMsbUJBQW1CQyxFQUFFLEtBQUtYLGFBQVAsQ0FBekI7QUFDQSxVQUFNWSxjQUFjRCxFQUFFLEtBQUtQLFVBQVAs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RUFBK0I7QUFDN0JvQix5QkFBaUJ4QixHQUFqQixDQUFxQlAsS0FBS2tDLFNBQTFCO0FBQ0Q7O0FBRUQsVUFBSSxJQUFJbEMsS0FBS1gsT0FBTCxDQUFhc0IsTUFBckIsRUFBNkI7QUFDM0JxQix3QkFBZ0J6QixHQUFoQixDQUFvQlAsS0FBS1gsT0FBekI7QUFDRDtBQUNGOzs7Ozs7a0JBeEhrQlQsbUI7Ozs7Ozs7Ozs7Ozs7O0FDOUJyQjs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQXlCQTs7O2tCQUdlO0FBQ2JPLGFBQVcsOEJBREU7QUFFYkMsWUFBVSw2QkFGRztBQUdiQyxXQUFTLDJCQUhJO0FBSWJSLGlCQUFlLDhCQUpGO0FBS2JFLGVBQWEsNEJBTEE7QUFNYkcsaUJBQWUsa0NBTkY7QUFPYkYsc0JBQW9CO0FBUFAsQzs7Ozs7Ozs7OztBQ0hmOzs7Ozs7QUFFQVEsRUFBRSxZQUFNO0FBQ04sTUFBSVosNkJBQUo7QUFDRCxDQUZELEUsQ0EzQkE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUNBQSxhQUFhLG1DQUFtQyxFQUFFLEkiLCJmaWxlIjoiY3VzdG9tZXJfYWRkcmVzc19mb3JtLmJ1bmRsZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGlkZW50aXR5IGZ1bmN0aW9uIGZvciBjYWxsaW5nIGhhcm1vbnkgaW1wb3J0cyB3aXRoIHRoZSBjb3JyZWN0IGNvbnRleHRcbiBcdF9fd2VicGFja19yZXF1aXJlX18uaSA9IGZ1bmN0aW9uKHZhbHVlKSB7IHJldHVybiB2YWx1ZTsgfTtcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7XG4gXHRcdFx0XHRjb25maWd1cmFibGU6IGZhbHNlLFxuIFx0XHRcdFx0ZW51bWVyYWJsZTogdHJ1ZSxcbiBcdFx0XHRcdGdldDogZ2V0dGVyXG4gXHRcdFx0fSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gMzEzKTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyB3ZWJwYWNrL2Jvb3RzdHJhcCA2NjJmYTk4MmJiODM2MjAwMzUzYiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBhZGRyZXNzRm9ybU1hcCBmcm9tIFwiLi9hZGRyZXNzLWZvcm0tbWFwXCI7XG5cbi8qKlxuICogQ2xhc3MgcmVzcG9uc2libGUgZm9yIGphdmFzY3JpcHQgYWN0aW9ucyBpbiBjdXN0b21lciBhZGRyZXNzIGFkZC9lZGl0IGZvcm0uXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEN1c3RvbWVyQWRkcmVzc0Zvcm0ge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICB0aGlzLmNvdW50cnlTZWxlY3QgPSBhZGRyZXNzRm9ybU1hcC5jb3VudHJ5U2VsZWN0O1xuICAgIHRoaXMuc3RhdGVTZWxlY3QgPSBhZGRyZXNzRm9ybU1hcC5zdGF0ZVNlbGVjdDtcbiAgICB0aGlzLnN0YXRlRm9ybVJvd1NlbGVjdCA9IGFkZHJlc3NGb3JtTWFwLnN0YXRlRm9ybVJvd1NlbGVjdDtcbiAgICB0aGlzLmVtYWlsSW5wdXQgPSBhZGRyZXNzRm9ybU1hcC5jdXN0b21lckVtYWlsO1xuXG4gICAgdGhpcy5maXJzdE5hbWUgPSBhZGRyZXNzRm9ybU1hcC5maXJzdE5hbWU7XG4gICAgdGhpcy5sYXN0TmFtZSA9IGFkZHJlc3NGb3JtTWFwLmxhc3ROYW1lO1xuICAgIHRoaXMuY29tcGFueSA9IGFkZHJlc3NGb3JtTWFwLmNvbXBhbnk7XG5cbiAgICB0aGlzLl9pbml0RXZlbnRzKCk7XG5cbiAgICByZXR1cm4ge307XG4gIH1cblxuICAvKipcbiAgICogSW5pdGlhbGl6ZSBwYWdlJ3MgZXZlbnRzLlxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2luaXRFdmVudHMoKSB7XG4gICAgY29uc3QgJGNvdW50cnlEcm9wZG93biA9ICQodGhpcy5jb3VudHJ5U2VsZWN0KTtcbiAgICBjb25zdCAkZW1haWxJbnB1dCA9ICQodGhpcy5lbWFpbElucHV0KTtcblxuICAgIC8vIEluaXRpYWwgY2hlY2sgZm9yIGNvdW50cnkgc3RhdGVzLiBTaG91bGQgYmUgaGFuZGxlZCBpbiBiYWNrZW5kXG4gICAgdGhpcy5faGFuZGxlQ291bnRyeUNoYW5nZSgpO1xuXG4gICAgJGNvdW50cnlEcm9wZG93bi5vbignY2hhbmdlJywgKCkgPT4gdGhpcy5faGFuZGxlQ291bnRyeUNoYW5nZSgpKTtcbiAgICAkZW1haWxJbnB1dC5vbignYmx1cicsIChldmVudCkgPT4gdGhpcy5faGFuZGxlRW1haWxDaGFuZ2UoZXZlbnQpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIaWRlIHN0YXRlIHNlbGVjdCBpZiBjb3VudHJ5IGRvZXNudCBoYXZlIHN0YXRlcywgc2hvdyBpdCBvdGhlcndpc2UgYW5kIGZpbGwgd2l0aCBkYXRhLlxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hhbmRsZUNvdW50cnlDaGFuZ2UoKSB7XG4gICAgY29uc3QgY291bnRyeURyb3Bkb3duID0gJCh0aGlzLmNvdW50cnlTZWxlY3QpO1xuICAgIGNvbnN0IGdldENvdW50cnlTdGF0ZVVybCA9IGNvdW50cnlEcm9wZG93bi5kYXRhKCdzdGF0ZXMtdXJsJyk7XG4gICAgY29uc3Qgc3RhdGVEcm9wZG93biA9ICQodGhpcy5zdGF0ZVNlbGVjdCk7XG4gICAgY29uc3Qgc3RhdGVGb3JtUm93U2VsZWN0ID0gJCh0aGlzLnN0YXRlRm9ybVJvd1NlbGVjdCk7XG5cbiAgICAkLmFqYXgoe1xuICAgICAgdXJsOiBnZXRDb3VudHJ5U3RhdGVVcmwsXG4gICAgICBtZXRob2Q6ICdHRVQnLFxuICAgICAgZGF0YVR5cGU6ICdqc29uJyxcbiAgICAgIGRhdGE6IHtcbiAgICAgICAgaWRfY291bnRyeTogY291bnRyeURyb3Bkb3duLnZhbCgpLFxuICAgICAgfVxuICAgIH0pLnRoZW4oKHJlc3BvbnNlKSA9PiB7XG4gICAgICBpZiAocmVzcG9uc2Uuc3RhdGVzLmxlbmd0aCA9PT0gMCkge1xuICAgICAgICBzdGF0ZUZvcm1Sb3dTZWxlY3QuZmFkZU91dCgpO1xuICAgICAgICBzdGF0ZURyb3Bkb3duLmF0dHIoJ2Rpc2FibGVkJywgJ2Rpc2FibGVkJyk7XG5cbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICBzdGF0ZURyb3Bkb3duLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XG4gICAgICBzdGF0ZUZvcm1Sb3dTZWxlY3QuZmFkZUluKCk7XG5cbiAgICAgIHN0YXRlRHJvcGRvd24uZW1wdHkoKTtcbiAgICAgICQuZWFjaChyZXNwb25zZS5zdGF0ZXMsIGZ1bmN0aW9uIChpbmRleCwgdmFsdWUpIHtcbiAgICAgICAgc3RhdGVEcm9wZG93bi5hcHBlbmQoJCgnPG9wdGlvbj48L29wdGlvbj4nKS5hdHRyKCd2YWx1ZScsIHZhbHVlKS50ZXh0KGluZGV4KSk7XG4gICAgICB9KTtcbiAgICB9KS5jYXRjaCgocmVzcG9uc2UpID0+IHtcbiAgICAgIGlmICh0eXBlb2YgcmVzcG9uc2UucmVzcG9uc2VKU09OICE9PSAndW5kZWZpbmVkJykge1xuICAgICAgICBzaG93RXJyb3JNZXNzYWdlKHJlc3BvbnNlLnJlc3BvbnNlSlNPTi5tZXNzYWdlKTtcbiAgICAgIH1cbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGVzIGVtYWlsIGNoYW5nZSBldmVudCB0byBnZXQgY3VzdG9tZXIgZGF0YSBmb3IgY3VzdG9tZXIgZmllbGRzXG4gICAqXG4gICAqIEBwYXJhbSBldmVudFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hhbmRsZUVtYWlsQ2hhbmdlKGV2ZW50KSB7XG4gICAgY29uc3QgZW1haWxJbnB1dCA9ICQoZXZlbnQudGFyZ2V0KTtcbiAgICBjb25zdCBnZXRGaWxsQ3VzdG9tZXJEYXRhVXJsID0gZW1haWxJbnB1dC5kYXRhKCdjdXN0b21lci1pbmZvcm1hdGlvbi11cmwnKTtcbiAgICBjb25zdCBlbWFpbCA9IGVtYWlsSW5wdXQudmFsKCk7XG5cbiAgICBpZiAoZW1haWwubGVuZ3RoID4gNSkge1xuICAgICAgJC5hamF4KHtcbiAgICAgICAgdXJsOiBnZXRGaWxsQ3VzdG9tZXJEYXRhVXJsLFxuICAgICAgICBkYXRhOiB7XG4gICAgICAgICAgZW1haWw6IGVtYWlsLFxuICAgICAgICB9LFxuICAgICAgICBkYXRhVHlwZTogJ2pzb24nLFxuICAgICAgfSkudGhlbihyZXNwb25zZSA9PiB7XG4gICAgICAgIHRoaXMuX3NldEN1c3RvbWVySW5mb3JtYXRpb24ocmVzcG9uc2UpO1xuICAgICAgfSk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIEZpbGxzIGN1c3RvbWVyIGZpZWxkcyB3aXRoIHJlc3BvbnNlIGRhdGFcbiAgICpcbiAgICogQHBhcmFtIGRhdGFcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zZXRDdXN0b21lckluZm9ybWF0aW9uKGRhdGEpIHtcbiAgICBjb25zdCBmaXJzdE5hbWVTZWxlY3RvciA9ICQodGhpcy5maXJzdE5hbWUpO1xuICAgIGNvbnN0IGxhc3ROYW1lU2VsZWN0b3IgPSAkKHRoaXMubGFzdE5hbWUpO1xuICAgIGNvbnN0IGNvbXBhbnlTZWxlY3RvciA9ICQodGhpcy5jb21wYW55KTtcblxuICAgIGlmICgwID4gZGF0YS5maXJzdF9uYW1lLmxlbmd0aCkge1xuICAgICAgZmlyc3ROYW1lU2VsZWN0b3IudmFsKGRhdGEuZmlyc3RfbmFtZSk7XG4gICAgfVxuXG4gICAgaWYgKDAgPiBkYXRhLmxhc3RfbmFtZS5sZW5ndGgpIHtcbiAgICAgIGxhc3ROYW1lU2VsZWN0b3IudmFsKGRhdGEubGFzdF9uYW1lKTtcbiAgICB9XG5cbiAgICBpZiAoMCA+IGRhdGEuY29tcGFueS5sZW5ndGgpIHtcbiAgICAgIGNvbXBhbnlTZWxlY3Rvci52YWwoZGF0YS5jb21wYW55KTtcbiAgICB9XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL2FkZHJlc3MvQ3VzdG9tZXJBZGRyZXNzRm9ybS5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbi8qKlxuICogRGVmaW5lcyBhbGwgc2VsZWN0b3JzIHRoYXQgYXJlIHVzZWQgaW4gY3VzdG9tZXJzIGFkZHJlc3MgYWRkL2VkaXQgZm9ybS5cbiAqL1xuZXhwb3J0IGRlZmF1bHQge1xuICBmaXJzdE5hbWU6ICcjY3VzdG9tZXJfYWRkcmVzc19maXJzdF9uYW1lJyxcbiAgbGFzdE5hbWU6ICcjY3VzdG9tZXJfYWRkcmVzc19sYXN0X25hbWUnLFxuICBjb21wYW55OiAnI2N1c3RvbWVyX2FkZHJlc3NfY29tcGFueScsXG4gIGNvdW50cnlTZWxlY3Q6ICcjY3VzdG9tZXJfYWRkcmVzc19pZF9jb3VudHJ5JyxcbiAgc3RhdGVTZWxlY3Q6ICcjY3VzdG9tZXJfYWRkcmVzc19pZF9zdGF0ZScsXG4gIGN1c3RvbWVyRW1haWw6ICcjY3VzdG9tZXJfYWRkcmVzc19jdXN0b21lcl9lbWFpbCcsXG4gIHN0YXRlRm9ybVJvd1NlbGVjdDogJy5qcy1hZGRyZXNzLXN0YXRlLXNlbGVjdCcsXG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9hZGRyZXNzL2FkZHJlc3MtZm9ybS1tYXAuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgQ3VzdG9tZXJBZGRyZXNzRm9ybSBmcm9tIFwiLi9DdXN0b21lckFkZHJlc3NGb3JtXCI7XG5cbiQoKCkgPT4ge1xuICBuZXcgQ3VzdG9tZXJBZGRyZXNzRm9ybSgpO1xufSk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9hZGRyZXNzL2Zvcm0uanMiLCIoZnVuY3Rpb24oKSB7IG1vZHVsZS5leHBvcnRzID0gd2luZG93W1wialF1ZXJ5XCJdOyB9KCkpO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIGV4dGVybmFsIFwialF1ZXJ5XCJcbi8vIG1vZHVsZSBpZCA9IDRcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNiAyMiAyOCAyOSAzMSJdLCJzb3VyY2VSb290IjoiIn0=