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
/******/ 	return __webpack_require__(__webpack_require__.s = "./js/pages/cms-page/index.js");
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

/***/ "./js/components/grid/extension/bulk-action-checkbox-extension.js":
/*!************************************************************************!*\
  !*** ./js/components/grid/extension/bulk-action-checkbox-extension.js ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return BulkActionCheckboxExtension; });
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
 * Class BulkActionSelectCheckboxExtension
 */

var BulkActionCheckboxExtension =
/*#__PURE__*/
function () {
  function BulkActionCheckboxExtension() {
    _classCallCheck(this, BulkActionCheckboxExtension);
  }

  _createClass(BulkActionCheckboxExtension, [{
    key: "extend",

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
    key: "_handleBulkActionSelectAllCheckbox",
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
    key: "_handleBulkActionCheckboxSelect",
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
    key: "_enableBulkActionsBtn",
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
    key: "_disableBulkActionsBtn",
    value: function _disableBulkActionsBtn(grid) {
      grid.getContainer().find('.js-bulk-actions-btn').prop('disabled', true);
    }
  }]);

  return BulkActionCheckboxExtension;
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

/***/ "./js/components/grid/extension/export-to-sql-manager-extension.js":
/*!*************************************************************************!*\
  !*** ./js/components/grid/extension/export-to-sql-manager-extension.js ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return ExportToSqlManagerExtension; });
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
 * Class ExportToSqlManagerExtension extends grid with exporting query to SQL Manager
 */

var ExportToSqlManagerExtension =
/*#__PURE__*/
function () {
  function ExportToSqlManagerExtension() {
    _classCallCheck(this, ExportToSqlManagerExtension);
  }

  _createClass(ExportToSqlManagerExtension, [{
    key: "extend",

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
    key: "_onShowSqlQueryClick",
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
    key: "_onExportSqlManagerClick",
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
    key: "_fillExportForm",
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
    key: "_getNameFromBreadcrumb",
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

/***/ "./js/components/grid/extension/link-row-action-extension.js":
/*!*******************************************************************!*\
  !*** ./js/components/grid/extension/link-row-action-extension.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return LinkRowActionExtension; });
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
 * Class LinkRowActionExtension handles link row actions
 */

var LinkRowActionExtension =
/*#__PURE__*/
function () {
  function LinkRowActionExtension() {
    _classCallCheck(this, LinkRowActionExtension);
  }

  _createClass(LinkRowActionExtension, [{
    key: "extend",

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      grid.getContainer().on('click', '.js-link-row-action', function (event) {
        var confirmMessage = $(event.currentTarget).data('confirm-message');

        if (confirmMessage.length && !confirm(confirmMessage)) {
          event.preventDefault();
        }
      });
    }
  }]);

  return LinkRowActionExtension;
}();



/***/ }),

/***/ "./js/components/grid/extension/position-extension.js":
/*!************************************************************!*\
  !*** ./js/components/grid/extension/position-extension.js ***!
  \************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return PositionExtension; });
/* harmony import */ var tablednd_dist_jquery_tablednd_min__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tablednd/dist/jquery.tablednd.min */ "./node_modules/tablednd/dist/jquery.tablednd.min.js");
/* harmony import */ var tablednd_dist_jquery_tablednd_min__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tablednd_dist_jquery_tablednd_min__WEBPACK_IMPORTED_MODULE_0__);
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
 * Class PositionExtension extends Grid with reorderable positions
 */

var PositionExtension =
/*#__PURE__*/
function () {
  function PositionExtension() {
    var _this = this;

    _classCallCheck(this, PositionExtension);

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


  _createClass(PositionExtension, [{
    key: "extend",
    value: function extend(grid) {
      var _this2 = this;

      this.grid = grid;

      this._addIdsToGridTableRows();

      grid.getContainer().find('.js-grid-table').tableDnD({
        onDragClass: 'position-row-while-drag',
        dragHandle: '.js-drag-handle',
        onDrop: function onDrop(table, row) {
          return _this2._handlePositionChange(row);
        }
      });
      grid.getContainer().find('.js-drag-handle').hover(function () {
        $(this).closest('tr').addClass('hover');
      }, function () {
        $(this).closest('tr').removeClass('hover');
      });
    }
    /**
     * When position is changed handle update
     *
     * @param {HTMLElement} row
     *
     * @private
     */

  }, {
    key: "_handlePositionChange",
    value: function _handlePositionChange(row) {
      var $rowPositionContainer = $(row).find('.js-' + this.grid.getId() + '-position:first');
      var updateUrl = $rowPositionContainer.data('update-url');
      var method = $rowPositionContainer.data('update-method');
      var paginationOffset = parseInt($rowPositionContainer.data('pagination-offset'), 10);

      var positions = this._getRowsPositions(paginationOffset);

      var params = {
        positions: positions
      };

      this._updatePosition(updateUrl, params, method);
    }
    /**
     * Returns the current table positions
     * @returns {Array}
     * @private
     */

  }, {
    key: "_getRowsPositions",
    value: function _getRowsPositions(paginationOffset) {
      var tableData = JSON.parse($.tableDnD.jsonize());
      var rowsData = tableData[this.grid.getId() + '_grid_table'];
      var regex = /^row_(\d+)_(\d+)$/;
      var rowsNb = rowsData.length;
      var positions = [];
      var rowData, i;

      for (i = 0; i < rowsNb; ++i) {
        rowData = regex.exec(rowsData[i]);
        positions.push({
          rowId: rowData[1],
          newPosition: paginationOffset + i,
          oldPosition: parseInt(rowData[2], 10)
        });
      }

      return positions;
    }
    /**
     * Add ID's to Grid table rows to make tableDnD.onDrop() function work.
     *
     * @private
     */

  }, {
    key: "_addIdsToGridTableRows",
    value: function _addIdsToGridTableRows() {
      this.grid.getContainer().find('.js-grid-table .js-' + this.grid.getId() + '-position').each(function (index, positionWrapper) {
        var $positionWrapper = $(positionWrapper);
        var rowId = $positionWrapper.data('id');
        var position = $positionWrapper.data('position');
        var id = "row_".concat(rowId, "_").concat(position);
        $positionWrapper.closest('tr').attr('id', id);
        $positionWrapper.closest('td').addClass('js-drag-handle');
      });
    }
    /**
     * Process rows positions update
     *
     * @param {String} url
     * @param {Object} params
     * @param {String} method
     *
     * @private
     */

  }, {
    key: "_updatePosition",
    value: function _updatePosition(url, params, method) {
      var isGetOrPostMethod = ['GET', 'POST'].includes(method);
      var $form = $('<form>', {
        'action': url,
        'method': isGetOrPostMethod ? method : 'POST'
      }).appendTo('body');
      var positionsNb = params.positions.length;
      var position;

      for (var i = 0; i < positionsNb; ++i) {
        position = params.positions[i];
        $form.append($('<input>', {
          'type': 'hidden',
          'name': 'positions[' + i + '][rowId]',
          'value': position.rowId
        }), $('<input>', {
          'type': 'hidden',
          'name': 'positions[' + i + '][oldPosition]',
          'value': position.oldPosition
        }), $('<input>', {
          'type': 'hidden',
          'name': 'positions[' + i + '][newPosition]',
          'value': position.newPosition
        }));
      } // This _method param is used by Symfony to simulate DELETE and PUT methods


      if (!isGetOrPostMethod) {
        $form.append($('<input>', {
          'type': 'hidden',
          'name': '_method',
          'value': method
        }));
      }

      $form.submit();
    }
  }]);

  return PositionExtension;
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

/***/ "./js/components/grid/extension/submit-bulk-action-extension.js":
/*!**********************************************************************!*\
  !*** ./js/components/grid/extension/submit-bulk-action-extension.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return SubmitBulkActionExtension; });
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
 * Handles submit of grid actions
 */

var SubmitBulkActionExtension =
/*#__PURE__*/
function () {
  function SubmitBulkActionExtension() {
    var _this = this;

    _classCallCheck(this, SubmitBulkActionExtension);

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


  _createClass(SubmitBulkActionExtension, [{
    key: "extend",
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
    key: "submit",
    value: function submit(event, grid) {
      var $submitBtn = $(event.currentTarget);
      var confirmMessage = $submitBtn.data('confirm-message');

      if (typeof confirmMessage !== "undefined" && 0 < confirmMessage.length && !confirm(confirmMessage)) {
        return;
      }

      var $form = $('#' + grid.getId() + '_filter_form');
      $form.attr('action', $submitBtn.data('form-url'));
      $form.attr('method', $submitBtn.data('form-method'));
      $form.submit();
    }
  }]);

  return SubmitBulkActionExtension;
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

/***/ "./js/pages/cms-page/index.js":
/*!************************************!*\
  !*** ./js/pages/cms-page/index.js ***!
  \************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _components_grid_grid__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../components/grid/grid */ "./js/components/grid/grid.js");
/* harmony import */ var _components_grid_extension_sorting_extension__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../components/grid/extension/sorting-extension */ "./js/components/grid/extension/sorting-extension.js");
/* harmony import */ var _components_grid_extension_action_row_submit_row_action_extension__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../components/grid/extension/action/row/submit-row-action-extension */ "./js/components/grid/extension/action/row/submit-row-action-extension.js");
/* harmony import */ var _components_grid_extension_filters_reset_extension__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../components/grid/extension/filters-reset-extension */ "./js/components/grid/extension/filters-reset-extension.js");
/* harmony import */ var _components_grid_extension_reload_list_extension__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../components/grid/extension/reload-list-extension */ "./js/components/grid/extension/reload-list-extension.js");
/* harmony import */ var _components_grid_extension_export_to_sql_manager_extension__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../components/grid/extension/export-to-sql-manager-extension */ "./js/components/grid/extension/export-to-sql-manager-extension.js");
/* harmony import */ var _components_grid_extension_link_row_action_extension__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../components/grid/extension/link-row-action-extension */ "./js/components/grid/extension/link-row-action-extension.js");
/* harmony import */ var _components_grid_extension_submit_bulk_action_extension__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../components/grid/extension/submit-bulk-action-extension */ "./js/components/grid/extension/submit-bulk-action-extension.js");
/* harmony import */ var _components_grid_extension_bulk_action_checkbox_extension__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../components/grid/extension/bulk-action-checkbox-extension */ "./js/components/grid/extension/bulk-action-checkbox-extension.js");
/* harmony import */ var _components_grid_extension_column_toggling_extension__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../../components/grid/extension/column-toggling-extension */ "./js/components/grid/extension/column-toggling-extension.js");
/* harmony import */ var _components_grid_extension_position_extension__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../../components/grid/extension/position-extension */ "./js/components/grid/extension/position-extension.js");
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
$(function () {
  var cmsCategory = new _components_grid_grid__WEBPACK_IMPORTED_MODULE_0__["default"]('cms_page_category');
  cmsCategory.addExtension(new _components_grid_extension_reload_list_extension__WEBPACK_IMPORTED_MODULE_4__["default"]());
  cmsCategory.addExtension(new _components_grid_extension_export_to_sql_manager_extension__WEBPACK_IMPORTED_MODULE_5__["default"]());
  cmsCategory.addExtension(new _components_grid_extension_filters_reset_extension__WEBPACK_IMPORTED_MODULE_3__["default"]());
  cmsCategory.addExtension(new _components_grid_extension_sorting_extension__WEBPACK_IMPORTED_MODULE_1__["default"]());
  cmsCategory.addExtension(new _components_grid_extension_link_row_action_extension__WEBPACK_IMPORTED_MODULE_6__["default"]());
  cmsCategory.addExtension(new _components_grid_extension_submit_bulk_action_extension__WEBPACK_IMPORTED_MODULE_7__["default"]());
  cmsCategory.addExtension(new _components_grid_extension_bulk_action_checkbox_extension__WEBPACK_IMPORTED_MODULE_8__["default"]());
  cmsCategory.addExtension(new _components_grid_extension_action_row_submit_row_action_extension__WEBPACK_IMPORTED_MODULE_2__["default"]());
  cmsCategory.addExtension(new _components_grid_extension_column_toggling_extension__WEBPACK_IMPORTED_MODULE_9__["default"]());
  cmsCategory.addExtension(new _components_grid_extension_position_extension__WEBPACK_IMPORTED_MODULE_10__["default"]());
});

/***/ }),

/***/ "./node_modules/tablednd/dist/jquery.tablednd.min.js":
/*!***********************************************************!*\
  !*** ./node_modules/tablednd/dist/jquery.tablednd.min.js ***!
  \***********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(jQuery) {/*! jquery.tablednd.js 30-12-2017 */
!function(a,b,c,d){var e="touchstart mousedown",f="touchmove mousemove",g="touchend mouseup";a(c).ready(function(){function b(a){for(var b={},c=a.match(/([^;:]+)/g)||[];c.length;)b[c.shift()]=c.shift().trim();return b}a("table").each(function(){"dnd"===a(this).data("table")&&a(this).tableDnD({onDragStyle:a(this).data("ondragstyle")&&b(a(this).data("ondragstyle"))||null,onDropStyle:a(this).data("ondropstyle")&&b(a(this).data("ondropstyle"))||null,onDragClass:a(this).data("ondragclass")===d&&"tDnD_whileDrag"||a(this).data("ondragclass"),onDrop:a(this).data("ondrop")&&new Function("table","row",a(this).data("ondrop")),onDragStart:a(this).data("ondragstart")&&new Function("table","row",a(this).data("ondragstart")),onDragStop:a(this).data("ondragstop")&&new Function("table","row",a(this).data("ondragstop")),scrollAmount:a(this).data("scrollamount")||5,sensitivity:a(this).data("sensitivity")||10,hierarchyLevel:a(this).data("hierarchylevel")||0,indentArtifact:a(this).data("indentartifact")||'<div class="indent">&nbsp;</div>',autoWidthAdjust:a(this).data("autowidthadjust")||!0,autoCleanRelations:a(this).data("autocleanrelations")||!0,jsonPretifySeparator:a(this).data("jsonpretifyseparator")||"\t",serializeRegexp:a(this).data("serializeregexp")&&new RegExp(a(this).data("serializeregexp"))||/[^\-]*$/,serializeParamName:a(this).data("serializeparamname")||!1,dragHandle:a(this).data("draghandle")||null})})}),jQuery.tableDnD={currentTable:null,dragObject:null,mouseOffset:null,oldX:0,oldY:0,build:function(b){return this.each(function(){this.tableDnDConfig=a.extend({onDragStyle:null,onDropStyle:null,onDragClass:"tDnD_whileDrag",onDrop:null,onDragStart:null,onDragStop:null,scrollAmount:5,sensitivity:10,hierarchyLevel:0,indentArtifact:'<div class="indent">&nbsp;</div>',autoWidthAdjust:!0,autoCleanRelations:!0,jsonPretifySeparator:"\t",serializeRegexp:/[^\-]*$/,serializeParamName:!1,dragHandle:null},b||{}),a.tableDnD.makeDraggable(this),this.tableDnDConfig.hierarchyLevel&&a.tableDnD.makeIndented(this)}),this},makeIndented:function(b){var c,d,e=b.tableDnDConfig,f=b.rows,g=a(f).first().find("td:first")[0],h=0,i=0;if(a(b).hasClass("indtd"))return null;d=a(b).addClass("indtd").attr("style"),a(b).css({whiteSpace:"nowrap"});for(var j=0;j<f.length;j++)i<a(f[j]).find("td:first").text().length&&(i=a(f[j]).find("td:first").text().length,c=j);for(a(g).css({width:"auto"}),j=0;j<e.hierarchyLevel;j++)a(f[c]).find("td:first").prepend(e.indentArtifact);for(g&&a(g).css({width:g.offsetWidth}),d&&a(b).css(d),j=0;j<e.hierarchyLevel;j++)a(f[c]).find("td:first").children(":first").remove();return e.hierarchyLevel&&a(f).each(function(){(h=a(this).data("level")||0)<=e.hierarchyLevel&&a(this).data("level",h)||a(this).data("level",0);for(var b=0;b<a(this).data("level");b++)a(this).find("td:first").prepend(e.indentArtifact)}),this},makeDraggable:function(b){var c=b.tableDnDConfig;c.dragHandle&&a(c.dragHandle,b).each(function(){a(this).bind(e,function(d){return a.tableDnD.initialiseDrag(a(this).parents("tr")[0],b,this,d,c),!1})})||a(b.rows).each(function(){a(this).hasClass("nodrag")?a(this).css("cursor",""):a(this).bind(e,function(d){if("TD"===d.target.tagName)return a.tableDnD.initialiseDrag(this,b,this,d,c),!1}).css("cursor","move")})},currentOrder:function(){var b=this.currentTable.rows;return a.map(b,function(b){return(a(b).data("level")+b.id).replace(/\s/g,"")}).join("")},initialiseDrag:function(b,d,e,h,i){this.dragObject=b,this.currentTable=d,this.mouseOffset=this.getMouseOffset(e,h),this.originalOrder=this.currentOrder(),a(c).bind(f,this.mousemove).bind(g,this.mouseup),i.onDragStart&&i.onDragStart(d,e)},updateTables:function(){this.each(function(){this.tableDnDConfig&&a.tableDnD.makeDraggable(this)})},mouseCoords:function(a){return a.originalEvent.changedTouches?{x:a.originalEvent.changedTouches[0].clientX,y:a.originalEvent.changedTouches[0].clientY}:a.pageX||a.pageY?{x:a.pageX,y:a.pageY}:{x:a.clientX+c.body.scrollLeft-c.body.clientLeft,y:a.clientY+c.body.scrollTop-c.body.clientTop}},getMouseOffset:function(a,c){var d,e;return c=c||b.event,e=this.getPosition(a),d=this.mouseCoords(c),{x:d.x-e.x,y:d.y-e.y}},getPosition:function(a){var b=0,c=0;for(0===a.offsetHeight&&(a=a.firstChild);a.offsetParent;)b+=a.offsetLeft,c+=a.offsetTop,a=a.offsetParent;return b+=a.offsetLeft,c+=a.offsetTop,{x:b,y:c}},autoScroll:function(a){var d=this.currentTable.tableDnDConfig,e=b.pageYOffset,f=b.innerHeight?b.innerHeight:c.documentElement.clientHeight?c.documentElement.clientHeight:c.body.clientHeight;c.all&&(void 0!==c.compatMode&&"BackCompat"!==c.compatMode?e=c.documentElement.scrollTop:void 0!==c.body&&(e=c.body.scrollTop)),a.y-e<d.scrollAmount&&b.scrollBy(0,-d.scrollAmount)||f-(a.y-e)<d.scrollAmount&&b.scrollBy(0,d.scrollAmount)},moveVerticle:function(a,b){0!==a.vertical&&b&&this.dragObject!==b&&this.dragObject.parentNode===b.parentNode&&(0>a.vertical&&this.dragObject.parentNode.insertBefore(this.dragObject,b.nextSibling)||0<a.vertical&&this.dragObject.parentNode.insertBefore(this.dragObject,b))},moveHorizontal:function(b,c){var d,e=this.currentTable.tableDnDConfig;if(!e.hierarchyLevel||0===b.horizontal||!c||this.dragObject!==c)return null;d=a(c).data("level"),0<b.horizontal&&d>0&&a(c).find("td:first").children(":first").remove()&&a(c).data("level",--d),0>b.horizontal&&d<e.hierarchyLevel&&a(c).prev().data("level")>=d&&a(c).children(":first").prepend(e.indentArtifact)&&a(c).data("level",++d)},mousemove:function(b){var c,d,e,f,g,h=a(a.tableDnD.dragObject),i=a.tableDnD.currentTable.tableDnDConfig;return b&&b.preventDefault(),!!a.tableDnD.dragObject&&("touchmove"===b.type&&event.preventDefault(),i.onDragClass&&h.addClass(i.onDragClass)||h.css(i.onDragStyle),d=a.tableDnD.mouseCoords(b),f=d.x-a.tableDnD.mouseOffset.x,g=d.y-a.tableDnD.mouseOffset.y,a.tableDnD.autoScroll(d),c=a.tableDnD.findDropTargetRow(h,g),e=a.tableDnD.findDragDirection(f,g),a.tableDnD.moveVerticle(e,c),a.tableDnD.moveHorizontal(e,c),!1)},findDragDirection:function(a,b){var c=this.currentTable.tableDnDConfig.sensitivity,d=this.oldX,e=this.oldY,f=d-c,g=d+c,h=e-c,i=e+c,j={horizontal:a>=f&&a<=g?0:a>d?-1:1,vertical:b>=h&&b<=i?0:b>e?-1:1};return 0!==j.horizontal&&(this.oldX=a),0!==j.vertical&&(this.oldY=b),j},findDropTargetRow:function(b,c){for(var d=0,e=this.currentTable.rows,f=this.currentTable.tableDnDConfig,g=0,h=null,i=0;i<e.length;i++)if(h=e[i],g=this.getPosition(h).y,d=parseInt(h.offsetHeight)/2,0===h.offsetHeight&&(g=this.getPosition(h.firstChild).y,d=parseInt(h.firstChild.offsetHeight)/2),c>g-d&&c<g+d)return b.is(h)||f.onAllowDrop&&!f.onAllowDrop(b,h)||a(h).hasClass("nodrop")?null:h;return null},processMouseup:function(){if(!this.currentTable||!this.dragObject)return null;var b=this.currentTable.tableDnDConfig,d=this.dragObject,e=0,h=0;a(c).unbind(f,this.mousemove).unbind(g,this.mouseup),b.hierarchyLevel&&b.autoCleanRelations&&a(this.currentTable.rows).first().find("td:first").children().each(function(){(h=a(this).parents("tr:first").data("level"))&&a(this).parents("tr:first").data("level",--h)&&a(this).remove()})&&b.hierarchyLevel>1&&a(this.currentTable.rows).each(function(){if((h=a(this).data("level"))>1)for(e=a(this).prev().data("level");h>e+1;)a(this).find("td:first").children(":first").remove(),a(this).data("level",--h)}),b.onDragClass&&a(d).removeClass(b.onDragClass)||a(d).css(b.onDropStyle),this.dragObject=null,b.onDrop&&this.originalOrder!==this.currentOrder()&&a(d).hide().fadeIn("fast")&&b.onDrop(this.currentTable,d),b.onDragStop&&b.onDragStop(this.currentTable,d),this.currentTable=null},mouseup:function(b){return b&&b.preventDefault(),a.tableDnD.processMouseup(),!1},jsonize:function(a){var b=this.currentTable;return a?JSON.stringify(this.tableData(b),null,b.tableDnDConfig.jsonPretifySeparator):JSON.stringify(this.tableData(b))},serialize:function(){return a.param(this.tableData(this.currentTable))},serializeTable:function(a){for(var b="",c=a.tableDnDConfig.serializeParamName||a.id,d=a.rows,e=0;e<d.length;e++){b.length>0&&(b+="&");var f=d[e].id;f&&a.tableDnDConfig&&a.tableDnDConfig.serializeRegexp&&(f=f.match(a.tableDnDConfig.serializeRegexp)[0],b+=c+"[]="+f)}return b},serializeTables:function(){var b=[];return a("table").each(function(){this.id&&b.push(a.param(a.tableDnD.tableData(this)))}),b.join("&")},tableData:function(b){var c,d,e,f,g=b.tableDnDConfig,h=[],i=0,j=0,k=null,l={};if(b||(b=this.currentTable),!b||!b.rows||!b.rows.length)return{error:{code:500,message:"Not a valid table."}};if(!b.id&&!g.serializeParamName)return{error:{code:500,message:"No serializable unique id provided."}};f=g.autoCleanRelations&&b.rows||a.makeArray(b.rows),d=g.serializeParamName||b.id,e=d,c=function(a){return a&&g&&g.serializeRegexp?a.match(g.serializeRegexp)[0]:a},l[e]=[],!g.autoCleanRelations&&a(f[0]).data("level")&&f.unshift({id:"undefined"});for(var m=0;m<f.length;m++)if(g.hierarchyLevel){if(0===(j=a(f[m]).data("level")||0))e=d,h=[];else if(j>i)h.push([e,i]),e=c(f[m-1].id);else if(j<i)for(var n=0;n<h.length;n++)h[n][1]===j&&(e=h[n][0]),h[n][1]>=i&&(h[n][1]=0);i=j,a.isArray(l[e])||(l[e]=[]),k=c(f[m].id),k&&l[e].push(k)}else(k=c(f[m].id))&&l[e].push(k);return l}},jQuery.fn.extend({tableDnD:a.tableDnD.build,tableDnDUpdate:a.tableDnD.updateTables,tableDnDSerialize:a.proxy(a.tableDnD.serialize,a.tableDnD),tableDnDSerializeAll:a.tableDnD.serializeTables,tableDnDData:a.proxy(a.tableDnD.tableData,a.tableDnD)})}(jQuery,window,window.document);
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! jquery */ "jquery")))

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


/***/ }),

/***/ "jquery":
/*!*************************!*\
  !*** external "jQuery" ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = jQuery;

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vLy4vanMvYXBwL3V0aWxzL3Jlc2V0X3NlYXJjaC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9hcHAvdXRpbHMvdGFibGUtc29ydGluZy5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2FjdGlvbi9yb3cvc3VibWl0LXJvdy1hY3Rpb24tZXh0ZW5zaW9uLmpzIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYnVsay1hY3Rpb24tY2hlY2tib3gtZXh0ZW5zaW9uLmpzIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vY29sdW1uLXRvZ2dsaW5nLWV4dGVuc2lvbi5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2V4cG9ydC10by1zcWwtbWFuYWdlci1leHRlbnNpb24uanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXJlc2V0LWV4dGVuc2lvbi5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2xpbmstcm93LWFjdGlvbi1leHRlbnNpb24uanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9wb3NpdGlvbi1leHRlbnNpb24uanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9yZWxvYWQtbGlzdC1leHRlbnNpb24uanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9zb3J0aW5nLWV4dGVuc2lvbi5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3N1Ym1pdC1idWxrLWFjdGlvbi1leHRlbnNpb24uanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2dyaWQuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvY21zLXBhZ2UvaW5kZXguanMiLCJ3ZWJwYWNrOi8vLy4vbm9kZV9tb2R1bGVzL3RhYmxlZG5kL2Rpc3QvanF1ZXJ5LnRhYmxlZG5kLm1pbi5qcyIsIndlYnBhY2s6Ly8vKHdlYnBhY2spL2J1aWxkaW4vZ2xvYmFsLmpzIiwid2VicGFjazovLy9leHRlcm5hbCBcImpRdWVyeVwiIl0sIm5hbWVzIjpbIiQiLCJnbG9iYWwiLCJpbml0IiwicmVzZXRTZWFyY2giLCJ1cmwiLCJyZWRpcmVjdFVybCIsInBvc3QiLCJ0aGVuIiwid2luZG93IiwibG9jYXRpb24iLCJhc3NpZ24iLCJUYWJsZVNvcnRpbmciLCJ0YWJsZSIsInNlbGVjdG9yIiwiY29sdW1ucyIsImZpbmQiLCJvbiIsImUiLCIkY29sdW1uIiwiZGVsZWdhdGVUYXJnZXQiLCJfc29ydEJ5Q29sdW1uIiwiX2dldFRvZ2dsZWRTb3J0RGlyZWN0aW9uIiwiY29sdW1uTmFtZSIsImRpcmVjdGlvbiIsImlzIiwiRXJyb3IiLCJjb2x1bW4iLCJfZ2V0VXJsIiwiZGF0YSIsImNvbE5hbWUiLCJVUkwiLCJocmVmIiwicGFyYW1zIiwic2VhcmNoUGFyYW1zIiwic2V0IiwidG9TdHJpbmciLCJTdWJtaXRSb3dBY3Rpb25FeHRlbnNpb24iLCJncmlkIiwiZ2V0Q29udGFpbmVyIiwiZXZlbnQiLCJwcmV2ZW50RGVmYXVsdCIsIiRidXR0b24iLCJjdXJyZW50VGFyZ2V0IiwiY29uZmlybU1lc3NhZ2UiLCJsZW5ndGgiLCJjb25maXJtIiwibWV0aG9kIiwiaXNHZXRPclBvc3RNZXRob2QiLCJpbmNsdWRlcyIsIiRmb3JtIiwiYXBwZW5kVG8iLCJhcHBlbmQiLCJzdWJtaXQiLCJCdWxrQWN0aW9uQ2hlY2tib3hFeHRlbnNpb24iLCJfaGFuZGxlQnVsa0FjdGlvbkNoZWNrYm94U2VsZWN0IiwiX2hhbmRsZUJ1bGtBY3Rpb25TZWxlY3RBbGxDaGVja2JveCIsIiRjaGVja2JveCIsImlzQ2hlY2tlZCIsIl9lbmFibGVCdWxrQWN0aW9uc0J0biIsIl9kaXNhYmxlQnVsa0FjdGlvbnNCdG4iLCJwcm9wIiwiY2hlY2tlZFJvd3NDb3VudCIsIkNvbHVtblRvZ2dsaW5nRXh0ZW5zaW9uIiwiJHRhYmxlIiwiX3RvZ2dsZVZhbHVlIiwicm93IiwidG9nZ2xlVXJsIiwiX3N1Ym1pdEFzRm9ybSIsImFjdGlvbiIsIkV4cG9ydFRvU3FsTWFuYWdlckV4dGVuc2lvbiIsImdldEhlYWRlckNvbnRhaW5lciIsIl9vblNob3dTcWxRdWVyeUNsaWNrIiwiX29uRXhwb3J0U3FsTWFuYWdlckNsaWNrIiwiJHNxbE1hbmFnZXJGb3JtIiwiZ2V0SWQiLCJfZmlsbEV4cG9ydEZvcm0iLCIkbW9kYWwiLCJtb2RhbCIsInF1ZXJ5IiwidmFsIiwiX2dldE5hbWVGcm9tQnJlYWRjcnVtYiIsIiRicmVhZGNydW1icyIsIm5hbWUiLCJlYWNoIiwiaSIsIml0ZW0iLCIkYnJlYWRjcnVtYiIsImJyZWFkY3J1bWJUaXRsZSIsInRleHQiLCJjb25jYXQiLCJGaWx0ZXJzUmVzZXRFeHRlbnNpb24iLCJMaW5rUm93QWN0aW9uRXh0ZW5zaW9uIiwiUG9zaXRpb25FeHRlbnNpb24iLCJleHRlbmQiLCJfYWRkSWRzVG9HcmlkVGFibGVSb3dzIiwidGFibGVEbkQiLCJvbkRyYWdDbGFzcyIsImRyYWdIYW5kbGUiLCJvbkRyb3AiLCJfaGFuZGxlUG9zaXRpb25DaGFuZ2UiLCJob3ZlciIsImNsb3Nlc3QiLCJhZGRDbGFzcyIsInJlbW92ZUNsYXNzIiwiJHJvd1Bvc2l0aW9uQ29udGFpbmVyIiwidXBkYXRlVXJsIiwicGFnaW5hdGlvbk9mZnNldCIsInBhcnNlSW50IiwicG9zaXRpb25zIiwiX2dldFJvd3NQb3NpdGlvbnMiLCJfdXBkYXRlUG9zaXRpb24iLCJ0YWJsZURhdGEiLCJKU09OIiwicGFyc2UiLCJqc29uaXplIiwicm93c0RhdGEiLCJyZWdleCIsInJvd3NOYiIsInJvd0RhdGEiLCJleGVjIiwicHVzaCIsInJvd0lkIiwibmV3UG9zaXRpb24iLCJvbGRQb3NpdGlvbiIsImluZGV4IiwicG9zaXRpb25XcmFwcGVyIiwiJHBvc2l0aW9uV3JhcHBlciIsInBvc2l0aW9uIiwiaWQiLCJhdHRyIiwicG9zaXRpb25zTmIiLCJSZWxvYWRMaXN0RXh0ZW5zaW9uIiwicmVsb2FkIiwiU29ydGluZ0V4dGVuc2lvbiIsIiRzb3J0YWJsZVRhYmxlIiwiYXR0YWNoIiwiU3VibWl0QnVsa0FjdGlvbkV4dGVuc2lvbiIsIiRzdWJtaXRCdG4iLCJHcmlkIiwiJGNvbnRhaW5lciIsImV4dGVuc2lvbiIsImNtc0NhdGVnb3J5IiwiYWRkRXh0ZW5zaW9uIiwiUmVsb2FkTGlzdEFjdGlvbkV4dGVuc2lvbiIsIlN1Ym1pdEJ1bGtFeHRlbnNpb24iXSwibWFwcGluZ3MiOiI7QUFBQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7O0FBR0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLGtEQUEwQyxnQ0FBZ0M7QUFDMUU7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxnRUFBd0Qsa0JBQWtCO0FBQzFFO0FBQ0EseURBQWlELGNBQWM7QUFDL0Q7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGlEQUF5QyxpQ0FBaUM7QUFDMUUsd0hBQWdILG1CQUFtQixFQUFFO0FBQ3JJO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsbUNBQTJCLDBCQUEwQixFQUFFO0FBQ3ZELHlDQUFpQyxlQUFlO0FBQ2hEO0FBQ0E7QUFDQTs7QUFFQTtBQUNBLDhEQUFzRCwrREFBK0Q7O0FBRXJIO0FBQ0E7OztBQUdBO0FBQ0E7Ozs7Ozs7Ozs7Ozs7QUNsRkE7QUFBQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQXlCQTs7O0FBSUEsSUFBTUEsQ0FBQyxHQUFHQyxN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Q0FBeUMsd0JBQXdCLFdBQVcscURBQXFELDREQUE0RCxpQ0FBaUMsVUFBVSxtQkFBbUIsa09BQWtPLEVBQUUsZ0M7Ozs7Ozs7Ozs7OztBQ0QzcVM7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsQ0FBQzs7QUFFRDtBQUNBO0FBQ0E7QUFDQSxDQUFDO0FBQ0Q7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQSw0Q0FBNEM7O0FBRTVDOzs7Ozs7Ozs7Ozs7QUNuQkEsd0IiLCJmaWxlIjoiY21zX3BhZ2UuYnVuZGxlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7IGVudW1lcmFibGU6IHRydWUsIGdldDogZ2V0dGVyIH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBkZWZpbmUgX19lc01vZHVsZSBvbiBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnIgPSBmdW5jdGlvbihleHBvcnRzKSB7XG4gXHRcdGlmKHR5cGVvZiBTeW1ib2wgIT09ICd1bmRlZmluZWQnICYmIFN5bWJvbC50b1N0cmluZ1RhZykge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBTeW1ib2wudG9TdHJpbmdUYWcsIHsgdmFsdWU6ICdNb2R1bGUnIH0pO1xuIFx0XHR9XG4gXHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCAnX19lc01vZHVsZScsIHsgdmFsdWU6IHRydWUgfSk7XG4gXHR9O1xuXG4gXHQvLyBjcmVhdGUgYSBmYWtlIG5hbWVzcGFjZSBvYmplY3RcbiBcdC8vIG1vZGUgJiAxOiB2YWx1ZSBpcyBhIG1vZHVsZSBpZCwgcmVxdWlyZSBpdFxuIFx0Ly8gbW9kZSAmIDI6IG1lcmdlIGFsbCBwcm9wZXJ0aWVzIG9mIHZhbHVlIGludG8gdGhlIG5zXG4gXHQvLyBtb2RlICYgNDogcmV0dXJuIHZhbHVlIHdoZW4gYWxyZWFkeSBucyBvYmplY3RcbiBcdC8vIG1vZGUgJiA4fDE6IGJlaGF2ZSBsaWtlIHJlcXVpcmVcbiBcdF9fd2VicGFja19yZXF1aXJlX18udCA9IGZ1bmN0aW9uKHZhbHVlLCBtb2RlKSB7XG4gXHRcdGlmKG1vZGUgJiAxKSB2YWx1ZSA9IF9fd2VicGFja19yZXF1aXJlX18odmFsdWUpO1xuIFx0XHRpZihtb2RlICYgOCkgcmV0dXJuIHZhbHVlO1xuIFx0XHRpZigobW9kZSAmIDQpICYmIHR5cGVvZiB2YWx1ZSA9PT0gJ29iamVjdCcgJiYgdmFsdWUgJiYgdmFsdWUuX19lc01vZHVsZSkgcmV0dXJuIHZhbHVlO1xuIFx0XHR2YXIgbnMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLnIobnMpO1xuIFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkobnMsICdkZWZhdWx0JywgeyBlbnVtZXJhYmxlOiB0cnVlLCB2YWx1ZTogdmFsdWUgfSk7XG4gXHRcdGlmKG1vZGUgJiAyICYmIHR5cGVvZiB2YWx1ZSAhPSAnc3RyaW5nJykgZm9yKHZhciBrZXkgaW4gdmFsdWUpIF9fd2VicGFja19yZXF1aXJlX18uZChucywga2V5LCBmdW5jdGlvbihrZXkpIHsgcmV0dXJuIHZhbHVlW2tleV07IH0uYmluZChudWxsLCBrZXkpKTtcbiBcdFx0cmV0dXJuIG5zO1xuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCIvYWRtaW4tZGV2L3RoZW1lcy9uZXctdGhlbWUvcHVibGljL1wiO1xuXG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gXCIuL2pzL3BhZ2VzL2Ntcy1wYWdlL2luZGV4LmpzXCIpO1xuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuLyoqXG4gKiBTZW5kIGEgUG9zdCBSZXF1ZXN0IHRvIHJlc2V0IHNlYXJjaCBBY3Rpb24uXG4gKi9cblxuY29uc3QgJCA9IGdsb2JhbC4kO1xuXG5jb25zdCBpbml0ID0gZnVuY3Rpb24gcmVzZXRTZWFyY2godXJsLCByZWRpcmVjdFVybCkge1xuICAgICQucG9zdCh1cmwpLnRoZW4oKCkgPT4gd2luZG93LmxvY2F0aW9uLmFzc2lnbihyZWRpcmVjdFVybCkpO1xufTtcblxuZXhwb3J0IGRlZmF1bHQgaW5pdDtcbiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSBnbG9iYWwuJDtcblxuLyoqXG4gKiBNYWtlcyBhIHRhYmxlIHNvcnRhYmxlIGJ5IGNvbHVtbnMuXG4gKiBUaGlzIGZvcmNlcyBhIHBhZ2UgcmVsb2FkIHdpdGggbW9yZSBxdWVyeSBwYXJhbWV0ZXJzLlxuICovXG5jbGFzcyBUYWJsZVNvcnRpbmcge1xuXG4gIC8qKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gdGFibGVcbiAgICovXG4gIGNvbnN0cnVjdG9yKHRhYmxlKSB7XG4gICAgdGhpcy5zZWxlY3RvciA9ICcucHMtc29ydGFibGUtY29sdW1uJztcbiAgICB0aGlzLmNvbHVtbnMgPSAkKHRhYmxlKS5maW5kKHRoaXMuc2VsZWN0b3IpO1xuICB9XG5cbiAgLyoqXG4gICAqIEF0dGFjaGVzIHRoZSBsaXN0ZW5lcnNcbiAgICovXG4gIGF0dGFjaCgpIHtcbiAgICB0aGlzLmNvbHVtbnMub24oJ2NsaWNrJywgKGUpID0+IHtcbiAgICAgIGNvbnN0ICRjb2x1bW4gPSAkKGUuZGVsZWdhdGVUYXJnZXQpO1xuICAgICAgdGhpcy5fc29ydEJ5Q29sdW1uKCRjb2x1bW4sIHRoaXMuX2dldFRvZ2dsZWRTb3J0RGlyZWN0aW9uKCRjb2x1bW4pKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTb3J0IHVzaW5nIGEgY29sdW1uIG5hbWVcbiAgICogQHBhcmFtIHtzdHJpbmd9IGNvbHVtbk5hbWVcbiAgICogQHBhcmFtIHtzdHJpbmd9IGRpcmVjdGlvbiBcImFzY1wiIG9yIFwiZGVzY1wiXG4gICAqL1xuICBzb3J0QnkoY29sdW1uTmFtZSwgZGlyZWN0aW9uKSB7XG4gICAgY29uc3QgJGNvbHVtbiA9IHRoaXMuY29sdW1ucy5pcyhgW2RhdGEtc29ydC1jb2wtbmFtZT1cIiR7Y29sdW1uTmFtZX1cIl1gKTtcbiAgICBpZiAoISRjb2x1bW4pIHtcbiAgICAgIHRocm93IG5ldyBFcnJvcihgQ2Fubm90IHNvcnQgYnkgXCIke2NvbHVtbk5hbWV9XCI6IGludmFsaWQgY29sdW1uYCk7XG4gICAgfVxuXG4gICAgdGhpcy5fc29ydEJ5Q29sdW1uKCRjb2x1bW4sIGRpcmVjdGlvbik7XG4gIH1cblxuICAvKipcbiAgICogU29ydCB1c2luZyBhIGNvbHVtbiBlbGVtZW50XG4gICAqIEBwYXJhbSB7alF1ZXJ5fSBjb2x1bW5cbiAgICogQHBhcmFtIHtzdHJpbmd9IGRpcmVjdGlvbiBcImFzY1wiIG9yIFwiZGVzY1wiXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc29ydEJ5Q29sdW1uKGNvbHVtbiwgZGlyZWN0aW9uKSB7XG4gICAgd2luZG93LmxvY2F0aW9uID0gdGhpcy5fZ2V0VXJsKGNvbHVtbi5kYXRhKCdzb3J0Q29sTmFtZScpLCAoZGlyZWN0aW9uID09PSAnZGVzYycpID8gJ2Rlc2MnIDogJ2FzYycpO1xuICB9XG5cbiAgLyoqXG4gICAqIFJldHVybnMgdGhlIGludmVydGVkIGRpcmVjdGlvbiB0byBzb3J0IGFjY29yZGluZyB0byB0aGUgY29sdW1uJ3MgY3VycmVudCBvbmVcbiAgICogQHBhcmFtIHtqUXVlcnl9IGNvbHVtblxuICAgKiBAcmV0dXJuIHtzdHJpbmd9XG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfZ2V0VG9nZ2xlZFNvcnREaXJlY3Rpb24oY29sdW1uKSB7XG4gICAgcmV0dXJuIGNvbHVtbi5kYXRhKCdzb3J0RGlyZWN0aW9uJykgPT09ICdhc2MnID8gJ2Rlc2MnIDogJ2FzYyc7XG4gIH1cblxuICAvKipcbiAgICogUmV0dXJucyB0aGUgdXJsIGZvciB0aGUgc29ydGVkIHRhYmxlXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBjb2xOYW1lXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBkaXJlY3Rpb25cbiAgICogQHJldHVybiB7c3RyaW5nfVxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2dldFVybChjb2xOYW1lLCBkaXJlY3Rpb24pIHtcbiAgICBjb25zdCB1cmwgPSBuZXcgVVJMKHdpbmRvdy5sb2NhdGlvbi5ocmVmKTtcbiAgICBjb25zdCBwYXJhbXMgPSB1cmwuc2VhcmNoUGFyYW1zO1xuXG4gICAgcGFyYW1zLnNldCgnb3JkZXJCeScsIGNvbE5hbWUpO1xuICAgIHBhcmFtcy5zZXQoJ3NvcnRPcmRlcicsIGRpcmVjdGlvbik7XG5cbiAgICByZXR1cm4gdXJsLnRvU3RyaW5nKCk7XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgVGFibGVTb3J0aW5nO1xuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIFN1Ym1pdFJvd0FjdGlvbkV4dGVuc2lvbiBoYW5kbGVzIHN1Ym1pdHRpbmcgb2Ygcm93IGFjdGlvblxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBTdWJtaXRSb3dBY3Rpb25FeHRlbnNpb24ge1xuICAvKipcbiAgICogRXh0ZW5kIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkub24oJ2NsaWNrJywgJy5qcy1zdWJtaXQtcm93LWFjdGlvbicsIChldmVudCkgPT4ge1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgY29uc3QgJGJ1dHRvbiA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgICBjb25zdCBjb25maXJtTWVzc2FnZSA9ICRidXR0b24uZGF0YSgnY29uZmlybS1tZXNzYWdlJyk7XG5cbiAgICAgIGlmIChjb25maXJtTWVzc2FnZS5sZW5ndGggJiYgIWNvbmZpcm0oY29uZmlybU1lc3NhZ2UpKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cblxuICAgICAgY29uc3QgbWV0aG9kID0gJGJ1dHRvbi5kYXRhKCdtZXRob2QnKTtcbiAgICAgIGNvbnN0IGlzR2V0T3JQb3N0TWV0aG9kID0gWydHRVQnLCAnUE9TVCddLmluY2x1ZGVzKG1ldGhvZCk7XG5cbiAgICAgIGNvbnN0ICRmb3JtID0gJCgnPGZvcm0+Jywge1xuICAgICAgICAnYWN0aW9uJzogJGJ1dHRvbi5kYXRhKCd1cmwnKSxcbiAgICAgICAgJ21ldGhvZCc6IGlzR2V0T3JQb3N0TWV0aG9kID8gbWV0aG9kIDogJ1BPU1QnLFxuICAgICAgfSkuYXBwZW5kVG8oJ2JvZHknKTtcblxuICAgICAgaWYgKCFpc0dldE9yUG9zdE1ldGhvZCkge1xuICAgICAgICAkZm9ybS5hcHBlbmQoJCgnPGlucHV0PicsIHtcbiAgICAgICAgICAndHlwZSc6ICdfaGlkZGVuJyxcbiAgICAgICAgICAnbmFtZSc6ICdfbWV0aG9kJyxcbiAgICAgICAgICAndmFsdWUnOiBtZXRob2RcbiAgICAgICAgfSkpO1xuICAgICAgfVxuXG4gICAgICAkZm9ybS5zdWJtaXQoKTtcbiAgICB9KTtcbiAgfVxufVxuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIEJ1bGtBY3Rpb25TZWxlY3RDaGVja2JveEV4dGVuc2lvblxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBCdWxrQWN0aW9uQ2hlY2tib3hFeHRlbnNpb24ge1xuICAvKipcbiAgICogRXh0ZW5kIGdyaWQgd2l0aCBidWxrIGFjdGlvbiBjaGVja2JveGVzIGhhbmRsaW5nIGZ1bmN0aW9uYWxpdHlcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIHRoaXMuX2hhbmRsZUJ1bGtBY3Rpb25DaGVja2JveFNlbGVjdChncmlkKTtcbiAgICB0aGlzLl9oYW5kbGVCdWxrQWN0aW9uU2VsZWN0QWxsQ2hlY2tib3goZ3JpZCk7XG4gIH1cblxuICAvKipcbiAgICogSGFuZGxlcyBcIlNlbGVjdCBhbGxcIiBidXR0b24gaW4gdGhlIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGFuZGxlQnVsa0FjdGlvblNlbGVjdEFsbENoZWNrYm94KGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLm9uKCdjaGFuZ2UnLCAnLmpzLWJ1bGstYWN0aW9uLXNlbGVjdC1hbGwnLCAoZSkgPT4ge1xuICAgICAgY29uc3QgJGNoZWNrYm94ID0gJChlLmN1cnJlbnRUYXJnZXQpO1xuXG4gICAgICBjb25zdCBpc0NoZWNrZWQgPSAkY2hlY2tib3guaXMoJzpjaGVja2VkJyk7XG4gICAgICBpZiAoaXNDaGVja2VkKSB7XG4gICAgICAgIHRoaXMuX2VuYWJsZUJ1bGtBY3Rpb25zQnRuKGdyaWQpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgdGhpcy5fZGlzYWJsZUJ1bGtBY3Rpb25zQnRuKGdyaWQpO1xuICAgICAgfVxuXG4gICAgICBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5qcy1idWxrLWFjdGlvbi1jaGVja2JveCcpLnByb3AoJ2NoZWNrZWQnLCBpc0NoZWNrZWQpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEhhbmRsZXMgZWFjaCBidWxrIGFjdGlvbiBjaGVja2JveCBzZWxlY3QgaW4gdGhlIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGFuZGxlQnVsa0FjdGlvbkNoZWNrYm94U2VsZWN0KGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLm9uKCdjaGFuZ2UnLCAnLmpzLWJ1bGstYWN0aW9uLWNoZWNrYm94JywgKCkgPT4ge1xuICAgICAgY29uc3QgY2hlY2tlZFJvd3NDb3VudCA9IGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgnLmpzLWJ1bGstYWN0aW9uLWNoZWNrYm94OmNoZWNrZWQnKS5sZW5ndGg7XG5cbiAgICAgIGlmIChjaGVja2VkUm93c0NvdW50ID4gMCkge1xuICAgICAgICB0aGlzLl9lbmFibGVCdWxrQWN0aW9uc0J0bihncmlkKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHRoaXMuX2Rpc2FibGVCdWxrQWN0aW9uc0J0bihncmlkKTtcbiAgICAgIH1cbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFbmFibGUgYnVsayBhY3Rpb25zIGJ1dHRvblxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9lbmFibGVCdWxrQWN0aW9uc0J0bihncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCcuanMtYnVsay1hY3Rpb25zLWJ0bicpLnByb3AoJ2Rpc2FibGVkJywgZmFsc2UpO1xuICB9XG5cbiAgLyoqXG4gICAqIERpc2FibGUgYnVsayBhY3Rpb25zIGJ1dHRvblxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9kaXNhYmxlQnVsa0FjdGlvbnNCdG4oZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgnLmpzLWJ1bGstYWN0aW9ucy1idG4nKS5wcm9wKCdkaXNhYmxlZCcsIHRydWUpO1xuICB9XG59XG4iLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gZ2xvYmFsLiQ7XG5cbi8qKlxuICogQ2xhc3MgUmVsb2FkTGlzdEV4dGVuc2lvbiBleHRlbmRzIGdyaWQgd2l0aCBcIkNvbHVtbiB0b2dnbGluZ1wiIGZlYXR1cmVcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQ29sdW1uVG9nZ2xpbmdFeHRlbnNpb24ge1xuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgY29uc3QgJHRhYmxlID0gZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCd0YWJsZS50YWJsZScpO1xuICAgICR0YWJsZS5maW5kKCcucHMtdG9nZ2xhYmxlLXJvdycpLm9uKCdjbGljaycsIChlKSA9PiB7XG4gICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICB0aGlzLl90b2dnbGVWYWx1ZSgkKGUuZGVsZWdhdGVUYXJnZXQpKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gcm93XG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfdG9nZ2xlVmFsdWUocm93KSB7XG4gICAgY29uc3QgdG9nZ2xlVXJsID0gcm93LmRhdGEoJ3RvZ2dsZVVybCcpO1xuXG4gICAgdGhpcy5fc3VibWl0QXNGb3JtKHRvZ2dsZVVybCk7XG4gIH1cblxuICAvKipcbiAgICogU3VibWl0cyByZXF1ZXN0IHVybCBhcyBmb3JtXG4gICAqXG4gICAqIEBwYXJhbSB7c3RyaW5nfSB0b2dnbGVVcmxcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zdWJtaXRBc0Zvcm0odG9nZ2xlVXJsKSB7XG4gICAgY29uc3QgJGZvcm0gPSAkKCc8Zm9ybT4nLCB7XG4gICAgICBhY3Rpb246IHRvZ2dsZVVybCxcbiAgICAgIG1ldGhvZDogJ1BPU1QnLFxuICAgIH0pLmFwcGVuZFRvKCdib2R5Jyk7XG5cbiAgICAkZm9ybS5zdWJtaXQoKTtcbiAgfVxufVxuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIEV4cG9ydFRvU3FsTWFuYWdlckV4dGVuc2lvbiBleHRlbmRzIGdyaWQgd2l0aCBleHBvcnRpbmcgcXVlcnkgdG8gU1FMIE1hbmFnZXJcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgRXhwb3J0VG9TcWxNYW5hZ2VyRXh0ZW5zaW9uIHtcbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldEhlYWRlckNvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtY29tbW9uX3Nob3dfcXVlcnktZ3JpZC1hY3Rpb24nLCAoKSA9PiB0aGlzLl9vblNob3dTcWxRdWVyeUNsaWNrKGdyaWQpKTtcbiAgICBncmlkLmdldEhlYWRlckNvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtY29tbW9uX2V4cG9ydF9zcWxfbWFuYWdlci1ncmlkLWFjdGlvbicsICgpID0+IHRoaXMuX29uRXhwb3J0U3FsTWFuYWdlckNsaWNrKGdyaWQpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJbnZva2VkIHdoZW4gY2xpY2tpbmcgb24gdGhlIFwic2hvdyBzcWwgcXVlcnlcIiB0b29sYmFyIGJ1dHRvblxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9vblNob3dTcWxRdWVyeUNsaWNrKGdyaWQpIHtcbiAgICBjb25zdCAkc3FsTWFuYWdlckZvcm0gPSAkKCcjJyArIGdyaWQuZ2V0SWQoKSArICdfY29tbW9uX3Nob3dfcXVlcnlfbW9kYWxfZm9ybScpO1xuICAgIHRoaXMuX2ZpbGxFeHBvcnRGb3JtKCRzcWxNYW5hZ2VyRm9ybSwgZ3JpZCk7XG5cbiAgICBjb25zdCAkbW9kYWwgPSAkKCcjJyArIGdyaWQuZ2V0SWQoKSArICdfZ3JpZF9jb21tb25fc2hvd19xdWVyeV9tb2RhbCcpO1xuICAgICRtb2RhbC5tb2RhbCgnc2hvdycpO1xuXG4gICAgJG1vZGFsLm9uKCdjbGljaycsICcuYnRuLXNxbC1zdWJtaXQnLCAoKSA9PiAkc3FsTWFuYWdlckZvcm0uc3VibWl0KCkpO1xuICB9XG5cbiAgLyoqXG4gICAqIEludm9rZWQgd2hlbiBjbGlja2luZyBvbiB0aGUgXCJleHBvcnQgdG8gdGhlIHNxbCBxdWVyeVwiIHRvb2xiYXIgYnV0dG9uXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uRXhwb3J0U3FsTWFuYWdlckNsaWNrKGdyaWQpIHtcbiAgICBjb25zdCAkc3FsTWFuYWdlckZvcm0gPSAkKCcjJyArIGdyaWQuZ2V0SWQoKSArICdfY29tbW9uX3Nob3dfcXVlcnlfbW9kYWxfZm9ybScpO1xuXG4gICAgdGhpcy5fZmlsbEV4cG9ydEZvcm0oJHNxbE1hbmFnZXJGb3JtLCBncmlkKTtcblxuICAgICRzcWxNYW5hZ2VyRm9ybS5zdWJtaXQoKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBGaWxsIGV4cG9ydCBmb3JtIHdpdGggU1FMIGFuZCBpdCdzIG5hbWVcbiAgICpcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRzcWxNYW5hZ2VyRm9ybVxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9maWxsRXhwb3J0Rm9ybSgkc3FsTWFuYWdlckZvcm0sIGdyaWQpIHtcbiAgICBjb25zdCBxdWVyeSA9IGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgnLmpzLWdyaWQtdGFibGUnKS5kYXRhKCdxdWVyeScpO1xuXG4gICAgJHNxbE1hbmFnZXJGb3JtLmZpbmQoJ3RleHRhcmVhW25hbWU9XCJzcWxcIl0nKS52YWwocXVlcnkpO1xuICAgICRzcWxNYW5hZ2VyRm9ybS5maW5kKCdpbnB1dFtuYW1lPVwibmFtZVwiXScpLnZhbCh0aGlzLl9nZXROYW1lRnJvbUJyZWFkY3J1bWIoKSk7XG4gIH1cblxuICAvKipcbiAgICogR2V0IGV4cG9ydCBuYW1lIGZyb20gcGFnZSdzIGJyZWFkY3J1bWJcbiAgICpcbiAgICogQHJldHVybiB7U3RyaW5nfVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2dldE5hbWVGcm9tQnJlYWRjcnVtYigpIHtcbiAgICBjb25zdCAkYnJlYWRjcnVtYnMgPSAkKCcuaGVhZGVyLXRvb2xiYXInKS5maW5kKCcuYnJlYWRjcnVtYi1pdGVtJyk7XG4gICAgbGV0IG5hbWUgPSAnJztcblxuICAgICRicmVhZGNydW1icy5lYWNoKChpLCBpdGVtKSA9PiB7XG4gICAgICBjb25zdCAkYnJlYWRjcnVtYiA9ICQoaXRlbSk7XG5cbiAgICAgIGNvbnN0IGJyZWFkY3J1bWJUaXRsZSA9IDAgPCAkYnJlYWRjcnVtYi5maW5kKCdhJykubGVuZ3RoID9cbiAgICAgICAgJGJyZWFkY3J1bWIuZmluZCgnYScpLnRleHQoKSA6XG4gICAgICAgICRicmVhZGNydW1iLnRleHQoKTtcblxuICAgICAgaWYgKDAgPCBuYW1lLmxlbmd0aCkge1xuICAgICAgICBuYW1lID0gbmFtZS5jb25jYXQoJyA+ICcpO1xuICAgICAgfVxuXG4gICAgICBuYW1lID0gbmFtZS5jb25jYXQoYnJlYWRjcnVtYlRpdGxlKTtcbiAgICB9KTtcblxuICAgIHJldHVybiBuYW1lO1xuICB9XG59XG4iLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgcmVzZXRTZWFyY2ggZnJvbSAnLi4vLi4vLi4vYXBwL3V0aWxzL3Jlc2V0X3NlYXJjaCc7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBGaWx0ZXJzUmVzZXRFeHRlbnNpb24gZXh0ZW5kcyBncmlkIHdpdGggZmlsdGVycyByZXNldHRpbmdcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgRmlsdGVyc1Jlc2V0RXh0ZW5zaW9uIHtcblxuICAvKipcbiAgICogRXh0ZW5kIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkub24oJ2NsaWNrJywgJy5qcy1yZXNldC1zZWFyY2gnLCAoZXZlbnQpID0+IHtcbiAgICAgIHJlc2V0U2VhcmNoKCQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgndXJsJyksICQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgncmVkaXJlY3QnKSk7XG4gICAgfSk7XG4gIH1cbn1cbiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBMaW5rUm93QWN0aW9uRXh0ZW5zaW9uIGhhbmRsZXMgbGluayByb3cgYWN0aW9uc1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBMaW5rUm93QWN0aW9uRXh0ZW5zaW9uIHtcbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtbGluay1yb3ctYWN0aW9uJywgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCBjb25maXJtTWVzc2FnZSA9ICQoZXZlbnQuY3VycmVudFRhcmdldCkuZGF0YSgnY29uZmlybS1tZXNzYWdlJyk7XG5cbiAgICAgIGlmIChjb25maXJtTWVzc2FnZS5sZW5ndGggJiYgIWNvbmZpcm0oY29uZmlybU1lc3NhZ2UpKSB7XG4gICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cbn1cbiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCB0YWJsZURuRCBmcm9tIFwidGFibGVkbmQvZGlzdC9qcXVlcnkudGFibGVkbmQubWluXCI7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBQb3NpdGlvbkV4dGVuc2lvbiBleHRlbmRzIEdyaWQgd2l0aCByZW9yZGVyYWJsZSBwb3NpdGlvbnNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgUG9zaXRpb25FeHRlbnNpb24ge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICByZXR1cm4ge1xuICAgICAgZXh0ZW5kOiAoZ3JpZCkgPT4gdGhpcy5leHRlbmQoZ3JpZCksXG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICB0aGlzLmdyaWQgPSBncmlkO1xuICAgIHRoaXMuX2FkZElkc1RvR3JpZFRhYmxlUm93cygpO1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgnLmpzLWdyaWQtdGFibGUnKS50YWJsZURuRCh7XG4gICAgICBvbkRyYWdDbGFzczogJ3Bvc2l0aW9uLXJvdy13aGlsZS1kcmFnJyxcbiAgICAgIGRyYWdIYW5kbGU6ICcuanMtZHJhZy1oYW5kbGUnLFxuICAgICAgb25Ecm9wOiAodGFibGUsIHJvdykgPT4gdGhpcy5faGFuZGxlUG9zaXRpb25DaGFuZ2Uocm93KSxcbiAgICB9KTtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5qcy1kcmFnLWhhbmRsZScpLmhvdmVyKFxuICAgICAgZnVuY3Rpb24oKSB7XG4gICAgICAgICQodGhpcykuY2xvc2VzdCgndHInKS5hZGRDbGFzcygnaG92ZXInKTtcbiAgICAgIH0sXG4gICAgICBmdW5jdGlvbigpIHtcbiAgICAgICAgJCh0aGlzKS5jbG9zZXN0KCd0cicpLnJlbW92ZUNsYXNzKCdob3ZlcicpO1xuICAgICAgfVxuICAgICk7XG4gIH1cblxuICAvKipcbiAgICogV2hlbiBwb3NpdGlvbiBpcyBjaGFuZ2VkIGhhbmRsZSB1cGRhdGVcbiAgICpcbiAgICogQHBhcmFtIHtIVE1MRWxlbWVudH0gcm93XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGFuZGxlUG9zaXRpb25DaGFuZ2Uocm93KSB7XG4gICAgY29uc3QgJHJvd1Bvc2l0aW9uQ29udGFpbmVyID0gJChyb3cpLmZpbmQoJy5qcy0nICsgdGhpcy5ncmlkLmdldElkKCkgKyAnLXBvc2l0aW9uOmZpcnN0Jyk7XG4gICAgY29uc3QgdXBkYXRlVXJsID0gJHJvd1Bvc2l0aW9uQ29udGFpbmVyLmRhdGEoJ3VwZGF0ZS11cmwnKTtcbiAgICBjb25zdCBtZXRob2QgPSAkcm93UG9zaXRpb25Db250YWluZXIuZGF0YSgndXBkYXRlLW1ldGhvZCcpO1xuICAgIGNvbnN0IHBhZ2luYXRpb25PZmZzZXQgPSBwYXJzZUludCgkcm93UG9zaXRpb25Db250YWluZXIuZGF0YSgncGFnaW5hdGlvbi1vZmZzZXQnKSwgMTApO1xuICAgIGNvbnN0IHBvc2l0aW9ucyA9IHRoaXMuX2dldFJvd3NQb3NpdGlvbnMocGFnaW5hdGlvbk9mZnNldCk7XG4gICAgY29uc3QgcGFyYW1zID0ge3Bvc2l0aW9uc307XG5cbiAgICB0aGlzLl91cGRhdGVQb3NpdGlvbih1cGRhdGVVcmwsIHBhcmFtcywgbWV0aG9kKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZXR1cm5zIHRoZSBjdXJyZW50IHRhYmxlIHBvc2l0aW9uc1xuICAgKiBAcmV0dXJucyB7QXJyYXl9XG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfZ2V0Um93c1Bvc2l0aW9ucyhwYWdpbmF0aW9uT2Zmc2V0KSB7XG4gICAgY29uc3QgdGFibGVEYXRhID0gSlNPTi5wYXJzZSgkLnRhYmxlRG5ELmpzb25pemUoKSk7XG4gICAgY29uc3Qgcm93c0RhdGEgPSB0YWJsZURhdGFbdGhpcy5ncmlkLmdldElkKCkrJ19ncmlkX3RhYmxlJ107XG4gICAgY29uc3QgcmVnZXggPSAvXnJvd18oXFxkKylfKFxcZCspJC87XG5cbiAgICBjb25zdCByb3dzTmIgPSByb3dzRGF0YS5sZW5ndGg7XG4gICAgY29uc3QgcG9zaXRpb25zID0gW107XG4gICAgbGV0IHJvd0RhdGEsIGk7XG4gICAgZm9yIChpID0gMDsgaSA8IHJvd3NOYjsgKytpKSB7XG4gICAgICByb3dEYXRhID0gcmVnZXguZXhlYyhyb3dzRGF0YVtpXSk7XG4gICAgICBwb3NpdGlvbnMucHVzaCh7XG4gICAgICAgIHJvd0lkOiByb3dEYXRhWzFdLFxuICAgICAgICBuZXdQb3NpdGlvbjogcGFnaW5hdGlvbk9mZnNldCArIGksXG4gICAgICAgIG9sZFBvc2l0aW9uOiBwYXJzZUludChyb3dEYXRhWzJdLCAxMCksXG4gICAgICB9KTtcbiAgICB9XG5cbiAgICByZXR1cm4gcG9zaXRpb25zO1xuICB9XG5cbiAgLyoqXG4gICAqIEFkZCBJRCdzIHRvIEdyaWQgdGFibGUgcm93cyB0byBtYWtlIHRhYmxlRG5ELm9uRHJvcCgpIGZ1bmN0aW9uIHdvcmsuXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfYWRkSWRzVG9HcmlkVGFibGVSb3dzKCkge1xuICAgIHRoaXMuZ3JpZC5nZXRDb250YWluZXIoKVxuICAgICAgLmZpbmQoJy5qcy1ncmlkLXRhYmxlIC5qcy0nICsgdGhpcy5ncmlkLmdldElkKCkgKyAnLXBvc2l0aW9uJylcbiAgICAgIC5lYWNoKChpbmRleCwgcG9zaXRpb25XcmFwcGVyKSA9PiB7XG4gICAgICAgIGNvbnN0ICRwb3NpdGlvbldyYXBwZXIgPSAkKHBvc2l0aW9uV3JhcHBlcik7XG4gICAgICAgIGNvbnN0IHJvd0lkID0gJHBvc2l0aW9uV3JhcHBlci5kYXRhKCdpZCcpO1xuICAgICAgICBjb25zdCBwb3NpdGlvbiA9ICRwb3NpdGlvbldyYXBwZXIuZGF0YSgncG9zaXRpb24nKTtcbiAgICAgICAgY29uc3QgaWQgPSBgcm93XyR7cm93SWR9XyR7cG9zaXRpb259YDtcbiAgICAgICAgJHBvc2l0aW9uV3JhcHBlci5jbG9zZXN0KCd0cicpLmF0dHIoJ2lkJywgaWQpO1xuICAgICAgICAkcG9zaXRpb25XcmFwcGVyLmNsb3Nlc3QoJ3RkJykuYWRkQ2xhc3MoJ2pzLWRyYWctaGFuZGxlJyk7XG4gICAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBQcm9jZXNzIHJvd3MgcG9zaXRpb25zIHVwZGF0ZVxuICAgKlxuICAgKiBAcGFyYW0ge1N0cmluZ30gdXJsXG4gICAqIEBwYXJhbSB7T2JqZWN0fSBwYXJhbXNcbiAgICogQHBhcmFtIHtTdHJpbmd9IG1ldGhvZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3VwZGF0ZVBvc2l0aW9uKHVybCwgcGFyYW1zLCBtZXRob2QpIHtcbiAgICBjb25zdCBpc0dldE9yUG9zdE1ldGhvZCA9IFsnR0VUJywgJ1BPU1QnXS5pbmNsdWRlcyhtZXRob2QpO1xuXG4gICAgY29uc3QgJGZvcm0gPSAkKCc8Zm9ybT4nLCB7XG4gICAgICAnYWN0aW9uJzogdXJsLFxuICAgICAgJ21ldGhvZCc6IGlzR2V0T3JQb3N0TWV0aG9kID8gbWV0aG9kIDogJ1BPU1QnLFxuICAgIH0pLmFwcGVuZFRvKCdib2R5Jyk7XG5cbiAgICBjb25zdCBwb3NpdGlvbnNOYiA9IHBhcmFtcy5wb3NpdGlvbnMubGVuZ3RoO1xuICAgIGxldCBwb3NpdGlvbjtcbiAgICBmb3IgKGxldCBpID0gMDsgaSA8IHBvc2l0aW9uc05iOyArK2kpIHtcbiAgICAgIHBvc2l0aW9uID0gcGFyYW1zLnBvc2l0aW9uc1tpXTtcbiAgICAgICRmb3JtLmFwcGVuZChcbiAgICAgICAgJCgnPGlucHV0PicsIHtcbiAgICAgICAgICAndHlwZSc6ICdoaWRkZW4nLFxuICAgICAgICAgICduYW1lJzogJ3Bvc2l0aW9uc1snK2krJ11bcm93SWRdJyxcbiAgICAgICAgICAndmFsdWUnOiBwb3NpdGlvbi5yb3dJZFxuICAgICAgICB9KSxcbiAgICAgICAgJCgnPGlucHV0PicsIHtcbiAgICAgICAgICAndHlwZSc6ICdoaWRkZW4nLFxuICAgICAgICAgICduYW1lJzogJ3Bvc2l0aW9uc1snK2krJ11bb2xkUG9zaXRpb25dJyxcbiAgICAgICAgICAndmFsdWUnOiBwb3NpdGlvbi5vbGRQb3NpdGlvblxuICAgICAgICB9KSxcbiAgICAgICAgJCgnPGlucHV0PicsIHtcbiAgICAgICAgICAndHlwZSc6ICdoaWRkZW4nLFxuICAgICAgICAgICduYW1lJzogJ3Bvc2l0aW9uc1snK2krJ11bbmV3UG9zaXRpb25dJyxcbiAgICAgICAgICAndmFsdWUnOiBwb3NpdGlvbi5uZXdQb3NpdGlvblxuICAgICAgICB9KVxuICAgICAgKTtcbiAgICB9XG5cbiAgICAvLyBUaGlzIF9tZXRob2QgcGFyYW0gaXMgdXNlZCBieSBTeW1mb255IHRvIHNpbXVsYXRlIERFTEVURSBhbmQgUFVUIG1ldGhvZHNcbiAgICBpZiAoIWlzR2V0T3JQb3N0TWV0aG9kKSB7XG4gICAgICAkZm9ybS5hcHBlbmQoJCgnPGlucHV0PicsIHtcbiAgICAgICAgJ3R5cGUnOiAnaGlkZGVuJyxcbiAgICAgICAgJ25hbWUnOiAnX21ldGhvZCcsXG4gICAgICAgICd2YWx1ZSc6IG1ldGhvZCxcbiAgICAgIH0pKTtcbiAgICB9XG5cbiAgICAkZm9ybS5zdWJtaXQoKTtcbiAgfVxufVxuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuLyoqXG4gKiBDbGFzcyBSZWxvYWRMaXN0RXh0ZW5zaW9uIGV4dGVuZHMgZ3JpZCB3aXRoIFwiTGlzdCByZWxvYWRcIiBhY3Rpb25cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgUmVsb2FkTGlzdEV4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRIZWFkZXJDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLWNvbW1vbl9yZWZyZXNoX2xpc3QtZ3JpZC1hY3Rpb24nLCAoKSA9PiB7XG4gICAgICBsb2NhdGlvbi5yZWxvYWQoKTtcbiAgICB9KTtcbiAgfVxufVxuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IFRhYmxlU29ydGluZyBmcm9tICcuLi8uLi8uLi9hcHAvdXRpbHMvdGFibGUtc29ydGluZyc7XG5cbi8qKlxuICogQ2xhc3MgUmVsb2FkTGlzdEV4dGVuc2lvbiBleHRlbmRzIGdyaWQgd2l0aCBcIkxpc3QgcmVsb2FkXCIgYWN0aW9uXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFNvcnRpbmdFeHRlbnNpb24ge1xuICAvKipcbiAgICogRXh0ZW5kIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIGNvbnN0ICRzb3J0YWJsZVRhYmxlID0gZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCd0YWJsZS50YWJsZScpO1xuXG4gICAgbmV3IFRhYmxlU29ydGluZygkc29ydGFibGVUYWJsZSkuYXR0YWNoKCk7XG4gIH1cbn1cbiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBIYW5kbGVzIHN1Ym1pdCBvZiBncmlkIGFjdGlvbnNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgU3VibWl0QnVsa0FjdGlvbkV4dGVuc2lvbiB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHJldHVybiB7XG4gICAgICBleHRlbmQ6IChncmlkKSA9PiB0aGlzLmV4dGVuZChncmlkKSxcbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkIHdpdGggYnVsayBhY3Rpb24gc3VibWl0dGluZ1xuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLWJ1bGstYWN0aW9uLXN1Ym1pdC1idG4nLCAoZXZlbnQpID0+IHtcbiAgICAgIHRoaXMuc3VibWl0KGV2ZW50LCBncmlkKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGUgYnVsayBhY3Rpb24gc3VibWl0dGluZ1xuICAgKlxuICAgKiBAcGFyYW0ge0V2ZW50fSBldmVudFxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIHN1Ym1pdChldmVudCwgZ3JpZCkge1xuICAgIGNvbnN0ICRzdWJtaXRCdG4gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgIGNvbnN0IGNvbmZpcm1NZXNzYWdlID0gJHN1Ym1pdEJ0bi5kYXRhKCdjb25maXJtLW1lc3NhZ2UnKTtcblxuICAgIGlmICh0eXBlb2YgY29uZmlybU1lc3NhZ2UgIT09IFwidW5kZWZpbmVkXCIgJiYgMCA8IGNvbmZpcm1NZXNzYWdlLmxlbmd0aCAmJiAhY29uZmlybShjb25maXJtTWVzc2FnZSkpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBjb25zdCAkZm9ybSA9ICQoJyMnICsgZ3JpZC5nZXRJZCgpICsgJ19maWx0ZXJfZm9ybScpO1xuXG4gICAgJGZvcm0uYXR0cignYWN0aW9uJywgJHN1Ym1pdEJ0bi5kYXRhKCdmb3JtLXVybCcpKTtcbiAgICAkZm9ybS5hdHRyKCdtZXRob2QnLCAkc3VibWl0QnRuLmRhdGEoJ2Zvcm0tbWV0aG9kJykpO1xuICAgICRmb3JtLnN1Ym1pdCgpO1xuICB9XG59XG4iLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgaXMgcmVzcG9uc2libGUgZm9yIGhhbmRsaW5nIEdyaWQgZXZlbnRzXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEdyaWQge1xuICAvKipcbiAgICogR3JpZCBpZFxuICAgKlxuICAgKiBAcGFyYW0ge3N0cmluZ30gaWRcbiAgICovXG4gIGNvbnN0cnVjdG9yKGlkKSB7XG4gICAgdGhpcy5pZCA9IGlkO1xuICAgIHRoaXMuJGNvbnRhaW5lciA9ICQoJyMnICsgdGhpcy5pZCArICdfZ3JpZCcpO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBncmlkIGlkXG4gICAqXG4gICAqIEByZXR1cm5zIHtzdHJpbmd9XG4gICAqL1xuICBnZXRJZCgpIHtcbiAgICByZXR1cm4gdGhpcy5pZDtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgZ3JpZCBjb250YWluZXJcbiAgICpcbiAgICogQHJldHVybnMge2pRdWVyeX1cbiAgICovXG4gIGdldENvbnRhaW5lcigpIHtcbiAgICByZXR1cm4gdGhpcy4kY29udGFpbmVyO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBncmlkIGhlYWRlciBjb250YWluZXJcbiAgICpcbiAgICogQHJldHVybnMge2pRdWVyeX1cbiAgICovXG4gIGdldEhlYWRlckNvbnRhaW5lcigpIHtcbiAgICByZXR1cm4gdGhpcy4kY29udGFpbmVyLmNsb3Nlc3QoJy5qcy1ncmlkLXBhbmVsJykuZmluZCgnLmpzLWdyaWQtaGVhZGVyJyk7XG4gIH1cblxuICAvKipcbiAgICogRXh0ZW5kIGdyaWQgd2l0aCBleHRlcm5hbCBleHRlbnNpb25zXG4gICAqXG4gICAqIEBwYXJhbSB7b2JqZWN0fSBleHRlbnNpb25cbiAgICovXG4gIGFkZEV4dGVuc2lvbihleHRlbnNpb24pIHtcbiAgICBleHRlbnNpb24uZXh0ZW5kKHRoaXMpO1xuICB9XG59XG4iLCIvKipcbiAqIDIwMDctMjAxOCBQcmVzdGFTaG9wXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE4IFByZXN0YVNob3AgU0FcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IEdyaWQgZnJvbSAnLi4vLi4vY29tcG9uZW50cy9ncmlkL2dyaWQnO1xuaW1wb3J0IFNvcnRpbmdFeHRlbnNpb24gZnJvbSAnLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9zb3J0aW5nLWV4dGVuc2lvbic7XG5pbXBvcnQgU3VibWl0Um93QWN0aW9uRXh0ZW5zaW9uIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYWN0aW9uL3Jvdy9zdWJtaXQtcm93LWFjdGlvbi1leHRlbnNpb24nO1xuaW1wb3J0IEZpbHRlcnNSZXNldEV4dGVuc2lvbiBmcm9tICcuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2ZpbHRlcnMtcmVzZXQtZXh0ZW5zaW9uJztcbmltcG9ydCBSZWxvYWRMaXN0QWN0aW9uRXh0ZW5zaW9uIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vcmVsb2FkLWxpc3QtZXh0ZW5zaW9uJztcbmltcG9ydCBFeHBvcnRUb1NxbE1hbmFnZXJFeHRlbnNpb24gZnJvbSAnLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9leHBvcnQtdG8tc3FsLW1hbmFnZXItZXh0ZW5zaW9uJztcbmltcG9ydCBMaW5rUm93QWN0aW9uRXh0ZW5zaW9uIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vbGluay1yb3ctYWN0aW9uLWV4dGVuc2lvbic7XG5pbXBvcnQgU3VibWl0QnVsa0V4dGVuc2lvbiBmcm9tICcuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3N1Ym1pdC1idWxrLWFjdGlvbi1leHRlbnNpb24nO1xuaW1wb3J0IEJ1bGtBY3Rpb25DaGVja2JveEV4dGVuc2lvbiBmcm9tICcuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2J1bGstYWN0aW9uLWNoZWNrYm94LWV4dGVuc2lvbic7XG5pbXBvcnQgQ29sdW1uVG9nZ2xpbmdFeHRlbnNpb24gZnJvbSAnLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9jb2x1bW4tdG9nZ2xpbmctZXh0ZW5zaW9uJztcbmltcG9ydCBQb3NpdGlvbkV4dGVuc2lvbiBmcm9tICcuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3Bvc2l0aW9uLWV4dGVuc2lvbic7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuJCgoKSA9PiB7XG4gIGNvbnN0IGNtc0NhdGVnb3J5ID0gbmV3IEdyaWQoJ2Ntc19wYWdlX2NhdGVnb3J5Jyk7XG5cbiAgY21zQ2F0ZWdvcnkuYWRkRXh0ZW5zaW9uKG5ldyBSZWxvYWRMaXN0QWN0aW9uRXh0ZW5zaW9uKCkpO1xuICBjbXNDYXRlZ29yeS5hZGRFeHRlbnNpb24obmV3IEV4cG9ydFRvU3FsTWFuYWdlckV4dGVuc2lvbigpKTtcbiAgY21zQ2F0ZWdvcnkuYWRkRXh0ZW5zaW9uKG5ldyBGaWx0ZXJzUmVzZXRFeHRlbnNpb24oKSk7XG4gIGNtc0NhdGVnb3J5LmFkZEV4dGVuc2lvbihuZXcgU29ydGluZ0V4dGVuc2lvbigpKTtcbiAgY21zQ2F0ZWdvcnkuYWRkRXh0ZW5zaW9uKG5ldyBMaW5rUm93QWN0aW9uRXh0ZW5zaW9uKCkpO1xuICBjbXNDYXRlZ29yeS5hZGRFeHRlbnNpb24obmV3IFN1Ym1pdEJ1bGtFeHRlbnNpb24oKSk7XG4gIGNtc0NhdGVnb3J5LmFkZEV4dGVuc2lvbihuZXcgQnVsa0FjdGlvbkNoZWNrYm94RXh0ZW5zaW9uKCkpO1xuICBjbXNDYXRlZ29yeS5hZGRFeHRlbnNpb24obmV3IFN1Ym1pdFJvd0FjdGlvbkV4dGVuc2lvbigpKTtcbiAgY21zQ2F0ZWdvcnkuYWRkRXh0ZW5zaW9uKG5ldyBDb2x1bW5Ub2dnbGluZ0V4dGVuc2lvbigpKTtcbiAgY21zQ2F0ZWdvcnkuYWRkRXh0ZW5zaW9uKG5ldyBQb3NpdGlvbkV4dGVuc2lvbigpKTtcbn0pO1xuIiwiLyohIGpxdWVyeS50YWJsZWRuZC5qcyAzMC0xMi0yMDE3ICovXG4hZnVuY3Rpb24oYSxiLGMsZCl7dmFyIGU9XCJ0b3VjaHN0YXJ0IG1vdXNlZG93blwiLGY9XCJ0b3VjaG1vdmUgbW91c2Vtb3ZlXCIsZz1cInRvdWNoZW5kIG1vdXNldXBcIjthKGMpLnJlYWR5KGZ1bmN0aW9uKCl7ZnVuY3Rpb24gYihhKXtmb3IodmFyIGI9e30sYz1hLm1hdGNoKC8oW147Ol0rKS9nKXx8W107Yy5sZW5ndGg7KWJbYy5zaGlmdCgpXT1jLnNoaWZ0KCkudHJpbSgpO3JldHVybiBifWEoXCJ0YWJsZVwiKS5lYWNoKGZ1bmN0aW9uKCl7XCJkbmRcIj09PWEodGhpcykuZGF0YShcInRhYmxlXCIpJiZhKHRoaXMpLnRhYmxlRG5EKHtvbkRyYWdTdHlsZTphKHRoaXMpLmRhdGEoXCJvbmRyYWdzdHlsZVwiKSYmYihhKHRoaXMpLmRhdGEoXCJvbmRyYWdzdHlsZVwiKSl8fG51bGwsb25Ecm9wU3R5bGU6YSh0aGlzKS5kYXRhKFwib25kcm9wc3R5bGVcIikmJmIoYSh0aGlzKS5kYXRhKFwib25kcm9wc3R5bGVcIikpfHxudWxsLG9uRHJhZ0NsYXNzOmEodGhpcykuZGF0YShcIm9uZHJhZ2NsYXNzXCIpPT09ZCYmXCJ0RG5EX3doaWxlRHJhZ1wifHxhKHRoaXMpLmRhdGEoXCJvbmRyYWdjbGFzc1wiKSxvbkRyb3A6YSh0aGlzKS5kYXRhKFwib25kcm9wXCIpJiZuZXcgRnVuY3Rpb24oXCJ0YWJsZVwiLFwicm93XCIsYSh0aGlzKS5kYXRhKFwib25kcm9wXCIpKSxvbkRyYWdTdGFydDphKHRoaXMpLmRhdGEoXCJvbmRyYWdzdGFydFwiKSYmbmV3IEZ1bmN0aW9uKFwidGFibGVcIixcInJvd1wiLGEodGhpcykuZGF0YShcIm9uZHJhZ3N0YXJ0XCIpKSxvbkRyYWdTdG9wOmEodGhpcykuZGF0YShcIm9uZHJhZ3N0b3BcIikmJm5ldyBGdW5jdGlvbihcInRhYmxlXCIsXCJyb3dcIixhKHRoaXMpLmRhdGEoXCJvbmRyYWdzdG9wXCIpKSxzY3JvbGxBbW91bnQ6YSh0aGlzKS5kYXRhKFwic2Nyb2xsYW1vdW50XCIpfHw1LHNlbnNpdGl2aXR5OmEodGhpcykuZGF0YShcInNlbnNpdGl2aXR5XCIpfHwxMCxoaWVyYXJjaHlMZXZlbDphKHRoaXMpLmRhdGEoXCJoaWVyYXJjaHlsZXZlbFwiKXx8MCxpbmRlbnRBcnRpZmFjdDphKHRoaXMpLmRhdGEoXCJpbmRlbnRhcnRpZmFjdFwiKXx8JzxkaXYgY2xhc3M9XCJpbmRlbnRcIj4mbmJzcDs8L2Rpdj4nLGF1dG9XaWR0aEFkanVzdDphKHRoaXMpLmRhdGEoXCJhdXRvd2lkdGhhZGp1c3RcIil8fCEwLGF1dG9DbGVhblJlbGF0aW9uczphKHRoaXMpLmRhdGEoXCJhdXRvY2xlYW5yZWxhdGlvbnNcIil8fCEwLGpzb25QcmV0aWZ5U2VwYXJhdG9yOmEodGhpcykuZGF0YShcImpzb25wcmV0aWZ5c2VwYXJhdG9yXCIpfHxcIlxcdFwiLHNlcmlhbGl6ZVJlZ2V4cDphKHRoaXMpLmRhdGEoXCJzZXJpYWxpemVyZWdleHBcIikmJm5ldyBSZWdFeHAoYSh0aGlzKS5kYXRhKFwic2VyaWFsaXplcmVnZXhwXCIpKXx8L1teXFwtXSokLyxzZXJpYWxpemVQYXJhbU5hbWU6YSh0aGlzKS5kYXRhKFwic2VyaWFsaXplcGFyYW1uYW1lXCIpfHwhMSxkcmFnSGFuZGxlOmEodGhpcykuZGF0YShcImRyYWdoYW5kbGVcIil8fG51bGx9KX0pfSksalF1ZXJ5LnRhYmxlRG5EPXtjdXJyZW50VGFibGU6bnVsbCxkcmFnT2JqZWN0Om51bGwsbW91c2VPZmZzZXQ6bnVsbCxvbGRYOjAsb2xkWTowLGJ1aWxkOmZ1bmN0aW9uKGIpe3JldHVybiB0aGlzLmVhY2goZnVuY3Rpb24oKXt0aGlzLnRhYmxlRG5EQ29uZmlnPWEuZXh0ZW5kKHtvbkRyYWdTdHlsZTpudWxsLG9uRHJvcFN0eWxlOm51bGwsb25EcmFnQ2xhc3M6XCJ0RG5EX3doaWxlRHJhZ1wiLG9uRHJvcDpudWxsLG9uRHJhZ1N0YXJ0Om51bGwsb25EcmFnU3RvcDpudWxsLHNjcm9sbEFtb3VudDo1LHNlbnNpdGl2aXR5OjEwLGhpZXJhcmNoeUxldmVsOjAsaW5kZW50QXJ0aWZhY3Q6JzxkaXYgY2xhc3M9XCJpbmRlbnRcIj4mbmJzcDs8L2Rpdj4nLGF1dG9XaWR0aEFkanVzdDohMCxhdXRvQ2xlYW5SZWxhdGlvbnM6ITAsanNvblByZXRpZnlTZXBhcmF0b3I6XCJcXHRcIixzZXJpYWxpemVSZWdleHA6L1teXFwtXSokLyxzZXJpYWxpemVQYXJhbU5hbWU6ITEsZHJhZ0hhbmRsZTpudWxsfSxifHx7fSksYS50YWJsZURuRC5tYWtlRHJhZ2dhYmxlKHRoaXMpLHRoaXMudGFibGVEbkRDb25maWcuaGllcmFyY2h5TGV2ZWwmJmEudGFibGVEbkQubWFrZUluZGVudGVkKHRoaXMpfSksdGhpc30sbWFrZUluZGVudGVkOmZ1bmN0aW9uKGIpe3ZhciBjLGQsZT1iLnRhYmxlRG5EQ29uZmlnLGY9Yi5yb3dzLGc9YShmKS5maXJzdCgpLmZpbmQoXCJ0ZDpmaXJzdFwiKVswXSxoPTAsaT0wO2lmKGEoYikuaGFzQ2xhc3MoXCJpbmR0ZFwiKSlyZXR1cm4gbnVsbDtkPWEoYikuYWRkQ2xhc3MoXCJpbmR0ZFwiKS5hdHRyKFwic3R5bGVcIiksYShiKS5jc3Moe3doaXRlU3BhY2U6XCJub3dyYXBcIn0pO2Zvcih2YXIgaj0wO2o8Zi5sZW5ndGg7aisrKWk8YShmW2pdKS5maW5kKFwidGQ6Zmlyc3RcIikudGV4dCgpLmxlbmd0aCYmKGk9YShmW2pdKS5maW5kKFwidGQ6Zmlyc3RcIikudGV4dCgpLmxlbmd0aCxjPWopO2ZvcihhKGcpLmNzcyh7d2lkdGg6XCJhdXRvXCJ9KSxqPTA7ajxlLmhpZXJhcmNoeUxldmVsO2orKylhKGZbY10pLmZpbmQoXCJ0ZDpmaXJzdFwiKS5wcmVwZW5kKGUuaW5kZW50QXJ0aWZhY3QpO2ZvcihnJiZhKGcpLmNzcyh7d2lkdGg6Zy5vZmZzZXRXaWR0aH0pLGQmJmEoYikuY3NzKGQpLGo9MDtqPGUuaGllcmFyY2h5TGV2ZWw7aisrKWEoZltjXSkuZmluZChcInRkOmZpcnN0XCIpLmNoaWxkcmVuKFwiOmZpcnN0XCIpLnJlbW92ZSgpO3JldHVybiBlLmhpZXJhcmNoeUxldmVsJiZhKGYpLmVhY2goZnVuY3Rpb24oKXsoaD1hKHRoaXMpLmRhdGEoXCJsZXZlbFwiKXx8MCk8PWUuaGllcmFyY2h5TGV2ZWwmJmEodGhpcykuZGF0YShcImxldmVsXCIsaCl8fGEodGhpcykuZGF0YShcImxldmVsXCIsMCk7Zm9yKHZhciBiPTA7YjxhKHRoaXMpLmRhdGEoXCJsZXZlbFwiKTtiKyspYSh0aGlzKS5maW5kKFwidGQ6Zmlyc3RcIikucHJlcGVuZChlLmluZGVudEFydGlmYWN0KX0pLHRoaXN9LG1ha2VEcmFnZ2FibGU6ZnVuY3Rpb24oYil7dmFyIGM9Yi50YWJsZURuRENvbmZpZztjLmRyYWdIYW5kbGUmJmEoYy5kcmFnSGFuZGxlLGIpLmVhY2goZnVuY3Rpb24oKXthKHRoaXMpLmJpbmQoZSxmdW5jdGlvbihkKXtyZXR1cm4gYS50YWJsZURuRC5pbml0aWFsaXNlRHJhZyhhKHRoaXMpLnBhcmVudHMoXCJ0clwiKVswXSxiLHRoaXMsZCxjKSwhMX0pfSl8fGEoYi5yb3dzKS5lYWNoKGZ1bmN0aW9uKCl7YSh0aGlzKS5oYXNDbGFzcyhcIm5vZHJhZ1wiKT9hKHRoaXMpLmNzcyhcImN1cnNvclwiLFwiXCIpOmEodGhpcykuYmluZChlLGZ1bmN0aW9uKGQpe2lmKFwiVERcIj09PWQudGFyZ2V0LnRhZ05hbWUpcmV0dXJuIGEudGFibGVEbkQuaW5pdGlhbGlzZURyYWcodGhpcyxiLHRoaXMsZCxjKSwhMX0pLmNzcyhcImN1cnNvclwiLFwibW92ZVwiKX0pfSxjdXJyZW50T3JkZXI6ZnVuY3Rpb24oKXt2YXIgYj10aGlzLmN1cnJlbnRUYWJsZS5yb3dzO3JldHVybiBhLm1hcChiLGZ1bmN0aW9uKGIpe3JldHVybihhKGIpLmRhdGEoXCJsZXZlbFwiKStiLmlkKS5yZXBsYWNlKC9cXHMvZyxcIlwiKX0pLmpvaW4oXCJcIil9LGluaXRpYWxpc2VEcmFnOmZ1bmN0aW9uKGIsZCxlLGgsaSl7dGhpcy5kcmFnT2JqZWN0PWIsdGhpcy5jdXJyZW50VGFibGU9ZCx0aGlzLm1vdXNlT2Zmc2V0PXRoaXMuZ2V0TW91c2VPZmZzZXQoZSxoKSx0aGlzLm9yaWdpbmFsT3JkZXI9dGhpcy5jdXJyZW50T3JkZXIoKSxhKGMpLmJpbmQoZix0aGlzLm1vdXNlbW92ZSkuYmluZChnLHRoaXMubW91c2V1cCksaS5vbkRyYWdTdGFydCYmaS5vbkRyYWdTdGFydChkLGUpfSx1cGRhdGVUYWJsZXM6ZnVuY3Rpb24oKXt0aGlzLmVhY2goZnVuY3Rpb24oKXt0aGlzLnRhYmxlRG5EQ29uZmlnJiZhLnRhYmxlRG5ELm1ha2VEcmFnZ2FibGUodGhpcyl9KX0sbW91c2VDb29yZHM6ZnVuY3Rpb24oYSl7cmV0dXJuIGEub3JpZ2luYWxFdmVudC5jaGFuZ2VkVG91Y2hlcz97eDphLm9yaWdpbmFsRXZlbnQuY2hhbmdlZFRvdWNoZXNbMF0uY2xpZW50WCx5OmEub3JpZ2luYWxFdmVudC5jaGFuZ2VkVG91Y2hlc1swXS5jbGllbnRZfTphLnBhZ2VYfHxhLnBhZ2VZP3t4OmEucGFnZVgseTphLnBhZ2VZfTp7eDphLmNsaWVudFgrYy5ib2R5LnNjcm9sbExlZnQtYy5ib2R5LmNsaWVudExlZnQseTphLmNsaWVudFkrYy5ib2R5LnNjcm9sbFRvcC1jLmJvZHkuY2xpZW50VG9wfX0sZ2V0TW91c2VPZmZzZXQ6ZnVuY3Rpb24oYSxjKXt2YXIgZCxlO3JldHVybiBjPWN8fGIuZXZlbnQsZT10aGlzLmdldFBvc2l0aW9uKGEpLGQ9dGhpcy5tb3VzZUNvb3JkcyhjKSx7eDpkLngtZS54LHk6ZC55LWUueX19LGdldFBvc2l0aW9uOmZ1bmN0aW9uKGEpe3ZhciBiPTAsYz0wO2ZvcigwPT09YS5vZmZzZXRIZWlnaHQmJihhPWEuZmlyc3RDaGlsZCk7YS5vZmZzZXRQYXJlbnQ7KWIrPWEub2Zmc2V0TGVmdCxjKz1hLm9mZnNldFRvcCxhPWEub2Zmc2V0UGFyZW50O3JldHVybiBiKz1hLm9mZnNldExlZnQsYys9YS5vZmZzZXRUb3Ase3g6Yix5OmN9fSxhdXRvU2Nyb2xsOmZ1bmN0aW9uKGEpe3ZhciBkPXRoaXMuY3VycmVudFRhYmxlLnRhYmxlRG5EQ29uZmlnLGU9Yi5wYWdlWU9mZnNldCxmPWIuaW5uZXJIZWlnaHQ/Yi5pbm5lckhlaWdodDpjLmRvY3VtZW50RWxlbWVudC5jbGllbnRIZWlnaHQ/Yy5kb2N1bWVudEVsZW1lbnQuY2xpZW50SGVpZ2h0OmMuYm9keS5jbGllbnRIZWlnaHQ7Yy5hbGwmJih2b2lkIDAhPT1jLmNvbXBhdE1vZGUmJlwiQmFja0NvbXBhdFwiIT09Yy5jb21wYXRNb2RlP2U9Yy5kb2N1bWVudEVsZW1lbnQuc2Nyb2xsVG9wOnZvaWQgMCE9PWMuYm9keSYmKGU9Yy5ib2R5LnNjcm9sbFRvcCkpLGEueS1lPGQuc2Nyb2xsQW1vdW50JiZiLnNjcm9sbEJ5KDAsLWQuc2Nyb2xsQW1vdW50KXx8Zi0oYS55LWUpPGQuc2Nyb2xsQW1vdW50JiZiLnNjcm9sbEJ5KDAsZC5zY3JvbGxBbW91bnQpfSxtb3ZlVmVydGljbGU6ZnVuY3Rpb24oYSxiKXswIT09YS52ZXJ0aWNhbCYmYiYmdGhpcy5kcmFnT2JqZWN0IT09YiYmdGhpcy5kcmFnT2JqZWN0LnBhcmVudE5vZGU9PT1iLnBhcmVudE5vZGUmJigwPmEudmVydGljYWwmJnRoaXMuZHJhZ09iamVjdC5wYXJlbnROb2RlLmluc2VydEJlZm9yZSh0aGlzLmRyYWdPYmplY3QsYi5uZXh0U2libGluZyl8fDA8YS52ZXJ0aWNhbCYmdGhpcy5kcmFnT2JqZWN0LnBhcmVudE5vZGUuaW5zZXJ0QmVmb3JlKHRoaXMuZHJhZ09iamVjdCxiKSl9LG1vdmVIb3Jpem9udGFsOmZ1bmN0aW9uKGIsYyl7dmFyIGQsZT10aGlzLmN1cnJlbnRUYWJsZS50YWJsZURuRENvbmZpZztpZighZS5oaWVyYXJjaHlMZXZlbHx8MD09PWIuaG9yaXpvbnRhbHx8IWN8fHRoaXMuZHJhZ09iamVjdCE9PWMpcmV0dXJuIG51bGw7ZD1hKGMpLmRhdGEoXCJsZXZlbFwiKSwwPGIuaG9yaXpvbnRhbCYmZD4wJiZhKGMpLmZpbmQoXCJ0ZDpmaXJzdFwiKS5jaGlsZHJlbihcIjpmaXJzdFwiKS5yZW1vdmUoKSYmYShjKS5kYXRhKFwibGV2ZWxcIiwtLWQpLDA+Yi5ob3Jpem9udGFsJiZkPGUuaGllcmFyY2h5TGV2ZWwmJmEoYykucHJldigpLmRhdGEoXCJsZXZlbFwiKT49ZCYmYShjKS5jaGlsZHJlbihcIjpmaXJzdFwiKS5wcmVwZW5kKGUuaW5kZW50QXJ0aWZhY3QpJiZhKGMpLmRhdGEoXCJsZXZlbFwiLCsrZCl9LG1vdXNlbW92ZTpmdW5jdGlvbihiKXt2YXIgYyxkLGUsZixnLGg9YShhLnRhYmxlRG5ELmRyYWdPYmplY3QpLGk9YS50YWJsZURuRC5jdXJyZW50VGFibGUudGFibGVEbkRDb25maWc7cmV0dXJuIGImJmIucHJldmVudERlZmF1bHQoKSwhIWEudGFibGVEbkQuZHJhZ09iamVjdCYmKFwidG91Y2htb3ZlXCI9PT1iLnR5cGUmJmV2ZW50LnByZXZlbnREZWZhdWx0KCksaS5vbkRyYWdDbGFzcyYmaC5hZGRDbGFzcyhpLm9uRHJhZ0NsYXNzKXx8aC5jc3MoaS5vbkRyYWdTdHlsZSksZD1hLnRhYmxlRG5ELm1vdXNlQ29vcmRzKGIpLGY9ZC54LWEudGFibGVEbkQubW91c2VPZmZzZXQueCxnPWQueS1hLnRhYmxlRG5ELm1vdXNlT2Zmc2V0LnksYS50YWJsZURuRC5hdXRvU2Nyb2xsKGQpLGM9YS50YWJsZURuRC5maW5kRHJvcFRhcmdldFJvdyhoLGcpLGU9YS50YWJsZURuRC5maW5kRHJhZ0RpcmVjdGlvbihmLGcpLGEudGFibGVEbkQubW92ZVZlcnRpY2xlKGUsYyksYS50YWJsZURuRC5tb3ZlSG9yaXpvbnRhbChlLGMpLCExKX0sZmluZERyYWdEaXJlY3Rpb246ZnVuY3Rpb24oYSxiKXt2YXIgYz10aGlzLmN1cnJlbnRUYWJsZS50YWJsZURuRENvbmZpZy5zZW5zaXRpdml0eSxkPXRoaXMub2xkWCxlPXRoaXMub2xkWSxmPWQtYyxnPWQrYyxoPWUtYyxpPWUrYyxqPXtob3Jpem9udGFsOmE+PWYmJmE8PWc/MDphPmQ/LTE6MSx2ZXJ0aWNhbDpiPj1oJiZiPD1pPzA6Yj5lPy0xOjF9O3JldHVybiAwIT09ai5ob3Jpem9udGFsJiYodGhpcy5vbGRYPWEpLDAhPT1qLnZlcnRpY2FsJiYodGhpcy5vbGRZPWIpLGp9LGZpbmREcm9wVGFyZ2V0Um93OmZ1bmN0aW9uKGIsYyl7Zm9yKHZhciBkPTAsZT10aGlzLmN1cnJlbnRUYWJsZS5yb3dzLGY9dGhpcy5jdXJyZW50VGFibGUudGFibGVEbkRDb25maWcsZz0wLGg9bnVsbCxpPTA7aTxlLmxlbmd0aDtpKyspaWYoaD1lW2ldLGc9dGhpcy5nZXRQb3NpdGlvbihoKS55LGQ9cGFyc2VJbnQoaC5vZmZzZXRIZWlnaHQpLzIsMD09PWgub2Zmc2V0SGVpZ2h0JiYoZz10aGlzLmdldFBvc2l0aW9uKGguZmlyc3RDaGlsZCkueSxkPXBhcnNlSW50KGguZmlyc3RDaGlsZC5vZmZzZXRIZWlnaHQpLzIpLGM+Zy1kJiZjPGcrZClyZXR1cm4gYi5pcyhoKXx8Zi5vbkFsbG93RHJvcCYmIWYub25BbGxvd0Ryb3AoYixoKXx8YShoKS5oYXNDbGFzcyhcIm5vZHJvcFwiKT9udWxsOmg7cmV0dXJuIG51bGx9LHByb2Nlc3NNb3VzZXVwOmZ1bmN0aW9uKCl7aWYoIXRoaXMuY3VycmVudFRhYmxlfHwhdGhpcy5kcmFnT2JqZWN0KXJldHVybiBudWxsO3ZhciBiPXRoaXMuY3VycmVudFRhYmxlLnRhYmxlRG5EQ29uZmlnLGQ9dGhpcy5kcmFnT2JqZWN0LGU9MCxoPTA7YShjKS51bmJpbmQoZix0aGlzLm1vdXNlbW92ZSkudW5iaW5kKGcsdGhpcy5tb3VzZXVwKSxiLmhpZXJhcmNoeUxldmVsJiZiLmF1dG9DbGVhblJlbGF0aW9ucyYmYSh0aGlzLmN1cnJlbnRUYWJsZS5yb3dzKS5maXJzdCgpLmZpbmQoXCJ0ZDpmaXJzdFwiKS5jaGlsZHJlbigpLmVhY2goZnVuY3Rpb24oKXsoaD1hKHRoaXMpLnBhcmVudHMoXCJ0cjpmaXJzdFwiKS5kYXRhKFwibGV2ZWxcIikpJiZhKHRoaXMpLnBhcmVudHMoXCJ0cjpmaXJzdFwiKS5kYXRhKFwibGV2ZWxcIiwtLWgpJiZhKHRoaXMpLnJlbW92ZSgpfSkmJmIuaGllcmFyY2h5TGV2ZWw+MSYmYSh0aGlzLmN1cnJlbnRUYWJsZS5yb3dzKS5lYWNoKGZ1bmN0aW9uKCl7aWYoKGg9YSh0aGlzKS5kYXRhKFwibGV2ZWxcIikpPjEpZm9yKGU9YSh0aGlzKS5wcmV2KCkuZGF0YShcImxldmVsXCIpO2g+ZSsxOylhKHRoaXMpLmZpbmQoXCJ0ZDpmaXJzdFwiKS5jaGlsZHJlbihcIjpmaXJzdFwiKS5yZW1vdmUoKSxhKHRoaXMpLmRhdGEoXCJsZXZlbFwiLC0taCl9KSxiLm9uRHJhZ0NsYXNzJiZhKGQpLnJlbW92ZUNsYXNzKGIub25EcmFnQ2xhc3MpfHxhKGQpLmNzcyhiLm9uRHJvcFN0eWxlKSx0aGlzLmRyYWdPYmplY3Q9bnVsbCxiLm9uRHJvcCYmdGhpcy5vcmlnaW5hbE9yZGVyIT09dGhpcy5jdXJyZW50T3JkZXIoKSYmYShkKS5oaWRlKCkuZmFkZUluKFwiZmFzdFwiKSYmYi5vbkRyb3AodGhpcy5jdXJyZW50VGFibGUsZCksYi5vbkRyYWdTdG9wJiZiLm9uRHJhZ1N0b3AodGhpcy5jdXJyZW50VGFibGUsZCksdGhpcy5jdXJyZW50VGFibGU9bnVsbH0sbW91c2V1cDpmdW5jdGlvbihiKXtyZXR1cm4gYiYmYi5wcmV2ZW50RGVmYXVsdCgpLGEudGFibGVEbkQucHJvY2Vzc01vdXNldXAoKSwhMX0sanNvbml6ZTpmdW5jdGlvbihhKXt2YXIgYj10aGlzLmN1cnJlbnRUYWJsZTtyZXR1cm4gYT9KU09OLnN0cmluZ2lmeSh0aGlzLnRhYmxlRGF0YShiKSxudWxsLGIudGFibGVEbkRDb25maWcuanNvblByZXRpZnlTZXBhcmF0b3IpOkpTT04uc3RyaW5naWZ5KHRoaXMudGFibGVEYXRhKGIpKX0sc2VyaWFsaXplOmZ1bmN0aW9uKCl7cmV0dXJuIGEucGFyYW0odGhpcy50YWJsZURhdGEodGhpcy5jdXJyZW50VGFibGUpKX0sc2VyaWFsaXplVGFibGU6ZnVuY3Rpb24oYSl7Zm9yKHZhciBiPVwiXCIsYz1hLnRhYmxlRG5EQ29uZmlnLnNlcmlhbGl6ZVBhcmFtTmFtZXx8YS5pZCxkPWEucm93cyxlPTA7ZTxkLmxlbmd0aDtlKyspe2IubGVuZ3RoPjAmJihiKz1cIiZcIik7dmFyIGY9ZFtlXS5pZDtmJiZhLnRhYmxlRG5EQ29uZmlnJiZhLnRhYmxlRG5EQ29uZmlnLnNlcmlhbGl6ZVJlZ2V4cCYmKGY9Zi5tYXRjaChhLnRhYmxlRG5EQ29uZmlnLnNlcmlhbGl6ZVJlZ2V4cClbMF0sYis9YytcIltdPVwiK2YpfXJldHVybiBifSxzZXJpYWxpemVUYWJsZXM6ZnVuY3Rpb24oKXt2YXIgYj1bXTtyZXR1cm4gYShcInRhYmxlXCIpLmVhY2goZnVuY3Rpb24oKXt0aGlzLmlkJiZiLnB1c2goYS5wYXJhbShhLnRhYmxlRG5ELnRhYmxlRGF0YSh0aGlzKSkpfSksYi5qb2luKFwiJlwiKX0sdGFibGVEYXRhOmZ1bmN0aW9uKGIpe3ZhciBjLGQsZSxmLGc9Yi50YWJsZURuRENvbmZpZyxoPVtdLGk9MCxqPTAsaz1udWxsLGw9e307aWYoYnx8KGI9dGhpcy5jdXJyZW50VGFibGUpLCFifHwhYi5yb3dzfHwhYi5yb3dzLmxlbmd0aClyZXR1cm57ZXJyb3I6e2NvZGU6NTAwLG1lc3NhZ2U6XCJOb3QgYSB2YWxpZCB0YWJsZS5cIn19O2lmKCFiLmlkJiYhZy5zZXJpYWxpemVQYXJhbU5hbWUpcmV0dXJue2Vycm9yOntjb2RlOjUwMCxtZXNzYWdlOlwiTm8gc2VyaWFsaXphYmxlIHVuaXF1ZSBpZCBwcm92aWRlZC5cIn19O2Y9Zy5hdXRvQ2xlYW5SZWxhdGlvbnMmJmIucm93c3x8YS5tYWtlQXJyYXkoYi5yb3dzKSxkPWcuc2VyaWFsaXplUGFyYW1OYW1lfHxiLmlkLGU9ZCxjPWZ1bmN0aW9uKGEpe3JldHVybiBhJiZnJiZnLnNlcmlhbGl6ZVJlZ2V4cD9hLm1hdGNoKGcuc2VyaWFsaXplUmVnZXhwKVswXTphfSxsW2VdPVtdLCFnLmF1dG9DbGVhblJlbGF0aW9ucyYmYShmWzBdKS5kYXRhKFwibGV2ZWxcIikmJmYudW5zaGlmdCh7aWQ6XCJ1bmRlZmluZWRcIn0pO2Zvcih2YXIgbT0wO208Zi5sZW5ndGg7bSsrKWlmKGcuaGllcmFyY2h5TGV2ZWwpe2lmKDA9PT0oaj1hKGZbbV0pLmRhdGEoXCJsZXZlbFwiKXx8MCkpZT1kLGg9W107ZWxzZSBpZihqPmkpaC5wdXNoKFtlLGldKSxlPWMoZlttLTFdLmlkKTtlbHNlIGlmKGo8aSlmb3IodmFyIG49MDtuPGgubGVuZ3RoO24rKyloW25dWzFdPT09aiYmKGU9aFtuXVswXSksaFtuXVsxXT49aSYmKGhbbl1bMV09MCk7aT1qLGEuaXNBcnJheShsW2VdKXx8KGxbZV09W10pLGs9YyhmW21dLmlkKSxrJiZsW2VdLnB1c2goayl9ZWxzZShrPWMoZlttXS5pZCkpJiZsW2VdLnB1c2goayk7cmV0dXJuIGx9fSxqUXVlcnkuZm4uZXh0ZW5kKHt0YWJsZURuRDphLnRhYmxlRG5ELmJ1aWxkLHRhYmxlRG5EVXBkYXRlOmEudGFibGVEbkQudXBkYXRlVGFibGVzLHRhYmxlRG5EU2VyaWFsaXplOmEucHJveHkoYS50YWJsZURuRC5zZXJpYWxpemUsYS50YWJsZURuRCksdGFibGVEbkRTZXJpYWxpemVBbGw6YS50YWJsZURuRC5zZXJpYWxpemVUYWJsZXMsdGFibGVEbkREYXRhOmEucHJveHkoYS50YWJsZURuRC50YWJsZURhdGEsYS50YWJsZURuRCl9KX0oalF1ZXJ5LHdpbmRvdyx3aW5kb3cuZG9jdW1lbnQpOyIsInZhciBnO1xuXG4vLyBUaGlzIHdvcmtzIGluIG5vbi1zdHJpY3QgbW9kZVxuZyA9IChmdW5jdGlvbigpIHtcblx0cmV0dXJuIHRoaXM7XG59KSgpO1xuXG50cnkge1xuXHQvLyBUaGlzIHdvcmtzIGlmIGV2YWwgaXMgYWxsb3dlZCAoc2VlIENTUClcblx0ZyA9IGcgfHwgbmV3IEZ1bmN0aW9uKFwicmV0dXJuIHRoaXNcIikoKTtcbn0gY2F0Y2ggKGUpIHtcblx0Ly8gVGhpcyB3b3JrcyBpZiB0aGUgd2luZG93IHJlZmVyZW5jZSBpcyBhdmFpbGFibGVcblx0aWYgKHR5cGVvZiB3aW5kb3cgPT09IFwib2JqZWN0XCIpIGcgPSB3aW5kb3c7XG59XG5cbi8vIGcgY2FuIHN0aWxsIGJlIHVuZGVmaW5lZCwgYnV0IG5vdGhpbmcgdG8gZG8gYWJvdXQgaXQuLi5cbi8vIFdlIHJldHVybiB1bmRlZmluZWQsIGluc3RlYWQgb2Ygbm90aGluZyBoZXJlLCBzbyBpdCdzXG4vLyBlYXNpZXIgdG8gaGFuZGxlIHRoaXMgY2FzZS4gaWYoIWdsb2JhbCkgeyAuLi59XG5cbm1vZHVsZS5leHBvcnRzID0gZztcbiIsIm1vZHVsZS5leHBvcnRzID0galF1ZXJ5OyJdLCJzb3VyY2VSb290IjoiIn0=