window["language"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 503);
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

/***/ 37:
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
 * Class SubmitRowActionExtension handles submitting of row action
 */

var SubmitRowActionExtension = function () {
  function SubmitRowActionExtension() {
    (0, _classCallCheck3.default)(this, SubmitRowActionExtension);
  }

  (0, _createClass3.default)(SubmitRowActionExtension, [{
    key: 'extend',

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      grid.getContainer().on('click', '.js-submit-row-action', function (event) {
        event.preventDefault();

        var $button = $(event.currentTarget);
        var confirmMessage = $button.data('confirm-message');

        if (confirmMessage.length && !confirm(confirmMessage)) {
          return;
        }

        var method = $button.data('method');
        var isGetOrPostMethod = ['GET', 'POST'].includes(method);

        var $form = $('<form>', {
          'action': $button.data('url'),
          'method': isGetOrPostMethod ? method : 'POST'
        }).appendTo('body');

        if (!isGetOrPostMethod) {
          $form.append($('<input>', {
            'type': '_hidden',
            'name': '_method',
            'value': method
          }));
        }

        $form.submit();
      });
    }
  }]);
  return SubmitRowActionExtension;
}();

exports.default = SubmitRowActionExtension;

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

/***/ 5:
/***/ (function(module, exports) {

// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
var global = module.exports = typeof window != 'undefined' && window.Math == Math
  ? window : typeof self != 'undefined' && self.Math == Math ? self : Function('return this')();
if(typeof __g == 'number')__g = global; // eslint-disable-line no-undef

/***/ }),

/***/ 503:
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

var _sortingExtension = __webpack_require__(27);

var _sortingExtension2 = _interopRequireDefault(_sortingExtension);

var _linkRowActionExtension = __webpack_require__(34);

var _linkRowActionExtension2 = _interopRequireDefault(_linkRowActionExtension);

var _submitBulkActionExtension = __webpack_require__(32);

var _submitBulkActionExtension2 = _interopRequireDefault(_submitBulkActionExtension);

var _submitRowActionExtension = __webpack_require__(37);

var _submitRowActionExtension2 = _interopRequireDefault(_submitRowActionExtension);

var _bulkActionCheckboxExtension = __webpack_require__(31);

var _bulkActionCheckboxExtension2 = _interopRequireDefault(_bulkActionCheckboxExtension);

var _choiceTree = __webpack_require__(61);

var _choiceTree2 = _interopRequireDefault(_choiceTree);

var _columnTogglingExtension = __webpack_require__(62);

var _columnTogglingExtension2 = _interopRequireDefault(_columnTogglingExtension);

var _filtersSubmitButtonEnablerExtension = __webpack_require__(30);

var _filtersSubmitButtonEnablerExtension2 = _interopRequireDefault(_filtersSubmitButtonEnablerExtension);

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

$(document).ready(function () {
  var grid = new _grid2.default('language');

  grid.addExtension(new _reloadListExtension2.default());
  grid.addExtension(new _exportToSqlManagerExtension2.default());
  grid.addExtension(new _filtersResetExtension2.default());
  grid.addExtension(new _sortingExtension2.default());
  grid.addExtension(new _linkRowActionExtension2.default());
  grid.addExtension(new _submitBulkActionExtension2.default());
  grid.addExtension(new _submitRowActionExtension2.default());
  grid.addExtension(new _bulkActionCheckboxExtension2.default());
  grid.addExtension(new _columnTogglingExtension2.default());
  grid.addExtension(new _filtersSubmitButtonEnablerExtension2.default());

  // needed for shop association input in form
  new _choiceTree2.default('#language_shop_association').enableAutoCheckChildren();
});

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

/***/ 61:
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
 * Handles UI interactions of choice tree
 */

var ChoiceTree = function () {
  /**
   * @param {String} treeSelector
   */
  function ChoiceTree(treeSelector) {
    var _this = this;

    (0, _classCallCheck3.default)(this, ChoiceTree);

    this.$container = $(treeSelector);

    this.$container.on('click', '.js-input-wrapper', function (event) {
      var $inputWrapper = $(event.currentTarget);

      _this._toggleChildTree($inputWrapper);
    });

    this.$container.on('click', '.js-toggle-choice-tree-action', function (event) {
      var $action = $(event.currentTarget);

      _this._toggleTree($action);
    });

    return {
      enableAutoCheckChildren: function enableAutoCheckChildren() {
        return _this.enableAutoCheckChildren();
      },
      enableAllInputs: function enableAllInputs() {
        return _this.enableAllInputs();
      },
      disableAllInputs: function disableAllInputs() {
        return _this.disableAllInputs();
      }
    };
  }

  /**
   * Enable automatic check/uncheck of clicked item's children.
   */


  (0, _createClass3.default)(ChoiceTree, [{
    key: 'enableAutoCheckChildren',
    value: function enableAutoCheckChildren() {
      this.$container.on('change', 'input[type="checkbox"]', function (event) {
        var $clickedCheckbox = $(event.currentTarget);
        var $itemWithChildren = $clickedCheckbox.closest('li');

        $itemWithChildren.find('ul input[type="checkbox"]').prop('checked', $clickedCheckbox.is(':checked'));
      });
    }

    /**
     * Enable all inputs in the choice tree.
     */

  }, {
    key: 'enableAllInputs',
    value: function enableAllInputs() {
      this.$container.find('input').removeAttr('disabled');
    }

    /**
     * Disable all inputs in the choice tree.
     */

  }, {
    key: 'disableAllInputs',
    value: function disableAllInputs() {
      this.$container.find('input').attr('disabled', 'disabled');
    }

    /**
     * Collapse or expand sub-tree for single parent
     *
     * @param {jQuery} $inputWrapper
     *
     * @private
     */

  }, {
    key: '_toggleChildTree',
    value: function _toggleChildTree($inputWrapper) {
      var $parentWrapper = $inputWrapper.closest('li');

      if ($parentWrapper.hasClass('expanded')) {
        $parentWrapper.removeClass('expanded').addClass('collapsed');

        return;
      }

      if ($parentWrapper.hasClass('collapsed')) {
        $parentWrapper.removeClass('collapsed').addClass('expanded');
      }
    }

    /**
     * Collapse or expand whole tree
     *
     * @param {jQuery} $action
     *
     * @private
     */

  }, {
    key: '_toggleTree',
    value: function _toggleTree($action) {
      var $parentContainer = $action.closest('.js-choice-tree-container');
      var action = $action.data('action');

      // toggle action configuration
      var config = {
        addClass: {
          expand: 'expanded',
          collapse: 'collapsed'
        },
        removeClass: {
          expand: 'collapsed',
          collapse: 'expanded'
        },
        nextAction: {
          expand: 'collapse',
          collapse: 'expand'
        },
        text: {
          expand: 'collapsed-text',
          collapse: 'expanded-text'
        },
        icon: {
          expand: 'collapsed-icon',
          collapse: 'expanded-icon'
        }
      };

      $parentContainer.find('li').each(function (index, item) {
        var $item = $(item);

        if ($item.hasClass(config.removeClass[action])) {
          $item.removeClass(config.removeClass[action]).addClass(config.addClass[action]);
        }
      });

      $action.data('action', config.nextAction[action]);
      $action.find('.material-icons').text($action.data(config.icon[action]));
      $action.find('.js-toggle-text').text($action.data(config.text[action]));
    }
  }]);
  return ChoiceTree;
}();

exports.default = ChoiceTree;

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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgM2E2MTdjZWQyOWViY2NiNmExZDA/MTI1MCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2hlbHBlcnMvY2xhc3NDYWxsQ2hlY2suanM/MjFhZioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vYmFiZWwtcnVudGltZS9oZWxwZXJzL2NyZWF0ZUNsYXNzLmpzPzFkZmUqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19oaWRlLmpzP2E2ZGEqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2FuLW9iamVjdC5qcz8wZGEzKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19wcm9wZXJ0eS1kZXNjLmpzPzFlODYqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2N0eC5qcz9jZTAwKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL190by1wcmltaXRpdmUuanM/NDlhNCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZG9tLWNyZWF0ZS5qcz9hYjQ0KioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pZTgtZG9tLWRlZmluZS5qcz9iZDFmKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19hLWZ1bmN0aW9uLmpzP2Q1M2UqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vYmFiZWwtcnVudGltZS9jb3JlLWpzL29iamVjdC9kZWZpbmUtcHJvcGVydHkuanM/NWY3MCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZGVzY3JpcHRvcnMuanM/NzA1MSoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2RlZmluZS1wcm9wZXJ0eS5qcz9iN2Q4KioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5vYmplY3QuZGVmaW5lLXByb3BlcnR5LmpzP2M4MmMqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9ncmlkLmpzPzgxM2EqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2ZpbHRlcnMtcmVzZXQtZXh0ZW5zaW9uLmpzPzE2ZjEqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3NvcnRpbmctZXh0ZW5zaW9uLmpzPzExM2UqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3JlbG9hZC1saXN0LWV4dGVuc2lvbi5qcz9kM2UwKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9leHBvcnQtdG8tc3FsLW1hbmFnZXItZXh0ZW5zaW9uLmpzP2VkMmEqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19jb3JlLmpzPzFiNjIqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZmlsdGVycy1zdWJtaXQtYnV0dG9uLWVuYWJsZXItZXh0ZW5zaW9uLmpzPzAzNzkqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9idWxrLWFjdGlvbi1jaGVja2JveC1leHRlbnNpb24uanM/YjA5NyoqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vc3VibWl0LWJ1bGstYWN0aW9uLWV4dGVuc2lvbi5qcz8xYjFmKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9saW5rLXJvdy1hY3Rpb24tZXh0ZW5zaW9uLmpzPzM5ZGMqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9hY3Rpb24vcm93L3N1Ym1pdC1yb3ctYWN0aW9uLWV4dGVuc2lvbi5qcz8yN2QxKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faXMtb2JqZWN0LmpzPzI0YzgqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL2FwcC91dGlscy90YWJsZS1zb3J0aW5nLmpzPzE1ZDQqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9hcHAvdXRpbHMvcmVzZXRfc2VhcmNoLmpzPzFhN2YqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL21vZGFsLmpzPzc1NTgqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2dsb2JhbC5qcz83N2FhKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9sYW5ndWFnZS9pbmRleC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtZHAuanM/NDExNioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9mb3JtL2Nob2ljZS10cmVlLmpzPzU0MWEqKioqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vY29sdW1uLXRvZ2dsaW5nLWV4dGVuc2lvbi5qcz82OTQzKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2ZhaWxzLmpzPzkzNWQqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2V4cG9ydC5qcz9lY2UyKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vKHdlYnBhY2spL2J1aWxkaW4vZ2xvYmFsLmpzPzM2OTgqKioqKioqKioqKioqKioqKioqKioqKioiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsIkdyaWQiLCJpZCIsIiRjb250YWluZXIiLCJjbG9zZXN0IiwiZmluZCIsImV4dGVuc2lvbiIsImV4dGVuZCIsIkZpbHRlcnNSZXNldEV4dGVuc2lvbiIsImdyaWQiLCJnZXRDb250YWluZXIiLCJvbiIsImV2ZW50IiwiY3VycmVudFRhcmdldCIsImRhdGEiLCJTb3J0aW5nRXh0ZW5zaW9uIiwiJHNvcnRhYmxlVGFibGUiLCJUYWJsZVNvcnRpbmciLCJhdHRhY2giLCJSZWxvYWRMaXN0RXh0ZW5zaW9uIiwiZ2V0SGVhZGVyQ29udGFpbmVyIiwibG9jYXRpb24iLCJyZWxvYWQiLCJFeHBvcnRUb1NxbE1hbmFnZXJFeHRlbnNpb24iLCJfb25TaG93U3FsUXVlcnlDbGljayIsIl9vbkV4cG9ydFNxbE1hbmFnZXJDbGljayIsIiRzcWxNYW5hZ2VyRm9ybSIsImdldElkIiwiX2ZpbGxFeHBvcnRGb3JtIiwiJG1vZGFsIiwibW9kYWwiLCJzdWJtaXQiLCJxdWVyeSIsInZhbCIsIl9nZXROYW1lRnJvbUJyZWFkY3J1bWIiLCIkYnJlYWRjcnVtYnMiLCJuYW1lIiwiZWFjaCIsImkiLCJpdGVtIiwiJGJyZWFkY3J1bWIiLCJicmVhZGNydW1iVGl0bGUiLCJsZW5ndGgiLCJ0ZXh0IiwiY29uY2F0IiwiRmlsdGVyc1N1Ym1pdEJ1dHRvbkVuYWJsZXJFeHRlbnNpb24iLCIkZmlsdGVyc1JvdyIsInByb3AiLCJCdWxrQWN0aW9uQ2hlY2tib3hFeHRlbnNpb24iLCJfaGFuZGxlQnVsa0FjdGlvbkNoZWNrYm94U2VsZWN0IiwiX2hhbmRsZUJ1bGtBY3Rpb25TZWxlY3RBbGxDaGVja2JveCIsImUiLCIkY2hlY2tib3giLCJpc0NoZWNrZWQiLCJpcyIsIl9lbmFibGVCdWxrQWN0aW9uc0J0biIsIl9kaXNhYmxlQnVsa0FjdGlvbnNCdG4iLCJjaGVja2VkUm93c0NvdW50IiwiU3VibWl0QnVsa0FjdGlvbkV4dGVuc2lvbiIsIiRzdWJtaXRCdG4iLCJjb25maXJtTWVzc2FnZSIsImNvbmZpcm1UaXRsZSIsInVuZGVmaW5lZCIsInNob3dDb25maXJtTW9kYWwiLCJjb25maXJtIiwicG9zdEZvcm0iLCJjb25maXJtQnV0dG9uTGFiZWwiLCJjbG9zZUJ1dHRvbkxhYmVsIiwiY29uZmlybUJ1dHRvbkNsYXNzIiwiQ29uZmlybU1vZGFsIiwic2hvdyIsIiRmb3JtIiwiYXR0ciIsIkxpbmtSb3dBY3Rpb25FeHRlbnNpb24iLCJpbml0Um93TGlua3MiLCJpbml0Q29uZmlybWFibGVBY3Rpb25zIiwicHJldmVudERlZmF1bHQiLCJpbml0RWFjaFJvdyIsIiRwYXJlbnRSb3ciLCJwcm9wYWdhdGVGaXJzdExpbmtBY3Rpb24iLCIkcm93QWN0aW9uIiwiJHBhcmVudENlbGwiLCJjbGlja2FibGVDZWxscyIsIm5vdCIsImFkZENsYXNzIiwiY2xpY2siLCJkb2N1bWVudCIsIlN1Ym1pdFJvd0FjdGlvbkV4dGVuc2lvbiIsIiRidXR0b24iLCJtZXRob2QiLCJpc0dldE9yUG9zdE1ldGhvZCIsImluY2x1ZGVzIiwiYXBwZW5kVG8iLCJhcHBlbmQiLCJnbG9iYWwiLCJ0YWJsZSIsInNlbGVjdG9yIiwiY29sdW1ucyIsIiRjb2x1bW4iLCJkZWxlZ2F0ZVRhcmdldCIsIl9zb3J0QnlDb2x1bW4iLCJfZ2V0VG9nZ2xlZFNvcnREaXJlY3Rpb24iLCJjb2x1bW5OYW1lIiwiZGlyZWN0aW9uIiwiRXJyb3IiLCJjb2x1bW4iLCJfZ2V0VXJsIiwiY29sTmFtZSIsInByZWZpeCIsInVybCIsIlVSTCIsImhyZWYiLCJwYXJhbXMiLCJzZWFyY2hQYXJhbXMiLCJzZXQiLCJ0b1N0cmluZyIsImluaXQiLCJyZXNldFNlYXJjaCIsInJlZGlyZWN0VXJsIiwicG9zdCIsInRoZW4iLCJhc3NpZ24iLCJjb25maXJtQ2FsbGJhY2siLCJjbG9zYWJsZSIsIk1vZGFsIiwiY29udGFpbmVyIiwiY29uZmlybUJ1dHRvbiIsImFkZEV2ZW50TGlzdGVuZXIiLCJiYWNrZHJvcCIsImtleWJvYXJkIiwicXVlcnlTZWxlY3RvciIsInJlbW92ZSIsImJvZHkiLCJhcHBlbmRDaGlsZCIsImNyZWF0ZUVsZW1lbnQiLCJjbGFzc0xpc3QiLCJhZGQiLCJkaWFsb2ciLCJjb250ZW50IiwiaGVhZGVyIiwidGl0bGUiLCJpbm5lckhUTUwiLCJjbG9zZUljb24iLCJzZXRBdHRyaWJ1dGUiLCJkYXRhc2V0IiwiZGlzbWlzcyIsIm1lc3NhZ2UiLCJmb290ZXIiLCJjbG9zZUJ1dHRvbiIsInJlYWR5IiwiYWRkRXh0ZW5zaW9uIiwiUmVsb2FkTGlzdEFjdGlvbkV4dGVuc2lvbiIsIlN1Ym1pdEJ1bGtFeHRlbnNpb24iLCJDb2x1bW5Ub2dnbGluZ0V4dGVuc2lvbiIsIkNob2ljZVRyZWUiLCJlbmFibGVBdXRvQ2hlY2tDaGlsZHJlbiIsInRyZWVTZWxlY3RvciIsIiRpbnB1dFdyYXBwZXIiLCJfdG9nZ2xlQ2hpbGRUcmVlIiwiJGFjdGlvbiIsIl90b2dnbGVUcmVlIiwiZW5hYmxlQWxsSW5wdXRzIiwiZGlzYWJsZUFsbElucHV0cyIsIiRjbGlja2VkQ2hlY2tib3giLCIkaXRlbVdpdGhDaGlsZHJlbiIsInJlbW92ZUF0dHIiLCIkcGFyZW50V3JhcHBlciIsImhhc0NsYXNzIiwicmVtb3ZlQ2xhc3MiLCIkcGFyZW50Q29udGFpbmVyIiwiYWN0aW9uIiwiY29uZmlnIiwiZXhwYW5kIiwiY29sbGFwc2UiLCJuZXh0QWN0aW9uIiwiaWNvbiIsImluZGV4IiwiJGl0ZW0iLCIkdGFibGUiLCJfdG9nZ2xlVmFsdWUiLCJyb3ciLCJ0b2dnbGVVcmwiLCJfc3VibWl0QXNGb3JtIl0sIm1hcHBpbmdzIjoiOztBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQSxtREFBMkMsY0FBYzs7QUFFekQ7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxhQUFLO0FBQ0w7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxtQ0FBMkIsMEJBQTBCLEVBQUU7QUFDdkQseUNBQWlDLGVBQWU7QUFDaEQ7QUFDQTtBQUNBOztBQUVBO0FBQ0EsOERBQXNELCtEQUErRDs7QUFFckg7QUFDQTs7QUFFQTtBQUNBOzs7Ozs7Ozs7QUNoRUE7O0FBRUE7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQSxFOzs7Ozs7OztBQ1JBOztBQUVBOztBQUVBOztBQUVBOztBQUVBLHNDQUFzQyx1Q0FBdUMsZ0JBQWdCOztBQUU3RjtBQUNBO0FBQ0EsbUJBQW1CLGtCQUFrQjtBQUNyQztBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7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QTtBQUNBLENBQUM7QUFDRDtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0EsNENBQTRDOztBQUU1QyIsImZpbGUiOiJsYW5ndWFnZS5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBpZGVudGl0eSBmdW5jdGlvbiBmb3IgY2FsbGluZyBoYXJtb255IGltcG9ydHMgd2l0aCB0aGUgY29ycmVjdCBjb250ZXh0XG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmkgPSBmdW5jdGlvbih2YWx1ZSkgeyByZXR1cm4gdmFsdWU7IH07XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDUwMyk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gd2VicGFjay9ib290c3RyYXAgM2E2MTdjZWQyOWViY2NiNmExZDAiLCJcInVzZSBzdHJpY3RcIjtcblxuZXhwb3J0cy5fX2VzTW9kdWxlID0gdHJ1ZTtcblxuZXhwb3J0cy5kZWZhdWx0ID0gZnVuY3Rpb24gKGluc3RhbmNlLCBDb25zdHJ1Y3Rvcikge1xuICBpZiAoIShpbnN0YW5jZSBpbnN0YW5jZW9mIENvbnN0cnVjdG9yKSkge1xuICAgIHRocm93IG5ldyBUeXBlRXJyb3IoXCJDYW5ub3QgY2FsbCBhIGNsYXNzIGFzIGEgZnVuY3Rpb25cIik7XG4gIH1cbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvaGVscGVycy9jbGFzc0NhbGxDaGVjay5qc1xuLy8gbW9kdWxlIGlkID0gMFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIlwidXNlIHN0cmljdFwiO1xuXG5leHBvcnRzLl9fZXNNb2R1bGUgPSB0cnVlO1xuXG52YXIgX2RlZmluZVByb3BlcnR5ID0gcmVxdWlyZShcIi4uL2NvcmUtanMvb2JqZWN0L2RlZmluZS1wcm9wZXJ0eVwiKTtcblxudmFyIF9kZWZpbmVQcm9wZXJ0eTIgPSBfaW50ZXJvcFJlcXVpcmVEZWZhdWx0KF9kZWZpbmVQcm9wZXJ0eSk7XG5cbmZ1bmN0aW9uIF9pbnRlcm9wUmVxdWlyZURlZmF1bHQob2JqKSB7IHJldHVybiBvYmogJiYgb2JqLl9fZXNNb2R1bGUgPyBvYmogOiB7IGRlZmF1bHQ6IG9iaiB9OyB9XG5cbmV4cG9ydHMuZGVmYXVsdCA9IGZ1bmN0aW9uICgpIHtcbiAgZnVuY3Rpb24gZGVmaW5lUHJvcGVydGllcyh0YXJnZXQsIHByb3BzKSB7XG4gICAgZm9yICh2YXIgaSA9IDA7IGkgPCBwcm9wcy5sZW5ndGg7IGkrKykge1xuICAgICAgdmFyIGRlc2NyaXB0b3IgPSBwcm9wc1tpXTtcbiAgICAgIGRlc2NyaXB0b3IuZW51bWVyYWJsZSA9IGRlc2NyaXB0b3IuZW51bWVyYWJsZSB8fCBmYWxzZTtcbiAgICAgIGRlc2NyaXB0b3IuY29uZmlndXJhYmxlID0gdHJ1ZTtcbiAgICAgIGlmIChcInZhbHVlXCIgaW4gZGVzY3JpcHRvcikgZGVzY3JpcHRvci53cml0YWJsZSA9IHRydWU7XG4gICAgICAoMCwgX2RlZmluZVByb3BlcnR5Mi5kZWZhdWx0KSh0YXJnZXQsIGRlc2NyaXB0b3Iua2V5LCBkZXNjcmlwdG9yKTtcbiAgICB9XG4gIH1cblxuICByZXR1cm4gZnVuY3Rpb24gKENvbnN0cnVjdG9yLCBwcm90b1Byb3BzLCBzdGF0aWNQcm9wcykge1xuICAgIGlmIChwcm90b1Byb3BzKSBkZWZpbmVQcm9wZXJ0aWVzKENvbnN0cnVjdG9yLnByb3RvdHlwZSwgcHJvdG9Qcm9wcyk7XG4gICAgaWYgKHN0YXRpY1Byb3BzKSBkZWZpbmVQcm9wZXJ0aWVzKENvbnN0cnVjdG9yLCBzdGF0aWNQcm9wcyk7XG4gICAgcmV0dXJuIENvbnN0cnVjdG9yO1xuICB9O1xufSgpO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9iYWJlbC1ydW50aW1lL2hlbHBlcnMvY3JlYXRlQ2xhc3MuanNcbi8vIG1vZHVsZSBpZCA9IDFcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJ2YXIgZFAgICAgICAgICA9IHJlcXVpcmUoJy4vX29iamVjdC1kcCcpXG4gICwgY3JlYXRlRGVzYyA9IHJlcXVpcmUoJy4vX3Byb3BlcnR5LWRlc2MnKTtcbm1vZHVsZS5leHBvcnRzID0gcmVxdWlyZSgnLi9fZGVzY3JpcHRvcnMnKSA/IGZ1bmN0aW9uKG9iamVjdCwga2V5LCB2YWx1ZSl7XG4gIHJldHVybiBkUC5mKG9iamVjdCwga2V5LCBjcmVhdGVEZXNjKDEsIHZhbHVlKSk7XG59IDogZnVuY3Rpb24ob2JqZWN0LCBrZXksIHZhbHVlKXtcbiAgb2JqZWN0W2tleV0gPSB2YWx1ZTtcbiAgcmV0dXJuIG9iamVjdDtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19oaWRlLmpzXG4vLyBtb2R1bGUgaWQgPSAxMFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJ2YXIgaXNPYmplY3QgPSByZXF1aXJlKCcuL19pcy1vYmplY3QnKTtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXQpe1xuICBpZighaXNPYmplY3QoaXQpKXRocm93IFR5cGVFcnJvcihpdCArICcgaXMgbm90IGFuIG9iamVjdCEnKTtcbiAgcmV0dXJuIGl0O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2FuLW9iamVjdC5qc1xuLy8gbW9kdWxlIGlkID0gMTFcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwibW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihiaXRtYXAsIHZhbHVlKXtcbiAgcmV0dXJuIHtcbiAgICBlbnVtZXJhYmxlICA6ICEoYml0bWFwICYgMSksXG4gICAgY29uZmlndXJhYmxlOiAhKGJpdG1hcCAmIDIpLFxuICAgIHdyaXRhYmxlICAgIDogIShiaXRtYXAgJiA0KSxcbiAgICB2YWx1ZSAgICAgICA6IHZhbHVlXG4gIH07XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fcHJvcGVydHktZGVzYy5qc1xuLy8gbW9kdWxlIGlkID0gMTJcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiLy8gb3B0aW9uYWwgLyBzaW1wbGUgY29udGV4dCBiaW5kaW5nXG52YXIgYUZ1bmN0aW9uID0gcmVxdWlyZSgnLi9fYS1mdW5jdGlvbicpO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihmbiwgdGhhdCwgbGVuZ3RoKXtcbiAgYUZ1bmN0aW9uKGZuKTtcbiAgaWYodGhhdCA9PT0gdW5kZWZpbmVkKXJldHVybiBmbjtcbiAgc3dpdGNoKGxlbmd0aCl7XG4gICAgY2FzZSAxOiByZXR1cm4gZnVuY3Rpb24oYSl7XG4gICAgICByZXR1cm4gZm4uY2FsbCh0aGF0LCBhKTtcbiAgICB9O1xuICAgIGNhc2UgMjogcmV0dXJuIGZ1bmN0aW9uKGEsIGIpe1xuICAgICAgcmV0dXJuIGZuLmNhbGwodGhhdCwgYSwgYik7XG4gICAgfTtcbiAgICBjYXNlIDM6IHJldHVybiBmdW5jdGlvbihhLCBiLCBjKXtcbiAgICAgIHJldHVybiBmbi5jYWxsKHRoYXQsIGEsIGIsIGMpO1xuICAgIH07XG4gIH1cbiAgcmV0dXJuIGZ1bmN0aW9uKC8qIC4uLmFyZ3MgKi8pe1xuICAgIHJldHVybiBmbi5hcHBseSh0aGF0LCBhcmd1bWVudHMpO1xuICB9O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2N0eC5qc1xuLy8gbW9kdWxlIGlkID0gMTNcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiLy8gNy4xLjEgVG9QcmltaXRpdmUoaW5wdXQgWywgUHJlZmVycmVkVHlwZV0pXG52YXIgaXNPYmplY3QgPSByZXF1aXJlKCcuL19pcy1vYmplY3QnKTtcbi8vIGluc3RlYWQgb2YgdGhlIEVTNiBzcGVjIHZlcnNpb24sIHdlIGRpZG4ndCBpbXBsZW1lbnQgQEB0b1ByaW1pdGl2ZSBjYXNlXG4vLyBhbmQgdGhlIHNlY29uZCBhcmd1bWVudCAtIGZsYWcgLSBwcmVmZXJyZWQgdHlwZSBpcyBhIHN0cmluZ1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCwgUyl7XG4gIGlmKCFpc09iamVjdChpdCkpcmV0dXJuIGl0O1xuICB2YXIgZm4sIHZhbDtcbiAgaWYoUyAmJiB0eXBlb2YgKGZuID0gaXQudG9TdHJpbmcpID09ICdmdW5jdGlvbicgJiYgIWlzT2JqZWN0KHZhbCA9IGZuLmNhbGwoaXQpKSlyZXR1cm4gdmFsO1xuICBpZih0eXBlb2YgKGZuID0gaXQudmFsdWVPZikgPT0gJ2Z1bmN0aW9uJyAmJiAhaXNPYmplY3QodmFsID0gZm4uY2FsbChpdCkpKXJldHVybiB2YWw7XG4gIGlmKCFTICYmIHR5cGVvZiAoZm4gPSBpdC50b1N0cmluZykgPT0gJ2Z1bmN0aW9uJyAmJiAhaXNPYmplY3QodmFsID0gZm4uY2FsbChpdCkpKXJldHVybiB2YWw7XG4gIHRocm93IFR5cGVFcnJvcihcIkNhbid0IGNvbnZlcnQgb2JqZWN0IHRvIHByaW1pdGl2ZSB2YWx1ZVwiKTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL190by1wcmltaXRpdmUuanNcbi8vIG1vZHVsZSBpZCA9IDE0XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciBpc09iamVjdCA9IHJlcXVpcmUoJy4vX2lzLW9iamVjdCcpXG4gICwgZG9jdW1lbnQgPSByZXF1aXJlKCcuL19nbG9iYWwnKS5kb2N1bWVudFxuICAvLyBpbiBvbGQgSUUgdHlwZW9mIGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQgaXMgJ29iamVjdCdcbiAgLCBpcyA9IGlzT2JqZWN0KGRvY3VtZW50KSAmJiBpc09iamVjdChkb2N1bWVudC5jcmVhdGVFbGVtZW50KTtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXQpe1xuICByZXR1cm4gaXMgPyBkb2N1bWVudC5jcmVhdGVFbGVtZW50KGl0KSA6IHt9O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2RvbS1jcmVhdGUuanNcbi8vIG1vZHVsZSBpZCA9IDE2XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIm1vZHVsZS5leHBvcnRzID0gIXJlcXVpcmUoJy4vX2Rlc2NyaXB0b3JzJykgJiYgIXJlcXVpcmUoJy4vX2ZhaWxzJykoZnVuY3Rpb24oKXtcbiAgcmV0dXJuIE9iamVjdC5kZWZpbmVQcm9wZXJ0eShyZXF1aXJlKCcuL19kb20tY3JlYXRlJykoJ2RpdicpLCAnYScsIHtnZXQ6IGZ1bmN0aW9uKCl7IHJldHVybiA3OyB9fSkuYSAhPSA3O1xufSk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pZTgtZG9tLWRlZmluZS5qc1xuLy8gbW9kdWxlIGlkID0gMTdcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwibW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCl7XG4gIGlmKHR5cGVvZiBpdCAhPSAnZnVuY3Rpb24nKXRocm93IFR5cGVFcnJvcihpdCArICcgaXMgbm90IGEgZnVuY3Rpb24hJyk7XG4gIHJldHVybiBpdDtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19hLWZ1bmN0aW9uLmpzXG4vLyBtb2R1bGUgaWQgPSAxOFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJtb2R1bGUuZXhwb3J0cyA9IHsgXCJkZWZhdWx0XCI6IHJlcXVpcmUoXCJjb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2RlZmluZS1wcm9wZXJ0eVwiKSwgX19lc01vZHVsZTogdHJ1ZSB9O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvb2JqZWN0L2RlZmluZS1wcm9wZXJ0eS5qc1xuLy8gbW9kdWxlIGlkID0gMTlcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiLy8gVGhhbmsncyBJRTggZm9yIGhpcyBmdW5ueSBkZWZpbmVQcm9wZXJ0eVxubW9kdWxlLmV4cG9ydHMgPSAhcmVxdWlyZSgnLi9fZmFpbHMnKShmdW5jdGlvbigpe1xuICByZXR1cm4gT2JqZWN0LmRlZmluZVByb3BlcnR5KHt9LCAnYScsIHtnZXQ6IGZ1bmN0aW9uKCl7IHJldHVybiA3OyB9fSkuYSAhPSA3O1xufSk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19kZXNjcmlwdG9ycy5qc1xuLy8gbW9kdWxlIGlkID0gMlxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJyZXF1aXJlKCcuLi8uLi9tb2R1bGVzL2VzNi5vYmplY3QuZGVmaW5lLXByb3BlcnR5Jyk7XG52YXIgJE9iamVjdCA9IHJlcXVpcmUoJy4uLy4uL21vZHVsZXMvX2NvcmUnKS5PYmplY3Q7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uIGRlZmluZVByb3BlcnR5KGl0LCBrZXksIGRlc2Mpe1xuICByZXR1cm4gJE9iamVjdC5kZWZpbmVQcm9wZXJ0eShpdCwga2V5LCBkZXNjKTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9mbi9vYmplY3QvZGVmaW5lLXByb3BlcnR5LmpzXG4vLyBtb2R1bGUgaWQgPSAyMFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJ2YXIgJGV4cG9ydCA9IHJlcXVpcmUoJy4vX2V4cG9ydCcpO1xuLy8gMTkuMS4yLjQgLyAxNS4yLjMuNiBPYmplY3QuZGVmaW5lUHJvcGVydHkoTywgUCwgQXR0cmlidXRlcylcbiRleHBvcnQoJGV4cG9ydC5TICsgJGV4cG9ydC5GICogIXJlcXVpcmUoJy4vX2Rlc2NyaXB0b3JzJyksICdPYmplY3QnLCB7ZGVmaW5lUHJvcGVydHk6IHJlcXVpcmUoJy4vX29iamVjdC1kcCcpLmZ9KTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM2Lm9iamVjdC5kZWZpbmUtcHJvcGVydHkuanNcbi8vIG1vZHVsZSBpZCA9IDIxXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIGlzIHJlc3BvbnNpYmxlIGZvciBoYW5kbGluZyBHcmlkIGV2ZW50c1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBHcmlkIHtcbiAgLyoqXG4gICAqIEdyaWQgaWRcbiAgICpcbiAgICogQHBhcmFtIHtzdHJpbmd9IGlkXG4gICAqL1xuICBjb25zdHJ1Y3RvcihpZCkge1xuICAgIHRoaXMuaWQgPSBpZDtcbiAgICB0aGlzLiRjb250YWluZXIgPSAkKCcjJyArIHRoaXMuaWQgKyAnX2dyaWQnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgZ3JpZCBpZFxuICAgKlxuICAgKiBAcmV0dXJucyB7c3RyaW5nfVxuICAgKi9cbiAgZ2V0SWQoKSB7XG4gICAgcmV0dXJuIHRoaXMuaWQ7XG4gIH1cblxuICAvKipcbiAgICogR2V0IGdyaWQgY29udGFpbmVyXG4gICAqXG4gICAqIEByZXR1cm5zIHtqUXVlcnl9XG4gICAqL1xuICBnZXRDb250YWluZXIoKSB7XG4gICAgcmV0dXJuIHRoaXMuJGNvbnRhaW5lcjtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgZ3JpZCBoZWFkZXIgY29udGFpbmVyXG4gICAqXG4gICAqIEByZXR1cm5zIHtqUXVlcnl9XG4gICAqL1xuICBnZXRIZWFkZXJDb250YWluZXIoKSB7XG4gICAgcmV0dXJuIHRoaXMuJGNvbnRhaW5lci5jbG9zZXN0KCcuanMtZ3JpZC1wYW5lbCcpLmZpbmQoJy5qcy1ncmlkLWhlYWRlcicpO1xuICB9XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkIHdpdGggZXh0ZXJuYWwgZXh0ZW5zaW9uc1xuICAgKlxuICAgKiBAcGFyYW0ge29iamVjdH0gZXh0ZW5zaW9uXG4gICAqL1xuICBhZGRFeHRlbnNpb24oZXh0ZW5zaW9uKSB7XG4gICAgZXh0ZW5zaW9uLmV4dGVuZCh0aGlzKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2dyaWQuanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmltcG9ydCByZXNldFNlYXJjaCBmcm9tICdAYXBwL3V0aWxzL3Jlc2V0X3NlYXJjaCc7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBGaWx0ZXJzUmVzZXRFeHRlbnNpb24gZXh0ZW5kcyBncmlkIHdpdGggZmlsdGVycyByZXNldHRpbmdcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgRmlsdGVyc1Jlc2V0RXh0ZW5zaW9uIHtcblxuICAvKipcbiAgICogRXh0ZW5kIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkub24oJ2NsaWNrJywgJy5qcy1yZXNldC1zZWFyY2gnLCAoZXZlbnQpID0+IHtcbiAgICAgIHJlc2V0U2VhcmNoKCQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgndXJsJyksICQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgncmVkaXJlY3QnKSk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZmlsdGVycy1yZXNldC1leHRlbnNpb24uanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmltcG9ydCBUYWJsZVNvcnRpbmcgZnJvbSAnQGFwcC91dGlscy90YWJsZS1zb3J0aW5nJztcblxuLyoqXG4gKiBDbGFzcyBSZWxvYWRMaXN0RXh0ZW5zaW9uIGV4dGVuZHMgZ3JpZCB3aXRoIFwiTGlzdCByZWxvYWRcIiBhY3Rpb25cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgU29ydGluZ0V4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgY29uc3QgJHNvcnRhYmxlVGFibGUgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJ3RhYmxlLnRhYmxlJyk7XG5cbiAgICBuZXcgVGFibGVTb3J0aW5nKCRzb3J0YWJsZVRhYmxlKS5hdHRhY2goKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9zb3J0aW5nLWV4dGVuc2lvbi5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuLyoqXG4gKiBDbGFzcyBSZWxvYWRMaXN0RXh0ZW5zaW9uIGV4dGVuZHMgZ3JpZCB3aXRoIFwiTGlzdCByZWxvYWRcIiBhY3Rpb25cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgUmVsb2FkTGlzdEV4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRIZWFkZXJDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLWNvbW1vbl9yZWZyZXNoX2xpc3QtZ3JpZC1hY3Rpb24nLCAoKSA9PiB7XG4gICAgICBsb2NhdGlvbi5yZWxvYWQoKTtcbiAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9yZWxvYWQtbGlzdC1leHRlbnNpb24uanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBFeHBvcnRUb1NxbE1hbmFnZXJFeHRlbnNpb24gZXh0ZW5kcyBncmlkIHdpdGggZXhwb3J0aW5nIHF1ZXJ5IHRvIFNRTCBNYW5hZ2VyXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEV4cG9ydFRvU3FsTWFuYWdlckV4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRIZWFkZXJDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLWNvbW1vbl9zaG93X3F1ZXJ5LWdyaWQtYWN0aW9uJywgKCkgPT4gdGhpcy5fb25TaG93U3FsUXVlcnlDbGljayhncmlkKSk7XG4gICAgZ3JpZC5nZXRIZWFkZXJDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLWNvbW1vbl9leHBvcnRfc3FsX21hbmFnZXItZ3JpZC1hY3Rpb24nLCAoKSA9PiB0aGlzLl9vbkV4cG9ydFNxbE1hbmFnZXJDbGljayhncmlkKSk7XG4gIH1cblxuICAvKipcbiAgICogSW52b2tlZCB3aGVuIGNsaWNraW5nIG9uIHRoZSBcInNob3cgc3FsIHF1ZXJ5XCIgdG9vbGJhciBidXR0b25cbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfb25TaG93U3FsUXVlcnlDbGljayhncmlkKSB7XG4gICAgY29uc3QgJHNxbE1hbmFnZXJGb3JtID0gJCgnIycgKyBncmlkLmdldElkKCkgKyAnX2NvbW1vbl9zaG93X3F1ZXJ5X21vZGFsX2Zvcm0nKTtcbiAgICB0aGlzLl9maWxsRXhwb3J0Rm9ybSgkc3FsTWFuYWdlckZvcm0sIGdyaWQpO1xuXG4gICAgY29uc3QgJG1vZGFsID0gJCgnIycgKyBncmlkLmdldElkKCkgKyAnX2dyaWRfY29tbW9uX3Nob3dfcXVlcnlfbW9kYWwnKTtcbiAgICAkbW9kYWwubW9kYWwoJ3Nob3cnKTtcblxuICAgICRtb2RhbC5vbignY2xpY2snLCAnLmJ0bi1zcWwtc3VibWl0JywgKCkgPT4gJHNxbE1hbmFnZXJGb3JtLnN1Ym1pdCgpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJbnZva2VkIHdoZW4gY2xpY2tpbmcgb24gdGhlIFwiZXhwb3J0IHRvIHRoZSBzcWwgcXVlcnlcIiB0b29sYmFyIGJ1dHRvblxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9vbkV4cG9ydFNxbE1hbmFnZXJDbGljayhncmlkKSB7XG4gICAgY29uc3QgJHNxbE1hbmFnZXJGb3JtID0gJCgnIycgKyBncmlkLmdldElkKCkgKyAnX2NvbW1vbl9zaG93X3F1ZXJ5X21vZGFsX2Zvcm0nKTtcblxuICAgIHRoaXMuX2ZpbGxFeHBvcnRGb3JtKCRzcWxNYW5hZ2VyRm9ybSwgZ3JpZCk7XG5cbiAgICAkc3FsTWFuYWdlckZvcm0uc3VibWl0KCk7XG4gIH1cblxuICAvKipcbiAgICogRmlsbCBleHBvcnQgZm9ybSB3aXRoIFNRTCBhbmQgaXQncyBuYW1lXG4gICAqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkc3FsTWFuYWdlckZvcm1cbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfZmlsbEV4cG9ydEZvcm0oJHNxbE1hbmFnZXJGb3JtLCBncmlkKSB7XG4gICAgY29uc3QgcXVlcnkgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5qcy1ncmlkLXRhYmxlJykuZGF0YSgncXVlcnknKTtcblxuICAgICRzcWxNYW5hZ2VyRm9ybS5maW5kKCd0ZXh0YXJlYVtuYW1lPVwic3FsXCJdJykudmFsKHF1ZXJ5KTtcbiAgICAkc3FsTWFuYWdlckZvcm0uZmluZCgnaW5wdXRbbmFtZT1cIm5hbWVcIl0nKS52YWwodGhpcy5fZ2V0TmFtZUZyb21CcmVhZGNydW1iKCkpO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBleHBvcnQgbmFtZSBmcm9tIHBhZ2UncyBicmVhZGNydW1iXG4gICAqXG4gICAqIEByZXR1cm4ge1N0cmluZ31cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9nZXROYW1lRnJvbUJyZWFkY3J1bWIoKSB7XG4gICAgY29uc3QgJGJyZWFkY3J1bWJzID0gJCgnLmhlYWRlci10b29sYmFyJykuZmluZCgnLmJyZWFkY3J1bWItaXRlbScpO1xuICAgIGxldCBuYW1lID0gJyc7XG5cbiAgICAkYnJlYWRjcnVtYnMuZWFjaCgoaSwgaXRlbSkgPT4ge1xuICAgICAgY29uc3QgJGJyZWFkY3J1bWIgPSAkKGl0ZW0pO1xuXG4gICAgICBjb25zdCBicmVhZGNydW1iVGl0bGUgPSAwIDwgJGJyZWFkY3J1bWIuZmluZCgnYScpLmxlbmd0aCA/XG4gICAgICAgICRicmVhZGNydW1iLmZpbmQoJ2EnKS50ZXh0KCkgOlxuICAgICAgICAkYnJlYWRjcnVtYi50ZXh0KCk7XG5cbiAgICAgIGlmICgwIDwgbmFtZS5sZW5ndGgpIHtcbiAgICAgICAgbmFtZSA9IG5hbWUuY29uY2F0KCcgPiAnKTtcbiAgICAgIH1cblxuICAgICAgbmFtZSA9IG5hbWUuY29uY2F0KGJyZWFkY3J1bWJUaXRsZSk7XG4gICAgfSk7XG5cbiAgICByZXR1cm4gbmFtZTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9leHBvcnQtdG8tc3FsLW1hbmFnZXItZXh0ZW5zaW9uLmpzIiwidmFyIGNvcmUgPSBtb2R1bGUuZXhwb3J0cyA9IHt2ZXJzaW9uOiAnMi40LjAnfTtcbmlmKHR5cGVvZiBfX2UgPT0gJ251bWJlcicpX19lID0gY29yZTsgLy8gZXNsaW50LWRpc2FibGUtbGluZSBuby11bmRlZlxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fY29yZS5qc1xuLy8gbW9kdWxlIGlkID0gM1xuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbi8qKlxuICogUmVzcG9uc2libGUgZm9yIGdyaWQgZmlsdGVycyBzZWFyY2ggYW5kIHJlc2V0IGJ1dHRvbiBhdmFpbGFiaWxpdHkgd2hlbiBmaWx0ZXIgaW5wdXRzIGNoYW5nZXMuXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEZpbHRlcnNTdWJtaXRCdXR0b25FbmFibGVyRXh0ZW5zaW9uIHtcblxuICAvKipcbiAgICogRXh0ZW5kIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIGNvbnN0ICRmaWx0ZXJzUm93ID0gZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCcuY29sdW1uLWZpbHRlcnMnKTtcbiAgICAkZmlsdGVyc1Jvdy5maW5kKCcuZ3JpZC1zZWFyY2gtYnV0dG9uJykucHJvcCgnZGlzYWJsZWQnLCB0cnVlKTtcblxuICAgICRmaWx0ZXJzUm93LmZpbmQoJ2lucHV0Om5vdCguanMtYnVsay1hY3Rpb24tc2VsZWN0LWFsbCksIHNlbGVjdCcpLm9uKCdpbnB1dCBkcC5jaGFuZ2UnLCAoKSA9PiB7XG4gICAgICAkZmlsdGVyc1Jvdy5maW5kKCcuZ3JpZC1zZWFyY2gtYnV0dG9uJykucHJvcCgnZGlzYWJsZWQnLCBmYWxzZSk7XG4gICAgICAkZmlsdGVyc1Jvdy5maW5kKCcuanMtZ3JpZC1yZXNldC1idXR0b24nKS5wcm9wKCdoaWRkZW4nLCBmYWxzZSk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZmlsdGVycy1zdWJtaXQtYnV0dG9uLWVuYWJsZXItZXh0ZW5zaW9uLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgQnVsa0FjdGlvblNlbGVjdENoZWNrYm94RXh0ZW5zaW9uXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEJ1bGtBY3Rpb25DaGVja2JveEV4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZCB3aXRoIGJ1bGsgYWN0aW9uIGNoZWNrYm94ZXMgaGFuZGxpbmcgZnVuY3Rpb25hbGl0eVxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgdGhpcy5faGFuZGxlQnVsa0FjdGlvbkNoZWNrYm94U2VsZWN0KGdyaWQpO1xuICAgIHRoaXMuX2hhbmRsZUJ1bGtBY3Rpb25TZWxlY3RBbGxDaGVja2JveChncmlkKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGVzIFwiU2VsZWN0IGFsbFwiIGJ1dHRvbiBpbiB0aGUgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oYW5kbGVCdWxrQWN0aW9uU2VsZWN0QWxsQ2hlY2tib3goZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkub24oJ2NoYW5nZScsICcuanMtYnVsay1hY3Rpb24tc2VsZWN0LWFsbCcsIChlKSA9PiB7XG4gICAgICBjb25zdCAkY2hlY2tib3ggPSAkKGUuY3VycmVudFRhcmdldCk7XG5cbiAgICAgIGNvbnN0IGlzQ2hlY2tlZCA9ICRjaGVja2JveC5pcygnOmNoZWNrZWQnKTtcbiAgICAgIGlmIChpc0NoZWNrZWQpIHtcbiAgICAgICAgdGhpcy5fZW5hYmxlQnVsa0FjdGlvbnNCdG4oZ3JpZCk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICB0aGlzLl9kaXNhYmxlQnVsa0FjdGlvbnNCdG4oZ3JpZCk7XG4gICAgICB9XG5cbiAgICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgnLmpzLWJ1bGstYWN0aW9uLWNoZWNrYm94JykucHJvcCgnY2hlY2tlZCcsIGlzQ2hlY2tlZCk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogSGFuZGxlcyBlYWNoIGJ1bGsgYWN0aW9uIGNoZWNrYm94IHNlbGVjdCBpbiB0aGUgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9oYW5kbGVCdWxrQWN0aW9uQ2hlY2tib3hTZWxlY3QoZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkub24oJ2NoYW5nZScsICcuanMtYnVsay1hY3Rpb24tY2hlY2tib3gnLCAoKSA9PiB7XG4gICAgICBjb25zdCBjaGVja2VkUm93c0NvdW50ID0gZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCcuanMtYnVsay1hY3Rpb24tY2hlY2tib3g6Y2hlY2tlZCcpLmxlbmd0aDtcblxuICAgICAgaWYgKGNoZWNrZWRSb3dzQ291bnQgPiAwKSB7XG4gICAgICAgIHRoaXMuX2VuYWJsZUJ1bGtBY3Rpb25zQnRuKGdyaWQpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgdGhpcy5fZGlzYWJsZUJ1bGtBY3Rpb25zQnRuKGdyaWQpO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEVuYWJsZSBidWxrIGFjdGlvbnMgYnV0dG9uXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2VuYWJsZUJ1bGtBY3Rpb25zQnRuKGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5qcy1idWxrLWFjdGlvbnMtYnRuJykucHJvcCgnZGlzYWJsZWQnLCBmYWxzZSk7XG4gIH1cblxuICAvKipcbiAgICogRGlzYWJsZSBidWxrIGFjdGlvbnMgYnV0dG9uXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2Rpc2FibGVCdWxrQWN0aW9uc0J0bihncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCcuanMtYnVsay1hY3Rpb25zLWJ0bicpLnByb3AoJ2Rpc2FibGVkJywgdHJ1ZSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYnVsay1hY3Rpb24tY2hlY2tib3gtZXh0ZW5zaW9uLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5pbXBvcnQgQ29uZmlybU1vZGFsIGZyb20gJ0Bjb21wb25lbnRzL21vZGFsJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIEhhbmRsZXMgc3VibWl0IG9mIGdyaWQgYWN0aW9uc1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBTdWJtaXRCdWxrQWN0aW9uRXh0ZW5zaW9uIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgcmV0dXJuIHtcbiAgICAgIGV4dGVuZDogKGdyaWQpID0+IHRoaXMuZXh0ZW5kKGdyaWQpLFxuICAgIH07XG4gIH1cblxuICAvKipcbiAgICogRXh0ZW5kIGdyaWQgd2l0aCBidWxrIGFjdGlvbiBzdWJtaXR0aW5nXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtYnVsay1hY3Rpb24tc3VibWl0LWJ0bicsIChldmVudCkgPT4ge1xuICAgICAgdGhpcy5zdWJtaXQoZXZlbnQsIGdyaWQpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEhhbmRsZSBidWxrIGFjdGlvbiBzdWJtaXR0aW5nXG4gICAqXG4gICAqIEBwYXJhbSB7RXZlbnR9IGV2ZW50XG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgc3VibWl0KGV2ZW50LCBncmlkKSB7XG4gICAgY29uc3QgJHN1Ym1pdEJ0biA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgY29uc3QgY29uZmlybU1lc3NhZ2UgPSAkc3VibWl0QnRuLmRhdGEoJ2NvbmZpcm0tbWVzc2FnZScpO1xuICAgIGNvbnN0IGNvbmZpcm1UaXRsZSA9ICRzdWJtaXRCdG4uZGF0YSgnY29uZmlybVRpdGxlJyk7XG5cbiAgICBpZiAoY29uZmlybU1lc3NhZ2UgIT09IHVuZGVmaW5lZCAmJiAwIDwgY29uZmlybU1lc3NhZ2UubGVuZ3RoKSB7XG4gICAgICBpZiAoY29uZmlybVRpdGxlICE9PSB1bmRlZmluZWQpIHtcbiAgICAgICAgdGhpcy5zaG93Q29uZmlybU1vZGFsKCRzdWJtaXRCdG4sIGdyaWQsIGNvbmZpcm1NZXNzYWdlLCBjb25maXJtVGl0bGUpO1xuICAgICAgfSBlbHNlIGlmIChjb25maXJtKGNvbmZpcm1NZXNzYWdlKSkge1xuICAgICAgICB0aGlzLnBvc3RGb3JtKCRzdWJtaXRCdG4sIGdyaWQpO1xuICAgICAgfVxuICAgIH0gZWxzZSB7XG4gICAgICB0aGlzLnBvc3RGb3JtKCRzdWJtaXRCdG4sIGdyaWQpO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gJHN1Ym1pdEJ0blxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICogQHBhcmFtIHtzdHJpbmd9IGNvbmZpcm1NZXNzYWdlXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBjb25maXJtVGl0bGVcbiAgICovXG4gIHNob3dDb25maXJtTW9kYWwoJHN1Ym1pdEJ0biwgZ3JpZCwgY29uZmlybU1lc3NhZ2UsIGNvbmZpcm1UaXRsZSkge1xuICAgIGNvbnN0IGNvbmZpcm1CdXR0b25MYWJlbCA9ICRzdWJtaXRCdG4uZGF0YSgnY29uZmlybUJ1dHRvbkxhYmVsJyk7XG4gICAgY29uc3QgY2xvc2VCdXR0b25MYWJlbCA9ICRzdWJtaXRCdG4uZGF0YSgnY2xvc2VCdXR0b25MYWJlbCcpO1xuICAgIGNvbnN0IGNvbmZpcm1CdXR0b25DbGFzcyA9ICRzdWJtaXRCdG4uZGF0YSgnY29uZmlybUJ1dHRvbkNsYXNzJyk7XG5cbiAgICBjb25zdCBtb2RhbCA9IG5ldyBDb25maXJtTW9kYWwoe1xuICAgICAgaWQ6IGAke2dyaWQuZ2V0SWQoKX1fZ3JpZF9jb25maXJtX21vZGFsYCxcbiAgICAgIGNvbmZpcm1UaXRsZSxcbiAgICAgIGNvbmZpcm1NZXNzYWdlLFxuICAgICAgY29uZmlybUJ1dHRvbkxhYmVsLFxuICAgICAgY2xvc2VCdXR0b25MYWJlbCxcbiAgICAgIGNvbmZpcm1CdXR0b25DbGFzcyxcbiAgICB9LCAoKSA9PiB0aGlzLnBvc3RGb3JtKCRzdWJtaXRCdG4sIGdyaWQpKTtcblxuICAgIG1vZGFsLnNob3coKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gJHN1Ym1pdEJ0blxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIHBvc3RGb3JtKCRzdWJtaXRCdG4sIGdyaWQpIHtcbiAgICBjb25zdCAkZm9ybSA9ICQoJyMnICsgZ3JpZC5nZXRJZCgpICsgJ19maWx0ZXJfZm9ybScpO1xuXG4gICAgJGZvcm0uYXR0cignYWN0aW9uJywgJHN1Ym1pdEJ0bi5kYXRhKCdmb3JtLXVybCcpKTtcbiAgICAkZm9ybS5hdHRyKCdtZXRob2QnLCAkc3VibWl0QnRuLmRhdGEoJ2Zvcm0tbWV0aG9kJykpO1xuICAgICRmb3JtLnN1Ym1pdCgpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3N1Ym1pdC1idWxrLWFjdGlvbi1leHRlbnNpb24uanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBMaW5rUm93QWN0aW9uRXh0ZW5zaW9uIGhhbmRsZXMgbGluayByb3cgYWN0aW9uc1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBMaW5rUm93QWN0aW9uRXh0ZW5zaW9uIHtcbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICB0aGlzLmluaXRSb3dMaW5rcyhncmlkKTtcbiAgICB0aGlzLmluaXRDb25maXJtYWJsZUFjdGlvbnMoZ3JpZCk7XG4gIH1cblxuICAvKipcbiAgICogRXh0ZW5kIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBpbml0Q29uZmlybWFibGVBY3Rpb25zKGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtbGluay1yb3ctYWN0aW9uJywgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCBjb25maXJtTWVzc2FnZSA9ICQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgnY29uZmlybS1tZXNzYWdlJyk7XG5cbiAgICAgIGlmIChjb25maXJtTWVzc2FnZS5sZW5ndGggJiYgIWNvbmZpcm0oY29uZmlybU1lc3NhZ2UpKSB7XG4gICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQWRkIGEgY2xpY2sgZXZlbnQgb24gcm93cyB0aGF0IG1hdGNoZXMgdGhlIGZpcnN0IGxpbmsgYWN0aW9uIChpZiBwcmVzZW50KVxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGluaXRSb3dMaW5rcyhncmlkKSB7XG4gICAgJCgndHInLCBncmlkLmdldENvbnRhaW5lcigpKS5lYWNoKGZ1bmN0aW9uIGluaXRFYWNoUm93KCkge1xuICAgICAgY29uc3QgJHBhcmVudFJvdyA9ICQodGhpcyk7XG5cbiAgICAgICQoJy5qcy1saW5rLXJvdy1hY3Rpb25bZGF0YS1jbGlja2FibGUtcm93PTFdOmZpcnN0JywgJHBhcmVudFJvdykuZWFjaChmdW5jdGlvbiBwcm9wYWdhdGVGaXJzdExpbmtBY3Rpb24oKSB7XG4gICAgICAgIGNvbnN0ICRyb3dBY3Rpb24gPSAkKHRoaXMpO1xuICAgICAgICBjb25zdCAkcGFyZW50Q2VsbCA9ICRyb3dBY3Rpb24uY2xvc2VzdCgndGQnKTtcbiAgICAgICAgXG4gICAgICAgIGNvbnN0IGNsaWNrYWJsZUNlbGxzID0gJCgndGQuY2xpY2thYmxlJywgJHBhcmVudFJvdylcbiAgICAgICAgICAubm90KCRwYXJlbnRDZWxsKVxuICAgICAgICA7XG5cbiAgICAgICAgY2xpY2thYmxlQ2VsbHMuYWRkQ2xhc3MoJ2N1cnNvci1wb2ludGVyJykuY2xpY2soKCkgPT4ge1xuICAgICAgICAgIGNvbnN0IGNvbmZpcm1NZXNzYWdlID0gJHJvd0FjdGlvbi5kYXRhKCdjb25maXJtLW1lc3NhZ2UnKTtcblxuICAgICAgICAgIGlmICghY29uZmlybU1lc3NhZ2UubGVuZ3RoIHx8IGNvbmZpcm0oY29uZmlybU1lc3NhZ2UpKSB7XG4gICAgICAgICAgICBkb2N1bWVudC5sb2NhdGlvbiA9ICRyb3dBY3Rpb24uYXR0cignaHJlZicpO1xuICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgICB9KTtcbiAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9saW5rLXJvdy1hY3Rpb24tZXh0ZW5zaW9uLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgU3VibWl0Um93QWN0aW9uRXh0ZW5zaW9uIGhhbmRsZXMgc3VibWl0dGluZyBvZiByb3cgYWN0aW9uXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFN1Ym1pdFJvd0FjdGlvbkV4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLXN1Ym1pdC1yb3ctYWN0aW9uJywgKGV2ZW50KSA9PiB7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICBjb25zdCAkYnV0dG9uID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICAgIGNvbnN0IGNvbmZpcm1NZXNzYWdlID0gJGJ1dHRvbi5kYXRhKCdjb25maXJtLW1lc3NhZ2UnKTtcblxuICAgICAgaWYgKGNvbmZpcm1NZXNzYWdlLmxlbmd0aCAmJiAhY29uZmlybShjb25maXJtTWVzc2FnZSkpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICBjb25zdCBtZXRob2QgPSAkYnV0dG9uLmRhdGEoJ21ldGhvZCcpO1xuICAgICAgY29uc3QgaXNHZXRPclBvc3RNZXRob2QgPSBbJ0dFVCcsICdQT1NUJ10uaW5jbHVkZXMobWV0aG9kKTtcblxuICAgICAgY29uc3QgJGZvcm0gPSAkKCc8Zm9ybT4nLCB7XG4gICAgICAgICdhY3Rpb24nOiAkYnV0dG9uLmRhdGEoJ3VybCcpLFxuICAgICAgICAnbWV0aG9kJzogaXNHZXRPclBvc3RNZXRob2QgPyBtZXRob2QgOiAnUE9TVCcsXG4gICAgICB9KS5hcHBlbmRUbygnYm9keScpO1xuXG4gICAgICBpZiAoIWlzR2V0T3JQb3N0TWV0aG9kKSB7XG4gICAgICAgICRmb3JtLmFwcGVuZCgkKCc8aW5wdXQ+Jywge1xuICAgICAgICAgICd0eXBlJzogJ19oaWRkZW4nLFxuICAgICAgICAgICduYW1lJzogJ19tZXRob2QnLFxuICAgICAgICAgICd2YWx1ZSc6IG1ldGhvZFxuICAgICAgICB9KSk7XG4gICAgICB9XG5cbiAgICAgICRmb3JtLnN1Ym1pdCgpO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2FjdGlvbi9yb3cvc3VibWl0LXJvdy1hY3Rpb24tZXh0ZW5zaW9uLmpzIiwibW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCl7XG4gIHJldHVybiB0eXBlb2YgaXQgPT09ICdvYmplY3QnID8gaXQgIT09IG51bGwgOiB0eXBlb2YgaXQgPT09ICdmdW5jdGlvbic7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faXMtb2JqZWN0LmpzXG4vLyBtb2R1bGUgaWQgPSA0XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuY29uc3QgJCA9IGdsb2JhbC4kO1xuXG4vKipcbiAqIE1ha2VzIGEgdGFibGUgc29ydGFibGUgYnkgY29sdW1ucy5cbiAqIFRoaXMgZm9yY2VzIGEgcGFnZSByZWxvYWQgd2l0aCBtb3JlIHF1ZXJ5IHBhcmFtZXRlcnMuXG4gKi9cbmNsYXNzIFRhYmxlU29ydGluZyB7XG5cbiAgLyoqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSB0YWJsZVxuICAgKi9cbiAgY29uc3RydWN0b3IodGFibGUpIHtcbiAgICB0aGlzLnNlbGVjdG9yID0gJy5wcy1zb3J0YWJsZS1jb2x1bW4nO1xuICAgIHRoaXMuY29sdW1ucyA9ICQodGFibGUpLmZpbmQodGhpcy5zZWxlY3Rvcik7XG4gIH1cblxuICAvKipcbiAgICogQXR0YWNoZXMgdGhlIGxpc3RlbmVyc1xuICAgKi9cbiAgYXR0YWNoKCkge1xuICAgIHRoaXMuY29sdW1ucy5vbignY2xpY2snLCAoZSkgPT4ge1xuICAgICAgY29uc3QgJGNvbHVtbiA9ICQoZS5kZWxlZ2F0ZVRhcmdldCk7XG4gICAgICB0aGlzLl9zb3J0QnlDb2x1bW4oJGNvbHVtbiwgdGhpcy5fZ2V0VG9nZ2xlZFNvcnREaXJlY3Rpb24oJGNvbHVtbikpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIFNvcnQgdXNpbmcgYSBjb2x1bW4gbmFtZVxuICAgKiBAcGFyYW0ge3N0cmluZ30gY29sdW1uTmFtZVxuICAgKiBAcGFyYW0ge3N0cmluZ30gZGlyZWN0aW9uIFwiYXNjXCIgb3IgXCJkZXNjXCJcbiAgICovXG4gIHNvcnRCeShjb2x1bW5OYW1lLCBkaXJlY3Rpb24pIHtcbiAgICBjb25zdCAkY29sdW1uID0gdGhpcy5jb2x1bW5zLmlzKGBbZGF0YS1zb3J0LWNvbC1uYW1lPVwiJHtjb2x1bW5OYW1lfVwiXWApO1xuICAgIGlmICghJGNvbHVtbikge1xuICAgICAgdGhyb3cgbmV3IEVycm9yKGBDYW5ub3Qgc29ydCBieSBcIiR7Y29sdW1uTmFtZX1cIjogaW52YWxpZCBjb2x1bW5gKTtcbiAgICB9XG5cbiAgICB0aGlzLl9zb3J0QnlDb2x1bW4oJGNvbHVtbiwgZGlyZWN0aW9uKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTb3J0IHVzaW5nIGEgY29sdW1uIGVsZW1lbnRcbiAgICogQHBhcmFtIHtqUXVlcnl9IGNvbHVtblxuICAgKiBAcGFyYW0ge3N0cmluZ30gZGlyZWN0aW9uIFwiYXNjXCIgb3IgXCJkZXNjXCJcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zb3J0QnlDb2x1bW4oY29sdW1uLCBkaXJlY3Rpb24pIHtcbiAgICB3aW5kb3cubG9jYXRpb24gPSB0aGlzLl9nZXRVcmwoY29sdW1uLmRhdGEoJ3NvcnRDb2xOYW1lJyksIChkaXJlY3Rpb24gPT09ICdkZXNjJykgPyAnZGVzYycgOiAnYXNjJywgY29sdW1uLmRhdGEoJ3NvcnRQcmVmaXgnKSk7XG4gIH1cblxuICAvKipcbiAgICogUmV0dXJucyB0aGUgaW52ZXJ0ZWQgZGlyZWN0aW9uIHRvIHNvcnQgYWNjb3JkaW5nIHRvIHRoZSBjb2x1bW4ncyBjdXJyZW50IG9uZVxuICAgKiBAcGFyYW0ge2pRdWVyeX0gY29sdW1uXG4gICAqIEByZXR1cm4ge3N0cmluZ31cbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9nZXRUb2dnbGVkU29ydERpcmVjdGlvbihjb2x1bW4pIHtcbiAgICByZXR1cm4gY29sdW1uLmRhdGEoJ3NvcnREaXJlY3Rpb24nKSA9PT0gJ2FzYycgPyAnZGVzYycgOiAnYXNjJztcbiAgfVxuXG4gIC8qKlxuICAgKiBSZXR1cm5zIHRoZSB1cmwgZm9yIHRoZSBzb3J0ZWQgdGFibGVcbiAgICogQHBhcmFtIHtzdHJpbmd9IGNvbE5hbWVcbiAgICogQHBhcmFtIHtzdHJpbmd9IGRpcmVjdGlvblxuICAgKiBAcGFyYW0ge3N0cmluZ30gcHJlZml4XG4gICAqIEByZXR1cm4ge3N0cmluZ31cbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9nZXRVcmwoY29sTmFtZSwgZGlyZWN0aW9uLCBwcmVmaXgpIHtcbiAgICBjb25zdCB1cmwgPSBuZXcgVVJMKHdpbmRvdy5sb2NhdGlvbi5ocmVmKTtcbiAgICBjb25zdCBwYXJhbXMgPSB1cmwuc2VhcmNoUGFyYW1zO1xuXG4gICAgaWYgKHByZWZpeCkge1xuICAgICAgcGFyYW1zLnNldChwcmVmaXgrJ1tvcmRlckJ5XScsIGNvbE5hbWUpO1xuICAgICAgcGFyYW1zLnNldChwcmVmaXgrJ1tzb3J0T3JkZXJdJywgZGlyZWN0aW9uKTtcbiAgICB9IGVsc2Uge1xuICAgICAgcGFyYW1zLnNldCgnb3JkZXJCeScsIGNvbE5hbWUpO1xuICAgICAgcGFyYW1zLnNldCgnc29ydE9yZGVyJywgZGlyZWN0aW9uKTtcbiAgICB9XG5cbiAgICByZXR1cm4gdXJsLnRvU3RyaW5nKCk7XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgVGFibGVTb3J0aW5nO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvYXBwL3V0aWxzL3RhYmxlLXNvcnRpbmcuanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbi8qKlxuICogU2VuZCBhIFBvc3QgUmVxdWVzdCB0byByZXNldCBzZWFyY2ggQWN0aW9uLlxuICovXG5cbmNvbnN0ICQgPSBnbG9iYWwuJDtcblxuY29uc3QgaW5pdCA9IGZ1bmN0aW9uIHJlc2V0U2VhcmNoKHVybCwgcmVkaXJlY3RVcmwpIHtcbiAgICAkLnBvc3QodXJsKS50aGVuKCgpID0+IHdpbmRvdy5sb2NhdGlvbi5hc3NpZ24ocmVkaXJlY3RVcmwpKTtcbn07XG5cbmV4cG9ydCBkZWZhdWx0IGluaXQ7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9hcHAvdXRpbHMvcmVzZXRfc2VhcmNoLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ29uZmlybU1vZGFsIGNvbXBvbmVudFxuICpcbiAqIEBwYXJhbSB7U3RyaW5nfSBpZFxuICogQHBhcmFtIHtTdHJpbmd9IGNvbmZpcm1UaXRsZVxuICogQHBhcmFtIHtTdHJpbmd9IGNvbmZpcm1NZXNzYWdlXG4gKiBAcGFyYW0ge1N0cmluZ30gY2xvc2VCdXR0b25MYWJlbFxuICogQHBhcmFtIHtTdHJpbmd9IGNvbmZpcm1CdXR0b25MYWJlbFxuICogQHBhcmFtIHtTdHJpbmd9IGNvbmZpcm1CdXR0b25DbGFzc1xuICogQHBhcmFtIHtCb29sZWFufSBjbG9zYWJsZVxuICogQHBhcmFtIHtGdW5jdGlvbn0gY29uZmlybUNhbGxiYWNrXG4gKlxuICovXG5leHBvcnQgZGVmYXVsdCBmdW5jdGlvbiBDb25maXJtTW9kYWwocGFyYW1zLCBjb25maXJtQ2FsbGJhY2spIHtcbiAgLy8gQ29uc3RydWN0IHRoZSBtb2RhbFxuICBjb25zdCB7aWQsIGNsb3NhYmxlfSA9IHBhcmFtcztcbiAgdGhpcy5tb2RhbCA9IE1vZGFsKHBhcmFtcyk7XG5cbiAgLy8galF1ZXJ5IG1vZGFsIG9iamVjdFxuICB0aGlzLiRtb2RhbCA9ICQodGhpcy5tb2RhbC5jb250YWluZXIpO1xuXG4gIHRoaXMuc2hvdyA9ICgpID0+IHtcbiAgICB0aGlzLiRtb2RhbC5tb2RhbCgpO1xuICB9O1xuXG4gIHRoaXMubW9kYWwuY29uZmlybUJ1dHRvbi5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIGNvbmZpcm1DYWxsYmFjayk7XG5cbiAgdGhpcy4kbW9kYWwubW9kYWwoe1xuICAgIGJhY2tkcm9wOiAoY2xvc2FibGUgPyB0cnVlIDogJ3N0YXRpYycpLFxuICAgIGtleWJvYXJkOiBjbG9zYWJsZSAhPT0gdW5kZWZpbmVkID8gY2xvc2FibGUgOiB0cnVlLFxuICAgIGNsb3NhYmxlOiBjbG9zYWJsZSAhPT0gdW5kZWZpbmVkID8gY2xvc2FibGUgOiB0cnVlLFxuICAgIHNob3c6IGZhbHNlLFxuICB9KTtcblxuICB0aGlzLiRtb2RhbC5vbignaGlkZGVuLmJzLm1vZGFsJywgKCkgPT4ge1xuICAgIGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoYCMke2lkfWApLnJlbW92ZSgpO1xuICB9KTtcblxuICBkb2N1bWVudC5ib2R5LmFwcGVuZENoaWxkKHRoaXMubW9kYWwuY29udGFpbmVyKTtcbn1cblxuLyoqXG4gKiBNb2RhbCBjb21wb25lbnQgdG8gaW1wcm92ZSBsaXNpYmlsaXR5IGJ5IGNvbnN0cnVjdGluZyB0aGUgbW9kYWwgb3V0c2lkZSB0aGUgbWFpbiBmdW5jdGlvblxuICpcbiAqIEBwYXJhbSB7T2JqZWN0fSBwYXJhbXNcbiAqXG4gKi9cbmZ1bmN0aW9uIE1vZGFsKFxuICB7XG4gICAgaWQgPSAnY29uZmlybV9tb2RhbCcsXG4gICAgY29uZmlybVRpdGxlLFxuICAgIGNvbmZpcm1NZXNzYWdlID0gJycsXG4gICAgY2xvc2VCdXR0b25MYWJlbCA9ICdDbG9zZScsXG4gICAgY29uZmlybUJ1dHRvbkxhYmVsID0gJ0FjY2VwdCcsXG4gICAgY29uZmlybUJ1dHRvbkNsYXNzID0gJ2J0bi1wcmltYXJ5JyxcbiAgfSkge1xuICBjb25zdCBtb2RhbCA9IHt9O1xuXG4gIC8vIE1haW4gbW9kYWwgZWxlbWVudFxuICBtb2RhbC5jb250YWluZXIgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdkaXYnKTtcbiAgbW9kYWwuY29udGFpbmVyLmNsYXNzTGlzdC5hZGQoJ21vZGFsJywgJ2ZhZGUnKTtcbiAgbW9kYWwuY29udGFpbmVyLmlkID0gaWQ7XG5cbiAgLy8gTW9kYWwgZGlhbG9nIGVsZW1lbnRcbiAgbW9kYWwuZGlhbG9nID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2Jyk7XG4gIG1vZGFsLmRpYWxvZy5jbGFzc0xpc3QuYWRkKCdtb2RhbC1kaWFsb2cnKTtcblxuICAvLyBNb2RhbCBjb250ZW50IGVsZW1lbnRcbiAgbW9kYWwuY29udGVudCA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2RpdicpO1xuICBtb2RhbC5jb250ZW50LmNsYXNzTGlzdC5hZGQoJ21vZGFsLWNvbnRlbnQnKTtcblxuICAvLyBNb2RhbCBoZWFkZXIgZWxlbWVudFxuICBtb2RhbC5oZWFkZXIgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdkaXYnKTtcbiAgbW9kYWwuaGVhZGVyLmNsYXNzTGlzdC5hZGQoJ21vZGFsLWhlYWRlcicpO1xuXG4gIC8vIE1vZGFsIHRpdGxlIGVsZW1lbnRcbiAgaWYgKGNvbmZpcm1UaXRsZSkge1xuICAgIG1vZGFsLnRpdGxlID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnaDQnKTtcbiAgICBtb2RhbC50aXRsZS5jbGFzc0xpc3QuYWRkKCdtb2RhbC10aXRsZScpO1xuICAgIG1vZGFsLnRpdGxlLmlubmVySFRNTCA9IGNvbmZpcm1UaXRsZTtcbiAgfVxuXG4gIC8vIE1vZGFsIGNsb3NlIGJ1dHRvbiBpY29uXG4gIG1vZGFsLmNsb3NlSWNvbiA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2J1dHRvbicpO1xuICBtb2RhbC5jbG9zZUljb24uY2xhc3NMaXN0LmFkZCgnY2xvc2UnKTtcbiAgbW9kYWwuY2xvc2VJY29uLnNldEF0dHJpYnV0ZSgndHlwZScsICdidXR0b24nKTtcbiAgbW9kYWwuY2xvc2VJY29uLmRhdGFzZXQuZGlzbWlzcyA9ICdtb2RhbCc7XG4gIG1vZGFsLmNsb3NlSWNvbi5pbm5lckhUTUwgPSAnw5cnO1xuXG4gIC8vIE1vZGFsIGJvZHkgZWxlbWVudFxuICBtb2RhbC5ib2R5ID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2Jyk7XG4gIG1vZGFsLmJvZHkuY2xhc3NMaXN0LmFkZCgnbW9kYWwtYm9keScsICd0ZXh0LWxlZnQnLCAnZm9udC13ZWlnaHQtbm9ybWFsJyk7XG5cbiAgLy8gTW9kYWwgbWVzc2FnZSBlbGVtZW50XG4gIG1vZGFsLm1lc3NhZ2UgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdwJyk7XG4gIG1vZGFsLm1lc3NhZ2UuY2xhc3NMaXN0LmFkZCgnY29uZmlybS1tZXNzYWdlJyk7XG4gIG1vZGFsLm1lc3NhZ2UuaW5uZXJIVE1MID0gY29uZmlybU1lc3NhZ2U7XG5cbiAgLy8gTW9kYWwgZm9vdGVyIGVsZW1lbnRcbiAgbW9kYWwuZm9vdGVyID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2Jyk7XG4gIG1vZGFsLmZvb3Rlci5jbGFzc0xpc3QuYWRkKCdtb2RhbC1mb290ZXInKTtcblxuICAvLyBNb2RhbCBjbG9zZSBidXR0b24gZWxlbWVudFxuICBtb2RhbC5jbG9zZUJ1dHRvbiA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2J1dHRvbicpO1xuICBtb2RhbC5jbG9zZUJ1dHRvbi5zZXRBdHRyaWJ1dGUoJ3R5cGUnLCAnYnV0dG9uJyk7XG4gIG1vZGFsLmNsb3NlQnV0dG9uLmNsYXNzTGlzdC5hZGQoJ2J0bicsICdidG4tb3V0bGluZS1zZWNvbmRhcnknLCAnYnRuLWxnJyk7XG4gIG1vZGFsLmNsb3NlQnV0dG9uLmRhdGFzZXQuZGlzbWlzcyA9ICdtb2RhbCc7XG4gIG1vZGFsLmNsb3NlQnV0dG9uLmlubmVySFRNTCA9IGNsb3NlQnV0dG9uTGFiZWw7XG5cbiAgLy8gTW9kYWwgY2xvc2UgYnV0dG9uIGVsZW1lbnRcbiAgbW9kYWwuY29uZmlybUJ1dHRvbiA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2J1dHRvbicpO1xuICBtb2RhbC5jb25maXJtQnV0dG9uLnNldEF0dHJpYnV0ZSgndHlwZScsICdidXR0b24nKTtcbiAgbW9kYWwuY29uZmlybUJ1dHRvbi5jbGFzc0xpc3QuYWRkKCdidG4nLCBjb25maXJtQnV0dG9uQ2xhc3MsICdidG4tbGcnLCAnYnRuLWNvbmZpcm0tc3VibWl0Jyk7XG4gIG1vZGFsLmNvbmZpcm1CdXR0b24uZGF0YXNldC5kaXNtaXNzID0gJ21vZGFsJztcbiAgbW9kYWwuY29uZmlybUJ1dHRvbi5pbm5lckhUTUwgPSBjb25maXJtQnV0dG9uTGFiZWw7XG5cbiAgLy8gQ29uc3RydWN0aW5nIHRoZSBtb2RhbFxuICBpZiAoY29uZmlybVRpdGxlKSB7XG4gICAgbW9kYWwuaGVhZGVyLmFwcGVuZChtb2RhbC50aXRsZSwgbW9kYWwuY2xvc2VJY29uKTtcbiAgfSBlbHNlIHtcbiAgICBtb2RhbC5oZWFkZXIuYXBwZW5kQ2hpbGQobW9kYWwuY2xvc2VJY29uKTtcbiAgfVxuXG4gIG1vZGFsLmJvZHkuYXBwZW5kQ2hpbGQobW9kYWwubWVzc2FnZSk7XG4gIG1vZGFsLmZvb3Rlci5hcHBlbmQobW9kYWwuY2xvc2VCdXR0b24sIG1vZGFsLmNvbmZpcm1CdXR0b24pO1xuICBtb2RhbC5jb250ZW50LmFwcGVuZChtb2RhbC5oZWFkZXIsIG1vZGFsLmJvZHksIG1vZGFsLmZvb3Rlcik7XG4gIG1vZGFsLmRpYWxvZy5hcHBlbmRDaGlsZChtb2RhbC5jb250ZW50KTtcbiAgbW9kYWwuY29udGFpbmVyLmFwcGVuZENoaWxkKG1vZGFsLmRpYWxvZyk7XG5cbiAgcmV0dXJuIG1vZGFsO1xufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9tb2RhbC5qcyIsIi8vIGh0dHBzOi8vZ2l0aHViLmNvbS96bG9pcm9jay9jb3JlLWpzL2lzc3Vlcy84NiNpc3N1ZWNvbW1lbnQtMTE1NzU5MDI4XG52YXIgZ2xvYmFsID0gbW9kdWxlLmV4cG9ydHMgPSB0eXBlb2Ygd2luZG93ICE9ICd1bmRlZmluZWQnICYmIHdpbmRvdy5NYXRoID09IE1hdGhcbiAgPyB3aW5kb3cgOiB0eXBlb2Ygc2VsZiAhPSAndW5kZWZpbmVkJyAmJiBzZWxmLk1hdGggPT0gTWF0aCA/IHNlbGYgOiBGdW5jdGlvbigncmV0dXJuIHRoaXMnKSgpO1xuaWYodHlwZW9mIF9fZyA9PSAnbnVtYmVyJylfX2cgPSBnbG9iYWw7IC8vIGVzbGludC1kaXNhYmxlLWxpbmUgbm8tdW5kZWZcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2dsb2JhbC5qc1xuLy8gbW9kdWxlIGlkID0gNVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmltcG9ydCBHcmlkIGZyb20gJ0Bjb21wb25lbnRzL2dyaWQvZ3JpZCc7XG5pbXBvcnQgUmVsb2FkTGlzdEFjdGlvbkV4dGVuc2lvbiBmcm9tICdAY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9yZWxvYWQtbGlzdC1leHRlbnNpb24nO1xuaW1wb3J0IEV4cG9ydFRvU3FsTWFuYWdlckV4dGVuc2lvbiBmcm9tICdAY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9leHBvcnQtdG8tc3FsLW1hbmFnZXItZXh0ZW5zaW9uJztcbmltcG9ydCBGaWx0ZXJzUmVzZXRFeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZmlsdGVycy1yZXNldC1leHRlbnNpb24nO1xuaW1wb3J0IFNvcnRpbmdFeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vc29ydGluZy1leHRlbnNpb24nO1xuaW1wb3J0IExpbmtSb3dBY3Rpb25FeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vbGluay1yb3ctYWN0aW9uLWV4dGVuc2lvbic7XG5pbXBvcnQgU3VibWl0QnVsa0V4dGVuc2lvbiBmcm9tICdAY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9zdWJtaXQtYnVsay1hY3Rpb24tZXh0ZW5zaW9uJztcbmltcG9ydCBTdWJtaXRSb3dBY3Rpb25FeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYWN0aW9uL3Jvdy9zdWJtaXQtcm93LWFjdGlvbi1leHRlbnNpb24nO1xuaW1wb3J0IEJ1bGtBY3Rpb25DaGVja2JveEV4dGVuc2lvbiBmcm9tICdAY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9idWxrLWFjdGlvbi1jaGVja2JveC1leHRlbnNpb24nO1xuaW1wb3J0IENob2ljZVRyZWUgZnJvbSAnQGNvbXBvbmVudHMvZm9ybS9jaG9pY2UtdHJlZSc7XG5pbXBvcnQgQ29sdW1uVG9nZ2xpbmdFeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vY29sdW1uLXRvZ2dsaW5nLWV4dGVuc2lvbic7XG5pbXBvcnQgRmlsdGVyc1N1Ym1pdEJ1dHRvbkVuYWJsZXJFeHRlbnNpb25cbiAgZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZmlsdGVycy1zdWJtaXQtYnV0dG9uLWVuYWJsZXItZXh0ZW5zaW9uJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4kKGRvY3VtZW50KS5yZWFkeSgoKSA9PiB7XG4gIGNvbnN0IGdyaWQgPSBuZXcgR3JpZCgnbGFuZ3VhZ2UnKTtcblxuICBncmlkLmFkZEV4dGVuc2lvbihuZXcgUmVsb2FkTGlzdEFjdGlvbkV4dGVuc2lvbigpKTtcbiAgZ3JpZC5hZGRFeHRlbnNpb24obmV3IEV4cG9ydFRvU3FsTWFuYWdlckV4dGVuc2lvbigpKTtcbiAgZ3JpZC5hZGRFeHRlbnNpb24obmV3IEZpbHRlcnNSZXNldEV4dGVuc2lvbigpKTtcbiAgZ3JpZC5hZGRFeHRlbnNpb24obmV3IFNvcnRpbmdFeHRlbnNpb24oKSk7XG4gIGdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBMaW5rUm93QWN0aW9uRXh0ZW5zaW9uKCkpO1xuICBncmlkLmFkZEV4dGVuc2lvbihuZXcgU3VibWl0QnVsa0V4dGVuc2lvbigpKTtcbiAgZ3JpZC5hZGRFeHRlbnNpb24obmV3IFN1Ym1pdFJvd0FjdGlvbkV4dGVuc2lvbigpKTtcbiAgZ3JpZC5hZGRFeHRlbnNpb24obmV3IEJ1bGtBY3Rpb25DaGVja2JveEV4dGVuc2lvbigpKTtcbiAgZ3JpZC5hZGRFeHRlbnNpb24obmV3IENvbHVtblRvZ2dsaW5nRXh0ZW5zaW9uKCkpO1xuICBncmlkLmFkZEV4dGVuc2lvbihuZXcgRmlsdGVyc1N1Ym1pdEJ1dHRvbkVuYWJsZXJFeHRlbnNpb24oKSk7XG5cbiAgLy8gbmVlZGVkIGZvciBzaG9wIGFzc29jaWF0aW9uIGlucHV0IGluIGZvcm1cbiAgbmV3IENob2ljZVRyZWUoJyNsYW5ndWFnZV9zaG9wX2Fzc29jaWF0aW9uJykuZW5hYmxlQXV0b0NoZWNrQ2hpbGRyZW4oKTtcbn0pO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvbGFuZ3VhZ2UvaW5kZXguanMiLCJ2YXIgYW5PYmplY3QgICAgICAgPSByZXF1aXJlKCcuL19hbi1vYmplY3QnKVxuICAsIElFOF9ET01fREVGSU5FID0gcmVxdWlyZSgnLi9faWU4LWRvbS1kZWZpbmUnKVxuICAsIHRvUHJpbWl0aXZlICAgID0gcmVxdWlyZSgnLi9fdG8tcHJpbWl0aXZlJylcbiAgLCBkUCAgICAgICAgICAgICA9IE9iamVjdC5kZWZpbmVQcm9wZXJ0eTtcblxuZXhwb3J0cy5mID0gcmVxdWlyZSgnLi9fZGVzY3JpcHRvcnMnKSA/IE9iamVjdC5kZWZpbmVQcm9wZXJ0eSA6IGZ1bmN0aW9uIGRlZmluZVByb3BlcnR5KE8sIFAsIEF0dHJpYnV0ZXMpe1xuICBhbk9iamVjdChPKTtcbiAgUCA9IHRvUHJpbWl0aXZlKFAsIHRydWUpO1xuICBhbk9iamVjdChBdHRyaWJ1dGVzKTtcbiAgaWYoSUU4X0RPTV9ERUZJTkUpdHJ5IHtcbiAgICByZXR1cm4gZFAoTywgUCwgQXR0cmlidXRlcyk7XG4gIH0gY2F0Y2goZSl7IC8qIGVtcHR5ICovIH1cbiAgaWYoJ2dldCcgaW4gQXR0cmlidXRlcyB8fCAnc2V0JyBpbiBBdHRyaWJ1dGVzKXRocm93IFR5cGVFcnJvcignQWNjZXNzb3JzIG5vdCBzdXBwb3J0ZWQhJyk7XG4gIGlmKCd2YWx1ZScgaW4gQXR0cmlidXRlcylPW1BdID0gQXR0cmlidXRlcy52YWx1ZTtcbiAgcmV0dXJuIE87XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWRwLmpzXG4vLyBtb2R1bGUgaWQgPSA2XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIEhhbmRsZXMgVUkgaW50ZXJhY3Rpb25zIG9mIGNob2ljZSB0cmVlXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIENob2ljZVRyZWUge1xuICAvKipcbiAgICogQHBhcmFtIHtTdHJpbmd9IHRyZWVTZWxlY3RvclxuICAgKi9cbiAgY29uc3RydWN0b3IodHJlZVNlbGVjdG9yKSB7XG4gICAgdGhpcy4kY29udGFpbmVyID0gJCh0cmVlU2VsZWN0b3IpO1xuXG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjbGljaycsICcuanMtaW5wdXQtd3JhcHBlcicsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgJGlucHV0V3JhcHBlciA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG5cbiAgICAgIHRoaXMuX3RvZ2dsZUNoaWxkVHJlZSgkaW5wdXRXcmFwcGVyKTtcbiAgICB9KTtcblxuICAgIHRoaXMuJGNvbnRhaW5lci5vbignY2xpY2snLCAnLmpzLXRvZ2dsZS1jaG9pY2UtdHJlZS1hY3Rpb24nLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0ICRhY3Rpb24gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuXG4gICAgICB0aGlzLl90b2dnbGVUcmVlKCRhY3Rpb24pO1xuICAgIH0pO1xuXG4gICAgcmV0dXJuIHtcbiAgICAgIGVuYWJsZUF1dG9DaGVja0NoaWxkcmVuOiAoKSA9PiB0aGlzLmVuYWJsZUF1dG9DaGVja0NoaWxkcmVuKCksXG4gICAgICBlbmFibGVBbGxJbnB1dHM6ICgpID0+IHRoaXMuZW5hYmxlQWxsSW5wdXRzKCksXG4gICAgICBkaXNhYmxlQWxsSW5wdXRzOiAoKSA9PiB0aGlzLmRpc2FibGVBbGxJbnB1dHMoKSxcbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqIEVuYWJsZSBhdXRvbWF0aWMgY2hlY2svdW5jaGVjayBvZiBjbGlja2VkIGl0ZW0ncyBjaGlsZHJlbi5cbiAgICovXG4gIGVuYWJsZUF1dG9DaGVja0NoaWxkcmVuKCkge1xuICAgIHRoaXMuJGNvbnRhaW5lci5vbignY2hhbmdlJywgJ2lucHV0W3R5cGU9XCJjaGVja2JveFwiXScsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgJGNsaWNrZWRDaGVja2JveCA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgICBjb25zdCAkaXRlbVdpdGhDaGlsZHJlbiA9ICRjbGlja2VkQ2hlY2tib3guY2xvc2VzdCgnbGknKTtcblxuICAgICAgJGl0ZW1XaXRoQ2hpbGRyZW5cbiAgICAgICAgLmZpbmQoJ3VsIGlucHV0W3R5cGU9XCJjaGVja2JveFwiXScpXG4gICAgICAgIC5wcm9wKCdjaGVja2VkJywgJGNsaWNrZWRDaGVja2JveC5pcygnOmNoZWNrZWQnKSk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogRW5hYmxlIGFsbCBpbnB1dHMgaW4gdGhlIGNob2ljZSB0cmVlLlxuICAgKi9cbiAgZW5hYmxlQWxsSW5wdXRzKCkge1xuICAgIHRoaXMuJGNvbnRhaW5lci5maW5kKCdpbnB1dCcpLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XG4gIH1cblxuICAvKipcbiAgICogRGlzYWJsZSBhbGwgaW5wdXRzIGluIHRoZSBjaG9pY2UgdHJlZS5cbiAgICovXG4gIGRpc2FibGVBbGxJbnB1dHMoKSB7XG4gICAgdGhpcy4kY29udGFpbmVyLmZpbmQoJ2lucHV0JykuYXR0cignZGlzYWJsZWQnLCAnZGlzYWJsZWQnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBDb2xsYXBzZSBvciBleHBhbmQgc3ViLXRyZWUgZm9yIHNpbmdsZSBwYXJlbnRcbiAgICpcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRpbnB1dFdyYXBwZXJcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF90b2dnbGVDaGlsZFRyZWUoJGlucHV0V3JhcHBlcikge1xuICAgIGNvbnN0ICRwYXJlbnRXcmFwcGVyID0gJGlucHV0V3JhcHBlci5jbG9zZXN0KCdsaScpO1xuXG4gICAgaWYgKCRwYXJlbnRXcmFwcGVyLmhhc0NsYXNzKCdleHBhbmRlZCcpKSB7XG4gICAgICAkcGFyZW50V3JhcHBlclxuICAgICAgICAucmVtb3ZlQ2xhc3MoJ2V4cGFuZGVkJylcbiAgICAgICAgLmFkZENsYXNzKCdjb2xsYXBzZWQnKTtcblxuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIGlmICgkcGFyZW50V3JhcHBlci5oYXNDbGFzcygnY29sbGFwc2VkJykpIHtcbiAgICAgICRwYXJlbnRXcmFwcGVyXG4gICAgICAgIC5yZW1vdmVDbGFzcygnY29sbGFwc2VkJylcbiAgICAgICAgLmFkZENsYXNzKCdleHBhbmRlZCcpO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBDb2xsYXBzZSBvciBleHBhbmQgd2hvbGUgdHJlZVxuICAgKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gJGFjdGlvblxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3RvZ2dsZVRyZWUoJGFjdGlvbikge1xuICAgIGNvbnN0ICRwYXJlbnRDb250YWluZXIgPSAkYWN0aW9uLmNsb3Nlc3QoJy5qcy1jaG9pY2UtdHJlZS1jb250YWluZXInKTtcbiAgICBjb25zdCBhY3Rpb24gPSAkYWN0aW9uLmRhdGEoJ2FjdGlvbicpO1xuXG4gICAgLy8gdG9nZ2xlIGFjdGlvbiBjb25maWd1cmF0aW9uXG4gICAgY29uc3QgY29uZmlnID0ge1xuICAgICAgYWRkQ2xhc3M6IHtcbiAgICAgICAgZXhwYW5kOiAnZXhwYW5kZWQnLFxuICAgICAgICBjb2xsYXBzZTogJ2NvbGxhcHNlZCcsXG4gICAgICB9LFxuICAgICAgcmVtb3ZlQ2xhc3M6IHtcbiAgICAgICAgZXhwYW5kOiAnY29sbGFwc2VkJyxcbiAgICAgICAgY29sbGFwc2U6ICdleHBhbmRlZCcsXG4gICAgICB9LFxuICAgICAgbmV4dEFjdGlvbjoge1xuICAgICAgICBleHBhbmQ6ICdjb2xsYXBzZScsXG4gICAgICAgIGNvbGxhcHNlOiAnZXhwYW5kJyxcbiAgICAgIH0sXG4gICAgICB0ZXh0OiB7XG4gICAgICAgIGV4cGFuZDogJ2NvbGxhcHNlZC10ZXh0JyxcbiAgICAgICAgY29sbGFwc2U6ICdleHBhbmRlZC10ZXh0JyxcbiAgICAgIH0sXG4gICAgICBpY29uOiB7XG4gICAgICAgIGV4cGFuZDogJ2NvbGxhcHNlZC1pY29uJyxcbiAgICAgICAgY29sbGFwc2U6ICdleHBhbmRlZC1pY29uJyxcbiAgICAgIH1cbiAgICB9O1xuXG4gICAgJHBhcmVudENvbnRhaW5lci5maW5kKCdsaScpLmVhY2goKGluZGV4LCBpdGVtKSA9PiB7XG4gICAgICBjb25zdCAkaXRlbSA9ICQoaXRlbSk7XG5cbiAgICAgIGlmICgkaXRlbS5oYXNDbGFzcyhjb25maWcucmVtb3ZlQ2xhc3NbYWN0aW9uXSkpIHtcbiAgICAgICAgICAkaXRlbS5yZW1vdmVDbGFzcyhjb25maWcucmVtb3ZlQ2xhc3NbYWN0aW9uXSlcbiAgICAgICAgICAgIC5hZGRDbGFzcyhjb25maWcuYWRkQ2xhc3NbYWN0aW9uXSk7XG4gICAgICB9XG4gICAgfSk7XG5cbiAgICAkYWN0aW9uLmRhdGEoJ2FjdGlvbicsIGNvbmZpZy5uZXh0QWN0aW9uW2FjdGlvbl0pO1xuICAgICRhY3Rpb24uZmluZCgnLm1hdGVyaWFsLWljb25zJykudGV4dCgkYWN0aW9uLmRhdGEoY29uZmlnLmljb25bYWN0aW9uXSkpO1xuICAgICRhY3Rpb24uZmluZCgnLmpzLXRvZ2dsZS10ZXh0JykudGV4dCgkYWN0aW9uLmRhdGEoY29uZmlnLnRleHRbYWN0aW9uXSkpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2Zvcm0vY2hvaWNlLXRyZWUuanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmNvbnN0ICQgPSBnbG9iYWwuJDtcblxuLyoqXG4gKiBDbGFzcyBSZWxvYWRMaXN0RXh0ZW5zaW9uIGV4dGVuZHMgZ3JpZCB3aXRoIFwiQ29sdW1uIHRvZ2dsaW5nXCIgZmVhdHVyZVxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBDb2x1bW5Ub2dnbGluZ0V4dGVuc2lvbiB7XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBjb25zdCAkdGFibGUgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJ3RhYmxlLnRhYmxlJyk7XG4gICAgJHRhYmxlLmZpbmQoJy5wcy10b2dnbGFibGUtcm93Jykub24oJ2NsaWNrJywgKGUpID0+IHtcbiAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgIHRoaXMuX3RvZ2dsZVZhbHVlKCQoZS5kZWxlZ2F0ZVRhcmdldCkpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSByb3dcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF90b2dnbGVWYWx1ZShyb3cpIHtcbiAgICBjb25zdCB0b2dnbGVVcmwgPSByb3cuZGF0YSgndG9nZ2xlVXJsJyk7XG5cbiAgICB0aGlzLl9zdWJtaXRBc0Zvcm0odG9nZ2xlVXJsKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTdWJtaXRzIHJlcXVlc3QgdXJsIGFzIGZvcm1cbiAgICpcbiAgICogQHBhcmFtIHtzdHJpbmd9IHRvZ2dsZVVybFxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3N1Ym1pdEFzRm9ybSh0b2dnbGVVcmwpIHtcbiAgICBjb25zdCAkZm9ybSA9ICQoJzxmb3JtPicsIHtcbiAgICAgIGFjdGlvbjogdG9nZ2xlVXJsLFxuICAgICAgbWV0aG9kOiAnUE9TVCcsXG4gICAgfSkuYXBwZW5kVG8oJ2JvZHknKTtcblxuICAgICRmb3JtLnN1Ym1pdCgpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2NvbHVtbi10b2dnbGluZy1leHRlbnNpb24uanMiLCJtb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGV4ZWMpe1xuICB0cnkge1xuICAgIHJldHVybiAhIWV4ZWMoKTtcbiAgfSBjYXRjaChlKXtcbiAgICByZXR1cm4gdHJ1ZTtcbiAgfVxufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2ZhaWxzLmpzXG4vLyBtb2R1bGUgaWQgPSA3XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciBnbG9iYWwgICAgPSByZXF1aXJlKCcuL19nbG9iYWwnKVxuICAsIGNvcmUgICAgICA9IHJlcXVpcmUoJy4vX2NvcmUnKVxuICAsIGN0eCAgICAgICA9IHJlcXVpcmUoJy4vX2N0eCcpXG4gICwgaGlkZSAgICAgID0gcmVxdWlyZSgnLi9faGlkZScpXG4gICwgUFJPVE9UWVBFID0gJ3Byb3RvdHlwZSc7XG5cbnZhciAkZXhwb3J0ID0gZnVuY3Rpb24odHlwZSwgbmFtZSwgc291cmNlKXtcbiAgdmFyIElTX0ZPUkNFRCA9IHR5cGUgJiAkZXhwb3J0LkZcbiAgICAsIElTX0dMT0JBTCA9IHR5cGUgJiAkZXhwb3J0LkdcbiAgICAsIElTX1NUQVRJQyA9IHR5cGUgJiAkZXhwb3J0LlNcbiAgICAsIElTX1BST1RPICA9IHR5cGUgJiAkZXhwb3J0LlBcbiAgICAsIElTX0JJTkQgICA9IHR5cGUgJiAkZXhwb3J0LkJcbiAgICAsIElTX1dSQVAgICA9IHR5cGUgJiAkZXhwb3J0LldcbiAgICAsIGV4cG9ydHMgICA9IElTX0dMT0JBTCA/IGNvcmUgOiBjb3JlW25hbWVdIHx8IChjb3JlW25hbWVdID0ge30pXG4gICAgLCBleHBQcm90byAgPSBleHBvcnRzW1BST1RPVFlQRV1cbiAgICAsIHRhcmdldCAgICA9IElTX0dMT0JBTCA/IGdsb2JhbCA6IElTX1NUQVRJQyA/IGdsb2JhbFtuYW1lXSA6IChnbG9iYWxbbmFtZV0gfHwge30pW1BST1RPVFlQRV1cbiAgICAsIGtleSwgb3duLCBvdXQ7XG4gIGlmKElTX0dMT0JBTClzb3VyY2UgPSBuYW1lO1xuICBmb3Ioa2V5IGluIHNvdXJjZSl7XG4gICAgLy8gY29udGFpbnMgaW4gbmF0aXZlXG4gICAgb3duID0gIUlTX0ZPUkNFRCAmJiB0YXJnZXQgJiYgdGFyZ2V0W2tleV0gIT09IHVuZGVmaW5lZDtcbiAgICBpZihvd24gJiYga2V5IGluIGV4cG9ydHMpY29udGludWU7XG4gICAgLy8gZXhwb3J0IG5hdGl2ZSBvciBwYXNzZWRcbiAgICBvdXQgPSBvd24gPyB0YXJnZXRba2V5XSA6IHNvdXJjZVtrZXldO1xuICAgIC8vIHByZXZlbnQgZ2xvYmFsIHBvbGx1dGlvbiBmb3IgbmFtZXNwYWNlc1xuICAgIGV4cG9ydHNba2V5XSA9IElTX0dMT0JBTCAmJiB0eXBlb2YgdGFyZ2V0W2tleV0gIT0gJ2Z1bmN0aW9uJyA/IHNvdXJjZVtrZXldXG4gICAgLy8gYmluZCB0aW1lcnMgdG8gZ2xvYmFsIGZvciBjYWxsIGZyb20gZXhwb3J0IGNvbnRleHRcbiAgICA6IElTX0JJTkQgJiYgb3duID8gY3R4KG91dCwgZ2xvYmFsKVxuICAgIC8vIHdyYXAgZ2xvYmFsIGNvbnN0cnVjdG9ycyBmb3IgcHJldmVudCBjaGFuZ2UgdGhlbSBpbiBsaWJyYXJ5XG4gICAgOiBJU19XUkFQICYmIHRhcmdldFtrZXldID09IG91dCA/IChmdW5jdGlvbihDKXtcbiAgICAgIHZhciBGID0gZnVuY3Rpb24oYSwgYiwgYyl7XG4gICAgICAgIGlmKHRoaXMgaW5zdGFuY2VvZiBDKXtcbiAgICAgICAgICBzd2l0Y2goYXJndW1lbnRzLmxlbmd0aCl7XG4gICAgICAgICAgICBjYXNlIDA6IHJldHVybiBuZXcgQztcbiAgICAgICAgICAgIGNhc2UgMTogcmV0dXJuIG5ldyBDKGEpO1xuICAgICAgICAgICAgY2FzZSAyOiByZXR1cm4gbmV3IEMoYSwgYik7XG4gICAgICAgICAgfSByZXR1cm4gbmV3IEMoYSwgYiwgYyk7XG4gICAgICAgIH0gcmV0dXJuIEMuYXBwbHkodGhpcywgYXJndW1lbnRzKTtcbiAgICAgIH07XG4gICAgICBGW1BST1RPVFlQRV0gPSBDW1BST1RPVFlQRV07XG4gICAgICByZXR1cm4gRjtcbiAgICAvLyBtYWtlIHN0YXRpYyB2ZXJzaW9ucyBmb3IgcHJvdG90eXBlIG1ldGhvZHNcbiAgICB9KShvdXQpIDogSVNfUFJPVE8gJiYgdHlwZW9mIG91dCA9PSAnZnVuY3Rpb24nID8gY3R4KEZ1bmN0aW9uLmNhbGwsIG91dCkgOiBvdXQ7XG4gICAgLy8gZXhwb3J0IHByb3RvIG1ldGhvZHMgdG8gY29yZS4lQ09OU1RSVUNUT1IlLm1ldGhvZHMuJU5BTUUlXG4gICAgaWYoSVNfUFJPVE8pe1xuICAgICAgKGV4cG9ydHMudmlydHVhbCB8fCAoZXhwb3J0cy52aXJ0dWFsID0ge30pKVtrZXldID0gb3V0O1xuICAgICAgLy8gZXhwb3J0IHByb3RvIG1ldGhvZHMgdG8gY29yZS4lQ09OU1RSVUNUT1IlLnByb3RvdHlwZS4lTkFNRSVcbiAgICAgIGlmKHR5cGUgJiAkZXhwb3J0LlIgJiYgZXhwUHJvdG8gJiYgIWV4cFByb3RvW2tleV0paGlkZShleHBQcm90bywga2V5LCBvdXQpO1xuICAgIH1cbiAgfVxufTtcbi8vIHR5cGUgYml0bWFwXG4kZXhwb3J0LkYgPSAxOyAgIC8vIGZvcmNlZFxuJGV4cG9ydC5HID0gMjsgICAvLyBnbG9iYWxcbiRleHBvcnQuUyA9IDQ7ICAgLy8gc3RhdGljXG4kZXhwb3J0LlAgPSA4OyAgIC8vIHByb3RvXG4kZXhwb3J0LkIgPSAxNjsgIC8vIGJpbmRcbiRleHBvcnQuVyA9IDMyOyAgLy8gd3JhcFxuJGV4cG9ydC5VID0gNjQ7ICAvLyBzYWZlXG4kZXhwb3J0LlIgPSAxMjg7IC8vIHJlYWwgcHJvdG8gbWV0aG9kIGZvciBgbGlicmFyeWAgXG5tb2R1bGUuZXhwb3J0cyA9ICRleHBvcnQ7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19leHBvcnQuanNcbi8vIG1vZHVsZSBpZCA9IDhcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwidmFyIGc7XHJcblxyXG4vLyBUaGlzIHdvcmtzIGluIG5vbi1zdHJpY3QgbW9kZVxyXG5nID0gKGZ1bmN0aW9uKCkge1xyXG5cdHJldHVybiB0aGlzO1xyXG59KSgpO1xyXG5cclxudHJ5IHtcclxuXHQvLyBUaGlzIHdvcmtzIGlmIGV2YWwgaXMgYWxsb3dlZCAoc2VlIENTUClcclxuXHRnID0gZyB8fCBGdW5jdGlvbihcInJldHVybiB0aGlzXCIpKCkgfHwgKDEsZXZhbCkoXCJ0aGlzXCIpO1xyXG59IGNhdGNoKGUpIHtcclxuXHQvLyBUaGlzIHdvcmtzIGlmIHRoZSB3aW5kb3cgcmVmZXJlbmNlIGlzIGF2YWlsYWJsZVxyXG5cdGlmKHR5cGVvZiB3aW5kb3cgPT09IFwib2JqZWN0XCIpXHJcblx0XHRnID0gd2luZG93O1xyXG59XHJcblxyXG4vLyBnIGNhbiBzdGlsbCBiZSB1bmRlZmluZWQsIGJ1dCBub3RoaW5nIHRvIGRvIGFib3V0IGl0Li4uXHJcbi8vIFdlIHJldHVybiB1bmRlZmluZWQsIGluc3RlYWQgb2Ygbm90aGluZyBoZXJlLCBzbyBpdCdzXHJcbi8vIGVhc2llciB0byBoYW5kbGUgdGhpcyBjYXNlLiBpZighZ2xvYmFsKSB7IC4uLn1cclxuXHJcbm1vZHVsZS5leHBvcnRzID0gZztcclxuXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gKHdlYnBhY2spL2J1aWxkaW4vZ2xvYmFsLmpzXG4vLyBtb2R1bGUgaWQgPSA5XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDkgMTAgMTEgMTIgMTMgMTcgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDYgNTQiXSwic291cmNlUm9vdCI6IiJ9