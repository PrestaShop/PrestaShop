window["themes"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 350);
/******/ })
/************************************************************************/
/******/ ({

/***/ 246:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


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

var _multiStoreRestrictionFieldMap = __webpack_require__(309);

var _multiStoreRestrictionFieldMap2 = _interopRequireDefault(_multiStoreRestrictionFieldMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.$;

/**
 * Enables multi store functionality for the page. It includes switch functionality and checkboxes
 */

var MultiStoreRestrictionField = function () {
  function MultiStoreRestrictionField() {
    var _this = this;

    _classCallCheck(this, MultiStoreRestrictionField);

    $(document).on('change', _multiStoreRestrictionFieldMap2.default.multiStoreRestrictionCheckbox, function (e) {
      return _this._multiStoreRestrictionCheckboxFieldChangeEvent(e);
    });

    $(document).on('change', _multiStoreRestrictionFieldMap2.default.multiStoreRestrictionSwitch, function (e) {
      return _this._multiStoreRestrictionSwitchFieldChangeEvent(e);
    });
  }

  /**
   * Toggles the checkbox field and enables or disables its related field.
   *
   * @param {Event} e
   * @private
   */


  _createClass(MultiStoreRestrictionField, [{
    key: '_multiStoreRestrictionCheckboxFieldChangeEvent',
    value: function _multiStoreRestrictionCheckboxFieldChangeEvent(e) {
      var $currentItem = $(e.currentTarget);

      this._toggleSourceFieldByTargetElement($currentItem, !$currentItem.is(':checked'));
    }

    /**
     * Mass updates multi-store checkbox fields - it enables or disabled the switch and after that
     * it calls the function
     * which handles the toggle update related form field by its current state.
     * @param {Event} e
     * @private
     */

  }, {
    key: '_multiStoreRestrictionSwitchFieldChangeEvent',
    value: function _multiStoreRestrictionSwitchFieldChangeEvent(e) {
      var _this2 = this;

      var $currentItem = $(e.currentTarget);
      var isSelected = 1 === parseInt($currentItem.val(), 10);
      var targetFormName = $currentItem.data('targetFormName');

      $('form[name="' + targetFormName + '"]').find(_multiStoreRestrictionFieldMap2.default.multiStoreRestrictionCheckbox).each(function (index, el) {
        var $el = $(el);
        $el.prop('checked', isSelected);
        _this2._toggleSourceFieldByTargetElement($el, !isSelected);
      });
    }

    /**
     * Changes related form fields state to disabled or enabled.
     * It also toggles class disabled since for some fields
     * this class is used instead of the native disabled attribute.
     *
     * @param {jquery} $targetElement
     * @param {boolean} isDisabled
     * @private
     */

  }, {
    key: '_toggleSourceFieldByTargetElement',
    value: function _toggleSourceFieldByTargetElement($targetElement, isDisabled) {
      var targetValue = $targetElement.data('shopRestrictionTarget');
      var $sourceFieldSelector = $('[data-shop-restriction-source="' + targetValue + '"]');
      $sourceFieldSelector.prop('disabled', isDisabled);
      $sourceFieldSelector.toggleClass('disabled', isDisabled);
    }
  }]);

  return MultiStoreRestrictionField;
}();

exports.default = MultiStoreRestrictionField;

/***/ }),

/***/ 269:
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
 * This handler displays delete theme modal and handles the submit action.
 */

var DeleteThemeHandler = function () {
  function DeleteThemeHandler() {
    var _this = this;

    _classCallCheck(this, DeleteThemeHandler);

    $(document).on('click', '.js-display-delete-theme-modal', function (e) {
      return _this._displayDeleteThemeModal(e);
    });
  }

  /**
   * Displays modal with its own event handling.
   *
   * @param e
   * @private
   */


  _createClass(DeleteThemeHandler, [{
    key: '_displayDeleteThemeModal',
    value: function _displayDeleteThemeModal(e) {
      var $modal = $('#delete_theme_modal');

      $modal.modal('show');

      this._submitForm($modal, e);
    }

    /**
     * Submits form by adding click event listener for modal and calling original form event.
     *
     * @param $modal
     * @param originalButtonEvent
     *
     * @private
     */

  }, {
    key: '_submitForm',
    value: function _submitForm($modal, originalButtonEvent) {
      var $formButton = $(originalButtonEvent.currentTarget);

      $modal.on('click', '.js-submit-delete-theme', function () {
        var $form = $formButton.closest('form');
        $form.submit();
      });
    }
  }]);

  return DeleteThemeHandler;
}();

exports.default = DeleteThemeHandler;

/***/ }),

/***/ 270:
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
 * Handles "Reset to defaults" action submitting on button click.
 */

var ResetThemeLayoutsHandler = function () {
  function ResetThemeLayoutsHandler() {
    var _this = this;

    _classCallCheck(this, ResetThemeLayoutsHandler);

    $(document).on('click', '.js-reset-theme-layouts-btn', function (e) {
      return _this._handleResetting(e);
    });

    return {};
  }

  /**
   * @param {Event} event
   *
   * @private
   */


  _createClass(ResetThemeLayoutsHandler, [{
    key: '_handleResetting',
    value: function _handleResetting(event) {
      var $btn = $(event.currentTarget);

      var $form = $('<form>', {
        'action': $btn.data('submit-url'),
        'method': 'POST'
      }).append($('<input>', {
        'name': 'token',
        'value': $btn.data('csrf-token'),
        'type': 'hidden'
      }));

      $form.appendTo('body');
      $form.submit();
    }
  }]);

  return ResetThemeLayoutsHandler;
}();

exports.default = ResetThemeLayoutsHandler;

/***/ }),

/***/ 271:
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
 * This handler displays use theme modal and handles the submit form logic.
 */

var UseThemeHandler = function () {
  function UseThemeHandler() {
    var _this = this;

    _classCallCheck(this, UseThemeHandler);

    $(document).on('click', '.js-display-use-theme-modal', function (e) {
      return _this._displayUseThemeModal(e);
    });
  }

  /**
   * Displays modal with its own event handling.
   *
   * @param e
   * @private
   */


  _createClass(UseThemeHandler, [{
    key: '_displayUseThemeModal',
    value: function _displayUseThemeModal(e) {
      var $modal = $('#use_theme_modal');

      $modal.modal('show');

      this._submitForm($modal, e);
    }

    /**
     * Submits form by adding click event listener for modal and calling original form event.
     *
     * @param $modal
     * @param originalButtonEvent
     *
     * @private
     */

  }, {
    key: '_submitForm',
    value: function _submitForm($modal, originalButtonEvent) {
      var $formButton = $(originalButtonEvent.currentTarget);

      $modal.on('click', '.js-submit-use-theme', function () {
        var $form = $formButton.closest('form');
        $form.submit();
      });
    }
  }]);

  return UseThemeHandler;
}();

exports.default = UseThemeHandler;

/***/ }),

/***/ 309:
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
 * Encapsulates selectors for multi store restriction component
 */
exports.default = {
  multiStoreRestrictionCheckbox: '.js-multi-store-restriction-checkbox',
  multiStoreRestrictionSwitch: '.js-multi-store-restriction-switch'
};

/***/ }),

/***/ 350:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _resetThemeLayoutsHandler = __webpack_require__(270);

var _resetThemeLayoutsHandler2 = _interopRequireDefault(_resetThemeLayoutsHandler);

var _useThemeHandler = __webpack_require__(271);

var _useThemeHandler2 = _interopRequireDefault(_useThemeHandler);

var _multiStoreRestrictionField = __webpack_require__(246);

var _multiStoreRestrictionField2 = _interopRequireDefault(_multiStoreRestrictionField);

var _deleteThemeHandler = __webpack_require__(269);

var _deleteThemeHandler2 = _interopRequireDefault(_deleteThemeHandler);

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
  new _resetThemeLayoutsHandler2.default();
  new _multiStoreRestrictionField2.default();
  new _useThemeHandler2.default();
  new _deleteThemeHandler2.default();
});

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNjhlODI5MWYxMzYwNzBmMjc2YmQ/MjBkNCoqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9tdWx0aS1zdG9yZS1yZXN0cmljdGlvbi1maWVsZC9tdWx0aS1zdG9yZS1yZXN0cmljdGlvbi1maWVsZC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy90aGVtZXMvZGVsZXRlLXRoZW1lLWhhbmRsZXIuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvdGhlbWVzL3Jlc2V0LXRoZW1lLWxheW91dHMtaGFuZGxlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy90aGVtZXMvdXNlLXRoZW1lLWhhbmRsZXIuanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9tdWx0aS1zdG9yZS1yZXN0cmljdGlvbi1maWVsZC9tdWx0aS1zdG9yZS1yZXN0cmljdGlvbi1maWVsZC1tYXAuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvdGhlbWVzL2luZGV4LmpzIl0sIm5hbWVzIjpbIiQiLCJ3aW5kb3ciLCJNdWx0aVN0b3JlUmVzdHJpY3Rpb25GaWVsZCIsImRvY3VtZW50Iiwib24iLCJtdWx0aVN0b3JlUmVzdHJpY3Rpb25GaWVsZE1hcCIsIm11bHRpU3RvcmVSZXN0cmljdGlvbkNoZWNrYm94IiwiX211bHRpU3RvcmVSZXN0cmljdGlvbkNoZWNrYm94RmllbGRDaGFuZ2VFdmVudCIsImUiLCJtdWx0aVN0b3JlUmVzdHJpY3Rpb25Td2l0Y2giLCJfbXVsdGlTdG9yZVJlc3RyaWN0aW9uU3dpdGNoRmllbGRDaGFuZ2VFdmVudCIsIiRjdXJyZW50SXRlbSIsImN1cnJlbnRUYXJnZXQiLCJfdG9nZ2xlU291cmNlRmllbGRCeVRhcmdldEVsZW1lbnQiLCJpcyIsImlzU2VsZWN0ZWQiLCJwYXJzZUludCIsInZhbCIsInRhcmdldEZvcm1OYW1lIiwiZGF0YSIsImZpbmQiLCJlYWNoIiwiaW5kZXgiLCJlbCIsIiRlbCIsInByb3AiLCIkdGFyZ2V0RWxlbWVudCIsImlzRGlzYWJsZWQiLCJ0YXJnZXRWYWx1ZSIsIiRzb3VyY2VGaWVsZFNlbGVjdG9yIiwidG9nZ2xlQ2xhc3MiLCJEZWxldGVUaGVtZUhhbmRsZXIiLCJfZGlzcGxheURlbGV0ZVRoZW1lTW9kYWwiLCIkbW9kYWwiLCJtb2RhbCIsIl9zdWJtaXRGb3JtIiwib3JpZ2luYWxCdXR0b25FdmVudCIsIiRmb3JtQnV0dG9uIiwiJGZvcm0iLCJjbG9zZXN0Iiwic3VibWl0IiwiUmVzZXRUaGVtZUxheW91dHNIYW5kbGVyIiwiX2hhbmRsZVJlc2V0dGluZyIsImV2ZW50IiwiJGJ0biIsImFwcGVuZCIsImFwcGVuZFRvIiwiVXNlVGhlbWVIYW5kbGVyIiwiX2Rpc3BsYXlVc2VUaGVtZU1vZGFsIl0sIm1hcHBpbmdzIjoiOztBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQSxtREFBMkMsY0FBYzs7QUFFekQ7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxhQUFLO0FBQ0w7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxtQ0FBMkIsMEJBQTBCLEVBQUU7QUFDdkQseUNBQWlDLGVBQWU7QUFDaEQ7QUFDQTtBQUNBOztBQUVBO0FBQ0EsOERBQXNELCtEQUErRDs7QUFFckg7QUFDQTs7QUFFQTtBQUNBOzs7Ozs7Ozs7Ozs7Ozs7cWpCQ2hFQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQXlCQTs7Ozs7Ozs7QUFFQSxJQUFNQSxJQUFJQyxPQUFPRCxDQUFqQjs7QUFFQTs7OztJQUdxQkUsMEI7QUFDbkIsd0NBQWM7QUFBQTs7QUFBQTs7QUFDWkYsTUFBRUcsUUFBRixFQUFZQyxFQUFaLENBQ0UsUUFERixF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LGNBQWNyQyxFQUFFb0Msb0JBQW9CeEIsYUFBdEIsQ0FBcEI7O0FBRUFxQixhQUFPN0IsRUFBUCxDQUFVLE9BQVYsRUFBbUIsc0JBQW5CLEVBQTJDLFlBQU07QUFDL0MsWUFBTWtDLFFBQVFELFlBQVlFLE9BQVosQ0FBb0IsTUFBcEIsQ0FBZDtBQUNBRCxjQUFNRSxNQUFOO0FBQ0QsT0FIRDtBQUlEOzs7Ozs7a0JBbENrQk8sZTs7Ozs7Ozs7Ozs7OztBQzlCckI7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUF5QkE7OztrQkFHZTtBQUNiekMsaUNBQStCLHNDQURsQjtBQUViRywrQkFBNkI7QUFGaEIsQzs7Ozs7Ozs7OztBQ0pmOzs7O0FBQ0E7Ozs7QUFDQTs7OztBQUNBOzs7Ozs7QUEzQkE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQTZCQSxJQUFNVCxJQUFJQyxPQUFPRCxDQUFqQjs7QUFFQUEsRUFBRSxZQUFNO0FBQ04sTUFBSXlDLGtDQUFKO0FBQ0EsTUFBSXZDLG9DQUFKO0FBQ0EsTUFBSTZDLHlCQUFKO0FBQ0EsTUFBSWhCLDRCQUFKO0FBQ0QsQ0FMRCxFIiwiZmlsZSI6InRoZW1lcy5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBpZGVudGl0eSBmdW5jdGlvbiBmb3IgY2FsbGluZyBoYXJtb255IGltcG9ydHMgd2l0aCB0aGUgY29ycmVjdCBjb250ZXh0XG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmkgPSBmdW5jdGlvbih2YWx1ZSkgeyByZXR1cm4gdmFsdWU7IH07XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDM1MCk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gd2VicGFjay9ib290c3RyYXAgNjhlODI5MWYxMzYwNzBmMjc2YmQiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgbXVsdGlTdG9yZVJlc3RyaWN0aW9uRmllbGRNYXAgZnJvbSAnLi9tdWx0aS1zdG9yZS1yZXN0cmljdGlvbi1maWVsZC1tYXAnO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogRW5hYmxlcyBtdWx0aSBzdG9yZSBmdW5jdGlvbmFsaXR5IGZvciB0aGUgcGFnZS4gSXQgaW5jbHVkZXMgc3dpdGNoIGZ1bmN0aW9uYWxpdHkgYW5kIGNoZWNrYm94ZXNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgTXVsdGlTdG9yZVJlc3RyaWN0aW9uRmllbGQge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICAkKGRvY3VtZW50KS5vbihcbiAgICAgICdjaGFuZ2UnLFxuICAgICAgbXVsdGlTdG9yZVJlc3RyaWN0aW9uRmllbGRNYXAubXVsdGlTdG9yZVJlc3RyaWN0aW9uQ2hlY2tib3gsXG4gICAgICBlID0+IHRoaXMuX211bHRpU3RvcmVSZXN0cmljdGlvbkNoZWNrYm94RmllbGRDaGFuZ2VFdmVudChlKVxuICAgICk7XG5cbiAgICAkKGRvY3VtZW50KS5vbihcbiAgICAgICdjaGFuZ2UnLFxuICAgICAgbXVsdGlTdG9yZVJlc3RyaWN0aW9uRmllbGRNYXAubXVsdGlTdG9yZVJlc3RyaWN0aW9uU3dpdGNoLFxuICAgICAgZSA9PiB0aGlzLl9tdWx0aVN0b3JlUmVzdHJpY3Rpb25Td2l0Y2hGaWVsZENoYW5nZUV2ZW50KGUpXG4gICAgKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBUb2dnbGVzIHRoZSBjaGVja2JveCBmaWVsZCBhbmQgZW5hYmxlcyBvciBkaXNhYmxlcyBpdHMgcmVsYXRlZCBmaWVsZC5cbiAgICpcbiAgICogQHBhcmFtIHtFdmVudH0gZVxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX211bHRpU3RvcmVSZXN0cmljdGlvbkNoZWNrYm94RmllbGRDaGFuZ2VFdmVudChlKSB7XG4gICAgY29uc3QgJGN1cnJlbnRJdGVtID0gJChlLmN1cnJlbnRUYXJnZXQpO1xuXG4gICAgdGhpcy5fdG9nZ2xlU291cmNlRmllbGRCeVRhcmdldEVsZW1lbnQoJGN1cnJlbnRJdGVtLCAhJGN1cnJlbnRJdGVtLmlzKCc6Y2hlY2tlZCcpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBNYXNzIHVwZGF0ZXMgbXVsdGktc3RvcmUgY2hlY2tib3ggZmllbGRzIC0gaXQgZW5hYmxlcyBvciBkaXNhYmxlZCB0aGUgc3dpdGNoIGFuZCBhZnRlciB0aGF0XG4gICAqIGl0IGNhbGxzIHRoZSBmdW5jdGlvblxuICAgKiB3aGljaCBoYW5kbGVzIHRoZSB0b2dnbGUgdXBkYXRlIHJlbGF0ZWQgZm9ybSBmaWVsZCBieSBpdHMgY3VycmVudCBzdGF0ZS5cbiAgICogQHBhcmFtIHtFdmVudH0gZVxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX211bHRpU3RvcmVSZXN0cmljdGlvblN3aXRjaEZpZWxkQ2hhbmdlRXZlbnQoZSkge1xuICAgIGNvbnN0ICRjdXJyZW50SXRlbSA9ICQoZS5jdXJyZW50VGFyZ2V0KTtcbiAgICBjb25zdCBpc1NlbGVjdGVkID0gMSA9PT0gcGFyc2VJbnQoJGN1cnJlbnRJdGVtLnZhbCgpLCAxMCk7XG4gICAgY29uc3QgdGFyZ2V0Rm9ybU5hbWUgPSAkY3VycmVudEl0ZW0uZGF0YSgndGFyZ2V0Rm9ybU5hbWUnKTtcblxuICAgICQoYGZvcm1bbmFtZT1cIiR7dGFyZ2V0Rm9ybU5hbWV9XCJdYCkuZmluZChtdWx0aVN0b3JlUmVzdHJpY3Rpb25GaWVsZE1hcC5tdWx0aVN0b3JlUmVzdHJpY3Rpb25DaGVja2JveCkuZWFjaCgoaW5kZXgsIGVsKSA9PiB7XG4gICAgICBjb25zdCAkZWwgPSAkKGVsKTtcbiAgICAgICRlbC5wcm9wKCdjaGVja2VkJywgaXNTZWxlY3RlZCk7XG4gICAgICB0aGlzLl90b2dnbGVTb3VyY2VGaWVsZEJ5VGFyZ2V0RWxlbWVudCgkZWwsICFpc1NlbGVjdGVkKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBDaGFuZ2VzIHJlbGF0ZWQgZm9ybSBmaWVsZHMgc3RhdGUgdG8gZGlzYWJsZWQgb3IgZW5hYmxlZC5cbiAgICogSXQgYWxzbyB0b2dnbGVzIGNsYXNzIGRpc2FibGVkIHNpbmNlIGZvciBzb21lIGZpZWxkc1xuICAgKiB0aGlzIGNsYXNzIGlzIHVzZWQgaW5zdGVhZCBvZiB0aGUgbmF0aXZlIGRpc2FibGVkIGF0dHJpYnV0ZS5cbiAgICpcbiAgICogQHBhcmFtIHtqcXVlcnl9ICR0YXJnZXRFbGVtZW50XG4gICAqIEBwYXJhbSB7Ym9vbGVhbn0gaXNEaXNhYmxlZFxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3RvZ2dsZVNvdXJjZUZpZWxkQnlUYXJnZXRFbGVtZW50KCR0YXJnZXRFbGVtZW50LCBpc0Rpc2FibGVkKSB7XG4gICAgY29uc3QgdGFyZ2V0VmFsdWUgPSAkdGFyZ2V0RWxlbWVudC5kYXRhKCdzaG9wUmVzdHJpY3Rpb25UYXJnZXQnKTtcbiAgICBjb25zdCAkc291cmNlRmllbGRTZWxlY3RvciA9ICQoYFtkYXRhLXNob3AtcmVzdHJpY3Rpb24tc291cmNlPVwiJHt0YXJnZXRWYWx1ZX1cIl1gKTtcbiAgICAkc291cmNlRmllbGRTZWxlY3Rvci5wcm9wKCdkaXNhYmxlZCcsIGlzRGlzYWJsZWQpO1xuICAgICRzb3VyY2VGaWVsZFNlbGVjdG9yLnRvZ2dsZUNsYXNzKCdkaXNhYmxlZCcsIGlzRGlzYWJsZWQpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL211bHRpLXN0b3JlLXJlc3RyaWN0aW9uLWZpZWxkL211bHRpLXN0b3JlLXJlc3RyaWN0aW9uLWZpZWxkLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIFRoaXMgaGFuZGxlciBkaXNwbGF5cyBkZWxldGUgdGhlbWUgbW9kYWwgYW5kIGhhbmRsZXMgdGhlIHN1Ym1pdCBhY3Rpb24uXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIERlbGV0ZVRoZW1lSGFuZGxlciB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsICcuanMtZGlzcGxheS1kZWxldGUtdGhlbWUtbW9kYWwnLCBlID0+IHRoaXMuX2Rpc3BsYXlEZWxldGVUaGVtZU1vZGFsKGUpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBEaXNwbGF5cyBtb2RhbCB3aXRoIGl0cyBvd24gZXZlbnQgaGFuZGxpbmcuXG4gICAqXG4gICAqIEBwYXJhbSBlXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfZGlzcGxheURlbGV0ZVRoZW1lTW9kYWwoZSkge1xuICAgIGNvbnN0ICRtb2RhbCA9ICQoJyNkZWxldGVfdGhlbWVfbW9kYWwnKTtcblxuICAgICRtb2RhbC5tb2RhbCgnc2hvdycpO1xuXG4gICAgdGhpcy5fc3VibWl0Rm9ybSgkbW9kYWwsIGUpO1xuICB9XG5cbiAgLyoqXG4gICAqIFN1Ym1pdHMgZm9ybSBieSBhZGRpbmcgY2xpY2sgZXZlbnQgbGlzdGVuZXIgZm9yIG1vZGFsIGFuZCBjYWxsaW5nIG9yaWdpbmFsIGZvcm0gZXZlbnQuXG4gICAqXG4gICAqIEBwYXJhbSAkbW9kYWxcbiAgICogQHBhcmFtIG9yaWdpbmFsQnV0dG9uRXZlbnRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zdWJtaXRGb3JtKCRtb2RhbCwgb3JpZ2luYWxCdXR0b25FdmVudCkge1xuICAgIGNvbnN0ICRmb3JtQnV0dG9uID0gJChvcmlnaW5hbEJ1dHRvbkV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuXG4gICAgJG1vZGFsLm9uKCdjbGljaycsICcuanMtc3VibWl0LWRlbGV0ZS10aGVtZScsICgpID0+IHtcbiAgICAgIGNvbnN0ICRmb3JtID0gJGZvcm1CdXR0b24uY2xvc2VzdCgnZm9ybScpO1xuICAgICAgJGZvcm0uc3VibWl0KCk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL3RoZW1lcy9kZWxldGUtdGhlbWUtaGFuZGxlci5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBIYW5kbGVzIFwiUmVzZXQgdG8gZGVmYXVsdHNcIiBhY3Rpb24gc3VibWl0dGluZyBvbiBidXR0b24gY2xpY2suXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFJlc2V0VGhlbWVMYXlvdXRzSGFuZGxlciB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsICcuanMtcmVzZXQtdGhlbWUtbGF5b3V0cy1idG4nLCAoZSkgPT4gdGhpcy5faGFuZGxlUmVzZXR0aW5nKGUpKTtcblxuICAgIHJldHVybiB7fTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcGFyYW0ge0V2ZW50fSBldmVudFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hhbmRsZVJlc2V0dGluZyhldmVudCkge1xuICAgIGNvbnN0ICRidG4gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuXG4gICAgY29uc3QgJGZvcm0gPSAkKCc8Zm9ybT4nLCB7XG4gICAgICAnYWN0aW9uJzogJGJ0bi5kYXRhKCdzdWJtaXQtdXJsJyksXG4gICAgICAnbWV0aG9kJzogJ1BPU1QnXG4gICAgfSkuYXBwZW5kKCQoJzxpbnB1dD4nLCB7XG4gICAgICAnbmFtZSc6ICd0b2tlbicsXG4gICAgICAndmFsdWUnOiAkYnRuLmRhdGEoJ2NzcmYtdG9rZW4nKSxcbiAgICAgICd0eXBlJzogJ2hpZGRlbidcbiAgICB9KSk7XG5cbiAgICAkZm9ybS5hcHBlbmRUbygnYm9keScpO1xuICAgICRmb3JtLnN1Ym1pdCgpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy90aGVtZXMvcmVzZXQtdGhlbWUtbGF5b3V0cy1oYW5kbGVyLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIFRoaXMgaGFuZGxlciBkaXNwbGF5cyB1c2UgdGhlbWUgbW9kYWwgYW5kIGhhbmRsZXMgdGhlIHN1Ym1pdCBmb3JtIGxvZ2ljLlxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBVc2VUaGVtZUhhbmRsZXIge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCAnLmpzLWRpc3BsYXktdXNlLXRoZW1lLW1vZGFsJywgZSA9PiB0aGlzLl9kaXNwbGF5VXNlVGhlbWVNb2RhbChlKSk7XG4gIH1cblxuICAvKipcbiAgICogRGlzcGxheXMgbW9kYWwgd2l0aCBpdHMgb3duIGV2ZW50IGhhbmRsaW5nLlxuICAgKlxuICAgKiBAcGFyYW0gZVxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2Rpc3BsYXlVc2VUaGVtZU1vZGFsKGUpIHtcbiAgICBjb25zdCAkbW9kYWwgPSAkKCcjdXNlX3RoZW1lX21vZGFsJyk7XG5cbiAgICAkbW9kYWwubW9kYWwoJ3Nob3cnKTtcblxuICAgIHRoaXMuX3N1Ym1pdEZvcm0oJG1vZGFsLCBlKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTdWJtaXRzIGZvcm0gYnkgYWRkaW5nIGNsaWNrIGV2ZW50IGxpc3RlbmVyIGZvciBtb2RhbCBhbmQgY2FsbGluZyBvcmlnaW5hbCBmb3JtIGV2ZW50LlxuICAgKlxuICAgKiBAcGFyYW0gJG1vZGFsXG4gICAqIEBwYXJhbSBvcmlnaW5hbEJ1dHRvbkV2ZW50XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc3VibWl0Rm9ybSgkbW9kYWwsIG9yaWdpbmFsQnV0dG9uRXZlbnQpIHtcbiAgICBjb25zdCAkZm9ybUJ1dHRvbiA9ICQob3JpZ2luYWxCdXR0b25FdmVudC5jdXJyZW50VGFyZ2V0KTtcblxuICAgICRtb2RhbC5vbignY2xpY2snLCAnLmpzLXN1Ym1pdC11c2UtdGhlbWUnLCAoKSA9PiB7XG4gICAgICBjb25zdCAkZm9ybSA9ICRmb3JtQnV0dG9uLmNsb3Nlc3QoJ2Zvcm0nKTtcbiAgICAgICRmb3JtLnN1Ym1pdCgpO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy90aGVtZXMvdXNlLXRoZW1lLWhhbmRsZXIuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG4vKipcbiAqIEVuY2Fwc3VsYXRlcyBzZWxlY3RvcnMgZm9yIG11bHRpIHN0b3JlIHJlc3RyaWN0aW9uIGNvbXBvbmVudFxuICovXG5leHBvcnQgZGVmYXVsdCB7XG4gIG11bHRpU3RvcmVSZXN0cmljdGlvbkNoZWNrYm94OiAnLmpzLW11bHRpLXN0b3JlLXJlc3RyaWN0aW9uLWNoZWNrYm94JyxcbiAgbXVsdGlTdG9yZVJlc3RyaWN0aW9uU3dpdGNoOiAnLmpzLW11bHRpLXN0b3JlLXJlc3RyaWN0aW9uLXN3aXRjaCcsXG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL211bHRpLXN0b3JlLXJlc3RyaWN0aW9uLWZpZWxkL211bHRpLXN0b3JlLXJlc3RyaWN0aW9uLWZpZWxkLW1hcC5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5pbXBvcnQgUmVzZXRUaGVtZUxheW91dHNIYW5kbGVyIGZyb20gJy4vcmVzZXQtdGhlbWUtbGF5b3V0cy1oYW5kbGVyJztcbmltcG9ydCBVc2VUaGVtZUhhbmRsZXIgZnJvbSAnLi91c2UtdGhlbWUtaGFuZGxlcic7XG5pbXBvcnQgTXVsdGlTdG9yZVJlc3RyaWN0aW9uRmllbGQgZnJvbSAnLi4vLi4vY29tcG9uZW50cy9tdWx0aS1zdG9yZS1yZXN0cmljdGlvbi1maWVsZC9tdWx0aS1zdG9yZS1yZXN0cmljdGlvbi1maWVsZCc7XG5pbXBvcnQgRGVsZXRlVGhlbWVIYW5kbGVyIGZyb20gJy4vZGVsZXRlLXRoZW1lLWhhbmRsZXInO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbiQoKCkgPT4ge1xuICBuZXcgUmVzZXRUaGVtZUxheW91dHNIYW5kbGVyKCk7XG4gIG5ldyBNdWx0aVN0b3JlUmVzdHJpY3Rpb25GaWVsZCgpO1xuICBuZXcgVXNlVGhlbWVIYW5kbGVyKCk7XG4gIG5ldyBEZWxldGVUaGVtZUhhbmRsZXIoKTtcbn0pO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvdGhlbWVzL2luZGV4LmpzIl0sInNvdXJjZVJvb3QiOiIifQ==