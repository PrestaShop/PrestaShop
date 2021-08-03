window["customer"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 491);
/******/ })
/************************************************************************/
/******/ ({

/***/ 0:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

exports.default = function (instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
};

/***/ }),

/***/ 1:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

var _defineProperty = __webpack_require__(19);

var _defineProperty2 = _interopRequireDefault(_defineProperty);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = function () {
  function defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      (0, _defineProperty2.default)(target, descriptor.key, descriptor);
    }
  }

  return function (Constructor, protoProps, staticProps) {
    if (protoProps) defineProperties(Constructor.prototype, protoProps);
    if (staticProps) defineProperties(Constructor, staticProps);
    return Constructor;
  };
}();

/***/ }),

/***/ 10:
/***/ (function(module, exports, __webpack_require__) {

var dP         = __webpack_require__(6)
  , createDesc = __webpack_require__(12);
module.exports = __webpack_require__(2) ? function(object, key, value){
  return dP.f(object, key, createDesc(1, value));
} : function(object, key, value){
  object[key] = value;
  return object;
};

/***/ }),

/***/ 11:
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__(4);
module.exports = function(it){
  if(!isObject(it))throw TypeError(it + ' is not an object!');
  return it;
};

/***/ }),

/***/ 12:
/***/ (function(module, exports) {

module.exports = function(bitmap, value){
  return {
    enumerable  : !(bitmap & 1),
    configurable: !(bitmap & 2),
    writable    : !(bitmap & 4),
    value       : value
  };
};

/***/ }),

/***/ 13:
/***/ (function(module, exports, __webpack_require__) {

// optional / simple context binding
var aFunction = __webpack_require__(18);
module.exports = function(fn, that, length){
  aFunction(fn);
  if(that === undefined)return fn;
  switch(length){
    case 1: return function(a){
      return fn.call(that, a);
    };
    case 2: return function(a, b){
      return fn.call(that, a, b);
    };
    case 3: return function(a, b, c){
      return fn.call(that, a, b, c);
    };
  }
  return function(/* ...args */){
    return fn.apply(that, arguments);
  };
};

/***/ }),

/***/ 133:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

var $ = window.$;

/**
 * ChoiceTable is responsible for managing common actions in choice table form type
 */

var ChoiceTable = function () {
  /**
   * Init constructor
   */
  function ChoiceTable() {
    var _this = this;

    (0, _classCallCheck3.default)(this, ChoiceTable);

    $(document).on('change', '.js-choice-table-select-all', function (e) {
      _this.handleSelectAll(e);
    });
  }

  /**
   * Check/uncheck all boxes in table
   *
   * @param {Event} event
   */


  (0, _createClass3.default)(ChoiceTable, [{
    key: 'handleSelectAll',
    value: function handleSelectAll(event) {
      var $selectAllCheckboxes = $(event.target);
      var isSelectAllChecked = $selectAllCheckboxes.is(':checked');

      $selectAllCheckboxes.closest('table').find('tbody input:checkbox').prop('checked', isSelectAllChecked);
    }
  }]);
  return ChoiceTable;
}();

exports.default = ChoiceTable;

/***/ }),

/***/ 134:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

var $ = window.$;

/**
 * Component which allows submitting very simple forms without having to use <form> element.
 *
 * Useful when performing actions on resource where URL contains all needed data.
 * For example, to toggle category status via "POST /categories/2/toggle-status)"
 * or delete cover image via "POST /categories/2/delete-cover-image".
 *
 * Usage example in template:
 *
 * <button class="js-form-submit-btn"
 *         data-form-submit-url="/my-custom-url"          // (required) URL to which form will be submitted
 *         data-method="GET|POST|DELETE|PATCH"            // (optional) specify the verb to use for the request.
 *                                                        // POST is taken by default if not value is set
 *         data-form-csrf-token="my-generated-csrf-token" // (optional) to increase security
 *         data-form-confirm-message="Are you sure?"      // (optional) to confirm action before submit
 *         type="button"                                  // make sure its simple button
 *                                                        // so we can avoid submitting actual form
 *                                                        // when our button is defined inside form
 * >
 *     Click me to submit form
 * </button>
 *
 * In page specific JS you have to enable this feature:
 *
 * new FormSubmitButton();
 */

var FormSubmitButton = function FormSubmitButton() {
  (0, _classCallCheck3.default)(this, FormSubmitButton);

  $(document).on('click', '.js-form-submit-btn', function (event) {
    event.preventDefault();

    var $btn = $(this);

    if ($btn.data('form-confirm-message') && false === confirm($btn.data('form-confirm-message'))) {
      return;
    }

    var method = 'POST';
    var addInput = null;

    if ($btn.data('method')) {
      var btnMethod = $btn.data('method');
      var isGetOrPostMethod = ['GET', 'POST'].includes(btnMethod);
      method = isGetOrPostMethod ? btnMethod : 'POST';

      if (!isGetOrPostMethod) {
        addInput = $('<input>', {
          type: '_hidden',
          name: '_method',
          value: method
        });
      }
    }

    var $form = $('<form>', {
      'action': $btn.data('form-submit-url'),
      'method': method
    });

    if (addInput) {
      $form.append(addInput);
    }

    if ($btn.data('form-csrf-token')) {
      $form.append($('<input>', {
        'type': '_hidden',
        'name': '_csrf_token',
        'value': $btn.data('form-csrf-token')
      }));
    }

    $form.appendTo('body').submit();
  });
};

exports.default = FormSubmitButton;

/***/ }),

/***/ 14:
/***/ (function(module, exports, __webpack_require__) {

// 7.1.1 ToPrimitive(input [, PreferredType])
var isObject = __webpack_require__(4);
// instead of the ES6 spec version, we didn't implement @@toPrimitive case
// and the second argument - flag - preferred type is a string
module.exports = function(it, S){
  if(!isObject(it))return it;
  var fn, val;
  if(S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it)))return val;
  if(typeof (fn = it.valueOf) == 'function' && !isObject(val = fn.call(it)))return val;
  if(!S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it)))return val;
  throw TypeError("Can't convert object to primitive value");
};

/***/ }),

/***/ 16:
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__(4)
  , document = __webpack_require__(5).document
  // in old IE typeof document.createElement is 'object'
  , is = isObject(document) && isObject(document.createElement);
module.exports = function(it){
  return is ? document.createElement(it) : {};
};

/***/ }),

/***/ 17:
/***/ (function(module, exports, __webpack_require__) {

module.exports = !__webpack_require__(2) && !__webpack_require__(7)(function(){
  return Object.defineProperty(__webpack_require__(16)('div'), 'a', {get: function(){ return 7; }}).a != 7;
});

/***/ }),

/***/ 18:
/***/ (function(module, exports) {

module.exports = function(it){
  if(typeof it != 'function')throw TypeError(it + ' is not a function!');
  return it;
};

/***/ }),

/***/ 19:
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(20), __esModule: true };

/***/ }),

/***/ 2:
/***/ (function(module, exports, __webpack_require__) {

// Thank's IE8 for his funny defineProperty
module.exports = !__webpack_require__(7)(function(){
  return Object.defineProperty({}, 'a', {get: function(){ return 7; }}).a != 7;
});

/***/ }),

/***/ 20:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(21);
var $Object = __webpack_require__(3).Object;
module.exports = function defineProperty(it, key, desc){
  return $Object.defineProperty(it, key, desc);
};

/***/ }),

/***/ 21:
/***/ (function(module, exports, __webpack_require__) {

var $export = __webpack_require__(8);
// 19.1.2.4 / 15.2.3.6 Object.defineProperty(O, P, Attributes)
$export($export.S + $export.F * !__webpack_require__(2), 'Object', {defineProperty: __webpack_require__(6).f});

/***/ }),

/***/ 24:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

var $ = window.$;

/**
 * Class is responsible for handling Grid events
 */

var Grid = function () {
  /**
   * Grid id
   *
   * @param {string} id
   */
  function Grid(id) {
    (0, _classCallCheck3.default)(this, Grid);

    this.id = id;
    this.$container = $('#' + this.id + '_grid');
  }

  /**
   * Get grid id
   *
   * @returns {string}
   */


  (0, _createClass3.default)(Grid, [{
    key: 'getId',
    value: function getId() {
      return this.id;
    }

    /**
     * Get grid container
     *
     * @returns {jQuery}
     */

  }, {
    key: 'getContainer',
    value: function getContainer() {
      return this.$container;
    }

    /**
     * Get grid header container
     *
     * @returns {jQuery}
     */

  }, {
    key: 'getHeaderContainer',
    value: function getHeaderContainer() {
      return this.$container.closest('.js-grid-panel').find('.js-grid-header');
    }

    /**
     * Extend grid with external extensions
     *
     * @param {object} extension
     */

  }, {
    key: 'addExtension',
    value: function addExtension(extension) {
      extension.extend(this);
    }
  }]);
  return Grid;
}();

exports.default = Grid;

/***/ }),

/***/ 26:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _reset_search = __webpack_require__(43);

var _reset_search2 = _interopRequireDefault(_reset_search);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$;

/**
 * Class FiltersResetExtension extends grid with filters resetting
 */
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

var FiltersResetExtension = function () {
  function FiltersResetExtension() {
    (0, _classCallCheck3.default)(this, FiltersResetExtension);
  }

  (0, _createClass3.default)(FiltersResetExtension, [{
    key: 'extend',


    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      grid.getContainer().on('click', '.js-reset-search', function (event) {
        (0, _reset_search2.default)($(event.currentTarget).data('url'), $(event.currentTarget).data('redirect'));
      });
    }
  }]);
  return FiltersResetExtension;
}();

exports.default = FiltersResetExtension;

/***/ }),

/***/ 27:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _tableSorting = __webpack_require__(41);

var _tableSorting2 = _interopRequireDefault(_tableSorting);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Class ReloadListExtension extends grid with "List reload" action
 */
var SortingExtension = function () {
  function SortingExtension() {
    (0, _classCallCheck3.default)(this, SortingExtension);
  }

  (0, _createClass3.default)(SortingExtension, [{
    key: 'extend',

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      var $sortableTable = grid.getContainer().find('table.table');

      new _tableSorting2.default($sortableTable).attach();
    }
  }]);
  return SortingExtension;
}(); /**
      * Copyright since 2007 PrestaShop SA and Contributors
      * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
      *
      * NOTICE OF LICENSE
      *
      * This source file is subject to the Open Software License (OSL 3.0)
      * that is bundled with this package in the file LICENSE.md.
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
      * needs please refer to https://devdocs.prestashop.com/ for more information.
      *
      * @author    PrestaShop SA and Contributors <contact@prestashop.com>
      * @copyright Since 2007 PrestaShop SA and Contributors
      * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
      */

exports.default = SortingExtension;

/***/ }),

/***/ 28:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * Class ReloadListExtension extends grid with "List reload" action
 */
var ReloadListExtension = function () {
  function ReloadListExtension() {
    (0, _classCallCheck3.default)(this, ReloadListExtension);
  }

  (0, _createClass3.default)(ReloadListExtension, [{
    key: 'extend',

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      grid.getHeaderContainer().on('click', '.js-common_refresh_list-grid-action', function () {
        location.reload();
      });
    }
  }]);
  return ReloadListExtension;
}();

exports.default = ReloadListExtension;

/***/ }),

/***/ 29:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

var $ = window.$;

/**
 * Class ExportToSqlManagerExtension extends grid with exporting query to SQL Manager
 */

var ExportToSqlManagerExtension = function () {
  function ExportToSqlManagerExtension() {
    (0, _classCallCheck3.default)(this, ExportToSqlManagerExtension);
  }

  (0, _createClass3.default)(ExportToSqlManagerExtension, [{
    key: 'extend',

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      var _this = this;

      grid.getHeaderContainer().on('click', '.js-common_show_query-grid-action', function () {
        return _this._onShowSqlQueryClick(grid);
      });
      grid.getHeaderContainer().on('click', '.js-common_export_sql_manager-grid-action', function () {
        return _this._onExportSqlManagerClick(grid);
      });
    }

    /**
     * Invoked when clicking on the "show sql query" toolbar button
     *
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_onShowSqlQueryClick',
    value: function _onShowSqlQueryClick(grid) {
      var $sqlManagerForm = $('#' + grid.getId() + '_common_show_query_modal_form');
      this._fillExportForm($sqlManagerForm, grid);

      var $modal = $('#' + grid.getId() + '_grid_common_show_query_modal');
      $modal.modal('show');

      $modal.on('click', '.btn-sql-submit', function () {
        return $sqlManagerForm.submit();
      });
    }

    /**
     * Invoked when clicking on the "export to the sql query" toolbar button
     *
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_onExportSqlManagerClick',
    value: function _onExportSqlManagerClick(grid) {
      var $sqlManagerForm = $('#' + grid.getId() + '_common_show_query_modal_form');

      this._fillExportForm($sqlManagerForm, grid);

      $sqlManagerForm.submit();
    }

    /**
     * Fill export form with SQL and it's name
     *
     * @param {jQuery} $sqlManagerForm
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_fillExportForm',
    value: function _fillExportForm($sqlManagerForm, grid) {
      var query = grid.getContainer().find('.js-grid-table').data('query');

      $sqlManagerForm.find('textarea[name="sql"]').val(query);
      $sqlManagerForm.find('input[name="name"]').val(this._getNameFromBreadcrumb());
    }

    /**
     * Get export name from page's breadcrumb
     *
     * @return {String}
     *
     * @private
     */

  }, {
    key: '_getNameFromBreadcrumb',
    value: function _getNameFromBreadcrumb() {
      var $breadcrumbs = $('.header-toolbar').find('.breadcrumb-item');
      var name = '';

      $breadcrumbs.each(function (i, item) {
        var $breadcrumb = $(item);

        var breadcrumbTitle = 0 < $breadcrumb.find('a').length ? $breadcrumb.find('a').text() : $breadcrumb.text();

        if (0 < name.length) {
          name = name.concat(' > ');
        }

        name = name.concat(breadcrumbTitle);
      });

      return name;
    }
  }]);
  return ExportToSqlManagerExtension;
}();

exports.default = ExportToSqlManagerExtension;

/***/ }),

/***/ 3:
/***/ (function(module, exports) {

var core = module.exports = {version: '2.4.0'};
if(typeof __e == 'number')__e = core; // eslint-disable-line no-undef

/***/ }),

/***/ 30:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * Responsible for grid filters search and reset button availability when filter inputs changes.
 */
var FiltersSubmitButtonEnablerExtension = function () {
  function FiltersSubmitButtonEnablerExtension() {
    (0, _classCallCheck3.default)(this, FiltersSubmitButtonEnablerExtension);
  }

  (0, _createClass3.default)(FiltersSubmitButtonEnablerExtension, [{
    key: 'extend',


    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      var $filtersRow = grid.getContainer().find('.column-filters');
      $filtersRow.find('.grid-search-button').prop('disabled', true);

      $filtersRow.find('input:not(.js-bulk-action-select-all), select').on('input dp.change', function () {
        $filtersRow.find('.grid-search-button').prop('disabled', false);
        $filtersRow.find('.js-grid-reset-button').prop('hidden', false);
      });
    }
  }]);
  return FiltersSubmitButtonEnablerExtension;
}();

exports.default = FiltersSubmitButtonEnablerExtension;

/***/ }),

/***/ 31:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

var $ = window.$;

/**
 * Class BulkActionSelectCheckboxExtension
 */

var BulkActionCheckboxExtension = function () {
  function BulkActionCheckboxExtension() {
    (0, _classCallCheck3.default)(this, BulkActionCheckboxExtension);
  }

  (0, _createClass3.default)(BulkActionCheckboxExtension, [{
    key: 'extend',

    /**
     * Extend grid with bulk action checkboxes handling functionality
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      this._handleBulkActionCheckboxSelect(grid);
      this._handleBulkActionSelectAllCheckbox(grid);
    }

    /**
     * Handles "Select all" button in the grid
     *
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_handleBulkActionSelectAllCheckbox',
    value: function _handleBulkActionSelectAllCheckbox(grid) {
      var _this = this;

      grid.getContainer().on('change', '.js-bulk-action-select-all', function (e) {
        var $checkbox = $(e.currentTarget);

        var isChecked = $checkbox.is(':checked');
        if (isChecked) {
          _this._enableBulkActionsBtn(grid);
        } else {
          _this._disableBulkActionsBtn(grid);
        }

        grid.getContainer().find('.js-bulk-action-checkbox').prop('checked', isChecked);
      });
    }

    /**
     * Handles each bulk action checkbox select in the grid
     *
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_handleBulkActionCheckboxSelect',
    value: function _handleBulkActionCheckboxSelect(grid) {
      var _this2 = this;

      grid.getContainer().on('change', '.js-bulk-action-checkbox', function () {
        var checkedRowsCount = grid.getContainer().find('.js-bulk-action-checkbox:checked').length;

        if (checkedRowsCount > 0) {
          _this2._enableBulkActionsBtn(grid);
        } else {
          _this2._disableBulkActionsBtn(grid);
        }
      });
    }

    /**
     * Enable bulk actions button
     *
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_enableBulkActionsBtn',
    value: function _enableBulkActionsBtn(grid) {
      grid.getContainer().find('.js-bulk-actions-btn').prop('disabled', false);
    }

    /**
     * Disable bulk actions button
     *
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_disableBulkActionsBtn',
    value: function _disableBulkActionsBtn(grid) {
      grid.getContainer().find('.js-bulk-actions-btn').prop('disabled', true);
    }
  }]);
  return BulkActionCheckboxExtension;
}();

exports.default = BulkActionCheckboxExtension;

/***/ }),

/***/ 32:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _modal = __webpack_require__(47);

var _modal2 = _interopRequireDefault(_modal);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$;

/**
 * Handles submit of grid actions
 */
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

var SubmitBulkActionExtension = function () {
  function SubmitBulkActionExtension() {
    var _this = this;

    (0, _classCallCheck3.default)(this, SubmitBulkActionExtension);

    return {
      extend: function extend(grid) {
        return _this.extend(grid);
      }
    };
  }

  /**
   * Extend grid with bulk action submitting
   *
   * @param {Grid} grid
   */


  (0, _createClass3.default)(SubmitBulkActionExtension, [{
    key: 'extend',
    value: function extend(grid) {
      var _this2 = this;

      grid.getContainer().on('click', '.js-bulk-action-submit-btn', function (event) {
        _this2.submit(event, grid);
      });
    }

    /**
     * Handle bulk action submitting
     *
     * @param {Event} event
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: 'submit',
    value: function submit(event, grid) {
      var $submitBtn = $(event.currentTarget);
      var confirmMessage = $submitBtn.data('confirm-message');
      var confirmTitle = $submitBtn.data('confirmTitle');

      if (confirmMessage !== undefined && 0 < confirmMessage.length) {
        if (confirmTitle !== undefined) {
          this.showConfirmModal($submitBtn, grid, confirmMessage, confirmTitle);
        } else if (confirm(confirmMessage)) {
          this.postForm($submitBtn, grid);
        }
      } else {
        this.postForm($submitBtn, grid);
      }
    }

    /**
     * @param {jQuery} $submitBtn
     * @param {Grid} grid
     * @param {string} confirmMessage
     * @param {string} confirmTitle
     */

  }, {
    key: 'showConfirmModal',
    value: function showConfirmModal($submitBtn, grid, confirmMessage, confirmTitle) {
      var _this3 = this;

      var confirmButtonLabel = $submitBtn.data('confirmButtonLabel');
      var closeButtonLabel = $submitBtn.data('closeButtonLabel');
      var confirmButtonClass = $submitBtn.data('confirmButtonClass');

      var modal = new _modal2.default({
        id: grid.getId() + '_grid_confirm_modal',
        confirmTitle: confirmTitle,
        confirmMessage: confirmMessage,
        confirmButtonLabel: confirmButtonLabel,
        closeButtonLabel: closeButtonLabel,
        confirmButtonClass: confirmButtonClass
      }, function () {
        return _this3.postForm($submitBtn, grid);
      });

      modal.show();
    }

    /**
     * @param {jQuery} $submitBtn
     * @param {Grid} grid
     */

  }, {
    key: 'postForm',
    value: function postForm($submitBtn, grid) {
      var $form = $('#' + grid.getId() + '_filter_form');

      $form.attr('action', $submitBtn.data('form-url'));
      $form.attr('method', $submitBtn.data('form-method'));
      $form.submit();
    }
  }]);
  return SubmitBulkActionExtension;
}();

exports.default = SubmitBulkActionExtension;

/***/ }),

/***/ 34:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

var $ = window.$;

/**
 * Class LinkRowActionExtension handles link row actions
 */

var LinkRowActionExtension = function () {
  function LinkRowActionExtension() {
    (0, _classCallCheck3.default)(this, LinkRowActionExtension);
  }

  (0, _createClass3.default)(LinkRowActionExtension, [{
    key: 'extend',

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      this.initRowLinks(grid);
      this.initConfirmableActions(grid);
    }

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */

  }, {
    key: 'initConfirmableActions',
    value: function initConfirmableActions(grid) {
      grid.getContainer().on('click', '.js-link-row-action', function (event) {
        var confirmMessage = $(event.currentTarget).data('confirm-message');

        if (confirmMessage.length && !confirm(confirmMessage)) {
          event.preventDefault();
        }
      });
    }

    /**
     * Add a click event on rows that matches the first link action (if present)
     *
     * @param {Grid} grid
     */

  }, {
    key: 'initRowLinks',
    value: function initRowLinks(grid) {
      $('tr', grid.getContainer()).each(function initEachRow() {
        var $parentRow = $(this);

        $('.js-link-row-action[data-clickable-row=1]:first', $parentRow).each(function propagateFirstLinkAction() {
          var $rowAction = $(this);
          var $parentCell = $rowAction.closest('td');

          var clickableCells = $('td.clickable', $parentRow).not($parentCell);

          clickableCells.addClass('cursor-pointer').click(function () {
            var confirmMessage = $rowAction.data('confirm-message');

            if (!confirmMessage.length || confirm(confirmMessage)) {
              document.location = $rowAction.attr('href');
            }
          });
        });
      });
    }
  }]);
  return LinkRowActionExtension;
}();

exports.default = LinkRowActionExtension;

/***/ }),

/***/ 387:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

var $ = window.$;

/**
 * Handles bulk delete for "Customers" grid.
 */

var DeleteCustomersBulkActionExtension = function () {
  function DeleteCustomersBulkActionExtension() {
    var _this = this;

    (0, _classCallCheck3.default)(this, DeleteCustomersBulkActionExtension);

    return {
      extend: function extend(grid) {
        return _this.extend(grid);
      }
    };
  }

  /**
   * Extend grid
   *
   * @param {Grid} grid
   */


  (0, _createClass3.default)(DeleteCustomersBulkActionExtension, [{
    key: 'extend',
    value: function extend(grid) {
      var _this2 = this;

      grid.getContainer().on('click', '.js-delete-customers-bulk-action', function (event) {
        event.preventDefault();

        var submitUrl = $(event.currentTarget).data('customers-delete-url');

        var $modal = $('#' + grid.getId() + '_grid_delete_customers_modal');
        $modal.modal('show');

        $modal.on('click', '.js-submit-delete-customers', function () {
          var $selectedCustomerCheckboxes = grid.getContainer().find('.js-bulk-action-checkbox:checked');

          $selectedCustomerCheckboxes.each(function (i, checkbox) {
            var $input = $(checkbox);

            _this2._addCustomerToDeleteCollectionInput($input.val());
          });

          var $form = $modal.find('form');

          $form.attr('action', submitUrl);
          $form.submit();
        });
      });
    }

    /**
     * Create input with customer id and add it to delete collection input
     *
     * @private
     */

  }, {
    key: '_addCustomerToDeleteCollectionInput',
    value: function _addCustomerToDeleteCollectionInput(customerId) {
      var $customersInput = $('#delete_customers_customers_to_delete');

      var customerInput = $customersInput.data('prototype').replace(/__name__/g, customerId);

      var $item = $($.parseHTML(customerInput)[0]);
      $item.val(customerId);

      $customersInput.append($item);
    }
  }]);
  return DeleteCustomersBulkActionExtension;
}();

exports.default = DeleteCustomersBulkActionExtension;

/***/ }),

/***/ 388:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

var $ = window.$;

/**
 * Class DeleteCustomerRowActionExtension handles submitting of row action
 */

var DeleteCustomerRowActionExtension = function () {
  function DeleteCustomerRowActionExtension() {
    var _this = this;

    (0, _classCallCheck3.default)(this, DeleteCustomerRowActionExtension);

    return {
      extend: function extend(grid) {
        return _this.extend(grid);
      }
    };
  }

  /**
   * Extend grid
   *
   * @param {Grid} grid
   */


  (0, _createClass3.default)(DeleteCustomerRowActionExtension, [{
    key: 'extend',
    value: function extend(grid) {
      var _this2 = this;

      grid.getContainer().on('click', '.js-delete-customer-row-action', function (event) {
        event.preventDefault();

        var $deleteCustomersModal = $('#' + grid.getId() + '_grid_delete_customers_modal');
        $deleteCustomersModal.modal('show');

        $deleteCustomersModal.on('click', '.js-submit-delete-customers', function () {
          var $button = $(event.currentTarget);
          var customerId = $button.data('customer-id');

          _this2._addCustomerInput(customerId);

          var $form = $deleteCustomersModal.find('form');

          $form.attr('action', $button.data('customer-delete-url'));
          $form.submit();
        });
      });
    }

    /**
     * Adds input for selected customer to delete form
     *
     * @param {integer} customerId
     *
     * @private
     */

  }, {
    key: '_addCustomerInput',
    value: function _addCustomerInput(customerId) {
      var $customersToDeleteInputBlock = $('#delete_customers_customers_to_delete');

      var customerInput = $customersToDeleteInputBlock.data('prototype').replace(/__name__/g, $customersToDeleteInputBlock.children().length);

      var $item = $($.parseHTML(customerInput)[0]);
      $item.val(customerId);

      $customersToDeleteInputBlock.append($item);
    }
  }]);
  return DeleteCustomerRowActionExtension;
}();

exports.default = DeleteCustomerRowActionExtension;

/***/ }),

/***/ 394:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

var $ = window.$;

/**
 * Takes link from clicked item and redirects to it.
 */

var LinkableItem = function LinkableItem() {
  (0, _classCallCheck3.default)(this, LinkableItem);

  $(document).on('click', '.js-linkable-item', function (event) {
    window.location = $(event.currentTarget).data('linkable-href');
  });
};

exports.default = LinkableItem;

/***/ }),

/***/ 4:
/***/ (function(module, exports) {

module.exports = function(it){
  return typeof it === 'object' ? it !== null : typeof it === 'function';
};

/***/ }),

/***/ 41:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

var $ = global.$;

/**
 * Makes a table sortable by columns.
 * This forces a page reload with more query parameters.
 */

var TableSorting = function () {

  /**
   * @param {jQuery} table
   */
  function TableSorting(table) {
    (0, _classCallCheck3.default)(this, TableSorting);

    this.selector = '.ps-sortable-column';
    this.columns = $(table).find(this.selector);
  }

  /**
   * Attaches the listeners
   */


  (0, _createClass3.default)(TableSorting, [{
    key: 'attach',
    value: function attach() {
      var _this = this;

      this.columns.on('click', function (e) {
        var $column = $(e.delegateTarget);
        _this._sortByColumn($column, _this._getToggledSortDirection($column));
      });
    }

    /**
     * Sort using a column name
     * @param {string} columnName
     * @param {string} direction "asc" or "desc"
     */

  }, {
    key: 'sortBy',
    value: function sortBy(columnName, direction) {
      var $column = this.columns.is('[data-sort-col-name="' + columnName + '"]');
      if (!$column) {
        throw new Error('Cannot sort by "' + columnName + '": invalid column');
      }

      this._sortByColumn($column, direction);
    }

    /**
     * Sort using a column element
     * @param {jQuery} column
     * @param {string} direction "asc" or "desc"
     * @private
     */

  }, {
    key: '_sortByColumn',
    value: function _sortByColumn(column, direction) {
      window.location = this._getUrl(column.data('sortColName'), direction === 'desc' ? 'desc' : 'asc', column.data('sortPrefix'));
    }

    /**
     * Returns the inverted direction to sort according to the column's current one
     * @param {jQuery} column
     * @return {string}
     * @private
     */

  }, {
    key: '_getToggledSortDirection',
    value: function _getToggledSortDirection(column) {
      return column.data('sortDirection') === 'asc' ? 'desc' : 'asc';
    }

    /**
     * Returns the url for the sorted table
     * @param {string} colName
     * @param {string} direction
     * @param {string} prefix
     * @return {string}
     * @private
     */

  }, {
    key: '_getUrl',
    value: function _getUrl(colName, direction, prefix) {
      var url = new URL(window.location.href);
      var params = url.searchParams;

      if (prefix) {
        params.set(prefix + '[orderBy]', colName);
        params.set(prefix + '[sortOrder]', direction);
      } else {
        params.set('orderBy', colName);
        params.set('sortOrder', direction);
      }

      return url.toString();
    }
  }]);
  return TableSorting;
}();

exports.default = TableSorting;
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(9)))

/***/ }),

/***/ 43:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {

Object.defineProperty(exports, "__esModule", {
  value: true
});
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * Send a Post Request to reset search Action.
 */

var $ = global.$;

var init = function resetSearch(url, redirectUrl) {
  $.post(url).then(function () {
    return window.location.assign(redirectUrl);
  });
};

exports.default = init;
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(9)))

/***/ }),

/***/ 47:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = ConfirmModal;
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

var $ = window.$;

/**
 * ConfirmModal component
 *
 * @param {String} id
 * @param {String} confirmTitle
 * @param {String} confirmMessage
 * @param {String} closeButtonLabel
 * @param {String} confirmButtonLabel
 * @param {String} confirmButtonClass
 * @param {Boolean} closable
 * @param {Function} confirmCallback
 *
 */
function ConfirmModal(params, confirmCallback) {
  var _this = this;

  // Construct the modal
  var id = params.id,
      closable = params.closable;

  this.modal = Modal(params);

  // jQuery modal object
  this.$modal = $(this.modal.container);

  this.show = function () {
    _this.$modal.modal();
  };

  this.modal.confirmButton.addEventListener('click', confirmCallback);

  this.$modal.modal({
    backdrop: closable ? true : 'static',
    keyboard: closable !== undefined ? closable : true,
    closable: closable !== undefined ? closable : true,
    show: false
  });

  this.$modal.on('hidden.bs.modal', function () {
    document.querySelector('#' + id).remove();
  });

  document.body.appendChild(this.modal.container);
}

/**
 * Modal component to improve lisibility by constructing the modal outside the main function
 *
 * @param {Object} params
 *
 */
function Modal(_ref) {
  var _ref$id = _ref.id,
      id = _ref$id === undefined ? 'confirm_modal' : _ref$id,
      confirmTitle = _ref.confirmTitle,
      _ref$confirmMessage = _ref.confirmMessage,
      confirmMessage = _ref$confirmMessage === undefined ? '' : _ref$confirmMessage,
      _ref$closeButtonLabel = _ref.closeButtonLabel,
      closeButtonLabel = _ref$closeButtonLabel === undefined ? 'Close' : _ref$closeButtonLabel,
      _ref$confirmButtonLab = _ref.confirmButtonLabel,
      confirmButtonLabel = _ref$confirmButtonLab === undefined ? 'Accept' : _ref$confirmButtonLab,
      _ref$confirmButtonCla = _ref.confirmButtonClass,
      confirmButtonClass = _ref$confirmButtonCla === undefined ? 'btn-primary' : _ref$confirmButtonCla;

  var modal = {};

  // Main modal element
  modal.container = document.createElement('div');
  modal.container.classList.add('modal', 'fade');
  modal.container.id = id;

  // Modal dialog element
  modal.dialog = document.createElement('div');
  modal.dialog.classList.add('modal-dialog');

  // Modal content element
  modal.content = document.createElement('div');
  modal.content.classList.add('modal-content');

  // Modal header element
  modal.header = document.createElement('div');
  modal.header.classList.add('modal-header');

  // Modal title element
  if (confirmTitle) {
    modal.title = document.createElement('h4');
    modal.title.classList.add('modal-title');
    modal.title.innerHTML = confirmTitle;
  }

  // Modal close button icon
  modal.closeIcon = document.createElement('button');
  modal.closeIcon.classList.add('close');
  modal.closeIcon.setAttribute('type', 'button');
  modal.closeIcon.dataset.dismiss = 'modal';
  modal.closeIcon.innerHTML = '×';

  // Modal body element
  modal.body = document.createElement('div');
  modal.body.classList.add('modal-body', 'text-left', 'font-weight-normal');

  // Modal message element
  modal.message = document.createElement('p');
  modal.message.classList.add('confirm-message');
  modal.message.innerHTML = confirmMessage;

  // Modal footer element
  modal.footer = document.createElement('div');
  modal.footer.classList.add('modal-footer');

  // Modal close button element
  modal.closeButton = document.createElement('button');
  modal.closeButton.setAttribute('type', 'button');
  modal.closeButton.classList.add('btn', 'btn-outline-secondary', 'btn-lg');
  modal.closeButton.dataset.dismiss = 'modal';
  modal.closeButton.innerHTML = closeButtonLabel;

  // Modal close button element
  modal.confirmButton = document.createElement('button');
  modal.confirmButton.setAttribute('type', 'button');
  modal.confirmButton.classList.add('btn', confirmButtonClass, 'btn-lg', 'btn-confirm-submit');
  modal.confirmButton.dataset.dismiss = 'modal';
  modal.confirmButton.innerHTML = confirmButtonLabel;

  // Constructing the modal
  if (confirmTitle) {
    modal.header.append(modal.title, modal.closeIcon);
  } else {
    modal.header.appendChild(modal.closeIcon);
  }

  modal.body.appendChild(modal.message);
  modal.footer.append(modal.closeButton, modal.confirmButton);
  modal.content.append(modal.header, modal.body, modal.footer);
  modal.dialog.appendChild(modal.content);
  modal.container.appendChild(modal.dialog);

  return modal;
}

/***/ }),

/***/ 491:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _grid = __webpack_require__(24);

var _grid2 = _interopRequireDefault(_grid);

var _reloadListExtension = __webpack_require__(28);

var _reloadListExtension2 = _interopRequireDefault(_reloadListExtension);

var _exportToSqlManagerExtension = __webpack_require__(29);

var _exportToSqlManagerExtension2 = _interopRequireDefault(_exportToSqlManagerExtension);

var _filtersResetExtension = __webpack_require__(26);

var _filtersResetExtension2 = _interopRequireDefault(_filtersResetExtension);

var _formSubmitButton = __webpack_require__(134);

var _formSubmitButton2 = _interopRequireDefault(_formSubmitButton);

var _sortingExtension = __webpack_require__(27);

var _sortingExtension2 = _interopRequireDefault(_sortingExtension);

var _bulkActionCheckboxExtension = __webpack_require__(31);

var _bulkActionCheckboxExtension2 = _interopRequireDefault(_bulkActionCheckboxExtension);

var _submitBulkActionExtension = __webpack_require__(32);

var _submitBulkActionExtension2 = _interopRequireDefault(_submitBulkActionExtension);

var _submitGridActionExtension = __webpack_require__(69);

var _submitGridActionExtension2 = _interopRequireDefault(_submitGridActionExtension);

var _linkRowActionExtension = __webpack_require__(34);

var _linkRowActionExtension2 = _interopRequireDefault(_linkRowActionExtension);

var _linkableItem = __webpack_require__(394);

var _linkableItem2 = _interopRequireDefault(_linkableItem);

var _choiceTable = __webpack_require__(133);

var _choiceTable2 = _interopRequireDefault(_choiceTable);

var _columnTogglingExtension = __webpack_require__(62);

var _columnTogglingExtension2 = _interopRequireDefault(_columnTogglingExtension);

var _deleteCustomersBulkActionExtension = __webpack_require__(387);

var _deleteCustomersBulkActionExtension2 = _interopRequireDefault(_deleteCustomersBulkActionExtension);

var _deleteCustomerRowActionExtension = __webpack_require__(388);

var _deleteCustomerRowActionExtension2 = _interopRequireDefault(_deleteCustomerRowActionExtension);

var _filtersSubmitButtonEnablerExtension = __webpack_require__(30);

var _filtersSubmitButtonEnablerExtension2 = _interopRequireDefault(_filtersSubmitButtonEnablerExtension);

var _showcaseCard = __webpack_require__(81);

var _showcaseCard2 = _interopRequireDefault(_showcaseCard);

var _showcaseCardCloseExtension = __webpack_require__(80);

var _showcaseCardCloseExtension2 = _interopRequireDefault(_showcaseCardCloseExtension);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

var $ = window.$;

$(function () {
  var customerGrid = new _grid2.default('customer');

  customerGrid.addExtension(new _reloadListExtension2.default());
  customerGrid.addExtension(new _exportToSqlManagerExtension2.default());
  customerGrid.addExtension(new _filtersResetExtension2.default());
  customerGrid.addExtension(new _sortingExtension2.default());
  customerGrid.addExtension(new _bulkActionCheckboxExtension2.default());
  customerGrid.addExtension(new _submitBulkActionExtension2.default());
  customerGrid.addExtension(new _submitGridActionExtension2.default());
  customerGrid.addExtension(new _linkRowActionExtension2.default());
  customerGrid.addExtension(new _columnTogglingExtension2.default());
  customerGrid.addExtension(new _deleteCustomersBulkActionExtension2.default());
  customerGrid.addExtension(new _deleteCustomerRowActionExtension2.default());
  customerGrid.addExtension(new _filtersSubmitButtonEnablerExtension2.default());

  var showcaseCard = new _showcaseCard2.default('customersShowcaseCard');
  showcaseCard.addExtension(new _showcaseCardCloseExtension2.default());

  // needed for "Group access" input in Add/Edit customer forms
  new _choiceTable2.default();

  // in customer view page
  // there are a lot of tables
  // where you click any row
  // and it redirects user to related page
  new _linkableItem2.default();

  new _formSubmitButton2.default();
});

/***/ }),

/***/ 5:
/***/ (function(module, exports) {

// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
var global = module.exports = typeof window != 'undefined' && window.Math == Math
  ? window : typeof self != 'undefined' && self.Math == Math ? self : Function('return this')();
if(typeof __g == 'number')__g = global; // eslint-disable-line no-undef

/***/ }),

/***/ 6:
/***/ (function(module, exports, __webpack_require__) {

var anObject       = __webpack_require__(11)
  , IE8_DOM_DEFINE = __webpack_require__(17)
  , toPrimitive    = __webpack_require__(14)
  , dP             = Object.defineProperty;

exports.f = __webpack_require__(2) ? Object.defineProperty : function defineProperty(O, P, Attributes){
  anObject(O);
  P = toPrimitive(P, true);
  anObject(Attributes);
  if(IE8_DOM_DEFINE)try {
    return dP(O, P, Attributes);
  } catch(e){ /* empty */ }
  if('get' in Attributes || 'set' in Attributes)throw TypeError('Accessors not supported!');
  if('value' in Attributes)O[P] = Attributes.value;
  return O;
};

/***/ }),

/***/ 62:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

var $ = global.$;

/**
 * Class ReloadListExtension extends grid with "Column toggling" feature
 */

var ColumnTogglingExtension = function () {
  function ColumnTogglingExtension() {
    (0, _classCallCheck3.default)(this, ColumnTogglingExtension);
  }

  (0, _createClass3.default)(ColumnTogglingExtension, [{
    key: 'extend',


    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      var _this = this;

      var $table = grid.getContainer().find('table.table');
      $table.find('.ps-togglable-row').on('click', function (e) {
        e.preventDefault();
        _this._toggleValue($(e.delegateTarget));
      });
    }

    /**
     * @param {jQuery} row
     * @private
     */

  }, {
    key: '_toggleValue',
    value: function _toggleValue(row) {
      var toggleUrl = row.data('toggleUrl');

      this._submitAsForm(toggleUrl);
    }

    /**
     * Submits request url as form
     *
     * @param {string} toggleUrl
     * @private
     */

  }, {
    key: '_submitAsForm',
    value: function _submitAsForm(toggleUrl) {
      var $form = $('<form>', {
        action: toggleUrl,
        method: 'POST'
      }).appendTo('body');

      $form.submit();
    }
  }]);
  return ColumnTogglingExtension;
}();

exports.default = ColumnTogglingExtension;
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(9)))

/***/ }),

/***/ 69:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

var $ = window.$;

/**
 * Class SubmitGridActionExtension handles grid action submits
 */

var SubmitGridActionExtension = function () {
  function SubmitGridActionExtension() {
    var _this = this;

    (0, _classCallCheck3.default)(this, SubmitGridActionExtension);

    return {
      extend: function extend(grid) {
        return _this.extend(grid);
      }
    };
  }

  (0, _createClass3.default)(SubmitGridActionExtension, [{
    key: 'extend',
    value: function extend(grid) {
      var _this2 = this;

      grid.getHeaderContainer().on('click', '.js-grid-action-submit-btn', function (event) {
        _this2.handleSubmit(event, grid);
      });
    }

    /**
     * Handle grid action submit.
     * It uses grid form to submit actions.
     *
     * @param {Event} event
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: 'handleSubmit',
    value: function handleSubmit(event, grid) {
      var $submitBtn = $(event.currentTarget);
      var confirmMessage = $submitBtn.data('confirm-message');

      if (typeof confirmMessage !== "undefined" && 0 < confirmMessage.length && !confirm(confirmMessage)) {
        return;
      }

      var $form = $('#' + grid.getId() + '_filter_form');

      $form.attr('action', $submitBtn.data('url'));
      $form.attr('method', $submitBtn.data('method'));
      $form.find('input[name="' + grid.getId() + '[_token]"]').val($submitBtn.data('csrf'));
      $form.submit();
    }
  }]);
  return SubmitGridActionExtension;
}();

exports.default = SubmitGridActionExtension;

/***/ }),

/***/ 7:
/***/ (function(module, exports) {

module.exports = function(exec){
  try {
    return !!exec();
  } catch(e){
    return true;
  }
};

/***/ }),

/***/ 8:
/***/ (function(module, exports, __webpack_require__) {

var global    = __webpack_require__(5)
  , core      = __webpack_require__(3)
  , ctx       = __webpack_require__(13)
  , hide      = __webpack_require__(10)
  , PROTOTYPE = 'prototype';

var $export = function(type, name, source){
  var IS_FORCED = type & $export.F
    , IS_GLOBAL = type & $export.G
    , IS_STATIC = type & $export.S
    , IS_PROTO  = type & $export.P
    , IS_BIND   = type & $export.B
    , IS_WRAP   = type & $export.W
    , exports   = IS_GLOBAL ? core : core[name] || (core[name] = {})
    , expProto  = exports[PROTOTYPE]
    , target    = IS_GLOBAL ? global : IS_STATIC ? global[name] : (global[name] || {})[PROTOTYPE]
    , key, own, out;
  if(IS_GLOBAL)source = name;
  for(key in source){
    // contains in native
    own = !IS_FORCED && target && target[key] !== undefined;
    if(own && key in exports)continue;
    // export native or passed
    out = own ? target[key] : source[key];
    // prevent global pollution for namespaces
    exports[key] = IS_GLOBAL && typeof target[key] != 'function' ? source[key]
    // bind timers to global for call from export context
    : IS_BIND && own ? ctx(out, global)
    // wrap global constructors for prevent change them in library
    : IS_WRAP && target[key] == out ? (function(C){
      var F = function(a, b, c){
        if(this instanceof C){
          switch(arguments.length){
            case 0: return new C;
            case 1: return new C(a);
            case 2: return new C(a, b);
          } return new C(a, b, c);
        } return C.apply(this, arguments);
      };
      F[PROTOTYPE] = C[PROTOTYPE];
      return F;
    // make static versions for prototype methods
    })(out) : IS_PROTO && typeof out == 'function' ? ctx(Function.call, out) : out;
    // export proto methods to core.%CONSTRUCTOR%.methods.%NAME%
    if(IS_PROTO){
      (exports.virtual || (exports.virtual = {}))[key] = out;
      // export proto methods to core.%CONSTRUCTOR%.prototype.%NAME%
      if(type & $export.R && expProto && !expProto[key])hide(expProto, key, out);
    }
  }
};
// type bitmap
$export.F = 1;   // forced
$export.G = 2;   // global
$export.S = 4;   // static
$export.P = 8;   // proto
$export.B = 16;  // bind
$export.W = 32;  // wrap
$export.U = 64;  // safe
$export.R = 128; // real proto method for `library` 
module.exports = $export;

/***/ }),

/***/ 80:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

var $ = window.$;

/**
 * Class ShowcaseCardCloseExtension is responsible for providing helper block closing behavior
 */

var ShowcaseCardCloseExtension = function () {
  function ShowcaseCardCloseExtension() {
    (0, _classCallCheck3.default)(this, ShowcaseCardCloseExtension);
  }

  (0, _createClass3.default)(ShowcaseCardCloseExtension, [{
    key: 'extend',


    /**
     * Extend helper block.
     *
     * @param {ShowcaseCard} helperBlock
     */
    value: function extend(helperBlock) {
      var container = helperBlock.getContainer();
      container.on('click', '.js-remove-helper-block', function (evt) {
        container.remove();

        var $btn = $(evt.target);
        var url = $btn.data('closeUrl');
        var cardName = $btn.data('cardName');

        if (url) {
          // notify the card was closed
          $.post(url, {
            close: 1,
            name: cardName
          });
        }
      });
    }
  }]);
  return ShowcaseCardCloseExtension;
}();

exports.default = ShowcaseCardCloseExtension;

/***/ }),

/***/ 81:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

var $ = window.$;

/**
 * Class ShowcaseCard is responsible for handling events related with showcase card.
 */

var ShowcaseCard = function () {

  /**
   * Showcase card id.
   *
   * @param {string} id
   */
  function ShowcaseCard(id) {
    (0, _classCallCheck3.default)(this, ShowcaseCard);

    this.id = id;
    this.$container = $('#' + this.id);
  }

  /**
   * Get showcase card container.
   *
   * @returns {jQuery}
   */


  (0, _createClass3.default)(ShowcaseCard, [{
    key: 'getContainer',
    value: function getContainer() {
      return this.$container;
    }

    /**
     * Extend showcase card with external extensions.
     *
     * @param {object} extension
     */

  }, {
    key: 'addExtension',
    value: function addExtension(extension) {
      extension.extend(this);
    }
  }]);
  return ShowcaseCard;
}();

exports.default = ShowcaseCard;

/***/ }),

/***/ 9:
/***/ (function(module, exports) {

var g;

// This works in non-strict mode
g = (function() {
	return this;
})();

try {
	// This works if eval is allowed (see CSP)
	g = g || Function("return this")() || (1,eval)("this");
} catch(e) {
	// This works if the window reference is available
	if(typeof window === "object")
		g = window;
}

// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}

module.exports = g;


/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgM2E2MTdjZWQyOWViY2NiNmExZDA/MTI1MCoqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2JhYmVsLXJ1bnRpbWUvaGVscGVycy9jbGFzc0NhbGxDaGVjay5qcz8yMWFmKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2JhYmVsLXJ1bnRpbWUvaGVscGVycy9jcmVhdGVDbGFzcy5qcz8xZGZlKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19oaWRlLmpzP2E2ZGEqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fYW4tb2JqZWN0LmpzPzBkYTMqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fcHJvcGVydHktZGVzYy5qcz8xZTg2KioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2N0eC5qcz9jZTAwKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvY2hvaWNlLXRhYmxlLmpzPzc1NmIiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9mb3JtLXN1Ym1pdC1idXR0b24uanM/MzU2MioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fdG8tcHJpbWl0aXZlLmpzPzQ5YTQqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZG9tLWNyZWF0ZS5qcz9hYjQ0KioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2llOC1kb20tZGVmaW5lLmpzP2JkMWYqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fYS1mdW5jdGlvbi5qcz9kNTNlKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vYmFiZWwtcnVudGltZS9jb3JlLWpzL29iamVjdC9kZWZpbmUtcHJvcGVydHkuanM/NWY3MCoqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19kZXNjcmlwdG9ycy5qcz83MDUxKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC9kZWZpbmUtcHJvcGVydHkuanM/YjdkOCoqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5vYmplY3QuZGVmaW5lLXByb3BlcnR5LmpzP2M4MmMqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2dyaWQuanM/ODEzYSoqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXJlc2V0LWV4dGVuc2lvbi5qcz8xNmYxKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3NvcnRpbmctZXh0ZW5zaW9uLmpzPzExM2UqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vcmVsb2FkLWxpc3QtZXh0ZW5zaW9uLmpzP2QzZTAqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZXhwb3J0LXRvLXNxbC1tYW5hZ2VyLWV4dGVuc2lvbi5qcz9lZDJhKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19jb3JlLmpzPzFiNjIqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXN1Ym1pdC1idXR0b24tZW5hYmxlci1leHRlbnNpb24uanM/MDM3OSoqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9idWxrLWFjdGlvbi1jaGVja2JveC1leHRlbnNpb24uanM/YjA5NyoqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9zdWJtaXQtYnVsay1hY3Rpb24tZXh0ZW5zaW9uLmpzPzFiMWYqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vbGluay1yb3ctYWN0aW9uLWV4dGVuc2lvbi5qcz8zOWRjKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2FjdGlvbi9idWxrL2N1c3RvbWVyL2RlbGV0ZS1jdXN0b21lcnMtYnVsay1hY3Rpb24tZXh0ZW5zaW9uLmpzIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYWN0aW9uL3Jvdy9jdXN0b21lci9kZWxldGUtY3VzdG9tZXItcm93LWFjdGlvbi1leHRlbnNpb24uanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9saW5rYWJsZS1pdGVtLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2lzLW9iamVjdC5qcz8yNGM4KioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL2FwcC91dGlscy90YWJsZS1zb3J0aW5nLmpzPzE1ZDQqKioqIiwid2VicGFjazovLy8uL2pzL2FwcC91dGlscy9yZXNldF9zZWFyY2guanM/MWE3ZioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9tb2RhbC5qcz83NTU4KioqKioqIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL2N1c3RvbWVyL2luZGV4LmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2dsb2JhbC5qcz83N2FhKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1kcC5qcz80MTE2KioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vY29sdW1uLXRvZ2dsaW5nLWV4dGVuc2lvbi5qcz82OTQzKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3N1Ym1pdC1ncmlkLWFjdGlvbi1leHRlbnNpb24uanM/NzVjMyoiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZmFpbHMuanM/OTM1ZCoqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19leHBvcnQuanM/ZWNlMioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL3Nob3djYXNlLWNhcmQvZXh0ZW5zaW9uL3Nob3djYXNlLWNhcmQtY2xvc2UtZXh0ZW5zaW9uLmpzP2QxNDAqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL3Nob3djYXNlLWNhcmQvc2hvd2Nhc2UtY2FyZC5qcz83NjM0KioiLCJ3ZWJwYWNrOi8vLyh3ZWJwYWNrKS9idWlsZGluL2dsb2JhbC5qcz8zNjk4KioqKioqKioqKioqKioiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsIkNob2ljZVRhYmxlIiwiZG9jdW1lbnQiLCJvbiIsImUiLCJoYW5kbGVTZWxlY3RBbGwiLCJldmVudCIsIiRzZWxlY3RBbGxDaGVja2JveGVzIiwidGFyZ2V0IiwiaXNTZWxlY3RBbGxDaGVja2VkIiwiaXMiLCJjbG9zZXN0IiwiZmluZCIsInByb3AiLCJGb3JtU3VibWl0QnV0dG9uIiwicHJldmVudERlZmF1bHQiLCIkYnRuIiwiZGF0YSIsImNvbmZpcm0iLCJtZXRob2QiLCJhZGRJbnB1dCIsImJ0bk1ldGhvZCIsImlzR2V0T3JQb3N0TWV0aG9kIiwiaW5jbHVkZXMiLCJ0eXBlIiwibmFtZSIsInZhbHVlIiwiJGZvcm0iLCJhcHBlbmQiLCJhcHBlbmRUbyIsInN1Ym1pdCIsIkdyaWQiLCJpZCIsIiRjb250YWluZXIiLCJleHRlbnNpb24iLCJleHRlbmQiLCJGaWx0ZXJzUmVzZXRFeHRlbnNpb24iLCJncmlkIiwiZ2V0Q29udGFpbmVyIiwiY3VycmVudFRhcmdldCIsIlNvcnRpbmdFeHRlbnNpb24iLCIkc29ydGFibGVUYWJsZSIsIlRhYmxlU29ydGluZyIsImF0dGFjaCIsIlJlbG9hZExpc3RFeHRlbnNpb24iLCJnZXRIZWFkZXJDb250YWluZXIiLCJsb2NhdGlvbiIsInJlbG9hZCIsIkV4cG9ydFRvU3FsTWFuYWdlckV4dGVuc2lvbiIsIl9vblNob3dTcWxRdWVyeUNsaWNrIiwiX29uRXhwb3J0U3FsTWFuYWdlckNsaWNrIiwiJHNxbE1hbmFnZXJGb3JtIiwiZ2V0SWQiLCJfZmlsbEV4cG9ydEZvcm0iLCIkbW9kYWwiLCJtb2RhbCIsInF1ZXJ5IiwidmFsIiwiX2dldE5hbWVGcm9tQnJlYWRjcnVtYiIsIiRicmVhZGNydW1icyIsImVhY2giLCJpIiwiaXRlbSIsIiRicmVhZGNydW1iIiwiYnJlYWRjcnVtYlRpdGxlIiwibGVuZ3RoIiwidGV4dCIsImNvbmNhdCIsIkZpbHRlcnNTdWJtaXRCdXR0b25FbmFibGVyRXh0ZW5zaW9uIiwiJGZpbHRlcnNSb3ciLCJCdWxrQWN0aW9uQ2hlY2tib3hFeHRlbnNpb24iLCJfaGFuZGxlQnVsa0FjdGlvbkNoZWNrYm94U2VsZWN0IiwiX2hhbmRsZUJ1bGtBY3Rpb25TZWxlY3RBbGxDaGVja2JveCIsIiRjaGVja2JveCIsImlzQ2hlY2tlZCIsIl9lbmFibGVCdWxrQWN0aW9uc0J0biIsIl9kaXNhYmxlQnVsa0FjdGlvbnNCdG4iLCJjaGVja2VkUm93c0NvdW50IiwiU3VibWl0QnVsa0FjdGlvbkV4dGVuc2lvbiIsIiRzdWJtaXRCdG4iLCJjb25maXJtTWVzc2FnZSIsImNvbmZpcm1UaXRsZSIsInVuZGVmaW5lZCIsInNob3dDb25maXJtTW9kYWwiLCJwb3N0Rm9ybSIsImNvbmZpcm1CdXR0b25MYWJlbCIsImNsb3NlQnV0dG9uTGFiZWwiLCJjb25maXJtQnV0dG9uQ2xhc3MiLCJDb25maXJtTW9kYWwiLCJzaG93IiwiYXR0ciIsIkxpbmtSb3dBY3Rpb25FeHRlbnNpb24iLCJpbml0Um93TGlua3MiLCJpbml0Q29uZmlybWFibGVBY3Rpb25zIiwiaW5pdEVhY2hSb3ciLCIkcGFyZW50Um93IiwicHJvcGFnYXRlRmlyc3RMaW5rQWN0aW9uIiwiJHJvd0FjdGlvbiIsIiRwYXJlbnRDZWxsIiwiY2xpY2thYmxlQ2VsbHMiLCJub3QiLCJhZGRDbGFzcyIsImNsaWNrIiwiRGVsZXRlQ3VzdG9tZXJzQnVsa0FjdGlvbkV4dGVuc2lvbiIsInN1Ym1pdFVybCIsIiRzZWxlY3RlZEN1c3RvbWVyQ2hlY2tib3hlcyIsImNoZWNrYm94IiwiJGlucHV0IiwiX2FkZEN1c3RvbWVyVG9EZWxldGVDb2xsZWN0aW9uSW5wdXQiLCJjdXN0b21lcklkIiwiJGN1c3RvbWVyc0lucHV0IiwiY3VzdG9tZXJJbnB1dCIsInJlcGxhY2UiLCIkaXRlbSIsInBhcnNlSFRNTCIsIkRlbGV0ZUN1c3RvbWVyUm93QWN0aW9uRXh0ZW5zaW9uIiwiJGRlbGV0ZUN1c3RvbWVyc01vZGFsIiwiJGJ1dHRvbiIsIl9hZGRDdXN0b21lcklucHV0IiwiJGN1c3RvbWVyc1RvRGVsZXRlSW5wdXRCbG9jayIsImNoaWxkcmVuIiwiTGlua2FibGVJdGVtIiwiZ2xvYmFsIiwidGFibGUiLCJzZWxlY3RvciIsImNvbHVtbnMiLCIkY29sdW1uIiwiZGVsZWdhdGVUYXJnZXQiLCJfc29ydEJ5Q29sdW1uIiwiX2dldFRvZ2dsZWRTb3J0RGlyZWN0aW9uIiwiY29sdW1uTmFtZSIsImRpcmVjdGlvbiIsIkVycm9yIiwiY29sdW1uIiwiX2dldFVybCIsImNvbE5hbWUiLCJwcmVmaXgiLCJ1cmwiLCJVUkwiLCJocmVmIiwicGFyYW1zIiwic2VhcmNoUGFyYW1zIiwic2V0IiwidG9TdHJpbmciLCJpbml0IiwicmVzZXRTZWFyY2giLCJyZWRpcmVjdFVybCIsInBvc3QiLCJ0aGVuIiwiYXNzaWduIiwiY29uZmlybUNhbGxiYWNrIiwiY2xvc2FibGUiLCJNb2RhbCIsImNvbnRhaW5lciIsImNvbmZpcm1CdXR0b24iLCJhZGRFdmVudExpc3RlbmVyIiwiYmFja2Ryb3AiLCJrZXlib2FyZCIsInF1ZXJ5U2VsZWN0b3IiLCJyZW1vdmUiLCJib2R5IiwiYXBwZW5kQ2hpbGQiLCJjcmVhdGVFbGVtZW50IiwiY2xhc3NMaXN0IiwiYWRkIiwiZGlhbG9nIiwiY29udGVudCIsImhlYWRlciIsInRpdGxlIiwiaW5uZXJIVE1MIiwiY2xvc2VJY29uIiwic2V0QXR0cmlidXRlIiwiZGF0YXNldCIsImRpc21pc3MiLCJtZXNzYWdlIiwiZm9vdGVyIiwiY2xvc2VCdXR0b24iLCJjdXN0b21lckdyaWQiLCJhZGRFeHRlbnNpb24iLCJSZWxvYWRMaXN0QWN0aW9uRXh0ZW5zaW9uIiwiU3VibWl0QnVsa0V4dGVuc2lvbiIsIlN1Ym1pdEdyaWRFeHRlbnNpb24iLCJDb2x1bW5Ub2dnbGluZ0V4dGVuc2lvbiIsInNob3djYXNlQ2FyZCIsIlNob3djYXNlQ2FyZCIsIlNob3djYXNlQ2FyZENsb3NlRXh0ZW5zaW9uIiwiJHRhYmxlIiwiX3RvZ2dsZVZhbHVlIiwicm93IiwidG9nZ2xlVXJsIiwiX3N1Ym1pdEFzRm9ybSIsImFjdGlvbiIsIlN1Ym1pdEdyaWRBY3Rpb25FeHRlbnNpb24iLCJoYW5kbGVTdWJtaXQiLCJoZWxwZXJCbG9jayIsImV2dCIsImNhcmROYW1lIiwiY2xvc2UiXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7OztBQUdBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBLG1EQUEyQyxjQUFjOztBQUV6RDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGFBQUs7QUFDTDtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLG1DQUEyQiwwQkFBMEIsRUFBRTtBQUN2RCx5Q0FBaUMsZUFBZTtBQUNoRDtBQUNBO0FBQ0E7O0FBRUE7QUFDQSw4REFBc0QsK0RBQStEOztBQUVySDtBQUNBOztBQUVBO0FBQ0E7Ozs7Ozs7OztBQ2hFQTs7QUFFQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLEU7Ozs7Ozs7O0FDUkE7O0FBRUE7O0FBRUE7O0FBRUE7O0FBRUEsc0NBQXNDLHVDQUF1QyxnQkFBZ0I7O0FBRTdGO0FBQ0E7QUFDQSxtQkFBbUIsa0JBQWtCO0FBQ3JDO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxDQUFDLEc7Ozs7Ozs7QUMxQkQ7QUFDQTtBQUNBO0FBQ0E7QUFDQSxDQUFDO0FBQ0Q7QUFDQTtBQUNBLEU7Ozs7Ozs7QUNQQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLEU7Ozs7Ozs7QUNKQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLEU7Ozs7Ozs7QUNQQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLEU7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQ25CQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQXlCQSxJQUFNQSxJQUFJQyxPQUFPRCxDQUFqQjs7QUFFQTs7OztJQUdxQkUsVztBQUNuQjs7O0FBR0EseUJBQWM7QUFBQTs7QUFBQTs7QUFDWkYsTUFBRUcsUUFBRixFQUFZQyxFQUFaLENBQWUsUUFBZixFQUF5Qiw2QkFBekIsRUFBd0QsVUFBQ0MsQ0FBRCxFQUFPO0FBQzdELFlBQUtDLGVBQUwsQ0FBcUJELENBQXJCO0FBQ0QsS0FGRDtBQUdEOztBQUVEOzs7Ozs7Ozs7b0NBS2dCRSxLLEVBQU87QUFDckIsVUFBTUMsdUJBQXVCUixFQUFFTyxNQUFNRSxNQUFSLENBQTdCO0FBQ0EsVUFBTUMscUJBQXFCRixxQkFBcUJHLEVBQXJCLENBQXdCLFVBQXhCLENBQTNCOztBQUVBSCwyQkFBcUJJLE9BQXJCLENBQTZCLE9BQTdCLEVBQXNDQyxJQUF0QyxDQUEyQyxzQkFBM0MsRUFBbUVDLElBQW5FLENBQXdFLFNBQXhFLEVBQW1GSixrQkFBbkY7QUFDRDs7Ozs7a0JBcEJrQlIsVzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUM5QnJCOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBeUJBLElBQU1GLElBQUlDLE9BQU9ELENBQWpCOztBQUVBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7SUEwQnFCZSxnQixHQUNuQiw0QkFBYztBQUFBOztBQUNaZixJQUFFRyxRQUFGLEVBQVlDLEVBQVosQ0FBZSxPQUFmLEVBQXdCLHFCQUF4QixFQUErQyxVQUFVRyxLQUFWLEVBQWlCO0FBQzlEQSxVQUFNUyxjQUFOOztBQUVBLFFBQU1DLE9BQU9qQixFQUFFLElBQUYsQ0FBYjs7QUFFQSxRQUFJaUIsS0FBS0MsSUFBTCxDQUFVLHNCQUFWLEtBQXFDLFVBQVVDLFFBQVFGLEtBQUtDLElBQUwsQ0FBVSxzQkFBVixDQUFSLENBQW5ELEVBQStGO0FBQzdGO0FBQ0Q7O0FBRUQsUUFBSUUsU0FBUyxNQUFiO0FBQ0EsUUFBSUMsV0FBVyxJQUFmOztBQUVBLFFBQUlKLEtBQUtDLElBQUwsQ0FBVSxRQUFWLENBQUosRUFBeUI7QUFDdkIsVUFBTUksWUFBWUwsS0FBS0MsSUFBTCxDQUFVLFFBQVYsQ0FBbEI7QUFDQSxVQUFNSyxvQkFBb0IsQ0FBQyxLQUFELEVBQVEsTUFBUixFQUFnQkMsUUFBaEIsQ0FBeUJGLFNBQXpCLENBQTFCO0FBQ0FGLGVBQVNHLG9CQUFvQkQsU0FBcEIsR0FBZ0MsTUFBekM7O0FBRUEsVUFBSSxDQUFDQyxpQkFBTCxFQUF3QjtBQUN0QkYsbUJBQVdyQixFQUFFLFNBQUYsRUFBYTtBQUN0QnlCLGdCQUFNLFNBRGdCO0FBRXRCQyxnQkFBTSxTQUZnQjtBQUd0QkMsaUJBQU9QO0FBSGUsU0FBYixDQUFYO0FBS0Q7QUFDRjs7QUFFRCxRQUFNUSxRQUFRNUIsRUFBRSxRQUFGLEVBQVk7QUFDeEIsZ0JBQVVpQixLQUFLQyxJQUFMLENBQVUsaUJBQVYsQ0FEYztBQUV4QixnQkFBVUU7QUFGYyxLQUFaLENBQWQ7O0FBS0EsUUFBSUMsUUFBSixFQUFjO0FBQ1pPLFlBQU1DLE1BQU4sQ0FBYVIsUUFBYjtBQUNEOztBQUVELFFBQUlKLEtBQUtDLElBQUwsQ0FBVSxpQkFBVixDQUFKLEVBQWtDO0FBQ2hDVSxZQUFNQyxNQUFOLENBQWE3QixFQUFFLFNBQUYsRUFBYTtBQUN4QixnQkFBUSxTQURnQjtBQUV4QixnQkFBUSxhQUZnQjtBQUd4QixpQkFBU2lCLEtBQUtDLElBQUwsQ0FBVSxpQkFBVjtBQUhlLE9BQWIsQ0FBYjtBQUtEOztBQUVEVSxVQUFNRSxRQUFOLENBQWUsTUFBZixFQUF1QkMsTUFBdkI7QUFDRCxHQTVDRDtBQTZDRCxDOztrQkEvQ2tCaEIsZ0I7Ozs7Ozs7QUNyRHJCO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxFOzs7Ozs7O0FDWEE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsRTs7Ozs7OztBQ05BO0FBQ0EscUVBQXNFLGdCQUFnQixVQUFVLEdBQUc7QUFDbkcsQ0FBQyxFOzs7Ozs7O0FDRkQ7QUFDQTtBQUNBO0FBQ0EsRTs7Ozs7OztBQ0hBLGtCQUFrQix3RDs7Ozs7OztBQ0FsQjtBQUNBO0FBQ0EsaUNBQWlDLFFBQVEsZ0JBQWdCLFVBQVUsR0FBRztBQUN0RSxDQUFDLEU7Ozs7Ozs7QUNIRDtBQUNBO0FBQ0E7QUFDQTtBQUNBLEU7Ozs7Ozs7QUNKQTtBQUNBO0FBQ0Esb0VBQXVFLHlDQUEwQyxFOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUNGakg7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUF5QkEsSUFBTWYsSUFBSUMsT0FBT0QsQ0FBakI7O0FBRUE7Ozs7SUFHcUJnQyxJO0FBQ25COzs7OztBQUtBLGdCQUFZQyxFQUFaLEVBQWdCO0FBQUE7O0FBQ2QsU0FBS0EsRUFBTCxHQUFVQSxFQUFWO0FBQ0EsU0FBS0MsVUFBTCxHQUFrQmxDLEVBQUUsTUFBTSxLQUFLaUMsRUFBWCxHQUFnQixPQUFsQixDQUFsQjtBQUNEOztBQUVEOzs7Ozs7Ozs7NEJBS1E7QUFDTixhQUFPLEtBQUtBLEVBQVo7QUFDRDs7QUFFRDs7Ozs7Ozs7bUNBS2U7QUFDYixhQUFPLEtBQUtDLFVBQVo7QUFDRDs7QUFFRDs7Ozs7Ozs7eUNBS3FCO0FBQ25CLGFBQU8sS0FBS0EsVUFBTCxDQUFnQnRCLE9BQWhCLENBQXdCLGdCQUF4QixFQUEwQ0MsSUFBMUMsQ0FBK0MsaUJBQS9DLENBQVA7QUFDRDs7QUFFRDs7Ozs7Ozs7aUNBS2FzQixTLEVBQVc7QUFDdEJBLGdCQUFVQyxNQUFWLENBQWlCLElBQWpCO0FBQ0Q7Ozs7O2tCQTdDa0JKLEk7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUNMckI7Ozs7OztBQUVBLElBQU1oQyxJQUFJQyxPQUFPRCxDQUFqQjs7QUFFQTs7O0FBN0JBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0lBZ0NxQnFDLHFCOzs7Ozs7Ozs7QUFFbkI7Ozs7OzJCQUtPQyxJLEVBQU07QUFDWEEsV0FBS0MsWUFBTCxHQUFvQm5DLEVBQXBCLENBQXVCLE9BQXZCLEVBQWdDLGtCQUFoQyxFQUFvRCxVQUFDRyxLQUFELEVBQVc7QUFDN0Qsb0NBQVlQLEVBQUVPLE1BQU1pQyxhQUFSLEVBQXVCdEIsSUFBdkIsQ0FBNEIsS0FBNUIsQ0FBWixFQUFnRGxCLEVBQUVPLE1BQU1pQyxhQUFSLEVBQXVCdEIsSUFBdkIsQ0FBNEIsVUFBNUIsQ0FBaEQ7QUFDRCxPQUZEO0FBR0Q7Ozs7O2tCQVhrQm1CLHFCOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FDUHJCOzs7Ozs7QUFFQTs7O0lBR3FCSSxnQjs7Ozs7Ozs7QUFDbkI7Ozs7OzJCQUtPSCxJLEVBQU07QUFDWCxVQUFNSSxpQkFBaUJKLEtBQUtDLFlBQUwsR0FBb0IxQixJQUFwQixDQUF5QixhQUF6QixDQUF2Qjs7QUFFQSxVQUFJOEIsc0JBQUosQ0FBaUJELGNBQWpCLEVBQWlDRSxNQUFqQztBQUNEOzs7S0F4Q0g7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7a0JBOEJxQkgsZ0I7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQzlCckI7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUF5QkE7OztJQUdxQkksbUI7Ozs7Ozs7O0FBQ25COzs7OzsyQkFLT1AsSSxFQUFNO0FBQ1hBLFdBQUtRLGtCQUFMLEdBQTBCMUMsRUFBMUIsQ0FBNkIsT0FBN0IsRUFBc0MscUNBQXRDLEVBQTZFLFlBQU07QUFDakYyQyxpQkFBU0MsTUFBVDtBQUNELE9BRkQ7QUFHRDs7Ozs7a0JBVmtCSCxtQjs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FDNUJyQjs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQXlCQSxJQUFNN0MsSUFBSUMsT0FBT0QsQ0FBakI7O0FBRUE7Ozs7SUFHcUJpRCwyQjs7Ozs7Ozs7QUFDbkI7Ozs7OzJCQUtPWCxJLEVBQU07QUFBQTs7QUFDWEEsV0FBS1Esa0JBQUwsR0FBMEIxQyxFQUExQixDQUE2QixPQUE3QixFQUFzQyxtQ0FBdEMsRUFBMkU7QUFBQSxlQUFNLE1BQUs4QyxvQkFBTCxDQUEwQlosSUFBMUIsQ0FBTjtBQUFBLE9BQTNFO0FBQ0FBLFdBQUtRLGtCQUFMLEdBQTBCMUMsRUFBMUIsQ0FBNkIsT0FBN0IsRUFBc0MsMkNBQXRDLEVBQW1GO0FBQUEsZUFBTSxNQUFLK0Msd0JBQUwsQ0FBOEJiLElBQTlCLENBQU47QUFBQSxPQUFuRjtBQUNEOztBQUVEOzs7Ozs7Ozs7O3lDQU9xQkEsSSxFQUFNO0FBQ3pCLFVBQU1jLGtCQUFrQnBELEVBQUUsTUFBTXNDLEtBQUtlLEtBQUwsRUFBTixHQUFxQiwrQkFBdkIsQ0FBeEI7QUFDQSxXQUFLQyxlQUFMLENBQXFCRixlQUFyQixFQUFzQ2QsSUFBdEM7O0FBRUEsVUFBTWlCLFNBQVN2RCxFQUFFLE1BQU1zQyxLQUFLZSxLQUFMLEVBQU4sR0FBcUIsK0JBQXZCLENBQWY7QUFDQUUsYUFBT0MsS0FBUCxDQUFhLE1BQWI7O0FBRUFELGFBQU9uRCxFQUFQLENBQVUsT0FBVixFQUFtQixpQkFBbkIsRUFBc0M7QUFBQSxlQUFNZ0QsZ0JBQWdCckIsTUFBaEIsRUFBTjtBQUFBLE9BQXRDO0FBQ0Q7O0FBRUQ7Ozs7Ozs7Ozs7NkNBT3lCTyxJLEVBQU07QUFDN0IsVUFBTWMsa0JBQWtCcEQsRUFBRSxNQUFNc0MsS0FBS2UsS0FBTCxFQUFOLEdBQXFCLCtCQUF2QixDQUF4Qjs7QUFFQSxXQUFLQyxlQUFMLENBQXFCRixlQUFyQixFQUFzQ2QsSUFBdEM7O0FBRUFjLHNCQUFnQnJCLE1BQWhCO0FBQ0Q7O0FBRUQ7Ozs7Ozs7Ozs7O29DQVFnQnFCLGUsRUFBaUJkLEksRUFBTTtBQUNyQyxVQUFNbUIsUUFBUW5CLEtBQUtDLFlBQUwsR0FBb0IxQixJQUFwQixDQUF5QixnQkFBekIsRUFBMkNLLElBQTNDLENBQWdELE9BQWhELENBQWQ7O0FBRUFrQyxzQkFBZ0J2QyxJQUFoQixDQUFxQixzQkFBckIsRUFBNkM2QyxHQUE3QyxDQUFpREQsS0FBakQ7QUFDQUwsc0JBQWdCdkMsSUFBaEIsQ0FBcUIsb0JBQXJCLEVBQTJDNkMsR0FBM0MsQ0FBK0MsS0FBS0Msc0JBQUwsRUFBL0M7QUFDRDs7QUFFRDs7Ozs7Ozs7Ozs2Q0FPeUI7QUFDdkIsVUFBTUMsZUFBZTVELEVBQUUsaUJBQUYsRUFBcUJhLElBQXJCLENBQTBCLGtCQUExQixDQUFyQjtBQUNBLFVBQUlhLE9BQU8sRUFBWDs7QUFFQWtDLG1CQUFhQyxJQUFiLENBQWtCLFVBQUNDLENBQUQsRUFBSUMsSUFBSixFQUFhO0FBQzdCLFlBQU1DLGNBQWNoRSxFQUFFK0QsSUFBRixDQUFwQjs7QUFFQSxZQUFNRSxrQkFBa0IsSUFBSUQsWUFBWW5ELElBQVosQ0FBaUIsR0FBakIsRUFBc0JxRCxNQUExQixHQUN0QkYsWUFBWW5ELElBQVosQ0FBaUIsR0FBakIsRUFBc0JzRCxJQUF0QixFQURzQixHQUV0QkgsWUFBWUcsSUFBWixFQUZGOztBQUlBLFlBQUksSUFBSXpDLEtBQUt3QyxNQUFiLEVBQXFCO0FBQ25CeEMsaUJBQU9BLEtBQUswQyxNQUFMLENBQVksS0FBWixDQUFQO0FBQ0Q7O0FBRUQxQyxlQUFPQSxLQUFLMEMsTUFBTCxDQUFZSCxlQUFaLENBQVA7QUFDRCxPQVpEOztBQWNBLGFBQU92QyxJQUFQO0FBQ0Q7Ozs7O2tCQXBGa0J1QiwyQjs7Ozs7OztBQzlCckIsNkJBQTZCO0FBQzdCLHFDQUFxQyxnQzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FDRHJDOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBeUJBOzs7SUFHcUJvQixtQzs7Ozs7Ozs7O0FBRW5COzs7OzsyQkFLTy9CLEksRUFBTTtBQUNYLFVBQU1nQyxjQUFjaEMsS0FBS0MsWUFBTCxHQUFvQjFCLElBQXBCLENBQXlCLGlCQUF6QixDQUFwQjtBQUNBeUQsa0JBQVl6RCxJQUFaLENBQWlCLHFCQUFqQixFQUF3Q0MsSUFBeEMsQ0FBNkMsVUFBN0MsRUFBeUQsSUFBekQ7O0FBRUF3RCxrQkFBWXpELElBQVosQ0FBaUIsK0NBQWpCLEVBQWtFVCxFQUFsRSxDQUFxRSxpQkFBckUsRUFBd0YsWUFBTTtBQUM1RmtFLG9CQUFZekQsSUFBWixDQUFpQixxQkFBakIsRUFBd0NDLElBQXhDLENBQTZDLFVBQTdDLEVBQXlELEtBQXpEO0FBQ0F3RCxvQkFBWXpELElBQVosQ0FBaUIsdUJBQWpCLEVBQTBDQyxJQUExQyxDQUErQyxRQUEvQyxFQUF5RCxLQUF6RDtBQUNELE9BSEQ7QUFJRDs7Ozs7a0JBZmtCdUQsbUM7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQzVCckI7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUF5QkEsSUFBTXJFLElBQUlDLE9BQU9ELENBQWpCOztBQUVBOzs7O0lBR3FCdUUsMkI7Ozs7Ozs7O0FBQ25COzs7OzsyQkFLT2pDLEksRUFBTTtBQUNYLFdBQUtrQywrQkFBTCxDQUFxQ2xDLElBQXJDO0FBQ0EsV0FBS21DLGtDQUFMLENBQXdDbkMsSUFBeEM7QUFDRDs7QUFFRDs7Ozs7Ozs7Ozt1REFPbUNBLEksRUFBTTtBQUFBOztBQUN2Q0EsV0FBS0MsWUFBTCxHQUFvQm5DLEVBQXBCLENBQXVCLFFBQXZCLEVBQWlDLDRCQUFqQyxFQUErRCxVQUFDQyxDQUFELEVBQU87QUFDcEUsWUFBTXFFLFlBQVkxRSxFQUFFSyxFQUFFbUMsYUFBSixDQUFsQjs7QUFFQSxZQUFNbUMsWUFBWUQsVUFBVS9ELEVBQVYsQ0FBYSxVQUFiLENBQWxCO0FBQ0EsWUFBSWdFLFNBQUosRUFBZTtBQUNiLGdCQUFLQyxxQkFBTCxDQUEyQnRDLElBQTNCO0FBQ0QsU0FGRCxNQUVPO0FBQ0wsZ0JBQUt1QyxzQkFBTCxDQUE0QnZDLElBQTVCO0FBQ0Q7O0FBRURBLGFBQUtDLFlBQUwsR0FBb0IxQixJQUFwQixDQUF5QiwwQkFBekIsRUFBcURDLElBQXJELENBQTBELFNBQTFELEVBQXFFNkQsU0FBckU7QUFDRCxPQVhEO0FBWUQ7O0FBRUQ7Ozs7Ozs7Ozs7b0RBT2dDckMsSSxFQUFNO0FBQUE7O0FBQ3BDQSxXQUFLQyxZQUFMLEdBQW9CbkMsRUFBcEIsQ0FBdUIsUUFBdkIsRUFBaUMsMEJBQWpDLEVBQTZELFlBQU07QUFDakUsWUFBTTBFLG1CQUFtQnhDLEtBQUtDLFlBQUwsR0FBb0IxQixJQUFwQixDQUF5QixrQ0FBekIsRUFBNkRxRCxNQUF0Rjs7QUFFQSxZQUFJWSxtQkFBbUIsQ0FBdkIsRUFBMEI7QUFDeEIsaUJBQUtGLHFCQUFMLENBQTJCdEMsSUFBM0I7QUFDRCxTQUZELE1BRU87QUFDTCxpQkFBS3VDLHNCQUFMLENBQTRCdkMsSUFBNUI7QUFDRDtBQUNGLE9BUkQ7QUFTRDs7QUFFRDs7Ozs7Ozs7OzswQ0FPc0JBLEksRUFBTTtBQUMxQkEsV0FBS0MsWUFBTCxHQUFvQjFCLElBQXBCLENBQXlCLHNCQUF6QixFQUFpREMsSUFBakQsQ0FBc0QsVUFBdEQsRUFBa0UsS0FBbEU7QUFDRDs7QUFFRDs7Ozs7Ozs7OzsyQ0FPdUJ3QixJLEVBQU07QUFDM0JBLFdBQUtDLFlBQUwsR0FBb0IxQixJQUFwQixDQUF5QixzQkFBekIsRUFBaURDLElBQWpELENBQXNELFVBQXRELEVBQWtFLElBQWxFO0FBQ0Q7Ozs7O2tCQXhFa0J5RCwyQjs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQ0xyQjs7Ozs7O0FBRUEsSUFBTXZFLElBQUlDLE9BQU9ELENBQWpCOztBQUVBOzs7QUE3QkE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7SUFnQ3FCK0UseUI7QUFDbkIsdUNBQWM7QUFBQTs7QUFBQTs7QUFDWixXQUFPO0FBQ0wzQyxjQUFRLGdCQUFDRSxJQUFEO0FBQUEsZUFBVSxNQUFLRixNQUFMLENBQVlFLElBQVosQ0FBVjtBQUFBO0FBREgsS0FBUDtBQUdEOztBQUVEOzs7Ozs7Ozs7MkJBS09BLEksRUFBTTtBQUFBOztBQUNYQSxXQUFLQyxZQUFMLEdBQW9CbkMsRUFBcEIsQ0FBdUIsT0FBdkIsRUFBZ0MsNEJBQWhDLEVBQThELFVBQUNHLEtBQUQsRUFBVztBQUN2RSxlQUFLd0IsTUFBTCxDQUFZeEIsS0FBWixFQUFtQitCLElBQW5CO0FBQ0QsT0FGRDtBQUdEOztBQUVEOzs7Ozs7Ozs7OzsyQkFRTy9CLEssRUFBTytCLEksRUFBTTtBQUNsQixVQUFNMEMsYUFBYWhGLEVBQUVPLE1BQU1pQyxhQUFSLENBQW5CO0FBQ0EsVUFBTXlDLGlCQUFpQkQsV0FBVzlELElBQVgsQ0FBZ0IsaUJBQWhCLENBQXZCO0FBQ0EsVUFBTWdFLGVBQWVGLFdBQVc5RCxJQUFYLENBQWdCLGNBQWhCLENBQXJCOztBQUVBLFVBQUkrRCxtQkFBbUJFLFNBQW5CLElBQWdDLElBQUlGLGVBQWVmLE1BQXZELEVBQStEO0FBQzdELFlBQUlnQixpQkFBaUJDLFNBQXJCLEVBQWdDO0FBQzlCLGVBQUtDLGdCQUFMLENBQXNCSixVQUF0QixFQUFrQzFDLElBQWxDLEVBQXdDMkMsY0FBeEMsRUFBd0RDLFlBQXhEO0FBQ0QsU0FGRCxNQUVPLElBQUkvRCxRQUFROEQsY0FBUixDQUFKLEVBQTZCO0FBQ2xDLGVBQUtJLFFBQUwsQ0FBY0wsVUFBZCxFQUEwQjFDLElBQTFCO0FBQ0Q7QUFDRixPQU5ELE1BTU87QUFDTCxhQUFLK0MsUUFBTCxDQUFjTCxVQUFkLEVBQTBCMUMsSUFBMUI7QUFDRDtBQUNGOztBQUVEOzs7Ozs7Ozs7cUNBTWlCMEMsVSxFQUFZMUMsSSxFQUFNMkMsYyxFQUFnQkMsWSxFQUFjO0FBQUE7O0FBQy9ELFVBQU1JLHFCQUFxQk4sV0FBVzlELElBQVgsQ0FBZ0Isb0JBQWhCLENBQTNCO0FBQ0EsVUFBTXFFLG1CQUFtQlAsV0FBVzlELElBQVgsQ0FBZ0Isa0JBQWhCLENBQXpCO0FBQ0EsVUFBTXNFLHFCQUFxQlIsV0FBVzlELElBQVgsQ0FBZ0Isb0JBQWhCLENBQTNCOztBQUVBLFVBQU1zQyxRQUFRLElBQUlpQyxlQUFKLENBQWlCO0FBQzdCeEQsWUFBT0ssS0FBS2UsS0FBTCxFQUFQLHdCQUQ2QjtBQUU3QjZCLGtDQUY2QjtBQUc3QkQsc0NBSDZCO0FBSTdCSyw4Q0FKNkI7QUFLN0JDLDBDQUw2QjtBQU03QkM7QUFONkIsT0FBakIsRUFPWDtBQUFBLGVBQU0sT0FBS0gsUUFBTCxDQUFjTCxVQUFkLEVBQTBCMUMsSUFBMUIsQ0FBTjtBQUFBLE9BUFcsQ0FBZDs7QUFTQWtCLFlBQU1rQyxJQUFOO0FBQ0Q7O0FBRUQ7Ozs7Ozs7NkJBSVNWLFUsRUFBWTFDLEksRUFBTTtBQUN6QixVQUFNVixRQUFRNUIsRUFBRSxNQUFNc0MsS0FBS2UsS0FBTCxFQUFOLEdBQXFCLGNBQXZCLENBQWQ7O0FBRUF6QixZQUFNK0QsSUFBTixDQUFXLFFBQVgsRUFBcUJYLFdBQVc5RCxJQUFYLENBQWdCLFVBQWhCLENBQXJCO0FBQ0FVLFlBQU0rRCxJQUFOLENBQVcsUUFBWCxFQUFxQlgsV0FBVzlELElBQVgsQ0FBZ0IsYUFBaEIsQ0FBckI7QUFDQVUsWUFBTUcsTUFBTjtBQUNEOzs7OztrQkEzRWtCZ0QseUI7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQ2hDckI7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUF5QkEsSUFBTS9FLElBQUlDLE9BQU9ELENBQWpCOztBQUVBOzs7O0lBR3FCNEYsc0I7Ozs7Ozs7O0FBQ25COzs7OzsyQkFLT3RELEksRUFBTTtBQUNYLFdBQUt1RCxZQUFMLENBQWtCdkQsSUFBbEI7QUFDQSxXQUFLd0Qsc0JBQUwsQ0FBNEJ4RCxJQUE1QjtBQUNEOztBQUVEOzs7Ozs7OzsyQ0FLdUJBLEksRUFBTTtBQUMzQkEsV0FBS0MsWUFBTCxHQUFvQm5DLEVBQXBCLENBQXVCLE9BQXZCLEVBQWdDLHFCQUFoQyxFQUF1RCxVQUFDRyxLQUFELEVBQVc7QUFDaEUsWUFBTTBFLGlCQUFpQmpGLEVBQUVPLE1BQU1pQyxhQUFSLEVBQXVCdEIsSUFBdkIsQ0FBNEIsaUJBQTVCLENBQXZCOztBQUVBLFlBQUkrRCxlQUFlZixNQUFmLElBQXlCLENBQUMvQyxRQUFROEQsY0FBUixDQUE5QixFQUF1RDtBQUNyRDFFLGdCQUFNUyxjQUFOO0FBQ0Q7QUFDRixPQU5EO0FBT0Q7O0FBRUQ7Ozs7Ozs7O2lDQUthc0IsSSxFQUFNO0FBQ2pCdEMsUUFBRSxJQUFGLEVBQVFzQyxLQUFLQyxZQUFMLEVBQVIsRUFBNkJzQixJQUE3QixDQUFrQyxTQUFTa0MsV0FBVCxHQUF1QjtBQUN2RCxZQUFNQyxhQUFhaEcsRUFBRSxJQUFGLENBQW5COztBQUVBQSxVQUFFLGlEQUFGLEVBQXFEZ0csVUFBckQsRUFBaUVuQyxJQUFqRSxDQUFzRSxTQUFTb0Msd0JBQVQsR0FBb0M7QUFDeEcsY0FBTUMsYUFBYWxHLEVBQUUsSUFBRixDQUFuQjtBQUNBLGNBQU1tRyxjQUFjRCxXQUFXdEYsT0FBWCxDQUFtQixJQUFuQixDQUFwQjs7QUFFQSxjQUFNd0YsaUJBQWlCcEcsRUFBRSxjQUFGLEVBQWtCZ0csVUFBbEIsRUFDcEJLLEdBRG9CLENBQ2hCRixXQURnQixDQUF2Qjs7QUFJQUMseUJBQWVFLFFBQWYsQ0FBd0IsZ0JBQXhCLEVBQTBDQyxLQUExQyxDQUFnRCxZQUFNO0FBQ3BELGdCQUFNdEIsaUJBQWlCaUIsV0FBV2hGLElBQVgsQ0FBZ0IsaUJBQWhCLENBQXZCOztBQUVBLGdCQUFJLENBQUMrRCxlQUFlZixNQUFoQixJQUEwQi9DLFFBQVE4RCxjQUFSLENBQTlCLEVBQXVEO0FBQ3JEOUUsdUJBQVM0QyxRQUFULEdBQW9CbUQsV0FBV1AsSUFBWCxDQUFnQixNQUFoQixDQUFwQjtBQUNEO0FBQ0YsV0FORDtBQU9ELFNBZkQ7QUFnQkQsT0FuQkQ7QUFvQkQ7Ozs7O2tCQXBEa0JDLHNCOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUM5QnJCOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBeUJBLElBQU01RixJQUFJQyxPQUFPRCxDQUFqQjs7QUFFQTs7OztJQUdxQndHLGtDO0FBRW5CLGdEQUFjO0FBQUE7O0FBQUE7O0FBQ1osV0FBTztBQUNMcEUsY0FBUSxnQkFBQ0UsSUFBRDtBQUFBLGVBQVUsTUFBS0YsTUFBTCxDQUFZRSxJQUFaLENBQVY7QUFBQTtBQURILEtBQVA7QUFHRDs7QUFFRDs7Ozs7Ozs7OzJCQUtPQSxJLEVBQU07QUFBQTs7QUFDWEEsV0FBS0MsWUFBTCxHQUFvQm5DLEVBQXBCLENBQXVCLE9BQXZCLEVBQWdDLGtDQUFoQyxFQUFvRSxVQUFDRyxLQUFELEVBQVc7QUFDN0VBLGNBQU1TLGNBQU47O0FBRUEsWUFBTXlGLFlBQVl6RyxFQUFFTyxNQUFNaUMsYUFBUixFQUF1QnRCLElBQXZCLENBQTRCLHNCQUE1QixDQUFsQjs7QUFFQSxZQUFNcUMsU0FBU3ZELFFBQU1zQyxLQUFLZSxLQUFMLEVBQU4sa0NBQWY7QUFDQUUsZUFBT0MsS0FBUCxDQUFhLE1BQWI7O0FBRUFELGVBQU9uRCxFQUFQLENBQVUsT0FBVixFQUFtQiw2QkFBbkIsRUFBa0QsWUFBTTtBQUN0RCxjQUFNc0csOEJBQThCcEUsS0FBS0MsWUFBTCxHQUFvQjFCLElBQXBCLENBQXlCLGtDQUF6QixDQUFwQzs7QUFFQTZGLHNDQUE0QjdDLElBQTVCLENBQWlDLFVBQUNDLENBQUQsRUFBSTZDLFFBQUosRUFBaUI7QUFDaEQsZ0JBQU1DLFNBQVM1RyxFQUFFMkcsUUFBRixDQUFmOztBQUVBLG1CQUFLRSxtQ0FBTCxDQUF5Q0QsT0FBT2xELEdBQVAsRUFBekM7QUFDRCxXQUpEOztBQU1BLGNBQU05QixRQUFRMkIsT0FBTzFDLElBQVAsQ0FBWSxNQUFaLENBQWQ7O0FBRUFlLGdCQUFNK0QsSUFBTixDQUFXLFFBQVgsRUFBcUJjLFNBQXJCO0FBQ0E3RSxnQkFBTUcsTUFBTjtBQUNELFNBYkQ7QUFjRCxPQXRCRDtBQXVCRDs7QUFFRDs7Ozs7Ozs7d0RBS29DK0UsVSxFQUFZO0FBQzlDLFVBQU1DLGtCQUFrQi9HLEVBQUUsdUNBQUYsQ0FBeEI7O0FBRUEsVUFBTWdILGdCQUFnQkQsZ0JBQ25CN0YsSUFEbUIsQ0FDZCxXQURjLEVBRW5CK0YsT0FGbUIsQ0FFWCxXQUZXLEVBRUVILFVBRkYsQ0FBdEI7O0FBS0EsVUFBTUksUUFBUWxILEVBQUVBLEVBQUVtSCxTQUFGLENBQVlILGFBQVosRUFBMkIsQ0FBM0IsQ0FBRixDQUFkO0FBQ0FFLFlBQU14RCxHQUFOLENBQVVvRCxVQUFWOztBQUVBQyxzQkFBZ0JsRixNQUFoQixDQUF1QnFGLEtBQXZCO0FBQ0Q7Ozs7O2tCQXhEa0JWLGtDOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUM5QnJCOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBeUJBLElBQU14RyxJQUFJQyxPQUFPRCxDQUFqQjs7QUFFQTs7OztJQUdxQm9ILGdDO0FBRW5CLDhDQUFjO0FBQUE7O0FBQUE7O0FBQ1osV0FBTztBQUNMaEYsY0FBUSxnQkFBQ0UsSUFBRDtBQUFBLGVBQVUsTUFBS0YsTUFBTCxDQUFZRSxJQUFaLENBQVY7QUFBQTtBQURILEtBQVA7QUFHRDs7QUFFRDs7Ozs7Ozs7OzJCQUtPQSxJLEVBQU07QUFBQTs7QUFDWEEsV0FBS0MsWUFBTCxHQUFvQm5DLEVBQXBCLENBQXVCLE9BQXZCLEVBQWdDLGdDQUFoQyxFQUFrRSxVQUFDRyxLQUFELEVBQVc7QUFDM0VBLGNBQU1TLGNBQU47O0FBRUEsWUFBTXFHLHdCQUF3QnJILFFBQU1zQyxLQUFLZSxLQUFMLEVBQU4sa0NBQTlCO0FBQ0FnRSw4QkFBc0I3RCxLQUF0QixDQUE0QixNQUE1Qjs7QUFFQTZELDhCQUFzQmpILEVBQXRCLENBQXlCLE9BQXpCLEVBQWtDLDZCQUFsQyxFQUFpRSxZQUFNO0FBQ3JFLGNBQU1rSCxVQUFVdEgsRUFBRU8sTUFBTWlDLGFBQVIsQ0FBaEI7QUFDQSxjQUFNc0UsYUFBYVEsUUFBUXBHLElBQVIsQ0FBYSxhQUFiLENBQW5COztBQUVBLGlCQUFLcUcsaUJBQUwsQ0FBdUJULFVBQXZCOztBQUVBLGNBQU1sRixRQUFReUYsc0JBQXNCeEcsSUFBdEIsQ0FBMkIsTUFBM0IsQ0FBZDs7QUFFQWUsZ0JBQU0rRCxJQUFOLENBQVcsUUFBWCxFQUFxQjJCLFFBQVFwRyxJQUFSLENBQWEscUJBQWIsQ0FBckI7QUFDQVUsZ0JBQU1HLE1BQU47QUFDRCxTQVZEO0FBV0QsT0FqQkQ7QUFrQkQ7O0FBRUQ7Ozs7Ozs7Ozs7c0NBT2tCK0UsVSxFQUFZO0FBQzVCLFVBQU1VLCtCQUErQnhILEVBQUUsdUNBQUYsQ0FBckM7O0FBRUEsVUFBTWdILGdCQUFnQlEsNkJBQ25CdEcsSUFEbUIsQ0FDZCxXQURjLEVBRW5CK0YsT0FGbUIsQ0FFWCxXQUZXLEVBRUVPLDZCQUE2QkMsUUFBN0IsR0FBd0N2RCxNQUYxQyxDQUF0Qjs7QUFJQSxVQUFNZ0QsUUFBUWxILEVBQUVBLEVBQUVtSCxTQUFGLENBQVlILGFBQVosRUFBMkIsQ0FBM0IsQ0FBRixDQUFkO0FBQ0FFLFlBQU14RCxHQUFOLENBQVVvRCxVQUFWOztBQUVBVSxtQ0FBNkIzRixNQUE3QixDQUFvQ3FGLEtBQXBDO0FBQ0Q7Ozs7O2tCQXBEa0JFLGdDOzs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQzlCckI7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUF5QkEsSUFBTXBILElBQUlDLE9BQU9ELENBQWpCOztBQUVBOzs7O0lBR3FCMEgsWSxHQUNuQix3QkFBYztBQUFBOztBQUNaMUgsSUFBRUcsUUFBRixFQUFZQyxFQUFaLENBQWUsT0FBZixFQUF3QixtQkFBeEIsRUFBNkMsVUFBQ0csS0FBRCxFQUFXO0FBQ3RETixXQUFPOEMsUUFBUCxHQUFrQi9DLEVBQUVPLE1BQU1pQyxhQUFSLEVBQXVCdEIsSUFBdkIsQ0FBNEIsZUFBNUIsQ0FBbEI7QUFDRCxHQUZEO0FBR0QsQzs7a0JBTGtCd0csWTs7Ozs7OztBQzlCckI7QUFDQTtBQUNBLEU7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQ0ZBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBeUJBLElBQU0xSCxJQUFJMkgsT0FBTzNILENBQWpCOztBQUVBOzs7OztJQUlNMkMsWTs7QUFFSjs7O0FBR0Esd0JBQVlpRixLQUFaLEVBQW1CO0FBQUE7O0FBQ2pCLFNBQUtDLFFBQUwsR0FBZ0IscUJBQWhCO0FBQ0EsU0FBS0MsT0FBTCxHQUFlOUgsRUFBRTRILEtBQUYsRUFBUy9HLElBQVQsQ0FBYyxLQUFLZ0gsUUFBbkIsQ0FBZjtBQUNEOztBQUVEOzs7Ozs7OzZCQUdTO0FBQUE7O0FBQ1AsV0FBS0MsT0FBTCxDQUFhMUgsRUFBYixDQUFnQixPQUFoQixFQUF5QixVQUFDQyxDQUFELEVBQU87QUFDOUIsWUFBTTBILFVBQVUvSCxFQUFFSyxFQUFFMkgsY0FBSixDQUFoQjtBQUNBLGNBQUtDLGFBQUwsQ0FBbUJGLE9BQW5CLEVBQTRCLE1BQUtHLHdCQUFMLENBQThCSCxPQUE5QixDQUE1QjtBQUNELE9BSEQ7QUFJRDs7QUFFRDs7Ozs7Ozs7MkJBS09JLFUsRUFBWUMsUyxFQUFXO0FBQzVCLFVBQU1MLFVBQVUsS0FBS0QsT0FBTCxDQUFhbkgsRUFBYiwyQkFBd0N3SCxVQUF4QyxRQUFoQjtBQUNBLFVBQUksQ0FBQ0osT0FBTCxFQUFjO0FBQ1osY0FBTSxJQUFJTSxLQUFKLHNCQUE2QkYsVUFBN0IsdUJBQU47QUFDRDs7QUFFRCxXQUFLRixhQUFMLENBQW1CRixPQUFuQixFQUE0QkssU0FBNUI7QUFDRDs7QUFFRDs7Ozs7Ozs7O2tDQU1jRSxNLEVBQVFGLFMsRUFBVztBQUMvQm5JLGFBQU84QyxRQUFQLEdBQWtCLEtBQUt3RixPQUFMLENBQWFELE9BQU9wSCxJQUFQLENBQVksYUFBWixDQUFiLEVBQTBDa0gsY0FBYyxNQUFmLEdBQXlCLE1BQXpCLEdBQWtDLEtBQTNFLEVBQWtGRSxPQUFPcEgsSUFBUCxDQUFZLFlBQVosQ0FBbEYsQ0FBbEI7QUFDRDs7QUFFRDs7Ozs7Ozs7OzZDQU15Qm9ILE0sRUFBUTtBQUMvQixhQUFPQSxPQUFPcEgsSUFBUCxDQUFZLGVBQVosTUFBaUMsS0FBakMsR0FBeUMsTUFBekMsR0FBa0QsS0FBekQ7QUFDRDs7QUFFRDs7Ozs7Ozs7Ozs7NEJBUVFzSCxPLEVBQVNKLFMsRUFBV0ssTSxFQUFRO0FBQ2xDLFVBQU1DLE1BQU0sSUFBSUMsR0FBSixDQUFRMUksT0FBTzhDLFFBQVAsQ0FBZ0I2RixJQUF4QixDQUFaO0FBQ0EsVUFBTUMsU0FBU0gsSUFBSUksWUFBbkI7O0FBRUEsVUFBSUwsTUFBSixFQUFZO0FBQ1ZJLGVBQU9FLEdBQVAsQ0FBV04sU0FBTyxXQUFsQixFQUErQkQsT0FBL0I7QUFDQUssZUFBT0UsR0FBUCxDQUFXTixTQUFPLGFBQWxCLEVBQWlDTCxTQUFqQztBQUNELE9BSEQsTUFHTztBQUNMUyxlQUFPRSxHQUFQLENBQVcsU0FBWCxFQUFzQlAsT0FBdEI7QUFDQUssZUFBT0UsR0FBUCxDQUFXLFdBQVgsRUFBd0JYLFNBQXhCO0FBQ0Q7O0FBRUQsYUFBT00sSUFBSU0sUUFBSixFQUFQO0FBQ0Q7Ozs7O2tCQUdZckcsWTs7Ozs7Ozs7Ozs7Ozs7QUM3R2Y7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUF5QkE7Ozs7QUFJQSxJQUFNM0MsSUFBSTJILE9BQU8zSCxDQUFqQjs7QUFFQSxJQUFNaUosT0FBTyxTQUFTQyxXQUFULENBQXFCUixHQUFyQixFQUEwQlMsV0FBMUIsRUFBdUM7QUFDaERuSixJQUFFb0osSUFBRixDQUFPVixHQUFQLEVBQVlXLElBQVosQ0FBaUI7QUFBQSxXQUFNcEosT0FBTzhDLFFBQVAsQ0FBZ0J1RyxNQUFoQixDQUF1QkgsV0FBdkIsQ0FBTjtBQUFBLEdBQWpCO0FBQ0gsQ0FGRDs7a0JBSWVGLEk7Ozs7Ozs7Ozs7Ozs7O2tCQ0tTeEQsWTtBQXhDeEI7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUF5QkEsSUFBTXpGLElBQUlDLE9BQU9ELENBQWpCOztBQUVBOzs7Ozs7Ozs7Ozs7O0FBYWUsU0FBU3lGLFlBQVQsQ0FBc0JvRCxNQUF0QixFQUE4QlUsZUFBOUIsRUFBK0M7QUFBQTs7QUFDNUQ7QUFENEQsTUFFckR0SCxFQUZxRCxHQUVyQzRHLE1BRnFDLENBRXJENUcsRUFGcUQ7QUFBQSxNQUVqRHVILFFBRmlELEdBRXJDWCxNQUZxQyxDQUVqRFcsUUFGaUQ7O0FBRzVELE9BQUtoRyxLQUFMLEdBQWFpRyxNQUFNWixNQUFOLENBQWI7O0FBRUE7QUFDQSxPQUFLdEYsTUFBTCxHQUFjdkQsRUFBRSxLQUFLd0QsS0FBTCxDQUFXa0csU0FBYixDQUFkOztBQUVBLE9BQUtoRSxJQUFMLEdBQVksWUFBTTtBQUNoQixVQUFLbkMsTUFBTCxDQUFZQyxLQUFaO0FBQ0QsR0FGRDs7QUFJQSxPQUFLQSxLQUFMLENBQVdtRyxhQUFYLENBQXlCQyxnQkFBekIsQ0FBMEMsT0FBMUMsRUFBbURMLGVBQW5EOztBQUVBLE9BQUtoRyxNQUFMLENBQVlDLEtBQVosQ0FBa0I7QUFDaEJxRyxjQUFXTCxXQUFXLElBQVgsR0FBa0IsUUFEYjtBQUVoQk0sY0FBVU4sYUFBYXJFLFNBQWIsR0FBeUJxRSxRQUF6QixHQUFvQyxJQUY5QjtBQUdoQkEsY0FBVUEsYUFBYXJFLFNBQWIsR0FBeUJxRSxRQUF6QixHQUFvQyxJQUg5QjtBQUloQjlELFVBQU07QUFKVSxHQUFsQjs7QUFPQSxPQUFLbkMsTUFBTCxDQUFZbkQsRUFBWixDQUFlLGlCQUFmLEVBQWtDLFlBQU07QUFDdENELGFBQVM0SixhQUFULE9BQTJCOUgsRUFBM0IsRUFBaUMrSCxNQUFqQztBQUNELEdBRkQ7O0FBSUE3SixXQUFTOEosSUFBVCxDQUFjQyxXQUFkLENBQTBCLEtBQUsxRyxLQUFMLENBQVdrRyxTQUFyQztBQUNEOztBQUVEOzs7Ozs7QUFNQSxTQUFTRCxLQUFULE9BUUs7QUFBQSxxQkFORHhILEVBTUM7QUFBQSxNQU5EQSxFQU1DLDJCQU5JLGVBTUo7QUFBQSxNQUxEaUQsWUFLQyxRQUxEQSxZQUtDO0FBQUEsaUNBSkRELGNBSUM7QUFBQSxNQUpEQSxjQUlDLHVDQUpnQixFQUloQjtBQUFBLG1DQUhETSxnQkFHQztBQUFBLE1BSERBLGdCQUdDLHlDQUhrQixPQUdsQjtBQUFBLG1DQUZERCxrQkFFQztBQUFBLE1BRkRBLGtCQUVDLHlDQUZvQixRQUVwQjtBQUFBLG1DQURERSxrQkFDQztBQUFBLE1BRERBLGtCQUNDLHlDQURvQixhQUNwQjs7QUFDSCxNQUFNaEMsUUFBUSxFQUFkOztBQUVBO0FBQ0FBLFFBQU1rRyxTQUFOLEdBQWtCdkosU0FBU2dLLGFBQVQsQ0FBdUIsS0FBdkIsQ0FBbEI7QUFDQTNHLFFBQU1rRyxTQUFOLENBQWdCVSxTQUFoQixDQUEwQkMsR0FBMUIsQ0FBOEIsT0FBOUIsRUFBdUMsTUFBdkM7QUFDQTdHLFFBQU1rRyxTQUFOLENBQWdCekgsRUFBaEIsR0FBcUJBLEVBQXJCOztBQUVBO0FBQ0F1QixRQUFNOEcsTUFBTixHQUFlbkssU0FBU2dLLGFBQVQsQ0FBdUIsS0FBdkIsQ0FBZjtBQUNBM0csUUFBTThHLE1BQU4sQ0FBYUYsU0FBYixDQUF1QkMsR0FBdkIsQ0FBMkIsY0FBM0I7O0FBRUE7QUFDQTdHLFFBQU0rRyxPQUFOLEdBQWdCcEssU0FBU2dLLGFBQVQsQ0FBdUIsS0FBdkIsQ0FBaEI7QUFDQTNHLFFBQU0rRyxPQUFOLENBQWNILFNBQWQsQ0FBd0JDLEdBQXhCLENBQTRCLGVBQTVCOztBQUVBO0FBQ0E3RyxRQUFNZ0gsTUFBTixHQUFlckssU0FBU2dLLGFBQVQsQ0FBdUIsS0FBdkIsQ0FBZjtBQUNBM0csUUFBTWdILE1BQU4sQ0FBYUosU0FBYixDQUF1QkMsR0FBdkIsQ0FBMkIsY0FBM0I7O0FBRUE7QUFDQSxNQUFJbkYsWUFBSixFQUFrQjtBQUNoQjFCLFVBQU1pSCxLQUFOLEdBQWN0SyxTQUFTZ0ssYUFBVCxDQUF1QixJQUF2QixDQUFkO0FBQ0EzRyxVQUFNaUgsS0FBTixDQUFZTCxTQUFaLENBQXNCQyxHQUF0QixDQUEwQixhQUExQjtBQUNBN0csVUFBTWlILEtBQU4sQ0FBWUMsU0FBWixHQUF3QnhGLFlBQXhCO0FBQ0Q7O0FBRUQ7QUFDQTFCLFFBQU1tSCxTQUFOLEdBQWtCeEssU0FBU2dLLGFBQVQsQ0FBdUIsUUFBdkIsQ0FBbEI7QUFDQTNHLFFBQU1tSCxTQUFOLENBQWdCUCxTQUFoQixDQUEwQkMsR0FBMUIsQ0FBOEIsT0FBOUI7QUFDQTdHLFFBQU1tSCxTQUFOLENBQWdCQyxZQUFoQixDQUE2QixNQUE3QixFQUFxQyxRQUFyQztBQUNBcEgsUUFBTW1ILFNBQU4sQ0FBZ0JFLE9BQWhCLENBQXdCQyxPQUF4QixHQUFrQyxPQUFsQztBQUNBdEgsUUFBTW1ILFNBQU4sQ0FBZ0JELFNBQWhCLEdBQTRCLEdBQTVCOztBQUVBO0FBQ0FsSCxRQUFNeUcsSUFBTixHQUFhOUosU0FBU2dLLGFBQVQsQ0FBdUIsS0FBdkIsQ0FBYjtBQUNBM0csUUFBTXlHLElBQU4sQ0FBV0csU0FBWCxDQUFxQkMsR0FBckIsQ0FBeUIsWUFBekIsRUFBdUMsV0FBdkMsRUFBb0Qsb0JBQXBEOztBQUVBO0FBQ0E3RyxRQUFNdUgsT0FBTixHQUFnQjVLLFNBQVNnSyxhQUFULENBQXVCLEdBQXZCLENBQWhCO0FBQ0EzRyxRQUFNdUgsT0FBTixDQUFjWCxTQUFkLENBQXdCQyxHQUF4QixDQUE0QixpQkFBNUI7QUFDQTdHLFFBQU11SCxPQUFOLENBQWNMLFNBQWQsR0FBMEJ6RixjQUExQjs7QUFFQTtBQUNBekIsUUFBTXdILE1BQU4sR0FBZTdLLFNBQVNnSyxhQUFULENBQXVCLEtBQXZCLENBQWY7QUFDQTNHLFFBQU13SCxNQUFOLENBQWFaLFNBQWIsQ0FBdUJDLEdBQXZCLENBQTJCLGNBQTNCOztBQUVBO0FBQ0E3RyxRQUFNeUgsV0FBTixHQUFvQjlLLFNBQVNnSyxhQUFULENBQXVCLFFBQXZCLENBQXBCO0FBQ0EzRyxRQUFNeUgsV0FBTixDQUFrQkwsWUFBbEIsQ0FBK0IsTUFBL0IsRUFBdUMsUUFBdkM7QUFDQXBILFFBQU15SCxXQUFOLENBQWtCYixTQUFsQixDQUE0QkMsR0FBNUIsQ0FBZ0MsS0FBaEMsRUFBdUMsdUJBQXZDLEVBQWdFLFFBQWhFO0FBQ0E3RyxRQUFNeUgsV0FBTixDQUFrQkosT0FBbEIsQ0FBMEJDLE9BQTFCLEdBQW9DLE9BQXBDO0FBQ0F0SCxRQUFNeUgsV0FBTixDQUFrQlAsU0FBbEIsR0FBOEJuRixnQkFBOUI7O0FBRUE7QUFDQS9CLFFBQU1tRyxhQUFOLEdBQXNCeEosU0FBU2dLLGFBQVQsQ0FBdUIsUUFBdkIsQ0FBdEI7QUFDQTNHLFFBQU1tRyxhQUFOLENBQW9CaUIsWUFBcEIsQ0FBaUMsTUFBakMsRUFBeUMsUUFBekM7QUFDQXBILFFBQU1tRyxhQUFOLENBQW9CUyxTQUFwQixDQUE4QkMsR0FBOUIsQ0FBa0MsS0FBbEMsRUFBeUM3RSxrQkFBekMsRUFBNkQsUUFBN0QsRUFBdUUsb0JBQXZFO0FBQ0FoQyxRQUFNbUcsYUFBTixDQUFvQmtCLE9BQXBCLENBQTRCQyxPQUE1QixHQUFzQyxPQUF0QztBQUNBdEgsUUFBTW1HLGFBQU4sQ0FBb0JlLFNBQXBCLEdBQWdDcEYsa0JBQWhDOztBQUVBO0FBQ0EsTUFBSUosWUFBSixFQUFrQjtBQUNoQjFCLFVBQU1nSCxNQUFOLENBQWEzSSxNQUFiLENBQW9CMkIsTUFBTWlILEtBQTFCLEVBQWlDakgsTUFBTW1ILFNBQXZDO0FBQ0QsR0FGRCxNQUVPO0FBQ0xuSCxVQUFNZ0gsTUFBTixDQUFhTixXQUFiLENBQXlCMUcsTUFBTW1ILFNBQS9CO0FBQ0Q7O0FBRURuSCxRQUFNeUcsSUFBTixDQUFXQyxXQUFYLENBQXVCMUcsTUFBTXVILE9BQTdCO0FBQ0F2SCxRQUFNd0gsTUFBTixDQUFhbkosTUFBYixDQUFvQjJCLE1BQU15SCxXQUExQixFQUF1Q3pILE1BQU1tRyxhQUE3QztBQUNBbkcsUUFBTStHLE9BQU4sQ0FBYzFJLE1BQWQsQ0FBcUIyQixNQUFNZ0gsTUFBM0IsRUFBbUNoSCxNQUFNeUcsSUFBekMsRUFBK0N6RyxNQUFNd0gsTUFBckQ7QUFDQXhILFFBQU04RyxNQUFOLENBQWFKLFdBQWIsQ0FBeUIxRyxNQUFNK0csT0FBL0I7QUFDQS9HLFFBQU1rRyxTQUFOLENBQWdCUSxXQUFoQixDQUE0QjFHLE1BQU04RyxNQUFsQzs7QUFFQSxTQUFPOUcsS0FBUDtBQUNELEM7Ozs7Ozs7Ozs7QUNwSUQ7Ozs7QUFDQTs7OztBQUNBOzs7O0FBQ0E7Ozs7QUFDQTs7OztBQUNBOzs7O0FBQ0E7Ozs7QUFDQTs7OztBQUNBOzs7O0FBQ0E7Ozs7QUFDQTs7OztBQUNBOzs7O0FBQ0E7Ozs7QUFDQTs7OztBQUVBOzs7O0FBRUE7Ozs7QUFFQTs7OztBQUNBOzs7Ozs7QUE3Q0E7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUErQ0EsSUFBTXhELElBQUlDLE9BQU9ELENBQWpCOztBQUVBQSxFQUFFLFlBQU07QUFDTixNQUFNa0wsZUFBZSxJQUFJbEosY0FBSixDQUFTLFVBQVQsQ0FBckI7O0FBRUFrSixlQUFhQyxZQUFiLENBQTBCLElBQUlDLDZCQUFKLEVBQTFCO0FBQ0FGLGVBQWFDLFlBQWIsQ0FBMEIsSUFBSWxJLHFDQUFKLEVBQTFCO0FBQ0FpSSxlQUFhQyxZQUFiLENBQTBCLElBQUk5SSwrQkFBSixFQUExQjtBQUNBNkksZUFBYUMsWUFBYixDQUEwQixJQUFJMUksMEJBQUosRUFBMUI7QUFDQXlJLGVBQWFDLFlBQWIsQ0FBMEIsSUFBSTVHLHFDQUFKLEVBQTFCO0FBQ0EyRyxlQUFhQyxZQUFiLENBQTBCLElBQUlFLG1DQUFKLEVBQTFCO0FBQ0FILGVBQWFDLFlBQWIsQ0FBMEIsSUFBSUcsbUNBQUosRUFBMUI7QUFDQUosZUFBYUMsWUFBYixDQUEwQixJQUFJdkYsZ0NBQUosRUFBMUI7QUFDQXNGLGVBQWFDLFlBQWIsQ0FBMEIsSUFBSUksaUNBQUosRUFBMUI7QUFDQUwsZUFBYUMsWUFBYixDQUEwQixJQUFJM0UsNENBQUosRUFBMUI7QUFDQTBFLGVBQWFDLFlBQWIsQ0FBMEIsSUFBSS9ELDBDQUFKLEVBQTFCO0FBQ0E4RCxlQUFhQyxZQUFiLENBQTBCLElBQUk5Ryw2Q0FBSixFQUExQjs7QUFFQSxNQUFNbUgsZUFBZSxJQUFJQyxzQkFBSixDQUFpQix1QkFBakIsQ0FBckI7QUFDQUQsZUFBYUwsWUFBYixDQUEwQixJQUFJTyxvQ0FBSixFQUExQjs7QUFFQTtBQUNBLE1BQUl4TCxxQkFBSjs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLE1BQUl3SCxzQkFBSjs7QUFFQSxNQUFJM0csMEJBQUo7QUFDRCxDQTdCRCxFOzs7Ozs7O0FDakRBO0FBQ0E7QUFDQTtBQUNBLHVDQUF1QyxnQzs7Ozs7OztBQ0h2QztBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxHQUFHLFVBQVU7QUFDYjtBQUNBO0FBQ0E7QUFDQSxFOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUNmQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQXlCQSxJQUFNZixJQUFJMkgsT0FBTzNILENBQWpCOztBQUVBOzs7O0lBR3FCdUwsdUI7Ozs7Ozs7OztBQUVuQjs7Ozs7MkJBS09qSixJLEVBQU07QUFBQTs7QUFDWCxVQUFNcUosU0FBU3JKLEtBQUtDLFlBQUwsR0FBb0IxQixJQUFwQixDQUF5QixhQUF6QixDQUFmO0FBQ0E4SyxhQUFPOUssSUFBUCxDQUFZLG1CQUFaLEVBQWlDVCxFQUFqQyxDQUFvQyxPQUFwQyxFQUE2QyxVQUFDQyxDQUFELEVBQU87QUFDbERBLFVBQUVXLGNBQUY7QUFDQSxjQUFLNEssWUFBTCxDQUFrQjVMLEVBQUVLLEVBQUUySCxjQUFKLENBQWxCO0FBQ0QsT0FIRDtBQUlEOztBQUVEOzs7Ozs7O2lDQUlhNkQsRyxFQUFLO0FBQ2hCLFVBQU1DLFlBQVlELElBQUkzSyxJQUFKLENBQVMsV0FBVCxDQUFsQjs7QUFFQSxXQUFLNkssYUFBTCxDQUFtQkQsU0FBbkI7QUFDRDs7QUFFRDs7Ozs7Ozs7O2tDQU1jQSxTLEVBQVc7QUFDdkIsVUFBTWxLLFFBQVE1QixFQUFFLFFBQUYsRUFBWTtBQUN4QmdNLGdCQUFRRixTQURnQjtBQUV4QjFLLGdCQUFRO0FBRmdCLE9BQVosRUFHWFUsUUFIVyxDQUdGLE1BSEUsQ0FBZDs7QUFLQUYsWUFBTUcsTUFBTjtBQUNEOzs7OztrQkF0Q2tCd0osdUI7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUM5QnJCOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBeUJBLElBQU12TCxJQUFJQyxPQUFPRCxDQUFqQjs7QUFFQTs7OztJQUdxQmlNLHlCO0FBQ25CLHVDQUFjO0FBQUE7O0FBQUE7O0FBQ1osV0FBTztBQUNMN0osY0FBUSxnQkFBQ0UsSUFBRDtBQUFBLGVBQVUsTUFBS0YsTUFBTCxDQUFZRSxJQUFaLENBQVY7QUFBQTtBQURILEtBQVA7QUFHRDs7OzsyQkFFTUEsSSxFQUFNO0FBQUE7O0FBQ1hBLFdBQUtRLGtCQUFMLEdBQTBCMUMsRUFBMUIsQ0FBNkIsT0FBN0IsRUFBc0MsNEJBQXRDLEVBQW9FLFVBQUNHLEtBQUQsRUFBVztBQUM3RSxlQUFLMkwsWUFBTCxDQUFrQjNMLEtBQWxCLEVBQXlCK0IsSUFBekI7QUFDRCxPQUZEO0FBR0Q7O0FBRUQ7Ozs7Ozs7Ozs7OztpQ0FTYS9CLEssRUFBTytCLEksRUFBTTtBQUN4QixVQUFNMEMsYUFBYWhGLEVBQUVPLE1BQU1pQyxhQUFSLENBQW5CO0FBQ0EsVUFBTXlDLGlCQUFpQkQsV0FBVzlELElBQVgsQ0FBZ0IsaUJBQWhCLENBQXZCOztBQUVBLFVBQUksT0FBTytELGNBQVAsS0FBMEIsV0FBMUIsSUFBeUMsSUFBSUEsZUFBZWYsTUFBNUQsSUFBc0UsQ0FBQy9DLFFBQVE4RCxjQUFSLENBQTNFLEVBQW9HO0FBQ2hHO0FBQ0g7O0FBRUQsVUFBTXJELFFBQVE1QixFQUFFLE1BQU1zQyxLQUFLZSxLQUFMLEVBQU4sR0FBcUIsY0FBdkIsQ0FBZDs7QUFFQXpCLFlBQU0rRCxJQUFOLENBQVcsUUFBWCxFQUFxQlgsV0FBVzlELElBQVgsQ0FBZ0IsS0FBaEIsQ0FBckI7QUFDQVUsWUFBTStELElBQU4sQ0FBVyxRQUFYLEVBQXFCWCxXQUFXOUQsSUFBWCxDQUFnQixRQUFoQixDQUFyQjtBQUNBVSxZQUFNZixJQUFOLENBQVcsaUJBQWlCeUIsS0FBS2UsS0FBTCxFQUFqQixHQUFnQyxZQUEzQyxFQUF5REssR0FBekQsQ0FBNkRzQixXQUFXOUQsSUFBWCxDQUFnQixNQUFoQixDQUE3RDtBQUNBVSxZQUFNRyxNQUFOO0FBQ0Q7Ozs7O2tCQXBDa0JrSyx5Qjs7Ozs7OztBQzlCckI7QUFDQTtBQUNBO0FBQ0EsR0FBRztBQUNIO0FBQ0E7QUFDQSxFOzs7Ozs7O0FDTkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLG1FQUFtRTtBQUNuRTtBQUNBLHFGQUFxRjtBQUNyRjtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsV0FBVztBQUNYLFNBQVM7QUFDVDtBQUNBO0FBQ0E7QUFDQTtBQUNBLEtBQUs7QUFDTDtBQUNBO0FBQ0EsK0NBQStDO0FBQy9DO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGNBQWM7QUFDZCxjQUFjO0FBQ2QsY0FBYztBQUNkLGNBQWM7QUFDZCxlQUFlO0FBQ2YsZUFBZTtBQUNmLGVBQWU7QUFDZixnQkFBZ0I7QUFDaEIseUI7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQzVEQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQXlCQSxJQUFNak0sSUFBSUMsT0FBT0QsQ0FBakI7O0FBRUE7Ozs7SUFHcUIwTCwwQjs7Ozs7Ozs7O0FBRW5COzs7OzsyQkFLT1MsVyxFQUFhO0FBQ2xCLFVBQU16QyxZQUFZeUMsWUFBWTVKLFlBQVosRUFBbEI7QUFDQW1ILGdCQUFVdEosRUFBVixDQUFhLE9BQWIsRUFBc0IseUJBQXRCLEVBQWlELFVBQUNnTSxHQUFELEVBQVM7QUFDeEQxQyxrQkFBVU0sTUFBVjs7QUFFQSxZQUFNL0ksT0FBT2pCLEVBQUVvTSxJQUFJM0wsTUFBTixDQUFiO0FBQ0EsWUFBTWlJLE1BQU16SCxLQUFLQyxJQUFMLENBQVUsVUFBVixDQUFaO0FBQ0EsWUFBTW1MLFdBQVdwTCxLQUFLQyxJQUFMLENBQVUsVUFBVixDQUFqQjs7QUFFQSxZQUFJd0gsR0FBSixFQUFTO0FBQ1A7QUFDQTFJLFlBQUVvSixJQUFGLENBQ0VWLEdBREYsRUFFRTtBQUNFNEQsbUJBQU8sQ0FEVDtBQUVFNUssa0JBQU0ySztBQUZSLFdBRkY7QUFPRDtBQUNGLE9BakJEO0FBa0JEOzs7OztrQkEzQmtCWCwwQjs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FDOUJyQjs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQXlCQSxJQUFNMUwsSUFBSUMsT0FBT0QsQ0FBakI7O0FBRUE7Ozs7SUFHcUJ5TCxZOztBQUVuQjs7Ozs7QUFLQSx3QkFBWXhKLEVBQVosRUFBZ0I7QUFBQTs7QUFDZCxTQUFLQSxFQUFMLEdBQVVBLEVBQVY7QUFDQSxTQUFLQyxVQUFMLEdBQWtCbEMsRUFBRSxNQUFNLEtBQUtpQyxFQUFiLENBQWxCO0FBQ0Q7O0FBRUQ7Ozs7Ozs7OzttQ0FLZTtBQUNiLGFBQU8sS0FBS0MsVUFBWjtBQUNEOztBQUVEOzs7Ozs7OztpQ0FLYUMsUyxFQUFXO0FBQ3RCQSxnQkFBVUMsTUFBVixDQUFpQixJQUFqQjtBQUNEOzs7OztrQkE1QmtCcUosWTs7Ozs7OztBQzlCckI7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsQ0FBQzs7QUFFRDtBQUNBO0FBQ0E7QUFDQSxDQUFDO0FBQ0Q7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBLDRDQUE0Qzs7QUFFNUMiLCJmaWxlIjoiY3VzdG9tZXIuYnVuZGxlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gaWRlbnRpdHkgZnVuY3Rpb24gZm9yIGNhbGxpbmcgaGFybW9ueSBpbXBvcnRzIHdpdGggdGhlIGNvcnJlY3QgY29udGV4dFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5pID0gZnVuY3Rpb24odmFsdWUpIHsgcmV0dXJuIHZhbHVlOyB9O1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHtcbiBcdFx0XHRcdGNvbmZpZ3VyYWJsZTogZmFsc2UsXG4gXHRcdFx0XHRlbnVtZXJhYmxlOiB0cnVlLFxuIFx0XHRcdFx0Z2V0OiBnZXR0ZXJcbiBcdFx0XHR9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCJcIjtcblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSA0OTEpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIHdlYnBhY2svYm9vdHN0cmFwIDNhNjE3Y2VkMjllYmNjYjZhMWQwIiwiXCJ1c2Ugc3RyaWN0XCI7XG5cbmV4cG9ydHMuX19lc01vZHVsZSA9IHRydWU7XG5cbmV4cG9ydHMuZGVmYXVsdCA9IGZ1bmN0aW9uIChpbnN0YW5jZSwgQ29uc3RydWN0b3IpIHtcbiAgaWYgKCEoaW5zdGFuY2UgaW5zdGFuY2VvZiBDb25zdHJ1Y3RvcikpIHtcbiAgICB0aHJvdyBuZXcgVHlwZUVycm9yKFwiQ2Fubm90IGNhbGwgYSBjbGFzcyBhcyBhIGZ1bmN0aW9uXCIpO1xuICB9XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9iYWJlbC1ydW50aW1lL2hlbHBlcnMvY2xhc3NDYWxsQ2hlY2suanNcbi8vIG1vZHVsZSBpZCA9IDBcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJcInVzZSBzdHJpY3RcIjtcblxuZXhwb3J0cy5fX2VzTW9kdWxlID0gdHJ1ZTtcblxudmFyIF9kZWZpbmVQcm9wZXJ0eSA9IHJlcXVpcmUoXCIuLi9jb3JlLWpzL29iamVjdC9kZWZpbmUtcHJvcGVydHlcIik7XG5cbnZhciBfZGVmaW5lUHJvcGVydHkyID0gX2ludGVyb3BSZXF1aXJlRGVmYXVsdChfZGVmaW5lUHJvcGVydHkpO1xuXG5mdW5jdGlvbiBfaW50ZXJvcFJlcXVpcmVEZWZhdWx0KG9iaikgeyByZXR1cm4gb2JqICYmIG9iai5fX2VzTW9kdWxlID8gb2JqIDogeyBkZWZhdWx0OiBvYmogfTsgfVxuXG5leHBvcnRzLmRlZmF1bHQgPSBmdW5jdGlvbiAoKSB7XG4gIGZ1bmN0aW9uIGRlZmluZVByb3BlcnRpZXModGFyZ2V0LCBwcm9wcykge1xuICAgIGZvciAodmFyIGkgPSAwOyBpIDwgcHJvcHMubGVuZ3RoOyBpKyspIHtcbiAgICAgIHZhciBkZXNjcmlwdG9yID0gcHJvcHNbaV07XG4gICAgICBkZXNjcmlwdG9yLmVudW1lcmFibGUgPSBkZXNjcmlwdG9yLmVudW1lcmFibGUgfHwgZmFsc2U7XG4gICAgICBkZXNjcmlwdG9yLmNvbmZpZ3VyYWJsZSA9IHRydWU7XG4gICAgICBpZiAoXCJ2YWx1ZVwiIGluIGRlc2NyaXB0b3IpIGRlc2NyaXB0b3Iud3JpdGFibGUgPSB0cnVlO1xuICAgICAgKDAsIF9kZWZpbmVQcm9wZXJ0eTIuZGVmYXVsdCkodGFyZ2V0LCBkZXNjcmlwdG9yLmtleSwgZGVzY3JpcHRvcik7XG4gICAgfVxuICB9XG5cbiAgcmV0dXJuIGZ1bmN0aW9uIChDb25zdHJ1Y3RvciwgcHJvdG9Qcm9wcywgc3RhdGljUHJvcHMpIHtcbiAgICBpZiAocHJvdG9Qcm9wcykgZGVmaW5lUHJvcGVydGllcyhDb25zdHJ1Y3Rvci5wcm90b3R5cGUsIHByb3RvUHJvcHMpO1xuICAgIGlmIChzdGF0aWNQcm9wcykgZGVmaW5lUHJvcGVydGllcyhDb25zdHJ1Y3Rvciwgc3RhdGljUHJvcHMpO1xuICAgIHJldHVybiBDb25zdHJ1Y3RvcjtcbiAgfTtcbn0oKTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vYmFiZWwtcnVudGltZS9oZWxwZXJzL2NyZWF0ZUNsYXNzLmpzXG4vLyBtb2R1bGUgaWQgPSAxXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwidmFyIGRQICAgICAgICAgPSByZXF1aXJlKCcuL19vYmplY3QtZHAnKVxuICAsIGNyZWF0ZURlc2MgPSByZXF1aXJlKCcuL19wcm9wZXJ0eS1kZXNjJyk7XG5tb2R1bGUuZXhwb3J0cyA9IHJlcXVpcmUoJy4vX2Rlc2NyaXB0b3JzJykgPyBmdW5jdGlvbihvYmplY3QsIGtleSwgdmFsdWUpe1xuICByZXR1cm4gZFAuZihvYmplY3QsIGtleSwgY3JlYXRlRGVzYygxLCB2YWx1ZSkpO1xufSA6IGZ1bmN0aW9uKG9iamVjdCwga2V5LCB2YWx1ZSl7XG4gIG9iamVjdFtrZXldID0gdmFsdWU7XG4gIHJldHVybiBvYmplY3Q7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faGlkZS5qc1xuLy8gbW9kdWxlIGlkID0gMTBcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwidmFyIGlzT2JqZWN0ID0gcmVxdWlyZSgnLi9faXMtb2JqZWN0Jyk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgaWYoIWlzT2JqZWN0KGl0KSl0aHJvdyBUeXBlRXJyb3IoaXQgKyAnIGlzIG5vdCBhbiBvYmplY3QhJyk7XG4gIHJldHVybiBpdDtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19hbi1vYmplY3QuanNcbi8vIG1vZHVsZSBpZCA9IDExXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oYml0bWFwLCB2YWx1ZSl7XG4gIHJldHVybiB7XG4gICAgZW51bWVyYWJsZSAgOiAhKGJpdG1hcCAmIDEpLFxuICAgIGNvbmZpZ3VyYWJsZTogIShiaXRtYXAgJiAyKSxcbiAgICB3cml0YWJsZSAgICA6ICEoYml0bWFwICYgNCksXG4gICAgdmFsdWUgICAgICAgOiB2YWx1ZVxuICB9O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3Byb3BlcnR5LWRlc2MuanNcbi8vIG1vZHVsZSBpZCA9IDEyXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIi8vIG9wdGlvbmFsIC8gc2ltcGxlIGNvbnRleHQgYmluZGluZ1xudmFyIGFGdW5jdGlvbiA9IHJlcXVpcmUoJy4vX2EtZnVuY3Rpb24nKTtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oZm4sIHRoYXQsIGxlbmd0aCl7XG4gIGFGdW5jdGlvbihmbik7XG4gIGlmKHRoYXQgPT09IHVuZGVmaW5lZClyZXR1cm4gZm47XG4gIHN3aXRjaChsZW5ndGgpe1xuICAgIGNhc2UgMTogcmV0dXJuIGZ1bmN0aW9uKGEpe1xuICAgICAgcmV0dXJuIGZuLmNhbGwodGhhdCwgYSk7XG4gICAgfTtcbiAgICBjYXNlIDI6IHJldHVybiBmdW5jdGlvbihhLCBiKXtcbiAgICAgIHJldHVybiBmbi5jYWxsKHRoYXQsIGEsIGIpO1xuICAgIH07XG4gICAgY2FzZSAzOiByZXR1cm4gZnVuY3Rpb24oYSwgYiwgYyl7XG4gICAgICByZXR1cm4gZm4uY2FsbCh0aGF0LCBhLCBiLCBjKTtcbiAgICB9O1xuICB9XG4gIHJldHVybiBmdW5jdGlvbigvKiAuLi5hcmdzICovKXtcbiAgICByZXR1cm4gZm4uYXBwbHkodGhhdCwgYXJndW1lbnRzKTtcbiAgfTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19jdHguanNcbi8vIG1vZHVsZSBpZCA9IDEzXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENob2ljZVRhYmxlIGlzIHJlc3BvbnNpYmxlIGZvciBtYW5hZ2luZyBjb21tb24gYWN0aW9ucyBpbiBjaG9pY2UgdGFibGUgZm9ybSB0eXBlXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIENob2ljZVRhYmxlIHtcbiAgLyoqXG4gICAqIEluaXQgY29uc3RydWN0b3JcbiAgICovXG4gIGNvbnN0cnVjdG9yKCkge1xuICAgICQoZG9jdW1lbnQpLm9uKCdjaGFuZ2UnLCAnLmpzLWNob2ljZS10YWJsZS1zZWxlY3QtYWxsJywgKGUpID0+IHtcbiAgICAgIHRoaXMuaGFuZGxlU2VsZWN0QWxsKGUpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIENoZWNrL3VuY2hlY2sgYWxsIGJveGVzIGluIHRhYmxlXG4gICAqXG4gICAqIEBwYXJhbSB7RXZlbnR9IGV2ZW50XG4gICAqL1xuICBoYW5kbGVTZWxlY3RBbGwoZXZlbnQpIHtcbiAgICBjb25zdCAkc2VsZWN0QWxsQ2hlY2tib3hlcyA9ICQoZXZlbnQudGFyZ2V0KTtcbiAgICBjb25zdCBpc1NlbGVjdEFsbENoZWNrZWQgPSAkc2VsZWN0QWxsQ2hlY2tib3hlcy5pcygnOmNoZWNrZWQnKTtcblxuICAgICRzZWxlY3RBbGxDaGVja2JveGVzLmNsb3Nlc3QoJ3RhYmxlJykuZmluZCgndGJvZHkgaW5wdXQ6Y2hlY2tib3gnKS5wcm9wKCdjaGVja2VkJywgaXNTZWxlY3RBbGxDaGVja2VkKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9jaG9pY2UtdGFibGUuanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDb21wb25lbnQgd2hpY2ggYWxsb3dzIHN1Ym1pdHRpbmcgdmVyeSBzaW1wbGUgZm9ybXMgd2l0aG91dCBoYXZpbmcgdG8gdXNlIDxmb3JtPiBlbGVtZW50LlxuICpcbiAqIFVzZWZ1bCB3aGVuIHBlcmZvcm1pbmcgYWN0aW9ucyBvbiByZXNvdXJjZSB3aGVyZSBVUkwgY29udGFpbnMgYWxsIG5lZWRlZCBkYXRhLlxuICogRm9yIGV4YW1wbGUsIHRvIHRvZ2dsZSBjYXRlZ29yeSBzdGF0dXMgdmlhIFwiUE9TVCAvY2F0ZWdvcmllcy8yL3RvZ2dsZS1zdGF0dXMpXCJcbiAqIG9yIGRlbGV0ZSBjb3ZlciBpbWFnZSB2aWEgXCJQT1NUIC9jYXRlZ29yaWVzLzIvZGVsZXRlLWNvdmVyLWltYWdlXCIuXG4gKlxuICogVXNhZ2UgZXhhbXBsZSBpbiB0ZW1wbGF0ZTpcbiAqXG4gKiA8YnV0dG9uIGNsYXNzPVwianMtZm9ybS1zdWJtaXQtYnRuXCJcbiAqICAgICAgICAgZGF0YS1mb3JtLXN1Ym1pdC11cmw9XCIvbXktY3VzdG9tLXVybFwiICAgICAgICAgIC8vIChyZXF1aXJlZCkgVVJMIHRvIHdoaWNoIGZvcm0gd2lsbCBiZSBzdWJtaXR0ZWRcbiAqICAgICAgICAgZGF0YS1tZXRob2Q9XCJHRVR8UE9TVHxERUxFVEV8UEFUQ0hcIiAgICAgICAgICAgIC8vIChvcHRpb25hbCkgc3BlY2lmeSB0aGUgdmVyYiB0byB1c2UgZm9yIHRoZSByZXF1ZXN0LlxuICogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIFBPU1QgaXMgdGFrZW4gYnkgZGVmYXVsdCBpZiBub3QgdmFsdWUgaXMgc2V0XG4gKiAgICAgICAgIGRhdGEtZm9ybS1jc3JmLXRva2VuPVwibXktZ2VuZXJhdGVkLWNzcmYtdG9rZW5cIiAvLyAob3B0aW9uYWwpIHRvIGluY3JlYXNlIHNlY3VyaXR5XG4gKiAgICAgICAgIGRhdGEtZm9ybS1jb25maXJtLW1lc3NhZ2U9XCJBcmUgeW91IHN1cmU/XCIgICAgICAvLyAob3B0aW9uYWwpIHRvIGNvbmZpcm0gYWN0aW9uIGJlZm9yZSBzdWJtaXRcbiAqICAgICAgICAgdHlwZT1cImJ1dHRvblwiICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIG1ha2Ugc3VyZSBpdHMgc2ltcGxlIGJ1dHRvblxuICogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIHNvIHdlIGNhbiBhdm9pZCBzdWJtaXR0aW5nIGFjdHVhbCBmb3JtXG4gKiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gd2hlbiBvdXIgYnV0dG9uIGlzIGRlZmluZWQgaW5zaWRlIGZvcm1cbiAqID5cbiAqICAgICBDbGljayBtZSB0byBzdWJtaXQgZm9ybVxuICogPC9idXR0b24+XG4gKlxuICogSW4gcGFnZSBzcGVjaWZpYyBKUyB5b3UgaGF2ZSB0byBlbmFibGUgdGhpcyBmZWF0dXJlOlxuICpcbiAqIG5ldyBGb3JtU3VibWl0QnV0dG9uKCk7XG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEZvcm1TdWJtaXRCdXR0b24ge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCAnLmpzLWZvcm0tc3VibWl0LWJ0bicsIGZ1bmN0aW9uIChldmVudCkge1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgY29uc3QgJGJ0biA9ICQodGhpcyk7XG5cbiAgICAgIGlmICgkYnRuLmRhdGEoJ2Zvcm0tY29uZmlybS1tZXNzYWdlJykgJiYgZmFsc2UgPT09IGNvbmZpcm0oJGJ0bi5kYXRhKCdmb3JtLWNvbmZpcm0tbWVzc2FnZScpKSkge1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG5cbiAgICAgIGxldCBtZXRob2QgPSAnUE9TVCc7XG4gICAgICBsZXQgYWRkSW5wdXQgPSBudWxsO1xuXG4gICAgICBpZiAoJGJ0bi5kYXRhKCdtZXRob2QnKSkge1xuICAgICAgICBjb25zdCBidG5NZXRob2QgPSAkYnRuLmRhdGEoJ21ldGhvZCcpO1xuICAgICAgICBjb25zdCBpc0dldE9yUG9zdE1ldGhvZCA9IFsnR0VUJywgJ1BPU1QnXS5pbmNsdWRlcyhidG5NZXRob2QpO1xuICAgICAgICBtZXRob2QgPSBpc0dldE9yUG9zdE1ldGhvZCA/IGJ0bk1ldGhvZCA6ICdQT1NUJztcblxuICAgICAgICBpZiAoIWlzR2V0T3JQb3N0TWV0aG9kKSB7XG4gICAgICAgICAgYWRkSW5wdXQgPSAkKCc8aW5wdXQ+Jywge1xuICAgICAgICAgICAgdHlwZTogJ19oaWRkZW4nLFxuICAgICAgICAgICAgbmFtZTogJ19tZXRob2QnLFxuICAgICAgICAgICAgdmFsdWU6IG1ldGhvZCxcbiAgICAgICAgICB9KTtcbiAgICAgICAgfVxuICAgICAgfVxuXG4gICAgICBjb25zdCAkZm9ybSA9ICQoJzxmb3JtPicsIHtcbiAgICAgICAgJ2FjdGlvbic6ICRidG4uZGF0YSgnZm9ybS1zdWJtaXQtdXJsJyksXG4gICAgICAgICdtZXRob2QnOiBtZXRob2QsXG4gICAgICB9KTtcblxuICAgICAgaWYgKGFkZElucHV0KSB7XG4gICAgICAgICRmb3JtLmFwcGVuZChhZGRJbnB1dCk7XG4gICAgICB9XG5cbiAgICAgIGlmICgkYnRuLmRhdGEoJ2Zvcm0tY3NyZi10b2tlbicpKSB7XG4gICAgICAgICRmb3JtLmFwcGVuZCgkKCc8aW5wdXQ+Jywge1xuICAgICAgICAgICd0eXBlJzogJ19oaWRkZW4nLFxuICAgICAgICAgICduYW1lJzogJ19jc3JmX3Rva2VuJyxcbiAgICAgICAgICAndmFsdWUnOiAkYnRuLmRhdGEoJ2Zvcm0tY3NyZi10b2tlbicpXG4gICAgICAgIH0pKTtcbiAgICAgIH1cblxuICAgICAgJGZvcm0uYXBwZW5kVG8oJ2JvZHknKS5zdWJtaXQoKTtcbiAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9mb3JtLXN1Ym1pdC1idXR0b24uanMiLCIvLyA3LjEuMSBUb1ByaW1pdGl2ZShpbnB1dCBbLCBQcmVmZXJyZWRUeXBlXSlcbnZhciBpc09iamVjdCA9IHJlcXVpcmUoJy4vX2lzLW9iamVjdCcpO1xuLy8gaW5zdGVhZCBvZiB0aGUgRVM2IHNwZWMgdmVyc2lvbiwgd2UgZGlkbid0IGltcGxlbWVudCBAQHRvUHJpbWl0aXZlIGNhc2Vcbi8vIGFuZCB0aGUgc2Vjb25kIGFyZ3VtZW50IC0gZmxhZyAtIHByZWZlcnJlZCB0eXBlIGlzIGEgc3RyaW5nXG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0LCBTKXtcbiAgaWYoIWlzT2JqZWN0KGl0KSlyZXR1cm4gaXQ7XG4gIHZhciBmbiwgdmFsO1xuICBpZihTICYmIHR5cGVvZiAoZm4gPSBpdC50b1N0cmluZykgPT0gJ2Z1bmN0aW9uJyAmJiAhaXNPYmplY3QodmFsID0gZm4uY2FsbChpdCkpKXJldHVybiB2YWw7XG4gIGlmKHR5cGVvZiAoZm4gPSBpdC52YWx1ZU9mKSA9PSAnZnVuY3Rpb24nICYmICFpc09iamVjdCh2YWwgPSBmbi5jYWxsKGl0KSkpcmV0dXJuIHZhbDtcbiAgaWYoIVMgJiYgdHlwZW9mIChmbiA9IGl0LnRvU3RyaW5nKSA9PSAnZnVuY3Rpb24nICYmICFpc09iamVjdCh2YWwgPSBmbi5jYWxsKGl0KSkpcmV0dXJuIHZhbDtcbiAgdGhyb3cgVHlwZUVycm9yKFwiQ2FuJ3QgY29udmVydCBvYmplY3QgdG8gcHJpbWl0aXZlIHZhbHVlXCIpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3RvLXByaW1pdGl2ZS5qc1xuLy8gbW9kdWxlIGlkID0gMTRcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwidmFyIGlzT2JqZWN0ID0gcmVxdWlyZSgnLi9faXMtb2JqZWN0JylcbiAgLCBkb2N1bWVudCA9IHJlcXVpcmUoJy4vX2dsb2JhbCcpLmRvY3VtZW50XG4gIC8vIGluIG9sZCBJRSB0eXBlb2YgZG9jdW1lbnQuY3JlYXRlRWxlbWVudCBpcyAnb2JqZWN0J1xuICAsIGlzID0gaXNPYmplY3QoZG9jdW1lbnQpICYmIGlzT2JqZWN0KGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQpO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCl7XG4gIHJldHVybiBpcyA/IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoaXQpIDoge307XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZG9tLWNyZWF0ZS5qc1xuLy8gbW9kdWxlIGlkID0gMTZcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwibW9kdWxlLmV4cG9ydHMgPSAhcmVxdWlyZSgnLi9fZGVzY3JpcHRvcnMnKSAmJiAhcmVxdWlyZSgnLi9fZmFpbHMnKShmdW5jdGlvbigpe1xuICByZXR1cm4gT2JqZWN0LmRlZmluZVByb3BlcnR5KHJlcXVpcmUoJy4vX2RvbS1jcmVhdGUnKSgnZGl2JyksICdhJywge2dldDogZnVuY3Rpb24oKXsgcmV0dXJuIDc7IH19KS5hICE9IDc7XG59KTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2llOC1kb20tZGVmaW5lLmpzXG4vLyBtb2R1bGUgaWQgPSAxN1xuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJtb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgaWYodHlwZW9mIGl0ICE9ICdmdW5jdGlvbicpdGhyb3cgVHlwZUVycm9yKGl0ICsgJyBpcyBub3QgYSBmdW5jdGlvbiEnKTtcbiAgcmV0dXJuIGl0O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2EtZnVuY3Rpb24uanNcbi8vIG1vZHVsZSBpZCA9IDE4XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIm1vZHVsZS5leHBvcnRzID0geyBcImRlZmF1bHRcIjogcmVxdWlyZShcImNvcmUtanMvbGlicmFyeS9mbi9vYmplY3QvZGVmaW5lLXByb3BlcnR5XCIpLCBfX2VzTW9kdWxlOiB0cnVlIH07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9vYmplY3QvZGVmaW5lLXByb3BlcnR5LmpzXG4vLyBtb2R1bGUgaWQgPSAxOVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCIvLyBUaGFuaydzIElFOCBmb3IgaGlzIGZ1bm55IGRlZmluZVByb3BlcnR5XG5tb2R1bGUuZXhwb3J0cyA9ICFyZXF1aXJlKCcuL19mYWlscycpKGZ1bmN0aW9uKCl7XG4gIHJldHVybiBPYmplY3QuZGVmaW5lUHJvcGVydHkoe30sICdhJywge2dldDogZnVuY3Rpb24oKXsgcmV0dXJuIDc7IH19KS5hICE9IDc7XG59KTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2Rlc2NyaXB0b3JzLmpzXG4vLyBtb2R1bGUgaWQgPSAyXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInJlcXVpcmUoJy4uLy4uL21vZHVsZXMvZXM2Lm9iamVjdC5kZWZpbmUtcHJvcGVydHknKTtcbnZhciAkT2JqZWN0ID0gcmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9fY29yZScpLk9iamVjdDtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24gZGVmaW5lUHJvcGVydHkoaXQsIGtleSwgZGVzYyl7XG4gIHJldHVybiAkT2JqZWN0LmRlZmluZVByb3BlcnR5KGl0LCBrZXksIGRlc2MpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC9kZWZpbmUtcHJvcGVydHkuanNcbi8vIG1vZHVsZSBpZCA9IDIwXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciAkZXhwb3J0ID0gcmVxdWlyZSgnLi9fZXhwb3J0Jyk7XG4vLyAxOS4xLjIuNCAvIDE1LjIuMy42IE9iamVjdC5kZWZpbmVQcm9wZXJ0eShPLCBQLCBBdHRyaWJ1dGVzKVxuJGV4cG9ydCgkZXhwb3J0LlMgKyAkZXhwb3J0LkYgKiAhcmVxdWlyZSgnLi9fZGVzY3JpcHRvcnMnKSwgJ09iamVjdCcsIHtkZWZpbmVQcm9wZXJ0eTogcmVxdWlyZSgnLi9fb2JqZWN0LWRwJykuZn0pO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczYub2JqZWN0LmRlZmluZS1wcm9wZXJ0eS5qc1xuLy8gbW9kdWxlIGlkID0gMjFcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgaXMgcmVzcG9uc2libGUgZm9yIGhhbmRsaW5nIEdyaWQgZXZlbnRzXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEdyaWQge1xuICAvKipcbiAgICogR3JpZCBpZFxuICAgKlxuICAgKiBAcGFyYW0ge3N0cmluZ30gaWRcbiAgICovXG4gIGNvbnN0cnVjdG9yKGlkKSB7XG4gICAgdGhpcy5pZCA9IGlkO1xuICAgIHRoaXMuJGNvbnRhaW5lciA9ICQoJyMnICsgdGhpcy5pZCArICdfZ3JpZCcpO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBncmlkIGlkXG4gICAqXG4gICAqIEByZXR1cm5zIHtzdHJpbmd9XG4gICAqL1xuICBnZXRJZCgpIHtcbiAgICByZXR1cm4gdGhpcy5pZDtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgZ3JpZCBjb250YWluZXJcbiAgICpcbiAgICogQHJldHVybnMge2pRdWVyeX1cbiAgICovXG4gIGdldENvbnRhaW5lcigpIHtcbiAgICByZXR1cm4gdGhpcy4kY29udGFpbmVyO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBncmlkIGhlYWRlciBjb250YWluZXJcbiAgICpcbiAgICogQHJldHVybnMge2pRdWVyeX1cbiAgICovXG4gIGdldEhlYWRlckNvbnRhaW5lcigpIHtcbiAgICByZXR1cm4gdGhpcy4kY29udGFpbmVyLmNsb3Nlc3QoJy5qcy1ncmlkLXBhbmVsJykuZmluZCgnLmpzLWdyaWQtaGVhZGVyJyk7XG4gIH1cblxuICAvKipcbiAgICogRXh0ZW5kIGdyaWQgd2l0aCBleHRlcm5hbCBleHRlbnNpb25zXG4gICAqXG4gICAqIEBwYXJhbSB7b2JqZWN0fSBleHRlbnNpb25cbiAgICovXG4gIGFkZEV4dGVuc2lvbihleHRlbnNpb24pIHtcbiAgICBleHRlbnNpb24uZXh0ZW5kKHRoaXMpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZ3JpZC5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuaW1wb3J0IHJlc2V0U2VhcmNoIGZyb20gJ0BhcHAvdXRpbHMvcmVzZXRfc2VhcmNoJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIEZpbHRlcnNSZXNldEV4dGVuc2lvbiBleHRlbmRzIGdyaWQgd2l0aCBmaWx0ZXJzIHJlc2V0dGluZ1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBGaWx0ZXJzUmVzZXRFeHRlbnNpb24ge1xuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLXJlc2V0LXNlYXJjaCcsIChldmVudCkgPT4ge1xuICAgICAgcmVzZXRTZWFyY2goJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCd1cmwnKSwgJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdyZWRpcmVjdCcpKTtcbiAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXJlc2V0LWV4dGVuc2lvbi5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuaW1wb3J0IFRhYmxlU29ydGluZyBmcm9tICdAYXBwL3V0aWxzL3RhYmxlLXNvcnRpbmcnO1xuXG4vKipcbiAqIENsYXNzIFJlbG9hZExpc3RFeHRlbnNpb24gZXh0ZW5kcyBncmlkIHdpdGggXCJMaXN0IHJlbG9hZFwiIGFjdGlvblxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBTb3J0aW5nRXh0ZW5zaW9uIHtcbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBjb25zdCAkc29ydGFibGVUYWJsZSA9IGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgndGFibGUudGFibGUnKTtcblxuICAgIG5ldyBUYWJsZVNvcnRpbmcoJHNvcnRhYmxlVGFibGUpLmF0dGFjaCgpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3NvcnRpbmctZXh0ZW5zaW9uLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG4vKipcbiAqIENsYXNzIFJlbG9hZExpc3RFeHRlbnNpb24gZXh0ZW5kcyBncmlkIHdpdGggXCJMaXN0IHJlbG9hZFwiIGFjdGlvblxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBSZWxvYWRMaXN0RXh0ZW5zaW9uIHtcbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldEhlYWRlckNvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtY29tbW9uX3JlZnJlc2hfbGlzdC1ncmlkLWFjdGlvbicsICgpID0+IHtcbiAgICAgIGxvY2F0aW9uLnJlbG9hZCgpO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3JlbG9hZC1saXN0LWV4dGVuc2lvbi5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIEV4cG9ydFRvU3FsTWFuYWdlckV4dGVuc2lvbiBleHRlbmRzIGdyaWQgd2l0aCBleHBvcnRpbmcgcXVlcnkgdG8gU1FMIE1hbmFnZXJcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgRXhwb3J0VG9TcWxNYW5hZ2VyRXh0ZW5zaW9uIHtcbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldEhlYWRlckNvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtY29tbW9uX3Nob3dfcXVlcnktZ3JpZC1hY3Rpb24nLCAoKSA9PiB0aGlzLl9vblNob3dTcWxRdWVyeUNsaWNrKGdyaWQpKTtcbiAgICBncmlkLmdldEhlYWRlckNvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtY29tbW9uX2V4cG9ydF9zcWxfbWFuYWdlci1ncmlkLWFjdGlvbicsICgpID0+IHRoaXMuX29uRXhwb3J0U3FsTWFuYWdlckNsaWNrKGdyaWQpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJbnZva2VkIHdoZW4gY2xpY2tpbmcgb24gdGhlIFwic2hvdyBzcWwgcXVlcnlcIiB0b29sYmFyIGJ1dHRvblxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9vblNob3dTcWxRdWVyeUNsaWNrKGdyaWQpIHtcbiAgICBjb25zdCAkc3FsTWFuYWdlckZvcm0gPSAkKCcjJyArIGdyaWQuZ2V0SWQoKSArICdfY29tbW9uX3Nob3dfcXVlcnlfbW9kYWxfZm9ybScpO1xuICAgIHRoaXMuX2ZpbGxFeHBvcnRGb3JtKCRzcWxNYW5hZ2VyRm9ybSwgZ3JpZCk7XG5cbiAgICBjb25zdCAkbW9kYWwgPSAkKCcjJyArIGdyaWQuZ2V0SWQoKSArICdfZ3JpZF9jb21tb25fc2hvd19xdWVyeV9tb2RhbCcpO1xuICAgICRtb2RhbC5tb2RhbCgnc2hvdycpO1xuXG4gICAgJG1vZGFsLm9uKCdjbGljaycsICcuYnRuLXNxbC1zdWJtaXQnLCAoKSA9PiAkc3FsTWFuYWdlckZvcm0uc3VibWl0KCkpO1xuICB9XG5cbiAgLyoqXG4gICAqIEludm9rZWQgd2hlbiBjbGlja2luZyBvbiB0aGUgXCJleHBvcnQgdG8gdGhlIHNxbCBxdWVyeVwiIHRvb2xiYXIgYnV0dG9uXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uRXhwb3J0U3FsTWFuYWdlckNsaWNrKGdyaWQpIHtcbiAgICBjb25zdCAkc3FsTWFuYWdlckZvcm0gPSAkKCcjJyArIGdyaWQuZ2V0SWQoKSArICdfY29tbW9uX3Nob3dfcXVlcnlfbW9kYWxfZm9ybScpO1xuXG4gICAgdGhpcy5fZmlsbEV4cG9ydEZvcm0oJHNxbE1hbmFnZXJGb3JtLCBncmlkKTtcblxuICAgICRzcWxNYW5hZ2VyRm9ybS5zdWJtaXQoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBGaWxsIGV4cG9ydCBmb3JtIHdpdGggU1FMIGFuZCBpdCdzIG5hbWVcbiAgICpcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRzcWxNYW5hZ2VyRm9ybVxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9maWxsRXhwb3J0Rm9ybSgkc3FsTWFuYWdlckZvcm0sIGdyaWQpIHtcbiAgICBjb25zdCBxdWVyeSA9IGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgnLmpzLWdyaWQtdGFibGUnKS5kYXRhKCdxdWVyeScpO1xuXG4gICAgJHNxbE1hbmFnZXJGb3JtLmZpbmQoJ3RleHRhcmVhW25hbWU9XCJzcWxcIl0nKS52YWwocXVlcnkpO1xuICAgICRzcWxNYW5hZ2VyRm9ybS5maW5kKCdpbnB1dFtuYW1lPVwibmFtZVwiXScpLnZhbCh0aGlzLl9nZXROYW1lRnJvbUJyZWFkY3J1bWIoKSk7XG4gIH1cblxuICAvKipcbiAgICogR2V0IGV4cG9ydCBuYW1lIGZyb20gcGFnZSdzIGJyZWFkY3J1bWJcbiAgICpcbiAgICogQHJldHVybiB7U3RyaW5nfVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2dldE5hbWVGcm9tQnJlYWRjcnVtYigpIHtcbiAgICBjb25zdCAkYnJlYWRjcnVtYnMgPSAkKCcuaGVhZGVyLXRvb2xiYXInKS5maW5kKCcuYnJlYWRjcnVtYi1pdGVtJyk7XG4gICAgbGV0IG5hbWUgPSAnJztcblxuICAgICRicmVhZGNydW1icy5lYWNoKChpLCBpdGVtKSA9PiB7XG4gICAgICBjb25zdCAkYnJlYWRjcnVtYiA9ICQoaXRlbSk7XG5cbiAgICAgIGNvbnN0IGJyZWFkY3J1bWJUaXRsZSA9IDAgPCAkYnJlYWRjcnVtYi5maW5kKCdhJykubGVuZ3RoID9cbiAgICAgICAgJGJyZWFkY3J1bWIuZmluZCgnYScpLnRleHQoKSA6XG4gICAgICAgICRicmVhZGNydW1iLnRleHQoKTtcblxuICAgICAgaWYgKDAgPCBuYW1lLmxlbmd0aCkge1xuICAgICAgICBuYW1lID0gbmFtZS5jb25jYXQoJyA+ICcpO1xuICAgICAgfVxuXG4gICAgICBuYW1lID0gbmFtZS5jb25jYXQoYnJlYWRjcnVtYlRpdGxlKTtcbiAgICB9KTtcblxuICAgIHJldHVybiBuYW1lO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2V4cG9ydC10by1zcWwtbWFuYWdlci1leHRlbnNpb24uanMiLCJ2YXIgY29yZSA9IG1vZHVsZS5leHBvcnRzID0ge3ZlcnNpb246ICcyLjQuMCd9O1xuaWYodHlwZW9mIF9fZSA9PSAnbnVtYmVyJylfX2UgPSBjb3JlOyAvLyBlc2xpbnQtZGlzYWJsZS1saW5lIG5vLXVuZGVmXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19jb3JlLmpzXG4vLyBtb2R1bGUgaWQgPSAzXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuLyoqXG4gKiBSZXNwb25zaWJsZSBmb3IgZ3JpZCBmaWx0ZXJzIHNlYXJjaCBhbmQgcmVzZXQgYnV0dG9uIGF2YWlsYWJpbGl0eSB3aGVuIGZpbHRlciBpbnB1dHMgY2hhbmdlcy5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgRmlsdGVyc1N1Ym1pdEJ1dHRvbkVuYWJsZXJFeHRlbnNpb24ge1xuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgY29uc3QgJGZpbHRlcnNSb3cgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5jb2x1bW4tZmlsdGVycycpO1xuICAgICRmaWx0ZXJzUm93LmZpbmQoJy5ncmlkLXNlYXJjaC1idXR0b24nKS5wcm9wKCdkaXNhYmxlZCcsIHRydWUpO1xuXG4gICAgJGZpbHRlcnNSb3cuZmluZCgnaW5wdXQ6bm90KC5qcy1idWxrLWFjdGlvbi1zZWxlY3QtYWxsKSwgc2VsZWN0Jykub24oJ2lucHV0IGRwLmNoYW5nZScsICgpID0+IHtcbiAgICAgICRmaWx0ZXJzUm93LmZpbmQoJy5ncmlkLXNlYXJjaC1idXR0b24nKS5wcm9wKCdkaXNhYmxlZCcsIGZhbHNlKTtcbiAgICAgICRmaWx0ZXJzUm93LmZpbmQoJy5qcy1ncmlkLXJlc2V0LWJ1dHRvbicpLnByb3AoJ2hpZGRlbicsIGZhbHNlKTtcbiAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXN1Ym1pdC1idXR0b24tZW5hYmxlci1leHRlbnNpb24uanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBCdWxrQWN0aW9uU2VsZWN0Q2hlY2tib3hFeHRlbnNpb25cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQnVsa0FjdGlvbkNoZWNrYm94RXh0ZW5zaW9uIHtcbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkIHdpdGggYnVsayBhY3Rpb24gY2hlY2tib3hlcyBoYW5kbGluZyBmdW5jdGlvbmFsaXR5XG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICB0aGlzLl9oYW5kbGVCdWxrQWN0aW9uQ2hlY2tib3hTZWxlY3QoZ3JpZCk7XG4gICAgdGhpcy5faGFuZGxlQnVsa0FjdGlvblNlbGVjdEFsbENoZWNrYm94KGdyaWQpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhhbmRsZXMgXCJTZWxlY3QgYWxsXCIgYnV0dG9uIGluIHRoZSBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hhbmRsZUJ1bGtBY3Rpb25TZWxlY3RBbGxDaGVja2JveChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2hhbmdlJywgJy5qcy1idWxrLWFjdGlvbi1zZWxlY3QtYWxsJywgKGUpID0+IHtcbiAgICAgIGNvbnN0ICRjaGVja2JveCA9ICQoZS5jdXJyZW50VGFyZ2V0KTtcblxuICAgICAgY29uc3QgaXNDaGVja2VkID0gJGNoZWNrYm94LmlzKCc6Y2hlY2tlZCcpO1xuICAgICAgaWYgKGlzQ2hlY2tlZCkge1xuICAgICAgICB0aGlzLl9lbmFibGVCdWxrQWN0aW9uc0J0bihncmlkKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHRoaXMuX2Rpc2FibGVCdWxrQWN0aW9uc0J0bihncmlkKTtcbiAgICAgIH1cblxuICAgICAgZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCcuanMtYnVsay1hY3Rpb24tY2hlY2tib3gnKS5wcm9wKCdjaGVja2VkJywgaXNDaGVja2VkKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGVzIGVhY2ggYnVsayBhY3Rpb24gY2hlY2tib3ggc2VsZWN0IGluIHRoZSBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hhbmRsZUJ1bGtBY3Rpb25DaGVja2JveFNlbGVjdChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2hhbmdlJywgJy5qcy1idWxrLWFjdGlvbi1jaGVja2JveCcsICgpID0+IHtcbiAgICAgIGNvbnN0IGNoZWNrZWRSb3dzQ291bnQgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5qcy1idWxrLWFjdGlvbi1jaGVja2JveDpjaGVja2VkJykubGVuZ3RoO1xuXG4gICAgICBpZiAoY2hlY2tlZFJvd3NDb3VudCA+IDApIHtcbiAgICAgICAgdGhpcy5fZW5hYmxlQnVsa0FjdGlvbnNCdG4oZ3JpZCk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICB0aGlzLl9kaXNhYmxlQnVsa0FjdGlvbnNCdG4oZ3JpZCk7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogRW5hYmxlIGJ1bGsgYWN0aW9ucyBidXR0b25cbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfZW5hYmxlQnVsa0FjdGlvbnNCdG4oZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgnLmpzLWJ1bGstYWN0aW9ucy1idG4nKS5wcm9wKCdkaXNhYmxlZCcsIGZhbHNlKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBEaXNhYmxlIGJ1bGsgYWN0aW9ucyBidXR0b25cbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfZGlzYWJsZUJ1bGtBY3Rpb25zQnRuKGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5qcy1idWxrLWFjdGlvbnMtYnRuJykucHJvcCgnZGlzYWJsZWQnLCB0cnVlKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9idWxrLWFjdGlvbi1jaGVja2JveC1leHRlbnNpb24uanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmltcG9ydCBDb25maXJtTW9kYWwgZnJvbSAnQGNvbXBvbmVudHMvbW9kYWwnO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogSGFuZGxlcyBzdWJtaXQgb2YgZ3JpZCBhY3Rpb25zXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFN1Ym1pdEJ1bGtBY3Rpb25FeHRlbnNpb24ge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICByZXR1cm4ge1xuICAgICAgZXh0ZW5kOiAoZ3JpZCkgPT4gdGhpcy5leHRlbmQoZ3JpZCksXG4gICAgfTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZCB3aXRoIGJ1bGsgYWN0aW9uIHN1Ym1pdHRpbmdcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkub24oJ2NsaWNrJywgJy5qcy1idWxrLWFjdGlvbi1zdWJtaXQtYnRuJywgKGV2ZW50KSA9PiB7XG4gICAgICB0aGlzLnN1Ym1pdChldmVudCwgZ3JpZCk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogSGFuZGxlIGJ1bGsgYWN0aW9uIHN1Ym1pdHRpbmdcbiAgICpcbiAgICogQHBhcmFtIHtFdmVudH0gZXZlbnRcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBzdWJtaXQoZXZlbnQsIGdyaWQpIHtcbiAgICBjb25zdCAkc3VibWl0QnRuID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICBjb25zdCBjb25maXJtTWVzc2FnZSA9ICRzdWJtaXRCdG4uZGF0YSgnY29uZmlybS1tZXNzYWdlJyk7XG4gICAgY29uc3QgY29uZmlybVRpdGxlID0gJHN1Ym1pdEJ0bi5kYXRhKCdjb25maXJtVGl0bGUnKTtcblxuICAgIGlmIChjb25maXJtTWVzc2FnZSAhPT0gdW5kZWZpbmVkICYmIDAgPCBjb25maXJtTWVzc2FnZS5sZW5ndGgpIHtcbiAgICAgIGlmIChjb25maXJtVGl0bGUgIT09IHVuZGVmaW5lZCkge1xuICAgICAgICB0aGlzLnNob3dDb25maXJtTW9kYWwoJHN1Ym1pdEJ0biwgZ3JpZCwgY29uZmlybU1lc3NhZ2UsIGNvbmZpcm1UaXRsZSk7XG4gICAgICB9IGVsc2UgaWYgKGNvbmZpcm0oY29uZmlybU1lc3NhZ2UpKSB7XG4gICAgICAgIHRoaXMucG9zdEZvcm0oJHN1Ym1pdEJ0biwgZ3JpZCk7XG4gICAgICB9XG4gICAgfSBlbHNlIHtcbiAgICAgIHRoaXMucG9zdEZvcm0oJHN1Ym1pdEJ0biwgZ3JpZCk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkc3VibWl0QnRuXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKiBAcGFyYW0ge3N0cmluZ30gY29uZmlybU1lc3NhZ2VcbiAgICogQHBhcmFtIHtzdHJpbmd9IGNvbmZpcm1UaXRsZVxuICAgKi9cbiAgc2hvd0NvbmZpcm1Nb2RhbCgkc3VibWl0QnRuLCBncmlkLCBjb25maXJtTWVzc2FnZSwgY29uZmlybVRpdGxlKSB7XG4gICAgY29uc3QgY29uZmlybUJ1dHRvbkxhYmVsID0gJHN1Ym1pdEJ0bi5kYXRhKCdjb25maXJtQnV0dG9uTGFiZWwnKTtcbiAgICBjb25zdCBjbG9zZUJ1dHRvbkxhYmVsID0gJHN1Ym1pdEJ0bi5kYXRhKCdjbG9zZUJ1dHRvbkxhYmVsJyk7XG4gICAgY29uc3QgY29uZmlybUJ1dHRvbkNsYXNzID0gJHN1Ym1pdEJ0bi5kYXRhKCdjb25maXJtQnV0dG9uQ2xhc3MnKTtcblxuICAgIGNvbnN0IG1vZGFsID0gbmV3IENvbmZpcm1Nb2RhbCh7XG4gICAgICBpZDogYCR7Z3JpZC5nZXRJZCgpfV9ncmlkX2NvbmZpcm1fbW9kYWxgLFxuICAgICAgY29uZmlybVRpdGxlLFxuICAgICAgY29uZmlybU1lc3NhZ2UsXG4gICAgICBjb25maXJtQnV0dG9uTGFiZWwsXG4gICAgICBjbG9zZUJ1dHRvbkxhYmVsLFxuICAgICAgY29uZmlybUJ1dHRvbkNsYXNzLFxuICAgIH0sICgpID0+IHRoaXMucG9zdEZvcm0oJHN1Ym1pdEJ0biwgZ3JpZCkpO1xuXG4gICAgbW9kYWwuc2hvdygpO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkc3VibWl0QnRuXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgcG9zdEZvcm0oJHN1Ym1pdEJ0biwgZ3JpZCkge1xuICAgIGNvbnN0ICRmb3JtID0gJCgnIycgKyBncmlkLmdldElkKCkgKyAnX2ZpbHRlcl9mb3JtJyk7XG5cbiAgICAkZm9ybS5hdHRyKCdhY3Rpb24nLCAkc3VibWl0QnRuLmRhdGEoJ2Zvcm0tdXJsJykpO1xuICAgICRmb3JtLmF0dHIoJ21ldGhvZCcsICRzdWJtaXRCdG4uZGF0YSgnZm9ybS1tZXRob2QnKSk7XG4gICAgJGZvcm0uc3VibWl0KCk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vc3VibWl0LWJ1bGstYWN0aW9uLWV4dGVuc2lvbi5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIExpbmtSb3dBY3Rpb25FeHRlbnNpb24gaGFuZGxlcyBsaW5rIHJvdyBhY3Rpb25zXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIExpbmtSb3dBY3Rpb25FeHRlbnNpb24ge1xuICAvKipcbiAgICogRXh0ZW5kIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIHRoaXMuaW5pdFJvd0xpbmtzKGdyaWQpO1xuICAgIHRoaXMuaW5pdENvbmZpcm1hYmxlQWN0aW9ucyhncmlkKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGluaXRDb25maXJtYWJsZUFjdGlvbnMoZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkub24oJ2NsaWNrJywgJy5qcy1saW5rLXJvdy1hY3Rpb24nLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0IGNvbmZpcm1NZXNzYWdlID0gJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdjb25maXJtLW1lc3NhZ2UnKTtcblxuICAgICAgaWYgKGNvbmZpcm1NZXNzYWdlLmxlbmd0aCAmJiAhY29uZmlybShjb25maXJtTWVzc2FnZSkpIHtcbiAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgIH1cbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBBZGQgYSBjbGljayBldmVudCBvbiByb3dzIHRoYXQgbWF0Y2hlcyB0aGUgZmlyc3QgbGluayBhY3Rpb24gKGlmIHByZXNlbnQpXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgaW5pdFJvd0xpbmtzKGdyaWQpIHtcbiAgICAkKCd0cicsIGdyaWQuZ2V0Q29udGFpbmVyKCkpLmVhY2goZnVuY3Rpb24gaW5pdEVhY2hSb3coKSB7XG4gICAgICBjb25zdCAkcGFyZW50Um93ID0gJCh0aGlzKTtcblxuICAgICAgJCgnLmpzLWxpbmstcm93LWFjdGlvbltkYXRhLWNsaWNrYWJsZS1yb3c9MV06Zmlyc3QnLCAkcGFyZW50Um93KS5lYWNoKGZ1bmN0aW9uIHByb3BhZ2F0ZUZpcnN0TGlua0FjdGlvbigpIHtcbiAgICAgICAgY29uc3QgJHJvd0FjdGlvbiA9ICQodGhpcyk7XG4gICAgICAgIGNvbnN0ICRwYXJlbnRDZWxsID0gJHJvd0FjdGlvbi5jbG9zZXN0KCd0ZCcpO1xuICAgICAgICBcbiAgICAgICAgY29uc3QgY2xpY2thYmxlQ2VsbHMgPSAkKCd0ZC5jbGlja2FibGUnLCAkcGFyZW50Um93KVxuICAgICAgICAgIC5ub3QoJHBhcmVudENlbGwpXG4gICAgICAgIDtcblxuICAgICAgICBjbGlja2FibGVDZWxscy5hZGRDbGFzcygnY3Vyc29yLXBvaW50ZXInKS5jbGljaygoKSA9PiB7XG4gICAgICAgICAgY29uc3QgY29uZmlybU1lc3NhZ2UgPSAkcm93QWN0aW9uLmRhdGEoJ2NvbmZpcm0tbWVzc2FnZScpO1xuXG4gICAgICAgICAgaWYgKCFjb25maXJtTWVzc2FnZS5sZW5ndGggfHwgY29uZmlybShjb25maXJtTWVzc2FnZSkpIHtcbiAgICAgICAgICAgIGRvY3VtZW50LmxvY2F0aW9uID0gJHJvd0FjdGlvbi5hdHRyKCdocmVmJyk7XG4gICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICAgIH0pO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2xpbmstcm93LWFjdGlvbi1leHRlbnNpb24uanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBIYW5kbGVzIGJ1bGsgZGVsZXRlIGZvciBcIkN1c3RvbWVyc1wiIGdyaWQuXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIERlbGV0ZUN1c3RvbWVyc0J1bGtBY3Rpb25FeHRlbnNpb24ge1xuXG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHJldHVybiB7XG4gICAgICBleHRlbmQ6IChncmlkKSA9PiB0aGlzLmV4dGVuZChncmlkKSxcbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtZGVsZXRlLWN1c3RvbWVycy1idWxrLWFjdGlvbicsIChldmVudCkgPT4ge1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgY29uc3Qgc3VibWl0VXJsID0gJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdjdXN0b21lcnMtZGVsZXRlLXVybCcpO1xuXG4gICAgICBjb25zdCAkbW9kYWwgPSAkKGAjJHtncmlkLmdldElkKCl9X2dyaWRfZGVsZXRlX2N1c3RvbWVyc19tb2RhbGApO1xuICAgICAgJG1vZGFsLm1vZGFsKCdzaG93Jyk7XG5cbiAgICAgICRtb2RhbC5vbignY2xpY2snLCAnLmpzLXN1Ym1pdC1kZWxldGUtY3VzdG9tZXJzJywgKCkgPT4ge1xuICAgICAgICBjb25zdCAkc2VsZWN0ZWRDdXN0b21lckNoZWNrYm94ZXMgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5qcy1idWxrLWFjdGlvbi1jaGVja2JveDpjaGVja2VkJyk7XG5cbiAgICAgICAgJHNlbGVjdGVkQ3VzdG9tZXJDaGVja2JveGVzLmVhY2goKGksIGNoZWNrYm94KSA9PiB7XG4gICAgICAgICAgY29uc3QgJGlucHV0ID0gJChjaGVja2JveCk7XG5cbiAgICAgICAgICB0aGlzLl9hZGRDdXN0b21lclRvRGVsZXRlQ29sbGVjdGlvbklucHV0KCRpbnB1dC52YWwoKSk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgIGNvbnN0ICRmb3JtID0gJG1vZGFsLmZpbmQoJ2Zvcm0nKTtcblxuICAgICAgICAkZm9ybS5hdHRyKCdhY3Rpb24nLCBzdWJtaXRVcmwpO1xuICAgICAgICAkZm9ybS5zdWJtaXQoKTtcbiAgICAgIH0pO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIENyZWF0ZSBpbnB1dCB3aXRoIGN1c3RvbWVyIGlkIGFuZCBhZGQgaXQgdG8gZGVsZXRlIGNvbGxlY3Rpb24gaW5wdXRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9hZGRDdXN0b21lclRvRGVsZXRlQ29sbGVjdGlvbklucHV0KGN1c3RvbWVySWQpIHtcbiAgICBjb25zdCAkY3VzdG9tZXJzSW5wdXQgPSAkKCcjZGVsZXRlX2N1c3RvbWVyc19jdXN0b21lcnNfdG9fZGVsZXRlJyk7XG5cbiAgICBjb25zdCBjdXN0b21lcklucHV0ID0gJGN1c3RvbWVyc0lucHV0XG4gICAgICAuZGF0YSgncHJvdG90eXBlJylcbiAgICAgIC5yZXBsYWNlKC9fX25hbWVfXy9nLCBjdXN0b21lcklkKVxuICAgIDtcblxuICAgIGNvbnN0ICRpdGVtID0gJCgkLnBhcnNlSFRNTChjdXN0b21lcklucHV0KVswXSk7XG4gICAgJGl0ZW0udmFsKGN1c3RvbWVySWQpO1xuXG4gICAgJGN1c3RvbWVyc0lucHV0LmFwcGVuZCgkaXRlbSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYWN0aW9uL2J1bGsvY3VzdG9tZXIvZGVsZXRlLWN1c3RvbWVycy1idWxrLWFjdGlvbi1leHRlbnNpb24uanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBEZWxldGVDdXN0b21lclJvd0FjdGlvbkV4dGVuc2lvbiBoYW5kbGVzIHN1Ym1pdHRpbmcgb2Ygcm93IGFjdGlvblxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBEZWxldGVDdXN0b21lclJvd0FjdGlvbkV4dGVuc2lvbiB7XG5cbiAgY29uc3RydWN0b3IoKSB7XG4gICAgcmV0dXJuIHtcbiAgICAgIGV4dGVuZDogKGdyaWQpID0+IHRoaXMuZXh0ZW5kKGdyaWQpLFxuICAgIH07XG4gIH1cblxuICAvKipcbiAgICogRXh0ZW5kIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkub24oJ2NsaWNrJywgJy5qcy1kZWxldGUtY3VzdG9tZXItcm93LWFjdGlvbicsIChldmVudCkgPT4ge1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgY29uc3QgJGRlbGV0ZUN1c3RvbWVyc01vZGFsID0gJChgIyR7Z3JpZC5nZXRJZCgpfV9ncmlkX2RlbGV0ZV9jdXN0b21lcnNfbW9kYWxgKTtcbiAgICAgICRkZWxldGVDdXN0b21lcnNNb2RhbC5tb2RhbCgnc2hvdycpO1xuXG4gICAgICAkZGVsZXRlQ3VzdG9tZXJzTW9kYWwub24oJ2NsaWNrJywgJy5qcy1zdWJtaXQtZGVsZXRlLWN1c3RvbWVycycsICgpID0+IHtcbiAgICAgICAgY29uc3QgJGJ1dHRvbiA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgICAgIGNvbnN0IGN1c3RvbWVySWQgPSAkYnV0dG9uLmRhdGEoJ2N1c3RvbWVyLWlkJyk7XG5cbiAgICAgICAgdGhpcy5fYWRkQ3VzdG9tZXJJbnB1dChjdXN0b21lcklkKTtcblxuICAgICAgICBjb25zdCAkZm9ybSA9ICRkZWxldGVDdXN0b21lcnNNb2RhbC5maW5kKCdmb3JtJyk7XG5cbiAgICAgICAgJGZvcm0uYXR0cignYWN0aW9uJywgJGJ1dHRvbi5kYXRhKCdjdXN0b21lci1kZWxldGUtdXJsJykpO1xuICAgICAgICAkZm9ybS5zdWJtaXQoKTtcbiAgICAgIH0pO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEFkZHMgaW5wdXQgZm9yIHNlbGVjdGVkIGN1c3RvbWVyIHRvIGRlbGV0ZSBmb3JtXG4gICAqXG4gICAqIEBwYXJhbSB7aW50ZWdlcn0gY3VzdG9tZXJJZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2FkZEN1c3RvbWVySW5wdXQoY3VzdG9tZXJJZCkge1xuICAgIGNvbnN0ICRjdXN0b21lcnNUb0RlbGV0ZUlucHV0QmxvY2sgPSAkKCcjZGVsZXRlX2N1c3RvbWVyc19jdXN0b21lcnNfdG9fZGVsZXRlJyk7XG5cbiAgICBjb25zdCBjdXN0b21lcklucHV0ID0gJGN1c3RvbWVyc1RvRGVsZXRlSW5wdXRCbG9ja1xuICAgICAgLmRhdGEoJ3Byb3RvdHlwZScpXG4gICAgICAucmVwbGFjZSgvX19uYW1lX18vZywgJGN1c3RvbWVyc1RvRGVsZXRlSW5wdXRCbG9jay5jaGlsZHJlbigpLmxlbmd0aCk7XG5cbiAgICBjb25zdCAkaXRlbSA9ICQoJC5wYXJzZUhUTUwoY3VzdG9tZXJJbnB1dClbMF0pO1xuICAgICRpdGVtLnZhbChjdXN0b21lcklkKTtcblxuICAgICRjdXN0b21lcnNUb0RlbGV0ZUlucHV0QmxvY2suYXBwZW5kKCRpdGVtKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9hY3Rpb24vcm93L2N1c3RvbWVyL2RlbGV0ZS1jdXN0b21lci1yb3ctYWN0aW9uLWV4dGVuc2lvbi5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIFRha2VzIGxpbmsgZnJvbSBjbGlja2VkIGl0ZW0gYW5kIHJlZGlyZWN0cyB0byBpdC5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgTGlua2FibGVJdGVtIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgJy5qcy1saW5rYWJsZS1pdGVtJywgKGV2ZW50KSA9PiB7XG4gICAgICB3aW5kb3cubG9jYXRpb24gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmRhdGEoJ2xpbmthYmxlLWhyZWYnKTtcbiAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9saW5rYWJsZS1pdGVtLmpzIiwibW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCl7XG4gIHJldHVybiB0eXBlb2YgaXQgPT09ICdvYmplY3QnID8gaXQgIT09IG51bGwgOiB0eXBlb2YgaXQgPT09ICdmdW5jdGlvbic7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faXMtb2JqZWN0LmpzXG4vLyBtb2R1bGUgaWQgPSA0XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuY29uc3QgJCA9IGdsb2JhbC4kO1xuXG4vKipcbiAqIE1ha2VzIGEgdGFibGUgc29ydGFibGUgYnkgY29sdW1ucy5cbiAqIFRoaXMgZm9yY2VzIGEgcGFnZSByZWxvYWQgd2l0aCBtb3JlIHF1ZXJ5IHBhcmFtZXRlcnMuXG4gKi9cbmNsYXNzIFRhYmxlU29ydGluZyB7XG5cbiAgLyoqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSB0YWJsZVxuICAgKi9cbiAgY29uc3RydWN0b3IodGFibGUpIHtcbiAgICB0aGlzLnNlbGVjdG9yID0gJy5wcy1zb3J0YWJsZS1jb2x1bW4nO1xuICAgIHRoaXMuY29sdW1ucyA9ICQodGFibGUpLmZpbmQodGhpcy5zZWxlY3Rvcik7XG4gIH1cblxuICAvKipcbiAgICogQXR0YWNoZXMgdGhlIGxpc3RlbmVyc1xuICAgKi9cbiAgYXR0YWNoKCkge1xuICAgIHRoaXMuY29sdW1ucy5vbignY2xpY2snLCAoZSkgPT4ge1xuICAgICAgY29uc3QgJGNvbHVtbiA9ICQoZS5kZWxlZ2F0ZVRhcmdldCk7XG4gICAgICB0aGlzLl9zb3J0QnlDb2x1bW4oJGNvbHVtbiwgdGhpcy5fZ2V0VG9nZ2xlZFNvcnREaXJlY3Rpb24oJGNvbHVtbikpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIFNvcnQgdXNpbmcgYSBjb2x1bW4gbmFtZVxuICAgKiBAcGFyYW0ge3N0cmluZ30gY29sdW1uTmFtZVxuICAgKiBAcGFyYW0ge3N0cmluZ30gZGlyZWN0aW9uIFwiYXNjXCIgb3IgXCJkZXNjXCJcbiAgICovXG4gIHNvcnRCeShjb2x1bW5OYW1lLCBkaXJlY3Rpb24pIHtcbiAgICBjb25zdCAkY29sdW1uID0gdGhpcy5jb2x1bW5zLmlzKGBbZGF0YS1zb3J0LWNvbC1uYW1lPVwiJHtjb2x1bW5OYW1lfVwiXWApO1xuICAgIGlmICghJGNvbHVtbikge1xuICAgICAgdGhyb3cgbmV3IEVycm9yKGBDYW5ub3Qgc29ydCBieSBcIiR7Y29sdW1uTmFtZX1cIjogaW52YWxpZCBjb2x1bW5gKTtcbiAgICB9XG5cbiAgICB0aGlzLl9zb3J0QnlDb2x1bW4oJGNvbHVtbiwgZGlyZWN0aW9uKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTb3J0IHVzaW5nIGEgY29sdW1uIGVsZW1lbnRcbiAgICogQHBhcmFtIHtqUXVlcnl9IGNvbHVtblxuICAgKiBAcGFyYW0ge3N0cmluZ30gZGlyZWN0aW9uIFwiYXNjXCIgb3IgXCJkZXNjXCJcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zb3J0QnlDb2x1bW4oY29sdW1uLCBkaXJlY3Rpb24pIHtcbiAgICB3aW5kb3cubG9jYXRpb24gPSB0aGlzLl9nZXRVcmwoY29sdW1uLmRhdGEoJ3NvcnRDb2xOYW1lJyksIChkaXJlY3Rpb24gPT09ICdkZXNjJykgPyAnZGVzYycgOiAnYXNjJywgY29sdW1uLmRhdGEoJ3NvcnRQcmVmaXgnKSk7XG4gIH1cblxuICAvKipcbiAgICogUmV0dXJucyB0aGUgaW52ZXJ0ZWQgZGlyZWN0aW9uIHRvIHNvcnQgYWNjb3JkaW5nIHRvIHRoZSBjb2x1bW4ncyBjdXJyZW50IG9uZVxuICAgKiBAcGFyYW0ge2pRdWVyeX0gY29sdW1uXG4gICAqIEByZXR1cm4ge3N0cmluZ31cbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9nZXRUb2dnbGVkU29ydERpcmVjdGlvbihjb2x1bW4pIHtcbiAgICByZXR1cm4gY29sdW1uLmRhdGEoJ3NvcnREaXJlY3Rpb24nKSA9PT0gJ2FzYycgPyAnZGVzYycgOiAnYXNjJztcbiAgfVxuXG4gIC8qKlxuICAgKiBSZXR1cm5zIHRoZSB1cmwgZm9yIHRoZSBzb3J0ZWQgdGFibGVcbiAgICogQHBhcmFtIHtzdHJpbmd9IGNvbE5hbWVcbiAgICogQHBhcmFtIHtzdHJpbmd9IGRpcmVjdGlvblxuICAgKiBAcGFyYW0ge3N0cmluZ30gcHJlZml4XG4gICAqIEByZXR1cm4ge3N0cmluZ31cbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9nZXRVcmwoY29sTmFtZSwgZGlyZWN0aW9uLCBwcmVmaXgpIHtcbiAgICBjb25zdCB1cmwgPSBuZXcgVVJMKHdpbmRvdy5sb2NhdGlvbi5ocmVmKTtcbiAgICBjb25zdCBwYXJhbXMgPSB1cmwuc2VhcmNoUGFyYW1zO1xuXG4gICAgaWYgKHByZWZpeCkge1xuICAgICAgcGFyYW1zLnNldChwcmVmaXgrJ1tvcmRlckJ5XScsIGNvbE5hbWUpO1xuICAgICAgcGFyYW1zLnNldChwcmVmaXgrJ1tzb3J0T3JkZXJdJywgZGlyZWN0aW9uKTtcbiAgICB9IGVsc2Uge1xuICAgICAgcGFyYW1zLnNldCgnb3JkZXJCeScsIGNvbE5hbWUpO1xuICAgICAgcGFyYW1zLnNldCgnc29ydE9yZGVyJywgZGlyZWN0aW9uKTtcbiAgICB9XG5cbiAgICByZXR1cm4gdXJsLnRvU3RyaW5nKCk7XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgVGFibGVTb3J0aW5nO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvYXBwL3V0aWxzL3RhYmxlLXNvcnRpbmcuanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbi8qKlxuICogU2VuZCBhIFBvc3QgUmVxdWVzdCB0byByZXNldCBzZWFyY2ggQWN0aW9uLlxuICovXG5cbmNvbnN0ICQgPSBnbG9iYWwuJDtcblxuY29uc3QgaW5pdCA9IGZ1bmN0aW9uIHJlc2V0U2VhcmNoKHVybCwgcmVkaXJlY3RVcmwpIHtcbiAgICAkLnBvc3QodXJsKS50aGVuKCgpID0+IHdpbmRvdy5sb2NhdGlvbi5hc3NpZ24ocmVkaXJlY3RVcmwpKTtcbn07XG5cbmV4cG9ydCBkZWZhdWx0IGluaXQ7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9hcHAvdXRpbHMvcmVzZXRfc2VhcmNoLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ29uZmlybU1vZGFsIGNvbXBvbmVudFxuICpcbiAqIEBwYXJhbSB7U3RyaW5nfSBpZFxuICogQHBhcmFtIHtTdHJpbmd9IGNvbmZpcm1UaXRsZVxuICogQHBhcmFtIHtTdHJpbmd9IGNvbmZpcm1NZXNzYWdlXG4gKiBAcGFyYW0ge1N0cmluZ30gY2xvc2VCdXR0b25MYWJlbFxuICogQHBhcmFtIHtTdHJpbmd9IGNvbmZpcm1CdXR0b25MYWJlbFxuICogQHBhcmFtIHtTdHJpbmd9IGNvbmZpcm1CdXR0b25DbGFzc1xuICogQHBhcmFtIHtCb29sZWFufSBjbG9zYWJsZVxuICogQHBhcmFtIHtGdW5jdGlvbn0gY29uZmlybUNhbGxiYWNrXG4gKlxuICovXG5leHBvcnQgZGVmYXVsdCBmdW5jdGlvbiBDb25maXJtTW9kYWwocGFyYW1zLCBjb25maXJtQ2FsbGJhY2spIHtcbiAgLy8gQ29uc3RydWN0IHRoZSBtb2RhbFxuICBjb25zdCB7aWQsIGNsb3NhYmxlfSA9IHBhcmFtcztcbiAgdGhpcy5tb2RhbCA9IE1vZGFsKHBhcmFtcyk7XG5cbiAgLy8galF1ZXJ5IG1vZGFsIG9iamVjdFxuICB0aGlzLiRtb2RhbCA9ICQodGhpcy5tb2RhbC5jb250YWluZXIpO1xuXG4gIHRoaXMuc2hvdyA9ICgpID0+IHtcbiAgICB0aGlzLiRtb2RhbC5tb2RhbCgpO1xuICB9O1xuXG4gIHRoaXMubW9kYWwuY29uZmlybUJ1dHRvbi5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIGNvbmZpcm1DYWxsYmFjayk7XG5cbiAgdGhpcy4kbW9kYWwubW9kYWwoe1xuICAgIGJhY2tkcm9wOiAoY2xvc2FibGUgPyB0cnVlIDogJ3N0YXRpYycpLFxuICAgIGtleWJvYXJkOiBjbG9zYWJsZSAhPT0gdW5kZWZpbmVkID8gY2xvc2FibGUgOiB0cnVlLFxuICAgIGNsb3NhYmxlOiBjbG9zYWJsZSAhPT0gdW5kZWZpbmVkID8gY2xvc2FibGUgOiB0cnVlLFxuICAgIHNob3c6IGZhbHNlLFxuICB9KTtcblxuICB0aGlzLiRtb2RhbC5vbignaGlkZGVuLmJzLm1vZGFsJywgKCkgPT4ge1xuICAgIGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoYCMke2lkfWApLnJlbW92ZSgpO1xuICB9KTtcblxuICBkb2N1bWVudC5ib2R5LmFwcGVuZENoaWxkKHRoaXMubW9kYWwuY29udGFpbmVyKTtcbn1cblxuLyoqXG4gKiBNb2RhbCBjb21wb25lbnQgdG8gaW1wcm92ZSBsaXNpYmlsaXR5IGJ5IGNvbnN0cnVjdGluZyB0aGUgbW9kYWwgb3V0c2lkZSB0aGUgbWFpbiBmdW5jdGlvblxuICpcbiAqIEBwYXJhbSB7T2JqZWN0fSBwYXJhbXNcbiAqXG4gKi9cbmZ1bmN0aW9uIE1vZGFsKFxuICB7XG4gICAgaWQgPSAnY29uZmlybV9tb2RhbCcsXG4gICAgY29uZmlybVRpdGxlLFxuICAgIGNvbmZpcm1NZXNzYWdlID0gJycsXG4gICAgY2xvc2VCdXR0b25MYWJlbCA9ICdDbG9zZScsXG4gICAgY29uZmlybUJ1dHRvbkxhYmVsID0gJ0FjY2VwdCcsXG4gICAgY29uZmlybUJ1dHRvbkNsYXNzID0gJ2J0bi1wcmltYXJ5JyxcbiAgfSkge1xuICBjb25zdCBtb2RhbCA9IHt9O1xuXG4gIC8vIE1haW4gbW9kYWwgZWxlbWVudFxuICBtb2RhbC5jb250YWluZXIgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdkaXYnKTtcbiAgbW9kYWwuY29udGFpbmVyLmNsYXNzTGlzdC5hZGQoJ21vZGFsJywgJ2ZhZGUnKTtcbiAgbW9kYWwuY29udGFpbmVyLmlkID0gaWQ7XG5cbiAgLy8gTW9kYWwgZGlhbG9nIGVsZW1lbnRcbiAgbW9kYWwuZGlhbG9nID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2Jyk7XG4gIG1vZGFsLmRpYWxvZy5jbGFzc0xpc3QuYWRkKCdtb2RhbC1kaWFsb2cnKTtcblxuICAvLyBNb2RhbCBjb250ZW50IGVsZW1lbnRcbiAgbW9kYWwuY29udGVudCA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2RpdicpO1xuICBtb2RhbC5jb250ZW50LmNsYXNzTGlzdC5hZGQoJ21vZGFsLWNvbnRlbnQnKTtcblxuICAvLyBNb2RhbCBoZWFkZXIgZWxlbWVudFxuICBtb2RhbC5oZWFkZXIgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdkaXYnKTtcbiAgbW9kYWwuaGVhZGVyLmNsYXNzTGlzdC5hZGQoJ21vZGFsLWhlYWRlcicpO1xuXG4gIC8vIE1vZGFsIHRpdGxlIGVsZW1lbnRcbiAgaWYgKGNvbmZpcm1UaXRsZSkge1xuICAgIG1vZGFsLnRpdGxlID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnaDQnKTtcbiAgICBtb2RhbC50aXRsZS5jbGFzc0xpc3QuYWRkKCdtb2RhbC10aXRsZScpO1xuICAgIG1vZGFsLnRpdGxlLmlubmVySFRNTCA9IGNvbmZpcm1UaXRsZTtcbiAgfVxuXG4gIC8vIE1vZGFsIGNsb3NlIGJ1dHRvbiBpY29uXG4gIG1vZGFsLmNsb3NlSWNvbiA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2J1dHRvbicpO1xuICBtb2RhbC5jbG9zZUljb24uY2xhc3NMaXN0LmFkZCgnY2xvc2UnKTtcbiAgbW9kYWwuY2xvc2VJY29uLnNldEF0dHJpYnV0ZSgndHlwZScsICdidXR0b24nKTtcbiAgbW9kYWwuY2xvc2VJY29uLmRhdGFzZXQuZGlzbWlzcyA9ICdtb2RhbCc7XG4gIG1vZGFsLmNsb3NlSWNvbi5pbm5lckhUTUwgPSAnw5cnO1xuXG4gIC8vIE1vZGFsIGJvZHkgZWxlbWVudFxuICBtb2RhbC5ib2R5ID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2Jyk7XG4gIG1vZGFsLmJvZHkuY2xhc3NMaXN0LmFkZCgnbW9kYWwtYm9keScsICd0ZXh0LWxlZnQnLCAnZm9udC13ZWlnaHQtbm9ybWFsJyk7XG5cbiAgLy8gTW9kYWwgbWVzc2FnZSBlbGVtZW50XG4gIG1vZGFsLm1lc3NhZ2UgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdwJyk7XG4gIG1vZGFsLm1lc3NhZ2UuY2xhc3NMaXN0LmFkZCgnY29uZmlybS1tZXNzYWdlJyk7XG4gIG1vZGFsLm1lc3NhZ2UuaW5uZXJIVE1MID0gY29uZmlybU1lc3NhZ2U7XG5cbiAgLy8gTW9kYWwgZm9vdGVyIGVsZW1lbnRcbiAgbW9kYWwuZm9vdGVyID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2Jyk7XG4gIG1vZGFsLmZvb3Rlci5jbGFzc0xpc3QuYWRkKCdtb2RhbC1mb290ZXInKTtcblxuICAvLyBNb2RhbCBjbG9zZSBidXR0b24gZWxlbWVudFxuICBtb2RhbC5jbG9zZUJ1dHRvbiA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2J1dHRvbicpO1xuICBtb2RhbC5jbG9zZUJ1dHRvbi5zZXRBdHRyaWJ1dGUoJ3R5cGUnLCAnYnV0dG9uJyk7XG4gIG1vZGFsLmNsb3NlQnV0dG9uLmNsYXNzTGlzdC5hZGQoJ2J0bicsICdidG4tb3V0bGluZS1zZWNvbmRhcnknLCAnYnRuLWxnJyk7XG4gIG1vZGFsLmNsb3NlQnV0dG9uLmRhdGFzZXQuZGlzbWlzcyA9ICdtb2RhbCc7XG4gIG1vZGFsLmNsb3NlQnV0dG9uLmlubmVySFRNTCA9IGNsb3NlQnV0dG9uTGFiZWw7XG5cbiAgLy8gTW9kYWwgY2xvc2UgYnV0dG9uIGVsZW1lbnRcbiAgbW9kYWwuY29uZmlybUJ1dHRvbiA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2J1dHRvbicpO1xuICBtb2RhbC5jb25maXJtQnV0dG9uLnNldEF0dHJpYnV0ZSgndHlwZScsICdidXR0b24nKTtcbiAgbW9kYWwuY29uZmlybUJ1dHRvbi5jbGFzc0xpc3QuYWRkKCdidG4nLCBjb25maXJtQnV0dG9uQ2xhc3MsICdidG4tbGcnLCAnYnRuLWNvbmZpcm0tc3VibWl0Jyk7XG4gIG1vZGFsLmNvbmZpcm1CdXR0b24uZGF0YXNldC5kaXNtaXNzID0gJ21vZGFsJztcbiAgbW9kYWwuY29uZmlybUJ1dHRvbi5pbm5lckhUTUwgPSBjb25maXJtQnV0dG9uTGFiZWw7XG5cbiAgLy8gQ29uc3RydWN0aW5nIHRoZSBtb2RhbFxuICBpZiAoY29uZmlybVRpdGxlKSB7XG4gICAgbW9kYWwuaGVhZGVyLmFwcGVuZChtb2RhbC50aXRsZSwgbW9kYWwuY2xvc2VJY29uKTtcbiAgfSBlbHNlIHtcbiAgICBtb2RhbC5oZWFkZXIuYXBwZW5kQ2hpbGQobW9kYWwuY2xvc2VJY29uKTtcbiAgfVxuXG4gIG1vZGFsLmJvZHkuYXBwZW5kQ2hpbGQobW9kYWwubWVzc2FnZSk7XG4gIG1vZGFsLmZvb3Rlci5hcHBlbmQobW9kYWwuY2xvc2VCdXR0b24sIG1vZGFsLmNvbmZpcm1CdXR0b24pO1xuICBtb2RhbC5jb250ZW50LmFwcGVuZChtb2RhbC5oZWFkZXIsIG1vZGFsLmJvZHksIG1vZGFsLmZvb3Rlcik7XG4gIG1vZGFsLmRpYWxvZy5hcHBlbmRDaGlsZChtb2RhbC5jb250ZW50KTtcbiAgbW9kYWwuY29udGFpbmVyLmFwcGVuZENoaWxkKG1vZGFsLmRpYWxvZyk7XG5cbiAgcmV0dXJuIG1vZGFsO1xufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9tb2RhbC5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuaW1wb3J0IEdyaWQgZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9ncmlkJztcbmltcG9ydCBSZWxvYWRMaXN0QWN0aW9uRXh0ZW5zaW9uIGZyb20gJ0Bjb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3JlbG9hZC1saXN0LWV4dGVuc2lvbic7XG5pbXBvcnQgRXhwb3J0VG9TcWxNYW5hZ2VyRXh0ZW5zaW9uIGZyb20gJ0Bjb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2V4cG9ydC10by1zcWwtbWFuYWdlci1leHRlbnNpb24nO1xuaW1wb3J0IEZpbHRlcnNSZXNldEV4dGVuc2lvbiBmcm9tICdAY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXJlc2V0LWV4dGVuc2lvbic7XG5pbXBvcnQgRm9ybVN1Ym1pdEJ1dHRvbiBmcm9tICdAY29tcG9uZW50cy9mb3JtLXN1Ym1pdC1idXR0b24nO1xuaW1wb3J0IFNvcnRpbmdFeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vc29ydGluZy1leHRlbnNpb24nO1xuaW1wb3J0IEJ1bGtBY3Rpb25DaGVja2JveEV4dGVuc2lvbiBmcm9tICdAY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9idWxrLWFjdGlvbi1jaGVja2JveC1leHRlbnNpb24nO1xuaW1wb3J0IFN1Ym1pdEJ1bGtFeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vc3VibWl0LWJ1bGstYWN0aW9uLWV4dGVuc2lvbic7XG5pbXBvcnQgU3VibWl0R3JpZEV4dGVuc2lvbiBmcm9tICdAY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9zdWJtaXQtZ3JpZC1hY3Rpb24tZXh0ZW5zaW9uJztcbmltcG9ydCBMaW5rUm93QWN0aW9uRXh0ZW5zaW9uIGZyb20gJ0Bjb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2xpbmstcm93LWFjdGlvbi1leHRlbnNpb24nO1xuaW1wb3J0IExpbmthYmxlSXRlbSBmcm9tICdAY29tcG9uZW50cy9saW5rYWJsZS1pdGVtJztcbmltcG9ydCBDaG9pY2VUYWJsZSBmcm9tICdAY29tcG9uZW50cy9jaG9pY2UtdGFibGUnO1xuaW1wb3J0IENvbHVtblRvZ2dsaW5nRXh0ZW5zaW9uIGZyb20gJ0Bjb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2NvbHVtbi10b2dnbGluZy1leHRlbnNpb24nO1xuaW1wb3J0IERlbGV0ZUN1c3RvbWVyc0J1bGtBY3Rpb25FeHRlbnNpb25cbiAgZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYWN0aW9uL2J1bGsvY3VzdG9tZXIvZGVsZXRlLWN1c3RvbWVycy1idWxrLWFjdGlvbi1leHRlbnNpb24nO1xuaW1wb3J0IERlbGV0ZUN1c3RvbWVyUm93QWN0aW9uRXh0ZW5zaW9uXG4gIGZyb20gXCJAY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9hY3Rpb24vcm93L2N1c3RvbWVyL2RlbGV0ZS1jdXN0b21lci1yb3ctYWN0aW9uLWV4dGVuc2lvblwiO1xuaW1wb3J0IEZpbHRlcnNTdWJtaXRCdXR0b25FbmFibGVyRXh0ZW5zaW9uXG4gIGZyb20gJ0Bjb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2ZpbHRlcnMtc3VibWl0LWJ1dHRvbi1lbmFibGVyLWV4dGVuc2lvbic7XG5pbXBvcnQgU2hvd2Nhc2VDYXJkIGZyb20gJ0Bjb21wb25lbnRzL3Nob3djYXNlLWNhcmQvc2hvd2Nhc2UtY2FyZCc7XG5pbXBvcnQgU2hvd2Nhc2VDYXJkQ2xvc2VFeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvc2hvd2Nhc2UtY2FyZC9leHRlbnNpb24vc2hvd2Nhc2UtY2FyZC1jbG9zZS1leHRlbnNpb24nO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbiQoKCkgPT4ge1xuICBjb25zdCBjdXN0b21lckdyaWQgPSBuZXcgR3JpZCgnY3VzdG9tZXInKTtcblxuICBjdXN0b21lckdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBSZWxvYWRMaXN0QWN0aW9uRXh0ZW5zaW9uKCkpO1xuICBjdXN0b21lckdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBFeHBvcnRUb1NxbE1hbmFnZXJFeHRlbnNpb24oKSk7XG4gIGN1c3RvbWVyR3JpZC5hZGRFeHRlbnNpb24obmV3IEZpbHRlcnNSZXNldEV4dGVuc2lvbigpKTtcbiAgY3VzdG9tZXJHcmlkLmFkZEV4dGVuc2lvbihuZXcgU29ydGluZ0V4dGVuc2lvbigpKTtcbiAgY3VzdG9tZXJHcmlkLmFkZEV4dGVuc2lvbihuZXcgQnVsa0FjdGlvbkNoZWNrYm94RXh0ZW5zaW9uKCkpO1xuICBjdXN0b21lckdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBTdWJtaXRCdWxrRXh0ZW5zaW9uKCkpO1xuICBjdXN0b21lckdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBTdWJtaXRHcmlkRXh0ZW5zaW9uKCkpO1xuICBjdXN0b21lckdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBMaW5rUm93QWN0aW9uRXh0ZW5zaW9uKCkpO1xuICBjdXN0b21lckdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBDb2x1bW5Ub2dnbGluZ0V4dGVuc2lvbigpKTtcbiAgY3VzdG9tZXJHcmlkLmFkZEV4dGVuc2lvbihuZXcgRGVsZXRlQ3VzdG9tZXJzQnVsa0FjdGlvbkV4dGVuc2lvbigpKTtcbiAgY3VzdG9tZXJHcmlkLmFkZEV4dGVuc2lvbihuZXcgRGVsZXRlQ3VzdG9tZXJSb3dBY3Rpb25FeHRlbnNpb24oKSk7XG4gIGN1c3RvbWVyR3JpZC5hZGRFeHRlbnNpb24obmV3IEZpbHRlcnNTdWJtaXRCdXR0b25FbmFibGVyRXh0ZW5zaW9uKCkpO1xuXG4gIGNvbnN0IHNob3djYXNlQ2FyZCA9IG5ldyBTaG93Y2FzZUNhcmQoJ2N1c3RvbWVyc1Nob3djYXNlQ2FyZCcpO1xuICBzaG93Y2FzZUNhcmQuYWRkRXh0ZW5zaW9uKG5ldyBTaG93Y2FzZUNhcmRDbG9zZUV4dGVuc2lvbigpKTtcblxuICAvLyBuZWVkZWQgZm9yIFwiR3JvdXAgYWNjZXNzXCIgaW5wdXQgaW4gQWRkL0VkaXQgY3VzdG9tZXIgZm9ybXNcbiAgbmV3IENob2ljZVRhYmxlKCk7XG5cbiAgLy8gaW4gY3VzdG9tZXIgdmlldyBwYWdlXG4gIC8vIHRoZXJlIGFyZSBhIGxvdCBvZiB0YWJsZXNcbiAgLy8gd2hlcmUgeW91IGNsaWNrIGFueSByb3dcbiAgLy8gYW5kIGl0IHJlZGlyZWN0cyB1c2VyIHRvIHJlbGF0ZWQgcGFnZVxuICBuZXcgTGlua2FibGVJdGVtKCk7XG5cbiAgbmV3IEZvcm1TdWJtaXRCdXR0b24oKTtcbn0pO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvY3VzdG9tZXIvaW5kZXguanMiLCIvLyBodHRwczovL2dpdGh1Yi5jb20vemxvaXJvY2svY29yZS1qcy9pc3N1ZXMvODYjaXNzdWVjb21tZW50LTExNTc1OTAyOFxudmFyIGdsb2JhbCA9IG1vZHVsZS5leHBvcnRzID0gdHlwZW9mIHdpbmRvdyAhPSAndW5kZWZpbmVkJyAmJiB3aW5kb3cuTWF0aCA9PSBNYXRoXG4gID8gd2luZG93IDogdHlwZW9mIHNlbGYgIT0gJ3VuZGVmaW5lZCcgJiYgc2VsZi5NYXRoID09IE1hdGggPyBzZWxmIDogRnVuY3Rpb24oJ3JldHVybiB0aGlzJykoKTtcbmlmKHR5cGVvZiBfX2cgPT0gJ251bWJlcicpX19nID0gZ2xvYmFsOyAvLyBlc2xpbnQtZGlzYWJsZS1saW5lIG5vLXVuZGVmXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19nbG9iYWwuanNcbi8vIG1vZHVsZSBpZCA9IDVcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwidmFyIGFuT2JqZWN0ICAgICAgID0gcmVxdWlyZSgnLi9fYW4tb2JqZWN0JylcbiAgLCBJRThfRE9NX0RFRklORSA9IHJlcXVpcmUoJy4vX2llOC1kb20tZGVmaW5lJylcbiAgLCB0b1ByaW1pdGl2ZSAgICA9IHJlcXVpcmUoJy4vX3RvLXByaW1pdGl2ZScpXG4gICwgZFAgICAgICAgICAgICAgPSBPYmplY3QuZGVmaW5lUHJvcGVydHk7XG5cbmV4cG9ydHMuZiA9IHJlcXVpcmUoJy4vX2Rlc2NyaXB0b3JzJykgPyBPYmplY3QuZGVmaW5lUHJvcGVydHkgOiBmdW5jdGlvbiBkZWZpbmVQcm9wZXJ0eShPLCBQLCBBdHRyaWJ1dGVzKXtcbiAgYW5PYmplY3QoTyk7XG4gIFAgPSB0b1ByaW1pdGl2ZShQLCB0cnVlKTtcbiAgYW5PYmplY3QoQXR0cmlidXRlcyk7XG4gIGlmKElFOF9ET01fREVGSU5FKXRyeSB7XG4gICAgcmV0dXJuIGRQKE8sIFAsIEF0dHJpYnV0ZXMpO1xuICB9IGNhdGNoKGUpeyAvKiBlbXB0eSAqLyB9XG4gIGlmKCdnZXQnIGluIEF0dHJpYnV0ZXMgfHwgJ3NldCcgaW4gQXR0cmlidXRlcyl0aHJvdyBUeXBlRXJyb3IoJ0FjY2Vzc29ycyBub3Qgc3VwcG9ydGVkIScpO1xuICBpZigndmFsdWUnIGluIEF0dHJpYnV0ZXMpT1tQXSA9IEF0dHJpYnV0ZXMudmFsdWU7XG4gIHJldHVybiBPO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1kcC5qc1xuLy8gbW9kdWxlIGlkID0gNlxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmNvbnN0ICQgPSBnbG9iYWwuJDtcblxuLyoqXG4gKiBDbGFzcyBSZWxvYWRMaXN0RXh0ZW5zaW9uIGV4dGVuZHMgZ3JpZCB3aXRoIFwiQ29sdW1uIHRvZ2dsaW5nXCIgZmVhdHVyZVxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBDb2x1bW5Ub2dnbGluZ0V4dGVuc2lvbiB7XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBjb25zdCAkdGFibGUgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJ3RhYmxlLnRhYmxlJyk7XG4gICAgJHRhYmxlLmZpbmQoJy5wcy10b2dnbGFibGUtcm93Jykub24oJ2NsaWNrJywgKGUpID0+IHtcbiAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgIHRoaXMuX3RvZ2dsZVZhbHVlKCQoZS5kZWxlZ2F0ZVRhcmdldCkpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSByb3dcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF90b2dnbGVWYWx1ZShyb3cpIHtcbiAgICBjb25zdCB0b2dnbGVVcmwgPSByb3cuZGF0YSgndG9nZ2xlVXJsJyk7XG5cbiAgICB0aGlzLl9zdWJtaXRBc0Zvcm0odG9nZ2xlVXJsKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTdWJtaXRzIHJlcXVlc3QgdXJsIGFzIGZvcm1cbiAgICpcbiAgICogQHBhcmFtIHtzdHJpbmd9IHRvZ2dsZVVybFxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3N1Ym1pdEFzRm9ybSh0b2dnbGVVcmwpIHtcbiAgICBjb25zdCAkZm9ybSA9ICQoJzxmb3JtPicsIHtcbiAgICAgIGFjdGlvbjogdG9nZ2xlVXJsLFxuICAgICAgbWV0aG9kOiAnUE9TVCcsXG4gICAgfSkuYXBwZW5kVG8oJ2JvZHknKTtcblxuICAgICRmb3JtLnN1Ym1pdCgpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2NvbHVtbi10b2dnbGluZy1leHRlbnNpb24uanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBTdWJtaXRHcmlkQWN0aW9uRXh0ZW5zaW9uIGhhbmRsZXMgZ3JpZCBhY3Rpb24gc3VibWl0c1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBTdWJtaXRHcmlkQWN0aW9uRXh0ZW5zaW9uIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgcmV0dXJuIHtcbiAgICAgIGV4dGVuZDogKGdyaWQpID0+IHRoaXMuZXh0ZW5kKGdyaWQpXG4gICAgfTtcbiAgfVxuXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRIZWFkZXJDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLWdyaWQtYWN0aW9uLXN1Ym1pdC1idG4nLCAoZXZlbnQpID0+IHtcbiAgICAgIHRoaXMuaGFuZGxlU3VibWl0KGV2ZW50LCBncmlkKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGUgZ3JpZCBhY3Rpb24gc3VibWl0LlxuICAgKiBJdCB1c2VzIGdyaWQgZm9ybSB0byBzdWJtaXQgYWN0aW9ucy5cbiAgICpcbiAgICogQHBhcmFtIHtFdmVudH0gZXZlbnRcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBoYW5kbGVTdWJtaXQoZXZlbnQsIGdyaWQpIHtcbiAgICBjb25zdCAkc3VibWl0QnRuID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICBjb25zdCBjb25maXJtTWVzc2FnZSA9ICRzdWJtaXRCdG4uZGF0YSgnY29uZmlybS1tZXNzYWdlJyk7XG5cbiAgICBpZiAodHlwZW9mIGNvbmZpcm1NZXNzYWdlICE9PSBcInVuZGVmaW5lZFwiICYmIDAgPCBjb25maXJtTWVzc2FnZS5sZW5ndGggJiYgIWNvbmZpcm0oY29uZmlybU1lc3NhZ2UpKSB7XG4gICAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBjb25zdCAkZm9ybSA9ICQoJyMnICsgZ3JpZC5nZXRJZCgpICsgJ19maWx0ZXJfZm9ybScpO1xuXG4gICAgJGZvcm0uYXR0cignYWN0aW9uJywgJHN1Ym1pdEJ0bi5kYXRhKCd1cmwnKSk7XG4gICAgJGZvcm0uYXR0cignbWV0aG9kJywgJHN1Ym1pdEJ0bi5kYXRhKCdtZXRob2QnKSk7XG4gICAgJGZvcm0uZmluZCgnaW5wdXRbbmFtZT1cIicgKyBncmlkLmdldElkKCkgKyAnW190b2tlbl1cIl0nKS52YWwoJHN1Ym1pdEJ0bi5kYXRhKCdjc3JmJykpO1xuICAgICRmb3JtLnN1Ym1pdCgpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3N1Ym1pdC1ncmlkLWFjdGlvbi1leHRlbnNpb24uanMiLCJtb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGV4ZWMpe1xuICB0cnkge1xuICAgIHJldHVybiAhIWV4ZWMoKTtcbiAgfSBjYXRjaChlKXtcbiAgICByZXR1cm4gdHJ1ZTtcbiAgfVxufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2ZhaWxzLmpzXG4vLyBtb2R1bGUgaWQgPSA3XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciBnbG9iYWwgICAgPSByZXF1aXJlKCcuL19nbG9iYWwnKVxuICAsIGNvcmUgICAgICA9IHJlcXVpcmUoJy4vX2NvcmUnKVxuICAsIGN0eCAgICAgICA9IHJlcXVpcmUoJy4vX2N0eCcpXG4gICwgaGlkZSAgICAgID0gcmVxdWlyZSgnLi9faGlkZScpXG4gICwgUFJPVE9UWVBFID0gJ3Byb3RvdHlwZSc7XG5cbnZhciAkZXhwb3J0ID0gZnVuY3Rpb24odHlwZSwgbmFtZSwgc291cmNlKXtcbiAgdmFyIElTX0ZPUkNFRCA9IHR5cGUgJiAkZXhwb3J0LkZcbiAgICAsIElTX0dMT0JBTCA9IHR5cGUgJiAkZXhwb3J0LkdcbiAgICAsIElTX1NUQVRJQyA9IHR5cGUgJiAkZXhwb3J0LlNcbiAgICAsIElTX1BST1RPICA9IHR5cGUgJiAkZXhwb3J0LlBcbiAgICAsIElTX0JJTkQgICA9IHR5cGUgJiAkZXhwb3J0LkJcbiAgICAsIElTX1dSQVAgICA9IHR5cGUgJiAkZXhwb3J0LldcbiAgICAsIGV4cG9ydHMgICA9IElTX0dMT0JBTCA/IGNvcmUgOiBjb3JlW25hbWVdIHx8IChjb3JlW25hbWVdID0ge30pXG4gICAgLCBleHBQcm90byAgPSBleHBvcnRzW1BST1RPVFlQRV1cbiAgICAsIHRhcmdldCAgICA9IElTX0dMT0JBTCA/IGdsb2JhbCA6IElTX1NUQVRJQyA/IGdsb2JhbFtuYW1lXSA6IChnbG9iYWxbbmFtZV0gfHwge30pW1BST1RPVFlQRV1cbiAgICAsIGtleSwgb3duLCBvdXQ7XG4gIGlmKElTX0dMT0JBTClzb3VyY2UgPSBuYW1lO1xuICBmb3Ioa2V5IGluIHNvdXJjZSl7XG4gICAgLy8gY29udGFpbnMgaW4gbmF0aXZlXG4gICAgb3duID0gIUlTX0ZPUkNFRCAmJiB0YXJnZXQgJiYgdGFyZ2V0W2tleV0gIT09IHVuZGVmaW5lZDtcbiAgICBpZihvd24gJiYga2V5IGluIGV4cG9ydHMpY29udGludWU7XG4gICAgLy8gZXhwb3J0IG5hdGl2ZSBvciBwYXNzZWRcbiAgICBvdXQgPSBvd24gPyB0YXJnZXRba2V5XSA6IHNvdXJjZVtrZXldO1xuICAgIC8vIHByZXZlbnQgZ2xvYmFsIHBvbGx1dGlvbiBmb3IgbmFtZXNwYWNlc1xuICAgIGV4cG9ydHNba2V5XSA9IElTX0dMT0JBTCAmJiB0eXBlb2YgdGFyZ2V0W2tleV0gIT0gJ2Z1bmN0aW9uJyA/IHNvdXJjZVtrZXldXG4gICAgLy8gYmluZCB0aW1lcnMgdG8gZ2xvYmFsIGZvciBjYWxsIGZyb20gZXhwb3J0IGNvbnRleHRcbiAgICA6IElTX0JJTkQgJiYgb3duID8gY3R4KG91dCwgZ2xvYmFsKVxuICAgIC8vIHdyYXAgZ2xvYmFsIGNvbnN0cnVjdG9ycyBmb3IgcHJldmVudCBjaGFuZ2UgdGhlbSBpbiBsaWJyYXJ5XG4gICAgOiBJU19XUkFQICYmIHRhcmdldFtrZXldID09IG91dCA/IChmdW5jdGlvbihDKXtcbiAgICAgIHZhciBGID0gZnVuY3Rpb24oYSwgYiwgYyl7XG4gICAgICAgIGlmKHRoaXMgaW5zdGFuY2VvZiBDKXtcbiAgICAgICAgICBzd2l0Y2goYXJndW1lbnRzLmxlbmd0aCl7XG4gICAgICAgICAgICBjYXNlIDA6IHJldHVybiBuZXcgQztcbiAgICAgICAgICAgIGNhc2UgMTogcmV0dXJuIG5ldyBDKGEpO1xuICAgICAgICAgICAgY2FzZSAyOiByZXR1cm4gbmV3IEMoYSwgYik7XG4gICAgICAgICAgfSByZXR1cm4gbmV3IEMoYSwgYiwgYyk7XG4gICAgICAgIH0gcmV0dXJuIEMuYXBwbHkodGhpcywgYXJndW1lbnRzKTtcbiAgICAgIH07XG4gICAgICBGW1BST1RPVFlQRV0gPSBDW1BST1RPVFlQRV07XG4gICAgICByZXR1cm4gRjtcbiAgICAvLyBtYWtlIHN0YXRpYyB2ZXJzaW9ucyBmb3IgcHJvdG90eXBlIG1ldGhvZHNcbiAgICB9KShvdXQpIDogSVNfUFJPVE8gJiYgdHlwZW9mIG91dCA9PSAnZnVuY3Rpb24nID8gY3R4KEZ1bmN0aW9uLmNhbGwsIG91dCkgOiBvdXQ7XG4gICAgLy8gZXhwb3J0IHByb3RvIG1ldGhvZHMgdG8gY29yZS4lQ09OU1RSVUNUT1IlLm1ldGhvZHMuJU5BTUUlXG4gICAgaWYoSVNfUFJPVE8pe1xuICAgICAgKGV4cG9ydHMudmlydHVhbCB8fCAoZXhwb3J0cy52aXJ0dWFsID0ge30pKVtrZXldID0gb3V0O1xuICAgICAgLy8gZXhwb3J0IHByb3RvIG1ldGhvZHMgdG8gY29yZS4lQ09OU1RSVUNUT1IlLnByb3RvdHlwZS4lTkFNRSVcbiAgICAgIGlmKHR5cGUgJiAkZXhwb3J0LlIgJiYgZXhwUHJvdG8gJiYgIWV4cFByb3RvW2tleV0paGlkZShleHBQcm90bywga2V5LCBvdXQpO1xuICAgIH1cbiAgfVxufTtcbi8vIHR5cGUgYml0bWFwXG4kZXhwb3J0LkYgPSAxOyAgIC8vIGZvcmNlZFxuJGV4cG9ydC5HID0gMjsgICAvLyBnbG9iYWxcbiRleHBvcnQuUyA9IDQ7ICAgLy8gc3RhdGljXG4kZXhwb3J0LlAgPSA4OyAgIC8vIHByb3RvXG4kZXhwb3J0LkIgPSAxNjsgIC8vIGJpbmRcbiRleHBvcnQuVyA9IDMyOyAgLy8gd3JhcFxuJGV4cG9ydC5VID0gNjQ7ICAvLyBzYWZlXG4kZXhwb3J0LlIgPSAxMjg7IC8vIHJlYWwgcHJvdG8gbWV0aG9kIGZvciBgbGlicmFyeWAgXG5tb2R1bGUuZXhwb3J0cyA9ICRleHBvcnQ7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19leHBvcnQuanNcbi8vIG1vZHVsZSBpZCA9IDhcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgU2hvd2Nhc2VDYXJkQ2xvc2VFeHRlbnNpb24gaXMgcmVzcG9uc2libGUgZm9yIHByb3ZpZGluZyBoZWxwZXIgYmxvY2sgY2xvc2luZyBiZWhhdmlvclxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBTaG93Y2FzZUNhcmRDbG9zZUV4dGVuc2lvbiB7XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBoZWxwZXIgYmxvY2suXG4gICAqXG4gICAqIEBwYXJhbSB7U2hvd2Nhc2VDYXJkfSBoZWxwZXJCbG9ja1xuICAgKi9cbiAgZXh0ZW5kKGhlbHBlckJsb2NrKSB7XG4gICAgY29uc3QgY29udGFpbmVyID0gaGVscGVyQmxvY2suZ2V0Q29udGFpbmVyKCk7XG4gICAgY29udGFpbmVyLm9uKCdjbGljaycsICcuanMtcmVtb3ZlLWhlbHBlci1ibG9jaycsIChldnQpID0+IHtcbiAgICAgIGNvbnRhaW5lci5yZW1vdmUoKTtcblxuICAgICAgY29uc3QgJGJ0biA9ICQoZXZ0LnRhcmdldCk7XG4gICAgICBjb25zdCB1cmwgPSAkYnRuLmRhdGEoJ2Nsb3NlVXJsJyk7XG4gICAgICBjb25zdCBjYXJkTmFtZSA9ICRidG4uZGF0YSgnY2FyZE5hbWUnKTtcblxuICAgICAgaWYgKHVybCkge1xuICAgICAgICAvLyBub3RpZnkgdGhlIGNhcmQgd2FzIGNsb3NlZFxuICAgICAgICAkLnBvc3QoXG4gICAgICAgICAgdXJsLFxuICAgICAgICAgIHtcbiAgICAgICAgICAgIGNsb3NlOiAxLFxuICAgICAgICAgICAgbmFtZTogY2FyZE5hbWVcbiAgICAgICAgICB9XG4gICAgICAgICk7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvc2hvd2Nhc2UtY2FyZC9leHRlbnNpb24vc2hvd2Nhc2UtY2FyZC1jbG9zZS1leHRlbnNpb24uanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBTaG93Y2FzZUNhcmQgaXMgcmVzcG9uc2libGUgZm9yIGhhbmRsaW5nIGV2ZW50cyByZWxhdGVkIHdpdGggc2hvd2Nhc2UgY2FyZC5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgU2hvd2Nhc2VDYXJkIHtcblxuICAvKipcbiAgICogU2hvd2Nhc2UgY2FyZCBpZC5cbiAgICpcbiAgICogQHBhcmFtIHtzdHJpbmd9IGlkXG4gICAqL1xuICBjb25zdHJ1Y3RvcihpZCkge1xuICAgIHRoaXMuaWQgPSBpZDtcbiAgICB0aGlzLiRjb250YWluZXIgPSAkKCcjJyArIHRoaXMuaWQpO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBzaG93Y2FzZSBjYXJkIGNvbnRhaW5lci5cbiAgICpcbiAgICogQHJldHVybnMge2pRdWVyeX1cbiAgICovXG4gIGdldENvbnRhaW5lcigpIHtcbiAgICByZXR1cm4gdGhpcy4kY29udGFpbmVyO1xuICB9XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBzaG93Y2FzZSBjYXJkIHdpdGggZXh0ZXJuYWwgZXh0ZW5zaW9ucy5cbiAgICpcbiAgICogQHBhcmFtIHtvYmplY3R9IGV4dGVuc2lvblxuICAgKi9cbiAgYWRkRXh0ZW5zaW9uKGV4dGVuc2lvbikge1xuICAgIGV4dGVuc2lvbi5leHRlbmQodGhpcyk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvc2hvd2Nhc2UtY2FyZC9zaG93Y2FzZS1jYXJkLmpzIiwidmFyIGc7XHJcblxyXG4vLyBUaGlzIHdvcmtzIGluIG5vbi1zdHJpY3QgbW9kZVxyXG5nID0gKGZ1bmN0aW9uKCkge1xyXG5cdHJldHVybiB0aGlzO1xyXG59KSgpO1xyXG5cclxudHJ5IHtcclxuXHQvLyBUaGlzIHdvcmtzIGlmIGV2YWwgaXMgYWxsb3dlZCAoc2VlIENTUClcclxuXHRnID0gZyB8fCBGdW5jdGlvbihcInJldHVybiB0aGlzXCIpKCkgfHwgKDEsZXZhbCkoXCJ0aGlzXCIpO1xyXG59IGNhdGNoKGUpIHtcclxuXHQvLyBUaGlzIHdvcmtzIGlmIHRoZSB3aW5kb3cgcmVmZXJlbmNlIGlzIGF2YWlsYWJsZVxyXG5cdGlmKHR5cGVvZiB3aW5kb3cgPT09IFwib2JqZWN0XCIpXHJcblx0XHRnID0gd2luZG93O1xyXG59XHJcblxyXG4vLyBnIGNhbiBzdGlsbCBiZSB1bmRlZmluZWQsIGJ1dCBub3RoaW5nIHRvIGRvIGFib3V0IGl0Li4uXHJcbi8vIFdlIHJldHVybiB1bmRlZmluZWQsIGluc3RlYWQgb2Ygbm90aGluZyBoZXJlLCBzbyBpdCdzXHJcbi8vIGVhc2llciB0byBoYW5kbGUgdGhpcyBjYXNlLiBpZighZ2xvYmFsKSB7IC4uLn1cclxuXHJcbm1vZHVsZS5leHBvcnRzID0gZztcclxuXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gKHdlYnBhY2spL2J1aWxkaW4vZ2xvYmFsLmpzXG4vLyBtb2R1bGUgaWQgPSA5XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDkgMTAgMTEgMTIgMTMgMTcgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDYgNTQiXSwic291cmNlUm9vdCI6IiJ9