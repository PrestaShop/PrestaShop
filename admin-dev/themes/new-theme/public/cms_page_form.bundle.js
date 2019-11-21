window["cms_page_form"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 347);
/******/ })
/************************************************************************/
/******/ ({

/***/ 347:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _previewOpener = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/form/preview-opener\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _previewOpener2 = _interopRequireDefault(_previewOpener);

var _choiceTree = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/form/choice-tree\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _choiceTree2 = _interopRequireDefault(_choiceTree);

var _taggableField = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/taggable-field\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _taggableField2 = _interopRequireDefault(_taggableField);

var _translatableInput = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/translatable-input\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _translatableInput2 = _interopRequireDefault(_translatableInput);

var _textToLinkRewriteCopier = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"@components/text-to-link-rewrite-copier\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

var _textToLinkRewriteCopier2 = _interopRequireDefault(_textToLinkRewriteCopier);

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
  new _choiceTree2.default('#cms_page_page_category_id');

  var translatorInput = new _translatableInput2.default();

  new _index2.default({
    container: '#serp-app',
    defaultTitle: 'input[name^="cms_page[title]"]',
    watchedTitle: 'input[name^="cms_page[meta_title]"]',
    defaultDescription: 'input[name^="cms_page[description]"]',
    watchedDescription: 'input[name^="cms_page[meta_description]"]',
    watchedMetaUrl: 'input[name^="cms_page[friendly_url]"]',
    multiLanguageInput: translatorInput.localeInputSelector + ':not(.d-none)',
    multiLanguageItem: translatorInput.localeItemSelector
  }, $('#serp-app').data('cms-url'));

  new _translatableField2.default();
  new _tinymceEditor2.default();

  new _taggableField2.default({
    tokenFieldSelector: 'input.js-taggable-field',
    options: {
      createTokensOnBlur: true
    }
  });

  new _previewOpener2.default('.js-preview-url');

  (0, _textToLinkRewriteCopier2.default)({
    sourceElementSelector: 'input.js-copier-source-title',
    destinationElementSelector: translatorInput.localeInputSelector + ':not(.d-none) input.js-copier-destination-friendly-url'
  });

  new _choiceTree2.default('#cms_page_shop_association').enableAutoCheckChildren();
});

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgOTNlN2NjMWY2YWNkMjQxMGYyNDI/OGQ2NioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvY21zLXBhZ2UvZm9ybS9pbmRleC5qcyJdLCJuYW1lcyI6WyIkIiwid2luZG93IiwiQ2hvaWNlVHJlZSIsInRyYW5zbGF0b3JJbnB1dCIsIlRyYW5zbGF0YWJsZUlucHV0IiwiU2VycCIsImNvbnRhaW5lciIsImRlZmF1bHRUaXRsZSIsIndhdGNoZWRUaXRsZSIsImRlZmF1bHREZXNjcmlwdGlvbiIsIndhdGNoZWREZXNjcmlwdGlvbiIsIndhdGNoZWRNZXRhVXJsIiwibXVsdGlMYW5ndWFnZUlucHV0IiwibG9jYWxlSW5wdXRTZWxlY3RvciIsIm11bHRpTGFuZ3VhZ2VJdGVtIiwibG9jYWxlSXRlbVNlbGVjdG9yIiwiZGF0YSIsIlRyYW5zbGF0YWJsZUZpZWxkIiwiVGlueU1DRUVkaXRvciIsIlRhZ2dhYmxlRmllbGQiLCJ0b2tlbkZpZWxkU2VsZWN0b3IiLCJvcHRpb25zIiwiY3JlYXRlVG9rZW5zT25CbHVyIiwiUHJldmlld09wZW5lciIsInNvdXJjZUVsZW1lbnRTZWxlY3RvciIsImRlc3RpbmF0aW9uRWxlbWVudFNlbGVjdG9yIiwiZW5hYmxlQXV0b0NoZWNrQ2hpbGRyZW4iXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7OztBQUdBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBLG1EQUEyQyxjQUFjOztBQUV6RDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGFBQUs7QUFDTDtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLG1DQUEyQiwwQkFBMEIsRUFBRTtBQUN2RCx5Q0FBaUMsZUFBZTtBQUNoRDtBQUNBO0FBQ0E7O0FBRUE7QUFDQSw4REFBc0QsK0RBQStEOztBQUVySDtBQUNBOztBQUVBO0FBQ0E7Ozs7Ozs7Ozs7O0FDckNBOzs7O0FBQ0E7Ozs7QUFDQTs7OztBQUNBOzs7O0FBQ0E7Ozs7QUFDQTs7OztBQUNBOzs7O0FBQ0E7Ozs7OztBQWxDQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQXlCQSxJQUFNQSxJQUFJQyxPQUFPRCxDQUFqQjs7QUFXQUEsRUFBRSxZQUFNO0FBQ04sTUFBSUUsb0JBQUosQ0FBZSw0QkFBZjs7QUFFQSxNQUFNQyxrQkFBa0IsSUFBSUMsMkJBQUosRUFBeEI7O0FBRUEsTUFBSUMsZUFBSixDQUNFO0FBQ0VDLGVBQVcsV0FEYjtBQUVFQyxrQkFBYyxnQ0FGaEI7QUFHRUMsa0JBQWMscUNBSGhCO0FBSUVDLHdCQUFvQixzQ0FKdEI7QUFLRUMsd0JBQW9CLDJDQUx0QjtBQU1FQyxvQkFBZ0IsdUNBTmxCO0FBT0VDLHdCQUF1QlQsZ0JBQWdCVSxtQkFBdkMsa0JBUEY7QUFRRUMsdUJBQW1CWCxnQkFBZ0JZO0FBUnJDLEdBREYsRUFXRWYsRUFBRSxXQUFGLEVBQWVnQixJQUFmLENBQW9CLFNBQXBCLENBWEY7O0FBY0EsTUFBSUMsMkJBQUo7QUFDQSxNQUFJQyx1QkFBSjs7QUFFQSxNQUFJQyx1QkFBSixDQUFrQjtBQUNoQkMsd0JBQW9CLHlCQURKO0FBRWhCQyxhQUFTO0FBQ1BDLDBCQUFvQjtBQURiO0FBRk8sR0FBbEI7O0FBT0EsTUFBSUMsdUJBQUosQ0FBa0IsaUJBQWxCOztBQUVBLHlDQUF3QjtBQUN0QkMsMkJBQXVCLDhCQUREO0FBRXRCQyxnQ0FBK0J0QixnQkFBZ0JVLG1CQUEvQztBQUZzQixHQUF4Qjs7QUFLQSxNQUFJWCxvQkFBSixDQUFlLDRCQUFmLEVBQTZDd0IsdUJBQTdDO0FBQ0QsQ0FyQ0QsRSIsImZpbGUiOiJjbXNfcGFnZV9mb3JtLmJ1bmRsZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGlkZW50aXR5IGZ1bmN0aW9uIGZvciBjYWxsaW5nIGhhcm1vbnkgaW1wb3J0cyB3aXRoIHRoZSBjb3JyZWN0IGNvbnRleHRcbiBcdF9fd2VicGFja19yZXF1aXJlX18uaSA9IGZ1bmN0aW9uKHZhbHVlKSB7IHJldHVybiB2YWx1ZTsgfTtcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7XG4gXHRcdFx0XHRjb25maWd1cmFibGU6IGZhbHNlLFxuIFx0XHRcdFx0ZW51bWVyYWJsZTogdHJ1ZSxcbiBcdFx0XHRcdGdldDogZ2V0dGVyXG4gXHRcdFx0fSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gMzQ3KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyB3ZWJwYWNrL2Jvb3RzdHJhcCA5M2U3Y2MxZjZhY2QyNDEwZjI0MiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuaW1wb3J0IFByZXZpZXdPcGVuZXIgZnJvbSAnQGNvbXBvbmVudHMvZm9ybS9wcmV2aWV3LW9wZW5lcic7XG5pbXBvcnQgQ2hvaWNlVHJlZSBmcm9tICdAY29tcG9uZW50cy9mb3JtL2Nob2ljZS10cmVlJztcbmltcG9ydCBUYWdnYWJsZUZpZWxkIGZyb20gJ0Bjb21wb25lbnRzL3RhZ2dhYmxlLWZpZWxkJztcbmltcG9ydCBUcmFuc2xhdGFibGVJbnB1dCBmcm9tICdAY29tcG9uZW50cy90cmFuc2xhdGFibGUtaW5wdXQnO1xuaW1wb3J0IHRleHRUb0xpbmtSZXdyaXRlQ29waWVyIGZyb20gJ0Bjb21wb25lbnRzL3RleHQtdG8tbGluay1yZXdyaXRlLWNvcGllcic7XG5pbXBvcnQgVHJhbnNsYXRhYmxlRmllbGQgZnJvbSAnQGNvbXBvbmVudHMvdHJhbnNsYXRhYmxlLWZpZWxkJztcbmltcG9ydCBUaW55TUNFRWRpdG9yIGZyb20gJ0Bjb21wb25lbnRzL3RpbnltY2UtZWRpdG9yJztcbmltcG9ydCBTZXJwIGZyb20gJ0BhcHAvdXRpbHMvc2VycC9pbmRleCc7XG5cbiQoKCkgPT4ge1xuICBuZXcgQ2hvaWNlVHJlZSgnI2Ntc19wYWdlX3BhZ2VfY2F0ZWdvcnlfaWQnKTtcblxuICBjb25zdCB0cmFuc2xhdG9ySW5wdXQgPSBuZXcgVHJhbnNsYXRhYmxlSW5wdXQoKTtcblxuICBuZXcgU2VycChcbiAgICB7XG4gICAgICBjb250YWluZXI6ICcjc2VycC1hcHAnLFxuICAgICAgZGVmYXVsdFRpdGxlOiAnaW5wdXRbbmFtZV49XCJjbXNfcGFnZVt0aXRsZV1cIl0nLFxuICAgICAgd2F0Y2hlZFRpdGxlOiAnaW5wdXRbbmFtZV49XCJjbXNfcGFnZVttZXRhX3RpdGxlXVwiXScsXG4gICAgICBkZWZhdWx0RGVzY3JpcHRpb246ICdpbnB1dFtuYW1lXj1cImNtc19wYWdlW2Rlc2NyaXB0aW9uXVwiXScsXG4gICAgICB3YXRjaGVkRGVzY3JpcHRpb246ICdpbnB1dFtuYW1lXj1cImNtc19wYWdlW21ldGFfZGVzY3JpcHRpb25dXCJdJyxcbiAgICAgIHdhdGNoZWRNZXRhVXJsOiAnaW5wdXRbbmFtZV49XCJjbXNfcGFnZVtmcmllbmRseV91cmxdXCJdJyxcbiAgICAgIG11bHRpTGFuZ3VhZ2VJbnB1dDogYCR7dHJhbnNsYXRvcklucHV0LmxvY2FsZUlucHV0U2VsZWN0b3J9Om5vdCguZC1ub25lKWAsXG4gICAgICBtdWx0aUxhbmd1YWdlSXRlbTogdHJhbnNsYXRvcklucHV0LmxvY2FsZUl0ZW1TZWxlY3RvcixcbiAgICB9LFxuICAgICQoJyNzZXJwLWFwcCcpLmRhdGEoJ2Ntcy11cmwnKSxcbiAgKTtcblxuICBuZXcgVHJhbnNsYXRhYmxlRmllbGQoKTtcbiAgbmV3IFRpbnlNQ0VFZGl0b3IoKTtcblxuICBuZXcgVGFnZ2FibGVGaWVsZCh7XG4gICAgdG9rZW5GaWVsZFNlbGVjdG9yOiAnaW5wdXQuanMtdGFnZ2FibGUtZmllbGQnLFxuICAgIG9wdGlvbnM6IHtcbiAgICAgIGNyZWF0ZVRva2Vuc09uQmx1cjogdHJ1ZSxcbiAgICB9LFxuICB9KTtcblxuICBuZXcgUHJldmlld09wZW5lcignLmpzLXByZXZpZXctdXJsJyk7XG5cbiAgdGV4dFRvTGlua1Jld3JpdGVDb3BpZXIoe1xuICAgIHNvdXJjZUVsZW1lbnRTZWxlY3RvcjogJ2lucHV0LmpzLWNvcGllci1zb3VyY2UtdGl0bGUnLFxuICAgIGRlc3RpbmF0aW9uRWxlbWVudFNlbGVjdG9yOiBgJHt0cmFuc2xhdG9ySW5wdXQubG9jYWxlSW5wdXRTZWxlY3Rvcn06bm90KC5kLW5vbmUpIGlucHV0LmpzLWNvcGllci1kZXN0aW5hdGlvbi1mcmllbmRseS11cmxgLFxuICB9KTtcblxuICBuZXcgQ2hvaWNlVHJlZSgnI2Ntc19wYWdlX3Nob3BfYXNzb2NpYXRpb24nKS5lbmFibGVBdXRvQ2hlY2tDaGlsZHJlbigpO1xufSk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9jbXMtcGFnZS9mb3JtL2luZGV4LmpzIl0sInNvdXJjZVJvb3QiOiIifQ==