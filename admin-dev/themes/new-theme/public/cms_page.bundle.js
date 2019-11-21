window["cms_page"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 348);
/******/ })
/************************************************************************/
/******/ ({

/***/ 348:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _grid = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/grid\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _grid2 = _interopRequireDefault(_grid);

var _sortingExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/sorting-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _sortingExtension2 = _interopRequireDefault(_sortingExtension);

var _submitRowActionExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/action/row/submit-row-action-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _submitRowActionExtension2 = _interopRequireDefault(_submitRowActionExtension);

var _filtersResetExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/filters-reset-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _filtersResetExtension2 = _interopRequireDefault(_filtersResetExtension);

var _reloadListExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/reload-list-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _reloadListExtension2 = _interopRequireDefault(_reloadListExtension);

var _exportToSqlManagerExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/export-to-sql-manager-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _exportToSqlManagerExtension2 = _interopRequireDefault(_exportToSqlManagerExtension);

var _linkRowActionExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/link-row-action-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _linkRowActionExtension2 = _interopRequireDefault(_linkRowActionExtension);

var _submitBulkActionExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/submit-bulk-action-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _submitBulkActionExtension2 = _interopRequireDefault(_submitBulkActionExtension);

var _bulkActionCheckboxExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/bulk-action-checkbox-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _bulkActionCheckboxExtension2 = _interopRequireDefault(_bulkActionCheckboxExtension);

var _columnTogglingExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/column-toggling-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _columnTogglingExtension2 = _interopRequireDefault(_columnTogglingExtension);

var _positionExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/position-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _positionExtension2 = _interopRequireDefault(_positionExtension);

var _choiceTree = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/form/choice-tree\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _choiceTree2 = _interopRequireDefault(_choiceTree);

var _translatableInput = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/translatable-input\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _translatableInput2 = _interopRequireDefault(_translatableInput);

var _textToLinkRewriteCopier = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/text-to-link-rewrite-copier\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _textToLinkRewriteCopier2 = _interopRequireDefault(_textToLinkRewriteCopier);

var _filtersSubmitButtonEnablerExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/grid/extension/filters-submit-button-enabler-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _filtersSubmitButtonEnablerExtension2 = _interopRequireDefault(_filtersSubmitButtonEnablerExtension);

var _taggableField = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/taggable-field\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _taggableField2 = _interopRequireDefault(_taggableField);

var _showcaseCard = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/showcase-card/showcase-card\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _showcaseCard2 = _interopRequireDefault(_showcaseCard);

var _showcaseCardCloseExtension = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/showcase-card/extension/showcase-card-close-extension\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _showcaseCardCloseExtension2 = _interopRequireDefault(_showcaseCardCloseExtension);

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
  var cmsCategory = new _grid2.default('cms_page_category');

  cmsCategory.addExtension(new _reloadListExtension2.default());
  cmsCategory.addExtension(new _exportToSqlManagerExtension2.default());
  cmsCategory.addExtension(new _filtersResetExtension2.default());
  cmsCategory.addExtension(new _sortingExtension2.default());
  cmsCategory.addExtension(new _linkRowActionExtension2.default());
  cmsCategory.addExtension(new _submitBulkActionExtension2.default());
  cmsCategory.addExtension(new _bulkActionCheckboxExtension2.default());
  cmsCategory.addExtension(new _submitRowActionExtension2.default());
  cmsCategory.addExtension(new _columnTogglingExtension2.default());
  cmsCategory.addExtension(new _positionExtension2.default());
  cmsCategory.addExtension(new _filtersSubmitButtonEnablerExtension2.default());

  var translatorInput = new _translatableInput2.default();

  (0, _textToLinkRewriteCopier2.default)({
    sourceElementSelector: 'input[name^="cms_page_category[name]"]',
    destinationElementSelector: translatorInput.localeInputSelector + ':not(.d-none) input[name^="cms_page_category[friendly_url]"]'
  });

  new _choiceTree2.default('#cms_page_category_parent_category');

  var shopChoiceTree = new _choiceTree2.default('#cms_page_category_shop_association');
  shopChoiceTree.enableAutoCheckChildren();

  new _taggableField2.default({
    tokenFieldSelector: 'input[name^="cms_page_category[meta_keywords]"]',
    options: {
      createTokensOnBlur: true
    }
  });

  var cmsGrid = new _grid2.default('cms_page');
  cmsGrid.addExtension(new _reloadListExtension2.default());
  cmsGrid.addExtension(new _exportToSqlManagerExtension2.default());
  cmsGrid.addExtension(new _filtersResetExtension2.default());
  cmsGrid.addExtension(new _sortingExtension2.default());
  cmsGrid.addExtension(new _columnTogglingExtension2.default());
  cmsGrid.addExtension(new _bulkActionCheckboxExtension2.default());
  cmsGrid.addExtension(new _submitBulkActionExtension2.default());
  cmsGrid.addExtension(new _submitRowActionExtension2.default());
  cmsGrid.addExtension(new _positionExtension2.default());
  cmsGrid.addExtension(new _filtersSubmitButtonEnablerExtension2.default());
  cmsGrid.addExtension(new _linkRowActionExtension2.default());

  var helperBlock = new _showcaseCard2.default('cms-pages-showcase-card');
  helperBlock.addExtension(new _showcaseCardCloseExtension2.default());
});

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgOTNlN2NjMWY2YWNkMjQxMGYyNDI/OGQ2NioqKioqIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL2Ntcy1wYWdlL2luZGV4LmpzIl0sIm5hbWVzIjpbIiQiLCJ3aW5kb3ciLCJjbXNDYXRlZ29yeSIsIkdyaWQiLCJhZGRFeHRlbnNpb24iLCJSZWxvYWRMaXN0QWN0aW9uRXh0ZW5zaW9uIiwiRXhwb3J0VG9TcWxNYW5hZ2VyRXh0ZW5zaW9uIiwiRmlsdGVyc1Jlc2V0RXh0ZW5zaW9uIiwiU29ydGluZ0V4dGVuc2lvbiIsIkxpbmtSb3dBY3Rpb25FeHRlbnNpb24iLCJTdWJtaXRCdWxrRXh0ZW5zaW9uIiwiQnVsa0FjdGlvbkNoZWNrYm94RXh0ZW5zaW9uIiwiU3VibWl0Um93QWN0aW9uRXh0ZW5zaW9uIiwiQ29sdW1uVG9nZ2xpbmdFeHRlbnNpb24iLCJQb3NpdGlvbkV4dGVuc2lvbiIsIkZpbHRlcnNTdWJtaXRCdXR0b25FbmFibGVyRXh0ZW5zaW9uIiwidHJhbnNsYXRvcklucHV0IiwiVHJhbnNsYXRhYmxlSW5wdXQiLCJzb3VyY2VFbGVtZW50U2VsZWN0b3IiLCJkZXN0aW5hdGlvbkVsZW1lbnRTZWxlY3RvciIsImxvY2FsZUlucHV0U2VsZWN0b3IiLCJDaG9pY2VUcmVlIiwic2hvcENob2ljZVRyZWUiLCJlbmFibGVBdXRvQ2hlY2tDaGlsZHJlbiIsIlRhZ2dhYmxlRmllbGQiLCJ0b2tlbkZpZWxkU2VsZWN0b3IiLCJvcHRpb25zIiwiY3JlYXRlVG9rZW5zT25CbHVyIiwiY21zR3JpZCIsImhlbHBlckJsb2NrIiwiU2hvd2Nhc2VDYXJkIiwiU2hvd2Nhc2VDYXJkQ2xvc2VFeHRlbnNpb24iXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNB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QUFpQix5QkFBakIsQ0FBcEI7QUFDQUQsY0FBWXpCLFlBQVosQ0FBeUIsSUFBSTJCLG9DQUFKLEVBQXpCO0FBQ0QsQ0FsREQsRSIsImZpbGUiOiJjbXNfcGFnZS5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBpZGVudGl0eSBmdW5jdGlvbiBmb3IgY2FsbGluZyBoYXJtb255IGltcG9ydHMgd2l0aCB0aGUgY29ycmVjdCBjb250ZXh0XG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmkgPSBmdW5jdGlvbih2YWx1ZSkgeyByZXR1cm4gdmFsdWU7IH07XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDM0OCk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gd2VicGFjay9ib290c3RyYXAgOTNlN2NjMWY2YWNkMjQxMGYyNDIiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgR3JpZCBmcm9tICdAY29tcG9uZW50cy9ncmlkL2dyaWQnO1xuaW1wb3J0IFNvcnRpbmdFeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vc29ydGluZy1leHRlbnNpb24nO1xuaW1wb3J0IFN1Ym1pdFJvd0FjdGlvbkV4dGVuc2lvbiBmcm9tICdAY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9hY3Rpb24vcm93L3N1Ym1pdC1yb3ctYWN0aW9uLWV4dGVuc2lvbic7XG5pbXBvcnQgRmlsdGVyc1Jlc2V0RXh0ZW5zaW9uIGZyb20gJ0Bjb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2ZpbHRlcnMtcmVzZXQtZXh0ZW5zaW9uJztcbmltcG9ydCBSZWxvYWRMaXN0QWN0aW9uRXh0ZW5zaW9uIGZyb20gJ0Bjb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3JlbG9hZC1saXN0LWV4dGVuc2lvbic7XG5pbXBvcnQgRXhwb3J0VG9TcWxNYW5hZ2VyRXh0ZW5zaW9uIGZyb20gJ0Bjb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2V4cG9ydC10by1zcWwtbWFuYWdlci1leHRlbnNpb24nO1xuaW1wb3J0IExpbmtSb3dBY3Rpb25FeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vbGluay1yb3ctYWN0aW9uLWV4dGVuc2lvbic7XG5pbXBvcnQgU3VibWl0QnVsa0V4dGVuc2lvbiBmcm9tICdAY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9zdWJtaXQtYnVsay1hY3Rpb24tZXh0ZW5zaW9uJztcbmltcG9ydCBCdWxrQWN0aW9uQ2hlY2tib3hFeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYnVsay1hY3Rpb24tY2hlY2tib3gtZXh0ZW5zaW9uJztcbmltcG9ydCBDb2x1bW5Ub2dnbGluZ0V4dGVuc2lvbiBmcm9tICdAY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9jb2x1bW4tdG9nZ2xpbmctZXh0ZW5zaW9uJztcbmltcG9ydCBQb3NpdGlvbkV4dGVuc2lvbiBmcm9tICdAY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9wb3NpdGlvbi1leHRlbnNpb24nO1xuaW1wb3J0IENob2ljZVRyZWUgZnJvbSAnQGNvbXBvbmVudHMvZm9ybS9jaG9pY2UtdHJlZSc7XG5pbXBvcnQgVHJhbnNsYXRhYmxlSW5wdXQgZnJvbSAnQGNvbXBvbmVudHMvdHJhbnNsYXRhYmxlLWlucHV0JztcbmltcG9ydCB0ZXh0VG9MaW5rUmV3cml0ZUNvcGllciBmcm9tICdAY29tcG9uZW50cy90ZXh0LXRvLWxpbmstcmV3cml0ZS1jb3BpZXInO1xuaW1wb3J0IEZpbHRlcnNTdWJtaXRCdXR0b25FbmFibGVyRXh0ZW5zaW9uXG4gIGZyb20gJ0Bjb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2ZpbHRlcnMtc3VibWl0LWJ1dHRvbi1lbmFibGVyLWV4dGVuc2lvbic7XG5pbXBvcnQgVGFnZ2FibGVGaWVsZCBmcm9tICdAY29tcG9uZW50cy90YWdnYWJsZS1maWVsZCc7XG5pbXBvcnQgU2hvd2Nhc2VDYXJkIGZyb20gJ0Bjb21wb25lbnRzL3Nob3djYXNlLWNhcmQvc2hvd2Nhc2UtY2FyZCc7XG5pbXBvcnQgU2hvd2Nhc2VDYXJkQ2xvc2VFeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvc2hvd2Nhc2UtY2FyZC9leHRlbnNpb24vc2hvd2Nhc2UtY2FyZC1jbG9zZS1leHRlbnNpb24nO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbiQoKCkgPT4ge1xuICBjb25zdCBjbXNDYXRlZ29yeSA9IG5ldyBHcmlkKCdjbXNfcGFnZV9jYXRlZ29yeScpO1xuXG4gIGNtc0NhdGVnb3J5LmFkZEV4dGVuc2lvbihuZXcgUmVsb2FkTGlzdEFjdGlvbkV4dGVuc2lvbigpKTtcbiAgY21zQ2F0ZWdvcnkuYWRkRXh0ZW5zaW9uKG5ldyBFeHBvcnRUb1NxbE1hbmFnZXJFeHRlbnNpb24oKSk7XG4gIGNtc0NhdGVnb3J5LmFkZEV4dGVuc2lvbihuZXcgRmlsdGVyc1Jlc2V0RXh0ZW5zaW9uKCkpO1xuICBjbXNDYXRlZ29yeS5hZGRFeHRlbnNpb24obmV3IFNvcnRpbmdFeHRlbnNpb24oKSk7XG4gIGNtc0NhdGVnb3J5LmFkZEV4dGVuc2lvbihuZXcgTGlua1Jvd0FjdGlvbkV4dGVuc2lvbigpKTtcbiAgY21zQ2F0ZWdvcnkuYWRkRXh0ZW5zaW9uKG5ldyBTdWJtaXRCdWxrRXh0ZW5zaW9uKCkpO1xuICBjbXNDYXRlZ29yeS5hZGRFeHRlbnNpb24obmV3IEJ1bGtBY3Rpb25DaGVja2JveEV4dGVuc2lvbigpKTtcbiAgY21zQ2F0ZWdvcnkuYWRkRXh0ZW5zaW9uKG5ldyBTdWJtaXRSb3dBY3Rpb25FeHRlbnNpb24oKSk7XG4gIGNtc0NhdGVnb3J5LmFkZEV4dGVuc2lvbihuZXcgQ29sdW1uVG9nZ2xpbmdFeHRlbnNpb24oKSk7XG4gIGNtc0NhdGVnb3J5LmFkZEV4dGVuc2lvbihuZXcgUG9zaXRpb25FeHRlbnNpb24oKSk7XG4gIGNtc0NhdGVnb3J5LmFkZEV4dGVuc2lvbihuZXcgRmlsdGVyc1N1Ym1pdEJ1dHRvbkVuYWJsZXJFeHRlbnNpb24oKSk7XG5cbiAgY29uc3QgdHJhbnNsYXRvcklucHV0ID0gbmV3IFRyYW5zbGF0YWJsZUlucHV0KCk7XG5cbiAgdGV4dFRvTGlua1Jld3JpdGVDb3BpZXIoe1xuICAgIHNvdXJjZUVsZW1lbnRTZWxlY3RvcjogJ2lucHV0W25hbWVePVwiY21zX3BhZ2VfY2F0ZWdvcnlbbmFtZV1cIl0nLFxuICAgIGRlc3RpbmF0aW9uRWxlbWVudFNlbGVjdG9yOiBgJHt0cmFuc2xhdG9ySW5wdXQubG9jYWxlSW5wdXRTZWxlY3Rvcn06bm90KC5kLW5vbmUpIGlucHV0W25hbWVePVwiY21zX3BhZ2VfY2F0ZWdvcnlbZnJpZW5kbHlfdXJsXVwiXWAsXG4gIH0pO1xuXG4gIG5ldyBDaG9pY2VUcmVlKCcjY21zX3BhZ2VfY2F0ZWdvcnlfcGFyZW50X2NhdGVnb3J5Jyk7XG5cbiAgY29uc3Qgc2hvcENob2ljZVRyZWUgPSBuZXcgQ2hvaWNlVHJlZSgnI2Ntc19wYWdlX2NhdGVnb3J5X3Nob3BfYXNzb2NpYXRpb24nKTtcbiAgc2hvcENob2ljZVRyZWUuZW5hYmxlQXV0b0NoZWNrQ2hpbGRyZW4oKTtcblxuXG4gIG5ldyBUYWdnYWJsZUZpZWxkKHtcbiAgICB0b2tlbkZpZWxkU2VsZWN0b3I6ICdpbnB1dFtuYW1lXj1cImNtc19wYWdlX2NhdGVnb3J5W21ldGFfa2V5d29yZHNdXCJdJyxcbiAgICBvcHRpb25zOiB7XG4gICAgICBjcmVhdGVUb2tlbnNPbkJsdXI6IHRydWUsXG4gICAgfSxcbiAgfSk7XG5cbiAgY29uc3QgY21zR3JpZCA9IG5ldyBHcmlkKCdjbXNfcGFnZScpO1xuICBjbXNHcmlkLmFkZEV4dGVuc2lvbihuZXcgUmVsb2FkTGlzdEFjdGlvbkV4dGVuc2lvbigpKTtcbiAgY21zR3JpZC5hZGRFeHRlbnNpb24obmV3IEV4cG9ydFRvU3FsTWFuYWdlckV4dGVuc2lvbigpKTtcbiAgY21zR3JpZC5hZGRFeHRlbnNpb24obmV3IEZpbHRlcnNSZXNldEV4dGVuc2lvbigpKTtcbiAgY21zR3JpZC5hZGRFeHRlbnNpb24obmV3IFNvcnRpbmdFeHRlbnNpb24oKSk7XG4gIGNtc0dyaWQuYWRkRXh0ZW5zaW9uKG5ldyBDb2x1bW5Ub2dnbGluZ0V4dGVuc2lvbigpKTtcbiAgY21zR3JpZC5hZGRFeHRlbnNpb24obmV3IEJ1bGtBY3Rpb25DaGVja2JveEV4dGVuc2lvbigpKTtcbiAgY21zR3JpZC5hZGRFeHRlbnNpb24obmV3IFN1Ym1pdEJ1bGtFeHRlbnNpb24oKSk7XG4gIGNtc0dyaWQuYWRkRXh0ZW5zaW9uKG5ldyBTdWJtaXRSb3dBY3Rpb25FeHRlbnNpb24oKSk7XG4gIGNtc0dyaWQuYWRkRXh0ZW5zaW9uKG5ldyBQb3NpdGlvbkV4dGVuc2lvbigpKTtcbiAgY21zR3JpZC5hZGRFeHRlbnNpb24obmV3IEZpbHRlcnNTdWJtaXRCdXR0b25FbmFibGVyRXh0ZW5zaW9uKCkpO1xuICBjbXNHcmlkLmFkZEV4dGVuc2lvbihuZXcgTGlua1Jvd0FjdGlvbkV4dGVuc2lvbigpKTtcblxuICBjb25zdCBoZWxwZXJCbG9jayA9IG5ldyBTaG93Y2FzZUNhcmQoJ2Ntcy1wYWdlcy1zaG93Y2FzZS1jYXJkJyk7XG4gIGhlbHBlckJsb2NrLmFkZEV4dGVuc2lvbihuZXcgU2hvd2Nhc2VDYXJkQ2xvc2VFeHRlbnNpb24oKSk7XG59KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL2Ntcy1wYWdlL2luZGV4LmpzIl0sInNvdXJjZVJvb3QiOiIifQ==