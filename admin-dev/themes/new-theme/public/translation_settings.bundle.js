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
/******/ 	return __webpack_require__(__webpack_require__.s = 351);
/******/ })
/************************************************************************/
/******/ ({

/***/ 272:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});

var _FormFieldToggle = __webpack_require__(350);

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

/***/ 350:
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

/***/ 351:
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNWQ5OTkwOTRkMTFhZWZmMGI1ODI/M2YxNioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy90cmFuc2xhdGlvbi1zZXR0aW5ncy9UcmFuc2xhdGlvblNldHRpbmdzUGFnZS5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy90cmFuc2xhdGlvbi1zZXR0aW5ncy9Gb3JtRmllbGRUb2dnbGUuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvdHJhbnNsYXRpb24tc2V0dGluZ3MvaW5kZXguanMiXSwibmFtZXMiOlsiVHJhbnNsYXRpb25TZXR0aW5nc1BhZ2UiLCJGb3JtRmllbGRUb2dnbGUiLCIkIiwid2luZG93IiwiYmFjayIsInRoZW1lcyIsIm1vZHVsZXMiLCJtYWlscyIsIm90aGVycyIsImVtYWlsQ29udGVudEJvZHkiLCJvbiIsInRvZ2dsZUZpZWxkcyIsImJpbmQiLCJ0b2dnbGVFbWFpbEZpZWxkcyIsInNlbGVjdGVkT3B0aW9uIiwidmFsIiwiJG1vZHVsZXNGb3JtR3JvdXAiLCIkZW1haWxGb3JtR3JvdXAiLCIkdGhlbWVzRm9ybUdyb3VwIiwiJHRoZW1lc1NlbGVjdCIsImZpbmQiLCIkbm9UaGVtZU9wdGlvbiIsIiRmaXJzdFRoZW1lT3B0aW9uIiwiX2hpZGUiLCJpcyIsIl9zaG93Iiwic2VsZWN0ZWRFbWFpbENvbnRlbnRUeXBlIiwicHJvcCIsIiRzZWxlY3RvcnMiLCJrZXkiLCJhZGRDbGFzcyIsInJlbW92ZUNsYXNzIl0sIm1hcHBpbmdzIjoiOztBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQSxtREFBMkMsY0FBYzs7QUFFekQ7QUFDQTtB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QUZELEUiLCJmaWxlIjoidHJhbnNsYXRpb25fc2V0dGluZ3MuYnVuZGxlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gaWRlbnRpdHkgZnVuY3Rpb24gZm9yIGNhbGxpbmcgaGFybW9ueSBpbXBvcnRzIHdpdGggdGhlIGNvcnJlY3QgY29udGV4dFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5pID0gZnVuY3Rpb24odmFsdWUpIHsgcmV0dXJuIHZhbHVlOyB9O1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHtcbiBcdFx0XHRcdGNvbmZpZ3VyYWJsZTogZmFsc2UsXG4gXHRcdFx0XHRlbnVtZXJhYmxlOiB0cnVlLFxuIFx0XHRcdFx0Z2V0OiBnZXR0ZXJcbiBcdFx0XHR9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCJcIjtcblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSAzNTEpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIHdlYnBhY2svYm9vdHN0cmFwIDVkOTk5MDk0ZDExYWVmZjBiNTgyIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IEZvcm1GaWVsZFRvZ2dsZSBmcm9tIFwiLi9Gb3JtRmllbGRUb2dnbGVcIjtcblxuZXhwb3J0IGRlZmF1bHQgY2xhc3MgVHJhbnNsYXRpb25TZXR0aW5nc1BhZ2Uge1xuICAgIGNvbnN0cnVjdG9yKCkge1xuICAgICAgICBuZXcgRm9ybUZpZWxkVG9nZ2xlKCk7XG4gICAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvdHJhbnNsYXRpb24tc2V0dGluZ3MvVHJhbnNsYXRpb25TZXR0aW5nc1BhZ2UuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQmFjayBvZmZpY2UgdHJhbnNsYXRpb25zIHR5cGVcbiAqXG4gKiBAdHlwZSB7c3RyaW5nfVxuICovXG5jb25zdCBiYWNrID0gJ2JhY2snO1xuXG4vKipcbiAqIE1vZHVsZXMgdHJhbnNsYXRpb25zIHR5cGVcbiAqIEB0eXBlIHtzdHJpbmd9XG4gKi9cbmNvbnN0IHRoZW1lcyA9ICd0aGVtZXMnO1xuXG4vKipcbiAqIE1vZHVsZXMgdHJhbnNsYXRpb25zIHR5cGVcbiAqIEB0eXBlIHtzdHJpbmd9XG4gKi9cbmNvbnN0IG1vZHVsZXMgPSAnbW9kdWxlcyc7XG5cbi8qKlxuICogTWFpbHMgdHJhbnNsYXRpb25zIHR5cGVcbiAqIEB0eXBlIHtzdHJpbmd9XG4gKi9cbmNvbnN0IG1haWxzID0gJ21haWxzJztcblxuLyoqXG4gKiBPdGhlciB0cmFuc2xhdGlvbnMgdHlwZVxuICogQHR5cGUge3N0cmluZ31cbiAqL1xuY29uc3Qgb3RoZXJzID0gJ290aGVycyc7XG5cbi8qKlxuICogRW1haWwgYm9keSB0cmFuc2xhdGlvbnMgdHlwZVxuICogQHR5cGUge3N0cmluZ31cbiAqL1xuY29uc3QgZW1haWxDb250ZW50Qm9keSA9ICdib2R5JztcblxuZXhwb3J0IGRlZmF1bHQgY2xhc3MgRm9ybUZpZWxkVG9nZ2xlIHtcbiAgICBjb25zdHJ1Y3RvcigpIHtcbiAgICAgICAgJCgnLmpzLXRyYW5zbGF0aW9uLXR5cGUnKS5vbignY2hhbmdlJywgdGhpcy50b2dnbGVGaWVsZHMuYmluZCh0aGlzKSk7XG4gICAgICAgICQoJy5qcy1lbWFpbC1jb250ZW50LXR5cGUnKS5vbignY2hhbmdlJywgdGhpcy50b2dnbGVFbWFpbEZpZWxkcy5iaW5kKHRoaXMpKTtcblxuICAgICAgICB0aGlzLnRvZ2dsZUZpZWxkcygpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFRvZ2dsZSBkZXBlbmRhbnQgdHJhbnNsYXRpb25zIGZpZWxkcywgYmFzZWQgb24gc2VsZWN0ZWQgdHJhbnNsYXRpb24gdHlwZVxuICAgICAqL1xuICAgIHRvZ2dsZUZpZWxkcygpIHtcbiAgICAgICAgbGV0IHNlbGVjdGVkT3B0aW9uID0gJCgnLmpzLXRyYW5zbGF0aW9uLXR5cGUnKS52YWwoKTtcbiAgICAgICAgbGV0ICRtb2R1bGVzRm9ybUdyb3VwID0gJCgnLmpzLW1vZHVsZS1mb3JtLWdyb3VwJyk7XG4gICAgICAgIGxldCAkZW1haWxGb3JtR3JvdXAgPSAkKCcuanMtZW1haWwtZm9ybS1ncm91cCcpO1xuICAgICAgICBsZXQgJHRoZW1lc0Zvcm1Hcm91cCA9ICQoJy5qcy10aGVtZS1mb3JtLWdyb3VwJyk7XG4gICAgICAgIGxldCAkdGhlbWVzU2VsZWN0ID0gJHRoZW1lc0Zvcm1Hcm91cC5maW5kKCdzZWxlY3QnKTtcbiAgICAgICAgbGV0ICRub1RoZW1lT3B0aW9uID0gJHRoZW1lc1NlbGVjdC5maW5kKCcuanMtbm8tdGhlbWUnKTtcbiAgICAgICAgbGV0ICRmaXJzdFRoZW1lT3B0aW9uID0gJHRoZW1lc1NlbGVjdC5maW5kKCdvcHRpb246bm90KC5qcy1uby10aGVtZSk6Zmlyc3QnKTtcblxuICAgICAgICBzd2l0Y2ggKHNlbGVjdGVkT3B0aW9uKSB7XG4gICAgICAgICAgICBjYXNlIGJhY2s6XG4gICAgICAgICAgICBjYXNlIG90aGVyczpcbiAgICAgICAgICAgICAgICB0aGlzLl9oaWRlKCRtb2R1bGVzRm9ybUdyb3VwLCAkZW1haWxGb3JtR3JvdXAsICR0aGVtZXNGb3JtR3JvdXApO1xuXG4gICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICBjYXNlIHRoZW1lczpcbiAgICAgICAgICAgICAgICBpZiAoJG5vVGhlbWVPcHRpb24uaXMoJzpzZWxlY3RlZCcpKSB7XG4gICAgICAgICAgICAgICAgICAgICR0aGVtZXNTZWxlY3QudmFsKCRmaXJzdFRoZW1lT3B0aW9uLnZhbCgpKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB0aGlzLl9oaWRlKCRtb2R1bGVzRm9ybUdyb3VwLCAkZW1haWxGb3JtR3JvdXAsICRub1RoZW1lT3B0aW9uKTtcbiAgICAgICAgICAgICAgICB0aGlzLl9zaG93KCR0aGVtZXNGb3JtR3JvdXApO1xuXG4gICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICBjYXNlIG1vZHVsZXM6XG4gICAgICAgICAgICAgICAgdGhpcy5faGlkZSgkZW1haWxGb3JtR3JvdXAsICR0aGVtZXNGb3JtR3JvdXApO1xuICAgICAgICAgICAgICAgIHRoaXMuX3Nob3coJG1vZHVsZXNGb3JtR3JvdXApO1xuXG4gICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICBjYXNlIG1haWxzOlxuICAgICAgICAgICAgICAgIHRoaXMuX2hpZGUoJG1vZHVsZXNGb3JtR3JvdXAsICR0aGVtZXNGb3JtR3JvdXApO1xuICAgICAgICAgICAgICAgIHRoaXMuX3Nob3coJGVtYWlsRm9ybUdyb3VwKTtcblxuICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICB9XG5cbiAgICAgICAgdGhpcy50b2dnbGVFbWFpbEZpZWxkcygpO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFRvZ2dsZXMgZmllbGRzLCB3aGljaCBhcmUgcmVsYXRlZCB0byBlbWFpbCB0cmFuc2xhdGlvbnNcbiAgICAgKi9cbiAgICB0b2dnbGVFbWFpbEZpZWxkcygpIHtcbiAgICAgICAgaWYgKCQoJy5qcy10cmFuc2xhdGlvbi10eXBlJykudmFsKCkgIT09IG1haWxzKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBsZXQgc2VsZWN0ZWRFbWFpbENvbnRlbnRUeXBlID0gJCgnLmpzLWVtYWlsLWZvcm0tZ3JvdXAnKS5maW5kKCdzZWxlY3QnKS52YWwoKTtcbiAgICAgICAgbGV0ICR0aGVtZXNGb3JtR3JvdXAgPSAkKCcuanMtdGhlbWUtZm9ybS1ncm91cCcpO1xuICAgICAgICBsZXQgJG5vVGhlbWVPcHRpb24gPSAkdGhlbWVzRm9ybUdyb3VwLmZpbmQoJy5qcy1uby10aGVtZScpO1xuXG4gICAgICAgIGlmIChzZWxlY3RlZEVtYWlsQ29udGVudFR5cGUgPT09IGVtYWlsQ29udGVudEJvZHkpIHtcbiAgICAgICAgICAgICRub1RoZW1lT3B0aW9uLnByb3AoJ3NlbGVjdGVkJywgdHJ1ZSk7XG4gICAgICAgICAgICB0aGlzLl9zaG93KCRub1RoZW1lT3B0aW9uLCAkdGhlbWVzRm9ybUdyb3VwKTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIHRoaXMuX2hpZGUoJG5vVGhlbWVPcHRpb24sICR0aGVtZXNGb3JtR3JvdXApO1xuICAgICAgICB9XG4gICAgfVxuXG5cbiAgICAvKipcbiAgICAgKiBNYWtlIGFsbCBnaXZlbiBzZWxlY3RvcnMgaGlkZGVuXG4gICAgICpcbiAgICAgKiBAcGFyYW0gJHNlbGVjdG9yc1xuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgX2hpZGUoLi4uJHNlbGVjdG9ycykge1xuICAgICAgICBmb3IgKGxldCBrZXkgaW4gJHNlbGVjdG9ycykge1xuICAgICAgICAgICAgJHNlbGVjdG9yc1trZXldLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgICAgICAgICAgICRzZWxlY3RvcnNba2V5XS5maW5kKCdzZWxlY3QnKS5wcm9wKCdkaXNhYmxlZCcsICdkaXNhYmxlZCcpO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogTWFrZSBhbGwgZ2l2ZW4gc2VsZWN0b3JzIHZpc2libGVcbiAgICAgKlxuICAgICAqIEBwYXJhbSAkc2VsZWN0b3JzXG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBfc2hvdyguLi4kc2VsZWN0b3JzKSB7XG4gICAgICAgIGZvciAobGV0IGtleSBpbiAkc2VsZWN0b3JzKSB7XG4gICAgICAgICAgICAkc2VsZWN0b3JzW2tleV0ucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICAgICAgICAgICAgJHNlbGVjdG9yc1trZXldLmZpbmQoJ3NlbGVjdCcpLnByb3AoJ2Rpc2FibGVkJywgZmFsc2UpO1xuICAgICAgICB9XG4gICAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvdHJhbnNsYXRpb24tc2V0dGluZ3MvRm9ybUZpZWxkVG9nZ2xlLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IFRyYW5zbGF0aW9uU2V0dGluZ3NQYWdlIGZyb20gJy4vVHJhbnNsYXRpb25TZXR0aW5nc1BhZ2UnO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbiQoKCkgPT4ge1xuICAgIG5ldyBUcmFuc2xhdGlvblNldHRpbmdzUGFnZSgpO1xufSk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy90cmFuc2xhdGlvbi1zZXR0aW5ncy9pbmRleC5qcyJdLCJzb3VyY2VSb290IjoiIn0=