window["manufacturer"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 336);
/******/ })
/************************************************************************/
/******/ ({

/***/ 1:
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


/***/ }),

/***/ 10:
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
 * Class ExportToSqlManagerExtension extends grid with exporting query to SQL Manager
 */

var ExportToSqlManagerExtension = function () {
  function ExportToSqlManagerExtension() {
    _classCallCheck(this, ExportToSqlManagerExtension);
  }

  _createClass(ExportToSqlManagerExtension, [{
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

/***/ 11:
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
 * Handles submit of grid actions
 */

var SubmitBulkActionExtension = function () {
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

exports.default = SubmitBulkActionExtension;

/***/ }),

/***/ 12:
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
 * Class SubmitRowActionExtension handles submitting of row action
 */

var SubmitRowActionExtension = function () {
  function SubmitRowActionExtension() {
    _classCallCheck(this, SubmitRowActionExtension);
  }

  _createClass(SubmitRowActionExtension, [{
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

/***/ 13:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {

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
    _classCallCheck(this, TableSorting);

    this.selector = '.ps-sortable-column';
    this.columns = $(table).find(this.selector);
  }

  /**
   * Attaches the listeners
   */


  _createClass(TableSorting, [{
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
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ }),

/***/ 14:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {

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
 * Send a Post Request to reset search Action.
 */

var $ = global.$;

var init = function resetSearch(url, redirectUrl) {
  $.post(url).then(function () {
    return window.location.assign(redirectUrl);
  });
};

exports.default = init;
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ }),

/***/ 16:
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

var _eventEmitter = __webpack_require__(18);

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.$;

/**
 * This class is used to automatically toggle translated inputs (displayed with one
 * input and a language selector using the TranslatableType Symfony form type).
 * Also compatible with TranslatableField changes.
 */

var TranslatableInput = function () {
  function TranslatableInput(options) {
    _classCallCheck(this, TranslatableInput);

    options = options || {};

    this.localeItemSelector = options.localeItemSelector || '.js-locale-item';
    this.localeButtonSelector = options.localeButtonSelector || '.js-locale-btn';
    this.localeInputSelector = options.localeInputSelector || '.js-locale-input';

    $('body').on('click', this.localeItemSelector, this.toggleLanguage.bind(this));
    _eventEmitter.EventEmitter.on('languageSelected', this.toggleInputs.bind(this));
  }

  /**
   * Dispatch event on language selection to update inputs and other components which depend on the locale.
   *
   * @param event
   */


  _createClass(TranslatableInput, [{
    key: 'toggleLanguage',
    value: function toggleLanguage(event) {
      var localeItem = $(event.target);
      var form = localeItem.closest('form');
      _eventEmitter.EventEmitter.emit('languageSelected', { selectedLocale: localeItem.data('locale'), form: form });
    }

    /**
     * Toggle all translatable inputs in form in which locale was changed
     *
     * @param {Event} event
     */

  }, {
    key: 'toggleInputs',
    value: function toggleInputs(event) {
      var form = event.form;
      var selectedLocale = event.selectedLocale;
      var localeButton = form.find(this.localeButtonSelector);
      var changeLanguageUrl = localeButton.data('change-language-url');

      localeButton.text(selectedLocale);
      form.find(this.localeInputSelector).addClass('d-none');
      form.find(this.localeInputSelector + '.js-locale-' + selectedLocale).removeClass('d-none');

      if (changeLanguageUrl) {
        this._saveSelectedLanguage(changeLanguageUrl, selectedLocale);
      }
    }

    /**
     * Save language choice for employee forms.
     *
     * @param {String} changeLanguageUrl
     * @param {String} selectedLocale
     *
     * @private
     */

  }, {
    key: '_saveSelectedLanguage',
    value: function _saveSelectedLanguage(changeLanguageUrl, selectedLocale) {
      $.post({
        url: changeLanguageUrl,
        data: {
          language_iso_code: selectedLocale
        }
      });
    }
  }]);

  return TranslatableInput;
}();

exports.default = TranslatableInput;

/***/ }),

/***/ 17:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {

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

var $ = global.$;

/**
 * Class ReloadListExtension extends grid with "Column toggling" feature
 */

var ColumnTogglingExtension = function () {
  function ColumnTogglingExtension() {
    _classCallCheck(this, ColumnTogglingExtension);
  }

  _createClass(ColumnTogglingExtension, [{
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
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ }),

/***/ 18:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.EventEmitter = undefined;

var _events = __webpack_require__(20);

var _events2 = _interopRequireDefault(_events);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * We instanciate one EventEmitter (restricted via a const) so that every components
 * register/dispatch on the same one and can communicate with each other.
 */
var EventEmitter = exports.EventEmitter = new _events2.default(); /**
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

/***/ }),

/***/ 19:
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
 * Handles UI interactions of choice tree
 */

var ChoiceTree = function () {
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


  _createClass(ChoiceTree, [{
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

/***/ 20:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
// Copyright Joyent, Inc. and other Node contributors.
//
// Permission is hereby granted, free of charge, to any person obtaining a
// copy of this software and associated documentation files (the
// "Software"), to deal in the Software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of the Software, and to permit
// persons to whom the Software is furnished to do so, subject to the
// following conditions:
//
// The above copyright notice and this permission notice shall be included
// in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
// USE OR OTHER DEALINGS IN THE SOFTWARE.



var R = typeof Reflect === 'object' ? Reflect : null
var ReflectApply = R && typeof R.apply === 'function'
  ? R.apply
  : function ReflectApply(target, receiver, args) {
    return Function.prototype.apply.call(target, receiver, args);
  }

var ReflectOwnKeys
if (R && typeof R.ownKeys === 'function') {
  ReflectOwnKeys = R.ownKeys
} else if (Object.getOwnPropertySymbols) {
  ReflectOwnKeys = function ReflectOwnKeys(target) {
    return Object.getOwnPropertyNames(target)
      .concat(Object.getOwnPropertySymbols(target));
  };
} else {
  ReflectOwnKeys = function ReflectOwnKeys(target) {
    return Object.getOwnPropertyNames(target);
  };
}

function ProcessEmitWarning(warning) {
  if (console && console.warn) console.warn(warning);
}

var NumberIsNaN = Number.isNaN || function NumberIsNaN(value) {
  return value !== value;
}

function EventEmitter() {
  EventEmitter.init.call(this);
}
module.exports = EventEmitter;

// Backwards-compat with node 0.10.x
EventEmitter.EventEmitter = EventEmitter;

EventEmitter.prototype._events = undefined;
EventEmitter.prototype._eventsCount = 0;
EventEmitter.prototype._maxListeners = undefined;

// By default EventEmitters will print a warning if more than 10 listeners are
// added to it. This is a useful default which helps finding memory leaks.
var defaultMaxListeners = 10;

Object.defineProperty(EventEmitter, 'defaultMaxListeners', {
  enumerable: true,
  get: function() {
    return defaultMaxListeners;
  },
  set: function(arg) {
    if (typeof arg !== 'number' || arg < 0 || NumberIsNaN(arg)) {
      throw new RangeError('The value of "defaultMaxListeners" is out of range. It must be a non-negative number. Received ' + arg + '.');
    }
    defaultMaxListeners = arg;
  }
});

EventEmitter.init = function() {

  if (this._events === undefined ||
      this._events === Object.getPrototypeOf(this)._events) {
    this._events = Object.create(null);
    this._eventsCount = 0;
  }

  this._maxListeners = this._maxListeners || undefined;
};

// Obviously not all Emitters should be limited to 10. This function allows
// that to be increased. Set to zero for unlimited.
EventEmitter.prototype.setMaxListeners = function setMaxListeners(n) {
  if (typeof n !== 'number' || n < 0 || NumberIsNaN(n)) {
    throw new RangeError('The value of "n" is out of range. It must be a non-negative number. Received ' + n + '.');
  }
  this._maxListeners = n;
  return this;
};

function $getMaxListeners(that) {
  if (that._maxListeners === undefined)
    return EventEmitter.defaultMaxListeners;
  return that._maxListeners;
}

EventEmitter.prototype.getMaxListeners = function getMaxListeners() {
  return $getMaxListeners(this);
};

EventEmitter.prototype.emit = function emit(type) {
  var args = [];
  for (var i = 1; i < arguments.length; i++) args.push(arguments[i]);
  var doError = (type === 'error');

  var events = this._events;
  if (events !== undefined)
    doError = (doError && events.error === undefined);
  else if (!doError)
    return false;

  // If there is no 'error' event listener then throw.
  if (doError) {
    var er;
    if (args.length > 0)
      er = args[0];
    if (er instanceof Error) {
      // Note: The comments on the `throw` lines are intentional, they show
      // up in Node's output if this results in an unhandled exception.
      throw er; // Unhandled 'error' event
    }
    // At least give some kind of context to the user
    var err = new Error('Unhandled error.' + (er ? ' (' + er.message + ')' : ''));
    err.context = er;
    throw err; // Unhandled 'error' event
  }

  var handler = events[type];

  if (handler === undefined)
    return false;

  if (typeof handler === 'function') {
    ReflectApply(handler, this, args);
  } else {
    var len = handler.length;
    var listeners = arrayClone(handler, len);
    for (var i = 0; i < len; ++i)
      ReflectApply(listeners[i], this, args);
  }

  return true;
};

function _addListener(target, type, listener, prepend) {
  var m;
  var events;
  var existing;

  if (typeof listener !== 'function') {
    throw new TypeError('The "listener" argument must be of type Function. Received type ' + typeof listener);
  }

  events = target._events;
  if (events === undefined) {
    events = target._events = Object.create(null);
    target._eventsCount = 0;
  } else {
    // To avoid recursion in the case that type === "newListener"! Before
    // adding it to the listeners, first emit "newListener".
    if (events.newListener !== undefined) {
      target.emit('newListener', type,
                  listener.listener ? listener.listener : listener);

      // Re-assign `events` because a newListener handler could have caused the
      // this._events to be assigned to a new object
      events = target._events;
    }
    existing = events[type];
  }

  if (existing === undefined) {
    // Optimize the case of one listener. Don't need the extra array object.
    existing = events[type] = listener;
    ++target._eventsCount;
  } else {
    if (typeof existing === 'function') {
      // Adding the second element, need to change to array.
      existing = events[type] =
        prepend ? [listener, existing] : [existing, listener];
      // If we've already got an array, just append.
    } else if (prepend) {
      existing.unshift(listener);
    } else {
      existing.push(listener);
    }

    // Check for listener leak
    m = $getMaxListeners(target);
    if (m > 0 && existing.length > m && !existing.warned) {
      existing.warned = true;
      // No error code for this since it is a Warning
      // eslint-disable-next-line no-restricted-syntax
      var w = new Error('Possible EventEmitter memory leak detected. ' +
                          existing.length + ' ' + String(type) + ' listeners ' +
                          'added. Use emitter.setMaxListeners() to ' +
                          'increase limit');
      w.name = 'MaxListenersExceededWarning';
      w.emitter = target;
      w.type = type;
      w.count = existing.length;
      ProcessEmitWarning(w);
    }
  }

  return target;
}

EventEmitter.prototype.addListener = function addListener(type, listener) {
  return _addListener(this, type, listener, false);
};

EventEmitter.prototype.on = EventEmitter.prototype.addListener;

EventEmitter.prototype.prependListener =
    function prependListener(type, listener) {
      return _addListener(this, type, listener, true);
    };

function onceWrapper() {
  var args = [];
  for (var i = 0; i < arguments.length; i++) args.push(arguments[i]);
  if (!this.fired) {
    this.target.removeListener(this.type, this.wrapFn);
    this.fired = true;
    ReflectApply(this.listener, this.target, args);
  }
}

function _onceWrap(target, type, listener) {
  var state = { fired: false, wrapFn: undefined, target: target, type: type, listener: listener };
  var wrapped = onceWrapper.bind(state);
  wrapped.listener = listener;
  state.wrapFn = wrapped;
  return wrapped;
}

EventEmitter.prototype.once = function once(type, listener) {
  if (typeof listener !== 'function') {
    throw new TypeError('The "listener" argument must be of type Function. Received type ' + typeof listener);
  }
  this.on(type, _onceWrap(this, type, listener));
  return this;
};

EventEmitter.prototype.prependOnceListener =
    function prependOnceListener(type, listener) {
      if (typeof listener !== 'function') {
        throw new TypeError('The "listener" argument must be of type Function. Received type ' + typeof listener);
      }
      this.prependListener(type, _onceWrap(this, type, listener));
      return this;
    };

// Emits a 'removeListener' event if and only if the listener was removed.
EventEmitter.prototype.removeListener =
    function removeListener(type, listener) {
      var list, events, position, i, originalListener;

      if (typeof listener !== 'function') {
        throw new TypeError('The "listener" argument must be of type Function. Received type ' + typeof listener);
      }

      events = this._events;
      if (events === undefined)
        return this;

      list = events[type];
      if (list === undefined)
        return this;

      if (list === listener || list.listener === listener) {
        if (--this._eventsCount === 0)
          this._events = Object.create(null);
        else {
          delete events[type];
          if (events.removeListener)
            this.emit('removeListener', type, list.listener || listener);
        }
      } else if (typeof list !== 'function') {
        position = -1;

        for (i = list.length - 1; i >= 0; i--) {
          if (list[i] === listener || list[i].listener === listener) {
            originalListener = list[i].listener;
            position = i;
            break;
          }
        }

        if (position < 0)
          return this;

        if (position === 0)
          list.shift();
        else {
          spliceOne(list, position);
        }

        if (list.length === 1)
          events[type] = list[0];

        if (events.removeListener !== undefined)
          this.emit('removeListener', type, originalListener || listener);
      }

      return this;
    };

EventEmitter.prototype.off = EventEmitter.prototype.removeListener;

EventEmitter.prototype.removeAllListeners =
    function removeAllListeners(type) {
      var listeners, events, i;

      events = this._events;
      if (events === undefined)
        return this;

      // not listening for removeListener, no need to emit
      if (events.removeListener === undefined) {
        if (arguments.length === 0) {
          this._events = Object.create(null);
          this._eventsCount = 0;
        } else if (events[type] !== undefined) {
          if (--this._eventsCount === 0)
            this._events = Object.create(null);
          else
            delete events[type];
        }
        return this;
      }

      // emit removeListener for all listeners on all events
      if (arguments.length === 0) {
        var keys = Object.keys(events);
        var key;
        for (i = 0; i < keys.length; ++i) {
          key = keys[i];
          if (key === 'removeListener') continue;
          this.removeAllListeners(key);
        }
        this.removeAllListeners('removeListener');
        this._events = Object.create(null);
        this._eventsCount = 0;
        return this;
      }

      listeners = events[type];

      if (typeof listeners === 'function') {
        this.removeListener(type, listeners);
      } else if (listeners !== undefined) {
        // LIFO order
        for (i = listeners.length - 1; i >= 0; i--) {
          this.removeListener(type, listeners[i]);
        }
      }

      return this;
    };

function _listeners(target, type, unwrap) {
  var events = target._events;

  if (events === undefined)
    return [];

  var evlistener = events[type];
  if (evlistener === undefined)
    return [];

  if (typeof evlistener === 'function')
    return unwrap ? [evlistener.listener || evlistener] : [evlistener];

  return unwrap ?
    unwrapListeners(evlistener) : arrayClone(evlistener, evlistener.length);
}

EventEmitter.prototype.listeners = function listeners(type) {
  return _listeners(this, type, true);
};

EventEmitter.prototype.rawListeners = function rawListeners(type) {
  return _listeners(this, type, false);
};

EventEmitter.listenerCount = function(emitter, type) {
  if (typeof emitter.listenerCount === 'function') {
    return emitter.listenerCount(type);
  } else {
    return listenerCount.call(emitter, type);
  }
};

EventEmitter.prototype.listenerCount = listenerCount;
function listenerCount(type) {
  var events = this._events;

  if (events !== undefined) {
    var evlistener = events[type];

    if (typeof evlistener === 'function') {
      return 1;
    } else if (evlistener !== undefined) {
      return evlistener.length;
    }
  }

  return 0;
}

EventEmitter.prototype.eventNames = function eventNames() {
  return this._eventsCount > 0 ? ReflectOwnKeys(this._events) : [];
};

function arrayClone(arr, n) {
  var copy = new Array(n);
  for (var i = 0; i < n; ++i)
    copy[i] = arr[i];
  return copy;
}

function spliceOne(list, index) {
  for (; index + 1 < list.length; index++)
    list[index] = list[index + 1];
  list.pop();
}

function unwrapListeners(arr) {
  var ret = new Array(arr.length);
  for (var i = 0; i < ret.length; ++i) {
    ret[i] = arr[i].listener || arr[i];
  }
  return ret;
}


/***/ }),

/***/ 3:
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
 * Class is responsible for handling Grid events
 */

var Grid = function () {
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

/***/ 30:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

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
 * class TaggableField is responsible for providing functionality from bootstrap-tokenfield plugin.
 * It allows to have taggable fields which are split in separate blocks once you click enter. Values originally saved
 * in comma split strings.
 */

var TaggableField =
/**
 * @param {string} tokenFieldSelector -  a selector which is used within jQuery object.
 * @param {object} options - extends basic tokenField behavior with additional options such as minLength, delimiter,
 * allow to add token on focus out action. See bootstrap-tokenfield docs for more information.
 */
function TaggableField(_ref) {
  var tokenFieldSelector = _ref.tokenFieldSelector,
      _ref$options = _ref.options,
      options = _ref$options === undefined ? {} : _ref$options;

  _classCallCheck(this, TaggableField);

  $(tokenFieldSelector).tokenfield(options);
};

exports.default = TaggableField;

/***/ }),

/***/ 32:
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
 * This class init TinyMCE instances in the back-office. It is wildly inspired by
 * the scripts from js/admin And it actually loads TinyMCE from the js/tiny_mce
 * folder along with its modules. One improvement could be to install TinyMCE via
 * npm and fully integrate in the back-office theme.
 */

var TinyMCEEditor = function () {
  function TinyMCEEditor(options) {
    _classCallCheck(this, TinyMCEEditor);

    options = options || {};
    this.tinyMCELoaded = false;
    if (typeof options.baseAdminUrl == 'undefined') {
      if (typeof window.baseAdminDir != 'undefined') {
        options.baseAdminUrl = window.baseAdminDir;
      } else {
        var pathParts = window.location.pathname.split('/');
        pathParts.every(function (pathPart) {
          if (pathPart !== '') {
            options.baseAdminUrl = '/' + pathPart + '/';

            return false;
          }

          return true;
        });
      }
    }
    if (typeof options.langIsRtl == 'undefined') {
      options.langIsRtl = typeof window.lang_is_rtl != 'undefined' ? window.lang_is_rtl === '1' : false;
    }
    this.setupTinyMCE(options);
  }

  /**
   * Initial setup which checks if the tinyMCE library is already loaded.
   *
   * @param config
   */


  _createClass(TinyMCEEditor, [{
    key: 'setupTinyMCE',
    value: function setupTinyMCE(config) {
      if (typeof tinyMCE === 'undefined') {
        this.loadAndInitTinyMCE(config);
      } else {
        this.initTinyMCE(config);
      }
    }

    /**
     * Prepare the config and init all TinyMCE editors
     *
     * @param config
     */

  }, {
    key: 'initTinyMCE',
    value: function initTinyMCE(config) {
      var _this = this;

      config = Object.assign({
        selector: '.rte',
        plugins: 'align colorpicker link image filemanager table media placeholder advlist code table autoresize',
        browser_spellcheck: true,
        toolbar1: 'code,colorpicker,bold,italic,underline,strikethrough,blockquote,link,align,bullist,numlist,table,image,media,formatselect',
        toolbar2: '',
        external_filemanager_path: config.baseAdminUrl + 'filemanager/',
        filemanager_title: 'File manager',
        external_plugins: {
          'filemanager': config.baseAdminUrl + 'filemanager/plugin.min.js'
        },
        language: iso_user,
        content_style: config.langIsRtl ? 'body {direction:rtl;}' : '',
        skin: 'prestashop',
        menubar: false,
        statusbar: false,
        relative_urls: false,
        convert_urls: false,
        entity_encoding: 'raw',
        extended_valid_elements: 'em[class|name|id],@[role|data-*|aria-*]',
        valid_children: '+*[*]',
        valid_elements: '*[*]',
        rel_list: [{ title: 'nofollow', value: 'nofollow' }],
        editor_selector: 'autoload_rte',
        init_instance_callback: function init_instance_callback() {
          _this.changeToMaterial();
        },
        setup: function setup(editor) {
          _this.setupEditor(editor);
        }
      }, config);

      if (typeof config.editor_selector != 'undefined') {
        config.selector = '.' + config.editor_selector;
      }

      // Change icons in popups
      $('body').on('click', '.mce-btn, .mce-open, .mce-menu-item', function () {
        _this.changeToMaterial();
      });

      tinyMCE.init(config);
      this.watchTabChanges(config);
    }

    /**
     * Setup TinyMCE editor once it has been initialized
     *
     * @param editor
     */

  }, {
    key: 'setupEditor',
    value: function setupEditor(editor) {
      var _this2 = this;

      editor.on('loadContent', function (event) {
        _this2.handleCounterTiny(event.target.id);
      });
      editor.on('change', function (event) {
        tinyMCE.triggerSave();
        _this2.handleCounterTiny(event.target.id);
      });
      editor.on('blur', function () {
        tinyMCE.triggerSave();
      });
    }

    /**
     * When the editor is inside a tab it can cause a bug on tab switching.
     * So we check if the editor is contained in a navigation and refresh the editor when its
     * parent tab is shown.
     *
     * @param config
     */

  }, {
    key: 'watchTabChanges',
    value: function watchTabChanges(config) {
      $(config.selector).each(function (index, textarea) {
        var translatedField = $(textarea).closest('.translation-field');
        var tabContainer = $(textarea).closest('.translations.tabbable');

        if (translatedField.length && tabContainer.length) {
          var textareaLocale = translatedField.data('locale');
          var textareaLinkSelector = '.nav-item a[data-locale="' + textareaLocale + '"]';

          $(textareaLinkSelector, tabContainer).on('shown.bs.tab', function () {
            var editor = tinyMCE.get(textarea.id);
            if (editor) {
              //Reset content to force refresh of editor
              editor.setContent(editor.getContent());
            }
          });
        }
      });
    }

    /**
     * Loads the TinyMCE javascript library and then init the editors
     *
     * @param config
     */

  }, {
    key: 'loadAndInitTinyMCE',
    value: function loadAndInitTinyMCE(config) {
      var _this3 = this;

      if (this.tinyMCELoaded) {
        return;
      }

      this.tinyMCELoaded = true;
      var pathArray = config.baseAdminUrl.split('/');
      pathArray.splice(pathArray.length - 2, 2);
      var finalPath = pathArray.join('/');
      window.tinyMCEPreInit = {};
      window.tinyMCEPreInit.base = finalPath + '/js/tiny_mce';
      window.tinyMCEPreInit.suffix = '.min';
      $.getScript(finalPath + '/js/tiny_mce/tinymce.min.js', function () {
        _this3.setupTinyMCE(config);
      });
    }

    /**
     * Replace initial TinyMCE icons with material icons
     */

  }, {
    key: 'changeToMaterial',
    value: function changeToMaterial() {
      var materialIconAssoc = {
        'mce-i-code': '<i class="material-icons">code</i>',
        'mce-i-none': '<i class="material-icons">format_color_text</i>',
        'mce-i-bold': '<i class="material-icons">format_bold</i>',
        'mce-i-italic': '<i class="material-icons">format_italic</i>',
        'mce-i-underline': '<i class="material-icons">format_underlined</i>',
        'mce-i-strikethrough': '<i class="material-icons">format_strikethrough</i>',
        'mce-i-blockquote': '<i class="material-icons">format_quote</i>',
        'mce-i-link': '<i class="material-icons">link</i>',
        'mce-i-alignleft': '<i class="material-icons">format_align_left</i>',
        'mce-i-aligncenter': '<i class="material-icons">format_align_center</i>',
        'mce-i-alignright': '<i class="material-icons">format_align_right</i>',
        'mce-i-alignjustify': '<i class="material-icons">format_align_justify</i>',
        'mce-i-bullist': '<i class="material-icons">format_list_bulleted</i>',
        'mce-i-numlist': '<i class="material-icons">format_list_numbered</i>',
        'mce-i-image': '<i class="material-icons">image</i>',
        'mce-i-table': '<i class="material-icons">grid_on</i>',
        'mce-i-media': '<i class="material-icons">video_library</i>',
        'mce-i-browse': '<i class="material-icons">attachment</i>',
        'mce-i-checkbox': '<i class="mce-ico mce-i-checkbox"></i>'
      };

      $.each(materialIconAssoc, function (index, value) {
        $('.' + index).replaceWith(value);
      });
    }

    /**
     * Updates the characters counter
     *
     * @param id
     */

  }, {
    key: 'handleCounterTiny',
    value: function handleCounterTiny(id) {
      var textarea = $('#' + id);
      var counter = textarea.attr('counter');
      var counterType = textarea.attr('counter_type');
      var max = tinyMCE.activeEditor.getBody().textContent.length;

      textarea.parent().find('span.currentLength').text(max);
      if ('recommended' !== counterType && max > counter) {
        textarea.parent().find('span.maxLength').addClass('text-danger');
      } else {
        textarea.parent().find('span.maxLength').removeClass('text-danger');
      }
    }
  }]);

  return TinyMCEEditor;
}();

exports.default = TinyMCEEditor;

/***/ }),

/***/ 336:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _translatableInput = __webpack_require__(16);

var _translatableInput2 = _interopRequireDefault(_translatableInput);

var _taggableField = __webpack_require__(30);

var _taggableField2 = _interopRequireDefault(_taggableField);

var _formSubmitButton = __webpack_require__(55);

var _formSubmitButton2 = _interopRequireDefault(_formSubmitButton);

var _grid = __webpack_require__(3);

var _grid2 = _interopRequireDefault(_grid);

var _sortingExtension = __webpack_require__(6);

var _sortingExtension2 = _interopRequireDefault(_sortingExtension);

var _filtersResetExtension = __webpack_require__(4);

var _filtersResetExtension2 = _interopRequireDefault(_filtersResetExtension);

var _reloadListExtension = __webpack_require__(5);

var _reloadListExtension2 = _interopRequireDefault(_reloadListExtension);

var _columnTogglingExtension = __webpack_require__(17);

var _columnTogglingExtension2 = _interopRequireDefault(_columnTogglingExtension);

var _submitRowActionExtension = __webpack_require__(12);

var _submitRowActionExtension2 = _interopRequireDefault(_submitRowActionExtension);

var _submitBulkActionExtension = __webpack_require__(11);

var _submitBulkActionExtension2 = _interopRequireDefault(_submitBulkActionExtension);

var _bulkActionCheckboxExtension = __webpack_require__(9);

var _bulkActionCheckboxExtension2 = _interopRequireDefault(_bulkActionCheckboxExtension);

var _exportToSqlManagerExtension = __webpack_require__(10);

var _exportToSqlManagerExtension2 = _interopRequireDefault(_exportToSqlManagerExtension);

var _filtersSubmitButtonEnablerExtension = __webpack_require__(8);

var _filtersSubmitButtonEnablerExtension2 = _interopRequireDefault(_filtersSubmitButtonEnablerExtension);

var _choiceTree = __webpack_require__(19);

var _choiceTree2 = _interopRequireDefault(_choiceTree);

var _translatableField = __webpack_require__(44);

var _translatableField2 = _interopRequireDefault(_translatableField);

var _tinymceEditor = __webpack_require__(32);

var _tinymceEditor2 = _interopRequireDefault(_tinymceEditor);

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
  ['manufacturer', 'manufacturer_address'].forEach(function (gridName) {
    var grid = new _grid2.default(gridName);
    grid.addExtension(new _exportToSqlManagerExtension2.default());
    grid.addExtension(new _reloadListExtension2.default());
    grid.addExtension(new _sortingExtension2.default());
    grid.addExtension(new _filtersResetExtension2.default());
    grid.addExtension(new _columnTogglingExtension2.default());
    grid.addExtension(new _submitRowActionExtension2.default());
    grid.addExtension(new _submitBulkActionExtension2.default());
    grid.addExtension(new _bulkActionCheckboxExtension2.default());
    grid.addExtension(new _filtersSubmitButtonEnablerExtension2.default());
  });

  new _translatableInput2.default();
  new _translatableField2.default();
  new _tinymceEditor2.default();
  new _taggableField2.default({
    tokenFieldSelector: 'input.js-taggable-field',
    options: {
      createTokensOnBlur: true
    }
  });

  new _formSubmitButton2.default();
  new _choiceTree2.default('#manufacturer_shop_association').enableAutoCheckChildren();
});

/***/ }),

/***/ 4:
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

var _reset_search = __webpack_require__(14);

var _reset_search2 = _interopRequireDefault(_reset_search);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.$;

/**
 * Class FiltersResetExtension extends grid with filters resetting
 */

var FiltersResetExtension = function () {
  function FiltersResetExtension() {
    _classCallCheck(this, FiltersResetExtension);
  }

  _createClass(FiltersResetExtension, [{
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

/***/ 44:
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

var _eventEmitter = __webpack_require__(18);

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.$;

/**
 * This class is used to automatically toggle translated fields (displayed with tabs
 * using the TranslateType Symfony form type).
 * Also compatible with TranslatableInput changes.
 */

var TranslatableField = function () {
  function TranslatableField(options) {
    _classCallCheck(this, TranslatableField);

    options = options || {};

    this.localeButtonSelector = options.localeButtonSelector || '.translationsLocales.nav .nav-item a[data-toggle="tab"]';
    this.localeNavigationSelector = options.localeNavigationSelector || '.translationsLocales.nav';

    $('body').on('shown.bs.tab', this.localeButtonSelector, this.toggleLanguage.bind(this));
    _eventEmitter.EventEmitter.on('languageSelected', this.toggleFields.bind(this));
  }

  /**
   * Dispatch event on language selection to update inputs and other components which depend on the locale.
   *
   * @param event
   */


  _createClass(TranslatableField, [{
    key: 'toggleLanguage',
    value: function toggleLanguage(event) {
      var localeLink = $(event.target);
      var form = localeLink.closest('form');
      _eventEmitter.EventEmitter.emit('languageSelected', { selectedLocale: localeLink.data('locale'), form: form });
    }

    /**
     * Toggle all transtation fields to the selected locale
     *
     * @param event
     */

  }, {
    key: 'toggleFields',
    value: function toggleFields(event) {
      $(this.localeNavigationSelector).each(function (index, navigation) {
        var selectedLink = $('.nav-item a.active', navigation);
        var selectedLocale = selectedLink.data('locale');
        if (event.selectedLocale !== selectedLocale) {
          $('.nav-item a[data-locale="' + event.selectedLocale + '"]', navigation).tab('show');
        }
      });
    }
  }]);

  return TranslatableField;
}();

exports.default = TranslatableField;

/***/ }),

/***/ 5:
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

/**
 * Class ReloadListExtension extends grid with "List reload" action
 */
var ReloadListExtension = function () {
  function ReloadListExtension() {
    _classCallCheck(this, ReloadListExtension);
  }

  _createClass(ReloadListExtension, [{
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

/***/ 55:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

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
  _classCallCheck(this, FormSubmitButton);

  $(document).on('click', '.js-form-submit-btn', function (event) {
    event.preventDefault();

    var $btn = $(this);

    if ($btn.data('form-confirm-message') && false === confirm($btn.data('form-confirm-message'))) {
      return;
    }

    var $form = $('<form>', {
      'action': $btn.data('form-submit-url'),
      'method': 'POST'
    });

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

/***/ 6:
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

var _tableSorting = __webpack_require__(13);

var _tableSorting2 = _interopRequireDefault(_tableSorting);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * Class ReloadListExtension extends grid with "List reload" action
 */
var SortingExtension = function () {
  function SortingExtension() {
    _classCallCheck(this, SortingExtension);
  }

  _createClass(SortingExtension, [{
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
}();

exports.default = SortingExtension;

/***/ }),

/***/ 8:
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

/**
 * Responsible for grid filters search and reset button availability when filter inputs changes.
 */
var FiltersSubmitButtonEnablerExtension = function () {
  function FiltersSubmitButtonEnablerExtension() {
    _classCallCheck(this, FiltersSubmitButtonEnablerExtension);
  }

  _createClass(FiltersSubmitButtonEnablerExtension, [{
    key: 'extend',


    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      var $filtersRow = grid.getContainer().find('.column-filters');
      $filtersRow.find('.grid-search-button').prop('disabled', true);

      $filtersRow.find('input, select').on('input', function () {
        $filtersRow.find('.grid-search-button').prop('disabled', false);
        $filtersRow.find('.js-grid-reset-button').prop('hidden', false);
      });
    }
  }]);

  return FiltersSubmitButtonEnablerExtension;
}();

exports.default = FiltersSubmitButtonEnablerExtension;

/***/ }),

/***/ 9:
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
 * Class BulkActionSelectCheckboxExtension
 */

var BulkActionCheckboxExtension = function () {
  function BulkActionCheckboxExtension() {
    _classCallCheck(this, BulkActionCheckboxExtension);
  }

  _createClass(BulkActionCheckboxExtension, [{
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

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNWQ5OTkwOTRkMTFhZWZmMGI1ODI/M2YxNioqKioqKioqIiwid2VicGFjazovLy8od2VicGFjaykvYnVpbGRpbi9nbG9iYWwuanM/MzY5OCoqKioqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZXhwb3J0LXRvLXNxbC1tYW5hZ2VyLWV4dGVuc2lvbi5qcz9lZDJhKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vc3VibWl0LWJ1bGstYWN0aW9uLWV4dGVuc2lvbi5qcz8xYjFmKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYWN0aW9uL3Jvdy9zdWJtaXQtcm93LWFjdGlvbi1leHRlbnNpb24uanM/MjdkMSoqKiIsIndlYnBhY2s6Ly8vLi9qcy9hcHAvdXRpbHMvdGFibGUtc29ydGluZy5qcz8xNWQ0KioqIiwid2VicGFjazovLy8uL2pzL2FwcC91dGlscy9yZXNldF9zZWFyY2guanM/MWE3ZioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL3RyYW5zbGF0YWJsZS1pbnB1dC5qcz8xNTk0KioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2NvbHVtbi10b2dnbGluZy1leHRlbnNpb24uanM/Njk0MyIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2V2ZW50LWVtaXR0ZXIuanM/MGUwMyoqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9mb3JtL2Nob2ljZS10cmVlLmpzPzU0MWEqKiIsIndlYnBhY2s6Ly8vLi9+L2V2ZW50cy9ldmVudHMuanM/N2M3MSoqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2dyaWQuanM/ODEzYSoqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL3RhZ2dhYmxlLWZpZWxkLmpzPzY0M2QqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy90aW55bWNlLWVkaXRvci5qcz81MjZiKiIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9tYW51ZmFjdHVyZXIvaW5kZXguanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXJlc2V0LWV4dGVuc2lvbi5qcz8xNmYxKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvdHJhbnNsYXRhYmxlLWZpZWxkLmpzP2Y4NmMqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vcmVsb2FkLWxpc3QtZXh0ZW5zaW9uLmpzP2QzZTAqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9mb3JtLXN1Ym1pdC1idXR0b24uanM/MzU2MiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3NvcnRpbmctZXh0ZW5zaW9uLmpzPzExM2UqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXN1Ym1pdC1idXR0b24tZW5hYmxlci1leHRlbnNpb24uanM/MDM3OSoqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2J1bGstYWN0aW9uLWNoZWNrYm94LWV4dGVuc2lvbi5qcz9iMDk3KioqIl0sIm5hbWVzIjpbIiQiLCJ3aW5kb3ciLCJFeHBvcnRUb1NxbE1hbmFnZXJFeHRlbnNpb24iLCJncmlkIiwiZ2V0SGVhZGVyQ29udGFpbmVyIiwib24iLCJfb25TaG93U3FsUXVlcnlDbGljayIsIl9vbkV4cG9ydFNxbE1hbmFnZXJDbGljayIsIiRzcWxNYW5hZ2VyRm9ybSIsImdldElkIiwiX2ZpbGxFeHBvcnRGb3JtIiwiJG1vZGFsIiwibW9kYWwiLCJzdWJtaXQiLCJxdWVyeSIsImdldENvbnRhaW5lciIsImZpbmQiLCJkYXRhIiwidmFsIiwiX2dldE5hbWVGcm9tQnJlYWRjcnVtYiIsIiRicmVhZGNydW1icyIsIm5hbWUiLCJlYWNoIiwiaSIsIml0ZW0iLCIkYnJlYWRjcnVtYiIsImJyZWFkY3J1bWJUaXRsZSIsImxlbmd0aCIsInRleHQiLCJjb25jYXQiLCJTdWJtaXRCdWxrQWN0aW9uRXh0ZW5zaW9uIiwiZXh0ZW5kIiwiZXZlbnQiLCIkc3VibWl0QnRuIiwiY3VycmVudFRhcmdldCIsImNvbmZpcm1NZXNzYWdlIiwiY29uZmlybSIsIiRmb3JtIiwiYXR0ciIsIlN1Ym1pdFJvd0FjdGlvbkV4dGVuc2lvbiIsInByZXZlbnREZWZhdWx0IiwiJGJ1dHRvbiIsIm1ldGhvZCIsImlzR2V0T3JQb3N0TWV0aG9kIiwiaW5jbHVkZXMiLCJhcHBlbmRUbyIsImFwcGVuZCIsImdsb2JhbCIsIlRhYmxlU29ydGluZyIsInRhYmxlIiwic2VsZWN0b3IiLCJjb2x1bW5zIiwiZSIsIiRjb2x1bW4iLCJkZWxlZ2F0ZVRhcmdldCIsIl9zb3J0QnlDb2x1bW4iLCJfZ2V0VG9nZ2xlZFNvcnREaXJlY3Rpb24iLCJjb2x1bW5OYW1lIiwiZGlyZWN0aW9uIiwiaXMiLCJFcnJvciIsImNvbHVtbiIsImxvY2F0aW9uIiwiX2dldFVybCIsImNvbE5hbWUiLCJwcmVmaXgiLCJ1cmwiLCJVUkwiLCJocmVmIiwicGFyYW1zIiwic2VhcmNoUGFyYW1zIiwic2V0IiwidG9TdHJpbmciLCJpbml0IiwicmVzZXRTZWFyY2giLCJyZWRpcmVjdFVybCIsInBvc3QiLCJ0aGVuIiwiYXNzaWduIiwiVHJhbnNsYXRhYmxlSW5wdXQiLCJvcHRpb25zIiwibG9jYWxlSXRlbVNlbGVjdG9yIiwibG9jYWxlQnV0dG9uU2VsZWN0b3IiLCJsb2NhbGVJbnB1dFNlbGVjdG9yIiwidG9nZ2xlTGFuZ3VhZ2UiLCJiaW5kIiwiRXZlbnRFbWl0dGVyIiwidG9nZ2xlSW5wdXRzIiwibG9jYWxlSXRlbSIsInRhcmdldCIsImZvcm0iLCJjbG9zZXN0IiwiZW1pdCIsInNlbGVjdGVkTG9jYWxlIiwibG9jYWxlQnV0dG9uIiwiY2hhbmdlTGFuZ3VhZ2VVcmwiLCJhZGRDbGFzcyIsInJlbW92ZUNsYXNzIiwiX3NhdmVTZWxlY3RlZExhbmd1YWdlIiwibGFuZ3VhZ2VfaXNvX2NvZGUiLCJDb2x1bW5Ub2dnbGluZ0V4dGVuc2lvbiIsIiR0YWJsZSIsIl90b2dnbGVWYWx1ZSIsInJvdyIsInRvZ2dsZVVybCIsIl9zdWJtaXRBc0Zvcm0iLCJhY3Rpb24iLCJFdmVudEVtaXR0ZXJDbGFzcyIsIkNob2ljZVRyZWUiLCJ0cmVlU2VsZWN0b3IiLCIkY29udGFpbmVyIiwiJGlucHV0V3JhcHBlciIsIl90b2dnbGVDaGlsZFRyZWUiLCIkYWN0aW9uIiwiX3RvZ2dsZVRyZWUiLCJlbmFibGVBdXRvQ2hlY2tDaGlsZHJlbiIsImVuYWJsZUFsbElucHV0cyIsImRpc2FibGVBbGxJbnB1dHMiLCIkY2xpY2tlZENoZWNrYm94IiwiJGl0ZW1XaXRoQ2hpbGRyZW4iLCJwcm9wIiwicmVtb3ZlQXR0ciIsIiRwYXJlbnRXcmFwcGVyIiwiaGFzQ2xhc3MiLCIkcGFyZW50Q29udGFpbmVyIiwiY29uZmlnIiwiZXhwYW5kIiwiY29sbGFwc2UiLCJuZXh0QWN0aW9uIiwiaWNvbiIsImluZGV4IiwiJGl0ZW0iLCJHcmlkIiwiaWQiLCJleHRlbnNpb24iLCJUYWdnYWJsZUZpZWxkIiwidG9rZW5GaWVsZFNlbGVjdG9yIiwidG9rZW5maWVsZCIsIlRpbnlNQ0VFZGl0b3IiLCJ0aW55TUNFTG9hZGVkIiwiYmFzZUFkbWluVXJsIiwiYmFzZUFkbWluRGlyIiwicGF0aFBhcnRzIiwicGF0aG5hbWUiLCJzcGxpdCIsImV2ZXJ5IiwicGF0aFBhcnQiLCJsYW5nSXNSdGwiLCJsYW5nX2lzX3J0bCIsInNldHVwVGlueU1DRSIsInRpbnlNQ0UiLCJsb2FkQW5kSW5pdFRpbnlNQ0UiLCJpbml0VGlueU1DRSIsIk9iamVjdCIsInBsdWdpbnMiLCJicm93c2VyX3NwZWxsY2hlY2siLCJ0b29sYmFyMSIsInRvb2xiYXIyIiwiZXh0ZXJuYWxfZmlsZW1hbmFnZXJfcGF0aCIsImZpbGVtYW5hZ2VyX3RpdGxlIiwiZXh0ZXJuYWxfcGx1Z2lucyIsImxhbmd1YWdlIiwiaXNvX3VzZXIiLCJjb250ZW50X3N0eWxlIiwic2tpbiIsIm1lbnViYXIiLCJzdGF0dXNiYXIiLCJyZWxhdGl2ZV91cmxzIiwiY29udmVydF91cmxzIiwiZW50aXR5X2VuY29kaW5nIiwiZXh0ZW5kZWRfdmFsaWRfZWxlbWVudHMiLCJ2YWxpZF9jaGlsZHJlbiIsInZhbGlkX2VsZW1lbnRzIiwicmVsX2xpc3QiLCJ0aXRsZSIsInZhbHVlIiwiZWRpdG9yX3NlbGVjdG9yIiwiaW5pdF9pbnN0YW5jZV9jYWxsYmFjayIsImNoYW5nZVRvTWF0ZXJpYWwiLCJzZXR1cCIsImVkaXRvciIsInNldHVwRWRpdG9yIiwid2F0Y2hUYWJDaGFuZ2VzIiwiaGFuZGxlQ291bnRlclRpbnkiLCJ0cmlnZ2VyU2F2ZSIsInRleHRhcmVhIiwidHJhbnNsYXRlZEZpZWxkIiwidGFiQ29udGFpbmVyIiwidGV4dGFyZWFMb2NhbGUiLCJ0ZXh0YXJlYUxpbmtTZWxlY3RvciIsImdldCIsInNldENvbnRlbnQiLCJnZXRDb250ZW50IiwicGF0aEFycmF5Iiwic3BsaWNlIiwiZmluYWxQYXRoIiwiam9pbiIsInRpbnlNQ0VQcmVJbml0IiwiYmFzZSIsInN1ZmZpeCIsImdldFNjcmlwdCIsIm1hdGVyaWFsSWNvbkFzc29jIiwicmVwbGFjZVdpdGgiLCJjb3VudGVyIiwiY291bnRlclR5cGUiLCJtYXgiLCJhY3RpdmVFZGl0b3IiLCJnZXRCb2R5IiwidGV4dENvbnRlbnQiLCJwYXJlbnQiLCJmb3JFYWNoIiwiZ3JpZE5hbWUiLCJhZGRFeHRlbnNpb24iLCJSZWxvYWRMaXN0QWN0aW9uRXh0ZW5zaW9uIiwiU29ydGluZ0V4dGVuc2lvbiIsIkZpbHRlcnNSZXNldEV4dGVuc2lvbiIsIlN1Ym1pdEJ1bGtFeHRlbnNpb24iLCJCdWxrQWN0aW9uQ2hlY2tib3hFeHRlbnNpb24iLCJGaWx0ZXJzU3VibWl0QnV0dG9uRW5hYmxlckV4dGVuc2lvbiIsIlRyYW5zbGF0YWJsZUZpZWxkIiwiY3JlYXRlVG9rZW5zT25CbHVyIiwiRm9ybVN1Ym1pdEJ1dHRvbiIsImxvY2FsZU5hdmlnYXRpb25TZWxlY3RvciIsInRvZ2dsZUZpZWxkcyIsImxvY2FsZUxpbmsiLCJuYXZpZ2F0aW9uIiwic2VsZWN0ZWRMaW5rIiwidGFiIiwiUmVsb2FkTGlzdEV4dGVuc2lvbiIsInJlbG9hZCIsImRvY3VtZW50IiwiJGJ0biIsIiRzb3J0YWJsZVRhYmxlIiwiYXR0YWNoIiwiJGZpbHRlcnNSb3ciLCJfaGFuZGxlQnVsa0FjdGlvbkNoZWNrYm94U2VsZWN0IiwiX2hhbmRsZUJ1bGtBY3Rpb25TZWxlY3RBbGxDaGVja2JveCIsIiRjaGVja2JveCIsImlzQ2hlY2tlZCIsIl9lbmFibGVCdWxrQWN0aW9uc0J0biIsIl9kaXNhYmxlQnVsa0FjdGlvbnNCdG4iLCJjaGVja2VkUm93c0NvdW50Il0sIm1hcHBpbmdzIjoiOztBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQSxtREFBMkMsY0FBYzs7QUFFekQ7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxhQUFLO0FBQ0w7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxtQ0FBMkIsMEJBQTBCLEVBQUU7QUFDdkQseUNBQWlDLGVBQWU7QUFDaEQ7QUFDQTtBQUNBOztBQUVBO0FBQ0EsOERBQXNELCtEQUErRDs7QUFFckg7QUFDQTs7QUFFQTtBQUNBOzs7Ozs7OztBQ2hFQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxDQUFDOztBQUVEO0FBQ0E7QUFDQTtBQUNBLENBQUM7QUFDRDtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0EsNENBQTRDOztBQUU1Qzs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQ3BCQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7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RDs7QUFFRDs7Ozs7Ozs7OztvREFPZ0MzTyxJLEVBQU07QUFBQTs7QUFDcENBLFdBQUtZLFlBQUwsR0FBb0JWLEVBQXBCLENBQXVCLFFBQXZCLEVBQWlDLDBCQUFqQyxFQUE2RCxZQUFNO0FBQ2pFLFlBQU00TyxtQkFBbUI5TyxLQUFLWSxZQUFMLEdBQW9CQyxJQUFwQixDQUF5QixrQ0FBekIsRUFBNkRXLE1BQXRGOztBQUVBLFlBQUlzTixtQkFBbUIsQ0FBdkIsRUFBMEI7QUFDeEIsaUJBQUtGLHFCQUFMLENBQTJCNU8sSUFBM0I7QUFDRCxTQUZELE1BRU87QUFDTCxpQkFBSzZPLHNCQUFMLENBQTRCN08sSUFBNUI7QUFDRDtBQUNGLE9BUkQ7QUFTRDs7QUFFRDs7Ozs7Ozs7OzswQ0FPc0JBLEksRUFBTTtBQUMxQkEsV0FBS1ksWUFBTCxHQUFvQkMsSUFBcEIsQ0FBeUIsc0JBQXpCLEVBQWlEd0csSUFBakQsQ0FBc0QsVUFBdEQsRUFBa0UsS0FBbEU7QUFDRDs7QUFFRDs7Ozs7Ozs7OzsyQ0FPdUJySCxJLEVBQU07QUFDM0JBLFdBQUtZLFlBQUwsR0FBb0JDLElBQXBCLENBQXlCLHNCQUF6QixFQUFpRHdHLElBQWpELENBQXNELFVBQXRELEVBQWtFLElBQWxFO0FBQ0Q7Ozs7OztrQkF4RWtCaUcsMkIiLCJmaWxlIjoibWFudWZhY3R1cmVyLmJ1bmRsZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGlkZW50aXR5IGZ1bmN0aW9uIGZvciBjYWxsaW5nIGhhcm1vbnkgaW1wb3J0cyB3aXRoIHRoZSBjb3JyZWN0IGNvbnRleHRcbiBcdF9fd2VicGFja19yZXF1aXJlX18uaSA9IGZ1bmN0aW9uKHZhbHVlKSB7IHJldHVybiB2YWx1ZTsgfTtcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7XG4gXHRcdFx0XHRjb25maWd1cmFibGU6IGZhbHNlLFxuIFx0XHRcdFx0ZW51bWVyYWJsZTogdHJ1ZSxcbiBcdFx0XHRcdGdldDogZ2V0dGVyXG4gXHRcdFx0fSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gMzM2KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyB3ZWJwYWNrL2Jvb3RzdHJhcCA1ZDk5OTA5NGQxMWFlZmYwYjU4MiIsInZhciBnO1xyXG5cclxuLy8gVGhpcyB3b3JrcyBpbiBub24tc3RyaWN0IG1vZGVcclxuZyA9IChmdW5jdGlvbigpIHtcclxuXHRyZXR1cm4gdGhpcztcclxufSkoKTtcclxuXHJcbnRyeSB7XHJcblx0Ly8gVGhpcyB3b3JrcyBpZiBldmFsIGlzIGFsbG93ZWQgKHNlZSBDU1ApXHJcblx0ZyA9IGcgfHwgRnVuY3Rpb24oXCJyZXR1cm4gdGhpc1wiKSgpIHx8ICgxLGV2YWwpKFwidGhpc1wiKTtcclxufSBjYXRjaChlKSB7XHJcblx0Ly8gVGhpcyB3b3JrcyBpZiB0aGUgd2luZG93IHJlZmVyZW5jZSBpcyBhdmFpbGFibGVcclxuXHRpZih0eXBlb2Ygd2luZG93ID09PSBcIm9iamVjdFwiKVxyXG5cdFx0ZyA9IHdpbmRvdztcclxufVxyXG5cclxuLy8gZyBjYW4gc3RpbGwgYmUgdW5kZWZpbmVkLCBidXQgbm90aGluZyB0byBkbyBhYm91dCBpdC4uLlxyXG4vLyBXZSByZXR1cm4gdW5kZWZpbmVkLCBpbnN0ZWFkIG9mIG5vdGhpbmcgaGVyZSwgc28gaXQnc1xyXG4vLyBlYXNpZXIgdG8gaGFuZGxlIHRoaXMgY2FzZS4gaWYoIWdsb2JhbCkgeyAuLi59XHJcblxyXG5tb2R1bGUuZXhwb3J0cyA9IGc7XHJcblxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vICh3ZWJwYWNrKS9idWlsZGluL2dsb2JhbC5qc1xuLy8gbW9kdWxlIGlkID0gMVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjUgMzAgMzUiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgRXhwb3J0VG9TcWxNYW5hZ2VyRXh0ZW5zaW9uIGV4dGVuZHMgZ3JpZCB3aXRoIGV4cG9ydGluZyBxdWVyeSB0byBTUUwgTWFuYWdlclxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBFeHBvcnRUb1NxbE1hbmFnZXJFeHRlbnNpb24ge1xuICAvKipcbiAgICogRXh0ZW5kIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIGdyaWQuZ2V0SGVhZGVyQ29udGFpbmVyKCkub24oJ2NsaWNrJywgJy5qcy1jb21tb25fc2hvd19xdWVyeS1ncmlkLWFjdGlvbicsICgpID0+IHRoaXMuX29uU2hvd1NxbFF1ZXJ5Q2xpY2soZ3JpZCkpO1xuICAgIGdyaWQuZ2V0SGVhZGVyQ29udGFpbmVyKCkub24oJ2NsaWNrJywgJy5qcy1jb21tb25fZXhwb3J0X3NxbF9tYW5hZ2VyLWdyaWQtYWN0aW9uJywgKCkgPT4gdGhpcy5fb25FeHBvcnRTcWxNYW5hZ2VyQ2xpY2soZ3JpZCkpO1xuICB9XG5cbiAgLyoqXG4gICAqIEludm9rZWQgd2hlbiBjbGlja2luZyBvbiB0aGUgXCJzaG93IHNxbCBxdWVyeVwiIHRvb2xiYXIgYnV0dG9uXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uU2hvd1NxbFF1ZXJ5Q2xpY2soZ3JpZCkge1xuICAgIGNvbnN0ICRzcWxNYW5hZ2VyRm9ybSA9ICQoJyMnICsgZ3JpZC5nZXRJZCgpICsgJ19jb21tb25fc2hvd19xdWVyeV9tb2RhbF9mb3JtJyk7XG4gICAgdGhpcy5fZmlsbEV4cG9ydEZvcm0oJHNxbE1hbmFnZXJGb3JtLCBncmlkKTtcblxuICAgIGNvbnN0ICRtb2RhbCA9ICQoJyMnICsgZ3JpZC5nZXRJZCgpICsgJ19ncmlkX2NvbW1vbl9zaG93X3F1ZXJ5X21vZGFsJyk7XG4gICAgJG1vZGFsLm1vZGFsKCdzaG93Jyk7XG5cbiAgICAkbW9kYWwub24oJ2NsaWNrJywgJy5idG4tc3FsLXN1Ym1pdCcsICgpID0+ICRzcWxNYW5hZ2VyRm9ybS5zdWJtaXQoKSk7XG4gIH1cblxuICAvKipcbiAgICogSW52b2tlZCB3aGVuIGNsaWNraW5nIG9uIHRoZSBcImV4cG9ydCB0byB0aGUgc3FsIHF1ZXJ5XCIgdG9vbGJhciBidXR0b25cbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfb25FeHBvcnRTcWxNYW5hZ2VyQ2xpY2soZ3JpZCkge1xuICAgIGNvbnN0ICRzcWxNYW5hZ2VyRm9ybSA9ICQoJyMnICsgZ3JpZC5nZXRJZCgpICsgJ19jb21tb25fc2hvd19xdWVyeV9tb2RhbF9mb3JtJyk7XG5cbiAgICB0aGlzLl9maWxsRXhwb3J0Rm9ybSgkc3FsTWFuYWdlckZvcm0sIGdyaWQpO1xuXG4gICAgJHNxbE1hbmFnZXJGb3JtLnN1Ym1pdCgpO1xuICB9XG5cbiAgLyoqXG4gICAqIEZpbGwgZXhwb3J0IGZvcm0gd2l0aCBTUUwgYW5kIGl0J3MgbmFtZVxuICAgKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gJHNxbE1hbmFnZXJGb3JtXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2ZpbGxFeHBvcnRGb3JtKCRzcWxNYW5hZ2VyRm9ybSwgZ3JpZCkge1xuICAgIGNvbnN0IHF1ZXJ5ID0gZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCcuanMtZ3JpZC10YWJsZScpLmRhdGEoJ3F1ZXJ5Jyk7XG5cbiAgICAkc3FsTWFuYWdlckZvcm0uZmluZCgndGV4dGFyZWFbbmFtZT1cInNxbFwiXScpLnZhbChxdWVyeSk7XG4gICAgJHNxbE1hbmFnZXJGb3JtLmZpbmQoJ2lucHV0W25hbWU9XCJuYW1lXCJdJykudmFsKHRoaXMuX2dldE5hbWVGcm9tQnJlYWRjcnVtYigpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgZXhwb3J0IG5hbWUgZnJvbSBwYWdlJ3MgYnJlYWRjcnVtYlxuICAgKlxuICAgKiBAcmV0dXJuIHtTdHJpbmd9XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfZ2V0TmFtZUZyb21CcmVhZGNydW1iKCkge1xuICAgIGNvbnN0ICRicmVhZGNydW1icyA9ICQoJy5oZWFkZXItdG9vbGJhcicpLmZpbmQoJy5icmVhZGNydW1iLWl0ZW0nKTtcbiAgICBsZXQgbmFtZSA9ICcnO1xuXG4gICAgJGJyZWFkY3J1bWJzLmVhY2goKGksIGl0ZW0pID0+IHtcbiAgICAgIGNvbnN0ICRicmVhZGNydW1iID0gJChpdGVtKTtcblxuICAgICAgY29uc3QgYnJlYWRjcnVtYlRpdGxlID0gMCA8ICRicmVhZGNydW1iLmZpbmQoJ2EnKS5sZW5ndGggP1xuICAgICAgICAkYnJlYWRjcnVtYi5maW5kKCdhJykudGV4dCgpIDpcbiAgICAgICAgJGJyZWFkY3J1bWIudGV4dCgpO1xuXG4gICAgICBpZiAoMCA8IG5hbWUubGVuZ3RoKSB7XG4gICAgICAgIG5hbWUgPSBuYW1lLmNvbmNhdCgnID4gJyk7XG4gICAgICB9XG5cbiAgICAgIG5hbWUgPSBuYW1lLmNvbmNhdChicmVhZGNydW1iVGl0bGUpO1xuICAgIH0pO1xuXG4gICAgcmV0dXJuIG5hbWU7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZXhwb3J0LXRvLXNxbC1tYW5hZ2VyLWV4dGVuc2lvbi5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBIYW5kbGVzIHN1Ym1pdCBvZiBncmlkIGFjdGlvbnNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgU3VibWl0QnVsa0FjdGlvbkV4dGVuc2lvbiB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHJldHVybiB7XG4gICAgICBleHRlbmQ6IChncmlkKSA9PiB0aGlzLmV4dGVuZChncmlkKSxcbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkIHdpdGggYnVsayBhY3Rpb24gc3VibWl0dGluZ1xuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLWJ1bGstYWN0aW9uLXN1Ym1pdC1idG4nLCAoZXZlbnQpID0+IHtcbiAgICAgIHRoaXMuc3VibWl0KGV2ZW50LCBncmlkKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGUgYnVsayBhY3Rpb24gc3VibWl0dGluZ1xuICAgKlxuICAgKiBAcGFyYW0ge0V2ZW50fSBldmVudFxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIHN1Ym1pdChldmVudCwgZ3JpZCkge1xuICAgIGNvbnN0ICRzdWJtaXRCdG4gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgIGNvbnN0IGNvbmZpcm1NZXNzYWdlID0gJHN1Ym1pdEJ0bi5kYXRhKCdjb25maXJtLW1lc3NhZ2UnKTtcblxuICAgIGlmICh0eXBlb2YgY29uZmlybU1lc3NhZ2UgIT09IFwidW5kZWZpbmVkXCIgJiYgMCA8IGNvbmZpcm1NZXNzYWdlLmxlbmd0aCAmJiAhY29uZmlybShjb25maXJtTWVzc2FnZSkpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBjb25zdCAkZm9ybSA9ICQoJyMnICsgZ3JpZC5nZXRJZCgpICsgJ19maWx0ZXJfZm9ybScpO1xuXG4gICAgJGZvcm0uYXR0cignYWN0aW9uJywgJHN1Ym1pdEJ0bi5kYXRhKCdmb3JtLXVybCcpKTtcbiAgICAkZm9ybS5hdHRyKCdtZXRob2QnLCAkc3VibWl0QnRuLmRhdGEoJ2Zvcm0tbWV0aG9kJykpO1xuICAgICRmb3JtLnN1Ym1pdCgpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3N1Ym1pdC1idWxrLWFjdGlvbi1leHRlbnNpb24uanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgU3VibWl0Um93QWN0aW9uRXh0ZW5zaW9uIGhhbmRsZXMgc3VibWl0dGluZyBvZiByb3cgYWN0aW9uXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFN1Ym1pdFJvd0FjdGlvbkV4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLXN1Ym1pdC1yb3ctYWN0aW9uJywgKGV2ZW50KSA9PiB7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICBjb25zdCAkYnV0dG9uID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICAgIGNvbnN0IGNvbmZpcm1NZXNzYWdlID0gJGJ1dHRvbi5kYXRhKCdjb25maXJtLW1lc3NhZ2UnKTtcblxuICAgICAgaWYgKGNvbmZpcm1NZXNzYWdlLmxlbmd0aCAmJiAhY29uZmlybShjb25maXJtTWVzc2FnZSkpIHtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICBjb25zdCBtZXRob2QgPSAkYnV0dG9uLmRhdGEoJ21ldGhvZCcpO1xuICAgICAgY29uc3QgaXNHZXRPclBvc3RNZXRob2QgPSBbJ0dFVCcsICdQT1NUJ10uaW5jbHVkZXMobWV0aG9kKTtcblxuICAgICAgY29uc3QgJGZvcm0gPSAkKCc8Zm9ybT4nLCB7XG4gICAgICAgICdhY3Rpb24nOiAkYnV0dG9uLmRhdGEoJ3VybCcpLFxuICAgICAgICAnbWV0aG9kJzogaXNHZXRPclBvc3RNZXRob2QgPyBtZXRob2QgOiAnUE9TVCcsXG4gICAgICB9KS5hcHBlbmRUbygnYm9keScpO1xuXG4gICAgICBpZiAoIWlzR2V0T3JQb3N0TWV0aG9kKSB7XG4gICAgICAgICRmb3JtLmFwcGVuZCgkKCc8aW5wdXQ+Jywge1xuICAgICAgICAgICd0eXBlJzogJ19oaWRkZW4nLFxuICAgICAgICAgICduYW1lJzogJ19tZXRob2QnLFxuICAgICAgICAgICd2YWx1ZSc6IG1ldGhvZFxuICAgICAgICB9KSk7XG4gICAgICB9XG5cbiAgICAgICRmb3JtLnN1Ym1pdCgpO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2FjdGlvbi9yb3cvc3VibWl0LXJvdy1hY3Rpb24tZXh0ZW5zaW9uLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IGdsb2JhbC4kO1xuXG4vKipcbiAqIE1ha2VzIGEgdGFibGUgc29ydGFibGUgYnkgY29sdW1ucy5cbiAqIFRoaXMgZm9yY2VzIGEgcGFnZSByZWxvYWQgd2l0aCBtb3JlIHF1ZXJ5IHBhcmFtZXRlcnMuXG4gKi9cbmNsYXNzIFRhYmxlU29ydGluZyB7XG5cbiAgLyoqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSB0YWJsZVxuICAgKi9cbiAgY29uc3RydWN0b3IodGFibGUpIHtcbiAgICB0aGlzLnNlbGVjdG9yID0gJy5wcy1zb3J0YWJsZS1jb2x1bW4nO1xuICAgIHRoaXMuY29sdW1ucyA9ICQodGFibGUpLmZpbmQodGhpcy5zZWxlY3Rvcik7XG4gIH1cblxuICAvKipcbiAgICogQXR0YWNoZXMgdGhlIGxpc3RlbmVyc1xuICAgKi9cbiAgYXR0YWNoKCkge1xuICAgIHRoaXMuY29sdW1ucy5vbignY2xpY2snLCAoZSkgPT4ge1xuICAgICAgY29uc3QgJGNvbHVtbiA9ICQoZS5kZWxlZ2F0ZVRhcmdldCk7XG4gICAgICB0aGlzLl9zb3J0QnlDb2x1bW4oJGNvbHVtbiwgdGhpcy5fZ2V0VG9nZ2xlZFNvcnREaXJlY3Rpb24oJGNvbHVtbikpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIFNvcnQgdXNpbmcgYSBjb2x1bW4gbmFtZVxuICAgKiBAcGFyYW0ge3N0cmluZ30gY29sdW1uTmFtZVxuICAgKiBAcGFyYW0ge3N0cmluZ30gZGlyZWN0aW9uIFwiYXNjXCIgb3IgXCJkZXNjXCJcbiAgICovXG4gIHNvcnRCeShjb2x1bW5OYW1lLCBkaXJlY3Rpb24pIHtcbiAgICBjb25zdCAkY29sdW1uID0gdGhpcy5jb2x1bW5zLmlzKGBbZGF0YS1zb3J0LWNvbC1uYW1lPVwiJHtjb2x1bW5OYW1lfVwiXWApO1xuICAgIGlmICghJGNvbHVtbikge1xuICAgICAgdGhyb3cgbmV3IEVycm9yKGBDYW5ub3Qgc29ydCBieSBcIiR7Y29sdW1uTmFtZX1cIjogaW52YWxpZCBjb2x1bW5gKTtcbiAgICB9XG5cbiAgICB0aGlzLl9zb3J0QnlDb2x1bW4oJGNvbHVtbiwgZGlyZWN0aW9uKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTb3J0IHVzaW5nIGEgY29sdW1uIGVsZW1lbnRcbiAgICogQHBhcmFtIHtqUXVlcnl9IGNvbHVtblxuICAgKiBAcGFyYW0ge3N0cmluZ30gZGlyZWN0aW9uIFwiYXNjXCIgb3IgXCJkZXNjXCJcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zb3J0QnlDb2x1bW4oY29sdW1uLCBkaXJlY3Rpb24pIHtcbiAgICB3aW5kb3cubG9jYXRpb24gPSB0aGlzLl9nZXRVcmwoY29sdW1uLmRhdGEoJ3NvcnRDb2xOYW1lJyksIChkaXJlY3Rpb24gPT09ICdkZXNjJykgPyAnZGVzYycgOiAnYXNjJywgY29sdW1uLmRhdGEoJ3NvcnRQcmVmaXgnKSk7XG4gIH1cblxuICAvKipcbiAgICogUmV0dXJucyB0aGUgaW52ZXJ0ZWQgZGlyZWN0aW9uIHRvIHNvcnQgYWNjb3JkaW5nIHRvIHRoZSBjb2x1bW4ncyBjdXJyZW50IG9uZVxuICAgKiBAcGFyYW0ge2pRdWVyeX0gY29sdW1uXG4gICAqIEByZXR1cm4ge3N0cmluZ31cbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9nZXRUb2dnbGVkU29ydERpcmVjdGlvbihjb2x1bW4pIHtcbiAgICByZXR1cm4gY29sdW1uLmRhdGEoJ3NvcnREaXJlY3Rpb24nKSA9PT0gJ2FzYycgPyAnZGVzYycgOiAnYXNjJztcbiAgfVxuXG4gIC8qKlxuICAgKiBSZXR1cm5zIHRoZSB1cmwgZm9yIHRoZSBzb3J0ZWQgdGFibGVcbiAgICogQHBhcmFtIHtzdHJpbmd9IGNvbE5hbWVcbiAgICogQHBhcmFtIHtzdHJpbmd9IGRpcmVjdGlvblxuICAgKiBAcGFyYW0ge3N0cmluZ30gcHJlZml4XG4gICAqIEByZXR1cm4ge3N0cmluZ31cbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9nZXRVcmwoY29sTmFtZSwgZGlyZWN0aW9uLCBwcmVmaXgpIHtcbiAgICBjb25zdCB1cmwgPSBuZXcgVVJMKHdpbmRvdy5sb2NhdGlvbi5ocmVmKTtcbiAgICBjb25zdCBwYXJhbXMgPSB1cmwuc2VhcmNoUGFyYW1zO1xuXG4gICAgaWYgKHByZWZpeCkge1xuICAgICAgcGFyYW1zLnNldChwcmVmaXgrJ1tvcmRlckJ5XScsIGNvbE5hbWUpO1xuICAgICAgcGFyYW1zLnNldChwcmVmaXgrJ1tzb3J0T3JkZXJdJywgZGlyZWN0aW9uKTtcbiAgICB9IGVsc2Uge1xuICAgICAgcGFyYW1zLnNldCgnb3JkZXJCeScsIGNvbE5hbWUpO1xuICAgICAgcGFyYW1zLnNldCgnc29ydE9yZGVyJywgZGlyZWN0aW9uKTtcbiAgICB9XG5cbiAgICByZXR1cm4gdXJsLnRvU3RyaW5nKCk7XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgVGFibGVTb3J0aW5nO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvYXBwL3V0aWxzL3RhYmxlLXNvcnRpbmcuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG4vKipcbiAqIFNlbmQgYSBQb3N0IFJlcXVlc3QgdG8gcmVzZXQgc2VhcmNoIEFjdGlvbi5cbiAqL1xuXG5jb25zdCAkID0gZ2xvYmFsLiQ7XG5cbmNvbnN0IGluaXQgPSBmdW5jdGlvbiByZXNldFNlYXJjaCh1cmwsIHJlZGlyZWN0VXJsKSB7XG4gICAgJC5wb3N0KHVybCkudGhlbigoKSA9PiB3aW5kb3cubG9jYXRpb24uYXNzaWduKHJlZGlyZWN0VXJsKSk7XG59O1xuXG5leHBvcnQgZGVmYXVsdCBpbml0O1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvYXBwL3V0aWxzL3Jlc2V0X3NlYXJjaC5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCB7RXZlbnRFbWl0dGVyfSBmcm9tICcuL2V2ZW50LWVtaXR0ZXInO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogVGhpcyBjbGFzcyBpcyB1c2VkIHRvIGF1dG9tYXRpY2FsbHkgdG9nZ2xlIHRyYW5zbGF0ZWQgaW5wdXRzIChkaXNwbGF5ZWQgd2l0aCBvbmVcbiAqIGlucHV0IGFuZCBhIGxhbmd1YWdlIHNlbGVjdG9yIHVzaW5nIHRoZSBUcmFuc2xhdGFibGVUeXBlIFN5bWZvbnkgZm9ybSB0eXBlKS5cbiAqIEFsc28gY29tcGF0aWJsZSB3aXRoIFRyYW5zbGF0YWJsZUZpZWxkIGNoYW5nZXMuXG4gKi9cbmNsYXNzIFRyYW5zbGF0YWJsZUlucHV0IHtcbiAgY29uc3RydWN0b3Iob3B0aW9ucykge1xuICAgIG9wdGlvbnMgPSBvcHRpb25zIHx8IHt9O1xuXG4gICAgdGhpcy5sb2NhbGVJdGVtU2VsZWN0b3IgPSBvcHRpb25zLmxvY2FsZUl0ZW1TZWxlY3RvciB8fCAnLmpzLWxvY2FsZS1pdGVtJztcbiAgICB0aGlzLmxvY2FsZUJ1dHRvblNlbGVjdG9yID0gb3B0aW9ucy5sb2NhbGVCdXR0b25TZWxlY3RvciB8fCAnLmpzLWxvY2FsZS1idG4nO1xuICAgIHRoaXMubG9jYWxlSW5wdXRTZWxlY3RvciA9IG9wdGlvbnMubG9jYWxlSW5wdXRTZWxlY3RvciB8fCAnLmpzLWxvY2FsZS1pbnB1dCc7XG5cbiAgICAkKCdib2R5Jykub24oJ2NsaWNrJywgdGhpcy5sb2NhbGVJdGVtU2VsZWN0b3IsIHRoaXMudG9nZ2xlTGFuZ3VhZ2UuYmluZCh0aGlzKSk7XG4gICAgRXZlbnRFbWl0dGVyLm9uKCdsYW5ndWFnZVNlbGVjdGVkJywgdGhpcy50b2dnbGVJbnB1dHMuYmluZCh0aGlzKSk7XG4gIH1cblxuICAvKipcbiAgICogRGlzcGF0Y2ggZXZlbnQgb24gbGFuZ3VhZ2Ugc2VsZWN0aW9uIHRvIHVwZGF0ZSBpbnB1dHMgYW5kIG90aGVyIGNvbXBvbmVudHMgd2hpY2ggZGVwZW5kIG9uIHRoZSBsb2NhbGUuXG4gICAqXG4gICAqIEBwYXJhbSBldmVudFxuICAgKi9cbiAgdG9nZ2xlTGFuZ3VhZ2UoZXZlbnQpIHtcbiAgICBjb25zdCBsb2NhbGVJdGVtID0gJChldmVudC50YXJnZXQpO1xuICAgIGNvbnN0IGZvcm0gPSBsb2NhbGVJdGVtLmNsb3Nlc3QoJ2Zvcm0nKTtcbiAgICBFdmVudEVtaXR0ZXIuZW1pdCgnbGFuZ3VhZ2VTZWxlY3RlZCcsIHtzZWxlY3RlZExvY2FsZTogbG9jYWxlSXRlbS5kYXRhKCdsb2NhbGUnKSwgZm9ybTogZm9ybX0pO1xuICB9XG5cbiAgLyoqXG4gICAqIFRvZ2dsZSBhbGwgdHJhbnNsYXRhYmxlIGlucHV0cyBpbiBmb3JtIGluIHdoaWNoIGxvY2FsZSB3YXMgY2hhbmdlZFxuICAgKlxuICAgKiBAcGFyYW0ge0V2ZW50fSBldmVudFxuICAgKi9cbiAgdG9nZ2xlSW5wdXRzKGV2ZW50KSB7XG4gICAgY29uc3QgZm9ybSA9IGV2ZW50LmZvcm07XG4gICAgY29uc3Qgc2VsZWN0ZWRMb2NhbGUgPSBldmVudC5zZWxlY3RlZExvY2FsZTtcbiAgICBjb25zdCBsb2NhbGVCdXR0b24gPSBmb3JtLmZpbmQodGhpcy5sb2NhbGVCdXR0b25TZWxlY3Rvcik7XG4gICAgY29uc3QgY2hhbmdlTGFuZ3VhZ2VVcmwgPSBsb2NhbGVCdXR0b24uZGF0YSgnY2hhbmdlLWxhbmd1YWdlLXVybCcpO1xuXG4gICAgbG9jYWxlQnV0dG9uLnRleHQoc2VsZWN0ZWRMb2NhbGUpO1xuICAgIGZvcm0uZmluZCh0aGlzLmxvY2FsZUlucHV0U2VsZWN0b3IpLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgICBmb3JtLmZpbmQoYCR7dGhpcy5sb2NhbGVJbnB1dFNlbGVjdG9yfS5qcy1sb2NhbGUtJHtzZWxlY3RlZExvY2FsZX1gKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG5cbiAgICBpZiAoY2hhbmdlTGFuZ3VhZ2VVcmwpIHtcbiAgICAgIHRoaXMuX3NhdmVTZWxlY3RlZExhbmd1YWdlKGNoYW5nZUxhbmd1YWdlVXJsLCBzZWxlY3RlZExvY2FsZSk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIFNhdmUgbGFuZ3VhZ2UgY2hvaWNlIGZvciBlbXBsb3llZSBmb3Jtcy5cbiAgICpcbiAgICogQHBhcmFtIHtTdHJpbmd9IGNoYW5nZUxhbmd1YWdlVXJsXG4gICAqIEBwYXJhbSB7U3RyaW5nfSBzZWxlY3RlZExvY2FsZVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3NhdmVTZWxlY3RlZExhbmd1YWdlKGNoYW5nZUxhbmd1YWdlVXJsLCBzZWxlY3RlZExvY2FsZSkge1xuICAgICQucG9zdCh7XG4gICAgICB1cmw6IGNoYW5nZUxhbmd1YWdlVXJsLFxuICAgICAgZGF0YToge1xuICAgICAgICBsYW5ndWFnZV9pc29fY29kZTogc2VsZWN0ZWRMb2NhbGVcbiAgICAgIH0sXG4gICAgfSk7XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgVHJhbnNsYXRhYmxlSW5wdXQ7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL3RyYW5zbGF0YWJsZS1pbnB1dC5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSBnbG9iYWwuJDtcblxuLyoqXG4gKiBDbGFzcyBSZWxvYWRMaXN0RXh0ZW5zaW9uIGV4dGVuZHMgZ3JpZCB3aXRoIFwiQ29sdW1uIHRvZ2dsaW5nXCIgZmVhdHVyZVxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBDb2x1bW5Ub2dnbGluZ0V4dGVuc2lvbiB7XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBjb25zdCAkdGFibGUgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJ3RhYmxlLnRhYmxlJyk7XG4gICAgJHRhYmxlLmZpbmQoJy5wcy10b2dnbGFibGUtcm93Jykub24oJ2NsaWNrJywgKGUpID0+IHtcbiAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgIHRoaXMuX3RvZ2dsZVZhbHVlKCQoZS5kZWxlZ2F0ZVRhcmdldCkpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSByb3dcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF90b2dnbGVWYWx1ZShyb3cpIHtcbiAgICBjb25zdCB0b2dnbGVVcmwgPSByb3cuZGF0YSgndG9nZ2xlVXJsJyk7XG5cbiAgICB0aGlzLl9zdWJtaXRBc0Zvcm0odG9nZ2xlVXJsKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTdWJtaXRzIHJlcXVlc3QgdXJsIGFzIGZvcm1cbiAgICpcbiAgICogQHBhcmFtIHtzdHJpbmd9IHRvZ2dsZVVybFxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3N1Ym1pdEFzRm9ybSh0b2dnbGVVcmwpIHtcbiAgICBjb25zdCAkZm9ybSA9ICQoJzxmb3JtPicsIHtcbiAgICAgIGFjdGlvbjogdG9nZ2xlVXJsLFxuICAgICAgbWV0aG9kOiAnUE9TVCcsXG4gICAgfSkuYXBwZW5kVG8oJ2JvZHknKTtcblxuICAgICRmb3JtLnN1Ym1pdCgpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2NvbHVtbi10b2dnbGluZy1leHRlbnNpb24uanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgRXZlbnRFbWl0dGVyQ2xhc3MgZnJvbSAnZXZlbnRzJztcblxuLyoqXG4gKiBXZSBpbnN0YW5jaWF0ZSBvbmUgRXZlbnRFbWl0dGVyIChyZXN0cmljdGVkIHZpYSBhIGNvbnN0KSBzbyB0aGF0IGV2ZXJ5IGNvbXBvbmVudHNcbiAqIHJlZ2lzdGVyL2Rpc3BhdGNoIG9uIHRoZSBzYW1lIG9uZSBhbmQgY2FuIGNvbW11bmljYXRlIHdpdGggZWFjaCBvdGhlci5cbiAqL1xuZXhwb3J0IGNvbnN0IEV2ZW50RW1pdHRlciA9IG5ldyBFdmVudEVtaXR0ZXJDbGFzcygpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ldmVudC1lbWl0dGVyLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIEhhbmRsZXMgVUkgaW50ZXJhY3Rpb25zIG9mIGNob2ljZSB0cmVlXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIENob2ljZVRyZWUge1xuICAvKipcbiAgICogQHBhcmFtIHtTdHJpbmd9IHRyZWVTZWxlY3RvclxuICAgKi9cbiAgY29uc3RydWN0b3IodHJlZVNlbGVjdG9yKSB7XG4gICAgdGhpcy4kY29udGFpbmVyID0gJCh0cmVlU2VsZWN0b3IpO1xuXG4gICAgdGhpcy4kY29udGFpbmVyLm9uKCdjbGljaycsICcuanMtaW5wdXQtd3JhcHBlcicsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgJGlucHV0V3JhcHBlciA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG5cbiAgICAgIHRoaXMuX3RvZ2dsZUNoaWxkVHJlZSgkaW5wdXRXcmFwcGVyKTtcbiAgICB9KTtcblxuICAgIHRoaXMuJGNvbnRhaW5lci5vbignY2xpY2snLCAnLmpzLXRvZ2dsZS1jaG9pY2UtdHJlZS1hY3Rpb24nLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0ICRhY3Rpb24gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuXG4gICAgICB0aGlzLl90b2dnbGVUcmVlKCRhY3Rpb24pO1xuICAgIH0pO1xuXG4gICAgcmV0dXJuIHtcbiAgICAgIGVuYWJsZUF1dG9DaGVja0NoaWxkcmVuOiAoKSA9PiB0aGlzLmVuYWJsZUF1dG9DaGVja0NoaWxkcmVuKCksXG4gICAgICBlbmFibGVBbGxJbnB1dHM6ICgpID0+IHRoaXMuZW5hYmxlQWxsSW5wdXRzKCksXG4gICAgICBkaXNhYmxlQWxsSW5wdXRzOiAoKSA9PiB0aGlzLmRpc2FibGVBbGxJbnB1dHMoKSxcbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqIEVuYWJsZSBhdXRvbWF0aWMgY2hlY2svdW5jaGVjayBvZiBjbGlja2VkIGl0ZW0ncyBjaGlsZHJlbi5cbiAgICovXG4gIGVuYWJsZUF1dG9DaGVja0NoaWxkcmVuKCkge1xuICAgIHRoaXMuJGNvbnRhaW5lci5vbignY2hhbmdlJywgJ2lucHV0W3R5cGU9XCJjaGVja2JveFwiXScsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgJGNsaWNrZWRDaGVja2JveCA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgICBjb25zdCAkaXRlbVdpdGhDaGlsZHJlbiA9ICRjbGlja2VkQ2hlY2tib3guY2xvc2VzdCgnbGknKTtcblxuICAgICAgJGl0ZW1XaXRoQ2hpbGRyZW5cbiAgICAgICAgLmZpbmQoJ3VsIGlucHV0W3R5cGU9XCJjaGVja2JveFwiXScpXG4gICAgICAgIC5wcm9wKCdjaGVja2VkJywgJGNsaWNrZWRDaGVja2JveC5pcygnOmNoZWNrZWQnKSk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogRW5hYmxlIGFsbCBpbnB1dHMgaW4gdGhlIGNob2ljZSB0cmVlLlxuICAgKi9cbiAgZW5hYmxlQWxsSW5wdXRzKCkge1xuICAgIHRoaXMuJGNvbnRhaW5lci5maW5kKCdpbnB1dCcpLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XG4gIH1cblxuICAvKipcbiAgICogRGlzYWJsZSBhbGwgaW5wdXRzIGluIHRoZSBjaG9pY2UgdHJlZS5cbiAgICovXG4gIGRpc2FibGVBbGxJbnB1dHMoKSB7XG4gICAgdGhpcy4kY29udGFpbmVyLmZpbmQoJ2lucHV0JykuYXR0cignZGlzYWJsZWQnLCAnZGlzYWJsZWQnKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBDb2xsYXBzZSBvciBleHBhbmQgc3ViLXRyZWUgZm9yIHNpbmdsZSBwYXJlbnRcbiAgICpcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRpbnB1dFdyYXBwZXJcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF90b2dnbGVDaGlsZFRyZWUoJGlucHV0V3JhcHBlcikge1xuICAgIGNvbnN0ICRwYXJlbnRXcmFwcGVyID0gJGlucHV0V3JhcHBlci5jbG9zZXN0KCdsaScpO1xuXG4gICAgaWYgKCRwYXJlbnRXcmFwcGVyLmhhc0NsYXNzKCdleHBhbmRlZCcpKSB7XG4gICAgICAkcGFyZW50V3JhcHBlclxuICAgICAgICAucmVtb3ZlQ2xhc3MoJ2V4cGFuZGVkJylcbiAgICAgICAgLmFkZENsYXNzKCdjb2xsYXBzZWQnKTtcblxuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIGlmICgkcGFyZW50V3JhcHBlci5oYXNDbGFzcygnY29sbGFwc2VkJykpIHtcbiAgICAgICRwYXJlbnRXcmFwcGVyXG4gICAgICAgIC5yZW1vdmVDbGFzcygnY29sbGFwc2VkJylcbiAgICAgICAgLmFkZENsYXNzKCdleHBhbmRlZCcpO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBDb2xsYXBzZSBvciBleHBhbmQgd2hvbGUgdHJlZVxuICAgKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gJGFjdGlvblxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3RvZ2dsZVRyZWUoJGFjdGlvbikge1xuICAgIGNvbnN0ICRwYXJlbnRDb250YWluZXIgPSAkYWN0aW9uLmNsb3Nlc3QoJy5qcy1jaG9pY2UtdHJlZS1jb250YWluZXInKTtcbiAgICBjb25zdCBhY3Rpb24gPSAkYWN0aW9uLmRhdGEoJ2FjdGlvbicpO1xuXG4gICAgLy8gdG9nZ2xlIGFjdGlvbiBjb25maWd1cmF0aW9uXG4gICAgY29uc3QgY29uZmlnID0ge1xuICAgICAgYWRkQ2xhc3M6IHtcbiAgICAgICAgZXhwYW5kOiAnZXhwYW5kZWQnLFxuICAgICAgICBjb2xsYXBzZTogJ2NvbGxhcHNlZCcsXG4gICAgICB9LFxuICAgICAgcmVtb3ZlQ2xhc3M6IHtcbiAgICAgICAgZXhwYW5kOiAnY29sbGFwc2VkJyxcbiAgICAgICAgY29sbGFwc2U6ICdleHBhbmRlZCcsXG4gICAgICB9LFxuICAgICAgbmV4dEFjdGlvbjoge1xuICAgICAgICBleHBhbmQ6ICdjb2xsYXBzZScsXG4gICAgICAgIGNvbGxhcHNlOiAnZXhwYW5kJyxcbiAgICAgIH0sXG4gICAgICB0ZXh0OiB7XG4gICAgICAgIGV4cGFuZDogJ2NvbGxhcHNlZC10ZXh0JyxcbiAgICAgICAgY29sbGFwc2U6ICdleHBhbmRlZC10ZXh0JyxcbiAgICAgIH0sXG4gICAgICBpY29uOiB7XG4gICAgICAgIGV4cGFuZDogJ2NvbGxhcHNlZC1pY29uJyxcbiAgICAgICAgY29sbGFwc2U6ICdleHBhbmRlZC1pY29uJyxcbiAgICAgIH1cbiAgICB9O1xuXG4gICAgJHBhcmVudENvbnRhaW5lci5maW5kKCdsaScpLmVhY2goKGluZGV4LCBpdGVtKSA9PiB7XG4gICAgICBjb25zdCAkaXRlbSA9ICQoaXRlbSk7XG5cbiAgICAgIGlmICgkaXRlbS5oYXNDbGFzcyhjb25maWcucmVtb3ZlQ2xhc3NbYWN0aW9uXSkpIHtcbiAgICAgICAgICAkaXRlbS5yZW1vdmVDbGFzcyhjb25maWcucmVtb3ZlQ2xhc3NbYWN0aW9uXSlcbiAgICAgICAgICAgIC5hZGRDbGFzcyhjb25maWcuYWRkQ2xhc3NbYWN0aW9uXSk7XG4gICAgICB9XG4gICAgfSk7XG5cbiAgICAkYWN0aW9uLmRhdGEoJ2FjdGlvbicsIGNvbmZpZy5uZXh0QWN0aW9uW2FjdGlvbl0pO1xuICAgICRhY3Rpb24uZmluZCgnLm1hdGVyaWFsLWljb25zJykudGV4dCgkYWN0aW9uLmRhdGEoY29uZmlnLmljb25bYWN0aW9uXSkpO1xuICAgICRhY3Rpb24uZmluZCgnLmpzLXRvZ2dsZS10ZXh0JykudGV4dCgkYWN0aW9uLmRhdGEoY29uZmlnLnRleHRbYWN0aW9uXSkpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2Zvcm0vY2hvaWNlLXRyZWUuanMiLCIvLyBDb3B5cmlnaHQgSm95ZW50LCBJbmMuIGFuZCBvdGhlciBOb2RlIGNvbnRyaWJ1dG9ycy5cbi8vXG4vLyBQZXJtaXNzaW9uIGlzIGhlcmVieSBncmFudGVkLCBmcmVlIG9mIGNoYXJnZSwgdG8gYW55IHBlcnNvbiBvYnRhaW5pbmcgYVxuLy8gY29weSBvZiB0aGlzIHNvZnR3YXJlIGFuZCBhc3NvY2lhdGVkIGRvY3VtZW50YXRpb24gZmlsZXMgKHRoZVxuLy8gXCJTb2Z0d2FyZVwiKSwgdG8gZGVhbCBpbiB0aGUgU29mdHdhcmUgd2l0aG91dCByZXN0cmljdGlvbiwgaW5jbHVkaW5nXG4vLyB3aXRob3V0IGxpbWl0YXRpb24gdGhlIHJpZ2h0cyB0byB1c2UsIGNvcHksIG1vZGlmeSwgbWVyZ2UsIHB1Ymxpc2gsXG4vLyBkaXN0cmlidXRlLCBzdWJsaWNlbnNlLCBhbmQvb3Igc2VsbCBjb3BpZXMgb2YgdGhlIFNvZnR3YXJlLCBhbmQgdG8gcGVybWl0XG4vLyBwZXJzb25zIHRvIHdob20gdGhlIFNvZnR3YXJlIGlzIGZ1cm5pc2hlZCB0byBkbyBzbywgc3ViamVjdCB0byB0aGVcbi8vIGZvbGxvd2luZyBjb25kaXRpb25zOlxuLy9cbi8vIFRoZSBhYm92ZSBjb3B5cmlnaHQgbm90aWNlIGFuZCB0aGlzIHBlcm1pc3Npb24gbm90aWNlIHNoYWxsIGJlIGluY2x1ZGVkXG4vLyBpbiBhbGwgY29waWVzIG9yIHN1YnN0YW50aWFsIHBvcnRpb25zIG9mIHRoZSBTb2Z0d2FyZS5cbi8vXG4vLyBUSEUgU09GVFdBUkUgSVMgUFJPVklERUQgXCJBUyBJU1wiLCBXSVRIT1VUIFdBUlJBTlRZIE9GIEFOWSBLSU5ELCBFWFBSRVNTXG4vLyBPUiBJTVBMSUVELCBJTkNMVURJTkcgQlVUIE5PVCBMSU1JVEVEIFRPIFRIRSBXQVJSQU5USUVTIE9GXG4vLyBNRVJDSEFOVEFCSUxJVFksIEZJVE5FU1MgRk9SIEEgUEFSVElDVUxBUiBQVVJQT1NFIEFORCBOT05JTkZSSU5HRU1FTlQuIElOXG4vLyBOTyBFVkVOVCBTSEFMTCBUSEUgQVVUSE9SUyBPUiBDT1BZUklHSFQgSE9MREVSUyBCRSBMSUFCTEUgRk9SIEFOWSBDTEFJTSxcbi8vIERBTUFHRVMgT1IgT1RIRVIgTElBQklMSVRZLCBXSEVUSEVSIElOIEFOIEFDVElPTiBPRiBDT05UUkFDVCwgVE9SVCBPUlxuLy8gT1RIRVJXSVNFLCBBUklTSU5HIEZST00sIE9VVCBPRiBPUiBJTiBDT05ORUNUSU9OIFdJVEggVEhFIFNPRlRXQVJFIE9SIFRIRVxuLy8gVVNFIE9SIE9USEVSIERFQUxJTkdTIElOIFRIRSBTT0ZUV0FSRS5cblxuJ3VzZSBzdHJpY3QnO1xuXG52YXIgUiA9IHR5cGVvZiBSZWZsZWN0ID09PSAnb2JqZWN0JyA/IFJlZmxlY3QgOiBudWxsXG52YXIgUmVmbGVjdEFwcGx5ID0gUiAmJiB0eXBlb2YgUi5hcHBseSA9PT0gJ2Z1bmN0aW9uJ1xuICA/IFIuYXBwbHlcbiAgOiBmdW5jdGlvbiBSZWZsZWN0QXBwbHkodGFyZ2V0LCByZWNlaXZlciwgYXJncykge1xuICAgIHJldHVybiBGdW5jdGlvbi5wcm90b3R5cGUuYXBwbHkuY2FsbCh0YXJnZXQsIHJlY2VpdmVyLCBhcmdzKTtcbiAgfVxuXG52YXIgUmVmbGVjdE93bktleXNcbmlmIChSICYmIHR5cGVvZiBSLm93bktleXMgPT09ICdmdW5jdGlvbicpIHtcbiAgUmVmbGVjdE93bktleXMgPSBSLm93bktleXNcbn0gZWxzZSBpZiAoT2JqZWN0LmdldE93blByb3BlcnR5U3ltYm9scykge1xuICBSZWZsZWN0T3duS2V5cyA9IGZ1bmN0aW9uIFJlZmxlY3RPd25LZXlzKHRhcmdldCkge1xuICAgIHJldHVybiBPYmplY3QuZ2V0T3duUHJvcGVydHlOYW1lcyh0YXJnZXQpXG4gICAgICAuY29uY2F0KE9iamVjdC5nZXRPd25Qcm9wZXJ0eVN5bWJvbHModGFyZ2V0KSk7XG4gIH07XG59IGVsc2Uge1xuICBSZWZsZWN0T3duS2V5cyA9IGZ1bmN0aW9uIFJlZmxlY3RPd25LZXlzKHRhcmdldCkge1xuICAgIHJldHVybiBPYmplY3QuZ2V0T3duUHJvcGVydHlOYW1lcyh0YXJnZXQpO1xuICB9O1xufVxuXG5mdW5jdGlvbiBQcm9jZXNzRW1pdFdhcm5pbmcod2FybmluZykge1xuICBpZiAoY29uc29sZSAmJiBjb25zb2xlLndhcm4pIGNvbnNvbGUud2Fybih3YXJuaW5nKTtcbn1cblxudmFyIE51bWJlcklzTmFOID0gTnVtYmVyLmlzTmFOIHx8IGZ1bmN0aW9uIE51bWJlcklzTmFOKHZhbHVlKSB7XG4gIHJldHVybiB2YWx1ZSAhPT0gdmFsdWU7XG59XG5cbmZ1bmN0aW9uIEV2ZW50RW1pdHRlcigpIHtcbiAgRXZlbnRFbWl0dGVyLmluaXQuY2FsbCh0aGlzKTtcbn1cbm1vZHVsZS5leHBvcnRzID0gRXZlbnRFbWl0dGVyO1xuXG4vLyBCYWNrd2FyZHMtY29tcGF0IHdpdGggbm9kZSAwLjEwLnhcbkV2ZW50RW1pdHRlci5FdmVudEVtaXR0ZXIgPSBFdmVudEVtaXR0ZXI7XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuX2V2ZW50cyA9IHVuZGVmaW5lZDtcbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuX2V2ZW50c0NvdW50ID0gMDtcbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuX21heExpc3RlbmVycyA9IHVuZGVmaW5lZDtcblxuLy8gQnkgZGVmYXVsdCBFdmVudEVtaXR0ZXJzIHdpbGwgcHJpbnQgYSB3YXJuaW5nIGlmIG1vcmUgdGhhbiAxMCBsaXN0ZW5lcnMgYXJlXG4vLyBhZGRlZCB0byBpdC4gVGhpcyBpcyBhIHVzZWZ1bCBkZWZhdWx0IHdoaWNoIGhlbHBzIGZpbmRpbmcgbWVtb3J5IGxlYWtzLlxudmFyIGRlZmF1bHRNYXhMaXN0ZW5lcnMgPSAxMDtcblxuT2JqZWN0LmRlZmluZVByb3BlcnR5KEV2ZW50RW1pdHRlciwgJ2RlZmF1bHRNYXhMaXN0ZW5lcnMnLCB7XG4gIGVudW1lcmFibGU6IHRydWUsXG4gIGdldDogZnVuY3Rpb24oKSB7XG4gICAgcmV0dXJuIGRlZmF1bHRNYXhMaXN0ZW5lcnM7XG4gIH0sXG4gIHNldDogZnVuY3Rpb24oYXJnKSB7XG4gICAgaWYgKHR5cGVvZiBhcmcgIT09ICdudW1iZXInIHx8IGFyZyA8IDAgfHwgTnVtYmVySXNOYU4oYXJnKSkge1xuICAgICAgdGhyb3cgbmV3IFJhbmdlRXJyb3IoJ1RoZSB2YWx1ZSBvZiBcImRlZmF1bHRNYXhMaXN0ZW5lcnNcIiBpcyBvdXQgb2YgcmFuZ2UuIEl0IG11c3QgYmUgYSBub24tbmVnYXRpdmUgbnVtYmVyLiBSZWNlaXZlZCAnICsgYXJnICsgJy4nKTtcbiAgICB9XG4gICAgZGVmYXVsdE1heExpc3RlbmVycyA9IGFyZztcbiAgfVxufSk7XG5cbkV2ZW50RW1pdHRlci5pbml0ID0gZnVuY3Rpb24oKSB7XG5cbiAgaWYgKHRoaXMuX2V2ZW50cyA9PT0gdW5kZWZpbmVkIHx8XG4gICAgICB0aGlzLl9ldmVudHMgPT09IE9iamVjdC5nZXRQcm90b3R5cGVPZih0aGlzKS5fZXZlbnRzKSB7XG4gICAgdGhpcy5fZXZlbnRzID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiAgICB0aGlzLl9ldmVudHNDb3VudCA9IDA7XG4gIH1cblxuICB0aGlzLl9tYXhMaXN0ZW5lcnMgPSB0aGlzLl9tYXhMaXN0ZW5lcnMgfHwgdW5kZWZpbmVkO1xufTtcblxuLy8gT2J2aW91c2x5IG5vdCBhbGwgRW1pdHRlcnMgc2hvdWxkIGJlIGxpbWl0ZWQgdG8gMTAuIFRoaXMgZnVuY3Rpb24gYWxsb3dzXG4vLyB0aGF0IHRvIGJlIGluY3JlYXNlZC4gU2V0IHRvIHplcm8gZm9yIHVubGltaXRlZC5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuc2V0TWF4TGlzdGVuZXJzID0gZnVuY3Rpb24gc2V0TWF4TGlzdGVuZXJzKG4pIHtcbiAgaWYgKHR5cGVvZiBuICE9PSAnbnVtYmVyJyB8fCBuIDwgMCB8fCBOdW1iZXJJc05hTihuKSkge1xuICAgIHRocm93IG5ldyBSYW5nZUVycm9yKCdUaGUgdmFsdWUgb2YgXCJuXCIgaXMgb3V0IG9mIHJhbmdlLiBJdCBtdXN0IGJlIGEgbm9uLW5lZ2F0aXZlIG51bWJlci4gUmVjZWl2ZWQgJyArIG4gKyAnLicpO1xuICB9XG4gIHRoaXMuX21heExpc3RlbmVycyA9IG47XG4gIHJldHVybiB0aGlzO1xufTtcblxuZnVuY3Rpb24gJGdldE1heExpc3RlbmVycyh0aGF0KSB7XG4gIGlmICh0aGF0Ll9tYXhMaXN0ZW5lcnMgPT09IHVuZGVmaW5lZClcbiAgICByZXR1cm4gRXZlbnRFbWl0dGVyLmRlZmF1bHRNYXhMaXN0ZW5lcnM7XG4gIHJldHVybiB0aGF0Ll9tYXhMaXN0ZW5lcnM7XG59XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuZ2V0TWF4TGlzdGVuZXJzID0gZnVuY3Rpb24gZ2V0TWF4TGlzdGVuZXJzKCkge1xuICByZXR1cm4gJGdldE1heExpc3RlbmVycyh0aGlzKTtcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuZW1pdCA9IGZ1bmN0aW9uIGVtaXQodHlwZSkge1xuICB2YXIgYXJncyA9IFtdO1xuICBmb3IgKHZhciBpID0gMTsgaSA8IGFyZ3VtZW50cy5sZW5ndGg7IGkrKykgYXJncy5wdXNoKGFyZ3VtZW50c1tpXSk7XG4gIHZhciBkb0Vycm9yID0gKHR5cGUgPT09ICdlcnJvcicpO1xuXG4gIHZhciBldmVudHMgPSB0aGlzLl9ldmVudHM7XG4gIGlmIChldmVudHMgIT09IHVuZGVmaW5lZClcbiAgICBkb0Vycm9yID0gKGRvRXJyb3IgJiYgZXZlbnRzLmVycm9yID09PSB1bmRlZmluZWQpO1xuICBlbHNlIGlmICghZG9FcnJvcilcbiAgICByZXR1cm4gZmFsc2U7XG5cbiAgLy8gSWYgdGhlcmUgaXMgbm8gJ2Vycm9yJyBldmVudCBsaXN0ZW5lciB0aGVuIHRocm93LlxuICBpZiAoZG9FcnJvcikge1xuICAgIHZhciBlcjtcbiAgICBpZiAoYXJncy5sZW5ndGggPiAwKVxuICAgICAgZXIgPSBhcmdzWzBdO1xuICAgIGlmIChlciBpbnN0YW5jZW9mIEVycm9yKSB7XG4gICAgICAvLyBOb3RlOiBUaGUgY29tbWVudHMgb24gdGhlIGB0aHJvd2AgbGluZXMgYXJlIGludGVudGlvbmFsLCB0aGV5IHNob3dcbiAgICAgIC8vIHVwIGluIE5vZGUncyBvdXRwdXQgaWYgdGhpcyByZXN1bHRzIGluIGFuIHVuaGFuZGxlZCBleGNlcHRpb24uXG4gICAgICB0aHJvdyBlcjsgLy8gVW5oYW5kbGVkICdlcnJvcicgZXZlbnRcbiAgICB9XG4gICAgLy8gQXQgbGVhc3QgZ2l2ZSBzb21lIGtpbmQgb2YgY29udGV4dCB0byB0aGUgdXNlclxuICAgIHZhciBlcnIgPSBuZXcgRXJyb3IoJ1VuaGFuZGxlZCBlcnJvci4nICsgKGVyID8gJyAoJyArIGVyLm1lc3NhZ2UgKyAnKScgOiAnJykpO1xuICAgIGVyci5jb250ZXh0ID0gZXI7XG4gICAgdGhyb3cgZXJyOyAvLyBVbmhhbmRsZWQgJ2Vycm9yJyBldmVudFxuICB9XG5cbiAgdmFyIGhhbmRsZXIgPSBldmVudHNbdHlwZV07XG5cbiAgaWYgKGhhbmRsZXIgPT09IHVuZGVmaW5lZClcbiAgICByZXR1cm4gZmFsc2U7XG5cbiAgaWYgKHR5cGVvZiBoYW5kbGVyID09PSAnZnVuY3Rpb24nKSB7XG4gICAgUmVmbGVjdEFwcGx5KGhhbmRsZXIsIHRoaXMsIGFyZ3MpO1xuICB9IGVsc2Uge1xuICAgIHZhciBsZW4gPSBoYW5kbGVyLmxlbmd0aDtcbiAgICB2YXIgbGlzdGVuZXJzID0gYXJyYXlDbG9uZShoYW5kbGVyLCBsZW4pO1xuICAgIGZvciAodmFyIGkgPSAwOyBpIDwgbGVuOyArK2kpXG4gICAgICBSZWZsZWN0QXBwbHkobGlzdGVuZXJzW2ldLCB0aGlzLCBhcmdzKTtcbiAgfVxuXG4gIHJldHVybiB0cnVlO1xufTtcblxuZnVuY3Rpb24gX2FkZExpc3RlbmVyKHRhcmdldCwgdHlwZSwgbGlzdGVuZXIsIHByZXBlbmQpIHtcbiAgdmFyIG07XG4gIHZhciBldmVudHM7XG4gIHZhciBleGlzdGluZztcblxuICBpZiAodHlwZW9mIGxpc3RlbmVyICE9PSAnZnVuY3Rpb24nKSB7XG4gICAgdGhyb3cgbmV3IFR5cGVFcnJvcignVGhlIFwibGlzdGVuZXJcIiBhcmd1bWVudCBtdXN0IGJlIG9mIHR5cGUgRnVuY3Rpb24uIFJlY2VpdmVkIHR5cGUgJyArIHR5cGVvZiBsaXN0ZW5lcik7XG4gIH1cblxuICBldmVudHMgPSB0YXJnZXQuX2V2ZW50cztcbiAgaWYgKGV2ZW50cyA9PT0gdW5kZWZpbmVkKSB7XG4gICAgZXZlbnRzID0gdGFyZ2V0Ll9ldmVudHMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuICAgIHRhcmdldC5fZXZlbnRzQ291bnQgPSAwO1xuICB9IGVsc2Uge1xuICAgIC8vIFRvIGF2b2lkIHJlY3Vyc2lvbiBpbiB0aGUgY2FzZSB0aGF0IHR5cGUgPT09IFwibmV3TGlzdGVuZXJcIiEgQmVmb3JlXG4gICAgLy8gYWRkaW5nIGl0IHRvIHRoZSBsaXN0ZW5lcnMsIGZpcnN0IGVtaXQgXCJuZXdMaXN0ZW5lclwiLlxuICAgIGlmIChldmVudHMubmV3TGlzdGVuZXIgIT09IHVuZGVmaW5lZCkge1xuICAgICAgdGFyZ2V0LmVtaXQoJ25ld0xpc3RlbmVyJywgdHlwZSxcbiAgICAgICAgICAgICAgICAgIGxpc3RlbmVyLmxpc3RlbmVyID8gbGlzdGVuZXIubGlzdGVuZXIgOiBsaXN0ZW5lcik7XG5cbiAgICAgIC8vIFJlLWFzc2lnbiBgZXZlbnRzYCBiZWNhdXNlIGEgbmV3TGlzdGVuZXIgaGFuZGxlciBjb3VsZCBoYXZlIGNhdXNlZCB0aGVcbiAgICAgIC8vIHRoaXMuX2V2ZW50cyB0byBiZSBhc3NpZ25lZCB0byBhIG5ldyBvYmplY3RcbiAgICAgIGV2ZW50cyA9IHRhcmdldC5fZXZlbnRzO1xuICAgIH1cbiAgICBleGlzdGluZyA9IGV2ZW50c1t0eXBlXTtcbiAgfVxuXG4gIGlmIChleGlzdGluZyA9PT0gdW5kZWZpbmVkKSB7XG4gICAgLy8gT3B0aW1pemUgdGhlIGNhc2Ugb2Ygb25lIGxpc3RlbmVyLiBEb24ndCBuZWVkIHRoZSBleHRyYSBhcnJheSBvYmplY3QuXG4gICAgZXhpc3RpbmcgPSBldmVudHNbdHlwZV0gPSBsaXN0ZW5lcjtcbiAgICArK3RhcmdldC5fZXZlbnRzQ291bnQ7XG4gIH0gZWxzZSB7XG4gICAgaWYgKHR5cGVvZiBleGlzdGluZyA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgLy8gQWRkaW5nIHRoZSBzZWNvbmQgZWxlbWVudCwgbmVlZCB0byBjaGFuZ2UgdG8gYXJyYXkuXG4gICAgICBleGlzdGluZyA9IGV2ZW50c1t0eXBlXSA9XG4gICAgICAgIHByZXBlbmQgPyBbbGlzdGVuZXIsIGV4aXN0aW5nXSA6IFtleGlzdGluZywgbGlzdGVuZXJdO1xuICAgICAgLy8gSWYgd2UndmUgYWxyZWFkeSBnb3QgYW4gYXJyYXksIGp1c3QgYXBwZW5kLlxuICAgIH0gZWxzZSBpZiAocHJlcGVuZCkge1xuICAgICAgZXhpc3RpbmcudW5zaGlmdChsaXN0ZW5lcik7XG4gICAgfSBlbHNlIHtcbiAgICAgIGV4aXN0aW5nLnB1c2gobGlzdGVuZXIpO1xuICAgIH1cblxuICAgIC8vIENoZWNrIGZvciBsaXN0ZW5lciBsZWFrXG4gICAgbSA9ICRnZXRNYXhMaXN0ZW5lcnModGFyZ2V0KTtcbiAgICBpZiAobSA+IDAgJiYgZXhpc3RpbmcubGVuZ3RoID4gbSAmJiAhZXhpc3Rpbmcud2FybmVkKSB7XG4gICAgICBleGlzdGluZy53YXJuZWQgPSB0cnVlO1xuICAgICAgLy8gTm8gZXJyb3IgY29kZSBmb3IgdGhpcyBzaW5jZSBpdCBpcyBhIFdhcm5pbmdcbiAgICAgIC8vIGVzbGludC1kaXNhYmxlLW5leHQtbGluZSBuby1yZXN0cmljdGVkLXN5bnRheFxuICAgICAgdmFyIHcgPSBuZXcgRXJyb3IoJ1Bvc3NpYmxlIEV2ZW50RW1pdHRlciBtZW1vcnkgbGVhayBkZXRlY3RlZC4gJyArXG4gICAgICAgICAgICAgICAgICAgICAgICAgIGV4aXN0aW5nLmxlbmd0aCArICcgJyArIFN0cmluZyh0eXBlKSArICcgbGlzdGVuZXJzICcgK1xuICAgICAgICAgICAgICAgICAgICAgICAgICAnYWRkZWQuIFVzZSBlbWl0dGVyLnNldE1heExpc3RlbmVycygpIHRvICcgK1xuICAgICAgICAgICAgICAgICAgICAgICAgICAnaW5jcmVhc2UgbGltaXQnKTtcbiAgICAgIHcubmFtZSA9ICdNYXhMaXN0ZW5lcnNFeGNlZWRlZFdhcm5pbmcnO1xuICAgICAgdy5lbWl0dGVyID0gdGFyZ2V0O1xuICAgICAgdy50eXBlID0gdHlwZTtcbiAgICAgIHcuY291bnQgPSBleGlzdGluZy5sZW5ndGg7XG4gICAgICBQcm9jZXNzRW1pdFdhcm5pbmcodyk7XG4gICAgfVxuICB9XG5cbiAgcmV0dXJuIHRhcmdldDtcbn1cblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5hZGRMaXN0ZW5lciA9IGZ1bmN0aW9uIGFkZExpc3RlbmVyKHR5cGUsIGxpc3RlbmVyKSB7XG4gIHJldHVybiBfYWRkTGlzdGVuZXIodGhpcywgdHlwZSwgbGlzdGVuZXIsIGZhbHNlKTtcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUub24gPSBFdmVudEVtaXR0ZXIucHJvdG90eXBlLmFkZExpc3RlbmVyO1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLnByZXBlbmRMaXN0ZW5lciA9XG4gICAgZnVuY3Rpb24gcHJlcGVuZExpc3RlbmVyKHR5cGUsIGxpc3RlbmVyKSB7XG4gICAgICByZXR1cm4gX2FkZExpc3RlbmVyKHRoaXMsIHR5cGUsIGxpc3RlbmVyLCB0cnVlKTtcbiAgICB9O1xuXG5mdW5jdGlvbiBvbmNlV3JhcHBlcigpIHtcbiAgdmFyIGFyZ3MgPSBbXTtcbiAgZm9yICh2YXIgaSA9IDA7IGkgPCBhcmd1bWVudHMubGVuZ3RoOyBpKyspIGFyZ3MucHVzaChhcmd1bWVudHNbaV0pO1xuICBpZiAoIXRoaXMuZmlyZWQpIHtcbiAgICB0aGlzLnRhcmdldC5yZW1vdmVMaXN0ZW5lcih0aGlzLnR5cGUsIHRoaXMud3JhcEZuKTtcbiAgICB0aGlzLmZpcmVkID0gdHJ1ZTtcbiAgICBSZWZsZWN0QXBwbHkodGhpcy5saXN0ZW5lciwgdGhpcy50YXJnZXQsIGFyZ3MpO1xuICB9XG59XG5cbmZ1bmN0aW9uIF9vbmNlV3JhcCh0YXJnZXQsIHR5cGUsIGxpc3RlbmVyKSB7XG4gIHZhciBzdGF0ZSA9IHsgZmlyZWQ6IGZhbHNlLCB3cmFwRm46IHVuZGVmaW5lZCwgdGFyZ2V0OiB0YXJnZXQsIHR5cGU6IHR5cGUsIGxpc3RlbmVyOiBsaXN0ZW5lciB9O1xuICB2YXIgd3JhcHBlZCA9IG9uY2VXcmFwcGVyLmJpbmQoc3RhdGUpO1xuICB3cmFwcGVkLmxpc3RlbmVyID0gbGlzdGVuZXI7XG4gIHN0YXRlLndyYXBGbiA9IHdyYXBwZWQ7XG4gIHJldHVybiB3cmFwcGVkO1xufVxuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLm9uY2UgPSBmdW5jdGlvbiBvbmNlKHR5cGUsIGxpc3RlbmVyKSB7XG4gIGlmICh0eXBlb2YgbGlzdGVuZXIgIT09ICdmdW5jdGlvbicpIHtcbiAgICB0aHJvdyBuZXcgVHlwZUVycm9yKCdUaGUgXCJsaXN0ZW5lclwiIGFyZ3VtZW50IG11c3QgYmUgb2YgdHlwZSBGdW5jdGlvbi4gUmVjZWl2ZWQgdHlwZSAnICsgdHlwZW9mIGxpc3RlbmVyKTtcbiAgfVxuICB0aGlzLm9uKHR5cGUsIF9vbmNlV3JhcCh0aGlzLCB0eXBlLCBsaXN0ZW5lcikpO1xuICByZXR1cm4gdGhpcztcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUucHJlcGVuZE9uY2VMaXN0ZW5lciA9XG4gICAgZnVuY3Rpb24gcHJlcGVuZE9uY2VMaXN0ZW5lcih0eXBlLCBsaXN0ZW5lcikge1xuICAgICAgaWYgKHR5cGVvZiBsaXN0ZW5lciAhPT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICB0aHJvdyBuZXcgVHlwZUVycm9yKCdUaGUgXCJsaXN0ZW5lclwiIGFyZ3VtZW50IG11c3QgYmUgb2YgdHlwZSBGdW5jdGlvbi4gUmVjZWl2ZWQgdHlwZSAnICsgdHlwZW9mIGxpc3RlbmVyKTtcbiAgICAgIH1cbiAgICAgIHRoaXMucHJlcGVuZExpc3RlbmVyKHR5cGUsIF9vbmNlV3JhcCh0aGlzLCB0eXBlLCBsaXN0ZW5lcikpO1xuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfTtcblxuLy8gRW1pdHMgYSAncmVtb3ZlTGlzdGVuZXInIGV2ZW50IGlmIGFuZCBvbmx5IGlmIHRoZSBsaXN0ZW5lciB3YXMgcmVtb3ZlZC5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUucmVtb3ZlTGlzdGVuZXIgPVxuICAgIGZ1bmN0aW9uIHJlbW92ZUxpc3RlbmVyKHR5cGUsIGxpc3RlbmVyKSB7XG4gICAgICB2YXIgbGlzdCwgZXZlbnRzLCBwb3NpdGlvbiwgaSwgb3JpZ2luYWxMaXN0ZW5lcjtcblxuICAgICAgaWYgKHR5cGVvZiBsaXN0ZW5lciAhPT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICB0aHJvdyBuZXcgVHlwZUVycm9yKCdUaGUgXCJsaXN0ZW5lclwiIGFyZ3VtZW50IG11c3QgYmUgb2YgdHlwZSBGdW5jdGlvbi4gUmVjZWl2ZWQgdHlwZSAnICsgdHlwZW9mIGxpc3RlbmVyKTtcbiAgICAgIH1cblxuICAgICAgZXZlbnRzID0gdGhpcy5fZXZlbnRzO1xuICAgICAgaWYgKGV2ZW50cyA9PT0gdW5kZWZpbmVkKVxuICAgICAgICByZXR1cm4gdGhpcztcblxuICAgICAgbGlzdCA9IGV2ZW50c1t0eXBlXTtcbiAgICAgIGlmIChsaXN0ID09PSB1bmRlZmluZWQpXG4gICAgICAgIHJldHVybiB0aGlzO1xuXG4gICAgICBpZiAobGlzdCA9PT0gbGlzdGVuZXIgfHwgbGlzdC5saXN0ZW5lciA9PT0gbGlzdGVuZXIpIHtcbiAgICAgICAgaWYgKC0tdGhpcy5fZXZlbnRzQ291bnQgPT09IDApXG4gICAgICAgICAgdGhpcy5fZXZlbnRzID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiAgICAgICAgZWxzZSB7XG4gICAgICAgICAgZGVsZXRlIGV2ZW50c1t0eXBlXTtcbiAgICAgICAgICBpZiAoZXZlbnRzLnJlbW92ZUxpc3RlbmVyKVxuICAgICAgICAgICAgdGhpcy5lbWl0KCdyZW1vdmVMaXN0ZW5lcicsIHR5cGUsIGxpc3QubGlzdGVuZXIgfHwgbGlzdGVuZXIpO1xuICAgICAgICB9XG4gICAgICB9IGVsc2UgaWYgKHR5cGVvZiBsaXN0ICE9PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgIHBvc2l0aW9uID0gLTE7XG5cbiAgICAgICAgZm9yIChpID0gbGlzdC5sZW5ndGggLSAxOyBpID49IDA7IGktLSkge1xuICAgICAgICAgIGlmIChsaXN0W2ldID09PSBsaXN0ZW5lciB8fCBsaXN0W2ldLmxpc3RlbmVyID09PSBsaXN0ZW5lcikge1xuICAgICAgICAgICAgb3JpZ2luYWxMaXN0ZW5lciA9IGxpc3RbaV0ubGlzdGVuZXI7XG4gICAgICAgICAgICBwb3NpdGlvbiA9IGk7XG4gICAgICAgICAgICBicmVhaztcbiAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICBpZiAocG9zaXRpb24gPCAwKVxuICAgICAgICAgIHJldHVybiB0aGlzO1xuXG4gICAgICAgIGlmIChwb3NpdGlvbiA9PT0gMClcbiAgICAgICAgICBsaXN0LnNoaWZ0KCk7XG4gICAgICAgIGVsc2Uge1xuICAgICAgICAgIHNwbGljZU9uZShsaXN0LCBwb3NpdGlvbik7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAobGlzdC5sZW5ndGggPT09IDEpXG4gICAgICAgICAgZXZlbnRzW3R5cGVdID0gbGlzdFswXTtcblxuICAgICAgICBpZiAoZXZlbnRzLnJlbW92ZUxpc3RlbmVyICE9PSB1bmRlZmluZWQpXG4gICAgICAgICAgdGhpcy5lbWl0KCdyZW1vdmVMaXN0ZW5lcicsIHR5cGUsIG9yaWdpbmFsTGlzdGVuZXIgfHwgbGlzdGVuZXIpO1xuICAgICAgfVxuXG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9O1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLm9mZiA9IEV2ZW50RW1pdHRlci5wcm90b3R5cGUucmVtb3ZlTGlzdGVuZXI7XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUucmVtb3ZlQWxsTGlzdGVuZXJzID1cbiAgICBmdW5jdGlvbiByZW1vdmVBbGxMaXN0ZW5lcnModHlwZSkge1xuICAgICAgdmFyIGxpc3RlbmVycywgZXZlbnRzLCBpO1xuXG4gICAgICBldmVudHMgPSB0aGlzLl9ldmVudHM7XG4gICAgICBpZiAoZXZlbnRzID09PSB1bmRlZmluZWQpXG4gICAgICAgIHJldHVybiB0aGlzO1xuXG4gICAgICAvLyBub3QgbGlzdGVuaW5nIGZvciByZW1vdmVMaXN0ZW5lciwgbm8gbmVlZCB0byBlbWl0XG4gICAgICBpZiAoZXZlbnRzLnJlbW92ZUxpc3RlbmVyID09PSB1bmRlZmluZWQpIHtcbiAgICAgICAgaWYgKGFyZ3VtZW50cy5sZW5ndGggPT09IDApIHtcbiAgICAgICAgICB0aGlzLl9ldmVudHMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuICAgICAgICAgIHRoaXMuX2V2ZW50c0NvdW50ID0gMDtcbiAgICAgICAgfSBlbHNlIGlmIChldmVudHNbdHlwZV0gIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgIGlmICgtLXRoaXMuX2V2ZW50c0NvdW50ID09PSAwKVxuICAgICAgICAgICAgdGhpcy5fZXZlbnRzID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiAgICAgICAgICBlbHNlXG4gICAgICAgICAgICBkZWxldGUgZXZlbnRzW3R5cGVdO1xuICAgICAgICB9XG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgICAgfVxuXG4gICAgICAvLyBlbWl0IHJlbW92ZUxpc3RlbmVyIGZvciBhbGwgbGlzdGVuZXJzIG9uIGFsbCBldmVudHNcbiAgICAgIGlmIChhcmd1bWVudHMubGVuZ3RoID09PSAwKSB7XG4gICAgICAgIHZhciBrZXlzID0gT2JqZWN0LmtleXMoZXZlbnRzKTtcbiAgICAgICAgdmFyIGtleTtcbiAgICAgICAgZm9yIChpID0gMDsgaSA8IGtleXMubGVuZ3RoOyArK2kpIHtcbiAgICAgICAgICBrZXkgPSBrZXlzW2ldO1xuICAgICAgICAgIGlmIChrZXkgPT09ICdyZW1vdmVMaXN0ZW5lcicpIGNvbnRpbnVlO1xuICAgICAgICAgIHRoaXMucmVtb3ZlQWxsTGlzdGVuZXJzKGtleSk7XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy5yZW1vdmVBbGxMaXN0ZW5lcnMoJ3JlbW92ZUxpc3RlbmVyJyk7XG4gICAgICAgIHRoaXMuX2V2ZW50cyA9IE9iamVjdC5jcmVhdGUobnVsbCk7XG4gICAgICAgIHRoaXMuX2V2ZW50c0NvdW50ID0gMDtcbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgICB9XG5cbiAgICAgIGxpc3RlbmVycyA9IGV2ZW50c1t0eXBlXTtcblxuICAgICAgaWYgKHR5cGVvZiBsaXN0ZW5lcnMgPT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgdGhpcy5yZW1vdmVMaXN0ZW5lcih0eXBlLCBsaXN0ZW5lcnMpO1xuICAgICAgfSBlbHNlIGlmIChsaXN0ZW5lcnMgIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAvLyBMSUZPIG9yZGVyXG4gICAgICAgIGZvciAoaSA9IGxpc3RlbmVycy5sZW5ndGggLSAxOyBpID49IDA7IGktLSkge1xuICAgICAgICAgIHRoaXMucmVtb3ZlTGlzdGVuZXIodHlwZSwgbGlzdGVuZXJzW2ldKTtcbiAgICAgICAgfVxuICAgICAgfVxuXG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9O1xuXG5mdW5jdGlvbiBfbGlzdGVuZXJzKHRhcmdldCwgdHlwZSwgdW53cmFwKSB7XG4gIHZhciBldmVudHMgPSB0YXJnZXQuX2V2ZW50cztcblxuICBpZiAoZXZlbnRzID09PSB1bmRlZmluZWQpXG4gICAgcmV0dXJuIFtdO1xuXG4gIHZhciBldmxpc3RlbmVyID0gZXZlbnRzW3R5cGVdO1xuICBpZiAoZXZsaXN0ZW5lciA9PT0gdW5kZWZpbmVkKVxuICAgIHJldHVybiBbXTtcblxuICBpZiAodHlwZW9mIGV2bGlzdGVuZXIgPT09ICdmdW5jdGlvbicpXG4gICAgcmV0dXJuIHVud3JhcCA/IFtldmxpc3RlbmVyLmxpc3RlbmVyIHx8IGV2bGlzdGVuZXJdIDogW2V2bGlzdGVuZXJdO1xuXG4gIHJldHVybiB1bndyYXAgP1xuICAgIHVud3JhcExpc3RlbmVycyhldmxpc3RlbmVyKSA6IGFycmF5Q2xvbmUoZXZsaXN0ZW5lciwgZXZsaXN0ZW5lci5sZW5ndGgpO1xufVxuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLmxpc3RlbmVycyA9IGZ1bmN0aW9uIGxpc3RlbmVycyh0eXBlKSB7XG4gIHJldHVybiBfbGlzdGVuZXJzKHRoaXMsIHR5cGUsIHRydWUpO1xufTtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5yYXdMaXN0ZW5lcnMgPSBmdW5jdGlvbiByYXdMaXN0ZW5lcnModHlwZSkge1xuICByZXR1cm4gX2xpc3RlbmVycyh0aGlzLCB0eXBlLCBmYWxzZSk7XG59O1xuXG5FdmVudEVtaXR0ZXIubGlzdGVuZXJDb3VudCA9IGZ1bmN0aW9uKGVtaXR0ZXIsIHR5cGUpIHtcbiAgaWYgKHR5cGVvZiBlbWl0dGVyLmxpc3RlbmVyQ291bnQgPT09ICdmdW5jdGlvbicpIHtcbiAgICByZXR1cm4gZW1pdHRlci5saXN0ZW5lckNvdW50KHR5cGUpO1xuICB9IGVsc2Uge1xuICAgIHJldHVybiBsaXN0ZW5lckNvdW50LmNhbGwoZW1pdHRlciwgdHlwZSk7XG4gIH1cbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUubGlzdGVuZXJDb3VudCA9IGxpc3RlbmVyQ291bnQ7XG5mdW5jdGlvbiBsaXN0ZW5lckNvdW50KHR5cGUpIHtcbiAgdmFyIGV2ZW50cyA9IHRoaXMuX2V2ZW50cztcblxuICBpZiAoZXZlbnRzICE9PSB1bmRlZmluZWQpIHtcbiAgICB2YXIgZXZsaXN0ZW5lciA9IGV2ZW50c1t0eXBlXTtcblxuICAgIGlmICh0eXBlb2YgZXZsaXN0ZW5lciA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgcmV0dXJuIDE7XG4gICAgfSBlbHNlIGlmIChldmxpc3RlbmVyICE9PSB1bmRlZmluZWQpIHtcbiAgICAgIHJldHVybiBldmxpc3RlbmVyLmxlbmd0aDtcbiAgICB9XG4gIH1cblxuICByZXR1cm4gMDtcbn1cblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5ldmVudE5hbWVzID0gZnVuY3Rpb24gZXZlbnROYW1lcygpIHtcbiAgcmV0dXJuIHRoaXMuX2V2ZW50c0NvdW50ID4gMCA/IFJlZmxlY3RPd25LZXlzKHRoaXMuX2V2ZW50cykgOiBbXTtcbn07XG5cbmZ1bmN0aW9uIGFycmF5Q2xvbmUoYXJyLCBuKSB7XG4gIHZhciBjb3B5ID0gbmV3IEFycmF5KG4pO1xuICBmb3IgKHZhciBpID0gMDsgaSA8IG47ICsraSlcbiAgICBjb3B5W2ldID0gYXJyW2ldO1xuICByZXR1cm4gY29weTtcbn1cblxuZnVuY3Rpb24gc3BsaWNlT25lKGxpc3QsIGluZGV4KSB7XG4gIGZvciAoOyBpbmRleCArIDEgPCBsaXN0Lmxlbmd0aDsgaW5kZXgrKylcbiAgICBsaXN0W2luZGV4XSA9IGxpc3RbaW5kZXggKyAxXTtcbiAgbGlzdC5wb3AoKTtcbn1cblxuZnVuY3Rpb24gdW53cmFwTGlzdGVuZXJzKGFycikge1xuICB2YXIgcmV0ID0gbmV3IEFycmF5KGFyci5sZW5ndGgpO1xuICBmb3IgKHZhciBpID0gMDsgaSA8IHJldC5sZW5ndGg7ICsraSkge1xuICAgIHJldFtpXSA9IGFycltpXS5saXN0ZW5lciB8fCBhcnJbaV07XG4gIH1cbiAgcmV0dXJuIHJldDtcbn1cblxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9ldmVudHMvZXZlbnRzLmpzXG4vLyBtb2R1bGUgaWQgPSAyMFxuLy8gbW9kdWxlIGNodW5rcyA9IDMgNSA2IDcgOCAxMCAxMSAxMiAyNCAyNSAyOSIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBpcyByZXNwb25zaWJsZSBmb3IgaGFuZGxpbmcgR3JpZCBldmVudHNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgR3JpZCB7XG4gIC8qKlxuICAgKiBHcmlkIGlkXG4gICAqXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBpZFxuICAgKi9cbiAgY29uc3RydWN0b3IoaWQpIHtcbiAgICB0aGlzLmlkID0gaWQ7XG4gICAgdGhpcy4kY29udGFpbmVyID0gJCgnIycgKyB0aGlzLmlkICsgJ19ncmlkJyk7XG4gIH1cblxuICAvKipcbiAgICogR2V0IGdyaWQgaWRcbiAgICpcbiAgICogQHJldHVybnMge3N0cmluZ31cbiAgICovXG4gIGdldElkKCkge1xuICAgIHJldHVybiB0aGlzLmlkO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBncmlkIGNvbnRhaW5lclxuICAgKlxuICAgKiBAcmV0dXJucyB7alF1ZXJ5fVxuICAgKi9cbiAgZ2V0Q29udGFpbmVyKCkge1xuICAgIHJldHVybiB0aGlzLiRjb250YWluZXI7XG4gIH1cblxuICAvKipcbiAgICogR2V0IGdyaWQgaGVhZGVyIGNvbnRhaW5lclxuICAgKlxuICAgKiBAcmV0dXJucyB7alF1ZXJ5fVxuICAgKi9cbiAgZ2V0SGVhZGVyQ29udGFpbmVyKCkge1xuICAgIHJldHVybiB0aGlzLiRjb250YWluZXIuY2xvc2VzdCgnLmpzLWdyaWQtcGFuZWwnKS5maW5kKCcuanMtZ3JpZC1oZWFkZXInKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZCB3aXRoIGV4dGVybmFsIGV4dGVuc2lvbnNcbiAgICpcbiAgICogQHBhcmFtIHtvYmplY3R9IGV4dGVuc2lvblxuICAgKi9cbiAgYWRkRXh0ZW5zaW9uKGV4dGVuc2lvbikge1xuICAgIGV4dGVuc2lvbi5leHRlbmQodGhpcyk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9ncmlkLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIGNsYXNzIFRhZ2dhYmxlRmllbGQgaXMgcmVzcG9uc2libGUgZm9yIHByb3ZpZGluZyBmdW5jdGlvbmFsaXR5IGZyb20gYm9vdHN0cmFwLXRva2VuZmllbGQgcGx1Z2luLlxuICogSXQgYWxsb3dzIHRvIGhhdmUgdGFnZ2FibGUgZmllbGRzIHdoaWNoIGFyZSBzcGxpdCBpbiBzZXBhcmF0ZSBibG9ja3Mgb25jZSB5b3UgY2xpY2sgZW50ZXIuIFZhbHVlcyBvcmlnaW5hbGx5IHNhdmVkXG4gKiBpbiBjb21tYSBzcGxpdCBzdHJpbmdzLlxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBUYWdnYWJsZUZpZWxkIHtcbiAgLyoqXG4gICAqIEBwYXJhbSB7c3RyaW5nfSB0b2tlbkZpZWxkU2VsZWN0b3IgLSAgYSBzZWxlY3RvciB3aGljaCBpcyB1c2VkIHdpdGhpbiBqUXVlcnkgb2JqZWN0LlxuICAgKiBAcGFyYW0ge29iamVjdH0gb3B0aW9ucyAtIGV4dGVuZHMgYmFzaWMgdG9rZW5GaWVsZCBiZWhhdmlvciB3aXRoIGFkZGl0aW9uYWwgb3B0aW9ucyBzdWNoIGFzIG1pbkxlbmd0aCwgZGVsaW1pdGVyLFxuICAgKiBhbGxvdyB0byBhZGQgdG9rZW4gb24gZm9jdXMgb3V0IGFjdGlvbi4gU2VlIGJvb3RzdHJhcC10b2tlbmZpZWxkIGRvY3MgZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gICAqL1xuICBjb25zdHJ1Y3Rvcih7dG9rZW5GaWVsZFNlbGVjdG9yLCBvcHRpb25zID0ge319KSB7XG4gICAgJCh0b2tlbkZpZWxkU2VsZWN0b3IpLnRva2VuZmllbGQob3B0aW9ucyk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvdGFnZ2FibGUtZmllbGQuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogVGhpcyBjbGFzcyBpbml0IFRpbnlNQ0UgaW5zdGFuY2VzIGluIHRoZSBiYWNrLW9mZmljZS4gSXQgaXMgd2lsZGx5IGluc3BpcmVkIGJ5XG4gKiB0aGUgc2NyaXB0cyBmcm9tIGpzL2FkbWluIEFuZCBpdCBhY3R1YWxseSBsb2FkcyBUaW55TUNFIGZyb20gdGhlIGpzL3RpbnlfbWNlXG4gKiBmb2xkZXIgYWxvbmcgd2l0aCBpdHMgbW9kdWxlcy4gT25lIGltcHJvdmVtZW50IGNvdWxkIGJlIHRvIGluc3RhbGwgVGlueU1DRSB2aWFcbiAqIG5wbSBhbmQgZnVsbHkgaW50ZWdyYXRlIGluIHRoZSBiYWNrLW9mZmljZSB0aGVtZS5cbiAqL1xuY2xhc3MgVGlueU1DRUVkaXRvciB7XG4gIGNvbnN0cnVjdG9yKG9wdGlvbnMpIHtcbiAgICBvcHRpb25zID0gb3B0aW9ucyB8fCB7fTtcbiAgICB0aGlzLnRpbnlNQ0VMb2FkZWQgPSBmYWxzZTtcbiAgICBpZiAodHlwZW9mIG9wdGlvbnMuYmFzZUFkbWluVXJsID09ICd1bmRlZmluZWQnKSB7XG4gICAgICBpZiAodHlwZW9mIHdpbmRvdy5iYXNlQWRtaW5EaXIgIT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICAgb3B0aW9ucy5iYXNlQWRtaW5VcmwgPSB3aW5kb3cuYmFzZUFkbWluRGlyO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgY29uc3QgcGF0aFBhcnRzID0gd2luZG93LmxvY2F0aW9uLnBhdGhuYW1lLnNwbGl0KCcvJyk7XG4gICAgICAgIHBhdGhQYXJ0cy5ldmVyeShmdW5jdGlvbihwYXRoUGFydCkge1xuICAgICAgICAgIGlmIChwYXRoUGFydCAhPT0gJycpIHtcbiAgICAgICAgICAgIG9wdGlvbnMuYmFzZUFkbWluVXJsID0gYC8ke3BhdGhQYXJ0fS9gO1xuXG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgICAgfVxuXG4gICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgIH0pO1xuICAgICAgfVxuICAgIH1cbiAgICBpZiAodHlwZW9mIG9wdGlvbnMubGFuZ0lzUnRsID09ICd1bmRlZmluZWQnKSB7XG4gICAgICBvcHRpb25zLmxhbmdJc1J0bCA9IHR5cGVvZiB3aW5kb3cubGFuZ19pc19ydGwgIT0gJ3VuZGVmaW5lZCcgPyB3aW5kb3cubGFuZ19pc19ydGwgPT09ICcxJyA6IGZhbHNlO1xuICAgIH1cbiAgICB0aGlzLnNldHVwVGlueU1DRShvcHRpb25zKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJbml0aWFsIHNldHVwIHdoaWNoIGNoZWNrcyBpZiB0aGUgdGlueU1DRSBsaWJyYXJ5IGlzIGFscmVhZHkgbG9hZGVkLlxuICAgKlxuICAgKiBAcGFyYW0gY29uZmlnXG4gICAqL1xuICBzZXR1cFRpbnlNQ0UoY29uZmlnKSB7XG4gICAgaWYgKHR5cGVvZiB0aW55TUNFID09PSAndW5kZWZpbmVkJykge1xuICAgICAgdGhpcy5sb2FkQW5kSW5pdFRpbnlNQ0UoY29uZmlnKTtcbiAgICB9IGVsc2Uge1xuICAgICAgdGhpcy5pbml0VGlueU1DRShjb25maWcpO1xuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBQcmVwYXJlIHRoZSBjb25maWcgYW5kIGluaXQgYWxsIFRpbnlNQ0UgZWRpdG9yc1xuICAgKlxuICAgKiBAcGFyYW0gY29uZmlnXG4gICAqL1xuICBpbml0VGlueU1DRShjb25maWcpIHtcbiAgICBjb25maWcgPSBPYmplY3QuYXNzaWduKHtcbiAgICAgIHNlbGVjdG9yOiAnLnJ0ZScsXG4gICAgICBwbHVnaW5zOiAnYWxpZ24gY29sb3JwaWNrZXIgbGluayBpbWFnZSBmaWxlbWFuYWdlciB0YWJsZSBtZWRpYSBwbGFjZWhvbGRlciBhZHZsaXN0IGNvZGUgdGFibGUgYXV0b3Jlc2l6ZScsXG4gICAgICBicm93c2VyX3NwZWxsY2hlY2s6IHRydWUsXG4gICAgICB0b29sYmFyMTogJ2NvZGUsY29sb3JwaWNrZXIsYm9sZCxpdGFsaWMsdW5kZXJsaW5lLHN0cmlrZXRocm91Z2gsYmxvY2txdW90ZSxsaW5rLGFsaWduLGJ1bGxpc3QsbnVtbGlzdCx0YWJsZSxpbWFnZSxtZWRpYSxmb3JtYXRzZWxlY3QnLFxuICAgICAgdG9vbGJhcjI6ICcnLFxuICAgICAgZXh0ZXJuYWxfZmlsZW1hbmFnZXJfcGF0aDogY29uZmlnLmJhc2VBZG1pblVybCArICdmaWxlbWFuYWdlci8nLFxuICAgICAgZmlsZW1hbmFnZXJfdGl0bGU6ICdGaWxlIG1hbmFnZXInLFxuICAgICAgZXh0ZXJuYWxfcGx1Z2luczoge1xuICAgICAgICAnZmlsZW1hbmFnZXInOiBjb25maWcuYmFzZUFkbWluVXJsICsgJ2ZpbGVtYW5hZ2VyL3BsdWdpbi5taW4uanMnXG4gICAgICB9LFxuICAgICAgbGFuZ3VhZ2U6IGlzb191c2VyLFxuICAgICAgY29udGVudF9zdHlsZSA6IChjb25maWcubGFuZ0lzUnRsID8gJ2JvZHkge2RpcmVjdGlvbjpydGw7fScgOiAnJyksXG4gICAgICBza2luOiAncHJlc3Rhc2hvcCcsXG4gICAgICBtZW51YmFyOiBmYWxzZSxcbiAgICAgIHN0YXR1c2JhcjogZmFsc2UsXG4gICAgICByZWxhdGl2ZV91cmxzOiBmYWxzZSxcbiAgICAgIGNvbnZlcnRfdXJsczogZmFsc2UsXG4gICAgICBlbnRpdHlfZW5jb2Rpbmc6ICdyYXcnLFxuICAgICAgZXh0ZW5kZWRfdmFsaWRfZWxlbWVudHM6ICdlbVtjbGFzc3xuYW1lfGlkXSxAW3JvbGV8ZGF0YS0qfGFyaWEtKl0nLFxuICAgICAgdmFsaWRfY2hpbGRyZW46ICcrKlsqXScsXG4gICAgICB2YWxpZF9lbGVtZW50czogJypbKl0nLFxuICAgICAgcmVsX2xpc3Q6W1xuICAgICAgICB7IHRpdGxlOiAnbm9mb2xsb3cnLCB2YWx1ZTogJ25vZm9sbG93JyB9XG4gICAgICBdLFxuICAgICAgZWRpdG9yX3NlbGVjdG9yIDonYXV0b2xvYWRfcnRlJyxcbiAgICAgIGluaXRfaW5zdGFuY2VfY2FsbGJhY2s6ICgpID0+IHsgdGhpcy5jaGFuZ2VUb01hdGVyaWFsKCk7IH0sXG4gICAgICBzZXR1cCA6IChlZGl0b3IpID0+IHsgdGhpcy5zZXR1cEVkaXRvcihlZGl0b3IpOyB9LFxuICAgIH0sIGNvbmZpZyk7XG5cbiAgICBpZiAodHlwZW9mIGNvbmZpZy5lZGl0b3Jfc2VsZWN0b3IgIT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgIGNvbmZpZy5zZWxlY3RvciA9ICcuJyArIGNvbmZpZy5lZGl0b3Jfc2VsZWN0b3I7XG4gICAgfVxuXG4gICAgLy8gQ2hhbmdlIGljb25zIGluIHBvcHVwc1xuICAgICQoJ2JvZHknKS5vbignY2xpY2snLCAnLm1jZS1idG4sIC5tY2Utb3BlbiwgLm1jZS1tZW51LWl0ZW0nLCAoKSA9PiB7IHRoaXMuY2hhbmdlVG9NYXRlcmlhbCgpOyB9KTtcblxuICAgIHRpbnlNQ0UuaW5pdChjb25maWcpO1xuICAgIHRoaXMud2F0Y2hUYWJDaGFuZ2VzKGNvbmZpZyk7XG4gIH1cblxuICAvKipcbiAgICogU2V0dXAgVGlueU1DRSBlZGl0b3Igb25jZSBpdCBoYXMgYmVlbiBpbml0aWFsaXplZFxuICAgKlxuICAgKiBAcGFyYW0gZWRpdG9yXG4gICAqL1xuICBzZXR1cEVkaXRvcihlZGl0b3IpIHtcbiAgICBlZGl0b3Iub24oJ2xvYWRDb250ZW50JywgKGV2ZW50KSA9PiB7XG4gICAgICB0aGlzLmhhbmRsZUNvdW50ZXJUaW55KGV2ZW50LnRhcmdldC5pZCk7XG4gICAgfSk7XG4gICAgZWRpdG9yLm9uKCdjaGFuZ2UnLCAoZXZlbnQpID0+IHtcbiAgICAgIHRpbnlNQ0UudHJpZ2dlclNhdmUoKTtcbiAgICAgIHRoaXMuaGFuZGxlQ291bnRlclRpbnkoZXZlbnQudGFyZ2V0LmlkKTtcbiAgICB9KTtcbiAgICBlZGl0b3Iub24oJ2JsdXInLCAoKSA9PiB7XG4gICAgICB0aW55TUNFLnRyaWdnZXJTYXZlKCk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogV2hlbiB0aGUgZWRpdG9yIGlzIGluc2lkZSBhIHRhYiBpdCBjYW4gY2F1c2UgYSBidWcgb24gdGFiIHN3aXRjaGluZy5cbiAgICogU28gd2UgY2hlY2sgaWYgdGhlIGVkaXRvciBpcyBjb250YWluZWQgaW4gYSBuYXZpZ2F0aW9uIGFuZCByZWZyZXNoIHRoZSBlZGl0b3Igd2hlbiBpdHNcbiAgICogcGFyZW50IHRhYiBpcyBzaG93bi5cbiAgICpcbiAgICogQHBhcmFtIGNvbmZpZ1xuICAgKi9cbiAgd2F0Y2hUYWJDaGFuZ2VzKGNvbmZpZykge1xuICAgICQoY29uZmlnLnNlbGVjdG9yKS5lYWNoKChpbmRleCwgdGV4dGFyZWEpID0+IHtcbiAgICAgIGNvbnN0IHRyYW5zbGF0ZWRGaWVsZCA9ICQodGV4dGFyZWEpLmNsb3Nlc3QoJy50cmFuc2xhdGlvbi1maWVsZCcpO1xuICAgICAgY29uc3QgdGFiQ29udGFpbmVyID0gJCh0ZXh0YXJlYSkuY2xvc2VzdCgnLnRyYW5zbGF0aW9ucy50YWJiYWJsZScpO1xuXG4gICAgICBpZiAodHJhbnNsYXRlZEZpZWxkLmxlbmd0aCAmJiB0YWJDb250YWluZXIubGVuZ3RoKSB7XG4gICAgICAgIGNvbnN0IHRleHRhcmVhTG9jYWxlID0gdHJhbnNsYXRlZEZpZWxkLmRhdGEoJ2xvY2FsZScpO1xuICAgICAgICBjb25zdCB0ZXh0YXJlYUxpbmtTZWxlY3RvciA9ICcubmF2LWl0ZW0gYVtkYXRhLWxvY2FsZT1cIicrdGV4dGFyZWFMb2NhbGUrJ1wiXSc7XG5cbiAgICAgICAgJCh0ZXh0YXJlYUxpbmtTZWxlY3RvciwgdGFiQ29udGFpbmVyKS5vbignc2hvd24uYnMudGFiJywgKCkgPT4ge1xuICAgICAgICAgIGNvbnN0IGVkaXRvciA9IHRpbnlNQ0UuZ2V0KHRleHRhcmVhLmlkKTtcbiAgICAgICAgICBpZiAoZWRpdG9yKSB7XG4gICAgICAgICAgICAvL1Jlc2V0IGNvbnRlbnQgdG8gZm9yY2UgcmVmcmVzaCBvZiBlZGl0b3JcbiAgICAgICAgICAgIGVkaXRvci5zZXRDb250ZW50KGVkaXRvci5nZXRDb250ZW50KCkpO1xuICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogTG9hZHMgdGhlIFRpbnlNQ0UgamF2YXNjcmlwdCBsaWJyYXJ5IGFuZCB0aGVuIGluaXQgdGhlIGVkaXRvcnNcbiAgICpcbiAgICogQHBhcmFtIGNvbmZpZ1xuICAgKi9cbiAgbG9hZEFuZEluaXRUaW55TUNFKGNvbmZpZykge1xuICAgIGlmICh0aGlzLnRpbnlNQ0VMb2FkZWQpIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICB0aGlzLnRpbnlNQ0VMb2FkZWQgPSB0cnVlO1xuICAgIGNvbnN0IHBhdGhBcnJheSA9IGNvbmZpZy5iYXNlQWRtaW5Vcmwuc3BsaXQoJy8nKTtcbiAgICBwYXRoQXJyYXkuc3BsaWNlKChwYXRoQXJyYXkubGVuZ3RoIC0gMiksIDIpO1xuICAgIGNvbnN0IGZpbmFsUGF0aCA9IHBhdGhBcnJheS5qb2luKCcvJyk7XG4gICAgd2luZG93LnRpbnlNQ0VQcmVJbml0ID0ge307XG4gICAgd2luZG93LnRpbnlNQ0VQcmVJbml0LmJhc2UgPSBmaW5hbFBhdGgrJy9qcy90aW55X21jZSc7XG4gICAgd2luZG93LnRpbnlNQ0VQcmVJbml0LnN1ZmZpeCA9ICcubWluJztcbiAgICAkLmdldFNjcmlwdChgJHtmaW5hbFBhdGh9L2pzL3RpbnlfbWNlL3RpbnltY2UubWluLmpzYCwgKCkgPT4ge3RoaXMuc2V0dXBUaW55TUNFKGNvbmZpZyl9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBSZXBsYWNlIGluaXRpYWwgVGlueU1DRSBpY29ucyB3aXRoIG1hdGVyaWFsIGljb25zXG4gICAqL1xuICBjaGFuZ2VUb01hdGVyaWFsKCkge1xuICAgIGxldCBtYXRlcmlhbEljb25Bc3NvYyA9IHtcbiAgICAgICdtY2UtaS1jb2RlJzogJzxpIGNsYXNzPVwibWF0ZXJpYWwtaWNvbnNcIj5jb2RlPC9pPicsXG4gICAgICAnbWNlLWktbm9uZSc6ICc8aSBjbGFzcz1cIm1hdGVyaWFsLWljb25zXCI+Zm9ybWF0X2NvbG9yX3RleHQ8L2k+JyxcbiAgICAgICdtY2UtaS1ib2xkJzogJzxpIGNsYXNzPVwibWF0ZXJpYWwtaWNvbnNcIj5mb3JtYXRfYm9sZDwvaT4nLFxuICAgICAgJ21jZS1pLWl0YWxpYyc6ICc8aSBjbGFzcz1cIm1hdGVyaWFsLWljb25zXCI+Zm9ybWF0X2l0YWxpYzwvaT4nLFxuICAgICAgJ21jZS1pLXVuZGVybGluZSc6ICc8aSBjbGFzcz1cIm1hdGVyaWFsLWljb25zXCI+Zm9ybWF0X3VuZGVybGluZWQ8L2k+JyxcbiAgICAgICdtY2UtaS1zdHJpa2V0aHJvdWdoJzogJzxpIGNsYXNzPVwibWF0ZXJpYWwtaWNvbnNcIj5mb3JtYXRfc3RyaWtldGhyb3VnaDwvaT4nLFxuICAgICAgJ21jZS1pLWJsb2NrcXVvdGUnOiAnPGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPmZvcm1hdF9xdW90ZTwvaT4nLFxuICAgICAgJ21jZS1pLWxpbmsnOiAnPGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPmxpbms8L2k+JyxcbiAgICAgICdtY2UtaS1hbGlnbmxlZnQnOiAnPGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPmZvcm1hdF9hbGlnbl9sZWZ0PC9pPicsXG4gICAgICAnbWNlLWktYWxpZ25jZW50ZXInOiAnPGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPmZvcm1hdF9hbGlnbl9jZW50ZXI8L2k+JyxcbiAgICAgICdtY2UtaS1hbGlnbnJpZ2h0JzogJzxpIGNsYXNzPVwibWF0ZXJpYWwtaWNvbnNcIj5mb3JtYXRfYWxpZ25fcmlnaHQ8L2k+JyxcbiAgICAgICdtY2UtaS1hbGlnbmp1c3RpZnknOiAnPGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPmZvcm1hdF9hbGlnbl9qdXN0aWZ5PC9pPicsXG4gICAgICAnbWNlLWktYnVsbGlzdCc6ICc8aSBjbGFzcz1cIm1hdGVyaWFsLWljb25zXCI+Zm9ybWF0X2xpc3RfYnVsbGV0ZWQ8L2k+JyxcbiAgICAgICdtY2UtaS1udW1saXN0JzogJzxpIGNsYXNzPVwibWF0ZXJpYWwtaWNvbnNcIj5mb3JtYXRfbGlzdF9udW1iZXJlZDwvaT4nLFxuICAgICAgJ21jZS1pLWltYWdlJzogJzxpIGNsYXNzPVwibWF0ZXJpYWwtaWNvbnNcIj5pbWFnZTwvaT4nLFxuICAgICAgJ21jZS1pLXRhYmxlJzogJzxpIGNsYXNzPVwibWF0ZXJpYWwtaWNvbnNcIj5ncmlkX29uPC9pPicsXG4gICAgICAnbWNlLWktbWVkaWEnOiAnPGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPnZpZGVvX2xpYnJhcnk8L2k+JyxcbiAgICAgICdtY2UtaS1icm93c2UnOiAnPGkgY2xhc3M9XCJtYXRlcmlhbC1pY29uc1wiPmF0dGFjaG1lbnQ8L2k+JyxcbiAgICAgICdtY2UtaS1jaGVja2JveCc6ICc8aSBjbGFzcz1cIm1jZS1pY28gbWNlLWktY2hlY2tib3hcIj48L2k+JyxcbiAgICB9O1xuXG4gICAgJC5lYWNoKG1hdGVyaWFsSWNvbkFzc29jLCBmdW5jdGlvbiAoaW5kZXgsIHZhbHVlKSB7XG4gICAgICAkKGAuJHtpbmRleH1gKS5yZXBsYWNlV2l0aCh2YWx1ZSk7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogVXBkYXRlcyB0aGUgY2hhcmFjdGVycyBjb3VudGVyXG4gICAqXG4gICAqIEBwYXJhbSBpZFxuICAgKi9cbiAgaGFuZGxlQ291bnRlclRpbnkoaWQpIHtcbiAgICBjb25zdCB0ZXh0YXJlYSA9ICQoYCMke2lkfWApO1xuICAgIGNvbnN0IGNvdW50ZXIgPSB0ZXh0YXJlYS5hdHRyKCdjb3VudGVyJyk7XG4gICAgY29uc3QgY291bnRlclR5cGUgPSB0ZXh0YXJlYS5hdHRyKCdjb3VudGVyX3R5cGUnKTtcbiAgICBjb25zdCBtYXggPSB0aW55TUNFLmFjdGl2ZUVkaXRvci5nZXRCb2R5KCkudGV4dENvbnRlbnQubGVuZ3RoO1xuXG4gICAgdGV4dGFyZWEucGFyZW50KCkuZmluZCgnc3Bhbi5jdXJyZW50TGVuZ3RoJykudGV4dChtYXgpO1xuICAgIGlmICgncmVjb21tZW5kZWQnICE9PSBjb3VudGVyVHlwZSAmJiBtYXggPiBjb3VudGVyKSB7XG4gICAgICB0ZXh0YXJlYS5wYXJlbnQoKS5maW5kKCdzcGFuLm1heExlbmd0aCcpLmFkZENsYXNzKCd0ZXh0LWRhbmdlcicpO1xuICAgIH0gZWxzZSB7XG4gICAgICB0ZXh0YXJlYS5wYXJlbnQoKS5maW5kKCdzcGFuLm1heExlbmd0aCcpLnJlbW92ZUNsYXNzKCd0ZXh0LWRhbmdlcicpO1xuICAgIH1cbiAgfVxufVxuXG5leHBvcnQgZGVmYXVsdCBUaW55TUNFRWRpdG9yO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy90aW55bWNlLWVkaXRvci5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBUcmFuc2xhdGFibGVJbnB1dCBmcm9tICcuLi8uLi9jb21wb25lbnRzL3RyYW5zbGF0YWJsZS1pbnB1dCc7XG5pbXBvcnQgVGFnZ2FibGVGaWVsZCBmcm9tICcuLi8uLi9jb21wb25lbnRzL3RhZ2dhYmxlLWZpZWxkJztcbmltcG9ydCBGb3JtU3VibWl0QnV0dG9uIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvZm9ybS1zdWJtaXQtYnV0dG9uJztcbmltcG9ydCBHcmlkIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvZ3JpZC9ncmlkJztcbmltcG9ydCBTb3J0aW5nRXh0ZW5zaW9uIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vc29ydGluZy1leHRlbnNpb24nO1xuaW1wb3J0IEZpbHRlcnNSZXNldEV4dGVuc2lvbiBmcm9tICcuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2ZpbHRlcnMtcmVzZXQtZXh0ZW5zaW9uJztcbmltcG9ydCBSZWxvYWRMaXN0QWN0aW9uRXh0ZW5zaW9uIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vcmVsb2FkLWxpc3QtZXh0ZW5zaW9uJztcbmltcG9ydCBDb2x1bW5Ub2dnbGluZ0V4dGVuc2lvbiBmcm9tICcuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2NvbHVtbi10b2dnbGluZy1leHRlbnNpb24nO1xuaW1wb3J0IFN1Ym1pdFJvd0FjdGlvbkV4dGVuc2lvbiBmcm9tICcuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2FjdGlvbi9yb3cvc3VibWl0LXJvdy1hY3Rpb24tZXh0ZW5zaW9uJztcbmltcG9ydCBTdWJtaXRCdWxrRXh0ZW5zaW9uIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vc3VibWl0LWJ1bGstYWN0aW9uLWV4dGVuc2lvbic7XG5pbXBvcnQgQnVsa0FjdGlvbkNoZWNrYm94RXh0ZW5zaW9uIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYnVsay1hY3Rpb24tY2hlY2tib3gtZXh0ZW5zaW9uJztcbmltcG9ydCBFeHBvcnRUb1NxbE1hbmFnZXJFeHRlbnNpb24gZnJvbSAnLi4vLi4vY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9leHBvcnQtdG8tc3FsLW1hbmFnZXItZXh0ZW5zaW9uJztcbmltcG9ydCBGaWx0ZXJzU3VibWl0QnV0dG9uRW5hYmxlckV4dGVuc2lvblxuICBmcm9tICcuLi8uLi9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2ZpbHRlcnMtc3VibWl0LWJ1dHRvbi1lbmFibGVyLWV4dGVuc2lvbic7XG5pbXBvcnQgQ2hvaWNlVHJlZSBmcm9tICcuLi8uLi9jb21wb25lbnRzL2Zvcm0vY2hvaWNlLXRyZWUnO1xuaW1wb3J0IFRyYW5zbGF0YWJsZUZpZWxkIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvdHJhbnNsYXRhYmxlLWZpZWxkJztcbmltcG9ydCBUaW55TUNFRWRpdG9yIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvdGlueW1jZS1lZGl0b3InO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbiQoKCkgPT4ge1xuICBbJ21hbnVmYWN0dXJlcicsICdtYW51ZmFjdHVyZXJfYWRkcmVzcyddLmZvckVhY2goKGdyaWROYW1lKSA9PiB7XG4gICAgY29uc3QgZ3JpZCA9IG5ldyBHcmlkKGdyaWROYW1lKTtcbiAgICBncmlkLmFkZEV4dGVuc2lvbihuZXcgRXhwb3J0VG9TcWxNYW5hZ2VyRXh0ZW5zaW9uKCkpO1xuICAgIGdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBSZWxvYWRMaXN0QWN0aW9uRXh0ZW5zaW9uKCkpO1xuICAgIGdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBTb3J0aW5nRXh0ZW5zaW9uKCkpO1xuICAgIGdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBGaWx0ZXJzUmVzZXRFeHRlbnNpb24oKSk7XG4gICAgZ3JpZC5hZGRFeHRlbnNpb24obmV3IENvbHVtblRvZ2dsaW5nRXh0ZW5zaW9uKCkpO1xuICAgIGdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBTdWJtaXRSb3dBY3Rpb25FeHRlbnNpb24oKSk7XG4gICAgZ3JpZC5hZGRFeHRlbnNpb24obmV3IFN1Ym1pdEJ1bGtFeHRlbnNpb24oKSk7XG4gICAgZ3JpZC5hZGRFeHRlbnNpb24obmV3IEJ1bGtBY3Rpb25DaGVja2JveEV4dGVuc2lvbigpKTtcbiAgICBncmlkLmFkZEV4dGVuc2lvbihuZXcgRmlsdGVyc1N1Ym1pdEJ1dHRvbkVuYWJsZXJFeHRlbnNpb24oKSk7XG4gIH0pO1xuXG4gIG5ldyBUcmFuc2xhdGFibGVJbnB1dCgpO1xuICBuZXcgVHJhbnNsYXRhYmxlRmllbGQoKTtcbiAgbmV3IFRpbnlNQ0VFZGl0b3IoKTtcbiAgbmV3IFRhZ2dhYmxlRmllbGQoe1xuICAgIHRva2VuRmllbGRTZWxlY3RvcjogJ2lucHV0LmpzLXRhZ2dhYmxlLWZpZWxkJyxcbiAgICBvcHRpb25zOiB7XG4gICAgICBjcmVhdGVUb2tlbnNPbkJsdXI6IHRydWUsXG4gICAgfSxcbiAgfSk7XG5cbiAgbmV3IEZvcm1TdWJtaXRCdXR0b24oKTtcbiAgbmV3IENob2ljZVRyZWUoJyNtYW51ZmFjdHVyZXJfc2hvcF9hc3NvY2lhdGlvbicpLmVuYWJsZUF1dG9DaGVja0NoaWxkcmVuKCk7XG59KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL21hbnVmYWN0dXJlci9pbmRleC5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCByZXNldFNlYXJjaCBmcm9tICcuLi8uLi8uLi9hcHAvdXRpbHMvcmVzZXRfc2VhcmNoJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIEZpbHRlcnNSZXNldEV4dGVuc2lvbiBleHRlbmRzIGdyaWQgd2l0aCBmaWx0ZXJzIHJlc2V0dGluZ1xuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBGaWx0ZXJzUmVzZXRFeHRlbnNpb24ge1xuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLXJlc2V0LXNlYXJjaCcsIChldmVudCkgPT4ge1xuICAgICAgcmVzZXRTZWFyY2goJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCd1cmwnKSwgJChldmVudC5jdXJyZW50VGFyZ2V0KS5kYXRhKCdyZWRpcmVjdCcpKTtcbiAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXJlc2V0LWV4dGVuc2lvbi5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCB7RXZlbnRFbWl0dGVyfSBmcm9tICcuL2V2ZW50LWVtaXR0ZXInO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogVGhpcyBjbGFzcyBpcyB1c2VkIHRvIGF1dG9tYXRpY2FsbHkgdG9nZ2xlIHRyYW5zbGF0ZWQgZmllbGRzIChkaXNwbGF5ZWQgd2l0aCB0YWJzXG4gKiB1c2luZyB0aGUgVHJhbnNsYXRlVHlwZSBTeW1mb255IGZvcm0gdHlwZSkuXG4gKiBBbHNvIGNvbXBhdGlibGUgd2l0aCBUcmFuc2xhdGFibGVJbnB1dCBjaGFuZ2VzLlxuICovXG5jbGFzcyBUcmFuc2xhdGFibGVGaWVsZCB7XG4gIGNvbnN0cnVjdG9yKG9wdGlvbnMpIHtcbiAgICBvcHRpb25zID0gb3B0aW9ucyB8fCB7fTtcblxuICAgIHRoaXMubG9jYWxlQnV0dG9uU2VsZWN0b3IgPSBvcHRpb25zLmxvY2FsZUJ1dHRvblNlbGVjdG9yIHx8wqAnLnRyYW5zbGF0aW9uc0xvY2FsZXMubmF2IC5uYXYtaXRlbSBhW2RhdGEtdG9nZ2xlPVwidGFiXCJdJztcbiAgICB0aGlzLmxvY2FsZU5hdmlnYXRpb25TZWxlY3RvciA9IG9wdGlvbnMubG9jYWxlTmF2aWdhdGlvblNlbGVjdG9yIHx8wqAnLnRyYW5zbGF0aW9uc0xvY2FsZXMubmF2JztcblxuICAgICQoJ2JvZHknKS5vbignc2hvd24uYnMudGFiJywgdGhpcy5sb2NhbGVCdXR0b25TZWxlY3RvciwgdGhpcy50b2dnbGVMYW5ndWFnZS5iaW5kKHRoaXMpKTtcbiAgICBFdmVudEVtaXR0ZXIub24oJ2xhbmd1YWdlU2VsZWN0ZWQnLCB0aGlzLnRvZ2dsZUZpZWxkcy5iaW5kKHRoaXMpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBEaXNwYXRjaCBldmVudCBvbiBsYW5ndWFnZSBzZWxlY3Rpb24gdG8gdXBkYXRlIGlucHV0cyBhbmQgb3RoZXIgY29tcG9uZW50cyB3aGljaCBkZXBlbmQgb24gdGhlIGxvY2FsZS5cbiAgICpcbiAgICogQHBhcmFtIGV2ZW50XG4gICAqL1xuICB0b2dnbGVMYW5ndWFnZShldmVudCkge1xuICAgIGNvbnN0IGxvY2FsZUxpbmsgPSAkKGV2ZW50LnRhcmdldCk7XG4gICAgY29uc3QgZm9ybSA9IGxvY2FsZUxpbmsuY2xvc2VzdCgnZm9ybScpO1xuICAgIEV2ZW50RW1pdHRlci5lbWl0KCdsYW5ndWFnZVNlbGVjdGVkJywge3NlbGVjdGVkTG9jYWxlOiBsb2NhbGVMaW5rLmRhdGEoJ2xvY2FsZScpLCBmb3JtOiBmb3JtfSk7XG4gIH1cblxuICAvKipcbiAgICogVG9nZ2xlIGFsbCB0cmFuc3RhdGlvbiBmaWVsZHMgdG8gdGhlIHNlbGVjdGVkIGxvY2FsZVxuICAgKlxuICAgKiBAcGFyYW0gZXZlbnRcbiAgICovXG4gIHRvZ2dsZUZpZWxkcyhldmVudCkge1xuICAgICQodGhpcy5sb2NhbGVOYXZpZ2F0aW9uU2VsZWN0b3IpLmVhY2goKGluZGV4LCBuYXZpZ2F0aW9uKSA9PiB7XG4gICAgICBjb25zdCBzZWxlY3RlZExpbmsgPSAkKCcubmF2LWl0ZW0gYS5hY3RpdmUnLCBuYXZpZ2F0aW9uKTtcbiAgICAgIGNvbnN0IHNlbGVjdGVkTG9jYWxlID0gc2VsZWN0ZWRMaW5rLmRhdGEoJ2xvY2FsZScpO1xuICAgICAgaWYgKGV2ZW50LnNlbGVjdGVkTG9jYWxlICE9PSBzZWxlY3RlZExvY2FsZSkge1xuICAgICAgICAkKCcubmF2LWl0ZW0gYVtkYXRhLWxvY2FsZT1cIicrZXZlbnQuc2VsZWN0ZWRMb2NhbGUrJ1wiXScsIG5hdmlnYXRpb24pLnRhYignc2hvdycpO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG59XG5cbmV4cG9ydCBkZWZhdWx0IFRyYW5zbGF0YWJsZUZpZWxkO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy90cmFuc2xhdGFibGUtZmllbGQuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG4vKipcbiAqIENsYXNzIFJlbG9hZExpc3RFeHRlbnNpb24gZXh0ZW5kcyBncmlkIHdpdGggXCJMaXN0IHJlbG9hZFwiIGFjdGlvblxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBSZWxvYWRMaXN0RXh0ZW5zaW9uIHtcbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldEhlYWRlckNvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtY29tbW9uX3JlZnJlc2hfbGlzdC1ncmlkLWFjdGlvbicsICgpID0+IHtcbiAgICAgIGxvY2F0aW9uLnJlbG9hZCgpO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3JlbG9hZC1saXN0LWV4dGVuc2lvbi5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDb21wb25lbnQgd2hpY2ggYWxsb3dzIHN1Ym1pdHRpbmcgdmVyeSBzaW1wbGUgZm9ybXMgd2l0aG91dCBoYXZpbmcgdG8gdXNlIDxmb3JtPiBlbGVtZW50LlxuICpcbiAqIFVzZWZ1bCB3aGVuIHBlcmZvcm1pbmcgYWN0aW9ucyBvbiByZXNvdXJjZSB3aGVyZSBVUkwgY29udGFpbnMgYWxsIG5lZWRlZCBkYXRhLlxuICogRm9yIGV4YW1wbGUsIHRvIHRvZ2dsZSBjYXRlZ29yeSBzdGF0dXMgdmlhIFwiUE9TVCAvY2F0ZWdvcmllcy8yL3RvZ2dsZS1zdGF0dXMpXCJcbiAqIG9yIGRlbGV0ZSBjb3ZlciBpbWFnZSB2aWEgXCJQT1NUIC9jYXRlZ29yaWVzLzIvZGVsZXRlLWNvdmVyLWltYWdlXCIuXG4gKlxuICogVXNhZ2UgZXhhbXBsZSBpbiB0ZW1wbGF0ZTpcbiAqXG4gKiA8YnV0dG9uIGNsYXNzPVwianMtZm9ybS1zdWJtaXQtYnRuXCJcbiAqICAgICAgICAgZGF0YS1mb3JtLXN1Ym1pdC11cmw9XCIvbXktY3VzdG9tLXVybFwiICAgICAgICAgIC8vIChyZXF1aXJlZCkgVVJMIHRvIHdoaWNoIGZvcm0gd2lsbCBiZSBzdWJtaXR0ZWRcbiAqICAgICAgICAgZGF0YS1mb3JtLWNzcmYtdG9rZW49XCJteS1nZW5lcmF0ZWQtY3NyZi10b2tlblwiIC8vIChvcHRpb25hbCkgdG8gaW5jcmVhc2Ugc2VjdXJpdHlcbiAqICAgICAgICAgZGF0YS1mb3JtLWNvbmZpcm0tbWVzc2FnZT1cIkFyZSB5b3Ugc3VyZT9cIiAgICAgIC8vIChvcHRpb25hbCkgdG8gY29uZmlybSBhY3Rpb24gYmVmb3JlIHN1Ym1pdFxuICogICAgICAgICB0eXBlPVwiYnV0dG9uXCIgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gbWFrZSBzdXJlIGl0cyBzaW1wbGUgYnV0dG9uXG4gKiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gc28gd2UgY2FuIGF2b2lkIHN1Ym1pdHRpbmcgYWN0dWFsIGZvcm1cbiAqICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyB3aGVuIG91ciBidXR0b24gaXMgZGVmaW5lZCBpbnNpZGUgZm9ybVxuICogPlxuICogICAgIENsaWNrIG1lIHRvIHN1Ym1pdCBmb3JtXG4gKiA8L2J1dHRvbj5cbiAqXG4gKiBJbiBwYWdlIHNwZWNpZmljIEpTIHlvdSBoYXZlIHRvIGVuYWJsZSB0aGlzIGZlYXR1cmU6XG4gKlxuICogbmV3IEZvcm1TdWJtaXRCdXR0b24oKTtcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgRm9ybVN1Ym1pdEJ1dHRvbiB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsICcuanMtZm9ybS1zdWJtaXQtYnRuJywgZnVuY3Rpb24gKGV2ZW50KSB7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICBjb25zdCAkYnRuID0gJCh0aGlzKTtcblxuICAgICAgaWYgKCRidG4uZGF0YSgnZm9ybS1jb25maXJtLW1lc3NhZ2UnKSAmJiBmYWxzZSA9PT0gY29uZmlybSgkYnRuLmRhdGEoJ2Zvcm0tY29uZmlybS1tZXNzYWdlJykpKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cblxuICAgICAgY29uc3QgJGZvcm0gPSAkKCc8Zm9ybT4nLCB7XG4gICAgICAgICdhY3Rpb24nOiAkYnRuLmRhdGEoJ2Zvcm0tc3VibWl0LXVybCcpLFxuICAgICAgICAnbWV0aG9kJzogJ1BPU1QnLFxuICAgICAgfSk7XG5cbiAgICAgIGlmICgkYnRuLmRhdGEoJ2Zvcm0tY3NyZi10b2tlbicpKSB7XG4gICAgICAgICRmb3JtLmFwcGVuZCgkKCc8aW5wdXQ+Jywge1xuICAgICAgICAgICd0eXBlJzogJ19oaWRkZW4nLFxuICAgICAgICAgICduYW1lJzogJ19jc3JmX3Rva2VuJyxcbiAgICAgICAgICAndmFsdWUnOiAkYnRuLmRhdGEoJ2Zvcm0tY3NyZi10b2tlbicpXG4gICAgICAgIH0pKTtcbiAgICAgIH1cblxuICAgICAgJGZvcm0uYXBwZW5kVG8oJ2JvZHknKS5zdWJtaXQoKTtcbiAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9mb3JtLXN1Ym1pdC1idXR0b24uanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgVGFibGVTb3J0aW5nIGZyb20gJy4uLy4uLy4uL2FwcC91dGlscy90YWJsZS1zb3J0aW5nJztcblxuLyoqXG4gKiBDbGFzcyBSZWxvYWRMaXN0RXh0ZW5zaW9uIGV4dGVuZHMgZ3JpZCB3aXRoIFwiTGlzdCByZWxvYWRcIiBhY3Rpb25cbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgU29ydGluZ0V4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgY29uc3QgJHNvcnRhYmxlVGFibGUgPSBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJ3RhYmxlLnRhYmxlJyk7XG5cbiAgICBuZXcgVGFibGVTb3J0aW5nKCRzb3J0YWJsZVRhYmxlKS5hdHRhY2goKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9zb3J0aW5nLWV4dGVuc2lvbi5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbi8qKlxuICogUmVzcG9uc2libGUgZm9yIGdyaWQgZmlsdGVycyBzZWFyY2ggYW5kIHJlc2V0IGJ1dHRvbiBhdmFpbGFiaWxpdHkgd2hlbiBmaWx0ZXIgaW5wdXRzIGNoYW5nZXMuXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEZpbHRlcnNTdWJtaXRCdXR0b25FbmFibGVyRXh0ZW5zaW9uIHtcblxuICAvKipcbiAgICogRXh0ZW5kIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIGNvbnN0ICRmaWx0ZXJzUm93ID0gZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCcuY29sdW1uLWZpbHRlcnMnKTtcbiAgICAkZmlsdGVyc1Jvdy5maW5kKCcuZ3JpZC1zZWFyY2gtYnV0dG9uJykucHJvcCgnZGlzYWJsZWQnLCB0cnVlKTtcblxuICAgICRmaWx0ZXJzUm93LmZpbmQoJ2lucHV0LCBzZWxlY3QnKS5vbignaW5wdXQnLCAoKSA9PiB7XG4gICAgICAkZmlsdGVyc1Jvdy5maW5kKCcuZ3JpZC1zZWFyY2gtYnV0dG9uJykucHJvcCgnZGlzYWJsZWQnLCBmYWxzZSk7XG4gICAgICAkZmlsdGVyc1Jvdy5maW5kKCcuanMtZ3JpZC1yZXNldC1idXR0b24nKS5wcm9wKCdoaWRkZW4nLCBmYWxzZSk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZmlsdGVycy1zdWJtaXQtYnV0dG9uLWVuYWJsZXItZXh0ZW5zaW9uLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIEJ1bGtBY3Rpb25TZWxlY3RDaGVja2JveEV4dGVuc2lvblxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBCdWxrQWN0aW9uQ2hlY2tib3hFeHRlbnNpb24ge1xuICAvKipcbiAgICogRXh0ZW5kIGdyaWQgd2l0aCBidWxrIGFjdGlvbiBjaGVja2JveGVzIGhhbmRsaW5nIGZ1bmN0aW9uYWxpdHlcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIHRoaXMuX2hhbmRsZUJ1bGtBY3Rpb25DaGVja2JveFNlbGVjdChncmlkKTtcbiAgICB0aGlzLl9oYW5kbGVCdWxrQWN0aW9uU2VsZWN0QWxsQ2hlY2tib3goZ3JpZCk7XG4gIH1cblxuICAvKipcbiAgICogSGFuZGxlcyBcIlNlbGVjdCBhbGxcIiBidXR0b24gaW4gdGhlIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGFuZGxlQnVsa0FjdGlvblNlbGVjdEFsbENoZWNrYm94KGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLm9uKCdjaGFuZ2UnLCAnLmpzLWJ1bGstYWN0aW9uLXNlbGVjdC1hbGwnLCAoZSkgPT4ge1xuICAgICAgY29uc3QgJGNoZWNrYm94ID0gJChlLmN1cnJlbnRUYXJnZXQpO1xuXG4gICAgICBjb25zdCBpc0NoZWNrZWQgPSAkY2hlY2tib3guaXMoJzpjaGVja2VkJyk7XG4gICAgICBpZiAoaXNDaGVja2VkKSB7XG4gICAgICAgIHRoaXMuX2VuYWJsZUJ1bGtBY3Rpb25zQnRuKGdyaWQpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgdGhpcy5fZGlzYWJsZUJ1bGtBY3Rpb25zQnRuKGdyaWQpO1xuICAgICAgfVxuXG4gICAgICBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5qcy1idWxrLWFjdGlvbi1jaGVja2JveCcpLnByb3AoJ2NoZWNrZWQnLCBpc0NoZWNrZWQpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEhhbmRsZXMgZWFjaCBidWxrIGFjdGlvbiBjaGVja2JveCBzZWxlY3QgaW4gdGhlIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGFuZGxlQnVsa0FjdGlvbkNoZWNrYm94U2VsZWN0KGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLm9uKCdjaGFuZ2UnLCAnLmpzLWJ1bGstYWN0aW9uLWNoZWNrYm94JywgKCkgPT4ge1xuICAgICAgY29uc3QgY2hlY2tlZFJvd3NDb3VudCA9IGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgnLmpzLWJ1bGstYWN0aW9uLWNoZWNrYm94OmNoZWNrZWQnKS5sZW5ndGg7XG5cbiAgICAgIGlmIChjaGVja2VkUm93c0NvdW50ID4gMCkge1xuICAgICAgICB0aGlzLl9lbmFibGVCdWxrQWN0aW9uc0J0bihncmlkKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHRoaXMuX2Rpc2FibGVCdWxrQWN0aW9uc0J0bihncmlkKTtcbiAgICAgIH1cbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFbmFibGUgYnVsayBhY3Rpb25zIGJ1dHRvblxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9lbmFibGVCdWxrQWN0aW9uc0J0bihncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCcuanMtYnVsay1hY3Rpb25zLWJ0bicpLnByb3AoJ2Rpc2FibGVkJywgZmFsc2UpO1xuICB9XG5cbiAgLyoqXG4gICAqIERpc2FibGUgYnVsayBhY3Rpb25zIGJ1dHRvblxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9kaXNhYmxlQnVsa0FjdGlvbnNCdG4oZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgnLmpzLWJ1bGstYWN0aW9ucy1idG4nKS5wcm9wKCdkaXNhYmxlZCcsIHRydWUpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2J1bGstYWN0aW9uLWNoZWNrYm94LWV4dGVuc2lvbi5qcyJdLCJzb3VyY2VSb290IjoiIn0=