window["translation_settings"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 352);
/******/ })
/************************************************************************/
/******/ ({

/***/ 272:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});

var _FormFieldToggle = __webpack_require__(351);

var _FormFieldToggle2 = _interopRequireDefault(_FormFieldToggle);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } } /**
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

    new _FormFieldToggle2.default();
};

exports.default = TranslationSettingsPage;

/***/ }),

/***/ 351:
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

var FormFieldToggle = function () {
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
        key: 'toggleFields',
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
        key: 'toggleEmailFields',
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
        key: '_hide',
        value: function _hide() {
            for (var _len = arguments.length, $selectors = Array(_len), _key = 0; _key < _len; _key++) {
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
        key: '_show',
        value: function _show() {
            for (var _len2 = arguments.length, $selectors = Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
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

exports.default = FormFieldToggle;

/***/ }),

/***/ 352:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _TranslationSettingsPage = __webpack_require__(272);

var _TranslationSettingsPage2 = _interopRequireDefault(_TranslationSettingsPage);

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

$(function () {
  new _TranslationSettingsPage2.default();
});

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNjhlODI5MWYxMzYwNzBmMjc2YmQ/MjBkNCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL3RyYW5zbGF0aW9uLXNldHRpbmdzL1RyYW5zbGF0aW9uU2V0dGluZ3NQYWdlLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL3RyYW5zbGF0aW9uLXNldHRpbmdzL0Zvcm1GaWVsZFRvZ2dsZS5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy90cmFuc2xhdGlvbi1zZXR0aW5ncy9pbmRleC5qcyJdLCJuYW1lcyI6WyJUcmFuc2xhdGlvblNldHRpbmdzUGFnZSIsIkZvcm1GaWVsZFRvZ2dsZSIsIiQiLCJ3aW5kb3ciLCJiYWNrIiwidGhlbWVzIiwibW9kdWxlcyIsIm1haWxzIiwib3RoZXJzIiwiZW1haWxDb250ZW50Qm9keSIsIm9uIiwidG9nZ2xlRmllbGRzIiwiYmluZCIsInRvZ2dsZUVtYWlsRmllbGRzIiwic2VsZWN0ZWRPcHRpb24iLCJ2YWwiLCIkbW9kdWxlc0Zvcm1Hcm91cCIsIiRlbWFpbEZvcm1Hcm91cCIsIiR0aGVtZXNGb3JtR3JvdXAiLCIkdGhlbWVzU2VsZWN0IiwiZmluZCIsIiRub1RoZW1lT3B0aW9uIiwiJGZpcnN0VGhlbWVPcHRpb24iLCJfaGlkZSIsImlzIiwiX3Nob3ciLCJzZWxlY3RlZEVtYWlsQ29udGVudFR5cGUiLCJwcm9wIiwiJHNlbGVjdG9ycyIsImtleSIsImFkZENsYXNzIiwicmVtb3ZlQ2xhc3MiXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7OztBQUdBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBLG1EQUEyQyxjQUFjOztBQUV6RDtBQUNB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LENBRkQsRSIsImZpbGUiOiJ0cmFuc2xhdGlvbl9zZXR0aW5ncy5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBpZGVudGl0eSBmdW5jdGlvbiBmb3IgY2FsbGluZyBoYXJtb255IGltcG9ydHMgd2l0aCB0aGUgY29ycmVjdCBjb250ZXh0XG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmkgPSBmdW5jdGlvbih2YWx1ZSkgeyByZXR1cm4gdmFsdWU7IH07XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDM1Mik7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gd2VicGFjay9ib290c3RyYXAgNjhlODI5MWYxMzYwNzBmMjc2YmQiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgRm9ybUZpZWxkVG9nZ2xlIGZyb20gXCIuL0Zvcm1GaWVsZFRvZ2dsZVwiO1xuXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBUcmFuc2xhdGlvblNldHRpbmdzUGFnZSB7XG4gICAgY29uc3RydWN0b3IoKSB7XG4gICAgICAgIG5ldyBGb3JtRmllbGRUb2dnbGUoKTtcbiAgICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy90cmFuc2xhdGlvbi1zZXR0aW5ncy9UcmFuc2xhdGlvblNldHRpbmdzUGFnZS5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBCYWNrIG9mZmljZSB0cmFuc2xhdGlvbnMgdHlwZVxuICpcbiAqIEB0eXBlIHtzdHJpbmd9XG4gKi9cbmNvbnN0IGJhY2sgPSAnYmFjayc7XG5cbi8qKlxuICogTW9kdWxlcyB0cmFuc2xhdGlvbnMgdHlwZVxuICogQHR5cGUge3N0cmluZ31cbiAqL1xuY29uc3QgdGhlbWVzID0gJ3RoZW1lcyc7XG5cbi8qKlxuICogTW9kdWxlcyB0cmFuc2xhdGlvbnMgdHlwZVxuICogQHR5cGUge3N0cmluZ31cbiAqL1xuY29uc3QgbW9kdWxlcyA9ICdtb2R1bGVzJztcblxuLyoqXG4gKiBNYWlscyB0cmFuc2xhdGlvbnMgdHlwZVxuICogQHR5cGUge3N0cmluZ31cbiAqL1xuY29uc3QgbWFpbHMgPSAnbWFpbHMnO1xuXG4vKipcbiAqIE90aGVyIHRyYW5zbGF0aW9ucyB0eXBlXG4gKiBAdHlwZSB7c3RyaW5nfVxuICovXG5jb25zdCBvdGhlcnMgPSAnb3RoZXJzJztcblxuLyoqXG4gKiBFbWFpbCBib2R5IHRyYW5zbGF0aW9ucyB0eXBlXG4gKiBAdHlwZSB7c3RyaW5nfVxuICovXG5jb25zdCBlbWFpbENvbnRlbnRCb2R5ID0gJ2JvZHknO1xuXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBGb3JtRmllbGRUb2dnbGUge1xuICAgIGNvbnN0cnVjdG9yKCkge1xuICAgICAgICAkKCcuanMtdHJhbnNsYXRpb24tdHlwZScpLm9uKCdjaGFuZ2UnLCB0aGlzLnRvZ2dsZUZpZWxkcy5iaW5kKHRoaXMpKTtcbiAgICAgICAgJCgnLmpzLWVtYWlsLWNvbnRlbnQtdHlwZScpLm9uKCdjaGFuZ2UnLCB0aGlzLnRvZ2dsZUVtYWlsRmllbGRzLmJpbmQodGhpcykpO1xuXG4gICAgICAgIHRoaXMudG9nZ2xlRmllbGRzKCk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogVG9nZ2xlIGRlcGVuZGFudCB0cmFuc2xhdGlvbnMgZmllbGRzLCBiYXNlZCBvbiBzZWxlY3RlZCB0cmFuc2xhdGlvbiB0eXBlXG4gICAgICovXG4gICAgdG9nZ2xlRmllbGRzKCkge1xuICAgICAgICBsZXQgc2VsZWN0ZWRPcHRpb24gPSAkKCcuanMtdHJhbnNsYXRpb24tdHlwZScpLnZhbCgpO1xuICAgICAgICBsZXQgJG1vZHVsZXNGb3JtR3JvdXAgPSAkKCcuanMtbW9kdWxlLWZvcm0tZ3JvdXAnKTtcbiAgICAgICAgbGV0ICRlbWFpbEZvcm1Hcm91cCA9ICQoJy5qcy1lbWFpbC1mb3JtLWdyb3VwJyk7XG4gICAgICAgIGxldCAkdGhlbWVzRm9ybUdyb3VwID0gJCgnLmpzLXRoZW1lLWZvcm0tZ3JvdXAnKTtcbiAgICAgICAgbGV0ICR0aGVtZXNTZWxlY3QgPSAkdGhlbWVzRm9ybUdyb3VwLmZpbmQoJ3NlbGVjdCcpO1xuICAgICAgICBsZXQgJG5vVGhlbWVPcHRpb24gPSAkdGhlbWVzU2VsZWN0LmZpbmQoJy5qcy1uby10aGVtZScpO1xuICAgICAgICBsZXQgJGZpcnN0VGhlbWVPcHRpb24gPSAkdGhlbWVzU2VsZWN0LmZpbmQoJ29wdGlvbjpub3QoLmpzLW5vLXRoZW1lKTpmaXJzdCcpO1xuXG4gICAgICAgIHN3aXRjaCAoc2VsZWN0ZWRPcHRpb24pIHtcbiAgICAgICAgICAgIGNhc2UgYmFjazpcbiAgICAgICAgICAgIGNhc2Ugb3RoZXJzOlxuICAgICAgICAgICAgICAgIHRoaXMuX2hpZGUoJG1vZHVsZXNGb3JtR3JvdXAsICRlbWFpbEZvcm1Hcm91cCwgJHRoZW1lc0Zvcm1Hcm91cCk7XG5cbiAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgIGNhc2UgdGhlbWVzOlxuICAgICAgICAgICAgICAgIGlmICgkbm9UaGVtZU9wdGlvbi5pcygnOnNlbGVjdGVkJykpIHtcbiAgICAgICAgICAgICAgICAgICAgJHRoZW1lc1NlbGVjdC52YWwoJGZpcnN0VGhlbWVPcHRpb24udmFsKCkpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHRoaXMuX2hpZGUoJG1vZHVsZXNGb3JtR3JvdXAsICRlbWFpbEZvcm1Hcm91cCwgJG5vVGhlbWVPcHRpb24pO1xuICAgICAgICAgICAgICAgIHRoaXMuX3Nob3coJHRoZW1lc0Zvcm1Hcm91cCk7XG5cbiAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgIGNhc2UgbW9kdWxlczpcbiAgICAgICAgICAgICAgICB0aGlzLl9oaWRlKCRlbWFpbEZvcm1Hcm91cCwgJHRoZW1lc0Zvcm1Hcm91cCk7XG4gICAgICAgICAgICAgICAgdGhpcy5fc2hvdygkbW9kdWxlc0Zvcm1Hcm91cCk7XG5cbiAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgIGNhc2UgbWFpbHM6XG4gICAgICAgICAgICAgICAgdGhpcy5faGlkZSgkbW9kdWxlc0Zvcm1Hcm91cCwgJHRoZW1lc0Zvcm1Hcm91cCk7XG4gICAgICAgICAgICAgICAgdGhpcy5fc2hvdygkZW1haWxGb3JtR3JvdXApO1xuXG4gICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgIH1cblxuICAgICAgICB0aGlzLnRvZ2dsZUVtYWlsRmllbGRzKCk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogVG9nZ2xlcyBmaWVsZHMsIHdoaWNoIGFyZSByZWxhdGVkIHRvIGVtYWlsIHRyYW5zbGF0aW9uc1xuICAgICAqL1xuICAgIHRvZ2dsZUVtYWlsRmllbGRzKCkge1xuICAgICAgICBpZiAoJCgnLmpzLXRyYW5zbGF0aW9uLXR5cGUnKS52YWwoKSAhPT0gbWFpbHMpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIGxldCBzZWxlY3RlZEVtYWlsQ29udGVudFR5cGUgPSAkKCcuanMtZW1haWwtZm9ybS1ncm91cCcpLmZpbmQoJ3NlbGVjdCcpLnZhbCgpO1xuICAgICAgICBsZXQgJHRoZW1lc0Zvcm1Hcm91cCA9ICQoJy5qcy10aGVtZS1mb3JtLWdyb3VwJyk7XG4gICAgICAgIGxldCAkbm9UaGVtZU9wdGlvbiA9ICR0aGVtZXNGb3JtR3JvdXAuZmluZCgnLmpzLW5vLXRoZW1lJyk7XG5cbiAgICAgICAgaWYgKHNlbGVjdGVkRW1haWxDb250ZW50VHlwZSA9PT0gZW1haWxDb250ZW50Qm9keSkge1xuICAgICAgICAgICAgJG5vVGhlbWVPcHRpb24ucHJvcCgnc2VsZWN0ZWQnLCB0cnVlKTtcbiAgICAgICAgICAgIHRoaXMuX3Nob3coJG5vVGhlbWVPcHRpb24sICR0aGVtZXNGb3JtR3JvdXApO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgdGhpcy5faGlkZSgkbm9UaGVtZU9wdGlvbiwgJHRoZW1lc0Zvcm1Hcm91cCk7XG4gICAgICAgIH1cbiAgICB9XG5cblxuICAgIC8qKlxuICAgICAqIE1ha2UgYWxsIGdpdmVuIHNlbGVjdG9ycyBoaWRkZW5cbiAgICAgKlxuICAgICAqIEBwYXJhbSAkc2VsZWN0b3JzXG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBfaGlkZSguLi4kc2VsZWN0b3JzKSB7XG4gICAgICAgIGZvciAobGV0IGtleSBpbiAkc2VsZWN0b3JzKSB7XG4gICAgICAgICAgICAkc2VsZWN0b3JzW2tleV0uYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICAgICAgICAgICAgJHNlbGVjdG9yc1trZXldLmZpbmQoJ3NlbGVjdCcpLnByb3AoJ2Rpc2FibGVkJywgJ2Rpc2FibGVkJyk7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBNYWtlIGFsbCBnaXZlbiBzZWxlY3RvcnMgdmlzaWJsZVxuICAgICAqXG4gICAgICogQHBhcmFtICRzZWxlY3RvcnNcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIF9zaG93KC4uLiRzZWxlY3RvcnMpIHtcbiAgICAgICAgZm9yIChsZXQga2V5IGluICRzZWxlY3RvcnMpIHtcbiAgICAgICAgICAgICRzZWxlY3RvcnNba2V5XS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgICAgICAgICAkc2VsZWN0b3JzW2tleV0uZmluZCgnc2VsZWN0JykucHJvcCgnZGlzYWJsZWQnLCBmYWxzZSk7XG4gICAgICAgIH1cbiAgICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy90cmFuc2xhdGlvbi1zZXR0aW5ncy9Gb3JtRmllbGRUb2dnbGUuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgVHJhbnNsYXRpb25TZXR0aW5nc1BhZ2UgZnJvbSAnLi9UcmFuc2xhdGlvblNldHRpbmdzUGFnZSc7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuJCgoKSA9PiB7XG4gICAgbmV3IFRyYW5zbGF0aW9uU2V0dGluZ3NQYWdlKCk7XG59KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL3RyYW5zbGF0aW9uLXNldHRpbmdzL2luZGV4LmpzIl0sInNvdXJjZVJvb3QiOiIifQ==