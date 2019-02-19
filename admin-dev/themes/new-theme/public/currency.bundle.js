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
/******/ 	return __webpack_require__(__webpack_require__.s = "./js/pages/currency/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./js/app/utils/reset_search.js":
/*!**************************************!*\
  !*** ./js/app/utils/reset_search.js ***!
  \**************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(global) {/**
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
 * Send a Post Request to reset search Action.
 */
var $ = global.$;

var init = function resetSearch(url, redirectUrl) {
  $.post(url).then(function () {
    return window.location.assign(redirectUrl);
  });
};

/* harmony default export */ __webpack_exports__["default"] = (init);
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../node_modules/webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./js/app/utils/table-sorting.js":
/*!***************************************!*\
  !*** ./js/app/utils/table-sorting.js ***!
  \***************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(global) {function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

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
var $ = global.$;
/**
 * Makes a table sortable by columns.
 * This forces a page reload with more query parameters.
 */

var TableSorting =
/*#__PURE__*/
function () {
  /**
   * @param {jQuery} table
   */
  function TableSorting(table) {
    _classCallCheck(this, TableSorting);

    this.selector = '.ps-sortable-column';
    this.columns = $(table).find(this.selector);
  }
  /**
   * Attaches the listeners
   */


  _createClass(TableSorting, [{
    key: "attach",
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
    key: "sortBy",
    value: function sortBy(columnName, direction) {
      var $column = this.columns.is("[data-sort-col-name=\"".concat(columnName, "\"]"));

      if (!$column) {
        throw new Error("Cannot sort by \"".concat(columnName, "\": invalid column"));
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
    key: "_sortByColumn",
    value: function _sortByColumn(column, direction) {
      window.location = this._getUrl(column.data('sortColName'), direction === 'desc' ? 'desc' : 'asc');
    }
    /**
     * Returns the inverted direction to sort according to the column's current one
     * @param {jQuery} column
     * @return {string}
     * @private
     */

  }, {
    key: "_getToggledSortDirection",
    value: function _getToggledSortDirection(column) {
      return column.data('sortDirection') === 'asc' ? 'desc' : 'asc';
    }
    /**
     * Returns the url for the sorted table
     * @param {string} colName
     * @param {string} direction
     * @return {string}
     * @private
     */

  }, {
    key: "_getUrl",
    value: function _getUrl(colName, direction) {
      var url = new URL(window.location.href);
      var params = url.searchParams;
      params.set('orderBy', colName);
      params.set('sortOrder', direction);
      return url.toString();
    }
  }]);

  return TableSorting;
}();

/* harmony default export */ __webpack_exports__["default"] = (TableSorting);
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../node_modules/webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./js/components/form/choice-tree.js":
/*!*******************************************!*\
  !*** ./js/components/form/choice-tree.js ***!
  \*******************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return ChoiceTree; });
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
 * Handles UI interactions of choice tree
 */

var ChoiceTree =
/*#__PURE__*/
function () {
  /**
   * @param {String} treeSelector
   */
  function ChoiceTree(treeSelector) {
    var _this = this;

    _classCallCheck(this, ChoiceTree);

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
      }
    };
  }
  /**
   * Enable automatic check/uncheck of clicked item's children.
   */


  _createClass(ChoiceTree, [{
    key: "enableAutoCheckChildren",
    value: function enableAutoCheckChildren() {
      this.$container.on('change', 'input[type="checkbox"]', function (event) {
        var $clickedCheckbox = $(event.currentTarget);
        var $itemWithChildren = $clickedCheckbox.closest('li');
        $itemWithChildren.find('ul input[type="checkbox"]').prop('checked', $clickedCheckbox.is(':checked'));
      });
    }
    /**
     * Collapse or expand sub-tree for single parent
     *
     * @param {jQuery} $inputWrapper
     *
     * @private
     */

  }, {
    key: "_toggleChildTree",
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
    key: "_toggleTree",
    value: function _toggleTree($action) {
      var $parentContainer = $action.closest('.js-choice-tree-container');
      var action = $action.data('action'); // toggle action configuration

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



/***/ }),

/***/ "./js/components/grid/extension/action/row/submit-row-action-extension.js":
/*!********************************************************************************!*\
  !*** ./js/components/grid/extension/action/row/submit-row-action-extension.js ***!
  \********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return SubmitRowActionExtension; });
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
 * Class SubmitRowActionExtension handles submitting of row action
 */

var SubmitRowActionExtension =
/*#__PURE__*/
function () {
  function SubmitRowActionExtension() {
    _classCallCheck(this, SubmitRowActionExtension);
  }

  _createClass(SubmitRowActionExtension, [{
    key: "extend",

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



/***/ }),

/***/ "./js/components/grid/extension/column-toggling-extension.js":
/*!*******************************************************************!*\
  !*** ./js/components/grid/extension/column-toggling-extension.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(global) {/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return ColumnTogglingExtension; });
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
var $ = global.$;
/**
 * Class ReloadListExtension extends grid with "Column toggling" feature
 */

var ColumnTogglingExtension =
/*#__PURE__*/
function () {
  function ColumnTogglingExtension() {
    _classCallCheck(this, ColumnTogglingExtension);
  }

  _createClass(ColumnTogglingExtension, [{
    key: "extend",

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
    key: "_toggleValue",
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
    key: "_submitAsForm",
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


/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../../node_modules/webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./js/components/grid/extension/filters-reset-extension.js":
/*!*****************************************************************!*\
  !*** ./js/components/grid/extension/filters-reset-extension.js ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return FiltersResetExtension; });
/* harmony import */ var _app_utils_reset_search__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../app/utils/reset_search */ "./js/app/utils/reset_search.js");
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
 * Class FiltersResetExtension extends grid with filters resetting
 */

var FiltersResetExtension =
/*#__PURE__*/
function () {
  function FiltersResetExtension() {
    _classCallCheck(this, FiltersResetExtension);
  }

  _createClass(FiltersResetExtension, [{
    key: "extend",

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      grid.getContainer().on('click', '.js-reset-search', function (event) {
        Object(_app_utils_reset_search__WEBPACK_IMPORTED_MODULE_0__["default"])($(event.currentTarget).data('url'), $(event.currentTarget).data('redirect'));
      });
    }
  }]);

  return FiltersResetExtension;
}();



/***/ }),

/***/ "./js/components/grid/extension/reload-list-extension.js":
/*!***************************************************************!*\
  !*** ./js/components/grid/extension/reload-list-extension.js ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return ReloadListExtension; });
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

/**
 * Class ReloadListExtension extends grid with "List reload" action
 */
var ReloadListExtension =
/*#__PURE__*/
function () {
  function ReloadListExtension() {
    _classCallCheck(this, ReloadListExtension);
  }

  _createClass(ReloadListExtension, [{
    key: "extend",

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



/***/ }),

/***/ "./js/components/grid/extension/sorting-extension.js":
/*!***********************************************************!*\
  !*** ./js/components/grid/extension/sorting-extension.js ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return SortingExtension; });
/* harmony import */ var _app_utils_table_sorting__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../app/utils/table-sorting */ "./js/app/utils/table-sorting.js");
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

/**
 * Class ReloadListExtension extends grid with "List reload" action
 */

var SortingExtension =
/*#__PURE__*/
function () {
  function SortingExtension() {
    _classCallCheck(this, SortingExtension);
  }

  _createClass(SortingExtension, [{
    key: "extend",

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      var $sortableTable = grid.getContainer().find('table.table');
      new _app_utils_table_sorting__WEBPACK_IMPORTED_MODULE_0__["default"]($sortableTable).attach();
    }
  }]);

  return SortingExtension;
}();



/***/ }),

/***/ "./js/components/grid/grid.js":
/*!************************************!*\
  !*** ./js/components/grid/grid.js ***!
  \************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Grid; });
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
 * Class is responsible for handling Grid events
 */

var Grid =
/*#__PURE__*/
function () {
  /**
   * Grid id
   *
   * @param {string} id
   */
  function Grid(id) {
    _classCallCheck(this, Grid);

    this.id = id;
    this.$container = $('#' + this.id + '_grid');
  }
  /**
   * Get grid id
   *
   * @returns {string}
   */


  _createClass(Grid, [{
    key: "getId",
    value: function getId() {
      return this.id;
    }
    /**
     * Get grid container
     *
     * @returns {jQuery}
     */

  }, {
    key: "getContainer",
    value: function getContainer() {
      return this.$container;
    }
    /**
     * Get grid header container
     *
     * @returns {jQuery}
     */

  }, {
    key: "getHeaderContainer",
    value: function getHeaderContainer() {
      return this.$container.closest('.js-grid-panel').find('.js-grid-header');
    }
    /**
     * Extend grid with external extensions
     *
     * @param {object} extension
     */

  }, {
    key: "addExtension",
    value: function addExtension(extension) {
      extension.extend(this);
    }
  }]);

  return Grid;
}();



/***/ }),

/***/ "./js/pages/currency/ExchangeRatesUpdateScheduler.js":
/*!***********************************************************!*\
  !*** ./js/pages/currency/ExchangeRatesUpdateScheduler.js ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return ExchangeRatesUpdateScheduler; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
var $ = window.$;
/**
 * This class triggers events required for turning on or off exchange rates scheduler an displaying the right text
 * below the switch.
 */

var ExchangeRatesUpdateScheduler =
/*#__PURE__*/
function () {
  function ExchangeRatesUpdateScheduler() {
    _classCallCheck(this, ExchangeRatesUpdateScheduler);

    this._initEvents();

    return {};
  }

  _createClass(ExchangeRatesUpdateScheduler, [{
    key: "_initEvents",
    value: function _initEvents() {
      var _this = this;

      $(document).on('change', '.js-live-exchange-rate', function (event) {
        return _this._initLiveExchangeRate(event);
      });
    }
    /**
     * @param {Object} event
     *
     * @private
     */

  }, {
    key: "_initLiveExchangeRate",
    value: function _initLiveExchangeRate(event) {
      var _this2 = this;

      var $liveExchangeRatesSwitch = $(event.currentTarget);
      var $form = $liveExchangeRatesSwitch.closest('form');
      var formItems = $form.serialize();
      $.ajax({
        type: 'POST',
        url: $liveExchangeRatesSwitch.attr('data-url'),
        data: formItems
      }).then(function (response) {
        if (!response.status) {
          showErrorMessage(response.message);

          _this2._changeTextByCurrentSwitchValue($liveExchangeRatesSwitch.val());

          return;
        }

        showSuccessMessage(response.message);

        _this2._changeTextByCurrentSwitchValue($liveExchangeRatesSwitch.val());
      }).fail(function (response) {
        if (typeof response.responseJSON !== 'undefined') {
          showErrorMessage(response.responseJSON.message);

          _this2._changeTextByCurrentSwitchValue($liveExchangeRatesSwitch.val());
        }
      });
    }
  }, {
    key: "_changeTextByCurrentSwitchValue",
    value: function _changeTextByCurrentSwitchValue(switchValue) {
      var valueParsed = parseInt(switchValue);
      $('.js-exchange-rate-text-when-disabled').toggleClass('d-none', 0 !== valueParsed);
      $('.js-exchange-rate-text-when-enabled').toggleClass('d-none', 1 !== valueParsed);
    }
  }]);

  return ExchangeRatesUpdateScheduler;
}();



/***/ }),

/***/ "./js/pages/currency/index.js":
/*!************************************!*\
  !*** ./js/pages/currency/index.js ***!
  \************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _components_grid_grid__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../components/grid/grid */ "./js/components/grid/grid.js");
/* harmony import */ var _components_grid_extension_sorting_extension__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../components/grid/extension/sorting-extension */ "./js/components/grid/extension/sorting-extension.js");
/* harmony import */ var _components_grid_extension_filters_reset_extension__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../components/grid/extension/filters-reset-extension */ "./js/components/grid/extension/filters-reset-extension.js");
/* harmony import */ var _components_grid_extension_reload_list_extension__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../components/grid/extension/reload-list-extension */ "./js/components/grid/extension/reload-list-extension.js");
/* harmony import */ var _components_grid_extension_column_toggling_extension__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../components/grid/extension/column-toggling-extension */ "./js/components/grid/extension/column-toggling-extension.js");
/* harmony import */ var _components_grid_extension_action_row_submit_row_action_extension__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../components/grid/extension/action/row/submit-row-action-extension */ "./js/components/grid/extension/action/row/submit-row-action-extension.js");
/* harmony import */ var _components_form_choice_tree__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../components/form/choice-tree */ "./js/components/form/choice-tree.js");
/* harmony import */ var _ExchangeRatesUpdateScheduler__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./ExchangeRatesUpdateScheduler */ "./js/pages/currency/ExchangeRatesUpdateScheduler.js");
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
  var currency = new _components_grid_grid__WEBPACK_IMPORTED_MODULE_0__["default"]('currency');
  currency.addExtension(new _components_grid_extension_sorting_extension__WEBPACK_IMPORTED_MODULE_1__["default"]());
  currency.addExtension(new _components_grid_extension_filters_reset_extension__WEBPACK_IMPORTED_MODULE_2__["default"]());
  currency.addExtension(new _components_grid_extension_reload_list_extension__WEBPACK_IMPORTED_MODULE_3__["default"]());
  currency.addExtension(new _components_grid_extension_column_toggling_extension__WEBPACK_IMPORTED_MODULE_4__["default"]());
  currency.addExtension(new _components_grid_extension_action_row_submit_row_action_extension__WEBPACK_IMPORTED_MODULE_5__["default"]());
  var choiceTree = new _components_form_choice_tree__WEBPACK_IMPORTED_MODULE_6__["default"]('#currency_shop_association');
  choiceTree.enableAutoCheckChildren();
  new _ExchangeRatesUpdateScheduler__WEBPACK_IMPORTED_MODULE_7__["default"]();
});

/***/ }),

/***/ "./node_modules/webpack/buildin/global.js":
/*!***********************************!*\
  !*** (webpack)/buildin/global.js ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports) {

var g;

// This works in non-strict mode
g = (function() {
	return this;
})();

try {
	// This works if eval is allowed (see CSP)
	g = g || new Function("return this")();
} catch (e) {
	// This works if the window reference is available
	if (typeof window === "object") g = window;
}

// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}

module.exports = g;


/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vLy4vanMvYXBwL3V0aWxzL3Jlc2V0X3NlYXJjaC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9hcHAvdXRpbHMvdGFibGUtc29ydGluZy5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2Zvcm0vY2hvaWNlLXRyZWUuanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9hY3Rpb24vcm93L3N1Ym1pdC1yb3ctYWN0aW9uLWV4dGVuc2lvbi5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2NvbHVtbi10b2dnbGluZy1leHRlbnNpb24uanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXJlc2V0LWV4dGVuc2lvbi5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3JlbG9hZC1saXN0LWV4dGVuc2lvbi5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3NvcnRpbmctZXh0ZW5zaW9uLmpzIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9ncmlkLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL2N1cnJlbmN5L0V4Y2hhbmdlUmF0ZXNVcGRhdGVTY2hlZHVsZXIuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvY3VycmVuY3kvaW5kZXguanMiLCJ3ZWJwYWNrOi8vLyh3ZWJwYWNrKS9idWlsZGluL2dsb2JhbC5qcyJdLCJuYW1lcyI6WyIkIiwiZ2xvYmFsIiwiaW5pdCIsInJlc2V0U2VhcmNoIiwidXJsIiwicmVkaXJlY3RVcmwiLCJwb3N0IiwidGhlbiIsIndpbmRvdyIsImxvY2F0aW9uIiwiYXNzaWduIiwiVGFibGVTb3J0aW5nIiwidGFibGUiLCJzZWxlY3RvciIsImNvbHVtbnMiLCJmaW5kIiwib24iLCJlIiwiJGNvbHVtbiIsImRlbGVnYXRlVGFyZ2V0IiwiX3NvcnRCeUNvbHVtbiIsIl9nZXRUb2dnbGVkU29ydERpcmVjdGlvbiIsImNvbHVtbk5hbWUiLCJkaXJlY3Rpb24iLCJpcyIsIkVycm9yIiwiY29sdW1uIiwiX2dldFVybCIsImRhdGEiLCJjb2xOYW1lIiwiVVJMIiwiaHJlZiIsInBhcmFtcyIsInNlYXJjaFBhcmFtcyIsInNldCIsInRvU3RyaW5nIiwiQ2hvaWNlVHJlZSIsInRyZWVTZWxlY3RvciIsIiRjb250YWluZXIiLCJldmVudCIsIiRpbnB1dFdyYXBwZXIiLCJjdXJyZW50VGFyZ2V0IiwiX3RvZ2dsZUNoaWxkVHJlZSIsIiRhY3Rpb24iLCJfdG9nZ2xlVHJlZSIsImVuYWJsZUF1dG9DaGVja0NoaWxkcmVuIiwiJGNsaWNrZWRDaGVja2JveCIsIiRpdGVtV2l0aENoaWxkcmVuIiwiY2xvc2VzdCIsInByb3AiLCIkcGFyZW50V3JhcHBlciIsImhhc0NsYXNzIiwicmVtb3ZlQ2xhc3MiLCJhZGRDbGFzcyIsIiRwYXJlbnRDb250YWluZXIiLCJhY3Rpb24iLCJjb25maWciLCJleHBhbmQiLCJjb2xsYXBzZSIsIm5leHRBY3Rpb24iLCJ0ZXh0IiwiaWNvbiIsImVhY2giLCJpbmRleCIsIml0ZW0iLCIkaXRlbSIsIlN1Ym1pdFJvd0FjdGlvbkV4dGVuc2lvbiIsImdyaWQiLCJnZXRDb250YWluZXIiLCJwcmV2ZW50RGVmYXVsdCIsIiRidXR0b24iLCJjb25maXJtTWVzc2FnZSIsImxlbmd0aCIsImNvbmZpcm0iLCJtZXRob2QiLCJpc0dldE9yUG9zdE1ldGhvZCIsImluY2x1ZGVzIiwiJGZvcm0iLCJhcHBlbmRUbyIsImFwcGVuZCIsInN1Ym1pdCIsIkNvbHVtblRvZ2dsaW5nRXh0ZW5zaW9uIiwiJHRhYmxlIiwiX3RvZ2dsZVZhbHVlIiwicm93IiwidG9nZ2xlVXJsIiwiX3N1Ym1pdEFzRm9ybSIsIkZpbHRlcnNSZXNldEV4dGVuc2lvbiIsIlJlbG9hZExpc3RFeHRlbnNpb24iLCJnZXRIZWFkZXJDb250YWluZXIiLCJyZWxvYWQiLCJTb3J0aW5nRXh0ZW5zaW9uIiwiJHNvcnRhYmxlVGFibGUiLCJhdHRhY2giLCJHcmlkIiwiaWQiLCJleHRlbnNpb24iLCJleHRlbmQiLCJFeGNoYW5nZVJhdGVzVXBkYXRlU2NoZWR1bGVyIiwiX2luaXRFdmVudHMiLCJkb2N1bWVudCIsIl9pbml0TGl2ZUV4Y2hhbmdlUmF0ZSIsIiRsaXZlRXhjaGFuZ2VSYXRlc1N3aXRjaCIsImZvcm1JdGVtcyIsInNlcmlhbGl6ZSIsImFqYXgiLCJ0eXBlIiwiYXR0ciIsInJlc3BvbnNlIiwic3RhdHVzIiwic2hvd0Vycm9yTWVzc2FnZSIsIm1lc3NhZ2UiLCJfY2hhbmdlVGV4dEJ5Q3VycmVudFN3aXRjaFZhbHVlIiwidmFsIiwic2hvd1N1Y2Nlc3NNZXNzYWdlIiwiZmFpbCIsInJlc3BvbnNlSlNPTiIsInN3aXRjaFZhbHVlIiwidmFsdWVQYXJzZWQiLCJwYXJzZUludCIsInRvZ2dsZUNsYXNzIiwiY3VycmVuY3kiLCJhZGRFeHRlbnNpb24iLCJSZWxvYWRMaXN0QWN0aW9uRXh0ZW5zaW9uIiwiY2hvaWNlVHJlZSJdLCJtYXBwaW5ncyI6IjtBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0Esa0RBQTBDLGdDQUFnQztBQUMxRTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLGdFQUF3RCxrQkFBa0I7QUFDMUU7QUFDQSx5REFBaUQsY0FBYztBQUMvRDs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsaURBQXlDLGlDQUFpQztBQUMxRSx3SEFBZ0gsbUJBQW1CLEVBQUU7QUFDckk7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7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LElBQUl6Qyw0RkFBSixFQUF0QjtBQUNBd0MsVUFBUSxDQUFDQyxZQUFULENBQXNCLElBQUl4RCx5R0FBSixFQUF0QjtBQUVBLE1BQU0wRCxVQUFVLEdBQUcsSUFBSXhGLG9FQUFKLENBQWUsNEJBQWYsQ0FBbkI7QUFDQXdGLFlBQVUsQ0FBQy9FLHVCQUFYO0FBRUEsTUFBSXFELHFFQUFKO0FBQ0QsQ0FaQSxDQUFELEM7Ozs7Ozs7Ozs7O0FDcENBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLENBQUM7O0FBRUQ7QUFDQTtBQUNBO0FBQ0EsQ0FBQztBQUNEO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0EsNENBQTRDOztBQUU1QyIsImZpbGUiOiJjdXJyZW5jeS5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHsgZW51bWVyYWJsZTogdHJ1ZSwgZ2V0OiBnZXR0ZXIgfSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGRlZmluZSBfX2VzTW9kdWxlIG9uIGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uciA9IGZ1bmN0aW9uKGV4cG9ydHMpIHtcbiBcdFx0aWYodHlwZW9mIFN5bWJvbCAhPT0gJ3VuZGVmaW5lZCcgJiYgU3ltYm9sLnRvU3RyaW5nVGFnKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFN5bWJvbC50b1N0cmluZ1RhZywgeyB2YWx1ZTogJ01vZHVsZScgfSk7XG4gXHRcdH1cbiBcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsICdfX2VzTW9kdWxlJywgeyB2YWx1ZTogdHJ1ZSB9KTtcbiBcdH07XG5cbiBcdC8vIGNyZWF0ZSBhIGZha2UgbmFtZXNwYWNlIG9iamVjdFxuIFx0Ly8gbW9kZSAmIDE6IHZhbHVlIGlzIGEgbW9kdWxlIGlkLCByZXF1aXJlIGl0XG4gXHQvLyBtb2RlICYgMjogbWVyZ2UgYWxsIHByb3BlcnRpZXMgb2YgdmFsdWUgaW50byB0aGUgbnNcbiBcdC8vIG1vZGUgJiA0OiByZXR1cm4gdmFsdWUgd2hlbiBhbHJlYWR5IG5zIG9iamVjdFxuIFx0Ly8gbW9kZSAmIDh8MTogYmVoYXZlIGxpa2UgcmVxdWlyZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy50ID0gZnVuY3Rpb24odmFsdWUsIG1vZGUpIHtcbiBcdFx0aWYobW9kZSAmIDEpIHZhbHVlID0gX193ZWJwYWNrX3JlcXVpcmVfXyh2YWx1ZSk7XG4gXHRcdGlmKG1vZGUgJiA4KSByZXR1cm4gdmFsdWU7XG4gXHRcdGlmKChtb2RlICYgNCkgJiYgdHlwZW9mIHZhbHVlID09PSAnb2JqZWN0JyAmJiB2YWx1ZSAmJiB2YWx1ZS5fX2VzTW9kdWxlKSByZXR1cm4gdmFsdWU7XG4gXHRcdHZhciBucyA9IE9iamVjdC5jcmVhdGUobnVsbCk7XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18ucihucyk7XG4gXHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShucywgJ2RlZmF1bHQnLCB7IGVudW1lcmFibGU6IHRydWUsIHZhbHVlOiB2YWx1ZSB9KTtcbiBcdFx0aWYobW9kZSAmIDIgJiYgdHlwZW9mIHZhbHVlICE9ICdzdHJpbmcnKSBmb3IodmFyIGtleSBpbiB2YWx1ZSkgX193ZWJwYWNrX3JlcXVpcmVfXy5kKG5zLCBrZXksIGZ1bmN0aW9uKGtleSkgeyByZXR1cm4gdmFsdWVba2V5XTsgfS5iaW5kKG51bGwsIGtleSkpO1xuIFx0XHRyZXR1cm4gbnM7XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIi9hZG1pbi1kZXYvdGhlbWVzL25ldy10aGVtZS9wdWJsaWMvXCI7XG5cblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSBcIi4vanMvcGFnZXMvY3VycmVuY3kvaW5kZXguanNcIik7XG4iLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG4vKipcbiAqIFNlbmQgYSBQb3N0IFJlcXVlc3QgdG8gcmVzZXQgc2VhcmNoIEFjdGlvbi5cbiAqL1xuXG5jb25zdCAkID0gZ2xvYmFsLiQ7XG5cbmNvbnN0IGluaXQgPSBmdW5jdGlvbiByZXNldFNlYXJjaCh1cmwsIHJlZGlyZWN0VXJsKSB7XG4gICAgJC5wb3N0KHVybCkudGhlbigoKSA9PiB3aW5kb3cubG9jYXRpb24uYXNzaWduKHJlZGlyZWN0VXJsKSk7XG59O1xuXG5leHBvcnQgZGVmYXVsdCBpbml0O1xuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IGdsb2JhbC4kO1xuXG4vKipcbiAqIE1ha2VzIGEgdGFibGUgc29ydGFibGUgYnkgY29sdW1ucy5cbiAqIFRoaXMgZm9yY2VzIGEgcGFnZSByZWxvYWQgd2l0aCBtb3JlIHF1ZXJ5IHBhcmFtZXRlcnMuXG4gKi9cbmNsYXNzIFRhYmxlU29ydGluZyB7XG5cbiAgLyoqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSB0YWJsZVxuICAgKi9cbiAgY29uc3RydWN0b3IodGFibGUpIHtcbiAgICB0aGlzLnNlbGVjdG9yID0gJy5wcy1zb3J0YWJsZS1jb2x1bW4nO1xuICAgIHRoaXMuY29sdW1ucyA9ICQodGFibGUpLmZpbmQodGhpcy5zZWxlY3Rvcik7XG4gIH1cblxuICAvKipcbiAgICogQXR0YWNoZXMgdGhlIGxpc3RlbmVyc1xuICAgKi9cbiAgYXR0YWNoKCkge1xuICAgIHRoaXMuY29sdW1ucy5vbignY2xpY2snLCAoZSkgPT4ge1xuICAgICAgY29uc3QgJGNvbHVtbiA9ICQoZS5kZWxlZ2F0ZVRhcmdldCk7XG4gICAgICB0aGlzLl9zb3J0QnlDb2x1bW4oJGNvbHVtbiwgdGhpcy5fZ2V0VG9nZ2xlZFNvcnREaXJlY3Rpb24oJGNvbHVtbikpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIFNvcnQgdXNpbmcgYSBjb2x1bW4gbmFtZVxuICAgKiBAcGFyYW0ge3N0cmluZ30gY29sdW1uTmFtZVxuICAgKiBAcGFyYW0ge3N0cmluZ30gZGlyZWN0aW9uIFwiYXNjXCIgb3IgXCJkZXNjXCJcbiAgICovXG4gIHNvcnRCeShjb2x1bW5OYW1lLCBkaXJlY3Rpb24pIHtcbiAgICBjb25zdCAkY29sdW1uID0gdGhpcy5jb2x1bW5zLmlzKGBbZGF0YS1zb3J0LWNvbC1uYW1lPVwiJHtjb2x1bW5OYW1lfVwiXWApO1xuICAgIGlmICghJGNvbHVtbikge1xuICAgICAgdGhyb3cgbmV3IEVycm9yKGBDYW5ub3Qgc29ydCBieSBcIiR7Y29sdW1uTmFtZX1cIjogaW52YWxpZCBjb2x1bW5gKTtcbiAgICB9XG5cbiAgICB0aGlzLl9zb3J0QnlDb2x1bW4oJGNvbHVtbiwgZGlyZWN0aW9uKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTb3J0IHVzaW5nIGEgY29sdW1uIGVsZW1lbnRcbiAgICogQHBhcmFtIHtqUXVlcnl9IGNvbHVtblxuICAgKiBAcGFyYW0ge3N0cmluZ30gZGlyZWN0aW9uIFwiYXNjXCIgb3IgXCJkZXNjXCJcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zb3J0QnlDb2x1bW4oY29sdW1uLCBkaXJlY3Rpb24pIHtcbiAgICB3aW5kb3cubG9jYXRpb24gPSB0aGlzLl9nZXRVcmwoY29sdW1uLmRhdGEoJ3NvcnRDb2xOYW1lJyksIChkaXJlY3Rpb24gPT09ICdkZXNjJykgPyAnZGVzYycgOiAnYXNjJyk7XG4gIH1cblxuICAvKipcbiAgICogUmV0dXJucyB0aGUgaW52ZXJ0ZWQgZGlyZWN0aW9uIHRvIHNvcnQgYWNjb3JkaW5nIHRvIHRoZSBjb2x1bW4ncyBjdXJyZW50IG9uZVxuICAgKiBAcGFyYW0ge2pRdWVyeX0gY29sdW1uXG4gICAqIEByZXR1cm4ge3N0cmluZ31cbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9nZXRUb2dnbGVkU29ydERpcmVjdGlvbihjb2x1bW4pIHtcbiAgICByZXR1cm4gY29sdW1uLmRhdGEoJ3NvcnREaXJlY3Rpb24nKSA9PT0gJ2FzYycgPyAnZGVzYycgOiAnYXNjJztcbiAgfVxuXG4gIC8qKlxuICAgKiBSZXR1cm5zIHRoZSB1cmwgZm9yIHRoZSBzb3J0ZWQgdGFibGVcbiAgICogQHBhcmFtIHtzdHJpbmd9IGNvbE5hbWVcbiAgICogQHBhcmFtIHtzdHJpbmd9IGRpcmVjdGlvblxuICAgKiBAcmV0dXJuIHtzdHJpbmd9XG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfZ2V0VXJsKGNvbE5hbWUsIGRpcmVjdGlvbikge1xuICAgIGNvbnN0IHVybCA9IG5ldyBVUkwod2luZG93LmxvY2F0aW9uLmhyZWYpO1xuICAgIGNvbnN0IHBhcmFtcyA9IHVybC5zZWFyY2hQYXJhbXM7XG5cbiAgICBwYXJhbXMuc2V0KCdvcmRlckJ5JywgY29sTmFtZSk7XG4gICAgcGFyYW1zLnNldCgnc29ydE9yZGVyJywgZGlyZWN0aW9uKTtcblxuICAgIHJldHVybiB1cmwudG9TdHJpbmcoKTtcbiAgfVxufVxuXG5leHBvcnQgZGVmYXVsdCBUYWJsZVNvcnRpbmc7XG4iLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogSGFuZGxlcyBVSSBpbnRlcmFjdGlvbnMgb2YgY2hvaWNlIHRyZWVcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQ2hvaWNlVHJlZSB7XG4gIC8qKlxuICAgKiBAcGFyYW0ge1N0cmluZ30gdHJlZVNlbGVjdG9yXG4gICAqL1xuICBjb25zdHJ1Y3Rvcih0cmVlU2VsZWN0b3IpIHtcbiAgICB0aGlzLiRjb250YWluZXIgPSAkKHRyZWVTZWxlY3Rvcik7XG5cbiAgICB0aGlzLiRjb250YWluZXIub24oJ2NsaWNrJywgJy5qcy1pbnB1dC13cmFwcGVyJywgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCAkaW5wdXRXcmFwcGVyID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcblxuICAgICAgdGhpcy5fdG9nZ2xlQ2hpbGRUcmVlKCRpbnB1dFdyYXBwZXIpO1xuICAgIH0pO1xuXG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjbGljaycsICcuanMtdG9nZ2xlLWNob2ljZS10cmVlLWFjdGlvbicsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgJGFjdGlvbiA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG5cbiAgICAgIHRoaXMuX3RvZ2dsZVRyZWUoJGFjdGlvbik7XG4gICAgfSk7XG5cbiAgICByZXR1cm4ge1xuICAgICAgZW5hYmxlQXV0b0NoZWNrQ2hpbGRyZW46ICgpID0+IHRoaXMuZW5hYmxlQXV0b0NoZWNrQ2hpbGRyZW4oKSxcbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqIEVuYWJsZSBhdXRvbWF0aWMgY2hlY2svdW5jaGVjayBvZiBjbGlja2VkIGl0ZW0ncyBjaGlsZHJlbi5cbiAgICovXG4gIGVuYWJsZUF1dG9DaGVja0NoaWxkcmVuKCkge1xuICAgIHRoaXMuJGNvbnRhaW5lci5vbignY2hhbmdlJywgJ2lucHV0W3R5cGU9XCJjaGVja2JveFwiXScsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgJGNsaWNrZWRDaGVja2JveCA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgICBjb25zdCAkaXRlbVdpdGhDaGlsZHJlbiA9ICRjbGlja2VkQ2hlY2tib3guY2xvc2VzdCgnbGknKTtcblxuICAgICAgJGl0ZW1XaXRoQ2hpbGRyZW5cbiAgICAgICAgLmZpbmQoJ3VsIGlucHV0W3R5cGU9XCJjaGVja2JveFwiXScpXG4gICAgICAgIC5wcm9wKCdjaGVja2VkJywgJGNsaWNrZWRDaGVja2JveC5pcygnOmNoZWNrZWQnKSk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQ29sbGFwc2Ugb3IgZXhwYW5kIHN1Yi10cmVlIGZvciBzaW5nbGUgcGFyZW50XG4gICAqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkaW5wdXRXcmFwcGVyXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfdG9nZ2xlQ2hpbGRUcmVlKCRpbnB1dFdyYXBwZXIpIHtcbiAgICBjb25zdCAkcGFyZW50V3JhcHBlciA9ICRpbnB1dFdyYXBwZXIuY2xvc2VzdCgnbGknKTtcblxuICAgIGlmICgkcGFyZW50V3JhcHBlci5oYXNDbGFzcygnZXhwYW5kZWQnKSkge1xuICAgICAgJHBhcmVudFdyYXBwZXJcbiAgICAgICAgLnJlbW92ZUNsYXNzKCdleHBhbmRlZCcpXG4gICAgICAgIC5hZGRDbGFzcygnY29sbGFwc2VkJyk7XG5cbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBpZiAoJHBhcmVudFdyYXBwZXIuaGFzQ2xhc3MoJ2NvbGxhcHNlZCcpKSB7XG4gICAgICAkcGFyZW50V3JhcHBlclxuICAgICAgICAucmVtb3ZlQ2xhc3MoJ2NvbGxhcHNlZCcpXG4gICAgICAgIC5hZGRDbGFzcygnZXhwYW5kZWQnKTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogQ29sbGFwc2Ugb3IgZXhwYW5kIHdob2xlIHRyZWVcbiAgICpcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRhY3Rpb25cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF90b2dnbGVUcmVlKCRhY3Rpb24pIHtcbiAgICBjb25zdCAkcGFyZW50Q29udGFpbmVyID0gJGFjdGlvbi5jbG9zZXN0KCcuanMtY2hvaWNlLXRyZWUtY29udGFpbmVyJyk7XG4gICAgY29uc3QgYWN0aW9uID0gJGFjdGlvbi5kYXRhKCdhY3Rpb24nKTtcblxuICAgIC8vIHRvZ2dsZSBhY3Rpb24gY29uZmlndXJhdGlvblxuICAgIGNvbnN0IGNvbmZpZyA9IHtcbiAgICAgIGFkZENsYXNzOiB7XG4gICAgICAgIGV4cGFuZDogJ2V4cGFuZGVkJyxcbiAgICAgICAgY29sbGFwc2U6ICdjb2xsYXBzZWQnLFxuICAgICAgfSxcbiAgICAgIHJlbW92ZUNsYXNzOiB7XG4gICAgICAgIGV4cGFuZDogJ2NvbGxhcHNlZCcsXG4gICAgICAgIGNvbGxhcHNlOiAnZXhwYW5kZWQnLFxuICAgICAgfSxcbiAgICAgIG5leHRBY3Rpb246IHtcbiAgICAgICAgZXhwYW5kOiAnY29sbGFwc2UnLFxuICAgICAgICBjb2xsYXBzZTogJ2V4cGFuZCcsXG4gICAgICB9LFxuICAgICAgdGV4dDoge1xuICAgICAgICBleHBhbmQ6ICdjb2xsYXBzZWQtdGV4dCcsXG4gICAgICAgIGNvbGxhcHNlOiAnZXhwYW5kZWQtdGV4dCcsXG4gICAgICB9LFxuICAgICAgaWNvbjoge1xuICAgICAgICBleHBhbmQ6ICdjb2xsYXBzZWQtaWNvbicsXG4gICAgICAgIGNvbGxhcHNlOiAnZXhwYW5kZWQtaWNvbicsXG4gICAgICB9XG4gICAgfTtcblxuICAgICRwYXJlbnRDb250YWluZXIuZmluZCgnbGknKS5lYWNoKChpbmRleCwgaXRlbSkgPT4ge1xuICAgICAgY29uc3QgJGl0ZW0gPSAkKGl0ZW0pO1xuXG4gICAgICBpZiAoJGl0ZW0uaGFzQ2xhc3MoY29uZmlnLnJlbW92ZUNsYXNzW2FjdGlvbl0pKSB7XG4gICAgICAgICAgJGl0ZW0ucmVtb3ZlQ2xhc3MoY29uZmlnLnJlbW92ZUNsYXNzW2FjdGlvbl0pXG4gICAgICAgICAgICAuYWRkQ2xhc3MoY29uZmlnLmFkZENsYXNzW2FjdGlvbl0pO1xuICAgICAgfVxuICAgIH0pO1xuXG4gICAgJGFjdGlvbi5kYXRhKCdhY3Rpb24nLCBjb25maWcubmV4dEFjdGlvblthY3Rpb25dKTtcbiAgICAkYWN0aW9uLmZpbmQoJy5tYXRlcmlhbC1pY29ucycpLnRleHQoJGFjdGlvbi5kYXRhKGNvbmZpZy5pY29uW2FjdGlvbl0pKTtcbiAgICAkYWN0aW9uLmZpbmQoJy5qcy10b2dnbGUtdGV4dCcpLnRleHQoJGFjdGlvbi5kYXRhKGNvbmZpZy50ZXh0W2FjdGlvbl0pKTtcbiAgfVxufVxuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIFN1Ym1pdFJvd0FjdGlvbkV4dGVuc2lvbiBoYW5kbGVzIHN1Ym1pdHRpbmcgb2Ygcm93IGFjdGlvblxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBTdWJtaXRSb3dBY3Rpb25FeHRlbnNpb24ge1xuICAvKipcbiAgICogRXh0ZW5kIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkub24oJ2NsaWNrJywgJy5qcy1zdWJtaXQtcm93LWFjdGlvbicsIChldmVudCkgPT4ge1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgY29uc3QgJGJ1dHRvbiA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgICBjb25zdCBjb25maXJtTWVzc2FnZSA9ICRidXR0b24uZGF0YSgnY29uZmlybS1tZXNzYWdlJyk7XG5cbiAgICAgIGlmIChjb25maXJtTWVzc2FnZS5sZW5ndGggJiYgIWNvbmZpcm0oY29uZmlybU1lc3NhZ2UpKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cblxuICAgICAgY29uc3QgbWV0aG9kID0gJGJ1dHRvbi5kYXRhKCdtZXRob2QnKTtcbiAgICAgIGNvbnN0IGlzR2V0T3JQb3N0TWV0aG9kID0gWydHRVQnLCAnUE9TVCddLmluY2x1ZGVzKG1ldGhvZCk7XG5cbiAgICAgIGNvbnN0ICRmb3JtID0gJCgnPGZvcm0+Jywge1xuICAgICAgICAnYWN0aW9uJzogJGJ1dHRvbi5kYXRhKCd1cmwnKSxcbiAgICAgICAgJ21ldGhvZCc6IGlzR2V0T3JQb3N0TWV0aG9kID8gbWV0aG9kIDogJ1BPU1QnLFxuICAgICAgfSkuYXBwZW5kVG8oJ2JvZHknKTtcblxuICAgICAgaWYgKCFpc0dldE9yUG9zdE1ldGhvZCkge1xuICAgICAgICAkZm9ybS5hcHBlbmQoJCgnPGlucHV0PicsIHtcbiAgICAgICAgICAndHlwZSc6ICdfaGlkZGVuJyxcbiAgICAgICAgICAnbmFtZSc6ICdfbWV0aG9kJyxcbiAgICAgICAgICAndmFsdWUnOiBtZXRob2RcbiAgICAgICAgfSkpO1xuICAgICAgfVxuXG4gICAgICAkZm9ybS5zdWJtaXQoKTtcbiAgICB9KTtcbiAgfVxufVxuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IGdsb2JhbC4kO1xuXG4vKipcbiAqIENsYXNzIFJlbG9hZExpc3RFeHRlbnNpb24gZXh0ZW5kcyBncmlkIHdpdGggXCJDb2x1bW4gdG9nZ2xpbmdcIiBmZWF0dXJlXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIENvbHVtblRvZ2dsaW5nRXh0ZW5zaW9uIHtcblxuICAvKipcbiAgICogRXh0ZW5kIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIGNvbnN0ICR0YWJsZSA9IGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgndGFibGUudGFibGUnKTtcbiAgICAkdGFibGUuZmluZCgnLnBzLXRvZ2dsYWJsZS1yb3cnKS5vbignY2xpY2snLCAoZSkgPT4ge1xuICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgdGhpcy5fdG9nZ2xlVmFsdWUoJChlLmRlbGVnYXRlVGFyZ2V0KSk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQHBhcmFtIHtqUXVlcnl9IHJvd1xuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3RvZ2dsZVZhbHVlKHJvdykge1xuICAgIGNvbnN0IHRvZ2dsZVVybCA9IHJvdy5kYXRhKCd0b2dnbGVVcmwnKTtcblxuICAgIHRoaXMuX3N1Ym1pdEFzRm9ybSh0b2dnbGVVcmwpO1xuICB9XG5cbiAgLyoqXG4gICAqIFN1Ym1pdHMgcmVxdWVzdCB1cmwgYXMgZm9ybVxuICAgKlxuICAgKiBAcGFyYW0ge3N0cmluZ30gdG9nZ2xlVXJsXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc3VibWl0QXNGb3JtKHRvZ2dsZVVybCkge1xuICAgIGNvbnN0ICRmb3JtID0gJCgnPGZvcm0+Jywge1xuICAgICAgYWN0aW9uOiB0b2dnbGVVcmwsXG4gICAgICBtZXRob2Q6ICdQT1NUJyxcbiAgICB9KS5hcHBlbmRUbygnYm9keScpO1xuXG4gICAgJGZvcm0uc3VibWl0KCk7XG4gIH1cbn1cbiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCByZXNldFNlYXJjaCBmcm9tICcuLi8uLi8uLi9hcHAvdXRpbHMvcmVzZXRfc2VhcmNoJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIEZpbHRlcnNSZXNldEV4dGVuc2lvbiBleHRlbmRzIGdyaWQgd2l0aCBmaWx0ZXJzIHJlc2V0dGluZ1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBGaWx0ZXJzUmVzZXRFeHRlbnNpb24ge1xuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLXJlc2V0LXNlYXJjaCcsIChldmVudCkgPT4ge1xuICAgICAgcmVzZXRTZWFyY2goJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCd1cmwnKSwgJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdyZWRpcmVjdCcpKTtcbiAgICB9KTtcbiAgfVxufVxuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuLyoqXG4gKiBDbGFzcyBSZWxvYWRMaXN0RXh0ZW5zaW9uIGV4dGVuZHMgZ3JpZCB3aXRoIFwiTGlzdCByZWxvYWRcIiBhY3Rpb25cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgUmVsb2FkTGlzdEV4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRIZWFkZXJDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLWNvbW1vbl9yZWZyZXNoX2xpc3QtZ3JpZC1hY3Rpb24nLCAoKSA9PiB7XG4gICAgICBsb2NhdGlvbi5yZWxvYWQoKTtcbiAgICB9KTtcbiAgfVxufVxuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IFRhYmxlU29ydGluZyBmcm9tICcuLi8uLi8uLi9hcHAvdXRpbHMvdGFibGUtc29ydGluZyc7XG5cbi8qKlxuICogQ2xhc3MgUmVsb2FkTGlzdEV4dGVuc2lvbiBleHRlbmRzIGdyaWQgd2l0aCBcIkxpc3QgcmVsb2FkXCIgYWN0aW9uXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFNvcnRpbmdFeHRlbnNpb24ge1xuICAvKipcbiAgICogRXh0ZW5kIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIGNvbnN0ICRzb3J0YWJsZVRhYmxlID0gZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCd0YWJsZS50YWJsZScpO1xuXG4gICAgbmV3IFRhYmxlU29ydGluZygkc29ydGFibGVUYWJsZSkuYXR0YWNoKCk7XG4gIH1cbn1cbiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBpcyByZXNwb25zaWJsZSBmb3IgaGFuZGxpbmcgR3JpZCBldmVudHNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgR3JpZCB7XG4gIC8qKlxuICAgKiBHcmlkIGlkXG4gICAqXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBpZFxuICAgKi9cbiAgY29uc3RydWN0b3IoaWQpIHtcbiAgICB0aGlzLmlkID0gaWQ7XG4gICAgdGhpcy4kY29udGFpbmVyID0gJCgnIycgKyB0aGlzLmlkICsgJ19ncmlkJyk7XG4gIH1cblxuICAvKipcbiAgICogR2V0IGdyaWQgaWRcbiAgICpcbiAgICogQHJldHVybnMge3N0cmluZ31cbiAgICovXG4gIGdldElkKCkge1xuICAgIHJldHVybiB0aGlzLmlkO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBncmlkIGNvbnRhaW5lclxuICAgKlxuICAgKiBAcmV0dXJucyB7alF1ZXJ5fVxuICAgKi9cbiAgZ2V0Q29udGFpbmVyKCkge1xuICAgIHJldHVybiB0aGlzLiRjb250YWluZXI7XG4gIH1cblxuICAvKipcbiAgICogR2V0IGdyaWQgaGVhZGVyIGNvbnRhaW5lclxuICAgKlxuICAgKiBAcmV0dXJucyB7alF1ZXJ5fVxuICAgKi9cbiAgZ2V0SGVhZGVyQ29udGFpbmVyKCkge1xuICAgIHJldHVybiB0aGlzLiRjb250YWluZXIuY2xvc2VzdCgnLmpzLWdyaWQtcGFuZWwnKS5maW5kKCcuanMtZ3JpZC1oZWFkZXInKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZCB3aXRoIGV4dGVybmFsIGV4dGVuc2lvbnNcbiAgICpcbiAgICogQHBhcmFtIHtvYmplY3R9IGV4dGVuc2lvblxuICAgKi9cbiAgYWRkRXh0ZW5zaW9uKGV4dGVuc2lvbikge1xuICAgIGV4dGVuc2lvbi5leHRlbmQodGhpcyk7XG4gIH1cbn1cbiIsIi8qKlxuICogMjAwNy0yMDE4IFByZXN0YVNob3BcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHA6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTggUHJlc3RhU2hvcCBTQVxuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogVGhpcyBjbGFzcyB0cmlnZ2VycyBldmVudHMgcmVxdWlyZWQgZm9yIHR1cm5pbmcgb24gb3Igb2ZmIGV4Y2hhbmdlIHJhdGVzIHNjaGVkdWxlciBhbiBkaXNwbGF5aW5nIHRoZSByaWdodCB0ZXh0XG4gKiBiZWxvdyB0aGUgc3dpdGNoLlxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBFeGNoYW5nZVJhdGVzVXBkYXRlU2NoZWR1bGVyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICB0aGlzLl9pbml0RXZlbnRzKCk7XG5cbiAgIHJldHVybiB7fTtcbiAgfVxuXG4gIF9pbml0RXZlbnRzKCkge1xuICAgICQoZG9jdW1lbnQpLm9uKCdjaGFuZ2UnLCAnLmpzLWxpdmUtZXhjaGFuZ2UtcmF0ZScsIChldmVudCkgPT4gdGhpcy5faW5pdExpdmVFeGNoYW5nZVJhdGUoZXZlbnQpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcGFyYW0ge09iamVjdH0gZXZlbnRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9pbml0TGl2ZUV4Y2hhbmdlUmF0ZShldmVudCkge1xuICAgIGNvbnN0ICRsaXZlRXhjaGFuZ2VSYXRlc1N3aXRjaCA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgY29uc3QgJGZvcm0gPSAkbGl2ZUV4Y2hhbmdlUmF0ZXNTd2l0Y2guY2xvc2VzdCgnZm9ybScpO1xuICAgIGNvbnN0IGZvcm1JdGVtcyA9ICRmb3JtLnNlcmlhbGl6ZSgpO1xuXG4gICAgJC5hamF4KHtcbiAgICAgIHR5cGU6ICdQT1NUJyxcbiAgICAgIHVybDogJGxpdmVFeGNoYW5nZVJhdGVzU3dpdGNoLmF0dHIoJ2RhdGEtdXJsJyksXG4gICAgICBkYXRhOiBmb3JtSXRlbXMsXG4gICAgfSlcbiAgICAgIC50aGVuKChyZXNwb25zZSkgPT4ge1xuICAgICAgICBpZiAoIXJlc3BvbnNlLnN0YXR1cykge1xuICAgICAgICAgIHNob3dFcnJvck1lc3NhZ2UocmVzcG9uc2UubWVzc2FnZSk7XG4gICAgICAgICAgdGhpcy5fY2hhbmdlVGV4dEJ5Q3VycmVudFN3aXRjaFZhbHVlKCRsaXZlRXhjaGFuZ2VSYXRlc1N3aXRjaC52YWwoKSk7XG5cbiAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBzaG93U3VjY2Vzc01lc3NhZ2UocmVzcG9uc2UubWVzc2FnZSk7XG4gICAgICAgIHRoaXMuX2NoYW5nZVRleHRCeUN1cnJlbnRTd2l0Y2hWYWx1ZSgkbGl2ZUV4Y2hhbmdlUmF0ZXNTd2l0Y2gudmFsKCkpO1xuICAgICAgfVxuICAgICkuZmFpbCgocmVzcG9uc2UpID0+IHtcbiAgICAgIGlmICh0eXBlb2YgcmVzcG9uc2UucmVzcG9uc2VKU09OICE9PSAndW5kZWZpbmVkJykge1xuICAgICAgICBzaG93RXJyb3JNZXNzYWdlKHJlc3BvbnNlLnJlc3BvbnNlSlNPTi5tZXNzYWdlKTtcbiAgICAgICAgdGhpcy5fY2hhbmdlVGV4dEJ5Q3VycmVudFN3aXRjaFZhbHVlKCRsaXZlRXhjaGFuZ2VSYXRlc1N3aXRjaC52YWwoKSk7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cblxuICBfY2hhbmdlVGV4dEJ5Q3VycmVudFN3aXRjaFZhbHVlKHN3aXRjaFZhbHVlKSB7XG4gICAgY29uc3QgdmFsdWVQYXJzZWQgPSBwYXJzZUludChzd2l0Y2hWYWx1ZSk7XG4gICAgJCgnLmpzLWV4Y2hhbmdlLXJhdGUtdGV4dC13aGVuLWRpc2FibGVkJykudG9nZ2xlQ2xhc3MoJ2Qtbm9uZScsIDAgIT09IHZhbHVlUGFyc2VkKTtcbiAgICAkKCcuanMtZXhjaGFuZ2UtcmF0ZS10ZXh0LXdoZW4tZW5hYmxlZCcpLnRvZ2dsZUNsYXNzKCdkLW5vbmUnLCAxICE9PSB2YWx1ZVBhcnNlZCk7XG4gIH1cbn1cbiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBHcmlkIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvZ3JpZC9ncmlkJztcbmltcG9ydCBTb3J0aW5nRXh0ZW5zaW9uIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vc29ydGluZy1leHRlbnNpb24nO1xuaW1wb3J0IEZpbHRlcnNSZXNldEV4dGVuc2lvbiBmcm9tICcuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2ZpbHRlcnMtcmVzZXQtZXh0ZW5zaW9uJztcbmltcG9ydCBSZWxvYWRMaXN0QWN0aW9uRXh0ZW5zaW9uIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vcmVsb2FkLWxpc3QtZXh0ZW5zaW9uJztcbmltcG9ydCBDb2x1bW5Ub2dnbGluZ0V4dGVuc2lvbiBmcm9tICcuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2NvbHVtbi10b2dnbGluZy1leHRlbnNpb24nO1xuaW1wb3J0IFN1Ym1pdFJvd0FjdGlvbkV4dGVuc2lvbiBmcm9tICcuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2FjdGlvbi9yb3cvc3VibWl0LXJvdy1hY3Rpb24tZXh0ZW5zaW9uJztcbmltcG9ydCBDaG9pY2VUcmVlIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvZm9ybS9jaG9pY2UtdHJlZSc7XG5pbXBvcnQgRXhjaGFuZ2VSYXRlc1VwZGF0ZVNjaGVkdWxlciBmcm9tICcuL0V4Y2hhbmdlUmF0ZXNVcGRhdGVTY2hlZHVsZXInO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbiQoKCkgPT4ge1xuICBjb25zdCBjdXJyZW5jeSA9IG5ldyBHcmlkKCdjdXJyZW5jeScpO1xuICBjdXJyZW5jeS5hZGRFeHRlbnNpb24obmV3IFNvcnRpbmdFeHRlbnNpb24oKSk7XG4gIGN1cnJlbmN5LmFkZEV4dGVuc2lvbihuZXcgRmlsdGVyc1Jlc2V0RXh0ZW5zaW9uKCkpO1xuICBjdXJyZW5jeS5hZGRFeHRlbnNpb24obmV3IFJlbG9hZExpc3RBY3Rpb25FeHRlbnNpb24oKSk7XG4gIGN1cnJlbmN5LmFkZEV4dGVuc2lvbihuZXcgQ29sdW1uVG9nZ2xpbmdFeHRlbnNpb24oKSk7XG4gIGN1cnJlbmN5LmFkZEV4dGVuc2lvbihuZXcgU3VibWl0Um93QWN0aW9uRXh0ZW5zaW9uKCkpO1xuXG4gIGNvbnN0IGNob2ljZVRyZWUgPSBuZXcgQ2hvaWNlVHJlZSgnI2N1cnJlbmN5X3Nob3BfYXNzb2NpYXRpb24nKTtcbiAgY2hvaWNlVHJlZS5lbmFibGVBdXRvQ2hlY2tDaGlsZHJlbigpO1xuXG4gIG5ldyBFeGNoYW5nZVJhdGVzVXBkYXRlU2NoZWR1bGVyKCk7XG59KTtcbiIsInZhciBnO1xuXG4vLyBUaGlzIHdvcmtzIGluIG5vbi1zdHJpY3QgbW9kZVxuZyA9IChmdW5jdGlvbigpIHtcblx0cmV0dXJuIHRoaXM7XG59KSgpO1xuXG50cnkge1xuXHQvLyBUaGlzIHdvcmtzIGlmIGV2YWwgaXMgYWxsb3dlZCAoc2VlIENTUClcblx0ZyA9IGcgfHwgbmV3IEZ1bmN0aW9uKFwicmV0dXJuIHRoaXNcIikoKTtcbn0gY2F0Y2ggKGUpIHtcblx0Ly8gVGhpcyB3b3JrcyBpZiB0aGUgd2luZG93IHJlZmVyZW5jZSBpcyBhdmFpbGFibGVcblx0aWYgKHR5cGVvZiB3aW5kb3cgPT09IFwib2JqZWN0XCIpIGcgPSB3aW5kb3c7XG59XG5cbi8vIGcgY2FuIHN0aWxsIGJlIHVuZGVmaW5lZCwgYnV0IG5vdGhpbmcgdG8gZG8gYWJvdXQgaXQuLi5cbi8vIFdlIHJldHVybiB1bmRlZmluZWQsIGluc3RlYWQgb2Ygbm90aGluZyBoZXJlLCBzbyBpdCdzXG4vLyBlYXNpZXIgdG8gaGFuZGxlIHRoaXMgY2FzZS4gaWYoIWdsb2JhbCkgeyAuLi59XG5cbm1vZHVsZS5leHBvcnRzID0gZztcbiJdLCJzb3VyY2VSb290IjoiIn0=