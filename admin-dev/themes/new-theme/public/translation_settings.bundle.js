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
/******/ 	return __webpack_require__(__webpack_require__.s = "./js/pages/translation-settings/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./js/pages/translation-settings/FormFieldToggle.js":
/*!**********************************************************!*\
  !*** ./js/pages/translation-settings/FormFieldToggle.js ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return FormFieldToggle; });
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
/**
 * Back office translations type
 *
 * @type {string}
 */

var back = 'back';
/**
 * Modules translations type
 * @type {string}
 */

var themes = 'themes';
/**
 * Modules translations type
 * @type {string}
 */

var modules = 'modules';
/**
 * Mails translations type
 * @type {string}
 */

var mails = 'mails';
/**
 * Other translations type
 * @type {string}
 */

var others = 'others';
/**
 * Email body translations type
 * @type {string}
 */

var emailContentBody = 'body';

var FormFieldToggle =
/*#__PURE__*/
function () {
  function FormFieldToggle() {
    _classCallCheck(this, FormFieldToggle);

    $('.js-translation-type').on('change', this.toggleFields.bind(this));
    $('.js-email-content-type').on('change', this.toggleEmailFields.bind(this));
    this.toggleFields();
  }
  /**
   * Toggle dependant translations fields, based on selected translation type
   */


  _createClass(FormFieldToggle, [{
    key: "toggleFields",
    value: function toggleFields() {
      var selectedOption = $('.js-translation-type').val();
      var $modulesFormGroup = $('.js-module-form-group');
      var $emailFormGroup = $('.js-email-form-group');
      var $themesFormGroup = $('.js-theme-form-group');
      var $themesSelect = $themesFormGroup.find('select');
      var $noThemeOption = $themesSelect.find('.js-no-theme');
      var $firstThemeOption = $themesSelect.find('option:not(.js-no-theme):first');

      switch (selectedOption) {
        case back:
        case others:
          this._hide($modulesFormGroup, $emailFormGroup, $themesFormGroup);

          break;

        case themes:
          if ($noThemeOption.is(':selected')) {
            $themesSelect.val($firstThemeOption.val());
          }

          this._hide($modulesFormGroup, $emailFormGroup, $noThemeOption);

          this._show($themesFormGroup);

          break;

        case modules:
          this._hide($emailFormGroup, $themesFormGroup);

          this._show($modulesFormGroup);

          break;

        case mails:
          this._hide($modulesFormGroup, $themesFormGroup);

          this._show($emailFormGroup);

          break;
      }

      this.toggleEmailFields();
    }
    /**
     * Toggles fields, which are related to email translations
     */

  }, {
    key: "toggleEmailFields",
    value: function toggleEmailFields() {
      if ($('.js-translation-type').val() !== mails) {
        return;
      }

      var selectedEmailContentType = $('.js-email-form-group').find('select').val();
      var $themesFormGroup = $('.js-theme-form-group');
      var $noThemeOption = $themesFormGroup.find('.js-no-theme');

      if (selectedEmailContentType === emailContentBody) {
        $noThemeOption.prop('selected', true);

        this._show($noThemeOption, $themesFormGroup);
      } else {
        this._hide($noThemeOption, $themesFormGroup);
      }
    }
    /**
     * Make all given selectors hidden
     *
     * @param $selectors
     * @private
     */

  }, {
    key: "_hide",
    value: function _hide() {
      for (var _len = arguments.length, $selectors = new Array(_len), _key = 0; _key < _len; _key++) {
        $selectors[_key] = arguments[_key];
      }

      for (var key in $selectors) {
        $selectors[key].addClass('d-none');
        $selectors[key].find('select').prop('disabled', 'disabled');
      }
    }
    /**
     * Make all given selectors visible
     *
     * @param $selectors
     * @private
     */

  }, {
    key: "_show",
    value: function _show() {
      for (var _len2 = arguments.length, $selectors = new Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
        $selectors[_key2] = arguments[_key2];
      }

      for (var key in $selectors) {
        $selectors[key].removeClass('d-none');
        $selectors[key].find('select').prop('disabled', false);
      }
    }
  }]);

  return FormFieldToggle;
}();



/***/ }),

/***/ "./js/pages/translation-settings/TranslationSettingsPage.js":
/*!******************************************************************!*\
  !*** ./js/pages/translation-settings/TranslationSettingsPage.js ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return TranslationSettingsPage; });
/* harmony import */ var _FormFieldToggle__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./FormFieldToggle */ "./js/pages/translation-settings/FormFieldToggle.js");
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


var TranslationSettingsPage = function TranslationSettingsPage() {
  _classCallCheck(this, TranslationSettingsPage);

  new _FormFieldToggle__WEBPACK_IMPORTED_MODULE_0__["default"]();
};



/***/ }),

/***/ "./js/pages/translation-settings/index.js":
/*!************************************************!*\
  !*** ./js/pages/translation-settings/index.js ***!
  \************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _TranslationSettingsPage__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./TranslationSettingsPage */ "./js/pages/translation-settings/TranslationSettingsPage.js");
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
  new _TranslationSettingsPage__WEBPACK_IMPORTED_MODULE_0__["default"]();
});

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvdHJhbnNsYXRpb24tc2V0dGluZ3MvRm9ybUZpZWxkVG9nZ2xlLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL3RyYW5zbGF0aW9uLXNldHRpbmdzL1RyYW5zbGF0aW9uU2V0dGluZ3NQYWdlLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL3RyYW5zbGF0aW9uLXNldHRpbmdzL2luZGV4LmpzIl0sIm5hbWVzIjpbIiQiLCJ3aW5kb3ciLCJiYWNrIiwidGhlbWVzIiwibW9kdWxlcyIsIm1haWxzIiwib3RoZXJzIiwiZW1haWxDb250ZW50Qm9keSIsIkZvcm1GaWVsZFRvZ2dsZSIsIm9uIiwidG9nZ2xlRmllbGRzIiwiYmluZCIsInRvZ2dsZUVtYWlsRmllbGRzIiwic2VsZWN0ZWRPcHRpb24iLCJ2YWwiLCIkbW9kdWxlc0Zvcm1Hcm91cCIsIiRlbWFpbEZvcm1Hcm91cCIsIiR0aGVtZXNGb3JtR3JvdXAiLCIkdGhlbWVzU2VsZWN0IiwiZmluZCIsIiRub1RoZW1lT3B0aW9uIiwiJGZpcnN0VGhlbWVPcHRpb24iLCJfaGlkZSIsImlzIiwiX3Nob3ciLCJzZWxlY3RlZEVtYWlsQ29udGVudFR5cGUiLCJwcm9wIiwiJHNlbGVjdG9ycyIsImtleSIsImFkZENsYXNzIiwicmVtb3ZlQ2xhc3MiLCJUcmFuc2xhdGlvblNldHRpbmdzUGFnZSJdLCJtYXBwaW5ncyI6IjtBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0Esa0RBQTBDLGdDQUFnQztBQUMxRTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLGdFQUF3RCxrQkFBa0I7QUFDMUU7QUFDQSx5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L0IsQ0FBb0MsVUFBcEMsRUFBZ0QsS0FBaEQ7QUFDSDtBQUNKOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQy9KTDs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBeUJBOztJQUVxQkssdUIsR0FDakIsbUNBQWM7QUFBQTs7QUFDVixNQUFJdkIsd0RBQUo7QUFDSCxDOzs7Ozs7Ozs7Ozs7OztBQzlCTDtBQUFBO0FBQUE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQXlCQTtBQUVBLElBQU1SLENBQUMsR0FBR0MsTUFBTSxDQUFDRCxDQUFqQjtBQUVBQSxDQUFDLENBQUMsWUFBTTtBQUNKLE1BQUkrQixnRUFBSjtBQUNILENBRkEsQ0FBRCxDIiwiZmlsZSI6InRyYW5zbGF0aW9uX3NldHRpbmdzLmJ1bmRsZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwgeyBlbnVtZXJhYmxlOiB0cnVlLCBnZXQ6IGdldHRlciB9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZGVmaW5lIF9fZXNNb2R1bGUgb24gZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5yID0gZnVuY3Rpb24oZXhwb3J0cykge1xuIFx0XHRpZih0eXBlb2YgU3ltYm9sICE9PSAndW5kZWZpbmVkJyAmJiBTeW1ib2wudG9TdHJpbmdUYWcpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgU3ltYm9sLnRvU3RyaW5nVGFnLCB7IHZhbHVlOiAnTW9kdWxlJyB9KTtcbiBcdFx0fVxuIFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgJ19fZXNNb2R1bGUnLCB7IHZhbHVlOiB0cnVlIH0pO1xuIFx0fTtcblxuIFx0Ly8gY3JlYXRlIGEgZmFrZSBuYW1lc3BhY2Ugb2JqZWN0XG4gXHQvLyBtb2RlICYgMTogdmFsdWUgaXMgYSBtb2R1bGUgaWQsIHJlcXVpcmUgaXRcbiBcdC8vIG1vZGUgJiAyOiBtZXJnZSBhbGwgcHJvcGVydGllcyBvZiB2YWx1ZSBpbnRvIHRoZSBuc1xuIFx0Ly8gbW9kZSAmIDQ6IHJldHVybiB2YWx1ZSB3aGVuIGFscmVhZHkgbnMgb2JqZWN0XG4gXHQvLyBtb2RlICYgOHwxOiBiZWhhdmUgbGlrZSByZXF1aXJlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnQgPSBmdW5jdGlvbih2YWx1ZSwgbW9kZSkge1xuIFx0XHRpZihtb2RlICYgMSkgdmFsdWUgPSBfX3dlYnBhY2tfcmVxdWlyZV9fKHZhbHVlKTtcbiBcdFx0aWYobW9kZSAmIDgpIHJldHVybiB2YWx1ZTtcbiBcdFx0aWYoKG1vZGUgJiA0KSAmJiB0eXBlb2YgdmFsdWUgPT09ICdvYmplY3QnICYmIHZhbHVlICYmIHZhbHVlLl9fZXNNb2R1bGUpIHJldHVybiB2YWx1ZTtcbiBcdFx0dmFyIG5zID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5yKG5zKTtcbiBcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KG5zLCAnZGVmYXVsdCcsIHsgZW51bWVyYWJsZTogdHJ1ZSwgdmFsdWU6IHZhbHVlIH0pO1xuIFx0XHRpZihtb2RlICYgMiAmJiB0eXBlb2YgdmFsdWUgIT0gJ3N0cmluZycpIGZvcih2YXIga2V5IGluIHZhbHVlKSBfX3dlYnBhY2tfcmVxdWlyZV9fLmQobnMsIGtleSwgZnVuY3Rpb24oa2V5KSB7IHJldHVybiB2YWx1ZVtrZXldOyB9LmJpbmQobnVsbCwga2V5KSk7XG4gXHRcdHJldHVybiBucztcbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiL2FkbWluLWRldi90aGVtZXMvbmV3LXRoZW1lL3B1YmxpYy9cIjtcblxuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IFwiLi9qcy9wYWdlcy90cmFuc2xhdGlvbi1zZXR0aW5ncy9pbmRleC5qc1wiKTtcbiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBCYWNrIG9mZmljZSB0cmFuc2xhdGlvbnMgdHlwZVxuICpcbiAqIEB0eXBlIHtzdHJpbmd9XG4gKi9cbmNvbnN0IGJhY2sgPSAnYmFjayc7XG5cbi8qKlxuICogTW9kdWxlcyB0cmFuc2xhdGlvbnMgdHlwZVxuICogQHR5cGUge3N0cmluZ31cbiAqL1xuY29uc3QgdGhlbWVzID0gJ3RoZW1lcyc7XG5cbi8qKlxuICogTW9kdWxlcyB0cmFuc2xhdGlvbnMgdHlwZVxuICogQHR5cGUge3N0cmluZ31cbiAqL1xuY29uc3QgbW9kdWxlcyA9ICdtb2R1bGVzJztcblxuLyoqXG4gKiBNYWlscyB0cmFuc2xhdGlvbnMgdHlwZVxuICogQHR5cGUge3N0cmluZ31cbiAqL1xuY29uc3QgbWFpbHMgPSAnbWFpbHMnO1xuXG4vKipcbiAqIE90aGVyIHRyYW5zbGF0aW9ucyB0eXBlXG4gKiBAdHlwZSB7c3RyaW5nfVxuICovXG5jb25zdCBvdGhlcnMgPSAnb3RoZXJzJztcblxuLyoqXG4gKiBFbWFpbCBib2R5IHRyYW5zbGF0aW9ucyB0eXBlXG4gKiBAdHlwZSB7c3RyaW5nfVxuICovXG5jb25zdCBlbWFpbENvbnRlbnRCb2R5ID0gJ2JvZHknO1xuXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBGb3JtRmllbGRUb2dnbGUge1xuICAgIGNvbnN0cnVjdG9yKCkge1xuICAgICAgICAkKCcuanMtdHJhbnNsYXRpb24tdHlwZScpLm9uKCdjaGFuZ2UnLCB0aGlzLnRvZ2dsZUZpZWxkcy5iaW5kKHRoaXMpKTtcbiAgICAgICAgJCgnLmpzLWVtYWlsLWNvbnRlbnQtdHlwZScpLm9uKCdjaGFuZ2UnLCB0aGlzLnRvZ2dsZUVtYWlsRmllbGRzLmJpbmQodGhpcykpO1xuXG4gICAgICAgIHRoaXMudG9nZ2xlRmllbGRzKCk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogVG9nZ2xlIGRlcGVuZGFudCB0cmFuc2xhdGlvbnMgZmllbGRzLCBiYXNlZCBvbiBzZWxlY3RlZCB0cmFuc2xhdGlvbiB0eXBlXG4gICAgICovXG4gICAgdG9nZ2xlRmllbGRzKCkge1xuICAgICAgICBsZXQgc2VsZWN0ZWRPcHRpb24gPSAkKCcuanMtdHJhbnNsYXRpb24tdHlwZScpLnZhbCgpO1xuICAgICAgICBsZXQgJG1vZHVsZXNGb3JtR3JvdXAgPSAkKCcuanMtbW9kdWxlLWZvcm0tZ3JvdXAnKTtcbiAgICAgICAgbGV0ICRlbWFpbEZvcm1Hcm91cCA9ICQoJy5qcy1lbWFpbC1mb3JtLWdyb3VwJyk7XG4gICAgICAgIGxldCAkdGhlbWVzRm9ybUdyb3VwID0gJCgnLmpzLXRoZW1lLWZvcm0tZ3JvdXAnKTtcbiAgICAgICAgbGV0ICR0aGVtZXNTZWxlY3QgPSAkdGhlbWVzRm9ybUdyb3VwLmZpbmQoJ3NlbGVjdCcpO1xuICAgICAgICBsZXQgJG5vVGhlbWVPcHRpb24gPSAkdGhlbWVzU2VsZWN0LmZpbmQoJy5qcy1uby10aGVtZScpO1xuICAgICAgICBsZXQgJGZpcnN0VGhlbWVPcHRpb24gPSAkdGhlbWVzU2VsZWN0LmZpbmQoJ29wdGlvbjpub3QoLmpzLW5vLXRoZW1lKTpmaXJzdCcpO1xuXG4gICAgICAgIHN3aXRjaCAoc2VsZWN0ZWRPcHRpb24pIHtcbiAgICAgICAgICAgIGNhc2UgYmFjazpcbiAgICAgICAgICAgIGNhc2Ugb3RoZXJzOlxuICAgICAgICAgICAgICAgIHRoaXMuX2hpZGUoJG1vZHVsZXNGb3JtR3JvdXAsICRlbWFpbEZvcm1Hcm91cCwgJHRoZW1lc0Zvcm1Hcm91cCk7XG5cbiAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgIGNhc2UgdGhlbWVzOlxuICAgICAgICAgICAgICAgIGlmICgkbm9UaGVtZU9wdGlvbi5pcygnOnNlbGVjdGVkJykpIHtcbiAgICAgICAgICAgICAgICAgICAgJHRoZW1lc1NlbGVjdC52YWwoJGZpcnN0VGhlbWVPcHRpb24udmFsKCkpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHRoaXMuX2hpZGUoJG1vZHVsZXNGb3JtR3JvdXAsICRlbWFpbEZvcm1Hcm91cCwgJG5vVGhlbWVPcHRpb24pO1xuICAgICAgICAgICAgICAgIHRoaXMuX3Nob3coJHRoZW1lc0Zvcm1Hcm91cCk7XG5cbiAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgIGNhc2UgbW9kdWxlczpcbiAgICAgICAgICAgICAgICB0aGlzLl9oaWRlKCRlbWFpbEZvcm1Hcm91cCwgJHRoZW1lc0Zvcm1Hcm91cCk7XG4gICAgICAgICAgICAgICAgdGhpcy5fc2hvdygkbW9kdWxlc0Zvcm1Hcm91cCk7XG5cbiAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgIGNhc2UgbWFpbHM6XG4gICAgICAgICAgICAgICAgdGhpcy5faGlkZSgkbW9kdWxlc0Zvcm1Hcm91cCwgJHRoZW1lc0Zvcm1Hcm91cCk7XG4gICAgICAgICAgICAgICAgdGhpcy5fc2hvdygkZW1haWxGb3JtR3JvdXApO1xuXG4gICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLnRvZ2dsZUVtYWlsRmllbGRzKCk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogVG9nZ2xlcyBmaWVsZHMsIHdoaWNoIGFyZSByZWxhdGVkIHRvIGVtYWlsIHRyYW5zbGF0aW9uc1xuICAgICAqL1xuICAgIHRvZ2dsZUVtYWlsRmllbGRzKCkge1xuICAgICAgICBpZiAoJCgnLmpzLXRyYW5zbGF0aW9uLXR5cGUnKS52YWwoKSAhPT0gbWFpbHMpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIGxldCBzZWxlY3RlZEVtYWlsQ29udGVudFR5cGUgPSAkKCcuanMtZW1haWwtZm9ybS1ncm91cCcpLmZpbmQoJ3NlbGVjdCcpLnZhbCgpO1xuICAgICAgICBsZXQgJHRoZW1lc0Zvcm1Hcm91cCA9ICQoJy5qcy10aGVtZS1mb3JtLWdyb3VwJyk7XG4gICAgICAgIGxldCAkbm9UaGVtZU9wdGlvbiA9ICR0aGVtZXNGb3JtR3JvdXAuZmluZCgnLmpzLW5vLXRoZW1lJyk7XG5cbiAgICAgICAgaWYgKHNlbGVjdGVkRW1haWxDb250ZW50VHlwZSA9PT0gZW1haWxDb250ZW50Qm9keSkge1xuICAgICAgICAgICAgJG5vVGhlbWVPcHRpb24ucHJvcCgnc2VsZWN0ZWQnLCB0cnVlKTtcbiAgICAgICAgICAgIHRoaXMuX3Nob3coJG5vVGhlbWVPcHRpb24sICR0aGVtZXNGb3JtR3JvdXApO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgdGhpcy5faGlkZSgkbm9UaGVtZU9wdGlvbiwgJHRoZW1lc0Zvcm1Hcm91cCk7XG4gICAgICAgIH1cbiAgICB9XG5cblxuICAgIC8qKlxuICAgICAqIE1ha2UgYWxsIGdpdmVuIHNlbGVjdG9ycyBoaWRkZW5cbiAgICAgKlxuICAgICAqIEBwYXJhbSAkc2VsZWN0b3JzXG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBfaGlkZSguLi4kc2VsZWN0b3JzKSB7XG4gICAgICAgIGZvciAobGV0IGtleSBpbiAkc2VsZWN0b3JzKSB7XG4gICAgICAgICAgICAkc2VsZWN0b3JzW2tleV0uYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICAgICAgICAgICAgJHNlbGVjdG9yc1trZXldLmZpbmQoJ3NlbGVjdCcpLnByb3AoJ2Rpc2FibGVkJywgJ2Rpc2FibGVkJyk7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBNYWtlIGFsbCBnaXZlbiBzZWxlY3RvcnMgdmlzaWJsZVxuICAgICAqXG4gICAgICogQHBhcmFtICRzZWxlY3RvcnNcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9zaG93KC4uLiRzZWxlY3RvcnMpIHtcbiAgICAgICAgZm9yIChsZXQga2V5IGluICRzZWxlY3RvcnMpIHtcbiAgICAgICAgICAgICRzZWxlY3RvcnNba2V5XS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgICAgICAgICAkc2VsZWN0b3JzW2tleV0uZmluZCgnc2VsZWN0JykucHJvcCgnZGlzYWJsZWQnLCBmYWxzZSk7XG4gICAgICAgIH1cbiAgICB9XG59XG4iLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgRm9ybUZpZWxkVG9nZ2xlIGZyb20gXCIuL0Zvcm1GaWVsZFRvZ2dsZVwiO1xuXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBUcmFuc2xhdGlvblNldHRpbmdzUGFnZSB7XG4gICAgY29uc3RydWN0b3IoKSB7XG4gICAgICAgIG5ldyBGb3JtRmllbGRUb2dnbGUoKTtcbiAgICB9XG59XG4iLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgVHJhbnNsYXRpb25TZXR0aW5nc1BhZ2UgZnJvbSAnLi9UcmFuc2xhdGlvblNldHRpbmdzUGFnZSc7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuJCgoKSA9PiB7XG4gICAgbmV3IFRyYW5zbGF0aW9uU2V0dGluZ3NQYWdlKCk7XG59KTtcbiJdLCJzb3VyY2VSb290IjoiIn0=