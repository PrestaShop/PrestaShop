window["sql_manager"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 393);
/******/ })
/************************************************************************/
/******/ ({

/***/ 393:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }(); /**
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      * 2007-2019 PrestaShop SA and Contributors
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

var _grid = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/grid\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _grid2 = _interopRequireDefault(_grid);

var _reloadListExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/reload-list-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _reloadListExtension2 = _interopRequireDefault(_reloadListExtension);

var _exportToSqlManagerExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/export-to-sql-manager-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _exportToSqlManagerExtension2 = _interopRequireDefault(_exportToSqlManagerExtension);

var _filtersResetExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/filters-reset-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _filtersResetExtension2 = _interopRequireDefault(_filtersResetExtension);

var _sortingExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/sorting-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _sortingExtension2 = _interopRequireDefault(_sortingExtension);

var _bulkActionCheckboxExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/bulk-action-checkbox-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _bulkActionCheckboxExtension2 = _interopRequireDefault(_bulkActionCheckboxExtension);

var _submitBulkActionExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/submit-bulk-action-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _submitBulkActionExtension2 = _interopRequireDefault(_submitBulkActionExtension);

var _submitGridActionExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/submit-grid-action-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _submitGridActionExtension2 = _interopRequireDefault(_submitGridActionExtension);

var _linkRowActionExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/link-row-action-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _linkRowActionExtension2 = _interopRequireDefault(_linkRowActionExtension);

var _filtersSubmitButtonEnablerExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/filters-submit-button-enabler-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _filtersSubmitButtonEnablerExtension2 = _interopRequireDefault(_filtersSubmitButtonEnablerExtension);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var $ = window.$;

var SqlManagerPage = function () {
  function SqlManagerPage() {
    var _this = this;

    _classCallCheck(this, SqlManagerPage);

    var requestSqlGrid = new _grid2.default('sql_request');
    requestSqlGrid.addExtension(new _reloadListExtension2.default());
    requestSqlGrid.addExtension(new _exportToSqlManagerExtension2.default());
    requestSqlGrid.addExtension(new _filtersResetExtension2.default());
    requestSqlGrid.addExtension(new _sortingExtension2.default());
    requestSqlGrid.addExtension(new _linkRowActionExtension2.default());
    requestSqlGrid.addExtension(new _submitGridActionExtension2.default());
    requestSqlGrid.addExtension(new _submitBulkActionExtension2.default());
    requestSqlGrid.addExtension(new _bulkActionCheckboxExtension2.default());
    requestSqlGrid.addExtension(new _filtersSubmitButtonEnablerExtension2.default());

    $(document).on('change', '.js-db-tables-select', function () {
      return _this.reloadDbTableColumns();
    });
    $(document).on('click', '.js-add-db-table-to-query-btn', function (event) {
      return _this.addDbTableToQuery(event);
    });
    $(document).on('click', '.js-add-db-table-column-to-query-btn', function (event) {
      return _this.addDbTableColumnToQuery(event);
    });
  }

  /**
   * Reload database table columns
   */


  _createClass(SqlManagerPage, [{
    key: 'reloadDbTableColumns',
    value: function reloadDbTableColumns() {
      var $selectedOption = $('.js-db-tables-select').find('option:selected');
      var $table = $('.js-table-columns');

      $.ajax($selectedOption.data('table-columns-url')).then(function (response) {
        $('.js-table-alert').addClass('d-none');

        var columns = response.columns;

        $table.removeClass('d-none');
        $table.find('tbody').empty();

        columns.forEach(function (column) {
          var $row = $('<tr>').append($('<td>').html(column.name)).append($('<td>').html(column.type)).append($('<td>').addClass('text-right').append($('<button>').addClass('btn btn-sm btn-outline-secondary js-add-db-table-column-to-query-btn').attr('data-column', column.name).html($table.data('action-btn'))));

          $table.find('tbody').append($row);
        });
      });
    }

    /**
     * Add selected database table name to SQL query input
     *
     * @param event
     */

  }, {
    key: 'addDbTableToQuery',
    value: function addDbTableToQuery(event) {
      var $selectedOption = $('.js-db-tables-select').find('option:selected');

      if ($selectedOption.length === 0) {
        alert($(event.target).data('choose-table-message'));

        return;
      }

      this.addToQuery($selectedOption.val());
    }

    /**
     * Add table column to SQL query input
     *
     * @param event
     */

  }, {
    key: 'addDbTableColumnToQuery',
    value: function addDbTableColumnToQuery(event) {
      this.addToQuery($(event.target).data('column'));
    }

    /**
     * Add data to SQL query input
     *
     * @param {String} data
     */

  }, {
    key: 'addToQuery',
    value: function addToQuery(data) {
      var $queryInput = $('#sql_request_sql');
      $queryInput.val($queryInput.val() + ' ' + data);
    }
  }]);

  return SqlManagerPage;
}();

$(document).ready(function () {
  new SqlManagerPage();
});

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgOTNlN2NjMWY2YWNkMjQxMGYyNDIiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvc3FsLW1hbmFnZXIvaW5kZXguanMiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsIlNxbE1hbmFnZXJQYWdlIiwicmVxdWVzdFNxbEdyaWQiLCJHcmlkIiwiYWRkRXh0ZW5zaW9uIiwiUmVsb2FkTGlzdEFjdGlvbkV4dGVuc2lvbiIsIkV4cG9ydFRvU3FsTWFuYWdlckV4dGVuc2lvbiIsIkZpbHRlcnNSZXNldEV4dGVuc2lvbiIsIlNvcnRpbmdFeHRlbnNpb24iLCJMaW5rUm93QWN0aW9uRXh0ZW5zaW9uIiwiU3VibWl0R3JpZEV4dGVuc2lvbiIsIlN1Ym1pdEJ1bGtFeHRlbnNpb24iLCJCdWxrQWN0aW9uQ2hlY2tib3hFeHRlbnNpb24iLCJGaWx0ZXJzU3VibWl0QnV0dG9uRW5hYmxlckV4dGVuc2lvbiIsImRvY3VtZW50Iiwib24iLCJyZWxvYWREYlRhYmxlQ29sdW1ucyIsImV2ZW50IiwiYWRkRGJUYWJsZVRvUXVlcnkiLCJhZGREYlRhYmxlQ29sdW1uVG9RdWVyeSIsIiRzZWxlY3RlZE9wdGlvbiIsImZpbmQiLCIkdGFibGUiLCJhamF4IiwiZGF0YSIsInRoZW4iLCJyZXNwb25zZSIsImFkZENsYXNzIiwiY29sdW1ucyIsInJlbW92ZUNsYXNzIiwiZW1wdHkiLCJmb3JFYWNoIiwiY29sdW1uIiwiJHJvdyIsImFwcGVuZCIsImh0bWwiLCJuYW1lIiwidHlwZSIsImF0dHIiLCJsZW5ndGgiLCJhbGVydCIsInRhcmdldCIsImFkZFRvUXVlcnkiLCJ2YWwiLCIkcXVlcnlJbnB1dCIsInJlYWR5Il0sIm1hcHBpbmdzIjoiOztBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQSxtREFBMkMsY0FBYzs7QUFFekQ7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxhQUFLO0FBQ0w7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxtQ0FBMkIsMEJBQTBCLEVBQUU7QUFDdkQseUNBQWlDLGVBQWU7QUFDaEQ7QUFDQTtBQUNBOztBQUVBO0FBQ0EsOERBQXNELCtEQUErRDs7QUFFckg7QUFDQTs7QUFFQTtBQUNBOzs7Ozs7Ozs7OztxakJDaEVBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBeUJBOzs7O0FBQ0E7Ozs7QUFDQTs7OztBQUNBOzs7O0FBQ0E7Ozs7QUFDQTs7OztBQUNBOzs7O0FBQ0E7Ozs7QUFDQTs7OztBQUNBOzs7Ozs7OztBQUdBLElBQU1BLElBQUlDLE9BQU9ELENBQWpCOztJQUVNRSxjO0FBQ0osNEJBQWM7QUFBQTs7QUFBQTs7QUFDWixRQUFNQyxpQkFBaUIsSUFBSUMsY0FBSixDQUFTLGFBQVQsQ0FBdkI7QUFDQUQsbUJBQWVFLFlBQWYsQ0FBNEIsSUFBSUMsNkJBQUosRUFBNUI7QUFDQUgsbUJBQWVFLFlBQWYsQ0FBNEIsSUFBSUUscUNBQUosRUFBNUI7QUFDQUosbUJBQWVFLFlBQWYsQ0FBNEIsSUFBSUcsK0JBQUosRUFBNUI7QUFDQUwsbUJBQWVFLFlBQWYsQ0FBNEIsSUFBSUksMEJBQUosRUFBNUI7QUFDQU4sbUJBQWVFLFlBQWYsQ0FBNEIsSUFBSUssZ0NBQUosRUFBNUI7QUFDQVAsbUJBQWVFLFlBQWYsQ0FBNEIsSUFBSU0sbUNBQUosRUFBNUI7QUFDQVIsbUJBQWVFLFlBQWYsQ0FBNEIsSUFBSU8sbUNBQUosRUFBNUI7QUFDQVQsbUJBQWVFLFlBQWYsQ0FBNEIsSUFBSVEscUNBQUosRUFBNUI7QUFDQVYsbUJBQWVFLFlBQWYsQ0FBNEIsSUFBSVMsNkNBQUosRUFBNUI7O0FBRUFkLE1BQUVlLFFBQUYsRUFBWUMsRUFBWixDQUFlLFFBQWYsRUFBeUIsc0JBQXpCLEVBQWlEO0FBQUEsYUFBTSxNQUFLQyxvQkFBTCxFQUFOO0FBQUEsS0FBakQ7QUFDQWpCLE1BQUVlLFFBQUYsRUFBWUMsRUFBWixDQUFlLE9BQWYsRUFBd0IsK0JBQXhCLEVBQXlELFVBQUNFLEtBQUQ7QUFBQSxhQUFXLE1BQUtDLGlCQUFMLENBQXVCRCxLQUF2QixDQUFYO0FBQUEsS0FBekQ7QUFDQWxCLE1BQUVlLFFBQUYsRUFBWUMsRUFBWixDQUFlLE9BQWYsRUFBd0Isc0NBQXhCLEVBQWdFLFVBQUNFLEtBQUQ7QUFBQSxhQUFXLE1BQUtFLHVCQUFMLENBQTZCRixLQUE3QixDQUFYO0FBQUEsS0FBaEU7QUFDRDs7QUFFRDs7Ozs7OzsyQ0FHdUI7QUFDckIsVUFBTUcsa0JBQWtCckIsRUFBRSxzQkFBRixFQUEwQnNCLElBQTFCLENBQStCLGlCQUEvQixDQUF4QjtBQUNBLFVBQU1DLFNBQVN2QixFQUFFLG1CQUFGLENBQWY7O0FBRUFBLFFBQUV3QixJQUFGLENBQU9ILGdCQUFnQkksSUFBaEIsQ0FBcUIsbUJBQXJCLENBQVAsRUFDR0MsSUFESCxDQUNRLFVBQUNDLFFBQUQsRUFBYztBQUNsQjNCLFVBQUUsaUJBQUYsRUFBcUI0QixRQUFyQixDQUE4QixRQUE5Qjs7QUFFQSxZQUFNQyxVQUFVRixTQUFTRSxPQUF6Qjs7QUFFQU4sZUFBT08sV0FBUCxDQUFtQixRQUFuQjtBQUNBUCxlQUFPRCxJQUFQLENBQVksT0FBWixFQUFxQlMsS0FBckI7O0FBRUFGLGdCQUFRRyxPQUFSLENBQWdCLFVBQUNDLE1BQUQsRUFBWTtBQUMxQixjQUFNQyxPQUFPbEMsRUFBRSxNQUFGLEVBQ1ZtQyxNQURVLENBQ0huQyxFQUFFLE1BQUYsRUFBVW9DLElBQVYsQ0FBZUgsT0FBT0ksSUFBdEIsQ0FERyxFQUVWRixNQUZVLENBRUhuQyxFQUFFLE1BQUYsRUFBVW9DLElBQVYsQ0FBZUgsT0FBT0ssSUFBdEIsQ0FGRyxFQUdWSCxNQUhVLENBR0huQyxFQUFFLE1BQUYsRUFBVTRCLFFBQVYsQ0FBbUIsWUFBbkIsRUFDTE8sTUFESyxDQUNFbkMsRUFBRSxVQUFGLEVBQ0w0QixRQURLLENBQ0ksc0VBREosRUFFTFcsSUFGSyxDQUVBLGFBRkEsRUFFZU4sT0FBT0ksSUFGdEIsRUFHTEQsSUFISyxDQUdBYixPQUFPRSxJQUFQLENBQVksWUFBWixDQUhBLENBREYsQ0FIRyxDQUFiOztBQVdBRixpQkFBT0QsSUFBUCxDQUFZLE9BQVosRUFBcUJhLE1BQXJCLENBQTRCRCxJQUE1QjtBQUNELFNBYkQ7QUFjRCxPQXZCSDtBQXdCRDs7QUFFRDs7Ozs7Ozs7c0NBS2tCaEIsSyxFQUFPO0FBQ3ZCLFVBQU1HLGtCQUFrQnJCLEVBQUUsc0JBQUYsRUFBMEJzQixJQUExQixDQUErQixpQkFBL0IsQ0FBeEI7O0FBRUEsVUFBSUQsZ0JBQWdCbUIsTUFBaEIsS0FBMkIsQ0FBL0IsRUFBa0M7QUFDaENDLGNBQU16QyxFQUFFa0IsTUFBTXdCLE1BQVIsRUFBZ0JqQixJQUFoQixDQUFxQixzQkFBckIsQ0FBTjs7QUFFQTtBQUNEOztBQUVELFdBQUtrQixVQUFMLENBQWdCdEIsZ0JBQWdCdUIsR0FBaEIsRUFBaEI7QUFDRDs7QUFFRDs7Ozs7Ozs7NENBS3dCMUIsSyxFQUFPO0FBQzdCLFdBQUt5QixVQUFMLENBQWdCM0MsRUFBRWtCLE1BQU13QixNQUFSLEVBQWdCakIsSUFBaEIsQ0FBcUIsUUFBckIsQ0FBaEI7QUFDRDs7QUFFRDs7Ozs7Ozs7K0JBS1dBLEksRUFBTTtBQUNmLFVBQU1vQixjQUFjN0MsRUFBRSxrQkFBRixDQUFwQjtBQUNBNkMsa0JBQVlELEdBQVosQ0FBZ0JDLFlBQVlELEdBQVosS0FBb0IsR0FBcEIsR0FBMEJuQixJQUExQztBQUNEOzs7Ozs7QUFHSHpCLEVBQUVlLFFBQUYsRUFBWStCLEtBQVosQ0FBa0IsWUFBTTtBQUN0QixNQUFJNUMsY0FBSjtBQUNELENBRkQsRSIsImZpbGUiOiJzcWxfbWFuYWdlci5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBpZGVudGl0eSBmdW5jdGlvbiBmb3IgY2FsbGluZyBoYXJtb255IGltcG9ydHMgd2l0aCB0aGUgY29ycmVjdCBjb250ZXh0XG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmkgPSBmdW5jdGlvbih2YWx1ZSkgeyByZXR1cm4gdmFsdWU7IH07XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDM5Myk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gd2VicGFjay9ib290c3RyYXAgOTNlN2NjMWY2YWNkMjQxMGYyNDIiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgR3JpZCBmcm9tICdAY29tcG9uZW50cy9ncmlkL2dyaWQnO1xuaW1wb3J0IFJlbG9hZExpc3RBY3Rpb25FeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vcmVsb2FkLWxpc3QtZXh0ZW5zaW9uJztcbmltcG9ydCBFeHBvcnRUb1NxbE1hbmFnZXJFeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZXhwb3J0LXRvLXNxbC1tYW5hZ2VyLWV4dGVuc2lvbic7XG5pbXBvcnQgRmlsdGVyc1Jlc2V0RXh0ZW5zaW9uIGZyb20gJ0Bjb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2ZpbHRlcnMtcmVzZXQtZXh0ZW5zaW9uJztcbmltcG9ydCBTb3J0aW5nRXh0ZW5zaW9uIGZyb20gJ0Bjb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3NvcnRpbmctZXh0ZW5zaW9uJztcbmltcG9ydCBCdWxrQWN0aW9uQ2hlY2tib3hFeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYnVsay1hY3Rpb24tY2hlY2tib3gtZXh0ZW5zaW9uJztcbmltcG9ydCBTdWJtaXRCdWxrRXh0ZW5zaW9uIGZyb20gJ0Bjb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3N1Ym1pdC1idWxrLWFjdGlvbi1leHRlbnNpb24nO1xuaW1wb3J0IFN1Ym1pdEdyaWRFeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vc3VibWl0LWdyaWQtYWN0aW9uLWV4dGVuc2lvbic7XG5pbXBvcnQgTGlua1Jvd0FjdGlvbkV4dGVuc2lvbiBmcm9tICdAY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9saW5rLXJvdy1hY3Rpb24tZXh0ZW5zaW9uJztcbmltcG9ydCBGaWx0ZXJzU3VibWl0QnV0dG9uRW5hYmxlckV4dGVuc2lvblxuICBmcm9tICdAY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXN1Ym1pdC1idXR0b24tZW5hYmxlci1leHRlbnNpb24nO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbmNsYXNzIFNxbE1hbmFnZXJQYWdlIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgY29uc3QgcmVxdWVzdFNxbEdyaWQgPSBuZXcgR3JpZCgnc3FsX3JlcXVlc3QnKTtcbiAgICByZXF1ZXN0U3FsR3JpZC5hZGRFeHRlbnNpb24obmV3IFJlbG9hZExpc3RBY3Rpb25FeHRlbnNpb24oKSk7XG4gICAgcmVxdWVzdFNxbEdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBFeHBvcnRUb1NxbE1hbmFnZXJFeHRlbnNpb24oKSk7XG4gICAgcmVxdWVzdFNxbEdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBGaWx0ZXJzUmVzZXRFeHRlbnNpb24oKSk7XG4gICAgcmVxdWVzdFNxbEdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBTb3J0aW5nRXh0ZW5zaW9uKCkpO1xuICAgIHJlcXVlc3RTcWxHcmlkLmFkZEV4dGVuc2lvbihuZXcgTGlua1Jvd0FjdGlvbkV4dGVuc2lvbigpKTtcbiAgICByZXF1ZXN0U3FsR3JpZC5hZGRFeHRlbnNpb24obmV3IFN1Ym1pdEdyaWRFeHRlbnNpb24oKSk7XG4gICAgcmVxdWVzdFNxbEdyaWQuYWRkRXh0ZW5zaW9uKG5ldyBTdWJtaXRCdWxrRXh0ZW5zaW9uKCkpO1xuICAgIHJlcXVlc3RTcWxHcmlkLmFkZEV4dGVuc2lvbihuZXcgQnVsa0FjdGlvbkNoZWNrYm94RXh0ZW5zaW9uKCkpO1xuICAgIHJlcXVlc3RTcWxHcmlkLmFkZEV4dGVuc2lvbihuZXcgRmlsdGVyc1N1Ym1pdEJ1dHRvbkVuYWJsZXJFeHRlbnNpb24oKSk7XG5cbiAgICAkKGRvY3VtZW50KS5vbignY2hhbmdlJywgJy5qcy1kYi10YWJsZXMtc2VsZWN0JywgKCkgPT4gdGhpcy5yZWxvYWREYlRhYmxlQ29sdW1ucygpKTtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCAnLmpzLWFkZC1kYi10YWJsZS10by1xdWVyeS1idG4nLCAoZXZlbnQpID0+IHRoaXMuYWRkRGJUYWJsZVRvUXVlcnkoZXZlbnQpKTtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCAnLmpzLWFkZC1kYi10YWJsZS1jb2x1bW4tdG8tcXVlcnktYnRuJywgKGV2ZW50KSA9PiB0aGlzLmFkZERiVGFibGVDb2x1bW5Ub1F1ZXJ5KGV2ZW50KSk7XG4gIH1cblxuICAvKipcbiAgICogUmVsb2FkIGRhdGFiYXNlIHRhYmxlIGNvbHVtbnNcbiAgICovXG4gIHJlbG9hZERiVGFibGVDb2x1bW5zKCkge1xuICAgIGNvbnN0ICRzZWxlY3RlZE9wdGlvbiA9ICQoJy5qcy1kYi10YWJsZXMtc2VsZWN0JykuZmluZCgnb3B0aW9uOnNlbGVjdGVkJyk7XG4gICAgY29uc3QgJHRhYmxlID0gJCgnLmpzLXRhYmxlLWNvbHVtbnMnKTtcblxuICAgICQuYWpheCgkc2VsZWN0ZWRPcHRpb24uZGF0YSgndGFibGUtY29sdW1ucy11cmwnKSlcbiAgICAgIC50aGVuKChyZXNwb25zZSkgPT4ge1xuICAgICAgICAkKCcuanMtdGFibGUtYWxlcnQnKS5hZGRDbGFzcygnZC1ub25lJyk7XG5cbiAgICAgICAgY29uc3QgY29sdW1ucyA9IHJlc3BvbnNlLmNvbHVtbnM7XG5cbiAgICAgICAgJHRhYmxlLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgICAgICAgJHRhYmxlLmZpbmQoJ3Rib2R5JykuZW1wdHkoKTtcblxuICAgICAgICBjb2x1bW5zLmZvckVhY2goKGNvbHVtbikgPT4ge1xuICAgICAgICAgIGNvbnN0ICRyb3cgPSAkKCc8dHI+JylcbiAgICAgICAgICAgIC5hcHBlbmQoJCgnPHRkPicpLmh0bWwoY29sdW1uLm5hbWUpKVxuICAgICAgICAgICAgLmFwcGVuZCgkKCc8dGQ+JykuaHRtbChjb2x1bW4udHlwZSkpXG4gICAgICAgICAgICAuYXBwZW5kKCQoJzx0ZD4nKS5hZGRDbGFzcygndGV4dC1yaWdodCcpXG4gICAgICAgICAgICAgIC5hcHBlbmQoJCgnPGJ1dHRvbj4nKVxuICAgICAgICAgICAgICAgIC5hZGRDbGFzcygnYnRuIGJ0bi1zbSBidG4tb3V0bGluZS1zZWNvbmRhcnkganMtYWRkLWRiLXRhYmxlLWNvbHVtbi10by1xdWVyeS1idG4nKVxuICAgICAgICAgICAgICAgIC5hdHRyKCdkYXRhLWNvbHVtbicsIGNvbHVtbi5uYW1lKVxuICAgICAgICAgICAgICAgIC5odG1sKCR0YWJsZS5kYXRhKCdhY3Rpb24tYnRuJykpXG4gICAgICAgICAgICAgIClcbiAgICAgICAgICAgICk7XG5cbiAgICAgICAgICAkdGFibGUuZmluZCgndGJvZHknKS5hcHBlbmQoJHJvdyk7XG4gICAgICAgIH0pO1xuICAgICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogQWRkIHNlbGVjdGVkIGRhdGFiYXNlIHRhYmxlIG5hbWUgdG8gU1FMIHF1ZXJ5IGlucHV0XG4gICAqXG4gICAqIEBwYXJhbSBldmVudFxuICAgKi9cbiAgYWRkRGJUYWJsZVRvUXVlcnkoZXZlbnQpIHtcbiAgICBjb25zdCAkc2VsZWN0ZWRPcHRpb24gPSAkKCcuanMtZGItdGFibGVzLXNlbGVjdCcpLmZpbmQoJ29wdGlvbjpzZWxlY3RlZCcpO1xuXG4gICAgaWYgKCRzZWxlY3RlZE9wdGlvbi5sZW5ndGggPT09IDApIHtcbiAgICAgIGFsZXJ0KCQoZXZlbnQudGFyZ2V0KS5kYXRhKCdjaG9vc2UtdGFibGUtbWVzc2FnZScpKTtcblxuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIHRoaXMuYWRkVG9RdWVyeSgkc2VsZWN0ZWRPcHRpb24udmFsKCkpO1xuICB9XG5cbiAgLyoqXG4gICAqIEFkZCB0YWJsZSBjb2x1bW4gdG8gU1FMIHF1ZXJ5IGlucHV0XG4gICAqXG4gICAqIEBwYXJhbSBldmVudFxuICAgKi9cbiAgYWRkRGJUYWJsZUNvbHVtblRvUXVlcnkoZXZlbnQpIHtcbiAgICB0aGlzLmFkZFRvUXVlcnkoJChldmVudC50YXJnZXQpLmRhdGEoJ2NvbHVtbicpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBBZGQgZGF0YSB0byBTUUwgcXVlcnkgaW5wdXRcbiAgICpcbiAgICogQHBhcmFtIHtTdHJpbmd9IGRhdGFcbiAgICovXG4gIGFkZFRvUXVlcnkoZGF0YSkge1xuICAgIGNvbnN0ICRxdWVyeUlucHV0ID0gJCgnI3NxbF9yZXF1ZXN0X3NxbCcpO1xuICAgICRxdWVyeUlucHV0LnZhbCgkcXVlcnlJbnB1dC52YWwoKSArICcgJyArIGRhdGEpO1xuICB9XG59XG5cbiQoZG9jdW1lbnQpLnJlYWR5KCgpID0+IHtcbiAgbmV3IFNxbE1hbmFnZXJQYWdlKCk7XG59KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL3NxbC1tYW5hZ2VyL2luZGV4LmpzIl0sInNvdXJjZVJvb3QiOiIifQ==