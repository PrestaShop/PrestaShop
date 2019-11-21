window["category"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 346);
/******/ })
/************************************************************************/
/******/ ({

/***/ 346:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _grid = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/grid\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _grid2 = _interopRequireDefault(_grid);

var _filtersResetExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/filters-reset-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _filtersResetExtension2 = _interopRequireDefault(_filtersResetExtension);

var _sortingExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/sorting-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _sortingExtension2 = _interopRequireDefault(_sortingExtension);

var _exportToSqlManagerExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/export-to-sql-manager-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _exportToSqlManagerExtension2 = _interopRequireDefault(_exportToSqlManagerExtension);

var _reloadListExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/reload-list-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _reloadListExtension2 = _interopRequireDefault(_reloadListExtension);

var _bulkActionCheckboxExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/bulk-action-checkbox-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _bulkActionCheckboxExtension2 = _interopRequireDefault(_bulkActionCheckboxExtension);

var _submitBulkActionExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/submit-bulk-action-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _submitBulkActionExtension2 = _interopRequireDefault(_submitBulkActionExtension);

var _submitRowActionExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/action/row/submit-row-action-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _submitRowActionExtension2 = _interopRequireDefault(_submitRowActionExtension);

var _linkRowActionExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/link-row-action-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _linkRowActionExtension2 = _interopRequireDefault(_linkRowActionExtension);

var _categoryPositionExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/column/catalog/category-position-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _categoryPositionExtension2 = _interopRequireDefault(_categoryPositionExtension);

var _asyncToggleColumnExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/column/common/async-toggle-column-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _asyncToggleColumnExtension2 = _interopRequireDefault(_asyncToggleColumnExtension);

var _deleteCategoryRowActionExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/action/row/category/delete-category-row-action-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _deleteCategoryRowActionExtension2 = _interopRequireDefault(_deleteCategoryRowActionExtension);

var _deleteCategoriesBulkActionExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/action/bulk/category/delete-categories-bulk-action-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _deleteCategoriesBulkActionExtension2 = _interopRequireDefault(_deleteCategoriesBulkActionExtension);

var _translatableInput = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/translatable-input\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _translatableInput2 = _interopRequireDefault(_translatableInput);

var _choiceTable = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/choice-table\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _choiceTable2 = _interopRequireDefault(_choiceTable);

var _textToLinkRewriteCopier = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/text-to-link-rewrite-copier\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _textToLinkRewriteCopier2 = _interopRequireDefault(_textToLinkRewriteCopier);

var _choiceTree = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/form/choice-tree\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _choiceTree2 = _interopRequireDefault(_choiceTree);

var _formSubmitButton = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/form-submit-button\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _formSubmitButton2 = _interopRequireDefault(_formSubmitButton);

var _taggableField = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/taggable-field\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _taggableField2 = _interopRequireDefault(_taggableField);

var _filtersSubmitButtonEnablerExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/filters-submit-button-enabler-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _filtersSubmitButtonEnablerExtension2 = _interopRequireDefault(_filtersSubmitButtonEnablerExtension);

var _showcaseCard = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/showcase-card/showcase-card\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _showcaseCard2 = _interopRequireDefault(_showcaseCard);

var _showcaseCardCloseExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/showcase-card/extension/showcase-card-close-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _showcaseCardCloseExtension2 = _interopRequireDefault(_showcaseCardCloseExtension);

var _textWithRecommendedLengthCounter = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/form/text-with-recommended-length-counter\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _textWithRecommendedLengthCounter2 = _interopRequireDefault(_textWithRecommendedLengthCounter);

var _translatableField = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/translatable-field\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _translatableField2 = _interopRequireDefault(_translatableField);

var _tinymceEditor = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/tinymce-editor\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _tinymceEditor2 = _interopRequireDefault(_tinymceEditor);

var _index = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@app/utils/serp/index\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _index2 = _interopRequireDefault(_index);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
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

var $ = window.$;

$(function () {
  var categoriesGrid = new _grid2.default('category');

  categoriesGrid.addExtension(new _filtersResetExtension2.default());
  categoriesGrid.addExtension(new _sortingExtension2.default());
  categoriesGrid.addExtension(new _categoryPositionExtension2.default());
  categoriesGrid.addExtension(new _exportToSqlManagerExtension2.default());
  categoriesGrid.addExtension(new _reloadListExtension2.default());
  categoriesGrid.addExtension(new _bulkActionCheckboxExtension2.default());
  categoriesGrid.addExtension(new _submitBulkActionExtension2.default());
  categoriesGrid.addExtension(new _submitRowActionExtension2.default());
  categoriesGrid.addExtension(new _linkRowActionExtension2.default());
  categoriesGrid.addExtension(new _asyncToggleColumnExtension2.default());
  categoriesGrid.addExtension(new _deleteCategoryRowActionExtension2.default());
  categoriesGrid.addExtension(new _deleteCategoriesBulkActionExtension2.default());
  categoriesGrid.addExtension(new _filtersSubmitButtonEnablerExtension2.default());

  var showcaseCard = new _showcaseCard2.default('categoriesShowcaseCard');
  showcaseCard.addExtension(new _showcaseCardCloseExtension2.default());

  new _translatableField2.default();
  new _tinymceEditor2.default();
  var translatorInput = new _translatableInput2.default();
  new _choiceTable2.default();
  new _textWithRecommendedLengthCounter2.default();

  (0, _textToLinkRewriteCopier2.default)({
    sourceElementSelector: 'input[name^="category[name]"]',
    destinationElementSelector: translatorInput.localeInputSelector + ':not(.d-none) input[name^="category[link_rewrite]"]'
  });

  (0, _textToLinkRewriteCopier2.default)({
    sourceElementSelector: 'input[name^="root_category[name]"]',
    destinationElementSelector: translatorInput.localeInputSelector + ':not(.d-none) input[name^="root_category[link_rewrite]"]'
  });

  new _index2.default({
    container: '#serp-app',
    defaultTitle: 'input[name^="category[name]"]',
    watchedTitle: 'input[name^="category[meta_title]"]',
    defaultDescription: 'textarea[name^="category[description]"]',
    watchedDescription: 'textarea[name^="category[meta_description]"]',
    watchedMetaUrl: 'input[name^="category[link_rewrite]"]',
    multiLanguageInput: translatorInput.localeInputSelector + ':not(.d-none)',
    multiLanguageItem: translatorInput.localeItemSelector
  }, $('#serp-app').data('category-url'));

  new _formSubmitButton2.default();

  new _taggableField2.default({
    tokenFieldSelector: 'input.js-taggable-field',
    options: {
      createTokensOnBlur: true
    }
  });

  new _choiceTree2.default('#category_id_parent');
  new _choiceTree2.default('#category_shop_association').enableAutoCheckChildren();

  new _choiceTree2.default('#root_category_id_parent');
  new _choiceTree2.default('#root_category_shop_association').enableAutoCheckChildren();
});

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgOTNlN2NjMWY2YWNkMjQxMGYyNDI/OGQ2NioqKiIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9jYXRlZ29yeS9pbmRleC5qcyJdLCJuYW1lcyI6WyIkIiwid2luZG93IiwiY2F0ZWdvcmllc0dyaWQiLCJHcmlkIiwiYWRkRXh0ZW5zaW9uIiwiRmlsdGVyc1Jlc2V0RXh0ZW5zaW9uIiwiU29ydGluZ0V4dGVuc2lvbiIsIkNhdGVnb3J5UG9zaXRpb25FeHRlbnNpb24iLCJFeHBvcnRUb1NxbE1hbmFnZXJFeHRlbnNpb24iLCJSZWxvYWRMaXN0RXh0ZW5zaW9uIiwiQnVsa0FjdGlvbkNoZWNrYm94RXh0ZW5zaW9uIiwiU3VibWl0QnVsa0V4dGVuc2lvbiIsIlN1Ym1pdFJvd0FjdGlvbkV4dGVuc2lvbiIsIkxpbmtSb3dBY3Rpb25FeHRlbnNpb24iLCJBc3luY1RvZ2dsZUNvbHVtbkV4dGVuc2lvbiIsIkRlbGV0ZUNhdGVnb3J5Um93QWN0aW9uRXh0ZW5zaW9uIiwiRGVsZXRlQ2F0ZWdvcmllc0J1bGtBY3Rpb25FeHRlbnNpb24iLCJGaWx0ZXJzU3VibWl0QnV0dG9uRW5hYmxlckV4dGVuc2lvbiIsInNob3djYXNlQ2FyZCIsIlNob3djYXNlQ2FyZCIsIlNob3djYXNlQ2FyZENsb3NlRXh0ZW5zaW9uIiwiVHJhbnNsYXRhYmxlRmllbGQiLCJUaW55TUNFRWRpdG9yIiwidHJhbnNsYXRvcklucHV0IiwiVHJhbnNsYXRhYmxlSW5wdXQiLCJDaG9pY2VUYWJsZSIsIlRleHRXaXRoUmVjb21tZW5kZWRMZW5ndGhDb3VudGVyIiwic291cmNlRWxlbWVudFNlbGVjdG9yIiwiZGVzdGluYXRpb25FbGVtZW50U2VsZWN0b3IiLCJsb2NhbGVJbnB1dFNlbGVjdG9yIiwiU2VycCIsImNvbnRhaW5lciIsImRlZmF1bHRUaXRsZSIsIndhdGNoZWRUaXRsZSIsImRlZmF1bHREZXNjcmlwdGlvbiIsIndhdGNoZWREZXNjcmlwdGlvbiIsIndhdGNoZWRNZXRhVXJsIiwibXVsdGlMYW5ndWFnZUlucHV0IiwibXVsdGlMYW5ndWFnZUl0ZW0iLCJsb2NhbGVJdGVtU2VsZWN0b3IiLCJkYXRhIiwiRm9ybVN1Ym1pdEJ1dHRvbiIsIlRhZ2dhYmxlRmllbGQiLCJ0b2tlbkZpZWxkU2VsZWN0b3IiLCJvcHRpb25zIiwiY3JlYXRlVG9rZW5zT25CbHVyIiwiQ2hvaWNlVHJlZSIsImVuYWJsZUF1dG9DaGVja0NoaWxkcmVuIl0sIm1hcHBpbmdzIjoiOztBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQSxtREFBMkMsY0FBYzs7QUFFekQ7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxhQUFLO0FBQ0w7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxtQ0FBMkIsMEJBQTBCLEVBQUU7QUFDdkQseUNBQWlDLGVBQWU7QUFDaEQ7QUFDQTtBQUNBOztBQUVBO0FBQ0EsOERBQXNELCtEQUErRDs7QUFFckg7QUFDQTs7QUFFQTtBQUNBOzs7Ozs7Ozs7OztBQ3ZDQTs7OztBQUNBOzs7O0FBQ0E7Ozs7QUFDQTs7OztBQUNBOzs7O0FBQ0E7Ozs7QUFDQTs7OztBQUNBOzs7O0FBQ0E7Ozs7QUFDQTs7OztBQUNBOzs7O0FBQ0E7Ozs7QUFDQTs7OztBQUNBOzs7O0FBQ0E7Ozs7QUFDQTs7OztBQUNBOzs7O0FBQ0E7Ozs7QUFDQTs7OztBQUNBOzs7O0FBRUE7Ozs7QUFDQTs7OztBQUNBOzs7O0FBQ0E7Ozs7QUFDQTs7OztBQUNBOzs7Ozs7QUFuREE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUFxREEsSUFBTUEsSUFBSUMsT0FBT0QsQ0FBakI7O0FBRUFBLEVBQUUsWUFBTTtBQUNOLE1BQU1FLGlCQUFpQixJQUFJQyxjQUFKLENBQVMsVUFBVCxDQUF2Qjs7QUFFQUQsaUJBQWVFLFlBQWYsQ0FBNEIsSUFBSUMsK0JBQUosRUFBNUI7QUFDQUgsaUJBQWVFLFlBQWYsQ0FBNEIsSUFBSUUsMEJBQUosRUFBNUI7QUFDQUosaUJBQWVFLFlBQWYsQ0FBNEIsSUFBSUcsbUNBQUosRUFBNUI7QUFDQUwsaUJBQWVFLFlBQWYsQ0FBNEIsSUFBSUkscUNBQUosRUFBNUI7QUFDQU4saUJBQWVFLFlBQWYsQ0FBNEIsSUFBSUssNkJBQUosRUFBNUI7QUFDQVAsaUJBQWVFLFlBQWYsQ0FBNEIsSUFBSU0scUNBQUosRUFBNUI7QUFDQVIsaUJBQWVFLFlBQWYsQ0FBNEIsSUFBSU8sbUNBQUosRUFBNUI7QUFDQVQsaUJBQWVFLFlBQWYsQ0FBNEIsSUFBSVEsa0NBQUosRUFBNUI7QUFDQVYsaUJBQWVFLFlBQWYsQ0FBNEIsSUFBSVMsZ0NBQUosRUFBNUI7QUFDQVgsaUJBQWVFLFlBQWYsQ0FBNEIsSUFBSVUsb0NBQUosRUFBNUI7QUFDQVosaUJBQWVFLFlBQWYsQ0FBNEIsSUFBSVcsMENBQUosRUFBNUI7QUFDQWIsaUJBQWVFLFlBQWYsQ0FBNEIsSUFBSVksNkNBQUosRUFBNUI7QUFDQWQsaUJBQWVFLFlBQWYsQ0FBNEIsSUFBSWEsNkNBQUosRUFBNUI7O0FBRUEsTUFBTUMsZUFBZSxJQUFJQyxzQkFBSixDQUFpQix3QkFBakIsQ0FBckI7QUFDQUQsZUFBYWQsWUFBYixDQUEwQixJQUFJZ0Isb0NBQUosRUFBMUI7O0FBRUEsTUFBSUMsMkJBQUo7QUFDQSxNQUFJQyx1QkFBSjtBQUNBLE1BQU1DLGtCQUFrQixJQUFJQywyQkFBSixFQUF4QjtBQUNBLE1BQUlDLHFCQUFKO0FBQ0EsTUFBSUMsMENBQUo7O0FBRUEseUNBQXdCO0FBQ3RCQywyQkFBdUIsK0JBREQ7QUFFdEJDLGdDQUErQkwsZ0JBQWdCTSxtQkFBL0M7QUFGc0IsR0FBeEI7O0FBS0EseUNBQXdCO0FBQ3RCRiwyQkFBdUIsb0NBREQ7QUFFdEJDLGdDQUErQkwsZ0JBQWdCTSxtQkFBL0M7QUFGc0IsR0FBeEI7O0FBS0EsTUFBSUMsZUFBSixDQUNFO0FBQ0VDLGVBQVcsV0FEYjtBQUVFQyxrQkFBYywrQkFGaEI7QUFHRUMsa0JBQWMscUNBSGhCO0FBSUVDLHdCQUFvQix5Q0FKdEI7QUFLRUMsd0JBQW9CLDhDQUx0QjtBQU1FQyxvQkFBZ0IsdUNBTmxCO0FBT0VDLHdCQUF1QmQsZ0JBQWdCTSxtQkFBdkMsa0JBUEY7QUFRRVMsdUJBQW1CZixnQkFBZ0JnQjtBQVJyQyxHQURGLEVBV0V2QyxFQUFFLFdBQUYsRUFBZXdDLElBQWYsQ0FBb0IsY0FBcEIsQ0FYRjs7QUFjQSxNQUFJQywwQkFBSjs7QUFFQSxNQUFJQyx1QkFBSixDQUFrQjtBQUNoQkMsd0JBQW9CLHlCQURKO0FBRWhCQyxhQUFTO0FBQ1BDLDBCQUFvQjtBQURiO0FBRk8sR0FBbEI7O0FBT0EsTUFBSUMsb0JBQUosQ0FBZSxxQkFBZjtBQUNBLE1BQUlBLG9CQUFKLENBQWUsNEJBQWYsRUFBNkNDLHVCQUE3Qzs7QUFFQSxNQUFJRCxvQkFBSixDQUFlLDBCQUFmO0FBQ0EsTUFBSUEsb0JBQUosQ0FBZSxpQ0FBZixFQUFrREMsdUJBQWxEO0FBQ0QsQ0FoRUQsRSIsImZpbGUiOiJjYXRlZ29yeS5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBpZGVudGl0eSBmdW5jdGlvbiBmb3IgY2FsbGluZyBoYXJtb255IGltcG9ydHMgd2l0aCB0aGUgY29ycmVjdCBjb250ZXh0XG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmkgPSBmdW5jdGlvbih2YWx1ZSkgeyByZXR1cm4gdmFsdWU7IH07XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDM0Nik7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gd2VicGFjay9ib290c3RyYXAgOTNlN2NjMWY2YWNkMjQxMGYyNDIiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgR3JpZCBmcm9tICdAY29tcG9uZW50cy9ncmlkL2dyaWQnO1xuaW1wb3J0IEZpbHRlcnNSZXNldEV4dGVuc2lvbiBmcm9tICdAY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXJlc2V0LWV4dGVuc2lvbic7XG5pbXBvcnQgU29ydGluZ0V4dGVuc2lvbiBmcm9tICdAY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9zb3J0aW5nLWV4dGVuc2lvbic7XG5pbXBvcnQgRXhwb3J0VG9TcWxNYW5hZ2VyRXh0ZW5zaW9uIGZyb20gJ0Bjb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2V4cG9ydC10by1zcWwtbWFuYWdlci1leHRlbnNpb24nO1xuaW1wb3J0IFJlbG9hZExpc3RFeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vcmVsb2FkLWxpc3QtZXh0ZW5zaW9uJztcbmltcG9ydCBCdWxrQWN0aW9uQ2hlY2tib3hFeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYnVsay1hY3Rpb24tY2hlY2tib3gtZXh0ZW5zaW9uJztcbmltcG9ydCBTdWJtaXRCdWxrRXh0ZW5zaW9uIGZyb20gJ0Bjb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3N1Ym1pdC1idWxrLWFjdGlvbi1leHRlbnNpb24nO1xuaW1wb3J0IFN1Ym1pdFJvd0FjdGlvbkV4dGVuc2lvbiBmcm9tICdAY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9hY3Rpb24vcm93L3N1Ym1pdC1yb3ctYWN0aW9uLWV4dGVuc2lvbic7XG5pbXBvcnQgTGlua1Jvd0FjdGlvbkV4dGVuc2lvbiBmcm9tICdAY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9saW5rLXJvdy1hY3Rpb24tZXh0ZW5zaW9uJztcbmltcG9ydCBDYXRlZ29yeVBvc2l0aW9uRXh0ZW5zaW9uIGZyb20gJ0Bjb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2NvbHVtbi9jYXRhbG9nL2NhdGVnb3J5LXBvc2l0aW9uLWV4dGVuc2lvbic7XG5pbXBvcnQgQXN5bmNUb2dnbGVDb2x1bW5FeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vY29sdW1uL2NvbW1vbi9hc3luYy10b2dnbGUtY29sdW1uLWV4dGVuc2lvbic7XG5pbXBvcnQgRGVsZXRlQ2F0ZWdvcnlSb3dBY3Rpb25FeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYWN0aW9uL3Jvdy9jYXRlZ29yeS9kZWxldGUtY2F0ZWdvcnktcm93LWFjdGlvbi1leHRlbnNpb24nO1xuaW1wb3J0IERlbGV0ZUNhdGVnb3JpZXNCdWxrQWN0aW9uRXh0ZW5zaW9uIGZyb20gJ0Bjb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2FjdGlvbi9idWxrL2NhdGVnb3J5L2RlbGV0ZS1jYXRlZ29yaWVzLWJ1bGstYWN0aW9uLWV4dGVuc2lvbic7XG5pbXBvcnQgVHJhbnNsYXRhYmxlSW5wdXQgZnJvbSAnQGNvbXBvbmVudHMvdHJhbnNsYXRhYmxlLWlucHV0JztcbmltcG9ydCBDaG9pY2VUYWJsZSBmcm9tICdAY29tcG9uZW50cy9jaG9pY2UtdGFibGUnO1xuaW1wb3J0IHRleHRUb0xpbmtSZXdyaXRlQ29waWVyIGZyb20gJ0Bjb21wb25lbnRzL3RleHQtdG8tbGluay1yZXdyaXRlLWNvcGllcic7XG5pbXBvcnQgQ2hvaWNlVHJlZSBmcm9tICdAY29tcG9uZW50cy9mb3JtL2Nob2ljZS10cmVlJztcbmltcG9ydCBGb3JtU3VibWl0QnV0dG9uIGZyb20gJ0Bjb21wb25lbnRzL2Zvcm0tc3VibWl0LWJ1dHRvbic7XG5pbXBvcnQgVGFnZ2FibGVGaWVsZCBmcm9tICdAY29tcG9uZW50cy90YWdnYWJsZS1maWVsZCc7XG5pbXBvcnQgRmlsdGVyc1N1Ym1pdEJ1dHRvbkVuYWJsZXJFeHRlbnNpb25cbiAgZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZmlsdGVycy1zdWJtaXQtYnV0dG9uLWVuYWJsZXItZXh0ZW5zaW9uJztcbmltcG9ydCBTaG93Y2FzZUNhcmQgZnJvbSAnQGNvbXBvbmVudHMvc2hvd2Nhc2UtY2FyZC9zaG93Y2FzZS1jYXJkJztcbmltcG9ydCBTaG93Y2FzZUNhcmRDbG9zZUV4dGVuc2lvbiBmcm9tICdAY29tcG9uZW50cy9zaG93Y2FzZS1jYXJkL2V4dGVuc2lvbi9zaG93Y2FzZS1jYXJkLWNsb3NlLWV4dGVuc2lvbic7XG5pbXBvcnQgVGV4dFdpdGhSZWNvbW1lbmRlZExlbmd0aENvdW50ZXIgZnJvbSAnQGNvbXBvbmVudHMvZm9ybS90ZXh0LXdpdGgtcmVjb21tZW5kZWQtbGVuZ3RoLWNvdW50ZXInO1xuaW1wb3J0IFRyYW5zbGF0YWJsZUZpZWxkIGZyb20gJ0Bjb21wb25lbnRzL3RyYW5zbGF0YWJsZS1maWVsZCc7XG5pbXBvcnQgVGlueU1DRUVkaXRvciBmcm9tICdAY29tcG9uZW50cy90aW55bWNlLWVkaXRvcic7XG5pbXBvcnQgU2VycCBmcm9tICdAYXBwL3V0aWxzL3NlcnAvaW5kZXgnO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbiQoKCkgPT4ge1xuICBjb25zdCBjYXRlZ29yaWVzR3JpZCA9IG5ldyBHcmlkKCdjYXRlZ29yeScpO1xuXG4gIGNhdGVnb3JpZXNHcmlkLmFkZEV4dGVuc2lvbihuZXcgRmlsdGVyc1Jlc2V0RXh0ZW5zaW9uKCkpO1xuICBjYXRlZ29yaWVzR3JpZC5hZGRFeHRlbnNpb24obmV3IFNvcnRpbmdFeHRlbnNpb24oKSk7XG4gIGNhdGVnb3JpZXNHcmlkLmFkZEV4dGVuc2lvbihuZXcgQ2F0ZWdvcnlQb3NpdGlvbkV4dGVuc2lvbigpKTtcbiAgY2F0ZWdvcmllc0dyaWQuYWRkRXh0ZW5zaW9uKG5ldyBFeHBvcnRUb1NxbE1hbmFnZXJFeHRlbnNpb24oKSk7XG4gIGNhdGVnb3JpZXNHcmlkLmFkZEV4dGVuc2lvbihuZXcgUmVsb2FkTGlzdEV4dGVuc2lvbigpKTtcbiAgY2F0ZWdvcmllc0dyaWQuYWRkRXh0ZW5zaW9uKG5ldyBCdWxrQWN0aW9uQ2hlY2tib3hFeHRlbnNpb24oKSk7XG4gIGNhdGVnb3JpZXNHcmlkLmFkZEV4dGVuc2lvbihuZXcgU3VibWl0QnVsa0V4dGVuc2lvbigpKTtcbiAgY2F0ZWdvcmllc0dyaWQuYWRkRXh0ZW5zaW9uKG5ldyBTdWJtaXRSb3dBY3Rpb25FeHRlbnNpb24oKSk7XG4gIGNhdGVnb3JpZXNHcmlkLmFkZEV4dGVuc2lvbihuZXcgTGlua1Jvd0FjdGlvbkV4dGVuc2lvbigpKTtcbiAgY2F0ZWdvcmllc0dyaWQuYWRkRXh0ZW5zaW9uKG5ldyBBc3luY1RvZ2dsZUNvbHVtbkV4dGVuc2lvbigpKTtcbiAgY2F0ZWdvcmllc0dyaWQuYWRkRXh0ZW5zaW9uKG5ldyBEZWxldGVDYXRlZ29yeVJvd0FjdGlvbkV4dGVuc2lvbigpKTtcbiAgY2F0ZWdvcmllc0dyaWQuYWRkRXh0ZW5zaW9uKG5ldyBEZWxldGVDYXRlZ29yaWVzQnVsa0FjdGlvbkV4dGVuc2lvbigpKTtcbiAgY2F0ZWdvcmllc0dyaWQuYWRkRXh0ZW5zaW9uKG5ldyBGaWx0ZXJzU3VibWl0QnV0dG9uRW5hYmxlckV4dGVuc2lvbigpKTtcblxuICBjb25zdCBzaG93Y2FzZUNhcmQgPSBuZXcgU2hvd2Nhc2VDYXJkKCdjYXRlZ29yaWVzU2hvd2Nhc2VDYXJkJyk7XG4gIHNob3djYXNlQ2FyZC5hZGRFeHRlbnNpb24obmV3IFNob3djYXNlQ2FyZENsb3NlRXh0ZW5zaW9uKCkpO1xuXG4gIG5ldyBUcmFuc2xhdGFibGVGaWVsZCgpO1xuICBuZXcgVGlueU1DRUVkaXRvcigpO1xuICBjb25zdCB0cmFuc2xhdG9ySW5wdXQgPSBuZXcgVHJhbnNsYXRhYmxlSW5wdXQoKTtcbiAgbmV3IENob2ljZVRhYmxlKCk7XG4gIG5ldyBUZXh0V2l0aFJlY29tbWVuZGVkTGVuZ3RoQ291bnRlcigpO1xuXG4gIHRleHRUb0xpbmtSZXdyaXRlQ29waWVyKHtcbiAgICBzb3VyY2VFbGVtZW50U2VsZWN0b3I6ICdpbnB1dFtuYW1lXj1cImNhdGVnb3J5W25hbWVdXCJdJyxcbiAgICBkZXN0aW5hdGlvbkVsZW1lbnRTZWxlY3RvcjogYCR7dHJhbnNsYXRvcklucHV0LmxvY2FsZUlucHV0U2VsZWN0b3J9Om5vdCguZC1ub25lKSBpbnB1dFtuYW1lXj1cImNhdGVnb3J5W2xpbmtfcmV3cml0ZV1cIl1gLFxuICB9KTtcblxuICB0ZXh0VG9MaW5rUmV3cml0ZUNvcGllcih7XG4gICAgc291cmNlRWxlbWVudFNlbGVjdG9yOiAnaW5wdXRbbmFtZV49XCJyb290X2NhdGVnb3J5W25hbWVdXCJdJyxcbiAgICBkZXN0aW5hdGlvbkVsZW1lbnRTZWxlY3RvcjogYCR7dHJhbnNsYXRvcklucHV0LmxvY2FsZUlucHV0U2VsZWN0b3J9Om5vdCguZC1ub25lKSBpbnB1dFtuYW1lXj1cInJvb3RfY2F0ZWdvcnlbbGlua19yZXdyaXRlXVwiXWAsXG4gIH0pO1xuXG4gIG5ldyBTZXJwKFxuICAgIHtcbiAgICAgIGNvbnRhaW5lcjogJyNzZXJwLWFwcCcsXG4gICAgICBkZWZhdWx0VGl0bGU6ICdpbnB1dFtuYW1lXj1cImNhdGVnb3J5W25hbWVdXCJdJyxcbiAgICAgIHdhdGNoZWRUaXRsZTogJ2lucHV0W25hbWVePVwiY2F0ZWdvcnlbbWV0YV90aXRsZV1cIl0nLFxuICAgICAgZGVmYXVsdERlc2NyaXB0aW9uOiAndGV4dGFyZWFbbmFtZV49XCJjYXRlZ29yeVtkZXNjcmlwdGlvbl1cIl0nLFxuICAgICAgd2F0Y2hlZERlc2NyaXB0aW9uOiAndGV4dGFyZWFbbmFtZV49XCJjYXRlZ29yeVttZXRhX2Rlc2NyaXB0aW9uXVwiXScsXG4gICAgICB3YXRjaGVkTWV0YVVybDogJ2lucHV0W25hbWVePVwiY2F0ZWdvcnlbbGlua19yZXdyaXRlXVwiXScsXG4gICAgICBtdWx0aUxhbmd1YWdlSW5wdXQ6IGAke3RyYW5zbGF0b3JJbnB1dC5sb2NhbGVJbnB1dFNlbGVjdG9yfTpub3QoLmQtbm9uZSlgLFxuICAgICAgbXVsdGlMYW5ndWFnZUl0ZW06IHRyYW5zbGF0b3JJbnB1dC5sb2NhbGVJdGVtU2VsZWN0b3IsXG4gICAgfSxcbiAgICAkKCcjc2VycC1hcHAnKS5kYXRhKCdjYXRlZ29yeS11cmwnKSxcbiAgKTtcblxuICBuZXcgRm9ybVN1Ym1pdEJ1dHRvbigpO1xuXG4gIG5ldyBUYWdnYWJsZUZpZWxkKHtcbiAgICB0b2tlbkZpZWxkU2VsZWN0b3I6ICdpbnB1dC5qcy10YWdnYWJsZS1maWVsZCcsXG4gICAgb3B0aW9uczoge1xuICAgICAgY3JlYXRlVG9rZW5zT25CbHVyOiB0cnVlLFxuICAgIH0sXG4gIH0pO1xuXG4gIG5ldyBDaG9pY2VUcmVlKCcjY2F0ZWdvcnlfaWRfcGFyZW50Jyk7XG4gIG5ldyBDaG9pY2VUcmVlKCcjY2F0ZWdvcnlfc2hvcF9hc3NvY2lhdGlvbicpLmVuYWJsZUF1dG9DaGVja0NoaWxkcmVuKCk7XG5cbiAgbmV3IENob2ljZVRyZWUoJyNyb290X2NhdGVnb3J5X2lkX3BhcmVudCcpO1xuICBuZXcgQ2hvaWNlVHJlZSgnI3Jvb3RfY2F0ZWdvcnlfc2hvcF9hc3NvY2lhdGlvbicpLmVuYWJsZUF1dG9DaGVja0NoaWxkcmVuKCk7XG59KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL2NhdGVnb3J5L2luZGV4LmpzIl0sInNvdXJjZVJvb3QiOiIifQ==