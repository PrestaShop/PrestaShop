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
/******/ 	return __webpack_require__(__webpack_require__.s = "./js/pages/customer/index.js");
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

/***/ "./js/components/choice-table.js":
/*!***************************************!*\
  !*** ./js/components/choice-table.js ***!
  \***************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return ChoiceTable; });
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
 * ChoiceTable is responsible for managing common actions in choice table form type
 */

var ChoiceTable =
/*#__PURE__*/
function () {
  /**
   * Init constructor
   */
  function ChoiceTable() {
    var _this = this;

    _classCallCheck(this, ChoiceTable);

    $(document).on('change', '.js-choice-table-select-all', function (e) {
      _this.handleSelectAll(e);
    });
  }
  /**
   * Check/uncheck all boxes in table
   *
   * @param {Event} event
   */


  _createClass(ChoiceTable, [{
    key: "handleSelectAll",
    value: function handleSelectAll(event) {
      var $selectAllCheckboxes = $(event.target);
      var isSelectAllChecked = $selectAllCheckboxes.is(':checked');
      $selectAllCheckboxes.closest('table').find('tbody input:checkbox').prop('checked', isSelectAllChecked);
    }
  }]);

  return ChoiceTable;
}();



/***/ }),

/***/ "./js/components/grid/extension/action/bulk/customer/delete-customers-bulk-action-extension.js":
/*!*****************************************************************************************************!*\
  !*** ./js/components/grid/extension/action/bulk/customer/delete-customers-bulk-action-extension.js ***!
  \*****************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return DeleteCustomersBulkActionExtension; });
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
 * Handles bulk delete for "Customers" grid.
 */

var DeleteCustomersBulkActionExtension =
/*#__PURE__*/
function () {
  function DeleteCustomersBulkActionExtension() {
    var _this = this;

    _classCallCheck(this, DeleteCustomersBulkActionExtension);

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


  _createClass(DeleteCustomersBulkActionExtension, [{
    key: "extend",
    value: function extend(grid) {
      var _this2 = this;

      grid.getContainer().on('click', '.js-delete-customers-bulk-action', function (event) {
        event.preventDefault();
        var submitUrl = $(event.currentTarget).data('customers-delete-url');
        var $modal = $("#".concat(grid.getId(), "_grid_delete_customers_modal"));
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
    key: "_addCustomerToDeleteCollectionInput",
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



/***/ }),

/***/ "./js/components/grid/extension/action/row/customer/delete-customer-row-action-extension.js":
/*!**************************************************************************************************!*\
  !*** ./js/components/grid/extension/action/row/customer/delete-customer-row-action-extension.js ***!
  \**************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return DeleteCustomerRowActionExtension; });
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
 * Class DeleteCustomerRowActionExtension handles submitting of row action
 */

var DeleteCustomerRowActionExtension =
/*#__PURE__*/
function () {
  function DeleteCustomerRowActionExtension() {
    var _this = this;

    _classCallCheck(this, DeleteCustomerRowActionExtension);

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


  _createClass(DeleteCustomerRowActionExtension, [{
    key: "extend",
    value: function extend(grid) {
      var _this2 = this;

      grid.getContainer().on('click', '.js-delete-customer-row-action', function (event) {
        event.preventDefault();
        var $deleteCustomersModal = $("#".concat(grid.getId(), "_grid_delete_customers_modal"));
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
    key: "_addCustomerInput",
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

/***/ "./js/components/grid/extension/submit-grid-action-extension.js":
/*!**********************************************************************!*\
  !*** ./js/components/grid/extension/submit-grid-action-extension.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return SubmitGridActionExtension; });
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
 * Class SubmitGridActionExtension handles grid action submits
 */

var SubmitGridActionExtension =
/*#__PURE__*/
function () {
  function SubmitGridActionExtension() {
    var _this = this;

    _classCallCheck(this, SubmitGridActionExtension);

    return {
      extend: function extend(grid) {
        return _this.extend(grid);
      }
    };
  }

  _createClass(SubmitGridActionExtension, [{
    key: "extend",
    value: function extend(grid) {
      var _this2 = this;

      grid.getContainer().on('click', '.js-grid-action-submit-btn', function (event) {
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
    key: "handleSubmit",
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

/***/ "./js/components/helper-card.js":
/*!**************************************!*\
  !*** ./js/components/helper-card.js ***!
  \**************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return HelperCard; });
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
 * Manages Helper cards on page.
 */

var HelperCard = function HelperCard() {
  _classCallCheck(this, HelperCard);

  $(document).on('click', '.js-close-helper-card', function (event) {
    $(event.currentTarget).closest('.helper-card').remove();
  });
};



/***/ }),

/***/ "./js/components/linkable-item.js":
/*!****************************************!*\
  !*** ./js/components/linkable-item.js ***!
  \****************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return LinkableItem; });
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
 * Takes link from clicked item and redirects to it.
 */

var LinkableItem = function LinkableItem() {
  _classCallCheck(this, LinkableItem);

  $(document).on('click', '.js-linkable-item', function (event) {
    window.location = $(event.currentTarget).data('linkable-href');
  });
};



/***/ }),

/***/ "./js/pages/customer/index.js":
/*!************************************!*\
  !*** ./js/pages/customer/index.js ***!
  \************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _components_grid_grid__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../components/grid/grid */ "./js/components/grid/grid.js");
/* harmony import */ var _components_grid_extension_reload_list_extension__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../components/grid/extension/reload-list-extension */ "./js/components/grid/extension/reload-list-extension.js");
/* harmony import */ var _components_grid_extension_export_to_sql_manager_extension__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../components/grid/extension/export-to-sql-manager-extension */ "./js/components/grid/extension/export-to-sql-manager-extension.js");
/* harmony import */ var _components_grid_extension_filters_reset_extension__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../components/grid/extension/filters-reset-extension */ "./js/components/grid/extension/filters-reset-extension.js");
/* harmony import */ var _components_grid_extension_sorting_extension__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../components/grid/extension/sorting-extension */ "./js/components/grid/extension/sorting-extension.js");
/* harmony import */ var _components_grid_extension_bulk_action_checkbox_extension__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../components/grid/extension/bulk-action-checkbox-extension */ "./js/components/grid/extension/bulk-action-checkbox-extension.js");
/* harmony import */ var _components_grid_extension_submit_bulk_action_extension__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../components/grid/extension/submit-bulk-action-extension */ "./js/components/grid/extension/submit-bulk-action-extension.js");
/* harmony import */ var _components_grid_extension_submit_grid_action_extension__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../components/grid/extension/submit-grid-action-extension */ "./js/components/grid/extension/submit-grid-action-extension.js");
/* harmony import */ var _components_grid_extension_link_row_action_extension__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../../components/grid/extension/link-row-action-extension */ "./js/components/grid/extension/link-row-action-extension.js");
/* harmony import */ var _components_linkable_item__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../../components/linkable-item */ "./js/components/linkable-item.js");
/* harmony import */ var _components_choice_table__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../../components/choice-table */ "./js/components/choice-table.js");
/* harmony import */ var _components_grid_extension_column_toggling_extension__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../../components/grid/extension/column-toggling-extension */ "./js/components/grid/extension/column-toggling-extension.js");
/* harmony import */ var _components_grid_extension_action_bulk_customer_delete_customers_bulk_action_extension__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../../components/grid/extension/action/bulk/customer/delete-customers-bulk-action-extension */ "./js/components/grid/extension/action/bulk/customer/delete-customers-bulk-action-extension.js");
/* harmony import */ var _components_grid_extension_action_row_customer_delete_customer_row_action_extension__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ../../components/grid/extension/action/row/customer/delete-customer-row-action-extension */ "./js/components/grid/extension/action/row/customer/delete-customer-row-action-extension.js");
/* harmony import */ var _components_helper_card__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ../../components/helper-card */ "./js/components/helper-card.js");
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
  var customerGrid = new _components_grid_grid__WEBPACK_IMPORTED_MODULE_0__["default"]('customer');
  customerGrid.addExtension(new _components_grid_extension_reload_list_extension__WEBPACK_IMPORTED_MODULE_1__["default"]());
  customerGrid.addExtension(new _components_grid_extension_export_to_sql_manager_extension__WEBPACK_IMPORTED_MODULE_2__["default"]());
  customerGrid.addExtension(new _components_grid_extension_filters_reset_extension__WEBPACK_IMPORTED_MODULE_3__["default"]());
  customerGrid.addExtension(new _components_grid_extension_sorting_extension__WEBPACK_IMPORTED_MODULE_4__["default"]());
  customerGrid.addExtension(new _components_grid_extension_bulk_action_checkbox_extension__WEBPACK_IMPORTED_MODULE_5__["default"]());
  customerGrid.addExtension(new _components_grid_extension_submit_bulk_action_extension__WEBPACK_IMPORTED_MODULE_6__["default"]());
  customerGrid.addExtension(new _components_grid_extension_submit_grid_action_extension__WEBPACK_IMPORTED_MODULE_7__["default"]());
  customerGrid.addExtension(new _components_grid_extension_link_row_action_extension__WEBPACK_IMPORTED_MODULE_8__["default"]());
  customerGrid.addExtension(new _components_grid_extension_column_toggling_extension__WEBPACK_IMPORTED_MODULE_11__["default"]());
  customerGrid.addExtension(new _components_grid_extension_action_bulk_customer_delete_customers_bulk_action_extension__WEBPACK_IMPORTED_MODULE_12__["default"]());
  customerGrid.addExtension(new _components_grid_extension_action_row_customer_delete_customer_row_action_extension__WEBPACK_IMPORTED_MODULE_13__["default"]()); // needed for "Group access" input in Add/Edit customer forms

  new _components_choice_table__WEBPACK_IMPORTED_MODULE_10__["default"](); // in customer view page
  // there are a lot of tables
  // where you click any row
  // and it redirects user to related page

  new _components_linkable_item__WEBPACK_IMPORTED_MODULE_9__["default"]();
  new _components_helper_card__WEBPACK_IMPORTED_MODULE_14__["default"]();
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vLy4vanMvYXBwL3V0aWxzL3Jlc2V0X3NlYXJjaC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9hcHAvdXRpbHMvdGFibGUtc29ydGluZy5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2Nob2ljZS10YWJsZS5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2FjdGlvbi9idWxrL2N1c3RvbWVyL2RlbGV0ZS1jdXN0b21lcnMtYnVsay1hY3Rpb24tZXh0ZW5zaW9uLmpzIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYWN0aW9uL3Jvdy9jdXN0b21lci9kZWxldGUtY3VzdG9tZXItcm93LWFjdGlvbi1leHRlbnNpb24uanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9idWxrLWFjdGlvbi1jaGVja2JveC1leHRlbnNpb24uanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9jb2x1bW4tdG9nZ2xpbmctZXh0ZW5zaW9uLmpzIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZXhwb3J0LXRvLXNxbC1tYW5hZ2VyLWV4dGVuc2lvbi5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2ZpbHRlcnMtcmVzZXQtZXh0ZW5zaW9uLmpzIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vbGluay1yb3ctYWN0aW9uLWV4dGVuc2lvbi5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3JlbG9hZC1saXN0LWV4dGVuc2lvbi5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3NvcnRpbmctZXh0ZW5zaW9uLmpzIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vc3VibWl0LWJ1bGstYWN0aW9uLWV4dGVuc2lvbi5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3N1Ym1pdC1ncmlkLWFjdGlvbi1leHRlbnNpb24uanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2dyaWQuanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9oZWxwZXItY2FyZC5qcyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2xpbmthYmxlLWl0ZW0uanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvY3VzdG9tZXIvaW5kZXguanMiLCJ3ZWJwYWNrOi8vLyh3ZWJwYWNrKS9idWlsZGluL2dsb2JhbC5qcyJdLCJuYW1lcyI6WyIkIiwiZ2xvYmFsIiwiaW5pdCIsInJlc2V0U2VhcmNoIiwidXJsIiwicmVkaXJlY3RVcmwiLCJwb3N0IiwidGhlbiIsIndpbmRvdyIsImxvY2F0aW9uIiwiYXNzaWduIiwiVGFibGVTb3J0aW5nIiwidGFibGUiLCJzZWxlY3RvciIsImNvbHVtbnMiLCJmaW5kIiwib24iLCJlIiwiJGNvbHVtbiIsImRlbGVnYXRlVGFyZ2V0IiwiX3NvcnRCeUNvbHVtbiIsIl9nZXRUb2dnbGVkU29ydERpcmVjdGlvbiIsImNvbHVtbk5hbWUiLCJkaXJlY3Rpb24iLCJpcyIsIkVycm9yIiwiY29sdW1uIiwiX2dldFVybCIsImRhdGEiLCJjb2xOYW1lIiwiVVJMIiwiaHJlZiIsInBhcmFtcyIsInNlYXJjaFBhcmFtcyIsInNldCIsInRvU3RyaW5nIiwiQ2hvaWNlVGFibGUiLCJkb2N1bWVudCIsImhhbmRsZVNlbGVjdEFsbCIsImV2ZW50IiwiJHNlbGVjdEFsbENoZWNrYm94ZXMiLCJ0YXJnZXQiLCJpc1NlbGVjdEFsbENoZWNrZWQiLCJjbG9zZXN0IiwicHJvcCIsIkRlbGV0ZUN1c3RvbWVyc0J1bGtBY3Rpb25FeHRlbnNpb24iLCJleHRlbmQiLCJncmlkIiwiZ2V0Q29udGFpbmVyIiwicHJldmVudERlZmF1bHQiLCJzdWJtaXRVcmwiLCJjdXJyZW50VGFyZ2V0IiwiJG1vZGFsIiwiZ2V0SWQiLCJtb2RhbCIsIiRzZWxlY3RlZEN1c3RvbWVyQ2hlY2tib3hlcyIsImVhY2giLCJpIiwiY2hlY2tib3giLCIkaW5wdXQiLCJfYWRkQ3VzdG9tZXJUb0RlbGV0ZUNvbGxlY3Rpb25JbnB1dCIsInZhbCIsIiRmb3JtIiwiYXR0ciIsInN1Ym1pdCIsImN1c3RvbWVySWQiLCIkY3VzdG9tZXJzSW5wdXQiLCJjdXN0b21lcklucHV0IiwicmVwbGFjZSIsIiRpdGVtIiwicGFyc2VIVE1MIiwiYXBwZW5kIiwiRGVsZXRlQ3VzdG9tZXJSb3dBY3Rpb25FeHRlbnNpb24iLCIkZGVsZXRlQ3VzdG9tZXJzTW9kYWwiLCIkYnV0dG9uIiwiX2FkZEN1c3RvbWVySW5wdXQiLCIkY3VzdG9tZXJzVG9EZWxldGVJbnB1dEJsb2NrIiwiY2hpbGRyZW4iLCJsZW5ndGgiLCJCdWxrQWN0aW9uQ2hlY2tib3hFeHRlbnNpb24iLCJfaGFuZGxlQnVsa0FjdGlvbkNoZWNrYm94U2VsZWN0IiwiX2hhbmRsZUJ1bGtBY3Rpb25TZWxlY3RBbGxDaGVja2JveCIsIiRjaGVja2JveCIsImlzQ2hlY2tlZCIsIl9lbmFibGVCdWxrQWN0aW9uc0J0biIsIl9kaXNhYmxlQnVsa0FjdGlvbnNCdG4iLCJjaGVja2VkUm93c0NvdW50IiwiQ29sdW1uVG9nZ2xpbmdFeHRlbnNpb24iLCIkdGFibGUiLCJfdG9nZ2xlVmFsdWUiLCJyb3ciLCJ0b2dnbGVVcmwiLCJfc3VibWl0QXNGb3JtIiwiYWN0aW9uIiwibWV0aG9kIiwiYXBwZW5kVG8iLCJFeHBvcnRUb1NxbE1hbmFnZXJFeHRlbnNpb24iLCJnZXRIZWFkZXJDb250YWluZXIiLCJfb25TaG93U3FsUXVlcnlDbGljayIsIl9vbkV4cG9ydFNxbE1hbmFnZXJDbGljayIsIiRzcWxNYW5hZ2VyRm9ybSIsIl9maWxsRXhwb3J0Rm9ybSIsInF1ZXJ5IiwiX2dldE5hbWVGcm9tQnJlYWRjcnVtYiIsIiRicmVhZGNydW1icyIsIm5hbWUiLCJpdGVtIiwiJGJyZWFkY3J1bWIiLCJicmVhZGNydW1iVGl0bGUiLCJ0ZXh0IiwiY29uY2F0IiwiRmlsdGVyc1Jlc2V0RXh0ZW5zaW9uIiwiTGlua1Jvd0FjdGlvbkV4dGVuc2lvbiIsImNvbmZpcm1NZXNzYWdlIiwiY29uZmlybSIsIlJlbG9hZExpc3RFeHRlbnNpb24iLCJyZWxvYWQiLCJTb3J0aW5nRXh0ZW5zaW9uIiwiJHNvcnRhYmxlVGFibGUiLCJhdHRhY2giLCJTdWJtaXRCdWxrQWN0aW9uRXh0ZW5zaW9uIiwiJHN1Ym1pdEJ0biIsIlN1Ym1pdEdyaWRBY3Rpb25FeHRlbnNpb24iLCJoYW5kbGVTdWJtaXQiLCJHcmlkIiwiaWQiLCIkY29udGFpbmVyIiwiZXh0ZW5zaW9uIiwiSGVscGVyQ2FyZCIsInJlbW92ZSIsIkxpbmthYmxlSXRlbSIsImN1c3RvbWVyR3JpZCIsImFkZEV4dGVuc2lvbiIsIlJlbG9hZExpc3RBY3Rpb25FeHRlbnNpb24iLCJTdWJtaXRCdWxrRXh0ZW5zaW9uIiwiU3VibWl0R3JpZEV4dGVuc2lvbiJdLCJtYXBwaW5ncyI6IjtBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0Esa0RBQTBDLGdDQUFnQztBQUMxRTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLGdFQUF3RCxrQkFBa0I7QUFDMUU7QUFDQSx5REFBaUQsY0FBYztBQUMvRDs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsaURBQXlDLGlDQUFpQztBQUMxRSx3SEFBZ0gsbUJBQW1CLEVBQUU7QUFDckk7QUFDQTs7QUFFQTtBQUNBO0FB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SixFQUExQjtBQUNBbUMsY0FBWSxDQUFDQyxZQUFiLENBQTBCLElBQUlyQiwwRkFBSixFQUExQjtBQUNBb0IsY0FBWSxDQUFDQyxZQUFiLENBQTBCLElBQUlmLG9GQUFKLEVBQTFCO0FBQ0FjLGNBQVksQ0FBQ0MsWUFBYixDQUEwQixJQUFJckQsaUdBQUosRUFBMUI7QUFDQW9ELGNBQVksQ0FBQ0MsWUFBYixDQUEwQixJQUFJRSwrRkFBSixFQUExQjtBQUNBSCxjQUFZLENBQUNDLFlBQWIsQ0FBMEIsSUFBSUcsK0ZBQUosRUFBMUI7QUFDQUosY0FBWSxDQUFDQyxZQUFiLENBQTBCLElBQUlwQiw0RkFBSixFQUExQjtBQUNBbUIsY0FBWSxDQUFDQyxZQUFiLENBQTBCLElBQUk3Qyw2RkFBSixFQUExQjtBQUNBNEMsY0FBWSxDQUFDQyxZQUFiLENBQTBCLElBQUl2RiwrSEFBSixFQUExQjtBQUNBc0YsY0FBWSxDQUFDQyxZQUFiLENBQTBCLElBQUk1RCw0SEFBSixFQUExQixFQWJNLENBZU47O0FBQ0EsTUFBSXBDLGlFQUFKLEdBaEJNLENBa0JOO0FBQ0E7QUFDQTtBQUNBOztBQUNBLE1BQUk4RixpRUFBSjtBQUNBLE1BQUlGLGdFQUFKO0FBQ0QsQ0F4QkEsQ0FBRCxDOzs7Ozs7Ozs7OztBQzdDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxDQUFDOztBQUVEO0FBQ0E7QUFDQTtBQUNBLENBQUM7QUFDRDtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBLDRDQUE0Qzs7QUFFNUMiLCJmaWxlIjoiY3VzdG9tZXIuYnVuZGxlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7IGVudW1lcmFibGU6IHRydWUsIGdldDogZ2V0dGVyIH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBkZWZpbmUgX19lc01vZHVsZSBvbiBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnIgPSBmdW5jdGlvbihleHBvcnRzKSB7XG4gXHRcdGlmKHR5cGVvZiBTeW1ib2wgIT09ICd1bmRlZmluZWQnICYmIFN5bWJvbC50b1N0cmluZ1RhZykge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBTeW1ib2wudG9TdHJpbmdUYWcsIHsgdmFsdWU6ICdNb2R1bGUnIH0pO1xuIFx0XHR9XG4gXHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCAnX19lc01vZHVsZScsIHsgdmFsdWU6IHRydWUgfSk7XG4gXHR9O1xuXG4gXHQvLyBjcmVhdGUgYSBmYWtlIG5hbWVzcGFjZSBvYmplY3RcbiBcdC8vIG1vZGUgJiAxOiB2YWx1ZSBpcyBhIG1vZHVsZSBpZCwgcmVxdWlyZSBpdFxuIFx0Ly8gbW9kZSAmIDI6IG1lcmdlIGFsbCBwcm9wZXJ0aWVzIG9mIHZhbHVlIGludG8gdGhlIG5zXG4gXHQvLyBtb2RlICYgNDogcmV0dXJuIHZhbHVlIHdoZW4gYWxyZWFkeSBucyBvYmplY3RcbiBcdC8vIG1vZGUgJiA4fDE6IGJlaGF2ZSBsaWtlIHJlcXVpcmVcbiBcdF9fd2VicGFja19yZXF1aXJlX18udCA9IGZ1bmN0aW9uKHZhbHVlLCBtb2RlKSB7XG4gXHRcdGlmKG1vZGUgJiAxKSB2YWx1ZSA9IF9fd2VicGFja19yZXF1aXJlX18odmFsdWUpO1xuIFx0XHRpZihtb2RlICYgOCkgcmV0dXJuIHZhbHVlO1xuIFx0XHRpZigobW9kZSAmIDQpICYmIHR5cGVvZiB2YWx1ZSA9PT0gJ29iamVjdCcgJiYgdmFsdWUgJiYgdmFsdWUuX19lc01vZHVsZSkgcmV0dXJuIHZhbHVlO1xuIFx0XHR2YXIgbnMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLnIobnMpO1xuIFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkobnMsICdkZWZhdWx0JywgeyBlbnVtZXJhYmxlOiB0cnVlLCB2YWx1ZTogdmFsdWUgfSk7XG4gXHRcdGlmKG1vZGUgJiAyICYmIHR5cGVvZiB2YWx1ZSAhPSAnc3RyaW5nJykgZm9yKHZhciBrZXkgaW4gdmFsdWUpIF9fd2VicGFja19yZXF1aXJlX18uZChucywga2V5LCBmdW5jdGlvbihrZXkpIHsgcmV0dXJuIHZhbHVlW2tleV07IH0uYmluZChudWxsLCBrZXkpKTtcbiBcdFx0cmV0dXJuIG5zO1xuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCIvYWRtaW4tZGV2L3RoZW1lcy9uZXctdGhlbWUvcHVibGljL1wiO1xuXG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gXCIuL2pzL3BhZ2VzL2N1c3RvbWVyL2luZGV4LmpzXCIpO1xuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuLyoqXG4gKiBTZW5kIGEgUG9zdCBSZXF1ZXN0IHRvIHJlc2V0IHNlYXJjaCBBY3Rpb24uXG4gKi9cblxuY29uc3QgJCA9IGdsb2JhbC4kO1xuXG5jb25zdCBpbml0ID0gZnVuY3Rpb24gcmVzZXRTZWFyY2godXJsLCByZWRpcmVjdFVybCkge1xuICAgICQucG9zdCh1cmwpLnRoZW4oKCkgPT4gd2luZG93LmxvY2F0aW9uLmFzc2lnbihyZWRpcmVjdFVybCkpO1xufTtcblxuZXhwb3J0IGRlZmF1bHQgaW5pdDtcbiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSBnbG9iYWwuJDtcblxuLyoqXG4gKiBNYWtlcyBhIHRhYmxlIHNvcnRhYmxlIGJ5IGNvbHVtbnMuXG4gKiBUaGlzIGZvcmNlcyBhIHBhZ2UgcmVsb2FkIHdpdGggbW9yZSBxdWVyeSBwYXJhbWV0ZXJzLlxuICovXG5jbGFzcyBUYWJsZVNvcnRpbmcge1xuXG4gIC8qKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gdGFibGVcbiAgICovXG4gIGNvbnN0cnVjdG9yKHRhYmxlKSB7XG4gICAgdGhpcy5zZWxlY3RvciA9ICcucHMtc29ydGFibGUtY29sdW1uJztcbiAgICB0aGlzLmNvbHVtbnMgPSAkKHRhYmxlKS5maW5kKHRoaXMuc2VsZWN0b3IpO1xuICB9XG5cbiAgLyoqXG4gICAqIEF0dGFjaGVzIHRoZSBsaXN0ZW5lcnNcbiAgICovXG4gIGF0dGFjaCgpIHtcbiAgICB0aGlzLmNvbHVtbnMub24oJ2NsaWNrJywgKGUpID0+IHtcbiAgICAgIGNvbnN0ICRjb2x1bW4gPSAkKGUuZGVsZWdhdGVUYXJnZXQpO1xuICAgICAgdGhpcy5fc29ydEJ5Q29sdW1uKCRjb2x1bW4sIHRoaXMuX2dldFRvZ2dsZWRTb3J0RGlyZWN0aW9uKCRjb2x1bW4pKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTb3J0IHVzaW5nIGEgY29sdW1uIG5hbWVcbiAgICogQHBhcmFtIHtzdHJpbmd9IGNvbHVtbk5hbWVcbiAgICogQHBhcmFtIHtzdHJpbmd9IGRpcmVjdGlvbiBcImFzY1wiIG9yIFwiZGVzY1wiXG4gICAqL1xuICBzb3J0QnkoY29sdW1uTmFtZSwgZGlyZWN0aW9uKSB7XG4gICAgY29uc3QgJGNvbHVtbiA9IHRoaXMuY29sdW1ucy5pcyhgW2RhdGEtc29ydC1jb2wtbmFtZT1cIiR7Y29sdW1uTmFtZX1cIl1gKTtcbiAgICBpZiAoISRjb2x1bW4pIHtcbiAgICAgIHRocm93IG5ldyBFcnJvcihgQ2Fubm90IHNvcnQgYnkgXCIke2NvbHVtbk5hbWV9XCI6IGludmFsaWQgY29sdW1uYCk7XG4gICAgfVxuXG4gICAgdGhpcy5fc29ydEJ5Q29sdW1uKCRjb2x1bW4sIGRpcmVjdGlvbik7XG4gIH1cblxuICAvKipcbiAgICogU29ydCB1c2luZyBhIGNvbHVtbiBlbGVtZW50XG4gICAqIEBwYXJhbSB7alF1ZXJ5fSBjb2x1bW5cbiAgICogQHBhcmFtIHtzdHJpbmd9IGRpcmVjdGlvbiBcImFzY1wiIG9yIFwiZGVzY1wiXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc29ydEJ5Q29sdW1uKGNvbHVtbiwgZGlyZWN0aW9uKSB7XG4gICAgd2luZG93LmxvY2F0aW9uID0gdGhpcy5fZ2V0VXJsKGNvbHVtbi5kYXRhKCdzb3J0Q29sTmFtZScpLCAoZGlyZWN0aW9uID09PSAnZGVzYycpID8gJ2Rlc2MnIDogJ2FzYycpO1xuICB9XG5cbiAgLyoqXG4gICAqIFJldHVybnMgdGhlIGludmVydGVkIGRpcmVjdGlvbiB0byBzb3J0IGFjY29yZGluZyB0byB0aGUgY29sdW1uJ3MgY3VycmVudCBvbmVcbiAgICogQHBhcmFtIHtqUXVlcnl9IGNvbHVtblxuICAgKiBAcmV0dXJuIHtzdHJpbmd9XG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfZ2V0VG9nZ2xlZFNvcnREaXJlY3Rpb24oY29sdW1uKSB7XG4gICAgcmV0dXJuIGNvbHVtbi5kYXRhKCdzb3J0RGlyZWN0aW9uJykgPT09ICdhc2MnID8gJ2Rlc2MnIDogJ2FzYyc7XG4gIH1cblxuICAvKipcbiAgICogUmV0dXJucyB0aGUgdXJsIGZvciB0aGUgc29ydGVkIHRhYmxlXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBjb2xOYW1lXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBkaXJlY3Rpb25cbiAgICogQHJldHVybiB7c3RyaW5nfVxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2dldFVybChjb2xOYW1lLCBkaXJlY3Rpb24pIHtcbiAgICBjb25zdCB1cmwgPSBuZXcgVVJMKHdpbmRvdy5sb2NhdGlvbi5ocmVmKTtcbiAgICBjb25zdCBwYXJhbXMgPSB1cmwuc2VhcmNoUGFyYW1zO1xuXG4gICAgcGFyYW1zLnNldCgnb3JkZXJCeScsIGNvbE5hbWUpO1xuICAgIHBhcmFtcy5zZXQoJ3NvcnRPcmRlcicsIGRpcmVjdGlvbik7XG5cbiAgICByZXR1cm4gdXJsLnRvU3RyaW5nKCk7XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgVGFibGVTb3J0aW5nO1xuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENob2ljZVRhYmxlIGlzIHJlc3BvbnNpYmxlIGZvciBtYW5hZ2luZyBjb21tb24gYWN0aW9ucyBpbiBjaG9pY2UgdGFibGUgZm9ybSB0eXBlXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIENob2ljZVRhYmxlIHtcbiAgLyoqXG4gICAqIEluaXQgY29uc3RydWN0b3JcbiAgICovXG4gIGNvbnN0cnVjdG9yKCkge1xuICAgICQoZG9jdW1lbnQpLm9uKCdjaGFuZ2UnLCAnLmpzLWNob2ljZS10YWJsZS1zZWxlY3QtYWxsJywgKGUpID0+IHtcbiAgICAgIHRoaXMuaGFuZGxlU2VsZWN0QWxsKGUpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIENoZWNrL3VuY2hlY2sgYWxsIGJveGVzIGluIHRhYmxlXG4gICAqXG4gICAqIEBwYXJhbSB7RXZlbnR9IGV2ZW50XG4gICAqL1xuICBoYW5kbGVTZWxlY3RBbGwoZXZlbnQpIHtcbiAgICBjb25zdCAkc2VsZWN0QWxsQ2hlY2tib3hlcyA9ICQoZXZlbnQudGFyZ2V0KTtcbiAgICBjb25zdCBpc1NlbGVjdEFsbENoZWNrZWQgPSAkc2VsZWN0QWxsQ2hlY2tib3hlcy5pcygnOmNoZWNrZWQnKTtcblxuICAgICRzZWxlY3RBbGxDaGVja2JveGVzLmNsb3Nlc3QoJ3RhYmxlJykuZmluZCgndGJvZHkgaW5wdXQ6Y2hlY2tib3gnKS5wcm9wKCdjaGVja2VkJywgaXNTZWxlY3RBbGxDaGVja2VkKTtcbiAgfVxufVxuIiwiLyoqXG4gKiAyMDA3LTIwMTggUHJlc3RhU2hvcFxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cDovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOCBQcmVzdGFTaG9wIFNBXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBIYW5kbGVzIGJ1bGsgZGVsZXRlIGZvciBcIkN1c3RvbWVyc1wiIGdyaWQuXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIERlbGV0ZUN1c3RvbWVyc0J1bGtBY3Rpb25FeHRlbnNpb24ge1xuXG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHJldHVybiB7XG4gICAgICBleHRlbmQ6IChncmlkKSA9PiB0aGlzLmV4dGVuZChncmlkKSxcbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtZGVsZXRlLWN1c3RvbWVycy1idWxrLWFjdGlvbicsIChldmVudCkgPT4ge1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgY29uc3Qgc3VibWl0VXJsID0gJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdjdXN0b21lcnMtZGVsZXRlLXVybCcpO1xuXG4gICAgICBjb25zdCAkbW9kYWwgPSAkKGAjJHtncmlkLmdldElkKCl9X2dyaWRfZGVsZXRlX2N1c3RvbWVyc19tb2RhbGApO1xuICAgICAgJG1vZGFsLm1vZGFsKCdzaG93Jyk7XG5cbiAgICAgICRtb2RhbC5vbignY2xpY2snLCAnLmpzLXN1Ym1pdC1kZWxldGUtY3VzdG9tZXJzJywgKCkgPT4ge1xuICAgICAgICBjb25zdCAkc2VsZWN0ZWRDdXN0b21lckNoZWNrYm94ZXMgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5qcy1idWxrLWFjdGlvbi1jaGVja2JveDpjaGVja2VkJyk7XG5cbiAgICAgICAgJHNlbGVjdGVkQ3VzdG9tZXJDaGVja2JveGVzLmVhY2goKGksIGNoZWNrYm94KSA9PiB7XG4gICAgICAgICAgY29uc3QgJGlucHV0ID0gJChjaGVja2JveCk7XG5cbiAgICAgICAgICB0aGlzLl9hZGRDdXN0b21lclRvRGVsZXRlQ29sbGVjdGlvbklucHV0KCRpbnB1dC52YWwoKSk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgIGNvbnN0ICRmb3JtID0gJG1vZGFsLmZpbmQoJ2Zvcm0nKTtcblxuICAgICAgICAkZm9ybS5hdHRyKCdhY3Rpb24nLCBzdWJtaXRVcmwpO1xuICAgICAgICAkZm9ybS5zdWJtaXQoKTtcbiAgICAgIH0pO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIENyZWF0ZSBpbnB1dCB3aXRoIGN1c3RvbWVyIGlkIGFuZCBhZGQgaXQgdG8gZGVsZXRlIGNvbGxlY3Rpb24gaW5wdXRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9hZGRDdXN0b21lclRvRGVsZXRlQ29sbGVjdGlvbklucHV0KGN1c3RvbWVySWQpIHtcbiAgICBjb25zdCAkY3VzdG9tZXJzSW5wdXQgPSAkKCcjZGVsZXRlX2N1c3RvbWVyc19jdXN0b21lcnNfdG9fZGVsZXRlJyk7XG5cbiAgICBjb25zdCBjdXN0b21lcklucHV0ID0gJGN1c3RvbWVyc0lucHV0XG4gICAgICAuZGF0YSgncHJvdG90eXBlJylcbiAgICAgIC5yZXBsYWNlKC9fX25hbWVfXy9nLCBjdXN0b21lcklkKVxuICAgIDtcblxuICAgIGNvbnN0ICRpdGVtID0gJCgkLnBhcnNlSFRNTChjdXN0b21lcklucHV0KVswXSk7XG4gICAgJGl0ZW0udmFsKGN1c3RvbWVySWQpO1xuXG4gICAgJGN1c3RvbWVyc0lucHV0LmFwcGVuZCgkaXRlbSk7XG4gIH1cbn1cbiIsIi8qKlxuICogMjAwNy0yMDE4IFByZXN0YVNob3BcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHA6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTggUHJlc3RhU2hvcCBTQVxuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgRGVsZXRlQ3VzdG9tZXJSb3dBY3Rpb25FeHRlbnNpb24gaGFuZGxlcyBzdWJtaXR0aW5nIG9mIHJvdyBhY3Rpb25cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgRGVsZXRlQ3VzdG9tZXJSb3dBY3Rpb25FeHRlbnNpb24ge1xuXG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHJldHVybiB7XG4gICAgICBleHRlbmQ6IChncmlkKSA9PiB0aGlzLmV4dGVuZChncmlkKSxcbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtZGVsZXRlLWN1c3RvbWVyLXJvdy1hY3Rpb24nLCAoZXZlbnQpID0+IHtcbiAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgIGNvbnN0ICRkZWxldGVDdXN0b21lcnNNb2RhbCA9ICQoYCMke2dyaWQuZ2V0SWQoKX1fZ3JpZF9kZWxldGVfY3VzdG9tZXJzX21vZGFsYCk7XG4gICAgICAkZGVsZXRlQ3VzdG9tZXJzTW9kYWwubW9kYWwoJ3Nob3cnKTtcblxuICAgICAgJGRlbGV0ZUN1c3RvbWVyc01vZGFsLm9uKCdjbGljaycsICcuanMtc3VibWl0LWRlbGV0ZS1jdXN0b21lcnMnLCAoKSA9PiB7XG4gICAgICAgIGNvbnN0ICRidXR0b24gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgICAgICBjb25zdCBjdXN0b21lcklkID0gJGJ1dHRvbi5kYXRhKCdjdXN0b21lci1pZCcpO1xuXG4gICAgICAgIHRoaXMuX2FkZEN1c3RvbWVySW5wdXQoY3VzdG9tZXJJZCk7XG5cbiAgICAgICAgY29uc3QgJGZvcm0gPSAkZGVsZXRlQ3VzdG9tZXJzTW9kYWwuZmluZCgnZm9ybScpO1xuXG4gICAgICAgICRmb3JtLmF0dHIoJ2FjdGlvbicsICRidXR0b24uZGF0YSgnY3VzdG9tZXItZGVsZXRlLXVybCcpKTtcbiAgICAgICAgJGZvcm0uc3VibWl0KCk7XG4gICAgICB9KTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBBZGRzIGlucHV0IGZvciBzZWxlY3RlZCBjdXN0b21lciB0byBkZWxldGUgZm9ybVxuICAgKlxuICAgKiBAcGFyYW0ge2ludGVnZXJ9IGN1c3RvbWVySWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9hZGRDdXN0b21lcklucHV0KGN1c3RvbWVySWQpIHtcbiAgICBjb25zdCAkY3VzdG9tZXJzVG9EZWxldGVJbnB1dEJsb2NrID0gJCgnI2RlbGV0ZV9jdXN0b21lcnNfY3VzdG9tZXJzX3RvX2RlbGV0ZScpO1xuXG4gICAgY29uc3QgY3VzdG9tZXJJbnB1dCA9ICRjdXN0b21lcnNUb0RlbGV0ZUlucHV0QmxvY2tcbiAgICAgIC5kYXRhKCdwcm90b3R5cGUnKVxuICAgICAgLnJlcGxhY2UoL19fbmFtZV9fL2csICRjdXN0b21lcnNUb0RlbGV0ZUlucHV0QmxvY2suY2hpbGRyZW4oKS5sZW5ndGgpO1xuXG4gICAgY29uc3QgJGl0ZW0gPSAkKCQucGFyc2VIVE1MKGN1c3RvbWVySW5wdXQpWzBdKTtcbiAgICAkaXRlbS52YWwoY3VzdG9tZXJJZCk7XG5cbiAgICAkY3VzdG9tZXJzVG9EZWxldGVJbnB1dEJsb2NrLmFwcGVuZCgkaXRlbSk7XG4gIH1cbn1cbiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBCdWxrQWN0aW9uU2VsZWN0Q2hlY2tib3hFeHRlbnNpb25cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQnVsa0FjdGlvbkNoZWNrYm94RXh0ZW5zaW9uIHtcbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkIHdpdGggYnVsayBhY3Rpb24gY2hlY2tib3hlcyBoYW5kbGluZyBmdW5jdGlvbmFsaXR5XG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICB0aGlzLl9oYW5kbGVCdWxrQWN0aW9uQ2hlY2tib3hTZWxlY3QoZ3JpZCk7XG4gICAgdGhpcy5faGFuZGxlQnVsa0FjdGlvblNlbGVjdEFsbENoZWNrYm94KGdyaWQpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhhbmRsZXMgXCJTZWxlY3QgYWxsXCIgYnV0dG9uIGluIHRoZSBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hhbmRsZUJ1bGtBY3Rpb25TZWxlY3RBbGxDaGVja2JveChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2hhbmdlJywgJy5qcy1idWxrLWFjdGlvbi1zZWxlY3QtYWxsJywgKGUpID0+IHtcbiAgICAgIGNvbnN0ICRjaGVja2JveCA9ICQoZS5jdXJyZW50VGFyZ2V0KTtcblxuICAgICAgY29uc3QgaXNDaGVja2VkID0gJGNoZWNrYm94LmlzKCc6Y2hlY2tlZCcpO1xuICAgICAgaWYgKGlzQ2hlY2tlZCkge1xuICAgICAgICB0aGlzLl9lbmFibGVCdWxrQWN0aW9uc0J0bihncmlkKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHRoaXMuX2Rpc2FibGVCdWxrQWN0aW9uc0J0bihncmlkKTtcbiAgICAgIH1cblxuICAgICAgZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCcuanMtYnVsay1hY3Rpb24tY2hlY2tib3gnKS5wcm9wKCdjaGVja2VkJywgaXNDaGVja2VkKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGVzIGVhY2ggYnVsayBhY3Rpb24gY2hlY2tib3ggc2VsZWN0IGluIHRoZSBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hhbmRsZUJ1bGtBY3Rpb25DaGVja2JveFNlbGVjdChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2hhbmdlJywgJy5qcy1idWxrLWFjdGlvbi1jaGVja2JveCcsICgpID0+IHtcbiAgICAgIGNvbnN0IGNoZWNrZWRSb3dzQ291bnQgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5qcy1idWxrLWFjdGlvbi1jaGVja2JveDpjaGVja2VkJykubGVuZ3RoO1xuXG4gICAgICBpZiAoY2hlY2tlZFJvd3NDb3VudCA+IDApIHtcbiAgICAgICAgdGhpcy5fZW5hYmxlQnVsa0FjdGlvbnNCdG4oZ3JpZCk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICB0aGlzLl9kaXNhYmxlQnVsa0FjdGlvbnNCdG4oZ3JpZCk7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogRW5hYmxlIGJ1bGsgYWN0aW9ucyBidXR0b25cbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfZW5hYmxlQnVsa0FjdGlvbnNCdG4oZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgnLmpzLWJ1bGstYWN0aW9ucy1idG4nKS5wcm9wKCdkaXNhYmxlZCcsIGZhbHNlKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBEaXNhYmxlIGJ1bGsgYWN0aW9ucyBidXR0b25cbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfZGlzYWJsZUJ1bGtBY3Rpb25zQnRuKGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5qcy1idWxrLWFjdGlvbnMtYnRuJykucHJvcCgnZGlzYWJsZWQnLCB0cnVlKTtcbiAgfVxufVxuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IGdsb2JhbC4kO1xuXG4vKipcbiAqIENsYXNzIFJlbG9hZExpc3RFeHRlbnNpb24gZXh0ZW5kcyBncmlkIHdpdGggXCJDb2x1bW4gdG9nZ2xpbmdcIiBmZWF0dXJlXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIENvbHVtblRvZ2dsaW5nRXh0ZW5zaW9uIHtcblxuICAvKipcbiAgICogRXh0ZW5kIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIGNvbnN0ICR0YWJsZSA9IGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgndGFibGUudGFibGUnKTtcbiAgICAkdGFibGUuZmluZCgnLnBzLXRvZ2dsYWJsZS1yb3cnKS5vbignY2xpY2snLCAoZSkgPT4ge1xuICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgdGhpcy5fdG9nZ2xlVmFsdWUoJChlLmRlbGVnYXRlVGFyZ2V0KSk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQHBhcmFtIHtqUXVlcnl9IHJvd1xuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3RvZ2dsZVZhbHVlKHJvdykge1xuICAgIGNvbnN0IHRvZ2dsZVVybCA9IHJvdy5kYXRhKCd0b2dnbGVVcmwnKTtcblxuICAgIHRoaXMuX3N1Ym1pdEFzRm9ybSh0b2dnbGVVcmwpO1xuICB9XG5cbiAgLyoqXG4gICAqIFN1Ym1pdHMgcmVxdWVzdCB1cmwgYXMgZm9ybVxuICAgKlxuICAgKiBAcGFyYW0ge3N0cmluZ30gdG9nZ2xlVXJsXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc3VibWl0QXNGb3JtKHRvZ2dsZVVybCkge1xuICAgIGNvbnN0ICRmb3JtID0gJCgnPGZvcm0+Jywge1xuICAgICAgYWN0aW9uOiB0b2dnbGVVcmwsXG4gICAgICBtZXRob2Q6ICdQT1NUJyxcbiAgICB9KS5hcHBlbmRUbygnYm9keScpO1xuXG4gICAgJGZvcm0uc3VibWl0KCk7XG4gIH1cbn1cbiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBFeHBvcnRUb1NxbE1hbmFnZXJFeHRlbnNpb24gZXh0ZW5kcyBncmlkIHdpdGggZXhwb3J0aW5nIHF1ZXJ5IHRvIFNRTCBNYW5hZ2VyXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEV4cG9ydFRvU3FsTWFuYWdlckV4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRIZWFkZXJDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLWNvbW1vbl9zaG93X3F1ZXJ5LWdyaWQtYWN0aW9uJywgKCkgPT4gdGhpcy5fb25TaG93U3FsUXVlcnlDbGljayhncmlkKSk7XG4gICAgZ3JpZC5nZXRIZWFkZXJDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLWNvbW1vbl9leHBvcnRfc3FsX21hbmFnZXItZ3JpZC1hY3Rpb24nLCAoKSA9PiB0aGlzLl9vbkV4cG9ydFNxbE1hbmFnZXJDbGljayhncmlkKSk7XG4gIH1cblxuICAvKipcbiAgICogSW52b2tlZCB3aGVuIGNsaWNraW5nIG9uIHRoZSBcInNob3cgc3FsIHF1ZXJ5XCIgdG9vbGJhciBidXR0b25cbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfb25TaG93U3FsUXVlcnlDbGljayhncmlkKSB7XG4gICAgY29uc3QgJHNxbE1hbmFnZXJGb3JtID0gJCgnIycgKyBncmlkLmdldElkKCkgKyAnX2NvbW1vbl9zaG93X3F1ZXJ5X21vZGFsX2Zvcm0nKTtcbiAgICB0aGlzLl9maWxsRXhwb3J0Rm9ybSgkc3FsTWFuYWdlckZvcm0sIGdyaWQpO1xuXG4gICAgY29uc3QgJG1vZGFsID0gJCgnIycgKyBncmlkLmdldElkKCkgKyAnX2dyaWRfY29tbW9uX3Nob3dfcXVlcnlfbW9kYWwnKTtcbiAgICAkbW9kYWwubW9kYWwoJ3Nob3cnKTtcblxuICAgICRtb2RhbC5vbignY2xpY2snLCAnLmJ0bi1zcWwtc3VibWl0JywgKCkgPT4gJHNxbE1hbmFnZXJGb3JtLnN1Ym1pdCgpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJbnZva2VkIHdoZW4gY2xpY2tpbmcgb24gdGhlIFwiZXhwb3J0IHRvIHRoZSBzcWwgcXVlcnlcIiB0b29sYmFyIGJ1dHRvblxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9vbkV4cG9ydFNxbE1hbmFnZXJDbGljayhncmlkKSB7XG4gICAgY29uc3QgJHNxbE1hbmFnZXJGb3JtID0gJCgnIycgKyBncmlkLmdldElkKCkgKyAnX2NvbW1vbl9zaG93X3F1ZXJ5X21vZGFsX2Zvcm0nKTtcblxuICAgIHRoaXMuX2ZpbGxFeHBvcnRGb3JtKCRzcWxNYW5hZ2VyRm9ybSwgZ3JpZCk7XG5cbiAgICAkc3FsTWFuYWdlckZvcm0uc3VibWl0KCk7XG4gIH1cblxuICAvKipcbiAgICogRmlsbCBleHBvcnQgZm9ybSB3aXRoIFNRTCBhbmQgaXQncyBuYW1lXG4gICAqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkc3FsTWFuYWdlckZvcm1cbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfZmlsbEV4cG9ydEZvcm0oJHNxbE1hbmFnZXJGb3JtLCBncmlkKSB7XG4gICAgY29uc3QgcXVlcnkgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5qcy1ncmlkLXRhYmxlJykuZGF0YSgncXVlcnknKTtcblxuICAgICRzcWxNYW5hZ2VyRm9ybS5maW5kKCd0ZXh0YXJlYVtuYW1lPVwic3FsXCJdJykudmFsKHF1ZXJ5KTtcbiAgICAkc3FsTWFuYWdlckZvcm0uZmluZCgnaW5wdXRbbmFtZT1cIm5hbWVcIl0nKS52YWwodGhpcy5fZ2V0TmFtZUZyb21CcmVhZGNydW1iKCkpO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBleHBvcnQgbmFtZSBmcm9tIHBhZ2UncyBicmVhZGNydW1iXG4gICAqXG4gICAqIEByZXR1cm4ge1N0cmluZ31cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9nZXROYW1lRnJvbUJyZWFkY3J1bWIoKSB7XG4gICAgY29uc3QgJGJyZWFkY3J1bWJzID0gJCgnLmhlYWRlci10b29sYmFyJykuZmluZCgnLmJyZWFkY3J1bWItaXRlbScpO1xuICAgIGxldCBuYW1lID0gJyc7XG5cbiAgICAkYnJlYWRjcnVtYnMuZWFjaCgoaSwgaXRlbSkgPT4ge1xuICAgICAgY29uc3QgJGJyZWFkY3J1bWIgPSAkKGl0ZW0pO1xuXG4gICAgICBjb25zdCBicmVhZGNydW1iVGl0bGUgPSAwIDwgJGJyZWFkY3J1bWIuZmluZCgnYScpLmxlbmd0aCA/XG4gICAgICAgICRicmVhZGNydW1iLmZpbmQoJ2EnKS50ZXh0KCkgOlxuICAgICAgICAkYnJlYWRjcnVtYi50ZXh0KCk7XG5cbiAgICAgIGlmICgwIDwgbmFtZS5sZW5ndGgpIHtcbiAgICAgICAgbmFtZSA9IG5hbWUuY29uY2F0KCcgPiAnKTtcbiAgICAgIH1cblxuICAgICAgbmFtZSA9IG5hbWUuY29uY2F0KGJyZWFkY3J1bWJUaXRsZSk7XG4gICAgfSk7XG5cbiAgICByZXR1cm4gbmFtZTtcbiAgfVxufVxuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IHJlc2V0U2VhcmNoIGZyb20gJy4uLy4uLy4uL2FwcC91dGlscy9yZXNldF9zZWFyY2gnO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgRmlsdGVyc1Jlc2V0RXh0ZW5zaW9uIGV4dGVuZHMgZ3JpZCB3aXRoIGZpbHRlcnMgcmVzZXR0aW5nXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEZpbHRlcnNSZXNldEV4dGVuc2lvbiB7XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtcmVzZXQtc2VhcmNoJywgKGV2ZW50KSA9PiB7XG4gICAgICByZXNldFNlYXJjaCgkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmRhdGEoJ3VybCcpLCAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmRhdGEoJ3JlZGlyZWN0JykpO1xuICAgIH0pO1xuICB9XG59XG4iLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgTGlua1Jvd0FjdGlvbkV4dGVuc2lvbiBoYW5kbGVzIGxpbmsgcm93IGFjdGlvbnNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgTGlua1Jvd0FjdGlvbkV4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLWxpbmstcm93LWFjdGlvbicsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgY29uZmlybU1lc3NhZ2UgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmRhdGEoJ2NvbmZpcm0tbWVzc2FnZScpO1xuXG4gICAgICBpZiAoY29uZmlybU1lc3NhZ2UubGVuZ3RoICYmICFjb25maXJtKGNvbmZpcm1NZXNzYWdlKSkge1xuICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG59XG4iLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG4vKipcbiAqIENsYXNzIFJlbG9hZExpc3RFeHRlbnNpb24gZXh0ZW5kcyBncmlkIHdpdGggXCJMaXN0IHJlbG9hZFwiIGFjdGlvblxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBSZWxvYWRMaXN0RXh0ZW5zaW9uIHtcbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldEhlYWRlckNvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtY29tbW9uX3JlZnJlc2hfbGlzdC1ncmlkLWFjdGlvbicsICgpID0+IHtcbiAgICAgIGxvY2F0aW9uLnJlbG9hZCgpO1xuICAgIH0pO1xuICB9XG59XG4iLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgVGFibGVTb3J0aW5nIGZyb20gJy4uLy4uLy4uL2FwcC91dGlscy90YWJsZS1zb3J0aW5nJztcblxuLyoqXG4gKiBDbGFzcyBSZWxvYWRMaXN0RXh0ZW5zaW9uIGV4dGVuZHMgZ3JpZCB3aXRoIFwiTGlzdCByZWxvYWRcIiBhY3Rpb25cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgU29ydGluZ0V4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgY29uc3QgJHNvcnRhYmxlVGFibGUgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJ3RhYmxlLnRhYmxlJyk7XG5cbiAgICBuZXcgVGFibGVTb3J0aW5nKCRzb3J0YWJsZVRhYmxlKS5hdHRhY2goKTtcbiAgfVxufVxuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIEhhbmRsZXMgc3VibWl0IG9mIGdyaWQgYWN0aW9uc1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBTdWJtaXRCdWxrQWN0aW9uRXh0ZW5zaW9uIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgcmV0dXJuIHtcbiAgICAgIGV4dGVuZDogKGdyaWQpID0+IHRoaXMuZXh0ZW5kKGdyaWQpLFxuICAgIH07XG4gIH1cblxuICAvKipcbiAgICogRXh0ZW5kIGdyaWQgd2l0aCBidWxrIGFjdGlvbiBzdWJtaXR0aW5nXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtYnVsay1hY3Rpb24tc3VibWl0LWJ0bicsIChldmVudCkgPT4ge1xuICAgICAgdGhpcy5zdWJtaXQoZXZlbnQsIGdyaWQpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEhhbmRsZSBidWxrIGFjdGlvbiBzdWJtaXR0aW5nXG4gICAqXG4gICAqIEBwYXJhbSB7RXZlbnR9IGV2ZW50XG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgc3VibWl0KGV2ZW50LCBncmlkKSB7XG4gICAgY29uc3QgJHN1Ym1pdEJ0biA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgY29uc3QgY29uZmlybU1lc3NhZ2UgPSAkc3VibWl0QnRuLmRhdGEoJ2NvbmZpcm0tbWVzc2FnZScpO1xuXG4gICAgaWYgKHR5cGVvZiBjb25maXJtTWVzc2FnZSAhPT0gXCJ1bmRlZmluZWRcIiAmJiAwIDwgY29uZmlybU1lc3NhZ2UubGVuZ3RoICYmICFjb25maXJtKGNvbmZpcm1NZXNzYWdlKSkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIGNvbnN0ICRmb3JtID0gJCgnIycgKyBncmlkLmdldElkKCkgKyAnX2ZpbHRlcl9mb3JtJyk7XG5cbiAgICAkZm9ybS5hdHRyKCdhY3Rpb24nLCAkc3VibWl0QnRuLmRhdGEoJ2Zvcm0tdXJsJykpO1xuICAgICRmb3JtLmF0dHIoJ21ldGhvZCcsICRzdWJtaXRCdG4uZGF0YSgnZm9ybS1tZXRob2QnKSk7XG4gICAgJGZvcm0uc3VibWl0KCk7XG4gIH1cbn1cbiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBTdWJtaXRHcmlkQWN0aW9uRXh0ZW5zaW9uIGhhbmRsZXMgZ3JpZCBhY3Rpb24gc3VibWl0c1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBTdWJtaXRHcmlkQWN0aW9uRXh0ZW5zaW9uIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgcmV0dXJuIHtcbiAgICAgIGV4dGVuZDogKGdyaWQpID0+IHRoaXMuZXh0ZW5kKGdyaWQpXG4gICAgfTtcbiAgfVxuXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLWdyaWQtYWN0aW9uLXN1Ym1pdC1idG4nLCAoZXZlbnQpID0+IHtcbiAgICAgIHRoaXMuaGFuZGxlU3VibWl0KGV2ZW50LCBncmlkKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGUgZ3JpZCBhY3Rpb24gc3VibWl0LlxuICAgKiBJdCB1c2VzIGdyaWQgZm9ybSB0byBzdWJtaXQgYWN0aW9ucy5cbiAgICpcbiAgICogQHBhcmFtIHtFdmVudH0gZXZlbnRcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBoYW5kbGVTdWJtaXQoZXZlbnQsIGdyaWQpIHtcbiAgICBjb25zdCAkc3VibWl0QnRuID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICBjb25zdCBjb25maXJtTWVzc2FnZSA9ICRzdWJtaXRCdG4uZGF0YSgnY29uZmlybS1tZXNzYWdlJyk7XG5cbiAgICBpZiAodHlwZW9mIGNvbmZpcm1NZXNzYWdlICE9PSBcInVuZGVmaW5lZFwiICYmIDAgPCBjb25maXJtTWVzc2FnZS5sZW5ndGggJiYgIWNvbmZpcm0oY29uZmlybU1lc3NhZ2UpKSB7XG4gICAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBjb25zdCAkZm9ybSA9ICQoJyMnICsgZ3JpZC5nZXRJZCgpICsgJ19maWx0ZXJfZm9ybScpO1xuXG4gICAgJGZvcm0uYXR0cignYWN0aW9uJywgJHN1Ym1pdEJ0bi5kYXRhKCd1cmwnKSk7XG4gICAgJGZvcm0uYXR0cignbWV0aG9kJywgJHN1Ym1pdEJ0bi5kYXRhKCdtZXRob2QnKSk7XG4gICAgJGZvcm0uZmluZCgnaW5wdXRbbmFtZT1cIicgKyBncmlkLmdldElkKCkgKyAnW190b2tlbl1cIl0nKS52YWwoJHN1Ym1pdEJ0bi5kYXRhKCdjc3JmJykpO1xuICAgICRmb3JtLnN1Ym1pdCgpO1xuICB9XG59XG4iLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgaXMgcmVzcG9uc2libGUgZm9yIGhhbmRsaW5nIEdyaWQgZXZlbnRzXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEdyaWQge1xuICAvKipcbiAgICogR3JpZCBpZFxuICAgKlxuICAgKiBAcGFyYW0ge3N0cmluZ30gaWRcbiAgICovXG4gIGNvbnN0cnVjdG9yKGlkKSB7XG4gICAgdGhpcy5pZCA9IGlkO1xuICAgIHRoaXMuJGNvbnRhaW5lciA9ICQoJyMnICsgdGhpcy5pZCArICdfZ3JpZCcpO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBncmlkIGlkXG4gICAqXG4gICAqIEByZXR1cm5zIHtzdHJpbmd9XG4gICAqL1xuICBnZXRJZCgpIHtcbiAgICByZXR1cm4gdGhpcy5pZDtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgZ3JpZCBjb250YWluZXJcbiAgICpcbiAgICogQHJldHVybnMge2pRdWVyeX1cbiAgICovXG4gIGdldENvbnRhaW5lcigpIHtcbiAgICByZXR1cm4gdGhpcy4kY29udGFpbmVyO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBncmlkIGhlYWRlciBjb250YWluZXJcbiAgICpcbiAgICogQHJldHVybnMge2pRdWVyeX1cbiAgICovXG4gIGdldEhlYWRlckNvbnRhaW5lcigpIHtcbiAgICByZXR1cm4gdGhpcy4kY29udGFpbmVyLmNsb3Nlc3QoJy5qcy1ncmlkLXBhbmVsJykuZmluZCgnLmpzLWdyaWQtaGVhZGVyJyk7XG4gIH1cblxuICAvKipcbiAgICogRXh0ZW5kIGdyaWQgd2l0aCBleHRlcm5hbCBleHRlbnNpb25zXG4gICAqXG4gICAqIEBwYXJhbSB7b2JqZWN0fSBleHRlbnNpb25cbiAgICovXG4gIGFkZEV4dGVuc2lvbihleHRlbnNpb24pIHtcbiAgICBleHRlbnNpb24uZXh0ZW5kKHRoaXMpO1xuICB9XG59XG4iLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogTWFuYWdlcyBIZWxwZXIgY2FyZHMgb24gcGFnZS5cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgSGVscGVyQ2FyZCB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsICcuanMtY2xvc2UtaGVscGVyLWNhcmQnLCAoZXZlbnQpID0+IHtcbiAgICAgICQoZXZlbnQuY3VycmVudFRhcmdldCkuY2xvc2VzdCgnLmhlbHBlci1jYXJkJykucmVtb3ZlKCk7XG4gICAgfSk7XG4gIH1cbn1cbiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBUYWtlcyBsaW5rIGZyb20gY2xpY2tlZCBpdGVtIGFuZCByZWRpcmVjdHMgdG8gaXQuXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIExpbmthYmxlSXRlbSB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsICcuanMtbGlua2FibGUtaXRlbScsIChldmVudCkgPT4ge1xuICAgICAgd2luZG93LmxvY2F0aW9uID0gJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdsaW5rYWJsZS1ocmVmJyk7XG4gICAgfSk7XG4gIH1cbn1cbiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBHcmlkIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvZ3JpZC9ncmlkJztcbmltcG9ydCBSZWxvYWRMaXN0QWN0aW9uRXh0ZW5zaW9uIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vcmVsb2FkLWxpc3QtZXh0ZW5zaW9uJztcbmltcG9ydCBFeHBvcnRUb1NxbE1hbmFnZXJFeHRlbnNpb24gZnJvbSAnLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9leHBvcnQtdG8tc3FsLW1hbmFnZXItZXh0ZW5zaW9uJztcbmltcG9ydCBGaWx0ZXJzUmVzZXRFeHRlbnNpb24gZnJvbSAnLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXJlc2V0LWV4dGVuc2lvbic7XG5pbXBvcnQgU29ydGluZ0V4dGVuc2lvbiBmcm9tICcuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3NvcnRpbmctZXh0ZW5zaW9uJztcbmltcG9ydCBCdWxrQWN0aW9uQ2hlY2tib3hFeHRlbnNpb24gZnJvbSAnLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9idWxrLWFjdGlvbi1jaGVja2JveC1leHRlbnNpb24nO1xuaW1wb3J0IFN1Ym1pdEJ1bGtFeHRlbnNpb24gZnJvbSAnLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9zdWJtaXQtYnVsay1hY3Rpb24tZXh0ZW5zaW9uJztcbmltcG9ydCBTdWJtaXRHcmlkRXh0ZW5zaW9uIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vc3VibWl0LWdyaWQtYWN0aW9uLWV4dGVuc2lvbic7XG5pbXBvcnQgTGlua1Jvd0FjdGlvbkV4dGVuc2lvbiBmcm9tICcuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2xpbmstcm93LWFjdGlvbi1leHRlbnNpb24nO1xuaW1wb3J0IExpbmthYmxlSXRlbSBmcm9tIFwiLi4vLi4vY29tcG9uZW50cy9saW5rYWJsZS1pdGVtXCI7XG5pbXBvcnQgQ2hvaWNlVGFibGUgZnJvbSBcIi4uLy4uL2NvbXBvbmVudHMvY2hvaWNlLXRhYmxlXCI7XG5pbXBvcnQgQ29sdW1uVG9nZ2xpbmdFeHRlbnNpb24gZnJvbSBcIi4uLy4uL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vY29sdW1uLXRvZ2dsaW5nLWV4dGVuc2lvblwiO1xuaW1wb3J0IERlbGV0ZUN1c3RvbWVyc0J1bGtBY3Rpb25FeHRlbnNpb25cbiAgZnJvbSBcIi4uLy4uL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYWN0aW9uL2J1bGsvY3VzdG9tZXIvZGVsZXRlLWN1c3RvbWVycy1idWxrLWFjdGlvbi1leHRlbnNpb25cIjtcbmltcG9ydCBEZWxldGVDdXN0b21lclJvd0FjdGlvbkV4dGVuc2lvblxuICBmcm9tIFwiLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9hY3Rpb24vcm93L2N1c3RvbWVyL2RlbGV0ZS1jdXN0b21lci1yb3ctYWN0aW9uLWV4dGVuc2lvblwiO1xuaW1wb3J0IEhlbHBlckNhcmQgZnJvbSBcIi4uLy4uL2NvbXBvbmVudHMvaGVscGVyLWNhcmRcIjtcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4kKCgpID0+IHtcbiAgY29uc3QgY3VzdG9tZXJHcmlkID0gbmV3IEdyaWQoJ2N1c3RvbWVyJyk7XG5cbiAgY3VzdG9tZXJHcmlkLmFkZEV4dGVuc2lvbihuZXcgUmVsb2FkTGlzdEFjdGlvbkV4dGVuc2lvbigpKTtcbiAgY3VzdG9tZXJHcmlkLmFkZEV4dGVuc2lvbihuZXcgRXhwb3J0VG9TcWxNYW5hZ2VyRXh0ZW5zaW9uKCkpO1xuICBjdXN0b21lckdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBGaWx0ZXJzUmVzZXRFeHRlbnNpb24oKSk7XG4gIGN1c3RvbWVyR3JpZC5hZGRFeHRlbnNpb24obmV3IFNvcnRpbmdFeHRlbnNpb24oKSk7XG4gIGN1c3RvbWVyR3JpZC5hZGRFeHRlbnNpb24obmV3IEJ1bGtBY3Rpb25DaGVja2JveEV4dGVuc2lvbigpKTtcbiAgY3VzdG9tZXJHcmlkLmFkZEV4dGVuc2lvbihuZXcgU3VibWl0QnVsa0V4dGVuc2lvbigpKTtcbiAgY3VzdG9tZXJHcmlkLmFkZEV4dGVuc2lvbihuZXcgU3VibWl0R3JpZEV4dGVuc2lvbigpKTtcbiAgY3VzdG9tZXJHcmlkLmFkZEV4dGVuc2lvbihuZXcgTGlua1Jvd0FjdGlvbkV4dGVuc2lvbigpKTtcbiAgY3VzdG9tZXJHcmlkLmFkZEV4dGVuc2lvbihuZXcgQ29sdW1uVG9nZ2xpbmdFeHRlbnNpb24oKSk7XG4gIGN1c3RvbWVyR3JpZC5hZGRFeHRlbnNpb24obmV3IERlbGV0ZUN1c3RvbWVyc0J1bGtBY3Rpb25FeHRlbnNpb24oKSk7XG4gIGN1c3RvbWVyR3JpZC5hZGRFeHRlbnNpb24obmV3IERlbGV0ZUN1c3RvbWVyUm93QWN0aW9uRXh0ZW5zaW9uKCkpO1xuXG4gIC8vIG5lZWRlZCBmb3IgXCJHcm91cCBhY2Nlc3NcIiBpbnB1dCBpbiBBZGQvRWRpdCBjdXN0b21lciBmb3Jtc1xuICBuZXcgQ2hvaWNlVGFibGUoKTtcblxuICAvLyBpbiBjdXN0b21lciB2aWV3IHBhZ2VcbiAgLy8gdGhlcmUgYXJlIGEgbG90IG9mIHRhYmxlc1xuICAvLyB3aGVyZSB5b3UgY2xpY2sgYW55IHJvd1xuICAvLyBhbmQgaXQgcmVkaXJlY3RzIHVzZXIgdG8gcmVsYXRlZCBwYWdlXG4gIG5ldyBMaW5rYWJsZUl0ZW0oKTtcbiAgbmV3IEhlbHBlckNhcmQoKTtcbn0pO1xuIiwidmFyIGc7XG5cbi8vIFRoaXMgd29ya3MgaW4gbm9uLXN0cmljdCBtb2RlXG5nID0gKGZ1bmN0aW9uKCkge1xuXHRyZXR1cm4gdGhpcztcbn0pKCk7XG5cbnRyeSB7XG5cdC8vIFRoaXMgd29ya3MgaWYgZXZhbCBpcyBhbGxvd2VkIChzZWUgQ1NQKVxuXHRnID0gZyB8fCBuZXcgRnVuY3Rpb24oXCJyZXR1cm4gdGhpc1wiKSgpO1xufSBjYXRjaCAoZSkge1xuXHQvLyBUaGlzIHdvcmtzIGlmIHRoZSB3aW5kb3cgcmVmZXJlbmNlIGlzIGF2YWlsYWJsZVxuXHRpZiAodHlwZW9mIHdpbmRvdyA9PT0gXCJvYmplY3RcIikgZyA9IHdpbmRvdztcbn1cblxuLy8gZyBjYW4gc3RpbGwgYmUgdW5kZWZpbmVkLCBidXQgbm90aGluZyB0byBkbyBhYm91dCBpdC4uLlxuLy8gV2UgcmV0dXJuIHVuZGVmaW5lZCwgaW5zdGVhZCBvZiBub3RoaW5nIGhlcmUsIHNvIGl0J3Ncbi8vIGVhc2llciB0byBoYW5kbGUgdGhpcyBjYXNlLiBpZighZ2xvYmFsKSB7IC4uLn1cblxubW9kdWxlLmV4cG9ydHMgPSBnO1xuIl0sInNvdXJjZVJvb3QiOiIifQ==